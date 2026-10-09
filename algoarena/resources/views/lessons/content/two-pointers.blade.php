@php
    $tpSteps = [
        ['Membaca data', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long S;
    cin >> n >> S;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;
CPP, <<<'TXT'
<p><strong>Soal contoh "Jendela Terpendek":</strong> diberikan n bilangan <strong>tak negatif</strong> dan S. Cari panjang subarray (berurutan) terpendek yang jumlahnya paling sedikit S, atau 0 jika tidak ada.</p>
<p>Mencoba semua pasangan (l, r) butuh O(n²). Kata kuncinya "tak negatif": memperpanjang jendela tidak pernah mengurangi jumlahnya.</p>
TXT],
        ['Dua penunjuk yang hanya maju', <<<'CPP'

    int l = 0, best = INT_MAX;
    long long sum = 0;
    for (int r = 0; r < n; r++) {
        sum += a[r];                       // perlebar jendela ke kanan
CPP, <<<'TXT'
<p>Jendela adalah <code>a[l..r]</code>. Penunjuk kanan <code>r</code> maju satu per satu di loop luar, dan <code>sum</code> selalu berisi jumlah jendela.</p>
TXT],
        ['Persempit selama masih valid', <<<'CPP'
        while (sum >= S) {                 // jendela valid
            best = min(best, r - l + 1);   // catat panjangnya
            sum -= a[l];                   // persempit dari kiri
            l++;
        }
    }
CPP, <<<'TXT'
<p>Selama jendela masih memenuhi syarat, catat panjangnya lalu coba buang elemen paling kiri. Kita berhenti ketika jumlahnya turun di bawah S.</p>
<p>Mengapa <code>l</code> tidak pernah perlu mundur? Untuk r yang lebih besar, jendela [l', r] dengan l' &lt; l pasti lebih panjang dari jendela valid yang sudah kita catat, jadi tidak mungkin menjadi jawaban yang lebih baik.</p>
<div class="wt-tip">Loop <code>while</code> di dalam <code>for</code> terlihat seperti O(n²). Tetapi <code>l</code> hanya maju dan paling banyak n kali sepanjang program: totalnya O(n).</div>
TXT],
        ['Jawaban', <<<'CPP'

    cout << (best == INT_MAX ? 0 : best) << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Jika tidak ada jendela valid, <code>best</code> tetap <code>INT_MAX</code> dan kita cetak 0.</p>
TXT],
    ];

    $tpPy = <<<'PY'
import sys
data = sys.stdin.read().split()
n, S = int(data[0]), int(data[1])
a = list(map(int, data[2:2 + n]))
l = 0
s = 0
best = float('inf')
for r in range(n):
    s += a[r]
    while s >= S:
        best = min(best, r - l + 1)
        s -= a[l]
        l += 1
print(0 if best == float('inf') else best)
PY;

    $tpPair = <<<'CPP'
// Pola 1: berlawanan arah (array HARUS terurut)
// Apakah ada pasangan berjumlah tepat X?
sort(a.begin(), a.end());
int l = 0, r = n - 1;
while (l < r) {
    long long s = a[l] + a[r];
    if (s == X) break;            // ketemu
    if (s < X) l++;               // butuh lebih besar: geser kiri
    else r--;                     // butuh lebih kecil: geser kanan
}
CPP;

    $tpFixed = <<<'CPP'
// Pola 2: jendela UKURAN TETAP k — jumlah maksimum
long long sum = 0, best = LLONG_MIN;
for (int r = 0; r < n; r++) {
    sum += a[r];                    // masuk
    if (r >= k) sum -= a[r - k];    // keluar
    if (r >= k - 1) best = max(best, sum);
}
CPP;

    $tpCount = <<<'CPP'
// Pola 3: MENGHITUNG subarray valid (jumlah <= S, a[i] >= 0)
// Untuk setiap r, semua jendela [l..r], [l+1..r], ..., [r..r] valid
long long hitung = 0, sum = 0;
for (int r = 0, l = 0; r < n; r++) {
    sum += a[r];
    while (sum > S) sum -= a[l++];
    hitung += r - l + 1;            // banyak subarray valid yang berakhir di r
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengganti dua loop bersarang dengan dua penunjuk yang hanya maju, sehingga O(n²) menjadi O(n).</li>
        <li>Membedakan tiga pola: berlawanan arah, jendela ukuran tetap, dan jendela ukuran variabel.</li>
        <li>Mengenali syarat <strong>monoton</strong> yang membuat two pointers benar, dan kapan ia gagal (bilangan negatif).</li>
        <li>Menghitung banyak subarray valid dengan <code>r − l + 1</code>.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'berjalan tanpa mundur', 'desc' => 'Ide dua penunjuk dan syarat monoton.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Jendela Kereta">
    <h2>Intuisi: Mengisi Tas</h2>
    <div class="prose">
        <p>Kamu mengisi tas dengan barang yang berjajar di konveyor. Barang terus dimasukkan dari depan. Begitu tas kelebihan beban, keluarkan barang dari <strong>bawah</strong> (yang paling lama) sampai muat lagi. Kamu tidak pernah perlu mengosongkan seluruh tas dan mulai dari awal.</p>
        <p>Itulah sliding window: penunjuk kanan memasukkan, penunjuk kiri mengeluarkan, dan keduanya <strong>hanya maju</strong>. Kuncinya ada di satu sifat: jika tas sudah kelebihan, menambah barang lagi jelas tidak akan membuatnya muat. Sifat ini disebut <strong>monoton</strong>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Berlawanan arah</b><span>l dari kiri, r dari kanan, bertemu di tengah. Butuh array terurut. Untuk pasangan dengan jumlah tertentu.</span></div>
        <div class="term"><b>Jendela ukuran tetap</b><span>Selalu k elemen: satu masuk, satu keluar. Untuk rata-rata atau jumlah k elemen berurutan.</span></div>
        <div class="term"><b>Jendela ukuran variabel</b><span>Perlebar dari kanan, persempit dari kiri saat melanggar. Untuk subarray terpanjang/terpendek yang memenuhi syarat.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Tiga Pola">
    <h2>Visualisasi: Tiga Pola Two Pointers</h2>
    <div class="prose"><p>Ganti modenya untuk melihat ketiga pola. Perhatikan penunjuk <code>l</code> dan <code>r</code>: tidak ada yang pernah mundur. Itulah sebabnya total langkahnya linear.</p></div>
    <div data-viz="two-pointer-pair"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: Jendela Terpendek</h2>
    <div class="prose"><p>a = [1, 2, 3, 4, 5, 6, 7, 8], S = 11.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>r</th><th>jendela</th><th>sum</th><th>aksi</th><th>best</th></tr>
            <tr><td>0–3</td><td>[0..3]</td><td>10</td><td>belum cukup, r maju</td><td>∞</td></tr>
            <tr class="hl"><td>4</td><td>[0..4] → [1..4] → [2..4]</td><td>15 → 14 → 12</td><td>valid, persempit</td><td>5 → 4 → 3</td></tr>
            <tr class="hl"><td>5</td><td>[3..5] → [4..5]</td><td>15 → 11</td><td>valid, persempit</td><td>3 → 2</td></tr>
            <tr><td>6</td><td>[5..6]</td><td>13</td><td>valid, panjang 2</td><td>2</td></tr>
            <tr class="ok"><td>7</td><td>[6..7]</td><td>15</td><td>valid, panjang 2</td><td><b>2</b></td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'two pointers di C++', 'desc' => 'Program lengkap dan tiga potongan pola.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Jendela Terpendek</h2>
    @include('lessons.walkthrough', [
        'title' => 'Sliding window ukuran variabel',
        'steps' => $tpSteps,
        'sample' => ['input' => "8 11\n1 2 3 4 5 6 7 8\n", 'output' => "2\n"],
        'py' => $tpPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Potongan Pola">
    <h2>Potongan Pola Lainnya</h2>
    @include('lessons.code', ['cpp' => $tpPair, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $tpFixed, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $tpCount, 'js' => null, 'py' => null])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Menghitung, bukan hanya mencari.</strong> Jika [l, r] valid dan syaratnya monoton, maka semua jendela yang lebih pendek dengan ujung kanan r juga valid. Ada tepat <code>r − l + 1</code> jendela seperti itu, jadi jawabannya cukup ditambah sekaligus.</p>
    </div>
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Pola</th><th>Waktu</th><th>Syarat</th></tr>
        <tr><td>Berlawanan arah</td><td><code>O(n)</code> + sort <code>O(n log n)</code></td><td>Array terurut</td></tr>
        <tr><td>Jendela tetap</td><td><code>O(n)</code></td><td>–</td></tr>
        <tr><td>Jendela variabel</td><td><code>O(n)</code></td><td>Syarat monoton</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Bilangan negatif merusak segalanya.</strong> Dengan nilai negatif, menambah elemen bisa <em>mengurangi</em> jumlah, sehingga jendela yang melanggar bisa kembali valid. Untuk "subarray berjumlah K" dengan nilai negatif, pakai prefix sum + map.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Subarray vs subsequence.</strong> Two pointers bekerja untuk bagian yang <em>berurutan</em>. Jika elemen boleh dipilih melompat-lompat, biasanya itu wilayah DP atau greedy.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'mengapa linear dan mengapa benar', 'desc' => 'Argumen amortisasi dan invarian monoton.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Benar?">
    <h2>Mengapa Benar dan Mengapa O(n)?</h2>
    <div class="proof">
        <p><strong>Waktu.</strong> Setiap iterasi loop dalam memajukan l satu langkah, dan l tidak pernah melebihi n. Jadi total iterasi loop dalam sepanjang program paling banyak n, ditambah n iterasi loop luar: O(n).</p>
        <p><strong>Kebenaran (jendela terpendek).</strong> Misalkan jawaban optimal adalah [L, R]. Ketika r mencapai R, penunjuk l belum melewati L: jika l &gt; L, berarti saat r' &lt; R jendela [L, r'] sudah valid, padahal itu lebih pendek dari [L, R], kontradiksi. Selama l ≤ L, jumlah [l, R] ≥ jumlah [L, R] ≥ S (nilai tak negatif), sehingga loop dalam terus memajukan l sampai minimal L, dan panjang R − L + 1 pasti tercatat.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Dua bilangan berjumlah X</h4><p>Sort + berlawanan arah.</p></div>
        <div class="pattern"><h4>Tiga bilangan berjumlah X</h4><p>Loop satu indeks + two pointers: O(n²).</p></div>
        <div class="pattern"><h4>Substring tanpa huruf kembar</h4><p>Jendela variabel + array frekuensi.</p></div>
        <div class="pattern"><h4>Paling banyak K nilai berbeda</h4><p>Jendela variabel + hitung jenis.</p></div>
        <div class="pattern"><h4>Rata-rata k berurutan</h4><p>Jendela tetap.</p></div>
        <div class="pattern"><h4>Menggabung dua array urut</h4><p>Dua penunjuk, satu per array.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="l dan r masing-masing hanya maju paling banyak n kali, jadi total langkahnya 2n: O(n).">
        <p class="quiz-q">Kompleksitas sliding window dengan <code>while</code> di dalam <code>for</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(n²)</button>
            <button class="quiz-option">O(n log n)</button>
            <button class="quiz-option">O(n)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Dengan bilangan negatif, memperlebar jendela bisa mengurangi jumlah, jadi syaratnya tidak monoton lagi.">
        <p class="quiz-q">Mengapa sliding window gagal untuk "subarray berjumlah K" jika ada bilangan negatif?</p>
        <div class="quiz-options">
            <button class="quiz-option">Syaratnya tidak lagi monoton</button>
            <button class="quiz-option">Array harus diurutkan dulu</button>
            <button class="quiz-option">Butuh lebih banyak memori</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Jendela [2..5] valid, maka [2..5], [3..5], [4..5], [5..5] juga valid: 5 − 2 + 1 = 4 subarray.">
        <p class="quiz-q">Saat r = 5, jendela valid terlebar adalah [2..5]. Berapa subarray valid yang berakhir di 5?</p>
        <div class="quiz-options">
            <button class="quiz-option">3</button>
            <button class="quiz-option">4</button>
            <button class="quiz-option">6</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
