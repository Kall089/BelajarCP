@php
    $dsSteps = [
        ['Penunjuk induk dan ukuran', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

vector<int> p, sz;
int kelompok;

int find(int x) {
    int r = x;
    while (p[r] != r) r = p[r];
    while (p[x] != r) {
        int nx = p[x];
        p[x] = r;
        x = nx;
    }
    return r;
}

CPP, <<<'TXT'
<p>Setiap kelompok disimpan sebagai pohon. <code>p[x]</code> adalah induk x; akar menunjuk dirinya sendiri dan menjadi "wakil" kelompok. Dua elemen sekelompok jika dan hanya jika akarnya sama.</p>
<p><code>find</code> dibuat tanpa rekursi: lintasan pertama naik sampai akar, lintasan kedua membuat setiap simpul di jalur itu langsung menunjuk akar (<strong>kompresi jalur</strong>). Tanpa rekursi, tidak ada risiko stack overflow walaupun pohonnya sempat tinggi.</p>
TXT],
        ['Menggabungkan dengan ukuran', <<<'CPP'
void unite(int a, int b) {
    a = find(a);
    b = find(b);
    if (a == b) return;
    if (sz[a] < sz[b]) swap(a, b);
    p[b] = a;
    sz[a] += sz[b];
    kelompok--;
}

CPP, <<<'TXT'
<p>Yang digabung selalu <strong>akar</strong>, bukan a dan b itu sendiri. Pohon yang lebih kecil digantung di bawah yang lebih besar (<em>union by size</em>): sebuah simpul hanya bisa turun satu tingkat jika ukuran kelompoknya minimal berlipat dua, sehingga kedalaman tidak pernah melebihi log<sub>2</sub> N.</p>
<p>Banyak kelompok berkurang satu setiap kali dua akar berbeda digabung. <code>sz</code> hanya diperbarui di akar, jadi ukuran kelompok x dibaca dari <code>sz[find(x)]</code>.</p>
TXT],
        ['Menjawab operasi', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    p.resize(n + 1);
    sz.assign(n + 1, 1);
    iota(p.begin(), p.end(), 0);    // p[i] = i
    kelompok = n;

    string out;
    while (q--) {
        int t;
        cin >> t;
        if (t == 1) {
            int a, b;
            cin >> a >> b;
            unite(a, b);
        } else if (t == 2) {
            int x;
            cin >> x;
            out += to_string(sz[find(x)]) + "\n";
        } else {
            out += to_string(kelompok) + "\n";
        }
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Operasi 1 menggabungkan, operasi 2 menanyakan ukuran kelompok x, operasi 3 menanyakan banyak kelompok. Semua operasi praktis O(1), sehingga 2 · 10<sup>5</sup> operasi selesai dalam hitungan milidetik.</p>
TXT],
    ];

    $dsPy = <<<'PY'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    p = list(range(n + 1))
    sz = [1] * (n + 1)

    def find(x):
        r = x
        while p[r] != r:
            r = p[r]
        while p[x] != r:
            p[x], x = r, p[x]
        return r

    kelompok = n
    out = []
    i = 2
    for _ in range(q):
        t = data[i]; i += 1
        if t == b"1":
            a, b = find(int(data[i])), find(int(data[i + 1])); i += 2
            if a != b:
                if sz[a] < sz[b]:
                    a, b = b, a
                p[b] = a
                sz[a] += sz[b]
                kelompok -= 1
        elif t == b"2":
            out.append(sz[find(int(data[i]))]); i += 1
        else:
            out.append(kelompok)
    print("\n".join(map(str, out)))

main()
PY;

    $dsOffline = <<<'CPP'
// Jalan diputus satu per satu; setelah setiap pemutusan, berapa kelompok tersisa?
// DSU tidak bisa memisahkan. Balik waktunya: mulai dari keadaan AKHIR
// (semua jalan yang akan diputus sudah hilang), lalu tambahkan kembali dari belakang.
for (int i = 0; i < m; i++)
    if (!diputus[i]) unite(u[i], v[i]);          // jalan yang tidak pernah diputus
vector<int> jawab(k);
for (int j = k - 1; j >= 0; j--) {
    jawab[j] = kelompok;                         // keadaan setelah pemutusan ke-j
    unite(u[putus[j]], v[putus[j]]);             // mundur satu langkah waktu
}
// cetak jawab[0], jawab[1], ..., jawab[k - 1]
CPP;

    $dsParitas = <<<'CPP'
// DSU paritas: beda[x] = 0 jika x satu tim dengan induknya, 1 jika berlawanan.
// find mengembalikan akar sekaligus memperbarui beda[x] menjadi paritas x terhadap akar.
// Rekursi aman karena union by size membuat kedalaman ≤ log N.
int find(int x) {
    if (p[x] == x) return x;
    int r = find(p[x]);
    beda[x] ^= beda[p[x]];    // p[x] sudah menunjuk akar: paritasnya sudah terhadap akar
    p[x] = r;
    return r;
}

// Catat "a dan b berbeda tim" (d = 1) atau "satu tim" (d = 0).
// Mengembalikan false jika bertentangan dengan informasi sebelumnya.
bool catat(int a, int b, int d) {
    int ra = find(a), rb = find(b);
    if (ra == rb) return (beda[a] ^ beda[b]) == d;
    if (sz[ra] < sz[rb]) swap(ra, rb);
    p[rb] = ra;
    beda[rb] = beda[a] ^ beda[b] ^ d;   // agar paritas a terhadap b menjadi d
    sz[ra] += sz[rb];
    return true;
}
CPP;

    $dsNext = <<<'CPP'
// Q perintah "cat pagar l..r dengan warna c"; perintah belakangan menimpa yang lama.
// Proses dari perintah TERAKHIR: petak yang sudah dicat tidak akan berubah lagi.
// nxt[i] = petak belum dicat paling kiri yang >= i (nxt[n] = n sebagai penjaga).
int cari(int x) {
    while (nxt[x] != x) {
        nxt[x] = nxt[nxt[x]];      // path halving: lompat dua tingkat sekaligus
        x = nxt[x];
    }
    return x;
}

iota(nxt.begin(), nxt.end(), 0);
for (int k = q - 1; k >= 0; k--)
    for (int i = cari(l[k]); i <= r[k]; i = cari(i)) {
        warna[i] = c[k];
        nxt[i] = i + 1;            // petak i terisi: setiap pencarian akan melompatinya
    }
// Setiap petak dicat sekali, total hampir O(n + q).
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menulis DSU tanpa rekursi dengan kompresi jalur dan <em>union by size</em>, serta menjelaskan mengapa cepat.</li>
        <li>Melacak banyak kelompok dan ukuran setiap kelompok saat penggabungan terjadi.</li>
        <li>Mengubah soal "menghapus" menjadi "menambah" dengan memproses <strong>terbalik secara offline</strong>.</li>
        <li>Memakai DSU berbobot/paritas dan trik "petak kosong berikutnya".</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'hutan penunjuk induk', 'desc' => 'Setiap kelompok adalah pohon; akarnya adalah wakil kelompok.'])

<section class="lesson-section" id="masalah" data-toc="Masalahnya">
    <h2>Kelompok yang Terus Bergabung</h2>
    <div class="prose">
        <p>Di materi <a href="{{ route('lessons.show', 'mst') }}">Minimum Spanning Tree</a> kamu sudah memakai DSU untuk algoritma Kruskal. Materi ini membahasnya sebagai struktur data mandiri, karena DSU muncul di banyak soal yang sama sekali bukan tentang MST: pertemanan, jaringan, pulau yang menyatu, persamaan "x = y", sampai pengecatan rentang.</p>
        <p>Polanya selalu sama: ada N elemen, kelompok-kelompok terus <strong>bergabung</strong>, dan kita sering bertanya "apakah a dan b sekelompok?" atau "berapa besar kelompok x?". BFS/DFS ulang setelah setiap penggabungan butuh O(N) per pertanyaan; DSU menjawabnya hampir O(1).</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>p[x]</b><span>Induk x. Akar: p[r] = r.</span></div>
        <div class="term"><b>find(x)</b><span>Akar dari pohon yang memuat x, yaitu wakil kelompoknya.</span></div>
        <div class="term"><b>unite(a, b)</b><span>Menggantung akar yang satu di bawah akar yang lain.</span></div>
        <div class="term"><b>sz[r]</b><span>Banyak elemen di kelompok berakar r (hanya bermakna di akar).</span></div>
    </div>
</section>

<section class="lesson-section" id="ide" data-toc="Dua Optimasi">
    <h2>Dua Optimasi yang Membuatnya Cepat</h2>
    <div class="prose">
        <p>Tanpa optimasi, menggabungkan 1–2, 1–3, 1–4, … bisa membentuk rantai panjang, dan <code>find</code> menjadi O(N). Dua perbaikan kecil mengatasinya:</p>
        <ul>
            <li><strong>Union by size</strong>: gantung pohon kecil di bawah pohon besar. Setiap kali sebuah simpul turun satu tingkat, kelompoknya minimal berlipat dua, jadi kedalaman paling banyak log<sub>2</sub> N.</li>
            <li><strong>Kompresi jalur</strong>: setelah <code>find(x)</code> menemukan akar, semua simpul di jalurnya langsung diarahkan ke akar. Pencarian berikutnya hanya satu langkah.</li>
        </ul>
        <p>Dengan keduanya, biaya rata-rata per operasi adalah O(α(N)), dengan α fungsi invers Ackermann yang tidak pernah melebihi 4 untuk N sebesar apa pun di dunia nyata.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>DSU hanya bisa <strong>menggabungkan</strong>, tidak bisa memisahkan. Jika soal meminta penghapusan, coba balik urutan waktunya (level 3).</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Panah menunjuk ke induk. Format operasi: <code>a-b</code> untuk menggabungkan, <code>?x</code> untuk mencari akar x. Perhatikan langkah <em>Kompresi jalur</em> saat <code>?8</code>: simpul 8 dan 7 langsung menunjuk akar. Lalu pilih mode <strong>Naif</strong> dan masukkan <code>1-2, 1-3, 1-4, 1-5, 1-6, 1-7, 1-8, ?1</code> untuk melihat rantai terbentuk.</p></div>
    <div data-viz="dsu"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Gabung, ukuran kelompok, dan banyak kelompok.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Input: N elemen dan Q operasi. <code>1 a b</code> menggabungkan kelompok a dan b, <code>2 x</code> menanyakan ukuran kelompok x, <code>3</code> menanyakan banyak kelompok.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'DSU: ukuran dan banyak kelompok',
        'steps' => $dsSteps,
        'sample' => ['input' => "6 8\n1 1 2\n1 3 4\n2 1\n1 2 4\n2 3\n3\n1 1 3\n3\n", 'output' => "2\n4\n3\n3\n"],
        'py' => $dsPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Operasi</th><th>find</th><th>Aksi</th><th>p[1..6]</th><th>Output</th></tr>
            <tr><td>1 1 2</td><td>1, 2</td><td>sz sama: p[2] = 1</td><td>1 1 3 4 5 6</td><td></td></tr>
            <tr><td>1 3 4</td><td>3, 4</td><td>p[4] = 3</td><td>1 1 3 3 5 6</td><td></td></tr>
            <tr><td>2 1</td><td>1</td><td>sz[1]</td><td>1 1 3 3 5 6</td><td>2</td></tr>
            <tr class="hl"><td>1 2 4</td><td>1, 3</td><td>sz sama: p[3] = 1, sz[1] = 4</td><td>1 1 1 3 5 6</td><td></td></tr>
            <tr><td>2 3</td><td>1</td><td>sz[1]</td><td>1 1 1 3 5 6</td><td>4</td></tr>
            <tr><td>3</td><td></td><td>6 − 3 penggabungan</td><td></td><td>3</td></tr>
            <tr class="ok"><td>1 1 3</td><td>1, 1</td><td>sudah sekelompok</td><td>1 1 1 3 5 6</td><td></td></tr>
            <tr><td>3</td><td></td><td></td><td></td><td>3</td></tr>
        </table>
    </div>
    <div class="prose"><p>Perhatikan p[4] masih 3, bukan 1. Kompresi baru terjadi saat <code>find(4)</code> dipanggil; DSU tidak perlu selalu rapi, cukup benar.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Versi</th><th>Per operasi</th><th>Catatan</th></tr>
        <tr><td>Naif</td><td><code>O(N)</code></td><td>Rantai panjang pada urutan yang buruk.</td></tr>
        <tr><td>Hanya union by size</td><td><code>O(log N)</code></td><td>Kedalaman ≤ log N; cocok jika perlu rollback.</td></tr>
        <tr><td>Hanya kompresi jalur</td><td><code>O(log N)</code> rata-rata</td><td>Sudah cukup cepat di hampir semua soal.</td></tr>
        <tr class="ok"><td>Keduanya</td><td><code>O(α(N))</code></td><td>Praktis konstan.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Membandingkan p[a] dengan p[b].</strong> Induk langsung belum tentu akar. Selalu bandingkan <code>find(a) == find(b)</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Menggabungkan bukan akar.</strong> <code>p[a] = b</code> memutus a dari kelompok lamanya. Yang benar: <code>p[find(a)] = find(b)</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>find rekursif tanpa union by size</strong> bisa membuat rekursi sedalam 10<sup>6</sup> pada rantai, dan program crash karena stack overflow. Pakai versi tanpa rekursi seperti di atas, atau pastikan union by size dipakai.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'teknik lanjutan', 'desc' => 'Proses terbalik, DSU berbobot, dan petak kosong berikutnya.'])

<section class="lesson-section" id="offline" data-toc="Proses Terbalik">
    <h2>Menghapus dengan Cara Menambah dari Belakang</h2>
    <div class="prose">
        <p>DSU tidak bisa memutus sambungan. Namun jika semua pemutusan diketahui di awal (soal <em>offline</em>), balik arah waktu: mulai dari keadaan paling akhir, lalu "putar mundur". Pemutusan yang dibalik menjadi penyambungan, dan itu keahlian DSU. Simpan jawaban di array, lalu cetak dalam urutan aslinya.</p>
    </div>
    @include('lessons.code', ['cpp' => $dsOffline, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="paritas" data-toc="DSU Paritas">
    <h2>DSU Berbobot dan Paritas</h2>
    <div class="prose">
        <p>Kadang kita perlu tahu lebih dari "sekelompok atau tidak", misalnya "a dan b berlawanan tim", "x<sub>a</sub> − x<sub>b</sub> = 5", atau apakah graph tetap bipartit setelah sisi ditambah. Simpan di setiap simpul <strong>nilai relatif terhadap induknya</strong>. Saat kompresi jalur, nilai relatif itu dijumlahkan (atau di-XOR untuk paritas) sepanjang jalur sehingga menjadi relatif terhadap akar.</p>
    </div>
    @include('lessons.code', ['cpp' => $dsParitas, 'js' => null, 'py' => null])
    <div class="prose"><p>Untuk selisih bilangan, ganti XOR dengan penjumlahan: <code>beda[x] += beda[p[x]]</code> dan <code>beda[rb] = beda[a] − beda[b] + d</code> (dengan arti beda = nilai simpul dikurangi nilai induknya, dan d = x<sub>b</sub> − x<sub>a</sub>). Hati-hati: jika ra dan rb ditukar karena ukuran, yang digantung adalah ra, sehingga tandanya ikut dibalik. Selalu periksa dengan contoh kecil.</p></div>
</section>

<section class="lesson-section" id="next" data-toc="Petak Kosong Berikutnya">
    <h2>Trik "Petak Kosong Berikutnya"</h2>
    <div class="prose">
        <p>DSU juga bisa dipakai pada array untuk melompati posisi yang sudah selesai diproses. Setiap posisi menunjuk ke posisi berikutnya yang masih kosong; setelah diisi, posisi itu digabung ke kanannya. Pola ini muncul pada soal pengecatan rentang, penjadwalan ke hari kosong pertama, dan penghapusan elemen berulang.</p>
    </div>
    @include('lessons.code', ['cpp' => $dsNext, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Terhubung?</h4><p>find(a) == find(b).</p></div>
        <div class="pattern"><h4>Ukuran kelompok</h4><p>sz[find(x)].</p></div>
        <div class="pattern"><h4>Siklus pada graph tak berarah</h4><p>Sisi (u, v) dengan find(u) == find(v) membentuk siklus.</p></div>
        <div class="pattern"><h4>Penghapusan offline</h4><p>Proses terbalik, simpan jawaban, cetak urut asli.</p></div>
        <div class="pattern"><h4>Bipartit dinamis</h4><p>DSU paritas; konflik saat paritas tidak cocok.</p></div>
        <div class="pattern"><h4>Rentang yang ditimpa</h4><p>Proses dari belakang, nxt[i] melompati petak terisi.</p></div>
    </div>
    <div class="callout note">
        <span class="callout-icon">💡</span>
        <p><strong>DSU dengan rollback.</strong> Tanpa kompresi jalur (hanya union by size), setiap penggabungan hanya mengubah dua nilai, sehingga bisa dibatalkan dengan stack. Ini dasar teknik konektivitas dinamis offline untuk soal yang menambah dan menghapus sisi bergantian.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Induk langsung bisa berbeda walaupun akarnya sama, misalnya p[4] = 3 dan p[2] = 1 padahal keduanya berakar 1.">
        <p class="quiz-q">Mengapa <code>p[a] == p[b]</code> tidak boleh dipakai untuk mengecek apakah a dan b sekelompok?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena lebih lambat daripada find</button>
            <button class="quiz-option">Karena induk langsung belum tentu akar kelompok</button>
            <button class="quiz-option">Karena p hanya terisi di akar</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Simpul turun satu tingkat hanya ketika kelompoknya bergabung dengan kelompok yang minimal sama besar, sehingga ukurannya minimal berlipat dua. Berlipat dua paling banyak log N kali.">
        <p class="quiz-q">Dengan union by size saja, mengapa kedalaman pohon paling banyak log<sub>2</sub> N?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena setiap pohon berbentuk biner</button>
            <button class="quiz-option">Karena find selalu memendekkan jalur</button>
            <button class="quiz-option">Karena setiap kali simpul turun satu tingkat, ukuran kelompoknya minimal berlipat dua</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Dari belakang, setiap pemutusan menjadi penambahan sisi, yang bisa ditangani DSU.">
        <p class="quiz-q">Sisi-sisi graph diputus satu per satu dan kita perlu banyak komponen setelah setiap pemutusan. Pendekatan yang tepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">Proses dari pemutusan terakhir ke pertama sambil menyambung sisi dengan DSU</button>
            <button class="quiz-option">Jalankan DSU biasa lalu kurangi sz saat sisi diputus</button>
            <button class="quiz-option">Tidak mungkin lebih cepat dari BFS ulang</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
