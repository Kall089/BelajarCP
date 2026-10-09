@php
    $mgSteps = [
        ['Penampung global', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

vector<long long> a, tmp;
long long inv = 0;
CPP, <<<'TXT'
<p><strong>Soal contoh "Hitung Inversi":</strong> diberikan n bilangan. Inversi adalah pasangan (i, j) dengan i &lt; j tetapi a[i] &gt; a[j]. Hitung banyaknya. n sampai 2 · 10<sup>5</sup>, jadi O(n²) terlalu lambat.</p>
<p><code>tmp</code> dibuat sekali di luar fungsi. Membuat vector baru di setiap pemanggilan rekursif memang benar, tetapi jauh lebih lambat.</p>
TXT],
        ['Pecah dan urutkan dua bagian', <<<'CPP'

// urutkan a[l, r) sambil menghitung inversi di dalamnya
void mergeSort(int l, int r) {
    if (r - l <= 1) return;
    int m = l + (r - l) / 2;
    mergeSort(l, m);
    mergeSort(m, r);
CPP, <<<'TXT'
<p>Konvensi <strong>setengah terbuka [l, r)</strong>: panjangnya r − l, dan dua bagiannya [l, m) dan [m, r) tanpa ±1 yang membingungkan. Rentang berisi ≤ 1 elemen sudah urut dan tidak punya inversi.</p>
<p>Setelah dua panggilan rekursif, inversi <em>di dalam</em> masing-masing bagian sudah terhitung. Yang tersisa: inversi <strong>lintas</strong>, dengan i di kiri dan j di kanan.</p>
TXT],
        ['Gabungkan sambil menghitung', <<<'CPP'
    int i = l, j = m, k = l;
    while (i < m && j < r) {
        if (a[i] <= a[j]) {
            tmp[k++] = a[i++];
        } else {
            inv += m - i;          // a[i], a[i+1], ..., a[m-1] semuanya > a[j]
            tmp[k++] = a[j++];
        }
    }
    while (i < m) tmp[k++] = a[i++];
    while (j < r) tmp[k++] = a[j++];
    for (int t = l; t < r; t++) a[t] = tmp[t];
}
CPP, <<<'TXT'
<p>Kedua bagian sudah urut. Saat a[j] (kanan) diambil lebih dulu karena a[i] &gt; a[j], maka <strong>semua</strong> sisa elemen kiri a[i..m−1] juga &gt; a[j] (karena kiri urut menaik). Itulah <code>m − i</code> inversi sekaligus, bukan hanya 1.</p>
<p>Tanda <code>&lt;=</code> penting: elemen sama diambil dari kiri lebih dulu (merge sort jadi <strong>stabil</strong>), dan pasangan bernilai sama tidak dihitung sebagai inversi.</p>
TXT],
        ['Program utama', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    a.resize(n);
    tmp.resize(n);
    for (auto& x : a) cin >> x;
    mergeSort(0, n);
    cout << inv << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Kedalaman rekursi log<sub>2</sub> n ≈ 18, setiap tingkat memproses n elemen: total O(n log n). Banyak inversi bisa mencapai n(n−1)/2 ≈ 2 · 10<sup>10</sup>: pakai <code>long long</code>.</p>
TXT],
    ];

    $mgPy = <<<'PY'
import sys
def hitung(a):
    # merge sort iteratif (bottom-up) agar aman dari batas rekursi
    n = len(a)
    inv = 0
    lebar = 1
    while lebar < n:
        b = []
        for l in range(0, n, 2 * lebar):
            m, r = min(l + lebar, n), min(l + 2 * lebar, n)
            i, j = l, m
            while i < m and j < r:
                if a[i] <= a[j]:
                    b.append(a[i]); i += 1
                else:
                    inv += m - i
                    b.append(a[j]); j += 1
            b.extend(a[i:m]); b.extend(a[j:r])
        a = b
        lebar *= 2
    return inv
data = sys.stdin.read().split()
n = int(data[0])
print(hitung(list(map(int, data[1:1 + n]))))
PY;

    $mgDC = <<<'CPP'
// Kerangka umum divide & conquer
Hasil selesaikan(rentang) {
    if (rentang kecil) return jawaban langsung;
    pecah rentang menjadi kiri dan kanan;
    Hasil A = selesaikan(kiri);
    Hasil B = selesaikan(kanan);
    return gabungkan(A, B);       // di sinilah "kecerdikan" soalnya
}
// Waktu: T(n) = 2T(n/2) + O(biaya gabung). Gabung O(n) → O(n log n).
CPP;

    $mgTwoPhase = <<<'CPP'
// Varian: hitung pasangan i < j dengan a[i] > 2 * a[j]
// Syaratnya BUKAN syarat urutan merge, jadi hitung dalam fase TERPISAH sebelum merge
int j = m;
for (int i = l; i < m; i++) {
    while (j < r && a[i] > 2LL * a[j]) j++;   // two pointers: j hanya maju
    cnt += j - m;                              // a[m..j-1] memenuhi untuk a[i]
}
// ... lalu merge biasa
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan pola <strong>divide and conquer</strong>: pecah, selesaikan, gabungkan.</li>
        <li>Menulis merge sort dengan konvensi [l, r) dan langkah merge yang benar dan stabil.</li>
        <li>Menghitung <strong>inversi</strong> dalam O(n log n), dan mengerti mengapa <code>m − i</code>, bukan 1.</li>
        <li>Mengenali inversi yang menyamar (tukar bersebelahan minimum) dan varian "dua fase".</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'pecah, taklukkan, gabungkan', 'desc' => 'Ide merge sort dan langkah penggabungan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Dua Tumpukan Urut">
    <h2>Intuisi: Menggabung Dua Tumpukan yang Sudah Urut</h2>
    <div class="prose">
        <p>Dua guru masing-masing sudah mengurutkan tumpukan kertas ujian kelasnya. Untuk menggabungkannya, kamu cukup membandingkan dua kertas teratas, mengambil yang lebih kecil, dan mengulang. Setiap kertas hanya disentuh sekali: menggabung itu <strong>linear</strong>.</p>
        <p>Merge sort memakai ide ini secara rekursif: pecah array menjadi dua, urutkan masing-masing (dengan cara yang sama), lalu gabungkan. Array berisi satu elemen sudah urut dengan sendirinya.</p>
    </div>
    @include('lessons.code', ['cpp' => $mgDC, 'js' => null, 'py' => null])
    <div class="recurrence"><small>Waktu</small>T(n) = 2 · T(n / 2) + O(n)   →   O(n log n)
(log n tingkat pemecahan, setiap tingkat menggabung total n elemen)</div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Merge Sort + Inversi</h2>
    <div class="prose"><p>Bagian kiri berwarna biru muda, kanan ungu. Setiap kali elemen kanan diambil lebih dulu, perhatikan berapa elemen kiri yang "dilompati": itulah tambahan inversinya. Mode Tebak menanyakan angka itu.</p></div>
    <div data-viz="merge-sort"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: Satu Langkah Merge</h2>
    <div class="prose"><p>Kiri = [2, 5, 8], kanan = [1, 3, 9].</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Bandingkan</th><th>Ambil</th><th>Inversi baru</th><th>Pasangan</th></tr>
            <tr class="hl"><td>2 vs 1</td><td>1 (kanan)</td><td>+3</td><td>(2,1), (5,1), (8,1)</td></tr>
            <tr><td>2 vs 3</td><td>2 (kiri)</td><td>0</td><td>–</td></tr>
            <tr class="hl"><td>5 vs 3</td><td>3 (kanan)</td><td>+2</td><td>(5,3), (8,3)</td></tr>
            <tr><td>5 vs 9</td><td>5</td><td>0</td><td>–</td></tr>
            <tr><td>8 vs 9</td><td>8</td><td>0</td><td>–</td></tr>
            <tr class="ok"><td>sisa</td><td>9</td><td>total 5</td><td></td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'merge sort di C++', 'desc' => 'Program lengkap untuk menghitung inversi.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Hitung Inversi</h2>
    @include('lessons.walkthrough', [
        'title' => 'Merge sort + inversi',
        'steps' => $mgSteps,
        'sample' => ['input' => "8\n5 2 8 1 9 3 7 4\n", 'output' => "13\n"],
        'py' => $mgPy,
    ])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th><th>Memori</th></tr>
        <tr><td>Periksa semua pasangan</td><td><code>O(n²)</code></td><td><code>O(1)</code></td></tr>
        <tr><td>Merge sort</td><td><code>O(n log n)</code></td><td><code>O(n)</code> untuk tmp</td></tr>
        <tr><td>Fenwick tree (materi Struktur Data)</td><td><code>O(n log n)</code></td><td><code>O(n)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong><code>inv += 1</code> alih-alih <code>m − i</code></strong>: bug klasik yang lolos sampel kecil tetapi gagal di tes besar. Uji dengan stress test melawan versi O(n²).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong><code>&lt;</code> alih-alih <code>&lt;=</code></strong> di merge membuat elemen sama diambil dari kanan dulu: pasangan bernilai sama ikut terhitung sebagai inversi, dan sort tidak stabil lagi.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow mid.</strong> <code>(l + r) / 2</code> bisa meluap pada indeks besar; biasakan <code>l + (r − l) / 2</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'inversi yang menyamar', 'desc' => 'Tukar bersebelahan dan syarat yang bukan syarat urutan.'])

<section class="lesson-section" id="menyamar" data-toc="Inversi yang Menyamar">
    <h2>Inversi yang Menyamar</h2>
    <div class="proof">
        <p><strong>Klaim:</strong> banyak tukar <em>bersebelahan</em> minimum untuk mengurutkan array = banyak inversi.</p>
        <p>Menukar dua elemen bersebelahan yang terbalik mengurangi inversi tepat 1 (pasangan lain tidak berubah posisi relatifnya). Array urut punya 0 inversi, jadi butuh paling sedikit I tukar. Bubble sort hanya menukar pasangan bersebelahan yang terbalik, jadi tepat I tukar: batas itu tercapai.</p>
    </div>
    <div class="prose"><p>Jika syaratnya bukan sekadar "a[i] &gt; a[j]" (misalnya a[i] &gt; 2·a[j]), pasangan yang memenuhi tidak lagi selaras dengan urutan pengambilan saat merge. Hitung dalam <strong>fase terpisah</strong> dengan two pointers sebelum merge, memanfaatkan kedua bagian yang sudah urut:</p></div>
    @include('lessons.code', ['cpp' => $mgTwoPhase, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Hitung inversi</h4><p><code>inv += m − i</code> saat mengambil dari kanan.</p></div>
        <div class="pattern"><h4>Tukar bersebelahan minimum</h4><p>= inversi.</p></div>
        <div class="pattern"><h4>a[i] &gt; 2·a[j]</h4><p>Fase hitung terpisah + two pointers.</p></div>
        <div class="pattern"><h4>Menggabung K list</h4><p>Min-heap, O(N log K).</p></div>
        <div class="pattern"><h4>Kecil di kanan setiap elemen</h4><p>Merge sort pada pasangan (nilai, indeks).</p></div>
        <div class="pattern"><h4>Jarak Kendall antar ranking</h4><p>Inversi setelah memetakan satu ranking ke yang lain.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Bagian kiri [4, 6, 7] urut, dan 4 > 3, maka 6 dan 7 juga > 3: tiga inversi sekaligus (m − i = 3).">
        <p class="quiz-q">Kiri = [4, 6, 7], kanan = [3, …]. Saat 3 diambil, inversi bertambah berapa?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Pasangan terbalik: (3,1), (3,2). Jadi 2 inversi dan 2 tukar bersebelahan: 3 1 2 → 1 3 2 → 1 2 3.">
        <p class="quiz-q">Berapa tukar bersebelahan minimum untuk mengurutkan [3, 1, 2]?</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">1</button>
            <button class="quiz-option">3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Ada log n tingkat pemecahan dan setiap tingkat menggabungkan total n elemen.">
        <p class="quiz-q">Mengapa merge sort O(n log n)?</p>
        <div class="quiz-options">
            <button class="quiz-option">Setiap elemen dibandingkan dengan log n elemen</button>
            <button class="quiz-option">log n tingkat, masing-masing O(n)</button>
            <button class="quiz-option">Karena memakai binary search</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
