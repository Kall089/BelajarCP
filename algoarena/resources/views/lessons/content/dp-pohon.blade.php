@php
    $dtSteps = [
        ['Membaca pohon dan nilai', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> nilai(n + 1);
    for (int i = 1; i <= n; i++) cin >> nilai[i];
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
CPP, <<<'TXT'
<p>Perusahaan punya N karyawan dengan struktur atasan–bawahan berbentuk pohon; karyawan 1 adalah direktur (akar). Karyawan i punya nilai keceriaan <code>nilai[i]</code>.</p>
<p>Seseorang tidak mau datang ke pesta jika <strong>atasan langsungnya</strong> juga datang. Pilih tamu agar total keceriaan maksimum.</p>
TXT],
        ['Urutan BFS dari akar', <<<'CPP'

    vector<int> parent(n + 1, 0), urutan;
    urutan.push_back(1);
    parent[1] = -1;
    for (int h = 0; h < (int)urutan.size(); h++) {
        int u = urutan[h];
        for (int v : adj[u])
            if (v != parent[u]) {
                parent[v] = u;
                urutan.push_back(v);
            }
    }
CPP, <<<'TXT'
<p>BFS dari akar memberi urutan di mana setiap orang tua muncul <strong>sebelum</strong> anaknya. Membacanya dari belakang memberi urutan "anak dulu, baru orang tua", yang kita butuhkan untuk DP dari bawah ke atas, tanpa rekursi.</p>
TXT],
        ['State: dua jawaban per simpul', <<<'CPP'

    // dp0[u] = total terbaik di subtree u jika u TIDAK diundang
    // dp1[u] = total terbaik di subtree u jika u DIUNDANG
    vector<long long> dp0(n + 1, 0), dp1(n + 1, 0);
CPP, <<<'TXT'
<p>Satu angka per simpul tidak cukup. Saat menghitung orang tua, kita perlu tahu jawaban terbaik anak <em>ketika anak itu tidak diundang</em> (karena orang tuanya mungkin diundang). Jadi setiap simpul menyimpan dua jawaban.</p>
<div class="wt-tip">Pola "tambah dimensi untuk keadaan simpul" sangat umum di DP pohon: dipilih/tidak, warna simpul, apakah sudah ada penjaga, dan sebagainya.</div>
TXT],
        ['Transisi dari bawah ke atas', <<<'CPP'

    for (int i = n - 1; i >= 0; i--) {
        int u = urutan[i];
        dp1[u] += nilai[u];
        if (parent[u] > 0) {
            int p = parent[u];
            dp0[p] += max(dp0[u], dp1[u]);
            dp1[p] += dp0[u];
        }
    }
CPP, <<<'TXT'
<p>Saat u diproses, semua anaknya sudah menyumbangkan nilainya ke <code>dp0[u]</code> dan <code>dp1[u]</code>. Tambahkan nilai u sendiri ke <code>dp1[u]</code>, lalu sumbangkan ke orang tuanya:</p>
<ul>
    <li>Jika orang tua <strong>tidak</strong> diundang, u bebas: ambil <code>max(dp0[u], dp1[u])</code>.</li>
    <li>Jika orang tua <strong>diundang</strong>, u tidak boleh datang: hanya <code>dp0[u]</code>.</li>
</ul>
<pre>daun 5 (nilai 4): dp0 = 0, dp1 = 4
simpul 2 (nilai 6), anak 5 & 6:
  dp0[2] = max(0,4) + max(0,5) = 9
  dp1[2] = 6 + 0 + 0 = 6</pre>
TXT],
        ['Jawaban di akar', <<<'CPP'

    cout << max(dp0[1], dp1[1]) << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Direktur boleh diundang atau tidak; ambil yang lebih baik. Pada contoh <code>max(17, 18) = 18</code>: undang direktur dan keempat "cucu"-nya. Seluruh program O(N).</p>
TXT],
    ];

    $dtPy = <<<'PY'
import sys
input = sys.stdin.readline

n = int(input())
nilai = [0] + list(map(int, input().split()))
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

parent = [0] * (n + 1)
parent[1] = -1
urutan = [1]
for u in urutan:
    for v in adj[u]:
        if v != parent[u]:
            parent[v] = u
            urutan.append(v)

dp0 = [0] * (n + 1)
dp1 = [0] * (n + 1)
for u in reversed(urutan):
    dp1[u] += nilai[u]
    p = parent[u]
    if p > 0:
        dp0[p] += max(dp0[u], dp1[u])
        dp1[p] += dp0[u]
print(max(dp0[1], dp1[1]))
PY;

    $dtCount = <<<'CPP'
// Banyak cara memilih himpunan simpul tanpa dua simpul bertetangga (mod MOD).
// Sama persis, hanya max diganti penjumlahan dan + diganti perkalian.
for (int u : urutanTerbalik) {
    cnt0[u] = cnt1[u] = 1;
    for (int v : anak[u]) {
        cnt0[u] = cnt0[u] * ((cnt0[v] + cnt1[v]) % MOD) % MOD;   // anak bebas
        cnt1[u] = cnt1[u] * cnt0[v] % MOD;                       // anak tidak dipilih
    }
}
// jawaban = (cnt0[1] + cnt1[1]) % MOD
CPP;

    $dtPairs = <<<'CPP'
// Jumlah jarak SEMUA pasangan simpul, O(N), dengan teknik kontribusi:
// sisi (u, parent[u]) dilewati oleh setiap pasangan dengan satu simpul
// di subtree u dan satu di luar → sz[u] * (n - sz[u]) pasangan.
long long total = 0;
for (int v = 2; v <= n; v++) total += 1LL * sz[v] * (n - sz[v]);
CPP;

    $dtKnap = <<<'CPP'
// DP pohon + knapsack: pilih tepat K simpul yang membentuk subtree terhubung
// berisi akar, maksimalkan nilai. dp[u][k] = terbaik di subtree u, ambil k simpul, u diambil.
// Menggabungkan anak satu per satu: total kerja O(N²) (bukan O(N³)) jika
// loop dibatasi oleh ukuran subtree yang sudah digabung.
void dfs(int u, int p) {
    sz[u] = 1;
    dp[u][1] = nilai[u];
    for (int v : adj[u]) if (v != p) {
        dfs(v, u);
        vector<long long> baru(sz[u] + sz[v] + 1, -INF);
        for (int a = 1; a <= sz[u]; a++)
            for (int b = 0; b <= sz[v]; b++)
                baru[a + b] = max(baru[a + b], dp[u][a] + (b ? dp[v][b] : 0));
        sz[u] += sz[v];
        dp[u] = baru;
    }
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan mengapa pohon sangat cocok untuk DP: subtree adalah subsoal yang <strong>tidak saling tumpang tindih</strong>.</li>
        <li>Merancang state <code>dp[u][keadaan]</code> dan menggabungkan jawaban anak-anak ke orang tua.</li>
        <li>Menulis DP pohon tanpa rekursi dengan urutan BFS terbalik.</li>
        <li>Memakai variasinya: menghitung cara, teknik kontribusi, dan knapsack di pohon.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'subtree sebagai subsoal', 'desc' => 'Soal undangan pesta dan cara berpikir dua keadaan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Undangan Pesta Kantor</h2>
    <div class="prose">
        <p>Pada DP biasa, subsoal adalah awalan array atau sel tabel. Pada pohon, subsoal yang paling alami adalah <strong>subtree</strong>: jawaban untuk subtree u hanya bergantung pada jawaban subtree anak-anaknya, dan subtree anak-anak itu tidak saling berbagi simpul.</p>
        <p>Soal: setiap karyawan punya nilai keceriaan. Karyawan tidak mau datang jika atasan langsungnya datang. Siapa yang diundang agar total keceriaan maksimum?</p>
        <p>Memilih yang nilainya terbesar dulu (serakah) gagal: mengundang satu atasan bernilai 6 bisa membuat tiga bawahan bernilai 5 tidak bisa datang. Kita perlu mempertimbangkan dua kemungkinan untuk setiap orang.</p>
    </div>
    <div class="recurrence"><small>State dan transisi</small>dp[u][0] = terbaik di subtree u, u TIDAK diundang
dp[u][1] = terbaik di subtree u, u DIUNDANG

dp[u][0] = Σ max(dp[v][0], dp[v][1])     untuk setiap anak v
dp[u][1] = nilai[u] + Σ dp[v][0]          untuk setiap anak v
jawaban  = max(dp[akar][0], dp[akar][1])</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Resep DP pohon: (1) apa yang perlu diketahui orang tua tentang anaknya? Itu menjadi <em>keadaan</em> tambahan. (2) Gabungkan anak satu per satu. (3) Hitung dari daun naik ke akar.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Pohon dari visualisasi: 1 punya anak 2, 3, 4; anak 2 adalah 5, 6; anak 4 adalah 7, 8. Nilai: 1→3, 2→6, 3→2, 4→1, 5→4, 6→5, 7→3, 8→3.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Simpul (nilai)</th><th>dp[u][0] (tidak diundang)</th><th>dp[u][1] (diundang)</th></tr>
            <tr><td>5 (4), 6 (5), 7 (3), 8 (3), 3 (2)</td><td>0</td><td>nilainya sendiri</td></tr>
            <tr class="hl"><td>2 (6)</td><td>max(0,4) + max(0,5) = 9</td><td>6 + 0 + 0 = 6</td></tr>
            <tr class="hl"><td>4 (1)</td><td>max(0,3) + max(0,3) = 6</td><td>1 + 0 + 0 = 1</td></tr>
            <tr class="ok"><td>1 (3)</td><td>max(9,6) + max(0,2) + max(6,1) = 17</td><td>3 + 9 + 0 + 6 = <span class="new">18</span></td></tr>
        </table>
    </div>
    <div class="prose"><p>Jawaban 18: undang 1, 5, 6, 7, 8. Perhatikan bahwa dp[1][1] memakai dp[2][0] = 9, yaitu jawaban subtree 2 <em>saat 2 tidak diundang</em>, yang otomatis mengundang 5 dan 6.</p></div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi DP Pohon</h2>
    <div class="prose"><p>Label setiap simpul berubah dari nilainya menjadi <code>dp[u][0] / dp[u][1]</code> saat subtree-nya selesai. Di akhir, simpul hijau menunjukkan tamu yang diundang.</p></div>
    <div data-viz="dp-tree"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Akarkan pohon</b><span>Pilih akar, hitung parent dan urutan (DFS/BFS).</span></div>
        <div class="step-card"><b>Tentukan keadaan</b><span>Apa yang membatasi pilihan anak, bergantung pada keputusan di u?</span></div>
        <div class="step-card"><b>Base case di daun</b><span>Daun tidak punya anak; nilainya langsung diketahui.</span></div>
        <div class="step-card"><b>Gabungkan anak</b><span>Penjumlahan (max total), perkalian (hitung cara), atau knapsack (distribusi jatah).</span></div>
        <div class="step-card"><b>Jawaban</b><span>Di akar, ambil keadaan terbaik.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'DP pohon di C++', 'desc' => 'Program lengkap tanpa rekursi, aman untuk pohon yang sangat dalam.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Undangan Pesta</h2>
    @include('lessons.walkthrough', [
        'title' => 'Maximum weight independent set pada pohon',
        'steps' => $dtSteps,
        'sample' => ['input' => "8\n3 6 2 1 4 5 3 3\n1 2\n1 3\n1 4\n2 5\n2 6\n4 7\n4 8\n", 'output' => "18\n"],
        'py' => $dtPy,
    ])
</section>

<section class="lesson-section" id="variasi" data-toc="Variasi DP Pohon">
    <h2>Variasi DP Pohon</h2>
    <div class="prose">
        <h3>Menghitung banyak cara</h3>
        <p>"Ada berapa himpunan simpul tanpa dua simpul bertetangga?" Strukturnya sama; anak-anak saling bebas sehingga jumlah cara <strong>dikalikan</strong> (aturan perkalian).</p>
    </div>
    @include('lessons.code', ['cpp' => $dtCount, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>Teknik kontribusi</h3>
        <p>Daripada menghitung setiap pasangan, hitung berapa kali setiap <strong>sisi</strong> dipakai.</p>
    </div>
    @include('lessons.code', ['cpp' => $dtPairs, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Jenis DP pohon</th><th>Waktu</th></tr>
        <tr><td>Keadaan konstan per simpul (dipilih/tidak)</td><td><code>O(N)</code></td></tr>
        <tr><td>Knapsack di pohon (dp[u][k])</td><td><code>O(N²)</code> dengan batas ukuran subtree</td></tr>
        <tr><td>Rerooting</td><td><code>O(N)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Nilai negatif.</strong> Jika nilai keceriaan bisa negatif, <code>dp[u][1]</code> mungkin lebih buruk dari tidak mengundang siapa pun. Rumus max di atas tetap benar karena selalu membandingkan kedua pilihan.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Akar yang salah.</strong> Jika soal menyebut akarnya (misalnya direktur bukan nomor 1), pastikan DFS/BFS dimulai dari situ: arah "atasan" bergantung pada akar.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'knapsack di pohon', 'desc' => 'Menggabungkan tabel anak dan analisis O(N²).'])

<section class="lesson-section" id="knapsack" data-toc="Knapsack di Pohon">
    <h2>Knapsack di Pohon</h2>
    <div class="prose">
        <p>Jika keadaan simpul berupa <em>angka</em> (misalnya "berapa simpul yang diambil di subtree ini"), menggabungkan dua anak menjadi seperti knapsack: bagi jatah k antara subtree yang sudah digabung dan anak baru.</p>
    </div>
    @include('lessons.code', ['cpp' => $dtKnap, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Mengapa totalnya O(N²), bukan O(N³)?</strong> Loop ganda saat menggabungkan anak v ke u berjalan sz[u] × sz[v] kali. Bayangkan setiap pasangan simpul (x, y) dengan x di bagian yang sudah digabung dan y di subtree v: pasangan itu hanya "dihitung" sekali, tepat di LCA mereka. Karena ada paling banyak N² pasangan, total kerjanya O(N²).</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Dipilih / tidak</h4><p>Independent set, vertex cover, dominating set (3 keadaan).</p></div>
        <div class="pattern"><h4>Hitung cara</h4><p>Anak saling bebas → kalikan.</p></div>
        <div class="pattern"><h4>Kontribusi sisi</h4><p>sz[v] · (n − sz[v]) untuk jumlah jarak semua pasangan.</p></div>
        <div class="pattern"><h4>Rerooting</h4><p>Jawaban untuk setiap akar dengan dua penjelajahan.</p></div>
        <div class="pattern"><h4>Knapsack pohon</h4><p>Gabungkan tabel anak, O(N²).</p></div>
        <div class="pattern"><h4>Diameter dengan DP</h4><p>Di setiap simpul, dua tinggi anak terbesar + 2.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Jika u diundang, anak-anaknya tidak boleh datang, jadi yang dipakai adalah dp[v][0].">
        <p class="quiz-q">Saat menghitung dp[u][1] (u diundang), nilai apa yang diambil dari anak v?</p>
        <div class="quiz-options">
            <button class="quiz-option">max(dp[v][0], dp[v][1])</button>
            <button class="quiz-option">dp[v][1]</button>
            <button class="quiz-option">dp[v][0]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Anak-anak saling bebas: setiap kombinasi pilihan anak 1 bisa dipasangkan dengan setiap kombinasi pilihan anak 2, jadi dikalikan.">
        <p class="quiz-q">Saat menghitung <strong>banyak cara</strong>, jawaban anak-anak digabung dengan…</p>
        <div class="quiz-options">
            <button class="quiz-option">Perkalian</button>
            <button class="quiz-option">Penjumlahan</button>
            <button class="quiz-option">Maksimum</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Urutan BFS menempatkan orang tua sebelum anak. Dibaca terbalik, semua anak diproses sebelum orang tuanya.">
        <p class="quiz-q">Tanpa rekursi, dalam urutan apa simpul diproses untuk DP dari bawah ke atas?</p>
        <div class="quiz-options">
            <button class="quiz-option">Urutan BFS dari akar</button>
            <button class="quiz-option">Urutan BFS dari akar, dibalik</button>
            <button class="quiz-option">Urutan nomor simpul</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
