<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal tambahan track Dynamic Programming: LIS, DP Interval, DP Bitmask.
 * Semua kode C++ harus lolos C++14 (GCC 6.3): tanpa structured binding.
 */

/** Panjang subsequence naik tegas terpanjang, O(n log n). */
$lisLength = function (array $a): int {
    $tails = [];
    foreach ($a as $x) {
        $lo = 0;
        $hi = count($tails);
        while ($lo < $hi) {
            $mid = ($lo + $hi) >> 1;
            if ($tails[$mid] < $x) {
                $lo = $mid + 1;
            } else {
                $hi = $mid;
            }
        }
        $tails[$lo] = $x;
    }

    return count($tails);
};

$arrayInput = function (int $n, int $max, string $mode = 'acak'): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = match ($mode) {
            'naik' => $i + 1,
            'turun' => $n - $i,
            'sama' => 7,
            default => mt_rand(1, $max),
        };
    }

    return "$n\n".implode(' ', $a)."\n";
};

return [
    // ═════════════════════════════ LIS ═════════════════════════════
    [
        'slug' => 'foto-bertingkat',
        'lesson' => 'lis',
        'title' => 'Foto Bertingkat',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'lis', 'binary search'],
        'statement' => '<p>Panitia wisuda menyusun <strong>N</strong> siswa dalam satu barisan. Tinggi siswa ke-i adalah <code>a<sub>i</sub></code>. Untuk foto khusus, fotografer ingin memilih sebanyak mungkin siswa <strong>tanpa mengubah urutan berdiri</strong>, sehingga tinggi siswa yang terpilih <strong>naik tegas</strong> dari kiri ke kanan.</p>
<p>Berapa banyak siswa paling banyak yang bisa dipilih?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>N</code> bilangan <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Panjang subsequence naik tegas terpanjang.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "8\n3 10 2 1 20 4 6 8\n", 'explanation' => 'Contohnya 3, 4, 6, 8 (atau 1, 4, 6, 8). Tidak ada yang lebih panjang dari 4.'],
            ['input' => "5\n5 5 5 5 5\n", 'explanation' => 'Tinggi harus naik tegas, jadi dua siswa setinggi sama tidak boleh dipilih bersama.'],
        ],
        'tests' => function () use ($arrayInput) {
            return [
                "1\n42\n", $arrayInput(10, 20), $arrayInput(100, 50), $arrayInput(1000, 1000000000), $arrayInput(5000, 100),
                $arrayInput(200000, 1000000000), $arrayInput(200000, 1000), $arrayInput(200000, 0, 'naik'), $arrayInput(200000, 0, 'turun'), $arrayInput(200000, 0, 'sama'),
            ];
        },
        'solve' => function (string $input) use ($lisLength) {
            $lines = T::lines($input);

            return (string) $lisLength(T::ints($lines[1]));
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];

    // Tulis solusimu di sini

    return 0;
}
CODE,
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
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];

    // tails[k] = ujung terkecil dari subsequence naik sepanjang k + 1
    vector<int> tails;
    for (int x : a) {
        auto it = lower_bound(tails.begin(), tails.end(), x);  // naik TEGAS
        if (it == tails.end()) tails.push_back(x);
        else *it = x;
    }
    cout << tails.size() << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();

const tails = [];
for (const x of a) {
  let lo = 0, hi = tails.length;
  while (lo < hi) {
    const mid = (lo + hi) >> 1;
    if (tails[mid] < x) lo = mid + 1;
    else hi = mid;
  }
  tails[lo] = x;
}
console.log(tails.length);
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_left
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))

tails = []
for x in a:
    i = bisect_left(tails, x)   # naik tegas
    if i == len(tails):
        tails.append(x)
    else:
        tails[i] = x
print(len(tails))
CODE,
        ],
        'editorial' => '<p>Ini adalah LIS (naik tegas). Untuk N = 200 000, versi O(N²) terlalu lambat (4 · 10<sup>10</sup> operasi), jadi pakai versi <strong>O(N log N)</strong>.</p>
<p>Simpan <code>tails[k]</code> = ujung terkecil dari semua subsequence naik sepanjang k + 1. Untuk setiap x, cari posisi elemen pertama yang <strong>≥ x</strong> (<code>lower_bound</code>). Jika tidak ada, x memperpanjang subsequence terpanjang; jika ada, x menggantikannya karena ujung yang lebih kecil lebih menguntungkan.</p>
<p>Karena naik <strong>tegas</strong>, gunakan <code>lower_bound</code>. Dengan <code>upper_bound</code>, tinggi yang sama ikut dihitung (contoh kedua akan memberi 5, bukan 1).</p>
<p><strong>Kompleksitas:</strong> O(N log N).</p>',
        'hints' => [
            'Ini soal Longest Increasing Subsequence. Tetapi N sampai 200 000, jadi dua loop bersarang terlalu lambat.',
            'Simpan tails[k] = ujung terkecil dari subsequence naik sepanjang k + 1. Array ini selalu urut naik.',
            'Untuk setiap x, cari dengan binary search elemen pertama ≥ x di tails. Ganti elemen itu dengan x, atau tambahkan x di akhir.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'kotak-bersarang',
        'lesson' => 'lis',
        'title' => 'Kotak Bersarang',
        'difficulty' => 'Sulit',
        'tags' => ['dp', 'lis', 'sorting'],
        'statement' => '<p>Sebuah toko kado punya <strong>N</strong> kotak berbentuk persegi panjang. Kotak ke-i berukuran lebar <code>w<sub>i</sub></code> dan tinggi <code>h<sub>i</sub></code>. Kotak A bisa dimasukkan ke dalam kotak B jika <strong>lebar A &lt; lebar B</strong> dan <strong>tinggi A &lt; tinggi B</strong> (keduanya tegas). Kotak tidak boleh diputar.</p>
<p>Berapa banyak kotak paling banyak yang bisa disusun bersarang, satu di dalam yang lain?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya berisi <code>w h</code>.</p>',
        'output_format' => '<p>Banyak kotak terbanyak dalam satu susunan bersarang.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>1 ≤ w, h ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n5 4\n6 4\n6 7\n2 3\n3 5\n", 'explanation' => 'Susunan (2, 3) ⊂ (5, 4) ⊂ (6, 7) berisi 3 kotak. Kotak (6, 4) tidak bisa masuk ke (6, 7) karena lebarnya sama.'],
            ['input' => "3\n4 4\n4 5\n4 6\n", 'explanation' => 'Semua lebarnya 4, jadi tidak ada yang bisa saling masuk.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $out = [(string) $n];
                for ($i = 0; $i < $n; $i++) {
                    $out[] = mt_rand(1, $max).' '.mt_rand(1, $max);
                }

                return implode("\n", $out)."\n";
            };
            $sameW = "6\n3 1\n3 2\n3 3\n3 4\n3 5\n3 6\n";
            $chain = "4\n1 1\n2 2\n3 3\n4 4\n";

            return ["1\n7 7\n", $sameW, $chain, $mk(10, 6), $mk(100, 30), $mk(2000, 1000), $mk(100000, 1000000000), $mk(100000, 300), $mk(100000, 50)];
        },
        'solve' => function (string $input) use ($lisLength) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $box = [];
            for ($i = 1; $i <= $n; $i++) {
                $box[] = T::ints($lines[$i]);
            }
            usort($box, fn ($p, $q) => $p[0] !== $q[0] ? $p[0] <=> $q[0] : $q[1] <=> $p[1]);

            return (string) $lisLength(array_column($box, 1));
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<pair<int, int>> box(n);  // {lebar, tinggi}
    for (int i = 0; i < n; i++) cin >> box[i].first >> box[i].second;

    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const box = [];
for (let i = 0; i < n; i++) box.push(readInts()); // [lebar, tinggi]

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
box = [tuple(map(int, input().split())) for _ in range(n)]  # (lebar, tinggi)

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<pair<int, int>> box(n);
    for (int i = 0; i < n; i++) cin >> box[i].first >> box[i].second;

    // Lebar naik; untuk lebar sama, tinggi TURUN
    // agar dua kotak berlebar sama tidak bisa terpilih bersama.
    sort(box.begin(), box.end(), [](const pair<int, int>& p, const pair<int, int>& q) {
        if (p.first != q.first) return p.first < q.first;
        return p.second > q.second;
    });

    // LIS naik tegas pada tinggi
    vector<int> tails;
    for (int i = 0; i < n; i++) {
        int h = box[i].second;
        auto it = lower_bound(tails.begin(), tails.end(), h);
        if (it == tails.end()) tails.push_back(h);
        else *it = h;
    }
    cout << tails.size() << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const box = [];
for (let i = 0; i < n; i++) box.push(readInts());
box.sort((p, q) => (p[0] !== q[0] ? p[0] - q[0] : q[1] - p[1]));

const tails = [];
for (const [, h] of box) {
  let lo = 0, hi = tails.length;
  while (lo < hi) {
    const mid = (lo + hi) >> 1;
    if (tails[mid] < h) lo = mid + 1;
    else hi = mid;
  }
  tails[lo] = h;
}
console.log(tails.length);
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_left
input = sys.stdin.readline

n = int(input())
box = [tuple(map(int, input().split())) for _ in range(n)]
box.sort(key=lambda b: (b[0], -b[1]))

tails = []
for _, h in box:
    i = bisect_left(tails, h)
    if i == len(tails):
        tails.append(h)
    else:
        tails[i] = h
print(len(tails))
CODE,
        ],
        'editorial' => '<p>Jika kotak diurutkan berdasarkan lebar, setiap susunan bersarang menjadi subsequence dengan tinggi yang <strong>naik tegas</strong>. Jadi jawabannya adalah LIS dari tinggi.</p>
<p>Masalahnya ada pada lebar yang <strong>sama</strong>: kotak (3, 1) dan (3, 2) tidak boleh saling masuk, tetapi tingginya naik. Triknya, untuk lebar yang sama urutkan tinggi <strong>turun</strong>. Dengan begitu, di antara kotak berlebar sama tingginya tidak pernah naik, sehingga LIS tidak mungkin memilih dua di antaranya.</p>
<p>LIS dihitung dengan versi O(N log N).</p>
<p><strong>Kompleksitas:</strong> O(N log N).</p>',
        'hints' => [
            'Coba urutkan kotak berdasarkan lebar. Apa yang harus terjadi pada tinggi di sepanjang susunan?',
            'Setelah diurutkan, susunan bersarang = subsequence dengan tinggi naik tegas. Itu LIS.',
            'Hati-hati dengan lebar yang sama: urutkan tingginya secara menurun agar dua kotak berlebar sama tidak terpilih bersama.',
        ],
    ],

    // ═════════════════════════════ DP Interval ═════════════════════════════
    [
        'slug' => 'gabung-batu',
        'lesson' => 'dp-interval',
        'title' => 'Menggabungkan Tumpukan Batu',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'dp interval'],
        'statement' => '<p>Di halaman sekolah ada <strong>N</strong> tumpukan batu berjajar. Tumpukan ke-i berisi <code>a<sub>i</sub></code> batu. Petugas ingin menyatukan semuanya menjadi satu tumpukan.</p>
<p>Dalam satu langkah, petugas memilih <strong>dua tumpukan yang bersebelahan</strong> dan menggabungkannya. Biaya langkah itu sama dengan banyak batu pada tumpukan hasil gabungan. Tumpukan baru menempati posisi keduanya, jadi urutan tumpukan lain tidak berubah.</p>
<p>Berapa <strong>total biaya minimum</strong>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Total biaya minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4\n4 1 1 4\n", 'explanation' => 'Gabung 1 + 1 (biaya 2), lalu 2 + 4 di kanan (biaya 6), lalu 4 + 6 (biaya 10). Total 18.'],
            ['input' => "1\n100\n", 'explanation' => 'Sudah satu tumpukan, tidak perlu biaya.'],
        ],
        'tests' => function () use ($arrayInput) {
            return ["2\n3 5\n", "3\n1 2 3\n", $arrayInput(6, 10), $arrayInput(20, 100), $arrayInput(80, 1000000), $arrayInput(200, 1000000), $arrayInput(200, 3), $arrayInput(200, 0, 'naik'), $arrayInput(199, 1000)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $a = T::ints($lines[1]);
            $pre = [0];
            foreach ($a as $x) {
                $pre[] = end($pre) + $x;
            }
            $dp = array_fill(0, $n, array_fill(0, $n, 0));
            for ($len = 2; $len <= $n; $len++) {
                for ($l = 0; $l + $len - 1 < $n; $l++) {
                    $r = $l + $len - 1;
                    $best = PHP_INT_MAX;
                    $dl = $dp[$l];
                    for ($k = $l; $k < $r; $k++) {
                        $c = $dl[$k] + $dp[$k + 1][$r];
                        if ($c < $best) {
                            $best = $c;
                        }
                    }
                    $dp[$l][$r] = $best + $pre[$r + 1] - $pre[$l];
                }
            }

            return (string) $dp[0][$n - 1];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];

    // Tulis solusimu di sini

    return 0;
}
CODE,
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
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n), pre(n + 1, 0);
    for (int i = 0; i < n; i++) {
        cin >> a[i];
        pre[i + 1] = pre[i] + a[i];
    }

    // dp[l][r] = biaya minimum menyatukan tumpukan l..r
    vector<vector<long long>> dp(n, vector<long long>(n, 0));
    for (int len = 2; len <= n; len++) {
        for (int l = 0; l + len - 1 < n; l++) {
            int r = l + len - 1;
            long long best = LLONG_MAX;
            for (int k = l; k < r; k++) best = min(best, dp[l][k] + dp[k + 1][r]);
            dp[l][r] = best + pre[r + 1] - pre[l];   // + gabungan terakhir
        }
    }
    cout << dp[0][n - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
const pre = [0];
for (const x of a) pre.push(pre[pre.length - 1] + x);

const dp = Array.from({ length: n }, () => new Array(n).fill(0));
for (let len = 2; len <= n; len++) {
  for (let l = 0; l + len - 1 < n; l++) {
    const r = l + len - 1;
    let best = Infinity;
    for (let k = l; k < r; k++) best = Math.min(best, dp[l][k] + dp[k + 1][r]);
    dp[l][r] = best + pre[r + 1] - pre[l];
  }
}
console.log(dp[0][n - 1]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
pre = [0]
for x in a:
    pre.append(pre[-1] + x)

dp = [[0] * n for _ in range(n)]
for length in range(2, n + 1):
    for l in range(n - length + 1):
        r = l + length - 1
        dl = dp[l]
        best = min(dl[k] + dp[k + 1][r] for k in range(l, r))
        dp[l][r] = best + pre[r + 1] - pre[l]
print(dp[0][n - 1])
CODE,
        ],
        'editorial' => '<p>Lihat <strong>gabungan terakhir</strong>. Sebelum langkah terakhir, ada dua tumpukan: yang kiri berasal dari tumpukan l..k dan yang kanan dari k+1..r. Biaya langkah terakhir selalu jumlah semua batu l..r, berapa pun k-nya.</p>
<p style="text-align:center"><code>dp[l][r] = min<sub>k</sub>(dp[l][k] + dp[k+1][r]) + jumlah(l..r)</code></p>
<p>Base case <code>dp[i][i] = 0</code>. Isi berdasarkan <strong>panjang interval</strong> agar interval yang lebih pendek selalu siap. Jumlah l..r dihitung dengan prefix sum.</p>
<p>Total biaya bisa mencapai sekitar 200 · 200 · 10<sup>6</sup>, jadi pakai <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(N³).</p>',
        'hints' => [
            'Menggabungkan pasangan termurah lebih dulu (serakah) tidak selalu optimal.',
            'Pikirkan gabungan TERAKHIR: tumpukan kiri berasal dari l..k, tumpukan kanan dari k+1..r.',
            'dp[l][r] = min over k (dp[l][k] + dp[k+1][r]) + jumlah(l..r). Isi berdasarkan panjang interval, dan pakai prefix sum.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'ambil-ujung',
        'lesson' => 'dp-interval',
        'title' => 'Permainan Ambil Ujung',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'dp interval', 'game'],
        'statement' => '<p>Andi dan Budi memainkan permainan kartu. Ada <strong>N</strong> kartu berjajar, kartu ke-i bernilai <code>a<sub>i</sub></code> (bisa negatif). Mereka bergantian mengambil <strong>satu kartu dari ujung kiri atau ujung kanan</strong> barisan, dimulai dari Andi. Skor setiap pemain adalah jumlah nilai kartu yang ia ambil.</p>
<p>Keduanya bermain <strong>optimal</strong>: masing-masing berusaha memaksimalkan (skor sendiri − skor lawan). Cetak <strong>skor Andi − skor Budi</strong> di akhir permainan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Selisih skor Andi − skor Budi.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 2000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4\n10 80 90 30\n", 'explanation' => 'Andi mengambil 30, Budi 90, Andi 80, Budi 10. Skor Andi 110 dan Budi 100, selisih 10. Jika Andi serakah mengambil 10 lebih dulu, Budi bisa membuat selisihnya lebih buruk bagi Andi.'],
            ['input' => "3\n10 100 10\n", 'explanation' => 'Apa pun pilihan Andi, Budi mengambil 100. Andi 20, Budi 100, selisih −80.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $lo, int $hi) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand($lo, $hi);
                }

                return "$n\n".implode(' ', $a)."\n";
            };

            return ["1\n-5\n", "2\n3 9\n", $mk(5, -10, 10), $mk(50, 1, 100), $mk(500, -1000000000, 1000000000), $mk(2000, -1000000000, 1000000000), $mk(2000, 1, 1000000000), $mk(1999, -5, 5)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $a = T::ints($lines[1]);
            $dp = $a; // panjang 1: dp[l] = a[l], dp[l] mewakili interval [l, l+len-1]
            for ($len = 2; $len <= $n; $len++) {
                $next = [];
                for ($l = 0; $l + $len - 1 < $n; $l++) {
                    $r = $l + $len - 1;
                    $next[$l] = max($a[$l] - $dp[$l + 1], $a[$r] - $dp[$l]);
                }
                $dp = $next;
            }

            return (string) $dp[0];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];

    // Tulis solusimu di sini

    return 0;
}
CODE,
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
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];

    // dp[l][r] = (skor pemain yang sedang jalan) - (skor lawan) pada sisa kartu l..r
    vector<vector<long long>> dp(n, vector<long long>(n, 0));
    for (int i = 0; i < n; i++) dp[i][i] = a[i];
    for (int len = 2; len <= n; len++) {
        for (int l = 0; l + len - 1 < n; l++) {
            int r = l + len - 1;
            // setelah kita mengambil, giliran lawan pada sisa kartu: selisihnya dikurangkan
            dp[l][r] = max(a[l] - dp[l + 1][r], a[r] - dp[l][r - 1]);
        }
    }
    cout << dp[0][n - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts().map(BigInt);

// dp[l] mewakili interval [l, l + len - 1]; cukup satu baris per panjang
let dp = a.slice();
for (let len = 2; len <= n; len++) {
  const next = [];
  for (let l = 0; l + len - 1 < n; l++) {
    const r = l + len - 1;
    const kiri = a[l] - dp[l + 1];
    const kanan = a[r] - dp[l];
    next.push(kiri > kanan ? kiri : kanan);
  }
  dp = next;
}
console.log(dp[0].toString());
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))

# dp[l] mewakili interval [l, l + len - 1]; cukup satu baris per panjang
dp = a[:]
for length in range(2, n + 1):
    dp = [max(a[l] - dp[l + 1], a[l + length - 1] - dp[l]) for l in range(n - length + 1)]
print(dp[0])
CODE,
        ],
        'editorial' => '<p>Pada setiap saat, sisa kartu selalu berupa potongan berurutan <code>a[l..r]</code>, jadi state-nya interval. Karena kedua pemain memakai strategi yang sama, simpan nilai dari sudut pandang <strong>pemain yang sedang jalan</strong>:</p>
<p style="text-align:center"><code>dp[l][r]</code> = (skor pemain yang jalan) − (skor lawannya) dengan sisa kartu l..r.</p>
<p>Jika pemain mengambil kartu kiri, ia mendapat <code>a[l]</code>, lalu lawan menjadi pemain yang jalan pada <code>[l+1, r]</code> dan unggul <code>dp[l+1][r]</code>. Jadi selisihnya <code>a[l] − dp[l+1][r]</code>. Sama untuk kanan:</p>
<p style="text-align:center"><code>dp[l][r] = max(a[l] − dp[l+1][r], a[r] − dp[l][r−1])</code>, dengan <code>dp[i][i] = a[i]</code>.</p>
<p>Nilai bisa mencapai 2000 · 10<sup>9</sup>, pakai <code>long long</code> (atau BigInt di JavaScript). Memori bisa dihemat dengan menyimpan satu baris per panjang interval.</p>
<p><strong>Kompleksitas:</strong> O(N²).</p>',
        'hints' => [
            'Setelah beberapa giliran, kartu yang tersisa selalu potongan berurutan a[l..r].',
            'Simpan selisih skor dari sudut pandang pemain yang SEDANG jalan, bukan skor Andi atau Budi secara terpisah.',
            'dp[l][r] = max(a[l] − dp[l+1][r], a[r] − dp[l][r−1]). Setelah kita mengambil, lawanlah yang menjadi pemain yang jalan.',
        ],
        'sample_visual' => 'bars',
    ],

    // ═════════════════════════════ DP Bitmask ═════════════════════════════
    [
        'slug' => 'bagi-tugas',
        'lesson' => 'dp-bitmask',
        'title' => 'Pembagian Tugas Piket',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'bitmask', 'assignment'],
        'statement' => '<p>Ada <strong>N</strong> siswa dan <strong>N</strong> tugas piket. Siswa ke-i membutuhkan waktu <code>c[i][j]</code> menit untuk mengerjakan tugas ke-j. Setiap siswa mendapat <strong>tepat satu</strong> tugas, dan setiap tugas dikerjakan <strong>tepat satu</strong> siswa.</p>
<p>Tentukan <strong>total waktu minimum</strong>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi N bilangan: baris ke-i adalah <code>c[i][1] … c[i][N]</code>.</p>',
        'output_format' => '<p>Total waktu minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 16</li><li>1 ≤ c[i][j] ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n9 2 7\n6 4 3\n5 8 1\n", 'explanation' => 'Siswa 1 → tugas 2 (2), siswa 2 → tugas 1 (6), siswa 3 → tugas 3 (1). Total 9.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $out = [(string) $n];
                for ($i = 0; $i < $n; $i++) {
                    $row = [];
                    for ($j = 0; $j < $n; $j++) {
                        $row[] = mt_rand(1, $max);
                    }
                    $out[] = implode(' ', $row);
                }

                return implode("\n", $out)."\n";
            };

            return ["1\n5\n", "2\n1 100\n100 1\n", $mk(4, 10), $mk(8, 1000), $mk(12, 1000000), $mk(15, 100), $mk(16, 1000000), $mk(16, 3)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $c = [];
            for ($i = 1; $i <= $n; $i++) {
                $c[] = T::ints($lines[$i]);
            }
            $full = 1 << $n;
            $dp = array_fill(0, $full, PHP_INT_MAX);
            $dp[0] = 0;
            for ($mask = 0; $mask < $full; $mask++) {
                $cur = $dp[$mask];
                if ($cur === PHP_INT_MAX) {
                    continue;
                }
                $i = substr_count(decbin($mask), '1');
                if ($i === $n) {
                    continue;
                }
                $row = $c[$i];
                for ($j = 0; $j < $n; $j++) {
                    if (! ($mask >> $j & 1)) {
                        $nm = $mask | (1 << $j);
                        if ($cur + $row[$j] < $dp[$nm]) {
                            $dp[$nm] = $cur + $row[$j];
                        }
                    }
                }
            }

            return (string) $dp[$full - 1];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<long long>> c(n, vector<long long>(n));
    for (int i = 0; i < n; i++)
        for (int j = 0; j < n; j++) cin >> c[i][j];

    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const c = [];
for (let i = 0; i < n; i++) c.push(readInts());

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
c = [list(map(int, input().split())) for _ in range(n)]

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<long long>> c(n, vector<long long>(n));
    for (int i = 0; i < n; i++)
        for (int j = 0; j < n; j++) cin >> c[i][j];

    // dp[mask] = waktu minimum jika tugas-tugas di mask sudah dibagikan
    //            ke popcount(mask) siswa pertama
    vector<long long> dp(1 << n, INF);
    dp[0] = 0;
    for (int mask = 0; mask < (1 << n); mask++) {
        if (dp[mask] == INF) continue;
        int i = __builtin_popcount(mask);   // siswa berikutnya
        if (i == n) continue;
        for (int j = 0; j < n; j++) {
            if (mask >> j & 1) continue;    // tugas j sudah diambil
            int baru = mask | (1 << j);
            dp[baru] = min(dp[baru], dp[mask] + c[i][j]);
        }
    }
    cout << dp[(1 << n) - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const c = [];
for (let i = 0; i < n; i++) c.push(readInts());

const FULL = 1 << n;
const dp = new Array(FULL).fill(Infinity);
const pop = new Array(FULL).fill(0);
for (let m = 1; m < FULL; m++) pop[m] = pop[m >> 1] + (m & 1);
dp[0] = 0;
for (let mask = 0; mask < FULL; mask++) {
  if (dp[mask] === Infinity) continue;
  const i = pop[mask];
  if (i === n) continue;
  for (let j = 0; j < n; j++) {
    if ((mask >> j) & 1) continue;
    const baru = mask | (1 << j);
    if (dp[mask] + c[i][j] < dp[baru]) dp[baru] = dp[mask] + c[i][j];
  }
}
console.log(dp[FULL - 1]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
c = [list(map(int, input().split())) for _ in range(n)]

INF = float("inf")
FULL = 1 << n
dp = [INF] * FULL
dp[0] = 0
for mask in range(FULL):
    cur = dp[mask]
    if cur == INF:
        continue
    i = bin(mask).count("1")
    if i == n:
        continue
    row = c[i]
    for j in range(n):
        if not (mask >> j & 1):
            baru = mask | (1 << j)
            if cur + row[j] < dp[baru]:
                dp[baru] = cur + row[j]
print(dp[FULL - 1])
CODE,
        ],
        'editorial' => '<p>Mencoba semua pembagian butuh N! langkah, terlalu banyak untuk N = 16 (≈ 2 · 10<sup>13</sup>). Perhatikan bahwa kita boleh membagikan tugas ke siswa <strong>secara berurutan</strong>: siswa 1 dulu, lalu siswa 2, dan seterusnya. Yang perlu diingat hanyalah <em>himpunan</em> tugas yang sudah diambil.</p>
<p><code>dp[mask]</code> = waktu minimum jika tugas-tugas di mask sudah dibagikan. Banyak bit 1 di mask = banyak siswa yang sudah dapat, jadi siswa berikutnya adalah <code>i = popcount(mask)</code>. Transisinya: berikan tugas j yang belum diambil ke siswa i.</p>
<p><strong>Kompleksitas:</strong> O(2<sup>N</sup> · N) ≈ 10<sup>6</sup>.</p>',
        'hints' => [
            'Mencoba semua permutasi (N!) terlalu lambat. Yang penting hanya tugas MANA yang sudah diambil, bukan urutannya.',
            'Simpan himpunan tugas yang sudah dibagikan sebagai bilangan biner (mask). Ada 2^N kemungkinan.',
            'Bagikan ke siswa secara berurutan: siswa berikutnya adalah popcount(mask). dp[mask | 1<<j] = min(…, dp[mask] + c[i][j]).',
        ],
    ],

    [
        'slug' => 'kurir-keliling',
        'lesson' => 'dp-bitmask',
        'title' => 'Kurir Keliling',
        'difficulty' => 'Sulit',
        'tags' => ['dp', 'bitmask', 'tsp'],
        'statement' => '<p>Seorang kurir berangkat dari kantor pos di titik <strong>0</strong> dan harus mengantar paket ke <strong>N − 1</strong> rumah (titik 1 sampai N − 1). Waktu tempuh dari titik i ke titik j adalah <code>d[i][j]</code> menit (tidak harus sama dengan <code>d[j][i]</code>).</p>
<p>Kurir harus mengunjungi setiap rumah <strong>tepat sekali</strong> lalu kembali ke kantor pos. Berapa total waktu tempuh minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya berisi matriks <code>d</code>; baris ke-i berisi <code>d[i][0] … d[i][N−1]</code>. Nilai <code>d[i][i]</code> selalu 0.</p>',
        'output_format' => '<p>Total waktu minimum untuk berkeliling dan kembali ke titik 0.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 13</li><li>1 ≤ d[i][j] ≤ 10<sup>6</sup> untuk i ≠ j</li></ul>',
        'samples' => [
            ['input' => "4\n0 2 9 4\n2 0 6 3\n9 6 0 5\n4 3 5 0\n", 'explanation' => 'Rute 0 → 1 → 2 → 3 → 0 memakan 2 + 6 + 5 + 4 = 17 menit.'],
            ['input' => "3\n0 1 10\n10 0 1\n1 10 0\n", 'explanation' => 'Jalan searah 0 → 1 → 2 → 0 hanya 3 menit, sedangkan arah sebaliknya 30 menit.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $out = [(string) $n];
                for ($i = 0; $i < $n; $i++) {
                    $row = [];
                    for ($j = 0; $j < $n; $j++) {
                        $row[] = $i === $j ? 0 : mt_rand(1, $max);
                    }
                    $out[] = implode(' ', $row);
                }

                return implode("\n", $out)."\n";
            };

            return ["2\n0 5\n7 0\n", $mk(3, 10), $mk(5, 100), $mk(8, 1000), $mk(10, 1000000), $mk(12, 50), $mk(13, 1000000), $mk(13, 2)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $d = [];
            for ($i = 1; $i <= $n; $i++) {
                $d[] = T::ints($lines[$i]);
            }
            $INF = PHP_INT_MAX;
            $full = 1 << $n;
            $dp = array_fill(0, $full, array_fill(0, $n, $INF));
            $dp[1][0] = 0;
            for ($mask = 1; $mask < $full; $mask += 2) {
                for ($v = 0; $v < $n; $v++) {
                    $cur = $dp[$mask][$v];
                    if ($cur === $INF) {
                        continue;
                    }
                    $dv = $d[$v];
                    for ($u = 1; $u < $n; $u++) {
                        if ($mask >> $u & 1) {
                            continue;
                        }
                        $nm = $mask | (1 << $u);
                        if ($cur + $dv[$u] < $dp[$nm][$u]) {
                            $dp[$nm][$u] = $cur + $dv[$u];
                        }
                    }
                }
            }
            $best = $INF;
            for ($v = 1; $v < $n; $v++) {
                $best = min($best, $dp[$full - 1][$v] + $d[$v][0]);
            }

            return (string) $best;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<long long>> d(n, vector<long long>(n));
    for (int i = 0; i < n; i++)
        for (int j = 0; j < n; j++) cin >> d[i][j];

    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const d = [];
for (let i = 0; i < n; i++) d.push(readInts());

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
d = [list(map(int, input().split())) for _ in range(n)]

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<long long>> d(n, vector<long long>(n));
    for (int i = 0; i < n; i++)
        for (int j = 0; j < n; j++) cin >> d[i][j];

    // dp[mask][v] = waktu minimum: mulai di 0, sudah mengunjungi titik-titik di mask,
    //               dan sekarang berada di titik v
    int FULL = 1 << n;
    vector<vector<long long>> dp(FULL, vector<long long>(n, INF));
    dp[1][0] = 0;
    for (int mask = 1; mask < FULL; mask += 2) {   // titik 0 selalu ada di mask
        for (int v = 0; v < n; v++) {
            if (dp[mask][v] == INF) continue;
            for (int u = 1; u < n; u++) {
                if (mask >> u & 1) continue;
                int baru = mask | (1 << u);
                dp[baru][u] = min(dp[baru][u], dp[mask][v] + d[v][u]);
            }
        }
    }

    long long jawaban = INF;
    for (int v = 1; v < n; v++) jawaban = min(jawaban, dp[FULL - 1][v] + d[v][0]);
    cout << jawaban << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const d = [];
for (let i = 0; i < n; i++) d.push(readInts());

const FULL = 1 << n;
const dp = Array.from({ length: FULL }, () => new Array(n).fill(Infinity));
dp[1][0] = 0;
for (let mask = 1; mask < FULL; mask += 2) {
  for (let v = 0; v < n; v++) {
    const cur = dp[mask][v];
    if (cur === Infinity) continue;
    for (let u = 1; u < n; u++) {
      if ((mask >> u) & 1) continue;
      const baru = mask | (1 << u);
      if (cur + d[v][u] < dp[baru][u]) dp[baru][u] = cur + d[v][u];
    }
  }
}
let jawaban = Infinity;
for (let v = 1; v < n; v++) jawaban = Math.min(jawaban, dp[FULL - 1][v] + d[v][0]);
console.log(jawaban);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
d = [list(map(int, input().split())) for _ in range(n)]

INF = float("inf")
FULL = 1 << n
dp = [[INF] * n for _ in range(FULL)]
dp[1][0] = 0
for mask in range(1, FULL, 2):
    row = dp[mask]
    for v in range(n):
        cur = row[v]
        if cur == INF:
            continue
        dv = d[v]
        for u in range(1, n):
            if mask >> u & 1:
                continue
            baru = mask | (1 << u)
            if cur + dv[u] < dp[baru][u]:
                dp[baru][u] = cur + dv[u]
print(min(dp[FULL - 1][v] + d[v][0] for v in range(1, n)))
CODE,
        ],
        'editorial' => '<p>Ini adalah <strong>Travelling Salesman Problem</strong>. Mencoba semua urutan rumah butuh (N − 1)! ≈ 4,8 · 10<sup>8</sup> rute untuk N = 13, terlalu lambat. Yang menentukan kelanjutan rute hanyalah <em>himpunan</em> titik yang sudah dikunjungi dan <em>posisi sekarang</em>.</p>
<p><code>dp[mask][v]</code> = waktu minimum untuk mulai di 0, mengunjungi tepat titik-titik di mask, dan berakhir di v. Base case <code>dp[1][0] = 0</code>. Transisinya: dari (mask, v) pergi ke titik u yang belum dikunjungi:</p>
<p style="text-align:center"><code>dp[mask | 1&lt;&lt;u][u] = min(…, dp[mask][v] + d[v][u])</code></p>
<p>Jawabannya <code>min(dp[penuh][v] + d[v][0])</code> untuk v ≥ 1. Ingat bahwa matriksnya <strong>tidak simetris</strong>, jadi gunakan <code>d[v][u]</code> sesuai arah.</p>
<p><strong>Kompleksitas:</strong> O(2<sup>N</sup> · N²).</p>',
        'hints' => [
            'Mencoba semua urutan rumah terlalu lambat. Apa yang benar-benar perlu diingat di tengah perjalanan?',
            'Cukup dua hal: himpunan titik yang sudah dikunjungi (mask) dan posisi kurir sekarang (v).',
            'dp[mask][v]: dari sini, pergi ke u yang belum ada di mask. Di akhir, tambahkan waktu kembali d[v][0].',
        ],
    ],
];
