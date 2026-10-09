@php
    $qsSteps = [
        ['Pivot acak', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

mt19937 rng(chrono::steady_clock::now().time_since_epoch().count());
vector<long long> a;
CPP, <<<'TXT'
<p><strong>Soal contoh "Elemen ke-k":</strong> diberikan n bilangan dan k (1-indexed). Cetak bilangan terkecil ke-k, tanpa mengurutkan seluruh array.</p>
<p><code>mt19937</code> adalah pembangkit acak berkualitas baik. Pivot acak membuat kasus terburuk O(n²) hampir mustahil terjadi, walaupun penyusun soal menyiapkan data yang sudah urut.</p>
TXT],
        ['Partisi Lomuto', <<<'CPP'

// susun ulang a[l..r]: semua <= pivot di kiri, pivot di posisi final, sisanya di kanan
int partisi(int l, int r) {
    int acak = l + rng() % (r - l + 1);
    swap(a[acak], a[r]);              // pivot dipindah ke ujung kanan
    long long p = a[r];
    int i = l;                        // a[l..i-1] <= p
    for (int j = l; j < r; j++)
        if (a[j] <= p) swap(a[i++], a[j]);
    swap(a[i], a[r]);
    return i;                         // posisi final pivot
}
CPP, <<<'TXT'
<p>Invarian selama loop: <code>a[l..i−1] ≤ p</code> dan <code>a[i..j−1] &gt; p</code>. Setiap elemen a[j] yang ≤ p ditukar ke ujung bagian kiri.</p>
<p>Setelah loop, pivot ditukar ke posisi i. Pivot sekarang berada <strong>tepat</strong> di posisi yang akan ia tempati di array yang terurut.</p>
TXT],
        ['Quickselect: hanya satu sisi', <<<'CPP'

long long pilih(int k) {              // k: indeks 0-based setelah diurutkan
    int l = 0, r = (int)a.size() - 1;
    while (true) {
        int m = partisi(l, r);
        if (m == k) return a[m];
        if (k < m) r = m - 1;         // yang dicari pasti di kiri
        else l = m + 1;               // atau di kanan
    }
}
CPP, <<<'TXT'
<p>Quick sort akan merekursi ke <em>dua</em> sisi. Quickselect cukup satu sisi, karena kita tahu di sisi mana posisi k berada. Rata-rata kerjanya n + n/2 + n/4 + … ≈ 2n: <strong>O(n)</strong>.</p>
TXT],
        ['Program utama', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    a.resize(n);
    for (auto& x : a) cin >> x;
    cout << pilih(k - 1) << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Di kontes, cukup pakai <code>nth_element(a.begin(), a.begin() + k − 1, a.end())</code>: fungsi bawaan dengan ide yang sama dan jaminan kasus terburuk yang baik.</p>
TXT],
    ];

    $qsPy = <<<'PY'
import sys, random
data = sys.stdin.read().split()
n, k = int(data[0]), int(data[1])
a = list(map(int, data[2:2 + n]))
k -= 1
# quickselect dengan partisi tiga arah (aman untuk banyak nilai kembar)
while True:
    p = random.choice(a)
    kecil = [x for x in a if x < p]
    sama = sum(1 for x in a if x == p)
    if k < len(kecil):
        a = kecil
    elif k < len(kecil) + sama:
        print(p)
        break
    else:
        k -= len(kecil) + sama
        a = [x for x in a if x > p]
PY;

    $qsNth = <<<'CPP'
// Fungsi bawaan:
nth_element(a.begin(), a.begin() + k, a.end());
// Setelahnya: a[k] = elemen terkecil ke-k (0-based),
// semua a[0..k-1] <= a[k] <= semua a[k+1..n-1] (tetapi tidak terurut)

// Median (n ganjil):
nth_element(a.begin(), a.begin() + n / 2, a.end());
long long median = a[n / 2];
CPP;

    $qs3way = <<<'CPP'
// Partisi tiga arah (Dutch national flag): < p | == p | > p
int lt = l, i = l, gt = r;
while (i <= gt) {
    if (a[i] < p) swap(a[lt++], a[i++]);
    else if (a[i] > p) swap(a[i], a[gt--]);
    else i++;
}
// a[l..lt-1] < p, a[lt..gt] == p, a[gt+1..r] > p
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menulis partisi Lomuto dan menjelaskan invariannya.</li>
        <li>Menjelaskan mengapa quick sort rata-rata O(n log n) tetapi bisa O(n²), dan cara mencegahnya dengan pivot acak.</li>
        <li>Mencari elemen terkecil ke-k dalam O(n) rata-rata dengan <strong>quickselect</strong> atau <code>nth_element</code>.</li>
        <li>Memakai partisi tiga arah untuk data dengan banyak nilai kembar.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'satu pivot, dua kubu', 'desc' => 'Ide partisi dan mengapa pivot langsung berada di tempat finalnya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Barisan Tinggi">
    <h2>Intuisi: Membagi Kelas Menjadi Dua Kubu</h2>
    <div class="prose">
        <p>Guru menunjuk satu siswa sebagai patokan, lalu berkata: "Yang lebih pendek dari Budi ke kiri, yang lebih tinggi ke kanan." Dalam satu kali seruan, Budi langsung berada di posisi yang benar, walaupun kedua kubu masih acak. Ulangi cara yang sama pada setiap kubu, dan seluruh kelas akan terurut. Itulah <strong>quick sort</strong>.</p>
        <p>Jika yang dicari hanya "siswa tertinggi ke-10", guru tidak perlu mengurus kubu yang jelas tidak memuat siswa itu. Itulah <strong>quickselect</strong>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Pivot</b><span>Elemen patokan. Setelah partisi, ia berada di posisi finalnya.</span></div>
        <div class="term"><b>Partisi</b><span>Menyusun ulang dalam O(n) sehingga ≤ pivot di kiri dan &gt; pivot di kanan.</span></div>
        <div class="term"><b>Pivot acak</b><span>Mencegah kasus terburuk yang disengaja: data urut + pivot ujung = O(n²).</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Partisi, Quick Sort, dan Quickselect</h2>
    <div class="prose"><p>Pivot berwarna ungu. Batang biru muda adalah bagian "≤ pivot" yang terus tumbuh. Bandingkan banyak perbandingan antara mode quick sort dan quickselect.</p></div>
    <div data-viz="quick-sort"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'quickselect di C++', 'desc' => 'Mencari elemen ke-k tanpa mengurutkan semuanya.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Elemen ke-k</h2>
    @include('lessons.walkthrough', [
        'title' => 'Quickselect dengan pivot acak',
        'steps' => $qsSteps,
        'sample' => ['input' => "7 3\n7 2 9 4 3 8 1\n", 'output' => "3\n"],
        'py' => $qsPy,
    ])
    @include('lessons.code', ['cpp' => $qsNth, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Algoritma</th><th>Rata-rata</th><th>Terburuk</th></tr>
        <tr><td>Quick sort</td><td><code>O(n log n)</code></td><td><code>O(n²)</code></td></tr>
        <tr><td>Quickselect</td><td><code>O(n)</code></td><td><code>O(n²)</code></td></tr>
        <tr><td><code>std::sort</code> (introsort)</td><td><code>O(n log n)</code></td><td><code>O(n log n)</code></td></tr>
        <tr><td><code>nth_element</code></td><td><code>O(n)</code></td><td>terjamin baik dalam praktik</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pivot selalu elemen terakhir</strong> pada array yang sudah urut: setiap partisi hanya membuang satu elemen, total n²/2 perbandingan. Selalu acak pivotnya.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Banyak nilai kembar.</strong> Dengan Lomuto, array berisi n nilai sama juga O(n²): semua masuk bagian kiri. Pakai partisi tiga arah.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'partisi tiga arah', 'desc' => 'Mengatasi nilai kembar, dan analisis rata-rata.'])

<section class="lesson-section" id="tiga-arah" data-toc="Partisi Tiga Arah">
    <h2>Partisi Tiga Arah (Bendera Belanda)</h2>
    @include('lessons.code', ['cpp' => $qs3way, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Mengapa quickselect rata-rata O(n)?</strong> Pivot acak jatuh di tengah separuh (persentil 25–75) dengan peluang 1/2. Jika itu terjadi, ukuran sisi yang tersisa paling banyak 3/4. Jadi rata-rata setiap dua partisi ukuran menyusut menjadi ≤ 3/4, dan total kerja dibatasi deret geometri n · (1 + 3/4 + (3/4)² + …) · 2 = O(n).</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Median</h4><p><code>nth_element</code> pada n/2.</p></div>
        <div class="pattern"><h4>K terkecil (tanpa urutan)</h4><p><code>nth_element</code> lalu ambil k pertama.</p></div>
        <div class="pattern"><h4>Kelompokkan tiga jenis</h4><p>Partisi tiga arah dalam satu kali jalan.</p></div>
        <div class="pattern"><h4>Titik pusat 1D</h4><p>Median meminimalkan Σ|x − a<sub>i</sub>|.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Ada 3 elemen ≤ 5 selain pivot (2, 1, 4), jadi pivot 5 berakhir di indeks 3.">
        <p class="quiz-q">a = [8, 2, 1, 9, 4, 5], pivot = 5 (elemen terakhir). Di indeks berapa 5 setelah partisi?</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">5</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Quickselect hanya merekursi ke sisi yang memuat posisi k, sehingga kerjanya n + n/2 + … ≈ 2n.">
        <p class="quiz-q">Mengapa quickselect lebih cepat dari quick sort?</p>
        <div class="quiz-options">
            <button class="quiz-option">Hanya menelusuri satu sisi setelah partisi</button>
            <button class="quiz-option">Pivotnya selalu median</button>
            <button class="quiz-option">Tidak memakai perbandingan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Pivot terakhir pada array urut selalu maksimum, sehingga satu sisi kosong dan sisi lain n − 1 elemen: O(n²).">
        <p class="quiz-q">Kapan quick sort dengan pivot elemen terakhir menjadi O(n²)?</p>
        <div class="quiz-options">
            <button class="quiz-option">Array acak</button>
            <button class="quiz-option">Semua elemen berbeda</button>
            <button class="quiz-option">Array sudah terurut</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
