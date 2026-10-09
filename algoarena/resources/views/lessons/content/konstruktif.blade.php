@php
    $koSteps = [
        ['Kasus kecil yang mustahil', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    if (n == 2 || n == 3) {                  // ditemukan dengan mencoba tangan / brute force
        cout << "NO SOLUTION\n";
        return 0;
    }
CPP, <<<'TXT'
<p><strong>Soal contoh "Permutasi Indah":</strong> susun permutasi 1..n sehingga tidak ada dua bilangan bersebelahan yang selisihnya 1. Cetak satu permutasi seperti itu, atau <code>NO SOLUTION</code>.</p>
<p>Langkah pertama soal konstruktif: coba n kecil. n = 1: "1". n = 2: "1 2" dan "2 1" sama-sama gagal. n = 3: angka 2 selalu bertetangga dengan 1 atau 3. n = 4: "2 4 1 3" berhasil.</p>
TXT],
        ['Pisahkan genap dan ganjil', <<<'CPP'

    string out;
    for (int x = 2; x <= n; x += 2) out += to_string(x) + " ";   // semua genap dulu
    for (int x = 1; x <= n; x += 2) out += to_string(x) + " ";   // lalu semua ganjil
    out.back() = '\n';
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Di dalam kelompok genap, tetangga berselisih 2; begitu juga di kelompok ganjil. Satu-satunya sambungan berbahaya adalah genap terbesar lalu 1. Genap terbesar ≥ 4 untuk n ≥ 4, jadi selisihnya ≥ 3. Aman.</p>
<p>Perhatikan: soal ini menerima <em>jawaban apa pun</em> yang sah. Kita tidak perlu mencari semua permutasi, cukup satu pola yang bisa dibuktikan.</p>
TXT],
    ];

    $koPy = <<<'PY'
n = int(input())
if n in (2, 3):
    print("NO SOLUTION")
else:
    print(" ".join(map(str, list(range(2, n + 1, 2)) + list(range(1, n + 1, 2)))))
PY;

    $koBrute = <<<'CPP'
// Cari pola: brute force semua permutasi untuk n kecil, cetak yang memenuhi
for (int n = 1; n <= 7; n++) {
    vector<int> p(n);
    iota(p.begin(), p.end(), 1);
    int banyak = 0;
    do {
        bool ok = true;
        for (int i = 0; i + 1 < n; i++) if (abs(p[i] - p[i + 1]) == 1) ok = false;
        if (ok && banyak++ < 3) cetak(p);       // lihat beberapa contoh pertama
    } while (next_permutation(p.begin(), p.end()));
    cout << "n = " << n << ": " << banyak << " solusi\n";
}
CPP;

    $koCheck = <<<'CPP'
// Checker: pastikan jawaban konstruktifmu benar-benar sah sebelum dikirim
bool sah(int n, const vector<int>& p) {
    if ((int)p.size() != n) return false;
    vector<bool> ada(n + 1, false);
    for (int x : p) {
        if (x < 1 || x > n || ada[x]) return false;   // harus permutasi
        ada[x] = true;
    }
    for (int i = 0; i + 1 < n; i++)
        if (abs(p[i] - p[i + 1]) == 1) return false;
    return true;
}
CPP;

    $koLex = <<<'CPP'
// Pola "terkecil secara leksikografis": isi dari kiri, pilih nilai terkecil
// yang masih MENYISAKAN jawaban sah untuk posisi-posisi berikutnya.
for (int i = 0; i < n; i++) {
    for (int v = palingKecil(i); ; v++) {
        if (masihBisaDiselesaikan(i, v)) {    // uji kelayakan yang cepat (sering berupa rumus)
            jawab[i] = v;
            break;
        }
    }
}
// Contoh: bilangan n digit dengan jumlah digit S → masihBisa: S - v <= 9 * (n - 1 - i)
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menemukan pola konstruksi dengan <strong>mencoba kasus kecil</strong> (tangan atau brute force).</li>
        <li>Memakai pola umum: <strong>pisah paritas</strong>, zigzag, berpasangan, dan simetri.</li>
        <li>Membangun jawaban <strong>leksikografis terkecil</strong> dengan uji kelayakan di setiap langkah.</li>
        <li>Menulis <strong>checker</strong> untuk memastikan jawaban konstruktif sah.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'membangun, bukan mencari', 'desc' => 'Kadang jawabannya lebih mudah dibuat daripada dicari.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Arsitek, Bukan Detektif">
    <h2>Intuisi: Menjadi Arsitek, Bukan Detektif</h2>
    <div class="prose">
        <p>Di banyak soal kita mencari jawaban terbaik di antara kemungkinan yang sangat banyak. Soal <strong>konstruktif</strong> berbeda: kita diminta <em>membuat</em> satu objek (permutasi, string, grid, graph) yang memenuhi syarat. Mencari dengan brute force mustahil, tetapi sering ada pola sederhana yang selalu berhasil.</p>
        <p>Cara menemukannya mirip eksperimen sains: coba n = 1, 2, 3, 4, 5 dengan tangan atau program kecil, perhatikan polanya, tebak aturan umumnya, lalu buktikan. Soal <strong>ad-hoc</strong> juga begitu: tidak ada algoritma baku, yang ada observasi.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Konstruktif</b><span>Bangun satu objek yang sah, atau buktikan tidak ada.</span></div>
        <div class="term"><b>Observasi</b><span>Fakta kecil tentang struktur soal yang memangkas kemungkinan.</span></div>
        <div class="term"><b>Checker</b><span>Program yang memeriksa apakah sebuah jawaban sah.</span></div>
        <div class="term"><b>Leksikografis terkecil</b><span>Jika ada banyak jawaban dan soal meminta yang "terkecil", bangun dari kiri.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Bilangan dengan Jumlah Digit Tertentu</h2>
    <div class="prose"><p>Bangun bilangan n digit dengan jumlah digit S yang terkecil atau terbesar. Setiap digit dipilih sekecil (atau sebesar) mungkin, asalkan digit-digit di kanannya <em>masih bisa</em> menampung sisa jumlahnya.</p></div>
    <div data-viz="constructive"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'konstruksi di C++', 'desc' => 'Pola pisah paritas, brute force pencari pola, dan checker.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Permutasi Indah</h2>
    @include('lessons.walkthrough', [
        'title' => 'Genap dulu, lalu ganjil',
        'steps' => $koSteps,
        'sample' => ['input' => "5\n", 'output' => "2 4 1 3 5\n"],
        'py' => $koPy,
    ])
</section>

<section class="lesson-section" id="alat" data-toc="Brute Force & Checker">
    <h2>Dua Alat Wajib: Pencari Pola dan Checker</h2>
    <div class="prose"><p>Program brute force kecil memperlihatkan solusi untuk n kecil dan kapan solusinya tidak ada. Dari situ pola sering terlihat.</p></div>
    @include('lessons.code', ['cpp' => $koBrute, 'js' => null, 'py' => null])
    <div class="prose"><p>Jawaban konstruktif mudah salah di kasus tepi. Checker yang sederhana bisa dijalankan pada semua n dari 1 sampai 1000 dalam sekejap.</p></div>
    @include('lessons.code', ['cpp' => $koCheck, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Konstruksi">
    <h2>Pola Konstruksi yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Pisah paritas</h4><p>Genap lalu ganjil; posisi ganjil untuk kelompok A, posisi genap untuk B.</p></div>
        <div class="pattern"><h4>Zigzag</h4><p>1, n, 2, n − 1, …: selisih besar dan mengecil bergantian.</p></div>
        <div class="pattern"><h4>Berpasangan</h4><p>Pasangkan i dengan n + 1 − i agar jumlahnya sama.</p></div>
        <div class="pattern"><h4>Dari kiri + kelayakan</h4><p>Pilih terkecil yang masih menyisakan jawaban sah.</p></div>
        <div class="pattern"><h4>Mulai dari jawaban</h4><p>Bangun mundur dari keadaan akhir yang diinginkan.</p></div>
        <div class="pattern"><h4>Kasus kecil manual</h4><p>n ≤ 3 sering istimewa; selesaikan terpisah lalu pakai pola umum.</p></div>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Kasus tepi.</strong> Pola umum sering gagal untuk n = 1, 2, 3 atau ketika semua elemen sama. Jalankan checker untuk semua n kecil.</p>
    </div>
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Jika soal menerima banyak jawaban, judge memakai checker. Di AlgoArena, soal konstruktif selalu meminta jawaban yang <em>tunggal</em> (misalnya leksikografis terkecil) agar bisa dinilai dengan perbandingan output biasa.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'leksikografis terkecil', 'desc' => 'Greedy dari kiri dengan uji kelayakan.'])

<section class="lesson-section" id="leks" data-toc="Leksikografis Terkecil">
    <h2>Membangun Jawaban Leksikografis Terkecil</h2>
    <div class="prose"><p>Barisan A lebih kecil dari B secara leksikografis jika pada posisi pertama yang berbeda, A lebih kecil. Artinya posisi paling kiri paling menentukan: lebih baik memperkecil posisi 1 sebanyak mungkin, walaupun posisi-posisi sesudahnya jadi besar.</p></div>
    @include('lessons.code', ['cpp' => $koLex, 'js' => null, 'py' => null])
    <div class="proof">
        <p>Mengapa benar? Misalkan di posisi i kita memilih v, nilai terkecil yang masih bisa diselesaikan. Jawaban mana pun dengan prefiks yang sama tetapi nilai lebih kecil di posisi i tidak bisa diselesaikan (uji kelayakan bilang tidak). Jawaban dengan nilai lebih besar di posisi i pasti lebih besar secara leksikografis. Jadi pilihan kita ada di jawaban terkecil, dan induksi menyelesaikan posisi berikutnya.</p>
        <p>Kuncinya adalah <strong>uji kelayakan yang cepat</strong>. Untuk bilangan dengan jumlah digit S: sisa jumlah harus di antara 0 dan 9 × (banyak posisi tersisa). Untuk permutasi dengan k inversi: sisa inversi harus ≤ banyak inversi maksimum dari elemen yang tersisa.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Untuk n = 3, angka 2 berada di mana pun pasti bertetangga dengan 1 atau 3 (selisih 1).">
        <p class="quiz-q">Mengapa n = 3 tidak punya permutasi indah?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena 3 ganjil</button>
            <button class="quiz-option">Karena angka 2 selalu bertetangga dengan 1 atau 3</button>
            <button class="quiz-option">Karena hanya ada 6 permutasi</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Digit pertama minimal max(1, 15 − 18) = 1, digit kedua minimal max(0, 14 − 9) = 5, digit ketiga 9: 159.">
        <p class="quiz-q">Bilangan 3 digit terkecil dengan jumlah digit 15?</p>
        <div class="quiz-options">
            <button class="quiz-option">159</button>
            <button class="quiz-option">069</button>
            <button class="quiz-option">177</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Posisi pertama yang berbeda adalah posisi kedua: 2 < 3, jadi [1, 2, 9] lebih kecil walaupun elemen terakhirnya lebih besar.">
        <p class="quiz-q">Mana yang lebih kecil secara leksikografis?</p>
        <div class="quiz-options">
            <button class="quiz-option">[1, 3, 0]</button>
            <button class="quiz-option">Sama saja</button>
            <button class="quiz-option">[1, 2, 9]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
