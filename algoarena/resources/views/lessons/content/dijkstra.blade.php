@php
    $dijSteps = [
        ['Header & konstanta tak hingga', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;
CPP, <<<'TXT'
<p>Selain header biasa, kita butuh nilai "tak hingga" untuk jarak simpul yang belum terjangkau.</p>
<p><code>LLONG_MAX</code> adalah bilangan <code>long long</code> terbesar (±9,2 × 10<sup>18</sup>). Kita membaginya dengan 4 supaya <code>INF + bobot</code> tidak <strong>meluap</strong> (overflow) menjadi bilangan negatif.</p>
<div class="wt-tip">Jarak memakai <code>long long</code> karena total bobot bisa melewati batas <code>int</code> (±2,1 × 10<sup>9</sup>). Misalnya 10<sup>5</sup> sisi × bobot 10<sup>9</sup>.</div>
TXT],
        ['main() & membaca N, M, S, T', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, s, t;
    cin >> n >> m >> s >> t;
CPP, <<<'TXT'
<p>Input dimulai dengan banyak simpul <code>n</code>, banyak sisi <code>m</code>, simpul awal <code>s</code>, dan tujuan <code>t</code>. Pada contoh: 6 simpul, 9 sisi, dari 1 ke 6.</p>
TXT],
        ['Adjacency list berbobot', <<<'CPP'

    vector<vector<pair<int, int>>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v, w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w});
        adj[v].push_back({u, w});
    }
CPP, <<<'TXT'
<p>Karena setiap sisi punya bobot, adjacency list menyimpan <strong>pasangan</strong> <code>{tetangga, bobot}</code>. Pada <code>pair</code>, bagian pertama diakses dengan <code>.first</code> dan bagian kedua dengan <code>.second</code>.</p>
<pre>adj[1] = {(2,4), (3,2)}
adj[3] = {(1,2), (2,1), (5,8), (4,8)}</pre>
<div class="wt-tip">Untuk jalan satu arah, hapus baris <code>adj[v].push_back({u, w})</code>.</div>
TXT],
        ['dist, parent, dan priority queue', <<<'CPP'

    vector<long long> dist(n + 1, INF);
    vector<int> parent(n + 1, 0);
    priority_queue<pair<long long, int>, vector<pair<long long, int>>,
                   greater<pair<long long, int>>> pq;
CPP, <<<'TXT'
<ul>
    <li><code>dist[v]</code>: jarak terbaik yang <em>sejauh ini</em> diketahui dari s ke v. Awalnya INF.</li>
    <li><code>parent[v]</code>: simpul sebelum v pada rute terbaik, untuk menyusun rute.</li>
    <li><code>pq</code>: priority queue berisi pasangan <code>(jarak, simpul)</code>.</li>
</ul>
<p>Secara bawaan, <code>priority_queue</code> C++ mengeluarkan elemen <strong>terbesar</strong>. Dengan tambahan <code>greater&lt;…&gt;</code> ia berubah menjadi <strong>min-heap</strong> yang mengeluarkan jarak <strong>terkecil</strong> lebih dulu. Pasangan dibandingkan dari <code>.first</code> (jarak) dulu, itulah sebabnya jarak diletakkan di depan.</p>
TXT],
        ['Simpul awal', <<<'CPP'

    dist[s] = 0;
    pq.push({0, s});
CPP, <<<'TXT'
<p>Jarak s ke dirinya sendiri adalah 0, dan s menjadi isi pertama priority queue.</p>
TXT],
        ['Ambil simpul terdekat & buang data usang', <<<'CPP'

    while (!pq.empty()) {
        long long d = pq.top().first;
        int u = pq.top().second;
        pq.pop();
        if (d > dist[u]) continue;
CPP, <<<'TXT'
<p>Setiap putaran, ambil pasangan dengan jarak terkecil, <code>(d, u)</code>.</p>
<p>Baris <code>if (d &gt; dist[u]) continue;</code> sangat penting. Simpul yang sama bisa masuk PQ <strong>berkali-kali</strong> (setiap kali jaraknya membaik). Pasangan lama dengan jarak yang lebih besar disebut <strong>data usang</strong> dan harus dilewati.</p>
<div class="wt-tip">Pada contoh, simpul 2 pernah masuk sebagai (4, 2), lalu membaik menjadi (3, 2). Saat (4, 2) keluar belakangan, 4 &gt; dist[2] = 3, jadi dilewati.</div>
TXT],
        ['Relaksasi setiap sisi', <<<'CPP'

        for (auto &e : adj[u]) {
            int v = e.first, w = e.second;
            if (dist[u] + w < dist[v]) {
                dist[v] = dist[u] + w;
                parent[v] = u;
                pq.push({dist[v], v});
            }
        }
    }
CPP, <<<'TXT'
<p>Untuk setiap sisi <code>u → v</code> berbobot <code>w</code>, tanyakan: "Apakah lewat u lebih cepat daripada jarak terbaik v yang sudah ada?" Jika ya:</p>
<ol>
    <li>Perbarui <code>dist[v] = dist[u] + w</code>.</li>
    <li>Catat <code>parent[v] = u</code>.</li>
    <li>Masukkan pasangan baru <code>(dist[v], v)</code> ke PQ.</li>
</ol>
<p>Langkah ini disebut <strong>relaksasi</strong>. Contoh: saat u = 3 (jarak 2), sisi 3 → 2 berbobot 1 memberi 2 + 1 = 3 &lt; 4, sehingga <code>dist[2]</code> turun dari 4 menjadi 3.</p>
<div class="wt-tip"><code>auto &amp;e</code> mengambil setiap pasangan dengan referensi (tanpa menyalin), lalu dipecah menjadi <code>v</code> dan <code>w</code> agar mudah dibaca.</div>
TXT],
        ['Tujuan tidak terjangkau', <<<'CPP'

    if (dist[t] == INF) {
        cout << -1 << '\n';
        return 0;
    }
CPP, <<<'TXT'
<p>Jika <code>dist[t]</code> masih INF setelah PQ kosong, tidak ada jalan dari s ke t.</p>
TXT],
        ['Menyusun rute & mencetak', <<<'CPP'

    vector<int> path;
    for (int v = t; v != s; v = parent[v]) path.push_back(v);
    path.push_back(s);
    reverse(path.begin(), path.end());

    cout << dist[t] << '\n';
    for (int i = 0; i < (int)path.size(); i++) {
        cout << path[i] << (i + 1 < (int)path.size() ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Sama seperti pada BFS: telusuri <code>parent</code> mundur dari t sampai s, lalu balik urutannya.</p>
<pre>6 ← parent 5 ← parent 3 ← parent 1
rute: 1 3 5 6, total 2 + 8 + 3 = 13</pre>
TXT],
    ];

    $dijJs = <<<'JS'
const [n, m, s, t] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  adj[u].push([v, w]);
  adj[v].push([u, w]);
}

// Min-heap sederhana berisi pasangan [jarak, simpul]
const heap = [];
const push = (x) => {
  heap.push(x);
  for (let i = heap.length - 1; i > 0; ) {
    const p = (i - 1) >> 1;
    if (heap[p][0] <= heap[i][0]) break;
    [heap[p], heap[i]] = [heap[i], heap[p]];
    i = p;
  }
};
const pop = () => {
  const top = heap[0];
  const last = heap.pop();
  if (heap.length) {
    heap[0] = last;
    for (let i = 0; ; ) {
      const l = 2 * i + 1, r = l + 1;
      let k = i;
      if (l < heap.length && heap[l][0] < heap[k][0]) k = l;
      if (r < heap.length && heap[r][0] < heap[k][0]) k = r;
      if (k === i) break;
      [heap[k], heap[i]] = [heap[i], heap[k]];
      i = k;
    }
  }
  return top;
};

const dist = new Array(n + 1).fill(Infinity);
const parent = new Array(n + 1).fill(0);
dist[s] = 0;
push([0, s]);
while (heap.length) {
  const [d, u] = pop();
  if (d > dist[u]) continue; // data usang
  for (const [v, w] of adj[u]) {
    if (dist[u] + w < dist[v]) {
      dist[v] = dist[u] + w;
      parent[v] = u;
      push([dist[v], v]);
    }
  }
}

if (dist[t] === Infinity) {
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

    $dijPy = <<<'PY'
import sys
import heapq
input = sys.stdin.readline

n, m, s, t = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v, w = map(int, input().split())
    adj[u].append((v, w))
    adj[v].append((u, w))

INF = float("inf")
dist = [INF] * (n + 1)
parent = [0] * (n + 1)
dist[s] = 0
pq = [(0, s)]
while pq:
    d, u = heapq.heappop(pq)
    if d > dist[u]:
        continue  # data usang
    for v, w in adj[u]:
        if dist[u] + w < dist[v]:
            dist[v] = dist[u] + w
            parent[v] = u
            heapq.heappush(pq, (dist[v], v))

if dist[t] == INF:
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

    $dijState = <<<'CPP'
// Dijkstra dengan keadaan tambahan: punya 1 kupon untuk menggratiskan satu sisi
// dist[v][k] = biaya termurah sampai di v dengan k kupon sudah dipakai (k = 0/1)
vector<array<long long, 2>> dist(n + 1, {INF, INF});
priority_queue<tuple<long long, int, int>, vector<tuple<long long, int, int>>,
               greater<tuple<long long, int, int>>> pq;
dist[s][0] = 0;
pq.push(make_tuple(0LL, s, 0));
while (!pq.empty()) {
    long long d; int u, k;
    tie(d, u, k) = pq.top();
    pq.pop();
    if (d > dist[u][k]) continue;
    for (auto &e : adj[u]) {
        int v = e.first, w = e.second;
        if (d + w < dist[v][k]) {                    // bayar normal
            dist[v][k] = d + w;
            pq.push(make_tuple(dist[v][k], v, k));
        }
        if (k == 0 && d < dist[v][1]) {              // pakai kupon di sisi ini
            dist[v][1] = d;
            pq.push(make_tuple(dist[v][1], v, 1));
        }
    }
}
// Jawaban: min(dist[t][0], dist[t][1])
CPP;

    $dijCount = <<<'CPP'
// Menghitung BANYAKNYA rute terpendek (modulo 1e9+7)
vector<long long> cnt(n + 1, 0);
cnt[s] = 1;
// ...di dalam relaksasi Dijkstra:
if (dist[u] + w < dist[v]) {          // rute baru yang lebih pendek
    dist[v] = dist[u] + w;
    cnt[v] = cnt[u];
    pq.push({dist[v], v});
} else if (dist[u] + w == dist[v]) {  // rute lain yang sama pendek
    cnt[v] = (cnt[v] + cnt[u]) % MOD;
}
CPP;
@endphp

<div class="lead-box">
    <h3>🎯 Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan mengapa BFS gagal pada graph <strong>berbobot</strong> dan bagaimana Dijkstra memperbaikinya.</li>
        <li>Memahami <strong>relaksasi</strong> sisi dan peran <strong>priority queue</strong> (min-heap).</li>
        <li>Menulis Dijkstra lengkap di C++ dengan <code>long long</code>, penanganan data usang, dan rekonstruksi rute.</li>
        <li>Memakai Dijkstra dengan <strong>keadaan tambahan</strong> dan menghitung banyaknya rute terpendek.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'memahami cara kerja Dijkstra', 'desc' => 'Mengapa BFS tidak cukup, ide serakah Dijkstra, dan jejaknya dengan tangan.'])

<section class="lesson-section" id="intuisi" data-toc="Mengapa BFS Tidak Cukup?">
    <h2><span class="sec-icon">💡</span> Mengapa BFS Tidak Cukup?</h2>
    <div class="prose">
        <p>BFS menghitung jarak dalam <strong>jumlah sisi</strong>. Tapi di dunia nyata, setiap jalan punya panjang berbeda. Rute dengan sedikit belokan belum tentu paling cepat!</p>
        <p>Pada gambar di bawah, dari kota 1 ke kota 2 ada jalan langsung sepanjang 4. Namun lewat kota 3 hanya 2 + 1 = 3. BFS akan memilih jalan langsung (1 sisi), padahal lebih jauh.</p>
        <p><strong>Ide Dijkstra:</strong> selalu proses kota yang <strong>paling dekat</strong> dengan sumber di antara kota yang belum final. Jarak kota itu dijamin sudah benar, karena semua jalan lain pasti lebih jauh (bobot tidak ada yang negatif).</p>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 380,
        'nodes' => [[1, 70, 200, 'green'], [2, 230, 95, 'vis'], [3, 230, 310, 'vis'], [4, 410, 95, 'vis'], [5, 410, 310, 'vis'], [6, 570, 200, 'cur']],
        'edges' => [[1, 2, '', 4], [1, 3, 'ok', 2], [3, 2, '', 1], [2, 4, '', 5], [3, 5, 'ok', 8], [3, 4, '', 8], [4, 5, '', 2], [4, 6, '', 6], [5, 6, 'ok', 3]],
        'badges' => [1 => '0', 2 => '3', 3 => '2', 4 => '8', 5 => '10', 6 => '13'],
        'caption' => 'Angka kuning di atas simpul adalah jarak terpendek dari kota 1. Rute hijau 1 → 3 → 5 → 6 berpanjang 13.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Dijkstra = BFS yang antriannya diganti <strong>priority queue</strong>: bukan "yang datang duluan", melainkan "yang <strong>jaraknya terkecil</strong>" yang diproses duluan.</p>
    </div>
</section>

<section class="lesson-section" id="relaksasi" data-toc="Relaksasi">
    <h2><span class="sec-icon">🪢</span> Konsep Kunci: Relaksasi</h2>
    <div class="prose">
        <p>Bayangkan <code>dist[v]</code> sebagai "rekor jarak terbaik ke v sejauh ini". Saat kita memproses kota u, kita cek setiap jalan u → v berbobot w:</p>
        <pre class="snippet"><code class="language-cpp">if (dist[u] + w < dist[v]) {   // lewat u lebih cepat?
    dist[v] = dist[u] + w;     // pecahkan rekor
}</code></pre>
        <p>Pemeriksaan ini disebut <strong>relaksasi</strong> (seperti tali yang tegang menjadi lebih kendur). Semua algoritma jalur terpendek, termasuk Bellman-Ford dan Floyd-Warshall, dibangun dari operasi sederhana ini.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2><span class="sec-icon">✍️</span> Coba dengan Tangan</h2>
    <div class="prose">
        <p>Jalankan Dijkstra dari kota 1 pada graph di atas. PQ ditulis sebagai pasangan <code>(jarak, simpul)</code>, terurut dari yang terkecil.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>#</th><th>Ambil (d, u)</th><th>Relaksasi</th><th>Isi PQ sesudahnya</th></tr>
            <tr><td>0</td><td>–</td><td><span class="new">dist[1] = 0</span></td><td class="q">(0,1)</td></tr>
            <tr class="hl"><td>1</td><td><b>(0, 1)</b></td><td><span class="new">dist[2] = 4, dist[3] = 2</span></td><td class="q">(2,3) (4,2)</td></tr>
            <tr><td>2</td><td><b>(2, 3)</b></td><td><span class="new">dist[2]: 4 → 3</span>, dist[5] = 10, dist[4] = 10</td><td class="q">(3,2) (4,2) (10,4) (10,5)</td></tr>
            <tr class="hl"><td>3</td><td><b>(3, 2)</b></td><td><span class="new">dist[4]: 10 → 8</span></td><td class="q">(4,2) (8,4) (10,4) (10,5)</td></tr>
            <tr><td>4</td><td>(4, 2)</td><td>Usang: 4 &gt; dist[2] = 3, lewati</td><td class="q">(8,4) (10,4) (10,5)</td></tr>
            <tr class="hl"><td>5</td><td><b>(8, 4)</b></td><td><span class="new">dist[6] = 14</span>; ke 5: 8 + 2 = 10, tidak lebih kecil</td><td class="q">(10,4) (10,5) (14,6)</td></tr>
            <tr><td>6</td><td>(10, 4)</td><td>Usang, lewati</td><td class="q">(10,5) (14,6)</td></tr>
            <tr class="hl"><td>7</td><td><b>(10, 5)</b></td><td><span class="new">dist[6]: 14 → 13</span></td><td class="q">(13,6) (14,6)</td></tr>
            <tr><td>8</td><td><b>(13, 6)</b></td><td>–</td><td class="q">(14,6)</td></tr>
            <tr class="ok"><td>9</td><td>(14, 6)</td><td>Usang, PQ kosong</td><td>Selesai ✓</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Perhatikan langkah 2: jarak ke kota 2 <strong>membaik</strong> dari 4 menjadi 3. Pada BFS hal ini tidak pernah terjadi. Pada Dijkstra, sebuah simpul baru dianggap <strong>final</strong> ketika keluar dari PQ dengan data yang tidak usang.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2><span class="sec-icon">🎬</span> Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Perhatikan panel <strong>priority queue</strong>: elemen terkecil selalu keluar lebih dulu. Simpul ungu tua artinya jaraknya sudah final. Dengan <strong>🎯 Mode Tebak</strong>, tebak simpul yang keluar berikutnya dan hasil relaksasinya.</p>
    </div>
    <div data-viz="dijkstra"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri soal Dijkstra</b><span>"Biaya/waktu/jarak minimum" dengan bobot <strong>berbeda-beda</strong> dan <strong>tidak negatif</strong>.</span></div>
        <div class="step-card"><b>Bangun adjacency list berbobot</b><span>Simpan <code>{tetangga, bobot}</code>. Perhatikan berarah atau tidak.</span></div>
        <div class="step-card"><b>Siapkan dist = INF dan min-heap</b><span>Gunakan <code>long long</code>. Masukkan <code>(0, s)</code>.</span></div>
        <div class="step-card"><b>Ambil terkecil, lewati data usang, relaksasi</b><span>Ulangi sampai PQ kosong.</span></div>
        <div class="step-card"><b>Baca jawaban</b><span><code>dist[t]</code>, atau -1 jika masih INF. Rute disusun dari <code>parent</code>.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis Dijkstra di C++', 'desc' => 'Program lengkap dengan priority queue, data usang, dan rekonstruksi rute.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2><span class="sec-icon">💻</span> Kode C++ Lengkap: Rute Termurah</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> graph tak berarah berbobot. Cetak total bobot rute termurah dari <code>s</code> ke <code>t</code> beserta rutenya, atau <code>-1</code> jika tidak ada.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Dijkstra: rute termurah dari s ke t',
        'steps' => $dijSteps,
        'sample' => ['input' => "6 9 1 6\n1 2 4\n1 3 2\n3 2 1\n2 4 5\n3 5 8\n3 4 8\n4 5 2\n4 6 6\n5 6 3\n", 'output' => "13\n1 3 5 6\n"],
        'js' => $dijJs,
        'py' => $dijPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal Dijkstra">
    <h2><span class="sec-icon">🧠</span> Pola Soal yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>🚚 Satu sumber</h4><p>"Biaya termurah dari gudang ke setiap kota". Dijkstra biasa, cetak semua <code>dist</code>.</p></div>
        <div class="pattern"><h4>🏁 Ke satu tujuan</h4><p>Boleh berhenti lebih awal begitu t keluar dari PQ (jaraknya sudah final).</p></div>
        <div class="pattern"><h4>🔄 Arah dibalik</h4><p>"Jarak semua kota <b>ke</b> t" pada graph berarah: balik semua sisi, lalu Dijkstra dari t.</p></div>
        <div class="pattern"><h4>🎟️ Keadaan tambahan</h4><p>Kupon, bensin, atau jumlah transit: simpul menjadi pasangan <code>(kota, keadaan)</code>.</p></div>
        <div class="pattern"><h4>🧮 Hitung rute</h4><p>Banyak rute terpendek: tambahkan array <code>cnt</code> saat relaksasi.</p></div>
        <div class="pattern"><h4>🏥 Banyak sumber</h4><p>Semua sumber masuk PQ dengan jarak 0, seperti multi-source BFS.</p></div>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Versi</th><th>Waktu</th><th>Cocok untuk</th></tr>
        <tr><td>Priority queue (min-heap)</td><td><code>O((N + M) log M)</code></td><td>Hampir semua soal</td></tr>
        <tr><td>Tanpa heap (cari minimum linear)</td><td><code>O(N²)</code></td><td>Graph padat, N ≤ ±5000</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa <code>if (d &gt; dist[u]) continue;</code></strong> tidak membuat jawaban salah, tetapi simpul yang sama bisa diproses berulang kali dan program bisa TLE pada tes besar.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> Pakai <code>long long</code> untuk jarak dan jangan memakai <code>INT_MAX</code> sebagai INF jika nanti dijumlahkan dengan bobot.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Bobot negatif dilarang.</strong> Dijkstra bisa memberi jawaban salah, atau (pada versi yang memproses ulang simpul) menjadi sangat lambat. Gunakan Bellman-Ford (materi Floyd-Warshall &amp; Bellman-Ford).</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'Dijkstra untuk soal sulit', 'desc' => 'Bukti keserakahan, kegagalan pada bobot negatif, keadaan tambahan, dan menghitung rute.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Dijkstra Benar?">
    <h2><span class="sec-icon">🔬</span> Mengapa Dijkstra Benar?</h2>
    <div class="proof">
        <p><strong>Klaim.</strong> Ketika simpul u keluar dari PQ (dan datanya tidak usang), <code>dist[u]</code> sudah merupakan jarak terpendek yang sebenarnya.</p>
        <p><strong>Bukti singkat.</strong> Misalkan ada rute lain ke u yang lebih pendek. Rute itu pasti keluar dari "wilayah final" melalui suatu simpul x yang belum final. Karena x belum keluar dari PQ, <code>dist[x] ≥ dist[u]</code>. Sisa perjalanan dari x ke u berbobot <strong>≥ 0</strong>, jadi total rute itu ≥ <code>dist[u]</code>. Kontradiksi.</p>
        <p><strong>Perhatikan</strong> bagian "berbobot ≥ 0". Jika ada sisi negatif, sisa perjalanan bisa mengurangi total, dan bukti ini runtuh.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 520, 'h' => 200, 'directed' => true,
        'nodes' => [[1, 60, 110, 'green', 'A'], [2, 260, 40, 'vis', 'B'], [3, 260, 170, 'red', 'C'], [4, 460, 110, 'cur', 'D']],
        'edges' => [[1, 2, '', 2], [1, 3, '', 5], [3, 2, 'bad', -4], [2, 4, '', 1]],
        'caption' => 'Bobot negatif: B keluar dari PQ dengan jarak 2 dan dianggap final, padahal A → C → B hanya 5 − 4 = 1. Jarak D yang dihitung dari B ikut salah.',
    ])
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2><span class="sec-icon">🚀</span> Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Dijkstra dengan keadaan tambahan</h3>
        <p>Soal: "Kamu punya satu kupon untuk menggratiskan satu ruas jalan. Berapa biaya termurah?" Cukup tahu kota saja tidak cukup: kita juga perlu tahu kupon sudah dipakai atau belum. Jadikan <strong>pasangan (kota, kupon)</strong> sebagai simpul baru. Graph-nya jadi dua lapis, dan Dijkstra berjalan seperti biasa di atasnya.</p>
    </div>
    @include('lessons.code', ['cpp' => $dijState, 'js' => null, 'py' => null])
    <div class="prose">
        <p><code>tuple</code> menyimpan tiga nilai sekaligus, dan <code>tie(d, u, k) = …</code> membongkarnya ke tiga variabel. Ini cara C++14 karena structured binding <code>auto [d, u, k]</code> baru ada di C++17.</p>
        <h3>2. Menghitung banyaknya rute terpendek</h3>
        <p>Jika rute baru <strong>lebih pendek</strong>, banyaknya rute ke v disalin dari u. Jika <strong>sama pendek</strong>, rute-rute lewat u ditambahkan.</p>
    </div>
    @include('lessons.code', ['cpp' => $dijCount, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>3. Dijkstra O(N²) untuk graph padat</h3>
        <p>Jika hampir setiap pasangan kota terhubung (M ≈ N²), versi tanpa heap justru lebih cepat. Setiap putaran, cari simpul belum final dengan <code>dist</code> terkecil secara linear, finalkan, lalu relaksasi semua tetangganya. Totalnya <code>O(N²)</code> tanpa faktor log.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Secara bawaan priority_queue adalah max-heap. greater<> membaliknya menjadi min-heap sehingga jarak terkecil keluar lebih dulu.">
        <p class="quiz-q">Mengapa <code>priority_queue</code> di Dijkstra diberi <code>greater&lt;…&gt;</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar bisa menyimpan pasangan</button>
            <button class="quiz-option">Agar elemen dengan jarak terkecil keluar lebih dulu</button>
            <button class="quiz-option">Agar tidak terjadi overflow</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Simpul bisa masuk PQ beberapa kali. Pasangan lama yang jaraknya lebih besar dari dist[u] adalah data usang dan harus dilewati.">
        <p class="quiz-q">Apa fungsi baris <code>if (d &gt; dist[u]) continue;</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Melewati pasangan lama (usang) yang jaraknya sudah tidak berlaku</button>
            <button class="quiz-option">Mendeteksi bobot negatif</button>
            <button class="quiz-option">Menghentikan algoritma saat tujuan ditemukan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Semua sisi berbobot sama (1), jadi BFS cukup dan lebih cepat (O(N + M) tanpa log). Dijkstra juga benar, hanya tidak perlu.">
        <p class="quiz-q">Semua jalan di soal berbobot 1. Algoritma apa yang paling tepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">Dijkstra, karena itu graph berbobot</button>
            <button class="quiz-option">Bellman-Ford</button>
            <button class="quiz-option">BFS</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
