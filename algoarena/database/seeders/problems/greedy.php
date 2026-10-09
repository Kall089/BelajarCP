<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Greedy: prinsip & exchange argument, interval, rasio/Huffman, regret dengan heap,
 * konstruktif (jawaban tunggal), dan invarian.
 * Semua kode C++ harus lolos C++14: tanpa structured binding.
 */

return [
    // ───────────────────────── Prinsip Greedy ─────────────────────────
    [
        'slug' => 'perahu-penyeberangan',
        'lesson' => 'greedy-dasar',
        'title' => 'Perahu Penyeberangan',
        'difficulty' => 'Mudah',
        'tags' => ['greedy', 'two pointers', 'sorting'],
        'statement' => '<p>Ada <strong>n</strong> orang yang ingin menyeberang sungai. Berat orang ke-i adalah <code>w<sub>i</sub></code>. Setiap perahu memuat <strong>paling banyak dua orang</strong> dengan total berat paling banyak <strong>x</strong>.</p>
<p>Berapa perahu minimum yang dibutuhkan agar semua orang menyeberang?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n x</code>. Baris kedua berisi <code>w<sub>1</sub> … w<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak perahu minimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>1 ≤ w<sub>i</sub> ≤ x ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 10\n7 2 3 9\n", 'explanation' => 'Orang 9 harus sendirian (9 + 2 = 11 &gt; 10). Orang 7 bisa bersama orang 2, dan orang 3 menyeberang sendiri: {9}, {2, 7}, {3}. Dua perahu tidak cukup.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $x, int $lo) {
                return "$n $x\n".T::join(T::arr($n, $lo, $x))."\n";
            };

            return [
                "1 5\n5\n", "2 10\n5 5\n", "3 10\n6 6 6\n", $gen(10, 20, 1), $gen(1000, 100, 1), $gen(200000, 1000000000, 1),
                $gen(200000, 1000000000, 500000000), $gen(200000, 100, 50), $gen(199999, 7, 1),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $x] = T::ints($lines[0]);
            $w = T::ints($lines[1]);
            sort($w);
            // Referensi: orang terberat selalu naik; ajak orang teringan jika muat
            $i = 0;
            $j = $n - 1;
            $p = 0;
            while ($i <= $j) {
                if ($i < $j && $w[$i] + $w[$j] <= $x) {
                    $i++;
                }
                $j--;
                $p++;
            }

            return (string) $p;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long x;
    cin >> n >> x;
    vector<long long> w(n);
    for (auto& v : w) cin >> v;
    sort(w.begin(), w.end());

    // orang terberat pasti butuh perahu: dengan siapa ia sebaiknya berpasangan?
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, x] = readInts();
const w = Float64Array.from(readInts()).sort();
// orang terberat pasti butuh perahu: dengan siapa ia sebaiknya berpasangan?
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, x = int(data[0]), int(data[1])
w = sorted(map(int, data[2:2 + n]))
# orang terberat pasti butuh perahu: dengan siapa ia sebaiknya berpasangan?
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
    long long x;
    cin >> n >> x;
    vector<long long> w(n);
    for (auto& v : w) cin >> v;
    sort(w.begin(), w.end());

    int i = 0, j = n - 1, perahu = 0;
    while (i <= j) {
        if (i < j && w[i] + w[j] <= x) i++;   // yang teringan ikut, jika muat
        j--;                                  // yang terberat pasti naik perahu ini
        perahu++;
    }
    cout << perahu << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, x] = readInts();
const w = Float64Array.from(readInts()).sort();
let i = 0, j = n - 1, perahu = 0;
while (i <= j) {
  if (i < j && w[i] + w[j] <= x) i++;
  j--;
  perahu++;
}
console.log(String(perahu));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, x = int(data[0]), int(data[1])
w = sorted(map(int, data[2:2 + n]))
i, j, perahu = 0, n - 1, 0
while i <= j:
    if i < j and w[i] + w[j] <= x:
        i += 1
    j -= 1
    perahu += 1
print(perahu)
CODE,
        ],
        'editorial' => '<p>Urutkan berat. Orang terberat pasti butuh perahu. Satu-satunya pertanyaan: siapa yang ikut bersamanya? Jika orang teringan pun tidak muat, ia harus sendirian. Jika muat, ajak orang teringan.</p>
<p><strong>Mengapa teringan?</strong> Misalkan H orang terberat, L orang teringan, dan H + L ≤ x. Di solusi optimal mana pun, jika H tidak bersama L, maka H bersama B (atau sendirian) dan L bersama C (atau sendirian). Ubah menjadi {H, L} dan {B, C}. Pasangan pertama muat karena dicek. Pasangan kedua juga muat: C ≤ H, jadi B + C ≤ B + H ≤ x. Banyak perahu tidak bertambah, jadi selalu ada solusi optimal yang memasangkan H dengan L.</p>
<p>Dua pointer di kedua ujung array terurut: O(n log n).</p>',
        'hints' => [
            'Urutkan berat. Pikirkan orang terberat lebih dulu: ia pasti butuh satu perahu.',
            'Siapa teman perahu terbaik untuk orang terberat? Pilihan yang paling "mudah muat" adalah orang teringan.',
            'Dua pointer: i di yang teringan, j di yang terberat. Jika w[i] + w[j] ≤ x, keduanya naik; jika tidak, hanya w[j].',
        ],
    ],

    [
        'slug' => 'tugas-tenggat',
        'lesson' => 'greedy-dasar',
        'title' => 'Tugas dan Tenggat',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'exchange argument', 'sorting'],
        'statement' => '<p>Kamu harus mengerjakan <strong>n</strong> tugas, satu per satu, mulai dari waktu 0. Tugas ke-i butuh waktu <code>a<sub>i</sub></code> dan punya tenggat <code>d<sub>i</sub></code>. Jika tugas itu selesai pada waktu f, kamu mendapat poin <code>d<sub>i</sub> − f</code> (bisa negatif jika terlambat).</p>
<p>Semua tugas harus dikerjakan. Berapa total poin maksimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi <code>a<sub>i</sub> d<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: total poin maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>5</sup></li><li>1 ≤ d<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n6 10\n8 15\n5 12\n", 'explanation' => 'Urutan 5, 6, 8 (berdasarkan durasi): selesai pada 5, 11, 19. Poin (12 − 5) + (10 − 11) + (15 − 19) = 7 − 1 − 4 = 2.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $aMax, int $dMax) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = mt_rand(1, $aMax).' '.mt_rand(1, $dMax);
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n5 3\n", "2\n1 100\n100 1\n", $gen(8, 10, 30), $gen(1000, 1000, 100000), $gen(200000, 100000, 1000000000),
                $gen(200000, 100000, 10), $gen(200000, 1, 1000000000), $gen(200000, 7, 1000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $a = [];
            $sumD = 0;
            for ($i = 1; $i <= $n; $i++) {
                [$x, $y] = T::ints($lines[$i]);
                $a[] = $x;
                $sumD += $y;
            }
            sort($a);
            // Total poin = Σd − Σf; Σf minimum jika durasi pendek dikerjakan dulu
            $f = 0;
            $sumF = 0;
            foreach ($a as $x) {
                $f += $x;
                $sumF += $f;
            }

            return (string) ($sumD - $sumF);
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
    vector<pair<long long, long long>> t(n);   // (durasi, tenggat)
    for (auto& p : t) cin >> p.first >> p.second;

    // tulis total poin sebagai (jumlah tenggat) − (jumlah waktu selesai)
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = [], d = [];
for (let i = 0; i < n; i++) {
  const [x, y] = readInts();
  a.push(x);
  d.push(y);
}
// tulis total poin sebagai (jumlah tenggat) − (jumlah waktu selesai)
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + 2 * n:2]))
d = list(map(int, data[2:2 + 2 * n:2]))
# tulis total poin sebagai (jumlah tenggat) − (jumlah waktu selesai)
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
    vector<pair<long long, long long>> t(n);   // (durasi, tenggat)
    for (auto& p : t) cin >> p.first >> p.second;
    sort(t.begin(), t.end());                  // durasi terpendek dulu

    long long waktu = 0, poin = 0;
    for (auto& p : t) {
        waktu += p.first;                      // tugas ini selesai pada 'waktu'
        poin += p.second - waktu;
    }
    cout << poin << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const t = [];
for (let i = 0; i < n; i++) t.push(readInts());
t.sort((p, q) => p[0] - q[0]);
let waktu = 0, poin = 0;
for (const [a, d] of t) {
  waktu += a;
  poin += d - waktu;
}
console.log(String(poin));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
t = sorted(zip(map(int, data[1:1 + 2 * n:2]), map(int, data[2:2 + 2 * n:2])))
waktu = poin = 0
for a, d in t:
    waktu += a
    poin += d - waktu
print(poin)
CODE,
        ],
        'editorial' => '<p>Total poin = Σ d<sub>i</sub> − Σ f<sub>i</sub>. Bagian pertama tidak bergantung pada urutan! Jadi soalnya sama dengan meminimalkan jumlah waktu selesai Σ f<sub>i</sub>, dan tenggat sama sekali tidak memengaruhi urutan terbaik.</p>
<p><strong>Tukar tetangga.</strong> Jika dua tugas berdampingan i lalu j dengan a<sub>i</sub> &gt; a<sub>j</sub>, menukar keduanya tidak mengubah waktu selesai tugas lain, sedangkan jumlah waktu selesai keduanya berkurang a<sub>i</sub> − a<sub>j</sub>. Jadi urutan optimal adalah durasi menaik (Shortest Job First).</p>
<p>Σ f bisa mencapai sekitar 2·10<sup>15</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Tulis total poin sebagai Σd − Σf. Bagian mana yang dipengaruhi urutan pengerjaan?',
            'Tenggat ternyata tidak memengaruhi urutan terbaik. Yang perlu diminimalkan hanya jumlah waktu selesai.',
            'Tukar dua tugas berdampingan yang durasinya "terbalik": jumlah waktu selesai turun. Jadi urutkan durasi menaik.',
        ],
    ],

    [
        'slug' => 'hasil-kali-minimum',
        'lesson' => 'greedy-dasar',
        'title' => 'Jumlah Hasil Kali Minimum',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'ketidaksamaan susunan ulang', 'sorting'],
        'statement' => '<p>Diberikan dua array <strong>a</strong> dan <strong>b</strong> berukuran n. Kamu boleh menyusun ulang isi <strong>b</strong> sesukamu (a tetap). Berapa nilai terkecil dari</p>
<p><code>a<sub>1</sub>·b<sub>1</sub> + a<sub>2</sub>·b<sub>2</sub> + … + a<sub>n</sub>·b<sub>n</sub></code>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Baris kedua berisi isi a, baris ketiga berisi isi b.</p>',
        'output_format' => '<p>Satu bilangan: jumlah terkecil.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>−10<sup>5</sup> ≤ a<sub>i</sub>, b<sub>i</sub> ≤ 10<sup>5</sup></li></ul>',
        'samples' => [
            ['input' => "3\n1 2 3\n4 5 6\n", 'explanation' => 'Pasangkan 1 dengan 6, 2 dengan 5, 3 dengan 4: 6 + 10 + 12 = 28.'],
            ['input' => "2\n-3 2\n-1 5\n", 'explanation' => 'Pasangkan −3 dengan 5 dan 2 dengan −1: −15 − 2 = −17.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return [
                "1\n-100000\n100000\n", $gen(6, -5, 5), $gen(8, 0, 10), $gen(1000, -1000, 1000), $gen(200000, -100000, 100000),
                $gen(200000, 0, 100000), $gen(200000, -100000, 0), $gen(200000, -2, 2),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $b = T::ints($lines[2]);
            sort($a);
            rsort($b);
            $s = 0;
            foreach ($a as $i => $x) {
                $s += $x * $b[$i];
            }

            return (string) $s;
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
    vector<long long> a(n), b(n);
    for (auto& v : a) cin >> v;
    for (auto& v : b) cin >> v;

    // nilai besar di a sebaiknya dipasangkan dengan nilai apa di b?
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = readInts(), b = readInts();
// nilai besar di a sebaiknya dipasangkan dengan nilai apa di b?
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + n]))
b = list(map(int, data[1 + n:1 + 2 * n]))
# nilai besar di a sebaiknya dipasangkan dengan nilai apa di b?
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
    vector<long long> a(n), b(n);
    for (auto& v : a) cin >> v;
    for (auto& v : b) cin >> v;
    sort(a.begin(), a.end());                     // a naik
    sort(b.begin(), b.end(), greater<long long>()); // b turun

    long long s = 0;
    for (int i = 0; i < n; i++) s += a[i] * b[i];
    cout << s << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = Float64Array.from(readInts()).sort();
const b = Float64Array.from(readInts()).sort().reverse();
let s = 0;
for (let i = 0; i < n; i++) s += a[i] * b[i];
console.log(String(s));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
a = sorted(map(int, data[1:1 + n]))
b = sorted(map(int, data[1 + n:1 + 2 * n]), reverse=True)
print(sum(x * y for x, y in zip(a, b)))
CODE,
        ],
        'editorial' => '<p><strong>Ketidaksamaan susunan ulang:</strong> Σ a<sub>i</sub>b<sub>i</sub> paling kecil jika a dan b diurutkan berlawanan arah (yang terbesar dengan yang terkecil), dan paling besar jika searah. Ini berlaku juga untuk bilangan negatif.</p>
<p>Buktinya tukar tetangga: misalkan a<sub>i</sub> &lt; a<sub>j</sub> tetapi b<sub>i</sub> &lt; b<sub>j</sub> (searah). Menukar b<sub>i</sub> dan b<sub>j</sub> mengubah jumlah sebesar (a<sub>i</sub>b<sub>j</sub> + a<sub>j</sub>b<sub>i</sub>) − (a<sub>i</sub>b<sub>i</sub> + a<sub>j</sub>b<sub>j</sub>) = −(a<sub>j</sub> − a<sub>i</sub>)(b<sub>j</sub> − b<sub>i</sub>) &lt; 0. Jadi setiap pasangan searah bisa diperbaiki, sampai semuanya berlawanan arah.</p>
<p>Hasil maksimum sekitar 2·10<sup>15</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Coba dua elemen saja: a = (1, 3), b = (2, 5). Pasangan mana yang memberi jumlah lebih kecil?',
            'Bandingkan jumlah sebelum dan sesudah menukar b<sub>i</sub> dan b<sub>j</sub>. Selisihnya bisa ditulis sebagai hasil kali dua selisih.',
            'Urutkan a naik dan b turun, lalu kalikan berpasangan.',
        ],
    ],

    // ───────────────────────── Greedy Interval ─────────────────────────
    [
        'slug' => 'jadwal-film',
        'lesson' => 'greedy-interval',
        'title' => 'Festival Film',
        'difficulty' => 'Mudah',
        'tags' => ['greedy', 'interval', 'activity selection'],
        'statement' => '<p>Di sebuah festival diputar <strong>n</strong> film. Film ke-i mulai pada waktu <code>a<sub>i</sub></code> dan selesai pada waktu <code>b<sub>i</sub></code>. Kamu ingin menonton film sebanyak mungkin, dan setiap film harus ditonton utuh dari awal sampai akhir.</p>
<p>Kamu boleh langsung menonton film yang mulai tepat saat film sebelumnya selesai. Berapa film paling banyak yang bisa ditonton?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi <code>a<sub>i</sub> b<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak film maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>0 ≤ a<sub>i</sub> &lt; b<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n3 5\n4 9\n5 8\n", 'explanation' => 'Tonton [3, 5] lalu [5, 8]. Film [4, 9] bentrok dengan keduanya.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi, int $len) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $a = mt_rand(0, $hi - 1);
                    $b = min($hi, $a + mt_rand(1, $len));
                    $rows[] = "$a $b";
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n0 1000000000\n", "3\n1 2\n2 3\n3 4\n", $gen(8, 20, 6), $gen(1000, 10000, 100), $gen(200000, 1000000000, 100000),
                $gen(200000, 1000000000, 1000000000), $gen(200000, 1000, 5), $gen(200000, 200000, 3),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $iv = [];
            for ($i = 1; $i <= $n; $i++) {
                $iv[] = T::ints($lines[$i]);
            }
            usort($iv, fn ($p, $q) => $p[1] <=> $q[1]);
            $akhir = -1;
            $c = 0;
            foreach ($iv as $v) {
                if ($v[0] >= $akhir) {
                    $c++;
                    $akhir = $v[1];
                }
            }

            return (string) $c;
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
    vector<pair<long long, long long>> film(n);
    for (auto& p : film) cin >> p.first >> p.second;

    // urutkan berdasarkan apa?
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const film = [];
for (let i = 0; i < n; i++) film.push(readInts());
// urutkan berdasarkan apa?
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
film = list(zip(map(int, data[1:1 + 2 * n:2]), map(int, data[2:2 + 2 * n:2])))
# urutkan berdasarkan apa?
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
    vector<pair<long long, long long>> film(n);    // simpan (selesai, mulai)
    for (auto& p : film) cin >> p.second >> p.first;
    sort(film.begin(), film.end());                // selesai paling awal dulu

    long long akhir = -1;
    int ditonton = 0;
    for (auto& p : film) {
        if (p.second >= akhir) {                   // mulai setelah film terakhir selesai
            ditonton++;
            akhir = p.first;
        }
    }
    cout << ditonton << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const film = [];
for (let i = 0; i < n; i++) film.push(readInts());
film.sort((p, q) => p[1] - q[1]);
let akhir = -1, ditonton = 0;
for (const [a, b] of film) {
  if (a >= akhir) {
    ditonton++;
    akhir = b;
  }
}
console.log(String(ditonton));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
film = sorted(zip(map(int, data[2:2 + 2 * n:2]), map(int, data[1:1 + 2 * n:2])))   # (selesai, mulai)
akhir, ditonton = -1, 0
for b, a in film:
    if a >= akhir:
        ditonton += 1
        akhir = b
print(ditonton)
CODE,
        ],
        'editorial' => '<p>Ini <em>activity selection</em> klasik. Urutkan film berdasarkan waktu <strong>selesai</strong>, lalu ambil setiap film yang mulai tidak lebih awal dari selesainya film terakhir yang diambil.</p>
<p><strong>Bukti (exchange).</strong> Film yang selesai paling awal, f, boleh dianggap ada di solusi optimal: jika solusi optimal dimulai dengan film g, ganti g dengan f. Karena f selesai tidak lebih lambat dari g, film-film lain di solusi itu tetap tidak bentrok. Ulangi untuk sisa film yang mulai setelah f selesai.</p>
<p>Kriteria lain (mulai paling awal, durasi terpendek) punya contoh penyangkal. Total O(n log n).</p>',
        'hints' => [
            'Film mana yang paling aman ditonton pertama? Pikirkan film yang menyisakan waktu paling banyak.',
            'Urutkan berdasarkan waktu selesai, bukan waktu mulai atau durasi.',
            'Simpan waktu selesai film terakhir; ambil film berikutnya jika mulainya ≥ waktu itu.',
        ],
    ],

    [
        'slug' => 'ruang-rapat',
        'lesson' => 'greedy-interval',
        'title' => 'Ruang Rapat Minimum',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'interval', 'sweep line', 'priority queue'],
        'statement' => '<p>Ada <strong>n</strong> rapat. Rapat ke-i berlangsung pada selang waktu <code>[l<sub>i</sub>, r<sub>i</sub>)</code>: mulai pada l<sub>i</sub> dan selesai tepat sebelum r<sub>i</sub>. Satu ruang hanya bisa dipakai satu rapat pada satu waktu, tetapi rapat yang mulai pada waktu r boleh memakai ruang rapat yang selesai pada r.</p>
<p>Berapa banyak ruang minimum agar semua rapat terlaksana?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi <code>l<sub>i</sub> r<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak ruang minimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>0 ≤ l<sub>i</sub> &lt; r<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4\n1 5\n2 6\n4 8\n6 9\n", 'explanation' => 'Pada waktu 4, tiga rapat berlangsung bersamaan: [1, 5), [2, 6), [4, 8). Rapat [6, 9) bisa memakai ruang rapat [2, 6) yang selesai pada 6.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi, int $len) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $a = mt_rand(0, $hi - 1);
                    $b = min($hi, $a + mt_rand(1, $len));
                    $rows[] = "$a $b";
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n0 1\n", "3\n1 2\n2 3\n3 4\n", "3\n1 4\n1 4\n1 4\n", $gen(8, 20, 8), $gen(1000, 10000, 500),
                $gen(200000, 1000000000, 1000000), $gen(200000, 1000000000, 1000000000), $gen(200000, 100, 3), $gen(200000, 300000, 10),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: min-heap waktu selesai (pakai ulang ruang yang sudah kosong)
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $iv = [];
            for ($i = 1; $i <= $n; $i++) {
                $iv[] = T::ints($lines[$i]);
            }
            usort($iv, fn ($p, $q) => $p[0] <=> $q[0]);
            $h = new SplMinHeap;
            foreach ($iv as $v) {
                if (! $h->isEmpty() && $h->top() <= $v[0]) {
                    $h->extract();
                }
                $h->insert($v[1]);
            }

            return (string) $h->count();
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
    vector<pair<long long, int>> ev;            // (waktu, +1 mulai / -1 selesai)
    for (int i = 0; i < n; i++) {
        long long l, r;
        cin >> l >> r;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const L = [], R = [];
for (let i = 0; i < n; i++) {
  const [l, r] = readInts();
  L.push(l);
  R.push(r);
}
// berapa rapat paling banyak yang berlangsung bersamaan?
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
L = list(map(int, data[1:1 + 2 * n:2]))
R = list(map(int, data[2:2 + 2 * n:2]))
# berapa rapat paling banyak yang berlangsung bersamaan?
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
    vector<pair<long long, int>> ev;            // (waktu, +1 mulai / -1 selesai)
    for (int i = 0; i < n; i++) {
        long long l, r;
        cin >> l >> r;
        ev.push_back({l, +1});
        ev.push_back({r, -1});
    }
    sort(ev.begin(), ev.end());                 // waktu sama: -1 (selesai) sebelum +1 (mulai)

    int aktif = 0, ruang = 0;
    for (auto& e : ev) {
        aktif += e.second;
        ruang = max(ruang, aktif);
    }
    cout << ruang << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const L = new Float64Array(n), R = new Float64Array(n);
for (let i = 0; i < n; i++) {
  const [l, r] = readInts();
  L[i] = l;
  R[i] = r;
}
L.sort();
R.sort();
// dua pointer: proses peristiwa selesai lebih dulu jika waktunya sama
let i = 0, j = 0, aktif = 0, ruang = 0;
while (i < n) {
  if (R[j] <= L[i]) { aktif--; j++; }
  else { aktif++; i++; if (aktif > ruang) ruang = aktif; }
}
console.log(String(ruang));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
L = sorted(map(int, data[1:1 + 2 * n:2]))
R = sorted(map(int, data[2:2 + 2 * n:2]))
i = j = aktif = ruang = 0
while i < n:
    if R[j] <= L[i]:          # ada rapat yang sudah selesai sebelum/saat rapat ini mulai
        aktif -= 1
        j += 1
    else:
        aktif += 1
        i += 1
        if aktif > ruang:
            ruang = aktif
print(ruang)
CODE,
        ],
        'editorial' => '<p><strong>Batas bawah.</strong> Jika pada suatu saat k rapat berlangsung bersamaan, jelas butuh paling sedikit k ruang.</p>
<p><strong>Cukup.</strong> Proses rapat berdasarkan waktu mulai, dan beri setiap rapat ruang mana pun yang sudah kosong (atau ruang baru jika tidak ada). Ruang baru hanya dibuka ketika semua ruang yang ada sedang terisi, yaitu ketika banyak rapat yang berlangsung bersamaan bertambah. Jadi banyak ruang = tumpukan maksimum.</p>
<p>Menghitung tumpukan maksimum: ubah setiap rapat menjadi peristiwa (l, +1) dan (r, −1), urutkan, lalu jumlahkan. Pada waktu yang sama, peristiwa selesai harus diproses lebih dulu karena selangnya setengah terbuka. Dengan <code>pair</code> di C++, −1 otomatis sebelum +1. Alternatifnya, urutkan semua l dan semua r terpisah lalu dua pointer. O(n log n).</p>',
        'hints' => [
            'Jika pada satu saat ada k rapat berlangsung, butuh paling sedikit k ruang. Apakah k ruang selalu cukup?',
            'Hitung banyak rapat yang berlangsung bersamaan dengan peristiwa: mulai +1, selesai −1, lalu urutkan.',
            'Hati-hati di waktu yang sama: rapat yang selesai pada r harus "keluar" sebelum rapat yang mulai pada r masuk.',
        ],
    ],

    [
        'slug' => 'tutup-jalan',
        'lesson' => 'greedy-interval',
        'title' => 'Lampu Penerang Jalan',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'interval', 'interval covering'],
        'statement' => '<p>Sebuah jalan lurus membentang dari titik 0 sampai titik <strong>L</strong>. Ada <strong>n</strong> lampu; lampu ke-i berada di titik <code>p<sub>i</sub></code> dan menerangi ruas <code>[p<sub>i</sub> − r<sub>i</sub>, p<sub>i</sub> + r<sub>i</sub>]</code>.</p>
<p>Nyalakan lampu sesedikit mungkin sehingga <strong>setiap titik</strong> di [0, L] diterangi paling sedikit satu lampu yang menyala. Cetak banyak lampu minimum, atau <code>-1</code> jika mustahil.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n L</code>. Setiap dari n baris berikutnya berisi <code>p<sub>i</sub> r<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak lampu minimum, atau -1.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>1 ≤ L ≤ 10<sup>9</sup></li><li>0 ≤ p<sub>i</sub> ≤ 10<sup>9</sup>, 1 ≤ r<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 10\n2 2\n5 1\n6 3\n9 2\n", 'explanation' => 'Lampu di 2 menerangi [0, 4], lampu di 6 menerangi [3, 9], lampu di 9 menerangi [7, 11]. Tiga lampu cukup; dua tidak.'],
            ['input' => "2 10\n2 2\n8 2\n", 'explanation' => 'Ruas (4, 6) tidak diterangi lampu mana pun.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $L, int $rMax) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = mt_rand(0, $L).' '.mt_rand(1, $rMax);
                }

                return "$n $L\n".implode("\n", $rows)."\n";
            };

            return [
                "1 1\n0 1\n", "1 10\n5 5\n", "2 4\n1 1\n3 1\n", "2 5\n1 1\n4 1\n", $gen(8, 30, 5), $gen(1000, 100000, 300),
                $gen(200000, 1000000000, 10000), $gen(200000, 1000000000, 2000), $gen(200000, 1000000000, 1000000000), $gen(200000, 1000000, 3),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $L] = T::ints($lines[0]);
            $iv = [];
            for ($i = 1; $i <= $n; $i++) {
                [$p, $r] = T::ints($lines[$i]);
                $iv[] = [$p - $r, $p + $r];
            }
            usort($iv, fn ($a, $b) => $a[0] <=> $b[0]);
            $cur = 0;
            $cnt = 0;
            $i = 0;
            while ($cur < $L) {
                $best = $cur;
                while ($i < $n && $iv[$i][0] <= $cur) {
                    $best = max($best, $iv[$i][1]);
                    $i++;
                }
                if ($best == $cur) {
                    return '-1';
                }
                $cur = $best;
                $cnt++;
            }

            return (string) $cnt;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long L;
    cin >> n >> L;
    vector<pair<long long, long long>> iv(n);      // ruas terang [kiri, kanan]
    for (auto& q : iv) {
        long long p, r;
        cin >> p >> r;
        q = {p - r, p + r};
    }
    // tutup [0, L] dari kiri ke kanan
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, L] = readInts();
const iv = [];
for (let i = 0; i < n; i++) {
  const [p, r] = readInts();
  iv.push([p - r, p + r]);
}
// tutup [0, L] dari kiri ke kanan
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, L = int(data[0]), int(data[1])
iv = [(int(data[2 + 2 * i]) - int(data[3 + 2 * i]), int(data[2 + 2 * i]) + int(data[3 + 2 * i])) for i in range(n)]
# tutup [0, L] dari kiri ke kanan
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
    long long L;
    cin >> n >> L;
    vector<pair<long long, long long>> iv(n);      // ruas terang [kiri, kanan]
    for (auto& q : iv) {
        long long p, r;
        cin >> p >> r;
        q = {p - r, p + r};
    }
    sort(iv.begin(), iv.end());                    // urut berdasarkan ujung kiri

    long long tertutup = 0;                        // [0, tertutup] sudah terang
    int lampu = 0, i = 0;
    while (tertutup < L) {
        long long terjauh = tertutup;
        while (i < n && iv[i].first <= tertutup)    // lampu yang menyambung dengan bagian terang
            terjauh = max(terjauh, iv[i++].second);
        if (terjauh == tertutup) {                 // tidak ada yang memperpanjang: ada celah
            cout << -1 << '\n';
            return 0;
        }
        tertutup = terjauh;                        // nyalakan yang menjangkau paling jauh
        lampu++;
    }
    cout << lampu << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, L] = readInts();
const iv = [];
for (let i = 0; i < n; i++) {
  const [p, r] = readInts();
  iv.push([p - r, p + r]);
}
iv.sort((a, b) => a[0] - b[0]);
let tertutup = 0, lampu = 0, i = 0, gagal = false;
while (tertutup < L) {
  let terjauh = tertutup;
  while (i < n && iv[i][0] <= tertutup) terjauh = Math.max(terjauh, iv[i++][1]);
  if (terjauh === tertutup) { gagal = true; break; }
  tertutup = terjauh;
  lampu++;
}
console.log(gagal ? "-1" : String(lampu));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, L = int(data[0]), int(data[1])
iv = sorted((int(data[2 + 2 * i]) - int(data[3 + 2 * i]), int(data[2 + 2 * i]) + int(data[3 + 2 * i])) for i in range(n))
tertutup, lampu, i = 0, 0, 0
gagal = False
while tertutup < L:
    terjauh = tertutup
    while i < n and iv[i][0] <= tertutup:
        if iv[i][1] > terjauh:
            terjauh = iv[i][1]
        i += 1
    if terjauh == tertutup:
        gagal = True
        break
    tertutup = terjauh
    lampu += 1
print(-1 if gagal else lampu)
CODE,
        ],
        'editorial' => '<p>Setiap lampu adalah interval [p − r, p + r]. Kita ingin menutup ruas [0, L] dengan interval sesedikit mungkin: soal <em>interval covering</em>.</p>
<p>Simpan "bagian yang sudah terang" sebagai [0, tertutup]. Di antara semua lampu yang ujung kirinya ≤ tertutup (yang menyambung, termasuk menyentuh di satu titik karena ruasnya tertutup), nyalakan yang ujung kanannya paling jauh. Jika tidak ada yang melampaui <code>tertutup</code>, ada titik tepat setelahnya yang tidak bisa diterangi: −1.</p>
<p><strong>Mengapa terjauh?</strong> Solusi optimal mana pun juga harus menyalakan salah satu lampu yang menyambung. Menggantinya dengan lampu yang menjangkau paling jauh hanya memperbesar bagian yang terang, jadi tidak pernah membutuhkan lampu tambahan. Setelah diurutkan berdasarkan ujung kiri, setiap lampu diperiksa sekali: O(n log n).</p>',
        'hints' => [
            'Ubah setiap lampu menjadi ruas [p − r, p + r]. Kamu perlu menutup [0, L].',
            'Mulai dari titik 0. Di antara ruas yang sudah menyambung dengan bagian terang, mana yang paling berguna?',
            'Urutkan berdasarkan ujung kiri. Berulang kali ambil ruas yang menyambung dan menjangkau paling jauh; jika tidak ada kemajuan, cetak −1.',
        ],
    ],

    // ───────────────────────── Greedy Rasio & Huffman ─────────────────────────
    [
        'slug' => 'karung-rempah',
        'lesson' => 'greedy-rasio',
        'title' => 'Karung Rempah',
        'difficulty' => 'Mudah',
        'tags' => ['greedy', 'fractional knapsack', 'rasio'],
        'statement' => '<p>Seorang pedagang membawa karung berkapasitas <strong>W</strong> kilogram. Di pasar ada <strong>n</strong> jenis rempah; jenis ke-i tersedia <code>w<sub>i</sub></code> kilogram dengan nilai total <code>v<sub>i</sub></code> (rempah bisa diambil sebagian dan nilainya sebanding dengan beratnya).</p>
<p>Berapa nilai maksimum yang bisa dibawa? Karena jawabannya bisa berupa pecahan, cetak <strong>bagian bulatnya</strong> (dibulatkan ke bawah).</p>',
        'input_format' => '<p>Baris pertama berisi <code>n W</code>. Setiap dari n baris berikutnya berisi <code>w<sub>i</sub> v<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: bagian bulat dari nilai maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>1 ≤ W ≤ 10<sup>12</sup></li><li>1 ≤ w<sub>i</sub>, v<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3 50\n10 60\n20 100\n30 120\n", 'explanation' => 'Rasio nilai/berat: 6, 5, 4. Ambil semua jenis 1 (10 kg, 60) dan jenis 2 (20 kg, 100), lalu 20 kg dari jenis 3 bernilai 120 × 20/30 = 80. Total 240.'],
            ['input' => "1 1\n3 10\n", 'explanation' => 'Hanya 1/3 bagian yang muat: 10/3 = 3,33…, dicetak 3.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $W, int $hi) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = mt_rand(1, $hi).' '.mt_rand(1, $hi);
                }

                return "$n $W\n".implode("\n", $rows)."\n";
            };

            return [
                "1 1000000000000\n1000000 1000000\n", "2 7\n3 3\n3 3\n", $gen(6, 15, 10), $gen(1000, 30000, 100), $gen(200000, 1000000000, 1000000),
                $gen(200000, 1000000000000, 1000000), $gen(200000, 12345678, 1000000), $gen(200000, 999, 10), $gen(150000, 77777777, 1000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $W] = T::ints($lines[0]);
            $it = [];
            for ($i = 1; $i <= $n; $i++) {
                $it[] = T::ints($lines[$i]);
            }
            usort($it, fn ($a, $b) => $b[1] * $a[0] <=> $a[1] * $b[0]);   // v/w menurun
            $sisa = $W;
            $total = 0;
            foreach ($it as $x) {
                if ($x[0] <= $sisa) {
                    $sisa -= $x[0];
                    $total += $x[1];
                } else {
                    $total += intdiv($x[1] * $sisa, $x[0]);
                    break;
                }
            }

            return (string) $total;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long W;
    cin >> n >> W;
    vector<long long> w(n), v(n);
    for (int i = 0; i < n; i++) cin >> w[i] >> v[i];

    // urutkan berdasarkan v/w menurun (bandingkan dengan perkalian silang)
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, W] = readInts();
const it = [];
for (let i = 0; i < n; i++) it.push(readInts());   // [w, v]
// urutkan berdasarkan v/w menurun (bandingkan dengan perkalian silang)
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, W = int(data[0]), int(data[1])
it = [(int(data[2 + 2 * i]), int(data[3 + 2 * i])) for i in range(n)]   # (w, v)
# urutkan berdasarkan v/w menurun (bandingkan dengan perkalian silang)
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
    long long W;
    cin >> n >> W;
    vector<long long> w(n), v(n);
    for (int i = 0; i < n; i++) cin >> w[i] >> v[i];

    vector<int> id(n);
    iota(id.begin(), id.end(), 0);
    sort(id.begin(), id.end(), [&](int i, int j) {
        return v[i] * w[j] > v[j] * w[i];        // v[i]/w[i] > v[j]/w[j]
    });

    long long sisa = W, total = 0;
    for (int i : id) {
        if (w[i] <= sisa) {                       // ambil utuh
            sisa -= w[i];
            total += v[i];
        } else {                                  // ambil sebagian: v[i] * sisa / w[i]
            total += v[i] * sisa / w[i];          // pembagian bulat = bagian bulat, karena sisanya < 1
            break;
        }
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, W] = readInts();
const it = [];
for (let i = 0; i < n; i++) it.push(readInts());   // [w, v]
it.sort((a, b) => b[1] * a[0] - a[1] * b[0]);       // v/w menurun
let sisa = W, total = 0;
for (const [w, v] of it) {
  if (w <= sisa) {
    sisa -= w;
    total += v;
  } else {
    total += Math.floor((v * sisa) / w);
    break;
  }
}
console.log(String(total));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
n, W = int(data[0]), int(data[1])
it = [(int(data[2 + 2 * i]), int(data[3 + 2 * i])) for i in range(n)]   # (w, v)
# kunci bulat ⌊v·10^13 / w⌋: dua rasio berbeda (selisih ≥ 10^-12) pasti punya kunci berbeda
it.sort(key=lambda t: -(t[1] * 10**13 // t[0]))
sisa, total = W, 0
for w, v in it:
    if w <= sisa:
        sisa -= w
        total += v
    else:
        total += v * sisa // w
        break
print(total)
CODE,
        ],
        'editorial' => '<p>Ini <strong>fractional knapsack</strong>. Urutkan rempah berdasarkan nilai per kilogram (v/w) menurun. Ambil utuh selama muat; rempah pertama yang tidak muat diambil sebagian untuk memenuhi karung, lalu berhenti.</p>
<p><strong>Bukti singkat.</strong> Jika solusi optimal memakai x kg rempah berasio lebih rendah sementara ada rempah berasio lebih tinggi yang belum habis, tukar sebagian kecil: nilainya naik atau tetap. Jadi solusi optimal mengisi karung dengan rasio tertinggi lebih dulu.</p>
<p><strong>Tanpa pecahan.</strong> Bandingkan v<sub>i</sub>/w<sub>i</sub> dengan v<sub>i</sub>·w<sub>j</sub> &gt; v<sub>j</sub>·w<sub>i</sub> (paling besar 10<sup>12</sup>, aman di <code>long long</code>). Bagian sebagian bernilai v·sisa/w dengan sisa &lt; w, jadi bagian bulat jawabannya adalah total utuh + ⌊v·sisa/w⌋.</p>',
        'hints' => [
            'Rempah boleh diambil sebagian. Rempah mana yang paling "menguntungkan" per kilogram?',
            'Urutkan berdasarkan v/w menurun dan isi karung dari yang terbaik. Bandingkan pecahan dengan perkalian silang.',
            'Rempah pertama yang tidak muat diambil sebagian: tambahkan ⌊v·sisa/w⌋ lalu berhenti.',
        ],
    ],

    [
        'slug' => 'gabung-tali',
        'lesson' => 'greedy-rasio',
        'title' => 'Menyambung Tali',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'huffman', 'priority queue'],
        'statement' => '<p>Ada <strong>n</strong> potong tali dengan panjang <code>l<sub>1</sub>, …, l<sub>n</sub></code>. Kamu ingin menyambung semuanya menjadi satu tali. Setiap kali, kamu memilih dua tali dan menyambungnya; biaya satu penyambungan sama dengan <strong>jumlah panjang</strong> kedua tali itu.</p>
<p>Berapa total biaya minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Baris kedua berisi <code>l<sub>1</sub> … l<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: total biaya minimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>1 ≤ l<sub>i</sub> ≤ 10<sup>8</sup></li></ul>',
        'samples' => [
            ['input' => "4\n2 3 4 6\n", 'explanation' => 'Sambung 2 + 3 = 5 (biaya 5), lalu 4 + 5 = 9 (biaya 9), lalu 6 + 9 = 15 (biaya 15). Total 29.'],
            ['input' => "1\n100\n", 'explanation' => 'Sudah satu tali: biaya 0.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };
            $pangkat = [];
            for ($i = 0; $i < 25; $i++) {
                $pangkat[] = 1 << $i;
            }

            return [
                "2\n1 1\n", "25\n".T::join($pangkat)."\n", $gen(8, 20), $gen(1000, 1000), $gen(200000, 100000000), $gen(200000, 1),
                $gen(200000, 1000), $gen(199999, 100000000),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: dua antrean (array terurut + antrean hasil gabungan yang juga terurut)
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            sort($a);
            $b = [];
            $i = 0;
            $j = 0;
            $n = count($a);
            $cost = 0;
            $ambil = function () use (&$a, &$b, &$i, &$j, $n) {
                if ($j >= count($b) || ($i < $n && $a[$i] <= $b[$j])) {
                    return $a[$i++];
                }

                return $b[$j++];
            };
            for ($k = 1; $k < $n; $k++) {
                $x = $ambil();
                $y = $ambil();
                $cost += $x + $y;
                $b[] = $x + $y;
            }

            return (string) $cost;
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
    vector<long long> l(n);
    for (auto& v : l) cin >> v;

    // tali yang disambung lebih awal akan ikut "dibayar" berkali-kali
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const l = readInts();
// tali yang disambung lebih awal akan ikut "dibayar" berkali-kali
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
l = list(map(int, data[1:1 + n]))
# tali yang disambung lebih awal akan ikut "dibayar" berkali-kali
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
    priority_queue<long long, vector<long long>, greater<long long>> pq;   // min-heap
    for (int i = 0; i < n; i++) {
        long long v;
        cin >> v;
        pq.push(v);
    }
    long long biaya = 0;
    while (pq.size() > 1) {
        long long x = pq.top(); pq.pop();     // dua tali terpendek
        long long y = pq.top(); pq.pop();
        biaya += x + y;
        pq.push(x + y);
    }
    cout << biaya << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Dua antrean: tali awal terurut + hasil sambungan (yang otomatis terurut naik)
const n = readInts()[0];
const a = Float64Array.from(readInts()).sort();
const b = new Float64Array(n);
let i = 0, j = 0, bl = 0, biaya = 0;
const ambil = () => (j >= bl || (i < n && a[i] <= b[j]) ? a[i++] : b[j++]);
for (let k = 1; k < n; k++) {
  const x = ambil(), y = ambil();
  biaya += x + y;
  b[bl++] = x + y;
}
console.log(String(biaya));
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n = int(data[0])
pq = list(map(int, data[1:1 + n]))
heapq.heapify(pq)
biaya = 0
while len(pq) > 1:
    x = heapq.heappop(pq)
    y = heapq.heappop(pq)
    biaya += x + y
    heapq.heappush(pq, x + y)
print(biaya)
CODE,
        ],
        'editorial' => '<p>Gambarkan proses penyambungan sebagai pohon biner: daun adalah tali awal, dan setiap simpul dalam adalah satu penyambungan. Tali awal dengan kedalaman d ikut terhitung dalam d penyambungan, jadi total biaya = Σ l<sub>i</sub>·kedalaman(i). Ini persis biaya pohon <strong>Huffman</strong>.</p>
<p>Greedy Huffman: selalu sambung <strong>dua tali terpendek</strong>. Dengan min-heap, O(n log n).</p>
<p>Alternatif tanpa heap: hasil sambungan muncul dengan panjang yang tidak pernah turun, jadi cukup dua antrean (tali awal yang sudah diurutkan, dan antrean hasil sambungan) dan ambil yang terkecil dari depan keduanya. Biaya total sampai sekitar 4·10<sup>14</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Tali yang disambung paling awal ikut dihitung lagi di setiap penyambungan berikutnya. Tali mana yang sebaiknya disambung lebih dulu?',
            'Selalu sambung dua tali terpendek yang ada saat itu (Huffman).',
            'Pakai min-heap: ambil dua teratas, tambahkan jumlahnya ke biaya, masukkan kembali jumlahnya.',
        ],
    ],

    [
        'slug' => 'gabung-k-berkas',
        'lesson' => 'greedy-rasio',
        'title' => 'Menggabung Berkas Sekaligus',
        'difficulty' => 'Sulit',
        'tags' => ['greedy', 'huffman k-ary', 'priority queue'],
        'statement' => '<p>Ada <strong>n</strong> berkas terurut dengan ukuran <code>s<sub>1</sub>, …, s<sub>n</sub></code>. Program penggabung bisa menggabung <strong>antara 2 sampai k berkas sekaligus</strong> menjadi satu berkas; biayanya sama dengan total ukuran berkas-berkas yang digabung.</p>
<p>Gabungkan semua berkas menjadi satu dengan total biaya minimum.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n k</code>. Baris kedua berisi <code>s<sub>1</sub> … s<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: total biaya minimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>2 ≤ k ≤ 200 000</li><li>1 ≤ s<sub>i</sub> ≤ 10<sup>8</sup></li></ul>',
        'samples' => [
            ['input' => "4 3\n1 2 3 4\n", 'explanation' => 'Gabung 1 dan 2 dulu (biaya 3), lalu 3, 3, 4 sekaligus (biaya 10): total 13. Menggabung 1, 2, 3 dulu (6) lalu 6 dan 4 (10) memberi 16.'],
            ['input' => "5 3\n1 1 1 1 1\n", 'explanation' => 'Gabung tiga berkas (3), lalu 3, 1, 1 (5): total 8.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $k, int $hi) {
                return "$n $k\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1 2\n5\n", "2 5\n3 4\n", "6 4\n1 1 1 1 1 1\n", $gen(7, 3, 10), $gen(9, 4, 20), $gen(1000, 5, 1000), $gen(200000, 2, 100000000),
                $gen(200000, 3, 100000000), $gen(200000, 1000, 100000000), $gen(200000, 200000, 100000000), $gen(199998, 7, 1000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            if ($n == 1) {
                return '0';
            }
            while ((count($a) - 1) % ($k - 1) != 0) {
                $a[] = 0;
            }
            sort($a);
            $h = new SplMinHeap;
            foreach ($a as $x) {
                $h->insert($x);
            }
            $cost = 0;
            while ($h->count() > 1) {
                $s = 0;
                for ($t = 0; $t < $k; $t++) {
                    $s += $h->extract();
                }
                $cost += $s;
                $h->insert($s);
            }

            return (string) $cost;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> s(n);
    for (auto& v : s) cin >> v;

    // Huffman dengan k anak. Petunjuk: tambahkan berkas berukuran 0.
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const s = readInts();
// Huffman dengan k anak. Petunjuk: tambahkan berkas berukuran 0.
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, k = int(data[0]), int(data[1])
s = list(map(int, data[2:2 + n]))
# Huffman dengan k anak. Petunjuk: tambahkan berkas berukuran 0.
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> s(n);
    for (auto& v : s) cin >> v;
    if (n == 1) {
        cout << 0 << '\n';
        return 0;
    }
    // Setiap penggabungan k berkas mengurangi banyak berkas sebanyak k-1.
    // Tambahkan berkas 0 sampai (banyak - 1) habis dibagi (k - 1).
    while ((s.size() - 1) % (k - 1) != 0) s.push_back(0);

    // Dua antrean: s terurut, dan hasil gabungan (yang muncul dengan ukuran naik)
    sort(s.begin(), s.end());
    vector<long long> g;
    size_t i = 0, j = 0;
    long long biaya = 0;
    size_t sisa = s.size();
    while (sisa > 1) {
        long long jumlah = 0;
        for (int t = 0; t < k; t++) {
            if (j >= g.size() || (i < s.size() && s[i] <= g[j])) jumlah += s[i++];
            else jumlah += g[j++];
        }
        biaya += jumlah;
        g.push_back(jumlah);
        sisa -= k - 1;
    }
    cout << biaya << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const s0 = readInts();
if (n === 1) {
  console.log("0");
} else {
  let m = n;
  while ((m - 1) % (k - 1) !== 0) m++;
  const s = new Float64Array(m);
  for (let i = 0; i < n; i++) s[i] = s0[i];
  s.sort();
  const g = [];
  let i = 0, j = 0, biaya = 0, sisa = m;
  while (sisa > 1) {
    let jumlah = 0;
    for (let t = 0; t < k; t++) {
      if (j >= g.length || (i < m && s[i] <= g[j])) jumlah += s[i++];
      else jumlah += g[j++];
    }
    biaya += jumlah;
    g.push(jumlah);
    sisa -= k - 1;
  }
  console.log(String(biaya));
}
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n, k = int(data[0]), int(data[1])
s = list(map(int, data[2:2 + n]))
if n == 1:
    print(0)
else:
    while (len(s) - 1) % (k - 1):
        s.append(0)
    heapq.heapify(s)
    biaya = 0
    while len(s) > 1:
        jumlah = 0
        for _ in range(k):
            jumlah += heapq.heappop(s)
        biaya += jumlah
        heapq.heappush(s, jumlah)
    print(biaya)
CODE,
        ],
        'editorial' => '<p>Seperti tali, proses penggabungan membentuk pohon, kini dengan paling banyak k anak per simpul, dan biayanya Σ s<sub>i</sub>·kedalaman(i). Greedy Huffman k-ary: selalu gabung k berkas terkecil.</p>
<p><strong>Jebakan:</strong> setiap penggabungan k berkas mengurangi banyak berkas sebanyak k − 1. Jika (n − 1) tidak habis dibagi (k − 1), penggabungan terakhir hanya berisi sedikit berkas di <em>puncak</em> pohon, padahal "slot kosong" sebaiknya ada di dasar pohon (di penggabungan pertama, untuk berkas terkecil). Solusinya: tambahkan berkas berukuran 0 sampai (n − 1) habis dibagi (k − 1), lalu selalu gabung tepat k terkecil. Berkas 0 tidak menambah biaya, dan penggabungan pertama otomatis berisi lebih sedikit berkas sungguhan.</p>
<p>Dengan heap butuh O(n log n), tetapi untuk k besar sebaiknya pakai dua antrean (berkas terurut + hasil gabungan yang naik) agar tiap pengambilan O(1).</p>',
        'hints' => [
            'Ini Huffman dengan k anak: gabungkan k berkas terkecil setiap kali. Tetapi coba n = 4, k = 3 dengan tangan: apa yang salah jika langsung menggabung 3 terkecil?',
            'Setiap penggabungan mengurangi banyak berkas sebanyak k − 1. Penggabungan yang "tidak penuh" sebaiknya terjadi paling awal, bukan paling akhir.',
            'Tambahkan berkas berukuran 0 sampai (n − 1) habis dibagi (k − 1), lalu selalu gabung tepat k berkas terkecil.',
        ],
    ],

    // ───────────────────────── Greedy dengan Priority Queue ─────────────────────────
    [
        'slug' => 'kerja-tenggat',
        'lesson' => 'greedy-pq',
        'title' => 'Pekerjaan Lepas Berbayar',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'priority queue', 'penjadwalan'],
        'statement' => '<p>Seorang pekerja lepas mendapat tawaran <strong>n</strong> pekerjaan. Setiap pekerjaan butuh tepat 1 hari. Pekerjaan ke-i dibayar <code>p<sub>i</sub></code> jika diselesaikan paling lambat pada hari ke-<code>d<sub>i</sub></code> (hari dimulai dari 1). Dalam satu hari ia hanya bisa mengerjakan satu pekerjaan.</p>
<p>Berapa total bayaran maksimum yang bisa ia peroleh?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi <code>d<sub>i</sub> p<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: total bayaran maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>1 ≤ d<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ p<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n2 100\n1 19\n2 27\n1 25\n3 15\n", 'explanation' => 'Hari 1: pekerjaan bayaran 27, hari 2: 100, hari 3: 15. Total 142. Pekerjaan dengan tenggat 1 hanya bisa satu, dan lebih baik hari 1 dipakai untuk 27.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $dMax, int $pMax) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = mt_rand(1, $dMax).' '.mt_rand(1, $pMax);
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n1 5\n", "3\n1 5\n1 7\n1 6\n", $gen(8, 4, 50), $gen(1000, 100, 1000), $gen(200000, 1000000000, 1000000000),
                $gen(200000, 1000, 1000000000), $gen(200000, 50000, 1000), $gen(200000, 1, 1000000000), $gen(200000, 200000, 7),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: urut bayaran menurun, taruh di hari kosong terakhir ≤ tenggat (DSU)
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $job = [];
            for ($i = 1; $i <= $n; $i++) {
                $job[] = T::ints($lines[$i]);
            }
            usort($job, fn ($a, $b) => $b[1] <=> $a[1]);
            $par = range(0, $n);
            $find = function (int $x) use (&$par) {
                $r = $x;
                while ($par[$r] != $r) {
                    $r = $par[$r];
                }
                while ($par[$x] != $r) {
                    $nx = $par[$x];
                    $par[$x] = $r;
                    $x = $nx;
                }

                return $r;
            };
            $total = 0;
            foreach ($job as $j) {
                $day = $find(min($j[0], $n));
                if ($day > 0) {
                    $total += $j[1];
                    $par[$day] = $day - 1;
                }
            }

            return (string) $total;
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
    vector<pair<long long, long long>> job(n);   // (tenggat, bayaran)
    for (auto& j : job) cin >> j.first >> j.second;

    // proses berdasarkan tenggat; simpan bayaran pekerjaan yang diambil di min-heap
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const job = [];
for (let i = 0; i < n; i++) job.push(readInts());   // [tenggat, bayaran]
// proses berdasarkan tenggat; simpan bayaran pekerjaan yang diambil di min-heap
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n = int(data[0])
job = sorted(zip(map(int, data[1:1 + 2 * n:2]), map(int, data[2:2 + 2 * n:2])))   # (tenggat, bayaran)
# proses berdasarkan tenggat; simpan bayaran pekerjaan yang diambil di min-heap
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
    vector<pair<long long, long long>> job(n);   // (tenggat, bayaran)
    for (auto& j : job) cin >> j.first >> j.second;
    sort(job.begin(), job.end());                // tenggat paling awal dulu

    priority_queue<long long, vector<long long>, greater<long long>> pq;   // bayaran terkecil di atas
    long long total = 0;
    for (auto& j : job) {
        pq.push(j.second);                       // ambil dulu
        total += j.second;
        if ((long long)pq.size() > j.first) {    // lebih banyak pekerjaan daripada hari yang tersedia
            total -= pq.top();                   // menyesal: buang yang bayarannya terkecil
            pq.pop();
        }
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const job = [];
for (let i = 0; i < n; i++) job.push(readInts());
job.sort((a, b) => a[0] - b[0]);
// min-heap sederhana
const h = [];
const push = (v) => {
  h.push(v);
  let i = h.length - 1;
  while (i > 0) {
    const p = (i - 1) >> 1;
    if (h[p] <= h[i]) break;
    [h[p], h[i]] = [h[i], h[p]];
    i = p;
  }
};
const pop = () => {
  const top = h[0], last = h.pop();
  if (h.length) {
    h[0] = last;
    let i = 0;
    for (;;) {
      const l = 2 * i + 1, r = l + 1;
      let m = i;
      if (l < h.length && h[l] < h[m]) m = l;
      if (r < h.length && h[r] < h[m]) m = r;
      if (m === i) break;
      [h[m], h[i]] = [h[i], h[m]];
      i = m;
    }
  }
  return top;
};
let total = 0;
for (const [d, p] of job) {
  push(p);
  total += p;
  if (h.length > d) total -= pop();
}
console.log(String(total));
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n = int(data[0])
job = sorted(zip(map(int, data[1:1 + 2 * n:2]), map(int, data[2:2 + 2 * n:2])))
pq, total = [], 0
for d, p in job:
    heapq.heappush(pq, p)
    total += p
    if len(pq) > d:
        total -= heapq.heappop(pq)
print(total)
CODE,
        ],
        'editorial' => '<p>Himpunan pekerjaan bisa dijadwalkan jika, setelah diurutkan berdasarkan tenggat, pekerjaan ke-j (1-based) punya tenggat ≥ j. Jadi syaratnya: untuk setiap t, banyak pekerjaan terpilih dengan tenggat ≤ t tidak melebihi t.</p>
<p><strong>Regret greedy.</strong> Proses pekerjaan berdasarkan tenggat naik dan langsung ambil. Jika banyak pekerjaan yang diambil melebihi tenggat saat ini, syarat di atas dilanggar: buang pekerjaan dengan bayaran terkecil (min-heap). Membuang yang termurah mempertahankan total terbesar di antara semua himpunan yang sah untuk prefiks tersebut.</p>
<p>Alternatif: urutkan bayaran menurun dan taruh setiap pekerjaan di hari kosong terakhir sebelum tenggatnya (DSU untuk mencari hari kosong). Tenggat sampai 10<sup>9</sup> tidak masalah: hari setelah hari ke-n tidak pernah dibutuhkan.</p>',
        'hints' => [
            'Jika kamu sudah memilih himpunan pekerjaan, urutan terbaik untuk mengerjakannya selalu berdasarkan tenggat.',
            'Proses pekerjaan berdasarkan tenggat naik dan ambil semuanya, tetapi simpan bayarannya di min-heap.',
            'Jika banyak pekerjaan yang diambil melebihi tenggat saat ini, buang yang bayarannya paling kecil.',
        ],
    ],

    [
        'slug' => 'stasiun-bensin',
        'lesson' => 'greedy-pq',
        'title' => 'Perjalanan Mobil Tua',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'priority queue'],
        'statement' => '<p>Sebuah mobil tua ingin menempuh jalan lurus dari titik 0 ke titik <strong>T</strong>. Mobil memakai 1 liter bensin per kilometer dan awalnya berisi <strong>F</strong> liter. Tangkinya sangat besar sehingga tidak pernah penuh.</p>
<p>Di sepanjang jalan ada <strong>n</strong> pom bensin. Pom ke-i ada di titik <code>x<sub>i</sub></code> dan menyediakan <code>b<sub>i</sub></code> liter; jika mobil berhenti di sana, ia mengambil semuanya. Berapa kali minimum mobil harus berhenti untuk mengisi bensin agar sampai di T? Cetak <code>-1</code> jika mustahil.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n T F</code>. Setiap dari n baris berikutnya berisi <code>x<sub>i</sub> b<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak pengisian minimum, atau -1.</p>',
        'constraints' => '<ul><li>0 ≤ n ≤ 200 000</li><li>1 ≤ T, F ≤ 10<sup>9</sup></li><li>0 &lt; x<sub>i</sub> &lt; T, 1 ≤ b<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 100 10\n10 60\n20 30\n30 30\n60 40\n", 'explanation' => 'Isi di titik 10 (bensin cukup sampai 70), lalu di titik 60 (cukup sampai 110). Dua kali.'],
            ['input' => "1 100 1\n10 100\n", 'explanation' => 'Bensin habis di titik 1, sebelum mencapai pom di titik 10.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $T, int $F, int $bMax) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = mt_rand(1, $T - 1).' '.mt_rand(1, $bMax);
                }

                return "$n $T $F\n".implode("\n", $rows).($n ? "\n" : '');
            };

            return [
                "0 5 10\n", "0 10 5\n", "2 10 5\n5 5\n6 100\n", $gen(8, 50, 10, 15), $gen(1000, 100000, 500, 500),
                $gen(200000, 1000000000, 100000, 10000), $gen(200000, 1000000000, 1000, 1000000000), $gen(200000, 1000000000, 5000, 9000),
                $gen(200000, 1000000, 10, 20),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $T, $F] = T::ints($lines[0]);
            $st = [];
            for ($i = 1; $i <= $n; $i++) {
                $st[] = T::ints($lines[$i]);
            }
            usort($st, fn ($a, $b) => $a[0] <=> $b[0]);
            $h = new SplMaxHeap;
            $reach = $F;
            $isi = 0;
            $i = 0;
            while ($reach < $T) {
                while ($i < $n && $st[$i][0] <= $reach) {
                    $h->insert($st[$i][1]);
                    $i++;
                }
                if ($h->isEmpty()) {
                    return '-1';
                }
                $reach += $h->extract();
                $isi++;
            }

            return (string) $isi;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long T, F;
    cin >> n >> T >> F;
    vector<pair<long long, long long>> pom(n);   // (posisi, liter)
    for (auto& p : pom) cin >> p.first >> p.second;

    // jangan memutuskan di setiap pom: catat dulu, isi saat terpaksa
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, T, F] = readInts();
const pom = [];
for (let i = 0; i < n; i++) pom.push(readInts());   // [posisi, liter]
// jangan memutuskan di setiap pom: catat dulu, isi saat terpaksa
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n, T, F = int(data[0]), int(data[1]), int(data[2])
pom = sorted(zip(map(int, data[3:3 + 2 * n:2]), map(int, data[4:4 + 2 * n:2])))
# jangan memutuskan di setiap pom: catat dulu, isi saat terpaksa
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
    long long T, F;
    cin >> n >> T >> F;
    vector<pair<long long, long long>> pom(n);   // (posisi, liter)
    for (auto& p : pom) cin >> p.first >> p.second;
    sort(pom.begin(), pom.end());

    priority_queue<long long> lewat;             // liter di pom yang sudah bisa dicapai
    long long jangkauan = F;                     // titik terjauh yang bisa dicapai saat ini
    int isi = 0, i = 0;
    while (jangkauan < T) {
        while (i < n && pom[i].first <= jangkauan) lewat.push(pom[i++].second);
        if (lewat.empty()) {                     // terdampar sebelum pom berikutnya
            cout << -1 << '\n';
            return 0;
        }
        jangkauan += lewat.top();                // "seharusnya" mengisi di pom terbesar yang terlewati
        lewat.pop();
        isi++;
    }
    cout << isi << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, T, F] = readInts();
const pom = [];
for (let i = 0; i < n; i++) pom.push(readInts());
pom.sort((a, b) => a[0] - b[0]);
// max-heap sederhana
const h = [];
const push = (v) => {
  h.push(v);
  let i = h.length - 1;
  while (i > 0) {
    const p = (i - 1) >> 1;
    if (h[p] >= h[i]) break;
    [h[p], h[i]] = [h[i], h[p]];
    i = p;
  }
};
const pop = () => {
  const top = h[0], last = h.pop();
  if (h.length) {
    h[0] = last;
    let i = 0;
    for (;;) {
      const l = 2 * i + 1, r = l + 1;
      let m = i;
      if (l < h.length && h[l] > h[m]) m = l;
      if (r < h.length && h[r] > h[m]) m = r;
      if (m === i) break;
      [h[m], h[i]] = [h[i], h[m]];
      i = m;
    }
  }
  return top;
};
let jangkauan = F, isi = 0, i = 0, gagal = false;
while (jangkauan < T) {
  while (i < n && pom[i][0] <= jangkauan) push(pom[i++][1]);
  if (!h.length) { gagal = true; break; }
  jangkauan += pop();
  isi++;
}
console.log(gagal ? "-1" : String(isi));
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n, T, F = int(data[0]), int(data[1]), int(data[2])
pom = sorted(zip(map(int, data[3:3 + 2 * n:2]), map(int, data[4:4 + 2 * n:2])))
lewat = []
jangkauan, isi, i = F, 0, 0
gagal = False
while jangkauan < T:
    while i < n and pom[i][0] <= jangkauan:
        heapq.heappush(lewat, -pom[i][1])
        i += 1
    if not lewat:
        gagal = True
        break
    jangkauan -= heapq.heappop(lewat)
    isi += 1
print(-1 if gagal else isi)
CODE,
        ],
        'editorial' => '<p>Keputusan "isi di pom ini atau tidak" sulit diambil saat melewatinya. Trik regret: <strong>jangan memutuskan dulu</strong>. Jalankan mobil sejauh bensinnya, dan catat semua pom yang terlewati di max-heap.</p>
<p>Saat mobil tidak bisa maju lagi (jangkauan &lt; T dan pom berikutnya di luar jangkauan), kita tahu harus mengisi di salah satu pom yang terlewati. Pilih yang literanya terbesar: banyak pengisian bertambah satu apa pun pilihannya, dan liter terbesar memberi jangkauan terjauh, yang hanya membuka lebih banyak pilihan. Jika heap kosong, mobil terdampar: −1.</p>
<p>Setiap pom masuk dan keluar heap paling banyak sekali: O(n log n). Jangkauan bisa mencapai 2·10<sup>14</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Daripada memutuskan di setiap pom, bayangkan kamu bisa "mundur" dan mengisi di pom mana pun yang sudah kamu lewati.',
            'Kapan kamu benar-benar terpaksa mengisi? Saat itu, pom terlewati mana yang paling menguntungkan?',
            'Max-heap liter dari pom yang posisinya ≤ jangkauan. Selama jangkauan < T, ambil yang terbesar; jika heap kosong, −1.',
        ],
    ],

    [
        'slug' => 'jual-beli-saham',
        'lesson' => 'greedy-pq',
        'title' => 'Jual Beli Saham Harian',
        'difficulty' => 'Sulit',
        'tags' => ['greedy', 'priority queue', 'regret'],
        'statement' => '<p>Kamu tahu harga sebuah saham untuk <strong>n</strong> hari ke depan: <code>p<sub>1</sub>, …, p<sub>n</sub></code>. Setiap hari kamu boleh melakukan <strong>tepat satu</strong> dari tiga hal: membeli satu lembar saham, menjual satu lembar saham yang kamu punya, atau tidak melakukan apa-apa.</p>
<p>Awalnya kamu tidak punya saham, dan di akhir hari ke-n kamu juga tidak boleh punya saham. Berapa keuntungan maksimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Baris kedua berisi <code>p<sub>1</sub> … p<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: keuntungan maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 300 000</li><li>1 ≤ p<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "9\n10 5 4 7 9 12 6 2 10\n", 'explanation' => 'Beli di 5 dan 4, jual di 9 dan 12, lalu beli di 2 dan jual di 10: −5 − 4 + 9 + 12 − 2 + 10 = 20.'],
            ['input' => "3\n5 4 3\n", 'explanation' => 'Harga terus turun: jangan bertransaksi.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };
            $naik = range(1, 300000);

            return [
                "1\n5\n", "2\n1 1000000\n", $gen(8, 10), $gen(12, 6), $gen(1000, 1000), $gen(300000, 1000000), $gen(300000, 3),
                "300000\n".T::join($naik)."\n", "300000\n".T::join(array_reverse($naik))."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $p = T::ints($lines[1]);
            $h = new SplMinHeap;
            $ans = 0;
            foreach ($p as $x) {
                if (! $h->isEmpty() && $h->top() < $x) {
                    $ans += $x - $h->extract();
                    $h->insert($x);
                }
                $h->insert($x);
            }

            return (string) $ans;
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
    vector<long long> p(n);
    for (auto& v : p) cin >> v;

    // min-heap berisi harga "calon hari beli"
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const p = readInts();
// min-heap berisi harga "calon hari beli"
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n = int(data[0])
p = list(map(int, data[1:1 + n]))
# min-heap berisi harga "calon hari beli"
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
    priority_queue<long long, vector<long long>, greater<long long>> pq;   // calon hari beli
    long long untung = 0;
    for (int i = 0; i < n; i++) {
        long long x;
        cin >> x;
        if (!pq.empty() && pq.top() < x) {
            untung += x - pq.top();     // jual hari ini, beli di hari termurah yang tersedia
            pq.pop();
            pq.push(x);                 // tiket penyesalan: penjualan hari ini bisa "dipindah" nanti
        }
        pq.push(x);                     // hari ini juga bisa menjadi hari beli
    }
    cout << untung << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const p = readInts();
const h = [];
const push = (v) => {
  h.push(v);
  let i = h.length - 1;
  while (i > 0) {
    const q = (i - 1) >> 1;
    if (h[q] <= h[i]) break;
    [h[q], h[i]] = [h[i], h[q]];
    i = q;
  }
};
const pop = () => {
  const top = h[0], last = h.pop();
  if (h.length) {
    h[0] = last;
    let i = 0;
    for (;;) {
      const l = 2 * i + 1, r = l + 1;
      let m = i;
      if (l < h.length && h[l] < h[m]) m = l;
      if (r < h.length && h[r] < h[m]) m = r;
      if (m === i) break;
      [h[m], h[i]] = [h[i], h[m]];
      i = m;
    }
  }
  return top;
};
let untung = 0;
for (const x of p) {
  if (h.length && h[0] < x) {
    untung += x - pop();
    push(x);
  }
  push(x);
}
console.log(String(untung));
CODE,
            'python' => <<<'CODE'
import sys, heapq

data = sys.stdin.buffer.read().split()
n = int(data[0])
pq, untung = [], 0
for x in map(int, data[1:1 + n]):
    if pq and pq[0] < x:
        untung += x - heapq.heapreplace(pq, x)   # jual hari ini, tiket penyesalan seharga x
    heapq.heappush(pq, x)
print(untung)
CODE,
        ],
        'editorial' => '<p>DP O(n²) (hari × banyak saham dimiliki) terlalu lambat. Teknik <strong>regret</strong> memberi O(n log n).</p>
<p>Simpan harga hari-hari sebelumnya di min-heap sebagai calon hari beli. Pada hari dengan harga x, jika ada calon beli termurah m &lt; x, "jual hari ini dengan modal m": untung bertambah x − m. Tetapi mungkin nanti ada harga y &gt; x, dan seharusnya saham itu dijual di y, bukan di x. Untuk itu, masukkan x ke heap sebagai <strong>tiket penyesalan</strong>: jika nanti tiket itu dipakai, untung bertambah y − x, sehingga total (x − m) + (y − x) = y − m, persis seperti memindahkan penjualan ke hari y. Hari x sendiri juga dimasukkan sebagai calon beli biasa.</p>
<p>Setiap hari paling banyak dua kali push dan satu pop: O(n log n). Mengapa tidak melanggar "satu aksi per hari"? Hari yang dipakai sebagai tiket tidak lagi menjual; hari itu menjadi "tidak melakukan apa-apa", sedangkan salinan keduanya bisa menjadi hari beli. Keuntungan maksimum sekitar 1,5·10<sup>11</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Coba greedy sederhana: jual setiap kali harga hari ini lebih tinggi dari harga termurah yang belum dipakai. Di mana salahnya?',
            'Keputusan menjual hari ini bisa disesali jika nanti ada harga lebih tinggi. Bagaimana caranya agar penjualan itu bisa "dipindahkan"?',
            'Setelah menjual pada harga x, masukkan x ke heap sekali lagi sebagai tiket penyesalan; memakainya nanti di harga y menambah y − x.',
        ],
    ],

    // ───────────────────────── Konstruktif ─────────────────────────
    [
        'slug' => 'palindrom-terkecil',
        'lesson' => 'konstruktif',
        'title' => 'Palindrom Terkecil',
        'difficulty' => 'Mudah',
        'tags' => ['konstruktif', 'string', 'greedy'],
        'statement' => '<p>Susun ulang semua huruf dari string <strong>s</strong> menjadi sebuah <strong>palindrom</strong> (dibaca sama dari depan dan belakang). Jika ada beberapa, cetak yang terkecil menurut urutan kamus. Jika tidak mungkin, cetak <code>TIDAK MUNGKIN</code>.</p>',
        'input_format' => '<p>Satu baris berisi string <code>s</code> (huruf kecil).</p>',
        'output_format' => '<p>Palindrom terkecil, atau <code>TIDAK MUNGKIN</code>.</p>',
        'constraints' => '<ul><li>1 ≤ |s| ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "aabbc\n", 'explanation' => 'Palindrom yang mungkin: abcba dan bacab. Yang terkecil abcba.'],
            ['input' => "abc\n", 'explanation' => 'Tiga huruf muncul ganjil kali; palindrom hanya boleh punya paling banyak satu.'],
        ],
        'tests' => function () {
            $pal = function (int $half, string $alpha, bool $mid) {
                $h = T::word($half, $alpha);
                $s = $h.($mid ? T::word(1, $alpha) : '').$h;
                $arr = str_split($s);
                T::shuffle($arr);

                return implode('', $arr)."\n";
            };

            return [
                "a\n", "ab\n", "zz\n", "zyzyx\n", $pal(5, 'abc', true), $pal(500, 'xyz', false), $pal(499999, 'abcdefghijklmnopqrstuvwxyz', true),
                $pal(500000, 'ab', false), T::word(1000000, 'abcdefghijklmnopqrstuvwxyz')."\n", $pal(300000, 'z', true),
            ];
        },
        'solve' => function (string $input) {
            $s = trim($input);
            $cnt = count_chars($s, 1);
            ksort($cnt);
            $odd = '';
            $half = '';
            foreach ($cnt as $c => $k) {
                if ($k % 2) {
                    if ($odd !== '') {
                        return 'TIDAK MUNGKIN';
                    }
                    $odd = chr($c);
                }
                $half .= str_repeat(chr($c), intdiv($k, 2));
            }

            return $half.$odd.strrev($half);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int cnt[26] = {0};
    for (char c : s) cnt[c - 'a']++;

    // berapa huruf yang boleh muncul ganjil kali?
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const s = readLine().trim();
const cnt = new Array(26).fill(0);
for (let i = 0; i < s.length; i++) cnt[s.charCodeAt(i) - 97]++;
// berapa huruf yang boleh muncul ganjil kali?
CODE,
            'python' => <<<'CODE'
s = input().strip()
cnt = [0] * 26
for ch in s:
    cnt[ord(ch) - 97] += 1
# berapa huruf yang boleh muncul ganjil kali?
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int cnt[26] = {0};
    for (char c : s) cnt[c - 'a']++;

    string kiri, tengah;
    for (int c = 0; c < 26; c++) {
        if (cnt[c] % 2) {
            if (!tengah.empty()) {                 // dua huruf ganjil: mustahil
                cout << "TIDAK MUNGKIN\n";
                return 0;
            }
            tengah = string(1, char('a' + c));
        }
        kiri += string(cnt[c] / 2, char('a' + c)); // huruf kecil sedepan mungkin
    }
    string kanan(kiri.rbegin(), kiri.rend());
    cout << kiri << tengah << kanan << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const s = readLine().trim();
const cnt = new Array(26).fill(0);
for (let i = 0; i < s.length; i++) cnt[s.charCodeAt(i) - 97]++;
let kiri = "", tengah = "", mustahil = false;
for (let c = 0; c < 26; c++) {
  if (cnt[c] % 2) {
    if (tengah) { mustahil = true; break; }
    tengah = String.fromCharCode(97 + c);
  }
  kiri += String.fromCharCode(97 + c).repeat(cnt[c] >> 1);
}
console.log(mustahil ? "TIDAK MUNGKIN" : kiri + tengah + kiri.split("").reverse().join(""));
CODE,
            'python' => <<<'CODE'
s = input().strip()
cnt = [0] * 26
for ch in s:
    cnt[ord(ch) - 97] += 1
ganjil = [c for c in range(26) if cnt[c] % 2]
if len(ganjil) > 1:
    print("TIDAK MUNGKIN")
else:
    kiri = "".join(chr(97 + c) * (cnt[c] // 2) for c in range(26))
    tengah = chr(97 + ganjil[0]) if ganjil else ""
    print(kiri + tengah + kiri[::-1])
CODE,
        ],
        'editorial' => '<p><strong>Kapan mungkin?</strong> Di palindrom, setiap huruf berpasangan dengan pasangannya di posisi cermin, kecuali satu huruf di tengah (jika panjangnya ganjil). Jadi paling banyak satu huruf boleh muncul ganjil kali. Dengan |s| ganjil tepat satu, dengan |s| genap tidak ada: syarat "paling banyak satu" sudah mencakup keduanya.</p>
<p><strong>Yang terkecil.</strong> Palindrom ditentukan sepenuhnya oleh separuh kirinya (dan huruf tengah yang dipaksa). Agar terkecil secara leksikografis, separuh kiri harus terkecil: semua huruf diurutkan, a dulu, lalu b, dan seterusnya, masing-masing sebanyak setengah kemunculannya. Huruf tengah tidak punya pilihan.</p>
<p>O(|s| + 26). Untuk |s| = 10<sup>6</sup>, bangun string sekaligus dan cetak sekali.</p>',
        'hints' => [
            'Di palindrom, huruf-huruf berpasangan secara cermin. Berapa huruf yang boleh muncul ganjil kali?',
            'Palindrom ditentukan oleh separuh kirinya. Separuh kiri yang terkecil seperti apa?',
            'Separuh kiri: setiap huruf dari a sampai z sebanyak cnt/2. Lalu huruf ganjil (jika ada), lalu cermin separuh kiri.',
        ],
    ],

    [
        'slug' => 'digit-dan-jumlah',
        'lesson' => 'konstruktif',
        'title' => 'Panjang dan Jumlah Digit',
        'difficulty' => 'Sedang',
        'tags' => ['konstruktif', 'greedy'],
        'statement' => '<p>Diberikan panjang <strong>m</strong> dan jumlah digit <strong>s</strong>. Cari bilangan bulat tak negatif <strong>terkecil</strong> dan <strong>terbesar</strong> yang terdiri dari tepat m digit (tanpa nol di depan) dan jumlah digitnya s.</p>
<p>Jika tidak ada bilangan seperti itu, cetak <code>-1 -1</code>. (Bilangan 0 dianggap punya 1 digit.)</p>',
        'input_format' => '<p>Satu baris berisi <code>m s</code>.</p>',
        'output_format' => '<p>Dua bilangan dipisah spasi: yang terkecil dan yang terbesar, atau <code>-1 -1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ m ≤ 100 000</li><li>0 ≤ s ≤ 900 000</li></ul>',
        'samples' => [
            ['input' => "2 15\n", 'explanation' => 'Bilangan 2 digit dengan jumlah digit 15: 69, 78, 87, 96. Terkecil 69, terbesar 96.'],
            ['input' => "3 0\n", 'explanation' => 'Bilangan 3 digit tanpa nol di depan punya jumlah digit minimal 1.'],
        ],
        'tests' => function () {
            return [
                "1 0\n", "1 9\n", "1 10\n", "2 1\n", "2 18\n", "2 19\n", "5 1\n", "100 900\n", "100 901\n", "100000 1\n",
                "100000 900000\n", "100000 450007\n", "99999 123456\n", "7 50\n",
            ];
        },
        'solve' => function (string $input) {
            [$m, $s] = T::ints($input);
            if ($s == 0) {
                return $m == 1 ? '0 0' : '-1 -1';
            }
            if ($s > 9 * $m) {
                return '-1 -1';
            }
            // terkecil: bangun dari kanan (digit kanan sebesar mungkin), digit pertama minimal 1
            $small = array_fill(0, $m, 0);
            $rest = $s - 1;
            for ($i = $m - 1; $i >= 0; $i--) {
                $d = min(9, $rest);
                $small[$i] = $d;
                $rest -= $d;
            }
            $small[0] += 1;
            $big = '';
            $rest = $s;
            for ($i = 0; $i < $m; $i++) {
                $d = min(9, $rest);
                $big .= $d;
                $rest -= $d;
            }

            return implode('', $small).' '.$big;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int m, s;
    cin >> m >> s;
    // isi digit dari kiri: pilih yang terkecil/terbesar yang masih menyisakan jawaban sah
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [m, s] = readInts();
// isi digit dari kiri: pilih yang terkecil/terbesar yang masih menyisakan jawaban sah
CODE,
            'python' => <<<'CODE'
m, s = map(int, input().split())
# isi digit dari kiri: pilih yang terkecil/terbesar yang masih menyisakan jawaban sah
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int m, s;
    cin >> m >> s;
    if (s == 0) {
        cout << (m == 1 ? "0 0" : "-1 -1") << '\n';
        return 0;
    }
    if (s > 9 * m) {
        cout << "-1 -1\n";
        return 0;
    }

    string kecil, besar;
    int sisa = s;
    for (int i = 0; i < m; i++) {
        int kanan = m - 1 - i;                          // banyak posisi di kanan
        int d = max(i == 0 ? 1 : 0, sisa - 9 * kanan);  // sekecil mungkin, asal sisanya muat
        kecil += char('0' + d);
        sisa -= d;
    }
    sisa = s;
    for (int i = 0; i < m; i++) {
        int d = min(9, sisa);                           // sebesar mungkin
        besar += char('0' + d);
        sisa -= d;
    }
    cout << kecil << ' ' << besar << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [m, s] = readInts();
if (s === 0) {
  console.log(m === 1 ? "0 0" : "-1 -1");
} else if (s > 9 * m) {
  console.log("-1 -1");
} else {
  const kecil = [], besar = [];
  let sisa = s;
  for (let i = 0; i < m; i++) {
    const d = Math.max(i === 0 ? 1 : 0, sisa - 9 * (m - 1 - i));
    kecil.push(d);
    sisa -= d;
  }
  sisa = s;
  for (let i = 0; i < m; i++) {
    const d = Math.min(9, sisa);
    besar.push(d);
    sisa -= d;
  }
  console.log(kecil.join("") + " " + besar.join(""));
}
CODE,
            'python' => <<<'CODE'
m, s = map(int, input().split())
if s == 0:
    print("0 0" if m == 1 else "-1 -1")
elif s > 9 * m:
    print("-1 -1")
else:
    kecil, sisa = [], s
    for i in range(m):
        d = max(1 if i == 0 else 0, sisa - 9 * (m - 1 - i))
        kecil.append(str(d))
        sisa -= d
    besar, sisa = [], s
    for i in range(m):
        d = min(9, sisa)
        besar.append(str(d))
        sisa -= d
    print("".join(kecil), "".join(besar))
CODE,
        ],
        'editorial' => '<p><strong>Kapan mungkin?</strong> Jumlah digit m angka paling besar 9m. Jika s = 0, hanya bilangan 0 (m = 1) yang memenuhi; untuk m &gt; 1, digit pertama minimal 1.</p>
<p><strong>Terbesar.</strong> Digit paling kiri paling menentukan, jadi buat sebesar mungkin: min(9, sisa). Sisanya tidak pernah negatif, dan karena s ≤ 9m, semua jumlah habis terbagi.</p>
<p><strong>Terkecil.</strong> Buat digit paling kiri sekecil mungkin, tetapi masih menyisakan jawaban sah: digit-digit di kanannya (sebanyak k) paling banyak menampung 9k, jadi digit sekarang minimal sisa − 9k (dan minimal 1 untuk digit pertama). Pola "terkecil yang masih bisa diselesaikan" adalah greedy konstruktif yang umum.</p>
<p>O(m).</p>',
        'hints' => [
            'Kapan jawabannya tidak ada? Perhatikan batas jumlah digit dan kasus s = 0.',
            'Untuk yang terbesar, buat digit dari kiri sebesar mungkin.',
            'Untuk yang terkecil, digit di posisi i minimal sisa − 9 × (banyak posisi di kanannya), dan digit pertama minimal 1.',
        ],
    ],

    [
        'slug' => 'permutasi-inversi',
        'lesson' => 'konstruktif',
        'title' => 'Permutasi dengan k Inversi',
        'difficulty' => 'Sulit',
        'tags' => ['konstruktif', 'greedy', 'inversi'],
        'statement' => '<p>Sebuah <strong>inversi</strong> pada permutasi p adalah pasangan indeks (i, j) dengan i &lt; j dan p<sub>i</sub> &gt; p<sub>j</sub>.</p>
<p>Diberikan n dan k. Cetak permutasi 1..n yang punya <strong>tepat k inversi</strong> dan paling kecil menurut urutan leksikografis.</p>',
        'input_format' => '<p>Satu baris berisi <code>n k</code>.</p>',
        'output_format' => '<p>n bilangan: permutasi yang diminta.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 100 000</li><li>0 ≤ k ≤ n(n − 1)/2</li></ul>',
        'samples' => [
            ['input' => "4 2\n", 'explanation' => '1 2 3 4 punya 0 inversi. 1 3 4 2 punya 2 inversi: (3, 2) dan (4, 2). Tidak ada permutasi yang lebih kecil dengan tepat 2 inversi; 1 3 2 4 hanya punya 1.'],
            ['input' => "5 9\n", 'explanation' => 'Maksimum 10 inversi (5 4 3 2 1). Dengan 9 inversi, yang terkecil adalah 4 5 3 2 1.'],
        ],
        'tests' => function () {
            $f = function (int $n, int $k) {
                return "$n $k\n";
            };
            $mx = fn (int $n) => intdiv($n * ($n - 1), 2);

            return [
                $f(1, 0), $f(2, 1), $f(6, 0), $f(6, 15), $f(7, 11), $f(8, 3), $f(1000, 123456), $f(100000, 0), $f(100000, $mx(100000)),
                $f(100000, $mx(100000) - 1), $f(100000, 2500000000), $f(99999, 777777), $f(100000, $mx(100000) - 99999),
            ];
        },
        'solve' => function (string $input) {
            [$n, $k] = T::ints($input);
            $res = [];
            $lo = 1;
            for ($i = 0; $i < $n; $i++) {
                $m = $n - $i;
                $maxRest = intdiv(($m - 1) * ($m - 2), 2);
                if ($k <= $maxRest) {
                    $res[] = $lo++;
                } else {
                    $j = $k - $maxRest;
                    $x = $lo + $j;
                    $res[] = $x;
                    for ($v = $n; $v >= $lo; $v--) {
                        if ($v != $x) {
                            $res[] = $v;
                        }
                    }
                    break;
                }
            }

            return implode(' ', $res);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    long long n, k;
    cin >> n >> k;
    // isi dari kiri: pilih elemen terkecil yang masih memungkinkan tepat k inversi
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
// isi dari kiri: pilih elemen terkecil yang masih memungkinkan tepat k inversi
CODE,
            'python' => <<<'CODE'
n, k = map(int, input().split())
# isi dari kiri: pilih elemen terkecil yang masih memungkinkan tepat k inversi
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    long long n, k;
    cin >> n >> k;
    vector<long long> res;
    res.reserve(n);
    long long lo = 1;                         // elemen tersisa selalu {lo, lo+1, ..., n}
    for (long long i = 0; i < n; i++) {
        long long m = n - i;                  // banyak elemen tersisa (termasuk posisi ini)
        long long maxSisa = (m - 1) * (m - 2) / 2;   // inversi maksimum dari m-1 elemen sesudahnya
        if (k <= maxSisa) {
            res.push_back(lo++);              // ambil yang terkecil: tidak menambah inversi
        } else {
            long long j = k - maxSisa;        // terpaksa: ambil elemen terkecil ke-j (0-based)
            long long x = lo + j;             // menyumbang j inversi
            res.push_back(x);
            for (long long v = n; v >= lo; v--) // sisanya harus memberi maxSisa inversi: urut turun
                if (v != x) res.push_back(v);
            break;
        }
    }
    string out;
    for (size_t t = 0; t < res.size(); t++) {
        if (t) out += ' ';
        out += to_string(res[t]);
    }
    cout << out << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k0] = readInts();
let k = k0;
const res = [];
let lo = 1;
for (let i = 0; i < n; i++) {
  const m = n - i;
  const maxSisa = ((m - 1) * (m - 2)) / 2;
  if (k <= maxSisa) {
    res.push(lo++);
  } else {
    const x = lo + (k - maxSisa);
    res.push(x);
    for (let v = n; v >= lo; v--) if (v !== x) res.push(v);
    break;
  }
}
console.log(res.join(" "));
CODE,
            'python' => <<<'CODE'
n, k = map(int, input().split())
res = []
lo = 1
for i in range(n):
    m = n - i
    max_sisa = (m - 1) * (m - 2) // 2
    if k <= max_sisa:
        res.append(lo)
        lo += 1
    else:
        x = lo + (k - max_sisa)
        res.append(x)
        res.extend(v for v in range(n, lo - 1, -1) if v != x)
        break
print(" ".join(map(str, res)))
CODE,
        ],
        'editorial' => '<p>Bangun dari kiri, dan di setiap posisi pilih elemen <strong>terkecil</strong> yang masih memungkinkan total tepat k inversi (pola leksikografis terkecil).</p>
<p>Jika masih tersisa m elemen dan kita menaruh elemen terkecil ke-j (0-based) di posisi ini, ia menyumbang tepat j inversi (dengan j elemen lebih kecil yang datang sesudahnya). Sisa m − 1 elemen bisa menyumbang 0 sampai (m − 1)(m − 2)/2 inversi. Jadi j terkecil yang layak adalah max(0, k − (m − 1)(m − 2)/2).</p>
<p>Selama j = 0 kita mengambil elemen terkecil, sehingga elemen tersisa selalu berupa rentang {lo, …, n}. Begitu j &gt; 0 untuk pertama kali, sisa inversi yang dibutuhkan menjadi tepat maksimum, artinya semua elemen sesudahnya harus urut turun. Jadi jawabannya: 1, 2, …, lo − 1, lalu lo + j, lalu sisanya menurun. O(n).</p>
<p>k bisa mencapai sekitar 5·10<sup>9</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Bangun dari kiri. Jika di posisi ini kamu menaruh elemen terkecil ke-j dari yang tersisa, berapa inversi yang ia sumbangkan?',
            'Sisa m − 1 elemen bisa menyumbang paling banyak (m − 1)(m − 2)/2 inversi. Jadi j minimal berapa?',
            'Begitu terpaksa mengambil j > 0, sisa inversinya harus maksimum: semua elemen sesudahnya urut turun.',
        ],
    ],

    // ───────────────────────── Invarian ─────────────────────────
    [
        'slug' => 'tukar-jarak-dua',
        'lesson' => 'invariant',
        'title' => 'Tukar Berjarak Dua',
        'difficulty' => 'Mudah',
        'tags' => ['invarian', 'sorting'],
        'statement' => '<p>Diberikan array <strong>a</strong> berisi n bilangan. Satu operasi: pilih indeks i (1 ≤ i ≤ n − 2) lalu tukar <code>a<sub>i</sub></code> dan <code>a<sub>i+2</sub></code>. Operasi boleh dilakukan berapa kali pun.</p>
<p>Bisakah array diurutkan menjadi tidak turun? Jawab untuk beberapa kasus uji.</p>',
        'input_format' => '<p>Baris pertama berisi <code>t</code>. Setiap kasus terdiri dari baris berisi <code>n</code> lalu baris berisi <code>a<sub>1</sub> … a<sub>n</sub></code>.</p>',
        'output_format' => '<p>Untuk setiap kasus, <code>YA</code> atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ t ≤ 10 000</li><li>1 ≤ n, jumlah n ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n4\n3 2 1 4\n4\n2 1 3 4\n5\n5 2 3 4 1\n", 'explanation' => 'Kasus 1: tukar a<sub>1</sub> dan a<sub>3</sub> → 1 2 3 4. Kasus 2: 2 harus pindah dari posisi 1 ke posisi 2, padahal operasi tidak pernah mengubah paritas posisi. Kasus 3: tukar posisi 1 dan 3, lalu 3 dan 5 … bisa: 1 2 3 4 5.'],
        ],
        'tests' => function () {
            $gen = function (int $t, int $n, int $hi, bool $sortable) {
                $cases = [];
                for ($c = 0; $c < $t; $c++) {
                    $a = T::arr($n, 1, $hi);
                    if ($sortable && $c % 2 == 0) {
                        sort($a);
                        $odd = [];
                        $even = [];
                        foreach ($a as $i => $v) {
                            if ($i % 2) {
                                $odd[] = $v;
                            } else {
                                $even[] = $v;
                            }
                        }
                        T::shuffle($odd);
                        T::shuffle($even);
                        $a = [];
                        for ($i = 0; $i < $n; $i++) {
                            $a[] = $i % 2 ? $odd[intdiv($i, 2)] : $even[intdiv($i, 2)];
                        }
                    }
                    $cases[] = "$n\n".T::join($a);
                }

                return "$t\n".implode("\n", $cases)."\n";
            };

            return [
                "2\n1\n7\n2\n2 1\n", $gen(50, 5, 5, true), $gen(1000, 6, 3, true), $gen(10000, 20, 1000000000, true), $gen(1, 200000, 1000000000, true),
                $gen(2, 100000, 2, true), $gen(1, 200000, 1000000000, false), $gen(4, 50000, 50000, true),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $out = [];
            $ln = 1;
            for ($c = 0; $c < $t; $c++) {
                $n = (int) $lines[$ln++];
                $a = T::ints($lines[$ln++]);
                $b = $a;
                sort($b);
                // invarian: multiset nilai di posisi genap (0-based) tidak berubah
                $e1 = [];
                $e2 = [];
                for ($i = 0; $i < $n; $i += 2) {
                    $e1[] = $a[$i];
                    $e2[] = $b[$i];
                }
                sort($e1);
                $out[] = $e1 == $e2 ? 'YA' : 'TIDAK';
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

    int t;
    cin >> t;
    while (t--) {
        int n;
        cin >> n;
        vector<long long> a(n);
        for (auto& v : a) cin >> v;
        // apa yang tidak pernah berubah oleh operasi tukar berjarak dua?
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const t = readInts()[0];
const out = [];
for (let c = 0; c < t; c++) {
  const n = readInts()[0];
  const a = readInts();
  // apa yang tidak pernah berubah oleh operasi tukar berjarak dua?
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
t = int(data[0])
pos = 1
out = []
for _ in range(t):
    n = int(data[pos]); pos += 1
    a = list(map(int, data[pos:pos + n])); pos += n
    # apa yang tidak pernah berubah oleh operasi tukar berjarak dua?
print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int t;
    cin >> t;
    while (t--) {
        int n;
        cin >> n;
        vector<long long> a(n);
        for (auto& v : a) cin >> v;
        // Invarian: elemen tidak pernah berpindah antara posisi genap dan ganjil.
        // Di dalam satu kelompok, tukar berjarak dua = tukar bersebelahan → bisa diurutkan bebas.
        vector<long long> genap, ganjil;
        for (int i = 0; i < n; i++) (i % 2 ? ganjil : genap).push_back(a[i]);
        sort(genap.begin(), genap.end());
        sort(ganjil.begin(), ganjil.end());
        vector<long long> b(n);
        for (int i = 0; i < n; i++) b[i] = (i % 2 ? ganjil[i / 2] : genap[i / 2]);
        bool urut = is_sorted(b.begin(), b.end());
        cout << (urut ? "YA" : "TIDAK") << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const t = readInts()[0];
const out = [];
for (let c = 0; c < t; c++) {
  const n = readInts()[0];
  const a = readInts();
  const genap = [], ganjil = [];
  for (let i = 0; i < n; i++) (i % 2 ? ganjil : genap).push(a[i]);
  genap.sort((p, q) => p - q);
  ganjil.sort((p, q) => p - q);
  let ok = true, prev = -Infinity;
  for (let i = 0; i < n; i++) {
    const v = i % 2 ? ganjil[i >> 1] : genap[i >> 1];
    if (v < prev) { ok = false; break; }
    prev = v;
  }
  out.push(ok ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
t = int(data[0])
pos = 1
out = []
for _ in range(t):
    n = int(data[pos]); pos += 1
    a = list(map(int, data[pos:pos + n])); pos += n
    b = [0] * n
    b[0::2] = sorted(a[0::2])
    b[1::2] = sorted(a[1::2])
    out.append("YA" if all(b[i] <= b[i + 1] for i in range(n - 1)) else "TIDAK")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p><strong>Invarian:</strong> operasi menukar posisi i dan i + 2, yang paritasnya sama. Jadi sebuah nilai tidak pernah berpindah antara posisi ganjil dan genap: himpunan nilai di posisi ganjil (dan di posisi genap) tetap.</p>
<p><strong>Cukup:</strong> jika dilihat hanya posisi ganjil (1, 3, 5, …), operasi itu sama dengan menukar dua elemen bersebelahan di subbarisan tersebut, dan dengan tukar bersebelahan (bubble sort) subbarisan bisa diurutkan apa pun. Sama untuk posisi genap.</p>
<p>Jadi urutkan kedua subbarisan secara terpisah, gabungkan kembali secara selang-seling, lalu periksa apakah hasilnya tidak turun. O(n log n) per kasus.</p>',
        'hints' => [
            'Sebuah nilai di posisi ganjil, setelah operasi apa pun, ada di posisi apa?',
            'Di dalam posisi-posisi ganjil saja, operasinya sama dengan menukar dua elemen bersebelahan. Apa yang bisa dicapai dengan itu?',
            'Urutkan elemen di posisi ganjil dan genap secara terpisah, gabungkan selang-seling, lalu cek apakah terurut.',
        ],
    ],

    [
        'slug' => 'domino-dua-lubang',
        'lesson' => 'invariant',
        'title' => 'Domino di Papan Berlubang',
        'difficulty' => 'Sedang',
        'tags' => ['invarian', 'pewarnaan', 'matematika'],
        'statement' => '<p>Sebuah papan berukuran <strong>n × m</strong> kehilangan dua petak berbeda: <code>(r<sub>1</sub>, c<sub>1</sub>)</code> dan <code>(r<sub>2</sub>, c<sub>2</sub>)</code>. Bisakah semua petak yang tersisa ditutup tepat oleh domino 1 × 2 (boleh mendatar atau tegak, tanpa tumpang tindih dan tanpa keluar papan)?</p>
<p>Jawab untuk Q pertanyaan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi <code>n m r<sub>1</sub> c<sub>1</sub> r<sub>2</sub> c<sub>2</sub></code> (baris dan kolom mulai dari 1).</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, <code>YA</code> atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>2 ≤ n, m ≤ 10<sup>9</sup></li><li>1 ≤ r ≤ n, 1 ≤ c ≤ m, kedua petak berbeda</li></ul>',
        'samples' => [
            ['input' => "3\n8 8 1 1 8 8\n8 8 1 1 1 2\n3 3 1 1 3 3\n", 'explanation' => 'Pertanyaan 1: dua sudut berseberangan berwarna sama → mustahil. Pertanyaan 2: dua petak berwarna berbeda → bisa. Pertanyaan 3: 9 − 2 = 7 petak, ganjil → mustahil.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $nMax) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $n = mt_rand(2, $nMax);
                    $m = mt_rand(2, $nMax);
                    do {
                        $r1 = mt_rand(1, $n);
                        $c1 = mt_rand(1, $m);
                        $r2 = mt_rand(1, $n);
                        $c2 = mt_rand(1, $m);
                    } while ($r1 == $r2 && $c1 == $c2);
                    $rows[] = "$n $m $r1 $c1 $r2 $c2";
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return [
                "2\n2 2 1 1 1 2\n2 2 1 1 2 2\n", $gen(200, 4), $gen(1000, 6), $gen(100000, 1000000000), $gen(100000, 3), $gen(100000, 1000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                [$n, $m, $r1, $c1, $r2, $c2] = T::ints($lines[$i]);
                $genap = ($n % 2 == 0) || ($m % 2 == 0);
                $beda = (($r1 + $c1) % 2) != (($r2 + $c2) % 2);
                $out[] = $genap && $beda ? 'YA' : 'TIDAK';
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

    int q;
    cin >> q;
    while (q--) {
        long long n, m, r1, c1, r2, c2;
        cin >> n >> m >> r1 >> c1 >> r2 >> c2;
        // warnai papan seperti papan catur
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = readInts()[0];
const out = [];
for (let i = 0; i < q; i++) {
  const [n, m, r1, c1, r2, c2] = readInts();
  // warnai papan seperti papan catur
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
q = int(data[0])
out = []
for i in range(q):
    n, m, r1, c1, r2, c2 = map(int, data[1 + 6 * i:7 + 6 * i])
    # warnai papan seperti papan catur
print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    while (q--) {
        long long n, m, r1, c1, r2, c2;
        cin >> n >> m >> r1 >> c1 >> r2 >> c2;
        bool totalGenap = (n % 2 == 0) || (m % 2 == 0);          // n*m genap
        bool warnaBeda = (r1 + c1) % 2 != (r2 + c2) % 2;          // satu hitam, satu putih
        cout << (totalGenap && warnaBeda ? "YA" : "TIDAK") << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = readInts()[0];
const out = [];
for (let i = 0; i < q; i++) {
  const [n, m, r1, c1, r2, c2] = readInts();
  const totalGenap = n % 2 === 0 || m % 2 === 0;
  const warnaBeda = (r1 + c1) % 2 !== (r2 + c2) % 2;
  out.push(totalGenap && warnaBeda ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
q = int(data[0])
out = []
for i in range(q):
    n, m, r1, c1, r2, c2 = map(int, data[1 + 6 * i:7 + 6 * i])
    ok = (n % 2 == 0 or m % 2 == 0) and (r1 + c1) % 2 != (r2 + c2) % 2
    out.append("YA" if ok else "TIDAK")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p><strong>Syarat perlu (invarian pewarnaan).</strong> Warnai papan seperti papan catur: petak (r, c) hitam jika r + c genap. Setiap domino selalu menutup satu petak hitam dan satu putih. Papan dengan n·m genap punya hitam dan putih sama banyak; agar tetap sama setelah dua petak dibuang, keduanya harus berbeda warna. Jika n·m ganjil, sisa petaknya ganjil: mustahil.</p>
<p><strong>Syarat cukup (teorema Gomory).</strong> Untuk n, m ≥ 2 dengan n·m genap, petak-petak papan bisa dijalani dalam satu siklus yang melewati setiap petak tepat sekali (siklus Hamilton, misalnya pola ular yang kembali lewat kolom pertama). Siklus itu berselang-seling warna. Membuang dua petak berbeda warna memotong siklus menjadi dua lintasan (atau satu), masing-masing dengan banyak petak genap, dan lintasan berpanjang genap selalu bisa ditutup domino berpasangan sepanjang lintasan.</p>
<p>Jadi jawabannya YA ⇔ n·m genap dan kedua petak berbeda warna. O(1) per pertanyaan.</p>',
        'hints' => [
            'Warnai papan seperti papan catur. Petak berwarna apa saja yang ditutup satu domino?',
            'Jika dua petak yang dibuang berwarna sama, apa yang terjadi pada selisih banyak petak hitam dan putih?',
            'Ternyata syarat itu juga cukup jika n·m genap (teorema Gomory, lewat siklus Hamilton di papan). Jawaban: n·m genap dan warna berbeda.',
        ],
    ],

    [
        'slug' => 'puzzle-geser',
        'lesson' => 'invariant',
        'title' => 'Puzzle Geser',
        'difficulty' => 'Sulit',
        'tags' => ['invarian', 'paritas permutasi'],
        'statement' => '<p>Puzzle geser berukuran <strong>n × n</strong> berisi ubin bernomor 1 sampai n² − 1 dan satu kotak kosong (ditulis 0). Satu langkah: geser ubin yang bersebelahan dengan kotak kosong ke kotak kosong itu.</p>
<p>Keadaan selesai adalah ubin 1, 2, …, n² − 1 terurut baris demi baris dari kiri atas, dengan kotak kosong di pojok kanan bawah. Bisakah keadaan yang diberikan diubah menjadi keadaan selesai?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi n bilangan; semuanya membentuk permutasi dari 0..n² − 1.</p>',
        'output_format' => '<p><code>BISA</code> atau <code>TIDAK BISA</code>.</p>',
        'constraints' => '<ul><li>2 ≤ n ≤ 500</li></ul>',
        'samples' => [
            ['input' => "3\n1 2 3\n4 5 6\n8 7 0\n", 'explanation' => 'Hanya ubin 7 dan 8 yang tertukar: satu inversi (ganjil) dengan lebar 3 (ganjil) → tidak bisa.'],
            ['input' => "2\n1 2\n0 3\n", 'explanation' => 'Geser ubin 3 ke kiri: selesai.'],
        ],
        'tests' => function () {
            $acak = function (int $n) {
                $p = range(0, $n * $n - 1);
                T::shuffle($p);
                $s = "$n\n";
                for ($r = 0; $r < $n; $r++) {
                    $s .= T::join(array_slice($p, $r * $n, $n))."\n";
                }

                return $s;
            };
            $jalan = function (int $n, int $langkah) {
                // dari keadaan selesai, lakukan langkah acak (pasti BISA)
                $g = range(1, $n * $n);
                $g[$n * $n - 1] = 0;
                $pos = $n * $n - 1;
                for ($k = 0; $k < $langkah; $k++) {
                    $r = intdiv($pos, $n);
                    $c = $pos % $n;
                    $opsi = [];
                    if ($r > 0) {
                        $opsi[] = $pos - $n;
                    }
                    if ($r < $n - 1) {
                        $opsi[] = $pos + $n;
                    }
                    if ($c > 0) {
                        $opsi[] = $pos - 1;
                    }
                    if ($c < $n - 1) {
                        $opsi[] = $pos + 1;
                    }
                    $nx = $opsi[mt_rand(0, count($opsi) - 1)];
                    $g[$pos] = $g[$nx];
                    $g[$nx] = 0;
                    $pos = $nx;
                }
                $s = "$n\n";
                for ($r = 0; $r < $n; $r++) {
                    $s .= T::join(array_slice($g, $r * $n, $n))."\n";
                }

                return $s;
            };

            return [
                "2\n0 1\n3 2\n", "2\n3 1\n2 0\n", $acak(3), $acak(4), $jalan(4, 1000), $acak(5), $acak(10), $jalan(10, 5000),
                $acak(100), $acak(500), $jalan(300, 200000), $acak(499), $acak(498),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: inversi lewat BIT (bukan siklus)
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $a = [];
            $rowKosong = 0;
            for ($r = 0; $r < $n; $r++) {
                foreach (T::ints($lines[$r + 1]) as $v) {
                    if ($v == 0) {
                        $rowKosong = $r;
                    } else {
                        $a[] = $v;
                    }
                }
            }
            $N = $n * $n;
            $bit = array_fill(0, $N + 1, 0);
            $inv = 0;
            $seen = 0;
            foreach ($a as $v) {
                // banyak elemen sebelumnya yang lebih besar dari v
                $s = 0;
                for ($i = $v; $i > 0; $i -= $i & -$i) {
                    $s += $bit[$i];
                }
                $inv += $seen - $s;
                for ($i = $v; $i <= $N; $i += $i & -$i) {
                    $bit[$i]++;
                }
                $seen++;
            }
            $dariBawah = $n - $rowKosong;
            $ok = $n % 2 == 1 ? $inv % 2 == 0 : ($inv + $dariBawah) % 2 == 1;

            return $ok ? 'BISA' : 'TIDAK BISA';
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
    vector<int> a;                 // ubin dibaca baris demi baris, tanpa 0
    int barisKosong = 0;
    for (int r = 0; r < n; r++)
        for (int c = 0; c < n; c++) {
            int v;
            cin >> v;
            if (v == 0) barisKosong = r;
            else a.push_back(v);
        }
    // hitung paritas banyak inversi a
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = [];
let barisKosong = 0;
for (let r = 0; r < n; r++) {
  const row = readInts();
  for (const v of row) {
    if (v === 0) barisKosong = r;
    else a.push(v);
  }
}
// hitung paritas banyak inversi a
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
vals = list(map(int, data[1:1 + n * n]))
baris_kosong = vals.index(0) // n
a = [v for v in vals if v != 0]
# hitung paritas banyak inversi a
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
    vector<int> a;                 // ubin dibaca baris demi baris, tanpa 0
    int barisKosong = 0;
    for (int r = 0; r < n; r++)
        for (int c = 0; c < n; c++) {
            int v;
            cin >> v;
            if (v == 0) barisKosong = r;
            else a.push_back(v);
        }

    // Paritas inversi = paritas (panjang - banyak siklus) dari permutasi a (nilai 1..N-1)
    int m = a.size();
    vector<char> lihat(m, 0);
    int siklus = 0;
    for (int i = 0; i < m; i++)
        if (!lihat[i]) {
            siklus++;
            for (int j = i; !lihat[j]; j = a[j] - 1) lihat[j] = 1;
        }
    int paritasInv = (m - siklus) % 2;
    int dariBawah = n - barisKosong;            // baris kotak kosong, dihitung dari bawah mulai 1

    bool bisa = (n % 2 == 1) ? (paritasInv == 0) : ((paritasInv + dariBawah) % 2 == 1);
    cout << (bisa ? "BISA" : "TIDAK BISA") << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = [];
let barisKosong = 0;
for (let r = 0; r < n; r++) {
  const row = readInts();
  for (const v of row) {
    if (v === 0) barisKosong = r;
    else a.push(v);
  }
}
const m = a.length;
const lihat = new Uint8Array(m);
let siklus = 0;
for (let i = 0; i < m; i++) {
  if (lihat[i]) continue;
  siklus++;
  for (let j = i; !lihat[j]; j = a[j] - 1) lihat[j] = 1;
}
const paritasInv = (m - siklus) % 2;
const dariBawah = n - barisKosong;
const bisa = n % 2 === 1 ? paritasInv === 0 : (paritasInv + dariBawah) % 2 === 1;
console.log(bisa ? "BISA" : "TIDAK BISA");
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
vals = list(map(int, data[1:1 + n * n]))
baris_kosong = vals.index(0) // n
a = [v for v in vals if v != 0]
m = len(a)
lihat = bytearray(m)
siklus = 0
for i in range(m):
    if not lihat[i]:
        siklus += 1
        j = i
        while not lihat[j]:
            lihat[j] = 1
            j = a[j] - 1
paritas_inv = (m - siklus) % 2
dari_bawah = n - baris_kosong
if n % 2 == 1:
    bisa = paritas_inv == 0
else:
    bisa = (paritas_inv + dari_bawah) % 2 == 1
print("BISA" if bisa else "TIDAK BISA")
CODE,
        ],
        'editorial' => '<p>Baca ubin baris demi baris (tanpa kotak kosong) sebagai barisan, dan hitung banyak inversinya.</p>
<ul><li><strong>Geser mendatar</strong> tidak mengubah barisan sama sekali.</li>
<li><strong>Geser tegak</strong> memindahkan satu ubin melompati n − 1 ubin lain di barisan, sehingga banyak inversi berubah sebesar bilangan berparitas sama dengan n − 1; sekaligus kotak kosong pindah satu baris.</li></ul>
<p>Untuk n ganjil, n − 1 genap: paritas inversi adalah invarian. Keadaan selesai punya 0 inversi, jadi syaratnya inversi genap. Untuk n genap, setiap geser tegak membalik paritas inversi <em>dan</em> paritas baris kotak kosong, sehingga paritas (inversi + baris kosong dihitung dari bawah) tetap; di keadaan selesai nilainya 0 + 1 = ganjil. Syarat-syarat ini juga cukup (fakta klasik puzzle 15).</p>
<p>Menghitung inversi n² ≈ 2,5·10<sup>5</sup> ubin bisa dengan BIT, tetapi paritasnya cukup lewat siklus permutasi: paritas inversi = (panjang − banyak siklus) mod 2. O(n²).</p>',
        'hints' => [
            'Baca ubin baris demi baris tanpa kotak kosong. Bagaimana geser mendatar dan geser tegak mengubah banyak inversinya?',
            'Untuk n ganjil, paritas inversi tidak pernah berubah. Untuk n genap, perhatikan juga baris kotak kosong.',
            'Paritas inversi bisa dihitung dari siklus permutasi: (panjang − banyak siklus) mod 2.',
        ],
    ],
];
