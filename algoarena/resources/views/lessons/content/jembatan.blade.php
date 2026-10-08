@php
    $brSteps = [
        ['Variabel global', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n, m, timer_ = 0;
vector<vector<int>> adj;
vector<int> tin, low;
vector<bool> artikulasi;
vector<pair<int, int>> jembatan;

CPP, <<<'TXT'
<ul>
    <li><code>tin[u]</code> = urutan waktu u pertama kali dikunjungi DFS (0, 1, 2, …).</li>
    <li><code>low[u]</code> = <code>tin</code> terkecil yang bisa dicapai dari subtree u di pohon DFS, dengan memakai paling banyak <strong>satu sisi balik</strong>.</li>
</ul>
TXT],
        ['Masuk simpul: catat tin dan low', <<<'CPP'
void dfs(int u, int p) {
    tin[u] = low[u] = timer_++;
    int anak = 0;
CPP, <<<'TXT'
<p>Awalnya u hanya bisa "mencapai" dirinya sendiri, jadi <code>low[u] = tin[u]</code>. <code>anak</code> menghitung banyak anak u di pohon DFS, khusus untuk menangani akar.</p>
TXT],
        ['Sisi balik: ke leluhur yang sudah dikunjungi', <<<'CPP'
    for (int v : adj[u]) {
        if (v == p) continue;
        if (tin[v] != -1) {
            low[u] = min(low[u], tin[v]);
        } else {
CPP, <<<'TXT'
<p>Lewati sisi ke orang tua (itu sisi yang baru saja kita pakai). Jika v sudah dikunjungi, u–v adalah <strong>sisi balik</strong>: dari u kita bisa "melompat" ke v yang lebih tinggi. Perbarui <code>low[u]</code> dengan <code>tin[v]</code>.</p>
<div class="wt-tip">Melewati orang tua dengan <code>v == p</code> salah jika ada sisi ganda u–v. Untuk graph dengan sisi ganda, lewati berdasarkan <em>indeks sisi</em>, bukan simpul.</div>
TXT],
        ['Sisi pohon: turun, lalu bandingkan', <<<'CPP'
            dfs(v, u);
            low[u] = min(low[u], low[v]);
            if (low[v] > tin[u]) jembatan.push_back({min(u, v), max(u, v)});
            if (p != 0 && low[v] >= tin[u]) artikulasi[u] = true;
            anak++;
        }
    }
CPP, <<<'TXT'
<p>Setelah anak v selesai, apa pun yang bisa dicapai subtree v juga bisa dicapai u, jadi <code>low[u] = min(low[u], low[v])</code>. Lalu dua pengecekan inti:</p>
<ul>
    <li><code>low[v] &gt; tin[u]</code>: subtree v tidak punya jalan kembali ke u <em>atau</em> di atasnya selain sisi u–v. Menghapus sisi itu memutus subtree v. Jadi u–v <strong>jembatan</strong>.</li>
    <li><code>low[v] ≥ tin[u]</code>: subtree v tidak bisa melewati u untuk naik lebih tinggi. Menghapus <em>simpul</em> u memutus subtree v. Jadi u <strong>titik artikulasi</strong> (kecuali u akar).</li>
</ul>
TXT],
        ['Kasus khusus akar', <<<'CPP'
    if (p == 0 && anak > 1) artikulasi[u] = true;
}

CPP, <<<'TXT'
<p>Akar tidak punya simpul di atasnya, jadi syarat <code>low[v] ≥ tin[u]</code> selalu benar dan tidak berarti apa-apa. Akar adalah titik artikulasi jika punya <strong>lebih dari satu anak</strong> di pohon DFS: anak-anak itu hanya terhubung lewat akar.</p>
TXT],
        ['Membaca graph dan menjalankan DFS', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> m;
    adj.assign(n + 1, {});
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
    tin.assign(n + 1, -1);
    low.assign(n + 1, -1);
    artikulasi.assign(n + 1, false);
    for (int v = 1; v <= n; v++)
        if (tin[v] == -1) dfs(v, 0);
CPP, <<<'TXT'
<p>Graph tak berarah, mungkin tidak terhubung. DFS dimulai dari setiap simpul yang belum dikunjungi; masing-masing menjadi akar pohon DFS-nya sendiri.</p>
TXT],
        ['Mencetak hasil', <<<'CPP'

    sort(jembatan.begin(), jembatan.end());
    cout << jembatan.size() << '\n';
    for (auto& e : jembatan) cout << e.first << ' ' << e.second << '\n';
    vector<int> titik;
    for (int v = 1; v <= n; v++) if (artikulasi[v]) titik.push_back(v);
    cout << titik.size() << '\n';
    for (int i = 0; i < (int)titik.size(); i++) cout << titik[i] << (i + 1 < (int)titik.size() ? ' ' : '\n');
    return 0;
}
CPP, <<<'TXT'
<p>Cetak banyak jembatan beserta daftarnya (terurut), lalu banyak titik artikulasi beserta daftarnya.</p>
<pre>Contoh: segitiga 1-2-3, sisi 3-4, segitiga 4-5-6, sisi 6-7, sisi 7-8
jembatan: 3-4, 6-7, 7-8
artikulasi: 3, 4, 6, 7</pre>
TXT],
    ];

    $brPy = <<<'PY'
import sys
sys.setrecursionlimit(10000)

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
for _ in range(m):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

tin = [-1] * (n + 1)
low = [-1] * (n + 1)
art = [False] * (n + 1)
jembatan = []
timer = 0

def dfs(u, p):
    global timer
    tin[u] = low[u] = timer
    timer += 1
    anak = 0
    for v in adj[u]:
        if v == p:
            continue
        if tin[v] != -1:
            low[u] = min(low[u], tin[v])
        else:
            dfs(v, u)
            low[u] = min(low[u], low[v])
            if low[v] > tin[u]:
                jembatan.append((min(u, v), max(u, v)))
            if p != 0 and low[v] >= tin[u]:
                art[u] = True
            anak += 1
    if p == 0 and anak > 1:
        art[u] = True

for v in range(1, n + 1):
    if tin[v] == -1:
        dfs(v, 0)
jembatan.sort()
print(len(jembatan))
for a, b in jembatan:
    print(a, b)
titik = [v for v in range(1, n + 1) if art[v]]
print(len(titik))
print(*titik)
PY;

    $brBrute = <<<'CPP'
// Cara lambat O(M · (N + M)): hapus setiap sisi satu per satu, lalu cek keterhubungan.
// Berguna untuk MENGUJI solusi cepat pada graph kecil.
for (int i = 0; i < m; i++) {
    int komponenTanpaSisi = hitungKomponen(/* abaikan sisi ke-i */ i);
    if (komponenTanpaSisi > komponenAwal) cout << "sisi " << i << " jembatan\n";
}
CPP;

    $brTwoEdge = <<<'CPP'
// Komponen 2-edge-connected: buang semua jembatan, sisanya adalah "pulau"
// yang tetap terhubung walaupun satu sisi mana pun putus.
// Pulau-pulau + jembatan membentuk sebuah POHON (bridge tree).
vector<int> pulau(n + 1, -1);
int k = 0;
for (int s = 1; s <= n; s++) {
    if (pulau[s] != -1) continue;
    // BFS/DFS dari s tanpa melewati jembatan
    k++;
    // ...
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan arti <strong>jembatan</strong> dan <strong>titik artikulasi</strong> pada jaringan.</li>
        <li>Memahami pohon DFS, <strong>sisi balik</strong>, serta nilai <code>tin</code> dan <code>low</code>.</li>
        <li>Menemukan semua jembatan dan titik artikulasi dengan satu DFS, O(N + M).</li>
        <li>Membangun "pohon jembatan" untuk soal ketahanan jaringan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'titik rawan jaringan', 'desc' => 'Definisi dan cara berpikir dengan pohon DFS.'])

<section class="lesson-section" id="definisi" data-toc="Definisi">
    <h2>Jembatan dan Titik Artikulasi</h2>
    <div class="prose">
        <p>Bayangkan jaringan jalan antar desa. Sebuah <strong>jembatan</strong> adalah ruas jalan yang, jika rusak, membuat beberapa desa tidak bisa saling mencapai. <strong>Titik artikulasi</strong> adalah desa yang, jika ditutup beserta semua jalannya, memutus jaringan.</p>
        <p>Secara formal pada graph tak berarah: sisi e adalah jembatan jika menghapus e menambah banyak komponen terhubung. Simpul v adalah titik artikulasi jika menghapus v (dan sisi-sisinya) menambah banyak komponen.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 620, 'h' => 250,
        'nodes' => [[1, 60, 70], [2, 60, 200], [3, 170, 135, 'red'], [4, 300, 135, 'red'], [5, 400, 60], [6, 410, 210, 'red'], [7, 520, 135, 'red'], [8, 590, 220]],
        'edges' => [[1, 2], [2, 3], [3, 1], [3, 4, 'bad'], [4, 5], [5, 6], [6, 4], [6, 7, 'bad'], [7, 8, 'bad']],
        'caption' => 'Sisi merah adalah jembatan: 3–4, 6–7, dan 7–8. Simpul merah adalah titik artikulasi: 3, 4, 6, dan 7. Sisi di dalam segitiga bukan jembatan karena selalu ada jalan memutar.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Sebuah sisi adalah jembatan jika dan hanya jika sisi itu <strong>tidak berada di siklus mana pun</strong>. Jadi pertanyaannya menjadi: dari bagian bawah pohon DFS, adakah jalan memutar untuk kembali ke atas?</p>
    </div>
</section>

<section class="lesson-section" id="tinlow" data-toc="tin dan low">
    <h2>Pohon DFS, tin, dan low</h2>
    <div class="prose">
        <p>DFS pada graph tak berarah membagi sisi menjadi dua jenis:</p>
        <div class="term-grid">
            <div class="term"><b>Sisi pohon</b><span>Sisi yang dipakai DFS untuk turun ke simpul baru.</span></div>
            <div class="term"><b>Sisi balik</b><span>Sisi ke simpul yang sudah dikunjungi. Pada graph tak berarah, sisi seperti ini selalu menuju <strong>leluhur</strong> (tidak ada "sisi silang").</span></div>
        </div>
        <p><code>tin[u]</code> adalah waktu u dimasuki. <code>low[u]</code> adalah tin terkecil yang bisa dicapai dari subtree u dengan turun di pohon lalu memakai satu sisi balik. Jika <code>low[v]</code> masih lebih besar dari <code>tin[u]</code>, berarti subtree v sama sekali tidak bisa naik melewati sisi u–v.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Simpul</th><th>tin</th><th>low</th><th>Alasan low</th></tr>
            <tr><td>1</td><td>0</td><td>0</td><td>akar</td></tr>
            <tr><td>2</td><td>1</td><td>0</td><td>lewat anak 3 yang punya sisi balik 3–1</td></tr>
            <tr><td>3</td><td>2</td><td>0</td><td>sisi balik 3–1 (tin 0)</td></tr>
            <tr class="hl"><td>4</td><td>3</td><td>3</td><td>tidak ada jalan ke atas 4 → <b>3–4 jembatan</b></td></tr>
            <tr><td>5</td><td>4</td><td>3</td><td>lewat anak 6 yang punya sisi balik 6–4</td></tr>
            <tr><td>6</td><td>5</td><td>3</td><td>sisi balik 6–4 (tin 3)</td></tr>
            <tr class="hl"><td>7</td><td>6</td><td>6</td><td><b>6–7 jembatan</b></td></tr>
            <tr class="hl"><td>8</td><td>7</td><td>7</td><td><b>7–8 jembatan</b></td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Label setiap simpul berbentuk <code>tin/low</code>. Perhatikan bagaimana <code>low</code> mengecil saat sisi balik (garis putus-putus biru) ditemukan, lalu "naik" ke orang tua saat DFS kembali.</p></div>
    <div data-viz="bridges"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>DFS dengan tin/low</b><span>Satu DFS dari setiap simpul yang belum dikunjungi.</span></div>
        <div class="step-card"><b>Sisi balik</b><span><code>low[u] = min(low[u], tin[v])</code>.</span></div>
        <div class="step-card"><b>Setelah anak selesai</b><span><code>low[u] = min(low[u], low[v])</code>, lalu cek jembatan (<code>&gt;</code>) dan artikulasi (<code>≥</code>).</span></div>
        <div class="step-card"><b>Akar</b><span>Artikulasi jika punya lebih dari satu anak di pohon DFS.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Mencari semua jembatan dan titik artikulasi dalam satu DFS.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Jembatan & titik artikulasi',
        'steps' => $brSteps,
        'sample' => ['input' => "8 9\n1 2\n2 3\n3 1\n3 4\n4 5\n5 6\n6 4\n6 7\n7 8\n", 'output' => "3\n3 4\n6 7\n7 8\n4\n3 4 6 7\n"],
        'py' => $brPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th></tr>
        <tr><td>Hapus setiap sisi lalu cek keterhubungan</td><td><code>O(M · (N + M))</code></td></tr>
        <tr><td>DFS dengan tin/low</td><td><code>O(N + M)</code></td></tr>
    </table>
    @include('lessons.code', ['cpp' => $brBrute, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>&gt; untuk jembatan, ≥ untuk artikulasi.</strong> Tertukar sedikit saja, hasilnya salah. Jembatan butuh subtree yang bahkan tidak bisa kembali ke u; artikulasi cukup tidak bisa melewati u.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Sisi ganda.</strong> Jika ada dua sisi u–v, keduanya bukan jembatan. Pengecekan <code>v == p</code> akan salah melewati sisi kedua; simpan indeks sisi dan lewati hanya sisi yang sama.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'pohon jembatan', 'desc' => 'Komponen 2-edge-connected dan bukti kriteria low.'])

<section class="lesson-section" id="pulau" data-toc="Pohon Jembatan">
    <h2>Komponen 2-Edge-Connected</h2>
    <div class="prose">
        <p>Hapus semua jembatan. Setiap komponen yang tersisa disebut komponen <strong>2-edge-connected</strong>: di dalamnya, putusnya satu sisi mana pun tidak memutus jaringan. Jika setiap komponen dijadikan satu simpul dan jembatan sebagai sisi, hasilnya selalu sebuah <strong>pohon</strong>. Banyak soal ketahanan jaringan menjadi soal pohon setelah transformasi ini.</p>
    </div>
    @include('lessons.code', ['cpp' => $brTwoEdge, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="bukti" data-toc="Mengapa Kriterianya Benar?">
    <h2>Mengapa low[v] &gt; tin[u] Berarti Jembatan?</h2>
    <div class="proof">
        <p>Misalkan u–v sisi pohon (u orang tua v). Simpul di subtree v hanya bisa terhubung ke luar subtree lewat sisi pohon u–v atau lewat sisi balik ke leluhur. Pada graph tak berarah tidak ada sisi silang antar cabang.</p>
        <p>Jika <code>low[v] &gt; tin[u]</code>, tidak ada sisi balik dari subtree v yang menuju u atau leluhurnya; semua sisi baliknya tetap di dalam subtree v. Menghapus u–v memutus subtree v. Sebaliknya, jika <code>low[v] ≤ tin[u]</code>, ada sisi balik dari subtree v ke u atau di atasnya, yang bersama jalur pohon membentuk siklus berisi u–v, jadi u–v bukan jembatan.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Ruas kritis</h4><p>Daftar jalan yang jika putus memisahkan kota: jembatan.</p></div>
        <div class="pattern"><h4>Server kritis</h4><p>Komputer yang jika mati memisahkan jaringan: titik artikulasi.</p></div>
        <div class="pattern"><h4>Pohon jembatan</h4><p>Ciutkan komponen 2-edge-connected, lalu kerjakan soal di pohon.</p></div>
        <div class="pattern"><h4>Menambah sisi</h4><p>Sisi minimum agar tidak ada jembatan: (banyak daun pohon jembatan + 1) / 2.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap sisi di sebuah siklus punya jalan memutar, jadi tidak ada jembatan pada graph berbentuk satu siklus.">
        <p class="quiz-q">Graph berbentuk satu siklus 1–2–3–4–1. Ada berapa jembatan?</p>
        <div class="quiz-options">
            <button class="quiz-option">4</button>
            <button class="quiz-option">0</button>
            <button class="quiz-option">1</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Pada pohon tidak ada siklus, jadi setiap sisi adalah jembatan.">
        <p class="quiz-q">Pada sebuah pohon dengan N simpul, berapa banyak jembatan?</p>
        <div class="quiz-options">
            <button class="quiz-option">N − 1 (semua sisi)</button>
            <button class="quiz-option">0</button>
            <button class="quiz-option">Bergantung pada akarnya</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Akar tidak punya leluhur, jadi kriteria low[v] ≥ tin[u] selalu terpenuhi. Akar artikulasi hanya jika punya ≥ 2 anak di pohon DFS.">
        <p class="quiz-q">Mengapa akar DFS diperlakukan khusus untuk titik artikulasi?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena tin akar selalu 1</button>
            <button class="quiz-option">Karena akar tidak pernah artikulasi</button>
            <button class="quiz-option">Karena syarat low[v] ≥ tin[akar] selalu benar</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
