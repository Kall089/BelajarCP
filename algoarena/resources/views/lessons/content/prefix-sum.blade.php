@php
    $psSteps = [
        ['Membaca operasi ke array selisih', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, q;
    cin >> n >> m >> q;
    vector<long long> d(n + 2, 0);
    for (int j = 0; j < m; j++) {
        int l, r;
        long long v;
        cin >> l >> r >> v;
        d[l] += v;
        d[r + 1] -= v;
    }

CPP, <<<'TXT'
<p>Ada N elemen yang awalnya 0, M operasi "tambah v ke elemen l..r", lalu Q pertanyaan "berapa jumlah elemen l..r setelah semua operasi?".</p>
<p><code>d[i]</code> menyimpan <strong>selisih</strong> <code>a[i] − a[i−1]</code>. Menambah v pada l..r hanya mengubah dua selisih: di l nilainya naik v dibanding tetangga kirinya, dan di r + 1 turun kembali v. Ukuran d adalah n + 2 agar <code>d[r + 1]</code> aman ketika r = n.</p>
TXT],
        ['Membangun array akhir dan prefix sum-nya', <<<'CPP'
    vector<long long> a(n + 1, 0), pre(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        a[i] = a[i - 1] + d[i];
        pre[i] = pre[i - 1] + a[i];
    }

CPP, <<<'TXT'
<p>Prefix sum dari d mengembalikan array asli: <code>a[i] = d[1] + … + d[i]</code>. Di loop yang sama kita langsung membangun prefix sum kedua, <code>pre[i] = a[1] + … + a[i]</code>, untuk menjawab pertanyaan jumlah rentang.</p>
<p>Jadi ada dua arah yang saling berkebalikan: <strong>selisih</strong> mengubah array menjadi "perubahannya", <strong>prefix sum</strong> mengubah "perubahan" kembali menjadi array.</p>
TXT],
        ['Menjawab pertanyaan dalam O(1)', <<<'CPP'
    string out;
    for (int j = 0; j < q; j++) {
        int l, r;
        cin >> l >> r;
        out += to_string(pre[r] - pre[l - 1]) + "\n";
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Jumlah a[l..r] = <code>pre[r] − pre[l − 1]</code>: jumlah sampai r dikurangi bagian sebelum l. <code>pre[0] = 0</code> membuat rumus ini juga benar untuk l = 1.</p>
<p>Total O(N + M + Q). Cara langsung (menambah satu per satu lalu menjumlah satu per satu) butuh O((M + Q) · N).</p>
TXT],
    ];

    $psPy = <<<'PY'
import sys
data = sys.stdin.buffer.read().split()
n, m, q = int(data[0]), int(data[1]), int(data[2])
d = [0] * (n + 2)
p = 3
for _ in range(m):
    l, r, v = int(data[p]), int(data[p + 1]), int(data[p + 2])
    p += 3
    d[l] += v
    d[r + 1] -= v
pre = [0] * (n + 1)
a = 0
for i in range(1, n + 1):
    a += d[i]
    pre[i] = pre[i - 1] + a
out = []
for _ in range(q):
    l, r = int(data[p]), int(data[p + 1])
    p += 2
    out.append(pre[r] - pre[l - 1])
print("\n".join(map(str, out)))
PY;

    $ps2d = <<<'CPP'
// P[i][j] = jumlah semua sel (x, y) dengan 1 ≤ x ≤ i dan 1 ≤ y ≤ j. Baris/kolom 0 bernilai 0.
for (int i = 1; i <= R; i++)
    for (int j = 1; j <= C; j++)
        P[i][j] = g[i][j] + P[i - 1][j] + P[i][j - 1] - P[i - 1][j - 1];

// Jumlah persegi panjang (r1, c1)–(r2, c2): inklusi–eksklusi
long long jumlah = P[r2][c2] - P[r1 - 1][c2] - P[r2][c1 - 1] + P[r1 - 1][c1 - 1];
CPP;

    $ps2dDiff = <<<'CPP'
// Tambah v ke semua sel di persegi panjang (r1, c1)–(r2, c2): cukup tandai 4 pojok.
D[r1][c1] += v;
D[r1][c2 + 1] -= v;
D[r2 + 1][c1] -= v;
D[r2 + 1][c2 + 1] += v;

// Setelah SEMUA operasi: nilai akhir = prefix sum 2D dari D (boleh di tempat).
for (int i = 1; i <= R; i++)
    for (int j = 1; j <= C; j++)
        D[i][j] += D[i - 1][j] + D[i][j - 1] - D[i - 1][j - 1];
CPP;

    $psMod = <<<'CPP'
// Banyak subarray yang jumlahnya habis dibagi k.
// jumlah a[l..r] = pre[r] − pre[l−1] habis dibagi k  <=>  pre[r] mod k == pre[l−1] mod k
map<long long, long long> frek;
frek[0] = 1;                              // pre[0] = 0
long long pre = 0, jawab = 0;
for (int i = 1; i <= n; i++) {
    pre = ((pre + a[i]) % k + k) % k;     // + k: a[i] boleh negatif
    jawab += frek[pre];                   // pasangkan dengan semua l−1 yang bersisa sama
    frek[pre]++;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjawab banyak pertanyaan jumlah rentang dalam O(1) dengan <strong>prefix sum</strong>.</li>
        <li>Melakukan banyak penambahan rentang dalam O(1) per operasi dengan <strong>array selisih</strong>.</li>
        <li>Memperluas keduanya ke grid 2D dengan inklusi–eksklusi.</li>
        <li>Menghitung subarray dengan sifat tertentu memakai prefix + tabel frekuensi.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'dua operasi yang saling berkebalikan', 'desc' => 'Prefix sum untuk bertanya, array selisih untuk mengubah.'])

<section class="lesson-section" id="prefix" data-toc="Prefix Sum">
    <h2>Prefix Sum: Menyimpan Jumlah Sejauh Ini</h2>
    <div class="prose">
        <p>Bayangkan buku tabungan yang di setiap baris mencatat <strong>saldo</strong>, bukan hanya setoran hari itu. Untuk tahu total setoran dari hari ke-l sampai hari ke-r, cukup kurangi saldo hari r dengan saldo hari l − 1. Tidak perlu menjumlah ulang.</p>
        <p>Itulah prefix sum: <code>pre[i] = a[1] + a[2] + … + a[i]</code>, dengan <code>pre[0] = 0</code>. Menyiapkannya O(N), lalu setiap pertanyaan jumlah rentang O(1):</p>
        <p style="text-align:center"><code>a[l] + … + a[r] = pre[r] − pre[l − 1]</code></p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>0</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th></tr>
            <tr><td>a[i]</td><td>–</td><td>5</td><td>−2</td><td>7</td><td>3</td><td>−4</td></tr>
            <tr><td>pre[i]</td><td>0</td><td>5</td><td>3</td><td>10</td><td>13</td><td>9</td></tr>
        </table>
    </div>
    <div class="prose"><p>Jumlah a[2..4] = pre[4] − pre[1] = 13 − 5 = 8 (memang −2 + 7 + 3 = 8).</p></div>
</section>

<section class="lesson-section" id="selisih" data-toc="Array Selisih">
    <h2>Array Selisih: Tandai Awal dan Akhir</h2>
    <div class="prose">
        <p>Sekarang kebalikannya. Panitia lomba mencatat M kelompok pengunjung; kelompok ke-j berjumlah v orang dan berada di area l sampai r. Berapa orang di setiap area? Menambah v ke setiap area dalam rentang memakan O(N) per kelompok.</p>
        <p>Trik array selisih: cukup tulis "<strong>+v mulai di l</strong>" dan "<strong>−v mulai di r + 1</strong>". Ketika nanti kita berjalan dari kiri sambil menjumlahkan tanda-tanda ini (prefix sum), kenaikan v otomatis berlaku tepat dari l sampai r.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Array selisih <code>d[i] = a[i] − a[i−1]</code> dan prefix sum adalah operasi yang saling membatalkan. Penambahan rentang di a hanya mengubah <strong>dua</strong> sel di d. Syaratnya: semua operasi selesai dulu, baru array akhirnya dibaca (pemrosesan <em>offline</em>).</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Perhatikan bahwa setiap operasi hanya mewarnai dua sel di baris <code>d</code>. Pada fase kedua, baris <code>a</code> diisi dari kiri dengan menjumlahkan d. Ganti operasinya (format <code>l r v</code>, dipisah titik koma) lalu tekan Terapkan.</p></div>
    <div data-viz="diffarray"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Siapkan d</b><span>Ukuran N + 2, semua 0 (atau isi dengan selisih array awal).</span></div>
        <div class="step-card"><b>Setiap operasi</b><span><code>d[l] += v</code>, <code>d[r + 1] −= v</code>.</span></div>
        <div class="step-card"><b>Bangun a</b><span>Prefix sum dari d.</span></div>
        <div class="step-card"><b>Pertanyaan</b><span>Jika perlu jumlah rentang, buat prefix sum lagi dari a.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Penambahan rentang lalu pertanyaan jumlah rentang.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Array selisih + prefix sum',
        'steps' => $psSteps,
        'sample' => ['input' => "6 3 3\n1 3 2\n2 5 3\n4 4 -1\n1 6\n2 4\n5 5\n", 'output' => "17\n12\n3\n"],
        'py' => $psPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Operasi: +2 pada 1..3, +3 pada 2..5, −1 pada 4..4. N = 6.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th></tr>
            <tr><td>d setelah +2 di 1..3</td><td>2</td><td>0</td><td>0</td><td>−2</td><td>0</td><td>0</td><td>0</td></tr>
            <tr><td>d setelah +3 di 2..5</td><td>2</td><td>3</td><td>0</td><td>−2</td><td>0</td><td>−3</td><td>0</td></tr>
            <tr class="hl"><td>d setelah −1 di 4..4</td><td>2</td><td>3</td><td>0</td><td>−3</td><td>1</td><td>−3</td><td>0</td></tr>
            <tr class="ok"><td>a = prefix d</td><td>2</td><td>5</td><td>5</td><td>2</td><td>3</td><td>0</td><td></td></tr>
            <tr><td>pre = prefix a</td><td>2</td><td>7</td><td>12</td><td>14</td><td>17</td><td>17</td><td></td></tr>
        </table>
    </div>
    <div class="prose"><p>Jumlah 2..4 = pre[4] − pre[1] = 14 − 2 = 12, sesuai output contoh.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Kebutuhan</th><th>Cara langsung</th><th>Dengan teknik ini</th></tr>
        <tr><td>Q pertanyaan jumlah rentang (array tetap)</td><td><code>O(Q · N)</code></td><td><code>O(N + Q)</code> prefix sum</td></tr>
        <tr><td>M penambahan rentang, dibaca di akhir</td><td><code>O(M · N)</code></td><td><code>O(N + M)</code> array selisih</td></tr>
        <tr><td>Penambahan dan pertanyaan bergantian</td><td><code>O((M + Q) · N)</code></td><td>Butuh Fenwick tree / segment tree, <code>O(log N)</code> per operasi</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Indeks r + 1.</strong> Buat array berukuran N + 2 (indeks 1-based) agar <code>d[r + 1]</code> tidak keluar batas saat r = N.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> 2 · 10<sup>5</sup> elemen bernilai 10<sup>9</sup> berjumlah 2 · 10<sup>14</sup>. Prefix sum hampir selalu butuh <code>long long</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Online vs offline.</strong> Array selisih hanya cocok jika semua penambahan terjadi sebelum array dibaca. Jika pertanyaan dan penambahan bercampur, gunakan materi berikutnya: Fenwick tree atau segment tree.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'dua dimensi & frekuensi', 'desc' => 'Inklusi–eksklusi pada grid dan prefix dengan tabel frekuensi.'])

<section class="lesson-section" id="dua-dimensi" data-toc="Prefix Sum 2D">
    <h2>Prefix Sum 2D</h2>
    <div class="prose">
        <p><code>P[i][j]</code> = jumlah semua sel di persegi panjang dari pojok (1, 1) sampai (i, j). Jumlah persegi panjang sembarang (r1, c1)–(r2, c2) diperoleh dari empat nilai P:</p>
    </div>
    <figure class="diagram">
        <svg viewBox="0 0 520 250" role="img" aria-label="Inklusi-eksklusi prefix sum 2D">
            <rect x="60" y="20" width="330" height="200" rx="6" style="fill: var(--panel-2); stroke: var(--line-strong)" />
            <rect x="60" y="20" width="330" height="70" style="fill: rgba(239, 68, 68, 0.13)" />
            <rect x="60" y="20" width="110" height="200" style="fill: rgba(239, 68, 68, 0.13)" />
            <rect x="60" y="20" width="110" height="70" style="fill: rgba(245, 158, 11, 0.32)" />
            <rect x="170" y="90" width="220" height="130" style="fill: rgba(34, 211, 238, 0.22); stroke: var(--cyan); stroke-width: 2" />
            <text x="115" y="60" class="dg-small" style="text-anchor: middle; fill: var(--hl-text); font-weight: 700">+ P[r1−1][c1−1]</text>
            <text x="280" y="60" class="dg-small" style="text-anchor: middle; fill: var(--red-text)">− P[r1−1][c2]</text>
            <text x="115" y="160" class="dg-small" style="text-anchor: middle; fill: var(--red-text)">− P[r2][c1−1]</text>
            <text x="280" y="160" class="dg-small" style="text-anchor: middle; fill: var(--text-strong); font-weight: 700">persegi yang ditanya</text>
            <text x="384" y="212" class="dg-small" style="text-anchor: end; fill: var(--muted)">(r2, c2)</text>
            <text x="178" y="106" class="dg-small" style="fill: var(--muted)">(r1, c1)</text>
            <text x="450" y="125" class="dg-small" style="text-anchor: middle; fill: var(--muted)">P[r2][c2]</text>
            <text x="450" y="142" class="dg-small" style="text-anchor: middle; fill: var(--muted)">= seluruh kotak</text>
        </svg>
        <figcaption>Mulai dari P[r2][c2] (seluruh kotak abu-abu), buang pita atas dan pita kiri (merah). Pojok kiri atas (jingga) terbuang dua kali, jadi tambahkan sekali lagi.</figcaption>
    </figure>
    @include('lessons.code', ['cpp' => $ps2d, 'js' => null, 'py' => null])
    <div class="prose"><p>Array selisih juga punya versi 2D: tandai empat pojok, lalu prefix sum 2D mengembalikan nilai akhir. Ini menjawab soal seperti "tambahkan v pada banyak persegi panjang, lalu cetak grid akhirnya".</p></div>
    @include('lessons.code', ['cpp' => $ps2dDiff, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="frekuensi" data-toc="Prefix + Frekuensi">
    <h2>Prefix + Tabel Frekuensi</h2>
    <div class="prose">
        <p>Banyak soal "hitung subarray yang …" bisa diubah menjadi "hitung pasangan indeks prefix yang …". Subarray l..r berpadanan dengan pasangan (l − 1, r). Jika syaratnya hanya membandingkan <code>pre[r]</code> dan <code>pre[l − 1]</code>, simpan berapa kali setiap nilai prefix sudah muncul.</p>
    </div>
    @include('lessons.code', ['cpp' => $psMod, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Jumlah tepat K</h4><p>Cari pre[l − 1] = pre[r] − K di tabel frekuensi.</p></div>
        <div class="pattern"><h4>Habis dibagi K</h4><p>Cocokkan sisa bagi prefix (awas sisa negatif).</p></div>
        <div class="pattern"><h4>XOR rentang</h4><p>px[i] = a[1] ⊕ … ⊕ a[i]; XOR l..r = px[r] ⊕ px[l − 1].</p></div>
        <div class="pattern"><h4>Banyak huruf</h4><p>Prefix per huruf: berapa huruf 'a' di s[l..r]? cnt[r][a] − cnt[l−1][a].</p></div>
        <div class="pattern"><h4>Seimbang</h4><p>Ubah 0 menjadi −1; subarray seimbang = pre sama di dua ujung.</p></div>
        <div class="pattern"><h4>Rata-rata ≥ X</h4><p>Kurangi setiap elemen dengan X; cari subarray berjumlah ≥ 0.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Penambahan v pada l..r hanya mengubah selisih di l (naik v) dan di r + 1 (turun v).">
        <p class="quiz-q">Menambah 4 pada a[3..7] mengubah array selisih d di …</p>
        <div class="quiz-options">
            <button class="quiz-option">d[3..7] semuanya +4</button>
            <button class="quiz-option">d[3] += 4 dan d[7] −= 4</button>
            <button class="quiz-option">d[3] += 4 dan d[8] −= 4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Pojok P[r1−1][c1−1] terkurangi dua kali (oleh pita atas dan pita kiri), jadi harus ditambah sekali.">
        <p class="quiz-q">Mengapa rumus jumlah persegi 2D menambahkan <code>P[r1−1][c1−1]</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena bagian itu terkurangi dua kali</button>
            <button class="quiz-option">Karena baris 0 tidak dihitung</button>
            <button class="quiz-option">Agar hasilnya tidak negatif</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Prefix sum statis tidak bisa diperbarui murah: setiap perubahan a[i] mengubah pre[i..N]. Untuk campuran update dan query, pakai Fenwick tree atau segment tree.">
        <p class="quiz-q">Q operasi bercampur: ubah satu elemen, atau tanya jumlah rentang. Prefix sum biasa …</p>
        <div class="quiz-options">
            <button class="quiz-option">tetap O(1) per operasi</button>
            <button class="quiz-option">butuh O(N) untuk setiap perubahan, jadi terlalu lambat</button>
            <button class="quiz-option">tidak bisa dipakai sama sekali untuk jumlah</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
