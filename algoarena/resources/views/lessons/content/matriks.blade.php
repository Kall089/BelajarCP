@php
    $mxSteps = [
        ['Matriks dan perkaliannya', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
typedef vector<vector<long long>> Mat;

Mat kali(const Mat& A, const Mat& B) {
    int n = A.size(), k = B.size(), m = B[0].size();
    Mat C(n, vector<long long>(m, 0));
    for (int i = 0; i < n; i++)
        for (int t = 0; t < k; t++) {
            if (A[i][t] == 0) continue;
            for (int j = 0; j < m; j++)
                C[i][j] = (C[i][j] + A[i][t] * B[t][j]) % MOD;
        }
    return C;
}

CPP, <<<'TXT'
<p>C[i][j] adalah jumlah A[i][t] · B[t][j] untuk semua t: baris i dari A "dikali" kolom j dari B. Untuk matriks k × k biayanya O(k<sup>3</sup>).</p>
<ul>
    <li>A[i][t] dan B[t][j] masing-masing &lt; MOD ≈ 10<sup>9</sup>, sehingga hasil kalinya &lt; 10<sup>18</sup> dan masih muat di <code>long long</code> (batas ±9,2 · 10<sup>18</sup>).</li>
    <li>Ambil modulo <strong>setiap</strong> kali menjumlah. Jika ditunda, jumlah beberapa hasil kali 10<sup>18</sup> akan meluap.</li>
    <li>Urutan loop i, t, j lebih ramah cache daripada i, j, t, dan baris <code>continue</code> melewati nol dengan murah.</li>
</ul>
TXT],
        ['Pangkat cepat', <<<'CPP'
Mat pangkat(Mat M, long long n) {
    int k = M.size();
    Mat R(k, vector<long long>(k, 0));
    for (int i = 0; i < k; i++) R[i][i] = 1;    // matriks identitas
    while (n > 0) {
        if (n & 1) R = kali(R, M);
        M = kali(M, M);
        n >>= 1;
    }
    return R;
}

CPP, <<<'TXT'
<p>Persis seperti pangkat cepat bilangan di materi <a href="{{ route('lessons.show', 'fpb-modular') }}">FPB & Aritmetika Modular</a>, hanya saja "1" diganti matriks identitas I dan perkalian bilangan diganti perkalian matriks. M<sup>13</sup> = M<sup>8</sup> · M<sup>4</sup> · M<sup>1</sup> karena 13 = 1101<sub>2</sub>.</p>
<p>Perkalian matriks bersifat asosiatif (walaupun tidak komutatif), jadi pengelompokan seperti ini sah. Banyak perkalian O(log n), total O(k<sup>3</sup> log n).</p>
TXT],
        ['Fibonacci dengan matriks', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int t;
    cin >> t;
    while (t--) {
        long long n;
        cin >> n;
        Mat M = {{1, 1}, {1, 0}};
        cout << pangkat(M, n)[0][1] << '\n';
    }
    return 0;
}
CPP, <<<'TXT'
<p>Vektor keadaan (F(i+1), F(i)) berpindah ke (F(i+2), F(i+1)) dengan dikali M = [[1, 1], [1, 0]]. Setelah n langkah dari (F(1), F(0)) = (1, 0), kita mendapat M<sup>n</sup> = [[F(n+1), F(n)], [F(n), F(n−1)]], sehingga F(n) adalah elemen [0][1].</p>
<p>n = 10<sup>18</sup> hanya butuh sekitar 2 · 60 perkalian matriks 2 × 2.</p>
TXT],
    ];

    $mxPy = <<<'PY'
import sys
MOD = 10**9 + 7

def kali(A, B):
    return [[sum(A[i][t] * B[t][j] for t in range(len(B))) % MOD for j in range(len(B[0]))] for i in range(len(A))]

def pangkat(M, n):
    k = len(M)
    R = [[int(i == j) for j in range(k)] for i in range(k)]
    while n:
        if n & 1:
            R = kali(R, M)
        M = kali(M, M)
        n >>= 1
    return R

data = sys.stdin.read().split()
t = int(data[0])
print("\n".join(str(pangkat([[1, 1], [1, 0]], int(x))[0][1]) for x in data[1:1 + t]))
PY;

    $mxLinear = <<<'CPP'
// f(n) = c[0]·f(n-1) + c[1]·f(n-2) + ... + c[k-1]·f(n-k), nilai awal f(0..k-1).
// Keadaan: (f(i+k-1), f(i+k-2), ..., f(i)).  Baris 0 = koefisien, baris lain menggeser.
long long suku(vector<long long> c, vector<long long> awal, long long n) {
    int k = c.size();
    if (n < k) return awal[n];
    Mat T(k, vector<long long>(k, 0));
    for (int j = 0; j < k; j++) T[0][j] = c[j];
    for (int i = 1; i < k; i++) T[i][i - 1] = 1;           // f(i) turun satu posisi
    Mat P = pangkat(T, n - (k - 1));
    long long hasil = 0;
    for (int j = 0; j < k; j++)                            // dikali vektor awal (f(k-1), ..., f(0))
        hasil = (hasil + P[0][j] * awal[k - 1 - j]) % MOD;
    return hasil;
}
// c = {1, 1}, awal = {0, 1} -> Fibonacci;  c = {1, 1, 1}, awal = {0, 0, 1} -> Tribonacci
CPP;

    $mxConst = <<<'CPP'
// f(n) = f(n-1) + f(n-2) + 1  (suku konstan): tambahkan komponen "1" ke keadaan.
//   [f(n)  ]   [1 1 1] [f(n-1)]
//   [f(n-1)] = [1 0 0] [f(n-2)]
//   [1     ]   [0 0 1] [1     ]
Mat T = {{1, 1, 1}, {1, 0, 0}, {0, 0, 1}};

// Jumlah prefiks S(n) = F(0) + ... + F(n): simpan S di keadaan juga.
//   [F(n+1)]   [1 1 0] [F(n)  ]
//   [F(n)  ] = [1 0 0] [F(n-1)]
//   [S(n)  ]   [1 0 1] [S(n-1)]      karena S(n) = S(n-1) + F(n)
Mat U = {{1, 1, 0}, {1, 0, 0}, {1, 0, 1}};
CPP;

    $mxWalk = <<<'CPP'
// Banyak rute dengan TEPAT k langkah dari u ke v = (A^k)[u][v], A = matriks ketetanggaan.
// Alasannya: (A^2)[u][v] = jumlah A[u][w]·A[w][v] = banyak w yang menjadi titik tengah.
Mat A(n, vector<long long>(n, 0));
for (auto& e : sisi) A[e.first][e.second]++;            // sisi ganda ikut dihitung
Mat P = pangkat(A, k);
long long rute = P[0][n - 1];                            // dari simpul 0 ke simpul n-1
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengalikan dan memangkatkan matriks modulo 10<sup>9</sup>+7 dalam O(k<sup>3</sup> log n).</li>
        <li>Menghitung Fibonacci dan rekurens linear lain sampai suku ke-10<sup>18</sup>.</li>
        <li>Menyusun matriks transisi untuk rekurens dengan suku konstan atau jumlah prefiks.</li>
        <li>Menghitung banyak rute sepanjang tepat k langkah di sebuah graph.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'rekurens sebagai perkalian matriks', 'desc' => 'Satu langkah rekurens = dikali satu matriks.'])

<section class="lesson-section" id="masalah" data-toc="Masalahnya">
    <h2>DP yang Terlalu Panjang</h2>
    <div class="prose">
        <p>F(n) = F(n−1) + F(n−2) mudah dihitung dengan DP O(n). Tetapi bagaimana jika n = 10<sup>18</sup>? Bahkan satu miliar langkah per detik butuh 30 tahun. Kita perlu cara "melompat" banyak langkah sekaligus.</p>
        <p>Kuncinya: satu langkah rekurens linear bisa ditulis sebagai <strong>perkalian vektor keadaan dengan matriks tetap</strong>. Melangkah n kali berarti dikali matriks itu n kali, yaitu dikali M<sup>n</sup>. Dan M<sup>n</sup> bisa dihitung dengan pangkat cepat hanya dalam O(log n) perkalian.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Vektor keadaan</b><span>Semua yang perlu diingat untuk melangkah, misalnya (F(i+1), F(i)).</span></div>
        <div class="term"><b>Matriks transisi</b><span>M sehingga keadaan(i+1) = M · keadaan(i).</span></div>
        <div class="term"><b>Identitas I</b><span>Diagonal 1, sisanya 0. I · A = A, berperan seperti angka 1.</span></div>
        <div class="term"><b>Asosiatif</b><span>(AB)C = A(BC), syarat agar pangkat cepat sah. Tidak komutatif: AB ≠ BA umumnya.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Setiap bit 1 pada n membuat R dikali pangkat M yang sedang disimpan; setelah itu M dikuadratkan. Coba n = 32 (hanya satu bit 1) dan n = 31 (semua bit 1), lalu bandingkan banyak perkaliannya.</p></div>
    <div data-viz="matpow"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Fibonacci ke-n untuk n sampai 10^18.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Input: T, lalu T bilangan n. Cetak F(n) mod 10<sup>9</sup>+7, dengan F(0) = 0 dan F(1) = 1.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Eksponensiasi matriks: Fibonacci',
        'steps' => $mxSteps,
        'sample' => ['input' => "5\n0\n1\n10\n50\n1000000000000000000\n", 'output' => "0\n1\n55\n586268941\n209783453\n"],
        'py' => $mxPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: M<sup>13</sup></h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Bit (nilai)</th><th>Bit</th><th>R sesudah</th><th>M sesudah dikuadratkan</th></tr>
            <tr class="ok"><td>2<sup>0</sup> = 1</td><td>1</td><td>M<sup>1</sup> = [[1,1],[1,0]]</td><td>M<sup>2</sup> = [[2,1],[1,1]]</td></tr>
            <tr><td>2<sup>1</sup> = 2</td><td>0</td><td>tetap M<sup>1</sup></td><td>M<sup>4</sup> = [[5,3],[3,2]]</td></tr>
            <tr class="ok"><td>2<sup>2</sup> = 4</td><td>1</td><td>M<sup>5</sup> = [[8,5],[5,3]]</td><td>M<sup>8</sup> = [[34,21],[21,13]]</td></tr>
            <tr class="ok"><td>2<sup>3</sup> = 8</td><td>1</td><td>M<sup>13</sup> = [[377,233],[233,144]]</td><td>(tidak perlu)</td></tr>
        </table>
    </div>
    <div class="prose"><p>F(13) = 233, didapat dengan 5 perkalian matriks, bukan 12 penjumlahan berantai. Selisihnya tidak terasa untuk 13, tetapi untuk 10<sup>18</sup> itu 120 lawan 10<sup>18</sup>.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>DP biasa</td><td><code>O(n · k)</code></td><td>Hanya untuk n sampai ±10<sup>8</sup>.</td></tr>
        <tr><td>Pangkat matriks</td><td><code>O(k<sup>3</sup> log n)</code></td><td>k = ukuran keadaan. k = 100 masih cepat.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Modulo ditunda.</strong> <code>C[i][j] += A[i][t] * B[t][j]</code> tanpa <code>% MOD</code> setiap kali akan meluap setelah sekitar 9 suku. Ambil modulo di setiap penjumlahan.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Membaca n.</strong> n sampai 10<sup>18</sup> wajib <code>long long</code>. Di JavaScript, Number tidak tepat di atas 9 · 10<sup>15</sup>: baca n sebagai BigInt.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Salah geser indeks.</strong> Tentukan dengan jelas vektor awal dan berapa kali matriks dipangkatkan. Uji dengan n kecil yang bisa dihitung DP biasa sebelum mengirim.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'menyusun matriks transisi', 'desc' => 'Rekurens orde k, suku konstan, jumlah prefiks, dan rute di graph.'])

<section class="lesson-section" id="linear" data-toc="Rekurens Orde k">
    <h2>Rekurens Linear Orde k</h2>
    <div class="prose"><p>Keadaannya k suku terakhir. Baris pertama matriks berisi koefisien; baris lain hanya "menggeser" suku turun satu posisi.</p></div>
    @include('lessons.code', ['cpp' => $mxLinear, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="konstan" data-toc="Suku Konstan & Jumlah">
    <h2>Suku Konstan dan Jumlah Prefiks</h2>
    <div class="prose"><p>Apa pun yang berubah secara linear dari langkah ke langkah bisa dimasukkan ke vektor keadaan: konstanta (komponen bernilai 1 yang selalu tetap), jumlah sejauh ini, bahkan n sendiri (n berubah menjadi n + 1 = n + 1 · 1).</p></div>
    @include('lessons.code', ['cpp' => $mxConst, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="rute" data-toc="Rute Sepanjang k">
    <h2>Rute Sepanjang Tepat k Langkah</h2>
    <div class="prose"><p>Pangkat matriks ketetanggaan menghitung rute. Ini berguna ketika k sangat besar tetapi simpulnya sedikit (N ≤ 100).</p></div>
    @include('lessons.code', ['cpp' => $mxWalk, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Fibonacci / rekurens orde k</h4><p>Matriks pendamping k × k.</p></div>
        <div class="pattern"><h4>Ada suku konstan</h4><p>Tambahkan komponen 1 ke keadaan.</p></div>
        <div class="pattern"><h4>Jumlah suku 0..n</h4><p>Tambahkan S ke keadaan.</p></div>
        <div class="pattern"><h4>Rute k langkah</h4><p>(A<sup>k</sup>)[u][v].</p></div>
        <div class="pattern"><h4>DP dengan state kecil, n besar</h4><p>Transisi DP yang sama setiap langkah = matriks.</p></div>
        <div class="pattern"><h4>Jalur terpendek tepat k sisi</h4><p>Ganti (+, ×) dengan (min, +); pangkat cepat tetap berlaku.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Pangkat cepat butuh sekitar log2(n) kuadrat ditambah paling banyak log2(n) perkalian untuk bit 1.">
        <p class="quiz-q">Kira-kira berapa perkalian matriks untuk menghitung M<sup>n</sup> dengan n = 10<sup>18</sup>?</p>
        <div class="quiz-options">
            <button class="quiz-option">10<sup>9</sup></button>
            <button class="quiz-option">sekitar 120</button>
            <button class="quiz-option">sekitar 18</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Keadaan (f(n−1), f(n−2), 1) berukuran 3, jadi matriksnya 3 × 3.">
        <p class="quiz-q">Untuk f(n) = 2f(n−1) + f(n−2) + 5, berapa ukuran matriks transisi paling kecil yang wajar?</p>
        <div class="quiz-options">
            <button class="quiz-option">2 × 2</button>
            <button class="quiz-option">5 × 5</button>
            <button class="quiz-option">3 × 3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="(A²)[u][v] = Σ A[u][w]·A[w][v], yaitu banyak cara memilih titik tengah w.">
        <p class="quiz-q">Apa arti (A<sup>2</sup>)[u][v] untuk matriks ketetanggaan A?</p>
        <div class="quiz-options">
            <button class="quiz-option">Banyak rute dari u ke v dengan tepat 2 sisi</button>
            <button class="quiz-option">Jarak terpendek dari u ke v</button>
            <button class="quiz-option">Apakah u dan v bertetangga</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
