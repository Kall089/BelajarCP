@php
    $kpSteps = [
        ['Fungsi prefiks', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

vector<int> fungsiPrefiks(const string& s) {
    int n = s.size();
    vector<int> pi(n, 0);
    for (int i = 1; i < n; i++) {
        int k = pi[i - 1];
        while (k > 0 && s[i] != s[k]) k = pi[k - 1];
        if (s[i] == s[k]) k++;
        pi[i] = k;
    }
    return pi;
}

CPP, <<<'TXT'
<p><code>pi[i]</code> = panjang <strong>border</strong> terpanjang dari s[0..i]: awalan yang sekaligus akhiran, tetapi bukan seluruh string. Contoh "abcab" punya border "ab", jadi π = 2.</p>
<ul>
    <li>Border baru untuk s[0..i] pasti hasil memperpanjang suatu border lama dari s[0..i−1] dengan satu huruf. Coba yang terpanjang dulu, k = pi[i − 1].</li>
    <li>Jika s[i] ≠ s[k], border sepanjang k gagal. Border berikutnya yang lebih pendek adalah border dari border itu sendiri: <code>k = pi[k − 1]</code>.</li>
    <li>Jika cocok, border bertambah satu.</li>
</ul>
<p>k naik paling banyak 1 per langkah dan setiap lompatan mundur menurunkannya, sehingga total O(n).</p>
TXT],
        ['Gabungkan pola dan teks', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string teks, pola;
    cin >> teks >> pola;
    int m = pola.size();
    string gabung = pola + "#" + teks;
    vector<int> pi = fungsiPrefiks(gabung);

CPP, <<<'TXT'
<p>Trik paling praktis: tempelkan pola, sebuah pemisah yang tidak muncul di kedua string (<code>#</code>), lalu teks. Di bagian teks, π tidak pernah melebihi m karena border tidak bisa melewati <code>#</code>. Jadi <code>pi[i] == m</code> berarti tepat di posisi i sebuah salinan pola berakhir.</p>
TXT],
        ['Kumpulkan posisi kemunculan', <<<'CPP'
    vector<int> posisi;
    for (int i = m + 1; i < (int)gabung.size(); i++)
        if (pi[i] == m) posisi.push_back(i - 2 * m + 1);

    cout << posisi.size() << '\n';
    for (int i = 0; i < (int)posisi.size(); i++) cout << posisi[i] << (i + 1 < (int)posisi.size() ? ' ' : '\n');
    return 0;
}
CPP, <<<'TXT'
<p>Indeks i di <code>gabung</code> berpadanan dengan indeks i − m − 1 di teks (0-based). Kemunculan berakhir di sana dan panjangnya m, jadi mulai di i − 2m (0-based), atau i − 2m + 1 jika dihitung dari 1. Kemunculan yang tumpang tindih (seperti "aba" di "ababa") ikut terhitung.</p>
<p>Total O(n + m). Mencoba setiap posisi lalu membandingkan huruf demi huruf bisa O(n · m), misalnya teks "aaaa…a" dan pola "aaa…ab".</p>
TXT],
    ];

    $kpPy = <<<'PY'
import sys
data = sys.stdin.read().split()
teks, pola = data[0], data[1]
m = len(pola)
s = pola + "#" + teks
n = len(s)
pi = [0] * n
for i in range(1, n):
    k = pi[i - 1]
    while k and s[i] != s[k]:
        k = pi[k - 1]
    if s[i] == s[k]:
        k += 1
    pi[i] = k
posisi = [i - 2 * m + 1 for i in range(m + 1, n) if pi[i] == m]
print(len(posisi))
if posisi:
    print(*posisi)
PY;

    $kpZ = <<<'CPP'
// Fungsi Z: z[i] = panjang awalan terpanjang s yang dimulai di posisi i (z[0] = 0).
// [l, r) = kotak Z terkanan yang sudah ditemukan; isinya sama dengan s[0 .. r-l).
vector<int> fungsiZ(const string& s) {
    int n = s.size();
    vector<int> z(n, 0);
    for (int i = 1, l = 0, r = 0; i < n; i++) {
        if (i < r) z[i] = min(r - i, z[i - l]);              // pakai yang sudah diketahui
        while (i + z[i] < n && s[z[i]] == s[i + z[i]]) z[i]++;   // perpanjang manual
        if (i + z[i] > r) { l = i; r = i + z[i]; }
    }
    return z;
}
// Mencari pola: z dari pola + "#" + teks, kemunculan di posisi dengan z[i] == m.
CPP;

    $kpCount = <<<'CPP'
// Berapa kali setiap awalan s[0..k-1] muncul di s?
vector<int> cnt(n + 1, 0);
for (int i = 0; i < n; i++) cnt[pi[i]]++;            // akhiran terpanjang yang juga awalan
for (int k = n; k > 0; k--) cnt[pi[k - 1]] += cnt[k];   // border dari border juga muncul
for (int k = 1; k <= n; k++) cnt[k]++;               // kemunculan di posisi 0 sendiri
// s = "ababa": a 3 kali, ab 2, aba 2, abab 1, ababa 1
CPP;

    $kpPeriod = <<<'CPP'
// Periode terpendek: p = n − pi[n − 1].
// s = t t t ... t (salinan utuh) jika dan hanya jika n habis dibagi p.
int p = n - pi[n - 1];
int salinan = (n % p == 0) ? n / p : 1;
// "abcabcabc" -> p = 3, 3 salinan;   "abcab" -> p = 3, tetapi 5 tidak habis dibagi 3 -> 1
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan <strong>border</strong> dan fungsi prefiks π, lalu membangunnya dalam O(n).</li>
        <li>Mencari semua kemunculan pola di teks dalam O(n + m) dengan KMP.</li>
        <li>Menghitung periode terpendek dan semua border sebuah string.</li>
        <li>Memakai fungsi Z sebagai alternatif, dan menghitung kemunculan setiap awalan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'border dan fungsi prefiks', 'desc' => 'Ide yang membuat pencarian pola tidak pernah mundur.'])

<section class="lesson-section" id="masalah" data-toc="Masalahnya">
    <h2>Mencari Kata di Dokumen</h2>
    <div class="prose">
        <p>Fitur Ctrl+F mencari pola P di teks T. Cara naif mencoba setiap posisi awal dan membandingkan huruf demi huruf. Untuk T = "aaaa…aaab" dan P = "aaa…ab", hampir setiap percobaan gagal di huruf terakhir pola: O(n · m), terlalu lambat untuk n, m = 10<sup>6</sup>.</p>
        <p>Pemborosannya: saat percobaan gagal, kita sudah <em>tahu</em> isi beberapa huruf terakhir teks (karena tadi cocok dengan pola), tetapi informasi itu dibuang. KMP (Knuth–Morris–Pratt) menyimpannya dalam fungsi prefiks.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Awalan</b><span>s[0..k−1]. "ab" adalah awalan "abcab".</span></div>
        <div class="term"><b>Akhiran</b><span>Bagian paling belakang. "ab" juga akhiran "abcab".</span></div>
        <div class="term"><b>Border</b><span>Awalan yang sekaligus akhiran, bukan string utuh. Border "abcab": "ab" (dan string kosong).</span></div>
        <div class="term"><b>π[i]</b><span>Panjang border terpanjang dari s[0..i].</span></div>
    </div>
</section>

<section class="lesson-section" id="ide" data-toc="Ide Fungsi Prefiks">
    <h2>Border dari Border</h2>
    <div class="prose">
        <p>Semua border sebuah string membentuk rantai: border terpanjang π, lalu border dari border itu, dan seterusnya. Mengapa? Jika x adalah border s dan y border yang lebih pendek, y adalah awalan dan akhiran s, sehingga juga awalan dan akhiran x. Jadi untuk mencoba "border berikutnya yang lebih pendek", cukup lompat <code>k = π[k − 1]</code>, tanpa mencoba satu per satu.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Saat mencari pola, ketidakcocokan di posisi k berarti: dari k huruf yang sudah cocok, bagian yang masih berguna adalah border terpanjangnya. Teks tidak pernah dibaca mundur, setiap huruf teks diperiksa O(1) kali secara amortisasi.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Huruf hijau adalah awalan dan akhiran yang sama (border yang sedang dipakai). Perhatikan langkah merah: saat tidak cocok, k melompat ke π[k − 1], bukan ke 0. Coba string lain, misalnya <code>abacabab</code>.</p></div>
    <div data-viz="kmp"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Mencari semua kemunculan pola dengan pola + # + teks.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'KMP: semua kemunculan pola',
        'steps' => $kpSteps,
        'sample' => ['input' => "abababcababa\naba\n", 'output' => "4\n1 3 8 10\n"],
        'py' => $kpPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: s = "aabaaab"</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>s[i]</th><th>k awal</th><th>Proses</th><th>π[i]</th></tr>
            <tr><td>0</td><td>a</td><td>–</td><td>selalu 0</td><td>0</td></tr>
            <tr><td>1</td><td>a</td><td>0</td><td>s[1] = s[0] → k = 1</td><td>1</td></tr>
            <tr><td>2</td><td>b</td><td>1</td><td>b ≠ s[1] = a → k = π[0] = 0; b ≠ s[0]</td><td>0</td></tr>
            <tr><td>3</td><td>a</td><td>0</td><td>s[3] = s[0] → k = 1</td><td>1</td></tr>
            <tr><td>4</td><td>a</td><td>1</td><td>s[4] = s[1] → k = 2</td><td>2</td></tr>
            <tr class="hl"><td>5</td><td>a</td><td>2</td><td>a ≠ s[2] = b → k = π[1] = 1; s[5] = s[1] → k = 2</td><td>2</td></tr>
            <tr class="ok"><td>6</td><td>b</td><td>2</td><td>s[6] = s[2] → k = 3 ("aab" = "aab")</td><td>3</td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Naif</td><td><code>O(n · m)</code></td><td>Cepat pada teks acak, lambat pada kasus jahat.</td></tr>
        <tr><td>KMP / fungsi Z</td><td><code>O(n + m)</code></td><td>Pasti, tanpa peluang salah.</td></tr>
        <tr><td>Hashing</td><td><code>O(n + m)</code></td><td>Sangat fleksibel, ada peluang tabrakan kecil.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pemisah harus benar-benar tidak muncul</strong> di pola maupun teks. Jika teks bisa berisi '#', pilih karakter lain (misalnya '\x01'), atau jalankan KMP tanpa penggabungan.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa memakai while.</strong> Mengganti <code>while</code> dengan <code>if</code> (hanya satu kali mundur) membuat π salah pada string seperti "aabaaab".</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'periode, fungsi Z, dan menghitung awalan', 'desc' => 'Tiga penerapan lain dari ide border.'])

<section class="lesson-section" id="periode" data-toc="Periode">
    <h2>Periode Terpendek</h2>
    <div class="prose">
        <p>Jika s punya border sepanjang b, maka s "berulang" dengan periode n − b: menggeser s sejauh n − b membuatnya cocok dengan dirinya sendiri. Border terpanjang memberi periode terpendek.</p>
    </div>
    @include('lessons.code', ['cpp' => $kpPeriod, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="z" data-toc="Fungsi Z">
    <h2>Fungsi Z</h2>
    <div class="prose">
        <p>Fungsi Z memandang dari sisi lain: <code>z[i]</code> = panjang awalan s yang <em>dimulai</em> di posisi i. Algoritmanya juga O(n), memanfaatkan "kotak Z" [l, r) terkanan yang sudah diketahui sama dengan awalan s. Banyak soal bisa diselesaikan dengan π maupun Z; pilih yang paling nyaman.</p>
    </div>
    @include('lessons.code', ['cpp' => $kpZ, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="hitung" data-toc="Menghitung Awalan">
    <h2>Menghitung Kemunculan Setiap Awalan</h2>
    @include('lessons.code', ['cpp' => $kpCount, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Semua kemunculan</h4><p>pola + # + teks, cari π = m.</p></div>
        <div class="pattern"><h4>Rotasi</h4><p>B rotasi dari A ⇔ |A| = |B| dan B muncul di A + A.</p></div>
        <div class="pattern"><h4>String berulang</h4><p>Periode n − π[n−1]; salinan utuh jika membagi n.</p></div>
        <div class="pattern"><h4>Semua border</h4><p>Ikuti rantai π[n−1], π[π[n−1]−1], … sampai 0.</p></div>
        <div class="pattern"><h4>Palindrom terpanjang di awal</h4><p>π dari s + # + balik(s).</p></div>
        <div class="pattern"><h4>Banyak pola</h4><p>Automaton Aho–Corasick: trie + "π" untuk banyak pola sekaligus.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Border terpanjang 'abacaba' adalah 'aba' (awalan dan akhiran), panjang 3.">
        <p class="quiz-q">Berapa π[6] untuk s = "abacaba"?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">7</button>
            <button class="quiz-option">3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Border lain yang lebih pendek pasti juga border dari border terpanjang, jadi cukup lompat ke π[k − 1].">
        <p class="quiz-q">Saat s[i] ≠ s[k], mengapa k menjadi π[k − 1]?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena border berikutnya yang lebih pendek adalah border dari border sepanjang k</button>
            <button class="quiz-option">Karena k harus mulai lagi dari 0</button>
            <button class="quiz-option">Karena π selalu turun</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="n = 6, π[5] = 4 ('abab'), periode 6 − 4 = 2 ('ab'), dan 6 habis dibagi 2: 3 salinan.">
        <p class="quiz-q">"ababab" adalah berapa salinan string terpendek?</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">6</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
