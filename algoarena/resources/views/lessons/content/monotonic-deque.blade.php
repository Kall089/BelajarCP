@php
    $mdSteps = [
        ['Kandidat disimpan di deque', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    deque<int> dq;     // indeks; nilai a[] menurun dari depan ke belakang
CPP, <<<'TXT'
<p><strong>Soal contoh "Maksimum Jendela":</strong> diberikan n bilangan dan k. Cetak maksimum dari setiap jendela berukuran k: a[0..k−1], a[1..k], …, a[n−k..n−1].</p>
<p>Menghitung ulang maksimum setiap jendela butuh O(n·k). Kita simpan hanya <strong>kandidat</strong> yang mungkin menjadi maksimum di masa depan.</p>
TXT],
        ['Buang yang kalah dari belakang', <<<'CPP'
    for (int i = 0; i < n; i++) {
        while (!dq.empty() && a[dq.back()] <= a[i]) dq.pop_back();
        dq.push_back(i);
CPP, <<<'TXT'
<p>Jika ada indeks j &lt; i dengan a[j] ≤ a[i], maka j <strong>kalah dalam segala hal</strong>: nilainya tidak lebih besar, dan ia akan keluar dari jendela lebih dulu. Selama i masih di jendela, j tidak mungkin menjadi maksimum. Buang selamanya.</p>
<p>Akibatnya isi deque selalu menurun dari depan ke belakang, dan elemen terdepan adalah maksimum.</p>
TXT],
        ['Buang yang kedaluwarsa dari depan', <<<'CPP'
        if (dq.front() <= i - k) dq.pop_front();
CPP, <<<'TXT'
<p>Jendela saat ini adalah [i − k + 1, i]. Jika indeks terdepan sudah di luar jendela, buang. Cukup sekali per iterasi, karena setiap langkah jendela hanya bergeser satu.</p>
<p>Inilah alasan memakai <strong>deque</strong>: kita membuang dari belakang (kalah) dan dari depan (kedaluwarsa).</p>
TXT],
        ['Jawaban di depan', <<<'CPP'
        if (i >= k - 1) cout << a[dq.front()] << (i + 1 < n ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Setiap indeks masuk deque sekali dan keluar paling banyak sekali: total <code>O(n)</code>.</p>
<div class="wt-tip">Untuk minimum jendela, cukup balik tanda: buang dari belakang selama <code>a[dq.back()] &gt;= a[i]</code>.</div>
TXT],
    ];

    $mdPy = <<<'PY'
import sys
from collections import deque
data = sys.stdin.read().split()
n, k = int(data[0]), int(data[1])
a = list(map(int, data[2:2 + n]))
dq = deque()
out = []
for i in range(n):
    while dq and a[dq[-1]] <= a[i]:
        dq.pop()
    dq.append(i)
    if dq[0] <= i - k:
        dq.popleft()
    if i >= k - 1:
        out.append(a[dq[0]])
print(" ".join(map(str, out)))
PY;

    $mdTwo = <<<'CPP'
// Jendela variabel + dua deque: subarray terpanjang dengan max - min <= K
deque<int> mx, mn;
int l = 0, best = 0;
for (int r = 0; r < n; r++) {
    while (!mx.empty() && a[mx.back()] <= a[r]) mx.pop_back();
    while (!mn.empty() && a[mn.back()] >= a[r]) mn.pop_back();
    mx.push_back(r); mn.push_back(r);
    while (a[mx.front()] - a[mn.front()] > K) {     // persempit dari kiri
        l++;
        if (mx.front() < l) mx.pop_front();
        if (mn.front() < l) mn.pop_front();
    }
    best = max(best, r - l + 1);
}
CPP;

    $mdDp = <<<'CPP'
// Optimasi DP: dp[i] = a[i] + max(dp[i-k], ..., dp[i-1])
// "max dari k nilai terakhir" = maksimum jendela geser atas dp
deque<int> dq;
dp[0] = a[0];
dq.push_back(0);
for (int i = 1; i < n; i++) {
    if (dq.front() < i - k) dq.pop_front();
    dp[i] = a[i] + dp[dq.front()];
    while (!dq.empty() && dp[dq.back()] <= dp[i]) dq.pop_back();
    dq.push_back(i);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menghitung maksimum (atau minimum) setiap jendela geser dalam O(n) dengan deque monoton.</li>
        <li>Menjelaskan dua alasan membuang kandidat: <strong>kalah</strong> (dari belakang) dan <strong>kedaluwarsa</strong> (dari depan).</li>
        <li>Menggabungkan dua deque untuk syarat "max − min ≤ K" pada jendela variabel.</li>
        <li>Mempercepat DP berbentuk <code>dp[i] = a[i] + max(dp[i−k..i−1])</code> dari O(nk) menjadi O(n).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'kandidat yang masih punya harapan', 'desc' => 'Maksimum jendela geser tanpa menghitung ulang.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Pemain Senior">
    <h2>Intuisi: Pemain Muda yang Lebih Kuat</h2>
    <div class="prose">
        <p>Sebuah tim olahraga hanya memakai pemain yang bergabung dalam k musim terakhir, dan setiap musim kapten adalah pemain terkuat. Jika datang pemain baru yang <strong>lebih kuat</strong> dari seorang senior, senior itu tidak akan pernah menjadi kapten lagi: ia lebih lemah, dan akan pensiun lebih dulu. Ia boleh langsung dicoret.</p>
        <p>Setelah semua coretan, daftar kandidat selalu tersusun dari yang terkuat (paling senior) sampai yang terlemah (paling baru). Kapten selalu di depan, kecuali jika ia sudah pensiun karena terlalu lama.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Buang dari belakang</b><span>Kandidat yang lebih lemah dan lebih tua dari elemen baru. Tidak akan pernah dipakai lagi.</span></div>
        <div class="term"><b>Buang dari depan</b><span>Kandidat yang sudah keluar dari jendela (indeks ≤ i − k).</span></div>
        <div class="term"><b>Depan = jawaban</b><span>Karena isinya menurun, elemen terdepan adalah maksimum jendela.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Sliding Window Maximum</h2>
    <div class="prose"><p>Ganti k dan isi array. Perhatikan panel <strong>Isi deque</strong>: nilainya selalu menurun dari depan ke belakang.</p></div>
    <div data-viz="mono-deque"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>a = [1, 3, −1, −3, 5, 3, 6, 7], k = 3. Deque ditulis sebagai nilai (indeks).</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>a[i]</th><th>aksi</th><th>deque</th><th>maks</th></tr>
            <tr><td>0</td><td>1</td><td>push</td><td>1(0)</td><td>–</td></tr>
            <tr><td>1</td><td>3</td><td>buang 1, push</td><td>3(1)</td><td>–</td></tr>
            <tr class="hl"><td>2</td><td>−1</td><td>push</td><td>3(1) −1(2)</td><td><b>3</b></td></tr>
            <tr><td>3</td><td>−3</td><td>push</td><td>3(1) −1(2) −3(3)</td><td><b>3</b></td></tr>
            <tr class="hl"><td>4</td><td>5</td><td>buang −3, −1, 3 (semua ≤ 5)</td><td>5(4)</td><td><b>5</b></td></tr>
            <tr><td>5</td><td>3</td><td>push</td><td>5(4) 3(5)</td><td><b>5</b></td></tr>
            <tr><td>6</td><td>6</td><td>buang 3, 5, push</td><td>6(6)</td><td><b>6</b></td></tr>
            <tr class="ok"><td>7</td><td>7</td><td>buang 6, push</td><td>7(7)</td><td><b>7</b></td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'monotonic deque di C++', 'desc' => 'Program lengkap, lalu dua deque untuk jendela variabel.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Maksimum Jendela</h2>
    @include('lessons.walkthrough', [
        'title' => 'Sliding window maximum',
        'steps' => $mdSteps,
        'sample' => ['input' => "8 3\n1 3 -1 -3 5 3 6 7\n", 'output' => "3 3 5 5 6 7\n"],
        'py' => $mdPy,
    ])
</section>

<section class="lesson-section" id="dua-deque" data-toc="Dua Deque Sekaligus">
    <h2>Dua Deque Sekaligus: max − min ≤ K</h2>
    <div class="prose"><p>Untuk syarat yang melibatkan maksimum <em>dan</em> minimum jendela, jaga dua deque: satu menurun (maksimum di depan), satu menaik (minimum di depan). Jendelanya berukuran variabel, jadi penunjuk kiri dimajukan selama syarat dilanggar.</p></div>
    @include('lessons.code', ['cpp' => $mdTwo, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th></tr>
        <tr><td>Hitung ulang setiap jendela</td><td><code>O(n · k)</code></td></tr>
        <tr><td>multiset berisi isi jendela</td><td><code>O(n log k)</code></td></tr>
        <tr><td>Monotonic deque</td><td><code>O(n)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Simpan indeks, bukan nilai.</strong> Tanpa indeks, kita tidak tahu kapan elemen terdepan sudah keluar dari jendela.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Urutan operasi.</strong> Untuk jendela ukuran tetap, buang yang kalah, push, lalu buang yang kedaluwarsa, baru baca jawaban. Membaca sebelum membuang yang kedaluwarsa memberi maksimum dari jendela yang salah.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'deque untuk mempercepat DP', 'desc' => 'Transisi "max dari k nilai terakhir".'])

<section class="lesson-section" id="dp" data-toc="Optimasi DP">
    <h2>Optimasi DP dengan Jendela</h2>
    <div class="prose">
        <p>Banyak DP berbentuk <code>dp[i] = a[i] + max(dp[j])</code> untuk j di jendela <code>[i − k, i − 1]</code>, misalnya "melompat paling jauh k langkah, maksimalkan skor". Menghitung max itu dengan loop membuat DP O(nk). Tetapi "max dari k nilai terakhir" adalah maksimum jendela geser atas array dp itu sendiri.</p>
    </div>
    @include('lessons.code', ['cpp' => $mdDp, 'js' => null, 'py' => null])
    <div class="proof">
        <p>Invarian deque: (1) indeksnya urut naik dan semuanya di jendela, (2) nilai dp-nya menurun, (3) setiap indeks di jendela yang <em>tidak</em> ada di deque didominasi oleh indeks yang lebih baru dan tidak lebih kecil. Dari (2) dan (3), elemen terdepan adalah maksimum jendela. Setiap operasi menjaga ketiga invarian, dan setiap indeks masuk serta keluar paling banyak sekali.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Maksimum / minimum jendela</h4><p>Satu deque.</p></div>
        <div class="pattern"><h4>max − min ≤ K</h4><p>Dua deque + jendela variabel.</p></div>
        <div class="pattern"><h4>Lompatan paling jauh k</h4><p>DP + deque.</p></div>
        <div class="pattern"><h4>Subarray dengan jumlah ≥ S, nilai negatif</h4><p>Deque menaik atas prefix sum.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Nilai 4 dan 2 di belakang deque ≤ 6, jadi dibuang. 8 di depan tetap. Deque menjadi [8, 6].">
        <p class="quiz-q">Deque berisi nilai [8, 4, 2] (depan → belakang). Datang 6. Isi deque setelah push?</p>
        <div class="quiz-options">
            <button class="quiz-option">[8, 4, 2, 6]</button>
            <button class="quiz-option">[8, 6]</button>
            <button class="quiz-option">[6]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Kita membuang dari belakang (kandidat kalah) dan dari depan (kedaluwarsa). Stack hanya bisa satu ujung.">
        <p class="quiz-q">Mengapa butuh deque, bukan stack?</p>
        <div class="quiz-options">
            <button class="quiz-option">Perlu membuang dari dua ujung</button>
            <button class="quiz-option">Deque lebih cepat dari stack</button>
            <button class="quiz-option">Agar isinya terurut otomatis</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Setiap indeks di-push sekali dan di-pop paling banyak sekali (dari depan atau belakang), jadi totalnya O(n) tidak bergantung k.">
        <p class="quiz-q">Kompleksitas sliding window maximum dengan deque?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(n · k)</button>
            <button class="quiz-option">O(n log k)</button>
            <button class="quiz-option">O(n)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
