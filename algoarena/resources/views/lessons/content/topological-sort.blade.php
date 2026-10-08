@php
    $topoSteps = [
        ['Header & namespace', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;
CPP, <<<'TXT'
<p>Header standar seperti biasa.</p>
TXT],
        ['main() & membaca N, M', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
CPP, <<<'TXT'
<p><code>n</code> = banyak tugas, <code>m</code> = banyak aturan "a harus sebelum b". Pada contoh ada 6 tugas dan 6 aturan.</p>
TXT],
        ['Graph berarah & indegree', <<<'CPP'

    vector<vector<int>> adj(n + 1);
    vector<int> indeg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        indeg[b]++;
    }
CPP, <<<'TXT'
<p>Aturan "a sebelum b" menjadi sisi berarah <code>a → b</code>, jadi hanya <code>adj[a]</code> yang diisi.</p>
<p><code>indeg[b]</code> (<em>indegree</em>) menghitung banyaknya panah yang masuk ke b, yaitu banyaknya <strong>prasyarat</strong> b yang belum selesai.</p>
<pre>indeg: 1→2  2→1  3→1  4→1  5→0  6→1</pre>
<p>Tugas 5 (kumpulkan kebutuhan) tidak punya prasyarat, jadi bisa langsung dikerjakan.</p>
TXT],
        ['Antrian berisi tugas tanpa prasyarat', <<<'CPP'

    queue<int> q;
    for (int v = 1; v <= n; v++) {
        if (indeg[v] == 0) q.push(v);
    }
CPP, <<<'TXT'
<p>Semua tugas ber-indegree 0 boleh dikerjakan sekarang juga, jadi semuanya masuk antrian sejak awal. Pada contoh hanya tugas 5.</p>
TXT],
        ['Proses tugas satu per satu', <<<'CPP'

    vector<int> urutan;
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        urutan.push_back(u);
        for (int v : adj[u]) {
            if (--indeg[v] == 0) q.push(v);
        }
    }
CPP, <<<'TXT'
<p>Ambil tugas u dari antrian dan tulis ke <code>urutan</code>. Karena u sudah selesai, setiap tugas v yang menunggu u berkurang satu prasyaratnya:</p>
<ul>
    <li><code>--indeg[v]</code> mengurangi dulu, baru hasilnya dibandingkan dengan 0.</li>
    <li>Jika menjadi 0, semua prasyarat v sudah selesai, jadi v masuk antrian.</li>
</ul>
<pre>ambil 5 → indeg[2]: 1→0 ✓, indeg[4]: 1→0 ✓   antrian [2, 4]
ambil 2 → indeg[1]: 2→1                     antrian [4]
ambil 4 → indeg[1]: 1→0 ✓                   antrian [1]
ambil 1 → indeg[3]: 1→0 ✓   ambil 3 → indeg[6]: 1→0 ✓   ambil 6</pre>
<div class="wt-tip">Inilah <strong>algoritma Kahn</strong>: seperti mengupas bawang, kita terus mengambil lapisan simpul yang sudah tidak punya panah masuk.</div>
TXT],
        ['Deteksi siklus & cetak', <<<'CPP'

    if ((int)urutan.size() < n) {
        cout << "MUSTAHIL\n";
    } else {
        for (int i = 0; i < n; i++) {
            cout << urutan[i] << (i + 1 < n ? ' ' : '\n');
        }
    }
    return 0;
}
CPP, <<<'TXT'
<p>Jika ada <strong>siklus</strong> (misalnya a sebelum b, b sebelum c, c sebelum a), tugas-tugas di siklus itu saling menunggu selamanya. Indegree mereka tidak pernah menjadi 0, sehingga tidak pernah masuk antrian.</p>
<p>Jadi cukup cek panjang <code>urutan</code>: kurang dari n berarti ada siklus dan jawabannya MUSTAHIL. Jika tidak, cetak urutannya.</p>
TXT],
    ];

    $topoJs = <<<'JS'
const [n, m] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
const indeg = new Array(n + 1).fill(0);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  adj[a].push(b);
  indeg[b]++;
}

const queue = [];
for (let v = 1; v <= n; v++) if (indeg[v] === 0) queue.push(v);

const urutan = [];
for (let head = 0; head < queue.length; head++) {
  const u = queue[head];
  urutan.push(u);
  for (const v of adj[u]) {
    if (--indeg[v] === 0) queue.push(v);
  }
}

console.log(urutan.length < n ? "MUSTAHIL" : urutan.join(" "));
JS;

    $topoPy = <<<'PY'
import sys
from collections import deque
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
indeg = [0] * (n + 1)
for _ in range(m):
    a, b = map(int, input().split())
    adj[a].append(b)
    indeg[b] += 1

q = deque(v for v in range(1, n + 1) if indeg[v] == 0)
urutan = []
while q:
    u = q.popleft()
    urutan.append(u)
    for v in adj[u]:
        indeg[v] -= 1
        if indeg[v] == 0:
            q.append(v)

print("MUSTAHIL" if len(urutan) < n else " ".join(map(str, urutan)))
PY;

    $topoLex = <<<'CPP'
// Urutan leksikografis terkecil: ganti queue dengan min-heap
priority_queue<int, vector<int>, greater<int>> pq;
for (int v = 1; v <= n; v++) if (indeg[v] == 0) pq.push(v);
while (!pq.empty()) {
    int u = pq.top();   // selalu tugas bebas bernomor terkecil
    pq.pop();
    urutan.push_back(u);
    for (int v : adj[u]) if (--indeg[v] == 0) pq.push(v);
}
CPP;

    $topoDp = <<<'CPP'
// DP di atas DAG: waktu selesai paling awal setiap tugas
// (diproses mengikuti urutan topologis hasil Kahn)
vector<long long> mulai(n + 1, 0), selesai(n + 1, 0);
for (int u : urutan) {
    selesai[u] = mulai[u] + durasi[u];
    for (int v : adj[u]) {
        mulai[v] = max(mulai[v], selesai[u]); // v menunggu prasyarat terlama
    }
}
// Waktu proyek = nilai terbesar di selesai[]
CPP;

    $topoDfs = <<<'CPP'
// Topological sort dengan DFS: urutan selesai, lalu dibalik
vector<bool> seen(n + 1, false);
vector<int> post;

void dfs(int u) {
    seen[u] = true;
    for (int v : adj[u]) if (!seen[v]) dfs(v);
    post.push_back(u);   // u selesai SETELAH semua yang bergantung padanya
}
// for (v = 1..n) if (!seen[v]) dfs(v);
// reverse(post.begin(), post.end());   // post sekarang urutan topologis
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memodelkan aturan "a harus sebelum b" sebagai <strong>graph berarah</strong> dan memahami istilah <strong>DAG</strong>.</li>
        <li>Menjalankan <strong>algoritma Kahn</strong> dengan indegree dan antrian, termasuk mendeteksi <strong>siklus</strong>.</li>
        <li>Menulis topological sort lengkap di C++.</li>
        <li>Membuat urutan <strong>leksikografis terkecil</strong>, versi DFS, dan <strong>DP di atas DAG</strong> (waktu proyek, jalur terpanjang).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'memahami urutan topologis', 'desc' => 'Dari daftar tugas sehari-hari ke algoritma Kahn yang dijalankan dengan tangan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Urutan Tugas">
    <h2><span class="sec-icon">💡</span> Intuisi: Urutan Mengerjakan Tugas</h2>
    <div class="prose">
        <p>Tim kalian membuat aplikasi. Ada aturan: kebutuhan harus dikumpulkan sebelum mendesain UI maupun merancang database; desain dan database harus jadi sebelum menulis kode; kode harus jadi sebelum diuji; dan aplikasi diuji sebelum dirilis.</p>
        <p>Setiap aturan "a sebelum b" adalah <strong>panah a → b</strong>. Mencari urutan yang mematuhi semua panah disebut <strong>topological sort</strong>. Urutan seperti itu hanya ada jika graph-nya <strong>DAG</strong> (<em>Directed Acyclic Graph</em>): berarah dan tanpa siklus.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 600, 'h' => 260, 'directed' => true,
        'nodes' => [[5, 60, 130, 'green'], [2, 190, 60, 'cyan'], [4, 190, 200, 'cyan'], [1, 320, 130, 'vis'], [3, 440, 130, 'vis'], [6, 560, 130, 'cur']],
        'edges' => [[5, 2], [5, 4], [2, 1], [4, 1], [1, 3], [3, 6]],
        'notes' => [[60, 172, 'Kebutuhan'], [190, 32, 'Desain UI'], [190, 242, 'Database'], [320, 172, 'Tulis kode'], [440, 172, 'Uji'], [560, 172, 'Rilis']],
        'caption' => 'Salah satu urutan yang benar: 5, 2, 4, 1, 3, 6. Urutan 5, 4, 2, 1, 3, 6 juga benar, karena desain dan database tidak saling bergantung.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Pertanyaan kunci: <strong>tugas mana yang boleh dikerjakan sekarang?</strong> Jawabannya: tugas yang semua prasyaratnya sudah selesai, yaitu tugas yang <strong>tidak punya panah masuk</strong> yang tersisa.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2><span class="sec-icon">✍️</span> Coba dengan Tangan: Algoritma Kahn</h2>
    <div class="prose">
        <p>Hitung dulu <strong>indegree</strong> (banyak panah masuk) setiap simpul. Masukkan semua simpul ber-indegree 0 ke antrian. Setiap kali mengambil u, "hapus" panah-panah keluar dari u dengan mengurangi indegree tujuannya.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>#</th><th>Ambil</th><th>Indegree yang berkurang</th><th>Antrian</th><th>Urutan</th></tr>
            <tr><td>0</td><td>–</td><td>Awal: 1:2, 2:1, 3:1, 4:1, <span class="new">5:0</span>, 6:1</td><td class="q">[5]</td><td>–</td></tr>
            <tr class="hl"><td>1</td><td><b>5</b></td><td>2: 1 → <span class="new">0</span>, 4: 1 → <span class="new">0</span></td><td class="q">[2, 4]</td><td>5</td></tr>
            <tr><td>2</td><td><b>2</b></td><td>1: 2 → 1</td><td class="q">[4]</td><td>5 2</td></tr>
            <tr class="hl"><td>3</td><td><b>4</b></td><td>1: 1 → <span class="new">0</span></td><td class="q">[1]</td><td>5 2 4</td></tr>
            <tr><td>4</td><td><b>1</b></td><td>3: 1 → <span class="new">0</span></td><td class="q">[3]</td><td>5 2 4 1</td></tr>
            <tr class="hl"><td>5</td><td><b>3</b></td><td>6: 1 → <span class="new">0</span></td><td class="q">[6]</td><td>5 2 4 1 3</td></tr>
            <tr class="ok"><td>6</td><td><b>6</b></td><td>–</td><td class="q">[ ]</td><td>5 2 4 1 3 6 ✓</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Pada langkah 2, tugas 1 belum bisa masuk antrian karena masih menunggu database (tugas 4). Inilah fungsi indegree: menghitung prasyarat yang <strong>belum</strong> selesai.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2><span class="sec-icon">🎬</span> Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Perhatikan angka indegree yang turun setiap kali sebuah simpul diproses. Tekan <strong>🔁 Tambah siklus</strong> untuk melihat apa yang terjadi jika ada ketergantungan melingkar: algoritma berhenti sebelum semua simpul terambil.</p>
    </div>
    <div data-viz="toposort"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri soalnya</b><span>"Prasyarat", "harus sebelum", "ketergantungan", "urutan yang valid", "apakah mungkin menyelesaikan semua".</span></div>
        <div class="step-card"><b>Bangun graph berarah + indegree</b><span>Untuk aturan "a sebelum b": <code>adj[a].push_back(b)</code> dan <code>indeg[b]++</code>.</span></div>
        <div class="step-card"><b>Antrian awal</b><span>Masukkan semua simpul dengan <code>indeg = 0</code>.</span></div>
        <div class="step-card"><b>Kupas lapis demi lapis</b><span>Ambil u, catat ke urutan, kurangi indegree setiap tetangga. Yang menjadi 0 masuk antrian.</span></div>
        <div class="step-card"><b>Cek siklus</b><span>Jika urutan berisi kurang dari N simpul, ada siklus.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis algoritma Kahn di C++', 'desc' => 'Program lengkap dengan indegree, antrian, dan deteksi siklus.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2><span class="sec-icon">💻</span> Kode C++ Lengkap</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> ada <code>n</code> tugas dan <code>m</code> aturan "a sebelum b". Cetak satu urutan pengerjaan yang valid, atau <code>MUSTAHIL</code> jika aturannya melingkar.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Topological sort: algoritma Kahn',
        'steps' => $topoSteps,
        'sample' => ['input' => "6 6\n5 2\n5 4\n2 1\n4 1\n1 3\n3 6\n", 'output' => "5 2 4 1 3 6\n"],
        'js' => $topoJs,
        'py' => $topoPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal">
    <h2><span class="sec-icon">🧠</span> Pola Soal yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>📋 Cetak urutan</h4><p>Algoritma Kahn langsung. Baca soal baik-baik: apakah urutan harus <b>terkecil</b> jika ada pilihan?</p></div>
        <div class="pattern"><h4>🔁 Mungkinkah?</h4><p>"Bisakah semua mapel diambil?" Sama dengan "apakah tidak ada siklus?".</p></div>
        <div class="pattern"><h4>⏳ Waktu proyek</h4><p>Tugas berdurasi, boleh dikerjakan paralel. DP: <code>mulai[v] = max(selesai[prasyarat])</code>.</p></div>
        <div class="pattern"><h4>🎓 Semester minimum</h4><p>Berapa "lapis" Kahn yang dibutuhkan? Sama dengan jalur terpanjang (dalam simpul).</p></div>
        <div class="pattern"><h4>🧮 Hitung jalur</h4><p>Banyaknya jalur di DAG: <code>cnt[v] += cnt[u]</code> mengikuti urutan topologis.</p></div>
        <div class="pattern"><h4>🔤 Urutan huruf</h4><p>"Kamus alien": bandingkan kata bertetangga untuk membangun panah antar huruf.</p></div>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Versi</th><th>Waktu</th><th>Keterangan</th></tr>
        <tr><td>Kahn (queue)</td><td><code>O(N + M)</code></td><td>Setiap simpul dan sisi diproses sekali.</td></tr>
        <tr><td>Kahn (min-heap)</td><td><code>O((N + M) log N)</code></td><td>Untuk urutan leksikografis terkecil.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Arah panah terbalik.</strong> "b membutuhkan a" berarti panah <code>a → b</code>, bukan sebaliknya. Gambar dulu contoh kecil sebelum menulis kode.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jawaban tidak unik.</strong> Biasanya ada banyak urutan yang benar. Jika soal meminta urutan tertentu (misalnya terkecil), queue biasa bisa memberi Wrong Answer.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'topological sort untuk soal sulit', 'desc' => 'Urutan terkecil, versi DFS, dan DP di atas DAG.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Kahn Benar?">
    <h2><span class="sec-icon">🔬</span> Mengapa Kahn Benar?</h2>
    <div class="proof">
        <p><strong>Urutannya valid.</strong> Simpul v baru masuk antrian setelah indegree-nya 0, yaitu setelah <em>semua</em> simpul dengan panah ke v sudah dicatat. Jadi setiap panah a → b dipatuhi.</p>
        <p><strong>Siklus pasti terdeteksi.</strong> Pada sebuah siklus, setiap simpul punya panah masuk dari simpul lain di siklus itu. Simpul pertama siklus yang akan masuk antrian harus menunggu simpul siklus sebelumnya, yang juga menunggu, dan seterusnya. Tidak ada yang bisa mulai, jadi semuanya tertinggal.</p>
        <p><strong>DAG selalu selesai.</strong> Setiap DAG pasti punya minimal satu simpul ber-indegree 0 (jika tidak, kita bisa terus mundur mengikuti panah masuk dan akhirnya berputar, artinya ada siklus). Setelah simpul itu dibuang, sisanya tetap DAG, jadi proses berlanjut sampai habis.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2><span class="sec-icon">🚀</span> Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Urutan leksikografis terkecil</h3>
        <p>Jika ada beberapa tugas bebas sekaligus dan soal meminta yang bernomor terkecil dikerjakan lebih dulu, ganti <code>queue</code> dengan <strong>min-heap</strong>:</p>
    </div>
    @include('lessons.code', ['cpp' => $topoLex, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. DP di atas DAG</h3>
        <p>Urutan topologis menjamin semua prasyarat diproses lebih dulu, jadi kita bisa mengisi DP "dari kiri ke kanan" mengikuti urutan itu. Contoh: waktu selesai paling awal jika tugas boleh dikerjakan paralel.</p>
    </div>
    @include('lessons.code', ['cpp' => $topoDp, 'js' => null, 'py' => null])
    <div class="prose">
        <p>Dengan pola yang sama: jalur terpanjang (<code>dp[v] = max(dp[u] + w)</code>), banyaknya jalur (<code>cnt[v] += cnt[u]</code>), atau semester minimum (setiap tugas berdurasi 1).</p>
        <h3>3. Topological sort dengan DFS</h3>
        <p>Simpul dicatat <strong>setelah</strong> semua simpul yang bisa dicapai darinya selesai. Akibatnya, urutan selesai (<em>postorder</em>) adalah kebalikan dari urutan topologis.</p>
    </div>
    @include('lessons.code', ['cpp' => $topoDfs, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Indegree 0 berarti tidak ada prasyarat yang tersisa, sehingga tugas itu boleh dikerjakan sekarang.">
        <p class="quiz-q">Simpul mana yang boleh masuk antrian Kahn?</p>
        <div class="quiz-options">
            <button class="quiz-option">Simpul dengan outdegree 0</button>
            <button class="quiz-option">Simpul dengan indegree 0</button>
            <button class="quiz-option">Simpul bernomor terkecil</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Simpul-simpul pada siklus saling menunggu, sehingga indegree mereka tidak pernah 0 dan tidak pernah masuk antrian.">
        <p class="quiz-q">Kahn selesai, tetapi urutan hanya berisi 7 dari 10 simpul. Artinya…</p>
        <div class="quiz-options">
            <button class="quiz-option">Ada 3 simpul yang tidak punya sisi</button>
            <button class="quiz-option">Graph tidak terhubung</button>
            <button class="quiz-option">Graph punya siklus</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Aturan 'b membutuhkan a' berarti a harus dikerjakan sebelum b, sehingga panahnya a → b dan indeg[b] bertambah.">
        <p class="quiz-q">Soal: "Mapel 4 membutuhkan mapel 2." Sisi apa yang dibuat?</p>
        <div class="quiz-options">
            <button class="quiz-option">2 → 4, lalu <code>indeg[4]++</code></button>
            <button class="quiz-option">4 → 2, lalu <code>indeg[2]++</code></button>
            <button class="quiz-option">Sisi tak berarah 2 – 4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
