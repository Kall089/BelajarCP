@php
    $sdSteps = [
        ['Data dengan urutan masuk', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

struct Peserta {
    string nama;
    int nilai;
};

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<Peserta> p(n);
    for (auto& x : p) cin >> x.nama >> x.nilai;
CPP, <<<'TXT'
<p><strong>Soal contoh "Pengumuman Nilai":</strong> n peserta mendaftar berurutan. Urutkan berdasarkan nilai <strong>menurun</strong>; peserta dengan nilai sama harus tetap dalam <strong>urutan pendaftaran</strong>. Cetak namanya.</p>
<p>Syarat kedua adalah definisi <strong>stabil</strong>: elemen yang dianggap sama oleh comparator tidak boleh bertukar urutan.</p>
TXT],
        ['stable_sort menjaga urutan seri', <<<'CPP'

    stable_sort(p.begin(), p.end(), [](const Peserta& a, const Peserta& b) {
        return a.nilai > b.nilai;
    });
CPP, <<<'TXT'
<p><code>std::sort</code> <em>tidak</em> menjamin stabil: dua peserta bernilai 80 bisa keluar dalam urutan mana pun. <code>stable_sort</code> menjamin urutan aslinya dipertahankan, dengan biaya O(n log n) juga (memakai merge sort).</p>
<div class="wt-tip">Cara lain: tambahkan indeks pendaftaran sebagai kriteria terakhir di comparator, lalu pakai <code>sort</code> biasa. Hasilnya sama.</div>
TXT],
        ['Cetak', <<<'CPP'

    for (const auto& x : p) cout << x.nama << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Insertion sort juga stabil (ia tidak pernah melompati elemen yang sama, karena berhenti saat <code>a[j] ≤ x</code>). Selection sort biasa <em>tidak</em> stabil, karena pertukaran jarak jauh bisa melompati elemen kembar.</p>
TXT],
    ];

    $sdPy = <<<'PY'
import sys
data = sys.stdin.read().split()
n = int(data[0])
p = [(data[1 + 2*i], int(data[2 + 2*i])) for i in range(n)]
p.sort(key=lambda x: -x[1])        # sort Python selalu stabil
print("\n".join(nama for nama, _ in p))
PY;

    $sdInsertion = <<<'CPP'
// Insertion sort: cepat untuk n kecil atau data yang HAMPIR urut
void insertionSort(vector<int>& a) {
    for (int i = 1; i < (int)a.size(); i++) {
        int x = a[i], j = i - 1;
        while (j >= 0 && a[j] > x) {   // > (bukan >=) menjaga stabilitas
            a[j + 1] = a[j];
            j--;
        }
        a[j + 1] = x;
    }
}
CPP;

    $sdCycle = <<<'CPP'
// Banyak tukar minimum untuk mengurutkan permutasi 1..n (tukar dua elemen sembarang)
// = n - (banyak siklus) pada graph i -> p[i]
vector<bool> lihat(n + 1, false);
int siklus = 0;
for (int i = 1; i <= n; i++) {
    if (lihat[i]) continue;
    siklus++;
    for (int j = i; !lihat[j]; j = p[j]) lihat[j] = true;
}
int jawab = n - siklus;
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan dan menulis bubble, selection, dan insertion sort beserta invarian masing-masing.</li>
        <li>Menghitung biaya ketiganya, dan tahu kapan insertion sort justru cepat.</li>
        <li>Memahami <strong>stabilitas</strong>, memakai <code>stable_sort</code>, dan mengurutkan dengan beberapa kriteria.</li>
        <li>Menghitung tukar minimum lewat siklus permutasi.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'tiga cara klasik', 'desc' => 'Bubble, selection, dan insertion sort, serta janji yang dijaga masing-masing.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Kartu di Tangan">
    <h2>Intuisi: Mengurutkan Kartu di Tangan</h2>
    <div class="prose">
        <p>Saat bermain kartu, kebanyakan orang mengambil kartu satu per satu dan <strong>menyisipkannya</strong> ke tempat yang benar di antara kartu yang sudah dipegang. Itulah <strong>insertion sort</strong>.</p>
        <p>Cara lain: cari kartu terkecil di meja, taruh paling kiri; cari terkecil berikutnya, dan seterusnya (<strong>selection sort</strong>). Atau berulang kali menukar dua kartu bersebelahan yang terbalik sampai tidak ada yang terbalik (<strong>bubble sort</strong>).</p>
    </div>
    <table class="cx-table">
        <tr><th>Algoritma</th><th>Invarian (yang dijaga)</th><th>Waktu</th><th>Stabil?</th></tr>
        <tr><td>Bubble</td><td>Setelah putaran i, i elemen terbesar sudah di ujung kanan</td><td><code>O(n²)</code></td><td>Ya</td></tr>
        <tr><td>Selection</td><td>Setelah putaran i, a[0..i] berisi i+1 elemen terkecil, urut</td><td><code>O(n²)</code> selalu</td><td>Tidak</td></tr>
        <tr><td>Insertion</td><td>Setelah langkah i, a[0..i] urut (relatif)</td><td><code>O(n + inversi)</code></td><td>Ya</td></tr>
    </table>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Bandingkan Ketiganya</h2>
    <div class="prose"><p>Jalankan ketiga algoritma pada array yang sama dan bandingkan banyak perbandingan serta tukar/geser. Coba array yang hampir urut, misalnya <code>1 2 3 5 4 6 7</code>: insertion sort hampir tidak bekerja.</p></div>
    <div data-viz="sort-basic"></div>
    @include('lessons.code', ['cpp' => $sdInsertion, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 2, 'title' => 'stabilitas dan multi-kriteria', 'desc' => 'Mengapa urutan elemen yang "sama" bisa penting.'])

<section class="lesson-section" id="stabil" data-toc="Stabilitas">
    <h2>Stabilitas</h2>
    <div class="prose">
        <p>Sorting disebut <strong>stabil</strong> jika dua elemen yang dianggap sama oleh comparator tetap dalam urutan aslinya. Ini penting ketika data punya informasi lain di luar kunci pengurutan.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th></th><th>Urutan</th></tr>
            <tr><td>Input (nama, nilai)</td><td>Ani 80, Budi 90, Cici 80, Dedi 70</td></tr>
            <tr class="ok"><td>Stabil (nilai ↓)</td><td>Budi 90, <b>Ani 80, Cici 80</b>, Dedi 70</td></tr>
            <tr class="hl"><td>Tidak stabil (mungkin)</td><td>Budi 90, <b>Cici 80, Ani 80</b>, Dedi 70</td></tr>
        </table>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Trik mengurutkan dengan beberapa kriteria:</strong> urutkan secara stabil berdasarkan kriteria <em>terakhir</em> lebih dulu, lalu kriteria sebelumnya, sampai kriteria utama. Atau, cara yang lebih sering: satu comparator yang memeriksa semua kriteria berurutan.</p>
    </div>
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Pengumuman Nilai</h2>
    @include('lessons.walkthrough', [
        'title' => 'stable_sort',
        'steps' => $sdSteps,
        'sample' => ['input' => "4\nani 80\nbudi 90\ncici 80\ndedi 70\n", 'output' => "budi\nani\ncici\ndedi\n"],
        'py' => $sdPy,
    ])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Kasus</th><th>Bubble</th><th>Selection</th><th>Insertion</th></tr>
        <tr><td>Sudah urut</td><td><code>O(n)</code>*</td><td><code>O(n²)</code></td><td><code>O(n)</code></td></tr>
        <tr><td>Terbalik</td><td><code>O(n²)</code></td><td><code>O(n²)</code></td><td><code>O(n²)</code></td></tr>
        <tr><td>Banyak tukar/tulis</td><td>banyak</td><td><b>paling sedikit</b> (≤ n − 1 tukar)</td><td>banyak</td></tr>
    </table>
    <p class="muted">* dengan penanda "tidak ada tukar di putaran ini → berhenti".</p>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jangan pakai O(n²) untuk n besar.</strong> Untuk n = 2 · 10<sup>5</sup>, n² = 4 · 10<sup>10</sup>. Di kontes, pakai <code>sort</code>/<code>stable_sort</code>. Algoritma O(n²) dipelajari untuk memahami invarian, menghitung tukar, dan untuk n yang sangat kecil.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'tukar dan siklus', 'desc' => 'Banyak tukar minimum, dan hubungan geseran dengan inversi.'])

<section class="lesson-section" id="siklus" data-toc="Tukar Minimum & Siklus">
    <h2>Tukar Minimum dan Siklus Permutasi</h2>
    <div class="prose">
        <p>Jika boleh menukar dua elemen <em>sembarang</em>, berapa tukar minimum untuk mengurutkan permutasi? Gambar panah i → p[i]: permutasi terpecah menjadi <strong>siklus</strong>. Siklus sepanjang k butuh tepat k − 1 tukar. Jadi jawabannya <code>n − (banyak siklus)</code>.</p>
    </div>
    @include('lessons.code', ['cpp' => $sdCycle, 'js' => null, 'py' => null])
    <div class="proof">
        <p>Setiap tukar mengubah banyak siklus tepat ±1. Permutasi terurut punya n siklus (semua titik tetap). Jadi dari c siklus butuh paling sedikit n − c tukar, dan selection sort pada setiap siklus mencapai batas itu.</p>
        <p>Untuk tukar <em>bersebelahan</em> (bubble sort), jawabannya berbeda: banyak <strong>inversi</strong>, karena setiap tukar bersebelahan mengubah banyak inversi tepat 1. Banyak geseran insertion sort juga sama dengan banyak inversi. Menghitung inversi dengan cepat dibahas di materi Merge Sort.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Urutan seri harus tetap</h4><p><code>stable_sort</code> atau tambahkan indeks.</p></div>
        <div class="pattern"><h4>Tukar sembarang minimum</h4><p>n − banyak siklus.</p></div>
        <div class="pattern"><h4>Tukar bersebelahan minimum</h4><p>Banyak inversi.</p></div>
        <div class="pattern"><h4>Data hampir urut</h4><p>Insertion sort O(n + inversi).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Pada array yang sudah urut, insertion sort hanya membandingkan setiap elemen sekali dengan tetangga kirinya: O(n).">
        <p class="quiz-q">Algoritma mana yang O(n) pada array yang sudah urut?</p>
        <div class="quiz-options">
            <button class="quiz-option">Selection sort</button>
            <button class="quiz-option">Semuanya O(n²)</button>
            <button class="quiz-option">Insertion sort</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Siklus: 1→3→2→1 (panjang 3) dan 4→4 (panjang 1). Banyak siklus 2, jadi 4 − 2 = 2 tukar.">
        <p class="quiz-q">p = [3, 1, 2, 4] (p[1] = 3, …). Tukar sembarang minimum untuk mengurutkannya?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Stabil berarti elemen yang dianggap sama tetap dalam urutan aslinya.">
        <p class="quiz-q">Apa arti sorting yang stabil?</p>
        <div class="quiz-options">
            <button class="quiz-option">Elemen yang sama tetap dalam urutan aslinya</button>
            <button class="quiz-option">Waktunya selalu O(n log n)</button>
            <button class="quiz-option">Tidak memakai memori tambahan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
