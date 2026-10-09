@php
    $msSteps = [
        ['Ide: setiap batang sebagai yang terpendek', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> h(n);
    for (auto& x : h) cin >> x;
CPP, <<<'TXT'
<p><strong>Soal contoh "Persegi Panjang di Histogram":</strong> ada n batang berlebar 1 dengan tinggi <code>h[i]</code>. Cari luas persegi panjang terbesar yang seluruhnya berada di dalam histogram.</p>
<p>Persegi panjang terbaik pasti setinggi salah satu batang, sebut batang i, yaitu batang <strong>terpendek</strong> di dalamnya. Agar luas maksimum, persegi itu melebar ke kiri dan ke kanan sampai menabrak batang yang <em>lebih pendek</em> dari h[i].</p>
TXT],
        ['Batas kiri: previous smaller', <<<'CPP'

    vector<int> kiri(n), kanan(n);
    stack<int> st;
    for (int i = 0; i < n; i++) {
        while (!st.empty() && h[st.top()] >= h[i]) st.pop();
        kiri[i] = st.empty() ? -1 : st.top();    // batang lebih pendek terdekat di kiri
        st.push(i);
    }
CPP, <<<'TXT'
<p>Untuk setiap i, cari indeks terdekat di kiri dengan tinggi <strong>&lt; h[i]</strong>. Monotonic stack: buang semua indeks yang tingginya ≥ h[i], karena mereka tidak akan pernah menjadi "yang lebih pendek terdekat" bagi siapa pun di kanan i (h[i] lebih dekat dan tidak lebih tinggi).</p>
<p>Setelah pembersihan, puncak stack (jika ada) adalah jawabannya. −1 berarti persegi bisa melebar sampai ujung kiri.</p>
TXT],
        ['Batas kanan: next smaller', <<<'CPP'
    while (!st.empty()) st.pop();
    for (int i = n - 1; i >= 0; i--) {
        while (!st.empty() && h[st.top()] >= h[i]) st.pop();
        kanan[i] = st.empty() ? n : st.top();    // batang lebih pendek terdekat di kanan
        st.push(i);
    }
CPP, <<<'TXT'
<p>Sama persis, tetapi berjalan dari kanan. <code>n</code> berarti bisa melebar sampai ujung kanan.</p>
<div class="wt-tip">Isi stack selalu monoton: dari dasar ke puncak tingginya naik. Elemen yang "kalah dalam segala hal" (lebih tinggi dan lebih jauh) dibuang selamanya.</div>
TXT],
        ['Luas untuk setiap batang', <<<'CPP'

    long long best = 0;
    for (int i = 0; i < n; i++)
        best = max(best, h[i] * (kanan[i] - kiri[i] - 1));
    cout << best << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Persegi dengan tinggi h[i] menempati indeks <code>kiri[i] + 1</code> sampai <code>kanan[i] − 1</code>, lebarnya <code>kanan[i] − kiri[i] − 1</code>.</p>
<p>Setiap indeks di-push dan di-pop paling banyak sekali per arah: total O(n). Luas bisa mencapai 10<sup>9</sup> · 2 · 10<sup>5</sup>: pakai <code>long long</code>.</p>
TXT],
    ];

    $msPy = <<<'PY'
import sys
data = sys.stdin.read().split()
n = int(data[0])
h = list(map(int, data[1:1 + n]))
kiri, kanan = [-1] * n, [n] * n
st = []
for i in range(n):
    while st and h[st[-1]] >= h[i]:
        st.pop()
    kiri[i] = st[-1] if st else -1
    st.append(i)
st = []
for i in range(n - 1, -1, -1):
    while st and h[st[-1]] >= h[i]:
        st.pop()
    kanan[i] = st[-1] if st else n
    st.append(i)
print(max(h[i] * (kanan[i] - kiri[i] - 1) for i in range(n)))
PY;

    $msFour = <<<'CPP'
// Empat varian, satu kerangka. Ubah arah loop dan tanda perbandingan:
// next greater   : loop kiri→kanan, pop selama a[top] <  a[i], jawaban top dari pop
// previous greater: loop kiri→kanan, pop selama a[top] <= a[i], jawaban = top setelah pop
// next smaller   : loop kiri→kanan, pop selama a[top] >  a[i]
// previous smaller: loop kiri→kanan, pop selama a[top] >= a[i], jawaban = top setelah pop
vector<int> prevGreater(n);
stack<int> st;
for (int i = 0; i < n; i++) {
    while (!st.empty() && a[st.top()] <= a[i]) st.pop();
    prevGreater[i] = st.empty() ? -1 : st.top();
    st.push(i);
}
CPP;

    $msContrib = <<<'CPP'
// Jumlah min dari SEMUA subarray: setiap a[i] menjadi minimum di berapa subarray?
// kiri[i]  = previous smaller (pakai <  : seri dihitung di salah satu sisi saja)
// kanan[i] = next smaller-or-equal (pakai <=)
long long total = 0;
for (int i = 0; i < n; i++)
    total += a[i] * (long long)(i - kiri[i]) * (kanan[i] - i);
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mencari <em>next greater</em>, <em>previous smaller</em>, dan dua varian lainnya untuk semua elemen dalam O(n).</li>
        <li>Menjelaskan mengapa <code>while</code> di dalam <code>for</code> tetap O(n).</li>
        <li>Menyelesaikan persegi panjang terbesar di histogram.</li>
        <li>Memakai teknik <strong>kontribusi</strong>: berapa subarray yang menjadikan a[i] sebagai minimum.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'buang yang pasti kalah', 'desc' => 'Mencari tetangga lebih besar pertama untuk semua elemen sekaligus.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Antrean Tinggi Badan">
    <h2>Intuisi: Siapa yang Lebih Tinggi di Belakangku?</h2>
    <div class="prose">
        <p>Orang-orang berdiri berbaris. Setiap orang ingin tahu: <em>siapa orang pertama di belakangku yang lebih tinggi?</em></p>
        <p>Begitu datang orang yang lebih tinggi, semua orang pendek yang sedang menunggu langsung mendapat jawaban dan boleh pulang. Orang yang <strong>lebih pendek dan lebih lama</strong> tidak akan pernah menjadi jawaban bagi siapa pun di belakang orang tinggi itu. Akibatnya, yang masih menunggu selalu tersusun makin pendek ke arah belakang: <strong>monoton</strong>.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>"Kandidat yang kalah dalam segala hal boleh dibuang selamanya." Kalimat ini akan muncul lagi di monotonic deque dan convex hull trick.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Next Greater Element</h2>
    <div class="prose"><p>Perhatikan bagaimana satu batang tinggi menjawab banyak indeks sekaligus, tetapi setiap indeks hanya dijawab sekali. Di Mode Tebak, tebak berapa indeks yang akan di-pop.</p></div>
    <div data-viz="mono-stack"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>a[i]</th><th>di-pop (terjawab oleh i)</th><th>stack setelah push</th></tr>
            <tr><td>0</td><td>2</td><td>–</td><td>[0]</td></tr>
            <tr><td>1</td><td>1</td><td>–</td><td>[0, 1]</td></tr>
            <tr class="hl"><td>2</td><td>5</td><td>1, 0 → ans = 2</td><td>[2]</td></tr>
            <tr class="hl"><td>3</td><td>6</td><td>2 → ans = 3</td><td>[3]</td></tr>
            <tr><td>4</td><td>2</td><td>–</td><td>[3, 4]</td></tr>
            <tr class="hl"><td>5</td><td>3</td><td>4 → ans = 5</td><td>[3, 5]</td></tr>
            <tr><td>6</td><td>1</td><td>–</td><td>[3, 5, 6]</td></tr>
            <tr class="ok"><td>akhir</td><td></td><td>3, 5, 6 → −1</td><td>ans = [2, 2, 3, −1, 5, −1, −1]</td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'monotonic stack di C++', 'desc' => 'Histogram, dan empat varian dalam satu kerangka.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Persegi Panjang di Histogram</h2>
    @include('lessons.walkthrough', [
        'title' => 'Previous smaller + next smaller',
        'steps' => $msSteps,
        'sample' => ['input' => "7\n2 1 5 6 2 3 1\n", 'output' => "10\n"],
        'py' => $msPy,
    ])
</section>

<section class="lesson-section" id="varian" data-toc="Empat Varian">
    <h2>Empat Varian dalam Satu Kerangka</h2>
    @include('lessons.code', ['cpp' => $msFour, 'js' => null, 'py' => null])
    <table class="cx-table">
        <tr><th>Yang dicari</th><th>Arah</th><th>Pop selama</th></tr>
        <tr><td>Next greater</td><td>kiri → kanan (jawab saat pop)</td><td><code>a[top] &lt; a[i]</code></td></tr>
        <tr><td>Previous greater</td><td>kiri → kanan (jawab = top sisa)</td><td><code>a[top] &lt;= a[i]</code></td></tr>
        <tr><td>Next smaller</td><td>kiri → kanan (jawab saat pop)</td><td><code>a[top] &gt; a[i]</code></td></tr>
        <tr><td>Previous smaller</td><td>kiri → kanan (jawab = top sisa)</td><td><code>a[top] &gt;= a[i]</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>&lt; atau ≤?</strong> Pada nilai kembar, pilihan tanda menentukan apakah elemen yang sama dianggap "lebih besar". Baca definisi soal dengan teliti, lalu uji dengan array berisi angka kembar seperti <code>3 3 3</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Simpan indeks, bukan nilai.</strong> Dengan indeks, nilai tetap bisa dibaca lewat <code>a[st.top()]</code>, dan jarak atau lebar bisa dihitung.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'amortisasi dan kontribusi', 'desc' => 'Mengapa O(n), dan menghitung jumlah dari semua subarray.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa O(n)?">
    <h2>Mengapa O(n)?</h2>
    <div class="proof">
        <p>Hitung operasi per <em>indeks</em>, bukan per iterasi loop. Setiap indeks di-push tepat sekali dan di-pop paling banyak sekali. Total push + pop ≤ 2n, dan setiap pemeriksaan kondisi <code>while</code> yang gagal terjadi paling banyak sekali per iterasi <code>for</code>. Jadi total kerja O(n), walaupun satu iterasi tertentu bisa mem-pop banyak elemen.</p>
        <p><strong>Kebenaran:</strong> saat indeks j di-pop oleh i, semua indeks di antara j dan i sudah di-pop lebih dulu atau lebih kecil dari a[j] (mereka masuk setelah j dan tidak mem-pop j). Jadi i memang elemen pertama di kanan j yang lebih besar.</p>
    </div>
</section>

<section class="lesson-section" id="kontribusi" data-toc="Teknik Kontribusi">
    <h2>Teknik Kontribusi: Jumlah Minimum Semua Subarray</h2>
    <div class="prose">
        <p>Ada n(n+1)/2 subarray, terlalu banyak untuk dihitung satu per satu. Balik pertanyaannya: <strong>di berapa subarray a[i] menjadi minimum?</strong> Subarray itu harus dimulai setelah batang lebih kecil terdekat di kiri dan berakhir sebelum batang lebih kecil terdekat di kanan: ada <code>(i − kiri[i]) × (kanan[i] − i)</code> pilihan.</p>
    </div>
    @include('lessons.code', ['cpp' => $msContrib, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Untuk nilai kembar, satu sisi memakai <code>&lt;</code> dan sisi lain <code>≤</code>. Jika keduanya memakai tanda yang sama, subarray dengan dua minimum kembar terhitung dua kali (atau tidak sama sekali).</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Hari menunggu suhu lebih hangat</h4><p>Next greater, jawab dengan selisih indeks.</p></div>
        <div class="pattern"><h4>Histogram terbesar</h4><p>Previous + next smaller.</p></div>
        <div class="pattern"><h4>Persegi 1 terbesar di matriks</h4><p>Histogram per baris.</p></div>
        <div class="pattern"><h4>Jumlah min semua subarray</h4><p>Kontribusi dengan &lt; dan ≤.</p></div>
        <div class="pattern"><h4>Air hujan tertampung</h4><p>Stack menurun, atau two pointers.</p></div>
        <div class="pattern"><h4>Melingkar</h4><p>Ulangi array dua kali (indeks i mod n).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Indeks 3 dan 4 (nilai 2 dan 4) lebih kecil dari 7, jadi keduanya di-pop. Indeks 0 (nilai 9) tetap.">
        <p class="quiz-q">Stack (dasar → puncak) berisi indeks dengan nilai 9, 2, 4. Datang a[i] = 7 (next greater). Berapa yang di-pop?</p>
        <div class="quiz-options">
            <button class="quiz-option">0</button>
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Setiap indeks masuk sekali dan keluar paling banyak sekali, jadi total operasi ≤ 2n.">
        <p class="quiz-q">Mengapa monotonic stack O(n) walaupun ada while di dalam for?</p>
        <div class="quiz-options">
            <button class="quiz-option">Setiap indeks di-push dan di-pop paling banyak sekali</button>
            <button class="quiz-option">while hanya berjalan sekali per iterasi</button>
            <button class="quiz-option">Stack selalu berukuran kecil</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Lebar = kanan − kiri − 1 = 4 − 1 − 1 = 2 (indeks 2 dan 3), luas = 5 × 2 = 10.">
        <p class="quiz-q">h[2] = 5, previous smaller di indeks 1, next smaller di indeks 4. Luas persegi setinggi 5?</p>
        <div class="quiz-options">
            <button class="quiz-option">5</button>
            <button class="quiz-option">10</button>
            <button class="quiz-option">15</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
