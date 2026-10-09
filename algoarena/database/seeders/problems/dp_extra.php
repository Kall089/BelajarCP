<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan tambahan track Dynamic Programming (bagian 1):
 * konsep DP, DP 1D, menghitung cara & modulo, DP grid, knapsack.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$MOD = 1000000007;

/** Pangkat modulo (PHP). Perkalian dua bilangan < 10^9+7 (≈ 10^18) masih muat di int 64-bit. */
$powmod = function (int $b, int $e, int $m): int {
    $r = 1;
    $b %= $m;
    while ($e > 0) {
        if ($e & 1) {
            $r = $r * $b % $m;
        }
        $b = $b * $b % $m;
        $e >>= 1;
    }

    return $r;
};

$arrInput = function (int $n, int $lo, int $hi): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = mt_rand($lo, $hi);
    }

    return "$n\n".implode(' ', $a)."\n";
};

return [
    // ═════════════════════════════ Konsep DP ═════════════════════════════
    [
        'slug' => 'lempar-dadu',
        'lesson' => 'konsep-dp',
        'title' => 'Lempar Dadu',
        'difficulty' => 'Mudah',
        'tags' => ['dp', 'menghitung cara', 'modulo'],
        'statement' => '<p>Kamu melempar dadu bersisi 1–6 berkali-kali dan menjumlahkan hasilnya. Ada berapa <strong>urutan lemparan</strong> berbeda yang jumlahnya tepat <strong>N</strong>? Contohnya untuk N = 3 ada 4 urutan: 1+1+1, 1+2, 2+1, dan 3.</p>
<p>Cetak jawabannya modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Banyak urutan modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1 000 000</li></ul>',
        'samples' => [
            ['input' => "3\n", 'explanation' => '1+1+1, 1+2, 2+1, 3.'],
            ['input' => "8\n", 'explanation' => null],
        ],
        'tests' => fn () => ["1\n", "2\n", "6\n", "7\n", "50\n", "1000\n", "123456\n", "1000000\n"],
        'solve' => function (string $input) use ($MOD) {
            $n = (int) trim($input);
            $dp = array_fill(0, $n + 1, 0);
            $dp[0] = 1;
            for ($i = 1; $i <= $n; $i++) {
                $s = 0;
                for ($d = 1; $d <= 6 && $d <= $i; $d++) {
                    $s += $dp[$i - $d];
                }
                $dp[$i] = $s % $MOD;
            }

            return (string) $dp[$n];
        },
        'starter' => $st("    int n;\n    cin >> n;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n;
    cin >> n;
    // dp[i] = banyak urutan lemparan yang jumlahnya tepat i
    vector<long long> dp(n + 1, 0);
    dp[0] = 1;                        // satu cara: belum melempar sama sekali
    for (int i = 1; i <= n; i++)
        for (int d = 1; d <= 6 && d <= i; d++)   // lemparan TERAKHIR bernilai d
            dp[i] = (dp[i] + dp[i - d]) % MOD;
    cout << dp[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const MOD = 1000000007;
const dp = new Array(n + 1).fill(0);
dp[0] = 1;
for (let i = 1; i <= n; i++) {
  let s = 0;
  for (let d = 1; d <= 6 && d <= i; d++) s += dp[i - d];
  dp[i] = s % MOD;
}
console.log(dp[n]);
CODE,
            'python' => <<<'CODE'
n = int(input())
MOD = 10**9 + 7
dp = [0] * (n + 1)
dp[0] = 1
for i in range(1, n + 1):
    dp[i] = sum(dp[max(0, i - 6):i]) % MOD
print(dp[n])
CODE,
        ],
        'editorial' => '<p>Lihat <strong>lemparan terakhir</strong>. Jika lemparan terakhir bernilai d, lemparan sebelumnya harus berjumlah i − d. Keenam kemungkinan d tidak tumpang tindih, jadi:</p>
<p style="text-align:center"><code>dp[i] = dp[i−1] + dp[i−2] + … + dp[i−6]</code>, dengan <code>dp[0] = 1</code>.</p>
<p>Base case <code>dp[0] = 1</code> berarti "satu cara mencapai 0: tidak melempar". Ambil modulo di setiap langkah.</p>
<p><strong>Kompleksitas:</strong> O(6N).</p>',
        'hints' => [
            'Pikirkan lemparan terakhir: nilainya 1 sampai 6.',
            'Jika lemparan terakhir d, sisanya harus berjumlah i − d. dp[i] = jumlah dp[i − d].',
            'dp[0] = 1, dan ambil modulo 10^9 + 7 setiap kali menjumlah.',
        ],
    ],

    [
        'slug' => 'lampu-hias',
        'lesson' => 'konsep-dp',
        'title' => 'Lampu Hias Hemat',
        'difficulty' => 'Mudah',
        'tags' => ['dp', 'state', 'menghitung cara'],
        'statement' => '<p>Sederet <strong>N</strong> lampu hias bisa diatur menyala atau mati. Agar tidak terlalu panas, <strong>tidak boleh ada dua lampu bersebelahan yang sama-sama menyala</strong>.</p>
<p>Ada berapa pola nyala-mati yang sah? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Banyak pola modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1 000 000</li></ul>',
        'samples' => [
            ['input' => "3\n", 'explanation' => '000, 001, 010, 100, 101 (1 = menyala). Ada 5 pola.'],
        ],
        'tests' => fn () => ["1\n", "2\n", "4\n", "10\n", "90\n", "1000\n", "999999\n", "1000000\n"],
        'solve' => function (string $input) use ($MOD) {
            $n = (int) trim($input);
            $mati = 1;
            $nyala = 1;
            for ($i = 2; $i <= $n; $i++) {
                [$mati, $nyala] = [($mati + $nyala) % $MOD, $mati];
            }

            return (string) (($mati + $nyala) % $MOD);
        },
        'starter' => $st("    int n;\n    cin >> n;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n;
    cin >> n;
    // State dua dimensi: banyak pola sepanjang i yang lampu TERAKHIRNYA mati / menyala
    vector<long long> mati(n + 1), nyala(n + 1);
    mati[1] = 1;
    nyala[1] = 1;
    for (int i = 2; i <= n; i++) {
        mati[i] = (mati[i - 1] + nyala[i - 1]) % MOD;  // setelah apa pun boleh mati
        nyala[i] = mati[i - 1];                        // menyala hanya setelah lampu mati
    }
    cout << (mati[n] + nyala[n]) % MOD << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const MOD = 1000000007;
let mati = 1, nyala = 1;
for (let i = 2; i <= n; i++) [mati, nyala] = [(mati + nyala) % MOD, mati];
console.log((mati + nyala) % MOD);
CODE,
            'python' => <<<'CODE'
n = int(input())
MOD = 10**9 + 7
mati, nyala = 1, 1
for _ in range(2, n + 1):
    mati, nyala = (mati + nyala) % MOD, mati
print((mati + nyala) % MOD)
CODE,
        ],
        'editorial' => '<p>Saat menambah lampu ke-i, yang penting hanyalah <strong>keadaan lampu sebelumnya</strong>. Jadi simpan dua angka per posisi:</p>
<ul><li><code>mati[i]</code> = pola sah sepanjang i yang lampu terakhirnya mati = <code>mati[i−1] + nyala[i−1]</code>.</li>
<li><code>nyala[i]</code> = pola sah yang lampu terakhirnya menyala = <code>mati[i−1]</code> (lampu sebelumnya wajib mati).</li></ul>
<p>Jawaban = <code>mati[N] + nyala[N]</code>. Polanya ternyata bilangan Fibonacci! Karena hanya baris sebelumnya yang dipakai, cukup dua variabel.</p>
<p><strong>Kompleksitas:</strong> O(N) waktu, O(1) memori.</p>',
        'hints' => [
            'Saat menambah lampu baru, informasi apa tentang pola sebelumnya yang dibutuhkan?',
            'Cukup tahu apakah lampu terakhir menyala atau mati. Simpan dua hitungan per posisi.',
            'mati[i] = mati[i−1] + nyala[i−1], nyala[i] = mati[i−1].',
        ],
    ],

    // ═════════════════════════════ DP 1D ═════════════════════════════
    [
        'slug' => 'panen-mangga',
        'lesson' => 'dp-1d',
        'title' => 'Panen Mangga',
        'difficulty' => 'Mudah',
        'tags' => ['dp', 'ambil atau lewati'],
        'statement' => '<p>Di pinggir jalan ada <strong>N</strong> pohon mangga berjajar. Pohon ke-i berbuah <code>a<sub>i</sub></code> buah. Pemilik kebun mengizinkan kamu memanen, dengan syarat <strong>tidak boleh memanen dua pohon yang bersebelahan</strong>.</p>
<p>Berapa buah terbanyak yang bisa dipanen?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi a<sub>1</sub> … a<sub>N</sub>.</p>',
        'output_format' => '<p>Banyak buah maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n2 7 9 3 1\n", 'explanation' => 'Panen pohon 1, 3, 5: 2 + 9 + 1 = 12.'],
            ['input' => "4\n5 1 1 5\n", 'explanation' => 'Pohon 1 dan 4: 10. Serakah memilih yang terbesar tidak selalu berhasil.'],
        ],
        'tests' => function () use ($arrInput) {
            return ["1\n7\n", "2\n3 8\n", $arrInput(10, 0, 20), $arrInput(1000, 0, 1000), $arrInput(200000, 0, 1000000000), $arrInput(200000, 0, 3), $arrInput(199999, 1000000000, 1000000000)];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $prev2 = 0;
            $prev1 = 0;
            foreach ($a as $x) {
                [$prev2, $prev1] = [$prev1, max($prev1, $prev2 + $x)];
            }

            return (string) $prev1;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> a(n);\n    for (auto& x : a) cin >> x;", "const [n] = readInts();\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // dp[i] = buah terbanyak dari pohon 0..i
    vector<long long> dp(n);
    dp[0] = a[0];
    if (n > 1) dp[1] = max(a[0], a[1]);
    for (int i = 2; i < n; i++)
        dp[i] = max(dp[i - 1],          // pohon i dilewati
                    dp[i - 2] + a[i]);  // pohon i dipanen → pohon i-1 pasti dilewati
    cout << dp[n - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
// Total bisa mencapai 10^14: masih aman untuk Number
let prev2 = 0, prev1 = 0;
for (const x of a) [prev2, prev1] = [prev1, Math.max(prev1, prev2 + x)];
console.log(prev1);
CODE,
            'python' => <<<'CODE'
n = int(input())
a = list(map(int, input().split()))
prev2 = prev1 = 0
for x in a:
    prev2, prev1 = prev1, max(prev1, prev2 + x)
print(prev1)
CODE,
        ],
        'editorial' => '<p>Untuk pohon ke-i hanya ada dua pilihan:</p>
<ul><li><strong>Dilewati</strong>: hasil terbaik sama dengan pohon 0..i−1, <code>dp[i−1]</code>.</li>
<li><strong>Dipanen</strong>: pohon i−1 harus dilewati, jadi <code>dp[i−2] + a[i]</code>.</li></ul>
<p style="text-align:center"><code>dp[i] = max(dp[i−1], dp[i−2] + a[i])</code></p>
<p>Karena hanya dua nilai sebelumnya yang dipakai, cukup dua variabel. Total bisa mencapai 10<sup>14</sup>: gunakan <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
        'hints' => [
            'Memilih pohon terbesar lebih dulu tidak selalu optimal (lihat contoh kedua).',
            'Untuk pohon ke-i: dipanen atau dilewati. Jika dipanen, pohon i−1 pasti dilewati.',
            'dp[i] = max(dp[i−1], dp[i−2] + a[i]). Gunakan long long.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'untung-terbesar',
        'lesson' => 'dp-1d',
        'title' => 'Periode Paling Untung',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'kadane', 'subarray'],
        'statement' => '<p>Koperasi sekolah mencatat untung harian selama <strong>N</strong> hari; nilai negatif berarti rugi. Kepala koperasi ingin melaporkan <strong>periode berurutan</strong> (paling sedikit satu hari) dengan total untung terbesar.</p>
<p>Berapa total untung terbesar itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N bilangan bulat.</p>',
        'output_format' => '<p>Jumlah subarray terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "8\n-2 1 -3 4 -1 2 1 -5\n", 'explanation' => 'Hari ke-4 sampai ke-7: 4 − 1 + 2 + 1 = 6.'],
            ['input' => "3\n-5 -2 -7\n", 'explanation' => 'Semua rugi: periode terbaik hanya hari ke-2 (−2).'],
        ],
        'tests' => function () use ($arrInput) {
            return ["1\n-7\n", "1\n5\n", $arrInput(10, -10, 10), $arrInput(1000, -1000, 1000), $arrInput(200000, -1000000000, 1000000000), $arrInput(200000, -1000000000, -1), $arrInput(200000, 1, 1000000000), $arrInput(200000, -3, 2)];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $best = $a[0];
            $cur = $a[0];
            for ($i = 1; $i < count($a); $i++) {
                $cur = max($a[$i], $cur + $a[$i]);
                $best = max($best, $cur);
            }

            return (string) $best;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> a(n);\n    for (auto& x : a) cin >> x;", "const [n] = readInts();\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // Kadane: akhir[i] = jumlah terbesar subarray yang BERAKHIR tepat di hari i
    long long akhir = a[0], jawaban = a[0];
    for (int i = 1; i < n; i++) {
        akhir = max(a[i], akhir + a[i]);   // mulai baru di i, atau sambung periode sebelumnya
        jawaban = max(jawaban, akhir);
    }
    cout << jawaban << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
let akhir = a[0], jawaban = a[0];
for (let i = 1; i < n; i++) {
  akhir = Math.max(a[i], akhir + a[i]);
  jawaban = Math.max(jawaban, akhir);
}
console.log(jawaban);
CODE,
            'python' => <<<'CODE'
n = int(input())
a = list(map(int, input().split()))
akhir = jawaban = a[0]
for x in a[1:]:
    akhir = max(x, akhir + x)
    jawaban = max(jawaban, akhir)
print(jawaban)
CODE,
        ],
        'editorial' => '<p>State yang tepat: <code>akhir[i]</code> = total terbesar periode yang <strong>berakhir tepat di hari i</strong>. Periode itu entah hanya hari i saja, entah menyambung periode terbaik yang berakhir di hari i − 1:</p>
<p style="text-align:center"><code>akhir[i] = max(a[i], akhir[i−1] + a[i])</code></p>
<p>Jawabannya maksimum seluruh <code>akhir[i]</code>. Ini <strong>algoritma Kadane</strong>, O(N). Perhatikan kasus semua negatif: periode tidak boleh kosong, jadi jangan memulai jawaban dari 0.</p>',
        'hints' => [
            'Mencoba semua pasangan awal–akhir butuh O(N²).',
            'Definisikan akhir[i] = jumlah terbesar subarray yang berakhir tepat di i.',
            'akhir[i] = max(a[i], akhir[i−1] + a[i]); jawabannya maksimum semua akhir[i]. Hati-hati jika semua negatif.',
        ],
        
    ],

    // ═════════════════════════════ DP menghitung cara & modulo ═════════════════════════════
    [
        'slug' => 'kombinasi-besar',
        'lesson' => 'dp-hitung',
        'title' => 'Memilih Panitia',
        'difficulty' => 'Sedang',
        'tags' => ['kombinatorika', 'modulo', 'invers modular'],
        'statement' => '<p>Sekolah sedang memilih panitia. Ada <strong>Q</strong> pertanyaan: dari <code>n</code> calon, berapa banyak cara memilih <code>k</code> orang (urutan tidak penting)? Cetak setiap jawaban modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya berisi <code>n k</code>.</p>',
        'output_format' => '<p>Q baris: C(n, k) mod 10<sup>9</sup> + 7 (0 jika k &gt; n).</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 200 000</li><li>0 ≤ k, n ≤ 1 000 000</li></ul>',
        'samples' => [
            ['input' => "4\n5 2\n10 0\n3 5\n1000000 500000\n", 'explanation' => 'C(5,2) = 10, C(10,0) = 1, C(3,5) = 0.'],
        ],
        'tests' => function () {
            $mk = function (int $q, int $maxN) {
                $out = [(string) $q];
                for ($i = 0; $i < $q; $i++) {
                    $n = mt_rand(0, $maxN);
                    $out[] = $n.' '.mt_rand(0, $n + 2);
                }

                return implode("\n", $out)."\n";
            };

            return ["1\n0 0\n", $mk(20, 20), $mk(1000, 1000), $mk(200000, 1000000), $mk(200000, 50)];
        },
        'solve' => function (string $input) use ($MOD, $powmod) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $qs = [];
            $maxN = 0;
            for ($i = 1; $i <= $q; $i++) {
                $qs[] = T::ints($lines[$i]);
                $maxN = max($maxN, $qs[$i - 1][0]);
            }
            $fact = [1];
            for ($i = 1; $i <= $maxN; $i++) {
                $fact[$i] = $fact[$i - 1] * $i % $MOD;
            }
            $inv = array_fill(0, $maxN + 1, 1);
            $inv[$maxN] = $powmod($fact[$maxN], $MOD - 2, $MOD);
            for ($i = $maxN; $i > 0; $i--) {
                $inv[$i - 1] = $inv[$i] * $i % $MOD;
            }
            $out = [];
            foreach ($qs as [$n, $k]) {
                $out[] = $k > $n ? 0 : $fact[$n] * $inv[$k] % $MOD * $inv[$n - $k] % $MOD;
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int q;\n    cin >> q;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;
const int N = 1000000;
long long fact[N + 1], inv[N + 1];

long long pangkat(long long b, long long e) {   // b^e mod MOD dalam O(log e)
    long long r = 1;
    b %= MOD;
    while (e > 0) {
        if (e & 1) r = r * b % MOD;
        b = b * b % MOD;
        e >>= 1;
    }
    return r;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    fact[0] = 1;
    for (int i = 1; i <= N; i++) fact[i] = fact[i - 1] * i % MOD;
    inv[N] = pangkat(fact[N], MOD - 2);          // Teorema kecil Fermat: x^(p-2) = 1/x
    for (int i = N; i > 0; i--) inv[i - 1] = inv[i] * i % MOD;

    int q;
    cin >> q;
    while (q--) {
        int n, k;
        cin >> n >> k;
        if (k > n) cout << 0 << '\n';
        else cout << fact[n] * inv[k] % MOD * inv[n - k] % MOD << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Perkalian dua bilangan < 10^9+7 bisa ~10^18, melebihi presisi Number (2^53).
// mulmod memecah perkalian agar tetap tepat.
const MOD = 1000000007;
function mulmod(a, b) {
  const bh = Math.floor(b / 65536), bl = b % 65536;
  return ((a * bh % MOD) * 65536 + a * bl) % MOD;
}
function pangkat(b, e) {
  let r = 1;
  while (e > 0) {
    if (e & 1) r = mulmod(r, b);
    b = mulmod(b, b);
    e = Math.floor(e / 2);
  }
  return r;
}
const [q] = readInts();
const qs = [];
let maxN = 0;
for (let i = 0; i < q; i++) { const p = readInts(); qs.push(p); maxN = Math.max(maxN, p[0]); }
const fact = new Array(maxN + 1), inv = new Array(maxN + 1);
fact[0] = 1;
for (let i = 1; i <= maxN; i++) fact[i] = mulmod(fact[i - 1], i);
inv[maxN] = pangkat(fact[maxN], MOD - 2);
for (let i = maxN; i > 0; i--) inv[i - 1] = mulmod(inv[i], i);
const out = qs.map(([n, k]) => (k > n ? 0 : mulmod(mulmod(fact[n], inv[k]), inv[n - k])));
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7
q = int(input())
qs = [tuple(map(int, input().split())) for _ in range(q)]
N = max(n for n, k in qs)
fact = [1] * (N + 1)
for i in range(1, N + 1):
    fact[i] = fact[i - 1] * i % MOD
inv = [1] * (N + 1)
inv[N] = pow(fact[N], MOD - 2, MOD)
for i in range(N, 0, -1):
    inv[i - 1] = inv[i] * i % MOD
out = [0 if k > n else fact[n] * inv[k] % MOD * inv[n - k] % MOD for n, k in qs]
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Segitiga Pascal butuh tabel 10<sup>6</sup> × 10<sup>6</sup>: mustahil. Gunakan rumus <code>C(n, k) = n! / (k! · (n − k)!)</code>, dengan pembagian diganti perkalian <strong>invers modular</strong>.</p>
<p>Karena 10<sup>9</sup> + 7 prima, invers x adalah <code>x<sup>p−2</sup> mod p</code> (Teorema kecil Fermat), dihitung dengan pangkat cepat. Siapkan <code>fact[i]</code> dan <code>inv[i] = 1 / i!</code> sekali: <code>inv[N]</code> dengan satu pangkat, lalu mundur <code>inv[i−1] = inv[i] · i</code>.</p>
<p>Setiap pertanyaan lalu dijawab O(1). <strong>Kompleksitas:</strong> O(N + log p + Q).</p>',
        'hints' => [
            'n sampai 10^6: segitiga Pascal terlalu besar. Pakai rumus faktorial.',
            'Pembagian dalam modulo diganti perkalian dengan invers: 1/x = x^(p−2) mod p untuk p prima.',
            'Siapkan fact[] dan invers faktorial inv[] sekali; jawaban = fact[n] · inv[k] · inv[n−k].',
        ],
    ],

    [
        'slug' => 'kurung-seimbang',
        'lesson' => 'dp-hitung',
        'title' => 'Barisan Kurung Seimbang',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'catalan', 'menghitung cara'],
        'statement' => '<p>Barisan kurung disebut <strong>seimbang</strong> jika setiap <code>(</code> punya pasangan <code>)</code> di sebelah kanannya dan pasangan-pasangannya tidak saling silang, seperti <code>(())()</code>. Barisan <code>())(</code> tidak seimbang.</p>
<p>Ada berapa barisan kurung seimbang dengan panjang tepat <strong>N</strong>? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Banyak barisan modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 5000</li></ul>',
        'samples' => [
            ['input' => "6\n", 'explanation' => '((())), (()()), (())(), ()(()), ()()(): ada 5.'],
            ['input' => "5\n", 'explanation' => 'Panjang ganjil tidak mungkin seimbang.'],
        ],
        'tests' => fn () => ["1\n", "2\n", "4\n", "10\n", "40\n", "1000\n", "4999\n", "5000\n"],
        'solve' => function (string $input) use ($MOD) {
            $n = (int) trim($input);
            if ($n % 2) {
                return '0';
            }
            $m = intdiv($n, 2);
            $cat = array_fill(0, $m + 1, 0);
            $cat[0] = 1;
            for ($i = 1; $i <= $m; $i++) {
                $s = 0;
                for ($j = 0; $j < $i; $j++) {
                    $s = ($s + $cat[$j] * $cat[$i - 1 - $j]) % $MOD;
                }
                $cat[$i] = $s;
            }

            return (string) $cat[$m];
        },
        'starter' => $st("    int n;\n    cin >> n;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n;
    cin >> n;
    if (n % 2) {
        cout << 0 << '\n';
        return 0;
    }
    int m = n / 2;                       // banyak pasangan kurung
    // cat[i] = banyak barisan seimbang dengan i pasang kurung
    vector<long long> cat(m + 1, 0);
    cat[0] = 1;
    for (int i = 1; i <= m; i++)
        for (int j = 0; j < i; j++)      // "(" pertama membungkus j pasang, sisanya i-1-j pasang di belakang
            cat[i] = (cat[i] + cat[j] * cat[i - 1 - j]) % MOD;
    cout << cat[m] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const MOD = 1000000007n;
if (n % 2) console.log(0);
else {
  const m = n / 2;
  // Hasil kali dua angka modulo bisa ~10^18: pakai BigInt agar tepat
  const cat = new Array(m + 1).fill(0n);
  cat[0] = 1n;
  for (let i = 1; i <= m; i++) {
    let s = 0n;
    for (let j = 0; j < i; j++) s += cat[j] * cat[i - 1 - j];
    cat[i] = s % MOD;
  }
  console.log(cat[m].toString());
}
CODE,
            'python' => <<<'CODE'
n = int(input())
MOD = 10**9 + 7
if n % 2:
    print(0)
else:
    m = n // 2
    cat = [0] * (m + 1)
    cat[0] = 1
    for i in range(1, m + 1):
        cat[i] = sum(cat[j] * cat[i - 1 - j] for j in range(i)) % MOD
    print(cat[m])
CODE,
        ],
        'editorial' => '<p>Panjang ganjil langsung 0. Untuk m = N/2 pasang kurung, pecah berdasarkan <strong>pasangan dari kurung buka pertama</strong>: <code>( A ) B</code>, dengan A barisan seimbang berisi j pasang dan B berisi m − 1 − j pasang. Setiap barisan punya tepat satu pecahan seperti itu, jadi:</p>
<p style="text-align:center"><code>cat[m] = Σ<sub>j=0</sub><sup>m−1</sup> cat[j] · cat[m−1−j]</code>, dengan <code>cat[0] = 1</code>.</p>
<p>Ini <strong>bilangan Catalan</strong>: 1, 1, 2, 5, 14, 42, … Perkalian dua nilai modulo bisa ~10<sup>18</sup>, jadi kalikan dalam <code>long long</code> lalu ambil modulo.</p>
<p><strong>Kompleksitas:</strong> O(N²). (Ada juga rumus tertutup <code>C(2m, m) / (m + 1)</code> dengan invers modular.)</p>',
        'hints' => [
            'Panjang ganjil tidak mungkin. Untuk m pasang, perhatikan pasangan dari kurung buka pertama.',
            'Barisan selalu berbentuk ( A ) B, dengan A dan B sendiri seimbang.',
            'cat[m] = jumlah cat[j] · cat[m−1−j] untuk j = 0..m−1, cat[0] = 1.',
        ],
    ],

    [
        'slug' => 'partisi-bilangan',
        'lesson' => 'dp-hitung',
        'title' => 'Memecah Bilangan',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'partisi', 'kombinasi vs permutasi'],
        'statement' => '<p>Bilangan 4 bisa ditulis sebagai jumlah bilangan bulat positif dengan 5 cara jika urutan <strong>tidak</strong> diperhatikan: 4, 3+1, 2+2, 2+1+1, 1+1+1+1.</p>
<p>Ada berapa cara menulis <strong>N</strong> seperti itu? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Banyak partisi N modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 5000</li></ul>',
        'samples' => [
            ['input' => "4\n", 'explanation' => null],
            ['input' => "10\n", 'explanation' => null],
        ],
        'tests' => fn () => ["1\n", "2\n", "7\n", "50\n", "100\n", "1000\n", "5000\n"],
        'solve' => function (string $input) use ($MOD) {
            $n = (int) trim($input);
            $dp = array_fill(0, $n + 1, 0);
            $dp[0] = 1;
            for ($k = 1; $k <= $n; $k++) {
                for ($s = $k; $s <= $n; $s++) {
                    $dp[$s] = ($dp[$s] + $dp[$s - $k]) % $MOD;
                }
            }

            return (string) $dp[$n];
        },
        'starter' => $st("    int n;\n    cin >> n;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n;
    cin >> n;
    // dp[s] = banyak cara menulis s memakai bagian-bagian yang sudah diproses
    vector<long long> dp(n + 1, 0);
    dp[0] = 1;
    // Bagian (1, 2, ..., n) di loop LUAR: setiap kumpulan dihitung sekali (urutan tak penting).
    // Loop s MAJU karena setiap bagian boleh dipakai berkali-kali (seperti unbounded knapsack).
    for (int k = 1; k <= n; k++)
        for (int s = k; s <= n; s++)
            dp[s] = (dp[s] + dp[s - k]) % MOD;
    cout << dp[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const MOD = 1000000007;
const dp = new Array(n + 1).fill(0);
dp[0] = 1;
for (let k = 1; k <= n; k++)
  for (let s = k; s <= n; s++) dp[s] = (dp[s] + dp[s - k]) % MOD;
console.log(dp[n]);
CODE,
            'python' => <<<'CODE'
n = int(input())
MOD = 10**9 + 7
dp = [0] * (n + 1)
dp[0] = 1
for k in range(1, n + 1):
    for s in range(k, n + 1):
        dp[s] = (dp[s] + dp[s - k]) % MOD
print(dp[n])
CODE,
        ],
        'editorial' => '<p>Ini soal <strong>kombinasi</strong> (urutan tidak penting), mirip menghitung cara membentuk nominal dengan koin 1, 2, …, N yang masing-masing tak terbatas.</p>
<p>Kuncinya urutan loop: <strong>jenis bagian k di loop luar</strong>. Dengan begitu, semua pemakaian bagian 1 diputuskan lebih dulu, lalu bagian 2, dan seterusnya; setiap kumpulan bagian hanya terhitung dalam satu urutan baku. Jika loop dibalik (s di luar), hasilnya menghitung <em>permutasi</em> (3+1 dan 1+3 terhitung berbeda).</p>
<p style="text-align:center"><code>dp[s] += dp[s − k]</code>, untuk k = 1..N dan s = k..N.</p>
<p><strong>Kompleksitas:</strong> O(N²).</p>',
        'hints' => [
            'Anggap bagian-bagian sebagai koin bernilai 1..N yang masing-masing boleh dipakai berkali-kali.',
            'Urutan tidak penting → ini menghitung kombinasi. Loop mana yang harus di luar?',
            'Loop jenis bagian k di luar, nominal s di dalam (maju): dp[s] += dp[s − k].',
        ],
    ],

    // ═════════════════════════════ DP grid ═════════════════════════════
    [
        'slug' => 'segitiga-angka',
        'lesson' => 'dp-grid',
        'title' => 'Segitiga Angka',
        'difficulty' => 'Mudah',
        'tags' => ['dp', 'grid', 'jalur maksimum'],
        'statement' => '<p>Sebuah segitiga angka punya <strong>N</strong> baris; baris ke-i berisi i angka. Mulai dari puncak, kamu turun satu baris setiap langkah, ke angka tepat di bawah atau di kanan bawahnya.</p>
<p>Berapa jumlah terbesar dari angka-angka yang dilewati sampai baris terakhir?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya: baris ke-i berisi i bilangan.</p>',
        'output_format' => '<p>Jumlah maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1000</li><li>0 ≤ angka ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4\n7\n3 8\n8 1 0\n2 7 4 4\n", 'explanation' => '7 → 3 → 8 → 7 = 25.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $out = [(string) $n];
                for ($i = 1; $i <= $n; $i++) {
                    $row = [];
                    for ($j = 0; $j < $i; $j++) {
                        $row[] = mt_rand(0, $max);
                    }
                    $out[] = implode(' ', $row);
                }

                return implode("\n", $out)."\n";
            };

            return ["1\n5\n", $mk(5, 9), $mk(50, 100), $mk(500, 1000000), $mk(1000, 1000000), $mk(1000, 1)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $dp = T::ints($lines[$n]);
            for ($i = $n - 1; $i >= 1; $i--) {
                $row = T::ints($lines[$i]);
                $nd = [];
                foreach ($row as $j => $x) {
                    $nd[$j] = $x + max($dp[$j], $dp[$j + 1]);
                }
                $dp = $nd;
            }

            return (string) $dp[0];
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<vector<long long>> a(n);\n    for (int i = 0; i < n; i++) {\n        a[i].resize(i + 1);\n        for (auto& x : a[i]) cin >> x;\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<long long>> a(n);
    for (int i = 0; i < n; i++) {
        a[i].resize(i + 1);
        for (auto& x : a[i]) cin >> x;
    }
    // Dari bawah ke atas: dp[j] = jumlah terbesar dari posisi (i, j) turun ke dasar
    vector<long long> dp = a[n - 1];
    for (int i = n - 2; i >= 0; i--)
        for (int j = 0; j <= i; j++)
            dp[j] = a[i][j] + max(dp[j], dp[j + 1]);   // ke bawah atau kanan bawah
    cout << dp[0] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = [];
for (let i = 0; i < n; i++) a.push(readInts());
let dp = a[n - 1].slice();
for (let i = n - 2; i >= 0; i--)
  for (let j = 0; j <= i; j++) dp[j] = a[i][j] + Math.max(dp[j], dp[j + 1]);
console.log(dp[0]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = [list(map(int, input().split())) for _ in range(n)]
dp = a[-1][:]
for i in range(n - 2, -1, -1):
    row = a[i]
    dp = [row[j] + max(dp[j], dp[j + 1]) for j in range(i + 1)]
print(dp[0])
CODE,
        ],
        'editorial' => '<p>Mencoba semua jalur butuh 2<sup>N−1</sup> kemungkinan. Dengan DP, isi dari <strong>bawah ke atas</strong>: <code>dp[i][j]</code> = jumlah terbesar dari posisi (i, j) sampai dasar.</p>
<p style="text-align:center"><code>dp[i][j] = a[i][j] + max(dp[i+1][j], dp[i+1][j+1])</code></p>
<p>Jawabannya <code>dp[0][0]</code>. Mengisi dari bawah menghindari kasus tepi, dan karena baris i hanya butuh baris i+1, satu array cukup.</p>
<p><strong>Kompleksitas:</strong> O(N²).</p>',
        'hints' => [
            'Serakah (pilih angka terbesar di bawah) bisa salah. Coba isi tabel dp.',
            'Isi dari baris terbawah: dp[i][j] = a[i][j] + max(dua angka di bawahnya).',
            'Jawabannya dp di puncak. Satu array sepanjang N cukup.',
        ],
    ],

    [
        'slug' => 'persegi-terbesar',
        'lesson' => 'dp-grid',
        'title' => 'Lahan Persegi Terbesar',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'grid', 'persegi'],
        'statement' => '<p>Peta tanah berbentuk grid R × C. Petak <code>1</code> adalah tanah kosong, petak <code>0</code> sudah ada bangunan. Seorang developer ingin membangun lapangan berbentuk <strong>persegi</strong> yang seluruhnya berada di tanah kosong.</p>
<p>Berapa panjang sisi persegi terbesar yang mungkin? (0 jika tidak ada tanah kosong.)</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>. R baris berikutnya berisi string C karakter <code>0</code>/<code>1</code>.</p>',
        'output_format' => '<p>Panjang sisi maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 1000</li></ul>',
        'samples' => [
            ['input' => "4 5\n10100\n10111\n11111\n10010\n", 'explanation' => 'Persegi 2 × 2 di baris 2–3, kolom 3–4.'],
        ],
        'tests' => function () {
            $mk = function (int $r, int $c, float $p1) {
                $out = ["$r $c"];
                for ($i = 0; $i < $r; $i++) {
                    $row = '';
                    for ($j = 0; $j < $c; $j++) {
                        $row .= mt_rand() / mt_getrandmax() < $p1 ? '1' : '0';
                    }
                    $out[] = $row;
                }

                return implode("\n", $out)."\n";
            };

            return ["1 1\n0\n", "1 1\n1\n", $mk(5, 5, 0.7), $mk(50, 80, 0.85), $mk(1000, 1000, 0.97), $mk(1000, 1000, 1.0), $mk(1000, 1000, 0.5), $mk(1, 1000, 0.9)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C] = T::ints($lines[0]);
            $prev = array_fill(0, $C + 1, 0);
            $best = 0;
            for ($i = 1; $i <= $R; $i++) {
                $row = $lines[$i];
                $cur = array_fill(0, $C + 1, 0);
                for ($j = 1; $j <= $C; $j++) {
                    if ($row[$j - 1] === '1') {
                        $cur[$j] = 1 + min($prev[$j], $cur[$j - 1], $prev[$j - 1]);
                        if ($cur[$j] > $best) {
                            $best = $cur[$j];
                        }
                    }
                }
                $prev = $cur;
            }

            return (string) $best;
        },
        'starter' => $st("    int R, C;\n    cin >> R >> C;\n    vector<string> g(R);\n    for (auto& baris : g) cin >> baris;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<string> g(R);
    for (auto& baris : g) cin >> baris;

    // dp[i][j] = sisi persegi terbesar yang POJOK KANAN BAWAHNYA di (i, j)
    vector<vector<int>> dp(R + 1, vector<int>(C + 1, 0));
    int terbaik = 0;
    for (int i = 1; i <= R; i++)
        for (int j = 1; j <= C; j++)
            if (g[i - 1][j - 1] == '1') {
                dp[i][j] = 1 + min({dp[i - 1][j], dp[i][j - 1], dp[i - 1][j - 1]});
                terbaik = max(terbaik, dp[i][j]);
            }
    cout << terbaik << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C] = readInts();
let prev = new Int32Array(C + 1), best = 0;
for (let i = 1; i <= R; i++) {
  const row = readLine().trim();
  const cur = new Int32Array(C + 1);
  for (let j = 1; j <= C; j++) {
    if (row[j - 1] === "1") {
      cur[j] = 1 + Math.min(prev[j], cur[j - 1], prev[j - 1]);
      if (cur[j] > best) best = cur[j];
    }
  }
  prev = cur;
}
console.log(best);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
prev = [0] * (C + 1)
best = 0
for _ in range(R):
    row = input().strip()
    cur = [0] * (C + 1)
    for j in range(1, C + 1):
        if row[j - 1] == "1":
            a, b, c = prev[j], cur[j - 1], prev[j - 1]
            m = a if a < b else b
            if c < m:
                m = c
            cur[j] = m + 1
    best = max(best, max(cur))
    prev = cur
print(best)
CODE,
        ],
        'editorial' => '<p>State: <code>dp[i][j]</code> = sisi persegi terbesar yang <strong>pojok kanan bawahnya</strong> di (i, j). Jika petaknya 0, nilainya 0. Jika 1:</p>
<p style="text-align:center"><code>dp[i][j] = 1 + min(dp[i−1][j], dp[i][j−1], dp[i−1][j−1])</code></p>
<p>Mengapa minimum? Persegi berukuran s di (i, j) membutuhkan persegi berukuran s − 1 di atasnya, di kirinya, dan di kiri-atasnya; persegi terkecil dari ketiganya yang membatasi.</p>
<p>Jawabannya maksimum seluruh tabel. <strong>Kompleksitas:</strong> O(R · C).</p>',
        'hints' => [
            'Mencoba semua persegi terlalu lambat. Tentukan state berdasarkan pojok kanan bawah.',
            'Persegi sisi s yang berakhir di (i, j) membutuhkan persegi sisi s−1 berakhir di atas, kiri, dan kiri-atas.',
            'dp[i][j] = 1 + min(atas, kiri, kiri-atas) jika petaknya 1. Jawaban = maksimum dp.',
        ],
    ],

    // ═════════════════════════════ Knapsack ═════════════════════════════
    [
        'slug' => 'stok-tak-terbatas',
        'lesson' => 'knapsack',
        'title' => 'Belanja Grosir',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'unbounded knapsack'],
        'statement' => '<p>Toko grosir menjual <strong>N</strong> jenis barang dengan stok <strong>tak terbatas</strong>. Barang jenis i seberat <code>w<sub>i</sub></code> kg dan bernilai <code>v<sub>i</sub></code>. Mobil bak terbuka bisa mengangkut paling banyak <strong>W</strong> kg.</p>
<p>Berapa total nilai terbesar yang bisa diangkut? Setiap jenis boleh dibeli berapa pun banyaknya (termasuk tidak sama sekali).</p>',
        'input_format' => '<p>Baris pertama berisi <code>N W</code>. N baris berikutnya berisi <code>w v</code>.</p>',
        'output_format' => '<p>Nilai maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100</li><li>1 ≤ W ≤ 100 000</li><li>1 ≤ w ≤ W, 1 ≤ v ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3 10\n5 10\n4 7\n2 3\n", 'explanation' => 'Dua barang jenis 1: berat 10, nilai 20. Bandingkan 4+4+2 → 7+7+3 = 17.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $W, int $maxV) {
                $out = ["$n $W"];
                for ($i = 0; $i < $n; $i++) {
                    $out[] = mt_rand(1, max(1, intdiv($W, 3))).' '.mt_rand(1, $maxV);
                }

                return implode("\n", $out)."\n";
            };

            return ["1 1\n1 5\n", "1 10\n3 4\n", $mk(5, 20, 20), $mk(30, 1000, 1000), $mk(100, 100000, 1000000000), $mk(100, 50000, 10)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $W] = T::ints($lines[0]);
            $dp = array_fill(0, $W + 1, 0);
            for ($i = 1; $i <= $n; $i++) {
                [$w, $v] = T::ints($lines[$i]);
                for ($c = $w; $c <= $W; $c++) {
                    if ($dp[$c - $w] + $v > $dp[$c]) {
                        $dp[$c] = $dp[$c - $w] + $v;
                    }
                }
            }

            return (string) $dp[$W];
        },
        'starter' => $st("    int n, W;\n    cin >> n >> W;\n    vector<int> w(n);\n    vector<long long> v(n);\n    for (int i = 0; i < n; i++) cin >> w[i] >> v[i];"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, W;
    cin >> n >> W;
    vector<int> w(n);
    vector<long long> v(n);
    for (int i = 0; i < n; i++) cin >> w[i] >> v[i];

    // dp[c] = nilai terbaik dengan kapasitas c
    vector<long long> dp(W + 1, 0);
    for (int i = 0; i < n; i++)
        for (int c = w[i]; c <= W; c++)          // MAJU: barang yang sama boleh dipakai lagi
            dp[c] = max(dp[c], dp[c - w[i]] + v[i]);
    cout << dp[W] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, W] = readInts();
// Nilai bisa mencapai 10^14: masih aman untuk Float64Array
const dp = new Float64Array(W + 1);
for (let i = 0; i < n; i++) {
  const [w, v] = readInts();
  for (let c = w; c <= W; c++) if (dp[c - w] + v > dp[c]) dp[c] = dp[c - w] + v;
}
console.log(dp[W]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, W = map(int, input().split())
dp = [0] * (W + 1)
for _ in range(n):
    w, v = map(int, input().split())
    for c in range(w, W + 1):
        t = dp[c - w] + v
        if t > dp[c]:
            dp[c] = t
print(dp[W])
CODE,
        ],
        'editorial' => '<p>Ini <strong>unbounded knapsack</strong>. Bedanya dengan knapsack 0/1 hanya arah loop kapasitas: <strong>maju</strong> (c dari w ke W). Saat <code>dp[c]</code> dihitung, <code>dp[c − w]</code> mungkin sudah memakai barang yang sama, dan memang itu yang diizinkan.</p>
<p style="text-align:center"><code>dp[c] = max(dp[c], dp[c − w] + v)</code></p>
<p>Nilai total bisa mencapai 10<sup>14</sup>: gunakan <code>long long</code>. <strong>Kompleksitas:</strong> O(N · W) = 10<sup>7</sup>.</p>',
        'hints' => [
            'Ini knapsack, tetapi setiap barang boleh diambil berkali-kali.',
            'Pada knapsack 0/1 loop kapasitas mundur. Apa yang terjadi jika maju?',
            'Loop maju: dp[c] = max(dp[c], dp[c − w] + v). Gunakan long long.',
        ],
    ],

    [
        'slug' => 'hitung-subset',
        'lesson' => 'knapsack',
        'title' => 'Paket Hadiah Tepat',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'subset sum', 'menghitung cara'],
        'statement' => '<p>Ada <strong>N</strong> hadiah dengan harga <code>a<sub>1</sub>, …, a<sub>N</sub></code> (setiap hadiah hanya satu). Panitia ingin membuat paket berisi sebagian hadiah dengan total harga <strong>tepat S</strong>.</p>
<p>Ada berapa cara memilih hadiahnya? Dua cara berbeda jika ada hadiah yang dipilih di satu cara tetapi tidak di cara lain (hadiah dengan harga sama tetap dianggap berbeda). Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N S</code>. Baris kedua berisi a<sub>1</sub> … a<sub>N</sub>.</p>',
        'output_format' => '<p>Banyak cara modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200</li><li>0 ≤ S ≤ 10 000</li><li>1 ≤ a<sub>i</sub> ≤ 10 000</li></ul>',
        'samples' => [
            ['input' => "5 5\n1 2 3 2 5\n", 'explanation' => '{5}, {2, 3}, {3, 2′}, {1, 2, 2′}: ada 4 cara (2 dan 2′ adalah dua hadiah berbeda berharga 2).'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $S, int $max) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, $max);
                }

                return "$n $S\n".implode(' ', $a)."\n";
            };

            return ["1 0\n5\n", "1 5\n5\n", $mk(6, 10, 5), $mk(30, 100, 20), $mk(200, 10000, 100), $mk(200, 5000, 10000), $mk(200, 10000, 3)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $S] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $MOD = 1000000007;
            $dp = array_fill(0, $S + 1, 0);
            $dp[0] = 1;
            foreach ($a as $x) {
                for ($s = $S; $s >= $x; $s--) {
                    $dp[$s] += $dp[$s - $x];
                    if ($dp[$s] >= $MOD) {
                        $dp[$s] -= $MOD;
                    }
                }
            }

            return (string) $dp[$S];
        },
        'starter' => $st("    int n, S;\n    cin >> n >> S;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n, S;
    cin >> n >> S;
    vector<int> a(n);
    for (auto& x : a) cin >> x;

    // dp[s] = banyak cara memilih hadiah (dari yang sudah diproses) dengan total s
    vector<long long> dp(S + 1, 0);
    dp[0] = 1;                                  // paket kosong
    for (int x : a)
        for (int s = S; s >= x; s--)            // MUNDUR: setiap hadiah hanya sekali
            dp[s] = (dp[s] + dp[s - x]) % MOD;  // tidak diambil (tetap) + diambil
    cout << dp[S] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, S] = readInts();
const a = readInts();
const MOD = 1000000007;
const dp = new Array(S + 1).fill(0);
dp[0] = 1;
for (const x of a)
  for (let s = S; s >= x; s--) {
    dp[s] += dp[s - x];
    if (dp[s] >= MOD) dp[s] -= MOD;
  }
console.log(dp[S]);
CODE,
            'python' => <<<'CODE'
n, S = map(int, input().split())
a = list(map(int, input().split()))
MOD = 10**9 + 7
dp = [0] * (S + 1)
dp[0] = 1
for x in a:
    # geser seluruh array sekaligus: dp[s] += dp[s - x] untuk s >= x (memakai nilai lama)
    lama = dp[:]
    for s in range(x, S + 1):
        dp[s] = (lama[s] + lama[s - x]) % MOD
print(dp[S])
CODE,
        ],
        'editorial' => '<p>Ini knapsack 0/1 versi <strong>menghitung cara</strong>: max diganti penjumlahan. Untuk setiap hadiah, semua cara terbagi dua kelompok yang tidak tumpang tindih: hadiah itu tidak diambil (<code>dp[s]</code> lama) atau diambil (<code>dp[s − x]</code> lama).</p>
<p style="text-align:center"><code>dp[s] = dp[s] + dp[s − x]</code>, loop s <strong>mundur</strong>, dengan <code>dp[0] = 1</code>.</p>
<p>Loop mundur menjamin <code>dp[s − x]</code> yang dibaca belum memakai hadiah yang sama. Base case <code>dp[0] = 1</code> mewakili paket kosong, sehingga jika S = 0 jawabannya 1.</p>
<p><strong>Kompleksitas:</strong> O(N · S).</p>',
        'hints' => [
            'Ini mirip knapsack 0/1, tetapi yang dihitung banyak cara, bukan nilai maksimum.',
            'Ganti max dengan penjumlahan: dp[s] += dp[s − x], dengan dp[0] = 1.',
            'Loop s mundur agar setiap hadiah dipakai paling banyak sekali.',
        ],
    ],
];
