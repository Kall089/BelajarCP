@php
    $reprSteps = [
        ['Header & namespace', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;
CPP, <<<'TXT'
<p>Dua baris "wajib" di hampir setiap program competitive programming C++:</p>
<ul>
    <li><code>#include &lt;bits/stdc++.h&gt;</code> memasukkan seluruh pustaka standar (<code>vector</code>, <code>sort</code>, <code>queue</code>, …) sekaligus.</li>
    <li><code>using namespace std;</code> agar kita cukup menulis <code>vector</code>, bukan <code>std::vector</code>.</li>
</ul>
TXT],
        ['main() & input cepat', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
CPP, <<<'TXT'
<p>Program dimulai dari <code>main()</code>. Dua baris setelahnya mempercepat <code>cin</code>/<code>cout</code>, penting ketika input berisi ratusan ribu angka.</p>
<div class="wt-tip">Biasakan selalu menulis dua baris ini. Tidak ada ruginya, dan sering menyelamatkan dari Time Limit Exceeded.</div>
TXT],
        ['Membaca banyak simpul & sisi', <<<'CPP'

    int n, m;
    cin >> n >> m;
CPP, <<<'TXT'
<p>Format input graph yang paling umum: baris pertama berisi <code>N M</code> (banyak simpul dan banyak sisi), lalu <code>M</code> baris berisi pasangan <code>u v</code>.</p>
<p>Pada contoh, <code>n = 5</code> dan <code>m = 6</code>.</p>
TXT],
        ['Menyiapkan adjacency list & matrix', <<<'CPP'

    vector<vector<int>> adj(n + 1);
    vector<vector<bool>> mat(n + 1, vector<bool>(n + 1, false));
CPP, <<<'TXT'
<p>Kita membuat <strong>dua</strong> representasi sekaligus agar bisa membandingkannya:</p>
<ul>
    <li><code>adj</code>: <code>n + 1</code> daftar kosong. <code>adj[u]</code> nanti berisi tetangga-tetangga u.</li>
    <li><code>mat</code>: tabel <code>(n + 1) × (n + 1)</code> berisi <code>false</code>. <code>mat[u][v] = true</code> artinya u dan v bertetangga.</li>
</ul>
<p>Ukurannya <code>n + 1</code> karena simpul bernomor 1..n. Indeks 0 sengaja dibiarkan kosong supaya kita tidak perlu repot mengurangi 1.</p>
<div class="wt-warn">Matrix memakan memori <code>n²</code>. Untuk <code>n = 100 000</code> berarti 10<sup>10</sup> sel: program langsung kehabisan memori. Pakai matrix hanya jika n kecil (kira-kira ≤ 2000).</div>
TXT],
        ['Mencatat setiap sisi', <<<'CPP'

    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
        mat[u][v] = mat[v][u] = true;
    }
CPP, <<<'TXT'
<p>Untuk setiap sisi <code>u v</code>:</p>
<ol>
    <li><code>adj[u].push_back(v)</code>: v adalah tetangga u.</li>
    <li><code>adj[v].push_back(u)</code>: u juga tetangga v, karena sisinya <strong>dua arah</strong>.</li>
    <li><code>mat[u][v] = mat[v][u] = true</code>: tandai kedua sel matrix (matrix graph tak berarah selalu simetris).</li>
</ol>
<p>Setelah 6 sisi contoh dibaca:</p>
<pre>adj[1] = {2, 4}      adj[4] = {1, 5}
adj[2] = {1, 3, 5}   adj[5] = {2, 4, 3}
adj[3] = {2, 5}</pre>
<div class="wt-tip">Untuk graph <strong>berarah</strong>, hapus baris <code>adj[v].push_back(u)</code> dan cukup isi <code>mat[u][v]</code>.</div>
TXT],
        ['Mencetak tetangga & derajat', <<<'CPP'

    for (int u = 1; u <= n; u++) {
        sort(adj[u].begin(), adj[u].end());
        cout << u << " (derajat " << adj[u].size() << "):";
        for (int v : adj[u]) cout << ' ' << v;
        cout << '\n';
    }
CPP, <<<'TXT'
<p>Loop setiap simpul dari 1 sampai n:</p>
<ul>
    <li><code>sort(adj[u].begin(), adj[u].end())</code> mengurutkan tetangga dari yang terkecil. Urutan di adjacency list mengikuti urutan input, jadi perlu diurutkan jika soal meminta.</li>
    <li><code>adj[u].size()</code> adalah banyaknya tetangga, yaitu <strong>derajat</strong> simpul u.</li>
    <li><code>for (int v : adj[u])</code> mencetak setiap tetangga didahului spasi.</li>
</ul>
<p>Perhatikan simpul 5: tetangganya tercatat <code>{2, 4, 3}</code>, lalu setelah diurutkan menjadi <code>2 3 4</code>.</p>
TXT],
        ['Menjawab pertanyaan dengan matrix', <<<'CPP'

    int q;
    cin >> q;
    while (q--) {
        int u, v;
        cin >> u >> v;
        cout << (mat[u][v] ? "YA" : "TIDAK") << '\n';
    }
    return 0;
}
CPP, <<<'TXT'
<p>Bagian terakhir input berisi <code>q</code> pertanyaan "apakah u dan v bertetangga?". Di sinilah matrix unggul: jawabannya cukup <code>mat[u][v]</code>, langsung <strong>O(1)</strong>.</p>
<p>Dengan adjacency list kita harus menelusuri <code>adj[u]</code> satu per satu, yaitu <code>O(derajat u)</code>.</p>
<p><code>while (q--)</code> adalah cara singkat mengulang tepat q kali: nilai q dicek lalu dikurangi 1 setiap putaran.</p>
TXT],
    ];

    $reprJs = <<<'JS'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const mat = Array.from({ length: n + 1 }, () => new Array(n + 1).fill(false));

for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
  mat[u][v] = mat[v][u] = true;
}

const out = [];
for (let u = 1; u <= n; u++) {
  adj[u].sort((a, b) => a - b); // urutkan sebagai angka
  out.push(`${u} (derajat ${adj[u].length}):` + adj[u].map((v) => " " + v).join(""));
}

const [q] = readInts();
for (let i = 0; i < q; i++) {
  const [u, v] = readInts();
  out.push(mat[u][v] ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
JS;

    $reprPy = <<<'PY'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
mat = [[False] * (n + 1) for _ in range(n + 1)]

for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)
    mat[u][v] = mat[v][u] = True

for u in range(1, n + 1):
    adj[u].sort()
    print(f"{u} (derajat {len(adj[u])}):", *adj[u])

q = int(input())
for _ in range(q):
    u, v = map(int, input().split())
    print("YA" if mat[u][v] else "TIDAK")
PY;

    $reprWeighted = <<<'CPP'
// Graph BERBOBOT: simpan pasangan {tetangga, bobot}
vector<vector<pair<int, int>>> adj(n + 1);
for (int i = 0; i < m; i++) {
    int u, v, w;
    cin >> u >> v >> w;
    adj[u].push_back({v, w});
    adj[v].push_back({u, w});   // hapus jika berarah
}
for (auto &e : adj[1]) {
    int v = e.first, w = e.second; // tetangga v dengan bobot w
}
CPP;

    $reprEdgeList = <<<'CPP'
// Edge list: cukup simpan semua sisi dalam satu vector
struct Sisi { int u, v, w; };
vector<Sisi> sisi(m);
for (auto &e : sisi) cin >> e.u >> e.v >> e.w;

// Contoh pemakaian: urutkan sisi dari bobot terkecil (dipakai Kruskal/MST)
sort(sisi.begin(), sisi.end(), [](const Sisi &a, const Sisi &b) {
    return a.w < b.w;
});
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali <strong>graph</strong> di balik cerita soal: apa simpulnya, apa sisinya, berarah atau tidak.</li>
        <li>Memahami istilah penting: tetangga, derajat, jalur, siklus, terhubung, pohon.</li>
        <li>Menyimpan graph di C++ dengan <strong>adjacency list</strong> dan <strong>adjacency matrix</strong>, serta tahu kapan memakai yang mana.</li>
        <li>Menangani graph berbobot, edge list, dan graph "tersembunyi" seperti grid.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'apa itu graph?', 'desc' => 'Mengenal simpul, sisi, dan istilah yang akan dipakai di semua materi graph.'])

<section class="lesson-section" id="intuisi" data-toc="Apa itu Graph?">
    <h2>Apa itu Graph?</h2>
    <div class="prose">
        <p>Bayangkan peta jalan di kotamu. Ada <strong>persimpangan</strong>, dan ada <strong>jalan</strong> yang menghubungkannya. Atau daftar pertemanan di media sosial: ada <strong>orang</strong>, dan ada <strong>hubungan pertemanan</strong>. Keduanya bisa digambarkan dengan struktur yang sama, yaitu <strong>graph</strong>.</p>
        <ul>
            <li><strong>Simpul</strong> (<em>vertex/node</em>): benda-bendanya, misalnya kota, orang, atau halaman web.</li>
            <li><strong>Sisi</strong> (<em>edge</em>): hubungan antar benda, misalnya jalan, pertemanan, atau link.</li>
        </ul>
        <p>Banyak soal competitive programming yang "diam-diam" adalah soal graph: labirin, jaringan komputer, jadwal pelajaran, bahkan teka-teki. Kemampuan <strong>mengenali graph</strong> di balik cerita soal adalah langkah pertama.</p>
    </div>
    <figure class="diagram">
        <div class="diagram-grid">
            <div>
                <h5>Tak berarah</h5>
                @include('lessons.diagram', ['bare' => true, 'w' => 260, 'h' => 200, 'nodes' => [[1, 40, 60], [2, 130, 30], [3, 220, 70], [4, 70, 165], [5, 185, 160]], 'edges' => [[1, 2], [1, 4], [2, 3], [2, 5], [4, 5], [3, 5]]])
            </div>
            <div>
                <h5>Berarah</h5>
                @include('lessons.diagram', ['bare' => true, 'directed' => true, 'w' => 260, 'h' => 200, 'nodes' => [[1, 40, 60], [2, 130, 30], [3, 220, 70], [4, 70, 165], [5, 185, 160]], 'edges' => [[1, 2], [4, 1], [2, 3], [2, 5], [4, 5], [5, 3]]])
            </div>
            <div>
                <h5>Berbobot</h5>
                @include('lessons.diagram', ['bare' => true, 'w' => 260, 'h' => 200, 'nodes' => [[1, 40, 60], [2, 130, 30], [3, 220, 70], [4, 70, 165], [5, 185, 160]], 'edges' => [[1, 2, '', 4], [1, 4, '', 7], [2, 3, '', 2], [2, 5, '', 5], [4, 5, '', 1], [3, 5, '', 3]]])
            </div>
        </div>
        <figcaption>Graph yang sama dalam tiga jenis. Pertemanan biasanya <strong>tak berarah</strong>, follow Instagram <strong>berarah</strong>, dan jalan dengan jarak <strong>berbobot</strong>.</figcaption>
    </figure>
</section>

<section class="lesson-section" id="istilah" data-toc="Istilah Penting">
    <h2>Istilah Penting</h2>
    <div class="term-grid">
        <div class="term"><b>Tetangga</b><span>Simpul yang terhubung langsung oleh satu sisi. Pada gambar, tetangga 2 adalah 1, 3, dan 5.</span></div>
        <div class="term"><b>Derajat</b><span>Banyaknya sisi yang menempel pada simpul. Derajat simpul 2 adalah 3.</span></div>
        <div class="term"><b>Jalur (path)</b><span>Urutan simpul yang dihubungkan sisi-sisi berurutan, misalnya 1 → 2 → 5.</span></div>
        <div class="term"><b>Siklus</b><span>Jalur yang kembali ke simpul awalnya, misalnya 2 → 3 → 5 → 2.</span></div>
        <div class="term"><b>Terhubung</b><span>Setiap simpul bisa dicapai dari simpul mana pun.</span></div>
        <div class="term"><b>Komponen</b><span>Kelompok simpul yang saling terhubung. Graph tak terhubung punya beberapa komponen.</span></div>
        <div class="term"><b>Pohon (tree)</b><span>Graph terhubung tanpa siklus. Selalu punya tepat N − 1 sisi.</span></div>
        <div class="term"><b>Derajat masuk/keluar</b><span>Pada graph berarah: banyak panah yang masuk ke / keluar dari simpul.</span></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Konvensi umum di soal: simpul dinomori <code>1..N</code>, lalu ada <code>M</code> baris berisi <code>u v</code> (ditambah bobot <code>w</code> jika berbobot).</p>
    </div>
</section>

<section class="lesson-section" id="menyimpan" data-toc="Dua Cara Menyimpan">
    <h2>Dua Cara Menyimpan Graph</h2>
    <div class="prose">
        <p>Komputer tidak bisa "melihat" gambar. Graph harus disimpan dalam struktur data. Ada dua cara utama, kita pakai graph tak berarah di atas sebagai contoh.</p>
    </div>
    <div class="diagram-grid" style="max-width: 860px; margin-bottom: 18px">
        <div>
            <h5>Adjacency List: daftar tetangga setiap simpul</h5>
            <div class="trace-wrap" style="margin: 0">
                <table class="trace-table">
                    <tr><th>Simpul</th><th>Tetangga</th></tr>
                    <tr><td><b>1</b></td><td class="q">2, 4</td></tr>
                    <tr><td><b>2</b></td><td class="q">1, 3, 5</td></tr>
                    <tr><td><b>3</b></td><td class="q">2, 5</td></tr>
                    <tr><td><b>4</b></td><td class="q">1, 5</td></tr>
                    <tr><td><b>5</b></td><td class="q">2, 3, 4</td></tr>
                </table>
            </div>
        </div>
        <div>
            <h5>Adjacency Matrix: tabel N × N</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(6, auto)">
                <span class="h"></span><span class="h">1</span><span class="h">2</span><span class="h">3</span><span class="h">4</span><span class="h">5</span>
                <span class="h">1</span><span>0</span><span class="ok">1</span><span>0</span><span class="ok">1</span><span>0</span>
                <span class="h">2</span><span class="ok">1</span><span>0</span><span class="ok">1</span><span>0</span><span class="ok">1</span>
                <span class="h">3</span><span>0</span><span class="ok">1</span><span>0</span><span>0</span><span class="ok">1</span>
                <span class="h">4</span><span class="ok">1</span><span>0</span><span>0</span><span>0</span><span class="ok">1</span>
                <span class="h">5</span><span>0</span><span class="ok">1</span><span class="ok">1</span><span class="ok">1</span><span>0</span>
            </div>
        </div>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Pada list, total isinya <code>2 × M = 12</code> angka. Pada matrix, isinya selalu <code>N × N = 25</code> sel walaupun kebanyakan 0. Untuk graph besar yang "jarang" (sedikit sisi), list jauh lebih hemat.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2>Visualisasi: Membangun List & Matrix</h2>
    <div class="prose">
        <p>Putar animasi di bawah dan perhatikan bagaimana setiap sisi dicatat ke <strong>adjacency list</strong> dan <strong>adjacency matrix</strong>. Coba mode <em>Berarah</em>, tombol <em>Acak</em>, atau gambar graph-mu sendiri dengan alat edit di pojok kanan atas.</p>
    </div>
    <div data-viz="graph-repr"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Identifikasi simpul & sisi</b><span>Dari cerita soal: apa yang menjadi simpul? Apa yang menjadi sisi? Apakah berarah? Berbobot?</span></div>
        <div class="step-card"><b>Pilih representasi</b><span>Hampir selalu <code>adjacency list</code>. Matrix hanya cocok jika N kecil (≤ ±2000) dan kamu sering bertanya "apakah u–v terhubung?".</span></div>
        <div class="step-card"><b>Bangun sambil membaca input</b><span>Siapkan N+1 list kosong (indeks 0 tidak dipakai), lalu untuk setiap sisi lakukan <code>push_back</code> ke list yang sesuai.</span></div>
        <div class="step-card"><b>Ingat sisi dua arah</b><span>Untuk graph tak berarah, masukkan ke <code>adj[u]</code> <em>dan</em> <code>adj[v]</code>. Lupa ini adalah bug paling umum!</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menyimpan graph di C++', 'desc' => 'Program lengkap yang membangun adjacency list & matrix, dibahas baris demi baris.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> baca graph tak berarah, cetak tetangga dan derajat setiap simpul (terurut), lalu jawab <code>Q</code> pertanyaan "apakah u dan v bertetangga?" dengan <code>YA</code> atau <code>TIDAK</code>.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Adjacency list & matrix',
        'steps' => $reprSteps,
        'sample' => ['input' => "5 6\n1 2\n1 4\n2 3\n2 5\n4 5\n3 5\n3\n1 2\n1 3\n5 4\n", 'output' => "1 (derajat 2): 2 4\n2 (derajat 3): 1 3 5\n3 (derajat 2): 2 5\n4 (derajat 2): 1 5\n5 (derajat 3): 2 3 4\nYA\nTIDAK\nYA\n"],
        'js' => $reprJs,
        'py' => $reprPy,
    ])
</section>

<section class="lesson-section" id="perbandingan" data-toc="List vs Matrix">
    <h2>List vs Matrix</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Adjacency List</th><th>Adjacency Matrix</th></tr>
        <tr><td>Memori</td><td><code>O(N + M)</code></td><td><code>O(N²)</code></td></tr>
        <tr><td>Cek apakah u–v terhubung</td><td><code>O(deg u)</code></td><td><code>O(1)</code></td></tr>
        <tr><td>Iterasi semua tetangga u</td><td><code>O(deg u)</code></td><td><code>O(N)</code></td></tr>
        <tr><td>Cocok untuk</td><td>Hampir semua soal</td><td>N kecil, graph padat, Floyd-Warshall</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Dengan N = 100 000, matrix butuh 10<sup>10</sup> sel, jauh melebihi memori! Selalu cek batasan N sebelum memilih matrix.</p>
    </div>
</section>

<section class="lesson-section" id="variasi" data-toc="Variasi Input">
    <h2>Variasi Input yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Berarah</h4><p>Cukup <code>adj[u].push_back(v)</code>. Kata kunci: "satu arah", "prasyarat", "follow", "mengirim ke".</p></div>
        <div class="pattern"><h4>Berbobot</h4><p>Simpan pasangan <code>{tetangga, bobot}</code> dengan <code>vector&lt;pair&lt;int,int&gt;&gt;</code>.</p></div>
        <div class="pattern"><h4>Grid</h4><p>Graph tersembunyi: setiap petak adalah simpul, tetangganya 4 arah. Tidak perlu membangun adjacency list.</p></div>
        <div class="pattern"><h4>0️⃣ Indeks dari 0</h4><p>Jika simpul bernomor <code>0..N-1</code>, buat vector berukuran <code>n</code> saja.</p></div>
        <div class="pattern"><h4>Pohon</h4><p>Sering diberikan sebagai <code>N − 1</code> sisi, atau sebagai array <code>parent[i]</code> untuk i = 2..N.</p></div>
        <div class="pattern"><h4>Edge list</h4><p>Simpan sisi apa adanya. Dipakai algoritma yang memproses sisi satu per satu (Kruskal, Bellman-Ford).</p></div>
    </div>
    <div class="prose"><p><strong>Graph berbobot</strong> disimpan sebagai daftar pasangan:</p></div>
    @include('lessons.code', ['cpp' => $reprWeighted, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 3, 'title' => 'sifat-sifat graph & representasi lain', 'desc' => 'Handshaking lemma, edge list, dan cara menghitung kebutuhan memori.'])

<section class="lesson-section" id="bukti" data-toc="Handshaking Lemma">
    <h2>Handshaking Lemma</h2>
    <div class="proof">
        <p><strong>Teorema.</strong> Pada graph tak berarah, jumlah derajat semua simpul selalu sama dengan <code>2 × M</code>.</p>
        <p><strong>Bukti.</strong> Setiap sisi <code>u – v</code> menyumbang tepat 1 ke derajat u dan 1 ke derajat v. Jadi setiap sisi dihitung tepat dua kali saat derajat dijumlahkan.</p>
        <p><strong>Akibatnya.</strong> Banyaknya simpul berderajat ganjil selalu <strong>genap</strong>. Pada graph di atas: 2 + 3 + 2 + 2 + 3 = 12 = 2 × 6, dan ada dua simpul berderajat ganjil (2 dan 5).</p>
    </div>
    <div class="prose">
        <p>Sifat ini sering dipakai untuk memeriksa jawaban dan pada soal "jalur Euler" (melewati setiap sisi tepat sekali): jalur seperti itu hanya ada jika banyak simpul berderajat ganjil adalah 0 atau 2.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2>Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Edge list</h3>
        <p>Kadang kita tidak butuh "siapa tetangga u", melainkan memproses <strong>semua sisi</strong>, misalnya mengurutkannya dari bobot terkecil. Untuk itu cukup simpan sisi dalam satu <code>vector</code> berisi <code>struct</code>:</p>
    </div>
    @include('lessons.code', ['cpp' => $reprEdgeList, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. Menghitung kebutuhan memori</h3>
        <p>Sebelum memilih representasi, hitung dulu. Satu <code>int</code> = 4 byte. Batas memori umumnya 256 MB ≈ 64 juta <code>int</code>.</p>
        <ul>
            <li>Matrix <code>int</code> untuk N = 5000: 25 juta sel × 4 byte = 100 MB. Masih muat, tapi boros.</li>
            <li>Matrix <code>bool</code> (<code>vector&lt;bool&gt;</code> menyimpan 1 bit per sel) untuk N = 20 000: 400 juta bit = 50 MB.</li>
            <li>Adjacency list untuk N = 200 000, M = 200 000: sekitar 400 000 angka saja.</li>
        </ul>
        <h3>3. Graph lengkap</h3>
        <p>Graph tak berarah dengan N simpul punya paling banyak <code>N(N − 1) / 2</code> sisi. Jika soal bilang "setiap pasangan kota terhubung", jangan bangun sisi satu per satu dengan N = 10<sup>5</sup>: itu 5 × 10<sup>9</sup> sisi. Biasanya ada trik matematika yang lebih cerdas.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Graph tak berarah dengan M sisi: setiap sisi masuk ke dua list, jadi totalnya 2M = 2 × 6 = 12 entri.">
        <p class="quiz-q">Graph tak berarah punya 5 simpul dan 6 sisi. Berapa total entri di seluruh adjacency list-nya?</p>
        <div class="quiz-options">
            <button class="quiz-option">5</button>
            <button class="quiz-option">6</button>
            <button class="quiz-option">12</button>
            <button class="quiz-option">25</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Follow di media sosial tidak harus timbal balik (kamu follow artis, artis belum tentu follow kamu), jadi sisinya berarah.">
        <p class="quiz-q">Relasi "follow" di media sosial paling tepat dimodelkan sebagai graph…</p>
        <div class="quiz-options">
            <button class="quiz-option">Tak berarah</button>
            <button class="quiz-option">Berarah</button>
            <button class="quiz-option">Berbobot negatif</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="N = 100 000 membuat matrix berukuran 10^10 sel, jauh melebihi memori. Adjacency list hanya butuh sekitar N + 2M angka.">
        <p class="quiz-q">N = 100 000 dan M = 200 000. Representasi mana yang tepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">Adjacency list</button>
            <button class="quiz-option">Adjacency matrix</button>
            <button class="quiz-option">Keduanya sama saja</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="3" data-explain="Menurut handshaking lemma, jumlah derajat = 2M = 14 adalah bilangan genap, jadi banyak simpul berderajat ganjil harus genap. Tiga simpul berderajat ganjil mustahil.">
        <p class="quiz-q">Sebuah graph punya 7 sisi. Mana yang <strong>mustahil</strong>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Jumlah semua derajat = 14</button>
            <button class="quiz-option">Ada 2 simpul berderajat ganjil</button>
            <button class="quiz-option">Ada 0 simpul berderajat ganjil</button>
            <button class="quiz-option">Ada 3 simpul berderajat ganjil</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
