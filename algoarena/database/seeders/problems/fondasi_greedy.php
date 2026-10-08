<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Greedy & Argumen Pertukaran:
 * memilih interval, mengurutkan pekerjaan, Huffman dengan min-heap, dan Moore–Hodgson dengan max-heap.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$arr = function (int $n, int $lo, int $hi): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = mt_rand($lo, $hi);
    }

    return implode(' ', $a);
};

return [
    [
        'slug' => 'jadwal-rapat',
        'lesson' => 'greedy',
        'title' => 'Jadwal Ruang Rapat',
        'difficulty' => 'Mudah',
        'tags' => ['greedy', 'interval', 'sorting'],
        'statement' => '<p>Balai desa hanya punya satu ruang rapat. Minggu depan ada <strong>N</strong> kelompok warga yang ingin memakainya. Kelompok ke-i ingin rapat mulai menit ke-<code>s<sub>i</sub></code> dan selesai tepat pada menit ke-<code>e<sub>i</sub></code>, sehingga ruangan terpakai selama selang <code>[s<sub>i</sub>, e<sub>i</sub>)</code>.</p>
<p>Dua rapat bertabrakan jika selang waktunya beririsan. Rapat yang selesai pada menit ke-5 <strong>tidak</strong> bertabrakan dengan rapat yang mulai pada menit ke-5, karena ruangan langsung kosong begitu rapat pertama selesai. Waktu rapat tidak boleh digeser atau dipotong.</p>
<p>Pak Kades ingin menyetujui sebanyak mungkin rapat tanpa ada dua rapat yang bertabrakan. Berapa banyak rapat yang bisa disetujui?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi <code>s<sub>i</sub> e<sub>i</sub></code>.</p>',
        'output_format' => '<p>Banyak rapat terbanyak yang bisa disetujui.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ s<sub>i</sub> &lt; e<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6\n1 4\n3 5\n0 6\n4 7\n7 9\n5 8\n", 'explanation' => 'Setujui rapat [1, 4), [4, 7), dan [7, 9). Rapat yang selesai pada menit 4 boleh langsung disusul rapat yang mulai pada menit 4. Empat rapat mustahil: semua rapat berada di antara menit 0 dan 9, sedangkan empat rapat terpendek saja sudah butuh 2 + 2 + 3 + 3 = 10 menit.'],
            ['input' => "4\n1 10\n2 3\n4 5\n6 7\n", 'explanation' => 'Rapat [1, 10) mulai paling awal, tetapi jika disetujui, ketiga rapat lain tertutup. Lebih baik menyetujui tiga rapat pendek.'],
            ['input' => "3\n1 5\n4 7\n6 10\n", 'explanation' => 'Rapat [4, 7) paling pendek, tetapi bertabrakan dengan kedua rapat lain. Pilihan terbaik adalah [1, 5) dan [6, 10).'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $maxT, int $maxLen) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $s = mt_rand(0, $maxT - 1);
                    $e = min($maxT, $s + mt_rand(1, $maxLen));
                    $rows[] = "$s $e";
                }

                return "$n\n".implode("\n", $rows)."\n";
            };
            $chain = [];
            $nested = [];
            for ($i = 0; $i < 200000; $i++) {
                $chain[] = ($i * 5000).' '.(($i + 1) * 5000);
                $nested[] = $i.' '.(1000000000 - $i);
            }
            T::shuffle($chain);
            T::shuffle($nested);

            return [
                "1\n0 1\n", "3\n3 4\n1 2\n2 3\n", "4\n2 9\n2 9\n2 9\n2 9\n", "5\n0 2\n1 3\n2 4\n3 5\n4 6\n",
                $mk(10, 30, 8), $mk(1000, 10000, 100), $mk(200000, 1000000000, 1000000000), $mk(200000, 1000000000, 20000),
                "200000\n".implode("\n", $chain)."\n", "200000\n".implode("\n", $nested)."\n", $mk(200000, 1000, 3), $mk(200000, 1000000000, 1),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $s = [];
            $e = [];
            for ($i = 1; $i <= $n; $i++) {
                [$s[], $e[]] = T::ints($lines[$i]);
            }
            array_multisort($e, SORT_ASC, SORT_NUMERIC, $s);
            $cnt = 0;
            $last = 0;
            for ($i = 0; $i < $n; $i++) {
                if ($s[$i] >= $last) {
                    $cnt++;
                    $last = $e[$i];
                }
            }

            return (string) $cnt;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> s(n), e(n);\n    for (int i = 0; i < n; i++) cin >> s[i] >> e[i];", "const n = Number(readLine());\nconst s = [], e = [];\nfor (let i = 0; i < n; i++) {\n  const [a, b] = readInts();\n  s.push(a);\n  e.push(b);\n}", "n = int(input())\nrapat = [tuple(map(int, input().split())) for _ in range(n)]  # (s, e)"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    // Simpan sebagai {e, s} agar sort mengurutkan berdasarkan waktu selesai.
    vector<pair<int, int>> rapat(n);
    for (int i = 0; i < n; i++) cin >> rapat[i].second >> rapat[i].first;
    sort(rapat.begin(), rapat.end());

    int jumlah = 0;
    int akhir = 0;   // waktu selesai rapat terakhir yang disetujui (awalnya ruangan kosong)
    for (int i = 0; i < n; i++) {
        int s = rapat[i].second, e = rapat[i].first;
        // [s, e) boleh menempel pada rapat sebelumnya, jadi syaratnya >=
        if (s >= akhir) {
            jumlah++;
            akhir = e;
        }
    }
    cout << jumlah << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const s = new Int32Array(n), e = new Int32Array(n);
for (let i = 0; i < n; i++) {
  const [a, b] = readInts();
  s[i] = a;
  e[i] = b;
}
// Urutkan indeks rapat berdasarkan waktu selesai
const idx = Array.from({ length: n }, (_, i) => i);
idx.sort((x, y) => e[x] - e[y]);

let jumlah = 0;
let akhir = 0; // waktu selesai rapat terakhir yang disetujui
for (const i of idx) {
  if (s[i] >= akhir) { // boleh menempel: >= bukan >
    jumlah++;
    akhir = e[i];
  }
}
console.log(jumlah);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
rapat = [tuple(map(int, input().split())) for _ in range(n)]  # (s, e)
rapat.sort(key=lambda x: x[1])   # urutkan berdasarkan waktu selesai

jumlah = 0
akhir = 0    # waktu selesai rapat terakhir yang disetujui
for s, e in rapat:
    if s >= akhir:               # boleh menempel: >= bukan >
        jumlah += 1
        akhir = e
print(jumlah)
CODE,
        ],
        'editorial' => '<p>Coba beberapa aturan serakah yang terdengar masuk akal:</p>
<ul><li><strong>Mulai paling awal</strong> gagal pada contoh kedua: rapat [1, 10) mulai paling awal tetapi menutup semua rapat lain.</li>
<li><strong>Paling pendek</strong> gagal pada contoh ketiga: rapat [4, 7) paling pendek tetapi bertabrakan dengan dua rapat lain.</li>
<li><strong>Selesai paling awal</strong> selalu benar.</li></ul>
<p><strong>Argumen pertukaran.</strong> Misalkan g adalah rapat yang selesai paling awal, dan O adalah suatu jawaban optimal dengan o sebagai rapat di O yang selesai paling awal. Karena e<sub>g</sub> ≤ e<sub>o</sub>, mengganti o dengan g tidak menimbulkan tabrakan: semua rapat lain di O mulai pada atau setelah e<sub>o</sub> ≥ e<sub>g</sub>. Banyak rapatnya tetap sama, jadi selalu ada jawaban optimal yang memuat g. Setelah g dipilih, buang semua rapat yang mulai sebelum e<sub>g</sub> dan ulangi argumen yang sama pada sisanya.</p>
<p>Implementasinya: urutkan rapat berdasarkan waktu selesai, simpan <code>akhir</code> = waktu selesai rapat terakhir yang disetujui, lalu setujui rapat i jika <code>s<sub>i</sub> ≥ akhir</code>. Total O(N log N) karena pengurutan.</p>
<p><strong>Jebakan:</strong> rapat yang hanya bersentuhan ujungnya tidak bertabrakan, jadi syaratnya <code>≥</code>, bukan <code>&gt;</code>.</p>',
        'hints' => [
            'Coba beberapa aturan serakah pada contoh: mulai paling awal, paling pendek, selesai paling awal. Mana yang tidak pernah gagal?',
            'Rapat yang selesai paling awal selalu aman disetujui, karena ia menyisakan waktu sebanyak mungkin untuk rapat lain.',
            'Urutkan berdasarkan waktu selesai. Setujui rapat jika s ≥ waktu selesai rapat terakhir yang disetujui (perhatikan tanda ≥).',
        ],
    ],

    [
        'slug' => 'antrian-fotokopi',
        'lesson' => 'greedy',
        'title' => 'Antrean Fotokopi',
        'difficulty' => 'Mudah',
        'tags' => ['greedy', 'sorting', 'argumen pertukaran'],
        'statement' => '<p>Pagi ini <strong>N</strong> mahasiswa datang bersamaan ke kios fotokopi Bu Ratna tepat saat kios dibuka (menit ke-0). Kios hanya punya satu mesin, sehingga pesanan dikerjakan satu per satu tanpa jeda. Pesanan mahasiswa ke-i butuh <code>t<sub>i</sub></code> menit.</p>
<p>Setiap mahasiswa menunggu dari menit ke-0 sampai pesanannya <strong>selesai</strong>. Misalnya, jika pesanan seorang mahasiswa dikerjakan ketiga, waktu tunggunya adalah jumlah lama tiga pesanan pertama.</p>
<p>Bu Ratna boleh mengatur urutan pengerjaan sesukanya. Berapa <strong>total</strong> waktu tunggu semua mahasiswa yang paling kecil?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>t<sub>1</sub> t<sub>2</sub> … t<sub>N</sub></code>.</p>',
        'output_format' => '<p>Total waktu tunggu terkecil.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ t<sub>i</sub> ≤ 100 000</li></ul>',
        'samples' => [
            ['input' => "3\n5 1 3\n", 'explanation' => 'Kerjakan pesanan 1 menit, lalu 3 menit, lalu 5 menit. Ketiganya selesai pada menit 1, 4, dan 9, totalnya 14. Jika dikerjakan sesuai urutan datang (5, 1, 3), waktu selesainya 5, 6, dan 9 dengan total 20.'],
            ['input' => "4\n7 7 2 7\n", 'explanation' => 'Pesanan 2 menit didahulukan, sehingga waktu selesainya 2, 9, 16, dan 23 dengan total 50. Urutan di antara tiga pesanan 7 menit tidak berpengaruh.'],
        ],
        'tests' => function () use ($arr) {
            $desc = [];
            for ($i = 0; $i < 200000; $i++) {
                $desc[] = intdiv(200000 - $i, 2) + 1;
            }

            return [
                "1\n7\n", "2\n100000 1\n", "5\n3 3 3 3 3\n", "7\n".$arr(7, 1, 20)."\n", "1000\n".$arr(1000, 1, 1000)."\n",
                "200000\n".implode(' ', array_fill(0, 200000, 100000))."\n", "200000\n".$arr(200000, 1, 100000)."\n", "200000\n".$arr(200000, 1, 10)."\n",
                "200000\n".implode(' ', $desc)."\n", "50000\n".$arr(50000, 99990, 100000)."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $t = T::ints($lines[1]);
            sort($t);
            $now = 0;
            $total = 0;
            foreach ($t as $x) {
                $now += $x;
                $total += $now;
            }

            return (string) $total;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> t(n);\n    for (auto& x : t) cin >> x;", "const n = Number(readLine());\nconst t = readInts();", "n = int(input())\nt = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> t(n);
    for (auto& x : t) cin >> x;

    // Pesanan tercepat dikerjakan lebih dulu.
    sort(t.begin(), t.end());

    long long sekarang = 0;   // menit saat pesanan terakhir selesai
    long long total = 0;      // bisa mencapai ~2 * 10^15: wajib long long
    for (long long x : t) {
        sekarang += x;
        total += sekarang;
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
// Float64Array.sort() mengurutkan secara numerik (bukan sebagai string)
const t = Float64Array.from(readInts()).sort();

let sekarang = 0; // menit saat pesanan terakhir selesai
let total = 0;    // paling besar ~2 · 10^15, masih aman untuk Number
for (let i = 0; i < n; i++) {
  sekarang += t[i];
  total += sekarang;
}
console.log(total);
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate
input = sys.stdin.readline

n = int(input())
t = list(map(int, input().split()))
t.sort()                              # pesanan tercepat lebih dulu
# accumulate memberi waktu selesai setiap pesanan: t1, t1 + t2, ...
print(sum(accumulate(t)))
CODE,
        ],
        'editorial' => '<p>Pesanan yang dikerjakan di posisi ke-k ikut menunda dirinya sendiri dan semua N − k pesanan sesudahnya, jadi lamanya terhitung <strong>N − k + 1 kali</strong> dalam total. Posisi depan berbobot paling besar, maka pesanan tercepat sebaiknya di depan: urutkan dari yang <strong>paling cepat</strong>.</p>
<p><strong>Argumen pertukaran.</strong> Ambil dua pesanan bersebelahan A lalu B dengan t<sub>A</sub> &gt; t<sub>B</sub>. Jika keduanya ditukar, waktu selesai pesanan lain tidak berubah (yang di depan tidak terpengaruh, yang di belakang tetap menunggu t<sub>A</sub> + t<sub>B</sub>). B kini selesai t<sub>A</sub> menit lebih cepat, sedangkan A selesai t<sub>B</sub> menit lebih lambat, sehingga total berkurang t<sub>A</sub> − t<sub>B</sub> &gt; 0. Jadi urutan yang punya pasangan "terbalik" tidak mungkin optimal; urutan menaik adalah jawabannya.</p>
<p>Setelah diurutkan, jumlahkan waktu selesai setiap pesanan (prefix sum berjalan). Total O(N log N).</p>
<p><strong>Awas overflow:</strong> total bisa mencapai sekitar 2 · 10<sup>15</sup>, gunakan <code>long long</code>.</p>',
        'hints' => [
            'Lama pesanan yang dikerjakan pertama ikut ditunggu oleh semua mahasiswa. Pesanan seperti apa yang sebaiknya didahulukan?',
            'Bayangkan dua pesanan bersebelahan A lalu B dengan t_A > t_B. Apa yang terjadi pada total jika keduanya ditukar?',
            'Urutkan dari yang tercepat, lalu jumlahkan waktu selesai setiap pesanan. Pakai long long.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'gabung-tali',
        'lesson' => 'greedy',
        'title' => 'Menyambung Tali Tambang',
        'difficulty' => 'Sedang',
        'tags' => ['greedy', 'priority queue', 'huffman'],
        'statement' => '<p>Pak Darto, seorang nelayan, punya <strong>N</strong> potongan tali tambang. Potongan ke-i panjangnya <code>a<sub>i</sub></code> meter. Ia ingin menyambung semuanya menjadi satu tali panjang untuk menambatkan kapal.</p>
<p>Setiap kali menyambung, ia memilih dua tali (boleh tali hasil sambungan sebelumnya) dan menyambungnya menjadi satu. Setelah disambung, seluruh tali baru itu harus dicelup ke dalam ter agar awet, sehingga menyambung tali sepanjang <code>x</code> dan <code>y</code> berbiaya <code>x + y</code> dan menghasilkan tali sepanjang <code>x + y</code>.</p>
<p>Berapa biaya total paling kecil agar semua potongan menjadi satu tali?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> a<sub>2</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Biaya total terkecil (0 jika N = 1).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n5 2 4 2 3\n", 'explanation' => 'Sambung 2 + 2 (biaya 4), lalu 3 + 4 (biaya 7), lalu 4 + 5 (biaya 9), terakhir 7 + 9 (biaya 16). Totalnya 4 + 7 + 9 + 16 = 36. Jika setelah diurutkan tali terus disambung ke tali hasil sambungan (2 + 2, lalu + 3, + 4, + 5), biayanya 4 + 7 + 11 + 16 = 38.'],
            ['input' => "1\n7\n", 'explanation' => 'Hanya ada satu tali, tidak perlu menyambung apa pun.'],
        ],
        'tests' => function () use ($arr) {
            $mix = [];
            for ($i = 0; $i < 199999; $i++) {
                $mix[] = $i % 2 ? 1 : 1000000000;
            }
            T::shuffle($mix);
            $asc = [];
            for ($i = 1; $i <= 200000; $i++) {
                $asc[] = $i * 5000;
            }

            return [
                "1\n1000000000\n", "2\n1 1\n", "3\n1 2 3\n", "8\n".$arr(8, 1, 20)."\n", "1000\n".$arr(1000, 1, 1000)."\n",
                "200000\n".$arr(200000, 1, 1000000000)."\n", "200000\n".implode(' ', array_fill(0, 200000, 1000000000))."\n",
                "200000\n".$arr(200000, 1, 10)."\n", "199999\n".implode(' ', $mix)."\n", "200000\n".implode(' ', $asc)."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            sort($a);
            // Dua antrean: tali awal (terurut) dan tali hasil sambungan (otomatis tidak turun).
            $n = count($a);
            $q = [];
            $i = 0;
            $j = 0;
            $cost = 0;
            $take = function () use (&$a, &$q, &$i, &$j, $n) {
                if ($j >= count($q) || ($i < $n && $a[$i] <= $q[$j])) {
                    return $a[$i++];
                }

                return $q[$j++];
            };
            for ($k = 1; $k < $n; $k++) {
                $x = $take();
                $y = $take();
                $cost += $x + $y;
                $q[] = $x + $y;
            }

            return (string) $cost;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> a(n);\n    for (auto& x : a) cin >> x;", "const n = Number(readLine());\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    // Min-heap: tali terpendek selalu ada di puncak.
    priority_queue<long long, vector<long long>, greater<long long>> pq;
    for (int i = 0; i < n; i++) {
        long long x;
        cin >> x;
        pq.push(x);
    }

    long long biaya = 0;   // bisa mencapai ~3.6 * 10^15
    while (pq.size() > 1) {
        long long x = pq.top();
        pq.pop();
        long long y = pq.top();
        pq.pop();
        biaya += x + y;    // sambung dua tali terpendek
        pq.push(x + y);    // tali baru kembali masuk antrean
    }
    cout << biaya << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();

// JavaScript tidak punya priority queue bawaan: min-heap di atas Float64Array.
const heap = new Float64Array(n);
let size = 0;
function push(x) {
  let i = size++;
  while (i > 0) {
    const p = (i - 1) >> 1;
    if (heap[p] <= x) break;
    heap[i] = heap[p]; // geser induk ke bawah
    i = p;
  }
  heap[i] = x;
}
function pop() {
  const top = heap[0];
  const x = heap[--size]; // elemen terakhir dicoba ditaruh di akar
  let i = 0;
  for (;;) {
    let c = 2 * i + 1;
    if (c >= size) break;
    if (c + 1 < size && heap[c + 1] < heap[c]) c++; // anak yang lebih kecil
    if (heap[c] >= x) break;
    heap[i] = heap[c];
    i = c;
  }
  heap[i] = x;
  return top;
}

for (const x of a) push(x);
let biaya = 0; // paling besar ~3,6 · 10^15, masih aman untuk Number
while (size > 1) {
  const x = pop(), y = pop();
  biaya += x + y;
  push(x + y);
}
console.log(biaya);
CODE,
            'python' => <<<'CODE'
import sys
import heapq
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
heapq.heapify(a)                  # min-heap, O(N)

biaya = 0
for _ in range(n - 1):
    x = heapq.heappop(a)          # tali terpendek
    y = a[0]                      # tali terpendek kedua (masih di heap)
    biaya += x + y
    heapq.heapreplace(a, x + y)   # buang y sekaligus masukkan tali baru
print(biaya)
CODE,
        ],
        'editorial' => '<p>Gambarkan proses penyambungan sebagai pohon biner: setiap tali awal adalah daun, dan setiap penyambungan membuat simpul baru di atas dua tali yang disambung. Panjang tali awal i ikut dibayar setiap kali tali yang memuatnya disambung, yaitu sebanyak kedalaman daunnya. Jadi biaya total = Σ a<sub>i</sub> × kedalaman<sub>i</sub>. Ini persis masalah <strong>kode Huffman</strong>.</p>
<p><strong>Serakah:</strong> selalu sambung dua tali <em>terpendek</em> yang ada saat ini. Argumen pertukaran: pada pohon optimal, ambil dua daun bersaudara di tingkat paling dalam. Menukar isinya dengan dua tali terpendek tidak menambah biaya, karena tali yang lebih pendek pindah ke tempat yang lebih dalam dan tali yang lebih panjang naik. Jadi ada jawaban optimal yang menyambung dua tali terpendek lebih dulu. Setelah itu keduanya menjadi satu tali sepanjang jumlahnya, dan soal yang sama berulang dengan N − 1 tali.</p>
<p>Implementasi dengan <strong>min-heap</strong> (priority queue): ambil dua terkecil, tambahkan jumlahnya ke biaya, masukkan kembali jumlahnya. Ini diulang N − 1 kali, total O(N log N). JavaScript tidak punya priority queue bawaan, jadi solusinya menulis heap sendiri di atas <code>Float64Array</code>.</p>
<p><strong>Jebakan:</strong></p>
<ul><li>Mengurutkan sekali lalu terus menyambung ke tali hasil sambungan itu salah, karena tali hasil sambungan bisa lebih panjang daripada tali yang belum disentuh (contoh pertama: 38, padahal bisa 36).</li>
<li>Biaya bisa mencapai sekitar 3,6 · 10<sup>15</sup> dan panjang tali di heap bisa mencapai 2 · 10<sup>14</sup>: pakai <code>long long</code> untuk keduanya.</li>
<li>Jika N = 1, biayanya 0.</li></ul>
<p>Catatan: tali hasil sambungan muncul dengan panjang yang tidak pernah turun, sehingga setelah pengurutan soal ini juga bisa diselesaikan dalam O(N) dengan dua antrean biasa (satu untuk tali awal, satu untuk tali hasil sambungan).</p>',
        'hints' => [
            'Tali yang ikut disambung berkali-kali menyumbang panjangnya berkali-kali. Tali mana yang sebaiknya disambung paling belakang?',
            'Selalu sambung dua tali terpendek yang ada saat ini, termasuk tali hasil sambungan sebelumnya. Mengurutkan sekali di awal saja tidak cukup.',
            'Pakai min-heap: ambil dua terkecil, tambahkan jumlahnya ke biaya, lalu masukkan jumlahnya kembali. Ulangi N − 1 kali dengan long long.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'tugas-tenggat',
        'lesson' => 'greedy',
        'title' => 'Kejar Tenggat Tugas',
        'difficulty' => 'Sulit',
        'tags' => ['greedy', 'priority queue', 'penjadwalan', 'argumen pertukaran'],
        'statement' => '<p>Menjelang akhir semester, Dimas punya <strong>N</strong> tugas kuliah. Tugas ke-i butuh <code>t<sub>i</sub></code> jam pengerjaan dan harus dikumpulkan paling lambat jam ke-<code>d<sub>i</sub></code>, dihitung dari sekarang (jam ke-0).</p>
<p>Dimas mulai bekerja pada jam ke-0 dan mengerjakan tugas satu per satu tanpa jeda: sekali sebuah tugas dimulai, ia mengerjakannya terus sampai selesai sebelum memulai tugas lain. Sebuah tugas dihitung <strong>tepat waktu</strong> jika selesai pada jam ke-<code>d<sub>i</sub></code> atau sebelumnya. Tugas yang tidak mungkin tepat waktu boleh tidak dikerjakan sama sekali.</p>
<p>Berapa banyak tugas paling banyak yang bisa ia selesaikan tepat waktu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi <code>t<sub>i</sub> d<sub>i</sub></code>.</p>',
        'output_format' => '<p>Banyak tugas terbanyak yang bisa selesai tepat waktu.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ t<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ d<sub>i</sub> ≤ 10<sup>14</sup></li></ul>',
        'samples' => [
            ['input' => "4\n2 7\n5 5\n2 8\n2 6\n", 'explanation' => 'Kerjakan tugas 4, 1, lalu 3 (masing-masing 2 jam). Ketiganya selesai pada jam 2, 4, dan 6, sebelum tenggatnya (6, 7, dan 8). Keempat tugas sekaligus butuh 11 jam, melewati tenggat terakhir (jam 8). Jika tugas 2 dikerjakan karena tenggatnya paling awal, paling banyak hanya 2 tugas yang tepat waktu.'],
            ['input' => "6\n3 3\n2 5\n4 6\n1 7\n3 9\n2 9\n", 'explanation' => 'Urutan tugas 1, 2, 4, 6 selesai pada jam 3, 5, 6, dan 8; semuanya tepat waktu. Lima tugas mustahil: lima tugas tercepat saja butuh 1 + 2 + 2 + 3 + 3 = 11 jam, padahal semua tenggat paling lambat jam 9.'],
            ['input' => "2\n5 4\n3 2\n", 'explanation' => 'Setiap tugas butuh waktu lebih lama daripada tenggatnya, jadi tidak ada yang bisa tepat waktu.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $tLo, int $tHi, int $dLo, int $dHi) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = mt_rand($tLo, $tHi).' '.mt_rand($dLo, $dHi);
                }

                return "$n\n".implode("\n", $rows)."\n";
            };
            $T = 100000000000000;

            return [
                "1\n5 4\n", "1\n4 4\n", "3\n1 1\n1 1\n1 1\n", "5\n1 100\n1 100\n1 100\n1 100\n1 100\n",
                $mk(8, 1, 10, 1, 30), $mk(1000, 1, 100, 1, 30000), $mk(200000, 1, 1000000000, 1, 30000000000000), $mk(200000, 1, 1000000000, 1, 1000000000),
                $mk(200000, 1, 10, 1, $T), $mk(200000, 1, 1000000000, 50000000000000, 50000000000000), $mk(100000, 100000001, 1000000000, 1, 100000000),
                $mk(200000, 1, 1000, 1, 30000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $t = [];
            $d = [];
            for ($i = 1; $i <= $n; $i++) {
                [$t[], $d[]] = T::ints($lines[$i]);
            }
            array_multisort($d, SORT_ASC, SORT_NUMERIC, $t);
            $heap = new SplMaxHeap;
            $total = 0;
            for ($i = 0; $i < $n; $i++) {
                $heap->insert($t[$i]);
                $total += $t[$i];
                if ($total > $d[$i]) {
                    $total -= $heap->extract();
                }
            }

            return (string) count($heap);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> t(n), d(n);\n    for (int i = 0; i < n; i++) cin >> t[i] >> d[i];", "const n = Number(readLine());\nconst t = [], d = [];\nfor (let i = 0; i < n; i++) {\n  const [a, b] = readInts();\n  t.push(a);\n  d.push(b);\n}", "n = int(input())\ntugas = [tuple(map(int, input().split())) for _ in range(n)]  # (t, d)"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    // Simpan sebagai {d, t} agar sort mengurutkan berdasarkan tenggat.
    vector<pair<long long, long long>> tugas(n);
    for (int i = 0; i < n; i++) cin >> tugas[i].second >> tugas[i].first;
    sort(tugas.begin(), tugas.end());

    priority_queue<long long> dipilih;   // max-heap berisi lama tugas yang sedang dipilih
    long long total = 0;                 // total lama tugas terpilih (bisa > 2^31)
    for (int i = 0; i < n; i++) {
        long long d = tugas[i].first, t = tugas[i].second;
        dipilih.push(t);
        total += t;
        if (total > d) {
            // Terlambat: buang tugas terlama (bisa tugas yang baru masuk).
            total -= dipilih.top();
            dipilih.pop();
        }
    }
    cout << dipilih.size() << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const t = new Float64Array(n), d = new Float64Array(n); // d ≤ 10^14 masih tepat di Number
for (let i = 0; i < n; i++) {
  const [a, b] = readInts();
  t[i] = a;
  d[i] = b;
}
// Proses tugas berdasarkan tenggat paling awal
const idx = Array.from({ length: n }, (_, i) => i);
idx.sort((x, y) => d[x] - d[y]);

// Max-heap buatan sendiri berisi lama tugas yang sedang dipilih
const heap = new Float64Array(n);
let size = 0;
function push(x) {
  let i = size++;
  while (i > 0) {
    const p = (i - 1) >> 1;
    if (heap[p] >= x) break;
    heap[i] = heap[p];
    i = p;
  }
  heap[i] = x;
}
function pop() {
  const top = heap[0];
  const x = heap[--size];
  let i = 0;
  for (;;) {
    let c = 2 * i + 1;
    if (c >= size) break;
    if (c + 1 < size && heap[c + 1] > heap[c]) c++; // anak yang lebih besar
    if (heap[c] <= x) break;
    heap[i] = heap[c];
    i = c;
  }
  heap[i] = x;
  return top;
}

let total = 0;
for (const i of idx) {
  push(t[i]);
  total += t[i];
  if (total > d[i]) total -= pop(); // terlambat: buang tugas terlama
}
console.log(size);
CODE,
            'python' => <<<'CODE'
import sys
import heapq
input = sys.stdin.readline

n = int(input())
tugas = [tuple(map(int, input().split())) for _ in range(n)]  # (t, d)
tugas.sort(key=lambda x: x[1])   # urutkan berdasarkan tenggat

heap = []      # max-heap lewat nilai negatif: -heap[0] = tugas terlama yang dipilih
total = 0
for t, d in tugas:
    if total + t <= d:
        heapq.heappush(heap, -t)
        total += t
    elif heap and -heap[0] > t:
        # Terlambat: tukar tugas terlama dengan tugas baru yang lebih singkat.
        # heapreplace mengembalikan -terlama, jadi total = total + t - terlama.
        total += t + heapq.heapreplace(heap, -t)
    # selain itu tugas baru sendiri yang terlama: lewati saja
print(len(heap))
CODE,
        ],
        'editorial' => '<p><strong>Langkah 1: urutan untuk himpunan tugas yang tetap.</strong> Jika sudah diputuskan tugas mana saja yang dikerjakan, kerjakan berdasarkan tenggat paling awal. Argumen pertukaran: jika ada dua tugas bersebelahan A lalu B dengan d<sub>A</sub> &gt; d<sub>B</sub>, menukarnya membuat B selesai lebih awal, sedangkan A kini selesai pada saat B tadinya selesai, yang ≤ d<sub>B</sub> &lt; d<sub>A</sub>. Tidak ada tugas yang menjadi terlambat. Jadi sebuah himpunan tugas bisa tepat waktu semua jika dan hanya jika urutan berdasarkan tenggat berhasil.</p>
<p><strong>Langkah 2: memilih himpunan (algoritma Moore–Hodgson).</strong> Proses tugas berdasarkan tenggat menaik sambil menyimpan himpunan terpilih dan total lamanya. Masukkan tugas baru. Jika total sekarang melewati tenggat tugas itu, buang tugas <strong>terlama</strong> di himpunan (bisa jadi tugas baru itu sendiri). Tugas terlama diambil dengan <strong>max-heap</strong>, dan jawabannya adalah ukuran heap di akhir.</p>
<p>Mengapa benar? Setelah memproses k tugas pertama (dalam urutan tenggat), himpunan terpilih (1) berukuran maksimum di antara semua himpunan tepat waktu dari k tugas itu, dan (2) di antara himpunan berukuran maksimum, total lamanya paling kecil, sehingga menyisakan ruang terbanyak untuk tugas berikutnya. Ketika tugas baru membuat terlambat, ukurannya memang tidak bisa bertambah, dan membuang yang terlama memberi total lama terkecil untuk ukuran yang sama. Setelah pembuangan, total lama tidak lebih besar dari sebelumnya, jadi semua tugas terpilih tetap tepat waktu.</p>
<p>Kompleksitas O(N log N) untuk pengurutan dan operasi heap.</p>
<p><strong>Jebakan:</strong></p>
<ul><li>Melewati tugas baru begitu saja ketika tidak muat itu salah: pada contoh pertama, tugas 5 jam bertenggat jam 5 terambil lebih dulu dan menghalangi tugas-tugas pendek sesudahnya.</li>
<li>Mengerjakan tugas dari yang tersingkat tanpa melihat tenggat bisa membuat tugas bertenggat awal terlambat, misalnya tugas 1 jam bertenggat jam 100 dan tugas 2 jam bertenggat jam 2. Urutan pengerjaan harus berdasarkan tenggat.</li>
<li>Tenggat sampai 10<sup>14</sup> dan total lama bisa melewati 2<sup>31</sup>: pakai <code>long long</code>.</li></ul>',
        'hints' => [
            'Jika himpunan tugas yang dikerjakan sudah ditentukan, urutan terbaik untuk mengerjakannya adalah berdasarkan tenggat paling awal. Mengapa?',
            'Proses tugas berdasarkan tenggat. Jika tugas baru membuat total waktu melewati tenggatnya, satu tugas harus dibuang. Tugas mana yang paling menguntungkan untuk dibuang?',
            'Buang tugas terlama (bisa jadi tugas baru itu sendiri) dengan max-heap. Jawabannya adalah banyak tugas yang tersisa di heap. Total waktu bisa melewati 2^31, pakai long long.',
        ],
    ],
];
