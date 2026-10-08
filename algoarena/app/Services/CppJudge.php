<?php

namespace App\Services;

use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

/**
 * Kompilasi & eksekusi kode C++ memakai compiler lokal (g++).
 *
 * Ini bukan sandbox penuh: judge hanya menolak pola berbahaya yang umum
 * (system(), akses file, header OS) dan membatasi waktu eksekusi.
 */
class CppJudge
{
    public const BLOCKED_MESSAGE = 'Program hasil kompilasi diblokir antivirus di server (bukan kesalahan kodemu). '
        .'Admin: tambahkan folder storage/app/judge ke daftar pengecualian antivirus, lalu coba lagi.';

    private const FORBIDDEN = [
        '/#\s*include\s*[<"]\s*(windows\.h|winsock2?\.h|ws2tcpip\.h|process\.h|direct\.h|io\.h|conio\.h|fstream|filesystem|thread|unistd\.h|sys\/[^>"]+|dirent\.h|shellapi\.h|tlhelp32\.h|shlobj\.h)\s*[>"]/i'
            => 'Header %s tidak diizinkan di judge.',
        '/\b(system|_wsystem|popen|_popen|ShellExecute\w*|CreateProcess\w*|WinExec|LoadLibrary\w*|exec[lv]p?e?|_exec\w*|spawn[lv]p?e?|_spawn\w*|fork)\s*\(/'
            => 'Fungsi %s() tidak diizinkan di judge.',
        '/\b(fopen|_wfopen|freopen|_unlink|unlink|_rmdir|rmdir|_mkdir|mkdir|tmpfile|tmpnam|DeleteFile\w*|CopyFile\w*|MoveFile\w*)\s*\(/'
            => 'Fungsi %s() tidak diizinkan. Baca input dari cin/scanf dan tulis output ke cout/printf saja (hapus freopen sebelum submit).',
        '/\b(remove|rename)\s*\(\s*["\']/'
            => 'Fungsi %s() untuk file tidak diizinkan di judge.',
        '/\b(ofstream|ifstream|fstream|filebuf|wofstream|wifstream)\b/'
            => '%s tidak diizinkan. Gunakan cin dan cout.',
        '/\b(asm|__asm__|__asm)\b/' => 'Inline assembly (%s) tidak diizinkan.',
        '/#\s*pragma\s+comment/' => 'Direktif %s tidak diizinkan.',
        '/##/' => 'Operator token-pasting %s pada macro tidak diizinkan di judge.',
    ];

    private const MAX_OUTPUT = 4_000_000;

    /** Pesan dari Windows ketika file .exe hilang atau dikunci (biasanya oleh antivirus). */
    private const SYSTEM_FAILURE = '/cannot execute the specified program|is not recognized as an internal or external command|Access is denied|being used by another process|cannot find the file specified/i';

    public function __construct(
        private string $compiler,
        private string $flags,
        private string $dir,
        private ?string $fallbackFlags = null,
    ) {}

    public static function make(): self
    {
        return new self(
            config('judge.cpp.compiler'),
            config('judge.cpp.flags'),
            storage_path('app/judge'),
            config('judge.cpp.fallback_flags'),
        );
    }

    public static function enabled(): bool
    {
        return (bool) config('judge.cpp.enabled');
    }

    /** Kembalikan pesan jika kode memakai sesuatu yang dilarang, atau null jika aman. */
    public function forbidden(string $code): ?string
    {
        // Abaikan komentar agar penjelasan di komentar tidak memicu penolakan
        $stripped = preg_replace(['~/\*.*?\*/~s', '~//[^\n]*~'], '', $code);
        foreach (self::FORBIDDEN as $pattern => $message) {
            if (preg_match($pattern, $stripped, $m)) {
                return sprintf($message, $m[1] ?? $m[0]);
            }
        }

        return null;
    }

    /**
     * Kompilasi kode. Jika hasilnya diblokir antivirus, coba sekali lagi dengan flag cadangan.
     *
     * @return array{ok: bool, exe?: string, error?: string, blocked?: bool}
     */
    public function compile(string $code): array
    {
        if ($reason = $this->forbidden($code)) {
            return ['ok' => false, 'error' => $reason];
        }
        if (! is_dir($this->dir)) {
            mkdir($this->dir, 0777, true);
        }

        foreach (array_unique(array_filter([$this->flags, $this->fallbackFlags])) as $flags) {
            $result = $this->build($code, $flags);
            if (! $result['ok'] || ! empty($result['cached'])) {
                return $result;
            }
            // Pemanasan: run pertama .exe baru di Windows lambat karena dipindai antivirus.
            // Sekaligus memastikan file tidak dihapus/dikunci antivirus.
            if ($this->run($result['exe'], '', 3000)['status'] !== 'sys') {
                $this->prune();

                return $result;
            }
            @unlink($result['exe']);
        }

        return ['ok' => false, 'blocked' => true, 'error' => self::BLOCKED_MESSAGE];
    }

    /** @return array{ok: bool, exe?: string, error?: string, cached?: bool} */
    private function build(string $code, string $flags): array
    {
        $hash = sha1($code.'|'.$this->compiler.'|'.$flags);
        $exe = "{$this->dir}/{$hash}.exe";
        if (is_file($exe)) {
            return ['ok' => true, 'exe' => $exe, 'cached' => true];
        }

        $src = "{$this->dir}/{$hash}.cpp";
        file_put_contents($src, $code);
        $args = array_merge([$this->compiler], preg_split('/\s+/', trim($flags)), ['-o', $exe, $src]);
        $process = new Process($args, $this->dir, $this->env(), null, 60);
        try {
            $process->run();
        } catch (ProcessTimedOutException) {
            @unlink($src);

            return ['ok' => false, 'error' => 'Kompilasi terlalu lama (lebih dari 60 detik).'];
        }
        @unlink($src);

        if (! $process->isSuccessful() || ! is_file($exe)) {
            return ['ok' => false, 'error' => $this->cleanCompilerError($process->getErrorOutput()."\n".$process->getOutput(), $hash)];
        }

        return ['ok' => true, 'exe' => $exe];
    }

    /**
     * Status: ok | tle | re | sys (gagal dijalankan karena masalah server, misalnya diblokir antivirus).
     *
     * @return array{status: string, output: string, time: int, error?: string}
     */
    public function run(string $exe, string $input, int $limitMs): array
    {
        if (! is_file($exe)) {
            return ['status' => 'sys', 'output' => '', 'time' => 0, 'error' => self::BLOCKED_MESSAGE];
        }

        // Input/output lewat file sementara: tidak ada risiko pipe penuh (deadlock),
        // dan proc_open langsung (tanpa cmd.exe) jauh lebih cepat di Windows.
        $base = "{$this->dir}/run-".bin2hex(random_bytes(6));
        file_put_contents("$base.in", $input);
        $spec = [0 => ['file', "$base.in", 'r'], 1 => ['file', "$base.out", 'w'], 2 => ['file', "$base.err", 'w']];
        $proc = @proc_open([$exe], $spec, $pipes, $this->dir, $this->env(true), ['bypass_shell' => true]);
        if (! is_resource($proc)) {
            $this->cleanup($base);

            return ['status' => 'sys', 'output' => '', 'time' => 0, 'error' => self::BLOCKED_MESSAGE];
        }

        $start = microtime(true);
        $deadline = $start + ($limitMs + 300) / 1000;
        $verdict = null;
        for ($tick = 0; ($status = proc_get_status($proc))['running']; $tick++) {
            if (microtime(true) > $deadline) {
                $verdict = 'tle';
            } elseif ($tick % 64 === 63) {
                clearstatcache(true, "$base.out");
                if (@filesize("$base.out") > self::MAX_OUTPUT) {
                    $verdict = 'big';
                }
            }
            if ($verdict) {
                proc_terminate($proc, 9);
                break;
            }
            usleep(1000);
        }
        $code = $status['running'] ? null : $status['exitcode'];
        if ($code !== null && $code < 0) {
            $code += 4294967296; // kode NTSTATUS Windows (mis. 0xC0000005) dibaca sebagai int bertanda
        }
        proc_close($proc);
        $time = (int) round((microtime(true) - $start) * 1000);
        $output = (string) @file_get_contents("$base.out", false, null, 0, self::MAX_OUTPUT);
        $stderr = (string) @file_get_contents("$base.err", false, null, 0, 4000);
        $this->cleanup($base);

        if ($verdict === 'tle') {
            return ['status' => 'tle', 'output' => '', 'time' => $limitMs];
        }
        if ($verdict === 'big') {
            return ['status' => 're', 'output' => '', 'time' => $time, 'error' => 'Output terlalu besar (lebih dari 4 MB). Mungkin ada perulangan yang tidak berhenti.'];
        }
        if ($code !== 0) {
            if (! is_file($exe) || preg_match(self::SYSTEM_FAILURE, $stderr)) {
                return ['status' => 'sys', 'output' => '', 'time' => $time, 'error' => self::BLOCKED_MESSAGE];
            }

            return ['status' => 're', 'output' => $output, 'time' => $time, 'error' => $this->exitMessage($code, $stderr)];
        }
        if ($time > $limitMs) {
            return ['status' => 'tle', 'output' => $output, 'time' => $time];
        }

        return ['status' => 'ok', 'output' => $output, 'time' => $time];
    }

    private function cleanup(string $base): void
    {
        foreach (['in', 'out', 'err'] as $ext) {
            @unlink("$base.$ext");
        }
    }

    /**
     * PATH ditambah folder compiler agar program hasil link dinamis menemukan DLL MinGW.
     * $full = true mengembalikan seluruh environment (proc_open mengganti, bukan menggabungkan).
     */
    private function env(bool $full = false): ?array
    {
        $bin = dirname($this->compiler);
        if ($bin === '.' || ! is_dir($bin)) {
            return null;
        }
        $env = $full ? getenv() : [];
        $key = collect(array_keys(getenv()))->first(fn ($k) => strcasecmp($k, 'PATH') === 0) ?? 'PATH';
        $env[$key] = $bin.PATH_SEPARATOR.getenv($key);

        return $env;
    }

    private function exitMessage(?int $code, string $stderr): string
    {
        $known = [
            3221225477 => 'Segmentation fault: mengakses memori di luar batas (cek indeks array dan ukuran array).',
            3221225725 => 'Stack overflow: rekursi terlalu dalam atau array lokal terlalu besar (pindahkan array besar ke global).',
            3221225620 => 'Pembagian dengan nol.',
            3221225495 => 'Memori habis (alokasi terlalu besar).',
            3 => 'Program berhenti lewat abort(): biasanya assert gagal atau exception yang tidak ditangkap.',
        ];
        $message = $known[$code] ?? "Program berhenti dengan kode {$code}. Pastikan main() diakhiri dengan return 0.";
        $stderr = trim($stderr);

        return $stderr !== '' ? $message."\n".mb_substr($stderr, 0, 800) : $message;
    }

    private function cleanCompilerError(string $raw, string $hash): string
    {
        $text = str_replace(["{$this->dir}/{$hash}.cpp", "{$this->dir}\\{$hash}.cpp", "{$hash}.cpp"], 'solusi.cpp', $raw);
        $text = preg_replace('/^.*(collect2|ld\.exe|ld returned).*$/m', '', $text);
        $lines = array_values(array_filter(array_map('rtrim', explode("\n", $text)), fn ($l) => $l !== ''));

        return mb_substr(implode("\n", array_slice($lines, 0, 30)), 0, 4000) ?: 'Kompilasi gagal.';
    }

    /** Hapus file hasil kompilasi yang sudah lama agar folder tidak membengkak. */
    private function prune(): void
    {
        if (random_int(1, 20) !== 1) {
            return;
        }
        foreach (glob("{$this->dir}/*.exe") ?: [] as $file) {
            if (filemtime($file) < time() - 86400) {
                @unlink($file);
            }
        }
    }
}
