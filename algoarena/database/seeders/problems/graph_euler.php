<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Jalur & Sirkuit Euler.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$readGraphCpp = "    int n, m;\n    cin >> n >> m;\n    vector<pair<int, int>> sisi(m);\n    for (auto& e : sisi) cin >> e.first >> e.second;";
$readGraphJs = "const [n, m] = readInts();\nconst sisi = [];\nfor (let i = 0; i < m; i++) sisi.push(readInts());";
$readGraphPy = "n, m = map(int, input().split())\nsisi = [tuple(map(int, input().split())) for _ in range(m)]";

/**
 * Jalan acak sepanjang $len langkah di simpul $lo..$hi (tanpa self-loop).
 * $closed = true: kembali ke simpul awal. Mengembalikan daftar sisi.
 */
$walk = function (int $lo, int $hi, int $len, bool $closed, ?int $start = null): array {
    $cur = $start ?? mt_rand($lo, $hi);
    $first = $cur;
    $edges = [];
    for ($i = 0; $i < $len; $i++) {
        $last = $closed && $i === $len - 1;
        if ($last) {
            $nx = $first;
        } else {
            do {
                $nx = mt_rand($lo, $hi);
            } while ($nx === $cur || ($closed && $i === $len - 2 && $nx === $first));
        }
        $edges[] = [$cur, $nx];
        $cur = $nx;
    }

    return $edges;
};

/** Sisi acak tanpa self-loop (boleh ganda). */
$randomEdges = function (int $n, int $m, int $lo = 1): array {
    $e = [];
    for ($i = 0; $i < $m; $i++) {
        $u = mt_rand($lo, $n);
        do {
            $v = mt_rand($lo, $n);
        } while ($v === $u);
        $e[] = [$u, $v];
    }

    return $e;
};

$shuffleOrient = function (array $edges, bool $flip = true): array {
    T::shuffle($edges);
    if ($flip) {
        foreach ($edges as &$e) {
            if (mt_rand(0, 1)) {
                $e = [$e[1], $e[0]];
            }
        }
        unset($e);
    }

    return $edges;
};

/** Komponen terhubung (DSU) di antara simpul yang punya sisi. */
$components = function (int $n, array $edges): array {
    $p = range(0, $n);
    $find = function (int $x) use (&$p) {
        while ($p[$x] !== $x) {
            $p[$x] = $p[$p[$x]];
            $x = $p[$x];
        }

        return $x;
    };
    foreach ($edges as [$u, $v]) {
        $a = $find($u);
        $b = $find($v);
        if ($a !== $b) {
            $p[$a] = $b;
        }
    }
    $root = [];
    foreach ($edges as [$u, $v]) {
        $root[$u] = $find($u);
        $root[$v] = $find($v);
    }

    return $root; // simpul ber-sisi => wakil komponen
};

return [
    [
        'slug' => 'gambar-amplop',
        'lesson' => 'euler',
        'title' => 'Gambar Tanpa Angkat Pensil',
        'difficulty' => 'Mudah',
        'tags' => ['jalur euler', 'derajat', 'keterhubungan'],
        'statement' => '<p>Dina suka menantang adiknya menggambar pola garis <strong>tanpa mengangkat pensil</strong> dan <strong>tanpa menggambar garis yang sama dua kali</strong>. Sebuah pola terdiri dari <strong>N</strong> titik dan <strong>M</strong> garis lurus; garis ke-i menghubungkan titik <code>u<sub>i</sub></code> dan <code>v<sub>i</sub></code>. Dua titik boleh dihubungkan lebih dari satu garis (garisnya melengkung).</p>
<p>Setiap garis harus tergambar tepat sekali. Titik yang tidak terhubung ke garis mana pun boleh diabaikan. Tentukan jenis pola:</p>
<ul><li><code>SIRKUIT</code>: pola bisa digambar dan pensil bisa berakhir di titik awal.</li><li><code>JALUR</code>: pola bisa digambar, tetapi pensil pasti berakhir di titik yang berbeda dari titik awal.</li><li><code>TIDAK</code>: pola tidak bisa digambar dengan satu goresan.</li></ul>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code> (u ≠ v).</p>',
        'output_format' => '<p>Salah satu dari <code>SIRKUIT</code>, <code>JALUR</code>, atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>1 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "5 8\n1 2\n2 3\n3 4\n4 1\n1 3\n2 4\n3 5\n4 5\n", 'explanation' => 'Pola "rumah amplop". Titik 1 dan 2 berderajat 3 (ganjil), sisanya genap: bisa digambar dari 1 ke 2, tetapi tidak bisa kembali ke titik awal.'],
            ['input' => "4 7\n1 3\n1 3\n2 3\n2 3\n1 4\n2 4\n3 4\n", 'explanation' => 'Jembatan Königsberg: keempat titik berderajat ganjil.'],
            ['input' => "6 6\n1 2\n2 3\n3 1\n4 5\n5 6\n6 4\n", 'explanation' => 'Dua segitiga terpisah. Derajat semuanya genap, tetapi pensil harus diangkat untuk pindah ke segitiga kedua.'],
        ],
        'tests' => function () use ($walk, $randomEdges, $shuffleOrient) {
            $fmt = fn (int $n, array $e) => T::graphInput("$n ".count($e), $e);
            $twoParts = array_merge($walk(1, 50, 120, true), $walk(51, 100, 80, true));

            return [
                "2 1\n1 2\n", "2 2\n1 2\n2 1\n", "3 3\n1 2\n2 3\n3 1\n", "4 3\n1 2\n1 3\n1 4\n",
                $fmt(10, $shuffleOrient($walk(1, 6, 12, true))), $fmt(10, $shuffleOrient($walk(2, 9, 15, false))),
                $fmt(100, $shuffleOrient($twoParts)), $fmt(1000, $shuffleOrient($randomEdges(1000, 3000))),
                $fmt(100000, $shuffleOrient($walk(1, 100000, 200000, true))), $fmt(100000, $shuffleOrient($walk(1, 80000, 200000, false))),
                $fmt(100000, $shuffleOrient(array_merge($walk(1, 100000, 199999, false), [[1, 2]]))),
                $fmt(100000, $shuffleOrient(array_merge($walk(1, 50000, 150000, true), $walk(50001, 100000, 50000, true)))),
            ];
        },
        'solve' => function (string $input) use ($components) {
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
            $roots = array_unique(array_values($components($n, $edges)));
            if (count($roots) > 1) {
                return 'TIDAK';
            }
            $odd = 0;
            for ($v = 1; $v <= $n; $v++) {
                $odd += $deg[$v] % 2;
            }

            return $odd === 0 ? 'SIRKUIT' : ($odd === 2 ? 'JALUR' : 'TIDAK');
        },
        'starter' => $st($readGraphCpp, $readGraphJs, $readGraphPy),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<int> induk;
int cari(int x) {                       // DSU dengan path compression (iteratif)
    while (induk[x] != x) x = induk[x] = induk[induk[x]];
    return x;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> deg(n + 1, 0);
    induk.resize(n + 1);
    iota(induk.begin(), induk.end(), 0);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        deg[u]++;
        deg[v]++;
        induk[cari(u)] = cari(v);
    }

    // 1. Semua titik yang punya garis harus berada di satu komponen.
    int wakil = -1;
    for (int v = 1; v <= n; v++) {
        if (deg[v] == 0) continue;
        if (wakil == -1) wakil = cari(v);
        else if (cari(v) != wakil) {
            cout << "TIDAK\n";
            return 0;
        }
    }
    // 2. Hitung titik berderajat ganjil.
    int ganjil = 0;
    for (int v = 1; v <= n; v++) ganjil += deg[v] % 2;

    if (ganjil == 0) cout << "SIRKUIT\n";
    else if (ganjil == 2) cout << "JALUR\n";
    else cout << "TIDAK\n";
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const deg = new Int32Array(n + 1);
const induk = new Int32Array(n + 1).map((_, i) => i);
const cari = (x) => {
  while (induk[x] !== x) x = induk[x] = induk[induk[x]];
  return x;
};
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  deg[u]++;
  deg[v]++;
  induk[cari(u)] = cari(v);
}

let wakil = -1, terhubung = true;
for (let v = 1; v <= n; v++) {
  if (deg[v] === 0) continue;
  if (wakil === -1) wakil = cari(v);
  else if (cari(v) !== wakil) terhubung = false;
}
let ganjil = 0;
for (let v = 1; v <= n; v++) ganjil += deg[v] % 2;

if (!terhubung) console.log("TIDAK");
else if (ganjil === 0) console.log("SIRKUIT");
else if (ganjil === 2) console.log("JALUR");
else console.log("TIDAK");
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
deg = [0] * (n + 1)
induk = list(range(n + 1))

def cari(x):
    while induk[x] != x:
        induk[x] = induk[induk[x]]
        x = induk[x]
    return x

for _ in range(m):
    u, v = map(int, input().split())
    deg[u] += 1
    deg[v] += 1
    induk[cari(u)] = cari(v)

wakil = {cari(v) for v in range(1, n + 1) if deg[v] > 0}
ganjil = sum(d % 2 for d in deg)
if len(wakil) > 1:
    print("TIDAK")
elif ganjil == 0:
    print("SIRKUIT")
elif ganjil == 2:
    print("JALUR")
else:
    print("TIDAK")
CODE,
        ],
        'editorial' => '<p>Ini persis pertanyaan jalur Euler pada graph tak berarah (dengan sisi ganda). Kita tidak perlu membangun jalurnya, cukup memeriksa dua syarat:</p>
<ol><li><strong>Terhubung:</strong> semua titik yang punya garis berada di satu komponen. Gunakan DSU atau BFS/DFS. Titik tanpa garis diabaikan.</li><li><strong>Derajat:</strong> hitung titik berderajat ganjil. 0 → <code>SIRKUIT</code>, 2 → <code>JALUR</code>, selain itu <code>TIDAK</code>.</li></ol>
<p>Contoh ketiga menunjukkan mengapa syarat keterhubungan tidak boleh dilupakan: semua derajat genap, tetapi gambarnya terpisah. Total O(N + M).</p>',
        'hints' => [
            'Titik = simpul, garis = sisi. Menggambar tanpa mengangkat pensil = jalur yang melewati setiap sisi tepat sekali.',
            'Hitung derajat setiap titik. Berapa banyak titik berderajat ganjil yang diperbolehkan?',
            'Jangan lupa: semua garis harus berada dalam satu komponen terhubung. Pakai DSU.',
        ],
    ],

    [
        'slug' => 'goresan-minimum',
        'lesson' => 'euler',
        'title' => 'Goresan Paling Sedikit',
        'difficulty' => 'Sedang',
        'tags' => ['jalur euler', 'derajat', 'komponen'],
        'statement' => '<p>Seorang seniman ingin menggambar sebuah pola berisi <strong>N</strong> titik dan <strong>M</strong> garis. Satu <strong>goresan</strong> berarti menggambar beberapa garis secara berurutan tanpa mengangkat pensil. Setiap garis harus digambar <strong>tepat sekali</strong> (garis yang sudah tergambar tidak boleh ditimpa).</p>
<p>Berapa goresan paling sedikit yang dibutuhkan untuk menggambar semua garis?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>: garis antara titik u dan v (u ≠ v, boleh ada garis ganda).</p>',
        'output_format' => '<p>Banyak goresan minimum.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>1 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "4 7\n1 3\n1 3\n2 3\n2 3\n1 4\n2 4\n3 4\n", 'explanation' => 'Empat titik ganjil dalam satu komponen: butuh 4 / 2 = 2 goresan.'],
            ['input' => "7 6\n1 2\n2 3\n3 1\n4 5\n6 7\n5 6\n", 'explanation' => 'Segitiga 1-2-3 (1 goresan, semua genap) dan garis lurus 4-5-6-7 (1 goresan dari 4 ke 7).'],
        ],
        'tests' => function () use ($walk, $randomEdges, $shuffleOrient) {
            $fmt = fn (int $n, array $e) => T::graphInput("$n ".count($e), $e);
            $many = function (int $parts, int $size, int $len) use ($walk) {
                $all = [];
                for ($p = 0; $p < $parts; $p++) {
                    $lo = $p * $size + 1;
                    $all = array_merge($all, $walk($lo, $lo + $size - 1, $len, mt_rand(0, 1) === 1));
                }

                return $all;
            };

            return [
                "2 1\n1 2\n", "3 3\n1 2\n2 3\n3 1\n", "5 4\n1 2\n1 3\n1 4\n1 5\n", $fmt(10, $shuffleOrient($randomEdges(10, 12))),
                $fmt(1000, $shuffleOrient($many(10, 100, 150))), $fmt(1000, $shuffleOrient($randomEdges(1000, 700))),
                $fmt(100000, $shuffleOrient($randomEdges(100000, 200000))), $fmt(100000, $shuffleOrient($many(1000, 100, 200))),
                $fmt(100000, $shuffleOrient($walk(1, 100000, 200000, true))), $fmt(100000, $shuffleOrient($randomEdges(100000, 60000))),
            ];
        },
        'solve' => function (string $input) use ($components) {
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
            $odd = [];
            foreach ($components($n, $edges) as $v => $r) {
                $odd[$r] = ($odd[$r] ?? 0) + $deg[$v] % 2;
            }
            $ans = 0;
            foreach ($odd as $c) {
                $ans += max(1, intdiv($c, 2));
            }

            return (string) $ans;
        },
        'starter' => $st($readGraphCpp, $readGraphJs, $readGraphPy),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<int> induk;
int cari(int x) {
    while (induk[x] != x) x = induk[x] = induk[induk[x]];
    return x;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> deg(n + 1, 0);
    induk.resize(n + 1);
    iota(induk.begin(), induk.end(), 0);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        deg[u]++;
        deg[v]++;
        induk[cari(u)] = cari(v);
    }

    // ganjil[r] = banyak titik berderajat ganjil di komponen ber-wakil r
    vector<int> ganjil(n + 1, 0);
    vector<bool> adaSisi(n + 1, false);
    for (int v = 1; v <= n; v++) {
        if (deg[v] == 0) continue;
        int r = cari(v);
        adaSisi[r] = true;
        ganjil[r] += deg[v] % 2;
    }
    long long goresan = 0;
    for (int r = 1; r <= n; r++)
        if (adaSisi[r]) goresan += max(1, ganjil[r] / 2);   // tiap goresan "memakai" 2 titik ganjil
    cout << goresan << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const deg = new Int32Array(n + 1);
const induk = new Int32Array(n + 1).map((_, i) => i);
const cari = (x) => {
  while (induk[x] !== x) x = induk[x] = induk[induk[x]];
  return x;
};
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  deg[u]++;
  deg[v]++;
  induk[cari(u)] = cari(v);
}
const ganjil = new Int32Array(n + 1);
const adaSisi = new Uint8Array(n + 1);
for (let v = 1; v <= n; v++) {
  if (deg[v] === 0) continue;
  const r = cari(v);
  adaSisi[r] = 1;
  ganjil[r] += deg[v] % 2;
}
let goresan = 0;
for (let r = 1; r <= n; r++) if (adaSisi[r]) goresan += Math.max(1, ganjil[r] / 2);
console.log(goresan);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
deg = [0] * (n + 1)
induk = list(range(n + 1))

def cari(x):
    while induk[x] != x:
        induk[x] = induk[induk[x]]
        x = induk[x]
    return x

for _ in range(m):
    u, v = map(int, input().split())
    deg[u] += 1
    deg[v] += 1
    induk[cari(u)] = cari(v)

ganjil = {}
for v in range(1, n + 1):
    if deg[v]:
        r = cari(v)
        ganjil[r] = ganjil.get(r, 0) + deg[v] % 2
print(sum(max(1, c // 2) for c in ganjil.values()))
CODE,
        ],
        'editorial' => '<p>Kerjakan setiap komponen terhubung (yang punya garis) secara terpisah, karena satu goresan tidak bisa berpindah komponen.</p>
<p>Misalkan sebuah komponen punya <code>k</code> titik berderajat ganjil (k selalu genap). Setiap goresan yang tidak tertutup punya dua ujung, dan sebuah titik ganjil pasti menjadi ujung dari suatu goresan. Jadi dibutuhkan paling sedikit <code>k / 2</code> goresan.</p>
<p>Jumlah itu juga cukup: tambahkan k/2 garis khayalan yang memasangkan titik-titik ganjil. Sekarang semua derajat genap dan ada sirkuit Euler; hapus kembali garis khayalan dan sirkuit itu terpotong menjadi tepat k/2 goresan. Jika k = 0, komponen bisa digambar dalam 1 goresan.</p>
<p>Jawaban: jumlah <code>max(1, k / 2)</code> untuk setiap komponen yang punya garis. DSU membuatnya O(N + M α(N)).</p>',
        'hints' => [
            'Goresan tidak bisa melompat ke komponen lain. Hitung jawaban per komponen, lalu jumlahkan.',
            'Di dalam satu komponen, titik berderajat ganjil pasti menjadi ujung sebuah goresan. Satu goresan punya berapa ujung?',
            'Komponen dengan k titik ganjil butuh max(1, k / 2) goresan.',
        ],
        'sample_visual' => 'graph',
    ],

    [
        'slug' => 'rantai-kata',
        'lesson' => 'euler',
        'title' => 'Rantai Kata',
        'difficulty' => 'Sedang',
        'tags' => ['jalur euler', 'graph berarah', 'graph tersembunyi'],
        'statement' => '<p>Dalam permainan sambung kata, setiap kata harus diawali huruf terakhir kata sebelumnya. Contohnya <code>ayam → makan → nasi</code>.</p>
<p>Diberikan <strong>N</strong> kata (boleh ada kata yang sama). Bisakah <strong>semua</strong> kata itu disusun menjadi satu rantai, masing-masing dipakai tepat sekali?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi satu kata huruf kecil.</p>',
        'output_format' => '<p><code>YA</code> jika bisa, <code>TIDAK</code> jika tidak.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>Panjang setiap kata 1 sampai 10</li></ul>',
        'samples' => [
            ['input' => "3\nnasi\nayam\nmakan\n", 'explanation' => 'ayam → makan → nasi.'],
            ['input' => "2\nbola\nbola\n", 'explanation' => '"bola" berakhir dengan a, tetapi tidak ada kata berawalan a.'],
            ['input' => "4\naba\naca\nxyx\nxzx\n", 'explanation' => 'Kata berawalan a dan kata berawalan x tidak pernah bisa bersambung.'],
        ],
        'tests' => function () {
            $mid = fn () => T::word(mt_rand(0, 8), 'abcdefghijklmnopqrstuvwxyz');
            $word = function (string $a, string $b) use ($mid) {
                $w = $a.$mid().$b;
                if (strlen($w) > 10) {
                    $w = $a.substr($w, 1, 8).$b;
                }

                return mt_rand(0, 9) === 0 && $a === $b ? $a : $w;
            };
            $chain = function (int $n, string $letters, bool $closed) use ($word) {
                $L = strlen($letters);
                $cur = $letters[mt_rand(0, $L - 1)];
                $first = $cur;
                $ws = [];
                for ($i = 0; $i < $n; $i++) {
                    $nx = ($closed && $i === $n - 1) ? $first : $letters[mt_rand(0, $L - 1)];
                    $ws[] = $word($cur, $nx);
                    $cur = $nx;
                }
                T::shuffle($ws);

                return "$n\n".implode("\n", $ws)."\n";
            };
            $random = function (int $n, string $letters) use ($word) {
                $L = strlen($letters);
                $ws = [];
                for ($i = 0; $i < $n; $i++) {
                    $ws[] = $word($letters[mt_rand(0, $L - 1)], $letters[mt_rand(0, $L - 1)]);
                }

                return "$n\n".implode("\n", $ws)."\n";
            };
            $broken = function (int $n) use ($chain) {
                $s = $chain($n, 'abcdefghij', true);
                $lines = explode("\n", trim($s));
                $lines[1] = 'q'.substr($lines[1], 1);   // dua kata diganti awalannya:
                $lines[2] = 'q'.substr($lines[2], 1);   // huruf q punya keluar - masuk = 2

                return implode("\n", $lines)."\n";
            };

            return [
                "1\nz\n", "1\nkapal\n", "2\nab\nba\n", "3\nab\nbc\nca\n", "3\nab\nab\nba\n",
                $chain(10, 'abc', false), $random(10, 'ab'), $chain(1000, 'abcdefghijklmnopqrstuvwxyz', true),
                $chain(100000, 'abcdefghijklmnopqrstuvwxyz', false), $chain(100000, 'aeiou', true), $random(100000, 'abcdefghijklmnopqrstuvwxyz'),
                $broken(100000), "4\nab\nba\ncd\ndc\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $in = array_fill(0, 26, 0);
            $out = array_fill(0, 26, 0);
            $p = range(0, 25);
            $find = function (int $x) use (&$p) {
                while ($p[$x] !== $x) {
                    $p[$x] = $p[$p[$x]];
                    $x = $p[$x];
                }

                return $x;
            };
            for ($i = 1; $i <= $n; $i++) {
                $w = trim($lines[$i]);
                $a = ord($w[0]) - 97;
                $b = ord($w[strlen($w) - 1]) - 97;
                $out[$a]++;
                $in[$b]++;
                $p[$find($a)] = $find($b);
            }
            $root = -1;
            $start = 0;
            $end = 0;
            for ($c = 0; $c < 26; $c++) {
                if ($in[$c] + $out[$c] === 0) {
                    continue;
                }
                if ($root === -1) {
                    $root = $find($c);
                } elseif ($find($c) !== $root) {
                    return 'TIDAK';
                }
                $d = $out[$c] - $in[$c];
                if ($d === 1) {
                    $start++;
                } elseif ($d === -1) {
                    $end++;
                } elseif ($d !== 0) {
                    return 'TIDAK';
                }
            }

            return ($start === 0 && $end === 0) || ($start === 1 && $end === 1) ? 'YA' : 'TIDAK';
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<string> kata(n);\n    for (auto& w : kata) cin >> w;", "const n = Number(readLine());\nconst kata = [];\nfor (let i = 0; i < n; i++) kata.push(readLine().trim());", "n = int(input())\nkata = [input().strip() for _ in range(n)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int induk[26];
int cari(int x) { return induk[x] == x ? x : induk[x] = cari(induk[x]); }

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    // Simpul = 26 huruf. Kata = sisi berarah dari huruf pertama ke huruf terakhir.
    int masuk[26] = {0}, keluar[26] = {0};
    for (int c = 0; c < 26; c++) induk[c] = c;
    for (int i = 0; i < n; i++) {
        string w;
        cin >> w;
        int a = w[0] - 'a', b = w.back() - 'a';
        keluar[a]++;
        masuk[b]++;
        induk[cari(a)] = cari(b);          // keterhubungan lemah (abaikan arah)
    }

    int wakil = -1, awal = 0, akhir = 0;
    bool bisa = true;
    for (int c = 0; c < 26; c++) {
        if (masuk[c] + keluar[c] == 0) continue;     // huruf tidak dipakai
        if (wakil == -1) wakil = cari(c);
        else if (cari(c) != wakil) bisa = false;     // ada dua kelompok terpisah
        int d = keluar[c] - masuk[c];
        if (d == 1) awal++;
        else if (d == -1) akhir++;
        else if (d != 0) bisa = false;
    }
    if (!((awal == 0 && akhir == 0) || (awal == 1 && akhir == 1))) bisa = false;
    cout << (bisa ? "YA" : "TIDAK") << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const masuk = new Array(26).fill(0), keluar = new Array(26).fill(0);
const induk = Array.from({ length: 26 }, (_, i) => i);
const cari = (x) => (induk[x] === x ? x : (induk[x] = cari(induk[x])));
for (let i = 0; i < n; i++) {
  const w = readLine().trim();
  const a = w.charCodeAt(0) - 97, b = w.charCodeAt(w.length - 1) - 97;
  keluar[a]++;
  masuk[b]++;
  induk[cari(a)] = cari(b);
}
let wakil = -1, awal = 0, akhir = 0, bisa = true;
for (let c = 0; c < 26; c++) {
  if (masuk[c] + keluar[c] === 0) continue;
  if (wakil === -1) wakil = cari(c);
  else if (cari(c) !== wakil) bisa = false;
  const d = keluar[c] - masuk[c];
  if (d === 1) awal++;
  else if (d === -1) akhir++;
  else if (d !== 0) bisa = false;
}
if (!((awal === 0 && akhir === 0) || (awal === 1 && akhir === 1))) bisa = false;
console.log(bisa ? "YA" : "TIDAK");
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
masuk = [0] * 26
keluar = [0] * 26
induk = list(range(26))

def cari(x):
    while induk[x] != x:
        induk[x] = induk[induk[x]]
        x = induk[x]
    return x

for _ in range(n):
    w = input().strip()
    a, b = ord(w[0]) - 97, ord(w[-1]) - 97
    keluar[a] += 1
    masuk[b] += 1
    induk[cari(a)] = cari(b)

dipakai = [c for c in range(26) if masuk[c] + keluar[c] > 0]
bisa = len({cari(c) for c in dipakai}) == 1
awal = sum(1 for c in dipakai if keluar[c] - masuk[c] == 1)
akhir = sum(1 for c in dipakai if masuk[c] - keluar[c] == 1)
if any(abs(keluar[c] - masuk[c]) > 1 for c in dipakai):
    bisa = False
if not ((awal == 0 and akhir == 0) or (awal == 1 and akhir == 1)):
    bisa = False
print("YA" if bisa else "TIDAK")
CODE,
        ],
        'editorial' => '<p>Jangan jadikan kata sebagai simpul (itu soal jalur Hamilton yang sulit). Jadikan setiap kata sebuah <strong>sisi berarah</strong> dari huruf pertamanya ke huruf terakhirnya. Graph-nya hanya punya 26 simpul. Menyusun semua kata menjadi rantai = jalur yang melewati setiap sisi tepat sekali = <strong>jalur Euler berarah</strong>.</p>
<p>Periksa syarat jalur Euler berarah:</p>
<ul><li>Semua huruf yang dipakai berada dalam satu komponen jika arah sisi diabaikan (DSU pada 26 huruf).</li><li>Setiap huruf punya masuk = keluar, kecuali boleh ada tepat satu huruf dengan keluar − masuk = 1 (awal rantai) dan tepat satu huruf dengan masuk − keluar = 1 (akhir rantai).</li></ul>
<p>Total O(N) ditambah panjang input.</p>',
        'hints' => [
            'Mencoba urutan kata adalah soal Hamilton yang terlalu lambat. Coba ubah sudut pandang: apa yang menjadi simpul?',
            'Simpul = huruf (26 saja). Kata "ayam" = sisi berarah a → m. Rantai kata = jalan yang memakai setiap sisi sekali.',
            'Cek syarat jalur Euler berarah: keterhubungan (abaikan arah) dan selisih derajat masuk/keluar.',
        ],
    ],

    [
        'slug' => 'tiket-pesawat',
        'lesson' => 'euler',
        'title' => 'Tiket Pesawat',
        'difficulty' => 'Sulit',
        'tags' => ['jalur euler', 'hierholzer', 'leksikografis'],
        'statement' => '<p>Raka memenangkan undian berisi <strong>M</strong> tiket pesawat satu arah antara <strong>N</strong> kota. Tiket ke-i bisa dipakai sekali untuk terbang dari kota <code>a<sub>i</sub></code> ke kota <code>b<sub>i</sub></code> (boleh ada tiket yang sama persis).</p>
<p>Raka tinggal di kota <strong>1</strong> dan ingin memakai <strong>semua</strong> tiket, masing-masing tepat sekali, dalam satu perjalanan yang dimulai dari kota 1. Cetak urutan kota yang dikunjungi. Jika ada beberapa kemungkinan, cetak yang <strong>terkecil secara leksikografis</strong> (bandingkan kota pertama yang berbeda). Jika tidak mungkin, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>a b</code> (a ≠ b).</p>',
        'output_format' => '<p>M + 1 bilangan dipisah spasi: urutan kota dari kota 1, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>1 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "4 4\n1 2\n1 3\n3 1\n2 4\n", 'explanation' => 'Dari kota 1, tujuan terkecil adalah 2. Tetapi jika langsung ke 2, Raka terjebak di kota 4 sebelum memakai tiket 1 → 3 dan 3 → 1. Rute terkecil yang memakai semua tiket adalah 1 → 3 → 1 → 2 → 4.'],
            ['input' => "3 2\n1 2\n1 3\n", 'explanation' => 'Setelah memakai salah satu tiket dari kota 1, Raka tidak bisa kembali ke kota 1 untuk memakai tiket lainnya.'],
        ],
        'tests' => function () use ($walk, $randomEdges) {
            $fmt = function (int $n, array $e) {
                T::shuffle($e);

                return T::graphInput("$n ".count($e), $e);
            };

            return [
                "2 1\n1 2\n", "2 1\n2 1\n", "3 3\n1 2\n2 3\n3 1\n", "3 4\n1 2\n2 1\n1 3\n3 1\n", "3 2\n1 2\n1 3\n",
                $fmt(5, $walk(1, 5, 12, false, 1)), $fmt(6, $walk(1, 6, 15, true, 1)), $fmt(10, $randomEdges(10, 20)),
                $fmt(1000, $walk(1, 300, 5000, false, 1)), $fmt(1000, $walk(2, 1000, 3000, true)),
                $fmt(100000, $walk(1, 100000, 200000, false, 1)), $fmt(100000, $walk(1, 20, 200000, true, 1)),
                $fmt(100000, array_merge($walk(1, 50000, 150000, true, 1), $walk(50001, 100000, 50000, true))),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            $diff = array_fill(0, $n + 1, 0);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $adj[$a][] = $b;
                $diff[$a]++;
                $diff[$b]--;
            }
            $startOk = true;
            $plus = 0;
            $minus = 0;
            for ($v = 1; $v <= $n; $v++) {
                if ($diff[$v] === 1) {
                    $plus++;
                    if ($v !== 1) {
                        $startOk = false;
                    }
                } elseif ($diff[$v] === -1) {
                    $minus++;
                } elseif ($diff[$v] !== 0) {
                    return '-1';
                }
            }
            if (! $startOk || ! (($plus === 0 && $minus === 0) || ($plus === 1 && $minus === 1))) {
                return '-1';
            }
            foreach ($adj as &$l) {
                sort($l);
            }
            unset($l);
            $ptr = array_fill(0, $n + 1, 0);
            $stack = [1];
            $route = [];
            while ($stack) {
                $u = $stack[count($stack) - 1];
                if ($ptr[$u] < count($adj[$u])) {
                    $stack[] = $adj[$u][$ptr[$u]++];
                } else {
                    $route[] = $u;
                    array_pop($stack);
                }
            }
            if (count($route) !== $m + 1) {
                return '-1';
            }

            return implode(' ', array_reverse($route));
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int a, b;\n        cin >> a >> b;\n        adj[a].push_back(b);\n    }", "const [n, m] = readInts();\nconst adj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [a, b] = readInts();\n  adj[a].push(b);\n}", "n, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    a, b = map(int, input().split())\n    adj[a].append(b)"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1);
    vector<int> selisih(n + 1, 0);        // keluar - masuk
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        selisih[a]++;
        selisih[b]--;
    }

    // Syarat jalur Euler berarah yang DIMULAI di kota 1.
    int plus = 0, minus = 0;
    bool bisa = true;
    for (int v = 1; v <= n; v++) {
        if (selisih[v] == 1) {
            plus++;
            if (v != 1) bisa = false;     // awal jalur harus kota 1
        } else if (selisih[v] == -1) minus++;
        else if (selisih[v] != 0) bisa = false;
    }
    if (!((plus == 0 && minus == 0) || (plus == 1 && minus == 1))) bisa = false;
    if (!bisa) {
        cout << -1 << '\n';
        return 0;
    }

    // Tetangga terkecil dicoba lebih dulu -> rute terkecil secara leksikografis.
    for (int v = 1; v <= n; v++) sort(adj[v].begin(), adj[v].end());
    vector<int> ptr(n + 1, 0), tumpukan = {1}, rute;
    while (!tumpukan.empty()) {
        int u = tumpukan.back();
        if (ptr[u] < (int)adj[u].size()) tumpukan.push_back(adj[u][ptr[u]++]);
        else {
            rute.push_back(u);
            tumpukan.pop_back();
        }
    }
    if ((int)rute.size() != m + 1) {      // ada tiket yang tidak terjangkau
        cout << -1 << '\n';
        return 0;
    }
    reverse(rute.begin(), rute.end());
    string out;
    for (int i = 0; i <= m; i++) out += to_string(rute[i]) + (i < m ? " " : "\n");
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const selisih = new Int32Array(n + 1);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  selisih[a]++;
  selisih[b]--;
}
let plus = 0, minus = 0, bisa = true;
for (let v = 1; v <= n; v++) {
  if (selisih[v] === 1) {
    plus++;
    if (v !== 1) bisa = false;
  } else if (selisih[v] === -1) minus++;
  else if (selisih[v] !== 0) bisa = false;
}
if (!((plus === 0 && minus === 0) || (plus === 1 && minus === 1))) bisa = false;

let hasil = "-1";
if (bisa) {
  for (const l of adj) l.sort((x, y) => x - y);
  const ptr = new Int32Array(n + 1);
  const tumpukan = [1], rute = [];
  while (tumpukan.length) {
    const u = tumpukan[tumpukan.length - 1];
    if (ptr[u] < adj[u].length) tumpukan.push(adj[u][ptr[u]++]);
    else rute.push(tumpukan.pop());
  }
  if (rute.length === m + 1) hasil = rute.reverse().join(" ");
}
console.log(hasil);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
selisih = [0] * (n + 1)
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    selisih[a] += 1
    selisih[b] -= 1

plus = [v for v in range(1, n + 1) if selisih[v] == 1]
minus = [v for v in range(1, n + 1) if selisih[v] == -1]
bisa = all(-1 <= d <= 1 for d in selisih)
bisa = bisa and ((not plus and not minus) or (plus == [1] and len(minus) == 1))

hasil = "-1"
if bisa:
    for l in adj:
        l.sort()
    ptr = [0] * (n + 1)
    tumpukan, rute = [1], []
    while tumpukan:
        u = tumpukan[-1]
        if ptr[u] < len(adj[u]):
            tumpukan.append(adj[u][ptr[u]])
            ptr[u] += 1
        else:
            rute.append(tumpukan.pop())
    if len(rute) == m + 1:
        hasil = " ".join(map(str, reversed(rute)))
print(hasil)
CODE,
        ],
        'editorial' => '<p>Tiket = sisi berarah, kota = simpul. Memakai semua tiket tepat sekali dalam satu perjalanan = <strong>jalur Euler berarah</strong> yang dimulai di kota 1.</p>
<p><strong>Syarat.</strong> Setiap kota punya keluar = masuk, kecuali (opsional) satu kota dengan keluar − masuk = 1 yang <em>harus</em> kota 1, dan satu kota dengan masuk − keluar = 1. Lalu jalankan Hierholzer dari kota 1; jika rute tidak berisi M + 1 kota, ada tiket yang tidak terjangkau dan jawabannya <code>-1</code>.</p>
<p><strong>Terkecil secara leksikografis.</strong> Urutkan setiap daftar tetangga dan selalu ambil tetangga terkecil yang tersisa. Mungkin terlihat aneh: kadang tetangga terkecil membawa kita ke jalan buntu. Hierholzer menangani itu dengan sendirinya: bagian yang buntu duluan dipindah ke <em>akhir</em> rute, sehingga di setiap posisi rute akhir memakai pilihan terkecil yang masih memungkinkan menyelesaikan perjalanan.</p>
<p>Contoh pertama: dari kota 1, Hierholzer mencoba 2 lalu 4 dan buntu. Kota 4 dicatat sebagai ujung rute, Hierholzer mundur ke 1, lalu menelusuri 1 → 3 → 1. Setelah dibalik, rutenya 1 → 3 → 1 → 2 → 4: tetangga terkecil (2) tetap dipakai, hanya posisinya digeser ke bagian akhir yang memang harus menjadi akhir perjalanan.</p>
<p>Total O(N + M log M) karena pengurutan.</p>',
        'hints' => [
            'Tiket = sisi berarah. Memakai semua tiket sekali = jalur Euler berarah dari kota 1.',
            'Periksa selisih keluar − masuk setiap kota. Kota 1 boleh +1, satu kota boleh −1, sisanya harus 0.',
            'Urutkan tetangga, jalankan Hierholzer dengan tumpukan, cek panjang rute = M + 1, lalu balik.',
        ],
        'sample_visual' => 'digraph',
    ],
];
