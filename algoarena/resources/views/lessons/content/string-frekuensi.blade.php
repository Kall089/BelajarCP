@php
    $sfSteps = [
        ['Frekuensi pola', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string t, p;
    cin >> t >> p;
    int n = t.size(), m = p.size();
    int need[26] = {0}, have[26] = {0};
    for (char c : p) need[c - 'a']++;
CPP, <<<'TXT'
<p><strong>Soal contoh "Anagram Tersembunyi":</strong> diberikan teks t dan pola p (huruf kecil). Ada berapa posisi i sehingga potongan <code>t[i..i+m−1]</code> adalah <strong>anagram</strong> dari p (huruf yang sama dengan banyak yang sama, urutan bebas)?</p>
<p>Dua string adalah anagram tepat ketika array frekuensi 26 hurufnya sama. <code>c − 'a'</code> mengubah huruf menjadi indeks 0..25.</p>
TXT],
        ['Hitung berapa huruf yang sudah cocok', <<<'CPP'

    int cocok = 0;                   // banyak huruf x dengan have[x] == need[x]
    for (int x = 0; x < 26; x++) cocok += (have[x] == need[x]);
    auto ubah = [&](int x, int d) {
        if (have[x] == need[x]) cocok--;
        have[x] += d;
        if (have[x] == need[x]) cocok++;
    };
CPP, <<<'TXT'
<p>Membandingkan 26 sel setiap kali jendela bergeser masih cepat (O(26n)), tetapi ada trik yang lebih rapi: simpan <code>cocok</code> = banyak huruf yang frekuensinya sudah sama. Setiap perubahan satu sel hanya bisa mengubah status huruf itu sendiri.</p>
TXT],
        ['Geser jendela', <<<'CPP'

    int jawab = 0;
    for (int i = 0; i < n; i++) {
        ubah(t[i] - 'a', +1);                 // huruf masuk
        if (i >= m) ubah(t[i - m] - 'a', -1); // huruf keluar
        if (i >= m - 1 && cocok == 26) jawab++;
    }
    cout << jawab << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Jendela berukuran tetap m. Setiap langkah: satu huruf masuk, satu keluar. Jika ke-26 huruf cocok, jendela itu anagram. Total O(n + m).</p>
<div class="wt-tip">Pola "frekuensi + jendela" menyelesaikan banyak soal string: substring dengan huruf unik, substring yang memuat semua huruf pola, dan seterusnya.</div>
TXT],
    ];

    $sfPy = <<<'PY'
import sys
t, p = sys.stdin.read().split()[:2]
n, m = len(t), len(p)
need = [0] * 26
have = [0] * 26
for c in p:
    need[ord(c) - 97] += 1
jawab = 0
for i in range(n):
    have[ord(t[i]) - 97] += 1
    if i >= m:
        have[ord(t[i - m]) - 97] -= 1
    if i >= m - 1 and have == need:
        jawab += 1
print(jawab)
PY;

    $sfBasic = <<<'CPP'
string s = "kasur rusak";
s.size();  s[0];  s.back();
s.substr(2, 3);                 // "sur" (mulai, panjang)
s.find("rus");                  // 6, atau string::npos jika tidak ada
reverse(s.begin(), s.end());
string r = s + "!";             // gabung
getline(cin, s);                // membaca satu baris penuh (dengan spasi)

// Palindrom: sama dengan kebalikannya
bool palindrom(const string& s) {
    for (int i = 0, j = (int)s.size() - 1; i < j; i++, j--)
        if (s[i] != s[j]) return false;
    return true;
}
CPP;

    $sfFreq = <<<'CPP'
int cnt[26] = {0};
for (char c : s) cnt[c - 'a']++;          // frekuensi huruf

// Anagram: frekuensi sama
// Bisa disusun menjadi palindrom: paling banyak SATU huruf berfrekuensi ganjil
int ganjil = 0;
for (int x = 0; x < 26; x++) ganjil += cnt[x] % 2;
bool bisaPalindrom = (ganjil <= 1);

// Frekuensi angka kecil (nilai <= 10^6): array jauh lebih cepat dari map
static int f[1000001];
for (int x : a) f[x]++;
CPP;

    $sfPrefix26 = <<<'CPP'
// 26 prefix sum: banyak huruf c di s[l..r] dalam O(1)
vector<array<int, 26>> pre(n + 1);
pre[0].fill(0);
for (int i = 0; i < n; i++) {
    pre[i + 1] = pre[i];
    pre[i + 1][s[i] - 'a']++;
}
int banyak = pre[r + 1][c - 'a'] - pre[l][c - 'a'];
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengolah string di C++: substr, find, reverse, membaca baris, dan memeriksa palindrom.</li>
        <li>Memakai array frekuensi 26 huruf untuk anagram dan "bisa disusun menjadi palindrom".</li>
        <li>Menggabungkan frekuensi dengan jendela geser dan dengan prefix sum.</li>
        <li>Memilih array frekuensi alih-alih <code>map</code> ketika domain nilainya kecil.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'kotak surat, bukan perbandingan', 'desc' => 'Menghitung kemunculan sebagai senjata utama.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Kotak Surat">
    <h2>Intuisi: Kotak Surat</h2>
    <div class="prose">
        <p>Untuk memeriksa apakah dua tumpukan surat berisi alamat yang sama, kamu tidak perlu membandingkan setiap surat dengan setiap surat lain. Siapkan 26 kotak (satu per huruf), masukkan setiap surat ke kotaknya, lalu bandingkan isi kotak. <strong>Satu sapuan untuk memasukkan, satu sapuan untuk membaca.</strong></p>
        <p>Itulah array frekuensi. Karena huruf kecil hanya 26, array <code>cnt[26]</code> menggantikan <code>map&lt;char,int&gt;</code> dan jauh lebih cepat.</p>
    </div>
    @include('lessons.code', ['cpp' => $sfBasic, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $sfFreq, 'js' => null, 'py' => null])
    <div class="term-grid">
        <div class="term"><b>Anagram</b><span>Frekuensi setiap huruf sama. "kasur" dan "rusak" adalah anagram.</span></div>
        <div class="term"><b>Palindrom</b><span>Sama jika dibaca terbalik: "katak", "kasur rusak" (tanpa spasi).</span></div>
        <div class="term"><b>Bisa jadi palindrom</b><span>Paling banyak satu huruf berfrekuensi ganjil (hurufnya ditaruh di tengah).</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Mencari Anagram dengan Jendela</h2>
    <div class="prose"><p>Jendela seukuran pola bergeser satu huruf setiap langkah; hanya dua sel frekuensi yang berubah. Di Mode Tebak, tebak apakah jendela saat ini anagram dari pola.</p></div>
    <div data-viz="freq-count"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'frekuensi di C++', 'desc' => 'Jendela anagram dan prefix sum 26 huruf.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Anagram Tersembunyi</h2>
    @include('lessons.walkthrough', [
        'title' => 'Frekuensi + jendela ukuran tetap',
        'steps' => $sfSteps,
        'sample' => ['input' => "cbaebabacd abc\n", 'output' => "2\n"],
        'py' => $sfPy,
    ])
</section>

<section class="lesson-section" id="prefix26" data-toc="26 Prefix Sum">
    <h2>26 Prefix Sum: Huruf di Potongan</h2>
    <div class="prose"><p>"Berapa huruf 'a' di s[l..r]?" untuk banyak pertanyaan: siapkan satu prefix sum untuk setiap huruf. Memori 26 × (n + 1) bilangan, setiap pertanyaan O(1).</p></div>
    @include('lessons.code', ['cpp' => $sfPrefix26, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Wadah</th><th>Per operasi</th><th>Kapan</th></tr>
        <tr><td><code>int cnt[26]</code></td><td><code>O(1)</code>, sangat cepat</td><td>Huruf kecil</td></tr>
        <tr><td><code>int f[1000001]</code></td><td><code>O(1)</code></td><td>Nilai kecil (≤ ~10<sup>7</sup>)</td></tr>
        <tr><td><code>unordered_map</code> / <code>map</code></td><td><code>O(1)</code> rata-rata / <code>O(log n)</code></td><td>Nilai besar atau string</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Multi test case.</strong> Mengosongkan array frekuensi besar dengan <code>memset</code> di setiap test bisa TLE jika banyak test. Kosongkan hanya sel yang tadi dipakai (simpan daftarnya), atau pakai array kecil.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>s.substr di dalam loop</strong> membuat string baru (O(panjang)) setiap kali. Membandingkan <code>s.substr(i, m) == p</code> untuk semua i sudah O(n·m).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>cin &gt;&gt; s</strong> berhenti di spasi. Untuk membaca kalimat, pakai <code>getline</code>, dan ingat <code>cin.ignore()</code> jika sebelumnya membaca angka.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'menghitung pasangan dengan frekuensi', 'desc' => 'Pola pasangan berjumlah atau berselisih K.'])

<section class="lesson-section" id="pasangan" data-toc="Pasangan dengan Frekuensi">
    <h2>Menghitung Pasangan dengan Frekuensi</h2>
    <div class="proof">
        <p>"Berapa pasangan i &lt; j dengan a[i] + a[j] = K?" Jalan dari kiri ke kanan; untuk setiap j, pasangan yang berakhir di j adalah banyak i sebelumnya dengan a[i] = K − a[j]. Itu persis <code>cnt[K − a[j]]</code> sebelum a[j] dimasukkan. Setiap pasangan terhitung tepat sekali, di indeks yang lebih besar.</p>
        <p>Jika pasangan dihitung dengan "semua x, semua y" lalu dibagi dua, hati-hati dengan x = y: pasangan (x, x) butuh C(cnt[x], 2), bukan cnt[x]².</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Anagram</h4><p>Bandingkan array frekuensi.</p></div>
        <div class="pattern"><h4>Bisa jadi palindrom</h4><p>Huruf berfrekuensi ganjil ≤ 1.</p></div>
        <div class="pattern"><h4>Huruf di potongan</h4><p>26 prefix sum.</p></div>
        <div class="pattern"><h4>Mode (paling sering)</h4><p>Satu sapuan atas cnt.</p></div>
        <div class="pattern"><h4>Pasangan berjumlah K</h4><p>cnt[K − a[j]] sebelum memasukkan a[j].</p></div>
        <div class="pattern"><h4>Frekuensi dari frekuensi</h4><p>Berapa nilai yang muncul tepat k kali?</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="Frekuensi: a=2, b=2, c=1. Hanya c yang ganjil (≤ 1), jadi bisa: misalnya 'abcba'.">
        <p class="quiz-q">Bisakah huruf-huruf "aabbc" disusun menjadi palindrom?</p>
        <div class="quiz-options">
            <button class="quiz-option">Bisa</button>
            <button class="quiz-option">Tidak bisa</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="'listen' dan 'silent' punya frekuensi huruf yang sama persis: e, i, l, n, s, t masing-masing satu.">
        <p class="quiz-q">Pasangan mana yang anagram?</p>
        <div class="quiz-options">
            <button class="quiz-option">"apel" dan "lapel"</button>
            <button class="quiz-option">"tikus" dan "kutu"</button>
            <button class="quiz-option">"listen" dan "silent"</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="pre[r+1][c] − pre[l][c] memberi banyak huruf c di s[l..r] dalam O(1).">
        <p class="quiz-q">Dengan 26 prefix sum, berapa biaya satu pertanyaan "banyak huruf c di s[l..r]"?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(r − l)</button>
            <button class="quiz-option">O(1)</button>
            <button class="quiz-option">O(26 · n)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
