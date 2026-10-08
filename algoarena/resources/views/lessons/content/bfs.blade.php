@php
    $bfsSteps = [
        ['Header & namespace', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;
CPP, <<<'TXT'
<p><code>#include &lt;bits/stdc++.h&gt;</code> memasukkan <strong>seluruh</strong> pustaka standar C++ sekaligus: <code>vector</code>, <code>queue</code>, <code>algorithm</code>, dan lainnya. Di competitive programming ini praktis karena kita tidak perlu mengingat header satu per satu.</p>
<p><code>using namespace std;</code> membuat kita boleh menulis <code>vector</code> dan <code>cin</code> tanpa awalan <code>std::</code>.</p>
<div class="wt-tip">Header <code>bits/stdc++.h</code> hanya tersedia di compiler GCC (g++). Judge AlgoArena memakai g++, jadi aman dipakai.</div>
TXT],
        ['Fungsi main & input cepat', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
CPP, <<<'TXT'
<p>Program C++ selalu mulai dijalankan dari fungsi <code>main()</code>.</p>
<p>Dua baris berikutnya adalah <strong>jurus input cepat</strong>. <code>ios::sync_with_stdio(false)</code> memutus sinkronisasi <code>cin</code> dengan <code>scanf</code>, dan <code>cin.tie(nullptr)</code> mencegah <code>cout</code> dikosongkan setiap kali kita membaca. Membaca ratusan ribu angka bisa menjadi 5–10× lebih cepat.</p>
<div class="wt-warn">Setelah memakai dua baris ini, jangan mencampur <code>cin</code>/<code>cout</code> dengan <code>scanf</code>/<code>printf</code> dalam satu program.</div>
TXT],
        ['Membaca N, M, S, dan T', <<<'CPP'

    int n, m, s, t;
    cin >> n >> m >> s >> t;
CPP, <<<'TXT'
<p>Baris pertama input berisi empat bilangan: banyak simpul <code>n</code>, banyak sisi <code>m</code>, simpul awal <code>s</code>, dan simpul tujuan <code>t</code>.</p>
<p>Operator <code>&gt;&gt;</code> membaca bilangan satu per satu, tidak peduli dipisah spasi atau baris baru. Pada contoh input di bawah, hasilnya <code>n = 6</code>, <code>m = 7</code>, <code>s = 1</code>, <code>t = 6</code>.</p>
TXT],
        ['Membangun adjacency list', <<<'CPP'

    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
CPP, <<<'TXT'
<p><code>vector&lt;vector&lt;int&gt;&gt; adj(n + 1)</code> membuat <code>n + 1</code> daftar kosong. <code>adj[u]</code> akan berisi semua tetangga simpul <code>u</code>. Ukurannya <code>n + 1</code> karena simpul bernomor 1 sampai n, sehingga indeks 0 tidak dipakai.</p>
<p>Setiap sisi <code>u v</code> dicatat <strong>dua kali</strong>: v masuk ke daftar u, dan u masuk ke daftar v, karena jalannya dua arah. Setelah semua sisi contoh dibaca:</p>
<pre>adj[1] = {2, 3}       adj[4] = {2, 3, 5}
adj[2] = {1, 4}       adj[5] = {4, 6, 3}
adj[3] = {1, 4, 5}    adj[6] = {5}</pre>
<div class="wt-warn">Lupa menulis <code>adj[v].push_back(u)</code> adalah bug paling umum. Graph-nya jadi satu arah dan BFS tidak menemukan sebagian simpul.</div>
TXT],
        ['Menyiapkan dist, parent, dan antrian', <<<'CPP'

    vector<int> dist(n + 1, -1);
    vector<int> parent(n + 1, 0);
    queue<int> q;
CPP, <<<'TXT'
<ul>
    <li><code>dist[v]</code> menyimpan jarak (jumlah sisi) dari s ke v. Nilai <code>-1</code> berarti v <strong>belum ditemukan</strong>, jadi array ini sekaligus menjadi penanda "sudah dikunjungi".</li>
    <li><code>parent[v]</code> menyimpan simpul sebelum v pada rute terpendek. Nanti dipakai untuk menyusun rutenya.</li>
    <li><code>queue&lt;int&gt; q</code> adalah antrian FIFO: <code>push</code> menaruh di belakang, <code>front</code> melihat yang paling depan, dan <code>pop</code> membuang yang paling depan.</li>
</ul>
TXT],
        ['Masukkan simpul awal', <<<'CPP'

    dist[s] = 0;
    q.push(s);
CPP, <<<'TXT'
<p>Jarak s ke dirinya sendiri adalah 0. Simpul s menjadi satu-satunya isi antrian. Dari sinilah "riak air" mulai menyebar.</p>
<pre>dist  = [-, 0, -1, -1, -1, -1, -1]   (indeks 1..6)
antrian = [1]</pre>
TXT],
        ['Ambil simpul paling depan', <<<'CPP'

    while (!q.empty()) {
        int u = q.front();
        q.pop();
CPP, <<<'TXT'
<p>Selama antrian belum kosong, ambil simpul paling depan sebagai <code>u</code>, lalu buang dari antrian.</p>
<p>Karena antrian bersifat FIFO, simpul yang ditemukan lebih dulu (jaraknya lebih kecil) selalu diproses lebih dulu. Itulah sebabnya BFS bergerak lapis demi lapis.</p>
<div class="wt-warn"><code>q.front()</code> hanya <em>melihat</em> isi terdepan. Tanpa <code>q.pop()</code>, simpul yang sama terus diambil dan program tidak pernah berhenti.</div>
TXT],
        ['Periksa semua tetangga', <<<'CPP'

        for (int v : adj[u]) {
            if (dist[v] == -1) {
                dist[v] = dist[u] + 1;
                parent[v] = u;
                q.push(v);
            }
        }
    }
CPP, <<<'TXT'
<p><code>for (int v : adj[u])</code> menelusuri setiap tetangga <code>v</code> dari <code>u</code>. Jika <code>dist[v]</code> masih -1, berarti v baru pertama kali ditemukan:</p>
<ol>
    <li>Jaraknya satu lebih jauh dari u: <code>dist[v] = dist[u] + 1</code>.</li>
    <li>Catat bahwa kita datang dari u: <code>parent[v] = u</code>.</li>
    <li>Masukkan v ke belakang antrian agar tetangganya nanti ikut diperiksa.</li>
</ol>
<p>Jika <code>dist[v]</code> sudah terisi, v dilewati. Pemeriksaan ini mencegah simpul diproses dua kali dan mencegah perulangan tanpa akhir pada graph yang punya siklus.</p>
<div class="wt-tip">Pada contoh, saat <code>u = 3</code>: tetangga 1 dan 4 dilewati (sudah ditemukan), lalu 5 ditemukan dengan <code>dist[5] = 2</code> dan <code>parent[5] = 3</code>.</div>
TXT],
        ['Jika tujuan tidak terjangkau', <<<'CPP'

    if (dist[t] == -1) {
        cout << -1 << '\n';
        return 0;
    }
CPP, <<<'TXT'
<p>Setelah BFS selesai, jika <code>dist[t]</code> masih -1, tidak ada jalan dari s ke t (graph-nya tidak terhubung). Cetak -1 dan hentikan program dengan <code>return 0</code>.</p>
<div class="wt-tip">Pakai <code>'\n'</code>, bukan <code>endl</code>. <code>endl</code> memaksa output dikirim saat itu juga sehingga lambat jika dipanggil ratusan ribu kali.</div>
TXT],
        ['Menyusun rute lewat parent', <<<'CPP'

    vector<int> path;
    for (int v = t; v != s; v = parent[v]) path.push_back(v);
    path.push_back(s);
    reverse(path.begin(), path.end());
CPP, <<<'TXT'
<p>Rute disusun <strong>mundur</strong> dari t: t → parent[t] → parent[parent[t]] → … sampai tiba di s. Karena urutannya terbalik, rute dibalik di akhir dengan <code>reverse</code>.</p>
<pre>v = 6 → simpan 6, lalu v = parent[6] = 5
v = 5 → simpan 5, lalu v = parent[5] = 3
v = 3 → simpan 3, lalu v = parent[3] = 1 = s, berhenti
path = {6, 5, 3} + {1}  →  dibalik  →  {1, 3, 5, 6}</pre>
TXT],
        ['Mencetak jawaban', <<<'CPP'

    cout << dist[t] << '\n';
    for (int i = 0; i < (int)path.size(); i++) {
        cout << path[i] << (i + 1 < (int)path.size() ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Baris pertama berisi panjang rute <code>dist[t]</code>. Baris kedua berisi simpul-simpul pada rute, dipisah spasi.</p>
<p>Ekspresi <code>(i + 1 &lt; size ? ' ' : '\n')</code> mencetak spasi di antara angka dan baris baru setelah angka terakhir.</p>
<div class="wt-tip"><code>(int)path.size()</code> mengubah ukuran vector (bertipe <code>size_t</code>, tanpa tanda) menjadi <code>int</code>, agar perbandingan dengan <code>i</code> aman dan tidak memicu warning.</div>
TXT],
    ];

    $bfsJs = <<<'JS'
const [n, m, s, t] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}

const dist = new Array(n + 1).fill(-1);
const parent = new Array(n + 1).fill(0);
const queue = [s];
let head = 0; // indeks depan antrian (lebih cepat daripada shift())
dist[s] = 0;

while (head < queue.length) {
  const u = queue[head++];
  for (const v of adj[u]) {
    if (dist[v] === -1) {
      dist[v] = dist[u] + 1;
      parent[v] = u;
      queue.push(v);
    }
  }
}

if (dist[t] === -1) {
  console.log(-1);
} else {
  const path = [];
  for (let v = t; v !== s; v = parent[v]) path.push(v);
  path.push(s);
  path.reverse();
  console.log(dist[t]);
  console.log(path.join(" "));
}
JS;

    $bfsPy = <<<'PY'
import sys
from collections import deque
input = sys.stdin.readline

n, m, s, t = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

dist = [-1] * (n + 1)
parent = [0] * (n + 1)
dist[s] = 0
q = deque([s])
while q:
    u = q.popleft()
    for v in adj[u]:
        if dist[v] == -1:
            dist[v] = dist[u] + 1
            parent[v] = u
            q.append(v)

if dist[t] == -1:
    print(-1)
else:
    path = []
    v = t
    while v != s:
        path.append(v)
        v = parent[v]
    path.append(s)
    path.reverse()
    print(dist[t])
    print(*path)
PY;

    $bfsGrid = <<<'CPP'
// BFS di grid: setiap petak (r, c) adalah simpul dengan 4 tetangga
const int dr[4] = {-1, 1, 0, 0};
const int dc[4] = {0, 0, -1, 1};

vector<vector<int>> dist(R, vector<int>(C, -1));
queue<pair<int, int>> q;
dist[sr][sc] = 0;
q.push({sr, sc});
while (!q.empty()) {
    int r = q.front().first, c = q.front().second;
    q.pop();
    for (int k = 0; k < 4; k++) {
        int nr = r + dr[k], nc = c + dc[k];
        if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue;   // keluar peta
        if (grid[nr][nc] == '#' || dist[nr][nc] != -1) continue; // tembok / sudah
        dist[nr][nc] = dist[r][c] + 1;
        q.push({nr, nc});
    }
}
CPP;

    $bfsMulti = <<<'CPP'
// Multi-source BFS: semua sumber masuk antrian di awal dengan jarak 0
vector<int> dist(n + 1, -1);
queue<int> q;
for (int src : sumber) {
    dist[src] = 0;
    q.push(src);
}
// ...lalu loop BFS persis sama seperti biasa.
// dist[v] = jarak v ke sumber TERDEKAT.
CPP;

    $bfs01 = <<<'CPP'
// 0-1 BFS: sisi berbobot 0 atau 1, pakai deque
vector<int> dist(n + 1, INT_MAX);
deque<int> dq;
dist[s] = 0;
dq.push_back(s);
while (!dq.empty()) {
    int u = dq.front();
    dq.pop_front();
    for (auto &e : adj[u]) {          // e.first = tujuan, e.second = bobot (0/1)
        int v = e.first, w = e.second;
        if (dist[u] + w < dist[v]) {
            dist[v] = dist[u] + w;
            if (w == 0) dq.push_front(v);  // bobot 0: sama dekatnya, taruh di depan
            else dq.push_back(v);          // bobot 1: satu lapis lebih jauh
        }
    }
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan cara BFS menjelajah graph <strong>lapis demi lapis</strong> dan mengapa ia memakai antrian.</li>
        <li>Menghitung <strong>jarak terpendek</strong> (jumlah sisi) dari satu simpul ke semua simpul lain.</li>
        <li>Menulis BFS lengkap dalam <strong>C++</strong>, termasuk menyusun rutenya dengan array <code>parent</code>.</li>
        <li>Memakai BFS di grid, <strong>multi-source BFS</strong>, BFS pada ruang keadaan, dan <strong>0-1 BFS</strong>.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'memahami cara kerja BFS', 'desc' => 'Mulai dari analogi sederhana, coba jalankan dengan tangan, lalu lihat algoritmanya bergerak.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Riak Air">
    <h2><span class="sec-icon">💡</span> Intuisi: Riak Air</h2>
    <div class="prose">
        <p>Lemparkan batu ke kolam. Riaknya menyebar <strong>melingkar</strong>: mula-mula ke titik terdekat, lalu ke lingkaran berikutnya, dan seterusnya. <strong>Breadth-First Search</strong> (BFS, "penelusuran melebar") menjelajahi graph dengan cara yang persis sama.</p>
        <ul>
            <li><strong>Lapis 0:</strong> simpul awal.</li>
            <li><strong>Lapis 1:</strong> semua tetangga simpul awal.</li>
            <li><strong>Lapis 2:</strong> tetangga dari lapis 1 yang belum dikunjungi, dan seterusnya.</li>
        </ul>
        <p>Karena dijelajahi lapis demi lapis, saat sebuah simpul <strong>pertama kali</strong> ditemukan, kita pasti menemukannya lewat <strong>jalur terpendek</strong> (dihitung dalam jumlah sisi).</p>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 380,
        'nodes' => [[1, 80, 200, 'green'], [2, 210, 100, 'cyan'], [3, 210, 300, 'cyan'], [4, 350, 70, 'vis'], [5, 350, 200, 'vis'], [6, 350, 330, 'vis'], [7, 480, 120, 'cur'], [8, 570, 260, 'cur']],
        'edges' => [[1, 2, 'hl'], [1, 3, 'hl'], [2, 4, 'hl'], [2, 5, 'hl'], [3, 5], [3, 6, 'hl'], [4, 7, 'hl'], [5, 7], [6, 8, 'hl'], [7, 8]],
        'badges' => [1 => 'd=0', 2 => 'd=1', 3 => 'd=1', 4 => 'd=2', 5 => 'd=2', 6 => 'd=2', 7 => 'd=3', 8 => 'd=3'],
        'notes' => [[80, 24, 'Lapis 0'], [210, 24, 'Lapis 1'], [350, 24, 'Lapis 2'], [525, 24, 'Lapis 3']],
        'caption' => 'BFS dari simpul 1. Warna menunjukkan lapis, angka d adalah jarak. Garis ungu membentuk <strong>pohon BFS</strong>: setiap simpul tergantung pada simpul yang menemukannya.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Rahasia BFS adalah <strong>antrian (queue)</strong>: yang masuk duluan keluar duluan (FIFO, <em>First In First Out</em>). Simpul lapis 1 pasti selesai diproses sebelum simpul lapis 2 mulai diproses.</p>
    </div>
</section>

<section class="lesson-section" id="istilah" data-toc="Bahan-bahan BFS">
    <h2><span class="sec-icon">🧩</span> Bahan-bahan BFS</h2>
    <div class="term-grid">
        <div class="term"><b>Antrian (queue)</b><span>Tempat simpul yang sudah ditemukan tetapi tetangganya belum diperiksa. Diproses dari depan.</span></div>
        <div class="term"><b>dist[v]</b><span>Jarak dari simpul awal ke v. Diisi -1 (atau ∞) jika belum ditemukan.</span></div>
        <div class="term"><b>Ditemukan</b><span>Simpul yang sudah punya jarak dan sudah masuk antrian. Tidak boleh dimasukkan lagi.</span></div>
        <div class="term"><b>parent[v]</b><span>Simpul yang menemukan v. Dipakai untuk menyusun rute terpendek.</span></div>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2><span class="sec-icon">✍️</span> Coba dengan Tangan</h2>
    <div class="prose">
        <p>Sebelum melihat kode, jalankan BFS dari simpul 1 pada graph di atas memakai pensil. Tetangga diperiksa dari nomor terkecil. Perhatikan kolom antrian: simpul selalu <strong>masuk di belakang</strong> dan <strong>keluar dari depan</strong>.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Langkah</th><th>Ambil u</th><th>Tetangga baru</th><th>Antrian sesudahnya</th><th>dist yang terisi</th></tr>
            <tr><td>0</td><td>–</td><td>1 (simpul awal)</td><td class="q">[1]</td><td><span class="new">dist[1] = 0</span></td></tr>
            <tr class="hl"><td>1</td><td><b>1</b></td><td>2, 3</td><td class="q">[2, 3]</td><td><span class="new">dist[2] = dist[3] = 1</span></td></tr>
            <tr><td>2</td><td><b>2</b></td><td>4, 5</td><td class="q">[3, 4, 5]</td><td><span class="new">dist[4] = dist[5] = 2</span></td></tr>
            <tr class="hl"><td>3</td><td><b>3</b></td><td>6 (5 sudah ditemukan)</td><td class="q">[4, 5, 6]</td><td><span class="new">dist[6] = 2</span></td></tr>
            <tr><td>4</td><td><b>4</b></td><td>7</td><td class="q">[5, 6, 7]</td><td><span class="new">dist[7] = 3</span></td></tr>
            <tr class="hl"><td>5</td><td><b>5</b></td><td>– (2, 3, 7 sudah)</td><td class="q">[6, 7]</td><td>–</td></tr>
            <tr><td>6</td><td><b>6</b></td><td>8</td><td class="q">[7, 8]</td><td><span class="new">dist[8] = 3</span></td></tr>
            <tr class="hl"><td>7</td><td><b>7</b></td><td>–</td><td class="q">[8]</td><td>–</td></tr>
            <tr class="ok"><td>8</td><td><b>8</b></td><td>–</td><td class="q">[ ]</td><td>Antrian kosong, selesai ✓</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Lihat langkah 3: simpul 5 adalah tetangga 3, tetapi sudah ditemukan lewat 2 dengan jarak yang sama. BFS <strong>melewatinya</strong>. Isi antrian juga selalu berpola "jarak d, …, d, d+1, …, d+1". Pola inilah yang menjamin jaraknya terpendek.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2><span class="sec-icon">🎬</span> Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Tekan <strong>▶ Putar</strong> atau maju langkah demi langkah. Perhatikan isi <strong>antrian</strong>, angka <strong>jarak</strong> di atas simpul, dan garis ungu pohon BFS. Klik simpul mana pun untuk mengganti titik awal, atau ubah graph-nya dengan alat edit.</p>
        <p>Aktifkan <strong>Mode Tebak</strong> untuk menguji diri: kamu akan diminta menebak simpul mana yang keluar dari antrian berikutnya dan berapa jaraknya.</p>
    </div>
    <div data-viz="bfs"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri soal BFS</b><span>"Langkah minimum", "jarak terdekat", "berapa lapis", dan setiap langkah bernilai <strong>sama</strong> (bobot 1).</span></div>
        <div class="step-card"><b>Tentukan simpul dan sisinya</b><span>Simpul bisa berupa kota, petak grid, atau keadaan (misalnya posisi bidak catur). Sisi adalah satu langkah yang diperbolehkan.</span></div>
        <div class="step-card"><b>Siapkan dist & antrian</b><span>Isi <code>dist</code> dengan -1, set <code>dist[s] = 0</code>, lalu masukkan s ke antrian.</span></div>
        <div class="step-card"><b>Ulangi sampai antrian kosong</b><span>Ambil u dari depan. Untuk setiap tetangga v dengan <code>dist[v] = -1</code>: isi <code>dist[v] = dist[u] + 1</code> lalu masukkan v ke antrian.</span></div>
        <div class="step-card"><b>Baca jawaban</b><span><code>dist[t]</code> adalah jarak terpendek ke t. Nilai -1 berarti t tidak terjangkau.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis BFS di C++', 'desc' => 'Program lengkap dibedah baris demi baris, lalu pola soal yang sering muncul.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2><span class="sec-icon">💻</span> Kode C++ Lengkap: Jarak & Rute Terpendek</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> diberikan graph tak berarah dengan <code>n</code> simpul dan <code>m</code> sisi. Cetak jarak terpendek dari <code>s</code> ke <code>t</code> beserta rutenya, atau <code>-1</code> jika t tidak terjangkau.</p>
        <p>Klik <strong>Berikutnya</strong> untuk membaca penjelasan setiap bagian. Baris kode yang sedang dibahas akan menyala.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'BFS: jarak & rute terpendek',
        'steps' => $bfsSteps,
        'sample' => ['input' => "6 7 1 6\n1 2\n1 3\n2 4\n3 4\n4 5\n5 6\n3 5\n", 'output' => "3\n1 3 5 6\n"],
        'js' => $bfsJs,
        'py' => $bfsPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal BFS">
    <h2><span class="sec-icon">🧠</span> Pola Soal yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>📏 Jarak tak berbobot</h4><p>"Berapa langkah minimum…", "jarak pertemanan". <b>Langsung BFS</b> dari titik awal.</p></div>
        <div class="pattern"><h4>🧱 Grid / labirin</h4><p>Setiap petak adalah simpul, tetangganya atas-bawah-kiri-kanan. Periksa batas dan tembok.</p></div>
        <div class="pattern"><h4>🦠 Banyak sumber</h4><p>"Jarak ke api / virus terdekat". Masukkan <b>semua sumber</b> ke antrian sejak awal.</p></div>
        <div class="pattern"><h4>♞ Ruang keadaan</h4><p>Simpul = keadaan (posisi kuda, isi ember, kunci yang dimiliki). Sisi = satu aksi.</p></div>
        <div class="pattern"><h4>🎨 Dua warna</h4><p>"Bisakah dibagi dua kelompok?" Warnai lapis genap & ganjil, cek sisi yang warnanya sama.</p></div>
        <div class="pattern"><h4>⚖️ Bobot 0 atau 1</h4><p>Gunakan <b>0-1 BFS</b> dengan deque, bukan Dijkstra. Lihat level Lanjut.</p></div>
    </div>
    <div class="prose">
        <p><strong>Template BFS di grid.</strong> Simpan arah gerak di array <code>dr</code> dan <code>dc</code> supaya keempat tetangga cukup diperiksa dengan satu loop:</p>
    </div>
    @include('lessons.code', ['cpp' => $bfsGrid, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Aspek</th><th>Nilai</th><th>Alasan</th></tr>
        <tr><td>Waktu</td><td><code>O(N + M)</code></td><td>Setiap simpul masuk antrian sekali, setiap sisi diperiksa sekali (dua kali jika tak berarah).</td></tr>
        <tr><td>Memori</td><td><code>O(N + M)</code></td><td>Adjacency list, array dist, dan antrian.</td></tr>
        <tr><td>Grid R × C</td><td><code>O(R · C)</code></td><td>Setiap petak punya paling banyak 4 tetangga.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Tandai saat dimasukkan, bukan saat dikeluarkan.</strong> Jika <code>dist[v]</code> baru diisi ketika v diambil dari antrian, simpul yang sama bisa masuk antrian berkali-kali dan program menjadi sangat lambat.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>BFS hanya menjamin jarak terpendek jika <strong>semua sisi berbobot sama</strong>. Untuk bobot berbeda-beda, gunakan Dijkstra.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'teknik BFS untuk soal sulit', 'desc' => 'Bukti kebenaran, BFS banyak sumber, ruang keadaan, dan 0-1 BFS.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa BFS Benar?">
    <h2><span class="sec-icon">🔬</span> Mengapa Jaraknya Pasti Terpendek?</h2>
    <div class="proof">
        <p><strong>Sifat antrian.</strong> Di setiap saat, jarak simpul di dalam antrian selalu berbentuk <code>d, d, …, d, d+1, …, d+1</code>: tidak turun, dan selisih yang terdepan dengan yang terbelakang paling banyak 1.</p>
        <p><strong>Alasannya.</strong> Awalnya antrian hanya berisi s (jarak 0). Setiap kali kita mengambil u berjarak d dari depan, simpul baru yang kita masukkan berjarak d + 1, dan ditaruh di belakang. Polanya tetap terjaga.</p>
        <p><strong>Akibatnya.</strong> Simpul diambil dari antrian dalam urutan jarak yang tidak turun. Saat v pertama kali ditemukan dari u (jarak d), semua simpul berjarak kurang dari d sudah selesai diproses, dan tidak satu pun dari mereka bertetangga dengan v. Jadi tidak ada jalur ke v yang lebih pendek dari <code>d + 1</code>.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2><span class="sec-icon">🚀</span> Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Multi-source BFS</h3>
        <p>Soal seperti "berapa menit sampai virus menyebar ke semua kota?" atau "jarak setiap petak ke api terdekat" punya <strong>banyak titik awal</strong>. Menjalankan BFS dari setiap sumber satu per satu terlalu lambat (O(K · (N + M))). Triknya: masukkan <strong>semua sumber</strong> ke antrian dengan jarak 0 sekaligus. Bayangkan ada satu "simpul super" yang terhubung ke semua sumber.</p>
    </div>
    @include('lessons.code', ['cpp' => $bfsMulti, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. BFS pada ruang keadaan</h3>
        <p>Simpul tidak harus berupa titik pada peta. Pada soal "langkah minimum kuda catur dari A ke B", simpulnya adalah <strong>posisi kuda</strong> <code>(r, c)</code> dan sisinya adalah 8 gerakan huruf L:</p>
        <pre class="snippet"><code class="language-cpp">const int dr[8] = {-2, -2, -1, -1, 1, 1, 2, 2};
const int dc[8] = {-1, 1, -2, 2, -2, 2, -1, 1};</code></pre>
        <p>Jika keadaannya punya beberapa komponen (misalnya posisi + kunci yang sudah diambil), gunakan array dist berdimensi lebih banyak: <code>dist[r][c][kunci]</code>.</p>
        <h3>3. 0-1 BFS</h3>
        <p>Jika bobot sisi hanya 0 atau 1, kita tidak perlu Dijkstra. Gunakan <code>deque</code>: simpul yang dicapai lewat sisi berbobot 0 ditaruh di <strong>depan</strong> (jaraknya sama), sedangkan lewat sisi berbobot 1 ditaruh di <strong>belakang</strong>. Kompleksitasnya tetap <code>O(N + M)</code>.</p>
    </div>
    @include('lessons.code', ['cpp' => $bfs01, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧭</span>
        <p>Pada 0-1 BFS sebuah simpul boleh diperbarui lebih dari sekali, karena itu kondisinya <code>dist[u] + w &lt; dist[v]</code>, bukan <code>dist[v] == -1</code>.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="BFS memproses simpul sesuai urutan masuk (FIFO), sehingga lapis yang lebih dekat selalu selesai lebih dulu.">
        <p class="quiz-q">Struktur data apa yang membuat BFS menjelajah lapis demi lapis?</p>
        <div class="quiz-options">
            <button class="quiz-option">Queue (antrian, FIFO)</button>
            <button class="quiz-option">Stack (tumpukan, LIFO)</button>
            <button class="quiz-option">Array terurut</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Simpul harus ditandai (dist diisi) saat dimasukkan. Jika ditandai saat dikeluarkan, simpul yang sama bisa masuk antrian berkali-kali.">
        <p class="quiz-q">Kapan <code>dist[v]</code> sebaiknya diisi?</p>
        <div class="quiz-options">
            <button class="quiz-option">Saat v diambil dari depan antrian</button>
            <button class="quiz-option">Saat v pertama kali ditemukan, sebelum dimasukkan ke antrian</button>
            <button class="quiz-option">Setelah BFS selesai</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Masukkan semua sumber ke antrian dengan jarak 0 lalu jalankan BFS sekali. Totalnya tetap O(N + M).">
        <p class="quiz-q">Ada 1000 rumah sakit di peta 10<sup>5</sup> simpul. Bagaimana menghitung jarak setiap simpul ke rumah sakit terdekat dengan cepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">BFS dari setiap rumah sakit, lalu ambil minimum</button>
            <button class="quiz-option">BFS dari setiap simpul</button>
            <button class="quiz-option">Multi-source BFS: semua rumah sakit masuk antrian di awal</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Grid 100 × 100 berisi 10 000 petak, masing-masing punya paling banyak 4 tetangga, jadi sekitar 40 000 operasi.">
        <p class="quiz-q">BFS pada grid 100 × 100 kira-kira membutuhkan berapa operasi?</p>
        <div class="quiz-options">
            <button class="quiz-option">Sekitar 100</button>
            <button class="quiz-option">Sekitar 10<sup>8</sup></button>
            <button class="quiz-option">Sekitar 4 × 10<sup>4</sup></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
