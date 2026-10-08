@php
    $lcsSteps = [
        ['Header dan membaca dua string', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string a, b;
    cin >> a >> b;
    int n = a.size(), m = b.size();
CPP, <<<'TXT'
<p>Dua string dibaca dengan <code>cin</code> (tanpa spasi di dalamnya). Contoh: <code>a = "ABCBDAB"</code> (n = 7) dan <code>b = "BDCABA"</code> (m = 6).</p>
TXT],
        ['Tabel (n + 1) × (m + 1)', <<<'CPP'

    // dp[i][j] = panjang LCS dari a[0..i) dan b[0..j)
    vector<vector<int>> dp(n + 1, vector<int>(m + 1, 0));
CPP, <<<'TXT'
<p>Ukurannya satu lebih besar dari panjang string karena baris 0 dan kolom 0 mewakili <strong>string kosong</strong>. LCS antara apa pun dengan string kosong adalah 0, dan itu sudah terisi otomatis.</p>
<div class="wt-tip">Notasi <code>a[0..i)</code> artinya i huruf pertama a: indeks 0 sampai i−1.</div>
TXT],
        ['Transisi: bandingkan huruf terakhir', <<<'CPP'

    for (int i = 1; i <= n; i++) {
        for (int j = 1; j <= m; j++) {
            if (a[i - 1] == b[j - 1]) {
                dp[i][j] = dp[i - 1][j - 1] + 1;
            } else {
                dp[i][j] = max(dp[i - 1][j], dp[i][j - 1]);
            }
        }
    }
CPP, <<<'TXT'
<p>Huruf terakhir prefiks <code>a[0..i)</code> adalah <code>a[i − 1]</code>, bukan <code>a[i]</code>. Indeks dp bergeser satu dari indeks string.</p>
<ul>
    <li><strong>Sama:</strong> huruf itu dipakai sebagai huruf terakhir LCS. Buang dari keduanya: diagonal + 1.</li>
    <li><strong>Beda:</strong> paling tidak satu dari keduanya tidak terpakai. Coba buang huruf terakhir a (sel atas) atau huruf terakhir b (sel kiri), ambil yang lebih panjang.</li>
</ul>
<pre>i=2 (B), j=1 (B): sama → dp[1][0] + 1 = 1
i=2 (B), j=2 (D): beda → max(dp[1][2], dp[2][1]) = max(0, 1) = 1</pre>
TXT],
        ['Telusur balik dari pojok kanan bawah', <<<'CPP'

    string hasil;
    int i = n, j = m;
    while (i > 0 && j > 0) {
        if (a[i - 1] == b[j - 1]) {
            hasil += a[i - 1];
            i--, j--;
        } else if (dp[i - 1][j] >= dp[i][j - 1]) {
            i--;
        } else {
            j--;
        }
    }
    reverse(hasil.begin(), hasil.end());
CPP, <<<'TXT'
<p>Ikuti kembali keputusan yang menghasilkan setiap sel:</p>
<ul>
    <li>Huruf sama: huruf itu bagian dari LCS. Simpan, lalu bergerak diagonal.</li>
    <li>Huruf beda: pindah ke tetangga (atas atau kiri) yang nilainya lebih besar, karena dari situlah nilai sel ini berasal.</li>
</ul>
<p>Huruf terkumpul dari belakang ke depan, jadi string dibalik di akhir.</p>
<div class="wt-tip">Jika atas dan kiri sama besar, keduanya benar. Pilihan yang berbeda bisa memberi LCS yang berbeda tetapi sama panjang.</div>
TXT],
        ['Mencetak hasil', <<<'CPP'

    cout << dp[n][m] << '\n' << hasil << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Panjang LCS ada di <code>dp[n][m]</code>. Untuk contoh, hasilnya 4 dengan salah satu LCS <code>BCBA</code>. (<code>BDAB</code> dan <code>BCAB</code> juga LCS yang sah.)</p>
TXT],
    ];

    $lcsJs = <<<'JS'
const a = readLine().trim();
const b = readLine().trim();
const n = a.length, m = b.length;

const dp = Array.from({ length: n + 1 }, () => new Array(m + 1).fill(0));
for (let i = 1; i <= n; i++) {
  for (let j = 1; j <= m; j++) {
    if (a[i - 1] === b[j - 1]) dp[i][j] = dp[i - 1][j - 1] + 1;
    else dp[i][j] = Math.max(dp[i - 1][j], dp[i][j - 1]);
  }
}

let i = n, j = m, hasil = "";
while (i > 0 && j > 0) {
  if (a[i - 1] === b[j - 1]) { hasil = a[i - 1] + hasil; i--; j--; }
  else if (dp[i - 1][j] >= dp[i][j - 1]) i--;
  else j--;
}
console.log(dp[n][m] + "\n" + hasil);
JS;

    $lcsPy = <<<'PY'
a = input().strip()
b = input().strip()
n, m = len(a), len(b)

dp = [[0] * (m + 1) for _ in range(n + 1)]
for i in range(1, n + 1):
    for j in range(1, m + 1):
        if a[i - 1] == b[j - 1]:
            dp[i][j] = dp[i - 1][j - 1] + 1
        else:
            dp[i][j] = max(dp[i - 1][j], dp[i][j - 1])

i, j, hasil = n, m, []
while i > 0 and j > 0:
    if a[i - 1] == b[j - 1]:
        hasil.append(a[i - 1])
        i -= 1
        j -= 1
    elif dp[i - 1][j] >= dp[i][j - 1]:
        i -= 1
    else:
        j -= 1
print(dp[n][m])
print("".join(reversed(hasil)))
PY;

    $lcsRoll = <<<'CPP'
// Hanya butuh panjangnya? Dua baris cukup: O(m) memori
vector<int> prev(m + 1, 0), cur(m + 1, 0);
for (int i = 1; i <= n; i++) {
    for (int j = 1; j <= m; j++) {
        if (a[i - 1] == b[j - 1]) cur[j] = prev[j - 1] + 1;
        else cur[j] = max(prev[j], cur[j - 1]);
    }
    swap(prev, cur);
}
cout << prev[m] << '\n';
CPP;

    $lcsPal = <<<'CPP'
// Subsequence palindrom terpanjang = LCS(s, kebalikan s)
string r(s.rbegin(), s.rend());
// ... jalankan LCS pada s dan r

// Huruf minimum yang harus disisipkan agar s menjadi palindrom
// = n − (panjang subsequence palindrom terpanjang)
CPP;

    $lcsPerm = <<<'CPP'
// Jika a dan b adalah PERMUTASI (setiap angka muncul sekali di masing-masing),
// LCS bisa dihitung dalam O(n log n):
// ganti setiap angka di b dengan posisinya di a, lalu cari LIS-nya.
vector<int> posA(n + 1);
for (int i = 0; i < n; i++) posA[a[i]] = i;
vector<int> c(n);
for (int j = 0; j < n; j++) c[j] = posA[b[j]];
// jawaban = panjang LIS dari c   (lihat materi LIS)
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan beda <strong>substring</strong> dan <strong>subsequence</strong>.</li>
        <li>Merancang DP dua string <code>dp[i][j]</code> dengan melihat <strong>huruf terakhir</strong> kedua prefiks.</li>
        <li>Menulis LCS di C++ lengkap dengan telusur balik untuk mendapatkan string-nya.</li>
        <li>Memakai pola yang sama untuk edit distance, palindrom, dan LCS permutasi.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'membandingkan dua string', 'desc' => 'Pengertian subsequence, rumus LCS, dan mengisi tabelnya dengan tangan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Mencari Kesamaan</h2>
    <div class="prose">
        <p><strong>Subsequence</strong> adalah huruf-huruf yang diambil dari sebuah string <em>dengan urutan tetap</em>, tetapi tidak harus berdekatan. Dari <code>PROGRAM</code> kita bisa mengambil <code>PGM</code> atau <code>ROAM</code>, tetapi tidak bisa <code>MAP</code> karena urutannya terbalik. Berbeda dengan <strong>substring</strong>, yang hurufnya harus bersebelahan.</p>
        <p><strong>Longest Common Subsequence</strong> (LCS) adalah subsequence terpanjang yang dimiliki <em>dua</em> string sekaligus. Ide ini dipakai oleh <code>git diff</code>, pendeteksi plagiarisme, dan analisis DNA.</p>
        <p>Kuncinya ada di <strong>huruf terakhir</strong> kedua string:</p>
        <div class="term-grid">
            <div class="term"><b>Huruf terakhir sama</b><span>Aman dipakai di LCS. Buang dari keduanya dan tambah 1: <code>dp[i−1][j−1] + 1</code></span></div>
            <div class="term"><b>Huruf terakhir beda</b><span>Paling tidak satu tidak terpakai. Coba buang masing-masing, ambil yang terbaik: <code>max(dp[i−1][j], dp[i][j−1])</code></span></div>
        </div>
    </div>
    <div class="recurrence"><small>Rumus transisi</small>dp[i][j] = dp[i−1][j−1] + 1                 jika a[i−1] = b[j−1]
dp[i][j] = max(dp[i−1][j], dp[i][j−1])     jika berbeda
dp[0][j] = dp[i][0] = 0</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Pada DP dua string, state-nya hampir selalu <strong>"prefiks A sepanjang i dan prefiks B sepanjang j"</strong>. Transisinya hanya melihat huruf terakhir masing-masing prefiks.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose">
        <p>a = <code>ABCBDAB</code> (baris), b = <code>BDCABA</code> (kolom). Sel tebal adalah tempat huruf sama (diagonal + 1).</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th></th><th>∅</th><th>B</th><th>D</th><th>C</th><th>A</th><th>B</th><th>A</th></tr>
            <tr><td>∅</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
            <tr><td>A</td><td>0</td><td>0</td><td>0</td><td>0</td><td><b>1</b></td><td>1</td><td><b>1</b></td></tr>
            <tr><td>B</td><td>0</td><td><b>1</b></td><td>1</td><td>1</td><td>1</td><td><b>2</b></td><td>2</td></tr>
            <tr><td>C</td><td>0</td><td>1</td><td>1</td><td><b>2</b></td><td>2</td><td>2</td><td>2</td></tr>
            <tr><td>B</td><td>0</td><td><b>1</b></td><td>1</td><td>2</td><td>2</td><td><b>3</b></td><td>3</td></tr>
            <tr><td>D</td><td>0</td><td>1</td><td><b>2</b></td><td>2</td><td>2</td><td>3</td><td>3</td></tr>
            <tr><td>A</td><td>0</td><td>1</td><td>2</td><td>2</td><td><b>3</b></td><td>3</td><td><b>4</b></td></tr>
            <tr class="ok"><td>B</td><td>0</td><td><b>1</b></td><td>2</td><td>2</td><td>3</td><td><b>4</b></td><td><span class="new">4</span></td></tr>
        </table>
    </div>
    <div class="prose">
        <p>Jawabannya <code>dp[7][6] = 4</code>. Telusur balik dari pojok kanan bawah: B ≠ A, atas (4) ≥ kiri (4) → naik; A = A → ambil <b>A</b>, diagonal; B = B → ambil <b>B</b>; … sampai terkumpul <code>BCBA</code>.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi LCS</h2>
    <div class="prose">
        <p>Saat huruf sama, panah <span style="color:var(--cyan)">diagonal</span> muncul (+1). Saat beda, dua panah membandingkan atas dan kiri. Di akhir, jejak hijau merekonstruksi LCS-nya. Coba ganti dengan kata-katamu sendiri.</p>
    </div>
    <div data-viz="lcs"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan: DP Dua String</h2>
    <div class="steps">
        <div class="step-card"><b>State</b><span><code>dp[i][j]</code> = jawaban untuk <strong>prefiks</strong> A sepanjang i dan prefiks B sepanjang j.</span></div>
        <div class="step-card"><b>Base case</b><span>Baris 0 dan kolom 0 adalah string kosong. Untuk LCS nilainya 0.</span></div>
        <div class="step-card"><b>Bandingkan huruf terakhir</b><span><code>A[i−1]</code> vs <code>B[j−1]</code> (indeks string mulai dari 0).</span></div>
        <div class="step-card"><b>Isi baris demi baris</b><span>Sel atas, kiri, dan diagonal selalu sudah terisi lebih dulu.</span></div>
        <div class="step-card"><b>Jawaban & rekonstruksi</b><span><code>dp[n][m]</code>. Untuk string-nya, telusur balik dari pojok kanan bawah.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis LCS di C++', 'desc' => 'Program lengkap dengan telusur balik, dan versi hemat memori.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: LCS + Rekonstruksi</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> diberikan dua string. Cetak panjang LCS-nya, lalu salah satu LCS.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Longest Common Subsequence',
        'steps' => $lcsSteps,
        'sample' => ['input' => "ABCBDAB\nBDCABA\n", 'output' => "4\nBCBA\n"],
        'js' => $lcsJs,
        'py' => $lcsPy,
    ])
</section>

<section class="lesson-section" id="hemat" data-toc="Hemat Memori">
    <h2>Hemat Memori: Dua Baris</h2>
    <div class="prose">
        <p>Baris i hanya membaca baris i−1 (atas dan diagonal) serta baris i sendiri (kiri). Jika yang dibutuhkan hanya <em>panjang</em> LCS, dua baris sudah cukup.</p>
    </div>
    @include('lessons.code', ['cpp' => $lcsRoll, 'js' => null, 'py' => null])
    <table class="cx-table">
        <tr><th>Versi</th><th>Waktu</th><th>Memori</th></tr>
        <tr><td>Tabel penuh (bisa rekonstruksi)</td><td><code>O(n · m)</code></td><td><code>O(n · m)</code></td></tr>
        <tr><td>Dua baris (panjang saja)</td><td><code>O(n · m)</code></td><td><code>O(m)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Untuk n = m = 5000, tabel <code>int</code> penuh butuh 100 MB. Periksa batas memori soal sebelum membuat tabel 2D.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Membaca string dengan <code>getline</code> setelah <code>cin &gt;&gt;</code> sering menghasilkan string kosong. Jika string tidak mengandung spasi, pakai <code>cin &gt;&gt; s</code> saja.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'keluarga DP string', 'desc' => 'Edit distance, palindrom, dan LCS yang lebih cepat untuk permutasi.'])

<section class="lesson-section" id="edit" data-toc="Edit Distance">
    <h2>Edit Distance (Levenshtein)</h2>
    <div class="prose">
        <p>Berapa operasi minimum (sisip, hapus, ganti satu huruf) untuk mengubah a menjadi b? Tabelnya sama, hanya base case dan transisinya yang berbeda:</p>
    </div>
    <div class="recurrence"><small>Rumus edit distance</small>dp[i][0] = i      (hapus semua i huruf)
dp[0][j] = j      (sisipkan j huruf)
dp[i][j] = dp[i−1][j−1]                                    jika a[i−1] = b[j−1]
dp[i][j] = 1 + min( dp[i−1][j−1],   ← ganti
                    dp[i−1][j],     ← hapus a[i−1]
                    dp[i][j−1] )    ← sisipkan b[j−1]</div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th></th><th>∅</th><th>S</th><th>A</th><th>T</th></tr>
            <tr><td>∅</td><td>0</td><td>1</td><td>2</td><td>3</td></tr>
            <tr><td>C</td><td>1</td><td>1</td><td>2</td><td>3</td></tr>
            <tr><td>A</td><td>2</td><td>2</td><td><b>1</b></td><td>2</td></tr>
            <tr class="ok"><td>T</td><td>3</td><td>3</td><td>2</td><td><span class="new">1</span></td></tr>
        </table>
    </div>
    <div class="prose"><p>"CAT" menjadi "SAT" cukup dengan 1 operasi (ganti C dengan S). Soal latihan <em>Koreksi Ketikan</em> memakai rumus ini.</p></div>
</section>

<section class="lesson-section" id="variasi" data-toc="Variasi Lain">
    <h2>Variasi Lain</h2>
    <div class="prose">
        <h3>Palindrom</h3>
        <p>Subsequence palindrom terpanjang dari s sama dengan LCS antara s dan kebalikannya. Dari situ, banyak huruf minimum yang harus disisipkan agar s menjadi palindrom adalah n dikurangi panjang tersebut.</p>
    </div>
    @include('lessons.code', ['cpp' => $lcsPal, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>LCS dua permutasi dalam O(n log n)</h3>
        <p>Jika n sampai 10<sup>5</sup>, tabel n × m tidak mungkin. Tetapi bila setiap angka muncul tepat sekali di kedua barisan, LCS berubah menjadi LIS.</p>
    </div>
    @include('lessons.code', ['cpp' => $lcsPerm, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>LCS</h4><p>Huruf sama: diagonal + 1. Beda: max(atas, kiri).</p></div>
        <div class="pattern"><h4>Edit distance</h4><p>Sama: diagonal. Beda: 1 + min(tiga tetangga).</p></div>
        <div class="pattern"><h4>Substring bersama terpanjang</h4><p>Sama: diagonal + 1. Beda: <strong>0</strong>. Jawaban = max seluruh tabel.</p></div>
        <div class="pattern"><h4>Banyak cara</h4><p>"Berapa kali b muncul sebagai subsequence a?" Ganti max dengan penjumlahan.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="'ACE' muncul berurutan di ABCDE (A, C, E). Panjangnya 3.">
        <p class="quiz-q">Berapa panjang LCS dari "ABCDE" dan "ACE"?</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">5</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Baris/kolom 0 mewakili string kosong, sehingga indeks dp bergeser satu dari indeks string. Huruf untuk dp[i][j] adalah a[i−1] dan b[j−1].">
        <p class="quiz-q">Mengapa yang dibandingkan a[i−1] dan b[j−1], bukan a[i] dan b[j]?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena baris/kolom 0 dipakai untuk string kosong</button>
            <button class="quiz-option">Agar lebih cepat</button>
            <button class="quiz-option">Karena string C++ mulai dari indeks 1</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Substring harus bersebelahan, jadi begitu hurufnya berbeda, rangkaian terputus dan nilainya kembali 0.">
        <p class="quiz-q">Untuk <strong>substring</strong> bersama terpanjang, berapa dp[i][j] saat a[i−1] ≠ b[j−1]?</p>
        <div class="quiz-options">
            <button class="quiz-option">max(dp[i−1][j], dp[i][j−1])</button>
            <button class="quiz-option">dp[i−1][j−1]</button>
            <button class="quiz-option">0</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
