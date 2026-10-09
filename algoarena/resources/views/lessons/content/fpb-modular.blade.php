@php
    $fmSteps = [
        ['FPB dengan algoritma Euclid', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

long long fpb(long long a, long long b) {
    while (b != 0) {
        long long r = a % b;
        a = b;
        b = r;
    }
    return a;
}

CPP, <<<'TXT'
<p>Kunci Euclid: <code>FPB(a, b) = FPB(b, a mod b)</code>. Setiap pembagi bersama a dan b juga membagi <code>a − q·b = a mod b</code>, dan sebaliknya, jadi himpunan pembagi bersamanya tidak berubah. Ulangi sampai b = 0; saat itu FPB = a.</p>
<p>Setiap dua langkah, bilangan yang lebih besar paling sedikit menjadi setengahnya, sehingga banyak langkah O(log min(a, b)). Di C++17 ada <code>std::gcd</code>, dan GCC menyediakan <code>__gcd</code>.</p>
TXT],
        ['Pangkat cepat modulo m', <<<'CPP'
long long pangkat(long long a, long long e, long long m) {
    long long hasil = 1 % m;
    a %= m;
    while (e > 0) {
        if (e & 1) hasil = hasil * a % m;
        a = a * a % m;
        e >>= 1;
    }
    return hasil;
}

CPP, <<<'TXT'
<p>Tulis pangkat e dalam biner. <code>a</code> berturut-turut menjadi a<sup>1</sup>, a<sup>2</sup>, a<sup>4</sup>, a<sup>8</sup>, … (dikuadratkan setiap langkah), dan <code>hasil</code> dikalikan hanya dengan pangkat yang bit-nya 1. Contoh 3<sup>13</sup> = 3<sup>8</sup> · 3<sup>4</sup> · 3<sup>1</sup> karena 13 = 1101₂. Hanya O(log e) perkalian, bahkan untuk e = 10<sup>18</sup>.</p>
<div class="wt-tip"><code>1 % m</code> menangani m = 1 (semua bilangan ≡ 0). Hasil kali dua bilangan &lt; m harus muat di <code>long long</code>: aman untuk m ≤ 3 · 10<sup>9</sup>; untuk m sampai 10<sup>18</sup> pakai <code>__int128</code>.</div>
TXT],
        ['Euclid diperluas dan invers modular', <<<'CPP'
long long fpbDiperluas(long long a, long long b, long long& x, long long& y) {
    if (b == 0) {
        x = 1;
        y = 0;
        return a;
    }
    long long x1, y1;
    long long g = fpbDiperluas(b, a % b, x1, y1);
    x = y1;
    y = x1 - (a / b) * y1;
    return g;
}

long long invers(long long a, long long m) {
    long long x, y;
    long long g = fpbDiperluas(a % m, m, x, y);
    if (g != 1) return -1;
    return ((x % m) + m) % m;
}

CPP, <<<'TXT'
<p><code>fpbDiperluas</code> mencari x, y dengan <code>a·x + b·y = FPB(a, b)</code>. Jika rekursi untuk (b, a mod b) memberi <code>b·x₁ + (a mod b)·y₁ = g</code>, substitusikan a mod b = a − ⌊a/b⌋·b, lalu kelompokkan suku a dan b: x = y₁, y = x₁ − ⌊a/b⌋·y₁.</p>
<p><strong>Invers</strong> a modulo m adalah bilangan x dengan a·x ≡ 1 (mod m). Dari <code>a·x + m·y = 1</code>, ambil modulo m: a·x ≡ 1. Invers hanya ada jika FPB(a, m) = 1; x bisa negatif, jadi dinormalkan ke 0..m−1.</p>
TXT],
        ['Program utama', <<<'CPP'
int main() {
    int q;
    cin >> q;
    while (q--) {
        int jenis;
        cin >> jenis;
        if (jenis == 1) {
            long long a, b;
            cin >> a >> b;
            long long g = fpb(a, b);
            cout << g << ' ' << a / g * b << '\n';
        } else if (jenis == 2) {
            long long a, e, m;
            cin >> a >> e >> m;
            cout << pangkat(a, e, m) << '\n';
        } else {
            long long a, m;
            cin >> a >> m;
            cout << invers(a, m) << '\n';
        }
    }
    return 0;
}
CPP, <<<'TXT'
<p>Tiga jenis pertanyaan: <code>1 a b</code> mencetak FPB dan KPK, <code>2 a e m</code> mencetak a<sup>e</sup> mod m, <code>3 a m</code> mencetak invers a modulo m (−1 jika tidak ada).</p>
<p>KPK dihitung sebagai <code>a / g * b</code>, bukan <code>a * b / g</code>: pembagian dulu mencegah overflow sementara (a·b bisa 10<sup>18</sup> walaupun KPK-nya kecil).</p>
TXT],
    ];

    $fmPy = <<<'PY'
import sys
from math import gcd

def invers(a, m):
    # Euclid diperluas iteratif: pertahankan a·x ≡ r (mod m)
    r0, r1, x0, x1 = a % m, m, 1, 0
    while r1:
        q = r0 // r1
        r0, r1 = r1, r0 - q * r1
        x0, x1 = x1, x0 - q * x1
    return x0 % m if r0 == 1 else -1

data = sys.stdin.read().split()
q = int(data[0])
p = 1
out = []
for _ in range(q):
    jenis = data[p]
    if jenis == "1":
        a, b = int(data[p + 1]), int(data[p + 2]); p += 3
        g = gcd(a, b)
        out.append(f"{g} {a // g * b}")
    elif jenis == "2":
        a, e, m = int(data[p + 1]), int(data[p + 2]), int(data[p + 3]); p += 4
        out.append(str(pow(a, e, m)))
    else:
        a, m = int(data[p + 1]), int(data[p + 2]); p += 3
        out.append(str(invers(a, m)))
print("\n".join(out))
PY;

    $fmFermat = <<<'CPP'
// Jika p PRIMA dan a tidak habis dibagi p: a^(p−1) ≡ 1 (mod p)   (teorema kecil Fermat)
// maka a · a^(p−2) ≡ 1, artinya invers a = a^(p−2) mod p.
const long long MOD = 1e9 + 7;
long long inversPrima(long long a) { return pangkat(a, MOD - 2, MOD); }
// Pembagian modulo: (x / y) mod p = x · inversPrima(y) mod p
CPP;

    $fmDioph = <<<'CPP'
// a·x + b·y = c punya solusi bulat  <=>  g = FPB(a, b) membagi c.
long long x, y;
long long g = fpbDiperluas(a, b, x, y);          // a·x + b·y = g
if (c % g != 0) { /* tidak ada solusi */ }
x *= c / g;                                      // sekarang a·x + b·y = c
y *= c / g;
// Semua solusi: x + k·(b/g), y − k·(a/g) untuk k bulat.
long long langkah = b / g;
long long xMin = ((x % langkah) + langkah) % langkah;   // x tak negatif terkecil
long long yPasangan = (c - a * xMin) / b;
// 6x + 10y = 14  ->  x = 4, y = −1
CPP;

    $fmCrt = <<<'CPP'
// Chinese Remainder Theorem untuk dua kongruensi:
//   x ≡ r1 (mod m1),  x ≡ r2 (mod m2)
// Tulis x = r1 + m1·k. Butuh m1·k ≡ r2 − r1 (mod m2).
long long p, q;
long long g = fpbDiperluas(m1, m2, p, q);        // m1·p + m2·q = g
if ((r2 - r1) % g != 0) { /* tidak ada solusi */ }
long long m2g = m2 / g;
long long k = (__int128)((r2 - r1) / g) % m2g * (p % m2g) % m2g;
if (k < 0) k += m2g;
long long kpk = m1 * m2g;
long long xKecil = (r1 + (__int128)m1 * k) % kpk;   // solusi terkecil 0 ≤ x < KPK(m1, m2)
// x ≡ 2 (mod 3), x ≡ 3 (mod 5)  ->  8
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menghitung FPB dengan algoritma Euclid dan KPK tanpa overflow.</li>
        <li>Bekerja dengan aritmetika modulo: penjumlahan, pengurangan, perkalian, dan pangkat cepat.</li>
        <li>Mencari invers modular dengan teorema kecil Fermat dan Euclid diperluas.</li>
        <li>Menyelesaikan persamaan Diophantine linear dan sistem dua kongruensi (CRT).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'FPB dan KPK', 'desc' => 'Algoritma Euclid dan gambarannya sebagai ubin.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Ubin Persegi Terbesar</h2>
    <div class="prose">
        <p>Lantai berukuran 42 × 30 ingin ditutup ubin persegi yang semuanya sama besar, tanpa memotong ubin. Sisi ubin harus membagi 42 dan juga membagi 30, dan kita ingin yang terbesar: itulah <strong>FPB</strong> (faktor persekutuan terbesar), di sini 6.</p>
        <p><strong>KPK</strong> (kelipatan persekutuan terkecil) adalah kebalikannya: dua lampu berkedip setiap 42 dan 30 detik akan berkedip bersamaan lagi setelah KPK(42, 30) = 210 detik. Keduanya terhubung oleh <code>FPB(a, b) · KPK(a, b) = a · b</code>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>a mod b</b><span>Sisa pembagian a oleh b. 42 mod 30 = 12.</span></div>
        <div class="term"><b>Saling prima</b><span>FPB(a, b) = 1, misalnya 8 dan 15.</span></div>
        <div class="term"><b>Kongruen</b><span><code>a ≡ b (mod m)</code> jika a dan b bersisa sama saat dibagi m.</span></div>
        <div class="term"><b>Invers modular</b><span>x dengan a·x ≡ 1 (mod m): "pembagian" dalam dunia modulo.</span></div>
    </div>
</section>

<section class="lesson-section" id="euclid" data-toc="Algoritma Euclid">
    <h2>Algoritma Euclid</h2>
    <div class="prose">
        <p>Dari lantai 42 × 30, potong persegi 30 × 30 sebesar mungkin. Sisanya 12 × 30. Ubin yang menutup lantai awal juga harus menutup sisa ini, dan sebaliknya. Jadi FPB(42, 30) = FPB(30, 12). Ulangi: FPB(30, 12) = FPB(12, 6) = FPB(6, 0) = 6.</p>
        <p>Dalam bahasa bilangan: <code>FPB(a, b) = FPB(b, a mod b)</code>, dan <code>FPB(a, 0) = a</code>. Untuk bilangan sampai 10<sup>18</sup>, Euclid hanya butuh kurang dari 90 langkah.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Setiap langkah memotong persegi terbesar dari persegi panjang yang tersisa. Di akhir, garis putus-putus memperlihatkan ubin FPB × FPB menutup seluruh lantai. Bagian kedua menelusuri tabel dari bawah ke atas untuk mencari x dan y Euclid diperluas.</p></div>
    <div data-viz="euclid"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'aritmetika modular', 'desc' => 'Sifat modulo, pangkat cepat, dan invers.'])

<section class="lesson-section" id="modulo" data-toc="Sifat Modulo">
    <h2>Bekerja dalam Modulo</h2>
    <div class="prose">
        <p>Jawaban yang sangat besar biasanya diminta modulo 10<sup>9</sup> + 7. Kita tidak perlu menghitung bilangan aslinya, cukup sisa baginya, karena:</p>
    </div>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Aturan</th><th>Catatan</th></tr>
        <tr><td>Tambah</td><td><code>(a + b) mod m = ((a mod m) + (b mod m)) mod m</code></td><td>Aman.</td></tr>
        <tr><td>Kurang</td><td><code>((a − b) mod m + m) mod m</code></td><td>Di C++ <code>%</code> bisa negatif; tambahkan m.</td></tr>
        <tr><td>Kali</td><td><code>(a · b) mod m = ((a mod m) · (b mod m)) mod m</code></td><td>Hasil kali dua bilangan &lt; m harus muat di long long.</td></tr>
        <tr><td>Bagi</td><td><code>a / b → a · b<sup>−1</sup> mod m</code></td><td>Tidak boleh langsung dibagi; pakai invers.</td></tr>
    </table>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Langkah</th><th>e (biner)</th><th>Bit terakhir</th><th>a = 3<sup>2<sup>k</sup></sup> mod 1000</th><th>hasil</th></tr>
            <tr><td>1</td><td>1101</td><td>1</td><td>3</td><td>3</td></tr>
            <tr><td>2</td><td>110</td><td>0</td><td>9</td><td>3</td></tr>
            <tr><td>3</td><td>11</td><td>1</td><td>81</td><td>3 · 81 = 243</td></tr>
            <tr class="ok"><td>4</td><td>1</td><td>1</td><td>6561 mod 1000 = 561</td><td>243 · 561 mod 1000 = <b>323</b></td></tr>
        </table>
    </div>
    <div class="prose"><p>Tabel di atas menghitung 3<sup>13</sup> mod 1000 dengan pangkat cepat: hanya 4 putaran, bukan 13 perkalian.</p></div>
</section>

<section class="lesson-section" id="invers" data-toc="Invers Modular">
    <h2>Invers Modular</h2>
    <div class="prose">
        <p>Ada dua cara mencari invers a modulo m:</p>
        <ul>
            <li><strong>Fermat</strong>, jika m prima (misalnya 10<sup>9</sup> + 7): invers = a<sup>m−2</sup> mod m. Satu panggilan pangkat cepat.</li>
            <li><strong>Euclid diperluas</strong>, untuk m sembarang asalkan FPB(a, m) = 1.</li>
        </ul>
    </div>
    @include('lessons.code', ['cpp' => $fmFermat, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kotak Perkakas Bilangan</h2>
    @include('lessons.walkthrough', [
        'title' => 'FPB, pangkat cepat, invers',
        'steps' => $fmSteps,
        'sample' => ['input' => "5\n1 42 30\n2 3 13 1000\n2 2 100 1000000007\n3 3 11\n3 6 9\n", 'output' => "6 210\n323\n976371285\n4\n-1\n"],
        'py' => $fmPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Waktu</th></tr>
        <tr><td>FPB / KPK / Euclid diperluas</td><td><code>O(log min(a, b))</code></td></tr>
        <tr><td>Pangkat cepat a<sup>e</sup></td><td><code>O(log e)</code></td></tr>
        <tr><td>Invers (Fermat atau Euclid)</td><td><code>O(log m)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Modulo negatif.</strong> Di C++, <code>-7 % 3 == -1</code>. Gunakan <code>((x % m) + m) % m</code> setiap kali x bisa negatif. Python selalu memberi hasil non-negatif.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow perkalian.</strong> Jika m sampai 10<sup>18</sup>, <code>a * b</code> bisa mencapai 10<sup>36</sup>. Pakai <code>(__int128)a * b % m</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Fermat hanya untuk modulus prima</strong>, dan a tidak boleh kelipatan m. Untuk m = 10 (bukan prima), a<sup>m−2</sup> bukan invers; gunakan Euclid diperluas.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'Diophantine & CRT', 'desc' => 'Menyelesaikan persamaan bulat dan sistem kongruensi.'])

<section class="lesson-section" id="diophantine" data-toc="Persamaan Diophantine">
    <h2>Persamaan Diophantine Linear</h2>
    <div class="prose">
        <p>Timbangan hanya punya batu 6 kg dan 10 kg (boleh diletakkan di kedua sisi). Bisakah menimbang tepat 14 kg? Artinya mencari bilangan bulat x, y dengan <code>6x + 10y = 14</code>. Karena 6x + 10y selalu kelipatan FPB(6, 10) = 2, solusi ada <em>jika dan hanya jika</em> FPB membagi c. Euclid diperluas memberi satu solusi, dan semua solusi lain berselisih kelipatan (b/g, −a/g).</p>
    </div>
    @include('lessons.code', ['cpp' => $fmDioph, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="crt" data-toc="Chinese Remainder Theorem">
    <h2>Chinese Remainder Theorem</h2>
    <div class="prose">
        <p>Bilangan berapa yang bersisa 2 jika dibagi 3, dan bersisa 3 jika dibagi 5? Jawabannya 8, dan semua jawaban lain berselisih kelipatan 15 = KPK(3, 5). Secara umum dua kongruensi bisa digabung menjadi satu kongruensi modulo KPK, atau ternyata tidak punya solusi jika sisanya bertentangan (misalnya x ≡ 1 mod 4 dan x ≡ 2 mod 6: yang satu ganjil, yang lain genap).</p>
    </div>
    @include('lessons.code', ['cpp' => $fmCrt, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Kapan bersamaan lagi?</h4><p>Kejadian periodik: KPK. Banyak periode: KPK berantai, awas overflow.</p></div>
        <div class="pattern"><h4>Potongan terbesar</h4><p>Bagi beberapa panjang menjadi potongan sama tanpa sisa: FPB semuanya.</p></div>
        <div class="pattern"><h4>Pangkat raksasa</h4><p>a<sup>b</sup> mod m dengan b sampai 10<sup>18</sup>: pangkat cepat.</p></div>
        <div class="pattern"><h4>Pembagian modulo</h4><p>Peluang, rata-rata, C(n, k): kalikan dengan invers.</p></div>
        <div class="pattern"><h4>Kombinasi dua langkah</h4><p>Bisa mencapai c dengan langkah ±a, ±b? Ya jika FPB(a, b) | c.</p></div>
        <div class="pattern"><h4>Jadwal berulang</h4><p>Beberapa syarat sisa bagi: CRT, digabung satu per satu.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="FPB(84, 36) = FPB(36, 12) = FPB(12, 0) = 12.">
        <p class="quiz-q">Berapa FPB(84, 36)?</p>
        <div class="quiz-options">
            <button class="quiz-option">6</button>
            <button class="quiz-option">12</button>
            <button class="quiz-option">36</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="FPB(4, 8) = 4 ≠ 1, sehingga 4x mod 8 hanya bisa 0 atau 4, tidak pernah 1.">
        <p class="quiz-q">Invers 4 modulo 8 adalah …</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">4<sup>6</sup> mod 8</button>
            <button class="quiz-option">Tidak ada</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Pangkat cepat mengalikan sekali per bit dari e, sehingga sekitar log₂(10^18) ≈ 60 putaran.">
        <p class="quiz-q">Berapa kira-kira banyak putaran pangkat cepat untuk e = 10<sup>18</sup>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Sekitar 60</button>
            <button class="quiz-option">Sekitar 10<sup>9</sup></button>
            <button class="quiz-option">Sekitar 10<sup>18</sup></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
