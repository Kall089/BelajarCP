<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Bilangan Prima & Saringan:
 * uji prima, saringan Eratosthenes, saringan faktor prima terkecil (SPF), banyak pembagi, fungsi phi Euler.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Daftar bilangan prima ≤ $n (saringan Eratosthenes pada string), disimpan agar tidak dihitung ulang. */
$primesUpTo = function (int $n): array {
    static $cache = [];
    if (! isset($cache[$n])) {
        $s = str_repeat("\1", $n + 1);
        $s[0] = "\0";
        if ($n >= 1) {
            $s[1] = "\0";
        }
        for ($i = 2; $i * $i <= $n; $i++) {
            if ($s[$i] === "\1") {
                for ($j = $i * $i; $j <= $n; $j += $i) {
                    $s[$j] = "\0";
                }
            }
        }
        $pr = [];
        for ($pos = strpos($s, "\1"); $pos !== false; $pos = strpos($s, "\1", $pos + 1)) {
            $pr[] = $pos;
        }
        $cache[$n] = $pr;
    }

    return $cache[$n];
};

/** Uji prima untuk n ≤ 10^12: coba bagi dengan prima p selama p · p ≤ n. */
$isPrime = function (int $n) use ($primesUpTo): bool {
    if ($n < 2) {
        return false;
    }
    foreach ($primesUpTo(1000000) as $p) {
        if ($p * $p > $n) {
            return true;
        }
        if ($n % $p === 0) {
            return false;
        }
    }

    return true;
};

/** $n bilangan dari pembangkit $gen. */
$many = function (int $n, callable $gen): array {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = $gen();
    }

    return $a;
};

return [
    [
        'slug' => 'nomor-prima',
        'lesson' => 'bilangan-prima',
        'title' => 'Kupon Undian Prima',
        'difficulty' => 'Mudah',
        'tags' => ['bilangan prima', 'uji prima', 'saringan eratosthenes'],
        'statement' => '<p>Panitia bazar sekolah membagikan kupon undian bernomor. Hadiah utama hanya diberikan kepada kupon yang nomornya <strong>bilangan prima</strong>, yaitu bilangan bulat lebih dari 1 yang hanya habis dibagi 1 dan dirinya sendiri.</p>
<p>Di akhir acara, <strong>Q</strong> pengunjung datang menukarkan kuponnya. Nomor kupon bisa sangat besar, sampai 10<sup>12</sup>. Bantu panitia memeriksa setiap kupon: berhadiah atau tidak?</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi satu bilangan <code>n</code>, yaitu nomor sebuah kupon.</p>',
        'output_format' => '<p>Untuk setiap kupon, sesuai urutan masukan, cetak satu baris: <code>YA</code> jika n bilangan prima, atau <code>TIDAK</code> jika bukan.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100</li><li>1 ≤ n ≤ 10<sup>12</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1\n2\n15\n97\n1000000007\n", 'explanation' => '1 bukan prima karena bilangan prima harus lebih dari 1. 15 = 3 · 5 bukan prima. 2, 97, dan 1000000007 tidak punya pembagi selain 1 dan dirinya sendiri.'],
            ['input' => "3\n999966000289\n999962000357\n999999999989\n", 'explanation' => '999966000289 = 999983² dan 999962000357 = 999979 · 999983. Faktor prima terkecil keduanya hampir 10<sup>6</sup>, jadi pengecekan harus berjalan sampai √n. 999999999989 adalah bilangan prima terbesar yang tidak melebihi 10<sup>12</sup>.'],
        ],
        'tests' => function () use ($isPrime, $many) {
            $mk = fn (array $ns): string => count($ns)."\n".implode("\n", $ns)."\n";
            $prime = function (int $lo, int $hi) use ($isPrime): int {
                do {
                    $x = mt_rand($lo, $hi);
                } while (! $isPrime($x));

                return $x;
            };
            // Setiap bilangan: prima (dari $yes) atau bukan/acak (dari $no) dengan peluang sama.
            $mix = function (int $q, callable $yes, callable $no): array {
                $ns = [];
                for ($i = 0; $i < $q; $i++) {
                    $ns[] = mt_rand(0, 1) ? $yes() : $no();
                }

                return $ns;
            };
            $T = 1000000000000;
            $square = function () use ($prime): int {
                $p = $prime(2, 1000000);

                return $p * $p;
            };

            // Bilangan Carmichael & pseudoprima kuat: menjebak uji "Fermat" yang tidak tepat.
            $pseudo = [561, 1105, 1729, 2465, 2821, 6601, 8911, 10585, 15841, 29341, 41041, 46657, 52633, 62745, 63973, 75361, 101101, 115921,
                126217, 162401, 172081, 188461, 252601, 278545, 294409, 314821, 334153, 340561, 399001, 410041, 449065, 488881, 512461,
                25326001, 3215031751, 4759123141];
            $pseudoMix = array_merge($pseudo, $many(64, fn () => $prime(2, 10000000000)));
            T::shuffle($pseudoMix);

            // Pangkat 2, pangkat 3, dan bilangan Mersenne 2^k − 1.
            $powers = [];
            for ($k = 0; $k <= 36; $k++) {
                $powers[] = 2 ** $k;
            }
            for ($k = 1; $k <= 25; $k++) {
                $powers[] = 3 ** $k;
            }
            for ($k = 2; $k <= 39; $k++) {
                $powers[] = 2 ** $k - 1;
            }
            T::shuffle($powers);

            return [
                $mk([1]),
                $mk([2]),
                $mk(range(1, 100)),
                $mk($many(100, fn () => mt_rand(1, $T))),
                $mk($many(100, fn () => $prime($T - 100000000, $T))),
                $mk($mix(100, fn () => $prime(900000000000, $T), fn () => $prime(500000, 1000000) * $prime(500000, 1000000))),
                $mk($mix(100, fn () => $prime(2, $T), $square)),
                $mk($pseudoMix),
                $mk($mix(100, fn () => $prime(1, 1000000), fn () => mt_rand(1, 1000000))),
                $mk(range($T - 99, $T)),
                $mk($powers),
            ];
        },
        'solve' => function (string $input) use ($isPrime) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $out[] = $isPrime((int) trim($lines[$i])) ? 'YA' : 'TIDAK';
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int q;\n    cin >> q;\n    vector<long long> n(q);\n    for (auto& x : n) cin >> x;", "const q = Number(readLine());\nconst n = [];\nfor (let i = 0; i < q; i++) n.push(Number(readLine()));", "q = int(input())\nn = [int(input()) for _ in range(q)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    // Saringan Eratosthenes: semua prima <= 10^6 = akar dari 10^12
    const int B = 1000000;
    vector<bool> komposit(B + 1, false);
    vector<int> prima;
    for (int i = 2; i <= B; i++) {
        if (komposit[i]) continue;
        prima.push_back(i);
        for (long long j = (long long)i * i; j <= B; j += i) komposit[j] = true;
    }

    int q;
    cin >> q;
    string out;
    while (q--) {
        long long n;
        cin >> n;
        bool hasil = n >= 2;                 // 1 bukan prima
        // Bilangan komposit pasti punya faktor prima p dengan p * p <= n
        for (int p : prima) {
            if ((long long)p * p > n) break;
            if (n % p == 0) {
                hasil = false;
                break;
            }
        }
        out += hasil ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
// Saringan Eratosthenes: semua prima <= 10^6 = akar dari 10^12
const B = 1000000;
const komposit = new Uint8Array(B + 1);
const prima = [];
for (let i = 2; i <= B; i++) {
  if (komposit[i]) continue;
  prima.push(i);
  for (let j = i * i; j <= B; j += i) komposit[j] = 1;
}
const out = [];
for (let k = 0; k < q; k++) {
  const n = Number(readLine()); // <= 10^12, masih tepat sebagai Number
  let hasil = n >= 2;           // 1 bukan prima
  for (const p of prima) {
    if (p * p > n) break;
    if (n % p === 0) { hasil = false; break; }
  }
  out.push(hasil ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_right
from itertools import compress
from math import isqrt
input = sys.stdin.readline

# Saringan Eratosthenes: semua prima <= 10^6 = akar dari 10^12
B = 10**6
ayak = bytearray([1]) * (B + 1)
ayak[0] = ayak[1] = 0
for i in range(2, isqrt(B) + 1):
    if ayak[i]:
        ayak[i * i::i] = bytes(len(range(i * i, B + 1, i)))   # coret kelipatan sekaligus
prima = list(compress(range(B + 1), ayak))                   # 78 498 bilangan prima


def cek(n):
    if n < 2:                                  # 1 bukan prima
        return False
    k = bisect_right(prima, isqrt(n))          # cukup prima p dengan p <= akar(n)
    for p in prima[:k]:
        if n % p == 0:
            return False
    return True


q = int(input())
out = []
for _ in range(q):
    out.append("YA" if cek(int(input())) else "TIDAK")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Cara paling dasar menguji apakah n prima adalah mencoba membaginya dengan 2, 3, 4, …. Cukup sampai <strong>√n</strong>: jika n = a · b dengan a ≤ b, maka a · a ≤ n, sehingga setiap bilangan komposit punya pembagi selain 1 yang tidak lebih dari √n. Jangan lupa bahwa 1 bukan prima.</p>
<p>Untuk n ≤ 10<sup>12</sup>, √n ≤ 10<sup>6</sup>. Dengan 100 kupon, pembagian dengan semua bilangan 2..10<sup>6</sup> butuh sekitar 10<sup>8</sup> operasi modulo. C++ masih sanggup (apalagi jika hanya mencoba bilangan ganjil), tetapi Python jauh terlalu lambat.</p>
<p>Perbaikannya: pembagi terkecil (selain 1) dari bilangan komposit selalu <strong>prima</strong>. Jadi cukup coba bagi dengan bilangan prima ≤ 10<sup>6</sup>, yang banyaknya hanya 78 498. Daftar prima ini dibuat sekali dengan saringan Eratosthenes, lalu setiap kupon diperiksa dengan paling banyak 78 498 pembagian. Totalnya sekitar 8 · 10<sup>6</sup> operasi.</p>
<p><strong>Jebakan:</strong> n sampai 10<sup>12</sup> tidak muat di <code>int</code>, pakai <code>long long</code>. Hitung juga <code>p * p</code> sebagai long long saat membandingkannya dengan n. Bilangan seperti 999983² punya faktor prima tepat di √n, jadi syarat berhentinya harus <code>p * p &gt; n</code>, bukan <code>p * p &gt;= n</code>.</p>',
        'hints' => [
            'Bilangan komposit n = a · b dengan a ≤ b pasti punya pembagi a ≤ √n. Sampai mana kamu perlu mencoba membagi?',
            '√(10^12) = 10^6. Mencoba semua bilangan 2..10^6 untuk 100 kupon cukup lambat. Pembagi terkecil (selain 1) dari bilangan komposit selalu berupa bilangan apa?',
            'Saring semua prima ≤ 10^6 sekali dengan Eratosthenes (ada 78 498), lalu untuk setiap n coba bagi hanya dengan prima p selama p · p ≤ n. Ingat: 1 bukan prima.',
        ],
    ],

    [
        'slug' => 'hitung-prima',
        'lesson' => 'bilangan-prima',
        'title' => 'Lampu Rumah Prima',
        'difficulty' => 'Mudah',
        'tags' => ['bilangan prima', 'saringan eratosthenes', 'prefix sum'],
        'statement' => '<p>Jalan Panjang di Kota Angka memiliki 10<sup>7</sup> rumah bernomor 1, 2, …, 10<sup>7</sup>. Wali kota memutuskan bahwa setiap rumah yang nomornya <strong>bilangan prima</strong> akan mendapat lampu hias.</p>
<p>Sebelum lampu dipesan, petugas mengajukan <strong>Q</strong> pertanyaan. Setiap pertanyaan berisi dua bilangan <code>L</code> dan <code>R</code>: berapa banyak rumah bernomor prima di antara nomor L sampai R (keduanya termasuk)?</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi <code>L R</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, cetak banyak bilangan prima p dengan L ≤ p ≤ R, satu jawaban per baris.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ L ≤ R ≤ 10<sup>7</sup></li></ul>',
        'samples' => [
            ['input' => "3\n1 10\n10 20\n24 28\n", 'explanation' => 'Prima di antara 1 dan 10: 2, 3, 5, 7. Di antara 10 dan 20: 11, 13, 17, 19. Tidak ada prima di antara 24 dan 28 (24, 25, 26, 27, 28 semuanya komposit).'],
            ['input' => "2\n2 2\n1 100\n", 'explanation' => 'Rumah nomor 2 adalah prima. Ada 25 bilangan prima yang tidak melebihi 100.'],
        ],
        'tests' => function () {
            $mk = function (int $q, callable $gen): string {
                $lines = [$q];
                for ($i = 0; $i < $q; $i++) {
                    [$l, $r] = $gen();
                    $lines[] = "$l $r";
                }

                return implode("\n", $lines)."\n";
            };
            $pair = function (int $max): array {
                $a = mt_rand(1, $max);
                $b = mt_rand(1, $max);

                return $a <= $b ? [$a, $b] : [$b, $a];
            };
            $M = 10000000;

            return [
                "1\n1 1\n",
                "1\n1 10000000\n",
                "5\n2 2\n4 4\n1 2\n9999991 10000000\n9999992 10000000\n",
                $mk(100, fn () => $pair(100)),
                $mk(1000, fn () => $pair(100000)),
                $mk(200000, fn () => $pair($M)),
                $mk(200000, function () use ($M) {
                    $l = mt_rand(1, $M);

                    return [$l, min($M, $l + mt_rand(0, 150))];
                }),
                $mk(200000, fn () => [1, mt_rand(1, $M)]),
                $mk(100000, function () use ($M) {
                    $x = mt_rand(1, $M);

                    return [$x, $x];
                }),
                $mk(100000, fn () => [mt_rand(1, $M), $M]),
                $mk(200000, fn () => $pair(1000)),
            ];
        },
        'solve' => function (string $input) use ($primesUpTo) {
            $pr = $primesUpTo(10000000);
            $total = count($pr);
            // banyak prima <= x: indeks pertama di $pr yang > x (binary search)
            $pi = function (int $x) use ($pr, $total): int {
                $lo = 0;
                $hi = $total;
                while ($lo < $hi) {
                    $mid = ($lo + $hi) >> 1;
                    if ($pr[$mid] <= $x) {
                        $lo = $mid + 1;
                    } else {
                        $hi = $mid;
                    }
                }

                return $lo;
            };
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                [$l, $r] = T::ints($lines[$i]);
                $out[] = $pi($r) - $pi($l - 1);
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int q;\n    cin >> q;\n    vector<int> L(q), R(q);\n    for (int i = 0; i < q; i++) cin >> L[i] >> R[i];", "const q = Number(readLine());\nconst kueri = [];\nfor (let i = 0; i < q; i++) kueri.push(readInts()); // [L, R]", "data = sys.stdin.read().split()  # baca seluruh input sekaligus (cepat)\nq = int(data[0])\nkueri = [(int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(q)]  # (L, R)"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    vector<int> L(q), R(q);
    int M = 1;
    for (int i = 0; i < q; i++) {
        cin >> L[i] >> R[i];
        M = max(M, R[i]);
    }

    // Saringan Eratosthenes sampai M (R terbesar). vector<char> hemat memori: 1 byte per bilangan.
    vector<char> komposit(M + 1, 0);
    for (long long i = 2; i * i <= M; i++)
        if (!komposit[i])
            for (long long j = i * i; j <= M; j += i) komposit[j] = 1;

    // cnt[x] = banyak prima <= x
    vector<int> cnt(M + 1, 0);
    for (int x = 2; x <= M; x++) cnt[x] = cnt[x - 1] + (komposit[x] ? 0 : 1);

    string out;
    for (int i = 0; i < q; i++) {
        out += to_string(cnt[R[i]] - cnt[L[i] - 1]);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const L = new Int32Array(q), R = new Int32Array(q);
let M = 1;
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  L[i] = l;
  R[i] = r;
  if (r > M) M = r;
}
// Saringan Eratosthenes sampai M. Typed array jauh lebih hemat dan cepat daripada array biasa.
const komposit = new Uint8Array(M + 1);
for (let i = 2; i * i <= M; i++) {
  if (komposit[i]) continue;
  for (let j = i * i; j <= M; j += i) komposit[j] = 1;
}
// cnt[x] = banyak prima <= x
const cnt = new Int32Array(M + 1);
for (let x = 2; x <= M; x++) cnt[x] = cnt[x - 1] + (komposit[x] ? 0 : 1);
const out = new Array(q);
for (let i = 0; i < q; i++) out[i] = cnt[R[i]] - cnt[L[i] - 1];
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from array import array
from itertools import accumulate
from math import isqrt

data = sys.stdin.read().split()           # baca seluruh input sekaligus
q = int(data[0])
angka = list(map(int, data[1:1 + 2 * q]))  # L1 R1 L2 R2 ...
M = max(angka[1::2])                       # cukup menyaring sampai R terbesar

# Saringan Eratosthenes: ayak[x] = 1 jika x prima
ayak = bytearray([1]) * (M + 1)
ayak[0] = 0
if M >= 1:
    ayak[1] = 0
for i in range(2, isqrt(M) + 1):
    if ayak[i]:
        # coret i*i, i*i + i, ... sekaligus lewat slicing (jauh lebih cepat dari loop)
        ayak[i * i::i] = bytes(len(range(i * i, M + 1, i)))

# cnt[x] = banyak prima <= x. array('i') hanya 4 byte per elemen;
# list berisi 10^7 bilangan Python akan memakan ratusan MB.
cnt = array('i', accumulate(ayak))

out = []
for k in range(q):
    out.append(cnt[angka[2 * k + 1]] - cnt[angka[2 * k] - 1])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Menguji keprimaan setiap bilangan di [L, R] untuk setiap pertanyaan jelas terlalu lambat: satu pertanyaan saja bisa mencakup 10<sup>7</sup> bilangan, dan ada 2 · 10<sup>5</sup> pertanyaan.</p>
<p>Karena semua nomor tidak lebih dari 10<sup>7</sup>, kerjakan bagian beratnya sekali saja di awal:</p>
<ul><li><strong>Saringan Eratosthenes</strong> sampai M = R terbesar: untuk setiap prima i dengan i · i ≤ M, coret i · i, i · i + i, i · i + 2i, …. Kelipatan i yang lebih kecil dari i · i sudah dicoret oleh prima yang lebih kecil. Kompleksitasnya O(M log log M).</li>
<li><strong>Prefix count</strong>: <code>cnt[x]</code> = banyak prima ≤ x, dihitung dengan <code>cnt[x] = cnt[x − 1] + (x prima ? 1 : 0)</code>.</li></ul>
<p>Jawaban pertanyaan (L, R) adalah <code>cnt[R] − cnt[L − 1]</code>, O(1) per pertanyaan. Total O(M log log M + Q).</p>
<p><strong>Hemat memori dan waktu:</strong> array berukuran 10<sup>7</sup> cukup besar. Di C++ pakai <code>vector&lt;char&gt;</code> untuk saringan dan <code>vector&lt;int&gt;</code> untuk cnt. Di JavaScript pakai <code>Uint8Array</code> dan <code>Int32Array</code>, bukan array biasa. Di Python, coret kelipatan sekaligus lewat slicing <code>ayak[i*i::i] = bytes(...)</code> pada <code>bytearray</code>, lalu bangun cnt dengan <code>array(\'i\', accumulate(ayak))</code>; list berisi 10<sup>7</sup> bilangan Python memakan ratusan MB.</p>',
        'hints' => [
            'Ada banyak pertanyaan, tetapi semuanya tentang bilangan ≤ 10^7. Bisakah semua bilangan prima ≤ 10^7 ditemukan sekali saja di awal?',
            'Saringan Eratosthenes menandai semua prima ≤ M dalam O(M log log M). Lalu bagaimana menjawab "berapa prima di [L, R]" tanpa menghitung ulang?',
            'Buat cnt[x] = banyak prima ≤ x (prefix sum dari hasil saringan). Jawabannya cnt[R] − cnt[L − 1].',
        ],
    ],

    [
        'slug' => 'banyak-pembagi',
        'lesson' => 'bilangan-prima',
        'title' => 'Formasi Barisan Upacara',
        'difficulty' => 'Sedang',
        'tags' => ['faktorisasi prima', 'saringan spf', 'banyak pembagi'],
        'statement' => '<p>Menjelang upacara bendera, setiap kelas harus berbaris membentuk persegi panjang penuh: <strong>r</strong> baris yang masing-masing berisi <strong>c</strong> siswa, dengan r · c tepat sama dengan banyak siswa di kelas itu. Formasi 2 × 3 (2 baris, 3 siswa per baris) dianggap berbeda dengan 3 × 2. Formasi satu baris memanjang atau satu kolom juga boleh.</p>
<p>Ada <strong>N</strong> kelas; kelas ke-i berisi <code>a<sub>i</sub></code> siswa. Untuk setiap kelas, tentukan berapa banyak formasi berbeda yang bisa dibentuk.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> a<sub>2</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris berisi N bilangan dipisah spasi: banyak formasi untuk setiap kelas, sesuai urutan masukan.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4\n6 1 12 7\n", 'explanation' => '6 siswa: 1 × 6, 2 × 3, 3 × 2, 6 × 1. 1 siswa: hanya 1 × 1. 12 = 2² · 3 punya (2 + 1)(1 + 1) = 6 pembagi, yaitu 1, 2, 3, 4, 6, 12. 7 prima, jadi hanya 1 × 7 dan 7 × 1.'],
            ['input' => "3\n720720 1000000 999983\n", 'explanation' => '720720 = 2⁴ · 3² · 5 · 7 · 11 · 13, banyak pembaginya 5 · 3 · 2 · 2 · 2 · 2 = 240; tidak ada bilangan ≤ 10<sup>6</sup> yang punya lebih banyak pembagi. 10<sup>6</sup> = 2⁶ · 5⁶ memberi 7 · 7 = 49. 999983 adalah prima.'],
        ],
        'tests' => function () use ($many) {
            $mk = fn (array $a): string => count($a)."\n".implode(' ', $a)."\n";
            // bilangan dengan banyak pembagi atau banyak faktor prima
            $hc = [720720, 831600, 942480, 982800, 997920, 524288, 531441, 362880, 665280, 498960, 554400, 604800];
            // hasil kali prima-prima kecil acak, tidak lebih dari 10^6
            $smooth = function (): int {
                $ps = [2, 3, 5, 7, 11, 13];
                $x = 1;
                while (true) {
                    $p = $ps[mt_rand(0, 5)];
                    if ($x * $p > 1000000) {
                        return $x;
                    }
                    $x *= $p;
                }
            };
            // pangkat sebuah prima kecil
            $power = function (): int {
                $p = [2, 3, 5, 7][mt_rand(0, 3)];
                $x = $p;
                while ($x * $p <= 1000000 && mt_rand(0, 9) > 0) {
                    $x *= $p;
                }

                return $x;
            };
            $shuffled = range(1, 100);
            T::shuffle($shuffled);

            return [
                $mk([1]),
                $mk(range(1, 10)),
                $mk($shuffled),
                $mk($many(1000, fn () => mt_rand(1, 1000))),
                $mk($many(200000, fn () => mt_rand(1, 1000000))),
                $mk($many(200000, fn () => $hc[mt_rand(0, count($hc) - 1)])),
                $mk($many(200000, $smooth)),
                $mk($many(200000, fn () => mt_rand(999000, 1000000))),
                $mk(array_fill(0, 200000, 1000000)),
                $mk($many(200000, fn () => mt_rand(1, 20))),
                $mk($many(200000, $power)),
            ];
        },
        'solve' => function (string $input) {
            static $spf = null;
            if ($spf === null) {
                $M = 1000000;
                $spf = array_fill(0, $M + 1, 0);
                for ($i = 2; $i <= $M; $i++) {
                    if ($spf[$i] === 0) {
                        $spf[$i] = $i;
                        for ($j = $i * $i; $j <= $M; $j += $i) {
                            if ($spf[$j] === 0) {
                                $spf[$j] = $i;
                            }
                        }
                    }
                }
            }
            $lines = T::lines($input);
            $out = [];
            foreach (T::ints($lines[1]) as $x) {
                $d = 1;
                while ($x > 1) {
                    $p = $spf[$x];
                    $e = 0;
                    while ($x % $p === 0) {
                        $x = intdiv($x, $p);
                        $e++;
                    }
                    $d *= $e + 1;
                }
                $out[] = $d;
            }

            return implode(' ', $out);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;", "const [n] = readInts();\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
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
    int M = 1;
    for (int i = 0; i < n; i++) {
        cin >> a[i];
        M = max(M, a[i]);
    }

    // spf[x] = faktor prima terkecil dari x (0 = belum terisi)
    vector<int> spf(M + 1, 0);
    for (int i = 2; i <= M; i++) {
        if (spf[i] != 0) continue;           // i komposit
        spf[i] = i;                           // i prima
        for (long long j = (long long)i * i; j <= M; j += i)
            if (spf[j] == 0) spf[j] = i;
    }

    string out;
    for (int i = 0; i < n; i++) {
        int x = a[i];
        int d = 1;
        // Faktorkan x: bagi terus dengan spf[x]. Paling banyak ~20 langkah karena x minimal terbagi 2.
        while (x > 1) {
            int p = spf[x], e = 0;
            while (x % p == 0) {
                x /= p;
                e++;
            }
            d *= e + 1;                       // pangkat p di pembagi bisa 0, 1, ..., e
        }
        if (i > 0) out += ' ';
        out += to_string(d);
    }
    out += '\n';
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
let M = 1;
for (const x of a) if (x > M) M = x;
// spf[x] = faktor prima terkecil dari x (0 = belum terisi)
const spf = new Int32Array(M + 1);
for (let i = 2; i <= M; i++) {
  if (spf[i] !== 0) continue; // i komposit
  spf[i] = i;                 // i prima
  for (let j = i * i; j <= M; j += i) if (spf[j] === 0) spf[j] = i;
}
const out = new Array(n);
for (let k = 0; k < n; k++) {
  let x = a[k], d = 1;
  while (x > 1) {
    const p = spf[x];
    let e = 0;
    while (x % p === 0) {
      x /= p;
      e++;
    }
    d *= e + 1; // pangkat p di pembagi bisa 0, 1, ..., e
  }
  out[k] = d;
}
console.log(out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
from math import isqrt

data = sys.stdin.read().split()   # baca seluruh input sekaligus
n = int(data[0])
a = list(map(int, data[1:1 + n]))
M = max(a)

# Prima kecil (<= akar M) dengan saringan Eratosthenes biasa
akar = isqrt(M)
ayak = bytearray([1]) * (akar + 1)
prima_kecil = []
for i in range(2, akar + 1):
    if ayak[i]:
        prima_kecil.append(i)
        ayak[i * i::i] = bytes(len(range(i * i, akar + 1, i)))

# spf[x] = faktor prima terkecil dari x.
# Isi dari prima TERBESAR ke terkecil lewat slicing: tulisan terakhir pada spf[j]
# berasal dari prima terkecil yang membagi j, jadi hasil akhirnya tepat.
spf = list(range(M + 1))
for p in reversed(prima_kecil):
    spf[p * p::p] = [p] * len(range(p * p, M + 1, p))

out = []
for x in a:
    d = 1
    while x > 1:
        p = spf[x]
        e = 0
        while x % p == 0:
            x //= p
            e += 1
        d *= e + 1                # pangkat p di pembagi bisa 0, 1, ..., e
    out.append(d)
print(" ".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Formasi r × c ditentukan sepenuhnya oleh r (karena c = a / r), dan r harus membagi a. Jadi jawabannya adalah <strong>banyak pembagi</strong> dari a.</p>
<p>Jika faktorisasi prima a = p<sub>1</sub><sup>e<sub>1</sub></sup> · p<sub>2</sub><sup>e<sub>2</sub></sup> · … · p<sub>k</sub><sup>e<sub>k</sub></sup>, setiap pembagi memilih pangkat 0, 1, …, e<sub>j</sub> untuk setiap p<sub>j</sub> secara bebas, sehingga banyak pembaginya (e<sub>1</sub> + 1)(e<sub>2</sub> + 1)…(e<sub>k</sub> + 1). Untuk a = 1 faktorisasinya kosong dan hasilnya 1.</p>
<p>Memfaktorkan setiap bilangan dengan pembagian sampai √a butuh hingga 2 · 10<sup>5</sup> · 10<sup>3</sup> = 2 · 10<sup>8</sup> operasi. Lebih cepat: buat <strong>saringan faktor prima terkecil</strong> <code>spf[x]</code> untuk semua x ≤ 10<sup>6</sup>. Caranya seperti Eratosthenes, tetapi saat mencoret j sebagai kelipatan prima i, catat <code>spf[j] = i</code> jika belum terisi. Karena prima diproses dari yang terkecil, isian pertama selalu faktor prima terkecil.</p>
<p>Setelah itu, faktorisasi a cukup dengan terus membagi a dengan spf[a] sambil menghitung pangkatnya. Setiap pembagian membuat a minimal setengahnya, jadi paling banyak log<sub>2</sub> a ≈ 20 langkah. Total O(M log log M + N log M) dengan M = 10<sup>6</sup>.</p>
<p>Di Python, mengisi spf dengan loop ganda cukup lambat. Triknya: proses prima dari yang <em>terbesar</em> ke terkecil dan isi <code>spf[p*p::p]</code> sekaligus dengan slicing. Penulisan terakhir pada spf[j] berasal dari prima terkecil yang membagi j, sehingga hasilnya tetap faktor prima terkecil.</p>',
        'hints' => [
            'Formasi r × c ditentukan oleh r saja. Syarat apa yang harus dipenuhi r?',
            'Jawabannya adalah banyak pembagi a. Jika a = p₁^e₁ · … · pₖ^eₖ, banyak pembaginya (e₁ + 1)…(eₖ + 1). Bagaimana memfaktorkan 200 000 bilangan dengan cepat?',
            'Saring faktor prima terkecil spf[x] untuk semua x ≤ 10^6. Faktorkan a dengan berulang kali membagi a dengan spf[a] sambil menghitung pangkat setiap prima.',
        ],
    ],

    [
        'slug' => 'pecahan-sederhana',
        'lesson' => 'bilangan-prima',
        'title' => 'Tanda pada Penggaris',
        'difficulty' => 'Sulit',
        'tags' => ['fungsi phi euler', 'fpb', 'saringan', 'prefix sum'],
        'statement' => '<p>Pak Darto, seorang tukang kayu, sedang membuat penggaris sepanjang 1 meter. Ia memilih sebuah bilangan <strong>N</strong>, lalu untuk setiap q = 2, 3, …, N ia membagi penggaris menjadi q bagian yang sama panjang dan menggores tanda di setiap titik pembagiannya, yaitu di posisi 1/q, 2/q, …, (q − 1)/q meter dari ujung kiri. Kedua ujung penggaris tidak ditandai.</p>
<p>Banyak goresan jatuh di tempat yang sama, misalnya 1/2 dan 2/4. Pak Darto ingin tahu berapa banyak <strong>posisi berbeda</strong> yang bertanda setelah semua goresan selesai. Ia berencana membuat <strong>Q</strong> penggaris dengan nilai N yang berbeda-beda. Jawablah untuk setiap penggaris.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Baris kedua berisi <code>N<sub>1</sub> N<sub>2</sub> … N<sub>Q</sub></code>.</p>',
        'output_format' => '<p>Q baris. Baris ke-i berisi banyak posisi bertanda yang berbeda pada penggaris dengan N = N<sub>i</sub>.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>1 ≤ N<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n2 4 6\n", 'explanation' => 'N = 2: satu tanda di 1/2. N = 4: tanda di 1/4, 1/3, 1/2, 2/3, 3/4; goresan 2/4 jatuh di posisi yang sama dengan 1/2. N = 6: q = 5 menambah 1/5, 2/5, 3/5, 4/5 dan q = 6 menambah 1/6, 5/6 (goresan 2/6, 3/6, 4/6 sudah ada), sehingga totalnya 5 + 4 + 2 = 11.'],
            ['input' => "2\n1 1000000\n", 'explanation' => 'N = 1: tidak ada goresan sama sekali. Untuk N = 10<sup>6</sup> jawabannya lebih dari 3 · 10<sup>11</sup>, jadi butuh tipe data 64-bit.'],
        ],
        'tests' => function () use ($many) {
            $mk = fn (array $ns): string => count($ns)."\n".implode(' ', $ns)."\n";

            return [
                $mk([1]),
                $mk([2]),
                $mk(range(1, 10)),
                $mk($many(100, fn () => mt_rand(1, 100))),
                $mk($many(1000, fn () => mt_rand(1, 10000))),
                $mk($many(100000, fn () => mt_rand(1, 1000000))),
                $mk($many(100000, fn () => mt_rand(900000, 1000000))),
                $mk($many(100000, fn () => mt_rand(1, 1000))),
                $mk([1000000]),
                // sebaran logaritmik: N kecil dan besar sama-sama sering muncul
                $mk($many(100000, fn () => (int) (10 ** (mt_rand(0, 6000000) / 1000000)))),
            ];
        },
        'solve' => function (string $input) {
            static $pre = null;
            if ($pre === null) {
                $M = 1000000;
                $phi = range(0, $M);
                for ($p = 2; $p <= $M; $p++) {
                    if ($phi[$p] === $p) {
                        for ($j = $p; $j <= $M; $j += $p) {
                            $phi[$j] -= intdiv($phi[$j], $p);
                        }
                    }
                }
                $pre = array_fill(0, $M + 1, 0);
                for ($x = 2; $x <= $M; $x++) {
                    $pre[$x] = $pre[$x - 1] + $phi[$x];
                }
            }
            $lines = T::lines($input);
            $out = [];
            foreach (T::ints($lines[1]) as $n) {
                $out[] = $pre[$n];
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int q;\n    cin >> q;\n    vector<int> ns(q);\n    for (auto& x : ns) cin >> x;", "const [q] = readInts();\nconst ns = readInts();", "q = int(input())\nns = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    vector<int> ns(q);
    int M = 1;
    for (auto& x : ns) {
        cin >> x;
        M = max(M, x);
    }

    // Saringan phi: mula-mula phi[x] = x. Untuk setiap prima p,
    // kalikan phi semua kelipatan p dengan (1 - 1/p), yaitu phi[j] -= phi[j] / p.
    vector<int> phi(M + 1);
    for (int i = 0; i <= M; i++) phi[i] = i;
    for (int p = 2; p <= M; p++) {
        if (phi[p] != p) continue;           // phi[p] sudah berubah -> p bukan prima
        for (int j = p; j <= M; j += p) phi[j] -= phi[j] / p;
    }

    // pre[n] = phi(2) + ... + phi(n). Bisa mencapai ~3 * 10^11 -> long long.
    // phi(1) tidak dihitung: penyebut 1 tidak menghasilkan tanda.
    vector<long long> pre(M + 1, 0);
    for (int x = 2; x <= M; x++) pre[x] = pre[x - 1] + phi[x];

    string out;
    for (int x : ns) {
        out += to_string(pre[x]);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [q] = readInts();
const ns = readInts();
let M = 1;
for (const x of ns) if (x > M) M = x;
// Saringan phi: phi[x] = x, lalu untuk setiap prima p kalikan kelipatannya dengan (1 - 1/p)
const phi = new Int32Array(M + 1);
for (let i = 0; i <= M; i++) phi[i] = i;
for (let p = 2; p <= M; p++) {
  if (phi[p] !== p) continue; // p bukan prima
  for (let j = p; j <= M; j += p) phi[j] -= (phi[j] / p) | 0;
}
// pre[n] = phi(2) + ... + phi(n) <= 3,04 * 10^11: Float64Array masih menyimpannya dengan tepat
const pre = new Float64Array(M + 1);
for (let x = 2; x <= M; x++) pre[x] = pre[x - 1] + phi[x];
const out = new Array(q);
for (let i = 0; i < q; i++) out[i] = pre[ns[i]];
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate


def main():
    data = sys.stdin.read().split()   # baca seluruh input sekaligus
    q = int(data[0])
    ns = list(map(int, data[1:1 + q]))
    M = max(ns)

    # Saringan phi: mula-mula phi[x] = x. Untuk setiap prima p (phi[p] masih p),
    # kalikan phi semua kelipatan p dengan (1 - 1/p), yaitu x -> x - x // p.
    # Slicing phi[p::p] memproses semua kelipatan p sekaligus.
    phi = list(range(M + 1))
    for p in range(2, M + 1):
        if phi[p] == p:
            phi[p::p] = [x - x // p for x in phi[p::p]]

    phi[1] = 0                       # penyebut 1 tidak menghasilkan tanda
    pre = list(accumulate(phi))      # pre[n] = phi(2) + ... + phi(n)
    print("\n".join(str(pre[n]) for n in ns))


main()
CODE,
        ],
        'editorial' => '<p>Setiap posisi bertanda adalah sebuah pecahan di antara 0 dan 1. Setiap nilai pecahan punya tepat satu bentuk <strong>paling sederhana</strong> p/q dengan FPB(p, q) = 1. Jika suatu nilai digores sebagai a/b dengan b ≤ N, bentuk sederhananya punya penyebut q ≤ b ≤ N, jadi bentuk sederhana itu juga ikut digores. Maka banyak posisi berbeda sama dengan banyak pasangan (p, q) dengan 1 ≤ p &lt; q ≤ N dan FPB(p, q) = 1.</p>
<p>Untuk penyebut q tertentu, banyak p di 1..q − 1 yang relatif prima dengan q adalah <strong>fungsi phi Euler</strong> φ(q). Jadi jawabannya φ(2) + φ(3) + … + φ(N).</p>
<p>Hitung φ untuk semua bilangan ≤ 10<sup>6</sup> dengan <strong>saringan phi</strong>: mula-mula phi[x] = x; untuk setiap prima p (dikenali karena phi[p] masih bernilai p, sebab bilangan komposit pasti sudah diubah oleh faktor prima terkecilnya), kurangi setiap kelipatan j dengan <code>phi[j] / p</code>. Ini menerapkan rumus φ(n) = n · Π(1 − 1/p) untuk setiap prima p yang membagi n, dan pembagiannya selalu tepat. Kompleksitasnya O(M log log M). Lalu buat prefix sum agar setiap pertanyaan dijawab dalam O(1).</p>
<p><strong>Jebakan:</strong> φ(1) = 1 tidak boleh ikut dijumlahkan karena penyebut 1 tidak menghasilkan tanda (jawaban untuk N = 1 adalah 0). Jawaban untuk N = 10<sup>6</sup> sekitar 3,04 · 10<sup>11</sup>, melebihi batas <code>int</code>, jadi pakai <code>long long</code>. Di JavaScript, Number masih tepat sampai 9 · 10<sup>15</sup>.</p>',
        'hints' => [
            'Dua goresan jatuh di posisi yang sama jika pecahannya bernilai sama. Setiap nilai punya tepat satu bentuk paling sederhana; hitung saja bentuk-bentuk itu.',
            'Untuk penyebut q, banyak pembilang 1 ≤ p < q dengan FPB(p, q) = 1 adalah φ(q). Jadi jawabannya φ(2) + … + φ(N).',
            'Saring φ untuk semua bilangan ≤ 10^6 (phi[x] = x, lalu untuk setiap prima p kurangi phi[j] dengan phi[j] / p pada semua kelipatan j), buat prefix sum, dan pakai long long.',
        ],
    ],
];
