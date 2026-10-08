@php
    $sccSteps = [
        ['Membaca graph dan graph terbalik', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n, m;
vector<vector<int>> adj, radj;
vector<int> urutan, komp;
vector<bool> vis;

CPP, <<<'TXT'
<p>Kosaraju membutuhkan dua graph: <code>adj</code> (sisi asli u → v) dan <code>radj</code> (semua sisi dibalik, v → u). Variabel dibuat global agar mudah dipakai oleh fungsi DFS.</p>
TXT],
        ['Tahap 1: DFS dan catat urutan selesai', <<<'CPP'
void dfs1(int u) {
    vis[u] = true;
    for (int v : adj[u])
        if (!vis[v]) dfs1(v);
    urutan.push_back(u);
}

CPP, <<<'TXT'
<p>DFS biasa, tetapi simpul dicatat saat <strong>selesai</strong> (semua yang bisa dicapai darinya sudah dijelajahi), bukan saat dimasuki.</p>
<p>Akibatnya: jika ada sisi dari komponen A ke komponen B (dan tidak sebaliknya), simpul terakhir yang selesai di A pasti selesai <strong>setelah</strong> semua simpul di B.</p>
TXT],
        ['Tahap 2: DFS di graph terbalik', <<<'CPP'
void dfs2(int u, int c) {
    komp[u] = c;
    for (int v : radj[u])
        if (komp[v] == -1) dfs2(v, c);
}

CPP, <<<'TXT'
<p>Di graph terbalik, DFS dari simpul u hanya bisa menjangkau simpul yang di graph asli bisa <strong>mencapai</strong> u. Jika kita mulai dari komponen yang tepat, DFS ini tidak akan "bocor" ke komponen lain, sehingga semua simpul yang dikunjungi membentuk tepat satu komponen kuat.</p>
TXT],
        ['Membaca input', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> m;
    adj.assign(n + 1, {});
    radj.assign(n + 1, {});
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        radj[v].push_back(u);
    }
CPP, <<<'TXT'
<p>Setiap sisi u → v dimasukkan ke <code>adj[u]</code> dan versi terbaliknya ke <code>radj[v]</code>.</p>
TXT],
        ['Jalankan tahap 1 dari semua simpul', <<<'CPP'

    vis.assign(n + 1, false);
    for (int v = 1; v <= n; v++)
        if (!vis[v]) dfs1(v);
    reverse(urutan.begin(), urutan.end());
CPP, <<<'TXT'
<p>Graph mungkin tidak terhubung, jadi DFS dimulai dari setiap simpul yang belum dikunjungi. Setelah dibalik, <code>urutan</code> dimulai dari simpul yang selesai paling akhir, yang berada di komponen "hulu" (tidak ada komponen lain yang masuk ke sana).</p>
TXT],
        ['Jalankan tahap 2 sesuai urutan', <<<'CPP'

    komp.assign(n + 1, -1);
    int c = 0;
    for (int v : urutan)
        if (komp[v] == -1) dfs2(v, ++c);
CPP, <<<'TXT'
<p>Setiap kali menemukan simpul yang belum punya komponen, mulai komponen baru. Komponen diberi nomor 1, 2, 3, … sesuai urutan ditemukan.</p>
<pre>urutan (dibalik): 1 2 3 4 5 6 7 8
dfs2(1) → {1, 3, 2} = K1
dfs2(4) → {4, 6, 5} = K2
dfs2(7) → {7, 8}    = K3</pre>
TXT],
        ['Mencetak hasil', <<<'CPP'

    cout << c << '\n';
    for (int v = 1; v <= n; v++) cout << komp[v] << (v < n ? ' ' : '\n');
    return 0;
}
CPP, <<<'TXT'
<p>Baris pertama: banyak komponen kuat. Baris kedua: nomor komponen setiap simpul. Total kerja dua DFS: <strong>O(N + M)</strong>.</p>
<div class="wt-warn">DFS rekursif bisa sangat dalam untuk N = 10<sup>5</sup>. Di judge dengan stack kecil, tulis versi iteratif atau gunakan Tarjan iteratif.</div>
TXT],
    ];

    $sccJs = <<<'JS'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const radj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  radj[v].push(u);
}
const vis = new Array(n + 1).fill(false), urutan = [];
function dfs1(u) {
  vis[u] = true;
  for (const v of adj[u]) if (!vis[v]) dfs1(v);
  urutan.push(u);
}
for (let v = 1; v <= n; v++) if (!vis[v]) dfs1(v);
urutan.reverse();
const komp = new Array(n + 1).fill(-1);
function dfs2(u, c) {
  komp[u] = c;
  for (const v of radj[u]) if (komp[v] === -1) dfs2(v, c);
}
let c = 0;
for (const v of urutan) if (komp[v] === -1) dfs2(v, ++c);
console.log(c + "\n" + komp.slice(1).join(" "));
JS;

    $sccPy = <<<'PY'
import sys
sys.setrecursionlimit(10000)

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
radj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    radj[v].append(u)

vis = [False] * (n + 1)
urutan = []
def dfs1(u):
    vis[u] = True
    for v in adj[u]:
        if not vis[v]:
            dfs1(v)
    urutan.append(u)

for v in range(1, n + 1):
    if not vis[v]:
        dfs1(v)
urutan.reverse()

komp = [-1] * (n + 1)
def dfs2(u, c):
    komp[u] = c
    for v in radj[u]:
        if komp[v] == -1:
            dfs2(v, c)

c = 0
for v in urutan:
    if komp[v] == -1:
        c += 1
        dfs2(v, c)
print(c)
print(*komp[1:])
PY;

    $sccTarjan = <<<'CPP'
// Algoritma Tarjan: SATU kali DFS, memakai tin/low dan sebuah stack.
int timer = 0, c = 0;
vector<int> tin, low, komp;
vector<bool> diStack;
stack<int> st;

void dfs(int u) {
    tin[u] = low[u] = timer++;
    st.push(u);
    diStack[u] = true;
    for (int v : adj[u]) {
        if (tin[v] == -1) {                 // belum dikunjungi
            dfs(v);
            low[u] = min(low[u], low[v]);
        } else if (diStack[v]) {            // masih di komponen yang sedang dibangun
            low[u] = min(low[u], tin[v]);
        }
    }
    if (low[u] == tin[u]) {                 // u adalah "akar" sebuah komponen
        c++;
        while (true) {
            int x = st.top(); st.pop();
            diStack[x] = false;
            komp[x] = c;
            if (x == u) break;
        }
    }
}
CPP;

    $sccCond = <<<'CPP'
// Graph kondensasi: setiap komponen menjadi satu simpul, hasilnya selalu DAG.
// Contoh: jalur dengan jumlah nilai terbesar, boleh mengunjungi simpul berkali-kali.
vector<long long> nilaiK(c + 1, 0);
for (int v = 1; v <= n; v++) nilaiK[komp[v]] += nilai[v];

vector<vector<int>> dag(c + 1);
for (int u = 1; u <= n; u++)
    for (int v : adj[u])
        if (komp[u] != komp[v]) dag[komp[u]].push_back(komp[v]);

// Kosaraju memberi nomor komponen dalam urutan topologis (hulu → hilir),
// jadi DP cukup dijalankan dari nomor terbesar ke terkecil.
vector<long long> best(c + 1, 0);
for (int k = c; k >= 1; k--) {
    best[k] = nilaiK[k];
    for (int j : dag[k]) best[k] = max(best[k], nilaiK[k] + best[j]);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan arti <strong>terhubung kuat</strong> pada graph berarah.</li>
        <li>Menemukan semua <strong>komponen kuat</strong> (SCC) dengan algoritma Kosaraju dalam O(N + M).</li>
        <li>Membangun <strong>graph kondensasi</strong> yang selalu DAG, lalu menjalankan DP di atasnya.</li>
        <li>Mengenal algoritma Tarjan yang hanya memakai satu DFS.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'terhubung kuat', 'desc' => 'Definisi, contoh, dan mengapa SCC membuat graph berarah menjadi DAG.'])

<section class="lesson-section" id="definisi" data-toc="Terhubung Kuat">
    <h2>Apa Itu Komponen Terhubung Kuat?</h2>
    <div class="prose">
        <p>Pada graph berarah, bisa pergi dari u ke v belum tentu bisa kembali dari v ke u. Dua simpul <strong>terhubung kuat</strong> jika keduanya saling bisa mencapai: ada jalur u ⇝ v <em>dan</em> v ⇝ u.</p>
        <p><strong>Komponen kuat</strong> (<em>Strongly Connected Component</em>, SCC) adalah kelompok terbesar simpul yang semuanya saling terhubung kuat. Setiap simpul berada di tepat satu SCC (simpul sendirian juga dihitung sebagai SCC berukuran 1).</p>
    </div>
    @include('lessons.diagram', [
        'w' => 620, 'h' => 250, 'directed' => true,
        'nodes' => [[1, 60, 70, 'vis'], [2, 160, 40, 'vis'], [3, 150, 170, 'vis'], [4, 290, 190, 'cyan'], [5, 390, 90, 'cyan'], [6, 420, 220, 'cyan'], [7, 520, 150, 'green'], [8, 580, 230, 'green']],
        'edges' => [[1, 2], [2, 3], [3, 1], [3, 4, 'hl'], [4, 5], [5, 6], [6, 4], [6, 7, 'hl'], [7, 8, '', null, 14], [8, 7, '', null, 14]],
        'caption' => 'Tiga komponen kuat: {1, 2, 3}, {4, 5, 6}, dan {7, 8}. Sisi ungu menghubungkan antar komponen dan hanya bisa dilalui satu arah.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Jika setiap SCC diciutkan menjadi satu simpul, graph yang tersisa <strong>tidak punya siklus</strong> (DAG). Inilah alasan SCC sangat berguna: soal pada graph berarah sembarang bisa diubah menjadi soal pada DAG, yang bisa diselesaikan dengan topological sort dan DP.</p>
    </div>
</section>

<section class="lesson-section" id="ide" data-toc="Ide Kosaraju">
    <h2>Ide Algoritma Kosaraju</h2>
    <div class="prose">
        <p>Bayangkan DFS dari simpul 4 di graph atas. Ia bisa mencapai 4, 5, 6, 7, 8, padahal komponen kuatnya hanya {4, 5, 6}. DFS "bocor" ke komponen hilir {7, 8}. Bagaimana mencegahnya?</p>
        <ol>
            <li><strong>Tahap 1:</strong> DFS di graph asli dan catat urutan <em>selesai</em>. Komponen "hulu" ({1, 2, 3}) pasti punya simpul yang selesai paling akhir.</li>
            <li><strong>Tahap 2:</strong> balik semua sisi. Sekarang dari komponen hulu, sisi yang keluar menjadi sisi yang masuk, sehingga DFS <strong>tidak bisa bocor</strong> ke komponen lain. Proses simpul menurut urutan selesai dari yang paling akhir; setiap DFS menemukan tepat satu SCC.</li>
        </ol>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Tahap</th><th>Kejadian</th><th>Hasil</th></tr>
            <tr><td>1</td><td>dfs1(1) → 2 → 3 → 4 → 5 → 6 → 7 → 8</td><td>selesai: 8, 7, 6, 5, 4, 3, 2, 1</td></tr>
            <tr class="hl"><td>1</td><td>dibalik</td><td>urutan: 1, 2, 3, 4, 5, 6, 7, 8</td></tr>
            <tr><td>2</td><td>dfs2(1) di graph terbalik: 1 → 3 → 2 (sisi 3→4 terbalik menjadi 4→3, tidak bisa dipakai dari 3)</td><td>K1 = {1, 2, 3}</td></tr>
            <tr><td>2</td><td>dfs2(4): 4 → 6 → 5 (3 sudah punya komponen)</td><td>K2 = {4, 5, 6}</td></tr>
            <tr class="ok"><td>2</td><td>dfs2(7): 7 → 8</td><td>K3 = {7, 8}</td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi Kosaraju</h2>
    <div class="prose"><p>Tahap 1 mengisi panel <strong>Urutan selesai</strong>. Tahap 2 mewarnai simpul per komponen; garis putus-putus biru adalah sisi asli yang dilalui secara terbalik.</p></div>
    <div data-viz="scc"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali cirinya</b><span>Graph berarah, dan soal bertanya tentang "saling bisa mencapai", "kelompok yang tertutup", atau graph punya siklus yang mengganggu DP.</span></div>
        <div class="step-card"><b>Cari SCC</b><span>Kosaraju (dua DFS) atau Tarjan (satu DFS).</span></div>
        <div class="step-card"><b>Ciutkan</b><span>Bangun DAG antar komponen, gabungkan nilai simpul per komponen.</span></div>
        <div class="step-card"><b>Selesaikan di DAG</b><span>Topological sort, DP jalur terpanjang, hitung komponen tanpa sisi masuk, dan sebagainya.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'Kosaraju di C++', 'desc' => 'Program lengkap: banyak komponen dan nomor komponen setiap simpul.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kosaraju</h2>
    @include('lessons.walkthrough', [
        'title' => 'Strongly Connected Components',
        'steps' => $sccSteps,
        'sample' => ['input' => "8 10\n1 2\n2 3\n3 1\n3 4\n4 5\n5 6\n6 4\n6 7\n7 8\n8 7\n", 'output' => "3\n1 1 1 2 2 2 3 3\n"],
        'js' => $sccJs,
        'py' => $sccPy,
    ])
</section>

<section class="lesson-section" id="kondensasi" data-toc="Graph Kondensasi">
    <h2>Graph Kondensasi + DP</h2>
    <div class="prose">
        <p>Soal klasik: setiap simpul punya nilai. Mulai dari simpul mana saja, berjalan mengikuti sisi berarah (boleh melewati simpul yang sama berkali-kali, tetapi nilainya hanya dihitung sekali). Berapa total nilai terbesar?</p>
        <p>Di dalam satu SCC, kita bisa berputar dan mengambil semua nilai. Jadi ciutkan setiap SCC menjadi satu simpul bernilai jumlah, lalu cari jalur bernilai terbesar di DAG.</p>
    </div>
    @include('lessons.code', ['cpp' => $sccCond, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Algoritma</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Kosaraju</td><td><code>O(N + M)</code></td><td>Dua DFS, butuh graph terbalik</td></tr>
        <tr><td>Tarjan</td><td><code>O(N + M)</code></td><td>Satu DFS, tanpa graph terbalik</td></tr>
        <tr><td>Membangun kondensasi</td><td><code>O(N + M)</code></td><td>Bisa ada sisi ganda antar komponen</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Tahap 2 di graph asli</strong> adalah kesalahan paling umum. DFS kedua harus memakai <code>radj</code>; jika memakai <code>adj</code>, komponen akan bocor dan tergabung.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'Tarjan dan bukti', 'desc' => 'Satu DFS dengan low-link, dan mengapa urutan selesai bekerja.'])

<section class="lesson-section" id="tarjan" data-toc="Algoritma Tarjan">
    <h2>Algoritma Tarjan</h2>
    <div class="prose">
        <p>Tarjan menemukan SCC dalam satu DFS. Setiap simpul diberi <code>tin</code> (waktu masuk) dan <code>low</code> (tin terkecil yang bisa dicapai dari subtree-nya lewat simpul yang masih di stack). Jika setelah semua anak selesai <code>low[u] == tin[u]</code>, berarti tidak ada jalan dari subtree u kembali ke atas u. Semua simpul di stack mulai dari u adalah satu komponen.</p>
    </div>
    @include('lessons.code', ['cpp' => $sccTarjan, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Tarjan memberi nomor komponen dalam urutan topologis <strong>terbalik</strong> (komponen hilir mendapat nomor kecil), sedangkan Kosaraju memberi urutan topologis biasa.</p>
    </div>
</section>

<section class="lesson-section" id="bukti" data-toc="Mengapa Kosaraju Benar?">
    <h2>Mengapa Kosaraju Benar?</h2>
    <div class="proof">
        <p><strong>Lemma.</strong> Jika ada sisi dari komponen A ke komponen B (A ≠ B), maka waktu selesai terbesar di A lebih besar dari waktu selesai terbesar di B.</p>
        <p>Bukti singkat: jika DFS pertama kali masuk ke A, ia akan menjelajahi seluruh B sebelum kembali dan menyelesaikan simpul pertama di A. Jika DFS pertama kali masuk ke B, ia tidak bisa mencapai A (kalau bisa, A dan B akan menjadi satu komponen), jadi B selesai seluruhnya sebelum A dimasuki.</p>
        <p>Maka simpul dengan waktu selesai terbesar berada di komponen tanpa sisi masuk dari komponen lain. Di graph terbalik, komponen itu tidak punya sisi <em>keluar</em>, sehingga dfs2 dari sana hanya menjangkau komponennya sendiri. Setelah komponen itu ditandai, argumen yang sama berlaku untuk sisa graph.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Kondensasi + DP</h4><p>Jalur bernilai terbesar di graph berarah dengan siklus.</p></div>
        <div class="pattern"><h4>Komponen sumber</h4><p>Berapa simpul minimum agar semua terjangkau? Banyak komponen tanpa sisi masuk.</p></div>
        <div class="pattern"><h4>2-SAT</h4><p>Variabel x dan ¬x di SCC yang sama berarti tidak ada solusi.</p></div>
        <div class="pattern"><h4>Membuat terhubung kuat</h4><p>Sisi minimum yang ditambahkan = max(sumber, muara) di DAG kondensasi.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Sisi 1 → 2 saja tidak cukup; 2 harus bisa kembali ke 1. Tanpa sisi lain, setiap simpul adalah komponennya sendiri.">
        <p class="quiz-q">Graph berarah dengan satu sisi 1 → 2. Ada berapa SCC?</p>
        <div class="quiz-options">
            <button class="quiz-option">0</button>
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Jika graph kondensasi punya siklus, semua komponen di siklus itu saling bisa mencapai dan seharusnya menjadi satu komponen. Jadi kondensasi selalu DAG.">
        <p class="quiz-q">Graph kondensasi (setiap SCC menjadi satu simpul) selalu…</p>
        <div class="quiz-options">
            <button class="quiz-option">Tidak punya siklus (DAG)</button>
            <button class="quiz-option">Sebuah pohon</button>
            <button class="quiz-option">Terhubung kuat</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Di graph asli, DFS dari komponen hulu bisa bocor ke komponen hilir. Membalik sisi mencegahnya.">
        <p class="quiz-q">Pada tahap 2 Kosaraju, DFS dijalankan di…</p>
        <div class="quiz-options">
            <button class="quiz-option">Graph asli</button>
            <button class="quiz-option">Graph dengan semua sisi dibalik</button>
            <button class="quiz-option">Graph tak berarah</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
