<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Palindrom & Algoritma Manacher: menghitung substring palindrom,
 * cek s[a..b] palindrom dalam O(1), akhiran palindrom terpanjang, dan DP partisi palindrom.
 * Semua kode C++ harus lolos C++14. Konvensi Manacher mengikuti materi (indeks 0):
 *   t = "#s0#s1#...#" (|t| = 2n + 1), p[i] = jari-jari palindrom terpanjang di t yang berpusat di i
 *   = panjang palindrom aslinya di s. Huruf s[x] ada di t[2x + 1]; pusat potongan s[a..b] adalah t[a + b + 1].
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$AZ = 'abcdefghijklmnopqrstuvwxyz';

/** Manacher untuk solusi referensi PHP: array p dengan konvensi di atas. */
$manPal = function (string $s): array {
    $t = '#'.implode('#', str_split($s)).'#';
    $n = strlen($t);
    $p = array_fill(0, $n, 0);
    for ($i = 0, $l = 0, $r = -1; $i < $n; $i++) {
        $k = $i > $r ? 0 : min($p[$l + $r - $i], $r - $i);
        while ($i - $k - 1 >= 0 && $i + $k + 1 < $n && $t[$i - $k - 1] === $t[$i + $k + 1]) {
            $k++;
        }
        $p[$i] = $k;
        if ($i + $k > $r) {
            $l = $i - $k;
            $r = $i + $k;
        }
    }

    return $p;
};

/** Sambungan palindrom acak (ganjil/genap) sepanjang $n: banyak palindrom panjang di dalamnya. */
$gabungPal = function (int $n, string $abjad, int $maks): string {
    $s = '';
    while (strlen($s) < $n) {
        $p = T::word(mt_rand(1, $maks), $abjad);
        $s .= $p.(mt_rand(0, 1) ? substr(strrev($p), 1) : strrev($p));
    }

    return substr($s, 0, $n);
};

/** Awalan sepanjang $n dari kata Fibonacci (abaababaabaab...). */
$fibo = function (int $n): string {
    $a = 'a';
    $b = 'ab';
    while (strlen($b) < $n) {
        [$a, $b] = [$b, $b.$a];
    }

    return substr($b, 0, $n);
};

/** Blok-blok 'a' dengan panjang acak 1..$maks yang dipisah satu huruf dari 'bcd'. */
$blokA = function (int $n, int $maks): string {
    $s = '';
    while (strlen($s) < $n) {
        $s .= str_repeat('a', mt_rand(1, $maks)).'bcd'[mt_rand(0, 2)];
    }

    return substr($s, 0, $n);
};

/** Fungsi Manacher dalam C++ (sama dengan materi; dipakai semua solusi C++). */
$manCpp = <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

// Manacher dengan pemisah '#': t = "#s0#s1#...#", |t| = 2n + 1.
// p[i] = jari-jari palindrom terpanjang di t yang berpusat di i = panjang palindrom aslinya di s.
vector<int> manacher(const string& s) {
    string t = "#";
    for (char c : s) {
        t += c;
        t += '#';
    }
    int n = t.size();
    vector<int> p(n, 0);
    // [l, r] = palindrom dengan ujung kanan paling jauh sejauh ini ("kotak")
    for (int i = 0, l = 0, r = -1; i < n; i++) {
        int k = (i > r) ? 0 : min(p[l + r - i], r - i);   // pinjam dari cermin j = l + r - i
        while (i - k - 1 >= 0 && i + k + 1 < n && t[i - k - 1] == t[i + k + 1]) k++;
        p[i] = k;
        if (i + k > r) {
            l = i - k;
            r = i + k;
        }
    }
    return p;
}

CODE;

/** Fungsi Manacher dalam JavaScript. */
$manJs = <<<'CODE'
// Manacher dengan pemisah '#': t = "#s0#s1#...#", |t| = 2n + 1.
// p[i] = jari-jari palindrom terpanjang di t yang berpusat di i = panjang palindrom aslinya di s.
function manacher(s) {
  const t = "#" + s.split("").join("#") + "#";
  const n = t.length;
  const p = new Int32Array(n);
  for (let i = 0, l = 0, r = -1; i < n; i++) {
    let k = i > r ? 0 : Math.min(p[l + r - i], r - i); // pinjam dari cermin j = l + r - i
    while (i - k - 1 >= 0 && i + k + 1 < n && t.charCodeAt(i - k - 1) === t.charCodeAt(i + k + 1)) k++;
    p[i] = k;
    if (i + k > r) { l = i - k; r = i + k; }
  }
  return p;
}

CODE;

/** Fungsi Manacher dalam Python. */
$manPy = <<<'CODE'
import sys


def manacher(s):
    # t = "#s0#s1#...#"; p[i] = jari-jari palindrom terpanjang di t yang berpusat di i
    #                          = panjang palindrom aslinya di s
    t = "#" + "#".join(s) + "#"
    n = len(t)
    p = [0] * n
    l, r = 0, -1                      # "kotak": palindrom dengan ujung kanan terjauh
    for i in range(n):
        k = 0
        if i <= r:
            k = p[l + r - i]          # pinjam dari cermin j = l + r - i
            if k > r - i:
                k = r - i
        while i - k - 1 >= 0 and i + k + 1 < n and t[i - k - 1] == t[i + k + 1]:
            k += 1
        p[i] = k
        if i + k > r:
            l, r = i - k, i + k
    return p


CODE;

$bacaS = ["    string s;\n    cin >> s;", 'const s = readLine().trim();', 's = input().strip()'];

return [
    [
        'slug' => 'hitung-potongan-palindrom',
        'lesson' => 'palindrom',
        'title' => 'Kalung Manik Cermin',
        'difficulty' => 'Mudah',
        'tags' => ['string', 'palindrom', 'manacher', 'menghitung'],
        'statement' => '<p>Nenek Lastri merangkai manik-manik berhuruf menjadi seuntai kalung lurus yang terbaca sebagai string <strong>S</strong>. Cucunya, Dinda, senang mencari <em>potongan cermin</em>: beberapa manik berurutan yang terbaca sama dari kiri maupun dari kanan, seperti <code>a</code>, <code>aa</code>, atau <code>aba</code>.</p>
<p>Ada berapa potongan S yang merupakan palindrom? Dua potongan dihitung berbeda jika letaknya berbeda, walaupun hurufnya sama. Setiap potongan sepanjang satu huruf juga palindrom.</p>',
        'input_format' => '<p>Satu baris berisi string <code>S</code>.</p>',
        'output_format' => '<p>Banyak potongan S yang palindrom.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 500 000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code>.</li></ul>',
        'samples' => [
            ['input' => "abaab\n", 'explanation' => 'Ada 5 potongan satu huruf, ditambah <code>aba</code> (huruf 1–3), <code>aa</code> (huruf 3–4), dan <code>baab</code> (huruf 2–5). Totalnya 8.'],
            ['input' => "aaaa\n", 'explanation' => 'Semua potongan string ini palindrom: 4 + 3 + 2 + 1 = 10. Potongan <code>aa</code> muncul di tiga letak berbeda sehingga dihitung tiga kali.'],
        ],
        'tests' => function () use ($AZ, $gabungPal, $fibo, $blokA) {
            $N = 500000;

            return [
                "a\n", "ab\n", "zz\n", T::word(12, 'ab')."\n", T::word(1000, 'abc')."\n",
                str_repeat('a', $N)."\n",
                T::word($N, $AZ)."\n",
                T::word($N, 'ab')."\n",
                $gabungPal($N, 'abc', 2000)."\n",
                $fibo($N)."\n",
                str_repeat('ab', intdiv($N, 2))."\n",
                $blokA($N, 3000)."\n",
            ];
        },
        'solve' => function (string $input) use ($manPal) {
            $total = 0;
            foreach ($manPal(trim(T::lines($input)[0])) as $x) {
                $total += intdiv($x + 1, 2);
            }

            return (string) $total;
        },
        'starter' => $st(...$bacaS),
        'solutions' => [
            'cpp' => $manCpp.<<<'CODE'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    vector<int> p = manacher(s);

    // Palindrom-palindrom berpusat sama bersarang: pusat dengan jari-jari p[i] memuat
    // palindrom sepanjang p[i], p[i] - 2, ..., sampai 1 atau 2, yaitu (p[i] + 1) / 2 buah.
    long long total = 0;   // bisa mencapai N(N+1)/2 ≈ 1,25 · 10^11
    for (int x : p) total += (x + 1) / 2;
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => $manJs.<<<'CODE'
const s = readLine().trim();
const p = manacher(s);
// pusat dengan jari-jari p[i] memuat (p[i] + 1) / 2 palindrom bersarang
let total = 0; // ≤ 1,25 · 10^11, masih tepat untuk Number
for (let i = 0; i < p.length; i++) total += (p[i] + 1) >> 1;
console.log(total);
CODE,
            'python' => $manPy.<<<'CODE'
def main():
    s = sys.stdin.readline().strip()
    p = manacher(s)
    # pusat dengan jari-jari p[i] memuat (p[i] + 1) // 2 palindrom bersarang
    print(sum((x + 1) // 2 for x in p))


main()
CODE,
        ],
        'editorial' => '<p>Mencoba semua O(N<sup>2</sup>) potongan dan memeriksa masing-masing butuh O(N<sup>3</sup>). <strong>Ekspansi dari pusat</strong> lebih baik: dari setiap pusat (sebuah huruf, atau celah di antara dua huruf), perlebar ke kiri dan ke kanan selama hurufnya sama; setiap pelebaran yang berhasil adalah satu palindrom baru. Namun pada string <code>aaa…a</code> banyaknya palindrom sekitar N<sup>2</sup>/2, sehingga ekspansi tetap memakan ≈ 1,25 · 10<sup>11</sup> langkah.</p>
<p>Kuncinya, palindrom-palindrom yang berpusat sama <strong>bersarang</strong>: membuang huruf pertama dan terakhir sebuah palindrom tetap menghasilkan palindrom. Jadi cukup ketahui palindrom <em>terpanjang</em> di setiap pusat. Dengan Manacher versi pemisah (<code>t = "#s0#s1#…#"</code>), <code>p[i]</code> adalah panjang palindrom terpanjang di s yang berpusat di posisi i pada t. Pusat itu memuat palindrom sepanjang p[i], p[i] − 2, …, sampai 1 (pusat huruf) atau 2 (pusat <code>#</code>), yaitu tepat <code>(p[i] + 1)/2</code> buah. Setiap palindrom punya tepat satu pusat, sehingga</p>
<p style="text-align:center"><code>jawaban = Σ (p[i] + 1)/2</code></p>
<p>Manacher menghitung seluruh p dalam O(N): simpan "kotak" [l, r], yaitu palindrom dengan ujung kanan paling jauh. Untuk i di dalam kotak, cerminannya j = l + r − i sudah dihitung, sehingga p[i] paling sedikit min(p[j], r − i); sisanya diperluas manual, dan setiap perluasan yang berhasil menggeser r ke kanan.</p>
<p><strong>Awas overflow:</strong> jawaban bisa mencapai N(N + 1)/2 ≈ 1,25 · 10<sup>11</sup>, jadi pakai <code>long long</code> di C++. Number di JavaScript masih tepat sampai 9 · 10<sup>15</sup>.</p>',
        'hints' => [
            'Setiap palindrom punya pusat: sebuah huruf (panjang ganjil) atau celah di antara dua huruf (panjang genap). Coba hitung palindrom per pusat.',
            'Jika palindrom terpanjang di suatu pusat sepanjang L, maka di pusat itu juga ada palindrom sepanjang L − 2, L − 4, …, karena membuang huruf di kedua ujung palindrom tetap menghasilkan palindrom.',
            'Hitung p dengan Manacher dalam O(N); setiap pusat menyumbang (p[i] + 1)/2 palindrom. Jumlahkan dengan long long.',
        ],
    ],

    [
        'slug' => 'dugaan-palindrom-prasasti',
        'lesson' => 'palindrom',
        'title' => 'Dugaan Sang Arkeolog',
        'difficulty' => 'Sedang',
        'tags' => ['string', 'palindrom', 'manacher', 'kueri'],
        'statement' => '<p>Seorang arkeolog menemukan prasasti panjang bertuliskan untaian <strong>N</strong> huruf tanpa spasi, yaitu string <strong>S</strong>. Ia curiga juru tulis kuno sengaja menyelipkan kata-kata cermin (palindrom) di dalamnya, lalu mencatat <strong>Q</strong> dugaan. Dugaan ke-i berbunyi: "potongan dari huruf ke-l sampai huruf ke-r adalah palindrom".</p>
<p>Bantu ia memeriksa setiap dugaan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi S. Q baris berikutnya masing-masing berisi <code>l r</code>.</p>',
        'output_format' => '<p>Untuk setiap dugaan, cetak <code>YA</code> jika potongan S[l..r] palindrom atau <code>TIDAK</code> jika bukan, masing-masing pada satu baris.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ Q ≤ 200 000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code></li><li>1 ≤ l ≤ r ≤ N</li></ul>',
        'samples' => [
            ['input' => "10 7\nkatakkodok\n1 5\n6 10\n5 6\n1 10\n2 4\n3 6\n4 7\n", 'explanation' => 'Potongan <code>katak</code> (1..5), <code>kodok</code> (6..10), <code>kk</code> (5..6), dan <code>ata</code> (2..4) adalah palindrom. Seluruh string jika dibalik menjadi <code>kodokkatak</code>, sedangkan <code>takk</code> (3..6) dan <code>akko</code> (4..7) jika dibalik menjadi <code>kkat</code> dan <code>okka</code>.'],
        ],
        'tests' => function () use ($AZ, $manPal, $gabungPal, $fibo) {
            // Campuran dugaan: palindrom di pusat acak (YA), satu huruf lebih panjang dari palindrom
            // terpanjang di pusatnya (TIDAK), dan potongan acak pendek/panjang.
            $kueri = function (string $s, int $q) use ($manPal): array {
                $n = strlen($s);
                $p = $manPal($s);
                $qs = [];
                for ($i = 0; $i < $q; $i++) {
                    $t = mt_rand(1, 100);
                    $L = -1;
                    $R = -1;
                    $c = mt_rand(1, 2 * $n - 1);   // pusat di t: ganjil = huruf, genap = celah
                    if ($t <= 70 && $p[$c] > 0) {
                        if ($t <= 45) {
                            $len = mt_rand(0, 1) ? $p[$c] : $p[$c] - 2 * mt_rand(0, intdiv($p[$c] - 1, 2));
                        } else {
                            $len = $p[$c] + 2;
                        }
                        $L = intdiv($c - $len, 2);
                        $R = $L + $len - 1;
                        if ($L < 0 || $R >= $n) {
                            $L = -1;
                        }
                    }
                    if ($L < 0) {
                        $L = mt_rand(0, $n - 1);
                        $R = min($n - 1, $L + (mt_rand(0, 1) ? mt_rand(0, 10) : mt_rand(0, $n)));
                    }
                    $qs[] = [$L + 1, $R + 1];
                }

                return $qs;
            };
            $format = function (string $s, array $qs): string {
                $rows = [strlen($s).' '.count($qs), $s];
                foreach ($qs as $x) {
                    $rows[] = $x[0].' '.$x[1];
                }

                return implode("\n", $rows)."\n";
            };
            $N = 200000;

            $kecil = T::word(10, 'ab');
            $semua = [];
            for ($l = 1; $l <= 10; $l++) {
                for ($r = $l; $r <= 10; $r++) {
                    $semua[] = [$l, $r];
                }
            }

            // Hampir semuanya 'a': dugaan panjang membuat pengecekan huruf demi huruf terlalu lambat.
            $satuB = str_repeat('a', $N);
            $satuB[mt_rand(0, $N - 1)] = 'b';

            // Satu palindrom raksasa, ditambah dugaan yang simetris terhadap tengahnya.
            $separuh = T::word($N / 2, 'ab');
            $raksasa = $separuh.strrev($separuh);
            $qsR = $kueri($raksasa, 38000);
            for ($i = 0; $i < 2000; $i++) {
                $l = mt_rand(1, $N / 2);
                $qsR[] = mt_rand(0, 3) ? [$l, $N + 1 - $l] : [$l, $N - $l];
            }
            T::shuffle($qsR);

            return [
                $format('a', [[1, 1]]),
                $format('ab', [[1, 1], [1, 2], [2, 2]]),
                $format($kecil, $semua),
                $format($s = T::word(1000, 'ab'), $kueri($s, 3000)),
                $format($satuB, $kueri($satuB, 130000)),
                $format($s = T::word($N, $AZ), $kueri($s, 40000)),
                $format($s = $gabungPal($N, 'abc', 500), $kueri($s, 40000)),
                $format($raksasa, $qsR),
                $format($s = $fibo($N), $kueri($s, 40000)),
                $format($s = str_repeat('ab', $N / 2), $kueri($s, 40000)),
            ];
        },
        'solve' => function (string $input) use ($manPal) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $p = $manPal(trim($lines[1]));
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$l, $r] = T::ints($lines[2 + $i]);
                // indeks 0: a = l - 1, b = r - 1, pusat di t[a + b + 1] = t[l + r - 1]
                $out[] = $p[$l + $r - 1] >= $r - $l + 1 ? 'YA' : 'TIDAK';
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    string s;\n    cin >> n >> q >> s;\n    for (int i = 0; i < q; i++) {\n        int l, r;\n        cin >> l >> r;\n    }", "const [n, q] = readInts();\nconst s = readLine().trim();\nfor (let i = 0; i < q; i++) {\n  const [l, r] = readInts();\n}", "data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\ns = data[2].decode()\nkueri = list(map(int, data[3:3 + 2 * q]))   # l r berurutan"),
        'solutions' => [
            'cpp' => $manCpp.<<<'CODE'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    string s;
    cin >> n >> q >> s;
    vector<int> p = manacher(s);

    string out;
    for (int i = 0; i < q; i++) {
        int l, r;
        cin >> l >> r;
        int a = l - 1, b = r - 1;               // indeks 0
        // Pusat s[a..b] ada di t[a + b + 1]. Palindrom berpusat sama bersarang, jadi s[a..b]
        // palindrom tepat ketika palindrom terpanjang di pusat itu minimal sepanjang b - a + 1.
        bool pal = p[a + b + 1] >= b - a + 1;
        out += pal ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => $manJs.<<<'CODE'
const [n, q] = readInts();
const s = readLine().trim();
const p = manacher(s);

const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  const a = l - 1, b = r - 1; // indeks 0
  // pusat s[a..b] ada di t[a + b + 1]; cukup cek jari-jarinya
  out.push(p[a + b + 1] >= b - a + 1 ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => $manPy.<<<'CODE'
def main():
    data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)
    n, q = int(data[0]), int(data[1])
    s = data[2].decode()
    p = manacher(s)

    it = iter(map(int, data[3:3 + 2 * q]))
    out = []
    for l, r in zip(it, it):
        # indeks 0: a = l - 1, b = r - 1; pusat s[a..b] ada di t[a + b + 1] = t[l + r - 1]
        out.append("YA" if p[l + r - 1] >= r - l + 1 else "TIDAK")
    print("\n".join(out))


main()
CODE,
        ],
        'editorial' => '<p>Memeriksa setiap dugaan huruf demi huruf butuh O(r − l) per dugaan. Pada string seperti <code>aaa…a</code> dengan dugaan-dugaan panjang, totalnya mencapai puluhan miliar langkah.</p>
<p>Pakai Manacher versi pemisah: <code>t = "#s0#s1#…#"</code>, dan <code>p[i]</code> = panjang palindrom terpanjang di s yang berpusat di t[i]. Huruf s[x] berada di t[2x + 1], sehingga potongan s[a..b] (indeks 0) menempati t[2a + 1 .. 2b + 1] dan pusatnya di <code>t[a + b + 1]</code>, baik panjangnya ganjil maupun genap.</p>
<p>Palindrom-palindrom berpusat sama <strong>bersarang</strong>, jadi s[a..b] palindrom <strong>jika dan hanya jika</strong> palindrom terpanjang di pusat itu cukup lebar untuk menutupinya:</p>
<p style="text-align:center"><code>p[a + b + 1] ≥ b − a + 1</code></p>
<p>Hitung p sekali dalam O(N), lalu setiap dugaan dijawab dalam O(1). Total O(N + Q).</p>
<p>Soal yang mirip bisa juga diselesaikan dengan hashing maju-mundur, tetapi Manacher memberi jawaban yang pasti benar tanpa risiko tabrakan hash. Hati-hati mengubah l dan r yang dimulai dari 1 ke indeks 0: dengan l dan r asli, pusatnya di t[l + r − 1].</p>',
        'hints' => [
            'Setiap potongan punya tepat satu pusat. Jika potongan itu palindrom, ia termasuk palindrom-palindrom yang berpusat di sana.',
            'Palindrom berpusat sama saling bersarang: s[a..b] palindrom jika dan hanya jika palindrom terpanjang di pusatnya setidaknya sepanjang b − a + 1.',
            'Dengan t = "#s0#s1#…#" dan p dari Manacher, pusat s[a..b] (indeks 0) adalah t[a + b + 1]. Jawab YA jika p[a + b + 1] ≥ b − a + 1.',
        ],
    ],

    [
        'slug' => 'lengkapi-palindrom-akhir',
        'lesson' => 'palindrom',
        'title' => 'Papan Nama Simetris',
        'difficulty' => 'Sedang',
        'tags' => ['string', 'palindrom', 'manacher', 'akhiran'],
        'statement' => '<p>Pak Joko ingin papan nama tokonya terbaca sama dari kiri maupun dari kanan. Tulisan di papan sekarang adalah string <strong>S</strong>. Huruf-huruf yang sudah terpasang tidak bisa dilepas atau dipindah, tetapi ia boleh <strong>menambahkan huruf baru di ujung kanan</strong> papan.</p>
<p>Berapa huruf paling sedikit yang perlu ditambahkan agar seluruh tulisan menjadi palindrom?</p>',
        'input_format' => '<p>Satu baris berisi string <code>S</code>.</p>',
        'output_format' => '<p>Banyak huruf paling sedikit yang perlu ditambahkan.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 500 000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code>.</li></ul>',
        'samples' => [
            ['input' => "kasur\n", 'explanation' => 'Akhiran palindrom terpanjang dari <code>kasur</code> hanya <code>r</code>, jadi empat huruf sebelumnya harus dicerminkan: tambahkan <code>usak</code> sehingga menjadi <code>kasurusak</code>.'],
            ['input' => "abacab\n", 'explanation' => 'Akhiran <code>bacab</code> sudah palindrom, jadi cukup tambahkan <code>a</code>: <code>abacaba</code>.'],
            ['input' => "level\n", 'explanation' => 'Tulisannya sudah palindrom, tidak perlu menambah huruf.'],
        ],
        'tests' => function () use ($AZ, $gabungPal, $fibo) {
            $N = 500000;
            // Palindrom raksasa berhuruf a/b, lalu diberi awalan acak dari huruf lain.
            $separuh = T::word(249500, 'ab');
            $awalanPal = T::word(1000, 'xyz').$separuh.strrev($separuh);
            // Palindrom raksasa yang dirusak satu hurufnya di dekat awal.
            $separuh = T::word($N / 2, 'ab');
            $rusak = $separuh.strrev($separuh);
            $rusak[mt_rand(100, 5000)] = 'c';

            return [
                "a\n", "ab\n", "aab\n", T::word(10, 'ab')."\n", T::word(1000, 'ab')."\n",
                // a^k b a^m: memeriksa setiap akhiran langsung butuh O(N^2)
                str_repeat('a', 299999).'b'.str_repeat('a', 200000)."\n",
                T::word($N, $AZ)."\n",
                $awalanPal."\n",
                $rusak."\n",
                str_repeat('ab', $N / 2)."\n",
                $fibo($N)."\n",
                $gabungPal($N, 'ab', 50000)."\n",
                str_repeat('a', $N)."\n",
            ];
        },
        'solve' => function (string $input) use ($manPal) {
            $s = trim(T::lines($input)[0]);
            $n = strlen($s);
            $best = 0;
            foreach ($manPal($s) as $i => $x) {
                if ($i + $x === 2 * $n) {
                    $best = max($best, $x);
                }
            }

            return (string) ($n - $best);
        },
        'starter' => $st(...$bacaS),
        'solutions' => [
            'cpp' => $manCpp.<<<'CODE'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int n = s.size();
    vector<int> p = manacher(s);   // |p| = 2n + 1

    // Akhiran palindrom terpanjang: palindrom terpanjang di pusat i menempati t[i-p[i] .. i+p[i]],
    // dan menjadi akhiran tepat ketika menyentuh ujung kanan t, yaitu i + p[i] = 2n.
    int akhiran = 0;
    for (int i = 0; i < (int)p.size(); i++)
        if (i + p[i] == 2 * n) akhiran = max(akhiran, p[i]);
    // Huruf sebelum akhiran itu harus dicerminkan di belakang.
    cout << n - akhiran << '\n';
    return 0;
}
CODE,
            'javascript' => $manJs.<<<'CODE'
const s = readLine().trim();
const n = s.length;
const p = manacher(s); // panjang 2n + 1

// akhiran palindrom terpanjang = palindrom terpanjang suatu pusat yang menyentuh ujung kanan t
let akhiran = 0;
for (let i = 0; i < p.length; i++) {
  if (i + p[i] === 2 * n && p[i] > akhiran) akhiran = p[i];
}
console.log(n - akhiran);
CODE,
            'python' => $manPy.<<<'CODE'
def main():
    s = sys.stdin.readline().strip()
    n = len(s)
    p = manacher(s)                          # panjang 2n + 1

    # akhiran palindrom terpanjang = palindrom terpanjang suatu pusat yang menyentuh ujung kanan t
    akhiran = 0
    for i, x in enumerate(p):
        if i + x == 2 * n and x > akhiran:
            akhiran = x
    print(n - akhiran)


main()
CODE,
        ],
        'editorial' => '<p>Misalkan kita menambahkan k huruf X di akhir sehingga S + X palindrom. Huruf pertama S harus berpasangan dengan huruf terakhir X, huruf kedua S dengan huruf kedua terakhir X, dan seterusnya, jadi X pasti kebalikan dari k huruf pertama S. Huruf-huruf S yang tersisa, yaitu akhiran S[k..N−1], berpasangan di antara mereka sendiri sehingga akhiran itu harus palindrom. Sebaliknya, jika S[k..N−1] palindrom, menambahkan kebalikan S[0..k−1] pasti menghasilkan palindrom.</p>
<p>Jadi jawabannya <strong>N − (panjang akhiran palindrom terpanjang)</strong>. Akhiran satu huruf selalu palindrom, sehingga jawabannya paling banyak N − 1.</p>
<p>Dengan Manacher versi pemisah (<code>t = "#s0#s1#…#"</code>, panjang 2N + 1), palindrom terpanjang di pusat i menempati t[i − p[i] .. i + p[i]] dan panjangnya di s adalah p[i]. Palindrom itu merupakan akhiran S tepat ketika menyentuh ujung kanan t: <code>i + p[i] = 2N</code>. Palindrom lain di pusat yang sama lebih pendek, jadi tidak mungkin menyentuh ujung jika yang terpanjang pun tidak. Ambil p[i] terbesar di antara pusat-pusat itu; total O(N).</p>
<p>Cara lain memakai fungsi prefiks KMP: panjang akhiran palindrom terpanjang S sama dengan nilai fungsi prefiks di posisi terakhir string <code>kebalikan(S) + "#" + S</code>. Memeriksa setiap akhiran secara langsung butuh O(N<sup>2</sup>) pada string seperti <code>aa…abaa…a</code>.</p>',
        'hints' => [
            'Jika kamu menambahkan k huruf, huruf-huruf itu harus berpasangan dengan k huruf pertama S. Apa syarat untuk sisa S?',
            'Sisa S, yaitu sebuah akhiran, harus sudah palindrom. Jadi yang dicari adalah akhiran palindrom terpanjang.',
            'Dengan p dari Manacher (t = "#s0#s1#…#"), pusat i memberi akhiran palindrom sepanjang p[i] jika i + p[i] = 2N. Jawabannya N dikurangi yang terpanjang.',
        ],
    ],

    [
        'slug' => 'potong-pita-palindrom',
        'lesson' => 'palindrom',
        'title' => 'Menggunting Pita Hiasan',
        'difficulty' => 'Sulit',
        'tags' => ['string', 'palindrom', 'manacher', 'dp'],
        'statement' => '<p>Bu Sari punya pita panjang bertuliskan untaian huruf <strong>S</strong>. Untuk hiasan pesta, ia ingin menggunting pita itu menjadi beberapa potongan (tanpa mengubah urutan huruf) sehingga <strong>setiap potongan adalah palindrom</strong>. Potongan sepanjang satu huruf diperbolehkan.</p>
<p>Berapa potongan paling sedikit yang bisa ia dapatkan?</p>',
        'input_format' => '<p>Satu baris berisi string <code>S</code>.</p>',
        'output_format' => '<p>Banyak potongan paling sedikit.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 4000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code>.</li></ul>',
        'samples' => [
            ['input' => "bananas\n", 'explanation' => 'Gunting menjadi <code>b | anana | s</code>. Dua potongan tidak cukup: satu-satunya awalan palindrom adalah <code>b</code>, dan sisanya, <code>ananas</code>, bukan palindrom.'],
            ['input' => "abcd\n", 'explanation' => 'Tidak ada potongan sepanjang 2 atau lebih yang palindrom, jadi setiap huruf menjadi satu potongan.'],
        ],
        'tests' => function () use ($AZ, $gabungPal, $fibo, $blokA) {
            $N = 4000;

            return [
                "a\n", "ab\n", "abcba\n", T::word(12, 'ab')."\n", T::word(200, 'abc')."\n",
                str_repeat('a', $N)."\n",
                T::word($N, $AZ)."\n",
                T::word($N, 'ab')."\n",
                $gabungPal($N, 'ab', 300)."\n",
                str_repeat('ab', $N / 2)."\n",
                str_repeat('a', 1999).'b'.str_repeat('a', 2000)."\n",
                $blokA($N, 60)."\n",
                $fibo($N)."\n",
            ];
        },
        'solve' => function (string $input) use ($manPal) {
            // Referensi: kunjungi setiap palindrom lewat pusatnya di t (dari kiri ke kanan).
            $s = trim(T::lines($input)[0]);
            $n = strlen($s);
            $p = $manPal($s);
            $dp = array_fill(0, $n + 1, $n + 1);
            $dp[0] = 0;
            foreach ($p as $i => $x) {
                // palindrom sepanjang x, x-2, ...: s[(i-len)/2 .. (i+len)/2 - 1]
                for ($len = $x; $len > 0; $len -= 2) {
                    $a = intdiv($i - $len, 2);
                    $b = intdiv($i + $len, 2);
                    if ($dp[$a] + 1 < $dp[$b]) {
                        $dp[$b] = $dp[$a] + 1;
                    }
                }
            }

            return (string) $dp[$n];
        },
        'starter' => $st(...$bacaS),
        'solutions' => [
            'cpp' => $manCpp.<<<'CODE'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int n = s.size();
    vector<int> p = manacher(s);

    // s[a..b] palindrom  <=>  p[a + b + 1] >= b - a + 1   (cek O(1))
    // dp[j] = potongan paling sedikit untuk awalan s[0..j-1]; potongan terakhir s[i..j-1]
    vector<int> dp(n + 1, n + 1);
    dp[0] = 0;
    for (int j = 1; j <= n; j++)
        for (int i = 0; i < j; i++)
            if (dp[i] + 1 < dp[j] && p[i + j] >= j - i) dp[j] = dp[i] + 1;
    cout << dp[n] << '\n';
    return 0;
}
CODE,
            'javascript' => $manJs.<<<'CODE'
const s = readLine().trim();
const n = s.length;
const p = manacher(s);

// s[a..b] palindrom  <=>  p[a + b + 1] >= b - a + 1   (cek O(1))
// dp[j] = potongan paling sedikit untuk awalan s[0..j-1]; potongan terakhir s[i..j-1]
const dp = new Int32Array(n + 1).fill(n + 1);
dp[0] = 0;
for (let j = 1; j <= n; j++) {
  for (let i = 0; i < j; i++) {
    if (dp[i] + 1 < dp[j] && p[i + j] >= j - i) dp[j] = dp[i] + 1;
  }
}
console.log(dp[n]);
CODE,
            'python' => $manPy.<<<'CODE'
def main():
    s = sys.stdin.readline().strip()
    n = len(s)
    p = manacher(s)

    # s[a..b] palindrom  <=>  p[a + b + 1] >= b - a + 1   (cek O(1))
    # dp[j] = potongan paling sedikit untuk awalan s[0..j-1]; potongan terakhir s[i..j-1]
    dp = [n + 1] * (n + 1)
    dp[0] = 0
    for j in range(1, n + 1):
        terbaik = n + 1
        for i in range(j):
            if dp[i] + 1 < terbaik and p[i + j] >= j - i:
                terbaik = dp[i] + 1
        dp[j] = terbaik
    print(dp[n])


main()
CODE,
        ],
        'editorial' => '<p>Misalkan <code>dp[j]</code> = banyak potongan paling sedikit untuk awalan s[0..j−1], dengan dp[0] = 0. Potongan terakhir awalan itu adalah suatu palindrom s[i..j−1], sehingga</p>
<p style="text-align:center"><code>dp[j] = min { dp[i] + 1 : s[i..j−1] palindrom }</code></p>
<p>Ada sekitar N<sup>2</sup>/2 ≈ 8 · 10<sup>6</sup> pasangan (i, j). Jika setiap pasangan diperiksa huruf demi huruf, totalnya O(N<sup>3</sup>), sekitar 5 · 10<sup>9</sup> langkah pada string <code>aaa…a</code> sepanjang 4000. Pemeriksaan palindrom harus dibuat O(1).</p>
<p>Di sinilah Manacher berguna. Dengan <code>t = "#s0#s1#…#"</code> dan p dari Manacher, pusat s[a..b] berada di t[a + b + 1], sehingga s[a..b] palindrom tepat ketika <code>p[a + b + 1] ≥ b − a + 1</code>. Untuk potongan s[i..j−1], syaratnya <code>p[i + j] ≥ j − i</code>. Total O(N<sup>2</sup>) dengan cek O(1), dan memeriksa <code>dp[i] + 1 &lt; dp[j]</code> lebih dulu melewati banyak pasangan yang tidak mungkin memperbaiki jawaban.</p>
<p>Alternatif lain: kunjungi setiap palindrom lewat pusatnya. Pusat i memuat palindrom sepanjang p[i], p[i] − 2, …, dan palindrom s[a..b] memberi transisi <code>dp[b+1] = min(dp[b+1], dp[a] + 1)</code>. Jika pusat diproses dari kiri ke kanan, dp[a] sudah final saat dipakai, karena semua palindrom yang berakhir di huruf a − 1 pusatnya lebih kiri. Banyak langkahnya sama dengan banyak substring palindrom: paling banyak N(N + 1)/2, dan jauh lebih sedikit untuk string acak.</p>
<p>Ingat: soal ini menanyakan banyak <strong>potongan</strong>. Banyak <em>guntingan</em> adalah satu lebih sedikit.</p>',
        'hints' => [
            'Pikirkan potongan terakhir. Jika potongan terakhir adalah s[i..j−1], sisa masalahnya adalah awalan sepanjang i.',
            'dp[j] = min(dp[i] + 1) untuk semua i dengan s[i..j−1] palindrom. Masalahnya, memeriksa palindrom huruf demi huruf membuat O(N³).',
            'Hitung p dengan Manacher sekali, lalu s[i..j−1] palindrom jika p[i + j] ≥ j − i. DP-nya menjadi O(N²) dengan cek O(1).',
        ],
    ],
];
