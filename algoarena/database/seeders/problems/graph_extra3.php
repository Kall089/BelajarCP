<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan tambahan track Graph (bagian 3): Floyd-Warshall, LCA, SCC, jembatan & artikulasi.
 * Semua kode C++ harus lolos C++14. Solusi ditulis iteratif agar aman untuk graph yang dalam.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$cppDigraph = "    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n    }";
$jsDigraph = "const [n, m] = readInts();\nconst adj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [u, v] = readInts();\n  adj[u].push(v);\n}";
$pyDigraph = "n, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    u, v = map(int, input().split())\n    adj[u].append(v)";
$cppGraph = "    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }";
$jsGraph = "const [n, m] = readInts();\nconst adj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [u, v] = readInts();\n  adj[u].push(v);\n  adj[v].push(u);\n}";
$pyGraph = "n, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)";

/** Komponen kuat (PHP, iteratif): mengembalikan [banyak, komp[]] dengan nomor menurut simpul terkecil. */
$sccPhp = function (int $n, array $adj): array {
    $radj = array_fill(0, $n + 1, []);
    foreach ($adj as $u => $list) {
        foreach ($list as $v) {
            $radj[$v][] = $u;
        }
    }
    $vis = array_fill(0, $n + 1, false);
    $order = [];
    $idx = array_fill(0, $n + 1, 0);
    for ($s = 1; $s <= $n; $s++) {
        if ($vis[$s]) {
            continue;
        }
        $vis[$s] = true;
        $stack = [$s];
        while ($stack) {
            $u = end($stack);
            if ($idx[$u] < count($adj[$u])) {
                $v = $adj[$u][$idx[$u]++];
                if (! $vis[$v]) {
                    $vis[$v] = true;
                    $stack[] = $v;
                }
            } else {
                array_pop($stack);
                $order[] = $u;
            }
        }
    }
    $comp = array_fill(0, $n + 1, -1);
    $c = 0;
    for ($i = $n - 1; $i >= 0; $i--) {
        $s = $order[$i];
        if ($comp[$s] !== -1) {
            continue;
        }
        $comp[$s] = $c;
        $stack = [$s];
        while ($stack) {
            $u = array_pop($stack);
            foreach ($radj[$u] as $v) {
                if ($comp[$v] === -1) {
                    $comp[$v] = $c;
                    $stack[] = $v;
                }
            }
        }
        $c++;
    }

    return [$c, $comp];
};

/** Jembatan (PHP, iteratif, graph sederhana): mengembalikan [tin, low, parent, urutan kunjungan]. */
$lowlinkPhp = function (int $n, array $adj): array {
    $tin = array_fill(0, $n + 1, -1);
    $low = array_fill(0, $n + 1, 0);
    $par = array_fill(0, $n + 1, 0);
    $idx = array_fill(0, $n + 1, 0);
    $timer = 0;
    $roots = [];
    for ($s = 1; $s <= $n; $s++) {
        if ($tin[$s] !== -1) {
            continue;
        }
        $roots[] = $s;
        $tin[$s] = $low[$s] = $timer++;
        $stack = [$s];
        while ($stack) {
            $u = end($stack);
            if ($idx[$u] < count($adj[$u])) {
                $v = $adj[$u][$idx[$u]++];
                if ($v === $par[$u]) {
                    continue;
                }
                if ($tin[$v] !== -1) {
                    $low[$u] = min($low[$u], $tin[$v]);
                } else {
                    $par[$v] = $u;
                    $tin[$v] = $low[$v] = $timer++;
                    $stack[] = $v;
                }
            } else {
                array_pop($stack);
                if ($stack) {
                    $p = end($stack);
                    $low[$p] = min($low[$p], $low[$u]);
                }
            }
        }
    }

    return [$tin, $low, $par, $roots];
};

/** Graph tak berarah sederhana & terhubung yang punya banyak jembatan: gugus-gugus siklus yang disambung pohon. */
$bridgyGraph = function (int $n, int $extra): array {
    $edges = [];
    $seen = [];
    $add = function ($u, $v) use (&$edges, &$seen) {
        if ($u === $v) {
            return;
        }
        $k = min($u, $v).'-'.max($u, $v);
        if (isset($seen[$k])) {
            return;
        }
        $seen[$k] = true;
        $edges[] = [$u, $v];
    };
    $perm = range(1, $n);
    T::shuffle($perm);
    for ($i = 1; $i < $n; $i++) {
        $add($perm[mt_rand(max(0, $i - 3), $i - 1)], $perm[$i]);
    }
    for ($i = 0; $i < $extra; $i++) {
        $a = mt_rand(0, $n - 1);
        $b = min($n - 1, $a + mt_rand(1, 4));
        $add($perm[$a], $perm[$b]);
    }
    T::shuffle($edges);

    return $edges;
};

return [
    // ═════════════════════════════ Floyd-Warshall ═════════════════════════════
    [
        'slug' => 'jangkauan',
        'lesson' => 'floyd-warshall',
        'title' => 'Pasangan Terjangkau',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'transitive closure', 'floyd-warshall'],
        'statement' => '<p>Ada <strong>N</strong> halte dan <strong>M</strong> rute bus <strong>satu arah</strong>. Penumpang boleh berganti bus berkali-kali.</p>
<p>Berapa banyak pasangan terurut (a, b) dengan a ≠ b sehingga penumpang bisa pergi dari halte a ke halte b?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>: rute dari u ke v.</p>',
        'output_format' => '<p>Banyak pasangan terjangkau.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 400</li><li>0 ≤ M ≤ 5000</li></ul>',
        'samples' => [
            ['input' => "4 3\n1 2\n2 3\n3 1\n", 'explanation' => 'Halte 1, 2, 3 saling terjangkau (6 pasangan). Halte 4 tidak terhubung.'],
            ['input' => "3 2\n1 2\n2 3\n", 'explanation' => '(1,2), (1,3), (2,3).'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m) {
                $edges = T::edges($n, min($m, $n * ($n - 1)), true);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", $mk(5, 6), $mk(30, 60), $mk(200, 300), $mk(400, 5000), $mk(400, 450), $mk(400, 380)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = $v;
            }
            $total = 0;
            for ($s = 1; $s <= $n; $s++) {
                $seen = [$s => true];
                $q = [$s];
                for ($h = 0; $h < count($q); $h++) {
                    foreach ($adj[$q[$h]] as $v) {
                        if (! isset($seen[$v])) {
                            $seen[$v] = true;
                            $q[] = $v;
                        }
                    }
                }
                $total += count($q) - 1;
            }

            return (string) $total;
        },
        'starter' => $st($cppDigraph, $jsDigraph, $pyDigraph),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, m;
    cin >> n >> m;
    // reach[i][j] = apakah j bisa dicapai dari i
    vector<vector<char>> reach(n + 1, vector<char>(n + 1, 0));
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        reach[u][v] = 1;
    }
    // Floyd-Warshall versi boolean (transitive closure):
    // i bisa ke j jika i bisa ke k dan k bisa ke j
    for (int k = 1; k <= n; k++)
        for (int i = 1; i <= n; i++)
            if (reach[i][k])
                for (int j = 1; j <= n; j++)
                    if (reach[k][j]) reach[i][j] = 1;

    long long total = 0;
    for (int i = 1; i <= n; i++)
        for (int j = 1; j <= n; j++)
            if (i != j && reach[i][j]) total++;
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
}
// BFS dari setiap halte: O(N · (N + M))
let total = 0;
const seen = new Int32Array(n + 1);
for (let s = 1; s <= n; s++) {
  seen[s] = s;
  const q = [s];
  for (let h = 0; h < q.length; h++)
    for (const v of adj[q[h]]) if (seen[v] !== s) { seen[v] = s; q.push(v); }
  total += q.length - 1;
}
console.log(total);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)

total = 0
for s in range(1, n + 1):
    seen = [False] * (n + 1)
    seen[s] = True
    q = [s]
    for u in q:
        for v in adj[u]:
            if not seen[v]:
                seen[v] = True
                q.append(v)
    total += len(q) - 1
print(total)
CODE,
        ],
        'editorial' => '<p>Ini <strong>transitive closure</strong>: untuk setiap pasangan, apakah ada jalur. Floyd-Warshall bisa dipakai dengan nilai benar/salah: <code>reach[i][j] |= reach[i][k] &amp;&amp; reach[k][j]</code>, dengan k sebagai perantara di loop paling luar. Total O(N³) = 6,4 · 10<sup>7</sup> operasi ringan.</p>
<p>Alternatif: BFS dari setiap halte, O(N · (N + M)) ≈ 2 · 10<sup>6</sup>, lebih cepat jika graph-nya jarang. Kode C++ di atas memakai Floyd; JavaScript dan Python memakai BFS.</p>
<p><strong>Lanjutan:</strong> dengan <code>bitset</code>, baris i cukup di-OR dengan baris k: <code>if (reach[i][k]) reach[i] |= reach[k]</code>, 64 kali lebih cepat.</p>',
        'hints' => [
            'Untuk setiap pasangan (a, b), apakah ada jalur dari a ke b? N kecil, jadi matriks N × N muat.',
            'Floyd-Warshall bisa dipakai untuk nilai benar/salah: a bisa ke b jika a bisa ke k dan k bisa ke b.',
            'Atau jalankan BFS dari setiap halte dan hitung berapa halte yang tercapai.',
        ],
        'sample_visual' => 'digraph',
    ],

    [
        'slug' => 'kota-pusat',
        'lesson' => 'floyd-warshall',
        'title' => 'Lokasi Rumah Sakit',
        'difficulty' => 'Sedang',
        'tags' => ['floyd-warshall', 'pusat graph'],
        'statement' => '<p>Ada <strong>N</strong> kota yang terhubung <strong>M</strong> jalan dua arah berbobot. Sebuah rumah sakit rujukan akan dibangun di salah satu kota. Agar adil, pilih kota yang membuat <strong>jarak ke kota terjauh sekecil mungkin</strong>. Jika ada beberapa, pilih nomor terkecil.</p>
<p>Dijamin semua kota terhubung.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v w</code>.</p>',
        'output_format' => '<p>Dua bilangan: nomor kota terpilih dan jarak ke kota terjauh darinya.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200</li><li>N − 1 ≤ M ≤ 5000</li><li>1 ≤ w ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4 4\n1 2 3\n2 3 4\n3 4 2\n1 4 10\n", 'explanation' => 'Dari kota 2, jarak terjauh adalah ke kota 4: 4 + 2 = 6. Kota lain punya jarak terjauh yang lebih besar (kota 3: 7, kota 1: 9, kota 4: 9).'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $maxW) {
                $edges = T::edges($n, min($m, intdiv($n * ($n - 1), 2)), false, true, [1, $maxW]);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "2 1\n1 2 5\n", $mk(6, 8, 10), $mk(50, 120, 100), $mk(200, 5000, 1000000), $mk(200, 199, 1000), $mk(200, 400, 3)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $INF = PHP_INT_MAX >> 2;
            $d = array_fill(1, $n, array_fill(1, $n, $INF));
            for ($i = 1; $i <= $n; $i++) {
                $d[$i][$i] = 0;
            }
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v, $w] = T::ints($lines[$i]);
                $d[$u][$v] = min($d[$u][$v], $w);
                $d[$v][$u] = min($d[$v][$u], $w);
            }
            for ($k = 1; $k <= $n; $k++) {
                $dk = $d[$k];
                for ($i = 1; $i <= $n; $i++) {
                    $dik = $d[$i][$k];
                    $row = &$d[$i];
                    foreach ($dk as $j => $kj) {
                        if ($dik + $kj < $row[$j]) {
                            $row[$j] = $dik + $kj;
                        }
                    }
                    unset($row);
                }
            }
            $best = 0;
            $bestVal = PHP_INT_MAX;
            for ($i = 1; $i <= $n; $i++) {
                $e = max($d[$i]);
                if ($e < $bestVal) {
                    $bestVal = $e;
                    $best = $i;
                }
            }

            return "$best $bestVal";
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    const long long INF = 1e18;\n    vector<vector<long long>> d(n + 1, vector<long long>(n + 1, INF));\n    for (int i = 1; i <= n; i++) d[i][i] = 0;\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        long long w;\n        cin >> u >> v >> w;\n        d[u][v] = min(d[u][v], w);\n        d[v][u] = min(d[v][u], w);\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, m;
    cin >> n >> m;
    const long long INF = 1e18;
    vector<vector<long long>> d(n + 1, vector<long long>(n + 1, INF));
    for (int i = 1; i <= n; i++) d[i][i] = 0;
    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        d[u][v] = min(d[u][v], w);
        d[v][u] = min(d[v][u], w);
    }
    // Jarak semua pasangan
    for (int k = 1; k <= n; k++)
        for (int i = 1; i <= n; i++)
            for (int j = 1; j <= n; j++)
                if (d[i][k] + d[k][j] < d[i][j]) d[i][j] = d[i][k] + d[k][j];

    // Eksentrisitas kota i = jarak ke kota terjauh darinya; pilih yang terkecil
    int kota = 1;
    long long terbaik = INF;
    for (int i = 1; i <= n; i++) {
        long long terjauh = *max_element(d[i].begin() + 1, d[i].end());
        if (terjauh < terbaik) {
            terbaik = terjauh;
            kota = i;
        }
    }
    cout << kota << ' ' << terbaik << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const d = Array.from({ length: n + 1 }, () => new Array(n + 1).fill(Infinity));
for (let i = 1; i <= n; i++) d[i][i] = 0;
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  if (w < d[u][v]) d[u][v] = d[v][u] = w;
}
for (let k = 1; k <= n; k++) {
  const dk = d[k];
  for (let i = 1; i <= n; i++) {
    const dik = d[i][k], di = d[i];
    for (let j = 1; j <= n; j++) if (dik + dk[j] < di[j]) di[j] = dik + dk[j];
  }
}
let kota = 1, terbaik = Infinity;
for (let i = 1; i <= n; i++) {
  let t = 0;
  for (let j = 1; j <= n; j++) t = Math.max(t, d[i][j]);
  if (t < terbaik) { terbaik = t; kota = i; }
}
console.log(kota + " " + terbaik);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
INF = float("inf")
d = [[INF] * (n + 1) for _ in range(n + 1)]
for i in range(1, n + 1):
    d[i][i] = 0
for _ in range(m):
    u, v, w = map(int, input().split())
    if w < d[u][v]:
        d[u][v] = d[v][u] = w

for k in range(1, n + 1):
    dk = d[k]
    for i in range(1, n + 1):
        dik = d[i][k]
        if dik == INF:
            continue
        d[i] = [a if a <= dik + b else dik + b for a, b in zip(d[i], dk)]

kota, terbaik = 1, INF
for i in range(1, n + 1):
    t = max(d[i][1:])
    if t < terbaik:
        terbaik, kota = t, i
print(kota, terbaik)
CODE,
        ],
        'editorial' => '<p>Hitung jarak semua pasangan dengan <strong>Floyd-Warshall</strong> (N ≤ 200 → 8 · 10<sup>6</sup> operasi). Untuk setiap kota i, <em>eksentrisitas</em>-nya adalah jarak ke kota terjauh: <code>max_j d[i][j]</code>. Kota dengan eksentrisitas terkecil disebut <strong>pusat graph</strong>; pilih yang nomornya terkecil jika seri (gunakan perbandingan <code>&lt;</code>, bukan <code>≤</code>).</p>
<p>Perhatikan jalan ganda antar kota yang sama: ambil yang terpendek saat mengisi matriks awal.</p>
<p><strong>Kompleksitas:</strong> O(N³).</p>',
        'hints' => [
            'Untuk setiap calon lokasi, kita perlu jarak ke semua kota lain. Algoritma mana yang memberi jarak semua pasangan?',
            'Setelah Floyd-Warshall, jarak terjauh dari kota i adalah maksimum baris d[i].',
            'Pilih kota dengan nilai maksimum terkecil; jika seri, nomor terkecil.',
        ],
        'sample_visual' => 'wgraph',
    ],

    // ═════════════════════════════ LCA ═════════════════════════════
    [
        'slug' => 'leluhur-ke-k',
        'lesson' => 'lca',
        'title' => 'Leluhur ke-K',
        'difficulty' => 'Sedang',
        'tags' => ['pohon', 'binary lifting'],
        'statement' => '<p>Silsilah sebuah kerajaan berbentuk pohon dengan <strong>N</strong> anggota; anggota 1 adalah raja pertama (akar). Orang tua setiap anggota lain diketahui.</p>
<p>Jawab <strong>Q</strong> pertanyaan: siapa leluhur ke-<code>k</code> dari anggota <code>v</code>? Leluhur ke-1 adalah orang tua, ke-2 adalah kakek/nenek, dan seterusnya. Jika tidak ada, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi N − 1 bilangan: orang tua anggota 2, 3, …, N. Q baris berikutnya berisi <code>v k</code>.</p>',
        'output_format' => '<p>Q baris jawaban.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>1 ≤ k ≤ N</li><li>Orang tua anggota i tidak harus bernomor lebih kecil dari i.</li></ul>',
        'samples' => [
            ['input' => "6 4\n1 1 2 4 3\n5 1\n5 3\n5 4\n6 2\n", 'explanation' => 'Rantai leluhur 5: 4, 2, 1. Leluhur ke-4 tidak ada. Leluhur ke-2 dari 6 adalah 1 (orang tua 6 adalah 3).'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $q, string $shape) {
                $perm = range(2, $n);
                T::shuffle($perm);
                $order = array_merge([1], $perm);
                $par = array_fill(0, $n + 1, 0);
                for ($i = 1; $i < $n; $i++) {
                    $p = $shape === 'garis' ? $i - 1 : mt_rand(max(0, $i - 5), $i - 1);
                    $par[$order[$i]] = $order[$p];
                }
                $out = ["$n $q", implode(' ', array_slice($par, 2))];
                for ($i = 0; $i < $q; $i++) {
                    $out[] = mt_rand(1, $n).' '.mt_rand(1, $shape === 'garis' ? $n : min($n, 40));
                }

                return implode("\n", $out)."\n";
            };

            return ["1 1\n\n1 1\n", "2 2\n1\n2 1\n2 2\n", $mk(10, 20, 'acak'), $mk(1000, 1000, 'acak'), $mk(200000, 200000, 'acak'), $mk(200000, 200000, 'garis')];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $par = array_fill(0, $n + 1, 0);
            if ($n > 1) {
                foreach (T::ints($lines[1]) as $i => $p) {
                    $par[$i + 2] = $p;
                }
            }
            $LOG = 18;
            $up = [$par];
            for ($k = 1; $k < $LOG; $k++) {
                $prev = $up[$k - 1];
                $cur = array_fill(0, $n + 1, 0);
                for ($v = 1; $v <= $n; $v++) {
                    $cur[$v] = $prev[$prev[$v]];
                }
                $up[] = $cur;
            }
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$v, $k] = T::ints($lines[2 + $i]);
                for ($b = 0; $b < $LOG && $v; $b++) {
                    if ($k >> $b & 1) {
                        $v = $up[$b][$v];
                    }
                }
                if ($k >= (1 << $LOG)) {
                    $v = 0;
                }
                $out[] = $v ?: -1;
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<int> par(n + 1, 0);   // par[1] = 0 (tidak punya orang tua)\n    for (int i = 2; i <= n; i++) cin >> par[i];"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    const int LOG = 18;                 // 2^18 > 200 000
    // up[j][v] = leluhur ke-2^j dari v (0 jika tidak ada)
    vector<vector<int>> up(LOG, vector<int>(n + 1, 0));
    for (int i = 2; i <= n; i++) cin >> up[0][i];
    for (int j = 1; j < LOG; j++)
        for (int v = 1; v <= n; v++)
            up[j][v] = up[j - 1][up[j - 1][v]];   // up[..][0] = 0, jadi "tidak ada" menular

    while (q--) {
        int v, k;
        cin >> v >> k;
        // Pecah k menjadi pangkat dua: k = 13 = 8 + 4 + 1
        for (int j = 0; j < LOG && v != 0; j++)
            if (k >> j & 1) v = up[j][v];
        cout << (v == 0 ? -1 : v) << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const LOG = 18;
const up = Array.from({ length: LOG }, () => new Int32Array(n + 1));
if (n > 1) {
  const p = readInts();
  for (let i = 2; i <= n; i++) up[0][i] = p[i - 2];
} else readLine();
for (let j = 1; j < LOG; j++)
  for (let v = 1; v <= n; v++) up[j][v] = up[j - 1][up[j - 1][v]];
const out = [];
for (let i = 0; i < q; i++) {
  let [v, k] = readInts();
  for (let j = 0; j < LOG && v !== 0; j++) if ((k >> j) & 1) v = up[j][v];
  out.push(v === 0 ? -1 : v);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
line = input().split()
LOG = 18
up = [[0] * (n + 1)]
for i, p in enumerate(line):
    up[0][i + 2] = int(p)
for j in range(1, LOG):
    prev = up[j - 1]
    up.append([prev[prev[v]] for v in range(n + 1)])

out = []
for _ in range(q):
    v, k = map(int, input().split())
    j = 0
    while k and v:
        if k & 1:
            v = up[j][v]
        k >>= 1
        j += 1
    out.append(v if v else -1)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Naik satu per satu butuh O(k) per pertanyaan, terlalu lambat untuk pohon berbentuk garis. Gunakan <strong>binary lifting</strong>: <code>up[j][v]</code> = leluhur ke-2<sup>j</sup> dari v, dengan <code>up[j][v] = up[j−1][ up[j−1][v] ]</code>.</p>
<p>Untuk menjawab, tulis k dalam biner dan lompat untuk setiap bit yang menyala. Contoh k = 13 = 8 + 4 + 1: tiga lompatan. Gunakan 0 sebagai "tidak ada leluhur"; karena <code>up[j][0] = 0</code>, nilai 0 menular dengan sendirinya.</p>
<p><strong>Kompleksitas:</strong> O((N + Q) log N).</p>',
        'hints' => [
            'Naik satu per satu bisa butuh 200 000 langkah per pertanyaan. Bagaimana jika kita bisa melompat jauh?',
            'Siapkan up[j][v] = leluhur ke-2^j dari v.',
            'Tulis k dalam biner; untuk setiap bit j yang menyala, v = up[j][v].',
        ],
    ],

    [
        'slug' => 'jarak-berbobot',
        'lesson' => 'lca',
        'title' => 'Jarak Kabel Fiber',
        'difficulty' => 'Sedang',
        'tags' => ['pohon', 'lca', 'jarak'],
        'statement' => '<p>Jaringan fiber optik menghubungkan <strong>N</strong> gedung dengan N − 1 kabel berbentuk pohon. Kabel ke-i antara gedung <code>u</code> dan <code>v</code> panjangnya <code>w</code> meter.</p>
<p>Jawab <strong>Q</strong> pertanyaan: berapa panjang total kabel di jalur antara gedung <code>a</code> dan <code>b</code>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. N − 1 baris berikutnya berisi <code>u v w</code>. Q baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>Q baris jawaban.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 100 000</li><li>1 ≤ w ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5 3\n1 2 4\n1 3 2\n3 4 7\n3 5 1\n2 4\n4 5\n1 1\n", 'explanation' => 'Jalur 2 – 1 – 3 – 4: 4 + 2 + 7 = 13. Jalur 4 – 3 – 5: 7 + 1 = 8.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $q, bool $line) {
                $perm = range(1, $n);
                T::shuffle($perm);
                $out = ["$n $q"];
                for ($i = 1; $i < $n; $i++) {
                    $p = $line ? $i - 1 : mt_rand(max(0, $i - 10), $i - 1);
                    $out[] = $perm[$p].' '.$perm[$i].' '.mt_rand(1, 1000000000);
                }
                for ($i = 0; $i < $q; $i++) {
                    $out[] = mt_rand(1, $n).' '.mt_rand(1, $n);
                }

                return implode("\n", $out)."\n";
            };

            return ["1 1\n1 1\n", $mk(8, 10, false), $mk(1000, 1000, false), $mk(100000, 100000, false), $mk(100000, 100000, true)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i < $n; $i++) {
                [$u, $v, $w] = T::ints($lines[$i]);
                $adj[$u][] = [$v, $w];
                $adj[$v][] = [$u, $w];
            }
            $LOG = 17;
            $depth = array_fill(0, $n + 1, -1);
            $dw = array_fill(0, $n + 1, 0);
            $up0 = array_fill(0, $n + 1, 1);
            $depth[1] = 0;
            $order = [1];
            for ($h = 0; $h < count($order); $h++) {
                $u = $order[$h];
                foreach ($adj[$u] as [$v, $w]) {
                    if ($depth[$v] === -1) {
                        $depth[$v] = $depth[$u] + 1;
                        $dw[$v] = $dw[$u] + $w;
                        $up0[$v] = $u;
                        $order[] = $v;
                    }
                }
            }
            $up = [$up0];
            for ($k = 1; $k < $LOG; $k++) {
                $prev = $up[$k - 1];
                $cur = [];
                for ($v = 0; $v <= $n; $v++) {
                    $cur[$v] = $prev[$prev[$v]];
                }
                $up[] = $cur;
            }
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$a, $b] = T::ints($lines[$n + $i]);
                $x = $a;
                $y = $b;
                if ($depth[$x] < $depth[$y]) {
                    [$x, $y] = [$y, $x];
                }
                $diff = $depth[$x] - $depth[$y];
                for ($k = 0; $k < $LOG; $k++) {
                    if ($diff >> $k & 1) {
                        $x = $up[$k][$x];
                    }
                }
                if ($x !== $y) {
                    for ($k = $LOG - 1; $k >= 0; $k--) {
                        if ($up[$k][$x] !== $up[$k][$y]) {
                            $x = $up[$k][$x];
                            $y = $up[$k][$y];
                        }
                    }
                    $x = $up[0][$x];
                }
                $out[] = $dw[$a] + $dw[$b] - 2 * $dw[$x];
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<vector<pair<int, long long>>> adj(n + 1);\n    for (int i = 0; i < n - 1; i++) {\n        int u, v;\n        long long w;\n        cin >> u >> v >> w;\n        adj[u].push_back({v, w});\n        adj[v].push_back({u, w});\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<vector<pair<int, long long>>> adj(n + 1);
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w});
        adj[v].push_back({u, w});
    }

    const int LOG = 17;
    vector<int> depth(n + 1, -1);
    vector<long long> dw(n + 1, 0);     // dw[v] = panjang kabel dari akar 1 ke v
    vector<vector<int>> up(LOG, vector<int>(n + 1, 1));
    queue<int> bfs;
    depth[1] = 0;
    bfs.push(1);
    while (!bfs.empty()) {
        int u = bfs.front();
        bfs.pop();
        for (auto& e : adj[u]) {
            int v = e.first;
            if (depth[v] != -1) continue;
            depth[v] = depth[u] + 1;
            dw[v] = dw[u] + e.second;
            up[0][v] = u;
            bfs.push(v);
        }
    }
    for (int j = 1; j < LOG; j++)
        for (int v = 1; v <= n; v++) up[j][v] = up[j - 1][up[j - 1][v]];

    auto lca = [&](int a, int b) {
        if (depth[a] < depth[b]) swap(a, b);
        int diff = depth[a] - depth[b];
        for (int j = 0; j < LOG; j++)
            if (diff >> j & 1) a = up[j][a];
        if (a == b) return a;
        for (int j = LOG - 1; j >= 0; j--)
            if (up[j][a] != up[j][b]) {
                a = up[j][a];
                b = up[j][b];
            }
        return up[0][a];
    };

    while (q--) {
        int a, b;
        cin >> a >> b;
        // Jalur a–b = (akar → a) + (akar → b) − 2 × (akar → LCA)
        cout << dw[a] + dw[b] - 2 * dw[lca(a, b)] << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
  adj[v].push([u, w]);
}
const LOG = 17;
const depth = new Int32Array(n + 1).fill(-1);
const dw = new Float64Array(n + 1);
const up = Array.from({ length: LOG }, () => new Int32Array(n + 1).fill(1));
depth[1] = 0;
const order = [1];
for (let h = 0; h < order.length; h++) {
  const u = order[h];
  for (const [v, w] of adj[u]) {
    if (depth[v] !== -1) continue;
    depth[v] = depth[u] + 1;
    dw[v] = dw[u] + w;
    up[0][v] = u;
    order.push(v);
  }
}
for (let j = 1; j < LOG; j++)
  for (let v = 1; v <= n; v++) up[j][v] = up[j - 1][up[j - 1][v]];
function lca(a, b) {
  if (depth[a] < depth[b]) [a, b] = [b, a];
  const diff = depth[a] - depth[b];
  for (let j = 0; j < LOG; j++) if ((diff >> j) & 1) a = up[j][a];
  if (a === b) return a;
  for (let j = LOG - 1; j >= 0; j--) if (up[j][a] !== up[j][b]) { a = up[j][a]; b = up[j][b]; }
  return up[0][a];
}
const out = [];
for (let i = 0; i < q; i++) {
  const [a, b] = readInts();
  out.push(dw[a] + dw[b] - 2 * dw[lca(a, b)]);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))
    adj[v].append((u, w))

LOG = 17
depth = [-1] * (n + 1)
dw = [0] * (n + 1)
up0 = [1] * (n + 1)
depth[1] = 0
order = [1]
for u in order:
    for v, w in adj[u]:
        if depth[v] == -1:
            depth[v] = depth[u] + 1
            dw[v] = dw[u] + w
            up0[v] = u
            order.append(v)
up = [up0]
for j in range(1, LOG):
    prev = up[-1]
    up.append([prev[prev[v]] for v in range(n + 1)])

def lca(a, b):
    if depth[a] < depth[b]:
        a, b = b, a
    diff = depth[a] - depth[b]
    j = 0
    while diff:
        if diff & 1:
            a = up[j][a]
        diff >>= 1
        j += 1
    if a == b:
        return a
    for j in range(LOG - 1, -1, -1):
        if up[j][a] != up[j][b]:
            a = up[j][a]
            b = up[j][b]
    return up[0][a]

out = []
for _ in range(q):
    a, b = map(int, input().split())
    out.append(dw[a] + dw[b] - 2 * dw[lca(a, b)])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Jadikan gedung 1 sebagai akar dan hitung <code>dw[v]</code> = panjang kabel dari akar ke v (dari atas ke bawah). Jalur a–b naik dari a sampai LCA(a, b), lalu turun ke b, sehingga:</p>
<p style="text-align:center"><code>jarak(a, b) = dw[a] + dw[b] − 2 · dw[LCA(a, b)]</code></p>
<p>LCA dihitung dengan binary lifting (perhatikan bahwa lompatan memakai <em>kedalaman</em> dalam jumlah sisi, bukan panjang kabel). Panjang bisa mencapai 10<sup>14</sup>: gunakan <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O((N + Q) log N).</p>',
        'hints' => [
            'Pilih akar. Simpan panjang kabel dari akar ke setiap gedung.',
            'Jalur a–b naik ke leluhur bersama terdekat lalu turun lagi.',
            'jarak = dw[a] + dw[b] − 2 · dw[LCA(a, b)], dengan LCA dari binary lifting.',
        ],
    ],

    // ═════════════════════════════ SCC ═════════════════════════════
    [
        'slug' => 'terhubung-kuat',
        'lesson' => 'scc',
        'title' => 'Rute Penerbangan Lengkap?',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'berarah', 'keterjangkauan'],
        'statement' => '<p>Sebuah maskapai melayani <strong>N</strong> kota dengan <strong>M</strong> rute <strong>satu arah</strong>. Maskapai mengklaim bahwa dari kota mana pun, penumpang bisa mencapai kota mana pun (mungkin dengan transit).</p>
<p>Jika klaim benar, cetak <code>YA</code>. Jika tidak, cetak <code>TIDAK</code> dan sebuah pasangan kota <code>a b</code> sehingga tidak ada rute dari a ke b, dengan aturan berikut agar jawabannya unik:</p>
<ul><li>Jika ada kota yang tidak bisa dicapai dari kota 1, cetak <code>1 v</code> dengan v terkecil.</li>
<li>Jika tidak, cetak <code>v 1</code> dengan v terkecil yang tidak bisa mencapai kota 1.</li></ul>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p><code>YA</code>, atau <code>TIDAK</code> diikuti baris berisi pasangan kota.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "4 5\n1 2\n2 3\n3 1\n4 2\n2 4\n", 'explanation' => 'Semua kota saling terjangkau.'],
            ['input' => "4 4\n1 2\n2 3\n3 1\n3 4\n", 'explanation' => 'Semua kota terjangkau dari 1, tetapi dari kota 4 tidak bisa kembali ke 1.'],
        ],
        'tests' => function () {
            $strong = function (int $n, int $m) {
                $perm = range(1, $n);
                T::shuffle($perm);
                $edges = [];
                for ($i = 0; $i < $n; $i++) {
                    $edges[] = [$perm[$i], $perm[($i + 1) % $n]];
                }
                for ($i = $n; $i < $m; $i++) {
                    $edges[] = [mt_rand(1, $n), mt_rand(1, $n)];
                }
                T::shuffle($edges);

                return T::graphInput("$n ".count($edges), $edges);
            };
            $rand = function (int $n, int $m) {
                $edges = T::edges($n, min($m, $n * ($n - 1)), true, true);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "2 1\n1 2\n", "2 1\n2 1\n", $strong(10, 15), $rand(10, 15), $strong(1000, 2000), $rand(1000, 3000), $strong(100000, 200000), $rand(100000, 200000)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            $radj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $adj[$a][] = $b;
                $radj[$b][] = $a;
            }
            $reach = function (array $g) use ($n) {
                $seen = array_fill(0, $n + 1, false);
                $seen[1] = true;
                $q = [1];
                for ($h = 0; $h < count($q); $h++) {
                    foreach ($g[$q[$h]] as $v) {
                        if (! $seen[$v]) {
                            $seen[$v] = true;
                            $q[] = $v;
                        }
                    }
                }

                return $seen;
            };
            $f = $reach($adj);
            for ($v = 1; $v <= $n; $v++) {
                if (! $f[$v]) {
                    return "TIDAK\n1 $v";
                }
            }
            $r = $reach($radj);
            for ($v = 1; $v <= $n; $v++) {
                if (! $r[$v]) {
                    return "TIDAK\n$v 1";
                }
            }

            return 'YA';
        },
        'starter' => $st($cppDigraph, $jsDigraph, $pyDigraph),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n, m;

// Kota mana saja yang bisa dicapai dari kota 1 di graph g?
vector<bool> jangkau(const vector<vector<int>>& g) {
    vector<bool> seen(n + 1, false);
    queue<int> q;
    seen[1] = true;
    q.push(1);
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        for (int v : g[u])
            if (!seen[v]) {
                seen[v] = true;
                q.push(v);
            }
    }
    return seen;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> m;
    vector<vector<int>> adj(n + 1), radj(n + 1);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        radj[b].push_back(a);       // graph terbalik
    }
    // Terhubung kuat <=> semua bisa dicapai DARI 1 dan semua bisa MENCAPAI 1
    vector<bool> dari1 = jangkau(adj);
    for (int v = 1; v <= n; v++)
        if (!dari1[v]) {
            cout << "TIDAK\n1 " << v << '\n';
            return 0;
        }
    vector<bool> ke1 = jangkau(radj);   // di graph terbalik: siapa yang bisa mencapai 1
    for (int v = 1; v <= n; v++)
        if (!ke1[v]) {
            cout << "TIDAK\n" << v << " 1\n";
            return 0;
        }
    cout << "YA\n";
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const radj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  radj[b].push(a);
}
function jangkau(g) {
  const seen = new Uint8Array(n + 1);
  seen[1] = 1;
  const q = [1];
  for (let h = 0; h < q.length; h++) for (const v of g[q[h]]) if (!seen[v]) { seen[v] = 1; q.push(v); }
  return seen;
}
const f = jangkau(adj);
let ans = "YA";
for (let v = 1; v <= n; v++) if (!f[v]) { ans = "TIDAK\n1 " + v; break; }
if (ans === "YA") {
  const r = jangkau(radj);
  for (let v = 1; v <= n; v++) if (!r[v]) { ans = "TIDAK\n" + v + " 1"; break; }
}
console.log(ans);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
radj = [[] for _ in range(n + 1)]
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    radj[b].append(a)

def jangkau(g):
    seen = [False] * (n + 1)
    seen[1] = True
    q = [1]
    for u in q:
        for v in g[u]:
            if not seen[v]:
                seen[v] = True
                q.append(v)
    return seen

f = jangkau(adj)
for v in range(1, n + 1):
    if not f[v]:
        print("TIDAK")
        print(1, v)
        sys.exit()
r = jangkau(radj)
for v in range(1, n + 1):
    if not r[v]:
        print("TIDAK")
        print(v, 1)
        sys.exit()
print("YA")
CODE,
        ],
        'editorial' => '<p>Graph berarah <strong>terhubung kuat</strong> jika setiap kota bisa mencapai setiap kota lain. Cukup periksa satu kota acuan (kota 1):</p>
<ul><li>Semua kota bisa dicapai <strong>dari</strong> 1: BFS di graph asli.</li>
<li>Semua kota bisa <strong>mencapai</strong> 1: BFS dari 1 di graph <strong>terbalik</strong>.</li></ul>
<p>Jika keduanya benar, untuk sembarang a dan b ada jalur a → 1 → b. Jika salah satunya gagal, kota yang tidak tercapai langsung memberi pasangan yang diminta. Ini sama dengan memeriksa apakah banyak SCC = 1.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            'Memeriksa setiap pasangan kota terlalu lambat. Cukupkah memeriksa terhadap satu kota saja?',
            'Jika semua kota bisa dicapai dari kota 1 dan semua kota bisa mencapai kota 1, maka semua pasangan saling terjangkau.',
            '"Semua bisa mencapai 1" diperiksa dengan BFS dari 1 di graph yang semua sisinya dibalik.',
        ],
        'sample_visual' => 'digraph',
    ],

    [
        'slug' => 'hitung-scc',
        'lesson' => 'scc',
        'title' => 'Kerajaan-Kerajaan',
        'difficulty' => 'Sedang',
        'tags' => ['scc', 'kosaraju'],
        'statement' => '<p>Ada <strong>N</strong> kota dan <strong>M</strong> jalan satu arah. Dua kota termasuk <strong>kerajaan</strong> yang sama jika masing-masing bisa mencapai yang lain. Setiap kota termasuk tepat satu kerajaan.</p>
<p>Cetak banyak kerajaan, lalu nomor kerajaan setiap kota. Penomoran: kerajaan yang berisi kota 1 bernomor 1; kerajaan berikutnya adalah yang berisi kota bernomor terkecil yang belum bernomor, dan seterusnya.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>Baris pertama: banyak kerajaan K. Baris kedua: N bilangan, nomor kerajaan kota 1..N.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "6 7\n1 2\n2 1\n3 4\n4 5\n5 3\n2 3\n6 6\n", 'explanation' => 'Kerajaan: {1, 2}, {3, 4, 5}, {6}.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m) {
                $edges = T::edges($n, min($m, $n * ($n - 1)), true);

                return T::graphInput("$n ".count($edges), $edges);
            };
            $clusters = function (int $n, int $k, int $extra) {
                $perm = range(1, $n);
                T::shuffle($perm);
                $edges = [];
                $groups = array_chunk($perm, max(1, intdiv($n, $k)));
                foreach ($groups as $g) {
                    $c = count($g);
                    for ($i = 0; $i < $c && $c > 1; $i++) {
                        $edges[] = [$g[$i], $g[($i + 1) % $c]];
                    }
                }
                for ($i = 0; $i < $extra; $i++) {
                    $a = mt_rand(0, count($groups) - 2);
                    $b = mt_rand($a + 1, count($groups) - 1);
                    $edges[] = [$groups[$a][array_rand($groups[$a])], $groups[$b][array_rand($groups[$b])]];
                }
                T::shuffle($edges);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "3 0\n", $mk(8, 12), $clusters(20, 4, 10), $mk(1000, 2000), $clusters(1000, 30, 300), $mk(100000, 200000), $clusters(100000, 1000, 50000), $clusters(100000, 5, 20)];
        },
        'solve' => function (string $input) use ($sccPhp) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $adj[$a][] = $b;
            }
            [$c, $comp] = $sccPhp($n, $adj);
            $label = [];
            $out = [];
            for ($v = 1; $v <= $n; $v++) {
                $label[$comp[$v]] ??= count($label) + 1;
                $out[] = $label[$comp[$v]];
            }

            return $c."\n".implode(' ', $out);
        },
        'starter' => $st($cppDigraph, $jsDigraph, $pyDigraph),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1), radj(n + 1);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        radj[b].push_back(a);
    }

    // Kosaraju tahap 1 (iteratif): urutan selesai DFS di graph asli
    vector<int> urutan, idx(n + 1, 0);
    vector<bool> vis(n + 1, false);
    for (int s = 1; s <= n; s++) {
        if (vis[s]) continue;
        vector<int> st = {s};
        vis[s] = true;
        while (!st.empty()) {
            int u = st.back();
            if (idx[u] < (int)adj[u].size()) {
                int v = adj[u][idx[u]++];
                if (!vis[v]) {
                    vis[v] = true;
                    st.push_back(v);
                }
            } else {
                urutan.push_back(u);    // u selesai
                st.pop_back();
            }
        }
    }
    // Tahap 2: DFS di graph terbalik, urutan selesai dari belakang
    vector<int> komp(n + 1, -1);
    int c = 0;
    for (int i = n - 1; i >= 0; i--) {
        int s = urutan[i];
        if (komp[s] != -1) continue;
        vector<int> st = {s};
        komp[s] = c;
        while (!st.empty()) {
            int u = st.back();
            st.pop_back();
            for (int v : radj[u])
                if (komp[v] == -1) {
                    komp[v] = c;
                    st.push_back(v);
                }
        }
        c++;
    }
    // Penomoran ulang sesuai urutan kota terkecil
    vector<int> label(c, 0);
    int berikut = 0;
    cout << c << '\n';
    for (int v = 1; v <= n; v++) {
        if (!label[komp[v]]) label[komp[v]] = ++berikut;
        cout << label[komp[v]] << (v < n ? ' ' : '\n');
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const radj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  radj[b].push(a);
}
const urutan = [], idx = new Int32Array(n + 1), vis = new Uint8Array(n + 1);
for (let s = 1; s <= n; s++) {
  if (vis[s]) continue;
  const st = [s];
  vis[s] = 1;
  while (st.length) {
    const u = st[st.length - 1];
    if (idx[u] < adj[u].length) {
      const v = adj[u][idx[u]++];
      if (!vis[v]) { vis[v] = 1; st.push(v); }
    } else { urutan.push(u); st.pop(); }
  }
}
const komp = new Int32Array(n + 1).fill(-1);
let c = 0;
for (let i = n - 1; i >= 0; i--) {
  const s = urutan[i];
  if (komp[s] !== -1) continue;
  const st = [s];
  komp[s] = c;
  while (st.length) {
    const u = st.pop();
    for (const v of radj[u]) if (komp[v] === -1) { komp[v] = c; st.push(v); }
  }
  c++;
}
const label = new Int32Array(c);
let next = 0;
const out = [];
for (let v = 1; v <= n; v++) {
  if (!label[komp[v]]) label[komp[v]] = ++next;
  out.push(label[komp[v]]);
}
console.log(c + "\n" + out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
radj = [[] for _ in range(n + 1)]
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    radj[b].append(a)

urutan = []
idx = [0] * (n + 1)
vis = [False] * (n + 1)
for s in range(1, n + 1):
    if vis[s]:
        continue
    vis[s] = True
    st = [s]
    while st:
        u = st[-1]
        if idx[u] < len(adj[u]):
            v = adj[u][idx[u]]
            idx[u] += 1
            if not vis[v]:
                vis[v] = True
                st.append(v)
        else:
            urutan.append(u)
            st.pop()

komp = [-1] * (n + 1)
c = 0
for s in reversed(urutan):
    if komp[s] != -1:
        continue
    komp[s] = c
    st = [s]
    while st:
        u = st.pop()
        for v in radj[u]:
            if komp[v] == -1:
                komp[v] = c
                st.append(v)
    c += 1

label = [0] * c
nxt = 0
out = []
for v in range(1, n + 1):
    if not label[komp[v]]:
        nxt += 1
        label[komp[v]] = nxt
    out.append(label[komp[v]])
print(c)
print(*out)
CODE,
        ],
        'editorial' => '<p>Kerajaan adalah <strong>komponen terhubung kuat</strong>. Gunakan Kosaraju: DFS di graph asli untuk urutan selesai, lalu DFS di graph terbalik mengikuti urutan selesai dari belakang; setiap DFS kedua menemukan satu komponen.</p>
<p>Nomor komponen dari Kosaraju belum tentu sesuai aturan soal, jadi lakukan <strong>penomoran ulang</strong>: telusuri kota 1..N, dan setiap kali bertemu komponen yang belum diberi label, beri label berikutnya.</p>
<p>Untuk N = 10<sup>5</sup>, DFS ditulis iteratif (stack manual + indeks tetangga) agar tidak stack overflow. <strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            '"Saling bisa mencapai" adalah definisi komponen terhubung kuat (SCC).',
            'Kosaraju: urutan selesai DFS di graph asli, lalu DFS di graph terbalik dari yang selesai paling akhir.',
            'Nomor dari algoritma perlu dipetakan ulang: telusuri kota 1..N dan beri label baru saat bertemu komponen baru.',
        ],
        'sample_visual' => 'digraph',
    ],

    [
        'slug' => 'kumpul-koin',
        'lesson' => 'scc',
        'title' => 'Petualangan Pengumpul Koin',
        'difficulty' => 'Sulit',
        'tags' => ['scc', 'kondensasi', 'dp dag'],
        'statement' => '<p>Sebuah dunia game punya <strong>N</strong> ruangan dan <strong>M</strong> lorong satu arah. Ruangan i berisi <code>k<sub>i</sub></code> koin. Pemain boleh mulai dan berhenti di ruangan mana pun, berjalan mengikuti lorong, dan mengunjungi ruangan berkali-kali. Koin di sebuah ruangan hanya bisa diambil sekali.</p>
<p>Berapa koin terbanyak yang bisa dikumpulkan?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. Baris kedua berisi k<sub>1</sub> … k<sub>N</sub>. M baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>Koin maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li><li>1 ≤ k<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 4\n4 5 2 7\n1 2\n2 1\n1 3\n4 3\n", 'explanation' => 'Berputar di ruangan 1 dan 2 (4 + 5), lalu ke ruangan 3 (2): total 11. Ruangan 4 (7) + 3 (2) hanya 9.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, bool $clustered) {
                $coins = [];
                for ($i = 0; $i < $n; $i++) {
                    $coins[] = mt_rand(1, 1000000000);
                }
                if ($clustered) {
                    $perm = range(1, $n);
                    T::shuffle($perm);
                    $groups = array_chunk($perm, 5);
                    $edges = [];
                    foreach ($groups as $g) {
                        for ($i = 0; $i < count($g) && count($g) > 1; $i++) {
                            $edges[] = [$g[$i], $g[($i + 1) % count($g)]];
                        }
                    }
                    for ($i = count($edges); $i < $m; $i++) {
                        $a = mt_rand(0, count($groups) - 2);
                        $b = mt_rand($a + 1, min(count($groups) - 1, $a + 20));
                        $edges[] = [$groups[$a][array_rand($groups[$a])], $groups[$b][array_rand($groups[$b])]];
                    }
                    T::shuffle($edges);
                } else {
                    $edges = T::edges($n, min($m, $n * ($n - 1)), true);
                }

                return "$n ".count($edges)."\n".implode(' ', $coins)."\n".implode("\n", array_map(fn ($e) => "{$e[0]} {$e[1]}", $edges))."\n";
            };

            return ["1 0\n5\n", "2 1\n3 4\n2 1\n", $mk(8, 10, false), $mk(1000, 2000, true), $mk(1000, 1500, false), $mk(100000, 200000, true), $mk(100000, 120000, false)];
        },
        'solve' => function (string $input) use ($sccPhp) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $coin = array_merge([0], T::ints($lines[1]));
            $adj = array_fill(0, $n + 1, []);
            $edges = [];
            for ($i = 0; $i < $m; $i++) {
                [$a, $b] = T::ints($lines[2 + $i]);
                $adj[$a][] = $b;
                $edges[] = [$a, $b];
            }
            [$c, $comp] = $sccPhp($n, $adj);
            $val = array_fill(0, $c, 0);
            for ($v = 1; $v <= $n; $v++) {
                $val[$comp[$v]] += $coin[$v];
            }
            $dag = array_fill(0, $c, []);
            foreach ($edges as [$a, $b]) {
                if ($comp[$a] !== $comp[$b]) {
                    $dag[$comp[$a]][] = $comp[$b];
                }
            }
            // Kosaraju memberi nomor komponen dalam urutan topologis (sisi selalu ke nomor lebih besar)
            $best = array_fill(0, $c, 0);
            $ans = 0;
            for ($k = $c - 1; $k >= 0; $k--) {
                $b = 0;
                foreach ($dag[$k] as $j) {
                    $b = max($b, $best[$j]);
                }
                $best[$k] = $val[$k] + $b;
                $ans = max($ans, $best[$k]);
            }

            return (string) $ans;
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<long long> koin(n + 1);\n    for (int i = 1; i <= n; i++) cin >> koin[i];\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int a, b;\n        cin >> a >> b;\n        adj[a].push_back(b);\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<long long> koin(n + 1);
    for (int i = 1; i <= n; i++) cin >> koin[i];
    vector<vector<int>> adj(n + 1), radj(n + 1);
    vector<pair<int, int>> sisi(m);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        radj[b].push_back(a);
        sisi[i] = {a, b};
    }

    // 1) SCC dengan Kosaraju (iteratif)
    vector<int> urutan, idx(n + 1, 0);
    vector<bool> vis(n + 1, false);
    for (int s = 1; s <= n; s++) {
        if (vis[s]) continue;
        vector<int> st = {s};
        vis[s] = true;
        while (!st.empty()) {
            int u = st.back();
            if (idx[u] < (int)adj[u].size()) {
                int v = adj[u][idx[u]++];
                if (!vis[v]) {
                    vis[v] = true;
                    st.push_back(v);
                }
            } else {
                urutan.push_back(u);
                st.pop_back();
            }
        }
    }
    vector<int> komp(n + 1, -1);
    int c = 0;
    for (int i = n - 1; i >= 0; i--) {
        int s = urutan[i];
        if (komp[s] != -1) continue;
        vector<int> st = {s};
        komp[s] = c;
        while (!st.empty()) {
            int u = st.back();
            st.pop_back();
            for (int v : radj[u])
                if (komp[v] == -1) {
                    komp[v] = c;
                    st.push_back(v);
                }
        }
        c++;
    }

    // 2) Graph kondensasi: satu komponen = satu simpul bernilai jumlah koinnya
    vector<long long> nilai(c, 0);
    for (int v = 1; v <= n; v++) nilai[komp[v]] += koin[v];
    vector<vector<int>> dag(c);
    for (auto& e : sisi)
        if (komp[e.first] != komp[e.second]) dag[komp[e.first]].push_back(komp[e.second]);

    // 3) DP jalur bernilai terbesar di DAG. Kosaraju memberi nomor dalam urutan
    //    topologis (sisi selalu menuju nomor yang lebih besar), jadi proses dari belakang.
    vector<long long> best(c, 0);
    long long jawaban = 0;
    for (int k = c - 1; k >= 0; k--) {
        long long lanjut = 0;
        for (int j : dag[k]) lanjut = max(lanjut, best[j]);
        best[k] = nilai[k] + lanjut;
        jawaban = max(jawaban, best[k]);
    }
    cout << jawaban << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const koin = [0, ...readInts()];
const adj = Array.from({ length: n + 1 }, () => []);
const radj = Array.from({ length: n + 1 }, () => []);
const sisi = [];
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  radj[b].push(a);
  sisi.push([a, b]);
}
const urutan = [], idx = new Int32Array(n + 1), vis = new Uint8Array(n + 1);
for (let s = 1; s <= n; s++) {
  if (vis[s]) continue;
  const st = [s];
  vis[s] = 1;
  while (st.length) {
    const u = st[st.length - 1];
    if (idx[u] < adj[u].length) {
      const v = adj[u][idx[u]++];
      if (!vis[v]) { vis[v] = 1; st.push(v); }
    } else { urutan.push(u); st.pop(); }
  }
}
const komp = new Int32Array(n + 1).fill(-1);
let c = 0;
for (let i = n - 1; i >= 0; i--) {
  const s = urutan[i];
  if (komp[s] !== -1) continue;
  const st = [s];
  komp[s] = c;
  while (st.length) {
    const u = st.pop();
    for (const v of radj[u]) if (komp[v] === -1) { komp[v] = c; st.push(v); }
  }
  c++;
}
// Koin bisa mencapai 10^14: masih aman untuk Number
const nilai = new Array(c).fill(0);
for (let v = 1; v <= n; v++) nilai[komp[v]] += koin[v];
const dag = Array.from({ length: c }, () => []);
for (const [a, b] of sisi) if (komp[a] !== komp[b]) dag[komp[a]].push(komp[b]);
const best = new Array(c).fill(0);
let jawaban = 0;
for (let k = c - 1; k >= 0; k--) {
  let lanjut = 0;
  for (const j of dag[k]) lanjut = Math.max(lanjut, best[j]);
  best[k] = nilai[k] + lanjut;
  jawaban = Math.max(jawaban, best[k]);
}
console.log(jawaban);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
koin = [0] + list(map(int, input().split()))
adj = [[] for _ in range(n + 1)]
radj = [[] for _ in range(n + 1)]
sisi = []
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    radj[b].append(a)
    sisi.append((a, b))

urutan = []
idx = [0] * (n + 1)
vis = [False] * (n + 1)
for s in range(1, n + 1):
    if vis[s]:
        continue
    vis[s] = True
    st = [s]
    while st:
        u = st[-1]
        if idx[u] < len(adj[u]):
            v = adj[u][idx[u]]
            idx[u] += 1
            if not vis[v]:
                vis[v] = True
                st.append(v)
        else:
            urutan.append(u)
            st.pop()

komp = [-1] * (n + 1)
c = 0
for s in reversed(urutan):
    if komp[s] != -1:
        continue
    komp[s] = c
    st = [s]
    while st:
        u = st.pop()
        for v in radj[u]:
            if komp[v] == -1:
                komp[v] = c
                st.append(v)
    c += 1

nilai = [0] * c
for v in range(1, n + 1):
    nilai[komp[v]] += koin[v]
dag = [[] for _ in range(c)]
for a, b in sisi:
    if komp[a] != komp[b]:
        dag[komp[a]].append(komp[b])
best = [0] * c
for k in range(c - 1, -1, -1):
    best[k] = nilai[k] + max((best[j] for j in dag[k]), default=0)
print(max(best))
CODE,
        ],
        'editorial' => '<p>Di dalam satu komponen kuat, pemain bisa berkeliling dan mengambil <strong>semua</strong> koin, lalu kembali ke mana pun dalam komponen itu. Jadi ciutkan setiap SCC menjadi satu simpul bernilai jumlah koinnya. Hasilnya <strong>DAG</strong> (graph kondensasi).</p>
<p>Di DAG, jawabannya jalur bernilai terbesar: <code>best[k] = nilai[k] + max(best[j])</code> untuk setiap sisi k → j. Urutan Kosaraju kebetulan sudah topologis: komponen ditemukan dari "hulu" ke "hilir", sehingga sisi selalu menuju nomor yang lebih besar dan DP cukup dijalankan dari nomor terbesar ke terkecil.</p>
<p>Koin total bisa mencapai 10<sup>14</sup>: <code>long long</code>. <strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            'Jika beberapa ruangan saling bisa dicapai, pemain bisa mengambil semua koinnya sekaligus. Struktur apa ini?',
            'Ciutkan setiap SCC menjadi satu simpul dengan nilai = jumlah koin. Graph hasilnya tidak punya siklus.',
            'Di DAG, cari jalur bernilai terbesar dengan DP dalam urutan topologis.',
        ],
        'sample_visual' => 'digraph',
    ],

    // ═════════════════════════════ Jembatan & titik artikulasi ═════════════════════════════
    [
        'slug' => 'jalan-kritis',
        'lesson' => 'jembatan',
        'title' => 'Jalan Kritis',
        'difficulty' => 'Sedang',
        'tags' => ['jembatan', 'tin low', 'dfs'],
        'statement' => '<p>Sebuah kabupaten punya <strong>N</strong> desa dan <strong>M</strong> jalan dua arah. Sebuah jalan disebut <strong>kritis</strong> jika putusnya jalan itu (misalnya karena longsor) membuat ada dua desa yang tadinya terhubung menjadi tidak bisa saling mencapai.</p>
<p>Cetak semua jalan kritis.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>. Tidak ada jalan ganda dan tidak ada jalan dari desa ke dirinya sendiri.</p>',
        'output_format' => '<p>Baris pertama: banyak jalan kritis K. K baris berikutnya: setiap jalan sebagai <code>a b</code> dengan a &lt; b, terurut naik (menurut a, lalu b).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "6 6\n1 2\n2 3\n3 1\n3 4\n4 5\n4 6\n", 'explanation' => 'Jalan di segitiga 1-2-3 punya jalan memutar. Jalan 3-4, 4-5, dan 4-6 kritis.'],
        ],
        'tests' => function () use ($bridgyGraph) {
            $g = function (int $n, int $extra) use ($bridgyGraph) {
                $edges = $bridgyGraph($n, $extra);

                return T::graphInput("$n ".count($edges), $edges);
            };
            $rand = function (int $n, int $m) {
                $edges = T::edges($n, min($m, intdiv($n * ($n - 1), 2)));

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "2 1\n1 2\n", "3 3\n1 2\n2 3\n3 1\n", $g(10, 4), $rand(10, 9), $g(1000, 400), $rand(1000, 1100), $g(100000, 40000), $g(100000, 100000), $rand(100000, 100000)];
        },
        'solve' => function (string $input) use ($lowlinkPhp) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            [$tin, $low, $par] = $lowlinkPhp($n, $adj);
            $res = [];
            for ($v = 1; $v <= $n; $v++) {
                $p = $par[$v];
                if ($p && $low[$v] > $tin[$p]) {
                    $res[] = [min($p, $v), max($p, $v)];
                }
            }
            sort($res);

            return count($res).($res ? "\n".implode("\n", array_map(fn ($e) => "{$e[0]} {$e[1]}", $res)) : '');
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }"),
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
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }

    // DFS iteratif dengan tin/low
    vector<int> tin(n + 1, -1), low(n + 1, 0), par(n + 1, 0), idx(n + 1, 0);
    vector<pair<int, int>> kritis;
    int timer = 0;
    for (int s = 1; s <= n; s++) {
        if (tin[s] != -1) continue;
        tin[s] = low[s] = timer++;
        vector<int> st = {s};
        while (!st.empty()) {
            int u = st.back();
            if (idx[u] < (int)adj[u].size()) {
                int v = adj[u][idx[u]++];
                if (v == par[u]) continue;            // sisi yang baru dipakai turun
                if (tin[v] != -1) {
                    low[u] = min(low[u], tin[v]);     // sisi balik
                } else {
                    par[v] = u;
                    tin[v] = low[v] = timer++;
                    st.push_back(v);
                }
            } else {
                st.pop_back();                        // u selesai, "kembali" ke orang tua
                int p = par[u];
                if (p != 0) {
                    low[p] = min(low[p], low[u]);
                    if (low[u] > tin[p]) kritis.push_back({min(p, u), max(p, u)});
                }
            }
        }
    }
    sort(kritis.begin(), kritis.end());
    cout << kritis.size() << '\n';
    for (auto& e : kritis) cout << e.first << ' ' << e.second << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const tin = new Int32Array(n + 1).fill(-1), low = new Int32Array(n + 1), par = new Int32Array(n + 1), idx = new Int32Array(n + 1);
const kritis = [];
let timer = 0;
for (let s = 1; s <= n; s++) {
  if (tin[s] !== -1) continue;
  tin[s] = low[s] = timer++;
  const st = [s];
  while (st.length) {
    const u = st[st.length - 1];
    if (idx[u] < adj[u].length) {
      const v = adj[u][idx[u]++];
      if (v === par[u]) continue;
      if (tin[v] !== -1) low[u] = Math.min(low[u], tin[v]);
      else { par[v] = u; tin[v] = low[v] = timer++; st.push(v); }
    } else {
      st.pop();
      const p = par[u];
      if (p) {
        low[p] = Math.min(low[p], low[u]);
        if (low[u] > tin[p]) kritis.push([Math.min(p, u), Math.max(p, u)]);
      }
    }
  }
}
kritis.sort((a, b) => a[0] - b[0] || a[1] - b[1]);
console.log([kritis.length, ...kritis.map((e) => e.join(" "))].join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

tin = [-1] * (n + 1)
low = [0] * (n + 1)
par = [0] * (n + 1)
idx = [0] * (n + 1)
kritis = []
timer = 0
for s in range(1, n + 1):
    if tin[s] != -1:
        continue
    tin[s] = low[s] = timer
    timer += 1
    st = [s]
    while st:
        u = st[-1]
        if idx[u] < len(adj[u]):
            v = adj[u][idx[u]]
            idx[u] += 1
            if v == par[u]:
                continue
            if tin[v] != -1:
                if tin[v] < low[u]:
                    low[u] = tin[v]
            else:
                par[v] = u
                tin[v] = low[v] = timer
                timer += 1
                st.append(v)
        else:
            st.pop()
            p = par[u]
            if p:
                if low[u] < low[p]:
                    low[p] = low[u]
                if low[u] > tin[p]:
                    kritis.append((min(p, u), max(p, u)))
kritis.sort()
print(len(kritis))
for a, b in kritis:
    print(a, b)
CODE,
        ],
        'editorial' => '<p>Jalan kritis adalah <strong>jembatan</strong>. Jalankan DFS sambil mencatat <code>tin[u]</code> (waktu masuk) dan <code>low[u]</code> (tin terkecil yang bisa dicapai dari subtree u lewat satu sisi balik). Sisi pohon p–u adalah jembatan jika <code>low[u] &gt; tin[p]</code>: subtree u tidak punya jalan lain untuk naik ke p atau leluhurnya.</p>
<p>Untuk N = 10<sup>5</sup>, DFS ditulis <strong>iteratif</strong>: simpan indeks tetangga berikutnya untuk setiap simpul, dan saat sebuah simpul selesai (di-pop), perbarui <code>low</code> orang tuanya lalu periksa kriteria jembatan. Ini persis langkah "setelah anak selesai" pada versi rekursif.</p>
<p><strong>Kompleksitas:</strong> O(N + M) ditambah pengurutan hasil.</p>',
        'hints' => [
            'Sebuah jalan kritis jika dan hanya jika jalan itu tidak berada di siklus mana pun. Bagaimana mendeteksinya dengan DFS?',
            'Catat tin (waktu masuk) dan low (tin terkecil yang bisa dicapai dari subtree lewat sisi balik).',
            'Sisi pohon p–u kritis jika low[u] > tin[p]. Untuk N besar, tulis DFS secara iteratif.',
        ],
        'sample_visual' => 'graph',
    ],

    [
        'slug' => 'server-kritis',
        'lesson' => 'jembatan',
        'title' => 'Server Kritis',
        'difficulty' => 'Sedang',
        'tags' => ['titik artikulasi', 'tin low', 'dfs'],
        'statement' => '<p>Jaringan kampus punya <strong>N</strong> server dan <strong>M</strong> kabel dua arah. Sebuah server disebut <strong>kritis</strong> jika matinya server itu (beserta semua kabelnya) membuat beberapa server lain yang tadinya terhubung menjadi terputus.</p>
<p>Cetak semua server kritis.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>. Tidak ada kabel ganda atau kabel ke diri sendiri.</p>',
        'output_format' => '<p>Baris pertama: banyak server kritis K. Baris kedua: nomor-nomornya terurut naik (baris kosong jika K = 0).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "7 8\n1 2\n2 3\n3 1\n3 4\n4 5\n5 6\n6 4\n6 7\n", 'explanation' => 'Mematikan server 3 memisahkan {1, 2} dari sisanya. Mematikan server 4 memisahkan segitiga 1-2-3 dari {5, 6, 7}. Mematikan server 6 memisahkan server 7.'],
        ],
        'tests' => function () use ($bridgyGraph) {
            $g = function (int $n, int $extra) use ($bridgyGraph) {
                $edges = $bridgyGraph($n, $extra);

                return T::graphInput("$n ".count($edges), $edges);
            };
            $rand = function (int $n, int $m) {
                $edges = T::edges($n, min($m, intdiv($n * ($n - 1), 2)));

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "3 2\n1 2\n2 3\n", "3 3\n1 2\n2 3\n3 1\n", $g(10, 4), $rand(10, 9), $g(1000, 400), $rand(1000, 1100), $g(100000, 40000), $g(100000, 100000), $rand(100000, 100000)];
        },
        'solve' => function (string $input) use ($lowlinkPhp) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            [$tin, $low, $par] = $lowlinkPhp($n, $adj);
            $art = array_fill(0, $n + 1, false);
            $rootKids = array_fill(0, $n + 1, 0);
            for ($v = 1; $v <= $n; $v++) {
                $p = $par[$v];
                if (! $p) {
                    continue;
                }
                if ($par[$p] === 0) {
                    $rootKids[$p]++;
                } elseif ($low[$v] >= $tin[$p]) {
                    $art[$p] = true;
                }
            }
            for ($v = 1; $v <= $n; $v++) {
                if ($par[$v] === 0 && $rootKids[$v] > 1) {
                    $art[$v] = true;
                }
            }
            $res = array_keys(array_filter($art));

            return count($res)."\n".implode(' ', $res);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }"),
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
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }

    vector<int> tin(n + 1, -1), low(n + 1, 0), par(n + 1, 0), idx(n + 1, 0), anakAkar(n + 1, 0);
    vector<bool> kritis(n + 1, false);
    int timer = 0;
    for (int s = 1; s <= n; s++) {
        if (tin[s] != -1) continue;
        tin[s] = low[s] = timer++;
        vector<int> st = {s};
        while (!st.empty()) {
            int u = st.back();
            if (idx[u] < (int)adj[u].size()) {
                int v = adj[u][idx[u]++];
                if (v == par[u]) continue;
                if (tin[v] != -1) {
                    low[u] = min(low[u], tin[v]);
                } else {
                    par[v] = u;
                    tin[v] = low[v] = timer++;
                    st.push_back(v);
                }
            } else {
                st.pop_back();
                int p = par[u];
                if (p == 0) continue;
                low[p] = min(low[p], low[u]);
                if (par[p] == 0) anakAkar[p]++;              // p adalah akar DFS
                else if (low[u] >= tin[p]) kritis[p] = true; // subtree u tak bisa melewati p
            }
        }
        if (anakAkar[s] > 1) kritis[s] = true;               // akar dengan ≥ 2 anak
    }
    vector<int> hasil;
    for (int v = 1; v <= n; v++) if (kritis[v]) hasil.push_back(v);
    cout << hasil.size() << '\n';
    for (int i = 0; i < (int)hasil.size(); i++) cout << hasil[i] << (i + 1 < (int)hasil.size() ? " " : "");
    cout << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const tin = new Int32Array(n + 1).fill(-1), low = new Int32Array(n + 1), par = new Int32Array(n + 1);
const idx = new Int32Array(n + 1), anakAkar = new Int32Array(n + 1), kritis = new Uint8Array(n + 1);
let timer = 0;
for (let s = 1; s <= n; s++) {
  if (tin[s] !== -1) continue;
  tin[s] = low[s] = timer++;
  const st = [s];
  while (st.length) {
    const u = st[st.length - 1];
    if (idx[u] < adj[u].length) {
      const v = adj[u][idx[u]++];
      if (v === par[u]) continue;
      if (tin[v] !== -1) low[u] = Math.min(low[u], tin[v]);
      else { par[v] = u; tin[v] = low[v] = timer++; st.push(v); }
    } else {
      st.pop();
      const p = par[u];
      if (!p) continue;
      low[p] = Math.min(low[p], low[u]);
      if (par[p] === 0) anakAkar[p]++;
      else if (low[u] >= tin[p]) kritis[p] = 1;
    }
  }
  if (anakAkar[s] > 1) kritis[s] = 1;
}
const hasil = [];
for (let v = 1; v <= n; v++) if (kritis[v]) hasil.push(v);
console.log(hasil.length + "\n" + hasil.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

tin = [-1] * (n + 1)
low = [0] * (n + 1)
par = [0] * (n + 1)
idx = [0] * (n + 1)
anak_akar = [0] * (n + 1)
kritis = [False] * (n + 1)
timer = 0
for s in range(1, n + 1):
    if tin[s] != -1:
        continue
    tin[s] = low[s] = timer
    timer += 1
    st = [s]
    while st:
        u = st[-1]
        if idx[u] < len(adj[u]):
            v = adj[u][idx[u]]
            idx[u] += 1
            if v == par[u]:
                continue
            if tin[v] != -1:
                if tin[v] < low[u]:
                    low[u] = tin[v]
            else:
                par[v] = u
                tin[v] = low[v] = timer
                timer += 1
                st.append(v)
        else:
            st.pop()
            p = par[u]
            if not p:
                continue
            if low[u] < low[p]:
                low[p] = low[u]
            if par[p] == 0:
                anak_akar[p] += 1
            elif low[u] >= tin[p]:
                kritis[p] = True
    if anak_akar[s] > 1:
        kritis[s] = True
hasil = [v for v in range(1, n + 1) if kritis[v]]
print(len(hasil))
print(*hasil)
CODE,
        ],
        'editorial' => '<p>Server kritis adalah <strong>titik artikulasi</strong>. Dengan DFS tin/low, simpul p (bukan akar) adalah titik artikulasi jika punya anak u di pohon DFS dengan <code>low[u] ≥ tin[p]</code>: subtree u tidak bisa naik melewati p, sehingga matinya p memutus subtree itu.</p>
<p>Akar DFS diperlakukan khusus: akar kritis jika punya <strong>lebih dari satu anak</strong> di pohon DFS.</p>
<p>Perhatikan perbedaan dengan jembatan: jembatan memakai <code>&gt;</code>, titik artikulasi memakai <code>≥</code>. <strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            'Server kritis = titik artikulasi. Gunakan DFS dengan tin dan low.',
            'Simpul p bukan akar kritis jika ada anak u dengan low[u] ≥ tin[p].',
            'Akar DFS kritis jika punya lebih dari satu anak di pohon DFS.',
        ],
        'sample_visual' => 'graph',
    ],

    [
        'slug' => 'jaringan-tangguh',
        'lesson' => 'jembatan',
        'title' => 'Jaringan Tahan Putus',
        'difficulty' => 'Sulit',
        'tags' => ['jembatan', 'pohon jembatan', 'komponen 2-edge-connected'],
        'statement' => '<p>Jaringan listrik <strong>N</strong> gardu dihubungkan <strong>M</strong> kabel dua arah, dan semua gardu sudah terhubung. PLN ingin jaringan <strong>tahan putus</strong>: putusnya kabel mana pun (satu saja) tidak boleh memisahkan gardu mana pun.</p>
<p>Berapa kabel baru paling sedikit yang harus ditambahkan (boleh antara dua gardu mana pun)?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>. Tidak ada kabel ganda atau kabel ke diri sendiri. Graph dijamin terhubung.</p>',
        'output_format' => '<p>Banyak kabel tambahan minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>N − 1 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "6 6\n1 2\n2 3\n3 1\n3 4\n4 5\n4 6\n", 'explanation' => 'Setelah segitiga 1-2-3 diciutkan, pohon jembatannya adalah: {1,2,3} – 4 – 5 dan 4 – 6, dengan 3 daun ({1,2,3}, 5, 6). Butuh (3 + 1) / 2 = 2 kabel, misalnya 5–6 dan 6–1.'],
            ['input' => "3 3\n1 2\n2 3\n3 1\n", 'explanation' => 'Sudah tidak ada jembatan.'],
        ],
        'tests' => function () use ($bridgyGraph) {
            $g = function (int $n, int $extra) use ($bridgyGraph) {
                $edges = $bridgyGraph($n, $extra);

                return T::graphInput("$n ".count($edges), $edges);
            };
            $tree = function (int $n) {
                $edges = T::edges($n, $n - 1, false, true);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "2 1\n1 2\n", "3 2\n1 2\n1 3\n", $g(10, 4), $tree(10), $g(1000, 300), $tree(1000), $g(100000, 30000), $g(100000, 120000), $tree(100000)];
        },
        'solve' => function (string $input) use ($lowlinkPhp) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            [$tin, $low, $par] = $lowlinkPhp($n, $adj);
            $isBridge = [];
            for ($v = 1; $v <= $n; $v++) {
                if ($par[$v] && $low[$v] > $tin[$par[$v]]) {
                    $isBridge[min($v, $par[$v]).'-'.max($v, $par[$v])] = true;
                }
            }
            if (! $isBridge) {
                return '0';
            }
            $comp = array_fill(0, $n + 1, -1);
            $c = 0;
            for ($s = 1; $s <= $n; $s++) {
                if ($comp[$s] !== -1) {
                    continue;
                }
                $comp[$s] = $c;
                $q = [$s];
                for ($h = 0; $h < count($q); $h++) {
                    $u = $q[$h];
                    foreach ($adj[$u] as $v) {
                        if ($comp[$v] === -1 && ! isset($isBridge[min($u, $v).'-'.max($u, $v)])) {
                            $comp[$v] = $c;
                            $q[] = $v;
                        }
                    }
                }
                $c++;
            }
            $deg = array_fill(0, $c, 0);
            foreach (array_keys($isBridge) as $k) {
                [$a, $b] = array_map('intval', explode('-', $k));
                $deg[$comp[$a]]++;
                $deg[$comp[$b]]++;
            }
            $leaves = count(array_filter($deg, fn ($d) => $d === 1));

            return (string) intdiv($leaves + 1, 2);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }"),
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
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }

    // 1) Cari semua jembatan (DFS iteratif tin/low). Jembatan p–u ditandai di simpul u.
    vector<int> tin(n + 1, -1), low(n + 1, 0), par(n + 1, 0), idx(n + 1, 0);
    vector<bool> jembatanKeOrtu(n + 1, false);   // apakah sisi par[u]–u jembatan
    int timer = 0;
    tin[1] = low[1] = timer++;
    vector<int> st = {1};
    while (!st.empty()) {
        int u = st.back();
        if (idx[u] < (int)adj[u].size()) {
            int v = adj[u][idx[u]++];
            if (v == par[u]) continue;
            if (tin[v] != -1) low[u] = min(low[u], tin[v]);
            else {
                par[v] = u;
                tin[v] = low[v] = timer++;
                st.push_back(v);
            }
        } else {
            st.pop_back();
            int p = par[u];
            if (p) {
                low[p] = min(low[p], low[u]);
                if (low[u] > tin[p]) jembatanKeOrtu[u] = true;
            }
        }
    }
    auto jembatan = [&](int a, int b) {
        return (par[b] == a && jembatanKeOrtu[b]) || (par[a] == b && jembatanKeOrtu[a]);
    };

    // 2) Komponen setelah semua jembatan dibuang
    vector<int> komp(n + 1, -1);
    int c = 0;
    for (int s = 1; s <= n; s++) {
        if (komp[s] != -1) continue;
        komp[s] = c;
        vector<int> q = {s};
        for (int h = 0; h < (int)q.size(); h++) {
            int u = q[h];
            for (int v : adj[u])
                if (komp[v] == -1 && !jembatan(u, v)) {
                    komp[v] = c;
                    q.push_back(v);
                }
        }
        c++;
    }
    if (c == 1) {
        cout << 0 << '\n';
        return 0;
    }

    // 3) Pohon jembatan: hitung daun (komponen dengan tepat satu jembatan)
    vector<int> deg(c, 0);
    for (int v = 2; v <= n; v++)
        if (jembatanKeOrtu[v]) {
            deg[komp[v]]++;
            deg[komp[par[v]]]++;
        }
    int daun = 0;
    for (int k = 0; k < c; k++) if (deg[k] == 1) daun++;
    // Pasangkan daun-daun: setiap kabel baru "menutup" dua daun
    cout << (daun + 1) / 2 << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const tin = new Int32Array(n + 1).fill(-1), low = new Int32Array(n + 1), par = new Int32Array(n + 1), idx = new Int32Array(n + 1);
const br = new Uint8Array(n + 1);
let timer = 0;
tin[1] = low[1] = timer++;
const st = [1];
while (st.length) {
  const u = st[st.length - 1];
  if (idx[u] < adj[u].length) {
    const v = adj[u][idx[u]++];
    if (v === par[u]) continue;
    if (tin[v] !== -1) low[u] = Math.min(low[u], tin[v]);
    else { par[v] = u; tin[v] = low[v] = timer++; st.push(v); }
  } else {
    st.pop();
    const p = par[u];
    if (p) {
      low[p] = Math.min(low[p], low[u]);
      if (low[u] > tin[p]) br[u] = 1;
    }
  }
}
const isBridge = (a, b) => (par[b] === a && br[b]) || (par[a] === b && br[a]);
const komp = new Int32Array(n + 1).fill(-1);
let c = 0;
for (let s = 1; s <= n; s++) {
  if (komp[s] !== -1) continue;
  komp[s] = c;
  const q = [s];
  for (let h = 0; h < q.length; h++) {
    const u = q[h];
    for (const v of adj[u]) if (komp[v] === -1 && !isBridge(u, v)) { komp[v] = c; q.push(v); }
  }
  c++;
}
if (c === 1) console.log(0);
else {
  const deg = new Int32Array(c);
  for (let v = 2; v <= n; v++) if (br[v]) { deg[komp[v]]++; deg[komp[par[v]]]++; }
  let daun = 0;
  for (let k = 0; k < c; k++) if (deg[k] === 1) daun++;
  console.log((daun + 1) >> 1);
}
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

tin = [-1] * (n + 1)
low = [0] * (n + 1)
par = [0] * (n + 1)
idx = [0] * (n + 1)
br = [False] * (n + 1)
timer = 1
tin[1] = low[1] = 0
st = [1]
while st:
    u = st[-1]
    if idx[u] < len(adj[u]):
        v = adj[u][idx[u]]
        idx[u] += 1
        if v == par[u]:
            continue
        if tin[v] != -1:
            if tin[v] < low[u]:
                low[u] = tin[v]
        else:
            par[v] = u
            tin[v] = low[v] = timer
            timer += 1
            st.append(v)
    else:
        st.pop()
        p = par[u]
        if p:
            if low[u] < low[p]:
                low[p] = low[u]
            if low[u] > tin[p]:
                br[u] = True

def is_bridge(a, b):
    return (par[b] == a and br[b]) or (par[a] == b and br[a])

komp = [-1] * (n + 1)
c = 0
for s in range(1, n + 1):
    if komp[s] != -1:
        continue
    komp[s] = c
    q = [s]
    for u in q:
        for v in adj[u]:
            if komp[v] == -1 and not is_bridge(u, v):
                komp[v] = c
                q.append(v)
    c += 1

if c == 1:
    print(0)
else:
    deg = [0] * c
    for v in range(2, n + 1):
        if br[v]:
            deg[komp[v]] += 1
            deg[komp[par[v]]] += 1
    daun = sum(1 for d in deg if d == 1)
    print((daun + 1) // 2)
CODE,
        ],
        'editorial' => '<p>Jaringan tahan putus berarti <strong>tidak ada jembatan</strong>. Langkah-langkahnya:</p>
<ol><li>Cari semua jembatan dengan DFS tin/low.</li>
<li>Buang jembatan; setiap komponen yang tersisa adalah komponen <strong>2-edge-connected</strong> ("pulau" yang sudah tahan putus).</li>
<li>Ciutkan setiap pulau menjadi satu simpul. Dengan jembatan sebagai sisi, hasilnya sebuah <strong>pohon</strong>.</li></ol>
<p>Pada pohon dengan L daun, setiap kabel baru yang menghubungkan dua daun membuat jalur di antara keduanya menjadi siklus, sehingga semua jembatan di jalur itu hilang. Dengan memasangkan daun secara cerdas, <strong>⌈L / 2⌉</strong> kabel selalu cukup, dan kurang dari itu tidak mungkin karena setiap daun harus mendapat paling sedikit satu kabel baru.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            'Tahan putus = tidak ada jembatan. Mulailah dengan mencari semua jembatan.',
            'Ciutkan setiap komponen yang tersisa setelah jembatan dibuang. Bentuk apa yang terjadi?',
            'Hasilnya pohon. Setiap daun butuh kabel baru, dan satu kabel bisa melayani dua daun: jawabannya ⌈daun / 2⌉.',
        ],
        'sample_visual' => 'graph',
    ],
];
