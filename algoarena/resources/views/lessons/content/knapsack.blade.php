@php
    $js = <<<'CODE'
// items: [[berat, nilai], ...], W: kapasitas
function knapsack(items, W) {
  const n = items.length;
  // dp[i][c] = nilai terbaik memakai barang 1..i dengan kapasitas c
  const dp = Array.from({ length: n + 1 }, () => new Array(W + 1).fill(0));

  for (let i = 1; i <= n; i++) {
    const [w, v] = items[i - 1];
    for (let c = 0; c <= W; c++) {
      dp[i][c] = dp[i - 1][c];                        // tidak diambil
      if (w <= c) {
        dp[i][c] = Math.max(dp[i][c], dp[i - 1][c - w] + v); // diambil
      }
    }
  }
  return dp[n][W];
}

// Versi hemat memori O(W): loop kapasitas MUNDUR
function knapsack1D(items, W) {
  const dp = new Array(W + 1).fill(0);
  for (const [w, v] of items) {
    for (let c = W; c >= w; c--) dp[c] = Math.max(dp[c], dp[c - w] + v);
  }
  return dp[W];
}

console.log(knapsack([[1, 1], [3, 4], [4, 5], [5, 7]], 7)); // 9
CODE;
    $py = <<<'CODE'
def knapsack(items, W):
    n = len(items)
    # dp[i][c] = nilai terbaik memakai barang 1..i dengan kapasitas c
    dp = [[0] * (W + 1) for _ in range(n + 1)]
    for i in range(1, n + 1):
        w, v = items[i - 1]
        for c in range(W + 1):
            dp[i][c] = dp[i - 1][c]                   # tidak diambil
            if w <= c:
                dp[i][c] = max(dp[i][c], dp[i - 1][c - w] + v)  # diambil
    return dp[n][W]

def knapsack_1d(items, W):
    dp = [0] * (W + 1)
    for w, v in items:
        for c in range(W, w - 1, -1):              # MUNDUR!
            dp[c] = max(dp[c], dp[c - w] + v)
    return dp[W]

print(knapsack([(1, 1), (3, 4), (4, 5), (5, 7)], 7))  # 9
CODE;
@endphp

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2><span class="sec-icon">💡</span> Intuisi: Ambil atau Tinggalkan?</h2>
    <div class="prose">
        <p>Kamu akan berkemah dan ransel hanya muat <strong>W kg</strong>. Ada beberapa barang, masing-masing punya berat dan nilai kegunaan. Barang mana yang dibawa agar total nilainya maksimum?</p>
        <p>Mencoba semua kombinasi butuh 2<sup>n</sup> kemungkinan. Untuk 100 barang itu lebih dari jumlah atom di alam semesta! Dengan DP, kita memutuskan barang <strong>satu per satu</strong>. Untuk setiap barang hanya ada dua pilihan:</p>
        <div class="term-grid">
            <div class="term"><b>❌ Tidak diambil</b><span>Nilainya sama dengan solusi terbaik tanpa barang ini, dengan kapasitas yang sama: <code>dp[i−1][c]</code></span></div>
            <div class="term"><b>✅ Diambil</b><span>Kapasitas berkurang w, nilai bertambah v: <code>dp[i−1][c−w] + v</code></span></div>
        </div>
        <p style="text-align:center"><code>dp[i][c] = max(dp[i−1][c], dp[i−1][c−w] + v)</code></p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>State-nya butuh <strong>dua informasi</strong>: sampai barang ke berapa (i), dan sisa kapasitas berapa (c). Ketika satu angka tidak cukup menggambarkan keadaan, tambahkan dimensi!</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2><span class="sec-icon">🎬</span> Visualisasi Knapsack</h2>
    <div class="prose">
        <p>Setiap sel melihat ke <span style="color:var(--cyan)">atas</span> (tidak ambil) dan ke <span style="color:var(--pink)">atas-kiri sejauh w</span> (ambil). Di akhir, perhatikan <strong>telusur balik</strong>: jika nilai sel berbeda dari sel di atasnya, berarti barang itu diambil.</p>
    </div>
    <div data-viz="knapsack"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri knapsack</b><span>Ada "kapasitas/anggaran" terbatas, setiap item dipilih atau tidak, dan kita memaksimalkan atau meminimalkan sesuatu.</span></div>
        <div class="step-card"><b>Tabel (n+1) × (W+1)</b><span>Baris 0 (tanpa barang) bernilai 0 semua.</span></div>
        <div class="step-card"><b>Isi baris demi baris</b><span>Untuk setiap barang i dan kapasitas c, pilih max antara tidak ambil dan ambil (jika muat).</span></div>
        <div class="step-card"><b>Jawaban</b><span><code>dp[n][W]</code>. Butuh daftar barangnya? Telusur balik dari <code>(n, W)</code>.</span></div>
        <div class="step-card"><b>Hemat memori</b><span>Pakai array 1D dan loop c dari W <strong>turun</strong> ke w. Loop naik akan membuat barang terpakai berkali-kali.</span></div>
    </div>
</section>

<section class="lesson-section" id="kode" data-toc="Implementasi">
    <h2><span class="sec-icon">💻</span> Implementasi</h2>
    @include('lessons.code', ['js' => $js, 'py' => $py])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Variasi</h2>
    <table class="cx-table">
        <tr><th>Versi</th><th>Waktu</th><th>Memori</th></tr>
        <tr><td>Tabel 2D</td><td><code>O(n × W)</code></td><td><code>O(n × W)</code></td></tr>
        <tr><td>Array 1D (loop mundur)</td><td><code>O(n × W)</code></td><td><code>O(W)</code></td></tr>
    </table>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p><strong>Keluarga knapsack:</strong> <em>subset sum</em> (bisakah membentuk jumlah tepat S?), <em>partisi</em> (bagi dua sama rata), dan <em>unbounded knapsack</em> (barang tak terbatas, loop maju). Semuanya memakai tabel yang sama!</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p>Kompleksitas bergantung pada <strong>nilai W</strong>, bukan jumlah digitnya. Jika W = 10<sup>9</sup>, tabel ini terlalu besar dan butuh trik lain.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Dengan loop naik, dp[c−w] yang dibaca mungkin sudah memakai barang yang sama di iterasi ini, sehingga barang bisa terambil berkali-kali (unbounded).">
        <p class="quiz-q">Pada knapsack 1D, apa akibatnya jika loop kapasitas dibuat NAIK (0 → W)?</p>
        <div class="quiz-options">
            <button class="quiz-option">Hasilnya tetap sama</button>
            <button class="quiz-option">Barang yang sama bisa diambil berkali-kali</button>
            <button class="quiz-option">Program error</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Barang 3 kg tidak muat di kapasitas 2, jadi pilihan 'diambil' tidak tersedia. Nilai disalin dari atas: dp[i][2] = dp[i−1][2].">
        <p class="quiz-q">Barang ke-i berat 3. Berapa dp[i][2]?</p>
        <div class="quiz-options">
            <button class="quiz-option">dp[i−1][2] + v</button>
            <button class="quiz-option">0</button>
            <button class="quiz-option">dp[i−1][2]</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
