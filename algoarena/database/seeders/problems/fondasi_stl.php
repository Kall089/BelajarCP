<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi STL, Sorting & Kompresi Koordinat:
 * map untuk frekuensi + sort dengan pembanding, kompresi koordinat,
 * multiset (upper_bound, hapus satu salinan), dan set + multiset bersamaan.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Kumpulan $cnt nama berbeda (huruf dari $alpha, panjang $minLen..$maxLen). */
$namePool = function (int $cnt, int $minLen, int $maxLen, string $alpha): array {
    $set = [];
    while (count($set) < $cnt) {
        $set[T::word(mt_rand($minLen, $maxLen), $alpha)] = true;
    }

    return array_map('strval', array_keys($set));
};

/** $cnt bilangan bulat berbeda di $lo..$hi (posisi lampu), dalam urutan acak. */
$distinctPoints = function (int $cnt, int $lo, int $hi): array {
    $set = [];
    while (count($set) < $cnt) {
        $set[mt_rand($lo, $hi)] = true;
    }
    $pts = array_keys($set);
    T::shuffle($pts);

    return $pts;
};

/** Susun kejadian: lampu $pts dipasang berurutan, $nq pertanyaan diselipkan acak (selalu diakhiri '?'). */
$lampEvents = function (int $x, array $pts, int $nq): string {
    $types = array_merge(array_fill(0, count($pts), 1), array_fill(0, max(0, $nq - 1), 0));
    T::shuffle($types);
    $types[] = 0;
    $ev = [];
    $k = 0;
    foreach ($types as $t) {
        $ev[] = $t ? '+ '.$pts[$k++] : '?';
    }

    return "$x ".count($ev)."\n".implode("\n", $ev)."\n";
};

return [
    [
        'slug' => 'hitung-cepat-osis',
        'lesson' => 'set-map',
        'title' => 'Hitung Cepat Pemilihan OSIS',
        'difficulty' => 'Mudah',
        'tags' => ['map', 'frekuensi', 'sorting', 'pembanding'],
        'statement' => '<p>Pemilihan ketua OSIS baru saja selesai. Setiap siswa menuliskan nama satu kandidat di kertas suara, lalu panitia mengumpulkan <strong>N</strong> kertas suara. Semua nama ditulis dengan huruf kecil, dan kertas yang bertuliskan nama yang sama dianggap memilih kandidat yang sama.</p>
<p>Panitia hanya akan mengumumkan <strong>K</strong> kandidat teratas. Kandidat diurutkan dari perolehan suara <strong>terbanyak</strong>; jika suaranya sama, kandidat yang namanya lebih kecil secara leksikografis (urutan kamus) disebut lebih dulu. Jika banyak kandidat kurang dari K, semua kandidat diumumkan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Masing-masing dari N baris berikutnya berisi nama yang tertulis pada satu kertas suara.</p>',
        'output_format' => '<p>Paling banyak K baris sesuai urutan pengumuman. Setiap baris berisi nama kandidat dan perolehan suaranya, dipisahkan satu spasi.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>1 ≤ K ≤ 10</li><li>Setiap nama terdiri dari 1 sampai 10 huruf kecil <code>a</code>–<code>z</code></li></ul>',
        'samples' => [
            ['input' => "8 3\nbudi\nani\ncitra\nbudi\nani\ndodi\nbudi\ncitra\n", 'explanation' => 'Perolehan suara: budi 3, ani 2, citra 2, dodi 1. Ani dan citra seri, sehingga ani (lebih dulu menurut abjad) disebut lebih dulu. Dodi tidak masuk tiga besar.'],
            ['input' => "3 5\nzaki\nzaki\nayu\n", 'explanation' => 'Hanya ada 2 kandidat, jadi keduanya diumumkan. Zaki lebih dulu karena suaranya lebih banyak, walaupun namanya lebih besar menurut abjad.'],
        ],
        'tests' => function () use ($namePool) {
            $mk = function (int $k, array $votes) {
                return count($votes)." $k\n".implode("\n", $votes)."\n";
            };
            $skewed = function (int $n, array $pool) {
                $c = count($pool);
                $v = [];
                for ($i = 0; $i < $n; $i++) {
                    $v[] = $pool[min(mt_rand(0, $c - 1), mt_rand(0, $c - 1))];
                }

                return $v;
            };
            $uniform = function (int $n, array $pool) {
                $c = count($pool);
                $v = [];
                for ($i = 0; $i < $n; $i++) {
                    $v[] = $pool[mt_rand(0, $c - 1)];
                }

                return $v;
            };

            // sepuluh nama, masing-masing tepat 10 000 suara
            $ten = $namePool(10, 3, 8, 'abcdefghijklmnopqrstuvwxyz');
            $even = [];
            foreach ($ten as $nm) {
                for ($i = 0; $i < 10000; $i++) {
                    $even[] = $nm;
                }
            }
            T::shuffle($even);

            // tiga nama mirip bersaing ketat di puncak
            $tight = array_merge(array_fill(0, 25000, 'rini'), array_fill(0, 25000, 'rina'), array_fill(0, 24999, 'rino'),
                $uniform(25000, $namePool(100, 2, 6, 'aeiourst')));
            T::shuffle($tight);

            $distinct = [];
            for ($i = 0; $i < 100000; $i++) {
                $distinct[] = T::word(10, 'abcdefghijklmnopqrstuvwxyz');
            }

            return [
                "1 1\nx\n",
                "5 10\ne\nd\nc\nb\na\n",
                "5 3\nabadi\nabad\nb\nabadi\nabad\n",
                $mk(3, $uniform(20, $namePool(5, 1, 4, 'abc'))),
                $mk(5, $skewed(1000, $namePool(40, 2, 7, 'abcdefghij'))),
                $mk(10, $skewed(100000, $namePool(3000, 3, 10, 'abcdefghijklmnopqrstuvwxyz'))),
                $mk(10, $distinct),
                $mk(7, $even),
                $mk(10, $uniform(100000, $namePool(14, 1, 3, 'ab'))),
                $mk(4, $tight),
                $mk(10, array_fill(0, 100000, 'zzzzzzzzzz')),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $cnt = [];
            for ($i = 1; $i <= $n; $i++) {
                $s = trim($lines[$i]);
                $cnt[$s] = ($cnt[$s] ?? 0) + 1;
            }
            $v = [];
            foreach ($cnt as $name => $c) {
                $v[] = [(string) $name, $c];
            }
            usort($v, fn ($a, $b) => $a[1] !== $b[1] ? $b[1] <=> $a[1] : strcmp($a[0], $b[0]));
            $out = [];
            foreach (array_slice($v, 0, $k) as [$name, $c]) {
                $out[] = "$name $c";
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, k;\n    cin >> n >> k;\n    vector<string> nama(n);\n    for (int i = 0; i < n; i++) cin >> nama[i];", "const [n, k] = readInts();\nconst nama = [];\nfor (let i = 0; i < n; i++) nama.push(readLine().trim());", "n, k = map(int, input().split())\nnama = [input().strip() for _ in range(n)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    map<string, int> suara;          // nama -> banyak suara (nilai awal otomatis 0)
    for (int i = 0; i < n; i++) {
        string nama;
        cin >> nama;
        suara[nama]++;
    }

    vector<pair<string, int>> v(suara.begin(), suara.end());
    // Suara lebih banyak dulu; jika seri, nama yang lebih kecil dulu.
    // Pembanding harus "kurang dari" yang ketat (jangan pakai >= atau <=).
    sort(v.begin(), v.end(), [](const pair<string, int>& a, const pair<string, int>& b) {
        if (a.second != b.second) return a.second > b.second;
        return a.first < b.first;
    });

    int tampil = min(k, (int)v.size());
    for (int i = 0; i < tampil; i++) cout << v[i].first << ' ' << v[i].second << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const suara = new Map(); // nama -> banyak suara
for (let i = 0; i < n; i++) {
  const nama = readLine().trim();
  suara.set(nama, (suara.get(nama) || 0) + 1);
}

const v = [...suara.entries()]; // pasangan [nama, suara]
// Suara menurun; jika seri, nama menaik.
// Bandingkan string dengan < (urutan kode karakter), bukan localeCompare.
v.sort((a, b) => (a[1] !== b[1] ? b[1] - a[1] : a[0] < b[0] ? -1 : 1));

const out = [];
for (let i = 0; i < Math.min(k, v.length); i++) out.push(v[i][0] + " " + v[i][1]);
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
suara = {}                          # nama -> banyak suara
for _ in range(n):
    nama = input().strip()
    suara[nama] = suara.get(nama, 0) + 1

# kunci (-suara, nama): suara menurun, lalu nama menaik
urut = sorted(suara.items(), key=lambda x: (-x[1], x[0]))
print("\n".join(nama + " " + str(s) for nama, s in urut[:k]))
CODE,
        ],
        'editorial' => '<p>Nama tidak bisa langsung dijadikan indeks array, jadi hitung frekuensinya dengan <code>map&lt;string, int&gt;</code>: cukup <code>suara[nama]++</code>, karena kunci yang belum ada otomatis dibuat dengan nilai 0. Di Python pakai <code>dict</code>, di JavaScript pakai <code>Map</code>.</p>
<p>Setelah semua suara dihitung, salin isi map ke <code>vector&lt;pair&lt;string, int&gt;&gt;</code>, lalu urutkan dengan pembanding: suara lebih besar dulu, dan jika seri nama lebih kecil dulu. Cetak min(K, banyak kandidat) pasangan pertama. Cara lain yang ringkas: urutkan pasangan <code>(−suara, nama)</code> dengan urutan bawaan, karena pair dibandingkan elemen pertama dulu, baru elemen kedua. Itulah yang dilakukan kunci <code>(-x[1], x[0])</code> di solusi Python.</p>
<p>Kompleksitas O(N log N). Beberapa jebakan:</p>
<ul><li>Pembanding untuk <code>sort</code> harus ketat. Menulis <code>a.second &gt;= b.second</code> melanggar aturan <em>strict weak ordering</em> dan bisa membuat program crash.</li>
<li>Di JavaScript, bandingkan string dengan <code>&lt;</code>. <code>localeCompare</code> mengikuti aturan bahasa dan tidak selalu sama dengan urutan leksikografis biasa.</li>
<li><code>unordered_map</code> juga bisa dipakai untuk menghitung, tetapi isinya tidak berurutan sehingga tetap harus diurutkan di akhir. Untuk kunci bilangan bulat, <code>unordered_map</code> juga rentan diperlambat oleh tes yang sengaja dibuat bertabrakan; <code>map</code> selalu O(log N).</li></ul>',
        'hints' => [
            'Hitung dulu berapa suara yang diperoleh setiap nama. Struktur apa yang bisa memetakan string ke angka?',
            'Pakai map<string, int> (dict di Python, Map di JavaScript). Setelah itu kumpulkan pasangan (nama, suara) ke dalam sebuah array.',
            'Urutkan array itu dengan pembanding: suara menurun, lalu nama menaik. Cetak paling banyak K pasangan pertama.',
        ],
    ],

    [
        'slug' => 'loket-tiket-konser',
        'lesson' => 'set-map',
        'title' => 'Loket Tiket Konser',
        'difficulty' => 'Sedang',
        'tags' => ['multiset', 'upper bound', 'simulasi', 'dsu'],
        'statement' => '<p>Loket sebuah konser musik masih menyimpan <strong>N</strong> tiket, dan tiket ke-i berharga <code>h<sub>i</sub></code> rupiah. Sebanyak <strong>M</strong> calon penonton datang ke loket satu per satu. Penonton ke-j membawa uang <code>t<sub>j</sub></code> rupiah dan selalu meminta tiket <strong>termahal</strong> yang harganya tidak melebihi uangnya. Tiket yang sudah terjual tentu tidak bisa dijual lagi. Jika tidak ada tiket yang terjangkau, penonton itu pulang tanpa tiket.</p>
<p>Untuk setiap penonton, tentukan harga tiket yang ia beli.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. Baris kedua berisi <code>h<sub>1</sub> … h<sub>N</sub></code>. Baris ketiga berisi <code>t<sub>1</sub> … t<sub>M</sub></code> sesuai urutan kedatangan.</p>',
        'output_format' => '<p>M baris. Baris ke-j berisi harga tiket yang dibeli penonton ke-j, atau <code>-1</code> jika ia pulang tanpa tiket.</p>',
        'constraints' => '<ul><li>1 ≤ N, M ≤ 100 000</li><li>1 ≤ h<sub>i</sub>, t<sub>j</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "5 5\n50 30 80 30 100\n40 90 35 20 1000\n", 'explanation' => 'Penonton 1 (uang 40) membeli salah satu tiket seharga 30. Penonton 2 (uang 90) mendapat tiket 80. Penonton 3 (uang 35) masih bisa membeli tiket 30 yang kedua, karena tadi hanya satu salinan yang terjual. Penonton 4 hanya membawa 20 sehingga pulang. Penonton 5 membeli tiket 100, dan tiket 50 tidak terjual.'],
            ['input' => "3 4\n7 7 7\n7 10 6 7\n", 'explanation' => 'Ketiga tiket berharga 7. Penonton 1 dan 2 masing-masing membeli satu, penonton 3 hanya membawa 6 sehingga pulang, dan penonton 4 mendapat tiket 7 yang terakhir.'],
        ],
        'tests' => function () {
            $arr = function (int $n, int $lo, int $hi) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand($lo, $hi);
                }

                return implode(' ', $a);
            };
            $mk = function (int $n, int $m, int $hLo, int $hHi, int $tLo, int $tHi) use ($arr) {
                return "$n $m\n".$arr($n, $hLo, $hHi)."\n".$arr($m, $tLo, $tHi)."\n";
            };

            return [
                "1 1\n5\n4\n",
                "1 2\n5\n5 5\n",
                "3 3\n1 1 1\n1 1 1\n",
                "4 6\n10 20 30 40\n100 100 100 100 100 100\n",
                $mk(10, 10, 1, 20, 1, 25),
                $mk(1000, 1000, 1, 100, 1, 120),
                $mk(100000, 100000, 1, 1000000, 1, 1000000),
                $mk(100000, 100000, 1, 50, 1, 60),
                $mk(100000, 30000, 1, 1000000, 1000000, 1000000),
                $mk(1000, 30000, 1, 1000000, 1, 1000000),
                "100000 100000\n".implode(' ', array_fill(0, 100000, 7777))."\n".$arr(100000, 1, 15000)."\n",
                $mk(100000, 1000, 500000, 1000000, 1, 1000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $h = T::ints($lines[1]);
            sort($h);
            $t = T::ints($lines[2]);
            $par = range(0, $n);
            $out = [];
            foreach ($t as $money) {
                $lo = 0;
                $hi = $n;
                while ($lo < $hi) {
                    $mid = ($lo + $hi) >> 1;
                    if ($h[$mid] <= $money) {
                        $lo = $mid + 1;
                    } else {
                        $hi = $mid;
                    }
                }
                $x = $lo;
                while ($par[$x] !== $x) {
                    $par[$x] = $par[$par[$x]];
                    $x = $par[$x];
                }
                if ($x === 0) {
                    $out[] = -1;
                } else {
                    $out[] = $h[$x - 1];
                    $par[$x] = $x - 1;
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<int> h(n), t(m);\n    for (int i = 0; i < n; i++) cin >> h[i];\n    for (int j = 0; j < m; j++) cin >> t[j];", "const [n, m] = readInts();\nconst h = readInts();\nconst t = readInts();", "n, m = map(int, input().split())\nh = list(map(int, input().split()))\nt = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    multiset<int> tiket;                 // harga boleh kembar -> multiset
    for (int i = 0; i < n; i++) {
        int h;
        cin >> h;
        tiket.insert(h);
    }

    string out;
    for (int j = 0; j < m; j++) {
        int t;
        cin >> t;
        auto it = tiket.upper_bound(t);  // tiket pertama yang harganya > t
        if (it == tiket.begin()) {
            out += "-1\n";               // tidak ada tiket dengan harga <= t
        } else {
            --it;                        // tiket termahal dengan harga <= t
            out += to_string(*it) + '\n';
            tiket.erase(it);             // hapus SATU salinan lewat iterator
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const h = Int32Array.from(readInts()).sort(); // harga menaik
const t = readInts();

// Tiket diberi nomor 1..n sesuai urutan harga (tiket nomor i berharga h[i - 1]).
// par[i] = i jika tiket i belum terjual; begitu terjual, par[i] = i - 1.
// find(x) = nomor terbesar <= x yang belum terjual (0 berarti tidak ada).
const par = new Int32Array(n + 1);
for (let i = 0; i <= n; i++) par[i] = i;
const find = (x) => {
  let akar = x;
  while (par[akar] !== akar) akar = par[akar];
  while (par[x] !== akar) { // kompresi jalur
    const berikut = par[x];
    par[x] = akar;
    x = berikut;
  }
  return akar;
};

const out = [];
for (let j = 0; j < m; j++) {
  // upper_bound: banyak tiket dengan harga <= t[j]
  let lo = 0, hi = n;
  while (lo < hi) {
    const mid = (lo + hi) >> 1;
    if (h[mid] <= t[j]) lo = mid + 1;
    else hi = mid;
  }
  const i = find(lo);
  if (i === 0) out.push(-1);
  else {
    out.push(h[i - 1]);
    par[i] = i - 1; // tiket i terjual: lompat ke kirinya
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_right
input = sys.stdin.readline

n, m = map(int, input().split())
h = sorted(map(int, input().split()))   # tiket nomor i (1..n) berharga h[i - 1]
t = list(map(int, input().split()))

# par[i] = i jika tiket i belum terjual; jika sudah, menunjuk ke kiri.
# Nomor 0 adalah penanda "tidak ada tiket".
par = list(range(n + 1))
out = []
for uang in t:
    x = bisect_right(h, uang)           # banyak tiket dengan harga <= uang
    while par[x] != x:                  # cari nomor terbesar <= x yang belum terjual
        par[x] = par[par[x]]            # path halving agar tetap cepat
        x = par[x]
    if x == 0:
        out.append(-1)
    else:
        out.append(h[x - 1])
        par[x] = x - 1
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Di C++ soal ini adalah latihan langsung untuk <code>multiset</code>. Masukkan semua harga ke <code>multiset&lt;int&gt;</code> (harga boleh kembar). Untuk penonton dengan uang t, <code>it = ms.upper_bound(t)</code> menunjuk tiket pertama yang harganya <em>lebih dari</em> t. Jika <code>it == ms.begin()</code>, tidak ada tiket yang terjangkau. Jika tidak, tiket termahal yang terjangkau ada di <code>prev(it)</code>: cetak harganya lalu hapus dengan <code>ms.erase(prev(it))</code>. Setiap penonton O(log N), total O((N + M) log N).</p>
<p><strong>Jebakan klasik:</strong> <code>ms.erase(x)</code> dengan sebuah <em>nilai</em> menghapus <em>semua</em> tiket berharga x sekaligus. Contoh kedua langsung salah jika kamu melakukannya. Hapus satu salinan lewat iterator, atau dengan <code>ms.erase(ms.find(x))</code>.</p>
<p><strong>Tanpa multiset (JavaScript, Python).</strong> Kedua bahasa ini tidak punya struktur terurut bawaan, tetapi soal ini hanya pernah <em>menghapus</em> tiket. Urutkan harga secara menaik dan beri nomor 1..N. Jika belum ada tiket yang terjual, tiket termahal yang terjangkau adalah nomor j = banyak harga ≤ t, yang didapat dengan binary search (<code>bisect_right</code>). Karena sebagian tiket sudah terjual, yang kita cari sebenarnya adalah <em>nomor terbesar ≤ j yang belum terjual</em>. Pakai DSU "tetangga kiri yang masih tersedia": <code>par[i] = i</code> selama tiket i belum terjual, dan begitu terjual <code>par[i] = i − 1</code>. Nomor 0 menjadi penanda "tidak ada tiket". Dengan kompresi jalur, <code>find(j)</code> berjalan hampir O(1) amortisasi, sehingga total waktunya didominasi pengurutan, O((N + M) log N).</p>
<p>Menelusuri array ke kiri satu per satu sambil melewati tiket yang sudah terjual memang benar, tetapi bisa O(N) per penonton, misalnya jika semua tiket berharga sama.</p>',
        'hints' => [
            'Bayangkan semua tiket diurutkan menurut harga. Tiket mana yang diambil penonton dengan uang t?',
            'Butuh struktur yang selalu terurut, boleh berisi nilai kembar, dan bisa menghapus satu elemen: multiset. Cari dengan upper_bound(t), lalu mundur satu langkah.',
            'Hapus dengan ms.erase(iterator), bukan ms.erase(harga). Tanpa multiset (JS/Python): array terurut + binary search + DSU yang melompati tiket terjual ke kiri.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'ruas-tanpa-lampu',
        'lesson' => 'set-map',
        'title' => 'Ruas Jalan Tanpa Lampu',
        'difficulty' => 'Sulit',
        'tags' => ['set', 'multiset', 'offline', 'proses terbalik', 'linked list'],
        'statement' => '<p>Jalan utama sebuah desa membentang dari titik 0 sampai titik <strong>X</strong>, dan awalnya belum memiliki lampu sama sekali. Mulai pekan ini, petugas memasang lampu satu per satu di titik-titik bilangan bulat di sepanjang jalan. Lampu-lampu itu membagi jalan menjadi beberapa <em>ruas tanpa lampu</em>: ruas di antara dua lampu yang bersebelahan, ruas dari titik 0 sampai lampu pertama, dan ruas dari lampu terakhir sampai titik X.</p>
<p>Di sela-sela pemasangan, kepala desa beberapa kali bertanya: <em>berapa panjang ruas tanpa lampu yang terpanjang saat ini?</em> Bantu petugas menjawab setiap pertanyaan itu.</p>',
        'input_format' => '<p>Baris pertama berisi <code>X Q</code>. Masing-masing dari Q baris berikutnya berisi satu kejadian, sesuai urutan waktu:</p>
<ul><li><code>+ p</code>: sebuah lampu dipasang di titik p;</li><li><code>?</code>: kepala desa bertanya.</li></ul>',
        'output_format' => '<p>Untuk setiap kejadian <code>?</code>, cetak satu baris berisi panjang ruas tanpa lampu yang terpanjang pada saat itu.</p>',
        'constraints' => '<ul><li>2 ≤ X ≤ 10<sup>9</sup></li><li>1 ≤ Q ≤ 200 000</li><li>Setiap lampu dipasang di titik p dengan 0 &lt; p &lt; X, dan tidak ada dua lampu di titik yang sama</li><li>Ada paling sedikit satu kejadian <code>?</code></li></ul>',
        'samples' => [
            ['input' => "10 7\n?\n+ 3\n?\n+ 7\n?\n+ 5\n?\n", 'explanation' => 'Sebelum ada lampu, seluruh jalan sepanjang 10 adalah satu ruas. Setelah lampu di titik 3, ruasnya 3 dan 7. Setelah lampu di titik 7, ruasnya 3, 4, dan 3. Setelah lampu di titik 5, ruasnya 3, 2, 2, dan 3, sehingga yang terpanjang 3.'],
            ['input' => "20 6\n+ 5\n?\n+ 2\n?\n+ 12\n?\n", 'explanation' => 'Lampu di titik 5 menyisakan ruas [5, 20] sepanjang 15. Lampu di titik 2 memotong ruas [0, 5], bukan ruas terpanjang, jadi jawabannya tetap 15. Lampu di titik 12 membelah [5, 20] menjadi 7 dan 8, sehingga ruas terpanjang menjadi 8.'],
        ],
        'tests' => function () use ($distinctPoints, $lampEvents) {
            $equal = range(100, 900, 100);
            T::shuffle($equal);
            $ev = [];
            foreach ($equal as $p) {
                $ev[] = "+ $p";
                $ev[] = '?';
            }
            $equalTest = '1000 '.count($ev)."\n".implode("\n", $ev)."\n";

            $all = range(1, 150000);
            T::shuffle($all);

            $alt = [];
            foreach ($distinctPoints(100000, 1, 999999) as $p) {
                $alt[] = "+ $p";
                $alt[] = '?';
            }
            $altTest = '1000000 '.count($alt)."\n".implode("\n", $alt)."\n";

            $asc = [];
            for ($i = 1; $i <= 100000; $i++) {
                $asc[] = '+ '.($i * 9999);
                if ($i % 10 === 0) {
                    $asc[] = '?';
                }
            }
            $ascTest = '1000000000 '.count($asc)."\n".implode("\n", $asc)."\n";

            $edge = array_fill(0, 1000, '?');
            foreach ($distinctPoints(100000, 1, 999999999) as $p) {
                $edge[] = "+ $p";
            }
            $edge = array_merge($edge, array_fill(0, 1000, '?'));
            $edgeTest = '1000000000 '.count($edge)."\n".implode("\n", $edge)."\n";

            return [
                "2 3\n?\n+ 1\n?\n",
                "5 1\n?\n",
                $equalTest,
                $lampEvents(30, $distinctPoints(10, 1, 29), 8),
                $lampEvents(1000, $distinctPoints(300, 1, 999), 300),
                $lampEvents(1000000000, $distinctPoints(160000, 1, 999999999), 40000),
                $lampEvents(150001, $all, 50000),
                $altTest,
                $ascTest,
                $lampEvents(1000000000, $distinctPoints(150000, 1, 200000), 20000),
                $edgeTest,
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$x, $q] = T::ints($lines[0]);
            $ops = [];
            $pts = [0, $x];
            for ($i = 1; $i <= $q; $i++) {
                $s = trim($lines[$i]);
                if ($s[0] === '+') {
                    $p = (int) substr($s, 1);
                    $ops[] = $p;
                    $pts[] = $p;
                } else {
                    $ops[] = -1;
                }
            }
            sort($pts);
            $L = count($pts);
            $id = array_flip($pts);
            $left = [];
            $right = [];
            $best = 0;
            for ($i = 0; $i < $L; $i++) {
                $left[$i] = $i - 1;
                $right[$i] = $i + 1;
                if ($i + 1 < $L) {
                    $best = max($best, $pts[$i + 1] - $pts[$i]);
                }
            }
            $ans = [];
            for ($i = $q - 1; $i >= 0; $i--) {
                if ($ops[$i] === -1) {
                    $ans[] = $best;
                } else {
                    $k = $id[$ops[$i]];
                    $a = $left[$k];
                    $b = $right[$k];
                    $right[$a] = $b;
                    $left[$b] = $a;
                    $best = max($best, $pts[$b] - $pts[$a]);
                }
            }

            return implode("\n", array_reverse($ans));
        },
        'starter' => $st("    int x, q;\n    cin >> x >> q;\n    for (int i = 0; i < q; i++) {\n        string op;\n        cin >> op;          // \"+\" atau \"?\"\n        if (op == \"+\") {\n            int p;\n            cin >> p;\n        }\n    }", "const [x, q] = readInts();\nfor (let i = 0; i < q; i++) {\n  const s = readLine().trim(); // \"+ p\" atau \"?\"\n}", "x, q = map(int, input().split())\nfor _ in range(q):\n    s = input().split()   # ['+', 'p'] atau ['?']"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int x, q;
    cin >> x >> q;
    set<int> pos = {0, x};               // posisi lampu, ditambah kedua ujung jalan
    multiset<int> ruas = {x};            // panjang semua ruas (boleh kembar)

    string out;
    for (int i = 0; i < q; i++) {
        string op;
        cin >> op;
        if (op == "+") {
            int p;
            cin >> p;
            auto it = pos.upper_bound(p);   // tetangga kanan p
            int r = *it, l = *prev(it);     // tetangga kiri p
            ruas.erase(ruas.find(r - l));   // ruas [l, r] terbelah: hapus SATU salinan
            ruas.insert(p - l);
            ruas.insert(r - p);
            pos.insert(p);
        } else {
            out += to_string(*ruas.rbegin()) + '\n';   // elemen terbesar
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [x, q] = readInts();
// ops[i] = posisi lampu, atau -1 untuk pertanyaan
const ops = new Int32Array(q);
let banyak = 0;
for (let i = 0; i < q; i++) {
  const s = readLine().trim();
  if (s[0] === "+") {
    ops[i] = Number(s.slice(1));
    banyak++;
  } else ops[i] = -1;
}

// Proses OFFLINE dan TERBALIK: mulai dari keadaan akhir (semua lampu terpasang).
const pos = new Int32Array(banyak + 2);
let k = 0;
pos[k++] = 0;
pos[k++] = x;
for (let i = 0; i < q; i++) if (ops[i] !== -1) pos[k++] = ops[i];
pos.sort();
const L = pos.length;
const cari = (p) => { // indeks p di pos (binary search)
  let lo = 0, hi = L - 1;
  while (lo < hi) {
    const mid = (lo + hi) >> 1;
    if (pos[mid] < p) lo = mid + 1;
    else hi = mid;
  }
  return lo;
};

// Linked list ganda di atas posisi terurut.
const kiri = new Int32Array(L), kanan = new Int32Array(L);
let terbesar = 0;
for (let i = 0; i < L; i++) {
  kiri[i] = i - 1;
  kanan[i] = i + 1;
  if (i + 1 < L && pos[i + 1] - pos[i] > terbesar) terbesar = pos[i + 1] - pos[i];
}

const jawab = [];
for (let i = q - 1; i >= 0; i--) {
  if (ops[i] === -1) jawab.push(terbesar);
  else {
    // Batalkan pemasangan: cabut lampu, dua ruas di sebelahnya bergabung.
    const j = cari(ops[i]);
    const a = kiri[j], b = kanan[j];
    kanan[a] = b;
    kiri[b] = a;
    if (pos[b] - pos[a] > terbesar) terbesar = pos[b] - pos[a];
  }
}
jawab.reverse();
console.log(jawab.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

x, q = map(int, input().split())
ops = []                                # posisi lampu, atau -1 untuk pertanyaan
for _ in range(q):
    s = input().split()
    ops.append(int(s[1]) if s[0] == "+" else -1)

# Proses OFFLINE dan TERBALIK: mulai dari keadaan akhir (semua lampu terpasang).
pos = sorted([0, x] + [p for p in ops if p != -1])
L = len(pos)
indeks = {p: i for i, p in enumerate(pos)}
kiri = list(range(-1, L - 1))           # linked list ganda di atas posisi terurut
kanan = list(range(1, L + 1))
terbesar = max(pos[i + 1] - pos[i] for i in range(L - 1))

jawab = []
for p in reversed(ops):
    if p == -1:
        jawab.append(terbesar)
    else:
        # batalkan pemasangan: cabut lampu, dua ruas di sebelahnya bergabung
        i = indeks[p]
        a = kiri[i]
        b = kanan[i]
        kanan[a] = b
        kiri[b] = a
        if pos[b] - pos[a] > terbesar:
            terbesar = pos[b] - pos[a]
jawab.reverse()
print("\n".join(map(str, jawab)))
CODE,
        ],
        'editorial' => '<p>Saat lampu dipasang di titik p, hanya satu ruas yang berubah: ruas [l, r] yang memuat p terbelah menjadi [l, p] dan [p, r]. Jadi kita butuh dua hal: menemukan tetangga kiri dan kanan p dengan cepat, dan mengetahui ruas terpanjang di antara semua ruas yang terus berubah.</p>
<p><strong>Solusi C++: set + multiset.</strong> Simpan posisi lampu, ditambah titik 0 dan X, dalam <code>set&lt;int&gt;</code>, dan simpan panjang semua ruas dalam <code>multiset&lt;int&gt;</code>. Awalnya set = {0, X} dan multiset = {X}. Untuk <code>+ p</code>: <code>it = pos.upper_bound(p)</code> memberi tetangga kanan r = <code>*it</code>, dan tetangga kiri l = <code>*prev(it)</code>. Hapus <strong>satu salinan</strong> panjang r − l, masukkan p − l dan r − p, lalu masukkan p ke set. Untuk <code>?</code>, jawabannya elemen terbesar multiset, <code>*ruas.rbegin()</code>. Setiap kejadian O(log Q).</p>
<p>Jebakan: panjang ruas sangat mungkin kembar (misalnya lampu-lampu berjarak sama), sehingga harus memakai <code>multiset</code>, bukan <code>set</code>, dan menghapus dengan <code>ruas.erase(ruas.find(r − l))</code>. <code>ruas.erase(r − l)</code> menghapus semua ruas sepanjang itu. Menghitung ulang maksimum dengan menelusuri semua ruas setiap kali ditanya juga terlalu lambat: O(Q<sup>2</sup>).</p>
<p><strong>Tanpa multiset (JavaScript, Python): proses terbalik.</strong> Semua kejadian bisa dibaca dulu, jadi kerjakan secara <em>offline</em> dari belakang. Pada keadaan akhir semua lampu sudah terpasang. Urutkan posisinya bersama 0 dan X, hubungkan sebagai linked list ganda (<code>kiri[i]</code>, <code>kanan[i]</code>), dan hitung ruas terpanjang M saat itu. Sekarang telusuri kejadian dari yang terakhir:</p>
<ul><li><code>?</code> dijawab dengan M saat ini;</li>
<li><code>+ p</code> dibatalkan dengan mencabut lampu p dari linked list, sehingga ruas di kiri dan kanannya bergabung menjadi satu ruas sepanjang <code>pos[kanan] − pos[kiri]</code>.</li></ul>
<p>Mencabut lampu tidak pernah memendekkan ruas mana pun; hanya muncul satu ruas baru yang lebih panjang dari kedua ruas pembentuknya. Maka maksimum cukup diperbarui dengan <code>M = max(M, ruas gabungan)</code>, tanpa perlu menghapus apa pun dari struktur data. Inilah gunanya membalik arah waktu: operasi "membelah" yang sulit diubah menjadi operasi "menggabung" yang mudah. Totalnya O(Q log Q) untuk pengurutan dan O(Q) untuk sisanya. Jawaban dikumpulkan terbalik, jadi balik lagi sebelum dicetak.</p>
<p>Di C++, set + multiset sudah cukup dan lebih langsung. Teknik terbalik ini berguna saat bahasa tidak menyediakan struktur terurut, dan juga sering muncul di soal lain yang hanya menambah atau hanya menghapus.</p>',
        'hints' => [
            'Saat lampu baru dipasang, hanya satu ruas yang berubah: ruas yang memuat titik itu terbelah dua. Bagaimana menemukan lampu terdekat di kiri dan kanannya dengan cepat?',
            'Simpan posisi lampu (beserta 0 dan X) di set dan panjang semua ruas di multiset. Hapus satu salinan ruas lama, masukkan dua ruas baru; jawabannya elemen terbesar multiset.',
            'Tanpa multiset: proses kejadian dari belakang. Mulai dari semua lampu terpasang, lalu cabut satu per satu memakai linked list. Mencabut lampu hanya menggabungkan ruas, jadi maksimum cukup diperbarui dengan max.',
        ],
    ],
];
