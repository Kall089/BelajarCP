@php
    $ksSteps = [
        ['Header dan membaca barang', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, W;
    cin >> n >> W;
    vector<int> w(n + 1), v(n + 1);
    for (int i = 1; i <= n; i++) cin >> w[i] >> v[i];
CPP, <<<'TXT'
<p>Barang disimpan mulai indeks <strong>1</strong>, bukan 0. Dengan begitu baris 0 tabel bisa dipakai sebagai "belum ada barang sama sekali" tanpa perlu kasus khusus.</p>
<p>Contoh input: 4 barang, kapasitas 7, dengan (berat, nilai) = (1, 1), (3, 4), (4, 5), (5, 7).</p>
TXT],
        ['Tabel dan base case', <<<'CPP'

    // dp[i][c] = nilai terbaik memakai barang 1..i dengan kapasitas c
    vector<vector<long long>> dp(n + 1, vector<long long>(W + 1, 0));
CPP, <<<'TXT'
<p>Tabel berukuran <code>(n + 1) × (W + 1)</code>, semuanya 0.</p>
<ul>
    <li>Baris <code>i = 0</code>: belum ada barang yang boleh dipakai, jadi nilainya 0 untuk kapasitas berapa pun. Itulah base case, dan sudah otomatis benar karena isi awalnya 0.</li>
    <li>Nilai bisa mencapai n × max(v). Untuk n = 100 dan v ≤ 10<sup>9</sup>, itu melewati batas <code>int</code>, jadi pakai <code>long long</code>.</li>
</ul>
TXT],
        ['Transisi: ambil atau tidak', <<<'CPP'

    for (int i = 1; i <= n; i++) {
        for (int c = 0; c <= W; c++) {
            dp[i][c] = dp[i - 1][c];
            if (w[i] <= c) {
                dp[i][c] = max(dp[i][c], dp[i - 1][c - w[i]] + v[i]);
            }
        }
    }
CPP, <<<'TXT'
<p>Untuk setiap barang i dan setiap kapasitas c:</p>
<ol>
    <li><strong>Tidak diambil:</strong> nilainya sama dengan baris di atasnya, <code>dp[i−1][c]</code>.</li>
    <li><strong>Diambil</strong> (hanya jika muat): sisa kapasitas tinggal <code>c − w[i]</code> untuk barang 1..i−1, ditambah nilai barang ini.</li>
</ol>
<pre>dp[3][7] = max(dp[2][7], dp[2][7 − 4] + 5)
         = max(5,        4 + 5)          = 9</pre>
<div class="wt-tip">Perhatikan bahwa yang dibaca selalu baris <code>i − 1</code>. Itulah yang menjamin barang i paling banyak dipakai sekali.</div>
TXT],
        ['Jawaban', <<<'CPP'

    cout << dp[n][W] << '\n';
CPP, <<<'TXT'
<p>Semua barang sudah dipertimbangkan dan kapasitas penuh tersedia, jadi jawabannya ada di pojok kanan bawah tabel: <code>dp[n][W]</code>.</p>
TXT],
        ['Telusur balik barang yang dipilih', <<<'CPP'

    vector<int> pilih;
    for (int i = n, c = W; i >= 1; i--) {
        if (dp[i][c] != dp[i - 1][c]) {
            pilih.push_back(i);
            c -= w[i];
        }
    }
    reverse(pilih.begin(), pilih.end());
CPP, <<<'TXT'
<p>Mulai dari sel <code>(n, W)</code> lalu naik satu baris setiap langkah:</p>
<ul>
    <li>Jika <code>dp[i][c]</code> <strong>sama</strong> dengan sel di atasnya, barang i tidak diperlukan. Naik saja.</li>
    <li>Jika <strong>berbeda</strong>, nilai itu hanya bisa didapat dengan mengambil barang i. Catat i, lalu kurangi kapasitas sebesar <code>w[i]</code>.</li>
</ul>
<pre>(4, 7): 9 = dp[3][7] → barang 4 tidak dipakai
(3, 7): 9 ≠ dp[2][7] = 5 → ambil barang 3, c = 3
(2, 3): 4 ≠ dp[1][3] = 1 → ambil barang 2, c = 0
(1, 0): 0 = dp[0][0]    → selesai</pre>
TXT],
        ['Mencetak hasil', <<<'CPP'

    for (int i = 0; i < (int)pilih.size(); i++) {
        cout << pilih[i] << (i + 1 < (int)pilih.size() ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Telusur balik menemukan barang dari belakang, jadi daftarnya dibalik dulu agar tercetak urut naik.</p>
<div class="wt-warn">Telusur balik butuh tabel 2D yang utuh. Versi hemat memori 1D (di bawah) hanya bisa memberi <em>nilai</em> maksimum, bukan daftar barangnya.</div>
TXT],
    ];

    $ksJs = <<<'JS'
const [n, W] = readInts();
const w = [0], v = [0];
for (let i = 0; i < n; i++) {
  const [a, b] = readInts();
  w.push(a);
  v.push(b);
}

const dp = Array.from({ length: n + 1 }, () => new Array(W + 1).fill(0));
for (let i = 1; i <= n; i++) {
  for (let c = 0; c <= W; c++) {
    dp[i][c] = dp[i - 1][c];
    if (w[i] <= c) dp[i][c] = Math.max(dp[i][c], dp[i - 1][c - w[i]] + v[i]);
  }
}

const pilih = [];
for (let i = n, c = W; i >= 1; i--) {
  if (dp[i][c] !== dp[i - 1][c]) {
    pilih.push(i);
    c -= w[i];
  }
}
console.log(dp[n][W] + "\n" + pilih.reverse().join(" "));
JS;

    $ksPy = <<<'PY'
n, W = map(int, input().split())
w, v = [0], [0]
for _ in range(n):
    a, b = map(int, input().split())
    w.append(a)
    v.append(b)

dp = [[0] * (W + 1) for _ in range(n + 1)]
for i in range(1, n + 1):
    for c in range(W + 1):
        dp[i][c] = dp[i - 1][c]
        if w[i] <= c:
            dp[i][c] = max(dp[i][c], dp[i - 1][c - w[i]] + v[i])

pilih = []
c = W
for i in range(n, 0, -1):
    if dp[i][c] != dp[i - 1][c]:
        pilih.append(i)
        c -= w[i]
print(dp[n][W])
print(*reversed(pilih))
PY;

    $ks1D = <<<'CPP'
// Versi hemat memori: satu baris saja, O(W)
vector<long long> dp(W + 1, 0);
for (int i = 1; i <= n; i++) {
    // MUNDUR: dp[c - w[i]] yang dibaca masih nilai "baris i-1"
    for (int c = W; c >= w[i]; c--) {
        dp[c] = max(dp[c], dp[c - w[i]] + v[i]);
    }
}
cout << dp[W] << '\n';
CPP;

    $ksUnbounded = <<<'CPP'
// Unbounded knapsack: setiap barang boleh diambil berkali-kali
// Cukup balik arah loop menjadi MAJU
for (int i = 1; i <= n; i++)
    for (int c = w[i]; c <= W; c++)
        dp[c] = max(dp[c], dp[c - w[i]] + v[i]);
CPP;

    $ksSubset = <<<'CPP'
// Subset sum: bisakah memilih sebagian angka yang jumlahnya tepat S?
vector<char> bisa(S + 1, false);
bisa[0] = true;                        // jumlah 0: pilih tidak ada
for (int x : a)
    for (int s = S; s >= x; s--)       // mundur, seperti knapsack 0/1
        if (bisa[s - x]) bisa[s] = true;

// Versi bitset: 64 kali lebih cepat
bitset<100001> b;
b[0] = 1;
for (int x : a) b |= (b << x);         // geser = "tambahkan x ke semua jumlah"
CPP;

    $ksValue = <<<'CPP'
// W sangat besar (≤ 1e9) tetapi total nilai kecil (≤ 1e5):
// balik peran! minBerat[t] = berat terkecil untuk mencapai nilai tepat t
const long long INF = 4e18;
int V = accumulate(v.begin(), v.end(), 0);
vector<long long> minBerat(V + 1, INF);
minBerat[0] = 0;
for (int i = 1; i <= n; i++)
    for (int t = V; t >= v[i]; t--)
        if (minBerat[t - v[i]] != INF)
            minBerat[t] = min(minBerat[t], minBerat[t - v[i]] + w[i]);

int jawaban = 0;
for (int t = 0; t <= V; t++)
    if (minBerat[t] <= W) jawaban = t;
CPP;

    $ksBounded = <<<'CPP'
// Bounded knapsack: barang i ada sebanyak k[i] buah.
// Pecah menjadi paket 1, 2, 4, ..., sisa  →  O(log k) barang 0/1 biasa
vector<pair<int,long long>> paket;     // (berat, nilai)
for (int i = 1; i <= n; i++) {
    int sisa = k[i];
    for (int p = 1; sisa > 0; p *= 2) {
        int ambil = min(p, sisa);
        paket.push_back({w[i] * ambil, v[i] * ambil});
        sisa -= ambil;
    }
}
// lalu jalankan knapsack 0/1 biasa pada "paket"
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali soal bertipe <strong>knapsack</strong>: ada kapasitas terbatas dan setiap barang dipilih atau tidak.</li>
        <li>Mengisi tabel <code>dp[i][c]</code> dengan tangan dan menelusuri balik barang yang dipilih.</li>
        <li>Menulis knapsack 0/1 di C++, lalu menghemat memorinya menjadi satu baris.</li>
        <li>Memakai variasinya: subset sum, unbounded, bounded, dan knapsack dengan kapasitas raksasa.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'ambil atau tinggalkan', 'desc' => 'Ide dasar knapsack 0/1 dan cara mengisi tabelnya dengan tangan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Ransel yang Terbatas</h2>
    <div class="prose">
        <p>Kamu akan berkemah dan ransel hanya kuat menampung <strong>W kg</strong>. Ada beberapa barang, masing-masing punya berat dan nilai kegunaan. Barang mana yang dibawa agar total nilainya paling besar, tanpa melebihi W?</p>
        <p>Mencoba semua kombinasi berarti 2<sup>n</sup> kemungkinan. Untuk 100 barang itu sekitar 10<sup>30</sup>, jauh terlalu banyak. DP memutuskan barang <strong>satu per satu</strong>, dan untuk setiap barang hanya ada dua pilihan:</p>
        <div class="term-grid">
            <div class="term"><b>Tidak diambil</b><span>Hasilnya sama dengan solusi terbaik tanpa barang ini, dengan kapasitas yang sama: <code>dp[i−1][c]</code></span></div>
            <div class="term"><b>Diambil</b><span>Kapasitas berkurang w, nilai bertambah v: <code>dp[i−1][c−w] + v</code></span></div>
        </div>
    </div>
    <div class="recurrence"><small>Rumus transisi</small>dp[i][c] = max( dp[i−1][c],  dp[i−1][c − w<sub>i</sub>] + v<sub>i</sub> )     jika w<sub>i</sub> ≤ c
dp[i][c] = dp[i−1][c]                                  jika w<sub>i</sub> &gt; c
dp[0][c] = 0</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Satu angka saja tidak cukup untuk menggambarkan keadaan. Kita perlu tahu <strong>sampai barang ke berapa</strong> (i) dan <strong>sisa kapasitas berapa</strong> (c). Ketika satu dimensi tidak cukup, tambahkan dimensi.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Serakah tidak berhasil.</strong> Mengambil barang dengan rasio nilai/berat tertinggi lebih dulu terlihat masuk akal, tetapi gagal. Contoh W = 7 di bawah: rasio terbaik adalah barang 4 (7/5 = 1,4), dan setelah itu hanya barang 1 yang muat. Total 8, padahal jawaban optimalnya 9.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose">
        <p>Kapasitas <strong>W = 7</strong>. Barang (berat, nilai): <strong>1</strong> = (1, 1), <strong>2</strong> = (3, 4), <strong>3</strong> = (4, 5), <strong>4</strong> = (5, 7). Setiap baris adalah satu barang baru yang boleh dipakai; setiap kolom adalah kapasitas.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i (w, v)</th><th>c=0</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th></tr>
            <tr><td>0 (–)</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
            <tr><td>1 (1, 1)</td><td>0</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
            <tr class="hl"><td>2 (3, 4)</td><td>0</td><td>1</td><td>1</td><td><b>4</b></td><td>5</td><td>5</td><td>5</td><td>5</td></tr>
            <tr class="hl"><td>3 (4, 5)</td><td>0</td><td>1</td><td>1</td><td>4</td><td>5</td><td>6</td><td>6</td><td><b>9</b></td></tr>
            <tr class="ok"><td>4 (5, 7)</td><td>0</td><td>1</td><td>1</td><td>4</td><td>5</td><td>7</td><td>8</td><td><span class="new">9</span></td></tr>
        </table>
    </div>
    <div class="prose">
        <p>Beberapa sel yang menarik:</p>
        <ul>
            <li><code>dp[2][4] = max(dp[1][4], dp[1][1] + 4) = max(1, 1 + 4) = 5</code>. Barang 1 dan 2 sama-sama masuk.</li>
            <li><code>dp[3][7] = max(dp[2][7], dp[2][3] + 5) = max(5, 4 + 5) = 9</code>. Barang 2 dan 3.</li>
            <li><code>dp[4][7] = max(dp[3][7], dp[3][2] + 7) = max(9, 1 + 7) = 9</code>. Barang 4 tidak membantu.</li>
        </ul>
        <p><strong>Telusur balik</strong> dari sel kanan bawah: setiap kali nilai sel berbeda dengan sel tepat di atasnya, barang di baris itu diambil. Hasilnya barang <strong>2 dan 3</strong>.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi Knapsack</h2>
    <div class="prose">
        <p>Setiap sel melihat ke <span style="color:var(--cyan)">atas</span> (tidak ambil) dan ke <span style="color:var(--pink)">atas, mundur sejauh w</span> (ambil). Di akhir, perhatikan telusur baliknya.</p>
    </div>
    <div data-viz="knapsack"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri knapsack</b><span>Ada "kapasitas" atau "anggaran" terbatas, setiap item dipilih atau tidak, dan kita memaksimalkan atau meminimalkan sesuatu.</span></div>
        <div class="step-card"><b>Tabel (n+1) × (W+1)</b><span>Baris 0 (tanpa barang) bernilai 0 semua.</span></div>
        <div class="step-card"><b>Isi baris demi baris</b><span>Untuk setiap barang i dan kapasitas c, ambil max antara tidak ambil dan ambil (jika muat).</span></div>
        <div class="step-card"><b>Jawaban</b><span><code>dp[n][W]</code>. Butuh daftar barangnya? Telusur balik dari <code>(n, W)</code>.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis knapsack di C++', 'desc' => 'Program lengkap dengan rekonstruksi, lalu versi hemat memori.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Knapsack 0/1</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> n barang dan kapasitas W. Cetak nilai total maksimum, lalu nomor barang-barang yang dipilih (urut naik).</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Knapsack 0/1 + telusur balik',
        'steps' => $ksSteps,
        'sample' => ['input' => "4 7\n1 1\n3 4\n4 5\n5 7\n", 'output' => "9\n2 3\n"],
        'js' => $ksJs,
        'py' => $ksPy,
    ])
</section>

<section class="lesson-section" id="hemat" data-toc="Hemat Memori 1D">
    <h2>Hemat Memori: Satu Baris Saja</h2>
    <div class="prose">
        <p>Baris i hanya membaca baris i−1. Jadi kita bisa memakai <strong>satu array</strong> dan menimpanya. Kuncinya ada di <strong>arah loop</strong>:</p>
    </div>
    @include('lessons.code', ['cpp' => $ks1D, 'js' => null, 'py' => null])
    <div class="prose">
        <p>Mengapa harus mundur? Misalkan barang (w = 2, v = 3) dan array awalnya nol semua.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Arah loop</th><th>Urutan pengisian</th><th>dp[4]</th><th>Arti</th></tr>
            <tr class="ok"><td>Mundur (c = 4, 3, 2)</td><td>dp[4] membaca dp[2] yang masih <b>0</b> (nilai lama)</td><td>3</td><td>Barang dipakai sekali. Benar.</td></tr>
            <tr class="hl"><td>Maju (c = 2, 3, 4)</td><td>dp[2] = 3 lebih dulu, lalu dp[4] membaca dp[2] = <b>3</b> (nilai baru)</td><td>6</td><td>Barang dipakai dua kali. Salah untuk 0/1.</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Kesalahan ini justru berguna: loop <strong>maju</strong> adalah solusi tepat untuk <em>unbounded knapsack</em>, ketika setiap barang boleh diambil berkali-kali.</p>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Versi</th><th>Waktu</th><th>Memori</th><th>Bisa rekonstruksi?</th></tr>
        <tr><td>Tabel 2D</td><td><code>O(n · W)</code></td><td><code>O(n · W)</code></td><td>Ya</td></tr>
        <tr><td>Array 1D (loop mundur)</td><td><code>O(n · W)</code></td><td><code>O(W)</code></td><td>Tidak langsung</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pseudo-polinomial.</strong> Waktunya bergantung pada <em>nilai</em> W. Untuk n = 100 dan W = 10<sup>5</sup> itu 10<sup>7</sup> operasi, ringan. Untuk W = 10<sup>9</sup>, tabelnya mustahil dibuat. Lihat trik "balik peran" di level Lanjut.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Memori 2D.</strong> Tabel <code>long long</code> berukuran 1000 × 10<sup>5</sup> butuh 800 MB. Jika tidak perlu rekonstruksi, pakai versi 1D.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'keluarga knapsack', 'desc' => 'Empat variasi yang sering muncul di kompetisi.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Benar?">
    <h2>Mengapa Transisinya Benar?</h2>
    <div class="proof">
        <p>Ambil solusi optimal untuk (i, c). Barang i entah ada di dalamnya, entah tidak.</p>
        <p><strong>Jika tidak ada</strong>, solusi itu hanya memakai barang 1..i−1 dengan kapasitas c, jadi nilainya paling banyak <code>dp[i−1][c]</code>.</p>
        <p><strong>Jika ada</strong>, buang barang i. Sisanya adalah pilihan dari barang 1..i−1 dengan berat paling banyak c − w<sub>i</sub>. Sisa itu harus optimal; kalau tidak, kita bisa menggantinya dengan yang lebih baik dan mendapat solusi (i, c) yang lebih baik lagi. Kontradiksi. Jadi nilainya <code>dp[i−1][c − w<sub>i</sub>] + v<sub>i</sub></code>.</p>
        <p>Kedua kasus sudah dicoba, maka max keduanya adalah jawabannya.</p>
    </div>
</section>

<section class="lesson-section" id="variasi" data-toc="Variasi Knapsack">
    <h2>Variasi Knapsack</h2>
    <div class="prose">
        <h3>1. Subset sum dan partisi</h3>
        <p>"Bisakah memilih sebagian angka yang jumlahnya tepat S?" adalah knapsack yang nilainya hanya benar/salah. Soal "bagi dua kelompok dengan selisih sekecil mungkin" adalah subset sum dengan S = total/2.</p>
    </div>
    @include('lessons.code', ['cpp' => $ksSubset, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. Unbounded knapsack</h3>
        <p>Setiap barang tersedia tak terbatas. Bedanya hanya arah loop.</p>
    </div>
    @include('lessons.code', ['cpp' => $ksUnbounded, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>3. Kapasitas raksasa, nilai kecil</h3>
        <p>Jika W sampai 10<sup>9</sup> tetapi jumlah semua nilai kecil, tukar perannya: state-nya menjadi <em>nilai</em>, dan isinya <em>berat minimum</em>. Jawabannya adalah nilai terbesar yang berat minimumnya masih ≤ W.</p>
    </div>
    @include('lessons.code', ['cpp' => $ksValue, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>4. Bounded knapsack (stok terbatas)</h3>
        <p>Barang i ada k<sub>i</sub> buah. Menyalin barang sebanyak k<sub>i</sub> kali terlalu lambat. Triknya: pecah menjadi paket berukuran 1, 2, 4, …, sisa. Gabungan paket-paket itu bisa membentuk jumlah berapa pun dari 0 sampai k<sub>i</sub>.</p>
    </div>
    @include('lessons.code', ['cpp' => $ksBounded, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>0/1</h4><p>Setiap barang sekali. Loop kapasitas <strong>mundur</strong>.</p></div>
        <div class="pattern"><h4>Unbounded</h4><p>Barang tak terbatas. Loop kapasitas <strong>maju</strong>.</p></div>
        <div class="pattern"><h4>Bounded</h4><p>Stok k buah. Pecah biner lalu 0/1.</p></div>
        <div class="pattern"><h4>Subset sum</h4><p>Nilai benar/salah. Bisa dipercepat dengan <code>bitset</code>.</p></div>
        <div class="pattern"><h4>Balik peran</h4><p>W besar, nilai kecil: <code>minBerat[nilai]</code>.</p></div>
        <div class="pattern"><h4>Hitung cara</h4><p>Ganti max dengan penjumlahan: banyak cara memilih subset berjumlah S.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Dengan loop naik, dp[c−w] yang dibaca mungkin sudah memakai barang yang sama di iterasi ini, sehingga barang bisa terambil berkali-kali (unbounded).">
        <p class="quiz-q">Pada knapsack 1D, apa akibatnya jika loop kapasitas dibuat NAIK (0 → W)?</p>
        <div class="quiz-options">
            <button class="quiz-option">Hasilnya tetap sama</button>
            <button class="quiz-option">Barang yang sama bisa diambil berkali-kali</button>
            <button class="quiz-option">Program error</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Barang 3 kg tidak muat di kapasitas 2, jadi pilihan 'diambil' tidak tersedia. Nilai disalin dari atas: dp[i][2] = dp[i−1][2].">
        <p class="quiz-q">Barang ke-i berat 3. Berapa dp[i][2]?</p>
        <div class="quiz-options">
            <button class="quiz-option">dp[i−1][2] + v</button>
            <button class="quiz-option">0</button>
            <button class="quiz-option">dp[i−1][2]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="n · W = 100 · 10⁹ = 10¹¹ terlalu besar. Total nilai paling banyak 100 · 100 = 10⁴, jadi DP berdasarkan nilai (minBerat) hanya butuh 100 · 10⁴ = 10⁶ operasi.">
        <p class="quiz-q">n = 100, W = 10<sup>9</sup>, setiap nilai ≤ 100. Pendekatan apa yang tepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">DP dengan state nilai, isinya berat minimum</button>
            <button class="quiz-option">Tabel dp[n][W] biasa</button>
            <button class="quiz-option">Serakah berdasarkan rasio nilai/berat</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
