@php
    $xbSteps = [
        ['Sisipkan ke basis', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    long long basis[60] = {};          // basis[b]: vektor yang bit tertingginya b
    for (int i = 0; i < n; i++) {
        long long x;
        cin >> x;
        for (int b = 59; b >= 0; b--) {
            if (!(x >> b & 1)) continue;
            if (!basis[b]) {           // belum ada wakil untuk bit b
                basis[b] = x;
                break;
            }
            x ^= basis[b];             // hapus bit b, lanjut ke bit lebih rendah
        }                              // jika x habis menjadi 0: tidak menambah apa-apa
    }
CPP, <<<'TXT'
<p><strong>Soal contoh "XOR Maksimum":</strong> pilih sebagian dari n bilangan (n ≤ 10<sup>5</sup>, setiap bilangan &lt; 2<sup>60</sup>) sehingga XOR semua yang dipilih sebesar mungkin. Ada 2<sup>n</sup> pilihan, terlalu banyak.</p>
<p>Anggap setiap bilangan sebagai vektor 60 bit, dan XOR sebagai penjumlahan vektor modulo 2. Himpunan semua XOR subset adalah <em>ruang vektor</em>, dan ruang itu bisa diwakili paling banyak 60 vektor basis. Penyisipan ini adalah eliminasi Gauss: O(60) per bilangan.</p>
TXT],
        ['Ambil serakah dari bit tertinggi', <<<'CPP'

    long long best = 0;
    for (int b = 59; b >= 0; b--)
        if ((best ^ basis[b]) > best) best ^= basis[b];
    cout << best << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Dari bit tertinggi ke terendah: jika XOR dengan wakil bit b membuat bit b menyala (artinya hasil membesar), ambil. Keputusan di bit tinggi tidak pernah dirusak oleh wakil bit rendah, karena wakil bit rendah tidak punya bit setinggi itu.</p>
<p>Total O(60·n).</p>
TXT],
    ];

    $xbPy = <<<'PY'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
basis = [0] * 60
for v in data[1:1 + n]:
    x = int(v)
    for b in range(59, -1, -1):
        if not x >> b & 1:
            continue
        if not basis[b]:
            basis[b] = x
            break
        x ^= basis[b]
best = 0
for b in range(59, -1, -1):
    if best ^ basis[b] > best:
        best ^= basis[b]
print(best)
PY;

    $xbQuery = <<<'CPP'
// Bisakah x dibentuk sebagai XOR suatu subset?
bool bisa(long long x) {
    for (int b = 59; b >= 0; b--)
        if (x >> b & 1) {
            if (!basis[b]) return false;      // bit b tidak bisa dihapus
            x ^= basis[b];
        }
    return true;                              // x habis menjadi 0
}
// Banyak nilai XOR berbeda (termasuk 0) = 2^rank, rank = banyak basis[b] tidak nol.
// Setiap nilai itu dicapai oleh tepat 2^(n - rank) subset.
CPP;

    $xbKth = <<<'CPP'
// Nilai XOR terkecil ke-k (k mulai 0): ubah basis menjadi "tereduksi" dulu,
// yaitu bit tertinggi setiap wakil tidak muncul di wakil lain.
for (int b = 59; b >= 0; b--)
    if (basis[b])
        for (int c = b - 1; c >= 0; c--)
            if (basis[c] && (basis[b] >> c & 1)) basis[b] ^= basis[c];
vector<long long> v;                          // wakil dari bit rendah ke tinggi
for (int b = 0; b < 60; b++) if (basis[b]) v.push_back(basis[b]);
long long jawab = 0;                          // bit ke-i dari k memilih v[i]
for (size_t i = 0; i < v.size(); i++) if (k >> i & 1) jawab ^= v[i];
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memandang XOR sebagai penjumlahan vektor bit dan membangun <strong>basis linear</strong> dengan eliminasi Gauss.</li>
        <li>Mencari <strong>XOR maksimum</strong> dari subset mana pun dalam O(60·n).</li>
        <li>Menjawab apakah x bisa dibentuk, dan menghitung <strong>banyak nilai XOR berbeda</strong> (2<sup>rank</sup>).</li>
        <li>Mencari nilai XOR terkecil ke-k dengan basis tereduksi.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'XOR sebagai vektor', 'desc' => 'Ribuan bilangan, tetapi paling banyak 60 yang benar-benar penting.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Saklar Lampu">
    <h2>Intuisi: Papan Saklar Lampu</h2>
    <div class="prose">
        <p>Bayangkan 60 lampu dan n tombol. Setiap tombol membalik sekumpulan lampu tertentu (bit-bit bilangannya). Menekan tombol dua kali sama dengan tidak menekan, dan urutan tidak penting: itulah XOR. Pola lampu apa saja yang bisa dicapai?</p>
        <p>Banyak tombol ternyata "mubazir": efeknya bisa ditiru kombinasi tombol lain. Setelah membuang yang mubazir, tersisa paling banyak 60 tombol <strong>bebas linear</strong> yang disebut <strong>basis</strong>. Setiap pola yang bisa dicapai dibentuk dari basis dengan cara yang tunggal, jadi banyak polanya tepat 2<sup>rank</sup>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Ruang XOR</b><span>Himpunan semua XOR subset. Tertutup terhadap XOR dan memuat 0.</span></div>
        <div class="term"><b>Basis</b><span>Himpunan minimal yang menghasilkan ruang yang sama. Paling banyak 60 untuk bilangan &lt; 2<sup>60</sup>.</span></div>
        <div class="term"><b>Rank</b><span>Ukuran basis. Banyak nilai XOR berbeda = 2<sup>rank</sup>.</span></div>
        <div class="term"><b>Basis tereduksi</b><span>Bit tertinggi setiap wakil hanya muncul di wakil itu: untuk XOR terkecil ke-k.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Menyisipkan ke Basis</h2>
    <div class="prose"><p>Setiap bilangan dibersihkan dari bit tertinggi ke bawah dengan wakil yang sudah ada. Jika tersisa sesuatu, sisa itu menjadi wakil baru; jika habis menjadi 0, bilangan itu sudah bisa dibentuk.</p></div>
    <div data-viz="xor-basis"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'basis XOR di C++', 'desc' => 'XOR maksimum, keterwakilan, dan banyak nilai.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: XOR Maksimum</h2>
    @include('lessons.walkthrough', [
        'title' => 'Eliminasi Gauss pada bit',
        'steps' => $xbSteps,
        'sample' => ['input' => "4\n9 8 5 3\n", 'output' => "15\n"],
        'py' => $xbPy,
    ])
    <div class="prose"><p>Contoh: 9 ⊕ 5 ⊕ 3 = 1001 ⊕ 0101 ⊕ 0011 = 1111 = 15, nilai 4 bit terbesar yang mungkin.</p></div>
</section>

<section class="lesson-section" id="pertanyaan" data-toc="Pertanyaan pada Basis">
    <h2>Pertanyaan Lain pada Basis</h2>
    @include('lessons.code', ['cpp' => $xbQuery, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Subset kosong.</strong> XOR subset kosong adalah 0. Jika soal mewajibkan subset tidak kosong, 0 hanya bisa dicapai jika ada bilangan yang mubazir (rank &lt; n).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Geser 1 ke kiri.</strong> <code>1 &lt;&lt; 40</code> meluap di <code>int</code>. Pakai <code>x &gt;&gt; b &amp; 1</code> untuk membaca bit, atau <code>1LL &lt;&lt; b</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'basis tereduksi dan pola', 'desc' => 'Mengurutkan semua nilai XOR tanpa membuatnya.'])

<section class="lesson-section" id="tereduksi" data-toc="Basis Tereduksi & Pola">
    <h2>Basis Tereduksi dan XOR Terkecil ke-k</h2>
    <div class="prose"><p>Jika bit tertinggi setiap wakil tidak muncul di wakil lain, maka memilih subset wakil berarti memilih bit-bit tertinggi itu secara bebas, dan urutan nilainya mengikuti urutan biner pilihan tersebut. Nilai terkecil ke-k didapat dengan membaca k dalam biner.</p></div>
    @include('lessons.code', ['cpp' => $xbKth, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Mengapa XOR maksimum serakah benar?</strong> Proses dari bit tertinggi. Jika kita bisa membuat bit b menyala (karena ada wakil bit b), nilai dengan bit b menyala selalu lebih besar daripada nilai mana pun dengan bit b mati dan bit-bit di atasnya sama. Wakil bit yang lebih rendah tidak pernah mengubah bit b, jadi keputusan ini tidak pernah perlu dibatalkan.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>XOR maksimum subset</h4><p>Basis + serakah dari bit tertinggi.</p></div>
        <div class="pattern"><h4>XOR maksimum jalan di graph</h4><p>XOR satu jalan + basis dari XOR semua siklus.</p></div>
        <div class="pattern"><h4>Banyak subset dengan XOR = x</h4><p>0 atau 2<sup>n − rank</sup>.</p></div>
        <div class="pattern"><h4>Game Nim dengan memilih tumpukan</h4><p>Pemain kedua menang jika ada subset ber-XOR 0: cek rank &lt; n.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="3 = 1 ⊕ 2, jadi hanya 1 dan 2 yang bebas: rank 2, nilai XOR berbeda 2² = 4 (0, 1, 2, 3).">
        <p class="quiz-q">Bilangan {1, 2, 3}. Berapa banyak nilai XOR subset yang berbeda?</p>
        <div class="quiz-options">
            <button class="quiz-option">8</button>
            <button class="quiz-option">4</button>
            <button class="quiz-option">3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Bilangan < 2^60 adalah vektor 60 dimensi; basisnya paling banyak 60 vektor, berapa pun n.">
        <p class="quiz-q">n = 10<sup>5</sup> bilangan &lt; 2<sup>60</sup>. Ukuran basis paling banyak…</p>
        <div class="quiz-options">
            <button class="quiz-option">60</button>
            <button class="quiz-option">10<sup>5</sup></button>
            <button class="quiz-option">2<sup>60</sup></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Rank r berarti setiap nilai yang bisa dicapai dibentuk oleh tepat 2^(n−r) subset: 2^(5−3) = 4.">
        <p class="quiz-q">n = 5 bilangan dengan rank 3. Berapa subset yang XOR-nya sama dengan suatu nilai yang bisa dicapai?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">8</button>
            <button class="quiz-option">4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
