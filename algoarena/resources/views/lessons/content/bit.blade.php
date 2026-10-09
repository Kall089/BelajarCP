@php
    $btSteps = [
        ['Baca barang', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<long long> w(n);
    long long total = 0;
    for (auto& x : w) {
        cin >> x;
        total += x;
    }

CPP, <<<'TXT'
<p>N barang (N ≤ 20) harus dibagi ke dua kelompok, A dan B, agar selisih total beratnya sekecil mungkin. Setiap barang punya dua pilihan, jadi ada 2<sup>N</sup> cara membagi. Untuk N = 20 itu sekitar sejuta: cukup dicoba semua.</p>
TXT],
        ['Setiap mask adalah satu pembagian', <<<'CPP'
    long long terbaik = LLONG_MAX;
    for (int mask = 0; mask < (1 << n); mask++) {
        long long a = 0;
        for (int i = 0; i < n; i++)
            if (mask >> i & 1) a += w[i];
        terbaik = min(terbaik, llabs(total - 2 * a));
    }

CPP, <<<'TXT'
<p>Bilangan <code>mask</code> dari 0 sampai 2<sup>N</sup> − 1 mewakili setiap himpunan bagian: bit ke-i bernilai 1 berarti barang i masuk kelompok A. <code>mask &gt;&gt; i &amp; 1</code> mengambil bit ke-i (operator &gt;&gt; lebih kuat daripada &amp;, jadi tidak perlu kurung).</p>
<p>Jika A berbobot a, maka B berbobot total − a, dan selisihnya |total − 2a|. Total O(2<sup>N</sup> · N) ≈ 2 · 10<sup>7</sup> untuk N = 20.</p>
TXT],
        ['Cetak hasil', <<<'CPP'
    cout << terbaik << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Tanpa rekursi dan tanpa backtracking: perulangan biasa sudah mencoba semua pembagian. Untuk N sampai 40, ide yang sama dipakai dua kali dengan <strong>meet in the middle</strong> (level 3).</p>
TXT],
    ];

    $btPy = <<<'PY'
n = int(input())
w = list(map(int, input().split()))
total = sum(w)
# jumlah setiap subset dihitung dari subset tanpa bit terendahnya: O(2^n)
jumlah = [0] * (1 << n)
terbaik = total
for mask in range(1, 1 << n):
    low = mask & -mask
    jumlah[mask] = jumlah[mask ^ low] + w[low.bit_length() - 1]
    terbaik = min(terbaik, abs(total - 2 * jumlah[mask]))
print(terbaik)
PY;

    $btOps = <<<'CPP'
int x = 44;                          // 101100 (biner)
bool ada  = x >> 3 & 1;              // bit ke-3 menyala?  -> 1
x |= 1 << 0;                         // nyalakan bit 0     -> 101101 = 45
x &= ~(1 << 2);                      // matikan bit 2      -> 101001 = 41
x ^= 1 << 5;                         // balik bit 5        -> 001001 = 9
int satu   = __builtin_popcount(x);  // banyak bit 1       -> 2
int rendah = x & -x;                 // bit 1 terendah     -> 1
int tinggi = __lg(x);                // indeks bit 1 tertinggi -> 3
bool pangkatDua = x > 0 && (x & (x - 1)) == 0;   // 9 bukan pangkat 2 -> false
long long besar = 1LL << 40;         // tanpa LL, 1 << 40 meluap (int hanya 32 bit)
CPP;

    $btSub = <<<'CPP'
// Semua submask dari m (himpunan bagian dari himpunan m), dari besar ke kecil.
for (int s = m; s > 0; s = (s - 1) & m) {
    // proses s
}
// proses juga s = 0 bila perlu.
// Untuk SEMUA m sekaligus, total langkahnya 3^n (setiap elemen: di luar m, di m tapi tidak di s, di s).
// n = 15 -> 3^15 ≈ 1,4 · 10^7, masih cepat. Sering dipakai di DP bitmask: dp[m] dari dp[s] + dp[m ^ s].
CPP;

    $btContrib = <<<'CPP'
// Jumlah (a[i] AND a[j]) untuk semua pasangan i < j, N sampai 2 · 10^5.
// Bit b menyumbang 2^b untuk setiap pasangan yang KEDUANYA punya bit b.
long long hasil = 0;
for (int b = 0; b < 30; b++) {
    long long c = 0;
    for (int i = 0; i < n; i++) c += a[i] >> b & 1;
    hasil += c * (c - 1) / 2 * (1LL << b);
}
// Untuk XOR: pasangan dengan bit b berbeda = c * (n - c).  Untuk OR: semua pasangan - pasangan keduanya 0.
CPP;

    $btMitm = <<<'CPP'
// Meet in the middle: adakah subset dari N ≤ 40 bilangan yang jumlahnya tepat X?
// 2^40 terlalu banyak, tetapi 2 × 2^20 masih ringan.
vector<long long> semuaJumlah(const vector<long long>& v) {
    int k = v.size();
    vector<long long> s(1 << k, 0);
    for (int mask = 1; mask < (1 << k); mask++) {
        int i = __builtin_ctz(mask);                  // indeks bit terendah
        s[mask] = s[mask & (mask - 1)] + v[i];        // dari subset tanpa bit itu
    }
    return s;
}

bool bisa(const vector<long long>& a, long long X) {
    vector<long long> kiri(a.begin(), a.begin() + a.size() / 2);
    vector<long long> kanan(a.begin() + a.size() / 2, a.end());
    vector<long long> L = semuaJumlah(kiri), R = semuaJumlah(kanan);
    sort(R.begin(), R.end());
    for (long long x : L)                                     // pasangkan setiap jumlah kiri
        if (binary_search(R.begin(), R.end(), X - x)) return true;   // dengan pasangan kanannya
    return false;
}
CPP;

    $btBitset = <<<'CPP'
// Subset sum: jumlah apa saja yang bisa dibentuk? bitset menggeser 64 bit sekaligus.
bitset<100001> bisa;
bisa[0] = 1;
for (int x : w) bisa |= bisa << x;      // ambil x atau tidak
// bisa[s] == 1 jika ada subset berjumlah s. Waktu O(N · S / 64).
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Membaca dan mengubah bit dengan operator &amp;, |, ^, ~, &lt;&lt;, &gt;&gt;.</li>
        <li>Mencoba semua himpunan bagian dengan perulangan <code>mask</code>, termasuk semua submask.</li>
        <li>Menghitung jumlah AND/OR/XOR semua pasangan dengan <strong>kontribusi per bit</strong>.</li>
        <li>Memakai <strong>meet in the middle</strong> untuk N sampai 40 dan <code>bitset</code> untuk subset sum.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'bilangan sebagai deretan saklar', 'desc' => 'Operator bit dan trik yang sering muncul.'])

<section class="lesson-section" id="ide" data-toc="Operator Bit">
    <h2>Operator Bit</h2>
    <div class="prose">
        <p>Komputer menyimpan bilangan bulat dalam basis 2. Bilangan 44 adalah 101100<sub>2</sub> = 32 + 8 + 4. Operator bit bekerja pada setiap posisi sekaligus, sehingga sangat cepat, dan memungkinkan satu bilangan menyimpan sebuah <strong>himpunan</strong> kecil: bit ke-i menyala berarti elemen i ada di himpunan.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>a &amp; b</b><span>Irisan: bit 1 jika keduanya 1.</span></div>
        <div class="term"><b>a | b</b><span>Gabungan: bit 1 jika salah satu 1.</span></div>
        <div class="term"><b>a ^ b</b><span>Beda simetris: bit 1 jika berbeda. x ^ x = 0.</span></div>
        <div class="term"><b>~a</b><span>Komplemen: semua bit dibalik.</span></div>
        <div class="term"><b>1 &lt;&lt; k</b><span>2<sup>k</sup>, himpunan berisi elemen k saja.</span></div>
        <div class="term"><b>a &gt;&gt; k &amp; 1</b><span>Bit ke-k dari a.</span></div>
    </div>
    @include('lessons.code', ['cpp' => $btOps, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Mode <strong>Operator</strong> memperlihatkan setiap operasi bit demi bit. Mode <strong>Semua subset</strong> menelusuri mask dari 0 sampai 2<sup>n</sup> − 1 dan menandai subset yang jumlahnya tepat K.</p></div>
    <div data-viz="bits"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'mencoba semua himpunan bagian', 'desc' => 'Satu perulangan menggantikan rekursi pilih/tidak.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Bagi Dua Seimbang</h2>
    <div class="prose"><p>Input: N (≤ 20) dan berat N barang. Bagi semua barang ke dua kelompok; cetak selisih total berat terkecil.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Enumerasi subset dengan bitmask',
        'steps' => $btSteps,
        'sample' => ['input' => "4\n7 3 2 9\n", 'output' => "1\n"],
        'py' => $btPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Berat 7, 3, 2, 9 (barang 0..3), total 21. Beberapa mask:</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>mask</th><th>Biner (barang 3..0)</th><th>Kelompok A</th><th>a</th><th>|21 − 2a|</th></tr>
            <tr><td>0</td><td>0000</td><td>–</td><td>0</td><td>21</td></tr>
            <tr><td>3</td><td>0011</td><td>7, 3</td><td>10</td><td>1</td></tr>
            <tr><td>5</td><td>0101</td><td>7, 2</td><td>9</td><td>3</td></tr>
            <tr class="ok"><td>12</td><td>1100</td><td>2, 9</td><td>11</td><td>1</td></tr>
            <tr><td>15</td><td>1111</td><td>semua</td><td>21</td><td>21</td></tr>
        </table>
    </div>
    <div class="prose"><p>Total 21 ganjil, jadi selisih 0 mustahil; 1 adalah jawaban terbaik. Perhatikan mask 3 dan 12 saling melengkapi (3 ^ 15 = 12): setiap pembagian muncul dua kali.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Teknik</th><th>Waktu</th><th>Batas N yang wajar</th></tr>
        <tr><td>Semua subset</td><td><code>O(2<sup>N</sup> · N)</code></td><td>N ≤ 20</td></tr>
        <tr><td>Semua submask dari semua mask</td><td><code>O(3<sup>N</sup>)</code></td><td>N ≤ 15–16</td></tr>
        <tr><td>Meet in the middle</td><td><code>O(2<sup>N/2</sup> · N)</code></td><td>N ≤ 40</td></tr>
        <tr><td>Kontribusi per bit</td><td><code>O(N · log V)</code></td><td>N sampai 10<sup>6</sup></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Prioritas operator.</strong> <code>x &amp; 1 == 0</code> dibaca <code>x &amp; (1 == 0)</code> karena == lebih kuat daripada &amp;. Selalu beri kurung: <code>(x &amp; 1) == 0</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Geser melebihi 31 bit.</strong> <code>1 &lt;&lt; 40</code> adalah perilaku tak terdefinisi untuk int. Pakai <code>1LL &lt;&lt; 40</code> dan <code>__builtin_popcountll</code> untuk long long.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'teknik lanjutan', 'desc' => 'Submask, kontribusi per bit, meet in the middle, dan bitset.'])

<section class="lesson-section" id="submask" data-toc="Submask">
    <h2>Enumerasi Submask</h2>
    @include('lessons.code', ['cpp' => $btSub, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kontribusi" data-toc="Kontribusi per Bit">
    <h2>Kontribusi per Bit</h2>
    <div class="prose"><p>AND, OR, dan XOR bekerja pada setiap bit secara terpisah. Jadi jumlah hasil operasi untuk semua pasangan bisa dihitung bit demi bit: untuk setiap bit b, cukup tahu berapa bilangan yang bit b-nya menyala.</p></div>
    @include('lessons.code', ['cpp' => $btContrib, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="mitm" data-toc="Meet in the Middle">
    <h2>Meet in the Middle</h2>
    <div class="prose"><p>Bagi barang menjadi dua kelompok berisi N/2. Daftar semua jumlah subset di setiap kelompok (2 × 2<sup>N/2</sup>), lalu pasangkan: subset total = subset kiri + subset kanan. Urutkan salah satu daftar agar pasangan bisa dicari dengan binary search atau two pointers.</p></div>
    @include('lessons.code', ['cpp' => $btMitm, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="bitset" data-toc="bitset">
    <h2>bitset untuk Subset Sum</h2>
    @include('lessons.code', ['cpp' => $btBitset, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>N ≤ 20, pilih sebagian</h4><p>Perulangan mask 0..2<sup>N</sup>−1.</p></div>
        <div class="pattern"><h4>N ≤ 40</h4><p>Meet in the middle.</p></div>
        <div class="pattern"><h4>Jumlah AND/OR/XOR pasangan</h4><p>Kontribusi per bit.</p></div>
        <div class="pattern"><h4>Maksimalkan AND/XOR</h4><p>Tentukan bit dari yang tertinggi (greedy per bit, atau trie biner).</p></div>
        <div class="pattern"><h4>Subset sum dengan S ≤ 10<sup>5</sup></h4><p>bitset, O(N·S/64).</p></div>
        <div class="pattern"><h4>DP atas himpunan</h4><p>Lihat materi DP Bitmask.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="12 = 1100, 10 = 1010: AND = 1000 = 8, XOR = 0110 = 6.">
        <p class="quiz-q">Berapa 12 ^ 10?</p>
        <div class="quiz-options">
            <button class="quiz-option">8</button>
            <button class="quiz-option">14</button>
            <button class="quiz-option">6</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="2^20 ≈ 10^6 subset, dan setiap subset dijumlahkan dengan memeriksa 20 bit: sekitar 2 · 10^7 langkah.">
        <p class="quiz-q">N = 20 barang. Berapa kira-kira langkah untuk mencoba semua subset dan menjumlahkan beratnya?</p>
        <div class="quiz-options">
            <button class="quiz-option">2 · 10<sup>7</sup></button>
            <button class="quiz-option">10<sup>12</sup></button>
            <button class="quiz-option">400</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Pasangan yang bit b-nya berbeda: satu dari c bilangan yang menyala dan satu dari n − c yang mati, jadi c · (n − c).">
        <p class="quiz-q">Dari n bilangan, c di antaranya punya bit b menyala. Berapa pasangan yang XOR-nya punya bit b menyala?</p>
        <div class="quiz-options">
            <button class="quiz-option">c · (c − 1) / 2</button>
            <button class="quiz-option">c · (n − c)</button>
            <button class="quiz-option">n − c</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
