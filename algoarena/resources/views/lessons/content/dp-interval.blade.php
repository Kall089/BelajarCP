@php
    $ivSteps = [
        ['Header dan membaca tumpukan', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int n;
vector<vector<long long>> dp;
vector<vector<int>> belah;
vector<long long> a;
CPP, <<<'TXT'
<p>Variabel dibuat global agar mudah dipakai oleh fungsi rekonstruksi di bawah.</p>
<ul>
    <li><code>dp[l][r]</code> = biaya termurah menyatukan tumpukan l sampai r menjadi satu.</li>
    <li><code>belah[l][r]</code> = posisi k terbaik, yaitu gabungan terakhir adalah [l..k] dengan [k+1..r].</li>
</ul>
<p><code>INF</code> diambil seperempat batas <code>long long</code> agar penjumlahan dua INF tidak meluap.</p>
TXT],
        ['Rekonstruksi bentuk penggabungan', <<<'CPP'

string bentuk(int l, int r) {
    if (l == r) return to_string(a[l]);
    int k = belah[l][r];
    return "(" + bentuk(l, k) + " " + bentuk(k + 1, r) + ")";
}
CPP, <<<'TXT'
<p>Fungsi rekursif ini menulis urutan penggabungan dalam bentuk kurung. Interval satu tumpukan ditulis angkanya saja. Interval lebih panjang dipecah di <code>belah[l][r]</code>, lalu kedua bagiannya ditulis secara rekursif.</p>
<pre>bentuk(0, 3) dengan belah = 1
  = "(" + bentuk(0, 1) + " " + bentuk(2, 3) + ")"
  = "((4 1) (1 4))"</pre>
TXT],
        ['Membaca input dan prefix sum', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n;
    a.resize(n);
    for (auto& x : a) cin >> x;

    vector<long long> pre(n + 1, 0);
    for (int i = 0; i < n; i++) pre[i + 1] = pre[i] + a[i];
    auto sum = [&](int l, int r) { return pre[r + 1] - pre[l]; };
CPP, <<<'TXT'
<p>Biaya gabungan terakhir untuk interval [l, r] selalu sama dengan <strong>jumlah semua batu</strong> di dalamnya. Agar jumlah itu didapat dalam O(1), kita siapkan prefix sum.</p>
<pre>a   :    4  1  1  4
pre : 0  4  5  6  10
sum(1, 3) = pre[4] − pre[1] = 10 − 4 = 6</pre>
TXT],
        ['Base case: interval satu tumpukan', <<<'CPP'

    dp.assign(n, vector<long long>(n, 0));
    belah.assign(n, vector<int>(n, -1));
CPP, <<<'TXT'
<p>Semua sel dimulai dari 0. Untuk interval panjang 1 (<code>dp[i][i]</code>), nilai 0 itu memang jawabannya: satu tumpukan tidak perlu digabung. Sel lainnya akan ditimpa nanti.</p>
TXT],
        ['Isi berdasarkan panjang interval', <<<'CPP'

    for (int len = 2; len <= n; len++) {
        for (int l = 0; l + len - 1 < n; l++) {
            int r = l + len - 1;
            dp[l][r] = INF;
            for (int k = l; k < r; k++) {
                long long biaya = dp[l][k] + dp[k + 1][r] + sum(l, r);
                if (biaya < dp[l][r]) {
                    dp[l][r] = biaya;
                    belah[l][r] = k;
                }
            }
        }
    }
CPP, <<<'TXT'
<p>Ini inti DP interval. Loop paling luar adalah <strong>panjang interval</strong>, bukan l. Dengan begitu, ketika kita menghitung [l, r], semua interval yang lebih pendek (termasuk [l, k] dan [k+1, r]) sudah selesai.</p>
<p>Untuk setiap titik belah k, biayanya = satukan bagian kiri + satukan bagian kanan + gabungan terakhir keduanya.</p>
<pre>dp[0][3]: k=0 → dp[0][0] + dp[1][3] + 10 = 0 + 8 + 10 = 18
          k=1 → dp[0][1] + dp[2][3] + 10 = 5 + 5 + 10 = 20
          k=2 → dp[0][2] + dp[3][3] + 10 = 8 + 0 + 10 = 18</pre>
<div class="wt-warn">Jika loop luarnya <code>l</code> dari 0 naik, <code>dp[k+1][r]</code> belum dihitung saat dibutuhkan. Urutan isi adalah sumber bug nomor satu di DP interval.</div>
TXT],
        ['Mencetak hasil', <<<'CPP'

    cout << dp[0][n - 1] << '\n' << bentuk(0, n - 1) << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Jawabannya interval penuh, <code>dp[0][n − 1]</code>. Pada contoh <code>4 1 1 4</code>, biaya minimumnya 18. Ada dua belahan terbaik (k = 0 dan k = 2); karena memakai <code>&lt;</code>, kode ini menyimpan yang ditemukan pertama, sehingga bentuknya <code>(4 ((1 1) 4))</code>: gabung 1+1, lalu dengan 4 di kanan, terakhir dengan 4 di kiri.</p>
TXT],
    ];

    $ivJs = <<<'JS'
const [n] = readInts();
const a = readInts();
const pre = [0];
for (const x of a) pre.push(pre[pre.length - 1] + x);
const sum = (l, r) => pre[r + 1] - pre[l];

const dp = Array.from({ length: n }, () => new Array(n).fill(0));
const belah = Array.from({ length: n }, () => new Array(n).fill(-1));
for (let len = 2; len <= n; len++) {
  for (let l = 0; l + len - 1 < n; l++) {
    const r = l + len - 1;
    dp[l][r] = Infinity;
    for (let k = l; k < r; k++) {
      const biaya = dp[l][k] + dp[k + 1][r] + sum(l, r);
      if (biaya < dp[l][r]) { dp[l][r] = biaya; belah[l][r] = k; }
    }
  }
}
const bentuk = (l, r) => (l === r ? String(a[l]) : `(${bentuk(l, belah[l][r])} ${bentuk(belah[l][r] + 1, r)})`);
console.log(dp[0][n - 1] + "\n" + bentuk(0, n - 1));
JS;

    $ivPy = <<<'PY'
import sys
sys.setrecursionlimit(10000)

n = int(input())
a = list(map(int, input().split()))
pre = [0]
for x in a:
    pre.append(pre[-1] + x)

dp = [[0] * n for _ in range(n)]
belah = [[-1] * n for _ in range(n)]
for length in range(2, n + 1):
    for l in range(n - length + 1):
        r = l + length - 1
        dp[l][r] = float("inf")
        for k in range(l, r):
            biaya = dp[l][k] + dp[k + 1][r] + pre[r + 1] - pre[l]
            if biaya < dp[l][r]:
                dp[l][r] = biaya
                belah[l][r] = k

def bentuk(l, r):
    if l == r:
        return str(a[l])
    k = belah[l][r]
    return "(" + bentuk(l, k) + " " + bentuk(k + 1, r) + ")"

print(dp[0][n - 1])
print(bentuk(0, n - 1))
PY;

    $ivGame = <<<'CPP'
// Permainan mengambil dari ujung: dua pemain bergantian mengambil
// angka dari ujung KIRI atau KANAN barisan. Keduanya bermain optimal.
// dp[l][r] = (skor pemain yang sedang jalan) − (skor lawan) pada sisa a[l..r]
for (int i = 0; i < n; i++) dp[i][i] = a[i];
for (int len = 2; len <= n; len++)
    for (int l = 0; l + len - 1 < n; l++) {
        int r = l + len - 1;
        dp[l][r] = max(a[l] - dp[l + 1][r],    // ambil kiri, lalu lawan yang jalan
                       a[r] - dp[l][r - 1]);   // ambil kanan
    }
// dp[0][n-1] > 0 berarti pemain pertama menang
CPP;

    $ivPal = <<<'CPP'
// Subsequence palindrom terpanjang sebagai DP interval
// dp[l][r] = panjang palindrom terpanjang di dalam s[l..r]
for (int i = 0; i < n; i++) dp[i][i] = 1;
for (int len = 2; len <= n; len++)
    for (int l = 0; l + len - 1 < n; l++) {
        int r = l + len - 1;
        if (s[l] == s[r]) dp[l][r] = dp[l + 1][r - 1] + 2;   // kedua ujung dipakai
        else dp[l][r] = max(dp[l + 1][r], dp[l][r - 1]);     // buang salah satu ujung
    }
// catatan: untuk len = 2, dp[l+1][r-1] adalah interval kosong (bernilai 0)
CPP;

    $ivMemo = <<<'CPP'
// Gaya rekursi + memo: urutan isi tidak perlu dipikirkan
long long memo[505][505];
bool sudah[505][505];

long long solve(int l, int r) {
    if (l == r) return 0;
    if (sudah[l][r]) return memo[l][r];
    long long best = LLONG_MAX;
    for (int k = l; k < r; k++)
        best = min(best, solve(l, k) + solve(k + 1, r) + sum(l, r));
    sudah[l][r] = true;
    return memo[l][r] = best;
}
CPP;

    $ivKnuth = <<<'CPP'
// Optimasi Knuth: berlaku jika belah[l][r-1] <= belah[l][r] <= belah[l+1][r]
// (benar untuk soal menggabungkan tumpukan). Total kerja turun menjadi O(n²).
for (int len = 2; len <= n; len++)
    for (int l = 0; l + len - 1 < n; l++) {
        int r = l + len - 1;
        dp[l][r] = INF;
        int dari = belah[l][r - 1], sampai = belah[l + 1][r];
        for (int k = dari; k <= min(sampai, r - 1); k++) {
            long long biaya = dp[l][k] + dp[k + 1][r] + sum(l, r);
            if (biaya < dp[l][r]) { dp[l][r] = biaya; belah[l][r] = k; }
        }
    }
// base case: belah[i][i] = i
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali soal yang jawabannya bergantung pada <strong>potongan berurutan</strong> [l, r] dari sebuah barisan.</li>
        <li>Memilih titik belah k dan menyusun transisi <code>dp[l][r]</code> dari dua interval yang lebih pendek.</li>
        <li>Mengisi tabel dengan urutan yang benar: <strong>berdasarkan panjang interval</strong>.</li>
        <li>Memakai pola yang sama untuk permainan dua pemain, palindrom, dan memperkenalkan optimasi Knuth.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'memecah rentang', 'desc' => 'Soal menggabungkan tumpukan dan cara berpikir "gabungan terakhir".'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Menggabungkan Tumpukan Batu</h2>
    <div class="prose">
        <p>Ada beberapa tumpukan batu berjajar, misalnya <code>4, 1, 1, 4</code>. Dalam satu langkah, kamu boleh menggabungkan <strong>dua tumpukan yang bersebelahan</strong>, dan biayanya sama dengan jumlah batu di kedua tumpukan itu. Ulangi sampai tinggal satu tumpukan. Berapa total biaya minimum?</p>
        <p>Mencoba semua urutan penggabungan terlalu banyak. Kita ubah sudut pandangnya: <strong>lihat gabungan yang paling akhir</strong>. Sebelum langkah terakhir pasti ada dua tumpukan, yang kiri berasal dari tumpukan l..k dan yang kanan dari k+1..r. Kedua bagian itu adalah subsoal yang sama, hanya lebih pendek.</p>
    </div>
    <div class="recurrence"><small>State dan transisi</small>dp[l][r] = biaya minimum menyatukan tumpukan l, l+1, …, r
dp[i][i] = 0
dp[l][r] = min over k di [l, r−1] of ( dp[l][k] + dp[k+1][r] + jumlah(l..r) )</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Ciri soal DP interval: objeknya <strong>barisan</strong>, operasinya hanya bisa terjadi pada bagian yang <strong>bersebelahan</strong>, dan setiap langkah membagi atau menyatukan sebuah rentang. State-nya hampir selalu <code>dp[l][r]</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Serakah gagal lagi.</strong> Menggabungkan pasangan termurah lebih dulu terlihat masuk akal, tetapi tidak selalu optimal karena setiap tumpukan besar akan "ditanggung" ulang di setiap gabungan berikutnya.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose">
        <p>Tumpukan <code>4, 1, 1, 4</code> (indeks 0..3). Isi per <strong>panjang</strong> interval.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Interval</th><th>Jumlah</th><th>Pilihan belahan k</th><th>dp</th></tr>
            <tr><td>[0,1] = 4 1</td><td>5</td><td>k=0: 0 + 0 + 5</td><td><span class="new">5</span></td></tr>
            <tr><td>[1,2] = 1 1</td><td>2</td><td>k=1: 0 + 0 + 2</td><td><span class="new">2</span></td></tr>
            <tr><td>[2,3] = 1 4</td><td>5</td><td>k=2: 0 + 0 + 5</td><td><span class="new">5</span></td></tr>
            <tr class="hl"><td>[0,2] = 4 1 1</td><td>6</td><td>k=0: 0 + 2 + 6 = 8 &nbsp;|&nbsp; k=1: 5 + 0 + 6 = 11</td><td><span class="new">8</span></td></tr>
            <tr class="hl"><td>[1,3] = 1 1 4</td><td>6</td><td>k=1: 0 + 5 + 6 = 11 &nbsp;|&nbsp; k=2: 2 + 0 + 6 = 8</td><td><span class="new">8</span></td></tr>
            <tr class="ok"><td>[0,3] = 4 1 1 4</td><td>10</td><td>k=0: 0+8+10 = 18 &nbsp;|&nbsp; k=1: 5+5+10 = 20 &nbsp;|&nbsp; k=2: 8+0+10 = 18</td><td><span class="new">18</span></td></tr>
        </table>
    </div>
    <div class="prose">
        <p>Jawabannya 18, misalnya dengan urutan: gabung 1+1 (biaya 2), lalu 4+2 (biaya 6), lalu 6+4 (biaya 10). Total 2 + 6 + 10 = 18.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi DP Interval</h2>
    <div class="prose">
        <p>Tabel berbentuk segitiga: hanya sel dengan l ≤ r yang berarti. Perhatikan bahwa pengisian berjalan <strong>diagonal demi diagonal</strong>, dari interval pendek ke interval panjang. Untuk setiap sel, semua titik belah k dicoba satu per satu.</p>
    </div>
    <div data-viz="interval"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>State</b><span><code>dp[l][r]</code> = jawaban untuk potongan barisan dari l sampai r.</span></div>
        <div class="step-card"><b>Base case</b><span>Interval terpendek: panjang 1 (kadang juga panjang 0 atau 2).</span></div>
        <div class="step-card"><b>Transisi</b><span>Pilih titik belah k, atau lihat apa yang terjadi pada <strong>ujung</strong> l dan r.</span></div>
        <div class="step-card"><b>Urutan isi</b><span>Loop luar = panjang interval, dari pendek ke panjang.</span></div>
        <div class="step-card"><b>Jawaban</b><span>Biasanya <code>dp[0][n − 1]</code>.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'DP interval di C++', 'desc' => 'Program lengkap dengan prefix sum dan rekonstruksi urutan penggabungan.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Menggabungkan Tumpukan</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> n tumpukan berjajar. Cetak biaya minimum, lalu urutan penggabungannya dalam bentuk kurung.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'DP interval + rekonstruksi',
        'steps' => $ivSteps,
        'sample' => ['input' => "4\n4 1 1 4\n", 'output' => "18\n(4 ((1 1) 4))\n"],
        'js' => $ivJs,
        'py' => $ivPy,
    ])
</section>

<section class="lesson-section" id="memo" data-toc="Alternatif: Memoization">
    <h2>Alternatif: Rekursi dengan Memo</h2>
    <div class="prose">
        <p>Jika urutan pengisian terasa membingungkan, tulis versi rekursif. Rekursi otomatis menghitung interval pendek lebih dulu. Hasilnya sama, kompleksitasnya juga sama.</p>
    </div>
    @include('lessons.code', ['cpp' => $ivMemo, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Bagian</th><th>Banyaknya</th></tr>
        <tr><td>State (l, r)</td><td><code>O(n²)</code></td></tr>
        <tr><td>Pilihan k per state</td><td><code>O(n)</code></td></tr>
        <tr><td>Total waktu</td><td><code>O(n³)</code>, aman sampai n ≈ 500</td></tr>
        <tr><td>Memori</td><td><code>O(n²)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Urutan loop.</strong> <code>for l = 0..n−1, for r = l..n−1</code> salah, karena <code>dp[k+1][r]</code> belum terisi. Gunakan loop panjang, atau loop <code>l</code> <em>turun</em> dari n−1 ke 0 dengan <code>r</code> naik dari l.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Barisan melingkar.</strong> Jika tumpukan tersusun melingkar, gandakan array menjadi panjang 2n, jalankan DP, lalu ambil minimum <code>dp[i][i + n − 1]</code> untuk setiap i.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'variasi DP interval', 'desc' => 'Permainan dua pemain, palindrom, dan optimasi Knuth.'])

<section class="lesson-section" id="ujung" data-toc="Transisi dari Ujung">
    <h2>Transisi dari Ujung, Bukan dari Belahan</h2>
    <div class="prose">
        <p>Tidak semua DP interval memakai titik belah k. Banyak soal cukup melihat apa yang terjadi pada <strong>ujung kiri atau ujung kanan</strong>, sehingga transisinya O(1) dan totalnya O(n²).</p>
        <h3>Permainan mengambil dari ujung</h3>
        <p>Dua pemain bergantian mengambil angka dari ujung kiri atau kanan. Masing-masing ingin skornya sebesar mungkin. Triknya: simpan <strong>selisih</strong> skor dari sudut pandang pemain yang sedang jalan. Setelah kita mengambil, lawan menjadi "pemain yang sedang jalan" pada sisa interval, jadi selisihnya dikurangkan.</p>
    </div>
    @include('lessons.code', ['cpp' => $ivGame, 'js' => null, 'py' => null])
    <div class="prose">
        <p>Untuk barisan <code>4 7 2 9 5 2</code>, <code>dp[0][5] = 7</code>: pemain pertama menang dengan selisih 7 poin.</p>
        <h3>Palindrom terpanjang</h3>
    </div>
    @include('lessons.code', ['cpp' => $ivPal, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="knuth" data-toc="Optimasi Knuth">
    <h2>Optimasi Knuth: dari O(n³) ke O(n²)</h2>
    <div class="prose">
        <p>Pada soal menggabungkan tumpukan, titik belah terbaik bersifat <strong>monoton</strong>: <code>belah[l][r−1] ≤ belah[l][r] ≤ belah[l+1][r]</code>. Jadi k tidak perlu dicari dari l sampai r, cukup di antara dua batas itu. Jumlah total percobaan k menjadi O(n²), sehingga n sampai ±5000 masih sanggup.</p>
    </div>
    @include('lessons.code', ['cpp' => $ivKnuth, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Optimasi ini hanya sah jika biaya memenuhi syarat tertentu (<em>quadrangle inequality</em>). Biaya "jumlah batu" memenuhinya. Untuk soal lain, buktikan dulu atau uji dengan brute force sebelum memakainya.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Tumpukan / rantai matriks</h4><p>Belah di k, biaya gabungan terakhir.</p></div>
        <div class="pattern"><h4>Permainan ujung</h4><p>Simpan selisih skor; transisi dari ujung.</p></div>
        <div class="pattern"><h4>Palindrom</h4><p>Bandingkan s[l] dan s[r].</p></div>
        <div class="pattern"><h4>Menghapus rentang</h4><p>"Hapus balon / hapus huruf sama": pikirkan elemen yang dihapus <strong>terakhir</strong>.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="dp[l][r] membutuhkan interval yang lebih pendek. Mengisi berdasarkan panjang menjamin semuanya sudah siap.">
        <p class="quiz-q">Loop paling luar pada DP interval sebaiknya…</p>
        <div class="quiz-options">
            <button class="quiz-option">l dari 0 naik ke n − 1</button>
            <button class="quiz-option">Panjang interval dari 2 naik ke n</button>
            <button class="quiz-option">k dari 0 naik ke n − 1</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Ada O(n²) interval dan masing-masing mencoba O(n) titik belah, jadi O(n³).">
        <p class="quiz-q">Berapa kompleksitas DP menggabungkan tumpukan tanpa optimasi?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(n)</button>
            <button class="quiz-option">O(n²)</button>
            <button class="quiz-option">O(n³)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Tumpukan 3 dan 5: hanya ada satu cara, biaya 3 + 5 = 8.">
        <p class="quiz-q">Tumpukan <code>3 5</code>. Berapa dp[0][1]?</p>
        <div class="quiz-options">
            <button class="quiz-option">8</button>
            <button class="quiz-option">0</button>
            <button class="quiz-option">15</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
