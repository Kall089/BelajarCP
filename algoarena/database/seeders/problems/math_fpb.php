<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi FPB, KPK & Aritmetika Modular:
 * algoritma Euclid, KPK, pangkat cepat, Euclid diperluas (Diophantine linear), CRT dua kongruensi.
 * Semua kode C++ harus lolos C++14.
 * JavaScript memakai perkalian modulo yang dipecah 16 bit dan BigInt untuk nilai di atas 2^53.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** FPB dengan algoritma Euclid (solusi referensi PHP). */
$gcd = function (int $a, int $b): int {
    while ($b !== 0) {
        $r = $a % $b;
        $a = $b;
        $b = $r;
    }

    return $a;
};

/** Euclid diperluas iteratif: [g, x] dengan a·x + b·y = g = FPB(a, b). */
$egcd = function (int $a, int $b): array {
    $x0 = 1;
    $x1 = 0;
    while ($b !== 0) {
        $t = intdiv($a, $b);
        $s = $a - $t * $b;
        $a = $b;
        $b = $s;
        $s = $x0 - $t * $x1;
        $x0 = $x1;
        $x1 = $s;
    }

    return [$a, $x0];
};

/** Token-token input sebagai bilangan bulat (cepat untuk Q besar). */
$tok = fn (string $input): array => array_map('intval', preg_split('/\s+/', trim($input)));

/** Susun input "Q" + Q baris dari generator satu baris. */
$kueri = function (int $q, callable $baris): string {
    $rows = [];
    for ($i = 0; $i < $q; $i++) {
        $rows[] = $baris();
    }

    return "$q\n".implode("\n", $rows)."\n";
};

$cppEgcd = <<<'CODE'
// Euclid diperluas: mengembalikan g = FPB(a, b) dan mengisi x, y sehingga a·x + b·y = g
long long fpbDiperluas(long long a, long long b, long long& x, long long& y) {
    if (b == 0) {
        x = 1;
        y = 0;
        return a;
    }
    long long x1, y1;
    long long g = fpbDiperluas(b, a % b, x1, y1);
    x = y1;
    y = x1 - (a / b) * y1;
    return g;
}
CODE;

$jsEgcd = <<<'CODE'
// x · y mod m untuk 0 ≤ x, y < m ≤ 10^9 tanpa melewati 2^53:
// y dipecah menjadi 16 bit atas dan 16 bit bawah
const mulmod = (x, y, m) => (((x * (y >>> 16)) % m) * 65536 + x * (y & 65535)) % m;

// Euclid diperluas (iteratif): mengembalikan [g, x] dengan a·x + b·y = g = FPB(a, b).
// Semua nilai antara tetap ≤ 2 · 10^9, aman untuk Number.
function fpbDiperluas(a, b) {
  let x0 = 1, x1 = 0;
  while (b !== 0) {
    const t = Math.floor(a / b);
    let s = a - t * b;
    a = b;
    b = s;
    s = x0 - t * x1;
    x0 = x1;
    x1 = s;
  }
  return [a, x0];
}
CODE;

$pyEgcd = <<<'CODE'
def fpb_diperluas(a, b):
    # Euclid diperluas (iteratif): kembalikan (g, x) dengan a·x + b·y = g = FPB(a, b)
    x0, x1 = 1, 0
    while b:
        t = a // b
        a, b = b, a - t * b
        x0, x1 = x1, x0 - t * x1
    return a, x0
CODE;

return [
    [
        'slug' => 'jadwal-bersama',
        'lesson' => 'fpb-modular',
        'title' => 'Jadwal Dua Kereta',
        'difficulty' => 'Mudah',
        'tags' => ['fpb', 'kpk', 'algoritma euclid'],
        'statement' => '<p>Di Stasiun Lembah ada dua jalur kereta. Kereta jalur Merah berangkat setiap <strong>a</strong> menit dan kereta jalur Biru setiap <strong>b</strong> menit. Keduanya berangkat bersamaan pada menit 0.</p>
<p>Untuk setiap usulan jadwal, kepala stasiun ingin tahu dua hal:</p>
<ul><li>Bel peron akan dipasang agar berbunyi setiap <strong>d</strong> menit sejak menit 0, dan setiap keberangkatan kedua kereta harus tepat bersamaan dengan bunyi bel. Berapa d terbesar yang mungkin?</li><li>Setelah menit 0, pada menit ke berapa kedua kereta pertama kali berangkat bersamaan lagi?</li></ul>
<p>Ada <strong>Q</strong> usulan jadwal. Jawab kedua pertanyaan untuk setiap usulan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi <code>a b</code>.</p>',
        'output_format' => '<p>Q baris; baris ke-i berisi dua bilangan untuk usulan ke-i: d terbesar, lalu menit keberangkatan bersama berikutnya.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 10<sup>5</sup></li><li>1 ≤ a, b ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n4 6\n7 5\n15 45\n", 'explanation' => 'Jadwal (4, 6): Merah berangkat di menit 0, 4, 8, 12, …, Biru di menit 0, 6, 12, …; bel setiap 2 menit mengenai semua keberangkatan, dan keduanya bersama lagi di menit 12. Jadwal (7, 5): hanya bel setiap 1 menit yang cocok, dan keduanya bersama lagi di menit 35. Jadwal (15, 45): 45 adalah kelipatan 15, jadi jawabannya 15 dan 45.'],
            ['input' => "1\n1000000000 999999999\n", 'explanation' => 'Dua bilangan berurutan selalu saling prima, jadi d = 1 dan keduanya bersama lagi di menit 10<sup>9</sup> · 999 999 999 = 999 999 999 000 000 000. Nilai ini jauh melewati batas <code>int</code>.'],
        ],
        'tests' => function () use ($kueri) {
            $maks = 1000000000;
            $acak = fn (int $hi) => fn () => mt_rand(1, $hi).' '.mt_rand(1, $hi);
            $faktor = function () use ($maks) {
                $g = mt_rand(1, 100000);
                $lim = intdiv($maks, $g);

                return ($g * mt_rand(1, $lim)).' '.($g * mt_rand(1, $lim));
            };
            $kelipatan = function () use ($maks) {
                $a = mt_rand(1, 100000);
                $b = $a * mt_rand(1, intdiv($maks, $a));

                return mt_rand(0, 1) ? "$a $b" : "$b $a";
            };
            // pasangan Fibonacci berurutan: kasus terburuk banyak langkah Euclid
            $fib = [1, 2];
            while ($fib[count($fib) - 1] + $fib[count($fib) - 2] <= $maks) {
                $fib[] = $fib[count($fib) - 1] + $fib[count($fib) - 2];
            }
            $fibRows = [];
            for ($i = 0; $i + 1 < count($fib); $i++) {
                $fibRows[] = mt_rand(0, 1) ? $fib[$i].' '.$fib[$i + 1] : $fib[$i + 1].' '.$fib[$i];
            }
            $fibRows[] = $fib[count($fib) - 1].' '.$fib[count($fib) - 3];
            T::shuffle($fibRows);

            return [
                "1\n1 1\n",
                "5\n6 6\n1 1000000000\n1000000000 1\n999999937 999999929\n1000000000 1000000000\n",
                $kueri(10, $acak(30)),
                $kueri(1000, $acak(1000)),
                count($fibRows)."\n".implode("\n", $fibRows)."\n",
                $kueri(100000, $acak($maks)),
                $kueri(100000, $faktor),
                $kueri(100000, $kelipatan),
                $kueri(100000, fn () => mt_rand(999000000, $maks).' '.mt_rand(999000000, $maks)),
                $kueri(20000, fn () => (1 << mt_rand(0, 29)).' '.((1 << mt_rand(0, 18)) * 3 ** mt_rand(0, 6))),
            ];
        },
        'solve' => function (string $input) use ($tok, $gcd) {
            $t = $tok($input);
            $q = $t[0];
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                $a = $t[1 + 2 * $i];
                $b = $t[2 + 2 * $i];
                $g = $gcd($a, $b);
                $out[] = $g.' '.(intdiv($a, $g) * $b);
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int q;\n    cin >> q;\n    while (q--) {\n        long long a, b;\n        cin >> a >> b;\n    }",
            "const q = Number(readLine());\nfor (let i = 0; i < q; i++) {\n  const [a, b] = readInts();\n}",
            "q = int(input())\nfor _ in range(q):\n    a, b = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

// Algoritma Euclid: FPB(a, b) = FPB(b, a mod b), dan FPB(a, 0) = a
long long fpb(long long a, long long b) {
    while (b != 0) {
        long long r = a % b;
        a = b;
        b = r;
    }
    return a;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    string out;
    while (q--) {
        long long a, b;
        cin >> a >> b;
        // d harus membagi a dan b sekaligus -> d terbesar = FPB
        long long g = fpb(a, b);
        // keberangkatan bersama = kelipatan persekutuan -> yang terkecil = KPK
        // bagi dulu baru kali: hasilnya bisa ~10^18, jadi wajib long long
        long long kpk = a / g * b;
        out += to_string(g);
        out += ' ';
        out += to_string(kpk);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Algoritma Euclid: FPB(a, b) = FPB(b, a mod b), dan FPB(a, 0) = a
function fpb(a, b) {
  while (b !== 0) {
    const r = a % b;
    a = b;
    b = r;
  }
  return a;
}

const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [a, b] = readInts();
  const g = fpb(a, b);
  // KPK bisa sampai ~10^18, melewati 2^53: hitung dengan BigInt agar tepat
  const kpk = BigInt(a / g) * BigInt(b);
  out.push(g + " " + kpk);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline


def fpb(a, b):
    # Algoritma Euclid (math.gcd melakukan hal yang sama)
    while b:
        a, b = b, a % b
    return a


q = int(input())
out = []
for _ in range(q):
    a, b = map(int, input().split())
    g = fpb(a, b)
    out.append(f"{g} {a // g * b}")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Bel setiap d menit berbunyi di menit 0, d, 2d, … Semua keberangkatan Merah (kelipatan a) dan Biru (kelipatan b) terkena bunyi bel tepat jika d membagi a <em>dan</em> d membagi b. Jadi d terbesar adalah <strong>FPB(a, b)</strong>. Menit keberangkatan bersama adalah bilangan yang sekaligus kelipatan a dan kelipatan b; yang terkecil setelah 0 adalah <strong>KPK(a, b)</strong>.</p>
<p>FPB dihitung dengan algoritma Euclid: <code>FPB(a, b) = FPB(b, a mod b)</code> dan <code>FPB(a, 0) = a</code>. Banyak langkahnya O(log min(a, b)), sehingga 10<sup>5</sup> pertanyaan selesai seketika. Setelah itu <code>KPK = a / FPB · b</code>.</p>
<p><strong>Jebakan overflow:</strong> KPK bisa hampir 10<sup>18</sup> (contoh kedua), jauh di atas batas <code>int</code> (sekitar 2,1 · 10<sup>9</sup>), jadi pakai <code>long long</code>. Biasakan membagi dulu baru mengalikan (<code>a / g * b</code>): di soal ini a · b ≤ 10<sup>18</sup> kebetulan masih muat, tetapi pada batas yang lebih besar a · b bisa meluap walaupun KPK-nya sendiri muat. Di JavaScript, Number hanya tepat sampai sekitar 9 · 10<sup>15</sup>, jadi KPK dihitung dengan BigInt.</p>',
        'hints' => [
            'Bel setiap d menit berbunyi di menit 0, d, 2d, … Kapan semua kelipatan a ada di antaranya?',
            'd harus membagi a dan b sekaligus, jadi jawabannya FPB(a, b). Keberangkatan bersama adalah kelipatan persekutuan; yang terkecil adalah KPK.',
            'Hitung FPB dengan algoritma Euclid, lalu KPK = a / FPB · b dalam long long (BigInt di JavaScript).',
        ],
    ],

    [
        'slug' => 'pangkat-besar',
        'lesson' => 'fpb-modular',
        'title' => 'Kalender Planet Jauh',
        'difficulty' => 'Sedang',
        'tags' => ['pangkat cepat', 'aritmetika modular'],
        'statement' => '<p>Badan antariksa sedang menyusun kalender untuk planet-planet yang baru ditemukan. Di sebuah planet, satu pekan terdiri dari <strong>m</strong> hari bernomor 0, 1, …, m − 1; setelah hari m − 1, kalender kembali ke hari 0. Di setiap planet, hari ini adalah hari nomor 0.</p>
<p>Para astronom gemar bertanya tentang hari yang sangat jauh di masa depan. Ada <strong>Q</strong> pertanyaan. Setiap pertanyaan menyebut sebuah planet dengan pekan sepanjang m hari dan menanyakan: hari nomor berapa yang jatuh tepat <strong>a<sup>b</sup></strong> hari dari sekarang?</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi <code>a b m</code>.</p>',
        'output_format' => '<p>Q baris; baris ke-i berisi nomor hari untuk pertanyaan ke-i.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 10<sup>5</sup></li><li>1 ≤ a ≤ 10<sup>9</sup></li><li>0 ≤ b ≤ 10<sup>18</sup></li><li>1 ≤ m ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4\n2 10 1000\n5 3 13\n3 0 5\n7 1000000000000000000 1\n", 'explanation' => '2<sup>10</sup> = 1024 = 1 · 1000 + 24, jadi hari nomor 24. Lalu 5<sup>3</sup> = 125 = 9 · 13 + 8. Berikutnya 3<sup>0</sup> = 1: besok adalah hari nomor 1. Terakhir, planet dengan pekan sepanjang 1 hari hanya punya hari nomor 0.'],
            ['input' => "2\n1000000000 1000000000000000000 999999999\n999999999 3 1000000000\n", 'explanation' => '10<sup>9</sup> = 999 999 999 + 1, jadi 10<sup>9</sup> ≡ 1 (mod 999 999 999) dan pangkat berapa pun tetap bersisa 1. Lalu 999 999 999 ≡ −1 (mod 10<sup>9</sup>), sehingga pangkat tiganya ≡ −1 ≡ 999 999 999.'],
        ],
        'tests' => function () use ($kueri) {
            $G = 1000000000;
            $E = 1000000000000000000;

            return [
                "1\n2 10 1000\n",
                "8\n5 0 1\n5 0 7\n1000000000 1000000000000000000 1\n1000000000 3 1000000000\n7 1000000000000000000 1000000000\n1 1000000000000000000 999999999\n2 62 1000000000\n999999999 999999999999999999 1000000000\n",
                $kueri(300, fn () => mt_rand(1, 20).' '.mt_rand(0, 30).' '.mt_rand(1, 50)),
                $kueri(1000, fn () => mt_rand(1, $G).' '.mt_rand(0, 1000000).' '.mt_rand(1, $G)),
                $kueri(100000, fn () => mt_rand(1, $G).' '.mt_rand(0, $E).' '.mt_rand(1, $G)),
                $kueri(100000, fn () => mt_rand(1, $G).' '.($E - mt_rand(0, 1000)).' 1000000007'),
                $kueri(100000, fn () => mt_rand(1, $G).' '.mt_rand(0, $E).' '.mt_rand(1, 12)),
                $kueri(100000, fn () => mt_rand(999999000, $G).' '.((1 << 59) - 1 - mt_rand(0, 1)).' '.mt_rand(999999000, $G)),
                $kueri(50000, fn () => mt_rand(1, $G).' 999999935 999999937'),
                $kueri(50000, fn () => mt_rand(1, $G).' '.mt_rand(0, 3).' '.mt_rand(1, $G)),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            $q = $t[0];
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                $a = $t[1 + 3 * $i];
                $b = $t[2 + 3 * $i];
                $m = $t[3 + 3 * $i];
                $h = 1 % $m;
                $a %= $m;
                while ($b > 0) {
                    if ($b & 1) {
                        $h = $h * $a % $m;
                    }
                    $a = $a * $a % $m;
                    $b >>= 1;
                }
                $out[] = $h;
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int q;\n    cin >> q;\n    while (q--) {\n        long long a, b, m;   // b sampai 10^18 masih muat di long long\n        cin >> a >> b >> m;\n    }",
            "const q = Number(readLine());\nfor (let i = 0; i < q; i++) {\n  const [sa, sb, sm] = readLine().trim().split(/\\s+/);\n  const a = Number(sa), m = Number(sm);\n  const b = BigInt(sb); // b sampai 10^18 tidak tepat jika disimpan sebagai Number\n}",
            "q = int(input())\nfor _ in range(q):\n    a, b, m = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

// a^b mod m dengan pangkat cepat (binary exponentiation)
long long pangkat(long long a, long long b, long long m) {
    long long h = 1 % m;          // m = 1: semua bilangan bersisa 0
    a %= m;
    while (b > 0) {
        // h dan a selalu < m <= 10^9, jadi hasil kalinya < 10^18: aman di long long
        if (b & 1) h = h * a % m;
        a = a * a % m;            // a, a^2, a^4, a^8, ...
        b >>= 1;
    }
    return h;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    string out;
    while (q--) {
        long long a, b, m;
        cin >> a >> b >> m;
        out += to_string(pangkat(a, b, m));
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// x · y mod m untuk 0 ≤ x, y < m ≤ 10^9 tanpa melewati 2^53:
// y dipecah menjadi 16 bit atas dan 16 bit bawah
const mulmod = (x, y, m) => (((x * (y >>> 16)) % m) * 65536 + x * (y & 65535)) % m;

const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [sa, sb, sm] = readLine().trim().split(/\s+/);
  const m = Number(sm);
  const a = Number(sa) % m;
  // b sampai 10^18 tidak tepat sebagai Number: ubah ke BigInt, lalu ambil digit binernya
  const bit = BigInt(sb).toString(2);
  // pangkat cepat dari bit paling kiri: kuadratkan, lalu kalikan a jika bitnya 1
  let h = 1 % m;
  for (let j = 0; j < bit.length; j++) {
    h = mulmod(h, h, m);
    if (bit[j] === "1") h = mulmod(h, a, m);
  }
  out.push(h);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline


def pangkat(a, b, m):
    # pangkat cepat; ditulis untuk belajar, pow(a, b, m) bawaan melakukan hal yang sama
    h = 1 % m
    a %= m
    while b > 0:
        if b & 1:
            h = h * a % m
        a = a * a % m
        b >>= 1
    return h


q = int(input())
out = []
for _ in range(q):
    a, b, m = map(int, input().split())
    # pow tiga argumen ditulis dalam C sehingga jauh lebih cepat daripada pangkat() di atas
    out.append(pow(a, b, m))
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Hari yang jatuh n hari dari sekarang bernomor <code>n mod m</code>, jadi yang dicari adalah <code>a<sup>b</sup> mod m</code>. Nilai a<sup>b</sup> sendiri luar biasa besar, tetapi kita hanya butuh sisanya, dan sifat <code>(x · y) mod m = ((x mod m) · (y mod m)) mod m</code> membolehkan kita mengambil modulo setelah setiap perkalian.</p>
<p>Mengalikan a sebanyak b kali tetap mustahil untuk b = 10<sup>18</sup>. <strong>Pangkat cepat</strong> memakai <code>a<sup>2k</sup> = (a<sup>k</sup>)<sup>2</sup></code>: tulis b dalam biner, kuadratkan a berulang-ulang (a, a<sup>2</sup>, a<sup>4</sup>, a<sup>8</sup>, …), dan kalikan ke hasil hanya pangkat yang bitnya 1 pada b. Cukup sekitar 60 langkah per pertanyaan, total O(Q log b).</p>
<p>Jebakan yang perlu diperhatikan:</p>
<ul><li>Setelah a dimodulo, kedua faktor selalu &lt; m ≤ 10<sup>9</sup>, sehingga hasil kalinya &lt; 10<sup>18</sup> dan muat di <code>long long</code>. Lupa memodulo a di awal bisa membuat a · a meluap.</li><li>Untuk b = 0 jawabannya 1 mod m, dan untuk m = 1 jawabannya selalu 0. Mulailah dengan <code>hasil = 1 % m</code>, bukan 1.</li><li>JavaScript: b sampai 10<sup>18</sup> tidak bisa disimpan tepat sebagai Number, jadi baca sebagai BigInt lalu ambil bit-bitnya. Hasil kali dua bilangan &lt; 10<sup>9</sup> bisa melewati 2<sup>53</sup>, jadi pakai perkalian modulo yang dipecah 16 bit.</li><li>Python: <code>pow(a, b, m)</code> bawaan sudah memakai pangkat cepat.</li></ul>',
        'hints' => [
            'Hari ke-n jatuh pada nomor n mod m. Bolehkah modulo diambil di tengah-tengah perhitungan a^b?',
            'Pangkat genap bisa dipecah: a^(2k) = (a^k)². Lihat b dalam bentuk biner.',
            'Selama b > 0: jika bit terakhir b bernilai 1, kalikan hasil dengan a; lalu a = a² mod m dan b dibagi 2. Mulai dari hasil = 1 % m.',
        ],
    ],

    [
        'slug' => 'timbangan-dua-batu',
        'lesson' => 'fpb-modular',
        'title' => 'Timbangan Dua Batu',
        'difficulty' => 'Sedang',
        'tags' => ['euclid diperluas', 'persamaan diophantine', 'fpb'],
        'statement' => '<p>Bu Rina berjualan rempah di pasar dengan timbangan dua piring. Ia punya dua jenis batu anak timbangan dalam jumlah tak terbatas: batu A seberat <strong>a</strong> gram dan batu B seberat <strong>b</strong> gram.</p>
<p>Untuk menimbang pesanan seberat <strong>c</strong> gram, rempah diletakkan di piring kiri. Batu A terlalu besar untuk piring kiri, jadi hanya boleh diletakkan di piring kanan. Batu B boleh diletakkan di piring mana saja: di piring kanan, atau di piring kiri bersama rempah. Timbangan harus seimbang.</p>
<p>Misalkan Bu Rina memakai <strong>x</strong> batu A, dan <strong>y</strong> adalah banyak batu B di piring kanan dikurangi banyak batu B di piring kiri. Timbangan seimbang tepat ketika <code>a · x + b · y = c</code>. Bu Rina ingin memakai batu A <strong>sesedikit mungkin</strong>, yaitu x ≥ 0 sekecil mungkin.</p>
<p>Ada <strong>Q</strong> pesanan. Untuk setiap pesanan, tentukan x dan y tersebut, atau laporkan bahwa pesanan itu tidak mungkin ditimbang.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi <code>a b c</code>.</p>',
        'output_format' => '<p>Q baris. Untuk setiap pesanan, cetak <code>x y</code> (y negatif berarti |y| batu B diletakkan di piring kiri), atau <code>-1</code> jika tidak ada bilangan bulat x ≥ 0 dan y yang memenuhi.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 10<sup>5</sup></li><li>1 ≤ a, b ≤ 10<sup>9</sup></li><li>0 ≤ c ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4\n4 6 10\n4 6 7\n7 5 3\n6 3 12\n", 'explanation' => 'Pesanan 10 gram: satu batu A (4 g) dan satu batu B (6 g) di piring kanan, 4 + 6 = 10; tanpa batu A, kelipatan 6 tidak pernah sama dengan 10. Pesanan 7 gram mustahil: dengan batu 4 g dan 6 g, selisih berat kedua piring selalu genap. Pesanan 3 gram: empat batu A di kanan (28 g) dan lima batu B di kiri bersama rempah (3 + 25 = 28); 7x − 3 harus habis dibagi 5, dan x = 0, 1, 2, 3 tidak memenuhi. Pesanan 12 gram: cukup empat batu B di kanan tanpa batu A.'],
        ],
        'tests' => function () use ($kueri) {
            $G = 1000000000;
            $baris = function (int $maxV, string $mode): string {
                if ($mode === 'campur') {
                    $mode = ['acak', 'faktor', 'pasti'][mt_rand(0, 2)];
                }
                if ($mode === 'acak') {
                    return mt_rand(1, $maxV).' '.mt_rand(1, $maxV).' '.mt_rand(0, $maxV);
                }
                // a dan b berbagi faktor g: 'faktor' memilih c acak (sering mustahil), 'pasti' memilih c kelipatan g
                $g = mt_rand(2, max(2, min(100000, intdiv($maxV, 3))));
                $lim = max(1, intdiv($maxV, $g));
                $c = $mode === 'pasti' ? $g * mt_rand(0, $lim) : mt_rand(0, $maxV);

                return ($g * mt_rand(1, $lim)).' '.($g * mt_rand(1, $lim)).' '.$c;
            };

            return [
                "1\n1 1 0\n",
                "6\n2 4 7\n1 1000000000 1000000000\n1000000000 1 1000000000\n1000000000 999999999 1\n2 999999999 1\n1000000000 1000000000 0\n",
                $kueri(10, fn () => $baris(20, 'campur')),
                $kueri(500, fn () => $baris(40, 'campur')),
                $kueri(2000, fn () => $baris(100000, 'campur')),
                $kueri(100000, fn () => $baris($G, 'acak')),
                $kueri(100000, fn () => $baris($G, 'faktor')),
                $kueri(100000, fn () => $baris($G, 'pasti')),
                $kueri(100000, fn () => $baris($G, 'campur')),
                $kueri(50000, fn () => mt_rand(999000000, $G).' '.mt_rand(999000000, $G).' '.mt_rand(0, $G)),
            ];
        },
        'solve' => function (string $input) use ($tok, $egcd) {
            $t = $tok($input);
            $q = $t[0];
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                $a = $t[1 + 3 * $i];
                $b = $t[2 + 3 * $i];
                $c = $t[3 + 3 * $i];
                [$g, $p] = $egcd($a, $b);
                if ($c % $g !== 0) {
                    $out[] = '-1';

                    continue;
                }
                $bb = intdiv($b, $g);
                $x = (($p % $bb) + $bb) % $bb * (intdiv($c, $g) % $bb) % $bb;
                $y = intdiv($c - $a * $x, $b);
                $out[] = "$x $y";
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int q;\n    cin >> q;\n    while (q--) {\n        long long a, b, c;\n        cin >> a >> b >> c;\n    }",
            "const q = Number(readLine());\nfor (let i = 0; i < q; i++) {\n  const [a, b, c] = readInts();\n}",
            "q = int(input())\nfor _ in range(q):\n    a, b, c = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppEgcd.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    string out;
    while (q--) {
        long long a, b, c;
        cin >> a >> b >> c;
        long long p, t;
        long long g = fpbDiperluas(a, b, p, t);   // a·p + b·t = g
        if (c % g != 0) {                         // ruas kiri selalu kelipatan g
            out += "-1\n";
            continue;
        }
        // Dibagi g: (a/g)·p ≡ 1 (mod b/g), jadi x ≡ (c/g)·p (mod b/g).
        // Semua solusi x berselisih kelipatan b/g -> x terkecil yang >= 0 ada di [0, b/g).
        long long bb = b / g;
        long long pp = (p % bb + bb) % bb;        // % di C++ bisa negatif
        long long x = pp * ((c / g) % bb) % bb;   // kedua faktor < 10^9: hasil kali < 10^18
        long long y = (c - a * x) / b;            // x < b, jadi |a·x| < 10^18 masih muat
        out += to_string(x);
        out += ' ';
        out += to_string(y);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => $jsEgcd.<<<'CODE'


const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [a, b, c] = readInts();
  const [g, p] = fpbDiperluas(a, b);   // a·p + b·(...) = g
  if (c % g !== 0) {
    out.push(-1);
    continue;
  }
  // x ≡ (c/g)·p (mod b/g); normalkan p dulu karena % di JavaScript bisa negatif
  const bb = b / g;
  const x = mulmod(((p % bb) + bb) % bb, (c / g) % bb, bb);
  // a·x bisa ~10^18: hitung y dengan BigInt (hasilnya sendiri |y| ≤ 10^9)
  const y = (BigInt(c) - BigInt(a) * BigInt(x)) / BigInt(b);
  out.push(x + " " + y);
}
console.log(out.join("\n"));
CODE,
            'python' => "import sys\ninput = sys.stdin.readline\n\n\n".$pyEgcd.<<<'CODE'



q = int(input())
out = []
for _ in range(q):
    a, b, c = map(int, input().split())
    g, p = fpb_diperluas(a, b)
    if c % g:
        out.append("-1")
        continue
    bb = b // g
    x = p * (c // g) % bb       # % di Python selalu non-negatif
    y = (c - a * x) // b
    out.append(f"{x} {y}")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Ini adalah persamaan Diophantine linear <code>a·x + b·y = c</code>. Misalkan g = FPB(a, b). Ruas kiri selalu kelipatan g, jadi jika c tidak habis dibagi g, jawabannya <code>-1</code>. Sebaliknya, Euclid diperluas memberi p dan t dengan <code>a·p + b·t = g</code>; kalikan kedua ruas dengan c/g untuk mendapat satu solusi <code>x<sub>0</sub> = p · (c/g)</code>, <code>y<sub>0</sub> = t · (c/g)</code>.</p>
<p>Semua solusi berbentuk <code>x = x<sub>0</sub> + k · (b/g)</code>, <code>y = y<sub>0</sub> − k · (a/g)</code> untuk bilangan bulat k. Menambah b/g batu A sambil mengurangi a/g batu B tidak mengubah keseimbangan (a · b/g − b · a/g = 0), dan tidak ada geseran yang lebih kecil karena a/g dan b/g saling prima. Jadi x terkecil yang ≥ 0 adalah <code>x<sub>0</sub> mod (b/g)</code> (dinormalkan agar tidak negatif), lalu <code>y = (c − a·x) / b</code>. Kompleksitas O(Q log max(a, b)).</p>
<p><strong>Jebakan:</strong></p>
<ul><li>p bisa sebesar b/g dan c/g sampai 10<sup>9</sup>, jadi p · (c/g) bisa mendekati 10<sup>18</sup>. Modulo-kan kedua faktor dengan b/g dulu sehingga hasil kalinya pasti &lt; 10<sup>18</sup>. Karena x &lt; b, nilai a·x &lt; 10<sup>18</sup> juga masih muat di <code>long long</code>.</li><li>Operator <code>%</code> di C++ dan JavaScript bisa menghasilkan sisa negatif (p sering negatif). Normalkan dengan <code>(p % m + m) % m</code>.</li><li>JavaScript: gunakan perkalian modulo 16 bit untuk x dan BigInt untuk a·x.</li></ul>',
        'hints' => [
            'Jika g = FPB(a, b), ruas kiri a·x + b·y selalu kelipatan g. Kapan persamaan pasti tidak punya solusi?',
            'Euclid diperluas memberi a·p + b·t = g. Kalikan dengan c/g untuk mendapat satu solusi. Seperti apa bentuk solusi-solusi lainnya?',
            'Semua nilai x yang sah berselisih kelipatan b/g. Jadi x = (p · c/g) mod (b/g) dan y = (c − a·x)/b. Modulo-kan faktor sebelum mengalikan agar tidak meluap.',
        ],
    ],

    [
        'slug' => 'dua-jam-pasir',
        'lesson' => 'fpb-modular',
        'title' => 'Dua Jam Pasir',
        'difficulty' => 'Sulit',
        'tags' => ['chinese remainder theorem', 'euclid diperluas', 'invers modular'],
        'statement' => '<p>Di menara pengawas sebuah benteng ada dua jam pasir yang dibalik otomatis oleh pegas setiap kali pasirnya habis. Pasir jam pertama habis dalam <strong>m<sub>1</sub></strong> menit dan pasir jam kedua dalam <strong>m<sub>2</sub></strong> menit. Keduanya mulai mengalir bersamaan tepat pada menit 0. Kacanya diberi garis skala, sehingga dari tinggi pasir terlihat sudah berapa menit jam itu mengalir sejak terakhir dibalik.</p>
<p>Seorang penjaga terbangun dan mencatat bahwa jam pertama sudah mengalir <strong>r<sub>1</sub></strong> menit dan jam kedua <strong>r<sub>2</sub></strong> menit sejak terakhir dibalik. Dengan kata lain, jika sekarang menit ke-x, maka <code>x mod m<sub>1</sub> = r<sub>1</sub></code> dan <code>x mod m<sub>2</sub> = r<sub>2</sub></code>. Paling sedikit berapa menit yang sudah berlalu sejak menit 0? Bisa jadi penjaga salah lihat, sehingga keadaan itu tidak pernah terjadi.</p>
<p>Ada <strong>Q</strong> catatan penjaga. Jawab setiap catatan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi <code>m<sub>1</sub> r<sub>1</sub> m<sub>2</sub> r<sub>2</sub></code>.</p>',
        'output_format' => '<p>Q baris. Untuk setiap catatan, cetak bilangan bulat x ≥ 0 terkecil yang memenuhi kedua syarat, atau <code>-1</code> jika tidak ada.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 10<sup>5</sup></li><li>1 ≤ m<sub>1</sub>, m<sub>2</sub> ≤ 10<sup>9</sup> (tidak harus saling prima)</li><li>0 ≤ r<sub>1</sub> &lt; m<sub>1</sub> dan 0 ≤ r<sub>2</sub> &lt; m<sub>2</sub></li></ul>',
        'samples' => [
            ['input' => "4\n3 2 5 3\n4 1 6 3\n4 2 6 3\n5 0 7 0\n", 'explanation' => 'Catatan pertama: menit yang bersisa 2 jika dibagi 3 adalah 2, 5, 8, …, dan yang pertama bersisa 3 jika dibagi 5 adalah 8. Catatan kedua: kandidatnya 1, 5, 9, …, dan 9 mod 6 = 3. Catatan ketiga mustahil: x mod 4 = 2 berarti x genap, sedangkan x mod 6 = 3 berarti x ganjil. Catatan keempat: kedua jam baru saja dibalik, dan menit 0 sendiri sudah cocok.'],
            ['input' => "1\n999999937 999999936 999999929 999999928\n", 'explanation' => 'Kedua modulus adalah bilangan prima, sehingga KPK-nya 999 999 937 · 999 999 929 = 999 999 866 000 004 473. Sisa yang dicatat sama dengan −1 untuk kedua modulus, jadi x = KPK − 1. Jawabannya hampir 10<sup>18</sup>, sehingga perhitungan antara harus dijaga agar tidak meluap.'],
        ],
        'tests' => function () use ($kueri) {
            $G = 1000000000;
            $baris = function (int $maxM, string $mode): string {
                if ($mode === 'campur') {
                    $mode = ['acak', 'pasti', 'mustahil', 'faktor'][mt_rand(0, 3)];
                }
                if ($mode === 'acak') {
                    $m1 = mt_rand(1, $maxM);
                    $m2 = mt_rand(1, $maxM);

                    return "$m1 ".mt_rand(0, $m1 - 1)." $m2 ".mt_rand(0, $m2 - 1);
                }
                // m1 = g·u, m2 = g·v: solusi ada tepat jika r1 ≡ r2 (mod FPB)
                $g = mt_rand(2, max(2, min(100000, intdiv($maxM, 3))));
                $lim = max(1, intdiv($maxM, $g));
                $v = mt_rand(1, $lim);
                $m1 = $g * mt_rand(1, $lim);
                $m2 = $g * $v;
                $r1 = mt_rand(0, $m1 - 1);
                if ($mode === 'faktor') {
                    $r2 = mt_rand(0, $m2 - 1);
                } elseif ($mode === 'pasti') {
                    $r2 = $r1 % $g + $g * mt_rand(0, $v - 1);
                } else {
                    $r2 = ($r1 + mt_rand(1, $g - 1)) % $g + $g * mt_rand(0, $v - 1);
                }

                return "$m1 $r1 $m2 $r2";
            };
            $besar = function () use ($G) {
                $m1 = mt_rand(999000000, $G);
                $m2 = mt_rand(999000000, $G);

                return "$m1 ".mt_rand(0, $m1 - 1)." $m2 ".mt_rand(0, $m2 - 1);
            };
            $membagi = function () use ($G) {
                $m1 = mt_rand(1, 100000);
                $m2 = $m1 * mt_rand(1, intdiv($G, $m1));
                $r2 = mt_rand(0, $m2 - 1);
                $r1 = mt_rand(0, 1) ? $r2 % $m1 : mt_rand(0, $m1 - 1);

                return mt_rand(0, 1) ? "$m1 $r1 $m2 $r2" : "$m2 $r2 $m1 $r1";
            };

            return [
                "1\n1 0 1 0\n",
                "7\n1000000000 999999999 1000000000 999999999\n1000000000 0 1000000000 1\n999999937 0 999999929 1\n1 0 1000000000 123456789\n1000000000 999999999 999999999 999999998\n1000000000 123456789 1 0\n6 5 10 9\n",
                $kueri(10, fn () => $baris(12, 'campur')),
                $kueri(500, fn () => $baris(30, 'campur')),
                $kueri(3000, fn () => $baris(100000, 'campur')),
                $kueri(100000, fn () => $baris($G, 'acak')),
                $kueri(100000, fn () => $baris($G, 'campur')),
                $kueri(100000, fn () => $baris($G, 'pasti')),
                $kueri(100000, $besar),
                $kueri(50000, $membagi),
            ];
        },
        'solve' => function (string $input) use ($tok, $egcd) {
            $t = $tok($input);
            $q = $t[0];
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                $m1 = $t[1 + 4 * $i];
                $r1 = $t[2 + 4 * $i];
                $m2 = $t[3 + 4 * $i];
                $r2 = $t[4 + 4 * $i];
                [$g, $p] = $egcd($m1, $m2);
                $d = $r2 - $r1;
                if ($d % $g !== 0) {
                    $out[] = -1;

                    continue;
                }
                $mm = intdiv($m2, $g);
                $k = ((intdiv($d, $g) % $mm) + $mm) % $mm * ((($p % $mm) + $mm) % $mm) % $mm;
                $out[] = $r1 + $m1 * $k;
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int q;\n    cin >> q;\n    while (q--) {\n        long long m1, r1, m2, r2;\n        cin >> m1 >> r1 >> m2 >> r2;\n    }",
            "const q = Number(readLine());\nfor (let i = 0; i < q; i++) {\n  const [m1, r1, m2, r2] = readInts();\n}",
            "q = int(input())\nfor _ in range(q):\n    m1, r1, m2, r2 = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppEgcd.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    string out;
    while (q--) {
        long long m1, r1, m2, r2;
        cin >> m1 >> r1 >> m2 >> r2;
        // x = r1 + m1·k, dan syarat kedua menjadi m1·k ≡ r2 − r1 (mod m2)
        long long p, t;
        long long g = fpbDiperluas(m1, m2, p, t);   // m1·p + m2·t = g
        long long d = r2 - r1;
        if (d % g != 0) {
            out += "-1\n";
            continue;
        }
        // Dibagi g: (m1/g)·k ≡ d/g (mod m2/g), dan p adalah invers m1/g modulo m2/g
        long long mm = m2 / g;
        long long dd = (d / g % mm + mm) % mm;      // normalkan: d bisa negatif
        long long pp = (p % mm + mm) % mm;
        long long k = dd * pp % mm;                 // kedua faktor < 10^9: hasil kali < 10^18
        // k < m2/g, jadi x = r1 + m1·k < m1·m2/g = KPK <= 10^18
        long long x = r1 + m1 * k;
        out += to_string(x);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => $jsEgcd.<<<'CODE'


const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [m1, r1, m2, r2] = readInts();
  // x = r1 + m1·k, dan syarat kedua menjadi m1·k ≡ r2 − r1 (mod m2)
  const [g, p] = fpbDiperluas(m1, m2);   // m1·p + m2·(...) = g
  const d = r2 - r1;
  if (d % g !== 0) {
    out.push(-1);
    continue;
  }
  // (m1/g)·k ≡ d/g (mod m2/g), dan p adalah invers m1/g modulo m2/g
  const mm = m2 / g;
  const k = mulmod((((d / g) % mm) + mm) % mm, ((p % mm) + mm) % mm, mm);
  // x < KPK ≤ 10^18 melewati 2^53: susun dengan BigInt
  out.push(String(BigInt(r1) + BigInt(m1) * BigInt(k)));
}
console.log(out.join("\n"));
CODE,
            'python' => "import sys\ninput = sys.stdin.readline\n\n\n".$pyEgcd.<<<'CODE'



q = int(input())
out = []
for _ in range(q):
    m1, r1, m2, r2 = map(int, input().split())
    # x = r1 + m1·k, dan syarat kedua menjadi m1·k ≡ r2 − r1 (mod m2)
    g, p = fpb_diperluas(m1, m2)        # m1·p + m2·(...) = g
    d = r2 - r1
    if d % g:
        out.append("-1")
        continue
    mm = m2 // g
    k = d // g * p % mm                 # p = invers m1/g modulo m2/g; hasil % selalu >= 0
    out.append(str(r1 + m1 * k))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Catatan penjaga berarti <code>x ≡ r<sub>1</sub> (mod m<sub>1</sub>)</code> dan <code>x ≡ r<sub>2</sub> (mod m<sub>2</sub>)</code>: Chinese Remainder Theorem untuk dua kongruensi, tetapi modulusnya tidak harus saling prima.</p>
<p>Dari kongruensi pertama, <code>x = r<sub>1</sub> + m<sub>1</sub>·k</code> untuk suatu bilangan bulat k. Substitusikan ke kongruensi kedua: <code>m<sub>1</sub>·k ≡ r<sub>2</sub> − r<sub>1</sub> (mod m<sub>2</sub>)</code>. Misalkan g = FPB(m<sub>1</sub>, m<sub>2</sub>). Ruas kiri dan m<sub>2</sub> sama-sama kelipatan g, jadi jika r<sub>2</sub> − r<sub>1</sub> tidak habis dibagi g, tidak ada solusi. Jika habis, bagi semuanya dengan g:</p>
<p style="text-align:center"><code>(m<sub>1</sub>/g) · k ≡ (r<sub>2</sub> − r<sub>1</sub>)/g (mod m<sub>2</sub>/g)</code></p>
<p>Sekarang m<sub>1</sub>/g dan m<sub>2</sub>/g saling prima, sehingga m<sub>1</sub>/g punya invers modulo m<sub>2</sub>/g. Euclid diperluas pada (m<sub>1</sub>, m<sub>2</sub>) memberi <code>m<sub>1</sub>·p + m<sub>2</sub>·t = g</code>; setelah dibagi g terlihat bahwa p adalah invers tersebut. (Invers lewat teorema Fermat tidak bisa dipakai karena m<sub>2</sub>/g belum tentu prima.) Ambil <code>k = ((r<sub>2</sub> − r<sub>1</sub>)/g · p) mod (m<sub>2</sub>/g)</code> di rentang [0, m<sub>2</sub>/g). Maka <code>x = r<sub>1</sub> + m<sub>1</sub>·k</code> berada di [0, KPK), dan karena solusi berulang setiap KPK(m<sub>1</sub>, m<sub>2</sub>) menit, inilah x terkecil. Kompleksitas O(Q log max(m<sub>1</sub>, m<sub>2</sub>)).</p>
<p><strong>Jebakan overflow:</strong> KPK bisa hampir 10<sup>18</sup> (contoh kedua). Jangan mengalikan tiga bilangan besar sekaligus: m<sub>1</sub> · p · (r<sub>2</sub> − r<sub>1</sub>) bisa mencapai 10<sup>27</sup>. Normalkan dulu kedua faktor ke [0, m<sub>2</sub>/g) sehingga hasil kalinya &lt; 10<sup>18</sup>, baru kalikan k dengan m<sub>1</sub>; hasil akhirnya &lt; KPK ≤ 10<sup>18</sup> dan muat di <code>long long</code>. (Alternatif di C++: <code>__int128</code> untuk perkalian antara.) Ingat juga bahwa r<sub>2</sub> − r<sub>1</sub> dan p bisa negatif. Di JavaScript, hitung k dengan perkalian modulo 16 bit, lalu x dengan BigInt.</p>',
        'hints' => [
            'Tulis x = r1 + m1·k. Syarat apa yang harus dipenuhi k agar x mod m2 = r2?',
            'm1·k ≡ r2 − r1 (mod m2) hanya punya solusi jika g = FPB(m1, m2) membagi r2 − r1. Setelah semuanya dibagi g, m1/g punya invers modulo m2/g, yang bisa didapat dari Euclid diperluas.',
            'k = ((r2 − r1)/g · invers) mod (m2/g), lalu x = r1 + m1·k < KPK. Normalkan faktor yang negatif dan modulo-kan sebelum mengalikan agar tidak meluap.',
        ],
    ],
];
