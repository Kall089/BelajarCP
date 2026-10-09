<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Submission;
use App\Services\CppJudge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProblemController extends Controller
{
    /** Bahasa yang dikenal; C++ dinilai di server, sisanya dijalankan di browser. */
    public const LANGUAGES = [
        'cpp' => 'C++',
        'javascript' => 'JavaScript',
        'python' => 'Python 3',
    ];

    /** Batas total waktu eksekusi satu submit C++ di server (milidetik). */
    private const SERVER_BUDGET_MS = 45000;

    /** @return array<string, string> bahasa yang aktif beserta labelnya */
    public static function languages(): array
    {
        $languages = self::LANGUAGES;
        if (CppJudge::enabled()) {
            $languages['cpp'] = config('judge.cpp.label');
        } else {
            unset($languages['cpp']);
        }

        return $languages;
    }

    public function index(Request $request)
    {
        $problems = Problem::with('lesson:id,slug,title,track')
            ->withCount('submissions')
            ->orderBy('position')
            ->get();
        $best = LearnController::bestScores($request->user()->id);

        $solvers = Submission::where('score', 100)
            ->selectRaw('problem_id, count(distinct user_id) as solvers')
            ->groupBy('problem_id')
            ->pluck('solvers', 'problem_id');

        return view('problems.index', compact('problems', 'best', 'solvers'));
    }

    public function show(Request $request, Problem $problem)
    {
        $user = $request->user();
        $problem->load(['lesson', 'samples']);
        $prev = Problem::where('position', '<', $problem->position)->orderByDesc('position')->first(['slug', 'title']);
        $next = Problem::where('position', '>', $problem->position)->orderBy('position')->first(['slug', 'title']);
        $submissions = $problem->submissions()->where('user_id', $user->id)->latest()->limit(20)->get();
        $best = (int) $submissions->max('score');
        $languages = self::languages();

        $config = [
            'slug' => $problem->slug,
            'userId' => $user->id,
            'starter' => $problem->starter,
            'languages' => $languages,
            'serverLanguages' => ['cpp'],
            'timeLimits' => collect($languages)->keys()->mapWithKeys(fn ($l) => [$l => $problem->timeLimitFor($l)]),
            'samples' => $problem->samples->map(fn ($t) => ['id' => $t->id, 'input' => $t->input, 'output' => $t->output])->values(),
            'sampleVisual' => $problem->sample_visual,
            'testsUrl' => route('problems.tests', $problem),
            'runUrl' => route('problems.run', $problem),
            'submitUrl' => route('problems.submit', $problem),
            'editorialUrl' => route('problems.editorial', $problem),
            'nextUrl' => $next ? route('problems.show', $next->slug) : null,
            'runners' => [
                'js' => asset('js/runners/js-runner.js').'?v='.filemtime(public_path('js/runners/js-runner.js')),
                'py' => asset('js/runners/py-runner.js').'?v='.filemtime(public_path('js/runners/py-runner.js')),
            ],
            'pyodide' => 'https://cdn.jsdelivr.net/pyodide/v0.27.7/full/',
        ];

        return view('problems.show', compact('problem', 'prev', 'next', 'submissions', 'best', 'config', 'languages'));
    }

    /** Pembahasan hanya dikirim setelah siswa pernah submit soal ini. */
    public function editorial(Request $request, Problem $problem)
    {
        abort_unless($problem->submissions()->where('user_id', $request->user()->id)->exists(), 403);

        return view('problems.editorial', compact('problem'));
    }

    /** Input semua tes (tanpa output) untuk dijalankan di browser. */
    public function tests(Problem $problem): JsonResponse
    {
        return response()->json(
            $problem->testCases()->get(['id', 'input', 'is_sample'])
                ->map(fn ($t) => ['id' => $t->id, 'input' => $t->input, 'sample' => $t->is_sample])
        );
    }

    /** Jalankan kode C++ di server untuk tes contoh atau input sendiri (tidak dinilai). */
    public function run(Request $request, Problem $problem): JsonResponse
    {
        abort_unless(CppJudge::enabled(), 404);
        $data = $request->validate([
            'code' => ['required', 'string', 'max:65000'],
            'inputs' => ['required', 'array', 'min:1', 'max:10'],
            'inputs.*' => ['nullable', 'string', 'max:200000'],
        ]);
        @set_time_limit(120);

        $judge = CppJudge::make();
        $start = microtime(true);
        $compiled = $judge->compile($data['code']);
        $compileMs = (int) round((microtime(true) - $start) * 1000);
        if (! $compiled['ok']) {
            $this->abortIfBlocked($compiled);

            return response()->json(['compileError' => $compiled['error']]);
        }

        $limit = $problem->timeLimitFor('cpp');
        $results = array_map(function ($input) use ($judge, $compiled, $limit) {
            $r = $judge->run($compiled['exe'], (string) $input, $limit);
            $this->abortIfBlocked($r);
            $r['output'] = mb_strcut($r['output'], 0, 200_000);

            return $r;
        }, $data['inputs']);

        return response()->json(['results' => $results, 'compileMs' => $compileMs]);
    }

    /** Nilai submisi. C++ dijalankan di server; JS/Python mengirim output hasil eksekusi di browser. */
    public function submit(Request $request, Problem $problem): JsonResponse
    {
        $data = $request->validate([
            'language' => ['required', Rule::in(array_keys(self::languages()))],
            'code' => ['required', 'string', 'max:65000'],
        ]);
        $tests = $problem->testCases()->get();

        if ($data['language'] === 'cpp') {
            @set_time_limit(180);
            $judge = CppJudge::make();
            $compiled = $judge->compile($data['code']);
            if (! $compiled['ok']) {
                $this->abortIfBlocked($compiled);
                $firstFail = ['n' => null, 'verdict' => 'CE', 'sample' => false, 'input' => null, 'expected' => null, 'output' => null, 'error' => $compiled['error']];

                return $this->record($request, $problem, $data, [], $firstFail, $tests->count());
            }
            $byId = $this->runOnServer($judge, $compiled['exe'], $tests, $problem->timeLimitFor('cpp'));
        } else {
            $byId = collect($request->validate([
                'results' => ['required', 'array'],
                'results.*.id' => ['required', 'integer'],
                'results.*.status' => ['required', 'in:ok,tle,re'],
                'results.*.output' => ['nullable', 'string'],
                'results.*.fp' => ['nullable', 'string', 'max:40'],
                'results.*.time' => ['nullable', 'numeric'],
                'results.*.error' => ['nullable', 'string', 'max:2000'],
            ])['results'])->keyBy('id');
        }

        $verdicts = [];
        $firstFail = null;
        foreach ($tests as $i => $test) {
            $r = $byId->get($test->id);
            $verdict = match (true) {
                $r === null => 'RE',
                $r['status'] === 'tle' => 'TLE',
                $r['status'] === 're' => 'RE',
                ! empty($r['fp']) => hash_equals(Problem::outputFingerprint($test->output), (string) $r['fp']) ? 'AC' : 'WA',
                Problem::outputsMatch($test->output, (string) ($r['output'] ?? '')) => 'AC',
                default => 'WA',
            };

            $verdicts[] = ['n' => $i + 1, 'sample' => $test->is_sample, 'verdict' => $verdict, 'time' => (int) round($r['time'] ?? 0)];

            if ($verdict !== 'AC' && $firstFail === null) {
                $firstFail = [
                    'n' => $i + 1,
                    'verdict' => $verdict,
                    'sample' => $test->is_sample,
                    // Detail hanya dibuka untuk tes contoh; tes tersembunyi tetap rahasia.
                    'input' => $test->is_sample ? $test->input : null,
                    'expected' => $test->is_sample ? $test->output : null,
                    'output' => $test->is_sample ? mb_substr((string) ($r['output'] ?? ''), 0, 2000) : null,
                    'error' => isset($r['error']) ? mb_substr((string) $r['error'], 0, 2000) : null,
                ];
            }
        }

        return $this->record($request, $problem, $data, $verdicts, $firstFail, count($verdicts));
    }

    /** Jalankan semua tes di server; tes yang melewati anggaran waktu total dianggap TLE. */
    private function runOnServer(CppJudge $judge, string $exe, Collection $tests, int $limit): Collection
    {
        $results = collect();
        $spent = 0;
        foreach ($tests as $test) {
            if ($spent > self::SERVER_BUDGET_MS) {
                $results->put($test->id, ['status' => 'tle', 'output' => '', 'time' => $limit]);

                continue;
            }
            $r = $judge->run($exe, $test->input, $limit);
            $this->abortIfBlocked($r);
            $spent += $r['time'];
            $results->put($test->id, $r);
        }

        return $results;
    }

    /** Masalah di server (misalnya .exe diblokir antivirus) bukan kesalahan siswa: jangan dicatat sebagai submisi. */
    private function abortIfBlocked(array $result): void
    {
        if (! empty($result['blocked']) || ($result['status'] ?? null) === 'sys') {
            abort(response()->json(['message' => CppJudge::BLOCKED_MESSAGE], 503));
        }
    }

    private function record(Request $request, Problem $problem, array $data, array $verdicts, ?array $firstFail, int $total): JsonResponse
    {
        $passed = collect($verdicts)->where('verdict', 'AC')->count();
        $score = $total ? (int) floor($passed / $total * 100) : 0;
        $verdict = $firstFail['verdict'] ?? 'AC';
        $maxTime = (int) collect($verdicts)->max('time');

        $submission = Submission::create([
            'user_id' => $request->user()->id,
            'problem_id' => $problem->id,
            'language' => $data['language'],
            'code' => $data['code'],
            'verdict' => $verdict,
            'score' => $score,
            'passed' => $passed,
            'total' => $total,
            'time_ms' => $maxTime,
            'results' => $verdicts,
        ]);

        $best = (int) Submission::where('user_id', $request->user()->id)->where('problem_id', $problem->id)->max('score');

        return response()->json([
            'id' => $submission->id,
            'verdict' => $verdict,
            'verdictLabel' => $submission->verdictLabel(),
            'score' => $score,
            'passed' => $passed,
            'total' => $total,
            'time' => $maxTime,
            'tests' => $verdicts,
            'firstFail' => $firstFail,
            'best' => $best,
            'language' => self::languages()[$data['language']] ?? $data['language'],
            'createdAt' => $submission->created_at->locale('id')->translatedFormat('d M, H:i'),
        ]);
    }

    public function submission(Request $request, Submission $submission): JsonResponse
    {
        abort_unless($submission->user_id === $request->user()->id, 403);

        return response()->json([
            'language' => $submission->language,
            'code' => $submission->code,
        ]);
    }

    public function history(Request $request)
    {
        $submissions = Submission::with('problem:id,slug,title')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(25);

        return view('problems.history', compact('submissions'));
    }

    public function leaderboard(Request $request)
    {
        $best = DB::table('submissions')
            ->selectRaw('user_id, problem_id, max(score) as best')
            ->groupBy('user_id', 'problem_id');

        $rows = DB::query()->fromSub($best, 'b')
            ->join('users', 'users.id', '=', 'b.user_id')
            ->selectRaw('users.id, users.username, sum(b.best) as points, sum(case when b.best = 100 then 1 else 0 end) as solved, count(*) as attempted')
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('points')
            ->orderByDesc('solved')
            ->orderBy('users.username')
            ->limit(100)
            ->get();

        $lessonsDone = DB::table('lesson_progress')
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return view('problems.leaderboard', [
            'rows' => $rows,
            'lessonsDone' => $lessonsDone,
            'totalProblems' => Problem::count(),
        ]);
    }
}
