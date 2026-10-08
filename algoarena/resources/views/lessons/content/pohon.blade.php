@php
    $trSteps = [
        ['Membaca pohon', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<vector<int>> adj;

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
CPP, <<<'TXT'
<p>Pohon dengan N simpul selalu punya tepat <strong>N − 1 sisi</strong>. Sisi disimpan dua arah karena input tidak memberi tahu siapa orang tua siapa.</p>
<p>Contoh: 9 simpul dengan sisi 1–2, 1–3, 2–4, 2–5, 3–6, 5–7, 5–8, 6–9.</p>
TXT],
        ['DFS iteratif dari akar', <<<'CPP'

    vector<int> parent(n + 1, 0), depth(n + 1, 0), urutan;
    vector<int> st = {1};
    parent[1] = -1;
    while (!st.empty()) {
        int u = st.back();
        st.pop_back();
        urutan.push_back(u);
        for (int v : adj[u]) {
            if (v == parent[u]) continue;
            parent[v] = u;
            depth[v] = depth[u] + 1;
            st.push_back(v);
        }
    }
CPP, <<<'TXT'
<p>Kita jadikan simpul 1 sebagai <strong>akar</strong>. Untuk setiap simpul u yang diambil dari stack, semua tetangganya adalah <em>anak</em>, kecuali satu: orang tuanya sendiri.</p>
<ul>
    <li>Pada pohon, kita tidak butuh array <code>visited</code>. Cukup lewati <code>parent[u]</code>, karena tidak ada siklus yang bisa membawa kita kembali ke simpul lain yang sudah dikunjungi.</li>
    <li>Memakai stack sendiri (bukan rekursi) aman untuk pohon berbentuk garis dengan 10<sup>5</sup>–10<sup>6</sup> simpul, yang bisa membuat rekursi kehabisan stack.</li>
</ul>
<p><code>urutan</code> mencatat urutan simpul diambil. Setiap simpul selalu muncul <strong>setelah</strong> orang tuanya.</p>
<div class="wt-tip"><code>parent[1] = -1</code> agar tidak ada tetangga yang tertukar dengan "orang tua" akar.</div>
TXT],
        ['Ukuran subtree dari bawah ke atas', <<<'CPP'

    vector<int> sz(n + 1, 1);
    for (int i = n - 1; i >= 1; i--) {
        int u = urutan[i];
        sz[parent[u]] += sz[u];
    }
CPP, <<<'TXT'
<p><code>sz[u]</code> = banyak simpul di subtree u (u beserta semua keturunannya). Karena setiap anak muncul setelah orang tuanya di <code>urutan</code>, membaca <code>urutan</code> <strong>dari belakang</strong> menjamin semua anak sudah selesai sebelum orang tuanya.</p>
<pre>sz[7] = sz[8] = 1   → sz[5] = 1 + 1 + 1 = 3
sz[4] = 1           → sz[2] = 1 + 1 + 3 = 5
sz[9] = 1 → sz[6] = 2 → sz[3] = 3
sz[1] = 1 + 5 + 3 = 9</pre>
<p>Indeks 0 (akar) dilewati karena akar tidak punya orang tua.</p>
TXT],
        ['BFS untuk mencari simpul terjauh', <<<'CPP'

    vector<int> dist(n + 1);
    auto terjauh = [&](int s) {
        fill(dist.begin(), dist.end(), -1);
        queue<int> q;
        q.push(s);
        dist[s] = 0;
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
        return far;
    };
CPP, <<<'TXT'
<p>Fungsi lambda ini menjalankan BFS dari s dan mengembalikan simpul dengan jarak terbesar. Pada pohon, jalur antara dua simpul selalu <strong>unik</strong>, jadi jarak BFS adalah panjang jalur itu.</p>
TXT],
        ['Diameter dengan dua BFS', <<<'CPP'

    int a = terjauh(1);
    int b = terjauh(a);
    int diameter = dist[b];
CPP, <<<'TXT'
<p><strong>Diameter</strong> adalah jalur terpanjang di pohon. Triknya:</p>
<ol>
    <li>BFS dari simpul mana pun (misalnya 1). Simpul terjauh, <code>a</code>, pasti salah satu ujung diameter.</li>
    <li>BFS lagi dari <code>a</code>. Simpul terjauh <code>b</code> adalah ujung lainnya, dan <code>dist[b]</code> adalah panjang diameter.</li>
</ol>
<pre>terjauh(1) = 7  (jarak 3)
terjauh(7) = 9  (jalur 7–5–2–1–3–6–9, 6 sisi)</pre>
<div class="wt-warn">Trik dua BFS hanya benar untuk <strong>pohon</strong> dengan bobot tak negatif. Untuk graph umum, mencari jalur terpanjang adalah soal yang sangat sulit.</div>
TXT],
        ['Mencetak hasil', <<<'CPP'

    for (int v = 1; v <= n; v++) cout << sz[v] << (v < n ? ' ' : '\n');
    cout << diameter << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Baris pertama: ukuran subtree setiap simpul. Baris kedua: panjang diameter. Seluruh program berjalan dalam <strong>O(N)</strong>.</p>
TXT],
    ];

    $trJs = <<<'JS'
const [n] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}

const parent = new Array(n + 1).fill(0), urutan = [];
parent[1] = -1;
const st = [1];
while (st.length) {
  const u = st.pop();
  urutan.push(u);
  for (const v of adj[u]) if (v !== parent[u]) { parent[v] = u; st.push(v); }
}
const sz = new Array(n + 1).fill(1);
for (let i = n - 1; i >= 1; i--) sz[parent[urutan[i]]] += sz[urutan[i]];

let dist = [];
function terjauh(s) {
  dist = new Array(n + 1).fill(-1);
  dist[s] = 0;
  const q = [s];
  let far = s;
  for (let h = 0; h < q.length; h++) {
    const u = q[h];
    if (dist[u] > dist[far]) far = u;
    for (const v of adj[u]) if (dist[v] === -1) { dist[v] = dist[u] + 1; q.push(v); }
  }
  return far;
}
const a = terjauh(1), b = terjauh(a);
console.log(sz.slice(1).join(" ") + "\n" + dist[b]);
JS;

    $trPy = <<<'PY'
import sys
from collections import deque
input = sys.stdin.readline

n = int(input())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

parent = [0] * (n + 1)
parent[1] = -1
urutan, st = [], [1]
while st:
    u = st.pop()
    urutan.append(u)
    for v in adj[u]:
        if v != parent[u]:
            parent[v] = u
            st.append(v)
sz = [1] * (n + 1)
for u in reversed(urutan[1:]):
    sz[parent[u]] += sz[u]

def terjauh(s):
    dist = [-1] * (n + 1)
    dist[s] = 0
    q = deque([s])
    far = s
    while q:
        u = q.popleft()
        if dist[u] > dist[far]:
            far = u
        for v in adj[u]:
            if dist[v] == -1:
                dist[v] = dist[u] + 1
                q.append(v)
    return far, dist

a, _ = terjauh(1)
b, dist = terjauh(a)
print(*sz[1:])
print(dist[b])
PY;

    $trEuler = <<<'CPP'
// Euler tour: beri nomor saat MASUK (tin) dan saat KELUAR (tout).
// Subtree u = semua simpul v dengan tin[u] <= tin[v] <= tout[u]
// → subtree menjadi RENTANG berurutan, bisa dipakai dengan prefix sum / segment tree.
int timer = 0;
void dfs(int u, int p) {
    tin[u] = timer++;
    for (int v : adj[u]) if (v != p) dfs(v, u);
    tout[u] = timer - 1;
}
bool leluhur(int u, int v) {   // apakah u leluhur v?
    return tin[u] <= tin[v] && tout[v] <= tout[u];
}
CPP;

    $trReroot = <<<'CPP'
// Rerooting: jumlah jarak dari SETIAP simpul ke semua simpul lain, O(N) total.
// 1) Dari akar 1: jawab[1] = jumlah depth semua simpul (satu DFS).
// 2) Pindah akar dari u ke anaknya v:
//    sz[v] simpul menjadi 1 lebih DEKAT, (n - sz[v]) simpul menjadi 1 lebih JAUH.
for (int u : urutan)                 // orang tua selalu diproses sebelum anak
    for (int v : adj[u])
        if (v != parent[u])
            jawab[v] = jawab[u] - sz[v] + (n - sz[v]);
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan sifat-sifat <strong>pohon</strong> dan istilahnya: akar, orang tua, anak, daun, kedalaman, subtree.</li>
        <li>Menjadikan graph pohon sebagai pohon <strong>berakar</strong> dengan DFS, lalu menghitung kedalaman dan ukuran subtree.</li>
        <li>Mencari <strong>diameter</strong> pohon dengan dua kali BFS.</li>
        <li>Memakai Euler tour dan rerooting untuk soal pohon tingkat lanjut.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'mengenal pohon', 'desc' => 'Definisi, istilah, dan mengapa pohon lebih mudah diolah daripada graph umum.'])

<section class="lesson-section" id="definisi" data-toc="Apa Itu Pohon?">
    <h2>Apa Itu Pohon?</h2>
    <div class="prose">
        <p><strong>Pohon</strong> (<em>tree</em>) adalah graph tak berarah yang <strong>terhubung</strong> dan <strong>tidak punya siklus</strong>. Contohnya silsilah keluarga, struktur folder di komputer, atau jaringan pipa yang tidak pernah membentuk lingkaran.</p>
        <p>Empat pernyataan berikut setara. Jika satu benar untuk graph dengan N simpul, yang lain juga benar:</p>
        <ol>
            <li>Graph terhubung dan tidak punya siklus.</li>
            <li>Graph terhubung dan punya tepat <strong>N − 1 sisi</strong>.</li>
            <li>Graph tidak punya siklus dan punya tepat N − 1 sisi.</li>
            <li>Antara setiap dua simpul ada tepat <strong>satu jalur</strong>.</li>
        </ol>
        <p>Sifat nomor 4 yang paling berguna: karena jalurnya unik, "jarak" antara dua simpul tidak pernah ambigu, dan banyak soal yang sulit di graph umum menjadi mudah di pohon.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 560, 'h' => 250,
        'nodes' => [[1, 280, 34, 'cur'], [2, 170, 104], [3, 390, 104], [4, 100, 174, 'green'], [5, 230, 174], [6, 390, 174], [7, 180, 232, 'green'], [8, 280, 232, 'green'], [9, 390, 232, 'green']],
        'edges' => [[1, 2], [1, 3], [2, 4], [2, 5], [3, 6], [5, 7], [5, 8], [6, 9]],
        'notes' => [[330, 30, 'akar (kedalaman 0)'], [470, 108, 'kedalaman 1'], [470, 178, 'kedalaman 2'], [470, 236, 'kedalaman 3']],
        'caption' => 'Pohon dengan 9 simpul dan 8 sisi, berakar di simpul 1. Simpul hijau adalah <b>daun</b> (tidak punya anak). Subtree simpul 5 = {5, 7, 8}.',
    ])
</section>

<section class="lesson-section" id="istilah" data-toc="Istilah Penting">
    <h2>Istilah pada Pohon Berakar</h2>
    <div class="term-grid">
        <div class="term"><b>Akar (root)</b><span>Simpul yang dipilih sebagai puncak. Soal biasanya menyebut akarnya; jika tidak, pilih simpul 1.</span></div>
        <div class="term"><b>Orang tua & anak</b><span>Pada sisi u–v, jika u lebih dekat ke akar, u adalah orang tua v dan v anak u. Setiap simpul kecuali akar punya tepat satu orang tua.</span></div>
        <div class="term"><b>Daun (leaf)</b><span>Simpul yang tidak punya anak.</span></div>
        <div class="term"><b>Kedalaman (depth)</b><span>Banyak sisi dari akar ke simpul itu. Kedalaman akar = 0.</span></div>
        <div class="term"><b>Tinggi (height)</b><span>Kedalaman terbesar di antara semua simpul.</span></div>
        <div class="term"><b>Subtree</b><span>Simpul u beserta semua keturunannya. <code>sz[u]</code> = banyak simpulnya.</span></div>
        <div class="term"><b>Leluhur</b><span>Semua simpul di jalur dari u naik ke akar.</span></div>
        <div class="term"><b>Diameter</b><span>Jalur terpanjang antara dua simpul mana pun.</span></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Input soal biasanya hanya memberi daftar sisi tanpa arah. Langkah pertama hampir selalu sama: <strong>pilih akar, lalu DFS/BFS</strong> untuk mengetahui orang tua, kedalaman, dan urutan simpul.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Untuk pohon di atas, hitung kedalaman saat DFS turun dan ukuran subtree saat DFS kembali naik.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Simpul</th><th>Orang tua</th><th>Anak</th><th>Kedalaman</th><th>sz (ukuran subtree)</th></tr>
            <tr><td>1</td><td>–</td><td>2, 3</td><td>0</td><td>1 + 5 + 3 = <span class="new">9</span></td></tr>
            <tr><td>2</td><td>1</td><td>4, 5</td><td>1</td><td>1 + 1 + 3 = <span class="new">5</span></td></tr>
            <tr><td>3</td><td>1</td><td>6</td><td>1</td><td>1 + 2 = <span class="new">3</span></td></tr>
            <tr class="hl"><td>5</td><td>2</td><td>7, 8</td><td>2</td><td>1 + 1 + 1 = <span class="new">3</span></td></tr>
            <tr><td>6</td><td>3</td><td>9</td><td>2</td><td>1 + 1 = <span class="new">2</span></td></tr>
            <tr><td>4, 7, 8, 9</td><td>2, 5, 5, 6</td><td>– (daun)</td><td>2, 3, 3, 3</td><td><span class="new">1</span></td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Kedalaman dihitung <strong>dari atas ke bawah</strong> (anak = orang tua + 1). Ukuran subtree dihitung <strong>dari bawah ke atas</strong> (orang tua = 1 + jumlah anak). Dua arah ini adalah pola dasar semua DP pada pohon.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: DFS pada Pohon & Diameter</h2>
    <div class="prose">
        <p>Mode <strong>Kedalaman & subtree</strong> menunjukkan DFS rekursif: label <code>d=…</code> muncul saat simpul dimasuki, lalu berubah menjadi <code>sz=…</code> saat semua anaknya selesai. Mode <strong>Diameter</strong> menjalankan dua BFS dan menandai jalur terpanjang.</p>
    </div>
    <div data-viz="tree"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan Soal Pohon</h2>
    <div class="steps">
        <div class="step-card"><b>Simpan sebagai adjacency list</b><span>N − 1 sisi, simpan dua arah.</span></div>
        <div class="step-card"><b>Pilih akar dan jelajahi</b><span>DFS/BFS dari akar, lewati orang tua. Catat parent, depth, dan urutan kunjungan.</span></div>
        <div class="step-card"><b>Hitung dari atas ke bawah</b><span>Nilai yang bergantung pada leluhur: kedalaman, jarak dari akar, jumlah bobot dari akar.</span></div>
        <div class="step-card"><b>Hitung dari bawah ke atas</b><span>Nilai yang bergantung pada keturunan: ukuran subtree, jumlah nilai subtree, tinggi subtree.</span></div>
        <div class="step-card"><b>Pertanyaan dua simpul?</b><span>Butuh leluhur bersama (materi LCA) atau diameter (dua BFS).</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'pohon di C++', 'desc' => 'DFS iteratif, ukuran subtree, dan diameter dalam satu program O(N).'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> diberikan pohon N simpul (akar 1). Cetak ukuran subtree setiap simpul, lalu panjang diameternya.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Subtree & diameter pohon',
        'steps' => $trSteps,
        'sample' => ['input' => "9\n1 2\n1 3\n2 4\n2 5\n3 6\n5 7\n5 8\n6 9\n", 'output' => "9 5 3 1 3 2 1 1 1\n6\n"],
        'js' => $trJs,
        'py' => $trPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Waktu</th></tr>
        <tr><td>DFS/BFS dari akar (parent, depth, urutan)</td><td><code>O(N)</code></td></tr>
        <tr><td>Ukuran subtree semua simpul</td><td><code>O(N)</code></td></tr>
        <tr><td>Diameter (dua BFS)</td><td><code>O(N)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Rekursi terlalu dalam.</strong> Pohon berbentuk garis dengan 2 · 10<sup>5</sup> simpul membuat DFS rekursif sedalam 2 · 10<sup>5</sup> pemanggilan. Di beberapa judge (terutama Windows) stack-nya kecil dan program crash. Gunakan stack manual seperti di atas jika ragu.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa melewati orang tua.</strong> Tanpa <code>if (v == parent[u]) continue;</code>, DFS akan bolak-balik di sisi yang sama tanpa henti.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'teknik pohon lanjutan', 'desc' => 'Bukti trik diameter, Euler tour, dan rerooting.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Dua BFS Benar?">
    <h2>Mengapa Dua BFS Menemukan Diameter?</h2>
    <div class="proof">
        <p>Misalkan diameter sebenarnya adalah jalur x – y, dan BFS dari s menemukan simpul terjauh a. Kita tunjukkan bahwa a juga ujung sebuah diameter.</p>
        <p>Ambil simpul c, tempat jalur dari s ke a "bertemu" jalur x – y (atau titik terdekat jalur x – y dari s). Karena a adalah yang terjauh dari s, <code>dist(c, a) ≥ dist(c, x)</code> dan <code>dist(c, a) ≥ dist(c, y)</code>. Jika tidak, x atau y akan lebih jauh dari s daripada a.</p>
        <p>Maka jalur dari a lewat c ke ujung yang lebih jauh (x atau y) panjangnya paling sedikit sama dengan x – y. Jadi a adalah ujung diameter, dan BFS kedua dari a pasti mencapai ujung lainnya.</p>
    </div>
</section>

<section class="lesson-section" id="euler" data-toc="Euler Tour">
    <h2>Euler Tour: Subtree Menjadi Rentang</h2>
    <div class="prose">
        <p>Beri setiap simpul nomor urut saat DFS <strong>masuk</strong> (<code>tin</code>) dan catat nomor terakhir saat DFS <strong>keluar</strong> (<code>tout</code>). Semua keturunan u mendapat nomor di antara <code>tin[u]</code> dan <code>tout[u]</code>. Akibatnya, "jumlah nilai di subtree u" berubah menjadi "jumlah pada rentang array", yang bisa dijawab dengan prefix sum atau struktur data rentang.</p>
    </div>
    @include('lessons.code', ['cpp' => $trEuler, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="reroot" data-toc="Rerooting">
    <h2>Rerooting: Jawaban untuk Setiap Akar</h2>
    <div class="prose">
        <p>Soal: untuk <strong>setiap</strong> simpul, hitung jumlah jarak ke semua simpul lain. Menjalankan BFS dari setiap simpul butuh O(N²). Dengan <em>rerooting</em>, cukup O(N): hitung jawaban untuk akar 1, lalu "geser" akar ke anaknya satu per satu dan perbarui jawabannya dalam O(1).</p>
    </div>
    @include('lessons.code', ['cpp' => $trReroot, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Atas → bawah</h4><p>Kedalaman, jarak dari akar, nilai yang diwarisi dari leluhur.</p></div>
        <div class="pattern"><h4>Bawah → atas</h4><p>Ukuran subtree, tinggi subtree, jumlah nilai subtree.</p></div>
        <div class="pattern"><h4>Diameter</h4><p>Dua BFS, atau DP: dua tinggi anak terbesar di setiap simpul.</p></div>
        <div class="pattern"><h4>Pusat pohon</h4><p>Simpul tengah dari jalur diameter: meminimalkan jarak terjauh.</p></div>
        <div class="pattern"><h4>Euler tour</h4><p>Subtree → rentang; cek leluhur dalam O(1).</p></div>
        <div class="pattern"><h4>Rerooting</h4><p>Jawaban untuk setiap akar dengan dua kali penjelajahan.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Pohon dengan N simpul selalu punya tepat N − 1 sisi. 100 simpul → 99 sisi.">
        <p class="quiz-q">Sebuah pohon punya 100 simpul. Berapa banyak sisinya?</p>
        <div class="quiz-options">
            <button class="quiz-option">100</button>
            <button class="quiz-option">99</button>
            <button class="quiz-option">Tergantung bentuknya</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Ukuran subtree orang tua bergantung pada ukuran subtree semua anaknya, jadi anak harus selesai lebih dulu: dari bawah ke atas.">
        <p class="quiz-q">Dalam urutan apa ukuran subtree harus dihitung?</p>
        <div class="quiz-options">
            <button class="quiz-option">Dari akar turun ke daun</button>
            <button class="quiz-option">Urutan nomor simpul</button>
            <button class="quiz-option">Dari daun naik ke akar</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Pada pohon tidak ada siklus, jadi satu-satunya simpul yang sudah dikunjungi di antara tetangga u adalah orang tuanya.">
        <p class="quiz-q">Mengapa DFS pada pohon cukup melewati orang tua, tanpa array visited?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena tidak ada siklus, tetangga yang sudah dikunjungi hanya orang tuanya</button>
            <button class="quiz-option">Karena pohon selalu kecil</button>
            <button class="quiz-option">Karena DFS selalu dimulai dari daun</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
