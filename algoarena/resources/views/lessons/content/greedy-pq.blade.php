@php
    $gpSteps = [
        ['Baca kursus dan urutkan tenggat', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<pair<long long, long long>> c(n);    // (tenggat, durasi)
    for (auto& p : c) cin >> p.second >> p.first;
    sort(c.begin(), c.end());                   // tenggat paling awal dulu
CPP, <<<'TXT'
<p><strong>Soal contoh "Kursus Daring":</strong> ada n kursus. Kursus ke-i butuh t<sub>i</sub> hari berturut-turut dan harus selesai paling lambat hari d<sub>i</sub>. Kamu mulai di hari 0 dan hanya bisa mengikuti satu kursus pada satu waktu. Berapa kursus paling banyak yang bisa diselesaikan?</p>
<p>Jika himpunan kursus sudah dipilih, urutan terbaik untuk mengerjakannya selalu berdasarkan tenggat (tukar tetangga). Jadi kita memproses kursus dalam urutan tenggat.</p>
TXT],
        ['Ambil dulu', <<<'CPP'

    priority_queue<long long> pq;               // durasi kursus yang sedang diambil (terbesar di atas)
    long long total = 0;                        // total hari yang terpakai
    for (auto& p : c) {
        long long tenggat = p.first, durasi = p.second;
        total += durasi;
        pq.push(durasi);
CPP, <<<'TXT'
<p>Setiap kursus langsung diambil. Kita belum tahu apakah ini keputusan bagus, tetapi kita menyimpannya di heap supaya bisa dibatalkan nanti.</p>
TXT],
        ['Menyesal: buang yang terlama', <<<'CPP'
        if (total > tenggat) {                  // tidak muat sebelum tenggat ini
            total -= pq.top();                  // batalkan kursus TERPANJANG
            pq.pop();
        }
    }
    cout << pq.size() << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Jika total melewati tenggat, satu kursus harus dibuang. Membuang yang <strong>terpanjang</strong> mengurangi total paling banyak, sedangkan banyak kursus berkurang satu apa pun yang dibuang. Ruang yang tersisa paling lega untuk kursus-kursus berikutnya.</p>
<p>Setiap kursus masuk dan keluar heap paling banyak sekali: O(n log n).</p>
TXT],
    ];

    $gpPy = <<<'PY'
import sys, heapq

data = sys.stdin.buffer.read().split()
n = int(data[0])
c = sorted((int(data[2 + 2 * i]), int(data[1 + 2 * i])) for i in range(n))   # (tenggat, durasi)
pq, total = [], 0
for tenggat, durasi in c:
    total += durasi
    heapq.heappush(pq, -durasi)          # max-heap dengan nilai negatif
    if total > tenggat:
        total += heapq.heappop(pq)       # buang yang terpanjang
print(len(pq))
PY;

    $gpUnit = <<<'CPP'
// Pekerjaan 1 hari dengan tenggat & untung: maksimalkan total untung
sort(job.begin(), job.end());                      // (tenggat, untung), tenggat naik
priority_queue<long long, vector<long long>, greater<long long>> pq;   // untung terkecil di atas
for (auto& j : job) {
    pq.push(j.second);
    if ((long long)pq.size() > j.first) pq.pop();  // terlalu banyak untuk tenggat ini: buang termurah
}
// jawaban = jumlah isi pq
CPP;

    $gpStock = <<<'CPP'
// Jual-beli saham: setiap hari boleh beli 1, jual 1, atau diam. Maksimalkan untung.
priority_queue<long long, vector<long long>, greater<long long>> pq;
long long untung = 0;
for (long long p : harga) {
    if (!pq.empty() && pq.top() < p) {
        untung += p - pq.top();      // jual hari ini, pasangkan dengan hari beli termurah
        pq.pop();
        pq.push(p);                  // "tiket penyesalan": jika nanti ada harga lebih tinggi,
    }                                //  penjualan hari ini bisa dialihkan ke hari itu
    pq.push(p);                      // hari ini juga calon hari beli
}
CPP;

    $gpFuel = <<<'CPP'
// Isi bensin sesedikit mungkin: lewati pom dulu, isi "mundur" dari yang terbanyak saat kehabisan
priority_queue<long long> lewat;                   // bensin di pom yang sudah dilewati
long long bensin = awal; int isi = 0, i = 0;
while (bensin < tujuan) {                          // bensin = jarak terjauh yang bisa dicapai
    while (i < n && pos[i] <= bensin) lewat.push(liter[i++]);
    if (lewat.empty()) { isi = -1; break; }        // terdampar
    bensin += lewat.top(); lewat.pop(); isi++;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai teknik <strong>"ambil dulu, menyesal kemudian"</strong>: ambil semua, buang yang terburuk dari heap saat aturan dilanggar.</li>
        <li>Menjadwalkan pekerjaan dengan <strong>tenggat</strong> (banyak maksimum atau untung maksimum).</li>
        <li>Menyelesaikan soal jual-beli saham dan isi bensin dengan heap.</li>
        <li>Menjelaskan mengapa membuang elemen terburuk tidak pernah merugikan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'keputusan yang bisa dibatalkan', 'desc' => 'Heap menyimpan pilihan-pilihan yang mungkin kita sesali.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Tas Belanja">
    <h2>Intuisi: Memilih Belanjaan di Kasir</h2>
    <div class="prose">
        <p>Kamu masuk minimarket dengan uang terbatas dan memasukkan barang ke keranjang sambil berjalan. Di kasir, totalnya ternyata melebihi uangmu. Barang mana yang dikembalikan? Tentu yang paling tidak penting, satu per satu, sampai cukup.</p>
        <p>Itulah pola <strong>regret greedy</strong>: proses data dalam urutan yang tepat (biasanya urutan tenggat), langsung ambil setiap pilihan, dan simpan pilihan-pilihan itu di <strong>priority queue</strong>. Ketika aturan dilanggar, buang pilihan terburuk dari heap. Kita tidak perlu menebak masa depan; kita cukup membatalkan kesalahan dengan murah.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Ambil dulu</b><span>Masukkan setiap kandidat ke heap tanpa ragu.</span></div>
        <div class="term"><b>Menyesal</b><span>Saat batas dilanggar, keluarkan elemen terburuk (heap.top()).</span></div>
        <div class="term"><b>Urutan proses</b><span>Biasanya tenggat naik, posisi naik, atau waktu naik.</span></div>
        <div class="term"><b>Invarian heap</b><span>Isi heap selalu himpunan terbaik untuk prefiks yang sudah diproses.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Pekerjaan dengan Tenggat dan Untung</h2>
    <div class="prose"><p>Setiap pekerjaan butuh satu hari. Tulis sebagai <code>tenggat:untung</code>. Pekerjaan diproses berdasarkan tenggat; jika isi jadwal melebihi tenggat saat ini, untung terkecil dibuang.</p></div>
    <div data-viz="regret"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'regret greedy di C++', 'desc' => 'Kursus dengan tenggat, lalu tiga pola heap lainnya.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kursus Daring</h2>
    @include('lessons.walkthrough', [
        'title' => 'Banyak kursus maksimum sebelum tenggat',
        'steps' => $gpSteps,
        'sample' => ['input' => "4\n100 200\n200 1300\n1000 1250\n2000 3200\n", 'output' => "3\n"],
        'py' => $gpPy,
    ])
    <table class="trace-table">
        <tr><th>kursus (t, d)</th><th>total</th><th>aksi</th><th>heap</th></tr>
        <tr><td>(100, 200)</td><td>100</td><td>muat</td><td>{100}</td></tr>
        <tr><td>(1000, 1250)</td><td>1100</td><td>muat</td><td>{1000, 100}</td></tr>
        <tr><td>(200, 1300)</td><td>1300</td><td>muat (tepat)</td><td>{1000, 200, 100}</td></tr>
        <tr class="hl"><td>(2000, 3200)</td><td>3300 → 1300</td><td>3300 &gt; 3200: buang 2000</td><td>{1000, 200, 100}</td></tr>
        <tr class="ok"><td colspan="3">jawaban</td><td><b>3</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="pola" data-toc="Pola Heap Lainnya">
    <h2>Pola Heap Lainnya</h2>
    <div class="prose"><p><strong>Untung maksimum, pekerjaan 1 hari.</strong> Sama seperti visualisasi: proses berdasarkan tenggat, buang untung terkecil saat jadwal kelebihan.</p></div>
    @include('lessons.code', ['cpp' => $gpUnit, 'js' => null, 'py' => null])
    <div class="prose"><p><strong>Isi bensin minimum.</strong> Jangan memutuskan di setiap pom. Lewati saja sambil mencatat bensinnya; ketika mobil tidak bisa maju lagi, "mundur dalam pikiran" dan isi dari pom terlewat yang bensinnya paling banyak.</p></div>
    @include('lessons.code', ['cpp' => $gpFuel, 'js' => null, 'py' => null])
    <div class="prose"><p><strong>Jual-beli saham.</strong> Contoh regret yang lebih licik: setiap penjualan memasukkan "tiket" seharga hari itu ke heap. Jika kelak ada harga lebih tinggi, tiket itu dipakai sebagai hari beli semu, yang secara efektif memindahkan penjualan ke hari yang lebih mahal.</p></div>
    @include('lessons.code', ['cpp' => $gpStock, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Soal</th><th>Urutan proses</th><th>Heap</th><th>Waktu</th></tr>
        <tr><td>Kursus dengan tenggat</td><td>tenggat naik</td><td>max-heap durasi</td><td><code>O(n log n)</code></td></tr>
        <tr><td>Pekerjaan 1 hari, untung</td><td>tenggat naik</td><td>min-heap untung</td><td><code>O(n log n)</code></td></tr>
        <tr><td>Isi bensin</td><td>posisi naik</td><td>max-heap liter</td><td><code>O(n log n)</code></td></tr>
        <tr><td>Saham</td><td>hari naik</td><td>min-heap harga</td><td><code>O(n log n)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Arah heap.</strong> <code>priority_queue</code> di C++ defaultnya <em>max-heap</em>. Untuk min-heap pakai <code>priority_queue&lt;T, vector&lt;T&gt;, greater&lt;T&gt;&gt;</code>; di Python, <code>heapq</code> selalu min-heap, jadi simpan nilai negatif untuk max-heap.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Urutan proses salah.</strong> Regret greedy hanya benar dalam urutan yang tepat (biasanya tenggat). Memproses berdasarkan durasi atau untung tidak menjamin hasil optimal.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'mengapa membuang yang terburuk aman', 'desc' => 'Invarian: heap berisi himpunan terbaik untuk prefiks.'])

<section class="lesson-section" id="bukti" data-toc="Bukti Regret">
    <h2>Mengapa Membuang yang Terburuk Aman</h2>
    <div class="proof">
        <p>Untuk soal kursus, klaimnya: setelah memproses k kursus pertama (urut tenggat), heap berisi himpunan kursus <em>terbanyak</em> yang bisa diselesaikan, dan di antara himpunan dengan banyak yang sama, total durasinya <em>paling kecil</em>.</p>
        <p>Saat kursus baru ditambahkan dan total masih ≤ tenggat, semuanya muat (dikerjakan urut tenggat). Jika total melewati tenggat, tidak ada himpunan sebesar |heap| + 1 yang muat: himpunan lama sudah minimal totalnya, ditambah kursus baru pun tidak muat. Jadi banyak maksimum tetap |heap|, dan membuang durasi terbesar memberi total terkecil di antara himpunan sebesar itu. Invarian terjaga.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Batas kapasitas berjalan</h4><p>"Total ≤ tenggat saat ini": proses urut tenggat + heap.</p></div>
        <div class="pattern"><h4>k terbaik dari prefiks</h4><p>Heap berukuran k: buang yang terburuk setiap kali melebihi k.</p></div>
        <div class="pattern"><h4>Tunda keputusan</h4><p>Simpan opsi yang dilewati di heap; pakai ketika terpaksa (bensin).</p></div>
        <div class="pattern"><h4>Tiket penyesalan</h4><p>Masukkan kembali nilai yang dipakai agar keputusan bisa "dipindahkan" (saham).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Banyak kursus berkurang satu apa pun yang dibuang, jadi buang yang paling banyak mengurangi total: durasi terpanjang.">
        <p class="quiz-q">Total durasi melewati tenggat. Kursus mana yang dibuang dari heap?</p>
        <div class="quiz-options">
            <button class="quiz-option">Kursus yang baru saja ditambahkan</button>
            <button class="quiz-option">Kursus dengan durasi terpanjang</button>
            <button class="quiz-option">Kursus dengan tenggat paling awal</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Proses tenggat 1: ambil 19 → {19}. Tenggat 1 berikutnya (25): {19, 25} melebihi 1, buang 19 → {25}. Tenggat 2: 100 → {25, 100}; 27 → {25, 27, 100} melebihi 2, buang 25 → {27, 100}. Tenggat 3: 15 → {15, 27, 100}. Total 142.">
        <p class="quiz-q">Pekerjaan (tenggat:untung) 2:100, 1:19, 2:27, 1:25, 3:15. Untung maksimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">142</button>
            <button class="quiz-option">152</button>
            <button class="quiz-option">127</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="priority_queue default adalah max-heap; untuk min-heap tambahkan greater<T>.">
        <p class="quiz-q"><code>priority_queue&lt;int&gt; pq;</code> lalu <code>pq.top()</code> mengembalikan…</p>
        <div class="quiz-options">
            <button class="quiz-option">elemen terkecil</button>
            <button class="quiz-option">elemen yang pertama dimasukkan</button>
            <button class="quiz-option">elemen terbesar</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
