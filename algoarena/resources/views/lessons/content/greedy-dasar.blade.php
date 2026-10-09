@php
    $gdSteps = [
        ['Baca data', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    long long k;
    cin >> n >> m >> k;
    vector<long long> a(n), b(m);       // a: ukuran yang diinginkan, b: ukuran apartemen
    for (auto& x : a) cin >> x;
    for (auto& x : b) cin >> x;
CPP, <<<'TXT'
<p><strong>Soal contoh "Apartemen":</strong> ada n pelamar dan m apartemen. Pelamar ke-i mau menerima apartemen berukuran b asalkan |a<sub>i</sub> − b| ≤ k. Setiap pelamar mendapat paling banyak satu apartemen, dan setiap apartemen untuk paling banyak satu pelamar. Berapa pelamar paling banyak yang bisa mendapat apartemen?</p>
TXT],
        ['Urutkan keduanya', <<<'CPP'

    sort(a.begin(), a.end());
    sort(b.begin(), b.end());
CPP, <<<'TXT'
<p>Hampir semua greedy diawali dengan <strong>mengurutkan</strong>. Setelah terurut, pelamar terkecil dan apartemen terkecil bisa diputuskan lebih dulu tanpa memikirkan sisanya.</p>
TXT],
        ['Pasangkan dari yang terkecil', <<<'CPP'

    int i = 0, j = 0, cocok = 0;
    while (i < n && j < m) {
        if (b[j] < a[i] - k) j++;           // apartemen terlalu kecil untuk pelamar ini dan semua sesudahnya
        else if (b[j] > a[i] + k) i++;      // pelamar ini tidak cocok dengan apartemen mana pun yang tersisa
        else {                              // cocok: pasangkan sekarang
            cocok++;
            i++;
            j++;
        }
    }
    cout << cocok << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Tiga kasus, dan setiap kasus aman:</p>
<ul><li>Apartemen j terlalu kecil untuk pelamar i, padahal pelamar berikutnya lebih besar: apartemen j tidak berguna bagi siapa pun. Buang.</li>
<li>Apartemen j terlalu besar untuk pelamar i, padahal apartemen berikutnya lebih besar lagi: pelamar i tidak akan pernah dapat. Buang.</li>
<li>Cocok: memasangkan keduanya tidak pernah merugikan (lihat bukti exchange argument di bawah).</li></ul>
<p>Total O(n log n + m log m) untuk mengurutkan, lalu O(n + m).</p>
TXT],
    ];

    $gdPy = <<<'PY'
import sys

data = sys.stdin.buffer.read().split()
n, m, k = int(data[0]), int(data[1]), int(data[2])
a = sorted(map(int, data[3:3 + n]))
b = sorted(map(int, data[3 + n:3 + n + m]))
i = j = cocok = 0
while i < n and j < m:
    if b[j] < a[i] - k:
        j += 1
    elif b[j] > a[i] + k:
        i += 1
    else:
        cocok += 1
        i += 1
        j += 1
print(cocok)
PY;

    $gdBrute = <<<'CPP'
// Membantah greedy dengan cepat: bandingkan dengan brute force pada input kecil acak
for (int iter = 0; iter < 10000; iter++) {
    vector<int> koin = acakKoin();            // misalnya {1, 3, 4}
    int X = rand() % 30 + 1;
    if (greedy(koin, X) != dpOptimal(koin, X)) {
        cout << "contoh penyangkal: X = " << X << "\n";   // simpan, lalu pikirkan ulang idenya
        break;
    }
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan ide <strong>greedy</strong>: mengambil keputusan terbaik saat ini tanpa pernah membatalkannya.</li>
        <li>Membuktikan greedy dengan <strong>exchange argument</strong> dan <strong>greedy stays ahead</strong>.</li>
        <li>Membantah greedy yang salah dengan <strong>contoh penyangkal</strong> kecil.</li>
        <li>Mengenali tanda soal yang butuh DP, bukan greedy.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'pilihan serakah', 'desc' => 'Ambil yang terbaik sekarang, dan jangan menoleh ke belakang.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Kasir dan Kembalian">
    <h2>Intuisi: Kasir dan Uang Kembalian</h2>
    <div class="prose">
        <p>Kasir yang memberi kembalian 87 ribu dengan lembar sesedikit mungkin tidak menghitung semua kemungkinan. Ia mengambil lembar terbesar yang muat (50), lalu terbesar lagi (20), lalu 10, 5, 2: lima lembar. Strategi seperti ini disebut <strong>greedy</strong> (serakah): di setiap langkah ambil pilihan yang terlihat terbaik, dan jangan pernah membatalkannya.</p>
        <p>Greedy sangat cepat dan kodenya pendek. Masalahnya, greedy <em>sering salah</em>. Dengan koin {1, 3, 4}, greedy membayar 6 sebagai 4 + 1 + 1 (tiga koin), padahal 3 + 3 cukup dua koin. Jadi setiap greedy butuh <strong>alasan</strong> mengapa pilihan serakah tidak merugikan.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Pilihan serakah</b><span>Keputusan lokal terbaik menurut satu kriteria (terbesar, terkecil, selesai paling awal, …).</span></div>
        <div class="term"><b>Exchange argument</b><span>Ubah solusi optimal mana pun menjadi solusi greedy, langkah demi langkah, tanpa membuatnya lebih buruk.</span></div>
        <div class="term"><b>Greedy stays ahead</b><span>Setelah setiap langkah, greedy selalu "setidaknya sebaik" solusi lain.</span></div>
        <div class="term"><b>Contoh penyangkal</b><span>Satu input kecil di mana greedy kalah. Cukup satu untuk membuktikan greedy salah.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Greedy Koin</h2>
    <div class="prose"><p>Coba sistem koin Rupiah dan koin Amerika: greedy selalu optimal. Lalu pilih koin {1, 3, 4} dengan jumlah 6: di akhir, greedy dibandingkan dengan jawaban optimal dari DP.</p></div>
    <div data-viz="greedy-coin"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Mendesain Greedy">
    <h2>Cara Mendesain (dan Menguji) Greedy</h2>
    <div class="steps">
        <div class="step-card"><b>Tebak kriteria</b><span>Urutkan berdasarkan apa? Terkecil dulu, terbesar dulu, selesai paling awal, rasio terbaik? Biasanya ada 2–3 kandidat.</span></div>
        <div class="step-card"><b>Cari contoh penyangkal</b><span>Coba kasus kecil yang "nakal" dengan tangan. Jika greedy kalah, buang kriterianya.</span></div>
        <div class="step-card"><b>Buktikan</b><span>Exchange argument: ambil solusi optimal yang berbeda dari greedy di langkah pertama, tukar, dan tunjukkan tidak memburuk.</span></div>
        <div class="step-card"><b>Uji dengan brute force</b><span>Jika bukti terasa goyah, bandingkan dengan solusi lambat pada ribuan input acak kecil.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'greedy di C++', 'desc' => 'Pasangkan yang kecil dengan yang kecil, ditulis lengkap.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Apartemen</h2>
    @include('lessons.walkthrough', [
        'title' => 'Mencocokkan pelamar dan apartemen',
        'steps' => $gdSteps,
        'sample' => ['input' => "4 3 5\n60 45 80 60\n30 60 75\n", 'output' => "2\n"],
        'py' => $gdPy,
    ])
    <table class="trace-table">
        <tr><th>a[i]</th><th>b[j]</th><th>keputusan</th><th>cocok</th></tr>
        <tr><td colspan="4">a terurut = 45 60 60 80, b terurut = 30 60 75, k = 5</td></tr>
        <tr><td>45</td><td>30</td><td>30 &lt; 45 − 5: buang apartemen</td><td>0</td></tr>
        <tr><td>45</td><td>60</td><td>60 &gt; 45 + 5: buang pelamar</td><td>0</td></tr>
        <tr class="hl"><td>60</td><td>60</td><td>cocok</td><td>1</td></tr>
        <tr><td>60</td><td>75</td><td>75 &gt; 65: buang pelamar</td><td>1</td></tr>
        <tr class="ok"><td>80</td><td>75</td><td>cocok</td><td><b>2</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="bukti" data-toc="Exchange Argument">
    <h2>Membuktikan dengan Exchange Argument</h2>
    <div class="proof">
        <p>Lihat pelamar terkecil a<sub>1</sub> dan apartemen terkecil yang cocok untuknya, b<sub>j</sub>. Ambil solusi optimal O mana pun.</p>
        <ul>
            <li>Jika di O pelamar a<sub>1</sub> tidak mendapat apa-apa dan b<sub>j</sub> kosong, tambahkan pasangan (a<sub>1</sub>, b<sub>j</sub>): O malah membaik, mustahil.</li>
            <li>Jika di O b<sub>j</sub> dipakai pelamar lain a<sub>x</sub> dan a<sub>1</sub> mendapat b<sub>y</sub> (atau tidak dapat), tukar: berikan b<sub>j</sub> ke a<sub>1</sub> dan b<sub>y</sub> ke a<sub>x</sub>. Karena a<sub>1</sub> ≤ a<sub>x</sub> dan b<sub>j</sub> ≤ b<sub>y</sub>, pasangan baru tetap memenuhi |a − b| ≤ k.</li>
        </ul>
        <p>Dalam semua kasus, ada solusi optimal yang memuat pasangan greedy (a<sub>1</sub>, b<sub>j</sub>). Buang keduanya dan ulangi argumen yang sama untuk sisa input: greedy optimal.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Pola bukti ini berlaku untuk banyak greedy: <em>"ada solusi optimal yang mengambil pilihan greedy pertama"</em>, lalu induksi pada sisa masalah.</p>
    </div>
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Pola</th><th>Contoh</th><th>Waktu</th></tr>
        <tr><td>Urutkan lalu sapu</td><td>Apartemen, jadwal kegiatan</td><td><code>O(n log n)</code></td></tr>
        <tr><td>Selalu ambil terbesar/terkecil yang tersisa</td><td>Huffman, penjadwalan dengan heap</td><td><code>O(n log n)</code></td></tr>
        <tr><td>Dua pointer di dua ujung</td><td>Perahu berkapasitas dua orang</td><td><code>O(n log n)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>"Kelihatannya benar" bukan bukti.</strong> Knapsack 0/1 dengan rasio nilai/berat terbaik, koin {1, 3, 4}, dan jalur dengan langkah termurah dulu adalah greedy yang terkenal salah. Jika kamu tidak bisa membuktikan, uji dengan brute force.</p>
    </div>
    @include('lessons.code', ['cpp' => $gdBrute, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p><strong>Greedy atau DP?</strong> Jika keputusan sekarang bisa "merugikan" pilihan nanti dengan cara yang tidak bisa dibandingkan secara lokal (misalnya kapasitas tas yang tersisa), biasanya butuh DP. Jika ada urutan alami di mana keputusan terbaik tidak bergantung pada masa depan, coba greedy.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'teknik bukti lanjutan', 'desc' => 'Greedy stays ahead, tukar tetangga, dan pola umum.'])

<section class="lesson-section" id="lanjut" data-toc="Bukti Lanjutan & Pola">
    <h2>Teknik Bukti Lanjutan dan Pola Umum</h2>
    <div class="prose">
        <p><strong>Greedy stays ahead.</strong> Bandingkan greedy dengan solusi optimal langkah demi langkah dan tunjukkan greedy tidak pernah tertinggal. Contoh: memilih kegiatan yang selesai paling awal. Setelah k pilihan, kegiatan ke-k greedy selesai tidak lebih lambat dari kegiatan ke-k solusi mana pun, sehingga greedy selalu punya ruang untuk kegiatan berikutnya.</p>
        <p><strong>Tukar tetangga.</strong> Untuk soal mengurutkan (urutan mengerjakan tugas), lihat dua tugas yang berdampingan i lalu j. Tukar keduanya dan hitung perubahan biaya; tugas lain tidak terpengaruh. Jika menukar tidak pernah merugikan saat i "lebih baik" dari j menurut suatu kriteria, maka mengurutkan berdasarkan kriteria itu optimal.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Pasangkan terurut</h4><p>Kecil dengan kecil (toleransi), atau kecil dengan besar (perahu, kerja kelompok).</p></div>
        <div class="pattern"><h4>Selesai paling awal</h4><p>Memilih interval terbanyak yang tidak bentrok.</p></div>
        <div class="pattern"><h4>Durasi terpendek dulu</h4><p>Meminimalkan total waktu tunggu: tukar tetangga.</p></div>
        <div class="pattern"><h4>Ambil dulu, buang nanti</h4><p>Heap untuk "menyesal" saat batas terlampaui.</p></div>
        <div class="pattern"><h4>Ketidaksamaan susunan ulang</h4><p>Σ a<sub>i</sub>b<sub>i</sub> maksimum jika keduanya diurutkan searah, minimum jika berlawanan.</p></div>
        <div class="pattern"><h4>Dari kiri dengan kelayakan</h4><p>Bangun jawaban leksikografis terkecil: pilih terkecil yang masih menyisakan jawaban sah.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Greedy mengambil 4 + 1 + 1 = 3 koin, padahal 3 + 3 = 2 koin. Satu contoh penyangkal sudah cukup.">
        <p class="quiz-q">Koin {1, 3, 4}. Untuk jumlah berapa greedy "koin terbesar dulu" gagal?</p>
        <div class="quiz-options">
            <button class="quiz-option">5</button>
            <button class="quiz-option">6</button>
            <button class="quiz-option">8</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Exchange argument mengubah solusi optimal mana pun menjadi solusi greedy tanpa memperburuknya, sehingga greedy pasti optimal.">
        <p class="quiz-q">Apa inti exchange argument?</p>
        <div class="quiz-options">
            <button class="quiz-option">Solusi optimal mana pun bisa ditukar menjadi solusi greedy tanpa memburuk</button>
            <button class="quiz-option">Greedy dicoba pada semua input kecil</button>
            <button class="quiz-option">Greedy selalu menukar dua elemen bersebelahan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Untuk knapsack 0/1, memilih rasio terbaik bisa menyisakan ruang yang terbuang. Contoh: kapasitas 10, barang (berat 6, nilai 7) dan dua barang (berat 5, nilai 5). Rasio terbaik memilih yang 6 (nilai 7), padahal dua barang berat 5 memberi 10.">
        <p class="quiz-q">Greedy mana yang TIDAK benar?</p>
        <div class="quiz-options">
            <button class="quiz-option">Pilih kegiatan yang selesai paling awal untuk memaksimalkan banyak kegiatan</button>
            <button class="quiz-option">Bayar dengan uang kertas Rupiah terbesar dulu</button>
            <button class="quiz-option">Knapsack 0/1: ambil barang dengan rasio nilai/berat terbaik dulu</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
