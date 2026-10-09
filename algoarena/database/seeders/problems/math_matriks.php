<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Eksponensiasi Matriks: perkalian dan pangkat cepat matriks modulo 10^9 + 7,
 * rekurens linear sebagai matriks transisi, suku konstan dan jumlah prefiks, serta banyak jalan di graph.
 * Semua kode C++ harus lolos C++14. JavaScript membaca bilangan sampai 10^18 sebagai BigInt dan memakai
 * perkalian modulo yang dipecah 16 bit agar tetap tepat dengan Number.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nconst long long MOD = 1e9 + 7;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini (cetak jawaban modulo MOD)\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : '')."const MOD = 1000000007;\n// a · b mod MOD tanpa melewati batas presisi Number (2^53)\nconst mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;\n\n// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\nMOD = 10**9 + 7\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$MOD = 1000000007;

/** C = A · B (mod 10^9 + 7) untuk matriks persegi. Untuk solusi referensi PHP. */
$matMul = function (array $A, array $B): array {
    $n = count($A);
    $C = [];
    for ($i = 0; $i < $n; $i++) {
        $Ci = array_fill(0, $n, 0);
        foreach ($A[$i] as $t => $a) {
            if ($a === 0) {
                continue;
            }
            $Bt = $B[$t];
            for ($j = 0; $j < $n; $j++) {
                $Ci[$j] += $a * $Bt[$j] % 1000000007;
            }
        }
        for ($j = 0; $j < $n; $j++) {
            $Ci[$j] %= 1000000007;
        }
        $C[] = $Ci;
    }

    return $C;
};

/** A^e (mod 10^9 + 7) dengan pangkat cepat. */
$matPow = function (array $A, int $e) use ($matMul): array {
    $n = count($A);
    $H = [];
    for ($i = 0; $i < $n; $i++) {
        $H[] = array_fill(0, $n, 0);
        $H[$i][$i] = 1;
    }
    while ($e > 0) {
        if ($e & 1) {
            $H = $matMul($H, $A);
        }
        $e >>= 1;
        if ($e > 0) {
            $A = $matMul($A, $A);
        }
    }

    return $H;
};

/** Vektor baris v dikali matriks A (mod 10^9 + 7). */
$vecMul = function (array $v, array $A): array {
    $n = count($v);
    $h = array_fill(0, $n, 0);
    for ($i = 0; $i < $n; $i++) {
        if ($v[$i] === 0) {
            continue;
        }
        $Ai = $A[$i];
        for ($j = 0; $j < $n; $j++) {
            $h[$j] += $v[$i] * $Ai[$j] % 1000000007;
        }
    }
    for ($j = 0; $j < $n; $j++) {
        $h[$j] %= 1000000007;
    }

    return $h;
};

/** Bilangan acak 1 .. 10^18 − 1 (mt_rand hanya sampai 2^31 − 1). */
$big = fn (): int => mt_rand(1, 999999999) * 1000000000 + mt_rand(0, 999999999);

/** Kode C++ perkalian dan pangkat cepat matriks yang dipakai bersama. */
$cppKali = <<<'CODE'
const long long MOD = 1e9 + 7;
typedef vector<vector<long long>> Matriks;

// C = A · B (mod MOD) untuk matriks persegi k x k: O(k^3)
Matriks kali(const Matriks& A, const Matriks& B) {
    int k = A.size();
    Matriks C(k, vector<long long>(k, 0));
    for (int i = 0; i < k; i++)
        for (int t = 0; t < k; t++) {
            if (A[i][t] == 0) continue;
            for (int j = 0; j < k; j++) C[i][j] = (C[i][j] + A[i][t] * B[t][j]) % MOD;
        }
    return C;
}
CODE;

$cppPangkat = <<<'CODE'
// A^e dengan pangkat cepat: O(k^3 log e)
Matriks pangkat(Matriks A, long long e) {
    int k = A.size();
    Matriks H(k, vector<long long>(k, 0));
    for (int i = 0; i < k; i++) H[i][i] = 1;    // matriks identitas
    while (e > 0) {
        if (e & 1) H = kali(H, A);              // bit e bernilai 1: kalikan A^(2^i) ke hasil
        A = kali(A, A);                          // A, A^2, A^4, A^8, ...
        e >>= 1;
    }
    return H;
}
CODE;

/** Kode JavaScript perkalian modulo dan matriks yang dipakai bersama. */
$jsKali = <<<'CODE'
const MOD = 1000000007;
// a · b mod MOD tanpa melewati batas presisi Number (2^53)
const mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;

// C = A · B (mod MOD). Setiap suku < MOD, jadi jumlah puluhan suku masih tepat sebagai Number.
const kaliMatriks = (A, B) => {
  const k = A.length;
  const C = [];
  for (let i = 0; i < k; i++) {
    const baris = new Array(k).fill(0);
    for (let t = 0; t < k; t++) {
      const a = A[i][t];
      if (a === 0) continue;
      const Bt = B[t];
      for (let j = 0; j < k; j++) baris[j] += mul(a, Bt[j]);
    }
    for (let j = 0; j < k; j++) baris[j] %= MOD;
    C.push(baris);
  }
  return C;
};
CODE;

$jsPangkat = <<<'CODE'
// A^e dengan pangkat cepat; e berupa BigInt
const pangkatMatriks = (A, e) => {
  const k = A.length;
  let H = Array.from({ length: k }, (_, i) => Array.from({ length: k }, (_, j) => (i === j ? 1 : 0)));
  while (e > 0n) {
    if (e & 1n) H = kaliMatriks(H, A);
    A = kaliMatriks(A, A);
    e >>= 1n;
  }
  return H;
};
CODE;

/** Kode Python perkalian dan pangkat cepat matriks yang dipakai bersama. */
$pyMat = <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7


def kali(A, B):
    # C = A · B (mod MOD); kolom-kolom B diambil sekali lewat zip(*B)
    kolom = list(zip(*B))
    return [[sum(x * y for x, y in zip(baris, kol)) % MOD for kol in kolom] for baris in A]


def pangkat(A, e):
    # A^e dengan pangkat cepat: O(k^3 log e)
    k = len(A)
    H = [[int(i == j) for j in range(k)] for i in range(k)]
    while e:
        if e & 1:
            H = kali(H, A)
        e >>= 1
        if e:
            A = kali(A, A)
    return H
CODE;

return [
    [
        'slug' => 'kelinci-fibonacci',
        'lesson' => 'matriks',
        'title' => 'Populasi Kelinci',
        'difficulty' => 'Mudah',
        'tags' => ['eksponensiasi matriks', 'fibonacci', 'pangkat cepat'],
        'statement' => '<p>Seorang peneliti mengamati kelinci di sebuah pulau terpencil. Pada bulan ke-0 pulau itu belum berpenghuni, lalu pada bulan ke-1 sepasang kelinci muda dilepas di sana. Setiap pasang kelinci yang sudah berumur dua bulan atau lebih melahirkan sepasang anak setiap bulan, dan tidak ada kelinci yang mati. Akibatnya, banyak pasang kelinci pada bulan ke-n mengikuti deret Fibonacci:</p>
<p style="text-align:center"><code>F(0) = 0, F(1) = 1, F(n) = F(n−1) + F(n−2) untuk n ≥ 2</code></p>
<p>Sang peneliti punya <strong>T</strong> pertanyaan. Setiap pertanyaan berisi satu bilangan n: berapa pasang kelinci pada bulan ke-n? Karena n bisa mencapai 10<sup>18</sup>, cetak jawabannya modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. T baris berikutnya masing-masing berisi satu bilangan <code>n</code>.</p>',
        'output_format' => '<p>T baris; baris ke-i berisi <code>F(n) mod 10<sup>9</sup> + 7</code> untuk pertanyaan ke-i.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 1 000</li><li>0 ≤ n ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "4\n0\n1\n10\n50\n", 'explanation' => 'Deretnya 0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, sehingga F(10) = 55. F(50) = 12 586 269 025 = 12 · (10<sup>9</sup> + 7) + 586 268 941, jadi jawabannya 586 268 941.'],
            ['input' => "1\n1000000000000000000\n", 'explanation' => 'Menghitung satu per satu sampai bulan ke-10<sup>18</sup> mustahil. Dengan pangkat cepat matriks cukup sekitar 60 kali pengkuadratan matriks 2 × 2.'],
        ],
        'tests' => function () use ($big) {
            $q = fn (array $ns) => count($ns)."\n".implode("\n", $ns)."\n";
            $acak = [];
            $campur = [];
            $penuh = [];
            for ($i = 0; $i < 1000; $i++) {
                $acak[] = $big();
                $campur[] = mt_rand(0, 3) === 0 ? mt_rand(0, 1000000) : $big();
                $penuh[] = (1 << 59) - 1 - mt_rand(0, 1000000);
            }

            return [
                $q([0]),
                $q([1, 2, 3, 4, 5]),
                $q(range(0, 90)),
                $q([1000000000000000000]),
                $q([2000000016, 2000000017, 4000000032, 1000000006, 1000000007, 1000000008]),
                $q([576460752303423487, 288230376151711744, 999999999999999999, 1000000000000000000]),
                $q($acak),
                $q($campur),
                $q($penuh),
                $q(array_fill(0, 1000, 1000000000000000000)),
            ];
        },
        'solve' => function (string $input) {
            $t = T::ints(str_replace("\n", ' ', $input));
            $out = [];
            for ($i = 1; $i <= $t[0]; $i++) {
                $n = $t[$i];
                // [[1, 1], [1, 0]]^n = [[F(n+1), F(n)], [F(n), F(n−1)]]
                [$ha, $hb, $hc, $hd] = [1, 0, 0, 1];
                [$qa, $qb, $qc, $qd] = [1, 1, 1, 0];
                while ($n > 0) {
                    if ($n & 1) {
                        [$ha, $hb, $hc, $hd] = [
                            ($ha * $qa + $hb * $qc) % 1000000007, ($ha * $qb + $hb * $qd) % 1000000007,
                            ($hc * $qa + $hd * $qc) % 1000000007, ($hc * $qb + $hd * $qd) % 1000000007,
                        ];
                    }
                    [$qa, $qb, $qc, $qd] = [
                        ($qa * $qa + $qb * $qc) % 1000000007, ($qa * $qb + $qb * $qd) % 1000000007,
                        ($qc * $qa + $qd * $qc) % 1000000007, ($qc * $qb + $qd * $qd) % 1000000007,
                    ];
                    $n >>= 1;
                }
                $out[] = $hb;
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int t;\n    cin >> t;\n    while (t--) {\n        long long n;   // n sampai 10^18 masih muat di long long\n        cin >> n;\n        // hitung F(n) mod MOD\n    }",
            "const t = Number(readLine());\nconst out = [];\nfor (let i = 0; i < t; i++) {\n  const n = BigInt(readLine().trim()); // n sampai 10^18: pakai BigInt\n  // out.push(...)\n}",
            "t = int(input())\nout = []\nfor _ in range(t):\n    n = int(input())\n    # out.append(...)"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

// Matriks 2 x 2 [[a, b], [c, d]]
struct M2 {
    long long a, b, c, d;
};

// Setiap elemen < MOD, jadi x·y + z·w < 2,1 · 10^18: masih muat di long long
M2 kali(const M2& X, const M2& Y) {
    return {(X.a * Y.a + X.b * Y.c) % MOD, (X.a * Y.b + X.b * Y.d) % MOD,
            (X.c * Y.a + X.d * Y.c) % MOD, (X.c * Y.b + X.d * Y.d) % MOD};
}

// Q = [[1, 1], [1, 0]] memenuhi Q^n = [[F(n+1), F(n)], [F(n), F(n-1)]]
long long fib(long long n) {
    M2 hasil = {1, 0, 0, 1};   // matriks identitas = Q^0
    M2 Q = {1, 1, 1, 0};
    while (n > 0) {
        if (n & 1) hasil = kali(hasil, Q);
        Q = kali(Q, Q);        // Q, Q^2, Q^4, Q^8, ...
        n >>= 1;
    }
    return hasil.b;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int t;
    cin >> t;
    string out;
    while (t--) {
        long long n;
        cin >> n;
        out += to_string(fib(n));
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
// a · b mod MOD tanpa melewati batas presisi Number (2^53)
const mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;

// Matriks 2 x 2 [[a, b], [c, d]] disimpan sebagai array [a, b, c, d]
const kali = (X, Y) => [
  (mul(X[0], Y[0]) + mul(X[1], Y[2])) % MOD,
  (mul(X[0], Y[1]) + mul(X[1], Y[3])) % MOD,
  (mul(X[2], Y[0]) + mul(X[3], Y[2])) % MOD,
  (mul(X[2], Y[1]) + mul(X[3], Y[3])) % MOD,
];

// Q = [[1, 1], [1, 0]] memenuhi Q^n = [[F(n+1), F(n)], [F(n), F(n-1)]]; n berupa BigInt
const fib = (n) => {
  let hasil = [1, 0, 0, 1]; // matriks identitas
  let Q = [1, 1, 1, 0];
  while (n > 0n) {
    if (n & 1n) hasil = kali(hasil, Q);
    Q = kali(Q, Q);
    n >>= 1n;
  }
  return hasil[1];
};

const t = Number(readLine());
const out = [];
for (let i = 0; i < t; i++) {
  // n sampai 10^18 melewati batas presisi Number: baca sebagai BigInt
  out.push(fib(BigInt(readLine().trim())));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7


def kali(X, Y):
    # matriks 2 x 2 [[a, b], [c, d]] disimpan sebagai tuple (a, b, c, d)
    a, b, c, d = X
    e, f, g, h = Y
    return ((a * e + b * g) % MOD, (a * f + b * h) % MOD,
            (c * e + d * g) % MOD, (c * f + d * h) % MOD)


def fib(n):
    # Q = [[1, 1], [1, 0]] memenuhi Q^n = [[F(n+1), F(n)], [F(n), F(n-1)]]
    hasil = (1, 0, 0, 1)
    Q = (1, 1, 1, 0)
    while n:
        if n & 1:
            hasil = kali(hasil, Q)
        Q = kali(Q, Q)
        n >>= 1
    return hasil[1]


t = int(input())
print("\n".join(str(fib(int(input()))) for _ in range(t)))
CODE,
        ],
        'editorial' => '<p>Perulangan biasa butuh n langkah, mustahil untuk n = 10<sup>18</sup>. Perhatikan bahwa satu langkah rekurens adalah transformasi linear dari pasangan dua suku terakhir:</p>
<p style="text-align:center"><code>(F(k+1), F(k)) = Q · (F(k), F(k−1)),  Q = [[1, 1], [1, 0]]</code></p>
<p>Menjalankan langkah itu n kali sama dengan mengalikan dengan <code>Q<sup>n</sup></code>, dan dengan induksi dapat ditunjukkan</p>
<p style="text-align:center"><code>Q<sup>n</sup> = [[F(n+1), F(n)], [F(n), F(n−1)]]</code></p>
<p>Jadi F(n) adalah elemen kanan atas Q<sup>n</sup>; untuk n = 0, Q<sup>0</sup> adalah matriks identitas yang memberi 0 dengan benar.</p>
<p>Q<sup>n</sup> dihitung dengan <strong>pangkat cepat</strong>, persis seperti memangkatkan bilangan: kuadratkan Q berulang kali (Q, Q<sup>2</sup>, Q<sup>4</sup>, …) dan kalikan ke hasil setiap kali bit n yang bersesuaian bernilai 1. Perkalian matriks bersifat asosiatif, jadi cara ini sah. Untuk n ≤ 10<sup>18</sup> hanya ada sekitar 60 bit, masing-masing paling banyak dua perkalian matriks 2 × 2. Total O(T log n).</p>
<p><strong>Jebakan:</strong> hasil kali dua elemen bisa mendekati 10<sup>18</sup>, jadi pakai <code>long long</code> dan ambil modulo setelah setiap penjumlahan dua hasil kali (sekitar 2 · 10<sup>18</sup> masih muat). Di JavaScript, n tidak bisa disimpan tepat sebagai Number (batasnya sekitar 9 · 10<sup>15</sup>), jadi baca sebagai BigInt; perkalian elemennya memakai helper <code>mul</code> 16 bit.</p>',
        'hints' => [
            'Perulangan n kali terlalu lambat. Bisakah satu langkah (F(k), F(k−1)) → (F(k+1), F(k)) ditulis sebagai perkalian dengan sebuah matriks tetap?',
            'Matriks Q = [[1, 1], [1, 0]] memenuhi Q^n = [[F(n+1), F(n)], [F(n), F(n−1)]].',
            'Hitung Q^n dengan pangkat cepat (kuadratkan Q, kalikan ke hasil saat bit n bernilai 1), semuanya modulo 10^9 + 7. Di JavaScript, baca n sebagai BigInt.',
        ],
    ],

    [
        'slug' => 'koloni-ganggang',
        'lesson' => 'matriks',
        'title' => 'Koloni Ganggang',
        'difficulty' => 'Sedang',
        'tags' => ['eksponensiasi matriks', 'rekurens linear', 'matriks transisi'],
        'statement' => '<p>Di laboratorium biologi, Dina membiakkan sejenis ganggang di dalam akuarium. Setiap sel ganggang yang lahir pada hari d akan membelah dan menghasilkan <code>c<sub>1</sub></code> sel baru pada hari d + 1, <code>c<sub>2</sub></code> sel baru pada hari d + 2, …, dan <code>c<sub>K</sub></code> sel baru pada hari d + K. Setelah itu sel tersebut tidak lagi menghasilkan sel baru.</p>
<p>Dina sudah mencatat banyak sel yang lahir pada hari 1, 2, …, K, yaitu f(1), f(2), …, f(K). Mulai hari K + 1, semua sel baru berasal dari pembelahan, sehingga</p>
<p style="text-align:center"><code>f(n) = c<sub>1</sub> · f(n−1) + c<sub>2</sub> · f(n−2) + … + c<sub>K</sub> · f(n−K)</code></p>
<p>Berapa banyak sel yang lahir pada hari ke-<strong>N</strong>? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>K N</code>. Baris kedua berisi <code>c<sub>1</sub> c<sub>2</sub> … c<sub>K</sub></code>. Baris ketiga berisi <code>f(1) f(2) … f(K)</code>.</p>',
        'output_format' => '<p><code>f(N) mod 10<sup>9</sup> + 7</code>.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ 10</li><li>1 ≤ N ≤ 10<sup>18</sup></li><li>0 ≤ c<sub>i</sub> ≤ 10<sup>9</sup></li><li>0 ≤ f(i) ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3 6\n2 0 1\n1 1 1\n", 'explanation' => 'f(4) = 2 · 1 + 0 · 1 + 1 · 1 = 3, f(5) = 2 · 3 + 0 · 1 + 1 · 1 = 7, dan f(6) = 2 · 7 + 0 · 3 + 1 · 1 = 15.'],
            ['input' => "1 1000000000000000000\n2\n1\n", 'explanation' => 'Setiap sel menghasilkan 2 sel baru keesokan harinya, jadi f(n) = 2<sup>n−1</sup>. Jawabannya 2<sup>10<sup>18</sup> − 1</sup> mod 10<sup>9</sup> + 7.'],
            ['input' => "4 2\n1 1 1 1\n5 8 13 21\n", 'explanation' => 'N ≤ K: f(2) = 8 sudah tercatat, tidak perlu dihitung.'],
        ],
        'tests' => function () use ($big) {
            $acak = fn (int $k, int $lo, int $hi) => implode(' ', array_map(fn () => mt_rand($lo, $hi), range(1, $k)));
            $mk = fn (int $k, int $n, string $c, string $f) => "$k $n\n$c\n$f\n";
            $G = 1000000000;

            return [
                $mk(1, 1, '5', '7'),
                $mk(2, 50, '1 1', '1 1'),
                $mk(10, 5, $acak(10, 0, $G), $acak(10, 0, $G)),
                $mk(10, 11, $acak(10, 0, 9), $acak(10, 0, 9)),
                $mk(5, 1000000000000000000, '0 0 0 0 1', '3 1 4 1 5'),
                $mk(3, 1000000000000000000, '1 1 1', '0 0 1'),
                $mk(10, 1000000000000000000, implode(' ', array_fill(0, 10, $G)), implode(' ', array_fill(0, 10, $G))),
                $mk(10, $big(), $acak(10, 0, $G), $acak(10, 0, $G)),
                $mk(7, $big(), $acak(7, 0, 3), $acak(7, 0, 100)),
                $mk(4, 999999999999999999, '0 0 0 0', '1 2 3 4'),
                $mk(1, 999999999999999999, '1000000000', '999999999'),
                $mk(6, (1 << 59) - 1, $acak(6, 0, $G), $acak(6, 0, $G)),
                $mk(8, mt_rand(100, 1000), $acak(8, 0, $G), $acak(8, 0, $G)),
            ];
        },
        'solve' => function (string $input) use ($matPow) {
            $lines = T::lines($input);
            [$k, $n] = T::ints($lines[0]);
            $c = T::ints($lines[1]);
            $f = array_merge([0], T::ints($lines[2]));
            if ($n <= $k) {
                return (string) ($f[$n] % 1000000007);
            }
            $M = [];
            for ($i = 0; $i < $k; $i++) {
                $M[] = array_fill(0, $k, 0);
            }
            for ($j = 0; $j < $k; $j++) {
                $M[0][$j] = $c[$j] % 1000000007;
            }
            for ($i = 1; $i < $k; $i++) {
                $M[$i][$i - 1] = 1;
            }
            $P = $matPow($M, $n - $k);
            $ans = 0;
            for ($j = 0; $j < $k; $j++) {
                $ans = ($ans + $P[0][$j] * ($f[$k - $j] % 1000000007)) % 1000000007;
            }

            return (string) $ans;
        },
        'starter' => $st(
            "    int k;\n    long long n;   // N sampai 10^18 masih muat di long long\n    cin >> k >> n;\n    vector<long long> c(k + 1), f(k + 1);\n    for (int i = 1; i <= k; i++) cin >> c[i];\n    for (int i = 1; i <= k; i++) cin >> f[i];",
            "const baris = readLine().trim().split(/\\s+/);\nconst k = Number(baris[0]);\nconst n = BigInt(baris[1]); // N sampai 10^18: pakai BigInt\nconst c = [0, ...readInts()];\nconst f = [0, ...readInts()];",
            "k, n = map(int, input().split())\nc = [0] + list(map(int, input().split()))\nf = [0] + list(map(int, input().split()))"
        ),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppKali."\n\n".$cppPangkat.<<<'CODE'


int main() {
    int k;
    long long n;
    cin >> k >> n;
    vector<long long> c(k + 1), f(k + 1);
    for (int i = 1; i <= k; i++) cin >> c[i];
    for (int i = 1; i <= k; i++) cin >> f[i];
    if (n <= k) {                       // sudah tercatat
        cout << f[n] % MOD << '\n';
        return 0;
    }
    // Vektor keadaan v(m) = (f(m), f(m-1), ..., f(m-k+1)); satu hari = kalikan dengan M
    Matriks M(k, vector<long long>(k, 0));
    for (int j = 0; j < k; j++) M[0][j] = c[j + 1] % MOD;  // f(m+1) = c1 f(m) + ... + ck f(m-k+1)
    for (int i = 1; i < k; i++) M[i][i - 1] = 1;            // suku lain bergeser satu posisi
    // v(n) = M^(n-k) · v(k), dengan v(k) = (f(k), f(k-1), ..., f(1))
    Matriks P = pangkat(M, n - k);
    long long jawab = 0;
    for (int j = 0; j < k; j++) jawab = (jawab + P[0][j] * (f[k - j] % MOD)) % MOD;
    cout << jawab << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const baris = readLine().trim().split(/\s+/);
const k = Number(baris[0]);
const n = BigInt(baris[1]); // N sampai 10^18 melewati batas presisi Number: pakai BigInt
const c = [0, ...readInts()];
const f = [0, ...readInts()];

CODE.$jsKali."\n\n".$jsPangkat.<<<'CODE'


if (n <= BigInt(k)) {
  console.log(f[Number(n)] % MOD); // sudah tercatat
} else {
  // Vektor keadaan v(m) = (f(m), f(m-1), ..., f(m-k+1)); satu hari = kalikan dengan M
  const M = Array.from({ length: k }, () => new Array(k).fill(0));
  for (let j = 0; j < k; j++) M[0][j] = c[j + 1] % MOD;
  for (let i = 1; i < k; i++) M[i][i - 1] = 1;
  const P = pangkatMatriks(M, n - BigInt(k));
  let jawab = 0;
  for (let j = 0; j < k; j++) jawab = (jawab + mul(P[0][j], f[k - j] % MOD)) % MOD;
  console.log(jawab);
}
CODE,
            'python' => $pyMat.<<<'CODE'



k, n = map(int, input().split())
c = [0] + list(map(int, input().split()))
f = [0] + list(map(int, input().split()))

if n <= k:
    print(f[n] % MOD)  # sudah tercatat
else:
    # Vektor keadaan v(m) = (f(m), f(m-1), ..., f(m-k+1)); satu hari = kalikan dengan M
    M = [[0] * k for _ in range(k)]
    M[0] = [x % MOD for x in c[1:]]
    for i in range(1, k):
        M[i][i - 1] = 1
    P = pangkat(M, n - k)
    # v(n) = M^(n-k) · v(k), dengan v(k) = (f(k), ..., f(1)); ambil elemen pertamanya
    print(sum(P[0][j] * f[k - j] for j in range(k)) % MOD)
CODE,
        ],
        'editorial' => '<p>Perulangan langsung butuh O(N · K) langkah, terlalu lambat untuk N = 10<sup>18</sup>. Kuncinya: simpan K suku terakhir sebagai satu <strong>vektor keadaan</strong></p>
<p style="text-align:center"><code>v(m) = (f(m), f(m−1), …, f(m−K+1))</code></p>
<p>Keadaan hari berikutnya v(m + 1) adalah kombinasi linear dari elemen v(m):</p>
<ul><li>elemen pertama yang baru adalah f(m + 1) = c<sub>1</sub>f(m) + … + c<sub>K</sub>f(m−K+1), jadi baris pertama matriks berisi c<sub>1</sub>, …, c<sub>K</sub>;</li>
<li>elemen lainnya hanya bergeser satu posisi (f(m) pindah ke posisi kedua, dan seterusnya), jadi baris ke-i (i ≥ 2) berisi angka 1 di kolom i − 1.</li></ul>
<p>Untuk K = 3, matriks transisinya</p>
<p style="text-align:center"><code>M = [[c<sub>1</sub>, c<sub>2</sub>, c<sub>3</sub>], [1, 0, 0], [0, 1, 0]]</code></p>
<p>sehingga <code>v(m+1) = M · v(m)</code> dan <code>v(N) = M<sup>N−K</sup> · v(K)</code>. Jawabannya elemen pertama v(N), yaitu baris pertama M<sup>N−K</sup> dikali vektor (f(K), f(K−1), …, f(1)). Pada contoh pertama, M · (1, 1, 1) = (3, 1, 1), lalu (7, 3, 1), lalu (15, 7, 3).</p>
<p>Pangkat cepat matriks butuh O(K<sup>3</sup> log N), sekitar 10<sup>3</sup> · 60 · 2 perkalian.</p>
<p><strong>Jebakan:</strong> jika N ≤ K jawabannya langsung f(N); jangan memangkatkan dengan eksponen negatif. Urutan elemen vektor harus cocok dengan baris pertama matriks: c<sub>1</sub> berpasangan dengan suku terbaru f(m), bukan dengan f(m−K+1). Hasil kali dua elemen bisa mendekati 10<sup>18</sup>, jadi modulo-kan setiap penjumlahan.</p>',
        'hints' => [
            'Simpan K suku terakhir sebagai satu vektor (f(m), f(m−1), …, f(m−K+1)). Bagaimana vektor itu berubah dari satu hari ke hari berikutnya?',
            'Baris pertama matriks transisi berisi c1, …, cK; baris lainnya hanya menggeser (angka 1 tepat di bawah diagonal).',
            'v(N) = M^(N−K) · v(K). Hitung pangkatnya dengan pangkat cepat matriks; jika N ≤ K, cetak f(N) langsung.',
        ],
    ],

    [
        'slug' => 'koin-level-tak-berujung',
        'lesson' => 'matriks',
        'title' => 'Koin Level Tak Berujung',
        'difficulty' => 'Sedang',
        'tags' => ['eksponensiasi matriks', 'suku konstan', 'jumlah prefiks'],
        'statement' => '<p>Sebuah gim petualangan punya level yang tidak pernah habis. Level 1 berisi <strong>X</strong> koin dan level 2 berisi <strong>Y</strong> koin. Mulai level 3, banyak koin di level n ditentukan oleh rumus pembuat gim:</p>
<p style="text-align:center"><code>f(n) = A · f(n−1) + B · f(n−2) + C</code></p>
<p>dengan C adalah bonus koin tetap yang ditambahkan di setiap level. Rara ingin tahu total koin yang ia kumpulkan jika menamatkan level 1 sampai <strong>N</strong>, yaitu <code>S(N) = f(1) + f(2) + … + f(N)</code>. Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>X Y</code>. Baris ketiga berisi <code>A B C</code>.</p>',
        'output_format' => '<p><code>S(N) mod 10<sup>9</sup> + 7</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>18</sup></li><li>0 ≤ X, Y, A, B, C ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1 2\n1 1 1\n", 'explanation' => 'f(3) = 2 + 1 + 1 = 4, f(4) = 4 + 2 + 1 = 7, f(5) = 7 + 4 + 1 = 12. Totalnya 1 + 2 + 4 + 7 + 12 = 26.'],
            ['input' => "4\n3 5\n0 2 10\n", 'explanation' => 'f(3) = 0 · 5 + 2 · 3 + 10 = 16 dan f(4) = 0 · 16 + 2 · 5 + 10 = 20. Totalnya 3 + 5 + 16 + 20 = 44.'],
        ],
        'tests' => function () use ($big) {
            $r = fn () => mt_rand(0, 1000000000);
            $mk = fn (int $n, array $xy, array $abc) => "$n\n".implode(' ', $xy)."\n".implode(' ', $abc)."\n";
            $G = 1000000000;
            $E = 1000000000000000000;

            return [
                $mk(1, [7, 9], [5, 5, 5]),
                $mk(2, [$G, $G], [1, 1, 1]),
                $mk(3, [4, 6], [2, 3, 1]),
                $mk(10, [0, 0], [1, 1, 0]),
                $mk(mt_rand(50, 1000), [$r(), $r()], [$r(), $r(), $r()]),
                $mk($E, [0, 0], [0, 0, 1]),
                $mk($E, [1, 1], [1, 1, 0]),
                $mk($E, [$G, $G], [$G, $G, $G]),
                $mk($big(), [$r(), $r()], [$r(), $r(), $r()]),
                $mk($big(), [$r(), $r()], [1, 0, $r()]),
                $mk($big(), [mt_rand(0, 9), mt_rand(0, 9)], [0, 1, mt_rand(0, 9)]),
                $mk((1 << 59) - 1, [$r(), $r()], [$r(), $r(), $r()]),
            ];
        },
        'solve' => function (string $input) use ($matPow) {
            $t = T::ints(str_replace("\n", ' ', $input));
            [$n, $x, $y, $a, $b, $c] = $t;
            if ($n === 1) {
                return (string) ($x % 1000000007);
            }
            $M = [[$a, $b, 0, $c], [1, 0, 0, 0], [$a, $b, 1, $c], [0, 0, 0, 1]];
            $v = [$y, $x, ($x + $y) % 1000000007, 1];
            $P = $matPow($M, $n - 2);
            $ans = 0;
            for ($j = 0; $j < 4; $j++) {
                $ans = ($ans + $P[2][$j] * $v[$j]) % 1000000007;
            }

            return (string) $ans;
        },
        'starter' => $st(
            "    long long n, x, y, a, b, c;   // N sampai 10^18 masih muat di long long\n    cin >> n >> x >> y >> a >> b >> c;",
            "const n = BigInt(readLine().trim()); // N sampai 10^18: pakai BigInt\nconst [x, y] = readInts();\nconst [a, b, c] = readInts();",
            "n = int(input())\nx, y = map(int, input().split())\na, b, c = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppKali."\n\n".$cppPangkat.<<<'CODE'


int main() {
    long long n, x, y, a, b, c;
    cin >> n >> x >> y >> a >> b >> c;
    if (n == 1) {
        cout << x % MOD << '\n';
        return 0;
    }
    // Vektor keadaan v(m) = (f(m), f(m-1), S(m), 1), dengan S(m) = f(1) + ... + f(m)
    Matriks M = {
        {a, b, 0, c},   // f(m+1) = A f(m) + B f(m-1) + C · 1
        {1, 0, 0, 0},   // f(m) bergeser ke posisi kedua
        {a, b, 1, c},   // S(m+1) = S(m) + f(m+1)
        {0, 0, 0, 1},   // angka 1 tetap 1
    };
    vector<long long> v = {y, x, (x + y) % MOD, 1};   // v(2)
    Matriks P = pangkat(M, n - 2);                     // v(n) = M^(n-2) · v(2)
    long long jawab = 0;
    for (int j = 0; j < 4; j++) jawab = (jawab + P[2][j] * v[j]) % MOD;
    cout << jawab << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = BigInt(readLine().trim()); // N sampai 10^18 melewati batas presisi Number: pakai BigInt
const [x, y] = readInts();
const [a, b, c] = readInts();

CODE.$jsKali."\n\n".$jsPangkat.<<<'CODE'


if (n === 1n) {
  console.log(x % MOD);
} else {
  // Vektor keadaan v(m) = (f(m), f(m-1), S(m), 1), dengan S(m) = f(1) + ... + f(m)
  const M = [
    [a, b, 0, c], // f(m+1) = A f(m) + B f(m-1) + C · 1
    [1, 0, 0, 0], // f(m) bergeser ke posisi kedua
    [a, b, 1, c], // S(m+1) = S(m) + f(m+1)
    [0, 0, 0, 1], // angka 1 tetap 1
  ];
  const v = [y, x, (x + y) % MOD, 1]; // v(2)
  const P = pangkatMatriks(M, n - 2n); // v(n) = M^(n-2) · v(2)
  let jawab = 0;
  for (let j = 0; j < 4; j++) jawab = (jawab + mul(P[2][j], v[j])) % MOD;
  console.log(jawab);
}
CODE,
            'python' => $pyMat.<<<'CODE'



n = int(input())
x, y = map(int, input().split())
a, b, c = map(int, input().split())

if n == 1:
    print(x % MOD)
else:
    # Vektor keadaan v(m) = (f(m), f(m-1), S(m), 1), dengan S(m) = f(1) + ... + f(m)
    M = [[a, b, 0, c],   # f(m+1) = A f(m) + B f(m-1) + C · 1
         [1, 0, 0, 0],   # f(m) bergeser ke posisi kedua
         [a, b, 1, c],   # S(m+1) = S(m) + f(m+1)
         [0, 0, 0, 1]]   # angka 1 tetap 1
    v = [y, x, x + y, 1]  # v(2)
    P = pangkat(M, n - 2)  # v(n) = M^(n-2) · v(2)
    print(sum(P[2][j] * v[j] for j in range(4)) % MOD)
CODE,
        ],
        'editorial' => '<p>Dua hal membuat soal ini belum langsung berbentuk "vektor dikali matriks": ada suku konstan C, dan yang ditanya adalah jumlah S(N), bukan f(N). Keduanya diatasi dengan menambah elemen ke vektor keadaan.</p>
<ul><li><strong>Suku konstan:</strong> masukkan angka 1 ke vektor. Elemen ini tidak pernah berubah (barisnya di matriks hanya berisi 1 di diagonal), dan C cukup ditulis di kolom milik angka 1 pada baris f.</li>
<li><strong>Jumlah prefiks:</strong> masukkan S(m) ke vektor. Karena <code>S(m+1) = S(m) + f(m+1) = S(m) + A · f(m) + B · f(m−1) + C</code>, baris S sama dengan baris f ditambah angka 1 di kolom S.</li></ul>
<p>Dengan vektor <code>v(m) = (f(m), f(m−1), S(m), 1)</code>:</p>
<p style="text-align:center"><code>M = [[A, B, 0, C], [1, 0, 0, 0], [A, B, 1, C], [0, 0, 0, 1]],  v(m+1) = M · v(m)</code></p>
<p>Mulai dari <code>v(2) = (Y, X, X + Y, 1)</code>, maka <code>v(N) = M<sup>N−2</sup> · v(2)</code> dan jawabannya elemen ketiga. Untuk N = 1 jawabannya X. Pada contoh pertama: v(2) = (2, 1, 3, 1), v(3) = (4, 2, 7, 1), v(4) = (7, 4, 14, 1), v(5) = (12, 7, 26, 1).</p>
<p>Pangkat cepat matriks 4 × 4 butuh O(4<sup>3</sup> log N), sangat ringan.</p>
<p><strong>Jebakan:</strong> C harus muncul di baris S juga, bukan hanya di baris f, karena f(m+1) yang ditambahkan ke S sudah mengandung C. Jangan lupa kasus N = 1 (eksponen N − 2 negatif). X + Y bisa melebihi 10<sup>9</sup> + 7, jadi modulo-kan sebelum dikalikan.</p>',
        'hints' => [
            'Konstanta C bisa "dibawa" di dalam vektor keadaan sebagai elemen bernilai 1 yang tidak pernah berubah.',
            'Tambahkan juga S(m) ke vektor: S(m+1) = S(m) + f(m+1), dan f(m+1) bisa ditulis dari elemen-elemen vektor saat ini.',
            'Pakai vektor (f(m), f(m−1), S(m), 1) dan matriks 4 × 4; mulai dari m = 2 lalu pangkatkan N − 2. Tangani N = 1 secara terpisah.',
        ],
    ],

    [
        'slug' => 'tiket-terusan-kereta',
        'lesson' => 'matriks',
        'title' => 'Tiket Terusan Kereta',
        'difficulty' => 'Sulit',
        'tags' => ['eksponensiasi matriks', 'graph', 'matriks ketetanggaan', 'menghitung jalan'],
        'statement' => '<p>Negeri Rel punya <strong>N</strong> stasiun bernomor 1 sampai N dan <strong>M</strong> jalur kereta satu arah. Jalur ke-i berangkat dari stasiun u<sub>i</sub> menuju stasiun v<sub>i</sub>. Bisa ada beberapa jalur dengan stasiun asal dan tujuan yang sama (misalnya kereta ekspres dan kereta lokal); jalur-jalur itu dianggap pilihan yang berbeda.</p>
<p>Tono membeli tiket terusan yang berlaku untuk <strong>paling banyak K</strong> kali naik kereta. Ia mulai di stasiun 1 dan ingin mengakhiri perjalanannya di stasiun N. Sebuah <em>rencana perjalanan</em> adalah urutan jalur yang ia naiki (minimal satu jalur): setiap jalur harus berangkat dari stasiun tempat jalur sebelumnya tiba, dan jalur terakhir harus tiba di stasiun N. Stasiun dan jalur yang sama boleh dilalui berkali-kali, termasuk melewati stasiun N di tengah perjalanan.</p>
<p>Ada berapa rencana perjalanan berbeda? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M K</code>. M baris berikutnya masing-masing berisi <code>u<sub>i</sub> v<sub>i</sub></code>.</p>',
        'output_format' => '<p>Banyak rencana perjalanan, modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 50</li><li>1 ≤ M ≤ 2 500</li><li>1 ≤ u<sub>i</sub>, v<sub>i</sub> ≤ N, u<sub>i</sub> ≠ v<sub>i</sub></li><li>1 ≤ K ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "3 4 3\n1 2\n2 3\n1 3\n3 1\n", 'explanation' => 'Rencananya: 1 → 3, 1 → 2 → 3, dan 1 → 3 → 1 → 3. Rencana 1 → 2 → 3 → 1 → 3 butuh 4 kali naik kereta, melebihi K = 3.'],
            ['input' => "2 3 3\n1 2\n1 2\n2 1\n", 'explanation' => 'Sebut dua jalur dari 1 ke 2 sebagai a dan b, dan jalur dari 2 ke 1 sebagai c. Rencana sepanjang 1: a, b. Sepanjang 2 tidak ada (berakhir di stasiun 1). Sepanjang 3: a-c-a, a-c-b, b-c-a, b-c-b. Totalnya 6.'],
        ],
        'tests' => function () use ($big) {
            $acak = function (int $n, int $m) {
                $e = [];
                for ($i = 0; $i < $m; $i++) {
                    $u = mt_rand(1, $n);
                    $v = mt_rand(1, $n - 1);
                    if ($v >= $u) {
                        $v++;
                    }
                    $e[] = [$u, $v];
                }

                return $e;
            };
            $mk = fn (int $n, int $k, array $e) => T::graphInput("$n ".count($e)." $k", $e);
            $lengkap = [];
            for ($u = 1; $u <= 50; $u++) {
                for ($v = 1; $v <= 50; $v++) {
                    if ($u !== $v) {
                        $lengkap[] = [$u, $v];
                    }
                }
            }
            T::shuffle($lengkap);
            $siklus = [];
            for ($u = 1; $u <= 50; $u++) {
                $siklus[] = [$u, $u % 50 + 1];
            }
            T::shuffle($siklus);
            $dag = array_map(fn ($e) => $e[0] < $e[1] ? $e : [$e[1], $e[0]], T::edges(50, 600));
            $E = 1000000000000000000;

            return [
                $mk(2, 1, [[1, 2]]),
                $mk(2, $E, [[1, 2]]),
                $mk(2, $E, [[1, 2], [2, 1]]),
                $mk(3, 5, [[2, 3]]),
                $mk(5, 20, $acak(5, 10)),
                $mk(6, mt_rand(30, 60), $acak(6, 14)),
                $mk(50, $E, $siklus),
                $mk(50, $E, $lengkap),
                $mk(50, $E, $acak(50, 2500)),
                $mk(50, $big(), $acak(50, 120)),
                $mk(50, $E, $dag),
                $mk(40, 123456789012345678, $acak(40, 2000)),
                $mk(50, (1 << 59) - 1, $acak(50, 2500)),
            ];
        },
        'solve' => function (string $input) use ($matMul, $vecMul) {
            $lines = T::lines($input);
            [$n, $m, $k] = T::ints($lines[0]);
            $A = [];
            for ($i = 0; $i <= $n; $i++) {
                $A[] = array_fill(0, $n + 1, 0);
            }
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $A[$u - 1][$v - 1]++;
            }
            $A[$n - 1][$n] = 1;
            $A[$n][$n] = 1;
            $vec = array_fill(0, $n + 1, 0);
            $vec[0] = 1;
            $e = $k + 1;
            while ($e > 0) {
                if ($e & 1) {
                    $vec = $vecMul($vec, $A);
                }
                $e >>= 1;
                if ($e > 0) {
                    $A = $matMul($A, $A);
                }
            }

            return (string) $vec[$n];
        },
        'starter' => $st(
            "    int n, m;\n    long long k;   // K sampai 10^18 masih muat di long long\n    cin >> n >> m >> k;\n    vector<vector<long long>> A(n, vector<long long>(n, 0));   // A[u][v] = banyak jalur u -> v\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        A[u - 1][v - 1]++;\n    }",
            "const baris = readLine().trim().split(/\\s+/);\nconst n = Number(baris[0]), m = Number(baris[1]);\nconst k = BigInt(baris[2]); // K sampai 10^18: pakai BigInt\nconst A = Array.from({ length: n }, () => new Array(n).fill(0)); // A[u][v] = banyak jalur u -> v\nfor (let i = 0; i < m; i++) {\n  const [u, v] = readInts();\n  A[u - 1][v - 1]++;\n}",
            "n, m, k = map(int, input().split())\nA = [[0] * n for _ in range(n)]  # A[u][v] = banyak jalur u -> v\nfor _ in range(m):\n    u, v = map(int, input().split())\n    A[u - 1][v - 1] += 1"
        ),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppKali.<<<'CODE'


// Vektor baris v dikali matriks A: hasil[j] = Σ v[i] · A[i][j], cukup O(k^2)
vector<long long> kaliVektor(const vector<long long>& v, const Matriks& A) {
    int k = v.size();
    vector<long long> hasil(k, 0);
    for (int i = 0; i < k; i++) {
        if (v[i] == 0) continue;
        for (int j = 0; j < k; j++) hasil[j] = (hasil[j] + v[i] * A[i][j]) % MOD;
    }
    return hasil;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    long long k;
    cin >> n >> m >> k;
    // Simpul 0..n-1 = stasiun 1..n, simpul n = "selesai" (penampung)
    Matriks A(n + 1, vector<long long>(n + 1, 0));
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        A[u - 1][v - 1]++;              // jalur paralel menambah banyak pilihan
    }
    A[n - 1][n] = 1;                    // dari stasiun N boleh "turun" ke simpul selesai
    A[n][n] = 1;                        // simpul selesai berdiam di tempat
    // Rencana sepanjang t <= K yang berakhir di N  <->  jalan sepanjang tepat K+1 dari 1 ke simpul selesai.
    // Hanya baris stasiun 1 dari A^(K+1) yang dibutuhkan: kalikan vektor, kuadratkan matriks.
    vector<long long> v(n + 1, 0);
    v[0] = 1;
    long long e = k + 1;
    while (e > 0) {
        if (e & 1) v = kaliVektor(v, A);
        e >>= 1;
        if (e > 0) A = kali(A, A);       // A, A^2, A^4, ...
    }
    cout << v[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const baris = readLine().trim().split(/\s+/);
const n = Number(baris[0]), m = Number(baris[1]);
const k = BigInt(baris[2]); // K sampai 10^18 melewati batas presisi Number: pakai BigInt

CODE.$jsKali.<<<'CODE'


// Vektor baris v dikali matriks A, cukup O(k^2)
const kaliVektor = (v, A) => {
  const k = v.length;
  const hasil = new Array(k).fill(0);
  for (let i = 0; i < k; i++) {
    if (v[i] === 0) continue;
    for (let j = 0; j < k; j++) hasil[j] += mul(v[i], A[i][j]);
  }
  return hasil.map((x) => x % MOD);
};

// Simpul 0..n-1 = stasiun 1..n, simpul n = "selesai" (penampung)
let A = Array.from({ length: n + 1 }, () => new Array(n + 1).fill(0));
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  A[u - 1][v - 1]++; // jalur paralel menambah banyak pilihan
}
A[n - 1][n] = 1; // dari stasiun N boleh "turun" ke simpul selesai
A[n][n] = 1; // simpul selesai berdiam di tempat

// Jawaban = elemen (stasiun 1, selesai) dari A^(K+1); cukup lacak satu baris sebagai vektor
let v = new Array(n + 1).fill(0);
v[0] = 1;
let e = k + 1n;
while (e > 0n) {
  if (e & 1n) v = kaliVektor(v, A);
  e >>= 1n;
  if (e > 0n) A = kaliMatriks(A, A);
}
console.log(v[n]);
CODE,
            'python' => <<<'CODE'
import sys
from operator import mul
input = sys.stdin.readline

MOD = 10**9 + 7


def kali(A, B):
    # sum(map(mul, baris, kolom)) menjalankan perkalian di dalam kode C bawaan Python,
    # jauh lebih cepat daripada tiga perulangan for. Jumlah 51 hasil kali tetap tepat di int Python.
    kolom = list(zip(*B))
    return [[sum(map(mul, baris, kol)) % MOD for kol in kolom] for baris in A]


def kali_vektor(v, A):
    # vektor baris v dikali matriks A, cukup O(k^2)
    return [sum(map(mul, v, kol)) % MOD for kol in zip(*A)]


n, m, k = map(int, input().split())
# Simpul 0..n-1 = stasiun 1..n, simpul n = "selesai" (penampung)
A = [[0] * (n + 1) for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    A[u - 1][v - 1] += 1  # jalur paralel menambah banyak pilihan
A[n - 1][n] = 1  # dari stasiun N boleh "turun" ke simpul selesai
A[n][n] = 1      # simpul selesai berdiam di tempat

# Jawaban = elemen (stasiun 1, selesai) dari A^(K+1); cukup lacak satu baris sebagai vektor
vek = [0] * (n + 1)
vek[0] = 1
e = k + 1
while e:
    if e & 1:
        vek = kali_vektor(vek, A)
    e >>= 1
    if e:
        A = kali(A, A)
print(vek[n])
CODE,
        ],
        'editorial' => '<p><strong>Langkah 1: panjang tepat t.</strong> Misalkan A matriks ketetanggaan dengan <code>A[u][v]</code> = banyak jalur dari u ke v (jalur paralel dijumlahkan). Banyak rencana dengan tepat t kali naik dari u ke v adalah <code>A<sup>t</sup>[u][v]</code>. Buktinya dengan induksi: rencana sepanjang t + 1 dari u ke v adalah rencana sepanjang t dari u ke suatu stasiun w, disambung satu jalur w → v, sehingga <code>A<sup>t+1</sup>[u][v] = Σ<sub>w</sub> A<sup>t</sup>[u][w] · A[w][v]</code>, persis definisi perkalian matriks.</p>
<p><strong>Langkah 2: paling banyak K.</strong> Yang ditanya adalah <code>A<sup>1</sup>[1][N] + A<sup>2</sup>[1][N] + … + A<sup>K</sup>[1][N]</code>, dan menghitung K suku jelas mustahil. Gunakan trik yang mirip dengan menambah baris/kolom 1 pada rekurens bersuku konstan: tambahkan simpul baru <strong>Z</strong> ("selesai") dengan sisi N → Z dan sisi Z → Z. Setiap jalan sepanjang tepat K + 1 dari stasiun 1 ke Z berbentuk: rencana sepanjang t yang berakhir di N, satu langkah N → Z, lalu K − t kali berdiam di Z. Ini korespondensi satu-satu untuk setiap t = 0..K (t = 0 tidak menyumbang karena stasiun 1 ≠ N). Jadi</p>
<p style="text-align:center"><code>jawaban = A\'<sup>K+1</sup>[1][Z]</code></p>
<p>dengan A\' matriks berukuran (N + 1) × (N + 1). Pada contoh pertama, jalan sepanjang 4 dari 1 ke Z adalah 1 → 3 → Z → Z → Z, 1 → 2 → 3 → Z → Z, dan 1 → 3 → 1 → 3 → Z.</p>
<p><strong>Langkah 3: cukup satu baris.</strong> Kita hanya butuh baris stasiun 1 dari A\'<sup>K+1</sup>. Simpan baris itu sebagai vektor v (awalnya 1 di posisi stasiun 1). Saat pangkat cepat, setiap bit K + 1 yang bernilai 1 cukup mengalikan <em>vektor</em> dengan A\'<sup>2<sup>i</sup></sup> dalam O(N<sup>2</sup>), dan hanya pengkuadratan matriks yang butuh O(N<sup>3</sup>). Totalnya sekitar 60 pengkuadratan matriks 51 × 51, kira-kira 8 · 10<sup>6</sup> perkalian modulo, separuh dari pangkat matriks biasa. Penghematan ini terasa di JavaScript dan Python; di Python, hitung baris × kolom dengan <code>sum(map(mul, baris, kolom))</code> agar perkaliannya berjalan di kode C bawaan.</p>
<p><strong>Jebakan:</strong> jalur paralel harus <em>ditambahkan</em> ke A, bukan ditimpa dengan 1. K + 1 bisa sedikit melewati 10<sup>18</sup> tetapi masih muat di <code>long long</code>; di JavaScript, baca K sebagai BigInt dan pakai perkalian modulo 16 bit. Cara lain untuk jumlah pangkat adalah matriks blok <code>[[A, I], [0, I]]</code> berukuran 2N, tetapi itu 8 kali lebih lambat.</p>',
        'hints' => [
            'Banyak jalan sepanjang tepat t dari u ke v adalah elemen (u, v) dari A^t, dengan A matriks ketetanggaan (jalur paralel dijumlahkan).',
            'Untuk "paling banyak K", tambahkan simpul baru Z dengan sisi N → Z dan Z → Z. Jalan sepanjang tepat K + 1 dari 1 ke Z berpadanan satu-satu dengan rencana sepanjang ≤ K yang berakhir di N.',
            'Cukup lacak baris stasiun 1 sebagai vektor: kalikan vektor dengan A^(2^i) untuk setiap bit K + 1 yang bernilai 1, sambil mengkuadratkan matriks (N + 1) × (N + 1). Baca K sebagai BigInt di JavaScript.',
        ],
    ],
];
