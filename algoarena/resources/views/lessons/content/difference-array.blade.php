@php
    $daSteps = [
        ['Waktu sebagai indeks', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int MAXT = 1000000;
int d[MAXT + 2];          // global: terisi nol otomatis
CPP, <<<'TXT'
<p><strong>Soal contoh "Ruang Rapat":</strong> ada n rapat; rapat ke-i memakai ruangan dari menit <code>s<sub>i</sub></code> sampai sebelum menit <code>e<sub>i</sub></code> (interval setengah terbuka [s, e)). Paling sedikit berapa ruangan yang dibutuhkan, dan pada menit berapa (paling awal) kesibukan itu terjadi?</p>
<p>Menit hanya sampai 10<sup>6</sup>, jadi waktu bisa langsung dijadikan indeks array.</p>
TXT],
        ['Setiap rapat = dua sentuhan', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    for (int i = 0; i < n; i++) {
        int s, e;
        cin >> s >> e;
        d[s]++;            // mulai memakai ruangan di menit s
        d[e]--;            // berhenti tepat di menit e
    }
CPP, <<<'TXT'
<p>Menambahkan 1 pada setiap menit di [s, e) dengan loop bisa butuh 10<sup>6</sup> langkah per rapat. Difference array hanya menulis <strong>dua catatan</strong>: "+1 mulai menit s" dan "−1 mulai menit e".</p>
<p>Karena intervalnya setengah terbuka, pengurangannya tepat di <code>e</code> (bukan e + 1). Rapat yang selesai di menit 10 dan rapat yang mulai di menit 10 tidak bertabrakan.</p>
TXT],
        ['Prefix sum mengembalikan nilai asli', <<<'CPP'

    int sibuk = 0, terbanyak = 0, kapan = 0;
    for (int t = 0; t <= MAXT; t++) {
        sibuk += d[t];                 // banyak rapat yang berjalan di menit t
        if (sibuk > terbanyak) {
            terbanyak = sibuk;
            kapan = t;
        }
    }
CPP, <<<'TXT'
<p>Jumlah berjalan dari d adalah banyak rapat yang sedang berlangsung pada menit t. Tanda <code>&gt;</code> (bukan ≥) menjaga menit <em>paling awal</em> saat rekor tercapai.</p>
TXT],
        ['Jawaban', <<<'CPP'

    cout << terbanyak << " " << kapan << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Total O(n + T), dengan T = rentang waktu. Jika waktu bisa sampai 10<sup>9</sup>, array sebesar itu tidak mungkin: gunakan kompresi koordinat atau urutkan kejadian (sweep line).</p>
TXT],
    ];

    $daPy = <<<'PY'
import sys
data = sys.stdin.buffer.read().split()
n = int(data[0])
MAXT = 10**6
d = [0] * (MAXT + 2)
for i in range(n):
    s, e = int(data[1 + 2*i]), int(data[2 + 2*i])
    d[s] += 1
    d[e] -= 1
sibuk = terbanyak = kapan = 0
for t in range(MAXT + 1):
    sibuk += d[t]
    if sibuk > terbanyak:
        terbanyak, kapan = sibuk, t
print(terbanyak, kapan)
PY;

    $da1d = <<<'CPP'
vector<long long> d(n + 1, 0);           // satu sel cadangan untuk r + 1
// update: tambah v ke a[l..r]
d[l] += v;
d[r + 1] -= v;
// setelah SEMUA update selesai
long long berjalan = 0;
for (int i = 0; i < n; i++) {
    berjalan += d[i];
    a[i] += berjalan;
}
CPP;

    $da2d = <<<'CPP'
// Difference 2D: tambah v ke persegi (r1, c1) .. (r2, c2)
D[r1][c1]         += v;
D[r1][c2 + 1]     -= v;
D[r2 + 1][c1]     -= v;
D[r2 + 1][c2 + 1] += v;
// lalu prefix sum 2D atas D memberi nilai setiap sel
for (int i = 0; i < R; i++)
    for (int j = 0; j < C; j++) {
        if (i) D[i][j] += D[i - 1][j];
        if (j) D[i][j] += D[i][j - 1];
        if (i && j) D[i][j] -= D[i - 1][j - 1];
    }
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menambah nilai pada rentang dalam O(1) dengan dua sentuhan <code>d[l] += v</code>, <code>d[r+1] −= v</code>.</li>
        <li>Mengembalikan array akhir dengan prefix sum dan menjelaskan mengapa hasilnya benar.</li>
        <li>Menghitung "berapa interval aktif pada setiap titik" untuk soal jadwal dan tumpang tindih.</li>
        <li>Memperluas teknik ini ke dua dimensi untuk update persegi panjang.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'kebalikan prefix sum', 'desc' => 'Mencatat di mana perubahan mulai dan berhenti.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Catatan Guru">
    <h2>Intuisi: Catatan "Mulai" dan "Berhenti"</h2>
    <div class="prose">
        <p>Guru ingin menambah nilai 5 untuk semua siswa bernomor 10 sampai 30. Daripada mencoret 21 nama satu per satu, ia menulis dua catatan: <strong>"+5 mulai nomor 10"</strong> dan <strong>"−5 mulai nomor 31"</strong>. Di akhir, ia membaca daftar dari atas sambil menjumlahkan catatan: setiap siswa mendapat tambahan sebesar jumlah catatan yang sudah dilewati.</p>
        <p>Prefix sum mempercepat <em>pembacaan</em> rentang. Difference array adalah kebalikannya: mempercepat <em>penambahan</em> pada rentang. Hasilnya baru dibaca sekali di akhir.</p>
    </div>
    <div class="recurrence"><small>Rumus</small>update (l, r, v):   d[l] += v,  d[r + 1] −= v        O(1)
nilai akhir:        a[i] = d[0] + d[1] + … + d[i]   (prefix sum, O(n) sekali)</div>
    @include('lessons.code', ['cpp' => $da1d, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Empat Update, Satu Kali Baca</h2>
    <div class="prose"><p>Perhatikan bahwa selama fase update, array a tidak pernah disentuh. Semua informasi tersimpan di d sebagai titik "mulai" dan "berhenti".</p></div>
    <div data-viz="diff-array"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Langkah</th><th>d[0..5]</th><th>Keterangan</th></tr>
            <tr><td>awal</td><td>0 0 0 0 0 0</td><td>n = 5, satu sel cadangan</td></tr>
            <tr class="hl"><td>+2 di [1, 3]</td><td>0 <b>2</b> 0 0 <b>−2</b> 0</td><td>dua sentuhan</td></tr>
            <tr class="hl"><td>+4 di [0, 1]</td><td><b>4</b> 2 <b>−4</b> 0 −2 0</td><td>dua sentuhan</td></tr>
            <tr class="ok"><td>prefix</td><td>a = 4 6 2 2 0</td><td>4, 4+2, 6−4, 2+0, 2−2</td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'difference array di C++', 'desc' => 'Menghitung kesibukan dari banyak interval.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Ruang Rapat</h2>
    @include('lessons.walkthrough', [
        'title' => 'Banyak interval aktif di setiap menit',
        'steps' => $daSteps,
        'sample' => ['input' => "4\n1 5\n2 6\n5 9\n3 4\n", 'output' => "3 3\n"],
        'py' => $daPy,
    ])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Satu update</th><th>Q update + baca semua</th></tr>
        <tr><td>Loop polos</td><td><code>O(r − l + 1)</code></td><td><code>O(n · Q)</code></td></tr>
        <tr><td>Difference array</td><td><code>O(1)</code></td><td><code>O(n + Q)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Ukuran d.</strong> <code>d[r + 1]</code> bisa menyentuh indeks n. Siapkan n + 1 sel, atau periksa <code>if (r + 1 &lt; n)</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Interval tertutup atau setengah terbuka?</strong> Untuk [l, r] pengurangannya di r + 1; untuk [s, e) di e. Salah satu langkah membuat jawaban meleset satu.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Query di tengah update.</strong> Difference array hanya bisa dibaca setelah semua update selesai. Jika query dan update saling berselang, butuh Fenwick tree (range update, point query).</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'dua dimensi dan sweep', 'desc' => 'Update persegi panjang dan hubungannya dengan sweep line.'])

<section class="lesson-section" id="dua-dimensi" data-toc="Difference 2D">
    <h2>Difference Array 2D</h2>
    <div class="prose"><p>Untuk menambah v pada persegi panjang, tulis empat catatan di sudut-sudutnya. Prefix sum 2D di akhir menyebarkan catatan itu tepat ke dalam persegi.</p></div>
    @include('lessons.code', ['cpp' => $da2d, 'js' => null, 'py' => null])
    <div class="proof">
        <p>Mengapa benar? Prefix sum dari d di posisi i adalah jumlah semua catatan di indeks ≤ i. Untuk satu update (l, r, v): jika i &lt; l, tidak ada catatannya yang terhitung (0). Jika l ≤ i ≤ r, hanya <code>+v</code> yang terhitung (v). Jika i &gt; r, keduanya terhitung (v − v = 0). Jadi setiap update menyumbang v tepat di [l, r], dan karena penjumlahan bersifat linear, update-update itu saling bertumpuk dengan benar.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Tambah nilai pada rentang</h4><p>Dua sentuhan, baca dengan prefix.</p></div>
        <div class="pattern"><h4>Interval paling ramai</h4><p>+1 di awal, −1 di akhir, cari prefix maksimum.</p></div>
        <div class="pattern"><h4>Sel yang tertutup ≥ K kali</h4><p>Difference 2D lalu hitung.</p></div>
        <div class="pattern"><h4>Waktu sampai 10<sup>9</sup></h4><p>Kompresi koordinat atau urutkan kejadian.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="d[2] += 3 dan d[5] −= 3. Indeks r + 1 = 5.">
        <p class="quiz-q">Untuk menambah 3 pada a[2..4] (tertutup), sel mana yang diubah?</p>
        <div class="quiz-options">
            <button class="quiz-option">d[2] += 3, d[5] −= 3</button>
            <button class="quiz-option">d[2] += 3, d[4] −= 3</button>
            <button class="quiz-option">d[2..4] += 3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Prefix dari [1, 0, −1, 2, −2] adalah 1, 1, 0, 2, 0.">
        <p class="quiz-q">d = [1, 0, −1, 2, −2]. Berapa a[3]?</p>
        <div class="quiz-options">
            <button class="quiz-option">0</button>
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Difference array baru bisa dibaca setelah semua update selesai. Jika query dan update berselang-seling, perlu struktur dinamis seperti Fenwick tree.">
        <p class="quiz-q">Kapan difference array <strong>tidak</strong> cukup?</p>
        <div class="quiz-options">
            <button class="quiz-option">Update rentang dengan nilai negatif</button>
            <button class="quiz-option">Query nilai a[i] di sela-sela update</button>
            <button class="quiz-option">Interval yang saling tumpang tindih</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
