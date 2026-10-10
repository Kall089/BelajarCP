@php
    $mcSteps = [
        ['Sisi berpasangan dengan biaya', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

struct Sisi { int ke; long long cap, biaya; };
vector<Sisi> E;                     // sisi ke-id dan sisi baliknya di id ^ 1
vector<vector<int>> adj;

void tambah(int u, int v, long long cap, long long biaya) {
    adj[u].push_back(E.size()); E.push_back({v, cap, biaya});
    adj[v].push_back(E.size()); E.push_back({u, 0, -biaya});   // sisi balik: biaya negatif
}

CPP, <<<'TXT'
<p>Sama seperti aliran maksimum, setiap sisi disimpan berpasangan dengan sisi baliknya, sehingga pasangannya cukup <code>id ^ 1</code>. Bedanya hanya satu: <strong>sisi balik berbiaya −c</strong>. Mengalirkan satu satuan lewat sisi balik berarti membatalkan satu satuan di sisi maju, dan biayanya ikut dikembalikan.</p>
TXT],
        ['SPFA: jalur termurah di graph sisa', <<<'CPP'
const long long INF = LLONG_MAX / 4;
int n;
vector<long long> dist;
vector<int> lewat;                  // id sisi yang dipakai untuk masuk ke simpul

bool spfa(int s, int t) {
    dist.assign(n + 1, INF);
    lewat.assign(n + 1, -1);
    vector<bool> diAntri(n + 1, false);
    deque<int> q;
    dist[s] = 0; q.push_back(s); diAntri[s] = true;
    while (!q.empty()) {
        int u = q.front(); q.pop_front(); diAntri[u] = false;
        for (int id : adj[u]) {
            Sisi& e = E[id];
            if (e.cap > 0 && dist[u] + e.biaya < dist[e.ke]) {
                dist[e.ke] = dist[u] + e.biaya;
                lewat[e.ke] = id;
                if (!diAntri[e.ke]) { diAntri[e.ke] = true; q.push_back(e.ke); }
            }
        }
    }
    return dist[t] < INF;
}

CPP, <<<'TXT'
<p>Karena ada biaya negatif, Dijkstra biasa tidak boleh dipakai. <strong>SPFA</strong> adalah Bellman-Ford dengan antrean: simpul hanya diproses ulang jika jaraknya baru saja membaik. Hanya sisi dengan kapasitas sisa &gt; 0 yang boleh dilewati.</p>
<p><code>lewat[v]</code> menyimpan sisi terakhir menuju v, untuk menelusuri jalurnya kembali.</p>
TXT],
        ['Alirkan sepanjang jalur termurah, ulangi', <<<'CPP'
int main() {
    int m, s, t;
    cin >> n >> m >> s >> t;
    adj.assign(n + 1, {});
    for (int i = 0; i < m; i++) {
        int u, v; long long c, w;
        cin >> u >> v >> c >> w;
        tambah(u, v, c, w);
    }
    long long aliran = 0, biaya = 0;
    while (spfa(s, t)) {
        long long f = INF;                         // kapasitas terkecil di jalur
        for (int v = t; v != s; v = E[lewat[v] ^ 1].ke)
            f = min(f, E[lewat[v]].cap);
        for (int v = t; v != s; v = E[lewat[v] ^ 1].ke) {
            E[lewat[v]].cap -= f;
            E[lewat[v] ^ 1].cap += f;
        }
        aliran += f;
        biaya += f * dist[t];
    }
    cout << aliran << ' ' << biaya << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Selama masih ada jalur dari s ke t, ambil yang <strong>termurah</strong>, alirkan sebanyak kapasitas terkecilnya, dan tambahkan <code>f × dist[t]</code> ke total biaya. Simpul asal sisi <code>lewat[v]</code> adalah tujuan sisi baliknya, <code>E[lewat[v] ^ 1].ke</code>.</p>
<p>Contoh di bawah adalah jaringan penugasan 2 pekerja (simpul 2, 3) dan 2 tugas (simpul 4, 5): iterasi pertama memakai jalur berbiaya 1, iterasi kedua berbiaya 2 dan membatalkan pasangan pertama lewat sisi balik. Hasil: aliran 2, biaya 3.</p>
TXT],
    ];

    $mcPy = <<<'PY'
import sys
from collections import deque

def main():
    data = sys.stdin.buffer.read().split()
    n, m, s, t = (int(x) for x in data[:4])
    ke, cap, biaya = [], [], []
    adj = [[] for _ in range(n + 1)]
    def tambah(u, v, c, w):
        adj[u].append(len(ke)); ke.append(v); cap.append(c); biaya.append(w)
        adj[v].append(len(ke)); ke.append(u); cap.append(0); biaya.append(-w)
    p = 4
    for _ in range(m):
        u, v, c, w = (int(x) for x in data[p:p + 4]); p += 4
        tambah(u, v, c, w)
    INF = float('inf')
    aliran = total = 0
    while True:
        dist = [INF] * (n + 1)
        lewat = [-1] * (n + 1)
        antri = [False] * (n + 1)
        dist[s] = 0
        q = deque([s])
        while q:
            u = q.popleft(); antri[u] = False
            for i in adj[u]:
                if cap[i] > 0 and dist[u] + biaya[i] < dist[ke[i]]:
                    dist[ke[i]] = dist[u] + biaya[i]
                    lewat[ke[i]] = i
                    if not antri[ke[i]]:
                        antri[ke[i]] = True; q.append(ke[i])
        if dist[t] == INF:
            break
        f, v = INF, t
        while v != s:
            f = min(f, cap[lewat[v]]); v = ke[lewat[v] ^ 1]
        v = t
        while v != s:
            cap[lewat[v]] -= f; cap[lewat[v] ^ 1] += f; v = ke[lewat[v] ^ 1]
        aliran += f
        total += f * dist[t]
    print(aliran, total)

main()
PY;

    $mcHung = <<<'CPP'
// Hungarian O(n² m): a[1..n][1..m] dengan n ≤ m, indeks mulai 1.
// Hasil: p[j] = baris yang ditugaskan ke kolom j, biaya minimum = -v[0].
const long long INF = LLONG_MAX / 4;
vector<long long> u(n + 1), v(m + 1);
vector<int> p(m + 1), way(m + 1);
for (int i = 1; i <= n; i++) {
    p[0] = i;
    int j0 = 0;
    vector<long long> minv(m + 1, INF);
    vector<char> used(m + 1, false);
    do {                                         // Dijkstra kecil dengan "harga" u, v
        used[j0] = true;
        int i0 = p[j0], j1 = 0;
        long long delta = INF;
        for (int j = 1; j <= m; j++)
            if (!used[j]) {
                long long cur = a[i0][j] - u[i0] - v[j];
                if (cur < minv[j]) { minv[j] = cur; way[j] = j0; }
                if (minv[j] < delta) { delta = minv[j]; j1 = j; }
            }
        for (int j = 0; j <= m; j++)
            if (used[j]) { u[p[j]] += delta; v[j] -= delta; }
            else minv[j] -= delta;
        j0 = j1;
    } while (p[j0] != 0);
    do { int j1 = way[j0]; p[j0] = p[j1]; j0 = j1; } while (j0);   // balik jalur penambah
}
long long biayaMin = -v[0];
CPP;

    $mcPot = <<<'CPP'
// Dijkstra dengan potensial Johnson: biaya tereduksi c'(u,v) = c(u,v) + h[u] - h[v] ≥ 0.
// h awal = jarak dari SPFA/Bellman-Ford sekali (atau 0 jika semua biaya awal ≥ 0).
// Setelah setiap Dijkstra: h[v] += dist[v] untuk simpul yang terjangkau.
priority_queue<pair<long long,int>, vector<pair<long long,int>>, greater<pair<long long,int>>> pq;
// ... relaksasi memakai E[id].biaya + h[u] - h[E[id].ke]
// biaya jalur asli = dist[t] - h[s] + h[t]
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memodelkan soal dengan <strong>kapasitas dan biaya per satuan</strong> sebagai jaringan aliran berbiaya.</li>
        <li>Menjelaskan mengapa <strong>sisi balik berbiaya negatif</strong> membuat algoritma bisa membatalkan pilihan lama.</li>
        <li>Menulis MCMF dengan <strong>jalur penambah termurah</strong> (SPFA) dan menghitung total biayanya.</li>
        <li>Mengenali soal <strong>penugasan</strong> n × n dan memakai algoritma Hungarian O(n³).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'aliran yang paling murah', 'desc' => 'Dari aliran maksimum ke aliran berbiaya minimum.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Membagi Tugas">
    <h2>Intuisi: Membagi Tugas dengan Ongkos Berbeda</h2>
    <div class="prose">
        <p>Dua kurir, dua pesanan. Ongkos kurir p<sub>1</sub>: 1 ke pesanan t<sub>1</sub>, 2 ke t<sub>2</sub>. Ongkos kurir p<sub>2</sub>: 1 ke t<sub>1</sub>, 9 ke t<sub>2</sub>. Cara rakus mengambil pasangan termurah dulu: p<sub>1</sub>–t<sub>1</sub> (1), lalu p<sub>2</sub> terpaksa ke t<sub>2</sub> (9). Total 10. Padahal p<sub>1</sub>–t<sub>2</sub> dan p<sub>2</sub>–t<sub>1</sub> hanya 2 + 1 = 3.</p>
        <p>Aliran maksimum mencari jalur penambah <em>mana saja</em>. <strong>Aliran biaya minimum</strong> (MCMF, <em>min-cost max-flow</em>) mencari jalur penambah <strong>yang termurah</strong>, dan berkat sisi balik berbiaya negatif, jalur berikutnya boleh "menarik kembali" keputusan sebelumnya.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Biaya per satuan</b><span>Mengalirkan f satuan lewat sisi berbiaya c menambah f · c ke total.</span></div>
        <div class="term"><b>Graph sisa</b><span>Sisi maju dengan kapasitas tersisa, dan sisi balik selebar aliran yang sudah lewat.</span></div>
        <div class="term"><b>Sisi balik −c</b><span>Membatalkan satu satuan aliran juga mengembalikan biayanya.</span></div>
        <div class="term"><b>Jalur termurah</b><span>Jalur s → t di graph sisa dengan jumlah biaya terkecil.</span></div>
    </div>
    <table class="cx-table">
        <tr><th>Strategi</th><th>Pasangan</th><th>Total</th></tr>
        <tr><td>Rakus: termurah dulu</td><td>p<sub>1</sub>–t<sub>1</sub> (1), p<sub>2</sub>–t<sub>2</sub> (9)</td><td>10</td></tr>
        <tr class="ok"><td>MCMF</td><td>p<sub>1</sub>–t<sub>2</sub> (2), p<sub>2</sub>–t<sub>1</sub> (1)</td><td><b>3</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Sisi Balik yang Membatalkan Pilihan</h2>
    <div class="prose"><p>Masukkan matriks biaya (baris dipisah <code>/</code>, maksimal 3 × 3). Angka <em>d</em> di bawah simpul adalah jarak termurah dari S hasil SPFA. Perhatikan iterasi kedua pada contoh 2 × 2: jalurnya melewati sisi balik ungu t<sub>1</sub> → p<sub>1</sub> berbiaya −1.</p></div>
    <div data-viz="mcmf"></div>
</section>

<section class="lesson-section" id="ide" data-toc="Algoritmanya">
    <h2>Algoritmanya: Jalur Terpendek Berturut-turut</h2>
    <div class="steps">
        <div class="step-card"><b>1. Bangun jaringan</b><p>Setiap sisi punya kapasitas dan biaya. Sisi balik: kapasitas 0, biaya −c.</p></div>
        <div class="step-card"><b>2. Cari jalur termurah</b><p>Di graph sisa, hitung jarak terpendek menurut biaya dari s (SPFA, karena ada biaya negatif).</p></div>
        <div class="step-card"><b>3. Alirkan</b><p>Kirim f = kapasitas terkecil di jalur. Biaya bertambah f × dist[t].</p></div>
        <div class="step-card"><b>4. Ulangi</b><p>Sampai t tidak terjangkau. Hasilnya aliran maksimum dengan biaya minimum.</p></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Satu-satunya perubahan dari Ford-Fulkerson: jalur penambah dipilih yang <strong>termurah</strong>, bukan sembarang. Itu cukup untuk menjamin hasil akhirnya optimal.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'MCMF di C++', 'desc' => 'Program lengkap dengan SPFA dan cara memodelkan soal.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Input: <code>n m s t</code>, lalu m sisi berarah <code>u v kapasitas biaya</code>. Output: aliran maksimum dan biaya minimumnya.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Min-cost max-flow dengan SPFA',
        'steps' => $mcSteps,
        'sample' => ['input' => "6 8 1 6\n1 2 1 0\n1 3 1 0\n2 4 1 1\n2 5 1 2\n3 4 1 1\n3 5 1 9\n4 6 1 0\n5 6 1 0\n", 'output' => "2 3\n"],
        'py' => $mcPy,
    ])
    <table class="trace-table">
        <tr><th>iterasi</th><th>jalur termurah</th><th>biaya jalur</th><th>total</th></tr>
        <tr><td>1</td><td>1 → 2 → 4 → 6</td><td>0 + 1 + 0 = 1</td><td>1</td></tr>
        <tr class="hl"><td>2</td><td>1 → 3 → 4 → 2 → 5 → 6</td><td>0 + 1 + (−1) + 2 + 0 = 2</td><td>3</td></tr>
        <tr class="ok"><td>3</td><td>tidak ada jalur</td><td>–</td><td><b>3</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="pemodelan" data-toc="Memodelkan Soal">
    <h2>Memodelkan Soal sebagai Jaringan Berbiaya</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Penugasan</h4><p>S → pekerja (1, 0), pekerja → tugas (1, biaya), tugas → T (1, 0). Aliran n = semua terpasang.</p></div>
        <div class="pattern"><h4>Transportasi</h4><p>S → gudang (stok, 0), gudang → toko (∞, ongkos per unit), toko → T (permintaan, 0).</p></div>
        <div class="pattern"><h4>k jalur saling lepas termurah</h4><p>Kapasitas 1 per sisi, biaya = panjang. Alirkan tepat k satuan. Untuk lepas-simpul, pecah simpul v menjadi v<sub>in</sub> → v<sub>out</sub> berkapasitas 1.</p></div>
        <div class="pattern"><h4>Memaksimalkan keuntungan</h4><p>Pakai biaya = −untung. Jika aliran tidak wajib maksimum, berhenti saat dist[t] ≥ 0.</p></div>
    </div>
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <div class="prose"><p>Setiap iterasi SPFA O(V · E) pada kasus terburuk (biasanya jauh lebih cepat), dan banyak iterasi paling banyak sebesar nilai aliran F. Total O(F · V · E): cukup untuk graph berukuran ratusan simpul dan ribuan sisi.</p></div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Lupa biaya negatif pada sisi balik.</strong> Tanpa −c, algoritma tidak bisa membatalkan keputusan lama dan hasilnya sama dengan rakus yang salah.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Dijkstra tanpa potensial.</strong> Sisi balik berbiaya negatif membuat Dijkstra memberi jarak yang salah. Pakai SPFA, atau Dijkstra dengan potensial Johnson (di bawah).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Aliran tidak wajib maksimum.</strong> Jika soal hanya meminta biaya (atau untung) terbaik, menambah aliran saat dist[t] sudah positif justru memperburuk. Hentikan loop pada saat itu.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'bukti, Hungarian, dan potensial', 'desc' => 'Mengapa jalur termurah berturut-turut optimal, dan alat yang lebih cepat untuk penugasan.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Optimal?">
    <h2>Mengapa Jalur Termurah Berturut-turut Optimal?</h2>
    <div class="proof">
        <p><strong>Kriteria optimal.</strong> Aliran bernilai F berbiaya minimum di antara semua aliran bernilai F jika dan hanya jika graph sisanya <em>tidak punya siklus berbiaya negatif</em>. (Jika ada, alirkan satu satuan mengitari siklus itu: nilai aliran tetap, biaya turun.)</p>
        <p><strong>Invarian.</strong> Awalnya aliran 0 dan graph sisa hanya berisi sisi asli; asumsikan tidak ada siklus negatif di sana. Menambah aliran sepanjang jalur <em>terpendek</em> hanya memunculkan sisi balik di sepanjang jalur itu, dan karena jarak d sudah konsisten (d[v] ≤ d[u] + c untuk setiap sisi sisa), sisi balik baru juga memenuhi d[u] ≤ d[v] − c. Maka tidak ada siklus negatif yang terbentuk. Dengan induksi, setiap nilai aliran yang dicapai algoritma sudah berbiaya minimum.</p>
    </div>
    <div class="prose"><p><strong>Potensial Johnson.</strong> Jarak d dari iterasi sebelumnya bisa dipakai sebagai "harga" simpul h. Biaya tereduksi c + h[u] − h[v] tidak pernah negatif di graph sisa, sehingga Dijkstra kembali sah dan setiap iterasi turun menjadi O(E log V).</p></div>
    @include('lessons.code', ['cpp' => $mcPot, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="hungarian" data-toc="Algoritma Hungarian">
    <h2>Algoritma Hungarian untuk Penugasan</h2>
    <div class="prose">
        <p>Untuk penugasan n pekerja ke n tugas (satu-satu), membangun jaringan MCMF berarti n iterasi SPFA pada n² sisi, terlalu lambat untuk n = 500. <strong>Hungarian</strong> menyelesaikannya dalam O(n³) langsung pada matriks biaya. Ia menjaga harga u[i] untuk setiap baris dan v[j] untuk setiap kolom sehingga a[i][j] − u[i] − v[j] ≥ 0, lalu menambah satu baris setiap putaran lewat jalur penambah di sisi-sisi yang biaya tereduksinya 0.</p>
    </div>
    @include('lessons.code', ['cpp' => $mcHung, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Untuk penugasan yang <strong>memaksimalkan</strong> nilai, negasikan semua biaya (atau pakai M − a[i][j]). Jika jumlah pekerja lebih sedikit dari tugas, Hungarian di atas tetap bekerja selama n ≤ m.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Sisi balik membatalkan aliran yang sudah lewat, jadi biayanya harus dikembalikan: −c.">
        <p class="quiz-q">Sisi u → v berbiaya 7. Berapa biaya sisi baliknya v → u?</p>
        <div class="quiz-options">
            <button class="quiz-option">7</button>
            <button class="quiz-option">−7</button>
            <button class="quiz-option">0</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Graph sisa MCMF berisi sisi berbiaya negatif. Dijkstra biasa mengasumsikan semua bobot non-negatif, jadi jaraknya bisa salah. SPFA (Bellman-Ford) menangani bobot negatif.">
        <p class="quiz-q">Mengapa MCMF dasar memakai SPFA, bukan Dijkstra biasa?</p>
        <div class="quiz-options">
            <button class="quiz-option">SPFA selalu lebih cepat</button>
            <button class="quiz-option">Graph-nya tidak berbobot</button>
            <button class="quiz-option">Ada sisi berbiaya negatif di graph sisa</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Penugasan n × n dengan n = 400 adalah wilayah Hungarian O(n³) ≈ 6,4·10⁷ langkah. MCMF butuh 400 iterasi SPFA pada 160 000 sisi.">
        <p class="quiz-q">400 pekerja, 400 tugas, setiap pasangan punya biaya. Alat yang paling tepat?</p>
        <div class="quiz-options">
            <button class="quiz-option">Hungarian</button>
            <button class="quiz-option">Greedy pasangan termurah</button>
            <button class="quiz-option">Brute force semua permutasi</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
