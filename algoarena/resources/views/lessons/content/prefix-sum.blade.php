@php
    $psSteps = [
        ['Pertanyaan yang diubah', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long K;
    cin >> n >> K;
CPP, <<<'TXT'
<p><strong>Soal contoh "Subarray Berjumlah K":</strong> diberikan n bilangan (boleh negatif) dan K. Ada berapa pasang (l, r) sehingga <code>a[l] + … + a[r] = K</code>?</p>
<p>Mencoba semua pasangan butuh O(n²). Dengan prefix sum, syaratnya berubah bentuk:</p>
<p style="text-align:center"><code>pre[r+1] − pre[l] = K</code> ⟺ <code>pre[l] = pre[r+1] − K</code></p>
<p>Jadi untuk setiap ujung kanan, kita hanya perlu tahu: <em>berapa kali nilai <code>pre − K</code> sudah muncul sebelumnya?</em></p>
TXT],
        ['Menghitung prefix yang sudah lewat', <<<'CPP'

    map<long long, long long> pernah;   // nilai prefix → berapa kali muncul
    pernah[0] = 1;                       // pre[0] = 0 (array kosong)
    long long pre = 0, jawaban = 0;
CPP, <<<'TXT'
<p><code>pernah[x]</code> mencatat berapa banyak indeks l (yang sudah dilewati) dengan <code>pre[l] = x</code>.</p>
<p><code>pernah[0] = 1</code> mewakili <code>pre[0] = 0</code>. Tanpanya, subarray yang dimulai dari indeks pertama tidak akan pernah terhitung.</p>
TXT],
        ['Satu kali jalan', <<<'CPP'
    for (int i = 0; i < n; i++) {
        long long a;
        cin >> a;
        pre += a;                                  // pre = pre[i+1]
        auto it = pernah.find(pre - K);
        if (it != pernah.end()) jawaban += it->second;
        pernah[pre]++;                             // catat SETELAH menghitung
    }
CPP, <<<'TXT'
<p>Urutan dua baris terakhir penting: kita menghitung pasangan dengan l yang <strong>sebelum</strong> r, baru kemudian mencatat prefix saat ini. Jika dicatat lebih dulu, untuk K = 0 kita akan menghitung subarray kosong.</p>
<pre>a   :    1   2   1  -1   2   1     K = 3
pre : 0  1   3   4   3   5   6
pre=1 → cari −2 → 0
pre=3 → cari  0 → 1   [1,2]
pre=4 → cari  1 → 1   [2,1]
pre=3 → cari  0 → 1   [1,2,1,−1]
pre=5 → cari  2 → 0
pre=6 → cari  3 → 2   [1,−1,2,1] dan [2,1]
total = 5</pre>
TXT],
        ['Jawaban', <<<'CPP'

    cout << jawaban << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Total O(n log n) dengan <code>map</code>. Teknik "prefix + hitung yang sudah lewat" juga menjawab: subarray berjumlah kelipatan M (pakai <code>pre mod M</code>), subarray dengan jumlah 0, atau subarray dengan banyak huruf A = banyak huruf B (ubah A → +1, B → −1).</p>
TXT],
    ];

    $psPy = <<<'PY'
import sys
from collections import defaultdict
data = sys.stdin.read().split()
n, K = int(data[0]), int(data[1])
pernah = defaultdict(int)
pernah[0] = 1
pre = jawaban = 0
for a in map(int, data[2:2 + n]):
    pre += a
    jawaban += pernah[pre - K]
    pernah[pre] += 1
print(jawaban)
PY;

    $ps1d = <<<'CPP'
vector<long long> pre(n + 1, 0);          // pre[i] = a[0] + ... + a[i-1]
for (int i = 0; i < n; i++) pre[i + 1] = pre[i] + a[i];
// jumlah a[l..r] (0-indexed, inklusif)
long long jumlah = pre[r + 1] - pre[l];

// Variasi: prefix XOR, prefix banyak bilangan genap, prefix per huruf
vector<int> px(n + 1, 0);
for (int i = 0; i < n; i++) px[i + 1] = px[i] ^ a[i];
int xorRentang = px[r + 1] ^ px[l];       // XOR juga punya "kebalikan": dirinya sendiri
CPP;

    $ps2d = <<<'CPP'
// P[i][j] = jumlah semua a[x][y] dengan x < i dan y < j
vector<vector<long long>> P(R + 1, vector<long long>(C + 1, 0));
for (int i = 1; i <= R; i++)
    for (int j = 1; j <= C; j++)
        P[i][j] = a[i - 1][j - 1] + P[i - 1][j] + P[i][j - 1] - P[i - 1][j - 1];

// jumlah persegi (r1, c1) .. (r2, c2), 0-indexed inklusif
long long s = P[r2 + 1][c2 + 1] - P[r1][c2 + 1] - P[r2 + 1][c1] + P[r1][c1];
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Membangun prefix sum dalam O(n) dan menjawab jumlah rentang apa pun dalam O(1).</li>
        <li>Memakai prefix sum 2D dengan rumus inklusi-eksklusi untuk jumlah submatriks.</li>
        <li>Menghitung subarray dengan syarat jumlah tertentu memakai "prefix + hitung yang sudah lewat".</li>
        <li>Tahu batasnya: hanya untuk data statis dan operasi yang punya kebalikan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'saldo, bukan setoran', 'desc' => 'Ide prefix sum dan cara membangunnya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Buku Tabungan">
    <h2>Intuisi: Buku Tabungan</h2>
    <div class="prose">
        <p>Buku tabungan mencatat <strong>saldo</strong> setiap hari, bukan hanya setoran harian. Untuk tahu total setoran dari tanggal 1 sampai 20 Maret, kamu tidak perlu menjumlahkan 20 setoran: cukup <em>saldo tanggal 20 dikurangi saldo akhir Februari</em>.</p>
        <p>Itulah prefix sum: siapkan tabel jumlah kumulatif <strong>sekali</strong> dalam O(n), lalu setiap pertanyaan jumlah rentang dijawab dengan <strong>satu pengurangan</strong>.</p>
    </div>
    <div class="recurrence"><small>Rumus</small>pre[0] = 0
pre[i + 1] = pre[i] + a[i]
jumlah a[l..r] = pre[r + 1] − pre[l]</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Perhatikan indeksnya: <code>pre[i]</code> adalah jumlah <strong>i elemen pertama</strong> (a[0] sampai a[i−1]), bukan sampai indeks i. Konvensi "geser satu" ini membuat <code>pre[0] = 0</code> menangani rentang yang dimulai dari awal tanpa kasus khusus.</p>
    </div>
    @include('lessons.code', ['cpp' => $ps1d, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: 1D lalu 2D</h2>
    <div class="prose">
        <p>Mode <strong>1 dimensi</strong> membangun <code>pre</code> sel demi sel lalu menjawab beberapa pertanyaan. Mode <strong>2 dimensi</strong> memperlihatkan mengapa satu sel harus <em>dikurangi</em>: bagian kiri-atas terhitung dua kali. Nyalakan Mode Tebak untuk menebak isi sel sebelum ditampilkan.</p>
    </div>
    <div data-viz="prefix-2d"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>0</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th></tr>
            <tr><td>a[i]</td><td>1</td><td>3</td><td>4</td><td>8</td><td>6</td><td>–</td></tr>
            <tr class="hl"><td>pre[i]</td><td>0</td><td>1</td><td>4</td><td>8</td><td>16</td><td>22</td></tr>
        </table>
    </div>
    <div class="prose">
        <ul>
            <li>jumlah a[1..3] = pre[4] − pre[1] = 16 − 1 = <strong>15</strong> (3 + 4 + 8).</li>
            <li>jumlah a[0..4] = pre[5] − pre[0] = <strong>22</strong>.</li>
            <li>jumlah a[2..2] = pre[3] − pre[2] = 8 − 4 = <strong>4</strong>.</li>
        </ul>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'prefix sum di C++', 'desc' => 'Dua dimensi, dan menghitung subarray dengan syarat jumlah.'])

<section class="lesson-section" id="dua-dimensi" data-toc="Prefix Sum 2D">
    <h2>Prefix Sum 2D: Inklusi-Eksklusi</h2>
    <div class="prose">
        <p>Untuk grid, <code>P[i][j]</code> = jumlah persegi dari sudut kiri atas sampai (i−1, j−1). Saat membangun, kita menambah persegi di atas dan persegi di kiri; bagian kiri-atas terhitung <strong>dua kali</strong>, jadi dikurangi sekali. Saat menjawab, kebalikannya: kurangi dua persegi, lalu tambahkan kembali sudut yang terkurangi dua kali.</p>
    </div>
    @include('lessons.code', ['cpp' => $ps2d, 'js' => null, 'py' => null])
    <div class="steps">
        <div class="step-card"><b>Ambil persegi besar</b><span><code>P[r2+1][c2+1]</code>: dari sudut (0, 0) sampai sudut kanan bawah yang ditanya.</span></div>
        <div class="step-card"><b>Buang bagian atas</b><span><code>− P[r1][c2+1]</code>: baris-baris di atas r1.</span></div>
        <div class="step-card"><b>Buang bagian kiri</b><span><code>− P[r2+1][c1]</code>: kolom-kolom di kiri c1.</span></div>
        <div class="step-card"><b>Kembalikan sudut</b><span><code>+ P[r1][c1]</code>: sudut kiri atas terbuang dua kali.</span></div>
    </div>
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Subarray Berjumlah K</h2>
    @include('lessons.walkthrough', [
        'title' => 'Prefix sum + map: menghitung pasangan',
        'steps' => $psSteps,
        'sample' => ['input' => "6 3\n1 2 1 -1 2 1\n", 'output' => "5\n"],
        'py' => $psPy,
    ])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Tanpa prefix</th><th>Dengan prefix</th></tr>
        <tr><td>Membangun</td><td>–</td><td><code>O(n)</code> / <code>O(R · C)</code></td></tr>
        <tr><td>Satu query jumlah rentang</td><td><code>O(n)</code></td><td><code>O(1)</code></td></tr>
        <tr><td>Q query</td><td><code>O(n · Q)</code></td><td><code>O(n + Q)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> Jumlah 2 · 10<sup>5</sup> bilangan sebesar 10<sup>9</sup> mencapai 2 · 10<sup>14</sup>. Array <code>pre</code> harus <code>long long</code>, walaupun input muat di <code>int</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Data berubah.</strong> Prefix sum hanya benar selama array tidak diubah setelah tabelnya dibuat. Jika ada update di sela query, pakai Fenwick tree atau segment tree.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Maksimum dan minimum tidak bisa.</strong> Jumlah punya kebalikan (pengurangan), XOR punya kebalikan (XOR lagi), tetapi max tidak. Untuk min/max rentang, pakai sparse table.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'prefix sebagai cara berpikir', 'desc' => 'Mengubah syarat subarray menjadi syarat pasangan prefix.'])

<section class="lesson-section" id="pola" data-toc="Pola Prefix + Hitung">
    <h2>Pola: Ubah Subarray Menjadi Pasangan Prefix</h2>
    <div class="proof">
        <p>Setiap subarray a[l..r] berkorespondensi satu-satu dengan pasangan indeks prefix (l, r + 1) dengan l &lt; r + 1. Maka "berapa subarray yang jumlahnya memenuhi syarat X" sama dengan "berapa pasangan prefix (i &lt; j) dengan pre[j] − pre[i] memenuhi X".</p>
        <p>Jika X berbentuk <em>"sama dengan K"</em>, untuk setiap j cukup menghitung berapa i sebelumnya dengan <code>pre[i] = pre[j] − K</code>: map atau array frekuensi. Jika X berbentuk <em>"habis dibagi M"</em>, syaratnya menjadi <code>pre[i] ≡ pre[j] (mod M)</code>: hitung berdasarkan sisa bagi.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Jumlah rentang statis</h4><p><code>pre[r+1] − pre[l]</code>.</p></div>
        <div class="pattern"><h4>Submatriks</h4><p>Prefix 2D dengan inklusi-eksklusi.</p></div>
        <div class="pattern"><h4>Subarray berjumlah K</h4><p>map frekuensi prefix.</p></div>
        <div class="pattern"><h4>Kelipatan M</h4><p>Hitung prefix per sisa bagi M.</p></div>
        <div class="pattern"><h4>Seimbang A dan B</h4><p>A = +1, B = −1, cari subarray berjumlah 0.</p></div>
        <div class="pattern"><h4>Hasil kali kecuali diri sendiri</h4><p>Prefix kali dan suffix kali.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="pre = [0, 2, 7, 8, 12]. jumlah a[1..2] = pre[3] − pre[1] = 8 − 2 = 6 (5 + 1).">
        <p class="quiz-q">a = [2, 5, 1, 4]. Berapa <code>pre[3] − pre[1]</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">5</button>
            <button class="quiz-option">6</button>
            <button class="quiz-option">10</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Saat menjumlahkan persegi atas dan persegi kiri, persegi kiri-atas ikut terhitung dua kali, sehingga harus dikurangi sekali.">
        <p class="quiz-q">Mengapa ada <code>− P[i−1][j−1]</code> saat membangun prefix 2D?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar tidak overflow</button>
            <button class="quiz-option">Karena indeks dimulai dari 1</button>
            <button class="quiz-option">Bagian kiri-atas terhitung dua kali</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Maksimum tidak punya operasi kebalikan: dari max(a[0..r]) dan max(a[0..l−1]) kita tidak bisa mendapat max(a[l..r]).">
        <p class="quiz-q">Query mana yang <strong>tidak</strong> bisa dijawab dengan prefix?</p>
        <div class="quiz-options">
            <button class="quiz-option">Maksimum a[l..r]</button>
            <button class="quiz-option">XOR a[l..r]</button>
            <button class="quiz-option">Banyak bilangan genap di a[l..r]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
