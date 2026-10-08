@php
    $ddSteps = [
        ['Variabel memo', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int S;
string D;                 // digit-digit batas atas X
long long memo[20][200];
bool ada[20][200];
CPP, <<<'TXT'
<p>Bilangan sampai 10<sup>18</sup> punya paling banyak 19 digit, dan jumlah digitnya paling banyak 9 × 19 = 171. Jadi memo berukuran 20 × 200 sudah cukup.</p>
<p><code>D</code> menyimpan digit-digit batas atas sebagai string, misalnya X = 325 menjadi <code>"325"</code>.</p>
TXT],
        ['Fungsi rekursif: posisi, jumlah, ketat', <<<'CPP'

long long go(int pos, int sum, bool tight) {
    if (sum > S) return 0;
    if (pos == (int)D.size()) return sum == S;
    if (!tight && ada[pos][sum]) return memo[pos][sum];
CPP, <<<'TXT'
<p>Kita menyusun bilangan digit demi digit dari kiri. Keadaannya:</p>
<ul>
    <li><code>pos</code>: digit ke berapa yang sedang dipilih.</li>
    <li><code>sum</code>: jumlah digit yang sudah dipilih.</li>
    <li><code>tight</code>: apakah semua digit sebelumnya <strong>sama persis</strong> dengan D. Jika ya, digit sekarang tidak boleh melebihi <code>D[pos]</code>; jika tidak, digit bebas 0–9.</li>
</ul>
<p>Base case: semua posisi terisi → berhasil jika jumlahnya tepat S. Pemangkasan: jumlah sudah melewati S tidak mungkin turun lagi.</p>
<div class="wt-tip">Hanya keadaan <strong>tidak ketat</strong> yang di-memo. Keadaan ketat hanya muncul sekali per posisi (satu jalur sepanjang digit D), jadi tidak perlu disimpan, dan memonya bisa dipakai ulang untuk batas X yang berbeda.</div>
TXT],
        ['Coba setiap digit', <<<'CPP'
    int batas = tight ? D[pos] - '0' : 9;
    long long res = 0;
    for (int d = 0; d <= batas; d++)
        res += go(pos + 1, sum + d, tight && d == batas);
CPP, <<<'TXT'
<p>Digit yang boleh dipilih: 0 sampai <code>batas</code>. Setelah memilih d, kita tetap ketat hanya jika sebelumnya ketat <strong>dan</strong> d sama dengan digit D di posisi ini.</p>
<pre>X = 325, pos = 0, tight: d ∈ {0, 1, 2, 3}
  d = 0, 1, 2 → tidak ketat lagi (sudah pasti &lt; 325)
  d = 3       → tetap ketat, posisi berikutnya maksimal 2</pre>
<p>Bilangan dengan digit lebih sedikit (misalnya 7) dianggap memakai nol di depan (007). Nol tidak menambah jumlah digit, jadi hasilnya tetap benar.</p>
TXT],
        ['Simpan ke memo', <<<'CPP'
    if (!tight) {
        ada[pos][sum] = true;
        memo[pos][sum] = res;
    }
    return res;
}
CPP, <<<'TXT'
<p>Untuk keadaan tidak ketat, hasilnya tidak bergantung pada D (digit sisanya bebas), sehingga aman disimpan.</p>
TXT],
        ['Hitung 0..X', <<<'CPP'

long long hitung(long long X) {
    if (X < 0) return 0;
    D = to_string(X);
    memset(ada, 0, sizeof ada);
    return go(0, 0, true);
}
CPP, <<<'TXT'
<p><code>hitung(X)</code> = banyak bilangan di [0, X] dengan jumlah digit S. Memo direset karena panjang D bisa berbeda (posisi yang sama berarti "sisa digit" yang berbeda).</p>
TXT],
        ['Jawaban rentang [A, B]', <<<'CPP'

int main() {
    long long A, B;
    cin >> A >> B >> S;
    cout << hitung(B) - hitung(A - 1) << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Trik rentang yang selalu dipakai pada digit DP: banyak di [A, B] = banyak di [0, B] − banyak di [0, A − 1].</p>
<pre>A = 1, B = 325, S = 7
hitung(325) = 24, hitung(0) = 0 → 24</pre>
<p>Kerjanya hanya sekitar 19 × 171 × 10 langkah per pemanggilan, berapa pun besar B.</p>
TXT],
    ];

    $ddPy = <<<'PY'
from functools import lru_cache

A, B, S = map(int, input().split())

def hitung(X):
    if X < 0:
        return 0
    D = str(X)

    @lru_cache(maxsize=None)
    def go(pos, s, tight):
        if s > S:
            return 0
        if pos == len(D):
            return 1 if s == S else 0
        batas = int(D[pos]) if tight else 9
        return sum(go(pos + 1, s + d, tight and d == batas) for d in range(batas + 1))

    return go(0, 0, True)

print(hitung(B) - hitung(A - 1))
PY;

    $ddLead = <<<'CPP'
// Bilangan tanpa dua digit bersebelahan yang sama (misalnya 1213 boleh, 1223 tidak).
// Butuh dua keadaan tambahan: digit sebelumnya, dan apakah bilangan SUDAH DIMULAI
// (nol di depan tidak dihitung sebagai digit, jadi 0 0 7 tidak melanggar aturan).
long long go(int pos, int prev, bool tight, bool mulai) {
    if (pos == (int)D.size()) return 1;
    // memo[pos][prev][mulai] untuk keadaan tidak ketat ...
    int batas = tight ? D[pos] - '0' : 9;
    long long res = 0;
    for (int d = 0; d <= batas; d++) {
        if (mulai && d == prev) continue;          // dilarang sama dengan sebelumnya
        bool mulaiBaru = mulai || d != 0;           // digit bukan nol pertama memulai bilangan
        res += go(pos + 1, mulaiBaru ? d : -1, tight && d == batas, mulaiBaru);
    }
    return res;
}
CPP;

    $ddMod = <<<'CPP'
// Banyak bilangan di [0, X] yang HABIS DIBAGI K dan jumlah digitnya juga habis dibagi K.
// State: (pos, sisa bilangan mod K, sisa jumlah digit mod K, tight)
long long go(int pos, int modBil, int modSum, bool tight) {
    if (pos == (int)D.size()) return modBil == 0 && modSum == 0;
    // ...
    for (int d = 0; d <= batas; d++)
        res += go(pos + 1, (modBil * 10 + d) % K, (modSum + d) % K, tight && d == batas);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali soal digit DP: "berapa banyak bilangan di [A, B] yang memenuhi sifat …" dengan B sangat besar.</li>
        <li>Merancang state <strong>(posisi, informasi, ketat)</strong> dan memahami arti flag <code>tight</code>.</li>
        <li>Menulis digit DP rekursif dengan memo, dan menjawab rentang dengan F(B) − F(A − 1).</li>
        <li>Menangani nol di depan dan sifat yang melibatkan sisa bagi.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'membangun bilangan digit demi digit', 'desc' => 'Mengapa mencoba semua bilangan mustahil, dan ide flag "ketat".'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Menghitung Tanpa Mendaftar</h2>
    <div class="prose">
        <p>Berapa banyak bilangan dari 1 sampai 10<sup>18</sup> yang jumlah digitnya 50? Memeriksa satu per satu butuh 10<sup>18</sup> langkah: mustahil. Tetapi jika kita menyusun bilangan <strong>digit demi digit</strong>, yang perlu diingat hanyalah sedikit hal: posisi sekarang dan jumlah digit sejauh ini. Banyaknya keadaan itu hanya sekitar 19 × 171.</p>
        <p>Satu-satunya kesulitan adalah batas atas. Jika batasnya 325, kita tidak boleh menyusun 4xx atau 33x. Di sinilah flag <strong>ketat</strong> (<em>tight</em>) berperan.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Ketat (tight = true)</b><span>Semua digit yang sudah dipilih sama persis dengan awalan batas. Digit berikutnya paling besar sama dengan digit batas.</span></div>
        <div class="term"><b>Bebas (tight = false)</b><span>Sudah pernah memilih digit yang lebih kecil dari batas, jadi bilangan pasti lebih kecil. Sisa digit bebas 0–9.</span></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Begitu sebuah digit lebih kecil dari digit batas, semua digit sesudahnya bebas. Jumlah cara untuk "sisa digit bebas" tidak bergantung pada batas, sehingga bisa di-memo dan dipakai berulang kali.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose">
        <p>Hitung bilangan 0..325 dengan jumlah digit 7 (ditulis 3 digit, misalnya 016). Telusuri digit batas 3, 2, 5:</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Awalan ketat</th><th>Digit dipilih lebih kecil</th><th>Sisa digit bebas, jumlah yang dibutuhkan</th><th>Banyak cara</th></tr>
            <tr><td>(kosong)</td><td>0, 1, 2 di ratusan</td><td>2 digit bebas berjumlah 7, 6, 5</td><td>8 + 7 + 6 = 21</td></tr>
            <tr class="hl"><td>3</td><td>0, 1 di puluhan</td><td>1 digit bebas berjumlah 4, 3</td><td>1 + 1 = 2</td></tr>
            <tr class="hl"><td>32</td><td>0..4 di satuan</td><td>jumlah awalan 5 + d = 7 → d = 2</td><td>1</td></tr>
            <tr class="ok"><td>325</td><td>–</td><td>325 sendiri: 3 + 2 + 5 = 10 ≠ 7</td><td>0</td></tr>
        </table>
    </div>
    <div class="prose">
        <p>Totalnya 21 + 2 + 1 = <strong>24</strong> bilangan. Perhatikan baris pertama: dua digit bebas berjumlah 7 ada <strong>8</strong> cara (07, 16, 25, 34, 43, 52, 61, 70), berjumlah 6 ada 7 cara, dan berjumlah 5 ada 6 cara. Setelah satu digit lebih kecil dari batas dipilih, kita tidak lagi peduli pada batas, hanya pada "berapa digit tersisa dan berapa jumlah yang masih dibutuhkan".</p>
        <p>Bilangan 0 tidak ikut terhitung karena jumlah digitnya 0, jadi hasil untuk [0, 325] dan [1, 325] sama-sama 24.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi Digit DP</h2>
    <div class="prose">
        <p>Tahap 1 mengisi tabel <code>f[i][s]</code>: banyak cara mengisi digit ke-i sampai akhir secara <strong>bebas</strong> jika jumlah sejauh ini s. Tahap 2 berjalan di sepanjang digit N, menjumlahkan sel-sel hijau setiap kali memilih digit yang lebih kecil dari digit N.</p>
    </div>
    <div data-viz="digit"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Ubah ke [0, X]</b><span>Jawaban [A, B] = F(B) − F(A − 1).</span></div>
        <div class="step-card"><b>Tentukan informasi</b><span>Apa yang perlu diingat tentang digit yang sudah dipilih? Jumlah, sisa bagi, digit terakhir, sudah mulai atau belum.</span></div>
        <div class="step-card"><b>Rekursi go(pos, info, tight)</b><span>Coba digit 0..batas, perbarui info dan tight.</span></div>
        <div class="step-card"><b>Memo keadaan bebas</b><span>Simpan hanya jika tight = false.</span></div>
        <div class="step-card"><b>Hitung ukuran state</b><span>Panjang digit × banyak nilai info × 10 digit.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'digit DP di C++', 'desc' => 'Rekursi dengan memo dan trik rentang F(B) − F(A − 1).'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Jumlah Digit Tepat S</h2>
    <div class="prose"><p><strong>Soal contoh:</strong> diberikan A, B (0 ≤ A ≤ B ≤ 10<sup>18</sup>) dan S. Berapa banyak bilangan di [A, B] yang jumlah digitnya tepat S?</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Digit DP dengan flag ketat',
        'steps' => $ddSteps,
        'sample' => ['input' => "1 325 7\n", 'output' => "24\n"],
        'py' => $ddPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Bagian</th><th>Banyaknya</th></tr>
        <tr><td>Posisi</td><td>≤ 19</td></tr>
        <tr><td>Informasi (jumlah digit)</td><td>≤ 172</td></tr>
        <tr><td>Pilihan digit per keadaan</td><td>10</td></tr>
        <tr><td>Total per pemanggilan F(X)</td><td>≈ 19 · 172 · 10 ≈ 33 000</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>A = 0.</strong> F(A − 1) = F(−1) harus bernilai 0. Tangani bilangan negatif secara khusus.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Memo keadaan ketat.</strong> Jika keadaan ketat ikut disimpan tanpa membedakan tight, hasil untuk "bebas" bisa tertimpa hasil "ketat". Simpan tight sebagai dimensi memo, atau jangan memo keadaan ketat sama sekali.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'state tambahan', 'desc' => 'Nol di depan, digit sebelumnya, dan sisa bagi.'])

<section class="lesson-section" id="lead" data-toc="Nol di Depan">
    <h2>Nol di Depan dan Digit Sebelumnya</h2>
    <div class="prose">
        <p>Untuk sifat "tidak ada dua digit bersebelahan yang sama", nol di depan menjadi masalah: 007 punya dua nol bersebelahan, padahal bilangan 7 tidak melanggar. Tambahkan flag <code>mulai</code>: apakah sudah ada digit bukan nol yang dipilih.</p>
    </div>
    @include('lessons.code', ['cpp' => $ddLead, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="mod" data-toc="Sisa Bagi">
    <h2>Sifat dengan Sisa Bagi</h2>
    <div class="prose">
        <p>Untuk "habis dibagi K", kita tidak perlu mengingat seluruh bilangan, cukup sisa baginya. Menambahkan digit d di belakang bilangan bersisa r menghasilkan sisa <code>(r · 10 + d) mod K</code>.</p>
    </div>
    @include('lessons.code', ['cpp' => $ddMod, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Jumlah digit</h4><p>Info = jumlah sejauh ini (≤ 9 · panjang).</p></div>
        <div class="pattern"><h4>Habis dibagi K</h4><p>Info = sisa bagi (K kemungkinan).</p></div>
        <div class="pattern"><h4>Digit terlarang</h4><p>Lewati digit tertentu dalam loop d.</p></div>
        <div class="pattern"><h4>Tidak ada digit kembar bersebelahan</h4><p>Info = digit sebelumnya + flag mulai.</p></div>
        <div class="pattern"><h4>Banyak kemunculan digit</h4><p>"Berapa kali angka 1 ditulis dari 1 sampai N?" Kembalikan pasangan (banyak bilangan, total kemunculan).</p></div>
        <div class="pattern"><h4>Bilangan biner</h4><p>Sama saja, hanya digitnya 0–1 dan panjangnya ±60.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Jika masih ketat dengan batas 3 5 7, digit kedua maksimal 5. Jika memilih 5, tetap ketat; jika lebih kecil, menjadi bebas.">
        <p class="quiz-q">Batas X = 357. Digit pertama yang dipilih 3 (masih ketat). Digit kedua boleh berapa saja?</p>
        <div class="quiz-options">
            <button class="quiz-option">0 sampai 9</button>
            <button class="quiz-option">0 sampai 5</button>
            <button class="quiz-option">Hanya 5</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="F(B) menghitung [0, B], F(A − 1) menghitung [0, A − 1]. Selisihnya tepat [A, B].">
        <p class="quiz-q">Bagaimana menghitung banyak bilangan di [A, B] dengan fungsi F(X) = banyak di [0, X]?</p>
        <div class="quiz-options">
            <button class="quiz-option">F(B) − F(A)</button>
            <button class="quiz-option">F(B) + F(A)</button>
            <button class="quiz-option">F(B) − F(A − 1)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Keadaan bebas tidak bergantung pada digit batas berikutnya, jadi hasilnya bisa dipakai ulang. Keadaan ketat hanya terjadi di satu jalur.">
        <p class="quiz-q">Mengapa yang di-memo hanya keadaan tidak ketat?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena hasilnya tidak bergantung pada digit batas yang tersisa</button>
            <button class="quiz-option">Karena keadaan ketat selalu bernilai 0</button>
            <button class="quiz-option">Agar memori lebih kecil saja</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
