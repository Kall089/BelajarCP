<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Trie: menghitung kata berawalan sama, saran kata terkecil,
 * trie biner untuk XOR pasangan terbesar, dan prefix XOR + trie untuk subarray.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Input berformat kamus: N, N kata, Q, Q awalan (satu per baris). */
$masukan = function (array $kata, array $awalan): string {
    return count($kata)."\n".implode("\n", $kata)."\n".count($awalan)."\n".implode("\n", $awalan)."\n";
};

/** @return array{0: string[], 1: string[]} [kata, awalan] */
$bacaKamus = function (string $input): array {
    $lines = T::lines($input);
    $n = (int) $lines[0];
    $kata = [];
    for ($i = 1; $i <= $n; $i++) {
        $kata[] = trim($lines[$i]);
    }
    $q = (int) $lines[$n + 1];
    $awalan = [];
    for ($i = 0; $i < $q; $i++) {
        $awalan[] = trim($lines[$n + 2 + $i]);
    }

    return [$kata, $awalan];
};

/** Indeks pertama di $arr (terurut) yang >= $key. */
$lowerBound = function (array $arr, string $key): int {
    $lo = 0;
    $hi = count($arr);
    while ($lo < $hi) {
        $mid = ($lo + $hi) >> 1;
        if (strcmp($arr[$mid], $key) < 0) {
            $lo = $mid + 1;
        } else {
            $hi = $mid;
        }
    }

    return $lo;
};

/** N kata acak dengan panjang $lo..$hi. */
$kataAcak = function (int $n, int $lo, int $hi, string $abjad): array {
    $w = [];
    for ($i = 0; $i < $n; $i++) {
        $w[] = T::word(mt_rand($lo, $hi), $abjad);
    }

    return $w;
};

/** Q awalan: $persenAcak% acak, sisanya awalan dari kata di kamus (panjang ≤ $maxLen). */
$awalanAcak = function (array $kata, int $q, int $persenAcak, int $maxLen, string $abjad): array {
    $p = [];
    $n = count($kata);
    for ($i = 0; $i < $q; $i++) {
        if (mt_rand(1, 100) <= $persenAcak) {
            $p[] = T::word(mt_rand(1, $maxLen), $abjad);
        } else {
            $w = $kata[mt_rand(0, $n - 1)];
            $p[] = substr($w, 0, mt_rand(1, min($maxLen, strlen($w))));
        }
    }

    return $p;
};

/**
 * XOR terbesar dari dua elemen berbeda indeks (count >= 2), dengan himpunan awalan per bit.
 * Kunci hash diacak (m ^ (m >> 29), m = p · konstanta ganjil) karena tabel hash PHP memakai
 * bit rendah kunci bilangan bulat apa adanya: kelipatan 2^k akan bertabrakan semua.
 */
$xorPasangan = function (array $a): int {
    $ans = 0;
    for ($b = 29; $b >= 0; $b--) {
        $cand = $ans | (1 << $b);
        $t = $cand >> $b;
        $set = [];
        foreach ($a as $x) {
            $p = $x >> $b;
            $m = $p * 2654435761;
            $set[$m ^ ($m >> 29)] = $p;
        }
        foreach ($set as $p) {
            $m = ($p ^ $t) * 2654435761;
            if (isset($set[$m ^ ($m >> 29)])) {
                $ans = $cand;
                break;
            }
        }
    }

    return $ans;
};

$deret = function (int $n, callable $f): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = $f($i);
    }

    return "$n\n".implode(' ', $a)."\n";
};

return [
    [
        'slug' => 'kamus-awalan',
        'lesson' => 'trie',
        'title' => 'Kamus Awalan',
        'difficulty' => 'Mudah',
        'tags' => ['trie', 'string', 'awalan'],
        'statement' => '<p>Rani sedang menyusun kamus saku bahasa daerah. Ia sudah mencatat <strong>N</strong> kata dari berbagai buku; kata yang sama bisa saja tercatat lebih dari sekali. Teman-temannya sering bertanya, misalnya "ada berapa kata yang diawali <code>ba</code>?"</p>
<p>Bantu Rani menjawab <strong>Q</strong> pertanyaan seperti itu. Sebuah kata diawali awalan p jika huruf-huruf pertamanya persis sama dengan p; kata yang sama persis dengan p juga terhitung. Setiap kata dihitung sebanyak ia tercatat.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi satu kata. Baris berikutnya berisi <code>Q</code>, lalu Q baris masing-masing berisi satu awalan.</p>',
        'output_format' => '<p>Q baris: untuk setiap awalan, banyak kata yang diawali awalan tersebut.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 10<sup>5</sup></li><li>Setiap kata dan awalan terdiri atas huruf kecil <code>a</code>–<code>z</code> dan panjangnya minimal 1</li><li>Total panjang semua kata ≤ 10<sup>6</sup></li><li>Total panjang semua awalan ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "6\nbatik\nbatu\nbata\nbaju\nkapal\nbatu\n5\nbat\nba\nbatu\nkap\nc\n", 'explanation' => 'Kata yang diawali <code>bat</code> adalah batik, bata, dan batu yang tercatat dua kali, jadi jawabannya 4. Ditambah baju, ada 5 kata yang diawali <code>ba</code>. Awalan <code>batu</code> hanya cocok dengan dua catatan batu, dan tidak ada kata yang diawali <code>c</code>.'],
            ['input' => "3\napel\napi\na\n3\na\napelku\nap\n", 'explanation' => 'Kata <code>a</code> sendiri ikut dihitung untuk awalan <code>a</code>, jadi jawabannya 3. Awalan <code>apelku</code> lebih panjang daripada semua kata sehingga jawabannya 0, sedangkan <code>ap</code> mengawali apel dan api.'],
        ],
        'tests' => function () use ($masukan, $kataAcak, $awalanAcak) {
            $huruf = 'abcdefghijklmnopqrstuvwxyz';

            // Beberapa kata sangat panjang yang berbagi awalan panjang.
            $panjang = function () use ($masukan) {
                $dasar = T::word(100000, 'ab');
                $kata = [];
                for ($i = 0; $i < 10; $i++) {
                    $k = mt_rand(50000, 99990);
                    $kata[] = substr($dasar, 0, $k).T::word(100000 - $k, 'ab');
                }
                $aw = [];
                $tot = 0;
                while (true) {
                    $len = mt_rand(1, 20000);
                    if ($tot + $len > 1000000) {
                        break;
                    }
                    $s = substr($kata[mt_rand(0, 9)], 0, $len);
                    if (mt_rand(0, 3) === 0) {
                        $s[$len - 1] = $s[$len - 1] === 'a' ? 'b' : 'a';
                    }
                    $aw[] = $s;
                    $tot += $len;
                }

                return $masukan($kata, $aw);
            };

            // Satu kata "aaaa...a" sepanjang 10^6: trie berupa rantai sangat dalam.
            $rantai = function () use ($masukan) {
                $aw = [];
                for ($i = 0; $i < 100000; $i++) {
                    $len = mt_rand(1, 10);
                    $aw[] = mt_rand(0, 2) ? str_repeat('a', $len) : str_repeat('a', $len - 1).'b';
                }

                return $masukan([str_repeat('a', 1000000)], $aw);
            };

            // Banyak kata kembar dari kumpulan kecil: jawaban bisa puluhan ribu.
            $kembar = function () use ($masukan, $kataAcak) {
                $pool = $kataAcak(30, 1, 6, 'abc');
                $kata = [];
                for ($i = 0; $i < 100000; $i++) {
                    $kata[] = $pool[mt_rand(0, 29)];
                }
                $aw = [];
                for ($i = 0; $i < 100000; $i++) {
                    $aw[] = T::word(mt_rand(1, 3), 'abc');
                }

                return $masukan($kata, $aw);
            };

            $k1 = $kataAcak(20, 1, 4, 'ab');
            $k2 = $kataAcak(1000, 1, 8, 'abc');
            $k3 = $kataAcak(100000, 10, 10, $huruf);
            $k4 = $kataAcak(100000, 1, 10, 'ab');
            $k5 = $kataAcak(100000, 1, 1, $huruf);
            $k6 = $kataAcak(100000, 3, 10, 'abcde');

            return [
                "1\na\n1\na\n",
                "2\nab\nb\n4\na\nab\nabc\nb\n",
                $masukan($k1, $awalanAcak($k1, 20, 40, 4, 'ab')),
                $masukan($k2, $awalanAcak($k2, 1000, 30, 8, 'abc')),
                $masukan($k3, $awalanAcak($k3, 100000, 50, 10, $huruf)),
                $masukan($k4, $awalanAcak($k4, 100000, 30, 10, 'ab')),
                $masukan($k5, $awalanAcak($k5, 100000, 50, 2, $huruf)),
                $masukan($k6, $awalanAcak($k6, 100000, 20, 10, 'abcde')),
                $panjang(),
                $rantai(),
                $kembar(),
            ];
        },
        'solve' => function (string $input) use ($bacaKamus, $lowerBound) {
            [$kata, $awalan] = $bacaKamus($input);
            sort($kata, SORT_STRING);
            $out = [];
            foreach ($awalan as $p) {
                // kata berawalan p membentuk blok [p, p + '{') pada urutan kamus ('{' tepat setelah 'z')
                $out[] = $lowerBound($kata, $p.'{') - $lowerBound($kata, $p);
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<string> kata(n);\n    for (auto& w : kata) cin >> w;\n    int q;\n    cin >> q;\n    vector<string> awalan(q);\n    for (auto& p : awalan) cin >> p;", "const n = Number(readLine());\nconst kata = [];\nfor (let i = 0; i < n; i++) kata.push(readLine().trim());\nconst q = Number(readLine());\nconst awalan = [];\nfor (let i = 0; i < q; i++) awalan.push(readLine().trim());", "n = int(input())\nkata = [input().strip() for _ in range(n)]\nq = int(input())\nawalan = [input().strip() for _ in range(q)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000005;   // total panjang kata ≤ 10^6, ditambah simpul akar
int anak[MAKS][26];         // anak[v][c] = simpul anak v lewat huruf c (0 = belum ada)
int cnt[MAKS];              // cnt[v] = banyak kata yang MELEWATI simpul v
int banyakSimpul = 1;       // simpul 0 = akar

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    for (int i = 0; i < n; i++) {
        string w;
        cin >> w;
        int v = 0;
        for (char ch : w) {
            int c = ch - 'a';
            if (!anak[v][c]) anak[v][c] = banyakSimpul++;
            v = anak[v][c];
            cnt[v]++;               // setiap simpul di jalur kata mendapat +1
        }
    }

    int q;
    cin >> q;
    string out;
    for (int i = 0; i < q; i++) {
        string p;
        cin >> p;
        int v = 0;
        for (char ch : p) {
            v = anak[v][ch - 'a'];
            if (v == 0) break;      // jalur putus: tidak ada kata dengan awalan ini
        }
        out += to_string(cnt[v]);   // cnt[0] selalu 0, jadi jalur putus otomatis menjawab 0
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const kata = [];
let total = 0;
for (let i = 0; i < n; i++) {
  const w = readLine().trim();
  kata.push(w);
  total += w.length;
}

// Banyak simpul paling banyak total panjang kata + 1 (akar).
const anak = new Int32Array(26 * (total + 1)); // anak[26 * v + c], 0 = belum ada
const cnt = new Int32Array(total + 1);         // cnt[v] = banyak kata yang melewati v
let banyakSimpul = 1;                           // simpul 0 = akar
for (const w of kata) {
  let v = 0;
  for (let j = 0; j < w.length; j++) {
    const i = 26 * v + (w.charCodeAt(j) - 97);
    if (anak[i] === 0) anak[i] = banyakSimpul++;
    v = anak[i];
    cnt[v]++;
  }
}

const q = Number(readLine());
const hasil = [];
for (let k = 0; k < q; k++) {
  const p = readLine().trim();
  let v = 0;
  for (let j = 0; j < p.length; j++) {
    v = anak[26 * v + (p.charCodeAt(j) - 97)];
    if (v === 0) break; // jalur putus
  }
  hasil.push(cnt[v]); // cnt[0] = 0
}
console.log(hasil.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from array import array


def main():
    # Token berupa bytes; mengiterasi bytes menghasilkan kode ASCII (97 = 'a').
    data = sys.stdin.buffer.read().split()
    n = int(data[0])
    kata = data[1:1 + n]
    q = int(data[1 + n])
    awalan = data[2 + n:2 + n + q]

    maks = sum(map(len, kata)) + 1       # simpul paling banyak = total panjang + akar
    anak = array("i", [0]) * (26 * maks)  # anak[26 * v + c], 0 = belum ada
    cnt = array("i", [0]) * maks          # cnt[v] = banyak kata yang melewati v
    baru = 0                              # nomor simpul terakhir (akar = 0)
    for w in kata:
        v = 0
        for c in w:
            i = 26 * v + c - 97
            if not anak[i]:
                baru += 1
                anak[i] = baru
            v = anak[i]
            cnt[v] += 1

    hasil = []
    for p in awalan:
        v = 0
        for c in p:
            v = anak[26 * v + c - 97]
            if not v:
                break                     # jalur putus
        hasil.append(cnt[v])              # cnt[0] = 0
    print("\n".join(map(str, hasil)))


main()   # kode di dalam fungsi berjalan lebih cepat (variabel lokal)
CODE,
        ],
        'editorial' => '<p>Masukkan semua kata ke dalam <strong>trie</strong>. Setiap simpul mewakili sebuah awalan, dan anak simpul lewat huruf c mewakili awalan itu ditambah c. Selain array anak berukuran 26, simpan <code>cnt[v]</code> = banyak kata yang <em>melewati</em> simpul v: saat menyisipkan sebuah kata, tambahkan 1 pada cnt <strong>setiap</strong> simpul di jalurnya, bukan hanya simpul terakhir.</p>
<p>Kata w diawali p tepat ketika jalur w di trie melewati simpul milik p. Maka jawaban untuk p adalah <code>cnt</code> di simpul tempat penelusuran p berakhir, atau 0 jika di tengah jalan anak yang dibutuhkan belum ada. Kata yang sama persis dengan p ikut terhitung karena jalurnya juga melewati (berakhir di) simpul itu, dan kata kembar terhitung berkali-kali karena masing-masing menambah cnt.</p>
<p>Kompleksitas O(total panjang kata + total panjang awalan). Banyak simpul paling banyak total panjang kata + 1, jadi tabel anak berisi sekitar 2,6 · 10<sup>7</sup> bilangan (±104 MB untuk int 4 byte). Simpan sebagai array datar: array global di C++, <code>Int32Array</code> di JavaScript, <code>array("i")</code> di Python. Membuat objek atau dict untuk setiap simpul jauh lebih boros dan lambat.</p>
<p><strong>Jebakan:</strong> menambah cnt hanya di ujung kata membuat kita menghitung kata yang sama persis, bukan yang berawalan. Saat anak tidak ada, hentikan penelusuran (jangan melanjutkan dari akar). Sebagai pembanding, soal ini juga bisa diselesaikan dengan mengurutkan kata: kata berawalan p membentuk satu blok dari <code>lower_bound(p)</code> sampai <code>lower_bound(p + "{")</code>, karena <code>{</code> adalah karakter tepat setelah <code>z</code>.</p>',
        'hints' => [
            'Bayangkan semua kata disusun sebagai pohon huruf: kata-kata dengan awalan yang sama berbagi jalur dari akar.',
            'Di setiap simpul trie, simpan berapa banyak kata yang melewatinya. Tambahkan 1 di setiap simpul pada jalur saat menyisipkan kata.',
            'Untuk awalan p, telusuri trie huruf demi huruf. Jika jalurnya putus, jawabannya 0; jika tidak, jawabannya cnt di simpul terakhir.',
        ],
    ],

    [
        'slug' => 'saran-kata',
        'lesson' => 'trie',
        'title' => 'Saran Kata Papan Ketik',
        'difficulty' => 'Sedang',
        'tags' => ['trie', 'string', 'leksikografis', 'binary search'],
        'statement' => '<p>Dimas membuat aplikasi papan ketik dengan fitur saran kata. Kamusnya berisi <strong>N</strong> kata. Setelah pengguna mengetik sebuah awalan, papan ketik menampilkan <strong>satu</strong> saran: kata di kamus yang diawali awalan itu dan paling kecil menurut urutan kamus (leksikografis). Jika tidak ada kata yang cocok, papan ketik menampilkan <code>-</code>.</p>
<p>Urutan kamus membandingkan huruf demi huruf dari kiri, dan kata yang menjadi awalan kata lain lebih kecil. Contohnya <code>main</code> &lt; <code>mainan</code> &lt; <code>makan</code>. Sebuah kata dianggap diawali dirinya sendiri.</p>
<p>Diberikan <strong>Q</strong> awalan yang diketik (masing-masing berdiri sendiri), tentukan saran yang ditampilkan untuk setiap awalan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi satu kata kamus. Baris berikutnya berisi <code>Q</code>, lalu Q baris masing-masing berisi satu awalan.</p>',
        'output_format' => '<p>Q baris: kata saran untuk setiap awalan, atau <code>-</code> jika tidak ada.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 10<sup>5</sup></li><li>Setiap kata dan awalan terdiri atas huruf kecil <code>a</code>–<code>z</code> dengan panjang 1 sampai 10</li><li>Kata di kamus boleh ada yang sama</li></ul>',
        'samples' => [
            ['input' => "6\nmakan\nmain\nmainan\nminum\nmalam\nmandi\n5\nma\nmak\nmi\nmu\nmainan\n", 'explanation' => 'Kata yang diawali <code>ma</code> ada lima: main, mainan, makan, malam, dan mandi. Yang terkecil adalah <code>main</code>: huruf ketiganya i lebih kecil daripada k, l, dan n, dan main adalah awalan dari mainan. Awalan <code>mak</code> hanya cocok dengan makan, <code>mi</code> dengan minum, dan tidak ada kata yang diawali <code>mu</code>.'],
            ['input' => "3\ncba\ncb\nca\n3\nc\ncb\ncbaa\n", 'explanation' => 'Untuk awalan <code>c</code>, kata ca lebih kecil daripada cb dan cba karena huruf keduanya a. Untuk awalan <code>cb</code>, kata cb sendiri lebih kecil daripada cba. Tidak ada kata yang diawali <code>cbaa</code>.'],
        ],
        'tests' => function () use ($masukan, $kataAcak, $awalanAcak) {
            $huruf = 'abcdefghijklmnopqrstuvwxyz';

            // Semua kata berawalan "aaaaa": simpul-simpul atas dilewati banyak kata.
            $seragam = function () use ($masukan) {
                $kata = [];
                for ($i = 0; $i < 100000; $i++) {
                    $kata[] = 'aaaaa'.T::word(5, 'abcdefghij');
                }
                $aw = [];
                for ($i = 0; $i < 100000; $i++) {
                    $aw[] = mt_rand(0, 1) ? str_repeat('a', mt_rand(1, 6)).T::word(mt_rand(0, 4), 'abcdefghijk') : T::word(mt_rand(1, 10), 'abcdefghijk');
                }

                return $masukan($kata, $aw);
            };

            $k1 = $kataAcak(10, 1, 3, 'ab');
            $k2 = $kataAcak(200, 1, 5, 'abc');
            $k3 = $kataAcak(100000, 1, 10, $huruf);
            $k4 = $kataAcak(100000, 1, 10, 'ab');
            $k5 = $kataAcak(100000, 10, 10, 'abcde');
            // Kamus diberikan dalam urutan menurun: kata yang muncul duluan BUKAN yang terkecil.
            $k6 = $kataAcak(100000, 1, 10, 'abc');
            rsort($k6, SORT_STRING);
            $k7 = ['trie'];

            return [
                "1\na\n2\na\nb\n",
                $masukan($k1, $awalanAcak($k1, 10, 50, 3, 'ab')),
                $masukan($k2, $awalanAcak($k2, 200, 40, 5, 'abc')),
                $masukan($k3, $awalanAcak($k3, 100000, 50, 10, $huruf)),
                $masukan($k4, $awalanAcak($k4, 100000, 100, 10, 'ab')),
                $masukan($k5, $awalanAcak($k5, 100000, 60, 10, 'abcde')),
                $masukan($k6, $awalanAcak($k6, 100000, 40, 10, 'abc')),
                $masukan($k7, $awalanAcak($k7, 100000, 70, 5, 'eirt')),
                $seragam(),
            ];
        },
        'solve' => function (string $input) use ($bacaKamus, $lowerBound) {
            [$kata, $awalan] = $bacaKamus($input);
            sort($kata, SORT_STRING);
            $n = count($kata);
            $out = [];
            foreach ($awalan as $p) {
                $i = $lowerBound($kata, $p);
                $out[] = ($i < $n && strncmp($kata[$i], $p, strlen($p)) === 0) ? $kata[$i] : '-';
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<string> kata(n);\n    for (auto& w : kata) cin >> w;\n    int q;\n    cin >> q;\n    vector<string> awalan(q);\n    for (auto& p : awalan) cin >> p;", "const n = Number(readLine());\nconst kata = [];\nfor (let i = 0; i < n; i++) kata.push(readLine().trim());\nconst q = Number(readLine());\nconst awalan = [];\nfor (let i = 0; i < q; i++) awalan.push(readLine().trim());", "n = int(input())\nkata = [input().strip() for _ in range(n)]\nq = int(input())\nawalan = [input().strip() for _ in range(q)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000005;   // N ≤ 10^5 kata dengan panjang ≤ 10, ditambah akar
int anak[MAKS][26];         // 0 = belum ada
int terkecil[MAKS];         // terkecil[v] = indeks kata terkecil yang melewati simpul v
int banyakSimpul = 1;       // simpul 0 = akar

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<string> kata(n);
    for (auto& w : kata) cin >> w;

    // Sisipkan kata dari yang terkecil. Simpul baru selalu dibuat oleh kata pertama
    // yang melewatinya, dan kata itulah yang terkecil di antara semua yang melewatinya.
    sort(kata.begin(), kata.end());
    for (int i = 0; i < n; i++) {
        int v = 0;
        for (char ch : kata[i]) {
            int c = ch - 'a';
            if (!anak[v][c]) {
                anak[v][c] = banyakSimpul;
                terkecil[banyakSimpul] = i;
                banyakSimpul++;
            }
            v = anak[v][c];
        }
    }

    int q;
    cin >> q;
    string out;
    for (int i = 0; i < q; i++) {
        string p;
        cin >> p;
        int v = 0;
        for (char ch : p) {
            v = anak[v][ch - 'a'];
            if (v == 0) break;      // tidak ada kata dengan awalan ini
        }
        if (v) out += kata[terkecil[v]];
        else out += '-';
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const kata = [];
let total = 0;
for (let i = 0; i < n; i++) {
  const w = readLine().trim();
  kata.push(w);
  total += w.length;
}
// Urutan bawaan sort() pada string huruf kecil = urutan kamus.
kata.sort();

const anak = new Int32Array(26 * (total + 1)); // anak[26 * v + c], 0 = belum ada
const terkecil = new Int32Array(total + 1);    // indeks kata terkecil yang melewati v
let banyakSimpul = 1;                           // simpul 0 = akar
for (let k = 0; k < n; k++) {
  const w = kata[k];
  let v = 0;
  for (let j = 0; j < w.length; j++) {
    const i = 26 * v + (w.charCodeAt(j) - 97);
    if (anak[i] === 0) {
      anak[i] = banyakSimpul;
      terkecil[banyakSimpul] = k; // kata pertama yang lewat = kata terkecil
      banyakSimpul++;
    }
    v = anak[i];
  }
}

const q = Number(readLine());
const hasil = [];
for (let k = 0; k < q; k++) {
  const p = readLine().trim();
  let v = 0;
  for (let j = 0; j < p.length; j++) {
    v = anak[26 * v + (p.charCodeAt(j) - 97)];
    if (v === 0) break;
  }
  hasil.push(v ? kata[terkecil[v]] : "-");
}
console.log(hasil.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from array import array


def main():
    data = sys.stdin.buffer.read().split()
    n = int(data[0])
    kata = sorted(data[1:1 + n])          # bytes huruf kecil terurut = urutan kamus
    q = int(data[1 + n])
    awalan = data[2 + n:2 + n + q]

    maks = sum(map(len, kata)) + 1
    anak = array("i", [0]) * (26 * maks)  # anak[26 * v + c], 0 = belum ada
    terkecil = array("i", [0]) * maks     # indeks kata terkecil yang melewati v
    baru = 0                              # nomor simpul terakhir (akar = 0)
    for idx, w in enumerate(kata):
        v = 0
        for c in w:                       # c = kode ASCII huruf
            i = 26 * v + c - 97
            if not anak[i]:
                baru += 1
                anak[i] = baru
                terkecil[baru] = idx      # kata pertama yang lewat = kata terkecil
            v = anak[i]

    hasil = []
    for p in awalan:
        v = 0
        for c in p:
            v = anak[26 * v + c - 97]
            if not v:
                break
        hasil.append(kata[terkecil[v]] if v else b"-")
    print(b"\n".join(hasil).decode())


main()
CODE,
        ],
        'editorial' => '<p><strong>Cara 1: trie.</strong> Semua kata yang diawali p melewati simpul milik p di trie. Jadi cukup simpan di setiap simpul <code>terkecil[v]</code> = kata terkecil di antara kata-kata yang melewati v; jawaban untuk p adalah nilai itu di simpul akhir penelusuran p, atau <code>-</code> jika jalurnya putus.</p>
<p>Mengisi <code>terkecil</code> paling mudah dengan <strong>mengurutkan kamus lebih dulu</strong> lalu menyisipkan kata dari yang terkecil. Sebuah simpul dibuat oleh kata pertama yang melewatinya; semua kata yang menyusul tidak lebih kecil, jadi nilai yang dicatat saat simpul dibuat sudah pasti benar. Tanpa pengurutan pun bisa: di setiap simpul pada jalur, bandingkan kata baru dengan kata yang tersimpan dan ambil yang lebih kecil (perbandingan paling lama 10 huruf). Kompleksitas O(N log N · L + Q · L) dengan L ≤ 10.</p>
<p><strong>Cara 2: urutkan + binary search.</strong> Pada kamus yang terurut, cari kata pertama yang ≥ p (<code>lower_bound</code>). Jika kata itu diawali p, itulah jawabannya; jika tidak, tidak ada kata yang diawali p. Alasannya: setiap kata yang diawali p bernilai ≥ p, sedangkan kata ≥ p yang <em>tidak</em> diawali p pasti berbeda dari p di salah satu huruf dengan huruf yang lebih besar, sehingga lebih besar daripada semua kata berawalan p. Jadi kata-kata berawalan p membentuk satu blok yang dimulai tepat di <code>lower_bound(p)</code>.</p>
<p><strong>Jebakan:</strong> kata yang dimasukkan pertama kali ke trie belum tentu yang terkecil jika kamus tidak diurutkan (salah satu tes memberikan kamus dalam urutan menurun). Awalan yang sama persis dengan sebuah kata menyarankan kata itu sendiri, karena ia lebih kecil daripada semua perpanjangannya.</p>',
        'hints' => [
            'Kata-kata yang diawali p semuanya melewati simpul p di trie. Informasi apa yang perlu disimpan di simpul itu?',
            'Simpan kata terkecil yang melewati setiap simpul. Jika kata disisipkan berurutan dari yang terkecil, siapa yang pertama kali membuat sebuah simpul?',
            'Alternatif tanpa trie: urutkan kamus lalu cari kata pertama yang ≥ p (lower_bound). Jika kata itu diawali p, itulah jawabannya; jika tidak, cetak "-".',
        ],
    ],

    [
        'slug' => 'xor-pasangan',
        'lesson' => 'trie',
        'title' => 'Pasangan Kartu Terkuat',
        'difficulty' => 'Sedang',
        'tags' => ['trie biner', 'xor', 'bit', 'greedy'],
        'statement' => '<p>Dalam permainan kartu Sihir Biner, setiap kartu bertuliskan sebuah bilangan bulat. Dua kartu yang dimainkan bersama menghasilkan kekuatan sebesar <strong>XOR</strong> kedua bilangannya.</p>
<p>XOR (ditulis <code>^</code> di C++, JavaScript, dan Python) bekerja bit demi bit: bit hasilnya 1 jika kedua bit berbeda dan 0 jika sama. Contohnya 5 XOR 3 = 101<sub>2</sub> XOR 011<sub>2</sub> = 110<sub>2</sub> = 6.</p>
<p>Kamu memegang <strong>N</strong> kartu, kartu ke-i bertuliskan <code>a<sub>i</sub></code>. Pilih dua kartu yang berbeda (bilangannya boleh sama) sehingga kekuatannya sebesar mungkin. Berapa kekuatan terbesar itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Nilai terbesar dari <code>a<sub>i</sub> XOR a<sub>j</sub></code> dengan i ≠ j.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 10<sup>5</sup></li><li>0 ≤ a<sub>i</sub> &lt; 2<sup>30</sup></li></ul>',
        'samples' => [
            ['input' => "5\n3 10 5 25 2\n", 'explanation' => '5 = 00101<sub>2</sub> dan 25 = 11001<sub>2</sub>, XOR-nya 11100<sub>2</sub> = 28. Hanya 25 yang punya bit bernilai 16, jadi pasangan terbaik pasti memakai 25; pasangan lainnya memberi 25 XOR 2 = 27, 25 XOR 3 = 26, dan 25 XOR 10 = 19.'],
            ['input' => "3\n7 7 7\n", 'explanation' => 'Semua kartu bertuliskan bilangan yang sama, sehingga XOR dua kartu mana pun adalah 0.'],
        ],
        'tests' => function () use ($deret) {
            $tinggi = mt_rand(0, 4095) << 18;
            $mask = mt_rand(1 << 24, (1 << 30) - 1);
            $sama = mt_rand(0, (1 << 30) - 1);
            $dua = [mt_rand(0, (1 << 30) - 1), mt_rand(0, (1 << 30) - 1)];

            return [
                "2\n0 0\n",
                "2\n0 1073741823\n",
                "5\n1 2 3 4 5\n",
                $deret(10, fn () => mt_rand(0, 63)),
                $deret(1000, fn () => mt_rand(0, 4095)),
                $deret(300, fn () => mt_rand(0, (1 << 30) - 1)),
                $deret(100000, fn () => mt_rand(0, (1 << 30) - 1)),
                // 12 bit teratas semua bilangan sama: jawaban < 2^18
                $deret(100000, fn () => $tinggi | mt_rand(0, (1 << 18) - 1)),
                $deret(100000, fn () => mt_rand(0, (1 << 30) - 1) & $mask),
                $deret(100000, fn () => $sama),
                $deret(100000, fn () => $dua[mt_rand(0, 1)]),
                $deret(100000, fn () => mt_rand(0, (1 << 15) - 1) << 15),
            ];
        },
        'solve' => function (string $input) use ($xorPasangan) {
            $lines = T::lines($input);

            return (string) $xorPasangan(T::ints($lines[1]));
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;", "const n = Number(readLine());\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int B = 30;                  // a < 2^30: bit 29 sampai bit 0
const int MAKS = 100000 * B + 5;   // setiap bilangan menambah paling banyak B simpul
int anak[MAKS][2];                 // anak[v][bit], 0 = belum ada
int banyakSimpul = 1;              // simpul 0 = akar

void sisip(int x) {
    int v = 0;
    for (int b = B - 1; b >= 0; b--) {
        int c = (x >> b) & 1;
        if (!anak[v][c]) anak[v][c] = banyakSimpul++;
        v = anak[v][c];
    }
}

// XOR terbesar antara x dan salah satu bilangan di trie (trie tidak kosong).
int cari(int x) {
    int v = 0, hasil = 0;
    for (int b = B - 1; b >= 0; b--) {
        int c = (x >> b) & 1;
        if (anak[v][c ^ 1]) {      // ada cabang berlawanan: bit b hasil XOR bernilai 1
            hasil |= 1 << b;
            v = anak[v][c ^ 1];
        } else {                   // terpaksa lewat cabang yang sama: bit b bernilai 0
            v = anak[v][c];
        }
    }
    return hasil;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> a(n);
    for (auto& x : a) cin >> x;

    // Cari pasangan terbaik a[i] di antara a[0..i-1], baru sisipkan a[i].
    sisip(a[0]);
    int terbaik = 0;
    for (int i = 1; i < n; i++) {
        terbaik = max(terbaik, cari(a[i]));
        sisip(a[i]);
    }
    cout << terbaik << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
const B = 30;                                  // bit 29 sampai bit 0
const anak = new Int32Array(2 * (n * B + 1));  // anak[2 * v + bit], 0 = belum ada
let banyakSimpul = 1;                          // simpul 0 = akar

function sisip(x) {
  let v = 0;
  for (let b = B - 1; b >= 0; b--) {
    const i = 2 * v + ((x >> b) & 1);
    if (anak[i] === 0) anak[i] = banyakSimpul++;
    v = anak[i];
  }
}

// XOR terbesar antara x dan salah satu bilangan di trie.
function cari(x) {
  let v = 0, hasil = 0;
  for (let b = B - 1; b >= 0; b--) {
    const c = (x >> b) & 1;
    const lawan = anak[2 * v + (c ^ 1)];
    if (lawan !== 0) {          // utamakan bit berlawanan
      hasil |= 1 << b;
      v = lawan;
    } else {
      v = anak[2 * v + c];
    }
  }
  return hasil;
}

sisip(a[0]);
let terbaik = 0;
for (let i = 1; i < n; i++) {
  terbaik = Math.max(terbaik, cari(a[i]));
  sisip(a[i]);
}
console.log(terbaik);
CODE,
            'python' => <<<'CODE'
import sys

B = 30  # bit 29 sampai bit 0


def main():
    data = sys.stdin.buffer.read().split()
    n = int(data[0])
    a = list(map(int, data[1:1 + n]))

    # Trie biner dalam satu list datar. Simpul diberi nomor genap v (akar = 0):
    # anak lewat bit 0 ada di anak[v], lewat bit 1 di anak[v + 1]; 0 = belum ada.
    anak = [0] * (2 * (n * B + 1))
    daun = {}            # daun[v] = bilangan yang jalurnya berakhir di simpul v
    ke01 = bytes.maketrans(b"01", b"\x00\x01")
    baru = 0             # nomor simpul terakhir
    terbaik = 0
    for x in a:
        # 30 bit x dari yang tertinggi, tiap byte bernilai 0 atau 1
        bit = format(x, "030b").encode().translate(ke01)
        if baru:         # cari pasangan terbaik di antara bilangan sebelumnya
            v = 0
            for c in bit:
                # utamakan cabang berlawanan (1 - c); jika tidak ada, lewat cabang c
                v = anak[v + 1 - c] or anak[v + c]
            if x ^ daun[v] > terbaik:
                terbaik = x ^ daun[v]
        v = 0            # sisipkan x
        for c in bit:
            i = v + c
            v = anak[i]
            if not v:
                baru += 2
                anak[i] = v = baru
        daun[v] = x
    print(terbaik)


main()   # kode di dalam fungsi berjalan lebih cepat (variabel lokal)
CODE,
        ],
        'editorial' => '<p>Mencoba semua pasangan butuh N(N − 1)/2 ≈ 5 · 10<sup>9</sup> operasi, terlalu lambat. Kuncinya: bit ke-b bernilai 2<sup>b</sup>, lebih besar daripada jumlah semua bit di bawahnya (2<sup>b</sup> − 1). Jadi untuk memaksimalkan <code>x XOR y</code>, kita <strong>serakah dari bit tertinggi</strong>: usahakan bit 29 hasilnya 1, lalu bit 28, dan seterusnya, tanpa pernah mengorbankan bit yang lebih tinggi.</p>
<p>Simpan setiap bilangan sebagai untaian 30 bit, dari bit 29 sampai bit 0, ke dalam <strong>trie biner</strong> (setiap simpul punya paling banyak dua anak: bit 0 dan bit 1). Untuk mencari pasangan terbaik x, telusuri trie dari akar; di setiap level, jika ada anak dengan bit <em>berlawanan</em> dengan bit x, lewat sana (bit hasil XOR menjadi 1), jika tidak, lewat anak yang sama (bit hasil 0). Karena setiap pilihan memaksimalkan bit yang lebih tinggi lebih dulu, hasilnya adalah XOR terbesar x dengan bilangan mana pun di trie.</p>
<p>Agar kedua kartu berbeda, proses kartu satu per satu: <em>cari dulu</em> pasangan terbaik a<sub>i</sub> di antara a<sub>1</sub>, …, a<sub>i−1</sub> yang sudah ada di trie, <em>baru sisipkan</em> a<sub>i</sub>. Setiap pasangan j &lt; i diperiksa tepat sekali. Kompleksitas O(30 · N) waktu, dengan paling banyak 30N + 1 simpul (3 · 10<sup>6</sup> simpul × 2 anak).</p>
<p><strong>Jebakan:</strong> semua bilangan harus disisipkan dengan <strong>panjang bit yang sama</strong> (30 bit, termasuk nol di depan), supaya level ke-k di trie selalu mewakili bit yang sama. Di Python, simpan trie sebagai list datar (bukan dict atau objek per simpul) dan letakkan kode di dalam fungsi agar cukup cepat.</p>',
        'hints' => [
            'Bit tertinggi bernilai lebih besar daripada jumlah semua bit di bawahnya. Bit mana yang harus diusahakan bernilai 1 lebih dulu?',
            'Simpan semua bilangan sebagai untaian 30 bit (dari bit 29 ke bit 0) di dalam trie biner.',
            'Untuk setiap a_i, telusuri trie dan di setiap level pilih cabang yang bitnya berlawanan dengan bit a_i jika ada. Cari dulu, baru sisipkan a_i.',
        ],
    ],

    [
        'slug' => 'xor-subarray',
        'lesson' => 'trie',
        'title' => 'Kode Rangkaian Gerbong',
        'difficulty' => 'Sulit',
        'tags' => ['trie biner', 'xor', 'prefix xor'],
        'statement' => '<p>Sebuah kereta barang terdiri atas <strong>N</strong> gerbong berjajar, dan gerbong ke-i membawa kode muatan <code>a<sub>i</sub></code>. Untuk pemeriksaan, petugas memilih satu <strong>rangkaian gerbong yang bersebelahan</strong>: paling sedikit satu gerbong, boleh juga seluruh kereta.</p>
<p>Kode sebuah rangkaian adalah XOR dari kode semua gerbong di dalamnya (XOR bit demi bit, operator <code>^</code>). Petugas ingin memeriksa rangkaian dengan kode terbesar. Berapa kode itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Nilai terbesar dari <code>a<sub>l</sub> XOR a<sub>l+1</sub> XOR … XOR a<sub>r</sub></code> untuk 1 ≤ l ≤ r ≤ N.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>5</sup></li><li>0 ≤ a<sub>i</sub> &lt; 2<sup>30</sup></li></ul>',
        'samples' => [
            ['input' => "4\n8 1 2 12\n", 'explanation' => 'Rangkaian gerbong ke-2 sampai ke-4: 1 XOR 2 XOR 12 = 0001<sub>2</sub> XOR 0010<sub>2</sub> XOR 1100<sub>2</sub> = 1111<sub>2</sub> = 15, nilai terbesar yang mungkin karena semua kode kurang dari 16. Seluruh kereta justru hanya memberi 8 XOR 1 XOR 2 XOR 12 = 7.'],
            ['input' => "3\n6 6 6\n", 'explanation' => 'Rangkaian dua gerbong bernilai 6 XOR 6 = 0, sedangkan satu gerbong atau ketiga gerbong bernilai 6.'],
        ],
        'tests' => function () use ($deret) {
            $mask = mt_rand(1 << 20, (1 << 30) - 1);
            $sama = mt_rand(0, (1 << 30) - 1);
            $dua = [mt_rand(0, (1 << 30) - 1), mt_rand(0, (1 << 30) - 1)];

            return [
                "1\n0\n",
                "1\n1073741823\n",
                "2\n5 3\n",
                $deret(8, fn () => mt_rand(0, 15)),
                $deret(1000, fn () => mt_rand(0, 4095)),
                $deret(300, fn () => mt_rand(0, (1 << 30) - 1)),
                $deret(100000, fn () => mt_rand(0, (1 << 30) - 1)),
                $deret(100000, fn () => mt_rand(0, (1 << 30) - 1) & $mask),
                $deret(100000, fn () => $sama),
                $deret(100000, fn () => mt_rand(0, 1023) << 20),
                $deret(100000, fn () => $dua[mt_rand(0, 1)]),
                $deret(100000, fn ($i) => 1 << ($i % 29)),
            ];
        },
        'solve' => function (string $input) use ($xorPasangan) {
            $lines = T::lines($input);
            $pre = [0];
            $p = 0;
            foreach (T::ints($lines[1]) as $x) {
                $p ^= $x;
                $pre[] = $p;
            }

            return (string) $xorPasangan($pre);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;", "const n = Number(readLine());\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int B = 30;                        // a < 2^30: bit 29 sampai bit 0
const int MAKS = (100000 + 1) * B + 5;   // N + 1 prefix, masing-masing ≤ B simpul baru
int anak[MAKS][2];                       // 0 = belum ada
int banyakSimpul = 1;                    // simpul 0 = akar

void sisip(int x) {
    int v = 0;
    for (int b = B - 1; b >= 0; b--) {
        int c = (x >> b) & 1;
        if (!anak[v][c]) anak[v][c] = banyakSimpul++;
        v = anak[v][c];
    }
}

// XOR terbesar antara x dan salah satu bilangan di trie.
int cari(int x) {
    int v = 0, hasil = 0;
    for (int b = B - 1; b >= 0; b--) {
        int c = (x >> b) & 1;
        if (anak[v][c ^ 1]) {
            hasil |= 1 << b;
            v = anak[v][c ^ 1];
        } else {
            v = anak[v][c];
        }
    }
    return hasil;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;

    // P[j] = a[1] ^ ... ^ a[j], P[0] = 0.
    // XOR a[l..r] = P[r] ^ P[l-1]  ->  cari dua prefix berbeda dengan XOR terbesar.
    int prefix = 0, terbaik = 0;
    sisip(0);                            // P[0]: rangkaian yang dimulai dari gerbong 1
    for (int j = 1; j <= n; j++) {
        int x;
        cin >> x;
        prefix ^= x;
        terbaik = max(terbaik, cari(prefix));   // pasangan dengan P[0..j-1]
        sisip(prefix);
    }
    cout << terbaik << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
const B = 30;
const anak = new Int32Array(2 * ((n + 1) * B + 1)); // anak[2 * v + bit], 0 = belum ada
let banyakSimpul = 1;                               // simpul 0 = akar

function sisip(x) {
  let v = 0;
  for (let b = B - 1; b >= 0; b--) {
    const i = 2 * v + ((x >> b) & 1);
    if (anak[i] === 0) anak[i] = banyakSimpul++;
    v = anak[i];
  }
}

function cari(x) {
  let v = 0, hasil = 0;
  for (let b = B - 1; b >= 0; b--) {
    const c = (x >> b) & 1;
    const lawan = anak[2 * v + (c ^ 1)];
    if (lawan !== 0) {
      hasil |= 1 << b;
      v = lawan;
    } else {
      v = anak[2 * v + c];
    }
  }
  return hasil;
}

// XOR a[l..r] = P[r] ^ P[l-1], dengan P[0] = 0.
let prefix = 0, terbaik = 0;
sisip(0);
for (let j = 0; j < n; j++) {
  prefix ^= a[j];
  terbaik = Math.max(terbaik, cari(prefix));
  sisip(prefix);
}
console.log(terbaik);
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate
from operator import xor

B = 30  # bit 29 sampai bit 0


def main():
    data = sys.stdin.buffer.read().split()
    n = int(data[0])
    a = list(map(int, data[1:1 + n]))

    # P[0] = 0, P[j] = a[1] ^ ... ^ a[j]; XOR a[l..r] = P[r] ^ P[l-1].
    # Jawaban = XOR terbesar dari dua prefix berbeda.
    pre = list(accumulate(a, xor, initial=0))

    # Trie biner dalam list datar: simpul bernomor genap v (akar = 0),
    # anak lewat bit 0 di anak[v], lewat bit 1 di anak[v + 1]; 0 = belum ada.
    anak = [0] * (2 * ((n + 1) * B + 1))
    daun = {}            # daun[v] = prefix yang jalurnya berakhir di simpul v
    ke01 = bytes.maketrans(b"01", b"\x00\x01")
    baru = 0
    terbaik = 0
    for x in pre:
        bit = format(x, "030b").encode().translate(ke01)   # 30 bit, tiap byte 0/1
        if baru:         # pasangkan dengan prefix-prefix sebelumnya
            v = 0
            for c in bit:
                v = anak[v + 1 - c] or anak[v + c]        # utamakan bit berlawanan
            if x ^ daun[v] > terbaik:
                terbaik = x ^ daun[v]
        v = 0
        for c in bit:
            i = v + c
            v = anak[i]
            if not v:
                baru += 2
                anak[i] = v = baru
        daun[v] = x
    print(terbaik)


main()
CODE,
        ],
        'editorial' => '<p>Definisikan <strong>prefix XOR</strong> <code>P<sub>0</sub> = 0</code> dan <code>P<sub>j</sub> = a<sub>1</sub> XOR … XOR a<sub>j</sub></code>. Karena x XOR x = 0, bagian a<sub>1</sub> … a<sub>l−1</sub> saling menghapus sehingga</p>
<p style="text-align:center"><code>a<sub>l</sub> XOR … XOR a<sub>r</sub> = P<sub>r</sub> XOR P<sub>l−1</sub></code></p>
<p>Setiap rangkaian tidak kosong berpadanan dengan sepasang indeks 0 ≤ l − 1 &lt; r ≤ N. Jadi soal ini berubah menjadi: dari N + 1 bilangan P<sub>0</sub>, …, P<sub>N</sub>, cari XOR terbesar dua bilangan berbeda indeks. Itu persis soal pasangan XOR dengan <strong>trie biner</strong>: sisipkan P<sub>0</sub>, lalu untuk j = 1, …, N cari pasangan terbaik P<sub>j</sub> di trie (utamakan cabang dengan bit berlawanan, dari bit 29 ke bit 0), kemudian sisipkan P<sub>j</sub>. Total O(30 · N).</p>
<p><strong>Jebakan:</strong> jangan lupa menyisipkan P<sub>0</sub> = 0; tanpanya, rangkaian yang dimulai dari gerbong pertama (misalnya satu gerbong pertama saja) tidak pernah diperiksa. Pendekatan serakah ala Kadane ("perpanjang jika menguntungkan") tidak berlaku untuk XOR: menambah satu gerbong bisa menurunkan lalu menaikkan lagi nilainya. Pada contoh pertama, seluruh kereta hanya bernilai 7, sedangkan rangkaian gerbong 2–4 bernilai 15.</p>',
        'hints' => [
            'XOR punya sifat x XOR x = 0. Bagaimana menyatakan XOR sebuah rangkaian memakai dua "prefix XOR"?',
            'XOR a_l..a_r = P_r XOR P_(l−1), dengan P_0 = 0. Sekarang soalnya: pilih dua prefix berbeda dengan XOR terbesar.',
            'Itu persis soal XOR pasangan: sisipkan P_0, lalu untuk j = 1..N cari pasangan terbaik P_j di trie biner sebelum menyisipkannya.',
        ],
    ],
];
