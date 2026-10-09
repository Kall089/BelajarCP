<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Prefix Sum & Array Selisih:
 * prefix sum 2D, array selisih 1D, menghitung subarray dengan frekuensi sisa bagi, array selisih 2D.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Rentang acak [lo, hi] di 1..n: 'acak' (dua titik acak), 'kecil' (panjang ≤ 30), 'besar' (hampir seluruhnya). */
$rentang = function (int $n, string $mode): array {
    if ($mode === 'kecil') {
        $lo = mt_rand(1, $n);

        return [$lo, min($n, $lo + mt_rand(0, 29))];
    }
    if ($mode === 'besar') {
        $tepi = intdiv($n, 10);

        return [mt_rand(1, max(1, $tepi)), mt_rand($n - $tepi, $n)];
    }
    $a = mt_rand(1, $n);
    $b = mt_rand(1, $n);

    return [min($a, $b), max($a, $b)];
};

/** Persegi panjang acak "r1 c1 r2 c2"; mode 'campur' memilih salah satu mode secara acak. */
$persegi = function (int $R, int $C, string $mode) use ($rentang): string {
    if ($mode === 'campur') {
        $mode = ['acak', 'kecil', 'besar'][mt_rand(0, 2)];
    }
    [$r1, $r2] = $rentang($R, $mode);
    [$c1, $c2] = $rentang($C, $mode);

    return "$r1 $c1 $r2 $c2";
};

/**
 * Tebal salju setiap blok: array selisih 2D lalu prefix sum 2D (di tempat).
 * Hasil berupa array datar dengan lebar C + 2; blok (i, j) ada di indeks i * (C + 2) + j.
 */
$tebalSalju = function (int $R, int $C, array $ops): array {
    $W = $C + 2;
    $d = array_fill(0, ($R + 2) * $W, 0);
    foreach ($ops as [$r1, $c1, $r2, $c2, $h]) {
        $d[$r1 * $W + $c1] += $h;
        $d[$r1 * $W + $c2 + 1] -= $h;
        $d[($r2 + 1) * $W + $c1] -= $h;
        $d[($r2 + 1) * $W + $c2 + 1] += $h;
    }
    for ($i = 1; $i <= $R; $i++) {
        $b = $i * $W;
        for ($j = 1; $j <= $C; $j++) {
            $id = $b + $j;
            $d[$id] += $d[$id - $W] + $d[$id - 1] - $d[$id - $W - 1];
        }
    }

    return $d;
};

return [
    [
        'slug' => 'jumlah-persegi',
        'lesson' => 'prefix-sum',
        'title' => 'Neraca Energi Kawasan',
        'difficulty' => 'Mudah',
        'tags' => ['prefix sum 2d', 'kueri rentang'],
        'statement' => '<p>Sebuah kawasan industri dibagi menjadi petak-petak berbentuk grid dengan <strong>R</strong> baris dan <strong>C</strong> kolom. Setiap petak punya catatan energi bersih harian: angka positif berarti petak itu menghasilkan listrik lebih banyak daripada yang dipakainya (misalnya atapnya penuh panel surya), angka negatif berarti petak itu lebih banyak memakai listrik.</p>
<p>Pengelola kawasan mengajukan <strong>Q</strong> pertanyaan. Setiap pertanyaan menyebut sebuah area persegi panjang dari baris <code>r<sub>1</sub></code> sampai <code>r<sub>2</sub></code> dan kolom <code>c<sub>1</sub></code> sampai <code>c<sub>2</sub></code> (semuanya termasuk). Berapa total energi bersih semua petak di area itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>. R baris berikutnya masing-masing berisi C bilangan bulat: nilai petak-petak pada baris itu. Baris berikutnya berisi <code>Q</code>, lalu Q baris masing-masing berisi <code>r<sub>1</sub> c<sub>1</sub> r<sub>2</sub> c<sub>2</sub></code>.</p>',
        'output_format' => '<p>Q baris; baris ke-i berisi total energi bersih area pada pertanyaan ke-i.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 1 000</li><li>−10<sup>9</sup> ≤ nilai petak ≤ 10<sup>9</sup></li><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ r<sub>1</sub> ≤ r<sub>2</sub> ≤ R dan 1 ≤ c<sub>1</sub> ≤ c<sub>2</sub> ≤ C</li></ul>',
        'samples' => [
            ['input' => "3 4\n2 -1 3 0\n-4 5 1 2\n3 0 -2 6\n4\n1 1 2 2\n2 2 3 4\n3 3 3 3\n1 1 3 4\n", 'explanation' => 'Area (1, 1)–(2, 2): 2 − 1 − 4 + 5 = 2. Area (2, 2)–(3, 4): (5 + 1 + 2) + (0 − 2 + 6) = 12. Area (3, 3)–(3, 3) hanya satu petak bernilai −2. Seluruh kawasan: 4 + 4 + 7 = 15.'],
        ],
        'tests' => function () use ($persegi) {
            $mk = function (int $R, int $C, int $lo, int $hi, int $q, string $mode) use ($persegi): string {
                $out = ["$R $C"];
                for ($i = 0; $i < $R; $i++) {
                    $row = [];
                    for ($j = 0; $j < $C; $j++) {
                        $row[] = mt_rand($lo, $hi);
                    }
                    $out[] = implode(' ', $row);
                }
                $out[] = (string) $q;
                for ($k = 0; $k < $q; $k++) {
                    $out[] = $persegi($R, $C, $mode);
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n-7\n1\n1 1 1 1\n", "2 3\n1 2 3\n4 5 6\n3\n1 1 2 3\n2 2 2 3\n1 3 2 3\n", $mk(4, 5, -10, 10, 10, 'acak'),
                $mk(1, 1000, -1000000000, 1000000000, 1000, 'campur'), $mk(1000, 1, -1000000000, 1000000000, 1000, 'campur'),
                $mk(60, 50, -1000, 1000, 3000, 'campur'), $mk(600, 600, -1000000000, 1000000000, 100000, 'campur'),
                $mk(1000, 1000, -9, 9, 200000, 'campur'), $mk(200, 300, 900000000, 1000000000, 50000, 'besar'),
                $mk(500, 400, -1000000000, -900000000, 50000, 'besar'), $mk(1000, 1000, 0, 5, 200000, 'kecil'),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C] = T::ints($lines[0]);
            $W = $C + 1;
            $P = array_fill(0, ($R + 1) * $W, 0);
            for ($i = 1; $i <= $R; $i++) {
                $row = T::ints($lines[$i]);
                $b = $i * $W;
                for ($j = 1; $j <= $C; $j++) {
                    $P[$b + $j] = $row[$j - 1] + $P[$b - $W + $j] + $P[$b + $j - 1] - $P[$b - $W + $j - 1];
                }
            }
            $q = (int) $lines[$R + 1];
            $out = [];
            for ($k = 1; $k <= $q; $k++) {
                [$r1, $c1, $r2, $c2] = T::ints($lines[$R + 1 + $k]);
                $out[] = $P[$r2 * $W + $c2] - $P[($r1 - 1) * $W + $c2] - $P[$r2 * $W + $c1 - 1] + $P[($r1 - 1) * $W + $c1 - 1];
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int R, C;\n    cin >> R >> C;\n    vector<vector<long long>> a(R + 1, vector<long long>(C + 1, 0));\n    for (int i = 1; i <= R; i++)\n        for (int j = 1; j <= C; j++) cin >> a[i][j];\n    int Q;\n    cin >> Q;\n    for (int k = 0; k < Q; k++) {\n        int r1, c1, r2, c2;\n        cin >> r1 >> c1 >> r2 >> c2;\n    }",
            "const [R, C] = readInts();\nconst a = [];\nfor (let i = 0; i < R; i++) a.push(readInts());\nconst Q = Number(readLine());\nfor (let k = 0; k < Q; k++) {\n  const [r1, c1, r2, c2] = readInts();\n}",
            "data = sys.stdin.buffer.read().split()  # baca seluruh input sekaligus (cepat)\nR, C = int(data[0]), int(data[1])\na = [list(map(int, data[2 + i * C:2 + (i + 1) * C])) for i in range(R)]\npos = 2 + R * C\nQ = int(data[pos])\nkueri = list(map(int, data[pos + 1:pos + 1 + 4 * Q]))  # r1 c1 r2 c2 berurutan"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    // P[i][j] = jumlah semua petak di baris 1..i dan kolom 1..j (baris/kolom 0 bernilai 0)
    vector<vector<long long>> P(R + 1, vector<long long>(C + 1, 0));
    for (int i = 1; i <= R; i++)
        for (int j = 1; j <= C; j++) {
            long long x;
            cin >> x;
            P[i][j] = x + P[i - 1][j] + P[i][j - 1] - P[i - 1][j - 1];
        }

    int Q;
    cin >> Q;
    string out;
    for (int k = 0; k < Q; k++) {
        int r1, c1, r2, c2;
        cin >> r1 >> c1 >> r2 >> c2;
        // inklusi-eksklusi: buang area di atas dan di kiri, pojok kiri-atas ikut terbuang dua kali
        long long s = P[r2][c2] - P[r1 - 1][c2] - P[r2][c1 - 1] + P[r1 - 1][c1 - 1];
        out += to_string(s);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const W = C + 1;
// P[i * W + j] = jumlah petak di baris 1..i dan kolom 1..j.
// |nilai| ≤ 10^15, masih tepat untuk Number (batas 9 · 10^15).
const P = new Float64Array((R + 1) * W);
for (let i = 1; i <= R; i++) {
  const baris = readInts();
  for (let j = 1; j <= C; j++) {
    P[i * W + j] = baris[j - 1] + P[(i - 1) * W + j] + P[i * W + j - 1] - P[(i - 1) * W + j - 1];
  }
}
const Q = Number(readLine());
const out = new Array(Q);
for (let k = 0; k < Q; k++) {
  const [r1, c1, r2, c2] = readInts();
  out[k] = P[r2 * W + c2] - P[(r1 - 1) * W + c2] - P[r2 * W + c1 - 1] + P[(r1 - 1) * W + c1 - 1];
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate

data = sys.stdin.buffer.read().split()  # seluruh input sekaligus
R, C = int(data[0]), int(data[1])
pos = 2

# P[i][j] = jumlah petak di baris 1..i dan kolom 1..j; P[i][0] = 0
P = [[0] * (C + 1)]
for i in range(R):
    # prefix satu baris: [0, a1, a1+a2, ...]
    baris = accumulate(map(int, data[pos:pos + C]), initial=0)
    pos += C
    # P[i][j] = P[i-1][j] + (a[i][1] + ... + a[i][j])
    P.append([x + y for x, y in zip(P[-1], baris)])

Q = int(data[pos])
kueri = list(map(int, data[pos + 1:pos + 1 + 4 * Q]))
it = iter(kueri)
out = []
for r1, c1, r2, c2 in zip(it, it, it, it):
    bawah, atas = P[r2], P[r1 - 1]
    out.append(bawah[c2] - atas[c2] - bawah[c1 - 1] + atas[c1 - 1])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Menjumlahkan isi area satu per satu bisa butuh R · C = 10<sup>6</sup> langkah per pertanyaan, total 2 · 10<sup>11</sup>: terlalu lambat.</p>
<p>Buat <strong>prefix sum 2D</strong>: <code>P[i][j]</code> = jumlah semua petak di baris 1..i dan kolom 1..j, dengan baris 0 dan kolom 0 bernilai 0. Tabel ini dibangun dalam O(R · C):</p>
<p style="text-align:center"><code>P[i][j] = a[i][j] + P[i−1][j] + P[i][j−1] − P[i−1][j−1]</code></p>
<p>Area P[i−1][j] dan P[i][j−1] tumpang tindih di P[i−1][j−1], jadi bagian itu dikurangi sekali. Dengan inklusi-eksklusi yang sama, jumlah area (r<sub>1</sub>, c<sub>1</sub>)–(r<sub>2</sub>, c<sub>2</sub>) adalah</p>
<p style="text-align:center"><code>P[r2][c2] − P[r1−1][c2] − P[r2][c1−1] + P[r1−1][c1−1]</code></p>
<p>Area di atas dan area di kiri dibuang, tetapi pojok kiri-atas ikut terbuang dua kali sehingga harus ditambahkan kembali. Setiap pertanyaan O(1); total O(R · C + Q).</p>
<p><strong>Awas:</strong> jumlah bisa mencapai 10<sup>6</sup> · 10<sup>9</sup> = 10<sup>15</sup>, gunakan <code>long long</code>. Input juga besar (sekitar satu juta angka), jadi pakai I/O cepat dan kumpulkan output sebelum dicetak. Perhatikan juga indeks <code>r1 − 1</code> dan <code>c1 − 1</code>: itulah alasan baris dan kolom 0 disediakan.</p>',
        'hints' => [
            'Ingat versi satu dimensi: jumlah rentang l..r = pre[r] − pre[l−1]. Bisakah idenya diperluas ke grid?',
            'Simpan P[i][j] = jumlah persegi panjang dari pojok (1, 1) sampai (i, j). Jumlah area mana pun bisa disusun dari empat nilai P.',
            'Jawaban = P[r2][c2] − P[r1−1][c2] − P[r2][c1−1] + P[r1−1][c1−1]. Gunakan long long.',
        ],
        'sample_visual' => 'numgrid',
    ],

    [
        'slug' => 'siram-kebun',
        'lesson' => 'difference-array',
        'title' => 'Penyiram Otomatis',
        'difficulty' => 'Mudah',
        'tags' => ['array selisih', 'prefix sum', 'penambahan rentang'],
        'statement' => '<p>Bu Ratna menanam bunga di <strong>N</strong> petak yang berjajar dan diberi nomor 1 sampai N. Pagi ini petak ke-i sudah berisi <code>a<sub>i</sub></code> mililiter air. Sistem penyiram otomatis lalu menjalankan <strong>M</strong> perintah. Setiap perintah berbentuk <code>l r v</code>, artinya setiap petak bernomor l sampai r (termasuk keduanya) mendapat tambahan v mililiter air.</p>
<p>Setelah semua perintah selesai, berapa mililiter air di setiap petak?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. M baris berikutnya masing-masing berisi <code>l r v</code>.</p>',
        'output_format' => '<p>Satu baris berisi N bilangan dipisah spasi: isi air petak 1 sampai N setelah semua perintah.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ M ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ l ≤ r ≤ N</li><li>1 ≤ v ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6 3\n2 0 5 1 0 3\n1 3 2\n2 5 4\n4 6 1\n", 'explanation' => 'Setelah perintah pertama: 4 2 7 1 0 3. Setelah perintah kedua: 4 6 11 5 4 3. Setelah perintah ketiga: 4 6 11 6 5 4.'],
            ['input' => "1 3\n7\n1 1 1000000000\n1 1 1000000000\n1 1 1000000000\n", 'explanation' => 'Isi akhirnya 3 000 000 007 mililiter, melebihi batas <code>int</code> (sekitar 2,1 · 10<sup>9</sup>). Gunakan <code>long long</code>.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $amax, int $vmax, int $maxLen = 0, bool $sampaiAkhir = false): string {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(0, $amax);
                }
                $out = ["$n $m", implode(' ', $a)];
                for ($k = 0; $k < $m; $k++) {
                    if ($maxLen > 0) {
                        $l = mt_rand(1, $n);
                        $r = min($n, $l + mt_rand(0, $maxLen - 1));
                    } else {
                        $x = mt_rand(1, $n);
                        $y = mt_rand(1, $n);
                        $l = min($x, $y);
                        $r = $sampaiAkhir ? $n : max($x, $y);
                    }
                    $out[] = "$l $r ".mt_rand(1, $vmax);
                }

                return implode("\n", $out)."\n";
            };
            $penuh = "200000 200000\n".implode(' ', array_fill(0, 200000, 1000000000))."\n".str_repeat("1 200000 1000000000\n", 200000);

            return [
                "1 1\n0\n1 1 5\n", "5 0\n1 2 3 4 5\n", $mk(8, 6, 10, 10), $mk(7, 7, 0, 9, 1), $mk(1000, 1000, 1000, 1000),
                $mk(200000, 200000, 1000000000, 1000000000), $penuh, $mk(200000, 200000, 0, 5, 10),
                $mk(200000, 200000, 1000, 1000, 0, true), $mk(200000, 1, 1000000000, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $d = array_fill(0, $n + 2, 0);
            for ($k = 0; $k < $m; $k++) {
                [$l, $r, $v] = T::ints($lines[2 + $k]);
                $d[$l] += $v;
                $d[$r + 1] -= $v;
            }
            $tambah = 0;
            $out = [];
            for ($i = 1; $i <= $n; $i++) {
                $tambah += $d[$i];
                $out[] = $a[$i - 1] + $tambah;
            }

            return implode(' ', $out);
        },
        'starter' => $st(
            "    int n, m;\n    cin >> n >> m;\n    vector<long long> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];\n    for (int k = 0; k < m; k++) {\n        int l, r;\n        long long v;\n        cin >> l >> r >> v;\n    }",
            "const [n, m] = readInts();\nconst a = readInts();\nfor (let k = 0; k < m; k++) {\n  const [l, r, v] = readInts();\n}",
            "data = sys.stdin.buffer.read().split()  # baca seluruh input sekaligus (cepat)\nn, m = int(data[0]), int(data[1])\na = list(map(int, data[2:2 + n]))\nperintah = list(map(int, data[2 + n:2 + n + 3 * m]))  # l r v berurutan"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<long long> a(n + 1);
    for (int i = 1; i <= n; i++) cin >> a[i];

    // Array selisih: tambahan v pada l..r dicatat di dua titik saja.
    // Ukuran n + 2 agar d[r + 1] tetap aman saat r = n.
    vector<long long> d(n + 2, 0);
    for (int k = 0; k < m; k++) {
        int l, r;
        long long v;
        cin >> l >> r >> v;
        d[l] += v;
        d[r + 1] -= v;
    }

    // prefix sum dari d = total tambahan untuk petak i
    string out;
    long long tambah = 0;
    for (int i = 1; i <= n; i++) {
        tambah += d[i];
        if (i > 1) out += ' ';
        out += to_string(a[i] + tambah);
    }
    out += '\n';
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const a = readInts();
// Array selisih; nilai ≤ 2 · 10^14 + 10^9, masih tepat untuk Number.
const d = new Float64Array(n + 2);
for (let k = 0; k < m; k++) {
  const [l, r, v] = readInts();
  d[l] += v;
  d[r + 1] -= v;
}
const hasil = new Array(n);
let tambah = 0;
for (let i = 1; i <= n; i++) {
  tambah += d[i]; // total tambahan untuk petak i
  hasil[i - 1] = a[i - 1] + tambah;
}
console.log(hasil.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate

data = sys.stdin.buffer.read().split()  # seluruh input sekaligus
n, m = int(data[0]), int(data[1])
a = map(int, data[2:2 + n])
perintah = list(map(int, data[2 + n:2 + n + 3 * m]))

# Array selisih: tambahan v pada l..r cukup dicatat di d[l] dan d[r + 1]
d = [0] * (n + 2)
it = iter(perintah)
for l, r, v in zip(it, it, it):
    d[l] += v
    d[r + 1] -= v

# prefix sum dari d[1..n] = total tambahan untuk setiap petak
hasil = [x + t for x, t in zip(a, accumulate(d[1:n + 1]))]
print(" ".join(map(str, hasil)))
CODE,
        ],
        'editorial' => '<p>Menambahkan v ke setiap petak satu per satu memakan O(N) per perintah, total O(N · M) = 4 · 10<sup>10</sup> pada kasus terburuk.</p>
<p>Gunakan <strong>array selisih</strong> <code>d</code> berukuran N + 2 yang awalnya 0. Perintah <code>l r v</code> cukup dicatat sebagai <code>d[l] += v</code> dan <code>d[r+1] −= v</code>. Setelah semua perintah, prefix sum <code>d[1] + … + d[i]</code> sama dengan total tambahan untuk petak i:</p>
<ul><li>perintah dengan l ≤ i ≤ r menyumbang +v (hanya d[l] yang terjumlah);</li><li>perintah yang sudah berakhir sebelum i (r &lt; i) menyumbang +v − v = 0;</li><li>perintah yang baru dimulai setelah i (l &gt; i) belum terjumlah sama sekali.</li></ul>
<p>Jawaban petak i = <code>a<sub>i</sub> + (d[1] + … + d[i])</code>. Total O(N + M).</p>
<p><strong>Awas:</strong> satu petak bisa menerima hingga 2 · 10<sup>5</sup> · 10<sup>9</sup> = 2 · 10<sup>14</sup> mililiter, jauh di atas batas <code>int</code>, jadi gunakan <code>long long</code>. Sediakan juga indeks <code>r + 1 = N + 1</code> agar tidak keluar array. Karena outputnya panjang, kumpulkan dulu dalam satu string.</p>',
        'hints' => [
            'Mengulang dari l sampai r untuk setiap perintah terlalu lambat. Cukup catat di mana tambahan dimulai dan di mana berakhir.',
            'Array selisih: d[l] += v dan d[r+1] −= v. Apa arti prefix sum dari d?',
            'Prefix sum d sampai indeks i adalah total tambahan untuk petak i. Jawaban = a_i + prefix itu; gunakan long long.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'salju-kota',
        'lesson' => 'difference-array',
        'title' => 'Salju Menutup Kota',
        'difficulty' => 'Sulit',
        'tags' => ['array selisih 2d', 'prefix sum 2d', 'kueri rentang'],
        'statement' => '<p>Sebuah kota di pegunungan berbentuk grid <strong>R</strong> × <strong>C</strong> blok, dan di awal musim dingin belum ada salju sama sekali. Selama musim itu terjadi <strong>K</strong> kali hujan salju. Hujan salju ke-i menambah tebal salju sebesar <code>h<sub>i</sub></code> sentimeter pada setiap blok di persegi panjang baris <code>r<sub>1</sub></code>..<code>r<sub>2</sub></code> dan kolom <code>c<sub>1</sub></code>..<code>c<sub>2</sub></code>.</p>
<p>Sebuah blok dianggap <strong>tertutup</strong> jika tebal saljunya di akhir musim paling sedikit <strong>T</strong> sentimeter. Dinas pekerjaan umum ingin membagi alat pengeruk salju, sehingga mereka bertanya <strong>Q</strong> kali: di kawasan persegi panjang baris r<sub>1</sub>..r<sub>2</sub> dan kolom c<sub>1</sub>..c<sub>2</sub>, ada berapa blok yang tertutup?</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C K Q T</code>. K baris berikutnya masing-masing berisi <code>r<sub>1</sub> c<sub>1</sub> r<sub>2</sub> c<sub>2</sub> h</code> (satu hujan salju). Q baris berikutnya masing-masing berisi <code>r<sub>1</sub> c<sub>1</sub> r<sub>2</sub> c<sub>2</sub></code> (satu pertanyaan).</p>',
        'output_format' => '<p>Q baris; baris ke-i berisi banyak blok tertutup di kawasan pertanyaan ke-i.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 1 000</li><li>1 ≤ K, Q ≤ 100 000</li><li>1 ≤ h ≤ 10<sup>9</sup></li><li>1 ≤ T ≤ 10<sup>14</sup></li><li>1 ≤ r<sub>1</sub> ≤ r<sub>2</sub> ≤ R dan 1 ≤ c<sub>1</sub> ≤ c<sub>2</sub> ≤ C</li></ul>',
        'samples' => [
            ['input' => "4 5 3 4 4\n1 1 2 3 2\n2 2 4 4 3\n1 3 3 5 1\n1 1 4 5\n1 1 2 3\n3 2 4 3\n4 1 4 5\n", 'explanation' => 'Tebal salju akhir per baris: 2 2 3 1 1 / 2 5 6 4 1 / 0 3 4 4 1 / 0 3 3 3 0. Blok tertutup (tebal ≥ 4) ada di (2, 2), (2, 3), (2, 4), (3, 3), dan (3, 4); blok bertebal tepat 4 ikut dihitung. Kawasan pertama adalah seluruh kota: 5 blok. Kawasan kedua memuat (2, 2) dan (2, 3): 2 blok. Kawasan ketiga hanya memuat (3, 3): 1 blok. Kawasan keempat (baris 4) tidak memuat blok tertutup: 0.'],
        ],
        'tests' => function () use ($persegi, $tebalSalju) {
            // $sisi > 0: hujan salju hanya menutupi persegi kecil (sisi ≤ $sisi).
            // T diambil dari kuantil tebal sejumlah blok acak agar jawaban bervariasi.
            $mk = function (int $R, int $C, int $K, int $Q, int $hmax, int $sisi, float $kuantil) use ($persegi, $tebalSalju): string {
                $ops = [];
                $lines = [];
                for ($i = 0; $i < $K; $i++) {
                    if ($sisi > 0) {
                        $r1 = mt_rand(1, $R);
                        $c1 = mt_rand(1, $C);
                        $r2 = min($R, $r1 + mt_rand(0, $sisi - 1));
                        $c2 = min($C, $c1 + mt_rand(0, $sisi - 1));
                    } else {
                        [$r1, $c1, $r2, $c2] = T::ints($persegi($R, $C, 'acak'));
                    }
                    $h = mt_rand(1, $hmax);
                    $ops[] = [$r1, $c1, $r2, $c2, $h];
                    $lines[] = "$r1 $c1 $r2 $c2 $h";
                }
                $v = $tebalSalju($R, $C, $ops);
                $contoh = [];
                for ($i = 0; $i < 201; $i++) {
                    $contoh[] = $v[mt_rand(1, $R) * ($C + 2) + mt_rand(1, $C)];
                }
                sort($contoh);
                $T = max(1, $contoh[(int) floor($kuantil * 200)]);
                for ($i = 0; $i < $Q; $i++) {
                    $lines[] = $persegi($R, $C, 'campur');
                }

                return "$R $C $K $Q $T\n".implode("\n", $lines)."\n";
            };
            $kueriAcak = function (int $R, int $C, int $Q) use ($persegi): string {
                $lines = [];
                for ($i = 0; $i < $Q; $i++) {
                    $lines[] = $persegi($R, $C, 'campur');
                }

                return implode("\n", $lines)."\n";
            };
            $penuh = "1000 1000 100000 2000 100000000000000\n".str_repeat("1 1 1000 1000 1000000000\n", 100000).$kueriAcak(1000, 1000, 2000);

            return [
                "1 1 1 1 1\n1 1 1 1 1\n1 1 1 1\n", "2 2 1 2 5\n1 1 2 2 4\n1 1 2 2\n2 2 2 2\n", $mk(5, 5, 5, 8, 10, 0, 0.5),
                $mk(30, 40, 200, 300, 100, 0, 0.3), $mk(1, 1000, 1000, 1000, 1000, 0, 0.5), $mk(1000, 1, 1000, 1000, 1000000000, 0, 0.6),
                $mk(1000, 1000, 100000, 100000, 1000000000, 0, 0.5), $mk(1000, 1000, 100000, 100000, 10, 20, 0.85), $penuh,
                $mk(1000, 1000, 1, 100000, 1000000000, 0, 0.99), $mk(300, 700, 100000, 100000, 1, 0, 0.9),
            ];
        },
        'solve' => function (string $input) use ($tebalSalju) {
            $lines = T::lines($input);
            [$R, $C, $K, $Q, $T] = T::ints($lines[0]);
            $ops = [];
            for ($i = 1; $i <= $K; $i++) {
                $ops[] = T::ints($lines[$i]);
            }
            $v = $tebalSalju($R, $C, $ops);
            $W = $C + 2;
            $P = array_fill(0, ($R + 1) * $W, 0);
            for ($i = 1; $i <= $R; $i++) {
                $b = $i * $W;
                for ($j = 1; $j <= $C; $j++) {
                    $id = $b + $j;
                    $P[$id] = ($v[$id] >= $T ? 1 : 0) + $P[$id - $W] + $P[$id - 1] - $P[$id - $W - 1];
                }
            }
            $out = [];
            for ($k = 1; $k <= $Q; $k++) {
                [$r1, $c1, $r2, $c2] = T::ints($lines[$K + $k]);
                $out[] = $P[$r2 * $W + $c2] - $P[($r1 - 1) * $W + $c2] - $P[$r2 * $W + $c1 - 1] + $P[($r1 - 1) * $W + $c1 - 1];
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int R, C, K, Q;\n    long long T;\n    cin >> R >> C >> K >> Q >> T;\n    for (int i = 0; i < K; i++) {\n        int r1, c1, r2, c2;\n        long long h;\n        cin >> r1 >> c1 >> r2 >> c2 >> h;\n    }\n    for (int i = 0; i < Q; i++) {\n        int r1, c1, r2, c2;\n        cin >> r1 >> c1 >> r2 >> c2;\n    }",
            "const [R, C, K, Q, T] = readInts();\nfor (let i = 0; i < K; i++) {\n  const [r1, c1, r2, c2, h] = readInts();\n}\nfor (let i = 0; i < Q; i++) {\n  const [r1, c1, r2, c2] = readInts();\n}",
            "data = sys.stdin.buffer.read().split()  # baca seluruh input sekaligus (cepat)\nR, C, K, Q, T = map(int, data[:5])\nangka = list(map(int, data[5:]))\nhujan = [angka[5 * i:5 * i + 5] for i in range(K)]                   # r1 c1 r2 c2 h\nkawasan = [angka[5 * K + 4 * i:5 * K + 4 * i + 4] for i in range(Q)]  # r1 c1 r2 c2"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C, K, Q;
    long long T;
    cin >> R >> C >> K >> Q >> T;

    // Tahap 1: array selisih 2D. Satu hujan salju dicatat di empat pojok.
    vector<vector<long long>> d(R + 2, vector<long long>(C + 2, 0));
    for (int i = 0; i < K; i++) {
        int r1, c1, r2, c2;
        long long h;
        cin >> r1 >> c1 >> r2 >> c2 >> h;
        d[r1][c1] += h;
        d[r1][c2 + 1] -= h;
        d[r2 + 1][c1] -= h;
        d[r2 + 1][c2 + 1] += h;
    }

    // Prefix sum 2D dari d (di tempat) = tebal salju setiap blok (bisa 10^14 -> long long).
    // Sekaligus P = prefix sum 2D dari grid 0/1 "blok tertutup".
    vector<vector<int>> P(R + 1, vector<int>(C + 1, 0));
    for (int i = 1; i <= R; i++)
        for (int j = 1; j <= C; j++) {
            d[i][j] += d[i - 1][j] + d[i][j - 1] - d[i - 1][j - 1];
            int tutup = (d[i][j] >= T) ? 1 : 0;
            P[i][j] = tutup + P[i - 1][j] + P[i][j - 1] - P[i - 1][j - 1];
        }

    // Tahap 2: setiap pertanyaan dijawab dengan inklusi-eksklusi dalam O(1).
    string out;
    for (int i = 0; i < Q; i++) {
        int r1, c1, r2, c2;
        cin >> r1 >> c1 >> r2 >> c2;
        int banyak = P[r2][c2] - P[r1 - 1][c2] - P[r2][c1 - 1] + P[r1 - 1][c1 - 1];
        out += to_string(banyak);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C, K, Q, T] = readInts();
const W = C + 2; // lebar baris array datar (kolom 0..C+1)

// Tahap 1: array selisih 2D. |nilai| ≤ 10^14, masih tepat untuk Number.
const d = new Float64Array((R + 2) * W);
for (let i = 0; i < K; i++) {
  const [r1, c1, r2, c2, h] = readInts();
  d[r1 * W + c1] += h;
  d[r1 * W + c2 + 1] -= h;
  d[(r2 + 1) * W + c1] -= h;
  d[(r2 + 1) * W + c2 + 1] += h;
}

// Prefix 2D dari d (di tempat) = tebal salju; P = prefix 2D dari grid 0/1 "tertutup".
const P = new Int32Array((R + 1) * W);
for (let i = 1; i <= R; i++) {
  for (let j = 1; j <= C; j++) {
    const id = i * W + j;
    d[id] += d[id - W] + d[id - 1] - d[id - W - 1];
    P[id] = (d[id] >= T ? 1 : 0) + P[id - W] + P[id - 1] - P[id - W - 1];
  }
}

// Tahap 2: inklusi-eksklusi untuk setiap kawasan.
const out = new Array(Q);
for (let i = 0; i < Q; i++) {
  const [r1, c1, r2, c2] = readInts();
  out[i] = P[r2 * W + c2] - P[(r1 - 1) * W + c2] - P[r2 * W + c1 - 1] + P[(r1 - 1) * W + c1 - 1];
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate

data = sys.stdin.buffer.read().split()  # seluruh input sekaligus
R, C, K, Q, T = map(int, data[:5])
angka = list(map(int, data[5:5 + 5 * K + 4 * Q]))

# Tahap 1: array selisih 2D, satu hujan salju dicatat di empat pojok
d = [[0] * (C + 2) for _ in range(R + 2)]
it = iter(angka[:5 * K])
for r1, c1, r2, c2, h in zip(it, it, it, it, it):
    d[r1][c1] += h
    d[r1][c2 + 1] -= h
    d[r2 + 1][c1] -= h
    d[r2 + 1][c2 + 1] += h

# tebal[j] = tebal salju blok (i, j) = tebal baris sebelumnya + prefix baris d[i]
# P[i][j] = prefix sum 2D dari grid 0/1 "tertutup" (tebal >= T)
tebal = [0] * (C + 1)
P = [[0] * (C + 1)]
for i in range(1, R + 1):
    tebal = [x + y for x, y in zip(tebal, accumulate(d[i][1:C + 1], initial=0))]
    baris_tutup = accumulate([1 if x >= T else 0 for x in tebal])  # kolom 0 selalu 0 karena T >= 1
    P.append([x + y for x, y in zip(P[-1], baris_tutup)])

# Tahap 2: inklusi-eksklusi untuk setiap kawasan
it = iter(angka[5 * K:])
out = []
for r1, c1, r2, c2 in zip(it, it, it, it):
    bawah, atas = P[r2], P[r1 - 1]
    out.append(bawah[c2] - atas[c2] - bawah[c1 - 1] + atas[c1 - 1])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Soal ini terdiri dari dua tahap, dan masing-masing punya alatnya sendiri.</p>
<p><strong>Tahap 1: tebal salju setiap blok.</strong> Menambahkan h ke setiap blok di persegi panjang satu per satu bisa butuh 10<sup>6</sup> langkah per hujan, total 10<sup>11</sup>. Gunakan <strong>array selisih 2D</strong> <code>d</code> berukuran (R + 2) × (C + 2); satu hujan salju cukup dicatat di empat pojok:</p>
<p style="text-align:center"><code>d[r1][c1] += h, d[r1][c2+1] −= h, d[r2+1][c1] −= h, d[r2+1][c2+1] += h</code></p>
<p>Setelah semua hujan, prefix sum 2D dari d memberi tebal salju setiap blok. Blok (i, j) menjumlahkan semua d[a][b] dengan a ≤ i dan b ≤ j. Jika r1 ≤ i ≤ r2 dan c1 ≤ j ≤ c2, hanya pojok +h di (r1, c1) yang ikut terjumlah. Jika blok berada di kanan, di bawah, atau di kanan bawah persegi, pojok-pojok yang terjumlah saling menghapus (+h − h, atau +h − h − h + h); jika blok berada di atas atau di kiri persegi, tidak ada pojok yang terjumlah. Tahap ini O(K + R · C).</p>
<p><strong>Tahap 2: menjawab pertanyaan.</strong> Ubah grid menjadi 0/1 (1 jika tebal ≥ T), lalu buat prefix sum 2D <code>P</code> dari grid 0/1 itu. Banyak blok tertutup di kawasan adalah <code>P[r2][c2] − P[r1−1][c2] − P[r2][c1−1] + P[r1−1][c1−1]</code>, O(1) per pertanyaan. Kedua prefix bisa dihitung dalam satu putaran yang sama seperti pada kode.</p>
<p>Total O(K + R · C + Q).</p>
<p><strong>Jebakan:</strong></p>
<ul><li>Tebal satu blok bisa mencapai 10<sup>5</sup> · 10<sup>9</sup> = 10<sup>14</sup>, dan T juga bisa sebesar itu. Tebal dan T harus <code>long long</code>; banyak blok cukup <code>int</code>.</li><li>Yang ditanyakan adalah <em>banyak blok</em> yang melewati ambang, bukan total salju. Prefix sum dari tebal salju tidak bisa dipakai langsung; buat dulu grid 0/1.</li><li>Ukuran d harus R + 2 dan C + 2 karena indeks r2 + 1 dan c2 + 1 bisa bernilai R + 1 dan C + 1.</li></ul>',
        'hints' => [
            'Pisahkan soal menjadi dua tahap: (1) hitung tebal salju setiap blok, (2) hitung banyak blok tertutup di sebuah kawasan.',
            'Untuk tahap 1, gunakan array selisih 2D: satu hujan salju cukup mengubah empat pojok, lalu prefix sum 2D memulihkan tebal setiap blok.',
            'Untuk tahap 2, buat grid 0/1 (tebal ≥ T) lalu prefix sum 2D dari grid itu, dan jawab dengan inklusi-eksklusi. Tebal dan T perlu long long.',
        ],
    ],
];
