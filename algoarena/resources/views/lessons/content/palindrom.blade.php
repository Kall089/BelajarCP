@php
    $plSteps = [
        ['Sisipkan pemisah', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

vector<int> manacher(const string& s) {
    string t = "#";
    for (char c : s) {
        t += c;
        t += '#';
    }
    int n = t.size();
    vector<int> p(n, 0);

CPP, <<<'TXT'
<p>Palindrom panjang ganjil berpusat di sebuah huruf ("aba"), palindrom panjang genap berpusat di antara dua huruf ("abba"). Agar keduanya ditangani sama, sisipkan '#' di antara semua huruf: "abba" menjadi "#a#b#b#a#". Sekarang setiap palindrom di s berpusat tepat di satu posisi t.</p>
<p><code>p[i]</code> = jari-jari palindrom terpanjang di t yang berpusat di i. Keuntungan lain: jari-jari di t <strong>sama dengan panjang</strong> palindrom aslinya di s.</p>
TXT],
        ['Pinjam dari cermin, lalu perluas', <<<'CPP'
    for (int i = 0, l = 0, r = -1; i < n; i++) {
        int k = (i > r) ? 0 : min(p[l + r - i], r - i);
        while (i - k - 1 >= 0 && i + k + 1 < n && t[i - k - 1] == t[i + k + 1]) k++;
        p[i] = k;
        if (i + k > r) {
            l = i - k;
            r = i + k;
        }
    }
    return p;
}

CPP, <<<'TXT'
<p>[l, r] adalah palindrom yang sudah ditemukan dengan ujung kanan paling jauh (sebut saja "kotak"). Jika i ada di dalam kotak, bagian kotak di sekitar i adalah bayangan cermin dari bagian di sekitar <code>j = l + r − i</code>. Jadi p[i] paling sedikit min(p[j], r − i), tanpa perlu membandingkan huruf.</p>
<p>Setelah itu perluas secara manual. Setiap perbandingan yang berhasil di luar kotak menggeser r ke kanan, dan r tidak pernah mundur, sehingga total semua perluasan O(n). Inilah inti Manacher: O(n), bukan O(n<sup>2</sup>).</p>
TXT],
        ['Ambil palindrom terpanjang', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    vector<int> p = manacher(s);
    int panjang = 0, mulai = 0;
    for (int i = 0; i < (int)p.size(); i++)
        if (p[i] > panjang) {
            panjang = p[i];
            mulai = (i - p[i]) / 2;
        }
    cout << panjang << '\n' << s.substr(mulai, panjang) << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Palindrom berpusat di i dengan jari-jari p[i] menempati t[i − p[i] .. i + p[i]]. Huruf asli s[x] berada di t[2x + 1], jadi palindrom itu dimulai di s pada indeks <code>(i − p[i]) / 2</code>. Memakai &gt; (bukan ≥) memilih palindrom terpanjang yang paling kiri.</p>
TXT],
    ];

    $plPy = <<<'PY'
import sys

def main():
    s = sys.stdin.readline().strip()
    t = "#" + "#".join(s) + "#"
    n = len(t)
    p = [0] * n
    l, r = 0, -1
    for i in range(n):
        k = 0 if i > r else min(p[l + r - i], r - i)
        while i - k - 1 >= 0 and i + k + 1 < n and t[i - k - 1] == t[i + k + 1]:
            k += 1
        p[i] = k
        if i + k > r:
            l, r = i - k, i + k
    panjang = max(p)
    i = p.index(panjang)
    mulai = (i - panjang) // 2
    print(panjang)
    print(s[mulai:mulai + panjang])

main()
PY;

    $plExpand = <<<'CPP'
// Ekspansi dari pusat: O(n^2), sering cukup untuk n ≤ 5000.
int terpanjang = 0;
for (int c = 0; c < 2 * n - 1; c++) {      // pusat ganjil (c genap) dan genap (c ganjil)
    int l = c / 2, r = l + c % 2;
    while (l >= 0 && r < n && s[l] == s[r]) {
        terpanjang = max(terpanjang, r - l + 1);
        l--;
        r++;
    }
}
CPP;

    $plUse = <<<'CPP'
vector<int> p = manacher(s);               // t = "#s0#s1#...", |t| = 2n + 1

// 1) Banyak substring palindrom (dihitung per posisi): pusat i menyumbang (p[i] + 1) / 2.
long long banyak = 0;
for (int x : p) banyak += (x + 1) / 2;

// 2) Apakah s[a..b] palindrom? Pusatnya di t[a + b + 1]; cukup cek jari-jarinya.
auto palindrom = [&](int a, int b) { return p[a + b + 1] >= b - a + 1; };

// 3) Akhiran palindrom terpanjang: pusat i yang mencapai ujung kanan t (i + p[i] == 2n).
int akhiran = 0;
for (int i = 0; i < (int)p.size(); i++)
    if (i + p[i] == 2 * n) akhiran = max(akhiran, p[i]);
// Huruf minimum yang perlu ditambah di AKHIR agar s menjadi palindrom = n - akhiran:
// bagian awal di luar akhiran palindrom itu dicerminkan ke belakang ("abac" + "aba").
CPP;

    $plDp = <<<'CPP'
// Partisi menjadi palindrom sesedikit mungkin, n ≤ 5000: O(n^2) dengan cek O(1) dari Manacher.
vector<int> dp(n + 1, INT_MAX);            // dp[j] = potongan minimum untuk s[0..j-1]
dp[0] = 0;
for (int j = 1; j <= n; j++)
    for (int i = 0; i < j; i++)            // potongan terakhir s[i..j-1]
        if (dp[i] + 1 < dp[j] && palindrom(i, j - 1)) dp[j] = dp[i] + 1;
// dp[n] = banyak potongan; banyak "pemotongan" = dp[n] - 1
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mencari palindrom dengan ekspansi dari pusat dalam O(n<sup>2</sup>).</li>
        <li>Menjalankan <strong>algoritma Manacher</strong> dalam O(n) dan menjelaskan mengapa cepat.</li>
        <li>Memakai hasil Manacher untuk menghitung semua substring palindrom dan mengecek s[a..b] dalam O(1).</li>
        <li>Menyelesaikan soal akhiran palindrom dan partisi palindrom.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'pusat palindrom', 'desc' => 'Setiap palindrom tumbuh dari pusatnya.'])

<section class="lesson-section" id="ide" data-toc="Ekspansi dari Pusat">
    <h2>Ekspansi dari Pusat</h2>
    <div class="prose">
        <p>Palindrom dibaca sama dari depan dan belakang: "kasur rusak", "abba", "level". Cara memeriksanya paling alami bukan dari ujung, melainkan dari <strong>tengah</strong>: jika s[l..r] palindrom dan s[l − 1] = s[r + 1], maka s[l − 1..r + 1] juga palindrom.</p>
        <p>String sepanjang n punya 2n − 1 pusat: n huruf (palindrom ganjil) dan n − 1 celah (palindrom genap). Memperluas dari setiap pusat memberi O(n<sup>2</sup>), cukup untuk n sampai beberapa ribu.</p>
    </div>
    @include('lessons.code', ['cpp' => $plExpand, 'js' => null, 'py' => null])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Pemborosan ekspansi biasa: palindrom panjang membuat kita membandingkan ulang huruf yang sama berkali-kali. Padahal di dalam palindrom, bagian kanan adalah <strong>cermin</strong> bagian kiri. Manacher memanfaatkan cermin ini.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Perhatikan langkah bertanda kunci: p[i] langsung diisi dari cermin j tanpa membandingkan huruf. Coba juga <code>aaaaaaa</code>; ekspansi biasa butuh O(n<sup>2</sup>) perbandingan, Manacher hanya O(n).</p></div>
    <div data-viz="manacher"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'algoritma Manacher', 'desc' => 'Semua palindrom maksimal dalam waktu linear.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Palindrom Terpanjang</h2>
    <div class="prose"><p>Input: string s (huruf kecil, sampai 10<sup>6</sup> karakter). Cetak panjang substring palindrom terpanjang, lalu substring itu (yang paling kiri jika ada beberapa).</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Manacher: palindrom terpanjang',
        'steps' => $plSteps,
        'sample' => ['input' => "babadxyzzyx\n", 'output' => "6\nxyzzyx\n"],
        'py' => $plPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: s = "abaaba"</h2>
    <div class="prose"><p>t = "#a#b#a#a#b#a#" (indeks 0..12).</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>t[i]</th><th>Awal k</th><th>Proses</th><th>p[i]</th><th>Kotak</th></tr>
            <tr><td>1</td><td>a</td><td>0 (di luar kotak)</td><td>t[0] = t[2] = '#', lalu batas</td><td>1</td><td>[0, 2]</td></tr>
            <tr><td>3</td><td>b</td><td>0</td><td>perluas sampai "#a#b#a#"</td><td>3</td><td>[0, 6]</td></tr>
            <tr class="hl"><td>5</td><td>a</td><td>min(p[1], 6 − 5) = 1</td><td>pinjam dari cermin, lalu cek t[3] ≠ t[7]</td><td>1</td><td>[0, 6]</td></tr>
            <tr class="ok"><td>6</td><td>#</td><td>min(p[0], 0) = 0</td><td>perluas sampai seluruh t</td><td>6</td><td>[0, 12]</td></tr>
            <tr class="hl"><td>9</td><td>b</td><td>min(p[3], 12 − 9) = 3</td><td>langsung 3 dari cermin; i + k + 1 = 13 sudah di luar t, berhenti</td><td>3</td><td>[0, 12]</td></tr>
        </table>
    </div>
    <div class="prose"><p>p[6] = 6: seluruh "abaaba" adalah palindrom genap, berpusat di '#' antara kedua 'a' di tengah. Di posisi 9, jawaban 3 didapat dari cermin (posisi 3) tanpa membandingkan ulang.</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Cek semua substring</td><td><code>O(n<sup>3</sup>)</code></td><td>Hanya untuk brute force pembanding.</td></tr>
        <tr><td>Ekspansi dari pusat</td><td><code>O(n<sup>2</sup>)</code></td><td>Sederhana, n ≤ 5000.</td></tr>
        <tr><td>Hash + binary search per pusat</td><td><code>O(n log n)</code></td><td>Ada peluang tabrakan kecil.</td></tr>
        <tr class="ok"><td>Manacher</td><td><code>O(n)</code></td><td>Pasti benar dan paling cepat.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa kasus genap.</strong> Ekspansi hanya dari huruf melewatkan palindrom seperti "abba". Versi '#' menghindari kesalahan ini sepenuhnya.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Banyak palindrom bisa sangat besar.</strong> String "aaa…a" sepanjang 10<sup>6</sup> punya sekitar 5 · 10<sup>11</sup> substring palindrom: pakai <code>long long</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'memakai hasil Manacher', 'desc' => 'Menghitung, mengecek, dan memotong menjadi palindrom.'])

<section class="lesson-section" id="pakai" data-toc="Penerapan">
    <h2>Penerapan Array p</h2>
    <div class="prose"><p>Setelah p dihitung sekali dalam O(n), banyak pertanyaan langsung terjawab. Pusat dengan jari-jari p[i] memuat (p[i] + 1) / 2 palindrom bersarang (panjang p[i], p[i] − 2, …, sampai 1 atau 2), baik pusatnya huruf maupun '#'.</p></div>
    @include('lessons.code', ['cpp' => $plUse, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="partisi" data-toc="Partisi Palindrom">
    <h2>Partisi Palindrom</h2>
    <div class="prose"><p>Potong s menjadi bagian-bagian yang semuanya palindrom, sesedikit mungkin. Ini DP biasa: bagian terakhir s[i..j−1] harus palindrom. Cek palindrom O(1) dari Manacher membuat DP-nya O(n<sup>2</sup>).</p></div>
    @include('lessons.code', ['cpp' => $plDp, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Palindrom terpanjang</h4><p>maks p[i].</p></div>
        <div class="pattern"><h4>Banyak substring palindrom</h4><p>Jumlah (p[i] + 1) / 2.</p></div>
        <div class="pattern"><h4>Cek s[a..b]</h4><p>p[a + b + 1] ≥ b − a + 1.</p></div>
        <div class="pattern"><h4>Tambah huruf di akhir</h4><p>n − akhiran palindrom terpanjang.</p></div>
        <div class="pattern"><h4>Potong sesedikit mungkin</h4><p>DP O(n<sup>2</sup>) + cek O(1).</p></div>
        <div class="pattern"><h4>Palindrom berbeda (isi unik)</h4><p>Pohon palindrom (eertree) atau hash; paling banyak n palindrom berbeda.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap pusat di t: huruf untuk palindrom ganjil, '#' untuk palindrom genap. Tanpa '#', palindrom genap tidak punya pusat berupa huruf.">
        <p class="quiz-q">Mengapa Manacher versi ini menyisipkan '#' di antara huruf?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar string menjadi lebih pendek</button>
            <button class="quiz-option">Agar palindrom panjang genap juga punya pusat di satu posisi</button>
            <button class="quiz-option">Agar perbandingan huruf lebih cepat</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Perbandingan yang berhasil di luar kotak selalu menggeser r ke kanan, dan r paling jauh |t|, jadi total perbandingan O(n).">
        <p class="quiz-q">Mengapa total waktu Manacher O(n) walaupun ada while di dalam for?</p>
        <div class="quiz-options">
            <button class="quiz-option">Setiap perluasan yang berhasil menggeser batas kanan r yang tidak pernah mundur</button>
            <button class="quiz-option">Karena while paling banyak berjalan sekali</button>
            <button class="quiz-option">Karena string selalu pendek</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="'aaa' punya palindrom a, a, a (3), aa, aa (2), aaa (1): total 6.">
        <p class="quiz-q">Berapa banyak substring palindrom (dihitung per posisi) dari "aaa"?</p>
        <div class="quiz-options">
            <button class="quiz-option">3</button>
            <button class="quiz-option">4</button>
            <button class="quiz-option">6</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
