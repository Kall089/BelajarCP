@php
    $ivSteps = [
        ['Cek invarian dulu', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    string s;
    cin >> s;
    int n = s.size();
    int banyakT = count(s.begin(), s.end(), 'T');
    if (banyakT % 2 == 1) {            // paritas banyak T tidak pernah berubah
        cout << -1 << "\n";
        return 0;
    }
CPP, <<<'TXT'
<p><strong>Soal contoh "Koin Terbalik":</strong> sederet koin, masing-masing H (kepala) atau T (ekor). Satu operasi membalik dua koin yang bersebelahan. Berapa operasi minimum agar semua menjadi H? Cetak −1 jika mustahil.</p>
<p>Membalik dua koin mengubah banyak T sebesar −2 (TT → HH), 0 (TH → HT), atau +2 (HH → TT). Jadi <strong>paritas</strong> banyak T adalah invarian. Target "semua H" punya 0 T (genap); jika awalnya ganjil, target tidak akan pernah tercapai.</p>
TXT],
        ['Dorong T paling kiri ke kanan', <<<'CPP'

    long long ops = 0;
    for (int i = 0; i + 1 < n; i++) {
        if (s[i] == 'T') {                         // koin i tidak akan disentuh lagi setelah ini
            s[i] = 'H';
            s[i + 1] = (s[i + 1] == 'T') ? 'H' : 'T';
            ops++;
        }
    }
    cout << ops << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Jika paritasnya genap, menyapu dari kiri selalu berhasil: setiap T di posisi i harus dibalik sekarang (bersama i + 1), karena sesudah ini tidak ada operasi lain yang menyentuh posisi i. Di akhir, banyak T genap dan hanya posisi terakhir yang mungkin T, jadi pasti 0.</p>
<p>Hasilnya juga minimum: T-T dipasangkan berurutan (pertama dengan kedua, ketiga dengan keempat, …) dan biayanya jarak antarpasangan. Total O(n).</p>
TXT],
    ];

    $ivPy = <<<'PY'
s = list(input().strip())
n = len(s)
if s.count("T") % 2 == 1:
    print(-1)
else:
    ops = 0
    for i in range(n - 1):
        if s[i] == "T":
            s[i] = "H"
            s[i + 1] = "H" if s[i + 1] == "T" else "T"
            ops += 1
    print(ops)
PY;

    $ivPuzzle = <<<'CPP'
// Puzzle geser n x n (angka 1..n²-1 dan satu kotak kosong 0): bisakah diselesaikan?
// inv = banyak pasangan (i < j) dengan a[i] > a[j], abaikan 0, baca baris demi baris
// n ganjil : bisa  ⇔  inv genap
// n genap  : bisa  ⇔  (inv + baris kosong dihitung dari bawah, mulai 1) ganjil
bool bisa(int n, const vector<int>& a, int barisKosongDariBawah) {
    long long inv = hitungInversi(a);            // O(n² log n) dengan BIT, atau paritas lewat siklus
    if (n % 2 == 1) return inv % 2 == 0;
    return (inv + barisKosongDariBawah) % 2 == 1;
}
CPP;

    $ivParity = <<<'CPP'
// Paritas permutasi tanpa menghitung inversi: n − (banyak siklus) ≡ banyak inversi (mod 2)
int paritas(const vector<int>& p) {              // p permutasi 0..n-1
    int n = p.size(), siklus = 0;
    vector<bool> lihat(n, false);
    for (int i = 0; i < n; i++)
        if (!lihat[i]) {
            siklus++;
            for (int j = i; !lihat[j]; j = p[j]) lihat[j] = true;
        }
    return (n - siklus) % 2;                     // 0 = genap, 1 = ganjil
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mencari <strong>invarian</strong>: besaran yang tidak berubah oleh operasi, untuk membuktikan sesuatu mustahil.</li>
        <li>Memakai argumen <strong>paritas</strong> dan <strong>pewarnaan papan</strong>.</li>
        <li>Memakai <strong>monovarian</strong> (besaran yang selalu turun) untuk membuktikan proses pasti berhenti.</li>
        <li>Memeriksa apakah puzzle geser bisa diselesaikan dengan paritas permutasi.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'sesuatu yang tidak pernah berubah', 'desc' => 'Mencari besaran yang kebal terhadap operasi.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Papan Catur Rusak">
    <h2>Intuisi: Papan Catur yang Kehilangan Dua Sudut</h2>
    <div class="prose">
        <p>Papan catur 8 × 8 kehilangan dua petak di sudut yang berseberangan. Bisakah sisa 62 petak ditutup tepat dengan 31 domino 1 × 2? Mencoba semua cara penempatan domino jelas mustahil. Tetapi lihat warnanya: setiap domino selalu menutup <strong>satu petak hitam dan satu petak putih</strong>. Dua sudut berseberangan berwarna sama, jadi tersisa 32 petak satu warna dan 30 petak warna lain. 31 domino butuh 31 + 31. Mustahil.</p>
        <p>Itulah <strong>invarian</strong>: besaran yang tetap (atau berubah dengan pola yang bisa ditebak) setiap kali operasi dilakukan. Jika keadaan awal dan keadaan tujuan punya nilai invarian yang berbeda, tujuan tidak mungkin dicapai, sepintar apa pun langkah kita.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Invarian</b><span>Besaran yang tidak berubah oleh operasi apa pun.</span></div>
        <div class="term"><b>Paritas</b><span>Genap/ganjil. Invarian paling sering: operasi mengubah sesuatu sebesar ±2.</span></div>
        <div class="term"><b>Pewarnaan</b><span>Warnai papan (catur, per baris, mod k) agar setiap potongan menutup warna dengan pola tetap.</span></div>
        <div class="term"><b>Monovarian</b><span>Besaran yang selalu turun (atau naik). Menjamin proses berhenti.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Paritas Koin</h2>
    <div class="prose"><p>Ubah deretan koin (H/T) lalu ikuti sapuannya. Perhatikan kolom paritas di panel kanan: ia tidak pernah berubah.</p></div>
    <div data-viz="invariant"></div>
</section>

<section class="lesson-section" id="cari" data-toc="Cara Mencari Invarian">
    <h2>Cara Mencari Invarian</h2>
    <div class="steps">
        <div class="step-card"><b>Tulis efek satu operasi</b><span>Apa yang berubah? Jumlah, banyak elemen, posisi, XOR, hasil kali?</span></div>
        <div class="step-card"><b>Coba besaran sederhana</b><span>Jumlah semua, jumlah mod 2/3/k, XOR semua, banyak inversi, warna petak.</span></div>
        <div class="step-card"><b>Bandingkan awal dan tujuan</b><span>Jika nilainya berbeda: mustahil. Jika sama: belum tentu bisa, buktikan dengan konstruksi.</span></div>
        <div class="step-card"><b>Lengkapi dengan konstruksi</b><span>Tunjukkan cara mencapai tujuan saat invarian cocok (sering greedy).</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'invarian di C++', 'desc' => 'Cek mustahil dulu, lalu bangun jawaban.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Koin Terbalik</h2>
    @include('lessons.walkthrough', [
        'title' => 'Paritas + sapuan dari kiri',
        'steps' => $ivSteps,
        'sample' => ['input' => "THHTTT\n", 'output' => "4\n"],
        'py' => $ivPy,
    ])
    <table class="trace-table">
        <tr><th>i</th><th>sebelum</th><th>sesudah</th><th>ops</th></tr>
        <tr><td>0</td><td>THHTTT</td><td>HTHTTT</td><td>1</td></tr>
        <tr><td>1</td><td>HTHTTT</td><td>HHTTTT</td><td>2</td></tr>
        <tr><td>2</td><td>HHTTTT</td><td>HHHHTT</td><td>3</td></tr>
        <tr class="ok"><td>4</td><td>HHHHTT</td><td>HHHHHH</td><td><b>4</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="jebakan" data-toc="Contoh Klasik & Jebakan">
    <h2>Contoh Klasik dan Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Invarian</th></tr>
        <tr><td>Ganti a, b dengan |a − b|</td><td>paritas jumlah semua bilangan</td></tr>
        <tr><td>Ganti a, b dengan a + b</td><td>jumlah semua bilangan</td></tr>
        <tr><td>Ganti a, b dengan a ⊕ b</td><td>XOR semua bilangan</td></tr>
        <tr><td>Tukar dua elemen sembarang</td><td>paritas permutasi berganti setiap tukar</td></tr>
        <tr><td>Tukar a<sub>i</sub> dengan a<sub>i+2</sub></td><td>himpunan nilai di posisi ganjil (dan di posisi genap)</td></tr>
        <tr><td>Tutup papan dengan domino</td><td>selisih petak hitam dan putih yang tertutup</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Invarian cocok ≠ pasti bisa.</strong> Invarian hanya membuktikan "mustahil". Jika nilainya sama, kamu tetap harus menunjukkan konstruksinya (atau menemukan invarian lain yang membedakan).</p>
    </div>
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Jika operasi bisa dibalik dan invariannya "lengkap", sering jawabannya: bisa ⇔ invariannya sama. Untuk memastikan, bandingkan dengan BFS keadaan pada kasus kecil.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'paritas permutasi dan monovarian', 'desc' => 'Puzzle geser, pewarnaan lanjut, dan bukti berhenti.'])

<section class="lesson-section" id="puzzle" data-toc="Puzzle Geser & Monovarian">
    <h2>Puzzle Geser dan Monovarian</h2>
    <div class="prose">
        <p>Puzzle 15 (kotak 4 × 4 berisi 1..15 dan satu kosong) terkenal karena setengah dari semua susunan <em>tidak bisa</em> diselesaikan. Invariannya gabungan dua hal: paritas permutasi angka (dibaca baris demi baris) dan baris tempat kotak kosong.</p>
        <p>Geser horizontal tidak mengubah urutan angka. Geser vertikal memindahkan satu angka melewati n − 1 angka lain: untuk n ganjil itu genap (paritas inversi tetap), untuk n genap itu ganjil, tetapi baris kotak kosong juga berubah satu, sehingga jumlah keduanya tetap paritasnya.</p>
    </div>
    @include('lessons.code', ['cpp' => $ivPuzzle, 'js' => null, 'py' => null])
    <div class="prose"><p>Menghitung inversi untuk n² sampai 10<sup>6</sup> angka butuh BIT. Jika yang dibutuhkan hanya paritas, ada jalan pintas lewat banyak siklus permutasi:</p></div>
    @include('lessons.code', ['cpp' => $ivParity, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Monovarian.</strong> Proses "selama ada dua elemen bersebelahan yang terbalik, tukar keduanya" pasti berhenti, karena setiap penukaran mengurangi banyak inversi tepat 1, dan banyak inversi tidak bisa negatif. Besaran yang selalu turun dan terbatas di bawah membuktikan proses berhenti, dan sering juga memberi batas atas banyak langkah.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Operasi ±2</h4><p>Paritas banyak objek tetap.</p></div>
        <div class="pattern"><h4>Ubin di papan</h4><p>Warnai papan (catur, diagonal, mod k).</p></div>
        <div class="pattern"><h4>Tukar berjarak k</h4><p>Elemen tidak berpindah kelas posisi mod k.</p></div>
        <div class="pattern"><h4>Proses pasti berhenti?</h4><p>Cari monovarian: inversi, jumlah, energi.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Dua sudut berseberangan berwarna sama, sehingga sisa petak 32 berwarna satu dan 30 berwarna lain. Setiap domino menutup satu hitam dan satu putih.">
        <p class="quiz-q">Mengapa papan 8 × 8 tanpa dua sudut berseberangan tidak bisa ditutup 31 domino?</p>
        <div class="quiz-options">
            <button class="quiz-option">62 tidak habis dibagi 4</button>
            <button class="quiz-option">Domino tidak bisa diputar</button>
            <button class="quiz-option">Banyak petak hitam dan putih yang tersisa tidak sama</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="|a − b| ≡ a + b (mod 2), jadi paritas jumlah tetap. 1 + 2 + … + 10 = 55 ganjil, sehingga bilangan terakhir pasti ganjil.">
        <p class="quiz-q">Di papan tertulis 1, 2, …, 10. Berulang kali hapus dua bilangan a, b dan tulis |a − b|. Bilangan terakhir pasti…</p>
        <div class="quiz-options">
            <button class="quiz-option">ganjil</button>
            <button class="quiz-option">genap</button>
            <button class="quiz-option">nol</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Monovarian adalah besaran yang selalu bergerak satu arah (misalnya turun) dan terbatas, sehingga proses pasti berhenti.">
        <p class="quiz-q">Untuk membuktikan sebuah proses pasti berhenti, kita mencari…</p>
        <div class="quiz-options">
            <button class="quiz-option">invarian yang tidak pernah berubah</button>
            <button class="quiz-option">besaran yang selalu turun dan terbatas di bawah</button>
            <button class="quiz-option">contoh penyangkal</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
