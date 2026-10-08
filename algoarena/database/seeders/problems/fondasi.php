<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Fondasi: melatih membaca batasan dan memilih kompleksitas yang tepat.
 */

$queryInput = function (int $n, int $q, int $lo, int $hi, callable $query): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = mt_rand($lo, $hi);
    }
    $out = ["$n $q", implode(' ', $a)];
    for ($i = 0; $i < $q; $i++) {
        $out[] = $query($n);
    }

    return implode("\n", $out)."\n";
};

return [
    [
        'slug' => 'jumlah-rentang',
        'lesson' => 'kompleksitas',
        'title' => 'Tabungan Harian',
        'difficulty' => 'Mudah',
        'tags' => ['prefix sum', 'kompleksitas'],
        'statement' => '<p>Rani mencatat perubahan tabungannya selama <strong>N</strong> hari. Pada hari ke-i tabungannya berubah sebesar <code>a<sub>i</sub></code> rupiah (negatif berarti ia mengambil uang).</p>
<p>Ia punya <strong>Q</strong> pertanyaan: berapa total perubahan tabungan dari hari ke-<code>l</code> sampai hari ke-<code>r</code> (termasuk keduanya)?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Q baris berikutnya berisi <code>l r</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing jumlah <code>a<sub>l</sub> + … + a<sub>r</sub></code>.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ l ≤ r ≤ N</li></ul>',
        'samples' => [
            ['input' => "5 3\n5 -2 7 3 -4\n1 3\n2 5\n4 4\n", 'explanation' => 'Hari 1–3: 5 − 2 + 7 = 10. Hari 2–5: −2 + 7 + 3 − 4 = 4. Hari 4 saja: 3.'],
        ],
        'tests' => function () use ($queryInput) {
            $range = function (int $n) {
                $l = mt_rand(1, $n);
                $r = mt_rand($l, $n);

                return "$l $r";
            };

            return [
                "1 1\n-7\n1 1\n", $queryInput(10, 10, -10, 10, $range), $queryInput(1000, 1000, -1000000000, 1000000000, $range),
                $queryInput(200000, 200000, -1000000000, 1000000000, $range), $queryInput(200000, 200000, 1000000000, 1000000000, fn ($n) => "1 $n"),
                $queryInput(200000, 200000, -1000000000, -999999999, $range),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $pre = [0];
            $s = 0;
            foreach (T::ints($lines[1]) as $x) {
                $s += $x;
                $pre[] = $s;
            }
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$l, $r] = T::ints($lines[2 + $i]);
                $out[] = $pre[$r] - $pre[$l - 1];
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<long long> a(n + 1);
    for (int i = 1; i <= n; i++) cin >> a[i];

    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();

// Tulis solusimu di sini

const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  // out.push(...)
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
a = list(map(int, input().split()))

# Tulis solusimu di sini

out = []
for _ in range(q):
    l, r = map(int, input().split())
    # out.append(...)
print("\n".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    // pre[i] = a[1] + a[2] + ... + a[i]
    vector<long long> pre(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        long long x;
        cin >> x;
        pre[i] = pre[i - 1] + x;
    }
    string out;
    for (int i = 0; i < q; i++) {
        int l, r;
        cin >> l >> r;
        out += to_string(pre[r] - pre[l - 1]) + "\n";   // O(1) per pertanyaan
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
// Jumlah bisa mencapai 2 · 10^14: masih aman untuk Number (≤ 9 · 10^15)
const pre = [0];
for (let i = 0; i < n; i++) pre.push(pre[i] + a[i]);
const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  out.push(pre[r] - pre[l - 1]);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate
input = sys.stdin.readline

n, q = map(int, input().split())
a = list(map(int, input().split()))
pre = [0] + list(accumulate(a))

out = []
for _ in range(q):
    l, r = map(int, input().split())
    out.append(pre[r] - pre[l - 1])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Menjumlahkan l..r dengan loop untuk setiap pertanyaan butuh O(N) per pertanyaan, total O(N · Q) = 4 · 10<sup>10</sup>. Terlalu lambat.</p>
<p>Siapkan <strong>prefix sum</strong>: <code>pre[i] = a<sub>1</sub> + … + a<sub>i</sub></code>, dengan <code>pre[0] = 0</code>. Maka</p>
<p style="text-align:center"><code>a<sub>l</sub> + … + a<sub>r</sub> = pre[r] − pre[l − 1]</code></p>
<p>Menyiapkan pre butuh O(N), dan setiap pertanyaan dijawab dalam O(1). Totalnya O(N + Q).</p>
<p><strong>Awas:</strong> jumlahnya bisa mencapai 2 · 10<sup>14</sup>, jadi pakai <code>long long</code>.</p>',
        'hints' => [
            'Hitung dulu berapa operasi jika setiap pertanyaan dijawab dengan loop dari l sampai r. Apakah di bawah 10^8?',
            'Siapkan pre[i] = jumlah a[1..i] sekali saja di awal.',
            'Jumlah l..r = pre[r] − pre[l − 1]. Gunakan long long.',
        ],
    ],

    [
        'slug' => 'nilai-ujian',
        'lesson' => 'kompleksitas',
        'title' => 'Berapa yang Lulus?',
        'difficulty' => 'Mudah',
        'tags' => ['binary search', 'sorting', 'kompleksitas'],
        'statement' => '<p>Ada <strong>N</strong> siswa yang mengikuti ujian. Nilai siswa ke-i adalah <code>a<sub>i</sub></code>. Panitia belum menetapkan nilai batas kelulusan, jadi mereka ingin mencoba <strong>Q</strong> kemungkinan batas.</p>
<p>Untuk setiap batas <code>x</code>, berapa banyak siswa yang nilainya <strong>paling sedikit x</strong>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi satu bilangan <code>x</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing banyak siswa dengan nilai ≥ x.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>0 ≤ a<sub>i</sub>, x ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6 4\n70 85 60 85 90 40\n75\n85\n100\n0\n", 'explanation' => 'Nilai ≥ 75: 85, 85, 90 (3 siswa). Nilai ≥ 85: juga 3 siswa. Tidak ada yang ≥ 100. Semua 6 siswa ≥ 0.'],
        ],
        'tests' => function () use ($queryInput) {
            return [
                "1 2\n50\n50\n51\n", $queryInput(10, 10, 0, 100, fn () => (string) mt_rand(0, 100)),
                $queryInput(2000, 2000, 0, 1000000000, fn () => (string) mt_rand(0, 1000000000)),
                $queryInput(200000, 200000, 0, 1000000000, fn () => (string) mt_rand(0, 1000000000)),
                $queryInput(200000, 200000, 0, 100, fn () => (string) mt_rand(0, 101)),
                $queryInput(200000, 200000, 7, 7, fn () => (string) mt_rand(6, 8)),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            sort($a);
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                $x = (int) $lines[2 + $i];
                $lo = 0;
                $hi = $n;
                while ($lo < $hi) {
                    $mid = ($lo + $hi) >> 1;
                    if ($a[$mid] < $x) {
                        $lo = $mid + 1;
                    } else {
                        $hi = $mid;
                    }
                }
                $out[] = $n - $lo;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<int> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];

    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();

// Tulis solusimu di sini

const out = [];
for (let i = 0; i < q; i++) {
  const [x] = readInts();
  // out.push(...)
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
a = list(map(int, input().split()))

# Tulis solusimu di sini

out = []
for _ in range(q):
    x = int(input())
    # out.append(...)
print("\n".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<int> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];
    sort(a.begin(), a.end());               // O(N log N), sekali saja

    string out;
    for (int i = 0; i < q; i++) {
        int x;
        cin >> x;
        // posisi pertama dengan nilai >= x; semua di kanannya juga >= x
        int pos = lower_bound(a.begin(), a.end(), x) - a.begin();
        out += to_string(n - pos) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts().sort((p, r) => p - r);
const out = [];
for (let i = 0; i < q; i++) {
  const [x] = readInts();
  let lo = 0, hi = n;
  while (lo < hi) {
    const mid = (lo + hi) >> 1;
    if (a[mid] < x) lo = mid + 1;
    else hi = mid;
  }
  out.push(n - lo);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_left
input = sys.stdin.readline

n, q = map(int, input().split())
a = sorted(map(int, input().split()))
out = []
for _ in range(q):
    x = int(input())
    out.append(n - bisect_left(a, x))
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Menghitung dengan loop untuk setiap batas butuh O(N · Q) = 4 · 10<sup>10</sup> operasi, terlalu lambat.</p>
<p><strong>Urutkan</strong> nilai sekali di awal (O(N log N)). Setelah urut, semua nilai ≥ x berkumpul di bagian kanan. Cari posisi pertama yang nilainya ≥ x dengan <strong>binary search</strong> (<code>lower_bound</code>), lalu jawabannya <code>N − posisi</code>.</p>
<p><strong>Kompleksitas:</strong> O((N + Q) log N).</p>',
        'hints' => [
            'Loop ke semua siswa untuk setiap batas terlalu lambat: N · Q bisa 4 · 10^10.',
            'Jika nilai sudah diurutkan, siswa dengan nilai ≥ x pasti berkumpul di ujung kanan.',
            'Cari posisi pertama yang ≥ x dengan binary search (lower_bound). Jawabannya N − posisi.',
        ],
    ],
];
