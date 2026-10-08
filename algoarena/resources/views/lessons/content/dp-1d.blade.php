@php
    $d1Steps = [
        ['Header & nilai tak hingga', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int INF = 1e9;
CPP, <<<'TXT'
<p>Kita mencari <strong>minimum</strong>, jadi nominal yang belum bisa dibentuk diberi nilai sangat besar, <code>INF = 10<sup>9</sup></code>. Banyak koin tidak pernah mendekati angka itu, jadi aman memakai <code>int</code>.</p>
TXT],
        ['Membaca koin', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int k, x;
    cin >> k >> x;
    vector<int> koin(k);
    for (int i = 0; i < k; i++) cin >> koin[i];
CPP, <<<'TXT'
<p><code>k</code> jenis koin (setiap jenis boleh dipakai berkali-kali) dan nominal target <code>x</code>. Pada contoh: koin {1, 3, 4} dan target 6.</p>
TXT],
        ['State, catatan pilihan, dan base case', <<<'CPP'

    vector<int> dp(x + 1, INF);
    vector<int> pakai(x + 1, -1);
    dp[0] = 0;
CPP, <<<'TXT'
<ul>
    <li><strong>State:</strong> <code>dp[v]</code> = banyak koin <strong>paling sedikit</strong> untuk membentuk nominal tepat v. Awalnya INF ("belum bisa").</li>
    <li><code>pakai[v]</code> = koin terakhir yang dipakai pada solusi terbaik untuk v. Dipakai untuk menyusun ulang jawabannya.</li>
    <li><strong>Base case:</strong> nominal 0 dibentuk dengan 0 koin.</li>
</ul>
TXT],
        ['Transisi: koin terakhir', <<<'CPP'

    for (int v = 1; v <= x; v++) {
        for (int c : koin) {
            if (c <= v && dp[v - c] != INF && dp[v - c] + 1 < dp[v]) {
                dp[v] = dp[v - c] + 1;
                pakai[v] = c;
            }
        }
    }
CPP, <<<'TXT'
<p>Untuk setiap nominal v (dari kecil ke besar), coba setiap koin c sebagai <strong>koin terakhir</strong>. Jika koin terakhirnya c, sisanya <code>v − c</code> harus dibentuk seoptimal mungkin, yaitu <code>dp[v − c]</code> koin. Totalnya <code>dp[v − c] + 1</code>.</p>
<p>Ambil yang paling kecil dari semua pilihan c, dan catat c-nya di <code>pakai[v]</code>.</p>
<pre>v  : 0  1  2  3  4  5  6
dp : 0  1  2  1  1  2  2
dp[6]: lewat c=1 → dp[5]+1 = 3,  c=3 → dp[3]+1 = 2 ✓,  c=4 → dp[2]+1 = 3</pre>
<div class="wt-warn">Syarat <code>dp[v − c] != INF</code> penting: tanpa itu, <code>INF + 1</code> dianggap pilihan yang sah (dan untuk tipe yang lebih kecil bisa meluap).</div>
TXT],
        ['Jika mustahil', <<<'CPP'

    if (dp[x] == INF) {
        cout << -1 << '\n';
        return 0;
    }
CPP, <<<'TXT'
<p>Jika <code>dp[x]</code> masih INF, nominal x tidak bisa dibentuk sama sekali (misalnya koin {4, 6} dan target 7).</p>
TXT],
        ['Menyusun ulang koin yang dipakai', <<<'CPP'

    cout << dp[x] << '\n';
    vector<int> hasil;
    for (int v = x; v > 0; v -= pakai[v]) hasil.push_back(pakai[v]);
    for (int i = 0; i < (int)hasil.size(); i++) {
        cout << hasil[i] << (i + 1 < (int)hasil.size() ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Cetak banyak koinnya, lalu telusuri mundur: dari x, ambil koin <code>pakai[x]</code>, pindah ke <code>x − pakai[x]</code>, dan seterusnya sampai 0.</p>
<pre>v = 6 → pakai 3 → v = 3 → pakai 3 → v = 0, selesai
hasil: 3 3</pre>
<div class="wt-tip">Teknik "simpan pilihan, lalu telusuri mundur" dipakai di hampir semua soal DP yang meminta <em>bentuk</em> jawabannya, bukan hanya nilainya.</div>
TXT],
    ];

    $d1Js = <<<'JS'
const [k, x] = readInts();
const koin = readInts();

const INF = 1e9;
const dp = new Array(x + 1).fill(INF);
const pakai = new Array(x + 1).fill(-1);
dp[0] = 0;
for (let v = 1; v <= x; v++) {
  for (const c of koin) {
    if (c <= v && dp[v - c] + 1 < dp[v]) {
      dp[v] = dp[v - c] + 1;
      pakai[v] = c;
    }
  }
}

if (dp[x] >= INF) {
  console.log(-1);
} else {
  const hasil = [];
  for (let v = x; v > 0; v -= pakai[v]) hasil.push(pakai[v]);
  console.log(dp[x] + "\n" + hasil.join(" "));
}
JS;

    $d1Py = <<<'PY'
k, x = map(int, input().split())
koin = list(map(int, input().split()))

INF = 10**9
dp = [INF] * (x + 1)
pakai = [-1] * (x + 1)
dp[0] = 0
for v in range(1, x + 1):
    for c in koin:
        if c <= v and dp[v - c] + 1 < dp[v]:
            dp[v] = dp[v - c] + 1
            pakai[v] = c

if dp[x] >= INF:
    print(-1)
else:
    hasil = []
    v = x
    while v > 0:
        hasil.append(pakai[v])
        v -= pakai[v]
    print(dp[x])
    print(*hasil)
PY;

    $d1Order = <<<'CPP'
// KOMBINASI (urutan tidak penting: 1+3 sama dengan 3+1) → koin di loop LUAR
vector<long long> komb(x + 1, 0);
komb[0] = 1;
for (int c : koin)
    for (int v = c; v <= x; v++)
        komb[v] = (komb[v] + komb[v - c]) % MOD;

// PERMUTASI (urutan penting: 1+3 berbeda dengan 3+1) → nominal di loop LUAR
vector<long long> perm(x + 1, 0);
perm[0] = 1;
for (int v = 1; v <= x; v++)
    for (int c : koin)
        if (c <= v) perm[v] = (perm[v] + perm[v - c]) % MOD;
CPP;

    $d1Robber = <<<'CPP'
// Tidak boleh mengambil dua elemen bersebelahan, maksimalkan jumlah
// dp[i] = hasil terbaik dari elemen 0..i
dp[0] = a[0];
dp[1] = max(a[0], a[1]);
for (int i = 2; i < n; i++)
    dp[i] = max(dp[i - 1],          // lewati elemen i
                dp[i - 2] + a[i]);  // ambil elemen i, berarti i-1 dilewati
CPP;

    $d1Kadane = <<<'CPP'
// Kadane: jumlah subarray (bagian berurutan) terbesar dalam O(N)
// best = subarray terbaik yang BERAKHIR di i
long long best = a[0], jawaban = a[0];
for (int i = 1; i < n; i++) {
    best = max(a[i], best + a[i]);   // mulai baru di i, atau sambung yang sebelumnya
    jawaban = max(jawaban, best);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Merancang DP satu dimensi untuk <strong>menghitung cara</strong> dan mencari <strong>minimum</strong>.</li>
        <li>Menjelaskan mengapa strategi <strong>serakah</strong> (ambil koin terbesar) bisa salah.</li>
        <li>Menulis DP koin minimum di C++ beserta <strong>rekonstruksi</strong> koin yang dipakai.</li>
        <li>Membedakan menghitung <strong>kombinasi vs permutasi</strong>, dan mengenal pola "ambil atau lewati" serta Kadane.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'DP pada satu baris angka', 'desc' => 'Dua soal klasik: menghitung cara naik tangga dan kembalian dengan koin paling sedikit.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Kembalian Koin">
    <h2><span class="sec-icon">💡</span> Intuisi: Kembalian dengan Koin Aneh</h2>
    <div class="prose">
        <p>Sebuah kantin hanya punya koin bernilai <strong>1, 3, dan 4</strong>. Kasir ingin memberi kembalian <strong>6</strong> dengan koin sesedikit mungkin.</p>
        <p>Cara "serakah": ambil koin terbesar dulu. 4, sisa 2, lalu 1 + 1. Total <strong>3 koin</strong>. Padahal <strong>3 + 3 = 6</strong> hanya butuh <strong>2 koin</strong>! Serakah tidak selalu benar, jadi kita perlu memeriksa semua kemungkinan secara cerdas: DP.</p>
        <p>Pertanyaan kuncinya: <strong>"koin terakhir yang dipakai apa?"</strong> Jika koin terakhir 3, sisanya 6 − 3 = 3 harus dibentuk seminimal mungkin. Itu subsoal yang lebih kecil!</p>
    </div>
    <div class="diagram-grid" style="max-width: 860px; margin-bottom: 14px">
        <div>
            <h5>Koin minimum: dp[v] = min(dp[v − c] + 1)</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(8, auto)">
                <span class="h">v</span><span class="h">0</span><span class="h">1</span><span class="h">2</span><span class="h">3</span><span class="h">4</span><span class="h">5</span><span class="h">6</span>
                <span class="h">dp</span><span class="ok">0</span><span>1</span><span class="dep2">2</span><span class="dep">1</span><span>1</span><span class="dep2">2</span><span class="cur">2</span>
            </div>
            <p class="muted" style="font-size: 12.5px; margin: 6px 4px 0">dp[6] = min(dp[5]+1, <b style="color:var(--cyan)">dp[3]+1</b>, dp[2]+1) = min(3, 2, 3) = 2</p>
        </div>
        <div>
            <h5>Banyak cara naik tangga (1 atau 2): dp[i] = dp[i−1] + dp[i−2]</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(8, auto)">
                <span class="h">i</span><span class="h">0</span><span class="h">1</span><span class="h">2</span><span class="h">3</span><span class="h">4</span><span class="h">5</span><span class="h">6</span>
                <span class="h">dp</span><span class="ok">1</span><span>1</span><span>2</span><span>3</span><span class="dep2">5</span><span class="dep">8</span><span class="cur">13</span>
            </div>
            <p class="muted" style="font-size: 12.5px; margin: 6px 4px 0">dp[6] = dp[5] + dp[4] = 8 + 5 = 13</p>
        </div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Menghitung cara memakai <strong>penjumlahan</strong>, mencari yang terbaik memakai <strong>min/max</strong>. Kerangkanya sama: lihat langkah terakhir, lalu rujuk subsoal yang lebih kecil.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2><span class="sec-icon">✍️</span> Coba dengan Tangan</h2>
    <div class="prose"><p>Isi <code>dp[v]</code> untuk koin {1, 3, 4} dari v = 1 sampai 6. Setiap baris mencoba ketiga koin sebagai koin terakhir.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>v</th><th>c = 1</th><th>c = 3</th><th>c = 4</th><th>dp[v]</th><th>pakai[v]</th></tr>
            <tr><td>1</td><td>dp[0]+1 = 1</td><td>–</td><td>–</td><td><span class="new">1</span></td><td>1</td></tr>
            <tr><td>2</td><td>dp[1]+1 = 2</td><td>–</td><td>–</td><td><span class="new">2</span></td><td>1</td></tr>
            <tr class="hl"><td>3</td><td>dp[2]+1 = 3</td><td>dp[0]+1 = <b>1</b></td><td>–</td><td><span class="new">1</span></td><td>3</td></tr>
            <tr class="hl"><td>4</td><td>dp[3]+1 = 2</td><td>dp[1]+1 = 2</td><td>dp[0]+1 = <b>1</b></td><td><span class="new">1</span></td><td>4</td></tr>
            <tr><td>5</td><td>dp[4]+1 = <b>2</b></td><td>dp[2]+1 = 3</td><td>dp[1]+1 = 2</td><td><span class="new">2</span></td><td>1</td></tr>
            <tr class="ok"><td>6</td><td>dp[5]+1 = 3</td><td>dp[3]+1 = <b>2</b></td><td>dp[2]+1 = 3</td><td><span class="new">2</span></td><td>3</td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2><span class="sec-icon">🎬</span> Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Mode <strong>Banyak cara</strong> mengisi tabel tangga, mode <strong>Koin minimum</strong> mengisi tabel kembalian. Ubah daftar langkah/koin dan target, lalu tekan <strong>Terapkan</strong>. Panah menunjukkan sel-sel yang dipakai untuk menghitung sel kuning. Di akhir mode koin, jejak hijau menunjukkan koin yang dipakai.</p>
    </div>
    <div data-viz="coin"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>State</b><span><code>dp[v]</code> = jawaban untuk "ukuran" v (nominal, anak tangga, posisi ke-v).</span></div>
        <div class="step-card"><b>Transisi dari langkah terakhir</b><span>Daftar semua pilihan langkah terakhir; jumlahkan (hitung cara) atau ambil min/max (optimasi).</span></div>
        <div class="step-card"><b>Base case & nilai awal</b><span><code>dp[0]</code> = 1 (cara) atau 0 (minimum). Sisanya 0 (cara) atau INF (minimum).</span></div>
        <div class="step-card"><b>Isi dari kecil ke besar</b><span>Lalu jawab <code>dp[X]</code>. Cek kasus mustahil (masih INF).</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis DP 1D di C++', 'desc' => 'Koin minimum lengkap dengan rekonstruksi, lalu pola-pola soal 1D.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2><span class="sec-icon">💻</span> Kode C++ Lengkap: Kembalian Minimum</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> ada k jenis koin (masing-masing tak terbatas) dan target X. Cetak banyak koin minimum dan koin-koin yang dipakai, atau <code>-1</code> jika mustahil.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'DP koin minimum + rekonstruksi',
        'steps' => $d1Steps,
        'sample' => ['input' => "3 6\n1 3 4\n", 'output' => "2\n3 3\n"],
        'js' => $d1Js,
        'py' => $d1Py,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal DP 1D">
    <h2><span class="sec-icon">🧠</span> Pola Soal DP 1 Dimensi</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>🐸 Lompatan</h4><p>Dari posisi i−1 atau i−2 (atau sampai i−K), dengan biaya. <code>dp[i] = min(dp[j] + biaya(j, i))</code>.</p></div>
        <div class="pattern"><h4>🪙 Koin</h4><p>Minimum koin, banyak cara membentuk nominal, atau "bisakah dibentuk?".</p></div>
        <div class="pattern"><h4>🥭 Ambil atau lewati</h4><p>"Tidak boleh dua yang bersebelahan": <code>dp[i] = max(dp[i−1], dp[i−2] + a[i])</code>.</p></div>
        <div class="pattern"><h4>📈 Subarray terbaik</h4><p>Jumlah bagian berurutan terbesar: algoritma Kadane.</p></div>
        <div class="pattern"><h4>🔤 Memecah string</h4><p>"Berapa cara membaca kode 1226 sebagai huruf?" Langkah terakhir: 1 atau 2 digit.</p></div>
        <div class="pattern"><h4>🚫 Ada larangan</h4><p>Anak tangga rusak, petak terlarang: paksa <code>dp[i] = 0</code> (cara) atau INF (minimum).</p></div>
    </div>
    <div class="prose">
        <h3>Kombinasi atau permutasi? Urutan loop menentukan!</h3>
        <p>"Ada berapa cara membentuk 4 dari koin {1, 3}?" Jika <strong>urutan tidak penting</strong> (1+3 sama dengan 3+1), jawabannya 2: {1,1,1,1} dan {1,3}. Jika <strong>urutan penting</strong>, jawabannya 3: 1+1+1+1, 1+3, 3+1. Perbedaannya hanya posisi loop:</p>
    </div>
    @include('lessons.code', ['cpp' => $d1Order, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧭</span>
        <p>Dengan koin di loop luar, semua pemakaian koin pertama "selesai" sebelum koin kedua dipertimbangkan, sehingga setiap kumpulan koin hanya terhitung dalam satu urutan baku.</p>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Soal</th><th>Waktu</th><th>Memori</th></tr>
        <tr><td>Koin (k jenis, target X)</td><td><code>O(k · X)</code></td><td><code>O(X)</code></td></tr>
        <tr><td>Lompatan maksimal K langkah</td><td><code>O(N · K)</code></td><td><code>O(N)</code></td></tr>
        <tr><td>Kadane / ambil-lewati</td><td><code>O(N)</code></td><td><code>O(1)</code> dengan variabel bergulir</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Serakah itu menggoda.</strong> Untuk koin rupiah (100, 200, 500, 1000) serakah kebetulan benar, tetapi untuk himpunan koin sembarang tidak. Jika ragu, coba cari contoh kecil yang menggagalkan serakah.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Indeks negatif.</strong> Selalu cek <code>c &lt;= v</code> atau <code>i &gt;= 2</code> sebelum mengakses <code>dp[v − c]</code> atau <code>dp[i − 2]</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'pola 1D tingkat lanjut', 'desc' => 'Bukti transisi, "ambil atau lewati", dan algoritma Kadane.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Transisinya Benar?">
    <h2><span class="sec-icon">🔬</span> Mengapa Transisinya Benar?</h2>
    <div class="proof">
        <p><strong>Optimal substructure.</strong> Ambil solusi optimal untuk nominal v dan misalkan koin terakhirnya c. Koin-koin sisanya membentuk v − c. Jika ada cara membentuk v − c dengan koin <em>lebih sedikit</em>, kita bisa menggantinya dan mendapat solusi v yang lebih baik, padahal solusi awal sudah optimal. Kontradiksi. Jadi sisanya pasti juga optimal: <code>dp[v] = dp[v − c] + 1</code> untuk c yang tepat.</p>
        <p>Karena kita tidak tahu c mana yang tepat, kita coba <strong>semuanya</strong> dan ambil minimum. Tidak ada kemungkinan yang terlewat.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2><span class="sec-icon">🚀</span> Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Ambil atau lewati</h3>
        <p>Soal: deretan pohon mangga dengan banyak buah <code>a[i]</code>. Kamu tidak boleh memanen dua pohon yang bersebelahan. Berapa buah terbanyak? Setiap pohon i hanya punya dua pilihan:</p>
    </div>
    @include('lessons.code', ['cpp' => $d1Robber, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. Algoritma Kadane</h3>
        <p>Jumlah subarray terbesar adalah DP dengan state "subarray terbaik yang <strong>berakhir</strong> di i". Di setiap i, pilihannya: mulai subarray baru di i, atau menyambung yang terbaik sebelumnya.</p>
    </div>
    @include('lessons.code', ['cpp' => $d1Kadane, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>3. Lompatan sampai K langkah</h3>
        <p>Jika katak boleh melompat 1 sampai K batu, transisinya <code>dp[i] = min(dp[i−j] + |h[i] − h[i−j]|)</code> untuk j = 1..K. Totalnya O(N · K), cukup untuk N · K ≤ ±10<sup>8</sup>.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Serakah: 4 + 1 + 1 = 3 koin. Optimal: 3 + 3 = 2 koin.">
        <p class="quiz-q">Koin {1, 3, 4}, kembalian 6. Berapa koin minimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">3 (4 + 1 + 1)</button>
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Untuk minimum, nilai awal INF berarti belum bisa dibentuk. Jika diisi 0, min() akan selalu memilih 0 yang salah.">
        <p class="quiz-q">Pada DP koin minimum, <code>dp[v]</code> untuk v &gt; 0 sebaiknya diinisialisasi dengan…</p>
        <div class="quiz-options">
            <button class="quiz-option">INF (nilai sangat besar)</button>
            <button class="quiz-option">0</button>
            <button class="quiz-option">-1 lalu dibandingkan dengan min()</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Agar setiap kumpulan koin dihitung sekali (kombinasi), loop jenis koin diletakkan di luar dan loop nominal di dalam.">
        <p class="quiz-q">Menghitung banyak <strong>kombinasi</strong> koin (1+3 dianggap sama dengan 3+1). Loop mana yang di luar?</p>
        <div class="quiz-options">
            <button class="quiz-option">Loop nominal v</button>
            <button class="quiz-option">Loop jenis koin c</button>
            <button class="quiz-option">Tidak berpengaruh</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
