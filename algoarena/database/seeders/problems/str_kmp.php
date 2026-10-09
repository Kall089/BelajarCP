<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi KMP & Fungsi Prefiks: pencarian pola, periode terpendek,
 * rantai border, dan banyak kemunculan setiap awalan.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Fungsi prefiks untuk solusi referensi PHP: $pi[$i] = panjang border terpanjang dari s[0..i]. */
$prefiks = function (string $s): array {
    $n = strlen($s);
    $pi = $n > 0 ? array_fill(0, $n, 0) : [];
    $k = 0;
    for ($i = 1; $i < $n; $i++) {
        $c = $s[$i];
        while ($k > 0 && $s[$k] !== $c) {
            $k = $pi[$k - 1];
        }
        if ($s[$k] === $c) {
            $k++;
        }
        $pi[$i] = $k;
    }

    return $pi;
};

/** Kata Fibonacci terpanjang yang panjangnya ≤ $maxLen (a, ab, aba, abaab, abaababa, ...). */
$fibWord = function (int $maxLen): string {
    $a = 'a';
    $b = 'ab';
    while (strlen($a) + strlen($b) <= $maxLen) {
        [$a, $b] = [$b, $b.$a];
    }

    return $b;
};

/** Kata Zimin orde k: Z1 = a, Zk = Z(k-1) + huruf ke-k + Z(k-1). Panjangnya 2^k − 1. */
$zimin = function (int $k): string {
    $z = '';
    for ($i = 1; $i <= $k; $i++) {
        $z = $z.chr(96 + $i).$z;
    }

    return $z;
};

$AZ = 'abcdefghijklmnopqrstuvwxyz';

/** Fungsi prefiks yang dipakai bersama oleh semua solusi C++. */
$piCpp = <<<'CODE'
// pi[i] = panjang border terpanjang dari s[0..i], yaitu awalan terpanjang
// yang juga akhiran s[0..i] (tidak termasuk s[0..i] itu sendiri).
vector<int> fungsiPrefiks(const string& s) {
    int n = s.size();
    vector<int> pi(n, 0);
    for (int i = 1; i < n; i++) {
        int k = pi[i - 1];
        while (k > 0 && s[i] != s[k]) k = pi[k - 1];   // mundur lewat rantai border
        if (s[i] == s[k]) k++;
        pi[i] = k;
    }
    return pi;
}
CODE;

$piJs = <<<'CODE'
// pi[i] = panjang border terpanjang dari s[0..i]
function fungsiPrefiks(s) {
  const n = s.length;
  const pi = new Int32Array(n);
  for (let i = 1; i < n; i++) {
    const c = s.charCodeAt(i);
    let k = pi[i - 1];
    while (k > 0 && s.charCodeAt(k) !== c) k = pi[k - 1]; // mundur lewat rantai border
    if (s.charCodeAt(k) === c) k++;
    pi[i] = k;
  }
  return pi;
}
CODE;

$piPy = <<<'CODE'
def fungsi_prefiks(s):
    # pi[i] = panjang border terpanjang dari s[0..i]
    n = len(s)
    pi = [0] * n
    k = 0
    for i in range(1, n):
        c = s[i]
        while k and s[k] != c:
            k = pi[k - 1]      # mundur lewat rantai border
        if s[k] == c:
            k += 1
        pi[i] = k
    return pi
CODE;

return [
    [
        'slug' => 'cari-pola',
        'lesson' => 'kmp',
        'title' => 'Sandi di Naskah Kuno',
        'difficulty' => 'Mudah',
        'tags' => ['string', 'kmp', 'pencarian pola'],
        'statement' => '<p>Laras, seorang filolog, sedang meneliti salinan digital sebuah naskah kuno. Seluruh isi naskah sudah ditulis ulang menjadi satu untaian huruf kecil <strong>T</strong> tanpa spasi. Ia menduga sebuah kata sandi <strong>P</strong> sengaja disisipkan berkali-kali di dalamnya.</p>
<p>Bantu Laras menghitung berapa kali P muncul sebagai potongan berurutan di T. Kemunculan boleh tumpang tindih: di dalam <code>aaaa</code>, kata <code>aa</code> muncul 3 kali. Tuliskan juga posisi awal kemunculan-kemunculan itu (huruf pertama T berada di posisi 1). Agar daftarnya tidak terlalu panjang, cukup tuliskan <strong>paling banyak 1000 posisi pertama</strong>.</p>',
        'input_format' => '<p>Baris pertama berisi string <code>T</code>. Baris kedua berisi string <code>P</code>.</p>',
        'output_format' => '<p>Baris pertama berisi <code>K</code>, banyak kemunculan P di T. Baris kedua berisi posisi awal dari min(K, 1000) kemunculan pertama, terurut naik dan dipisah spasi. Jika K = 0, cukup cetak <code>0</code> (baris kedua boleh kosong).</p>',
        'constraints' => '<ul><li>1 ≤ |T| ≤ 10<sup>6</sup></li><li>1 ≤ |P| ≤ 10<sup>6</sup> (P boleh lebih panjang dari T)</li><li>T dan P hanya berisi huruf kecil <code>a</code>–<code>z</code>.</li></ul>',
        'samples' => [
            ['input' => "abababa\naba\n", 'explanation' => '<code>aba</code> muncul di posisi 1, 3, dan 5. Kemunculan di posisi 1 dan 3 sama-sama memakai huruf ke-3, tetapi keduanya tetap dihitung.'],
            ['input' => "kucingkucing\nanjing\n", 'explanation' => 'Kata <code>anjing</code> tidak pernah muncul, jadi cukup cetak 0.'],
        ],
        'tests' => function () use ($AZ) {
            // Teks acak dengan pola yang ditanam di beberapa posisi acak.
            $plant = function (int $n, int $m, int $times) {
                $t = T::word($n, 'ab');
                $p = T::word($m, 'ab');
                for ($i = 0; $i < $times; $i++) {
                    $t = substr_replace($t, $p, mt_rand(0, $n - $m), $m);
                }

                return "$t\n$p\n";
            };
            $big = T::word(1000000, $AZ);
            $xyz = T::word(1000000, 'xyz');

            return [
                "a\na\n",
                "a\nb\n",
                "abc\nabcd\n",
                T::word(20, 'ab')."\n".T::word(2, 'ab')."\n",
                T::word(1000, 'ab')."\naba\n",
                str_repeat('a', 1000000)."\n".str_repeat('a', 500000)."\n",
                str_repeat('a', 1000000)."\n".str_repeat('a', 499999)."b\n",
                T::word(1000000, 'ab')."\n".T::word(10, 'ab')."\n",
                str_repeat('abc', 333333)."a\nabcabca\n",
                $big."\n".substr($big, mt_rand(0, 999000), 1000)."\n",
                $plant(1000000, 30, 40),
                "a\n".str_repeat('a', 1000000)."\n",
                "$xyz\n$xyz\n",
            ];
        },
        'solve' => function (string $input) use ($prefiks) {
            $lines = T::lines($input);
            $t = trim($lines[0]);
            $p = trim($lines[1] ?? '');
            $m = strlen($p);
            if ($m > strlen($t)) {
                return '0';
            }
            $pi = $prefiks($p.'#'.$t);
            $len = count($pi);
            $k = 0;
            $pos = [];
            for ($i = 2 * $m; $i < $len; $i++) {
                if ($pi[$i] === $m) {
                    $k++;
                    if ($k <= 1000) {
                        $pos[] = $i - 2 * $m + 1;
                    }
                }
            }

            return $k === 0 ? '0' : $k."\n".implode(' ', $pos);
        },
        'starter' => $st("    string t, p;\n    cin >> t >> p;", "const t = readLine().trim();\nconst p = readLine().trim();", "t = input().strip()\np = input().strip()"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$piCpp.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string t, p;
    cin >> t >> p;
    int m = p.size();

    // Gabungkan menjadi P + '#' + T. Karena '#' tidak muncul di P maupun T,
    // nilai pi tidak pernah melebihi m, dan pi[i] == m berarti P berakhir di indeks i.
    string s = p + '#' + t;
    vector<int> pi = fungsiPrefiks(s);

    int k = 0;
    string posisi;
    for (int i = m + 1; i < (int)s.size(); i++) {
        if (pi[i] == m) {
            k++;
            if (k <= 1000) {
                if (k > 1) posisi += ' ';
                posisi += to_string(i - 2 * m + 1);   // posisi awal di T (1-based)
            }
        }
    }
    cout << k << '\n' << posisi << '\n';
    return 0;
}
CODE,
            'javascript' => "const t = readLine().trim();\nconst p = readLine().trim();\n".$piJs.<<<'CODE'

const m = p.length;
// P + '#' + T: pi[i] === m berarti P berakhir di indeks i
const s = p + "#" + t;
const pi = fungsiPrefiks(s);
let k = 0;
const posisi = [];
for (let i = m + 1; i < s.length; i++) {
  if (pi[i] === m) {
    k++;
    if (k <= 1000) posisi.push(i - 2 * m + 1); // posisi awal di T (1-based)
  }
}
console.log(k + "\n" + posisi.join(" "));
CODE,
            'python' => "import sys\ninput = sys.stdin.readline\n\n".$piPy.<<<'CODE'


t = input().strip()
p = input().strip()
m = len(p)

# P + '#' + T: pi[i] == m berarti P berakhir di indeks i
s = p + "#" + t
pi = fungsi_prefiks(s)
k = 0
posisi = []
for i in range(m + 1, len(s)):
    if pi[i] == m:
        k += 1
        if k <= 1000:
            posisi.append(i - 2 * m + 1)   # posisi awal di T (1-based)
print(k)
print(" ".join(map(str, posisi)))
CODE,
        ],
        'editorial' => '<p>Cara naif mencoba setiap posisi awal di T lalu membandingkan sampai |P| huruf: O(|T| · |P|). Untuk T = <code>aaa…a</code> dan P = <code>aa…a</code> sepanjang 5 · 10<sup>5</sup>, itu sekitar 2,5 · 10<sup>11</sup> perbandingan.</p>
<p>Gabungkan menjadi <code>S = P + \'#\' + T</code> lalu hitung fungsi prefiks π. Karena <code>#</code> tidak muncul di P maupun T, tidak ada border yang melewati <code>#</code>, sehingga π[i] ≤ |P|. Nilai π[i] = |P| berarti |P| huruf yang berakhir di indeks i sama dengan P: itulah satu kemunculan P di T. Jika i adalah indeks (0-based) di S, posisi awalnya di T (1-based) adalah <code>i − 2|P| + 1</code>.</p>
<p>Fungsi prefiks dibangun dalam O(|S|): nilai k naik paling banyak 1 per langkah, jadi total penurunan k (lewat rantai π) juga paling banyak |S|. Total O(|T| + |P|).</p>
<p><strong>Jebakan:</strong> kemunculan boleh tumpang tindih, jadi jangan melompat sejauh |P| setelah menemukan satu kemunculan. K bisa mencapai 10<sup>6</sup>; tetap hitung semuanya walaupun hanya 1000 posisi pertama yang dicetak. Jika |P| &gt; |T|, jawabannya 0.</p>',
        'hints' => [
            'Membandingkan P di setiap posisi T bisa sangat lambat jika keduanya berisi huruf yang sama berulang-ulang. Informasi apa dari perbandingan sebelumnya yang bisa dipakai lagi?',
            'Fungsi prefiks π[i] = panjang awalan terpanjang yang juga akhiran dari s[0..i]. Coba hitung π untuk gabungan P + "#" + T.',
            'Setiap indeks i dengan π[i] = |P| menandai kemunculan yang berakhir di i; posisi awalnya di T adalah i − 2|P| + 1. Hitung semuanya, tetapi simpan hanya 1000 posisi pertama.',
        ],
    ],

    [
        'slug' => 'motif-berulang',
        'lesson' => 'kmp',
        'title' => 'Cap Batik Berulang',
        'difficulty' => 'Sedang',
        'tags' => ['string', 'fungsi prefiks', 'periode'],
        'statement' => '<p>Bu Sekar membuat kain batik cap yang sangat panjang. Ia mengambil satu cap, lalu mengecapkannya berkali-kali secara berjajar, tanpa celah dan tanpa tumpang tindih. Setiap motif dicatat sebagai satu huruf kecil, sehingga pola seluruh kain bisa ditulis sebagai string <strong>S</strong>.</p>
<p>Sayangnya Bu Sekar lupa cap mana yang dipakainya. Bisa jadi kain itu dibuat dengan cap pendek yang dicap berkali-kali. Tentukan bilangan <strong>k terbesar</strong> sehingga S dapat ditulis sebagai <code>t t … t</code> (t diulang k kali) untuk suatu string t. Setiap kain setidaknya bisa dianggap satu kali cap berisi seluruh S, jadi jawabannya minimal 1.</p>',
        'input_format' => '<p>Satu baris berisi string <code>S</code>.</p>',
        'output_format' => '<p>Bilangan k terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 10<sup>6</sup></li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code>.</li></ul>',
        'samples' => [
            ['input' => "abcabcabcabc\n", 'explanation' => 'S = <code>abc</code> diulang 4 kali. <code>abcabc</code> diulang 2 kali juga sah, tetapi 4 lebih banyak.'],
            ['input' => "abababa\n", 'explanation' => 'Pola <code>ab</code> memang terlihat berulang, tetapi panjang 7 bukan kelipatan 2 sehingga huruf <code>a</code> terakhir tidak membentuk salinan utuh. Satu-satunya pilihan adalah t = S, jadi k = 1.'],
            ['input' => "zzzzzz\n", 'explanation' => 'Capnya cukup berisi satu motif <code>z</code>, dicap 6 kali.'],
        ],
        'tests' => function () use ($AZ, $fibWord) {
            $r7 = str_repeat(T::word(7, $AZ), 142857);

            return [
                "a\n",
                "ab\n",
                "aa\n",
                str_repeat(T::word(3, 'ab'), 7)."\n",
                str_repeat(T::word(1000, $AZ), 1000)."\n",
                str_repeat('a', 1000000)."\n",
                $r7."\n",
                substr($r7, 0, -1)."\n",
                str_repeat('abaab', 200000)."\n",
                T::word(1000000, 'ab')."\n",
                str_repeat(T::word(500000, 'ab'), 2)."\n",
                $fibWord(1000000)."\n",
                str_repeat('a', 999983)."\n",
            ];
        },
        'solve' => function (string $input) use ($prefiks) {
            $s = trim($input);
            $n = strlen($s);
            $pi = $prefiks($s);
            $p = $n - $pi[$n - 1];

            return (string) ($n % $p === 0 ? intdiv($n, $p) : 1);
        },
        'starter' => $st("    string s;\n    cin >> s;", 'const s = readLine().trim();', 's = input().strip()'),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$piCpp.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int n = s.size();
    vector<int> pi = fungsiPrefiks(s);

    // Border terpanjang pi[n-1]  ->  periode terpendek p = n - pi[n-1].
    int p = n - pi[n - 1];
    // Hanya jika p membagi n, S tersusun dari salinan utuh S[0..p-1].
    cout << (n % p == 0 ? n / p : 1) << '\n';
    return 0;
}
CODE,
            'javascript' => "const s = readLine().trim();\n".$piJs.<<<'CODE'

const n = s.length;
const pi = fungsiPrefiks(s);
const p = n - pi[n - 1]; // periode terpendek
console.log(n % p === 0 ? n / p : 1);
CODE,
            'python' => "import sys\ninput = sys.stdin.readline\n\n".$piPy.<<<'CODE'


s = input().strip()
n = len(s)
pi = fungsi_prefiks(s)
p = n - pi[n - 1]          # periode terpendek
print(n // p if n % p == 0 else 1)
CODE,
        ],
        'editorial' => '<p>Bilangan p disebut <strong>periode</strong> S jika S[i] = S[i + p] untuk semua i yang valid. Jika S = t<sup>k</sup>, maka |t| adalah periode yang membagi n = |S|.</p>
<p>Periode berhubungan erat dengan border: S punya border sepanjang b jika dan hanya jika S punya periode n − b (geser S sejauh n − b, bagian yang bertumpuk adalah awalan dan akhiran sepanjang b). Border terpanjang adalah π[n − 1], sehingga <strong>periode terpendek</strong> adalah <code>p = n − π[n − 1]</code>.</p>
<ul><li>Jika p membagi n, maka S = (S[0..p−1])<sup>n/p</sup>, dan tidak ada t yang lebih pendek. Jawabannya n / p.</li>
<li>Jika p tidak membagi n, jawabannya 1. Andaikan S = t<sup>k</sup> dengan k ≥ 2; maka p ≤ |t| ≤ n/2, sehingga p + |t| ≤ n. Menurut lemma periodisitas (Fine–Wilf), FPB(p, |t|) juga periode S. Tidak ada periode yang lebih kecil dari p, jadi FPB(p, |t|) = p: p membagi |t|, dan karena |t| membagi n, p juga membagi n. Kontradiksi.</li></ul>
<p>Total O(n) untuk membangun π.</p>
<p><strong>Jebakan:</strong> string seperti <code>abababa</code> punya periode 2, tetapi bukan gabungan salinan utuh, sehingga jawabannya 1, bukan 3 atau 4. Kata Fibonacci (<code>abaababaabaab…</code>) juga punya border yang sangat panjang tetapi tetap berjawaban 1.</p>',
        'hints' => [
            'Jika S = t t … t, maka S[i] = S[i + |t|] untuk semua i. Panjang seperti |t| disebut periode.',
            'Periode terpendek berkaitan dengan border terpanjang: p = n − π[n − 1].',
            'Jika p membagi n, jawabannya n / p; jika tidak, jawabannya 1 (lihat contoh abababa).',
        ],
    ],

    [
        'slug' => 'awalan-akhiran',
        'lesson' => 'kmp',
        'title' => 'Larik Bergema',
        'difficulty' => 'Sedang',
        'tags' => ['string', 'fungsi prefiks', 'border'],
        'statement' => '<p>Kirana menulis larik-larik puisi tanpa spasi, hanya dengan huruf kecil. Ia menyebut sebuah panjang <strong>L</strong> sebagai <em>gema</em> jika L huruf pertama larik itu sama persis dengan L huruf terakhirnya. Awalan dan akhiran itu boleh bertumpuk. Panjang L = |S| selalu merupakan gema, karena seluruh larik tentu sama dengan dirinya sendiri.</p>
<p>Untuk larik <strong>S</strong>, tuliskan semua panjang gema dari yang terkecil sampai yang terbesar.</p>',
        'input_format' => '<p>Satu baris berisi string <code>S</code>.</p>',
        'output_format' => '<p>Baris pertama berisi <code>K</code>, banyak panjang gema. Baris kedua berisi K bilangan tersebut, terurut naik dan dipisah spasi.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 10<sup>6</sup></li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code>.</li></ul>',
        'samples' => [
            ['input' => "abacaba\n", 'explanation' => 'L = 1: <code>a</code> dan <code>a</code>. L = 3: <code>aba</code> dan <code>aba</code>. L = 7: seluruh larik. Panjang lain tidak cocok, misalnya L = 2: <code>ab</code> ≠ <code>ba</code>.'],
            ['input' => "kupukupu\n", 'explanation' => '<code>kupu</code> ada di awal dan di akhir larik (L = 4), ditambah seluruh larik (L = 8).'],
            ['input' => "aaaa\n", 'explanation' => 'Setiap awalan <code>a…a</code> sama dengan akhiran yang sepanjang itu, meskipun keduanya bertumpuk.'],
        ],
        'tests' => function () use ($AZ, $fibWord, $zimin) {
            $u = T::word(300000, 'ab');

            return [
                "a\n",
                "ab\n",
                "aa\n",
                T::word(15, 'ab')."\n",
                T::word(1000, 'ab')."\n",
                str_repeat('a', 200000)."\n",
                str_repeat('ab', 100000)."a\n",
                str_repeat('a', 999999)."b\n",
                'b'.str_repeat('a', 999999)."\n",
                str_repeat(T::word(1000, $AZ), 1000)."\n",
                $zimin(19)."\n",
                $fibWord(1000000)."\n",
                $u.T::word(400000, 'ab').$u."\n",
            ];
        },
        'solve' => function (string $input) use ($prefiks) {
            $s = trim($input);
            $n = strlen($s);
            $pi = $prefiks($s);
            $res = [];
            for ($L = $n; $L > 0; $L = $pi[$L - 1]) {
                $res[] = $L;
            }
            $res = array_reverse($res);

            return count($res)."\n".implode(' ', $res);
        },
        'starter' => $st("    string s;\n    cin >> s;", 'const s = readLine().trim();', 's = input().strip()'),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$piCpp.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int n = s.size();
    vector<int> pi = fungsiPrefiks(s);

    // Rantai border: n -> pi[n-1] -> pi[L-1] -> ... -> 0 memuat SEMUA gema.
    vector<int> gema;
    for (int L = n; L > 0; L = pi[L - 1]) gema.push_back(L);
    reverse(gema.begin(), gema.end());

    string out = to_string(gema.size()) + "\n";
    for (size_t i = 0; i < gema.size(); i++) {
        if (i > 0) out += ' ';
        out += to_string(gema[i]);
    }
    cout << out << '\n';
    return 0;
}
CODE,
            'javascript' => "const s = readLine().trim();\n".$piJs.<<<'CODE'

const n = s.length;
const pi = fungsiPrefiks(s);
// Rantai border dari yang terpanjang: n, pi[n-1], pi[L-1], ...
const gema = [];
for (let L = n; L > 0; L = pi[L - 1]) gema.push(L);
gema.reverse();
console.log(gema.length + "\n" + gema.join(" "));
CODE,
            'python' => "import sys\ninput = sys.stdin.readline\n\n".$piPy.<<<'CODE'


s = input().strip()
n = len(s)
pi = fungsi_prefiks(s)

# Rantai border dari yang terpanjang: n, pi[n-1], pi[L-1], ...
gema = []
L = n
while L > 0:
    gema.append(L)
    L = pi[L - 1]
gema.reverse()
print(len(gema))
print(" ".join(map(str, gema)))
CODE,
        ],
        'editorial' => '<p>Panjang L &lt; n adalah gema jika dan hanya jika awalan sepanjang L adalah <strong>border</strong> S (awalan yang juga akhiran). Fungsi prefiks langsung memberi border terpanjang: π[n − 1].</p>
<p>Kuncinya: jika b adalah border S, maka setiap border S yang lebih pendek dari b juga merupakan border dari string b itu sendiri (ia adalah awalan S yang lebih pendek, jadi awalan b; dan akhiran S yang lebih pendek, jadi akhiran b). Sebaliknya, border dari b juga border dari S. Akibatnya border terpanjang berikutnya setelah b adalah π[b − 1], dan rantai</p>
<p style="text-align:center"><code>n → π[n−1] → π[L−1] → … → 0</code></p>
<p>memuat semua gema, masing-masing tepat sekali, dari yang terpanjang. Kumpulkan, lalu balik urutannya.</p>
<p>Membangun π O(n) dan rantai paling banyak n langkah, jadi total O(n).</p>
<p><strong>Jebakan:</strong> membandingkan awalan dan akhiran untuk setiap L secara langsung bisa O(n²), misalnya pada <code>aaa…ab</code> (setiap perbandingan baru gagal di huruf terakhir). Jangan lupa L = n. Banyak gema bisa sangat besar (pada <code>aaa…a</code> semua L adalah gema), jadi kumpulkan output dalam satu string.</p>',
        'hints' => [
            'Awalan yang juga akhiran disebut border. Nilai fungsi prefiks yang mana yang memberi border terpanjang dari seluruh S?',
            'Jika b adalah border S, maka border S yang lebih pendek dari b pasti juga border dari b. Border terpanjang b adalah π[b − 1].',
            'Ikuti rantai L = n, π[n − 1], π[L − 1], … sampai 0, lalu balik urutannya.',
        ],
    ],

    [
        'slug' => 'hitung-awalan',
        'lesson' => 'kmp',
        'title' => 'Motif Pembuka Lagu',
        'difficulty' => 'Sulit',
        'tags' => ['string', 'fungsi prefiks', 'menghitung kemunculan'],
        'statement' => '<p>Seorang komposer menuliskan melodi lagunya sebagai string <strong>S</strong> berisi huruf kecil, satu huruf untuk setiap nada. Ia penasaran seberapa sering bagian pembuka lagu terdengar kembali di tengah lagu.</p>
<p>Untuk setiap panjang <strong>i</strong> dari 1 sampai |S|, hitung berapa kali awalan S sepanjang i muncul sebagai potongan berurutan di S. Kemunculan boleh tumpang tindih, dan kemunculan di awal lagu ikut dihitung.</p>',
        'input_format' => '<p>Satu baris berisi string <code>S</code>.</p>',
        'output_format' => '<p>|S| bilangan dalam satu baris, dipisah spasi. Bilangan ke-i adalah banyak kemunculan awalan sepanjang i.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 200 000</li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code>.</li></ul>',
        'samples' => [
            ['input' => "ababa\n", 'explanation' => '<code>a</code> muncul di posisi 1, 3, dan 5. <code>ab</code> dan <code>aba</code> masing-masing muncul di posisi 1 dan 3 (dua kemunculan <code>aba</code> sama-sama memakai huruf ke-3). <code>abab</code> dan <code>ababa</code> hanya muncul sekali.'],
            ['input' => "aaaa\n", 'explanation' => 'Awalan sepanjang i muncul di setiap posisi 1, 2, …, 5 − i.'],
            ['input' => "abcab\n", 'explanation' => '<code>a</code> dan <code>ab</code> muncul di posisi 1 dan 4. Awalan yang lebih panjang hanya muncul di awal.'],
        ],
        'tests' => function () use ($AZ, $fibWord, $zimin) {
            return [
                "a\n",
                "ab\n",
                "aa\n",
                T::word(12, 'ab')."\n",
                T::word(1000, 'ab')."\n",
                str_repeat('a', 200000)."\n",
                str_repeat('ab', 60000)."\n",
                T::word(200000, $AZ)."\n",
                T::word(200000, 'ab')."\n",
                $fibWord(200000)."\n",
                str_repeat(T::word(50, 'abc'), 4000)."\n",
                $zimin(17)."\n",
                T::word(200000, 'aaaaaaaaab')."\n",
            ];
        },
        'solve' => function (string $input) use ($prefiks) {
            $s = trim($input);
            $n = strlen($s);
            $pi = $prefiks($s);
            $cnt = array_fill(0, $n + 1, 0);
            foreach ($pi as $v) {
                $cnt[$v]++;
            }
            for ($L = $n - 1; $L > 0; $L--) {
                $cnt[$pi[$L - 1]] += $cnt[$L];
            }
            $out = [];
            for ($L = 1; $L <= $n; $L++) {
                $out[] = $cnt[$L] + 1;
            }

            return implode(' ', $out);
        },
        'starter' => $st("    string s;\n    cin >> s;", 'const s = readLine().trim();', 's = input().strip()'),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$piCpp.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int n = s.size();
    vector<int> pi = fungsiPrefiks(s);

    // cnt[L] = banyak posisi akhir j (selain kemunculan di awal) tempat awalan sepanjang L berakhir.
    // Langkah 1: setiap j menyumbang hanya ke border terpanjangnya, pi[j].
    vector<int> cnt(n + 1, 0);
    for (int j = 0; j < n; j++) cnt[pi[j]]++;
    // Langkah 2: setiap ujung yang punya border L juga punya border pi[L-1].
    // Proses L dari besar ke kecil agar cnt[L] sudah lengkap sebelum diteruskan.
    for (int L = n - 1; L > 0; L--) cnt[pi[L - 1]] += cnt[L];

    // Langkah 3: +1 untuk kemunculan awalan itu sendiri di posisi 1.
    string out;
    for (int L = 1; L <= n; L++) {
        if (L > 1) out += ' ';
        out += to_string(cnt[L] + 1);
    }
    cout << out << '\n';
    return 0;
}
CODE,
            'javascript' => "const s = readLine().trim();\n".$piJs.<<<'CODE'

const n = s.length;
const pi = fungsiPrefiks(s);
const cnt = new Int32Array(n + 1);
for (let j = 0; j < n; j++) cnt[pi[j]]++;                 // border terpanjang setiap ujung
for (let L = n - 1; L > 0; L--) cnt[pi[L - 1]] += cnt[L]; // turunkan ke border berikutnya
const out = new Array(n);
for (let L = 1; L <= n; L++) out[L - 1] = cnt[L] + 1;    // +1: kemunculan di awal
console.log(out.join(" "));
CODE,
            'python' => "import sys\ninput = sys.stdin.readline\n\n".$piPy.<<<'CODE'


s = input().strip()
n = len(s)
pi = fungsi_prefiks(s)

cnt = [0] * (n + 1)
for v in pi:                      # border terpanjang setiap ujung
    cnt[v] += 1
for L in range(n - 1, 0, -1):     # turunkan ke border berikutnya dalam rantai
    cnt[pi[L - 1]] += cnt[L]
print(" ".join(str(cnt[L] + 1) for L in range(1, n + 1)))   # +1: kemunculan di awal
CODE,
        ],
        'editorial' => '<p>Lihat setiap posisi akhir j (0-based). Awalan sepanjang L muncul dan berakhir tepat di j jika dan hanya jika L = j + 1 (itulah kemunculan di awal) atau L adalah border dari S[0..j]. Border dari S[0..j] adalah rantai π[j], π[π[j] − 1], …. Jadi jawaban untuk L adalah 1 ditambah banyak j yang rantai border-nya memuat L.</p>
<p>Menelusuri seluruh rantai untuk setiap j bisa O(n²) (misalnya pada <code>aaa…a</code>, rantainya sepanjang j). Triknya adalah menunda penelusuran:</p>
<ol><li>Untuk setiap j, tambahkan hanya ke border terpanjangnya: <code>cnt[π[j]]++</code>.</li>
<li>Untuk L dari n − 1 turun ke 1: <code>cnt[π[L − 1]] += cnt[L]</code>. Setiap ujung yang punya border L juga punya border π[L − 1] (border terpanjang dari border itu). Karena π[L − 1] &lt; L, ketika L diproses semua sumbangan dari panjang yang lebih besar sudah masuk ke cnt[L].</li>
<li>Jawaban untuk L adalah <code>cnt[L] + 1</code>.</li></ol>
<p>Total O(n).</p>
<p>Alternatif dengan Z-function: awalan sepanjang L muncul di posisi j jika dan hanya jika z[j] ≥ L (dengan z[0] = n). Hitung frekuensi setiap nilai z, lalu jumlahkan dari L besar ke kecil.</p>
<p><strong>Jebakan:</strong> mencari setiap awalan dengan KMP terpisah memakan O(n²). Jangan lupa +1 untuk kemunculan di awal, dan kumpulkan output menjadi satu string karena ada n bilangan.</p>',
        'hints' => [
            'Jika awalan sepanjang L muncul dan berakhir di posisi j, apa hubungan L dengan potongan S[0..j]?',
            'L harus berupa border dari S[0..j] (atau L = j + 1). Menelusuri seluruh rantai border untuk setiap j terlalu lambat; mulai dengan menghitung cnt[π[j]]++ saja.',
            'Proses L dari besar ke kecil: cnt[π[L − 1]] += cnt[L]. Jawaban untuk L adalah cnt[L] + 1.',
        ],
    ],
];
