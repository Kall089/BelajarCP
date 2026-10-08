@php
    $mstSteps = [
        ['Header & struktur sisi', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

struct Sisi {
    int u, v, w;
};
CPP, <<<'TXT'
<p>Kruskal memproses <strong>daftar sisi</strong> (edge list), bukan adjacency list. Setiap sisi kita simpan dalam <code>struct</code> berisi dua ujung <code>u</code>, <code>v</code> dan bobot <code>w</code>.</p>
<p><code>struct</code> seperti "formulir" dengan beberapa kolom. <code>e.u</code>, <code>e.v</code>, <code>e.w</code> mengakses kolom-kolomnya.</p>
TXT],
        ['Union-Find: mencari ketua kelompok', <<<'CPP'

vector<int> par, sz;

int find(int x) {
    if (par[x] == x) return x;
    return par[x] = find(par[x]);
}
CPP, <<<'TXT'
<p><strong>Union-Find</strong> (DSU, <em>Disjoint Set Union</em>) mencatat simpul-simpul yang sudah tersambung dalam satu kelompok. Setiap kelompok punya satu <strong>ketua</strong>.</p>
<ul>
    <li><code>par[x]</code>: "atasan" x. Jika <code>par[x] == x</code>, x adalah ketua kelompoknya.</li>
    <li><code>find(x)</code>: naik terus ke atasan sampai bertemu ketua.</li>
</ul>
<p>Bagian <code>par[x] = find(par[x])</code> adalah <strong>path compression</strong>: setelah ketua ditemukan, x langsung menunjuk ke ketua. Pencarian berikutnya jadi hampir instan.</p>
<div class="wt-tip">Dua simpul berada di kelompok yang sama jika dan hanya jika <code>find(a) == find(b)</code>.</div>
TXT],
        ['Union-Find: menggabungkan dua kelompok', <<<'CPP'

bool unite(int a, int b) {
    a = find(a);
    b = find(b);
    if (a == b) return false;
    if (sz[a] < sz[b]) swap(a, b);
    par[b] = a;
    sz[a] += sz[b];
    return true;
}
CPP, <<<'TXT'
<p><code>unite(a, b)</code> menyambungkan kelompok a dan kelompok b:</p>
<ol>
    <li>Cari ketua masing-masing.</li>
    <li>Jika ketuanya sama, keduanya sudah satu kelompok. Kembalikan <code>false</code>: menyambung lagi hanya membuat <strong>siklus</strong>.</li>
    <li>Jika berbeda, ketua kelompok yang <strong>lebih kecil</strong> diangkat menjadi bawahan ketua kelompok yang lebih besar (<em>union by size</em>), lalu ukuran digabung.</li>
</ol>
<p>Kombinasi path compression dan union by size membuat setiap operasi praktis <code>O(1)</code>.</p>
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
<p>Baca <code>n</code> simpul dan <code>m</code> sisi, lalu isi setiap elemen vector <code>sisi</code> secara langsung.</p>
<p><code>for (auto &amp;e : sisi)</code> memakai <strong>referensi</strong> (<code>&amp;</code>) sehingga yang diisi adalah elemen aslinya. Tanpa <code>&amp;</code>, yang terisi hanya salinannya dan vector tetap kosong.</p>
TXT],
        ['Urutkan sisi dari yang termurah', <<<'CPP'

    sort(sisi.begin(), sisi.end(), [](const Sisi &a, const Sisi &b) {
        return tie(a.w, a.u, a.v) < tie(b.w, b.u, b.v);
    });
CPP, <<<'TXT'
<p>Inti Kruskal: periksa sisi dari bobot <strong>terkecil</strong>. Kita beri <code>sort</code> sebuah fungsi pembanding (<em>lambda</em>) yang mengurutkan berdasarkan bobot, lalu u, lalu v jika bobotnya sama.</p>
<p><code>tie(a.w, a.u, a.v) &lt; tie(b.w, b.u, b.v)</code> membandingkan tiga nilai sekaligus seperti membandingkan kata di kamus: kolom pertama dulu, jika sama baru kolom berikutnya.</p>
<pre>(4,5,2) (1,3,3) (6,7,3) (1,2,4) (2,3,5) (5,7,5) (2,4,6) …</pre>
TXT],
        ['Setiap simpul memulai sebagai kelompok sendiri', <<<'CPP'

    par.resize(n + 1);
    sz.assign(n + 1, 1);
    for (int i = 1; i <= n; i++) par[i] = i;
CPP, <<<'TXT'
<p>Awalnya belum ada kabel sama sekali, jadi setiap simpul adalah ketua bagi dirinya sendiri (<code>par[i] = i</code>) dengan ukuran kelompok 1.</p>
TXT],
        ['Ambil sisi yang tidak membentuk siklus', <<<'CPP'

    long long total = 0;
    vector<Sisi> dipilih;
    for (auto &e : sisi) {
        if (unite(e.u, e.v)) {
            total += e.w;
            dipilih.push_back(e);
            if ((int)dipilih.size() == n - 1) break;
        }
    }
CPP, <<<'TXT'
<p>Telusuri sisi dari yang termurah. <code>unite</code> sekaligus mengecek dan menggabungkan:</p>
<ul>
    <li>Jika <code>true</code>, kedua ujung tadinya berbeda kelompok. Sisi ini <strong>diambil</strong>: tambahkan bobotnya dan catat.</li>
    <li>Jika <code>false</code>, kedua ujung sudah tersambung. Sisi ini akan membentuk siklus, jadi <strong>dilewati</strong>.</li>
</ul>
<p>Pohon dengan n simpul selalu punya tepat <code>n − 1</code> sisi, jadi begitu jumlah itu tercapai kita bisa berhenti.</p>
<pre>(4,5,2) ambil   (1,3,3) ambil   (6,7,3) ambil   (1,2,4) ambil
(2,3,5) LEWATI: 2 dan 3 sudah satu kelompok
(5,7,5) ambil   (2,4,6) ambil → sudah 6 sisi, selesai</pre>
TXT],
        ['Cek keterhubungan & cetak', <<<'CPP'

    if ((int)dipilih.size() < n - 1) {
        cout << -1 << '\n';
        return 0;
    }
    cout << total << '\n';
    for (auto &e : dipilih) cout << e.u << ' ' << e.v << ' ' << e.w << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Jika setelah semua sisi diperiksa kita masih punya kurang dari <code>n − 1</code> sisi, graph aslinya <strong>tidak terhubung</strong> sehingga mustahil menyambungkan semua simpul. Cetak -1.</p>
<p>Jika tidak, cetak total biaya (23 pada contoh) dan daftar sisi yang dipilih.</p>
<div class="wt-tip"><code>total</code> memakai <code>long long</code> karena jumlah bobot bisa mencapai (n − 1) × 10<sup>9</sup>.</div>
TXT],
    ];

    $mstJs = <<<'JS'
const [n, m] = readInts();
const sisi = [];
for (let i = 0; i < m; i++) {
  const [u, v, w] = readInts();
  sisi.push([w, u, v]);
}
sisi.sort((a, b) => a[0] - b[0] || a[1] - b[1] || a[2] - b[2]);

const par = Array.from({ length: n + 1 }, (_, i) => i);
const sz = new Array(n + 1).fill(1);
const find = (x) => {
  while (par[x] !== x) {
    par[x] = par[par[x]]; // path halving
    x = par[x];
  }
  return x;
};
const unite = (a, b) => {
  a = find(a);
  b = find(b);
  if (a === b) return false;
  if (sz[a] < sz[b]) [a, b] = [b, a];
  par[b] = a;
  sz[a] += sz[b];
  return true;
};

let total = 0;
const dipilih = [];
for (const [w, u, v] of sisi) {
  if (unite(u, v)) {
    total += w;
    dipilih.push(`${u} ${v} ${w}`);
    if (dipilih.length === n - 1) break;
  }
}

console.log(dipilih.length < n - 1 ? "-1" : [total, ...dipilih].join("\n"));
JS;

    $mstPy = <<<'PY'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
sisi = []
for _ in range(m):
    u, v, w = map(int, input().split())
    sisi.append((w, u, v))
sisi.sort()

par = list(range(n + 1))
sz = [1] * (n + 1)

def find(x):
    while par[x] != x:
        par[x] = par[par[x]]  # path halving
        x = par[x]
    return x

def unite(a, b):
    a, b = find(a), find(b)
    if a == b:
        return False
    if sz[a] < sz[b]:
        a, b = b, a
    par[b] = a
    sz[a] += sz[b]
    return True

total = 0
dipilih = []
for w, u, v in sisi:
    if unite(u, v):
        total += w
        dipilih.append(f"{u} {v} {w}")
        if len(dipilih) == n - 1:
            break

if len(dipilih) < n - 1:
    print(-1)
else:
    print(total)
    print("\n".join(dipilih))
PY;

    $mstPrim = <<<'CPP'
// Prim: tumbuhkan pohon dari simpul 1, selalu tambah sisi termurah yang keluar
vector<bool> masuk(n + 1, false);
priority_queue<pair<int, int>, vector<pair<int, int>>, greater<pair<int, int>>> pq;
pq.push({0, 1});                       // {bobot sisi penghubung, simpul}
long long total = 0;
while (!pq.empty()) {
    int w = pq.top().first, u = pq.top().second;
    pq.pop();
    if (masuk[u]) continue;            // sudah tergabung di pohon
    masuk[u] = true;
    total += w;
    for (auto &e : adj[u]) {
        if (!masuk[e.first]) pq.push({e.second, e.first});
    }
}
CPP;

    $mstDsu = <<<'CPP'
// DSU untuk soal keterhubungan dinamis: "gabung a b" dan "tanya a b"
// sambil menghitung banyak kelompok yang tersisa
int kelompok = n;
while (q--) {
    string jenis;
    int a, b;
    cin >> jenis >> a >> b;
    if (jenis == "gabung") {
        if (unite(a, b)) kelompok--;
    } else {
        cout << (find(a) == find(b) ? "YA" : "TIDAK") << '\n';
    }
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan apa itu <strong>spanning tree</strong> dan <strong>minimum spanning tree</strong> (MST).</li>
        <li>Menjalankan <strong>algoritma Kruskal</strong>: urutkan sisi, ambil yang termurah selama tidak membentuk siklus.</li>
        <li>Menulis <strong>Union-Find (DSU)</strong> dengan path compression dan union by size di C++.</li>
        <li>Memahami bukti keserakahan (cut property), algoritma Prim, dan memakai DSU untuk soal keterhubungan.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'memahami spanning tree', 'desc' => 'Menyambungkan semua simpul semurah mungkin, lalu ide serakah Kruskal.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Kabel Termurah">
    <h2>Intuisi: Kabel Paling Murah</h2>
    <div class="prose">
        <p>Sekolah ingin menyambungkan 7 laboratorium dengan kabel jaringan. Setiap pasangan lab punya biaya pemasangan berbeda. Syaratnya: setiap lab harus bisa berkomunikasi dengan lab lain (boleh lewat lab perantara). Berapa biaya <strong>termurah</strong>?</p>
        <ul>
            <li>Tidak perlu siklus: jika A–B–C–A tersambung melingkar, satu kabel bisa dicabut dan semuanya tetap terhubung, lebih murah.</li>
            <li>Graph terhubung tanpa siklus adalah <strong>pohon</strong>. Pohon yang memuat semua simpul disebut <strong>spanning tree</strong>, dan selalu punya tepat <code>N − 1</code> sisi.</li>
            <li>Spanning tree dengan total bobot terkecil disebut <strong>Minimum Spanning Tree</strong> (MST).</li>
        </ul>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 380,
        'nodes' => [[1, 80, 200, 'vis'], [2, 200, 95, 'vis'], [3, 210, 310, 'vis'], [4, 340, 195, 'vis'], [5, 460, 85, 'vis'], [6, 470, 315, 'vis'], [7, 580, 200, 'vis']],
        'edges' => [[1, 2, 'ok', 4], [1, 3, 'ok', 3], [2, 3, '', 5], [2, 4, 'ok', 6], [3, 4, '', 7], [2, 5, '', 9], [4, 5, 'ok', 2], [4, 6, '', 8], [3, 6, '', 10], [5, 7, 'ok', 5], [6, 7, 'ok', 3], [4, 7, '', 6]],
        'caption' => 'MST berwarna hijau: 6 sisi dengan total 4 + 3 + 6 + 2 + 5 + 3 = <strong>23</strong>. Sisi abu-abu tidak diperlukan.',
    ])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Ide Kruskal:</strong> periksa sisi dari yang paling murah. Ambil sisi itu jika ia menyambungkan dua kelompok yang <strong>belum</strong> tersambung. Lewati jika kedua ujungnya sudah tersambung (akan membentuk siklus).</p>
    </div>
</section>

<section class="lesson-section" id="contoh" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose">
        <p>Urutkan semua sisi dari bobot terkecil, lalu putuskan satu per satu sambil mencatat kelompok yang sudah tersambung.</p>
    </div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Sisi (bobot)</th><th>Keputusan</th><th>Kelompok setelahnya</th><th>Total</th></tr>
            <tr class="ok"><td>4–5 (2)</td><td>Ambil</td><td>{1} {2} {3} {4,5} {6} {7}</td><td>2</td></tr>
            <tr class="ok"><td>1–3 (3)</td><td>Ambil</td><td>{1,3} {2} {4,5} {6} {7}</td><td>5</td></tr>
            <tr class="ok"><td>6–7 (3)</td><td>Ambil</td><td>{1,3} {2} {4,5} {6,7}</td><td>8</td></tr>
            <tr class="ok"><td>1–2 (4)</td><td>Ambil</td><td>{1,2,3} {4,5} {6,7}</td><td>12</td></tr>
            <tr class="hl"><td>2–3 (5)</td><td>Lewati: 2 dan 3 sudah satu kelompok</td><td>tetap</td><td>12</td></tr>
            <tr class="ok"><td>5–7 (5)</td><td>Ambil</td><td>{1,2,3} {4,5,6,7}</td><td>17</td></tr>
            <tr class="ok"><td>2–4 (6)</td><td>Ambil, sudah 6 = N − 1 sisi</td><td>{1,2,3,4,5,6,7}</td><td><b>23</b></td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">👀</span>
        <p>Untuk memutuskan "sudah satu kelompok atau belum" dengan cepat, kita butuh struktur data khusus: <strong>Union-Find</strong>. Bayangkan setiap kelompok punya <strong>ketua</strong>. Dua simpul satu kelompok jika ketuanya sama.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Interaktif">
    <h2>Visualisasi Interaktif</h2>
    <div class="prose">
        <p>Perhatikan daftar sisi terurut di panel samping dan warna kelompok yang perlahan menyatu. Dengan <strong>Mode Tebak</strong>, kamu memutuskan sendiri apakah setiap sisi diambil atau dilewati, lalu menebak total biayanya.</p>
    </div>
    <div data-viz="mst"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Kenali ciri soal MST</b><span>"Sambungkan semua", "biaya total minimum", "jaringan", "semua harus terhubung", dan sisi dua arah.</span></div>
        <div class="step-card"><b>Simpan edge list</b><span>Vector berisi <code>{u, v, w}</code>, lalu urutkan dari bobot terkecil.</span></div>
        <div class="step-card"><b>Siapkan Union-Find</b><span><code>par[i] = i</code> dan <code>sz[i] = 1</code> untuk setiap simpul.</span></div>
        <div class="step-card"><b>Ambil sisi jika <code>unite</code> berhasil</b><span>Tambahkan bobotnya ke total. Berhenti setelah N − 1 sisi.</span></div>
        <div class="step-card"><b>Cek keterhubungan</b><span>Kurang dari N − 1 sisi berarti graph tidak terhubung.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'menulis Kruskal + Union-Find di C++', 'desc' => 'Program lengkap: struct sisi, sort dengan lambda, find, dan unite.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose">
        <p><strong>Soal contoh:</strong> graph tak berarah berbobot. Cetak total bobot MST dan sisi-sisi yang dipilih (sesuai urutan dipilih), atau <code>-1</code> jika graph tidak terhubung.</p>
    </div>
    @include('lessons.walkthrough', [
        'title' => 'Kruskal + Union-Find',
        'steps' => $mstSteps,
        'sample' => ['input' => "7 12\n1 2 4\n1 3 3\n2 3 5\n2 4 6\n3 4 7\n2 5 9\n4 5 2\n4 6 8\n3 6 10\n5 7 5\n6 7 3\n4 7 6\n", 'output' => "23\n4 5 2\n1 3 3\n6 7 3\n1 2 4\n5 7 5\n2 4 6\n"],
        'js' => $mstJs,
        'py' => $mstPy,
    ])
</section>

<section class="lesson-section" id="pola" data-toc="Pola Soal">
    <h2>Pola Soal yang Sering Muncul</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Biaya minimum</h4><p>"Sambungkan semua kota/lab semurah mungkin". Kruskal langsung.</p></div>
        <div class="pattern"><h4>Keuntungan maksimum</h4><p>Maximum spanning tree: urutkan sisi dari yang <b>terbesar</b>.</p></div>
        <div class="pattern"><h4>Gabung & tanya</h4><p>"Apakah A dan B satu grup?" setelah serangkaian penggabungan. Cukup DSU, tanpa MST.</p></div>
        <div class="pattern"><h4>Banyak kelompok</h4><p>Mulai dari N, kurangi 1 setiap <code>unite</code> berhasil.</p></div>
        <div class="pattern"><h4>Sudah ada kabel</h4><p>Beberapa sisi sudah terpasang gratis: <code>unite</code> dulu sisi-sisi itu, baru Kruskal.</p></div>
        <div class="pattern"><h4>Bottleneck</h4><p>"Minimalkan bobot sisi terbesar di rute": jawabannya ada di sepanjang MST.</p></div>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Bagian</th><th>Waktu</th><th>Keterangan</th></tr>
        <tr><td>Mengurutkan sisi</td><td><code>O(M log M)</code></td><td>Bagian paling mahal dari Kruskal.</td></tr>
        <tr><td>Operasi DSU</td><td><code>≈ O(1)</code></td><td>Tepatnya O(α(N)), α tumbuh sangat lambat (≤ 4 untuk N sepraktis apa pun).</td></tr>
        <tr><td>Prim (heap)</td><td><code>O(M log M)</code></td><td>Alternatif, cocok dengan adjacency list.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa path compression</strong> bisa membuat <code>find</code> berjalan menyusuri rantai panjang dan program menjadi O(N) per operasi.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>MST tidak sama dengan jalur terpendek.</strong> MST meminimalkan total kabel untuk menyambungkan <em>semua</em> simpul; rute dari A ke B di dalam MST belum tentu rute terpendek.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'MST untuk soal sulit', 'desc' => 'Mengapa serakah itu benar, algoritma Prim, dan DSU untuk keterhubungan dinamis.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Kruskal Benar?">
    <h2>Mengapa Serakah Itu Benar? (Cut Property)</h2>
    <div class="proof">
        <p><strong>Cut property.</strong> Bagi simpul-simpul menjadi dua kubu sembarang. Di antara semua sisi yang menyeberangi dua kubu itu, sisi yang <strong>termurah</strong> pasti termasuk dalam suatu MST.</p>
        <p><strong>Bukti.</strong> Ambil MST mana pun yang tidak memuat sisi termurah e itu. Tambahkan e: terbentuk siklus yang pasti menyeberangi kubu di sisi lain melalui sisi f. Karena e termurah, <code>w(e) ≤ w(f)</code>. Tukar f dengan e: hasilnya tetap spanning tree dan totalnya tidak bertambah.</p>
        <p><strong>Kaitannya dengan Kruskal.</strong> Saat Kruskal mengambil sisi u–v, pandang kelompok u sebagai satu kubu. Semua sisi lebih murah sudah diperiksa dan tidak ada yang keluar dari kelompok u ke luar (jika ada, pasti sudah diambil). Jadi u–v adalah sisi termurah yang menyeberang, dan aman diambil.</p>
    </div>
</section>

<section class="lesson-section" id="teknik" data-toc="Teknik Lanjutan">
    <h2>Teknik Lanjutan</h2>
    <div class="prose">
        <h3>1. Algoritma Prim</h3>
        <p>Jika graph sudah tersimpan sebagai adjacency list, Prim sering lebih praktis. Mulai dari satu simpul, lalu berulang kali tambahkan sisi termurah yang menghubungkan pohon dengan simpul di luar pohon. Kodenya sangat mirip Dijkstra, hanya kuncinya bobot sisi, bukan jarak total.</p>
    </div>
    @include('lessons.code', ['cpp' => $mstPrim, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>2. DSU untuk keterhubungan dinamis</h3>
        <p>Banyak soal tidak meminta MST, tetapi menanyakan keterhubungan sambil graph terus bertambah: "gabungkan grup a dan b", "apakah a dan b satu grup?". BFS ulang setiap pertanyaan terlalu lambat. Dengan DSU, setiap operasi praktis O(1).</p>
    </div>
    @include('lessons.code', ['cpp' => $mstDsu, 'js' => null, 'py' => null])
    <div class="prose">
        <h3>3. Variasi DSU</h3>
        <ul>
            <li><strong>Ukuran kelompok</strong>: sudah tersedia di <code>sz[find(x)]</code>.</li>
            <li><strong>Kelompok terbesar</strong>: perbarui maksimum setiap kali <code>unite</code> berhasil.</li>
            <li><strong>Jawab secara offline</strong>: untuk soal "hapus sisi", proses kejadiannya mundur sehingga penghapusan berubah menjadi penggabungan.</li>
        </ul>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Spanning tree adalah pohon yang memuat semua N simpul, dan pohon dengan N simpul selalu punya tepat N − 1 sisi.">
        <p class="quiz-q">Graph dengan 10 simpul. Berapa banyak sisi pada MST-nya (jika terhubung)?</p>
        <div class="quiz-options">
            <button class="quiz-option">10</button>
            <button class="quiz-option">Tergantung bobotnya</button>
            <button class="quiz-option">9</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Jika find(u) == find(v), u dan v sudah tersambung. Menambah sisi u–v hanya membentuk siklus, jadi dilewati.">
        <p class="quiz-q">Kruskal melewati sisi u–v ketika…</p>
        <div class="quiz-options">
            <button class="quiz-option"><code>find(u) == find(v)</code></button>
            <button class="quiz-option">bobotnya lebih besar dari sisi sebelumnya</button>
            <button class="quiz-option">u atau v sudah punya tetangga</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Path compression membuat setiap simpul yang dilewati find langsung menunjuk ke ketua, sehingga pencarian berikutnya sangat singkat.">
        <p class="quiz-q">Apa gunanya <code>par[x] = find(par[x])</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Menggabungkan dua kelompok</button>
            <button class="quiz-option">Memendekkan jalan ke ketua untuk pencarian berikutnya</button>
            <button class="quiz-option">Menghitung ukuran kelompok</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
