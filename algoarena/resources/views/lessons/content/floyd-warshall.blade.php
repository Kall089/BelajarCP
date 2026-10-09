@php
    $fwSteps = [
        ['Header & konstanta tak hingga', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;
CPP, <<<'TXT'
<p>Seperti di Dijkstra, kita butuh nilai "tak hingga" untuk pasangan yang belum punya jalur. <code>LLONG_MAX / 4</code> cukup besar, tetapi masih aman jika dua INF dijumlahkan.</p>
TXT],
        ['main() & membaca N, M', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
CPP, <<<'TXT'
<p>Graph berarah dengan <code>n</code> simpul dan <code>m</code> sisi. Pada contoh: 4 simpul, 6 sisi.</p>
TXT],
        ['Matriks jarak awal', <<<'CPP'

    vector<vector<long long>> dist(n + 1, vector<long long>(n + 1, INF));
    for (int i = 1; i <= n; i++) dist[i][i] = 0;
CPP, <<<'TXT'
<p>Floyd-Warshall memakai <strong>adjacency matrix</strong>. <code>dist[i][j]</code> adalah jarak terpendek dari i ke j yang sudah diketahui. Awalnya semua INF, kecuali diagonal: jarak dari simpul ke dirinya sendiri adalah 0.</p>
<div class="wt-warn">Matriks berukuran (n + 1)². Untuk n = 500 itu 251 ribu angka (±2 MB), aman. Untuk n = 10<sup>5</sup> mustahil. Floyd hanya untuk n kecil.</div>
TXT],
        ['Isi bobot sisi', <<<'CPP'

    for (int i = 0; i < m; i++) {
        int u, v;
        long long w;
        cin >> u >> v >> w;
        dist[u][v] = min(dist[u][v], w);
    }
CPP, <<<'TXT'
<p>Setiap sisi <code>u → v</code> berbobot w langsung menjadi jarak awal <code>dist[u][v]</code>.</p>
<p>Mengapa <code>min</code>? Soal kadang memberi <strong>beberapa sisi</strong> untuk pasangan yang sama. Kita simpan yang termurah saja.</p>
<pre>       1   2   3   4
  1    0   8   ∞   1
  2    ∞   0   1   ∞
  3    4   ∞   0   ∞
  4    ∞   2   9   0</pre>
TXT],
        ['Tiga loop: k di paling luar', <<<'CPP'

    for (int k = 1; k <= n; k++) {
        for (int i = 1; i <= n; i++) {
            for (int j = 1; j <= n; j++) {
CPP, <<<'TXT'
<p>Inilah seluruh "rahasia" Floyd-Warshall. Loop <code>k</code> berarti: <strong>"mulai sekarang, simpul k boleh dipakai sebagai perantara"</strong>. Untuk setiap k, kita periksa semua pasangan (i, j).</p>
<p>Setelah putaran k selesai, <code>dist[i][j]</code> berisi jarak terpendek dari i ke j yang hanya boleh transit di simpul 1..k.</p>
<div class="wt-warn"><strong>Urutan loop wajib k, i, j.</strong> Jika k diletakkan di dalam, jawabannya bisa salah karena sebuah jalur dipakai sebelum jalur itu sendiri selesai dihitung.</div>
TXT],
        ['Bolehkah lewat k?', <<<'CPP'

                if (dist[i][k] == INF || dist[k][j] == INF) continue;
                dist[i][j] = min(dist[i][j], dist[i][k] + dist[k][j]);
            }
        }
    }
CPP, <<<'TXT'
<p>Jalur i → k → j panjangnya <code>dist[i][k] + dist[k][j]</code>. Jika lebih pendek dari <code>dist[i][j]</code> yang sekarang, perbarui. Ini relaksasi, sama seperti di Dijkstra.</p>
<p>Baris <code>continue</code> melewati pasangan yang salah satu bagiannya belum terjangkau. Tanpa pemeriksaan ini, <code>INF + bobot negatif</code> bisa menghasilkan angka yang terlihat "lebih kecil dari INF" padahal sebenarnya tidak ada jalur.</p>
<pre>k = 4: dist[1][2] = min(8, dist[1][4] + dist[4][2]) = min(8, 1 + 2) = 3
       dist[3][2] = min(12, 5 + 2) = 7</pre>
TXT],
        ['Mencetak matriks', <<<'CPP'

    for (int i = 1; i <= n; i++) {
        for (int j = 1; j <= n; j++) {
            if (dist[i][j] == INF) cout << "INF";
            else cout << dist[i][j];
            cout << (j < n ? ' ' : '\n');
        }
    }
    return 0;
}
CPP, <<<'TXT'
<p>Cetak matriks baris demi baris. Pasangan yang tetap INF berarti memang tidak ada jalur, jadi dicetak sebagai tulisan <code>INF</code>.</p>
<p>Setelah dihitung sekali (O(n³)), pertanyaan "berapa jarak dari a ke b?" bisa dijawab langsung dengan <code>dist[a][b]</code> dalam O(1). Itulah kekuatan Floyd untuk soal dengan <strong>banyak pertanyaan</strong>.</p>
TXT],
    ];

    $fwJs = <<<'JS'
const [n, m] = readInts();
const dist = Array.from({ length: n + 1 }, () => new Array(n + 1).fill(Infinity));
for (let i = 1; i <= n; i++) dist[i][i] = 0;
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  dist[u][v] = Math.min(dist[u][v], w);
}

for (let k = 1; k <= n; k++) {
  for (let i = 1; i <= n; i++) {
    if (dist[i][k] === Infinity) continue;
    for (let j = 1; j <= n; j++) {
      if (dist[i][k] + dist[k][j] < dist[i][j]) dist[i][j] = dist[i][k] + dist[k][j];
    }
  }
}

const out = [];
for (let i = 1; i <= n; i++) {
  out.push(dist[i].slice(1).map((d) => (d === Infinity ? "INF" : d)).join(" "));
}
console.log(out.join("\n"));
JS;

    $fwPy = <<<'PY'
import sys
input = sys.stdin.readline

INF = float("inf")
n, m = map(int, input().split())
dist = [[INF] * (n + 1) for _ in range(n + 1)]
for i in range(1, n + 1):
    dist[i][i] = 0
for _ in range(m):
    u, v, w = map(int, input().split())
    dist[u][v] = min(dist[u][v], w)

for k in range(1, n + 1):
    dk = dist[k]
    for i in range(1, n + 1):
        dik = dist[i][k]
        if dik == INF:
            continue
        di = dist[i]
        for j in range(1, n + 1):
            if dik + dk[j] < di[j]:
                di[j] = dik + dk[j]

for i in range(1, n + 1):
    print(" ".join("INF" if d == INF else str(d) for d in dist[i][1:]))
PY;

    $bfSteps = [
        ['Header, INF, dan struct sisi', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

struct Sisi {
    int u, v;
    long long w;
};
CPP, <<<'TXT'
<p>Bellman-Ford memproses <strong>daftar sisi</strong> berulang-ulang, jadi kita simpan setiap sisi dalam <code>struct</code>. Bobot <code>w</code> boleh <strong>negatif</strong>.</p>
TXT],
        ['Membaca semua sisi', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<Sisi> sisi(m);
    for (auto &e : sisi) cin >> e.u >> e.v >> e.w;
CPP, <<<'TXT'
<p>Baca n, m, lalu isi setiap sisi melalui referensi <code>auto &amp;e</code>. Pada contoh ada sisi <code>3 → 4</code> berbobot <strong>−3</strong>.</p>
TXT],
        ['Jarak awal dari simpul 1', <<<'CPP'

    vector<long long> dist(n + 1, INF);
    dist[1] = 0;
CPP, <<<'TXT'
<p>Sumbernya simpul 1. Sama seperti Dijkstra: jarak ke sumber 0, lainnya INF.</p>
TXT],
        ['N − 1 ronde relaksasi', <<<'CPP'

    for (int ronde = 1; ronde <= n - 1; ronde++) {
        bool berubah = false;
        for (auto &e : sisi) {
            if (dist[e.u] == INF) continue;
            if (dist[e.u] + e.w < dist[e.v]) {
                dist[e.v] = dist[e.u] + e.w;
                berubah = true;
            }
        }
        if (!berubah) break;
    }
CPP, <<<'TXT'
<p>Setiap <strong>ronde</strong>, relaksasi <em>semua</em> sisi satu per satu. Tidak ada priority queue, tidak ada pemilihan simpul terdekat. Sederhana sekali!</p>
<p>Mengapa cukup <code>n − 1</code> ronde? Jalur terpendek (tanpa siklus) paling banyak berisi <code>n − 1</code> sisi. Setiap ronde menjamin minimal satu sisi lagi dari jalur itu "terkunci" dengan benar.</p>
<pre>ronde 1: dist[2] = 5, dist[3] = 10
ronde 2: dist[4] = 7, dist[3] = 9   (lewat 2)
ronde 3: dist[5] = 9, dist[4] = 6
ronde 4: dist[5] = 8</pre>
<div class="wt-tip">Variabel <code>berubah</code> adalah optimasi: jika satu ronde penuh tidak mengubah apa pun, ronde berikutnya juga tidak akan mengubah apa pun, jadi kita boleh berhenti lebih awal.</div>
TXT],
        ['Ronde pemeriksaan: siklus negatif?', <<<'CPP'

    for (auto &e : sisi) {
        if (dist[e.u] != INF && dist[e.u] + e.w < dist[e.v]) {
            cout << "SIKLUS NEGATIF\n";
            return 0;
        }
    }
CPP, <<<'TXT'
<p>Setelah n − 1 ronde, semua jarak seharusnya sudah final. Jika <strong>masih ada</strong> sisi yang bisa merelaksasi, artinya ada <strong>siklus negatif</strong>: berputar di siklus itu terus mengurangi jarak tanpa batas, sehingga "jarak terpendek" tidak terdefinisi.</p>
<div class="wt-tip">Contoh siklus negatif: 3 → 4 (−3), 4 → 5 (+2), 5 → 3 (−4). Totalnya −5, jadi setiap putaran mengurangi jarak 5.</div>
TXT],
        ['Mencetak jarak', <<<'CPP'

    for (int v = 1; v <= n; v++) {
        if (dist[v] == INF) cout << "INF";
        else cout << dist[v];
        cout << (v < n ? ' ' : '\n');
    }
    return 0;
}
CPP, <<<'TXT'
<p>Jika tidak ada siklus negatif, cetak jarak dari simpul 1 ke setiap simpul, atau <code>INF</code> jika tidak terjangkau.</p>
TXT],
    ];

    $bfJs = <<<'JS'
const [n, m] = readInts();
const sisi = [];
for (let i = 0; i < m; i++) sisi.push(readInts());

const dist = new Array(n + 1).fill(Infinity);
dist[1] = 0;
for (let ronde = 1; ronde <= n - 1; ronde++) {
  let berubah = false;
  for (const [u, v, w] of sisi) {
    if (dist[u] !== Infinity && dist[u] + w < dist[v]) {
      dist[v] = dist[u] + w;
      berubah = true;
    }
  }
  if (!berubah) break;
}

const negatif = sisi.some(([u, v, w]) => dist[u] !== Infinity && dist[u] + w < dist[v]);
console.log(negatif ? "SIKLUS NEGATIF" : dist.slice(1).map((d) => (d === Infinity ? "INF" : d)).join(" "));
JS;

    $bfPy = <<<'PY'
import sys
input = sys.stdin.readline

INF = float("inf")
n, m = map(int, input().split())
sisi = [tuple(map(int, input().split())) for _ in range(m)]

dist = [INF] * (n + 1)
dist[1] = 0
for ronde in range(n - 1):
    berubah = False
    for u, v, w in sisi:
        if dist[u] != INF and dist[u] + w < dist[v]:
            dist[v] = dist[u] + w
            berubah = True
    if not berubah:
        break

if any(dist[u] != INF and dist[u] + w < dist[v] for u, v, w in sisi):
    print("SIKLUS NEGATIF")
else:
    print(" ".join("INF" if d == INF else str(d) for d in dist[1:]))
PY;

    $fwPath = <<<'CPP'
// Rekonstruksi rute Floyd: nxt[i][j] = simpul berikutnya setelah i pada rute i → j
vector<vector<int>> nxt(n + 1, vector<int>(n + 1, 0));
// saat membaca sisi u → v:   nxt[u][v] = v;   (dan nxt[i][i] = i)
// saat relaksasi lewat k:
if (dist[i][k] + dist[k][j] < dist[i][j]) {
    dist[i][j] = dist[i][k] + dist[k][j];
    nxt[i][j] = nxt[i][k];          // dari i, langkah pertama sama seperti menuju k
}
// mencetak rute a → b:
for (int x = a; x != b; x = nxt[x][b]) cout << x << ' ';
cout << b << '\n';
CPP;

    $fwReach = <<<'CPP'
// Transitive closure: bisa[i][j] = apakah ada jalur dari i ke j
for (int k = 1; k <= n; k++)
    for (int i = 1; i <= n; i++)
        if (bisa[i][k])
            for (int j = 1; j <= n; j++)
                if (bisa[k][j]) bisa[i][j] = true;
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menghitung jarak terpendek antara <strong>semua pasangan</strong> simpul dengan <strong>Floyd-Warshall</strong>.</li>
        <li>Memahami Floyd-Warshall sebagai <strong>DP di atas graph</strong>: "bolehkah lewat simpul k?".</li>
        <li>Menangani sisi berbobot <strong>negatif</strong> dan mendeteksi <strong>siklus negatif</strong> dengan <strong>Bellman-Ford</strong>.</li>
        <li>Memilih algoritma jalur terpendek yang tepat: BFS, Dijkstra, Bellman-Ford, atau Floyd-Warshall.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'jarak semua pasangan', 'desc' => 'Ide "perantara" Floyd-Warshall dan relaksasi berulang Bellman-Ford.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Tabel Jarak">
    <h2>Intuisi: Tabel Jarak Antar Kota</h2>
    <div class="prose">
        <p>Di buku atlas sering ada <strong>tabel jarak antar kota</strong>: baris dan kolomnya nama kota, isinya jarak terpendek. Dijkstra hanya menghitung satu baris (dari satu sumber). Bagaimana mengisi <strong>seluruh tabel</strong> sekaligus?</p>
        <p>Ide Floyd-Warshall: mulai dari tabel yang hanya berisi jalan langsung. Lalu izinkan simpul 1 sebagai tempat transit, perbarui tabel. Izinkan simpul 2, perbarui lagi. Begitu seterusnya sampai semua simpul pernah menjadi perantara.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 380, 'directed' => true,
        'nodes' => [[1, 110, 110, 'cyan'], [2, 330, 60, 'vis'], [3, 545, 200, 'vis'], [4, 270, 315, 'cur']],
        'edges' => [[1, 2, '', 8], [1, 4, 'ok', 1], [4, 2, 'ok', 2], [2, 3, '', 1], [3, 1, '', 4], [4, 3, '', 9]],
        'caption' => 'Dari 1 ke 2 ada jalan langsung berbobot 8. Tetapi jika <strong>boleh transit di 4</strong>: 1 → 4 → 2 = 1 + 2 = 3.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Rumus intinya hanya satu baris: <code>dist[i][j] = min(dist[i][j], dist[i][k] + dist[k][j])</code>, dijalankan untuk setiap perantara k dan setiap pasangan (i, j).</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose">
        <p>Matriks awal (kiri) hanya berisi jalan langsung. Berikut semua perubahan yang terjadi saat k = 1, 2, 3, 4, sampai menghasilkan matriks akhir (kanan).</p>
    </div>
    <div class="diagram-grid" style="max-width: 860px; margin-bottom: 14px">
        <div>
            <h5>Awal (hanya jalan langsung)</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(5, auto)">
                <span class="h"></span><span class="h">1</span><span class="h">2</span><span class="h">3</span><span class="h">4</span>
                <span class="h">1</span><span>0</span><span>8</span><span class="dim">∞</span><span>1</span>
                <span class="h">2</span><span class="dim">∞</span><span>0</span><span>1</span><span class="dim">∞</span>
                <span class="h">3</span><span>4</span><span class="dim">∞</span><span>0</span><span class="dim">∞</span>
                <span class="h">4</span><span class="dim">∞</span><span>2</span><span>9</span><span>0</span>
            </div>
        </div>
        <div>
            <h5>Akhir (semua perantara boleh)</h5>
            <div class="dp-mini" style="grid-template-columns: repeat(5, auto)">
                <span class="h"></span><span class="h">1</span><span class="h">2</span><span class="h">3</span><span class="h">4</span>
                <span class="h">1</span><span>0</span><span class="ok">3</span><span class="ok">4</span><span>1</span>
                <span class="h">2</span><span class="ok">5</span><span>0</span><span>1</span><span class="ok">6</span>
                <span class="h">3</span><span>4</span><span class="ok">7</span><span>0</span><span class="ok">5</span>
                <span class="h">4</span><span class="ok">7</span><span>2</span><span class="ok">3</span><span>0</span>
            </div>
        </div>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Perantara k</th><th>Perubahan</th><th>Rute baru</th></tr>
            <tr><td>k = 1</td><td><span class="new">dist[3][2]: ∞ → 12</span>, <span class="new">dist[3][4]: ∞ → 5</span></td><td>3 → 1 → 2, 3 → 1 → 4</td></tr>
            <tr class="hl"><td>k = 2</td><td><span class="new">dist[1][3]: ∞ → 9</span>, <span class="new">dist[4][3]: 9 → 3</span></td><td>1 → 2 → 3, 4 → 2 → 3</td></tr>
            <tr><td>k = 3</td><td><span class="new">dist[2][1] = 5</span>, <span class="new">dist[2][4] = 6</span>, <span class="new">dist[4][1] = 7</span></td><td>2 → 3 → 1, dst.</td></tr>
            <tr class="hl"><td>k = 4</td><td><span class="new">dist[1][2]: 8 → 3</span>, <span class="new">dist[1][3]: 9 → 4</span>, <span class="new">dist[3][2]: 12 → 7</span></td><td>1 → 4 → 2, 1 → 4 → (2 → 3), 3 → (1 → 4) → 2</td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Perhatikan k = 4: <code>dist[1][3] = dist[1][4] + dist[4][3] = 1 + 3</code>. Nilai <code>dist[4][3] = 3</code> sendiri sudah memakai perantara 2 (dihitung saat k = 2). Jalur panjang tersusun dari potongan-potongan yang sudah optimal. Itulah ciri <strong>Dynamic Programming</strong>.</p>
    </div>
</section>

<section class="lesson-section" id="negatif" data-toc="Bobot Negatif">
    <h2>Bagaimana Jika Ada Bobot Negatif?</h2>
    <div class="prose">
        <p>Bobot negatif bisa berarti "dapat uang/bonus" saat melewati jalan itu. Dijkstra tidak bisa dipakai karena ia memfinalkan simpul terlalu cepat. <strong>Bellman-Ford</strong> mengatasinya dengan cara yang lebih sabar: relaksasi <strong>semua sisi</strong>, ulangi <strong>N − 1 kali</strong>.</p>
        <p>Bahaya terbesarnya adalah <strong>siklus negatif</strong>: siklus yang total bobotnya negatif. Berputar di sana terus mengurangi jarak tanpa akhir. Bellman-Ford bisa <strong>mendeteksinya</strong> dengan satu ronde tambahan.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 380, 'directed' => true,
        'nodes' => [[1, 80, 190, 'green'], [2, 235, 80, 'vis'], [3, 235, 305, 'vis'], [4, 430, 305, 'vis'], [5, 570, 175, 'vis']],
        'edges' => [[4, 5, '', 2], [3, 4, 'bad', -3], [2, 3, '', 4], [1, 2, '', 5], [1, 3, '', 10]],
        'badges' => [1 => '0', 2 => '5', 3 => '9', 4 => '6', 5 => '8'],
        'caption' => 'Jarak dari simpul 1 setelah Bellman-Ford. Sisi 3 → 4 berbobot −3 membuat 1 → 2 → 3 → 4 hanya 5 + 4 − 3 = 6.',
    ])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2>Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Mode <strong>Floyd-Warshall</strong>: perhatikan sel biru <code>dist[i][k]</code> dan pink <code>dist[k][j]</code> yang dijumlahkan untuk mengisi sel kuning <code>dist[i][j]</code>. Mode <strong>Bellman-Ford</strong>: lihat bagaimana jarak "merambat" satu sisi per ronde, lalu tekan <strong>Tambah siklus negatif</strong> untuk melihat deteksinya.</p>
    </div>
    <div data-viz="floyd"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Cek ukuran N</b><span>N ≤ ±500 dan banyak pertanyaan jarak antar pasangan: <strong>Floyd-Warshall</strong> (O(N³)).</span></div>
        <div class="step-card"><b>Cek bobot negatif</b><span>Ada bobot negatif dan satu sumber: <strong>Bellman-Ford</strong> (O(N·M)). Diminta mendeteksi siklus negatif: juga Bellman-Ford.</span></div>
        <div class="step-card"><b>Siapkan INF dengan aman</b><span>Gunakan <code>long long</code> dan lewati pasangan yang salah satu bagiannya masih INF.</span></div>
        <div class="step-card"><b>Floyd: k di loop terluar</b><span>Lalu i, lalu j. Relaksasi <code>dist[i][j]</code> lewat k.</span></div>
        <div class="step-card"><b>Bellman-Ford: N − 1 ronde + 1 ronde cek</b><span>Jika ronde ke-N masih mengubah jarak, ada siklus negatif.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis Floyd-Warshall & Bellman-Ford di C++', 'desc' => 'Dua program lengkap, masing-masing dibahas baris demi baris.'])

<section class="lesson-section" id="kode" data-toc="Kode C++: Floyd-Warshall">
    <h2>Kode C++ Lengkap: Floyd-Warshall</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> graph berarah berbobot. Cetak matriks jarak terpendek antar semua pasangan, tulis <code>INF</code> jika tidak ada jalur.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Floyd-Warshall: jarak semua pasangan',
        'steps' => $fwSteps,
        'sample' => ['input' => "4 6\n1 2 8\n1 4 1\n4 2 2\n2 3 1\n3 1 4\n4 3 9\n", 'output' => "0 3 4 1\n5 0 1 6\n4 7 0 5\n7 2 3 0\n"],
        'js' => $fwJs,
        'py' => $fwPy,
    ])
</section>

<section class="lesson-section" id="kode-bf" data-toc="Kode C++: Bellman-Ford">
    <h2>Kode C++ Lengkap: Bellman-Ford</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> graph berarah, bobot boleh negatif. Cetak jarak dari simpul 1 ke semua simpul, atau <code>SIKLUS NEGATIF</code> jika ada siklus negatif yang terjangkau dari simpul 1.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Bellman-Ford: bobot negatif & deteksi siklus',
        'steps' => $bfSteps,
        'sample' => ['input' => "5 5\n4 5 2\n3 4 -3\n2 3 4\n1 2 5\n1 3 10\n", 'output' => "0 5 9 6 8\n"],
        'js' => $bfJs,
        'py' => $bfPy,
    ])
</section>

<section class="lesson-section" id="pilih" data-toc="Memilih Algoritma">
    <h2>Memilih Algoritma Jalur Terpendek</h2>
    <table class="cx-table" style="max-width: 860px">
        <tr><th>Situasi</th><th>Algoritma</th><th>Waktu</th></tr>
        <tr><td>Semua bobot sama (atau tak berbobot)</td><td>BFS</td><td><code>O(N + M)</code></td></tr>
        <tr><td>Bobot 0 atau 1</td><td>0-1 BFS</td><td><code>O(N + M)</code></td></tr>
        <tr><td>Bobot ≥ 0, satu sumber</td><td>Dijkstra</td><td><code>O((N + M) log M)</code></td></tr>
        <tr><td>Ada bobot negatif / cek siklus negatif</td><td>Bellman-Ford</td><td><code>O(N · M)</code></td></tr>
        <tr><td>Semua pasangan, N kecil (≤ ±500)</td><td>Floyd-Warshall</td><td><code>O(N³)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Floyd dengan N = 2000</strong> berarti 8 × 10<sup>9</sup> operasi: pasti TLE. Untuk banyak sumber pada graph besar, jalankan Dijkstra dari setiap sumber yang ditanyakan saja.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Graph tak berarah + bobot negatif</strong> otomatis punya siklus negatif (bolak-balik di sisi negatif itu). Biasanya soal bobot negatif selalu berarah.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'memahami lebih dalam', 'desc' => 'Bukti DP Floyd, alasan N − 1 ronde, rekonstruksi rute, dan transitive closure.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Benar?">
    <h2>Mengapa Keduanya Benar?</h2>
    <div class="proof">
        <p><strong>Floyd-Warshall sebagai DP.</strong> Definisikan <code>D<sub>k</sub>[i][j]</code> = jarak terpendek dari i ke j yang <em>hanya</em> boleh transit di simpul 1..k. Jalur terbaik itu entah tidak melewati k (nilainya <code>D<sub>k−1</sub>[i][j]</code>), entah melewati k tepat sekali (nilainya <code>D<sub>k−1</sub>[i][k] + D<sub>k−1</sub>[k][j]</code>). Ambil minimumnya. <code>D<sub>N</sub></code> adalah jawabannya.</p>
        <p><strong>Mengapa satu matriks cukup?</strong> Selama putaran k, nilai <code>dist[i][k]</code> dan <code>dist[k][j]</code> tidak berubah (lewat k untuk menuju k sendiri tidak membantu, karena <code>dist[k][k] = 0</code>). Jadi memperbarui di tempat aman.</p>
        <p><strong>Bellman-Ford.</strong> Jalur terpendek tanpa siklus punya paling banyak N − 1 sisi: v<sub>0</sub> → v<sub>1</sub> → … → v<sub>t</sub>. Setelah ronde ke-r, <code>dist[v<sub>r</sub>]</code> pasti sudah benar, karena sisi v<sub>r−1</sub> → v<sub>r</sub> direlaksasi setelah <code>dist[v<sub>r−1</sub>]</code> benar. Setelah N − 1 ronde, semua benar. Jika ronde ke-N masih bisa mengubah sesuatu, berarti ada jalur "terpendek" dengan N sisi atau lebih, yang hanya mungkin jika ada siklus negatif.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2>Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Rekonstruksi rute pada Floyd</h3>
        <p>Simpan <code>nxt[i][j]</code> = simpul pertama yang dikunjungi setelah i pada rute terpendek i → j. Saat relaksasi lewat k, langkah pertama menuju j sama dengan langkah pertama menuju k.</p>
    </div>
    @include('lessons.code', ['cpp' => $fwPath, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. Transitive closure</h3>
        <p>Pola tiga loop yang sama bisa menjawab "apakah i bisa mencapai j?" dengan tabel <code>bool</code>. Contoh: "apakah tim A secara tidak langsung lebih kuat dari tim B?"</p>
    </div>
    @include('lessons.code', ['cpp' => $fwReach, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>3. Siklus negatif pada Floyd</h3>
        <p>Setelah Floyd selesai, jika ada <code>dist[i][i] &lt; 0</code>, simpul i berada pada siklus negatif. Pasangan (a, b) yang rutenya bisa melewati simpul seperti itu tidak punya jarak terpendek.</p>
        <h3>4. SPFA</h3>
        <p>Bellman-Ford yang hanya merelaksasi sisi dari simpul yang baru berubah (memakai antrian) disebut SPFA. Sering lebih cepat, tetapi kasus terburuknya tetap <code>O(N · M)</code>, jadi jangan diandalkan untuk graph besar berbobot positif. Gunakan Dijkstra.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="Loop k harus di paling luar. Arti putaran k: semua jalur yang hanya transit di 1..k sudah dihitung lengkap sebelum k + 1 diizinkan.">
        <p class="quiz-q">Urutan tiga loop Floyd-Warshall yang benar adalah…</p>
        <div class="quiz-options">
            <button class="quiz-option">k (perantara), lalu i, lalu j</button>
            <button class="quiz-option">i, lalu j, lalu k</button>
            <button class="quiz-option">Urutan apa saja, hasilnya sama</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Jalur terpendek tanpa siklus paling banyak berisi N − 1 sisi, jadi N − 1 ronde cukup. Ronde ke-N dipakai untuk mendeteksi siklus negatif.">
        <p class="quiz-q">Mengapa Bellman-Ford cukup menjalankan N − 1 ronde?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena setiap ronde memfinalkan satu simpul terdekat</button>
            <button class="quiz-option">Karena graph punya N − 1 sisi</button>
            <button class="quiz-option">Karena jalur terpendek paling banyak terdiri dari N − 1 sisi</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="N = 300 dan banyak pertanyaan antar pasangan: Floyd O(N³) = 2,7 × 10^7 operasi sekali saja, lalu setiap pertanyaan O(1).">
        <p class="quiz-q">N = 300 kota, 10<sup>5</sup> pertanyaan "jarak dari a ke b", semua bobot positif. Pilihan terbaik?</p>
        <div class="quiz-options">
            <button class="quiz-option">Dijkstra untuk setiap pertanyaan</button>
            <button class="quiz-option">Floyd-Warshall sekali, lalu jawab dari matriks</button>
            <button class="quiz-option">Bellman-Ford untuk setiap pertanyaan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
