@php
    $kmSteps = [
        ['Pangkat cepat untuk satu invers', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;
const int MAXN = 1000000;
long long fakt[MAXN + 1], invF[MAXN + 1];

long long pangkat(long long a, long long e) {
    long long h = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) h = h * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return h;
}

CPP, <<<'TXT'
<p><code>fakt[i] = i! mod p</code> dan <code>invF[i] = (i!)<sup>−1</sup> mod p</code>. Karena p = 10<sup>9</sup> + 7 prima, invers dihitung dengan teorema kecil Fermat: x<sup>p−2</sup>. Kita hanya butuh <strong>satu</strong> pemanggilan pangkat cepat; sisanya diturunkan dengan trik di langkah berikutnya.</p>
TXT],
        ['Fungsi C(n, k)', <<<'CPP'
long long C(int n, int k) {
    if (k < 0 || k > n) return 0;
    return fakt[n] * invF[k] % MOD * invF[n - k] % MOD;
}

CPP, <<<'TXT'
<p><code>C(n, k) = n! / (k! · (n − k)!)</code>. Pembagian modulo diganti perkalian dengan invers, dan setiap perkalian langsung dimodulo agar tidak overflow (dua bilangan &lt; 10<sup>9</sup> + 7 dikali masih muat di <code>long long</code>).</p>
<p>Nilai k di luar 0..n berarti tidak ada cara: kembalikan 0. Pemeriksaan ini mencegah indeks negatif dan sering menyederhanakan rumus di soal.</p>
TXT],
        ['Prekomputasi O(N)', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    fakt[0] = 1;
    for (int i = 1; i <= MAXN; i++) fakt[i] = fakt[i - 1] * i % MOD;
    invF[MAXN] = pangkat(fakt[MAXN], MOD - 2);
    for (int i = MAXN; i > 0; i--) invF[i - 1] = invF[i] * i % MOD;

CPP, <<<'TXT'
<p>Faktorial dihitung maju. Untuk invers, hitung hanya <code>invF[MAXN]</code> dengan pangkat cepat, lalu mundur: karena <code>(i − 1)! = i! / i</code>, maka <code>((i − 1)!)<sup>−1</sup> = (i!)<sup>−1</sup> · i</code>. Seluruh persiapan O(N + log p), setelah itu setiap C(n, k) O(1).</p>
TXT],
        ['Menjawab pertanyaan', <<<'CPP'
    int q;
    cin >> q;
    string out;
    while (q--) {
        int n, k;
        cin >> n >> k;
        out += to_string(C(n, k)) + "\n";
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Bahkan C(10<sup>6</sup>, 5 · 10<sup>5</sup>), yang aslinya punya ratusan ribu digit, langsung dijawab. Pascal O(N²) tidak mungkin untuk N = 10<sup>6</sup>; itulah alasan teknik ini.</p>
TXT],
    ];

    $kmPy = <<<'PY'
import sys
MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
q = int(data[0])
qs = [(int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(q)]
N = max([n for n, _ in qs] + [1])
fakt = [1] * (N + 1)
for i in range(1, N + 1):
    fakt[i] = fakt[i - 1] * i % MOD
invF = [1] * (N + 1)
invF[N] = pow(fakt[N], MOD - 2, MOD)
for i in range(N, 0, -1):
    invF[i - 1] = invF[i] * i % MOD
out = []
for n, k in qs:
    out.append(0 if k < 0 or k > n else fakt[n] * invF[k] % MOD * invF[n - k] % MOD)
print("\n".join(map(str, out)))
PY;

    $kmMulti = <<<'CPP'
// Banyak susunan berbeda huruf-huruf "MATEMATIKA" (10 huruf: M×2, A×3, T×2, E, I, K).
// Semua susunan 10! terhitung berlebih: menukar dua M tidak menghasilkan kata baru, dst.
long long hasil = fakt[n];
for (int c = 0; c < 26; c++) hasil = hasil * invF[jumlah[c]] % MOD;   // bagi c_a! · c_b! · ...
// 10! / (2! · 3! · 2!) = 151200
CPP;

    $kmStars = <<<'CPP'
// Bagi n permen IDENTIK ke k anak (boleh ada yang 0): susun n bintang dan k − 1 sekat.
//   ★★|★|★★★  ->  anak 1 dapat 2, anak 2 dapat 1, anak 3 dapat 3
// Banyak cara = memilih posisi k − 1 sekat di antara n + k − 1 tempat:
long long caraBoleh0 = C(n + k - 1, k - 1);
// Jika setiap anak minimal 1: beri dulu masing-masing 1, sisanya n − k dibagi bebas:
long long caraMin1 = C(n - 1, k - 1);
CPP;

    $kmIncl = <<<'CPP'
// Berapa bilangan 1..N yang habis dibagi salah satu dari prima p[0..k-1]?
// |A ∪ B ∪ C| = |A| + |B| + |C| − |A∩B| − |A∩C| − |B∩C| + |A∩B∩C|
long long banyak = 0;
for (int mask = 1; mask < (1 << k); mask++) {
    long long kali = 1;
    int bit = 0;
    for (int i = 0; i < k; i++)
        if (mask >> i & 1) { kali *= p[i]; bit++; }   // habis dibagi semua prima di mask
    if (bit % 2 == 1) banyak += N / kali;            // ganjil: tambah
    else banyak -= N / kali;                         // genap: kurang
}
// N = 100, p = {2, 3, 5}: 50 + 33 + 20 − 16 − 10 − 6 + 3 = 74
CPP;

    $kmDerange = <<<'CPP'
// Derangement D(n): permutasi n kado sehingga TIDAK ADA yang mendapat kadonya sendiri.
// Inklusi–eksklusi: D(n) = Σ_{i=0..n} (−1)^i · C(n, i) · (n − i)!
// Atau rekurens: D(n) = (n − 1) · (D(n − 1) + D(n − 2)), D(0) = 1, D(1) = 0
D[0] = 1;
D[1] = 0;
for (int i = 2; i <= n; i++) D[i] = (i - 1) * (D[i - 1] + D[i - 2]) % MOD;
// D = 1, 0, 1, 2, 9, 44, 265, ...   (D(n) / n! mendekati 1/e ≈ 0,368)
CPP;

    $kmObst = <<<'CPP'
// Jalur grid H × W (ke kanan/bawah) yang menghindari K lubang, K kecil tetapi H, W sampai 10^5.
// Urutkan lubang; tambahkan tujuan (H, W) sebagai "lubang" terakhir.
// f[i] = jalur dari (1, 1) ke lubang i yang tidak menginjak lubang LAIN sebelumnya.
sort(lubang.begin(), lubang.end());
lubang.push_back({H, W});
for (int i = 0; i < (int)lubang.size(); i++) {
    int r = lubang[i].first, c = lubang[i].second;
    f[i] = C(r + c - 2, r - 1);                          // semua jalur ke (r, c)
    for (int j = 0; j < i; j++) {
        int rj = lubang[j].first, cj = lubang[j].second;
        if (rj <= r && cj <= c)                          // lubang j = lubang PERTAMA yang diinjak
            f[i] = (f[i] - f[j] * C(r - rj + c - cj, r - rj) % MOD + MOD) % MOD;
    }
}
// jawaban = f.back(), O(K² + H + W)
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menghitung C(n, k) modulo prima untuk n sampai 10<sup>6</sup> dalam O(1) per pertanyaan.</li>
        <li>Memakai permutasi multiset dan teknik <strong>bintang dan sekat</strong>.</li>
        <li>Menghitung dengan <strong>inklusi–eksklusi</strong>, termasuk derangement.</li>
        <li>Menghitung jalur grid yang menghindari rintangan tanpa tabel raksasa.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'rumus-rumus dasar', 'desc' => 'Permutasi, kombinasi, multiset, dan bintang & sekat.'])

<section class="lesson-section" id="dasar" data-toc="Rumus Dasar">
    <h2>Menghitung Tanpa Mendaftar</h2>
    <div class="prose">
        <p>Kombinatorika adalah seni menghitung banyaknya kemungkinan tanpa menuliskannya satu per satu. Hampir semua rumus berasal dari dua aturan (lihat juga materi DP Menghitung Cara): <strong>aturan perkalian</strong> untuk pilihan bertahap, dan <strong>aturan penjumlahan</strong> untuk kasus yang saling lepas.</p>
    </div>
    <table class="cx-table">
        <tr><th>Situasi</th><th>Rumus</th><th>Contoh</th></tr>
        <tr><td>Menyusun n benda berbeda</td><td><code>n!</code></td><td>5 buku di rak: 120</td></tr>
        <tr><td>Memilih k dari n, urutan penting</td><td><code>P(n, k) = n! / (n − k)!</code></td><td>Juara 1–3 dari 10: 720</td></tr>
        <tr><td>Memilih k dari n, urutan tidak penting</td><td><code>C(n, k) = n! / (k! (n − k)!)</code></td><td>Tim 3 dari 10: 120</td></tr>
        <tr><td>Menyusun benda dengan duplikat</td><td><code>n! / (c₁! c₂! …)</code></td><td>Huruf MATEMATIKA: 151 200</td></tr>
        <tr><td>n benda identik ke k kotak</td><td><code>C(n + k − 1, k − 1)</code></td><td>5 permen ke 3 anak: 21</td></tr>
    </table>
    @include('lessons.code', ['cpp' => $kmMulti, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="bintang" data-toc="Bintang dan Sekat">
    <h2>Bintang dan Sekat</h2>
    <div class="prose">
        <p>Membagi 5 permen yang sama persis ke 3 anak sama dengan menyusun 5 bintang dan 2 sekat dalam satu baris: <code>★★|★|★★</code> artinya 2, 1, 2. Setiap susunan berpadanan tepat dengan satu cara membagi. Ada 7 posisi, dan kita memilih 2 di antaranya untuk sekat: C(7, 2) = 21.</p>
    </div>
    @include('lessons.code', ['cpp' => $kmStars, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Batas bawah per anak (minimal L<sub>i</sub>) diatasi dengan memberikan L<sub>i</sub> lebih dulu. Batas atas lebih sulit: perlu inklusi–eksklusi (Level 3).</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Jalur Grid</h2>
    <div class="prose"><p>Setiap jalur dari kiri atas ke kanan bawah adalah barisan langkah "bawah" dan "kanan"; memilih posisi langkah bawah = kombinasi. Bagian kedua menunjukkan cara menghitung jalur yang menghindari satu lubang dengan pengurangan.</p></div>
    <div data-viz="gridpath"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'C(n, k) modulo prima', 'desc' => 'Faktorial dan invers faktorial dalam O(N).'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'C(n, k) mod 10^9 + 7',
        'steps' => $kmSteps,
        'sample' => ['input' => "4\n5 2\n10 3\n1000000 500000\n3 5\n", 'output' => "10\n120\n996692777\n0\n"],
        'py' => $kmPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: modulo 7</h2>
    <div class="prose"><p>Dengan p kecil kita bisa memeriksa trik invers mundur. Faktorial modulo 7 untuk 0..6:</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>0</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th></tr>
            <tr><td>fakt[i] mod 7</td><td>1</td><td>1</td><td>2</td><td>6</td><td>3</td><td>1</td><td>6</td></tr>
            <tr><td>invF[i] mod 7</td><td>1</td><td>1</td><td>4</td><td>6</td><td>5</td><td>1</td><td>6</td></tr>
        </table>
    </div>
    <div class="prose"><p>invF[6] = 6<sup>5</sup> mod 7 = 6 (karena 6 · 6 = 36 ≡ 1). Lalu mundur: invF[5] = invF[6] · 6 = 36 ≡ 1, invF[4] = 1 · 5 = 5, invF[3] = 5 · 4 = 20 ≡ 6, invF[2] = 6 · 3 = 18 ≡ 4. Cek: fakt[4] · invF[4] = 3 · 5 = 15 ≡ 1. Maka C(5, 2) = 1 · 4 · 6 mod 7 = 24 mod 7 = 3, dan memang 10 mod 7 = 3.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Persiapan</th><th>Per C(n, k)</th><th>Batas wajar</th></tr>
        <tr><td>Segitiga Pascal</td><td><code>O(N²)</code></td><td><code>O(1)</code></td><td>N ≤ 5 000, modulus apa saja</td></tr>
        <tr><td>Faktorial + invers</td><td><code>O(N)</code></td><td><code>O(1)</code></td><td>N ≤ 10<sup>7</sup>, modulus prima &gt; N</td></tr>
        <tr><td>Hitung langsung tiap pertanyaan</td><td>–</td><td><code>O(k log p)</code></td><td>sedikit pertanyaan, k kecil</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Modulus harus prima dan lebih besar dari n.</strong> Jika n ≥ p, n! ≡ 0 dan inversnya tidak ada. Untuk kasus itu pakai teorema Lucas; untuk modulus bukan prima (misalnya 10<sup>9</sup>) pakai Pascal.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Ukuran array.</strong> Soal sering membutuhkan C(n + k − 1, …) atau C(H + W − 2, …), jadi siapkan faktorial sampai nilai terbesar yang mungkin muncul, bukan hanya sampai n.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pengurangan modulo.</strong> Inklusi–eksklusi mengurangkan; tambahkan MOD sebelum <code>% MOD</code> agar tidak negatif.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'inklusi–eksklusi', 'desc' => 'Menambah dan mengurangi agar setiap objek terhitung tepat sekali.'])

<section class="lesson-section" id="inklusi" data-toc="Inklusi–Eksklusi">
    <h2>Prinsip Inklusi–Eksklusi</h2>
    <div class="prose">
        <p>Untuk menghitung objek yang memenuhi <em>paling sedikit satu</em> sifat, jumlahkan banyaknya per sifat, kurangi yang memenuhi dua sifat (terhitung dua kali), tambahkan lagi yang memenuhi tiga sifat, dan seterusnya dengan tanda bergantian. Dengan k sifat, telusuri semua 2<sup>k</sup> himpunan bagian memakai bitmask.</p>
    </div>
    @include('lessons.code', ['cpp' => $kmIncl, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="derangement" data-toc="Derangement">
    <h2>Derangement: Tukar Kado</h2>
    <div class="prose">
        <p>n teman bertukar kado secara acak. Ada berapa cara sehingga <strong>tidak seorang pun</strong> mendapat kadonya sendiri? Inklusi–eksklusi atas sifat "orang ke-i mendapat kadonya sendiri" memberi rumus derangement, dan ada rekurens yang lebih praktis.</p>
    </div>
    @include('lessons.code', ['cpp' => $kmDerange, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="rintangan" data-toc="Jalur dengan Rintangan">
    <h2>Jalur Grid dengan Banyak Rintangan</h2>
    <div class="prose">
        <p>Jika grid berukuran 10<sup>5</sup> × 10<sup>5</sup>, tabel DP mustahil. Tetapi rintangannya sedikit. Hitung jalur ke setiap rintangan yang tidak melewati rintangan lain, dengan mengurangkan jalur yang lebih dulu menginjak rintangan lain sebagai rintangan <em>pertama</em>. Setiap jalur buruk terhitung tepat sekali menurut rintangan pertamanya, jadi tidak ada yang dikurangi dua kali.</p>
    </div>
    @include('lessons.code', ['cpp' => $kmObst, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Banyak C(n, k)</h4><p>Faktorial + invers faktorial, O(1) per pertanyaan.</p></div>
        <div class="pattern"><h4>Susunan dengan huruf kembar</h4><p>n! dibagi faktorial setiap frekuensi.</p></div>
        <div class="pattern"><h4>Benda identik ke kotak</h4><p>Bintang dan sekat; batas bawah dikurangi dulu.</p></div>
        <div class="pattern"><h4>"Paling sedikit satu"</h4><p>Komplemen: total − "tidak satu pun", atau inklusi–eksklusi.</p></div>
        <div class="pattern"><h4>Tidak di tempat semula</h4><p>Derangement; tepat k di tempat semula = C(n, k) · D(n − k).</p></div>
        <div class="pattern"><h4>Grid besar, rintangan sedikit</h4><p>DP atas rintangan terurut, O(K²).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="6 bintang dan 3 sekat: C(9, 3) = 84.">
        <p class="quiz-q">Banyak cara membagi 6 kelereng identik ke 4 kotak (boleh kosong)?</p>
        <div class="quiz-options">
            <button class="quiz-option">C(6, 4) = 15</button>
            <button class="quiz-option">4<sup>6</sup> = 4096</button>
            <button class="quiz-option">C(9, 3) = 84</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Huruf A muncul 3 kali, N 2 kali, S sekali, dan total 6 huruf: 6! / (3! · 2!) = 60.">
        <p class="quiz-q">Banyak susunan berbeda huruf kata ANANAS?</p>
        <div class="quiz-options">
            <button class="quiz-option">60</button>
            <button class="quiz-option">120</button>
            <button class="quiz-option">720</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Jika n ≥ p maka n! mengandung faktor p sehingga n! ≡ 0 (mod p) dan tidak punya invers.">
        <p class="quiz-q">Mengapa trik faktorial + invers gagal jika n ≥ p?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena terlalu lambat</button>
            <button class="quiz-option">Karena n! ≡ 0 (mod p) sehingga inversnya tidak ada</button>
            <button class="quiz-option">Karena C(n, k) selalu 0</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
