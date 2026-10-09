@php
    $smSteps = [
        ['Membaca array', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n + 1);
    for (int i = 1; i <= n; i++) cin >> a[i];

CPP, <<<'TXT'
<p>Untuk setiap indeks i, cari nilai elemen <strong>pertama di sebelah kanan</strong> i yang lebih besar dari a[i]. Jika tidak ada, jawabannya −1.</p>
TXT],
        ['Stack berisi indeks yang menunggu', <<<'CPP'
    vector<long long> jawab(n + 1, -1);
    vector<int> st;
CPP, <<<'TXT'
<p>Semua jawaban diisi −1 dulu; indeks yang tidak pernah menemukan elemen lebih besar akan tetap −1.</p>
<p><code>vector</code> dipakai sebagai stack: <code>push_back</code> untuk push, <code>back()</code> untuk melihat puncak, <code>pop_back()</code> untuk pop. Isinya <strong>indeks</strong> (bukan nilai), karena kita perlu tahu posisi mana yang jawabannya diisi.</p>
TXT],
        ['Elemen baru menjawab yang lebih kecil', <<<'CPP'
    for (int i = 1; i <= n; i++) {
        while (!st.empty() && a[st.back()] < a[i]) {
            jawab[st.back()] = a[i];
            st.pop_back();
        }
        st.push_back(i);
    }

CPP, <<<'TXT'
<p>Saat a[i] datang, setiap indeks di puncak stack yang nilainya lebih kecil dari a[i] baru saja menemukan jawabannya: a[i] adalah elemen lebih besar <em>pertama</em> di kanannya (yang lain sebelumnya tidak cukup besar, karena itu ia masih menunggu). Keluarkan, lalu masukkan i.</p>
<p>Akibatnya nilai di stack tidak pernah naik dari dasar ke puncak: <strong>stack monoton</strong>. Begitu puncak tidak lebih kecil dari a[i], elemen di bawahnya pun tidak, jadi kita boleh berhenti.</p>
TXT],
        ['Mencetak jawaban', <<<'CPP'
    for (int i = 1; i <= n; i++) cout << jawab[i] << (i < n ? ' ' : '\n');
    return 0;
}
CPP, <<<'TXT'
<p>Setiap indeks di-push sekali dan di-pop paling banyak sekali, jadi walaupun ada <code>while</code> di dalam <code>for</code>, totalnya O(N).</p>
TXT],
    ];

    $smPy = <<<'PY'
import sys
data = sys.stdin.buffer.read().split()
n = int(data[0])
a = [0] + list(map(int, data[1:n + 1]))
jawab = [-1] * (n + 1)
st = []
for i in range(1, n + 1):
    while st and a[st[-1]] < a[i]:
        jawab[st.pop()] = a[i]
    st.append(i)
print(*jawab[1:])
PY;

    $smBracket = <<<'CPP'
// Apakah string berisi ( ) [ ] { } seimbang?
bool seimbang(const string& s) {
    vector<char> st;
    for (char c : s) {
        if (c == '(' || c == '[' || c == '{') st.push_back(c);   // kurung buka: tunggu pasangannya
        else {
            char buka = (c == ')') ? '(' : (c == ']') ? '[' : '{';
            if (st.empty() || st.back() != buka) return false;   // tidak ada / salah pasangan
            st.pop_back();
        }
    }
    return st.empty();   // semua kurung buka harus sudah tertutup
}
CPP;

    $smHist = <<<'CPP'
// Persegi panjang terbesar di histogram (h[1..n]).
// Batang i menjadi "batang terpendek" persegi yang membentang dari kiri[i]+1 sampai kanan[i]-1,
// dengan kiri[i]/kanan[i] = batang lebih PENDEK terdekat di kiri/kanan.
vector<int> kiri(n + 1), kanan(n + 1), st;
for (int i = 1; i <= n; i++) {
    while (!st.empty() && h[st.back()] >= h[i]) st.pop_back();
    kiri[i] = st.empty() ? 0 : st.back();
    st.push_back(i);
}
st.clear();
for (int i = n; i >= 1; i--) {
    while (!st.empty() && h[st.back()] >= h[i]) st.pop_back();
    kanan[i] = st.empty() ? n + 1 : st.back();
    st.push_back(i);
}
long long terbaik = 0;
for (int i = 1; i <= n; i++) terbaik = max(terbaik, h[i] * (kanan[i] - kiri[i] - 1));
// h = 2 1 5 6 2 3  ->  10 (batang 5 dan 6, tinggi 5, lebar 2)
CPP;

    $smContrib = <<<'CPP'
// Jumlah min(subarray) untuk SEMUA subarray.
// a[i] adalah minimum subarray l..r tepat ketika L[i] < l ≤ i ≤ r < R[i]:
//   L[i] = indeks terdekat di kiri dengan nilai <  a[i]  (pakai >= saat pop)
//   R[i] = indeks terdekat di kanan dengan nilai <= a[i] (pakai >  saat pop)
// Satu sisi ketat, satu sisi tidak: subarray dengan dua minimum sama dihitung SEKALI.
for (int i = 1; i <= n; i++) {
    while (!st.empty() && a[st.back()] >= a[i]) st.pop_back();
    L[i] = st.empty() ? 0 : st.back();
    st.push_back(i);
}
st.clear();
for (int i = n; i >= 1; i--) {
    while (!st.empty() && a[st.back()] > a[i]) st.pop_back();
    R[i] = st.empty() ? n + 1 : st.back();
    st.push_back(i);
}
long long total = 0;
for (int i = 1; i <= n; i++) total += a[i] * (i - L[i]) * (R[i] - i);   // kontribusi a[i]
// a = 3 1 2 4  ->  17
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai stack (masuk terakhir, keluar pertama) untuk memeriksa pasangan kurung.</li>
        <li>Mencari elemen lebih besar/lebih kecil terdekat di kiri atau kanan untuk <em>semua</em> indeks dalam O(N).</li>
        <li>Menghitung persegi panjang terbesar di histogram.</li>
        <li>Memakai <strong>teknik kontribusi</strong>: berapa subarray yang minimumnya a[i]?</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'stack dan elemen yang menunggu', 'desc' => 'LIFO, pasangan kurung, dan ide stack monoton.'])

<section class="lesson-section" id="stack" data-toc="Stack">
    <h2>Stack: Yang Terakhir Masuk, Pertama Keluar</h2>
    <div class="prose">
        <p>Stack seperti tumpukan piring: piring baru diletakkan di atas, dan yang diambil selalu piring paling atas. Tiga operasinya, push, pop, dan melihat puncak, semuanya O(1). Di C++ cukup pakai <code>vector</code> (atau <code>stack</code>).</p>
        <p>Stack cocok setiap kali sesuatu harus "menunggu pasangan" dan pasangannya selalu yang <em>paling baru</em> terbuka. Contoh klasik: kurung. Kurung tutup harus memasangkan kurung buka yang paling akhir dibuka.</p>
    </div>
    @include('lessons.code', ['cpp' => $smBracket, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="nge" data-toc="Elemen Lebih Besar Berikutnya">
    <h2>Elemen Lebih Besar Berikutnya</h2>
    <div class="prose">
        <p>Untuk deretan tinggi badan 4, 2, 1, 5, 3, 3, 6, 2, siapa orang pertama di sebelah kanan setiap orang yang lebih tinggi darinya? Cara naif memeriksa ke kanan satu per satu: O(N²).</p>
        <p>Ubah sudut pandang: proses dari kiri, dan simpan orang-orang yang <strong>belum menemukan</strong> jawabannya. Ketika orang baru datang, ia menjadi jawaban bagi semua yang sedang menunggu dan lebih pendek darinya. Yang menunggu paling baru adalah yang paling dekat, jadi periksa mulai dari puncak stack.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Isi stack selalu terurut (di sini tidak naik dari dasar ke puncak), maka disebut <strong>stack monoton</strong>. Elemen yang dikeluarkan tidak pernah kembali, sehingga semua pop total O(N).</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Baris "jawab" terisi ketika sebuah indeks dikeluarkan dari stack. Perhatikan dua angka 3 berturut-turut: 3 yang kedua tidak mengeluarkan 3 yang pertama, karena yang dicari <em>lebih besar</em>, bukan sama.</p></div>
    <div data-viz="monostack"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Tentukan arah</b><span>Cari di kanan → proses kiri ke kanan dan jawab saat pop. Cari di kiri → jawabannya puncak stack setelah pop.</span></div>
        <div class="step-card"><b>Tentukan perbandingan</b><span>Lebih besar atau lebih kecil? Ketat (&lt;) atau tidak (≤)?</span></div>
        <div class="step-card"><b>Pop selama</b><span>Puncak "kalah" dari elemen baru.</span></div>
        <div class="step-card"><b>Push indeks</b><span>Selalu simpan indeks, nilai bisa dibaca dari array.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Elemen lebih besar berikutnya untuk semua indeks dalam O(N).'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Next greater element',
        'steps' => $smSteps,
        'sample' => ['input' => "8\n4 2 1 5 3 3 6 2\n", 'output' => "5 5 5 6 6 6 -1 -1\n"],
        'py' => $smPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>a[i]</th><th>Dikeluarkan (jawabannya a[i])</th><th>Stack setelahnya (nilai)</th></tr>
            <tr><td>1</td><td>4</td><td>–</td><td class="q">4</td></tr>
            <tr><td>2</td><td>2</td><td>–</td><td class="q">4 2</td></tr>
            <tr><td>3</td><td>1</td><td>–</td><td class="q">4 2 1</td></tr>
            <tr class="hl"><td>4</td><td>5</td><td>indeks 3, 2, 1 → 5</td><td class="q">5</td></tr>
            <tr><td>5</td><td>3</td><td>–</td><td class="q">5 3</td></tr>
            <tr><td>6</td><td>3</td><td>– (3 tidak &lt; 3)</td><td class="q">5 3 3</td></tr>
            <tr class="hl"><td>7</td><td>6</td><td>indeks 6, 5, 4 → 6</td><td class="q">6</td></tr>
            <tr class="ok"><td>8</td><td>2</td><td>–</td><td class="q">6 2 (sisa: −1)</td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Empat Variasi</h2>
    <table class="cx-table">
        <tr><th>Yang dicari</th><th>Arah proses</th><th>Pop selama</th><th>Jawaban diambil</th></tr>
        <tr><td>Lebih besar berikutnya (kanan)</td><td>kiri → kanan</td><td><code>a[top] &lt; a[i]</code></td><td>saat top di-pop: a[i]</td></tr>
        <tr><td>Lebih kecil berikutnya (kanan)</td><td>kiri → kanan</td><td><code>a[top] &gt; a[i]</code></td><td>saat top di-pop: a[i]</td></tr>
        <tr><td>Lebih besar sebelumnya (kiri)</td><td>kiri → kanan</td><td><code>a[top] ≤ a[i]</code></td><td>puncak setelah pop</td></tr>
        <tr><td>Lebih kecil sebelumnya (kiri)</td><td>kiri → kanan</td><td><code>a[top] ≥ a[i]</code></td><td>puncak setelah pop</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Nilai kembar.</strong> "Lebih besar" berarti pop saat <code>&lt;</code>; "lebih besar atau sama" berarti pop saat <code>≤</code>. Salah memilih membuat jawaban untuk elemen kembar keliru. Selalu uji dengan input yang berisi nilai sama.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Stack kosong.</strong> Selalu cek <code>!st.empty()</code> sebelum <code>st.back()</code>; mengakses puncak stack kosong adalah undefined behavior dan sering menghasilkan runtime error.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'histogram & kontribusi', 'desc' => 'Dua penerapan klasik yang memakai elemen terdekat di kedua sisi.'])

<section class="lesson-section" id="histogram" data-toc="Histogram">
    <h2>Persegi Panjang Terbesar di Histogram</h2>
    <div class="prose">
        <p>Persegi panjang optimal pasti setinggi salah satu batang, sebut batang i, yaitu batang terpendek di dalamnya. Persegi itu bisa melebar ke kiri dan kanan sampai bertemu batang yang <em>lebih pendek</em>. Jadi untuk setiap i kita butuh batang lebih pendek terdekat di kiri dan di kanan: dua kali stack monoton.</p>
    </div>
    @include('lessons.code', ['cpp' => $smHist, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kontribusi" data-toc="Teknik Kontribusi">
    <h2>Teknik Kontribusi</h2>
    <div class="prose">
        <p>Soal "jumlahkan sesuatu atas semua subarray" punya O(N²) subarray. Balik pertanyaannya: untuk setiap elemen, <strong>di berapa subarray ia menjadi minimum?</strong> Jika batas pengaruhnya L[i] dan R[i], banyaknya (i − L[i]) · (R[i] − i), dan kontribusinya a[i] dikali jumlah itu.</p>
    </div>
    @include('lessons.code', ['cpp' => $smContrib, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Rentang saham</h4><p>Berapa hari berturut-turut sebelum hari ini harganya ≤ hari ini? Lebih besar sebelumnya.</p></div>
        <div class="pattern"><h4>Menunggu lebih hangat</h4><p>Berapa hari sampai suhu lebih tinggi? Lebih besar berikutnya, simpan selisih indeks.</p></div>
        <div class="pattern"><h4>Persegi 1 terbesar</h4><p>Pada grid 0/1, setiap baris adalah histogram tinggi kolom 1 berturut-turut.</p></div>
        <div class="pattern"><h4>Hapus K digit</h4><p>Bilangan terkecil setelah menghapus K digit: stack yang dijaga naik.</p></div>
        <div class="pattern"><h4>Air hujan</h4><p>Air yang tertampung di antara batang: stack batang yang menurun.</p></div>
        <div class="pattern"><h4>Maksimum − minimum</h4><p>Σ (maks − min) semua subarray = kontribusi maks − kontribusi min.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Setiap indeks masuk ke stack sekali dan keluar paling banyak sekali, jadi total operasi stack O(N).">
        <p class="quiz-q">Algoritma elemen lebih besar berikutnya punya <code>while</code> di dalam <code>for</code>. Kompleksitasnya …</p>
        <div class="quiz-options">
            <button class="quiz-option">O(N²) pada kasus terburuk</button>
            <button class="quiz-option">O(N)</button>
            <button class="quiz-option">O(N log N)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Kurung tutup ']' harus memasangkan kurung buka terakhir yang belum tertutup, yaitu '('. Pasangannya salah.">
        <p class="quiz-q">Saat memeriksa "([)]", apa yang terjadi ketika membaca ']'?</p>
        <div class="quiz-options">
            <button class="quiz-option">Cocok dengan '[' di posisi 2</button>
            <button class="quiz-option">Stack kosong</button>
            <button class="quiz-option">Puncak stack '(' bukan pasangannya: tidak seimbang</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Jika kedua sisi ketat, subarray dengan dua minimum sama tidak terhitung; jika kedua sisi tidak ketat, terhitung dua kali.">
        <p class="quiz-q">Mengapa pada teknik kontribusi satu sisi memakai &lt; dan sisi lain ≤?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar subarray dengan beberapa minimum yang sama dihitung tepat sekali</button>
            <button class="quiz-option">Agar lebih cepat</button>
            <button class="quiz-option">Karena stack tidak boleh berisi nilai sama</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
