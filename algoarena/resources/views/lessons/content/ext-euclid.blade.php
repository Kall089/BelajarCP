@php
    $eeSteps = [
        ['Extended Euclid rekursif', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

// mengembalikan g = gcd(a, b) dan mengisi x, y sehingga a*x + b*y = g
long long extgcd(long long a, long long b, long long& x, long long& y) {
    if (b == 0) {
        x = 1;
        y = 0;
        return a;
    }
    long long x1, y1;
    long long g = extgcd(b, a % b, x1, y1);   // b*x1 + (a mod b)*y1 = g
    x = y1;
    y = x1 - (a / b) * y1;                    // susun ulang menjadi a*x + b*y = g
    return g;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Invers Modulo Sembarang":</strong> untuk t pasangan (a, m), cetak invers a modulo m di rentang [0, m), atau −1 jika tidak ada. Berbeda dari materi sebelumnya, m <em>tidak</em> harus prima, jadi Fermat tidak bisa dipakai.</p>
<p>Dari b·x<sub>1</sub> + (a mod b)·y<sub>1</sub> = g dan a mod b = a − ⌊a/b⌋·b, kita dapat a·y<sub>1</sub> + b·(x<sub>1</sub> − ⌊a/b⌋·y<sub>1</sub>) = g. Itulah dua baris "naik" di atas.</p>
TXT],
        ['Invers dari Bezout', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int t;
    cin >> t;
    while (t--) {
        long long a, m, x, y;
        cin >> a >> m;
        long long g = extgcd(a % m, m, x, y);   // (a mod m)*x + m*y = g
        if (g != 1) {
            cout << -1 << "\n";                 // invers ada hanya jika gcd(a, m) = 1
            continue;
        }
        cout << ((x % m) + m) % m << "\n";     // x bisa negatif: rapikan ke [0, m)
    }
    return 0;
}
CPP, <<<'TXT'
<p>Jika a·x + m·y = 1, maka modulo m suku m·y hilang: a·x ≡ 1. Jadi x adalah inversnya. Jika gcd(a, m) = g &gt; 1, setiap a·x + m·y habis dibagi g sehingga tidak mungkin sama dengan 1: tidak ada invers.</p>
<p>Kompleksitas O(log m) per pertanyaan.</p>
TXT],
    ];

    $eePy = <<<'PY'
import sys

def extgcd(a, b):
    # versi iteratif: mengembalikan (g, x, y) dengan a*x + b*y = g
    x0, y0, x1, y1 = 1, 0, 0, 1
    while b:
        q = a // b
        a, b = b, a - q * b
        x0, x1 = x1, x0 - q * x1
        y0, y1 = y1, y0 - q * y1
    return a, x0, y0

data = sys.stdin.buffer.read().split()
t = int(data[0])
out = []
for i in range(t):
    a, m = int(data[1 + 2 * i]), int(data[2 + 2 * i])
    g, x, _ = extgcd(a % m, m)
    out.append(str(x % m) if g == 1 else "-1")
print("\n".join(out))
PY;

    $eeDio = <<<'CPP'
// Persamaan Diophantine linear a*x + b*y = c
long long x0, y0;
long long g = extgcd(a, b, x0, y0);
if (c % g != 0) { /* tidak ada solusi bulat */ }
x0 *= c / g;  y0 *= c / g;                 // satu solusi (awas overflow untuk c besar)
// SEMUA solusi: x = x0 + k*(b/g),  y = y0 - k*(a/g),  k bilangan bulat
// x terkecil yang tak negatif: ((x0 % (b/g)) + b/g) % (b/g)
CPP;

    $eeCrt = <<<'CPP'
__extension__ typedef __int128 i128;      // 128-bit untuk hasil kali sementara

// Gabungkan x ≡ r1 (mod m1) dan x ≡ r2 (mod m2), modulus boleh tidak koprima.
// Mengembalikan {r, lcm(m1, m2)} atau {-1, -1} jika tidak konsisten.
pair<long long, long long> gabung(long long r1, long long m1, long long r2, long long m2) {
    long long p, q;
    long long g = extgcd(m1, m2, p, q);          // m1*p + m2*q = g
    if ((r2 - r1) % g != 0) return {-1, -1};
    long long m2g = m2 / g;
    // x = r1 + m1*t dengan m1*t ≡ r2 - r1 (mod m2)  →  t ≡ (r2-r1)/g * p (mod m2/g)
    long long t = (long long)((i128)((r2 - r1) / g % m2g + m2g) % m2g * ((p % m2g + m2g) % m2g) % m2g);
    long long l = m1 / g * m2;
    long long r = (long long)(((i128)m1 * t + r1) % l);
    return {(r + l) % l, l};
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mencari x, y dengan <strong>ax + by = gcd(a, b)</strong> memakai extended Euclid.</li>
        <li>Menghitung <strong>invers modulo</strong> untuk modulus apa pun (tidak harus prima).</li>
        <li>Menyelesaikan <strong>persamaan Diophantine linear</strong> dan menuliskan semua solusinya.</li>
        <li>Menggabungkan kongruensi dengan <strong>Chinese Remainder Theorem</strong>, termasuk modulus yang tidak koprima.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'koefisien dari Euclid', 'desc' => 'Euclid tidak hanya memberi FPB, tetapi juga cara menyusunnya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Dua Gelas Ukur">
    <h2>Intuisi: Dua Gelas Ukur</h2>
    <div class="prose">
        <p>Kamu punya gelas ukur 240 ml dan 46 ml, dan boleh menuang atau membuang isi gelas penuh berkali-kali. Volume apa saja yang bisa kamu dapatkan? Ternyata tepat kelipatan gcd(240, 46) = 2 ml. <strong>Identitas Bezout</strong> menjamin ada bilangan bulat x, y dengan 240x + 46y = 2, dan extended Euclid menemukannya: 240·(−9) + 46·47 = 2.</p>
        <p>Koefisien x dan y ini adalah alat serbaguna: invers modulo, persamaan Diophantine, dan Chinese Remainder Theorem semuanya dibangun di atasnya.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Bezout</b><span>Untuk a, b tidak keduanya nol, ada x, y bulat dengan ax + by = gcd(a, b).</span></div>
        <div class="term"><b>Diophantine linear</b><span>ax + by = c punya solusi bulat ⇔ gcd(a, b) membagi c.</span></div>
        <div class="term"><b>Invers modulo m</b><span>Ada ⇔ gcd(a, m) = 1; nilainya x dari ax + my = 1.</span></div>
        <div class="term"><b>CRT</b><span>Sistem x ≡ rᵢ (mod mᵢ) punya solusi tunggal modulo KPK semua mᵢ (jika konsisten).</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Turun lalu Naik</h2>
    <div class="prose"><p>Fase turun adalah Euclid biasa. Fase naik membangun x dan y dari baris bawah ke atas. Di Mode Tebak, hitung sendiri y = x' − (a/b)·y'.</p></div>
    <div data-viz="ext-gcd"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'extended Euclid di C++', 'desc' => 'Invers untuk modulus apa pun, Diophantine, dan CRT dua persamaan.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Invers Modulo Sembarang</h2>
    @include('lessons.walkthrough', [
        'title' => 'Invers dengan extended Euclid',
        'steps' => $eeSteps,
        'sample' => ['input' => "3\n3 7\n4 6\n17 3120\n", 'output' => "5\n-1\n2753\n"],
        'py' => $eePy,
    ])
</section>

<section class="lesson-section" id="dio" data-toc="Diophantine & CRT">
    <h2>Persamaan Diophantine dan CRT</h2>
    <div class="prose"><p>Dari satu solusi (x<sub>0</sub>, y<sub>0</sub>), semua solusi didapat dengan menggeser x sebanyak kelipatan b/g dan y ke arah sebaliknya sebanyak a/g: a·(b/g) − b·(a/g) = 0, jadi jumlahnya tetap c.</p></div>
    @include('lessons.code', ['cpp' => $eeDio, 'js' => null, 'py' => null])
    <div class="prose"><p>CRT: "bilangan yang bersisa 2 jika dibagi 3, bersisa 3 jika dibagi 5, dan bersisa 2 jika dibagi 7" (jawabannya 23). Gabungkan dua kongruensi sekaligus, lalu ulangi dengan kongruensi berikutnya.</p></div>
    @include('lessons.code', ['cpp' => $eeCrt, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow di CRT.</strong> m<sub>1</sub>·t bisa mencapai KPK ≈ 10<sup>18</sup> dan hasil kali sementara jauh lebih besar. Pakai <code>__int128</code> (di GCC/Clang) untuk perkalian sementara, lalu ambil modulo.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>x negatif.</strong> Koefisien dari extended Euclid bisa negatif. Rapikan dengan <code>((x % m) + m) % m</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'bukti dan batas koefisien', 'desc' => 'Mengapa koefisiennya kecil dan kapan CRT konsisten.'])

<section class="lesson-section" id="bukti" data-toc="Bukti & Pola">
    <h2>Bukti dan Pola</h2>
    <div class="proof">
        <p><strong>Batas koefisien.</strong> Extended Euclid menghasilkan |x| ≤ b/g dan |y| ≤ a/g. Jadi untuk a, b ≤ 10<sup>18</sup>, koefisiennya masih muat di <code>long long</code>; yang meluap adalah perkalian dengan c/g, bukan x itu sendiri.</p>
        <p><strong>Konsistensi CRT.</strong> x ≡ r<sub>1</sub> (mod m<sub>1</sub>) dan x ≡ r<sub>2</sub> (mod m<sub>2</sub>) punya solusi ⇔ r<sub>1</sub> ≡ r<sub>2</sub> (mod gcd(m<sub>1</sub>, m<sub>2</sub>)). Arah "hanya jika": x − r<sub>1</sub> dan x − r<sub>2</sub> sama-sama habis dibagi g, jadi selisihnya juga. Arah "jika": persamaan m<sub>1</sub>t ≡ r<sub>2</sub> − r<sub>1</sub> (mod m<sub>2</sub>) adalah Diophantine yang syaratnya persis itu.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Invers, m tidak prima</h4><p>Extended Euclid, ada ⇔ gcd = 1.</p></div>
        <div class="pattern"><h4>Banyak solusi tak negatif</h4><p>Batasi k dari x ≥ 0 dan y ≥ 0, lalu hitung bilangan bulat di rentang k.</p></div>
        <div class="pattern"><h4>Jadwal berulang</h4><p>"Bus A tiap 12 menit mulai menit 5, bus B tiap 18 menit…" = CRT.</p></div>
        <div class="pattern"><h4>Modulus komposit</h4><p>Pecah m = Π pᵢ^eᵢ, hitung modulo setiap bagian, gabungkan dengan CRT.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="gcd(6, 9) = 3 tidak membagi 7, jadi 6x + 9y = 7 tidak punya solusi bulat.">
        <p class="quiz-q">Persamaan mana yang TIDAK punya solusi bulat?</p>
        <div class="quiz-options">
            <button class="quiz-option">6x + 9y = 12</button>
            <button class="quiz-option">6x + 9y = 7</button>
            <button class="quiz-option">5x + 3y = 1</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="gcd(4, 6) = 2 ≠ 1, jadi 4x ≡ 1 (mod 6) tidak punya solusi: 4x selalu genap.">
        <p class="quiz-q">Invers 4 modulo 6…</p>
        <div class="quiz-options">
            <button class="quiz-option">tidak ada</button>
            <button class="quiz-option">4</button>
            <button class="quiz-option">5</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="23 = 7·3 + 2 = 4·5 + 3 = 3·7 + 2. Solusinya tunggal modulo 3·5·7 = 105.">
        <p class="quiz-q">x ≡ 2 (mod 3), x ≡ 3 (mod 5), x ≡ 2 (mod 7). Solusi terkecil?</p>
        <div class="quiz-options">
            <button class="quiz-option">8</button>
            <button class="quiz-option">53</button>
            <button class="quiz-option">23</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
