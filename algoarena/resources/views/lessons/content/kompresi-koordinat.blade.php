@php
    $kkSteps = [
        ['Kumpulkan semua koordinat penting', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> L(n), R(n), xs;
    for (int i = 0; i < n; i++) {
        cin >> L[i] >> R[i];         // interval [L, R)
        xs.push_back(L[i]);
        xs.push_back(R[i]);
    }
CPP, <<<'TXT'
<p><strong>Soal contoh "Titik Tersibuk":</strong> ada n interval setengah terbuka [L, R) dengan koordinat sampai 10<sup>9</sup>. Berapa banyak interval terbanyak yang menutupi satu titik yang sama?</p>
<p>Dengan koordinat kecil, kita cukup memakai difference array. Tetapi array sebesar 10<sup>9</sup> mustahil. Perhatikan: banyak interval aktif hanya <strong>berubah</strong> di ujung-ujung interval. Hanya 2n koordinat itulah yang penting.</p>
TXT],
        ['Kompresi: sort, unique, erase', <<<'CPP'

    sort(xs.begin(), xs.end());
    xs.erase(unique(xs.begin(), xs.end()), xs.end());
    int k = xs.size();
    auto id = [&](long long x) {
        return int(lower_bound(xs.begin(), xs.end(), x) - xs.begin());
    };
CPP, <<<'TXT'
<p>Setelah idiom tiga langkah, <code>xs</code> berisi k ≤ 2n koordinat berbeda yang terurut. Fungsi <code>id(x)</code> memberi nomor 0..k−1 untuk setiap koordinat, dan urutannya terjaga: x &lt; y ⇔ id(x) &lt; id(y).</p>
TXT],
        ['Difference array atas nomor baru', <<<'CPP'

    vector<int> d(k + 1, 0);
    for (int i = 0; i < n; i++) {
        d[id(L[i])]++;
        d[id(R[i])]--;
    }
CPP, <<<'TXT'
<p>Sekarang difference array cukup berukuran k + 1. Interval [L, R) menjadi [id(L), id(R)) pada koordinat terkompresi.</p>
TXT],
        ['Sapuan dan jawaban', <<<'CPP'
    int aktif = 0, terbanyak = 0;
    for (int i = 0; i < k; i++) {
        aktif += d[i];
        terbanyak = max(terbanyak, aktif);
    }
    cout << terbanyak << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Total O(n log n) karena sort dan lower_bound. Teknik yang sama membuka jalan bagi Fenwick tree dan segment tree atas nilai raksasa (track Struktur Data).</p>
TXT],
    ];

    $kkPy = <<<'PY'
import sys
from bisect import bisect_left
data = sys.stdin.read().split()
n = int(data[0])
L = [int(data[1 + 2*i]) for i in range(n)]
R = [int(data[2 + 2*i]) for i in range(n)]
xs = sorted(set(L + R))
d = [0] * (len(xs) + 1)
for l, r in zip(L, R):
    d[bisect_left(xs, l)] += 1
    d[bisect_left(xs, r)] -= 1
aktif = terbanyak = 0
for v in d:
    aktif += v
    terbanyak = max(terbanyak, aktif)
print(terbanyak)
PY;

    $kkIdiom = <<<'CPP'
// Idiom kompresi koordinat
vector<long long> b = a;                                // 1. salin
sort(b.begin(), b.end());                               // 2. urutkan
b.erase(unique(b.begin(), b.end()), b.end());           // 3. buang kembar
for (auto& x : a)                                       // 4. ganti dengan peringkat
    x = lower_bound(b.begin(), b.end(), x) - b.begin(); //    0 .. k-1
// nilai asli peringkat r: b[r]
CPP;

    $kkMap = <<<'CPP'
// Alternatif dengan map (lebih lambat, tetapi bisa menambah nilai sambil jalan)
map<long long, int> nomor;
for (long long x : a) nomor[x];          // kumpulkan kunci
int k = 0;
for (auto& kv : nomor) kv.second = k++;  // urut kunci → 0, 1, 2, ...
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengganti nilai sampai 10<sup>9</sup> dengan peringkat 0..k−1 tanpa mengubah urutannya.</li>
        <li>Menulis idiom <code>sort + unique + erase + lower_bound</code> tanpa ragu.</li>
        <li>Memakai difference array dan array frekuensi pada koordinat raksasa.</li>
        <li>Menghindari jebakan: lupa unique, mengompres array asli, dan kehilangan nilai asli.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'yang penting urutannya', 'desc' => 'Mengganti nilai besar dengan peringkatnya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Nomor Punggung">
    <h2>Intuisi: Nomor Punggung Pelari</h2>
    <div class="prose">
        <p>Nomor punggung pelari bisa sampai jutaan, padahal pesertanya hanya 200 orang. Untuk mencatat siapa finis lebih dulu, cukup beri mereka nomor urut 1 sampai 200 sesuai nomor punggungnya. <strong>Urutan</strong> antar pelari tidak berubah sedikit pun.</p>
        <p>Banyak soal hanya peduli pada perbandingan (lebih kecil, lebih besar, sama), bukan pada jarak antar nilai. Untuk soal seperti itu, nilai 10<sup>9</sup> bisa diganti dengan peringkatnya, sehingga bisa dipakai sebagai <strong>indeks array</strong>.</p>
    </div>
    @include('lessons.code', ['cpp' => $kkIdiom, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi Kompresi</h2>
    <div class="prose"><p>b menjadi kamus dua arah: dari peringkat ke nilai (<code>b[r]</code>) dan dari nilai ke peringkat (<code>lower_bound</code>). Nilai kembar mendapat peringkat yang sama.</p></div>
    <div data-viz="compress"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Langkah</th><th>Isi</th></tr>
            <tr><td>a</td><td>1000000, 5, 999, −3, 5</td></tr>
            <tr class="hl"><td>sort(b)</td><td>−3, 5, 5, 999, 1000000</td></tr>
            <tr class="hl"><td>unique + erase</td><td>−3, 5, 999, 1000000 (k = 4)</td></tr>
            <tr class="ok"><td>a terkompresi</td><td>3, 1, 2, 0, 1</td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'kompresi di C++', 'desc' => 'Menggabungkan kompresi dengan difference array.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Titik Tersibuk</h2>
    @include('lessons.walkthrough', [
        'title' => 'Kompresi + difference array',
        'steps' => $kkSteps,
        'sample' => ['input' => "4\n1 1000000000\n500 700\n600 650\n640 1000\n", 'output' => "4\n"],
        'py' => $kkPy,
    ])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Langkah</th><th>Waktu</th></tr>
        <tr><td>sort + unique</td><td><code>O(n log n)</code></td></tr>
        <tr><td>Setiap pencarian peringkat</td><td><code>O(log k)</code></td></tr>
        <tr><td>Struktur atas peringkat</td><td>array berukuran k, bukan 10<sup>9</sup></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa unique.</strong> Tanpa membuang kembar, nilai sama tetap mendapat peringkat sama (lower_bound menunjuk salinan pertama), tetapi ada peringkat yang tidak pernah dipakai dan k lebih besar dari seharusnya. Untuk soal "berapa nilai berbeda", jawabannya salah.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Mengurutkan array asli.</strong> Kerjakan pada salinan. Urutan input biasanya masih dibutuhkan.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jarak hilang.</strong> Setelah kompresi, 5 dan 999 bisa menjadi 1 dan 2. Jika soal butuh panjang atau jarak (misalnya total panjang yang tertutup), hitung dengan nilai asli <code>b[i+1] − b[i]</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'kompresi sebagai pintu masuk', 'desc' => 'Kapan wajib, dan variasinya.'])

<section class="lesson-section" id="lanjut" data-toc="Variasi & Pola">
    <h2>Variasi dan Pola</h2>
    @include('lessons.code', ['cpp' => $kkMap, 'js' => null, 'py' => null])
    <div class="proof">
        <p>Mengapa urutan terjaga? b terurut tegas (tanpa kembar). Untuk x, y yang muncul di a, lower_bound mengembalikan posisi tepat x dan y di b. Karena b naik tegas, x &lt; y ⇔ posisi x &lt; posisi y, dan x = y ⇔ posisinya sama. Semua perbandingan antar elemen a tidak berubah.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Frekuensi nilai besar</h4><p>Kompres, lalu array cnt[k].</p></div>
        <div class="pattern"><h4>Interval dengan koordinat besar</h4><p>Kompres ujung-ujungnya, lalu difference array.</p></div>
        <div class="pattern"><h4>Panjang yang tertutup</h4><p>Segmen [b[i], b[i+1]) berbobot b[i+1] − b[i].</p></div>
        <div class="pattern"><h4>Inversi / Fenwick atas nilai</h4><p>Kompres dulu, lalu BIT berukuran k.</p></div>
        <div class="pattern"><h4>Query offline</h4><p>Ikutkan nilai dari query saat mengompres.</p></div>
        <div class="pattern"><h4>Grid raksasa</h4><p>Kompres baris dan kolom terpisah.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="b = [−7, 3, 10]. 10 berada di indeks 2, −7 di 0, 3 di 1. Hasil: [2, 0, 1, 2].">
        <p class="quiz-q">Kompres a = [10, −7, 3, 10]. Hasilnya?</p>
        <div class="quiz-options">
            <button class="quiz-option">[3, 0, 1, 2]</button>
            <button class="quiz-option">[2, 0, 1, 2]</button>
            <button class="quiz-option">[1, 2, 0, 1]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Kompresi hanya menjaga urutan. Total panjang butuh jarak asli, yang hilang setelah kompresi; hitung dengan b[i+1] − b[i].">
        <p class="quiz-q">Informasi apa yang <strong>hilang</strong> setelah kompresi?</p>
        <div class="quiz-options">
            <button class="quiz-option">Urutan antar nilai</button>
            <button class="quiz-option">Nilai mana yang kembar</button>
            <button class="quiz-option">Jarak antar nilai</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="unique hanya membuang kembar yang bersebelahan, jadi harus didahului sort.">
        <p class="quiz-q">Mengapa <code>sort</code> harus sebelum <code>unique</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">unique hanya membuang kembar yang bersebelahan</button>
            <button class="quiz-option">Agar lower_bound lebih cepat</button>
            <button class="quiz-option">Tidak harus</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
