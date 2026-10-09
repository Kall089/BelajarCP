<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Manipulasi Bit & Meet in the Middle:
 * enumerasi subset dengan bitmask, kontribusi per bit, greedy bit dari yang tertinggi, dan meet in the middle.
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

$BIT30 = (1 << 30) - 1;

/** Semua jumlah subset dari $v, terurut naik (digandakan lalu digabung seperti merge sort). */
$jumlahSubset = function (array $v): array {
    $s = [0];
    foreach ($v as $x) {
        $m = count($s);
        $g = [];
        $i = 0;
        $j = 0;
        while ($i < $m && $j < $m) {
            if ($s[$i] <= $s[$j] + $x) {
                $g[] = $s[$i++];
            } else {
                $g[] = $s[$j++] + $x;
            }
        }
        while ($i < $m) {
            $g[] = $s[$i++];
        }
        while ($j < $m) {
            $g[] = $s[$j++] + $x;
        }
        $s = $g;
    }

    return $s;
};

return [
    [
        'slug' => 'paket-oleh-oleh-bitmask',
        'lesson' => 'bit',
        'title' => 'Paket Oleh-oleh',
        'difficulty' => 'Mudah',
        'tags' => ['bitmask', 'enumerasi subset', 'brute force'],
        'statement' => '<p>Rani sedang berada di toko oleh-oleh dan tertarik pada <strong>N</strong> barang. Barang ke-i berharga <code>a<sub>i</sub></code> rupiah, dan setiap barang hanya tersedia satu buah.</p>
<p>Supaya mendapat gratis ongkos kirim, total belanjanya harus paling sedikit <strong>L</strong> rupiah. Namun uang Rani hanya <strong>R</strong> rupiah. Ada berapa banyak cara memilih barang yang dibeli (paling sedikit satu barang) sehingga total harganya berada di antara L dan R, termasuk L dan R itu sendiri?</p>
<p>Dua cara dianggap berbeda jika ada barang yang dibeli pada salah satu cara tetapi tidak pada cara lainnya. Barang yang harganya sama tetap dianggap barang yang berbeda.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N L R</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Banyak cara memilih barang dengan total harga di rentang [L, R].</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 20</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li><li>1 ≤ L ≤ R ≤ 2 · 10<sup>7</sup></li></ul>',
        'samples' => [
            ['input' => "4 5 10\n3 4 6 2\n", 'explanation' => 'Ada 8 cara: {3, 2} = 5, {6} = 6, {4, 2} = 6, {3, 4} = 7, {6, 2} = 8, {3, 6} = 9, {3, 4, 2} = 9, dan {4, 6} = 10. Cara lain bertotal kurang dari 5 (misalnya {4}) atau lebih dari 10 (misalnya {4, 6, 2} = 12).'],
            ['input' => "3 4 4\n2 2 2\n", 'explanation' => 'Total 4 hanya didapat dengan membeli tepat dua barang. Ada 3 cara memilih dua barang dari tiga barang yang berbeda, walaupun harganya sama.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $lo, int $hi, ?int $l = null, ?int $r = null) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand($lo, $hi);
                }
                $sum = array_sum($a);
                if ($l === null) {
                    $l = mt_rand(1, max(1, intdiv($sum, 2)));
                    $r = mt_rand($l, max($l, $sum));
                }

                return "$n $l $r\n".implode(' ', $a)."\n";
            };
            // L = R = total harga sebuah subset acak, jadi jawabannya paling sedikit 1
            $pas = function (int $n) {
                $a = [];
                $t = 0;
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, 1000000);
                    if (mt_rand(0, 1)) {
                        $t += $a[$i];
                    }
                }
                $t = max($t, $a[0]);

                return "$n $t $t\n".implode(' ', $a)."\n";
            };
            $pangkat = [];
            for ($i = 0; $i < 20; $i++) {
                $pangkat[] = 1 << $i;
            }
            T::shuffle($pangkat);

            return [
                "1 1 1\n1\n",
                "1 2 5\n1\n",
                "3 1 100\n5 5 5\n",
                $mk(8, 1, 20),
                "20 10 10\n".implode(' ', array_fill(0, 20, 1))."\n",
                "20 12345 678901\n".implode(' ', $pangkat)."\n",
                $mk(20, 1, 1000000, 1, 20000000),
                $mk(20, 1, 1000000, 9000000, 11000000),
                $pas(20),
                "20 20000000 20000000\n".implode(' ', array_fill(0, 20, 1000000))."\n",
                $mk(17, 1, 1000),
                $mk(20, 1, 10, 55, 55),
                $mk(20, 1, 1000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $l, $r] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $total = array_fill(0, 1 << $n, 0);
            $hasil = 0;
            for ($mask = 1; $mask < (1 << $n); $mask++) {
                $low = $mask & -$mask;
                $i = 0;
                while ((1 << $i) !== $low) {
                    $i++;
                }
                $t = $total[$mask ^ $low] + $a[$i];
                $total[$mask] = $t;
                if ($l <= $t && $t <= $r) {
                    $hasil++;
                }
            }

            return (string) $hasil;
        },
        'starter' => $st("    int n, l, r;\n    cin >> n >> l >> r;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;", "const [n, l, r] = readInts();\nconst a = readInts();", "n, l, r = map(int, input().split())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, l, r;
    cin >> n >> l >> r;
    vector<int> a(n);
    for (auto& x : a) cin >> x;

    // Setiap mask 1..2^N - 1 mewakili satu pilihan barang:
    // bit ke-i menyala  <=>  barang ke-i dibeli.
    int hasil = 0;
    for (int mask = 1; mask < (1 << n); mask++) {
        int total = 0;                      // paling besar 20 * 10^6, masih muat di int
        for (int i = 0; i < n; i++) {
            if ((mask >> i) & 1) total += a[i];
        }
        if (l <= total && total <= r) hasil++;
    }
    cout << hasil << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, l, r] = readInts();
const a = readInts();

// Setiap mask 1..2^N - 1 mewakili satu pilihan barang:
// bit ke-i menyala berarti barang ke-i dibeli.
let hasil = 0;
for (let mask = 1; mask < (1 << n); mask++) {
  let total = 0;
  for (let i = 0; i < n; i++) {
    if ((mask >> i) & 1) total += a[i];
  }
  if (l <= total && total <= r) hasil++;
}
console.log(hasil);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, l, r = map(int, input().split())
a = list(map(int, input().split()))

# total[mask] = total harga barang yang bit-nya menyala di mask.
# Buang bit terendah (lowbit = mask & -mask): sisanya lebih kecil dari mask
# sehingga totalnya sudah dihitung. Satu langkah per mask, O(2^N);
# memeriksa N bit untuk setiap mask terlalu lambat di Python.
total = [0] * (1 << n)
hasil = 0
for mask in range(1, 1 << n):
    low = mask & -mask
    t = total[mask ^ low] + a[low.bit_length() - 1]   # low = 2^i -> barang ke-i
    total[mask] = t
    if l <= t <= r:
        hasil += 1
print(hasil)
CODE,
        ],
        'editorial' => '<p>Setiap cara memilih barang adalah sebuah <strong>subset</strong> dari N barang, dan subset bisa ditulis sebagai bitmask: bilangan <code>mask</code> dari 0 sampai 2<sup>N</sup> − 1 dengan bit ke-i menyala jika barang ke-i dibeli. Cukup coba semua mask, hitung total harganya, lalu periksa apakah L ≤ total ≤ R.</p>
<p>Cara paling langsung memeriksa N bit (<code>(mask &gt;&gt; i) &amp; 1</code>) untuk setiap mask, total O(2<sup>N</sup> · N) ≈ 2 · 10<sup>7</sup> langkah. Ini ringan untuk C++ dan JavaScript, tetapi terlalu lambat untuk Python.</p>
<p>Versi O(2<sup>N</sup>) memakai <strong>lowbit</strong>: <code>low = mask &amp; -mask</code> adalah bit terendah yang menyala, misalnya bit ke-i. Maka <code>total[mask] = total[mask ^ low] + a[i]</code>. Karena <code>mask ^ low</code> lebih kecil dari mask, nilainya sudah dihitung lebih dulu. Solusi Python memakai cara ini.</p>
<p>Mask 0 (tidak membeli apa pun) bertotal 0 sehingga tidak pernah terhitung karena L ≥ 1. Total terbesar 20 · 10<sup>6</sup> masih muat di <code>int</code>.</p>',
        'hints' => [
            'N paling besar 20, jadi banyak cara memilih barang hanya sekitar satu juta. Semuanya bisa dicoba.',
            'Wakili setiap pilihan dengan bilangan mask 0..2^N − 1: bit ke-i menyala berarti barang ke-i dibeli.',
            'Untuk setiap mask, jumlahkan a[i] untuk setiap bit i yang menyala ((mask >> i) & 1), lalu periksa apakah totalnya di [L, R].',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'jumlah-xor-semua-pasangan',
        'lesson' => 'bit',
        'title' => 'Total Gangguan Sinyal',
        'difficulty' => 'Sedang',
        'tags' => ['bit', 'xor', 'kontribusi per bit'],
        'statement' => '<p>Di sebuah kota berdiri <strong>N</strong> menara pemancar. Menara ke-i memakai kode frekuensi <code>a<sub>i</sub></code>. Setiap dua menara saling mengganggu, dan besar gangguan antara menara i dan menara j adalah <code>a<sub>i</sub> XOR a<sub>j</sub></code>.</p>
<p>XOR (ditulis <code>^</code> di C++, JavaScript, dan Python) bekerja bit demi bit: bit hasilnya 1 jika kedua bit berbeda, dan 0 jika sama. Contohnya 6 XOR 3 = 110<sub>2</sub> XOR 011<sub>2</sub> = 101<sub>2</sub> = 5.</p>
<p>Dinas komunikasi ingin mengetahui total gangguan di kota itu, yaitu jumlah <code>a<sub>i</sub> XOR a<sub>j</sub></code> untuk <strong>semua</strong> pasangan menara 1 ≤ i &lt; j ≤ N.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Total gangguan semua pasangan menara (tanpa modulo).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>5</sup></li><li>0 ≤ a<sub>i</sub> &lt; 2<sup>30</sup></li></ul>',
        'samples' => [
            ['input' => "3\n1 2 3\n", 'explanation' => '1 XOR 2 = 3, 1 XOR 3 = 2, dan 2 XOR 3 = 1, sehingga totalnya 3 + 2 + 1 = 6.'],
            ['input' => "4\n5 5 0 7\n", 'explanation' => 'Lihat per bit (5 = 101<sub>2</sub>, 7 = 111<sub>2</sub>). Bit bernilai 1 menyala di 5, 5, 7 dan mati di 0: ada 3 · 1 = 3 pasangan yang berbeda di bit ini, menyumbang 3 · 1 = 3. Bit bernilai 2 hanya menyala di 7: 1 · 3 = 3 pasangan, menyumbang 3 · 2 = 6. Bit bernilai 4 menyala di 5, 5, 7: 3 · 1 = 3 pasangan, menyumbang 3 · 4 = 12. Totalnya 3 + 6 + 12 = 21.'],
        ],
        'tests' => function () use ($arr, $BIT30) {
            $ekstrem = [];
            for ($i = 0; $i < 100000; $i++) {
                $ekstrem[] = $i % 2 === 0 ? 0 : $BIT30;
            }
            T::shuffle($ekstrem);
            $sama = mt_rand(0, $BIT30);
            $tiga = [mt_rand(0, $BIT30), mt_rand(0, $BIT30), mt_rand(0, $BIT30)];
            $dariTiga = [];
            for ($i = 0; $i < 100000; $i++) {
                $dariTiga[] = $tiga[mt_rand(0, 2)];
            }

            return [
                "1\n0\n",
                "2\n0 $BIT30\n",
                "6\n1 2 4 8 16 32\n",
                "10\n".$arr(10, 0, 63)."\n",
                "1000\n".$arr(1000, 0, $BIT30)."\n",
                "100000\n".$arr(100000, 0, $BIT30)."\n",
                "100000\n".implode(' ', $ekstrem)."\n",
                "100000\n".$arr(100000, 0, 1)."\n",
                "100000\n".implode(' ', array_fill(0, 100000, $sama))."\n",
                "99999\n".$arr(99999, 0, 1000)."\n",
                "100000\n".implode(' ', $dariTiga)."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $a = T::ints($lines[1]);
            $cnt = array_fill(0, 30, 0);
            foreach ($a as $x) {
                for ($b = 0; $b < 30; $b++) {
                    $cnt[$b] += ($x >> $b) & 1;
                }
            }
            $total = 0;
            for ($b = 0; $b < 30; $b++) {
                $total += $cnt[$b] * ($n - $cnt[$b]) * (1 << $b);
            }

            return (string) $total;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;", "const n = Number(readLine());\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
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

    // Bit ke-b dari (x XOR y) bernilai 1 tepat jika bit ke-b x dan y berbeda.
    // Jika 'satu' bilangan punya bit b menyala, ada satu * (n - satu) pasangan
    // yang berbeda di bit itu, masing-masing menyumbang 2^b.
    long long total = 0;                    // jawaban bisa sekitar 2,7 * 10^18
    for (int b = 0; b < 30; b++) {
        long long satu = 0;
        for (int x : a) satu += (x >> b) & 1;
        long long nol = n - satu;
        total += satu * nol * (1LL << b);   // satu * nol bisa 2,5 * 10^9: harus long long
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();

// Jawaban bisa sekitar 2,7 * 10^18, melebihi batas tepat Number (9 * 10^15),
// jadi total disimpan sebagai BigInt.
let total = 0n;
for (let b = 0; b < 30; b++) {
  let satu = 0;
  for (let i = 0; i < n; i++) satu += (a[i] >> b) & 1;
  const pasangan = satu * (n - satu);   // paling besar 2,5 * 10^9: masih tepat di Number
  total += BigInt(pasangan) << BigInt(b);
}
console.log(String(total));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))

# Bit ke-b dari (x XOR y) bernilai 1 tepat jika bit ke-b x dan y berbeda.
total = 0
for b in range(30):
    satu = sum((x >> b) & 1 for x in a)    # banyak bilangan dengan bit b menyala
    total += satu * (n - satu) << b       # tiap pasangan berbeda menyumbang 2^b
print(total)
CODE,
        ],
        'editorial' => '<p>Mencoba semua pasangan butuh O(N<sup>2</sup>) ≈ 5 · 10<sup>9</sup> operasi, terlalu lambat. Kuncinya: XOR bekerja <strong>bit demi bit</strong> tanpa saling memengaruhi (tidak ada simpanan seperti pada penjumlahan), jadi kita bisa menghitung sumbangan setiap bit secara terpisah.</p>
<p>Ambil bit ke-b. Bit ke-b dari <code>a<sub>i</sub> XOR a<sub>j</sub></code> bernilai 1 tepat jika satu bilangan punya bit itu dan yang lain tidak. Jika ada <code>c<sub>b</sub></code> bilangan yang bit ke-b-nya menyala, banyak pasangan seperti itu adalah <code>c<sub>b</sub> · (N − c<sub>b</sub>)</code>, dan masing-masing menyumbang 2<sup>b</sup>. Jadi</p>
<p style="text-align:center"><code>jawaban = Σ<sub>b=0..29</sub> c<sub>b</sub> · (N − c<sub>b</sub>) · 2<sup>b</sup></code></p>
<p>Menghitung semua c<sub>b</sub> butuh O(30 · N).</p>
<p><strong>Awas overflow:</strong> c<sub>b</sub> · (N − c<sub>b</sub>) bisa 2,5 · 10<sup>9</sup> (tidak muat di <code>int</code>), dan jawabannya bisa mendekati 2,7 · 10<sup>18</sup>. Di C++ pakai <code>long long</code> (batasnya sekitar 9,2 · 10<sup>18</sup>). Di JavaScript, Number hanya tepat sampai sekitar 9 · 10<sup>15</sup>, jadi jumlahkan dengan <code>BigInt</code>. Python aman karena bilangan bulatnya tidak terbatas.</p>',
        'hints' => [
            'Mencoba semua pasangan terlalu lambat. XOR bekerja bit demi bit; coba hitung sumbangan setiap bit secara terpisah.',
            'Bit ke-b dari a XOR b bernilai 1 hanya jika tepat satu dari keduanya punya bit itu. Berapa banyak pasangan seperti itu jika ada c bilangan yang bit ke-b-nya menyala?',
            'Jawaban = jumlah c_b · (N − c_b) · 2^b untuk b = 0..29. Perhatikan jawabannya bisa sekitar 2,7 · 10^18 (long long, BigInt di JavaScript).',
        ],
    ],

    [
        'slug' => 'and-tim-robot',
        'lesson' => 'bit',
        'title' => 'Tim Robot Paling Kompak',
        'difficulty' => 'Sedang',
        'tags' => ['bit', 'and', 'greedy'],
        'statement' => '<p>Laboratorium robotika sekolah punya <strong>N</strong> robot. Kemampuan robot ke-i dicatat sebagai bilangan <code>a<sub>i</sub></code>: bit ke-j (bernilai 2<sup>j</sup>) bernilai 1 jika robot itu menguasai keahlian ke-j. Semakin tinggi nomor keahliannya, semakin berharga keahlian itu.</p>
<p>Untuk sebuah lomba, harus dipilih tim berisi tepat <strong>K</strong> robot. Tim hanya bisa memakai keahlian yang dikuasai oleh <em>semua</em> anggotanya, sehingga nilai kekompakan tim adalah hasil AND dari kode kemampuan seluruh anggotanya.</p>
<p>AND (ditulis <code>&amp;</code> di C++, JavaScript, dan Python) bekerja bit demi bit: bit hasilnya 1 hanya jika semua bit yang di-AND-kan bernilai 1. Contohnya 12 AND 10 = 1100<sub>2</sub> AND 1010<sub>2</sub> = 1000<sub>2</sub> = 8.</p>
<p>Berapa nilai kekompakan terbesar yang bisa dicapai?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Nilai kekompakan terbesar dari tim berisi tepat K robot.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 10<sup>5</sup></li><li>0 ≤ a<sub>i</sub> &lt; 2<sup>30</sup></li></ul>',
        'samples' => [
            ['input' => "5 2\n12 10 7 14 3\n", 'explanation' => 'Bit bernilai 8 dimiliki 12, 10, dan 14, jadi tim terbaik diambil dari ketiganya (hasil AND tim lain paling besar 7). Di antara mereka, bit bernilai 4 hanya dimiliki 12 dan 14, sehingga tim terbaik adalah {12, 14}: 1100<sub>2</sub> AND 1110<sub>2</sub> = 1100<sub>2</sub> = 12.'],
            ['input' => "4 3\n8 7 6 5\n", 'explanation' => 'Robot terkuat (8 = 1000<sub>2</sub>) tidak punya keahlian bersama dengan robot lain. Tim terbaik adalah {7, 6, 5}: 111<sub>2</sub> AND 110<sub>2</sub> AND 101<sub>2</sub> = 100<sub>2</sub> = 4.'],
        ],
        'tests' => function () use ($arr, $BIT30) {
            // m bilangan memuat semua bit pola T (ditambah bit acak), sisanya lebih jarang bitnya.
            // $tinggi: T memuat bit 29 dan bilangan lain < 2^29, sehingga jawaban pasti memuat T.
            $mk = function (int $n, int $k, int $m, int $bitT, bool $tinggi = false) use ($BIT30) {
                $t = $tinggi ? 1 << 29 : 0;
                for ($i = 0; $i < $bitT; $i++) {
                    $t |= 1 << mt_rand(0, 29);
                }
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $acak = mt_rand(0, $BIT30) & mt_rand(0, $BIT30);
                    $lain = $acak & mt_rand(0, $BIT30);
                    $a[] = $i < $m ? ($t | $acak) : ($tinggi ? $lain & ((1 << 29) - 1) : $lain);
                }
                T::shuffle($a);

                return "$n $k\n".implode(' ', $a)."\n";
            };

            return [
                "1 1\n5\n",
                "2 2\n6 3\n",
                "4 2\n0 0 0 0\n",
                "8 3\n".$arr(8, 0, 63)."\n",
                $mk(1000, 10, 15, 8, true),
                "100000 1\n".$arr(100000, 0, $BIT30)."\n",
                "100000 2\n".$arr(100000, 0, $BIT30)."\n",
                $mk(100000, 100000, 100000, 10),
                $mk(100000, 5000, 5000, 12),
                "100000 50000\n".$arr(100000, 0, $BIT30)."\n",
                $mk(100000, 300, 400, 6, true),
                "100000 7\n".$arr(100000, 0, 1000)."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $jawab = 0;
            for ($b = 29; $b >= 0; $b--) {
                $coba = $jawab | (1 << $b);
                $cnt = 0;
                foreach ($a as $x) {
                    if (($x & $coba) === $coba) {
                        $cnt++;
                    }
                }
                if ($cnt >= $k) {
                    $jawab = $coba;
                }
            }

            return (string) $jawab;
        },
        'starter' => $st("    int n, k;\n    cin >> n >> k;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;", "const [n, k] = readInts();\nconst a = readInts();", "n, k = map(int, input().split())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<int> a(n);
    for (auto& x : a) cin >> x;

    // Bangun jawaban dari bit tertinggi. Bit b lebih berharga daripada
    // gabungan semua bit di bawahnya (2^b > 2^b - 1), jadi ambil bit b
    // selama masih ada K robot yang memiliki semua bit yang sudah dipilih + bit b.
    int jawab = 0;
    for (int b = 29; b >= 0; b--) {
        int coba = jawab | (1 << b);
        int cnt = 0;
        for (int x : a) {
            if ((x & coba) == coba) cnt++;  // x memuat semua bit di 'coba'
        }
        if (cnt >= k) jawab = coba;
    }
    cout << jawab << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const a = readInts();

// Bangun jawaban dari bit tertinggi: ambil bit b jika masih ada
// K robot yang memuat semua bit terpilih ditambah bit b.
let jawab = 0;
for (let b = 29; b >= 0; b--) {
  const coba = jawab | (1 << b);
  let cnt = 0;
  for (let i = 0; i < n; i++) {
    if ((a[i] & coba) === coba) cnt++;
  }
  if (cnt >= k) jawab = coba;
}
console.log(jawab);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
a = list(map(int, input().split()))

# Bangun jawaban dari bit tertinggi: ambil bit b jika masih ada
# K robot yang memuat semua bit terpilih ditambah bit b.
jawab = 0
for b in range(29, -1, -1):
    coba = jawab | (1 << b)
    cnt = sum(1 for x in a if x & coba == coba)
    if cnt >= k:
        jawab = coba
print(jawab)
CODE,
        ],
        'editorial' => '<p>Mencoba semua tim jelas mustahil. Perhatikan sifat bilangan biner: bit ke-b bernilai 2<sup>b</sup>, lebih besar daripada jumlah semua bit di bawahnya (2<sup>b</sup> − 1). Jadi hasil AND yang memiliki bit tinggi selalu lebih baik daripada yang tidak memilikinya, apa pun bit-bit rendahnya. Ini membuat kita boleh <strong>serakah dari bit tertinggi</strong>.</p>
<p>Simpan <code>jawab</code> (mula-mula 0). Untuk b = 29 turun ke 0, coba <code>coba = jawab | (1 &lt;&lt; b)</code>. Hasil AND sebuah tim memuat semua bit di <code>coba</code> tepat jika setiap anggotanya memuat semua bit itu, yaitu <code>(a<sub>i</sub> &amp; coba) == coba</code>. Jika ada paling sedikit K robot seperti itu, bentuk tim dari K robot mana saja di antara mereka, dan bit b boleh diambil: <code>jawab = coba</code>. Jika tidak, bit b mustahil ada bersama bit-bit yang sudah dipilih, jadi lewati.</p>
<p>Setiap bit diputuskan dengan satu kali menyisir array, total O(30 · N).</p>
<p><strong>Jebakan:</strong> memilih K bilangan terbesar tidak benar. Pada contoh kedua, 8 adalah bilangan terbesar, tetapi tim yang memuatnya hanya bernilai 0.</p>',
        'hints' => [
            'Bit bernilai 2^b lebih besar daripada jumlah semua bit di bawahnya. Bit mana yang paling penting untuk dimiliki hasil AND?',
            'Putuskan bit dari yang tertinggi. Hasil AND memuat sekumpulan bit S tepat jika setiap anggota tim memuat semua bit di S.',
            'Untuk b = 29..0: coba = jawab | 2^b. Hitung robot dengan (a & coba) == coba; jika ada ≥ K, jawab = coba.',
        ],
    ],

    [
        'slug' => 'mantra-meet-in-the-middle',
        'lesson' => 'bit',
        'title' => 'Kombinasi Mantra',
        'difficulty' => 'Sulit',
        'tags' => ['meet in the middle', 'bitmask', 'two pointer', 'sorting'],
        'statement' => '<p>Dalam sebuah gim, penyihir Arka menguasai <strong>N</strong> mantra. Mantra ke-i membutuhkan <code>a<sub>i</sub></code> poin mana. Sebelum bertarung, Arka memilih sekumpulan mantra untuk dirapal sekaligus. Setiap mantra paling banyak dipilih sekali, dan total mana yang dibutuhkan tidak boleh melebihi persediaan mananya, yaitu <strong>X</strong> poin.</p>
<p>Ada berapa banyak kumpulan mantra yang bisa ia pilih? Kumpulan kosong (tidak merapal apa pun) juga dihitung. Mantra yang kebutuhan mananya sama tetap dianggap mantra yang berbeda.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N X</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Banyak kumpulan mantra dengan total mana paling banyak X.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 40</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>0 ≤ X ≤ 4 · 10<sup>10</sup></li></ul>',
        'samples' => [
            ['input' => "4 10\n3 5 6 8\n", 'explanation' => 'Kumpulan yang memenuhi: kosong (0), {3}, {5}, {6}, {8}, {3, 5} = 8, dan {3, 6} = 9. Pasangan lain sudah lebih dari 10 (misalnya {3, 8} = 11), apalagi tiga mantra atau lebih. Totalnya 7 kumpulan.'],
            ['input' => "3 0\n1 1 1\n", 'explanation' => 'Dengan mana 0, satu-satunya pilihan adalah tidak merapal mantra apa pun.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $lo, int $hi, $x) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand($lo, $hi);
                }
                $sum = array_sum($a);
                $x = is_callable($x) ? $x($sum) : $x;

                return "$n $x\n".implode(' ', $a)."\n";
            };
            $acak = fn ($s) => mt_rand(0, $s);

            return [
                "1 0\n5\n",
                "1 5\n5\n",
                "2 3\n1 2\n",
                "40 20000000000\n".implode(' ', array_fill(0, 40, 1000000000))."\n",
                $mk(40, 1, 1000000000, fn ($s) => intdiv($s, 2)),
                $mk(40, 1, 1000000000, 0),
                $mk(40, 1, 1000000000, 40000000000),
                $mk(40, 1, 10, fn ($s) => intdiv($s, 2) + 1),
                $mk(39, 1, 1000000000, $acak),
                $mk(35, 1, 1000000000, fn ($s) => intdiv($s, 3)),
                $mk(40, 1, 1000000000, fn ($s) => mt_rand(0, 40000000000)),
                $mk(21, 1, 1000, $acak),
                $mk(40, 500000000, 1000000000, 10000000000),
            ];
        },
        'solve' => function (string $input) use ($jumlahSubset) {
            $lines = T::lines($input);
            [$n, $x] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $h = intdiv($n, 2);
            $kiri = $jumlahSubset(array_slice($a, 0, $h));
            $kanan = $jumlahSubset(array_slice($a, $h));
            $j = count($kanan);
            $hasil = 0;
            foreach ($kiri as $s) {
                while ($j > 0 && $s + $kanan[$j - 1] > $x) {
                    $j--;
                }
                if ($j === 0) {
                    break;
                }
                $hasil += $j;
            }

            return (string) $hasil;
        },
        'starter' => $st("    int n;\n    long long X;\n    cin >> n >> X;\n    vector<long long> a(n);\n    for (auto& v : a) cin >> v;", "const [n, X] = readInts();\nconst a = readInts();", "n, X = map(int, input().split())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

// Semua 2^m jumlah subset dari v, diurutkan naik.
vector<long long> semuaJumlah(const vector<long long>& v) {
    int m = v.size();
    vector<long long> s(1 << m, 0);
    for (int mask = 1; mask < (1 << m); mask++) {
        int low = mask & -mask;                          // bit terendah yang menyala
        s[mask] = s[mask ^ low] + v[__builtin_ctz(mask)];  // ctz = nomor bit terendah
    }
    sort(s.begin(), s.end());
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

    // Bagi dua: setiap kumpulan = (bagian dari kiri) + (bagian dari kanan).
    int h = n / 2;
    vector<long long> kiri = semuaJumlah(vector<long long>(a.begin(), a.begin() + h));
    vector<long long> kanan = semuaJumlah(vector<long long>(a.begin() + h, a.end()));

    // Untuk setiap s di kiri (naik), hitung banyak t di kanan dengan s + t <= X.
    // Saat s membesar, batas X - s mengecil, jadi penunjuk j hanya bergerak mundur.
    long long hasil = 0;                                 // bisa sampai 2^40
    int j = kanan.size();                                // kanan[0..j-1] masih memenuhi
    for (long long s : kiri) {
        while (j > 0 && s + kanan[j - 1] > X) j--;
        if (j == 0) break;
        hasil += j;
    }
    cout << hasil << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, X] = readInts();
const a = readInts();

// Semua 2^m jumlah subset dari v, diurutkan naik.
// Jumlah paling besar 4 * 10^10: masih tepat di Number (Float64Array).
function semuaJumlah(v) {
  const m = v.length;
  const s = new Float64Array(1 << m);
  for (let mask = 1; mask < (1 << m); mask++) {
    const low = mask & -mask;                        // bit terendah yang menyala
    s[mask] = s[mask ^ low] + v[31 - Math.clz32(low)];
  }
  return s.sort();                                   // TypedArray: diurutkan sebagai angka
}

const h = Math.floor(n / 2);
const kiri = semuaJumlah(a.slice(0, h));
const kanan = semuaJumlah(a.slice(h));

// Dua penunjuk: s di kiri naik, batas X - s turun, j hanya mundur.
let hasil = 0;                                       // paling besar 2^40, aman untuk Number
let j = kanan.length;
for (let i = 0; i < kiri.length; i++) {
  const s = kiri[i];
  while (j > 0 && s + kanan[j - 1] > X) j--;
  if (j === 0) break;
  hasil += j;
}
console.log(hasil);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline


def semua_jumlah(v):
    # Semua jumlah subset dari v, terurut naik.
    # Untuk setiap x, daftar lama (tanpa x) dan daftar lama + x sama-sama terurut;
    # sorted() mendeteksi dua deret terurut itu dan menggabungkannya dalam waktu linear.
    s = [0]
    for x in v:
        s = sorted(s + [t + x for t in s])
    return s


n, X = map(int, input().split())
a = list(map(int, input().split()))
h = n // 2
kiri = semua_jumlah(a[:h])
kanan = semua_jumlah(a[h:])

# Dua penunjuk: s di kiri naik, batas X - s turun, j hanya mundur.
hasil = 0
j = len(kanan)
for s in kiri:
    while j > 0 and s + kanan[j - 1] > X:
        j -= 1
    if j == 0:
        break
    hasil += j
print(hasil)
CODE,
        ],
        'editorial' => '<p>Ada 2<sup>40</sup> ≈ 10<sup>12</sup> kumpulan, terlalu banyak untuk dicoba satu per satu. DP knapsack juga tidak bisa karena X sampai 4 · 10<sup>10</sup>.</p>
<p><strong>Meet in the middle:</strong> bagi mantra menjadi dua bagian, A berisi ⌊N/2⌋ mantra pertama dan B berisi sisanya. Setiap kumpulan mantra adalah gabungan sebuah subset A dan sebuah subset B, dan totalnya <code>s + t</code>. Setiap bagian hanya punya paling banyak 2<sup>20</sup> ≈ 10<sup>6</sup> subset, sehingga semua jumlahnya bisa didaftar dengan bitmask, seperti pada soal Paket Oleh-oleh (memakai lowbit: <code>jumlah[mask] = jumlah[mask ^ low] + a[nomor bit low]</code>).</p>
<p>Sekarang yang perlu dihitung adalah banyak pasangan (s, t) dengan s dari daftar A, t dari daftar B, dan s + t ≤ X. Urutkan kedua daftar. Untuk setiap s, banyak t yang memenuhi adalah banyak t ≤ X − s, bisa dicari dengan binary search. Lebih sederhana lagi dengan dua penunjuk: jika s diproses dari kecil ke besar, batas X − s terus mengecil, sehingga penunjuk j pada daftar B hanya perlu bergerak mundur.</p>
<p>Kompleksitas O(2<sup>N/2</sup> · log 2<sup>N/2</sup>) = O(2<sup>N/2</sup> · N) untuk pengurutan, sekitar 2 · 10<sup>7</sup> langkah, dengan memori dua array berukuran 2<sup>20</sup>.</p>
<p><strong>Jebakan:</strong> jumlah subset bisa sampai 4 · 10<sup>10</sup> dan jawabannya sampai 2<sup>40</sup>, jadi pakai <code>long long</code> (di JavaScript keduanya masih di bawah 2<sup>53</sup>, aman untuk Number). Jangan lupa kumpulan kosong ikut dihitung: jumlah 0 ada di kedua daftar. Di Python, membuat daftar jumlah satu per satu dengan perulangan mask cukup lambat; menggandakan daftar lalu memanggil <code>sorted</code> (yang menggabungkan dua deret terurut dalam waktu linear) jauh lebih cepat.</p>',
        'hints' => [
            'Mencoba 2^40 kumpulan terlalu lambat, tetapi 2^20 masih ringan. Bagaimana jika mantra dibagi menjadi dua kelompok?',
            'Daftarkan semua jumlah subset kelompok kiri dan kelompok kanan (masing-masing paling banyak 2^20). Setiap kumpulan adalah pasangan (s, t) dengan total s + t.',
            'Urutkan daftar kanan. Untuk setiap s di kiri, hitung banyak t ≤ X − s dengan binary search, atau dengan dua penunjuk karena X − s mengecil saat s membesar.',
        ],
        'sample_visual' => 'bars',
    ],
];
