<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Aliran Maksimum & Pencocokan Bipartit.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/**
 * Aliran maksimum (Edmonds-Karp) untuk solusi referensi PHP.
 * $arcs: [[u, v, kapasitas, kapasitasBalik], ...]
 */
$maxflow = function (int $n, array $arcs, int $s, int $t): int {
    $to = [];
    $res = [];
    $adj = array_fill(0, $n + 1, []);
    foreach ($arcs as $a) {
        [$u, $v, $c] = $a;
        $adj[$u][] = count($to);
        $to[] = $v;
        $res[] = $c;
        $adj[$v][] = count($to);
        $to[] = $u;
        $res[] = $a[3] ?? 0;
    }
    $total = 0;
    while (true) {
        $from = array_fill(0, $n + 1, -1);
        $seen = array_fill(0, $n + 1, false);
        $seen[$s] = true;
        $q = [$s];
        $h = 0;
        while ($h < count($q) && ! $seen[$t]) {
            $u = $q[$h++];
            foreach ($adj[$u] as $e) {
                $v = $to[$e];
                if (! $seen[$v] && $res[$e] > 0) {
                    $seen[$v] = true;
                    $from[$v] = $e;
                    $q[] = $v;
                }
            }
        }
        if (! $seen[$t]) {
            return $total;
        }
        $add = PHP_INT_MAX;
        for ($v = $t; $v !== $s; $v = $to[$from[$v] ^ 1]) {
            $add = min($add, $res[$from[$v]]);
        }
        for ($v = $t; $v !== $s; $v = $to[$from[$v] ^ 1]) {
            $res[$from[$v]] -= $add;
            $res[$from[$v] ^ 1] += $add;
        }
        $total += $add;
    }
};

/** Kode C++ Edmonds-Karp yang dipakai bersama beberapa solusi. */
$ekCpp = <<<'CODE'
struct Sisi {
    int ke;
    long long sisa;
};
vector<Sisi> sisi;           // sisi[e] dan sisi[e ^ 1] berpasangan
vector<vector<int>> adj;

void tambahSisi(int u, int v, long long kap, long long kapBalik = 0) {
    adj[u].push_back(sisi.size());
    sisi.push_back({v, kap});
    adj[v].push_back(sisi.size());
    sisi.push_back({u, kapBalik});
}

long long aliranMaks(int n, int s, int t) {
    long long total = 0;
    while (true) {
        vector<int> dari(n + 1, -1);
        vector<bool> lihat(n + 1, false);
        queue<int> q;
        q.push(s);
        lihat[s] = true;
        while (!q.empty() && !lihat[t]) {
            int u = q.front();
            q.pop();
            for (int e : adj[u])
                if (!lihat[sisi[e].ke] && sisi[e].sisa > 0) {
                    lihat[sisi[e].ke] = true;
                    dari[sisi[e].ke] = e;
                    q.push(sisi[e].ke);
                }
        }
        if (!lihat[t]) return total;
        long long tambah = LLONG_MAX;
        for (int v = t; v != s; v = sisi[dari[v] ^ 1].ke) tambah = min(tambah, sisi[dari[v]].sisa);
        for (int v = t; v != s; v = sisi[dari[v] ^ 1].ke) {
            sisi[dari[v]].sisa -= tambah;
            sisi[dari[v] ^ 1].sisa += tambah;
        }
        total += tambah;
    }
}
CODE;

$ekJs = <<<'CODE'
const ke = [], sisa = [];
let adj = [];
function tambahSisi(u, v, kap, kapBalik = 0) {
  adj[u].push(ke.length); ke.push(v); sisa.push(kap);
  adj[v].push(ke.length); ke.push(u); sisa.push(kapBalik);
}
function aliranMaks(n, s, t) {
  let total = 0;
  for (;;) {
    const dari = new Int32Array(n + 1).fill(-1);
    const lihat = new Uint8Array(n + 1);
    const q = [s];
    lihat[s] = 1;
    for (let h = 0; h < q.length && !lihat[t]; h++) {
      const u = q[h];
      for (const e of adj[u]) {
        const v = ke[e];
        if (!lihat[v] && sisa[e] > 0) { lihat[v] = 1; dari[v] = e; q.push(v); }
      }
    }
    if (!lihat[t]) return total;
    let tambah = Infinity;
    for (let v = t; v !== s; v = ke[dari[v] ^ 1]) tambah = Math.min(tambah, sisa[dari[v]]);
    for (let v = t; v !== s; v = ke[dari[v] ^ 1]) { sisa[dari[v]] -= tambah; sisa[dari[v] ^ 1] += tambah; }
    total += tambah;
  }
}
CODE;

$ekPy = <<<'CODE'
from collections import deque

ke, sisa = [], []
adj = []

def tambah_sisi(u, v, kap, kap_balik=0):
    adj[u].append(len(ke)); ke.append(v); sisa.append(kap)
    adj[v].append(len(ke)); ke.append(u); sisa.append(kap_balik)

def aliran_maks(n, s, t):
    total = 0
    while True:
        dari = [-1] * (n + 1)
        lihat = [False] * (n + 1)
        lihat[s] = True
        q = deque([s])
        while q and not lihat[t]:
            u = q.popleft()
            for e in adj[u]:
                v = ke[e]
                if not lihat[v] and sisa[e] > 0:
                    lihat[v] = True
                    dari[v] = e
                    q.append(v)
        if not lihat[t]:
            return total
        tambah = float("inf")
        v = t
        while v != s:
            tambah = min(tambah, sisa[dari[v]])
            v = ke[dari[v] ^ 1]
        v = t
        while v != s:
            sisa[dari[v]] -= tambah
            sisa[dari[v] ^ 1] += tambah
            v = ke[dari[v] ^ 1]
        total += tambah
CODE;

return [
    [
        'slug' => 'saluran-air',
        'lesson' => 'max-flow',
        'title' => 'Saluran Air Kota',
        'difficulty' => 'Sedang',
        'tags' => ['aliran maksimum', 'edmonds-karp'],
        'statement' => '<p>PDAM mengalirkan air dari waduk di titik <strong>1</strong> ke tandon kota di titik <strong>N</strong>. Ada <strong>M</strong> pipa satu arah; pipa ke-i mengalirkan air dari titik <code>u<sub>i</sub></code> ke titik <code>v<sub>i</sub></code> paling banyak <code>c<sub>i</sub></code> liter per detik. Di setiap titik selain waduk dan tandon, air yang masuk harus sama dengan air yang keluar.</p>
<p>Berapa liter per detik paling banyak yang bisa sampai di tandon kota?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v c</code>.</p>',
        'output_format' => '<p>Aliran maksimum dari titik 1 ke titik N.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100</li><li>1 ≤ M ≤ 1 000</li><li>1 ≤ c ≤ 10<sup>9</sup>, u ≠ v</li></ul>',
        'samples' => [
            ['input' => "6 7\n1 2 3\n2 3 2\n3 6 2\n1 4 2\n4 3 2\n2 5 2\n5 6 3\n", 'explanation' => '2 liter lewat 1 → 2 → 5 → 6, 1 liter lewat 1 → 2 → 3 → 6, dan 1 liter lewat 1 → 4 → 3 → 6. Pipa 3 → 6 dan 2 → 5 sama-sama penuh, totalnya 2 + 2 = 4, jadi tidak mungkin lebih.'],
            ['input' => "3 1\n2 3 5\n", 'explanation' => 'Tidak ada pipa yang keluar dari waduk.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $cmax, bool $layered = false) {
                $e = [];
                for ($i = 0; $i < $m; $i++) {
                    do {
                        $u = mt_rand(1, $n);
                        $v = mt_rand(1, $n);
                    } while ($u === $v || ($layered && $u >= $v));
                    $e[] = [$u, $v, mt_rand(1, $cmax)];
                }

                return T::graphInput("$n $m", $e);
            };

            return [
                "2 1\n1 2 7\n", "2 2\n1 2 1000000000\n1 2 1000000000\n", "6 10\n1 2 16\n1 3 13\n2 3 10\n3 2 4\n2 4 12\n4 3 9\n3 5 14\n5 4 7\n4 6 20\n5 6 4\n",
                $mk(10, 20, 10), $mk(30, 200, 100), $mk(100, 1000, 1000000000), $mk(100, 1000, 5, true), $mk(100, 300, 1000000000, true), $mk(100, 1000, 1),
            ];
        },
        'solve' => function (string $input) use ($maxflow) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $arcs = [];
            for ($i = 1; $i <= $m; $i++) {
                $arcs[] = T::ints($lines[$i]);
            }

            return (string) $maxflow($n, $arcs, 1, $n);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        long long c;\n        cin >> u >> v >> c;\n    }", "const [n, m] = readInts();\nfor (let i = 0; i < m; i++) {\n  const [u, v, c] = readInts();\n}", "n, m = map(int, input().split())\nfor _ in range(m):\n    u, v, c = map(int, input().split())"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$ekCpp."\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n    int n, m;\n    cin >> n >> m;\n    adj.assign(n + 1, {});\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        long long c;\n        cin >> u >> v >> c;\n        tambahSisi(u, v, c);\n    }\n    cout << aliranMaks(n, 1, n) << '\\n';\n    return 0;\n}\n",
            'javascript' => "const [n, m] = readInts();\n".$ekJs."\nadj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [u, v, c] = readInts();\n  tambahSisi(u, v, c);\n}\nconsole.log(aliranMaks(n, 1, n));\n",
            'python' => "import sys\ninput = sys.stdin.readline\n".$ekPy."\n\nn, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    u, v, c = map(int, input().split())\n    tambah_sisi(u, v, c)\nprint(aliran_maks(n, 1, n))\n",
        ],
        'editorial' => '<p>Ini soal aliran maksimum murni: simpul = titik, sisi berarah = pipa, kapasitas = debit maksimum, sumber = 1, muara = N.</p>
<p>Gunakan Edmonds-Karp: simpan setiap pipa beserta sisi baliknya (sisa 0), cari jalur augmentasi dengan BFS di graph residual, kirim aliran sebesar bottleneck, ulangi sampai muara tidak terjangkau. Dengan N ≤ 100 dan M ≤ 1 000, O(V · E²) jauh di bawah batas waktu.</p>
<p><strong>Awas:</strong> total aliran bisa mencapai 10<sup>12</sup>, gunakan <code>long long</code>. Pipa ganda antara dua titik yang sama tidak masalah: masing-masing punya sisi baliknya sendiri.</p>',
        'hints' => [
            'Titik = simpul, pipa = sisi berarah berkapasitas. Apa nama soal klasik ini?',
            'Aliran maksimum. Cari jalur dari 1 ke N yang masih punya sisa kapasitas, kirim sebanyak bottleneck, ulangi.',
            'Jangan lupa sisi balik (sisa awal 0) agar aliran bisa dialihkan. Pakai long long.',
        ],
        'sample_visual' => 'wdigraph',
    ],

    [
        'slug' => 'lowongan-kerja',
        'lesson' => 'max-flow',
        'title' => 'Bursa Kerja',
        'difficulty' => 'Sedang',
        'tags' => ['pencocokan bipartit', 'kuhn'],
        'statement' => '<p>Di sebuah bursa kerja ada <strong>L</strong> pelamar (bernomor 1..L) dan <strong>R</strong> lowongan (bernomor 1..R). Setiap pelamar hanya memenuhi syarat untuk beberapa lowongan. Setiap pelamar paling banyak diterima di satu lowongan, dan setiap lowongan hanya menerima satu orang.</p>
<p>Berapa banyak pelamar paling banyak yang bisa diterima bekerja?</p>',
        'input_format' => '<p>Baris pertama berisi <code>L R K</code>. K baris berikutnya berisi <code>a b</code>: pelamar a memenuhi syarat lowongan b (tidak ada pasangan yang berulang).</p>',
        'output_format' => '<p>Banyak pelamar maksimum yang diterima.</p>',
        'constraints' => '<ul><li>1 ≤ L, R ≤ 300</li><li>0 ≤ K ≤ 10 000</li></ul>',
        'samples' => [
            ['input' => "3 3 5\n1 1\n1 2\n2 1\n3 2\n3 3\n", 'explanation' => 'Pelamar 1 → lowongan 2, pelamar 2 → lowongan 1, pelamar 3 → lowongan 3.'],
            ['input' => "3 2 3\n1 1\n2 1\n3 1\n", 'explanation' => 'Semua pelamar hanya cocok dengan lowongan 1.'],
        ],
        'tests' => function () {
            $mk = function (int $l, int $r, int $k) {
                $set = [];
                $k = min($k, $l * $r);
                while (count($set) < $k) {
                    $set[mt_rand(1, $l).' '.mt_rand(1, $r)] = true;
                }
                $pairs = array_keys($set);

                return "$l $r $k\n".implode("\n", $pairs).($k ? "\n" : '');
            };
            $chainy = function (int $n) {
                // pelamar i cocok dengan lowongan i dan i+1: penempatan serakah sering salah
                $p = [];
                for ($i = 1; $i <= $n; $i++) {
                    if ($i < $n) {
                        $p[] = "$i ".($i + 1);
                    }
                    $p[] = "$i $i";
                }

                return "$n $n ".count($p)."\n".implode("\n", $p)."\n";
            };

            return ["1 1 0\n", "1 1 1\n1 1\n", $mk(5, 5, 8), $mk(20, 15, 40), $mk(100, 100, 300), $mk(300, 300, 10000), $mk(300, 200, 600), $mk(300, 300, 900), $chainy(300), $mk(300, 10, 3000)];
        },
        'solve' => function (string $input) use ($maxflow) {
            $lines = T::lines($input);
            [$l, $r, $k] = T::ints($lines[0]);
            $arcs = [];
            for ($i = 1; $i <= $k; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $arcs[] = [$a, $l + $b, 1];
            }
            for ($a = 1; $a <= $l; $a++) {
                $arcs[] = [0, $a, 1];
            }
            for ($b = 1; $b <= $r; $b++) {
                $arcs[] = [$l + $b, $l + $r + 1, 1];
            }

            return (string) $maxflow($l + $r + 1, $arcs, 0, $l + $r + 1);
        },
        'starter' => $st("    int L, R, K;\n    cin >> L >> R >> K;\n    vector<vector<int>> cocok(L + 1);\n    for (int i = 0; i < K; i++) {\n        int a, b;\n        cin >> a >> b;\n        cocok[a].push_back(b);\n    }", "const [L, R, K] = readInts();\nconst cocok = Array.from({ length: L + 1 }, () => []);\nfor (let i = 0; i < K; i++) {\n  const [a, b] = readInts();\n  cocok[a].push(b);\n}", "L, R, K = map(int, input().split())\ncocok = [[] for _ in range(L + 1)]\nfor _ in range(K):\n    a, b = map(int, input().split())\n    cocok[a].append(b)"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int L, R, K;
vector<vector<int>> cocok;
vector<int> pemegang;   // pemegang[b] = pelamar yang menempati lowongan b (0 = kosong)
vector<int> tanda;
int putaran = 0;

// Carikan lowongan untuk pelamar a, boleh menggeser pelamar lain ke lowongan lain.
bool coba(int a) {
    for (int b : cocok[a]) {
        if (tanda[b] == putaran) continue;
        tanda[b] = putaran;
        if (pemegang[b] == 0 || coba(pemegang[b])) {
            pemegang[b] = a;
            return true;
        }
    }
    return false;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> L >> R >> K;
    cocok.assign(L + 1, {});
    for (int i = 0; i < K; i++) {
        int a, b;
        cin >> a >> b;
        cocok[a].push_back(b);
    }
    pemegang.assign(R + 1, 0);
    tanda.assign(R + 1, 0);
    int diterima = 0;
    for (int a = 1; a <= L; a++) {
        putaran++;                 // reset penanda tanpa mengisi ulang array
        if (coba(a)) diterima++;
    }
    cout << diterima << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [L, R, K] = readInts();
const cocok = Array.from({ length: L + 1 }, () => []);
for (let i = 0; i < K; i++) {
  const [a, b] = readInts();
  cocok[a].push(b);
}
const pemegang = new Int32Array(R + 1);
const tanda = new Int32Array(R + 1);
let putaran = 0;
function coba(a) {
  for (const b of cocok[a]) {
    if (tanda[b] === putaran) continue;
    tanda[b] = putaran;
    if (pemegang[b] === 0 || coba(pemegang[b])) {
      pemegang[b] = a;
      return true;
    }
  }
  return false;
}
let diterima = 0;
for (let a = 1; a <= L; a++) {
  putaran++;
  if (coba(a)) diterima++;
}
console.log(diterima);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline
sys.setrecursionlimit(10000)

L, R, K = map(int, input().split())
cocok = [[] for _ in range(L + 1)]
for _ in range(K):
    a, b = map(int, input().split())
    cocok[a].append(b)

pemegang = [0] * (R + 1)
tanda = [0] * (R + 1)
putaran = 0

def coba(a):
    for b in cocok[a]:
        if tanda[b] == putaran:
            continue
        tanda[b] = putaran
        if pemegang[b] == 0 or coba(pemegang[b]):
            pemegang[b] = a
            return True
    return False

diterima = 0
for a in range(1, L + 1):
    putaran += 1
    if coba(a):
        diterima += 1
print(diterima)
CODE,
        ],
        'editorial' => '<p>Pelamar dan lowongan membentuk <strong>graph bipartit</strong>; yang dicari adalah <strong>pencocokan maksimum</strong>.</p>
<p>Cara serakah (beri setiap pelamar lowongan pertama yang kosong) bisa salah: pada contoh pertama, jika pelamar 1 mengambil lowongan 1, pelamar 2 tidak kebagian kecuali pelamar 1 mau pindah. Algoritma <strong>Kuhn</strong> mengizinkan perpindahan itu: untuk setiap pelamar a, coba setiap lowongan b; jika b kosong ambil, jika b dipegang pelamar lain, coba carikan lowongan lain untuk pemegangnya secara rekursif. Rangkaian perpindahan ini adalah jalur augmentasi pada jaringan aliran (sumber → pelamar → lowongan → muara, semua kapasitas 1).</p>
<p>Array <code>tanda</code> mencegah lowongan yang sama dicoba dua kali dalam satu putaran. Kompleksitas O(L · K) ≈ 3 · 10<sup>6</sup>.</p>',
        'hints' => [
            'Pelamar di satu sisi, lowongan di sisi lain, sisi = memenuhi syarat. Graph apa ini?',
            'Serakah bisa gagal. Bolehkan pelamar yang sudah diterima pindah ke lowongan lain jika itu membuat orang baru ikut diterima.',
            'Algoritma Kuhn: coba(a) mencoba setiap lowongan b; ambil jika kosong atau jika coba(pemegang[b]) berhasil.',
        ],
    ],

    [
        'slug' => 'kuota-ekskul',
        'lesson' => 'max-flow',
        'title' => 'Kuota Ekstrakurikuler',
        'difficulty' => 'Sedang',
        'tags' => ['aliran maksimum', 'pemodelan', 'kuota'],
        'statement' => '<p>Sekolah membuka <strong>M</strong> kegiatan ekstrakurikuler. Ekskul ke-j hanya bisa menerima paling banyak <code>k<sub>j</sub></code> siswa. Ada <strong>N</strong> siswa, dan siswa ke-i menuliskan daftar ekskul yang ia minati. Setiap siswa ikut <strong>paling banyak satu</strong> ekskul, dan hanya ekskul yang ia minati.</p>
<p>Berapa banyak siswa paling banyak yang bisa mendapat ekskul?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. Baris kedua berisi <code>k<sub>1</sub> … k<sub>M</sub></code>. N baris berikutnya: bilangan <code>c</code> diikuti c nomor ekskul yang diminati siswa itu (berbeda semua).</p>',
        'output_format' => '<p>Banyak siswa maksimum yang mendapat ekskul.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 500, 1 ≤ M ≤ 100</li><li>0 ≤ k<sub>j</sub> ≤ N</li><li>Total panjang semua daftar ≤ 5 000</li></ul>',
        'samples' => [
            ['input' => "4 2\n1 2\n1 1\n2 1 2\n1 1\n1 2\n", 'explanation' => 'Ekskul 1 hanya menerima 1 orang dan diminati siswa 1, 2, 3. Pilih siswa 1 di ekskul 1, siswa 2 dan siswa 4 di ekskul 2. Siswa 3 tidak kebagian.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $kmax, int $cmax) {
                $k = [];
                for ($j = 0; $j < $m; $j++) {
                    $k[] = mt_rand(0, $kmax);
                }
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $c = mt_rand(0, min($cmax, $m));
                    $all = range(1, $m);
                    T::shuffle($all);
                    $rows[] = trim($c.' '.implode(' ', array_slice($all, 0, $c)));
                }

                return "$n $m\n".implode(' ', $k)."\n".implode("\n", $rows)."\n";
            };

            return ["1 1\n0\n1 1\n", "1 1\n1\n0\n", "3 1\n2\n1 1\n1 1\n1 1\n", $mk(10, 3, 3, 2), $mk(50, 10, 6, 3), $mk(500, 100, 3, 10), $mk(500, 100, 10, 10), $mk(500, 20, 30, 10), $mk(500, 100, 1, 10), $mk(500, 5, 500, 1)];
        },
        'solve' => function (string $input) use ($maxflow) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $k = T::ints($lines[1]);
            $S = 0;
            $T = $n + $m + 1;
            $arcs = [];
            for ($i = 1; $i <= $n; $i++) {
                $arcs[] = [$S, $i, 1];
                $row = T::ints($lines[1 + $i]);
                for ($x = 1; $x <= $row[0]; $x++) {
                    $arcs[] = [$i, $n + $row[$x], 1];
                }
            }
            for ($j = 1; $j <= $m; $j++) {
                if ($k[$j - 1] > 0) {
                    $arcs[] = [$n + $j, $T, $k[$j - 1]];
                }
            }

            return (string) $maxflow($T, $arcs, $S, $T);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<int> kuota(m + 1);\n    for (int j = 1; j <= m; j++) cin >> kuota[j];\n    vector<vector<int>> minat(n + 1);\n    for (int i = 1; i <= n; i++) {\n        int c;\n        cin >> c;\n        minat[i].resize(c);\n        for (int& x : minat[i]) cin >> x;\n    }", "const [n, m] = readInts();\nconst kuota = [0, ...readInts()];\nconst minat = [[]];\nfor (let i = 1; i <= n; i++) minat.push(readInts().slice(1));", "n, m = map(int, input().split())\nkuota = [0] + list(map(int, input().split()))\nminat = [[]] + [list(map(int, input().split()))[1:] for _ in range(n)]"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$ekCpp."\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n    int n, m;\n    cin >> n >> m;\n    // Simpul: 0 = sumber, 1..n = siswa, n+1..n+m = ekskul, n+m+1 = muara\n    int S = 0, T = n + m + 1;\n    adj.assign(T + 1, {});\n    for (int j = 1; j <= m; j++) {\n        int k;\n        cin >> k;\n        tambahSisi(n + j, T, k);           // ekskul j menerima paling banyak k siswa\n    }\n    for (int i = 1; i <= n; i++) {\n        tambahSisi(S, i, 1);               // setiap siswa paling banyak satu ekskul\n        int c;\n        cin >> c;\n        while (c--) {\n            int j;\n            cin >> j;\n            tambahSisi(i, n + j, 1);\n        }\n    }\n    cout << aliranMaks(T, S, T) << '\\n';\n    return 0;\n}\n",
            'javascript' => "const [n, m] = readInts();\n".$ekJs."\nconst S = 0, T = n + m + 1;\nadj = Array.from({ length: T + 1 }, () => []);\nconst kuota = readInts();\nfor (let j = 1; j <= m; j++) tambahSisi(n + j, T, kuota[j - 1]);\nfor (let i = 1; i <= n; i++) {\n  tambahSisi(S, i, 1);\n  const row = readInts();\n  for (let x = 1; x <= row[0]; x++) tambahSisi(i, n + row[x], 1);\n}\nconsole.log(aliranMaks(T, S, T));\n",
            'python' => "import sys\ninput = sys.stdin.readline\n".$ekPy."\n\nn, m = map(int, input().split())\nS, T = 0, n + m + 1\nadj = [[] for _ in range(T + 1)]\nkuota = list(map(int, input().split()))\nfor j in range(1, m + 1):\n    tambah_sisi(n + j, T, kuota[j - 1])\nfor i in range(1, n + 1):\n    tambah_sisi(S, i, 1)\n    row = list(map(int, input().split()))\n    for j in row[1:]:\n        tambah_sisi(i, n + j, 1)\nprint(aliran_maks(T, S, T))\n",
        ],
        'editorial' => '<p>Mirip pencocokan bipartit, tetapi satu ekskul bisa menampung beberapa siswa. Aliran maksimum menangani kuota dengan mudah. Bangun jaringan:</p>
<ul><li>sumber → siswa i, kapasitas <strong>1</strong> (siswa ikut paling banyak satu ekskul);</li><li>siswa i → ekskul j yang diminati, kapasitas 1;</li><li>ekskul j → muara, kapasitas <strong>k<sub>j</sub></strong> (kuota).</li></ul>
<p>Setiap satuan aliran adalah satu siswa yang masuk satu ekskul, dan kapasitas memastikan semua aturan dipatuhi. Aliran maksimum = jawaban. Nilai aliran ≤ N = 500, dan setiap BFS O(N + M + total daftar), jadi Edmonds-Karp sangat cepat.</p>
<p>Alternatif: gandakan ekskul j menjadi k<sub>j</sub> "kursi" lalu jalankan Kuhn, tetapi graph-nya menjadi lebih besar.</p>',
        'hints' => [
            'Tanpa kuota, ini pencocokan bipartit. Bagaimana memodelkan ekskul yang menerima beberapa siswa?',
            'Buat jaringan aliran: sumber → siswa → ekskul → muara. Apa kapasitas tiap sisi?',
            'Sumber → siswa: 1. Siswa → ekskul: 1. Ekskul j → muara: k_j. Hitung aliran maksimum.',
        ],
    ],

    [
        'slug' => 'putus-jalan',
        'lesson' => 'max-flow',
        'title' => 'Memutus Jalur Penyelundup',
        'difficulty' => 'Sulit',
        'tags' => ['potongan minimum', 'jalur tak berbagi sisi', 'menger'],
        'statement' => '<p>Polisi mengetahui bahwa penyelundup akan bergerak dari kota <strong>1</strong> ke kota <strong>N</strong> lewat jaringan <strong>M</strong> jalan dua arah. Polisi bisa menutup jalan; jalan yang ditutup tidak bisa dilewati ke arah mana pun.</p>
<p>Berapa banyak jalan <strong>paling sedikit</strong> yang harus ditutup agar tidak ada lagi rute dari kota 1 ke kota N?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u v</code>: jalan dua arah antara u dan v (u ≠ v, boleh ada beberapa jalan untuk pasangan kota yang sama).</p>',
        'output_format' => '<p>Banyak jalan minimum yang harus ditutup.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 500</li><li>0 ≤ M ≤ 5 000</li></ul>',
        'samples' => [
            ['input' => "6 8\n1 2\n1 3\n2 3\n2 4\n3 5\n4 5\n4 6\n5 6\n", 'explanation' => 'Ada dua rute yang tidak berbagi jalan, misalnya 1-2-4-6 dan 1-3-5-6, jadi minimal 2 jalan. Menutup 1-2 dan 1-3 sudah cukup.'],
            ['input' => "4 2\n1 2\n3 4\n", 'explanation' => 'Kota 1 dan 4 memang tidak terhubung.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m) {
                $e = [];
                for ($i = 0; $i < $m; $i++) {
                    $u = mt_rand(1, $n);
                    do {
                        $v = mt_rand(1, $n);
                    } while ($v === $u);
                    $e[] = [$u, $v];
                }

                return T::graphInput("$n $m", $e);
            };
            $bundles = function (int $n, int $width) {
                // beberapa "lapisan" kota; jalan hanya antar lapisan bertetangga
                $layers = [[1]];
                $id = 2;
                while ($id < $n) {
                    $layer = [];
                    for ($j = 0; $j < $width && $id < $n; $j++) {
                        $layer[] = $id++;
                    }
                    $layers[] = $layer;
                }
                $layers[] = [$n];
                $e = [];
                for ($i = 0; $i + 1 < count($layers); $i++) {
                    foreach ($layers[$i] as $a) {
                        foreach ($layers[$i + 1] as $b) {
                            if (mt_rand(0, 2) > 0) {
                                $e[] = [$a, $b];
                            }
                        }
                    }
                }
                T::shuffle($e);

                return T::graphInput("$n ".count($e), $e);
            };

            return ["2 0\n", "2 3\n1 2\n1 2\n2 1\n", $mk(8, 14), $mk(50, 200), $mk(500, 5000), $mk(500, 1500), $bundles(500, 8), $bundles(300, 15), $mk(500, 700)];
        },
        'solve' => function (string $input) use ($maxflow) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $arcs = [];
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $arcs[] = [$u, $v, 1, 1];
            }

            return (string) $maxflow($n, $arcs, 1, $n);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n    }", "const [n, m] = readInts();\nfor (let i = 0; i < m; i++) {\n  const [u, v] = readInts();\n}", "n, m = map(int, input().split())\nfor _ in range(m):\n    u, v = map(int, input().split())"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$ekCpp."\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n    int n, m;\n    cin >> n >> m;\n    adj.assign(n + 1, {});\n    for (int i = 0; i < m; i++) {\n        int u, v;\n        cin >> u >> v;\n        // Jalan dua arah berkapasitas 1: sisi maju DAN sisi baliknya sama-sama berkapasitas 1.\n        tambahSisi(u, v, 1, 1);\n    }\n    // Teorema max-flow min-cut: jalan minimum yang ditutup = aliran maksimum.\n    cout << aliranMaks(n, 1, n) << '\\n';\n    return 0;\n}\n",
            'javascript' => "const [n, m] = readInts();\n".$ekJs."\nadj = Array.from({ length: n + 1 }, () => []);\nfor (let i = 0; i < m; i++) {\n  const [u, v] = readInts();\n  tambahSisi(u, v, 1, 1);\n}\nconsole.log(aliranMaks(n, 1, n));\n",
            'python' => "import sys\ninput = sys.stdin.readline\n".$ekPy."\n\nn, m = map(int, input().split())\nadj = [[] for _ in range(n + 1)]\nfor _ in range(m):\n    u, v = map(int, input().split())\n    tambah_sisi(u, v, 1, 1)\nprint(aliran_maks(n, 1, n))\n",
        ],
        'editorial' => '<p>Menutup jalan agar 1 dan N terputus adalah mencari <strong>potongan minimum</strong> dengan setiap jalan berbobot 1. Menurut teorema max-flow min-cut, jawabannya sama dengan <strong>aliran maksimum</strong> dari 1 ke N bila setiap jalan berkapasitas 1. Artinya juga: banyak rute dari 1 ke N yang tidak berbagi jalan (teorema Menger).</p>
<p>Jalan dua arah dimodelkan dengan satu pasangan sisi yang <em>kedua</em> arahnya berkapasitas 1 (bukan 1 dan 0). Air bisa lewat ke salah satu arah, dan sisi balik tetap berfungsi sebagai "undo".</p>
<p>Aliran paling banyak sebesar derajat kota 1, dan setiap BFS O(N + M), jadi totalnya O(M · (N + M)) dalam kasus terburuk, jauh lebih cepat dalam praktik. Mencoba semua himpunan jalan yang ditutup tentu mustahil (2<sup>M</sup>).</p>',
        'hints' => [
            'Bandingkan dengan menghitung banyak rute dari 1 ke N yang tidak memakai jalan yang sama. Apa hubungannya?',
            'Potongan minimum = aliran maksimum. Beri setiap jalan kapasitas 1.',
            'Jalan dua arah: tambahkan sisi u → v berkapasitas 1 dengan sisi balik yang juga berkapasitas 1.',
        ],
        'sample_visual' => 'graph',
    ],
];
