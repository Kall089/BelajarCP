@php
    $lcaSteps = [
        ['Header & ukuran tabel lompatan', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int LOG = 17;
CPP, <<<'TXT'
<p><code>LOG</code> adalah banyaknya "ukuran lompatan" yang kita siapkan: 2<sup>0</sup>, 2<sup>1</sup>, …, 2<sup>16</sup>. Karena 2<sup>17</sup> = 131 072 lebih besar dari N maksimum (10<sup>5</sup>), lompatan sejauh apa pun di pohon bisa disusun dari ukuran-ukuran ini.</p>
<div class="wt-tip">Aturan praktis: pilih LOG sehingga 2<sup>LOG</sup> &gt; N. Untuk N ≤ 2 × 10<sup>5</sup> pakai 18, untuk N ≤ 10<sup>6</sup> pakai 20.</div>
TXT],
        ['Membaca pohon', <<<'CPP'

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
CPP, <<<'TXT'
<p>Pohon dengan n simpul selalu punya tepat <code>n − 1</code> sisi. Kita simpan sebagai graph tak berarah biasa. Akar pohonnya simpul 1.</p>
TXT],
        ['Tabel up dan depth', <<<'CPP'

    vector<vector<int>> up(LOG, vector<int>(n + 1, 1));
    vector<int> depth(n + 1, -1);
CPP, <<<'TXT'
<ul>
    <li><code>up[j][v]</code> = leluhur v yang berada <strong>2<sup>j</sup> langkah</strong> di atasnya. <code>up[0][v]</code> adalah parent, <code>up[1][v]</code> kakek, <code>up[2][v]</code> leluhur 4 langkah di atas, dan seterusnya.</li>
    <li><code>depth[v]</code> = jarak v dari akar. Nilai -1 berarti belum dikunjungi.</li>
</ul>
<p>Semua sel diisi 1 (akar). Jadi jika lompatan melewati akar, hasilnya tetap berhenti di akar. Ini menghindari indeks tak valid.</p>
TXT],
        ['BFS dari akar: depth & parent', <<<'CPP'

    queue<int> q;
    depth[1] = 0;
    q.push(1);
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        for (int v : adj[u]) {
            if (depth[v] == -1) {
                depth[v] = depth[u] + 1;
                up[0][v] = u;
                q.push(v);
            }
        }
    }
CPP, <<<'TXT'
<p>BFS biasa dari akar. Saat v pertama kali ditemukan dari u, maka u adalah <strong>parent</strong> v (<code>up[0][v] = u</code>) dan kedalamannya satu lebih dari u.</p>
<div class="wt-tip">Kita pakai BFS, bukan DFS rekursif, supaya aman untuk pohon berbentuk garis panjang sedalam 10<sup>5</sup>.</div>
TXT],
        ['Mengisi tabel lompatan (doubling)', <<<'CPP'

    for (int j = 1; j < LOG; j++) {
        for (int v = 1; v <= n; v++) {
            up[j][v] = up[j - 1][up[j - 1][v]];
        }
    }
CPP, <<<'TXT'
<p>Kunci binary lifting: lompat 2<sup>j</sup> langkah = lompat 2<sup>j−1</sup> langkah, lalu lompat 2<sup>j−1</sup> langkah lagi.</p>
<pre>up[1][7] = up[0][ up[0][7] ] = up[0][6] = 5     (2 langkah)
up[2][7] = up[1][ up[1][7] ] = up[1][5] = 3     (4 langkah)</pre>
<p>Baris j hanya bergantung pada baris j − 1, jadi tabel diisi dari atas ke bawah. Totalnya <code>LOG × N</code> sel, yaitu <strong>O(N log N)</strong>.</p>
TXT],
        ['LCA tahap 1: samakan kedalaman', <<<'CPP'

    auto lca = [&](int u, int v) {
        if (depth[u] < depth[v]) swap(u, v);
        int diff = depth[u] - depth[v];
        for (int j = 0; j < LOG; j++) {
            if (diff >> j & 1) u = up[j][u];
        }
CPP, <<<'TXT'
<p>Fungsi <code>lca</code> ditulis sebagai <strong>lambda</strong> (<code>[&amp;]</code> berarti ia boleh memakai variabel <code>up</code> dan <code>depth</code> dari main).</p>
<ol>
    <li>Pastikan u yang lebih dalam (tukar jika perlu).</li>
    <li>Naikkan u sebanyak <code>diff</code> langkah. Caranya: tulis diff dalam biner, lalu untuk setiap bit yang menyala lompat 2<sup>j</sup>. <code>diff &gt;&gt; j &amp; 1</code> mengambil bit ke-j.</li>
</ol>
<pre>diff = 5 = 101₂  → lompat 2⁰ = 1 langkah, lalu 2² = 4 langkah</pre>
TXT],
        ['LCA tahap 2: naik bersama', <<<'CPP'

        if (u == v) return u;
        for (int j = LOG - 1; j >= 0; j--) {
            if (up[j][u] != up[j][v]) {
                u = up[j][u];
                v = up[j][v];
            }
        }
        return up[0][u];
    };
CPP, <<<'TXT'
<p>Jika setelah disamakan u sudah sama dengan v, maka v adalah leluhur u, dan itulah LCA-nya.</p>
<p>Jika belum, coba lompatan dari yang <strong>terbesar</strong> ke yang terkecil, untuk u dan v <strong>bersamaan</strong>:</p>
<ul>
    <li>Jika <code>up[j][u] != up[j][v]</code>, tujuan lompatan masih di bawah LCA. Aman, lompat.</li>
    <li>Jika sama, lompatan itu mendarat di LCA atau di atasnya. Bisa jadi kebablasan, jadi jangan lompat.</li>
</ul>
<p>Di akhir, u dan v tepat satu langkah di bawah LCA, sehingga jawabannya <code>up[0][u]</code>.</p>
TXT],
        ['Menjawab pertanyaan', <<<'CPP'

    int t;
    cin >> t;
    while (t--) {
        int u, v;
        cin >> u >> v;
        int w = lca(u, v);
        cout << w << ' ' << depth[u] + depth[v] - 2 * depth[w] << '\n';
    }
    return 0;
}
CPP, <<<'TXT'
<p>Untuk setiap pertanyaan, cetak LCA dan <strong>jarak</strong> antara u dan v (jumlah sisi).</p>
<p>Rumus jaraknya: dari u naik ke LCA (<code>depth[u] − depth[w]</code> langkah), lalu turun ke v (<code>depth[v] − depth[w]</code> langkah). Jumlahnya <code>depth[u] + depth[v] − 2 · depth[w]</code>.</p>
<pre>LCA(7, 10) = 3,  jarak = 6 + 5 − 2·2 = 7</pre>
TXT],
    ];

    $lcaJs = <<<'JS'
const [n] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}

const LOG = 17;
const up = Array.from({ length: LOG }, () => new Array(n + 1).fill(1));
const depth = new Array(n + 1).fill(-1);
depth[1] = 0;
const queue = [1];
for (let h = 0; h < queue.length; h++) {
  const u = queue[h];
  for (const v of adj[u]) {
    if (depth[v] === -1) {
      depth[v] = depth[u] + 1;
      up[0][v] = u;
      queue.push(v);
    }
  }
}
for (let j = 1; j < LOG; j++) {
  for (let v = 1; v <= n; v++) up[j][v] = up[j - 1][up[j - 1][v]];
}

function lca(u, v) {
  if (depth[u] < depth[v]) [u, v] = [v, u];
  const diff = depth[u] - depth[v];
  for (let j = 0; j < LOG; j++) if ((diff >> j) & 1) u = up[j][u];
  if (u === v) return u;
  for (let j = LOG - 1; j >= 0; j--) {
    if (up[j][u] !== up[j][v]) {
      u = up[j][u];
      v = up[j][v];
    }
  }
  return up[0][u];
}

const [t] = readInts();
const out = [];
for (let i = 0; i < t; i++) {
  const [u, v] = readInts();
  const w = lca(u, v);
  out.push(`${w} ${depth[u] + depth[v] - 2 * depth[w]}`);
}
console.log(out.join("\n"));
JS;

    $lcaPy = <<<'PY'
import sys
from collections import deque
input = sys.stdin.readline

n = int(input())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

LOG = 17
up = [[1] * (n + 1) for _ in range(LOG)]
depth = [-1] * (n + 1)
depth[1] = 0
q = deque([1])
while q:
    u = q.popleft()
    for v in adj[u]:
        if depth[v] == -1:
            depth[v] = depth[u] + 1
            up[0][v] = u
            q.append(v)
for j in range(1, LOG):
    prev, cur = up[j - 1], up[j]
    for v in range(1, n + 1):
        cur[v] = prev[prev[v]]

def lca(u, v):
    if depth[u] < depth[v]:
        u, v = v, u
    diff = depth[u] - depth[v]
    for j in range(LOG):
        if diff >> j & 1:
            u = up[j][u]
    if u == v:
        return u
    for j in range(LOG - 1, -1, -1):
        if up[j][u] != up[j][v]:
            u, v = up[j][u], up[j][v]
    return up[0][u]

t = int(input())
out = []
for _ in range(t):
    u, v = map(int, input().split())
    w = lca(u, v)
    out.append(f"{w} {depth[u] + depth[v] - 2 * depth[w]}")
print("\n".join(out))
PY;

    $lcaKth = <<<'CPP'
// Leluhur ke-k dari v (misalnya "siapa kakek buyut v?"), O(log N)
int leluhur(int v, int k) {
    if (k > depth[v]) return -1;          // melewati akar
    for (int j = 0; j < LOG; j++)
        if (k >> j & 1) v = up[j][v];
    return v;
}
CPP;

    $lcaWeighted = <<<'CPP'
// Pohon BERBOBOT: simpan jarak dari akar dalam long long
vector<long long> jarak(n + 1, 0);
// saat BFS menemukan v dari u lewat sisi berbobot w:
//     jarak[v] = jarak[u] + w;
// jarak antara a dan b:
long long d = jarak[a] + jarak[b] - 2 * jarak[lca(a, b)];
CPP;

    $lcaMax = <<<'CPP'
// Bobot sisi TERBESAR di rute u → v: simpan juga maksimum setiap lompatan
// mx[j][v] = bobot terbesar pada 2^j sisi di atas v
mx[j][v] = max(mx[j - 1][v], mx[j - 1][ up[j - 1][v] ]);
// Saat mengangkat u atau v di fungsi lca, catat jawaban = max(jawaban, mx[j][u]) sebelum u = up[j][u].
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memahami istilah pohon berakar: <strong>parent, leluhur, kedalaman</strong>, dan <strong>Lowest Common Ancestor</strong> (LCA).</li>
        <li>Menjelaskan mengapa cara naif O(N) per pertanyaan terlalu lambat.</li>
        <li>Membangun tabel <strong>binary lifting</strong> dan menjawab LCA dalam <strong>O(log N)</strong> di C++.</li>
        <li>Menghitung <strong>jarak di pohon</strong>, leluhur ke-k, dan informasi lain di sepanjang rute.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'leluhur bersama di pohon', 'desc' => 'Dari silsilah keluarga ke ide lompatan berpangkat dua.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Silsilah Keluarga">
    <h2>Intuisi: Silsilah Keluarga</h2>
    <div class="prose">
        <p>Bayangkan pohon silsilah. Kamu dan sepupumu punya banyak leluhur yang sama: kakek, buyut, dan seterusnya. Leluhur bersama yang <strong>paling dekat</strong> (paling dalam di pohon) disebut <strong>Lowest Common Ancestor</strong> (LCA). Untuk dua sepupu, LCA-nya adalah kakek mereka.</p>
        <ul>
            <li><strong>Akar</strong>: simpul paling atas (di sini simpul 1).</li>
            <li><strong>Kedalaman</strong> (<em>depth</em>): banyaknya langkah dari akar.</li>
            <li><strong>Leluhur</strong> v: semua simpul pada jalan dari v naik ke akar.</li>
        </ul>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 380,
        'nodes' => [[1, 300, 40, 'green'], [2, 230, 95], [12, 400, 95], [3, 230, 150, 'cur'], [4, 120, 205], [8, 340, 205], [5, 120, 260], [9, 340, 260], [6, 120, 315], [10, 340, 315, 'red'], [7, 60, 360, 'cyan'], [11, 400, 360]],
        'edges' => [[1, 2], [1, 12], [2, 3], [3, 4, 'cy'], [3, 8, 'hl'], [4, 5, 'cy'], [5, 6, 'cy'], [6, 7, 'cy'], [8, 9, 'hl'], [9, 10, 'hl'], [10, 11]],
        'badges' => [3 => 'LCA'],
        'caption' => 'LCA(7, 10) = <strong>3</strong>: titik pertama tempat jalan naik dari 7 (cyan) dan dari 10 (ungu) bertemu.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Banyak soal pohon bermuara ke LCA: jarak dua simpul, rute antar dua kota di jaringan berbentuk pohon, hubungan kekerabatan, sampai nilai terbesar di sepanjang rute.</p>
    </div>
</section>

<section class="lesson-section" id="naif" data-toc="Cara Naif & Masalahnya">
    <h2>Cara Naif dan Masalahnya</h2>
    <div class="prose">
        <p>Cara paling sederhana mencari LCA(u, v):</p>
        <ol>
            <li>Naikkan simpul yang lebih dalam <strong>satu langkah demi satu langkah</strong> sampai kedalamannya sama.</li>
            <li>Naikkan keduanya bersama-sama, satu langkah demi satu langkah, sampai bertemu.</li>
        </ol>
        <p>Benar, tetapi pada pohon berbentuk garis panjang satu pertanyaan bisa butuh 10<sup>5</sup> langkah. Dengan 10<sup>5</sup> pertanyaan, totalnya 10<sup>10</sup>: jauh terlalu lambat.</p>
        <p><strong>Ide binary lifting:</strong> setiap bilangan bisa ditulis sebagai jumlah pangkat dua, misalnya 13 = 8 + 4 + 1. Jika kita sudah menyiapkan "lompatan" sejauh 1, 2, 4, 8, … untuk setiap simpul, naik 13 langkah cukup dengan 3 lompatan. Naik sejauh apa pun cukup dengan <strong>paling banyak log₂ N</strong> lompatan.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: LCA(7, 10)</h2>
    <div class="prose">
        <p>Tabel lompatan untuk pohon di atas (sebagian). Baris <code>2^j</code> berisi leluhur 2<sup>j</sup> langkah di atas setiap simpul.</p>
    </div>
    <div class="dp-mini" style="grid-template-columns: repeat(9, auto); margin-bottom: 14px">
        <span class="h">j \ v</span><span class="h">3</span><span class="h">4</span><span class="h">5</span><span class="h">6</span><span class="h">7</span><span class="h">8</span><span class="h">9</span><span class="h">10</span>
        <span class="h">2^0</span><span>2</span><span class="dep">3</span><span>4</span><span>5</span><span class="dep">6</span><span class="dep2">3</span><span>8</span><span>9</span>
        <span class="h">2^1</span><span>1</span><span>2</span><span>3</span><span class="dep">4</span><span>5</span><span>2</span><span>3</span><span class="dep2">8</span>
        <span class="h">2^2</span><span>1</span><span>1</span><span>1</span><span>2</span><span>3</span><span>1</span><span>1</span><span>2</span>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>#</th><th>Aksi</th><th>u</th><th>v</th></tr>
            <tr><td>1</td><td>depth[7] = 6, depth[10] = 5. Selisih 1 = 1₂</td><td>7</td><td>10</td></tr>
            <tr class="hl"><td>2</td><td>Bit 0 menyala: u = up[0][7] = <span class="new">6</span></td><td>6</td><td>10</td></tr>
            <tr><td>3</td><td>j = 2: up[2][6] = 2 dan up[2][10] = 2 sama → jangan lompat</td><td>6</td><td>10</td></tr>
            <tr class="hl"><td>4</td><td>j = 1: up[1][6] = 4 ≠ up[1][10] = 8 → <span class="new">lompat</span></td><td>4</td><td>8</td></tr>
            <tr><td>5</td><td>j = 0: up[0][4] = 3 dan up[0][8] = 3 sama → jangan lompat</td><td>4</td><td>8</td></tr>
            <tr class="ok"><td>6</td><td>LCA = up[0][4] = <b>3</b> ✓</td><td colspan="2">selesai</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Langkah 3 penting: lompat 4 langkah membuat keduanya mendarat di simpul 2, yang memang leluhur bersama, tetapi <strong>bukan yang terdalam</strong>. Karena kita tidak tahu apakah sudah kebablasan, lompatan yang mendarat di tempat yang sama selalu dilewati.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2>Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Pilih simpul <strong>u</strong> dan <strong>v</strong> di atas visualisasi. Animasi mengisi tabel lompatan dulu, lalu menjawab LCA(u, v) langkah demi langkah. Di <strong>Mode Tebak</strong>, kamu mengisi sel tabel, mengklik simpul tujuan lompatan, dan memutuskan kapan harus melompat.</p>
    </div>
    <div data-viz="lca"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri soal LCA</b><span>Data berbentuk <strong>pohon</strong> (N − 1 sisi, terhubung) dan banyak pertanyaan tentang dua simpul: jarak, leluhur bersama, rute.</span></div>
        <div class="step-card"><b>BFS dari akar</b><span>Isi <code>depth[v]</code> dan <code>up[0][v]</code> (parent). Parent akar = akar itu sendiri.</span></div>
        <div class="step-card"><b>Isi tabel lompatan</b><span><code>up[j][v] = up[j−1][up[j−1][v]]</code> untuk j = 1..LOG−1.</span></div>
        <div class="step-card"><b>Jawab LCA(u, v)</b><span>Samakan kedalaman per bit, lalu lompat bersama dari j terbesar selama hasilnya berbeda. Jawaban: <code>up[0][u]</code>.</span></div>
        <div class="step-card"><b>Turunkan jawaban lain</b><span>Jarak = <code>depth[u] + depth[v] − 2·depth[lca]</code>.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis binary lifting di C++', 'desc' => 'Program lengkap: BFS, tabel lompatan, fungsi lca, dan jarak di pohon.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> diberikan pohon dengan N simpul (akar 1) dan T pertanyaan <code>u v</code>. Untuk setiap pertanyaan, cetak LCA(u, v) dan jarak antara u dan v.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'LCA dengan binary lifting',
        'steps' => $lcaSteps,
        'sample' => ['input' => "12\n1 2\n2 3\n3 4\n4 5\n5 6\n6 7\n3 8\n8 9\n9 10\n10 11\n1 12\n5\n7 10\n9 4\n12 6\n2 7\n5 5\n", 'output' => "3 7\n3 3\n1 6\n2 5\n5 0\n"],
        'js' => $lcaJs,
        'py' => $lcaPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal">
    <h2>Pola Soal yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Leluhur bersama</h4><p>Silsilah, struktur organisasi, folder komputer. LCA langsung.</p></div>
        <div class="pattern"><h4>Jarak di pohon</h4><p><code>depth[u] + depth[v] − 2·depth[lca]</code>. Versi berbobot: pakai jarak dari akar.</p></div>
        <div class="pattern"><h4>⬆️ Leluhur ke-k</h4><p>"Siapa atasan 5 tingkat di atas karyawan X?" Lompat sesuai bit k.</p></div>
        <div class="pattern"><h4>Apakah leluhur?</h4><p>u leluhur v jika <code>lca(u, v) == u</code>.</p></div>
        <div class="pattern"><h4>Maksimum di rute</h4><p>Simpan juga nilai terbesar di setiap lompatan (<code>mx[j][v]</code>).</p></div>
        <div class="pattern"><h4>Simpul tengah rute</h4><p>Gabungkan jarak dan leluhur ke-k untuk menemukan simpul di tengah jalan u → v.</p></div>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Bagian</th><th>Waktu</th><th>Memori</th></tr>
        <tr><td>Persiapan (BFS + tabel)</td><td><code>O(N log N)</code></td><td><code>O(N log N)</code></td></tr>
        <tr><td>Satu pertanyaan LCA</td><td><code>O(log N)</code></td><td>–</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>LOG terlalu kecil.</strong> Jika 2<sup>LOG</sup> ≤ kedalaman pohon, lompatan tidak cukup jauh dan jawabannya salah pada pohon yang dalam.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Parent akar.</strong> Isi <code>up[j][akar] = akar</code> (atau 0 dengan penanganan khusus). Jika dibiarkan berisi sampah, lompatan yang melewati akar mengakses indeks liar.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'binary lifting lebih jauh', 'desc' => 'Mengapa lompatan serakah benar, leluhur ke-k, pohon berbobot, dan maksimum di rute.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Benar?">
    <h2>Mengapa Lompatan Serakah Itu Benar?</h2>
    <div class="proof">
        <p><strong>Pengamatan.</strong> Setelah kedalaman disamakan, misalkan LCA berada <code>t</code> langkah di atas u (dan v). Untuk lompatan sejauh <code>s</code>: jika <code>s &lt; t</code>, u dan v mendarat di simpul <strong>berbeda</strong>; jika <code>s ≥ t</code>, keduanya mendarat di simpul yang <strong>sama</strong>.</p>
        <p><strong>Akibatnya.</strong> Kita ingin naik tepat <code>t − 1</code> langkah (satu di bawah LCA). Mencoba lompatan 2<sup>LOG−1</sup>, …, 2<sup>1</sup>, 2<sup>0</sup> dan hanya melompat jika hasilnya berbeda sama persis dengan menyusun bilangan <code>t − 1</code> dalam biner dari bit tertinggi. Setiap bit diputuskan sekali, jadi paling banyak LOG lompatan.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2>Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Leluhur ke-k</h3>
        <p>Tabel yang sama langsung menjawab "siapa leluhur v yang k langkah di atasnya?".</p>
    </div>
    @include('lessons.code', ['cpp' => $lcaKth, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. Pohon berbobot</h3>
        <p>Jika setiap sisi punya panjang, simpan <strong>jarak dari akar</strong> (bukan sekadar kedalaman) dalam <code>long long</code>. Kedalaman tetap dipakai untuk mencari LCA, jarak dipakai untuk menghitung jawaban.</p>
    </div>
    @include('lessons.code', ['cpp' => $lcaWeighted, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>3. Informasi di sepanjang rute</h3>
        <p>Binary lifting bisa membawa informasi tambahan, misalnya bobot sisi terbesar di setiap lompatan. Jawaban untuk rute u → v adalah gabungan informasi semua lompatan yang dilakukan saat mencari LCA.</p>
    </div>
    @include('lessons.code', ['cpp' => $lcaMax, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Lompat 2^j langkah sama dengan lompat 2^(j−1) langkah dua kali, jadi up[j][v] = up[j−1][up[j−1][v]].">
        <p class="quiz-q">Rumus mengisi tabel lompatan yang benar adalah…</p>
        <div class="quiz-options">
            <button class="quiz-option"><code>up[j][v] = up[j−1][v] + up[j−1][v]</code></button>
            <button class="quiz-option"><code>up[j][v] = up[j−1][ up[j−1][v] ]</code></button>
            <button class="quiz-option"><code>up[j][v] = up[0][v] × 2</code></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Jika hasil lompatan sama, kita mendarat di LCA atau di atasnya (mungkin kebablasan), jadi lompatan itu dilewati.">
        <p class="quiz-q">Pada tahap naik bersama, kapan u dan v <strong>tidak</strong> melompat?</p>
        <div class="quiz-options">
            <button class="quiz-option">Jika up[j][u] ≠ up[j][v]</button>
            <button class="quiz-option">Jika j genap</button>
            <button class="quiz-option">Jika up[j][u] = up[j][v]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Naik dari u ke LCA lalu turun ke v: (8 − 3) + (6 − 3) = 8 langkah, sama dengan 8 + 6 − 2·3.">
        <p class="quiz-q">depth[u] = 8, depth[v] = 6, dan LCA-nya berkedalaman 3. Berapa jarak u ke v?</p>
        <div class="quiz-options">
            <button class="quiz-option">8</button>
            <button class="quiz-option">14</button>
            <button class="quiz-option">11</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
