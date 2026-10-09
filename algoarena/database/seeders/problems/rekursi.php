<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Rekursi & Brute Force: rekursi/backtracking dan enumerasi subset (bitmask).
 * Semua kode C++ harus lolos C++14: tanpa structured binding.
 */

return [
    // ───────────────────────── Rekursi & Backtracking ─────────────────────────
    [
        'slug' => 'menara-hanoi',
        'lesson' => 'rekursi',
        'title' => 'Menara Hanoi',
        'difficulty' => 'Mudah',
        'tags' => ['rekursi'],
        'statement' => '<p>Ada tiga tiang bernomor 1, 2, dan 3. Di tiang 1 tersusun <strong>n</strong> cakram dengan ukuran berbeda, yang terbesar di bawah. Pindahkan semua cakram ke tiang 3 dengan aturan:</p>
<ul><li>setiap langkah memindahkan satu cakram teratas dari satu tiang ke tiang lain;</li><li>cakram yang lebih besar tidak boleh diletakkan di atas cakram yang lebih kecil.</li></ul>
<p>Cetak cara dengan <strong>banyak langkah paling sedikit</strong> (cara tersebut selalu tunggal).</p>',
        'input_format' => '<p>Satu bilangan <code>n</code>.</p>',
        'output_format' => '<p>Baris pertama berisi banyak langkah k. Setiap dari k baris berikutnya berisi <code>a b</code>: pindahkan cakram teratas dari tiang a ke tiang b.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 16</li></ul>',
        'samples' => [
            ['input' => "2\n", 'explanation' => 'Cakram kecil ke tiang 2, cakram besar ke tiang 3, lalu cakram kecil menyusul ke tiang 3.'],
            ['input' => "3\n", 'explanation' => 'Pindahkan 2 cakram atas ke tiang 2 (3 langkah), cakram terbesar ke tiang 3, lalu 2 cakram dari tiang 2 ke tiang 3 (3 langkah).'],
        ],
        'tests' => function () {
            return ["1\n", "4\n", "5\n", "8\n", "10\n", "13\n", "15\n", "16\n"];
        },
        'solve' => function (string $input) {
            $n = (int) trim($input);
            $out = [];
            $h = function (int $k, int $dari, int $ke, int $bantu) use (&$h, &$out) {
                if ($k == 0) {
                    return;
                }
                $h($k - 1, $dari, $bantu, $ke);
                $out[] = "$dari $ke";
                $h($k - 1, $bantu, $ke, $dari);
            };
            $h($n, 1, 3, 2);

            return count($out)."\n".implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

// pindahkan k cakram teratas dari tiang 'dari' ke tiang 'ke', memakai 'bantu'
void hanoi(int k, int dari, int ke, int bantu) {
}

int main() {
    int n;
    cin >> n;
    hanoi(n, 1, 3, 2);
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const out = [];
// hanoi(k, dari, ke, bantu): pindahkan k cakram teratas dari 'dari' ke 'ke'
CODE,
            'python' => <<<'CODE'
n = int(input())
out = []

def hanoi(k, dari, ke, bantu):
    # pindahkan k cakram teratas dari 'dari' ke 'ke', memakai 'bantu'
    pass
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

string out;
int langkah = 0;

// pindahkan k cakram teratas dari 'dari' ke 'ke', memakai 'bantu'
void hanoi(int k, int dari, int ke, int bantu) {
    if (k == 0) return;                  // base case: tidak ada yang dipindah
    hanoi(k - 1, dari, bantu, ke);       // singkirkan k-1 cakram ke tiang bantu
    out += to_string(dari);
    out += ' ';
    out += to_string(ke);
    out += '\n';
    langkah++;                           // cakram terbesar langsung ke tujuan
    hanoi(k - 1, bantu, ke, dari);       // k-1 cakram menyusul ke tujuan
}

int main() {
    int n;
    cin >> n;
    hanoi(n, 1, 3, 2);
    cout << langkah << '\n' << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const out = [];
const hanoi = (k, dari, ke, bantu) => {
  if (k === 0) return;
  hanoi(k - 1, dari, bantu, ke);
  out.push(dari + " " + ke);
  hanoi(k - 1, bantu, ke, dari);
};
hanoi(n, 1, 3, 2);
console.log(out.length + "\n" + out.join("\n"));
CODE,
            'python' => <<<'CODE'
n = int(input())
out = []

def hanoi(k, dari, ke, bantu):
    if k == 0:
        return
    hanoi(k - 1, dari, bantu, ke)
    out.append(f"{dari} {ke}")
    hanoi(k - 1, bantu, ke, dari)

hanoi(n, 1, 3, 2)
print(len(out))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Cakram terbesar harus dipindah tepat satu kali, dari tiang 1 ke tiang 3. Saat itu terjadi, semua n − 1 cakram lain harus menumpuk di tiang 2 (tidak ada tempat lain). Jadi solusinya pasti berbentuk:</p>
<ol><li>pindahkan n − 1 cakram dari 1 ke 2 (memakai 3 sebagai bantuan),</li><li>pindahkan cakram terbesar dari 1 ke 3,</li><li>pindahkan n − 1 cakram dari 2 ke 3 (memakai 1 sebagai bantuan).</li></ol>
<p>Ini rekursi dengan base case n = 0. Banyak langkahnya L(n) = 2L(n − 1) + 1 = 2<sup>n</sup> − 1, dan karena setiap langkah di atas terpaksa, cara terpendek itu tunggal. Untuk n = 16 ada 65 535 baris: kumpulkan output dalam satu string, jangan mencetak satu per satu dengan <code>endl</code>.</p>',
        'hints' => [
            'Pikirkan cakram terbesar: kapan ia bisa dipindah, dan di mana cakram lainnya saat itu?',
            'Tulis fungsi hanoi(k, dari, ke, bantu). Bagaimana memakai hanoi(k − 1, …) dua kali?',
            'Base case k = 0. Banyak langkahnya 2<sup>n</sup> − 1.',
        ],
    ],

    [
        'slug' => 'susun-huruf',
        'lesson' => 'rekursi',
        'title' => 'Susunan Huruf Berbeda',
        'difficulty' => 'Mudah',
        'tags' => ['backtracking', 'permutasi'],
        'statement' => '<p>Diberikan sebuah kata. Cetak semua susunan berbeda dari huruf-hurufnya (setiap huruf dipakai tepat sebanyak kemunculannya di kata itu), urut menurut abjad.</p>',
        'input_format' => '<p>Satu baris berisi kata <code>s</code> (huruf kecil).</p>',
        'output_format' => '<p>Baris pertama berisi banyak susunan k. Setiap dari k baris berikutnya berisi satu susunan, urut menurut abjad.</p>',
        'constraints' => '<ul><li>1 ≤ |s| ≤ 8</li><li>s hanya berisi huruf <code>a</code>–<code>z</code></li></ul>',
        'samples' => [
            ['input' => "aba\n", 'explanation' => 'Huruf a, a, b hanya bisa disusun menjadi aab, aba, dan baa. Susunan yang sama tidak dicetak dua kali.'],
        ],
        'tests' => function () {
            return [
                "a\n", "ab\n", "zyx\n", "abcdefgh\n", "aaaaaaaa\n", "aabbccdd\n", T::word(8, 'abc')."\n", T::word(7, 'xyz')."\n",
                "hgfedcba\n", T::word(8, 'aab')."\n",
            ];
        },
        'solve' => function (string $input) {
            // Referensi: next permutation buatan sendiri
            $a = str_split(trim($input));
            sort($a);
            $n = count($a);
            $out = [];
            while (true) {
                $out[] = implode('', $a);
                $i = $n - 2;
                while ($i >= 0 && $a[$i] >= $a[$i + 1]) {
                    $i--;
                }
                if ($i < 0) {
                    break;
                }
                $j = $n - 1;
                while ($a[$j] <= $a[$i]) {
                    $j--;
                }
                [$a[$i], $a[$j]] = [$a[$j], $a[$i]];
                $tail = array_reverse(array_slice($a, $i + 1));
                array_splice($a, $i + 1, count($tail), $tail);
            }

            return count($out)."\n".implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    string s;
    cin >> s;
    // hitung kemunculan setiap huruf, lalu susun huruf demi huruf (a dulu, baru b, ...)
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const s = readLine().trim();
// hitung kemunculan setiap huruf, lalu susun huruf demi huruf (a dulu, baru b, ...)
CODE,
            'python' => <<<'CODE'
s = input().strip()
# hitung kemunculan setiap huruf, lalu susun huruf demi huruf (a dulu, baru b, ...)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
int cnt[26];
string cur, out;
int total = 0;

void susun() {
    if ((int)cur.size() == n) {
        out += cur;
        out += '\n';
        total++;
        return;
    }
    for (int c = 0; c < 26; c++) {           // coba huruf dari a ke z → hasil urut abjad
        if (cnt[c] == 0) continue;           // setiap JENIS huruf hanya dicoba sekali per posisi
        cnt[c]--;
        cur.push_back(char('a' + c));
        susun();
        cur.pop_back();                      // batalkan pilihan (backtrack)
        cnt[c]++;
    }
}

int main() {
    string s;
    cin >> s;
    n = s.size();
    for (char ch : s) cnt[ch - 'a']++;
    susun();
    cout << total << '\n' << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const s = readLine().trim();
const n = s.length;
const cnt = new Array(26).fill(0);
for (const ch of s) cnt[ch.charCodeAt(0) - 97]++;
const cur = [], out = [];
const susun = () => {
  if (cur.length === n) { out.push(cur.join("")); return; }
  for (let c = 0; c < 26; c++) {
    if (cnt[c] === 0) continue;
    cnt[c]--;
    cur.push(String.fromCharCode(97 + c));
    susun();
    cur.pop();
    cnt[c]++;
  }
};
susun();
console.log(out.length + "\n" + out.join("\n"));
CODE,
            'python' => <<<'CODE'
s = input().strip()
n = len(s)
cnt = [0] * 26
for ch in s:
    cnt[ord(ch) - 97] += 1
cur, out = [], []

def susun():
    if len(cur) == n:
        out.append("".join(cur))
        return
    for c in range(26):
        if cnt[c]:
            cnt[c] -= 1
            cur.append(chr(97 + c))
            susun()
            cur.pop()
            cnt[c] += 1

susun()
print(len(out))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Mencoba semua n! urutan posisi lalu membuang duplikat bisa saja, tetapi ada cara yang lebih rapi: simpan <strong>banyak sisa</strong> setiap huruf, lalu isi posisi satu per satu. Di setiap posisi, coba setiap <em>jenis</em> huruf yang masih tersisa, dari a ke z.</p>
<p>Karena setiap jenis huruf dicoba sekali per posisi, susunan yang sama tidak pernah muncul dua kali. Karena huruf dicoba berurutan a → z, hasilnya otomatis urut abjad. Setelah rekursi kembali, kembalikan hurufnya (backtrack).</p>
<p>Alternatif singkat di C++: urutkan s lalu pakai <code>do { … } while (next_permutation(s.begin(), s.end()));</code>, yang juga melewati duplikat. Paling banyak 8! = 40 320 baris.</p>',
        'hints' => [
            'Daripada menukar posisi huruf, hitung dulu ada berapa a, berapa b, dan seterusnya.',
            'Isi posisi satu per satu: coba setiap jenis huruf yang masih tersisa, dari a ke z, lalu kembalikan setelah rekursi.',
            'Mencoba setiap JENIS huruf (bukan setiap posisi huruf) sekali per posisi otomatis mencegah duplikat.',
        ],
    ],

    [
        'slug' => 'kombinasi-target',
        'lesson' => 'rekursi',
        'title' => 'Kombinasi Koin Bertarget',
        'difficulty' => 'Sedang',
        'tags' => ['backtracking', 'pemangkasan'],
        'statement' => '<p>Di dompetmu ada <strong>n</strong> koin dengan nilai <code>a<sub>1</sub>, …, a<sub>n</sub></code> (nilainya boleh sama). Kamu ingin membayar tepat <strong>S</strong>. Cetak semua <strong>kombinasi nilai</strong> yang berbeda: setiap kombinasi ditulis sebagai daftar nilai koin yang naik, dan dua kombinasi dianggap sama jika daftar nilainya sama (meskipun koin fisiknya berbeda).</p>
<p>Urutkan kombinasi secara leksikografis sebagai barisan bilangan (bandingkan elemen pertama, lalu kedua, dan seterusnya).</p>',
        'input_format' => '<p>Baris pertama berisi <code>n S</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>n</sub></code>.</p>',
        'output_format' => '<p>Baris pertama berisi banyak kombinasi k. Setiap dari k baris berikutnya berisi satu kombinasi (nilai-nilai naik, dipisah spasi).</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 30</li><li>1 ≤ a<sub>i</sub> ≤ 50</li><li>1 ≤ S ≤ 40</li></ul>',
        'samples' => [
            ['input' => "7 8\n10 1 2 7 6 1 5\n", 'explanation' => 'Kombinasinya: 1+1+6, 1+2+5, 1+7, dan 2+6. Kombinasi 1+7 hanya dicetak sekali meskipun ada dua koin bernilai 1.'],
            ['input' => "2 5\n2 2\n", 'explanation' => 'Tidak ada kombinasi yang berjumlah 5.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi, int $S) {
                return "$n $S\n".T::join(T::arr($n, 1, $hi))."\n";
            };
            $banyak = [];
            for ($v = 1; count($banyak) < 30; $v++) {
                for ($c = 0; $c < max(1, intdiv(12, $v)) && count($banyak) < 30; $c++) {
                    $banyak[] = $v;
                }
            }
            T::shuffle($banyak);

            return [
                "1 1\n1\n", "1 3\n2\n", "30 40\n".T::join(array_fill(0, 30, 1))."\n", "30 40\n".T::join($banyak)."\n",
                $gen(10, 10, 15), $gen(20, 20, 30), $gen(30, 50, 40), $gen(30, 8, 40), $gen(30, 3, 33), $gen(25, 40, 40),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: rekursi per nilai berbeda (banyak salinan), lalu urutkan hasilnya
            $lines = T::lines($input);
            [$n, $S] = T::ints($lines[0]);
            $cnt = array_count_values(T::ints($lines[1]));
            ksort($cnt);
            $vals = array_keys($cnt);
            $res = [];
            $cur = [];
            $go = function (int $j, int $sisa) use (&$go, &$res, &$cur, $vals, $cnt) {
                if ($sisa == 0) {
                    $res[] = $cur;

                    return;
                }
                if ($j == count($vals) || $vals[$j] > $sisa) {
                    return;
                }
                $v = $vals[$j];
                for ($c = 0; $c <= $cnt[$v] && $c * $v <= $sisa; $c++) {
                    $go($j + 1, $sisa - $c * $v);
                    $cur[] = $v;
                }
                array_splice($cur, count($cur) - $c);
            };
            $go(0, $S);
            usort($res, function ($x, $y) {
                for ($i = 0; $i < min(count($x), count($y)); $i++) {
                    if ($x[$i] != $y[$i]) {
                        return $x[$i] <=> $y[$i];
                    }
                }

                return count($x) <=> count($y);
            });

            return count($res)."\n".implode("\n", array_map(fn ($r) => implode(' ', $r), $res));
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, S;
    cin >> n >> S;
    vector<int> a(n);
    for (auto& v : a) cin >> v;
    sort(a.begin(), a.end());

    // cari(mulai, sisa): pilih koin berikutnya dari indeks >= mulai
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, S] = readInts();
const a = readInts().sort((p, q) => p - q);
// cari(mulai, sisa): pilih koin berikutnya dari indeks >= mulai
CODE,
            'python' => <<<'CODE'
n, S = map(int, input().split())
a = sorted(map(int, input().split()))
# cari(mulai, sisa): pilih koin berikutnya dari indeks >= mulai
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<int> a, cur;
vector<string> hasil;

void cari(int mulai, int sisa) {
    if (sisa == 0) {
        string t;
        for (size_t k = 0; k < cur.size(); k++) {
            if (k) t += ' ';
            t += to_string(cur[k]);
        }
        hasil.push_back(t);
        return;
    }
    for (int i = mulai; i < n; i++) {
        if (a[i] > sisa) break;                        // terurut: koin berikutnya lebih besar lagi
        if (i > mulai && a[i] == a[i - 1]) continue;   // nilai sama di posisi ini sudah dicoba
        cur.push_back(a[i]);
        cari(i + 1, sisa - a[i]);
        cur.pop_back();
    }
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int S;
    cin >> n >> S;
    a.resize(n);
    for (auto& v : a) cin >> v;
    sort(a.begin(), a.end());
    cari(0, S);
    cout << hasil.size() << '\n';
    for (auto& t : hasil) cout << t << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, S] = readInts();
const a = readInts().sort((p, q) => p - q);
const cur = [], hasil = [];
const cari = (mulai, sisa) => {
  if (sisa === 0) { hasil.push(cur.join(" ")); return; }
  for (let i = mulai; i < n; i++) {
    if (a[i] > sisa) break;
    if (i > mulai && a[i] === a[i - 1]) continue;
    cur.push(a[i]);
    cari(i + 1, sisa - a[i]);
    cur.pop();
  }
};
cari(0, S);
console.log([hasil.length, ...hasil].join("\n"));
CODE,
            'python' => <<<'CODE'
n, S = map(int, input().split())
a = sorted(map(int, input().split()))
cur, hasil = [], []

def cari(mulai, sisa):
    if sisa == 0:
        hasil.append(" ".join(map(str, cur)))
        return
    for i in range(mulai, n):
        if a[i] > sisa:
            break
        if i > mulai and a[i] == a[i - 1]:
            continue
        cur.append(a[i])
        cari(i + 1, sisa - a[i])
        cur.pop()

cari(0, S)
print(len(hasil))
if hasil:
    print("\n".join(hasil))
CODE,
        ],
        'editorial' => '<p>Urutkan koin, lalu bangun kombinasi dari nilai terkecil: <code>cari(mulai, sisa)</code> memilih koin berikutnya dari indeks ≥ mulai, sehingga setiap kombinasi tersusun naik.</p>
<ul><li><strong>Duplikat.</strong> Di satu tingkat rekursi, nilai yang sama cukup dicoba sekali: lewati <code>a[i]</code> jika <code>i &gt; mulai</code> dan <code>a[i] == a[i − 1]</code>. Koin kembar tetap bisa dipakai bersama (misalnya 1 + 1 + 6), karena yang kedua dipilih di tingkat berikutnya.</li>
<li><strong>Pemangkasan.</strong> Karena terurut dan positif, begitu <code>a[i] &gt; sisa</code>, semua koin setelahnya juga terlalu besar: <code>break</code>.</li>
<li><strong>Urutan.</strong> Kombinasi dengan elemen pertama lebih kecil dihasilkan lebih dulu, dan seterusnya di setiap tingkat, sehingga output otomatis leksikografis.</li></ul>
<p>Tanpa pemangkasan, 2<sup>30</sup> subset terlalu banyak; dengan S ≤ 40 yang ditelusuri hanya kombinasi berjumlah ≤ S.</p>',
        'hints' => [
            'Urutkan koin dan bangun kombinasi dari kecil ke besar: setelah memilih koin ke-i, koin berikutnya hanya dari indeks > i.',
            'Agar kombinasi yang sama tidak tercetak dua kali: di satu tingkat rekursi, jangan mencoba nilai yang sama dua kali.',
            'Begitu a[i] lebih besar dari sisa, hentikan loop: koin setelahnya pasti lebih besar.',
        ],
    ],

    [
        'slug' => 'ratu-terlarang',
        'lesson' => 'rekursi',
        'title' => 'Ratu di Papan Berlubang',
        'difficulty' => 'Sulit',
        'tags' => ['backtracking', 'bitmask', 'n-queens'],
        'statement' => '<p>Papan catur berukuran <strong>n × n</strong> punya beberapa petak berlubang (<code>#</code>) yang tidak boleh ditempati. Petak lain ditandai <code>.</code>.</p>
<p>Ada berapa cara menaruh <strong>n</strong> ratu di petak-petak yang tidak berlubang sehingga tidak ada dua ratu yang saling menyerang (sebaris, sekolom, atau sediagonal)? Lubang tidak menghalangi serangan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi n karakter <code>.</code> atau <code>#</code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak cara.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 11</li></ul>',
        'samples' => [
            ['input' => "4\n.#..\n....\n....\n....\n", 'explanation' => 'Papan 4 × 4 punya dua solusi: kolom (2, 4, 1, 3) dan (3, 1, 4, 2) untuk baris 1–4. Yang pertama memakai petak berlubang di baris 1 kolom 2, jadi tinggal 1 cara.'],
        ],
        'tests' => function () {
            $board = function (int $n, float $p) {
                $s = "$n\n";
                for ($r = 0; $r < $n; $r++) {
                    $row = '';
                    for ($c = 0; $c < $n; $c++) {
                        $row .= mt_rand() / mt_getrandmax() < $p ? '#' : '.';
                    }
                    $s .= $row."\n";
                }

                return $s;
            };

            return [
                "1\n.\n", "1\n#\n", "3\n...\n...\n...\n", $board(8, 0.0), $board(11, 0.0), $board(6, 0.1), $board(10, 0.08),
                $board(11, 0.12), $board(11, 0.3), $board(9, 0.05),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: backtracking biasa dengan array kolom & diagonal
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $g = array_slice($lines, 1, $n);
            $col = array_fill(0, $n, false);
            $d1 = array_fill(0, 2 * $n, false);
            $d2 = array_fill(0, 2 * $n, false);
            $cara = 0;
            $go = function (int $r) use (&$go, &$col, &$d1, &$d2, &$cara, $n, $g) {
                if ($r == $n) {
                    $cara++;

                    return;
                }
                for ($c = 0; $c < $n; $c++) {
                    if ($g[$r][$c] == '#' || $col[$c] || $d1[$r + $c] || $d2[$r - $c + $n]) {
                        continue;
                    }
                    $col[$c] = $d1[$r + $c] = $d2[$r - $c + $n] = true;
                    $go($r + 1);
                    $col[$c] = $d1[$r + $c] = $d2[$r - $c + $n] = false;
                }
            };
            $go(0);

            return (string) $cara;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<string> g;
long long cara = 0;

// taruh ratu baris demi baris; catat kolom & diagonal yang sudah terserang

int main() {
    cin >> n;
    g.resize(n);
    for (auto& row : g) cin >> row;
    cout << cara << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const g = [];
for (let i = 0; i < n; i++) g.push(readLine().trim());
// taruh ratu baris demi baris; catat kolom & diagonal yang sudah terserang
CODE,
            'python' => <<<'CODE'
n = int(input())
g = [input().strip() for _ in range(n)]
# taruh ratu baris demi baris; catat kolom & diagonal yang sudah terserang
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n, penuh;
vector<int> lubang;                 // bit c di lubang[r] = petak (r, c) berlubang
long long cara = 0;

// kol, d1, d2: kolom yang terserang di baris r (sudah digeser untuk diagonal)
void taruh(int r, int kol, int d1, int d2) {
    if (r == n) {
        cara++;
        return;
    }
    int bebas = penuh & ~(kol | d1 | d2 | lubang[r]);
    while (bebas) {
        int b = bebas & -bebas;      // ambil satu kolom bebas (bit 1 terendah)
        bebas ^= b;
        taruh(r + 1, kol | b, ((d1 | b) << 1) & penuh, (d2 | b) >> 1);
    }
}

int main() {
    cin >> n;
    penuh = (1 << n) - 1;
    lubang.assign(n, 0);
    for (int r = 0; r < n; r++) {
        string row;
        cin >> row;
        for (int c = 0; c < n; c++)
            if (row[c] == '#') lubang[r] |= 1 << c;
    }
    taruh(0, 0, 0, 0);
    cout << cara << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const penuh = (1 << n) - 1;
const lubang = [];
for (let r = 0; r < n; r++) {
  const row = readLine().trim();
  let m = 0;
  for (let c = 0; c < n; c++) if (row[c] === "#") m |= 1 << c;
  lubang.push(m);
}
let cara = 0;
const taruh = (r, kol, d1, d2) => {
  if (r === n) { cara++; return; }
  let bebas = penuh & ~(kol | d1 | d2 | lubang[r]);
  while (bebas) {
    const b = bebas & -bebas;
    bebas ^= b;
    taruh(r + 1, kol | b, ((d1 | b) << 1) & penuh, (d2 | b) >> 1);
  }
};
taruh(0, 0, 0, 0);
console.log(String(cara));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.read().split()
    n = int(data[0])
    penuh = (1 << n) - 1
    lubang = []
    for r in range(n):
        m = 0
        for c, ch in enumerate(data[1 + r]):
            if ch == "#":
                m |= 1 << c
        lubang.append(m)
    cara = 0

    def taruh(r, kol, d1, d2):
        nonlocal cara
        if r == n:
            cara += 1
            return
        bebas = penuh & ~(kol | d1 | d2 | lubang[r])
        while bebas:
            b = bebas & -bebas
            bebas ^= b
            taruh(r + 1, kol | b, ((d1 | b) << 1) & penuh, (d2 | b) >> 1)

    taruh(0, 0, 0, 0)
    print(cara)

main()
CODE,
        ],
        'editorial' => '<p>Ini N-Queens dengan satu tambahan: petak berlubang. Taruh ratu baris demi baris (setiap baris tepat satu ratu). Agar cepat, simpan petak yang terserang di baris saat ini sebagai tiga bitmask:</p>
<ul><li><code>kol</code>: kolom yang sudah terisi;</li><li><code>d1</code>: diagonal "\" — setiap turun satu baris, serangannya bergeser satu kolom ke kanan (<code>&lt;&lt; 1</code>);</li><li><code>d2</code>: diagonal "/" — bergeser ke kiri (<code>&gt;&gt; 1</code>).</li></ul>
<p>Petak berlubang cukup ditambahkan ke daftar terlarang: <code>bebas = penuh &amp; ~(kol | d1 | d2 | lubang[r])</code>. Ambil kolom bebas satu per satu dengan <code>b = bebas &amp; −bebas</code>.</p>
<p>Untuk n = 11 tanpa lubang ada 2 680 solusi dan sekitar 1,7·10<sup>5</sup> simpul rekursi: sangat cepat. Lubang hanya memangkas lebih banyak cabang.</p>',
        'hints' => [
            'Setiap baris berisi tepat satu ratu. Taruh ratu baris demi baris secara rekursif.',
            'Catat kolom dan dua arah diagonal yang sudah terserang. Lubang cukup diperlakukan seperti petak terserang.',
            'Versi bitmask: bebas = penuh & ~(kol | d1 | d2 | lubang[r]); saat turun ke baris berikutnya, geser d1 ke kiri dan d2 ke kanan.',
        ],
    ],

    // ───────────────────────── Enumerasi Subset & Bitmask ─────────────────────────
    [
        'slug' => 'jadwal-kursus',
        'lesson' => 'enumerasi-subset',
        'title' => 'Jadwal Kursus Tanpa Bentrok',
        'difficulty' => 'Mudah',
        'tags' => ['bitmask', 'brute force'],
        'statement' => '<p>Ada <strong>n</strong> kursus. Kursus ke-i bernilai <code>c<sub>i</sub></code> SKS dan memakai beberapa jam pelajaran (jam 1 sampai 20) dalam seminggu. Dua kursus <strong>bentrok</strong> jika ada jam yang dipakai keduanya.</p>
<p>Pilih sebagian kursus yang tidak saling bentrok dengan total SKS sebesar mungkin.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi <code>c<sub>i</sub> k<sub>i</sub> t<sub>1</sub> … t<sub>k<sub>i</sub></sub></code>: SKS, banyak jam, lalu daftar jamnya (berbeda).</p>',
        'output_format' => '<p>Satu bilangan: total SKS terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 15</li><li>1 ≤ c<sub>i</sub> ≤ 1000</li><li>1 ≤ k<sub>i</sub> ≤ 20, 1 ≤ t<sub>j</sub> ≤ 20</li></ul>',
        'samples' => [
            ['input' => "4\n3 2 1 2\n2 1 3\n4 2 2 3\n2 1 4\n", 'explanation' => 'Kursus 3 (4 SKS) bentrok dengan kursus 1 (jam 2) dan kursus 2 (jam 3). Pilihan terbaik kursus 1, 2, dan 4: 3 + 2 + 2 = 7 SKS.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $kMax) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $k = mt_rand(1, $kMax);
                    $jam = range(1, 20);
                    T::shuffle($jam);
                    $rows[] = mt_rand(1, 1000).' '.$k.' '.T::join(array_slice($jam, 0, $k));
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n5 3 1 2 3\n", "2\n5 1 7\n6 1 7\n", $gen(5, 3), $gen(10, 2), $gen(15, 1), $gen(15, 2), $gen(15, 3),
                $gen(15, 6), $gen(15, 20), $gen(12, 4),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: rekursi ambil/lewati
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $c = [];
            $m = [];
            for ($i = 0; $i < $n; $i++) {
                $v = T::ints($lines[$i + 1]);
                $c[] = $v[0];
                $mask = 0;
                for ($j = 0; $j < $v[1]; $j++) {
                    $mask |= 1 << ($v[2 + $j] - 1);
                }
                $m[] = $mask;
            }
            $best = 0;
            $go = function (int $i, int $pakai, int $sks) use (&$go, &$best, $n, $c, $m) {
                if ($i == $n) {
                    $best = max($best, $sks);

                    return;
                }
                if (($pakai & $m[$i]) == 0) {
                    $go($i + 1, $pakai | $m[$i], $sks + $c[$i]);
                }
                $go($i + 1, $pakai, $sks);
            };
            $go(0, 0, 0);

            return (string) $best;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<int> sks(n), jam(n, 0);      // jam[i]: bitmask jam pelajaran kursus i
    for (int i = 0; i < n; i++) {
        int k;
        cin >> sks[i] >> k;
        for (int j = 0; j < k; j++) {
            int t;
            cin >> t;
            jam[i] |= 1 << (t - 1);
        }
    }
    // coba semua 2^n pilihan kursus
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const sks = [], jam = [];
for (let i = 0; i < n; i++) {
  const v = readInts();
  sks.push(v[0]);
  let m = 0;
  for (let j = 0; j < v[1]; j++) m |= 1 << (v[2 + j] - 1);
  jam.push(m);
}
// coba semua 2^n pilihan kursus
CODE,
            'python' => <<<'CODE'
n = int(input())
sks, jam = [], []
for _ in range(n):
    v = list(map(int, input().split()))
    sks.append(v[0])
    m = 0
    for t in v[2:2 + v[1]]:
        m |= 1 << (t - 1)
    jam.append(m)
# coba semua 2^n pilihan kursus
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<int> sks(n), jam(n, 0);      // jam[i]: bitmask jam pelajaran kursus i
    for (int i = 0; i < n; i++) {
        int k;
        cin >> sks[i] >> k;
        for (int j = 0; j < k; j++) {
            int t;
            cin >> t;
            jam[i] |= 1 << (t - 1);
        }
    }

    int best = 0;
    for (int mask = 0; mask < (1 << n); mask++) {
        int terpakai = 0, total = 0;
        bool aman = true;
        for (int i = 0; i < n && aman; i++) {
            if (!(mask >> i & 1)) continue;
            if (terpakai & jam[i]) aman = false;     // ada jam yang sudah dipakai kursus lain
            terpakai |= jam[i];
            total += sks[i];
        }
        if (aman) best = max(best, total);
    }
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const sks = [], jam = [];
for (let i = 0; i < n; i++) {
  const v = readInts();
  sks.push(v[0]);
  let m = 0;
  for (let j = 0; j < v[1]; j++) m |= 1 << (v[2 + j] - 1);
  jam.push(m);
}
let best = 0;
for (let mask = 0; mask < 1 << n; mask++) {
  let terpakai = 0, total = 0, aman = true;
  for (let i = 0; i < n && aman; i++) {
    if (!((mask >> i) & 1)) continue;
    if (terpakai & jam[i]) aman = false;
    terpakai |= jam[i];
    total += sks[i];
  }
  if (aman && total > best) best = total;
}
console.log(String(best));
CODE,
            'python' => <<<'CODE'
n = int(input())
sks, jam = [], []
for _ in range(n):
    v = list(map(int, input().split()))
    sks.append(v[0])
    m = 0
    for t in v[2:2 + v[1]]:
        m |= 1 << (t - 1)
    jam.append(m)

best = 0
for mask in range(1 << n):
    terpakai = total = 0
    aman = True
    for i in range(n):
        if mask >> i & 1:
            if terpakai & jam[i]:
                aman = False
                break
            terpakai |= jam[i]
            total += sks[i]
    if aman and total > best:
        best = total
print(best)
CODE,
        ],
        'editorial' => '<p>Dengan n ≤ 15 hanya ada 2<sup>15</sup> = 32 768 pilihan kursus: coba semuanya. Agar pengecekan bentrok cepat, simpan jam setiap kursus sebagai bitmask 20 bit (bit t − 1 menyala jika jam t dipakai).</p>
<p>Untuk satu pilihan (mask), tambahkan kursus satu per satu sambil menyimpan gabungan jam yang sudah terpakai. Kursus i bentrok jika <code>terpakai &amp; jam[i]</code> tidak nol. Total O(2<sup>n</sup> · n).</p>',
        'hints' => [
            'n ≤ 15: berapa banyak kemungkinan pilihan kursus? Cukup kecil untuk dicoba semua.',
            'Simpan jam setiap kursus sebagai bitmask. Dua kursus bentrok jika AND keduanya tidak nol.',
            'Untuk setiap mask, gabungkan jam kursus yang dipilih dengan OR sambil memeriksa bentrok.',
        ],
    ],

    [
        'slug' => 'tim-lengkap',
        'lesson' => 'enumerasi-subset',
        'title' => 'Tim dengan Keahlian Lengkap',
        'difficulty' => 'Sedang',
        'tags' => ['bitmask', 'brute force', 'set cover'],
        'statement' => '<p>Ada <strong>n</strong> kandidat dan <strong>k</strong> keahlian yang dibutuhkan proyek (keahlian 1 sampai k). Setiap kandidat menguasai sebagian keahlian.</p>
<p>Bentuk tim yang menguasai <strong>semua</strong> k keahlian (cukup satu anggota yang menguasai setiap keahlian) dengan anggota sesedikit mungkin. Cetak ukuran tim terkecil dan ada berapa tim berbeda dengan ukuran itu.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n k</code>. Setiap dari n baris berikutnya berisi string biner panjang k: karakter ke-j bernilai <code>1</code> jika kandidat itu menguasai keahlian j.</p>',
        'output_format' => '<p>Dua bilangan: ukuran tim terkecil dan banyak tim dengan ukuran itu. Jika tidak mungkin, cetak <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 18</li><li>1 ≤ k ≤ 12</li></ul>',
        'samples' => [
            ['input' => "4 3\n110\n011\n001\n100\n", 'explanation' => 'Tidak ada yang menguasai ketiganya sendirian. Tim berdua yang lengkap: {1, 2}, {1, 3}, dan {2, 4}.'],
            ['input' => "2 2\n10\n10\n", 'explanation' => 'Tidak ada yang menguasai keahlian 2.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $k, float $p) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $s = '';
                    for ($j = 0; $j < $k; $j++) {
                        $s .= mt_rand() / mt_getrandmax() < $p ? '1' : '0';
                    }
                    $rows[] = $s;
                }

                return "$n $k\n".implode("\n", $rows)."\n";
            };

            return [
                "1 1\n1\n", "1 3\n101\n", "3 3\n111\n111\n111\n", $gen(8, 6, 0.3), $gen(12, 10, 0.25), $gen(18, 12, 0.15),
                $gen(18, 12, 0.3), $gen(18, 12, 0.6), $gen(18, 4, 0.5), $gen(18, 12, 0.08), $gen(16, 12, 0.2),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: DP per banyak anggota (BFS lapis) atas mask keahlian
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $s = [];
            for ($i = 0; $i < $n; $i++) {
                $s[] = bindec(strrev(trim($lines[$i + 1])));
            }
            $full = (1 << $k) - 1;
            // cara[mask keahlian] untuk tim berukuran t, dibangun dengan memilih anggota berindeks naik
            // (rekursi dengan pemangkasan ukuran)
            for ($t = 0; $t <= $n; $t++) {
                $cnt = 0;
                $go = function (int $mulai, int $sisa, int $cov) use (&$go, &$cnt, $n, $s, $full) {
                    if ($sisa == 0) {
                        if ($cov == $full) {
                            $cnt++;
                        }

                        return;
                    }
                    for ($i = $mulai; $i <= $n - $sisa; $i++) {
                        $go($i + 1, $sisa - 1, $cov | $s[$i]);
                    }
                };
                $go(0, $t, 0);
                if ($cnt > 0) {
                    return "$t $cnt";
                }
            }

            return '-1';
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, k;
    cin >> n >> k;
    vector<int> bisa(n, 0);             // bitmask keahlian kandidat i
    for (int i = 0; i < n; i++) {
        string s;
        cin >> s;
        for (int j = 0; j < k; j++)
            if (s[j] == '1') bisa[i] |= 1 << j;
    }
    // cov[mask] = gabungan keahlian anggota mask
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const bisa = [];
for (let i = 0; i < n; i++) {
  const s = readLine().trim();
  let m = 0;
  for (let j = 0; j < k; j++) if (s[j] === "1") m |= 1 << j;
  bisa.push(m);
}
// cov[mask] = gabungan keahlian anggota mask
CODE,
            'python' => <<<'CODE'
n, k = map(int, input().split())
bisa = []
for _ in range(n):
    s = input().strip()
    bisa.append(sum(1 << j for j in range(k) if s[j] == "1"))
# cov[mask] = gabungan keahlian anggota mask
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, k;
    cin >> n >> k;
    vector<int> bisa(n, 0);             // bitmask keahlian kandidat i
    for (int i = 0; i < n; i++) {
        string s;
        cin >> s;
        for (int j = 0; j < k; j++)
            if (s[j] == '1') bisa[i] |= 1 << j;
    }
    int full = (1 << k) - 1;

    // cov[mask] dihitung dari mask tanpa bit terendahnya: O(1) per mask
    vector<int> cov(1 << n, 0);
    int best = INT_MAX;
    long long banyak = 0;
    for (int mask = 1; mask < (1 << n); mask++) {
        int low = mask & -mask;
        int i = __builtin_ctz(mask);
        cov[mask] = cov[mask ^ low] | bisa[i];
        if (cov[mask] != full) continue;
        int ukuran = __builtin_popcount(mask);
        if (ukuran < best) {
            best = ukuran;
            banyak = 1;
        } else if (ukuran == best) {
            banyak++;
        }
    }
    if (best == INT_MAX) cout << -1 << '\n';
    else cout << best << ' ' << banyak << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const bisa = [];
for (let i = 0; i < n; i++) {
  const s = readLine().trim();
  let m = 0;
  for (let j = 0; j < k; j++) if (s[j] === "1") m |= 1 << j;
  bisa.push(m);
}
const full = (1 << k) - 1;
const cov = new Int32Array(1 << n);
const pc = new Uint8Array(1 << n);
let best = Infinity, banyak = 0;
for (let mask = 1; mask < 1 << n; mask++) {
  const low = mask & -mask;
  const i = 31 - Math.clz32(low);
  cov[mask] = cov[mask ^ low] | bisa[i];
  pc[mask] = pc[mask ^ low] + 1;
  if (cov[mask] !== full) continue;
  if (pc[mask] < best) { best = pc[mask]; banyak = 1; }
  else if (pc[mask] === best) banyak++;
}
console.log(best === Infinity ? "-1" : best + " " + banyak);
CODE,
            'python' => <<<'CODE'
n, k = map(int, input().split())
bisa = []
for _ in range(n):
    s = input().strip()
    bisa.append(sum(1 << j for j in range(k) if s[j] == "1"))
full = (1 << k) - 1

cov = [0] * (1 << n)
best, banyak = None, 0
for mask in range(1, 1 << n):
    low = mask & -mask
    c = cov[mask ^ low] | bisa[low.bit_length() - 1]
    cov[mask] = c
    if c == full:
        u = mask.bit_count()
        if best is None or u < best:
            best, banyak = u, 1
        elif u == best:
            banyak += 1
print(-1 if best is None else f"{best} {banyak}")
CODE,
        ],
        'editorial' => '<p>n ≤ 18, jadi semua 2<sup>18</sup> ≈ 2,6·10<sup>5</sup> tim bisa dicoba. Simpan keahlian setiap kandidat sebagai bitmask k bit; keahlian sebuah tim adalah OR dari keahlian anggotanya.</p>
<p>Menghitung OR dari nol untuk setiap mask butuh O(n). Lebih cepat: tim <code>mask</code> sama dengan tim <code>mask</code> tanpa anggota terendahnya, ditambah anggota itu. Jadi</p>
<p><code>cov[mask] = cov[mask ^ low] | bisa[ctz(mask)]</code> dengan <code>low = mask &amp; −mask</code>,</p>
<p>dan karena <code>mask ^ low &lt; mask</code>, nilainya sudah dihitung. Total O(2<sup>n</sup>). Untuk setiap tim yang lengkap, bandingkan popcount-nya dengan ukuran terbaik dan hitung yang seri. Jika tidak ada tim lengkap (bahkan semua kandidat sekaligus), cetak −1.</p>',
        'hints' => [
            'Simpan keahlian sebagai bitmask. Keahlian satu tim = OR keahlian anggotanya; tim lengkap jika hasilnya (1 << k) − 1.',
            'Coba semua 2<sup>n</sup> tim. Bisakah OR sebuah tim diturunkan dari tim yang lebih kecil?',
            'cov[mask] = cov[mask tanpa bit terendah] | bisa[indeks bit terendah]. Lalu bandingkan popcount.',
        ],
    ],

    [
        'slug' => 'jadwal-ujian',
        'lesson' => 'enumerasi-subset',
        'title' => 'Jadwal Ujian Tersingkat',
        'difficulty' => 'Sulit',
        'tags' => ['bitmask', 'iterasi submask', 'dp bitmask'],
        'statement' => '<p>Sekolah harus menyelenggarakan <strong>n</strong> ujian. Ujian ke-i berlangsung <code>d<sub>i</sub></code> menit, dan dalam satu hari total durasi ujian tidak boleh melebihi <strong>C</strong> menit. Selain itu ada <strong>m</strong> pasangan ujian yang <em>bentrok</em> (ada siswa yang mengikuti keduanya), sehingga tidak boleh diadakan pada hari yang sama.</p>
<p>Setiap ujian diadakan utuh dalam satu hari. Berapa hari minimum untuk menyelenggarakan semua ujian?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n C m</code>. Baris kedua berisi <code>d<sub>1</sub> … d<sub>n</sub></code>. Setiap dari m baris berikutnya berisi <code>u v</code>: ujian u dan v bentrok.</p>',
        'output_format' => '<p>Satu bilangan: banyak hari minimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 13</li><li>1 ≤ d<sub>i</sub> ≤ C ≤ 10<sup>9</sup></li><li>0 ≤ m ≤ n(n − 1)/2, pasangan berbeda dan u ≠ v</li></ul>',
        'samples' => [
            ['input' => "4 5 2\n2 3 3 2\n1 2\n1 3\n", 'explanation' => 'Ujian 1 hanya boleh bersama ujian 4. Ujian 2 dan 3 berdurasi total 6 &gt; 5, jadi tidak muat sehari. Contoh jadwal 3 hari: {1, 4}, {2}, {3}.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $C, int $dMin, float $p) {
                $d = T::arr($n, $dMin, $C);
                $e = [];
                for ($u = 1; $u <= $n; $u++) {
                    for ($v = $u + 1; $v <= $n; $v++) {
                        if (mt_rand() / mt_getrandmax() < $p) {
                            $e[] = mt_rand(0, 1) ? "$u $v" : "$v $u";
                        }
                    }
                }
                T::shuffle($e);

                return "$n $C ".count($e)."\n".T::join($d)."\n".implode("\n", $e).(count($e) ? "\n" : '');
            };

            return [
                "1 10 0\n10\n", "3 10 3\n1 1 1\n1 2\n2 3\n1 3\n", $gen(6, 10, 1, 0.2), $gen(10, 100, 1, 0.1),
                $gen(13, 1000000000, 1, 0.0), $gen(13, 1000000000, 1, 0.3), $gen(13, 100, 10, 0.1), $gen(13, 50, 1, 0.05),
                $gen(13, 1000, 300, 0.15), $gen(12, 20, 1, 0.5), $gen(13, 30, 5, 0.02),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: DP atas semua submask (tanpa trik bit terendah)
            $lines = T::lines($input);
            [$n, $C, $m] = T::ints($lines[0]);
            $d = T::ints($lines[1]);
            $adj = array_fill(0, $n, 0);
            for ($k = 0; $k < $m; $k++) {
                [$u, $v] = T::ints($lines[2 + $k]);
                $adj[$u - 1] |= 1 << ($v - 1);
                $adj[$v - 1] |= 1 << ($u - 1);
            }
            $N = 1 << $n;
            $ok = array_fill(0, $N, false);
            for ($s = 1; $s < $N; $s++) {
                $sum = 0;
                $good = true;
                for ($i = 0; $i < $n; $i++) {
                    if ($s >> $i & 1) {
                        $sum += $d[$i];
                        if ($adj[$i] & $s) {
                            $good = false;
                        }
                    }
                }
                $ok[$s] = $good && $sum <= $C;
            }
            $dp = array_fill(0, $N, PHP_INT_MAX);
            $dp[0] = 0;
            for ($mask = 1; $mask < $N; $mask++) {
                for ($s = $mask; $s > 0; $s = ($s - 1) & $mask) {
                    if ($ok[$s] && $dp[$mask ^ $s] + 1 < $dp[$mask]) {
                        $dp[$mask] = $dp[$mask ^ $s] + 1;
                    }
                }
            }

            return (string) $dp[$N - 1];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, m;
    long long C;
    cin >> n >> C >> m;
    vector<long long> d(n);
    for (auto& v : d) cin >> v;
    vector<int> bentrok(n, 0);
    for (int k = 0; k < m; k++) {
        int u, v;
        cin >> u >> v;
        u--; v--;
        bentrok[u] |= 1 << v;
        bentrok[v] |= 1 << u;
    }
    // ok[s]: bisakah himpunan ujian s diadakan dalam satu hari?
    // dp[mask]: hari minimum untuk ujian-ujian di mask
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, C, m] = readInts();
const d = readInts();
const bentrok = new Array(n).fill(0);
for (let k = 0; k < m; k++) {
  const [u, v] = readInts();
  bentrok[u - 1] |= 1 << (v - 1);
  bentrok[v - 1] |= 1 << (u - 1);
}
// ok[s]: bisakah himpunan ujian s diadakan dalam satu hari?
// dp[mask]: hari minimum untuk ujian-ujian di mask
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.read().split()
n, C, m = int(data[0]), int(data[1]), int(data[2])
d = list(map(int, data[3:3 + n]))
bentrok = [0] * n
for k in range(m):
    u, v = int(data[3 + n + 2 * k]) - 1, int(data[4 + n + 2 * k]) - 1
    bentrok[u] |= 1 << v
    bentrok[v] |= 1 << u
# ok[s]: bisakah himpunan ujian s diadakan dalam satu hari?
# dp[mask]: hari minimum untuk ujian-ujian di mask
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, m;
    long long C;
    cin >> n >> C >> m;
    vector<long long> d(n);
    for (auto& v : d) cin >> v;
    vector<int> bentrok(n, 0);
    for (int k = 0; k < m; k++) {
        int u, v;
        cin >> u >> v;
        u--; v--;
        bentrok[u] |= 1 << v;
        bentrok[v] |= 1 << u;
    }
    int N = 1 << n;

    // ok[s] dibangun dari s tanpa bit terendah: O(2^n)
    vector<long long> durasi(N, 0);
    vector<char> aman(N, 1), ok(N, 0);
    for (int s = 1; s < N; s++) {
        int low = s & -s, i = __builtin_ctz(s), r = s ^ low;
        durasi[s] = durasi[r] + d[i];
        aman[s] = aman[r] && !(bentrok[i] & r);
        ok[s] = aman[s] && durasi[s] <= C;
    }

    // dp[mask] = min hari. Hari yang memuat ujian terendah di mask dipilih dulu
    // (submask dari sisanya + bit terendah) agar setiap pembagian dihitung sekali.
    vector<int> dp(N, INT_MAX);
    dp[0] = 0;
    for (int mask = 1; mask < N; mask++) {
        int low = mask & -mask, rest = mask ^ low;
        for (int s = rest; ; s = (s - 1) & rest) {
            int hari = s | low;
            if (ok[hari] && dp[mask ^ hari] + 1 < dp[mask]) dp[mask] = dp[mask ^ hari] + 1;
            if (s == 0) break;
        }
    }
    cout << dp[N - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, C, m] = readInts();
const d = readInts();
const bentrok = new Array(n).fill(0);
for (let k = 0; k < m; k++) {
  const [u, v] = readInts();
  bentrok[u - 1] |= 1 << (v - 1);
  bentrok[v - 1] |= 1 << (u - 1);
}
const N = 1 << n;
const durasi = new Float64Array(N), aman = new Uint8Array(N), ok = new Uint8Array(N);
aman[0] = 1;
for (let s = 1; s < N; s++) {
  const low = s & -s, i = 31 - Math.clz32(low), r = s ^ low;
  durasi[s] = durasi[r] + d[i];
  aman[s] = aman[r] && !(bentrok[i] & r) ? 1 : 0;
  ok[s] = aman[s] && durasi[s] <= C ? 1 : 0;
}
const dp = new Int32Array(N).fill(1 << 30);
dp[0] = 0;
for (let mask = 1; mask < N; mask++) {
  const low = mask & -mask, rest = mask ^ low;
  for (let s = rest; ; s = (s - 1) & rest) {
    const hari = s | low;
    if (ok[hari] && dp[mask ^ hari] + 1 < dp[mask]) dp[mask] = dp[mask ^ hari] + 1;
    if (s === 0) break;
  }
}
console.log(String(dp[N - 1]));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.read().split()
    n, C, m = int(data[0]), int(data[1]), int(data[2])
    d = list(map(int, data[3:3 + n]))
    bentrok = [0] * n
    for k in range(m):
        u, v = int(data[3 + n + 2 * k]) - 1, int(data[4 + n + 2 * k]) - 1
        bentrok[u] |= 1 << v
        bentrok[v] |= 1 << u
    N = 1 << n
    durasi = [0] * N
    aman = [True] * N
    ok = [False] * N
    for s in range(1, N):
        low = s & -s
        i = low.bit_length() - 1
        r = s ^ low
        durasi[s] = durasi[r] + d[i]
        aman[s] = aman[r] and not (bentrok[i] & r)
        ok[s] = aman[s] and durasi[s] <= C

    INF = 1 << 30
    dp = [INF] * N
    dp[0] = 0
    for mask in range(1, N):
        low = mask & -mask
        rest = mask ^ low
        best = INF
        s = rest
        while True:
            hari = s | low
            if ok[hari]:
                v = dp[mask ^ hari] + 1
                if v < best:
                    best = v
            if s == 0:
                break
            s = (s - 1) & rest
        dp[mask] = best
    print(dp[N - 1])

main()
CODE,
        ],
        'editorial' => '<p><strong>Langkah 1: himpunan yang muat sehari.</strong> Untuk setiap himpunan ujian s, <code>ok[s]</code> benar jika total durasinya ≤ C dan tidak ada pasangan bentrok di dalamnya. Keduanya bisa diturunkan dari s tanpa bit terendah, jadi semua ok dihitung dalam O(2<sup>n</sup>).</p>
<p><strong>Langkah 2: DP atas himpunan.</strong> dp[mask] = hari minimum untuk menyelesaikan ujian-ujian di mask. Hari pertama adalah suatu submask s yang ok, sisanya dp[mask ^ s]:</p>
<p><code>dp[mask] = min over s ⊆ mask, ok[s] ( dp[mask ^ s] + 1 )</code></p>
<p>Menelusuri semua submask dari semua mask totalnya 3<sup>n</sup> ≈ 1,6·10<sup>6</sup> untuk n = 13. Trik tambahan: ujian dengan nomor terkecil di mask <em>pasti</em> ada di salah satu hari, jadi cukup coba hari yang memuatnya. Itu menghindari menghitung pembagian yang sama berkali-kali dan memotong kerja kira-kira setengahnya.</p>
<p>Mengapa tidak greedy (isi hari sepenuh mungkin)? Karena bentrok dan durasi saling mengunci: hari yang "penuh" bisa memaksa sisa ujian terpecah menjadi lebih banyak hari.</p>',
        'hints' => [
            'Mulai dari pertanyaan kecil: himpunan ujian s mana saja yang boleh diadakan dalam satu hari? Hitung untuk semua 2<sup>n</sup> himpunan.',
            'dp[mask] = hari minimum untuk ujian di mask. Hari pertama adalah salah satu submask s yang valid: dp[mask] = min(dp[mask ^ s] + 1).',
            'Telusuri submask dengan s = (s − 1) & mask. Total 3<sup>n</sup>. Bisa dihemat dengan mewajibkan hari pertama memuat ujian bernomor terkecil.',
        ],
    ],
];
