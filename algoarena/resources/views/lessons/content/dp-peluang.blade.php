@php
    $plSteps = [
        ['Pangkat cepat dan invers modular', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

long long pangkat(long long a, long long b) {
    long long hasil = 1;
    a %= MOD;
    while (b > 0) {
        if (b & 1) hasil = hasil * a % MOD;
        a = a * a % MOD;
        b >>= 1;
    }
    return hasil;
}

long long invers(long long a) { return pangkat(a, MOD - 2); }

CPP, <<<'TXT'
<p>Nilai harapan biasanya pecahan. Soal kompetisi sering meminta pecahan P/Q dicetak sebagai <code>P · Q<sup>−1</sup> mod 10<sup>9</sup> + 7</code>. Karena MOD prima, teorema kecil Fermat memberi <code>Q<sup>−1</sup> = Q<sup>MOD − 2</sup></code>, dihitung dengan pangkat cepat dalam O(log MOD).</p>
<p>Dengan cara ini semua hitungan tetap bilangan bulat dan <strong>tepat</strong>, tanpa masalah pembulatan <code>double</code>.</p>
TXT],
        ['State: harapan dari setiap petak', <<<'CPP'
int main() {
    int n;
    cin >> n;
    // E[i] = harapan banyak lemparan dari petak i sampai tepat di petak n
    vector<long long> E(n + 1, 0);   // E[n] = 0: sudah sampai

CPP, <<<'TXT'
<p>Bidak mulai di petak 0. Setiap giliran lempar dadu (1–6, peluang sama) dan maju sebanyak angka itu. Jika langkahnya akan <strong>melewati</strong> petak n, bidak diam di tempat. Berapa harapan banyak lemparan sampai bidak tepat di petak n?</p>
<p>Seperti DP biasa, state-nya posisi. Bedanya, nilai state adalah <strong>harapan sisa biaya</strong> dari posisi itu, dan base case-nya di tujuan: <code>E[n] = 0</code>. Karena itu kita mengisi tabel dari belakang.</p>
TXT],
        ['Transisi dengan self-loop', <<<'CPP'
    for (int i = n - 1; i >= 0; i--) {
        long long jumlah = 6;
        int sah = 0;
        for (int d = 1; d <= 6; d++)
            if (i + d <= n) {
                jumlah = (jumlah + E[i + d]) % MOD;
                sah++;
            }
CPP, <<<'TXT'
<p>Dari petak i, satu lemparan (biaya 1), lalu dengan peluang 1/6 untuk setiap d:</p>
<ul>
    <li>jika i + d ≤ n, pindah ke petak i + d;</li>
    <li>jika tidak, tetap di i, dan harapannya kembali <code>E[i]</code> sendiri (<strong>self-loop</strong>).</li>
</ul>
<p>Jadi <code>E[i] = 1 + (1/6)·(Σ E[i+d] + (6 − sah)·E[i])</code>, dengan <code>sah</code> = banyak d yang tidak melewati n. Kalikan 6 dan pindahkan suku E[i] ke kiri: <code>sah · E[i] = 6 + Σ E[i+d]</code>. Variabel <code>jumlah</code> menghitung ruas kanan.</p>
TXT],
        ['Membagi dengan invers, lalu mencetak', <<<'CPP'
        E[i] = jumlah * invers(sah) % MOD;
    }
    cout << E[0] << '\n';
    return 0;
}
CPP, <<<'TXT'
<p><code>E[i] = (6 + Σ E[i+d]) / sah</code>. Pembagian modulo = perkalian dengan invers. Untuk n = 8 hasilnya 43/6 ≈ 7,17, dicetak sebagai 43 · 6<sup>−1</sup> mod 10<sup>9</sup> + 7 = 166666675.</p>
<div class="wt-tip">Self-loop tidak membuat DP berputar selamanya: cukup selesaikan persamaan linear satu variabel itu secara aljabar sebelum menulis kode.</div>
TXT],
    ];

    $plPy = <<<'PY'
MOD = 10**9 + 7
n = int(input())
E = [0] * (n + 1)
for i in range(n - 1, -1, -1):
    jumlah, sah = 6, 0
    for d in range(1, 7):
        if i + d <= n:
            jumlah += E[i + d]
            sah += 1
    E[i] = jumlah % MOD * pow(sah, MOD - 2, MOD) % MOD
print(E[0])
PY;

    $plFrac = <<<'CPP'
// Pecahan P/Q dicetak sebagai P · Q^(MOD-2) mod MOD.
long long pecahan(long long P, long long Q) {
    return P % MOD * pangkat(Q, MOD - 2) % MOD;
}
// pecahan(1, 2)  = 500000004   (karena 2 · 500000004 = 1000000008 ≡ 1)
// pecahan(43, 6) = 166666675
// Penjumlahan, pengurangan, dan perkalian pecahan dikerjakan langsung pada nilai modulonya.
CPP;

    $plCoupon = <<<'CPP'
// N jenis stiker, setiap bungkus berisi satu stiker acak (setiap jenis berpeluang sama).
// Saat sudah punya i jenis, peluang bungkus berikutnya berisi jenis BARU = (N - i) / N,
// maka harapan banyak bungkus sampai dapat jenis baru = N / (N - i)   (distribusi geometrik).
// Linearitas: total = Σ_{i=0}^{N-1} N / (N - i) = N · (1 + 1/2 + ... + 1/N).
double total = 0;
for (int i = 0; i < n; i++) total += (double)n / (n - i);
// n = 6 (seperti kartu bergambar 6 jenis): total = 14.7 bungkus
CPP;

    $plMax = <<<'CPP'
// Harapan nilai TERBESAR dari n dadu bersisi m.
// Untuk X bilangan bulat tak negatif: E[X] = Σ_{x≥1} P(X ≥ x)   ("jumlah ekor").
// P(maks ≥ x) = 1 − P(semua dadu ≤ x−1) = 1 − ((x−1)/m)^n.
double e = 0;
for (int x = 1; x <= m; x++) e += 1 - pow((double)(x - 1) / m, n);
// n = 2, m = 6: e = 161/36 ≈ 4.47
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menghitung distribusi peluang dengan DP (misalnya jumlah beberapa dadu).</li>
        <li>Merancang DP <strong>nilai harapan</strong> yang diisi dari belakang, termasuk state yang bisa kembali ke dirinya sendiri.</li>
        <li>Mencetak pecahan dalam bentuk <code>P · Q<sup>−1</sup> mod p</code> dengan invers modular.</li>
        <li>Memakai <strong>linearitas nilai harapan</strong>, distribusi geometrik, dan rumus jumlah ekor untuk soal yang tampak sulit.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'peluang sebagai DP', 'desc' => 'Aturan dasar peluang, distribusi jumlah dadu, dan nilai harapan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Menghitung Kemungkinan</h2>
    <div class="prose">
        <p>Lempar dua dadu. Jumlah 7 lebih sering muncul daripada jumlah 2, karena ada 6 pasangan yang berjumlah 7 (1+6, 2+5, …, 6+1) tetapi hanya satu yang berjumlah 2 (1+1). Dari 36 hasil yang sama mungkin, peluang jumlah 7 adalah 6/36.</p>
        <p>Untuk 10 dadu, kita tidak mungkin mendaftar 6<sup>10</sup> hasil. Tetapi "jumlah k dadu = s" bergantung hanya pada "jumlah k − 1 dadu" dan angka dadu terakhir. Itulah struktur DP.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Kejadian saling lepas</b><span>Tidak bisa terjadi bersamaan, peluangnya <strong>dijumlah</strong>. "Dadu terakhir 1 atau 2 atau …".</span></div>
        <div class="term"><b>Kejadian bebas</b><span>Tidak saling memengaruhi, peluangnya <strong>dikali</strong>. Dua lemparan dadu berbeda.</span></div>
        <div class="term"><b>Nilai harapan</b><span><code>E[X] = Σ x · P(X = x)</code>, rata-rata jangka panjang. Harapan satu dadu = 3,5.</span></div>
        <div class="term"><b>Linearitas</b><span><code>E[X + Y] = E[X] + E[Y]</code> <em>selalu</em> berlaku, bahkan jika X dan Y saling bergantung.</span></div>
    </div>
</section>

<section class="lesson-section" id="distribusi" data-toc="DP Peluang">
    <h2>DP Peluang: Distribusi Jumlah Dadu</h2>
    <div class="prose">
        <p>Definisikan <code>cara[k][s]</code> = banyak hasil k dadu yang jumlahnya s. Dadu ke-k bernilai d (1..6) dan k − 1 dadu sebelumnya harus berjumlah s − d:</p>
        <p style="text-align:center"><code>cara[k][s] = Σ<sub>d=1..6</sub> cara[k−1][s−d]</code>, &nbsp; <code>cara[0][0] = 1</code></p>
        <p>Peluangnya <code>cara[n][s] / 6<sup>n</sup></code>. Kita juga bisa langsung menyimpan peluang: <code>P[k][s] = Σ (1/6) · P[k−1][s−d]</code>. Hasilnya sama; versi "banyak cara" bebas dari pecahan sampai langkah terakhir.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>s</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th></tr>
            <tr><td>cara[2][s]</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td><b>6</b></td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Penjumlahan enam suku di setiap state adalah jumlah rentang: dengan prefix sum (materi Optimasi Transisi DP) DP ini menjadi O(n · S) walaupun dadunya bersisi banyak.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Setiap batang baru adalah jumlah enam batang biru di baris atasnya. Coba 3 dan 4 dadu: distribusinya makin menyerupai lonceng (teorema limit pusat).</p></div>
    <div data-viz="dice"></div>
</section>

<section class="lesson-section" id="harapan" data-toc="DP Nilai Harapan">
    <h2>DP Nilai Harapan: Isi dari Belakang</h2>
    <div class="prose">
        <p>Untuk soal "berapa harapan banyak langkah sampai selesai", definisikan <code>E[x]</code> = harapan sisa biaya jika sekarang berada di state x. Maka:</p>
        <p style="text-align:center"><code>E[tujuan] = 0</code>, &nbsp; <code>E[x] = biaya langkah + Σ<sub>y</sub> P(x → y) · E[y]</code></p>
        <p>Ini akibat linearitas: total biaya = biaya langkah pertama + sisa biaya dari state berikutnya. Karena E[x] bergantung pada state <em>sesudah</em> x, tabel diisi dari tujuan mundur ke awal.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>DP peluang bergerak <strong>maju</strong> (dari awal: "dengan peluang berapa saya sampai di sini?"). DP nilai harapan biasanya bergerak <strong>mundur</strong> (dari tujuan: "berapa lagi biaya dari sini?").</p>
    </div>
    <div class="steps">
        <div class="step-card"><b>State</b><span>Apa yang perlu diingat agar masa depan bisa dihitung? Posisi, jumlah yang sudah dikumpulkan, dsb.</span></div>
        <div class="step-card"><b>Base case</b><span>E[tujuan] = 0.</span></div>
        <div class="step-card"><b>Transisi</b><span>Biaya satu langkah + Σ peluang × E[state berikutnya].</span></div>
        <div class="step-card"><b>Self-loop?</b><span>Jika state bisa kembali ke dirinya, pindahkan suku itu ke ruas kiri lalu bagi.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Pecahan modulo dan DP harapan dengan self-loop.'])

<section class="lesson-section" id="modulo" data-toc="Pecahan Modulo">
    <h2>Mencetak Pecahan sebagai Bilangan Modulo</h2>
    <div class="prose">
        <p>Mencetak <code>double</code> berisiko: dua program yang benar bisa berbeda di digit terakhir. Karena itu soal biasanya meminta jawaban P/Q dalam bentuk <code>P · Q<sup>−1</sup> mod 10<sup>9</sup> + 7</code>, yaitu bilangan R dengan <code>R · Q ≡ P</code>. Semua operasi (+, −, ×, ÷) dikerjakan langsung dalam modulo, dan pembagian diganti perkalian dengan invers.</p>
    </div>
    @include('lessons.code', ['cpp' => $plFrac, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Papan Tanpa Melewati Garis Finis</h2>
    @include('lessons.walkthrough', [
        'title' => 'Harapan banyak lemparan',
        'steps' => $plSteps,
        'sample' => ['input' => "8\n", 'output' => "166666675\n"],
        'py' => $plPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: n = 8</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>Langkah sah</th><th>sah</th><th>E[i] = (6 + Σ E[i+d]) / sah</th></tr>
            <tr><td>8</td><td>–</td><td>–</td><td>0 (tujuan)</td></tr>
            <tr><td>7</td><td>d = 1 → 8</td><td>1</td><td>(6 + 0) / 1 = 6</td></tr>
            <tr><td>6</td><td>d = 1, 2 → 7, 8</td><td>2</td><td>(6 + 6 + 0) / 2 = 6</td></tr>
            <tr><td>2…5</td><td>d = 1..8 − i</td><td>8 − i</td><td>(6 + 6·(7 − i) + 0) / (8 − i) = 6</td></tr>
            <tr class="hl"><td>1</td><td>d = 1..6 → 2..7</td><td>6</td><td>(6 + 6·6) / 6 = 7</td></tr>
            <tr class="ok"><td>0</td><td>d = 1..6 → 1..6</td><td>6</td><td>(6 + 7 + 5·6) / 6 = <b>43/6</b></td></tr>
        </table>
    </div>
    <div class="prose"><p>Menarik: dari petak mana pun yang berjarak ≤ 6 dari finis, harapannya selalu 6. Alasannya: dari petak seperti itu, di setiap giliran selalu ada <em>tepat satu</em> angka dadu yang membawa bidak tepat ke finis, dan bidak tidak pernah keluar dari daerah ini. Jadi setiap giliran selesai dengan peluang 1/6, dan harapannya 1 / (1/6) = 6 (distribusi geometrik, lihat Level 3).</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <div class="prose"><p>DP peluang/harapan berukuran (banyak state) × (banyak kemungkinan per langkah), ditambah O(log MOD) jika setiap state butuh invers. Invers yang sama (misalnya 1/6) cukup dihitung sekali.</p></div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Salah arah pengisian.</strong> E[i] butuh E[i + d] yang lebih besar indeksnya, jadi loop harus dari n − 1 turun ke 0. Mengisi maju memakai nilai yang belum dihitung.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Membagi langsung.</strong> <code>jumlah / sah</code> dengan operator <code>/</code> biasa pada bilangan modulo salah total. Selalu kalikan dengan invers.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Siklus antar state.</strong> Self-loop satu state bisa diselesaikan aljabar. Jika state saling kembali dalam siklus panjang (misalnya ular tangga dengan ular), dibutuhkan sistem persamaan linear (eliminasi Gauss) atau trik khusus.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'teknik nilai harapan', 'desc' => 'Linearitas, distribusi geometrik, dan jumlah ekor.'])

<section class="lesson-section" id="linear" data-toc="Linearitas">
    <h2>Linearitas Nilai Harapan</h2>
    <div class="prose">
        <p>Banyak soal harapan tidak butuh DP sama sekali. Pecah besaran yang ditanya menjadi jumlah variabel indikator I<sub>k</sub> (bernilai 1 jika kejadian k terjadi, 0 jika tidak). Karena <code>E[I<sub>k</sub>] = P(kejadian k)</code>, maka</p>
        <p style="text-align:center"><code>E[banyak kejadian] = Σ P(kejadian k)</code></p>
        <p>Contoh: kocok kartu 1..N secara acak. Berapa harapan banyak kartu yang tetap di posisinya? Kartu k tetap di posisinya dengan peluang 1/N, ada N kartu, jadi harapannya N · 1/N = <strong>1</strong>, berapa pun N. Padahal kejadian-kejadiannya saling bergantung.</p>
    </div>
</section>

<section class="lesson-section" id="geometrik" data-toc="Distribusi Geometrik">
    <h2>Menunggu Keberhasilan: Distribusi Geometrik</h2>
    <div class="prose">
        <p>Jika setiap percobaan berhasil dengan peluang p (bebas satu sama lain), harapan banyak percobaan sampai berhasil pertama kali adalah <strong>1/p</strong>. Buktinya satu baris dengan self-loop: <code>E = 1 + (1 − p) · E</code>, sehingga <code>E = 1/p</code>. Menunggu angka 6 pada dadu: 6 lemparan.</p>
        <p>Gabungkan dengan linearitas untuk soal kolektor kupon:</p>
    </div>
    @include('lessons.code', ['cpp' => $plCoupon, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="ekor" data-toc="Jumlah Ekor">
    <h2>Rumus Jumlah Ekor untuk Maksimum</h2>
    <div class="prose">
        <p>Harapan nilai maksimum sulit dihitung langsung, tetapi "maksimum ≥ x" mudah: itu kebalikan dari "semua &lt; x", dan untuk kejadian bebas peluangnya dikali. Untuk bilangan bulat tak negatif berlaku <code>E[X] = Σ<sub>x≥1</sub> P(X ≥ x)</code>.</p>
    </div>
    @include('lessons.code', ['cpp' => $plMax, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Harapan banyak langkah</h4><p>DP mundur dari tujuan; self-loop diselesaikan aljabar.</p></div>
        <div class="pattern"><h4>Menghitung kejadian</h4><p>Indikator + linearitas: Σ peluang setiap kejadian.</p></div>
        <div class="pattern"><h4>Sampai berhasil</h4><p>Geometrik: 1/p percobaan. Kolektor kupon: N · H<sub>N</sub>.</p></div>
        <div class="pattern"><h4>Maksimum / minimum</h4><p>Jumlah ekor: Σ P(X ≥ x), dengan P dihitung lewat komplemen.</p></div>
        <div class="pattern"><h4>Jalan acak di DAG</h4><p>E[v] = 1 + rata-rata E[tetangga]; urutan topologis terbalik.</p></div>
        <div class="pattern"><h4>Ada pilihan pemain</h4><p>E[x] = max atau min atas pilihan, masing-masing berupa rata-rata peluang.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap dadu berharapan 3,5, dan linearitas memberi 3 · 3,5 = 10,5 tanpa perlu menghitung distribusi.">
        <p class="quiz-q">Berapa harapan jumlah 3 dadu?</p>
        <div class="quiz-options">
            <button class="quiz-option">9</button>
            <button class="quiz-option">10,5</button>
            <button class="quiz-option">Harus dihitung dengan DP distribusi</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="E = 1 + (1 − p)E memberi E = 1/p. Dengan p = 1/4, harapannya 4 percobaan.">
        <p class="quiz-q">Sebuah mesin berhasil dengan peluang 1/4 setiap dicoba. Harapan banyak percobaan sampai berhasil?</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Nilai harapan dari state x bergantung pada state sesudahnya, jadi base case-nya di tujuan dan tabel diisi mundur.">
        <p class="quiz-q">Pada DP "harapan banyak langkah sampai finis", base case-nya adalah …</p>
        <div class="quiz-options">
            <button class="quiz-option">E[finis] = 0, diisi mundur dari finis</button>
            <button class="quiz-option">E[awal] = 0, diisi maju dari awal</button>
            <button class="quiz-option">E[awal] = 1</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
