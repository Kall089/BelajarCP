<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan tambahan track Graph.
 * Semua kode C++ harus lolos C++14 (GCC 6.3 / clang): tanpa structured binding.
 */

/** Starter standar: C++ dengan kode pembacaan input, JS/Python dengan petunjuk singkat. */
$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$cppGraph = "    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }";
$jsGraph = "const [n, m] = readInts();\nconst adj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [u, v] = readInts();\n  adj[u].push(v);\n  adj[v].push(u);\n}";
$pyGraph = "n, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)";

$cppDigraph = "    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n    }";
$jsDigraph = "const [n, m] = readInts();\nconst adj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [u, v] = readInts();\n  adj[u].push(v);\n}";
$pyDigraph = "n, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    u, v = map(int, input().split())\n    adj[u].append(v)";

$cppWGraph = "    int n, m;\n    cin >> n >> m;\n    vector<vector<pair<int, long long>>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        long long w;\n        cin >> u >> v >> w;\n        adj[u].push_back({v, w});\n        adj[v].push_back({u, w});\n    }";
$jsWGraph = "const [n, m] = readInts();\nconst adj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [u, v, w] = readInts();\n  adj[u].push([v, w]);\n  adj[v].push([u, w]);\n}";
$pyWGraph = "n, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    u, v, w = map(int, input().split())\n    adj[u].append((v, w))\n    adj[v].append((u, w))";

/** Graph tak berarah acak dalam format "N M" + sisi. */
$graphInput = function (int $n, int $m, bool $connected = false, ?array $w = null, string $extra = ''): string {
    $m = min($m, intdiv($n * ($n - 1), 2));
    $edges = T::edges($n, $m, false, $connected, $w);

    return T::graphInput("$n ".count($edges).$extra, $edges);
};

/** Dijkstra referensi (PHP) dari banyak sumber sekaligus. */
$dijkstraPhp = function (int $n, array $adj, array $sources): array {
    $dist = array_fill(0, $n + 1, PHP_INT_MAX);
    $pq = new SplPriorityQueue;
    $pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    foreach ($sources as $s) {
        $dist[$s] = 0;
        $pq->insert($s, 0);
    }
    while (! $pq->isEmpty()) {
        $top = $pq->extract();
        $u = $top['data'];
        if (-$top['priority'] > $dist[$u]) {
            continue;
        }
        foreach ($adj[$u] as [$v, $w]) {
            if ($dist[$u] + $w < $dist[$v]) {
                $dist[$v] = $dist[$u] + $w;
                $pq->insert($v, -$dist[$v]);
            }
        }
    }

    return $dist;
};

return [
    // ═════════════════════════════ Representasi Graph ═════════════════════════════
    [
        'slug' => 'pusat-bintang',
        'lesson' => 'representasi-graph',
        'title' => 'Pusat Jaringan Bintang',
        'difficulty' => 'Mudah',
        'tags' => ['graph', 'derajat'],
        'statement' => '<p>Sebuah kantor memasang jaringan komputer berbentuk <strong>bintang</strong>: ada satu komputer pusat (server), dan setiap komputer lain terhubung langsung <strong>hanya</strong> ke server itu. Ada <strong>N</strong> komputer dan N − 1 kabel.</p>
<p>Daftar kabel diberikan dalam urutan acak. Tentukan nomor komputer server.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N − 1 baris berikutnya berisi <code>u v</code>, sebuah kabel antara komputer u dan v.</p>',
        'output_format' => '<p>Nomor komputer pusat.</p>',
        'constraints' => '<ul><li>3 ≤ N ≤ 100 000</li><li>Dijamin graph berbentuk bintang.</li></ul>',
        'samples' => [
            ['input' => "5\n2 4\n4 1\n3 4\n4 5\n", 'explanation' => 'Komputer 4 terhubung ke semua komputer lain.'],
        ],
        'tests' => function () {
            $mk = function (int $n) {
                $c = mt_rand(1, $n);
                $edges = [];
                for ($v = 1; $v <= $n; $v++) {
                    if ($v !== $c) {
                        $edges[] = mt_rand(0, 1) ? [$c, $v] : [$v, $c];
                    }
                }
                T::shuffle($edges);

                return T::graphInput((string) $n, $edges);
            };

            return [$mk(3), $mk(4), $mk(10), $mk(1000), $mk(100000), $mk(99999)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $deg = array_fill(0, $n + 1, 0);
            for ($i = 1; $i < $n; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $deg[$u]++;
                $deg[$v]++;
            }

            return (string) array_search($n - 1, $deg, true);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> deg(n + 1, 0);\n    for (int i = 0; i < n - 1; i++) {\n        int u, v;\n        cin >> u >> v;\n        // ...\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> deg(n + 1, 0);
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        cin >> u >> v;
        deg[u]++;
        deg[v]++;
    }
    // Pusat bintang terhubung ke semua komputer lain: derajatnya n - 1
    for (int v = 1; v <= n; v++) {
        if (deg[v] == n - 1) {
            cout << v << '\n';
            break;
        }
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const deg = new Array(n + 1).fill(0);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  deg[u]++;
  deg[v]++;
}
console.log(deg.indexOf(n - 1));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
deg = [0] * (n + 1)
for _ in range(n - 1):
    u, v = map(int, input().split())
    deg[u] += 1
    deg[v] += 1
print(deg.index(n - 1))
CODE,
        ],
        'editorial' => '<p>Hitung <strong>derajat</strong> setiap simpul (banyak kabel yang menyentuhnya). Server terhubung ke semua N − 1 komputer lain, sedangkan komputer lain hanya punya satu kabel. Jadi server adalah satu-satunya simpul berderajat N − 1.</p>
<p><strong>Trik lebih singkat:</strong> server pasti muncul di dua kabel pertama sekaligus, jadi cukup cari simpul yang sama di dua baris pertama.</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
        'hints' => [
            'Apa yang membedakan server dari komputer biasa, jika dilihat dari banyak kabelnya?',
            'Hitung derajat setiap simpul: berapa kali nomornya muncul di daftar kabel.',
            'Server punya derajat N − 1; semua yang lain berderajat 1.',
        ],
        'sample_visual' => 'tree',
    ],

    [
        'slug' => 'tetangga-bersama',
        'lesson' => 'representasi-graph',
        'title' => 'Teman Bersama',
        'difficulty' => 'Mudah',
        'tags' => ['graph', 'adjacency matrix'],
        'statement' => '<p>Sebuah media sosial sekolah punya <strong>N</strong> pengguna dan <strong>M</strong> pasangan pertemanan (dua arah). Fitur "teman bersama" menampilkan berapa banyak orang yang berteman dengan <em>kedua</em> pengguna sekaligus.</p>
<p>Jawab <strong>Q</strong> pertanyaan: untuk pasangan pengguna <code>u v</code>, berapa banyak teman bersama mereka?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M Q</code>. M baris berikutnya berisi pasangan teman <code>a b</code>. Q baris berikutnya berisi <code>u v</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing banyak teman bersama.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 500</li><li>0 ≤ M ≤ 20 000, tidak ada pasangan ganda</li><li>1 ≤ Q ≤ 2000</li><li>u ≠ v</li></ul>',
        'samples' => [
            ['input' => "5 6 3\n1 2\n1 3\n2 3\n2 4\n3 4\n4 5\n1 4\n2 3\n1 5\n", 'explanation' => 'Teman 1 = {2, 3}, teman 4 = {2, 3, 5}: bersama {2, 3} → 2. Teman 2 = {1, 3, 4} dan teman 3 = {1, 2, 4}: bersama {1, 4} → 2. Pengguna 1 dan 5 tidak punya teman bersama.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $q) {
                $edges = T::edges($n, min($m, intdiv($n * ($n - 1), 2)));
                $out = [$n.' '.count($edges)." $q"];
                foreach ($edges as $e) {
                    $out[] = "{$e[0]} {$e[1]}";
                }
                for ($i = 0; $i < $q; $i++) {
                    $u = mt_rand(1, $n);
                    do {
                        $v = mt_rand(1, $n);
                    } while ($v === $u);
                    $out[] = "$u $v";
                }

                return implode("\n", $out)."\n";
            };

            return ["2 1 1\n1 2\n2 1\n", $mk(6, 8, 10), $mk(50, 300, 200), $mk(500, 20000, 2000), $mk(500, 3000, 2000), $mk(200, 19900, 2000)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m, $q] = T::ints($lines[0]);
            $adj = array_fill(1, $n, []);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $adj[$a][$b] = true;
                $adj[$b][$a] = true;
            }
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$u, $v] = T::ints($lines[$m + 1 + $i]);
                $out[] = count(array_intersect_key($adj[$u], $adj[$v]));
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, m, q;\n    cin >> n >> m >> q;\n    vector<vector<bool>> mat(n + 1, vector<bool>(n + 1, false));\n    for (int i = 0; i < m; i++) {\n        int a, b;\n        cin >> a >> b;\n        mat[a][b] = mat[b][a] = true;\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, q;
    cin >> n >> m >> q;
    // Adjacency matrix: cek "a berteman dengan b?" dalam O(1)
    vector<vector<bool>> mat(n + 1, vector<bool>(n + 1, false));
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        mat[a][b] = mat[b][a] = true;
    }
    while (q--) {
        int u, v;
        cin >> u >> v;
        int bersama = 0;
        for (int w = 1; w <= n; w++)
            if (mat[u][w] && mat[v][w]) bersama++;
        cout << bersama << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m, q] = readInts();
const mat = Array.from({ length: n + 1 }, () => new Uint8Array(n + 1));
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  mat[a][b] = mat[b][a] = 1;
}
const out = [];
for (let i = 0; i < q; i++) {
  const [u, v] = readInts();
  let c = 0;
  for (let w = 1; w <= n; w++) if (mat[u][w] && mat[v][w]) c++;
  out.push(c);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m, q = map(int, input().split())
# Simpan teman setiap pengguna sebagai bilangan biner (bit ke-w menyala jika berteman dengan w)
bit = [0] * (n + 1)
for _ in range(m):
    a, b = map(int, input().split())
    bit[a] |= 1 << b
    bit[b] |= 1 << a
out = []
for _ in range(q):
    u, v = map(int, input().split())
    out.append(bin(bit[u] & bit[v]).count("1"))
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Karena N kecil (≤ 500), simpan graph sebagai <strong>adjacency matrix</strong>: <code>mat[a][b]</code> bernilai benar jika a dan b berteman. Untuk setiap pertanyaan, periksa semua pengguna w dan hitung yang berteman dengan u <em>dan</em> v.</p>
<p>Total kerja O(N² + Q · N) ≈ 10<sup>6</sup>. Dengan adjacency list, mengecek "apakah w teman v" butuh mencari di daftar, sehingga matriks lebih cocok di sini.</p>
<p><strong>Lanjutan:</strong> dengan <code>bitset&lt;501&gt;</code>, jawaban satu pertanyaan cukup <code>(b[u] &amp; b[v]).count()</code>, sekitar 64 kali lebih cepat.</p>',
        'hints' => [
            'N paling banyak 500. Representasi graph mana yang membuat pengecekan "apakah a berteman dengan b" O(1)?',
            'Simpan adjacency matrix mat[a][b].',
            'Untuk setiap pertanyaan, loop semua w dan hitung yang mat[u][w] dan mat[v][w] keduanya benar.',
        ],
        'sample_visual' => 'graph',
    ],

    // ═════════════════════════════ BFS ═════════════════════════════
    [
        'slug' => 'ksatria-catur',
        'lesson' => 'bfs',
        'title' => 'Langkah Kuda Catur',
        'difficulty' => 'Mudah',
        'tags' => ['bfs', 'grid', 'graph tersembunyi'],
        'statement' => '<p>Di papan catur berukuran <strong>N × N</strong> ada sebuah bidak kuda di petak (r<sub>1</sub>, c<sub>1</sub>). Kuda bergerak membentuk huruf L: dua petak ke satu arah lalu satu petak tegak lurus (paling banyak 8 kemungkinan gerakan), dan tidak boleh keluar papan.</p>
<p>Berapa langkah paling sedikit agar kuda sampai di petak (r<sub>2</sub>, c<sub>2</sub>)? Jika mustahil, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Satu baris berisi <code>N r1 c1 r2 c2</code> (baris dan kolom dinomori dari 1).</p>',
        'output_format' => '<p>Banyak langkah minimum, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 300</li><li>1 ≤ r1, c1, r2, c2 ≤ N</li></ul>',
        'samples' => [
            ['input' => "8 1 1 8 8\n", 'explanation' => 'Dari pojok ke pojok papan catur biasa butuh 6 langkah kuda.'],
            ['input' => "3 1 1 2 2\n", 'explanation' => 'Di papan 3 × 3, petak tengah tidak pernah bisa dicapai kuda.'],
        ],
        'tests' => function () {
            $mk = fn (int $n) => "$n ".mt_rand(1, $n).' '.mt_rand(1, $n).' '.mt_rand(1, $n).' '.mt_rand(1, $n)."\n";

            return ["1 1 1 1 1\n", "2 1 1 2 2\n", "4 1 1 4 4\n", "5 3 3 3 3\n", $mk(8), $mk(20), $mk(100), "300 1 1 300 300\n", "300 1 1 2 2\n", $mk(300)];
        },
        'solve' => function (string $input) {
            [$n, $r1, $c1, $r2, $c2] = T::ints(T::lines($input)[0]);
            $dist = array_fill(0, $n * $n, -1);
            $s = ($r1 - 1) * $n + $c1 - 1;
            $dist[$s] = 0;
            $q = [$s];
            $moves = [[1, 2], [2, 1], [-1, 2], [-2, 1], [1, -2], [2, -1], [-1, -2], [-2, -1]];
            for ($h = 0; $h < count($q); $h++) {
                $r = intdiv($q[$h], $n);
                $c = $q[$h] % $n;
                foreach ($moves as [$dr, $dc]) {
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    if ($nr >= 0 && $nr < $n && $nc >= 0 && $nc < $n && $dist[$nr * $n + $nc] === -1) {
                        $dist[$nr * $n + $nc] = $dist[$q[$h]] + 1;
                        $q[] = $nr * $n + $nc;
                    }
                }
            }

            return (string) $dist[($r2 - 1) * $n + $c2 - 1];
        },
        'starter' => $st("    int n, r1, c1, r2, c2;\n    cin >> n >> r1 >> c1 >> r2 >> c2;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, r1, c1, r2, c2;
    cin >> n >> r1 >> c1 >> r2 >> c2;
    int dr[] = {1, 2, -1, -2, 1, 2, -1, -2};
    int dc[] = {2, 1, 2, 1, -2, -1, -2, -1};

    // Setiap petak adalah simpul; gerakan kuda adalah sisi. BFS dari petak awal.
    vector<vector<int>> dist(n + 1, vector<int>(n + 1, -1));
    queue<pair<int, int>> q;
    dist[r1][c1] = 0;
    q.push({r1, c1});
    while (!q.empty()) {
        int r = q.front().first, c = q.front().second;
        q.pop();
        for (int k = 0; k < 8; k++) {
            int nr = r + dr[k], nc = c + dc[k];
            if (nr < 1 || nr > n || nc < 1 || nc > n || dist[nr][nc] != -1) continue;
            dist[nr][nc] = dist[r][c] + 1;
            q.push({nr, nc});
        }
    }
    cout << dist[r2][c2] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, r1, c1, r2, c2] = readInts();
const dist = new Int32Array((n + 1) * (n + 1)).fill(-1);
const id = (r, c) => r * (n + 1) + c;
const M = [[1, 2], [2, 1], [-1, 2], [-2, 1], [1, -2], [2, -1], [-1, -2], [-2, -1]];
const q = [[r1, c1]];
dist[id(r1, c1)] = 0;
for (let h = 0; h < q.length; h++) {
  const [r, c] = q[h];
  for (const [a, b] of M) {
    const nr = r + a, nc = c + b;
    if (nr < 1 || nr > n || nc < 1 || nc > n || dist[id(nr, nc)] !== -1) continue;
    dist[id(nr, nc)] = dist[id(r, c)] + 1;
    q.push([nr, nc]);
  }
}
console.log(dist[id(r2, c2)]);
CODE,
            'python' => <<<'CODE'
from collections import deque

n, r1, c1, r2, c2 = map(int, input().split())
dist = [[-1] * (n + 1) for _ in range(n + 1)]
dist[r1][c1] = 0
q = deque([(r1, c1)])
M = ((1, 2), (2, 1), (-1, 2), (-2, 1), (1, -2), (2, -1), (-1, -2), (-2, -1))
while q:
    r, c = q.popleft()
    for a, b in M:
        nr, nc = r + a, c + b
        if 1 <= nr <= n and 1 <= nc <= n and dist[nr][nc] == -1:
            dist[nr][nc] = dist[r][c] + 1
            q.append((nr, nc))
print(dist[r2][c2])
CODE,
        ],
        'editorial' => '<p>Ini <strong>graph tersembunyi</strong>: setiap petak adalah simpul, dan setiap gerakan kuda yang sah adalah sisi. Semua gerakan berbiaya sama (1 langkah), jadi jarak terpendek dicari dengan <strong>BFS</strong> dari petak awal.</p>
<p>Tetangga dihitung saat dibutuhkan dengan 8 pasangan (dr, dc); graph tidak perlu disimpan. Petak yang tidak pernah tercapai tetap bernilai −1 (misalnya petak tengah papan 3 × 3, atau semua petak lain pada papan 1 × 1 dan 2 × 2).</p>
<p><strong>Kompleksitas:</strong> O(N²) petak × 8 gerakan.</p>',
        'hints' => [
            'Anggap setiap petak sebagai simpul. Sisi apa yang menghubungkan dua petak?',
            'Semua gerakan berbiaya sama, jadi algoritma jarak terpendeknya BFS.',
            'Simpan dist[r][c] = −1 untuk petak yang belum dikunjungi, lalu jelajahi 8 gerakan dari setiap petak.',
        ],
        'sample_visual' => 'chess',
    ],

    [
        'slug' => 'pos-ronda',
        'lesson' => 'bfs',
        'title' => 'Pos Ronda Terdekat',
        'difficulty' => 'Sedang',
        'tags' => ['bfs', 'multi-source', 'grid'],
        'statement' => '<p>Peta kampung berbentuk grid <strong>R × C</strong>. Petak <code>.</code> adalah jalan, <code>#</code> adalah rumah/tembok yang tidak bisa dilewati, dan <code>P</code> adalah pos ronda. Petugas berjalan ke atas, bawah, kiri, atau kanan.</p>
<p>Untuk setiap petak jalan, petugas dari pos <strong>terdekat</strong> yang akan datang. Berapa jarak <strong>terjauh</strong> yang mungkin harus ditempuh petugas untuk mencapai sebuah petak jalan? Jika ada petak jalan yang tidak bisa dicapai pos mana pun, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>. R baris berikutnya berisi peta.</p>',
        'output_format' => '<p>Jarak terjauh ke pos terdekat, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 500</li><li>Ada paling sedikit satu pos ronda.</li></ul>',
        'samples' => [
            ['input' => "3 5\nP...#\n.#...\n...#P\n", 'explanation' => 'Petak di baris 3 kolom 3 berjarak 4 dari kedua pos (pos kiri atas lewat kolom paling kiri, pos kanan bawah lewat baris 2). Semua petak jalan lain lebih dekat ke salah satu pos, jadi jawabannya 4.'],
            ['input' => "2 3\nP#.\n.#.\n", 'explanation' => 'Kolom kanan terpisah tembok dari satu-satunya pos.'],
        ],
        'tests' => function () {
            $mk = function (int $r, int $c, float $wall, int $posts) {
                $g = T::grid($r, $c, $wall);
                for ($i = 0; $i < $posts; $i++) {
                    $g[mt_rand(0, $r - 1)][mt_rand(0, $c - 1)] = 'P';
                }

                return "$r $c\n".implode("\n", $g)."\n";
            };

            return ["1 1\nP\n", "1 4\nP...\n", $mk(5, 5, 0.2, 2), $mk(30, 40, 0.25, 4), $mk(200, 200, 0.3, 10), $mk(500, 500, 0.05, 1), $mk(500, 500, 0.3, 50), $mk(500, 500, 0.0, 3), $mk(400, 500, 0.45, 20)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C] = T::ints($lines[0]);
            $g = array_slice($lines, 1, $R);
            $dist = array_fill(0, $R * $C, -1);
            $q = [];
            for ($r = 0; $r < $R; $r++) {
                for ($c = 0; $c < $C; $c++) {
                    if ($g[$r][$c] === 'P') {
                        $dist[$r * $C + $c] = 0;
                        $q[] = $r * $C + $c;
                    }
                }
            }
            for ($h = 0; $h < count($q); $h++) {
                $r = intdiv($q[$h], $C);
                $c = $q[$h] % $C;
                foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$dr, $dc]) {
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    if ($nr >= 0 && $nr < $R && $nc >= 0 && $nc < $C && $g[$nr][$nc] !== '#' && $dist[$nr * $C + $nc] === -1) {
                        $dist[$nr * $C + $nc] = $dist[$q[$h]] + 1;
                        $q[] = $nr * $C + $nc;
                    }
                }
            }
            $best = 0;
            for ($r = 0; $r < $R; $r++) {
                for ($c = 0; $c < $C; $c++) {
                    if ($g[$r][$c] === '.') {
                        if ($dist[$r * $C + $c] === -1) {
                            return '-1';
                        }
                        $best = max($best, $dist[$r * $C + $c]);
                    }
                }
            }

            return (string) $best;
        },
        'starter' => $st("    int R, C;\n    cin >> R >> C;\n    vector<string> g(R);\n    for (auto& baris : g) cin >> baris;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<string> g(R);
    for (auto& baris : g) cin >> baris;

    // BFS multi-sumber: SEMUA pos masuk antrian dengan jarak 0 sekaligus
    vector<vector<int>> dist(R, vector<int>(C, -1));
    queue<pair<int, int>> q;
    for (int r = 0; r < R; r++)
        for (int c = 0; c < C; c++)
            if (g[r][c] == 'P') {
                dist[r][c] = 0;
                q.push({r, c});
            }
    int dr[] = {-1, 1, 0, 0}, dc[] = {0, 0, -1, 1};
    while (!q.empty()) {
        int r = q.front().first, c = q.front().second;
        q.pop();
        for (int k = 0; k < 4; k++) {
            int nr = r + dr[k], nc = c + dc[k];
            if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue;
            if (g[nr][nc] == '#' || dist[nr][nc] != -1) continue;
            dist[nr][nc] = dist[r][c] + 1;
            q.push({nr, nc});
        }
    }

    int jawaban = 0;
    for (int r = 0; r < R; r++)
        for (int c = 0; c < C; c++)
            if (g[r][c] == '.') {
                if (dist[r][c] == -1) {
                    cout << -1 << '\n';
                    return 0;
                }
                jawaban = max(jawaban, dist[r][c]);
            }
    cout << jawaban << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const g = [];
for (let i = 0; i < R; i++) g.push(readLine().trim());
const dist = new Int32Array(R * C).fill(-1);
const q = [];
for (let r = 0; r < R; r++)
  for (let c = 0; c < C; c++)
    if (g[r][c] === "P") { dist[r * C + c] = 0; q.push(r * C + c); }
const D = [[-1, 0], [1, 0], [0, -1], [0, 1]];
for (let h = 0; h < q.length; h++) {
  const r = Math.floor(q[h] / C), c = q[h] % C;
  for (const [a, b] of D) {
    const nr = r + a, nc = c + b;
    if (nr < 0 || nr >= R || nc < 0 || nc >= C || g[nr][nc] === "#" || dist[nr * C + nc] !== -1) continue;
    dist[nr * C + nc] = dist[q[h]] + 1;
    q.push(nr * C + nc);
  }
}
let best = 0, ok = true;
for (let r = 0; r < R; r++)
  for (let c = 0; c < C; c++)
    if (g[r][c] === ".") {
      if (dist[r * C + c] === -1) ok = false;
      best = Math.max(best, dist[r * C + c]);
    }
console.log(ok ? best : -1);
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

R, C = map(int, input().split())
g = [input().strip() for _ in range(R)]
dist = [[-1] * C for _ in range(R)]
q = deque()
for r in range(R):
    for c in range(C):
        if g[r][c] == "P":
            dist[r][c] = 0
            q.append((r, c))
while q:
    r, c = q.popleft()
    for nr, nc in ((r - 1, c), (r + 1, c), (r, c - 1), (r, c + 1)):
        if 0 <= nr < R and 0 <= nc < C and g[nr][nc] != "#" and dist[nr][nc] == -1:
            dist[nr][nc] = dist[r][c] + 1
            q.append((nr, nc))

best = 0
for r in range(R):
    for c in range(C):
        if g[r][c] == ".":
            if dist[r][c] == -1:
                print(-1)
                sys.exit()
            best = max(best, dist[r][c])
print(best)
CODE,
        ],
        'editorial' => '<p>Menjalankan BFS dari setiap pos terpisah bisa butuh O(banyak pos × R × C). Lebih baik pakai <strong>BFS multi-sumber</strong>: masukkan <em>semua</em> pos ke antrian dengan jarak 0 sejak awal. BFS lalu menyebar dari semua pos bersamaan, dan setiap petak pertama kali dicapai oleh pos terdekatnya.</p>
<p>Setelah BFS selesai, jawabannya adalah jarak terbesar di antara petak jalan; jika ada petak jalan yang masih −1, cetak −1.</p>
<p><strong>Kompleksitas:</strong> O(R · C).</p>',
        'hints' => [
            'BFS dari setiap pos satu per satu bisa terlalu lambat jika posnya banyak.',
            'Masukkan semua pos ke antrian dengan jarak 0 sebelum BFS dimulai.',
            'Jarak setiap petak sekarang adalah jarak ke pos terdekat. Ambil maksimumnya, dan perhatikan petak yang tidak tercapai.',
        ],
    ],

    // ═════════════════════════════ DFS ═════════════════════════════
    [
        'slug' => 'dua-tim',
        'lesson' => 'dfs',
        'title' => 'Dua Tim Lomba',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'bipartit', 'pewarnaan'],
        'statement' => '<p>Wali kelas ingin membagi <strong>N</strong> siswa ke dalam <strong>dua tim</strong>. Ada <strong>M</strong> pasangan siswa yang sering bertengkar, dan setiap pasangan seperti itu harus berada di tim yang <strong>berbeda</strong>.</p>
<p>Jika pembagian mungkin, cetak tim setiap siswa (1 atau 2). Agar jawabannya unik: proses siswa dari nomor terkecil, dan siswa bernomor terkecil yang belum mendapat tim selalu masuk tim <strong>1</strong>. Jika mustahil, cetak <code>TIDAK</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi pasangan <code>a b</code> yang harus dipisah.</p>',
        'output_format' => '<p>N bilangan (1 atau 2) dipisah spasi, atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li><li>a ≠ b</li></ul>',
        'samples' => [
            ['input' => "5 4\n1 2\n2 3\n3 4\n5 4\n", 'explanation' => 'Siswa 1 di tim 1, maka 2 di tim 2, 3 di tim 1, 4 di tim 2, 5 di tim 1.'],
            ['input' => "3 3\n1 2\n2 3\n3 1\n", 'explanation' => 'Tiga siswa yang saling bertengkar tidak bisa dibagi ke dua tim (siklus ganjil).'],
        ],
        'tests' => function () {
            $bip = function (int $n, int $m) {
                $side = [];
                for ($i = 1; $i <= $n; $i++) {
                    $side[$i] = mt_rand(0, 1);
                }
                $edges = [];
                $seen = [];
                $guard = 0;
                while (count($edges) < $m && $guard++ < $m * 20) {
                    $u = mt_rand(1, $n);
                    $v = mt_rand(1, $n);
                    if ($side[$u] === $side[$v] || isset($seen[min($u, $v).'-'.max($u, $v)])) {
                        continue;
                    }
                    $seen[min($u, $v).'-'.max($u, $v)] = true;
                    $edges[] = [$u, $v];
                }

                return T::graphInput("$n ".count($edges), $edges);
            };
            $rand = fn (int $n, int $m) => T::graphInput("$n ".min($m, intdiv($n * ($n - 1), 2)), T::edges($n, min($m, intdiv($n * ($n - 1), 2))));

            return ["1 0\n", "2 1\n1 2\n", $bip(8, 8), $rand(8, 6), $bip(1000, 3000), $rand(1000, 1200), $bip(100000, 200000), $bip(100000, 50000), $rand(100000, 100000)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $adj[$a][] = $b;
                $adj[$b][] = $a;
            }
            $col = array_fill(0, $n + 1, 0);
            for ($s = 1; $s <= $n; $s++) {
                if ($col[$s]) {
                    continue;
                }
                $col[$s] = 1;
                $q = [$s];
                for ($h = 0; $h < count($q); $h++) {
                    $u = $q[$h];
                    foreach ($adj[$u] as $v) {
                        if (! $col[$v]) {
                            $col[$v] = 3 - $col[$u];
                            $q[] = $v;
                        } elseif ($col[$v] === $col[$u]) {
                            return 'TIDAK';
                        }
                    }
                }
            }

            return implode(' ', array_slice($col, 1));
        },
        'starter' => $st($cppGraph, $jsGraph, $pyGraph),
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
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        adj[b].push_back(a);
    }

    // Pewarnaan dua warna: tetangga harus berbeda warna (1 <-> 2)
    vector<int> tim(n + 1, 0);
    for (int s = 1; s <= n; s++) {
        if (tim[s]) continue;
        tim[s] = 1;                       // siswa terkecil di komponen ini → tim 1
        queue<int> q;
        q.push(s);
        while (!q.empty()) {
            int u = q.front();
            q.pop();
            for (int v : adj[u]) {
                if (tim[v] == 0) {
                    tim[v] = 3 - tim[u];  // warna kebalikan
                    q.push(v);
                } else if (tim[v] == tim[u]) {
                    cout << "TIDAK\n";    // dua musuh terpaksa satu tim
                    return 0;
                }
            }
        }
    }
    for (int v = 1; v <= n; v++) cout << tim[v] << (v < n ? ' ' : '\n');
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  adj[b].push(a);
}
const tim = new Array(n + 1).fill(0);
let ok = true;
for (let s = 1; s <= n && ok; s++) {
  if (tim[s]) continue;
  tim[s] = 1;
  const q = [s];
  for (let h = 0; h < q.length && ok; h++) {
    const u = q[h];
    for (const v of adj[u]) {
      if (!tim[v]) { tim[v] = 3 - tim[u]; q.push(v); }
      else if (tim[v] === tim[u]) { ok = false; break; }
    }
  }
}
console.log(ok ? tim.slice(1).join(" ") : "TIDAK");
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    adj[b].append(a)

tim = [0] * (n + 1)
for s in range(1, n + 1):
    if tim[s]:
        continue
    tim[s] = 1
    q = deque([s])
    while q:
        u = q.popleft()
        for v in adj[u]:
            if not tim[v]:
                tim[v] = 3 - tim[u]
                q.append(v)
            elif tim[v] == tim[u]:
                print("TIDAK")
                sys.exit()
print(*tim[1:])
CODE,
        ],
        'editorial' => '<p>Anggap siswa sebagai simpul dan pasangan yang bertengkar sebagai sisi. Pertanyaannya: bisakah simpul diwarnai dua warna sehingga setiap sisi menghubungkan dua warna berbeda? Graph seperti itu disebut <strong>bipartit</strong>.</p>
<p>Jelajahi setiap komponen (DFS atau BFS). Simpul pertama diberi warna 1; setiap tetangga yang belum berwarna diberi warna kebalikan. Jika menemukan tetangga yang <strong>warnanya sama</strong>, ada siklus ganjil dan pembagian mustahil.</p>
<p>Dalam satu komponen, setelah warna simpul pertama ditetapkan, semua warna lain terpaksa, jadi aturan "siswa terkecil masuk tim 1" membuat jawabannya unik.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            'Ubah menjadi graph: siswa = simpul, pasangan bertengkar = sisi.',
            'Warnai simpul dua warna sambil menjelajah: tetangga harus mendapat warna kebalikan.',
            'Jika tetangga ternyata sudah berwarna sama, jawabannya TIDAK. Jangan lupa graph bisa tidak terhubung.',
        ],
        'sample_visual' => 'graph',
    ],

    [
        'slug' => 'siklus-berarah',
        'lesson' => 'dfs',
        'title' => 'Ketergantungan Melingkar',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'dfs', 'siklus', 'berarah'],
        'statement' => '<p>Sebuah proyek perangkat lunak terdiri dari <strong>N</strong> modul. Ada <strong>M</strong> ketergantungan: <code>a b</code> berarti modul a membutuhkan modul b. Jika ketergantungan membentuk lingkaran (misalnya a butuh b, b butuh c, c butuh a), proyek tidak bisa dikompilasi.</p>
<p>Apakah ada ketergantungan melingkar? Cetak <code>YA</code> atau <code>TIDAK</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p><code>YA</code> jika ada siklus berarah, <code>TIDAK</code> jika tidak.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li><li>Bisa ada a = b (modul membutuhkan dirinya sendiri, itu juga siklus).</li></ul>',
        'samples' => [
            ['input' => "4 4\n1 2\n2 3\n3 4\n1 3\n", 'explanation' => 'Semua ketergantungan mengarah "maju", tidak ada lingkaran.'],
            ['input' => "4 4\n1 2\n2 3\n3 1\n3 4\n", 'explanation' => '1 → 2 → 3 → 1 membentuk lingkaran.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, bool $cyclic) {
                $edges = $cyclic ? T::withCycle($n, $m) : T::dag($n, $m);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "1 1\n1 1\n", "2 2\n1 2\n2 1\n", $mk(10, 15, false), $mk(10, 15, true), $mk(1000, 3000, false), $mk(1000, 3000, true), $mk(100000, 200000, false), $mk(100000, 200000, true), $mk(100000, 99999, false)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            $indeg = array_fill(0, $n + 1, 0);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $adj[$a][] = $b;
                $indeg[$b]++;
            }
            $q = [];
            for ($v = 1; $v <= $n; $v++) {
                if ($indeg[$v] === 0) {
                    $q[] = $v;
                }
            }
            for ($h = 0; $h < count($q); $h++) {
                foreach ($adj[$q[$h]] as $v) {
                    if (--$indeg[$v] === 0) {
                        $q[] = $v;
                    }
                }
            }

            return count($q) < $n ? 'YA' : 'TIDAK';
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
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
    }

    // DFS tiga warna (iteratif agar aman untuk graph dalam):
    // 0 = belum dikunjungi, 1 = sedang di jalur DFS (stack), 2 = selesai
    vector<int> warna(n + 1, 0), idx(n + 1, 0);
    for (int s = 1; s <= n; s++) {
        if (warna[s]) continue;
        vector<int> st = {s};
        warna[s] = 1;
        while (!st.empty()) {
            int u = st.back();
            if (idx[u] < (int)adj[u].size()) {
                int v = adj[u][idx[u]++];
                if (warna[v] == 1) {          // kembali ke simpul di jalur aktif: siklus!
                    cout << "YA\n";
                    return 0;
                }
                if (warna[v] == 0) {
                    warna[v] = 1;
                    st.push_back(v);
                }
            } else {
                warna[u] = 2;
                st.pop_back();
            }
        }
    }
    cout << "TIDAK\n";
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
}
const warna = new Uint8Array(n + 1), idx = new Int32Array(n + 1);
let siklus = false;
for (let s = 1; s <= n && !siklus; s++) {
  if (warna[s]) continue;
  const st = [s];
  warna[s] = 1;
  while (st.length && !siklus) {
    const u = st[st.length - 1];
    if (idx[u] < adj[u].length) {
      const v = adj[u][idx[u]++];
      if (warna[v] === 1) siklus = true;
      else if (warna[v] === 0) { warna[v] = 1; st.push(v); }
    } else {
      warna[u] = 2;
      st.pop();
    }
  }
}
console.log(siklus ? "YA" : "TIDAK");
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)

warna = [0] * (n + 1)
idx = [0] * (n + 1)
for s in range(1, n + 1):
    if warna[s]:
        continue
    st = [s]
    warna[s] = 1
    while st:
        u = st[-1]
        if idx[u] < len(adj[u]):
            v = adj[u][idx[u]]
            idx[u] += 1
            if warna[v] == 1:
                print("YA")
                sys.exit()
            if warna[v] == 0:
                warna[v] = 1
                st.append(v)
        else:
            warna[u] = 2
            st.pop()
print("TIDAK")
CODE,
        ],
        'editorial' => '<p>Pada graph <strong>berarah</strong>, "menemukan simpul yang sudah dikunjungi" belum tentu siklus (misalnya 1 → 2, 1 → 3, 3 → 2). Yang menandakan siklus adalah kembali ke simpul yang <strong>masih berada di jalur DFS saat ini</strong>.</p>
<p>Gunakan tiga warna: putih (belum), abu-abu (sedang di stack DFS), hitam (selesai). Sisi ke simpul abu-abu adalah <em>back edge</em> dan berarti ada siklus. Sisi ke simpul hitam aman.</p>
<p>Alternatif: algoritma Kahn (topological sort). Jika tidak semua simpul bisa dikeluarkan, ada siklus.</p>
<p><strong>Kompleksitas:</strong> O(N + M). Untuk N = 10<sup>5</sup>, gunakan DFS iteratif atau Kahn agar tidak stack overflow.</p>',
        'hints' => [
            'Pada graph berarah, bertemu simpul yang sudah dikunjungi belum tentu berarti siklus. Kapan itu benar-benar siklus?',
            'Bedakan simpul yang sedang di jalur DFS (belum selesai) dan yang sudah selesai: pakai tiga warna.',
            'Sisi ke simpul yang masih "abu-abu" berarti siklus. Alternatif: topological sort Kahn.',
        ],
        'sample_visual' => 'digraph',
    ],

    // ═════════════════════════════ Dijkstra ═════════════════════════════
    [
        'slug' => 'hitung-rute-tercepat',
        'lesson' => 'dijkstra',
        'title' => 'Banyak Rute Tercepat',
        'difficulty' => 'Sulit',
        'tags' => ['dijkstra', 'menghitung', 'modulo'],
        'statement' => '<p>Kota punya <strong>N</strong> persimpangan dan <strong>M</strong> jalan dua arah; jalan ke-i antara <code>u</code> dan <code>v</code> butuh waktu <code>w</code> menit (w ≥ 1).</p>
<p>Aplikasi navigasi ingin menampilkan waktu tercepat dari persimpangan <strong>1</strong> ke persimpangan <strong>N</strong>, dan juga <strong>berapa banyak rute berbeda</strong> yang mencapai waktu tercepat itu. Karena bisa sangat banyak, cetak banyaknya modulo 10<sup>9</sup> + 7.</p>
<p>Jika N tidak bisa dicapai, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v w</code>.</p>',
        'output_format' => '<p>Dua bilangan: waktu tercepat dan banyak rute tercepat (mod 10<sup>9</sup> + 7), atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>1 ≤ M ≤ 200 000</li><li>1 ≤ w ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 5\n1 2 1\n1 3 1\n2 4 1\n3 4 1\n1 4 3\n", 'explanation' => 'Waktu tercepat 2, lewat 1–2–4 atau 1–3–4. Jalan langsung 1–4 butuh 3 menit.'],
            ['input' => "3 1\n1 2 5\n", 'explanation' => 'Persimpangan 3 tidak terhubung.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $maxW, bool $conn = true) {
                $edges = T::edges($n, min($m, intdiv($n * ($n - 1), 2)), false, $conn, [1, $maxW]);

                return T::graphInput("$n ".count($edges), $edges);
            };
            // Grid lebar 2 dengan bobot 1: banyak rute tercepat (bilangan binomial)
            $ladder = function (int $len) {
                $edges = [];
                $id = fn ($r, $c) => $r * $len + $c + 1;
                for ($c = 0; $c < $len; $c++) {
                    for ($r = 0; $r < 3; $r++) {
                        if ($c + 1 < $len) {
                            $edges[] = [$id($r, $c), $id($r, $c + 1), 1];
                        }
                        if ($r + 1 < 3) {
                            $edges[] = [$id($r, $c), $id($r + 1, $c), 1];
                        }
                    }
                }

                return T::graphInput((3 * $len).' '.count($edges), $edges);
            };

            return ["2 1\n1 2 7\n", $mk(6, 9, 2), $mk(50, 150, 3), $mk(1000, 5000, 2), $ladder(30), $ladder(20000), $mk(100000, 200000, 1000000000), $mk(100000, 200000, 2), $mk(100000, 100000, 5, false)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v, $w] = T::ints($lines[$i]);
                $adj[$u][] = [$v, $w];
                $adj[$v][] = [$u, $w];
            }
            $MOD = 1000000007;
            $dist = array_fill(0, $n + 1, PHP_INT_MAX);
            $cnt = array_fill(0, $n + 1, 0);
            $dist[1] = 0;
            $cnt[1] = 1;
            $pq = new SplPriorityQueue;
            $pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
            $pq->insert(1, 0);
            while (! $pq->isEmpty()) {
                $top = $pq->extract();
                $u = $top['data'];
                if (-$top['priority'] > $dist[$u]) {
                    continue;
                }
                foreach ($adj[$u] as [$v, $w]) {
                    $nd = $dist[$u] + $w;
                    if ($nd < $dist[$v]) {
                        $dist[$v] = $nd;
                        $cnt[$v] = $cnt[$u];
                        $pq->insert($v, -$nd);
                    } elseif ($nd === $dist[$v]) {
                        $cnt[$v] = ($cnt[$v] + $cnt[$u]) % $MOD;
                    }
                }
            }

            return $dist[$n] === PHP_INT_MAX ? '-1' : $dist[$n].' '.$cnt[$n];
        },
        'starter' => $st($cppWGraph, $jsWGraph, $pyWGraph),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;
const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<pair<int, long long>>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w});
        adj[v].push_back({u, w});
    }

    vector<long long> dist(n + 1, INF), cnt(n + 1, 0);
    typedef pair<long long, int> pli;
    priority_queue<pli, vector<pli>, greater<pli>> pq;
    dist[1] = 0;
    cnt[1] = 1;
    pq.push({0, 1});
    while (!pq.empty()) {
        long long d = pq.top().first;
        int u = pq.top().second;
        pq.pop();
        if (d > dist[u]) continue;        // dist[u] dan cnt[u] sudah final
        for (auto& e : adj[u]) {
            int v = e.first;
            long long nd = d + e.second;
            if (nd < dist[v]) {           // rute lebih cepat: hitungan dimulai ulang
                dist[v] = nd;
                cnt[v] = cnt[u];
                pq.push({nd, v});
            } else if (nd == dist[v]) {   // rute lain yang sama cepat: tambahkan
                cnt[v] = (cnt[v] + cnt[u]) % MOD;
            }
        }
    }
    if (dist[n] == INF) cout << -1 << '\n';
    else cout << dist[n] << ' ' << cnt[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Min-heap sederhana berisi [jarak, simpul]
class Heap {
  constructor() { this.a = []; }
  push(x) {
    const a = this.a; a.push(x);
    let i = a.length - 1;
    while (i > 0) { const p = (i - 1) >> 1; if (a[p][0] <= a[i][0]) break; [a[p], a[i]] = [a[i], a[p]]; i = p; }
  }
  pop() {
    const a = this.a, top = a[0], last = a.pop();
    if (a.length) {
      a[0] = last;
      let i = 0;
      for (;;) {
        const l = 2 * i + 1, r = l + 1;
        let s = i;
        if (l < a.length && a[l][0] < a[s][0]) s = l;
        if (r < a.length && a[r][0] < a[s][0]) s = r;
        if (s === i) break;
        [a[s], a[i]] = [a[i], a[s]]; i = s;
      }
    }
    return top;
  }
  get size() { return this.a.length; }
}

const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
  adj[v].push([u, w]);
}
const MOD = 1000000007;
const dist = new Array(n + 1).fill(Infinity), cnt = new Array(n + 1).fill(0);
dist[1] = 0; cnt[1] = 1;
const pq = new Heap();
pq.push([0, 1]);
while (pq.size) {
  const [d, u] = pq.pop();
  if (d > dist[u]) continue;
  for (const [v, w] of adj[u]) {
    const nd = d + w;
    if (nd < dist[v]) { dist[v] = nd; cnt[v] = cnt[u]; pq.push([nd, v]); }
    else if (nd === dist[v]) cnt[v] = (cnt[v] + cnt[u]) % MOD;
  }
}
console.log(dist[n] === Infinity ? -1 : dist[n] + " " + cnt[n]);
CODE,
            'python' => <<<'CODE'
import sys, heapq
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))
    adj[v].append((u, w))

MOD = 10**9 + 7
INF = float("inf")
dist = [INF] * (n + 1)
cnt = [0] * (n + 1)
dist[1] = 0
cnt[1] = 1
pq = [(0, 1)]
while pq:
    d, u = heapq.heappop(pq)
    if d > dist[u]:
        continue
    for v, w in adj[u]:
        nd = d + w
        if nd < dist[v]:
            dist[v] = nd
            cnt[v] = cnt[u]
            heapq.heappush(pq, (nd, v))
        elif nd == dist[v]:
            cnt[v] = (cnt[v] + cnt[u]) % MOD
print(-1 if dist[n] == INF else f"{dist[n]} {cnt[n]}")
CODE,
        ],
        'editorial' => '<p>Jalankan Dijkstra biasa sambil menyimpan <code>cnt[v]</code> = banyak rute tercepat ke v. Saat merelaksasi sisi u → v dari simpul u yang sudah final:</p>
<ul><li>Jika <code>dist[u] + w &lt; dist[v]</code>: ditemukan waktu yang lebih baik, semua rute lama tidak berlaku lagi. <code>cnt[v] = cnt[u]</code>.</li>
<li>Jika <code>dist[u] + w == dist[v]</code>: rute lain yang sama cepat. <code>cnt[v] += cnt[u]</code>.</li></ul>
<p>Mengapa benar? Karena bobot ≥ 1, setiap simpul sebelum v di rute tercepat punya jarak lebih kecil, sehingga sudah final (dan <code>cnt</code>-nya lengkap) sebelum v diambil dari antrian. Perhatikan bahwa hitungan ditambahkan hanya dari simpul yang <em>diambil</em> dari antrian (bukan entri basi).</p>
<p><strong>Awas:</strong> waktu bisa mencapai 10<sup>14</sup>, gunakan <code>long long</code>. <strong>Kompleksitas:</strong> O((N + M) log N).</p>',
        'hints' => [
            'Mulai dari Dijkstra biasa. Informasi tambahan apa yang perlu disimpan untuk setiap simpul?',
            'Simpan cnt[v] = banyak rute tercepat ke v. Bagaimana cnt berubah saat dist[v] mengecil? Dan saat sama?',
            'Lebih kecil: cnt[v] = cnt[u]. Sama: cnt[v] += cnt[u]. Ambil modulo, dan pakai long long untuk jarak.',
        ],
        'sample_visual' => 'wgraph',
    ],

    [
        'slug' => 'evakuasi',
        'lesson' => 'dijkstra',
        'title' => 'Jalur Evakuasi',
        'difficulty' => 'Sedang',
        'tags' => ['dijkstra', 'multi-source'],
        'statement' => '<p>Sebuah kabupaten punya <strong>N</strong> desa dan <strong>M</strong> jalan dua arah berbobot (waktu tempuh dalam menit). <strong>K</strong> desa di antaranya punya tempat pengungsian.</p>
<p>Untuk persiapan bencana, hitung untuk <strong>setiap</strong> desa waktu tempuh ke tempat pengungsian <strong>terdekat</strong>. Desa yang tidak bisa mencapai pengungsian mana pun ditulis <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M K</code>. Baris kedua berisi K nomor desa pengungsian. M baris berikutnya berisi <code>u v w</code>.</p>',
        'output_format' => '<p>N bilangan dipisah spasi: waktu ke pengungsian terdekat untuk desa 1 sampai N.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li><li>1 ≤ w ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "6 6 2\n1 6\n1 2 4\n2 3 1\n3 4 2\n4 5 5\n5 6 1\n2 5 10\n", 'explanation' => 'Desa 3 berjarak 5 ke desa 1 (lewat 2) dan 8 ke desa 6, jadi jawabannya 5. Desa 4 berjarak 7 ke desa 1 dan 6 ke desa 6.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $k, bool $conn = true) {
                $edges = T::edges($n, min($m, intdiv($n * ($n - 1), 2)), false, $conn, [1, 1000000]);
                $all = range(1, $n);
                T::shuffle($all);

                return "$n ".count($edges)." $k\n".implode(' ', array_slice($all, 0, $k))."\n".implode("\n", array_map(fn ($e) => implode(' ', $e), $edges))."\n";
            };

            return ["1 0 1\n1\n", $mk(8, 10, 2), $mk(100, 300, 5, false), $mk(1000, 3000, 1), $mk(100000, 200000, 10), $mk(100000, 150000, 1000, false), $mk(100000, 99999, 1)];
        },
        'solve' => function (string $input) use ($dijkstraPhp) {
            $lines = T::lines($input);
            [$n, $m, $k] = T::ints($lines[0]);
            $src = T::ints($lines[1]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 0; $i < $m; $i++) {
                [$u, $v, $w] = T::ints($lines[2 + $i]);
                $adj[$u][] = [$v, $w];
                $adj[$v][] = [$u, $w];
            }
            $dist = $dijkstraPhp($n, $adj, $src);

            return implode(' ', array_map(fn ($d) => $d === PHP_INT_MAX ? -1 : $d, array_slice($dist, 1)));
        },
        'starter' => $st("    int n, m, k;\n    cin >> n >> m >> k;\n    vector<int> pengungsian(k);\n    for (auto& x : pengungsian) cin >> x;\n    vector<vector<pair<int, long long>>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        long long w;\n        cin >> u >> v >> w;\n        adj[u].push_back({v, w});\n        adj[v].push_back({u, w});\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, k;
    cin >> n >> m >> k;
    vector<int> pengungsian(k);
    for (auto& x : pengungsian) cin >> x;
    vector<vector<pair<int, long long>>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w});
        adj[v].push_back({u, w});
    }

    // Dijkstra multi-sumber: semua pengungsian mulai dengan jarak 0
    vector<long long> dist(n + 1, INF);
    typedef pair<long long, int> pli;
    priority_queue<pli, vector<pli>, greater<pli>> pq;
    for (int s : pengungsian) {
        dist[s] = 0;
        pq.push({0, s});
    }
    while (!pq.empty()) {
        long long d = pq.top().first;
        int u = pq.top().second;
        pq.pop();
        if (d > dist[u]) continue;
        for (auto& e : adj[u]) {
            if (d + e.second < dist[e.first]) {
                dist[e.first] = d + e.second;
                pq.push({dist[e.first], e.first});
            }
        }
    }
    for (int v = 1; v <= n; v++) cout << (dist[v] == INF ? -1 : dist[v]) << (v < n ? ' ' : '\n');
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
class Heap {
  constructor() { this.a = []; }
  push(x) {
    const a = this.a; a.push(x);
    let i = a.length - 1;
    while (i > 0) { const p = (i - 1) >> 1; if (a[p][0] <= a[i][0]) break; [a[p], a[i]] = [a[i], a[p]]; i = p; }
  }
  pop() {
    const a = this.a, top = a[0], last = a.pop();
    if (a.length) {
      a[0] = last;
      let i = 0;
      for (;;) {
        const l = 2 * i + 1, r = l + 1;
        let s = i;
        if (l < a.length && a[l][0] < a[s][0]) s = l;
        if (r < a.length && a[r][0] < a[s][0]) s = r;
        if (s === i) break;
        [a[s], a[i]] = [a[i], a[s]]; i = s;
      }
    }
    return top;
  }
  get size() { return this.a.length; }
}

const [n, m, k] = readInts();
const src = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
  adj[v].push([u, w]);
}
const dist = new Array(n + 1).fill(Infinity);
const pq = new Heap();
for (const s of src) { dist[s] = 0; pq.push([0, s]); }
while (pq.size) {
  const [d, u] = pq.pop();
  if (d > dist[u]) continue;
  for (const [v, w] of adj[u]) if (d + w < dist[v]) { dist[v] = d + w; pq.push([dist[v], v]); }
}
console.log(dist.slice(1).map((d) => (d === Infinity ? -1 : d)).join(" "));
CODE,
            'python' => <<<'CODE'
import sys, heapq
input = sys.stdin.readline

n, m, k = map(int, input().split())
src = list(map(int, input().split()))
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))
    adj[v].append((u, w))

INF = float("inf")
dist = [INF] * (n + 1)
pq = []
for s in src:
    dist[s] = 0
    pq.append((0, s))
heapq.heapify(pq)
while pq:
    d, u = heapq.heappop(pq)
    if d > dist[u]:
        continue
    for v, w in adj[u]:
        if d + w < dist[v]:
            dist[v] = d + w
            heapq.heappush(pq, (dist[v], v))
print(" ".join(str(-1 if d == INF else d) for d in dist[1:]))
CODE,
        ],
        'editorial' => '<p>Menjalankan Dijkstra dari setiap desa (atau dari setiap pengungsian) terlalu lambat. Triknya sama dengan BFS multi-sumber: masukkan <strong>semua</strong> pengungsian ke priority queue dengan jarak 0 sekaligus. Bayangkan ada simpul super yang terhubung ke semua pengungsian dengan bobot 0.</p>
<p>Setelah Dijkstra selesai, <code>dist[v]</code> adalah jarak ke pengungsian terdekat.</p>
<p><strong>Kompleksitas:</strong> O((N + M) log N).</p>',
        'hints' => [
            'Dijkstra dari setiap desa terlalu lambat. Bisakah semua pengungsian diproses sekaligus?',
            'Masukkan semua pengungsian ke priority queue dengan jarak 0 di awal.',
            'Hasil Dijkstra itu adalah jarak ke pengungsian terdekat. Cetak −1 untuk yang tetap tak hingga.',
        ],
    ],
];
