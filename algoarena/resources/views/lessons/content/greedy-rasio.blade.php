@php
    $grSteps = [
        ['Baca pekerjaan', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> t(n), w(n);           // durasi dan denda per menit menunggu
    for (int i = 0; i < n; i++) cin >> t[i] >> w[i];
CPP, <<<'TXT'
<p><strong>Soal contoh "Antrean Servis":</strong> sebuah bengkel mengerjakan n mobil satu per satu. Mobil ke-i butuh t<sub>i</sub> menit, dan pemiliknya mendenda w<sub>i</sub> rupiah untuk setiap menit sampai mobilnya selesai. Jika mobil i selesai pada menit C<sub>i</sub>, dendanya w<sub>i</sub>·C<sub>i</sub>. Urutkan pekerjaan agar total denda minimum.</p>
TXT],
        ['Urutkan dengan rasio t/w', <<<'CPP'

    vector<int> id(n);
    iota(id.begin(), id.end(), 0);
    sort(id.begin(), id.end(), [&](int i, int j) {
        return t[i] * w[j] < t[j] * w[i];     // t[i]/w[i] < t[j]/w[j], tanpa pecahan
    });
CPP, <<<'TXT'
<p>Kerjakan dulu mobil dengan <strong>t/w terkecil</strong>: cepat dikerjakan dan dendanya mahal. Perbandingan pecahan ditulis sebagai perkalian silang supaya tetap bilangan bulat dan tidak terkena galat <code>double</code>.</p>
<p>Mengapa rasio ini? Lihat bukti "tukar tetangga" di bawah.</p>
TXT],
        ['Hitung total denda', <<<'CPP'

    long long waktu = 0, denda = 0;
    for (int i : id) {
        waktu += t[i];                        // mobil i selesai pada menit ini
        denda += w[i] * waktu;
    }
    cout << denda << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Total O(n log n). Dengan t, w ≤ 1000 dan n ≤ 2·10<sup>5</sup>, waktu sampai 2·10<sup>8</sup> dan denda sampai sekitar 4·10<sup>16</sup>: pakai <code>long long</code>.</p>
TXT],
    ];

    $grPy = <<<'PY'
import sys
from functools import cmp_to_key

data = sys.stdin.buffer.read().split()
n = int(data[0])
job = [(int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(n)]
job.sort(key=cmp_to_key(lambda p, q: p[0] * q[1] - q[0] * p[1]))
waktu = denda = 0
for t, w in job:
    waktu += t
    denda += w * waktu
print(denda)
PY;

    $grFrac = <<<'CPP'
// Fractional knapsack: barang boleh diambil sebagian
sort(id.begin(), id.end(), [&](int i, int j) {
    return v[i] * w[j] > v[j] * w[i];          // nilai per berat terbesar dulu
});
long long sisa = W, utuh = 0;                  // nilai dari barang yang diambil utuh
for (int i : id) {
    if (w[i] <= sisa) { sisa -= w[i]; utuh += v[i]; }
    else { /* ambil sisa/w[i] bagian: nilainya v[i] * sisa / w[i] */ break; }
}
CPP;

    $grHuff = <<<'CPP'
// Huffman / biaya penggabungan minimum: selalu gabungkan dua yang terkecil
priority_queue<long long, vector<long long>, greater<long long>> pq(a.begin(), a.end());
long long biaya = 0;
while (pq.size() > 1) {
    long long x = pq.top(); pq.pop();
    long long y = pq.top(); pq.pop();
    biaya += x + y;
    pq.push(x + y);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menyelesaikan <strong>fractional knapsack</strong> dengan mengurutkan rasio nilai/berat.</li>
        <li>Membuktikan aturan urutan dengan <strong>tukar tetangga</strong> (Smith's rule: urutkan t/w).</li>
        <li>Membangun kode <strong>Huffman</strong> dan menghitung biaya penggabungan minimum dengan heap.</li>
        <li>Membandingkan pecahan dengan perkalian silang, tanpa galat floating point.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'nilai per satuan', 'desc' => 'Rasio menentukan siapa yang didahulukan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Pedagang Rempah">
    <h2>Intuisi: Pedagang Rempah</h2>
    <div class="prose">
        <p>Seorang pedagang punya karung berkapasitas 10 kg dan boleh mengambil rempah <em>sebanyak berapa pun</em> dari setiap jenis (bisa dibagi). Kunyit Rp20 ribu/kg, lada Rp90 ribu/kg, kayu manis Rp50 ribu/kg. Tentu ia mengisi karung dengan rempah termahal per kilogram lebih dulu: lada sampai habis, lalu kayu manis, dan seterusnya. Itulah <strong>fractional knapsack</strong>, dan greedy berdasarkan <strong>rasio</strong> nilai/berat terbukti optimal.</p>
        <p>Jika barang tidak bisa dibagi (knapsack 0/1), greedy yang sama gagal: barang dengan rasio terbaik bisa menyisakan ruang yang terbuang. Di sana kita butuh DP.</p>
        <p>Ide rasio juga muncul di soal <em>urutan</em>: mengerjakan tugas mana dulu agar total denda keterlambatan minimum. Dan di Huffman, yang digabung lebih dulu adalah yang paling kecil, karena ia akan "ikut dihitung" berkali-kali.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Fractional knapsack</b><span>Barang boleh diambil sebagian. Urutkan v/w menurun.</span></div>
        <div class="term"><b>Smith's rule</b><span>Minimalkan Σ w<sub>i</sub>C<sub>i</sub>: kerjakan t/w terkecil dulu.</span></div>
        <div class="term"><b>Huffman</b><span>Kode biner terpendek: berulang kali gabungkan dua frekuensi terkecil.</span></div>
        <div class="term"><b>Perkalian silang</b><span>a/b &lt; c/d ⇔ a·d &lt; c·b (untuk b, d &gt; 0). Tanpa double.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Huffman">
    <h2>Visualisasi: Huffman</h2>
    <div class="prose"><p>Setiap angka adalah frekuensi sebuah simbol. Berulang kali ambil dua yang terkecil dari heap dan gabungkan. Total biaya sama dengan jumlah frekuensi × panjang kode setiap simbol.</p></div>
    <div data-viz="huffman"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'greedy rasio di C++', 'desc' => 'Mengurutkan pekerjaan dengan rasio, lalu fractional knapsack dan Huffman.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Antrean Servis</h2>
    @include('lessons.walkthrough', [
        'title' => 'Urutkan berdasarkan t/w',
        'steps' => $grSteps,
        'sample' => ['input' => "3\n3 1\n1 2\n2 2\n", 'output' => "14\n"],
        'py' => $grPy,
    ])
    <table class="trace-table">
        <tr><th>urutan (t, w)</th><th>t/w</th><th>selesai C</th><th>w · C</th></tr>
        <tr><td>(1, 2)</td><td>0,5</td><td>1</td><td>2</td></tr>
        <tr><td>(2, 2)</td><td>1</td><td>3</td><td>6</td></tr>
        <tr><td>(3, 1)</td><td>3</td><td>6</td><td>6</td></tr>
        <tr class="ok"><td colspan="3">total</td><td><b>14</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="tukar" data-toc="Bukti Tukar Tetangga">
    <h2>Bukti dengan Tukar Tetangga</h2>
    <div class="proof">
        <p>Ambil urutan apa pun dan lihat dua pekerjaan berdampingan i lalu j, yang dimulai pada waktu s. Pekerjaan lain tidak terpengaruh jika keduanya ditukar. Biaya keduanya:</p>
        <p>i lalu j: w<sub>i</sub>(s + t<sub>i</sub>) + w<sub>j</sub>(s + t<sub>i</sub> + t<sub>j</sub>)<br>j lalu i: w<sub>j</sub>(s + t<sub>j</sub>) + w<sub>i</sub>(s + t<sub>j</sub> + t<sub>i</sub>)</p>
        <p>Selisihnya (i dulu dikurangi j dulu) = w<sub>j</sub>t<sub>i</sub> − w<sub>i</sub>t<sub>j</sub>. Jadi i sebaiknya lebih dulu tepat ketika t<sub>i</sub>w<sub>j</sub> ≤ t<sub>j</sub>w<sub>i</sub>, yaitu t<sub>i</sub>/w<sub>i</sub> ≤ t<sub>j</sub>/w<sub>j</sub>.</p>
        <p>Urutan optimal mana pun yang punya pasangan berdampingan "terbalik" bisa ditukar tanpa memburuk. Mengulang penukaran (seperti bubble sort) menghasilkan urutan berdasarkan t/w, sehingga urutan itu optimal.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Kasus khusus w<sub>i</sub> = 1 untuk semua: kerjakan yang <strong>tercepat dulu</strong> (Shortest Job First) untuk meminimalkan total waktu tunggu.</p>
    </div>
</section>

<section class="lesson-section" id="varian" data-toc="Fractional Knapsack & Huffman">
    <h2>Fractional Knapsack dan Huffman di C++</h2>
    @include('lessons.code', ['cpp' => $grFrac, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $grHuff, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Membandingkan pecahan dengan double.</strong> Dua rasio yang sama bisa terlihat berbeda karena pembulatan, dan comparator yang tidak konsisten membuat <code>sort</code> berperilaku tak terdefinisi. Pakai perkalian silang dengan <code>long long</code> (pastikan hasil kalinya tidak overflow).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jawaban pecahan.</strong> Fractional knapsack bisa menghasilkan nilai bukan bulat. Jika soal meminta bagian bulat atau pecahan sederhana, hitung dengan bilangan bulat: v·sisa/w.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'mengapa Huffman optimal', 'desc' => 'Dua simbol terjarang boleh dianggap bersaudara di dasar pohon.'])

<section class="lesson-section" id="huffman" data-toc="Bukti Huffman">
    <h2>Mengapa Huffman Optimal</h2>
    <div class="proof">
        <p>Biaya sebuah pohon kode adalah Σ f<sub>i</sub>·kedalaman(i). Dua fakta:</p>
        <ol>
            <li>Di pohon optimal, simpul terdalam pasti punya saudara (jika tidak, naikkan ia satu tingkat dan biaya turun).</li>
            <li>Kita boleh menukar dua simbol terjarang x, y ke posisi dua daun terdalam yang bersaudara: memindahkan frekuensi kecil ke tempat dalam dan frekuensi besar ke tempat dangkal tidak menaikkan biaya.</li>
        </ol>
        <p>Jadi ada pohon optimal di mana x dan y bersaudara. Gabungkan keduanya menjadi simbol baru berfrekuensi f<sub>x</sub> + f<sub>y</sub>; biaya pohon asli = biaya pohon yang lebih kecil + f<sub>x</sub> + f<sub>y</sub>. Masalahnya mengecil satu simbol, dan induksi menyelesaikan sisanya.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Gabung tali / file</h4><p>Biaya gabung = jumlah panjang: Huffman biner.</p></div>
        <div class="pattern"><h4>Gabung k sekaligus</h4><p>Tambahkan elemen nol sampai (n − 1) habis dibagi (k − 1), lalu selalu gabung k terkecil.</p></div>
        <div class="pattern"><h4>Urutan dengan denda</h4><p>Tukar tetangga → kunci urut berbentuk rasio.</p></div>
        <div class="pattern"><h4>Rasio di binary search</h4><p>"Rata-rata maksimum" sering diselesaikan dengan binary search jawaban + greedy, bukan rasio langsung.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="Rasio nilai/berat: A = 6, B = 5, C = 4. Ambil A (1 kg, 6), B (2 kg, 10), lalu 2 kg dari C (2/3 × 12 = 8): total 24.">
        <p class="quiz-q">Kapasitas 5 kg. Barang (berat, nilai): A (1, 6), B (2, 10), C (3, 12), boleh sebagian. Nilai maksimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">24</button>
            <button class="quiz-option">22</button>
            <button class="quiz-option">28</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Gabung 2 + 3 = 5 (sisa {4, 5, 6}), lalu 4 + 5 = 9 (sisa {6, 9}), lalu 6 + 9 = 15. Total 5 + 9 + 15 = 29.">
        <p class="quiz-q">Panjang tali 2, 3, 4, 6. Biaya menggabung dua tali = jumlah panjangnya. Total biaya minimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">30</button>
            <button class="quiz-option">27</button>
            <button class="quiz-option">29</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="t/w: A = 4/2 = 2, B = 1/1 = 1, C = 6/2 = 3. Urutan B, A, C.">
        <p class="quiz-q">Pekerjaan (t, w): A (4, 2), B (1, 1), C (6, 2). Urutan dengan total Σ w·C minimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">A, B, C</button>
            <button class="quiz-option">B, A, C</button>
            <button class="quiz-option">B, C, A</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
