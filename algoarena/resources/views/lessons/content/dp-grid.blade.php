@php
    $dgSteps = [
        ['Header', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;
CPP, <<<'TXT'
<p>Header standar.</p>
TXT],
        ['Membaca grid angka', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<vector<long long>> a(R, vector<long long>(C));
    for (int i = 0; i < R; i++)
        for (int j = 0; j < C; j++) cin >> a[i][j];
CPP, <<<'TXT'
<p>Grid berukuran R × C, setiap petak berisi biaya <code>a[i][j]</code>. Baris dan kolom diberi nomor mulai 0, jadi petak awal (0, 0) dan petak tujuan (R−1, C−1).</p>
<pre>1 3 1 2
1 5 1 3
4 2 1 1</pre>
TXT],
        ['Tabel DP', <<<'CPP'

    vector<vector<long long>> dp(R, vector<long long>(C, 0));
CPP, <<<'TXT'
<p><strong>State:</strong> <code>dp[i][j]</code> = total biaya <strong>terkecil</strong> untuk berjalan dari (0, 0) sampai (i, j), termasuk biaya kedua petak itu.</p>
<p>Tabelnya berukuran sama dengan grid: satu jawaban untuk setiap petak.</p>
TXT],
        ['Mengisi tabel: empat kasus', <<<'CPP'

    for (int i = 0; i < R; i++) {
        for (int j = 0; j < C; j++) {
            if (i == 0 && j == 0) dp[i][j] = a[i][j];
            else if (i == 0) dp[i][j] = dp[i][j - 1] + a[i][j];
            else if (j == 0) dp[i][j] = dp[i - 1][j] + a[i][j];
            else dp[i][j] = min(dp[i - 1][j], dp[i][j - 1]) + a[i][j];
        }
    }
CPP, <<<'TXT'
<p>Robot hanya bergerak ke <strong>kanan</strong> atau ke <strong>bawah</strong>. Jadi petak (i, j) hanya bisa didatangi dari <strong>atas</strong> (i−1, j) atau dari <strong>kiri</strong> (i, j−1):</p>
<ul>
    <li><b>Petak awal:</b> biayanya sendiri (base case).</li>
    <li><b>Baris 0:</b> hanya bisa datang dari kiri.</li>
    <li><b>Kolom 0:</b> hanya bisa datang dari atas.</li>
    <li><b>Lainnya:</b> pilih asal yang lebih murah, lalu tambah biaya petak ini.</li>
</ul>
<p>Isi baris demi baris, kiri ke kanan. Urutan ini menjamin sel atas dan kiri selalu sudah terisi.</p>
<pre>1   4   5   7
2   7   6   9
6   8   7   8</pre>
TXT],
        ['Menelusuri jalur terbaik mundur', <<<'CPP'

    string jalan;
    int i = R - 1, j = C - 1;
    while (i > 0 || j > 0) {
        if (j == 0 || (i > 0 && dp[i - 1][j] <= dp[i][j - 1])) {
            jalan += 'D';
            i--;
        } else {
            jalan += 'R';
            j--;
        }
    }
    reverse(jalan.begin(), jalan.end());
CPP, <<<'TXT'
<p>Untuk tahu <strong>rute</strong>-nya, mulai dari petak tujuan dan tanyakan: "tadi saya datang dari atas atau dari kiri?" Jawabannya: dari tetangga yang nilai dp-nya lebih kecil.</p>
<ul>
    <li>Datang dari atas berarti langkah terakhirnya <b>D</b> (down), lalu naik ke (i−1, j).</li>
    <li>Datang dari kiri berarti langkah terakhirnya <b>R</b> (right), lalu mundur ke (i, j−1).</li>
</ul>
<p>Langkah dikumpulkan dari belakang, jadi string-nya dibalik di akhir.</p>
<pre>(2,3) ← kiri  (2,2) ← atas  (1,2) ← atas  (0,2) ← kiri  (0,1) ← kiri  (0,0)
terkumpul "RDDRR" → dibalik → "RRDDR"</pre>
TXT],
        ['Mencetak jawaban', <<<'CPP'

    cout << dp[R - 1][C - 1] << '\n' << jalan << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Cetak biaya minimum (8) dan rutenya (RRDDR): 1 → 3 → 1 → 1 → 1 → 1.</p>
TXT],
    ];

    $dgJs = <<<'JS'
const [R, C] = readInts();
const a = [];
for (let i = 0; i < R; i++) a.push(readInts());

const dp = Array.from({ length: R }, () => new Array(C).fill(0));
for (let i = 0; i < R; i++) {
  for (let j = 0; j < C; j++) {
    if (i === 0 && j === 0) dp[i][j] = a[i][j];
    else if (i === 0) dp[i][j] = dp[i][j - 1] + a[i][j];
    else if (j === 0) dp[i][j] = dp[i - 1][j] + a[i][j];
    else dp[i][j] = Math.min(dp[i - 1][j], dp[i][j - 1]) + a[i][j];
  }
}

const jalan = [];
let i = R - 1;
let j = C - 1;
while (i > 0 || j > 0) {
  if (j === 0 || (i > 0 && dp[i - 1][j] <= dp[i][j - 1])) {
    jalan.push("D");
    i--;
  } else {
    jalan.push("R");
    j--;
  }
}
console.log(dp[R - 1][C - 1] + "\n" + jalan.reverse().join(""));
JS;

    $dgPy = <<<'PY'
R, C = map(int, input().split())
a = [list(map(int, input().split())) for _ in range(R)]

dp = [[0] * C for _ in range(R)]
for i in range(R):
    for j in range(C):
        if i == 0 and j == 0:
            dp[i][j] = a[i][j]
        elif i == 0:
            dp[i][j] = dp[i][j - 1] + a[i][j]
        elif j == 0:
            dp[i][j] = dp[i - 1][j] + a[i][j]
        else:
            dp[i][j] = min(dp[i - 1][j], dp[i][j - 1]) + a[i][j]

jalan = []
i, j = R - 1, C - 1
while i > 0 or j > 0:
    if j == 0 or (i > 0 and dp[i - 1][j] <= dp[i][j - 1]):
        jalan.append("D")
        i -= 1
    else:
        jalan.append("R")
        j -= 1
print(dp[R - 1][C - 1])
print("".join(reversed(jalan)))
PY;

    $dgSquare = <<<'CPP'
// Persegi terbesar berisi '1': dp[i][j] = sisi persegi terbesar yang pojok kanan-bawahnya (i, j)
int terbesar = 0;
for (int i = 0; i < R; i++) {
    for (int j = 0; j < C; j++) {
        if (g[i][j] == '0') { dp[i][j] = 0; continue; }
        if (i == 0 || j == 0) dp[i][j] = 1;
        else dp[i][j] = min({dp[i - 1][j], dp[i][j - 1], dp[i - 1][j - 1]}) + 1;
        terbesar = max(terbesar, dp[i][j]);
    }
}
CPP;

    $dgRow = <<<'CPP'
// Hemat memori: cukup satu baris, karena dp[i][j] hanya butuh baris i-1 dan baris i
vector<long long> baris(C, 0);
for (int i = 0; i < R; i++) {
    for (int j = 0; j < C; j++) {
        if (i == 0 && j == 0) baris[j] = a[0][0];
        else if (i == 0) baris[j] = baris[j - 1] + a[i][j];
        else if (j == 0) baris[j] = baris[j] + a[i][j];         // baris[j] lama = dari atas
        else baris[j] = min(baris[j], baris[j - 1]) + a[i][j];  // atas vs kiri
    }
}
CPP;

    $dgPaths = <<<'CPP'
// Banyak jalur dengan rintangan '#', modulo MOD
dp[0][0] = (g[0][0] == '.');
for (int i = 0; i < R; i++)
    for (int j = 0; j < C; j++) {
        if (g[i][j] == '#' || (i == 0 && j == 0)) continue;
        long long atas = i > 0 ? dp[i - 1][j] : 0;
        long long kiri = j > 0 ? dp[i][j - 1] : 0;
        dp[i][j] = (atas + kiri) % MOD;
    }
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Merancang DP <strong>dua dimensi</strong> pada grid: state <code>dp[i][j]</code>, asal dari atas dan kiri.</li>
        <li>Menghitung <strong>banyak jalur</strong> (dengan rintangan) dan jalur <strong>termurah/termahal</strong>.</li>
        <li>Menulis DP grid di C++ lengkap dengan <strong>penelusuran rute</strong>.</li>
        <li>Menyelesaikan "persegi terbesar" dan menghemat memori menjadi satu baris.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'DP di atas petak-petak', 'desc' => 'Robot yang hanya boleh ke kanan dan ke bawah, dan pola Pascal yang muncul.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Robot di Grid">
    <h2><span class="sec-icon">💡</span> Intuisi: Robot di Grid</h2>
    <div class="prose">
        <p>Sebuah robot berdiri di pojok kiri atas sebuah grid dan ingin ke pojok kanan bawah. Ia hanya boleh bergerak <strong>ke kanan</strong> atau <strong>ke bawah</strong>. Ada berapa jalur berbeda?</p>
        <p>Perhatikan satu petak mana pun. Robot hanya bisa masuk dari <strong>atas</strong> atau dari <strong>kiri</strong>. Jadi banyak jalur menuju petak itu = jalur menuju petak di atasnya + jalur menuju petak di kirinya.</p>
    </div>
    <div class="diagram-grid" style="max-width: 860px; margin-bottom: 14px">
        <div>
            <h5>Banyak jalur (tanpa rintangan)</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(5, auto)">
                <span class="ok">1</span><span>1</span><span>1</span><span>1</span><span>1</span>
                <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                <span>1</span><span>3</span><span>6</span><span class="dep">10</span><span>15</span>
                <span>1</span><span>4</span><span class="dep2">10</span><span class="cur">20</span><span>35</span>
            </div>
            <p class="muted" style="font-size: 12.5px; margin: 6px 4px 0">20 = 10 (dari atas) + 10 (dari kiri). Polanya sama dengan segitiga Pascal!</p>
        </div>
        <div>
            <h5>Dengan rintangan</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(5, auto)">
                <span class="ok">1</span><span>1</span><span>1</span><span>1</span><span>1</span>
                <span>1</span><span class="bad">✕</span><span>1</span><span>2</span><span>3</span>
                <span>1</span><span>1</span><span>2</span><span class="bad">✕</span><span>3</span>
                <span>1</span><span>2</span><span>4</span><span>4</span><span class="cur">7</span>
            </div>
            <p class="muted" style="font-size: 12.5px; margin: 6px 4px 0">Petak rintangan selalu 0 jalur, sehingga "memutus" aliran angka.</p>
        </div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><code>dp[i][j] = dp[i−1][j] + dp[i][j−1]</code> untuk menghitung jalur, dan <code>dp[i][j] = min(dp[i−1][j], dp[i][j−1]) + a[i][j]</code> untuk mencari jalur termurah.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2><span class="sec-icon">✍️</span> Coba dengan Tangan: Jalur Termurah</h2>
    <div class="prose"><p>Grid biaya di kiri. Isi tabel dp baris demi baris (kanan). Setiap sel = biaya petak + asal termurah (atas atau kiri).</p></div>
    <div class="diagram-grid" style="max-width: 860px; margin-bottom: 14px">
        <div>
            <h5>Biaya petak a[i][j]</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(4, auto)">
                <span class="ok">1</span><span class="ok">3</span><span class="ok">1</span><span>2</span>
                <span>1</span><span>5</span><span class="ok">1</span><span>3</span>
                <span>4</span><span>2</span><span class="ok">1</span><span class="ok">1</span>
            </div>
        </div>
        <div>
            <h5>dp[i][j] = biaya termurah sampai (i, j)</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(4, auto)">
                <span class="ok">1</span><span class="ok">4</span><span class="ok">5</span><span>7</span>
                <span>2</span><span>7</span><span class="ok">6</span><span>9</span>
                <span>6</span><span>8</span><span class="ok">7</span><span class="ok">8</span>
            </div>
        </div>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Sel (1, 2) bernilai 6: dari atas 5 + 1 = 6, dari kiri 7 + 1 = 8. Pilih yang dari atas. Jalur hijau (kanan, kanan, bawah, bawah, kanan) total biayanya <strong>8</strong>.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2><span class="sec-icon">🎬</span> Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Klik sel untuk menambah atau menghapus rintangan, lalu putar animasinya. Panah biru dari atas dan pink dari kiri menunjukkan sumber penjumlahan. Aktifkan <strong>🌡 Heatmap</strong> untuk melihat bagaimana angka membesar ke arah kanan bawah.</p>
    </div>
    <div data-viz="grid"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>State</b><span><code>dp[i][j]</code> = jawaban untuk petak (i, j): banyak jalur, biaya terkecil, atau nilai terbesar sampai di sana.</span></div>
        <div class="step-card"><b>Dari mana saja bisa datang?</b><span>Biasanya atas dan kiri. Bisa juga diagonal, tergantung aturan gerak di soal.</span></div>
        <div class="step-card"><b>Base case & tepi</b><span>Petak awal, baris pertama, kolom pertama, dan petak rintangan butuh perlakuan khusus.</span></div>
        <div class="step-card"><b>Urutan pengisian</b><span>Baris demi baris, kiri ke kanan, agar sumbernya selalu siap.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis DP grid di C++', 'desc' => 'Jalur termurah lengkap dengan penelusuran rute, lalu pola soal grid.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2><span class="sec-icon">💻</span> Kode C++ Lengkap: Jalur Termurah</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> grid R × C berisi biaya. Robot berjalan dari kiri atas ke kanan bawah, hanya ke kanan (R) atau ke bawah (D). Cetak total biaya minimum dan rutenya.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'DP grid: jalur termurah + rute',
        'steps' => $dgSteps,
        'sample' => ['input' => "3 4\n1 3 1 2\n1 5 1 3\n4 2 1 1\n", 'output' => "8\nRRDDR\n"],
        'js' => $dgJs,
        'py' => $dgPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal Grid">
    <h2><span class="sec-icon">🧠</span> Pola Soal DP Grid</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>🧮 Banyak jalur</h4><p>Jumlahkan atas + kiri, rintangan = 0, ambil modulo.</p></div>
        <div class="pattern"><h4>💰 Koin terbanyak</h4><p>Ganti <code>min</code> menjadi <code>max</code>.</p></div>
        <div class="pattern"><h4>⬛ Persegi terbesar</h4><p><code>min(atas, kiri, diagonal) + 1</code> untuk petak bernilai 1.</p></div>
        <div class="pattern"><h4>↘️ Tiga arah</h4><p>Boleh turun lurus atau serong: sumbernya (i−1, j−1), (i−1, j), (i−1, j+1).</p></div>
        <div class="pattern"><h4>🧭 Rute</h4><p>Telusuri mundur dari tujuan ke sumber yang memberi nilai terbaik.</p></div>
        <div class="pattern"><h4>🔁 Grid tersembunyi</h4><p>Dua string (LCS), barang × kapasitas (knapsack) juga berbentuk tabel 2D.</p></div>
    </div>
    <div class="prose"><p><strong>Template banyak jalur dengan rintangan:</strong></p></div>
    @include('lessons.code', ['cpp' => $dgPaths, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Aspek</th><th>Nilai</th><th>Keterangan</th></tr>
        <tr><td>Waktu</td><td><code>O(R · C)</code></td><td>Setiap petak dihitung sekali dengan O(1) operasi.</td></tr>
        <tr><td>Memori</td><td><code>O(R · C)</code></td><td>Bisa menjadi O(C) jika rute tidak perlu disusun ulang.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa tepi.</strong> Mengakses <code>dp[i−1][j]</code> saat i = 0 berarti indeks -1: perilaku tak terduga atau crash. Tangani baris dan kolom pertama secara khusus.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Petak awal terhalang.</strong> Jika (0, 0) atau tujuan adalah rintangan, banyak jalurnya 0. Jangan lupa kasus ini.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'DP grid tingkat lanjut', 'desc' => 'Persegi terbesar, menghemat memori satu baris, dan bukti transisinya.'])

<section class="lesson-section" id="persegi" data-toc="Persegi Terbesar">
    <h2><span class="sec-icon">⬛</span> Persegi Terbesar</h2>
    <div class="prose">
        <p>Soal: grid berisi 0 dan 1. Cari persegi terbesar yang <strong>seluruhnya</strong> berisi 1. Brute force mencoba setiap pojok dan setiap ukuran: O(N⁴) atau lebih.</p>
        <p>DP: <code>dp[i][j]</code> = sisi persegi 1 terbesar yang pojok <strong>kanan-bawah</strong>-nya di (i, j). Persegi berukuran k di (i, j) hanya mungkin jika petak atas, kiri, dan diagonal kiri-atas masing-masing punya persegi berukuran minimal k − 1.</p>
    </div>
    @include('lessons.code', ['cpp' => $dgSquare, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Mengapa minimum dari tiga?</strong> Tiga persegi berukuran k − 1 (di atas, di kiri, dan di serong kiri-atas) bersama petak (i, j) menutupi persegi berukuran k sepenuhnya. Jika salah satunya lebih kecil, ada petak 0 di dalam calon persegi berukuran k. Jadi ukuran yang bisa dicapai dibatasi oleh yang <strong>terkecil</strong> di antara ketiganya.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2><span class="sec-icon">🚀</span> Teknik Lanjutan: Satu Baris Saja</h2>
    <div class="prose">
        <p>Nilai <code>dp[i][j]</code> hanya bergantung pada baris yang sedang diisi dan baris tepat di atasnya. Jika rute tidak perlu disusun ulang, simpan <strong>satu baris</strong> saja: sebelum ditimpa, <code>baris[j]</code> masih menyimpan nilai dari atas, sedangkan <code>baris[j−1]</code> sudah berisi nilai kiri yang baru.</p>
    </div>
    @include('lessons.code', ['cpp' => $dgRow, 'js' => null, 'py' => null])
    <div class="prose">
        <p>Memori turun dari R × C menjadi C. Untuk grid 5000 × 5000, itu perbedaan antara 200 MB dan 40 KB!</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="Grid 3 × 3 (tanpa rintangan): banyak jalurnya C(4, 2) = 6. Di tabel: 1 1 1 / 1 2 3 / 1 3 6.">
        <p class="quiz-q">Berapa banyak jalur (kanan/bawah) pada grid 3 × 3 tanpa rintangan?</p>
        <div class="quiz-options">
            <button class="quiz-option">6</button>
            <button class="quiz-option">9</button>
            <button class="quiz-option">4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Isi baris demi baris dari kiri ke kanan menjamin sel atas (baris sebelumnya) dan sel kiri (kolom sebelumnya) sudah terisi.">
        <p class="quiz-q">Urutan pengisian mana yang benar untuk transisi atas + kiri?</p>
        <div class="quiz-options">
            <button class="quiz-option">Baris dari bawah ke atas</button>
            <button class="quiz-option">Acak, asal semua terisi</button>
            <button class="quiz-option">Baris dari atas ke bawah, kolom dari kiri ke kanan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Jika ketiga tetangga punya persegi berukuran 2, 3, dan 2, persegi di (i, j) maksimal min(2, 3, 2) + 1 = 3.">
        <p class="quiz-q">Pada soal persegi terbesar, dp atas = 2, kiri = 3, diagonal = 2, dan petak (i, j) = 1. Berapa dp[i][j]?</p>
        <div class="quiz-options">
            <button class="quiz-option">4</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
