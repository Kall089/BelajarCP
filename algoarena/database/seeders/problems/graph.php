<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Graph.
 * 'solve'  : solusi referensi PHP untuk menghitung output setiap tes.
 * 'tests'  : fungsi pembangkit input tes tersembunyi.
 */

$readGraph = function (string $input, bool $weighted = false, int $headerCount = 2): array {
    $lines = T::lines($input);
    $head = T::ints($lines[0]);
    $n = $head[0];
    $m = $head[1];
    $edges = [];
    for ($i = 1; $i <= $m; $i++) {
        $edges[] = T::ints($lines[$i]);
    }

    return [$head, $n, $m, $edges, $lines];
};

$dijkstra = function (int $n, array $adj, int $src): array {
    $dist = array_fill(0, $n + 1, PHP_INT_MAX);
    $dist[$src] = 0;
    $pq = new SplPriorityQueue;
    $pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    $pq->insert($src, 0);
    while (! $pq->isEmpty()) {
        $top = $pq->extract();
        $u = $top['data'];
        $d = -$top['priority'];
        if ($d > $dist[$u]) {
            continue;
        }
        foreach ($adj[$u] as [$v, $w]) {
            if ($dist[$v] > $dist[$u] + $w) {
                $dist[$v] = $dist[$u] + $w;
                $pq->insert($v, -$dist[$v]);
            }
        }
    }

    return $dist;
};

$gridBfs = function (array $grid, int $sr, int $sc): array {
    $R = count($grid);
    $C = strlen($grid[0]);
    $dist = array_fill(0, $R, array_fill(0, $C, -1));
    $dist[$sr][$sc] = 0;
    $queue = [[$sr, $sc]];
    for ($h = 0; $h < count($queue); $h++) {
        [$r, $c] = $queue[$h];
        foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$dr, $dc]) {
            $nr = $r + $dr;
            $nc = $c + $dc;
            if ($nr < 0 || $nr >= $R || $nc < 0 || $nc >= $C || $grid[$nr][$nc] === '#' || $dist[$nr][$nc] !== -1) {
                continue;
            }
            $dist[$nr][$nc] = $dist[$r][$c] + 1;
            $queue[] = [$nr, $nc];
        }
    }

    return $dist;
};

$readEdgesStarterJs = <<<'CODE'
// readInts() membaca satu baris berisi angka-angka
const [n, m] = readInts();
const edges = [];
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  edges.push([u, v]);
}

// Tulis solusimu di sini


console.log();
CODE;

$readEdgesStarterPy = <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
edges = [tuple(map(int, input().split())) for _ in range(m)]

# Tulis solusimu di sini


print()
CODE;

return [
    // ═════════════════════════════ Representasi Graph ═════════════════════════════
    [
        'slug' => 'derajat-simpul',
        'lesson' => 'representasi-graph',
        'title' => 'Derajat Simpul',
        'difficulty' => 'Mudah',
        'tags' => ['graph', 'representasi'],
        'statement' => '<p>Di kelas XI RPL ada <strong>N</strong> siswa yang diberi nomor 1 sampai N. Beberapa pasang siswa saling berteman, dan pertemanan selalu dua arah.</p>
<p>Wali kelas ingin tahu <strong>berapa banyak teman</strong> yang dimiliki setiap siswa. Dalam istilah graph, jumlah teman sebuah simpul disebut <strong>derajat</strong> (<em>degree</em>).</p>',
        'input_format' => '<p>Baris pertama berisi dua bilangan bulat <code>N</code> dan <code>M</code>, yaitu banyak siswa dan banyak pasangan teman.</p>
<p><code>M</code> baris berikutnya masing-masing berisi dua bilangan <code>u v</code> yang artinya siswa <code>u</code> dan <code>v</code> berteman. Tidak ada pasangan yang ditulis dua kali.</p>',
        'output_format' => '<p>Satu baris berisi <code>N</code> bilangan yang dipisah spasi. Bilangan ke-i adalah derajat siswa ke-i.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1000</li><li>0 ≤ M ≤ 5000</li><li>1 ≤ u, v ≤ N, u ≠ v</li></ul>',
        'samples' => [
            ['input' => "5 4\n1 2\n1 3\n2 3\n3 4\n", 'explanation' => 'Siswa 3 berteman dengan 1, 2, dan 4 sehingga derajatnya 3. Siswa 5 tidak punya teman sehingga derajatnya 0.'],
            ['input' => "3 0\n", 'explanation' => 'Tidak ada pertemanan sama sekali.'],
        ],
        'tests' => function () {
            $tests = [];
            foreach ([[1, 0], [2, 1], [10, 15], [100, 300], [500, 2000], [1000, 5000], [1000, 999], [800, 0]] as [$n, $m]) {
                $tests[] = T::graphInput("$n ".min($m, intdiv($n * ($n - 1), 2)), T::edges($n, $m));
            }

            return $tests;
        },
        'solve' => function (string $input) use ($readGraph) {
            [, $n, , $edges] = $readGraph($input);
            $deg = array_fill(1, $n, 0);
            foreach ($edges as [$u, $v]) {
                $deg[$u]++;
                $deg[$v]++;
            }

            return implode(' ', $deg);
        },
        'starter' => ['javascript' => $readEdgesStarterJs, 'python' => $readEdgesStarterPy],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const deg = new Array(n + 1).fill(0);

for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  deg[u]++; // sisi u-v menambah derajat u
  deg[v]++; // ...dan juga derajat v (tak berarah)
}

console.log(deg.slice(1).join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
deg = [0] * (n + 1)

for _ in range(m):
    u, v = map(int, input().split())
    deg[u] += 1  # sisi u-v menambah derajat u
    deg[v] += 1  # ...dan juga derajat v (tak berarah)

print(*deg[1:])
CODE,
        ],
        'editorial' => '<p>Setiap sisi <code>u – v</code> pada graph tak berarah menyumbang <strong>1 derajat untuk u</strong> dan <strong>1 derajat untuk v</strong>. Jadi kita cukup menyiapkan array <code>deg</code> berukuran N+1 yang awalnya 0, lalu untuk setiap sisi tambahkan <code>deg[u]</code> dan <code>deg[v]</code>.</p>
<p><strong>Kompleksitas:</strong> O(N + M). Kita bahkan tidak perlu menyimpan graph-nya!</p>
<p><strong>Fakta menarik:</strong> jumlah semua derajat selalu sama dengan <code>2 × M</code> (Handshaking Lemma), karena setiap sisi dihitung dua kali.</p>',
    ],

    [
        'slug' => 'daftar-tetangga',
        'lesson' => 'representasi-graph',
        'title' => 'Daftar Tetangga',
        'difficulty' => 'Mudah',
        'tags' => ['graph', 'adjacency list'],
        'statement' => '<p>Peta desa digambarkan sebagai graph dengan <strong>N</strong> rumah dan <strong>M</strong> jalan dua arah. Kepala desa ingin mencetak <strong>adjacency list</strong>: untuk setiap rumah, daftar rumah lain yang terhubung langsung oleh jalan.</p>
<p>Tetangga setiap rumah harus dicetak <strong>terurut dari nomor terkecil</strong>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. <code>M</code> baris berikutnya berisi <code>u v</code>, yaitu jalan dua arah antara rumah <code>u</code> dan <code>v</code>.</p>',
        'output_format' => '<p>Cetak <code>N</code> baris. Baris ke-i berformat <code>i: t1 t2 ... tk</code> dengan t1 &lt; t2 &lt; ... &lt; tk adalah tetangga rumah i. Jika rumah i tidak punya tetangga, cetak <code>i:</code> saja.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1000</li><li>0 ≤ M ≤ 5000</li><li>Tidak ada jalan ganda maupun jalan ke rumah sendiri.</li></ul>',
        'samples' => [
            ['input' => "4 4\n1 2\n3 1\n2 3\n4 2\n", 'explanation' => 'Rumah 2 terhubung ke 1, 3, dan 4. Perhatikan bahwa jalan "3 1" juga membuat 3 menjadi tetangga 1.'],
        ],
        'tests' => function () {
            $tests = [T::graphInput('1 0', []), T::graphInput('3 0', [])];
            foreach ([[6, 8], [50, 120], [300, 1500], [1000, 5000], [1000, 400]] as [$n, $m]) {
                $tests[] = T::graphInput("$n $m", T::edges($n, $m));
            }

            return $tests;
        },
        'solve' => function (string $input) use ($readGraph) {
            [, $n, , $edges] = $readGraph($input);
            $adj = array_fill(1, $n, []);
            foreach ($edges as [$u, $v]) {
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            $out = [];
            for ($i = 1; $i <= $n; $i++) {
                sort($adj[$i]);
                $out[] = rtrim("$i: ".implode(' ', $adj[$i]));
            }

            return implode("\n", $out);
        },
        'starter' => ['javascript' => $readEdgesStarterJs, 'python' => $readEdgesStarterPy],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);

for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}

const out = [];
for (let i = 1; i <= n; i++) {
  adj[i].sort((a, b) => a - b); // urutkan sebagai angka, bukan string!
  out.push(`${i}: ${adj[i].join(" ")}`.trimEnd());
}
console.log(out.join("\n"));
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

out = []
for i in range(1, n + 1):
    tetangga = " ".join(map(str, sorted(adj[i])))
    out.append(f"{i}: {tetangga}".rstrip())
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Inilah representasi graph yang paling sering dipakai di competitive programming: <strong>adjacency list</strong>. Buat <code>N+1</code> list kosong, lalu untuk setiap sisi <code>u v</code> masukkan <code>v</code> ke list <code>u</code> dan <code>u</code> ke list <code>v</code>.</p>
<p><strong>Jebakan JavaScript:</strong> <code>[10, 9, 2].sort()</code> menghasilkan <code>[10, 2, 9]</code> karena dibandingkan sebagai string. Selalu pakai <code>sort((a, b) =&gt; a - b)</code> untuk angka.</p>
<p><strong>Kompleksitas:</strong> O(N + M log M) karena pengurutan.</p>',
    ],

    // ═════════════════════════════ BFS ═════════════════════════════
    [
        'slug' => 'jarak-pertemanan',
        'lesson' => 'bfs',
        'title' => 'Jarak Pertemanan',
        'difficulty' => 'Mudah',
        'tags' => ['graph', 'bfs', 'shortest path'],
        'statement' => '<p>Di media sosial sekolah ada <strong>N</strong> pengguna. Kamu adalah pengguna nomor <strong>1</strong>. Teman langsungmu berjarak 1, teman dari temanmu berjarak 2, dan seterusnya.</p>
<p>Hitung <strong>jarak pertemanan terdekat</strong> dari kamu ke setiap pengguna. Jika seorang pengguna sama sekali tidak bisa dijangkau, jaraknya <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. <code>M</code> baris berikutnya berisi <code>u v</code> yang berarti u dan v berteman (dua arah).</p>',
        'output_format' => '<p>Satu baris berisi <code>N</code> bilangan: jarak dari pengguna 1 ke pengguna 1, 2, ..., N.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 2000</li><li>0 ≤ M ≤ 10000</li></ul>',
        'samples' => [
            ['input' => "6 5\n1 2\n1 3\n2 4\n3 4\n4 5\n", 'explanation' => 'Pengguna 4 bisa dicapai lewat 1 → 2 → 4 (jarak 2). Pengguna 6 tidak terhubung sehingga -1.'],
        ],
        'tests' => function () {
            $tests = [T::graphInput('1 0', []), T::graphInput('2 0', []), T::graphInput('2 1', [[2, 1]])];
            $line = [];
            for ($i = 1; $i < 2000; $i++) {
                $line[] = [$i, $i + 1];
            }
            T::shuffle($line);
            $tests[] = T::graphInput('2000 1999', $line);
            foreach ([[30, 40, true], [500, 1200, false], [2000, 10000, true], [2000, 2500, false]] as [$n, $m, $conn]) {
                $e = T::edges($n, $m, false, $conn);
                $tests[] = T::graphInput("$n ".count($e), $e);
            }

            return $tests;
        },
        'solve' => function (string $input) use ($readGraph) {
            [, $n, , $edges] = $readGraph($input);
            $adj = array_fill(1, $n, []);
            foreach ($edges as [$u, $v]) {
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            $dist = array_fill(1, $n, -1);
            $dist[1] = 0;
            $q = [1];
            for ($h = 0; $h < count($q); $h++) {
                $u = $q[$h];
                foreach ($adj[$u] as $v) {
                    if ($dist[$v] === -1) {
                        $dist[$v] = $dist[$u] + 1;
                        $q[] = $v;
                    }
                }
            }

            return implode(' ', $dist);
        },
        'starter' => ['javascript' => $readEdgesStarterJs, 'python' => $readEdgesStarterPy],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}

const dist = new Array(n + 1).fill(-1);
dist[1] = 0;
const queue = [1];
let head = 0; // pakai indeks, jangan queue.shift() yang O(n)

while (head < queue.length) {
  const u = queue[head++];
  for (const v of adj[u]) {
    if (dist[v] === -1) {
      dist[v] = dist[u] + 1;
      queue.push(v);
    }
  }
}

console.log(dist.slice(1).join(" "));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

dist = [-1] * (n + 1)
dist[1] = 0
queue = deque([1])

while queue:
    u = queue.popleft()
    for v in adj[u]:
        if dist[v] == -1:
            dist[v] = dist[u] + 1
            queue.append(v)

print(*dist[1:])
CODE,
        ],
        'editorial' => '<p>Ini adalah soal BFS paling murni. Karena setiap sisi "berbobot 1", BFS menjamin simpul pertama kali ditemukan dengan <strong>jarak terkecil</strong>: semua simpul berjarak 1 diproses sebelum simpul berjarak 2, dan seterusnya.</p>
<ol><li>Isi <code>dist</code> dengan -1 (belum dikunjungi), lalu <code>dist[1] = 0</code>.</li>
<li>Masukkan 1 ke antrian.</li>
<li>Ambil simpul terdepan <code>u</code>. Untuk setiap tetangga <code>v</code> yang belum dikunjungi, set <code>dist[v] = dist[u] + 1</code> dan masukkan ke antrian.</li></ol>
<p><strong>Tips JavaScript:</strong> <code>Array.shift()</code> itu O(n). Untuk antrian besar, pakai variabel <code>head</code> sebagai penunjuk depan antrian.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
    ],

    [
        'slug' => 'labirin',
        'lesson' => 'bfs',
        'title' => 'Keluar dari Labirin',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'bfs', 'grid'],
        'statement' => '<p>Kamu terjebak di labirin berbentuk grid <strong>R × C</strong>. Setiap petak berisi salah satu karakter:</p>
<ul><li><code>.</code> lantai yang bisa dilewati</li><li><code>#</code> tembok</li><li><code>S</code> posisi awalmu</li><li><code>E</code> pintu keluar</li></ul>
<p>Dalam satu langkah kamu bisa bergerak ke atas, bawah, kiri, atau kanan (tidak diagonal) dan tidak boleh menembus tembok. Berapa <strong>langkah minimum</strong> untuk mencapai pintu keluar?</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>. <code>R</code> baris berikutnya masing-masing berisi string sepanjang <code>C</code>. Dijamin ada tepat satu <code>S</code> dan satu <code>E</code>.</p>',
        'output_format' => '<p>Langkah minimum dari S ke E, atau <code>-1</code> jika pintu keluar tidak bisa dicapai.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 100</li><li>R × C ≥ 2</li></ul>',
        'samples' => [
            ['input' => "4 5\nS.#..\n..#.#\n#...#\n##.E.\n", 'explanation' => 'Salah satu rute terpendek: turun, kanan, turun, kanan, kanan, turun (6 langkah).'],
            ['input' => "2 3\nS#E\n.#.\n", 'explanation' => 'Kolom tengah seluruhnya tembok sehingga E tidak bisa dicapai.'],
        ],
        'tests' => function () {
            $make = function (int $r, int $c, float $p) {
                $g = T::grid($r, $c, $p);
                $sr = mt_rand(0, $r - 1);
                $sc = mt_rand(0, $c - 1);
                do {
                    $er = mt_rand(0, $r - 1);
                    $ec = mt_rand(0, $c - 1);
                } while ($er === $sr && $ec === $sc);
                $g[$sr][$sc] = 'S';
                $g[$er][$ec] = 'E';

                return "$r $c\n".implode("\n", $g)."\n";
            };
            $tests = ["1 2\nSE\n", "1 3\nS#E\n", $make(5, 5, 0.2), $make(20, 30, 0.25), $make(50, 50, 0.3), $make(100, 100, 0.2), $make(100, 100, 0.45), $make(100, 100, 0.0)];
            // Labirin ular panjang
            $g = [];
            for ($i = 0; $i < 99; $i++) {
                $g[] = $i % 2 === 0 ? str_repeat('.', 100) : ($i % 4 === 1 ? str_repeat('#', 99).'.' : '.'.str_repeat('#', 99));
            }
            $g[0][0] = 'S';
            $g[98][99] = 'E';
            $tests[] = "99 100\n".implode("\n", $g)."\n";

            return $tests;
        },
        'solve' => function (string $input) use ($gridBfs) {
            $lines = T::lines($input);
            [$r] = T::ints($lines[0]);
            $grid = array_slice($lines, 1, $r);
            foreach ($grid as $i => $row) {
                if (($p = strpos($row, 'S')) !== false) {
                    [$sr, $sc] = [$i, $p];
                }
                if (($p = strpos($row, 'E')) !== false) {
                    [$er, $ec] = [$i, $p];
                }
            }

            return (string) $gridBfs($grid, $sr, $sc)[$er][$ec];
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const grid = [];
for (let i = 0; i < R; i++) grid.push(readLine());

// grid[r][c] adalah karakter pada baris r, kolom c
// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
grid = [input().strip() for _ in range(R)]

# grid[r][c] adalah karakter pada baris r, kolom c
# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const grid = [];
for (let i = 0; i < R; i++) grid.push(readLine());

let sr, sc, er, ec;
for (let r = 0; r < R; r++) {
  for (let c = 0; c < C; c++) {
    if (grid[r][c] === "S") [sr, sc] = [r, c];
    if (grid[r][c] === "E") [er, ec] = [r, c];
  }
}

const dist = Array.from({ length: R }, () => new Array(C).fill(-1));
const dr = [-1, 1, 0, 0];
const dc = [0, 0, -1, 1];
const queue = [[sr, sc]];
let head = 0;
dist[sr][sc] = 0;

while (head < queue.length) {
  const [r, c] = queue[head++];
  for (let k = 0; k < 4; k++) {
    const nr = r + dr[k];
    const nc = c + dc[k];
    if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue; // keluar grid
    if (grid[nr][nc] === "#" || dist[nr][nc] !== -1) continue; // tembok / sudah dikunjungi
    dist[nr][nc] = dist[r][c] + 1;
    queue.push([nr, nc]);
  }
}

console.log(dist[er][ec]);
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

R, C = map(int, input().split())
grid = [input().strip() for _ in range(R)]

for r in range(R):
    for c in range(C):
        if grid[r][c] == "S":
            sr, sc = r, c
        elif grid[r][c] == "E":
            er, ec = r, c

dist = [[-1] * C for _ in range(R)]
dist[sr][sc] = 0
queue = deque([(sr, sc)])

while queue:
    r, c = queue.popleft()
    for dr, dc in ((-1, 0), (1, 0), (0, -1), (0, 1)):
        nr, nc = r + dr, c + dc
        if 0 <= nr < R and 0 <= nc < C and grid[nr][nc] != "#" and dist[nr][nc] == -1:
            dist[nr][nc] = dist[r][c] + 1
            queue.append((nr, nc))

print(dist[er][ec])
CODE,
        ],
        'editorial' => '<p>Grid adalah graph yang "tersembunyi": setiap petak adalah simpul, dan dua petak bersebelahan (atas/bawah/kiri/kanan) yang bukan tembok terhubung oleh sisi.</p>
<p>Karena setiap langkah berbobot 1, <strong>BFS dari S</strong> langsung memberikan langkah minimum ke semua petak. Jawabannya adalah <code>dist</code> di posisi E.</p>
<p><strong>Trik arah:</strong> simpan perpindahan dalam array <code>dr = [-1, 1, 0, 0]</code> dan <code>dc = [0, 0, -1, 1]</code> supaya kode tetangga cukup ditulis sekali dalam loop.</p>
<p><strong>Kompleksitas:</strong> O(R × C).</p>',
    ],

    // ═════════════════════════════ DFS ═════════════════════════════
    [
        'slug' => 'hitung-pulau',
        'lesson' => 'dfs',
        'title' => 'Hitung Pulau',
        'difficulty' => 'Mudah',
        'tags' => ['graph', 'dfs', 'flood fill'],
        'statement' => '<p>Citra satelit sebuah kepulauan diubah menjadi grid <strong>R × C</strong>. Petak <code>#</code> adalah daratan dan petak <code>.</code> adalah lautan.</p>
<p>Sebuah <strong>pulau</strong> adalah kumpulan petak daratan yang saling terhubung secara <strong>horizontal atau vertikal</strong> (diagonal tidak dihitung). Berapa banyak pulau di peta tersebut?</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C</code>, diikuti <code>R</code> baris string sepanjang <code>C</code>.</p>',
        'output_format' => '<p>Satu bilangan: banyaknya pulau.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 30</li></ul><p><em>Catatan:</em> kedalaman rekursi di judge browser terbatas (±1500 level). DFS rekursif aman untuk batasan ini, tapi biasakan juga versi iteratif.</p>',
        'samples' => [
            ['input' => "4 5\n##..#\n#...#\n..#..\n...##\n", 'explanation' => 'Ada 4 pulau: kiri atas (3 petak), kanan atas (2 petak), tengah (1 petak), dan kanan bawah (2 petak).'],
            ['input' => "2 2\n#.\n.#\n", 'explanation' => 'Dua petak yang hanya bersentuhan secara diagonal dihitung sebagai 2 pulau berbeda.'],
        ],
        'tests' => function () {
            $wrap = fn (array $g) => count($g).' '.strlen($g[0])."\n".implode("\n", $g)."\n";
            $tests = [$wrap(['.']), $wrap(['#']), $wrap(T::grid(1, 30, 0.5)), $wrap(T::grid(10, 10, 0.4)), $wrap(T::grid(30, 30, 0.45)), $wrap(T::grid(30, 30, 0.6)), $wrap(T::grid(30, 30, 1.0))];
            $checker = [];
            for ($i = 0; $i < 30; $i++) {
                $row = '';
                for ($j = 0; $j < 30; $j++) {
                    $row .= ($i + $j) % 2 === 0 ? '#' : '.';
                }
                $checker[] = $row;
            }
            $tests[] = $wrap($checker);

            return $tests;
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C] = T::ints($lines[0]);
            $g = array_slice($lines, 1, $R);
            $seen = [];
            $count = 0;
            for ($r = 0; $r < $R; $r++) {
                for ($c = 0; $c < $C; $c++) {
                    if ($g[$r][$c] !== '#' || isset($seen["$r,$c"])) {
                        continue;
                    }
                    $count++;
                    $stack = [[$r, $c]];
                    $seen["$r,$c"] = true;
                    while ($stack) {
                        [$x, $y] = array_pop($stack);
                        foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$dx, $dy]) {
                            $nx = $x + $dx;
                            $ny = $y + $dy;
                            if ($nx >= 0 && $nx < $R && $ny >= 0 && $ny < $C && $g[$nx][$ny] === '#' && ! isset($seen["$nx,$ny"])) {
                                $seen["$nx,$ny"] = true;
                                $stack[] = [$nx, $ny];
                            }
                        }
                    }
                }
            }

            return (string) $count;
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const grid = [];
for (let i = 0; i < R; i++) grid.push(readLine());

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
grid = [input().strip() for _ in range(R)]

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [R, C] = readInts();
const grid = [];
for (let i = 0; i < R; i++) grid.push(readLine());

const seen = Array.from({ length: R }, () => new Array(C).fill(false));
const moves = [[-1, 0], [1, 0], [0, -1], [0, 1]];

function dfs(r, c) {
  seen[r][c] = true;
  for (const [dr, dc] of moves) {
    const nr = r + dr;
    const nc = c + dc;
    if (nr >= 0 && nr < R && nc >= 0 && nc < C && grid[nr][nc] === "#" && !seen[nr][nc]) {
      dfs(nr, nc);
    }
  }
}

let pulau = 0;
for (let r = 0; r < R; r++) {
  for (let c = 0; c < C; c++) {
    if (grid[r][c] === "#" && !seen[r][c]) {
      pulau++;   // daratan baru yang belum dijelajahi = pulau baru
      dfs(r, c); // tandai seluruh pulau ini
    }
  }
}

console.log(pulau);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C = map(int, input().split())
grid = [input().strip() for _ in range(R)]
seen = [[False] * C for _ in range(R)]

def jelajah(r, c):
    # DFS iteratif dengan stack (aman dari batas rekursi Python)
    stack = [(r, c)]
    seen[r][c] = True
    while stack:
        x, y = stack.pop()
        for dx, dy in ((-1, 0), (1, 0), (0, -1), (0, 1)):
            nx, ny = x + dx, y + dy
            if 0 <= nx < R and 0 <= ny < C and grid[nx][ny] == "#" and not seen[nx][ny]:
                seen[nx][ny] = True
                stack.append((nx, ny))

pulau = 0
for r in range(R):
    for c in range(C):
        if grid[r][c] == "#" and not seen[r][c]:
            pulau += 1
            jelajah(r, c)

print(pulau)
CODE,
        ],
        'editorial' => '<p>Teknik ini disebut <strong>flood fill</strong>: seperti menuang cat ke satu petak, lalu cat menyebar ke semua petak daratan yang terhubung.</p>
<ol><li>Telusuri grid dari kiri atas.</li>
<li>Saat menemukan daratan yang <em>belum dikunjungi</em>, berarti ini pulau baru: tambah penghitung.</li>
<li>Jalankan DFS dari petak itu untuk menandai <strong>seluruh</strong> pulau sebagai sudah dikunjungi, supaya tidak dihitung lagi.</li></ol>
<p><strong>Catatan Python:</strong> batas rekursi bawaan sekitar 1000. Untuk grid besar, gunakan DFS iteratif dengan stack seperti pada solusi, atau panggil <code>sys.setrecursionlimit</code>.</p>
<p><strong>Kompleksitas:</strong> O(R × C). Setiap petak dikunjungi tepat sekali.</p>',
    ],

    [
        'slug' => 'komponen-terhubung',
        'lesson' => 'dfs',
        'title' => 'Kelompok Belajar',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'dfs', 'komponen'],
        'statement' => '<p>Ada <strong>N</strong> siswa dan <strong>M</strong> pasangan siswa yang pernah belajar bersama. Jika A pernah belajar dengan B dan B dengan C, maka A, B, C berada dalam <strong>kelompok belajar</strong> yang sama.</p>
<p>Tentukan <strong>banyaknya kelompok</strong> dan <strong>ukuran kelompok terbesar</strong>. Siswa yang tidak pernah belajar bersama siapa pun membentuk kelompok sendiri berisi 1 orang.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>, diikuti <code>M</code> baris berisi <code>u v</code>.</p>',
        'output_format' => '<p>Dua bilangan dipisah spasi: banyaknya kelompok dan ukuran kelompok terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1000</li><li>0 ≤ M ≤ 5000</li></ul>',
        'samples' => [
            ['input' => "7 4\n1 2\n2 3\n4 5\n6 4\n", 'explanation' => 'Kelompoknya adalah {1,2,3}, {4,5,6}, dan {7}. Ada 3 kelompok, dan yang terbesar berisi 3 siswa.'],
        ],
        'tests' => function () {
            $tests = [T::graphInput('1 0', []), T::graphInput('5 0', [])];
            foreach ([[10, 6], [200, 150], [700, 600], [1000, 800], [1000, 5000], [1000, 999]] as [$n, $m]) {
                $e = T::edges($n, $m, false, $m >= $n - 1 && mt_rand(0, 1) === 1);
                $tests[] = T::graphInput("$n ".count($e), $e);
            }

            return $tests;
        },
        'solve' => function (string $input) use ($readGraph) {
            [, $n, , $edges] = $readGraph($input);
            $parent = range(0, $n);
            $find = function ($x) use (&$parent, &$find) {
                while ($parent[$x] !== $x) {
                    $parent[$x] = $parent[$parent[$x]];
                    $x = $parent[$x];
                }

                return $x;
            };
            foreach ($edges as [$u, $v]) {
                $parent[$find($u)] = $find($v);
            }
            $size = [];
            for ($i = 1; $i <= $n; $i++) {
                $r = $find($i);
                $size[$r] = ($size[$r] ?? 0) + 1;
            }

            return count($size).' '.max($size);
        },
        'starter' => ['javascript' => $readEdgesStarterJs, 'python' => $readEdgesStarterPy],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}

const seen = new Array(n + 1).fill(false);
let kelompok = 0;
let terbesar = 0;

for (let s = 1; s <= n; s++) {
  if (seen[s]) continue;
  kelompok++;
  // DFS iteratif sambil menghitung ukuran komponen
  let ukuran = 0;
  const stack = [s];
  seen[s] = true;
  while (stack.length) {
    const u = stack.pop();
    ukuran++;
    for (const v of adj[u]) {
      if (!seen[v]) {
        seen[v] = true;
        stack.push(v);
      }
    }
  }
  terbesar = Math.max(terbesar, ukuran);
}

console.log(kelompok, terbesar);
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

seen = [False] * (n + 1)
kelompok = 0
terbesar = 0

for s in range(1, n + 1):
    if seen[s]:
        continue
    kelompok += 1
    ukuran = 0
    stack = [s]
    seen[s] = True
    while stack:
        u = stack.pop()
        ukuran += 1
        for v in adj[u]:
            if not seen[v]:
                seen[v] = True
                stack.append(v)
    terbesar = max(terbesar, ukuran)

print(kelompok, terbesar)
CODE,
        ],
        'editorial' => '<p>Setiap kelompok belajar adalah sebuah <strong>komponen terhubung</strong>. Polanya sama seperti menghitung pulau:</p>
<ol><li>Loop semua simpul 1..N.</li>
<li>Jika simpul <code>s</code> belum dikunjungi, berarti ada komponen baru. Jalankan DFS dari <code>s</code> sambil <strong>menghitung</strong> berapa simpul yang dikunjungi.</li>
<li>Catat jumlah komponen dan ukuran maksimum.</li></ol>
<p><strong>Alternatif:</strong> struktur data <em>Disjoint Set Union</em> (DSU) juga bisa menyelesaikan soal ini dengan sangat cepat.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
    ],

    // ═════════════════════════════ Dijkstra ═════════════════════════════
    [
        'slug' => 'ongkos-kirim',
        'lesson' => 'dijkstra',
        'title' => 'Ongkos Kirim Termurah',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'dijkstra', 'shortest path'],
        'statement' => '<p>Sebuah jasa ekspedisi melayani <strong>N</strong> kota yang dihubungkan <strong>M</strong> rute dua arah. Setiap rute punya <strong>ongkos</strong> tertentu.</p>
<p>Paket harus dikirim dari kota <strong>1</strong> ke kota <strong>N</strong>. Berapa <strong>total ongkos termurah</strong>? Jika tidak ada rute sama sekali, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. <code>M</code> baris berikutnya berisi <code>u v w</code>: rute antara kota u dan v dengan ongkos w.</p>',
        'output_format' => '<p>Ongkos termurah dari kota 1 ke kota N, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 1000</li><li>0 ≤ M ≤ 5000</li><li>1 ≤ w ≤ 1000</li></ul>',
        'samples' => [
            ['input' => "4 5\n1 2 4\n1 3 1\n3 2 2\n2 4 5\n3 4 9\n", 'explanation' => 'Rute 1 → 3 → 2 → 4 berongkos 1 + 2 + 5 = 8, lebih murah daripada 1 → 2 → 4 (9) atau 1 → 3 → 4 (10).'],
            ['input' => "3 1\n1 2 5\n", 'explanation' => 'Kota 3 tidak terhubung ke mana pun.'],
        ],
        'tests' => function () {
            $tests = [T::graphInput('2 1', [[1, 2, 7]]), T::graphInput('2 0', [])];
            foreach ([[6, 9], [100, 300], [1000, 5000], [1000, 1200], [700, 699]] as [$n, $m]) {
                $e = T::edges($n, $m, false, mt_rand(0, 3) > 0, [1, 1000]);
                $tests[] = T::graphInput("$n ".count($e), $e);
            }
            // Jalan pintas yang banyak sisi tapi murah
            $e = [];
            for ($i = 1; $i < 500; $i++) {
                $e[] = [$i, $i + 1, 1];
            }
            $e[] = [1, 500, 1000];
            T::shuffle($e);
            $tests[] = T::graphInput('500 '.count($e), $e);

            return $tests;
        },
        'solve' => function (string $input) use ($readGraph, $dijkstra) {
            [, $n, , $edges] = $readGraph($input);
            $adj = array_fill(0, $n + 1, []);
            foreach ($edges as [$u, $v, $w]) {
                $adj[$u][] = [$v, $w];
                $adj[$v][] = [$u, $w];
            }
            $d = $dijkstra($n, $adj, 1)[$n];

            return (string) ($d === PHP_INT_MAX ? -1 : $d);
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
  adj[v].push([u, w]);
}

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))
    adj[v].append((u, w))

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
  adj[v].push([u, w]);
}

// Dijkstra versi O(N^2): cocok untuk N ≤ beberapa ribu
const dist = new Array(n + 1).fill(Infinity);
const selesai = new Array(n + 1).fill(false);
dist[1] = 0;

for (let iter = 0; iter < n; iter++) {
  // 1. pilih simpul belum selesai dengan dist terkecil
  let u = -1;
  for (let i = 1; i <= n; i++) {
    if (!selesai[i] && dist[i] < Infinity && (u === -1 || dist[i] < dist[u])) u = i;
  }
  if (u === -1) break; // sisanya tidak terjangkau
  selesai[u] = true;

  // 2. relaksasi semua sisi keluar dari u
  for (const [v, w] of adj[u]) {
    if (dist[u] + w < dist[v]) dist[v] = dist[u] + w;
  }
}

console.log(dist[n] === Infinity ? -1 : dist[n]);
CODE,
            'python' => <<<'CODE'
import sys
import heapq
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))
    adj[v].append((u, w))

INF = float("inf")
dist = [INF] * (n + 1)
dist[1] = 0
pq = [(0, 1)]  # (jarak, simpul)

while pq:
    d, u = heapq.heappop(pq)
    if d > dist[u]:
        continue  # entri lama, sudah ada yang lebih baik
    for v, w in adj[u]:
        if d + w < dist[v]:
            dist[v] = d + w
            heapq.heappush(pq, (dist[v], v))

print(-1 if dist[n] == INF else dist[n])
CODE,
        ],
        'editorial' => '<p>Karena setiap rute punya ongkos berbeda, BFS tidak lagi menjamin jawaban terpendek. Kita butuh <strong>Dijkstra</strong>.</p>
<p>Prinsipnya: selalu ambil kota dengan <em>ongkos sementara terkecil</em> yang belum final. Ongkos kota itu pasti sudah optimal, karena semua bobot positif sehingga jalan memutar lewat kota lain pasti lebih mahal. Lalu perbarui (<em>relaksasi</em>) ongkos tetangganya.</p>
<ul><li><strong>Versi O(N²)</strong>: cari minimum dengan loop biasa. Sederhana dan cukup untuk N ≤ 1000 (dipakai pada solusi JavaScript).</li>
<li><strong>Versi priority queue</strong>: O((N + M) log N), standar di lomba (dipakai pada solusi Python dengan <code>heapq</code>).</li></ul>
<p><strong>Jebakan umum:</strong> lupa memeriksa <code>if d &gt; dist[u]: continue</code> pada versi heap, sehingga simpul diproses berkali-kali.</p>',
    ],

    [
        'slug' => 'rute-tercepat',
        'lesson' => 'dijkstra',
        'title' => 'Rute Ojek Tercepat',
        'difficulty' => 'Sulit',
        'tags' => ['graph', 'dijkstra', 'berarah'],
        'statement' => '<p>Sebuah aplikasi ojek online memetakan kota sebagai <strong>N</strong> titik dan <strong>M</strong> jalan <strong>satu arah</strong>. Melewati jalan dari <code>u</code> ke <code>v</code> memakan waktu <code>w</code> menit.</p>
<p>Seorang driver berada di titik <strong>S</strong>. Untuk menampilkan estimasi waktu ke semua titik, hitung <strong>waktu tercepat</strong> dari S ke setiap titik. Titik yang tidak bisa dicapai ditampilkan <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M S</code>. <code>M</code> baris berikutnya berisi <code>u v w</code>: jalan satu arah dari u ke v selama w menit.</p>',
        'output_format' => '<p>Satu baris berisi <code>N</code> bilangan: waktu tercepat dari S ke titik 1, 2, ..., N.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1000</li><li>0 ≤ M ≤ 5000</li><li>1 ≤ w ≤ 10000</li><li>1 ≤ S ≤ N</li></ul>',
        'samples' => [
            ['input' => "5 6 1\n1 2 10\n1 3 3\n3 2 4\n2 4 2\n3 4 8\n4 1 1\n", 'explanation' => 'Ke titik 2 lebih cepat lewat 3 (3 + 4 = 7) daripada langsung (10). Titik 5 tidak bisa dicapai. Jalan 4 → 1 tidak membantu karena satu arah menuju S.'],
        ],
        'tests' => function () {
            $tests = [T::graphInput('1 0 1', []), T::graphInput('3 2 3', [[1, 2, 5], [2, 3, 5]])];
            foreach ([[8, 14], [150, 600], [1000, 5000], [1000, 2000], [600, 3000]] as [$n, $m]) {
                $e = T::edges($n, $m, true, false, [1, 10000]);
                $s = mt_rand(1, $n);
                $tests[] = T::graphInput("$n ".count($e)." $s", $e);
            }

            return $tests;
        },
        'solve' => function (string $input) use ($readGraph, $dijkstra) {
            [$head, $n, , $edges] = $readGraph($input);
            $adj = array_fill(0, $n + 1, []);
            foreach ($edges as [$u, $v, $w]) {
                $adj[$u][] = [$v, $w];
            }
            $dist = $dijkstra($n, $adj, $head[2]);
            $out = [];
            for ($i = 1; $i <= $n; $i++) {
                $out[] = $dist[$i] === PHP_INT_MAX ? -1 : $dist[$i];
            }

            return implode(' ', $out);
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n, m, s] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]); // satu arah!
}

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m, s = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))  # satu arah!

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m, s] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
}

// Min-heap sederhana berisi pasangan [jarak, simpul]
class MinHeap {
  constructor() { this.a = []; }
  get size() { return this.a.length; }
  push(x) {
    const a = this.a;
    a.push(x);
    let i = a.length - 1;
    while (i > 0) {
      const p = (i - 1) >> 1;
      if (a[p][0] <= a[i][0]) break;
      [a[p], a[i]] = [a[i], a[p]];
      i = p;
    }
  }
  pop() {
    const a = this.a;
    const top = a[0];
    const last = a.pop();
    if (a.length) {
      a[0] = last;
      let i = 0;
      while (true) {
        const l = 2 * i + 1, r = l + 1;
        let k = i;
        if (l < a.length && a[l][0] < a[k][0]) k = l;
        if (r < a.length && a[r][0] < a[k][0]) k = r;
        if (k === i) break;
        [a[k], a[i]] = [a[i], a[k]];
        i = k;
      }
    }
    return top;
  }
}

const dist = new Array(n + 1).fill(Infinity);
dist[s] = 0;
const pq = new MinHeap();
pq.push([0, s]);

while (pq.size) {
  const [d, u] = pq.pop();
  if (d > dist[u]) continue;
  for (const [v, w] of adj[u]) {
    if (d + w < dist[v]) {
      dist[v] = d + w;
      pq.push([dist[v], v]);
    }
  }
}

console.log(dist.slice(1).map((d) => (d === Infinity ? -1 : d)).join(" "));
CODE,
            'python' => <<<'CODE'
import sys
import heapq
input = sys.stdin.readline

n, m, s = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))

INF = float("inf")
dist = [INF] * (n + 1)
dist[s] = 0
pq = [(0, s)]

while pq:
    d, u = heapq.heappop(pq)
    if d > dist[u]:
        continue
    for v, w in adj[u]:
        if d + w < dist[v]:
            dist[v] = d + w
            heapq.heappush(pq, (dist[v], v))

print(*[-1 if d == INF else d for d in dist[1:]])
CODE,
        ],
        'editorial' => '<p>Ini Dijkstra <strong>single-source</strong> pada graph <strong>berarah</strong>. Perbedaannya dengan soal sebelumnya:</p>
<ul><li>Sisi hanya dimasukkan satu arah: <code>adj[u].push([v, w])</code>.</li>
<li>Sumbernya <code>S</code>, bukan selalu 1.</li>
<li>Kita mencetak seluruh array <code>dist</code>.</li></ul>
<p>Solusi JavaScript menyertakan <strong>min-heap</strong> buatan sendiri karena JavaScript tidak punya priority queue bawaan. Simpan potongan kode ini, karena sering dipakai di lomba!</p>
<p><strong>Kompleksitas:</strong> O((N + M) log M).</p>',
    ],

    // ═════════════════════════════ Topological Sort ═════════════════════════════
    [
        'slug' => 'urutan-mapel',
        'lesson' => 'topological-sort',
        'title' => 'Urutan Mata Pelajaran',
        'difficulty' => 'Sedang',
        'tags' => ['graph', 'topological sort', 'dag'],
        'statement' => '<p>Kurikulum jurusan RPL memiliki <strong>N</strong> materi bernomor 1..N. Beberapa materi punya <strong>prasyarat</strong>: pasangan <code>a b</code> berarti materi <code>a</code> harus dipelajari <strong>sebelum</strong> materi <code>b</code>.</p>
<p>Susun urutan belajar yang memenuhi semua prasyarat. Jika ada beberapa urutan yang valid, pilih yang <strong>terkecil secara leksikografis</strong> (setiap saat, pelajari materi bernomor terkecil yang sudah boleh dipelajari). Jika mustahil karena prasyaratnya melingkar, cetak <code>MUSTAHIL</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>, diikuti <code>M</code> baris berisi <code>a b</code>.</p>',
        'output_format' => '<p>Urutan materi (N bilangan dipisah spasi), atau <code>MUSTAHIL</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 500</li><li>0 ≤ M ≤ 2000</li></ul>',
        'samples' => [
            ['input' => "5 4\n1 3\n2 3\n3 4\n2 5\n", 'explanation' => 'Awalnya materi 1 dan 2 bebas, pilih 1. Lalu hanya 2 yang bebas. Setelah 2, materi 3 dan 5 bebas, pilih 3. Lalu 4 dan 5 bebas, pilih 4, lalu 5.'],
            ['input' => "3 3\n1 2\n2 3\n3 1\n", 'explanation' => '1 sebelum 2, 2 sebelum 3, tetapi 3 sebelum 1. Prasyaratnya melingkar.'],
        ],
        'tests' => function () {
            $tests = [T::graphInput('1 0', []), T::graphInput('4 0', []), T::graphInput('2 2', [[1, 2], [2, 1]])];
            foreach ([[10, 12], [100, 300], [500, 2000], [500, 800]] as [$n, $m]) {
                $e = T::dag($n, $m);
                $tests[] = T::graphInput("$n ".count($e), $e);
            }
            $e = T::withCycle(300, 900);
            $tests[] = T::graphInput('300 '.count($e), $e);

            return $tests;
        },
        'solve' => function (string $input) use ($readGraph) {
            [, $n, , $edges] = $readGraph($input);
            $adj = array_fill(1, $n, []);
            $in = array_fill(1, $n, 0);
            foreach ($edges as [$a, $b]) {
                $adj[$a][] = $b;
                $in[$b]++;
            }
            $heap = new SplMinHeap;
            for ($i = 1; $i <= $n; $i++) {
                if ($in[$i] === 0) {
                    $heap->insert($i);
                }
            }
            $order = [];
            while (! $heap->isEmpty()) {
                $u = $heap->extract();
                $order[] = $u;
                foreach ($adj[$u] as $v) {
                    if (--$in[$v] === 0) {
                        $heap->insert($v);
                    }
                }
            }

            return count($order) < $n ? 'MUSTAHIL' : implode(' ', $order);
        },
        'starter' => ['javascript' => $readEdgesStarterJs, 'python' => $readEdgesStarterPy],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const indeg = new Array(n + 1).fill(0);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  indeg[b]++;
}

// Kahn: setiap langkah ambil simpul ber-indegree 0 dengan nomor terkecil.
// N ≤ 500 sehingga pencarian linear O(N^2) sudah cukup cepat.
const dipakai = new Array(n + 1).fill(false);
const urutan = [];

for (let step = 0; step < n; step++) {
  let u = -1;
  for (let i = 1; i <= n; i++) {
    if (!dipakai[i] && indeg[i] === 0) { u = i; break; }
  }
  if (u === -1) break; // tidak ada yang bebas → ada siklus
  dipakai[u] = true;
  urutan.push(u);
  for (const v of adj[u]) indeg[v]--;
}

console.log(urutan.length < n ? "MUSTAHIL" : urutan.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
import heapq
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
indeg = [0] * (n + 1)
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    indeg[b] += 1

# Kahn dengan min-heap agar selalu mengambil nomor terkecil
heap = [i for i in range(1, n + 1) if indeg[i] == 0]
heapq.heapify(heap)
urutan = []

while heap:
    u = heapq.heappop(heap)
    urutan.append(u)
    for v in adj[u]:
        indeg[v] -= 1
        if indeg[v] == 0:
            heapq.heappush(heap, v)

print("MUSTAHIL" if len(urutan) < n else " ".join(map(str, urutan)))
CODE,
        ],
        'editorial' => '<p>Prasyarat membentuk graph berarah. Urutan yang valid adalah <strong>topological order</strong>, dan hanya ada jika graph-nya <strong>tidak bersiklus</strong> (DAG).</p>
<p><strong>Algoritma Kahn:</strong></p>
<ol><li>Hitung <code>indeg</code> (banyak prasyarat) setiap materi.</li>
<li>Materi dengan indeg 0 boleh langsung dipelajari.</li>
<li>Ambil salah satunya. Agar hasilnya leksikografis terkecil, pilih nomor terkecil (pakai min-heap). Lalu kurangi indeg semua materi yang bergantung padanya.</li>
<li>Ulangi. Jika berhenti sebelum N materi terambil, berarti ada siklus → <code>MUSTAHIL</code>.</li></ol>
<p><strong>Kompleksitas:</strong> O((N + M) log N) dengan heap.</p>',
    ],

    [
        'slug' => 'waktu-proyek',
        'lesson' => 'topological-sort',
        'title' => 'Jadwal Proyek Akhir',
        'difficulty' => 'Sulit',
        'tags' => ['graph', 'dag', 'dp on dag'],
        'statement' => '<p>Tim kamu mengerjakan proyek akhir yang terdiri dari <strong>N</strong> tugas. Tugas ke-i butuh waktu <code>t<sub>i</sub></code> hari. Ada <strong>M</strong> ketergantungan <code>a b</code>: tugas <code>b</code> baru boleh dimulai setelah tugas <code>a</code> <strong>selesai</strong>.</p>
<p>Anggota tim banyak, jadi tugas yang tidak saling bergantung bisa dikerjakan <strong>bersamaan</strong>. Berapa hari <strong>minimum</strong> sampai seluruh proyek selesai?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. Baris kedua berisi <code>t1 t2 ... tN</code>. <code>M</code> baris berikutnya berisi <code>a b</code>. Dijamin tidak ada ketergantungan melingkar.</p>',
        'output_format' => '<p>Jumlah hari minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 1000</li><li>0 ≤ M ≤ 5000</li><li>1 ≤ t<sub>i</sub> ≤ 1000</li></ul>',
        'samples' => [
            ['input' => "5 5\n3 2 4 1 5\n1 3\n2 3\n3 4\n1 5\n4 5\n", 'explanation' => 'Rantai terpanjang adalah 1 → 3 → 4 → 5 dengan total 3 + 4 + 1 + 5 = 13 hari. Tugas 2 dikerjakan bersamaan dengan tugas 1.'],
        ],
        'tests' => function () {
            $mk = function (int $n, array $edges) {
                $t = [];
                for ($i = 0; $i < $n; $i++) {
                    $t[] = mt_rand(1, 1000);
                }

                return "$n ".count($edges)."\n".implode(' ', $t)."\n".implode("\n", array_map(fn ($e) => implode(' ', $e), $edges)).(count($edges) ? "\n" : '');
            };
            $chain = [];
            for ($i = 1; $i < 1000; $i++) {
                $chain[] = [$i, $i + 1];
            }

            return [$mk(1, []), $mk(5, []), $mk(10, T::dag(10, 15)), $mk(200, T::dag(200, 800)), $mk(1000, T::dag(1000, 5000)), $mk(1000, $chain), $mk(1000, T::dag(1000, 1500))];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $t = array_merge([0], T::ints($lines[1]));
            $adj = array_fill(1, $n, []);
            $in = array_fill(1, $n, 0);
            for ($i = 0; $i < $m; $i++) {
                [$a, $b] = T::ints($lines[2 + $i]);
                $adj[$a][] = $b;
                $in[$b]++;
            }
            $finish = array_fill(1, $n, 0);
            $start = array_fill(1, $n, 0);
            $q = [];
            for ($i = 1; $i <= $n; $i++) {
                if ($in[$i] === 0) {
                    $q[] = $i;
                }
            }
            for ($h = 0; $h < count($q); $h++) {
                $u = $q[$h];
                $finish[$u] = $start[$u] + $t[$u];
                foreach ($adj[$u] as $v) {
                    $start[$v] = max($start[$v], $finish[$u]);
                    if (--$in[$v] === 0) {
                        $q[] = $v;
                    }
                }
            }

            return (string) max($finish);
        },
        'starter' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const t = [0, ...readInts()]; // t[1..n]
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
}

// Tulis solusimu di sini


console.log();
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
t = [0] + list(map(int, input().split()))  # t[1..n]
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)

# Tulis solusimu di sini


print()
CODE,
        ],
        'solutions' => [
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const t = [0, ...readInts()];
const adj = Array.from({ length: n + 1 }, () => []);
const indeg = new Array(n + 1).fill(0);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  indeg[b]++;
}

// mulai[v] = waktu paling awal tugas v boleh dimulai
const mulai = new Array(n + 1).fill(0);
const selesai = new Array(n + 1).fill(0);
const queue = [];
for (let i = 1; i <= n; i++) if (indeg[i] === 0) queue.push(i);

for (let head = 0; head < queue.length; head++) {
  const u = queue[head];
  selesai[u] = mulai[u] + t[u];
  for (const v of adj[u]) {
    mulai[v] = Math.max(mulai[v], selesai[u]); // tunggu prasyarat paling lama
    if (--indeg[v] === 0) queue.push(v);
  }
}

console.log(Math.max(...selesai));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, m = map(int, input().split())
t = [0] + list(map(int, input().split()))
adj = [[] for _ in range(n + 1)]
indeg = [0] * (n + 1)
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    indeg[b] += 1

mulai = [0] * (n + 1)
selesai = [0] * (n + 1)
queue = deque(i for i in range(1, n + 1) if indeg[i] == 0)

while queue:
    u = queue.popleft()
    selesai[u] = mulai[u] + t[u]
    for v in adj[u]:
        mulai[v] = max(mulai[v], selesai[u])
        indeg[v] -= 1
        if indeg[v] == 0:
            queue.append(v)

print(max(selesai))
CODE,
        ],
        'editorial' => '<p>Soal ini menggabungkan <strong>topological sort</strong> dan <strong>DP</strong>. Jembatan yang pas sebelum masuk track DP!</p>
<p>Definisikan <code>mulai[v]</code> = waktu paling awal tugas v boleh dimulai. Karena v harus menunggu <em>semua</em> prasyaratnya selesai:</p>
<p style="text-align:center"><code>mulai[v] = max(selesai[u])</code> untuk semua u → v, dan <code>selesai[v] = mulai[v] + t[v]</code>.</p>
<p>Nilai <code>selesai[u]</code> harus sudah final sebelum dipakai, dan urutan topologis menjamin hal itu. Jawaban akhirnya adalah <code>max(selesai)</code>. Konsep ini dikenal sebagai <strong>critical path</strong> (jalur kritis) dalam manajemen proyek.</p>
<p><strong>Kompleksitas:</strong> O(N + M).</p>',
    ],
];
