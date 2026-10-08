<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Problem;
use App\Models\Submission;
use Illuminate\Http\Request;

class LearnController extends Controller
{
    /** Beranda: peta belajar per track. */
    public function home(Request $request)
    {
        $user = $request->user();
        $lessons = Lesson::with(['problems:id,lesson_id,slug,title,difficulty'])->orderBy('position')->get();
        $completed = $user->completedLessons()->pluck('lessons.id')->flip();
        $best = $this->bestScores($user->id);

        $tracks = collect(Lesson::TRACKS)->map(function ($meta, $key) use ($lessons, $completed, $best) {
            $items = $lessons->where('track', $key)->values();
            $problemIds = $items->flatMap->problems->pluck('id');

            return $meta + [
                'key' => $key,
                'lessons' => $items,
                'lessonsDone' => $items->filter(fn ($l) => $completed->has($l->id))->count(),
                'problemsTotal' => $problemIds->count(),
                'problemsSolved' => $problemIds->filter(fn ($id) => ($best[$id] ?? 0) === 100)->count(),
            ];
        });

        $nextLesson = $lessons->first(fn ($l) => ! $completed->has($l->id));
        $stats = [
            'lessons' => $completed->count(),
            'solved' => collect($best)->filter(fn ($s) => $s === 100)->count(),
            'submissions' => Submission::where('user_id', $user->id)->count(),
            'totalLessons' => $lessons->count(),
            'totalProblems' => Problem::count(),
        ];

        return view('home', compact('tracks', 'completed', 'best', 'nextLesson', 'stats'));
    }

    public function lesson(Request $request, Lesson $lesson)
    {
        $user = $request->user();
        $lesson->load('problems');
        $siblings = Lesson::orderBy('position')->get(['id', 'slug', 'title', 'track', 'position']);
        $index = $siblings->search(fn ($l) => $l->id === $lesson->id);
        $prev = $siblings[$index - 1] ?? null;
        $next = $siblings[$index + 1] ?? null;
        $done = $user->completedLessons()->where('lessons.id', $lesson->id)->exists();
        $best = $this->bestScores($user->id);
        $trackLessons = $siblings->where('track', $lesson->track)->values();
        $completed = $user->completedLessons()->pluck('lessons.id')->flip();

        return view('lessons.show', compact('lesson', 'prev', 'next', 'done', 'best', 'trackLessons', 'completed'));
    }

    public function complete(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        if ($request->boolean('undo')) {
            $user->completedLessons()->detach($lesson->id);
        } else {
            $user->completedLessons()->syncWithoutDetaching([$lesson->id => ['completed_at' => now()]]);
        }

        if ($request->expectsJson()) {
            return response()->json(['done' => ! $request->boolean('undo')]);
        }

        return back();
    }

    /** @return array<int, int> problem_id => nilai terbaik */
    public static function bestScores(int $userId): array
    {
        return Submission::where('user_id', $userId)
            ->selectRaw('problem_id, max(score) as best')
            ->groupBy('problem_id')
            ->pluck('best', 'problem_id')
            ->map(fn ($v) => (int) $v)
            ->all();
    }
}
