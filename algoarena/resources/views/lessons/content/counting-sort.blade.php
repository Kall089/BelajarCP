@php
    $csSteps = [
        ['Rentang nilai kecil', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> cnt(101, 0);         // nilai ujian 0..100
CPP, <<<'TXT'
<p><strong>Soal contoh "Urutkan Nilai Ujian":</strong> ada n nilai ujian (bilangan bulat 0 sampai 100). Cetak semuanya dari yang tertinggi ke yang terendah. n bisa sampai 10<sup>6</sup>.</p>
<p>Hanya ada 101 kemungkinan nilai. Daripada membandingkan nilai satu sama lain, kita cukup <strong>menghitung</strong> berapa kali setiap nilai muncul.</p>
TXT],
        ['Hitung kemunculan', <<<'CPP'
    for (int i = 0; i < n; i++) {
        int x;
        cin >> x;
        cnt[x]++;
    }
CPP, <<<'TXT'
<p>Satu kali jalan, O(n). Setelah ini, informasi yang tersisa hanyalah "nilai x muncul cnt[x] kali", dan untuk bilangan biasa itu sudah cukup untuk menyusun ulang hasilnya.</p>
TXT],
        ['Tulis ulang dari ember', <<<'CPP'

    string out;
    for (int x = 100; x >= 0; x--)            // dari nilai tertinggi
        for (int k = 0; k < cnt[x]; k++) {
            out += to_string(x);
            out += ' ';
        }
    if (!out.empty()) out.back() = '\n';
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Total O(n + K) dengan K = banyak kemungkinan nilai (101). Tidak ada satu pun perbandingan antar elemen, jadi batas bawah Ω(n log n) untuk sorting berbasis perbandingan tidak berlaku.</p>
<div class="wt-warn">Jika setiap elemen membawa data lain (misalnya nama), menulis ulang dari cnt saja tidak cukup. Pakai versi <strong>stabil</strong> dengan prefix sum posisi (lihat level Menengah).</div>
TXT],
    ];

    $csPy = <<<'PY'
import sys
data = sys.stdin.buffer.read().split()
n = int(data[0])
cnt = [0] * 101
for x in data[1:1 + n]:
    cnt[int(x)] += 1
hasil = []
for x in range(100, -1, -1):
    hasil.extend([str(x)] * cnt[x])
print(" ".join(hasil))
PY;

    $csStable = <<<'CPP'
// Counting sort STABIL untuk record (kunci 0..K-1): dasar radix sort
vector<int> cnt(K, 0);
for (auto& r : rec) cnt[r.kunci]++;
for (int k = 1; k < K; k++) cnt[k] += cnt[k - 1];   // cnt[k] = posisi akhir ember k
vector<Rec> out(n);
for (int i = n - 1; i >= 0; i--)                   // dari belakang → stabil
    out[--cnt[rec[i].kunci]] = rec[i];
CPP;

    $csRadix = <<<'CPP'
// Radix sort LSD basis 2^16 untuk bilangan 32-bit tak negatif: 2 putaran
void radixSort(vector<unsigned>& a) {
    vector<unsigned> b(a.size());
    for (int geser = 0; geser < 32; geser += 16) {
        vector<int> cnt(1 << 16, 0);
        for (unsigned x : a) cnt[(x >> geser) & 0xFFFF]++;
        for (int k = 1; k < (1 << 16); k++) cnt[k] += cnt[k - 1];
        for (int i = (int)a.size() - 1; i >= 0; i--) b[--cnt[(a[i] >> geser) & 0xFFFF]] = a[i];
        a.swap(b);
    }
}
CPP;

    $csBucket = <<<'CPP'
// Celah maksimum antar elemen berurutan (setelah diurutkan) dalam O(n): ide ember
// n bilangan di [mn, mx]: celah maksimum >= (mx - mn) / (n - 1).
// Ember berlebar ≈ (mx - mn) / (n - 1): celah terbesar TIDAK mungkin di dalam satu ember,
// jadi cukup simpan min & max setiap ember, lalu bandingkan max ember dengan min ember berikutnya.
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengurutkan bilangan dengan rentang kecil dalam O(n + K) memakai <strong>counting sort</strong>.</li>
        <li>Menulis counting sort yang <strong>stabil</strong> dengan prefix sum posisi.</li>
        <li>Menjelaskan <strong>radix sort</strong> (digit demi digit) dan mengapa setiap putarannya harus stabil.</li>
        <li>Memakai ide <strong>ember</strong> (bucket) dan tahu batas bawah Ω(n log n) untuk sorting dengan perbandingan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'mengurutkan tanpa membandingkan', 'desc' => 'Ember-ember bernomor dan menghitung isinya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Kotak Pos">
    <h2>Intuisi: Kotak Pos Bernomor</h2>
    <div class="prose">
        <p>Petugas pos menerima ribuan surat untuk 100 rumah. Ia tidak membandingkan surat satu sama lain; ia langsung melempar setiap surat ke kotak bernomor rumahnya. Setelah itu, cukup ambil isi kotak 1, kotak 2, dan seterusnya: semuanya sudah urut menurut nomor rumah.</p>
        <p>Itulah <strong>counting sort</strong>. Syaratnya, rentang nilai (banyak kotak) K harus kecil. Waktunya O(n + K), lebih cepat dari O(n log n) jika K tidak terlalu besar.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Counting sort</b><span>Hitung kemunculan setiap nilai. O(n + K). Untuk K ≲ 10<sup>7</sup>.</span></div>
        <div class="term"><b>Radix sort</b><span>Counting sort per digit, dari digit paling kanan. O(d · (n + basis)).</span></div>
        <div class="term"><b>Bucket sort</b><span>Bagi rentang menjadi ember-ember, urutkan isi setiap ember. Bagus jika data tersebar merata.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Radix">
    <h2>Visualisasi: Radix Sort</h2>
    <div class="prose"><p>Setiap putaran mengelompokkan bilangan ke 10 ember menurut satu digit, lalu menggabungkannya kembali. Perhatikan bahwa di dalam setiap ember urutan lama dipertahankan: itulah yang membuat putaran sebelumnya tidak sia-sia.</p></div>
    <div data-viz="radix-sort"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'counting sort di C++', 'desc' => 'Versi sederhana, versi stabil, dan radix sort.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Urutkan Nilai Ujian</h2>
    @include('lessons.walkthrough', [
        'title' => 'Counting sort untuk nilai 0..100',
        'steps' => $csSteps,
        'sample' => ['input' => "8\n70 85 70 100 0 85 70 60\n", 'output' => "100 85 85 70 70 70 60 0\n"],
        'py' => $csPy,
    ])
</section>

<section class="lesson-section" id="stabil" data-toc="Versi Stabil & Radix">
    <h2>Versi Stabil dan Radix Sort</h2>
    <div class="prose"><p>Prefix sum dari cnt memberi <strong>posisi akhir</strong> setiap ember. Dengan menaruh elemen dari belakang ke depan, elemen berkunci sama tetap dalam urutan aslinya.</p></div>
    @include('lessons.code', ['cpp' => $csStable, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $csRadix, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Algoritma</th><th>Waktu</th><th>Memori</th><th>Syarat</th></tr>
        <tr><td>Counting sort</td><td><code>O(n + K)</code></td><td><code>O(K)</code></td><td>K kecil</td></tr>
        <tr><td>Radix sort</td><td><code>O(d · (n + B))</code></td><td><code>O(n + B)</code></td><td>Kunci berupa digit/bit</td></tr>
        <tr><td>Bucket sort</td><td><code>O(n)</code> rata-rata</td><td><code>O(n)</code></td><td>Data tersebar merata</td></tr>
        <tr><td><code>std::sort</code></td><td><code>O(n log n)</code></td><td><code>O(log n)</code></td><td>–</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>K besar.</strong> Counting sort untuk nilai sampai 10<sup>9</sup> butuh array 4 GB. Kompres dulu, atau pakai <code>sort</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Bilangan negatif.</strong> Geser semua nilai dengan +offset (misalnya +100) sebelum menjadi indeks.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'batas bawah dan ember', 'desc' => 'Mengapa perbandingan butuh n log n, dan trik ember untuk celah maksimum.'])

<section class="lesson-section" id="batas" data-toc="Batas Bawah Ω(n log n)">
    <h2>Batas Bawah Sorting dengan Perbandingan</h2>
    <div class="proof">
        <p>Algoritma yang hanya membandingkan pasangan elemen bisa digambarkan sebagai pohon keputusan: setiap simpul adalah satu perbandingan, setiap daun adalah satu urutan hasil. Ada n! kemungkinan urutan input yang harus dibedakan, jadi pohon itu butuh paling sedikit n! daun, dan tingginya paling sedikit log<sub>2</sub>(n!) ≈ n log<sub>2</sub> n. Maka kasus terburuknya Ω(n log n) perbandingan.</p>
        <p>Counting dan radix sort lolos dari batas ini karena tidak membandingkan: mereka memakai <em>nilai</em> elemen sebagai alamat.</p>
    </div>
    @include('lessons.code', ['cpp' => $csBucket, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Nilai 0..K kecil</h4><p>Counting sort atau array frekuensi.</p></div>
        <div class="pattern"><h4>String sama panjang</h4><p>Radix sort dari karakter terakhir.</p></div>
        <div class="pattern"><h4>Celah maksimum O(n)</h4><p>Ember + prinsip sarang merpati.</p></div>
        <div class="pattern"><h4>Urut stabil berdasarkan kunci kecil</h4><p>Counting sort dengan prefix posisi.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Counting sort O(n + K): 10^6 + 101 langkah. Tidak ada log n.">
        <p class="quiz-q">Mengurutkan 10<sup>6</sup> nilai di rentang 0..100 dengan counting sort butuh sekitar…</p>
        <div class="quiz-options">
            <button class="quiz-option">10<sup>6</sup> · log 10<sup>6</sup> langkah</button>
            <button class="quiz-option">10<sup>6</sup> + 101 langkah</button>
            <button class="quiz-option">101 · log 101 langkah</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Putaran berikutnya hanya membedakan digit yang lebih tinggi. Untuk digit sama, urutan dari putaran sebelumnya (digit lebih rendah) harus dipertahankan, yaitu stabil.">
        <p class="quiz-q">Mengapa setiap putaran radix sort harus stabil?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar urutan dari digit sebelumnya tidak rusak</button>
            <button class="quiz-option">Agar lebih cepat</button>
            <button class="quiz-option">Agar bisa menangani bilangan negatif</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Pohon keputusan butuh n! daun, sehingga tingginya ≥ log₂(n!) = Ω(n log n).">
        <p class="quiz-q">Dari mana batas bawah Ω(n log n) untuk sorting dengan perbandingan?</p>
        <div class="quiz-options">
            <button class="quiz-option">Dari merge sort</button>
            <button class="quiz-option">Dari kecepatan prosesor</button>
            <button class="quiz-option">Ada n! urutan yang harus dibedakan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
