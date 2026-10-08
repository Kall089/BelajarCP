@php
    $js = <<<'CODE'
function lcs(a, b) {
  const n = a.length, m = b.length;
  // dp[i][j] = LCS dari a[0..i) dan b[0..j)
  const dp = Array.from({ length: n + 1 }, () => new Array(m + 1).fill(0));

  for (let i = 1; i <= n; i++) {
    for (let j = 1; j <= m; j++) {
      if (a[i - 1] === b[j - 1]) {
        dp[i][j] = dp[i - 1][j - 1] + 1;              // diagonal
      } else {
        dp[i][j] = Math.max(dp[i - 1][j], dp[i][j - 1]); // atas / kiri
      }
    }
  }

  // Rekonstruksi salah satu LCS (telusur balik)
  let i = n, j = m, hasil = "";
  while (i > 0 && j > 0) {
    if (a[i - 1] === b[j - 1]) { hasil = a[i - 1] + hasil; i--; j--; }
    else if (dp[i - 1][j] >= dp[i][j - 1]) i--;
    else j--;
  }
  return [dp[n][m], hasil];
}

console.log(lcs("ACGTAG", "CATAGG")); // [4, "ATAG"]
CODE;
    $py = <<<'CODE'
def lcs(a, b):
    n, m = len(a), len(b)
    dp = [[0] * (m + 1) for _ in range(n + 1)]

    for i in range(1, n + 1):
        for j in range(1, m + 1):
            if a[i - 1] == b[j - 1]:
                dp[i][j] = dp[i - 1][j - 1] + 1               # diagonal
            else:
                dp[i][j] = max(dp[i - 1][j], dp[i][j - 1])    # atas / kiri

    # Rekonstruksi salah satu LCS
    i, j, hasil = n, m, []
    while i > 0 and j > 0:
        if a[i - 1] == b[j - 1]:
            hasil.append(a[i - 1]); i -= 1; j -= 1
        elif dp[i - 1][j] >= dp[i][j - 1]:
            i -= 1
        else:
            j -= 1
    return dp[n][m], "".join(reversed(hasil))

print(lcs("ACGTAG", "CATAGG"))  # (4, 'ATAG')
CODE;
@endphp

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2><span class="sec-icon">💡</span> Intuisi: Mencari Kesamaan</h2>
    <div class="prose">
        <p><strong>Subsequence</strong> adalah huruf-huruf yang diambil dari sebuah string <em>dengan urutan tetap</em>, tapi tidak harus berdekatan. Dari <code>PROGRAM</code>, kita bisa mengambil <code>PGM</code> atau <code>ROAM</code>, tapi tidak bisa <code>MAP</code> karena urutannya terbalik.</p>
        <p><strong>Longest Common Subsequence</strong> (LCS) adalah subsequence terpanjang yang dimiliki <em>dua</em> string sekaligus. Fitur ini dipakai di mana-mana: <code>git diff</code>, pendeteksi plagiarisme, sampai analisis DNA.</p>
        <p>Kuncinya ada di <strong>huruf terakhir</strong> kedua string:</p>
        <div class="term-grid">
            <div class="term"><b>Huruf terakhir sama</b><span>Pasti dipakai di LCS. Buang dari keduanya dan tambah 1: <code>dp[i−1][j−1] + 1</code></span></div>
            <div class="term"><b>Huruf terakhir beda</b><span>Salah satunya pasti tidak dipakai. Coba buang masing-masing dan ambil yang terbaik: <code>max(dp[i−1][j], dp[i][j−1])</code></span></div>
        </div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2><span class="sec-icon">🎬</span> Visualisasi LCS</h2>
    <div class="prose">
        <p>Baris adalah huruf string A dan kolom adalah huruf string B. Saat huruf sama, panah <span style="color:var(--cyan)">diagonal</span> muncul (+1). Saat beda, dua panah <span style="color:var(--pink)">pink</span> membandingkan atas dan kiri. Di akhir, jejak hijau merekonstruksi LCS-nya. Coba ganti dengan kata-katamu sendiri!</p>
    </div>
    <div data-viz="lcs"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2><span class="sec-icon">🧭</span> Cara Pengerjaan: DP Dua String</h2>
    <div class="steps">
        <div class="step-card"><b>State</b><span><code>dp[i][j]</code> = jawaban untuk <strong>prefiks</strong> A sepanjang i dan prefiks B sepanjang j.</span></div>
        <div class="step-card"><b>Base case</b><span>Baris 0 dan kolom 0 adalah string kosong. Untuk LCS nilainya 0, sedangkan untuk edit distance <code>dp[i][0] = i</code>.</span></div>
        <div class="step-card"><b>Bandingkan huruf terakhir</b><span><code>A[i−1]</code> vs <code>B[j−1]</code> (ingat indeks string mulai dari 0!).</span></div>
        <div class="step-card"><b>Isi baris demi baris</b><span>Sel atas, kiri, dan diagonal selalu sudah terisi.</span></div>
        <div class="step-card"><b>Jawaban & rekonstruksi</b><span><code>dp[n][m]</code>. Untuk mendapatkan string-nya, telusur balik dari pojok kanan bawah.</span></div>
    </div>
</section>

<section class="lesson-section" id="kode" data-toc="Implementasi">
    <h2><span class="sec-icon">💻</span> Implementasi</h2>
    @include('lessons.code', ['js' => $js, 'py' => $py])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas">
    <h2><span class="sec-icon">⏱️</span> Kompleksitas & Keluarga DP String</h2>
    <table class="cx-table">
        <tr><th>Aspek</th><th>Nilai</th></tr>
        <tr><td>Waktu</td><td><code>O(n × m)</code></td></tr>
        <tr><td>Memori</td><td><code>O(n × m)</code>, atau <code>O(m)</code> jika hanya butuh panjangnya</td></tr>
    </table>
    <div class="callout tip">
        <span class="callout-icon">🧬</span>
        <p><strong>Edit distance</strong> (jarak Levenshtein) memakai tabel yang sama, hanya rumusnya berbeda: jika huruf sama ambil diagonal, jika beda <code>1 + min(diagonal, atas, kiri)</code> untuk operasi ganti, hapus, dan sisip.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2><span class="sec-icon">✅</span> Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="'ACE' muncul berurutan di ABCDE (A, C, E). Panjangnya 3.">
        <p class="quiz-q">Berapa panjang LCS dari "ABCDE" dan "ACE"?</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">5</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Baris/kolom 0 mewakili string kosong, sehingga indeks dp bergeser satu dari indeks string. Huruf untuk dp[i][j] adalah a[i−1] dan b[j−1].">
        <p class="quiz-q">Kenapa yang dibandingkan a[i−1] dan b[j−1], bukan a[i] dan b[j]?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena baris/kolom 0 dipakai untuk string kosong</button>
            <button class="quiz-option">Agar lebih cepat</button>
            <button class="quiz-option">Karena JavaScript mulai dari 1</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
