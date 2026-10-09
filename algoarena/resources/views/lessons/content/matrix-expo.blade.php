@php
    $mxSteps = [
        ['Perkalian matriks modulo', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
typedef vector<vector<long long>> Mat;

Mat kali(const Mat& A, const Mat& B) {
    int n = A.size();
    Mat C(n, vector<long long>(n, 0));
    for (int i = 0; i < n; i++)
        for (int k = 0; k < n; k++) {
            if (A[i][k] == 0) continue;
            for (int j = 0; j < n; j++)
                C[i][j] = (C[i][j] + A[i][k] * B[k][j]) % MOD;
        }
    return C;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Fibonacci Raksasa":</strong> cetak F(n) mod 10<sup>9</sup> + 7 untuk n sampai 10<sup>18</sup>, dengan F(0) = 0, F(1) = 1.</p>
<p>Perkalian matriks k × k butuh O(k³). Urutan loop i–k–j lebih ramah cache daripada i–j–k, dan baris A[i][k] = 0 bisa dilewati.</p>
TXT],
        ['Perpangkatan cepat untuk matriks', <<<'CPP'

Mat pangkat(Mat A, long long e) {
    int n = A.size();
    Mat R(n, vector<long long>(n, 0));
    for (int i = 0; i < n; i++) R[i][i] = 1;      // matriks identitas
    while (e > 0) {
        if (e & 1) R = kali(R, A);
        A = kali(A, A);
        e >>= 1;
    }
    return R;
}
CPP, <<<'TXT'
<p>Persis seperti perpangkatan cepat bilangan, hanya saja "1" diganti matriks identitas dan perkalian bilangan diganti perkalian matriks. Perkalian matriks bersifat asosiatif, jadi pengelompokan A<sup>8</sup>·A<sup>4</sup>·A<sup>1</sup> sah.</p>
TXT],
        ['Fibonacci dari matriks', <<<'CPP'

int main() {
    long long n;
    cin >> n;
    Mat A = {{1, 1}, {1, 0}};          // [F(k+1), F(k)] = A · [F(k), F(k-1)]
    Mat R = pangkat(A, n);              // A^n = [[F(n+1), F(n)], [F(n), F(n-1)]]
    cout << R[0][1] << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Total O(2³ log n) ≈ 500 operasi untuk n = 10<sup>18</sup>. Untuk rekurens dengan k suku, matriksnya k × k dan biayanya O(k³ log n).</p>
TXT],
    ];

    $mxPy = <<<'PY'
MOD = 10**9 + 7

def kali(A, B):
    return [[sum(a * b for a, b in zip(baris, kolom)) % MOD for kolom in zip(*B)] for baris in A]

def pangkat(A, e):
    n = len(A)
    R = [[int(i == j) for j in range(n)] for i in range(n)]
    while e:
        if e & 1:
            R = kali(R, A)
        A = kali(A, A)
        e >>= 1
    return R

n = int(input())
print(pangkat([[1, 1], [1, 0]], n)[0][1])
PY;

    $mxGeneral = <<<'CPP'
// f(n) = c1·f(n-1) + c2·f(n-2) + ... + ck·f(n-k): matriks pendamping (companion) k × k
//  [ f(n)   ]   [ c1 c2 ... ck ]   [ f(n-1) ]
//  [ f(n-1) ] = [ 1  0  ...  0 ] · [ f(n-2) ]
//  [  ...   ]   [ 0  1  ...  0 ]   [  ...   ]
//  [f(n-k+1)]   [ 0  ... 1   0 ]   [ f(n-k) ]
Mat T(k, vector<long long>(k, 0));
for (int j = 0; j < k; j++) T[0][j] = c[j];
for (int i = 1; i < k; i++) T[i][i - 1] = 1;
// f(n) = (T^(n-k+1) · [f(k-1), ..., f(0)])[0]   untuk n ≥ k
CPP;

    $mxConst = <<<'CPP'
// Menambah konstanta atau jumlah prefix: perbesar keadaan
// g(n) = g(n-1) + g(n-2) + 5     → keadaan [g(n), g(n-1), 1]
// [g(n)  ]   [1 1 5]   [g(n-1)]
// [g(n-1)] = [1 0 0] · [g(n-2)]
// [  1   ]   [0 0 1]   [  1   ]
CPP;

    $mxWalk = <<<'CPP'
// Banyak jalan (walk) sepanjang tepat K dari u ke v = (Adj^K)[u][v]
// Adj[i][j] = banyak sisi dari i ke j. Untuk K sampai 10^18 dan n ≤ 100: O(n³ log K).
Mat P = pangkat(Adj, K);
cout << P[u][v] << "\n";
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menulis <strong>rekurens linear</strong> sebagai perkalian matriks dengan vektor keadaan.</li>
        <li>Menghitung suku ke-n untuk n sampai 10<sup>18</sup> dengan <strong>perpangkatan matriks</strong> dalam O(k³ log n).</li>
        <li>Menambahkan konstanta atau jumlah prefix ke dalam keadaan.</li>
        <li>Menghitung <strong>banyak jalan sepanjang K</strong> di graph dengan pangkat matriks ketetanggaan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'rekurens sebagai perkalian', 'desc' => 'Satu langkah rekurens = satu perkalian matriks.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Mesin Satu Langkah">
    <h2>Intuisi: Mesin Satu Langkah</h2>
    <div class="prose">
        <p>Bayangkan sebuah mesin yang menerima keadaan hari ini [F(k), F(k−1)] dan mengeluarkan keadaan besok [F(k+1), F(k)]. Mesin itu <em>linear</em>: setiap keluaran adalah jumlah berbobot dari masukan. Mesin linear bisa ditulis sebagai matriks, dan menjalankan mesin n kali sama dengan mengalikan dengan matriksnya n kali, yaitu memangkatkan matriks.</p>
        <p>Perpangkatan bisa dipercepat dengan kuadrat berulang, persis seperti a<sup>b</sup> mod m. Maka F(10<sup>18</sup>) bisa dihitung dalam sekitar 60 perkalian matriks 2 × 2.</p>
    </div>
    <div class="recurrence"><small>Fibonacci sebagai matriks</small>[ F(n+1) ]   [ 1  1 ]   [ F(n)   ]
[ F(n)   ] = [ 1  0 ] · [ F(n-1) ]

[ 1 1 ]^n   [ F(n+1)  F(n)   ]
[ 1 0 ]   = [ F(n)    F(n-1) ]</div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Fibonacci dengan Kuadrat Berulang</h2>
    <div class="prose"><p>Setiap bit 1 dari n mengalikan "hasil" dengan A<sup>2<sup>k</sup></sup>. Elemen kanan atas hasil selalu sebuah bilangan Fibonacci.</p></div>
    <div data-viz="matpow"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'perpangkatan matriks di C++', 'desc' => 'Fibonacci raksasa, rekurens umum, dan menambah konstanta.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Fibonacci Raksasa</h2>
    @include('lessons.walkthrough', [
        'title' => 'F(n) mod 10^9+7 untuk n ≤ 10^18',
        'steps' => $mxSteps,
        'sample' => ['input' => "1000000000000000000\n", 'output' => "209783453\n"],
        'py' => $mxPy,
    ])
</section>

<section class="lesson-section" id="umum" data-toc="Rekurens Umum">
    <h2>Rekurens Linear Umum</h2>
    <div class="prose"><p>Rekurens dengan k suku memakai keadaan berisi k nilai terakhir. Baris pertama matriks berisi koefisien; baris-baris lain hanya "menggeser" keadaan.</p></div>
    @include('lessons.code', ['cpp' => $mxGeneral, 'js' => null, 'py' => null])
    <div class="prose"><p>Konstanta, suku n, atau jumlah prefix juga bisa dimasukkan dengan menambah komponen keadaan.</p></div>
    @include('lessons.code', ['cpp' => $mxConst, 'js' => null, 'py' => null])
    <table class="cx-table">
        <tr><th>Masalah</th><th>Ukuran matriks</th><th>Waktu</th></tr>
        <tr><td>Fibonacci</td><td>2 × 2</td><td><code>O(8 log n)</code></td></tr>
        <tr><td>Rekurens k suku</td><td>k × k</td><td><code>O(k³ log n)</code></td></tr>
        <tr><td>Jalan sepanjang K di graph n simpul</td><td>n × n</td><td><code>O(n³ log K)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pangkat yang salah satu.</strong> Tuliskan jelas vektor awal dan berapa kali mesin dijalankan. Uji dengan n kecil (0, 1, 2, k) dibandingkan dengan loop biasa.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow dalam penjumlahan.</strong> Ambil modulo setelah setiap penambahan A[i][k]·B[k][j] (atau kumpulkan di <code>unsigned long long</code> dan modulo setiap beberapa suku).</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'matriks di graph', 'desc' => 'Pangkat matriks ketetanggaan menghitung jalan.'])

<section class="lesson-section" id="graph" data-toc="Jalan Sepanjang K">
    <h2>Banyak Jalan Sepanjang K</h2>
    <div class="proof">
        <p>Misalkan A<sup>t</sup>[u][v] adalah banyak jalan sepanjang t dari u ke v. Jalan sepanjang t + 1 terdiri dari jalan sepanjang t ke suatu w, lalu satu sisi w → v. Jadi A<sup>t+1</sup>[u][v] = Σ<sub>w</sub> A<sup>t</sup>[u][w] · A[w][v], yang persis definisi perkalian matriks. Dengan induksi, pangkat ke-K matriks ketetanggaan memberi semua banyak jalan sepanjang K.</p>
    </div>
    @include('lessons.code', ['cpp' => $mxWalk, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>n sampai 10<sup>18</sup>, rekurens linear</h4><p>Matriks pendamping + pangkat.</p></div>
        <div class="pattern"><h4>Jalan / string dengan otomaton</h4><p>Keadaan = simpul otomaton, transisi = matriks.</p></div>
        <div class="pattern"><h4>Jalan terpendek tepat K sisi</h4><p>Perkalian matriks (min, +) dengan pangkat yang sama.</p></div>
        <div class="pattern"><h4>Ubin / pewarnaan dengan pola</h4><p>Keadaan = profil kolom terakhir (DP profil) yang diulang.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Rekurens dengan 3 suku sebelumnya butuh keadaan 3 nilai, jadi matriks 3 × 3.">
        <p class="quiz-q">f(n) = f(n−1) + 2f(n−2) + 3f(n−3). Ukuran matriks minimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">2 × 2</button>
            <button class="quiz-option">3 × 3</button>
            <button class="quiz-option">4 × 4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Konstanta butuh komponen keadaan tambahan yang selalu 1: [g(n), g(n−1), 1], jadi 3 × 3.">
        <p class="quiz-q">g(n) = g(n−1) + g(n−2) + 7. Ukuran matriks?</p>
        <div class="quiz-options">
            <button class="quiz-option">3 × 3</button>
            <button class="quiz-option">2 × 2</button>
            <button class="quiz-option">Tidak bisa dengan matriks</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="n³ log K = 100³ · 60 = 6 · 10⁷ operasi.">
        <p class="quiz-q">Graph 100 simpul, banyak jalan sepanjang 10<sup>18</sup>. Kira-kira berapa operasi?</p>
        <div class="quiz-options">
            <button class="quiz-option">10<sup>18</sup></button>
            <button class="quiz-option">10<sup>4</sup></button>
            <button class="quiz-option">6 · 10<sup>7</sup></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
