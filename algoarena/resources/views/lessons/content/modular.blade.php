@php
    $mdSteps = [
        ['Fungsi perpangkatan cepat', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

long long pangkat(long long a, long long b, long long m) {
    long long hasil = 1 % m;          // 1 % m: aman juga jika m = 1
    a %= m;
    while (b > 0) {
        if (b & 1) hasil = hasil * a % m;
        a = a * a % m;
        b >>= 1;
    }
    return hasil;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Pangkat Besar":</strong> untuk t pertanyaan (a, b, m) dengan b sampai 10<sup>18</sup> dan m ≤ 10<sup>9</sup> + 7, cetak a<sup>b</sup> mod m.</p>
<p>Mengalikan a sebanyak b kali mustahil. Tulis b dalam biner: a<sup>13</sup> = a<sup>8</sup> · a<sup>4</sup> · a<sup>1</sup>. Kuadratkan a berulang kali (a, a², a⁴, a⁸, …) dan kalikan ke hasil hanya untuk bit yang bernilai 1. Hanya O(log b) perkalian.</p>
<p>Semua nilai di bawah m ≤ ±10<sup>9</sup>, jadi hasil kali dua nilai ≤ 10<sup>18</sup> dan muat di <code>long long</code>.</p>
TXT],
        ['Jawab setiap pertanyaan', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int t;
    cin >> t;
    while (t--) {
        long long a, b, m;
        cin >> a >> b >> m;
        cout << pangkat(a, b, m) << "\n";
    }
    return 0;
}
CPP, <<<'TXT'
<p>Perhatikan pertanyaan ketiga di contoh: 10<sup>10<sup>18</sup></sup> mod (10<sup>9</sup> + 7) dihitung dalam sekitar 60 putaran.</p>
<div class="wt-warn">Hati-hati dengan a<sup>0</sup>: hasilnya 1 (bahkan untuk a = 0, menurut kesepakatan soal-soal CP). Inisialisasi <code>hasil = 1 % m</code> menangani m = 1 dengan benar.</div>
TXT],
    ];

    $mdPy = <<<'PY'
import sys

data = sys.stdin.buffer.read().split()
t = int(data[0])
out = []
for i in range(t):
    a, b, m = int(data[1 + 3 * i]), int(data[2 + 3 * i]), int(data[3 + 3 * i])
    out.append(str(pow(a, b, m)))       # pow bawaan Python sudah memakai perpangkatan cepat
print("\n".join(out))
PY;

    $mdInv = <<<'CPP'
// Invers modulo prima p (Fermat kecil): a^(p-1) ≡ 1, jadi a^(p-2) ≡ a^(-1)  (a tidak habis dibagi p)
const long long MOD = 1000000007;
long long invers(long long a) { return pangkat(a, MOD - 2, MOD); }

// Pembagian: (x / y) mod p = x * invers(y) mod p
long long bagi(long long x, long long y) { return x % MOD * invers(y) % MOD; }
CPP;

    $mdNcr = <<<'CPP'
// C(n, k) mod p untuk banyak pertanyaan, n ≤ N: pra-hitung faktorial dan invers faktorial
vector<long long> fact(N + 1), inv(N + 1);
fact[0] = 1;
for (int i = 1; i <= N; i++) fact[i] = fact[i - 1] * i % MOD;
inv[N] = pangkat(fact[N], MOD - 2, MOD);                 // satu kali Fermat
for (int i = N; i > 0; i--) inv[i - 1] = inv[i] * i % MOD;   // 1/(i-1)! = i · 1/i!
long long C(int n, int k) {
    if (k < 0 || k > n) return 0;
    return fact[n] * inv[k] % MOD * inv[n - k] % MOD;
}
CPP;

    $mdNeg = <<<'CPP'
// Pengurangan: hasil % di C++ bisa NEGATIF
long long kurang(long long a, long long b) { return ((a - b) % MOD + MOD) % MOD; }
// Contoh: (2 - 5) % 7 == -3 di C++, padahal yang diinginkan 4
CPP;

    $mdLucas = <<<'CPP'
// Teorema Lucas: C(n, k) mod p (p prima kecil, n besar) = hasil kali C(n_i, k_i) per digit basis p
long long lucas(long long n, long long k, int p) {
    long long r = 1;
    while (n || k) {
        int ni = n % p, ki = k % p;
        if (ki > ni) return 0;
        r = r * Ckecil(ni, ki, p) % p;        // C kecil dari tabel faktorial mod p
        n /= p; k /= p;
    }
    return r;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai sifat <strong>aritmetika modulo</strong> untuk tambah, kurang, dan kali tanpa overflow.</li>
        <li>Menghitung a<sup>b</sup> mod m dalam O(log b) dengan <strong>perpangkatan cepat</strong>.</li>
        <li>Membagi modulo prima dengan <strong>invers Fermat</strong>.</li>
        <li>Menghitung <strong>C(n, k) mod p</strong> dengan faktorial dan invers faktorial, serta mengenal teorema Lucas dan Euler.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'berhitung di jam dinding', 'desc' => 'Bilangan raksasa cukup diwakili sisa baginya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Jam Dinding">
    <h2>Intuisi: Berhitung di Jam Dinding</h2>
    <div class="prose">
        <p>Jam dinding hanya mengenal 12 angka: 9 jam setelah pukul 8 adalah pukul 5, karena 17 mod 12 = 5. Itulah <strong>aritmetika modulo</strong>. Banyak soal CP meminta jawaban "modulo 10<sup>9</sup> + 7" karena jawaban aslinya terlalu besar (misalnya banyak cara yang punya ribuan digit). Kabar baiknya, kita tidak pernah perlu menyimpan bilangan raksasa itu: cukup sisa baginya, asal setiap operasi diambil modulonya.</p>
    </div>
    <div class="recurrence"><small>Sifat yang boleh dipakai</small>(a + b) mod m = ((a mod m) + (b mod m)) mod m
(a − b) mod m = ((a mod m) − (b mod m) + m) mod m
(a · b) mod m = ((a mod m) · (b mod m)) mod m
(a / b) mod m  ≠ (a mod m) / (b mod m)     ← pembagian butuh INVERS</div>
    <div class="term-grid">
        <div class="term"><b>10<sup>9</sup> + 7</b><span>Prima, dan 2 × (10<sup>9</sup>+7) muat di int; hasil kali dua nilai muat di long long.</span></div>
        <div class="term"><b>998244353</b><span>Prima yang ramah NTT (= 119·2<sup>23</sup> + 1).</span></div>
        <div class="term"><b>Invers</b><span>b<sup>−1</sup> dengan b·b<sup>−1</sup> ≡ 1 (mod m). Ada jika gcd(b, m) = 1.</span></div>
        <div class="term"><b>Fermat kecil</b><span>Untuk p prima dan a tidak habis dibagi p: a<sup>p−1</sup> ≡ 1, jadi a<sup>−1</sup> ≡ a<sup>p−2</sup>.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Perpangkatan Cepat</h2>
    <div class="prose"><p>Setiap baris tabel adalah satu bit dari b. Pangkat a selalu dikuadratkan; hasil hanya dikalikan saat bitnya 1.</p></div>
    <div data-viz="fastpow"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'modulo di C++', 'desc' => 'Perpangkatan cepat, invers, dan C(n, k).'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Pangkat Besar</h2>
    @include('lessons.walkthrough', [
        'title' => 'a^b mod m dalam O(log b)',
        'steps' => $mdSteps,
        'sample' => ['input' => "3\n2 10 1000\n3 13 1000\n10 1000000000000000000 1000000007\n", 'output' => "24\n323\n2401\n"],
        'py' => $mdPy,
    ])
</section>

<section class="lesson-section" id="invers" data-toc="Invers & C(n, k)">
    <h2>Invers Modulo dan C(n, k)</h2>
    <div class="prose"><p>Pembagian modulo prima diganti perkalian dengan invers. Teorema Fermat kecil memberi invers lewat perpangkatan cepat.</p></div>
    @include('lessons.code', ['cpp' => $mdInv, 'js' => null, 'py' => null])
    <div class="prose"><p>C(n, k) = n! / (k! (n − k)!). Pra-hitung semua faktorial dan invers faktorial sekali (O(N)), lalu setiap C(n, k) dijawab dalam O(1). Trik: cukup satu perpangkatan untuk 1/N!, sisanya mundur dengan 1/(i − 1)! = i · 1/i!.</p></div>
    @include('lessons.code', ['cpp' => $mdNcr, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Jebakan Modulo">
    <h2>Jebakan Modulo</h2>
    @include('lessons.code', ['cpp' => $mdNeg, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow perkalian.</strong> <code>int * int</code> dihitung dalam <code>int</code> walaupun disimpan ke <code>long long</code>. Tulis <code>1LL * a * b % MOD</code>. Untuk modulus sampai 10<sup>18</sup>, hasil kali butuh <code>__int128</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa modulo di tengah jalan.</strong> Menjumlahkan 10<sup>6</sup> nilai &lt; 10<sup>9</sup> tanpa modulo bisa melewati 10<sup>15</sup> (masih aman di long long), tetapi mengalikannya tidak. Ambil modulo setiap kali.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Membandingkan hasil modulo.</strong> Jika soal meminta "jawaban terbesar mod p", kamu tidak bisa membandingkan nilai sesudah modulo. Bandingkan nilai aslinya (atau logaritmanya), baru ambil modulo.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'Euler, Lucas, dan menara pangkat', 'desc' => 'Ketika modulus bukan prima atau n sangat besar.'])

<section class="lesson-section" id="lanjut" data-toc="Euler & Lucas">
    <h2>Teorema Euler dan Teorema Lucas</h2>
    <div class="prose">
        <p><strong>Teorema Euler.</strong> Untuk gcd(a, m) = 1: a<sup>φ(m)</sup> ≡ 1 (mod m). Fermat kecil adalah kasus m prima (φ(p) = p − 1). Akibatnya, pangkat boleh direduksi modulo φ(m): a<sup>b</sup> ≡ a<sup>b mod φ(m)</sup>. Ini kunci menghitung menara pangkat a<sup>b<sup>c</sup></sup> mod p: hitung dulu b<sup>c</sup> mod (p − 1).</p>
        <p><strong>Teorema Lucas.</strong> Jika p prima kecil tetapi n sangat besar (n ≥ p, sehingga n! mengandung p dan tidak punya invers), C(n, k) mod p adalah hasil kali C(n<sub>i</sub>, k<sub>i</sub>) untuk setiap digit basis-p dari n dan k.</p>
    </div>
    @include('lessons.code', ['cpp' => $mdLucas, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>"Cetak modulo 10<sup>9</sup>+7"</h4><p>Ambil modulo setiap operasi; bagi = kali invers.</p></div>
        <div class="pattern"><h4>Banyak C(n, k)</h4><p>Faktorial + invers faktorial, O(1) per pertanyaan.</p></div>
        <div class="pattern"><h4>Peluang / ekspektasi pecahan</h4><p>Cetak P · Q<sup>−1</sup> mod p.</p></div>
        <div class="pattern"><h4>Menara pangkat</h4><p>Reduksi eksponen modulo φ(m) (hati-hati jika gcd ≠ 1).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Di C++, (3 − 8) % 7 = −5. Tambahkan 7 lalu ambil modulo lagi: 2.">
        <p class="quiz-q">Berapa ((3 − 8) % 7 + 7) % 7?</p>
        <div class="quiz-options">
            <button class="quiz-option">−5</button>
            <button class="quiz-option">2</button>
            <button class="quiz-option">5</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="3 · 5 = 15 = 2 · 7 + 1, jadi 3 · 5 ≡ 1 (mod 7). Dengan Fermat: 3^(7−2) = 3^5 = 243 ≡ 5.">
        <p class="quiz-q">Invers 3 modulo 7 adalah…</p>
        <div class="quiz-options">
            <button class="quiz-option">3</button>
            <button class="quiz-option">4</button>
            <button class="quiz-option">5</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Perpangkatan cepat butuh sekitar log₂(10^18) ≈ 60 putaran, masing-masing paling banyak dua perkalian.">
        <p class="quiz-q">Kira-kira berapa perkalian untuk menghitung a<sup>10<sup>18</sup></sup> mod m dengan perpangkatan cepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">±120</button>
            <button class="quiz-option">±10<sup>9</sup></button>
            <button class="quiz-option">±10<sup>18</sup></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
