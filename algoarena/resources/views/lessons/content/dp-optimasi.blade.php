@php
    $opSteps = [
        ['Membaca input', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> c(n + 1);
    for (int i = 1; i <= n; i++) cin >> c[i];

CPP, <<<'TXT'
<p>Ada N batu; batu ke-i berbiaya <code>c[i]</code>. Katak mulai di batu 1, harus mendarat di batu N, dan setiap lompatan maju 1 sampai K batu. Biaya total = jumlah biaya batu tempat ia mendarat (termasuk batu 1 dan N).</p>
<p>State: <code>dp[i]</code> = biaya termurah untuk sampai di batu i. Transisi: <code>dp[i] = c[i] + min(dp[i−K], …, dp[i−1])</code>.</p>
TXT],
        ['Base case dan deque', <<<'CPP'
    vector<long long> dp(n + 1);
    deque<int> dq;
    dp[1] = c[1];
    dq.push_back(1);
CPP, <<<'TXT'
<p>Deque menyimpan <strong>indeks</strong> j (bukan nilai) yang masih mungkin menjadi minimum jendela. Invarian yang dijaga:</p>
<ul>
    <li>indeks di deque terurut naik dari depan ke belakang (yang lebih tua di depan);</li>
    <li><code>dp</code> dari indeks-indeks itu juga <strong>naik</strong>, sehingga minimum selalu di <strong>depan</strong>.</li>
</ul>
TXT],
        ['Buang yang keluar jendela, ambil minimum', <<<'CPP'
    for (int i = 2; i <= n; i++) {
        while (dq.front() < i - k) dq.pop_front();
        dp[i] = c[i] + dp[dq.front()];
CPP, <<<'TXT'
<p>Batu yang bisa melompat ke i adalah j ∈ [i − K, i − 1]. Indeks di depan yang lebih kecil dari i − K sudah terlalu jauh: buang. Setelah itu depan deque adalah j dengan dp terkecil di jendela, jadi transisi cukup O(1).</p>
<div class="wt-tip">Deque tidak pernah kosong di sini: indeks i − 1 baru saja dimasukkan dan selalu berada di jendela.</div>
TXT],
        ['Buang yang kalah bersaing, lalu masukkan i', <<<'CPP'
        while (!dq.empty() && dp[dq.back()] >= dp[i]) dq.pop_back();
        dq.push_back(i);
    }
    cout << dp[n] << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Jika j di belakang deque punya <code>dp[j] ≥ dp[i]</code>, maka j tidak akan pernah menjadi minimum lagi: i lebih muda (bertahan lebih lama di jendela) dan tidak lebih mahal. Buang j, lalu masukkan i di belakang. Inilah yang menjaga dp di deque tetap naik.</p>
<p>Setiap indeks masuk deque sekali dan keluar paling banyak sekali, sehingga semua <code>while</code> totalnya O(N).</p>
TXT],
    ];

    $opPy = <<<'PY'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
c = [0] + list(map(int, input().split()))
dp = [0] * (n + 1)
dp[1] = c[1]
dq = deque([1])
for i in range(2, n + 1):
    while dq[0] < i - k:
        dq.popleft()
    dp[i] = c[i] + dp[dq[0]]
    while dq and dp[dq[-1]] >= dp[i]:
        dq.pop()
    dq.append(i)
print(dp[n])
PY;

    $opPrefix = <<<'CPP'
// Banyak cara naik ke anak tangga n jika sekali melangkah boleh 1..K anak tangga.
// dp[i] = dp[i-1] + dp[i-2] + ... + dp[i-K]: O(N·K) jika dijumlahkan satu per satu.
// pre[i] = dp[0] + dp[1] + ... + dp[i], sehingga jumlah satu rentang = selisih dua prefix.
const long long MOD = 1e9 + 7;
vector<long long> dp(n + 1), pre(n + 1);
dp[0] = pre[0] = 1;
for (int i = 1; i <= n; i++) {
    long long kanan = pre[i - 1];                          // dp[0..i-1]
    long long kiri = (i - k - 1 >= 0) ? pre[i - k - 1] : 0; // dp[0..i-k-1]
    dp[i] = (kanan - kiri + MOD) % MOD;      // + MOD: selisih modulo bisa negatif!
    pre[i] = (pre[i - 1] + dp[i]) % MOD;
}
CPP;

    $opSeparable = <<<'CPP'
// Pos istirahat di posisi x[1] < x[2] < ... < x[n], keindahan b[i].
// Pendaki berhenti di beberapa pos (mulai di pos 1); berjalan d meter menghabiskan C·d energi.
// dp[i] = b[i] + max_{j < i} (dp[j] - C·(x[i] - x[j]))
//       = b[i] - C·x[i] + max_{j < i} (dp[j] + C·x[j])     <- bagian j berdiri sendiri!
long long terbaik = dp[1] + C * x[1];       // max (dp[j] + C·x[j]) untuk j yang sudah selesai
for (int i = 2; i <= n; i++) {
    dp[i] = b[i] - C * x[i] + terbaik;
    terbaik = max(terbaik, dp[i] + C * x[i]);
}
CPP;

    $opBinary = <<<'CPP'
// Proyek ke-i berlangsung dari hari mulai sampai hari selesai dan memberi untung.
// Dua proyek tidak boleh berbagi hari. Pilih proyek dengan total untung terbesar.
struct Proyek { long long mulai, selesai, untung; };
sort(p.begin(), p.end(), [](const Proyek& a, const Proyek& b) { return a.selesai < b.selesai; });
vector<long long> selesai(n), dp(n + 1, 0);    // dp[i] = terbaik memakai proyek 1..i
for (int i = 0; i < n; i++) selesai[i] = p[i].selesai;
for (int i = 1; i <= n; i++) {
    // j = banyak proyek yang selesai SEBELUM proyek i dimulai (mereka proyek 1..j)
    int j = lower_bound(selesai.begin(), selesai.end(), p[i - 1].mulai) - selesai.begin();
    dp[i] = max(dp[i - 1],                     // proyek i tidak diambil
                dp[j] + p[i - 1].untung);      // diambil: sisanya dari proyek 1..j
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali kapan DP sudah benar tetapi <strong>transisinya</strong> terlalu lambat.</li>
        <li>Mempercepat transisi berbentuk jumlah rentang dengan <strong>prefix sum</strong>.</li>
        <li>Mempercepat minimum/maksimum di jendela geser dengan <strong>deque monoton</strong>, O(N) total.</li>
        <li>Memisahkan suku j dan suku i ("running best"), serta mencari transisi dengan <strong>binary search</strong>.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'transisi yang terlalu lambat', 'desc' => 'Empat bentuk transisi dan cara mempercepatnya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: DP yang Benar Tapi Lambat</h2>
    <div class="prose">
        <p>Waktu DP = <strong>banyak state × biaya satu transisi</strong>. Sering kali state-nya sudah tepat (misalnya hanya N state), tetapi setiap state melihat banyak state sebelumnya. Contoh katak yang bisa melompat 1 sampai K batu: <code>dp[i] = c[i] + min(dp[i−K..i−1])</code>. Dengan N = K = 2 · 10<sup>5</sup>, cara langsung butuh 4 · 10<sup>10</sup> langkah.</p>
        <p>Kuncinya: transisi untuk i dan untuk i + 1 hampir sama. Jendelanya hanya bergeser satu. Jangan menghitung ulang dari nol; pakai struktur yang bisa <em>diperbarui</em>.</p>
    </div>
    <table class="cx-table">
        <tr><th>Bentuk transisi</th><th>Teknik</th><th>Biaya per state</th></tr>
        <tr><td><code>Σ dp[j]</code> untuk j di sebuah rentang</td><td>Prefix sum</td><td>O(1)</td></tr>
        <tr><td><code>min / max dp[j]</code> untuk j di jendela geser</td><td>Deque monoton</td><td>O(1) rata-rata</td></tr>
        <tr><td><code>max (dp[j] + f(j)) + g(i)</code> untuk semua j &lt; i</td><td>Simpan nilai terbaik berjalan</td><td>O(1)</td></tr>
        <tr><td>dp[j] dengan j ditentukan syarat urutan (misal "selesai sebelum mulai")</td><td>Sorting + binary search</td><td>O(log N)</td></tr>
    </table>
</section>

<section class="lesson-section" id="prefix" data-toc="Prefix Sum">
    <h2>Pola 1: Jumlah Rentang dengan Prefix Sum</h2>
    <div class="prose">
        <p>Jika transisi menjumlahkan dp di sebuah rentang, simpan juga <code>pre[i] = dp[0] + … + dp[i]</code>. Jumlah dp[a..b] = <code>pre[b] − pre[a − 1]</code>, sehingga setiap state O(1) dan seluruh DP O(N).</p>
    </div>
    @include('lessons.code', ['cpp' => $opPrefix, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Dalam modulo, <code>pre[b] − pre[a − 1]</code> bisa negatif walaupun jumlah aslinya positif, karena keduanya sudah dipotong modulo. Tambahkan MOD sebelum <code>% MOD</code>.</p>
    </div>
</section>

<section class="lesson-section" id="deque" data-toc="Deque Monoton">
    <h2>Pola 2: Minimum Jendela dengan Deque Monoton</h2>
    <div class="prose">
        <p>Untuk <code>min(dp[i−K..i−1])</code>, perhatikan dua calon j<sub>1</sub> &lt; j<sub>2</sub> di jendela. Jika <code>dp[j<sub>1</sub>] ≥ dp[j<sub>2</sub>]</code>, maka j<sub>1</sub> <strong>tidak akan pernah</strong> menjadi minimum lagi: j<sub>2</sub> lebih murah (atau sama) dan akan bertahan di jendela lebih lama. j<sub>1</sub> boleh dibuang selamanya.</p>
        <p>Setelah semua calon yang "kalah bersaing" dibuang, calon yang tersisa punya dp yang <strong>naik</strong> menurut umurnya. Simpan dalam <code>deque</code>: minimum ada di depan, calon baru masuk dari belakang, dan calon yang terlalu tua keluar dari depan.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Untuk <strong>maksimum</strong> jendela, balik perbandingannya: buang belakang selama <code>dp[back] ≤ dp[i]</code>, sehingga isi deque turun dan maksimum di depan.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Garis hijau di bawah sel menandai indeks yang masih ada di deque. Perhatikan sel merah: indeks yang dibuang tidak pernah kembali, itulah sebabnya totalnya O(N). Ubah biaya atau K lalu tekan Terapkan.</p></div>
    <div data-viz="monoqueue"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Tulis DP lambat dulu</b><span>Pastikan state dan transisinya benar, misalnya dengan uji coba kecil.</span></div>
        <div class="step-card"><b>Lihat bentuk transisi</b><span>Jumlah rentang? Min/max jendela? Suku j terpisah dari suku i? Syarat urutan?</span></div>
        <div class="step-card"><b>Pilih struktur</b><span>Prefix sum, deque, variabel terbaik, atau binary search.</span></div>
        <div class="step-card"><b>Bandingkan</b><span>Jalankan versi lambat dan cepat pada input acak kecil; hasilnya harus sama.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Lompat batu dalam O(N) dengan deque monoton.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Lompat Batu</h2>
    @include('lessons.walkthrough', [
        'title' => 'Lompat batu dengan deque monoton',
        'steps' => $opSteps,
        'sample' => ['input' => "8 3\n4 7 2 9 5 1 8 3\n", 'output' => "10\n"],
        'py' => $opPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Biaya 4 7 2 9 5 1 8 3, K = 3. Kolom deque berisi indeks (dp-nya dalam kurung).</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>Buang depan</th><th>dp[i]</th><th>Buang belakang</th><th>Deque setelahnya</th></tr>
            <tr><td>1</td><td>–</td><td>4</td><td>–</td><td class="q">1(4)</td></tr>
            <tr><td>2</td><td>–</td><td>7 + 4 = 11</td><td>–</td><td class="q">1(4) 2(11)</td></tr>
            <tr><td>3</td><td>–</td><td>2 + 4 = 6</td><td>2</td><td class="q">1(4) 3(6)</td></tr>
            <tr><td>4</td><td>–</td><td>9 + 4 = 13</td><td>–</td><td class="q">1(4) 3(6) 4(13)</td></tr>
            <tr class="hl"><td>5</td><td>1</td><td>5 + 6 = 11</td><td>4</td><td class="q">3(6) 5(11)</td></tr>
            <tr><td>6</td><td>–</td><td>1 + 6 = 7</td><td>5</td><td class="q">3(6) 6(7)</td></tr>
            <tr class="hl"><td>7</td><td>3</td><td>8 + 7 = 15</td><td>–</td><td class="q">6(7) 7(15)</td></tr>
            <tr class="ok"><td>8</td><td>–</td><td><b>3 + 7 = 10</b></td><td>7</td><td class="q">6(7) 8(10)</td></tr>
        </table>
    </div>
    <div class="prose"><p>Rutenya 1 → 3 → 6 → 8 dengan biaya 4 + 2 + 1 + 3 = 10.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th></tr>
        <tr><td>Loop seluruh jendela</td><td><code>O(N · K)</code></td></tr>
        <tr><td><code>multiset</code> / priority queue berisi jendela</td><td><code>O(N log N)</code></td></tr>
        <tr><td>Sparse table / segment tree untuk min rentang</td><td><code>O(N log N)</code></td></tr>
        <tr><td>Deque monoton</td><td><code>O(N)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Simpan indeks, bukan nilai.</strong> Untuk tahu kapan sebuah calon keluar dari jendela, kita butuh posisinya.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Batas jendela.</strong> Calon untuk i adalah j ≥ i − K, jadi yang dibuang adalah <code>j &lt; i − K</code>. Salah satu angka saja membuat jendela kelebihan atau kekurangan satu.</p>
    </div>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Jika ragu, <code>multiset</code> (masukkan dp[i], hapus dp[i − K] saat keluar jendela) juga cukup cepat untuk N = 2 · 10<sup>5</sup>. Deque lebih cepat dan lebih ringkas setelah terbiasa.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'memisahkan suku & binary search', 'desc' => 'Running best, penjadwalan berbobot, dan teknik lanjutan.'])

<section class="lesson-section" id="pisah" data-toc="Memisahkan Suku">
    <h2>Pola 3: Pisahkan Suku j dari Suku i</h2>
    <div class="prose">
        <p>Transisi seperti <code>max<sub>j&lt;i</sub>(dp[j] − C·(x[i] − x[j]))</code> tampak bergantung pada i dan j sekaligus. Uraikan: <code>−C·x[i]</code> hanya bergantung pada i dan bisa dikeluarkan dari max. Yang tersisa, <code>dp[j] + C·x[j]</code>, hanya bergantung pada j. Simpan nilai terbesarnya dalam satu variabel yang diperbarui setiap kali dp[j] selesai dihitung.</p>
    </div>
    @include('lessons.code', ['cpp' => $opSeparable, 'js' => null, 'py' => null])
    <div class="prose"><p>Jika j hanya boleh berasal dari jendela, gabungkan dengan deque: simpan <code>dp[j] + C·x[j]</code> di deque maksimum.</p></div>
</section>

<section class="lesson-section" id="binary" data-toc="Binary Search Transisi">
    <h2>Pola 4: Mencari Transisi dengan Binary Search</h2>
    <div class="prose">
        <p>Pada <strong>penjadwalan berbobot</strong>, proyek diurutkan berdasarkan waktu selesai. Jika proyek i diambil, proyek sebelumnya harus selesai sebelum proyek i mulai. Karena waktu selesai sudah terurut, proyek-proyek yang cocok adalah sebuah <em>prefiks</em> 1..j, dan j dicari dengan <code>lower_bound</code>.</p>
    </div>
    @include('lessons.code', ['cpp' => $opBinary, 'js' => null, 'py' => null])
    <div class="prose"><p>Mengapa cukup <code>dp[j]</code>? Karena dp[j] adalah yang terbaik dari <em>semua</em> proyek 1..j, dan semuanya kompatibel dengan proyek i. Totalnya O(N log N).</p></div>
</section>

<section class="lesson-section" id="lanjut" data-toc="Teknik Lanjutan">
    <h2>Teknik Lanjutan untuk Dipelajari Berikutnya</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Convex Hull Trick</h4><p><code>dp[i] = min(dp[j] + a[j] · b[i])</code>: setiap j adalah garis, cari garis terendah di titik b[i]. O(N) atau O(N log N).</p></div>
        <div class="pattern"><h4>Divide & Conquer</h4><p>Jika titik optimal opt[i] tidak pernah mundur, hitung satu lapis DP dalam O(N log N) dengan membagi rentang.</p></div>
        <div class="pattern"><h4>Knuth</h4><p>DP interval dengan opt[l][r−1] ≤ opt[l][r] ≤ opt[l+1][r] turun dari O(N³) ke O(N²).</p></div>
        <div class="pattern"><h4>SOS DP</h4><p>Jumlah atas semua submask dalam O(2<sup>n</sup> · n), bukan O(3<sup>n</sup>).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="j1 lebih tua (keluar jendela lebih dulu) dan tidak lebih murah, jadi selama j2 ada, j1 tidak pernah minimum.">
        <p class="quiz-q">Dalam deque minimum, mengapa j<sub>1</sub> &lt; j<sub>2</sub> dengan dp[j<sub>1</sub>] ≥ dp[j<sub>2</sub>] boleh dibuang?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena deque harus berukuran K</button>
            <button class="quiz-option">Karena j<sub>1</sub> sudah keluar jendela</button>
            <button class="quiz-option">Karena j<sub>2</sub> lebih murah dan bertahan lebih lama di jendela</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="pre[b] dan pre[a−1] sudah dipotong modulo, sehingga pre[b] bisa lebih kecil dari pre[a−1].">
        <p class="quiz-q">Mengapa <code>(pre[b] − pre[a−1]) % MOD</code> bisa salah?</p>
        <div class="quiz-options">
            <button class="quiz-option">Hasilnya bisa negatif karena kedua prefix sudah dimodulo</button>
            <button class="quiz-option">Karena prefix sum tidak boleh dimodulo</button>
            <button class="quiz-option">Karena harus memakai double</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap indeks masuk sekali dan keluar paling banyak sekali, jadi total operasi deque O(N).">
        <p class="quiz-q">Kompleksitas total DP dengan deque monoton untuk N state adalah …</p>
        <div class="quiz-options">
            <button class="quiz-option">O(N · K)</button>
            <button class="quiz-option">O(N)</button>
            <button class="quiz-option">O(N log K)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
