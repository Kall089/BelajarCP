@php
    $trSteps = [
        ['Simpul disimpan di array', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000005;   // paling banyak 1 + total panjang semua kata
int anak[MAKS][26], cnt[MAKS], akhir[MAKS], jumlahSimpul = 1;

CPP, <<<'TXT'
<p>Setiap simpul diberi nomor. Simpul 0 adalah <strong>akar</strong> (awalan kosong). <code>anak[v][x]</code> adalah nomor simpul yang dicapai dari v lewat huruf ke-x ('a' = 0, …, 'z' = 25). Nilai 0 berarti sisi itu belum ada; aman, karena akar tidak pernah menjadi anak siapa pun.</p>
<ul>
    <li><code>cnt[v]</code>: banyak kata yang <em>melewati</em> simpul v, yaitu banyak kata yang diawali awalan milik v.</li>
    <li><code>akhir[v]</code>: banyak kata yang <em>berakhir</em> tepat di v (dipakai untuk "apakah kata ini ada di kamus?").</li>
</ul>
<p>Setiap huruf yang disisipkan membuat paling banyak satu simpul baru, jadi ukuran array cukup 1 + total panjang kata.</p>
TXT],
        ['Menyisipkan kata', <<<'CPP'
void sisip(const string& w) {
    int v = 0;
    for (char c : w) {
        int x = c - 'a';
        if (!anak[v][x]) anak[v][x] = jumlahSimpul++;
        v = anak[v][x];
        cnt[v]++;
    }
    akhir[v]++;
}

CPP, <<<'TXT'
<p>Telusuri huruf demi huruf dari akar. Bila sisinya belum ada, buat simpul baru; bila sudah ada, berarti awalan ini sudah dipakai kata lain, cukup ikuti. Setiap simpul yang dilewati mendapat <code>cnt++</code> karena kata w diawali awalan simpul tersebut.</p>
<p>Waktu O(|w|), tidak bergantung pada isi kamus.</p>
TXT],
        ['Menghitung kata dengan awalan p', <<<'CPP'
int hitungAwalan(const string& p) {
    int v = 0;
    for (char c : p) {
        v = anak[v][c - 'a'];
        if (v == 0) return 0;
    }
    return cnt[v];
}

CPP, <<<'TXT'
<p>Telusuri p dari akar. Jika suatu sisi tidak ada (<code>v</code> kembali ke 0), tidak ada kata yang diawali p. Jika semua huruf berhasil diikuti, simpul terakhir mewakili awalan p, dan <code>cnt</code>-nya langsung menjadi jawaban.</p>
TXT],
        ['Program utama', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    for (int i = 0; i < n; i++) {
        string w;
        cin >> w;
        sisip(w);
    }
    string out;
    while (q--) {
        string p;
        cin >> p;
        out += to_string(hitungAwalan(p)) + "\n";
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Kata yang sama boleh disisipkan dua kali; cnt ikut bertambah dua, sesuai aturan "setiap kata dihitung sebanyak ia tercatat". Total waktu O(total panjang kata + total panjang awalan).</p>
TXT],
    ];

    $trPy = <<<'PY'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    anak = [{}]          # anak[v] = {huruf: simpul}
    cnt = [0]
    for i in range(2, 2 + n):
        v = 0
        for c in data[i]:
            nx = anak[v].get(c)
            if nx is None:
                nx = len(anak)
                anak[v][c] = nx
                anak.append({})
                cnt.append(0)
            v = nx
            cnt[v] += 1
    out = []
    for i in range(2 + n, 2 + n + q):
        v = 0
        for c in data[i]:
            v = anak[v].get(c, -1)
            if v < 0:
                break
        out.append(cnt[v] if v >= 0 else 0)
    print("\n".join(map(str, out)))

main()
PY;

    $trSaran = <<<'CPP'
// Kata terkecil (urutan kamus) yang diawali p, atau "-" bila tidak ada.
string saran(const string& p) {
    int v = 0;
    for (char c : p) {
        v = anak[v][c - 'a'];
        if (v == 0 || cnt[v] == 0) return "-";   // cnt 0: semua kata di sini sudah dihapus
    }
    string hasil = p;
    while (!akhir[v]) {                      // p sendiri belum kata: turun ke huruf terkecil
        int x = 0;
        while (!anak[v][x] || !cnt[anak[v][x]]) x++;   // pasti ada, karena cnt[v] > 0
        hasil += char('a' + x);
        v = anak[v][x];
    }
    return hasil;                            // kata yang menjadi awalan kata lain lebih kecil
}
CPP;

    $trHapus = <<<'CPP'
// Menghapus satu salinan kata w (w dijamin ada). Simpul tidak dibuang,
// cukup kurangi penghitungnya; simpul dengan cnt 0 dianggap tidak ada.
void hapus(const string& w) {
    int v = 0;
    for (char c : w) {
        v = anak[v][c - 'a'];
        cnt[v]--;
    }
    akhir[v]--;
}
// Fungsi lain wajib memeriksa cnt juga, seperti saran() di atas:
// if (v == 0 || cnt[v] == 0) return 0;
CPP;

    $trXor = <<<'CPP'
// Trie biner: setiap bilangan disimpan sebagai 30 bit, dari bit tertinggi.
const int LOG = 30;
int ke[100000 * LOG + 5][2], total = 1;     // simpul ≤ 1 + N · LOG

void sisipBit(int x) {
    int v = 0;
    for (int b = LOG - 1; b >= 0; b--) {
        int bit = x >> b & 1;
        if (!ke[v][bit]) ke[v][bit] = total++;
        v = ke[v][bit];
    }
}

// XOR terbesar antara x dan salah satu bilangan yang sudah disisipkan.
int xorTerbesar(int x) {
    int v = 0, hasil = 0;
    for (int b = LOG - 1; b >= 0; b--) {
        int bit = x >> b & 1;
        if (ke[v][bit ^ 1]) {                // bit berlawanan ada: bit b hasil menjadi 1
            hasil |= 1 << b;
            v = ke[v][bit ^ 1];
        } else {
            v = ke[v][bit];
        }
    }
    return hasil;
}

// Pasangan dengan XOR terbesar: setiap a[i] dibandingkan dengan semua a[j], j < i.
int best = 0;
sisipBit(a[0]);
for (int i = 1; i < n; i++) {
    best = max(best, xorTerbesar(a[i]));
    sisipBit(a[i]);
}
// a = {3, 10, 5, 25, 2, 8}  ->  28  (5 XOR 25)
CPP;

    $trSub = <<<'CPP'
// XOR subarray a[l..r] = P[r + 1] ^ P[l], dengan P[0] = 0 dan P[i + 1] = P[i] ^ a[i].
// Jadi jawabannya = XOR terbesar dari dua prefiks, persis soal pasangan di atas.
int p = 0, best = 0;
sisipBit(0);                                 // P[0]: subarray yang dimulai di indeks 0
for (int i = 0; i < n; i++) {
    p ^= a[i];
    best = max(best, xorTerbesar(p));
    sisipBit(p);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Membangun trie (pohon awalan) dengan array dan menjelaskan arti setiap simpul.</li>
        <li>Menjawab "berapa kata yang diawali p?" dalam O(|p|), berapa pun ukuran kamus.</li>
        <li>Mencari kata terkecil dengan awalan tertentu dan menghapus kata dari trie.</li>
        <li>Memakai <strong>trie biner</strong> untuk XOR terbesar dari pasangan dan dari subarray.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'pohon awalan', 'desc' => 'Kata-kata yang berawalan sama berbagi jalur yang sama.'])

<section class="lesson-section" id="masalah" data-toc="Masalahnya">
    <h2>Kamus dan Fitur Saran Kata</h2>
    <div class="prose">
        <p>Bayangkan kamus berisi 10<sup>5</sup> kata dan 10<sup>5</sup> pertanyaan "ada berapa kata yang diawali <code>ba</code>?". Membandingkan setiap pertanyaan dengan setiap kata butuh 10<sup>10</sup> perbandingan, jauh terlalu lambat.</p>
        <p>Perhatikan bahwa "bola", "bolu", dan "bot" sama-sama diawali "bo". Daripada menyimpan awalan itu tiga kali, <strong>trie</strong> menyimpannya sekali sebagai jalur dari akar. Setiap simpul mewakili tepat satu awalan, sehingga pertanyaan tentang awalan cukup dijawab dengan berjalan menyusuri hurufnya.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Akar</b><span>Simpul 0, mewakili awalan kosong.</span></div>
        <div class="term"><b>Sisi</b><span>Satu huruf. Dari satu simpul paling banyak ada satu sisi per huruf.</span></div>
        <div class="term"><b>Simpul v</b><span>Awalan yang dibaca dari akar sampai v. Simpul "bo" adalah anak 'o' dari simpul "b".</span></div>
        <div class="term"><b>cnt[v]</b><span>Banyak kata di kamus yang diawali awalan milik v.</span></div>
        <div class="term"><b>akhir[v]</b><span>Banyak kata yang sama persis dengan awalan milik v.</span></div>
    </div>
</section>

<section class="lesson-section" id="ide" data-toc="Ide Trie">
    <h2>Satu Simpul, Satu Awalan</h2>
    <div class="prose">
        <p>Menyisipkan kata berarti berjalan dari akar mengikuti huruf-hurufnya, membuat simpul baru hanya bila jalurnya belum ada. Karena setiap kata yang diawali p pasti melewati simpul p, cukup menambah <code>cnt</code> di setiap simpul yang dilewati saat menyisipkan. Pertanyaan awalan kemudian dijawab dengan membaca satu angka.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Biaya operasi trie bergantung pada <strong>panjang kata</strong>, bukan pada banyaknya kata. Kamus 10 kata atau 10<sup>6</sup> kata, menanyakan awalan sepanjang 3 huruf tetap butuh 3 langkah.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Angka di setiap simpul adalah <code>cnt</code>. Perhatikan saat kata kedua ("bolu") disisipkan: tiga huruf pertamanya mengikuti sisi yang sudah ada, dan hanya huruf terakhir yang membuat simpul baru. Coba ganti awalannya, misalnya <code>bu</code>, <code>bol</code>, atau <code>c</code>.</p></div>
    <div data-viz="trie"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Trie dengan array: sisipkan kata, lalu jawab pertanyaan awalan.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Input: N kata dan Q awalan. Untuk setiap awalan, cetak banyak kata yang diawali awalan itu.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Trie: menghitung kata berawalan p',
        'steps' => $trSteps,
        'sample' => ['input' => "6 5\nbola\nbolu\nbot\nbuku\nbus\nbola\nbo\nbu\nbol\nc\nbola\n", 'output' => "4\n2\n3\n0\n2\n"],
        'py' => $trPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: bola, bolu, bot</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Kata</th><th>Huruf</th><th>Sisi ada?</th><th>Simpul</th><th>cnt setelahnya</th></tr>
            <tr><td>bola</td><td>b, o, l, a</td><td>belum semua</td><td>buat 1, 2, 3, 4</td><td>1, 1, 1, 1</td></tr>
            <tr class="hl"><td>bolu</td><td>b, o, l</td><td>ada</td><td>ikuti 1, 2, 3</td><td>2, 2, 2</td></tr>
            <tr><td>bolu</td><td>u</td><td>belum</td><td>buat 5</td><td>1</td></tr>
            <tr class="hl"><td>bot</td><td>b, o</td><td>ada</td><td>ikuti 1, 2</td><td>3, 3</td></tr>
            <tr><td>bot</td><td>t</td><td>belum</td><td>buat 6</td><td>1</td></tr>
            <tr class="ok"><td colspan="3">Pertanyaan "bo": b → 1, o → 2</td><td>simpul 2</td><td>jawaban cnt[2] = 3</td></tr>
        </table>
    </div>
    <div class="prose"><p>Tiga kata dengan total 11 huruf hanya membutuhkan 7 simpul (termasuk akar), karena awalan "bo" dan "bol" dipakai bersama.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Sisipkan / hapus kata w</td><td><code>O(|w|)</code></td><td>Satu langkah per huruf.</td></tr>
        <tr><td>Hitung kata berawalan p</td><td><code>O(|p|)</code></td><td>Tidak bergantung pada N.</td></tr>
        <tr><td>Memori</td><td><code>O(L · Σ)</code></td><td>L = total panjang kata, Σ = ukuran alfabet (26).</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Hitung memorinya.</strong> <code>int anak[10<sup>6</sup>][26]</code> memakan sekitar 104 MB. Itu masih muat pada batas 256 MB, tetapi tidak pada 64 MB. Jika total huruf besar dan memori ketat, pakai <code>map&lt;char,int&gt;</code> per simpul (lebih lambat) atau kecilkan alfabet.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Ukuran array dari total huruf, bukan dari N.</strong> 10<sup>5</sup> kata sepanjang 10 bisa membuat 10<sup>6</sup> simpul. Untuk trie biner, setiap bilangan menambah sampai LOG simpul, jadi siapkan 1 + N · LOG.</p>
    </div>
    <div class="callout note">
        <span class="callout-icon">💡</span>
        <p><strong>Tidak selalu butuh trie.</strong> Hanya menghitung kata berawalan p bisa juga dengan mengurutkan kamus lalu dua kali <code>lower_bound</code> (antara p dan p diikuti huruf setelah 'z'). Trie unggul saat kata ditambah atau dihapus di tengah jalan, saat kamu perlu berjalan huruf demi huruf (saran kata), dan untuk soal XOR.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'saran kata, hapus, dan trie biner', 'desc' => 'Tiga penerapan yang paling sering muncul di soal.'])

<section class="lesson-section" id="saran" data-toc="Kata Terkecil">
    <h2>Kata Terkecil dengan Awalan p</h2>
    <div class="prose">
        <p>Fitur saran kata: setelah awalan p, tampilkan kata terkecil menurut urutan kamus. Dari simpul p, jika p sendiri sudah kata (<code>akhir &gt; 0</code>), itulah jawabannya, karena kata yang menjadi awalan kata lain selalu lebih kecil. Jika belum, turun lewat huruf terkecil yang tersedia dan ulangi. Simpul yang bukan akhir kata pasti punya anak, sebab ada kata yang melewatinya.</p>
    </div>
    @include('lessons.code', ['cpp' => $trSaran, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="hapus" data-toc="Menghapus Kata">
    <h2>Menghapus Kata</h2>
    <div class="prose"><p>Simpul tidak perlu benar-benar dibuang. Kurangi <code>cnt</code> di sepanjang jalurnya; simpul dengan cnt 0 berarti tidak dilewati kata mana pun dan diperlakukan seperti tidak ada.</p></div>
    @include('lessons.code', ['cpp' => $trHapus, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="xor" data-toc="Trie Biner & XOR">
    <h2>Trie Biner: XOR Terbesar</h2>
    <div class="prose">
        <p>Soal klasik: dari N bilangan, pilih dua yang XOR-nya terbesar. Mencoba semua pasangan O(N<sup>2</sup>). Simpan setiap bilangan sebagai string 30 bit (dari bit tertinggi) di trie dengan alfabet {0, 1}.</p>
        <p>Untuk x, kita ingin bit tertinggi hasil bernilai 1, yaitu memilih bilangan yang bit tertingginya berlawanan dengan x. Ini <strong>greedy yang benar</strong>: 2<sup>b</sup> lebih besar daripada jumlah semua bit di bawahnya (2<sup>b</sup> − 1), jadi memenangkan bit b lebih penting daripada semua bit yang lebih rendah. Di setiap tingkat, ambil sisi berlawanan bila ada, selain itu terpaksa sisi yang sama.</p>
    </div>
    @include('lessons.code', ['cpp' => $trXor, 'js' => null, 'py' => null])
    <div class="prose"><p>Untuk subarray, ubah ke prefiks XOR. XOR a[l..r] sama dengan P[r + 1] XOR P[l], sehingga soalnya kembali menjadi "pasangan dengan XOR terbesar" di antara prefiks. Jangan lupa menyisipkan P[0] = 0.</p></div>
    @include('lessons.code', ['cpp' => $trSub, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Hitung kata berawalan p</h4><p>cnt di simpul akhir p.</p></div>
        <div class="pattern"><h4>Apakah kata ada?</h4><p>akhir di simpul akhir w lebih dari 0.</p></div>
        <div class="pattern"><h4>Saran kata</h4><p>Dari simpul p, turun lewat huruf terkecil sampai akhir kata.</p></div>
        <div class="pattern"><h4>XOR terbesar pasangan</h4><p>Trie biner, pilih bit berlawanan dari atas.</p></div>
        <div class="pattern"><h4>XOR terbesar subarray</h4><p>Prefiks XOR, sisipkan P[0] = 0 lebih dulu.</p></div>
        <div class="pattern"><h4>Banyak pola sekaligus</h4><p>Aho–Corasick: trie dari semua pola ditambah tautan gagal seperti π pada KMP.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="a (1), k (2), u (3); 'apa' berbagi 'a' lalu p (4), a (5); 'api' berbagi 'ap' lalu i (6). Ditambah akar: 7 simpul.">
        <p class="quiz-q">Berapa simpul (termasuk akar) trie untuk kata "aku", "apa", dan "api"?</p>
        <div class="quiz-options">
            <button class="quiz-option">7</button>
            <button class="quiz-option">9</button>
            <button class="quiz-option">10</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Penelusuran hanya mengikuti huruf-huruf p, satu langkah per huruf. Banyaknya kata di kamus tidak berpengaruh.">
        <p class="quiz-q">Berapa waktu hitungAwalan(p) pada trie berisi N kata?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(N · |p|)</button>
            <button class="quiz-option">O(log N)</button>
            <button class="quiz-option">O(|p|)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="2^b lebih besar daripada 2^(b−1) + … + 2 + 1 = 2^b − 1, jadi hasil dengan bit b bernilai 1 selalu lebih besar daripada hasil mana pun dengan bit b bernilai 0.">
        <p class="quiz-q">Mengapa xorTerbesar boleh serakah memilih bit berlawanan mulai dari bit tertinggi?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena bilangan di trie sudah terurut</button>
            <button class="quiz-option">Karena satu bit tinggi bernilai lebih besar daripada gabungan semua bit di bawahnya</button>
            <button class="quiz-option">Karena XOR bersifat komutatif</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
