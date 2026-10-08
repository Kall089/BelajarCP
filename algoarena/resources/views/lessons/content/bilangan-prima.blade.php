@php
    $bpSteps = [
        ['Saringan faktor prima terkecil', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int MAXV = 1000000;
int spf[MAXV + 1];

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    for (int i = 2; i <= MAXV; i++) {
        if (spf[i] != 0) continue;
        spf[i] = i;
        for (long long k = (long long)i * i; k <= MAXV; k += i)
            if (spf[k] == 0) spf[k] = i;
    }

CPP, <<<'TXT'
<p>Ini saringan Eratosthenes dengan sedikit tambahan: selain mencoret, kita catat <strong>siapa yang mencoret pertama kali</strong>. Karena prima diproses dari kecil ke besar, pencoret pertama setiap bilangan adalah faktor prima terkecilnya, <code>spf[k]</code>.</p>
<p>Jika <code>spf[i]</code> masih 0 saat i dikunjungi, berarti tidak ada prima lebih kecil yang membaginya: i prima, dan spf[i] = i. Pencoretan dimulai dari i² (pakai <code>long long</code> agar i·i tidak overflow). Persiapan ini O(N log log N), cukup sekali untuk semua pertanyaan.</p>
TXT],
        ['Faktorisasi dengan membagi berulang', <<<'CPP'
    int q;
    cin >> q;
    string out;
    while (q--) {
        int x;
        cin >> x;
        long long pembagi = 1;
        string baris;
        while (x > 1) {
            int p = spf[x], e = 0;
            while (x % p == 0) {
                x /= p;
                e++;
            }
            baris += to_string(p) + "^" + to_string(e) + " ";
            pembagi *= e + 1;
        }
        out += baris + "(" + to_string(pembagi) + " pembagi)\n";
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Untuk x, ambil faktor prima terkecilnya p = spf[x], bagi x dengan p sebanyak mungkin (e kali), lalu ulangi pada sisa x. Setiap pembagian setidaknya membagi dua, jadi faktorisasi hanya O(log x), jauh lebih cepat dari mencoba pembagi sampai √x.</p>
<p>Dari faktorisasi <code>x = p₁<sup>e₁</sup> · p₂<sup>e₂</sup> · …</code>, banyak pembagi = <code>(e₁ + 1)(e₂ + 1)…</code>: setiap pembagi memilih pangkat 0..e<sub>i</sub> untuk tiap prima.</p>
TXT],
    ];

    $bpPy = <<<'PY'
import sys
MAXV = 1000000
spf = list(range(MAXV + 1))           # spf[i] = i awalnya
i = 2
while i * i <= MAXV:
    if spf[i] == i:                    # i prima
        for k in range(i * i, MAXV + 1, i):
            if spf[k] == k:
                spf[k] = i
    i += 1

data = sys.stdin.read().split()
out = []
for t in data[1:1 + int(data[0])]:
    x = int(t)
    bagian, pembagi = [], 1
    while x > 1:
        p, e = spf[x], 0
        while x % p == 0:
            x //= p
            e += 1
        bagian.append(f"{p}^{e}")
        pembagi *= e + 1
    out.append(" ".join(bagian) + f" ({pembagi} pembagi)")
print("\n".join(out))
PY;

    $bpTrial = <<<'CPP'
// Uji prima satu bilangan: cukup coba pembagi sampai √n.
// Jika n = a · b dengan a ≤ b, maka a ≤ √n, jadi pembagi kecil pasti ditemukan.
bool prima(long long n) {
    if (n < 2) return false;
    for (long long d = 2; d * d <= n; d++)
        if (n % d == 0) return false;
    return true;
}
// n sampai 10^12 -> paling banyak 10^6 percobaan.
CPP;

    $bpPhi = <<<'CPP'
// phi(n) = banyak bilangan 1..n yang saling prima dengan n.
// Rumus: phi(n) = n · Π (1 − 1/p) untuk setiap prima p yang membagi n.
// Saringan phi untuk semua 1..N sekaligus, O(N log log N):
vector<int> phi(N + 1);
iota(phi.begin(), phi.end(), 0);          // phi[i] = i
for (int p = 2; p <= N; p++)
    if (phi[p] == p)                      // p belum tersentuh -> prima
        for (int k = p; k <= N; k += p) phi[k] -= phi[k] / p;   // kali (1 − 1/p)
// phi[1..12] = 1 1 2 2 4 2 6 4 6 4 10 4
CPP;

    $bpSegment = <<<'CPP'
// Prima di rentang [L, R] dengan R sampai 10^12 tetapi R − L ≤ 10^6:
// coret kelipatan setiap prima kecil (≤ √R) langsung di dalam rentang.
vector<bool> prima(R - L + 1, true);
for (long long p : primaKecil)            // hasil saringan biasa sampai √R
    for (long long k = max(p * p, (L + p - 1) / p * p); k <= R; k += p)
        prima[k - L] = false;             // (L + p − 1) / p * p = kelipatan p pertama ≥ L
// x di [L, R] prima jika prima[x − L] masih true (dan x ≥ 2)
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menguji satu bilangan prima dalam O(√n).</li>
        <li>Mencari semua prima sampai N dengan <strong>saringan Eratosthenes</strong> dan memahami mengapa mulai dari p².</li>
        <li>Memfaktorkan banyak bilangan dengan cepat memakai <strong>faktor prima terkecil</strong> (SPF).</li>
        <li>Menghitung banyak pembagi, fungsi phi Euler, dan prima di rentang besar.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'menyaring prima', 'desc' => 'Uji prima, saringan Eratosthenes, dan alasan kebenarannya.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Bilangan Prima: Batu Bata Bilangan</h2>
    <div class="prose">
        <p>Bilangan prima adalah bilangan lebih dari 1 yang hanya habis dibagi 1 dan dirinya sendiri: 2, 3, 5, 7, 11, … Setiap bilangan bulat ≥ 2 bisa ditulis sebagai hasil kali prima dengan tepat satu cara (<strong>teorema dasar aritmetika</strong>), misalnya 360 = 2³ · 3² · 5. Banyak soal teori bilangan menjadi mudah setelah bilangannya difaktorkan.</p>
        <p>Untuk menguji <em>satu</em> bilangan, cukup coba pembagi sampai √n:</p>
    </div>
    @include('lessons.code', ['cpp' => $bpTrial, 'js' => null, 'py' => null])
    <div class="prose"><p>Tetapi untuk mencari <em>semua</em> prima sampai 10<sup>7</sup>, menguji satu per satu terlalu lambat. Lebih baik membalik caranya: bukan "apakah n punya pembagi?", tetapi "coret semua kelipatan dari setiap prima".</p></div>
</section>

<section class="lesson-section" id="saringan" data-toc="Saringan Eratosthenes">
    <h2>Saringan Eratosthenes</h2>
    <div class="steps">
        <div class="step-card"><b>Mulai</b><span>Anggap semua 2..N prima.</span></div>
        <div class="step-card"><b>Ambil p terkecil</b><span>yang belum dicoret: p pasti prima.</span></div>
        <div class="step-card"><b>Coret kelipatan</b><span>p², p² + p, p² + 2p, … ≤ N.</span></div>
        <div class="step-card"><b>Berhenti</b><span>saat p² &gt; N. Sisanya prima.</span></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Mengapa mulai dari p²?</strong> Kelipatan p yang lebih kecil, yaitu k · p dengan k &lt; p, punya faktor prima lebih kecil dari p, sehingga sudah dicoret sebelumnya. <strong>Mengapa berhenti di √N?</strong> Bilangan komposit ≤ N selalu punya faktor prima ≤ √N.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Setiap langkah mengambil prima berikutnya dan mencoret kelipatannya sekaligus. Angka kecil di pojok kotak adalah prima yang mencoretnya pertama kali, yaitu faktor prima terkecil bilangan itu.</p></div>
    <div data-viz="sieve"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'faktorisasi cepat', 'desc' => 'Saringan faktor prima terkecil dan banyak pembagi.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Faktorisasi Banyak Bilangan</h2>
    @include('lessons.walkthrough', [
        'title' => 'Saringan SPF + faktorisasi',
        'steps' => $bpSteps,
        'sample' => ['input' => "3\n360\n97\n1000000\n", 'output' => "2^3 3^2 5^1 (24 pembagi)\n97^1 (2 pembagi)\n2^6 5^6 (49 pembagi)\n"],
        'py' => $bpPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: x = 360</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>x</th><th>spf[x]</th><th>Dibagi berapa kali</th><th>Faktor</th></tr>
            <tr><td>360</td><td>2</td><td>360 → 180 → 90 → 45 (3 kali)</td><td>2³</td></tr>
            <tr><td>45</td><td>3</td><td>45 → 15 → 5 (2 kali)</td><td>3²</td></tr>
            <tr><td>5</td><td>5</td><td>5 → 1 (1 kali)</td><td>5¹</td></tr>
            <tr class="ok"><td>1</td><td>–</td><td>selesai</td><td>(3+1)(2+1)(1+1) = <b>24 pembagi</b></td></tr>
        </table>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Tugas</th><th>Cara</th><th>Waktu</th></tr>
        <tr><td>Uji satu n</td><td>Coba pembagi sampai √n</td><td><code>O(√n)</code></td></tr>
        <tr><td>Semua prima ≤ N</td><td>Saringan Eratosthenes</td><td><code>O(N log log N)</code></td></tr>
        <tr><td>Faktorkan banyak x ≤ N</td><td>Saringan SPF lalu bagi berulang</td><td><code>O(N log log N)</code> + <code>O(log x)</code> per x</td></tr>
        <tr><td>Faktorkan satu x ≤ 10<sup>12</sup></td><td>Coba pembagi sampai √x</td><td><code>O(√x)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow i · i.</strong> Untuk i mendekati 10<sup>6</sup> atau lebih, <code>i * i</code> dalam <code>int</code> bisa melewati 2<sup>31</sup>. Hitung dengan <code>long long</code>, atau tulis syarat sebagai <code>i &lt;= n / i</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>0 dan 1 bukan prima.</strong> Saringan harus menandai keduanya secara manual.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Memori.</strong> Saringan sampai 10<sup>8</sup> dengan <code>vector&lt;bool&gt;</code> butuh sekitar 12 MB, tetapi dengan <code>int spf[]</code> sudah 400 MB. Pakai SPF hanya sampai batas yang dibutuhkan (biasanya ≤ 10<sup>7</sup>).</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'phi Euler & saringan rentang', 'desc' => 'Fungsi aritmetika dan prima di rentang yang sangat besar.'])

<section class="lesson-section" id="phi" data-toc="Fungsi Phi Euler">
    <h2>Fungsi Phi Euler</h2>
    <div class="prose">
        <p><code>phi(n)</code> menghitung bilangan 1..n yang FPB-nya dengan n sama dengan 1. Contoh phi(12) = 4 (yaitu 1, 5, 7, 11). Rumusnya <code>n · Π(1 − 1/p)</code> atas prima p yang membagi n, karena setiap prima "membuang" sepersekian bilangan yang menjadi kelipatannya. Phi muncul di soal pecahan sederhana, rotasi kalung, dan teorema Euler <code>a<sup>phi(m)</sup> ≡ 1 (mod m)</code>.</p>
    </div>
    @include('lessons.code', ['cpp' => $bpPhi, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="rentang" data-toc="Saringan Rentang">
    <h2>Saringan Rentang</h2>
    <div class="prose">
        <p>Bagaimana jika ditanya prima di antara 10<sup>12</sup> dan 10<sup>12</sup> + 10<sup>6</sup>? Array sepanjang 10<sup>12</sup> mustahil, tetapi bilangan komposit di rentang itu pasti punya faktor prima ≤ 10<sup>6</sup>. Saring dulu prima kecil sampai √R, lalu coret kelipatannya hanya di dalam [L, R].</p>
    </div>
    @include('lessons.code', ['cpp' => $bpSegment, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Banyak prima di [L, R]</h4><p>Saringan + prefix count, pertanyaan O(1).</p></div>
        <div class="pattern"><h4>Banyak / jumlah pembagi</h4><p>Faktorkan: Π(e + 1) dan Π(p<sup>e+1</sup> − 1)/(p − 1).</p></div>
        <div class="pattern"><h4>Pecahan sederhana</h4><p>Σ phi(q): banyak p/q dengan FPB(p, q) = 1.</p></div>
        <div class="pattern"><h4>FPB / KPK banyak bilangan</h4><p>Bandingkan pangkat prima: FPB pakai minimum, KPK pakai maksimum.</p></div>
        <div class="pattern"><h4>Kuadrat sempurna</h4><p>Semua pangkat prima genap. Hasil kali menjadi kuadrat: paritas pangkat.</p></div>
        <div class="pattern"><h4>Goldbach & teman</h4><p>Saringan sekali, lalu periksa banyak pasangan prima dengan cepat.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Kelipatan 7 yang lebih kecil dari 49 (14, 21, 28, 35, 42) masing-masing punya faktor 2, 3, atau 5, jadi sudah dicoret.">
        <p class="quiz-q">Saat saringan sampai di p = 7, kelipatan pertama yang perlu dicoret adalah …</p>
        <div class="quiz-options">
            <button class="quiz-option">14</button>
            <button class="quiz-option">49</button>
            <button class="quiz-option">7</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="72 = 2³ · 3², sehingga banyak pembagi = (3 + 1)(2 + 1) = 12.">
        <p class="quiz-q">Berapa banyak pembagi 72?</p>
        <div class="quiz-options">
            <button class="quiz-option">6</button>
            <button class="quiz-option">8</button>
            <button class="quiz-option">12</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Bilangan saling prima dengan 10 di 1..10: 1, 3, 7, 9. Rumus: 10 · (1 − 1/2)(1 − 1/5) = 4.">
        <p class="quiz-q">Berapa phi(10)?</p>
        <div class="quiz-options">
            <button class="quiz-option">4</button>
            <button class="quiz-option">5</button>
            <button class="quiz-option">9</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
