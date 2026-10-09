<?php

namespace Database\Seeders\Support;

/**
 * Pembangkit data uji acak (deterministik karena memakai mt_srand dari seeder).
 */
class TestGen
{
    public static function int(int $min, int $max): int
    {
        return mt_rand($min, $max);
    }

    /**
     * Daftar sisi acak tanpa self-loop dan tanpa duplikat.
     * $connected = true: dibuat spanning tree acak dulu agar graph terhubung.
     *
     * @return array<int, array{0:int,1:int,2?:int}>
     */
    public static function edges(int $n, int $m, bool $directed = false, bool $connected = false, ?array $weight = null): array
    {
        $max = $directed ? $n * ($n - 1) : intdiv($n * ($n - 1), 2);
        $m = min($m, $max);
        $seen = [];
        $edges = [];

        $add = function (int $u, int $v) use (&$seen, &$edges, $directed, $weight): bool {
            if ($u === $v) {
                return false;
            }
            $key = $directed ? "$u-$v" : min($u, $v).'-'.max($u, $v);
            if (isset($seen[$key])) {
                return false;
            }
            $seen[$key] = true;
            $edge = [$u, $v];
            if ($weight) {
                $edge[] = mt_rand($weight[0], $weight[1]);
            }
            $edges[] = $edge;

            return true;
        };

        if ($connected && $n > 1) {
            $order = range(1, $n);
            self::shuffle($order);
            for ($i = 1; $i < $n && count($edges) < $m; $i++) {
                $add($order[mt_rand(0, $i - 1)], $order[$i]);
            }
        }

        $guard = 0;
        while (count($edges) < $m && $guard++ < $m * 50) {
            $add(mt_rand(1, $n), mt_rand(1, $n));
        }

        self::shuffle($edges);

        return $edges;
    }

    /**
     * Sisi acak untuk DAG: arah selalu mengikuti urutan permutasi tersembunyi.
     *
     * @return array<int, array{0:int,1:int}>
     */
    public static function dag(int $n, int $m): array
    {
        $order = range(1, $n);
        self::shuffle($order);
        $rank = array_flip($order);
        $edges = [];
        foreach (self::edges($n, $m) as [$u, $v]) {
            $edges[] = $rank[$u] < $rank[$v] ? [$u, $v] : [$v, $u];
        }

        return $edges;
    }

    /** Graph dengan siklus: DAG acak lalu ditambah satu sisi balik. */
    public static function withCycle(int $n, int $m): array
    {
        $edges = self::dag($n, max(1, $m - 1));
        [$u, $v] = $edges[0];
        $edges[] = [$v, $u];
        self::shuffle($edges);

        return $edges;
    }

    public static function graphInput(string $header, array $edges): string
    {
        $lines = [$header];
        foreach ($edges as $e) {
            $lines[] = implode(' ', $e);
        }

        return implode("\n", $lines)."\n";
    }

    /** @return string[] grid acak berisi $wall dan $open */
    public static function grid(int $r, int $c, float $wallProb, string $wall = '#', string $open = '.'): array
    {
        $rows = [];
        for ($i = 0; $i < $r; $i++) {
            $row = '';
            for ($j = 0; $j < $c; $j++) {
                $row .= (mt_rand() / mt_getrandmax()) < $wallProb ? $wall : $open;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public static function word(int $len, string $alphabet): string
    {
        $s = '';
        for ($i = 0; $i < $len; $i++) {
            $s .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
        }

        return $s;
    }

    public static function shuffle(array &$arr): void
    {
        for ($i = count($arr) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
        }
    }

    /** @return int[] n bilangan acak di [lo, hi] */
    public static function arr(int $n, int $lo, int $hi): array
    {
        $a = [];
        for ($i = 0; $i < $n; $i++) {
            $a[] = mt_rand($lo, $hi);
        }

        return $a;
    }

    /** @return int[] permutasi acak 1..n */
    public static function perm(int $n): array
    {
        $p = $n > 0 ? range(1, $n) : [];
        self::shuffle($p);

        return $p;
    }

    /**
     * Pohon acak berlabel 1..n sebagai daftar sisi [u, v].
     * $shape: random (ayah acak), line (rantai), star (bintang), deep (ayah dekat → pohon dalam), wide (ayah dari awal → pohon lebar)
     *
     * @return array<int, array{0:int,1:int}>
     */
    public static function tree(int $n, string $shape = 'random'): array
    {
        $label = self::perm($n);
        $edges = [];
        for ($i = 1; $i < $n; $i++) {
            $p = match ($shape) {
                'line' => $i - 1,
                'star' => 0,
                'deep' => mt_rand(max(0, $i - 3), $i - 1),
                'wide' => mt_rand(0, min($i - 1, 3)),
                default => mt_rand(0, $i - 1),
            };
            $edges[] = mt_rand(0, 1) ? [$label[$p], $label[$i]] : [$label[$i], $label[$p]];
        }
        self::shuffle($edges);

        return $edges;
    }

    /** Gabungkan bilangan menjadi satu baris berpemisah spasi. */
    public static function join(array $a): string
    {
        return implode(' ', $a);
    }

    /** Pecah input menjadi baris-baris (helper untuk solusi referensi). */
    public static function lines(string $input): array
    {
        return preg_split('/\r?\n/', rtrim($input, "\r\n"));
    }

    /** @return int[] */
    public static function ints(string $line): array
    {
        $line = trim($line);

        return $line === '' ? [] : array_map('intval', preg_split('/\s+/', $line));
    }
}
