@php
    $bmSteps = [
        ['Header dan membaca matriks jarak', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<long long>> d(n, vector<long long>(n));
    for (auto& baris : d)
        for (auto& x : baris) cin >> x;
CPP, <<<'TXT'
<p>Ada n kota (n ≤ 16) dan <code>d[i][j]</code> = jarak dari kota i ke kota j. Kita mencari rute terpendek yang berangkat dari kota 0, mengunjungi setiap kota <strong>tepat sekali</strong>, lalu kembali ke kota 0. Ini adalah <em>Travelling Salesman Problem</em> (TSP).</p>
TXT],
        ['State: himpunan kota yang sudah dikunjungi', <<<'CPP'

    int FULL = 1 << n;
    // dp[mask][v] = jarak terpendek: mulai di kota 0, sudah mengunjungi
    //               tepat kota-kota di mask, dan sekarang berada di kota v
    vector<vector<long long>> dp(FULL, vector<long long>(n, INF));
    vector<vector<int>> asal(FULL, vector<int>(n, -1));
    dp[1][0] = 0;
CPP, <<<'TXT'
<p>Agar rute bisa diperpanjang, kita perlu tahu dua hal: kota mana saja yang <strong>sudah</strong> dikunjungi (supaya tidak dikunjungi lagi), dan di kota mana kita <strong>sekarang</strong>. Himpunan kota disimpan sebagai bilangan biner <code>mask</code>: bit ke-i bernilai 1 jika kota i sudah dikunjungi.</p>
<pre>mask = 1  = 0001₂ → hanya kota 0
mask = 11 = 1011₂ → kota 0, 1, dan 3</pre>
<p><code>asal[mask][v]</code> mencatat kota sebelum v, untuk menyusun ulang rute. Base case: di kota 0, hanya kota 0 yang sudah dikunjungi, jarak 0.</p>
TXT],
        ['Transisi: pergi ke kota yang belum dikunjungi', <<<'CPP'

    for (int mask = 1; mask < FULL; mask++) {
        for (int v = 0; v < n; v++) {
            if (!(mask >> v & 1) || dp[mask][v] == INF) continue;
            for (int u = 0; u < n; u++) {
                if (mask >> u & 1) continue;
                int baru = mask | (1 << u);
                long long jarak = dp[mask][v] + d[v][u];
                if (jarak < dp[baru][u]) {
                    dp[baru][u] = jarak;
                    asal[baru][u] = v;
                }
            }
        }
    }
CPP, <<<'TXT'
<p>Dari setiap state yang sudah tercapai, coba pergi ke setiap kota u yang bit-nya masih 0. Mask baru didapat dengan menyalakan bit u: <code>mask | (1 &lt;&lt; u)</code>.</p>
<ul>
    <li><code>mask &gt;&gt; v &amp; 1</code> mengambil bit ke-v: apakah v ada di mask.</li>
    <li>Mask baru selalu <strong>lebih besar</strong> dari mask lama (ada bit yang dinyalakan), jadi loop mask yang naik sudah menjamin urutan pengisian yang benar.</li>
</ul>
<div class="wt-tip">Teknik ini disebut "push": dari state sekarang, dorong nilai ke state berikutnya. Kebalikannya, "pull", menarik dari state sebelumnya. Keduanya sah.</div>
TXT],
        ['Kembali ke kota 0', <<<'CPP'

    long long jawaban = INF;
    int akhir = -1;
    for (int v = 1; v < n; v++) {
        if (dp[FULL - 1][v] + d[v][0] < jawaban) {
            jawaban = dp[FULL - 1][v] + d[v][0];
            akhir = v;
        }
    }
CPP, <<<'TXT'
<p><code>FULL − 1</code> adalah mask yang semua bitnya 1 (semua kota sudah dikunjungi). Dari kota terakhir v mana pun, tambahkan jarak pulang ke kota 0, lalu ambil yang terkecil.</p>
TXT],
        ['Menyusun ulang rute', <<<'CPP'

    vector<int> rute;
    int mask = FULL - 1, v = akhir;
    while (v != -1) {
        rute.push_back(v);
        int sebelum = asal[mask][v];
        mask ^= (1 << v);
        v = sebelum;
    }
    reverse(rute.begin(), rute.end());
    rute.push_back(0);
CPP, <<<'TXT'
<p>Mundur dari state akhir: catat kota v, matikan bit-nya dengan XOR (<code>mask ^= 1 &lt;&lt; v</code>), lalu pindah ke <code>asal[mask][v]</code>. Rantai berhenti di kota 0, yang asal-nya −1.</p>
TXT],
        ['Mencetak hasil', <<<'CPP'

    cout << jawaban << '\n';
    for (int i = 0; i < (int)rute.size(); i++) {
        cout << rute[i] << (i + 1 < (int)rute.size() ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Untuk contoh, jarak terpendeknya 17 dengan rute <code>0 3 2 1 0</code> (4 + 5 + 6 + 2). Arah sebaliknya, <code>0 1 2 3 0</code>, sama panjangnya.</p>
<div class="wt-warn">Ukuran tabel 2<sup>n</sup> × n. Untuk n = 16 itu 1 juta sel <code>long long</code> (8 MB), ditambah tabel asal. Untuk n = 20 sudah 20 juta sel: periksa batas memori.</div>
TXT],
    ];

    $bmJs = <<<'JS'
const [n] = readInts();
const d = [];
for (let i = 0; i < n; i++) d.push(readInts());

const FULL = 1 << n;
const dp = Array.from({ length: FULL }, () => new Array(n).fill(Infinity));
const asal = Array.from({ length: FULL }, () => new Array(n).fill(-1));
dp[1][0] = 0;
for (let mask = 1; mask < FULL; mask++) {
  for (let v = 0; v < n; v++) {
    if (!((mask >> v) & 1) || dp[mask][v] === Infinity) continue;
    for (let u = 0; u < n; u++) {
      if ((mask >> u) & 1) continue;
      const baru = mask | (1 << u);
      const jarak = dp[mask][v] + d[v][u];
      if (jarak < dp[baru][u]) { dp[baru][u] = jarak; asal[baru][u] = v; }
    }
  }
}

let jawaban = Infinity, akhir = -1;
for (let v = 1; v < n; v++) {
  if (dp[FULL - 1][v] + d[v][0] < jawaban) { jawaban = dp[FULL - 1][v] + d[v][0]; akhir = v; }
}
const rute = [];
for (let mask = FULL - 1, v = akhir; v !== -1; ) {
  rute.push(v);
  const sebelum = asal[mask][v];
  mask ^= 1 << v;
  v = sebelum;
}
rute.reverse().push(0);
console.log(jawaban + "\n" + rute.join(" "));
JS;

    $bmPy = <<<'PY'
n = int(input())
d = [list(map(int, input().split())) for _ in range(n)]

INF = float("inf")
FULL = 1 << n
dp = [[INF] * n for _ in range(FULL)]
asal = [[-1] * n for _ in range(FULL)]
dp[1][0] = 0
for mask in range(1, FULL):
    for v in range(n):
        if not (mask >> v & 1) or dp[mask][v] == INF:
            continue
        for u in range(n):
            if mask >> u & 1:
                continue
            baru = mask | (1 << u)
            jarak = dp[mask][v] + d[v][u]
            if jarak < dp[baru][u]:
                dp[baru][u] = jarak
                asal[baru][u] = v

jawaban, akhir = INF, -1
for v in range(1, n):
    if dp[FULL - 1][v] + d[v][0] < jawaban:
        jawaban, akhir = dp[FULL - 1][v] + d[v][0], v

rute, mask, v = [], FULL - 1, akhir
while v != -1:
    rute.append(v)
    sebelum = asal[mask][v]
    mask ^= 1 << v
    v = sebelum
rute.reverse()
rute.append(0)
print(jawaban)
print(*rute)
PY;

    $bmOps = <<<'CPP'
int mask = 0b1011;              // himpunan {0, 1, 3}

bool ada   = mask >> 3 & 1;     // apakah 3 di dalam himpunan?   → true
int tambah = mask | (1 << 2);   // tambahkan 2                    → 0b1111
int hapus  = mask & ~(1 << 1);  // buang 1                        → 0b1001
int balik  = mask ^ (1 << 0);   // balik status 0                 → 0b1010
int ukuran = __builtin_popcount(mask);  // banyak anggota         → 3
int semua  = (1 << n) - 1;      // himpunan {0, 1, ..., n-1}

// Menjelajahi SEMUA himpunan bagian dari {0..n-1}
for (int m = 0; m < (1 << n); m++) { /* ... */ }
CPP;

    $bmAssign = <<<'CPP'
// Penugasan: n orang, n tugas, c[i][j] = biaya orang i mengerjakan tugas j.
// dp[mask] = biaya minimum jika tugas-tugas di mask sudah dibagikan
//            ke popcount(mask) orang PERTAMA.
vector<long long> dp(1 << n, INF);
dp[0] = 0;
for (int mask = 0; mask < (1 << n); mask++) {
    if (dp[mask] == INF) continue;
    int i = __builtin_popcount(mask);          // orang berikutnya
    if (i == n) continue;
    for (int j = 0; j < n; j++)
        if (!(mask >> j & 1))
            dp[mask | 1 << j] = min(dp[mask | 1 << j], dp[mask] + c[i][j]);
}
// jawaban: dp[(1 << n) - 1]
CPP;

    $bmSub = <<<'CPP'
// Menjelajahi semua SUBMASK dari mask (himpunan bagian dari mask)
for (int sub = mask; sub > 0; sub = (sub - 1) & mask) {
    // ... sub adalah himpunan bagian tak kosong dari mask
}

// Contoh: membagi n orang ke dalam kelompok-kelompok.
// dp[mask] = biaya minimum mengelompokkan orang di mask
// biayaKelompok[sub] dihitung lebih dulu
dp[0] = 0;
for (int mask = 1; mask < (1 << n); mask++) {
    dp[mask] = INF;
    for (int sub = mask; sub > 0; sub = (sub - 1) & mask)
        dp[mask] = min(dp[mask], dp[mask ^ sub] + biayaKelompok[sub]);
}
// total kerja: O(3^n), bukan O(4^n)
CPP;

    $bmSos = <<<'CPP'
// Sum over Subsets (SOS DP):
// F[mask] = jumlah A[sub] untuk SEMUA sub yang merupakan himpunan bagian mask
// O(n · 2^n), jauh lebih cepat dari O(3^n)
vector<long long> F(A);
for (int b = 0; b < n; b++)
    for (int mask = 0; mask < (1 << n); mask++)
        if (mask >> b & 1) F[mask] += F[mask ^ (1 << b)];
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menyimpan sebuah <strong>himpunan</strong> sebagai bilangan biner dan memakai operasi bit dasar.</li>
        <li>Mengenali tanda soal DP bitmask: n kecil (≤ 20) dan jawabannya bergantung pada "siapa saja yang sudah dipakai".</li>
        <li>Menulis DP penugasan dan TSP di C++, termasuk rekonstruksi rutenya.</li>
        <li>Menjelajahi submask dalam O(3<sup>n</sup>) dan mengenal SOS DP.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'himpunan sebagai angka', 'desc' => 'Operasi bit dasar dan DP penugasan.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Himpunan sebagai Bilangan Biner</h2>
    <div class="prose">
        <p>Bayangkan 4 lampu berjajar, bernomor 0 sampai 3 dari kanan. Setiap lampu menyala (1) atau mati (0). Keadaan semua lampu bisa ditulis sebagai bilangan biner, misalnya <code>1011</code>: lampu 0, 1, dan 3 menyala. Bilangan biner itu sama dengan angka desimal 11.</p>
        <p>Dengan cara ini, setiap <strong>himpunan bagian</strong> dari n benda punya nomor unik dari 0 sampai 2<sup>n</sup> − 1. Nomor itu bisa langsung dipakai sebagai indeks array: <code>dp[mask]</code>.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table bit-table">
            <tr><th>Tujuan</th><th>Ekspresi</th><th>mask = 1011₂</th></tr>
            <tr><td>Apakah i ada?</td><td><code>mask &gt;&gt; i &amp; 1</code></td><td>i = 2 → 0 (tidak ada)</td></tr>
            <tr><td>Tambahkan i</td><td><code>mask | (1 &lt;&lt; i)</code></td><td>i = 2 → 1111₂</td></tr>
            <tr><td>Buang i</td><td><code>mask &amp; ~(1 &lt;&lt; i)</code></td><td>i = 1 → 1001₂</td></tr>
            <tr><td>Balik status i</td><td><code>mask ^ (1 &lt;&lt; i)</code></td><td>i = 0 → 1010₂</td></tr>
            <tr><td>Banyak anggota</td><td><code>__builtin_popcount(mask)</code></td><td>3</td></tr>
            <tr><td>Himpunan penuh</td><td><code>(1 &lt;&lt; n) − 1</code></td><td>n = 4 → 1111₂ = 15</td></tr>
        </table>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Pakai DP bitmask jika state-nya perlu mengingat <strong>"benda mana saja yang sudah dipakai"</strong> dan n kecil. Banyaknya state 2<sup>n</sup>: n = 20 memberi sekitar satu juta, masih aman. n = 30 sudah satu miliar, terlalu besar.</p>
    </div>
</section>

<section class="lesson-section" id="penugasan" data-toc="Contoh: Penugasan">
    <h2>Contoh Pertama: Membagi Tugas</h2>
    <div class="prose">
        <p>Ada 3 orang dan 3 tugas. <code>c[i][j]</code> = biaya orang i mengerjakan tugas j. Setiap orang mendapat tepat satu tugas. Berapa total biaya minimum?</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>c[i][j]</th><th>tugas 0</th><th>tugas 1</th><th>tugas 2</th></tr>
            <tr><td>orang 0</td><td>9</td><td>2</td><td>7</td></tr>
            <tr><td>orang 1</td><td>6</td><td>4</td><td>3</td></tr>
            <tr><td>orang 2</td><td>5</td><td>8</td><td>1</td></tr>
        </table>
    </div>
    <div class="prose">
        <p>Ide utamanya: bagikan tugas ke orang <strong>secara berurutan</strong> (orang 0 dulu, lalu orang 1, …). Jadi cukup diingat tugas mana yang sudah diambil. Banyaknya bit 1 di mask langsung memberi tahu siapa orang berikutnya.</p>
    </div>
    <div class="recurrence"><small>State dan transisi</small>dp[mask] = biaya minimum, tugas di mask sudah dibagikan ke popcount(mask) orang pertama
dp[0] = 0
i = popcount(mask)                       ← orang berikutnya
dp[mask | 1&lt;&lt;j] = min( …, dp[mask] + c[i][j] )   untuk tugas j yang belum diambil</div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>mask (tugas 2 1 0)</th><th>Orang yang sudah dapat</th><th>Perhitungan</th><th>dp</th></tr>
            <tr><td><code>000</code></td><td>–</td><td>base case</td><td><span class="new">0</span></td></tr>
            <tr><td><code>001</code>, <code>010</code>, <code>100</code></td><td>orang 0</td><td>9, 2, 7</td><td><span class="new">9 / 2 / 7</span></td></tr>
            <tr class="hl"><td><code>011</code></td><td>orang 0, 1</td><td>min(dp[001] + c[1][1], dp[010] + c[1][0]) = min(13, 8)</td><td><span class="new">8</span></td></tr>
            <tr><td><code>101</code></td><td>orang 0, 1</td><td>min(9 + 3, 7 + 6)</td><td><span class="new">12</span></td></tr>
            <tr><td><code>110</code></td><td>orang 0, 1</td><td>min(2 + 3, 7 + 4)</td><td><span class="new">5</span></td></tr>
            <tr class="ok"><td><code>111</code></td><td>semua</td><td>min(8 + c[2][2], 12 + c[2][1], 5 + c[2][0]) = min(9, 20, 10)</td><td><span class="new">9</span></td></tr>
        </table>
    </div>
    <div class="prose"><p>Jawabannya 9: orang 0 → tugas 1, orang 1 → tugas 0, orang 2 → tugas 2 (2 + 6 + 1).</p></div>
    @include('lessons.code', ['cpp' => $bmAssign, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Mencoba semua cara pembagian butuh n! langkah: untuk n = 20 itu 2,4 · 10<sup>18</sup>. DP bitmask hanya butuh 2<sup>n</sup> · n ≈ 2 · 10<sup>7</sup>. Kita tidak peduli <em>urutan</em> tugas diambil, hanya <em>himpunannya</em>.</p>
    </div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Cek n</b><span>n ≤ 20 dan "setiap benda dipakai sekali" atau "pilih himpunan bagian" → curigai bitmask.</span></div>
        <div class="step-card"><b>State</b><span><code>dp[mask]</code>, atau <code>dp[mask][v]</code> jika juga perlu tahu posisi terakhir.</span></div>
        <div class="step-card"><b>Transisi</b><span>Tambahkan satu benda yang bit-nya masih 0.</span></div>
        <div class="step-card"><b>Urutan</b><span>Loop mask naik: menambah bit selalu membuat angka lebih besar.</span></div>
        <div class="step-card"><b>Jawaban</b><span>Biasanya di <code>dp[(1 &lt;&lt; n) − 1]</code>.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'TSP di C++', 'desc' => 'State dua dimensi dp[mask][v], visualisasi, dan rekonstruksi rute.'])

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi TSP">
    <h2>Visualisasi: Rute Terpendek Melewati Semua Kota</h2>
    <div class="prose">
        <p>Untuk TSP, mask saja tidak cukup: kita juga perlu tahu kota tempat kita berada, karena jarak berikutnya bergantung padanya. Setiap baris tabel adalah mask (hanya yang memuat kota 0), setiap kolom adalah kota sekarang. Perhatikan bagaimana nilai mengalir dari baris dengan sedikit bit 1 ke baris dengan lebih banyak bit 1.</p>
    </div>
    <div data-viz="bitmask"></div>
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: TSP</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> n kota dan matriks jarak. Cetak panjang rute terpendek yang mulai dan berakhir di kota 0 serta mengunjungi semua kota tepat sekali, lalu rutenya.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'TSP dengan DP bitmask',
        'steps' => $bmSteps,
        'sample' => ['input' => "4\n0 2 9 4\n2 0 6 3\n9 6 0 5\n4 3 5 0\n", 'output' => "17\n0 3 2 1 0\n"],
        'js' => $bmJs,
        'py' => $bmPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Soal</th><th>Waktu</th><th>Batas n yang aman</th></tr>
        <tr><td>Penugasan <code>dp[mask]</code></td><td><code>O(2ⁿ · n)</code></td><td>sekitar 20–22</td></tr>
        <tr><td>TSP <code>dp[mask][v]</code></td><td><code>O(2ⁿ · n²)</code></td><td>sekitar 16–18</td></tr>
        <tr><td>Submask <code>dp[mask]</code> dari semua sub</td><td><code>O(3ⁿ)</code></td><td>sekitar 15–16</td></tr>
        <tr><td>SOS DP</td><td><code>O(2ⁿ · n)</code></td><td>sekitar 20–22</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Prioritas operator.</strong> Di C++, <code>mask &amp; 1 &lt;&lt; i == 0</code> dibaca sebagai <code>mask &amp; ((1 &lt;&lt; i) == 0)</code>. Selalu beri kurung: <code>((mask &gt;&gt; i) &amp; 1) == 0</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Geser lebih dari 31.</strong> <code>1 &lt;&lt; 40</code> pada <code>int</code> adalah perilaku tak terdefinisi. Untuk n &gt; 30 pakai <code>1LL &lt;&lt; i</code>, meskipun DP bitmask jarang sebesar itu.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'teknik bitmask lanjutan', 'desc' => 'Menjelajahi submask dan Sum over Subsets.'])

<section class="lesson-section" id="submask" data-toc="Menjelajahi Submask">
    <h2>Menjelajahi Submask dalam O(3ⁿ)</h2>
    <div class="prose">
        <p>Beberapa soal meminta membagi himpunan menjadi kelompok-kelompok. Transisinya: pilih satu kelompok <code>sub</code> dari sisa mask. Trik <code>sub = (sub − 1) &amp; mask</code> melompat langsung ke submask berikutnya tanpa memeriksa angka yang bukan submask.</p>
    </div>
    @include('lessons.code', ['cpp' => $bmSub, 'js' => null, 'py' => null])
    <div class="proof">
        <p><strong>Mengapa totalnya 3<sup>n</sup>?</strong> Setiap pasangan (mask, sub) dengan sub ⊆ mask menentukan, untuk setiap benda, satu dari tiga keadaan: tidak ada di mask, ada di mask tetapi tidak di sub, atau ada di keduanya. Jadi banyak pasangannya tepat 3<sup>n</sup>. Untuk n = 15, itu sekitar 1,4 · 10<sup>7</sup>.</p>
    </div>
</section>

<section class="lesson-section" id="sos" data-toc="Sum over Subsets">
    <h2>Sum over Subsets (SOS DP)</h2>
    <div class="prose">
        <p>Jika yang dibutuhkan adalah jumlah (atau max/min) dari nilai <em>semua</em> submask, ada cara yang lebih cepat: proses bit satu per satu. Setelah bit b diproses, <code>F[mask]</code> berisi jumlah semua submask yang hanya berbeda di bit 0..b.</p>
    </div>
    @include('lessons.code', ['cpp' => $bmSos, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Penugasan</h4><p><code>dp[mask]</code>, orang ke-popcount(mask).</p></div>
        <div class="pattern"><h4>Jalur Hamilton / TSP</h4><p><code>dp[mask][v]</code>, posisi terakhir v.</p></div>
        <div class="pattern"><h4>Pembagian kelompok</h4><p>Jelajahi submask, O(3ⁿ).</p></div>
        <div class="pattern"><h4>Profil grid</h4><p>Mengisi papan dengan domino: mask = keadaan satu kolom.</p></div>
        <div class="pattern"><h4>SOS</h4><p>Jumlah atas semua submask, O(2ⁿ · n).</p></div>
        <div class="pattern"><h4>Meet in the middle</h4><p>n ≈ 40: bagi dua, masing-masing 2<sup>20</sup>, lalu gabungkan.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="13 = 1101₂. Bit yang menyala: 0, 2, dan 3.">
        <p class="quiz-q">mask = 13. Himpunan apa yang diwakilinya?</p>
        <div class="quiz-options">
            <button class="quiz-option">{1, 3}</button>
            <button class="quiz-option">{0, 2, 3}</button>
            <button class="quiz-option">{1, 2, 3}</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Banyak bit 1 di mask = banyak tugas yang sudah dibagikan = banyak orang yang sudah dapat. Jadi orang berikutnya adalah popcount(mask).">
        <p class="quiz-q">Pada DP penugasan, bagaimana kita tahu orang mana yang mendapat tugas berikutnya?</p>
        <div class="quiz-options">
            <button class="quiz-option">popcount(mask)</button>
            <button class="quiz-option">Disimpan sebagai dimensi kedua</button>
            <button class="quiz-option">Bit tertinggi di mask</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="2³⁰ · 30 ≈ 3 · 10¹⁰ state: terlalu besar untuk waktu dan memori. DP bitmask cocok untuk n sekitar 20.">
        <p class="quiz-q">n = 30. Apakah DP bitmask <code>dp[mask][v]</code> masih masuk akal?</p>
        <div class="quiz-options">
            <button class="quiz-option">Ya, 2³⁰ itu kecil</button>
            <button class="quiz-option">Ya, jika memakai long long</button>
            <button class="quiz-option">Tidak, state-nya terlalu banyak</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
