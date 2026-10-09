@php
    $kdpSteps = [
        ['Header & modulo', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
CPP, <<<'TXT'
<p>Banyaknya cara naik tangga tumbuh <strong>sangat cepat</strong> (untuk n = 100 sudah lebih dari 10<sup>26</sup>). Soal biasanya meminta jawaban <strong>modulo 1 000 000 007</strong>, yaitu sisa pembagiannya dengan bilangan prima itu.</p>
<div class="wt-tip">Ambil modulo <strong>setiap kali menjumlahkan</strong>, bukan hanya di akhir. Jika tidak, angkanya sudah meluap (overflow) sebelum sempat di-mod.</div>
TXT],
        ['Tempat menyimpan ingatan', <<<'CPP'

vector<long long> memo;
CPP, <<<'TXT'
<p><code>memo[i]</code> akan menyimpan jawaban untuk tangga ke-i <strong>setelah pertama kali dihitung</strong>. Nilai <code>-1</code> berarti "belum pernah dihitung".</p>
TXT],
        ['Fungsi rekursif: base case', <<<'CPP'

long long caraMemo(int i) {
    if (i < 0) return 0;
    if (i == 0) return 1;
CPP, <<<'TXT'
<p><code>caraMemo(i)</code> = banyaknya cara mencapai anak tangga ke-i. Dua <strong>base case</strong> menghentikan rekursi:</p>
<ul>
    <li><code>i &lt; 0</code>: kita melompat melewati lantai. Itu bukan cara yang sah, jadi 0.</li>
    <li><code>i == 0</code>: sudah tepat di bawah. Ada tepat <strong>1</strong> cara: tidak melangkah sama sekali.</li>
</ul>
TXT],
        ['Cek ingatan, lalu hitung', <<<'CPP'
    if (memo[i] != -1) return memo[i];
    return memo[i] = (caraMemo(i - 1) + caraMemo(i - 2) + caraMemo(i - 3)) % MOD;
}
CPP, <<<'TXT'
<p>Inilah <strong>memoization</strong>, dua baris yang mengubah algoritma lambat menjadi cepat:</p>
<ol>
    <li>Jika <code>memo[i]</code> sudah terisi, langsung kembalikan. Tidak perlu menghitung ulang.</li>
    <li>Jika belum, hitung dengan <strong>transisi</strong>: langkah terakhir untuk sampai di i pasti 1, 2, atau 3 anak tangga. Jadi jawabannya penjumlahan tiga subsoal. Hasilnya disimpan ke <code>memo[i]</code> sekaligus dikembalikan.</li>
</ol>
<div class="wt-tip"><code>return memo[i] = …;</code> menyimpan lalu mengembalikan nilai yang sama dalam satu baris.</div>
TXT],
        ['Menjalankan versi memoization', <<<'CPP'

int main() {
    int n;
    cin >> n;

    memo.assign(n + 1, -1);
    cout << "Memoization: " << caraMemo(n) << '\n';
CPP, <<<'TXT'
<p>Siapkan <code>memo</code> berukuran n + 1 berisi -1, lalu panggil <code>caraMemo(n)</code>. Rekursi berjalan <strong>dari atas ke bawah</strong> (top-down): mulai dari pertanyaan besar, lalu turun ke subsoal yang dibutuhkan.</p>
<p>Setiap <code>caraMemo(i)</code> hanya benar-benar dihitung sekali, jadi totalnya <strong>O(n)</strong>.</p>
TXT],
        ['Versi tabulasi: base case', <<<'CPP'

    vector<long long> dp(n + 1, 0);
    dp[0] = 1;
CPP, <<<'TXT'
<p>Cara kedua, <strong>tabulasi</strong> (bottom-up), tanpa rekursi. <code>dp[i]</code> artinya sama dengan <code>caraMemo(i)</code>. Base case-nya sama: <code>dp[0] = 1</code>.</p>
TXT],
        ['Isi tabel dari kecil ke besar', <<<'CPP'
    for (int i = 1; i <= n; i++) {
        dp[i] = dp[i - 1];
        if (i >= 2) dp[i] = (dp[i] + dp[i - 2]) % MOD;
        if (i >= 3) dp[i] = (dp[i] + dp[i - 3]) % MOD;
    }
CPP, <<<'TXT'
<p>Isi <code>dp[1], dp[2], …, dp[n]</code> berurutan. Saat menghitung <code>dp[i]</code>, nilai <code>dp[i−1]</code>, <code>dp[i−2]</code>, dan <code>dp[i−3]</code> <strong>pasti sudah siap</strong> karena indeksnya lebih kecil.</p>
<p>Pemeriksaan <code>i &gt;= 2</code> dan <code>i &gt;= 3</code> menggantikan base case <code>i &lt; 0</code> pada versi rekursif.</p>
<pre>i  :  0  1  2  3  4  5
dp :  1  1  2  4  7  13</pre>
TXT],
        ['Mencetak hasil tabulasi', <<<'CPP'
    cout << "Tabulasi: " << dp[n] << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Kedua cara memberi jawaban yang sama. Untuk n = 5: <strong>13</strong> cara.</p>
<p>Tabulasi biasanya sedikit lebih cepat (tanpa biaya pemanggilan fungsi) dan aman dari rekursi terlalu dalam. Memoization lebih mudah ditulis jika urutan pengisiannya rumit.</p>
TXT],
    ];

    $kdpJs = <<<'JS'
const [n] = readInts();
const MOD = 1000000007;

// Memoization (top-down)
const memo = new Array(n + 1).fill(-1);
function caraMemo(i) {
  if (i < 0) return 0;
  if (i === 0) return 1;
  if (memo[i] !== -1) return memo[i];
  return (memo[i] = (caraMemo(i - 1) + caraMemo(i - 2) + caraMemo(i - 3)) % MOD);
}
console.log("Memoization: " + caraMemo(n));

// Tabulasi (bottom-up)
const dp = new Array(n + 1).fill(0);
dp[0] = 1;
for (let i = 1; i <= n; i++) {
  dp[i] = dp[i - 1];
  if (i >= 2) dp[i] = (dp[i] + dp[i - 2]) % MOD;
  if (i >= 3) dp[i] = (dp[i] + dp[i - 3]) % MOD;
}
console.log("Tabulasi: " + dp[n]);
JS;

    $kdpPy = <<<'PY'
import sys
from functools import lru_cache
sys.setrecursionlimit(10000)

MOD = 1000000007
n = int(input())

@lru_cache(maxsize=None)   # lru_cache = memoization otomatis
def cara_memo(i):
    if i < 0:
        return 0
    if i == 0:
        return 1
    return (cara_memo(i - 1) + cara_memo(i - 2) + cara_memo(i - 3)) % MOD

print("Memoization:", cara_memo(n))

dp = [0] * (n + 1)
dp[0] = 1
for i in range(1, n + 1):
    dp[i] = dp[i - 1]
    if i >= 2:
        dp[i] = (dp[i] + dp[i - 2]) % MOD
    if i >= 3:
        dp[i] = (dp[i] + dp[i - 3]) % MOD
print("Tabulasi:", dp[n])
PY;

    $kdpRolling = <<<'CPP'
// Hemat memori: dp[i] hanya butuh 3 nilai terakhir
long long a = 0, b = 0, c = 1;       // dp[i-3], dp[i-2], dp[i-1] saat i = 1
for (int i = 1; i <= n; i++) {
    long long baru = (a + b + c) % MOD;
    a = b;
    b = c;
    c = baru;
}
// c = dp[n]
CPP;

    $kdpMatrix = <<<'CPP'
// Fibonacci dalam O(log n): [F(n+1) F(n); F(n) F(n-1)] = [[1,1],[1,0]]^n
typedef array<array<long long, 2>, 2> Mat;

Mat kali(const Mat &A, const Mat &B) {
    Mat C = {};
    for (int i = 0; i < 2; i++)
        for (int k = 0; k < 2; k++)
            for (int j = 0; j < 2; j++)
                C[i][j] = (C[i][j] + A[i][k] * B[k][j]) % MOD;
    return C;
}

long long fib(long long n) {
    Mat R = {{{1, 0}, {0, 1}}}, M = {{{1, 1}, {1, 0}}};
    while (n > 0) {                   // pangkat cepat: kuadratkan & kalikan
        if (n & 1) R = kali(R, M);
        M = kali(M, M);
        n >>= 1;
    }
    return R[0][1];                   // F(n)
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan mengapa rekursi biasa bisa sangat lambat: <strong>subsoal yang sama dihitung berulang kali</strong>.</li>
        <li>Mengubah rekursi menjadi cepat dengan <strong>memoization</strong> (top-down) atau <strong>tabulasi</strong> (bottom-up).</li>
        <li>Merancang DP dengan <strong>resep 4 langkah</strong>: state, transisi, base case, urutan &amp; jawaban.</li>
        <li>Menghemat memori dengan variabel bergulir, dan mengenal Fibonacci O(log n).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'apa itu Dynamic Programming?', 'desc' => 'Dari rekursi yang boros ke "ingat jawaban yang sudah pernah dihitung".'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Jangan Hitung Ulang">
    <h2>Intuisi: Jangan Menghitung Ulang</h2>
    <div class="prose">
        <p>Tulis <code>1 + 1 + 1 + 1 + 1 + 1 + 1 + 1</code> di papan. "Berapa hasilnya?" Kamu menghitung: <strong>8</strong>. Lalu tambahkan <code>+ 1</code> di ujungnya. "Sekarang berapa?" Kamu langsung menjawab <strong>9</strong> tanpa menghitung ulang, karena kamu <strong>ingat</strong> hasil sebelumnya adalah 8.</p>
        <p>Itulah inti <strong>Dynamic Programming</strong> (DP): pecah masalah besar menjadi subsoal yang lebih kecil, selesaikan setiap subsoal <strong>sekali saja</strong>, lalu simpan jawabannya agar bisa dipakai ulang.</p>
        <p>Contoh klasik: Fibonacci, <code>F(n) = F(n−1) + F(n−2)</code> dengan F(0) = 0 dan F(1) = 1. Rekursi biasa memanggil subsoal yang sama berkali-kali:</p>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 300,
        'nodes' => [
            [1, 320, 30, 'vis', 'F5'], [2, 180, 95, 'vis', 'F4'], [3, 470, 95, 'red', 'F3'],
            [4, 100, 160, 'vis', 'F3'], [5, 260, 160, 'red', 'F2'], [6, 410, 160, 'red', 'F2'], [7, 530, 160, 'green', 'F1'],
            [8, 55, 220, 'vis', 'F2'], [9, 145, 220, 'green', 'F1'], [10, 225, 220, 'green', 'F1'], [11, 295, 220, 'green', 'F0'], [12, 380, 220, 'green', 'F1'], [13, 440, 220, 'green', 'F0'],
            [14, 30, 280, 'green', 'F1'], [15, 80, 280, 'green', 'F0'],
        ],
        'edges' => [[1, 2], [1, 3], [2, 4], [2, 5], [3, 6], [3, 7], [4, 8], [4, 9], [5, 10], [5, 11], [6, 12], [6, 13], [8, 14], [8, 15]],
        'caption' => 'Pohon pemanggilan F(5): 15 panggilan. Simpul <span style="color:var(--red)">merah</span> adalah perhitungan ulang subsoal yang sebenarnya sudah pernah dihitung. Untuk F(40), rekursi biasa melakukan lebih dari 300 juta panggilan!',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>DP cocok jika soal punya dua sifat: <strong>subsoal yang tumpang tindih</strong> (subsoal yang sama muncul berkali-kali) dan <strong>struktur optimal</strong> (jawaban besar bisa disusun dari jawaban subsoal).</p>
    </div>
</section>

<section class="lesson-section" id="dua-cara" data-toc="Memoization vs Tabulasi">
    <h2>Dua Cara: Memoization & Tabulasi</h2>
    <div class="diagram-grid" style="max-width: 860px; margin-bottom: 14px">
        <div>
            <h5>Memoization (top-down)</h5>
            <div class="prose" style="font-size: 14px">
                <p>Tetap menulis rekursi, tetapi sebelum menghitung, <strong>cek catatan</strong> dulu. Jika sudah ada, pakai. Jika belum, hitung lalu catat.</p>
                <p>Mulai dari pertanyaan besar, turun ke subsoal yang benar-benar dibutuhkan.</p>
            </div>
        </div>
        <div>
            <h5>Tabulasi (bottom-up)</h5>
            <div class="prose" style="font-size: 14px">
                <p>Tanpa rekursi. Siapkan <strong>tabel</strong>, isi dari subsoal terkecil (base case) naik ke yang besar, sesuai urutan yang menjamin bahan selalu siap.</p>
                <div class="dp-mini" style="grid-template-columns: repeat(7, auto)">
                    <span class="h">i</span><span class="h">0</span><span class="h">1</span><span class="h">2</span><span class="h">3</span><span class="h">4</span><span class="h">5</span>
                    <span class="h">F</span><span class="ok">0</span><span class="ok">1</span><span>1</span><span>2</span><span class="dep">3</span><span class="cur">5</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2>Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Bandingkan ketiga mode. Pada <strong>Rekursi biasa</strong>, perhatikan simpul merah yang dihitung ulang. Pada <strong>Memoization</strong>, simpul biru diambil langsung dari catatan. Pada <strong>Tabulasi</strong>, tabel diisi dari kiri ke kanan. Grafik di panel samping menunjukkan betapa cepat jumlah panggilan rekursi biasa membengkak.</p>
    </div>
    <div data-viz="fib"></div>
</section>

<section class="lesson-section" id="resep" data-toc="Resep 4 Langkah DP">
    <h2>Resep 4 Langkah Merancang DP</h2>
    <div class="steps">
        <div class="step-card"><b>1. Definisikan state</b><span>Tulis dalam kalimat: "<code>dp[i]</code> = banyaknya cara / nilai terbaik untuk …". Ini langkah terpenting. State yang jelas membuat sisanya mudah.</span></div>
        <div class="step-card"><b>2. Cari transisi</b><span>Pikirkan <strong>langkah terakhir</strong>: dari subsoal mana saja kita bisa tiba di state ini? Contoh tangga: langkah terakhir 1, 2, atau 3 anak tangga, jadi <code>dp[i] = dp[i−1] + dp[i−2] + dp[i−3]</code>.</span></div>
        <div class="step-card"><b>3. Tentukan base case</b><span>Subsoal terkecil yang jawabannya jelas tanpa rumus: <code>dp[0] = 1</code>.</span></div>
        <div class="step-card"><b>4. Urutan & jawaban</b><span>Isi tabel sehingga bahan selalu siap lebih dulu (di sini i naik). Jawaban akhir: <code>dp[n]</code>.</span></div>
    </div>
    <div class="callout tip">
        <span class="callout-icon">🪜</span>
        <p><strong>Contoh soal:</strong> ada n anak tangga. Sekali melangkah kamu boleh naik 1, 2, atau 3 anak tangga. Ada berapa cara berbeda untuk sampai di puncak? Untuk n = 4: 1+1+1+1, 1+1+2, 1+2+1, 2+1+1, 2+2, 1+3, 3+1, yaitu <strong>7 cara</strong>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis DP di C++', 'desc' => 'Satu program, dua cara: memoization dan tabulasi, dibahas baris demi baris.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Naik Tangga 1, 2, atau 3</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> hitung banyaknya cara naik n anak tangga (langkah 1, 2, atau 3) modulo 10<sup>9</sup> + 7. Program menghitungnya dua kali, dengan memoization dan tabulasi, untuk menunjukkan bahwa hasilnya sama.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Memoization & tabulasi',
        'steps' => $kdpSteps,
        'sample' => ['input' => "5\n", 'output' => "Memoization: 13\nTabulasi: 13\n"],
        'js' => $kdpJs,
        'py' => $kdpPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Jenis Soal DP">
    <h2>Tiga Jenis Pertanyaan DP</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Menghitung cara</h4><p>"Ada berapa cara…". Transisi <b>menjumlahkan</b>: <code>dp[i] = Σ dp[sebelumnya]</code>. Biasanya dengan modulo.</p></div>
        <div class="pattern"><h4>Minimum / maksimum</h4><p>"Biaya termurah", "nilai terbesar". Transisi memakai <b>min/max</b>: <code>dp[i] = min(dp[j] + biaya)</code>.</p></div>
        <div class="pattern"><h4>Mungkin / tidak</h4><p>"Bisakah membentuk X?". DP bernilai <b>true/false</b>: <code>dp[i] = dp[i−a] || dp[i−b]</code>.</p></div>
    </div>
    <table class="cx-table">
        <tr><th></th><th>Memoization</th><th>Tabulasi</th></tr>
        <tr><td>Cara menulis</td><td>Rekursi + catatan</td><td>Loop mengisi tabel</td></tr>
        <tr><td>Subsoal yang dihitung</td><td>Hanya yang dibutuhkan</td><td>Semua</td></tr>
        <tr><td>Risiko</td><td>Rekursi terlalu dalam</td><td>Harus tahu urutan pengisian</td></tr>
        <tr><td>Kompleksitas</td><td colspan="2"><code>banyak state × biaya transisi</code></td></tr>
    </table>
</section>

<section class="lesson-section" id="jebakan" data-toc="Jebakan Umum">
    <h2>Jebakan Umum</h2>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Modulo terlambat.</strong> <code>(a + b + c) % MOD</code> dengan a, b, c &lt; MOD aman di <code>long long</code>. Tetapi menjumlahkan banyak nilai tanpa modulo di tengah jalan akan meluap.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Penanda memo yang salah.</strong> Jika jawaban yang sah bisa bernilai 0, jangan pakai 0 sebagai tanda "belum dihitung". Gunakan -1 atau array <code>bool</code> terpisah.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Base case terlupa.</strong> Rekursi tanpa base case tidak pernah berhenti (stack overflow), dan tabel tanpa base case berisi 0 semua.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'DP lebih dalam', 'desc' => 'Mengapa DP benar, menghemat memori, dan Fibonacci dalam O(log n).'])

<section class="lesson-section" id="bukti" data-toc="Mengapa DP Benar?">
    <h2>Mengapa DP Benar dan Cepat?</h2>
    <div class="proof">
        <p><strong>Benar</strong> karena transisinya <strong>membagi semua kemungkinan</strong> tanpa ada yang terlewat atau terhitung dua kali. Setiap cara naik ke anak tangga i berakhir dengan tepat satu dari tiga langkah terakhir (1, 2, atau 3). Jadi himpunan semua cara terbagi menjadi tiga kelompok yang tidak beririsan, dan ukuran setiap kelompok adalah <code>dp[i−1]</code>, <code>dp[i−2]</code>, dan <code>dp[i−3]</code>.</p>
        <p><strong>Cepat</strong> karena setiap state dihitung sekali. Ada n + 1 state dan setiap transisi butuh 3 operasi, jadi O(n). Rekursi biasa tanpa catatan butuh waktu sebanding dengan banyaknya <em>cara</em> itu sendiri, yang tumbuh eksponensial.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2>Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Variabel bergulir (hemat memori)</h3>
        <p>Jika <code>dp[i]</code> hanya bergantung pada beberapa nilai terakhir, kita tidak perlu menyimpan seluruh tabel. Cukup beberapa variabel yang terus "digeser". Memori turun dari O(n) menjadi O(1).</p>
    </div>
    @include('lessons.code', ['cpp' => $kdpRolling, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. Dari brute force ke DP</h3>
        <p>Cara paling aman menemukan DP: tulis dulu <strong>rekursi brute force</strong> yang benar (walaupun lambat), lalu tanyakan "parameter apa saja yang menentukan hasil fungsi ini?". Parameter itulah state DP-mu. Tambahkan memo, dan selesai.</p>
        <h3>3. Fibonacci dalam O(log n)</h3>
        <p>Jika n sampai 10<sup>18</sup>, bahkan O(n) terlalu lambat. Transisi linear seperti Fibonacci bisa ditulis sebagai perkalian matriks, lalu dipangkatkan dengan <strong>pangkat cepat</strong> (kuadratkan berulang) dalam O(log n).</p>
    </div>
    @include('lessons.code', ['cpp' => $kdpMatrix, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Rekursi Fibonacci biasa memanggil subsoal yang sama berulang kali sehingga jumlah panggilannya tumbuh eksponensial. Memoization membuat setiap F(i) dihitung sekali saja.">
        <p class="quiz-q">Mengapa F(40) dengan rekursi biasa sangat lambat?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena angkanya terlalu besar untuk int</button>
            <button class="quiz-option">Karena subsoal yang sama dihitung berulang kali</button>
            <button class="quiz-option">Karena rekursi selalu lebih lambat dari loop</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Langkah terakhir menuju anak tangga i bisa 1 atau 3, jadi dp[i] = dp[i−1] + dp[i−3].">
        <p class="quiz-q">Jika langkah yang boleh hanya 1 atau 3 anak tangga, transisinya…</p>
        <div class="quiz-options">
            <button class="quiz-option"><code>dp[i] = dp[i−1] + dp[i−2] + dp[i−3]</code></button>
            <button class="quiz-option"><code>dp[i] = dp[i−1] × dp[i−3]</code></button>
            <button class="quiz-option"><code>dp[i] = dp[i−1] + dp[i−3]</code></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Ada N state dan setiap transisi mencoba K pilihan, jadi totalnya O(N · K).">
        <p class="quiz-q">DP dengan N state, dan setiap state mencoba K pilihan transisi. Kompleksitasnya?</p>
        <div class="quiz-options">
            <button class="quiz-option"><code>O(N · K)</code></button>
            <button class="quiz-option"><code>O(N + K)</code></button>
            <button class="quiz-option"><code>O(K<sup>N</sup>)</code></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
