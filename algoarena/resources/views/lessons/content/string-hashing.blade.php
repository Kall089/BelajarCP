@php
    $shSteps = [
        ['Dua hash sekaligus', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long M1 = 1000000007, M2 = 1000000009;
const long long B1 = 131, B2 = 137;
vector<long long> h1, h2, p1, p2;

CPP, <<<'TXT'
<p>Kita memakai dua pasang (basis, modulus) sekaligus: <strong>double hashing</strong>. Dua string dianggap sama hanya jika kedua hash-nya sama. Peluang dua string berbeda bertabrakan di satu modulus sekitar 1/M ≈ 10<sup>−9</sup> per perbandingan; dengan dua modulus sekitar 10<sup>−18</sup>.</p>
<p>Basis harus lebih besar dari nilai karakter terbesar (di sini kode ASCII, sampai 127) agar setiap string pendek punya "angka" yang berbeda.</p>
TXT],
        ['Prefix hash dan pangkat basis', <<<'CPP'
void siapkan(const string& s) {
    int n = s.size();
    h1.assign(n + 1, 0); h2.assign(n + 1, 0);
    p1.assign(n + 1, 1); p2.assign(n + 1, 1);
    for (int i = 0; i < n; i++) {
        h1[i + 1] = (h1[i] * B1 + s[i]) % M1;
        h2[i + 1] = (h2[i] * B2 + s[i]) % M2;
        p1[i + 1] = p1[i] * B1 % M1;
        p2[i + 1] = p2[i] * B2 % M2;
    }
}

CPP, <<<'TXT'
<p><code>h[i]</code> adalah hash i karakter pertama, dibangun seperti membaca bilangan desimal digit demi digit: kalikan basis, tambahkan karakter baru. <code>p[i] = B<sup>i</sup></code> disiapkan sekalian. Persiapan O(n).</p>
TXT],
        ['Hash sebuah potongan dalam O(1)', <<<'CPP'
pair<long long, long long> ambil(int l, int r) {
    long long a = ((h1[r] - h1[l] * p1[r - l]) % M1 + M1) % M1;
    long long b = ((h2[r] - h2[l] * p2[r - l]) % M2 + M2) % M2;
    return {a, b};
}

CPP, <<<'TXT'
<p>Hash s[l..r−1] = <code>h[r] − h[l] · B<sup>r−l</sup></code>. Bayangkan bilangan desimal 31415: "415" = 31415 − 31 · 10<sup>3</sup>. Hasil pengurangan modulo bisa negatif, jadi ditambah M lalu dimodulo lagi.</p>
TXT],
        ['Menjawab pertanyaan', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    int q;
    cin >> s >> q;
    siapkan(s);

    string out;
    while (q--) {
        int a, b, c, d;
        cin >> a >> b >> c >> d;
        bool sama = (b - a == d - c) && ambil(a - 1, b) == ambil(c - 1, d);
        out += sama ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Pertanyaan "apakah s[a..b] = s[c..d]?" (1-based, inklusif). Panjang berbeda langsung TIDAK. Selain itu bandingkan pasangan hash: O(1) per pertanyaan, dibandingkan O(panjang) jika membandingkan huruf demi huruf.</p>
TXT],
    ];

    $shPy = <<<'PY'
import sys
data = sys.stdin.read().split()
s, q = data[0], int(data[1])
M1, M2, B1, B2 = 10**9 + 7, 10**9 + 9, 131, 137
n = len(s)
h1 = [0] * (n + 1); h2 = [0] * (n + 1)
p1 = [1] * (n + 1); p2 = [1] * (n + 1)
for i, ch in enumerate(s):
    c = ord(ch)
    h1[i + 1] = (h1[i] * B1 + c) % M1
    h2[i + 1] = (h2[i] * B2 + c) % M2
    p1[i + 1] = p1[i] * B1 % M1
    p2[i + 1] = p2[i] * B2 % M2

def ambil(l, r):
    return ((h1[r] - h1[l] * p1[r - l]) % M1, (h2[r] - h2[l] * p2[r - l]) % M2)

out = []
for t in range(q):
    a, b, c, d = map(int, data[2 + 4 * t: 6 + 4 * t])
    sama = b - a == d - c and ambil(a - 1, b) == ambil(c - 1, d)
    out.append("YA" if sama else "TIDAK")
print("\n".join(out))
PY;

    $sh61 = <<<'CPP'
// Modulus 2^61 − 1 (bilangan prima Mersenne): satu hash sudah sangat aman,
// tetapi perkaliannya butuh __int128.
const unsigned long long MOD = (1ULL << 61) - 1;
unsigned long long kaliMod(unsigned long long a, unsigned long long b) {
    __uint128_t c = (__uint128_t)a * b;
    unsigned long long hasil = (unsigned long long)(c & MOD) + (unsigned long long)(c >> 61);
    return hasil >= MOD ? hasil - MOD : hasil;
}
// Basis acak (misalnya dari jam) mencegah lawan membuat tes "anti-hash" khusus untukmu.
mt19937_64 rng(chrono::steady_clock::now().time_since_epoch().count());
unsigned long long BASIS = rng() % (MOD - 1000) + 500;
CPP;

    $shPal = <<<'CPP'
// Palindrom: s[l..r] sama dengan kebalikannya.
// Simpan juga hash dari string terbalik t = reverse(s). Potongan s[l..r] terbalik adalah
// t[n-1-r .. n-1-l], jadi cukup bandingkan dua hash.
bool palindrom(int l, int r) {                    // 0-based, inklusif
    return hashMaju(l, r + 1) == hashMundur(n - 1 - r, n - l);
}
CPP;

    $shLcs = <<<'CPP'
// Potongan bersama terpanjang dari A dan B (|A|, |B| sampai 10^5):
// jika ada potongan bersama sepanjang L, pasti ada juga yang sepanjang L − 1  -> monoton!
bool ada(int L) {
    unordered_set<unsigned long long> lihat;      // semua hash potongan A sepanjang L
    for (int i = 0; i + L <= (int)A.size(); i++) lihat.insert(hashA(i, i + L));
    for (int j = 0; j + L <= (int)B.size(); j++)
        if (lihat.count(hashB(j, j + L))) return true;
    return false;
}
// binary search L terbesar dengan ada(L) == true: O((|A| + |B|) log)
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengubah string menjadi bilangan dengan <strong>hash polinomial</strong>.</li>
        <li>Menghitung hash potongan mana pun dalam O(1) dengan <strong>prefix hash</strong>.</li>
        <li>Menilai risiko tabrakan dan memakai double hashing atau modulus 2<sup>61</sup> − 1.</li>
        <li>Memakai hash untuk palindrom, potongan berbeda, dan binary search pada panjang.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'string sebagai bilangan', 'desc' => 'Hash polinomial dan prefix hash.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Sidik Jari String</h2>
    <div class="prose">
        <p>Membandingkan dua string sepanjang 10<sup>5</sup> huruf butuh 10<sup>5</sup> langkah. Jika harus melakukannya 10<sup>5</sup> kali, totalnya 10<sup>10</sup>. Bagaimana kalau setiap string punya "sidik jari" berupa satu bilangan, sehingga membandingkan cukup O(1)?</p>
        <p>Baca string sebagai bilangan dalam basis B: "abc" ↦ a·B² + b·B + c. String berbeda memberi bilangan berbeda, tetapi bilangannya terlalu besar, jadi kita ambil sisa baginya terhadap modulus M yang besar. Sekarang dua string berbeda <em>bisa</em> memberi hash yang sama (tabrakan), tetapi dengan M ≈ 10<sup>9</sup> peluangnya sangat kecil.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Hash sama tidak menjamin string sama, tetapi hash berbeda menjamin string berbeda.</strong> Hashing menukar kepastian dengan kecepatan dan kesederhanaan. Untuk tugas yang punya algoritma pasti (seperti KMP), boleh pilih yang mana pun; untuk banyak tugas lain, hashing jauh lebih mudah ditulis.</p>
    </div>
</section>

<section class="lesson-section" id="prefix" data-toc="Prefix Hash">
    <h2>Prefix Hash</h2>
    <div class="prose">
        <p>Seperti prefix sum, simpan hash setiap awalan: <code>h[i + 1] = h[i] · B + s[i]</code>. Hash potongan s[l..r−1] diperoleh dari dua awalan:</p>
        <p style="text-align:center"><code>hash(s[l..r−1]) = h[r] − h[l] · B<sup>r−l</sup> (mod M)</code></p>
        <p>Dalam desimal: dari 31415, potongan "415" = 31415 − 31 · 1000. Suku <code>h[l]</code> harus digeser sejauh panjang potongan sebelum dikurangkan.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Modulus di sini sengaja kecil (1009) agar angkanya terbaca. Coba bandingkan potongan yang berbeda; dengan modulus sekecil ini kamu mungkin menemukan tabrakan, yang hampir tidak pernah terjadi dengan modulus ≈ 10<sup>9</sup> ganda.</p></div>
    <div data-viz="hash"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Membandingkan banyak pasangan potongan dengan double hashing.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Membandingkan potongan dengan hash',
        'steps' => $shSteps,
        'sample' => ['input' => "abracadabra\n4\n1 4 8 11\n1 3 4 6\n2 2 9 9\n1 5 7 11\n", 'output' => "YA\nTIDAK\nYA\nTIDAK\n"],
        'py' => $shPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Waktu</th></tr>
        <tr><td>Siapkan prefix hash</td><td><code>O(n)</code></td></tr>
        <tr><td>Hash satu potongan / bandingkan dua potongan</td><td><code>O(1)</code></td></tr>
        <tr><td>Potongan berbeda sepanjang L</td><td><code>O(n log n)</code> (sort hash) atau <code>O(n)</code> rata-rata (hash set)</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Paradoks ulang tahun.</strong> Membandingkan satu pasangan, peluang tabrakan ≈ 1/M. Tetapi jika kamu memasukkan 10<sup>5</sup> hash ke satu himpunan, ada sekitar 10<sup>10</sup> pasangan, dan dengan M ≈ 10<sup>9</sup> tabrakan hampir pasti terjadi. Gunakan double hash atau modulus 2<sup>61</sup> − 1.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Modulus 2<sup>64</sup> (overflow alami unsigned)</strong> tampak menggoda, tetapi ada string terkenal (barisan Thue–Morse) yang membuatnya bertabrakan berapa pun basisnya. Hindari.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Nilai karakter 0.</strong> Jika 'a' bernilai 0, maka "a", "aa", "aaa" semuanya ber-hash 0. Mulai dari 1 (atau pakai kode ASCII).</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'hash yang lebih kuat & penerapan', 'desc' => 'Modulus 2^61 − 1, palindrom, dan binary search pada panjang.'])

<section class="lesson-section" id="kuat" data-toc="Hash yang Lebih Kuat">
    <h2>Modulus 2<sup>61</sup> − 1 dan Basis Acak</h2>
    <div class="prose"><p>Satu hash modulo prima 2<sup>61</sup> − 1 sudah cukup aman untuk hampir semua soal, dan lebih cepat daripada dua hash. Di platform yang memperbolehkan peserta lain "meretas" solusimu, pilih basis secara acak saat program berjalan.</p></div>
    @include('lessons.code', ['cpp' => $sh61, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="palindrom" data-toc="Palindrom">
    <h2>Palindrom dengan Hash Maju dan Mundur</h2>
    <div class="prose"><p>Potongan adalah palindrom jika sama dengan kebalikannya. Siapkan prefix hash untuk s dan untuk s yang dibalik; setiap pertanyaan palindrom menjadi satu perbandingan O(1). Dikombinasikan dengan binary search, ini juga menemukan palindrom terpanjang di sekitar setiap pusat dalam O(n log n).</p></div>
    @include('lessons.code', ['cpp' => $shPal, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="binser" data-toc="Binary Search + Hash">
    <h2>Binary Search pada Panjang</h2>
    <div class="prose"><p>Banyak pertanyaan "terpanjang" bersifat monoton: jika ada potongan bersama sepanjang L, pasti ada yang sepanjang L − 1 (potong saja satu huruf). Binary search L, dan periksa setiap L dengan hash dalam waktu linear.</p></div>
    @include('lessons.code', ['cpp' => $shLcs, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Sama atau tidak?</h4><p>Bandingkan hash dua potongan, O(1).</p></div>
        <div class="pattern"><h4>Potongan berbeda</h4><p>Masukkan hash semua potongan sepanjang L ke himpunan.</p></div>
        <div class="pattern"><h4>Palindrom</h4><p>Hash maju dan mundur.</p></div>
        <div class="pattern"><h4>Terpanjang yang …</h4><p>Binary search panjang + hash.</p></div>
        <div class="pattern"><h4>Urutan leksikografis</h4><p>Bandingkan dua potongan: cari awalan bersama terpanjang dengan binary search + hash, lalu lihat huruf berikutnya.</p></div>
        <div class="pattern"><h4>Grid / 2D</h4><p>Hash setiap baris, lalu hash kolom dari hash baris: mencari pola persegi.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Hash berbeda menjamin string berbeda, tetapi hash sama hanya berarti hampir pasti sama (bisa tabrakan).">
        <p class="quiz-q">Hash dua potongan sama. Kesimpulannya …</p>
        <div class="quiz-options">
            <button class="quiz-option">Pasti sama</button>
            <button class="quiz-option">Hampir pasti sama, kecuali terjadi tabrakan</button>
            <button class="quiz-option">Pasti berbeda</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Awalan h[l] harus digeser sejauh panjang potongan, r − l, sebelum dikurangkan dari h[r].">
        <p class="quiz-q">Rumus hash s[l..r−1] adalah …</p>
        <div class="quiz-options">
            <button class="quiz-option">h[r] − h[l]</button>
            <button class="quiz-option">h[r] · B<sup>l</sup> − h[l]</button>
            <button class="quiz-option">h[r] − h[l] · B<sup>r−l</sup></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Dengan banyak hash sekaligus, banyaknya pasangan yang mungkin bertabrakan sangat besar (paradoks ulang tahun).">
        <p class="quiz-q">Mengapa satu modulus 10<sup>9</sup> + 7 berbahaya saat menyimpan 10<sup>5</sup> hash dalam satu himpunan?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena ada sekitar 10<sup>10</sup> pasangan, sehingga tabrakan hampir pasti</button>
            <button class="quiz-option">Karena modulus itu bukan prima</button>
            <button class="quiz-option">Karena hash tidak bisa disimpan di himpunan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
