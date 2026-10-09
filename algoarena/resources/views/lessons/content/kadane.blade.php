@php
    $kdSteps = [
        ['Membaca data', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;
CPP, <<<'TXT'
<p><strong>Soal contoh "Potongan Terbaik":</strong> diberikan n bilangan (boleh negatif). Pilih satu potongan berurutan yang <strong>tidak kosong</strong> dengan jumlah terbesar. Cetak jumlahnya serta indeks awal dan akhirnya (1-indexed; jika ada beberapa, yang paling awal).</p>
<p>Ada O(n²) potongan, dan menjumlahkan semuanya O(n³). Kadane menyelesaikannya dalam satu kali jalan.</p>
TXT],
        ['State: terbaik yang berakhir di sini', <<<'CPP'

    long long cur = a[0], best = a[0];   // BUKAN 0
    int mulai = 0, bl = 0, br = 0;
CPP, <<<'TXT'
<p>Pertanyaannya dipersempit: bukan "potongan terbaik di seluruh array", tetapi <strong>"potongan terbaik yang wajib berakhir di indeks i"</strong>. Itulah <code>cur</code>.</p>
<p><code>best</code> adalah rekor keseluruhan, disimpan terpisah. Keduanya dimulai dari <code>a[0]</code>: di indeks 0 hanya ada satu potongan, yaitu elemen itu sendiri.</p>
<div class="wt-warn">Jika dimulai dari 0, array yang semua elemennya negatif akan menjawab 0 (potongan kosong), padahal potongan tidak boleh kosong.</div>
TXT],
        ['Dua pilihan di setiap posisi', <<<'CPP'
    for (int i = 1; i < n; i++) {
        if (cur + a[i] >= a[i]) {
            cur += a[i];                 // sambung potongan sebelumnya
        } else {
            cur = a[i];                  // mulai baru dari i
            mulai = i;
        }
CPP, <<<'TXT'
<p>Potongan yang berakhir di i entah <strong>menyambung</strong> potongan terbaik yang berakhir di i−1, entah <strong>mulai baru</strong> tepat di i. Tidak ada kemungkinan ketiga.</p>
<p>Membandingkan <code>cur + a[i] ≥ a[i]</code> sama dengan <code>cur ≥ 0</code>: masa lalu yang negatif hanya menyeret turun, jadi buang.</p>
<p>Tanda <code>≥</code> (bukan &gt;) membuat seri diselesaikan dengan menyambung, sehingga indeks awal yang lebih kecil dipertahankan.</p>
TXT],
        ['Catat rekor', <<<'CPP'
        if (cur > best) {
            best = cur;
            bl = mulai;
            br = i;
        }
    }

    cout << best << " " << bl + 1 << " " << br + 1 << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Tanda <code>&gt;</code> mempertahankan rekor yang <em>paling awal</em> jika ada beberapa potongan dengan jumlah sama.</p>
<p>Ini adalah DP satu dimensi: <code>dp[i] = max(a[i], dp[i−1] + a[i])</code>, jawaban = max semua dp[i]. Karena dp[i] hanya butuh dp[i−1], cukup satu variabel.</p>
TXT],
    ];

    $kdPy = <<<'PY'
import sys
data = sys.stdin.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + n]))
cur = best = a[0]
mulai = bl = br = 0
for i in range(1, n):
    if cur + a[i] >= a[i]:
        cur += a[i]
    else:
        cur = a[i]
        mulai = i
    if cur > best:
        best, bl, br = cur, mulai, i
print(best, bl + 1, br + 1)
PY;

    $kdDp = <<<'CPP'
// Bentuk DP eksplisit (setara dengan dua variabel cur/best)
vector<long long> dp(n);
dp[0] = a[0];
for (int i = 1; i < n; i++)
    dp[i] = max(a[i], dp[i - 1] + a[i]);
long long jawab = *max_element(dp.begin(), dp.end());
CPP;

    $kdPrefix = <<<'CPP'
// Sudut pandang lain: jumlah a[l..r] = pre[r+1] - pre[l]
// Untuk setiap r, kurangi dengan prefix TERKECIL sebelumnya
long long pre = 0, minPre = 0, best = LLONG_MIN;
for (int r = 0; r < n; r++) {
    pre += a[r];
    best = max(best, pre - minPre);
    minPre = min(minPre, pre);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mencari subarray berjumlah maksimum dalam O(n) dengan dua variabel <code>cur</code> dan <code>best</code>.</li>
        <li>Merekonstruksi indeks awal dan akhir potongan terbaik.</li>
        <li>Menghindari jebakan inisialisasi nol pada array yang semuanya negatif.</li>
        <li>Menurunkan varian: jual-beli saham, subarray melingkar, dan hapus satu elemen.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'dua pilihan setiap hari', 'desc' => 'Ide Kadane sebagai DP satu dimensi pertama.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Untung Rugi">
    <h2>Intuisi: Catatan Untung Rugi</h2>
    <div class="prose">
        <p>Kamu berjualan setiap hari, kadang untung kadang rugi. Yang dicari: periode <strong>berturut-turut</strong> dengan total untung terbesar.</p>
        <p>Setiap pagi kamu hanya punya dua pilihan: <strong>melanjutkan</strong> periode kemarin, atau <strong>memulai periode baru</strong> hari ini. Jika total periode kemarin sudah minus, membawanya hanya menyeret turun; lebih baik mulai dari hari ini. Rekor untung terbesar dicatat terpisah.</p>
    </div>
    <div class="recurrence"><small>DP Kadane</small>cur[i]  = max( a[i],  cur[i−1] + a[i] )      // mulai baru, atau sambung
best    = max( cur[0], cur[1], …, cur[n−1] )</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Mempersempit pertanyaan dari "periode terbaik sepanjang tahun" menjadi "periode terbaik yang <strong>berakhir hari ini</strong>" adalah inti cara berpikir DP. Pola ini akan muncul berulang kali di track Dynamic Programming.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi Kadane</h2>
    <div class="prose"><p>Baris <code>cur</code> menunjukkan nilai terbaik yang berakhir di setiap indeks. Coba juga pilihan <strong>cur = 0 (salah)</strong> dengan array <code>-3 -1 -4 -2</code>, dan lihat mengapa jawabannya keliru.</p></div>
    <div data-viz="kadane"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>0</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th></tr>
            <tr><td>a[i]</td><td>−2</td><td>1</td><td>−3</td><td>4</td><td>−1</td><td>2</td><td>1</td><td>−5</td><td>4</td></tr>
            <tr class="hl"><td>cur</td><td>−2</td><td>1</td><td>−2</td><td>4</td><td>3</td><td>5</td><td>6</td><td>1</td><td>5</td></tr>
            <tr><td>pilihan</td><td>–</td><td>baru</td><td>sambung</td><td>baru</td><td>sambung</td><td>sambung</td><td>sambung</td><td>sambung</td><td>sambung</td></tr>
            <tr class="ok"><td>best</td><td>−2</td><td>1</td><td>1</td><td>4</td><td>4</td><td>5</td><td><b>6</b></td><td>6</td><td>6</td></tr>
        </table>
    </div>
    <div class="prose"><p>Potongan terbaik a[3..6] = 4 − 1 + 2 + 1 = 6. Perhatikan i = 4: elemennya negatif, tetapi cur (4) masih positif sehingga menyambung tetap lebih baik. Elemen negatif tidak otomatis memutus potongan.</p></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'Kadane di C++', 'desc' => 'Program lengkap dengan rekonstruksi indeks.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Potongan Terbaik</h2>
    @include('lessons.walkthrough', [
        'title' => 'Kadane + indeks potongan',
        'steps' => $kdSteps,
        'sample' => ['input' => "9\n-2 1 -3 4 -1 2 1 -5 4\n", 'output' => "6 4 7\n"],
        'py' => $kdPy,
    ])
    @include('lessons.code', ['cpp' => $kdDp, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th></tr>
        <tr><td>Semua pasangan + loop jumlah</td><td><code>O(n³)</code></td></tr>
        <tr><td>Semua pasangan + prefix sum</td><td><code>O(n²)</code></td></tr>
        <tr><td>Kadane</td><td><code>O(n)</code>, memori <code>O(1)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Inisialisasi nol.</strong> <code>cur = 0; best = 0;</code> dengan reset saat negatif hanya benar jika potongan kosong diizinkan. Pada array <code>[−3, −1, −4]</code> ia menjawab 0, padahal seharusnya −1.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Mencampur cur dan best.</strong> Mencetak <code>cur</code> di akhir memberi potongan terbaik yang berakhir di elemen terakhir, bukan jawaban.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> 2 · 10<sup>5</sup> elemen sebesar 10<sup>9</sup>: jumlahnya 2 · 10<sup>14</sup>. Pakai <code>long long</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'mengapa benar dan varian-variannya', 'desc' => 'Bukti, sudut pandang prefix sum, dan cara menurunkan varian.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Kadane Benar?">
    <h2>Mengapa Kadane Benar?</h2>
    <div class="proof">
        <p>Klaim: setelah memproses indeks i, <code>cur</code> = jumlah terbesar potongan yang berakhir tepat di i. Untuk i = 0 jelas benar. Misalkan benar untuk i − 1. Potongan yang berakhir di i adalah a[i] saja, atau a[j..i] dengan j &lt; i. Kasus kedua bernilai (jumlah a[j..i−1]) + a[i], dan maksimum suku pertama atas semua j adalah cur lama. Jadi cur baru = max(a[i], cur lama + a[i]). Terbukti dengan induksi.</p>
        <p>Setiap potongan berakhir di suatu indeks, jadi potongan terbaik global = max semua cur, yang dicatat oleh <code>best</code>.</p>
    </div>
    <div class="prose"><p>Sudut pandang prefix sum memberi algoritma yang setara dan sering lebih mudah dimodifikasi:</p></div>
    @include('lessons.code', ['cpp' => $kdPrefix, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Jual-beli saham sekali</h4><p>Kadane pada selisih harian, atau "harga − minimum sebelumnya".</p></div>
        <div class="pattern"><h4>Subarray melingkar</h4><p>max(Kadane biasa, total − subarray minimum).</p></div>
        <div class="pattern"><h4>Boleh hapus satu elemen</h4><p>Dua state: belum menghapus / sudah menghapus.</p></div>
        <div class="pattern"><h4>Hasil kali maksimum</h4><p>Simpan cur maksimum dan cur minimum (negatif × negatif).</p></div>
        <div class="pattern"><h4>Submatriks maksimum</h4><p>Tetapkan dua baris, Kadane pada jumlah kolom: O(n²m).</p></div>
        <div class="pattern"><h4>Panjang minimal L</h4><p>Prefix sum dikurangi minimum prefix yang berjarak ≥ L.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="cur = 3 + (−5) = −2 vs a[i] = −5: menyambung lebih baik (−2 > −5). cur = −2.">
        <p class="quiz-q">cur = 3, a[i] = −5. Berapa cur yang baru?</p>
        <div class="quiz-options">
            <button class="quiz-option">−5</button>
            <button class="quiz-option">−2</button>
            <button class="quiz-option">0</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Potongan tidak boleh kosong, jadi jawabannya elemen terbesar: −1. Versi yang dimulai dari 0 salah menjawab 0.">
        <p class="quiz-q">Berapa jawaban untuk a = [−3, −1, −4]?</p>
        <div class="quiz-options">
            <button class="quiz-option">0</button>
            <button class="quiz-option">−8</button>
            <button class="quiz-option">−1</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="cur hanya bergantung pada cur sebelumnya dan a[i], persis DP satu dimensi dengan memori satu variabel.">
        <p class="quiz-q">Kadane paling tepat digolongkan sebagai…</p>
        <div class="quiz-options">
            <button class="quiz-option">DP satu dimensi</button>
            <button class="quiz-option">Greedy tanpa bukti</button>
            <button class="quiz-option">Binary search</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
