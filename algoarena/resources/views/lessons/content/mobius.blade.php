@php
    $mbSteps = [
        ['Saringan linear untuk μ', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int N;
    cin >> N;
    vector<int> mu(N + 1, 0), primes;
    vector<char> komposit(N + 1, 0);
    mu[1] = 1;
    for (int i = 2; i <= N; i++) {
        if (!komposit[i]) {                  // i prima
            primes.push_back(i);
            mu[i] = -1;
        }
        for (int p : primes) {
            if ((long long)i * p > N) break;
            komposit[i * p] = 1;
            if (i % p == 0) {                // p² membagi i·p
                mu[i * p] = 0;
                break;
            }
            mu[i * p] = -mu[i];              // satu prima baru
        }
    }
CPP, <<<'TXT'
<p><strong>Soal contoh "Pasangan Koprima":</strong> berapa banyak pasangan berurutan (i, j) dengan 1 ≤ i, j ≤ N dan gcd(i, j) = 1? N sampai 10<sup>7</sup>, jadi mencoba semua pasangan mustahil.</p>
<p>Fungsi Möbius μ bersifat multiplikatif, sehingga mudah dihitung dengan saringan linear: setiap bilangan i·p dibangun dari faktor prima terkecilnya p. Jika p sudah membagi i, i·p memuat p² sehingga μ = 0; jika tidak, banyak faktor primanya bertambah satu dan tandanya berbalik.</p>
TXT],
        ['Jumlahkan dengan inversi Möbius', <<<'CPP'

    long long hasil = 0;
    for (int d = 1; d <= N; d++)
        hasil += (long long)mu[d] * (N / d) * (N / d);   // pasangan yang keduanya kelipatan d
    cout << hasil << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Kuncinya: Σ<sub>d | g</sub> μ(d) bernilai 1 jika g = 1 dan 0 jika g &gt; 1. Jadi</p>
<p>[gcd(i, j) = 1] = Σ<sub>d | gcd(i, j)</sub> μ(d) = Σ<sub>d | i dan d | j</sub> μ(d).</p>
<p>Tukar urutan penjumlahan: untuk setiap d, ada ⌊N/d⌋<sup>2</sup> pasangan yang keduanya kelipatan d. Total O(N).</p>
TXT],
    ];

    $mbPy = <<<'PY'
N = int(input())
mu = [1] * (N + 1)
prima = [True] * (N + 1)
for p in range(2, N + 1):
    if prima[p]:
        for x in range(p, N + 1, p):
            if x > p:
                prima[x] = False
            mu[x] = -mu[x]
        for x in range(p * p, N + 1, p * p):
            mu[x] = 0
print(sum(mu[d] * (N // d) ** 2 for d in range(1, N + 1)))
PY;

    $mbPhi = <<<'CPP'
// phi Euler dengan saringan: phi[n] = banyak 1 ≤ x ≤ n yang koprima dengan n
vector<int> phi(N + 1);
iota(phi.begin(), phi.end(), 0);
for (int p = 2; p <= N; p++)
    if (phi[p] == p)                           // p prima (belum disentuh)
        for (int x = p; x <= N; x += p) phi[x] -= phi[x] / p;   // kalikan (1 - 1/p)
CPP;

    $mbGcdSum = <<<'CPP'
// Σ_{i=1..N} Σ_{j=1..N} gcd(i, j): pakai identitas n = Σ_{d | n} phi(d)
//   gcd(i, j) = Σ_{d | gcd(i,j)} phi(d)  →  jumlah = Σ_d phi(d) · ⌊N/d⌋²
long long total = 0;
for (int d = 1; d <= N; d++) total += (long long)phi[d] * (N / d) * (N / d);
CPP;

    $mbBlock = <<<'CPP'
// ⌊N/d⌋ hanya punya O(√N) nilai berbeda: kelompokkan d dengan nilai yang sama
// (butuh prefix sum mu: M[r] - M[l-1])
for (long long l = 1, r; l <= N; l = r + 1) {
    long long q = N / l;
    r = N / q;                                 // d terbesar dengan ⌊N/d⌋ = q
    hasil += (M[r] - M[l - 1]) * q * q;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenal <strong>fungsi multiplikatif</strong>: φ (phi Euler), μ (Möbius), d (banyak pembagi).</li>
        <li>Menghitung μ dan φ untuk semua n ≤ N dengan <strong>saringan linear</strong>.</li>
        <li>Mengganti syarat <strong>gcd = 1</strong> menjadi penjumlahan atas pembagi dengan μ.</li>
        <li>Menghitung jumlah gcd semua pasangan dan memakai <strong>pengelompokan ⌊N/d⌋</strong>.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'inklusi-eksklusi atas semua prima', 'desc' => 'Fungsi Möbius adalah tanda +/− yang sudah dihitungkan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Inklusi-Eksklusi Otomatis">
    <h2>Intuisi: Inklusi-Eksklusi Otomatis</h2>
    <div class="prose">
        <p>Untuk menghitung pasangan (i, j) yang koprima, kita bisa memakai inklusi-eksklusi: mulai dari semua N² pasangan, kurangi yang keduanya genap, kurangi yang keduanya kelipatan 3, tambahkan kembali yang keduanya kelipatan 6, dan seterusnya untuk semua prima. Tanda setiap suku d hanya bergantung pada d: + jika d hasil kali genap banyak prima berbeda, − jika ganjil, dan suku dengan faktor kuadrat (4, 9, 12, …) tidak pernah muncul.</p>
        <p>Tanda itulah <strong>fungsi Möbius μ(d)</strong>. Dengan μ, inklusi-eksklusi atas ribuan prima menjadi satu loop sederhana.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Multiplikatif</b><span>f(ab) = f(a)f(b) jika gcd(a, b) = 1. Cukup tahu nilainya di pangkat prima.</span></div>
        <div class="term"><b>φ(n)</b><span>Banyak 1..n yang koprima dengan n. φ(p<sup>e</sup>) = p<sup>e</sup> − p<sup>e−1</sup>.</span></div>
        <div class="term"><b>μ(n)</b><span>0 jika ada p² | n; (−1)<sup>k</sup> jika n hasil kali k prima berbeda.</span></div>
        <div class="term"><b>Σ<sub>d|n</sub> μ(d)</b><span>= 1 jika n = 1, selain itu 0. Inti inversi Möbius.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: μ dan Pasangan Koprima</h2>
    <div class="prose"><p>Fase pertama mengisi μ(1..N). Fase kedua menjumlahkan μ(d)·⌊N/d⌋² dan di akhir dibandingkan dengan brute force.</p></div>
    <div data-viz="mobius"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'Möbius di C++', 'desc' => 'Saringan linear dan penjumlahan atas pembagi.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Pasangan Koprima</h2>
    @include('lessons.walkthrough', [
        'title' => 'Banyak pasangan dengan gcd = 1',
        'steps' => $mbSteps,
        'sample' => ['input' => "12\n", 'output' => "91\n"],
        'py' => $mbPy,
    ])
</section>

<section class="lesson-section" id="phi" data-toc="Phi Euler & Jumlah gcd">
    <h2>Phi Euler dan Jumlah gcd</h2>
    @include('lessons.code', ['cpp' => $mbPhi, 'js' => null, 'py' => null])
    <div class="prose"><p>Identitas Gauss n = Σ<sub>d | n</sub> φ(d) mengubah "jumlah gcd" menjadi penjumlahan atas pembagi, dengan cara yang sama seperti μ mengubah "gcd = 1".</p></div>
    @include('lessons.code', ['cpp' => $mbGcdSum, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pasangan berurutan atau tidak?</strong> Rumus di atas menghitung (i, j) dan (j, i) sebagai dua pasangan, termasuk i = j. Untuk pasangan tak berurutan i &lt; j, ubah: (total − banyak i = j yang memenuhi) / 2.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> μ(d)·⌊N/d⌋² sampai 10<sup>14</sup> untuk N = 10<sup>7</sup>: cast ke <code>long long</code> sebelum mengalikan.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'inversi Möbius dan blok ⌊N/d⌋', 'desc' => 'Dari O(N) ke O(√N).'])

<section class="lesson-section" id="inversi" data-toc="Inversi & Blok">
    <h2>Inversi Möbius dan Pengelompokan ⌊N/d⌋</h2>
    <div class="proof">
        <p><strong>Mengapa Σ<sub>d|n</sub> μ(d) = [n = 1]?</strong> Untuk n &gt; 1 dengan k prima berbeda, hanya pembagi bebas kuadrat yang bernilai tidak nol; ada C(k, j) yang memakai j prima, dengan tanda (−1)<sup>j</sup>. Jumlahnya Σ<sub>j</sub> C(k, j)(−1)<sup>j</sup> = (1 − 1)<sup>k</sup> = 0.</p>
        <p><strong>Inversi Möbius.</strong> Jika g(n) = Σ<sub>d|n</sub> f(d) untuk semua n, maka f(n) = Σ<sub>d|n</sub> μ(d)·g(n/d). Contoh: n = Σ φ(d), jadi φ(n) = Σ<sub>d|n</sub> μ(d)·n/d.</p>
    </div>
    <div class="prose"><p>Nilai ⌊N/d⌋ hanya berubah O(√N) kali saat d naik dari 1 ke N. Dengan prefix sum μ, penjumlahan di atas bisa dikelompokkan per blok nilai yang sama: berguna saat ada banyak pertanyaan N berbeda.</p></div>
    @include('lessons.code', ['cpp' => $mbBlock, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Hitung pasangan gcd = 1</h4><p>Σ μ(d) · (banyak kelipatan d)².</p></div>
        <div class="pattern"><h4>Pasangan gcd = k</h4><p>Bagi semua dengan k, lalu hitung gcd = 1 di rentang ⌊N/k⌋.</p></div>
        <div class="pattern"><h4>Array sembarang</h4><p>cnt[d] = banyak elemen kelipatan d; jawab Σ μ(d)·C(cnt[d], 2).</p></div>
        <div class="pattern"><h4>Jumlah gcd / lcm</h4><p>Pakai φ atau inversi Möbius, lalu blok ⌊N/d⌋.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="30 = 2·3·5, tiga prima berbeda tanpa kuadrat: μ = (−1)³ = −1. 12 = 2²·3 punya faktor kuadrat: μ = 0.">
        <p class="quiz-q">μ(30) dan μ(12) berturut-turut…</p>
        <div class="quiz-options">
            <button class="quiz-option">1 dan 0</button>
            <button class="quiz-option">−1 dan −1</button>
            <button class="quiz-option">−1 dan 0</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="φ(12) = 12 · (1 − 1/2)(1 − 1/3) = 4: yaitu 1, 5, 7, 11.">
        <p class="quiz-q">φ(12) = …</p>
        <div class="quiz-options">
            <button class="quiz-option">4</button>
            <button class="quiz-option">6</button>
            <button class="quiz-option">8</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Untuk N = 100, ⌊100/d⌋ bernilai berbeda sekitar 2√100 = 20 kali (tepatnya 19).">
        <p class="quiz-q">Kira-kira berapa banyak nilai berbeda ⌊N/d⌋ untuk d = 1..N?</p>
        <div class="quiz-options">
            <button class="quiz-option">N</button>
            <button class="quiz-option">sekitar 2√N</button>
            <button class="quiz-option">log N</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
