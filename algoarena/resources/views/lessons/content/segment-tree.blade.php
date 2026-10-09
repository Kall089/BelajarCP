@php
    $sgSteps = [
        ['Array pohon dan membangunnya', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX;
int n;
vector<long long> a, t;

void bangun(int v, int tl, int tr) {
    if (tl == tr) {
        t[v] = a[tl];
        return;
    }
    int tm = (tl + tr) / 2;
    bangun(2 * v, tl, tm);
    bangun(2 * v + 1, tm + 1, tr);
    t[v] = min(t[2 * v], t[2 * v + 1]);
}

CPP, <<<'TXT'
<p>Simpul nomor <code>v</code> mewakili rentang <code>[tl, tr]</code> dan menyimpan minimum rentang itu. Akarnya simpul 1 dengan rentang [1, n]. Anak kiri simpul v adalah <code>2v</code> (rentang [tl, tm]) dan anak kanannya <code>2v + 1</code> (rentang [tm + 1, tr]), dengan tm titik tengah.</p>
<p>Daun (tl = tr) berisi a[tl]. Simpul lain diisi dari kedua anaknya, jadi bangun dari bawah ke atas: rekursi dulu, gabungkan setelahnya. Totalnya O(N).</p>
<div class="wt-tip">Array <code>t</code> perlu berukuran <strong>4n</strong>. Jika n bukan pangkat dua, pohonnya tidak penuh dan nomor simpul bisa melebihi 2n.</div>
TXT],
        ['Query: tiga kemungkinan', <<<'CPP'
long long query(int v, int tl, int tr, int l, int r) {
    if (r < tl || tr < l) return INF;
    if (l <= tl && tr <= r) return t[v];
    int tm = (tl + tr) / 2;
    return min(query(2 * v, tl, tm, l, r), query(2 * v + 1, tm + 1, tr, l, r));
}

CPP, <<<'TXT'
<p>Rentang simpul [tl, tr] dibandingkan dengan rentang yang ditanya [l, r]:</p>
<ul>
    <li><strong>Di luar</strong> (tidak beririsan): kembalikan nilai netral, yaitu +∞ untuk minimum (0 untuk jumlah).</li>
    <li><strong>Di dalam</strong> sepenuhnya: nilai simpul sudah merangkum semuanya, kembalikan <code>t[v]</code>.</li>
    <li><strong>Sebagian</strong>: tanya kedua anak dan gabungkan.</li>
</ul>
<p>Di setiap level paling banyak dua simpul yang "sebagian", sehingga total simpul yang dikunjungi O(log N).</p>
TXT],
        ['Update titik', <<<'CPP'
void ubah(int v, int tl, int tr, int pos, long long x) {
    if (tl == tr) {
        t[v] = x;
        return;
    }
    int tm = (tl + tr) / 2;
    if (pos <= tm) ubah(2 * v, tl, tm, pos, x);
    else ubah(2 * v + 1, tm + 1, tr, pos, x);
    t[v] = min(t[2 * v], t[2 * v + 1]);
}

CPP, <<<'TXT'
<p>Turun ke daun yang memuat <code>pos</code>, ganti nilainya, lalu dalam perjalanan pulang hitung ulang setiap simpul di jalur itu. Hanya simpul-simpul di jalur akar–daun yang rentangnya memuat pos, jadi hanya mereka yang bisa berubah: O(log N).</p>
TXT],
        ['Program utama', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> n >> q;
    a.assign(n + 1, 0);
    for (int i = 1; i <= n; i++) cin >> a[i];
    t.assign(4 * n, 0);
    bangun(1, 1, n);

    string out;
    while (q--) {
        int jenis;
        cin >> jenis;
        if (jenis == 1) {
            int i;
            long long x;
            cin >> i >> x;
            ubah(1, 1, n, i, x);
        } else {
            int l, r;
            cin >> l >> r;
            out += to_string(query(1, 1, n, l, r)) + "\n";
        }
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Operasi <code>1 i x</code> mengganti a[i] menjadi x; operasi <code>2 l r</code> menanyakan minimum a[l..r]. Semua dipanggil dari akar: <code>(1, 1, n)</code>.</p>
TXT],
    ];

    $sgPy = <<<'PY'
import sys
data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
# Versi iteratif (lebih cepat di Python): daun di t[n..2n-1], indeks 0-based.
t = [0] * (2 * n)
for i in range(n):
    t[n + i] = int(data[2 + i])
for i in range(n - 1, 0, -1):
    t[i] = min(t[2 * i], t[2 * i + 1])
p = 2 + n
out = []
for _ in range(q):
    jenis, x, y = data[p], int(data[p + 1]), int(data[p + 2])
    p += 3
    if jenis == b"1":
        i = x - 1 + n
        t[i] = y
        while i > 1:
            i >>= 1
            t[i] = min(t[2 * i], t[2 * i + 1])
    else:
        l, r = x - 1 + n, y + n          # [l, r) dalam indeks daun
        res = float("inf")
        while l < r:
            if l & 1:
                res = min(res, t[l]); l += 1
            if r & 1:
                r -= 1; res = min(res, t[r])
            l >>= 1; r >>= 1
        out.append(res)
print("\n".join(map(str, out)))
PY;

    $sgIter = <<<'CPP'
// Segment tree iteratif (bottom-up): daun di t[n..2n-1], indeks 0-based, rentang setengah terbuka [l, r).
// Lebih pendek dan lebih cepat, tetapi hanya cocok untuk operasi gabung yang asosiatif.
vector<long long> t(2 * n);
for (int i = 0; i < n; i++) t[n + i] = a[i];
for (int i = n - 1; i >= 1; i--) t[i] = min(t[2 * i], t[2 * i + 1]);

void ubah(int p, long long x) {
    for (t[p += n] = x; p > 1; p >>= 1) t[p >> 1] = min(t[p], t[p ^ 1]);
}
long long query(int l, int r) {           // minimum a[l..r-1]
    long long res = LLONG_MAX;
    for (l += n, r += n; l < r; l >>= 1, r >>= 1) {
        if (l & 1) res = min(res, t[l++]);
        if (r & 1) res = min(res, t[--r]);
    }
    return res;
}
CPP;

    $sgInfo = <<<'CPP'
// Subarray (tidak kosong) berjumlah maksimum di dalam [l, r], dengan update.
// Setiap simpul menyimpan 4 nilai, lalu dua simpul digabung:
struct Info {
    long long total;   // jumlah seluruh rentang
    long long pre;     // prefiks terbaik
    long long suf;     // sufiks terbaik
    long long best;    // subarray terbaik di dalam rentang
};
Info gabung(const Info& A, const Info& B) {
    Info C;
    C.total = A.total + B.total;
    C.pre = max(A.pre, A.total + B.pre);          // prefiks: di A saja, atau seluruh A + prefiks B
    C.suf = max(B.suf, B.total + A.suf);
    C.best = max(max(A.best, B.best), A.suf + B.pre);   // subarray terbaik boleh melewati batas tengah
    return C;
}
// daun bernilai x: Info{x, x, x, x}. Query mengembalikan Info lalu ambil .best
CPP;

    $sgLazy = <<<'CPP'
// Lazy propagation: tambah x ke a[l..r] dan tanya jumlah a[l..r], keduanya O(log N).
// lz[v] = tambahan yang sudah diterapkan ke sum[v] tetapi BELUM diteruskan ke anak-anaknya.
void dorong(int v, int tl, int tr) {
    if (lz[v] != 0) {
        int tm = (tl + tr) / 2;
        sum[2 * v] += lz[v] * (tm - tl + 1);   lz[2 * v] += lz[v];
        sum[2 * v + 1] += lz[v] * (tr - tm);   lz[2 * v + 1] += lz[v];
        lz[v] = 0;
    }
}
void tambah(int v, int tl, int tr, int l, int r, long long x) {
    if (r < tl || tr < l) return;
    if (l <= tl && tr <= r) {                  // seluruh rentang: catat saja, jangan turun
        sum[v] += x * (tr - tl + 1);
        lz[v] += x;
        return;
    }
    dorong(v, tl, tr);                         // sebelum turun, teruskan utang ke anak
    int tm = (tl + tr) / 2;
    tambah(2 * v, tl, tm, l, r, x);
    tambah(2 * v + 1, tm + 1, tr, l, r, x);
    sum[v] = sum[2 * v] + sum[2 * v + 1];
}
long long jumlah(int v, int tl, int tr, int l, int r) {
    if (r < tl || tr < l) return 0;
    if (l <= tl && tr <= r) return sum[v];
    dorong(v, tl, tr);
    int tm = (tl + tr) / 2;
    return jumlah(2 * v, tl, tm, l, r) + jumlah(2 * v + 1, tm + 1, tr, l, r);
}
CPP;

    $sgSparse = <<<'CPP'
// Sparse table: minimum rentang pada array TETAP. Persiapan O(N log N), query O(1).
// sp[k][i] = min a[i .. i + 2^k - 1]
for (int i = 0; i < n; i++) sp[0][i] = a[i];
for (int k = 1; (1 << k) <= n; k++)
    for (int i = 0; i + (1 << k) <= n; i++)
        sp[k][i] = min(sp[k - 1][i], sp[k - 1][i + (1 << (k - 1))]);

int rmq(int l, int r) {                         // min a[l..r], 0-based
    int k = 31 - __builtin_clz(r - l + 1);      // 2^k terbesar yang ≤ panjang rentang
    return min(sp[k][l], sp[k][r - (1 << k) + 1]);   // dua potongan boleh tumpang tindih
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan struktur segment tree: setiap simpul merangkum satu rentang.</li>
        <li>Menulis build, query rentang, dan update titik, masing-masing O(N) dan O(log N).</li>
        <li>Merancang informasi kustom di simpul, misalnya subarray berjumlah maksimum.</li>
        <li>Memakai <strong>lazy propagation</strong> untuk update rentang, dan <strong>sparse table</strong> untuk array statis.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'membagi rentang menjadi dua', 'desc' => 'Struktur pohon, query, dan update.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Laporan Bertingkat</h2>
    <div class="prose">
        <p>Bayangkan perusahaan dengan 8 cabang. Setiap pasangan cabang punya manajer area yang tahu suhu gudang terendah di dua cabangnya, setiap dua manajer area punya manajer wilayah, dan seterusnya sampai direktur. Untuk tahu suhu terendah cabang 2 sampai 6, direktur tidak perlu menelepon kelima cabang: cukup bertanya pada beberapa manajer yang wilayahnya pas di dalam rentang itu.</p>
        <p>Itulah segment tree. Berbeda dengan Fenwick tree, operasi gabungnya boleh apa saja yang <strong>asosiatif</strong>: minimum, maksimum, FPB, jumlah, XOR, bahkan struktur buatan sendiri.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Simpul</b><span>Mewakili rentang [tl, tr] dan menyimpan ringkasannya (misalnya minimum).</span></div>
        <div class="term"><b>Anak</b><span>Simpul v dibelah di tengah: anak kiri 2v, anak kanan 2v + 1.</span></div>
        <div class="term"><b>Nilai netral</b><span>Nilai yang tidak mengubah hasil gabung: +∞ untuk min, 0 untuk jumlah.</span></div>
        <div class="term"><b>Tinggi</b><span>⌈log₂ N⌉ + 1 level, sehingga jalur akar–daun pendek.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Ikuti warna simpul saat query: hijau diambil utuh, biru dipecah, abu-abu putus-putus diabaikan. Setelah update, perhatikan bahwa hanya satu jalur dari daun ke akar yang dihitung ulang.</p></div>
    <div data-viz="segtree"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Tentukan isi simpul</b><span>Apa yang perlu diketahui tentang sebuah rentang? (min, jumlah, …)</span></div>
        <div class="step-card"><b>Tentukan gabung</b><span>Bagaimana dua rentang bersebelahan digabung? Harus asosiatif.</span></div>
        <div class="step-card"><b>Nilai netral</b><span>Dikembalikan untuk simpul di luar rentang.</span></div>
        <div class="step-card"><b>Tulis 3 fungsi</b><span>bangun, query, ubah. Uji dengan solusi brute force O(N) per query.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Minimum rentang dengan update titik.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Segment tree minimum',
        'steps' => $sgSteps,
        'sample' => ['input' => "8 5\n5 8 6 3 9 2 7 4\n2 2 5\n2 1 8\n1 6 10\n2 5 8\n2 3 3\n", 'output' => "3\n2\n4\n6\n"],
        'py' => $sgPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: min a[2..5]</h2>
    <div class="prose"><p>a = 5 8 6 3 9 2 7 4. Nilai simpul: akar 2; level 1: [1..4] = 3, [5..8] = 2; level 2: [1..2] = 5, [3..4] = 3, [5..6] = 2, [7..8] = 4.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Simpul</th><th>Hubungan dengan [2..5]</th><th>Tindakan</th></tr>
            <tr><td>[1..8]</td><td>sebagian</td><td>pecah</td></tr>
            <tr><td>[1..4]</td><td>sebagian</td><td>pecah</td></tr>
            <tr><td>[1..2]</td><td>sebagian</td><td>pecah → [1] di luar (∞), [2] di dalam (8)</td></tr>
            <tr class="hl"><td>[3..4]</td><td>di dalam</td><td>ambil 3</td></tr>
            <tr><td>[5..8]</td><td>sebagian</td><td>pecah</td></tr>
            <tr><td>[5..6]</td><td>sebagian</td><td>pecah → [5] di dalam (9), [6] di luar (∞)</td></tr>
            <tr><td>[7..8]</td><td>di luar</td><td>∞</td></tr>
            <tr class="ok"><td colspan="2">hasil</td><td><b>min(8, 3, 9) = 3</b></td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Waktu</th></tr>
        <tr><td>Bangun</td><td><code>O(N)</code></td></tr>
        <tr><td>Query rentang / update titik</td><td><code>O(log N)</code></td></tr>
        <tr><td>Update rentang (lazy)</td><td><code>O(log N)</code></td></tr>
        <tr><td>Memori</td><td><code>4N</code> (rekursif) atau <code>2N</code> (iteratif)</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Ukuran array.</strong> Dengan penomoran 2v / 2v + 1, ukuran 2N tidak cukup jika N bukan pangkat dua. Pakai 4N.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Nilai netral salah.</strong> Mengembalikan 0 untuk simpul di luar rentang pada query <em>minimum</em> membuat jawaban 0 setiap kali ada simpul di luar. Untuk min pakai +∞, untuk max −∞, untuk jumlah 0.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa menghitung ulang</strong> <code>t[v]</code> setelah rekursi update membuat simpul atas menyimpan nilai lama.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'simpul kustom, lazy, dan sparse table', 'desc' => 'Tiga pengembangan yang paling sering keluar.'])

<section class="lesson-section" id="iteratif" data-toc="Versi Iteratif">
    <h2>Versi Iteratif</h2>
    <div class="prose"><p>Untuk min/jumlah dengan update titik, versi bottom-up tanpa rekursi lebih pendek dan sekitar dua kali lebih cepat. Daun disimpan di <code>t[n..2n−1]</code>, dan orang tua simpul p adalah p / 2. Ini juga versi yang dipakai solusi Python di atas.</p></div>
    @include('lessons.code', ['cpp' => $sgIter, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kustom" data-toc="Informasi Kustom">
    <h2>Informasi Kustom di Simpul</h2>
    <div class="prose">
        <p>Kekuatan segment tree ada pada fungsi gabung. Pertanyaan "subarray berjumlah maksimum di l..r, dengan update" tidak bisa dijawab dengan satu angka per simpul, karena subarray terbaik bisa melintasi batas antara dua anak. Simpan cukup informasi agar dua rentang bisa digabung:</p>
    </div>
    @include('lessons.code', ['cpp' => $sgInfo, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="lazy" data-toc="Lazy Propagation">
    <h2>Lazy Propagation: Update Rentang</h2>
    <div class="prose">
        <p>Menambah x ke a[l..r] dengan update titik satu per satu butuh O(N log N). Idenya: jika sebuah simpul seluruhnya berada di dalam [l, r], perbarui ringkasannya lalu <strong>catat utang</strong> di <code>lz[v]</code> tanpa turun ke anak. Utang itu baru diteruskan (<code>dorong</code>) saat suatu operasi nanti memang perlu turun melewati simpul itu. Dengan begitu update rentang juga O(log N).</p>
    </div>
    @include('lessons.code', ['cpp' => $sgLazy, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="sparse" data-toc="Sparse Table">
    <h2>Sparse Table untuk Array Tetap</h2>
    <div class="prose">
        <p>Jika array tidak pernah berubah dan pertanyaannya minimum/maksimum, ada cara yang lebih cepat: simpan minimum setiap rentang sepanjang pangkat dua. Rentang [l, r] selalu bisa ditutup oleh <em>dua</em> rentang sepanjang 2<sup>k</sup> yang tumpang tindih, dan tumpang tindih tidak masalah untuk min/max. Query menjadi O(1).</p>
    </div>
    @include('lessons.code', ['cpp' => $sgSparse, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Min/max + update</h4><p>Segment tree dasar.</p></div>
        <div class="pattern"><h4>Array tetap, min/max</h4><p>Sparse table, query O(1).</p></div>
        <div class="pattern"><h4>Jumlah + update titik</h4><p>Fenwick tree (lebih ringkas) atau segment tree.</p></div>
        <div class="pattern"><h4>Tambah/ganti rentang</h4><p>Lazy propagation.</p></div>
        <div class="pattern"><h4>Subarray terbaik</h4><p>Simpul berisi total, prefiks, sufiks, terbaik.</p></div>
        <div class="pattern"><h4>Cari posisi pertama</h4><p>"Indeks pertama ≥ x": turun ke anak kiri jika maksimumnya cukup, O(log N).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Simpul di luar rentang harus mengembalikan nilai yang tidak memengaruhi hasil gabung. Untuk maksimum, itu −∞.">
        <p class="quiz-q">Pada segment tree <strong>maksimum</strong>, simpul di luar rentang query mengembalikan …</p>
        <div class="quiz-options">
            <button class="quiz-option">0</button>
            <button class="quiz-option">+∞</button>
            <button class="quiz-option">−∞</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Hanya simpul yang rentangnya memuat posisi yang diubah, yaitu satu jalur akar–daun sepanjang O(log N).">
        <p class="quiz-q">Berapa simpul yang berubah ketika satu elemen di-update?</p>
        <div class="quiz-options">
            <button class="quiz-option">Satu jalur akar–daun: O(log N)</button>
            <button class="quiz-option">Semua simpul: O(N)</button>
            <button class="quiz-option">Hanya daunnya</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Sparse table menutup rentang dengan dua potongan yang tumpang tindih. Untuk jumlah, bagian tumpang tindih terhitung dua kali.">
        <p class="quiz-q">Mengapa sparse table dengan query O(1) tidak dipakai untuk <strong>jumlah</strong> rentang?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena memorinya O(N²)</button>
            <button class="quiz-option">Karena dua potongan yang tumpang tindih membuat sebagian elemen terhitung dua kali</button>
            <button class="quiz-option">Karena jumlah tidak asosiatif</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
