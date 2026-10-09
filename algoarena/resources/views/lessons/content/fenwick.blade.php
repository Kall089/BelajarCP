@php
    $fwSteps = [
        ['Array t dan dua fungsi inti', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> t;

void tambah(int i, long long v) {
    for (; i <= n; i += i & (-i)) t[i] += v;
}

long long prefix(int i) {
    long long s = 0;
    for (; i > 0; i -= i & (-i)) s += t[i];
    return s;
}

CPP, <<<'TXT'
<p><code>i &amp; (−i)</code> adalah <strong>lowbit(i)</strong>, nilai bit 1 terendah dari i (misalnya 12 = 1100 → 4). <code>t[i]</code> menyimpan jumlah a[i − lowbit(i) + 1 .. i].</p>
<ul>
    <li><code>prefix(i)</code>: kumpulkan t[i], lalu lompat ke <code>i − lowbit(i)</code> (bit 1 terendah dihapus) sampai 0. Setiap lompatan menghapus satu bit, jadi paling banyak log₂ N langkah.</li>
    <li><code>tambah(i, v)</code>: perbarui semua t[j] yang rentangnya memuat i, yaitu i, i + lowbit(i), … sampai melewati n.</li>
</ul>
<div class="wt-tip">Indeks harus mulai dari 1. Dengan i = 0, <code>0 &amp; −0 = 0</code> dan loop <code>tambah</code> tidak pernah berhenti.</div>
TXT],
        ['Membangun dari array awal', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> n >> q;
    t.assign(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        long long x;
        cin >> x;
        tambah(i, x);
    }

CPP, <<<'TXT'
<p>Cara termudah membangun: mulai dari semua nol, lalu <code>tambah(i, a[i])</code> untuk setiap i. Totalnya O(N log N), cukup cepat untuk N = 2 · 10<sup>5</sup>. (Ada juga cara O(N), lihat bagian jebakan.)</p>
TXT],
        ['Menjawab operasi', <<<'CPP'
    string out;
    while (q--) {
        int jenis;
        cin >> jenis;
        if (jenis == 1) {
            int i;
            long long v;
            cin >> i >> v;
            tambah(i, v);
        } else {
            int l, r;
            cin >> l >> r;
            out += to_string(prefix(r) - prefix(l - 1)) + "\n";
        }
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Operasi <code>1 i v</code> menambah v ke a[i]; operasi <code>2 l r</code> menanyakan jumlah a[l..r] = <code>prefix(r) − prefix(l − 1)</code>, seperti prefix sum biasa. Bedanya, sekarang perubahan boleh terjadi di tengah-tengah pertanyaan, dan keduanya tetap O(log N).</p>
TXT],
    ];

    $fwPy = <<<'PY'
import sys
data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
t = [0] * (n + 1)

def tambah(i, v):
    while i <= n:
        t[i] += v
        i += i & -i

def prefix(i):
    s = 0
    while i > 0:
        s += t[i]
        i -= i & -i
    return s

for i in range(1, n + 1):
    tambah(i, int(data[1 + i]))
p = 2 + n
out = []
for _ in range(q):
    if data[p] == b"1":
        tambah(int(data[p + 1]), int(data[p + 2]))
    else:
        out.append(prefix(int(data[p + 2])) - prefix(int(data[p + 1]) - 1))
    p += 3
print("\n".join(map(str, out)))
PY;

    $fwInv = <<<'CPP'
// Banyak inversi: pasangan i < j dengan a[i] > a[j].
// Proses dari kiri; untuk a[i], hitung berapa elemen SEBELUMNYA yang lebih besar.
// Nilai sampai 10^9 dikompresi dulu menjadi peringkat 1..K agar bisa menjadi indeks BIT.
vector<long long> urut = a;
sort(urut.begin(), urut.end());
urut.erase(unique(urut.begin(), urut.end()), urut.end());
BIT bit(urut.size());                         // bit menghitung banyak nilai per peringkat
long long inversi = 0;
for (int i = 0; i < n; i++) {
    int k = lower_bound(urut.begin(), urut.end(), a[i]) - urut.begin() + 1;   // peringkat a[i]
    inversi += i - bit.prefix(k);             // sebelumnya ada i elemen; yang ≤ a[i] = prefix(k)
    bit.tambah(k, 1);
}
// a = 50 10 40 10 30  ->  6 inversi
CPP;

    $fwRange = <<<'CPP'
// Tambah v ke a[l..r] DAN tanya jumlah a[l..r], keduanya O(log N): dua BIT.
// Misal d = array selisih. a[1] + ... + a[i] = Σ_{j≤i} d[j] · (i − j + 1)
//                                          = i · Σ d[j]  −  Σ d[j] · (j − 1)
// B1 menyimpan d[j], B2 menyimpan d[j] · (j − 1).
void tambahRentang(int l, int r, long long v) {
    B1.tambah(l, v);             B1.tambah(r + 1, -v);
    B2.tambah(l, v * (l - 1));   B2.tambah(r + 1, -v * r);
}
long long prefixSum(int i) { return B1.prefix(i) * i - B2.prefix(i); }
// jumlah a[l..r] = prefixSum(r) − prefixSum(l − 1)
CPP;

    $fwKth = <<<'CPP'
// BIT menghitung "ada atau tidak" setiap nilai 1..n. Cari nilai terkecil ke-k dalam O(log N)
// dengan menurunkan "lompatan" dari pangkat dua terbesar (binary lifting di dalam BIT).
int keK(long long k) {
    int pos = 0;
    for (int p = 1 << LOG; p > 0; p >>= 1)        // 2^LOG ≥ n
        if (pos + p <= n && t[pos + p] < k) {     // t[pos+p] = jumlah a[pos+1 .. pos+p]
            pos += p;
            k -= t[pos];
        }
    return pos + 1;                               // posisi pertama dengan prefix ≥ k
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan mengapa prefix sum biasa lambat jika array berubah-ubah.</li>
        <li>Memahami <code>lowbit(i) = i &amp; (−i)</code> dan rentang yang disimpan setiap <code>t[i]</code>.</li>
        <li>Menulis Fenwick tree untuk update titik dan jumlah rentang, masing-masing O(log N).</li>
        <li>Memakai BIT untuk menghitung inversi, update rentang, dan mencari elemen ke-k.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'ide pembagian berdasarkan bit', 'desc' => 'Rentang yang disimpan setiap t[i] dan dua cara melompat.'])

<section class="lesson-section" id="masalah" data-toc="Masalahnya">
    <h2>Masalahnya: Array yang Berubah-ubah</h2>
    <div class="prose">
        <p>Kasir toko ingin tahu total penjualan dari rak l sampai rak r, tetapi stok rak terus berubah. Dengan prefix sum, pertanyaan O(1), tetapi satu perubahan a[i] mengubah <code>pre[i..N]</code>: O(N). Tanpa prefix sum, perubahan O(1) tetapi pertanyaan O(N). Dengan 2 · 10<sup>5</sup> operasi, keduanya terlalu lambat.</p>
        <p>Fenwick tree (disebut juga <strong>Binary Indexed Tree</strong>) mengambil jalan tengah: keduanya O(log N), dan kodenya hanya beberapa baris.</p>
    </div>
</section>

<section class="lesson-section" id="ide" data-toc="Ide lowbit">
    <h2>Setiap t[i] Menyimpan Sepotong Rentang</h2>
    <div class="prose">
        <p>Tulis i dalam biner. <strong>lowbit(i)</strong> adalah nilai bit 1 paling kanan: lowbit(6 = 110₂) = 2, lowbit(8 = 1000₂) = 8, lowbit(ganjil) = 1. Fenwick tree menyimpan</p>
        <p style="text-align:center"><code>t[i] = a[i − lowbit(i) + 1] + … + a[i]</code></p>
        <p>Di komputer, bilangan negatif disimpan dalam bentuk komplemen dua, sehingga <code>i &amp; (−i)</code> menghasilkan tepat bit terendah itu.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th></tr>
            <tr><td>biner</td><td>0001</td><td>0010</td><td>0011</td><td>0100</td><td>0101</td><td>0110</td><td>0111</td><td>1000</td></tr>
            <tr><td>lowbit</td><td>1</td><td>2</td><td>1</td><td>4</td><td>1</td><td>2</td><td>1</td><td>8</td></tr>
            <tr><td>t[i] mencakup</td><td>1</td><td>1..2</td><td>3</td><td>1..4</td><td>5</td><td>5..6</td><td>7</td><td>1..8</td></tr>
        </table>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Query</strong> prefix(i): ambil t[i], lalu <code>i −= lowbit(i)</code>; potongan-potongannya bersambung tanpa tumpang tindih. <strong>Update</strong> a[i]: perbarui t[i], lalu <code>i += lowbit(i)</code>; setiap lompatan naik ke potongan lebih besar yang juga memuat i. Keduanya paling banyak log₂ N + 1 langkah.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Setiap kotak di atas adalah satu <code>t[i]</code> yang membentang di atas elemen-elemen yang dicakupnya. Perhatikan pola biner: query menghapus bit terendah (berjalan ke kiri), update menambah bit terendah (berjalan ke atas).</p></div>
    <div data-viz="fenwick"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Indeks 1..N</b><span>Geser input yang 0-based menjadi 1-based.</span></div>
        <div class="step-card"><b>Bangun</b><span><code>tambah(i, a[i])</code> untuk setiap i.</span></div>
        <div class="step-card"><b>Update</b><span>Tambah v: <code>tambah(i, v)</code>. Ganti nilai: tambah (x − a[i]) dan simpan a[i] baru.</span></div>
        <div class="step-card"><b>Query</b><span><code>prefix(r) − prefix(l − 1)</code>.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Update titik dan jumlah rentang yang bercampur.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Fenwick tree: tambah titik, jumlah rentang',
        'steps' => $fwSteps,
        'sample' => ['input' => "8 5\n3 1 4 1 5 9 2 6\n2 1 7\n2 3 6\n1 3 5\n2 1 7\n2 3 3\n", 'output' => "25\n19\n30\n9\n"],
        'py' => $fwPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>a = 3 1 4 1 5 9 2 6, sehingga t = 3, 4, 4, 9, 5, 14, 2, 31.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Operasi</th><th>Indeks yang dikunjungi (biner)</th><th>Hasil</th></tr>
            <tr><td>prefix(7)</td><td class="q">7 (0111) → 6 (0110) → 4 (0100) → 0</td><td>t[7] + t[6] + t[4] = 2 + 14 + 9 = 25</td></tr>
            <tr><td>prefix(2)</td><td class="q">2 (0010) → 0</td><td>t[2] = 4, jadi jumlah 3..6 = prefix(6) − 4 = 23 − 4 = 19</td></tr>
            <tr class="hl"><td>tambah(3, 5)</td><td class="q">3 (0011) → 4 (0100) → 8 (1000) → 16 berhenti</td><td>t[3] = 9, t[4] = 14, t[8] = 36</td></tr>
            <tr class="ok"><td>prefix(7)</td><td class="q">7 → 6 → 4 → 0</td><td>2 + 14 + 14 = 30</td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Struktur</th><th>Update titik</th><th>Jumlah rentang</th><th>Memori</th></tr>
        <tr><td>Array biasa</td><td><code>O(1)</code></td><td><code>O(N)</code></td><td><code>N</code></td></tr>
        <tr><td>Prefix sum</td><td><code>O(N)</code></td><td><code>O(1)</code></td><td><code>N</code></td></tr>
        <tr><td>Fenwick tree</td><td><code>O(log N)</code></td><td><code>O(log N)</code></td><td><code>N</code></td></tr>
        <tr><td>Segment tree</td><td><code>O(log N)</code></td><td><code>O(log N)</code></td><td><code>2N–4N</code>, tapi lebih fleksibel (min, max, …)</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Indeks 0.</strong> lowbit(0) = 0, jadi <code>tambah(0, v)</code> berputar selamanya. Pastikan indeks 1..N.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Mengganti nilai</strong> (bukan menambah): BIT hanya tahu "tambah". Simpan array a terpisah, lalu <code>tambah(i, x − a[i]); a[i] = x;</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>BIT hanya untuk operasi yang bisa "dikurangkan".</strong> Jumlah rentang = prefix(r) − prefix(l − 1) butuh invers (pengurangan). Minimum rentang tidak punya invers, jadi untuk min/max rentang dengan update gunakan segment tree.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'penerapan lanjutan', 'desc' => 'Inversi, update rentang, dan mencari elemen ke-k.'])

<section class="lesson-section" id="inversi" data-toc="Menghitung Inversi">
    <h2>Menghitung Inversi</h2>
    <div class="prose">
        <p>BIT tidak hanya untuk "jumlah nilai", tetapi juga untuk "jumlah <em>kemunculan</em>". Jika BIT diindeks berdasarkan nilai, <code>prefix(k)</code> menjawab "sudah berapa nilai ≤ k yang muncul?". Inilah dasar soal inversi, peringkat, dan "berapa yang lebih kecil sebelum aku".</p>
    </div>
    @include('lessons.code', ['cpp' => $fwInv, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="rentang" data-toc="Update Rentang">
    <h2>Update Rentang</h2>
    <div class="prose">
        <p><strong>Update rentang, query titik:</strong> pasang BIT pada array selisih. Menambah v ke l..r = <code>tambah(l, v)</code> dan <code>tambah(r + 1, −v)</code>; nilai a[i] = <code>prefix(i)</code>.</p>
        <p><strong>Update rentang, query rentang:</strong> butuh dua BIT, dari penguraian aljabar jumlah prefix array selisih:</p>
    </div>
    @include('lessons.code', ['cpp' => $fwRange, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kek" data-toc="Elemen ke-k">
    <h2>Mencari Elemen ke-k</h2>
    <div class="prose">
        <p>Jika BIT menyimpan banyak kemunculan setiap nilai, "nilai terkecil ke-k" adalah posisi pertama dengan prefix ≥ k. Binary search biasa memanggil prefix O(log N) kali, total O(log² N). Struktur BIT memungkinkan O(log N): turunkan lompatan dari pangkat dua terbesar, karena <code>t[pos + p]</code> tepat mencakup a[pos + 1 .. pos + p] selama pos kelipatan 2p.</p>
    </div>
    @include('lessons.code', ['cpp' => $fwKth, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Jumlah dinamis</h4><p>Update titik + jumlah rentang: BIT dasar.</p></div>
        <div class="pattern"><h4>Inversi / peringkat</h4><p>BIT per nilai + kompresi koordinat.</p></div>
        <div class="pattern"><h4>Tambah rentang</h4><p>BIT di atas array selisih; dua BIT untuk jumlah rentang.</p></div>
        <div class="pattern"><h4>Median dinamis</h4><p>Elemen ke-k dengan binary lifting.</p></div>
        <div class="pattern"><h4>Grid 2D</h4><p>BIT 2D: <code>for i … for j …</code>, O(log² N) per operasi.</p></div>
        <div class="pattern"><h4>Offline</h4><p>Urutkan pertanyaan, isi BIT bertahap (misalnya "berapa nilai &lt; x di l..r").</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="12 = 1100 dalam biner, bit 1 terendahnya bernilai 4, jadi t[12] mencakup a[9..12].">
        <p class="quiz-q"><code>t[12]</code> menyimpan jumlah …</p>
        <div class="quiz-options">
            <button class="quiz-option">a[1..12]</button>
            <button class="quiz-option">a[11..12]</button>
            <button class="quiz-option">a[9..12]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="prefix(13): 13 (1101) → 12 (1100) → 8 (1000) → 0. Tiga simpul: t[13], t[12], t[8].">
        <p class="quiz-q">Simpul mana saja yang dijumlahkan oleh <code>prefix(13)</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">t[13], t[12], t[8]</button>
            <button class="quiz-option">t[13], t[14], t[16]</button>
            <button class="quiz-option">t[1] sampai t[13]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Minimum tidak bisa dikurangkan, sehingga min(l..r) tidak bisa didapat dari dua prefix. Gunakan segment tree.">
        <p class="quiz-q">Mengapa BIT biasa tidak dipakai untuk minimum rentang dengan update?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena lebih lambat dari O(log N)</button>
            <button class="quiz-option">Karena min(l..r) tidak bisa diperoleh dengan "mengurangkan" dua prefix</button>
            <button class="quiz-option">Karena BIT tidak bisa menyimpan bilangan negatif</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
