@php
    $gmSteps = [
        ['Baca himpunan langkah', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int m;
    cin >> m;
    vector<int> S(m);
    for (auto& s : S) cin >> s;

CPP, <<<'TXT'
<p>Permainan: beberapa tumpukan batu. Pada gilirannya, pemain memilih <strong>satu</strong> tumpukan lalu mengambil tepat s batu darinya, dengan s salah satu anggota himpunan S. Pemain yang tidak bisa melangkah kalah.</p>
<p>Kita akan menghitung nilai Grundy setiap ukuran tumpukan sekali saja, lalu menjawab beberapa babak permainan.</p>
TXT],
        ['Nilai Grundy dengan mex', <<<'CPP'
    const int MAKS = 100000;
    vector<int> g(MAKS + 1, 0);
    for (int x = 1; x <= MAKS; x++) {
        vector<bool> ada(m + 1, false);
        for (int s : S)
            if (s <= x) ada[g[x - s]] = true;
        while (ada[g[x]]) g[x]++;        // mex: nilai terkecil yang tidak muncul
    }

CPP, <<<'TXT'
<p><code>g[x]</code> = mex dari nilai Grundy semua posisi yang bisa dicapai dari x. mex (<em>minimum excludant</em>) adalah bilangan cacah terkecil yang tidak ada di himpunan: mex{0, 1, 3} = 2, mex{1, 2} = 0.</p>
<ul>
    <li>g[x] = 0 tepat ketika x posisi kalah: tidak ada langkah ke posisi bernilai 0.</li>
    <li>Dari x ada paling banyak m langkah, jadi mex paling besar m. Array <code>ada</code> cukup berukuran m + 1.</li>
</ul>
<p>Waktu O(MAKS · m).</p>
TXT],
        ['Gabungkan tumpukan dengan XOR', <<<'CPP'
    int t;
    cin >> t;
    while (t--) {
        int n;
        cin >> n;
        int total = 0;
        for (int i = 0; i < n; i++) {
            int a;
            cin >> a;
            total ^= g[a];
        }
        cout << (total != 0 ? "PERTAMA" : "KEDUA") << '\n';
    }
    return 0;
}
CPP, <<<'TXT'
<p><strong>Teorema Sprague–Grundy</strong>: gabungan beberapa permainan independen (setiap giliran memilih satu permainan lalu melangkah di sana) bernilai Grundy XOR dari nilai setiap permainan. Hasil 0 berarti pemain pertama kalah.</p>
<p>Nim biasa adalah kasus khusus: jika boleh mengambil berapa pun, g[x] = x, sehingga aturannya menjadi "XOR semua tumpukan".</p>
TXT],
    ];

    $gmPy = <<<'PY'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    m = int(data[0])
    S = [int(x) for x in data[1:1 + m]]
    MAKS = 100000
    g = [0] * (MAKS + 1)
    for x in range(1, MAKS + 1):
        ada = set(g[x - s] for s in S if s <= x)
        v = 0
        while v in ada:
            v += 1
        g[x] = v
    k = 1 + m
    t = int(data[k]); k += 1
    out = []
    for _ in range(t):
        n = int(data[k]); k += 1
        total = 0
        for i in range(n):
            total ^= g[int(data[k + i])]
        k += n
        out.append("PERTAMA" if total else "KEDUA")
    print("\n".join(out))

main()
PY;

    $gmSimple = <<<'CPP'
// Satu tumpukan n batu, boleh mengambil 1..K batu. Siapa menang?
// Tabel kecil: K = 3 -> posisi kalah 0, 4, 8, 12, ...  (kelipatan K + 1)
bool menang = n % (K + 1) != 0;
// Strategi pemenang: ambil n % (K + 1) batu, sehingga lawan selalu mendapat kelipatan K + 1.
// Apa pun yang lawan ambil (a batu), balas dengan K + 1 - a batu.
CPP;

    $gmNim = <<<'CPP'
// Nim: X = a[0] ^ a[1] ^ ... ^ a[n-1]. Pemain pertama menang jika dan hanya jika X != 0.
// Langkah menang: pilih tumpukan i dengan (a[i] ^ X) < a[i], lalu kecilkan menjadi a[i] ^ X.
long long X = 0;
for (long long x : a) X ^= x;
int banyakLangkahMenang = 0;
for (long long x : a)
    if ((x ^ X) < x) banyakLangkahMenang++;     // tepat satu cara per tumpukan semacam ini
// Tumpukan seperti itu pasti ada: tumpukan yang punya bit tertinggi dari X.
CPP;

    $gmMisere = <<<'CPP'
// Nim misère: pemain yang mengambil batu TERAKHIR justru KALAH.
bool semuaKecil = true;              // semua tumpukan berisi 0 atau 1 batu?
long long X = 0;
for (long long x : a) {
    X ^= x;
    if (x > 1) semuaKecil = false;
}
bool pertamaMenang = semuaKecil ? (X == 0) : (X != 0);
// Hanya kasus "semua tumpukan ≤ 1" yang terbalik; selebihnya sama dengan Nim biasa.
CPP;

    $gmStair = <<<'CPP'
// Nim tangga: koin di anak tangga 1..n; satu langkah = memindahkan ≥ 1 koin
// dari anak tangga i ke i - 1 (anak tangga 0 adalah lantai, koin di sana keluar dari permainan).
// Hanya anak tangga BERNOMOR GANJIL yang penting:
long long X = 0;
for (int i = 1; i <= n; i += 2) X ^= koin[i];
bool pertamaMenang = X != 0;
// Memindahkan dari tangga genap ke ganjil bisa langsung "dibalas" dengan memindahkan
// koin yang sama ke tangga genap berikutnya, jadi tidak mengubah apa pun.
CPP;

    $gmDag = <<<'CPP'
// Permainan di graph berarah tanpa siklus (DAG): token di simpul v, satu langkah = pindah
// lewat satu sisi. Grundy dihitung dari simpul tanpa sisi keluar (urutan topologis terbalik).
vector<int> g(n, -1);
function<int(int)> grundy = [&](int v) {
    if (g[v] != -1) return g[v];
    set<int> nilai;
    for (int w : adj[v]) nilai.insert(grundy(w));
    int mex = 0;
    while (nilai.count(mex)) mex++;
    return g[v] = mex;
};
// Banyak token sekaligus (masing-masing di simpulnya): XOR grundy setiap token.
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menentukan posisi <strong>menang</strong> dan <strong>kalah</strong> dengan DP dan menemukan polanya.</li>
        <li>Menyelesaikan Nim dengan XOR, termasuk mencari langkah menang.</li>
        <li>Menghitung nilai Grundy dengan mex dan menggabungkan banyak permainan (Sprague–Grundy).</li>
        <li>Mengenali variasi: Nim misère, Nim tangga, permainan di DAG, dan strategi cermin.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'posisi menang dan kalah', 'desc' => 'Satu aturan sederhana yang menyelesaikan semua permainan kecil.'])

<section class="lesson-section" id="ide" data-toc="Menang & Kalah">
    <h2>Siapa Menang Jika Keduanya Sempurna?</h2>
    <div class="prose">
        <p>Soal teori permainan di kompetisi hampir selalu berbentuk: dua pemain bergiliran, informasi lengkap, tidak ada unsur acak, dan pemain yang tidak bisa melangkah kalah. Pertanyaannya: siapa yang menang jika keduanya bermain sempurna?</p>
        <p>Setiap posisi permainan pasti salah satu dari dua jenis:</p>
        <ul>
            <li><strong>Posisi kalah (K)</strong>: semua langkah menuju posisi menang (milik lawan). Termasuk posisi tanpa langkah sama sekali.</li>
            <li><strong>Posisi menang (M)</strong>: ada <em>paling sedikit satu</em> langkah menuju posisi kalah. Pemain cukup mengambil langkah itu.</li>
        </ul>
        <p>Karena setiap langkah membuat permainan "lebih kecil", tabel M/K bisa diisi dari posisi terkecil, persis seperti DP.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Cara kerja di kompetisi: hitung tabel M/K untuk ukuran kecil dengan brute force, cari polanya (sering periodik), lalu buktikan atau setidaknya uji pola itu sebelum dipakai untuk n sampai 10<sup>18</sup>.</p>
    </div>
    @include('lessons.code', ['cpp' => $gmSimple, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Kotak bertepi biru menandai posisi yang bisa dicapai dari posisi yang sedang dihitung. Coba S = <code>1, 2, 3</code> (pola kelipatan 4), lalu S = <code>1, 3, 4</code> (pola periode 7). Ganti ke mode <strong>Nilai Grundy</strong> untuk melihat angka yang dipakai saat menggabungkan banyak tumpukan, dan nyalakan Mode Tebak untuk berlatih.</p></div>
    <div data-viz="nim"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'Nim dan Sprague–Grundy', 'desc' => 'Banyak tumpukan sekaligus diselesaikan dengan XOR.'])

<section class="lesson-section" id="nim" data-toc="Nim">
    <h2>Nim: Keajaiban XOR</h2>
    <div class="prose">
        <p>Ada beberapa tumpukan; setiap giliran, pilih satu tumpukan dan ambil berapa pun batu (minimal satu). Teorema Bouton: pemain pertama kalah <strong>jika dan hanya jika</strong> XOR semua tumpukan bernilai 0. Mengapa?</p>
        <ol>
            <li>Dari posisi dengan XOR 0, setiap langkah mengubah tepat satu tumpukan, sehingga XOR pasti menjadi tidak nol.</li>
            <li>Dari posisi dengan XOR X ≠ 0, pilih tumpukan yang memiliki bit tertinggi dari X. Mengubahnya menjadi a ^ X membuat XOR menjadi 0, dan a ^ X &lt; a karena bit tertinggi itu padam.</li>
            <li>Posisi akhir (semua kosong) ber-XOR 0 dan merupakan posisi kalah. Jadi XOR 0 = kalah, XOR ≠ 0 = menang.</li>
        </ol>
    </div>
    @include('lessons.code', ['cpp' => $gmNim, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Sprague–Grundy</h2>
    <div class="prose"><p>Input: m dan himpunan langkah S, lalu T babak; setiap babak berisi N lalu ukuran N tumpukan (≤ 10<sup>5</sup>). Cetak PERTAMA jika pemain pertama menang, KEDUA jika tidak.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Grundy: ambil s ∈ S batu dari satu tumpukan',
        'steps' => $gmSteps,
        'sample' => ['input' => "3\n1 3 4\n3\n3\n2 5 7\n2\n1 3\n2\n4 6\n", 'output' => "PERTAMA\nKEDUA\nKEDUA\n"],
        'py' => $gmPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: S = {1, 3, 4}</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>x</th><th>Bisa ke</th><th>Nilai tujuan</th><th>g[x] = mex</th></tr>
            <tr><td>0</td><td>–</td><td>{}</td><td>0 (kalah)</td></tr>
            <tr><td>1</td><td>0</td><td>{0}</td><td>1</td></tr>
            <tr><td>2</td><td>1</td><td>{1}</td><td>0 (kalah)</td></tr>
            <tr><td>3</td><td>2, 0</td><td>{0}</td><td>1</td></tr>
            <tr><td>4</td><td>3, 1, 0</td><td>{1, 0}</td><td>2</td></tr>
            <tr class="hl"><td>5</td><td>4, 2, 1</td><td>{2, 0, 1}</td><td>3</td></tr>
            <tr><td>6</td><td>5, 3, 2</td><td>{3, 1, 0}</td><td>2</td></tr>
            <tr class="hl"><td>7</td><td>6, 4, 3</td><td>{2, 1}</td><td>0 (kalah)</td></tr>
        </table>
    </div>
    <div class="prose"><p>Babak pertama contoh: tumpukan 2, 5, 7 bernilai 0, 3, 0; XOR = 3 ≠ 0, jadi PERTAMA menang. Babak kedua: 1 dan 3 bernilai 1 dan 1; XOR = 0, KEDUA menang. Perhatikan bahwa posisi 3 adalah posisi menang jika sendirian, tetapi dua tumpukan bernilai sama saling "membatalkan".</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>DP menang/kalah</td><td><code>O(N · |S|)</code></td><td>Satu tumpukan, N kecil.</td></tr>
        <tr><td>Grundy + XOR</td><td><code>O(maks · |S| + total tumpukan)</code></td><td>Banyak tumpukan sekaligus.</td></tr>
        <tr><td>Rumus / pola</td><td><code>O(1)</code></td><td>Untuk N sampai 10<sup>18</sup>, setelah pola terbukti.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>M/K tidak bisa digabung, Grundy bisa.</strong> Dua tumpukan yang masing-masing posisi menang belum tentu menang jika digabung (contoh di atas: 1 dan 3). Untuk gabungan permainan selalu pakai nilai Grundy, bukan sekadar M/K.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Baca aturan akhir dengan teliti.</strong> "Yang tidak bisa melangkah kalah" (normal) berbeda dengan "yang mengambil batu terakhir kalah" (misère). Teori Grundy hanya berlaku untuk versi normal.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'variasi yang sering muncul', 'desc' => 'Misère, Nim tangga, permainan di DAG, dan strategi cermin.'])

<section class="lesson-section" id="variasi" data-toc="Variasi Nim">
    <h2>Variasi Nim</h2>
    <div class="prose"><p><strong>Nim misère</strong> hampir sama dengan Nim biasa; yang berbeda hanya akhir permainan ketika semua tumpukan tinggal 0 atau 1 batu.</p></div>
    @include('lessons.code', ['cpp' => $gmMisere, 'js' => null, 'py' => null])
    <div class="prose"><p><strong>Nim tangga</strong> terlihat rumit, tetapi tangga bernomor genap tidak berpengaruh sama sekali.</p></div>
    @include('lessons.code', ['cpp' => $gmStair, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="dag" data-toc="Permainan di DAG">
    <h2>Permainan di Graph Berarah</h2>
    <div class="prose"><p>Setiap permainan tanpa siklus bisa dimodelkan sebagai graph: simpul = posisi, sisi = langkah. Nilai Grundy dihitung dengan memo seperti DP di DAG.</p></div>
    @include('lessons.code', ['cpp' => $gmDag, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Ambil 1..K</h4><p>Kalah tepat saat n habis dibagi K + 1.</p></div>
        <div class="pattern"><h4>Nim</h4><p>XOR semua tumpukan; 0 berarti kalah.</p></div>
        <div class="pattern"><h4>Langkah dari himpunan S</h4><p>Grundy dengan mex; periodik untuk S terbatas.</p></div>
        <div class="pattern"><h4>Memecah tumpukan</h4><p>Grundy(x) = mex dari g(a) XOR g(b) untuk setiap pecahan a + b = x.</p></div>
        <div class="pattern"><h4>Papan simetris</h4><p>Strategi cermin: pemain kedua meniru lawan secara simetris.</p></div>
        <div class="pattern"><h4>Tidak tahu polanya</h4><p>Cetak tabel brute force kecil, cari periode, uji, baru pakai.</p></div>
    </div>
    <div class="callout note">
        <span class="callout-icon">💡</span>
        <p><strong>Strategi cermin.</strong> Pada papan atau susunan yang simetris, pemain kedua sering bisa menang dengan selalu meniru langkah lawan di posisi pasangannya. Contoh: dua tumpukan sama besar dalam Nim, atau koin di meja bundar. Selalu periksa simetri sebelum menghitung Grundy.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Posisi menang cukup punya SATU langkah ke posisi kalah.">
        <p class="quiz-q">Kapan sebuah posisi disebut posisi menang?</p>
        <div class="quiz-options">
            <button class="quiz-option">Jika semua langkahnya menuju posisi kalah</button>
            <button class="quiz-option">Jika batunya masih banyak</button>
            <button class="quiz-option">Jika ada paling sedikit satu langkah menuju posisi kalah</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="3 XOR 5 XOR 6 = 0 (011 ^ 101 = 110, 110 ^ 110 = 0), jadi pemain pertama kalah.">
        <p class="quiz-q">Nim dengan tumpukan 3, 5, 6. Siapa menang?</p>
        <div class="quiz-options">
            <button class="quiz-option">Pemain kedua</button>
            <button class="quiz-option">Pemain pertama</button>
            <button class="quiz-option">Tergantung langkah pertama</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="mex{0, 1, 3} = 2, bilangan cacah terkecil yang tidak muncul.">
        <p class="quiz-q">Dari posisi x bisa ke posisi bernilai Grundy 0, 1, dan 3. Berapa g[x]?</p>
        <div class="quiz-options">
            <button class="quiz-option">4</button>
            <button class="quiz-option">2</button>
            <button class="quiz-option">0</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
