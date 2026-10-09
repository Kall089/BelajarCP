@php
    $mmSteps = [
        ['Semua jumlah subset dari satu paruh', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

// semua jumlah subset dari a[dari..sampai)
vector<long long> semuaJumlah(const vector<long long>& a, int dari, int sampai) {
    vector<long long> s = {0};                 // subset kosong
    for (int i = dari; i < sampai; i++) {
        int k = s.size();
        for (int j = 0; j < k; j++) s.push_back(s[j] + a[i]);   // versi "ambil a[i]"
    }
    return s;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Muatan Truk":</strong> ada n ≤ 40 kotak dengan berat w<sub>i</sub> ≤ 10<sup>9</sup> dan truk berkapasitas C. Pilih sebagian kotak sehingga total beratnya sebesar mungkin tetapi tidak melebihi C.</p>
<p>Knapsack DP tidak bisa (C sampai 10<sup>18</sup>), dan 2<sup>40</sup> ≈ 10<sup>12</sup> subset terlalu banyak. Tetapi 2<sup>20</sup> ≈ 10<sup>6</sup> masih ringan. Fungsi ini membangun daftar jumlah dengan menggandakan: setiap elemen baru menyalin semua jumlah lama lalu menambahkan dirinya.</p>
TXT],
        ['Baca input dan belah dua', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n;
    long long C;
    cin >> n >> C;
    vector<long long> w(n);
    for (auto& v : w) cin >> v;

    int h = n / 2;
    vector<long long> L = semuaJumlah(w, 0, h);    // 2^h jumlah
    vector<long long> R = semuaJumlah(w, h, n);    // 2^(n-h) jumlah
    sort(R.begin(), R.end());
CPP, <<<'TXT'
<p>Setiap subset dari seluruh array = (subset paruh kiri) + (subset paruh kanan). Jadi jawabannya adalah <em>pasangan</em> terbaik l ∈ L dan r ∈ R dengan l + r ≤ C.</p>
<p>R diurutkan agar untuk setiap l kita bisa mencari r terbesar yang masih muat dengan binary search.</p>
TXT],
        ['Gabungkan dengan binary search', <<<'CPP'

    long long best = 0;
    for (long long s : L) {
        if (s > C) continue;
        // r terbesar dengan r <= C - s; R[0] = 0 selalu memenuhi
        auto it = upper_bound(R.begin(), R.end(), C - s);
        best = max(best, s + *prev(it));
    }
    cout << best << "\n";
    return 0;
}
CPP, <<<'TXT'
<p><code>upper_bound</code> menunjuk elemen pertama yang <em>lebih besar</em> dari C − s, jadi elemen sebelumnya adalah yang terbesar yang masih ≤ C − s.</p>
<p>Total O(2<sup>n/2</sup> · n): membangun dan mengurutkan 2<sup>20</sup> bilangan, lalu 2<sup>20</sup> kali binary search. Untuk n = 40 sekitar 2·10<sup>7</sup> langkah.</p>
TXT],
    ];

    $mmPy = <<<'PY'
import sys
from bisect import bisect_right

def semua_jumlah(a):
    s = [0]
    for x in a:
        s += [v + x for v in s]
    return s

data = sys.stdin.buffer.read().split()
n, C = int(data[0]), int(data[1])
w = [int(v) for v in data[2:2 + n]]
h = n // 2
L = semua_jumlah(w[:h])
R = sorted(semua_jumlah(w[h:]))
best = 0
for s in L:
    if s <= C:
        k = bisect_right(R, C - s)        # R[0] = 0, jadi k >= 1
        best = max(best, s + R[k - 1])
print(best)
PY;

    $mmTwoPtr = <<<'CPP'
// Alternatif tanpa binary search: urutkan L naik dan R naik, lalu dua pointer
sort(L.begin(), L.end());
sort(R.begin(), R.end());
int j = (int)R.size() - 1;
long long best = 0;
for (long long s : L) {                       // s makin besar → pasangan r makin kecil
    while (j >= 0 && s + R[j] > C) j--;
    if (j < 0) break;
    best = max(best, s + R[j]);
}
CPP;

    $mmMerge = <<<'CPP'
// Membangun jumlah subset yang SUDAH terurut tanpa sort: gabungkan s dan s + a[i]
vector<long long> s = {0};
for (int i = dari; i < sampai; i++) {
    vector<long long> t(s.size());
    for (size_t j = 0; j < s.size(); j++) t[j] = s[j] + a[i];   // t juga terurut
    vector<long long> u(s.size() * 2);
    merge(s.begin(), s.end(), t.begin(), t.end(), u.begin());
    s.swap(u);
}
// total O(2^(n/2)) alih-alih O(2^(n/2) · n/2): berguna jika batas waktunya ketat
CPP;

    $mm4Sum = <<<'CPP'
// 4-sum: banyak (i, j, k, l) dengan A[i] + B[j] + C[k] + D[l] = 0, n ≤ 1000 per array
vector<long long> ab, cd;
for (long long x : A) for (long long y : B) ab.push_back(x + y);   // n^2 pasangan kiri
for (long long x : C) for (long long y : D) cd.push_back(x + y);   // n^2 pasangan kanan
sort(cd.begin(), cd.end());
long long cara = 0;
for (long long s : ab)
    cara += upper_bound(cd.begin(), cd.end(), -s) - lower_bound(cd.begin(), cd.end(), -s);
// O(n^2 log n) alih-alih O(n^4)
CPP;

    $mmBidir = <<<'CPP'
// Pencarian dua arah: BFS dari awal dan dari tujuan, masing-masing sampai kedalaman d/2.
// Jika setiap keadaan punya b langkah, kerjanya 2 · b^(d/2) alih-alih b^d.
// Contoh: puzzle geser, kubus rubik kecil, transformasi string dengan operasi yang bisa dibalik.
map<string, int> dariAwal = bfs(awal, d / 2);
map<string, int> dariAkhir = bfs(tujuan, d - d / 2);   // pakai operasi kebalikan
int terbaik = INF;
for (auto& kv : dariAwal) {
    auto it = dariAkhir.find(kv.first);
    if (it != dariAkhir.end()) terbaik = min(terbaik, kv.second + it->second);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali soal <strong>n ≈ 40</strong> yang terlalu besar untuk 2<sup>n</sup> tetapi pas untuk 2<sup>n/2</sup>.</li>
        <li>Membelah input, <strong>mengenumerasi</strong> setiap paruh, lalu <strong>menggabungkan</strong> dengan sort + binary search atau dua pointer.</li>
        <li>Memakai ide yang sama untuk <strong>4-sum</strong> (pasangan) dan pencarian dua arah.</li>
        <li>Menghitung biaya waktu dan memori dari setiap paruh.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'membelah pencarian', 'desc' => 'Dua pencarian kecil jauh lebih murah daripada satu pencarian besar.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Dua Regu Pencari">
    <h2>Intuisi: Bertemu di Tengah</h2>
    <div class="prose">
        <p>Dua orang ingin bertemu di sebuah terowongan panjang. Jika hanya satu orang yang berjalan, ia harus menempuh seluruh terowongan. Jika keduanya berjalan dari ujung masing-masing, mereka bertemu di tengah dan masing-masing hanya menempuh setengahnya.</p>
        <p>Dalam brute force, "panjang terowongan" adalah 2<sup>n</sup> kemungkinan. Membelah input menjadi dua paruh berukuran n/2 berarti dua kali 2<sup>n/2</sup>. Untuk n = 40, itu 2 · 10<sup>6</sup> alih-alih 10<sup>12</sup>.</p>
        <p>Syaratnya, jawaban dari seluruh input bisa disusun dari <em>satu hasil paruh kiri</em> dan <em>satu hasil paruh kanan</em>, dan kita bisa mencari pasangan yang cocok dengan cepat (biasanya dengan mengurutkan salah satu paruh).</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Enumerasi paruh</b><span>Daftar semua hasil (misalnya jumlah subset) dari setiap paruh: 2<sup>n/2</sup> buah.</span></div>
        <div class="term"><b>Penggabungan</b><span>Untuk setiap hasil kiri, cari pasangan kanan yang cocok: sort + binary search, dua pointer, atau hash map.</span></div>
        <div class="term"><b>Batas n</b><span>n ≤ 40–44 untuk subset; n ≤ 1000–4000 untuk 4-sum dengan pasangan.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Menghitung Subset dengan Target</h2>
    <div class="prose"><p>Masukkan array (paling banyak 8 elemen) dan target. Lihat semua jumlah subset dari dua paruh, lalu bagaimana setiap jumlah kiri mencari pasangannya di daftar kanan yang terurut.</p></div>
    <div data-viz="mitm"></div>
</section>

<section class="lesson-section" id="hitung" data-toc="Hitung Biayanya">
    <h2>Hitung Biayanya</h2>
    <table class="cx-table">
        <tr><th>n</th><th>2<sup>n</sup> (brute force)</th><th>2 · 2<sup>n/2</sup></th><th>Memori satu paruh (long long)</th></tr>
        <tr><td>20</td><td>10<sup>6</sup></td><td>2 · 10<sup>3</sup></td><td>8 KB</td></tr>
        <tr><td>30</td><td>10<sup>9</sup></td><td>6.5 · 10<sup>4</sup></td><td>256 KB</td></tr>
        <tr><td>40</td><td>10<sup>12</sup></td><td>2 · 10<sup>6</sup></td><td>8 MB</td></tr>
        <tr><td>46</td><td>7 · 10<sup>13</sup></td><td>1.7 · 10<sup>7</sup></td><td>64 MB</td></tr>
    </table>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Tanda soal meet in the middle: n sekitar 30–45 (terlalu besar untuk 2<sup>n</sup>, tetapi kecil sekali untuk algoritma polinomial), nilainya besar sehingga DP per nilai tidak mungkin.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'meet in the middle di C++', 'desc' => 'Subset sum dengan kapasitas besar, ditulis lengkap.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Muatan Truk</h2>
    @include('lessons.walkthrough', [
        'title' => 'Subset terbesar yang tidak melebihi C',
        'steps' => $mmSteps,
        'sample' => ['input' => "5 14\n7 11 4 9 6\n", 'output' => "13\n"],
        'py' => $mmPy,
    ])
    <div class="prose"><p>Jejak contoh: paruh kiri {7, 11}, paruh kanan {4, 9, 6}.</p></div>
    <table class="trace-table">
        <tr><th>s ∈ L</th><th>sisa C − s</th><th>r terbesar di R ≤ sisa</th><th>s + r</th></tr>
        <tr><td colspan="4">L = {0, 7, 11, 18}, R terurut = {0, 4, 6, 9, 10, 13, 15, 19}</td></tr>
        <tr class="hl"><td>0</td><td>14</td><td>13</td><td>13</td></tr>
        <tr class="hl"><td>7</td><td>7</td><td>6</td><td>13</td></tr>
        <tr><td>11</td><td>3</td><td>0</td><td>11</td></tr>
        <tr><td>18</td><td colspan="3">lebih dari C, lewati</td></tr>
        <tr class="ok"><td colspan="3">jawaban</td><td><b>13</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="variasi" data-toc="Dua Pointer & Merge">
    <h2>Dua Pointer dan Enumerasi Terurut</h2>
    <div class="prose"><p>Jika kedua daftar terurut, binary search bisa diganti dua pointer: saat s membesar, pasangan r yang masih muat hanya bisa mengecil.</p></div>
    @include('lessons.code', ['cpp' => $mmTwoPtr, 'js' => null, 'py' => null])
    <div class="prose"><p>Mengurutkan 2<sup>20</sup> bilangan butuh faktor log tambahan. Jika batas waktunya ketat, bangun daftar jumlah yang sudah terurut dengan <code>merge</code>: daftar lama dan daftar lama + a[i] masing-masing terurut.</p></div>
    @include('lessons.code', ['cpp' => $mmMerge, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Langkah</th><th>Waktu</th><th>Memori</th></tr>
        <tr><td>Enumerasi dua paruh</td><td><code>O(2<sup>n/2</sup>)</code></td><td><code>O(2<sup>n/2</sup>)</code></td></tr>
        <tr><td>Sort satu paruh</td><td><code>O(2<sup>n/2</sup> · n)</code></td><td>–</td></tr>
        <tr><td>Gabung (binary search)</td><td><code>O(2<sup>n/2</sup> · n)</code></td><td>–</td></tr>
        <tr><td>Gabung (dua pointer)</td><td><code>O(2<sup>n/2</sup>)</code></td><td>–</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Rekursi untuk enumerasi.</strong> Membangun daftar dengan rekursi tidak salah, tetapi cara menggandakan di atas lebih cepat dan tidak berisiko di bahasa yang batas rekursinya kecil.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Paruh tidak seimbang.</strong> Jika n ganjil, satu paruh mendapat ⌈n/2⌉. Urutkan paruh yang lebih besar dan iterasi paruh yang lebih kecil, atau sebaliknya; keduanya masih O(2<sup>n/2</sup> · n).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> 40 bilangan sampai 10<sup>9</sup> berjumlah sampai 4 · 10<sup>10</sup>: wajib <code>long long</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'meet in the middle di luar subset', 'desc' => '4-sum, pencarian dua arah, dan pola lainnya.'])

<section class="lesson-section" id="pola" data-toc="Pola Lanjutan">
    <h2>Pola Lanjutan</h2>
    <div class="prose"><p>Idenya tidak terbatas pada subset. Setiap kali jawaban adalah gabungan dua bagian yang bisa dienumerasi terpisah, kita bisa bertemu di tengah. Contoh klasik: 4-sum. Brute force O(n<sup>4</sup>); dengan memasangkan dua array pertama dan dua array terakhir, setiap "paruh" berisi n<sup>2</sup> jumlah.</p></div>
    @include('lessons.code', ['cpp' => $mm4Sum, 'js' => null, 'py' => null])
    <div class="prose"><p>Di graph keadaan, pencarian dua arah (bidirectional BFS) adalah meet in the middle: jelajahi setengah kedalaman dari awal, setengah dari tujuan, lalu cari keadaan yang dicapai keduanya.</p></div>
    @include('lessons.code', ['cpp' => $mmBidir, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Subset sum, n ≤ 40</h4><p>Jumlah tepat X, terdekat ke X, atau terbesar ≤ C.</p></div>
        <div class="pattern"><h4>Bagi dua seadil mungkin</h4><p>Beri tanda ± pada setiap elemen; cari jumlah yang paling dekat ke 0.</p></div>
        <div class="pattern"><h4>4-sum / k-sum</h4><p>Pasangkan menjadi dua kelompok, lalu hitung pasangan yang cocok.</p></div>
        <div class="pattern"><h4>XOR subset</h4><p>Simpan hasil XOR paruh kiri di hash map, cari pasangan dari paruh kanan.</p></div>
        <div class="pattern"><h4>Pencarian dua arah</h4><p>Puzzle dengan langkah yang bisa dibalik dan kedalaman jawaban kecil.</p></div>
        <div class="pattern"><h4>Baby-step giant-step</h4><p>Logaritma diskret a<sup>x</sup> ≡ b (mod p) dalam O(√p): dibahas di track Teori Bilangan.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap paruh berisi 20 elemen, jadi 2^20 ≈ 10^6 jumlah. Dua paruh ≈ 2 · 10^6, ditambah faktor log untuk sort dan binary search.">
        <p class="quiz-q">n = 40. Berapa kira-kira banyak jumlah subset yang dienumerasi meet in the middle?</p>
        <div class="quiz-options">
            <button class="quiz-option">2<sup>40</sup> ≈ 10<sup>12</sup></button>
            <button class="quiz-option">2 · 2<sup>20</sup> ≈ 2 · 10<sup>6</sup></button>
            <button class="quiz-option">40<sup>2</sup> = 1600</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Daftar R diurutkan agar untuk setiap s di L kita bisa menemukan pasangan yang cocok (misalnya r terbesar ≤ C − s) dengan binary search, bukan memeriksa semua r.">
        <p class="quiz-q">Mengapa salah satu paruh diurutkan?</p>
        <div class="quiz-options">
            <button class="quiz-option">Supaya pasangan untuk setiap s bisa dicari dengan binary search</button>
            <button class="quiz-option">Supaya jumlahnya tidak overflow</button>
            <button class="quiz-option">Supaya subset kosong tidak terhitung</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="A+B memberi n² = 10^6 jumlah, C+D juga. Urutkan salah satunya dan binary search: O(n² log n), sekitar 2 · 10^7 langkah.">
        <p class="quiz-q">4-sum dengan empat array berukuran 1000. Berapa kompleksitas pendekatan pasangan?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(n<sup>4</sup>)</button>
            <button class="quiz-option">O(n<sup>3</sup>)</button>
            <button class="quiz-option">O(n<sup>2</sup> log n)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
