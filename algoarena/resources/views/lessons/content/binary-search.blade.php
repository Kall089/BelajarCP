@php
    $bsSteps = [
        ['Membaca data', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n;
long long M;
vector<long long> h;
CPP, <<<'TXT'
<p>Seorang penebang punya gergaji yang bisa diatur ketinggiannya. Ada N pohon dengan tinggi <code>h[i]</code>. Jika gergaji diatur setinggi H, setiap pohon yang lebih tinggi dari H terpotong, dan penebang mendapat bagian <code>h[i] − H</code>.</p>
<p>Ia butuh <strong>paling sedikit M</strong> meter kayu dan ingin memotong sesedikit mungkin, jadi H harus <strong>setinggi mungkin</strong>. Berapa H terbesar?</p>
TXT],
        ['Fungsi pemeriksa', <<<'CPP'

long long kayu(long long H) {
    long long total = 0;
    for (long long x : h)
        if (x > H) total += x - H;
    return total;
}
CPP, <<<'TXT'
<p>Diberi tebakan H, mudah menghitung berapa kayu yang didapat: O(N). Pertanyaannya hanya H mana yang dicoba.</p>
<p>Kuncinya: <code>kayu(H)</code> <strong>monoton turun</strong>. Semakin tinggi gergaji, semakin sedikit kayu. Jadi ada batas: semua H di bawah batas itu cukup (≥ M), dan semua di atasnya tidak cukup.</p>
<pre>H     :  10  12  14  15  16  17
kayu  :  22  16  10   7   5   3     (M = 7)
cukup?:  ya  ya  ya  ya tidak tidak</pre>
TXT],
        ['Membaca input', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> M;
    h.resize(n);
    for (auto& x : h) cin >> x;
CPP, <<<'TXT'
<p>Tinggi pohon sampai 10<sup>9</sup> dan N sampai 10<sup>6</sup>, sehingga total kayu bisa mencapai 10<sup>15</sup>: gunakan <code>long long</code>.</p>
TXT],
        ['Binary search pada jawaban', <<<'CPP'

    long long lo = 0, hi = *max_element(h.begin(), h.end());
    while (lo < hi) {
        long long mid = lo + (hi - lo + 1) / 2;
        if (kayu(mid) >= M) lo = mid;
        else hi = mid - 1;
    }
CPP, <<<'TXT'
<p>Jawaban pasti di [0, tinggi maksimum]. Invarian: <code>lo</code> selalu "cukup", dan jawaban selalu di [lo, hi].</p>
<ul>
    <li>Jika <code>mid</code> cukup, jawaban paling sedikit mid: <code>lo = mid</code>.</li>
    <li>Jika tidak, jawaban pasti di bawah mid: <code>hi = mid − 1</code>.</li>
</ul>
<p><code>mid</code> dibulatkan <strong>ke atas</strong> (<code>+1</code>). Tanpa itu, saat hi = lo + 1, mid = lo dan <code>lo = mid</code> tidak mengubah apa pun: loop tak berujung.</p>
<pre>lo=0  hi=20 → mid=10 kayu 22 cukup  → lo=10
lo=10 hi=20 → mid=15 kayu 7  cukup  → lo=15
lo=15 hi=20 → mid=18 kayu 2  kurang → hi=17
lo=15 hi=17 → mid=16 kayu 5  kurang → hi=15   selesai: 15</pre>
TXT],
        ['Jawaban', <<<'CPP'

    cout << lo << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Total kerja: O(N log(max h)) ≈ 10<sup>6</sup> × 30. Mencoba setiap H satu per satu akan butuh 10<sup>9</sup> × 10<sup>6</sup> langkah.</p>
TXT],
    ];

    $bsPy = <<<'PY'
import sys
input = sys.stdin.readline

n, M = map(int, input().split())
h = list(map(int, input().split()))

def kayu(H):
    return sum(x - H for x in h if x > H)

lo, hi = 0, max(h)
while lo < hi:
    mid = (lo + hi + 1) // 2
    if kayu(mid) >= M:
        lo = mid
    else:
        hi = mid - 1
print(lo)
PY;

    $bsLower = <<<'CPP'
vector<int> a = {2, 4, 4, 4, 7, 9};      // HARUS sudah urut
// lower_bound: posisi pertama yang >= x
int p1 = lower_bound(a.begin(), a.end(), 4) - a.begin();   // 1
// upper_bound: posisi pertama yang > x
int p2 = upper_bound(a.begin(), a.end(), 4) - a.begin();   // 4
int banyak4 = p2 - p1;                                      // 3 kemunculan
bool ada5 = binary_search(a.begin(), a.end(), 5);           // false
CPP;

    $bsManual = <<<'CPP'
// Pola umum: cari x TERKECIL yang memenuhi cek(x), jika cek monoton (salah... salah benar... benar)
long long lo = BATAS_BAWAH, hi = BATAS_ATAS;     // pastikan cek(hi) benar
while (lo < hi) {
    long long mid = lo + (hi - lo) / 2;          // bulatkan ke bawah
    if (cek(mid)) hi = mid;                      // mid mungkin jawabannya
    else lo = mid + 1;                           // jawaban di kanan mid
}
// lo = jawaban

// Cari x TERBESAR yang memenuhi (benar... benar salah... salah): bulatkan ke ATAS
//     mid = lo + (hi - lo + 1) / 2;  if (cek(mid)) lo = mid; else hi = mid - 1;
CPP;

    $bsReal = <<<'CPP'
// Binary search pada bilangan real: ulangi sejumlah tetap, bukan "while (lo < hi)".
double lo = 0, hi = 1e9;
for (int it = 0; it < 100; it++) {               // 100 kali → presisi jauh di bawah 1e-9
    double mid = (lo + hi) / 2;
    if (cek(mid)) hi = mid;
    else lo = mid;
}
CPP;

    $bsTwo = <<<'CPP'
// Pasangan dengan jumlah tepat X pada array URUT: O(n)
int l = 0, r = n - 1;
while (l < r) {
    long long s = a[l] + a[r];
    if (s == X) break;        // ketemu
    if (s < X) l++;           // butuh lebih besar
    else r--;                 // butuh lebih kecil
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai <code>lower_bound</code>, <code>upper_bound</code>, dan menulis binary search sendiri tanpa loop tak berujung.</li>
        <li>Mengenali <strong>binary search pada jawaban</strong>: mencari nilai optimal ketika "cukup/tidak cukup" bersifat monoton.</li>
        <li>Menyelesaikan soal pasangan dan subarray dengan <strong>two pointers</strong> dan <strong>sliding window</strong> dalam O(N).</li>
        <li>Membuktikan kebenaran dengan invarian.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'membuang setengah setiap langkah', 'desc' => 'Binary search pada array urut dan fungsi bawaan C++.'])

<section class="lesson-section" id="ide" data-toc="Ide Binary Search">
    <h2>Ide Binary Search</h2>
    <div class="prose">
        <p>Tebak angka 1–100 dengan petunjuk "terlalu besar / terlalu kecil". Strategi terbaik: tebak tengahnya, lalu buang setengah yang pasti salah. Setelah 7 tebakan, pasti ketemu, karena 2<sup>7</sup> = 128 ≥ 100.</p>
        <p>Binary search bekerja pada apa pun yang <strong>monoton</strong>: array urut, atau fungsi yang berubah dari "salah" menjadi "benar" tepat sekali. Setiap langkah membuang setengah kemungkinan, jadi totalnya O(log N).</p>
    </div>
    <div data-viz="search-race"></div>
</section>

<section class="lesson-section" id="stl" data-toc="lower_bound & upper_bound">
    <h2>Fungsi Bawaan: lower_bound dan upper_bound</h2>
    <div class="prose"><p>Untuk array urut, jangan menulis binary search sendiri; C++ sudah menyediakannya:</p></div>
    @include('lessons.code', ['cpp' => $bsLower, 'js' => null, 'py' => null])
    <div class="term-grid">
        <div class="term"><b>lower_bound(x)</b><span>Posisi pertama yang nilainya <strong>≥ x</strong>. Banyak elemen &lt; x sama dengan posisi ini.</span></div>
        <div class="term"><b>upper_bound(x)</b><span>Posisi pertama yang nilainya <strong>&gt; x</strong>. Banyak elemen ≤ x sama dengan posisi ini.</span></div>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Keduanya mengembalikan <em>iterator</em>. Kurangi dengan <code>a.begin()</code> untuk mendapat indeks. Jika semua elemen lebih kecil, hasilnya <code>a.end()</code> (indeks n): selalu cek sebelum mengakses.</p>
    </div>
</section>

<section class="lesson-section" id="jawaban" data-toc="Binary Search pada Jawaban">
    <h2>Binary Search pada Jawaban</h2>
    <div class="prose">
        <p>Banyak soal optimasi berbentuk "cari nilai <strong>terbesar/terkecil</strong> X sehingga syarat terpenuhi". Jika memeriksa satu X mudah, dan sifat "terpenuhi" monoton terhadap X, kita bisa binary search pada X tanpa tahu rumusnya.</p>
    </div>
    @include('lessons.code', ['cpp' => $bsManual, 'js' => null, 'py' => null])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Tanda-tandanya: kata "maksimum dari minimum" atau "minimum dari maksimum", batas jawaban sangat besar (10<sup>9</sup>–10<sup>18</sup>), dan untuk X tertentu mudah dicek "bisa atau tidak" secara serakah.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'binary search pada jawaban di C++', 'desc' => 'Soal gergaji kayu, lengkap dengan invariannya.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Gergaji Kayu</h2>
    @include('lessons.walkthrough', [
        'title' => 'Binary search pada tinggi gergaji',
        'steps' => $bsSteps,
        'sample' => ['input' => "4 7\n20 15 10 17\n", 'output' => "15\n"],
        'py' => $bsPy,
    ])
</section>

<section class="lesson-section" id="two" data-toc="Two Pointers">
    <h2>Two Pointers dan Sliding Window</h2>
    <div class="prose">
        <p>Two pointers memakai dua indeks yang hanya bergerak <strong>satu arah</strong>. Karena setiap penunjuk bergerak paling banyak N kali, totalnya O(N), walaupun kodenya berisi loop di dalam loop.</p>
    </div>
    @include('lessons.code', ['cpp' => $bsTwo, 'js' => null, 'py' => null])
    <div class="prose">
        <p><strong>Sliding window</strong> menjaga sebuah jendela [l, r]: perlebar ke kanan satu per satu, dan persempit dari kiri selama jendela melanggar syarat. Cocok untuk "subarray terpanjang/terpendek yang memenuhi …" dengan elemen tak negatif.</p>
    </div>
    <div data-viz="window"></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Teknik</th><th>Waktu</th></tr>
        <tr><td>Binary search pada array</td><td><code>O(log N)</code></td></tr>
        <tr><td>Binary search pada jawaban</td><td><code>O(log(rentang) · biaya cek)</code></td></tr>
        <tr><td>Two pointers / sliding window</td><td><code>O(N)</code> (setelah sort: <code>O(N log N)</code>)</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Loop tak berujung.</strong> Pasangan <code>lo = mid</code> harus memakai mid yang dibulatkan ke atas; pasangan <code>hi = mid</code> memakai mid yang dibulatkan ke bawah.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Meluap.</strong> <code>(lo + hi) / 2</code> bisa meluap jika lo dan hi besar. Tulis <code>lo + (hi − lo) / 2</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Sliding window dengan bilangan negatif</strong> tidak bekerja: menambah elemen bisa membuat jumlah mengecil, sehingga syarat tidak lagi monoton.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'invarian dan bilangan real', 'desc' => 'Membuktikan binary search, dan versi untuk bilangan desimal.'])

<section class="lesson-section" id="bukti" data-toc="Invarian">
    <h2>Membuktikan dengan Invarian</h2>
    <div class="proof">
        <p>Untuk soal gergaji, invariannya: <em>kayu(lo) ≥ M</em>, dan jawaban berada di [lo, hi]. Awalnya benar (H = 0 memotong semua kayu, dan soal menjamin totalnya cukup). Setiap iterasi mempertahankannya: jika mid cukup kita geser lo ke mid; jika tidak, semua H ≥ mid juga tidak cukup (monoton), jadi aman membuang [mid, hi].</p>
        <p>Rentang [lo, hi] selalu mengecil (karena mid dibulatkan ke atas, mid &gt; lo), jadi loop berhenti dengan lo = hi, yaitu jawabannya.</p>
    </div>
</section>

<section class="lesson-section" id="real" data-toc="Bilangan Real">
    <h2>Binary Search pada Bilangan Real</h2>
    <div class="prose"><p>Jika jawabannya bilangan desimal (misalnya kecepatan atau panjang), jangan memakai <code>while (lo &lt; hi)</code> karena bisa tidak pernah berhenti akibat pembulatan. Ulangi sejumlah tetap; setiap iterasi membagi dua rentang.</p></div>
    @include('lessons.code', ['cpp' => $bsReal, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Potong kayu / kabel</h4><p>Maksimalkan panjang potongan agar dapat ≥ K potong.</p></div>
        <div class="pattern"><h4>Bagi array</h4><p>Bagi menjadi K bagian berurutan, minimalkan jumlah terbesar.</p></div>
        <div class="pattern"><h4>Jarak sapi</h4><p>Taruh K sapi di kandang, maksimalkan jarak terdekat.</p></div>
        <div class="pattern"><h4>Pasangan berjumlah X</h4><p>Sort + two pointers.</p></div>
        <div class="pattern"><h4>Subarray terpanjang</h4><p>Sliding window dengan syarat monoton.</p></div>
        <div class="pattern"><h4>Akar kuadrat</h4><p>x terbesar dengan x · x ≤ N tanpa sqrt() yang tidak presisi.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="lower_bound mencari posisi pertama yang ≥ 5. Di [1, 3, 5, 5, 8], itu indeks 2.">
        <p class="quiz-q">a = [1, 3, 5, 5, 8]. Berapa <code>lower_bound(5) − a.begin()</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
            <button class="quiz-option">4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Jika hi = lo + 1 dan mid dibulatkan ke bawah, mid = lo. Saat cek(mid) benar, lo = mid tidak berubah: loop tak berujung.">
        <p class="quiz-q">Mengapa mid dibulatkan ke atas pada pola <code>if (cek(mid)) lo = mid;</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar rentang selalu mengecil dan loop berhenti</button>
            <button class="quiz-option">Agar lebih cepat</button>
            <button class="quiz-option">Agar tidak meluap</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Penunjuk r maju n kali dan l maju paling banyak n kali sepanjang program, jadi total O(n).">
        <p class="quiz-q">Kompleksitas sliding window dengan loop <code>while</code> di dalam <code>for</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(n²)</button>
            <button class="quiz-option">O(n log n)</button>
            <button class="quiz-option">O(n)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
