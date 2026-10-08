<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal tambahan track Graph: MST & Union-Find, Floyd-Warshall & Bellman-Ford, LCA.
 * Semua kode C++ harus lolos C++14 (GCC 6.3): tanpa structured binding.
 */

/** Pohon acak berakar di 1 dengan N simpul, lalu Q pertanyaan pasangan simpul. */
$treeQueries = function (int $n, int $q, bool $line = false): string {
    $edges = [];
    if ($line) {
        $order = range(1, $n);
        T::shuffle($order);
        for ($i = 1; $i < $n; $i++) {
            $edges[] = [$order[$i - 1], $order[$i]];
        }
    } else {
        $edges = $n > 1 ? T::edges($n, $n - 1, false, true) : [];
    }
    $out = [(string) $n];
    foreach ($edges as $e) {
        $out[] = "{$e[0]} {$e[1]}";
    }
    $out[] = (string) $q;
    for ($i = 0; $i < $q; $i++) {
        $out[] = mt_rand(1, $n).' '.mt_rand(1, $n);
    }

    return implode("\n", $out)."\n";
};

/** Membaca pohon + pertanyaan, lalu menyiapkan binary lifting. */
$lcaSolver = function (string $input): array {
    $lines = T::lines($input);
    $n = (int) $lines[0];
    $adj = array_fill(0, $n + 1, []);
    for ($i = 1; $i < $n; $i++) {
        [$u, $v] = T::ints($lines[$i]);
        $adj[$u][] = $v;
        $adj[$v][] = $u;
    }
    $LOG = 1;
    while ((1 << $LOG) < $n) {
        $LOG++;
    }
    $depth = array_fill(0, $n + 1, -1);
    $up = array_fill(0, $LOG + 1, array_fill(0, $n + 1, 1));
    $depth[1] = 0;
    $queue = [1];
    for ($h = 0; $h < count($queue); $h++) {
        $u = $queue[$h];
        foreach ($adj[$u] as $v) {
            if ($depth[$v] === -1) {
                $depth[$v] = $depth[$u] + 1;
                $up[0][$v] = $u;
                $queue[] = $v;
            }
        }
    }
    for ($k = 1; $k <= $LOG; $k++) {
        for ($v = 1; $v <= $n; $v++) {
            $up[$k][$v] = $up[$k - 1][$up[$k - 1][$v]];
        }
    }
    $lca = function (int $a, int $b) use ($depth, $up, $LOG): int {
        if ($depth[$a] < $depth[$b]) {
            [$a, $b] = [$b, $a];
        }
        $diff = $depth[$a] - $depth[$b];
        for ($k = 0; $k <= $LOG; $k++) {
            if (($diff >> $k) & 1) {
                $a = $up[$k][$a];
            }
        }
        if ($a === $b) {
            return $a;
        }
        for ($k = $LOG; $k >= 0; $k--) {
            if ($up[$k][$a] !== $up[$k][$b]) {
                $a = $up[$k][$a];
                $b = $up[$k][$b];
            }
        }

        return $up[0][$a];
    };
    $q = (int) $lines[$n];
    $queries = [];
    for ($i = 0; $i < $q; $i++) {
        $queries[] = T::ints($lines[$n + 1 + $i]);
    }

    return [$lca, $depth, $queries];
};

$lcaReadJs = <<<'CODE'
const [n] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const [q] = readInts();
CODE;

$lcaReadPy = <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)
q = int(input())
CODE;

$lcaReadCpp = <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
    int q;
    cin >> q;
CODE;

/** Bagian binary lifting yang sama untuk dua soal LCA (C++). */
$lcaCoreCpp = <<<'CODE'

    int LOG = 1;
    while ((1 << LOG) < n) LOG++;
    vector<int> depth(n + 1, -1);
    vector<vector<int>> up(LOG + 1, vector<int>(n + 1, 1));
    // BFS dari akar 1 (bukan rekursi, agar aman untuk pohon berbentuk garis)
    queue<int> bfs;
    bfs.push(1);
    depth[1] = 0;
    while (!bfs.empty()) {
        int u = bfs.front();
        bfs.pop();
        for (int v : adj[u]) {
            if (depth[v] == -1) {
                depth[v] = depth[u] + 1;
                up[0][v] = u;
                bfs.push(v);
            }
        }
    }
    for (int k = 1; k <= LOG; k++)
        for (int v = 1; v <= n; v++) up[k][v] = up[k - 1][up[k - 1][v]];

    auto lca = [&](int a, int b) {
        if (depth[a] < depth[b]) swap(a, b);
        int diff = depth[a] - depth[b];
        for (int k = 0; k <= LOG; k++)
            if (diff >> k & 1) a = up[k][a];
        if (a == b) return a;
        for (int k = LOG; k >= 0; k--)
            if (up[k][a] != up[k][b]) {
                a = up[k][a];
                b = up[k][b];
            }
        return up[0][a];
    };
CODE;

$lcaCoreJs = <<<'CODE'

let LOG = 1;
while ((1 << LOG) < n) LOG++;
const depth = new Array(n + 1).fill(-1);
const up = Array.from({ length: LOG + 1 }, () => new Array(n + 1).fill(1));
depth[1] = 0;
const order = [1];
for (let h = 0; h < order.length; h++) {
  const u = order[h];
  for (const v of adj[u]) {
    if (depth[v] === -1) {
      depth[v] = depth[u] + 1;
      up[0][v] = u;
      order.push(v);
    }
  }
}
for (let k = 1; k <= LOG; k++)
  for (let v = 1; v <= n; v++) up[k][v] = up[k - 1][up[k - 1][v]];

function lca(a, b) {
  if (depth[a] < depth[b]) [a, b] = [b, a];
  const diff = depth[a] - depth[b];
  for (let k = 0; k <= LOG; k++) if ((diff >> k) & 1) a = up[k][a];
  if (a === b) return a;
  for (let k = LOG; k >= 0; k--) {
    if (up[k][a] !== up[k][b]) { a = up[k][a]; b = up[k][b]; }
  }
  return up[0][a];
}
CODE;

$lcaCorePy = <<<'CODE'

LOG = 1
while (1 << LOG) < n:
    LOG += 1
depth = [-1] * (n + 1)
up = [[1] * (n + 1) for _ in range(LOG + 1)]
depth[1] = 0
order = [1]
for u in order:
    for v in adj[u]:
        if depth[v] == -1:
            depth[v] = depth[u] + 1
            up[0][v] = u
            order.append(v)
for k in range(1, LOG + 1):
    prev, cur = up[k - 1], up[k]
    for v in range(1, n + 1):
        cur[v] = prev[prev[v]]

def lca(a, b):
    if depth[a] < depth[b]:
        a, b = b, a
    diff = depth[a] - depth[b]
    k = 0
    while diff:
        if diff & 1:
            a = up[k][a]
        diff >>= 1
        k += 1
    if a == b:
        return a
    for k in range(LOG, -1, -1):
        if up[k][a] != up[k][b]:
            a = up[k][a]
            b = up[k][b]
    return up[0][a]
CODE;

return [
    // ═════════════════════════════ MST & Union-Find ═════════════════════════════
    [
        'slug' => 'grup-pertemanan',
        'lesson' => 'mst',
        'title' => 'Grup Pertemanan',
        'difficulty' => 'Mudah',
        'tags' => ['graph', 'union-find', 'dsu'],
        'statement' => '<p>Di sebuah aplikasi pesan ada <strong>N</strong> pengguna, bernomor 1 sampai N. Awalnya tidak ada yang berteman.</p>
<p>Kamu menerima <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 u v</code>: pengguna u dan v menjadi teman.</li>
<li><code>2 u v</code>: apakah u dan v berada di <strong>grup yang sama</strong>? Dua pengguna satu grup jika terhubung lewat rantai pertemanan (teman, teman dari teman, dan seterusnya).</li></ul>
<p>Jawab setiap kejadian jenis 2 dengan <code>YA</code> atau <code>TIDAK</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. <code>Q</code> baris berikutnya masing-masing berisi <code>t u v</code>.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, satu baris berisi <code>YA</code> atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ Q ≤ 200 000</li><li>t ∈ {1, 2}, 1 ≤ u, v ≤ N</li><li>Setidaknya ada satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "5 6\n2 1 2\n1 1 2\n1 3 4\n2 1 2\n1 2 3\n2 1 4\n", 'explanation' => 'Awalnya 1 dan 2 belum terhubung (TIDAK). Setelah 1–2, 3–4, lalu 2–3, pengguna 1 dan 4 terhubung lewat 1 – 2 – 3 – 4.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $q, float $askProb) {
                $out = ["$n $q"];
                $hasAsk = false;
                for ($i = 0; $i < $q; $i++) {
                    $t = ($i === $q - 1 && ! $hasAsk) || mt_rand() / mt_getrandmax() < $askProb ? 2 : 1;
                    $hasAsk = $hasAsk || $t === 2;
                    $out[] = "$t ".mt_rand(1, $n).' '.mt_rand(1, $n);
                }

                return implode("\n", $out)."\n";
            };

            return ["1 1\n2 1 1\n", "2 2\n1 1 2\n2 2 1\n", $mk(10, 20, 0.5), $mk(100, 300, 0.4), $mk(1000, 5000, 0.5), $mk(200000, 200000, 0.5), $mk(200000, 200000, 0.1), $mk(50000, 200000, 0.7)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $p = range(0, $n);
            $find = function (int $x) use (&$p): int {
                $r = $x;
                while ($p[$r] !== $r) {
                    $r = $p[$r];
                }
                while ($p[$x] !== $r) {
                    $next = $p[$x];
                    $p[$x] = $r;
                    $x = $next;
                }

                return $r;
            };
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                [$t, $u, $v] = T::ints($lines[$i]);
                $a = $find($u);
                $b = $find($v);
                if ($t === 1) {
                    $p[$a] = $b;
                } else {
                    $out[] = $a === $b ? 'YA' : 'TIDAK';
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

    int n, q;
    cin >> n >> q;
    for (int i = 0; i < q; i++) {
        int t, u, v;
        cin >> t >> u >> v;
        // Tulis solusimu di sini
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const out = [];
for (let i = 0; i < q; i++) {
  const [t, u, v] = readInts();
  // Tulis solusimu di sini
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
out = []
for _ in range(q):
    t, u, v = map(int, input().split())
    # Tulis solusimu di sini

print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<int> parent, sz;

int find(int x) {
    while (parent[x] != x) {
        parent[x] = parent[parent[x]];  // path halving
        x = parent[x];
    }
    return x;
}

void unite(int a, int b) {
    a = find(a);
    b = find(b);
    if (a == b) return;
    if (sz[a] < sz[b]) swap(a, b);       // union by size
    parent[b] = a;
    sz[a] += sz[b];
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    parent.resize(n + 1);
    sz.assign(n + 1, 1);
    iota(parent.begin(), parent.end(), 0);

    string out;
    for (int i = 0; i < q; i++) {
        int t, u, v;
        cin >> t >> u >> v;
        if (t == 1) unite(u, v);
        else out += find(u) == find(v) ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const parent = Array.from({ length: n + 1 }, (_, i) => i);
const sz = new Array(n + 1).fill(1);
function find(x) {
  while (parent[x] !== x) {
    parent[x] = parent[parent[x]];
    x = parent[x];
  }
  return x;
}
const out = [];
for (let i = 0; i < q; i++) {
  const [t, u, v] = readInts();
  let a = find(u), b = find(v);
  if (t === 1) {
    if (a !== b) {
      if (sz[a] < sz[b]) [a, b] = [b, a];
      parent[b] = a;
      sz[a] += sz[b];
    }
  } else {
    out.push(a === b ? "YA" : "TIDAK");
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
parent = list(range(n + 1))
sz = [1] * (n + 1)

def find(x):
    while parent[x] != x:
        parent[x] = parent[parent[x]]
        x = parent[x]
    return x

out = []
for _ in range(q):
    t, u, v = map(int, input().split())
    a, b = find(u), find(v)
    if t == 1:
        if a != b:
            if sz[a] < sz[b]:
                a, b = b, a
            parent[b] = a
            sz[a] += sz[b]
    else:
        out.append("YA" if a == b else "TIDAK")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Ini adalah soal <strong>Union-Find</strong> (Disjoint Set Union) murni. Setiap grup diwakili satu "ketua". <code>find(x)</code> mencari ketua grup x, dan <code>unite(a, b)</code> menyatukan dua grup dengan menjadikan ketua yang satu bawahan ketua lainnya.</p>
<p>Dua pengguna satu grup jika dan hanya jika ketuanya sama.</p>
<p>Agar cepat, pakai dua optimasi: <strong>path compression</strong> (atau path halving) saat mencari ketua, dan <strong>union by size</strong> (grup kecil digabung ke grup besar). Dengan keduanya, setiap operasi hampir O(1).</p>
<p><strong>Kompleksitas:</strong> O((N + Q) · α(N)).</p>',
        'hints' => [
            'Mencari jalur dengan BFS untuk setiap pertanyaan terlalu lambat (Q × N).',
            'Simpan untuk setiap pengguna siapa "ketua" grupnya. Dua pengguna satu grup jika ketuanya sama.',
            'Saat menggabungkan, cukup jadikan ketua grup yang satu sebagai bawahan ketua grup lainnya. Pakai path compression agar pencarian ketua tetap cepat.',
        ],
    ],

    [
        'slug' => 'listrik-desa',
        'lesson' => 'mst',
        'title' => 'Jaringan Listrik Desa',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'mst', 'kruskal'],
        'statement' => '<p>Pemerintah kabupaten ingin mengalirkan listrik ke <strong>N</strong> desa. Ada <strong>M</strong> rencana jalur kabel; jalur ke-i menghubungkan desa <code>u</code> dan <code>v</code> dengan biaya <code>w</code> juta rupiah. Listrik bisa mengalir dua arah dan melewati desa lain.</p>
<p>Pilih sebagian jalur sehingga <strong>semua desa saling terhubung</strong> dengan <strong>total biaya minimum</strong>. Jika mustahil menghubungkan semua desa, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. <code>M</code> baris berikutnya berisi <code>u v w</code>.</p>',
        'output_format' => '<p>Total biaya minimum, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li><li>1 ≤ u, v ≤ N, u ≠ v</li><li>1 ≤ w ≤ 1 000 000</li></ul>',
        'samples' => [
            ['input' => "5 7\n1 2 4\n1 3 2\n2 3 1\n2 4 5\n3 4 8\n3 5 10\n4 5 2\n", 'explanation' => 'Pilih jalur 2–3 (1), 1–3 (2), 4–5 (2), dan 2–4 (5). Totalnya 10.'],
            ['input' => "4 2\n1 2 3\n3 4 1\n", 'explanation' => 'Desa {1, 2} dan {3, 4} tidak mungkin dihubungkan.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, bool $connected, int $maxW = 1000000) {
                return T::graphInput("$n ".min($m, intdiv($n * ($n - 1), 2)), T::edges($n, $m, false, $connected, [1, $maxW]));
            };

            return ["1 0\n", "2 1\n1 2 7\n", "3 1\n1 2 5\n", $mk(10, 20, true, 20), $mk(100, 500, true), $mk(1000, 1500, false), $mk(100000, 200000, true), $mk(100000, 99999, true), $mk(50000, 60000, false, 5)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $edges = [];
            for ($i = 1; $i <= $m; $i++) {
                $edges[] = T::ints($lines[$i]);
            }
            usort($edges, fn ($a, $b) => $a[2] <=> $b[2]);
            $p = range(0, $n);
            $find = function (int $x) use (&$p): int {
                while ($p[$x] !== $x) {
                    $p[$x] = $p[$p[$x]];
                    $x = $p[$x];
                }

                return $x;
            };
            $total = 0;
            $used = 0;
            foreach ($edges as [$u, $v, $w]) {
                $a = $find($u);
                $b = $find($v);
                if ($a !== $b) {
                    $p[$a] = $b;
                    $total += $w;
                    $used++;
                }
            }

            return (string) ($used === $n - 1 ? $total : -1);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<array<int, 3>> edges(m);  // {w, u, v}
    for (int i = 0; i < m; i++) cin >> edges[i][1] >> edges[i][2] >> edges[i][0];

    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const edges = [];
for (let i = 0; i < m; i++) edges.push(readInts()); // [u, v, w]

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
edges = [tuple(map(int, input().split())) for _ in range(m)]  # (u, v, w)

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<int> parent;

int find(int x) {
    while (parent[x] != x) {
        parent[x] = parent[parent[x]];
        x = parent[x];
    }
    return x;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<array<int, 3>> edges(m);  // {w, u, v}
    for (int i = 0; i < m; i++) cin >> edges[i][1] >> edges[i][2] >> edges[i][0];

    // Kruskal: coba sisi dari yang termurah
    sort(edges.begin(), edges.end());
    parent.resize(n + 1);
    iota(parent.begin(), parent.end(), 0);

    long long total = 0;
    int dipakai = 0;
    for (auto& e : edges) {
        int a = find(e[1]), b = find(e[2]);
        if (a != b) {           // tidak membentuk siklus
            parent[a] = b;
            total += e[0];
            dipakai++;
        }
    }
    cout << (dipakai == n - 1 ? total : -1) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const edges = [];
for (let i = 0; i < m; i++) edges.push(readInts());
edges.sort((a, b) => a[2] - b[2]);

const parent = Array.from({ length: n + 1 }, (_, i) => i);
function find(x) {
  while (parent[x] !== x) {
    parent[x] = parent[parent[x]];
    x = parent[x];
  }
  return x;
}

let total = 0, dipakai = 0;
for (const [u, v, w] of edges) {
  const a = find(u), b = find(v);
  if (a !== b) {
    parent[a] = b;
    total += w;
    dipakai++;
  }
}
console.log(dipakai === n - 1 ? total : -1);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
edges = [tuple(map(int, input().split())) for _ in range(m)]
edges.sort(key=lambda e: e[2])

parent = list(range(n + 1))
def find(x):
    while parent[x] != x:
        parent[x] = parent[parent[x]]
        x = parent[x]
    return x

total = dipakai = 0
for u, v, w in edges:
    a, b = find(u), find(v)
    if a != b:
        parent[a] = b
        total += w
        dipakai += 1
print(total if dipakai == n - 1 else -1)
CODE,
        ],
        'editorial' => '<p>Kita mencari <strong>Minimum Spanning Tree</strong>. Algoritma Kruskal: urutkan semua sisi dari yang termurah, lalu ambil sisi satu per satu <em>selama tidak membentuk siklus</em>. Pengecekan siklus dilakukan dengan Union-Find: sisi u–v membentuk siklus jika u dan v sudah satu komponen.</p>
<p>Spanning tree untuk N simpul selalu punya tepat N − 1 sisi. Jika setelah semua sisi diproses kita memakai kurang dari N − 1 sisi, graph-nya tidak terhubung dan jawabannya −1.</p>
<p><strong>Awas:</strong> total biaya bisa mencapai 99 999 × 10<sup>6</sup> ≈ 10<sup>11</sup>, melebihi <code>int</code>. Pakai <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(M log M) untuk pengurutan.</p>',
        'hints' => [
            'Soal ini meminta Minimum Spanning Tree: N − 1 sisi yang menghubungkan semua simpul dengan total bobot minimum.',
            'Urutkan sisi dari yang termurah. Ambil sebuah sisi jika kedua ujungnya belum terhubung (cek dengan Union-Find).',
            'Hitung berapa sisi yang terambil. Jika kurang dari N − 1, cetak −1. Jangan lupa memakai long long untuk totalnya.',
        ],
        'sample_visual' => 'wgraph',
    ],

    // ═════════════════════════════ Floyd-Warshall & Bellman-Ford ═════════════════════════════
    [
        'slug' => 'jarak-antar-kota',
        'lesson' => 'floyd-warshall',
        'title' => 'Tabel Jarak Antar Kota',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'floyd-warshall', 'shortest path'],
        'statement' => '<p>Sebuah aplikasi peta ingin menampilkan jarak terpendek antar kota. Ada <strong>N</strong> kota dan <strong>M</strong> ruas jalan <strong>satu arah</strong>; ruas ke-i dari kota <code>u</code> ke kota <code>v</code> panjangnya <code>w</code> km. Bisa ada beberapa ruas untuk pasangan kota yang sama.</p>
<p>Jawab <strong>Q</strong> pertanyaan: berapa jarak terpendek dari kota <code>a</code> ke kota <code>b</code>? Jika tidak ada rute, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M Q</code>. <code>M</code> baris berikutnya berisi <code>u v w</code>. <code>Q</code> baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing jarak terpendek atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 150</li><li>0 ≤ M ≤ 10 000</li><li>1 ≤ Q ≤ 20 000</li><li>1 ≤ u, v, a, b ≤ N</li><li>1 ≤ w ≤ 1 000 000</li></ul>',
        'samples' => [
            ['input' => "4 5 4\n1 2 5\n2 3 2\n1 3 9\n3 4 1\n4 1 3\n1 3\n3 1\n2 2\n1 4\n", 'explanation' => '1 → 3 lebih pendek lewat 2 (5 + 2 = 7) daripada langsung (9). 3 → 1 lewat 4: 1 + 3 = 4. Jarak kota ke dirinya sendiri 0. 1 → 4: 7 + 1 = 8.'],
            ['input' => "3 1 2\n1 2 4\n2 1\n1 2\n", 'explanation' => 'Jalan hanya satu arah, jadi tidak ada rute dari 2 ke 1.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $q, int $maxW = 1000000) {
                $out = ["$n $m $q"];
                for ($i = 0; $i < $m; $i++) {
                    $u = mt_rand(1, $n);
                    $v = mt_rand(1, $n);
                    if ($u === $v) {
                        $v = $v % $n + 1;
                    }
                    $out[] = "$u $v ".mt_rand(1, $maxW);
                }
                for ($i = 0; $i < $q; $i++) {
                    $out[] = mt_rand(1, $n).' '.mt_rand(1, $n);
                }

                return implode("\n", $out)."\n";
            };

            return ["1 0 1\n1 1\n", $mk(5, 6, 10, 10), $mk(30, 60, 200), $mk(100, 300, 2000), $mk(150, 10000, 20000), $mk(150, 400, 20000), $mk(150, 2000, 20000, 3)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m, $q] = T::ints($lines[0]);
            $INF = PHP_INT_MAX;
            $d = array_fill(1, $n, array_fill(1, $n, $INF));
            for ($i = 1; $i <= $n; $i++) {
                $d[$i][$i] = 0;
            }
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v, $w] = T::ints($lines[$i]);
                if ($u !== $v && $w < $d[$u][$v]) {
                    $d[$u][$v] = $w;
                }
            }
            for ($k = 1; $k <= $n; $k++) {
                $dk = $d[$k];
                for ($i = 1; $i <= $n; $i++) {
                    $dik = $d[$i][$k];
                    if ($dik === $INF) {
                        continue;
                    }
                    $row = &$d[$i];
                    foreach ($dk as $j => $kj) {
                        if ($kj !== $INF && $dik + $kj < $row[$j]) {
                            $row[$j] = $dik + $kj;
                        }
                    }
                    unset($row);
                }
            }
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                [$a, $b] = T::ints($lines[$m + 1 + $i]);
                $out[] = $d[$a][$b] === $INF ? -1 : $d[$a][$b];
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

    int n, m, q;
    cin >> n >> m >> q;
    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m, q] = readInts();
const roads = [];
for (let i = 0; i < m; i++) roads.push(readInts()); // [u, v, w]

// Tulis solusimu di sini

const out = [];
for (let i = 0; i < q; i++) {
  const [a, b] = readInts();
  // out.push(...)
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m, q = map(int, input().split())
roads = [tuple(map(int, input().split())) for _ in range(m)]  # (u, v, w)

# Tulis solusimu di sini

out = []
for _ in range(q):
    a, b = map(int, input().split())
    # out.append(...)
print("\n".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long INF = 1e18;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, q;
    cin >> n >> m >> q;
    vector<vector<long long>> d(n + 1, vector<long long>(n + 1, INF));
    for (int i = 1; i <= n; i++) d[i][i] = 0;
    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        d[u][v] = min(d[u][v], w);   // bisa ada beberapa ruas u -> v
    }

    // k = kota perantara yang boleh dipakai; HARUS loop paling luar
    for (int k = 1; k <= n; k++)
        for (int i = 1; i <= n; i++) {
            if (d[i][k] == INF) continue;
            for (int j = 1; j <= n; j++)
                if (d[k][j] != INF && d[i][k] + d[k][j] < d[i][j])
                    d[i][j] = d[i][k] + d[k][j];
        }

    string out;
    for (int i = 0; i < q; i++) {
        int a, b;
        cin >> a >> b;
        out += to_string(d[a][b] == INF ? -1 : d[a][b]) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m, q] = readInts();
const INF = Infinity;
const d = Array.from({ length: n + 1 }, () => new Array(n + 1).fill(INF));
for (let i = 1; i <= n; i++) d[i][i] = 0;
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  if (w < d[u][v]) d[u][v] = w;
}
for (let k = 1; k <= n; k++) {
  const dk = d[k];
  for (let i = 1; i <= n; i++) {
    const dik = d[i][k];
    if (dik === INF) continue;
    const di = d[i];
    for (let j = 1; j <= n; j++) {
      if (dik + dk[j] < di[j]) di[j] = dik + dk[j];
    }
  }
}
const out = [];
for (let i = 0; i < q; i++) {
  const [a, b] = readInts();
  out.push(d[a][b] === INF ? -1 : d[a][b]);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m, q = map(int, input().split())
INF = float("inf")
d = [[INF] * (n + 1) for _ in range(n + 1)]
for i in range(1, n + 1):
    d[i][i] = 0
for _ in range(m):
    u, v, w = map(int, input().split())
    if w < d[u][v]:
        d[u][v] = w

for k in range(1, n + 1):
    dk = d[k]
    for i in range(1, n + 1):
        dik = d[i][k]
        if dik == INF:
            continue
        di = d[i]
        for j in range(1, n + 1):
            if dik + dk[j] < di[j]:
                di[j] = dik + dk[j]

out = []
for _ in range(q):
    a, b = map(int, input().split())
    out.append(-1 if d[a][b] == INF else d[a][b])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Karena pertanyaannya banyak (sampai 20 000) dan kotanya sedikit (≤ 150), hitung <strong>semua</strong> jarak sekaligus dengan <strong>Floyd-Warshall</strong>, lalu jawab setiap pertanyaan dalam O(1).</p>
<p><code>d[i][j]</code> dimulai dari panjang ruas langsung (ambil yang terpendek jika ada beberapa), 0 untuk i = j, dan ∞ untuk lainnya. Lalu untuk setiap kota perantara k: <code>d[i][j] = min(d[i][j], d[i][k] + d[k][j])</code>. Loop <strong>k harus paling luar</strong>.</p>
<p>Jarak bisa mencapai 149 × 10<sup>6</sup>, masih muat di <code>int</code>, tetapi penjumlahan dua nilai ∞ bisa meluap. Lewati jika salah satunya ∞, atau pakai <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(N³ + Q).</p>',
        'hints' => [
            'Menjalankan Dijkstra untuk setiap pertanyaan bisa terlalu lambat. Karena N kecil, hitung jarak semua pasangan sekaligus.',
            'Floyd-Warshall: untuk setiap kota perantara k, perbarui d[i][j] dengan d[i][k] + d[k][j].',
            'Ingat: bisa ada beberapa ruas u → v, ambil yang terpendek. Dan d[i][i] = 0.',
        ],
    ],

    [
        'slug' => 'kurir-bonus',
        'lesson' => 'floyd-warshall',
        'title' => 'Kurir dan Jalan Bonus',
        'difficulty' => 'Sulit',
        'tags' => ['graph', 'bellman-ford', 'siklus negatif'],
        'statement' => '<p>Seorang kurir berangkat dari gudang di kota <strong>1</strong> menuju kota <strong>N</strong>. Ada <strong>M</strong> ruas jalan <strong>satu arah</strong>. Melewati ruas ke-i membutuhkan biaya bensin <code>w</code>. Beberapa ruas justru punya <code>w</code> negatif, karena kurir menerima bonus pengiriman di sepanjang ruas itu.</p>
<p>Kurir boleh melewati kota dan ruas yang sama berkali-kali. Tentukan <strong>biaya total minimum</strong> dari kota 1 ke kota N.</p>
<ul><li>Jika kota N tidak bisa dicapai, cetak <code>TIDAK ADA</code>.</li>
<li>Jika biayanya bisa dibuat <strong>sekecil-kecilnya tanpa batas</strong> (karena ada putaran yang selalu menguntungkan dan setelahnya kota N masih bisa dicapai), cetak <code>TAK TERBATAS</code>.</li></ul>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. <code>M</code> baris berikutnya berisi <code>u v w</code>.</p>',
        'output_format' => '<p>Biaya minimum, <code>TIDAK ADA</code>, atau <code>TAK TERBATAS</code>.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 1000</li><li>0 ≤ M ≤ 5000</li><li>1 ≤ u, v ≤ N</li><li>−10<sup>6</sup> ≤ w ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4 4\n1 2 5\n2 3 -3\n3 4 2\n1 4 10\n", 'explanation' => 'Rute 1 → 2 → 3 → 4 berbiaya 5 − 3 + 2 = 4, lebih murah dari ruas langsung (10).'],
            ['input' => "4 5\n1 2 1\n2 3 -2\n3 2 1\n3 4 5\n1 4 3\n", 'explanation' => 'Putaran 2 → 3 → 2 berbiaya −2 + 1 = −1. Kurir bisa berputar sebanyak mungkin lalu lanjut ke kota 4.'],
            ['input' => "3 1\n2 3 4\n", 'explanation' => 'Dari kota 1 tidak ada jalan keluar.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $neg, bool $allowCycle) {
                $out = ["$n $m"];
                // Tanpa siklus: semua ruas "maju" pada urutan acak (kota 1 pertama, kota N terakhir),
                // sehingga graph-nya DAG dan bobot negatif tidak bisa membentuk siklus.
                $mid = range(2, $n - 1);
                T::shuffle($mid);
                $rank = array_flip(array_merge([1], $mid, [$n]));
                for ($i = 0; $i < $m; $i++) {
                    do {
                        $u = mt_rand(1, $n);
                        $v = mt_rand(1, $n);
                    } while ($u === $v);
                    if (! $allowCycle && $rank[$u] > $rank[$v]) {
                        [$u, $v] = [$v, $u];
                    }
                    $w = mt_rand(1, 1000000) * (mt_rand(1, 100) <= $neg ? -1 : 1);
                    $out[] = "$u $v $w";
                }

                return implode("\n", $out)."\n";
            };

            return [
                "2 0\n", "2 1\n1 2 -5\n", "2 2\n1 2 3\n2 2 -1\n", "3 3\n1 2 1\n2 1 -1\n1 3 7\n", "3 2\n1 3 4\n2 2 -1\n",
                $mk(10, 25, 30, false), $mk(100, 600, 30, false), $mk(1000, 5000, 40, false), $mk(1000, 5000, 1, true),
                $mk(500, 1500, 5, true), $mk(1000, 3000, 50, false), $mk(1000, 1200, 20, true), $mk(300, 3000, 90, false),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $edges = [];
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                $e = T::ints($lines[$i]);
                $edges[] = $e;
                $adj[$e[0]][] = $e[1];
            }
            $INF = PHP_INT_MAX;
            $dist = array_fill(0, $n + 1, $INF);
            $dist[1] = 0;
            for ($r = 1; $r < $n; $r++) {
                $changed = false;
                foreach ($edges as [$u, $v, $w]) {
                    if ($dist[$u] !== $INF && $dist[$u] + $w < $dist[$v]) {
                        $dist[$v] = $dist[$u] + $w;
                        $changed = true;
                    }
                }
                if (! $changed) {
                    break;
                }
            }
            $bad = array_fill(0, $n + 1, false);
            $queue = [];
            foreach ($edges as [$u, $v, $w]) {
                if ($dist[$u] !== $INF && $dist[$u] + $w < $dist[$v] && ! $bad[$v]) {
                    $bad[$v] = true;
                    $queue[] = $v;
                }
            }
            for ($h = 0; $h < count($queue); $h++) {
                foreach ($adj[$queue[$h]] as $v) {
                    if (! $bad[$v]) {
                        $bad[$v] = true;
                        $queue[] = $v;
                    }
                }
            }
            if ($bad[$n]) {
                return 'TAK TERBATAS';
            }

            return $dist[$n] === $INF ? 'TIDAK ADA' : (string) $dist[$n];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> eu(m), ev(m);
    vector<long long> ew(m);
    for (int i = 0; i < m; i++) cin >> eu[i] >> ev[i] >> ew[i];

    // Tulis solusimu di sini

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const edges = [];
for (let i = 0; i < m; i++) edges.push(readInts()); // [u, v, w]

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
edges = [tuple(map(int, input().split())) for _ in range(m)]  # (u, v, w)

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> eu(m), ev(m);
    vector<long long> ew(m);
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        cin >> eu[i] >> ev[i] >> ew[i];
        adj[eu[i]].push_back(ev[i]);
    }

    // Bellman-Ford: N - 1 putaran relaksasi
    vector<long long> dist(n + 1, INF);
    dist[1] = 0;
    for (int r = 1; r < n; r++) {
        bool berubah = false;
        for (int i = 0; i < m; i++)
            if (dist[eu[i]] != INF && dist[eu[i]] + ew[i] < dist[ev[i]]) {
                dist[ev[i]] = dist[eu[i]] + ew[i];
                berubah = true;
            }
        if (!berubah) break;
    }

    // Simpul yang MASIH bisa diperbaiki terpengaruh siklus negatif.
    // Semua yang bisa dicapai dari simpul itu juga tak terbatas.
    vector<char> buruk(n + 1, 0);
    queue<int> q;
    for (int i = 0; i < m; i++)
        if (dist[eu[i]] != INF && dist[eu[i]] + ew[i] < dist[ev[i]] && !buruk[ev[i]]) {
            buruk[ev[i]] = 1;
            q.push(ev[i]);
        }
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        for (int v : adj[u])
            if (!buruk[v]) {
                buruk[v] = 1;
                q.push(v);
            }
    }

    if (buruk[n]) cout << "TAK TERBATAS\n";
    else if (dist[n] == INF) cout << "TIDAK ADA\n";
    else cout << dist[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const edges = [];
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const e = readInts();
  edges.push(e);
  adj[e[0]].push(e[1]);
}

const INF = Infinity;
const dist = new Array(n + 1).fill(INF);
dist[1] = 0;
for (let r = 1; r < n; r++) {
  let berubah = false;
  for (const [u, v, w] of edges) {
    if (dist[u] !== INF && dist[u] + w < dist[v]) { dist[v] = dist[u] + w; berubah = true; }
  }
  if (!berubah) break;
}

const buruk = new Array(n + 1).fill(false);
const q = [];
for (const [u, v, w] of edges) {
  if (dist[u] !== INF && dist[u] + w < dist[v] && !buruk[v]) { buruk[v] = true; q.push(v); }
}
for (let h = 0; h < q.length; h++) {
  for (const v of adj[q[h]]) if (!buruk[v]) { buruk[v] = true; q.push(v); }
}

if (buruk[n]) console.log("TAK TERBATAS");
else if (dist[n] === INF) console.log("TIDAK ADA");
else console.log(dist[n]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
edges = [tuple(map(int, input().split())) for _ in range(m)]
adj = [[] for _ in range(n + 1)]
for u, v, w in edges:
    adj[u].append(v)

INF = float("inf")
dist = [INF] * (n + 1)
dist[1] = 0
for _ in range(n - 1):
    berubah = False
    for u, v, w in edges:
        if dist[u] + w < dist[v]:
            dist[v] = dist[u] + w
            berubah = True
    if not berubah:
        break

buruk = [False] * (n + 1)
q = []
for u, v, w in edges:
    if dist[u] != INF and dist[u] + w < dist[v] and not buruk[v]:
        buruk[v] = True
        q.append(v)
for u in q:
    for v in adj[u]:
        if not buruk[v]:
            buruk[v] = True
            q.append(v)

if buruk[n]:
    print("TAK TERBATAS")
elif dist[n] == INF:
    print("TIDAK ADA")
else:
    print(dist[n])
CODE,
        ],
        'editorial' => '<p>Ada bobot negatif, jadi Dijkstra tidak bisa dipakai. Pakai <strong>Bellman-Ford</strong>: ulangi N − 1 kali, relaksasi setiap sisi. Jika tidak ada siklus negatif, setelah N − 1 putaran semua jarak sudah final.</p>
<p>Untuk mendeteksi masalah, lakukan satu putaran lagi. Sisi u → v yang <strong>masih</strong> bisa memperbaiki <code>dist[v]</code> berarti v dipengaruhi siklus negatif yang bisa dicapai dari kota 1. Tetapi siklus itu hanya relevan jika kota N bisa dicapai <em>dari</em> sana, jadi lakukan BFS dari semua simpul tersebut. Jika N ikut tertandai, jawabannya <code>TAK TERBATAS</code>.</p>
<p><strong>Jebakan:</strong> siklus negatif yang tidak terhubung ke N tidak membuat jawaban tak terbatas. Dan jangan merelaksasi dari simpul berjarak ∞ (∞ + bobot negatif tetap bukan jarak yang sah).</p>
<p><strong>Kompleksitas:</strong> O(N · M).</p>',
        'hints' => [
            'Dijkstra salah jika ada bobot negatif. Coba Bellman-Ford: relaksasi semua sisi sebanyak N − 1 kali.',
            'Setelah N − 1 putaran, sisi yang masih bisa memperbaiki jarak menandakan siklus negatif.',
            'Siklus negatif hanya penting jika kota N bisa dicapai darinya. Lakukan BFS dari simpul-simpul yang masih bisa diperbaiki.',
        ],
        'sample_visual' => 'wdigraph',
    ],

    // ═════════════════════════════ LCA ═════════════════════════════
    [
        'slug' => 'leluhur-bersama',
        'lesson' => 'lca',
        'title' => 'Leluhur Bersama',
        'difficulty' => 'Sedang',
        'tags' => ['tree', 'lca', 'binary lifting'],
        'statement' => '<p>Sebuah keluarga besar mencatat silsilahnya sebagai pohon dengan <strong>N</strong> anggota. Anggota nomor <strong>1</strong> adalah leluhur tertua (akar). Silsilah diberikan sebagai N − 1 pasangan orang tua–anak, tetapi urutan dalam pasangan tidak dijamin.</p>
<p>Ada <strong>Q</strong> pertanyaan: untuk dua anggota <code>u</code> dan <code>v</code>, siapa <strong>leluhur bersama terdekat</strong> mereka? Seseorang dianggap leluhur dari dirinya sendiri.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N − 1 baris berikutnya berisi <code>a b</code> (a dan b punya hubungan orang tua–anak). Baris berikutnya berisi <code>Q</code>, lalu Q baris berisi <code>u v</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing nomor leluhur bersama terdekat.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>1 ≤ Q ≤ 100 000</li><li>1 ≤ u, v ≤ N</li></ul>',
        'samples' => [
            ['input' => "7\n1 2\n1 3\n2 4\n2 5\n5 6\n3 7\n4\n4 6\n6 7\n5 2\n7 7\n", 'explanation' => 'Leluhur bersama terdekat 4 dan 6 adalah 2. Untuk 6 dan 7 adalah 1. Untuk 5 dan 2, jawabannya 2 sendiri karena 2 adalah leluhur 5.'],
        ],
        'tests' => function () use ($treeQueries) {
            return ["1\n1\n1 1\n", "2\n2 1\n2\n1 2\n2 2\n", $treeQueries(10, 20), $treeQueries(200, 500), $treeQueries(5000, 10000), $treeQueries(100000, 100000), $treeQueries(100000, 100000, true), $treeQueries(65536, 50000)];
        },
        'solve' => function (string $input) use ($lcaSolver) {
            [$lca, , $queries] = $lcaSolver($input);
            $out = [];
            foreach ($queries as [$u, $v]) {
                $out[] = $lca($u, $v);
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => $lcaReadCpp."\n    // Tulis solusimu di sini\n\n    return 0;\n}",
            'javascript' => $lcaReadJs."\n\n// Tulis solusimu di sini\nconst out = [];\nfor (let i = 0; i < q; i++) {\n  const [u, v] = readInts();\n  // out.push(...)\n}\nconsole.log(out.join(\"\\n\"));",
            'python' => $lcaReadPy."\n\n# Tulis solusimu di sini\nout = []\nfor _ in range(q):\n    u, v = map(int, input().split())\n    # out.append(...)\nprint(\"\\n\".join(map(str, out)))",
        ],
        'solutions' => [
            'cpp' => $lcaReadCpp.$lcaCoreCpp."\n\n    string out;\n    for (int i = 0; i < q; i++) {\n        int u, v;\n        cin >> u >> v;\n        out += to_string(lca(u, v)) + \"\\n\";\n    }\n    cout << out;\n    return 0;\n}",
            'javascript' => $lcaReadJs.$lcaCoreJs."\n\nconst out = [];\nfor (let i = 0; i < q; i++) {\n  const [u, v] = readInts();\n  out.push(lca(u, v));\n}\nconsole.log(out.join(\"\\n\"));",
            'python' => $lcaReadPy.$lcaCorePy."\n\nout = []\nfor _ in range(q):\n    u, v = map(int, input().split())\n    out.append(lca(u, v))\nprint(\"\\n\".join(map(str, out)))",
        ],
        'editorial' => '<p>Naik satu per satu dari u dan v bisa memakan O(N) per pertanyaan, terlalu lambat untuk pohon berbentuk garis. Gunakan <strong>binary lifting</strong>: <code>up[k][v]</code> = leluhur ke-2<sup>k</sup> dari v, dihitung dengan <code>up[k][v] = up[k−1][up[k−1][v]]</code>.</p>
<p>Untuk menjawab: samakan dulu kedalaman u dan v dengan melompat sesuai bit selisih kedalaman. Jika keduanya sudah sama, itulah jawabannya. Jika belum, lompat bersamaan dari k terbesar ke terkecil, <em>selama leluhurnya berbeda</em>. Akhirnya keduanya tepat di bawah LCA, jadi jawabannya <code>up[0][u]</code>.</p>
<p>Kedalaman dan orang tua dihitung dengan BFS dari akar. Hindari DFS rekursif untuk N = 10<sup>5</sup> berbentuk garis jika stack-nya kecil.</p>
<p><strong>Kompleksitas:</strong> O((N + Q) log N).</p>',
        'hints' => [
            'Pertama, tentukan orang tua dan kedalaman setiap simpul dengan BFS dari akar 1.',
            'Naik satu per satu terlalu lambat untuk pohon yang dalam. Simpan leluhur ke-1, ke-2, ke-4, ke-8, … untuk setiap simpul.',
            'Samakan kedalaman u dan v, lalu lompat bersamaan dengan lompatan terbesar yang tidak membuat keduanya bertemu.',
        ],
        'sample_visual' => 'tree',
    ],

    [
        'slug' => 'jarak-di-pohon',
        'lesson' => 'lca',
        'title' => 'Jarak di Jaringan Pipa',
        'difficulty' => 'Sedang',
        'tags' => ['tree', 'lca', 'jarak'],
        'statement' => '<p>Jaringan pipa air sebuah kota berbentuk pohon dengan <strong>N</strong> sambungan, bernomor 1 sampai N, dan N − 1 pipa. Setiap pipa menghubungkan dua sambungan dan panjangnya sama, yaitu 1 segmen.</p>
<p>Petugas menerima <strong>Q</strong> laporan. Untuk setiap laporan <code>u v</code>, hitung berapa <strong>banyak pipa</strong> yang dilewati pada jalur dari sambungan u ke sambungan v.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N − 1 baris berikutnya berisi <code>a b</code>. Baris berikutnya berisi <code>Q</code>, lalu Q baris berisi <code>u v</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing banyak pipa di antara u dan v.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>1 ≤ Q ≤ 100 000</li><li>1 ≤ u, v ≤ N</li></ul>',
        'samples' => [
            ['input' => "7\n1 2\n1 3\n2 4\n2 5\n5 6\n3 7\n3\n4 6\n6 7\n3 3\n", 'explanation' => 'Jalur 4 – 2 – 5 – 6 melewati 3 pipa. Jalur 6 – 5 – 2 – 1 – 3 – 7 melewati 5 pipa.'],
        ],
        'tests' => function () use ($treeQueries) {
            return ["1\n1\n1 1\n", "2\n1 2\n1\n2 1\n", $treeQueries(10, 20), $treeQueries(300, 500), $treeQueries(5000, 10000), $treeQueries(100000, 100000), $treeQueries(100000, 100000, true), $treeQueries(70000, 80000)];
        },
        'solve' => function (string $input) use ($lcaSolver) {
            [$lca, $depth, $queries] = $lcaSolver($input);
            $out = [];
            foreach ($queries as [$u, $v]) {
                $out[] = $depth[$u] + $depth[$v] - 2 * $depth[$lca($u, $v)];
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => $lcaReadCpp."\n    // Tulis solusimu di sini\n\n    return 0;\n}",
            'javascript' => $lcaReadJs."\n\n// Tulis solusimu di sini\nconst out = [];\nfor (let i = 0; i < q; i++) {\n  const [u, v] = readInts();\n  // out.push(...)\n}\nconsole.log(out.join(\"\\n\"));",
            'python' => $lcaReadPy."\n\n# Tulis solusimu di sini\nout = []\nfor _ in range(q):\n    u, v = map(int, input().split())\n    # out.append(...)\nprint(\"\\n\".join(map(str, out)))",
        ],
        'solutions' => [
            'cpp' => $lcaReadCpp.$lcaCoreCpp."\n\n    string out;\n    for (int i = 0; i < q; i++) {\n        int u, v;\n        cin >> u >> v;\n        // jarak = kedalaman u + kedalaman v - 2 x kedalaman LCA\n        out += to_string(depth[u] + depth[v] - 2 * depth[lca(u, v)]) + \"\\n\";\n    }\n    cout << out;\n    return 0;\n}",
            'javascript' => $lcaReadJs.$lcaCoreJs."\n\nconst out = [];\nfor (let i = 0; i < q; i++) {\n  const [u, v] = readInts();\n  out.push(depth[u] + depth[v] - 2 * depth[lca(u, v)]);\n}\nconsole.log(out.join(\"\\n\"));",
            'python' => $lcaReadPy.$lcaCorePy."\n\nout = []\nfor _ in range(q):\n    u, v = map(int, input().split())\n    out.append(depth[u] + depth[v] - 2 * depth[lca(u, v)])\nprint(\"\\n\".join(map(str, out)))",
        ],
        'editorial' => '<p>Jadikan sambungan 1 sebagai akar. Jalur dari u ke v selalu naik dari u sampai <strong>LCA</strong>(u, v), lalu turun ke v. Banyak pipa yang dilewati:</p>
<p style="text-align:center"><code>jarak(u, v) = depth[u] + depth[v] − 2 · depth[LCA(u, v)]</code></p>
<p>LCA dihitung dengan binary lifting seperti soal <em>Leluhur Bersama</em>.</p>
<p><strong>Kompleksitas:</strong> O((N + Q) log N).</p>',
        'hints' => [
            'Pilih sambungan 1 sebagai akar, lalu hitung kedalaman setiap sambungan.',
            'Jalur u → v naik dari u ke leluhur bersama terdekat, lalu turun ke v.',
            'jarak = depth[u] + depth[v] − 2 · depth[LCA(u, v)].',
        ],
        'sample_visual' => 'tree',
    ],
];
