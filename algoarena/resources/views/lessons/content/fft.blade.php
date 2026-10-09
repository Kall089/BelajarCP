@php
    $ftSteps = [
        ['Konstanta dan pangkat', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 998244353;      // 119 · 2^23 + 1: punya akar ke-2^23 dari 1
const long long G = 3;                // akar primitif modulo MOD

long long pw(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Perkalian Polinom":</strong> diberikan A(x) berderajat n dan B(x) berderajat m (n, m ≤ 2·10<sup>5</sup>). Cetak koefisien A(x)·B(x) modulo 998244353. Cara sekolah O(n·m) = 4·10<sup>10</sup>: terlalu lambat.</p>
<p>NTT (Number Theoretic Transform) adalah FFT di dunia modulo: alih-alih bilangan kompleks e<sup>2πi/n</sup>, kita memakai akar ke-n dari 1 modulo prima. 998244353 − 1 habis dibagi 2<sup>23</sup>, jadi akar ke-2<sup>k</sup> tersedia untuk ukuran sampai 2<sup>23</sup>.</p>
TXT],
        ['NTT iteratif', <<<'CPP'

void ntt(vector<long long>& a, bool balik) {
    int n = a.size();
    for (int i = 1, j = 0; i < n; i++) {           // urutkan ulang dengan bit-reversal
        int bit = n >> 1;
        for (; j & bit; bit >>= 1) j ^= bit;
        j ^= bit;
        if (i < j) swap(a[i], a[j]);
    }
    for (int len = 2; len <= n; len <<= 1) {       // gabungkan blok berukuran len/2 menjadi len
        long long w = pw(G, (MOD - 1) / len);       // akar ke-len dari 1
        if (balik) w = pw(w, MOD - 2);
        for (int i = 0; i < n; i += len) {
            long long t = 1;
            for (int k = 0; k < len / 2; k++) {
                long long u = a[i + k], v = a[i + k + len / 2] * t % MOD;
                a[i + k] = (u + v) % MOD;                  // P(w^k)       = G + w^k H
                a[i + k + len / 2] = (u - v + MOD) % MOD;  // P(w^(k+n/2)) = G - w^k H
                t = t * w % MOD;
            }
        }
    }
    if (balik) {                                    // invers: bagi dengan n
        long long inv = pw(n, MOD - 2);
        for (auto& x : a) x = x * inv % MOD;
    }
}
CPP, <<<'TXT'
<p>Ini versi tanpa rekursi dari "bagi genap–ganjil". Bit-reversal menaruh koefisien di posisi akhir rekursinya, lalu blok-blok digabung dari ukuran 2, 4, 8, … dengan rumus <em>butterfly</em>: dari nilai G dan H di setengah titik, dapat nilai P di semua titik. Setiap tingkat O(n), ada log n tingkat.</p>
<p>NTT balik memakai akar invers dan diakhiri pembagian dengan n.</p>
TXT],
        ['Evaluasi, kalikan, interpolasi', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n, m;
    cin >> n >> m;
    vector<long long> a(n + 1), b(m + 1);
    for (auto& x : a) cin >> x;
    for (auto& x : b) cin >> x;

    int sz = 1;
    while (sz < n + m + 1) sz <<= 1;               // pangkat dua ≥ banyak koefisien hasil
    a.resize(sz);
    b.resize(sz);
    ntt(a, false);
    ntt(b, false);
    for (int i = 0; i < sz; i++) a[i] = a[i] * b[i] % MOD;   // kalikan titik demi titik
    ntt(a, true);

    for (int i = 0; i <= n + m; i++) cout << a[i] << (i == n + m ? '\n' : ' ');
    return 0;
}
CPP, <<<'TXT'
<p>Polinom berderajat n + m ditentukan oleh nilainya di n + m + 1 titik. Jadi: evaluasi A dan B di sz titik (NTT), kalikan nilainya titik demi titik (O(sz)), lalu kembalikan ke koefisien (NTT balik). Total O(sz log sz).</p>
TXT],
    ];

    $ftPy = <<<'PY'
MOD, G = 998244353, 3

def ntt(a, balik):
    n = len(a)
    j = 0
    for i in range(1, n):
        bit = n >> 1
        while j & bit:
            j ^= bit
            bit >>= 1
        j ^= bit
        if i < j:
            a[i], a[j] = a[j], a[i]
    length = 2
    while length <= n:
        w = pow(G, (MOD - 1) // length, MOD)
        if balik:
            w = pow(w, MOD - 2, MOD)
        half = length // 2
        ws = [1] * half
        for k in range(1, half):
            ws[k] = ws[k - 1] * w % MOD
        for i in range(0, n, length):
            for k in range(half):
                u = a[i + k]
                v = a[i + k + half] * ws[k] % MOD
                a[i + k] = (u + v) % MOD
                a[i + k + half] = (u - v) % MOD
        length <<= 1
    if balik:
        inv = pow(n, MOD - 2, MOD)
        for i in range(n):
            a[i] = a[i] * inv % MOD

n, m = map(int, input().split())
a = list(map(int, input().split()))
b = list(map(int, input().split()))
sz = 1
while sz < n + m + 1:
    sz <<= 1
a += [0] * (sz - len(a))
b += [0] * (sz - len(b))
ntt(a, False)
ntt(b, False)
c = [x * y % MOD for x, y in zip(a, b)]
ntt(c, True)
print(" ".join(map(str, c[:n + m + 1])))
PY;

    $ftConv = <<<'CPP'
// Konvolusi = perkalian polinom. Contoh: banyak cara memilih satu elemen dari A dan satu dari B
// dengan jumlah tepat s, untuk SEMUA s sekaligus:
vector<long long> fa(MAKS + 1, 0), fb(MAKS + 1, 0);
for (int x : A) fa[x]++;                   // fa = polinom frekuensi
for (int y : B) fb[y]++;
vector<long long> c = kaliPolinom(fa, fb); // c[s] = Σ fa[i]·fb[s-i]
CPP;

    $ftComplex = <<<'CPP'
// FFT dengan bilangan kompleks (tanpa modulo) memakai akar exp(2πi/n).
// Hati-hati presisi: hasil koefisien harus dibulatkan (llround) dan aman hanya jika
// nilai koefisien hasil < ±10^15. Untuk jawaban modulo prima sembarang (misalnya 10^9+7)
// pakai tiga NTT dengan modulus berbeda + CRT, atau FFT "pecah dua" (split).
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memandang polinom sebagai <strong>nilai di titik-titik</strong> dan memahami mengapa perkalian jadi mudah di sana.</li>
        <li>Menjelaskan <strong>akar kesatuan</strong> dan trik bagi genap–ganjil yang membuat evaluasi O(n log n).</li>
        <li>Menulis <strong>NTT iteratif</strong> modulo 998244353 untuk mengalikan polinom.</li>
        <li>Mengenali soal <strong>konvolusi</strong>: menghitung semua jumlah pasangan, perkalian bilangan besar, dan pencocokan pola.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'dua cara menyimpan polinom', 'desc' => 'Koefisien mudah dibaca, nilai mudah dikalikan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Dua Wajah Polinom">
    <h2>Intuisi: Dua Wajah Sebuah Polinom</h2>
    <div class="prose">
        <p>Polinom berderajat &lt; n bisa disimpan sebagai n <strong>koefisien</strong>, atau sebagai <strong>nilainya di n titik</strong> berbeda (n titik menentukan polinomnya secara tunggal). Mengalikan dua polinom dalam bentuk koefisien butuh O(n²). Dalam bentuk nilai, cukup kalikan nilai di setiap titik: O(n)!</p>
        <p>Masalahnya, mengubah koefisien menjadi nilai di n titik sembarang butuh O(n²). Trik FFT: pilih titik-titik istimewa, yaitu <strong>akar ke-n dari 1</strong>. Karena kuadrat akar-akar itu adalah akar ke-(n/2) dari 1, masalah berukuran n terbelah menjadi dua masalah berukuran n/2:</p>
    </div>
    <div class="recurrence"><small>Bagi genap–ganjil</small>P(x) = Genap(x²) + x · Ganjil(x²)
P(ωᵏ)       = Genap(ω²ᵏ) + ωᵏ · Ganjil(ω²ᵏ)
P(ωᵏ⁺ⁿ/²)   = Genap(ω²ᵏ) − ωᵏ · Ganjil(ω²ᵏ)      (karena ωⁿ/² = −1)
T(n) = 2T(n/2) + O(n) = O(n log n)</div>
    <div class="term-grid">
        <div class="term"><b>Konvolusi</b><span>c[s] = Σ a[i]·b[s − i]: koefisien hasil kali polinom.</span></div>
        <div class="term"><b>Akar kesatuan</b><span>ω dengan ω<sup>n</sup> = 1 dan ω<sup>k</sup> ≠ 1 untuk 0 &lt; k &lt; n.</span></div>
        <div class="term"><b>NTT</b><span>FFT modulo prima p = c·2<sup>k</sup> + 1; tanpa galat pembulatan.</span></div>
        <div class="term"><b>998244353</b><span>= 119·2<sup>23</sup> + 1, akar primitif 3.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Pohon Rekursi NTT (mod 17)</h2>
    <div class="prose"><p>Modulo 17 dipakai agar angkanya kecil (3 adalah akar primitif, 3<sup>16</sup> ≡ 1). Turun: pisahkan koefisien berindeks genap dan ganjil. Naik: gabungkan nilai-nilai dengan rumus butterfly.</p></div>
    <div data-viz="fft"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'NTT di C++', 'desc' => 'Perkalian polinom O(n log n) ditulis lengkap.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Perkalian Polinom</h2>
    @include('lessons.walkthrough', [
        'title' => 'NTT modulo 998244353',
        'steps' => $ftSteps,
        'sample' => ['input' => "2 1\n1 2 3\n4 5\n", 'output' => "4 13 22 15\n"],
        'py' => $ftPy,
    ])
    <div class="prose"><p>Contoh: (1 + 2x + 3x²)(4 + 5x) = 4 + 13x + 22x² + 15x³.</p></div>
</section>

<section class="lesson-section" id="aplikasi" data-toc="Aplikasi & Jebakan">
    <h2>Aplikasi dan Jebakan</h2>
    @include('lessons.code', ['cpp' => $ftConv, 'js' => null, 'py' => null])
    <table class="cx-table">
        <tr><th>Soal</th><th>Polinom</th></tr>
        <tr><td>Banyak pasangan (a, b) dengan a + b = s, untuk semua s</td><td>frekuensi A × frekuensi B</td></tr>
        <tr><td>Perkalian bilangan sangat besar</td><td>digit sebagai koefisien, lalu bawa simpanan</td></tr>
        <tr><td>Pencocokan pola dengan wildcard</td><td>Σ (p − t)²·p·t sebagai beberapa konvolusi</td></tr>
        <tr><td>Banyak cara jumlah k dadu</td><td>pangkat polinom dadu</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Ukuran harus pangkat dua dan cukup besar.</strong> Hasil punya n + m + 1 koefisien; jika sz lebih kecil, koefisien "membungkus" (konvolusi siklik) dan hasilnya salah.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Modulus lain.</strong> NTT hanya bekerja untuk prima berbentuk c·2<sup>k</sup> + 1 dengan 2<sup>k</sup> ≥ sz. Untuk 10<sup>9</sup> + 7 perlu trik tambahan.</p>
    </div>
    @include('lessons.code', ['cpp' => $ftComplex, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 3, 'title' => 'mengapa invers membagi n', 'desc' => 'Ortogonalitas akar kesatuan.'])

<section class="lesson-section" id="invers" data-toc="Bukti Invers">
    <h2>Mengapa NTT Balik Bekerja</h2>
    <div class="proof">
        <p>NTT adalah perkalian dengan matriks V[j][k] = ω<sup>jk</sup>. Klaim: matriks dengan entri ω<sup>−jk</sup>, dibagi n, adalah inversnya. Hasil kali baris j dan kolom l adalah Σ<sub>k</sub> ω<sup>(j−l)k</sup>. Jika j = l, setiap suku 1, jumlahnya n. Jika j ≠ l, ini deret geometri dengan rasio r = ω<sup>j−l</sup> ≠ 1 dan r<sup>n</sup> = 1, sehingga jumlahnya (r<sup>n</sup> − 1)/(r − 1) = 0. Jadi hasilnya n·I, dan membaginya dengan n memberi identitas.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>"Untuk semua s, hitung banyak pasangan…"</h4><p>Konvolusi frekuensi.</p></div>
        <div class="pattern"><h4>Modulo 998244353</h4><p>Hampir pasti tanda NTT.</p></div>
        <div class="pattern"><h4>Pangkat polinom</h4><p>Perpangkatan cepat dengan NTT di setiap perkalian.</p></div>
        <div class="pattern"><h4>Ukuran 10<sup>6</sup></h4><p>sz = 2<sup>21</sup>: masih aman, ±0,2 detik di C++.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Hasil berderajat 3 + 4 = 7, jadi 8 koefisien. Pangkat dua terkecil ≥ 8 adalah 8.">
        <p class="quiz-q">A berderajat 3 dan B berderajat 4. Ukuran NTT minimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">4</button>
            <button class="quiz-option">16</button>
            <button class="quiz-option">8</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Kuadrat akar ke-n dari 1 adalah akar ke-(n/2) dari 1, jadi Genap dan Ganjil cukup dievaluasi di n/2 titik.">
        <p class="quiz-q">Mengapa trik genap–ganjil hanya butuh setengah titik untuk setiap bagian?</p>
        <div class="quiz-options">
            <button class="quiz-option">Kuadrat dari akar ke-n dari 1 hanya ada n/2 nilai berbeda</button>
            <button class="quiz-option">Koefisien ganjil selalu nol</button>
            <button class="quiz-option">Karena modulo 998244353</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Di bentuk nilai, perkalian polinom hanya perkalian titik demi titik: O(n).">
        <p class="quiz-q">Setelah kedua polinom diubah ke bentuk nilai, mengalikannya butuh…</p>
        <div class="quiz-options">
            <button class="quiz-option">O(n²)</button>
            <button class="quiz-option">O(n)</button>
            <button class="quiz-option">O(n log n)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
