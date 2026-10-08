@php
    $rkSteps = [
        ['Penanda kolom dan diagonal', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n, banyak = 0;
vector<int> posisi;              // posisi[r] = kolom ratu di baris r
vector<bool> kol, d1, d2;
CPP, <<<'TXT'
<p>Kita menaruh N ratu di papan N × N tanpa ada yang saling menyerang. Setiap baris pasti berisi tepat satu ratu, jadi kita isi <strong>baris demi baris</strong>.</p>
<p>Agar pengecekan "petak ini diserang?" berjalan O(1), simpan tiga penanda:</p>
<ul>
    <li><code>kol[c]</code>: kolom c sudah dipakai.</li>
    <li><code>d1[r + c]</code>: diagonal "/" (semua petak di diagonal ini punya r + c yang sama).</li>
    <li><code>d2[r − c + n]</code>: diagonal "\" (r − c sama; ditambah n agar tidak negatif).</li>
</ul>
TXT],
        ['Base case: semua baris terisi', <<<'CPP'

void solve(int r) {
    if (r == n) {
        banyak++;
        for (int i = 0; i < n; i++) cout << posisi[i] + 1 << (i + 1 < n ? ' ' : '\n');
        return;
    }
CPP, <<<'TXT'
<p>Setiap fungsi rekursif butuh <strong>base case</strong>: kondisi berhenti tanpa memanggil dirinya lagi. Di sini, jika r sudah sama dengan n, semua baris berisi ratu yang aman, jadi kita menemukan satu solusi. Cetak kolomnya (mulai dari 1) dan kembali.</p>
TXT],
        ['Coba setiap kolom, pangkas yang diserang', <<<'CPP'
    for (int c = 0; c < n; c++) {
        if (kol[c] || d1[r + c] || d2[r - c + n]) continue;
CPP, <<<'TXT'
<p>Untuk baris r, coba setiap kolom. Jika petaknya diserang, langsung <strong>lewati</strong>: semua susunan lanjutan dari pilihan ini pasti salah, jadi tidak perlu dijelajahi. Inilah <strong>pemangkasan</strong> (<em>pruning</em>), sumber kecepatan backtracking.</p>
TXT],
        ['Pilih, telusuri, batalkan', <<<'CPP'
        kol[c] = d1[r + c] = d2[r - c + n] = true;
        posisi[r] = c;
        solve(r + 1);
        kol[c] = d1[r + c] = d2[r - c + n] = false;
    }
}
CPP, <<<'TXT'
<p>Pola inti backtracking selalu tiga langkah:</p>
<ol>
    <li><strong>Pilih</strong>: taruh ratu, tandai kolom dan diagonalnya.</li>
    <li><strong>Telusuri</strong>: panggil rekursi untuk baris berikutnya.</li>
    <li><strong>Batalkan</strong>: angkat ratunya lagi (hapus tanda), agar keadaan kembali seperti sebelum memilih dan kolom berikutnya bisa dicoba dengan bersih.</li>
</ol>
<div class="wt-warn">Lupa langkah "batalkan" adalah bug backtracking yang paling sering: tanda dari cabang sebelumnya masih tertinggal dan membuat cabang lain dianggap diserang.</div>
TXT],
        ['Program utama', <<<'CPP'

int main() {
    cin >> n;
    posisi.assign(n, 0);
    kol.assign(n, false);
    d1.assign(2 * n, false);
    d2.assign(2 * n + 1, false);
    solve(0);
    cout << banyak << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Mulai dari baris 0. Untuk N = 6 ada 4 solusi; masing-masing dicetak sebagai daftar kolom ratu di baris 1 sampai 6, lalu banyaknya solusi.</p>
TXT],
    ];

    $rkPy = <<<'PY'
import sys
sys.setrecursionlimit(10000)

n = int(input())
posisi = [0] * n
kol = [False] * n
d1 = [False] * (2 * n)
d2 = [False] * (2 * n + 1)
banyak = 0

def solve(r):
    global banyak
    if r == n:
        banyak += 1
        print(*[c + 1 for c in posisi])
        return
    for c in range(n):
        if kol[c] or d1[r + c] or d2[r - c + n]:
            continue
        kol[c] = d1[r + c] = d2[r - c + n] = True
        posisi[r] = c
        solve(r + 1)
        kol[c] = d1[r + c] = d2[r - c + n] = False

solve(0)
print(banyak)
PY;

    $rkFact = <<<'CPP'
long long faktorial(int n) {
    if (n == 0) return 1;            // base case
    return n * faktorial(n - 1);     // langkah rekursif: soal lebih kecil
}
// faktorial(4) → 4 * faktorial(3) → 4 * 3 * faktorial(2) → ... → 24
CPP;

    $rkSubset = <<<'CPP'
// Semua himpunan bagian dari a[0..n-1]: setiap elemen DIAMBIL atau TIDAK.
vector<int> pilih;
void subset(int i) {
    if (i == n) {                    // semua elemen sudah diputuskan
        cetak(pilih);
        return;
    }
    subset(i + 1);                   // cabang 1: a[i] tidak diambil
    pilih.push_back(a[i]);           // cabang 2: a[i] diambil
    subset(i + 1);
    pilih.pop_back();                // batalkan
}
// 2^n himpunan bagian
CPP;

    $rkPerm = <<<'CPP'
// Semua permutasi 1..n: setiap posisi diisi angka yang BELUM dipakai.
vector<int> perm;
vector<bool> dipakai;
void permutasi() {
    if ((int)perm.size() == n) { cetak(perm); return; }
    for (int x = 1; x <= n; x++) {
        if (dipakai[x]) continue;
        dipakai[x] = true;  perm.push_back(x);     // pilih
        permutasi();                               // telusuri
        dipakai[x] = false; perm.pop_back();       // batalkan
    }
}
// n! permutasi. Alternatif singkat: next_permutation dari <algorithm>.
CPP;

    $rkMasterTable = <<<'CPP'
// Pohon rekursi f(n) = 2 f(n/2) + O(n)   (misalnya merge sort)
// level 0:  1 panggilan × n        = n
// level 1:  2 panggilan × n/2      = n
// level 2:  4 panggilan × n/4      = n
// ...       log2(n) level          → total O(n log n)
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menulis fungsi rekursif dengan <strong>base case</strong> dan <strong>langkah rekursif</strong> yang benar.</li>
        <li>Membayangkan <strong>call stack</strong> dan <strong>pohon rekursi</strong> untuk menghitung kompleksitas.</li>
        <li>Memakai pola backtracking <strong>pilih – telusuri – batalkan</strong> untuk himpunan bagian, permutasi, dan N-Queens.</li>
        <li>Mempercepat pencarian dengan <strong>pemangkasan</strong>, dan melihat kapan rekursi perlu memo (DP).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'fungsi yang memanggil dirinya sendiri', 'desc' => 'Base case, call stack, dan pohon rekursi.'])

<section class="lesson-section" id="rekursi" data-toc="Apa Itu Rekursi?">
    <h2>Apa Itu Rekursi?</h2>
    <div class="prose">
        <p><strong>Rekursi</strong> adalah cara menyelesaikan soal dengan memanggil fungsi yang sama untuk soal yang <strong>lebih kecil</strong>. Contohnya faktorial: 5! = 5 × 4!, dan 4! = 4 × 3!, terus sampai 0! = 1.</p>
        <p>Setiap fungsi rekursif yang benar punya dua bagian:</p>
        <div class="term-grid">
            <div class="term"><b>Base case</b><span>Soal terkecil yang jawabannya langsung diketahui, tanpa memanggil diri sendiri. Tanpa ini, rekursi tidak pernah berhenti (stack overflow).</span></div>
            <div class="term"><b>Langkah rekursif</b><span>Memecah soal menjadi soal yang lebih kecil, memanggil diri sendiri, lalu menggabungkan hasilnya. Soalnya harus benar-benar mengecil menuju base case.</span></div>
        </div>
    </div>
    @include('lessons.code', ['cpp' => $rkFact, 'js' => null, 'py' => null])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Lompatan keyakinan</strong> (<em>leap of faith</em>): saat menulis <code>faktorial(n)</code>, anggap saja <code>faktorial(n − 1)</code> sudah benar. Cukup pastikan base case benar dan langkah rekursifnya menggabungkan hasil dengan benar.</p>
    </div>
</section>

<section class="lesson-section" id="stack" data-toc="Call Stack">
    <h2>Call Stack</h2>
    <div class="prose">
        <p>Setiap pemanggilan fungsi menyimpan variabel lokalnya di <strong>call stack</strong>. Pemanggilan yang belum selesai menunggu di bawah, dan yang terbaru selalu di puncak.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Langkah</th><th>Isi call stack (puncak di kanan)</th><th>Keterangan</th></tr>
            <tr><td>1</td><td>f(3)</td><td>f(3) butuh f(2)</td></tr>
            <tr><td>2</td><td>f(3) f(2)</td><td>f(2) butuh f(1)</td></tr>
            <tr><td>3</td><td>f(3) f(2) f(1)</td><td>f(1) butuh f(0)</td></tr>
            <tr class="hl"><td>4</td><td>f(3) f(2) f(1) f(0)</td><td>base case: kembalikan 1</td></tr>
            <tr><td>5</td><td>f(3) f(2) f(1)</td><td>1 × 1 = 1</td></tr>
            <tr><td>6</td><td>f(3) f(2)</td><td>2 × 1 = 2</td></tr>
            <tr class="ok"><td>7</td><td>f(3)</td><td>3 × 2 = 6, selesai</td></tr>
        </table>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Kedalaman stack terbatas. Rekursi sedalam 10<sup>6</sup> pemanggilan bisa membuat program crash (<em>stack overflow</em>, terlihat sebagai RTE). Untuk rekursi yang sangat dalam, ubah menjadi loop dengan stack sendiri.</p>
    </div>
</section>

<section class="lesson-section" id="backtrack" data-toc="Backtracking">
    <h2>Backtracking: Mencoba Semua Pilihan dengan Rapi</h2>
    <div class="prose">
        <p>Backtracking adalah rekursi untuk mencari <strong>semua kemungkinan</strong>: setiap level rekursi mengambil satu keputusan, mencoba setiap pilihannya satu per satu, dan <strong>membatalkan</strong> pilihan sebelum mencoba yang berikutnya.</p>
    </div>
    @include('lessons.code', ['cpp' => $rkSubset, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $rkPerm, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Kerangkanya selalu sama: <strong>jika sudah lengkap → catat; jika belum → untuk setiap pilihan yang sah: pilih, rekursi, batalkan</strong>. Yang berubah hanya apa yang menjadi "pilihan" dan kapan pilihan dianggap sah.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi N-Queens">
    <h2>Visualisasi: N-Queens</h2>
    <div class="prose"><p>Petak merah adalah petak yang diserang dan langsung dilewati (pemangkasan). Perhatikan bagaimana ratu diangkat kembali saat sebuah baris buntu, lalu kolom berikutnya dicoba.</p></div>
    <div data-viz="nqueen"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Tentukan keputusan per level</b><span>Contoh: elemen ke-i diambil atau tidak; posisi ke-i diisi angka apa; ratu baris r di kolom mana.</span></div>
        <div class="step-card"><b>Base case</b><span>Semua keputusan sudah diambil → catat jawabannya.</span></div>
        <div class="step-card"><b>Pilih – telusuri – batalkan</b><span>Pastikan semua perubahan keadaan dikembalikan setelah rekursi.</span></div>
        <div class="step-card"><b>Pangkas</b><span>Hentikan cabang yang pasti gagal sedini mungkin.</span></div>
        <div class="step-card"><b>Hitung ukuran pencarian</b><span>2<sup>n</sup> atau n! harus masuk batas waktu (lihat tabel batasan di materi Kompleksitas).</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'N-Queens di C++', 'desc' => 'Backtracking lengkap dengan penanda diagonal O(1).'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: N-Queens</h2>
    @include('lessons.walkthrough', [
        'title' => 'Backtracking N-Queens',
        'steps' => $rkSteps,
        'sample' => ['input' => "6\n", 'output' => "2 4 6 1 3 5\n3 6 2 5 1 4\n4 1 5 2 6 3\n5 3 1 6 4 2\n4\n"],
        'py' => $rkPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas Rekursi">
    <h2>Kompleksitas Rekursi dan Backtracking</h2>
    <table class="cx-table">
        <tr><th>Pola</th><th>Banyak cabang</th><th>Batas n yang aman</th></tr>
        <tr><td>Himpunan bagian</td><td><code>2ⁿ</code></td><td>sekitar 20–25</td></tr>
        <tr><td>Permutasi</td><td><code>n!</code></td><td>sekitar 9–11</td></tr>
        <tr><td>N-Queens dengan pemangkasan</td><td>jauh di bawah <code>nⁿ</code></td><td>sekitar 12–14</td></tr>
    </table>
    @include('lessons.code', ['cpp' => $rkMasterTable, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 3, 'title' => 'dari rekursi ke DP', 'desc' => 'Kapan backtracking terlalu lambat, dan bagaimana memo menyelamatkannya.'])

<section class="lesson-section" id="ke-dp" data-toc="Rekursi ke DP">
    <h2>Dari Backtracking ke Dynamic Programming</h2>
    <div class="prose">
        <p>Backtracking mencoba semua jalur. Jika banyak jalur berbeda berakhir di <strong>keadaan yang sama</strong> (misalnya "sudah sampai indeks i dengan jumlah s"), pekerjaan setelah keadaan itu dihitung berulang-ulang. Menyimpan hasil per keadaan (memo) mengubah backtracking eksponensial menjadi DP polinomial.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Tidak ada keadaan berulang</h4><p>Permutasi, N-Queens: tetap backtracking, perkuat pemangkasan.</p></div>
        <div class="pattern"><h4>Keadaan berulang</h4><p>Subset sum, koin, jalur grid: tambahkan memo → DP.</p></div>
        <div class="pattern"><h4>Meet in the middle</h4><p>n ≈ 40: bagi dua, backtracking masing-masing 2<sup>20</sup>, lalu gabungkan dengan sort + binary search.</p></div>
        <div class="pattern"><h4>Urutan pilihan</h4><p>Coba pilihan yang paling mungkin berhasil dulu agar solusi pertama cepat ditemukan.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="Tanpa base case, fungsi terus memanggil dirinya sampai stack habis.">
        <p class="quiz-q">Apa yang terjadi jika fungsi rekursif tidak punya base case?</p>
        <div class="quiz-options">
            <button class="quiz-option">Rekursi tidak berhenti sampai stack overflow</button>
            <button class="quiz-option">Fungsi mengembalikan 0</button>
            <button class="quiz-option">Compiler menolak kodenya</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Setiap elemen punya dua pilihan (ambil/tidak), jadi 2 × 2 × … × 2 = 2¹⁰ = 1024.">
        <p class="quiz-q">Berapa banyak himpunan bagian dari 10 benda?</p>
        <div class="quiz-options">
            <button class="quiz-option">10</button>
            <button class="quiz-option">100</button>
            <button class="quiz-option">1024</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Petak (r, c) dan (r', c') sediagonal '/' jika r + c = r' + c'. Nilai r + c berkisar 0..2n−2.">
        <p class="quiz-q">Dua petak berada di diagonal "/" yang sama jika…</p>
        <div class="quiz-options">
            <button class="quiz-option">r − c sama</button>
            <button class="quiz-option">r + c sama</button>
            <button class="quiz-option">r · c sama</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
