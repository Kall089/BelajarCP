<?php

use Database\Seeders\Support\TestGen as T;

const DP_MOD = 1000000007;

return [
    // ═════════════════════════════ Konsep DP ═════════════════════════════
    [
        'slug' => 'fibonacci-modulo',
        'lesson' => 'konsep-dp',
        'title' => 'Fibonacci Raksasa',
        'difficulty' => 'Mudah',
        'tags' => ['dp', 'fibonacci', 'modulo'],
        'statement' => '<p>Deret Fibonacci didefinisikan sebagai <code>F(0) = 0</code>, <code>F(1) = 1</code>, dan <code>F(n) = F(n−1) + F(n−2)</code> untuk n ≥ 2.</p>
<p>Diberikan <strong>n</strong> yang bisa sangat besar. Hitung <code>F(n)</code>. Karena hasilnya bisa sangat besar, cetak sisa baginya terhadap <code>1 000 000 007</code>.</p>
<p>Solusi rekursif biasa akan <strong>Time Limit Exceeded</strong>, dan rekursi sedalam 100 000 akan <strong>Runtime Error</strong>. Gunakan DP dengan perulangan (tabulasi)!</p>',
        'input_format' => '<p>Satu bilangan bulat <code>n</code>.</p>',
        'output_format' => '<p><code>F(n) mod 1 000 000 007</code>.</p>',
        'constraints' => '<ul><li>0 ≤ n ≤ 100 000</li></ul>',
        'samples' => [
            ['input' => "10\n", 'explanation' => 'Deretnya 0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, sehingga F(10) = 55.'],
            ['input' => "0\n", 'explanation' => 'F(0) = 0 sesuai definisi.'],
        ],
        'tests' => fn () => ["1\n", "2\n", "45\n", "50\n", "1000\n", "77777\n", "99999\n", "100000\n"],
        'solve' => function (string $input) {
            $n = (int) trim($input);
            [$a, $b] = [0, 1];
            for ($i = 0; $i < $n; $i++) {
                [$a, $b] = [$b, ($a + $b) % DP_MOD];
            }

            return (string) $a;
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n] = readInts();
const MOD = 1000000007;

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
n = int(input())
MOD = 1_000_000_007

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n] = readInts();
const MOD = 1000000007;

// dp[i] = F(i). Cukup simpan dua nilai terakhir.
let a = 0; // F(i)
let b = 1; // F(i + 1)
for (let i = 0; i < n; i++) {
  [a, b] = [b, (a + b) % MOD];
}

console.log(a);
CODE,
            'python' => <<<'CODE'
n = int(input())
MOD = 1_000_000_007

a, b = 0, 1  # F(i), F(i+1)
for _ in range(n):
    a, b = b, (a + b) % MOD

print(a)
CODE,
        ],
        'editorial' => '<p>Rekursi <code>F(n) = F(n−1) + F(n−2)</code> tanpa memo memanggil subproblem yang sama berulang-ulang, sehingga waktunya eksponensial (≈ 1.6<sup>n</sup>).</p>
<p>Dengan <strong>tabulasi</strong> kita menghitung dari bawah: F(0), F(1), F(2), ... sampai F(n). Setiap nilai cukup dihitung <strong>sekali</strong>, sehingga O(n).</p>
<p><strong>Hemat memori:</strong> F(i) hanya butuh dua nilai sebelumnya, jadi tidak perlu array, cukup dua variabel. Ambil modulo di setiap langkah agar angka tidak meluap.</p>',
    ],

    [
        'slug' => 'lompatan-katak',
        'lesson' => 'konsep-dp',
        'title' => 'Lompatan Katak',
        'difficulty' => 'Mudah',
        'tags' => ['dp', 'dp 1d', 'minimum'],
        'statement' => '<p>Ada <strong>N</strong> batu berjajar, batu ke-i punya ketinggian <code>h<sub>i</sub></code>. Seekor katak mulai di batu 1 dan ingin ke batu N.</p>
<p>Dari batu i, katak bisa melompat ke batu <code>i+1</code> atau <code>i+2</code>. Setiap lompatan dari batu i ke j membutuhkan energi <code>|h<sub>i</sub> − h<sub>j</sub>|</code>. Berapa <strong>total energi minimum</strong> agar katak sampai di batu N?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>h1 h2 ... hN</code>.</p>',
        'output_format' => '<p>Total energi minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 50 000</li><li>1 ≤ h<sub>i</sub> ≤ 10 000</li></ul>',
        'samples' => [
            ['input' => "4\n10 30 40 20\n", 'explanation' => 'Rute 1 → 2 → 4 butuh |10−30| + |30−20| = 20 + 10 = 30. Itu yang paling hemat.'],
            ['input' => "6\n30 10 60 10 60 50\n", 'explanation' => 'Rute 1 → 3 → 5 → 6 butuh 30 + 0 + 10 = 40.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $h = [];
                for ($i = 0; $i < $n; $i++) {
                    $h[] = mt_rand(1, $max);
                }

                return "$n\n".implode(' ', $h)."\n";
            };

            return ["1\n5\n", "2\n7 3\n", $mk(10, 100), $mk(1000, 10000), $mk(50000, 10000), $mk(50000, 10), "5\n1 1 1 1 1\n"];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $h = T::ints($lines[1]);
            $dp = array_fill(0, $n, 0);
            for ($i = 1; $i < $n; $i++) {
                $dp[$i] = $dp[$i - 1] + abs($h[$i] - $h[$i - 1]);
                if ($i > 1) {
                    $dp[$i] = min($dp[$i], $dp[$i - 2] + abs($h[$i] - $h[$i - 2]));
                }
            }

            return (string) $dp[$n - 1];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n] = readInts();
const h = readInts(); // h[0..n-1]

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
h = list(map(int, input().split()))  # h[0..n-1]

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n] = readInts();
const h = readInts();

// dp[i] = energi minimum untuk sampai di batu i
const dp = new Array(n).fill(0);
for (let i = 1; i < n; i++) {
  dp[i] = dp[i - 1] + Math.abs(h[i] - h[i - 1]); // datang dari i-1
  if (i >= 2) {
    dp[i] = Math.min(dp[i], dp[i - 2] + Math.abs(h[i] - h[i - 2])); // atau dari i-2
  }
}

console.log(dp[n - 1]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
h = list(map(int, input().split()))

dp = [0] * n  # dp[i] = energi minimum sampai batu i
for i in range(1, n):
    dp[i] = dp[i - 1] + abs(h[i] - h[i - 1])
    if i >= 2:
        dp[i] = min(dp[i], dp[i - 2] + abs(h[i] - h[i - 2]))

print(dp[n - 1])
CODE,
        ],
        'editorial' => '<p>Empat langkah merancang DP:</p>
<ol><li><strong>State:</strong> <code>dp[i]</code> = energi minimum untuk sampai di batu i.</li>
<li><strong>Base case:</strong> <code>dp[0] = 0</code> (katak sudah di sana).</li>
<li><strong>Transisi:</strong> katak sampai di batu i dari batu i−1 atau i−2, jadi ambil yang termurah:<br><code>dp[i] = min(dp[i−1] + |h[i]−h[i−1]|, dp[i−2] + |h[i]−h[i−2]|)</code>.</li>
<li><strong>Jawaban:</strong> <code>dp[N−1]</code>.</li></ol>
<p>Strukturnya mirip Fibonacci, tetapi memakai <code>min</code> dan bukan penjumlahan. Inilah inti DP: <em>jawaban besar disusun dari jawaban kecil yang sudah optimal</em>.</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
    ],

    // ═════════════════════════════ DP 1D ═════════════════════════════
    [
        'slug' => 'naik-tangga',
        'lesson' => 'dp-1d',
        'title' => 'Tangga Sekolah Rusak',
        'difficulty' => 'Mudah',
        'tags' => ['dp', 'dp 1d', 'counting'],
        'statement' => '<p>Tangga menuju lab komputer punya <strong>n</strong> anak tangga (lantai bawah adalah anak tangga 0, lantai atas adalah anak tangga n). Dalam satu langkah, kamu bisa naik <strong>1 atau 2</strong> anak tangga.</p>
<p>Sayangnya ada <strong>m</strong> anak tangga yang rusak dan tidak boleh diinjak. Ada berapa <strong>cara berbeda</strong> untuk sampai ke atas? Cetak jawabannya modulo <code>1 000 000 007</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n m</code>. Baris kedua berisi <code>m</code> bilangan berbeda, yaitu nomor anak tangga yang rusak (baris ini kosong jika m = 0).</p>',
        'output_format' => '<p>Banyaknya cara modulo 1 000 000 007.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 100 000</li><li>0 ≤ m &lt; n</li><li>Anak tangga rusak bernomor antara 1 dan n − 1.</li></ul>',
        'samples' => [
            ['input' => "4 0\n\n", 'explanation' => 'Caranya: 1+1+1+1, 1+1+2, 1+2+1, 2+1+1, dan 2+2, sehingga ada 5 cara.'],
            ['input' => "6 1\n3\n", 'explanation' => 'Anak tangga 3 tidak boleh diinjak. Kamu harus melompat dari 2 ke 4. Ada 2 cara ke anak tangga 2, dan dari 4 ke 6 ada 2 cara, sehingga totalnya 4.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m) {
                $pool = range(1, $n - 1);
                T::shuffle($pool);
                $b = array_slice($pool, 0, $m);

                return "$n ".count($b)."\n".implode(' ', $b)."\n";
            };

            return ["1 0\n\n", "2 1\n1\n", "3 2\n1 2\n", $mk(30, 0), $mk(90, 5), $mk(1000, 50), $mk(100000, 0), $mk(100000, 2000)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n] = T::ints($lines[0]);
            $broken = array_flip(T::ints($lines[1] ?? ''));
            $dp = array_fill(0, $n + 1, 0);
            $dp[0] = 1;
            for ($i = 1; $i <= $n; $i++) {
                if (isset($broken[$i])) {
                    continue;
                }
                $dp[$i] = ($dp[$i - 1] + ($i >= 2 ? $dp[$i - 2] : 0)) % DP_MOD;
            }

            return (string) $dp[$n];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const rusak = readInts(); // bisa kosong
const MOD = 1000000007;

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
rusak = list(map(int, input().split()))  # bisa kosong
MOD = 1_000_000_007

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const rusak = new Set(readInts());
const MOD = 1000000007;

// dp[i] = banyak cara berdiri di anak tangga i
const dp = new Array(n + 1).fill(0);
dp[0] = 1; // satu cara: belum melangkah
for (let i = 1; i <= n; i++) {
  if (rusak.has(i)) continue; // tidak boleh diinjak → tetap 0
  dp[i] = (dp[i - 1] + (i >= 2 ? dp[i - 2] : 0)) % MOD;
}

console.log(dp[n]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
rusak = set(map(int, input().split()))
MOD = 1_000_000_007

dp = [0] * (n + 1)
dp[0] = 1
for i in range(1, n + 1):
    if i in rusak:
        continue
    dp[i] = (dp[i - 1] + (dp[i - 2] if i >= 2 else 0)) % MOD

print(dp[n])
CODE,
        ],
        'editorial' => '<p>Untuk berdiri di anak tangga <code>i</code>, langkah terakhirmu pasti datang dari <code>i−1</code> (naik 1) atau <code>i−2</code> (naik 2). Kedua kelompok cara itu tidak tumpang tindih, sehingga:</p>
<p style="text-align:center"><code>dp[i] = dp[i−1] + dp[i−2]</code></p>
<p>Anak tangga rusak tidak bisa diinjak, jadi <code>dp[rusak] = 0</code>. Nilai 0 ini otomatis "memutus" semua cara yang melewatinya.</p>
<p><strong>Base case:</strong> <code>dp[0] = 1</code>, karena ada tepat satu cara untuk berada di lantai bawah, yaitu tidak melangkah.</p>
<p><strong>Kompleksitas:</strong> O(n).</p>',
    ],

    [
        'slug' => 'koin-minimum',
        'lesson' => 'dp-1d',
        'title' => 'Kembalian Paling Sedikit',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'coin change', 'unbounded'],
        'statement' => '<p>Kasir kantin hanya punya <strong>K</strong> jenis koin dengan nominal tertentu, dan jumlah setiap koin <strong>tidak terbatas</strong>. Ia harus memberi kembalian tepat sebesar <strong>X</strong> rupiah.</p>
<p>Berapa <strong>banyak koin paling sedikit</strong> yang dibutuhkan? Jika nominal X tidak mungkin dibentuk, cetak <code>-1</code>.</p>
<p><em>Hati-hati:</em> strategi serakah (selalu ambil koin terbesar) tidak selalu benar!</p>',
        'input_format' => '<p>Baris pertama berisi <code>K X</code>. Baris kedua berisi <code>K</code> nominal koin.</p>',
        'output_format' => '<p>Banyak koin minimum, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ 50</li><li>0 ≤ X ≤ 10 000</li><li>1 ≤ nominal ≤ 10 000</li></ul>',
        'samples' => [
            ['input' => "3 6\n1 3 4\n", 'explanation' => 'Serakah memilih 4 + 1 + 1 (3 koin), padahal 3 + 3 hanya butuh 2 koin.'],
            ['input' => "2 7\n2 4\n", 'explanation' => 'Koin 2 dan 4 selalu membentuk bilangan genap, sehingga 7 mustahil dibentuk.'],
        ],
        'tests' => function () {
            $mk = function (int $k, int $x, int $max) {
                $c = [];
                for ($i = 0; $i < $k; $i++) {
                    $c[] = mt_rand(1, $max);
                }

                return "$k $x\n".implode(' ', $c)."\n";
            };

            return ["1 0\n5\n", "1 10\n3\n", "4 63\n1 5 10 25\n", "3 30\n7 11 13\n", $mk(5, 1000, 100), $mk(50, 10000, 500), $mk(3, 9999, 2000), "2 10000\n9999 10000\n", $mk(10, 10000, 10000)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [, $x] = T::ints($lines[0]);
            $coins = T::ints($lines[1]);
            $inf = PHP_INT_MAX;
            $dp = array_fill(0, $x + 1, $inf);
            $dp[0] = 0;
            for ($v = 1; $v <= $x; $v++) {
                foreach ($coins as $c) {
                    if ($c <= $v && $dp[$v - $c] !== $inf && $dp[$v] > $dp[$v - $c] + 1) {
                        $dp[$v] = $dp[$v - $c] + 1;
                    }
                }
            }

            return (string) ($dp[$x] === $inf ? -1 : $dp[$x]);
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [k, x] = readInts();
const koin = readInts();

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

k, x = map(int, input().split())
koin = list(map(int, input().split()))

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [k, x] = readInts();
const koin = readInts();

// dp[v] = koin minimum untuk membentuk nominal v
const dp = new Array(x + 1).fill(Infinity);
dp[0] = 0;

for (let v = 1; v <= x; v++) {
  for (const c of koin) {
    if (c <= v && dp[v - c] + 1 < dp[v]) {
      dp[v] = dp[v - c] + 1; // pakai satu koin c, sisanya v - c
    }
  }
}

console.log(dp[x] === Infinity ? -1 : dp[x]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

k, x = map(int, input().split())
koin = list(map(int, input().split()))

INF = float("inf")
dp = [INF] * (x + 1)  # dp[v] = koin minimum untuk nominal v
dp[0] = 0

for v in range(1, x + 1):
    for c in koin:
        if c <= v and dp[v - c] + 1 < dp[v]:
            dp[v] = dp[v - c] + 1

print(-1 if dp[x] == INF else dp[x])
CODE,
        ],
        'editorial' => '<p><strong>State:</strong> <code>dp[v]</code> = banyak koin minimum untuk membentuk nominal <code>v</code>.</p>
<p><strong>Transisi:</strong> koin terakhir yang dipakai pasti salah satu nominal <code>c</code>. Sisanya <code>v − c</code> harus dibentuk seoptimal mungkin, sehingga:</p>
<p style="text-align:center"><code>dp[v] = min(dp[v − c] + 1)</code> untuk setiap koin c ≤ v</p>
<p><strong>Base case:</strong> <code>dp[0] = 0</code>. Nominal yang mustahil dibentuk tetap bernilai ∞.</p>
<p><strong>Kenapa serakah gagal?</strong> Pada koin {1, 3, 4} dan X = 6, mengambil 4 dulu membuat sisa 2 yang hanya bisa dibentuk dengan 1 + 1. DP mencoba <em>semua</em> kemungkinan koin terakhir, sehingga pasti menemukan 3 + 3.</p>
<p><strong>Kompleksitas:</strong> O(K × X).</p>',
    ],

    // ═════════════════════════════ DP Grid ═════════════════════════════
    [
        'slug' => 'jalur-grid',
        'lesson' => 'dp-grid',
        'title' => 'Jalan Pulang Sekolah',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'grid', 'counting'],
        'statement' => '<p>Peta kompleks perumahan berbentuk grid <strong>R × C</strong>. Kamu mulai dari pojok kiri atas (sekolah) dan ingin ke pojok kanan bawah (rumah). Kamu hanya boleh bergerak ke <strong>kanan</strong> atau ke <strong>bawah</strong>.</p>
<p>Petak <code>#</code> adalah area yang sedang diperbaiki dan tidak boleh dilewati. Ada berapa <strong>jalur berbeda</strong> menuju rumah? Cetak modulo <code>1 000 000 007</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>, diikuti <code>R</code> baris string sepanjang <code>C</code> berisi <code>.</code> atau <code>#</code>.</p>',
        'output_format' => '<p>Banyaknya jalur modulo 1 000 000 007. Jika petak awal atau akhir terhalang, jawabannya 0.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 300</li></ul>',
        'samples' => [
            ['input' => "3 3\n...\n.#.\n...\n", 'explanation' => 'Hanya ada 2 jalur: menyusuri tepi atas lalu turun, atau turun dulu lalu menyusuri tepi bawah.'],
            ['input' => "2 3\n...\n...\n", 'explanation' => 'Tanpa halangan ada 3 jalur: KKB, KBK, dan BKK.'],
        ],
        'tests' => function () {
            $wrap = fn (array $g) => count($g).' '.strlen($g[0])."\n".implode("\n", $g)."\n";
            $tests = [$wrap(['.']), $wrap(['#']), $wrap(['.#', '..']), $wrap(['..', '.#'])];
            foreach ([[10, 10, 0.15], [50, 80, 0.1], [300, 300, 0.0], [300, 300, 0.08], [300, 250, 0.2]] as [$r, $c, $p]) {
                $g = T::grid($r, $c, $p);
                $g[0][0] = '.';
                $g[$r - 1][$c - 1] = '.';
                $tests[] = $wrap($g);
            }

            return $tests;
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C] = T::ints($lines[0]);
            $g = array_slice($lines, 1, $R);
            $dp = array_fill(0, $R, array_fill(0, $C, 0));
            for ($i = 0; $i < $R; $i++) {
                for ($j = 0; $j < $C; $j++) {
                    if ($g[$i][$j] === '#') {
                        continue;
                    }
                    if ($i === 0 && $j === 0) {
                        $dp[$i][$j] = 1;

                        continue;
                    }
                    $dp[$i][$j] = (($i > 0 ? $dp[$i - 1][$j] : 0) + ($j > 0 ? $dp[$i][$j - 1] : 0)) % DP_MOD;
                }
            }

            return (string) $dp[$R - 1][$C - 1];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const grid = [];
for (let i = 0; i < R; i++) grid.push(readLine());
const MOD = 1000000007;

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
grid = [input().strip() for _ in range(R)]
MOD = 1_000_000_007

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const grid = [];
for (let i = 0; i < R; i++) grid.push(readLine());
const MOD = 1000000007;

// dp[i][j] = banyak jalur dari (0,0) ke (i,j)
const dp = Array.from({ length: R }, () => new Array(C).fill(0));

for (let i = 0; i < R; i++) {
  for (let j = 0; j < C; j++) {
    if (grid[i][j] === "#") continue; // terhalang → 0 jalur
    if (i === 0 && j === 0) {
      dp[i][j] = 1;
      continue;
    }
    const atas = i > 0 ? dp[i - 1][j] : 0;
    const kiri = j > 0 ? dp[i][j - 1] : 0;
    dp[i][j] = (atas + kiri) % MOD;
  }
}

console.log(dp[R - 1][C - 1]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
grid = [input().strip() for _ in range(R)]
MOD = 1_000_000_007

dp = [[0] * C for _ in range(R)]
for i in range(R):
    for j in range(C):
        if grid[i][j] == "#":
            continue
        if i == 0 and j == 0:
            dp[i][j] = 1
            continue
        atas = dp[i - 1][j] if i > 0 else 0
        kiri = dp[i][j - 1] if j > 0 else 0
        dp[i][j] = (atas + kiri) % MOD

print(dp[R - 1][C - 1])
CODE,
        ],
        'editorial' => '<p>Karena hanya boleh bergerak ke kanan atau bawah, petak <code>(i, j)</code> hanya bisa dicapai dari <strong>atas</strong> <code>(i−1, j)</code> atau dari <strong>kiri</strong> <code>(i, j−1)</code>:</p>
<p style="text-align:center"><code>dp[i][j] = dp[i−1][j] + dp[i][j−1]</code></p>
<ul><li>Petak terhalang: <code>dp = 0</code>.</li>
<li>Base case: <code>dp[0][0] = 1</code> (jika tidak terhalang).</li>
<li>Isi tabel baris demi baris dari kiri ke kanan, sehingga atas dan kiri sudah selalu terhitung.</li></ul>
<p><strong>Kompleksitas:</strong> O(R × C).</p>',
    ],

    [
        'slug' => 'koleksi-koin',
        'lesson' => 'dp-grid',
        'title' => 'Robot Pengumpul Koin',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'grid', 'maksimum'],
        'statement' => '<p>Sebuah robot berada di pojok kiri atas grid <strong>R × C</strong>. Setiap petak berisi sejumlah koin. Robot bergerak hanya ke <strong>kanan</strong> atau <strong>bawah</strong> sampai pojok kanan bawah, dan mengambil semua koin di petak yang dilewati (termasuk petak awal dan akhir).</p>
<p>Berapa <strong>jumlah koin maksimum</strong> yang bisa dikumpulkan?</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>. <code>R</code> baris berikutnya masing-masing berisi <code>C</code> bilangan, yaitu banyak koin di setiap petak.</p>',
        'output_format' => '<p>Jumlah koin maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 200</li><li>0 ≤ koin ≤ 100</li></ul>',
        'samples' => [
            ['input' => "3 4\n1 3 1 2\n1 5 1 9\n4 2 1 1\n", 'explanation' => 'Jalur terbaik 1 → 3 → 5 → 1 → 9 → 1 mendapat 20 koin.'],
        ],
        'tests' => function () {
            $mk = function (int $r, int $c) {
                $rows = [];
                for ($i = 0; $i < $r; $i++) {
                    $row = [];
                    for ($j = 0; $j < $c; $j++) {
                        $row[] = mt_rand(0, 100);
                    }
                    $rows[] = implode(' ', $row);
                }

                return "$r $c\n".implode("\n", $rows)."\n";
            };

            return ["1 1\n7\n", "1 5\n1 2 3 4 5\n", "4 1\n5\n0\n3\n9\n", $mk(5, 5), $mk(60, 90), $mk(200, 200), $mk(200, 150)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C] = T::ints($lines[0]);
            $dp = [];
            for ($i = 0; $i < $R; $i++) {
                $row = T::ints($lines[$i + 1]);
                for ($j = 0; $j < $C; $j++) {
                    $best = 0;
                    if ($i > 0) {
                        $best = $dp[$i - 1][$j];
                    }
                    if ($j > 0) {
                        $best = $i > 0 ? max($best, $dp[$i][$j - 1]) : $dp[$i][$j - 1];
                    }
                    $dp[$i][$j] = $best + $row[$j];
                }
            }

            return (string) $dp[$R - 1][$C - 1];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const a = [];
for (let i = 0; i < R; i++) a.push(readInts());

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
a = [list(map(int, input().split())) for _ in range(R)]

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const a = [];
for (let i = 0; i < R; i++) a.push(readInts());

// dp[i][j] = koin maksimum yang terkumpul saat tiba di (i,j)
const dp = Array.from({ length: R }, () => new Array(C).fill(0));

for (let i = 0; i < R; i++) {
  for (let j = 0; j < C; j++) {
    let terbaik = 0;
    if (i > 0 && j > 0) terbaik = Math.max(dp[i - 1][j], dp[i][j - 1]);
    else if (i > 0) terbaik = dp[i - 1][j];
    else if (j > 0) terbaik = dp[i][j - 1];
    dp[i][j] = terbaik + a[i][j];
  }
}

console.log(dp[R - 1][C - 1]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
a = [list(map(int, input().split())) for _ in range(R)]

dp = [[0] * C for _ in range(R)]
for i in range(R):
    for j in range(C):
        if i > 0 and j > 0:
            terbaik = max(dp[i - 1][j], dp[i][j - 1])
        elif i > 0:
            terbaik = dp[i - 1][j]
        elif j > 0:
            terbaik = dp[i][j - 1]
        else:
            terbaik = 0
        dp[i][j] = terbaik + a[i][j]

print(dp[R - 1][C - 1])
CODE,
        ],
        'editorial' => '<p>Strukturnya persis sama dengan menghitung jalur, tetapi kali ini kita mencari <strong>maksimum</strong>, bukan menjumlahkan banyak cara:</p>
<p style="text-align:center"><code>dp[i][j] = a[i][j] + max(dp[i−1][j], dp[i][j−1])</code></p>
<p>Hati-hati dengan baris pertama dan kolom pertama: hanya ada satu arah datang. Untuk petak (0, 0), nilainya cukup <code>a[0][0]</code>.</p>
<p><strong>Pola penting:</strong> "banyak cara" memakai <code>+</code>, sedangkan "nilai terbaik" memakai <code>max</code>/<code>min</code>. Bentuk tabel dan urutan pengisiannya sama!</p>
<p><strong>Kompleksitas:</strong> O(R × C).</p>',
    ],

    // ═════════════════════════════ Knapsack ═════════════════════════════
    [
        'slug' => 'ransel-pendaki',
        'lesson' => 'knapsack',
        'title' => 'Ransel Pendaki',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'knapsack'],
        'statement' => '<p>Tim pecinta alam akan mendaki gunung. Ransel mereka hanya kuat menampung beban <strong>W</strong> kg. Ada <strong>N</strong> barang. Barang ke-i punya berat <code>w<sub>i</sub></code> dan nilai kegunaan <code>v<sub>i</sub></code>.</p>
<p>Setiap barang hanya ada <strong>satu</strong>: dibawa atau tidak. Pilih barang-barang sehingga total berat tidak melebihi W dan <strong>total nilai kegunaan maksimum</strong>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N W</code>. <code>N</code> baris berikutnya berisi <code>w<sub>i</sub> v<sub>i</sub></code>.</p>',
        'output_format' => '<p>Total nilai maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100</li><li>1 ≤ W ≤ 10 000</li><li>1 ≤ w<sub>i</sub> ≤ 10 000</li><li>1 ≤ v<sub>i</sub> ≤ 1 000</li></ul>',
        'samples' => [
            ['input' => "4 7\n1 1\n3 4\n4 5\n5 7\n", 'explanation' => 'Pilih barang 2 dan 3 (berat 3 + 4 = 7, nilai 4 + 5 = 9). Barang 4 bernilai 7 tapi seberat 5, dan sisa kapasitas 2 hanya cukup untuk barang 1 (total 8).'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $w, int $maxW) {
                $lines = ["$n $w"];
                for ($i = 0; $i < $n; $i++) {
                    $lines[] = mt_rand(1, $maxW).' '.mt_rand(1, 1000);
                }

                return implode("\n", $lines)."\n";
            };

            return ["1 5\n6 10\n", "1 5\n5 10\n", "3 10\n5 10\n4 40\n6 30\n", $mk(10, 50, 20), $mk(50, 1000, 300), $mk(100, 10000, 1000), $mk(100, 10000, 10000), $mk(100, 500, 50)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $W] = T::ints($lines[0]);
            $dp = array_fill(0, $W + 1, 0);
            for ($i = 1; $i <= $n; $i++) {
                [$w, $v] = T::ints($lines[$i]);
                for ($c = $W; $c >= $w; $c--) {
                    $dp[$c] = max($dp[$c], $dp[$c - $w] + $v);
                }
            }

            return (string) $dp[$W];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n, W] = readInts();
const items = [];
for (let i = 0; i < n; i++) items.push(readInts()); // [berat, nilai]

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, W = map(int, input().split())
items = [tuple(map(int, input().split())) for _ in range(n)]  # (berat, nilai)

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, W] = readInts();
const items = [];
for (let i = 0; i < n; i++) items.push(readInts());

// dp[c] = nilai maksimum dengan kapasitas c (memakai barang yang sudah diproses)
const dp = new Array(W + 1).fill(0);

for (const [w, v] of items) {
  // loop MUNDUR agar setiap barang hanya dipakai sekali
  for (let c = W; c >= w; c--) {
    dp[c] = Math.max(dp[c], dp[c - w] + v);
  }
}

console.log(dp[W]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, W = map(int, input().split())
items = [tuple(map(int, input().split())) for _ in range(n)]

dp = [0] * (W + 1)
for w, v in items:
    for c in range(W, w - 1, -1):  # mundur: tiap barang sekali
        if dp[c - w] + v > dp[c]:
            dp[c] = dp[c - w] + v

print(dp[W])
CODE,
        ],
        'editorial' => '<p><strong>State 2D:</strong> <code>dp[i][c]</code> = nilai maksimum dengan memakai barang 1..i dan kapasitas c. Untuk barang ke-i ada dua pilihan:</p>
<ul><li><strong>Tidak diambil:</strong> <code>dp[i−1][c]</code></li>
<li><strong>Diambil</strong> (jika w ≤ c): <code>dp[i−1][c−w] + v</code></li></ul>
<p style="text-align:center"><code>dp[i][c] = max(dp[i−1][c], dp[i−1][c−w] + v)</code></p>
<p><strong>Hemat memori ke 1D:</strong> baris i hanya bergantung pada baris i−1. Jika kita loop kapasitas <em>mundur</em> dari W ke w, nilai <code>dp[c−w]</code> yang dibaca masih nilai "baris lama", sehingga setiap barang hanya terpakai sekali. Loop <em>maju</em> justru menghasilkan knapsack <strong>unbounded</strong> (barang bisa dipakai berkali-kali).</p>
<p><strong>Kompleksitas:</strong> O(N × W) waktu, O(W) memori.</p>',
    ],

    [
        'slug' => 'bagi-dua-adil',
        'lesson' => 'knapsack',
        'title' => 'Bagi Dua yang Adil',
        'difficulty' => 'Sulit',
        'tags' => ['dp', 'knapsack', 'subset sum'],
        'statement' => '<p>Dua kakak beradik mendapat <strong>N</strong> kantong permen. Kantong ke-i berisi <code>a<sub>i</sub></code> permen. Kantong tidak boleh dibuka atau dipecah, dan setiap kantong harus diberikan ke salah satu dari mereka.</p>
<p>Bagi semua kantong agar <strong>selisih</strong> jumlah permen keduanya <strong>sekecil mungkin</strong>. Berapa selisih minimumnya?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a1 a2 ... aN</code>.</p>',
        'output_format' => '<p>Selisih minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100</li><li>1 ≤ a<sub>i</sub> ≤ 100</li></ul>',
        'samples' => [
            ['input' => "5\n3 1 4 2 2\n", 'explanation' => 'Total 12. Bagian {3, 1, 2} = 6 dan {4, 2} = 6, sehingga selisihnya 0.'],
            ['input' => "3\n10 2 3\n", 'explanation' => 'Pembagian terbaik {10} dan {2, 3}, selisih 10 − 5 = 5.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, $max);
                }

                return "$n\n".implode(' ', $a)."\n";
            };

            return ["1\n7\n", "2\n5 5\n", "2\n1 100\n", $mk(8, 20), $mk(30, 100), $mk(100, 100), $mk(100, 3), "4\n1 2 4 100\n"];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $S = array_sum($a);
            $can = array_fill(0, $S + 1, false);
            $can[0] = true;
            foreach ($a as $x) {
                for ($s = $S; $s >= $x; $s--) {
                    if ($can[$s - $x]) {
                        $can[$s] = true;
                    }
                }
            }
            for ($s = intdiv($S, 2); $s >= 0; $s--) {
                if ($can[$s]) {
                    return (string) ($S - 2 * $s);
                }
            }

            return (string) $S;
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
const total = a.reduce((s, x) => s + x, 0);

// bisa[s] = apakah ada sekumpulan kantong berjumlah tepat s
const bisa = new Array(total + 1).fill(false);
bisa[0] = true;

for (const x of a) {
  for (let s = total; s >= x; s--) { // mundur: knapsack 0/1
    if (bisa[s - x]) bisa[s] = true;
  }
}

// Cari jumlah terdekat ke total/2 untuk bagian pertama
for (let s = Math.floor(total / 2); s >= 0; s--) {
  if (bisa[s]) {
    console.log(total - 2 * s);
    break;
  }
}
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
total = sum(a)

bisa = [False] * (total + 1)  # bisa[s]: ada subset berjumlah s?
bisa[0] = True
for x in a:
    for s in range(total, x - 1, -1):
        if bisa[s - x]:
            bisa[s] = True

for s in range(total // 2, -1, -1):
    if bisa[s]:
        print(total - 2 * s)
        break
CODE,
        ],
        'editorial' => '<p>Jika bagian pertama berjumlah <code>s</code>, bagian kedua berjumlah <code>total − s</code> dan selisihnya <code>|total − 2s|</code>. Selisih paling kecil saat <code>s</code> sedekat mungkin dengan <code>total / 2</code>.</p>
<p>Jadi soal berubah menjadi: <strong>jumlah apa saja yang bisa dibentuk</strong> dari sebagian kantong? Ini knapsack 0/1 versi <em>boolean</em> (subset sum):</p>
<p style="text-align:center"><code>bisa[s] = bisa[s] OR bisa[s − a<sub>i</sub>]</code></p>
<p>Lalu cari <code>s ≤ total/2</code> terbesar yang bisa dibentuk. Teknik "mengubah soal menjadi knapsack" seperti ini sangat sering muncul di lomba!</p>
<p><strong>Kompleksitas:</strong> O(N × total) ≤ 100 × 10 000.</p>',
    ],

    // ═════════════════════════════ LCS ═════════════════════════════
    [
        'slug' => 'lcs-dna',
        'lesson' => 'lcs',
        'title' => 'Kemiripan DNA',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'lcs', 'string'],
        'statement' => '<p>Seorang peneliti biologi ingin mengukur kemiripan dua untai DNA. DNA ditulis sebagai string berisi huruf <code>A</code>, <code>C</code>, <code>G</code>, dan <code>T</code>.</p>
<p>Ukuran kemiripannya adalah panjang <strong>Longest Common Subsequence</strong> (LCS): subsequence terpanjang yang muncul di kedua string. Subsequence diperoleh dengan menghapus nol atau lebih karakter <strong>tanpa mengubah urutan</strong> karakter yang tersisa.</p>',
        'input_format' => '<p>Dua baris, masing-masing berisi satu string DNA.</p>',
        'output_format' => '<p>Panjang LCS.</p>',
        'constraints' => '<ul><li>1 ≤ panjang setiap string ≤ 1000</li></ul>',
        'samples' => [
            ['input' => "ACGTAG\nCATAGG\n", 'explanation' => 'Salah satu LCS adalah "CTAG" (panjang 4): A<u>C</u>G<u>TAG</u> dan <u>C</u>A<u>TAG</u>G.'],
            ['input' => "AAA\nTTT\n", 'explanation' => 'Tidak ada huruf yang sama sehingga LCS-nya 0.'],
        ],
        'tests' => fn () => ["A\nA\n", "A\nC\n", "ACGT\nACGT\n", T::word(10, 'ACGT')."\n".T::word(12, 'ACGT')."\n", T::word(300, 'ACGT')."\n".T::word(250, 'ACGT')."\n", T::word(1000, 'ACGT')."\n".T::word(1000, 'ACGT')."\n", T::word(1000, 'AC')."\n".T::word(900, 'GT')."\n", str_repeat('A', 1000)."\n".str_repeat('A', 500)."\n"],
        'solve' => function (string $input) {
            [$a, $b] = T::lines($input);
            $a = trim($a);
            $b = trim($b);
            $n = strlen($a);
            $m = strlen($b);
            $prev = array_fill(0, $m + 1, 0);
            for ($i = 1; $i <= $n; $i++) {
                $cur = [0];
                for ($j = 1; $j <= $m; $j++) {
                    $cur[$j] = $a[$i - 1] === $b[$j - 1] ? $prev[$j - 1] + 1 : max($prev[$j], $cur[$j - 1]);
                }
                $prev = $cur;
            }

            return (string) $prev[$m];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const a = readLine().trim();
const b = readLine().trim();

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
a = input().strip()
b = input().strip()

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const a = readLine().trim();
const b = readLine().trim();
const n = a.length;
const m = b.length;

// dp[i][j] = LCS dari a[0..i) dan b[0..j)
const dp = Array.from({ length: n + 1 }, () => new Array(m + 1).fill(0));

for (let i = 1; i <= n; i++) {
  for (let j = 1; j <= m; j++) {
    if (a[i - 1] === b[j - 1]) {
      dp[i][j] = dp[i - 1][j - 1] + 1; // huruf sama → perpanjang diagonal
    } else {
      dp[i][j] = Math.max(dp[i - 1][j], dp[i][j - 1]); // buang salah satu huruf
    }
  }
}

console.log(dp[n][m]);
CODE,
            'python' => <<<'CODE'
a = input().strip()
b = input().strip()
n, m = len(a), len(b)

# hemat memori: simpan baris sebelumnya saja
prev = [0] * (m + 1)
for i in range(1, n + 1):
    cur = [0] * (m + 1)
    ai = a[i - 1]
    for j in range(1, m + 1):
        if ai == b[j - 1]:
            cur[j] = prev[j - 1] + 1
        else:
            cur[j] = prev[j] if prev[j] > cur[j - 1] else cur[j - 1]
    prev = cur

print(prev[m])
CODE,
        ],
        'editorial' => '<p><strong>State:</strong> <code>dp[i][j]</code> = panjang LCS dari <code>i</code> huruf pertama string a dan <code>j</code> huruf pertama string b.</p>
<p><strong>Transisi:</strong> lihat huruf terakhir masing-masing, yaitu <code>a[i−1]</code> dan <code>b[j−1]</code>:</p>
<ul><li>Jika <strong>sama</strong>: huruf itu pasti bagian dari LCS → <code>dp[i−1][j−1] + 1</code> (panah diagonal).</li>
<li>Jika <strong>beda</strong>: salah satunya harus dibuang → <code>max(dp[i−1][j], dp[i][j−1])</code>.</li></ul>
<p><strong>Base case:</strong> baris 0 dan kolom 0 bernilai 0, karena LCS dengan string kosong adalah 0.</p>
<p><strong>Kompleksitas:</strong> O(N × M). Memori bisa dihemat menjadi O(M) dengan hanya menyimpan baris sebelumnya.</p>',
    ],

    [
        'slug' => 'jarak-edit',
        'lesson' => 'lcs',
        'title' => 'Koreksi Ketikan',
        'difficulty' => 'Sulit',
        'tags' => ['dp', 'edit distance', 'string'],
        'statement' => '<p>Fitur <em>autocorrect</em> di keyboard ponsel menghitung seberapa jauh kata yang kamu ketik dari kata di kamus. Ukurannya adalah <strong>edit distance</strong>: banyak operasi minimum untuk mengubah kata pertama menjadi kata kedua.</p>
<p>Operasi yang diizinkan (masing-masing bernilai 1):</p>
<ul><li><strong>Sisip</strong> satu huruf</li><li><strong>Hapus</strong> satu huruf</li><li><strong>Ganti</strong> satu huruf dengan huruf lain</li></ul>',
        'input_format' => '<p>Dua baris, masing-masing berisi satu kata huruf kecil.</p>',
        'output_format' => '<p>Edit distance minimum.</p>',
        'constraints' => '<ul><li>1 ≤ panjang setiap kata ≤ 800</li></ul>',
        'samples' => [
            ['input' => "kucing\nkuning\n", 'explanation' => 'Cukup ganti "c" menjadi "n": 1 operasi.'],
            ['input' => "sore\nsoto\n", 'explanation' => 'Ganti "r" → "t" dan "e" → "o": 2 operasi.'],
            ['input' => "abc\nyabd\n", 'explanation' => 'Sisip "y" di depan, lalu ganti "c" menjadi "d": 2 operasi.'],
        ],
        'tests' => fn () => ["a\na\n", "a\nb\n", "abc\nabcdef\n", "kitten\nsitting\n", T::word(20, 'abc')."\n".T::word(25, 'abc')."\n", T::word(400, 'abcdefghij')."\n".T::word(350, 'abcdefghij')."\n", T::word(800, 'ab')."\n".T::word(800, 'ab')."\n", str_repeat('a', 800)."\n".str_repeat('b', 10)."\n"],
        'solve' => function (string $input) {
            [$a, $b] = T::lines($input);
            $a = trim($a);
            $b = trim($b);
            $n = strlen($a);
            $m = strlen($b);
            $prev = range(0, $m);
            for ($i = 1; $i <= $n; $i++) {
                $cur = [$i];
                for ($j = 1; $j <= $m; $j++) {
                    $cur[$j] = $a[$i - 1] === $b[$j - 1]
                        ? $prev[$j - 1]
                        : 1 + min($prev[$j - 1], $prev[$j], $cur[$j - 1]);
                }
                $prev = $cur;
            }

            return (string) $prev[$m];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const a = readLine().trim();
const b = readLine().trim();

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
a = input().strip()
b = input().strip()

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const a = readLine().trim();
const b = readLine().trim();
const n = a.length;
const m = b.length;

// dp[i][j] = edit distance dari a[0..i) ke b[0..j)
const dp = Array.from({ length: n + 1 }, () => new Array(m + 1).fill(0));
for (let i = 0; i <= n; i++) dp[i][0] = i; // hapus semua
for (let j = 0; j <= m; j++) dp[0][j] = j; // sisip semua

for (let i = 1; i <= n; i++) {
  for (let j = 1; j <= m; j++) {
    if (a[i - 1] === b[j - 1]) {
      dp[i][j] = dp[i - 1][j - 1]; // huruf sama, tidak perlu operasi
    } else {
      dp[i][j] = 1 + Math.min(
        dp[i - 1][j - 1], // ganti
        dp[i - 1][j],     // hapus a[i-1]
        dp[i][j - 1],     // sisip b[j-1]
      );
    }
  }
}

console.log(dp[n][m]);
CODE,
            'python' => <<<'CODE'
a = input().strip()
b = input().strip()
n, m = len(a), len(b)

prev = list(range(m + 1))  # baris i = 0
for i in range(1, n + 1):
    cur = [i] + [0] * m
    ai = a[i - 1]
    for j in range(1, m + 1):
        if ai == b[j - 1]:
            cur[j] = prev[j - 1]
        else:
            cur[j] = 1 + min(prev[j - 1], prev[j], cur[j - 1])
    prev = cur

print(prev[m])
CODE,
        ],
        'editorial' => '<p>Tabelnya sama seperti LCS, hanya isinya berbeda. <code>dp[i][j]</code> = operasi minimum untuk mengubah <code>i</code> huruf pertama a menjadi <code>j</code> huruf pertama b.</p>
<ul><li>Jika <code>a[i−1] = b[j−1]</code>: tidak perlu operasi → <code>dp[i−1][j−1]</code>.</li>
<li>Jika berbeda: <code>1 + min(ganti, hapus, sisip)</code> = <code>1 + min(dp[i−1][j−1], dp[i−1][j], dp[i][j−1])</code>.</li></ul>
<p><strong>Base case:</strong> <code>dp[i][0] = i</code> (hapus semua huruf) dan <code>dp[0][j] = j</code> (sisip semua huruf).</p>
<p>Ketiga panah (diagonal, atas, kiri) sama persis dengan visualisasi LCS. Setelah memahami satu DP dua-string, kamu bisa menyelesaikan banyak soal sejenis!</p>
<p><strong>Kompleksitas:</strong> O(N × M).</p>',
    ],
];
