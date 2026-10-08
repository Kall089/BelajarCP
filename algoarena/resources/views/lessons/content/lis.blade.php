@php
    $lisSteps = [
        ['Header dan membaca array', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];
CPP, <<<'TXT'
<p>Contoh: <code>a = [3, 10, 2, 1, 20, 4, 6, 8]</code>, n = 8.</p>
TXT],
        ['State dan base case', <<<'CPP'

    // dp[i]   = panjang LIS yang BERAKHIR tepat di a[i]
    // prev[i] = indeks elemen sebelum a[i] pada LIS tersebut (-1 jika tidak ada)
    vector<int> dp(n, 1), prev(n, -1);
CPP, <<<'TXT'
<p>Kata kuncinya <strong>berakhir di i</strong>. Dengan begitu kita tahu persis elemen terakhirnya, sehingga mudah memeriksa apakah elemen baru boleh disambung.</p>
<p>Setiap elemen sendirian sudah merupakan subsequence naik sepanjang 1, jadi semua <code>dp[i]</code> dimulai dari 1.</p>
TXT],
        ['Transisi: sambung ke elemen sebelumnya', <<<'CPP'

    for (int i = 0; i < n; i++) {
        for (int j = 0; j < i; j++) {
            if (a[j] < a[i] && dp[j] + 1 > dp[i]) {
                dp[i] = dp[j] + 1;
                prev[i] = j;
            }
        }
    }
CPP, <<<'TXT'
<p>Untuk setiap i, periksa semua j sebelumnya. Jika <code>a[j] &lt; a[i]</code>, LIS yang berakhir di j bisa diperpanjang dengan a[i], menjadi <code>dp[j] + 1</code>. Ambil yang terpanjang dan catat j-nya.</p>
<pre>a    : 3  10  2  1  20  4  6  8
dp   : 1   2  1  1   3  2  3  4
prev : -   0  -  -   1  0  5  6</pre>
<div class="wt-tip">Syarat <code>a[j] &lt; a[i]</code> berarti <em>naik tegas</em>. Untuk "tidak turun" (boleh sama), ganti menjadi <code>a[j] &lt;= a[i]</code>.</div>
TXT],
        ['Cari ujung LIS terbaik', <<<'CPP'

    int ujung = 0;
    for (int i = 1; i < n; i++) {
        if (dp[i] > dp[ujung]) ujung = i;
    }
CPP, <<<'TXT'
<p>LIS terpanjang bisa berakhir di mana saja, jadi jawabannya adalah <strong>maksimum seluruh array dp</strong>, bukan <code>dp[n − 1]</code>. Pada contoh, <code>dp[7] = 4</code>.</p>
<div class="wt-warn">Kesalahan paling umum: mencetak <code>dp[n − 1]</code>. Untuk <code>[5, 6, 7, 1]</code> itu memberi 1, padahal jawabannya 3.</div>
TXT],
        ['Telusur balik lewat prev', <<<'CPP'

    vector<int> lis;
    for (int i = ujung; i != -1; i = prev[i]) lis.push_back(a[i]);
    reverse(lis.begin(), lis.end());

    cout << dp[ujung] << '\n';
    for (int i = 0; i < (int)lis.size(); i++) {
        cout << lis[i] << (i + 1 < (int)lis.size() ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Ikuti rantai <code>prev</code> dari ujung: 8 → 6 → 4 → 3. Hasilnya terbalik, jadi dibalik dulu sebelum dicetak: <code>3 4 6 8</code>.</p>
TXT],
    ];

    $lisJs = <<<'JS'
const [n] = readInts();
const a = readInts();

const dp = new Array(n).fill(1);
const prev = new Array(n).fill(-1);
for (let i = 0; i < n; i++) {
  for (let j = 0; j < i; j++) {
    if (a[j] < a[i] && dp[j] + 1 > dp[i]) {
      dp[i] = dp[j] + 1;
      prev[i] = j;
    }
  }
}

let ujung = 0;
for (let i = 1; i < n; i++) if (dp[i] > dp[ujung]) ujung = i;
const lis = [];
for (let i = ujung; i !== -1; i = prev[i]) lis.push(a[i]);
console.log(dp[ujung] + "\n" + lis.reverse().join(" "));
JS;

    $lisPy = <<<'PY'
n = int(input())
a = list(map(int, input().split()))

dp = [1] * n
prev = [-1] * n
for i in range(n):
    for j in range(i):
        if a[j] < a[i] and dp[j] + 1 > dp[i]:
            dp[i] = dp[j] + 1
            prev[i] = j

ujung = max(range(n), key=lambda i: (dp[i], -i))
lis = []
i = ujung
while i != -1:
    lis.append(a[i])
    i = prev[i]
print(dp[ujung])
print(*reversed(lis))
PY;

    $lisFast = <<<'CPP'
// LIS O(n log n)
// tails[k] = nilai ujung TERKECIL dari semua subsequence naik sepanjang k+1
vector<int> tails;
for (int x : a) {
    auto it = lower_bound(tails.begin(), tails.end(), x);  // elemen pertama >= x
    if (it == tails.end()) tails.push_back(x);  // x lebih besar dari semua: perpanjang
    else *it = x;                               // ganti dengan ujung yang lebih kecil
}
cout << tails.size() << '\n';
CPP;

    $lisNonDec = <<<'CPP'
// Tidak turun (boleh sama): pakai upper_bound
auto it = upper_bound(tails.begin(), tails.end(), x);   // elemen pertama > x
CPP;

    $lisRecon = <<<'CPP'
// O(n log n) + rekonstruksi: simpan INDEKS, bukan nilai
vector<int> tailIdx, prev(n, -1);
for (int i = 0; i < n; i++) {
    int k = lower_bound(tailIdx.begin(), tailIdx.end(), i,
                        [&](int p, int q) { return a[p] < a[q]; }) - tailIdx.begin();
    if (k > 0) prev[i] = tailIdx[k - 1];        // pendahulu = ujung panjang k
    if (k == (int)tailIdx.size()) tailIdx.push_back(i);
    else tailIdx[k] = i;
}
vector<int> lis;
for (int i = tailIdx.back(); i != -1; i = prev[i]) lis.push_back(a[i]);
reverse(lis.begin(), lis.end());
CPP;

    $lisEnvelope = <<<'CPP'
// Amplop bersarang (Russian doll): amplop (w, h) masuk ke (W, H) jika w < W dan h < H.
// Urutkan w naik; untuk w sama urutkan h TURUN, lalu cari LIS dari h.
sort(env.begin(), env.end(), [](auto& p, auto& q) {
    return p.first != q.first ? p.first < q.first : p.second > q.second;
});
// jawaban = LIS (naik tegas) dari env[i].second
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mendefinisikan state "<strong>berakhir di i</strong>" dan memahami mengapa jawabannya adalah maksimum seluruh dp.</li>
        <li>Menulis LIS O(n²) di C++ beserta rekonstruksi subsequence-nya.</li>
        <li>Mempercepatnya menjadi <strong>O(n log n)</strong> dengan array <code>tails</code> dan <code>lower_bound</code>.</li>
        <li>Mengenali soal yang diam-diam adalah LIS: amplop bersarang, LCS permutasi, rantai pasangan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'subsequence yang terus naik', 'desc' => 'State "berakhir di i" dan mengisi tabel dp dengan tangan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Barisan Pemain Berdasarkan Tinggi</h2>
    <div class="prose">
        <p>Delapan siswa berdiri berjajar dengan tinggi <code>3, 10, 2, 1, 20, 4, 6, 8</code> (dalam satuan bebas). Guru ingin memilih sebanyak mungkin siswa <strong>tanpa mengubah urutan berdiri</strong>, sehingga tinggi yang terpilih terus naik dari kiri ke kanan.</p>
        <p>Itulah <strong>Longest Increasing Subsequence</strong> (LIS). Jawabannya 4, misalnya <code>3, 4, 6, 8</code> atau <code>1, 4, 6, 8</code>.</p>
        <p>Pertanyaan pentingnya: kalau aku tahu LIS untuk awalan array, bagaimana cara memperpanjangnya dengan elemen baru? Elemen baru hanya boleh ditempel jika <strong>lebih besar dari elemen terakhir</strong> LIS itu. Maka state-nya harus menyimpan elemen terakhir:</p>
    </div>
    <div class="recurrence"><small>State dan transisi</small>dp[i] = panjang subsequence naik terpanjang yang BERAKHIR di a[i]
dp[i] = 1 + max( dp[j] )   untuk semua j &lt; i dengan a[j] &lt; a[i]
        (jika tidak ada j yang memenuhi, dp[i] = 1)
jawaban = max( dp[0], dp[1], …, dp[n−1] )</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>"Berakhir di i" adalah pola state yang sangat sering dipakai. Pola ini memberi kita informasi tentang elemen terakhir, sehingga transisi bisa memeriksa syarat sambungan.</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>i</th><th>a[i]</th><th>j yang boleh (a[j] &lt; a[i])</th><th>dp[i]</th><th>prev[i]</th></tr>
            <tr><td>0</td><td>3</td><td>–</td><td><span class="new">1</span></td><td>–</td></tr>
            <tr><td>1</td><td>10</td><td>j=0 (3): 1+1</td><td><span class="new">2</span></td><td>0</td></tr>
            <tr><td>2</td><td>2</td><td>–</td><td><span class="new">1</span></td><td>–</td></tr>
            <tr><td>3</td><td>1</td><td>–</td><td><span class="new">1</span></td><td>–</td></tr>
            <tr class="hl"><td>4</td><td>20</td><td>j=0,1,2,3 → terbaik j=1 (10): 2+1</td><td><span class="new">3</span></td><td>1</td></tr>
            <tr><td>5</td><td>4</td><td>j=0,2,3 → semua dp=1</td><td><span class="new">2</span></td><td>0</td></tr>
            <tr class="hl"><td>6</td><td>6</td><td>j=0,2,3,5 → terbaik j=5 (4): 2+1</td><td><span class="new">3</span></td><td>5</td></tr>
            <tr class="ok"><td>7</td><td>8</td><td>terbaik j=6 (6): 3+1</td><td><span class="new">4</span></td><td>6</td></tr>
        </table>
    </div>
    <div class="prose">
        <p>Maksimum dp adalah 4 di i = 7. Rantai prev: 7 → 6 → 5 → 0, yaitu <code>3, 4, 6, 8</code>. Perhatikan bahwa elemen 20 punya dp = 3, tetapi tidak ada yang bisa disambung setelahnya.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi LIS</h2>
    <div class="prose">
        <p>Batang kuning adalah elemen yang sedang dihitung. Batang yang lebih pendek di kirinya (biru) bisa disambung, yang lebih tinggi atau sama (merah) tidak. Ubah array untuk mencoba kasus lain.</p>
    </div>
    <div data-viz="lis"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>State</b><span><code>dp[i]</code> = panjang LIS yang berakhir tepat di a[i].</span></div>
        <div class="step-card"><b>Base case</b><span>Semua <code>dp[i] = 1</code>: elemen itu sendirian.</span></div>
        <div class="step-card"><b>Transisi</b><span>Untuk setiap j &lt; i dengan a[j] &lt; a[i], coba <code>dp[j] + 1</code>.</span></div>
        <div class="step-card"><b>Jawaban</b><span>Maksimum seluruh dp, bukan elemen terakhir.</span></div>
        <div class="step-card"><b>Cek batasan</b><span>n ≤ 5000: O(n²) cukup. n sampai 2 · 10<sup>5</sup>: pakai O(n log n).</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'LIS O(n²) di C++', 'desc' => 'Program lengkap beserta rekonstruksi subsequence-nya.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: LIS + Rekonstruksi</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> diberikan n bilangan. Cetak panjang LIS (naik tegas), lalu salah satu LIS-nya.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'LIS O(n²) dengan prev',
        'steps' => $lisSteps,
        'sample' => ['input' => "8\n3 10 2 1 20 4 6 8\n", 'output' => "4\n3 4 6 8\n"],
        'js' => $lisJs,
        'py' => $lisPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Versi</th><th>Waktu</th><th>Batas n yang aman (±1 detik)</th></tr>
        <tr><td>Dua loop (dp[i])</td><td><code>O(n²)</code></td><td>sekitar 10<sup>4</sup></td></tr>
        <tr><td>tails + binary search</td><td><code>O(n log n)</code></td><td>sekitar 10<sup>6</sup></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Naik tegas atau tidak turun?</strong> Baca soal dengan teliti. "Naik" biasanya berarti <code>&lt;</code>, "tidak turun" berarti <code>≤</code>. Pada versi cepat, ini menentukan <code>lower_bound</code> atau <code>upper_bound</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Subsequence, bukan subarray.</strong> Elemen tidak harus bersebelahan. Untuk subarray naik terpanjang, cukup satu loop: perpanjang jika <code>a[i] &gt; a[i−1]</code>, selain itu mulai dari 1.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'LIS O(n log n)', 'desc' => 'Array tails, binary search, rekonstruksi, dan soal-soal yang menyamar.'])

<section class="lesson-section" id="cepat" data-toc="LIS O(n log n)">
    <h2>LIS O(n log n) dengan Array tails</h2>
    <div class="prose">
        <p>Simpan <code>tails[k]</code> = <strong>ujung terkecil</strong> dari semua subsequence naik sepanjang k + 1 yang sudah ditemui. Ujung kecil lebih baik, karena memberi lebih banyak peluang untuk disambung nanti.</p>
        <p>Array <code>tails</code> selalu urut naik, sehingga posisi untuk setiap elemen baru bisa dicari dengan binary search.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>x</th><th>Aksi</th><th>tails sesudahnya</th></tr>
            <tr><td>3</td><td>tails kosong, tambahkan</td><td><code>[3]</code></td></tr>
            <tr><td>10</td><td>lebih besar dari semua, tambahkan</td><td><code>[3, 10]</code></td></tr>
            <tr class="hl"><td>2</td><td>ganti 3 (elemen pertama ≥ 2)</td><td><code>[2, 10]</code></td></tr>
            <tr class="hl"><td>1</td><td>ganti 2</td><td><code>[1, 10]</code></td></tr>
            <tr><td>20</td><td>tambahkan</td><td><code>[1, 10, 20]</code></td></tr>
            <tr class="hl"><td>4</td><td>ganti 10</td><td><code>[1, 4, 20]</code></td></tr>
            <tr class="hl"><td>6</td><td>ganti 20</td><td><code>[1, 4, 6]</code></td></tr>
            <tr class="ok"><td>8</td><td>tambahkan</td><td><code>[1, 4, 6, 8]</code> → panjang 4</td></tr>
        </table>
    </div>
    @include('lessons.code', ['cpp' => $lisFast, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>tails bukan LIS-nya.</strong> Isi akhir tails kebetulan <code>[1, 4, 6, 8]</code> di contoh ini, tetapi secara umum isinya bisa berupa campuran dari subsequence yang berbeda. Hanya <em>panjangnya</em> yang dijamin benar.</p>
    </div>
    <div class="prose"><p>Untuk versi tidak turun, cukup ganti binary search-nya:</p></div>
    @include('lessons.code', ['cpp' => $lisNonDec, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>Rekonstruksi pada versi cepat</h3>
        <p>Simpan <strong>indeks</strong> di tails. Saat elemen i masuk ke posisi k, pendahulunya adalah ujung panjang k (posisi k − 1) pada saat itu.</p>
    </div>
    @include('lessons.code', ['cpp' => $lisRecon, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="bukti" data-toc="Mengapa tails Benar?">
    <h2>Mengapa tails Benar?</h2>
    <div class="proof">
        <p><strong>tails selalu urut naik.</strong> Jika ada subsequence naik sepanjang k + 2 berujung di y, maka k + 1 elemen pertamanya membentuk subsequence sepanjang k + 1 yang berujung di nilai yang lebih kecil dari y. Jadi <code>tails[k] &lt; tails[k + 1]</code>.</p>
        <p><strong>Setiap update menjaga arti tails.</strong> Elemen x bisa menyambung subsequence mana pun yang ujungnya &lt; x. Panjang terbaik yang bisa dicapai adalah (posisi elemen pertama ≥ x) + 1. Di posisi itu, x menjadi ujung yang lebih kecil (atau sama), jadi kita menggantinya. Jika tidak ada elemen ≥ x, x memperpanjang subsequence terpanjang.</p>
        <p>Karena setiap panjang yang mungkin selalu tercatat, ukuran tails di akhir adalah panjang LIS.</p>
    </div>
</section>

<section class="lesson-section" id="pola" data-toc="Soal yang Menyamar">
    <h2>Soal yang Menyamar sebagai LIS</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Amplop bersarang</h4><p>Pasangan (w, h). Urutkan w naik dan h turun untuk w sama, lalu LIS dari h.</p></div>
        <div class="pattern"><h4>LCS dua permutasi</h4><p>Petakan b ke posisi di a, lalu LIS. O(n log n).</p></div>
        <div class="pattern"><h4>Minimum hapus</h4><p>Hapus sesedikit mungkin agar array naik: n − LIS.</p></div>
        <div class="pattern"><h4>Jumlah terbesar</h4><p>Subsequence naik dengan jumlah terbesar: ganti <code>dp[j] + 1</code> menjadi <code>dp[j] + a[i]</code>.</p></div>
        <div class="pattern"><h4>Banyak LIS</h4><p>Simpan juga <code>cnt[i]</code>: banyak LIS berakhir di i. Jumlahkan cnt[j] untuk j yang memberi panjang terbaik.</p></div>
        <div class="pattern"><h4>Bitonik</h4><p>Naik lalu turun: LIS dari kiri + LDS dari kanan − 1, untuk setiap puncak i.</p></div>
    </div>
    @include('lessons.code', ['cpp' => $lisEnvelope, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Mengapa h diurutkan <strong>turun</strong> untuk w yang sama? Agar dua amplop dengan lebar sama tidak bisa saling memasuki: urutan turun membuat LIS tidak mungkin memilih keduanya.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="LIS terpanjang bisa berakhir di mana saja. Contoh [5, 6, 7, 1]: dp[3] = 1 padahal jawabannya 3.">
        <p class="quiz-q">Dengan dp[i] = LIS yang berakhir di i, di mana jawaban akhirnya?</p>
        <div class="quiz-options">
            <button class="quiz-option">dp[n − 1]</button>
            <button class="quiz-option">dp[0]</button>
            <button class="quiz-option">Maksimum seluruh dp</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="tails = [1, 4, 20]. Elemen pertama ≥ 5 adalah 20, jadi diganti: [1, 4, 5].">
        <p class="quiz-q">tails = [1, 4, 20], elemen berikutnya 5. Bagaimana tails sesudahnya?</p>
        <div class="quiz-options">
            <button class="quiz-option">[1, 4, 20, 5]</button>
            <button class="quiz-option">[1, 4, 5]</button>
            <button class="quiz-option">[1, 5, 20]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Untuk tidak turun, elemen yang sama boleh disambung, jadi kita mencari elemen pertama yang lebih BESAR (upper_bound).">
        <p class="quiz-q">Untuk subsequence <strong>tidak turun</strong> (boleh sama), fungsi apa yang dipakai?</p>
        <div class="quiz-options">
            <button class="quiz-option">upper_bound</button>
            <button class="quiz-option">lower_bound</button>
            <button class="quiz-option">sort</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
