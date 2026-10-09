@php
    $saSteps = [
        ['Membaca matriks jarak', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<long long>> d(n, vector<long long>(n));
    for (auto& baris : d)
        for (auto& x : baris) cin >> x;
CPP, <<<'TXT'
<p><strong>Soal contoh "Kurir Kecil":</strong> seorang kurir berangkat dari kota 0, harus mengunjungi semua kota lain tepat sekali, lalu kembali ke kota 0. <code>d[i][j]</code> adalah jarak kota i ke j. Berapa total jarak terpendek? n ≤ 9.</p>
<p>n kecil adalah petunjuk besar: kita boleh mencoba <strong>semua</strong> urutan kunjungan.</p>
TXT],
        ['Urutan awal harus terurut', <<<'CPP'

    vector<int> rute(n - 1);
    iota(rute.begin(), rute.end(), 1);   // 1, 2, ..., n-1
    long long terbaik = LLONG_MAX;
CPP, <<<'TXT'
<p>Kota 0 selalu menjadi awal dan akhir, jadi yang perlu diatur hanyalah urutan kota 1 … n−1. <code>iota</code> mengisi 1, 2, 3, … secara otomatis.</p>
<div class="wt-warn"><code>next_permutation</code> menghasilkan permutasi <em>berikutnya dalam urutan kamus</em>. Agar semua permutasi terjelajahi, mulailah dari urutan yang <strong>terurut menaik</strong> (permutasi terkecil).</div>
TXT],
        ['Mencoba setiap urutan', <<<'CPP'
    do {
        long long total = d[0][rute[0]];
        for (int i = 0; i + 1 < (int)rute.size(); i++)
            total += d[rute[i]][rute[i + 1]];
        total += d[rute.back()][0];
        terbaik = min(terbaik, total);
    } while (next_permutation(rute.begin(), rute.end()));
CPP, <<<'TXT'
<p>Pola <code>do { … } while (next_permutation(…))</code> menjalankan badan loop untuk urutan awal juga. Jika memakai <code>while</code> biasa, urutan pertama terlewat.</p>
<p><code>next_permutation</code> mengubah array menjadi permutasi berikutnya dan mengembalikan <code>false</code> setelah permutasi terakhir.</p>
TXT],
        ['Jawaban', <<<'CPP'

    cout << terbaik << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Ada (n−1)! urutan, masing-masing dihitung dalam O(n). Untuk n = 9: 8! · 9 ≈ 360 000 langkah. Untuk n = 13 sudah 12! ≈ 4,8 · 10<sup>8</sup> urutan: terlalu lambat. Untuk n besar dibutuhkan DP bitmask (track DP).</p>
TXT],
    ];

    $saPy = <<<'PY'
import sys
from itertools import permutations
data = sys.stdin.read().split()
n = int(data[0])
d = [list(map(int, data[1 + i*n:1 + (i+1)*n])) for i in range(n)]
terbaik = float('inf')
for rute in permutations(range(1, n)):        # urutan kamus, mulai dari yang terurut
    total = d[0][rute[0]] + sum(d[rute[i]][rute[i+1]] for i in range(len(rute) - 1)) + d[rute[-1]][0]
    terbaik = min(terbaik, total)
print(terbaik)
PY;

    $saBound = <<<'CPP'
vector<int> a = {1, 3, 3, 5, 7, 7, 7, 9};          // WAJIB terurut
int kurang7    = lower_bound(a.begin(), a.end(), 7) - a.begin();   // 4: banyak < 7
int palingTinggi7 = upper_bound(a.begin(), a.end(), 7) - a.begin(); // 7: banyak <= 7
int muncul7    = palingTinggi7 - kurang7;                          // 3 kemunculan
// banyak nilai di rentang [l, r]:
int diRentang  = upper_bound(a.begin(), a.end(), r) - lower_bound(a.begin(), a.end(), l);
CPP;

    $saUnique = <<<'CPP'
vector<int> b = a;                           // kerjakan pada salinan
sort(b.begin(), b.end());
b.erase(unique(b.begin(), b.end()), b.end()); // idiom tiga langkah dalam satu baris
// b sekarang berisi nilai-nilai berbeda, terurut
CPP;

    $saMisc = <<<'CPP'
long long s = accumulate(a.begin(), a.end(), 0LL);  // 0LL! dengan 0 hasilnya int → overflow
int mx = *max_element(a.begin(), a.end());          // nilai
int pos = max_element(a.begin(), a.end()) - a.begin(); // indeks
reverse(a.begin(), a.end());
rotate(a.begin(), a.begin() + k, a.end());          // geser kiri sejauh k
fill(a.begin(), a.end(), -1);
int c = count(a.begin(), a.end(), 7);               // O(n)
iota(a.begin(), a.end(), 0);                        // 0, 1, 2, ...
partial_sum(a.begin(), a.end(), pre.begin());       // prefix sum
nth_element(a.begin(), a.begin() + k, a.end());     // a[k] = elemen ke-k terkecil, rata-rata O(n)
bool ada = binary_search(a.begin(), a.end(), 7);    // data harus terurut
__builtin_popcount(x);  __builtin_ctz(x);           // banyak bit 1, nol di ujung kanan
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai <code>lower_bound</code>/<code>upper_bound</code> untuk menghitung banyak nilai di bawah, di atas, atau di dalam rentang.</li>
        <li>Membuang duplikat dengan idiom <code>sort + unique + erase</code> dan tahu mengapa <code>sort</code> wajib lebih dulu.</li>
        <li>Mencoba semua urutan dengan <code>next_permutation</code>, dan tahu kapan n terlalu besar.</li>
        <li>Menghindari jebakan <code>accumulate</code> yang overflow dan <code>lower_bound</code> pada data tak terurut.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'kotak perkakas', 'desc' => 'Fungsi-fungsi bawaan yang menghemat puluhan baris kode.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Perkakas, Bukan Solusi</h2>
    <div class="prose">
        <p>Fungsi STL jarang menjadi solusi utuh, tetapi hampir selalu ada di dalamnya. Mencari nama di daftar hadir yang urut? Buka tengahnya (<code>lower_bound</code>). Merapikan daftar tamu yang punya nama kembar? Urutkan dulu agar yang kembar bersebelahan, baru coret yang berulang (<code>sort + unique</code>). Mencoba semua cara mengurutkan kunjungan? <code>next_permutation</code>.</p>
    </div>
    <table class="cx-table">
        <tr><th>Fungsi</th><th>Waktu</th><th>Syarat / catatan</th></tr>
        <tr><td><code>sort</code></td><td><code>O(n log n)</code></td><td>Comparator harus strict weak ordering</td></tr>
        <tr><td><code>lower_bound</code>, <code>upper_bound</code>, <code>binary_search</code></td><td><code>O(log n)</code></td><td>Data <strong>wajib</strong> sudah terurut</td></tr>
        <tr><td><code>unique</code></td><td><code>O(n)</code></td><td>Hanya membuang kembar yang <strong>bersebelahan</strong></td></tr>
        <tr><td><code>next_permutation</code></td><td><code>O(n)</code> per panggilan</td><td>Semua permutasi: n! panggilan, praktis n ≤ 10–11</td></tr>
        <tr><td><code>nth_element</code></td><td><code>O(n)</code> rata-rata</td><td>Elemen ke-k tanpa mengurutkan semuanya</td></tr>
        <tr><td><code>accumulate</code>, <code>count</code>, <code>max_element</code>, <code>reverse</code></td><td><code>O(n)</code></td><td><code>accumulate(…, 0LL)</code></td></tr>
    </table>
</section>

<section class="lesson-section" id="batas" data-toc="lower_bound & upper_bound">
    <h2>Dua Fungsi, Empat Pertanyaan</h2>
    <div class="prose"><p>Pada array terurut, <code>lower_bound(x)</code> menunjuk salinan x yang <strong>pertama</strong>, dan <code>upper_bound(x)</code> menunjuk tepat <strong>sesudah</strong> salinan terakhir. Indeks keduanya langsung menjawab pertanyaan menghitung.</p></div>
    @include('lessons.code', ['cpp' => $saBound, 'js' => null, 'py' => null])
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>indeks</th><td>0</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td></tr>
            <tr><th>a</th><td>1</td><td>3</td><td>3</td><td>5</td><td class="new">7</td><td>7</td><td>7</td><td>9</td></tr>
            <tr class="hl"><th>x = 7</th><td colspan="4">&lt; 7 (4 buah)</td><td colspan="3">lower_bound = 4 … upper_bound = 7</td><td>&gt; 7</td></tr>
        </table>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Pada data yang <strong>belum terurut</strong>, lower_bound tetap mengembalikan sesuatu, tetapi salah, tanpa peringatan apa pun.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: unique dan next_permutation</h2>
    <div class="prose">
        <p>Mode <strong>sort + unique + erase</strong> memperlihatkan dua penunjuk di dalam <code>unique</code>: penunjuk baca menyusuri array, penunjuk tulis hanya maju saat menemukan nilai baru. Mode <strong>next_permutation</strong> memperlihatkan empat langkah algoritmanya; coba array <code>1 3 5 4 2</code> atau <code>2 4 3 1</code>.</p>
    </div>
    <div data-viz="stl-algo"></div>
    @include('lessons.code', ['cpp' => $saUnique, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 2, 'title' => 'perkakas di dalam solusi', 'desc' => 'Mencoba semua permutasi, plus daftar fungsi pendukung.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kurir Kecil</h2>
    @include('lessons.walkthrough', [
        'title' => 'Mencoba semua rute dengan next_permutation',
        'steps' => $saSteps,
        'sample' => ['input' => "4\n0 10 15 20\n10 0 35 25\n15 35 0 30\n20 25 30 0\n", 'output' => "80\n"],
        'py' => $saPy,
    ])
</section>

<section class="lesson-section" id="lainnya" data-toc="Fungsi Pendukung">
    <h2>Fungsi Pendukung yang Sering Dipakai</h2>
    @include('lessons.code', ['cpp' => $saMisc, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>accumulate mengambil tipe dari nilai awal.</strong> <code>accumulate(a.begin(), a.end(), 0)</code> menjumlahkan dalam <code>int</code>; jumlah 2 · 10<sup>5</sup> bilangan sebesar 10<sup>9</sup> akan overflow menjadi bilangan negatif acak. Tulis <code>0LL</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>next_permutation dari urutan acak</strong> hanya menjelajahi permutasi yang "lebih besar" darinya. Selalu <code>sort</code> dulu jika ingin semua.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'di balik layar', 'desc' => 'Mengapa next_permutation benar, dan menghitung permutasi berulang.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa next_permutation Benar?">
    <h2>Mengapa next_permutation Benar?</h2>
    <div class="proof">
        <p>Ambil ekor terpanjang yang menurun, misalnya pada <code>1 3 <b>5 4 2</b></code>. Ekor yang menurun adalah susunan <em>terbesar</em> untuk elemen-elemen itu, jadi permutasi berikutnya pasti mengubah elemen sebelum ekor, yaitu 3.</p>
        <p>Agar kenaikannya sekecil mungkin, 3 diganti dengan elemen ekor yang <strong>sedikit lebih besar</strong> darinya (4), bukan yang lain. Setelah ditukar (<code>1 4 5 3 2</code>), ekor masih menurun; supaya hasilnya sekecil mungkin, ekor dibalik menjadi menaik: <code>1 4 2 3 5</code>.</p>
        <p>Setiap langkah membaca paling banyak n elemen, jadi satu panggilan O(n). Untuk array dengan nilai kembar, algoritma ini otomatis melewati permutasi yang sama, karena pemeriksaannya memakai ≥ dan ≤.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Semua permutasi</h4><p><code>sort</code> lalu <code>do … while (next_permutation)</code>, n ≤ 10.</p></div>
        <div class="pattern"><h4>Hitung kemunculan</h4><p><code>upper_bound(x) − lower_bound(x)</code>.</p></div>
        <div class="pattern"><h4>Nilai di [l, r]</h4><p><code>upper_bound(r) − lower_bound(l)</code>.</p></div>
        <div class="pattern"><h4>Banyak nilai berbeda</h4><p><code>sort + unique</code>, lalu ukur panjangnya.</p></div>
        <div class="pattern"><h4>Median</h4><p><code>nth_element</code> pada posisi n/2.</p></div>
        <div class="pattern"><h4>Permutasi ke-k</h4><p>Sistem bilangan faktorial (factoradic), bukan k kali next_permutation.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="unique hanya membandingkan elemen bersebelahan. Pada [3, 1, 3], tidak ada dua 3 yang bersebelahan, sehingga tidak ada yang dibuang.">
        <p class="quiz-q">Apa hasil <code>unique</code> (tanpa sort) pada [3, 1, 3]?</p>
        <div class="quiz-options">
            <button class="quiz-option">[3, 1]</button>
            <button class="quiz-option">[3, 1, 3] (tidak berubah)</button>
            <button class="quiz-option">[1, 3]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Ekor menurun [4, 3]; elemen yang naik adalah 2, ditukar dengan 3 (terkecil yang > 2), lalu ekor dibalik: 1 3 2 4.">
        <p class="quiz-q">Permutasi berikutnya dari <code>1 2 4 3</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">1 4 2 3</button>
            <button class="quiz-option">1 3 4 2</button>
            <button class="quiz-option">1 3 2 4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Nilai awal 0 bertipe int, sehingga jumlahnya dihitung dalam int dan overflow. 0LL membuat penjumlahan memakai long long.">
        <p class="quiz-q">Mengapa <code>accumulate(a.begin(), a.end(), 0)</code> bisa memberi hasil negatif pada <code>vector&lt;long long&gt;</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Tipe hasil mengikuti nilai awal (int)</button>
            <button class="quiz-option">accumulate tidak mendukung long long</button>
            <button class="quiz-option">Array harus diurutkan dulu</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
