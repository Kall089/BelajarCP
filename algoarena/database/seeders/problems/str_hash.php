<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi String Hashing: prefix hash, hash potongan O(1), hash maju-mundur, binary search + hash.
 * Semua kode C++ harus lolos C++14.
 * Solusi referensi PHP sengaja TIDAK memakai hash agar jawaban tes pasti benar:
 * perbandingan langsung (substr_compare), Manacher, dan suffix automaton.
 * Solusi C++/Python memakai modulus 2^61 − 1; JavaScript memakai dua modulus prima < 2^26.
 * Python membaca seluruh input sekaligus (sys.stdin.buffer) karena input bisa 200 000 baris.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$abjad = 'abcdefghijklmnopqrstuvwxyz';

/** Awalan barisan Thue–Morse (abbabaab...) sepanjang n: menjebak hash yang hanya mengandalkan luapan 2^64. */
$thue = function (int $n): string {
    $s = '';
    for ($i = 0; $i < $n; $i++) {
        $s .= substr_count(decbin($i), '1') % 2 ? 'b' : 'a';
    }

    return $s;
};

/** Awalan kata Fibonacci (a, ab, aba, abaab, ...) sepanjang n: penuh potongan berulang. */
$fibo = function (int $n): string {
    $x = 'a';
    $y = 'ab';
    while (strlen($y) < $n) {
        [$x, $y] = [$y, $y.$x];
    }

    return substr($y, 0, $n);
};

/** String acak yang sengaja berisi salinan potongan sebelumnya. Hasil: [s, daftar [asal, tujuan, panjang]] (0-indexed). */
$salinan = function (int $n, string $huruf, int $maxSalin): array {
    $s = '';
    $daftar = [];
    while (strlen($s) < $n) {
        $cur = strlen($s);
        if ($cur >= 20 && mt_rand(0, 2) > 0) {
            $len = mt_rand(1, min($maxSalin, $cur, $n - $cur));
            $src = mt_rand(0, $cur - $len);
            $s .= substr($s, $src, $len);
            $daftar[] = [$src, $cur, $len];
        } else {
            $s .= T::word(min(mt_rand(1, 30), $n - $cur), $huruf);
        }
    }

    return [$s, $daftar];
};

/** Input berformat "N Q", S, lalu Q baris pertanyaan. */
$format = function (string $s, array $qs): string {
    $rows = [];
    foreach ($qs as $x) {
        $rows[] = implode(' ', $x);
    }

    return strlen($s).' '.count($qs)."\n$s\n".implode("\n", $rows)."\n";
};

/**
 * Manacher: d1[i] = jari-jari palindrom ganjil terpanjang berpusat di i (termasuk pusat),
 * d2[i] = jari-jari palindrom genap terpanjang yang berpusat di antara i − 1 dan i.
 */
$manacher = function (string $s): array {
    $n = strlen($s);
    $d1 = array_fill(0, $n, 0);
    $l = 0;
    $r = -1;
    for ($i = 0; $i < $n; $i++) {
        $k = $i > $r ? 1 : min($d1[$l + $r - $i], $r - $i + 1);
        while ($i - $k >= 0 && $i + $k < $n && $s[$i - $k] === $s[$i + $k]) {
            $k++;
        }
        $d1[$i] = $k;
        if ($i + $k - 1 > $r) {
            $l = $i - $k + 1;
            $r = $i + $k - 1;
        }
    }
    $d2 = array_fill(0, $n, 0);
    $l = 0;
    $r = -1;
    for ($i = 0; $i < $n; $i++) {
        $k = $i > $r ? 0 : min($d2[$l + $r - $i + 1], $r - $i + 1);
        while ($i - $k - 1 >= 0 && $i + $k < $n && $s[$i - $k - 1] === $s[$i + $k]) {
            $k++;
        }
        $d2[$i] = $k;
        if ($i + $k - 1 > $r) {
            $l = $i - $k;
            $r = $i + $k - 1;
        }
    }

    return [$d1, $d2];
};

/**
 * Suffix automaton (pasti benar, tanpa hash). Transisi disimpan rata: $nx[state * 26 + huruf].
 * Hasil: [len, link, nx].
 */
$sam = function (string $s): array {
    $len = [0];
    $link = [-1];
    $nx = [];
    $last = 0;
    $sz = 1;
    $n = strlen($s);
    for ($i = 0; $i < $n; $i++) {
        $c = ord($s[$i]) - 97;
        $cur = $sz++;
        $len[$cur] = $len[$last] + 1;
        $link[$cur] = 0;
        $p = $last;
        while ($p !== -1 && ! isset($nx[$p * 26 + $c])) {
            $nx[$p * 26 + $c] = $cur;
            $p = $link[$p];
        }
        if ($p !== -1) {
            $q = $nx[$p * 26 + $c];
            if ($len[$p] + 1 === $len[$q]) {
                $link[$cur] = $q;
            } else {
                $cl = $sz++;
                $len[$cl] = $len[$p] + 1;
                $link[$cl] = $link[$q];
                for ($d = 0; $d < 26; $d++) {
                    if (isset($nx[$q * 26 + $d])) {
                        $nx[$cl * 26 + $d] = $nx[$q * 26 + $d];
                    }
                }
                while ($p !== -1 && ($nx[$p * 26 + $c] ?? -1) === $q) {
                    $nx[$p * 26 + $c] = $cl;
                    $p = $link[$p];
                }
                $link[$q] = $cl;
                $link[$cur] = $cl;
            }
        }
        $last = $cur;
    }

    return [$len, $link, $nx];
};

/**
 * Pertanyaan "a b c d" berpanjang sama. Jika $geser tidak kosong, sering c = a + k · g (g dari $geser)
 * agar banyak jawaban YA pada string berpola. Sesekali panjang potongan kedua digeser sedikit.
 */
$kueriAcak = function (int $n, int $q, int $maxLen, array $geser): array {
    $qs = [];
    for ($t = 0; $t < $q; $t++) {
        $len = mt_rand(1, min($n, $maxLen));
        $a = mt_rand(1, $n - $len + 1);
        $c = mt_rand(1, $n - $len + 1);
        if ($geser && mt_rand(0, 3) > 0) {
            $g = $geser[mt_rand(0, count($geser) - 1)];
            $c = $a + $g * mt_rand(-intdiv($a - 1, $g), intdiv($n - $len + 1 - $a, $g));
        }
        $len2 = $len;
        if (mt_rand(0, 9) === 0) {
            $len2 = max(1, min($n - $c + 1, $len + mt_rand(-3, 3)));
        }
        $qs[] = [$a, $a + $len - 1, $c, $c + $len2 - 1];
    }

    return $qs;
};

/** Pertanyaan dari daftar salinan: potongan yang memang disalin (YA), kadang digeser satu posisi. */
$kueriSalin = function (int $n, int $q, array $daftar): array {
    $qs = [];
    for ($t = 0; $t < $q; $t++) {
        [$src, $dst, $len] = $daftar[mt_rand(0, count($daftar) - 1)];
        $off = mt_rand(0, $len - 1);
        $l = mt_rand(1, $len - $off);
        $a = $src + $off + 1;
        $c = $dst + $off + 1;
        if (mt_rand(0, 3) === 0) {
            $c = max(1, min($n - $l + 1, $c + (mt_rand(0, 1) ? 1 : -1)));
        }
        if (mt_rand(0, 1)) {
            [$a, $c] = [$c, $a];
        }
        $qs[] = [$a, $a + $l - 1, $c, $c + $l - 1];
    }

    return $qs;
};

/** Pertanyaan pada barisan Thue–Morse (n pangkat 2): membandingkan blok sejajar sepanjang 2^k. */
$kueriThue = function (int $n, int $q): array {
    $qs = [];
    $maxK = (int) log($n, 2) - 1;
    for ($t = 0; $t < $q; $t++) {
        $k = mt_rand(0, 3) ? mt_rand(min(10, $maxK), $maxK) : mt_rand(0, $maxK);
        $m = 1 << $k;
        $a = mt_rand(0, intdiv($n, $m) - 1) * $m + 1;
        $c = mt_rand(0, intdiv($n, $m) - 1) * $m + 1;
        $qs[] = [$a, $a + $m - 1, $c, $c + $m - 1];
    }

    return $qs;
};

/**
 * Pertanyaan "l r" untuk soal palindrom: sebagian besar tepat di sekitar palindrom terpanjang
 * (jari-jari ≤ maksimum memberi YA, maksimum + 1 memberi TIDAK), sisanya acak sepanjang ≤ $maxAcak.
 */
$kueriPal = function (string $s, int $q, int $maxAcak) use ($manacher): array {
    [$d1, $d2] = $manacher($s);
    $n = strlen($s);
    $qs = [];
    for ($t = 0; $t < $q; $t++) {
        $jenis = mt_rand(0, 9);
        if ($jenis < 4) {
            $i = mt_rand(0, $n - 1);
            $R = $d1[$i];
            $rad = mt_rand(0, 2) ? mt_rand(1, $R) : $R + 1;
            if ($i - $rad + 1 < 0 || $i + $rad - 1 >= $n) {
                $rad = $R;
            }
            $qs[] = [$i - $rad + 2, $i + $rad];
        } elseif ($jenis < 8 && $n > 1) {
            $i = mt_rand(1, $n - 1);
            $R = $d2[$i];
            $rad = ($R > 0 && mt_rand(0, 2)) ? mt_rand(1, $R) : $R + 1;
            if ($i - $rad < 0 || $i + $rad - 1 >= $n) {
                $rad = max(1, $R);
            }
            $qs[] = [$i - $rad + 1, $i + $rad];
        } else {
            $len = mt_rand(1, min($n, $maxAcak));
            $l = mt_rand(1, $n - $len + 1);
            $qs[] = [$l, $l + $len - 1];
        }
    }

    return $qs;
};

return [
    [
        'slug' => 'cek-potongan',
        'lesson' => 'string-hashing',
        'title' => 'Salinan di Naskah Lontar',
        'difficulty' => 'Mudah',
        'tags' => ['string', 'hashing', 'prefix hash'],
        'statement' => '<p>Bu Laras, seorang filolog, menyalin sebuah naskah lontar kuno menjadi satu untaian huruf <strong>S</strong> sepanjang <strong>N</strong> (tanpa spasi). Ia curiga para juru tulis zaman dulu sering menyalin ulang bagian yang sama di tempat lain.</p>
<p>Untuk memeriksanya, ia mengajukan <strong>Q</strong> pertanyaan. Setiap pertanyaan berisi empat angka <code>a b c d</code>: apakah potongan huruf ke-a sampai ke-b <strong>sama persis</strong> dengan potongan huruf ke-c sampai ke-d? Dua potongan dianggap sama jika panjangnya sama dan huruf-huruf pada posisi yang bersesuaian juga sama. Kedua potongan boleh tumpang-tindih.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi S. Q baris berikutnya masing-masing berisi <code>a b c d</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, cetak <code>YA</code> jika kedua potongan sama atau <code>TIDAK</code> jika berbeda, masing-masing pada satu baris.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ Q ≤ 200 000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code></li><li>1 ≤ a ≤ b ≤ N dan 1 ≤ c ≤ d ≤ N</li></ul>',
        'samples' => [
            ['input' => "11 5\nabrakadabra\n1 4 8 11\n1 3 6 8\n4 4 6 6\n1 4 8 10\n2 3 9 10\n", 'explanation' => 'Potongan 1..4 dan 8..11 sama-sama "abra". Potongan 1..3 adalah "abr", sedangkan 6..8 adalah "ada". Pertanyaan ketiga membandingkan "a" dengan "a". Pada pertanyaan keempat panjangnya berbeda (4 dan 3), jadi pasti TIDAK. Terakhir, 2..3 dan 9..10 sama-sama "br".'],
            ['input' => "5 3\nbabab\n1 3 3 5\n1 2 2 3\n2 4 1 3\n", 'explanation' => 'Potongan boleh tumpang-tindih: 1..3 dan 3..5 sama-sama "bab" (huruf ke-3 dipakai keduanya). "ba" berbeda dengan "ab", dan "aba" berbeda dengan "bab".'],
        ],
        'tests' => function () use ($abjad, $thue, $fibo, $salinan, $format, $kueriAcak, $kueriSalin, $kueriThue) {
            $N = 200000;
            $Q = 200000;

            $kecil = T::word(12, 'ab');
            $sedang = T::word(1000, 'ab');

            // Semua 'a' kecuali satu 'b' di tengah, ditambah beberapa pertanyaan tepi.
            $satuB = str_repeat('a', 100000).'b'.str_repeat('a', 99999);
            $tepi = [[1, $N, 1, $N], [1, $N, 1, $N - 1], [1, 99999, 100002, $N], [5, 5, 5, 5], [1, 100001, 100000, $N], [100001, 100001, 1, 1]];
            $qsB = array_merge($tepi, $kueriAcak($N, $Q - count($tepi), $N, [1]));

            $blok = T::word(7, 'abc');
            $periodik = substr(str_repeat($blok, intdiv($N, 7) + 1), 0, $N);

            [$sSalin, $daftar] = $salinan($N, $abjad, 3000);

            $fibs = [1, 2];
            while (end($fibs) < $N) {
                $fibs[] = $fibs[count($fibs) - 1] + $fibs[count($fibs) - 2];
            }
            array_pop($fibs);

            $tm = $thue(131072);
            $acakAB = T::word($N, 'ab');

            return [
                $format('a', [[1, 1, 1, 1]]),
                $format($kecil, $kueriAcak(12, 30, 6, [])),
                $format($sedang, $kueriAcak(1000, 2000, 8, [])),
                $format(str_repeat('a', $N), $kueriAcak($N, $Q, $N, [1])),
                $format($satuB, $qsB),
                $format($periodik, $kueriAcak($N, $Q, $N, [7])),
                $format($sSalin, $kueriSalin($N, $Q, $daftar)),
                $format($tm, $kueriThue(131072, $Q)),
                $format($fibo($N), $kueriAcak($N, $Q, 50000, $fibs)),
                $format($acakAB, $kueriAcak($N, $Q, 15, [])),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $s = trim($lines[1]);
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$a, $b, $c, $d] = T::ints($lines[2 + $i]);
                $len = $b - $a + 1;
                // Perbandingan langsung (pasti benar), bukan hash.
                $sama = $len === $d - $c + 1 && ($a === $c || substr_compare($s, substr($s, $c - 1, $len), $a - 1, $len) === 0);
                $out[] = $sama ? 'YA' : 'TIDAK';
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    string s;\n    cin >> n >> q >> s;\n    for (int i = 0; i < q; i++) {\n        int a, b, c, d;\n        cin >> a >> b >> c >> d;\n    }", "const [n, q] = readInts();\nconst s = readLine().trim();\nfor (let i = 0; i < q; i++) {\n  const [a, b, c, d] = readInts();\n}", "data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\ns = data[2]   # bytes: s[i] berupa kode ASCII huruf ke-i\nkueri = list(map(int, data[3:3 + 4 * q]))   # a b c d berurutan"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

typedef unsigned long long ull;
const ull MOD = (1ULL << 61) - 1;   // bilangan prima Mersenne
const ull BASIS = 911382323;

// a · b mod (2^61 − 1) memakai perkalian 128-bit
ull mulmod(ull a, ull b) {
    __uint128_t c = (__uint128_t)a * b;
    ull r = (ull)(c & MOD) + (ull)(c >> 61);   // 2^61 ≡ 1 (mod 2^61 − 1)
    return r >= MOD ? r - MOD : r;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    string s;
    cin >> n >> q >> s;

    // h[i] = hash dari i huruf pertama, pw[i] = BASIS^i
    vector<ull> h(n + 1, 0), pw(n + 1, 1);
    for (int i = 0; i < n; i++) {
        h[i + 1] = (mulmod(h[i], BASIS) + (ull)s[i]) % MOD;
        pw[i + 1] = mulmod(pw[i], BASIS);
    }
    // hash potongan s[l..r-1] (0-indexed, r tidak termasuk)
    auto ambil = [&](int l, int r) {
        return (h[r] + MOD - mulmod(h[l], pw[r - l])) % MOD;
    };

    string out;
    for (int i = 0; i < q; i++) {
        int a, b, c, d;
        cin >> a >> b >> c >> d;
        bool sama = (b - a == d - c) && ambil(a - 1, b) == ambil(c - 1, d);
        out += sama ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const s = readLine().trim();

// Dua modulus prima < 2^26: hasil kali dua nilai < 2^52, jadi tetap tepat di Number.
const M1 = 67108859, M2 = 67108837;
const B1 = 9737333, B2 = 5555521;
const h1 = new Int32Array(n + 1), h2 = new Int32Array(n + 1);
const p1 = new Int32Array(n + 1), p2 = new Int32Array(n + 1);
p1[0] = p2[0] = 1;
for (let i = 0; i < n; i++) {
  const c = s.charCodeAt(i);
  h1[i + 1] = (h1[i] * B1 + c) % M1;
  h2[i + 1] = (h2[i] * B2 + c) % M2;
  p1[i + 1] = (p1[i] * B1) % M1;
  p2[i + 1] = (p2[i] * B2) % M2;
}
// hash potongan s[l..r-1] (0-indexed, r tidak termasuk) untuk masing-masing modulus
const ambil1 = (l, r) => (h1[r] - ((h1[l] * p1[r - l]) % M1) + M1) % M1;
const ambil2 = (l, r) => (h2[r] - ((h2[l] * p2[r - l]) % M2) + M2) % M2;

const out = [];
for (let i = 0; i < q; i++) {
  const [a, b, c, d] = readInts();
  const sama = b - a === d - c &&
    ambil1(a - 1, b) === ambil1(c - 1, d) &&
    ambil2(a - 1, b) === ambil2(c - 1, d);
  out.push(sama ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()   # seluruh input sekaligus
    n, q = int(data[0]), int(data[1])
    s = data[2]                              # bytes: s[i] langsung berupa kode ASCII
    M = (1 << 61) - 1                        # prima Mersenne, tabrakan sangat jarang
    B = 911382323

    h = [0] * (n + 1)                        # h[i] = hash i huruf pertama
    pw = [1] * (n + 1)                       # pw[i] = B^i mod M
    for i in range(n):
        h[i + 1] = (h[i] * B + s[i]) % M
        pw[i + 1] = pw[i] * B % M

    it = iter(map(int, data[3:3 + 4 * q]))
    out = []
    for a, b, c, d in zip(it, it, it, it):
        if b - a != d - c:                   # panjang berbeda: pasti tidak sama
            out.append("TIDAK")
            continue
        p = pw[b - a + 1]
        # hash s[a..b] = h[b] - h[a-1] * B^(panjang)
        if (h[b] - h[a - 1] * p) % M == (h[d] - h[c - 1] * p) % M:
            out.append("YA")
        else:
            out.append("TIDAK")
    print("\n".join(out))


main()
CODE,
        ],
        'editorial' => '<p>Membandingkan huruf satu per satu memakan O(panjang) per pertanyaan. Dengan Q = 2 · 10<sup>5</sup> pertanyaan yang masing-masing bisa sepanjang 2 · 10<sup>5</sup>, totalnya 4 · 10<sup>10</sup> langkah. Kita perlu membandingkan dua potongan dalam O(1).</p>
<p><strong>Hash polinomial.</strong> Wakili string t<sub>1</sub>t<sub>2</sub>…t<sub>k</sub> dengan angka <code>t<sub>1</sub>·B<sup>k−1</sup> + t<sub>2</sub>·B<sup>k−2</sup> + … + t<sub>k</sub> (mod M)</code>. Dua string yang sama pasti punya hash sama; dua string berbeda hampir pasti punya hash berbeda bila M besar.</p>
<p><strong>Prefix hash.</strong> Simpan <code>h[i]</code> = hash i huruf pertama, dengan <code>h[i] = h[i−1]·B + s<sub>i</sub></code>, serta <code>pw[i] = B<sup>i</sup></code>. Hash potongan s[a..b] adalah</p>
<p style="text-align:center"><code>h[b] − h[a−1] · B<sup>b−a+1</sup> (mod M)</code></p>
<p>karena h[b] berisi h[a−1] yang sudah "digeser" sebanyak b − a + 1 posisi. Jadi setiap pertanyaan: jika panjang berbeda jawab TIDAK, selain itu bandingkan dua hash. Total O(N + Q).</p>
<p><strong>Pilih modulus dengan hati-hati.</strong> Untuk dua string berbeda sepanjang L, peluang tabrakan kira-kira L / M. Modulus 2<sup>61</sup> − 1 (prima Mersenne) membuat peluang itu sekitar 10<sup>−13</sup>; perkalian dua nilai 61-bit memakai <code>__uint128_t</code> di C++, sedangkan Python aman karena bilangan bulatnya tak terbatas. Di JavaScript, Number hanya tepat sampai 2<sup>53</sup>, jadi pakai dua modulus prima &lt; 2<sup>26</sup> (hasil kali &lt; 2<sup>52</sup>) dan bandingkan kedua hash.</p>
<p><strong>Jebakan:</strong> jangan hanya membiarkan <code>unsigned long long</code> meluap (modulo 2<sup>64</sup>). Barisan Thue–Morse <code>abbabaabbaababba…</code> membuat dua blok berbeda sepanjang 2048 punya hash 2<sup>64</sup> yang sama untuk basis ganjil mana pun, dan salah satu tes memakai string ini.</p>',
        'hints' => [
            'Membandingkan huruf satu per satu bisa memakan 200 000 langkah per pertanyaan. Bisakah setiap potongan diwakili oleh sebuah angka?',
            'Hitung prefix hash h[i] = h[i−1]·B + s[i] (mod M). Hash potongan s[a..b] bisa diperoleh dari h[b] dan h[a−1].',
            'hash(a..b) = h[b] − h[a−1]·B^(b−a+1) mod M. Cek panjangnya dulu, lalu bandingkan hash. Pakai modulus besar seperti 2^61 − 1 atau dua modulus sekaligus.',
        ],
    ],

    [
        'slug' => 'potongan-berbeda',
        'lesson' => 'string-hashing',
        'title' => 'Katalog Motif Tenun',
        'difficulty' => 'Sedang',
        'tags' => ['string', 'hashing', 'himpunan'],
        'statement' => '<p>Seorang penenun di Sumba mencatat urutan warna benang pada selembar kain panjang sebagai string <strong>S</strong> sepanjang <strong>N</strong>; setiap huruf kecil mewakili satu warna. Ia ingin membuat katalog motif: setiap <strong>L</strong> benang berurutan pada kain dianggap satu motif.</p>
<p>Ada N − L + 1 motif yang bisa diambil, tetapi banyak yang kembar. Motif yang sama persis cukup dicatat sekali. Berapa banyak motif <strong>berbeda</strong> yang masuk katalog?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N L</code>. Baris kedua berisi S.</p>',
        'output_format' => '<p>Banyak potongan berbeda sepanjang L di dalam S.</p>',
        'constraints' => '<ul><li>1 ≤ L ≤ N ≤ 200 000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code></li></ul>',
        'samples' => [
            ['input' => "6 2\nbanana\n", 'explanation' => 'Motif sepanjang 2: ba, an, na, an, na. Yang berbeda hanya "ba", "an", dan "na".'],
            ['input' => "10 4\nabcabcabcd\n", 'explanation' => 'Ada 7 motif: abca, bcab, cabc, abca, bcab, cabc, abcd. Tiga motif pertama muncul lagi, sehingga yang berbeda hanya abca, bcab, cabc, dan abcd.'],
        ],
        'tests' => function () use ($abjad, $thue, $fibo, $salinan) {
            $f = fn (string $s, int $L) => strlen($s)." $L\n$s\n";
            $N = 200000;
            $blok = T::word(997, $abjad);

            return [
                $f('z', 1),
                $f('abcde', 5),
                $f(T::word(12, 'ab'), 3),
                $f(str_repeat('a', $N), 1000),
                $f(T::word($N, $abjad), 1),
                $f(T::word($N, 'ab'), 10),
                $f(T::word($N, 'ab'), 17),
                $f(T::word($N, $abjad), 60000),
                $f(substr(str_repeat($blok, 201), 0, $N), 500),
                $f($fibo($N), 1234),
                $f($thue(131072), 2048),
                $f($salinan($N, 'abc', 3000)[0], 25),
                $f($salinan($N, $abjad, 20000)[0], 5000),
            ];
        },
        'solve' => function (string $input) use ($sam) {
            $lines = T::lines($input);
            [$n, $L] = T::ints($lines[0]);
            // Suffix automaton: setiap state v mewakili tepat satu potongan berbeda untuk setiap
            // panjang di (len[link[v]], len[v]]. Hitung state yang rentangnya memuat L.
            [$len, $link] = $sam(trim($lines[1]));
            $cnt = 0;
            for ($v = 1, $k = count($len); $v < $k; $v++) {
                if ($len[$link[$v]] < $L && $L <= $len[$v]) {
                    $cnt++;
                }
            }

            return (string) $cnt;
        },
        'starter' => $st("    int n, L;\n    string s;\n    cin >> n >> L >> s;", "const [n, L] = readInts();\nconst s = readLine().trim();", "n, L = map(int, input().split())\ns = input().strip()"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

typedef unsigned long long ull;
const ull MOD = (1ULL << 61) - 1;   // bilangan prima Mersenne
const ull BASIS = 911382323;

ull mulmod(ull a, ull b) {
    __uint128_t c = (__uint128_t)a * b;
    ull r = (ull)(c & MOD) + (ull)(c >> 61);
    return r >= MOD ? r - MOD : r;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, L;
    string s;
    cin >> n >> L >> s;

    vector<ull> h(n + 1, 0), pw(n + 1, 1);
    for (int i = 0; i < n; i++) {
        h[i + 1] = (mulmod(h[i], BASIS) + (ull)s[i]) % MOD;
        pw[i + 1] = mulmod(pw[i], BASIS);
    }

    // Hash setiap motif s[i..i+L-1] dalam O(1), lalu buang yang kembar.
    vector<ull> motif;
    motif.reserve(n - L + 1);
    for (int i = 0; i + L <= n; i++)
        motif.push_back((h[i + L] + MOD - mulmod(h[i], pw[L])) % MOD);
    sort(motif.begin(), motif.end());
    int beda = unique(motif.begin(), motif.end()) - motif.begin();
    cout << beda << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, L] = readInts();
const s = readLine().trim();

// Dua modulus prima < 2^26 (hasil kali < 2^52, tepat di Number).
const M1 = 67108859, M2 = 67108837;
const B1 = 9737333, B2 = 5555521;
const h1 = new Int32Array(n + 1), h2 = new Int32Array(n + 1);
let q1 = 1, q2 = 1; // B1^L dan B2^L
for (let i = 0; i < n; i++) {
  const c = s.charCodeAt(i);
  h1[i + 1] = (h1[i] * B1 + c) % M1;
  h2[i + 1] = (h2[i] * B2 + c) % M2;
}
for (let i = 0; i < L; i++) {
  q1 = (q1 * B1) % M1;
  q2 = (q2 * B2) % M2;
}

const motif = new Set();
for (let i = 0; i + L <= n; i++) {
  const x1 = (h1[i + L] - ((h1[i] * q1) % M1) + M1) % M1;
  const x2 = (h2[i + L] - ((h2[i] * q2) % M2) + M2) % M2;
  motif.add(x1 * 67108864 + x2); // gabungkan dua hash menjadi satu angka < 2^52
}
console.log(motif.size);
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()
    n, L = int(data[0]), int(data[1])
    s = data[2]                      # bytes: s[i] berupa kode ASCII
    M = (1 << 61) - 1                # modulus besar: 2·10^5 hash tidak akan bertabrakan
    B = 911382323

    h = [0] * (n + 1)
    x = 0
    for i in range(n):
        x = (x * B + s[i]) % M
        h[i + 1] = x
    p = pow(B, L, M)

    # pasangan (h[i+L], h[i]) untuk setiap awal motif i = 0..n-L
    motif = {(y - z * p) % M for y, z in zip(h[L:], h)}
    print(len(motif))


main()
CODE,
        ],
        'editorial' => '<p>Ada N − L + 1 motif. Menyimpan setiap motif sebagai string di dalam set butuh O(N · L) waktu dan memori, terlalu besar ketika L sekitar 10<sup>5</sup>. Wakili setiap motif dengan hash-nya saja.</p>
<p>Dengan prefix hash, hash motif yang mulai di posisi i adalah <code>h[i+L] − h[i] · B<sup>L</sup> (mod M)</code>, O(1) per motif. Kumpulkan semua hash, lalu hitung yang berbeda: masukkan ke set, atau urutkan lalu buang duplikat. Total O(N log N).</p>
<p><strong>Waspadai paradoks ulang tahun.</strong> Di soal ini kita tidak membandingkan satu pasang, melainkan <em>semua</em> pasang dari 2 · 10<sup>5</sup> hash, yaitu sekitar 2 · 10<sup>10</sup> pasang. Dengan satu modulus sekitar 10<sup>9</sup>, diharapkan ada puluhan pasang yang bertabrakan, sehingga jawaban menjadi terlalu kecil. Modulus 2<sup>61</sup> − 1 (peluang tabrakan sekitar 10<sup>−8</sup>) atau pasangan dua modulus (digabung menjadi satu angka, seperti pada solusi JavaScript) cukup aman.</p>
<p>Soal ini juga bisa diselesaikan tanpa hash, misalnya dengan suffix array atau suffix automaton, tetapi hashing jauh lebih singkat ditulis.</p>',
        'hints' => [
            'Menyimpan setiap motif sebagai string bisa memakan N · L memori. Wakili setiap motif dengan hash-nya.',
            'Dengan prefix hash, hash setiap potongan sepanjang L didapat dalam O(1). Masukkan semuanya ke set, atau urutkan lalu buang duplikat.',
            'Ada sekitar 2·10^10 pasang hash yang bisa bertabrakan. Modulus sekitar 10^9 tidak cukup; pakai 2^61 − 1 atau gabungan dua modulus.',
        ],
    ],

    [
        'slug' => 'kueri-palindrom',
        'lesson' => 'string-hashing',
        'title' => 'Arsip Kata Cermin',
        'difficulty' => 'Sedang',
        'tags' => ['string', 'hashing', 'palindrom'],
        'statement' => '<p>Klub Kata Cermin mengoleksi palindrom, yaitu kata yang terbaca sama dari depan maupun dari belakang, seperti <code>katak</code>, <code>malam</code>, atau <code>kasurrusak</code>. Seluruh koleksi mereka tersimpan sebagai satu untaian huruf <strong>S</strong> sepanjang <strong>N</strong>.</p>
<p>Para anggota mengajukan <strong>Q</strong> pertanyaan. Setiap pertanyaan berisi <code>l r</code>: apakah potongan S dari huruf ke-l sampai ke-r merupakan palindrom?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi S. Q baris berikutnya masing-masing berisi <code>l r</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, cetak <code>YA</code> jika potongan itu palindrom atau <code>TIDAK</code> jika bukan, masing-masing pada satu baris.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ Q ≤ 200 000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code></li><li>1 ≤ l ≤ r ≤ N</li></ul>',
        'samples' => [
            ['input' => "10 6\nkasurrusak\n1 10\n4 7\n2 5\n5 6\n1 9\n3 3\n", 'explanation' => 'Seluruh string "kasurrusak" adalah palindrom, begitu pula "urru" (4..7), "rr" (5..6), dan satu huruf "s" (3..3). Sebaliknya, "asur" (2..5) jika dibalik menjadi "rusa", dan "kasurrusa" (1..9) jika dibalik menjadi "asurrusak".'],
            ['input' => "5 3\nmalam\n2 4\n1 2\n1 5\n", 'explanation' => '"ala" dan "malam" adalah palindrom, tetapi "ma" jika dibalik menjadi "am".'],
        ],
        'tests' => function () use ($abjad, $thue, $fibo, $format, $kueriPal, $kueriThue) {
            $N = 200000;
            $Q = 200000;

            // Semua pasangan (l, r) pada string kecil.
            $kecil = T::word(10, 'ab');
            $semua = [];
            for ($l = 1; $l <= 10; $l++) {
                for ($r = $l; $r <= 10; $r++) {
                    $semua[] = [$l, $r];
                }
            }
            $sedang = T::word(1000, 'ab');

            $satuB = str_repeat('a', $N);
            $satuB[mt_rand(0, $N - 1)] = 'b';

            $acak = T::word($N, $abjad);

            // Rangkaian palindrom acak yang disambung.
            $gabung = '';
            while (strlen($gabung) < $N) {
                $p = T::word(mt_rand(1, 400), 'abc');
                $gabung .= $p.(mt_rand(0, 1) ? substr(strrev($p), 1) : strrev($p));
            }
            $gabung = substr($gabung, 0, $N);

            // Satu palindrom raksasa, lalu versi yang dirusak satu hurufnya.
            $separuh = T::word($N / 2, 'ab');
            $raksasa = $separuh.strrev($separuh);
            $rusak = $raksasa;
            $rusak[mt_rand(40000, 60000)] = 'c';

            // Thue–Morse: blok sejajar sepanjang 2^k adalah palindrom jika k genap; jika k ganjil,
            // kebalikannya adalah blok "warna terbalik" yang menjebak hash modulo 2^64.
            $tm = $thue(131072);
            $qsTM = $kueriPal($tm, 100000, 300);
            foreach ($kueriThue(131072, 100000) as $x) {
                $qsTM[] = [$x[0], $x[1]];
            }
            T::shuffle($qsTM);
            $fb = $fibo($N);
            $acakAB = T::word($N, 'ab');

            return [
                $format('a', [[1, 1]]),
                $format($kecil, $semua),
                $format($sedang, $kueriPal($sedang, 2000, 12)),
                $format($satuB, $kueriPal($satuB, $Q, $N)),
                $format($acak, $kueriPal($acak, $Q, 5)),
                $format($gabung, $kueriPal($gabung, $Q, 50)),
                $format($raksasa, $kueriPal($raksasa, $Q, $N)),
                $format($rusak, $kueriPal($rusak, $Q, $N)),
                $format($tm, $qsTM),
                $format($fb, $kueriPal($fb, $Q, 1000)),
                $format($acakAB, $kueriPal($acakAB, $Q, 8)),
            ];
        },
        'solve' => function (string $input) use ($manacher) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            // Manacher (pasti benar, tanpa hash).
            [$d1, $d2] = $manacher(trim($lines[1]));
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$l, $r] = T::ints($lines[2 + $i]);
                $l--;
                $r--;
                $len = $r - $l + 1;
                if ($len % 2 === 1) {
                    $ok = $d1[intdiv($l + $r, 2)] >= intdiv($len + 1, 2);
                } else {
                    $ok = $d2[intdiv($l + $r + 1, 2)] >= intdiv($len, 2);
                }
                $out[] = $ok ? 'YA' : 'TIDAK';
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    string s;\n    cin >> n >> q >> s;\n    for (int i = 0; i < q; i++) {\n        int l, r;\n        cin >> l >> r;\n    }", "const [n, q] = readInts();\nconst s = readLine().trim();\nfor (let i = 0; i < q; i++) {\n  const [l, r] = readInts();\n}", "data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\ns = data[2]   # bytes: s[i] berupa kode ASCII huruf ke-i\nkueri = list(map(int, data[3:3 + 2 * q]))   # l r berurutan"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

typedef unsigned long long ull;
const ull MOD = (1ULL << 61) - 1;   // bilangan prima Mersenne
const ull BASIS = 911382323;

ull mulmod(ull a, ull b) {
    __uint128_t c = (__uint128_t)a * b;
    ull r = (ull)(c & MOD) + (ull)(c >> 61);
    return r >= MOD ? r - MOD : r;
}

int n;
vector<ull> pw;

vector<ull> prefiks(const string& s) {
    vector<ull> h(n + 1, 0);
    for (int i = 0; i < n; i++) h[i + 1] = (mulmod(h[i], BASIS) + (ull)s[i]) % MOD;
    return h;
}

// hash potongan s[l..r-1] (0-indexed, r tidak termasuk)
ull ambil(const vector<ull>& h, int l, int r) {
    return (h[r] + MOD - mulmod(h[l], pw[r - l])) % MOD;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    string s;
    cin >> n >> q >> s;
    string t(s.rbegin(), s.rend());   // t = s dibalik

    pw.assign(n + 1, 1);
    for (int i = 0; i < n; i++) pw[i + 1] = mulmod(pw[i], BASIS);
    vector<ull> hs = prefiks(s), ht = prefiks(t);

    string out;
    for (int i = 0; i < q; i++) {
        int l, r;
        cin >> l >> r;
        // kebalikan s[l..r] (1-indexed) adalah t[n-r .. n-l] (0-indexed)
        bool pal = ambil(hs, l - 1, r) == ambil(ht, n - r, n - l + 1);
        out += pal ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const s = readLine().trim();

// Dua modulus prima < 2^26 (hasil kali < 2^52, tepat di Number).
const M1 = 67108859, M2 = 67108837;
const B1 = 9737333, B2 = 5555521;
const p1 = new Int32Array(n + 1), p2 = new Int32Array(n + 1);
p1[0] = p2[0] = 1;
for (let i = 0; i < n; i++) {
  p1[i + 1] = (p1[i] * B1) % M1;
  p2[i + 1] = (p2[i] * B2) % M2;
}
// hs: hash maju dari s, ht: hash dari s yang dibalik (t[j] = s[n-1-j])
const hs1 = new Int32Array(n + 1), hs2 = new Int32Array(n + 1);
const ht1 = new Int32Array(n + 1), ht2 = new Int32Array(n + 1);
for (let i = 0; i < n; i++) {
  const c = s.charCodeAt(i), d = s.charCodeAt(n - 1 - i);
  hs1[i + 1] = (hs1[i] * B1 + c) % M1;
  hs2[i + 1] = (hs2[i] * B2 + c) % M2;
  ht1[i + 1] = (ht1[i] * B1 + d) % M1;
  ht2[i + 1] = (ht2[i] * B2 + d) % M2;
}
const ambil = (h, p, M, l, r) => (h[r] - ((h[l] * p[r - l]) % M) + M) % M;

const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  // kebalikan s[l..r] (1-indexed) adalah t[n-r .. n-l] (0-indexed)
  const pal = ambil(hs1, p1, M1, l - 1, r) === ambil(ht1, p1, M1, n - r, n - l + 1) &&
    ambil(hs2, p2, M2, l - 1, r) === ambil(ht2, p2, M2, n - r, n - l + 1);
  out.push(pal ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()   # seluruh input sekaligus
    n, q = int(data[0]), int(data[1])
    s = data[2]                              # bytes
    t = s[::-1]                              # s dibalik
    M = (1 << 61) - 1
    B = 911382323

    hs = [0] * (n + 1)                       # hash maju dari s
    ht = [0] * (n + 1)                       # hash maju dari t
    pw = [1] * (n + 1)
    for i in range(n):
        hs[i + 1] = (hs[i] * B + s[i]) % M
        ht[i + 1] = (ht[i] * B + t[i]) % M
        pw[i + 1] = pw[i] * B % M

    it = iter(map(int, data[3:3 + 2 * q]))
    out = []
    for l, r in zip(it, it):
        p = pw[r - l + 1]
        # kebalikan s[l..r] (1-indexed) = t[n-r+1 .. n-l+1] (1-indexed)
        if (hs[r] - hs[l - 1] * p) % M == (ht[n - l + 1] - ht[n - r] * p) % M:
            out.append("YA")
        else:
            out.append("TIDAK")
    print("\n".join(out))


main()
CODE,
        ],
        'editorial' => '<p>Sebuah potongan adalah palindrom jika potongan itu <strong>sama dengan kebalikannya</strong>. Jadi soal ini adalah soal membandingkan dua potongan, dan hashing bisa menjawabnya dalam O(1).</p>
<p>Buat T = S dibalik, yaitu T<sub>j</sub> = S<sub>N+1−j</sub>. Kebalikan dari S[l..r] adalah S<sub>r</sub>S<sub>r−1</sub>…S<sub>l</sub> = T[N−r+1 .. N−l+1]: potongan yang sepanjang sama di T. Hitung prefix hash untuk S dan untuk T (dengan basis dan modulus yang sama), lalu untuk setiap pertanyaan bandingkan</p>
<p style="text-align:center"><code>hash<sub>S</sub>(l, r) = hash<sub>T</sub>(N−r+1, N−l+1)</code></p>
<p>Total O(N + Q). Bagian yang paling sering salah adalah pemetaan indeks ke T: periksa dengan contoh kecil, misalnya potongan satu huruf di ujung string.</p>
<p>Seperti biasa, pakai modulus besar (2<sup>61</sup> − 1) atau dua modulus; salah satu tes memakai barisan Thue–Morse yang menjebak hash modulo 2<sup>64</sup>. Alternatif tanpa hash adalah <em>algoritma Manacher</em>, yang menghitung palindrom terpanjang di setiap pusat dalam O(N); potongan [l, r] palindrom jika jari-jari di pusatnya cukup panjang.</p>',
        'hints' => [
            'Palindrom berarti potongan itu sama dengan kebalikannya. Bagaimana cara membandingkan dua potongan dengan cepat?',
            'Buat juga string T = S dibalik. Kebalikan dari S[l..r] adalah sebuah potongan dari T. Potongan yang mana?',
            'Kebalikan S[l..r] = T[N−r+1..N−l+1]. Hitung prefix hash S dan T, lalu bandingkan kedua hash dalam O(1).',
        ],
    ],

    [
        'slug' => 'potongan-bersama-panjang',
        'lesson' => 'string-hashing',
        'title' => 'Jejak Protein Bersama',
        'difficulty' => 'Sulit',
        'tags' => ['string', 'hashing', 'binary search'],
        'statement' => '<p>Sebuah laboratorium bioinformatika membandingkan dua rantai protein yang sangat panjang, <strong>A</strong> dan <strong>B</strong>. Setiap asam amino ditulis sebagai satu huruf kecil. Bagian <strong>berurutan</strong> (substring) terpanjang yang muncul di kedua rantai sering menandakan fungsi biologis yang sama, jadi peneliti ingin tahu panjangnya.</p>
<p>Rantai bisa mencapai 100 000 huruf, sehingga membandingkan setiap pasang posisi (sekitar 10<sup>10</sup> pasang) jelas terlalu lambat. Berapa panjang potongan bersama terpanjang?</p>',
        'input_format' => '<p>Baris pertama berisi A. Baris kedua berisi B.</p>',
        'output_format' => '<p>Panjang substring terpanjang yang muncul di A dan juga di B (0 jika tidak ada huruf yang sama).</p>',
        'constraints' => '<ul><li>1 ≤ |A|, |B| ≤ 100 000</li><li>A dan B hanya berisi huruf kecil <code>a</code>–<code>z</code></li></ul>',
        'samples' => [
            ['input' => "mkvlaagiv\nqqlaagwmkv\n", 'explanation' => '"laag" muncul di A (huruf ke-4 sampai ke-7) dan di B (huruf ke-3 sampai ke-6). "mkv" juga muncul di keduanya, tetapi lebih pendek. Tidak ada potongan bersama sepanjang 5.'],
            ['input' => "abab\nbaba\n", 'explanation' => '"aba" dan "bab" muncul di kedua rantai. "abab" tidak ada di B dan "baba" tidak ada di A, jadi jawabannya 3.'],
            ['input' => "aaaa\nbbbbb\n", 'explanation' => 'Tidak ada satu huruf pun yang sama, jadi jawabannya 0.'],
        ],
        'tests' => function () use ($abjad, $thue, $fibo) {
            $f = fn (string $a, string $b) => "$a\n$b\n";
            $M = 100000;

            $x = T::word($M, $abjad);
            $tanam = T::word(30000, $abjad).substr($x, 12345, 50000).T::word(20000, $abjad);
            $sama = T::word($M, $abjad);

            $tm = $thue(131072);
            $tmB = strtr(substr($tm, 3000, $M), 'ab', 'ba');   // kebalikan warna Thue–Morse

            // Pola berulang dengan beberapa mutasi di kedua rantai.
            $blok = T::word(1000, $abjad);
            $polaA = substr(str_repeat($blok, 101), 0, $M);
            $polaB = substr(str_repeat(substr($blok, 377).substr($blok, 0, 377), 101), 0, $M);
            for ($i = 0; $i < 60; $i++) {
                $polaA[mt_rand(0, $M - 1)] = 'z';
                $polaB[mt_rand(0, $M - 1)] = 'y';
            }

            return [
                "a\na\n",
                "a\nb\n",
                $f(T::word(10, 'ab'), T::word(12, 'ab')),
                $f(T::word(300, 'abc'), T::word(500, 'abc')),
                $f('q', T::word($M, $abjad)),
                $f(T::word($M, $abjad), T::word($M, $abjad)),
                $f(T::word($M, 'ab'), T::word($M, 'ab')),
                $f($x, $tanam),
                $f($sama, $sama),
                $f(str_repeat('a', $M), str_repeat('a', 70000)),
                $f(T::word($M, 'abcdefghijklm'), T::word($M, 'nopqrstuvwxyz')),
                $f(substr($tm, 0, $M), $tmB),
                $f($fibo($M), substr($fibo(2 * $M), 777, $M)),
                $f($polaA, $polaB),
            ];
        },
        'solve' => function (string $input) use ($sam) {
            $lines = T::lines($input);
            $a = trim($lines[0]);
            $b = trim($lines[1]);
            // Suffix automaton dari A (pasti benar, tanpa hash), lalu jalankan B di atasnya:
            // l = panjang akhiran terpanjang dari B[..i] yang merupakan potongan A.
            [$len, $link, $nx] = $sam($a);
            $v = 0;
            $l = 0;
            $best = 0;
            $m = strlen($b);
            for ($i = 0; $i < $m; $i++) {
                $c = ord($b[$i]) - 97;
                while ($v !== 0 && ! isset($nx[$v * 26 + $c])) {
                    $v = $link[$v];
                    $l = $len[$v];
                }
                if (isset($nx[$v * 26 + $c])) {
                    $v = $nx[$v * 26 + $c];
                    $l++;
                } else {
                    $l = 0;
                }
                if ($l > $best) {
                    $best = $l;
                }
            }

            return (string) $best;
        },
        'starter' => $st("    string a, b;\n    cin >> a >> b;", "const A = readLine().trim();\nconst B = readLine().trim();", "A = input().strip()\nB = input().strip()"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

typedef unsigned long long ull;
const ull MOD = (1ULL << 61) - 1;   // bilangan prima Mersenne
const ull BASIS = 911382323;

ull mulmod(ull a, ull b) {
    __uint128_t c = (__uint128_t)a * b;
    ull r = (ull)(c & MOD) + (ull)(c >> 61);
    return r >= MOD ? r - MOD : r;
}

vector<ull> pw;

vector<ull> prefiks(const string& s) {
    vector<ull> h(s.size() + 1, 0);
    for (size_t i = 0; i < s.size(); i++) h[i + 1] = (mulmod(h[i], BASIS) + (ull)s[i]) % MOD;
    return h;
}

// hash potongan s[l..r-1] (0-indexed, r tidak termasuk)
ull ambil(const vector<ull>& h, int l, int r) {
    return (h[r] + MOD - mulmod(h[l], pw[r - l])) % MOD;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string a, b;
    cin >> a >> b;
    int n = a.size(), m = b.size();

    pw.assign(max(n, m) + 1, 1);
    for (size_t i = 1; i < pw.size(); i++) pw[i] = mulmod(pw[i - 1], BASIS);
    vector<ull> ha = prefiks(a), hb = prefiks(b);

    // ada(k): adakah potongan sepanjang k yang muncul di A dan di B?
    auto ada = [&](int k) {
        vector<ull> v;
        v.reserve(n - k + 1);
        for (int i = 0; i + k <= n; i++) v.push_back(ambil(ha, i, i + k));
        sort(v.begin(), v.end());
        for (int j = 0; j + k <= m; j++)
            if (binary_search(v.begin(), v.end(), ambil(hb, j, j + k))) return true;
        return false;
    };

    // Jika ada potongan bersama sepanjang k, pasti ada juga yang sepanjang k - 1.
    int lo = 0, hi = min(n, m);   // ada(lo) selalu benar
    while (lo < hi) {
        int mid = (lo + hi + 1) / 2;
        if (ada(mid)) lo = mid;
        else hi = mid - 1;
    }
    cout << lo << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const A = readLine().trim();
const B = readLine().trim();
const n = A.length, m = B.length;

// Dua modulus prima < 2^26 (hasil kali < 2^52, tepat di Number).
const M1 = 67108859, M2 = 67108837;
const B1 = 9737333, B2 = 5555521;
const N = Math.max(n, m);
const p1 = new Int32Array(N + 1), p2 = new Int32Array(N + 1);
p1[0] = p2[0] = 1;
for (let i = 0; i < N; i++) {
  p1[i + 1] = (p1[i] * B1) % M1;
  p2[i + 1] = (p2[i] * B2) % M2;
}
function prefiks(s, basis, mod) {
  const h = new Int32Array(s.length + 1);
  for (let i = 0; i < s.length; i++) h[i + 1] = (h[i] * basis + s.charCodeAt(i)) % mod;
  return h;
}
const a1 = prefiks(A, B1, M1), a2 = prefiks(A, B2, M2);
const b1 = prefiks(B, B1, M1), b2 = prefiks(B, B2, M2);

// ada(k): adakah potongan sepanjang k yang muncul di A dan di B?
function ada(k) {
  const q1 = p1[k], q2 = p2[k];
  const hashA = new Set();
  for (let i = 0; i + k <= n; i++) {
    const x1 = (a1[i + k] - ((a1[i] * q1) % M1) + M1) % M1;
    const x2 = (a2[i + k] - ((a2[i] * q2) % M2) + M2) % M2;
    hashA.add(x1 * 67108864 + x2); // gabungan dua hash, < 2^52
  }
  for (let j = 0; j + k <= m; j++) {
    const y1 = (b1[j + k] - ((b1[j] * q1) % M1) + M1) % M1;
    const y2 = (b2[j + k] - ((b2[j] * q2) % M2) + M2) % M2;
    if (hashA.has(y1 * 67108864 + y2)) return true;
  }
  return false;
}

let lo = 0, hi = Math.min(n, m); // ada(lo) selalu benar
while (lo < hi) {
  const mid = (lo + hi + 1) >> 1;
  if (ada(mid)) lo = mid;
  else hi = mid - 1;
}
console.log(lo);
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()
    a, b = data[0], data[1]                  # bytes
    n, m = len(a), len(b)
    M = (1 << 61) - 1
    B = 911382323

    def prefiks(s):
        h = [0] * (len(s) + 1)
        x = 0
        for i, c in enumerate(s):
            x = (x * B + c) % M
            h[i + 1] = x
        return h

    ha, hb = prefiks(a), prefiks(b)

    def ada(k):
        # adakah potongan sepanjang k yang muncul di A dan di B?
        p = pow(B, k, M)
        hash_a = {(x - y * p) % M for x, y in zip(ha[k:], ha)}
        return not hash_a.isdisjoint((x - y * p) % M for x, y in zip(hb[k:], hb))

    lo, hi = 0, min(n, m)                    # ada(lo) selalu benar
    while lo < hi:
        mid = (lo + hi + 1) // 2
        if ada(mid):
            lo = mid
        else:
            hi = mid - 1
    print(lo)


main()
CODE,
        ],
        'editorial' => '<p>DP substring bersama memakai tabel |A| × |B| = 10<sup>10</sup> sel, terlalu lambat untuk batasan ini. Kuncinya adalah sifat <strong>monoton</strong>: jika ada potongan bersama sepanjang k, buang satu huruf di ujungnya dan kita mendapat potongan bersama sepanjang k − 1. Jadi jawaban bisa dicari dengan <strong>binary search</strong> pada panjang k di rentang [0, min(|A|, |B|)].</p>
<p>Untuk memeriksa satu nilai k: dengan prefix hash, hitung hash semua potongan A sepanjang k (O(|A|)) dan masukkan ke set, lalu periksa apakah salah satu hash potongan B sepanjang k ada di set itu (O(|B|)). Bisa juga mengurutkan hash A lalu memakai binary search. Total O((|A| + |B|) log min(|A|, |B|)).</p>
<p><strong>Tabrakan.</strong> Setiap langkah pada dasarnya membandingkan setiap potongan B dengan setiap potongan A, sekitar 10<sup>10</sup> pasang, diulang sekitar 17 kali. Dengan modulus sekitar 10<sup>9</sup>, "kecocokan palsu" hampir pasti terjadi dan jawaban menjadi terlalu besar. Pakai 2<sup>61</sup> − 1 atau dua modulus.</p>
<p><strong>Jebakan lain:</strong> jawabannya bisa 0 (tidak ada huruf yang sama), jadi mulai binary search dari 0, dan gunakan <code>mid = (lo + hi + 1) / 2</code> agar tidak terjebak loop tak berujung. Alternatif tanpa hash dan tanpa log adalah suffix automaton atau suffix array, tetapi keduanya jauh lebih panjang ditulis.</p>',
        'hints' => [
            'Jika ada potongan bersama sepanjang k, pasti ada juga yang sepanjang k − 1. Sifat ini memungkinkan binary search pada jawabannya.',
            'Untuk k tertentu, masukkan hash semua potongan A sepanjang k ke sebuah set, lalu cek apakah ada potongan B sepanjang k yang hash-nya ada di set. Semuanya O(|A| + |B|).',
            'Binary search k di [0, min(|A|, |B|)] dengan pengecekan tadi: total O((|A| + |B|) log). Gunakan modulus besar karena ada sekitar 10^10 pasangan yang dibandingkan di setiap langkah.',
        ],
    ],
];
