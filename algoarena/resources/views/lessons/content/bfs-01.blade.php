@php
    $b01Steps = [
        ['Membaca peta', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<string> g(R);
    for (auto& baris : g) cin >> baris;
CPP, <<<'TXT'
<p>Peta berukuran R × C. Petak <code>.</code> adalah jalan kosong, petak <code>#</code> adalah tembok. Robot mulai di pojok kiri atas dan ingin ke pojok kanan bawah, bergerak ke atas/bawah/kiri/kanan.</p>
<p>Robot boleh <strong>merobohkan tembok</strong> untuk melewatinya. Berapa tembok paling sedikit yang harus dirobohkan?</p>
TXT],
        ['Graph tersembunyi: biaya masuk petak', <<<'CPP'

    const int INF = 1e9;
    vector<vector<int>> dist(R, vector<int>(C, INF));
    deque<pair<int, int>> dq;
    dist[0][0] = (g[0][0] == '#');
    dq.push_back({0, 0});
    int dr[] = {-1, 1, 0, 0};
    int dc[] = {0, 0, -1, 1};
CPP, <<<'TXT'
<p>Setiap petak adalah simpul. Bergerak ke petak tetangga berbiaya <strong>1</strong> jika petak itu tembok, dan <strong>0</strong> jika jalan kosong. Graph seperti ini tidak pernah ditulis secara eksplisit; tetangga dihitung saat dibutuhkan lewat <code>dr</code>/<code>dc</code>.</p>
<p><code>dist[r][c]</code> = tembok minimum yang dirobohkan untuk sampai ke (r, c). Jika petak awal sendiri tembok, biayanya 1.</p>
<div class="wt-tip">BFS biasa salah di sini karena tidak semua langkah berbiaya sama. Dijkstra benar tetapi lebih lambat. Bobot 0/1 adalah kasus khusus yang bisa ditangani dengan <strong>deque</strong>.</div>
TXT],
        ['Ambil dari depan deque', <<<'CPP'

    while (!dq.empty()) {
        int r = dq.front().first, c = dq.front().second;
        dq.pop_front();
CPP, <<<'TXT'
<p>Deque selalu menyimpan simpul dengan jarak <strong>d</strong> di depan dan <strong>d + 1</strong> di belakang, tidak pernah lebih dari dua nilai berbeda. Jadi yang diambil dari depan selalu simpul dengan jarak terkecil, persis seperti Dijkstra, tanpa biaya log N.</p>
TXT],
        ['Relaksasi: bobot 0 ke depan, bobot 1 ke belakang', <<<'CPP'
        for (int k = 0; k < 4; k++) {
            int nr = r + dr[k], nc = c + dc[k];
            if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue;
            int w = (g[nr][nc] == '#');
            if (dist[r][c] + w < dist[nr][nc]) {
                dist[nr][nc] = dist[r][c] + w;
                if (w == 0) dq.push_front({nr, nc});
                else dq.push_back({nr, nc});
            }
        }
    }
CPP, <<<'TXT'
<p>Untuk setiap tetangga di dalam peta, biayanya <code>w</code> = 1 jika tembok, 0 jika kosong. Jika jalur lewat (r, c) lebih murah, perbarui jaraknya:</p>
<ul>
    <li><strong>w = 0</strong>: jaraknya sama dengan (r, c), jadi taruh di <strong>depan</strong> agar diproses segera.</li>
    <li><strong>w = 1</strong>: jaraknya satu lebih besar, taruh di <strong>belakang</strong>.</li>
</ul>
<p>Sebuah petak bisa masuk deque lebih dari sekali, tetapi paling banyak dua kali per perbaikan, sehingga total tetap O(R · C).</p>
TXT],
        ['Jawaban', <<<'CPP'

    cout << dist[R - 1][C - 1] << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Jawabannya jarak ke pojok kanan bawah. Pada contoh, robot harus merobohkan <strong>2</strong> tembok, misalnya (2, 2) lalu (3, 3) lewat petak kosong (3, 2) di antaranya.</p>
TXT],
    ];

    $b01Js = <<<'JS'
const [R, C] = readInts();
const g = [];
for (let i = 0; i < R; i++) g.push(readLine().trim());

const INF = 1e9;
const dist = Array.from({ length: R }, () => new Array(C).fill(INF));
dist[0][0] = g[0][0] === "#" ? 1 : 0;
// deque sederhana dengan array dua arah
const dq = [[0, 0]];
const D = [[-1, 0], [1, 0], [0, -1], [0, 1]];
while (dq.length) {
  const [r, c] = dq.shift();
  for (const [a, b] of D) {
    const nr = r + a, nc = c + b;
    if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue;
    const w = g[nr][nc] === "#" ? 1 : 0;
    if (dist[r][c] + w < dist[nr][nc]) {
      dist[nr][nc] = dist[r][c] + w;
      if (w === 0) dq.unshift([nr, nc]);
      else dq.push([nr, nc]);
    }
  }
}
console.log(dist[R - 1][C - 1]);
JS;

    $b01Py = <<<'PY'
import sys
from collections import deque
input = sys.stdin.readline

R, C = map(int, input().split())
g = [input().strip() for _ in range(R)]

INF = 10**9
dist = [[INF] * C for _ in range(R)]
dist[0][0] = 1 if g[0][0] == "#" else 0
dq = deque([(0, 0)])
while dq:
    r, c = dq.popleft()
    for dr, dc in ((-1, 0), (1, 0), (0, -1), (0, 1)):
        nr, nc = r + dr, c + dc
        if 0 <= nr < R and 0 <= nc < C:
            w = 1 if g[nr][nc] == "#" else 0
            if dist[r][c] + w < dist[nr][nc]:
                dist[nr][nc] = dist[r][c] + w
                if w == 0:
                    dq.appendleft((nr, nc))
                else:
                    dq.append((nr, nc))
print(dist[R - 1][C - 1])
PY;

    $b01State = <<<'CPP'
// Graph keadaan: simpul = (posisi, kuponSudahDipakai)
// Dari kota u ke v berbiaya w; satu kali saja boleh memakai kupon (biaya jadi w/2).
// dist[v][k] = biaya termurah sampai v dengan k kupon terpakai (k = 0 atau 1)
typedef tuple<long long, int, int> T;           // (biaya, kota, k)
priority_queue<T, vector<T>, greater<T>> pq;
auto relaks = [&](int v, int k, long long biaya) {
    if (biaya < dist[v][k]) {
        dist[v][k] = biaya;
        pq.push(T(biaya, v, k));
    }
};
dist[s][0] = 0;
pq.push(T(0, s, 0));
while (!pq.empty()) {
    long long d; int u, k;
    tie(d, u, k) = pq.top();
    pq.pop();
    if (d > dist[u][k]) continue;
    for (auto& e : adj[u]) {
        int v = e.first;
        long long w = e.second;
        relaks(v, k, d + w);                      // tanpa kupon
        if (k == 0) relaks(v, 1, d + w / 2);      // pakai kupon sekarang
    }
}
// jawaban = min(dist[t][0], dist[t][1])
CPP;

    $b01Multi = <<<'CPP'
// BFS multi-sumber: semua sumber masuk antrian dengan jarak 0 sekaligus.
// Hasil: dist[v] = jarak ke sumber TERDEKAT. Total tetap O(N + M).
for (int s : sumber) {
    dist[s] = 0;
    q.push(s);
}
// ... lanjutkan BFS biasa
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memilih algoritma jalur terpendek yang tepat berdasarkan <strong>jenis bobot</strong> sisi.</li>
        <li>Menjalankan <strong>BFS 0-1</strong> dengan deque untuk graph berbobot 0 dan 1 dalam O(N + M).</li>
        <li>Melihat <strong>graph tersembunyi</strong> di soal grid dan soal "keadaan" (posisi + informasi tambahan).</li>
        <li>Membangun <strong>graph keadaan</strong> untuk soal dengan kupon, kunci, atau batasan langkah.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'memilih algoritma jalur terpendek', 'desc' => 'Mengapa BFS tidak selalu cukup, dan apa yang bisa dilakukan deque.'])

<section class="lesson-section" id="pilih" data-toc="Memilih Algoritma">
    <h2>BFS, BFS 0-1, atau Dijkstra?</h2>
    <div class="prose">
        <p>Semua algoritma jalur terpendek memproses simpul <strong>berurutan menurut jaraknya</strong>. Yang berbeda hanyalah cara menjaga urutan itu, dan itu bergantung pada jenis bobot sisi:</p>
    </div>
    <table class="cx-table">
        <tr><th>Bobot sisi</th><th>Algoritma</th><th>Struktur data</th><th>Waktu</th></tr>
        <tr><td>Semua sama (misalnya 1)</td><td>BFS</td><td><code>queue</code></td><td><code>O(N + M)</code></td></tr>
        <tr><td>Hanya 0 atau 1</td><td>BFS 0-1</td><td><code>deque</code></td><td><code>O(N + M)</code></td></tr>
        <tr><td>Bilangan tak negatif</td><td>Dijkstra</td><td><code>priority_queue</code></td><td><code>O((N + M) log N)</code></td></tr>
        <tr><td>Bisa negatif</td><td>Bellman-Ford</td><td>–</td><td><code>O(N · M)</code></td></tr>
        <tr><td>Semua pasangan, N kecil</td><td>Floyd-Warshall</td><td>matriks</td><td><code>O(N³)</code></td></tr>
    </table>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Pada BFS, antrian berisi simpul dengan jarak <strong>d</strong> lalu <strong>d + 1</strong>. Jika ada sisi berbobot 0, simpul baru berjarak <strong>d</strong> juga; ia harus diproses sebelum yang berjarak d + 1. Solusinya: taruh di <strong>depan</strong> antrian. Itulah BFS 0-1.</p>
    </div>
</section>

<section class="lesson-section" id="tersembunyi" data-toc="Graph Tersembunyi">
    <h2>Graph yang Tersembunyi di Soal</h2>
    <div class="prose">
        <p>Banyak soal tidak menyebut kata "graph" sama sekali. Tanyakan dua hal: <strong>apa yang menjadi simpul</strong> (keadaan), dan <strong>langkah apa</strong> yang memindahkan dari satu keadaan ke keadaan lain (sisi)?</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Grid</h4><p>Simpul = petak (r, c). Sisi = gerak ke 4 tetangga. Bobot bisa bergantung pada petak tujuan.</p></div>
        <div class="pattern"><h4>Angka</h4><p>"Dari x, boleh ×2 atau −1, minimal berapa langkah ke y?" Simpul = bilangan.</p></div>
        <div class="pattern"><h4>Teka-teki</h4><p>Simpul = susunan papan (misalnya puzzle geser). Sisi = satu gerakan.</p></div>
        <div class="pattern"><h4>Keadaan + info</h4><p>Simpul = (posisi, kunci yang dibawa) atau (kota, kupon terpakai). Banyak simpul = posisi × info.</p></div>
    </div>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Graph tersembunyi biasanya tidak perlu disimpan dalam adjacency list. Tetangga dihitung langsung saat simpul diproses, sehingga memori tetap kecil.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose">
        <p>Jalankan BFS 0-1 pada graph di visualisasi (mulai simpul 1). Isi deque setelah setiap simpul diproses:</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Ambil</th><th>Perubahan dist</th><th>Deque sesudahnya</th></tr>
            <tr><td>1 (0)</td><td>3 ← 0 (bobot 0, depan), 2 ← 1 (belakang)</td><td><code>[3, 2]</code></td></tr>
            <tr class="hl"><td>3 (0)</td><td>4 ← 1, 6 ← 1 (belakang)</td><td><code>[2, 4, 6]</code></td></tr>
            <tr><td>2 (1)</td><td>4 lewat sisi 0: tetap 1. 5 ← 2</td><td><code>[4, 6, 5]</code></td></tr>
            <tr class="hl"><td>4 (1)</td><td>5 ← 1 (bobot 0, depan)</td><td><code>[5, 6, 5]</code></td></tr>
            <tr><td>5 (1)</td><td>7 ← 2</td><td><code>[6, 5, 7]</code></td></tr>
            <tr class="ok"><td>6 (1)</td><td>7 ← 1 (bobot 0, depan)</td><td><code>[7, 5, 7]</code></td></tr>
        </table>
    </div>
    <div class="prose"><p>Perhatikan simpul 5: awalnya diberi jarak 2, lalu diperbaiki menjadi 1 dan masuk deque lagi. Entri lama yang tersisa tidak merusak hasil, karena saat diproses ulang tidak ada yang bisa diperbaiki.</p></div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi BFS 0-1</h2>
    <div class="prose"><p>Perhatikan panel Deque: simpul yang dicapai lewat sisi berbobot 0 langsung menyelip ke <strong>depan</strong>.</p></div>
    <div data-viz="bfs01"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Tentukan simpul</b><span>Apa yang perlu diingat agar langkah berikutnya bisa ditentukan? Itu keadaannya.</span></div>
        <div class="step-card"><b>Tentukan sisi dan bobotnya</b><span>Langkah apa saja yang boleh, dan berapa biayanya.</span></div>
        <div class="step-card"><b>Pilih algoritma</b><span>Bobot sama → BFS. 0/1 → deque. Tak negatif → Dijkstra.</span></div>
        <div class="step-card"><b>Hitung ukuran graph</b><span>Banyak keadaan × banyak langkah harus muat di batas waktu dan memori.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'BFS 0-1 di grid', 'desc' => 'Soal "robohkan tembok sesedikit mungkin" ditulis lengkap di C++.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Robohkan Tembok</h2>
    @include('lessons.walkthrough', [
        'title' => 'BFS 0-1 pada grid',
        'steps' => $b01Steps,
        'sample' => ['input' => "4 5\n..#..\n.##.#\n..##.\n##.#.\n", 'output' => "2\n"],
        'js' => $b01Js,
        'py' => $b01Py,
    ])
</section>

<section class="lesson-section" id="multi" data-toc="BFS Multi-Sumber">
    <h2>BFS Multi-Sumber</h2>
    <div class="prose">
        <p>"Untuk setiap petak, berapa jarak ke pos pemadam kebakaran <strong>terdekat</strong>?" Menjalankan BFS dari setiap pos terlalu lambat. Masukkan <strong>semua pos sekaligus</strong> ke antrian dengan jarak 0; BFS lalu menyebar dari semuanya bersamaan.</p>
    </div>
    @include('lessons.code', ['cpp' => $b01Multi, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Soal</th><th>Simpul</th><th>Waktu</th></tr>
        <tr><td>BFS 0-1 di grid R × C</td><td><code>R · C</code></td><td><code>O(R · C)</code></td></tr>
        <tr><td>Graph keadaan (kota, kupon)</td><td><code>2N</code></td><td><code>O((N + M) log N)</code></td></tr>
        <tr><td>Grid + K kunci</td><td><code>R · C · 2<sup>K</sup></code></td><td>BFS pada semua keadaan</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jangan menandai "sudah dikunjungi" saat masuk deque.</strong> Pada BFS 0-1 sebuah simpul bisa diperbaiki lagi lewat sisi 0. Gunakan pemeriksaan <code>dist[u] + w &lt; dist[v]</code>, bukan array visited.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'graph keadaan', 'desc' => 'Menambah informasi ke dalam simpul, dan mengapa deque selalu terurut.'])

<section class="lesson-section" id="keadaan" data-toc="Graph Keadaan">
    <h2>Graph Keadaan (State Graph)</h2>
    <div class="prose">
        <p>Soal: perjalanan dari kota s ke t. Kamu punya <strong>satu kupon</strong> yang membuat satu ruas jalan berbiaya setengah. Posisi saja tidak cukup untuk menggambarkan keadaan, karena biaya selanjutnya bergantung pada apakah kupon sudah dipakai. Jadi simpulnya adalah <strong>(kota, k)</strong> dengan k ∈ {0, 1}.</p>
    </div>
    @include('lessons.code', ['cpp' => $b01State, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Polanya sama dengan DP: "apa yang harus diingat?" menentukan state. Bedanya, di sini urutan pemrosesan diatur oleh Dijkstra/BFS, bukan oleh loop.</p>
    </div>
</section>

<section class="lesson-section" id="bukti" data-toc="Mengapa Deque Terurut?">
    <h2>Mengapa Deque Selalu Terurut?</h2>
    <div class="proof">
        <p><strong>Invarian:</strong> isi deque dari depan ke belakang punya jarak tidak turun, dan selisih jarak depan dan belakang paling banyak 1. Jadi isinya selalu berbentuk <code>d, d, …, d, d+1, …, d+1</code>.</p>
        <p>Saat kita mengambil simpul berjarak d dari depan, tetangga lewat sisi 0 berjarak d (ditaruh di depan, tetap terurut), dan lewat sisi 1 berjarak d + 1 (ditaruh di belakang, tetap terurut). Invarian terjaga.</p>
        <p>Karena simpul selalu diambil dengan jarak terkecil, argumen kebenaran Dijkstra berlaku tanpa perubahan.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="Simpul lewat sisi berbobot 0 jaraknya sama dengan simpul yang sedang diproses, jadi harus diproses lebih dulu dari yang berjarak +1.">
        <p class="quiz-q">Pada BFS 0-1, simpul yang dicapai lewat sisi berbobot 0 dimasukkan ke…</p>
        <div class="quiz-options">
            <button class="quiz-option">Depan deque</button>
            <button class="quiz-option">Belakang deque</button>
            <button class="quiz-option">Tidak dimasukkan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Bobot 1, 2, dan 5 bukan hanya 0/1, dan semuanya tak negatif: Dijkstra.">
        <p class="quiz-q">Bobot sisi bisa 1, 2, atau 5. Algoritma apa yang tepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">BFS biasa</button>
            <button class="quiz-option">BFS 0-1</button>
            <button class="quiz-option">Dijkstra</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap kota punya 2 keadaan kupon (belum/sudah dipakai), jadi 2 × 1000 = 2000 simpul.">
        <p class="quiz-q">Graph punya 1000 kota dan kamu punya satu kupon. Berapa banyak simpul di graph keadaan?</p>
        <div class="quiz-options">
            <button class="quiz-option">1000</button>
            <button class="quiz-option">2000</button>
            <button class="quiz-option">1 000 000</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
