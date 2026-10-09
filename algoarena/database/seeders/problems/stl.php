<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Fondasi & STL: vector & comparator, stack/queue/deque, set/map/priority_queue,
 * algoritma STL, dan bitset.
 * Semua kode C++ harus lolos C++14: tanpa structured binding.
 */

/** Nama acak huruf kecil yang dijamin unik. */
$uniqueNames = function (int $n, int $minLen = 3, int $maxLen = 8): array {
    $seen = [];
    $out = [];
    while (count($out) < $n) {
        $w = T::word(mt_rand($minLen, $maxLen), 'abcdefghijklmnopqrstuvwxyz');
        if (! isset($seen[$w])) {
            $seen[$w] = true;
            $out[] = $w;
        }
    }

    return $out;
};

return [
    // ───────────────────────── Vector, Pair & Comparator ─────────────────────────
    [
        'slug' => 'papan-skor',
        'lesson' => 'stl-vector',
        'title' => 'Papan Skor Lomba',
        'difficulty' => 'Mudah',
        'tags' => ['sorting', 'comparator', 'struct'],
        'statement' => '<p>Lomba pemrograman baru saja selesai. Setiap tim tercatat dengan <strong>nama</strong>, <strong>banyak soal</strong> yang diselesaikan, dan <strong>penalti</strong> (dalam menit).</p>
<p>Panitia mengurutkan papan skor dengan aturan berikut:</p>
<ol><li>Tim dengan soal <strong>lebih banyak</strong> berada di atas.</li><li>Jika banyak soalnya sama, tim dengan penalti <strong>lebih kecil</strong> di atas.</li><li>Jika masih sama, urutkan nama tim menurut <strong>abjad</strong>.</li></ol>
<p>Cetak nama tim dari atas ke bawah.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi <code>nama soal penalti</code>.</p>',
        'output_format' => '<p>N baris, nama tim sesuai urutan papan skor.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>Nama terdiri dari 1–10 huruf kecil dan semuanya berbeda</li><li>0 ≤ soal ≤ 15, 0 ≤ penalti ≤ 100 000</li></ul>',
        'samples' => [
            ['input' => "5\nkancil 3 120\nbadak 4 300\ngajah 3 95\nzebra 3 95\nbebek 4 300\n", 'explanation' => 'badak dan bebek sama-sama 4 soal dengan penalti 300, jadi diurutkan menurut abjad: badak lalu bebek. Di antara tim 3 soal, gajah dan zebra (penalti 95) di atas kancil (120), dan gajah sebelum zebra.'],
        ],
        'tests' => function () use ($uniqueNames) {
            $make = function (int $n, int $maxSoal, int $maxPen) use ($uniqueNames) {
                $names = $uniqueNames($n, 1, 10);
                $lines = [(string) $n];
                foreach ($names as $nm) {
                    $lines[] = $nm.' '.mt_rand(0, $maxSoal).' '.mt_rand(0, $maxPen);
                }

                return implode("\n", $lines)."\n";
            };

            return [
                "1\nsolo 0 0\n",
                $make(8, 2, 3),
                $make(50, 3, 10),
                $make(2000, 15, 100000),
                $make(200000, 15, 100000),
                $make(200000, 2, 5),
                $make(200000, 0, 0),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $nama = $soal = $pen = [];
            for ($i = 1; $i <= $n; $i++) {
                $p = explode(' ', trim($lines[$i]));
                $nama[] = $p[0];
                $soal[] = (int) $p[1];
                $pen[] = (int) $p[2];
            }
            array_multisort($soal, SORT_DESC, SORT_NUMERIC, $pen, SORT_ASC, SORT_NUMERIC, $nama, SORT_ASC, SORT_STRING);

            return implode("\n", $nama);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

struct Tim {
    string nama;
    int soal, penalti;
};

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<Tim> t(n);
    for (auto& x : t) cin >> x.nama >> x.soal >> x.penalti;

    // Urutkan t dengan comparator, lalu cetak namanya

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const t = [];
for (let i = 0; i < n; i++) {
  const [nama, soal, penalti] = readLine().split(" ");
  t.push({ nama, soal: Number(soal), penalti: Number(penalti) });
}

// Urutkan t, lalu cetak namanya
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
t = []
for _ in range(n):
    nama, soal, penalti = input().split()
    t.append((nama, int(soal), int(penalti)))

# Urutkan t, lalu cetak namanya
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

struct Tim {
    string nama;
    int soal, penalti;
};

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<Tim> t(n);
    for (auto& x : t) cin >> x.nama >> x.soal >> x.penalti;

    sort(t.begin(), t.end(), [](const Tim& a, const Tim& b) {
        if (a.soal != b.soal) return a.soal > b.soal;          // soal menurun
        if (a.penalti != b.penalti) return a.penalti < b.penalti; // penalti menaik
        return a.nama < b.nama;                                 // abjad
    });

    string out;
    for (const auto& x : t) {
        out += x.nama;
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const t = [];
for (let i = 0; i < n; i++) {
  const [nama, soal, penalti] = readLine().split(" ");
  t.push({ nama, soal: Number(soal), penalti: Number(penalti) });
}
t.sort((a, b) => {
  if (a.soal !== b.soal) return b.soal - a.soal;
  if (a.penalti !== b.penalti) return a.penalti - b.penalti;
  return a.nama < b.nama ? -1 : a.nama > b.nama ? 1 : 0;
});
console.log(t.map((x) => x.nama).join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
t = []
for _ in range(n):
    nama, soal, penalti = input().split()
    t.append((nama, int(soal), int(penalti)))

# Kunci tuple: soal menurun (dinegasi), penalti menaik, nama menaik
t.sort(key=lambda x: (-x[1], x[2], x[0]))
print("\n".join(x[0] for x in t))
CODE,
        ],
        'editorial' => '<p>Ini soal pengurutan <strong>multi-kriteria</strong>. Simpan setiap tim dalam <code>struct</code>, lalu tulis comparator yang menjawab "apakah tim a harus di atas tim b?":</p>
<ol><li>Jika banyak soal berbeda, jawabannya <code>a.soal &gt; b.soal</code> (menurun).</li><li>Jika sama tetapi penalti berbeda, jawabannya <code>a.penalti &lt; b.penalti</code> (menaik).</li><li>Jika masih sama, <code>a.nama &lt; b.nama</code>.</li></ol>
<p>Karena nama semuanya berbeda, setiap pasangan tim punya urutan yang pasti, sehingga hasilnya selalu sama walaupun <code>std::sort</code> tidak stabil. Kompleksitas <code>O(N log N)</code>.</p>
<p><strong>Jebakan:</strong> jangan memakai <code>&gt;=</code> atau <code>&lt;=</code> di comparator. Gunakan juga <code>const Tim&amp;</code> agar string tidak disalin pada setiap perbandingan.</p>',
        'hints' => [
            'Simpan setiap tim sebagai struct (nama, soal, penalti) di dalam vector.',
            'Comparator memeriksa kriteria satu per satu: jika kriteria pertama berbeda, langsung kembalikan hasil perbandingannya.',
            'if (a.soal != b.soal) return a.soal > b.soal; if (a.penalti != b.penalti) return a.penalti < b.penalti; return a.nama < b.nama;',
        ],
    ],

    [
        'slug' => 'peringkat-seri',
        'lesson' => 'stl-vector',
        'title' => 'Peringkat dengan Seri',
        'difficulty' => 'Sedang',
        'tags' => ['sorting', 'pair', 'argsort'],
        'statement' => '<p>Ada <strong>N</strong> peserta olimpiade dengan nilai <code>a<sub>1</sub>, a<sub>2</sub>, …, a<sub>N</sub></code>. Panitia memakai <em>peringkat kompetisi</em>: peringkat seorang peserta adalah <strong>1 + banyak peserta yang nilainya lebih tinggi</strong>.</p>
<p>Jadi peserta dengan nilai sama mendapat peringkat yang sama, dan peringkat sesudahnya "melompat". Misalnya nilai 90, 85, 85, 70 mendapat peringkat 1, 2, 2, 4.</p>
<p>Cetak peringkat setiap peserta <strong>sesuai urutan input</strong>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris berisi N bilangan: peringkat peserta ke-1 sampai ke-N, dipisahkan spasi.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6\n85 90 70 85 100 70\n", 'explanation' => 'Urutan menurun: 100, 90, 85, 85, 70, 70. Peserta bernilai 85 punya 2 peserta di atasnya (100 dan 90), jadi peringkatnya 3. Peserta bernilai 70 punya 4 peserta di atasnya, peringkatnya 5.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return [
                "1\n0\n", "4\n5 5 5 5\n", $make(10, 1, 5), $make(1000, 0, 1000000000),
                $make(200000, 0, 1000000000), $make(200000, 0, 30), $make(200000, 7, 7),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $s = $a;
            rsort($s);
            $rank = [];
            foreach ($s as $i => $v) {
                if (! isset($rank[$v])) {
                    $rank[$v] = $i + 1;
                }
            }

            return implode(' ', array_map(fn ($v) => $rank[$v], $a));
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    vector<int> peringkat(n);
    // Isi peringkat[i] untuk setiap peserta

    for (int i = 0; i < n; i++) cout << peringkat[i] << " \n"[i + 1 == n];
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
const peringkat = new Array(n).fill(0);

// Isi peringkat[i] untuk setiap peserta

console.log(peringkat.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
peringkat = [0] * n

# Isi peringkat[i] untuk setiap peserta

print(" ".join(map(str, peringkat)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<pair<long long, int>> p(n);   // (nilai, indeks asli)
    for (int i = 0; i < n; i++) {
        cin >> p[i].first;
        p[i].second = i;
    }
    // Nilai menurun; indeks tidak penting untuk peringkat
    sort(p.begin(), p.end(), [](const pair<long long, int>& x, const pair<long long, int>& y) {
        return x.first > y.first;
    });

    vector<int> peringkat(n);
    for (int k = 0; k < n; k++) {
        // Nilai sama dengan sebelumnya: peringkat ikut sebelumnya
        if (k > 0 && p[k].first == p[k - 1].first) peringkat[p[k].second] = peringkat[p[k - 1].second];
        else peringkat[p[k].second] = k + 1;   // k peserta di atasnya
    }

    for (int i = 0; i < n; i++) cout << peringkat[i] << " \n"[i + 1 == n];
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
const idx = Array.from({ length: n }, (_, i) => i);
idx.sort((i, j) => a[j] - a[i]);          // nilai menurun
const peringkat = new Array(n).fill(0);
for (let k = 0; k < n; k++) {
  const i = idx[k];
  if (k > 0 && a[i] === a[idx[k - 1]]) peringkat[i] = peringkat[idx[k - 1]];
  else peringkat[i] = k + 1;
}
console.log(peringkat.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
idx = sorted(range(n), key=lambda i: -a[i])   # indeks, nilai menurun
peringkat = [0] * n
for k, i in enumerate(idx):
    if k > 0 and a[i] == a[idx[k - 1]]:
        peringkat[i] = peringkat[idx[k - 1]]
    else:
        peringkat[i] = k + 1
print(" ".join(map(str, peringkat)))
CODE,
        ],
        'editorial' => '<p>Menghitung "banyak peserta yang lebih tinggi" dengan loop untuk setiap peserta butuh <code>O(N²)</code> = 4 · 10<sup>10</sup>: terlalu lambat.</p>
<p>Urutkan pasangan <code>(nilai, indeks asli)</code> secara menurun. Pada urutan itu, peserta di posisi ke-k (mulai 0) punya tepat k peserta di depannya, sehingga peringkatnya k + 1. Pengecualian: jika nilainya sama dengan peserta sebelumnya, peringkatnya ikut peserta sebelumnya (seri).</p>
<p>Karena kita menyimpan indeks asli, peringkat bisa ditulis langsung ke posisi semula: <code>peringkat[p[k].second] = …</code>. Inilah pola <strong>argsort</strong>: urutkan, hitung, lalu kembalikan ke urutan input. Total <code>O(N log N)</code>.</p>',
        'hints' => [
            'Peringkat bergantung pada urutan nilai, tetapi jawaban harus dicetak sesuai urutan input. Simpan indeks aslinya.',
            'Urutkan pasangan (nilai, indeks) menurun berdasarkan nilai. Peserta di posisi k pada urutan itu punya k peserta di depannya.',
            'Jika nilai di posisi k sama dengan posisi k − 1, peringkatnya sama; jika tidak, peringkatnya k + 1. Tulis ke peringkat[indeks asli].',
        ],
    ],

    // ───────────────────────── Stack, Queue & Deque ─────────────────────────
    [
        'slug' => 'kedalaman-kurung',
        'lesson' => 'stack-queue',
        'title' => 'Kedalaman Kurung',
        'difficulty' => 'Mudah',
        'tags' => ['stack', 'kurung'],
        'statement' => '<p>Seorang guru membuat soal kurung untuk muridnya. Setiap soal berupa string yang hanya berisi karakter <code>( ) [ ] { }</code>.</p>
<p>Sebuah string disebut <strong>seimbang</strong> jika setiap kurung buka punya kurung tutup yang sejenis, dan pasangan-pasangannya tidak saling silang. Contohnya <code>{[()]}()</code> seimbang, sedangkan <code>([)]</code>, <code>(()</code>, dan <code>)(</code> tidak.</p>
<p><strong>Kedalaman</strong> string seimbang adalah banyak kurung terbuka terbanyak pada satu saat. Misalnya <code>{[()]}()</code> berkedalaman 3, dan <code>()()</code> berkedalaman 1.</p>
<p>Untuk setiap string, cetak kedalamannya jika seimbang, atau <code>-1</code> jika tidak seimbang.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>, banyak string. T baris berikutnya masing-masing berisi satu string tidak kosong.</p>',
        'output_format' => '<p>T baris, jawaban untuk setiap string.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 100 000</li><li>Total panjang semua string ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "5\n{[()]}()\n([)]\n((()))[]\n(()\n)(\n", 'explanation' => 'String pertama seimbang dengan kedalaman 3 (saat membaca "(" ketiga ada 3 kurung terbuka). String ketiga berkedalaman 3. Sisanya tidak seimbang.'],
        ],
        'tests' => function () {
            $balanced = function (int $pairs): string {
                $open = '([{';
                $close = ')]}';
                $s = '';
                $st = [];
                $left = $pairs;
                while ($left > 0 || $st) {
                    if ($left > 0 && (! $st || mt_rand(0, 1))) {
                        $k = mt_rand(0, 2);
                        $s .= $open[$k];
                        $st[] = $k;
                        $left--;
                    } else {
                        $s .= $close[array_pop($st)];
                    }
                }

                return $s;
            };
            $corrupt = function (string $s): string {
                $n = strlen($s);
                switch (mt_rand(0, 3)) {
                    case 0: // ganti jenis satu kurung
                        $i = mt_rand(0, $n - 1);
                        $map = ['(' => '[', '[' => '{', '{' => '(', ')' => ']', ']' => '}', '}' => ')'];
                        $s[$i] = $map[$s[$i]];

                        return $s;
                    case 1: // buang satu karakter
                        $i = mt_rand(0, $n - 1);

                        return $n > 1 ? substr($s, 0, $i).substr($s, $i + 1) : ')';
                    case 2: // tukar dua karakter berbeda
                        $i = mt_rand(0, $n - 1);
                        $j = mt_rand(0, $n - 1);
                        [$s[$i], $s[$j]] = [$s[$j], $s[$i]];

                        return $s;
                    default: // tambah kurung buka di akhir
                        return $s.'(';
                }
            };
            $make = function (int $t, int $maxPairs) use ($balanced, $corrupt) {
                $lines = [(string) $t];
                for ($i = 0; $i < $t; $i++) {
                    $s = $balanced(mt_rand(1, $maxPairs));
                    if (mt_rand(0, 2) === 0) {
                        $s = $corrupt($s);
                    }
                    $lines[] = $s;
                }

                return implode("\n", $lines)."\n";
            };
            $deep = str_repeat('(', 250000).str_repeat(')', 250000);

            return [
                "3\n()\n)\n(\n", $make(20, 4), $make(1000, 30), $make(100000, 4), $make(10, 50000),
                "2\n$deep\n".str_repeat('[', 200000).str_repeat(']', 199999).")\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $pair = [')' => '(', ']' => '[', '}' => '{'];
            $out = [];
            for ($k = 1; $k <= $t; $k++) {
                $s = trim($lines[$k]);
                $st = [];
                $best = 0;
                $ok = true;
                $n = strlen($s);
                for ($i = 0; $i < $n; $i++) {
                    $c = $s[$i];
                    if ($c === '(' || $c === '[' || $c === '{') {
                        $st[] = $c;
                        if (count($st) > $best) {
                            $best = count($st);
                        }
                    } elseif (! $st || end($st) !== $pair[$c]) {
                        $ok = false;
                        break;
                    } else {
                        array_pop($st);
                    }
                }
                $out[] = ($ok && ! $st) ? $best : -1;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int t;
    cin >> t;
    while (t--) {
        string s;
        cin >> s;
        // Periksa s dengan stack, catat ukuran stack terbesar

    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const t = Number(readLine());
const out = [];
for (let k = 0; k < t; k++) {
  const s = readLine().trim();
  // Periksa s dengan stack, catat ukuran stack terbesar

}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

t = int(input())
out = []
for _ in range(t):
    s = input().strip()
    # Periksa s dengan stack, catat ukuran stack terbesar

print("\n".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

bool cocok(char buka, char tutup) {
    return (buka == '(' && tutup == ')') || (buka == '[' && tutup == ']') || (buka == '{' && tutup == '}');
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int t;
    cin >> t;
    while (t--) {
        string s;
        cin >> s;
        stack<char> st;
        int terdalam = 0;
        bool ok = true;
        for (char c : s) {
            if (c == '(' || c == '[' || c == '{') {
                st.push(c);
                terdalam = max(terdalam, (int)st.size());   // kurung terbuka saat ini
            } else {
                if (st.empty() || !cocok(st.top(), c)) {
                    ok = false;
                    break;
                }
                st.pop();
            }
        }
        if (!st.empty()) ok = false;                         // ada yang tidak ditutup
        cout << (ok ? terdalam : -1) << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const t = Number(readLine());
const pasangan = { ")": "(", "]": "[", "}": "{" };
const out = [];
for (let k = 0; k < t; k++) {
  const s = readLine().trim();
  const st = [];
  let terdalam = 0;
  let ok = true;
  for (const c of s) {
    if (c === "(" || c === "[" || c === "{") {
      st.push(c);
      if (st.length > terdalam) terdalam = st.length;
    } else if (st.length === 0 || st[st.length - 1] !== pasangan[c]) {
      ok = false;
      break;
    } else {
      st.pop();
    }
  }
  if (st.length > 0) ok = false;
  out.push(ok ? terdalam : -1);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

pasangan = {')': '(', ']': '[', '}': '{'}
data = sys.stdin.read().split()
t = int(data[0])
out = []
for s in data[1:1 + t]:
    st = []
    terdalam = 0
    ok = True
    for c in s:
        if c in '([{':
            st.append(c)
            if len(st) > terdalam:
                terdalam = len(st)
        elif not st or st[-1] != pasangan[c]:
            ok = False
            break
        else:
            st.pop()
    if st:
        ok = False
    out.append(terdalam if ok else -1)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Pakai stack persis seperti memeriksa kurung seimbang: kurung buka di-<em>push</em>, kurung tutup harus cocok dengan <code>top()</code> lalu di-<em>pop</em>. String seimbang jika tidak pernah gagal dan stack kosong di akhir.</p>
<p>Isi stack pada suatu saat adalah <strong>kurung yang sedang terbuka</strong>. Jadi kedalaman adalah ukuran stack terbesar selama pemrosesan. Cukup tambahkan satu baris setelah <code>push</code>: <code>terdalam = max(terdalam, st.size())</code>.</p>
<p>Setiap karakter diproses sekali, total <code>O(panjang semua string)</code> = 10<sup>6</sup> langkah.</p>
<p><strong>Jebakan:</strong> string <code>((</code> tidak gagal di tengah jalan, tetapi stack-nya tidak kosong di akhir, jadi jawabannya −1. String <code>)(</code> gagal di karakter pertama karena stack kosong.</p>',
        'hints' => [
            'Mulailah dari memeriksa apakah string seimbang memakai stack.',
            'Isi stack pada setiap saat adalah kurung yang sedang terbuka. Kedalaman = ukuran stack terbesar.',
            'Setelah setiap push, perbarui terdalam = max(terdalam, ukuran stack). Jangan lupa: stack harus kosong di akhir.',
        ],
    ],

    [
        'slug' => 'antrian-vip',
        'lesson' => 'stack-queue',
        'title' => 'Antrian Loket VIP',
        'difficulty' => 'Sedang',
        'tags' => ['deque', 'simulasi'],
        'statement' => '<p>Sebuah loket tiket punya satu antrian. Kamu diberi <strong>Q</strong> kejadian berurutan:</p>
<ul>
<li><code>DATANG x</code>: pengunjung bernomor x berdiri di <strong>belakang</strong> antrian.</li>
<li><code>VIP x</code>: pengunjung VIP bernomor x langsung berdiri di <strong>paling depan</strong> antrian.</li>
<li><code>PERGI</code>: pengunjung di <strong>paling belakang</strong> bosan dan meninggalkan antrian. Jika antrian kosong, tidak terjadi apa-apa.</li>
<li><code>LAYANI</code>: petugas melayani pengunjung <strong>paling depan</strong>; cetak nomornya. Jika antrian kosong, cetak <code>KOSONG</code>.</li>
</ul>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi satu kejadian.</p>',
        'output_format' => '<p>Satu baris untuk setiap kejadian <code>LAYANI</code>.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ x ≤ 10<sup>9</sup></li><li>Ada paling sedikit satu kejadian LAYANI</li></ul>',
        'samples' => [
            ['input' => "9\nDATANG 5\nDATANG 7\nVIP 9\nLAYANI\nDATANG 3\nPERGI\nLAYANI\nLAYANI\nLAYANI\n", 'explanation' => 'Antrian: [5] → [5, 7] → [9, 5, 7]. Layani 9 → [5, 7] → [5, 7, 3] → 3 pergi → [5, 7]. Layani 5, layani 7, lalu antrian kosong.'],
        ],
        'tests' => function () {
            $make = function (int $q, array $w) {
                $lines = [(string) $q];
                $size = 0;
                $hasServe = false;
                for ($i = 0; $i < $q; $i++) {
                    $r = mt_rand(1, array_sum($w));
                    if ($i === $q - 1 && ! $hasServe) {
                        $r = PHP_INT_MAX;
                    }
                    if ($r <= $w[0]) {
                        $lines[] = 'DATANG '.mt_rand(1, 1000000000);
                        $size++;
                    } elseif ($r <= $w[0] + $w[1]) {
                        $lines[] = 'VIP '.mt_rand(1, 1000000000);
                        $size++;
                    } elseif ($r <= $w[0] + $w[1] + $w[2]) {
                        $lines[] = 'PERGI';
                        $size = max(0, $size - 1);
                    } else {
                        $lines[] = 'LAYANI';
                        $hasServe = true;
                        $size = max(0, $size - 1);
                    }
                }

                return implode("\n", $lines)."\n";
            };

            return [
                "3\nLAYANI\nPERGI\nLAYANI\n",
                $make(20, [3, 2, 1, 3]),
                $make(1000, [4, 2, 1, 4]),
                $make(200000, [5, 3, 1, 4]),
                $make(200000, [1, 1, 1, 3]),
                $make(200000, [10, 0, 0, 1]),
                $make(200000, [0, 10, 0, 1]),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $dq = new SplDoublyLinkedList;
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $p = explode(' ', trim($lines[$i]));
                switch ($p[0]) {
                    case 'DATANG':
                        $dq->push((int) $p[1]);
                        break;
                    case 'VIP':
                        $dq->unshift((int) $p[1]);
                        break;
                    case 'PERGI':
                        if (! $dq->isEmpty()) {
                            $dq->pop();
                        }
                        break;
                    default:
                        $out[] = $dq->isEmpty() ? 'KOSONG' : $dq->shift();
                }
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    deque<long long> antrian;
    while (q--) {
        string jenis;
        cin >> jenis;
        // Tangani DATANG x, VIP x, PERGI, dan LAYANI

    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [jenis, x] = readLine().split(" ");
  // Tangani DATANG x, VIP x, PERGI, dan LAYANI
  // Hati-hati: array.shift() / unshift() di JavaScript O(n)!
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

q = int(input())
antrian = deque()
out = []
for _ in range(q):
    p = input().split()
    # Tangani DATANG x, VIP x, PERGI, dan LAYANI

print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    deque<long long> antrian;
    string out;
    while (q--) {
        string jenis;
        cin >> jenis;
        if (jenis == "DATANG") {
            long long x;
            cin >> x;
            antrian.push_back(x);            // berdiri di belakang
        } else if (jenis == "VIP") {
            long long x;
            cin >> x;
            antrian.push_front(x);           // menyelak ke depan
        } else if (jenis == "PERGI") {
            if (!antrian.empty()) antrian.pop_back();
        } else {                             // LAYANI
            if (antrian.empty()) {
                out += "KOSONG\n";
            } else {
                out += to_string(antrian.front()) + "\n";
                antrian.pop_front();
            }
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Deque dengan array melingkar: semua operasi O(1)
const q = Number(readLine());
const CAP = 1 << 19;                 // > 2 * 200000
const buf = new Array(CAP);
let head = 0, size = 0;              // head = indeks elemen paling depan
const out = [];
for (let i = 0; i < q; i++) {
  const [jenis, x] = readLine().split(" ");
  if (jenis === "DATANG") {
    buf[(head + size) & (CAP - 1)] = x;
    size++;
  } else if (jenis === "VIP") {
    head = (head - 1 + CAP) & (CAP - 1);
    buf[head] = x;
    size++;
  } else if (jenis === "PERGI") {
    if (size > 0) size--;
  } else {
    if (size === 0) out.push("KOSONG");
    else {
      out.push(buf[head]);
      head = (head + 1) & (CAP - 1);
      size--;
    }
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

q = int(input())
antrian = deque()
out = []
for _ in range(q):
    p = input().split()
    if p[0] == "DATANG":
        antrian.append(p[1])
    elif p[0] == "VIP":
        antrian.appendleft(p[1])
    elif p[0] == "PERGI":
        if antrian:
            antrian.pop()
    else:
        out.append(antrian.popleft() if antrian else "KOSONG")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Antrian ini diubah di <strong>kedua ujung</strong>: DATANG dan PERGI di belakang, VIP dan LAYANI di depan. Struktur data yang tepat adalah <code>deque</code>, karena keempat operasinya O(1):</p>
<table class="cx-table"><tr><th>Kejadian</th><th>Operasi deque</th></tr><tr><td>DATANG x</td><td><code>push_back(x)</code></td></tr><tr><td>VIP x</td><td><code>push_front(x)</code></td></tr><tr><td>PERGI</td><td><code>pop_back()</code> jika tidak kosong</td></tr><tr><td>LAYANI</td><td>cetak <code>front()</code> lalu <code>pop_front()</code></td></tr></table>
<p>Total <code>O(Q)</code>. Memakai <code>vector</code> dengan <code>insert(begin())</code> atau <code>erase(begin())</code> akan O(Q²) dan TLE.</p>
<p><strong>Catatan JavaScript:</strong> <code>Array.shift()</code> dan <code>unshift()</code> menggeser seluruh array (O(n)). Solusi JavaScript di atas memakai array melingkar dengan indeks kepala.</p>',
        'hints' => [
            'Perhatikan di ujung mana setiap kejadian terjadi: depan atau belakang?',
            'Ada operasi tambah dan buang di kedua ujung. Struktur apa yang O(1) untuk keempatnya?',
            'deque: DATANG = push_back, VIP = push_front, PERGI = pop_back, LAYANI = front + pop_front. Cek empty() sebelum pop.',
        ],
    ],

    // ───────────────────────── Set, Map & Priority Queue ─────────────────────────
    [
        'slug' => 'kata-favorit',
        'lesson' => 'set-map',
        'title' => 'Kata Favorit',
        'difficulty' => 'Mudah',
        'tags' => ['map', 'sorting'],
        'statement' => '<p>Sebuah kotak saran berisi <strong>N</strong> kata (huruf kecil semua). Panitia ingin tahu kata mana yang paling sering ditulis.</p>
<p>Cetak setiap kata <strong>berbeda</strong> beserta banyak kemunculannya. Urutkan dari yang paling sering muncul; jika banyaknya sama, urutkan menurut abjad.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N kata dipisahkan spasi.</p>',
        'output_format' => '<p>Untuk setiap kata berbeda, satu baris berisi <code>kata banyak</code>, sesuai urutan yang diminta.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>Setiap kata terdiri dari 1–10 huruf kecil</li></ul>',
        'samples' => [
            ['input' => "8\nbakso mie bakso sate mie bakso es es\n", 'explanation' => 'bakso muncul 3 kali. es dan mie sama-sama 2 kali, jadi diurutkan menurut abjad: es lalu mie. sate 1 kali.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $vocab, int $maxLen) {
                $words = [];
                for ($i = 0; $i < $vocab; $i++) {
                    $words[] = T::word(mt_rand(1, $maxLen), 'abcdefghij');
                }
                $out = [];
                for ($i = 0; $i < $n; $i++) {
                    // Distribusi miring: kata awal lebih sering
                    $k = (int) floor(($vocab - 1) * (mt_rand() / mt_getrandmax()) ** 2);
                    $out[] = $words[$k];
                }

                return "$n\n".implode(' ', $out)."\n";
            };

            return [
                "1\nhalo\n", "5\nb a b a c\n", $make(30, 6, 3), $make(2000, 300, 4),
                $make(200000, 5000, 10), $make(200000, 200000, 10), $make(200000, 3, 2),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $cnt = [];
            foreach (preg_split('/\s+/', trim($lines[1])) as $w) {
                $cnt[$w] = ($cnt[$w] ?? 0) + 1;
            }
            $words = array_map('strval', array_keys($cnt));
            $freq = array_values($cnt);
            array_multisort($freq, SORT_DESC, SORT_NUMERIC, $words, SORT_ASC, SORT_STRING);
            $out = [];
            foreach ($words as $i => $w) {
                $out[] = $w.' '.$freq[$i];
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    map<string, int> cnt;
    for (int i = 0; i < n; i++) {
        string w;
        cin >> w;
        // hitung kemunculan w
    }

    // Pindahkan ke vector, urutkan, lalu cetak

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const kata = readLine().trim().split(/\s+/);
const cnt = new Map();
// hitung kemunculan setiap kata, urutkan, lalu cetak
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
kata = input().split()
# hitung kemunculan setiap kata, urutkan, lalu cetak
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    map<string, int> cnt;
    for (int i = 0; i < n; i++) {
        string w;
        cin >> w;
        cnt[w]++;                       // kunci baru otomatis mulai dari 0
    }

    // (banyak, kata): urutkan banyak menurun, kata menaik
    vector<pair<int, string>> v;
    for (auto& kv : cnt) v.push_back(make_pair(kv.second, kv.first));
    sort(v.begin(), v.end(), [](const pair<int, string>& a, const pair<int, string>& b) {
        if (a.first != b.first) return a.first > b.first;
        return a.second < b.second;
    });

    string out;
    for (auto& p : v) out += p.second + " " + to_string(p.first) + "\n";
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const kata = readLine().trim().split(/\s+/);
const cnt = new Map();
for (const w of kata) cnt.set(w, (cnt.get(w) || 0) + 1);
const v = [...cnt.entries()];
v.sort((a, b) => (a[1] !== b[1] ? b[1] - a[1] : a[0] < b[0] ? -1 : a[0] > b[0] ? 1 : 0));
console.log(v.map(([w, c]) => w + " " + c).join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from collections import Counter
input = sys.stdin.readline

n = int(input())
cnt = Counter(input().split())
hasil = sorted(cnt.items(), key=lambda kv: (-kv[1], kv[0]))
print("\n".join(f"{w} {c}" for w, c in hasil))
CODE,
        ],
        'editorial' => '<p>Dua langkah:</p>
<ol><li><strong>Hitung</strong> kemunculan setiap kata dengan <code>map&lt;string, int&gt;</code>: cukup <code>cnt[w]++</code>, karena kunci yang belum ada otomatis dibuat bernilai 0.</li>
<li><strong>Urutkan</strong>. map terurut menurut <em>kunci</em> (abjad), padahal kita butuh urut menurut banyak kemunculan. Pindahkan isinya ke <code>vector&lt;pair&lt;int, string&gt;&gt;</code> lalu urutkan dengan comparator: banyak menurun, kata menaik.</li></ol>
<p>Kompleksitas <code>O(N log N)</code>. <strong>Alternatif:</strong> simpan pasangan <code>(-banyak, kata)</code> lalu <code>sort</code> biasa, tanpa comparator.</p>',
        'hints' => [
            'Langkah pertama: hitung berapa kali setiap kata muncul. Struktur apa yang memetakan string ke bilangan?',
            'map menyimpan kunci terurut abjad, tetapi kita perlu urut menurut banyak kemunculan. Pindahkan ke vector of pair.',
            'Urutkan pair (banyak, kata) dengan comparator: banyak menurun, jika sama kata menaik. Atau simpan (-banyak, kata) dan sort biasa.',
        ],
    ],

    [
        'slug' => 'nilai-terdekat',
        'lesson' => 'set-map',
        'title' => 'Nilai Terdekat',
        'difficulty' => 'Sedang',
        'tags' => ['multiset', 'lower_bound'],
        'statement' => '<p>Kamu mengelola sebuah kantong bilangan yang awalnya kosong. Ada <strong>Q</strong> perintah:</p>
<ul><li><code>+ x</code>: masukkan satu bilangan x ke kantong (boleh ada yang kembar).</li>
<li><code>- x</code>: keluarkan <strong>satu</strong> bilangan x dari kantong. Dijamin x ada di kantong.</li>
<li><code>? x</code>: cetak bilangan di kantong yang <strong>paling dekat</strong> dengan x, yaitu yang selisih mutlaknya terkecil. Jika ada dua yang sama dekat, cetak yang lebih kecil. Jika kantong kosong, cetak <code>-1</code>.</li></ul>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi satu perintah.</p>',
        'output_format' => '<p>Satu baris untuk setiap perintah <code>?</code>.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 200 000</li><li>0 ≤ x ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "9\n? 5\n+ 10\n+ 4\n? 6\n? 7\n+ 7\n- 4\n? 3\n? 100\n", 'explanation' => 'Mula-mula kantong kosong (−1). Isi {4, 10}: 6 paling dekat ke 4 (selisih 2). 7 berselisih 3 dari 4 dan 10, jadi pilih yang lebih kecil: 4. Setelah +7 dan −4 isinya {7, 10}: 3 paling dekat ke 7, dan 100 paling dekat ke 10.'],
        ],
        'tests' => function () {
            $make = function (int $q, int $maxX, array $w) {
                $lines = [(string) $q];
                $bag = [];
                for ($i = 0; $i < $q; $i++) {
                    $r = mt_rand(1, array_sum($w));
                    if ($r <= $w[0] || ($r <= $w[0] + $w[1] && ! $bag)) {
                        $x = mt_rand(0, $maxX);
                        $bag[] = $x;
                        $lines[] = "+ $x";
                    } elseif ($r <= $w[0] + $w[1]) {
                        $k = mt_rand(0, count($bag) - 1);
                        $x = $bag[$k];
                        $bag[$k] = end($bag);
                        array_pop($bag);
                        $lines[] = "- $x";
                    } else {
                        $lines[] = '? '.mt_rand(0, $maxX);
                    }
                }

                return implode("\n", $lines)."\n";
            };

            return [
                "4\n? 0\n+ 5\n? 0\n? 1000000000\n", "5\n+ 2\n+ 6\n? 4\n- 2\n? 4\n",
                $make(30, 20, [3, 1, 3]), $make(2000, 1000, [4, 2, 4]),
                $make(200000, 1000000000, [3, 1, 3]), $make(200000, 50, [3, 2, 4]), $make(200000, 1000000000, [1, 1, 1]),
            ];
        },
        'solve' => function (string $input) {
            // Referensi offline: Fenwick tree atas nilai yang dikompres
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $ops = [];
            $xs = [];
            for ($i = 1; $i <= $q; $i++) {
                [$op, $x] = explode(' ', trim($lines[$i]));
                $ops[] = $op;
                $xs[] = (int) $x;
            }
            $vals = array_values(array_unique($xs));
            sort($vals);
            $id = array_flip($vals);
            $m = count($vals);
            $bit = array_fill(0, $m + 1, 0);
            $log = 1;
            while ((1 << $log) <= $m) {
                $log++;
            }
            $total = 0;
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                $x = $xs[$i];
                if ($ops[$i] === '+' || $ops[$i] === '-') {
                    $d = $ops[$i] === '+' ? 1 : -1;
                    $total += $d;
                    for ($j = $id[$x] + 1; $j <= $m; $j += $j & -$j) {
                        $bit[$j] += $d;
                    }
                    continue;
                }
                if ($total === 0) {
                    $out[] = -1;
                    continue;
                }
                $c = 0;
                for ($j = $id[$x]; $j > 0; $j -= $j & -$j) {
                    $c += $bit[$j];
                }
                $kth = function (int $k) use (&$bit, $m, $log, $vals) {
                    $pos = 0;
                    for ($b = $log; $b >= 0; $b--) {
                        $np = $pos + (1 << $b);
                        if ($np <= $m && $bit[$np] < $k) {
                            $pos = $np;
                            $k -= $bit[$np];
                        }
                    }

                    return $vals[$pos];
                };
                $best = $c > 0 ? $kth($c) : -1;
                if ($c < $total) {
                    $up = $kth($c + 1);
                    if ($best === -1 || $up - $x < $x - $best) {
                        $best = $up;
                    }
                }
                $out[] = $best;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    multiset<long long> kantong;
    while (q--) {
        char op;
        long long x;
        cin >> op >> x;
        // Tangani +, -, dan ?

    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [op, xs] = readLine().split(" ");
  const x = Number(xs);
  // Tangani +, -, dan ?
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    op, x = input().split()
    x = int(x)
    # Tangani +, -, dan ?

print("\n".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    multiset<long long> kantong;
    string out;
    while (q--) {
        char op;
        long long x;
        cin >> op >> x;
        if (op == '+') {
            kantong.insert(x);
        } else if (op == '-') {
            kantong.erase(kantong.find(x));       // hapus SATU salinan
        } else {
            if (kantong.empty()) {
                out += "-1\n";
                continue;
            }
            auto it = kantong.lower_bound(x);     // pertama yang >= x
            long long best = -1;
            if (it != kantong.begin()) best = *prev(it);           // terbesar yang < x
            if (it != kantong.end() && (best == -1 || *it - x < x - best))
                best = *it;                                        // seri → tetap yang kecil
            out += to_string(best) + "\n";
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// JavaScript tidak punya multiset bawaan. Karena semua perintah diketahui di awal,
// kumpulkan semua nilai, kompres, lalu pakai Fenwick tree untuk "cari ke-k" (offline).
const q = Number(readLine());
const ops = [], xs = [];
for (let i = 0; i < q; i++) {
  const [op, s] = readLine().split(" ");
  ops.push(op);
  xs.push(Number(s));
}
const vals = [...new Set(xs)].sort((a, b) => a - b);
const id = new Map(vals.map((v, i) => [v, i + 1]));
const m = vals.length;
const bit = new Int32Array(m + 1);
let total = 0;
const add = (i, d) => { for (; i <= m; i += i & -i) bit[i] += d; };
const pref = (i) => { let s = 0; for (; i > 0; i -= i & -i) s += bit[i]; return s; };
let LOG = 1;
while ((1 << LOG) <= m) LOG++;
const kth = (k) => {            // posisi terkecil dengan prefix >= k
  let pos = 0;
  for (let b = LOG; b >= 0; b--) {
    const np = pos + (1 << b);
    if (np <= m && bit[np] < k) { pos = np; k -= bit[np]; }
  }
  return pos + 1;
};
const out = [];
for (let i = 0; i < q; i++) {
  const x = xs[i];
  if (ops[i] === "+") { add(id.get(x), 1); total++; }
  else if (ops[i] === "-") { add(id.get(x), -1); total--; }
  else {
    if (total === 0) { out.push(-1); continue; }
    const c = pref(id.get(x) - 1);          // banyak nilai < x
    let best = -1;
    if (c > 0) best = vals[kth(c) - 1];     // terbesar yang < x
    if (c < total) {
      const up = vals[kth(c + 1) - 1];      // terkecil yang >= x
      if (best === -1 || up - x < x - best) best = up;
    }
    out.push(best);
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
# Python tidak punya multiset bawaan: pakai Fenwick tree atas nilai yang dikompres (offline).
import sys
data = sys.stdin.buffer.read().split()
q = int(data[0])
ops = data[1::2][:q]
xs = list(map(int, data[2::2][:q]))
vals = sorted(set(xs))
idx = {v: i + 1 for i, v in enumerate(vals)}
m = len(vals)
bit = [0] * (m + 1)
LOG = m.bit_length()

def add(i, d):
    while i <= m:
        bit[i] += d
        i += i & -i

def pref(i):
    s = 0
    while i > 0:
        s += bit[i]
        i -= i & -i
    return s

def kth(k):
    pos = 0
    for b in range(LOG, -1, -1):
        np = pos + (1 << b)
        if np <= m and bit[np] < k:
            pos = np
            k -= bit[np]
    return pos + 1

total = 0
out = []
for op, x in zip(ops, xs):
    if op == b'+':
        add(idx[x], 1); total += 1
    elif op == b'-':
        add(idx[x], -1); total -= 1
    else:
        if total == 0:
            out.append(-1); continue
        c = pref(idx[x] - 1)
        best = -1
        if c > 0:
            best = vals[kth(c) - 1]
        if c < total:
            up = vals[kth(c + 1) - 1]
            if best == -1 or up - x < x - best:
                best = up
        out.append(best)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Isi kantong terus berubah, jadi menyimpan array terurut lalu mengurutkan ulang setiap kali terlalu lambat. <code>multiset</code> memberi ketiganya dalam O(log Q): menambah, menghapus satu salinan, dan mencari batas.</p>
<p>Untuk perintah <code>? x</code>, kandidat jawaban hanya dua:</p>
<ul><li><code>it = lower_bound(x)</code>: bilangan terkecil yang ≥ x;</li><li><code>prev(it)</code>: bilangan terbesar yang &lt; x (jika <code>it</code> bukan <code>begin()</code>).</li></ul>
<p>Bilangan lain pasti lebih jauh. Bandingkan selisihnya; jika sama dekat, ambil yang lebih kecil, yaitu <code>prev(it)</code>.</p>
<p><strong>Jebakan:</strong> perintah <code>- x</code> wajib memakai <code>erase(find(x))</code>. <code>erase(x)</code> menghapus <em>semua</em> salinan x.</p>
<p><em>Catatan:</em> JavaScript dan Python tidak punya multiset bawaan, jadi solusinya memakai Fenwick tree pada nilai yang dikompres (materi Struktur Data).</p>',
        'hints' => [
            'Data berubah terus dan kita butuh "nilai terdekat". Struktur apa yang tetap terurut setelah insert dan erase?',
            'Pada multiset, kandidat terdekat hanya dua: lower_bound(x) dan elemen tepat sebelumnya.',
            'Hapus satu salinan dengan erase(find(x)). Untuk seri, pilih yang lebih kecil (elemen sebelum lower_bound).',
        ],
    ],

    [
        'slug' => 'jumlah-k-terbesar',
        'lesson' => 'set-map',
        'title' => 'Jumlah K Terbesar',
        'difficulty' => 'Sedang',
        'tags' => ['priority_queue', 'heap'],
        'statement' => '<p>Sebuah aplikasi mencatat skor pemain satu per satu: <code>a<sub>1</sub>, a<sub>2</sub>, …, a<sub>N</sub></code>. Setelah setiap skor masuk, papan juara menampilkan <strong>jumlah K skor tertinggi</strong> yang sudah tercatat sejauh ini. Jika skor yang tercatat belum sampai K, jumlahkan semuanya.</p>
<p>Cetak angka yang ditampilkan papan juara setelah setiap skor.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris berisi N bilangan, dipisahkan spasi.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6 3\n5 1 8 3 9 2\n", 'explanation' => 'Setelah 3 skor pertama: 5 + 1 + 8 = 14. Skor 3 masuk: tiga tertinggi 8, 5, 3 → 16. Skor 9 masuk: 9, 8, 5 → 22. Skor 2 tidak mengubah tiga tertinggi.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $hi) {
                return "$n $k\n".T::join(T::arr($n, 0, $hi))."\n";
            };
            $inc = function (int $n, int $k) {
                return "$n $k\n".T::join(range(1, $n))."\n";
            };

            return [
                "1 1\n7\n", $make(10, 1, 10), $make(10, 10, 10), $make(1000, 37, 1000000000),
                $make(200000, 1000, 1000000000), $make(200000, 1, 1000000000), $make(200000, 200000, 1000000000),
                $inc(200000, 50000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $h = new SplMinHeap;
            $sum = 0;
            $out = [];
            foreach (T::ints($lines[1]) as $x) {
                $h->insert($x);
                $sum += $x;
                if ($h->count() > $k) {
                    $sum -= $h->extract();
                }
                $out[] = $sum;
            }

            return implode(' ', $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    // Simpan K skor tertinggi sejauh ini. Struktur apa yang cepat membuang yang TERKECIL?

    for (int i = 0; i < n; i++) {
        long long a;
        cin >> a;
        // ...
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const a = readInts();
const out = [];
// Simpan K skor tertinggi sejauh ini (perlu min-heap buatan sendiri)

console.log(out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
import heapq
input = sys.stdin.readline

n, k = map(int, input().split())
a = list(map(int, input().split()))
out = []
# Simpan K skor tertinggi sejauh ini. heapq adalah min-heap.

print(" ".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    // Min-heap berisi K skor tertinggi: top() = yang paling lemah di antara mereka
    priority_queue<long long, vector<long long>, greater<long long>> h;
    long long jumlah = 0;
    for (int i = 0; i < n; i++) {
        long long a;
        cin >> a;
        h.push(a);
        jumlah += a;
        if ((int)h.size() > k) {        // kelebihan satu: buang yang terkecil
            jumlah -= h.top();
            h.pop();
        }
        cout << jumlah << " \n"[i + 1 == n];
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const a = readInts();
// Min-heap sederhana di atas array
const h = [];
const push = (x) => {
  h.push(x);
  let i = h.length - 1;
  while (i > 0) {
    const p = (i - 1) >> 1;
    if (h[p] <= h[i]) break;
    [h[p], h[i]] = [h[i], h[p]];
    i = p;
  }
};
const pop = () => {
  const top = h[0];
  const last = h.pop();
  if (h.length) {
    h[0] = last;
    let i = 0;
    for (;;) {
      const l = 2 * i + 1, r = l + 1;
      let b = i;
      if (l < h.length && h[l] < h[b]) b = l;
      if (r < h.length && h[r] < h[b]) b = r;
      if (b === i) break;
      [h[b], h[i]] = [h[i], h[b]];
      i = b;
    }
  }
  return top;
};
let jumlah = 0;
const out = [];
for (const x of a) {
  push(x);
  jumlah += x;
  if (h.length > k) jumlah -= pop();
  out.push(jumlah);
}
console.log(out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
import heapq
input = sys.stdin.readline

n, k = map(int, input().split())
a = list(map(int, input().split()))
h = []          # min-heap berisi K skor tertinggi
jumlah = 0
out = []
for x in a:
    heapq.heappush(h, x)
    jumlah += x
    if len(h) > k:
        jumlah -= heapq.heappop(h)   # buang yang terkecil
    out.append(jumlah)
print(" ".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Mengurutkan ulang semua skor setiap kali butuh <code>O(N² log N)</code>. Kita perlu cara yang lebih cerdas.</p>
<p>Simpan hanya <strong>K skor tertinggi</strong> sejauh ini, beserta jumlahnya. Saat skor baru datang, masukkan ke kumpulan itu. Jika ukurannya menjadi K + 1, buang skor <strong>terkecil</strong> di kumpulan: ia pasti tidak termasuk K tertinggi.</p>
<p>Struktur yang cepat memberi dan membuang yang terkecil adalah <strong>min-heap</strong>: <code>priority_queue&lt;long long, vector&lt;long long&gt;, greater&lt;long long&gt;&gt;</code>. Kedengarannya terbalik (K <em>terbesar</em> disimpan di min-heap), tetapi justru itulah kuncinya: puncaknya adalah "anggota terlemah" yang siap ditendang.</p>
<p>Setiap skor: satu push dan paling banyak satu pop, <code>O(log K)</code>. Total <code>O(N log K)</code>. Jumlahnya bisa mencapai 2 · 10<sup>14</sup>, jadi pakai <code>long long</code>.</p>',
        'hints' => [
            'Kamu tidak perlu mengingat semua skor, cukup K yang tertinggi.',
            'Saat ada K + 1 kandidat, yang dibuang adalah yang terkecil. Struktur apa yang cepat memberi yang terkecil?',
            'Min-heap berukuran K: push skor baru, jika ukurannya > K buang top(). Jaga jumlahnya sambil jalan.',
        ],
    ],

    // ───────────────────────── Algoritma STL ─────────────────────────
    [
        'slug' => 'hitung-rentang',
        'lesson' => 'stl-algoritma',
        'title' => 'Berapa di Rentang?',
        'difficulty' => 'Mudah',
        'tags' => ['lower_bound', 'upper_bound', 'sorting'],
        'statement' => '<p>Petugas BMKG mencatat <strong>N</strong> suhu harian <code>a<sub>1</sub>, …, a<sub>N</sub></code> (boleh negatif). Ia punya <strong>Q</strong> pertanyaan: ada berapa hari yang suhunya berada di rentang <code>[l, r]</code> (termasuk l dan r)?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi N bilangan <code>a<sub>i</sub></code>. Q baris berikutnya masing-masing berisi <code>l r</code>.</p>',
        'output_format' => '<p>Q baris, jawaban setiap pertanyaan.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>−10<sup>9</sup> ≤ l ≤ r ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "7 4\n31 25 28 25 33 22 28\n25 28\n30 40\n26 27\n-5 100\n", 'explanation' => 'Suhu urut: 22 25 25 28 28 31 33. Rentang [25, 28] berisi 25, 25, 28, 28 (4 hari). [30, 40] berisi 31 dan 33. Tidak ada suhu di [26, 27]. Semua 7 hari ada di [−5, 100].'],
        ],
        'tests' => function () {
            $make = function (int $n, int $q, int $lo, int $hi) {
                $out = ["$n $q", T::join(T::arr($n, $lo, $hi))];
                for ($i = 0; $i < $q; $i++) {
                    $l = mt_rand($lo - 3, $hi + 3);
                    $r = mt_rand($l, min($hi + 3, $l + mt_rand(0, max(1, intdiv($hi - $lo, 3)))));
                    $out[] = "$l $r";
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 2\n5\n5 5\n6 7\n", $make(10, 10, -5, 5), $make(1000, 1000, -100, 100),
                $make(200000, 200000, -1000000000, 1000000000), $make(200000, 200000, 0, 50), $make(200000, 200000, 7, 7),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            sort($a);
            $bound = function (int $x, bool $upper) use ($a, $n) {
                $lo = 0;
                $hi = $n;
                while ($lo < $hi) {
                    $m = ($lo + $hi) >> 1;
                    if ($upper ? $a[$m] <= $x : $a[$m] < $x) {
                        $lo = $m + 1;
                    } else {
                        $hi = $m;
                    }
                }

                return $lo;
            };
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$l, $r] = T::ints($lines[2 + $i]);
                $out[] = $bound($r, true) - $bound($l, false);
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    while (q--) {
        long long l, r;
        cin >> l >> r;
        // banyak a[i] dengan l <= a[i] <= r
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  // banyak a[i] dengan l <= a[i] <= r
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
a = list(map(int, input().split()))
out = []
for _ in range(q):
    l, r = map(int, input().split())
    # banyak a[i] dengan l <= a[i] <= r

print("\n".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;
    sort(a.begin(), a.end());                    // syarat lower/upper_bound

    string out;
    while (q--) {
        long long l, r;
        cin >> l >> r;
        // (banyak yang <= r) - (banyak yang < l)
        long long c = (upper_bound(a.begin(), a.end(), r) - a.begin())
                    - (lower_bound(a.begin(), a.end(), l) - a.begin());
        out += to_string(c) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = Float64Array.from(readInts()).sort();
// pertama yang >= x (lower) atau > x (upper)
const bound = (x, upper) => {
  let lo = 0, hi = n;
  while (lo < hi) {
    const m = (lo + hi) >> 1;
    if (upper ? a[m] <= x : a[m] < x) lo = m + 1;
    else hi = m;
  }
  return lo;
};
const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  out.push(bound(r, true) - bound(l, false));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_left, bisect_right
input = sys.stdin.readline

n, q = map(int, input().split())
a = sorted(map(int, input().split()))
out = []
for _ in range(q):
    l, r = map(int, input().split())
    out.append(bisect_right(a, r) - bisect_left(a, l))
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Menghitung dengan loop untuk setiap pertanyaan butuh O(N · Q) = 4 · 10<sup>10</sup>.</p>
<p>Urutkan suhu sekali. Pada array terurut:</p>
<ul><li><code>upper_bound(r) − begin</code> = banyak nilai yang <strong>≤ r</strong>;</li><li><code>lower_bound(l) − begin</code> = banyak nilai yang <strong>&lt; l</strong>.</li></ul>
<p>Selisih keduanya adalah banyak nilai di <code>[l, r]</code>. Setiap pertanyaan O(log N), total <code>O((N + Q) log N)</code>.</p>
<p><strong>Jebakan JavaScript:</strong> <code>array.sort()</code> tanpa comparator mengurutkan sebagai <em>string</em> ("10" &lt; "9"). Pakai <code>sort((x, y) =&gt; x − y)</code> atau typed array.</p>',
        'hints' => [
            'Jika suhu sudah terurut, hari-hari dengan suhu di [l, r] membentuk satu blok berurutan.',
            'Ujung kiri blok itu ditemukan lower_bound(l), ujung kanannya upper_bound(r).',
            'Jawaban = (upper_bound(r) − begin) − (lower_bound(l) − begin). Jangan lupa sort sekali di awal.',
        ],
    ],

    [
        'slug' => 'urutan-berikutnya',
        'lesson' => 'stl-algoritma',
        'title' => 'Susunan Berikutnya',
        'difficulty' => 'Sedang',
        'tags' => ['next_permutation', 'greedy'],
        'statement' => '<p>Diberikan barisan <code>a<sub>1</sub>, …, a<sub>N</sub></code> (nilainya boleh kembar). Bayangkan semua susunan ulang barisan ini (tanpa susunan yang sama dihitung dua kali) diurutkan seperti kamus: dibandingkan elemen pertama dulu, jika sama elemen kedua, dan seterusnya.</p>
<p>Cetak susunan yang berada <strong>tepat setelah</strong> a dalam urutan itu. Jika a sudah susunan terakhir, cetak <code>TERAKHIR</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris: susunan berikutnya dipisahkan spasi, atau <code>TERAKHIR</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1 3 5 4 2\n", 'explanation' => 'Ekor 5 4 2 sudah menurun. Angka 3 harus naik menjadi 4 (terkecil di ekor yang lebih besar dari 3), lalu sisanya diurutkan menaik: 1 4 2 3 5.'],
            ['input' => "4\n3 3 2 1\n", 'explanation' => 'Seluruh barisan menurun (tidak naik di mana pun), jadi ini susunan terakhir.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi, string $shape = 'acak') {
                $a = T::arr($n, 1, $hi);
                if ($shape === 'turun') {
                    rsort($a);
                } elseif ($shape === 'ekor') {
                    // awalan acak + ekor panjang yang menurun
                    $k = intdiv($n, 3);
                    $tail = array_slice($a, $k);
                    rsort($tail);
                    $a = array_merge(array_slice($a, 0, $k), $tail);
                } elseif ($shape === 'naik') {
                    sort($a);
                }

                return "$n\n".T::join($a)."\n";
            };

            return [
                "1\n7\n", "2\n1 2\n", "2\n2 1\n", "3\n1 1 1\n", "4\n1 2 2 1\n", $make(8, 3), $make(1000, 5, 'ekor'),
                $make(200000, 1000000000), $make(200000, 1000000000, 'turun'), $make(200000, 3, 'ekor'), $make(200000, 1000000000, 'naik'),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $n = count($a);
            $i = $n - 2;
            while ($i >= 0 && $a[$i] >= $a[$i + 1]) {
                $i--;
            }
            if ($i < 0) {
                return 'TERAKHIR';
            }
            $j = $n - 1;
            while ($a[$j] <= $a[$i]) {
                $j--;
            }
            [$a[$i], $a[$j]] = [$a[$j], $a[$i]];
            $tail = array_reverse(array_slice($a, $i + 1));

            return T::join(array_merge(array_slice($a, 0, $i + 1), $tail));
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // Cari susunan berikutnya

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();

// Cari susunan berikutnya (tulis langkah-langkah next_permutation sendiri)
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))

# Cari susunan berikutnya (tulis langkah-langkah next_permutation sendiri)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // next_permutation sudah menangani nilai kembar dan mengembalikan false
    // jika a adalah susunan terakhir.
    if (!next_permutation(a.begin(), a.end())) {
        cout << "TERAKHIR\n";
        return 0;
    }
    string out;
    for (int i = 0; i < n; i++) {
        out += to_string(a[i]);
        out += (i + 1 == n ? '\n' : ' ');
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
let i = n - 2;
while (i >= 0 && a[i] >= a[i + 1]) i--;      // ekor menurun terpanjang
if (i < 0) {
  console.log("TERAKHIR");
} else {
  let j = n - 1;
  while (a[j] <= a[i]) j--;                  // terkecil di ekor yang > a[i]
  [a[i], a[j]] = [a[j], a[i]];
  for (let l = i + 1, r = n - 1; l < r; l++, r--) [a[l], a[r]] = [a[r], a[l]];
  console.log(a.join(" "));
}
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
i = n - 2
while i >= 0 and a[i] >= a[i + 1]:      # ekor menurun terpanjang
    i -= 1
if i < 0:
    print("TERAKHIR")
else:
    j = n - 1
    while a[j] <= a[i]:                 # terkecil di ekor yang > a[i]
        j -= 1
    a[i], a[j] = a[j], a[i]
    a[i + 1:] = reversed(a[i + 1:])
    print(" ".join(map(str, a)))
CODE,
        ],
        'editorial' => '<p>Di C++ soal ini satu baris: <code>next_permutation</code> mengubah array menjadi susunan berikutnya dan mengembalikan <code>false</code> jika tidak ada. Tetapi pahami cara kerjanya, karena bahasa lain tidak punya fungsi ini dan idenya sering muncul di soal lain:</p>
<ol><li>Dari kanan, cari ekor terpanjang yang <strong>tidak naik</strong>. Ekor seperti itu sudah susunan terbesar untuk isinya. Jika ekornya seluruh array, ini susunan terakhir.</li>
<li>Elemen tepat sebelum ekor, <code>a[i]</code>, harus naik. Tukar dengan elemen ekor <strong>terkecil yang lebih besar</strong> dari <code>a[i]</code> (cari dari kanan).</li>
<li>Balik ekornya agar menjadi menaik, yaitu susunan terkecil.</li></ol>
<p>Semua langkah O(N). Perbandingan <code>≥</code> dan <code>≤</code> (bukan &gt; dan &lt;) membuat nilai kembar ditangani dengan benar: susunan yang sama tidak dihasilkan dua kali.</p>',
        'hints' => [
            'Perhatikan bagian kanan barisan. Kapan bagian kanan sudah tidak bisa "dinaikkan" lagi?',
            'Cari dari kanan indeks i terakhir dengan a[i] < a[i+1]. Jika tidak ada, jawabannya TERAKHIR.',
            'Tukar a[i] dengan elemen paling kanan yang > a[i], lalu balik bagian a[i+1..n-1]. Di C++ cukup next_permutation.',
        ],
    ],

    // ───────────────────────── Optimasi Bitset ─────────────────────────
    [
        'slug' => 'uang-pas',
        'lesson' => 'bitset',
        'title' => 'Bisa Dibayar Pas?',
        'difficulty' => 'Sedang',
        'tags' => ['bitset', 'subset sum', 'dp'],
        'statement' => '<p>Dompet Raka berisi <strong>N</strong> uang logam dengan nilai <code>a<sub>1</sub>, …, a<sub>N</sub></code>. Setiap logam hanya bisa dipakai <strong>sekali</strong>.</p>
<p>Raka akan berbelanja di <strong>Q</strong> toko. Di toko ke-j harga barangnya <code>s<sub>j</sub></code> dan kasir tidak punya kembalian. Untuk setiap toko, apakah Raka bisa membayar <strong>pas</strong> dengan memilih sebagian logamnya? (Setiap toko dipertimbangkan terpisah: logam selalu kembali utuh.)</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Baris ketiga berisi <code>s<sub>1</sub> … s<sub>Q</sub></code>.</p>',
        'output_format' => '<p>Q baris, masing-masing <code>YA</code> atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1000</li><li>1 ≤ a<sub>i</sub> ≤ 1000</li><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ s<sub>j</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4 5\n2 3 7 10\n5 6 12 22 4\n", 'explanation' => '5 = 2 + 3, 12 = 2 + 10, 22 = 2 + 3 + 7 + 10. Harga 6 dan 4 tidak bisa dibentuk dari logam yang ada.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi, int $q, int $smax) {
                return "$n $q\n".T::join(T::arr($n, 1, $hi))."\n".T::join(T::arr($q, 1, $smax))."\n";
            };
            $even = function (int $n, int $q) {
                // Semua logam genap: jumlah ganjil mustahil
                $a = array_map(fn ($x) => 2 * $x, T::arr($n, 1, 500));

                return "$n $q\n".T::join($a)."\n".T::join(T::arr($q, 1, 1000000))."\n";
            };

            return [
                "1 3\n5\n5 1 10\n", $make(10, 20, 30, 120), $make(100, 1000, 1000, 60000),
                $make(1000, 1000, 200000, 1000000), $make(1000, 3, 200000, 3000), $even(1000, 200000), $make(30, 1000, 200000, 20000),
            ];
        },
        'solve' => function (string $input) {
            // Bitset manual: kata 62-bit di array PHP
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $qs = T::ints($lines[2]);
            $S = min(array_sum($a), 1000000);
            $B = 62;
            $mask = (1 << $B) - 1;
            $W = intdiv($S, $B) + 1;
            $dp = array_fill(0, $W, 0);
            $dp[0] = 1;
            foreach ($a as $x) {
                $ws = intdiv($x, $B);
                $bs = $x % $B;
                for ($i = $W - 1; $i >= $ws; $i--) {
                    $v = ($dp[$i - $ws] << $bs) & $mask;
                    if ($bs && $i - $ws - 1 >= 0) {
                        $v |= $dp[$i - $ws - 1] >> ($B - $bs);
                    }
                    $dp[$i] |= $v;
                }
            }
            $out = [];
            foreach ($qs as $s) {
                $out[] = ($s <= $S && (($dp[intdiv($s, $B)] >> ($s % $B)) & 1)) ? 'YA' : 'TIDAK';
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAXS = 1000000;
bitset<MAXS + 1> dp;      // global: terlalu besar untuk stack

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    // dp[s] = 1 jika s bisa dibentuk

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
const s = readInts();
// Petunjuk: BigInt bisa dipakai sebagai bitset (dp |= dp << BigInt(x))

CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
a = list(map(int, input().split()))
s = list(map(int, input().split()))
# Petunjuk: bilangan bulat Python bisa dipakai sebagai bitset

CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAXS = 1000000;
bitset<MAXS + 1> dp;      // global: terlalu besar untuk stack

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    dp[0] = 1;                      // jumlah 0: tidak memakai logam apa pun
    for (int i = 0; i < n; i++) {
        int a;
        cin >> a;
        dp |= dp << a;              // ambil / tidak ambil logam a, untuk semua jumlah sekaligus
    }
    string out;
    for (int j = 0; j < q; j++) {
        int s;
        cin >> s;
        out += dp[s] ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
const s = readInts();
let dp = 1n;                                   // BigInt sebagai bitset
for (const x of a) dp |= dp << BigInt(x);
const bits = dp.toString(2);                   // karakter terakhir = bit 0
const L = bits.length;
const out = s.map((v) => (v < L && bits[L - 1 - v] === "1" ? "YA" : "TIDAK"));
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
a = list(map(int, input().split()))
s = list(map(int, input().split()))
dp = 1                          # bit ke-k menyala = jumlah k bisa
for x in a:
    dp |= dp << x
bits = bin(dp)[2:][::-1]        # bits[k] = bit ke-k
L = len(bits)
print("\n".join("YA" if v < L and bits[v] == "1" else "TIDAK" for v in s))
CODE,
        ],
        'editorial' => '<p>Ini subset sum: <code>dp[s]</code> = apakah jumlah s bisa dibentuk. DP biasa memproses setiap logam dengan loop mundur atas semua jumlah: <code>O(N · S)</code> = 1000 · 10<sup>6</sup> = 10<sup>9</sup> operasi. Terlalu lambat.</p>
<p>Isi tabelnya hanya benar/salah, jadi simpan dalam <code>bitset</code>. Transisi untuk logam a menjadi satu baris:</p>
<p style="text-align:center"><code>dp |= dp &lt;&lt; a;</code></p>
<p>Geser kiri sejauh a berarti "tambahkan a ke setiap jumlah yang sudah bisa", dan OR menggabungkannya dengan "tidak memakai a". Biayanya O(S / 64) per logam, total sekitar 1,6 · 10<sup>7</sup> operasi kata. Setelah itu setiap pertanyaan dijawab O(1) dengan <code>dp[s]</code>.</p>
<p><strong>Penting:</strong> bitset sebesar 10<sup>6</sup> bit (125 KB) sebaiknya global. Di Python dan JavaScript, bilangan bulat besar (<code>int</code>/<code>BigInt</code>) berperilaku seperti bitset.</p>',
        'hints' => [
            'Pertanyaannya ya/tidak dan tidak bergantung urutan toko: hitung sekali semua jumlah yang bisa dibentuk.',
            'Subset sum biasa O(N · S) terlalu lambat. Isinya hanya boolean: bisakah disimpan sebagai bit?',
            'bitset<1000001> dp; dp[0] = 1; untuk setiap logam a: dp |= dp << a. Jawaban toko j adalah dp[s_j].',
        ],
    ],

    [
        'slug' => 'teman-bersama',
        'lesson' => 'bitset',
        'title' => 'Segitiga Pertemanan',
        'difficulty' => 'Sulit',
        'tags' => ['bitset', 'graph', 'popcount'],
        'statement' => '<p>Sebuah jejaring sosial punya <strong>N</strong> pengguna dan <strong>M</strong> pasang pertemanan (pertemanan berlaku dua arah). Tiga pengguna disebut <strong>segitiga pertemanan</strong> jika ketiganya saling berteman.</p>
<p>Hitung banyaknya segitiga pertemanan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya masing-masing berisi <code>u v</code>: pengguna u dan v berteman. Tidak ada pasangan yang muncul dua kali dan u ≠ v.</p>',
        'output_format' => '<p>Satu bilangan: banyak segitiga pertemanan.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 2000</li><li>0 ≤ M ≤ 200 000</li><li>1 ≤ u, v ≤ N</li></ul>',
        'samples' => [
            ['input' => "5 7\n1 2\n2 3\n1 3\n3 4\n2 4\n4 5\n1 5\n", 'explanation' => 'Segitiganya {1, 2, 3} dan {2, 3, 4}. Pengguna 5 berteman dengan 1 dan 4, tetapi 1 dan 4 tidak berteman.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $m) {
                $e = T::edges($n, $m);

                return T::graphInput("$n ".count($e), $e);
            };
            $complete = function (int $n) {
                $e = [];
                for ($i = 1; $i <= $n; $i++) {
                    for ($j = $i + 1; $j <= $n; $j++) {
                        $e[] = [$i, $j];
                    }
                }

                return T::graphInput("$n ".count($e), $e);
            };

            return [
                "1 0\n", "3 3\n1 2\n2 3\n3 1\n", $make(10, 20), $complete(30), $make(300, 8000),
                $make(2000, 200000), $make(2000, 60000), $complete(632),
            ];
        },
        'solve' => function (string $input) {
            // Referensi: orientasi sisi dari derajat kecil ke besar, lalu tandai tetangga keluar
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $deg = array_fill(0, $n + 1, 0);
            $edges = [];
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $edges[] = [$u, $v];
                $deg[$u]++;
                $deg[$v]++;
            }
            $out = array_fill(0, $n + 1, []);
            foreach ($edges as [$u, $v]) {
                if ($deg[$u] < $deg[$v] || ($deg[$u] === $deg[$v] && $u < $v)) {
                    $out[$u][] = $v;
                } else {
                    $out[$v][] = $u;
                }
            }
            $mark = array_fill(0, $n + 1, 0);
            $cnt = 0;
            for ($u = 1; $u <= $n; $u++) {
                foreach ($out[$u] as $v) {
                    $mark[$v] = $u;
                }
                foreach ($out[$u] as $v) {
                    foreach ($out[$v] as $w) {
                        if ($mark[$w] === $u) {
                            $cnt++;
                        }
                    }
                }
            }

            return (string) $cnt;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAXN = 2000;
bitset<MAXN> adj[MAXN];   // adj[u][v] = 1 jika u dan v berteman (global!)

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<pair<int, int>> sisi(m);
    for (auto& e : sisi) {
        cin >> e.first >> e.second;
        e.first--;
        e.second--;
        // isi adj
    }

    // Hitung segitiga

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const sisi = [];
for (let i = 0; i < m; i++) sisi.push(readInts());
// Simpan tetangga setiap pengguna sebagai deretan bit (misalnya Uint32Array)

CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
sisi = [tuple(map(int, input().split())) for _ in range(m)]
# Bilangan bulat Python bisa menjadi bitset tetangga

CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAXN = 2000;
bitset<MAXN> adj[MAXN];   // adj[u][v] = 1 jika u dan v berteman (global!)

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<pair<int, int>> sisi(m);
    for (auto& e : sisi) {
        cin >> e.first >> e.second;
        e.first--;
        e.second--;
        adj[e.first][e.second] = 1;
        adj[e.second][e.first] = 1;
    }

    long long total = 0;
    for (auto& e : sisi)                                  // teman bersama u dan v
        total += (adj[e.first] & adj[e.second]).count();  // O(N / 64)
    cout << total / 3 << '\n';                            // setiap segitiga terhitung 3 kali
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const W = (n + 31) >>> 5;                       // banyak kata 32-bit per pengguna
const adj = new Uint32Array((n + 1) * W);
const sisi = [];
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  sisi.push([u, v]);
  adj[u * W + (v >>> 5)] |= 1 << (v & 31);
  adj[v * W + (u >>> 5)] |= 1 << (u & 31);
}
const popcount = (x) => {
  x -= (x >>> 1) & 0x55555555;
  x = (x & 0x33333333) + ((x >>> 2) & 0x33333333);
  return (((x + (x >>> 4)) & 0x0f0f0f0f) * 0x01010101) >>> 24;
};
let total = 0;
for (const [u, v] of sisi) {
  const a = u * W, b = v * W;
  for (let k = 0; k < W; k++) total += popcount(adj[a + k] & adj[b + k]);
}
console.log(String(total / 3));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
n, m = int(data[0]), int(data[1])
adj = [0] * (n + 1)              # adj[u] = bitmask tetangga u
sisi = []
for i in range(m):
    u, v = int(data[2 + 2*i]), int(data[3 + 2*i])
    sisi.append((u, v))
    adj[u] |= 1 << v
    adj[v] |= 1 << u
total = 0
for u, v in sisi:
    total += (adj[u] & adj[v]).bit_count()
print(total // 3)
CODE,
        ],
        'editorial' => '<p>Segitiga {u, v, w} memuat sisi (u, v), dan w adalah <strong>teman bersama</strong> u dan v. Jadi:</p>
<p style="text-align:center">banyak segitiga = (Σ<sub>sisi (u, v)</sub> |teman(u) ∩ teman(v)|) / 3</p>
<p>Dibagi 3 karena setiap segitiga punya tiga sisi dan terhitung sekali dari masing-masing sisi.</p>
<p>Menghitung irisan dengan loop atas semua w butuh O(N) per sisi, total O(M · N) = 4 · 10<sup>8</sup>: mepet. Simpan daftar teman setiap pengguna sebagai <code>bitset&lt;2000&gt;</code>. Irisannya cukup <code>adj[u] &amp; adj[v]</code> dan banyaknya <code>.count()</code>, keduanya O(N / 64). Total sekitar 6 · 10<sup>6</sup> operasi kata.</p>
<p>Array 2000 bitset × 2000 bit = 500 KB: deklarasikan global agar tidak memenuhi stack.</p>',
        'hints' => [
            'Setiap segitiga memuat sisi (u, v) dan satu pengguna w yang berteman dengan keduanya.',
            'Untuk setiap sisi, hitung banyak teman bersama u dan v. Setiap segitiga akan terhitung tepat 3 kali.',
            'Simpan teman setiap pengguna sebagai bitset; teman bersama = (adj[u] & adj[v]).count(). Bagi total dengan 3.',
        ],
    ],
];
