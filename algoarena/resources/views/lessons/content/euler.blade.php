@php
    $euSteps = [
        ['Membaca graph beserta nomor sisinya', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<pair<int, int>>> adj(n + 1);   // {tetangga, nomor sisi}
    vector<int> deg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back({v, i});
        adj[v].push_back({u, i});
        deg[u]++;
        deg[v]++;
    }

CPP, <<<'TXT'
<p>Setiap sisi diberi <strong>nomor</strong> <code>i</code>. Pada graph tak berarah, sisi u–v muncul dua kali (di <code>adj[u]</code> dan <code>adj[v]</code>). Nomor sisi membuat kita bisa menandai sisi itu "sudah dilewati" sekali saja, dari arah mana pun. Ini juga membuat sisi ganda dan self-loop tertangani dengan benar.</p>
TXT],
        ['Memeriksa syarat derajat', <<<'CPP'
    vector<int> ganjil;
    for (int v = 1; v <= n; v++)
        if (deg[v] % 2 == 1) ganjil.push_back(v);
    if (ganjil.size() != 0 && ganjil.size() != 2) {
        cout << "TIDAK ADA\n";
        return 0;
    }

CPP, <<<'TXT'
<p>Kumpulkan simpul berderajat ganjil. Jalur Euler hanya mungkin jika banyaknya <strong>0</strong> (sirkuit: kembali ke titik awal) atau <strong>2</strong> (jalur dari satu simpul ganjil ke simpul ganjil lainnya).</p>
<div class="wt-tip">Jangan lewati pemeriksaan ini. Hierholzer yang dijalankan dari titik awal yang salah tetap menghasilkan urutan sepanjang M + 1, tetapi urutan itu <em>bukan</em> jalur yang sah.</div>
TXT],
        ['Memilih titik awal', <<<'CPP'
    int start = 1;
    if (!ganjil.empty()) start = ganjil[0];
    else
        while (start < n && deg[start] == 0) start++;

    for (int v = 1; v <= n; v++) sort(adj[v].begin(), adj[v].end());

CPP, <<<'TXT'
<p>Jika ada dua simpul ganjil, jalur <em>harus</em> dimulai di salah satunya; kita pilih yang lebih kecil. Jika semua genap, mulai dari simpul pertama yang punya sisi.</p>
<p>Tetangga diurutkan agar Hierholzer selalu mencoba tetangga terkecil lebih dulu. Hasilnya: jalur Euler yang <strong>terkecil secara leksikografis</strong> dari titik awal itu, dan output yang selalu sama.</p>
TXT],
        ['Hierholzer dengan tumpukan', <<<'CPP'
    vector<bool> dipakai(m, false);
    vector<int> ptr(n + 1, 0);
    vector<int> tumpukan = {start}, rute;
    while (!tumpukan.empty()) {
        int u = tumpukan.back();
        while (ptr[u] < (int)adj[u].size() && dipakai[adj[u][ptr[u]].second]) ptr[u]++;
        if (ptr[u] == (int)adj[u].size()) {
            rute.push_back(u);
            tumpukan.pop_back();
        } else {
            int v = adj[u][ptr[u]].first;
            dipakai[adj[u][ptr[u]].second] = true;
            tumpukan.push_back(v);
        }
    }

CPP, <<<'TXT'
<p>Lihat simpul <code>u</code> di puncak tumpukan:</p>
<ul>
    <li>Jika u masih punya sisi yang belum dipakai: lewati sisi itu, tandai, dan dorong tetangganya ke tumpukan. Kita terus berjalan sampai buntu.</li>
    <li>Jika u <strong>buntu</strong> (semua sisinya sudah dipakai): u sudah pasti berada di posisi itu pada rute akhir. Pindahkan u ke <code>rute</code>.</li>
</ul>
<p><code>ptr[u]</code> mengingat sampai mana daftar tetangga u sudah diperiksa, sehingga setiap sisi diperiksa O(1) kali secara total.</p>
TXT],
        ['Memeriksa keterhubungan lalu mencetak', <<<'CPP'
    if ((int)rute.size() != m + 1) {
        cout << "TIDAK ADA\n";
        return 0;
    }
    reverse(rute.begin(), rute.end());
    cout << (ganjil.empty() ? "SIRKUIT" : "JALUR") << '\n';
    for (int i = 0; i <= m; i++) cout << rute[i] << (i < m ? ' ' : '\n');
    return 0;
}
CPP, <<<'TXT'
<p>Rute yang sah melewati M sisi, jadi berisi tepat <code>M + 1</code> simpul. Jika lebih pendek, ada sisi di komponen lain yang tidak terjangkau dari titik awal: graph tidak terhubung, tidak ada jalur Euler.</p>
<p><code>rute</code> tersusun dari belakang (simpul yang buntu lebih dulu adalah ujung rute), jadi dibalik sebelum dicetak.</p>
TXT],
    ];

    $euPy = <<<'PY'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
adj = [[] for _ in range(n + 1)]
deg = [0] * (n + 1)
for i in range(m):
    u, v = map(int, input().split())
    adj[u].append((v, i))
    adj[v].append((u, i))
    deg[u] += 1
    deg[v] += 1

ganjil = [v for v in range(1, n + 1) if deg[v] % 2 == 1]
if len(ganjil) not in (0, 2):
    print("TIDAK ADA")
    sys.exit()
start = ganjil[0] if ganjil else next((v for v in range(1, n + 1) if deg[v] > 0), 1)
for l in adj:
    l.sort()

dipakai = [False] * m
ptr = [0] * (n + 1)
tumpukan, rute = [start], []
while tumpukan:
    u = tumpukan[-1]
    while ptr[u] < len(adj[u]) and dipakai[adj[u][ptr[u]][1]]:
        ptr[u] += 1
    if ptr[u] == len(adj[u]):
        rute.append(u)
        tumpukan.pop()
    else:
        v, i = adj[u][ptr[u]]
        dipakai[i] = True
        tumpukan.append(v)

if len(rute) != m + 1:
    print("TIDAK ADA")
else:
    print("JALUR" if ganjil else "SIRKUIT")
    print(*rute[::-1])
PY;

    $euDirected = <<<'CPP'
// Graph BERARAH: sisi u -> v hanya masuk ke adj[u], tidak perlu nomor sisi.
vector<int> masuk(n + 1, 0), keluar(n + 1, 0);
for (int i = 0; i < m; i++) {
    int u, v;
    cin >> u >> v;
    adj[u].push_back(v);
    keluar[u]++;
    masuk[v]++;
}
// Syarat: semua simpul masuk == keluar (sirkuit), atau tepat satu simpul
// dengan keluar - masuk = 1 (awal) dan satu dengan masuk - keluar = 1 (akhir).
int start = 1, awal = 0, akhir = 0;
bool bisa = true;
for (int v = 1; v <= n; v++) {
    int d = keluar[v] - masuk[v];
    if (d == 1) { awal++; start = v; }
    else if (d == -1) akhir++;
    else if (d != 0) bisa = false;
}
if (!(awal == 0 && akhir == 0) && !(awal == 1 && akhir == 1)) bisa = false;
if (awal == 0) while (start < n && keluar[start] == 0) start++;

// Hierholzer: setiap sisi hanya ada sekali, jadi cukup ptr[u]++.
vector<int> ptr(n + 1, 0), tumpukan = {start}, rute;
while (!tumpukan.empty()) {
    int u = tumpukan.back();
    if (ptr[u] < (int)adj[u].size()) tumpukan.push_back(adj[u][ptr[u]++]);
    else { rute.push_back(u); tumpukan.pop_back(); }
}
// tetap periksa rute.size() == m + 1, lalu balik
CPP;

    $euDeBruijn = <<<'CPP'
// Barisan De Bruijn: string TERPENDEK yang memuat setiap kombinasi n digit (0..k-1)
// sebagai potongan berurutan. Contoh n = 3, k = 2: "0001011100" memuat 000, 001, ..., 111.
// Simpul = (n-1) digit terakhir, sisi = menambah satu digit d. Setiap simpul punya
// masuk = keluar = k, jadi ada sirkuit Euler yang melewati semua k^n sisi.
int n, k, V;
vector<int> ptr;
string hasil;

void dfs(int u) {                    // u = (n-1) digit terakhir sebagai bilangan basis k
    while (ptr[u] < k) {
        int d = ptr[u]++;            // lewati sisi "tambah digit d"
        dfs((u * k + d) % V);
        hasil += char('0' + d);      // dicatat saat kembali (seperti rute Hierholzer)
    }
}

int main() {
    cin >> n >> k;
    V = 1;
    for (int i = 0; i < n - 1; i++) V *= k;
    ptr.assign(V, 0);
    dfs(0);
    reverse(hasil.begin(), hasil.end());
    cout << string(n - 1, '0') + hasil << '\n';   // panjang k^n + n - 1
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menentukan apakah sebuah graph punya <strong>jalur</strong> atau <strong>sirkuit Euler</strong> hanya dari derajat simpulnya.</li>
        <li>Membangun jalur Euler dalam O(N + M) dengan <strong>algoritma Hierholzer</strong> tanpa rekursi.</li>
        <li>Menangani graph berarah, sisi ganda, self-loop, dan graph yang tidak terhubung.</li>
        <li>Mengenali soal yang "menyamar" sebagai jalur Euler: rangkaian kata, domino, dan barisan De Bruijn.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'kapan jalur Euler ada?', 'desc' => 'Cerita Königsberg, syarat derajat, dan ide Hierholzer.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Tujuh Jembatan Königsberg</h2>
    <div class="prose">
        <p>Kota Königsberg dibelah sungai menjadi empat daratan yang dihubungkan tujuh jembatan. Warga bertanya-tanya: bisakah kita berjalan-jalan melewati <strong>setiap jembatan tepat sekali</strong>? Tahun 1736 Leonhard Euler membuktikan bahwa itu mustahil, dan buktinya dianggap sebagai awal lahirnya teori graph.</p>
        <p>Ubah daratan menjadi simpul dan jembatan menjadi sisi. Pertanyaannya menjadi: adakah jalan yang melewati setiap <em>sisi</em> tepat sekali? Simpul boleh dikunjungi berkali-kali.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 560, 'h' => 260,
        'nodes' => [[1, 280, 40, 'red', 'A'], [2, 140, 140, 'red', 'C'], [3, 280, 225, 'red', 'B'], [4, 470, 140, 'red', 'D']],
        'edges' => [[1, 2, '', null, -22], [1, 2, '', null, 22], [3, 2, '', null, -22], [3, 2, '', null, 22], [1, 4], [3, 4], [2, 4]],
        'badges' => [1 => 'd=3', 2 => 'd=5', 3 => 'd=3', 4 => 'd=3'],
        'caption' => 'Königsberg sebagai graph: daratan A, B, C, D dan tujuh jembatan (ada sisi ganda). Keempat simpul berderajat ganjil, jadi tidak ada jalan yang melewati setiap jembatan tepat sekali.',
    ])
    <div class="term-grid">
        <div class="term"><b>Jalur Euler</b><span>Jalan yang melewati setiap sisi tepat sekali. Titik awal dan akhir boleh berbeda.</span></div>
        <div class="term"><b>Sirkuit Euler</b><span>Jalur Euler yang kembali ke titik awal.</span></div>
        <div class="term"><b>Derajat</b><span>Banyak ujung sisi yang menempel pada simpul. Self-loop menyumbang 2.</span></div>
        <div class="term"><b>Bedanya dengan Hamilton</b><span>Jalur Hamilton mengunjungi setiap <em>simpul</em> sekali dan tergolong sulit (DP bitmask). Jalur Euler tentang <em>sisi</em> dan bisa diselesaikan dalam waktu linear.</span></div>
    </div>
</section>

<section class="lesson-section" id="syarat" data-toc="Syarat Keberadaan">
    <h2>Syarat Keberadaan</h2>
    <div class="prose">
        <p>Setiap kali jalur <em>melewati</em> sebuah simpul (masuk lalu keluar lagi), ia memakai <strong>dua</strong> sisi simpul itu. Jadi untuk simpul yang hanya dilewati, derajatnya pasti genap. Hanya titik awal (keluar sekali lebih banyak) dan titik akhir (masuk sekali lebih banyak) yang boleh berderajat ganjil, dan jika awal = akhir, keduanya saling melengkapi sehingga semua genap.</p>
    </div>
    <table class="cx-table">
        <tr><th>Graph</th><th>Sirkuit Euler</th><th>Jalur Euler (awal ≠ akhir)</th></tr>
        <tr><td>Tak berarah</td><td>Semua derajat genap</td><td>Tepat 2 simpul berderajat ganjil: itulah awal dan akhir</td></tr>
        <tr><td>Berarah</td><td>Setiap simpul: masuk = keluar</td><td>Satu simpul keluar − masuk = 1 (awal), satu simpul masuk − keluar = 1 (akhir), sisanya seimbang</td></tr>
    </table>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Selain syarat derajat, semua sisi harus berada di <strong>satu komponen terhubung</strong> (simpul tanpa sisi boleh diabaikan). Euler membuktikan kedua syarat ini juga <em>cukup</em>: jika terpenuhi, jalur Euler pasti ada.</p>
    </div>
    <div class="prose">
        <p>Mengapa banyak simpul ganjil tidak mungkin 1 atau 3? Jumlah semua derajat = 2M (setiap sisi punya dua ujung), selalu genap. Maka banyak simpul berderajat ganjil selalu <strong>genap</strong> (handshaking lemma). Pilihannya hanya 0, 2, 4, …, dan hanya 0 atau 2 yang memungkinkan jalur Euler.</p>
    </div>
</section>

<section class="lesson-section" id="ide" data-toc="Ide Hierholzer">
    <h2>Ide Hierholzer: Jalan Terus, Sisipkan Belakangan</h2>
    <div class="prose">
        <p>Cara naif: berjalan dari titik awal dan memilih sisi sembarang. Masalahnya, kita bisa "terjebak" kembali ke titik awal sementara masih ada sisi yang belum dilewati, misalnya putaran kecil yang menggantung di tengah jalan.</p>
        <p>Hierholzer menyelesaikannya dengan elegan. Berjalanlah sampai buntu. Simpul tempat kita buntu pasti merupakan <em>ujung</em> dari bagian rute yang tersisa, jadi catat simpul itu dan mundur satu langkah. Jika di simpul sebelumnya masih ada sisi, berjalanlah lagi dari sana: putaran baru itu otomatis "disisipkan" ke rute. Dengan tumpukan, semua ini berjalan tanpa rekursi.</p>
    </div>
    @include('lessons.diagram', [
        'w' => 560, 'h' => 240,
        'nodes' => [[1, 70, 50], [2, 70, 190], [3, 280, 120, 'cur'], [4, 490, 50], [5, 490, 190]],
        'edges' => [[1, 2, 'hl'], [2, 3, 'hl'], [3, 1, 'hl'], [3, 4, 'cy'], [4, 5, 'cy'], [5, 3, 'cy']],
        'caption' => 'Graph "kupu-kupu". Berjalan 1 → 2 → 3 → 1 (ungu) membuat kita buntu di 1 padahal sayap kanan (biru) belum dilewati. Hierholzer mundur ke 3, menelusuri 3 → 4 → 5 → 3, lalu menyisipkannya: 1 → 2 → 3 → 4 → 5 → 3 → 1.',
    ])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Ikuti isi tumpukan dan <code>rute</code>. Simpul masuk ke <code>rute</code> hanya ketika buntu, sehingga <code>rute</code> tersusun dari belakang. Coba preset "Tidak ada": graph K<sub>4</sub> punya empat simpul berderajat 3.</p></div>
    <div data-viz="euler"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Cek derajat</b><span>Hitung simpul ganjil (atau selisih masuk/keluar pada graph berarah). Harus 0 atau 2.</span></div>
        <div class="step-card"><b>Pilih titik awal</b><span>Simpul ganjil (atau simpul dengan keluar − masuk = 1). Jika semua seimbang, simpul mana pun yang punya sisi.</span></div>
        <div class="step-card"><b>Hierholzer</b><span>Puncak tumpukan punya sisi tersisa? Lewati dan dorong. Buntu? Pindahkan ke rute.</span></div>
        <div class="step-card"><b>Cek panjang</b><span>Rute harus berisi M + 1 simpul. Jika kurang, graph tidak terhubung. Balik rute lalu cetak.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Hierholzer iteratif untuk graph tak berarah, lengkap dengan semua pemeriksaan.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Program ini mencetak <code>SIRKUIT</code> atau <code>JALUR</code> beserta urutan simpulnya, atau <code>TIDAK ADA</code>. Contoh input adalah graph "rumah amplop" yang bisa digambar tanpa mengangkat pensil.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Jalur Euler dengan Hierholzer',
        'steps' => $euSteps,
        'sample' => ['input' => "5 8\n1 2\n2 3\n3 4\n4 1\n1 3\n2 4\n3 5\n4 5\n", 'output' => "JALUR\n1 2 3 1 4 3 5 4 2\n"],
        'py' => $euPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: Graph Kupu-kupu</h2>
    <div class="prose"><p>Sisi: 1–2, 2–3, 3–1, 3–4, 4–5, 5–3. Semua derajat genap, mulai dari 1. Tetangga diperiksa dari yang terkecil.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>#</th><th>Puncak</th><th>Aksi</th><th>Tumpukan</th><th>rute</th></tr>
            <tr><td>1</td><td>1</td><td>lewati 1–2</td><td class="q">[1, 2]</td><td>[ ]</td></tr>
            <tr><td>2</td><td>2</td><td>lewati 2–3</td><td class="q">[1, 2, 3]</td><td>[ ]</td></tr>
            <tr><td>3</td><td>3</td><td>lewati 3–1</td><td class="q">[1, 2, 3, 1]</td><td>[ ]</td></tr>
            <tr class="hl"><td>4</td><td>1</td><td>buntu</td><td class="q">[1, 2, 3]</td><td>[1]</td></tr>
            <tr><td>5</td><td>3</td><td>masih ada sisi: lewati 3–4</td><td class="q">[1, 2, 3, 4]</td><td>[1]</td></tr>
            <tr><td>6</td><td>4</td><td>lewati 4–5</td><td class="q">[1, 2, 3, 4, 5]</td><td>[1]</td></tr>
            <tr><td>7</td><td>5</td><td>lewati 5–3</td><td class="q">[1, 2, 3, 4, 5, 3]</td><td>[1]</td></tr>
            <tr><td>8–13</td><td>…</td><td>semua buntu, keluar satu per satu</td><td class="q">[ ]</td><td>[1, 3, 5, 4, 3, 2, 1]</td></tr>
            <tr class="ok"><td></td><td></td><td>balik rute</td><td></td><td><b>1 2 3 4 5 3 1</b></td></tr>
        </table>
    </div>
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Putaran 1 → 2 → 3 → 1 ditemukan lebih dulu, tetapi simpul 1 yang buntu masuk ke <code>rute</code> paling awal, sehingga setelah dibalik ia menjadi <em>ujung</em> rute. Sayap kanan 3 → 4 → 5 → 3 tersisip di tengah. Tanpa membalik, rute dibaca dari akhir ke awal.</p>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Cara</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Coba semua urutan sisi</td><td><code>O(M!)</code></td><td>Hanya untuk M yang sangat kecil.</td></tr>
        <tr><td>Fleury (hindari jembatan)</td><td><code>O(M²)</code></td><td>Setiap langkah mengecek apakah sisi adalah jembatan.</td></tr>
        <tr><td>Hierholzer</td><td><code>O(N + M)</code></td><td>+ O(M log M) jika tetangga diurutkan.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Titik awal yang salah menghasilkan jawaban palsu.</strong> Pada graph berarah 3 → 5, 5 → 5, 5 → 1, 1 → 4, 4 → 4, 1 → 3, Hierholzer dari simpul 3 menghasilkan urutan sepanjang M + 1 yang memuat langkah 4 → 3, padahal sisi itu tidak ada. Selalu periksa syarat derajat <em>sebelum</em> menjalankan Hierholzer.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Rekursi terlalu dalam.</strong> Versi rekursif Hierholzer bisa sedalam M = 2 · 10<sup>5</sup> panggilan dan membuat stack overflow (terutama di Python dan di Windows). Versi tumpukan di atas aman.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa nomor sisi.</strong> Pada graph tak berarah, menandai "sisi u→v sudah dipakai" tanpa menandai arah sebaliknya membuat sisi yang sama dilewati dua kali. Simpan nomor sisi, bukan hanya tetangganya.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'graph berarah & penerapan', 'desc' => 'Versi berarah, bukti kebenaran, dan soal yang menyamar.'])

<section class="lesson-section" id="berarah" data-toc="Graph Berarah">
    <h2>Jalur Euler pada Graph Berarah</h2>
    <div class="prose">
        <p>Algoritmanya sama, hanya syaratnya berubah: bandingkan derajat <strong>masuk</strong> dan <strong>keluar</strong>. Karena setiap sisi hanya tersimpan sekali (di <code>adj[u]</code>), kita tidak perlu nomor sisi: cukup majukan <code>ptr[u]</code>.</p>
        <p>Untuk keterhubungan, cukup periksa panjang rute = M + 1 di akhir. Itu sudah menjamin semua sisi terjangkau dari titik awal.</p>
    </div>
    @include('lessons.code', ['cpp' => $euDirected, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="bukti" data-toc="Mengapa Hierholzer Benar?">
    <h2>Mengapa Hierholzer Benar?</h2>
    <div class="proof">
        <p><strong>Buntu hanya terjadi di tempat yang tepat.</strong> Misalkan syarat derajat terpenuhi. Saat berjalan dari titik awal s, setiap kali kita masuk ke simpul v ≠ akhir, kita memakai satu sisinya dan sisa sisinya menjadi ganjil, jadi masih ada jalan keluar. Akibatnya perjalanan pertama hanya bisa buntu di titik akhir (atau di s untuk sirkuit).</p>
        <p><strong>Setiap simpul buntu adalah ujung bagian rute yang tersisa.</strong> Ketika u dipindahkan ke <code>rute</code>, semua sisinya sudah dipakai, sehingga dalam rute akhir tidak ada langkah yang keluar dari u setelah titik ini. Jika simpul di bawah u di tumpukan masih punya sisi, putaran yang dimulai darinya pasti kembali ke simpul itu (sisa derajatnya genap) dan tersusun tepat sebelum u di <code>rute</code>. Dengan begitu setiap putaran tersisip di posisi yang benar, dan setiap sisi masuk tepat sekali.</p>
        <p><strong>Waktu.</strong> Setiap sisi didorong ke tumpukan sekali dan setiap entri tumpukan dikeluarkan sekali; <code>ptr</code> hanya bergerak maju. Totalnya O(N + M).</p>
    </div>
</section>

<section class="lesson-section" id="penerapan" data-toc="Soal yang Menyamar">
    <h2>Soal yang Menyamar sebagai Jalur Euler</h2>
    <div class="prose">
        <p>Kuncinya: ketika sebuah soal meminta memakai setiap <em>potongan</em> (kata, kartu domino, tiket, pasangan digit) tepat sekali dan potongan itu "menyambung" ujung ke ujung, jadikan potongan sebagai <strong>sisi</strong>, dan ujung sambungannya sebagai <strong>simpul</strong>.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Rangkaian kata</h4><p>Kata "ayam" menyambung ke "mobil" karena huruf akhir = huruf awal. Simpul = 26 huruf, setiap kata = sisi berarah dari huruf pertama ke huruf terakhir.</p></div>
        <div class="pattern"><h4>Domino</h4><p>Kartu [a|b] = sisi tak berarah a–b. Menyusun semua kartu menjadi satu rantai = jalur Euler.</p></div>
        <div class="pattern"><h4>Tiket perjalanan</h4><p>Pakai semua tiket tepat sekali mulai dari kota tertentu. Jika diminta urutan terkecil, urutkan tetangga lalu Hierholzer.</p></div>
        <div class="pattern"><h4>Goresan minimum</h4><p>Berapa kali minimal mengangkat pensil? Setiap komponen butuh max(1, ganjil / 2) goresan.</p></div>
        <div class="pattern"><h4>Tukang pos</h4><p>Lewati setiap jalan minimal sekali dengan jarak terkecil: pasangkan simpul ganjil memakai jarak terpendek, lalu sirkuit Euler.</p></div>
        <div class="pattern"><h4>De Bruijn</h4><p>Kunci kombinasi: string terpendek yang memuat semua kode n digit. Lihat kode di bawah.</p></div>
    </div>
    @include('lessons.code', ['cpp' => $euDeBruijn, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Simpul ganjil ada 2 (simpul 1 dan 4), jadi ada jalur Euler dari 1 ke 4 (atau sebaliknya), tetapi bukan sirkuit.">
        <p class="quiz-q">Derajat simpul 1–5 adalah 3, 2, 4, 1, 2 dan graph terhubung. Apa yang ada?</p>
        <div class="quiz-options">
            <button class="quiz-option">Sirkuit Euler</button>
            <button class="quiz-option">Tidak ada keduanya</button>
            <button class="quiz-option">Jalur Euler, tetapi bukan sirkuit</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Jumlah derajat selalu 2M (genap), sehingga banyak simpul berderajat ganjil pasti genap. Tiga simpul ganjil mustahil.">
        <p class="quiz-q">Mungkinkah sebuah graph tak berarah punya tepat 3 simpul berderajat ganjil?</p>
        <div class="quiz-options">
            <button class="quiz-option">Mungkin, jika tidak terhubung</button>
            <button class="quiz-option">Tidak mungkin</button>
            <button class="quiz-option">Mungkin, jika ada self-loop</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Simpul masuk ke rute ketika buntu, sehingga ujung rute masuk paling awal. Membalik rute memberi urutan dari titik awal.">
        <p class="quiz-q">Mengapa <code>rute</code> pada Hierholzer perlu dibalik?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena simpul ditambahkan saat buntu, jadi ujung rute tercatat lebih dulu</button>
            <button class="quiz-option">Karena tetangga diurutkan menurun</button>
            <button class="quiz-option">Tidak perlu, hanya untuk mempercantik output</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
