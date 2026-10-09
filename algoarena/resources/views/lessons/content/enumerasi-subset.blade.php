@php
    $esSteps = [
        ['Simpan konflik sebagai bitmask', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> musuh(n, 0);           // bit j di musuh[i] = 1 jika i dan j tidak akur
    for (int k = 0; k < m; k++) {
        int u, v;
        cin >> u >> v;
        u--; v--;
        musuh[u] |= 1 << v;
        musuh[v] |= 1 << u;
    }
CPP, <<<'TXT'
<p><strong>Soal contoh "Kelompok Belajar":</strong> ada n ≤ 20 siswa dan m pasangan siswa yang tidak akur. Bentuk kelompok sebesar mungkin tanpa pasangan yang tidak akur. Cetak ukurannya dan anggota kelompoknya (kelompok dengan mask terkecil jika ada beberapa).</p>
<p>Daftar musuh setiap siswa disimpan sebagai <em>satu bilangan</em>: bit j menyala jika j musuhnya. Dengan begitu, "apakah i punya musuh di dalam kelompok?" cukup satu operasi AND.</p>
TXT],
        ['Coba semua 2^n kelompok', <<<'CPP'

    int terbaik = 0, maskTerbaik = 0;
    for (int mask = 0; mask < (1 << n); mask++) {
        bool aman = true;
        for (int i = 0; i < n && aman; i++)
            if ((mask >> i & 1) && (musuh[i] & mask)) aman = false;
        int ukuran = __builtin_popcount(mask);
        if (aman && ukuran > terbaik) {
            terbaik = ukuran;
            maskTerbaik = mask;
        }
    }
CPP, <<<'TXT'
<p>Setiap bilangan 0 … 2<sup>n</sup> − 1 mewakili satu kelompok: bit i menyala berarti siswa i ikut. <code>mask &gt;&gt; i &amp; 1</code> membaca bit ke-i, dan <code>musuh[i] &amp; mask</code> tidak nol jika ada musuh i di kelompok.</p>
<p><code>__builtin_popcount</code> menghitung banyak bit 1. Karena kita hanya mengganti jika ukurannya <em>lebih besar</em>, mask terkecil yang menang dipertahankan.</p>
<p>2<sup>20</sup> · 20 ≈ 2·10<sup>7</sup> operasi: aman.</p>
TXT],
        ['Cetak anggota', <<<'CPP'

    cout << terbaik << "\n";
    for (int i = 0; i < n; i++)
        if (maskTerbaik >> i & 1) cout << i + 1 << " ";
    cout << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Bit-bit mask terbaik langsung memberi daftar anggotanya. Siswa dicetak dengan nomor 1-based.</p>
<div class="wt-warn">Tanda kurung penting: <code>mask &gt;&gt; i &amp; 1</code> aman karena &gt;&gt; lebih kuat dari &amp;, tetapi <code>x &amp; 1 == 0</code> dibaca <code>x &amp; (1 == 0)</code>. Biasakan menulis <code>(x &amp; 1) == 0</code>.</div>
TXT],
    ];

    $esPy = <<<'PY'
import sys

data = sys.stdin.read().split()
n, m = int(data[0]), int(data[1])
musuh = [0] * n
for k in range(m):
    u, v = int(data[2 + 2 * k]) - 1, int(data[3 + 2 * k]) - 1
    musuh[u] |= 1 << v
    musuh[v] |= 1 << u

terbaik, mask_terbaik = 0, 0
for mask in range(1 << n):
    if all(not (mask >> i & 1) or not (musuh[i] & mask) for i in range(n)):
        c = bin(mask).count("1")
        if c > terbaik:
            terbaik, mask_terbaik = c, mask
print(terbaik)
print(" ".join(str(i + 1) for i in range(n) if mask_terbaik >> i & 1))
PY;

    $esRec = <<<'CPP'
// Rekursi ambil / lewati: mudah dipangkas
void cari(int i, long long jumlah) {
    if (jumlah > target) return;              // pangkas: bilangan positif, jumlah hanya naik
    if (i == n) {
        if (jumlah == target) cara++;
        return;
    }
    pilih.push_back(a[i]);
    cari(i + 1, jumlah + a[i]);               // ambil a[i]
    pilih.pop_back();
    cari(i + 1, jumlah);                      // lewati a[i]
}
CPP;

    $esPerm = <<<'CPP'
// Semua permutasi: next_permutation (mulai dari urutan terurut!) ...
sort(p.begin(), p.end());
do {
    proses(p);
} while (next_permutation(p.begin(), p.end()));

// ... atau rekursi dengan bitmask "sudah dipakai"
void susun(int k, int dipakai) {
    if (k == n) { proses(urut); return; }
    for (int i = 0; i < n; i++)
        if (!(dipakai >> i & 1)) {
            urut[k] = a[i];
            susun(k + 1, dipakai | 1 << i);
        }
}
CPP;

    $esSub = <<<'CPP'
// Untuk SETIAP mask, kunjungi semua submask-nya: total 3^n, bukan 4^n
for (int m = 0; m < (1 << n); m++) {
    for (int s = m; ; s = (s - 1) & m) {
        // s adalah submask dari m; m ^ s adalah sisanya
        if (s == 0) break;
    }
}
CPP;

    $esGosper = <<<'CPP'
// Semua mask dengan tepat k bit 1, urut naik (Gosper's hack)
for (int mask = (1 << k) - 1; mask < (1 << n); ) {
    proses(mask);
    int c = mask & -mask;              // bit 1 terendah
    int r = mask + c;                  // geser blok 1 terendah
    mask = (((r ^ mask) >> 2) / c) | r;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mewakili himpunan bagian dengan <strong>bitmask</strong> dan memakai operasi bit dasar.</li>
        <li>Menelusuri semua 2<sup>n</sup> subset dengan <strong>loop</strong> atau <strong>rekursi ambil/lewati</strong> (dengan pemangkasan).</li>
        <li>Menghasilkan semua <strong>permutasi</strong> dengan <code>next_permutation</code> atau rekursi.</li>
        <li>Menelusuri semua <strong>submask</strong> dalam total O(3<sup>n</sup>) dan tahu kapan n cukup kecil untuk brute force.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'himpunan sebagai bilangan', 'desc' => 'Deretan saklar on/off adalah bilangan biner.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Deretan Saklar">
    <h2>Intuisi: Deretan Saklar Lampu</h2>
    <div class="prose">
        <p>Bayangkan n lampu berjajar, masing-masing punya saklar on/off. Setiap pola saklar memilih sebagian lampu, dan sebaliknya setiap pilihan lampu adalah satu pola saklar. Ada 2<sup>n</sup> pola.</p>
        <p>Pola saklar itu persis bilangan biner n digit. Pola <code>1011</code> berarti lampu 0, 1, dan 3 menyala (bit dibaca dari kanan). Jadi menelusuri semua subset cukup dengan menghitung dari 0 sampai 2<sup>n</sup> − 1.</p>
        <p>Brute force seperti ini adalah senjata pertama ketika n kecil: <strong>n ≤ 20</strong> untuk 2<sup>n</sup> · n, <strong>n ≤ 10</strong> untuk n!, dan <strong>n ≤ 15</strong> untuk 3<sup>n</sup>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Bitmask</b><span>Bilangan bulat yang bit-bitnya menandai anggota himpunan.</span></div>
        <div class="term"><b>popcount</b><span>Banyak bit 1 = ukuran himpunan. <code>__builtin_popcount(x)</code>.</span></div>
        <div class="term"><b>Submask</b><span>s ⊆ m: semua bit 1 di s juga 1 di m, yaitu <code>(s &amp; m) == s</code>.</span></div>
        <div class="term"><b>Pemangkasan</b><span>Berhenti menelusuri cabang yang pasti tidak menghasilkan jawaban.</span></div>
    </div>
</section>

<section class="lesson-section" id="operasi" data-toc="Operasi Bit">
    <h2>Operasi Bit yang Wajib Hafal</h2>
    <table class="cx-table">
        <tr><th>Tujuan</th><th>Kode</th><th>Contoh (x = 1011<sub>2</sub>)</th></tr>
        <tr><td>Apakah i anggota?</td><td><code>x &gt;&gt; i &amp; 1</code></td><td>i = 2 → 0</td></tr>
        <tr><td>Tambahkan i</td><td><code>x | 1 &lt;&lt; i</code></td><td>i = 2 → 1111</td></tr>
        <tr><td>Buang i</td><td><code>x &amp; ~(1 &lt;&lt; i)</code></td><td>i = 0 → 1010</td></tr>
        <tr><td>Balik i</td><td><code>x ^ 1 &lt;&lt; i</code></td><td>i = 3 → 0011</td></tr>
        <tr><td>Gabungan / irisan / selisih</td><td><code>a | b</code>, <code>a &amp; b</code>, <code>a &amp; ~b</code></td><td>–</td></tr>
        <tr><td>Bit 1 terendah</td><td><code>x &amp; -x</code></td><td>0001</td></tr>
        <tr><td>Buang bit 1 terendah</td><td><code>x &amp; (x - 1)</code></td><td>1010</td></tr>
        <tr><td>Himpunan penuh n elemen</td><td><code>(1 &lt;&lt; n) - 1</code></td><td>n = 4 → 1111</td></tr>
    </table>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Tiga Cara Menelusuri</h2>
    <div class="prose"><p>Mode <b>bitmask</b> menghitung mask dari 0 ke atas, mode <b>rekursi</b> menggambar pohon keputusan ambil/lewati, dan mode <b>submask</b> menelusuri semua submask dari m (isi kolom kedua dengan m, misalnya 13 = 1101).</p></div>
    <div data-viz="subset-enum"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'brute force subset di C++', 'desc' => 'Satu soal lengkap, lalu rekursi dan permutasi.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kelompok Belajar</h2>
    @include('lessons.walkthrough', [
        'title' => 'Kelompok terbesar tanpa konflik',
        'steps' => $esSteps,
        'sample' => ['input' => "5 4\n1 2\n2 3\n3 4\n1 5\n", 'output' => "3\n2 4 5\n"],
        'py' => $esPy,
    ])
    <div class="prose"><p>Contoh di atas: pasangan tidak akur 1–2, 2–3, 3–4, dan 1–5. Kelompok {1, 3} aman, tetapi tidak bisa ditambah siapa pun (2 dan 4 bermusuhan dengan 3, sedangkan 5 dengan 1). Satu-satunya kelompok berukuran 3 yang aman adalah {2, 4, 5}, yaitu mask 11010<sub>2</sub> = 26.</p></div>
</section>

<section class="lesson-section" id="rekursi" data-toc="Rekursi & Permutasi">
    <h2>Rekursi Ambil/Lewati dan Permutasi</h2>
    <div class="prose"><p>Loop bitmask selalu memeriksa semua 2<sup>n</sup> subset. Rekursi menelusuri subset yang sama, tetapi kita bisa <strong>memangkas</strong> cabang yang sudah pasti gagal, misalnya ketika jumlah sudah melewati target.</p></div>
    @include('lessons.code', ['cpp' => $esRec, 'js' => null, 'py' => null])
    <div class="prose"><p>Untuk masalah urutan (siapa duluan), yang ditelusuri adalah n! permutasi. <code>next_permutation</code> menghasilkan permutasi berikutnya secara leksikografis, jadi mulailah dari array yang terurut.</p></div>
    @include('lessons.code', ['cpp' => $esPerm, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Penelusuran</th><th>Banyaknya</th><th>n maksimum (± 10<sup>8</sup> langkah)</th></tr>
        <tr><td>Semua subset</td><td><code>2<sup>n</sup></code> (× n untuk memeriksa)</td><td>20–23</td></tr>
        <tr><td>Subset + submask</td><td><code>3<sup>n</sup></code></td><td>15–16</td></tr>
        <tr><td>Semua permutasi</td><td><code>n!</code> (× n)</td><td>10–11</td></tr>
        <tr><td>Subset berukuran k</td><td><code>C(n, k)</code></td><td>tergantung k</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow geser.</strong> <code>1 &lt;&lt; 31</code> sudah melewati batas <code>int</code>. Untuk n ≥ 31 pakai <code>1LL &lt;&lt; i</code> dan <code>__builtin_popcountll</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Prioritas operator.</strong> <code>==</code> lebih kuat daripada <code>&amp;</code>, <code>|</code>, dan <code>^</code>. Selalu beri kurung: <code>(mask &amp; bit) == 0</code>.</p>
    </div>
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Jika memeriksa satu subset butuh O(n), sering bisa dibuat O(1) dengan menurunkannya dari subset lain, misalnya <code>jumlah[mask] = jumlah[mask &amp; (mask − 1)] + a[ctz(mask)]</code>. Itu langkah pertama menuju DP bitmask.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'submask dan trik bit lanjutan', 'desc' => 'Mengapa totalnya 3^n, dan subset berukuran tepat k.'])

<section class="lesson-section" id="submask" data-toc="Iterasi Submask 3^n">
    <h2>Iterasi Submask: Total 3<sup>n</sup></h2>
    <div class="prose"><p>Banyak soal membagi himpunan menjadi dua bagian (atau berulang kali), misalnya "kelompok pertama berangkat hari ini, sisanya besok". Untuk itu kita menelusuri, untuk setiap m, semua submask s dari m.</p></div>
    @include('lessons.code', ['cpp' => $esSub, 'js' => null, 'py' => null])
    <div class="proof">
        <p>Mengapa totalnya 3<sup>n</sup> dan bukan 2<sup>n</sup> · 2<sup>n</sup>? Lihat setiap elemen i dari pasangan (m, s) dengan s ⊆ m. Hanya ada tiga kemungkinan: i tidak di m; i di m tetapi tidak di s; atau i di s (dan otomatis di m). Pilihan setiap elemen bebas, jadi banyak pasangan tepat 3<sup>n</sup>.</p>
        <p>Untuk n = 15, 3<sup>15</sup> ≈ 1,4·10<sup>7</sup>: aman. Untuk n = 20, 3<sup>20</sup> ≈ 3,5·10<sup>9</sup>: terlalu lambat, dan saatnya memakai SOS DP.</p>
    </div>
    <div class="prose"><p>Langkah <code>s = (s − 1) &amp; m</code> bekerja karena mengurangi 1 mematikan bit 1 terendah dan menyalakan semua bit di bawahnya; AND dengan m membuang bit yang bukan milik m. Hasilnya submask m terbesar yang lebih kecil dari s.</p></div>
    <div class="prose"><p>Jika yang dibutuhkan hanya subset berukuran tepat k, Gosper's hack melompat langsung dari satu mask ke mask berikutnya yang punya k bit 1:</p></div>
    @include('lessons.code', ['cpp' => $esGosper, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>n ≤ 20, pilih sebagian</h4><p>Loop mask 0..2<sup>n</sup>−1, cek syarat dengan AND.</p></div>
        <div class="pattern"><h4>n ≤ 10, urutan penting</h4><p><code>next_permutation</code> atau rekursi dengan mask "dipakai".</p></div>
        <div class="pattern"><h4>Bagi menjadi kelompok-kelompok</h4><p>DP atas mask + iterasi submask, O(3<sup>n</sup>).</p></div>
        <div class="pattern"><h4>n ≈ 40</h4><p>Terlalu besar untuk 2<sup>n</sup>: meet in the middle.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Bit dibaca dari kanan: 1101₂ menyalakan bit 0, 2, dan 3, jadi elemen a[0], a[2], a[3].">
        <p class="quiz-q">mask = 13 = 1101<sub>2</sub>. Elemen mana yang dipilih?</p>
        <div class="quiz-options">
            <button class="quiz-option">a[0], a[1], a[3]</button>
            <button class="quiz-option">a[1], a[2], a[3]</button>
            <button class="quiz-option">a[0], a[2], a[3]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="1100 − 1 = 1011, lalu AND 1101 = 1001.">
        <p class="quiz-q">m = 1101<sub>2</sub>, s = 1100<sub>2</sub>. Submask berikutnya <code>(s − 1) &amp; m</code> adalah…</p>
        <div class="quiz-options">
            <button class="quiz-option">1001<sub>2</sub></button>
            <button class="quiz-option">1011<sub>2</sub></button>
            <button class="quiz-option">0100<sub>2</sub></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap elemen punya 3 status: di luar m, di m tetapi tidak di s, atau di s. Totalnya 3^n pasangan.">
        <p class="quiz-q">Berapa total pasangan (m, s) dengan s submask dari m, untuk n elemen?</p>
        <div class="quiz-options">
            <button class="quiz-option">4<sup>n</sup></button>
            <button class="quiz-option">3<sup>n</sup></button>
            <button class="quiz-option">2<sup>n</sup> · n</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
