<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan tambahan track Graph (bagian 2): BFS 0-1, topological sort, MST, pohon.
 * Semua kode C++ harus lolos C++14 (GCC 6.3 / clang): tanpa structured binding.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$cppTree = "    int n;\n    cin >> n;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < n - 1; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }";
$jsTree = "const [n] = readInts();\nconst adj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < n - 1; i++) {\n  const [u, v] = readInts();\n  adj[u].push(v);\n  adj[v].push(u);\n}";
$pyTree = "n = int(input())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(n - 1):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)";

/** Pohon acak N simpul (bentuk: acak, garis, atau bintang). */
$treeInput = function (int $n, string $shape = 'acak'): string {
    $edges = [];
    $perm = range(1, $n);
    T::shuffle($perm);
    for ($i = 1; $i < $n; $i++) {
        $p = match ($shape) {
            'garis' => $i - 1,
            'bintang' => 0,
            default => mt_rand(max(0, $i - 50), $i - 1),
        };
        $edges[] = mt_rand(0, 1) ? [$perm[$p], $perm[$i]] : [$perm[$i], $perm[$p]];
    }
    T::shuffle($edges);

    return T::graphInput((string) $n, $edges);
};

/** BFS dari s pada pohon/graph tak berbobot (PHP), mengembalikan array jarak. */
$bfsPhp = function (int $n, array $adj, int $s): array {
    $dist = array_fill(0, $n + 1, -1);
    $dist[$s] = 0;
    $q = [$s];
    for ($h = 0; $h < count($q); $h++) {
        foreach ($adj[$q[$h]] as $v) {
            if ($dist[$v] === -1) {
                $dist[$v] = $dist[$q[$h]] + 1;
                $q[] = $v;
            }
        }
    }

    return $dist;
};

$readTree = function (string $input): array {
    $lines = T::lines($input);
    $n = (int) $lines[0];
    $adj = array_fill(0, $n + 1, []);
    for ($i = 1; $i < $n; $i++) {
        [$u, $v] = T::ints($lines[$i]);
        $adj[$u][] = $v;
        $adj[$v][] = $u;
    }

    return [$n, $adj];
};

return [
    // ═════════════════════════════ BFS 0-1 & graph keadaan ═════════════════════════════
    [
        'slug' => 'tombol-ajaib',
        'lesson' => 'bfs-01',
        'title' => 'Dua Tombol Ajaib',
        'difficulty' => 'Mudah',
        'tags' => ['bfs', 'graph tersembunyi'],
        'statement' => '<p>Sebuah layar menampilkan bilangan <strong>X</strong>. Ada dua tombol:</p>
<ul><li>Tombol <strong>merah</strong> mengalikan bilangan dengan 2.</li>
<li>Tombol <strong>biru</strong> mengurangi bilangan dengan 1.</li></ul>
<p>Bilangan di layar harus selalu positif dan tidak boleh melebihi 200 000. Berapa kali paling sedikit tombol harus ditekan agar layar menampilkan <strong>Y</strong>?</p>',
        'input_format' => '<p>Satu baris berisi <code>X Y</code>.</p>',
        'output_format' => '<p>Banyak penekanan minimum.</p>',
        'constraints' => '<ul><li>1 ≤ X, Y ≤ 100 000</li></ul>',
        'samples' => [
            ['input' => "4 6\n", 'explanation' => '4 → 3 (biru) → 6 (merah): 2 kali.'],
            ['input' => "10 1\n", 'explanation' => 'Hanya bisa turun satu per satu: 9 kali biru.'],
        ],
        'tests' => function () {
            $r = fn () => mt_rand(1, 100000).' '.mt_rand(1, 100000)."\n";

            return ["1 1\n", "1 100000\n", "100000 1\n", "3 8\n", "99999 100000\n", "1 99999\n", $r(), $r(), $r(), $r()];
        },
        'solve' => function (string $input) {
            [$x, $y] = T::ints(T::lines($input)[0]);
            $LIM = 200000;
            $dist = array_fill(0, $LIM + 1, -1);
            $dist[$x] = 0;
            $q = [$x];
            for ($h = 0; $h < count($q); $h++) {
                $u = $q[$h];
                if ($u === $y) {
                    break;
                }
                foreach ([$u * 2, $u - 1] as $v) {
                    if ($v >= 1 && $v <= $LIM && $dist[$v] === -1) {
                        $dist[$v] = $dist[$u] + 1;
                        $q[] = $v;
                    }
                }
            }

            return (string) $dist[$y];
        },
        'starter' => $st("    int x, y;\n    cin >> x >> y;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int x, y;
    cin >> x >> y;
    const int LIM = 200000;
    // Simpul = bilangan di layar (1..LIM), sisi = satu tekanan tombol. BFS dari X.
    vector<int> dist(LIM + 1, -1);
    queue<int> q;
    dist[x] = 0;
    q.push(x);
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        if (u == y) break;
        int next[2] = {u * 2, u - 1};
        for (int v : next) {
            if (v < 1 || v > LIM || dist[v] != -1) continue;
            dist[v] = dist[u] + 1;
            q.push(v);
        }
    }
    cout << dist[y] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [x, y] = readInts();
const LIM = 200000;
const dist = new Int32Array(LIM + 1).fill(-1);
dist[x] = 0;
const q = [x];
for (let h = 0; h < q.length; h++) {
  const u = q[h];
  if (u === y) break;
  for (const v of [u * 2, u - 1]) {
    if (v < 1 || v > LIM || dist[v] !== -1) continue;
    dist[v] = dist[u] + 1;
    q.push(v);
  }
}
console.log(dist[y]);
CODE,
            'python' => <<<'CODE'
from collections import deque

x, y = map(int, input().split())
LIM = 200000
dist = [-1] * (LIM + 1)
dist[x] = 0
q = deque([x])
while q:
    u = q.popleft()
    if u == y:
        break
    for v in (u * 2, u - 1):
        if 1 <= v <= LIM and dist[v] == -1:
            dist[v] = dist[u] + 1
            q.append(v)
print(dist[y])
CODE,
        ],
        'editorial' => '<p>Ini <strong>graph tersembunyi</strong>: setiap bilangan 1..200 000 adalah simpul, dan setiap tombol adalah sisi berbobot 1 (u → 2u dan u → u − 1). Jarak terpendek dari X ke Y dicari dengan BFS.</p>
<p>Mengapa batas 200 000 cukup? Melewati 2Y tidak pernah berguna karena setelah itu kita harus banyak menekan tombol biru; soal juga membatasinya secara eksplisit.</p>
<p><strong>Kompleksitas:</strong> O(batas). Ada juga solusi serakah dari Y mundur ke X (jika Y genap bagi dua, jika ganjil tambah satu), tetapi BFS lebih mudah dibuktikan benar.</p>',
        'hints' => [
            'Anggap setiap bilangan sebagai simpul. Apa sisinya?',
            'Setiap tombol berbiaya 1, jadi gunakan BFS dari X.',
            'Simpan dist untuk bilangan 1..200000 dan jangan masuk ke bilangan di luar batas.',
        ],
    ],

    [
        'slug' => 'balik-arah',
        'lesson' => 'bfs-01',
        'title' => 'Jalan Satu Arah',
        'difficulty' => 'Sedang',
        'tags' => ['bfs 0-1', 'deque', 'berarah'],
        'statement' => '<p>Kota punya <strong>N</strong> persimpangan dan <strong>M</strong> jalan <strong>satu arah</strong>. Ambulans harus pergi dari persimpangan <strong>1</strong> ke persimpangan <strong>N</strong>. Polisi bisa <strong>membalik arah</strong> sebuah jalan agar ambulans bisa lewat melawan arah semula.</p>
<p>Berapa jalan paling sedikit yang perlu dibalik? Jika ambulans tetap tidak bisa sampai, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>: jalan dari u ke v.</p>',
        'output_format' => '<p>Banyak jalan minimum yang dibalik, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "5 5\n1 2\n3 2\n3 4\n5 4\n1 5\n", 'explanation' => 'Jalan 1 → 5 langsung tersedia, tidak perlu membalik apa pun.'],
            ['input' => "4 3\n2 1\n2 3\n4 3\n", 'explanation' => 'Balik 2 → 1 dan 4 → 3: rute 1 → 2 → 3 → 4 membutuhkan 2 pembalikan.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, bool $conn) {
                $edges = T::edges($n, min($m, $n * ($n - 1)), true, $conn);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["2 0\n", "2 1\n2 1\n", $mk(8, 10, true), $mk(100, 150, true), $mk(1000, 1500, false), $mk(100000, 200000, true), $mk(100000, 120000, true), $mk(100000, 90000, false)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = [$v, 0];
                $adj[$v][] = [$u, 1];
            }
            $dist = array_fill(0, $n + 1, PHP_INT_MAX);
            $dist[1] = 0;
            $dq = new SplDoublyLinkedList;
            $dq->push(1);
            while (! $dq->isEmpty()) {
                $u = $dq->shift();
                foreach ($adj[$u] as [$v, $w]) {
                    if ($dist[$u] + $w < $dist[$v]) {
                        $dist[$v] = $dist[$u] + $w;
                        $w ? $dq->push($v) : $dq->unshift($v);
                    }
                }
            }

            return (string) ($dist[$n] === PHP_INT_MAX ? -1 : $dist[$n]);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    // adj[u] berisi {v, biaya}: 0 jika searah, 1 jika harus dibalik\n    vector<vector<pair<int, int>>> adj(n + 1);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back({v, 0});\n        adj[v].push_back({u, 1});\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    // Jalan u -> v: lewat searah berbiaya 0, lewat melawan arah (dibalik) berbiaya 1
    vector<vector<pair<int, int>>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back({v, 0});
        adj[v].push_back({u, 1});
    }

    const int INF = 1e9;
    vector<int> dist(n + 1, INF);
    deque<int> dq;
    dist[1] = 0;
    dq.push_back(1);
    while (!dq.empty()) {
        int u = dq.front();
        dq.pop_front();
        for (auto& e : adj[u]) {
            int v = e.first, w = e.second;
            if (dist[u] + w < dist[v]) {
                dist[v] = dist[u] + w;
                if (w == 0) dq.push_front(v);
                else dq.push_back(v);
            }
        }
    }
    cout << (dist[n] == INF ? -1 : dist[n]) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push([v, 0]);
  adj[v].push([u, 1]);
}
// Deque dengan array berukuran tetap dan dua penunjuk
const cap = 4 * (n + m) + 10;
const buf = new Int32Array(cap);
let head = 2 * (n + m) + 5, tail = head;
const dist = new Array(n + 1).fill(Infinity);
dist[1] = 0;
buf[tail++] = 1;
while (head < tail) {
  const u = buf[head++];
  for (const [v, w] of adj[u]) {
    if (dist[u] + w < dist[v]) {
      dist[v] = dist[u] + w;
      if (w === 0) buf[--head] = v;
      else buf[tail++] = v;
    }
  }
}
console.log(dist[n] === Infinity ? -1 : dist[n]);
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append((v, 0))
    adj[v].append((u, 1))

INF = float("inf")
dist = [INF] * (n + 1)
dist[1] = 0
dq = deque([1])
while dq:
    u = dq.popleft()
    for v, w in adj[u]:
        if dist[u] + w < dist[v]:
            dist[v] = dist[u] + w
            if w == 0:
                dq.appendleft(v)
            else:
                dq.append(v)
print(-1 if dist[n] == INF else dist[n])
CODE,
        ],
        'editorial' => '<p>Ubah soal menjadi graph berbobot: untuk setiap jalan u → v, tambahkan sisi u → v berbobot <strong>0</strong> (lewat searah) dan sisi v → u berbobot <strong>1</strong> (lewat setelah dibalik). Jarak terpendek dari 1 ke N adalah banyak pembalikan minimum.</p>
<p>Karena bobot hanya 0 dan 1, gunakan <strong>BFS 0-1</strong> dengan deque: sisi 0 masuk depan, sisi 1 masuk belakang.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            'Melewati jalan searah gratis; melawan arah "membayar" satu pembalikan. Bagaimana jika ini dijadikan bobot sisi?',
            'Untuk jalan u → v: sisi u → v berbobot 0 dan v → u berbobot 1.',
            'Bobot hanya 0/1: pakai deque (BFS 0-1).',
        ],
        'sample_visual' => 'digraph',
    ],

    [
        'slug' => 'labirin-kunci',
        'lesson' => 'bfs-01',
        'title' => 'Labirin Berkunci',
        'difficulty' => 'Sulit',
        'tags' => ['bfs', 'graph keadaan', 'bitmask'],
        'statement' => '<p>Kamu berada di labirin R × C. Isi petak:</p>
<ul><li><code>.</code> jalan, <code>#</code> tembok, <code>S</code> posisi awal, <code>E</code> pintu keluar.</li>
<li><code>a</code>, <code>b</code>, <code>c</code>, <code>d</code>: kunci. Kunci diambil otomatis saat menginjak petaknya dan bisa dipakai berkali-kali.</li>
<li><code>A</code>, <code>B</code>, <code>C</code>, <code>D</code>: pintu. Pintu hanya bisa dilewati jika sudah memegang kunci huruf kecilnya.</li></ul>
<p>Setiap langkah ke petak tetangga (atas/bawah/kiri/kanan) memakan 1 detik. Berapa detik minimum untuk sampai ke <code>E</code>? Jika mustahil, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>, diikuti R baris peta. Tepat ada satu <code>S</code> dan satu <code>E</code>.</p>',
        'output_format' => '<p>Waktu minimum atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 60</li></ul>',
        'samples' => [
            ['input' => "3 6\nS.A..E\n.####.\n...a.#\n", 'explanation' => 'Satu-satunya jalan ke E melewati pintu A. Ambil kunci a dulu (5 langkah), kembali ke depan pintu (6 langkah), lalu lewat pintu sampai E (4 langkah): total 15.'],
            ['input' => "1 4\nSB.E\n", 'explanation' => 'Tidak ada kunci b sama sekali.'],
        ],
        'tests' => function () {
            $mk = function (int $r, int $c, float $wall, int $keys) {
                $g = T::grid($r, $c, $wall);
                $place = function ($ch) use (&$g, $r, $c) {
                    $g[mt_rand(0, $r - 1)][mt_rand(0, $c - 1)] = $ch;
                };
                for ($k = 0; $k < $keys; $k++) {
                    $place('abcd'[$k]);
                    for ($t = 0; $t < 3; $t++) {
                        $place('ABCD'[$k]);
                    }
                }
                $sr = mt_rand(0, $r - 1);
                $sc = mt_rand(0, $c - 1);
                $g[$sr][$sc] = 'S';
                do {
                    $er = mt_rand(0, $r - 1);
                    $ec = mt_rand(0, $c - 1);
                } while ($er === $sr && $ec === $sc);
                $g[$er][$ec] = 'E';

                return "$r $c\n".implode("\n", $g)."\n";
            };

            return ["1 2\nSE\n", "1 3\nSAE\n", "1 4\naSAE\n", $mk(5, 5, 0.2, 1), $mk(10, 10, 0.25, 2), $mk(30, 30, 0.3, 3), $mk(60, 60, 0.3, 4), $mk(60, 60, 0.15, 4), $mk(60, 60, 0.4, 2), $mk(40, 60, 0.0, 4)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C] = T::ints($lines[0]);
            $g = array_slice($lines, 1, $R);
            $start = 0;
            for ($r = 0; $r < $R; $r++) {
                for ($c = 0; $c < $C; $c++) {
                    if ($g[$r][$c] === 'S') {
                        $start = $r * $C + $c;
                    }
                }
            }
            $dist = array_fill(0, $R * $C * 16, -1);
            $dist[$start * 16] = 0;
            $q = [$start * 16];
            for ($h = 0; $h < count($q); $h++) {
                $state = $q[$h];
                $cell = intdiv($state, 16);
                $mask = $state % 16;
                $r = intdiv($cell, $C);
                $c = $cell % $C;
                if ($g[$r][$c] === 'E') {
                    return (string) $dist[$state];
                }
                foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$dr, $dc]) {
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    if ($nr < 0 || $nr >= $R || $nc < 0 || $nc >= $C) {
                        continue;
                    }
                    $ch = $g[$nr][$nc];
                    if ($ch === '#') {
                        continue;
                    }
                    $nm = $mask;
                    if ($ch >= 'A' && $ch <= 'D' && ! ($mask >> (ord($ch) - 65) & 1)) {
                        continue;
                    }
                    if ($ch >= 'a' && $ch <= 'd') {
                        $nm |= 1 << (ord($ch) - 97);
                    }
                    $ns = ($nr * $C + $nc) * 16 + $nm;
                    if ($dist[$ns] === -1) {
                        $dist[$ns] = $dist[$state] + 1;
                        $q[] = $ns;
                    }
                }
            }

            return '-1';
        },
        'starter' => $st("    int R, C;\n    cin >> R >> C;\n    vector<string> g(R);\n    for (auto& baris : g) cin >> baris;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int R, C;
    cin >> R >> C;
    vector<string> g(R);
    for (auto& baris : g) cin >> baris;

    // Keadaan = (baris, kolom, kunci yang dipegang sebagai bitmask 4 bit)
    vector<vector<vector<int>>> dist(R, vector<vector<int>>(C, vector<int>(16, -1)));
    queue<array<int, 3>> q;
    for (int r = 0; r < R; r++)
        for (int c = 0; c < C; c++)
            if (g[r][c] == 'S') {
                dist[r][c][0] = 0;
                q.push({r, c, 0});
            }
    int dr[] = {-1, 1, 0, 0}, dc[] = {0, 0, -1, 1};
    while (!q.empty()) {
        array<int, 3> cur = q.front();
        q.pop();
        int r = cur[0], c = cur[1], mask = cur[2];
        if (g[r][c] == 'E') {
            cout << dist[r][c][mask] << '\n';
            return 0;
        }
        for (int k = 0; k < 4; k++) {
            int nr = r + dr[k], nc = c + dc[k];
            if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue;
            char ch = g[nr][nc];
            if (ch == '#') continue;
            if (ch >= 'A' && ch <= 'D' && !(mask >> (ch - 'A') & 1)) continue;   // pintu terkunci
            int nm = mask;
            if (ch >= 'a' && ch <= 'd') nm |= 1 << (ch - 'a');                    // ambil kunci
            if (dist[nr][nc][nm] != -1) continue;
            dist[nr][nc][nm] = dist[r][c][mask] + 1;
            q.push({nr, nc, nm});
        }
    }
    cout << -1 << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const g = [];
for (let i = 0; i < R; i++) g.push(readLine().trim());
const dist = new Int32Array(R * C * 16).fill(-1);
const q = [];
for (let r = 0; r < R; r++)
  for (let c = 0; c < C; c++)
    if (g[r][c] === "S") { dist[(r * C + c) * 16] = 0; q.push((r * C + c) * 16); }
const D = [[-1, 0], [1, 0], [0, -1], [0, 1]];
let ans = -1;
for (let h = 0; h < q.length; h++) {
  const s = q[h], cell = s >> 4, mask = s & 15, r = Math.floor(cell / C), c = cell % C;
  if (g[r][c] === "E") { ans = dist[s]; break; }
  for (const [a, b] of D) {
    const nr = r + a, nc = c + b;
    if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue;
    const ch = g[nr][nc];
    if (ch === "#") continue;
    if (ch >= "A" && ch <= "D" && !((mask >> (ch.charCodeAt(0) - 65)) & 1)) continue;
    let nm = mask;
    if (ch >= "a" && ch <= "d") nm |= 1 << (ch.charCodeAt(0) - 97);
    const ns = (nr * C + nc) * 16 + nm;
    if (dist[ns] !== -1) continue;
    dist[ns] = dist[s] + 1;
    q.push(ns);
  }
}
console.log(ans);
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

R, C = map(int, input().split())
g = [input().strip() for _ in range(R)]
dist = [[[-1] * 16 for _ in range(C)] for _ in range(R)]
q = deque()
for r in range(R):
    for c in range(C):
        if g[r][c] == "S":
            dist[r][c][0] = 0
            q.append((r, c, 0))
ans = -1
while q:
    r, c, mask = q.popleft()
    if g[r][c] == "E":
        ans = dist[r][c][mask]
        break
    for nr, nc in ((r - 1, c), (r + 1, c), (r, c - 1), (r, c + 1)):
        if not (0 <= nr < R and 0 <= nc < C):
            continue
        ch = g[nr][nc]
        if ch == "#":
            continue
        if "A" <= ch <= "D" and not (mask >> (ord(ch) - 65)) & 1:
            continue
        nm = mask | (1 << (ord(ch) - 97)) if "a" <= ch <= "d" else mask
        if dist[nr][nc][nm] == -1:
            dist[nr][nc][nm] = dist[r][c][mask] + 1
            q.append((nr, nc, nm))
print(ans)
CODE,
        ],
        'editorial' => '<p>Posisi saja tidak cukup untuk menggambarkan keadaan: petak yang sama bisa dikunjungi sebelum dan sesudah mengambil kunci, dan pilihan langkah berikutnya berbeda. Jadi simpulnya adalah <strong>(baris, kolom, kunci yang dipegang)</strong>, dengan kunci disimpan sebagai bitmask 4 bit (16 kemungkinan).</p>
<p>Jalankan BFS di graph keadaan ini. Saat menginjak kunci, bit-nya dinyalakan; pintu hanya boleh dimasuki jika bit kuncinya menyala. Begitu BFS mengambil petak E (dengan kunci apa pun), itulah jawabannya.</p>
<p><strong>Kompleksitas:</strong> O(R · C · 16) keadaan.</p>',
        'hints' => [
            'Mengunjungi petak yang sama dua kali bisa berguna: sebelum dan sesudah memegang kunci. Apa yang harus ditambahkan ke keadaan?',
            'Keadaan = (r, c, kunci yang dipegang). Ada 4 jenis kunci, simpan sebagai bitmask 0..15.',
            'BFS pada keadaan (r, c, mask). Pintu hanya bisa dimasuki jika bit kuncinya menyala.',
        ],
    ],

    // ═════════════════════════════ Topological sort ═════════════════════════════
    [
        'slug' => 'urutan-terkecil',
        'lesson' => 'topological-sort',
        'title' => 'Urutan Ujian Terkecil',
        'difficulty' => 'Sedang',
        'tags' => ['topological sort', 'priority queue'],
        'statement' => '<p>Ada <strong>N</strong> ujian bernomor 1..N dan <strong>M</strong> syarat <code>a b</code>: ujian a harus dikerjakan sebelum ujian b. Tentukan urutan mengerjakan semua ujian yang memenuhi semua syarat. Jika ada banyak urutan, pilih yang <strong>terkecil secara leksikografis</strong> (bandingkan dari ujian pertama). Jika mustahil, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>N bilangan dipisah spasi, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "5 4\n3 1\n3 2\n5 2\n4 5\n", 'explanation' => 'Ujian yang bisa dikerjakan pertama: 3 atau 4. Pilih 3, lalu 1 menjadi bebas, dan seterusnya: 3 1 4 5 2.'],
            ['input' => "3 3\n1 2\n2 3\n3 1\n", 'explanation' => 'Syarat melingkar.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, bool $cyc) {
                $edges = $cyc ? T::withCycle($n, $m) : T::dag($n, $m);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["1 0\n", "3 0\n", $mk(8, 8, false), $mk(8, 8, true), $mk(1000, 2000, false), $mk(100000, 200000, false), $mk(100000, 50000, false), $mk(100000, 200000, true)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            $in = array_fill(0, $n + 1, 0);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $adj[$a][] = $b;
                $in[$b]++;
            }
            $pq = new SplMinHeap;
            for ($v = 1; $v <= $n; $v++) {
                if ($in[$v] === 0) {
                    $pq->insert($v);
                }
            }
            $res = [];
            while (! $pq->isEmpty()) {
                $u = $pq->extract();
                $res[] = $u;
                foreach ($adj[$u] as $v) {
                    if (--$in[$v] === 0) {
                        $pq->insert($v);
                    }
                }
            }

            return count($res) < $n ? '-1' : implode(' ', $res);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> adj(n + 1);\n    vector<int> indeg(n + 1, 0);\n    for (int i = 0; i < m; i++) {\n        int a, b;\n        cin >> a >> b;\n        adj[a].push_back(b);\n        indeg[b]++;\n    }"),
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
    vector<int> indeg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        indeg[b]++;
    }

    // Kahn dengan min-heap: dari semua ujian yang siap, selalu ambil nomor terkecil
    priority_queue<int, vector<int>, greater<int>> pq;
    for (int v = 1; v <= n; v++)
        if (indeg[v] == 0) pq.push(v);
    vector<int> urutan;
    while (!pq.empty()) {
        int u = pq.top();
        pq.pop();
        urutan.push_back(u);
        for (int v : adj[u])
            if (--indeg[v] == 0) pq.push(v);
    }
    if ((int)urutan.size() < n) {
        cout << -1 << '\n';
        return 0;
    }
    for (int i = 0; i < n; i++) cout << urutan[i] << (i + 1 < n ? ' ' : '\n');
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Min-heap bilangan
class MinHeap {
  constructor() { this.a = []; }
  push(x) {
    const a = this.a; a.push(x);
    let i = a.length - 1;
    while (i > 0) { const p = (i - 1) >> 1; if (a[p] <= a[i]) break; [a[p], a[i]] = [a[i], a[p]]; i = p; }
  }
  pop() {
    const a = this.a, top = a[0], last = a.pop();
    if (a.length) {
      a[0] = last;
      let i = 0;
      for (;;) {
        const l = 2 * i + 1, r = l + 1;
        let s = i;
        if (l < a.length && a[l] < a[s]) s = l;
        if (r < a.length && a[r] < a[s]) s = r;
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
const indeg = new Array(n + 1).fill(0);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  indeg[b]++;
}
const pq = new MinHeap();
for (let v = 1; v <= n; v++) if (indeg[v] === 0) pq.push(v);
const res = [];
while (pq.size) {
  const u = pq.pop();
  res.push(u);
  for (const v of adj[u]) if (--indeg[v] === 0) pq.push(v);
}
console.log(res.length < n ? -1 : res.join(" "));
CODE,
            'python' => <<<'CODE'
import sys, heapq
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
indeg = [0] * (n + 1)
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    indeg[b] += 1

pq = [v for v in range(1, n + 1) if indeg[v] == 0]
heapq.heapify(pq)
res = []
while pq:
    u = heapq.heappop(pq)
    res.append(u)
    for v in adj[u]:
        indeg[v] -= 1
        if indeg[v] == 0:
            heapq.heappush(pq, v)
print(-1 if len(res) < n else " ".join(map(str, res)))
CODE,
        ],
        'editorial' => '<p>Algoritma Kahn memilih <em>salah satu</em> simpul berindegree 0 di setiap langkah. Untuk mendapatkan urutan leksikografis terkecil, selalu pilih yang <strong>nomornya paling kecil</strong>: ganti antrian biasa dengan <strong>min-heap</strong> (<code>priority_queue</code> dengan <code>greater</code>).</p>
<p>Mengapa serakah ini benar? Posisi pertama urutan harus diisi simpul berindegree 0, dan memilih yang terkecil tidak pernah merugikan posisi berikutnya karena simpul lain yang siap tetap siap.</p>
<p>Jika urutan berisi kurang dari N simpul, ada siklus. <strong>Kompleksitas:</strong> O((N + M) log N).</p>',
        'hints' => [
            'Mulai dari topological sort Kahn. Saat ada beberapa ujian yang siap, mana yang harus dipilih?',
            'Selalu pilih nomor terkecil yang siap: gunakan min-heap, bukan queue biasa.',
            'Jika hasilnya kurang dari N ujian, ada syarat melingkar: cetak −1.',
        ],
        'sample_visual' => 'digraph',
    ],

    [
        'slug' => 'rute-wisata',
        'lesson' => 'topological-sort',
        'title' => 'Rute Wisata Terpanjang',
        'difficulty' => 'Sedang',
        'tags' => ['dag', 'dp', 'topological sort'],
        'statement' => '<p>Sebuah taman hiburan punya <strong>N</strong> wahana dan <strong>M</strong> jalur <strong>satu arah</strong> yang tidak pernah membentuk lingkaran (DAG). Melewati jalur dari u ke v memberi nilai keseruan <code>w</code>.</p>
<p>Pengunjung masuk di wahana <strong>1</strong> dan keluar di wahana <strong>N</strong>. Berapa total keseruan <strong>terbesar</strong> yang bisa didapat? Jika N tidak bisa dicapai dari 1, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v w</code>.</p>',
        'output_format' => '<p>Total keseruan maksimum atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li><li>1 ≤ w ≤ 10<sup>9</sup></li><li>Graph dijamin tidak punya siklus.</li></ul>',
        'samples' => [
            ['input' => "4 5\n1 2 3\n2 4 2\n1 3 1\n3 4 9\n2 3 1\n", 'explanation' => 'Rute 1 → 2 → 3 → 4 bernilai 3 + 1 + 9 = 13, lebih besar dari 1 → 3 → 4 (10) dan 1 → 2 → 4 (5).'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $maxW) {
                // DAG dengan urutan 1..N (sisi selalu dari nomor kecil ke besar) agar 1 → N sering terjangkau
                $edges = [];
                $seen = [];
                for ($i = 0; $i < $m; $i++) {
                    $u = mt_rand(1, $n - 1);
                    $v = mt_rand($u + 1, min($n, $u + 30));
                    if (isset($seen["$u-$v"])) {
                        continue;
                    }
                    $seen["$u-$v"] = true;
                    $edges[] = [$u, $v, mt_rand(1, $maxW)];
                }
                // Acak nomor simpul kecuali 1 dan N
                $mid = range(2, $n - 1);
                T::shuffle($mid);
                $map = [1 => 1, $n => $n];
                foreach ($mid as $i => $x) {
                    $map[$i + 2] = $x;
                }
                $edges = array_map(fn ($e) => [$map[$e[0]], $map[$e[1]], $e[2]], $edges);
                T::shuffle($edges);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["2 0\n", "2 1\n1 2 5\n", $mk(8, 12, 10), $mk(1000, 3000, 100), $mk(100000, 200000, 1000000000), $mk(100000, 60000, 1000), $mk(100000, 200000, 5)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $adj = array_fill(0, $n + 1, []);
            $in = array_fill(0, $n + 1, 0);
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v, $w] = T::ints($lines[$i]);
                $adj[$u][] = [$v, $w];
                $in[$v]++;
            }
            $q = [];
            for ($v = 1; $v <= $n; $v++) {
                if ($in[$v] === 0) {
                    $q[] = $v;
                }
            }
            $best = array_fill(0, $n + 1, -1);
            $best[1] = 0;
            for ($h = 0; $h < count($q); $h++) {
                $u = $q[$h];
                foreach ($adj[$u] as [$v, $w]) {
                    if ($best[$u] >= 0 && $best[$u] + $w > $best[$v]) {
                        $best[$v] = $best[$u] + $w;
                    }
                    if (--$in[$v] === 0) {
                        $q[] = $v;
                    }
                }
            }

            return (string) $best[$n];
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<vector<pair<int, long long>>> adj(n + 1);\n    vector<int> indeg(n + 1, 0);\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        long long w;\n        cin >> u >> v >> w;\n        adj[u].push_back({v, w});\n        indeg[v]++;\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<pair<int, long long>>> adj(n + 1);
    vector<int> indeg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w});
        indeg[v]++;
    }

    // best[v] = keseruan terbesar dari 1 ke v (-1 = tidak terjangkau dari 1)
    vector<long long> best(n + 1, -1);
    best[1] = 0;
    queue<int> q;
    for (int v = 1; v <= n; v++)
        if (indeg[v] == 0) q.push(v);
    // Proses dalam urutan topologis: saat u diambil, best[u] sudah final
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        for (auto& e : adj[u]) {
            int v = e.first;
            if (best[u] >= 0) best[v] = max(best[v], best[u] + e.second);
            if (--indeg[v] == 0) q.push(v);
        }
    }
    cout << best[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const indeg = new Array(n + 1).fill(0);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
  indeg[v]++;
}
// Total bisa mencapai ±10^14: masih aman untuk Number (≤ 9 · 10^15)
const best = new Array(n + 1).fill(-1);
best[1] = 0;
const q = [];
for (let v = 1; v <= n; v++) if (indeg[v] === 0) q.push(v);
for (let h = 0; h < q.length; h++) {
  const u = q[h];
  for (const [v, w] of adj[u]) {
    if (best[u] >= 0 && best[u] + w > best[v]) best[v] = best[u] + w;
    if (--indeg[v] === 0) q.push(v);
  }
}
console.log(best[n]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
indeg = [0] * (n + 1)
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))
    indeg[v] += 1

best = [-1] * (n + 1)
best[1] = 0
q = [v for v in range(1, n + 1) if indeg[v] == 0]
for u in q:
    for v, w in adj[u]:
        if best[u] >= 0 and best[u] + w > best[v]:
            best[v] = best[u] + w
        indeg[v] -= 1
        if indeg[v] == 0:
            q.append(v)
print(best[n])
CODE,
        ],
        'editorial' => '<p>Jalur <strong>terpanjang</strong> pada graph umum sangat sulit, tetapi pada <strong>DAG</strong> mudah dengan DP: <code>best[v] = max(best[u] + w)</code> untuk setiap sisi u → v. Supaya semua <code>best[u]</code> sudah final saat dipakai, proses simpul dalam <strong>urutan topologis</strong> (algoritma Kahn).</p>
<p>Simpul yang tidak terjangkau dari 1 diberi nilai −1 dan tidak boleh dipakai untuk merelaksasi. Total bisa mencapai 2 · 10<sup>14</sup>: gunakan <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
        'hints' => [
            'Jalur terpanjang di graph biasa sulit, tetapi graph ini tidak punya siklus. Urutan apa yang membuat DP mungkin?',
            'Proses simpul dalam urutan topologis, lalu best[v] = max(best[v], best[u] + w).',
            'Tandai simpul yang belum terjangkau dari 1 (misalnya −1) dan jangan merelaksasi dari simpul itu.',
        ],
        'sample_visual' => 'wdigraph',
    ],

    // ═════════════════════════════ MST & Union-Find ═════════════════════════════
    [
        'slug' => 'sambung-jaringan',
        'lesson' => 'mst',
        'title' => 'Kabel Tambahan',
        'difficulty' => 'Mudah',
        'tags' => ['union-find', 'komponen'],
        'statement' => '<p>Laboratorium komputer punya <strong>N</strong> komputer dan <strong>M</strong> kabel yang sudah terpasang (dua arah). Teknisi ingin semua komputer bisa saling berkomunikasi, langsung atau lewat komputer lain.</p>
<p>Berapa kabel <strong>tambahan</strong> paling sedikit yang harus dipasang?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>.</p>',
        'output_format' => '<p>Banyak kabel tambahan minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ M ≤ 200 000</li><li>Bisa ada kabel ganda atau kabel dari komputer ke dirinya sendiri.</li></ul>',
        'samples' => [
            ['input' => "6 4\n1 2\n2 3\n4 5\n1 3\n", 'explanation' => 'Ada tiga kelompok: {1, 2, 3}, {4, 5}, dan {6}. Dua kabel cukup untuk menyatukannya.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m) {
                $lines = ["$n $m"];
                for ($i = 0; $i < $m; $i++) {
                    $lines[] = mt_rand(1, $n).' '.mt_rand(1, $n);
                }

                return implode("\n", $lines)."\n";
            };

            return ["1 0\n", "3 0\n", "2 2\n1 1\n2 2\n", $mk(10, 5), $mk(1000, 600), $mk(100000, 50000), $mk(100000, 200000), $mk(100000, 99000)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $p = range(0, $n);
            $find = function ($x) use (&$p) {
                while ($p[$x] !== $x) {
                    $p[$x] = $p[$p[$x]];
                    $x = $p[$x];
                }

                return $x;
            };
            $comp = $n;
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $a = $find($u);
                $b = $find($v);
                if ($a !== $b) {
                    $p[$a] = $b;
                    $comp--;
                }
            }

            return (string) ($comp - 1);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;"),
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
    parent.resize(n + 1);
    iota(parent.begin(), parent.end(), 0);
    int komponen = n;                  // awalnya setiap komputer sendirian
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        int a = find(u), b = find(v);
        if (a != b) {                  // dua kelompok bergabung
            parent[a] = b;
            komponen--;
        }
    }
    // k kelompok butuh tepat k - 1 kabel untuk disatukan
    cout << komponen - 1 << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const parent = Array.from({ length: n + 1 }, (_, i) => i);
function find(x) {
  while (parent[x] !== x) { parent[x] = parent[parent[x]]; x = parent[x]; }
  return x;
}
let komp = n;
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  const a = find(u), b = find(v);
  if (a !== b) { parent[a] = b; komp--; }
}
console.log(komp - 1);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
parent = list(range(n + 1))
def find(x):
    while parent[x] != x:
        parent[x] = parent[parent[x]]
        x = parent[x]
    return x

komp = n
for _ in range(m):
    u, v = map(int, input().split())
    a, b = find(u), find(v)
    if a != b:
        parent[a] = b
        komp -= 1
print(komp - 1)
CODE,
        ],
        'editorial' => '<p>Hitung banyak <strong>komponen terhubung</strong> k. Dengan k − 1 kabel, kita bisa menyambungkan komponen-komponen secara berantai, dan kurang dari itu tidak mungkin (setiap kabel paling banyak menggabungkan dua komponen menjadi satu).</p>
<p>Komponen dihitung dengan Union-Find: mulai dari N komponen, setiap kabel yang menggabungkan dua kelompok berbeda mengurangi hitungan satu. Kabel di dalam kelompok yang sama (termasuk kabel ganda dan kabel ke diri sendiri) diabaikan.</p>
<p><strong>Kompleksitas:</strong> hampir O(N + M).</p>',
        'hints' => [
            'Jika ada k kelompok komputer yang terpisah, berapa kabel yang dibutuhkan untuk menyatukan semuanya?',
            'Jawabannya (banyak komponen) − 1. Sekarang hitung komponennya.',
            'Union-Find: mulai dari N komponen, kurangi satu setiap kali kabel menggabungkan dua kelompok berbeda.',
        ],
        'sample_visual' => 'graph',
    ],

    [
        'slug' => 'jalan-desa',
        'lesson' => 'mst',
        'title' => 'Jalan Antar Desa',
        'difficulty' => 'Sedang',
        'tags' => ['mst', 'prim', 'graph padat'],
        'statement' => '<p>Ada <strong>N</strong> desa di sebuah peta grid; desa ke-i berada di koordinat (x<sub>i</sub>, y<sub>i</sub>). Pemerintah ingin membangun jalan sehingga semua desa terhubung. Membangun jalan antara desa i dan j berbiaya <code>|x<sub>i</sub> − x<sub>j</sub>| + |y<sub>i</sub> − y<sub>j</sub>|</code> (jarak Manhattan), dan jalan boleh dibangun antara <strong>pasangan desa mana pun</strong>.</p>
<p>Berapa total biaya minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya berisi <code>x y</code>.</p>',
        'output_format' => '<p>Total biaya minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1500</li><li>0 ≤ x, y ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4\n0 0\n2 2\n3 10\n0 2\n", 'explanation' => 'Bangun (0,0)–(0,2) biaya 2, (0,2)–(2,2) biaya 2, (2,2)–(3,10) biaya 9. Total 13.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $out = [(string) $n];
                for ($i = 0; $i < $n; $i++) {
                    $out[] = mt_rand(0, $max).' '.mt_rand(0, $max);
                }

                return implode("\n", $out)."\n";
            };

            return ["1\n5 5\n", "2\n0 0\n1000000 1000000\n", $mk(10, 20), $mk(200, 1000), $mk(1000, 1000000), $mk(1500, 1000000), $mk(1500, 10)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $xs = [];
            $ys = [];
            for ($i = 1; $i <= $n; $i++) {
                [$xs[], $ys[]] = T::ints($lines[$i]);
            }
            $INF = PHP_INT_MAX;
            $d = array_fill(0, $n, $INF);
            $used = array_fill(0, $n, false);
            $d[0] = 0;
            $total = 0;
            for ($it = 0; $it < $n; $it++) {
                $u = -1;
                for ($v = 0; $v < $n; $v++) {
                    if (! $used[$v] && ($u === -1 || $d[$v] < $d[$u])) {
                        $u = $v;
                    }
                }
                $used[$u] = true;
                $total += $d[$u];
                $xu = $xs[$u];
                $yu = $ys[$u];
                for ($v = 0; $v < $n; $v++) {
                    if (! $used[$v]) {
                        $c = abs($xu - $xs[$v]) + abs($yu - $ys[$v]);
                        if ($c < $d[$v]) {
                            $d[$v] = $c;
                        }
                    }
                }
            }

            return (string) $total;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> x(n), y(n);\n    for (int i = 0; i < n; i++) cin >> x[i] >> y[i];"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<long long> x(n), y(n);
    for (int i = 0; i < n; i++) cin >> x[i] >> y[i];

    // Prim O(N^2): cocok untuk graph PADAT (semua pasangan bisa dihubungkan).
    // d[v] = biaya termurah menyambungkan v ke pohon yang sudah terbentuk
    const long long INF = LLONG_MAX;
    vector<long long> d(n, INF);
    vector<bool> dipakai(n, false);
    d[0] = 0;
    long long total = 0;
    for (int it = 0; it < n; it++) {
        int u = -1;
        for (int v = 0; v < n; v++)
            if (!dipakai[v] && (u == -1 || d[v] < d[u])) u = v;
        dipakai[u] = true;
        total += d[u];
        for (int v = 0; v < n; v++) {
            if (dipakai[v]) continue;
            long long biaya = llabs(x[u] - x[v]) + llabs(y[u] - y[v]);
            if (biaya < d[v]) d[v] = biaya;
        }
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const x = [], y = [];
for (let i = 0; i < n; i++) {
  const [a, b] = readInts();
  x.push(a); y.push(b);
}
const d = new Array(n).fill(Infinity);
const used = new Uint8Array(n);
d[0] = 0;
let total = 0;
for (let it = 0; it < n; it++) {
  let u = -1;
  for (let v = 0; v < n; v++) if (!used[v] && (u === -1 || d[v] < d[u])) u = v;
  used[u] = 1;
  total += d[u];
  for (let v = 0; v < n; v++) {
    if (used[v]) continue;
    const c = Math.abs(x[u] - x[v]) + Math.abs(y[u] - y[v]);
    if (c < d[v]) d[v] = c;
  }
}
console.log(total);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
x, y = [], []
for _ in range(n):
    a, b = map(int, input().split())
    x.append(a)
    y.append(b)

INF = float("inf")
d = [INF] * n
d[0] = 0
sisa = set(range(n))
total = 0
for _ in range(n):
    u = min(sisa, key=d.__getitem__)
    sisa.remove(u)
    total += d[u]
    xu, yu = x[u], y[u]
    for v in sisa:
        c = abs(xu - x[v]) + abs(yu - y[v])
        if c < d[v]:
            d[v] = c
print(total)
CODE,
        ],
        'editorial' => '<p>Ini soal MST pada graph <strong>lengkap</strong>: setiap pasangan desa bisa dihubungkan, sehingga ada N(N − 1)/2 ≈ 1,1 juta sisi. Kruskal tetap bisa (urutkan semua sisi), tetapi <strong>algoritma Prim O(N²)</strong> lebih sederhana dan cepat untuk graph padat.</p>
<p>Prim membangun pohon dari satu desa. Simpan <code>d[v]</code> = biaya termurah menyambungkan v ke pohon. Setiap langkah, ambil desa di luar pohon dengan d terkecil, masukkan ke pohon, lalu perbarui d semua desa lain memakai biaya dari desa baru itu. Biaya sisi dihitung langsung dari koordinat, tanpa menyimpan daftar sisi.</p>
<p><strong>Kompleksitas:</strong> O(N²) waktu, O(N) memori.</p>',
        'hints' => [
            'Setiap pasangan desa bisa dihubungkan: graph-nya lengkap dengan ±10^6 sisi. Algoritma MST mana yang cocok untuk graph padat?',
            'Prim O(N²): mulai dari satu desa, setiap langkah tambahkan desa luar yang paling murah disambungkan.',
            'Simpan d[v] = biaya termurah dari v ke pohon; hitung biaya Manhattan langsung tanpa menyimpan sisi.',
        ],
    ],

    // ═════════════════════════════ Algoritma pada pohon ═════════════════════════════
    [
        'slug' => 'banyak-bawahan',
        'lesson' => 'pohon',
        'title' => 'Banyak Bawahan',
        'difficulty' => 'Mudah',
        'tags' => ['pohon', 'subtree'],
        'statement' => '<p>Sebuah perusahaan punya <strong>N</strong> karyawan. Karyawan 1 adalah direktur, dan setiap karyawan lain punya tepat satu atasan langsung. Untuk setiap karyawan, hitung banyak <strong>bawahannya</strong>: semua karyawan yang berada di bawahnya, langsung maupun tidak langsung.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N − 1 bilangan: atasan karyawan 2, 3, …, N.</p>',
        'output_format' => '<p>N bilangan: banyak bawahan karyawan 1..N.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>Atasan karyawan i selalu bernomor lebih kecil dari i.</li></ul>',
        'samples' => [
            ['input' => "5\n1 1 2 3\n", 'explanation' => 'Direktur membawahi semua 4 orang. Karyawan 2 membawahi 4, karyawan 3 membawahi 5.'],
        ],
        'tests' => function () {
            $mk = function (int $n, string $shape) {
                $p = [];
                for ($i = 2; $i <= $n; $i++) {
                    $p[] = match ($shape) {
                        'garis' => $i - 1,
                        'bintang' => 1,
                        default => mt_rand(max(1, $i - 100), $i - 1),
                    };
                }

                return "$n\n".implode(' ', $p)."\n";
            };

            return ["1\n\n", "2\n1\n", $mk(10, 'acak'), $mk(1000, 'acak'), $mk(200000, 'acak'), $mk(200000, 'garis'), $mk(200000, 'bintang')];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $p = $n > 1 ? T::ints($lines[1]) : [];
            $sz = array_fill(0, $n + 1, 1);
            for ($i = $n; $i >= 2; $i--) {
                $sz[$p[$i - 2]] += $sz[$i];
            }

            return implode(' ', array_map(fn ($s) => $s - 1, array_slice($sz, 1)));
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> atasan(n + 1, 0);\n    for (int i = 2; i <= n; i++) cin >> atasan[i];"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> atasan(n + 1, 0);
    for (int i = 2; i <= n; i++) cin >> atasan[i];

    // Atasan selalu bernomor lebih kecil, jadi memproses i dari N turun ke 2
    // menjamin semua bawahan i sudah selesai sebelum i disumbangkan ke atasannya.
    vector<int> sz(n + 1, 1);
    for (int i = n; i >= 2; i--) sz[atasan[i]] += sz[i];
    for (int v = 1; v <= n; v++) cout << sz[v] - 1 << (v < n ? ' ' : '\n');
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const atasan = [0, 0, ...(n > 1 ? readInts() : [])];
const sz = new Array(n + 1).fill(1);
for (let i = n; i >= 2; i--) sz[atasan[i]] += sz[i];
console.log(sz.slice(1).map((s) => s - 1).join(" "));
CODE,
            'python' => <<<'CODE'
n = int(input())
atasan = [0, 0] + (list(map(int, input().split())) if n > 1 else [])
sz = [1] * (n + 1)
for i in range(n, 1, -1):
    sz[atasan[i]] += sz[i]
print(*[s - 1 for s in sz[1:]])
CODE,
        ],
        'editorial' => '<p>Banyak bawahan karyawan v sama dengan <strong>ukuran subtree</strong> v dikurangi 1 (dirinya sendiri). Ukuran subtree dihitung dari bawah ke atas: <code>sz[atasan] += sz[anak]</code>.</p>
<p>Karena atasan selalu bernomor lebih kecil, cukup loop i dari N turun ke 2; tidak perlu DFS. (Jika jaminan itu tidak ada, gunakan urutan BFS dari akar lalu proses terbalik.)</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
        'hints' => [
            'Banyak bawahan = ukuran subtree − 1.',
            'Ukuran subtree atasan = 1 + jumlah ukuran subtree bawahan langsungnya. Dalam urutan apa harus dihitung?',
            'Atasan bernomor lebih kecil, jadi loop i dari N turun ke 2: sz[atasan[i]] += sz[i].',
        ],
    ],

    [
        'slug' => 'diameter-pohon',
        'lesson' => 'pohon',
        'title' => 'Jalur Pendakian Terpanjang',
        'difficulty' => 'Sedang',
        'tags' => ['pohon', 'diameter', 'bfs'],
        'statement' => '<p>Kawasan pegunungan punya <strong>N</strong> pos pendakian dan N − 1 jalan setapak; semua pos terhubung dan tidak ada jalan yang melingkar (pohon). Pengelola ingin mengumumkan <strong>jalur terpanjang</strong>: dua pos yang jarak tempuhnya paling jauh, diukur dari banyak jalan setapak yang dilewati.</p>
<p>Berapa panjang jalur itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N − 1 baris berikutnya berisi <code>u v</code>.</p>',
        'output_format' => '<p>Panjang diameter (banyak sisi).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "6\n1 2\n1 3\n3 4\n3 5\n5 6\n", 'explanation' => 'Jalur 2 – 1 – 3 – 5 – 6 melewati 4 jalan.'],
        ],
        'tests' => function () use ($treeInput) {
            return ["1\n", "2\n1 2\n", $treeInput(10), $treeInput(1000), $treeInput(200000), $treeInput(200000, 'garis'), $treeInput(200000, 'bintang')];
        },
        'solve' => function (string $input) use ($readTree, $bfsPhp) {
            [$n, $adj] = $readTree($input);
            $d = $bfsPhp($n, $adj, 1);
            $a = array_search(max($d), $d, true);
            $d2 = $bfsPhp($n, $adj, $a);

            return (string) max($d2);
        },
        'starter' => $st($cppTree, $jsTree, $pyTree),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<vector<int>> adj;

// BFS dari s; kembalikan pasangan (simpul terjauh, jaraknya)
pair<int, int> terjauh(int s) {
    vector<int> dist(n + 1, -1);
    queue<int> q;
    dist[s] = 0;
    q.push(s);
    int far = s;
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        if (dist[u] > dist[far]) far = u;
        for (int v : adj[u])
            if (dist[v] == -1) {
                dist[v] = dist[u] + 1;
                q.push(v);
            }
    }
    return {far, dist[far]};
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n;
    adj.assign(n + 1, {});
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
    int a = terjauh(1).first;          // ujung pertama diameter
    cout << terjauh(a).second << '\n'; // jarak terjauh dari ujung itu
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
function terjauh(s) {
  const dist = new Int32Array(n + 1).fill(-1);
  dist[s] = 0;
  const q = [s];
  let far = s;
  for (let h = 0; h < q.length; h++) {
    const u = q[h];
    if (dist[u] > dist[far]) far = u;
    for (const v of adj[u]) if (dist[v] === -1) { dist[v] = dist[u] + 1; q.push(v); }
  }
  return [far, dist[far]];
}
const [a] = terjauh(1);
console.log(terjauh(a)[1]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

def terjauh(s):
    dist = [-1] * (n + 1)
    dist[s] = 0
    q = [s]
    for u in q:
        for v in adj[u]:
            if dist[v] == -1:
                dist[v] = dist[u] + 1
                q.append(v)
    far = q[-1]          # simpul terakhir BFS punya jarak terbesar
    return far, dist[far]

a, _ = terjauh(1)
print(terjauh(a)[1])
CODE,
        ],
        'editorial' => '<p>Diameter pohon dicari dengan <strong>dua kali BFS</strong>: BFS dari simpul mana pun (misalnya 1) menemukan simpul terjauh a, yang pasti salah satu ujung diameter. BFS kedua dari a menemukan ujung lainnya, dan jaraknya adalah panjang diameter.</p>
<p>Alternatif DP: untuk setiap simpul, ambil dua tinggi subtree anak terbesar; diameter = maksimum (tinggi1 + tinggi2) di semua simpul.</p>
<p><strong>Kompleksitas:</strong> O(N). Hindari DFS rekursif untuk pohon berbentuk garis sepanjang 2 · 10<sup>5</sup>.</p>',
        'hints' => [
            'Mencoba BFS dari setiap simpul butuh O(N²), terlalu lambat.',
            'Simpul terjauh dari simpul mana pun pasti salah satu ujung diameter.',
            'BFS dari 1 → dapat a. BFS dari a → jarak terjauh adalah jawabannya.',
        ],
        'sample_visual' => 'tree',
    ],

    [
        'slug' => 'jumlah-jarak',
        'lesson' => 'pohon',
        'title' => 'Lokasi Gudang Terbaik',
        'difficulty' => 'Sulit',
        'tags' => ['pohon', 'rerooting', 'dp'],
        'statement' => '<p>Ada <strong>N</strong> toko yang dihubungkan N − 1 jalan berbentuk pohon (setiap jalan panjangnya 1). Sebuah gudang akan dibangun di salah satu toko. Jika gudang ada di toko v, biaya pengiriman harian adalah <strong>jumlah jarak</strong> dari v ke semua toko lain.</p>
<p>Cetak biaya pengiriman untuk <strong>setiap</strong> kemungkinan lokasi gudang.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N − 1 baris berikutnya berisi <code>u v</code>.</p>',
        'output_format' => '<p>N bilangan: jumlah jarak dari toko 1, 2, …, N ke semua toko.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "5\n1 2\n1 3\n3 4\n3 5\n", 'explanation' => 'Dari toko 1: jarak ke 2, 3, 4, 5 adalah 1, 1, 2, 2 → total 6. Dari toko 3: 2 + 1 + 1 + 1 = 5.'],
        ],
        'tests' => function () use ($treeInput) {
            return ["1\n", "2\n2 1\n", $treeInput(8), $treeInput(1000), $treeInput(200000), $treeInput(200000, 'garis'), $treeInput(200000, 'bintang')];
        },
        'solve' => function (string $input) use ($readTree) {
            [$n, $adj] = $readTree($input);
            $par = array_fill(0, $n + 1, 0);
            $depth = array_fill(0, $n + 1, 0);
            $order = [1];
            $par[1] = -1;
            for ($h = 0; $h < count($order); $h++) {
                $u = $order[$h];
                foreach ($adj[$u] as $v) {
                    if ($v !== $par[$u]) {
                        $par[$v] = $u;
                        $depth[$v] = $depth[$u] + 1;
                        $order[] = $v;
                    }
                }
            }
            $sz = array_fill(0, $n + 1, 1);
            for ($i = $n - 1; $i >= 1; $i--) {
                $sz[$par[$order[$i]]] += $sz[$order[$i]];
            }
            $ans = array_fill(0, $n + 1, 0);
            $ans[1] = array_sum($depth);
            for ($i = 1; $i < $n; $i++) {
                $v = $order[$i];
                $ans[$v] = $ans[$par[$v]] - $sz[$v] + ($n - $sz[$v]);
            }

            return implode(' ', array_slice($ans, 1));
        },
        'starter' => $st($cppTree, $jsTree, $pyTree),
        'solutions' => [
            'cpp' => <<<'CODE'
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

    // 1) BFS dari akar 1: orang tua, kedalaman, urutan (orang tua sebelum anak)
    vector<int> par(n + 1, 0), urutan = {1};
    vector<long long> depth(n + 1, 0);
    par[1] = -1;
    for (int h = 0; h < (int)urutan.size(); h++) {
        int u = urutan[h];
        for (int v : adj[u])
            if (v != par[u]) {
                par[v] = u;
                depth[v] = depth[u] + 1;
                urutan.push_back(v);
            }
    }
    // 2) Ukuran subtree, dari bawah ke atas
    vector<long long> sz(n + 1, 1);
    for (int i = n - 1; i >= 1; i--) sz[par[urutan[i]]] += sz[urutan[i]];

    // 3) Rerooting: pindah gudang dari orang tua p ke anak v.
    //    sz[v] toko menjadi 1 lebih dekat, n - sz[v] toko menjadi 1 lebih jauh.
    vector<long long> jawab(n + 1, 0);
    for (int v = 1; v <= n; v++) jawab[1] += depth[v];
    for (int i = 1; i < n; i++) {
        int v = urutan[i];
        jawab[v] = jawab[par[v]] - sz[v] + (n - sz[v]);
    }
    for (int v = 1; v <= n; v++) cout << jawab[v] << (v < n ? ' ' : '\n');
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const par = new Int32Array(n + 1), depth = new Float64Array(n + 1), urutan = [1];
par[1] = -1;
for (let h = 0; h < urutan.length; h++) {
  const u = urutan[h];
  for (const v of adj[u]) if (v !== par[u]) { par[v] = u; depth[v] = depth[u] + 1; urutan.push(v); }
}
const sz = new Float64Array(n + 1).fill(1);
for (let i = n - 1; i >= 1; i--) sz[par[urutan[i]]] += sz[urutan[i]];
const jawab = new Float64Array(n + 1);
for (let v = 1; v <= n; v++) jawab[1] += depth[v];
for (let i = 1; i < n; i++) {
  const v = urutan[i];
  jawab[v] = jawab[par[v]] - sz[v] + (n - sz[v]);
}
console.log(Array.from(jawab.slice(1)).join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

par = [0] * (n + 1)
depth = [0] * (n + 1)
par[1] = -1
urutan = [1]
for u in urutan:
    for v in adj[u]:
        if v != par[u]:
            par[v] = u
            depth[v] = depth[u] + 1
            urutan.append(v)

sz = [1] * (n + 1)
for v in reversed(urutan[1:]):
    sz[par[v]] += sz[v]

jawab = [0] * (n + 1)
jawab[1] = sum(depth)
for v in urutan[1:]:
    jawab[v] = jawab[par[v]] - sz[v] + (n - sz[v])
print(*jawab[1:])
CODE,
        ],
        'editorial' => '<p>BFS dari setiap toko butuh O(N²), terlalu lambat untuk N = 2 · 10<sup>5</sup>. Gunakan <strong>rerooting</strong>:</p>
<ol><li>Hitung jawaban untuk akar 1: jumlah kedalaman semua simpul.</li>
<li>Hitung ukuran subtree <code>sz[v]</code> untuk akar 1.</li>
<li>Pindahkan gudang dari p ke anaknya v. Toko di subtree v (ada <code>sz[v]</code>) menjadi 1 lebih dekat; sisanya (<code>N − sz[v]</code>) menjadi 1 lebih jauh. Jadi <code>jawab[v] = jawab[p] − sz[v] + (N − sz[v])</code>.</li></ol>
<p>Proses simpul dalam urutan BFS agar jawaban orang tua selalu sudah tersedia. Jawaban bisa mencapai ±2 · 10<sup>10</sup>: gunakan <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
        'hints' => [
            'Menghitung dari setiap toko satu per satu terlalu lambat. Jika jawaban untuk toko p sudah diketahui, bagaimana jawaban anaknya v berubah?',
            'Saat gudang pindah dari p ke v, toko di subtree v mendekat 1 dan toko lainnya menjauh 1.',
            'jawab[v] = jawab[p] − sz[v] + (N − sz[v]). Mulai dari jawab[1] = jumlah kedalaman.',
        ],
        'sample_visual' => 'tree',
    ],
];
