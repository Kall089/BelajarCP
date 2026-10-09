@php
    $prSteps = [
        ['Perkalian modulo 128-bit', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

__extension__ typedef unsigned __int128 u128;   // 128-bit (GCC/Clang), __extension__ meredam peringatan
typedef unsigned long long ull;

ull mulmod(ull a, ull b, ull m) { return (ull)((u128)a * b % m); }

ull powmod(ull a, ull e, ull m) {
    ull r = 1;
    a %= m;
    while (e) {
        if (e & 1) r = mulmod(r, a, m);
        a = mulmod(a, a, m);
        e >>= 1;
    }
    return r;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Prima atau Bukan":</strong> untuk q bilangan n sampai 10<sup>18</sup>, cetak PRIMA atau BUKAN. Pembagian percobaan sampai √n = 10<sup>9</sup> terlalu lambat.</p>
<p>Untuk n sampai 10<sup>18</sup>, hasil kali dua sisa bisa mencapai 10<sup>36</sup>, melebihi 64 bit. <code>__int128</code> menyimpan hasil kali itu dengan aman sebelum diambil modulonya.</p>
TXT],
        ['Uji Miller-Rabin', <<<'CPP'

bool prima(ull n) {
    if (n < 2) return false;
    const ull basis[] = {2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37};
    for (ull p : basis)
        if (n % p == 0) return n == p;
    ull d = n - 1;
    int s = 0;
    while (d % 2 == 0) {                     // n - 1 = d · 2^s, d ganjil
        d /= 2;
        s++;
    }
    for (ull a : basis) {
        ull x = powmod(a, d, n);
        if (x == 1 || x == n - 1) continue;  // a lolos
        bool saksi = true;                   // a membuktikan n komposit, kecuali...
        for (int r = 1; r < s; r++) {
            x = mulmod(x, x, n);
            if (x == n - 1) {                // ...barisan kuadrat sempat mencapai -1
                saksi = false;
                break;
            }
        }
        if (saksi) return false;
    }
    return true;
}
CPP, <<<'TXT'
<p>Jika n prima, untuk setiap a barisan a<sup>d</sup>, a<sup>2d</sup>, …, a<sup>2<sup>s</sup>d</sup> ≡ 1 harus diawali 1, atau memuat −1 tepat sebelum menjadi 1 (karena akar kuadrat 1 modulo prima hanya ±1). Jika barisan melanggar, a adalah <strong>saksi</strong> bahwa n komposit.</p>
<p>Untuk n &lt; 2<sup>64</sup>, 12 basis prima pertama terbukti cukup: tidak ada komposit yang lolos semuanya. Setiap uji O(log n) perkalian.</p>
TXT],
        ['Jawab pertanyaan', <<<'CPP'

int main() {
    int q;
    cin >> q;
    while (q--) {
        ull n;
        cin >> n;
        cout << (prima(n) ? "PRIMA" : "BUKAN") << "\n";
    }
    return 0;
}
CPP, <<<'TXT'
<p>Total O(q · 12 · log n). Untuk q = 10<sup>5</sup> masih sangat cepat.</p>
TXT],
    ];

    $prPy = <<<'PY'
import sys

BASIS = (2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37)

def prima(n):
    if n < 2:
        return False
    for p in BASIS:
        if n % p == 0:
            return n == p
    d, s = n - 1, 0
    while d % 2 == 0:
        d //= 2
        s += 1
    for a in BASIS:
        x = pow(a, d, n)
        if x == 1 or x == n - 1:
            continue
        for _ in range(s - 1):
            x = x * x % n
            if x == n - 1:
                break
        else:
            return False
    return True

data = sys.stdin.read().split()
print("\n".join("PRIMA" if prima(int(v)) else "BUKAN" for v in data[1:1 + int(data[0])]))
PY;

    $prRho = <<<'CPP'
// Pollard Rho (Floyd): mencari SATU faktor nontrivial dari n komposit
mt19937_64 rng(12345);
ull rho(ull n) {
    if (n % 2 == 0) return 2;
    while (true) {
        ull c = rng() % (n - 1) + 1, x = rng() % n, y = x, d = 1;
        auto f = [&](ull v) { return (mulmod(v, v, n) + c) % n; };
        while (d == 1) {
            x = f(x);                  // kura-kura: satu langkah
            y = f(f(y));               // kelinci: dua langkah
            d = __gcd(x > y ? x - y : y - x, n);
        }
        if (d != n) return d;          // berhasil; jika d == n, coba c lain
    }
}
// Faktorisasi lengkap: rekursif sampai setiap bagian prima
void faktorkan(ull n, vector<ull>& f) {
    if (n == 1) return;
    if (prima(n)) { f.push_back(n); return; }
    ull d = rho(n);
    faktorkan(d, f);
    faktorkan(n / d, f);
}
CPP;

    $prBrent = <<<'CPP'
// Percepatan praktis: kalikan |x - y| beberapa kali (misalnya 128) sebelum memanggil gcd,
// karena gcd jauh lebih mahal daripada mulmod. Jika gcd kumpulan = n, ulangi satu per satu.
ull q = 1;
for (int i = 0; i < 128; i++) { x = f(x); y = f(f(y)); q = mulmod(q, x > y ? x - y : y - x, n); }
d = __gcd(q, n);
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengalikan modulo bilangan sampai 10<sup>18</sup> dengan <strong>__int128</strong>.</li>
        <li>Menguji keprimaan bilangan sampai 2<sup>64</sup> dengan <strong>Miller-Rabin deterministik</strong>.</li>
        <li>Memahami <strong>paradoks ulang tahun</strong> dan mengapa Pollard Rho menemukan faktor dalam ±n<sup>1/4</sup> langkah.</li>
        <li>Memfaktorkan bilangan sampai 10<sup>18</sup> dengan menggabungkan Miller-Rabin dan Pollard Rho.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'prima tanpa membagi', 'desc' => 'Saksi komposit dan barisan yang berulang.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Mencari Saksi">
    <h2>Intuisi: Mencari Saksi, Bukan Pembagi</h2>
    <div class="prose">
        <p>Untuk n = 10<sup>18</sup>, pembagian percobaan butuh 10<sup>9</sup> pembagian. Miller-Rabin memakai jalan lain: bilangan prima punya sifat tertentu (teorema Fermat dan "akar kuadrat dari 1 hanya ±1"). Jika kita menemukan bilangan a yang melanggar sifat itu, a adalah <strong>saksi</strong> bahwa n komposit, tanpa pernah tahu faktornya. Untuk n &lt; 2<sup>64</sup>, cukup memeriksa 12 calon saksi tertentu.</p>
        <p>Untuk benar-benar <em>menemukan</em> faktor, Pollard Rho memakai paradoks ulang tahun: barisan acak modulo p (faktor tersembunyi) pasti berulang setelah sekitar √p langkah. Kita tidak tahu p, tetapi pengulangan itu terlihat lewat gcd(|x − y|, n).</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Fermat</b><span>p prima ⇒ a<sup>p−1</sup> ≡ 1 (mod p). Ada komposit yang menipu (Carmichael).</span></div>
        <div class="term"><b>Miller-Rabin</b><span>Fermat diperkuat dengan syarat akar kuadrat 1. 12 basis cukup untuk 64 bit.</span></div>
        <div class="term"><b>Paradoks ulang tahun</b><span>Di antara ±√p bilangan acak mod p, kemungkinan besar ada dua yang sama.</span></div>
        <div class="term"><b>Floyd</b><span>Kura-kura 1 langkah, kelinci 2 langkah: bertemu di dalam siklus "ρ".</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Rho">
    <h2>Visualisasi: Kura-kura dan Kelinci</h2>
    <div class="prose"><p>Contoh klasik n = 8051 = 83 × 97. Perhatikan kapan gcd(|x − y|, n) berhenti bernilai 1.</p></div>
    <div data-viz="rho"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'Miller-Rabin di C++', 'desc' => 'Uji keprimaan deterministik untuk 64 bit.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Prima atau Bukan</h2>
    @include('lessons.walkthrough', [
        'title' => 'Miller-Rabin deterministik',
        'steps' => $prSteps,
        'sample' => ['input' => "5\n1\n2\n1000000007\n1000000000000000000\n999999999999999989\n", 'output' => "BUKAN\nPRIMA\nPRIMA\nBUKAN\nPRIMA\n"],
        'py' => $prPy,
    ])
</section>

<section class="lesson-section" id="rho" data-toc="Pollard Rho">
    <h2>Pollard Rho: Menemukan Faktor</h2>
    @include('lessons.code', ['cpp' => $prRho, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Uji prima dulu.</strong> Pollard Rho pada bilangan prima tidak akan pernah menemukan faktor (loop selamanya). Selalu periksa dengan Miller-Rabin sebelum memanggil rho.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong><code>__int128</code></strong> tersedia di GCC dan Clang 64-bit, tetapi tidak di MSVC. Di JavaScript pakai <code>BigInt</code>; Python punya bilangan bulat tak terbatas.</p>
    </div>
    @include('lessons.code', ['cpp' => $prBrent, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 3, 'title' => 'mengapa n^{1/4}', 'desc' => 'Analisis paradoks ulang tahun.'])

<section class="lesson-section" id="analisis" data-toc="Analisis & Pola">
    <h2>Analisis dan Pola</h2>
    <div class="proof">
        <p>Misalkan p faktor prima terkecil n, jadi p ≤ √n. Barisan x<sub>k+1</sub> = x<sub>k</sub>² + c mod n, jika dilihat modulo p, berperilaku seperti barisan acak di p nilai. Menurut paradoks ulang tahun, dua suku sama modulo p muncul setelah sekitar √p ≤ n<sup>1/4</sup> langkah. Saat itu x<sub>i</sub> ≡ x<sub>j</sub> (mod p), sehingga p membagi x<sub>i</sub> − x<sub>j</sub>, dan gcd(|x<sub>i</sub> − x<sub>j</sub>|, n) ≥ p. Floyd menemukan pasangan seperti itu dengan memori O(1).</p>
        <p>Untuk n ≈ 10<sup>18</sup>, n<sup>1/4</sup> ≈ 3·10<sup>4</sup> langkah, jauh lebih cepat dari 10<sup>9</sup>.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>n ≤ 10<sup>12</sup>, sedikit bilangan</h4><p>Pembagian percobaan sampai 10<sup>6</sup> masih cukup.</p></div>
        <div class="pattern"><h4>Uji prima n ≤ 10<sup>18</sup></h4><p>Miller-Rabin 12 basis.</p></div>
        <div class="pattern"><h4>Faktorkan n ≤ 10<sup>18</sup></h4><p>Miller-Rabin + Pollard Rho, rekursif.</p></div>
        <div class="pattern"><h4>Banyak pembagi n ≤ 10<sup>18</sup></h4><p>Faktorkan dengan rho, lalu Π (eᵢ + 1).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Hasil kali dua sisa < 10^18 bisa mencapai 10^36, melebihi 2^64 ≈ 1,8·10^19.">
        <p class="quiz-q">Mengapa butuh __int128 untuk mulmod dengan n ≈ 10<sup>18</sup>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar pembagian lebih cepat</button>
            <button class="quiz-option">Hasil kali dua bilangan &lt; 10<sup>18</sup> tidak muat di 64 bit</button>
            <button class="quiz-option">Karena n negatif</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Rho mencari pengulangan modulo faktor terkecil p ≤ √n; butuh sekitar √p ≤ n^(1/4) langkah.">
        <p class="quiz-q">Kira-kira berapa langkah Pollard Rho untuk n ≈ 10<sup>16</sup> dengan dua faktor prima seimbang?</p>
        <div class="quiz-options">
            <button class="quiz-option">sekitar 10<sup>4</sup></button>
            <button class="quiz-option">sekitar 10<sup>8</sup></button>
            <button class="quiz-option">sekitar 10<sup>16</sup></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Pada n prima tidak ada faktor nontrivial, sehingga gcd selalu 1 atau n dan loop tidak pernah selesai.">
        <p class="quiz-q">Apa yang terjadi jika Pollard Rho dipanggil pada bilangan prima?</p>
        <div class="quiz-options">
            <button class="quiz-option">Mengembalikan 1</button>
            <button class="quiz-option">Mengembalikan n</button>
            <button class="quiz-option">Tidak pernah menemukan faktor</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
