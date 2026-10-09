@php
    $tsSteps = [
        ['Fungsi yang ingin diminimalkan', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> x, w;

long long f(long long p) {                 // waktu terlama jika berkumpul di p
    long long terburuk = 0;
    for (int i = 0; i < n; i++)
        terburuk = max(terburuk, w[i] * llabs(x[i] - p));
    return terburuk;
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Titik Kumpul":</strong> n teman tinggal di titik x<sub>i</sub> pada sebuah garis. Teman ke-i butuh w<sub>i</sub> menit untuk berjalan satu satuan. Pilih titik kumpul <em>bilangan bulat</em> p sehingga teman yang tiba paling akhir tiba secepat mungkin. Cetak waktunya.</p>
<p>f(p) = maks w<sub>i</sub>·|x<sub>i</sub> − p|. Setiap w<sub>i</sub>·|x<sub>i</sub> − p| berbentuk huruf V (cembung), dan <strong>maksimum dari fungsi-fungsi cembung tetap cembung</strong>. Jadi f turun lalu naik: unimodal.</p>
TXT],
        ['Baca input', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    cin >> n;
    x.resize(n);
    w.resize(n);
    for (int i = 0; i < n; i++) cin >> x[i] >> w[i];
CPP, <<<'TXT'
<p>Menghitung f(p) sekali butuh O(n). Kita tidak boleh mencoba semua p (rentangnya bisa 2·10<sup>9</sup>), tetapi boleh memanggil f sekitar seratus kali.</p>
TXT],
        ['Ternary search bilangan bulat', <<<'CPP'

    long long lo = *min_element(x.begin(), x.end());
    long long hi = *max_element(x.begin(), x.end());   // titik terbaik pasti di sini
    while (hi - lo > 2) {
        long long m1 = lo + (hi - lo) / 3;
        long long m2 = hi - (hi - lo) / 3;
        if (f(m1) < f(m2)) hi = m2 - 1;    // semua p >= m2 lebih buruk dari m1
        else lo = m1 + 1;                  // semua p <= m1 tidak lebih baik dari m2
    }
CPP, <<<'TXT'
<p>Dua titik ukur m1 &lt; m2 membagi rentang menjadi tiga. Jika f(m1) &lt; f(m2), lembahnya tidak mungkin di kanan m2 (di sana fungsi sudah naik), jadi buang [m2, hi]. Jika tidak, buang [lo, m1].</p>
<p>Setiap putaran membuang sekitar sepertiga rentang, jadi butuh ±log<sub>1.5</sub>(2·10<sup>9</sup>) ≈ 53 putaran, masing-masing dua panggilan f.</p>
<div class="wt-warn">Berhenti ketika tinggal 3 kandidat. Jika diteruskan sampai hi − lo = 1, m1 dan m2 bisa sama atau keluar rentang.</div>
TXT],
        ['Periksa sisa kandidat', <<<'CPP'

    long long ans = f(lo);
    for (long long p = lo + 1; p <= hi; p++) ans = min(ans, f(p));
    cout << ans << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Paling banyak tiga titik tersisa, cek semuanya. Total O(n log R) dengan R = lebar rentang.</p>
TXT],
    ];

    $tsPy = <<<'PY'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n = int(data[0])
    x = [int(v) for v in data[1:1 + 2 * n:2]]
    w = [int(v) for v in data[2:2 + 2 * n:2]]

    def f(p):
        return max(wi * abs(xi - p) for xi, wi in zip(x, w))

    lo, hi = min(x), max(x)
    while hi - lo > 2:
        m1 = lo + (hi - lo) // 3
        m2 = hi - (hi - lo) // 3
        if f(m1) < f(m2):
            hi = m2 - 1
        else:
            lo = m1 + 1
    print(min(f(p) for p in range(lo, hi + 1)))

main()
PY;

    $tsJs = <<<'JS'
const n = readInts()[0];
const x = [], w = [];
for (let i = 0; i < n; i++) { const [a, b] = readInts(); x.push(a); w.push(b); }
const f = (p) => { let t = 0; for (let i = 0; i < n; i++) t = Math.max(t, w[i] * Math.abs(x[i] - p)); return t; };
let lo = Infinity, hi = -Infinity;
for (const v of x) { lo = Math.min(lo, v); hi = Math.max(hi, v); }
while (hi - lo > 2) {
    const m1 = lo + Math.floor((hi - lo) / 3), m2 = hi - Math.floor((hi - lo) / 3);
    if (f(m1) < f(m2)) hi = m2 - 1; else lo = m1 + 1;
}
let ans = f(lo);
for (let p = lo + 1; p <= hi; p++) ans = Math.min(ans, f(p));
print(ans);
JS;

    $tsReal = <<<'CPP'
// Versi bilangan real: jumlah iterasi tetap, bukan while (hi - lo > eps)
double lo = L, hi = R;
for (int it = 0; it < 100; it++) {          // sisa rentang (2/3)^100 ≈ 2·10^-18 kali semula
    double m1 = lo + (hi - lo) / 3;
    double m2 = hi - (hi - lo) / 3;
    if (f(m1) < f(m2)) hi = m2;            // mencari MAKSIMUM? balik tanda < menjadi >
    else lo = m1;
}
double xTerbaik = (lo + hi) / 2;
CPP;

    $tsSlope = <<<'CPP'
// Alternatif untuk bilangan bulat: binary search pada "turunan" g(p) = f(p+1) - f(p).
// Untuk fungsi cembung, g tidak pernah turun, jadi predikat g(p) >= 0 monoton: F F F T T T.
long long lo = L, hi = R;                  // cari p terkecil dengan f(p+1) >= f(p)
while (lo < hi) {
    long long mid = lo + (hi - lo) / 2;
    if (f(mid + 1) >= f(mid)) hi = mid;
    else lo = mid + 1;
}
// f(lo) adalah nilai minimum; hanya ±log2(R) putaran
CPP;

    $tsGolden = <<<'CPP'
// Golden section search: satu evaluasi f per putaran (titik lama dipakai ulang)
const double r = (sqrt(5.0) - 1) / 2;       // 0.618...
double a = L, b = R;
double c = b - r * (b - a), d = a + r * (b - a);
double fc = f(c), fd = f(d);
for (int it = 0; it < 100; it++) {
    if (fc < fd) { b = d; d = c; fd = fc; c = b - r * (b - a); fc = f(c); }
    else         { a = c; c = d; fc = fd; d = a + r * (b - a); fd = f(d); }
}
// berguna jika f mahal (misalnya f sendiri berisi ternary search atau O(n log n))
CPP;

    $tsNested = <<<'CPP'
// Fungsi dua variabel yang cembung: ternary search bersarang
double g(double xx) {                       // nilai terbaik jika x dikunci = xx
    double lo = Ly, hi = Ry;
    for (int it = 0; it < 60; it++) {
        double m1 = lo + (hi - lo) / 3, m2 = hi - (hi - lo) / 3;
        if (F(xx, m1) < F(xx, m2)) hi = m2; else lo = m1;
    }
    return F(xx, (lo + hi) / 2);
}
// lalu ternary search lagi pada g(x). Biaya 60 · 60 · biaya(F).
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali fungsi <strong>unimodal</strong> dan <strong>cembung</strong> dari bentuk soalnya.</li>
        <li>Menulis <strong>ternary search</strong> untuk bilangan real (iterasi tetap) dan bilangan bulat (sisakan tiga kandidat).</li>
        <li>Mengganti ternary search dengan <strong>binary search pada selisih</strong> f(p+1) − f(p).</li>
        <li>Menghindari jebakan <strong>dataran datar</strong> dan memakai golden section atau ternary bersarang bila perlu.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'mencari dasar lembah', 'desc' => 'Dua titik ukur sudah cukup untuk tahu ke mana harus berjalan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Lembah Berkabut">
    <h2>Intuisi: Mencari Dasar Lembah dalam Kabut</h2>
    <div class="prose">
        <p>Bayangkan kamu berdiri di sebuah lembah yang tertutup kabut tebal. Kamu tahu bentuk tanahnya hanya <em>turun lalu naik</em>, tetapi tidak bisa melihat di mana dasarnya. Yang bisa kamu lakukan: mengukur ketinggian di titik mana pun.</p>
        <p>Ukur dua titik, m1 di sepertiga kiri dan m2 di sepertiga kanan. Jika m1 lebih rendah, dasar lembah tidak mungkin di kanan m2, karena di sana tanah sudah menanjak. Buang bagian itu dan ulangi. Itulah <strong>ternary search</strong>: setiap langkah membuang sepertiga rentang.</p>
        <p>Binary search butuh pertanyaan "ya/tidak" yang monoton. Ternary search dipakai ketika yang kita punya adalah <em>nilai</em> f(x) yang turun lalu naik, dan kita ingin titik terendahnya (atau tertinggi, untuk fungsi naik lalu turun).</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Unimodal</b><span>Turun lalu naik (atau sebaliknya), dengan satu lembah/puncak. Syarat ternary search.</span></div>
        <div class="term"><b>Cembung (konveks)</b><span>Kemiringannya tidak pernah berkurang, seperti x², |x − a|. Selalu unimodal.</span></div>
        <div class="term"><b>Dataran datar</b><span>Bagian di mana f konstan. Aman jika hanya di dasar lembah, berbahaya jika di lereng.</span></div>
        <div class="term"><b>m1, m2</b><span>Dua titik ukur di sepertiga dan dua pertiga rentang.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Membuang Sepertiga</h2>
    <div class="prose"><p>Pilih fungsinya, lalu ikuti langkahnya. Di Mode Tebak, tentukan sendiri bagian mana yang aman dibuang dari nilai f(m1) dan f(m2).</p></div>
    <div data-viz="ternary"></div>
</section>

<section class="lesson-section" id="aturan" data-toc="Aturan Membuang">
    <h2>Aturan Membuang (untuk Mencari Minimum)</h2>
    <table class="cx-table">
        <tr><th>Hasil perbandingan</th><th>Kesimpulan</th><th>Bilangan real</th><th>Bilangan bulat</th></tr>
        <tr><td>f(m1) &lt; f(m2)</td><td>Di kanan m2 fungsi sudah naik</td><td><code>hi = m2</code></td><td><code>hi = m2 - 1</code></td></tr>
        <tr><td>f(m1) &gt; f(m2)</td><td>Di kiri m1 fungsi masih turun</td><td><code>lo = m1</code></td><td><code>lo = m1 + 1</code></td></tr>
        <tr><td>f(m1) = f(m2)</td><td>Lembahnya di antara m1 dan m2</td><td>salah satu boleh</td><td>salah satu boleh (untuk f cembung)</td></tr>
    </table>
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Untuk mencari <strong>maksimum</strong> fungsi yang naik lalu turun, cukup balik tanda perbandingannya, atau cari minimum dari −f.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'ternary search di C++', 'desc' => 'Versi bilangan bulat dibedah baris demi baris, lalu versi real dan binary search pada selisih.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Titik Kumpul</h2>
    @include('lessons.walkthrough', [
        'title' => 'Ternary search bilangan bulat',
        'steps' => $tsSteps,
        'sample' => ['input' => "3\n1 2\n5 1\n10 1\n", 'output' => "6\n"],
        'py' => $tsPy,
        'js' => $tsJs,
    ])
    <div class="prose"><p>Jejak untuk contoh di atas, dengan f(p) = maks(2·|1 − p|, |5 − p|, |10 − p|):</p></div>
    <table class="trace-table">
        <tr><th>[lo, hi]</th><th>m1, m2</th><th>f(m1), f(m2)</th><th>aksi</th></tr>
        <tr><td>[1, 10]</td><td>4, 7</td><td>6, 12</td><td>f(m1) &lt; f(m2) → hi = 6</td></tr>
        <tr><td>[1, 6]</td><td>2, 5</td><td>8, 8</td><td>sama → lo = 3</td></tr>
        <tr><td>[3, 6]</td><td>4, 5</td><td>6, 8</td><td>f(m1) &lt; f(m2) → hi = 4</td></tr>
        <tr class="ok"><td>[3, 4]</td><td colspan="2">cek f(3) = 7, f(4) = 6</td><td>jawaban <b>6</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="real" data-toc="Versi Real & Selisih">
    <h2>Versi Bilangan Real dan Binary Search pada Selisih</h2>
    <div class="prose">
        <p>Untuk bilangan real, jangan menulis <code>while (hi - lo &gt; 1e-9)</code>: jika nilainya besar (misalnya 10<sup>9</sup>), presisi double tidak cukup untuk selisih 10<sup>-9</sup> dan loopnya bisa tidak pernah berhenti. Pakai jumlah iterasi tetap.</p>
    </div>
    @include('lessons.code', ['cpp' => $tsReal, 'js' => null, 'py' => null])
    <div class="prose">
        <p>Untuk bilangan bulat ada cara yang lebih rapi. Pada fungsi cembung, selisih <code>f(p+1) − f(p)</code> tidak pernah berkurang: negatif di lereng kiri, lalu nol atau positif sejak dasar lembah. Itu predikat monoton, jadi binary search biasa bisa menemukan titik pertama yang selisihnya ≥ 0.</p>
    </div>
    @include('lessons.code', ['cpp' => $tsSlope, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Varian</th><th>Banyak evaluasi f</th><th>Catatan</th></tr>
        <tr><td>Ternary real, 100 iterasi</td><td>200</td><td>Aman untuk rentang sampai 10<sup>18</sup></td></tr>
        <tr><td>Ternary bulat</td><td>≈ 2 · log<sub>1.5</sub> R</td><td>Sisakan 3 kandidat, cek manual</td></tr>
        <tr><td>Binary search pada selisih</td><td>≈ 2 · log<sub>2</sub> R</td><td>Hanya untuk bilangan bulat, f cembung</td></tr>
        <tr><td>Golden section</td><td>≈ log<sub>1.618</sub> R</td><td>Satu evaluasi per putaran</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Dataran datar di lereng.</strong> Jika f(m1) = f(m2) karena keduanya jatuh di bagian datar (misalnya f konstan di [0, 5] lalu turun ke lembah di 8), kita tidak tahu sisi mana yang aman. Ternary search hanya benar jika dataran datar hanya ada di dasar lembah, seperti pada fungsi cembung.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Bukan unimodal.</strong> Fungsi dengan beberapa lembah (misalnya sin x, atau "keuntungan" yang naik-turun) bisa membuat ternary search membuang lembah terendah. Buktikan dulu bentuknya, atau uji dengan brute force untuk n kecil.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> w·|x − p| bisa mencapai 10<sup>12</sup> atau lebih: pakai <code>long long</code> di dalam f.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'kapan sebuah fungsi unimodal?', 'desc' => 'Aturan cembung, golden section, dan ternary bersarang.'])

<section class="lesson-section" id="cembung" data-toc="Mengenali Fungsi Cembung">
    <h2>Mengenali Fungsi Cembung Tanpa Menggambar</h2>
    <div class="prose"><p>Di soal, f(x) jarang diberikan sebagai rumus. Biasanya kita merakitnya dari potongan yang sederhana. Aturan-aturan ini memberi bukti cepat:</p></div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Potongan dasar</h4><p>|x − a|, (x − a)², fungsi linear ax + b: semuanya cembung.</p></div>
        <div class="pattern"><h4>Jumlah</h4><p>Jumlah fungsi cembung tetap cembung, misalnya Σ |x − a<sub>i</sub>| (total jarak).</p></div>
        <div class="pattern"><h4>Maksimum</h4><p>maks dari fungsi cembung tetap cembung, misalnya waktu tiba terakhir.</p></div>
        <div class="pattern"><h4>Kali konstanta positif</h4><p>c · f dengan c ≥ 0 tetap cembung.</p></div>
        <div class="pattern"><h4>Lebar sebaran</h4><p>maks(a<sub>i</sub> + v<sub>i</sub>t) − min(a<sub>i</sub> + v<sub>i</sub>t) cembung dalam t.</p></div>
        <div class="pattern"><h4>Hati-hati</h4><p>min dari fungsi cembung, atau selisih dua fungsi cembung, umumnya <em>tidak</em> cembung.</p></div>
    </div>
    <div class="proof">
        <p>Mengapa maksimum fungsi cembung tetap cembung? Fungsi cembung berarti setiap tali busur di antara dua titik grafiknya berada di atas grafik. Untuk h(x) = maks(f(x), g(x)) dan titik di antara a dan b: f di sana ≤ tali busur f ≤ tali busur h, begitu juga g. Jadi h di sana pun ≤ tali busur h.</p>
    </div>
</section>

<section class="lesson-section" id="lanjut" data-toc="Golden Section & Bersarang">
    <h2>Golden Section dan Ternary Bersarang</h2>
    <div class="prose"><p>Jika menghitung f mahal, golden section search menghemat setengah evaluasi: posisi titik ukur dipilih dengan rasio emas sehingga salah satu titik lama bisa dipakai lagi di putaran berikutnya.</p></div>
    @include('lessons.code', ['cpp' => $tsGolden, 'js' => null, 'py' => null])
    <div class="prose"><p>Untuk fungsi dua variabel yang cembung (misalnya mencari titik di bidang yang meminimalkan jumlah jarak), kunci x lalu cari y terbaik dengan ternary search; fungsi "nilai terbaik untuk x tertentu" ini juga cembung, jadi bisa di-ternary-search lagi.</p></div>
    @include('lessons.code', ['cpp' => $tsNested, 'js' => null, 'py' => null])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Untuk fungsi cembung yang tersusun dari |x − a<sub>i</sub>| sering ada jawaban langsung (misalnya median untuk Σ |x − a<sub>i</sub>|). Ternary search menang ketika bentuk f rumit tetapi kecembungannya mudah dibuktikan.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="sin x di [0, 10] punya lebih dari satu lembah, jadi tidak unimodal. Dua lainnya cembung: jumlah dua |x − a| dan parabola terbuka ke atas.">
        <p class="quiz-q">Fungsi mana yang TIDAK aman dicari minimumnya dengan ternary search?</p>
        <div class="quiz-options">
            <button class="quiz-option">f(x) = |x − 3| + |x − 8| di [0, 10]</button>
            <button class="quiz-option">f(x) = x² − 4x di [−100, 100]</button>
            <button class="quiz-option">f(x) = sin x di [0, 10]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Jika f(m1) < f(m2), fungsi sudah naik di m2, sehingga semua p ≥ m2 bernilai ≥ f(m2) > f(m1). Bagian [m2, hi] aman dibuang: hi = m2 − 1.">
        <p class="quiz-q">Ternary search bilangan bulat untuk minimum. Ternyata f(m1) &lt; f(m2). Bagian mana yang dibuang?</p>
        <div class="quiz-options">
            <button class="quiz-option">[lo, m1]</button>
            <button class="quiz-option">[m2, hi]</button>
            <button class="quiz-option">[m1, m2]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Jika m1 dan m2 sama-sama di bagian datar, f(m1) = f(m2) tidak memberi petunjuk; aturan 'buang kiri' bisa saja membuang lembahnya. Fungsi cembung tidak punya dataran datar di lereng, jadi aman.">
        <p class="quiz-q">f konstan di [0, 5], lalu turun ke lembah di x = 8, lalu naik. Mengapa ternary search bisa gagal?</p>
        <div class="quiz-options">
            <button class="quiz-option">f(m1) = f(m2) di bagian datar tidak menunjukkan arah lembah</button>
            <button class="quiz-option">Karena rentangnya terlalu kecil</button>
            <button class="quiz-option">Karena butuh lebih dari 100 iterasi</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
