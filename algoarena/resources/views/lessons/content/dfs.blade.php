@php
    $dfsSteps = [
        ['Header & namespace', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;
CPP, <<<'TXT'
<p>Seperti biasa: semua pustaka standar dan <code>using namespace std</code>.</p>
TXT],
        ['Variabel global: graph & penanda komponen', <<<'CPP'

vector<vector<int>> adj;
vector<int> komp;
CPP, <<<'TXT'
<p>Kedua variabel ini ditaruh di luar <code>main()</code> (<strong>global</strong>) agar fungsi <code>dfs</code> di bawah bisa langsung memakainya tanpa harus dikirim sebagai parameter.</p>
<ul>
    <li><code>adj</code>: adjacency list, ukurannya diatur nanti setelah n diketahui.</li>
    <li><code>komp[u]</code>: nomor komponen simpul u. Nilai <code>0</code> berarti u <strong>belum dikunjungi</strong>. Jadi satu array ini berperan ganda: penanda "sudah dikunjungi" sekaligus label kelompok.</li>
</ul>
TXT],
        ['Fungsi DFS rekursif', <<<'CPP'

void dfs(int u, int id) {
    komp[u] = id;
    for (int v : adj[u]) {
        if (komp[v] == 0) {
            dfs(v, id);
        }
    }
}
CPP, <<<'TXT'
<p>Inilah inti DFS, hanya 8 baris:</p>
<ol>
    <li><code>komp[u] = id</code>: tandai u sebagai bagian dari komponen <code>id</code>. Penandaan dilakukan <strong>sebelum</strong> memeriksa tetangga agar u tidak dikunjungi lagi.</li>
    <li>Untuk setiap tetangga <code>v</code> yang belum dikunjungi, <strong>langsung menyelam</strong> ke v dengan memanggil <code>dfs(v, id)</code>.</li>
    <li>Ketika semua tetangga u sudah diperiksa, fungsi selesai dan kita otomatis <strong>kembali</strong> (<em>backtrack</em>) ke simpul yang memanggil u.</li>
</ol>
<p>Pada contoh, <code>dfs(1)</code> memanggil <code>dfs(2)</code>, yang memanggil <code>dfs(3)</code>, yang memanggil <code>dfs(5)</code>. Simpul 5 buntu, jadi kita kembali ke 3, lalu ke 2, lalu ke 1, yang kemudian memanggil <code>dfs(4)</code>.</p>
<div class="wt-tip">Komputer mengingat "harus kembali ke mana" memakai <strong>call stack</strong>. Itulah sebabnya DFS rekursif tidak butuh stack buatan sendiri.</div>
TXT],
        ['main() & membaca graph', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    adj.assign(n + 1, vector<int>());
    komp.assign(n + 1, 0);
CPP, <<<'TXT'
<p>Setelah membaca <code>n</code> dan <code>m</code>, barulah ukuran variabel global diatur:</p>
<ul>
    <li><code>adj.assign(n + 1, vector&lt;int&gt;())</code>: n + 1 daftar tetangga kosong.</li>
    <li><code>komp.assign(n + 1, 0)</code>: semua simpul berlabel 0, artinya belum dikunjungi.</li>
</ul>
TXT],
        ['Membaca sisi', <<<'CPP'

    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
CPP, <<<'TXT'
<p>Graph tak berarah: setiap sisi dicatat di kedua ujungnya. Pada contoh:</p>
<pre>adj[1] = {2, 3, 4}   adj[6] = {7, 8}
adj[2] = {1, 3}      adj[7] = {6, 8}
adj[3] = {1, 2, 5}   adj[8] = {7, 6}
adj[4] = {1}         adj[9] = { }
adj[5] = {3}</pre>
TXT],
        ['Memulai DFS dari setiap simpul yang belum dikunjungi', <<<'CPP'

    int k = 0;
    for (int s = 1; s <= n; s++) {
        if (komp[s] == 0) {
            k++;
            dfs(s, k);
        }
    }
CPP, <<<'TXT'
<p>Satu kali <code>dfs</code> hanya menjangkau simpul-simpul yang <strong>terhubung</strong> dengan titik awalnya. Untuk menemukan semua kelompok, kita coba setiap simpul s dari 1 sampai n:</p>
<ul>
    <li>Jika <code>komp[s]</code> masih 0, s belum tersentuh DFS sebelumnya. Berarti s adalah anggota <strong>komponen baru</strong>: naikkan <code>k</code> lalu jelajahi seluruh komponennya dengan <code>dfs(s, k)</code>.</li>
    <li>Jika <code>komp[s]</code> sudah terisi, s sudah termasuk komponen lain, jadi dilewati.</li>
</ul>
<pre>s = 1 → komponen 1: {1, 2, 3, 5, 4}
s = 2..5 → sudah berlabel, lewati
s = 6 → komponen 2: {6, 7, 8}
s = 9 → komponen 3: {9}</pre>
<div class="wt-tip">Walaupun ada loop di luar dan rekursi di dalam, total kerjanya tetap <code>O(N + M)</code>, karena setiap simpul hanya dikunjungi sekali.</div>
TXT],
        ['Mengelompokkan anggota', <<<'CPP'

    vector<vector<int>> anggota(k + 1);
    for (int u = 1; u <= n; u++) anggota[komp[u]].push_back(u);
CPP, <<<'TXT'
<p>Sekarang setiap simpul sudah berlabel. Kita buat <code>k + 1</code> daftar kosong, lalu masukkan setiap simpul u ke daftar komponennya, <code>anggota[komp[u]]</code>.</p>
<p>Karena u dimasukkan dari 1 sampai n, setiap daftar otomatis sudah terurut.</p>
TXT],
        ['Mencetak hasil', <<<'CPP'

    cout << k << '\n';
    for (int id = 1; id <= k; id++) {
        for (int i = 0; i < (int)anggota[id].size(); i++) {
            cout << anggota[id][i] << (i + 1 < (int)anggota[id].size() ? ' ' : '\n');
        }
    }
    return 0;
}
CPP, <<<'TXT'
<p>Baris pertama: banyaknya komponen. Lalu satu baris untuk setiap komponen berisi anggotanya.</p>
<p>Komponen sudah otomatis terurut menurut anggota terkecilnya, karena komponen diberi nomor saat s (yang terkecil) pertama kali ditemukan.</p>
TXT],
    ];

    $dfsJs = <<<'JS'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}

const komp = new Array(n + 1).fill(0);
function dfs(u, id) {
  komp[u] = id;
  for (const v of adj[u]) {
    if (komp[v] === 0) dfs(v, id);
  }
}

let k = 0;
for (let s = 1; s <= n; s++) {
  if (komp[s] === 0) dfs(s, ++k);
}

const anggota = Array.from({ length: k + 1 }, () => []);
for (let u = 1; u <= n; u++) anggota[komp[u]].push(u);

const out = [String(k)];
for (let id = 1; id <= k; id++) out.push(anggota[id].join(" "));
console.log(out.join("\n"));
JS;

    $dfsPy = <<<'PY'
import sys
sys.setrecursionlimit(10000)
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

komp = [0] * (n + 1)

def dfs(u, id):
    komp[u] = id
    for v in adj[u]:
        if komp[v] == 0:
            dfs(v, id)

k = 0
for s in range(1, n + 1):
    if komp[s] == 0:
        k += 1
        dfs(s, k)

anggota = [[] for _ in range(k + 1)]
for u in range(1, n + 1):
    anggota[komp[u]].append(u)

print(k)
for id in range(1, k + 1):
    print(*anggota[id])
PY;

    $dfsIter = <<<'CPP'
// DFS iteratif: stack buatan sendiri, aman untuk graph yang sangat dalam
vector<bool> seen(n + 1, false);
stack<int> st;
st.push(s);
seen[s] = true;
while (!st.empty()) {
    int u = st.top();
    st.pop();
    for (int v : adj[u]) {
        if (!seen[v]) {
            seen[v] = true;
            st.push(v);
        }
    }
}
CPP;

    $dfsFlood = <<<'CPP'
// Flood fill: tandai seluruh daratan yang terhubung dengan (r, c)
void isi(int r, int c) {
    if (r < 0 || r >= R || c < 0 || c >= C) return;  // keluar peta
    if (grid[r][c] != '#' || seen[r][c]) return;     // bukan daratan / sudah
    seen[r][c] = true;
    isi(r - 1, c);
    isi(r + 1, c);
    isi(r, c - 1);
    isi(r, c + 1);
}
CPP;

    $dfsBip = <<<'CPP'
// Pewarnaan dua warna (bipartite): warna[u] = 0 (belum), 1, atau 2
vector<int> warna;
bool bisa = true;

void dfs(int u, int w) {
    warna[u] = w;
    for (int v : adj[u]) {
        if (warna[v] == 0) dfs(v, 3 - w);   // tetangga diberi warna lawan
        else if (warna[v] == w) bisa = false; // tetangga berwarna sama: gagal
    }
}
// Panggil dfs(s, 1) untuk setiap s yang warnanya masih 0.
CPP;

    $dfsCycle = <<<'CPP'
// Deteksi siklus pada graph BERARAH dengan tiga warna:
// 0 = putih (belum), 1 = abu-abu (sedang di call stack), 2 = hitam (selesai)
vector<int> st;
bool adaSiklus = false;

void dfs(int u) {
    st[u] = 1;
    for (int v : adj[u]) {
        if (st[v] == 1) adaSiklus = true;    // kembali ke simpul yang masih aktif
        else if (st[v] == 0) dfs(v);
    }
    st[u] = 2;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan cara DFS <strong>menyelam sedalam mungkin</strong> lalu <strong>kembali</strong> (backtrack).</li>
        <li>Menulis DFS rekursif di C++ dan memahami peran <strong>call stack</strong>.</li>
        <li>Menghitung dan menandai <strong>komponen terhubung</strong>, termasuk flood fill di grid.</li>
        <li>Memakai DFS untuk <strong>pewarnaan dua warna</strong>, <strong>deteksi siklus</strong>, dan versi iteratifnya.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'memahami cara kerja DFS', 'desc' => 'Analogi labirin, jejak call stack dengan tangan, dan visualisasi interaktif.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Menjelajah Labirin">
    <h2><span class="sec-icon">💡</span> Intuisi: Menjelajah Labirin</h2>
    <div class="prose">
        <p>Bayangkan kamu masuk labirin sambil membawa gulungan benang. Di setiap persimpangan, kamu <strong>pilih satu lorong dan terus masuk</strong> sejauh mungkin. Jika buntu atau semua lorong di depan sudah pernah dilewati, kamu <strong>mundur mengikuti benang</strong> ke persimpangan terakhir dan mencoba lorong lain.</p>
        <p>Itulah <strong>Depth-First Search</strong> (DFS, "penelusuran mendalam"). Berbeda dengan BFS yang menyebar lapis demi lapis, DFS <strong>menyelam dulu</strong>, baru kembali.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 380,
        'nodes' => [[1, 80, 130, 'vis'], [2, 200, 75, 'vis'], [3, 220, 210, 'vis'], [4, 90, 300, 'vis'], [5, 330, 310, 'vis'], [6, 430, 100, 'cyan'], [7, 570, 85, 'cyan'], [8, 510, 215, 'cyan'], [9, 570, 325, 'green']],
        'edges' => [[1, 2, 'hl'], [1, 3, 'bad'], [2, 3, 'hl'], [1, 4, 'hl'], [3, 5, 'hl'], [6, 7, 'cy'], [7, 8, 'cy'], [6, 8, 'bad']],
        'badges' => [1 => '#1', 2 => '#2', 3 => '#3', 5 => '#4', 4 => '#5', 6 => '#1', 7 => '#2', 8 => '#3', 9 => '#1'],
        'caption' => 'DFS menghasilkan tiga <strong>komponen</strong> (ungu, cyan, hijau). Label #k adalah urutan kunjungan di setiap komponen. Garis putus-putus merah adalah sisi yang menutup <strong>siklus</strong>: saat diperiksa, ujungnya sudah dikunjungi.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Rahasia DFS adalah <strong>stack (tumpukan)</strong>: yang masuk terakhir keluar duluan (LIFO). Pada DFS rekursif, stack ini disediakan otomatis oleh komputer sebagai <strong>call stack</strong>.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2><span class="sec-icon">✍️</span> Coba dengan Tangan</h2>
    <div class="prose">
        <p>Jalankan DFS dari simpul 1 pada komponen ungu. Tetangga diperiksa dari nomor terkecil. Perhatikan kolom <strong>call stack</strong>: simpul ditambahkan di atas ketika kita menyelam, dan dibuang dari atas ketika kita kembali.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Langkah</th><th>Aksi</th><th>Call stack (bawah → atas)</th><th>Sudah dikunjungi</th></tr>
            <tr><td>1</td><td>Mulai di <b>1</b></td><td class="q">[1]</td><td>1</td></tr>
            <tr class="hl"><td>2</td><td>1 → <b>2</b> (tetangga pertama 1)</td><td class="q">[1, 2]</td><td>1, 2</td></tr>
            <tr><td>3</td><td>2 → <b>3</b> (1 sudah dikunjungi)</td><td class="q">[1, 2, 3]</td><td>1, 2, 3</td></tr>
            <tr class="hl"><td>4</td><td>3 → <b>5</b> (1 dan 2 sudah)</td><td class="q">[1, 2, 3, 5]</td><td>1, 2, 3, 5</td></tr>
            <tr><td>5</td><td>5 buntu, <span class="new">kembali</span> ke 3</td><td class="q">[1, 2, 3]</td><td>–</td></tr>
            <tr class="hl"><td>6</td><td>3 selesai, <span class="new">kembali</span> ke 2</td><td class="q">[1, 2]</td><td>–</td></tr>
            <tr><td>7</td><td>2 selesai, <span class="new">kembali</span> ke 1</td><td class="q">[1]</td><td>–</td></tr>
            <tr class="hl"><td>8</td><td>1 → <b>4</b> (3 sudah)</td><td class="q">[1, 4]</td><td>1, 2, 3, 5, 4</td></tr>
            <tr><td>9</td><td>4 buntu, kembali ke 1</td><td class="q">[1]</td><td>–</td></tr>
            <tr class="ok"><td>10</td><td>1 selesai</td><td class="q">[ ]</td><td>Komponen selesai ✓</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Urutan kunjungannya 1, 2, 3, 5, 4. Bandingkan dengan BFS yang akan mengunjungi 1, 2, 3, 4, lalu 5. DFS "mengejar" jalur panjang dulu.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2><span class="sec-icon">🎬</span> Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Perhatikan panel <strong>call stack</strong> yang tumbuh saat menyelam dan menyusut saat kembali. Coba mode <strong>Hitung komponen</strong> untuk melihat DFS diulang dari setiap simpul yang belum dikunjungi. Dengan <strong>Mode Tebak</strong>, kamu diminta menebak ke simpul mana DFS menyelam berikutnya.</p>
    </div>
    <div data-viz="dfs"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri soal DFS</b><span>"Berapa kelompok/pulau", "apakah terhubung", "tandai seluruh area", "apakah ada siklus", "bisakah dibagi dua". Jarak tidak penting.</span></div>
        <div class="step-card"><b>Siapkan penanda</b><span>Array <code>visited</code>/<code>komp</code>/<code>warna</code> berukuran N + 1 yang awalnya 0 atau false.</span></div>
        <div class="step-card"><b>Tulis fungsi rekursif</b><span>Tandai u, lalu panggil DFS untuk setiap tetangga yang belum ditandai.</span></div>
        <div class="step-card"><b>Ulangi untuk semua simpul</b><span>Loop s = 1..N: jika s belum ditandai, mulai DFS baru. Setiap DFS baru = satu komponen baru.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis DFS di C++', 'desc' => 'Program pelabelan komponen lengkap, flood fill di grid, dan jebakan rekursi.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2><span class="sec-icon">💻</span> Kode C++ Lengkap: Melabeli Komponen</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> diberikan graph tak berarah. Cetak banyaknya komponen terhubung, lalu anggota setiap komponen (terurut, komponen diurutkan menurut anggota terkecilnya).</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'DFS: melabeli komponen terhubung',
        'steps' => $dfsSteps,
        'sample' => ['input' => "9 8\n1 2\n1 3\n2 3\n1 4\n3 5\n6 7\n7 8\n6 8\n", 'output' => "3\n1 2 3 4 5\n6 7 8\n9\n"],
        'js' => $dfsJs,
        'py' => $dfsPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal DFS">
    <h2><span class="sec-icon">🧠</span> Pola Soal yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>🏝️ Hitung pulau</h4><p>Grid berisi daratan & air. Setiap DFS dari daratan baru = satu pulau. Disebut <b>flood fill</b>.</p></div>
        <div class="pattern"><h4>👥 Komponen</h4><p>"Berapa kelompok", "kelompok terbesar", "berapa jalan minimal agar semua terhubung" (= komponen − 1).</p></div>
        <div class="pattern"><h4>🎨 Dua kubu</h4><p>"Bisakah dibagi dua tim tanpa musuh satu tim?" Warnai bergantian, cek konflik.</p></div>
        <div class="pattern"><h4>🔁 Ada siklus?</h4><p>Tak berarah: tetangga sudah dikunjungi dan bukan parent. Berarah: pakai tiga warna.</p></div>
        <div class="pattern"><h4>🌳 Ukuran subtree</h4><p>Pada pohon: <code>size[u] = 1 + Σ size[anak]</code>, dihitung saat kembali dari rekursi.</p></div>
        <div class="pattern"><h4>🧩 Semua kemungkinan</h4><p>Backtracking (permutasi, sudoku) adalah DFS pada "pohon pilihan".</p></div>
    </div>
    <div class="prose"><p><strong>Template flood fill di grid.</strong> Fungsi rekursif yang langsung berhenti jika keluar peta atau bukan daratan:</p></div>
    @include('lessons.code', ['cpp' => $dfsFlood, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Aspek</th><th>Nilai</th><th>Alasan</th></tr>
        <tr><td>Waktu</td><td><code>O(N + M)</code></td><td>Setiap simpul dikunjungi sekali, setiap sisi diperiksa paling banyak dua kali.</td></tr>
        <tr><td>Memori</td><td><code>O(N + M)</code></td><td>Adjacency list, penanda, dan call stack (sedalam jalur terpanjang).</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Tandai sebelum menyelam.</strong> Jika <code>komp[u]</code> baru diisi setelah loop tetangga, dua simpul bisa saling memanggil tanpa henti dan program crash (stack overflow).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Rekursi terlalu dalam.</strong> Graph berbentuk garis panjang (1–2–3–…–10<sup>6</sup>) membuat call stack sedalam 10<sup>6</sup>. Judge AlgoArena memberi stack 256 MB sehingga aman, tetapi beberapa judge lain hanya 8 MB. Jika ragu, gunakan DFS iteratif (level Lanjut).</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'DFS untuk soal sulit', 'desc' => 'Pewarnaan dua warna, deteksi siklus berarah, dan DFS tanpa rekursi.'])

<section class="lesson-section" id="bipartite" data-toc="Pewarnaan Dua Warna">
    <h2><span class="sec-icon">🎨</span> Pewarnaan Dua Warna (Bipartite)</h2>
    <div class="prose">
        <p>Graph disebut <strong>bipartite</strong> jika simpulnya bisa dibagi dua kelompok sehingga <strong>setiap sisi menghubungkan dua kelompok berbeda</strong>. Contoh soal: membagi siswa menjadi dua tim debat sehingga dua orang yang bermusuhan tidak satu tim.</p>
        <p>Caranya: warnai simpul awal dengan warna 1, lalu setiap tetangga diberi warna lawan (<code>3 − w</code> mengubah 1↔2). Jika suatu saat ada tetangga yang <strong>sudah berwarna sama</strong>, pembagian mustahil.</p>
    </div>
    @include('lessons.code', ['cpp' => $dfsBip, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Fakta penting.</strong> Graph bipartite jika dan hanya jika ia <strong>tidak punya siklus dengan panjang ganjil</strong>. Pada siklus ganjil (misalnya segitiga), warna harus bergantian 1, 2, 1, … dan simpul terakhir pasti bertemu simpul pertama dengan warna sama.</p>
    </div>
</section>

<section class="lesson-section" id="siklus" data-toc="Deteksi Siklus">
    <h2><span class="sec-icon">🔁</span> Deteksi Siklus pada Graph Berarah</h2>
    <div class="prose">
        <p>Pada graph berarah, "tetangga sudah dikunjungi" belum tentu berarti ada siklus (bisa saja simpul itu sudah selesai diproses lewat jalur lain). Kita perlu membedakan tiga keadaan:</p>
        <ul>
            <li><strong>Putih (0):</strong> belum pernah dikunjungi.</li>
            <li><strong>Abu-abu (1):</strong> sedang aktif, masih ada di call stack.</li>
            <li><strong>Hitam (2):</strong> sudah selesai.</li>
        </ul>
        <p>Siklus ada jika dan hanya jika DFS menemukan sisi menuju simpul <strong>abu-abu</strong>, yaitu kembali ke leluhur yang masih aktif.</p>
    </div>
    @include('lessons.code', ['cpp' => $dfsCycle, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="iteratif" data-toc="DFS Tanpa Rekursi">
    <h2><span class="sec-icon">🧰</span> DFS Tanpa Rekursi</h2>
    <div class="prose">
        <p>Untuk menghindari stack overflow, ganti call stack dengan <code>stack&lt;int&gt;</code> buatan sendiri. Urutan kunjungannya sedikit berbeda dari versi rekursif, tetapi untuk soal komponen, pulau, atau keterhubungan hasilnya sama.</p>
    </div>
    @include('lessons.code', ['cpp' => $dfsIter, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧭</span>
        <p>Perhatikan: kode iteratif ini sama persis dengan BFS, hanya <code>queue</code> diganti <code>stack</code>. Perbedaan satu struktur data inilah yang membedakan "melebar" dan "mendalam".</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="DFS memakai stack (LIFO): simpul yang terakhir masuk diproses lebih dulu, sehingga penjelajahan terus menyelam. Pada DFS rekursif, stack ini adalah call stack.">
        <p class="quiz-q">Struktur data apa yang membuat DFS "menyelam" lebih dulu?</p>
        <div class="quiz-options">
            <button class="quiz-option">Queue (FIFO)</button>
            <button class="quiz-option">Stack (LIFO)</button>
            <button class="quiz-option">Priority queue</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Setiap kali loop luar menemukan simpul yang belum dikunjungi, kita menemukan komponen baru. Jadi banyaknya pemanggilan DFS dari loop luar = banyaknya komponen.">
        <p class="quiz-q">Loop <code>for s = 1..N</code> memanggil <code>dfs(s)</code> untuk simpul yang belum dikunjungi sebanyak 4 kali. Artinya…</p>
        <div class="quiz-options">
            <button class="quiz-option">Graph punya 4 siklus</button>
            <button class="quiz-option">Simpul terjauh berjarak 4</button>
            <button class="quiz-option">Graph punya 4 komponen terhubung</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Segitiga adalah siklus ganjil (panjang 3). Siklus ganjil tidak bisa diwarnai bergantian dengan dua warna.">
        <p class="quiz-q">Graph berbentuk segitiga (1–2, 2–3, 3–1). Apakah bisa diwarnai dua warna?</p>
        <div class="quiz-options">
            <button class="quiz-option">Tidak, karena ada siklus ganjil</button>
            <button class="quiz-option">Ya, 1 dan 3 warna merah, 2 biru</button>
            <button class="quiz-option">Ya, semua graph bisa</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
