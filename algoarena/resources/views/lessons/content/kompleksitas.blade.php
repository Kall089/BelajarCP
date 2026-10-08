@php
    $kxSteps = [
        ['Header dan input cepat', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long x;
    cin >> n >> x;
    vector<long long> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];
CPP, <<<'TXT'
<p>Dua baris <code>ios::sync_with_stdio(false)</code> dan <code>cin.tie(nullptr)</code> membuat <code>cin</code>/<code>cout</code> jauh lebih cepat. Untuk input 10<sup>6</sup> angka, perbedaannya bisa beberapa detik. Biasakan selalu menulisnya.</p>
<p>Soalnya: ada n bilangan dan target x. Apakah ada <strong>dua bilangan berbeda posisi</strong> yang jumlahnya tepat x?</p>
TXT],
        ['Cara lambat: coba semua pasangan, O(n²)', <<<'CPP'

    // Cara 1 (lambat): semua pasangan i < j
    // bool ada = false;
    // for (int i = 0; i < n; i++)
    //     for (int j = i + 1; j < n; j++)
    //         if (a[i] + a[j] == x) ada = true;
CPP, <<<'TXT'
<p>Cara paling langsung: periksa setiap pasangan. Banyaknya pasangan n(n−1)/2. Untuk n = 2 · 10<sup>5</sup> itu sekitar <strong>2 · 10<sup>10</sup></strong> operasi, sekitar 200 kali lebih banyak dari yang sanggup dikerjakan dalam 1 detik.</p>
<div class="wt-warn">Kode ini benar, tetapi akan mendapat <strong>TLE</strong> (Time Limit Exceeded). Itulah sebabnya kita memperkirakan kompleksitas <em>sebelum</em> menulis kode.</div>
TXT],
        ['Urutkan dulu: O(n log n)', <<<'CPP'

    // Cara 2 (cepat): urutkan, lalu dua penunjuk
    sort(a.begin(), a.end());
CPP, <<<'TXT'
<p><code>sort</code> di C++ berjalan dalam O(n log n). Untuk n = 2 · 10<sup>5</sup>, itu sekitar 2 · 10<sup>5</sup> × 18 ≈ 3,6 · 10<sup>6</sup> langkah: sangat ringan.</p>
TXT],
        ['Dua penunjuk: O(n)', <<<'CPP'

    int kiri = 0, kanan = n - 1;
    bool ada = false;
    while (kiri < kanan) {
        long long jumlah = a[kiri] + a[kanan];
        if (jumlah == x) {
            ada = true;
            break;
        } else if (jumlah < x) {
            kiri++;
        } else {
            kanan--;
        }
    }
CPP, <<<'TXT'
<p>Satu penunjuk di ujung kiri (terkecil), satu di ujung kanan (terbesar):</p>
<ul>
    <li>Jumlah terlalu kecil → satu-satunya cara memperbesar adalah menggeser <code>kiri</code> ke kanan.</li>
    <li>Jumlah terlalu besar → geser <code>kanan</code> ke kiri.</li>
</ul>
<p>Setiap langkah membuang satu elemen, jadi paling banyak n langkah. Total program: O(n log n) untuk sort + O(n) = <strong>O(n log n)</strong>.</p>
<pre>a = [1, 3, 4, 6, 9], x = 10
1 + 9 = 10 → ketemu</pre>
<div class="wt-tip">Penjumlahan dua bilangan sampai 10<sup>9</sup> bisa mencapai 2 · 10<sup>9</sup>, melewati batas <code>int</code> (±2,1 · 10<sup>9</sup>). Itulah sebabnya dipakai <code>long long</code>.</div>
TXT],
        ['Cetak jawaban', <<<'CPP'

    cout << (ada ? "YA" : "TIDAK") << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Untuk contoh <code>5 10</code> dengan <code>9 4 1 6 3</code>, pasangan 4 + 6 (atau 1 + 9) ada, jadi jawabannya <code>YA</code>.</p>
TXT],
    ];

    $kxLoops = <<<'CPP'
// O(1): tidak bergantung pada n
int tengah = a[n / 2];

// O(n): satu loop sepanjang n
for (int i = 0; i < n; i++) total += a[i];

// O(n²): dua loop bersarang
for (int i = 0; i < n; i++)
    for (int j = 0; j < n; j++) cek(i, j);

// O(log n): n dibagi dua setiap langkah
while (n > 1) n /= 2;

// O(n log n): untuk setiap i, sebuah loop yang membagi dua
for (int i = 0; i < n; i++)
    for (int k = 1; k < n; k *= 2) proses(i, k);

// O(√n): berhenti saat i*i melewati n
for (long long i = 1; i * i <= n; i++) if (n % i == 0) faktor++;
CPP;

    $kxAmort = <<<'CPP'
// Terlihat O(n²) karena ada loop di dalam loop, padahal O(n):
// penunjuk j hanya pernah MAJU, total maju paling banyak n kali.
int j = 0;
for (int i = 0; i < n; i++) {
    while (j < n && a[j] - a[i] <= k) j++;
    terbaik = max(terbaik, j - i);
}
CPP;

    $kxHarmonic = <<<'CPP'
// Untuk setiap d, kunjungi kelipatannya: d, 2d, 3d, ...
// Total: n/1 + n/2 + n/3 + ... + n/n  ≈  n ln n  =  O(n log n)
for (int d = 1; d <= n; d++)
    for (int m = d; m <= n; m += d) banyakPembagi[m]++;
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan cara kerja judge dan arti <strong>TLE</strong>, <strong>WA</strong>, dan <strong>RTE</strong>.</li>
        <li>Menghitung kompleksitas kode dengan notasi <strong>Big-O</strong>.</li>
        <li>Membaca <strong>batasan soal</strong> untuk menebak algoritma yang diharapkan, sebelum menulis kode.</li>
        <li>Menghindari jebakan umum: <code>int</code> meluap, input lambat, dan loop yang diam-diam O(n²).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'cepat atau lambat?', 'desc' => 'Cara judge menilai kode, notasi Big-O, dan membaca batasan soal.'])

<section class="lesson-section" id="judge" data-toc="Cara Judge Bekerja">
    <h2>Cara Judge Bekerja</h2>
    <div class="prose">
        <p>Saat kamu menekan <strong>Submit</strong>, kodemu dikompilasi lalu dijalankan pada banyak <strong>tes tersembunyi</strong>. Untuk setiap tes, output-mu dibandingkan dengan jawaban yang benar, dan waktu jalannya diukur.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>AC (Accepted)</b><span>Output benar dan cukup cepat.</span></div>
        <div class="term"><b>WA (Wrong Answer)</b><span>Program selesai, tetapi output-nya salah.</span></div>
        <div class="term"><b>TLE (Time Limit Exceeded)</b><span>Program terlalu lama. Biasanya batasnya 1–2 detik.</span></div>
        <div class="term"><b>RTE (Runtime Error)</b><span>Program crash: indeks di luar array, pembagian nol, rekursi terlalu dalam.</span></div>
        <div class="term"><b>CE (Compile Error)</b><span>Kode tidak bisa dikompilasi.</span></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Patokan penting:</strong> komputer judge bisa mengerjakan sekitar <strong>10<sup>8</sup> operasi sederhana per detik</strong> dalam C++. Jika perkiraan operasimu jauh di atas itu, kodenya hampir pasti TLE.</p>
    </div>
</section>

<section class="lesson-section" id="bigo" data-toc="Notasi Big-O">
    <h2>Notasi Big-O</h2>
    <div class="prose">
        <p>Kita jarang menghitung operasi secara persis. Yang penting adalah <strong>bagaimana banyaknya operasi tumbuh</strong> ketika n membesar. Big-O menuliskan bagian yang paling cepat tumbuh dan membuang konstanta:</p>
        <ul>
            <li><code>3n + 5</code> ditulis <code>O(n)</code>.</li>
            <li><code>n² / 2 + 100n</code> ditulis <code>O(n²)</code>: untuk n besar, n² jauh mengalahkan 100n.</li>
            <li><code>n log n + n</code> ditulis <code>O(n log n)</code>.</li>
        </ul>
    </div>
    @include('lessons.code', ['cpp' => $kxLoops, 'js' => null, 'py' => null])
    <div class="prose">
        <p>Geser N pada penjelajah di bawah dan lihat bagaimana setiap kompleksitas tumbuh. Perhatikan kapan sebuah kelas mulai melewati batas 10<sup>8</sup>.</p>
    </div>
    <div data-viz="bigo"></div>
</section>

<section class="lesson-section" id="batasan" data-toc="Membaca Batasan Soal">
    <h2>Membaca Batasan Soal</h2>
    <div class="prose">
        <p>Batasan soal adalah petunjuk tersembunyi dari pembuat soal. Dari nilai n terbesar, kita bisa menebak kompleksitas yang diharapkan:</p>
    </div>
    <table class="cx-table">
        <tr><th>n paling besar</th><th>Kompleksitas yang masih aman</th><th>Contoh algoritma</th></tr>
        <tr><td>≤ 10</td><td><code>O(n!)</code></td><td>Coba semua permutasi</td></tr>
        <tr><td>≤ 20</td><td><code>O(2ⁿ · n)</code></td><td>Semua himpunan bagian, DP bitmask</td></tr>
        <tr><td>≤ 500</td><td><code>O(n³)</code></td><td>Floyd-Warshall, DP interval</td></tr>
        <tr><td>≤ 5 000</td><td><code>O(n²)</code></td><td>DP dua dimensi, dua loop</td></tr>
        <tr><td>≤ 10<sup>6</sup></td><td><code>O(n log n)</code></td><td>Sort, Dijkstra, binary search</td></tr>
        <tr><td>≤ 10<sup>8</sup></td><td><code>O(n)</code></td><td>Satu kali lewat, prefix sum</td></tr>
        <tr><td>≥ 10<sup>9</sup></td><td><code>O(log n)</code> atau <code>O(1)</code></td><td>Rumus, binary search pada jawaban</td></tr>
    </table>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Kebiasaan yang baik: sebelum menulis kode, tuliskan kompleksitas idemu dan hitung operasinya dengan n terbesar. Jika hasilnya di bawah 10<sup>8</sup>, lanjut. Jika tidak, cari ide yang lebih cepat dulu.</p>
    </div>
</section>

<section class="lesson-section" id="balapan" data-toc="Balapan Pencarian">
    <h2>Contoh Nyata: O(n) vs O(log n)</h2>
    <div class="prose">
        <p>Mencari sebuah angka di array <strong>yang sudah urut</strong>. Pencarian linear memeriksa satu per satu. Pencarian biner selalu memeriksa bagian tengah, lalu membuang setengah sisanya. Untuk n = 10<sup>9</sup>, pencarian linear butuh sampai satu miliar langkah, sedangkan pencarian biner hanya sekitar 30.</p>
    </div>
    <div data-viz="search-race"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'dari O(n²) ke O(n log n)', 'desc' => 'Satu soal, dua solusi, dan jebakan yang sering membuat WA atau TLE.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Dua Angka Berjumlah X</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> diberikan n ≤ 2 · 10<sup>5</sup> bilangan (masing-masing ≤ 10<sup>9</sup>) dan target x. Adakah dua bilangan (posisi berbeda) yang jumlahnya x? Cetak <code>YA</code> atau <code>TIDAK</code>.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Dari brute force ke dua penunjuk',
        'steps' => $kxSteps,
        'sample' => ['input' => "5 10\n9 4 1 6 3\n", 'output' => "YA\n"],
    ])
</section>

<section class="lesson-section" id="jebakan" data-toc="Jebakan Umum">
    <h2>Jebakan Umum</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>int meluap</h4><p><code>int</code> hanya sampai ±2,1 · 10<sup>9</sup>. Hasil kali atau jumlah yang besar butuh <code>long long</code> (±9,2 · 10<sup>18</sup>).</p></div>
        <div class="pattern"><h4>Input lambat</h4><p>Lupa <code>sync_with_stdio(false)</code>, atau memakai <code>endl</code> ribuan kali. Pakai <code>'\n'</code>.</p></div>
        <div class="pattern"><h4>Salin vector</h4><p>Mengoper <code>vector</code> ke fungsi tanpa <code>&amp;</code> menyalin seluruh isinya setiap pemanggilan.</p></div>
        <div class="pattern"><h4>Operasi tersembunyi</h4><p><code>s = s + c</code> pada string menyalin string setiap kali (O(n) per langkah). Pakai <code>s += c</code>.</p></div>
        <div class="pattern"><h4>Array besar di dalam fungsi</h4><p>Array lokal ukuran 10<sup>7</sup> bisa membuat stack overflow (RTE). Jadikan global atau pakai <code>vector</code>.</p></div>
        <div class="pattern"><h4>Memori</h4><p>Batas 256 MB ≈ 6 · 10<sup>7</sup> <code>int</code> atau 3 · 10<sup>7</sup> <code>long long</code>.</p></div>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'menghitung kompleksitas yang tidak terlihat', 'desc' => 'Analisis amortisasi, deret harmonik, dan rekursi.'])

<section class="lesson-section" id="amortisasi" data-toc="Amortisasi">
    <h2>Loop Bersarang yang Tetap O(n)</h2>
    <div class="prose">
        <p>Jangan langsung menyimpulkan O(n²) hanya karena ada loop di dalam loop. Hitung <strong>total</strong> pekerjaan loop dalam di sepanjang program:</p>
    </div>
    @include('lessons.code', ['cpp' => $kxAmort, 'js' => null, 'py' => null])
    <div class="prose">
        <p>Penunjuk <code>j</code> tidak pernah mundur, jadi total <code>j++</code> di seluruh program paling banyak n. Teknik ini (dua penunjuk / sliding window) total O(n). Hal yang sama berlaku untuk BFS dan DFS: setiap simpul dan sisi diproses sekali, jadi O(V + E), bukan O(V · E).</p>
        <h3>Deret harmonik</h3>
    </div>
    @include('lessons.code', ['cpp' => $kxHarmonic, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="rekursi" data-toc="Kompleksitas Rekursi">
    <h2>Kompleksitas Rekursi</h2>
    <div class="prose">
        <p>Untuk fungsi rekursif, hitung <strong>banyaknya pemanggilan</strong> dikali <strong>pekerjaan per pemanggilan</strong>:</p>
    </div>
    <table class="cx-table">
        <tr><th>Rekursi</th><th>Pemanggilan</th><th>Kompleksitas</th></tr>
        <tr><td>Fibonacci tanpa memo: <code>f(n−1) + f(n−2)</code></td><td>bercabang dua setiap level</td><td><code>O(2ⁿ)</code></td></tr>
        <tr><td>Fibonacci dengan memo</td><td>setiap n dihitung sekali</td><td><code>O(n)</code></td></tr>
        <tr><td>Pencarian biner</td><td>satu cabang, ukuran dibagi dua</td><td><code>O(log n)</code></td></tr>
        <tr><td>Merge sort: dua cabang ukuran n/2 + gabung O(n)</td><td>log n level, setiap level O(n)</td><td><code>O(n log n)</code></td></tr>
    </table>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Inilah alasan dynamic programming begitu kuat: rekursi O(2<sup>n</sup>) menjadi O(n) hanya dengan <strong>mengingat</strong> hasil yang sudah pernah dihitung. Kamu akan melihatnya di materi Konsep DP.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="n = 10⁵ → n² = 10¹⁰, sekitar 100 kali batas 10⁸. Perlu O(n log n) atau lebih baik.">
        <p class="quiz-q">n ≤ 10<sup>5</sup>, batas waktu 1 detik. Apakah solusi O(n²) aman?</p>
        <div class="quiz-options">
            <button class="quiz-option">Aman, 10<sup>5</sup> itu kecil</button>
            <button class="quiz-option">Aman jika memakai C++</button>
            <button class="quiz-option">Tidak, sekitar 10<sup>10</sup> operasi</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="i digandakan setiap langkah sampai melewati n, jadi banyak langkahnya sekitar log₂ n.">
        <p class="quiz-q">Kompleksitas <code>for (int i = 1; i &lt; n; i *= 2)</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(log n)</button>
            <button class="quiz-option">O(n)</button>
            <button class="quiz-option">O(n / 2)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="10⁹ × 10⁹ = 10¹⁸ melewati batas int (±2,1 · 10⁹) tetapi masih muat di long long (±9,2 · 10¹⁸).">
        <p class="quiz-q"><code>a</code> dan <code>b</code> masing-masing ≤ 10<sup>9</sup>. Tipe apa yang tepat untuk <code>a * b</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">int</button>
            <button class="quiz-option">long long</button>
            <button class="quiz-option">double</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
