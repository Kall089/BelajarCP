@php
    $mfSteps = [
        ['Menyimpan sisi berpasangan', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

struct Sisi {
    int ke;
    long long sisa;   // kapasitas yang masih bisa dipakai
};

int n, m;
vector<Sisi> sisi;          // sisi[e] dan sisi[e ^ 1] selalu berpasangan
vector<vector<int>> adj;    // adj[u] = nomor sisi yang keluar dari u

void tambahSisi(int u, int v, long long kap) {
    adj[u].push_back(sisi.size());
    sisi.push_back({v, kap});
    adj[v].push_back(sisi.size());
    sisi.push_back({u, 0});
}

CPP, <<<'TXT'
<p>Setiap sisi asli u → v disimpan bersama <strong>sisi balik</strong> v → u yang awalnya bersisa 0. Keduanya disimpan berurutan, sehingga nomornya 2k dan 2k + 1. Pasangan sebuah sisi <code>e</code> selalu <code>e ^ 1</code> (XOR 1 menukar 2k ↔ 2k + 1).</p>
<p>Kita hanya menyimpan <strong>sisa kapasitas</strong>, bukan aliran. Aliran pada sisi asli bisa dihitung kembali: kapasitas awal − sisa.</p>
TXT],
        ['Membaca jaringan', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> m;
    adj.assign(n + 1, {});
    for (int i = 0; i < m; i++) {
        int u, v;
        long long kap;
        cin >> u >> v >> kap;
        tambahSisi(u, v, kap);
    }
    int s = 1, t = n;

CPP, <<<'TXT'
<p>Sumber <code>s</code> adalah simpul 1 dan muara <code>t</code> adalah simpul N. Sisi ganda dan sisi dua arah (u → v dan v → u) tidak perlu perlakuan khusus: masing-masing punya pasangan baliknya sendiri.</p>
TXT],
        ['BFS di graph residual', <<<'CPP'
    long long total = 0;
    while (true) {
        vector<int> dariSisi(n + 1, -1);
        vector<bool> dikunjungi(n + 1, false);
        queue<int> q;
        q.push(s);
        dikunjungi[s] = true;
        while (!q.empty() && !dikunjungi[t]) {
            int u = q.front();
            q.pop();
            for (int e : adj[u]) {
                int v = sisi[e].ke;
                if (!dikunjungi[v] && sisi[e].sisa > 0) {
                    dikunjungi[v] = true;
                    dariSisi[v] = e;
                    q.push(v);
                }
            }
        }
        if (!dikunjungi[t]) break;

CPP, <<<'TXT'
<p>BFS biasa, tetapi hanya melewati sisi yang <strong>masih bersisa</strong> (termasuk sisi balik yang sisanya sudah positif). <code>dariSisi[v]</code> mencatat sisi yang dipakai untuk sampai ke v, agar jalurnya bisa ditelusuri mundur dari t.</p>
<p>Jika t tidak terjangkau, tidak ada jalur augmentasi lagi dan aliran sudah maksimum.</p>
<div class="wt-tip">Memakai BFS (jalur dengan sisi paling sedikit) itulah yang membedakan <strong>Edmonds-Karp</strong> dari Ford-Fulkerson biasa. BFS menjamin banyak iterasi paling banyak O(V · E), berapa pun besar kapasitasnya.</div>
TXT],
        ['Mencari bottleneck lalu mengirim aliran', <<<'CPP'
        long long tambah = LLONG_MAX;
        for (int v = t; v != s; v = sisi[dariSisi[v] ^ 1].ke)
            tambah = min(tambah, sisi[dariSisi[v]].sisa);
        for (int v = t; v != s; v = sisi[dariSisi[v] ^ 1].ke) {
            sisi[dariSisi[v]].sisa -= tambah;
            sisi[dariSisi[v] ^ 1].sisa += tambah;
        }
        total += tambah;
    }
    cout << total << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Telusuri jalur dari t mundur ke s. Simpul sebelum v adalah ujung lain sisi <code>dariSisi[v]</code>, yaitu <code>sisi[dariSisi[v] ^ 1].ke</code>.</p>
<ul>
    <li>Putaran pertama mencari sisa terkecil di jalur: <strong>bottleneck</strong>.</li>
    <li>Putaran kedua mengirim aliran sebesar itu: sisa sisi maju berkurang, sisa sisi balik bertambah. Sisi balik inilah "tombol undo" untuk iterasi berikutnya.</li>
</ul>
TXT],
    ];

    $mfPy = <<<'PY'
import sys
from collections import deque
input = sys.stdin.readline

n, m = map(int, input().split())
ke, sisa = [], []
adj = [[] for _ in range(n + 1)]

def tambah_sisi(u, v, kap):
    adj[u].append(len(ke)); ke.append(v); sisa.append(kap)
    adj[v].append(len(ke)); ke.append(u); sisa.append(0)

for _ in range(m):
    u, v, kap = map(int, input().split())
    tambah_sisi(u, v, kap)

s, t = 1, n
total = 0
while True:
    dari = [-1] * (n + 1)
    dikunjungi = [False] * (n + 1)
    dikunjungi[s] = True
    q = deque([s])
    while q and not dikunjungi[t]:
        u = q.popleft()
        for e in adj[u]:
            v = ke[e]
            if not dikunjungi[v] and sisa[e] > 0:
                dikunjungi[v] = True
                dari[v] = e
                q.append(v)
    if not dikunjungi[t]:
        break
    tambah = float("inf")
    v = t
    while v != s:
        tambah = min(tambah, sisa[dari[v]])
        v = ke[dari[v] ^ 1]
    v = t
    while v != s:
        sisa[dari[v]] -= tambah
        sisa[dari[v] ^ 1] += tambah
        v = ke[dari[v] ^ 1]
    total += tambah
print(total)
PY;

    $mfCut = <<<'CPP'
// Setelah aliran maksimum: simpul yang masih terjangkau dari s lewat sisi bersisa > 0
// membentuk daerah S. Sisi ASLI dari S ke luar S adalah sisi-sisi potongan minimum.
vector<bool> diS(n + 1, false);
queue<int> q;
q.push(s);
diS[s] = true;
while (!q.empty()) {
    int u = q.front();
    q.pop();
    for (int e : adj[u])
        if (!diS[sisi[e].ke] && sisi[e].sisa > 0) {
            diS[sisi[e].ke] = true;
            q.push(sisi[e].ke);
        }
}
for (int e = 0; e < (int)sisi.size(); e += 2) {   // sisi asli bernomor genap
    int u = sisi[e ^ 1].ke, v = sisi[e].ke;
    if (diS[u] && !diS[v]) cout << u << " -> " << v << '\n';
}
CPP;

    $mfKuhn = <<<'CPP'
// Pencocokan bipartit maksimum: algoritma Kuhn, O(V · E).
// Simpul kiri 1..L, simpul kanan 1..R. adj[u] = simpul kanan yang boleh dipasangkan dengan u.
int L, R;
vector<vector<int>> adj;
vector<int> pasangan;    // pasangan[v] = simpul kiri pemegang v (0 = masih kosong)
vector<int> tanda;       // tanda[v] == putaran: v sudah dicoba di putaran ini
int putaran = 0;

bool coba(int u) {                        // cari pasangan untuk u (boleh menggeser yang lain)
    for (int v : adj[u]) {
        if (tanda[v] == putaran) continue;
        tanda[v] = putaran;
        if (pasangan[v] == 0 || coba(pasangan[v])) {
            pasangan[v] = u;              // v kosong, atau pemegangnya berhasil pindah
            return true;
        }
    }
    return false;
}

int pencocokanMaksimum() {
    pasangan.assign(R + 1, 0);
    tanda.assign(R + 1, 0);
    int hasil = 0;
    for (int u = 1; u <= L; u++) {
        putaran++;
        if (coba(u)) hasil++;
    }
    return hasil;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menjelaskan jaringan aliran: kapasitas, kekekalan aliran, sumber, dan muara.</li>
        <li>Memahami mengapa cara serakah gagal dan bagaimana <strong>graph residual</strong> dengan sisi balik memperbaikinya.</li>
        <li>Menghitung aliran maksimum dengan <strong>Edmonds-Karp</strong> dan menemukan <strong>potongan minimum</strong>.</li>
        <li>Memodelkan pencocokan bipartit, jalur yang tidak berbagi sisi, dan pembagian dengan kuota sebagai soal aliran.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'jaringan pipa', 'desc' => 'Aliran, kapasitas, dan ide sisi balik.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Jaringan Pipa Air</h2>
    <div class="prose">
        <p>PDAM ingin mengalirkan air dari waduk (<strong>sumber</strong> s) ke kota (<strong>muara</strong> t) lewat jaringan pipa satu arah. Setiap pipa punya <strong>kapasitas</strong>: paling banyak berapa liter per detik yang bisa lewat. Berapa liter per detik paling banyak yang bisa sampai di kota?</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Kapasitas c(u, v)</b><span>Batas atas aliran pada sisi u → v.</span></div>
        <div class="term"><b>Aliran f(u, v)</b><span>Banyak air yang benar-benar lewat: 0 ≤ f ≤ c.</span></div>
        <div class="term"><b>Kekekalan aliran</b><span>Di setiap simpul selain s dan t, air yang masuk = air yang keluar. Tidak ada air yang hilang atau muncul.</span></div>
        <div class="term"><b>Nilai aliran</b><span>Total air yang keluar dari s (sama dengan total yang masuk ke t).</span></div>
    </div>
    @include('lessons.diagram', [
        'w' => 640, 'h' => 300, 'directed' => true,
        'nodes' => [[1, 50, 150, 'green', 's'], [2, 200, 50], [3, 330, 210], [4, 180, 260], [5, 450, 50], [6, 590, 150, 'red', 't']],
        'edges' => [[1, 2, '', 3], [2, 3, '', 2], [3, 6, '', 2], [1, 4, '', 2], [4, 3, '', 2], [2, 5, '', 2], [5, 6, '', 3]],
        'caption' => 'Jaringan contoh. Angka pada sisi adalah kapasitas. Aliran maksimumnya 4.',
    ])
</section>

<section class="lesson-section" id="serakah" data-toc="Mengapa Serakah Gagal">
    <h2>Mengapa Cara Serakah Gagal?</h2>
    <div class="prose">
        <p>Ide pertama: cari jalur dari s ke t yang masih ada sisa kapasitasnya, kirim sebanyak mungkin, ulangi sampai tidak ada jalur lagi. Pada jaringan di atas:</p>
        <ol>
            <li>Jalur s → 2 → 3 → t, kirim 2 (sisi 2 → 3 dan 3 → t penuh).</li>
            <li>Jalur s → 2 → 5 → t, kirim 1 (sisi s → 2 penuh).</li>
            <li>Dari s hanya tersisa s → 4 → 3, tetapi 3 → t sudah penuh. Berhenti dengan total <strong>3</strong>.</li>
        </ol>
        <p>Padahal 4 bisa dicapai: kirim s → 2 → 5 → t sebanyak 2, s → 2 → 3 → t sebanyak 1, dan s → 4 → 3 → t sebanyak 1. Kesalahan serakah ada di langkah 1: terlalu banyak aliran memakai 2 → 3. Kita butuh cara untuk <strong>membatalkan</strong> keputusan lama.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p><strong>Sisi balik.</strong> Setiap kali f unit mengalir di u → v, buat "sisi balik" v → u dengan sisa f. Melewati sisi balik berarti mengurangi aliran u → v, yaitu mengalihkan air yang tadinya lewat u → v ke rute lain. Dengan sisi balik, langkah 3 menemukan s → 4 → 3 → 2 → 5 → t dan total menjadi 4.</p>
    </div>
</section>

<section class="lesson-section" id="residual" data-toc="Graph Residual">
    <h2>Graph Residual</h2>
    <div class="prose">
        <p><strong>Graph residual</strong> berisi semua "gerakan" yang masih mungkin. Untuk setiap sisi asli u → v dengan kapasitas c dan aliran f:</p>
    </div>
    <table class="cx-table">
        <tr><th>Sisi residual</th><th>Sisa</th><th>Artinya</th></tr>
        <tr><td>u → v (maju)</td><td><code>c − f</code></td><td>Masih bisa menambah aliran sebanyak ini.</td></tr>
        <tr><td>v → u (balik)</td><td><code>f</code></td><td>Bisa membatalkan aliran yang sudah lewat, paling banyak f.</td></tr>
    </table>
    <div class="prose">
        <p>Jalur dari s ke t di graph residual disebut <strong>jalur augmentasi</strong>. Selama jalur seperti itu ada, aliran bisa ditambah sebesar sisa terkecil di jalur itu (<strong>bottleneck</strong>). Metode Ford-Fulkerson: ulangi sampai tidak ada jalur augmentasi. Edmonds-Karp: pilih jalur augmentasi dengan BFS.</p>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Pada preset "Butuh sisi balik", jalur ketiga berjalan dari 3 ke 2 melawan arah sisi 2 → 3 (garis biru putus-putus), membatalkan satu unit aliran di sana. Di akhir, daerah hijau dan sisi merah menunjukkan potongan minimum.</p></div>
    <div data-viz="flow"></div>
</section>

<section class="lesson-section" id="cara" data-toc="Cara Pengerjaan">
    <h2>Cara Pengerjaan</h2>
    <div class="steps">
        <div class="step-card"><b>Bangun residual</b><span>Setiap sisi u → v (kapasitas c) disimpan bersama sisi balik v → u (sisa 0).</span></div>
        <div class="step-card"><b>BFS</b><span>Cari jalur s → t yang hanya memakai sisi bersisa &gt; 0.</span></div>
        <div class="step-card"><b>Bottleneck</b><span>Sisa terkecil di jalur itu.</span></div>
        <div class="step-card"><b>Kirim</b><span>Sisi maju dikurangi, sisi balik ditambah. Ulangi sampai BFS gagal.</span></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Edmonds-Karp dengan sisi berpasangan e dan e ^ 1.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Input: N simpul, M sisi berarah <code>u v kapasitas</code>. Output: aliran maksimum dari simpul 1 ke simpul N. Contohnya jaringan klasik dari buku <em>Introduction to Algorithms</em>.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Aliran maksimum (Edmonds-Karp)',
        'steps' => $mfSteps,
        'sample' => ['input' => "6 10\n1 2 16\n1 3 13\n2 3 10\n3 2 4\n2 4 12\n4 3 9\n3 5 14\n5 4 7\n4 6 20\n5 6 4\n", 'output' => "23\n"],
        'py' => $mfPy,
    ])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Algoritma</th><th>Waktu</th><th>Kapan dipakai</th></tr>
        <tr><td>Ford-Fulkerson (DFS)</td><td><code>O(E · F)</code></td><td>F = nilai aliran. Cepat jika aliran kecil (misalnya kapasitas 1).</td></tr>
        <tr><td>Edmonds-Karp (BFS)</td><td><code>O(V · E²)</code></td><td>Tidak bergantung kapasitas. Cukup untuk V ≈ 500, E ≈ 5 000 dalam praktik.</td></tr>
        <tr><td>Dinic</td><td><code>O(V² · E)</code>, <code>O(E √V)</code> untuk pencocokan</td><td>Graph lebih besar. Pengembangan dari ide yang sama (BFS bertingkat + DFS).</td></tr>
        <tr><td>Kuhn (pencocokan)</td><td><code>O(V · E)</code></td><td>Khusus graph bipartit, kodenya sangat pendek.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa sisi balik</strong> membuat algoritma menjadi serakah dan jawabannya bisa terlalu kecil, seperti contoh 3 vs 4 di atas.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Sisi tak berarah</strong> u – v berkapasitas c: tambahkan u → v berkapasitas c <em>dan</em> sisi baliknya juga berkapasitas c (bukan 0). Air boleh mengalir ke salah satu arah.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> Total aliran bisa melebihi 2 · 10<sup>9</sup>; simpan sisa dan total dalam <code>long long</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'potongan minimum & pencocokan', 'desc' => 'Teorema max-flow min-cut, Kuhn, dan teknik pemodelan.'])

<section class="lesson-section" id="mincut" data-toc="Potongan Minimum">
    <h2>Aliran Maksimum = Potongan Minimum</h2>
    <div class="prose">
        <p>Sebuah <strong>potongan</strong> (cut) membagi simpul menjadi dua kelompok: S yang memuat s dan T yang memuat t. Kapasitas potongan = jumlah kapasitas sisi dari S ke T. Semua air harus menyeberang dari S ke T, jadi <em>setiap</em> aliran ≤ <em>setiap</em> potongan.</p>
        <p><strong>Teorema max-flow min-cut</strong>: nilai aliran maksimum <em>sama dengan</em> kapasitas potongan minimum. Ketika Edmonds-Karp berhenti, ambil S = simpul yang masih terjangkau dari s di graph residual. Setiap sisi asli dari S ke T pasti penuh (kalau tidak, ujungnya terjangkau), dan setiap sisi dari T ke S pasti kosong (kalau tidak, sisi baliknya terjangkau). Maka aliran = kapasitas potongan ini, sehingga keduanya optimal.</p>
    </div>
    @include('lessons.code', ['cpp' => $mfCut, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Soal berbunyi "hapus sisi dengan biaya total minimum agar s dan t terputus" adalah soal potongan minimum: jawabannya sama dengan aliran maksimum dengan biaya sebagai kapasitas.</p>
    </div>
</section>

<section class="lesson-section" id="matching" data-toc="Pencocokan Bipartit">
    <h2>Pencocokan Bipartit</h2>
    <div class="prose">
        <p>Ada L pelamar dan R lowongan. Setiap pelamar hanya cocok dengan beberapa lowongan, setiap pelamar paling banyak mendapat satu lowongan, dan setiap lowongan paling banyak diisi satu orang. Berapa pasangan paling banyak?</p>
        <p>Sebagai aliran: sumber → setiap pelamar (kapasitas 1), pelamar → lowongan yang cocok (1), setiap lowongan → muara (1). Aliran maksimum = pencocokan maksimum. Algoritma <strong>Kuhn</strong> melakukan hal yang sama dengan lebih ringkas: untuk setiap pelamar, cari lowongan kosong; jika semua penuh, coba "geser" pemegang lowongan ke lowongan lain secara rekursif (itulah jalur augmentasi).</p>
    </div>
    @include('lessons.diagram', [
        'w' => 600, 'h' => 250, 'directed' => true,
        'nodes' => [[1, 40, 125, 'green', 's'], [2, 190, 50, '', 'A'], [3, 190, 125, '', 'B'], [4, 190, 200, '', 'C'], [5, 400, 50, 'vis', 'x'], [6, 400, 125, 'vis', 'y'], [7, 400, 200, 'vis', 'z'], [8, 560, 125, 'red', 't']],
        'edges' => [[1, 2, '', 1], [1, 3, '', 1], [1, 4, '', 1], [2, 5, 'hl'], [2, 6], [3, 5], [4, 6, 'hl'], [4, 7], [5, 8, '', 1], [6, 8, '', 1], [7, 8, '', 1]],
        'caption' => 'Pelamar A, B, C dan lowongan x, y, z, semua kapasitas 1. Garis ungu adalah pasangan awal. Jika A memegang x, maka B (yang hanya cocok dengan x) tidak kebagian. Kuhn memindahkan A ke y dan C ke z sehingga ketiganya terisi.',
    ])
    @include('lessons.code', ['cpp' => $mfKuhn, 'js' => null, 'py' => null])
    <div class="prose">
        <p><strong>Teorema König</strong>: pada graph bipartit, ukuran pencocokan maksimum = ukuran <em>vertex cover</em> minimum (simpul paling sedikit yang menyentuh semua sisi). Soal "pilih baris/kolom paling sedikit untuk menutup semua sel bertanda" adalah soal pencocokan.</p>
    </div>
</section>

<section class="lesson-section" id="pemodelan" data-toc="Teknik Pemodelan">
    <h2>Teknik Pemodelan</h2>
    <div class="prose"><p>Bagian tersulit soal aliran adalah membangun jaringannya. Algoritmanya selalu sama.</p></div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Banyak sumber / muara</h4><p>Tambahkan super-sumber S* dengan sisi ke semua sumber, dan super-muara T* dari semua muara.</p></div>
        <div class="pattern"><h4>Kapasitas simpul</h4><p>Pecah simpul v menjadi v<sub>masuk</sub> → v<sub>keluar</sub> berkapasitas c(v). Semua sisi masuk ke v<sub>masuk</sub>, semua sisi keluar dari v<sub>keluar</sub>.</p></div>
        <div class="pattern"><h4>Jalur tak berbagi sisi</h4><p>Kapasitas setiap sisi 1. Aliran maksimum = banyak jalur s → t yang tidak memakai sisi yang sama (teorema Menger) = sisi minimum yang harus diputus.</p></div>
        <div class="pattern"><h4>Kuota</h4><p>Lowongan yang menerima k orang: sisi lowongan → muara berkapasitas k.</p></div>
        <div class="pattern"><h4>Penugasan jadwal</h4><p>Pekerja → hari → tugas, kapasitas sesuai batas per hari. Cek apakah aliran = total kebutuhan.</p></div>
        <div class="pattern"><h4>Pilih proyek</h4><p>Untung dari proyek, biaya dari alat: jawaban = total untung − potongan minimum (project selection).</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Sisi balik memungkinkan aliran yang sudah dikirim dibatalkan atau dialihkan. Tanpa itu, pilihan jalur yang buruk di awal tidak bisa diperbaiki.">
        <p class="quiz-q">Apa fungsi sisi balik di graph residual?</p>
        <div class="quiz-options">
            <button class="quiz-option">Menggandakan kapasitas sisi</button>
            <button class="quiz-option">Membatalkan atau mengalihkan aliran yang sudah dikirim</button>
            <button class="quiz-option">Mencegah siklus</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Setiap aliran harus menyeberangi setiap potongan, jadi aliran ≤ potongan. Teorema max-flow min-cut menyatakan nilai maksimum aliran sama dengan potongan minimum.">
        <p class="quiz-q">Aliran maksimum sebuah jaringan adalah 17. Berapa kapasitas potongan minimumnya?</p>
        <div class="quiz-options">
            <button class="quiz-option">Bisa lebih kecil dari 17</button>
            <button class="quiz-option">Selalu lebih besar dari 17</button>
            <button class="quiz-option">Tepat 17</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Setiap sisi (pelamar, lowongan) berkapasitas 1, begitu juga sisi dari sumber dan ke muara, sehingga setiap pelamar dan lowongan dipakai paling banyak sekali.">
        <p class="quiz-q">Pada pemodelan pencocokan bipartit sebagai aliran, kapasitas sisi sumber → pelamar adalah …</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">Banyak lowongan yang cocok</button>
            <button class="quiz-option">Tak terhingga</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
