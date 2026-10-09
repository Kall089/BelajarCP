@php
    $kbSteps = [
        ['Faktorial dan invers', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

long long pangkat(long long a, long long b) {
    long long r = 1;
    a %= MOD;
    while (b > 0) {
        if (b & 1) r = r * a % MOD;
        a = a * a % MOD;
        b >>= 1;
    }
    return r;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Kurung Seimbang":</strong> berapa banyak barisan n pasang kurung yang seimbang (setiap "(" punya pasangan ")" sesudahnya, dan tidak pernah ada ")" berlebih di prefiks mana pun)? Cetak modulo 10<sup>9</sup> + 7, n ≤ 10<sup>6</sup>.</p>
<p>Jawabannya bilangan Catalan C<sub>n</sub> = C(2n, n) / (n + 1). Kita butuh faktorial sampai 2n dan pembagian modulo (invers Fermat).</p>
TXT],
        ['Rumus Catalan', <<<'CPP'

int main() {
    int n;
    cin >> n;
    vector<long long> fact(2 * n + 1);
    fact[0] = 1;
    for (int i = 1; i <= 2 * n; i++) fact[i] = fact[i - 1] * i % MOD;
    // C(2n, n) / (n + 1) = (2n)! / (n! · n! · (n + 1))
    long long penyebut = fact[n] * fact[n] % MOD * (n + 1) % MOD;
    cout << fact[2 * n] * pangkat(penyebut, MOD - 2) % MOD << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Mengapa C(2n, n)/(n + 1)? Dari semua C(2n, n) barisan dengan n "(" dan n ")", yang <em>tidak</em> seimbang ada C(2n, n + 1) (prinsip pantulan, di bawah). C(2n, n) − C(2n, n + 1) = C(2n, n)/(n + 1).</p>
<p>O(n) untuk faktorial, O(log MOD) untuk invers.</p>
TXT],
    ];

    $kbPy = <<<'PY'
MOD = 10**9 + 7
n = int(input())
fact = [1] * (2 * n + 1)
for i in range(1, 2 * n + 1):
    fact[i] = fact[i - 1] * i % MOD
penyebut = fact[n] * fact[n] % MOD * (n + 1) % MOD
print(fact[2 * n] * pow(penyebut, MOD - 2, MOD) % MOD)
PY;

    $kbStars = <<<'CPP'
// Bintang dan sekat: banyak solusi x1 + x2 + ... + xk = n dengan xi ≥ 0
//   = C(n + k - 1, k - 1)      (n bintang, k - 1 sekat)
// Jika xi ≥ ai: substitusi yi = xi - ai, sehingga n berkurang Σ ai
long long caraBagi(long long n, int k) { return C(n + k - 1, k - 1); }
CPP;

    $kbReflect = <<<'CPP'
// Jalan monoton (kanan/atas) dari (0,0) ke (a,b), a ≥ b, yang TIDAK PERNAH di atas diagonal y = x:
//   C(a + b, b) - C(a + b, b - 1)
// Prinsip pantulan: jalan "buruk" pertama kali menyentuh y = x + 1; cerminkan sisa jalannya
// terhadap garis itu → berpasangan satu-satu dengan jalan dari (0,0) ke (b - 1, a + 1).
CPP;

    $kbStirling = <<<'CPP'
// Stirling jenis kedua S(n, k): membagi n benda berlabel ke k kelompok tak berlabel, tidak ada yang kosong
// Benda ke-n: masuk ke salah satu dari k kelompok yang sudah ada, atau menjadi kelompok baru sendiri.
S[0][0] = 1;
for (int i = 1; i <= n; i++)
    for (int j = 1; j <= i; j++)
        S[i][j] = (j * S[i - 1][j] + S[i - 1][j - 1]) % MOD;
CPP;

    $kbBurnside = <<<'CPP'
// Lemma Burnside: banyak kalung n manik dengan k warna (putaran dianggap sama)
//   = (1/n) · Σ_{i=0}^{n-1} k^gcd(i, n)
// Putaran sejauh i membagi posisi menjadi gcd(i, n) siklus; pewarnaan yang tidak berubah
// harus seragam di setiap siklus → k^gcd(i, n) pewarnaan.
long long kalung = 0;
for (int i = 0; i < n; i++) kalung = (kalung + pangkat(k, __gcd(i, n))) % MOD;
kalung = kalung * pangkat(n, MOD - 2) % MOD;
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menghitung pembagian dengan <strong>bintang dan sekat</strong>.</li>
        <li>Mengenali <strong>bilangan Catalan</strong> dan membuktikan rumusnya dengan prinsip pantulan.</li>
        <li>Menghitung <strong>Stirling jenis kedua</strong> (membagi ke kelompok tak berlabel).</li>
        <li>Menghitung objek "sama jika diputar" dengan <strong>Lemma Burnside</strong>.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'alat pencacahan', 'desc' => 'Tiga pola yang muncul berulang kali di soal menghitung.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Membagi Permen">
    <h2>Intuisi: Membagi Permen dan Menyusun Kurung</h2>
    <div class="prose">
        <p><strong>Bintang dan sekat.</strong> Membagi 7 permen identik ke 3 anak sama dengan menyusun 7 bintang dan 2 sekat dalam satu baris: <code>★★|★★★★|★</code> berarti 2, 4, 1. Banyak susunan = C(9, 2) = 36.</p>
        <p><strong>Catalan.</strong> Barisan kurung seimbang, pohon biner dengan n simpul, cara mentriangulasi segi-(n+2), jalur di grid yang tidak melewati diagonal: semuanya dihitung oleh bilangan yang sama, 1, 1, 2, 5, 14, 42, …</p>
        <p><strong>Burnside.</strong> Berapa kalung 6 manik dengan 2 warna, jika kalung yang hanya berbeda putaran dianggap sama? Bukan 2<sup>6</sup>/6 (tidak bulat!). Burnside menghitungnya dengan merata-ratakan banyak pewarnaan yang tidak berubah oleh setiap putaran.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>C(n + k − 1, k − 1)</b><span>Solusi x₁ + … + xₖ = n dengan xᵢ ≥ 0.</span></div>
        <div class="term"><b>Cₙ = C(2n, n)/(n + 1)</b><span>Rekurens Cₙ = Σ Cᵢ Cₙ₋₁₋ᵢ.</span></div>
        <div class="term"><b>S(n, k)</b><span>S(n, k) = k·S(n−1, k) + S(n−1, k−1).</span></div>
        <div class="term"><b>Burnside</b><span>Banyak kelas = rata-rata banyak titik tetap.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Catalan">
    <h2>Visualisasi: Rekurens Catalan</h2>
    <div class="prose"><p>Setiap barisan seimbang berbentuk ( A ) B. Jika A berisi i pasang, B berisi n − 1 − i pasang, dan pilihan keduanya bebas.</p></div>
    <div data-viz="catalan"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menghitung di C++', 'desc' => 'Catalan modulo prima, bintang dan sekat, pantulan, dan Stirling.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kurung Seimbang</h2>
    @include('lessons.walkthrough', [
        'title' => 'Bilangan Catalan modulo 10^9+7',
        'steps' => $kbSteps,
        'sample' => ['input' => "10\n", 'output' => "16796\n"],
        'py' => $kbPy,
    ])
</section>

<section class="lesson-section" id="alat" data-toc="Bintang-Sekat, Pantulan, Stirling">
    <h2>Bintang dan Sekat, Pantulan, Stirling</h2>
    @include('lessons.code', ['cpp' => $kbStars, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $kbReflect, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $kbStirling, 'js' => null, 'py' => null])
    <table class="cx-table">
        <tr><th>Situasi</th><th>Benda</th><th>Wadah</th><th>Boleh kosong?</th><th>Rumus</th></tr>
        <tr><td>Permen ke anak</td><td>identik</td><td>berbeda</td><td>ya</td><td>C(n + k − 1, k − 1)</td></tr>
        <tr><td>Permen ke anak, semua dapat</td><td>identik</td><td>berbeda</td><td>tidak</td><td>C(n − 1, k − 1)</td></tr>
        <tr><td>Bola ke kotak</td><td>berbeda</td><td>berbeda</td><td>ya</td><td>k<sup>n</sup></td></tr>
        <tr><td>Bola ke kotak, semua terisi</td><td>berbeda</td><td>berbeda</td><td>tidak</td><td>k!·S(n, k)</td></tr>
        <tr><td>Orang ke kelompok tak bernama</td><td>berbeda</td><td>identik</td><td>tidak</td><td>S(n, k)</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Berbeda atau identik?</strong> Kesalahan paling sering di soal menghitung adalah salah membaca apakah benda atau wadahnya dibedakan. Coba hitung manual untuk n dan k kecil, lalu cocokkan dengan rumus.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'Lemma Burnside', 'desc' => 'Menghitung objek yang dianggap sama jika diputar.'])

<section class="lesson-section" id="burnside" data-toc="Lemma Burnside">
    <h2>Lemma Burnside untuk Kalung</h2>
    <div class="proof">
        <p>Misalkan G adalah himpunan operasi simetri (misalnya n putaran kalung). Lemma Burnside: banyak pewarnaan yang <em>berbeda secara esensial</em> = (1/|G|) Σ<sub>g∈G</sub> |Fix(g)|, dengan Fix(g) = pewarnaan yang tidak berubah oleh g.</p>
        <p>Putaran sejauh i posisi memecah n posisi menjadi gcd(i, n) siklus. Pewarnaan tetap jika semua posisi dalam satu siklus berwarna sama, jadi |Fix| = k<sup>gcd(i, n)</sup>. Untuk n = 6, k = 2: (64 + 2 + 4 + 8 + 4 + 2)/6 = 84/6 = 14 kalung.</p>
        <p>Untuk n besar, kelompokkan i berdasarkan d = gcd(i, n): ada φ(n/d) nilai i dengan gcd d, sehingga jumlahnya Σ<sub>d | n</sub> φ(n/d)·k<sup>d</sup>, hanya sebanyak pembagi n.</p>
    </div>
    @include('lessons.code', ['cpp' => $kbBurnside, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Barisan kurung, pohon biner</h4><p>Catalan.</p></div>
        <div class="pattern"><h4>Jalan tidak melewati garis</h4><p>Prinsip pantulan: total − jalan yang dicerminkan.</p></div>
        <div class="pattern"><h4>Membagi ke kelompok</h4><p>Stirling jenis kedua, atau Bell untuk semua k.</p></div>
        <div class="pattern"><h4>Sama jika diputar/dibalik</h4><p>Burnside (gelang: tambahkan juga pencerminan).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Bintang dan sekat: C(10 + 3 − 1, 3 − 1) = C(12, 2) = 66.">
        <p class="quiz-q">Banyak cara membagi 10 permen identik ke 3 anak (boleh ada yang tidak dapat)?</p>
        <div class="quiz-options">
            <button class="quiz-option">36</button>
            <button class="quiz-option">66</button>
            <button class="quiz-option">120</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="C₄ = C(8, 4)/5 = 70/5 = 14.">
        <p class="quiz-q">Banyak barisan 4 pasang kurung yang seimbang?</p>
        <div class="quiz-options">
            <button class="quiz-option">14</button>
            <button class="quiz-option">16</button>
            <button class="quiz-option">8</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Burnside: (2⁴ + 2¹ + 2² + 2¹)/4 = (16 + 2 + 4 + 2)/4 = 6.">
        <p class="quiz-q">Banyak kalung 4 manik dengan 2 warna (putaran dianggap sama)?</p>
        <div class="quiz-options">
            <button class="quiz-option">4</button>
            <button class="quiz-option">16</button>
            <button class="quiz-option">6</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
