@php
    $bsetSteps = [
        ['Ukuran bitset harus konstanta', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int MAXS = 100000;
CPP, <<<'TXT'
<p><strong>Soal contoh "Bisa Dibayar Pas?":</strong> ada n uang logam dengan nilai <code>a<sub>i</sub></code>, masing-masing hanya boleh dipakai sekali. Untuk setiap jumlah s dari 1 sampai S, apakah s bisa dibayar pas? Cetak banyaknya jumlah yang bisa.</p>
<p><code>bitset&lt;N&gt;</code> butuh N yang diketahui saat kompilasi. Pakai batas atas dari soal (di sini total nilai ≤ 10<sup>5</sup>), bukan variabel dari input.</p>
TXT],
        ['Satu bit untuk setiap jumlah', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, S;
    cin >> n >> S;
    bitset<MAXS + 1> dp;
    dp[0] = 1;                       // jumlah 0: tidak mengambil apa pun
CPP, <<<'TXT'
<p><code>dp[s] = 1</code> artinya "jumlah s bisa dibentuk dari koin yang sudah diproses". Ini persis tabel DP subset sum, tetapi setiap sel hanya satu <strong>bit</strong>, dan 64 sel tersimpan dalam satu kata mesin.</p>
TXT],
        ['Satu baris menggantikan satu loop', <<<'CPP'
    for (int i = 0; i < n; i++) {
        int a;
        cin >> a;
        dp |= dp << a;               // "ambil koin a" untuk SEMUA jumlah sekaligus
    }
CPP, <<<'TXT'
<p>Versi tanpa bitset butuh loop dalam: <code>for (s = S; s &gt;= a; s--) dp[s] |= dp[s - a];</code>. Versi bitset melakukan hal yang sama dengan menggeser seluruh bitset sejauh a lalu meng-OR-kannya.</p>
<p>Loop mundur tidak diperlukan: <code>dp &lt;&lt; a</code> dihitung <strong>lengkap</strong> dari dp lama sebelum di-OR, jadi satu koin tidak mungkin terpakai dua kali.</p>
<div class="wt-tip">Biayanya O(MAXS / 64) per koin. Untuk n = 2000 dan MAXS = 10<sup>5</sup>: sekitar 3 · 10<sup>6</sup> operasi kata, bukan 2 · 10<sup>8</sup>.</div>
TXT],
        ['Menghitung bit yang menyala', <<<'CPP'

    int bisa = 0;
    for (int s = 1; s <= S; s++) bisa += dp[s];
    cout << bisa << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Jika yang diminta seluruh bitset, <code>dp.count()</code> menghitung semua bit 1 memakai instruksi <em>popcount</em> perangkat keras. Di sini kita hanya menghitung rentang 1..S.</p>
TXT],
    ];

    $bsetPy = <<<'PY'
import sys
data = sys.stdin.read().split()
n, S = int(data[0]), int(data[1])
dp = 1                          # bilangan bulat Python sebagai bitset: bit ke-s = jumlah s
for a in map(int, data[2:2 + n]):
    dp |= dp << a               # geser + OR, persis seperti bitset C++
dp &= (1 << (S + 1)) - 1        # buang jumlah di atas S
print(bin(dp).count("1") - 1)   # kurangi bit untuk jumlah 0
PY;

    $bsetOps = <<<'CPP'
bitset<1000> b;              // semua 0
b[5] = 1;  b.set(7);  b.reset(5);  b.flip(3);
b.test(7);                   // true
b.count();                   // banyak bit 1, O(N/64)
b.any(); b.none();
bitset<1000> c = a & b;      // irisan
bitset<1000> d = a | b;      // gabungan
bitset<1000> e = a ^ b;      // beda simetris
a <<= 3;  a >>= 2;           // geser semua bit
// Khusus GCC: telusuri bit yang menyala tanpa memeriksa satu per satu
for (int i = b._Find_first(); i < 1000; i = b._Find_next(i)) { }
bitset<8> x(13);  x.to_string();   // "00001101"
CPP;

    $bsetTri = <<<'CPP'
// Menghitung segitiga di graph n <= 2000: O(m · n / 64)
bitset<2000> adj[2000];
// ... adj[u][v] = adj[v][u] = 1 untuk setiap sisi
long long seg = 0;
for (auto& e : sisi)                       // sisi (u, v)
    seg += (adj[e.first] & adj[e.second]).count();   // tetangga bersama
seg /= 3;                                  // setiap segitiga terhitung 3 kali (sekali per sisi)
CPP;

    $bsetConst = <<<'CPP'
// Optimasi konstan lain yang sering menentukan AC / TLE

// 1. Urutan loop ramah cache: baris luar, kolom dalam
for (int i = 0; i < n; i++)
    for (int j = 0; j < m; j++) s += a[i][j];   // cepat (memori berurutan)
for (int j = 0; j < m; j++)
    for (int i = 0; i < n; i++) s += a[i][j];   // lambat (melompat-lompat)

// 2. Jangan hitung ulang di dalam loop
for (int i = 0; i < (int)s.size(); i++)        // strlen(s) di sini akan O(n) per iterasi!

// 3. Modulo hanya saat perlu
x += y;  if (x >= MOD) x -= MOD;                // lebih cepat dari x = (x + y) % MOD
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai <code>std::bitset</code>: set, reset, test, count, AND/OR/XOR, dan geser.</li>
        <li>Menulis subset sum dalam satu baris <code>dp |= dp &lt;&lt; a</code> dan menjelaskan mengapa benar.</li>
        <li>Mempercepat DP boolean dan perhitungan himpunan sekitar 64 kali, misalnya menghitung segitiga di graph.</li>
        <li>Mengenali kapan bitset <em>tidak</em> menolong, dan optimasi konstan lain yang sering menyelamatkan dari TLE.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => '64 nilai dalam satu instruksi', 'desc' => 'Apa itu bitset dan dari mana kecepatannya datang.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Lembar Centang">
    <h2>Intuisi: Satu Lembar Penuh Kotak Centang</h2>
    <div class="prose">
        <p>Absensi 1000 siswa tidak perlu 1000 lembar kertas: cukup satu lembar berisi 1000 kotak centang. Untuk mencari siswa yang hadir di <em>dua</em> kegiatan, tumpuk dua lembar dan lihat sekaligus, bukan memeriksa satu per satu.</p>
        <p>Itulah <code>bitset</code>. Setiap nilai benar/salah hanya memakai 1 bit, dan prosesor 64-bit mengolah <strong>64 bit dalam satu instruksi</strong>. Operasi AND, OR, XOR, dan geser pada bitset berukuran N hanya butuh N/64 langkah.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Hanya boolean</b><span>Bisa/tidak, tercapai/tidak, tetangga/bukan. Untuk tabel berisi angka, bitset tidak menolong.</span></div>
        <div class="term"><b>Dibagi 64, bukan lebih cepat secara asimtotik</b><span>O(n²) tetap kuadratik; ia menjadi O(n² / 64). Menolong untuk n puluhan ribu, bukan jutaan.</span></div>
        <div class="term"><b>Ukuran tetap</b><span><code>bitset&lt;N&gt;</code>: N konstanta saat kompilasi. Pakai batas atas soal.</span></div>
    </div>
    @include('lessons.code', ['cpp' => $bsetOps, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Geser + OR">
    <h2>Visualisasi: Subset Sum dengan Geser dan OR</h2>
    <div class="prose">
        <p>Setiap barang x menggeser <strong>seluruh</strong> bitset sejauh x (artinya "ambil x" untuk semua jumlah yang sudah bisa), lalu hasilnya digabung dengan yang lama ("tidak ambil x"). Coba barang <code>2 3 5</code> dengan batas 7: jumlah yang melewati batas otomatis terbuang.</p>
    </div>
    <div data-viz="bitset-shift"></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Setelah</th><th>Bit menyala (s = 0 … 10)</th><th>Jumlah yang bisa</th></tr>
            <tr><td>awal</td><td><code>10000000000</code></td><td>{0}</td></tr>
            <tr class="hl"><td>x = 2</td><td><code>10100000000</code></td><td>{0, 2}</td></tr>
            <tr class="hl"><td>x = 3</td><td><code>10110100000</code></td><td>{0, 2, 3, 5}</td></tr>
            <tr class="ok"><td>x = 5</td><td><code>10110101101</code></td><td>{0, 2, 3, 5, 7, 8, 10}</td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'bitset di C++', 'desc' => 'Subset sum cepat, lalu menghitung segitiga di graph.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Bisa Dibayar Pas?</h2>
    @include('lessons.walkthrough', [
        'title' => 'Subset sum dengan bitset',
        'steps' => $bsetSteps,
        'sample' => ['input' => "3 10\n2 3 5\n", 'output' => "6\n"],
        'py' => $bsetPy,
    ])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p><strong>Python dan JavaScript</strong> tidak punya bitset, tetapi punya bilangan bulat tanpa batas (<code>int</code> di Python, <code>BigInt</code> di JavaScript). Bilangan itu bisa dipakai sebagai bitset: <code>dp |= dp &lt;&lt; a</code> juga bekerja di sana.</p>
    </div>
</section>

<section class="lesson-section" id="segitiga" data-toc="Menghitung Segitiga">
    <h2>Penerapan Kedua: Tetangga Bersama</h2>
    <div class="prose">
        <p>Banyak segitiga di graph = untuk setiap sisi (u, v), banyak simpul w yang bertetangga dengan u <em>dan</em> v, dibagi 3. Jika tetangga setiap simpul disimpan sebagai bitset, "tetangga bersama" hanyalah satu operasi AND diikuti <code>count()</code>.</p>
    </div>
    @include('lessons.code', ['cpp' => $bsetTri, 'js' => null, 'py' => null])
    <table class="cx-table">
        <tr><th>Soal</th><th>Tanpa bitset</th><th>Dengan bitset</th></tr>
        <tr><td>Subset sum, n koin, jumlah ≤ S</td><td><code>O(n · S)</code></td><td><code>O(n · S / 64)</code></td></tr>
        <tr><td>Segitiga, n simpul, m sisi</td><td><code>O(m · n)</code></td><td><code>O(m · n / 64)</code></td></tr>
        <tr><td>Keterjangkauan semua pasangan di DAG</td><td><code>O(n · m)</code></td><td><code>O(n · m / 64)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>bitset besar di dalam fungsi</strong> (misalnya <code>bitset&lt;2000&gt; adj[2000]</code>, 500 KB) bisa menghabiskan stack. Deklarasikan sebagai variabel global.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><code>vector&lt;bool&gt;</code> juga menyimpan 1 bit per nilai, tetapi tidak punya operasi kata sekaligus. Ia tidak memberi percepatan 64 kali.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'DP boolean dan optimasi konstan', 'desc' => 'Mengapa satu geseran benar, dan cara lain memeras kecepatan.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Geser + OR Benar?">
    <h2>Mengapa <code>dp |= dp &lt;&lt; a</code> Benar?</h2>
    <div class="proof">
        <p>Misalkan dp sebelum koin a mewakili himpunan jumlah yang bisa dibentuk dari koin-koin sebelumnya, sebut A. Setelah koin a, jumlah yang bisa dibentuk adalah A (tidak ambil a) gabung { s + a : s ∈ A } (ambil a).</p>
        <p>Geser kiri sejauh a memindahkan bit s ke posisi s + a, jadi <code>dp &lt;&lt; a</code> tepat mewakili { s + a }. Ekspresi kanan dihitung seluruhnya dari dp <em>lama</em> sebelum penugasan, sehingga setiap jumlah baru memakai a paling banyak sekali. OR menggabungkan kedua kemungkinan.</p>
    </div>
</section>

<section class="lesson-section" id="konstan" data-toc="Optimasi Konstan Lain">
    <h2>Optimasi Konstan Lain</h2>
    <div class="prose"><p>Kadang analisis kompleksitasmu benar tetapi solusinya mepet. Sebelum mencari algoritma lain, periksa hal-hal ini:</p></div>
    @include('lessons.code', ['cpp' => $bsetConst, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Subset sum</h4><p><code>dp |= dp &lt;&lt; a</code>.</p></div>
        <div class="pattern"><h4>Tetangga bersama</h4><p><code>(adj[u] &amp; adj[v]).count()</code>.</p></div>
        <div class="pattern"><h4>Keterjangkauan</h4><p><code>reach[u] |= reach[v]</code> dalam urutan topologis terbalik.</p></div>
        <div class="pattern"><h4>Membagi dua sama besar</h4><p>Subset sum dengan S = total/2.</p></div>
        <div class="pattern"><h4>Bit menyala berikutnya</h4><p><code>_Find_first</code> / <code>_Find_next</code>.</p></div>
        <div class="pattern"><h4>Pencocokan pola</h4><p>Algoritma shift-and untuk string pendek.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="dp lama {0, 3}; digeser 4 menjadi {4, 7}; digabung: {0, 3, 4, 7}.">
        <p class="quiz-q">dp berisi jumlah {0, 3}. Setelah <code>dp |= dp &lt;&lt; 4</code>, isinya?</p>
        <div class="quiz-options">
            <button class="quiz-option">{0, 3, 4}</button>
            <button class="quiz-option">{4, 7}</button>
            <button class="quiz-option">{0, 3, 4, 7}</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Bitset hanya mempercepat tabel boolean. Knapsack dengan nilai maksimum menyimpan angka di setiap sel.">
        <p class="quiz-q">DP mana yang <strong>tidak</strong> bisa dipercepat dengan bitset?</p>
        <div class="quiz-options">
            <button class="quiz-option">Knapsack dengan nilai maksimum</button>
            <button class="quiz-option">Apakah jumlah s bisa dibentuk?</button>
            <button class="quiz-option">Apakah simpul v bisa dicapai dari u?</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Operasi bitset bekerja per kata 64 bit, jadi N bit butuh sekitar N/64 instruksi.">
        <p class="quiz-q">Berapa kira-kira biaya <code>a &amp; b</code> untuk <code>bitset&lt;N&gt;</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(1)</button>
            <button class="quiz-option">O(N / 64)</button>
            <button class="quiz-option">O(N)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
