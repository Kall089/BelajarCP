@php
    $sgSteps = [
        ['Saring faktor prima terkecil', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000000;
int spf[MAKS + 1];                    // spf[x] = faktor prima terkecil dari x

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    for (int i = 2; i <= MAKS; i++)
        if (spf[i] == 0)              // belum punya faktor: i prima
            for (int j = i; j <= MAKS; j += i)
                if (spf[j] == 0) spf[j] = i;
CPP, <<<'TXT'
<p><strong>Soal contoh "Banyak Pembagi":</strong> ada q pertanyaan, masing-masing sebuah bilangan x ≤ 10<sup>6</sup>. Untuk setiap x, cetak banyak pembagi positifnya. q bisa sampai 10<sup>5</sup>, jadi mencoba semua d ≤ √x untuk setiap pertanyaan terlalu lambat.</p>
<p>Sekali saring, simpan <strong>faktor prima terkecil</strong> (smallest prime factor) setiap bilangan. Total kerja Σ N/p = O(N log log N).</p>
TXT],
        ['Faktorkan dengan spf', <<<'CPP'

    int q;
    cin >> q;
    while (q--) {
        int x;
        cin >> x;
        long long pembagi = 1;
        while (x > 1) {
            int p = spf[x], e = 0;
            while (x % p == 0) {      // ambil semua faktor p
                x /= p;
                e++;
            }
            pembagi *= e + 1;         // x = p1^e1 · p2^e2 ··· → (e1+1)(e2+1)···
        }
        cout << pembagi << "\n";
    }
    return 0;
}
CPP, <<<'TXT'
<p>Dengan spf, memfaktorkan x hanya butuh O(log x) pembagian: setiap langkah membagi x dengan bilangan ≥ 2.</p>
<p>Jika x = p<sub>1</sub><sup>e<sub>1</sub></sup> · p<sub>2</sub><sup>e<sub>2</sub></sup> ⋯, setiap pembagi memilih pangkat 0..e<sub>i</sub> untuk setiap prima, jadi banyaknya (e<sub>1</sub> + 1)(e<sub>2</sub> + 1)⋯. Contoh 360 = 2³·3²·5: (3 + 1)(2 + 1)(1 + 1) = 24.</p>
TXT],
    ];

    $sgPy = <<<'PY'
import sys

data = sys.stdin.buffer.read().split()
q = int(data[0])
xs = [int(v) for v in data[1:1 + q]]
M = max(xs + [2])
spf = list(range(M + 1))
i = 2
while i * i <= M:
    if spf[i] == i:
        for j in range(i * i, M + 1, i):
            if spf[j] == j:
                spf[j] = i
    i += 1
out = []
for x in xs:
    pembagi = 1
    while x > 1:
        p, e = spf[x], 0
        while x % p == 0:
            x //= p
            e += 1
        pembagi *= e + 1
    out.append(str(pembagi))
print("\n".join(out))
PY;

    $sgPrime = <<<'CPP'
// Uji prima O(√n): cukup coba pembagi sampai √n
bool prima(long long n) {
    if (n < 2) return false;
    for (long long d = 2; d * d <= n; d++)
        if (n % d == 0) return false;
    return true;
}
// Faktorisasi O(√n): sisa > 1 setelah loop pasti prima
vector<pair<long long, int>> faktor(long long n) {
    vector<pair<long long, int>> f;
    for (long long d = 2; d * d <= n; d++)
        if (n % d == 0) {
            int e = 0;
            while (n % d == 0) { n /= d; e++; }
            f.push_back({d, e});
        }
    if (n > 1) f.push_back({n, 1});
    return f;
}
CPP;

    $sgGcd = <<<'CPP'
long long fpb(long long a, long long b) {      // algoritma Euclid, O(log min(a, b))
    while (b) { long long t = a % b; a = b; b = t; }
    return a;
}
long long kpk(long long a, long long b) {
    return a / fpb(a, b) * b;                 // bagi DULU agar tidak overflow
}
// Di C++14 pakai __gcd(a, b) dari <algorithm>; std::gcd baru ada di C++17
CPP;

    $sgLinear = <<<'CPP'
// Saringan linear: setiap bilangan komposit dicoret TEPAT sekali oleh spf-nya. O(N)
vector<int> primes, spf(N + 1, 0);
for (int i = 2; i <= N; i++) {
    if (spf[i] == 0) { spf[i] = i; primes.push_back(i); }
    for (int p : primes) {
        if (p > spf[i] || (long long)i * p > N) break;
        spf[i * p] = p;                       // p ≤ spf[i] → p adalah faktor terkecil i·p
    }
}
CPP;

    $sgSeg = <<<'CPP'
// Saringan bersegmen: prima di [L, R] dengan R sampai 10^12, R − L ≤ 10^6
vector<bool> komposit(R - L + 1, false);
for (long long p : primaKecil)                 // semua prima ≤ √R (saringan biasa)
    for (long long x = max(p * p, (L + p - 1) / p * p); x <= R; x += p)
        komposit[x - L] = true;
// hati-hati: jika L ≤ 1, tandai 0 dan 1 sebagai bukan prima
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menguji keprimaan dan memfaktorkan bilangan dalam O(√n).</li>
        <li>Menulis <strong>saringan Eratosthenes</strong> dan saringan <strong>faktor prima terkecil</strong> untuk menjawab banyak pertanyaan.</li>
        <li>Menghitung banyak pembagi dan jumlah pembagi dari faktorisasi prima.</li>
        <li>Memakai <strong>algoritma Euclid</strong> untuk FPB/KPK, saringan linear, dan saringan bersegmen.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'bilangan prima dan FPB', 'desc' => 'Batu bata semua bilangan bulat.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Batu Bata Bilangan">
    <h2>Intuisi: Bilangan Prima sebagai Batu Bata</h2>
    <div class="prose">
        <p>Setiap bilangan bulat ≥ 2 bisa ditulis sebagai hasil kali bilangan prima dengan cara yang <strong>tunggal</strong>: 360 = 2·2·2·3·3·5. Bilangan prima seperti batu bata, dan faktorisasi adalah denah bangunannya. Banyak soal teori bilangan menjadi mudah setelah kita tahu denah itu: banyak pembagi, FPB, KPK, φ(n), semuanya terbaca dari pangkat-pangkat prima.</p>
        <p>Untuk satu bilangan, cukup coba pembagi sampai √n: jika n = a·b dengan a ≤ b, maka a ≤ √n. Untuk banyak bilangan sekaligus, lebih hemat menyaring semuanya di awal: <strong>saringan Eratosthenes</strong>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Prima</b><span>Bilangan ≥ 2 yang hanya habis dibagi 1 dan dirinya sendiri. 1 bukan prima.</span></div>
        <div class="term"><b>spf(x)</b><span>Faktor prima terkecil x. Disimpan untuk semua x ≤ N agar faktorisasi O(log x).</span></div>
        <div class="term"><b>FPB / gcd</b><span>Faktor persekutuan terbesar. Euclid: gcd(a, b) = gcd(b, a mod b).</span></div>
        <div class="term"><b>KPK / lcm</b><span>Kelipatan persekutuan terkecil: a / gcd(a, b) · b.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Saringan">
    <h2>Visualisasi: Saringan Eratosthenes</h2>
    <div class="prose"><p>Setiap prima p mencoret kelipatannya mulai dari p², karena kelipatan yang lebih kecil sudah dicoret prima yang lebih kecil. Saringan berhenti saat p² melewati N.</p></div>
    <div data-viz="sieve"></div>
</section>

<section class="lesson-section" id="dasar" data-toc="Uji Prima, Faktorisasi, Euclid">
    <h2>Uji Prima, Faktorisasi, dan Euclid</h2>
    @include('lessons.code', ['cpp' => $sgPrime, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Mengapa Euclid benar?</strong> Setiap pembagi bersama a dan b juga membagi a − q·b = a mod b, dan sebaliknya. Jadi pasangan (a, b) dan (b, a mod b) punya pembagi bersama yang sama persis, termasuk yang terbesar. Karena a mod b &lt; b dan setiap dua langkah bilangannya setidaknya terbagi dua, banyak langkah O(log min(a, b)).</p>
    </div>
    @include('lessons.code', ['cpp' => $sgGcd, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 2, 'title' => 'saringan untuk banyak pertanyaan', 'desc' => 'Saring sekali, jawab ribuan pertanyaan.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Banyak Pembagi</h2>
    @include('lessons.walkthrough', [
        'title' => 'Saringan spf + faktorisasi cepat',
        'steps' => $sgSteps,
        'sample' => ['input' => "4\n12\n1\n97\n360\n", 'output' => "6\n1\n2\n24\n"],
        'py' => $sgPy,
    ])
</section>

<section class="lesson-section" id="rumus" data-toc="Rumus dari Faktorisasi">
    <h2>Rumus dari Faktorisasi</h2>
    <div class="recurrence"><small>n = p₁^e₁ · p₂^e₂ ··· pₖ^eₖ</small>banyak pembagi   d(n) = (e₁ + 1)(e₂ + 1)···(eₖ + 1)
jumlah pembagi   σ(n) = Π (pᵢ^(eᵢ+1) − 1) / (pᵢ − 1)
phi Euler        φ(n) = n · Π (1 − 1/pᵢ)
FPB / KPK        pangkat minimum / maksimum dari setiap prima</div>
    <table class="cx-table">
        <tr><th>Teknik</th><th>Waktu</th><th>Kapan dipakai</th></tr>
        <tr><td>Pembagian percobaan</td><td><code>O(√n)</code></td><td>Sedikit bilangan, n ≤ 10<sup>12</sup></td></tr>
        <tr><td>Saringan Eratosthenes</td><td><code>O(N log log N)</code></td><td>Semua prima ≤ N (N ≤ 10<sup>7</sup>–10<sup>8</sup>)</td></tr>
        <tr><td>Saringan spf + faktorisasi</td><td><code>O(N log log N) + O(log x)</code> per x</td><td>Banyak faktorisasi, x ≤ 10<sup>7</sup></td></tr>
        <tr><td>Saringan bersegmen</td><td><code>O((R − L) log log R + √R)</code></td><td>Rentang sempit di bilangan besar</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> <code>d * d &lt;= n</code> dengan <code>int</code> meluap untuk n mendekati 2·10<sup>9</sup>; pakai <code>long long</code>. KPK ditulis <code>a / gcd * b</code>, bukan <code>a * b / gcd</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Memori saringan.</strong> <code>vector&lt;bool&gt;</code> 10<sup>8</sup> butuh ±12 MB, tetapi <code>int spf[10<sup>8</sup>]</code> butuh 400 MB. Sesuaikan dengan batas memori.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'saringan linear dan bersegmen', 'desc' => 'O(N) tepat, dan prima di sekitar 10^12.'])

<section class="lesson-section" id="lanjut" data-toc="Linear & Bersegmen">
    <h2>Saringan Linear dan Saringan Bersegmen</h2>
    <div class="prose"><p>Saringan Eratosthenes mencoret 12 dua kali (oleh 2 dan 3). Saringan linear memastikan setiap bilangan komposit dicoret tepat sekali, oleh faktor prima terkecilnya, sehingga totalnya O(N). Bonusnya, spf dan daftar prima didapat sekaligus, dan saringan ini mudah diubah untuk menghitung fungsi multiplikatif seperti φ dan μ.</p></div>
    @include('lessons.code', ['cpp' => $sgLinear, 'js' => null, 'py' => null])
    <div class="prose"><p>Untuk prima di rentang [L, R] dengan R sampai 10<sup>12</sup>, kita tidak bisa menyaring sampai R. Tetapi setiap komposit ≤ R punya faktor prima ≤ √R ≤ 10<sup>6</sup>. Saring dulu prima sampai √R, lalu coret kelipatannya di dalam jendela [L, R] saja.</p></div>
    @include('lessons.code', ['cpp' => $sgSeg, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>FPB terbesar di antara pasangan</h4><p>Untuk setiap d dari besar ke kecil, hitung banyak elemen kelipatan d: O(M log M).</p></div>
        <div class="pattern"><h4>FPB rentang</h4><p>gcd bersifat asosiatif: sparse table atau segment tree.</p></div>
        <div class="pattern"><h4>gcd(a, b) = gcd(a, b − a)</h4><p>Berguna untuk FPB dari selisih: gcd(a₁ + x, …, aₙ + x).</p></div>
        <div class="pattern"><h4>Banyak pembagi ≤ 10<sup>18</sup></h4><p>Paling banyak sekitar 10<sup>5</sup>: brute force atas pembagi sering cukup.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="72 = 2³ · 3², jadi (3 + 1)(2 + 1) = 12 pembagi.">
        <p class="quiz-q">Berapa banyak pembagi positif 72?</p>
        <div class="quiz-options">
            <button class="quiz-option">8</button>
            <button class="quiz-option">10</button>
            <button class="quiz-option">12</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Kelipatan p yang lebih kecil dari p², yaitu p·k dengan k < p, punya faktor prima k' ≤ k < p, sehingga sudah dicoret oleh prima yang lebih kecil.">
        <p class="quiz-q">Mengapa saringan mulai mencoret dari p², bukan dari 2p?</p>
        <div class="quiz-options">
            <button class="quiz-option">Kelipatan p yang lebih kecil dari p² sudah dicoret oleh prima yang lebih kecil</button>
            <button class="quiz-option">Agar tidak mencoret p sendiri</button>
            <button class="quiz-option">Karena p² selalu genap</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="gcd(84, 36) = gcd(36, 12) = gcd(12, 0) = 12. KPK = 84 / 12 · 36 = 252.">
        <p class="quiz-q">KPK(84, 36) = …</p>
        <div class="quiz-options">
            <button class="quiz-option">3024</button>
            <button class="quiz-option">252</button>
            <button class="quiz-option">504</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
