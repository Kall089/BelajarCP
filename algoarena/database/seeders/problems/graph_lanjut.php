<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal Graph lanjutan: 2-SAT dan aliran biaya minimum (MCMF / Hungarian).
 * Kode C++ wajib C++14. DFS pada graph besar ditulis iteratif (stack judge bisa kecil).
 */

/**
 * Penyelesai 2-SAT referensi (Kosaraju iteratif).
 * $cl: daftar [a, b] simpul literal (2i = x_i benar, 2i+1 = x_i salah). Mengembalikan penugasan atau null.
 */
$sat2 = function (int $n, array $cl): ?array {
    $N = 2 * $n;
    $head = array_fill(0, $N, -1);
    $rhead = array_fill(0, $N, -1);
    $to = $nxt = $rto = $rnxt = [];
    $k = 0;
    foreach ($cl as $c) {
        foreach ([[$c[0] ^ 1, $c[1]], [$c[1] ^ 1, $c[0]]] as $e) {
            [$u, $v] = $e;
            $to[$k] = $v;
            $nxt[$k] = $head[$u];
            $head[$u] = $k;
            $rto[$k] = $u;
            $rnxt[$k] = $rhead[$v];
            $rhead[$v] = $k;
            $k++;
        }
    }
    $vis = array_fill(0, $N, false);
    $it = $head;
    $order = [];
    for ($s = 0; $s < $N; $s++) {
        if ($vis[$s]) {
            continue;
        }
        $vis[$s] = true;
        $st = [$s];
        while ($st) {
            $u = $st[count($st) - 1];
            $e = $it[$u];
            if ($e !== -1) {
                $it[$u] = $nxt[$e];
                $v = $to[$e];
                if (! $vis[$v]) {
                    $vis[$v] = true;
                    $st[] = $v;
                }
            } else {
                array_pop($st);
                $order[] = $u;
            }
        }
    }
    $comp = array_fill(0, $N, -1);
    $c = 0;
    for ($i = $N - 1; $i >= 0; $i--) {
        $s = $order[$i];
        if ($comp[$s] !== -1) {
            continue;
        }
        $comp[$s] = $c;
        $st = [$s];
        while ($st) {
            $u = array_pop($st);
            for ($e = $rhead[$u]; $e !== -1; $e = $rnxt[$e]) {
                $v = $rto[$e];
                if ($comp[$v] === -1) {
                    $comp[$v] = $c;
                    $st[] = $v;
                }
            }
        }
        $c++;
    }
    $x = [];
    for ($i = 0; $i < $n; $i++) {
        if ($comp[2 * $i] === $comp[2 * $i + 1]) {
            return null;
        }
        $x[] = $comp[2 * $i] > $comp[2 * $i + 1] ? 1 : 0;
    }

    return $x;
};

/** MCMF referensi (SPFA). $edges: [u, v, cap, cost]. Mengembalikan [aliran, biaya]; berhenti di $limit aliran. */
$mcmf = function (int $n, array $edges, int $s, int $t, int $limit = PHP_INT_MAX): array {
    $to = $cap = $cost = [];
    $adj = array_fill(0, $n, []);
    foreach ($edges as $e) {
        [$u, $v, $c, $w] = $e;
        $adj[$u][] = count($to);
        $to[] = $v;
        $cap[] = $c;
        $cost[] = $w;
        $adj[$v][] = count($to);
        $to[] = $u;
        $cap[] = 0;
        $cost[] = -$w;
    }
    $flow = 0;
    $total = 0;
    $INF = PHP_INT_MAX >> 2;
    while ($flow < $limit) {
        $dist = array_fill(0, $n, $INF);
        $inq = array_fill(0, $n, false);
        $pe = array_fill(0, $n, -1);
        $dist[$s] = 0;
        $q = new SplQueue;
        $q->enqueue($s);
        $inq[$s] = true;
        while (! $q->isEmpty()) {
            $u = $q->dequeue();
            $inq[$u] = false;
            $du = $dist[$u];
            foreach ($adj[$u] as $id) {
                if ($cap[$id] > 0) {
                    $v = $to[$id];
                    $nd = $du + $cost[$id];
                    if ($nd < $dist[$v]) {
                        $dist[$v] = $nd;
                        $pe[$v] = $id;
                        if (! $inq[$v]) {
                            $inq[$v] = true;
                            $q->enqueue($v);
                        }
                    }
                }
            }
        }
        if ($dist[$t] >= $INF) {
            break;
        }
        $f = $limit - $flow;
        for ($v = $t; $v !== $s; $v = $to[$pe[$v] ^ 1]) {
            $f = min($f, $cap[$pe[$v]]);
        }
        for ($v = $t; $v !== $s; $v = $to[$pe[$v] ^ 1]) {
            $cap[$pe[$v]] -= $f;
            $cap[$pe[$v] ^ 1] += $f;
        }
        $flow += $f;
        $total += $f * $dist[$t];
    }

    return [$flow, $total];
};

$litOf = fn (int $x) => $x > 0 ? 2 * ($x - 1) : 2 * (-$x - 1) + 1;

return [
    // ───────────────────────── 2-SAT ─────────────────────────
    [
        'slug' => 'pizza-untuk-semua',
        'lesson' => 'two-sat',
        'title' => 'Pizza untuk Semua',
        'difficulty' => 'Sedang',
        'tags' => ['2-SAT', 'SCC', 'graph implikasi'],
        'statement' => '<p>Kamu memesan satu pizza raksasa untuk acara kelas. Ada <strong>n</strong> pilihan topping, masing-masing boleh dipakai atau tidak. Setiap dari <strong>m</strong> temanmu mengajukan tepat <strong>dua keinginan</strong>, misalnya "pakai jamur" atau "jangan pakai nanas". Seorang teman senang jika <em>minimal satu</em> dari dua keinginannya terpenuhi.</p>
<p>Keinginan ditulis sebagai bilangan bertanda: <code>+k</code> berarti "pakai topping k", <code>-k</code> berarti "jangan pakai topping k". Tentukan apakah ada pilihan topping yang membuat <strong>semua</strong> teman senang.</p>
<p>Satu input berisi beberapa kasus uji.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>, banyak kasus. Setiap kasus diawali baris <code>n m</code>, diikuti m baris berisi dua bilangan bertanda <code>a b</code> (keinginan seorang teman).</p>',
        'output_format' => '<p>Untuk setiap kasus, cetak <code>YA</code> jika semua teman bisa senang, atau <code>TIDAK</code> jika mustahil.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 2000</li><li>1 ≤ n, m; jumlah n seluruh kasus ≤ 10<sup>5</sup>, jumlah m ≤ 1,5·10<sup>5</sup></li><li>1 ≤ |a|, |b| ≤ n (a dan b boleh sama)</li></ul>',
        'samples' => [
            ['input' => "3\n3 4\n1 2\n-1 3\n-2 -3\n1 3\n2 4\n1 2\n-1 2\n1 -2\n-1 -2\n1 2\n1 1\n-1 -1\n", 'explanation' => 'Kasus 1: topping 1 dan 3 dipakai, topping 2 tidak: setiap teman punya minimal satu keinginan yang terpenuhi. Kasus 2: empat teman meminta keempat kombinasi (1 atau 2), (¬1 atau 2), (1 atau ¬2), (¬1 atau ¬2); kombinasi apa pun membuat salah satu kecewa. Kasus 3: teman pertama memaksa topping 1 dipakai, teman kedua memaksa tidak dipakai.'],
        ],
        'tests' => function () {
            // kasus acak di sekitar ambang m ≈ n (campuran YA/TIDAK), kasus tertanam (pasti YA), dan rantai dalam
            $rand = function (int $n, int $m, ?array $plant = null) {
                $rows = [];
                while (count($rows) < $m) {
                    $a = mt_rand(1, $n) * (mt_rand(0, 1) ? 1 : -1);
                    $b = mt_rand(1, $n) * (mt_rand(0, 1) ? 1 : -1);
                    if ($plant) {
                        $va = $a > 0 ? $plant[$a] : 1 - $plant[-$a];
                        $vb = $b > 0 ? $plant[$b] : 1 - $plant[-$b];
                        if (! $va && ! $vb) {
                            continue;
                        }
                    }
                    $rows[] = "$a $b";
                }

                return "$n $m\n".implode("\n", $rows)."\n";
            };
            $plant = fn (int $n) => array_combine(range(1, $n), T::arr($n, 0, 1));
            $many = function (int $T, int $nMax, float $ratioLo, float $ratioHi) use ($rand) {
                $s = "$T\n";
                for ($i = 0; $i < $T; $i++) {
                    $n = mt_rand(1, $nMax);
                    $m = max(1, (int) round($n * ($ratioLo + ($ratioHi - $ratioLo) * mt_rand() / mt_getrandmax())));
                    $s .= $rand($n, $m);
                }

                return $s;
            };
            $chain = function (int $n, bool $bad) {
                $rows = ['1 1'];
                for ($i = 1; $i < $n; $i++) {
                    $rows[] = '-'.$i.' '.($i + 1);
                }
                if ($bad) {
                    $rows[] = "-$n -1";
                }

                return "1\n$n ".count($rows)."\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n1 1\n1 -1\n",
                "2\n2 1\n-2 -2\n3 3\n1 1\n-1 2\n-2 -3\n",
                $many(500, 6, 1.5, 4.0),
                $many(2000, 30, 1.2, 2.5),
                $many(10, 10000, 1.0, 1.3),
                "1\n".$rand(100000, 150000, $plant(100000)),
                "2\n".$rand(50000, 70000, $plant(50000)).$rand(50000, 70000),
                $chain(100000, false),
                $chain(100000, true),
            ];
        },
        'solve' => function (string $input) use ($sat2, $litOf) {
            $tok = preg_split('/\s+/', trim($input));
            $p = 0;
            $T = (int) $tok[$p++];
            $out = [];
            for ($c = 0; $c < $T; $c++) {
                $n = (int) $tok[$p++];
                $m = (int) $tok[$p++];
                $cl = [];
                for ($i = 0; $i < $m; $i++) {
                    $cl[] = [$litOf((int) $tok[$p++]), $litOf((int) $tok[$p++])];
                }
                $out[] = $sat2($n, $cl) === null ? 'TIDAK' : 'YA';
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

    int T;
    cin >> T;
    while (T--) {
        int n, m;
        cin >> n >> m;
        // simpul 2(k-1) = "topping k dipakai", 2(k-1)+1 = "tidak dipakai"
        vector<vector<int>> g(2 * n), rg(2 * n);
        for (int i = 0; i < m; i++) {
            int a, b;
            cin >> a >> b;
            // TODO: (a ∨ b) menjadi dua sisi implikasi
        }
        // TODO: SCC, lalu cek apakah x dan ¬x satu komponen
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const tok = input.split(/\s+/).filter((s) => s !== "").map(Number);
let p = 0;
const T = tok[p++];
const out = [];
for (let c = 0; c < T; c++) {
  const n = tok[p++], m = tok[p++];
  // TODO: bangun graph implikasi 2n simpul, SCC, cek x vs ¬x
  for (let i = 0; i < m; i++) {
    const a = tok[p++], b = tok[p++];
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
p = 0
T = int(data[p]); p += 1
out = []
for _ in range(T):
    n, m = int(data[p]), int(data[p + 1]); p += 2
    # TODO: bangun graph implikasi 2n simpul, SCC (iteratif), cek x vs ¬x
    for i in range(m):
        a, b = int(data[p]), int(data[p + 1]); p += 2
print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int lit(int x) { return x > 0 ? 2 * (x - 1) : 2 * (-x - 1) + 1; }

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int T;
    cin >> T;
    vector<int> st;
    while (T--) {
        int n, m;
        cin >> n >> m;
        int N = 2 * n;
        vector<vector<int>> g(N), rg(N);
        for (int i = 0; i < m; i++) {
            int a, b;
            cin >> a >> b;
            a = lit(a);
            b = lit(b);
            g[a ^ 1].push_back(b); rg[b].push_back(a ^ 1);   // ¬a → b
            g[b ^ 1].push_back(a); rg[a].push_back(b ^ 1);   // ¬b → a
        }
        // Kosaraju tahap 1 (iteratif): urutan selesai
        vector<int> order, it(N, 0);
        vector<char> vis(N, 0);
        for (int s = 0; s < N; s++) {
            if (vis[s]) continue;
            vis[s] = 1;
            st.assign(1, s);
            while (!st.empty()) {
                int u = st.back();
                if (it[u] < (int)g[u].size()) {
                    int v = g[u][it[u]++];
                    if (!vis[v]) { vis[v] = 1; st.push_back(v); }
                } else {
                    order.push_back(u);
                    st.pop_back();
                }
            }
        }
        // tahap 2: graph terbalik, urutan selesai terbalik
        vector<int> comp(N, -1);
        int c = 0;
        for (int i = N - 1; i >= 0; i--) {
            int s = order[i];
            if (comp[s] != -1) continue;
            comp[s] = c;
            st.assign(1, s);
            while (!st.empty()) {
                int u = st.back();
                st.pop_back();
                for (int v : rg[u])
                    if (comp[v] == -1) { comp[v] = c; st.push_back(v); }
            }
            c++;
        }
        bool ok = true;
        for (int i = 0; i < n; i++)
            if (comp[2 * i] == comp[2 * i + 1]) ok = false;
        cout << (ok ? "YA" : "TIDAK") << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const tok = input.split(/\s+/).filter((s) => s !== "").map(Number);
let p = 0;
const T = tok[p++];
const out = [];
const lit = (x) => (x > 0 ? 2 * (x - 1) : 2 * (-x - 1) + 1);
for (let c = 0; c < T; c++) {
  const n = tok[p++], m = tok[p++];
  const N = 2 * n, E = 2 * m;
  // graph dalam bentuk daftar berantai (head/next) agar cepat
  const head = new Int32Array(N).fill(-1), nxt = new Int32Array(E), to = new Int32Array(E);
  const rhead = new Int32Array(N).fill(-1), rnxt = new Int32Array(E), rto = new Int32Array(E);
  let k = 0;
  const add = (u, v) => {
    to[k] = v; nxt[k] = head[u]; head[u] = k;
    rto[k] = u; rnxt[k] = rhead[v]; rhead[v] = k; k++;
  };
  for (let i = 0; i < m; i++) {
    const a = lit(tok[p++]), b = lit(tok[p++]);
    add(a ^ 1, b);
    add(b ^ 1, a);
  }
  const vis = new Uint8Array(N), it = Int32Array.from(head), order = [], st = [];
  for (let s = 0; s < N; s++) {
    if (vis[s]) continue;
    vis[s] = 1; st.push(s);
    while (st.length) {
      const u = st[st.length - 1], e = it[u];
      if (e !== -1) {
        it[u] = nxt[e];
        const v = to[e];
        if (!vis[v]) { vis[v] = 1; st.push(v); }
      } else { order.push(u); st.pop(); }
    }
  }
  const comp = new Int32Array(N).fill(-1);
  let cc = 0;
  for (let i = N - 1; i >= 0; i--) {
    const s = order[i];
    if (comp[s] !== -1) continue;
    comp[s] = cc; st.push(s);
    while (st.length) {
      const u = st.pop();
      for (let e = rhead[u]; e !== -1; e = rnxt[e]) {
        const v = rto[e];
        if (comp[v] === -1) { comp[v] = cc; st.push(v); }
      }
    }
    cc++;
  }
  let ok = true;
  for (let i = 0; i < n; i++) if (comp[2 * i] === comp[2 * i + 1]) ok = false;
  out.push(ok ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    p = 0
    T = int(data[p]); p += 1
    out = []
    for _ in range(T):
        n, m = int(data[p]), int(data[p + 1]); p += 2
        N = 2 * n
        g = [[] for _ in range(N)]
        rg = [[] for _ in range(N)]
        for i in range(m):
            a, b = int(data[p]), int(data[p + 1]); p += 2
            a = 2 * (a - 1) if a > 0 else 2 * (-a - 1) + 1
            b = 2 * (b - 1) if b > 0 else 2 * (-b - 1) + 1
            g[a ^ 1].append(b); rg[b].append(a ^ 1)
            g[b ^ 1].append(a); rg[a].append(b ^ 1)
        vis = [False] * N
        it = [0] * N
        order = []
        for s in range(N):
            if vis[s]:
                continue
            vis[s] = True
            st = [s]
            while st:
                u = st[-1]
                gu = g[u]
                if it[u] < len(gu):
                    v = gu[it[u]]; it[u] += 1
                    if not vis[v]:
                        vis[v] = True
                        st.append(v)
                else:
                    order.append(u)
                    st.pop()
        comp = [-1] * N
        c = 0
        for s in reversed(order):
            if comp[s] != -1:
                continue
            comp[s] = c
            st = [s]
            while st:
                u = st.pop()
                for v in rg[u]:
                    if comp[v] == -1:
                        comp[v] = c
                        st.append(v)
            c += 1
        ok = all(comp[2 * i] != comp[2 * i + 1] for i in range(n))
        out.append("YA" if ok else "TIDAK")
    print("\n".join(out))

main()
CODE,
        ],
        'editorial' => '<p>Setiap teman adalah klausa dua literal (a ∨ b). Pemodelan langsung: peubah x<sub>k</sub> = "topping k dipakai". Bangun graph implikasi 2n simpul dengan dua sisi per klausa, ¬a → b dan ¬b → a, lalu hitung SCC.</p>
<p>Jawaban TIDAK tepat ketika ada k dengan x<sub>k</sub> dan ¬x<sub>k</sub> di komponen yang sama; selain itu jawabannya YA (penugasan bisa dibaca dari urutan topologis komponen, tetapi tidak diminta).</p>
<p><strong>Jebakan:</strong> tes terakhir berisi rantai implikasi sepanjang 10<sup>5</sup>. DFS rekursif bisa kehabisan stack di judge, jadi tulis Kosaraju/Tarjan secara iteratif. Kompleksitas O(n + m) per kasus.</p>',
        'hints' => [
            'Seorang teman dengan keinginan a dan b senang jika (a ∨ b) benar. Ini klausa 2-SAT.',
            '(a ∨ b) setara dengan dua implikasi: ¬a → b dan ¬b → a. Graph-nya punya 2n simpul.',
            'Tidak ada jawaban tepat ketika suatu x dan ¬x berada di SCC yang sama. Tulis DFS secara iteratif karena ada rantai sepanjang 10⁵.',
        ],
    ],

    [
        'slug' => 'pintu-dan-saklar',
        'lesson' => 'two-sat',
        'title' => 'Pintu dan Saklar',
        'difficulty' => 'Sedang',
        'tags' => ['2-SAT', 'XOR', 'pemodelan'],
        'statement' => '<p>Sebuah gedung punya <strong>n</strong> pintu dan <strong>m</strong> saklar. Setiap pintu dikendalikan oleh tepat <strong>dua</strong> saklar (boleh saja saklar yang sama disebut dua kali). Menekan sebuah saklar membalik keadaan semua pintu yang dikendalikannya: yang terkunci menjadi terbuka dan sebaliknya.</p>
<p>Diketahui keadaan awal setiap pintu. Apakah ada cara memilih saklar mana saja yang ditekan (masing-masing paling banyak sekali) sehingga <strong>semua pintu terbuka</strong> bersamaan?</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap kasus: baris <code>n m</code>, baris berisi n bilangan <code>r<sub>1</sub> … r<sub>n</sub></code> (1 = terbuka, 0 = terkunci), lalu n baris <code>a<sub>i</sub> b<sub>i</sub></code>: dua saklar yang mengendalikan pintu ke-i.</p>',
        'output_format' => '<p>Untuk setiap kasus, cetak <code>YA</code> atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 1000</li><li>1 ≤ n, m; jumlah n dan jumlah m seluruh kasus masing-masing ≤ 10<sup>5</sup></li><li>1 ≤ a<sub>i</sub>, b<sub>i</sub> ≤ m</li></ul>',
        'samples' => [
            ['input' => "2\n3 3\n1 0 0\n1 3\n1 2\n2 3\n3 3\n1 0 1\n1 3\n2 2\n2 3\n", 'explanation' => 'Kasus 1: tekan saklar 2 saja. Pintu 1 (saklar 1 dan 3) tidak tersentuh dan tetap terbuka; pintu 2 (saklar 1 dan 2) dan pintu 3 (saklar 2 dan 3) masing-masing dibalik sekali sehingga terbuka. Kasus 2: pintu 2 dikendalikan saklar 2 dua kali, jadi menekan saklar 2 membaliknya dua kali dan keadaannya tidak pernah berubah. Pintu itu tetap terkunci.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $m, bool $planted) {
                $s = T::arr($m, 0, 1);
                $pairs = [];
                $r = [];
                for ($i = 0; $i < $n; $i++) {
                    $a = mt_rand(1, $m);
                    $b = mt_rand(1, $m);
                    if (mt_rand(1, 50) > 1 && $m > 1) {
                        while ($b === $a) {
                            $b = mt_rand(1, $m);
                        }
                    }
                    $pairs[] = "$a $b";
                    // keadaan awal yang membuat penugasan tersembunyi s berhasil
                    $r[] = $planted ? 1 ^ $s[$a - 1] ^ $s[$b - 1] : mt_rand(0, 1);
                }

                return "$n $m\n".T::join($r)."\n".implode("\n", $pairs)."\n";
            };
            $many = function (int $T, int $nMax, int $mMax) use ($gen) {
                $out = "$T\n";
                for ($i = 0; $i < $T; $i++) {
                    $out .= $gen(mt_rand(1, $nMax), mt_rand(1, $mMax), mt_rand(0, 1) === 1);
                }

                return $out;
            };

            return [
                "1\n1 1\n1\n1 1\n",
                "2\n1 1\n0\n1 1\n2 2\n0 0\n1 2\n1 2\n",
                $many(300, 6, 5),
                $many(1000, 40, 30),
                "1\n".$gen(100000, 100000, true),
                "1\n".$gen(100000, 60000, false),
                "2\n".$gen(50000, 2000, true).$gen(50000, 50000, true),
            ];
        },
        'solve' => function (string $input) use ($sat2) {
            $tok = preg_split('/\s+/', trim($input));
            $p = 0;
            $T = (int) $tok[$p++];
            $out = [];
            for ($c = 0; $c < $T; $c++) {
                $n = (int) $tok[$p++];
                $m = (int) $tok[$p++];
                $r = [];
                for ($i = 0; $i < $n; $i++) {
                    $r[] = (int) $tok[$p++];
                }
                $cl = [];
                for ($i = 0; $i < $n; $i++) {
                    $a = 2 * ((int) $tok[$p++] - 1);
                    $b = 2 * ((int) $tok[$p++] - 1);
                    if ($r[$i] === 0) {          // s_a XOR s_b = 1
                        $cl[] = [$a, $b];
                        $cl[] = [$a ^ 1, $b ^ 1];
                    } else {                     // s_a XOR s_b = 0
                        $cl[] = [$a ^ 1, $b];
                        $cl[] = [$a, $b ^ 1];
                    }
                }
                $out[] = $sat2($m, $cl) === null ? 'TIDAK' : 'YA';
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

    int T;
    cin >> T;
    while (T--) {
        int n, m;
        cin >> n >> m;
        vector<int> r(n);
        for (auto& x : r) cin >> x;
        // peubah s_j = "saklar j ditekan"; pintu i terbuka di akhir jika r_i XOR s_a XOR s_b = 1
        for (int i = 0; i < n; i++) {
            int a, b;
            cin >> a >> b;
            // TODO: syarat XOR menjadi dua klausa 2-SAT
        }
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const tok = input.split(/\s+/).filter((s) => s !== "").map(Number);
let p = 0;
const T = tok[p++];
const out = [];
for (let c = 0; c < T; c++) {
  const n = tok[p++], m = tok[p++];
  const r = tok.slice(p, p + n); p += n;
  for (let i = 0; i < n; i++) {
    const a = tok[p++], b = tok[p++];
    // TODO: syarat XOR menjadi dua klausa 2-SAT
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
p = 0
T = int(data[p]); p += 1
out = []
for _ in range(T):
    n, m = int(data[p]), int(data[p + 1]); p += 2
    r = [int(x) for x in data[p:p + n]]; p += n
    for i in range(n):
        a, b = int(data[p]), int(data[p + 1]); p += 2
        # TODO: syarat XOR menjadi dua klausa 2-SAT
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

    int T;
    cin >> T;
    vector<int> st;
    while (T--) {
        int n, m;
        cin >> n >> m;
        vector<int> r(n);
        for (auto& x : r) cin >> x;
        int N = 2 * m;
        vector<vector<int>> g(N), rg(N);
        auto klausa = [&](int a, int b) {          // (a ∨ b)
            g[a ^ 1].push_back(b); rg[b].push_back(a ^ 1);
            g[b ^ 1].push_back(a); rg[a].push_back(b ^ 1);
        };
        for (int i = 0; i < n; i++) {
            int a, b;
            cin >> a >> b;
            a = 2 * (a - 1);
            b = 2 * (b - 1);
            if (r[i] == 0) {                        // harus dibalik ganjil kali: s_a XOR s_b = 1
                klausa(a, b);
                klausa(a ^ 1, b ^ 1);
            } else {                                // dibalik genap kali: s_a = s_b
                klausa(a ^ 1, b);
                klausa(a, b ^ 1);
            }
        }
        vector<int> order, it(N, 0);
        vector<char> vis(N, 0);
        for (int s = 0; s < N; s++) {
            if (vis[s]) continue;
            vis[s] = 1;
            st.assign(1, s);
            while (!st.empty()) {
                int u = st.back();
                if (it[u] < (int)g[u].size()) {
                    int v = g[u][it[u]++];
                    if (!vis[v]) { vis[v] = 1; st.push_back(v); }
                } else {
                    order.push_back(u);
                    st.pop_back();
                }
            }
        }
        vector<int> comp(N, -1);
        int c = 0;
        for (int i = N - 1; i >= 0; i--) {
            int s = order[i];
            if (comp[s] != -1) continue;
            comp[s] = c;
            st.assign(1, s);
            while (!st.empty()) {
                int u = st.back();
                st.pop_back();
                for (int v : rg[u])
                    if (comp[v] == -1) { comp[v] = c; st.push_back(v); }
            }
            c++;
        }
        bool ok = true;
        for (int j = 0; j < m; j++)
            if (comp[2 * j] == comp[2 * j + 1]) ok = false;
        cout << (ok ? "YA" : "TIDAK") << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const tok = input.split(/\s+/).filter((s) => s !== "").map(Number);
let p = 0;
const T = tok[p++];
const out = [];
for (let c = 0; c < T; c++) {
  const n = tok[p++], m = tok[p++];
  const r = tok.slice(p, p + n); p += n;
  const N = 2 * m, E = 4 * n;
  const head = new Int32Array(N).fill(-1), nxt = new Int32Array(E), to = new Int32Array(E);
  const rhead = new Int32Array(N).fill(-1), rnxt = new Int32Array(E), rto = new Int32Array(E);
  let k = 0;
  const add = (u, v) => {
    to[k] = v; nxt[k] = head[u]; head[u] = k;
    rto[k] = u; rnxt[k] = rhead[v]; rhead[v] = k; k++;
  };
  const klausa = (a, b) => { add(a ^ 1, b); add(b ^ 1, a); };
  for (let i = 0; i < n; i++) {
    const a = 2 * (tok[p++] - 1), b = 2 * (tok[p++] - 1);
    if (r[i] === 0) { klausa(a, b); klausa(a ^ 1, b ^ 1); }
    else { klausa(a ^ 1, b); klausa(a, b ^ 1); }
  }
  const vis = new Uint8Array(N), it = Int32Array.from(head), order = [], st = [];
  for (let s = 0; s < N; s++) {
    if (vis[s]) continue;
    vis[s] = 1; st.push(s);
    while (st.length) {
      const u = st[st.length - 1], e = it[u];
      if (e !== -1) {
        it[u] = nxt[e];
        const v = to[e];
        if (!vis[v]) { vis[v] = 1; st.push(v); }
      } else { order.push(u); st.pop(); }
    }
  }
  const comp = new Int32Array(N).fill(-1);
  let cc = 0;
  for (let i = N - 1; i >= 0; i--) {
    const s = order[i];
    if (comp[s] !== -1) continue;
    comp[s] = cc; st.push(s);
    while (st.length) {
      const u = st.pop();
      for (let e = rhead[u]; e !== -1; e = rnxt[e]) {
        const v = rto[e];
        if (comp[v] === -1) { comp[v] = cc; st.push(v); }
      }
    }
    cc++;
  }
  let ok = true;
  for (let j = 0; j < m; j++) if (comp[2 * j] === comp[2 * j + 1]) ok = false;
  out.push(ok ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    p = 0
    T = int(data[p]); p += 1
    out = []
    for _ in range(T):
        n, m = int(data[p]), int(data[p + 1]); p += 2
        r = data[p:p + n]; p += n
        N = 2 * m
        g = [[] for _ in range(N)]
        rg = [[] for _ in range(N)]
        def klausa(a, b):
            g[a ^ 1].append(b); rg[b].append(a ^ 1)
            g[b ^ 1].append(a); rg[a].append(b ^ 1)
        for i in range(n):
            a, b = 2 * (int(data[p]) - 1), 2 * (int(data[p + 1]) - 1); p += 2
            if r[i] == b'0':
                klausa(a, b); klausa(a ^ 1, b ^ 1)
            else:
                klausa(a ^ 1, b); klausa(a, b ^ 1)
        vis = [False] * N
        it = [0] * N
        order = []
        for s in range(N):
            if vis[s]:
                continue
            vis[s] = True
            st = [s]
            while st:
                u = st[-1]
                gu = g[u]
                if it[u] < len(gu):
                    v = gu[it[u]]; it[u] += 1
                    if not vis[v]:
                        vis[v] = True
                        st.append(v)
                else:
                    order.append(u)
                    st.pop()
        comp = [-1] * N
        c = 0
        for s in reversed(order):
            if comp[s] != -1:
                continue
            comp[s] = c
            st = [s]
            while st:
                u = st.pop()
                for v in rg[u]:
                    if comp[v] == -1:
                        comp[v] = c
                        st.append(v)
            c += 1
        ok = all(comp[2 * j] != comp[2 * j + 1] for j in range(m))
        out.append("YA" if ok else "TIDAK")
    print("\n".join(out))

main()
CODE,
        ],
        'editorial' => '<p>Peubah s<sub>j</sub> = "saklar j ditekan". Pintu i yang dikendalikan saklar a dan b berakhir terbuka jika r<sub>i</sub> ⊕ s<sub>a</sub> ⊕ s<sub>b</sub> = 1.</p>
<ul><li>r<sub>i</sub> = 0 (terkunci): perlu s<sub>a</sub> ⊕ s<sub>b</sub> = 1, yaitu "tepat satu": klausa (s<sub>a</sub> ∨ s<sub>b</sub>) dan (¬s<sub>a</sub> ∨ ¬s<sub>b</sub>).</li>
<li>r<sub>i</sub> = 1 (terbuka): perlu s<sub>a</sub> = s<sub>b</sub>: klausa (¬s<sub>a</sub> ∨ s<sub>b</sub>) dan (s<sub>a</sub> ∨ ¬s<sub>b</sub>).</li></ul>
<p>Jalankan 2-SAT pada 2m simpul. Kasus a = b otomatis tertangani: untuk pintu terkunci, klausa (s<sub>a</sub> ∨ s<sub>a</sub>) dan (¬s<sub>a</sub> ∨ ¬s<sub>a</sub>) memaksa s<sub>a</sub> benar sekaligus salah.</p>
<p>Catatan: karena semua syarat berbentuk XOR, soal ini juga bisa diselesaikan dengan DSU berparitas. 2-SAT tetap menjadi pemodelan yang paling langsung. O(n + m).</p>',
        'hints' => [
            'Peubahnya adalah saklar: s_j = 1 jika saklar j ditekan. Keadaan akhir pintu i = r_i ⊕ s_a ⊕ s_b.',
            'Pintu terkunci butuh tepat satu dari s_a, s_b (XOR = 1); pintu terbuka butuh s_a = s_b. Masing-masing adalah dua klausa.',
            'Tepat satu: (a ∨ b) dan (¬a ∨ ¬b). Sama nilai: (¬a ∨ b) dan (a ∨ ¬b). Lalu 2-SAT biasa.',
        ],
    ],

    [
        'slug' => 'bendera-berjauhan',
        'lesson' => 'two-sat',
        'title' => 'Bendera Berjauhan',
        'difficulty' => 'Sulit',
        'tags' => ['2-SAT', 'binary search jawaban'],
        'statement' => '<p>Panitia lomba lari memasang <strong>n</strong> bendera di sepanjang lintasan lurus. Bendera ke-i harus dipasang di salah satu dari dua lubang yang tersedia, di koordinat <code>x<sub>i</sub></code> atau <code>y<sub>i</sub></code>.</p>
<p>Agar mudah terlihat, jarak antara dua bendera mana pun harus sebesar mungkin. Tentukan nilai <strong>maksimum</strong> dari jarak terdekat antara dua bendera, jika setiap bendera dipasang dengan pilihan terbaik.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Setiap dari n baris berikutnya berisi <code>x<sub>i</sub> y<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: jarak terdekat terbesar yang bisa dicapai.</p>',
        'constraints' => '<ul><li>2 ≤ n ≤ 100</li><li>0 ≤ x<sub>i</sub>, y<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n1 4\n2 5\n0 6\n", 'explanation' => 'Bendera di 4, 2, dan 0 memberi jarak 2, 2, dan 4, jadi jarak terdekatnya 2. Jarak 3 mustahil: jika bendera 1 di 1, bendera 2 harus di 5, lalu bendera 3 (di 0 atau 6) berjarak 1 dari salah satunya; jika bendera 1 di 4, bendera 2 berjarak 2 atau 1 darinya.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $rows[] = mt_rand(0, $hi).' '.mt_rand(0, $hi);
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return [
                "2\n5 5\n5 5\n",
                "2\n0 1000000000\n0 1000000000\n",
                "4\n1 2\n1 2\n1 2\n1 2\n",
                $gen(8, 30),
                $gen(20, 100),
                $gen(50, 1000000),
                $gen(100, 1000000000),
                $gen(100, 300),
                $gen(100, 1000000000),
            ];
        },
        'solve' => function (string $input) use ($sat2) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $p = [];
            for ($i = 0; $i < $n; $i++) {
                $p[] = T::ints($lines[$i + 1]);
            }
            $cand = [0 => true];
            for ($i = 0; $i < $n; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    for ($a = 0; $a < 2; $a++) {
                        for ($b = 0; $b < 2; $b++) {
                            $cand[abs($p[$i][$a] - $p[$j][$b])] = true;
                        }
                    }
                }
            }
            $cand = array_keys($cand);
            sort($cand);
            $cek = function (int $D) use ($n, $p, $sat2) {
                $cl = [];
                for ($i = 0; $i < $n; $i++) {
                    for ($j = $i + 1; $j < $n; $j++) {
                        for ($a = 0; $a < 2; $a++) {
                            for ($b = 0; $b < 2; $b++) {
                                if (abs($p[$i][$a] - $p[$j][$b]) < $D) {
                                    // tidak boleh (i memilih a) dan (j memilih b) bersamaan
                                    $cl[] = [(2 * $i + $a) ^ 1, (2 * $j + $b) ^ 1];
                                }
                            }
                        }
                    }
                }

                return $sat2($n, $cl) !== null;
            };
            $lo = 0;
            $hi = count($cand) - 1;
            while ($lo < $hi) {
                $mid = intdiv($lo + $hi + 1, 2);
                if ($cek($cand[$mid])) {
                    $lo = $mid;
                } else {
                    $hi = $mid - 1;
                }
            }

            return (string) $cand[$lo];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> X, Y;

// apakah bisa memasang semua bendera dengan jarak antarbendera ≥ D?
bool bisa(long long D) {
    // TODO: peubah b_i = "bendera i di x_i"; pasangan pilihan yang terlalu dekat → klausa
    return true;
}

int main() {
    cin >> n;
    X.resize(n);
    Y.resize(n);
    for (int i = 0; i < n; i++) cin >> X[i] >> Y[i];
    // TODO: binary search D terbesar dengan bisa(D) benar
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const P = [];
for (let i = 0; i < n; i++) P.push(readInts());
// TODO: binary search D; cek(D) dengan 2-SAT
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
P = [(int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(n)]
# TODO: binary search D; cek(D) dengan 2-SAT
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
long long P[105][2];

bool bisa(long long D) {
    int N = 2 * n;                         // simpul 2i+c = "bendera i memilih lubang c"
    vector<vector<int>> g(N), rg(N);
    for (int i = 0; i < n; i++)
        for (int j = i + 1; j < n; j++)
            for (int a = 0; a < 2; a++)
                for (int b = 0; b < 2; b++)
                    if (llabs(P[i][a] - P[j][b]) < D) {
                        int u = 2 * i + a, v = 2 * j + b;   // klausa (¬u ∨ ¬v)
                        g[u].push_back(v ^ 1); rg[v ^ 1].push_back(u);
                        g[v].push_back(u ^ 1); rg[u ^ 1].push_back(v);
                    }
    vector<int> order, it(N, 0), st;
    vector<char> vis(N, 0);
    for (int s = 0; s < N; s++) {
        if (vis[s]) continue;
        vis[s] = 1;
        st.assign(1, s);
        while (!st.empty()) {
            int u = st.back();
            if (it[u] < (int)g[u].size()) {
                int v = g[u][it[u]++];
                if (!vis[v]) { vis[v] = 1; st.push_back(v); }
            } else {
                order.push_back(u);
                st.pop_back();
            }
        }
    }
    vector<int> comp(N, -1);
    int c = 0;
    for (int i = N - 1; i >= 0; i--) {
        int s = order[i];
        if (comp[s] != -1) continue;
        comp[s] = c;
        st.assign(1, s);
        while (!st.empty()) {
            int u = st.back();
            st.pop_back();
            for (int v : rg[u])
                if (comp[v] == -1) { comp[v] = c; st.push_back(v); }
        }
        c++;
    }
    for (int i = 0; i < n; i++)
        if (comp[2 * i] == comp[2 * i + 1]) return false;
    return true;
}

int main() {
    cin >> n;
    for (int i = 0; i < n; i++) cin >> P[i][0] >> P[i][1];
    vector<long long> cand = {0};               // jawaban pasti salah satu jarak antarlubang
    for (int i = 0; i < n; i++)
        for (int j = i + 1; j < n; j++)
            for (int a = 0; a < 2; a++)
                for (int b = 0; b < 2; b++) cand.push_back(llabs(P[i][a] - P[j][b]));
    sort(cand.begin(), cand.end());
    cand.erase(unique(cand.begin(), cand.end()), cand.end());
    int lo = 0, hi = (int)cand.size() - 1;      // bisa(cand[0] = 0) selalu benar
    while (lo < hi) {
        int mid = (lo + hi + 1) / 2;
        if (bisa(cand[mid])) lo = mid;
        else hi = mid - 1;
    }
    cout << cand[lo] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const P = [];
for (let i = 0; i < n; i++) P.push(readInts());

function bisa(D) {
  const N = 2 * n;
  const g = Array.from({ length: N }, () => []), rg = Array.from({ length: N }, () => []);
  for (let i = 0; i < n; i++)
    for (let j = i + 1; j < n; j++)
      for (let a = 0; a < 2; a++)
        for (let b = 0; b < 2; b++)
          if (Math.abs(P[i][a] - P[j][b]) < D) {
            const u = 2 * i + a, v = 2 * j + b;
            g[u].push(v ^ 1); rg[v ^ 1].push(u);
            g[v].push(u ^ 1); rg[u ^ 1].push(v);
          }
  const vis = new Uint8Array(N), it = new Int32Array(N), order = [];
  for (let s = 0; s < N; s++) {
    if (vis[s]) continue;
    vis[s] = 1;
    const st = [s];
    while (st.length) {
      const u = st[st.length - 1];
      if (it[u] < g[u].length) {
        const v = g[u][it[u]++];
        if (!vis[v]) { vis[v] = 1; st.push(v); }
      } else { order.push(u); st.pop(); }
    }
  }
  const comp = new Int32Array(N).fill(-1);
  let c = 0;
  for (let i = N - 1; i >= 0; i--) {
    const s = order[i];
    if (comp[s] !== -1) continue;
    comp[s] = c;
    const st = [s];
    while (st.length) {
      const u = st.pop();
      for (const v of rg[u]) if (comp[v] === -1) { comp[v] = c; st.push(v); }
    }
    c++;
  }
  for (let i = 0; i < n; i++) if (comp[2 * i] === comp[2 * i + 1]) return false;
  return true;
}

const set = new Set([0]);
for (let i = 0; i < n; i++)
  for (let j = i + 1; j < n; j++)
    for (let a = 0; a < 2; a++)
      for (let b = 0; b < 2; b++) set.add(Math.abs(P[i][a] - P[j][b]));
const cand = [...set].sort((x, y) => x - y);
let lo = 0, hi = cand.length - 1;
while (lo < hi) {
  const mid = (lo + hi + 1) >> 1;
  if (bisa(cand[mid])) lo = mid;
  else hi = mid - 1;
}
console.log(String(cand[lo]));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n = int(data[0])
    P = [(int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(n)]
    N = 2 * n
    pairs = []                               # (jarak, u, v) untuk semua pasangan pilihan
    for i in range(n):
        for j in range(i + 1, n):
            for a in range(2):
                for b in range(2):
                    pairs.append((abs(P[i][a] - P[j][b]), 2 * i + a, 2 * j + b))

    def bisa(D):
        g = [[] for _ in range(N)]
        rg = [[] for _ in range(N)]
        for d, u, v in pairs:
            if d < D:
                g[u].append(v ^ 1); rg[v ^ 1].append(u)
                g[v].append(u ^ 1); rg[u ^ 1].append(v)
        vis = [False] * N
        it = [0] * N
        order = []
        for s in range(N):
            if vis[s]:
                continue
            vis[s] = True
            st = [s]
            while st:
                u = st[-1]
                if it[u] < len(g[u]):
                    v = g[u][it[u]]; it[u] += 1
                    if not vis[v]:
                        vis[v] = True
                        st.append(v)
                else:
                    order.append(u)
                    st.pop()
        comp = [-1] * N
        c = 0
        for s in reversed(order):
            if comp[s] != -1:
                continue
            comp[s] = c
            st = [s]
            while st:
                u = st.pop()
                for v in rg[u]:
                    if comp[v] == -1:
                        comp[v] = c
                        st.append(v)
            c += 1
        return all(comp[2 * i] != comp[2 * i + 1] for i in range(n))

    cand = sorted(set([0] + [d for d, _, _ in pairs]))
    lo, hi = 0, len(cand) - 1
    while lo < hi:
        mid = (lo + hi + 1) // 2
        if bisa(cand[mid]):
            lo = mid
        else:
            hi = mid - 1
    print(cand[lo])

main()
CODE,
        ],
        'editorial' => '<p><strong>Binary search pada jawaban.</strong> Jika jarak terdekat ≥ D bisa dicapai, maka ≥ D′ untuk D′ &lt; D juga bisa. Jadi cari D terbesar yang <em>layak</em>. Jawabannya pasti salah satu jarak antara dua lubang (atau 0), sehingga binary search cukup dilakukan pada daftar kandidat yang sudah diurutkan.</p>
<p><strong>Cek kelayakan dengan 2-SAT.</strong> Peubah b<sub>i</sub> = "bendera i di x<sub>i</sub>" (salah berarti di y<sub>i</sub>). Untuk setiap dua bendera i ≠ j dan setiap pasangan lubang yang jaraknya &lt; D, kedua pilihan itu <em>tidak boleh bersamaan</em>: klausa (¬L<sub>i</sub> ∨ ¬L<sub>j</sub>). Layak tepat ketika 2-SAT punya jawaban.</p>
<p>Satu pemeriksaan O(n²); dengan ~log(2n²) ≈ 15 pemeriksaan, total O(n² log n). Brute force 2<sup>n</sup> tidak mungkin untuk n = 100.</p>
<p><strong>Jebakan:</strong> dua lubang milik bendera yang <em>sama</em> boleh berdekatan (hanya satu yang dipakai), jadi pasangan dengan i = j tidak menghasilkan klausa.</p>',
        'hints' => [
            'Jika jarak terdekat D bisa dicapai, D yang lebih kecil juga. Binary search pada D.',
            'Untuk D tetap, setiap bendera punya dua pilihan, dan pasangan pilihan yang jaraknya < D tidak boleh terjadi bersamaan.',
            '"Tidak boleh bersamaan" adalah klausa (¬a ∨ ¬b). Cek kelayakan = 2-SAT. Kandidat D cukup diambil dari semua jarak antarlubang.',
        ],
    ],

    // ───────────────────────── MCMF ─────────────────────────
    [
        'slug' => 'kurir-dan-pesanan',
        'lesson' => 'mcmf',
        'title' => 'Kurir dan Pesanan',
        'difficulty' => 'Sedang',
        'tags' => ['penugasan', 'Hungarian', 'MCMF'],
        'statement' => '<p>Ada <strong>n</strong> kurir dan <strong>n</strong> pesanan di sebuah kota berbentuk grid. Kurir ke-i berada di (a<sub>i</sub>, b<sub>i</sub>) dan pesanan ke-j di (c<sub>j</sub>, d<sub>j</sub>). Biaya menugaskan kurir i ke pesanan j adalah jarak Manhattan |a<sub>i</sub> − c<sub>j</sub>| + |b<sub>i</sub> − d<sub>j</sub>|.</p>
<p>Setiap kurir mengambil tepat satu pesanan dan setiap pesanan diambil tepat satu kurir. Berapa total biaya minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. n baris berikutnya berisi posisi kurir <code>a<sub>i</sub> b<sub>i</sub></code>, lalu n baris berisi posisi pesanan <code>c<sub>j</sub> d<sub>j</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: total biaya minimum.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 100</li><li>0 ≤ koordinat ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n0 0\n5 5\n10 0\n1 1\n9 1\n4 6\n", 'explanation' => 'Kurir (0, 0) ke pesanan (1, 1) biaya 2, kurir (5, 5) ke (4, 6) biaya 2, kurir (10, 0) ke (9, 1) biaya 2. Total 6.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi) {
                $rows = [];
                for ($i = 0; $i < 2 * $n; $i++) {
                    $rows[] = mt_rand(0, $hi).' '.mt_rand(0, $hi);
                }

                return "$n\n".implode("\n", $rows)."\n";
            };
            // kasus "rakus gagal": pasangan termurah global mengunci pasangan lain yang mahal
            $trap = "2\n0 0\n10 0\n9 0\n20 0\n";

            return [
                "1\n7 7\n7 7\n",
                "1\n0 0\n1000000000 1000000000\n",
                $trap,
                $gen(5, 10),
                $gen(7, 1000),
                $gen(40, 1000000),
                $gen(100, 1000000000),
                $gen(100, 50),
                $gen(100, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $tok = array_map('intval', preg_split('/\s+/', trim($input)));
            $n = $tok[0];
            $a = [];
            for ($i = 1; $i <= $n; $i++) {
                for ($j = 1; $j <= $n; $j++) {
                    $a[$i][$j] = abs($tok[2 * $i - 1] - $tok[2 * ($n + $j) - 1]) + abs($tok[2 * $i] - $tok[2 * ($n + $j)]);
                }
            }
            // Hungarian O(n^3)
            $INF = PHP_INT_MAX >> 2;
            $u = array_fill(0, $n + 1, 0);
            $v = array_fill(0, $n + 1, 0);
            $p = array_fill(0, $n + 1, 0);
            $way = array_fill(0, $n + 1, 0);
            for ($i = 1; $i <= $n; $i++) {
                $p[0] = $i;
                $j0 = 0;
                $minv = array_fill(0, $n + 1, $INF);
                $used = array_fill(0, $n + 1, false);
                do {
                    $used[$j0] = true;
                    $i0 = $p[$j0];
                    $delta = $INF;
                    $j1 = 0;
                    for ($j = 1; $j <= $n; $j++) {
                        if (! $used[$j]) {
                            $cur = $a[$i0][$j] - $u[$i0] - $v[$j];
                            if ($cur < $minv[$j]) {
                                $minv[$j] = $cur;
                                $way[$j] = $j0;
                            }
                            if ($minv[$j] < $delta) {
                                $delta = $minv[$j];
                                $j1 = $j;
                            }
                        }
                    }
                    for ($j = 0; $j <= $n; $j++) {
                        if ($used[$j]) {
                            $u[$p[$j]] += $delta;
                            $v[$j] -= $delta;
                        } else {
                            $minv[$j] -= $delta;
                        }
                    }
                    $j0 = $j1;
                } while ($p[$j0] !== 0);
                do {
                    $j1 = $way[$j0];
                    $p[$j0] = $p[$j1];
                    $j0 = $j1;
                } while ($j0 !== 0);
            }

            return (string) (-$v[0]);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<long long> a(n), b(n), c(n), d(n);
    for (int i = 0; i < n; i++) cin >> a[i] >> b[i];
    for (int j = 0; j < n; j++) cin >> c[j] >> d[j];
    // TODO: matriks biaya Manhattan, lalu penugasan berbiaya minimum
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const K = [], P = [];
for (let i = 0; i < n; i++) K.push(readInts());
for (let j = 0; j < n; j++) P.push(readInts());
// TODO: matriks biaya Manhattan, lalu penugasan berbiaya minimum
CODE,
            'python' => <<<'CODE'
import sys

data = list(map(int, sys.stdin.buffer.read().split()))
n = data[0]
K = [(data[1 + 2 * i], data[2 + 2 * i]) for i in range(n)]
P = [(data[1 + 2 * (n + j)], data[2 + 2 * (n + j)]) for j in range(n)]
# TODO: matriks biaya Manhattan, lalu penugasan berbiaya minimum
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<long long> X(n), Y(n), Px(n), Py(n);
    for (int i = 0; i < n; i++) cin >> X[i] >> Y[i];
    for (int j = 0; j < n; j++) cin >> Px[j] >> Py[j];
    vector<vector<long long>> a(n + 1, vector<long long>(n + 1, 0));
    for (int i = 1; i <= n; i++)
        for (int j = 1; j <= n; j++)
            a[i][j] = llabs(X[i - 1] - Px[j - 1]) + llabs(Y[i - 1] - Py[j - 1]);

    // Hungarian O(n^3), indeks mulai 1
    const long long INF = LLONG_MAX / 4;
    vector<long long> u(n + 1, 0), v(n + 1, 0);
    vector<int> p(n + 1, 0), way(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        p[0] = i;
        int j0 = 0;
        vector<long long> minv(n + 1, INF);
        vector<char> used(n + 1, false);
        do {
            used[j0] = true;
            int i0 = p[j0], j1 = 0;
            long long delta = INF;
            for (int j = 1; j <= n; j++)
                if (!used[j]) {
                    long long cur = a[i0][j] - u[i0] - v[j];
                    if (cur < minv[j]) { minv[j] = cur; way[j] = j0; }
                    if (minv[j] < delta) { delta = minv[j]; j1 = j; }
                }
            for (int j = 0; j <= n; j++)
                if (used[j]) { u[p[j]] += delta; v[j] -= delta; }
                else minv[j] -= delta;
            j0 = j1;
        } while (p[j0] != 0);
        do { int j1 = way[j0]; p[j0] = p[j1]; j0 = j1; } while (j0);
    }
    cout << -v[0] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Biaya bisa mencapai 2·10^11 per pasangan dan 2·10^13 total: masih aman di Number (< 2^53).
const [n] = readInts();
const K = [], P = [];
for (let i = 0; i < n; i++) K.push(readInts());
for (let j = 0; j < n; j++) P.push(readInts());
const a = [[]];
for (let i = 1; i <= n; i++) {
  a.push([0]);
  for (let j = 1; j <= n; j++) a[i].push(Math.abs(K[i - 1][0] - P[j - 1][0]) + Math.abs(K[i - 1][1] - P[j - 1][1]));
}
const INF = Number.MAX_SAFE_INTEGER;
const u = new Array(n + 1).fill(0), v = new Array(n + 1).fill(0);
const p = new Array(n + 1).fill(0), way = new Array(n + 1).fill(0);
for (let i = 1; i <= n; i++) {
  p[0] = i;
  let j0 = 0;
  const minv = new Array(n + 1).fill(INF), used = new Array(n + 1).fill(false);
  do {
    used[j0] = true;
    const i0 = p[j0];
    let delta = INF, j1 = 0;
    for (let j = 1; j <= n; j++)
      if (!used[j]) {
        const cur = a[i0][j] - u[i0] - v[j];
        if (cur < minv[j]) { minv[j] = cur; way[j] = j0; }
        if (minv[j] < delta) { delta = minv[j]; j1 = j; }
      }
    for (let j = 0; j <= n; j++)
      if (used[j]) { u[p[j]] += delta; v[j] -= delta; }
      else minv[j] -= delta;
    j0 = j1;
  } while (p[j0] !== 0);
  do { const j1 = way[j0]; p[j0] = p[j1]; j0 = j1; } while (j0);
}
console.log(String(-v[0]));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = list(map(int, sys.stdin.buffer.read().split()))
    n = data[0]
    K = [(data[1 + 2 * i], data[2 + 2 * i]) for i in range(n)]
    P = [(data[1 + 2 * (n + j)], data[2 + 2 * (n + j)]) for j in range(n)]
    a = [[0] * (n + 1)] + [[0] + [abs(K[i][0] - P[j][0]) + abs(K[i][1] - P[j][1]) for j in range(n)] for i in range(n)]
    INF = float('inf')
    u = [0] * (n + 1)
    v = [0] * (n + 1)
    p = [0] * (n + 1)
    way = [0] * (n + 1)
    for i in range(1, n + 1):
        p[0] = i
        j0 = 0
        minv = [INF] * (n + 1)
        used = [False] * (n + 1)
        while True:
            used[j0] = True
            i0 = p[j0]
            ai = a[i0]
            ui = u[i0]
            delta = INF
            j1 = 0
            for j in range(1, n + 1):
                if not used[j]:
                    cur = ai[j] - ui - v[j]
                    if cur < minv[j]:
                        minv[j] = cur
                        way[j] = j0
                    if minv[j] < delta:
                        delta = minv[j]
                        j1 = j
            for j in range(n + 1):
                if used[j]:
                    u[p[j]] += delta
                    v[j] -= delta
                else:
                    minv[j] -= delta
            j0 = j1
            if p[j0] == 0:
                break
        while j0:
            j1 = way[j0]
            p[j0] = p[j1]
            j0 = j1
    print(-v[0])

main()
CODE,
        ],
        'editorial' => '<p>Ini soal <strong>penugasan</strong> murni: matriks biaya n × n, pilih satu sel di setiap baris dan setiap kolom dengan jumlah minimum. Rakus (pasangan termurah dulu) salah: kurir di x = 0 dan 10, pesanan di x = 9 dan 20 (semua y = 0). Rakus memasangkan 10–9 (biaya 1), lalu terpaksa 0–20 (20), total 21. Optimalnya 0–9 dan 10–20, total 19.</p>
<p>Dua cara benar:</p>
<ul><li><strong>MCMF:</strong> S → kurir (kapasitas 1, biaya 0), kurir → pesanan (1, jarak), pesanan → T (1, 0). n iterasi SPFA pada n² sisi.</li>
<li><strong>Hungarian:</strong> O(n³) langsung pada matriks, lebih cepat dan lebih pendek untuk soal penugasan.</li></ul>
<p>Biaya total bisa mencapai 100 · 2·10<sup>9</sup>, jadi pakai <code>long long</code>.</p>',
        'hints' => [
            'Setiap kurir mendapat tepat satu pesanan dan sebaliknya: ini soal penugasan pada matriks biaya n × n.',
            'Memilih pasangan termurah lebih dulu tidak selalu optimal. Butuh algoritma yang bisa "membatalkan" pilihan: MCMF atau Hungarian.',
            'Hungarian O(n³) cukup untuk n = 100. Gunakan long long karena biaya total bisa melebihi 2³¹.',
        ],
    ],

    [
        'slug' => 'gudang-ke-toko',
        'lesson' => 'mcmf',
        'title' => 'Gudang ke Toko',
        'difficulty' => 'Sedang',
        'tags' => ['MCMF', 'transportasi'],
        'statement' => '<p>Sebuah perusahaan punya <strong>G</strong> gudang dan <strong>S</strong> toko. Gudang ke-i menyimpan <code>stok<sub>i</sub></code> karung beras, dan toko ke-j memesan <code>minta<sub>j</sub></code> karung. Mengirim satu karung dari gudang i ke toko j berongkos <code>c<sub>ij</sub></code>; jika <code>c<sub>ij</sub> = -1</code>, tidak ada jalan dari gudang i ke toko j.</p>
<p>Semua pesanan toko harus dipenuhi penuh (stok gudang boleh bersisa). Berapa ongkos minimum? Cetak <code>-1</code> jika mustahil.</p>',
        'input_format' => '<p>Baris pertama <code>G S</code>. Baris kedua berisi G bilangan stok. Baris ketiga berisi S bilangan permintaan. G baris berikutnya masing-masing berisi S bilangan <code>c<sub>i1</sub> … c<sub>iS</sub></code>.</p>',
        'output_format' => '<p>Ongkos minimum, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ G, S ≤ 40</li><li>0 ≤ stok<sub>i</sub>, minta<sub>j</sub> ≤ 1000</li><li>c<sub>ij</sub> = -1 atau 0 ≤ c<sub>ij</sub> ≤ 1000</li></ul>',
        'samples' => [
            ['input' => "2 3\n5 4\n3 3 3\n1 4 -1\n2 2 3\n", 'explanation' => 'Gudang 1 (stok 5) mengirim 3 karung ke toko 1 (ongkos 3·1) dan 2 karung ke toko 2 (2·4). Gudang 2 (stok 4) mengirim 1 ke toko 2 (1·2) dan 3 ke toko 3 (3·3). Total 3 + 8 + 2 + 9 = 22. Gudang 1 tidak bisa mengirim ke toko 3, jadi toko 3 sepenuhnya bergantung pada gudang 2.'],
            ['input' => "1 2\n10\n4 4\n5 -1\n", 'explanation' => 'Toko 2 tidak terhubung ke gudang mana pun, jadi pesanannya mustahil dipenuhi.'],
        ],
        'tests' => function () {
            $gen = function (int $G, int $S, int $maxQ, float $noRoad, bool $enough) {
                $minta = T::arr($S, 0, $maxQ);
                $stok = T::arr($G, 0, $maxQ);
                if ($enough) {
                    $need = array_sum($minta);
                    $have = array_sum($stok);
                    while ($have < $need) {
                        $i = mt_rand(0, $G - 1);
                        $add = min(1000 - $stok[$i], $need - $have);
                        $stok[$i] += $add;
                        $have += $add;
                        if ($add === 0 && array_sum($stok) >= 1000 * $G) {
                            break;
                        }
                    }
                }
                $rows = [];
                for ($i = 0; $i < $G; $i++) {
                    $r = [];
                    for ($j = 0; $j < $S; $j++) {
                        $r[] = (mt_rand() / mt_getrandmax()) < $noRoad ? -1 : mt_rand(0, 1000);
                    }
                    $rows[] = T::join($r);
                }

                return "$G $S\n".T::join($stok)."\n".T::join($minta)."\n".implode("\n", $rows)."\n";
            };

            return [
                "1 1\n0\n0\n-1\n",
                "1 1\n3\n5\n2\n",
                "2 2\n1000 1000\n1000 1000\n0 1000\n1000 0\n",
                $gen(3, 3, 5, 0.2, true),
                $gen(6, 5, 20, 0.3, true),
                $gen(10, 10, 100, 0.5, true),
                $gen(40, 40, 1000, 0.0, true),
                $gen(40, 40, 1000, 0.6, true),
                $gen(40, 40, 1000, 0.9, true),
                $gen(30, 40, 500, 0.1, false),
            ];
        },
        'solve' => function (string $input) use ($mcmf) {
            $tok = array_map('intval', preg_split('/\s+/', trim($input)));
            [$G, $S] = [$tok[0], $tok[1]];
            $p = 2;
            $stok = array_slice($tok, $p, $G);
            $p += $G;
            $minta = array_slice($tok, $p, $S);
            $p += $S;
            $src = $G + $S;
            $snk = $src + 1;
            $edges = [];
            for ($i = 0; $i < $G; $i++) {
                $edges[] = [$src, $i, $stok[$i], 0];
            }
            for ($j = 0; $j < $S; $j++) {
                $edges[] = [$G + $j, $snk, $minta[$j], 0];
            }
            for ($i = 0; $i < $G; $i++) {
                for ($j = 0; $j < $S; $j++) {
                    $c = $tok[$p++];
                    if ($c >= 0) {
                        $edges[] = [$i, $G + $j, 1000000, $c];
                    }
                }
            }
            [$f, $cost] = $mcmf($snk + 1, $edges, $src, $snk);

            return $f < array_sum($minta) ? '-1' : (string) $cost;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int G, S;
    cin >> G >> S;
    vector<long long> stok(G), minta(S);
    for (auto& x : stok) cin >> x;
    for (auto& x : minta) cin >> x;
    vector<vector<int>> c(G, vector<int>(S));
    for (auto& r : c)
        for (auto& x : r) cin >> x;
    // TODO: jaringan S → gudang → toko → T, lalu min-cost max-flow
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [G, S] = readInts();
const stok = readInts();
const minta = readInts();
const c = [];
for (let i = 0; i < G; i++) c.push(readInts());
// TODO: jaringan S → gudang → toko → T, lalu min-cost max-flow
CODE,
            'python' => <<<'CODE'
import sys

data = list(map(int, sys.stdin.buffer.read().split()))
G, S = data[0], data[1]
stok = data[2:2 + G]
minta = data[2 + G:2 + G + S]
c = [data[2 + G + S + i * S: 2 + G + S + (i + 1) * S] for i in range(G)]
# TODO: jaringan S → gudang → toko → T, lalu min-cost max-flow
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

struct Sisi { int ke; long long cap, biaya; };
vector<Sisi> E;
vector<vector<int>> adj;

void tambah(int u, int v, long long cap, long long biaya) {
    adj[u].push_back(E.size()); E.push_back({v, cap, biaya});
    adj[v].push_back(E.size()); E.push_back({u, 0, -biaya});
}

int main() {
    int G, S;
    cin >> G >> S;
    int src = G + S, snk = G + S + 1, N = G + S + 2;
    adj.assign(N, {});
    long long total = 0;
    for (int i = 0; i < G; i++) { long long x; cin >> x; tambah(src, i, x, 0); }
    for (int j = 0; j < S; j++) { long long x; cin >> x; tambah(G + j, snk, x, 0); total += x; }
    for (int i = 0; i < G; i++)
        for (int j = 0; j < S; j++) {
            int c; cin >> c;
            if (c >= 0) tambah(i, G + j, 1000000, c);
        }
    const long long INF = LLONG_MAX / 4;
    long long aliran = 0, biaya = 0;
    while (true) {
        vector<long long> dist(N, INF);
        vector<int> lewat(N, -1);
        vector<char> inq(N, 0);
        deque<int> q;
        dist[src] = 0; q.push_back(src); inq[src] = 1;
        while (!q.empty()) {
            int u = q.front(); q.pop_front(); inq[u] = 0;
            for (int id : adj[u]) {
                Sisi& e = E[id];
                if (e.cap > 0 && dist[u] + e.biaya < dist[e.ke]) {
                    dist[e.ke] = dist[u] + e.biaya;
                    lewat[e.ke] = id;
                    if (!inq[e.ke]) { inq[e.ke] = 1; q.push_back(e.ke); }
                }
            }
        }
        if (dist[snk] == INF) break;
        long long f = INF;
        for (int v = snk; v != src; v = E[lewat[v] ^ 1].ke) f = min(f, E[lewat[v]].cap);
        for (int v = snk; v != src; v = E[lewat[v] ^ 1].ke) {
            E[lewat[v]].cap -= f;
            E[lewat[v] ^ 1].cap += f;
        }
        aliran += f;
        biaya += f * dist[snk];
    }
    cout << (aliran < total ? -1 : biaya) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [G, S] = readInts();
const stok = readInts();
const minta = readInts();
const src = G + S, snk = G + S + 1, N = G + S + 2;
const ke = [], cap = [], cost = [];
const adj = Array.from({ length: N }, () => []);
const tambah = (u, v, c, w) => {
  adj[u].push(ke.length); ke.push(v); cap.push(c); cost.push(w);
  adj[v].push(ke.length); ke.push(u); cap.push(0); cost.push(-w);
};
let total = 0;
for (let i = 0; i < G; i++) tambah(src, i, stok[i], 0);
for (let j = 0; j < S; j++) { tambah(G + j, snk, minta[j], 0); total += minta[j]; }
for (let i = 0; i < G; i++) {
  const r = readInts();
  for (let j = 0; j < S; j++) if (r[j] >= 0) tambah(i, G + j, 1000000, r[j]);
}
const INF = Number.MAX_SAFE_INTEGER;
let aliran = 0, biaya = 0;
while (true) {
  const dist = new Array(N).fill(INF), lewat = new Array(N).fill(-1), inq = new Uint8Array(N);
  const q = [src];
  let qh = 0;
  dist[src] = 0; inq[src] = 1;
  while (qh < q.length) {
    const u = q[qh++];
    inq[u] = 0;
    for (const id of adj[u]) {
      if (cap[id] > 0 && dist[u] + cost[id] < dist[ke[id]]) {
        dist[ke[id]] = dist[u] + cost[id];
        lewat[ke[id]] = id;
        if (!inq[ke[id]]) { inq[ke[id]] = 1; q.push(ke[id]); }
      }
    }
  }
  if (dist[snk] === INF) break;
  let f = INF;
  for (let v = snk; v !== src; v = ke[lewat[v] ^ 1]) f = Math.min(f, cap[lewat[v]]);
  for (let v = snk; v !== src; v = ke[lewat[v] ^ 1]) { cap[lewat[v]] -= f; cap[lewat[v] ^ 1] += f; }
  aliran += f;
  biaya += f * dist[snk];
}
console.log(String(aliran < total ? -1 : biaya));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque

def main():
    data = list(map(int, sys.stdin.buffer.read().split()))
    G, S = data[0], data[1]
    stok = data[2:2 + G]
    minta = data[2 + G:2 + G + S]
    p = 2 + G + S
    src, snk, N = G + S, G + S + 1, G + S + 2
    ke, cap, cost = [], [], []
    adj = [[] for _ in range(N)]
    def tambah(u, v, c, w):
        adj[u].append(len(ke)); ke.append(v); cap.append(c); cost.append(w)
        adj[v].append(len(ke)); ke.append(u); cap.append(0); cost.append(-w)
    for i in range(G):
        tambah(src, i, stok[i], 0)
    for j in range(S):
        tambah(G + j, snk, minta[j], 0)
    for i in range(G):
        for j in range(S):
            c = data[p]; p += 1
            if c >= 0:
                tambah(i, G + j, 1000000, c)
    INF = float('inf')
    aliran = biaya = 0
    while True:
        dist = [INF] * N
        lewat = [-1] * N
        inq = [False] * N
        dist[src] = 0
        q = deque([src])
        while q:
            u = q.popleft(); inq[u] = False
            du = dist[u]
            for i in adj[u]:
                if cap[i] > 0:
                    v = ke[i]
                    nd = du + cost[i]
                    if nd < dist[v]:
                        dist[v] = nd
                        lewat[v] = i
                        if not inq[v]:
                            inq[v] = True
                            q.append(v)
        if dist[snk] == INF:
            break
        f, v = INF, snk
        while v != src:
            f = min(f, cap[lewat[v]]); v = ke[lewat[v] ^ 1]
        v = snk
        while v != src:
            cap[lewat[v]] -= f; cap[lewat[v] ^ 1] += f; v = ke[lewat[v] ^ 1]
        aliran += f
        biaya += f * dist[snk]
    print(-1 if aliran < sum(minta) else biaya)

main()
CODE,
        ],
        'editorial' => '<p>Ini <strong>soal transportasi</strong>, bentuk klasik aliran biaya minimum:</p>
<ul><li>S → gudang i: kapasitas stok<sub>i</sub>, biaya 0.</li>
<li>gudang i → toko j (jika ada jalan): kapasitas tak terbatas, biaya c<sub>ij</sub> per karung.</li>
<li>toko j → T: kapasitas minta<sub>j</sub>, biaya 0.</li></ul>
<p>Jalankan MCMF. Jika aliran maksimum lebih kecil dari total permintaan, sebagian pesanan tidak bisa dipenuhi: cetak -1. Selain itu, biaya MCMF adalah jawabannya.</p>
<p>Setiap jalur penambah menghabiskan kapasitas sebuah sisi S → gudang, toko → T, atau sisi balik, jadi banyak iterasi kecil (jauh di bawah total karung). Satu iterasi SPFA pada ≈ 1600 sisi. Rakus "kirim lewat jalan termurah dulu" salah karena bisa menghabiskan stok gudang yang menjadi satu-satunya pemasok toko lain.</p>',
        'hints' => [
            'Bangun jaringan: sumber → gudang (kapasitas stok), gudang → toko (biaya per karung), toko → tujuan (kapasitas permintaan).',
            'Aliran 1 satuan = 1 karung. Total ongkos = jumlah aliran × biaya per sisi: ini min-cost flow.',
            'Jika aliran maksimum < total permintaan, jawabannya -1. Kalikan f × dist[T] dalam long long.',
        ],
    ],

    [
        'slug' => 'dua-rute-konvoi',
        'lesson' => 'mcmf',
        'title' => 'Dua Rute Konvoi',
        'difficulty' => 'Sulit',
        'tags' => ['MCMF', 'node splitting', 'jalur saling lepas'],
        'statement' => '<p>Dua konvoi berangkat dari kota 1 menuju kota n melalui jaringan jalan satu arah. Demi keamanan, kedua rute <strong>tidak boleh melewati kota yang sama</strong> (kecuali kota 1 dan kota n) dan tidak boleh memakai ruas jalan yang sama.</p>
<p>Jalan ke-i menghubungkan u<sub>i</sub> → v<sub>i</sub> dengan panjang w<sub>i</sub>. Berapa <strong>total panjang minimum</strong> kedua rute? Cetak <code>-1</code> jika dua rute seperti itu tidak ada.</p>',
        'input_format' => '<p>Baris pertama <code>n m</code>. Setiap dari m baris berikutnya berisi <code>u v w</code>. Bisa ada beberapa jalan untuk pasangan kota yang sama.</p>',
        'output_format' => '<p>Total panjang minimum, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>2 ≤ n ≤ 300</li><li>1 ≤ m ≤ 5000</li><li>1 ≤ u, v ≤ n, u ≠ v, 1 ≤ w ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4 5\n1 2 1\n2 4 1\n1 3 2\n3 4 2\n2 3 1\n", 'explanation' => 'Rute 1 → 2 → 4 (panjang 2) dan 1 → 3 → 4 (panjang 4). Total 6. Rute terpendek tunggal 1 → 2 → 4 tetap dipakai, tetapi tidak selalu begitu (lihat pembahasan).'],
            ['input' => "3 2\n1 2 5\n2 3 5\n", 'explanation' => 'Hanya ada satu rute, dan kota 2 tidak boleh dilewati dua kali.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $m, int $wMax) {
                $rows = [];
                for ($i = 0; $i < $m; $i++) {
                    $u = mt_rand(1, $n);
                    $v = mt_rand(1, $n);
                    while ($v === $u) {
                        $v = mt_rand(1, $n);
                    }
                    $rows[] = "$u $v ".mt_rand(1, $wMax);
                }

                return "$n $m\n".implode("\n", $rows)."\n";
            };
            // jebakan: rute terpendek tunggal memblokir; harus "dibatalkan" lewat sisi balik
            $trap = "6 7\n1 2 1\n2 3 1\n3 6 1\n1 4 5\n4 3 1\n2 5 1\n5 6 5\n";
            // dua jalan langsung 1 → n
            $paralel = "2 3\n1 2 7\n1 2 3\n1 2 4\n";
            // rantai panjang + jalan pintas
            $rows = [];
            for ($i = 1; $i < 300; $i++) {
                $rows[] = "$i ".($i + 1).' 1';
            }
            for ($i = 1; $i <= 298; $i += 3) {
                $rows[] = "$i ".($i + 2).' 3';
            }
            $rantai = "300 ".count($rows)."\n".implode("\n", $rows)."\n";

            return [
                $paralel,
                "2 1\n1 2 9\n",
                $trap,
                $gen(6, 12, 9),
                $gen(30, 100, 100),
                $gen(100, 400, 1000),
                $gen(300, 5000, 1000000),
                $gen(300, 1200, 10),
                $rantai,
                $gen(300, 5000, 3),
            ];
        },
        'solve' => function (string $input) use ($mcmf) {
            $tok = array_map('intval', preg_split('/\s+/', trim($input)));
            [$n, $m] = [$tok[0], $tok[1]];
            // simpul v_in = 2(v-1), v_out = 2(v-1)+1
            $edges = [];
            for ($v = 1; $v <= $n; $v++) {
                $edges[] = [2 * ($v - 1), 2 * ($v - 1) + 1, ($v === 1 || $v === $n) ? 2 : 1, 0];
            }
            for ($i = 0; $i < $m; $i++) {
                [$u, $v, $w] = [$tok[2 + 3 * $i], $tok[3 + 3 * $i], $tok[4 + 3 * $i]];
                $edges[] = [2 * ($u - 1) + 1, 2 * ($v - 1), 1, $w];
            }
            [$f, $c] = $mcmf(2 * $n, $edges, 1, 2 * ($n - 1), 2);

            return $f < 2 ? '-1' : (string) $c;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n, m;
    cin >> n >> m;
    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        // TODO: pecah setiap kota menjadi v_in → v_out berkapasitas 1
    }
    // TODO: alirkan tepat 2 satuan dengan biaya minimum
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  // TODO: pecah setiap kota menjadi v_in → v_out berkapasitas 1
}
// TODO: alirkan tepat 2 satuan dengan biaya minimum
CODE,
            'python' => <<<'CODE'
import sys

data = list(map(int, sys.stdin.buffer.read().split()))
n, m = data[0], data[1]
for i in range(m):
    u, v, w = data[2 + 3 * i], data[3 + 3 * i], data[4 + 3 * i]
    # TODO: pecah setiap kota menjadi v_in -> v_out berkapasitas 1
# TODO: alirkan tepat 2 satuan dengan biaya minimum
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

struct Sisi { int ke; int cap; long long biaya; };
vector<Sisi> E;
vector<vector<int>> adj;

void tambah(int u, int v, int cap, long long biaya) {
    adj[u].push_back(E.size()); E.push_back({v, cap, biaya});
    adj[v].push_back(E.size()); E.push_back({u, 0, -biaya});
}

int main() {
    int n, m;
    cin >> n >> m;
    int N = 2 * n;                                  // in(v) = 2(v-1), out(v) = 2(v-1)+1
    adj.assign(N, {});
    for (int v = 1; v <= n; v++)
        tambah(2 * (v - 1), 2 * (v - 1) + 1, (v == 1 || v == n) ? 2 : 1, 0);
    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        tambah(2 * (u - 1) + 1, 2 * (v - 1), 1, w);
    }
    int s = 1, t = 2 * (n - 1);                     // dari out(1) ke in(n)
    const long long INF = LLONG_MAX / 4;
    int aliran = 0;
    long long biaya = 0;
    while (aliran < 2) {
        vector<long long> dist(N, INF);
        vector<int> lewat(N, -1);
        vector<char> inq(N, 0);
        deque<int> q;
        dist[s] = 0; q.push_back(s); inq[s] = 1;
        while (!q.empty()) {
            int u = q.front(); q.pop_front(); inq[u] = 0;
            for (int id : adj[u]) {
                Sisi& e = E[id];
                if (e.cap > 0 && dist[u] + e.biaya < dist[e.ke]) {
                    dist[e.ke] = dist[u] + e.biaya;
                    lewat[e.ke] = id;
                    if (!inq[e.ke]) { inq[e.ke] = 1; q.push_back(e.ke); }
                }
            }
        }
        if (dist[t] == INF) break;
        for (int v = t; v != s; v = E[lewat[v] ^ 1].ke) {   // setiap jalur membawa 1 satuan
            E[lewat[v]].cap -= 1;
            E[lewat[v] ^ 1].cap += 1;
        }
        aliran++;
        biaya += dist[t];
    }
    cout << (aliran < 2 ? -1 : biaya) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const N = 2 * n;
const ke = [], cap = [], cost = [];
const adj = Array.from({ length: N }, () => []);
const tambah = (u, v, c, w) => {
  adj[u].push(ke.length); ke.push(v); cap.push(c); cost.push(w);
  adj[v].push(ke.length); ke.push(u); cap.push(0); cost.push(-w);
};
for (let v = 1; v <= n; v++) tambah(2 * (v - 1), 2 * (v - 1) + 1, v === 1 || v === n ? 2 : 1, 0);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  tambah(2 * (u - 1) + 1, 2 * (v - 1), 1, w);
}
const s = 1, t = 2 * (n - 1), INF = Number.MAX_SAFE_INTEGER;
let aliran = 0, biaya = 0;
while (aliran < 2) {
  const dist = new Array(N).fill(INF), lewat = new Array(N).fill(-1), inq = new Uint8Array(N);
  const q = [s];
  let qh = 0;
  dist[s] = 0; inq[s] = 1;
  while (qh < q.length) {
    const u = q[qh++];
    inq[u] = 0;
    for (const id of adj[u]) {
      if (cap[id] > 0 && dist[u] + cost[id] < dist[ke[id]]) {
        dist[ke[id]] = dist[u] + cost[id];
        lewat[ke[id]] = id;
        if (!inq[ke[id]]) { inq[ke[id]] = 1; q.push(ke[id]); }
      }
    }
  }
  if (dist[t] === INF) break;
  for (let v = t; v !== s; v = ke[lewat[v] ^ 1]) { cap[lewat[v]] -= 1; cap[lewat[v] ^ 1] += 1; }
  aliran++;
  biaya += dist[t];
}
console.log(String(aliran < 2 ? -1 : biaya));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque

def main():
    data = list(map(int, sys.stdin.buffer.read().split()))
    n, m = data[0], data[1]
    N = 2 * n
    ke, cap, cost = [], [], []
    adj = [[] for _ in range(N)]
    def tambah(u, v, c, w):
        adj[u].append(len(ke)); ke.append(v); cap.append(c); cost.append(w)
        adj[v].append(len(ke)); ke.append(u); cap.append(0); cost.append(-w)
    for v in range(1, n + 1):
        tambah(2 * (v - 1), 2 * (v - 1) + 1, 2 if v in (1, n) else 1, 0)
    for i in range(m):
        u, v, w = data[2 + 3 * i], data[3 + 3 * i], data[4 + 3 * i]
        tambah(2 * (u - 1) + 1, 2 * (v - 1), 1, w)
    s, t = 1, 2 * (n - 1)
    INF = float('inf')
    aliran = biaya = 0
    while aliran < 2:
        dist = [INF] * N
        lewat = [-1] * N
        inq = [False] * N
        dist[s] = 0
        q = deque([s])
        while q:
            u = q.popleft(); inq[u] = False
            for i in adj[u]:
                if cap[i] > 0 and dist[u] + cost[i] < dist[ke[i]]:
                    dist[ke[i]] = dist[u] + cost[i]
                    lewat[ke[i]] = i
                    if not inq[ke[i]]:
                        inq[ke[i]] = True
                        q.append(ke[i])
        if dist[t] == INF:
            break
        v = t
        while v != s:
            cap[lewat[v]] -= 1; cap[lewat[v] ^ 1] += 1; v = ke[lewat[v] ^ 1]
        aliran += 1
        biaya += dist[t]
    print(-1 if aliran < 2 else biaya)

main()
CODE,
        ],
        'editorial' => '<p><strong>Mengapa tidak "cari rute terpendek, hapus, cari lagi"?</strong> Pada tes jebakan, rute terpendek 1 → 2 → 3 → 6 (panjang 3) memakai kota 2 dan 3 yang dibutuhkan kedua rute lain. Setelah menghapusnya tidak ada rute kedua, padahal 1 → 2 → 5 → 6 dan 1 → 4 → 3 → 6 (total 7 + 7 = 14) sah. Rute pertama harus bisa "dibatalkan" sebagian: itulah peran sisi balik pada MCMF.</p>
<p><strong>Pemodelan.</strong> Larangan berbagi <em>kota</em> ditangani dengan <strong>memecah simpul</strong>: setiap kota v menjadi v<sub>in</sub> → v<sub>out</sub> berkapasitas 1 (kota 1 dan n berkapasitas 2). Jalan u → v menjadi u<sub>out</sub> → v<sub>in</sub> berkapasitas 1 dan berbiaya w. Alirkan tepat 2 satuan dari 1<sub>out</sub> ke n<sub>in</sub> dengan biaya minimum. Jika aliran maksimum &lt; 2, jawabannya -1.</p>
<p>Hanya dua iterasi SPFA pada 2n simpul dan m + n sisi. Jawaban bisa mencapai 2·300·10<sup>6</sup>, pakai long long.</p>',
        'hints' => [
            'Mencari rute terpendek lalu menghapusnya bisa gagal: rute pertama mungkin "menghalangi" pasangan rute yang optimal.',
            'Larangan berbagi kota: pecah setiap kota v menjadi v_in → v_out dengan kapasitas 1.',
            'Alirkan tepat 2 satuan dengan min-cost flow dari 1_out ke n_in. Jika hanya 1 satuan yang bisa mengalir, jawabannya -1.',
        ],
    ],
];
