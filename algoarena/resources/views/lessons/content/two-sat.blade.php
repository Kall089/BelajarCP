@php
    $tsSteps = [
        ['Simpul literal dan klausa', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n, m;
vector<vector<int>> g, rg;          // graph implikasi dan kebalikannya
vector<int> urutan, komp;
vector<bool> vis;

int lit(int x) {                    // +i → "x_i benar", -i → "x_i salah"
    int i = abs(x) - 1;
    return x > 0 ? 2 * i : 2 * i + 1;
}

void klausa(int a, int b) {         // (a ∨ b) = (¬a → b) dan (¬b → a)
    g[a ^ 1].push_back(b);  rg[b].push_back(a ^ 1);
    g[b ^ 1].push_back(a);  rg[a].push_back(b ^ 1);
}

CPP, <<<'TXT'
<p><strong>Soal contoh:</strong> ada n peubah boolean x<sub>1</sub> … x<sub>n</sub> dan m klausa. Setiap klausa berisi dua literal, ditulis sebagai bilangan bertanda: <code>3</code> berarti x<sub>3</sub>, <code>-3</code> berarti ¬x<sub>3</sub>. Cari penugasan yang membuat semua klausa benar, atau cetak <code>TIDAK MUNGKIN</code>.</p>
<p>Peubah ke-i memakai dua simpul: <code>2i</code> untuk "x<sub>i</sub> benar" dan <code>2i+1</code> untuk "x<sub>i</sub> salah". Dengan penomoran ini, lawan sebuah literal cukup <code>v ^ 1</code>.</p>
<p>Fungsi <code>klausa</code> adalah satu-satunya tempat logika diterjemahkan, dan ia <strong>selalu menambah dua sisi</strong>. Sisi kebalikannya langsung disimpan di <code>rg</code> untuk Kosaraju.</p>
TXT],
        ['SCC dengan Kosaraju', <<<'CPP'
void dfs1(int u) {
    vis[u] = true;
    for (int v : g[u])
        if (!vis[v]) dfs1(v);
    urutan.push_back(u);            // dicatat saat selesai
}

void dfs2(int u, int c) {
    komp[u] = c;
    for (int v : rg[u])
        if (komp[v] == -1) dfs2(v, c);
}

CPP, <<<'TXT'
<p>Kosaraju standar dari materi SCC. Hal penting yang kita pakai: komponen diberi nomor <strong>sesuai urutan topologis</strong> graph kondensasi. Komponen bernomor kecil ada di "hulu", bernomor besar di "hilir".</p>
TXT],
        ['Baca input dan bangun graph', <<<'CPP'
int main() {
    cin >> n >> m;
    g.assign(2 * n, {});
    rg.assign(2 * n, {});
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        klausa(lit(a), lit(b));
    }

CPP, <<<'TXT'
<p>Ukuran graph <strong>2n</strong>, bukan n. Klausa satu literal "x<sub>a</sub> harus benar" cukup ditulis sebagai <code>klausa(a, a)</code> yang menghasilkan sisi ¬a → a.</p>
TXT],
        ['Periksa kontradiksi, baca jawaban', <<<'CPP'
    vis.assign(2 * n, false);
    for (int v = 0; v < 2 * n; v++)
        if (!vis[v]) dfs1(v);
    reverse(urutan.begin(), urutan.end());
    komp.assign(2 * n, -1);
    int c = 0;
    for (int v : urutan)
        if (komp[v] == -1) dfs2(v, c++);

    vector<int> x(n);
    for (int i = 0; i < n; i++) {
        if (komp[2 * i] == komp[2 * i + 1]) {     // x dan ¬x saling memaksa
            cout << "TIDAK MUNGKIN\n";
            return 0;
        }
        x[i] = komp[2 * i] > komp[2 * i + 1];      // pilih yang lebih hilir
    }
    for (int i = 0; i < n; i++) cout << x[i] << (i + 1 < n ? ' ' : '\n');
    return 0;
}
CPP, <<<'TXT'
<p>Jika x<sub>i</sub> dan ¬x<sub>i</sub> berada di SCC yang sama, ada rantai implikasi x → … → ¬x dan ¬x → … → x. Tidak ada nilai yang konsisten, jadi rumusnya mustahil.</p>
<p>Jika tidak, ambil literal yang komponennya <strong>lebih hilir</strong> (nomor lebih besar pada Kosaraju). Untuk contoh di bawah: komponen {x<sub>1</sub>, x<sub>3</sub>, ¬x<sub>2</sub>} hilir, sehingga x<sub>1</sub> = 1, x<sub>2</sub> = 0, x<sub>3</sub> = 1.</p>
<p>Total O(n + m). Jika memakai <strong>Tarjan</strong>, nomor komponennya terbalik, jadi tanda pembandingnya menjadi <code>&lt;</code>.</p>
TXT],
    ];

    $tsPy = <<<'PY'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n, m = int(data[0]), int(data[1])
    N = 2 * n
    g = [[] for _ in range(N)]
    rg = [[] for _ in range(N)]
    lit = lambda x: 2 * (x - 1) if x > 0 else 2 * (-x - 1) + 1
    for i in range(m):
        a, b = lit(int(data[2 + 2 * i])), lit(int(data[3 + 2 * i]))
        g[a ^ 1].append(b); rg[b].append(a ^ 1)
        g[b ^ 1].append(a); rg[a].append(b ^ 1)
    # Kosaraju iteratif
    vis = [False] * N
    urutan = []
    for s in range(N):
        if vis[s]:
            continue
        vis[s] = True
        st = [(s, 0)]
        while st:
            u, k = st[-1]
            if k < len(g[u]):
                st[-1] = (u, k + 1)
                v = g[u][k]
                if not vis[v]:
                    vis[v] = True
                    st.append((v, 0))
            else:
                st.pop()
                urutan.append(u)
    komp = [-1] * N
    c = 0
    for s in reversed(urutan):
        if komp[s] != -1:
            continue
        komp[s] = c
        st = [s]
        while st:
            u = st.pop()
            for v in rg[u]:
                if komp[v] == -1:
                    komp[v] = c
                    st.append(v)
        c += 1
    x = []
    for i in range(n):
        if komp[2 * i] == komp[2 * i + 1]:
            print("TIDAK MUNGKIN")
            return
        x.append(1 if komp[2 * i] > komp[2 * i + 1] else 0)
    print(*x)

main()
PY;

    $tsAtMostOne = <<<'CPP'
// "Paling banyak satu dari L[0..k-1] yang benar" dengan O(k) klausa, bukan O(k²).
// p[i] = "salah satu dari L[0..i] sudah benar" (peubah baru).
for (int i = 0; i < k; i++) {
    klausa(L[i] ^ 1, P[i]);                         // L[i] → p[i]
    if (i > 0) {
        klausa(P[i - 1] ^ 1, P[i]);                 // p[i-1] → p[i]
        klausa(P[i - 1] ^ 1, L[i] ^ 1);             // p[i-1] → ¬L[i]
    }
}
CPP;

    $tsBrute = <<<'CPP'
// Pembanding lambat untuk stress test: coba semua 2^n penugasan (n ≤ 20).
for (int mask = 0; mask < (1 << n); mask++) {
    bool ok = true;
    for (auto& c : klausaList) {                    // c = (a, b) bertanda
        bool va = c.first > 0 ? (mask >> (c.first - 1) & 1) : !(mask >> (-c.first - 1) & 1);
        bool vb = c.second > 0 ? (mask >> (c.second - 1) & 1) : !(mask >> (-c.second - 1) & 1);
        if (!va && !vb) { ok = false; break; }     // klausa gagal hanya jika keduanya salah
    }
    if (ok) { /* mask adalah salah satu jawaban */ }
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali soal <strong>2-SAT</strong>: banyak syarat "salah satu dari dua" atas pilihan benar/salah.</li>
        <li>Menerjemahkan setiap klausa (a ∨ b) menjadi <strong>dua implikasi</strong> ¬a → b dan ¬b → a.</li>
        <li>Memutuskan ada-tidaknya jawaban dengan <strong>SCC</strong> dan membangun penugasannya dari urutan topologis.</li>
        <li>Memodelkan syarat "jika–maka", "tidak boleh bersama", "tepat satu", dan "paling banyak satu dari k".</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'dari logika ke graph', 'desc' => 'Klausa dua literal, implikasi, dan graph implikasi.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Menyusun Menu">
    <h2>Intuisi: Menyusun Menu dengan Syarat</h2>
    <div class="prose">
        <p>Kamu menyusun menu acara. Setiap hidangan boleh ada atau tidak, tetapi ada syarat dari tamu: "harus ada nasi <em>atau</em> roti", "jangan ada nasi <em>dan</em> kopi bersamaan", "kalau ada sate maka harus ada lontong". Adakah menu yang memenuhi semua syarat?</p>
        <p>Mencoba semua kemungkinan butuh 2<sup>n</sup> percobaan. Namun jika <strong>setiap syarat hanya melibatkan dua hidangan</strong>, ada cara O(n + m): baca setiap syarat sebagai panah sebab-akibat. "Harus ada nasi atau roti" berarti <em>kalau tidak ada nasi, maka harus ada roti</em>, dan juga <em>kalau tidak ada roti, maka harus ada nasi</em>. Kumpulan panah itu membentuk graph berarah, dan pertanyaan logika berubah menjadi pertanyaan SCC.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Literal</b><span>Sebuah peubah atau negasinya: x<sub>3</sub> atau ¬x<sub>3</sub>.</span></div>
        <div class="term"><b>Klausa</b><span>"Atau" dari dua literal: (a ∨ b). Gagal hanya jika keduanya salah.</span></div>
        <div class="term"><b>Graph implikasi</b><span>2n simpul (setiap literal), dua sisi berarah per klausa.</span></div>
        <div class="term"><b>Penugasan</b><span>Nilai benar/salah untuk setiap peubah yang membuat semua klausa benar.</span></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Batas <strong>dua</strong> literal per klausa itu penting. Dengan tiga literal (3-SAT), soalnya NP-lengkap dan tidak dikenal algoritma polinomialnya.</p>
    </div>
</section>

<section class="lesson-section" id="implikasi" data-toc="Klausa Menjadi Implikasi">
    <h2>Satu Klausa, Dua Implikasi</h2>
    <div class="prose">
        <p>Klausa (a ∨ b) salah hanya ketika a salah dan b salah. Jadi begitu kita tahu a salah, b <em>wajib</em> benar: ¬a → b. Dengan alasan yang sama, ¬b → a. Kedua sisi ini adalah <strong>kontrapositif</strong> satu sama lain, sehingga graph implikasi selalu simetris: jika ada sisi u → v, pasti ada sisi ¬v → ¬u.</p>
    </div>
    <table class="cx-table">
        <tr><th>Klausa</th><th>Sisi pertama</th><th>Sisi kedua</th></tr>
        <tr><td>(x<sub>1</sub> ∨ x<sub>2</sub>)</td><td>¬x<sub>1</sub> → x<sub>2</sub></td><td>¬x<sub>2</sub> → x<sub>1</sub></td></tr>
        <tr><td>(¬x<sub>1</sub> ∨ x<sub>3</sub>)</td><td>x<sub>1</sub> → x<sub>3</sub></td><td>¬x<sub>3</sub> → ¬x<sub>1</sub></td></tr>
        <tr><td>(¬x<sub>2</sub> ∨ ¬x<sub>3</sub>)</td><td>x<sub>2</sub> → ¬x<sub>3</sub></td><td>x<sub>3</sub> → ¬x<sub>2</sub></td></tr>
        <tr><td>(x<sub>1</sub> ∨ x<sub>3</sub>)</td><td>¬x<sub>1</sub> → x<sub>3</sub></td><td>¬x<sub>3</sub> → x<sub>1</sub></td></tr>
    </table>
    <div class="prose">
        <p>Jika dari x kita bisa berjalan mengikuti panah sampai ¬x, maka "x benar" memaksa "x salah", jadi x harus salah. Jika sekaligus dari ¬x bisa sampai ke x, keduanya mustahil: tidak ada jawaban. "Saling bisa mencapai" adalah definisi <strong>satu SCC</strong>.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Graph Implikasi dan SCC</h2>
    <div class="prose"><p>Tulis klausa sebagai pasangan bilangan bertanda, misalnya <code>1 2, -1 3</code>. Ikuti pembangunan sisi, dua tahap Kosaraju, lalu pembacaan jawaban. Coba juga set "mustahil" untuk melihat x dan ¬x jatuh di komponen yang sama.</p></div>
    <div data-viz="twosat"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => '2-SAT di C++', 'desc' => 'Program lengkap O(n + m) dan pola klausa yang sering muncul.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Penyelesai 2-SAT dengan Kosaraju',
        'steps' => $tsSteps,
        'sample' => ['input' => "3 4\n1 2\n-1 3\n-2 -3\n1 3\n", 'output' => "1 0 1\n"],
        'py' => $tsPy,
    ])
    <table class="trace-table">
        <tr><th>klausa</th><th>x<sub>1</sub> = 1, x<sub>2</sub> = 0, x<sub>3</sub> = 1</th><th>hasil</th></tr>
        <tr><td>(x<sub>1</sub> ∨ x<sub>2</sub>)</td><td>1 ∨ 0</td><td>benar</td></tr>
        <tr><td>(¬x<sub>1</sub> ∨ x<sub>3</sub>)</td><td>0 ∨ 1</td><td>benar</td></tr>
        <tr><td>(¬x<sub>2</sub> ∨ ¬x<sub>3</sub>)</td><td>1 ∨ 0</td><td>benar</td></tr>
        <tr class="ok"><td>(x<sub>1</sub> ∨ x<sub>3</sub>)</td><td>1 ∨ 1</td><td><b>benar</b></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Rekursi dalam.</strong> DFS rekursif di atas aman untuk contoh kecil, tetapi graph 2-SAT bisa punya 2·10<sup>5</sup> simpul dalam satu rantai. Di judge dengan stack kecil (misalnya Windows, 1 MB) program bisa crash. Untuk soal besar, tulis DFS secara iteratif seperti versi Python.</p>
    </div>
</section>

<section class="lesson-section" id="pola" data-toc="Pola Klausa">
    <h2>Pola Klausa yang Sering Muncul</h2>
    <div class="prose"><p>Bagian tersulit 2-SAT hampir selalu <strong>pemodelan</strong>, bukan algoritmanya. Kode penyelesainya sama untuk semua soal; yang berubah hanya klausa yang dimasukkan. Enam pola berikut menutupi hampir semua kebutuhan.</p></div>
    <table class="cx-table">
        <tr><th>Kalimat soal</th><th>Klausa</th><th>Catatan</th></tr>
        <tr><td>a atau b (minimal satu)</td><td>(a ∨ b)</td><td>bentuk dasar</td></tr>
        <tr><td>jika a maka b</td><td>(¬a ∨ b)</td><td>pola paling sering</td></tr>
        <tr><td>a dan b tidak boleh bersamaan</td><td>(¬a ∨ ¬b)</td><td>"paling banyak satu dari dua"</td></tr>
        <tr><td>a harus benar</td><td>(a ∨ a)</td><td>menghasilkan sisi ¬a → a</td></tr>
        <tr><td>tepat satu dari a, b</td><td>(a ∨ b) dan (¬a ∨ ¬b)</td><td>dua klausa (a XOR b)</td></tr>
        <tr><td>a dan b bernilai sama</td><td>(¬a ∨ b) dan (a ∨ ¬b)</td><td>a ⇔ b</td></tr>
    </table>
    <div class="steps">
        <div class="step-card"><b>1. Tentukan peubahnya</b><p>Pilihan biner apa yang dibuat? "Lampu i menyala", "tim i main di kandang", "pion i diletakkan di posisi kiri".</p></div>
        <div class="step-card"><b>2. Ubah setiap syarat</b><p>Gunakan tabel di atas. Setiap syarat menjadi satu atau dua klausa dua-literal.</p></div>
        <div class="step-card"><b>3. Jalankan penyelesai</b><p>SCC, cek x vs ¬x, baca jawaban dari nomor komponen.</p></div>
        <div class="step-card"><b>4. Periksa hasilnya</b><p>Saat latihan, uji jawabanmu dengan pemeriksa klausa sederhana atau brute force 2<sup>n</sup>.</p></div>
    </div>
    @include('lessons.code', ['cpp' => $tsBrute, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <div class="prose"><p>Graph punya 2n simpul dan 2m sisi; SCC dan pembacaan jawaban linear. Total <strong>O(n + m)</strong> waktu dan memori.</p></div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Hanya satu sisi per klausa.</strong> Klausa (a ∨ b) memberi ¬a → b <em>dan</em> ¬b → a. Lupa salah satunya membuat graph tidak simetris dan jawabannya salah tanpa pesan error.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Arah pembanding komponen.</strong> Kosaraju menomori komponen mengikuti urutan topologis (pakai <code>&gt;</code>); Tarjan menomori terbalik (pakai <code>&lt;</code>). Salah tanda menghasilkan penugasan yang melanggar klausa.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Array berukuran n.</strong> Setiap peubah butuh dua simpul. Indeks 2i+1 keluar batas jika array hanya n.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'bukti dan pemodelan lanjut', 'desc' => 'Mengapa aturan "ambil yang lebih hilir" selalu benar, dan trik untuk syarat yang lebih besar.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Benar?">
    <h2>Mengapa Aturannya Benar?</h2>
    <div class="proof">
        <p><strong>Jika x dan ¬x satu SCC, tidak ada jawaban.</strong> Ada jalur x ⇝ ¬x. Setiap sisi adalah implikasi yang wajib dipatuhi, jadi x benar memaksa ¬x benar (kontradiksi). Ada juga jalur ¬x ⇝ x, jadi x salah pun memaksa x benar. Tidak ada nilai yang tersisa.</p>
        <p><strong>Jika tidak ada pasangan seperti itu, penugasan "ambil literal yang lebih hilir" sah.</strong> Misalkan ada sisi u → v yang dilanggar: u benar dan v salah. Karena v salah, ¬v benar, sehingga komp(¬v) lebih hilir dari komp(v). Karena u benar, komp(u) lebih hilir dari komp(¬u). Sisi u → v berarti komp(u) tidak lebih hilir dari komp(v), dan kembarannya ¬v → ¬u berarti komp(¬v) tidak lebih hilir dari komp(¬u). Rangkai: komp(¬u) &lt; komp(u) ≤ komp(v) &lt; komp(¬v) ≤ komp(¬u) dalam urutan topologis. Ini mustahil, jadi tidak ada sisi yang dilanggar, dan setiap klausa (yang terdiri dari dua sisi) terpenuhi.</p>
    </div>
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Intuisinya: memilih literal hilir berarti memilih pernyataan yang "tidak memaksa apa-apa lagi" ke arah yang belum diputuskan. Literal hulu justru yang berisiko memicu rantai kesimpulan.</p>
    </div>
</section>

<section class="lesson-section" id="lanjut" data-toc="Pemodelan Lanjut">
    <h2>Pemodelan Lanjut</h2>
    <div class="prose">
        <p><strong>Paling banyak satu dari k.</strong> Menulis (¬L<sub>i</sub> ∨ ¬L<sub>j</sub>) untuk semua pasangan butuh O(k²) klausa. Dengan peubah bantu prefiks p<sub>i</sub> = "ada yang benar di L<sub>0..i</sub>", cukup O(k) klausa:</p>
    </div>
    @include('lessons.code', ['cpp' => $tsAtMostOne, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>2-SAT + binary search</h4><p>"Maksimalkan jarak minimum antar titik terpilih": tebak D, lalu setiap pasangan pilihan yang berjarak &lt; D menjadi klausa "tidak boleh bersama". Cek dengan 2-SAT.</p></div>
        <div class="pattern"><h4>Dua pilihan per objek</h4><p>Setiap objek punya posisi A atau B (bendera, kunci, tim). Peubah x<sub>i</sub> = "objek i memakai A"; bentrokan antarposisi menjadi klausa.</p></div>
        <div class="pattern"><h4>Saklar dan lampu</h4><p>Lampu dikendalikan dua saklar dan harus menyala: saklar<sub>1</sub> XOR saklar<sub>2</sub> = nilai yang diminta, ditulis sebagai dua klausa.</p></div>
        <div class="pattern"><h4>Graph implikasi besar</h4><p>Jika satu literal berimplikasi ke sebuah rentang literal, pakai segment tree sebagai simpul perantara agar sisi tetap O(n log n).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Jika ¬x₂ benar (x₂ salah), klausa memaksa x₅ benar. Kontrapositifnya ¬x₅ → x₂ adalah sisi kedua.">
        <p class="quiz-q">Klausa (x<sub>2</sub> ∨ x<sub>5</sub>) menghasilkan sisi ¬x<sub>2</sub> → x<sub>5</sub> dan…</p>
        <div class="quiz-options">
            <button class="quiz-option">x<sub>5</sub> → x<sub>2</sub></button>
            <button class="quiz-option">x<sub>2</sub> → ¬x<sub>5</sub></button>
            <button class="quiz-option">¬x<sub>5</sub> → x<sub>2</sub></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="'Jika a maka b' gagal hanya saat a benar dan b salah, jadi setara dengan (¬a ∨ b).">
        <p class="quiz-q">Syarat "jika tim A ikut, tim B juga harus ikut" menjadi klausa…</p>
        <div class="quiz-options">
            <button class="quiz-option">(a ∨ b)</button>
            <button class="quiz-option">(¬a ∨ b)</button>
            <button class="quiz-option">(¬a ∨ ¬b)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Kosaraju menomori komponen sesuai urutan topologis, jadi nomor lebih besar = lebih hilir. Literal yang lebih hilir dipilih benar.">
        <p class="quiz-q">Dengan Kosaraju, komp[x] = 4 dan komp[¬x] = 1. Nilai x adalah…</p>
        <div class="quiz-options">
            <button class="quiz-option">benar</button>
            <button class="quiz-option">salah</button>
            <button class="quiz-option">rumusnya mustahil</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
