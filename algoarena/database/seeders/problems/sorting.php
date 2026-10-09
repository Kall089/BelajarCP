<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Sorting & Searching: sorting dasar, merge sort, quick sort, counting sort,
 * binary search, ternary search, soal interaktif (simulasi), dan meet in the middle.
 * Semua kode C++ harus lolos C++14: tanpa structured binding.
 */

return [

    // ───────────────────────── Sorting Dasar & Stabilitas ─────────────────────────
    [
        'slug' => 'antrian-stabil',
        'lesson' => 'sorting-dasar',
        'title' => 'Baris Upacara',
        'difficulty' => 'Mudah',
        'tags' => ['sorting', 'stabil'],
        'statement' => '<p><strong>N</strong> siswa mendaftar untuk upacara satu per satu; siswa ke-i (urutan daftar) bertinggi <code>t<sub>i</sub></code> cm. Petugas menyusun barisan dari yang <strong>terpendek</strong> ke yang tertinggi. Jika dua siswa sama tinggi, yang mendaftar <strong>lebih dulu</strong> berdiri di depan.</p>
<p>Cetak nomor urut daftar siswa dari depan ke belakang barisan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>t<sub>1</sub> … t<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris berisi N nomor siswa (1 sampai N).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>100 ≤ t<sub>i</sub> ≤ 200</li></ul>',
        'samples' => [
            ['input' => "6\n150 140 150 160 140 150\n", 'explanation' => 'Tinggi 140: siswa 2 dan 5 (urutan daftar). Tinggi 150: siswa 1, 3, 6. Lalu siswa 4.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return ["1\n170\n", "3\n150 150 150\n", $make(10, 140, 145), $make(1000, 100, 200), $make(200000, 100, 200), $make(200000, 150, 152), $make(200000, 200, 200)];
        },
        'solve' => function (string $input) {
            $t = T::ints(T::lines($input)[1]);
            $idx = range(1, count($t));
            array_multisort($t, SORT_ASC, SORT_NUMERIC, $idx, SORT_ASC, SORT_NUMERIC);

            return T::join($idx);
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
    vector<int> t(n);
    for (auto& x : t) cin >> x;

    // urutkan nomor siswa 1..n: tinggi menaik, seri → nomor daftar lebih kecil dulu

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const t = readInts();
// urutkan nomor siswa: tinggi menaik, seri → nomor daftar lebih kecil dulu
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
t = list(map(int, input().split()))
# urutkan nomor siswa: tinggi menaik, seri → nomor daftar lebih kecil dulu
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
    vector<int> t(n);
    for (auto& x : t) cin >> x;

    vector<int> id(n);
    iota(id.begin(), id.end(), 0);
    // stable_sort: siswa sama tinggi tetap dalam urutan daftar
    stable_sort(id.begin(), id.end(), [&](int a, int b) { return t[a] < t[b]; });

    for (int k = 0; k < n; k++) cout << id[k] + 1 << " \n"[k + 1 == n];
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const t = readInts();
const id = Array.from({ length: n }, (_, i) => i);
id.sort((a, b) => t[a] - t[b] || a - b);   // seri → indeks kecil dulu
console.log(id.map((i) => i + 1).join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
t = list(map(int, input().split()))
urut = sorted(range(n), key=lambda i: t[i])   # sorted() Python stabil
print(" ".join(str(i + 1) for i in urut))
CODE,
        ],
        'editorial' => '<p>Urutkan <strong>nomor siswa</strong> berdasarkan tinggi. Syarat "sama tinggi → yang daftar lebih dulu di depan" adalah definisi sorting <strong>stabil</strong>.</p>
<ul><li>C++: <code>stable_sort</code> dengan comparator tinggi saja; atau <code>sort</code> dengan kriteria tambahan nomor daftar.</li><li>Python: <code>sorted</code> selalu stabil.</li><li>JavaScript: <code>Array.prototype.sort</code> stabil sejak ES2019, tetapi menambahkan <code>|| a − b</code> membuatnya pasti.</li></ul>
<p>O(N log N). (Karena tinggi hanya 100–200, counting sort juga bisa: O(N + 101).)</p>',
        'hints' => [
            'Yang diurutkan adalah nomor siswa, dengan kunci tinggi badan.',
            'Seri harus mempertahankan urutan daftar. Itu definisi sorting stabil.',
            'stable_sort pada indeks dengan comparator t[a] < t[b], atau sort dengan kriteria kedua indeks.',
        ],
    ],

    [
        'slug' => 'tukar-minimum',
        'lesson' => 'sorting-dasar',
        'title' => 'Tukar Kursi',
        'difficulty' => 'Sedang',
        'tags' => ['permutasi', 'siklus', 'sorting'],
        'statement' => '<p><strong>N</strong> peserta ujian bernomor 1 sampai N sudah duduk di kursi 1 sampai N, tetapi kacau: di kursi ke-i duduk peserta bernomor <code>p<sub>i</sub></code>. Pengawas ingin setiap peserta k duduk di kursi k.</p>
<p>Dalam satu langkah, pengawas boleh meminta <strong>dua peserta mana saja</strong> bertukar kursi. Berapa langkah minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi permutasi <code>p<sub>1</sub> … p<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak tukar minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>p adalah permutasi 1..N</li></ul>',
        'samples' => [
            ['input' => "5\n3 1 2 5 4\n", 'explanation' => 'Siklus kursi 1 → 3 → 2 → 1 (panjang 3) butuh 2 tukar, siklus 4 ↔ 5 butuh 1 tukar. Total 3.'],
        ],
        'tests' => function () {
            $cycles = function (int $n, int $len) {
                $p = range(1, $n);
                for ($s = 0; $s + $len <= $n; $s += $len) {
                    for ($i = $s; $i < $s + $len - 1; $i++) {
                        [$p[$i], $p[$i + 1]] = [$p[$i + 1], $p[$i]];
                    }
                }

                return "$n\n".T::join($p)."\n";
            };

            return [
                "1\n1\n", "2\n2 1\n", "4\n1 2 3 4\n", "6\n".T::join(T::perm(6))."\n", "1000\n".T::join(T::perm(1000))."\n",
                "200000\n".T::join(T::perm(200000))."\n", $cycles(200000, 2), $cycles(200000, 200000), "200000\n".T::join(range(1, 200000))."\n",
            ];
        },
        'solve' => function (string $input) {
            $p = T::ints(T::lines($input)[1]);
            $n = count($p);
            $seen = array_fill(1, $n, false);
            $c = 0;
            for ($i = 1; $i <= $n; $i++) {
                if ($seen[$i]) {
                    continue;
                }
                $c++;
                for ($j = $i; ! $seen[$j]; $j = $p[$j - 1]) {
                    $seen[$j] = true;
                }
            }

            return (string) ($n - $c);
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
    vector<int> p(n + 1);
    for (int i = 1; i <= n; i++) cin >> p[i];

    // hitung banyak siklus pada i -> p[i]

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const p = [0, ...readInts()];   // 1-indexed
// hitung banyak siklus pada i -> p[i]
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
p = [0] + list(map(int, input().split()))   # 1-indexed
# hitung banyak siklus pada i -> p[i]
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
    vector<int> p(n + 1);
    for (int i = 1; i <= n; i++) cin >> p[i];

    vector<bool> lihat(n + 1, false);
    int siklus = 0;
    for (int i = 1; i <= n; i++) {
        if (lihat[i]) continue;
        siklus++;
        for (int j = i; !lihat[j]; j = p[j]) lihat[j] = true;   // telusuri satu siklus
    }
    cout << n - siklus << '\n';      // siklus sepanjang k butuh k - 1 tukar
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const p = [0, ...readInts()];
const lihat = new Uint8Array(n + 1);
let siklus = 0;
for (let i = 1; i <= n; i++) {
  if (lihat[i]) continue;
  siklus++;
  for (let j = i; !lihat[j]; j = p[j]) lihat[j] = 1;
}
console.log(String(n - siklus));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
p = [0] + list(map(int, input().split()))
lihat = [False] * (n + 1)
siklus = 0
for i in range(1, n + 1):
    if lihat[i]:
        continue
    siklus += 1
    j = i
    while not lihat[j]:
        lihat[j] = True
        j = p[j]
print(n - siklus)
CODE,
        ],
        'editorial' => '<p>Gambar panah dari kursi i ke kursi p<sub>i</sub> (kursi yang seharusnya ditempati peserta yang sedang duduk di i). Karena p permutasi, panah-panah ini membentuk beberapa <strong>siklus</strong> yang saling lepas.</p>
<ul><li>Siklus sepanjang 1: peserta sudah di kursinya.</li><li>Siklus sepanjang k bisa dibereskan dengan k − 1 tukar (setiap tukar menempatkan satu peserta dengan benar, dan tukar terakhir menempatkan dua).</li></ul>
<p>Setiap tukar mengubah banyak siklus paling banyak 1, sedangkan keadaan terurut punya N siklus. Jadi jawaban minimum = <code>N − (banyak siklus)</code>.</p>
<p>Telusuri setiap siklus sekali dengan array penanda: O(N).</p>',
        'hints' => [
            'Ikuti panah: peserta di kursi i harus pindah ke kursi p_i, dan peserta di sana harus pindah lagi... ke mana akhirnya?',
            'Panah i → p_i membentuk siklus-siklus. Berapa tukar untuk membereskan satu siklus sepanjang k?',
            'Jawaban = N − banyak siklus. Hitung siklus dengan menandai kursi yang sudah dikunjungi.',
        ],
    ],

    // ───────────────────────── Merge Sort, Inversi & D&C ─────────────────────────
    [
        'slug' => 'tukar-tetangga',
        'lesson' => 'merge-sort',
        'title' => 'Tukar Tetangga Minimum',
        'difficulty' => 'Sedang',
        'tags' => ['merge sort', 'inversi'],
        'statement' => '<p>Ada <strong>N</strong> buku di rak dengan tinggi <code>a<sub>1</sub>, …, a<sub>N</sub></code>. Pustakawan ingin bukunya tersusun dari yang terpendek ke yang tertinggi (tinggi sama boleh dalam urutan apa pun). Dalam satu langkah, ia hanya boleh menukar <strong>dua buku yang bersebelahan</strong>.</p>
<p>Berapa langkah minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak tukar minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n3 1 4 1 5\n", 'explanation' => 'Pasangan terbalik: (3, 1), (3, 1), (4, 1). Ada 3 inversi, jadi 3 tukar: 3 1 4 1 5 → 1 3 4 1 5 → 1 3 1 4 5 → 1 1 3 4 5.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1\n7\n", "3\n2 2 2\n", $make(10, 5), $make(1000, 1000000000), $make(200000, 1000000000), $make(200000, 3),
                "200000\n".T::join(range(200000, 1))."\n", "200000\n".T::join(range(1, 200000))."\n",
            ];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $n = count($a);
            $tmp = $a;
            $inv = 0;
            for ($w = 1; $w < $n; $w *= 2) {
                for ($l = 0; $l < $n; $l += 2 * $w) {
                    $m = min($l + $w, $n);
                    $r = min($l + 2 * $w, $n);
                    $i = $l;
                    $j = $m;
                    $k = $l;
                    while ($i < $m && $j < $r) {
                        if ($a[$i] <= $a[$j]) {
                            $tmp[$k++] = $a[$i++];
                        } else {
                            $inv += $m - $i;
                            $tmp[$k++] = $a[$j++];
                        }
                    }
                    while ($i < $m) {
                        $tmp[$k++] = $a[$i++];
                    }
                    while ($j < $r) {
                        $tmp[$k++] = $a[$j++];
                    }
                }
                [$a, $tmp] = [$tmp, $a];
            }

            return (string) $inv;
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
    for (auto& x : a) cin >> x;

    // Berapa tukar bersebelahan minimum? (petunjuk: inversi)

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// Berapa tukar bersebelahan minimum? (petunjuk: inversi)
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# Berapa tukar bersebelahan minimum? (petunjuk: inversi)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<long long> a, tmp;
long long inv = 0;

void mergeSort(int l, int r) {          // urutkan a[l, r)
    if (r - l <= 1) return;
    int m = l + (r - l) / 2;
    mergeSort(l, m);
    mergeSort(m, r);
    int i = l, j = m, k = l;
    while (i < m && j < r) {
        if (a[i] <= a[j]) tmp[k++] = a[i++];          // sama tinggi: bukan inversi
        else {
            inv += m - i;                             // a[i..m-1] semuanya > a[j]
            tmp[k++] = a[j++];
        }
    }
    while (i < m) tmp[k++] = a[i++];
    while (j < r) tmp[k++] = a[j++];
    for (int t = l; t < r; t++) a[t] = tmp[t];
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    a.resize(n);
    tmp.resize(n);
    for (auto& x : a) cin >> x;
    mergeSort(0, n);
    cout << inv << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
let a = readInts();
let tmp = new Array(n);
let inv = 0;
// merge sort bottom-up (tanpa rekursi)
for (let w = 1; w < n; w *= 2) {
  for (let l = 0; l < n; l += 2 * w) {
    const m = Math.min(l + w, n), r = Math.min(l + 2 * w, n);
    let i = l, j = m, k = l;
    while (i < m && j < r) {
      if (a[i] <= a[j]) tmp[k++] = a[i++];
      else { inv += m - i; tmp[k++] = a[j++]; }
    }
    while (i < m) tmp[k++] = a[i++];
    while (j < r) tmp[k++] = a[j++];
  }
  [a, tmp] = [tmp, a];
}
console.log(String(inv));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
inv = 0
w = 1
while w < n:                       # merge sort bottom-up
    b = []
    for l in range(0, n, 2 * w):
        m, r = min(l + w, n), min(l + 2 * w, n)
        i, j = l, m
        while i < m and j < r:
            if a[i] <= a[j]:
                b.append(a[i]); i += 1
            else:
                inv += m - i
                b.append(a[j]); j += 1
        b.extend(a[i:m]); b.extend(a[j:r])
    a = b
    w *= 2
print(inv)
CODE,
        ],
        'editorial' => '<p>Setiap tukar dua buku bersebelahan mengubah banyak <strong>inversi</strong> (pasangan i &lt; j dengan a<sub>i</sub> &gt; a<sub>j</sub>) tepat satu. Rak terurut punya 0 inversi, dan selalu ada tukar yang mengurangi inversi (dua tetangga yang terbalik). Jadi jawabannya tepat banyak inversi.</p>
<p>Hitung inversi dengan merge sort: saat elemen kanan a[j] diambil lebih dulu, semua sisa elemen kiri <code>a[i..m−1]</code> lebih besar darinya, tambahkan <code>m − i</code>. Pakai <code>&lt;=</code> agar buku sama tinggi tidak dihitung.</p>
<p>O(N log N). Jawaban bisa mencapai N(N−1)/2 ≈ 2 · 10<sup>10</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Apa yang berubah setiap kali dua buku bersebelahan ditukar? Pikirkan pasangan buku yang posisinya "terbalik".',
            'Jawabannya adalah banyak inversi: pasangan i < j dengan a_i > a_j (tegas).',
            'Hitung inversi dengan merge sort: inv += m − i saat mengambil dari kanan. Pakai <= untuk tinggi sama, dan long long.',
        ],
    ],

    [
        'slug' => 'inversi-ganda',
        'lesson' => 'merge-sort',
        'title' => 'Pasangan Jauh Lebih Besar',
        'difficulty' => 'Sulit',
        'tags' => ['merge sort', 'divide and conquer', 'two pointers'],
        'statement' => '<p>Diberikan barisan <code>a<sub>1</sub>, …, a<sub>N</sub></code> (boleh negatif). Pasangan (i, j) disebut <strong>timpang</strong> jika i &lt; j dan <code>a<sub>i</sub> &gt; 2 · a<sub>j</sub></code>.</p>
<p>Hitung banyak pasangan timpang.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak pasangan timpang.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1 3 2 3 1\n", 'explanation' => 'Pasangan timpang: (3, 1) dari a₂ dan a₅, serta (3, 1) dari a₄ dan a₅. Jawaban 2.'],
            ['input' => "4\n-1 -3 -5 2\n", 'explanation' => '−1 &gt; 2·(−3) = −6, −1 &gt; −10, −3 &gt; −10: ada 3 pasangan.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return [
                "1\n5\n", "2\n3 1\n", "2\n2 1\n", $make(10, -5, 5), $make(1000, -1000, 1000), $make(200000, -1000000000, 1000000000),
                $make(200000, 0, 1000000000), $make(200000, -10, 10), "200000\n".T::join(range(200000, 1))."\n",
            ];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $n = count($a);
            $tmp = $a;
            $cnt = 0;
            for ($w = 1; $w < $n; $w *= 2) {
                for ($l = 0; $l < $n; $l += 2 * $w) {
                    $m = min($l + $w, $n);
                    $r = min($l + 2 * $w, $n);
                    $j = $m;
                    for ($i = $l; $i < $m; $i++) {
                        while ($j < $r && $a[$i] > 2 * $a[$j]) {
                            $j++;
                        }
                        $cnt += $j - $m;
                    }
                    $i = $l;
                    $j = $m;
                    $k = $l;
                    while ($i < $m && $j < $r) {
                        $tmp[$k++] = $a[$i] <= $a[$j] ? $a[$i++] : $a[$j++];
                    }
                    while ($i < $m) {
                        $tmp[$k++] = $a[$i++];
                    }
                    while ($j < $r) {
                        $tmp[$k++] = $a[$j++];
                    }
                }
                [$a, $tmp] = [$tmp, $a];
            }

            return (string) $cnt;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<long long> a, tmp;
long long cnt = 0;

void solve(int l, int r) {     // a[l, r)
    if (r - l <= 1) return;
    int m = l + (r - l) / 2;
    solve(l, m);
    solve(m, r);
    // fase 1: hitung pasangan lintas (i di kiri, j di kanan) dengan a[i] > 2 a[j]
    // fase 2: merge biasa
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    a.resize(n);
    tmp.resize(n);
    for (auto& x : a) cin >> x;
    solve(0, n);
    cout << cnt << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
let a = readInts();
// merge sort dengan fase hitung terpisah sebelum merge
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# merge sort dengan fase hitung terpisah sebelum merge
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<long long> a, tmp;
long long cnt = 0;

void solve(int l, int r) {     // a[l, r)
    if (r - l <= 1) return;
    int m = l + (r - l) / 2;
    solve(l, m);
    solve(m, r);
    // Fase 1: kedua bagian sudah urut. Untuk a[i] yang makin besar,
    // banyak a[j] dengan 2*a[j] < a[i] hanya bertambah → j cukup maju.
    int j = m;
    for (int i = l; i < m; i++) {
        while (j < r && a[i] > 2 * a[j]) j++;
        cnt += j - m;
    }
    // Fase 2: merge biasa
    int i = l, k = l;
    j = m;
    while (i < m && j < r) tmp[k++] = (a[i] <= a[j]) ? a[i++] : a[j++];
    while (i < m) tmp[k++] = a[i++];
    while (j < r) tmp[k++] = a[j++];
    for (int t = l; t < r; t++) a[t] = tmp[t];
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    a.resize(n);
    tmp.resize(n);
    for (auto& x : a) cin >> x;
    solve(0, n);
    cout << cnt << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
let a = readInts();
let tmp = new Array(n);
let cnt = 0;
for (let w = 1; w < n; w *= 2) {
  for (let l = 0; l < n; l += 2 * w) {
    const m = Math.min(l + w, n), r = Math.min(l + 2 * w, n);
    let j = m;
    for (let i = l; i < m; i++) {
      while (j < r && a[i] > 2 * a[j]) j++;
      cnt += j - m;
    }
    let i = l, k = l;
    j = m;
    while (i < m && j < r) tmp[k++] = a[i] <= a[j] ? a[i++] : a[j++];
    while (i < m) tmp[k++] = a[i++];
    while (j < r) tmp[k++] = a[j++];
  }
  [a, tmp] = [tmp, a];
}
console.log(String(cnt));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
cnt = 0
w = 1
while w < n:
    b = []
    for l in range(0, n, 2 * w):
        m, r = min(l + w, n), min(l + 2 * w, n)
        j = m
        for i in range(l, m):              # fase hitung
            ai = a[i]
            while j < r and ai > 2 * a[j]:
                j += 1
            cnt += j - m
        i, j = l, m                        # fase merge
        while i < m and j < r:
            if a[i] <= a[j]:
                b.append(a[i]); i += 1
            else:
                b.append(a[j]); j += 1
        b.extend(a[i:m]); b.extend(a[j:r])
    a = b
    w *= 2
print(cnt)
CODE,
        ],
        'editorial' => '<p>Pakai kerangka merge sort (divide and conquer): pasangan timpang terbagi menjadi yang seluruhnya di kiri, seluruhnya di kanan (dihitung rekursif), dan yang <strong>lintas</strong> (i di kiri, j di kanan).</p>
<p>Berbeda dengan inversi biasa, syarat <code>a[i] &gt; 2·a[j]</code> tidak selaras dengan urutan pengambilan saat merge. Jadi hitung pasangan lintas dalam <strong>fase terpisah</strong> sebelum merge. Kedua bagian sudah urut menaik; saat i maju, a[i] membesar, sehingga himpunan j dengan <code>2·a[j] &lt; a[i]</code> hanya bertambah. Satu penunjuk j yang hanya maju (two pointers) menghitung semuanya dalam O(panjang).</p>
<p>Setelah itu lakukan merge biasa. Total O(N log N). Perhatikan <code>2·a[j]</code> sampai 2 · 10<sup>9</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Pakai divide and conquer seperti menghitung inversi: pisahkan pasangan lintas (i kiri, j kanan).',
            'Syarat a[i] > 2·a[j] tidak bisa dihitung sambil merge. Hitunglah di fase terpisah ketika kedua bagian sudah urut.',
            'Fase hitung dengan two pointers: untuk i dari l ke m−1, majukan j selama a[i] > 2·a[j], tambahkan j − m. Lalu merge biasa.',
        ],
    ],

    // ───────────────────────── Quick Sort & Quickselect ─────────────────────────
    [
        'slug' => 'median-nilai',
        'lesson' => 'quick-sort',
        'title' => 'Median Pendapatan',
        'difficulty' => 'Mudah',
        'tags' => ['quickselect', 'nth_element', 'median'],
        'statement' => '<p>Sebuah survei mencatat pendapatan <strong>N</strong> warga (N ganjil). Median adalah nilai yang berada tepat di tengah jika semua pendapatan diurutkan.</p>
<p>Cetak median pendapatan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N bilangan.</p>',
        'output_format' => '<p>Satu bilangan: median.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000, N ganjil</li><li>0 ≤ pendapatan ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n700 200 900 400 300\n", 'explanation' => 'Urut: 200 300 400 700 900. Yang di tengah adalah 400.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 0, $hi))."\n";
            };

            return ["1\n42\n", "3\n5 5 1\n", $make(11, 20), $make(999, 1000000000), $make(199999, 1000000000), $make(199999, 2), "199999\n".T::join(range(199999, 1))."\n"];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            sort($a);

            return (string) $a[intdiv(count($a), 2)];
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
    for (auto& x : a) cin >> x;

    // median tanpa mengurutkan semuanya

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// median
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# median
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
    for (auto& x : a) cin >> x;

    // quickselect bawaan: a[n/2] menjadi elemen yang benar, sisanya tidak perlu urut
    nth_element(a.begin(), a.begin() + n / 2, a.end());
    cout << a[n / 2] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = Float64Array.from(readInts()).sort();   // typed array: urut numerik
console.log(String(a[n >> 1]));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = sorted(map(int, input().split()))
print(a[n // 2])
CODE,
        ],
        'editorial' => '<p>Median adalah elemen terkecil ke-(N/2) (0-based). Mengurutkan semuanya (O(N log N)) sudah cukup cepat, tetapi tidak perlu: <code>nth_element(a.begin(), a.begin() + n/2, a.end())</code> menempatkan elemen yang benar di posisi n/2 dalam O(N) rata-rata, memakai ide quickselect.</p>
<p>Setelah <code>nth_element</code>, elemen di kiri posisi itu semuanya ≤ dan di kanannya ≥, tetapi keduanya tidak terurut.</p>',
        'hints' => [
            'Median = elemen ke-(N/2) (0-based) setelah diurutkan.',
            'Kamu tidak perlu seluruh urutan, hanya satu posisi.',
            'nth_element(a.begin(), a.begin() + n/2, a.end()); jawabannya a[n/2].',
        ],
    ],

    [
        'slug' => 'bendera-tiga-warna',
        'lesson' => 'quick-sort',
        'title' => 'Tiga Warna Bendera',
        'difficulty' => 'Sedang',
        'tags' => ['partisi', 'greedy', 'counting'],
        'statement' => '<p>Ada <strong>N</strong> kain berjajar, masing-masing berwarna <code>1</code> (merah), <code>2</code> (putih), atau <code>3</code> (biru). Panitia ingin kain tersusun: semua merah di depan, lalu semua putih, lalu semua biru.</p>
<p>Dalam satu langkah, panitia boleh menukar posisi <strong>dua kain mana saja</strong>. Berapa langkah minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N bilangan (masing-masing 1, 2, atau 3).</p>',
        'output_format' => '<p>Satu bilangan: banyak tukar minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "9\n2 2 1 3 3 3 2 3 1\n", 'explanation' => 'Target: 1 1 2 2 2 3 3 3 3. Satu tukar 2 ↔ 1 langsung membereskan dua kain; sisanya membentuk siklus tiga warna yang butuh 2 tukar. Total 4.'],
        ],
        'tests' => function () {
            $make = function (int $n, array $w) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $r = mt_rand(1, array_sum($w));
                    $a[] = $r <= $w[0] ? 1 : ($r <= $w[0] + $w[1] ? 2 : 3);
                }

                return "$n\n".T::join($a)."\n";
            };
            $cyc = function (int $k) {
                // pola 2 3 1 diulang: hanya siklus tiga
                $a = array_merge(array_fill(0, $k, 2), array_fill(0, $k, 3), array_fill(0, $k, 1));

                return (3 * $k)."\n".T::join($a)."\n";
            };

            return ["1\n3\n", "3\n3 2 1\n", "3\n2 3 1\n", $make(10, [1, 1, 1]), $make(1000, [1, 2, 3]), $make(200000, [1, 1, 1]), $make(200000, [5, 1, 1]), $cyc(66666), "6\n1 1 2 2 3 3\n"];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $c = [1 => 0, 2 => 0, 3 => 0];
            foreach ($a as $x) {
                $c[$x]++;
            }
            $m = [1 => [1 => 0, 2 => 0, 3 => 0], 2 => [1 => 0, 2 => 0, 3 => 0], 3 => [1 => 0, 2 => 0, 3 => 0]];
            foreach ($a as $i => $x) {
                $seg = $i < $c[1] ? 1 : ($i < $c[1] + $c[2] ? 2 : 3);
                $m[$x][$seg]++;
            }
            $swaps = 0;
            $wrong = 0;
            foreach ([[1, 2], [1, 3], [2, 3]] as [$x, $y]) {
                $t = min($m[$x][$y], $m[$y][$x]);
                $swaps += $t;
                $m[$x][$y] -= $t;
                $m[$y][$x] -= $t;
            }
            foreach ([1, 2, 3] as $x) {
                foreach ([1, 2, 3] as $y) {
                    if ($x !== $y) {
                        $wrong += $m[$x][$y];
                    }
                }
            }

            return (string) ($swaps + intdiv($wrong, 3) * 2);
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
    for (auto& x : a) cin >> x;

    // m[x][y] = banyak kain warna x yang berada di wilayah warna y

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// m[x][y] = banyak kain warna x yang berada di wilayah warna y
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# m[x][y] = banyak kain warna x yang berada di wilayah warna y
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
    for (auto& x : a) cin >> x;

    int c[4] = {0, 0, 0, 0};
    for (int x : a) c[x]++;
    // wilayah: [0, c1) untuk 1, [c1, c1+c2) untuk 2, sisanya untuk 3
    long long m[4][4] = {{0}};
    for (int i = 0; i < n; i++) {
        int wil = (i < c[1]) ? 1 : (i < c[1] + c[2] ? 2 : 3);
        m[a[i]][wil]++;
    }
    long long tukar = 0;
    // 1) pasangan yang saling salah tempat: satu tukar membereskan dua kain
    int px[3] = {1, 1, 2}, py[3] = {2, 3, 3};
    for (int k = 0; k < 3; k++) {
        long long t = min(m[px[k]][py[k]], m[py[k]][px[k]]);
        tukar += t;
        m[px[k]][py[k]] -= t;
        m[py[k]][px[k]] -= t;
    }
    // 2) sisa salah tempat membentuk siklus tiga (1→2→3→1): 3 kain butuh 2 tukar
    long long sisa = 0;
    for (int x = 1; x <= 3; x++)
        for (int y = 1; y <= 3; y++)
            if (x != y) sisa += m[x][y];
    tukar += sisa / 3 * 2;
    cout << tukar << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
const c = [0, 0, 0, 0];
for (const x of a) c[x]++;
const m = [0, 1, 2, 3].map(() => [0, 0, 0, 0]);
a.forEach((x, i) => {
  const w = i < c[1] ? 1 : i < c[1] + c[2] ? 2 : 3;
  m[x][w]++;
});
let tukar = 0;
for (const [x, y] of [[1, 2], [1, 3], [2, 3]]) {
  const t = Math.min(m[x][y], m[y][x]);
  tukar += t;
  m[x][y] -= t;
  m[y][x] -= t;
}
let sisa = 0;
for (let x = 1; x <= 3; x++) for (let y = 1; y <= 3; y++) if (x !== y) sisa += m[x][y];
tukar += (sisa / 3) * 2;
console.log(String(tukar));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
c = [0, a.count(1), a.count(2), a.count(3)]
m = [[0] * 4 for _ in range(4)]
for i, x in enumerate(a):
    w = 1 if i < c[1] else (2 if i < c[1] + c[2] else 3)
    m[x][w] += 1
tukar = 0
for x, y in ((1, 2), (1, 3), (2, 3)):
    t = min(m[x][y], m[y][x])
    tukar += t
    m[x][y] -= t
    m[y][x] -= t
sisa = sum(m[x][y] for x in range(1, 4) for y in range(1, 4) if x != y)
print(tukar + sisa // 3 * 2)
CODE,
        ],
        'editorial' => '<p>Susunan akhir sudah pasti: c<sub>1</sub> kain merah di depan, lalu c<sub>2</sub> putih, lalu c<sub>3</sub> biru. Bagi posisi menjadi tiga <strong>wilayah</strong> dan hitung <code>m[x][y]</code> = banyak kain warna x yang berada di wilayah warna y.</p>
<ol><li><strong>Tukar dua arah dulu (greedy).</strong> Kain merah di wilayah putih dan kain putih di wilayah merah bisa saling ditukar: satu tukar membereskan dua kain sekaligus. Lakukan sebanyak <code>min(m[x][y], m[y][x])</code> untuk setiap pasangan warna.</li>
<li><strong>Sisanya pasti siklus tiga.</strong> Setelah langkah 1, kain yang salah tempat hanya bisa berputar 1 → 2 → 3 → 1 (atau arah sebaliknya). Setiap tiga kain seperti itu butuh tepat 2 tukar.</li></ol>
<p>Jawaban = (banyak tukar dua arah) + 2 · (sisa / 3). Ini contoh partisi tiga arah yang dinilai berdasarkan banyak tukar. O(N).</p>',
        'hints' => [
            'Susunan akhirnya sudah pasti. Kelompokkan posisi menjadi wilayah merah, putih, biru.',
            'Hitung m[x][y]: kain warna x di wilayah y. Kain merah di wilayah putih dan putih di wilayah merah bisa dibereskan dengan satu tukar.',
            'Setelah semua tukar dua arah, sisa yang salah tempat membentuk siklus tiga: setiap 3 kain butuh 2 tukar.',
        ],
    ],

    // ───────────────────────── Counting, Radix & Bucket Sort ─────────────────────────
    [
        'slug' => 'histogram-nilai',
        'lesson' => 'counting-sort',
        'title' => 'Rekap Nilai Ujian',
        'difficulty' => 'Mudah',
        'tags' => ['counting sort', 'frekuensi'],
        'statement' => '<p>Guru punya <strong>N</strong> nilai ujian (bilangan bulat 0 sampai 100). Ia ingin rekap: untuk setiap nilai yang <strong>muncul</strong>, berapa siswa yang mendapatkannya. Rekap diurutkan dari nilai <strong>tertinggi</strong>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N nilai.</p>',
        'output_format' => '<p>Setiap baris berisi <code>nilai banyak</code>, dari nilai tertinggi ke terendah. Nilai yang tidak muncul tidak dicetak.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1 000 000</li><li>0 ≤ nilai ≤ 100</li></ul>',
        'samples' => [
            ['input' => "8\n70 85 70 100 0 85 70 60\n", 'explanation' => 'Nilai 100 sekali, 85 dua kali, 70 tiga kali, 60 sekali, 0 sekali.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return ["1\n0\n", $make(10, 60, 70), $make(1000, 0, 100), $make(1000000, 0, 100), $make(1000000, 100, 100), $make(500000, 40, 60)];
        },
        'solve' => function (string $input) {
            $cnt = array_count_values(T::ints(T::lines($input)[1]));
            krsort($cnt);
            $out = [];
            foreach ($cnt as $v => $c) {
                $out[] = "$v $c";
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

    int n;
    cin >> n;
    int cnt[101] = {0};
    // hitung, lalu cetak dari 100 turun ke 0

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// hitung, lalu cetak dari 100 turun ke 0
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# hitung, lalu cetak dari 100 turun ke 0
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
    int cnt[101] = {0};
    for (int i = 0; i < n; i++) {
        int x;
        cin >> x;
        cnt[x]++;
    }
    for (int v = 100; v >= 0; v--)
        if (cnt[v] > 0) cout << v << " " << cnt[v] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
const cnt = new Int32Array(101);
for (const x of a) cnt[x]++;
const out = [];
for (let v = 100; v >= 0; v--) if (cnt[v] > 0) out.push(v + " " + cnt[v]);
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
n = int(data[0])
cnt = [0] * 101
for x in data[1:1 + n]:
    cnt[int(x)] += 1
print("\n".join(f"{v} {cnt[v]}" for v in range(100, -1, -1) if cnt[v]))
CODE,
        ],
        'editorial' => '<p>Nilai hanya 0..100, jadi siapkan 101 "ember" <code>cnt[0..100]</code>. Satu kali jalan untuk menghitung, lalu satu kali jalan dari 100 turun ke 0 untuk mencetak ember yang tidak kosong.</p>
<p>Total O(N + 101), tanpa sorting berbasis perbandingan sama sekali. Untuk N = 10<sup>6</sup>, ini jauh lebih ringan daripada <code>map</code> atau <code>sort</code>.</p>',
        'hints' => [
            'Nilai hanya 101 kemungkinan.',
            'Siapkan array cnt[101] dan hitung kemunculan setiap nilai.',
            'Cetak dari v = 100 turun ke 0, hanya yang cnt[v] > 0.',
        ],
    ],

    [
        'slug' => 'celah-maksimum',
        'lesson' => 'counting-sort',
        'title' => 'Celah Terlebar',
        'difficulty' => 'Sedang',
        'tags' => ['bucket', 'sorting', 'sarang merpati'],
        'statement' => '<p>Ada <strong>N</strong> pos di sepanjang jalan tol, pos ke-i di kilometer <code>x<sub>i</sub></code> (tidak urut, boleh ada yang sama). Pengelola ingin tahu <strong>jarak terjauh antara dua pos yang bersebelahan</strong> (setelah pos-pos diurutkan menurut posisinya).</p>
<p>Jika hanya ada satu pos, jawabannya 0.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>x<sub>1</sub> … x<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: celah terlebar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ x<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6\n13 2 40 25 7 13\n", 'explanation' => 'Urut: 2 7 13 13 25 40. Celah: 5, 6, 0, 12, 15. Yang terlebar 15.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 0, $hi))."\n";
            };

            return ["1\n500\n", "2\n0 1000000000\n", "4\n5 5 5 5\n", $make(10, 100), $make(1000, 1000000000), $make(200000, 1000000000), $make(200000, 300000), $make(200000, 5)];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            sort($a);
            $best = 0;
            for ($i = 1, $n = count($a); $i < $n; $i++) {
                $best = max($best, $a[$i] - $a[$i - 1]);
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
    vector<long long> x(n);
    for (auto& v : x) cin >> v;

    // celah terlebar setelah diurutkan

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const x = readInts();
// celah terlebar setelah diurutkan
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
x = list(map(int, input().split()))
# celah terlebar setelah diurutkan
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
    vector<long long> x(n);
    for (auto& v : x) cin >> v;
    long long mn = *min_element(x.begin(), x.end());
    long long mx = *max_element(x.begin(), x.end());
    if (n == 1 || mn == mx) {
        cout << 0 << '\n';
        return 0;
    }
    // Bucket sort O(n): n ember berlebar ceil((mx - mn) / (n - 1)).
    // Celah terlebar >= lebar ember, jadi tidak mungkin di dalam satu ember.
    long long lebar = max(1LL, (mx - mn + n - 2) / (n - 1));
    int m = (mx - mn) / lebar + 1;
    vector<long long> bmin(m, LLONG_MAX), bmax(m, LLONG_MIN);
    for (long long v : x) {
        int b = (v - mn) / lebar;
        bmin[b] = min(bmin[b], v);
        bmax[b] = max(bmax[b], v);
    }
    long long best = 0, prev = LLONG_MIN;
    for (int b = 0; b < m; b++) {
        if (bmin[b] == LLONG_MAX) continue;            // ember kosong
        if (prev != LLONG_MIN) best = max(best, bmin[b] - prev);
        prev = bmax[b];
    }
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const x = Float64Array.from(readInts()).sort();
let best = 0;
for (let i = 1; i < n; i++) best = Math.max(best, x[i] - x[i - 1]);
console.log(String(best));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
x = sorted(map(int, input().split()))
print(max((x[i] - x[i - 1] for i in range(1, n)), default=0))
CODE,
        ],
        'editorial' => '<p>Cara langsung: urutkan, lalu cari selisih bersebelahan terbesar. O(N log N), dan sudah cukup cepat.</p>
<p>Ada juga cara <strong>O(N)</strong> dengan ide ember. Misalkan nilai terkecil mn dan terbesar mx. Ada N − 1 celah yang jumlahnya mx − mn, jadi celah terlebar ≥ (mx − mn) / (N − 1) (prinsip sarang merpati). Buat ember-ember selebar itu: dua pos di ember yang sama berjarak kurang dari lebar ember, sehingga celah terlebar <strong>pasti melintasi ember</strong>. Cukup simpan min dan max setiap ember, lalu bandingkan max sebuah ember dengan min ember tidak kosong berikutnya.</p>
<p>Solusi C++ di atas memakai cara ember; JavaScript dan Python memakai cara sort.</p>',
        'hints' => [
            'Cara paling sederhana: urutkan, lalu periksa setiap pasangan bersebelahan.',
            'Tantangan: bisakah tanpa sorting penuh? Celah terlebar paling sedikit (max − min) / (N − 1).',
            'Bagi rentang ke ember selebar itu; celah terlebar pasti antara max suatu ember dan min ember tidak kosong berikutnya.',
        ],
    ],

    // ───────────────────────── Binary Search & Two Pointers (materi lama) ─────────────────────────
    [
        'slug' => 'potong-kabel',
        'lesson' => 'binary-search',
        'title' => 'Potong Kabel',
        'difficulty' => 'Sedang',
        'tags' => ['binary search pada jawaban'],
        'statement' => '<p>Seorang teknisi punya <strong>N</strong> gulungan kabel dengan panjang <code>p<sub>1</sub>, …, p<sub>N</sub></code> meter. Ia butuh <strong>K</strong> potongan kabel yang <strong>sama panjang</strong> (panjangnya bilangan bulat meter). Setiap gulungan boleh dipotong menjadi beberapa potongan, tetapi potongan dari gulungan berbeda tidak boleh disambung. Sisa kabel boleh dibuang.</p>
<p>Berapa panjang potongan <strong>terbesar</strong> yang mungkin? Jika bahkan potongan sepanjang 1 meter tidak cukup, cetak 0.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>p<sub>1</sub> … p<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: panjang potongan terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ K ≤ 10<sup>9</sup></li><li>1 ≤ p<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 11\n802 743 457 539\n", 'explanation' => 'Dengan panjang 200: 4 + 3 + 2 + 2 = 11 potongan, cukup. Dengan 201: 3 + 3 + 2 + 2 = 10, kurang. Jadi 200.'],
            ['input' => "2 10\n3 4\n", 'explanation' => 'Total kabel hanya 7 meter, tidak mungkin dapat 10 potongan. Jawaban 0.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $hi) {
                return "$n $k\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1 1\n1000000000\n", "1 1000000000\n1000000000\n", $make(10, 7, 100), $make(1000, 5000, 1000000), $make(200000, 1000000000, 1000000000),
                $make(200000, 1, 1000000000), $make(200000, 123456, 1000000000), $make(200000, 1000000000, 10),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $p = T::ints($lines[1]);
            $lo = 0;
            $hi = max($p);
            while ($lo < $hi) {
                $mid = $lo + intdiv($hi - $lo + 1, 2);
                $c = 0;
                foreach ($p as $x) {
                    $c += intdiv($x, $mid);
                    if ($c >= $k) {
                        break;
                    }
                }
                if ($c >= $k) {
                    $lo = $mid;
                } else {
                    $hi = $mid - 1;
                }
            }

            return (string) $lo;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long k;
    cin >> n >> k;
    vector<long long> p(n);
    for (auto& x : p) cin >> x;

    // cukup(L): apakah panjang L memberi >= k potongan?

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const p = readInts();
// cukup(L): apakah panjang L memberi >= k potongan?
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
p = list(map(int, input().split()))
# cukup(L): apakah panjang L memberi >= k potongan?
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
long long k;
vector<long long> p;

bool cukup(long long L) {               // monoton: makin panjang, makin sedikit potongan
    long long potong = 0;
    for (long long x : p) {
        potong += x / L;
        if (potong >= k) return true;   // berhenti cepat, juga mencegah overflow
    }
    return false;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> k;
    p.resize(n);
    for (auto& x : p) cin >> x;

    long long lo = 0, hi = *max_element(p.begin(), p.end());   // lo = 0 selalu "aman"
    while (lo < hi) {
        long long mid = lo + (hi - lo + 1) / 2;                // bulatkan ke atas
        if (cukup(mid)) lo = mid;
        else hi = mid - 1;
    }
    cout << lo << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const p = readInts();
const cukup = (L) => {
  let c = 0;
  for (const x of p) {
    c += Math.floor(x / L);
    if (c >= k) return true;
  }
  return false;
};
let lo = 0, hi = 0;
for (const x of p) if (x > hi) hi = x;
while (lo < hi) {
  const mid = lo + Math.floor((hi - lo + 1) / 2);
  if (cukup(mid)) lo = mid;
  else hi = mid - 1;
}
console.log(String(lo));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
p = list(map(int, input().split()))

def cukup(L):
    return sum(x // L for x in p) >= k

lo, hi = 0, max(p)
while lo < hi:
    mid = (lo + hi + 1) // 2
    if cukup(mid):
        lo = mid
    else:
        hi = mid - 1
print(lo)
CODE,
        ],
        'editorial' => '<p>Untuk panjang L tertentu, banyak potongan mudah dihitung: <code>Σ ⌊p<sub>i</sub> / L⌋</code> dalam O(N). Fungsi ini <strong>monoton turun</strong> terhadap L: makin panjang potongannya, makin sedikit jumlahnya. Jadi ada batas: semua L sampai batas itu cukup, dan semua L di atasnya tidak.</p>
<p>Binary search pada jawaban L ∈ [0, max p]. Invarian: <code>lo</code> selalu cukup (L = 0 dianggap selalu cukup, sebagai penjaga untuk jawaban 0). Pola "cari terbesar yang memenuhi" butuh <code>mid</code> dibulatkan ke atas agar tidak terjebak loop tak berujung.</p>
<p>O(N log max p) ≈ 2 · 10<sup>5</sup> · 30. Hentikan penjumlahan begitu mencapai K agar tidak overflow (jumlah bisa mencapai 2 · 10<sup>14</sup>).</p>',
        'hints' => [
            'Jika panjang L cukup untuk K potongan, apakah panjang yang lebih pendek juga cukup?',
            'Binary search pada L. Untuk L tertentu, banyak potongan = jumlah p_i / L (dibulatkan ke bawah).',
            'Cari L terbesar yang cukup: lo = 0, hi = max p, mid = lo + (hi − lo + 1)/2, jika cukup lo = mid, jika tidak hi = mid − 1.',
        ],
    ],

    [
        'slug' => 'jarak-sapi',
        'lesson' => 'binary-search',
        'title' => 'Kandang Sapi yang Damai',
        'difficulty' => 'Sedang',
        'tags' => ['binary search pada jawaban', 'greedy'],
        'statement' => '<p>Peternak punya <strong>N</strong> kandang di sepanjang jalan, di posisi <code>x<sub>1</sub>, …, x<sub>N</sub></code> (semua berbeda). Ia harus menaruh <strong>C</strong> sapi, satu sapi per kandang. Sapi-sapinya mudah bertengkar, jadi peternak ingin <strong>jarak terdekat antara dua sapi</strong> sebesar mungkin.</p>
<p>Berapa nilai terbesar dari jarak terdekat itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N C</code>. Baris kedua berisi <code>x<sub>1</sub> … x<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: jarak terdekat terbesar.</p>',
        'constraints' => '<ul><li>2 ≤ C ≤ N ≤ 200 000</li><li>0 ≤ x<sub>i</sub> ≤ 10<sup>9</sup>, semuanya berbeda</li></ul>',
        'samples' => [
            ['input' => "5 3\n1 2 8 4 9\n", 'explanation' => 'Taruh sapi di kandang 1, 4, dan 8 (atau 9): jarak terdekat 3. Jarak 4 tidak mungkin dicapai untuk tiga sapi.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $c, int $hi) {
                $seen = [];
                while (count($seen) < $n) {
                    $seen[mt_rand(0, $hi)] = true;
                }
                $x = array_keys($seen);
                T::shuffle($x);

                return "$n $c\n".T::join($x)."\n";
            };

            return [
                "2 2\n0 1000000000\n", $make(10, 3, 30), $make(10, 10, 30), $make(1000, 50, 1000000), $make(200000, 2, 1000000000),
                $make(200000, 1000, 1000000000), $make(200000, 200000, 1000000000), $make(200000, 777, 300000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $c] = T::ints($lines[0]);
            $x = T::ints($lines[1]);
            sort($x);
            $lo = 1;
            $hi = intdiv($x[$n - 1] - $x[0], $c - 1);
            while ($lo < $hi) {
                $mid = $lo + intdiv($hi - $lo + 1, 2);
                $cnt = 1;
                $last = $x[0];
                for ($i = 1; $i < $n && $cnt < $c; $i++) {
                    if ($x[$i] - $last >= $mid) {
                        $cnt++;
                        $last = $x[$i];
                    }
                }
                if ($cnt >= $c) {
                    $lo = $mid;
                } else {
                    $hi = $mid - 1;
                }
            }

            return (string) $lo;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, c;
    cin >> n >> c;
    vector<long long> x(n);
    for (auto& v : x) cin >> v;
    sort(x.begin(), x.end());

    // bisa(D): bisakah C sapi ditaruh dengan jarak antar sapi >= D?

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, c] = readInts();
const x = Float64Array.from(readInts()).sort();
// bisa(D): bisakah C sapi ditaruh dengan jarak antar sapi >= D?
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, c = map(int, input().split())
x = sorted(map(int, input().split()))
# bisa(D): bisakah C sapi ditaruh dengan jarak antar sapi >= D?
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n, c;
vector<long long> x;

// Greedy: taruh sapi di kandang paling kiri yang masih berjarak >= D dari sapi terakhir
bool bisa(long long D) {
    int sapi = 1;
    long long terakhir = x[0];
    for (int i = 1; i < n && sapi < c; i++) {
        if (x[i] - terakhir >= D) {
            sapi++;
            terakhir = x[i];
        }
    }
    return sapi >= c;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> c;
    x.resize(n);
    for (auto& v : x) cin >> v;
    sort(x.begin(), x.end());

    long long lo = 1, hi = (x[n - 1] - x[0]) / (c - 1);    // batas atas: rata-rata celah
    while (lo < hi) {
        long long mid = lo + (hi - lo + 1) / 2;
        if (bisa(mid)) lo = mid;
        else hi = mid - 1;
    }
    cout << lo << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, c] = readInts();
const x = Float64Array.from(readInts()).sort();
const bisa = (D) => {
  let sapi = 1, terakhir = x[0];
  for (let i = 1; i < n && sapi < c; i++) {
    if (x[i] - terakhir >= D) { sapi++; terakhir = x[i]; }
  }
  return sapi >= c;
};
let lo = 1, hi = Math.floor((x[n - 1] - x[0]) / (c - 1));
while (lo < hi) {
  const mid = lo + Math.floor((hi - lo + 1) / 2);
  if (bisa(mid)) lo = mid;
  else hi = mid - 1;
}
console.log(String(lo));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, c = map(int, input().split())
x = sorted(map(int, input().split()))

def bisa(D):
    sapi, terakhir = 1, x[0]
    for v in x:
        if v - terakhir >= D:
            sapi += 1
            terakhir = v
            if sapi >= c:
                return True
    return sapi >= c

lo, hi = 1, (x[-1] - x[0]) // (c - 1)
while lo < hi:
    mid = (lo + hi + 1) // 2
    if bisa(mid):
        lo = mid
    else:
        hi = mid - 1
print(lo)
CODE,
        ],
        'editorial' => '<p>Soal "maksimumkan nilai minimum" hampir selalu <strong>binary search pada jawaban</strong>. Tanyakan: untuk jarak D tertentu, <em>bisakah</em> C sapi ditaruh dengan semua jarak ≥ D? Jika bisa untuk D, maka bisa juga untuk semua D yang lebih kecil: monoton.</p>
<p>Memeriksa satu D cukup dengan <strong>greedy</strong>: urutkan kandang, taruh sapi pertama di kandang paling kiri, lalu setiap sapi berikutnya di kandang paling kiri yang berjarak ≥ D dari sapi terakhir. Menaruh sedini mungkin tidak pernah merugikan, karena menyisakan ruang paling banyak di kanan.</p>
<p>Batas atas jawaban adalah <code>(x<sub>maks</sub> − x<sub>min</sub>) / (C − 1)</code>. Total O(N log N + N log(rentang)).</p>',
        'hints' => [
            'Ubah pertanyaannya: untuk jarak D tertentu, bisakah semua sapi ditaruh dengan jarak ≥ D?',
            'Memeriksa D cukup greedy: setelah diurutkan, taruh setiap sapi di kandang paling kiri yang masih berjarak ≥ D.',
            'bisa(D) monoton, jadi binary search D terbesar yang bisa (mid dibulatkan ke atas).',
        ],
    ],

    // ───────────────────────── Ternary Search ─────────────────────────
    [
        'slug' => 'jarak-kapal',
        'lesson' => 'ternary-search',
        'title' => 'Jarak Terdekat Dua Kapal',
        'difficulty' => 'Mudah',
        'tags' => ['ternary search', 'fungsi cembung'],
        'statement' => '<p>Dua kapal bergerak lurus dengan kecepatan tetap. Pada menit ke-t, kapal A berada di <code>(ax + avx·t, ay + avy·t)</code> dan kapal B di <code>(bx + bvx·t, by + bvy·t)</code>.</p>
<p>Radar hanya mencatat posisi pada menit bulat <code>t = 0, 1, …, T</code>. Untuk setiap pertanyaan, tentukan <strong>kuadrat jarak terkecil</strong> antara kedua kapal di antara menit-menit itu.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi sembilan bilangan <code>ax ay avx avy bx by bvx bvy T</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, satu baris berisi kuadrat jarak terkecil.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 20 000</li><li>|ax|, |ay|, |bx|, |by| ≤ 10<sup>6</sup></li><li>|avx|, |avy|, |bvx|, |bvy| ≤ 10</li><li>0 ≤ T ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n0 0 1 0 10 1 -1 0 10\n0 0 1 1 5 0 1 1 7\n0 0 0 0 3 4 -1 -1 2\n", 'explanation' => 'Pertanyaan 1: selisih posisi (−10 + 2t, −1), terdekat di t = 5: 0² + 1² = 1. Pertanyaan 2: kecepatannya sama, jaraknya tetap 25. Pertanyaan 3: kapal B mendekat sampai t = 3,5, tetapi radar berhenti di t = 2: (−1)² + (−2)² = 5.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $tMax, string $mode) {
                $rows = [];
                for ($k = 0; $k < $q; $k++) {
                    $T = mt_rand(0, $tMax);
                    $v = T::arr(4, -10, 10);
                    if ($mode === 'diam' && $k % 3 == 0) {
                        $v[2] = $v[0];
                        $v[3] = $v[1];
                    }
                    $ax = mt_rand(-1000000, 1000000);
                    $ay = mt_rand(-1000000, 1000000);
                    if ($mode === 'acak') {
                        $bx = mt_rand(-1000000, 1000000);
                        $by = mt_rand(-1000000, 1000000);
                    } else {
                        // buat kedua kapal berpapasan dekat menit t0
                        $t0 = mt_rand(0, $T);
                        $bx = max(-1000000, min(1000000, $ax + ($v[0] - $v[2]) * $t0 + mt_rand(-30, 30)));
                        $by = max(-1000000, min(1000000, $ay + ($v[1] - $v[3]) * $t0 + mt_rand(-30, 30)));
                    }
                    $rows[] = "$ax $ay {$v[0]} {$v[1]} $bx $by {$v[2]} {$v[3]} $T";
                }

                return count($rows)."\n".implode("\n", $rows)."\n";
            };

            return [
                "3\n0 0 0 0 0 0 0 0 0\n-1000000 -1000000 10 10 1000000 1000000 -10 -10 1000000\n1000000 0 -10 0 -1000000 7 10 0 1000000\n",
                $gen(10, 20, 'dekat'), $gen(200, 1000, 'acak'), $gen(20000, 1000000, 'dekat'), $gen(20000, 1000000, 'acak'),
                $gen(20000, 1000000, 'diam'), $gen(20000, 100, 'dekat'), $gen(20000, 3, 'dekat'),
            ];
        },
        'solve' => function (string $input) {
            // Referensi memakai rumus puncak parabola (berbeda dari solusi ternary search)
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($k = 1; $k <= $q; $k++) {
                [$ax, $ay, $avx, $avy, $bx, $by, $bvx, $bvy, $T] = T::ints($lines[$k]);
                $dx = $ax - $bx;
                $dy = $ay - $by;
                $vx = $avx - $bvx;
                $vy = $avy - $bvy;
                $vv = $vx * $vx + $vy * $vy;
                $cand = [0, $T];
                if ($vv > 0) {
                    $c = (int) floor(-($dx * $vx + $dy * $vy) / $vv);
                    $cand[] = $c;
                    $cand[] = $c + 1;
                }
                $best = PHP_INT_MAX;
                foreach ($cand as $t) {
                    $t = max(0, min($T, $t));
                    $x = $dx + $vx * $t;
                    $y = $dy + $vy * $t;
                    $best = min($best, $x * $x + $y * $y);
                }
                $out[] = $best;
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
        long long ax, ay, avx, avy, bx, by, bvx, bvy, T;
        cin >> ax >> ay >> avx >> avy >> bx >> by >> bvx >> bvy >> T;
        // f(t) = kuadrat jarak pada menit t. Cari minimumnya untuk t di [0, T].
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = readInts()[0];
const out = [];
for (let k = 0; k < q; k++) {
  const [ax, ay, avx, avy, bx, by, bvx, bvy, T] = readInts();
  // f(t) = kuadrat jarak pada menit t. Cari minimumnya untuk t di [0, T].
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    ax, ay, avx, avy, bx, by, bvx, bvy, T = map(int, input().split())
    # f(t) = kuadrat jarak pada menit t. Cari minimumnya untuk t di [0, T].
print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

long long dx, dy, vx, vy;          // posisi dan kecepatan A relatif terhadap B

long long f(long long t) {         // kuadrat jarak pada menit t: parabola, cembung
    long long x = dx + vx * t, y = dy + vy * t;
    return x * x + y * y;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    while (q--) {
        long long ax, ay, avx, avy, bx, by, bvx, bvy, T;
        cin >> ax >> ay >> avx >> avy >> bx >> by >> bvx >> bvy >> T;
        dx = ax - bx;
        dy = ay - by;
        vx = avx - bvx;
        vy = avy - bvy;

        long long lo = 0, hi = T;
        while (hi - lo > 2) {
            long long m1 = lo + (hi - lo) / 3;
            long long m2 = hi - (hi - lo) / 3;
            if (f(m1) < f(m2)) hi = m2 - 1;
            else lo = m1 + 1;
        }
        long long best = f(lo);
        for (long long t = lo + 1; t <= hi; t++) best = min(best, f(t));
        cout << best << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = readInts()[0];
const out = [];
for (let k = 0; k < q; k++) {
  const [ax, ay, avx, avy, bx, by, bvx, bvy, T] = readInts();
  const dx = ax - bx, dy = ay - by, vx = avx - bvx, vy = avy - bvy;
  const f = (t) => {
    const x = dx + vx * t, y = dy + vy * t;
    return x * x + y * y;
  };
  let lo = 0, hi = T;
  while (hi - lo > 2) {
    const m1 = lo + Math.floor((hi - lo) / 3), m2 = hi - Math.floor((hi - lo) / 3);
    if (f(m1) < f(m2)) hi = m2 - 1;
    else lo = m1 + 1;
  }
  let best = f(lo);
  for (let t = lo + 1; t <= hi; t++) best = Math.min(best, f(t));
  out.push(best);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    ax, ay, avx, avy, bx, by, bvx, bvy, T = map(int, input().split())
    dx, dy, vx, vy = ax - bx, ay - by, avx - bvx, avy - bvy

    def f(t):
        x, y = dx + vx * t, dy + vy * t
        return x * x + y * y

    lo, hi = 0, T
    while hi - lo > 2:
        m1 = lo + (hi - lo) // 3
        m2 = hi - (hi - lo) // 3
        if f(m1) < f(m2):
            hi = m2 - 1
        else:
            lo = m1 + 1
    out.append(str(min(f(t) for t in range(lo, hi + 1))))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Lihat kapal A dari sudut pandang kapal B: selisih posisinya <code>D + V·t</code> dengan D = (ax − bx, ay − by) dan V = (avx − bvx, avy − bvy). Kuadrat jaraknya</p>
<p><code>f(t) = |D|² + 2(D·V)·t + |V|²·t²</code></p>
<p>adalah parabola terbuka ke atas (atau konstan jika V = 0), jadi <strong>cembung</strong>. Ternary search bilangan bulat di [0, T] menemukan minimumnya dalam ±2·log<sub>1.5</sub> T evaluasi per pertanyaan.</p>
<p>Alternatif tanpa ternary search: puncak parabola ada di t* = −(D·V)/|V|². Cek t = ⌊t*⌋ dan ⌊t*⌋ + 1 (dipotong ke [0, T]) beserta kedua ujung. Nilai terbesar sekitar 10<sup>15</sup>, jadi pakai <code>long long</code>.</p>',
        'hints' => [
            'Kurangkan posisi dan kecepatan kedua kapal: cukup pikirkan satu titik D + V·t yang bergerak, dan jaraknya ke titik asal.',
            'Kuadrat jaraknya adalah polinom derajat dua dalam t dengan koefisien t² yang tidak negatif. Bentuknya seperti apa?',
            'Fungsi cembung → ternary search bilangan bulat di [0, T]: sisakan tiga kandidat terakhir lalu cek manual.',
        ],
    ],

    [
        'slug' => 'rombongan-pelari',
        'lesson' => 'ternary-search',
        'title' => 'Foto Rombongan Pelari',
        'difficulty' => 'Sedang',
        'tags' => ['ternary search', 'fungsi cembung'],
        'statement' => '<p><strong>N</strong> pelari berlari di lintasan lurus. Pelari ke-i mulai di posisi <code>x<sub>i</sub></code> dan bergerak <code>v<sub>i</sub></code> meter per detik (v<sub>i</sub> negatif berarti berlari ke arah sebaliknya). Pada detik ke-t, posisinya <code>x<sub>i</sub> + v<sub>i</sub>·t</code>.</p>
<p>Seorang fotografer ingin memotret seluruh rombongan pada satu detik bulat t dengan 0 ≤ t ≤ T. Agar semua masuk bingkai, ia ingin <strong>sebaran</strong> rombongan, yaitu posisi pelari paling depan dikurangi posisi pelari paling belakang, sekecil mungkin.</p>
<p>Berapa sebaran terkecil yang bisa ia dapatkan?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N T</code>. Setiap dari N baris berikutnya berisi <code>x<sub>i</sub> v<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: sebaran terkecil.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 50 000</li><li>0 ≤ T ≤ 10<sup>6</sup></li><li>|x<sub>i</sub>| ≤ 10<sup>9</sup></li><li>|v<sub>i</sub>| ≤ 1000</li></ul>',
        'samples' => [
            ['input' => "3 10\n0 2\n10 -1\n4 0\n", 'explanation' => 'Pada t = 3 posisinya 6, 7, dan 4: sebaran 3. Pada t = 2 (4, 8, 4) dan t = 4 (8, 6, 4) sebarannya 4. Sebaran 3 adalah yang terkecil.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $T, string $mode) {
                $t0 = mt_rand(0, $T);
                $p0 = mt_rand(-1000000, 1000000);
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $v = mt_rand(-1000, 1000);
                    if ($mode === 'acak') {
                        $x = mt_rand(-1000000000, 1000000000);
                    } elseif ($mode === 'sama') {
                        $v = 7;
                        $x = mt_rand(-1000000000, 1000000000);
                    } else {
                        // rombongan berkumpul di sekitar p0 pada detik t0
                        $x = max(-1000000000, min(1000000000, $p0 - $v * $t0 + mt_rand(-5000, 5000)));
                    }
                    $rows[] = "$x $v";
                }

                return "$n $T\n".implode("\n", $rows)."\n";
            };

            return [
                "1 5\n100 -3\n", "2 0\n-1000000000 1000\n1000000000 -1000\n", "2 1000000\n-1000000000 1000\n1000000000 -1000\n",
                $gen(10, 30, 'kumpul'), $gen(300, 1000, 'kumpul'), $gen(50000, 1000000, 'kumpul'), $gen(50000, 1000000, 'acak'),
                $gen(50000, 1000000, 'sama'), $gen(50000, 1000, 'kumpul'), $gen(2000, 1000000, 'kumpul'),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: binary search pada selisih f(t+1) - f(t)
            $lines = T::lines($input);
            [$n, $T] = T::ints($lines[0]);
            $x = [];
            $v = [];
            for ($i = 1; $i <= $n; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $x[] = $a;
                $v[] = $b;
            }
            $f = function (int $t) use ($n, $x, $v) {
                $mx = PHP_INT_MIN;
                $mn = PHP_INT_MAX;
                for ($i = 0; $i < $n; $i++) {
                    $p = $x[$i] + $v[$i] * $t;
                    if ($p > $mx) {
                        $mx = $p;
                    }
                    if ($p < $mn) {
                        $mn = $p;
                    }
                }

                return $mx - $mn;
            };
            $lo = 0;
            $hi = $T;
            while ($lo < $hi) {
                $mid = intdiv($lo + $hi, 2);
                if ($f($mid + 1) >= $f($mid)) {
                    $hi = $mid;
                } else {
                    $lo = $mid + 1;
                }
            }

            return (string) $f($lo);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> x, v;

long long sebaran(long long t) {
    // posisi terdepan - posisi paling belakang pada detik t
    return 0;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    long long T;
    cin >> n >> T;
    x.resize(n);
    v.resize(n);
    for (int i = 0; i < n; i++) cin >> x[i] >> v[i];

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, T] = readInts();
const x = new Float64Array(n), v = new Float64Array(n);
for (let i = 0; i < n; i++) {
  const [a, b] = readInts();
  x[i] = a;
  v[i] = b;
}
// sebaran(t) = posisi terdepan - posisi paling belakang pada detik t
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, T = int(data[0]), int(data[1])
x = list(map(int, data[2:2 + 2 * n:2]))
v = list(map(int, data[3:3 + 2 * n:2]))
# sebaran(t) = posisi terdepan - posisi paling belakang pada detik t
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> x, v;

// maks dari fungsi linear (cembung) dikurangi min dari fungsi linear (cekung) = cembung
long long sebaran(long long t) {
    long long depan = LLONG_MIN, belakang = LLONG_MAX;
    for (int i = 0; i < n; i++) {
        long long p = x[i] + v[i] * t;
        depan = max(depan, p);
        belakang = min(belakang, p);
    }
    return depan - belakang;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    long long T;
    cin >> n >> T;
    x.resize(n);
    v.resize(n);
    for (int i = 0; i < n; i++) cin >> x[i] >> v[i];

    long long lo = 0, hi = T;
    while (hi - lo > 2) {
        long long m1 = lo + (hi - lo) / 3;
        long long m2 = hi - (hi - lo) / 3;
        if (sebaran(m1) < sebaran(m2)) hi = m2 - 1;
        else lo = m1 + 1;
    }
    long long best = sebaran(lo);
    for (long long t = lo + 1; t <= hi; t++) best = min(best, sebaran(t));
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, T] = readInts();
const x = new Float64Array(n), v = new Float64Array(n);
for (let i = 0; i < n; i++) {
  const [a, b] = readInts();
  x[i] = a;
  v[i] = b;
}
const sebaran = (t) => {
  let depan = -Infinity, belakang = Infinity;
  for (let i = 0; i < n; i++) {
    const p = x[i] + v[i] * t;
    if (p > depan) depan = p;
    if (p < belakang) belakang = p;
  }
  return depan - belakang;
};
let lo = 0, hi = T;
while (hi - lo > 2) {
  const m1 = lo + Math.floor((hi - lo) / 3), m2 = hi - Math.floor((hi - lo) / 3);
  if (sebaran(m1) < sebaran(m2)) hi = m2 - 1;
  else lo = m1 + 1;
}
let best = sebaran(lo);
for (let t = lo + 1; t <= hi; t++) best = Math.min(best, sebaran(t));
console.log(String(best));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n, T = int(data[0]), int(data[1])
    x = list(map(int, data[2:2 + 2 * n:2]))
    v = list(map(int, data[3:3 + 2 * n:2]))

    def sebaran(t):
        pos = [a + b * t for a, b in zip(x, v)]
        return max(pos) - min(pos)

    lo, hi = 0, T
    while hi - lo > 2:
        m1 = lo + (hi - lo) // 3
        m2 = hi - (hi - lo) // 3
        if sebaran(m1) < sebaran(m2):
            hi = m2 - 1
        else:
            lo = m1 + 1
    print(min(sebaran(t) for t in range(lo, hi + 1)))

main()
CODE,
        ],
        'editorial' => '<p>Mencoba semua t butuh O(N · T) = 5·10<sup>10</sup> langkah: terlalu lambat. Lihat bentuk fungsinya:</p>
<p><code>S(t) = maks<sub>i</sub>(x<sub>i</sub> + v<sub>i</sub>t) − min<sub>i</sub>(x<sub>i</sub> + v<sub>i</sub>t)</code></p>
<p>Maksimum dari fungsi-fungsi linear adalah fungsi <strong>cembung</strong> (bentuk mangkuk bersegi). Minimum dari fungsi linear adalah cekung, jadi dikurangi menjadi cembung lagi. Jumlah dua fungsi cembung tetap cembung, sehingga S(t) cembung dan dataran datarnya hanya mungkin di dasar lembah.</p>
<p>Maka ternary search bilangan bulat di [0, T] benar: sekitar 2·log<sub>1.5</sub>(10<sup>6</sup>) ≈ 70 evaluasi, masing-masing O(N). Alternatifnya, binary search pada selisih: cari t terkecil dengan S(t + 1) − S(t) ≥ 0.</p>',
        'hints' => [
            'Coba gambar posisi setiap pelari terhadap waktu: garis-garis lurus. Seperti apa bentuk "garis teratas" dan "garis terbawah"?',
            'Garis teratas (maksimum fungsi linear) cembung; garis terbawah cekung. Selisihnya?',
            'S(t) cembung → ternary search bilangan bulat pada t, setiap evaluasi O(N).',
        ],
    ],

    // ───────────────────────── Soal Interaktif ─────────────────────────
    [
        'slug' => 'transkrip-juri',
        'lesson' => 'interaktif',
        'title' => 'Transkrip Tebak Angka',
        'difficulty' => 'Mudah',
        'tags' => ['interaktif', 'binary search', 'simulasi'],
        'statement' => '<p>Kamu sedang menguji program tebak angka milik temanmu tanpa juri sungguhan. Programnya selalu memakai strategi berikut untuk menebak bilangan rahasia X di [1, N]:</p>
<pre>lo = 1, hi = N
selama lo &lt; hi:
    mid = (lo + hi + 1) / 2        (dibagi bulat ke bawah)
    tanya "? mid"
    jika X &lt; mid : juri menjawab "&lt;",  hi = mid − 1
    selain itu   : juri menjawab "&gt;=", lo = mid
jawab "! lo"</pre>
<p>Untuk setiap skenario (N, X), tuliskan transkripnya: berapa pertanyaan yang diajukan program, dan bilangan apa saja yang ditanyakan, berurutan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi <code>N X</code>.</p>',
        'output_format' => '<p>Untuk setiap skenario, satu baris: banyak pertanyaan k, diikuti k bilangan yang ditanyakan.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 10 000</li><li>1 ≤ X ≤ N ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n100 73\n1 1\n10 1\n", 'explanation' => 'Skenario 1 sama dengan contoh di materi: 7 pertanyaan. Skenario 2: lo = hi sejak awal, tidak ada pertanyaan. Skenario 3: tanya 6 (jawab &lt;), 3 (&lt;), 2 (&lt;), lalu lo = hi = 1.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $nMax) {
                $rows = [];
                for ($k = 0; $k < $q; $k++) {
                    $n = mt_rand(1, $nMax);
                    $x = mt_rand(1, $n);
                    if ($k % 5 == 0) {
                        $x = 1;
                    } elseif ($k % 5 == 1) {
                        $x = $n;
                    }
                    $rows[] = "$n $x";
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n1 1\n", "4\n2 1\n2 2\n3 2\n1000000000 1000000000\n", $gen(50, 20), $gen(1000, 1000),
                $gen(10000, 1000000000), $gen(10000, 64),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($k = 1; $k <= $q; $k++) {
                [$n, $x] = T::ints($lines[$k]);
                $lo = 1;
                $hi = $n;
                $tanya = [];
                while ($lo < $hi) {
                    $mid = intdiv($lo + $hi + 1, 2);
                    $tanya[] = $mid;
                    if ($x < $mid) {
                        $hi = $mid - 1;
                    } else {
                        $lo = $mid;
                    }
                }
                $out[] = trim(count($tanya).' '.implode(' ', $tanya));
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
        long long n, x;
        cin >> n >> x;
        // jalankan strateginya, catat setiap mid yang ditanyakan
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = readInts()[0];
const out = [];
for (let k = 0; k < q; k++) {
  const [n, x] = readInts();
  // jalankan strateginya, catat setiap mid yang ditanyakan
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n, x = map(int, input().split())
    # jalankan strateginya, catat setiap mid yang ditanyakan
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
        long long n, x;
        cin >> n >> x;
        vector<long long> tanya;
        long long lo = 1, hi = n;
        while (lo < hi) {
            long long mid = (lo + hi + 1) / 2;
            tanya.push_back(mid);
            if (x < mid) hi = mid - 1;      // juri menjawab "<"
            else lo = mid;                  // juri menjawab ">="
        }
        cout << tanya.size();
        for (long long m : tanya) cout << ' ' << m;
        cout << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = readInts()[0];
const out = [];
for (let k = 0; k < q; k++) {
  const [n, x] = readInts();
  const tanya = [];
  let lo = 1, hi = n;
  while (lo < hi) {
    const mid = Math.floor((lo + hi + 1) / 2);
    tanya.push(mid);
    if (x < mid) hi = mid - 1;
    else lo = mid;
  }
  out.push([tanya.length, ...tanya].join(" "));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n, x = map(int, input().split())
    tanya = []
    lo, hi = 1, n
    while lo < hi:
        mid = (lo + hi + 1) // 2
        tanya.append(mid)
        if x < mid:
            hi = mid - 1
        else:
            lo = mid
    out.append(" ".join(map(str, [len(tanya)] + tanya)))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Cukup jalankan strateginya sambil berperan sebagai juri: kita tahu X, jadi jawaban setiap pertanyaan bisa langsung dihitung. Setiap skenario butuh paling banyak ⌈log<sub>2</sub> N⌉ ≤ 30 putaran.</p>
<p>Menulis "juri palsu" seperti ini adalah cara terbaik menguji solusi interaktif sebelum dikirim. Coba juga hitung: untuk N = 100, ada X yang hanya butuh 6 pertanyaan? (Ya: ketika rentangnya terbelah tidak sama besar, salah satu cabang lebih pendek.)</p>',
        'hints' => [
            'Kamu tahu rahasianya, jadi kamu bisa menjawab sendiri setiap pertanyaan program.',
            'Simpan setiap mid dalam daftar, lalu cetak panjang daftar diikuti isinya.',
            'Perhatikan pembulatan ke atas: mid = (lo + hi + 1) / 2.',
        ],
    ],

    [
        'slug' => 'koin-palsu',
        'lesson' => 'interaktif',
        'title' => 'Koin Palsu dan P Timbangan',
        'difficulty' => 'Sedang',
        'tags' => ['interaktif', 'batas informasi', 'matematika'],
        'statement' => '<p>Ada <strong>n</strong> koin yang tampak sama. Tepat satu koin palsu, dan koin palsu <em>lebih berat</em> daripada koin asli (semua koin asli sama beratnya).</p>
<p>Kamu punya <strong>P</strong> timbangan dua lengan. Dalam satu <em>ronde</em>, kamu memakai semua timbangan sekaligus: setiap timbangan diberi koin dengan <strong>jumlah yang sama</strong> di lengan kiri dan kanan (setiap koin paling banyak berada di satu lengan, dan boleh ada koin yang tidak ditimbang). Setiap timbangan lalu menunjukkan: kiri lebih berat, kanan lebih berat, atau seimbang.</p>
<p>Ronde berikutnya boleh direncanakan berdasarkan hasil ronde-ronde sebelumnya. Berapa ronde minimum yang <strong>selalu cukup</strong> untuk menemukan koin palsu, di mana pun letaknya?</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi <code>n P</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, satu baris berisi banyak ronde minimum.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>1 ≤ n ≤ 10<sup>18</sup></li><li>1 ≤ P ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n27 1\n1 5\n28 1\n100 2\n1000000000000000000 1\n", 'explanation' => 'Satu timbangan: setiap ronde punya 3 hasil, jadi k ronde membedakan 3<sup>k</sup> koin. 27 = 3³ butuh 3 ronde, 28 butuh 4. Satu koin tidak perlu ditimbang. Dua timbangan: setiap ronde punya 5 hasil (koin palsu di salah satu dari 4 lengan, atau tidak ditimbang), 5³ = 125 ≥ 100. 3<sup>37</sup> &lt; 10<sup>18</sup> ≤ 3<sup>38</sup>.'],
        ],
        'tests' => function () {
            $LIM = 1000000000000000000;
            $gen = function (int $q, int $pMax) use ($LIM) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $p = mt_rand(1, $pMax);
                    $b = 2 * $p + 1;
                    $pows = [1];
                    while ($pows[count($pows) - 1] <= intdiv($LIM, $b)) {
                        $pows[] = $pows[count($pows) - 1] * $b;
                    }
                    $n = $pows[mt_rand(0, count($pows) - 1)] + mt_rand(-1, 1);
                    if ($i % 4 == 3) {
                        $n = mt_rand(1, $LIM);
                    }
                    $n = max(1, min($LIM, $n));
                    $rows[] = "$n $p";
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n1 1\n", "4\n2 1\n3 1\n4 1\n10 1000000000\n", "2\n1000000000000000000 1\n1000000000000000000 1000000000\n",
                $gen(100, 3), $gen(100000, 1), $gen(100000, 1000000000), $gen(100000, 50),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                [$n, $p] = T::ints($lines[$i]);
                $b = 2 * $p + 1;
                $pw = 1;
                $k = 0;
                while ($pw < $n) {
                    $k++;
                    if ($pw > intdiv($n, $b)) {
                        break;
                    }
                    $pw *= $b;
                }
                $out[] = $k;
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
        long long n, p;
        cin >> n >> p;
        // satu ronde punya berapa kemungkinan hasil?
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// n sampai 10^18: pakai BigInt
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [ns, ps] = readLine().trim().split(/\s+/);
  const n = BigInt(ns), p = BigInt(ps);
  // satu ronde punya berapa kemungkinan hasil?
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n, p = map(int, input().split())
    # satu ronde punya berapa kemungkinan hasil?
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
        long long n, p;
        cin >> n >> p;
        long long b = 2 * p + 1;        // hasil satu ronde: salah satu dari 2P lengan, atau tidak ditimbang
        long long pw = 1;               // b^k = banyak koin yang bisa dibedakan dalam k ronde
        int k = 0;
        while (pw < n) {
            k++;
            if (pw > n / b) break;      // pw * b pasti >= n; berhenti sebelum overflow
            pw *= b;
        }
        cout << k << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [ns, ps] = readLine().trim().split(/\s+/);
  const n = BigInt(ns), b = 2n * BigInt(ps) + 1n;
  let pw = 1n, k = 0;
  while (pw < n) {
    pw *= b;
    k++;
  }
  out.push(k);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n, p = map(int, input().split())
    b = 2 * p + 1
    pw, k = 1, 0
    while pw < n:
        pw *= b
        k += 1
    out.append(str(k))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p><strong>Berapa hasil yang mungkin dalam satu ronde?</strong> Karena hanya ada satu koin palsu, paling banyak satu timbangan yang miring. Jadi hasil satu ronde hanya memberi tahu di mana koin palsu berada: di salah satu dari 2P lengan, atau di kelompok yang tidak ditimbang. Itu b = 2P + 1 kemungkinan.</p>
<p><strong>Batas bawah.</strong> Setelah k ronde, paling banyak b<sup>k</sup> rangkaian hasil yang berbeda, dan setiap rangkaian harus menunjuk satu koin. Maka b<sup>k</sup> ≥ n.</p>
<p><strong>Cukup.</strong> Jika n ≤ b<sup>k</sup>, bagi koin menjadi 2P + 1 kelompok berukuran paling banyak b<sup>k−1</sup>, dengan dua kelompok di setiap timbangan berukuran sama. Hasil ronde menunjuk satu kelompok, dan sisanya diselesaikan dengan k − 1 ronde (induksi).</p>
<p>Jawabannya k terkecil dengan (2P + 1)<sup>k</sup> ≥ n. Hati-hati overflow: (2P + 1)<sup>k</sup> bisa melewati 10<sup>18</sup>, jadi cek <code>pw &gt; n / b</code> sebelum mengalikan.</p>',
        'hints' => [
            'Hanya ada satu koin palsu. Berapa banyak timbangan yang bisa miring dalam satu ronde?',
            'Satu ronde punya 2P + 1 kemungkinan hasil. Setelah k ronde, ada berapa rangkaian hasil yang berbeda?',
            'Cari k terkecil dengan (2P + 1)<sup>k</sup> ≥ n, dan waspadai overflow saat mengalikan.',
        ],
    ],

    [
        'slug' => 'anggaran-merge-sort',
        'lesson' => 'interaktif',
        'title' => 'Anggaran Pertanyaan Merge Sort',
        'difficulty' => 'Sedang',
        'tags' => ['interaktif', 'rekurens', 'merge sort'],
        'statement' => '<p>Juri menyembunyikan n benda yang beratnya berbeda-beda. Programmu hanya boleh bertanya "apakah benda a lebih ringan daripada benda b?". Temanmu mengurutkan benda-benda itu dengan merge sort berikut:</p>
<pre>urut(l, r):                         // mengurutkan benda ke-l sampai ke-r
    jika l = r: selesai
    m = (l + r) / 2                 (dibagi bulat ke bawah)
    urut(l, m)
    urut(m + 1, r)
    gabungkan: selama kedua bagian masih punya benda,
        tanyakan benda terdepan bagian kiri vs terdepan bagian kanan,
        lalu pindahkan yang lebih ringan ke hasil.
    sisa bagian yang belum habis disalin tanpa bertanya.</pre>
<p>Panitia harus menetapkan batas pertanyaan yang <strong>pasti cukup</strong> untuk program ini, apa pun urutan beratnya. Untuk setiap n, hitung banyak pertanyaan pada <strong>kasus terburuk</strong> saat <code>urut(1, n)</code> dipanggil.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi satu bilangan <code>n</code>.</p>',
        'output_format' => '<p>Untuk setiap n, satu baris berisi banyak pertanyaan pada kasus terburuk.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>1 ≤ n ≤ 10<sup>17</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1\n2\n5\n10\n1000\n", 'explanation' => 'n = 2: satu pertanyaan. n = 5: dibelah 3 + 2; W(3) = 3, W(2) = 1, dan penggabungan 5 benda paling banyak 4 pertanyaan: 3 + 1 + 4 = 8.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $nMax) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    if ($i % 3 == 0) {
                        $n = (1 << mt_rand(0, 56)) + mt_rand(-1, 1);
                    } else {
                        $n = mt_rand(1, $nMax);
                    }
                    $rows[] = max(1, min(100000000000000000, $n));
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n1\n", "6\n1\n2\n3\n4\n100000000000000000\n99999999999999999\n", $gen(100, 100),
                $gen(100000, 100000000000000000), $gen(100000, 1000000),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: rekurens W(n) = W(ceil(n/2)) + W(floor(n/2)) + n - 1 dengan memo
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $memo = [];
            $W = function (int $n) use (&$W, &$memo) {
                if ($n <= 1) {
                    return 0;
                }
                if (isset($memo[$n])) {
                    return $memo[$n];
                }

                return $memo[$n] = $W(intdiv($n + 1, 2)) + $W(intdiv($n, 2)) + $n - 1;
            };
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $memo = [];
                $out[] = $W((int) $lines[$i]);
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

// W(n) = pertanyaan terburuk untuk n benda

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    while (q--) {
        long long n;
        cin >> n;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// n sampai 10^17 dan jawabannya sampai ~6·10^18: pakai BigInt
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const n = BigInt(readLine().trim());
  // W(n) = pertanyaan terburuk untuk n benda
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n = int(input())
    # W(n) = pertanyaan terburuk untuk n benda
print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

map<long long, long long> memo;

// W(n) = W(ceil(n/2)) + W(floor(n/2)) + (n - 1)
// Di setiap kedalaman hanya ada paling banyak dua ukuran berbeda (k dan k+1),
// jadi memo hanya berisi O(log n) nilai.
long long W(long long n) {
    if (n <= 1) return 0;
    auto it = memo.find(n);
    if (it != memo.end()) return it->second;
    long long r = W((n + 1) / 2) + W(n / 2) + (n - 1);
    memo[n] = r;
    return r;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    while (q--) {
        long long n;
        cin >> n;
        memo.clear();                   // memo per pertanyaan: hanya O(log n) isi
        cout << W(n) << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Rumus tertutup: W(n) = n·c − 2^c + 1 dengan c = ⌈log2 n⌉
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const n = BigInt(readLine().trim());
  let c = 0n, pw = 1n;
  while (pw < n) {
    pw *= 2n;
    c++;
  }
  out.push(String(n * c - pw + 1n));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n = int(input())
    c = (n - 1).bit_length()          # c = ceil(log2 n)
    out.append(str(n * c - (1 << c) + 1))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p><strong>Satu penggabungan.</strong> Menggabungkan bagian berukuran p dan q menanyakan satu pertanyaan per benda yang dipindahkan, kecuali benda-benda yang tersisa setelah satu bagian habis. Paling buruk hanya satu benda yang tersisa: p + q − 1 pertanyaan (misalnya jika urutan beratnya selang-seling kiri-kanan).</p>
<p><strong>Rekurens.</strong> Urutan berat bisa dipilih agar <em>setiap</em> penggabungan mencapai kasus terburuknya sekaligus, jadi</p>
<p><code>W(1) = 0,  W(n) = W(⌈n/2⌉) + W(⌊n/2⌋) + n − 1</code></p>
<p>n sampai 10<sup>17</sup>, tetapi di setiap kedalaman rekursi hanya muncul paling banyak dua ukuran berbeda (k dan k + 1). Dengan memo, setiap pertanyaan hanya menyentuh O(log n) nilai.</p>
<p><strong>Rumus tertutup.</strong> Dengan c = ⌈log<sub>2</sub> n⌉, W(n) = n·c − 2<sup>c</sup> + 1. Jawabannya sampai sekitar 5,7·10<sup>18</sup>: masih muat di <code>long long</code>, tetapi di JavaScript perlu BigInt. Bandingkan dengan batas informasi ⌈log<sub>2</sub> n!⌉: merge sort hanya sedikit di atasnya.</p>',
        'hints' => [
            'Berapa pertanyaan paling banyak untuk menggabungkan dua bagian berukuran p dan q?',
            'Tulis W(n) secara rekursif dari W(⌈n/2⌉) dan W(⌊n/2⌋). n terlalu besar untuk tabel, tetapi berapa banyak nilai berbeda yang benar-benar muncul?',
            'Memo dengan map, atau buktikan rumus W(n) = n·c − 2<sup>c</sup> + 1 dengan c = ⌈log<sub>2</sub> n⌉.',
        ],
    ],

    // ───────────────────────── Meet in the Middle ─────────────────────────
    [
        'slug' => 'hitung-subset-target',
        'lesson' => 'meet-in-the-middle',
        'title' => 'Hitung Subset Bertarget',
        'difficulty' => 'Sedang',
        'tags' => ['meet in the middle', 'subset sum'],
        'statement' => '<p>Diberikan <strong>n</strong> bilangan bulat positif <code>a<sub>1</sub>, …, a<sub>n</sub></code> dan sebuah target <strong>X</strong>. Ada berapa cara memilih sebagian elemen (subset, berdasarkan posisi) sehingga jumlahnya tepat X?</p>
<p>Dua subset dianggap berbeda jika ada posisi yang dipilih di satu subset tetapi tidak di subset lain, meskipun nilainya sama.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n X</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak subset yang jumlahnya X.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 36</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ X ≤ 10<sup>12</sup></li></ul>',
        'samples' => [
            ['input' => "4 5\n1 2 3 2\n", 'explanation' => 'Tiga subset: {2, 3} (posisi 2 dan 3), {3, 2} (posisi 3 dan 4), dan {1, 2, 2} (posisi 1, 2, dan 4).'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $lo, int $hi, string $mode) {
                $a = T::arr($n, $lo, $hi);
                if ($mode === 'subset') {
                    $X = 0;
                    foreach ($a as $v) {
                        if (mt_rand(0, 1)) {
                            $X += $v;
                        }
                    }
                    $X = max(1, $X);
                } elseif ($mode === 'separuh') {
                    $X = max(1, intdiv(array_sum($a), 2));
                } else {
                    $X = mt_rand(1, 1000000000000);
                }

                return "$n $X\n".T::join($a)."\n";
            };

            return [
                "1 5\n5\n", "1 4\n5\n", "36 18\n".T::join(array_fill(0, 36, 1))."\n",
                $gen(10, 1, 10, 'subset'), $gen(20, 1, 1000, 'subset'), $gen(36, 1, 1000000000, 'subset'),
                $gen(35, 1, 30, 'separuh'), $gen(36, 1, 100, 'separuh'), $gen(36, 1, 1000000000, 'acak'), $gen(36, 1000, 1010, 'separuh'),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $X] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $sums = function (array $arr) {
                $s = [0];
                foreach ($arr as $x) {
                    $k = count($s);
                    for ($j = 0; $j < $k; $j++) {
                        $s[] = $s[$j] + $x;
                    }
                }

                return $s;
            };
            $h = intdiv($n, 2);
            $L = $sums(array_slice($a, 0, $h));
            $cnt = array_count_values($sums(array_slice($a, $h)));
            $ans = 0;
            foreach ($L as $s) {
                $ans += $cnt[$X - $s] ?? 0;
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
    long long X;
    cin >> n >> X;
    vector<long long> a(n);
    for (auto& v : a) cin >> v;

    // 2^36 subset terlalu banyak. Belah dua!
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, X] = readInts();
const a = readInts();
// 2^36 subset terlalu banyak. Belah dua!
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, X = int(data[0]), int(data[1])
a = list(map(int, data[2:2 + n]))
# 2^36 subset terlalu banyak. Belah dua!
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<long long> semuaJumlah(const vector<long long>& a) {
    vector<long long> s(1, 0);
    s.reserve(1u << a.size());
    for (long long x : a) {
        int k = s.size();
        for (int j = 0; j < k; j++) s.push_back(s[j] + x);
    }
    return s;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long X;
    cin >> n >> X;
    vector<long long> a(n);
    for (auto& v : a) cin >> v;

    int h = n / 2;
    vector<long long> L = semuaJumlah(vector<long long>(a.begin(), a.begin() + h));
    vector<long long> R = semuaJumlah(vector<long long>(a.begin() + h, a.end()));
    sort(R.begin(), R.end());

    long long cara = 0;
    for (long long s : L) {
        long long butuh = X - s;          // pasangan dari paruh kanan
        cara += upper_bound(R.begin(), R.end(), butuh) - lower_bound(R.begin(), R.end(), butuh);
    }
    cout << cara << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, X] = readInts();
const a = readInts();
const semuaJumlah = (arr) => {
  const s = new Float64Array(2 ** arr.length);
  let k = 1;
  for (const x of arr) {
    for (let j = 0; j < k; j++) s[k + j] = s[j] + x;
    k *= 2;
  }
  return s;
};
const h = Math.floor(n / 2);
const L = semuaJumlah(a.slice(0, h));
const R = semuaJumlah(a.slice(h)).sort();
const pertama = (v, ketat) => {          // indeks pertama dengan R[i] >= v (atau > v jika ketat)
  let lo = 0, hi = R.length;
  while (lo < hi) {
    const m = (lo + hi) >> 1;
    if (R[m] < v || (ketat && R[m] === v)) lo = m + 1;
    else hi = m;
  }
  return lo;
};
let cara = 0;
for (const s of L) cara += pertama(X - s, true) - pertama(X - s, false);
console.log(String(cara));
CODE,
            'python' => <<<'CODE'
import sys
from collections import Counter

def semua_jumlah(a):
    s = [0]
    for x in a:
        s += [v + x for v in s]
    return s

data = sys.stdin.buffer.read().split()
n, X = int(data[0]), int(data[1])
a = list(map(int, data[2:2 + n]))
h = n // 2
L = semua_jumlah(a[:h])
R = Counter(semua_jumlah(a[h:]))
print(sum(R.get(X - s, 0) for s in L))
CODE,
        ],
        'editorial' => '<p>Setiap subset dari seluruh array adalah gabungan satu subset paruh kiri (indeks 1..n/2) dan satu subset paruh kanan. Jadi banyak cara = banyak pasangan (l, r) dengan l ∈ L, r ∈ R, l + r = X, di mana L dan R adalah daftar semua jumlah subset dari setiap paruh (termasuk subset kosong).</p>
<p>Setiap daftar berisi 2<sup>18</sup> ≈ 2,6·10<sup>5</sup> jumlah. Urutkan R, lalu untuk setiap l hitung berapa elemen R yang sama dengan X − l dengan <code>upper_bound − lower_bound</code>. Atau simpan frekuensi R di hash map. Total O(2<sup>n/2</sup> · n).</p>
<p>Jawabannya bisa sangat besar (misalnya 36 angka 1 dengan X = 18 memberi C(36, 18) ≈ 9·10<sup>9</sup>), jadi pakai <code>long long</code>.</p>',
        'hints' => [
            'Mencoba 2<sup>36</sup> ≈ 7·10<sup>10</sup> subset terlalu lambat, tetapi 2<sup>18</sup> sangat ringan.',
            'Subset seluruh array = subset paruh kiri + subset paruh kanan. Untuk jumlah kiri l, jumlah kanan berapa yang dibutuhkan?',
            'Urutkan semua jumlah paruh kanan, lalu hitung kemunculan X − l dengan lower_bound dan upper_bound.',
        ],
    ],

    [
        'slug' => 'empat-jumlah',
        'lesson' => 'meet-in-the-middle',
        'title' => 'Empat Jumlah Nol',
        'difficulty' => 'Sedang',
        'tags' => ['meet in the middle', 'binary search', 'hashing'],
        'statement' => '<p>Diberikan empat array <strong>A, B, C, D</strong>, masing-masing berisi <strong>n</strong> bilangan bulat. Hitung banyak empat indeks <code>(i, j, k, l)</code> sehingga</p>
<p><code>A[i] + B[j] + C[k] + D[l] = 0</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Empat baris berikutnya berisi isi A, B, C, dan D, masing-masing n bilangan.</p>',
        'output_format' => '<p>Satu bilangan: banyak empat indeks yang memenuhi.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 1000</li><li>|A[i]|, |B[j]|, |C[k]|, |D[l]| ≤ 10<sup>8</sup></li></ul>',
        'samples' => [
            ['input' => "3\n1 -2 0\n2 -1 3\n-3 1 0\n0 2 -2\n", 'explanation' => 'Salah satunya A[1] + B[1] + C[1] + D[1] = 1 + 2 − 3 + 0 = 0 (indeks dari 1). Ada 9 empat indeks seperti itu.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $lo, int $hi) {
                $s = "$n\n";
                for ($k = 0; $k < 4; $k++) {
                    $s .= T::join(T::arr($n, $lo, $hi))."\n";
                }

                return $s;
            };
            $tanam = function (int $n) {
                // nilai besar dengan beberapa solusi yang sengaja ditanam
                $A = T::arr($n, -25000000, 25000000);
                $B = T::arr($n, -25000000, 25000000);
                $C = T::arr($n, -25000000, 25000000);
                $D = T::arr($n, -100000000, 100000000);
                for ($t = 0; $t < $n; $t += 3) {
                    $D[$t] = -($A[mt_rand(0, $n - 1)] + $B[mt_rand(0, $n - 1)] + $C[mt_rand(0, $n - 1)]);
                }

                return "$n\n".T::join($A)."\n".T::join($B)."\n".T::join($C)."\n".T::join($D)."\n";
            };

            return [
                "1\n0\n0\n0\n0\n", "1\n1\n2\n3\n4\n", $gen(10, -3, 3), $gen(200, -20, 20), $gen(1000, -50, 50),
                $gen(1000, 0, 0), $tanam(1000), $gen(1000, -100000000, 100000000), $gen(999, -1000, 1000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $A = T::ints($lines[1]);
            $B = T::ints($lines[2]);
            $C = T::ints($lines[3]);
            $D = T::ints($lines[4]);
            $ab = [];
            foreach ($A as $x) {
                foreach ($B as $y) {
                    $ab[] = $x + $y;
                }
            }
            $cnt = array_count_values($ab);
            unset($ab);
            $ans = 0;
            foreach ($C as $x) {
                foreach ($D as $y) {
                    $ans += $cnt[-($x + $y)] ?? 0;
                }
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
    vector<long long> A(n), B(n), C(n), D(n);
    for (auto& v : A) cin >> v;
    for (auto& v : B) cin >> v;
    for (auto& v : C) cin >> v;
    for (auto& v : D) cin >> v;

    // n^4 = 10^12 terlalu banyak. Pasangkan (A, B) dan (C, D).
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const A = readInts(), B = readInts(), C = readInts(), D = readInts();
// n^4 = 10^12 terlalu banyak. Pasangkan (A, B) dan (C, D).
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
A = list(map(int, data[1:1 + n]))
B = list(map(int, data[1 + n:1 + 2 * n]))
C = list(map(int, data[1 + 2 * n:1 + 3 * n]))
D = list(map(int, data[1 + 3 * n:1 + 4 * n]))
# n^4 = 10^12 terlalu banyak. Pasangkan (A, B) dan (C, D).
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
    vector<long long> A(n), B(n), C(n), D(n);
    for (auto& v : A) cin >> v;
    for (auto& v : B) cin >> v;
    for (auto& v : C) cin >> v;
    for (auto& v : D) cin >> v;

    vector<long long> ab;                     // paruh kiri: n^2 jumlah A[i] + B[j]
    ab.reserve((size_t)n * n);
    for (long long x : A)
        for (long long y : B) ab.push_back(x + y);
    sort(ab.begin(), ab.end());

    long long cara = 0;
    for (long long x : C)
        for (long long y : D) {                // paruh kanan: butuh A[i] + B[j] = -(C[k] + D[l])
            long long t = -(x + y);
            cara += upper_bound(ab.begin(), ab.end(), t) - lower_bound(ab.begin(), ab.end(), t);
        }
    cout << cara << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const A = readInts(), B = readInts(), C = readInts(), D = readInts();
const ab = new Float64Array(n * n);
let k = 0;
for (let i = 0; i < n; i++) for (let j = 0; j < n; j++) ab[k++] = A[i] + B[j];
ab.sort();
const pertama = (v, ketat) => {
  let lo = 0, hi = ab.length;
  while (lo < hi) {
    const m = (lo + hi) >> 1;
    if (ab[m] < v || (ketat && ab[m] === v)) lo = m + 1;
    else hi = m;
  }
  return lo;
};
let cara = 0;
for (let i = 0; i < n; i++)
  for (let j = 0; j < n; j++) {
    const t = -(C[i] + D[j]);
    cara += pertama(t, true) - pertama(t, false);
  }
console.log(String(cara));
CODE,
            'python' => <<<'CODE'
import sys
from collections import Counter

data = sys.stdin.buffer.read().split()
n = int(data[0])
A = list(map(int, data[1:1 + n]))
B = list(map(int, data[1 + n:1 + 2 * n]))
C = list(map(int, data[1 + 2 * n:1 + 3 * n]))
D = list(map(int, data[1 + 3 * n:1 + 4 * n]))

cnt = Counter(x + y for x in A for y in B)
get = cnt.get
print(sum(get(-(x + y), 0) for x in C for y in D))
CODE,
        ],
        'editorial' => '<p>Brute force O(n<sup>4</sup>) = 10<sup>12</sup>, dan bahkan O(n<sup>3</sup>) = 10<sup>9</sup> dengan hash terlalu lambat. Belah empat array menjadi dua kelompok: (A, B) dan (C, D).</p>
<p>Hitung semua n<sup>2</sup> jumlah A[i] + B[j] (paruh kiri). Untuk setiap pasangan (k, l) di paruh kanan, kita butuh A[i] + B[j] = −(C[k] + D[l]). Urutkan paruh kiri, lalu hitung banyaknya nilai itu dengan <code>upper_bound − lower_bound</code>, atau pakai hash map frekuensi.</p>
<p>Total O(n<sup>2</sup> log n) ≈ 2·10<sup>7</sup>. Jawabannya bisa sampai n<sup>4</sup> = 10<sup>12</sup> (semua nol), jadi pakai <code>long long</code>.</p>',
        'hints' => [
            'Pisahkan rumusnya: A[i] + B[j] = −(C[k] + D[l]).',
            'Ada n² nilai di setiap sisi. Simpan semua nilai sisi kiri.',
            'Urutkan nilai sisi kiri, lalu untuk setiap nilai sisi kanan hitung kemunculan pasangannya dengan binary search.',
        ],
    ],

    [
        'slug' => 'bagi-warisan-adil',
        'lesson' => 'meet-in-the-middle',
        'title' => 'Bagi Dua Seadil Mungkin',
        'difficulty' => 'Sulit',
        'tags' => ['meet in the middle', 'two pointers', 'subset sum'],
        'statement' => '<p>Dua bersaudara mewarisi <strong>n</strong> barang dengan nilai <code>w<sub>1</sub>, …, w<sub>n</sub></code>. Setiap barang harus diberikan kepada tepat salah satu dari mereka (boleh saja satu orang tidak mendapat apa-apa).</p>
<p>Bagilah barang-barang itu sehingga <strong>selisih</strong> total nilai yang diterima keduanya sekecil mungkin. Cetak selisih terkecil itu.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Baris kedua berisi <code>w<sub>1</sub> … w<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: selisih terkecil.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 36</li><li>1 ≤ w<sub>i</sub> ≤ 10<sup>12</sup></li></ul>',
        'samples' => [
            ['input' => "5\n10 6 5 3 1\n", 'explanation' => 'Total 25 (ganjil), jadi selisih minimal 1: {10, 3} = 13 dan {6, 5, 1} = 12.'],
            ['input' => "1\n7\n", 'explanation' => 'Hanya satu barang: satu orang mendapat 7, yang lain 0.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n";
            };
            $dua = [];
            for ($i = 0; $i < 36; $i++) {
                $dua[] = 1 << $i;
            }
            T::shuffle($dua);

            return [
                "2\n1000000000000 1000000000000\n", "3\n1 1 1\n", "36\n".T::join($dua)."\n",
                $gen(10, 1, 100), $gen(20, 1, 1000000), $gen(36, 1, 1000000000000), $gen(35, 1, 1000000000000),
                $gen(36, 1, 1000), $gen(36, 999999000000, 1000000000000), $gen(33, 1, 1000000000000),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: jumlah bertanda kedua paruh + dua pointer
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $w = T::ints($lines[1]);
            $bertanda = function (array $a) {
                $s = [0];
                foreach ($a as $x) {
                    $t = [];
                    foreach ($s as $v) {
                        $t[] = $v + $x;
                        $t[] = $v - $x;
                    }
                    $s = $t;
                }

                return $s;
            };
            $h = intdiv($n, 2);
            $L = $bertanda(array_slice($w, 0, $h));
            $R = $bertanda(array_slice($w, $h));
            sort($L);
            sort($R);
            $m = count($R);
            $j = $m;
            $best = PHP_INT_MAX;
            foreach ($L as $l) {
                while ($j > 0 && $R[$j - 1] + $l >= 0) {
                    $j--;
                }
                if ($j < $m) {
                    $best = min($best, $R[$j] + $l);
                }
                if ($j > 0) {
                    $best = min($best, -($R[$j - 1] + $l));
                }
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
    vector<long long> w(n);
    for (auto& v : w) cin >> v;

    // beri tanda + (kakak) atau - (adik) pada setiap barang: buat jumlahnya sedekat mungkin ke 0
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const w = readInts();
// beri tanda + (kakak) atau - (adik) pada setiap barang: buat jumlahnya sedekat mungkin ke 0
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
w = list(map(int, data[1:1 + n]))
# beri tanda + (kakak) atau - (adik) pada setiap barang: buat jumlahnya sedekat mungkin ke 0
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

// semua jumlah ±a[0] ± a[1] ± ...: + untuk kakak, - untuk adik
vector<long long> bertanda(const vector<long long>& a) {
    vector<long long> s(1, 0);
    for (long long x : a) {
        int k = s.size();
        for (int j = 0; j < k; j++) {
            s.push_back(s[j] - x);
            s[j] += x;
        }
    }
    return s;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> w(n);
    for (auto& v : w) cin >> v;

    int h = n / 2;
    vector<long long> L = bertanda(vector<long long>(w.begin(), w.begin() + h));
    vector<long long> R = bertanda(vector<long long>(w.begin() + h, w.end()));
    sort(R.begin(), R.end());

    long long best = LLONG_MAX;
    for (long long l : L) {
        // cari r yang paling dekat ke -l: kandidatnya elemen pertama >= -l dan elemen sebelumnya
        auto it = lower_bound(R.begin(), R.end(), -l);
        if (it != R.end()) best = min(best, llabs(l + *it));
        if (it != R.begin()) best = min(best, llabs(l + *prev(it)));
    }
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const w = readInts();
const bertanda = (arr) => {
  const s = new Float64Array(2 ** arr.length);
  let k = 1;
  for (const x of arr) {
    for (let j = 0; j < k; j++) {
      s[k + j] = s[j] - x;
      s[j] += x;
    }
    k *= 2;
  }
  return s;
};
const h = Math.floor(n / 2);
const L = bertanda(w.slice(0, h));
const R = bertanda(w.slice(h)).sort();
let best = Infinity;
for (const l of L) {
  let lo = 0, hi = R.length;              // indeks pertama dengan R[i] >= -l
  while (lo < hi) {
    const m = (lo + hi) >> 1;
    if (R[m] < -l) lo = m + 1;
    else hi = m;
  }
  if (lo < R.length) best = Math.min(best, Math.abs(l + R[lo]));
  if (lo > 0) best = Math.min(best, Math.abs(l + R[lo - 1]));
}
console.log(String(best));
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_left

def bertanda(a):
    s = [0]
    for x in a:
        s = [v + x for v in s] + [v - x for v in s]
    return s

data = sys.stdin.buffer.read().split()
n = int(data[0])
w = list(map(int, data[1:1 + n]))
h = n // 2
L = bertanda(w[:h])
R = sorted(bertanda(w[h:]))
m = len(R)
best = None
for l in L:
    k = bisect_left(R, -l)
    if k < m:
        d = abs(l + R[k])
        if best is None or d < best:
            best = d
    if k > 0:
        d = abs(l + R[k - 1])
        if d < best:
            best = d
print(best)
CODE,
        ],
        'editorial' => '<p>Beri setiap barang tanda <strong>+</strong> (untuk kakak) atau <strong>−</strong> (untuk adik). Selisih total keduanya adalah nilai mutlak dari jumlah bertanda itu, jadi kita mencari jumlah bertanda yang paling dekat ke 0. Ada 2<sup>36</sup> pilihan tanda: terlalu banyak.</p>
<p><strong>Meet in the middle.</strong> Hitung semua jumlah bertanda paruh kiri (L) dan paruh kanan (R), masing-masing 2<sup>18</sup> nilai. Kita ingin meminimalkan |l + r|. Urutkan R; untuk setiap l, nilai r terbaik adalah elemen pertama yang ≥ −l atau elemen tepat sebelumnya (binary search). Dengan L juga diurutkan, binary search bisa diganti dua pointer.</p>
<p>Total O(2<sup>n/2</sup> · n). Nilainya sampai 3,6·10<sup>13</sup>: wajib <code>long long</code>.</p>',
        'hints' => [
            'Selisih = |jumlah barang kakak − jumlah barang adik|. Beri tanda + atau − pada setiap barang.',
            'Belah barang menjadi dua paruh dan hitung semua jumlah bertanda di setiap paruh. Untuk l dari kiri, r kanan yang mana yang terbaik?',
            'Urutkan jumlah paruh kanan; pasangan terbaik untuk l ada di sekitar posisi −l: cek elemen pertama ≥ −l dan elemen sebelumnya.',
        ],
    ],
];
