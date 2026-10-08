@php
    $chSteps = [
        ['Konstanta modulo', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int MOD = 1e9 + 7;
const int MAXN = 1000;
int C[MAXN + 1][MAXN + 1];
CPP, <<<'TXT'
<p>Banyak cara memilih benda tumbuh sangat cepat: C(1000, 500) punya sekitar 300 digit. Soal hampir selalu meminta jawaban <strong>modulo 10<sup>9</sup> + 7</strong>, sebuah bilangan prima yang cukup besar dan muat di <code>int</code>.</p>
<p>Tabel dibuat global: 1001 × 1001 <code>int</code> ≈ 4 MB, terlalu besar untuk ditaruh di stack fungsi <code>main</code>.</p>
TXT],
        ['Isi segitiga Pascal', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    for (int i = 0; i <= MAXN; i++) {
        C[i][0] = 1;
        for (int j = 1; j <= i; j++)
            C[i][j] = (C[i - 1][j - 1] + C[i - 1][j]) % MOD;
    }
CPP, <<<'TXT'
<p>Untuk memilih j benda dari i benda, lihat <strong>benda terakhir</strong>:</p>
<ul>
    <li>Ikut dipilih: sisa j − 1 benda dipilih dari i − 1 benda, <code>C[i−1][j−1]</code> cara.</li>
    <li>Tidak dipilih: semua j benda dipilih dari i − 1 benda, <code>C[i−1][j]</code> cara.</li>
</ul>
<p>Kedua kelompok tidak pernah tumpang tindih, jadi jumlahnya adalah total cara (<strong>aturan penjumlahan</strong>). Modulo diambil setiap kali agar angka tidak meluap.</p>
<div class="wt-tip">Untuk j &gt; i, <code>C[i][j]</code> tetap 0 (array global otomatis berisi 0), dan memang tidak ada cara memilih lebih banyak dari yang tersedia.</div>
TXT],
        ['Jawab pertanyaan', <<<'CPP'

    int q;
    cin >> q;
    while (q--) {
        int n, k;
        cin >> n >> k;
        cout << (k > n ? 0 : C[n][k]) << '\n';
    }
    return 0;
}
CPP, <<<'TXT'
<p>Setelah tabel siap (O(MAXN²) ≈ 5 · 10<sup>5</sup> operasi), setiap pertanyaan dijawab dalam O(1).</p>
<pre>C(5, 2)  = 10
C(10, 3) = 120
C(1000, 500) mod (10⁹+7) = 159835829</pre>
TXT],
    ];

    $chPy = <<<'PY'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7
MAXN = 1000
C = [[0] * (MAXN + 1) for _ in range(MAXN + 1)]
for i in range(MAXN + 1):
    C[i][0] = 1
    for j in range(1, i + 1):
        C[i][j] = (C[i - 1][j - 1] + C[i - 1][j]) % MOD

q = int(input())
out = []
for _ in range(q):
    n, k = map(int, input().split())
    out.append(0 if k > n else C[n][k])
print("\n".join(map(str, out)))
PY;

    $chModOps = <<<'CPP'
const long long MOD = 1e9 + 7;
long long a = 999999999, b = 888888888;

long long jumlah = (a + b) % MOD;               // aman: a + b < 2^63
long long kali   = (a % MOD) * (b % MOD) % MOD; // hasil kali < 10^18, masih muat long long
long long kurang = ((a - b) % MOD + MOD) % MOD; // + MOD agar tidak negatif

// SALAH: int x = a * b % MOD;  ← a * b dihitung dalam int dan meluap!
// SALAH: (a / b) % MOD ≠ (a % MOD) / (b % MOD)   ← pembagian butuh invers modular
CPP;

    $chFact = <<<'CPP'
// C(n, k) mod p untuk n sampai 10^6 dalam O(1) per pertanyaan.
const long long MOD = 1e9 + 7;
long long fact[N + 1], inv[N + 1];

long long pangkat(long long b, long long e) {      // b^e mod MOD, O(log e)
    long long r = 1;
    b %= MOD;
    while (e > 0) {
        if (e & 1) r = r * b % MOD;
        b = b * b % MOD;
        e >>= 1;
    }
    return r;
}

void siapkan() {
    fact[0] = 1;
    for (int i = 1; i <= N; i++) fact[i] = fact[i - 1] * i % MOD;
    inv[N] = pangkat(fact[N], MOD - 2);            // Teorema kecil Fermat
    for (int i = N; i > 0; i--) inv[i - 1] = inv[i] * i % MOD;
}

long long nCk(int n, int k) {
    if (k < 0 || k > n) return 0;
    return fact[n] * inv[k] % MOD * inv[n - k] % MOD;
}
CPP;

    $chCatalan = <<<'CPP'
// Banyak barisan kurung seimbang dengan n pasang kurung (bilangan Catalan).
// Kurung "(" pertama berpasangan dengan ")" di posisi tertentu:
// di dalamnya ada i pasang, di luarnya (setelahnya) ada n-1-i pasang.
cat[0] = 1;
for (int n = 1; n <= N; n++)
    for (int i = 0; i < n; i++)
        cat[n] = (cat[n] + cat[i] * cat[n - 1 - i]) % MOD;
// cat: 1, 1, 2, 5, 14, 42, 132, ...
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai <strong>aturan penjumlahan</strong> dan <strong>aturan perkalian</strong> untuk merancang DP menghitung cara.</li>
        <li>Mengerjakan aritmetika <strong>modulo</strong> dengan benar: tambah, kurang, kali, tanpa meluap.</li>
        <li>Membangun segitiga Pascal untuk C(n, k), lalu versi O(1) per pertanyaan dengan faktorial dan invers.</li>
        <li>Mengenali pola menghitung klasik: Catalan, jalur grid, dan "pecah berdasarkan elemen pertama/terakhir".</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'prinsip menghitung', 'desc' => 'Aturan penjumlahan, aturan perkalian, dan mengapa jawaban perlu modulo.'])

<section class="lesson-section" id="aturan" data-toc="Aturan Menghitung">
    <h2>Dua Aturan Dasar Menghitung</h2>
    <div class="prose">
        <p>Hampir semua DP menghitung cara dibangun dari dua aturan berikut:</p>
        <div class="term-grid">
            <div class="term"><b>Aturan penjumlahan</b><span>Jika semua cara bisa dibagi ke beberapa kelompok yang <strong>tidak tumpang tindih</strong>, total cara = jumlah cara setiap kelompok. Contoh: tangga 1 atau 2 langkah, <code>dp[i] = dp[i−1] + dp[i−2]</code>.</span></div>
            <div class="term"><b>Aturan perkalian</b><span>Jika sebuah cara terdiri dari dua pilihan yang <strong>saling bebas</strong>, total = hasil kali. Contoh: 3 baju × 4 celana = 12 pasangan.</span></div>
        </div>
        <p>Kunci merancang DP menghitung: bagi semua kemungkinan berdasarkan <strong>keputusan terakhir</strong> (atau pertama) sedemikian rupa sehingga setiap cara masuk ke <strong>tepat satu</strong> kelompok. Jika ada cara yang terhitung dua kali, jawabannya salah.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Pertanyaan pengecekan: "apakah setiap cara terhitung <strong>tepat sekali</strong>?" Tidak terlewat (semua kemungkinan langkah terakhir dicoba) dan tidak ganda (kelompoknya terpisah).</p>
    </div>
</section>

<section class="lesson-section" id="pascal" data-toc="Segitiga Pascal">
    <h2>C(n, k): Memilih k dari n</h2>
    <div class="prose">
        <p>Berapa cara memilih 2 anggota piket dari 4 siswa {A, B, C, D}? Daftar semuanya: AB, AC, AD, BC, BD, CD. Ada 6. Dengan DP, lihat siswa terakhir (D):</p>
        <ul>
            <li>D ikut terpilih: tinggal pilih 1 dari {A, B, C} → 3 cara (AD, BD, CD).</li>
            <li>D tidak terpilih: pilih 2 dari {A, B, C} → 3 cara (AB, AC, BC).</li>
        </ul>
    </div>
    <div class="recurrence"><small>Rumus segitiga Pascal</small>C(n, 0) = C(n, n) = 1
C(n, k) = C(n−1, k−1) + C(n−1, k)        untuk 0 &lt; k &lt; n</div>
    <div class="diagram-grid" style="max-width: 520px">
        <div>
            <h5>Segitiga Pascal (baris 0–5)</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(6, auto)">
                <span class="ok">1</span><span class="dim"></span><span class="dim"></span><span class="dim"></span><span class="dim"></span><span class="dim"></span>
                <span class="ok">1</span><span>1</span><span class="dim"></span><span class="dim"></span><span class="dim"></span><span class="dim"></span>
                <span class="ok">1</span><span>2</span><span>1</span><span class="dim"></span><span class="dim"></span><span class="dim"></span>
                <span class="ok">1</span><span class="dep">3</span><span class="dep2">3</span><span>1</span><span class="dim"></span><span class="dim"></span>
                <span class="ok">1</span><span>4</span><span class="cur">6</span><span>4</span><span>1</span><span class="dim"></span>
                <span class="ok">1</span><span>5</span><span>10</span><span>10</span><span>5</span><span>1</span>
            </div>
            <p class="muted" style="font-size: 12.5px; margin: 6px 4px 0">C(4, 2) = C(3, 1) + C(3, 2) = 3 + 3 = 6</p>
        </div>
    </div>
</section>

<section class="lesson-section" id="modulo" data-toc="Aritmetika Modulo">
    <h2>Aritmetika Modulo</h2>
    <div class="prose">
        <p>Banyak cara sering jauh melebihi <code>long long</code>. Soal lalu meminta sisa pembagiannya dengan M (biasanya 10<sup>9</sup> + 7). Untungnya, penjumlahan, pengurangan, dan perkalian bisa di-modulo <strong>di setiap langkah</strong> tanpa mengubah hasil akhir:</p>
    </div>
    <div class="recurrence"><small>Sifat modulo</small>(a + b) mod M = ((a mod M) + (b mod M)) mod M
(a − b) mod M = ((a mod M) − (b mod M) + M) mod M
(a × b) mod M = ((a mod M) × (b mod M)) mod M</div>
    @include('lessons.code', ['cpp' => $chModOps, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Perkalian dua angka &lt; 10<sup>9</sup>+7 bisa mencapai 10<sup>18</sup></strong>, melebihi <code>int</code> tetapi masih muat di <code>long long</code> (sampai 9,2 · 10<sup>18</sup>). Selalu kalikan dalam <code>long long</code>.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Mengisi Segitiga Pascal</h2>
    <div class="prose"><p>Coba ganti modulo menjadi 7 atau 10 untuk melihat bagaimana angka "dibungkus" kembali saat melewati batas.</p></div>
    <div data-viz="pascal"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan DP Menghitung Cara</h2>
    <div class="steps">
        <div class="step-card"><b>State</b><span><code>dp[...]</code> = banyak cara untuk subsoal itu.</span></div>
        <div class="step-card"><b>Pecah berdasarkan keputusan terakhir</b><span>Setiap cara harus masuk tepat satu kelompok.</span></div>
        <div class="step-card"><b>Base case</b><span>Biasanya <code>dp[0] = 1</code>: tepat satu cara untuk "tidak melakukan apa-apa".</span></div>
        <div class="step-card"><b>Modulo di setiap langkah</b><span>Jumlahkan lalu <code>% MOD</code>; kali dalam <code>long long</code>.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'C(n, k) di C++', 'desc' => 'Tabel Pascal untuk menjawab banyak pertanyaan sekaligus.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Banyak Pertanyaan C(n, k)</h2>
    <div class="prose"><p><strong>Soal contoh:</strong> Q pertanyaan, masing-masing <code>n k</code> (n ≤ 1000). Cetak C(n, k) mod 10<sup>9</sup> + 7.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Segitiga Pascal modulo',
        'steps' => $chSteps,
        'sample' => ['input' => "3\n5 2\n10 3\n1000 500\n", 'output' => "10\n120\n159835829\n"],
        'py' => $chPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Menghitung">
    <h2>Pola-Pola Menghitung</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Jalur grid</h4><p>Dari (0,0) ke (R,C) hanya kanan/bawah: C(R + C, R). Dengan rintangan: DP grid.</p></div>
        <div class="pattern"><h4>Barisan biner tanpa "11"</h4><p><code>dp[i] = dp[i−1] + dp[i−2]</code>: Fibonacci lagi!</p></div>
        <div class="pattern"><h4>Membagi permen</h4><p>n permen identik ke k anak (boleh 0): C(n + k − 1, k − 1).</p></div>
        <div class="pattern"><h4>Kurung seimbang</h4><p>Bilangan Catalan: pecah berdasarkan pasangan kurung pertama.</p></div>
        <div class="pattern"><h4>Subset berjumlah S</h4><p>Knapsack dengan penjumlahan: <code>dp[s] += dp[s − x]</code>.</p></div>
        <div class="pattern"><h4>Komplemen</h4><p>"Paling sedikit satu" = total − "tidak ada sama sekali".</p></div>
    </div>
    <div class="prose"><h3>Contoh: bilangan Catalan</h3></div>
    @include('lessons.code', ['cpp' => $chCatalan, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara menghitung C(n, k)</th><th>Persiapan</th><th>Per pertanyaan</th><th>Batas n</th></tr>
        <tr><td>Segitiga Pascal</td><td><code>O(n²)</code></td><td><code>O(1)</code></td><td>±5000</td></tr>
        <tr><td>Faktorial + invers modular</td><td><code>O(n)</code></td><td><code>O(1)</code></td><td>±10<sup>7</sup></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa modulo di tengah.</strong> Menjumlahkan banyak suku lalu me-modulo hanya di akhir bisa meluap. Modulo setelah <em>setiap</em> penjumlahan atau perkalian.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Hasil pengurangan negatif.</strong> Di C++, <code>(-3) % 7 = -3</code>, bukan 4. Tambahkan MOD sebelum modulo terakhir.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'pembagian dalam modulo', 'desc' => 'Pangkat cepat, Teorema Fermat, dan C(n, k) dalam O(1).'])

<section class="lesson-section" id="invers" data-toc="Invers Modular">
    <h2>Invers Modular dan Faktorial</h2>
    <div class="prose">
        <p>Rumus <code>C(n, k) = n! / (k! (n−k)!)</code> butuh <strong>pembagian</strong>, dan pembagian tidak bisa langsung di-modulo. Solusinya: ganti "bagi dengan x" dengan "kali dengan <strong>invers</strong> x", yaitu bilangan y sehingga <code>x · y ≡ 1 (mod p)</code>.</p>
        <p>Jika p prima, <strong>Teorema kecil Fermat</strong> mengatakan <code>x<sup>p−1</sup> ≡ 1</code>, sehingga invers x adalah <code>x<sup>p−2</sup> mod p</code>. Pangkat sebesar itu dihitung dengan <strong>pangkat cepat</strong> dalam O(log p).</p>
    </div>
    @include('lessons.code', ['cpp' => $chFact, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Mengapa inv[i−1] = inv[i] · i?</strong> Karena <code>1/(i−1)! = i / i!</code>. Jadi cukup satu kali pangkat (untuk inv[N]) dan sisanya dihitung mundur dalam O(N).</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="C(5, 2) = C(4, 1) + C(4, 2) = 4 + 6 = 10.">
        <p class="quiz-q">Berapa C(5, 2)?</p>
        <div class="quiz-options">
            <button class="quiz-option">5</button>
            <button class="quiz-option">10</button>
            <button class="quiz-option">20</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="a dan b masing-masing di bawah 10⁹ + 7, sehingga a · b bisa mencapai ±10¹⁸: melewati int tetapi muat di long long.">
        <p class="quiz-q">a, b &lt; 10<sup>9</sup> + 7. Tipe apa yang aman untuk menghitung <code>a * b % MOD</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">int</button>
            <button class="quiz-option">unsigned int</button>
            <button class="quiz-option">long long</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Dalam C++, sisa bagi bilangan negatif tetap negatif. ((a − b) % M + M) % M memastikan hasilnya di 0..M−1.">
        <p class="quiz-q">Bagaimana cara aman menghitung (a − b) mod M di C++?</p>
        <div class="quiz-options">
            <button class="quiz-option">((a − b) % M + M) % M</button>
            <button class="quiz-option">(a − b) % M</button>
            <button class="quiz-option">abs(a − b) % M</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
