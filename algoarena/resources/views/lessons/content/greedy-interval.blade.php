@php
    $giSteps = [
        ['Baca balon sebagai interval', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<pair<long long, long long>> b(n);       // (kanan, kiri) agar sort memakai ujung kanan
    for (auto& p : b) cin >> p.second >> p.first;
CPP, <<<'TXT'
<p><strong>Soal contoh "Balon dan Panah":</strong> setiap balon menempati selang [l, r] di sumbu x. Satu panah yang ditembakkan tegak di posisi x memecahkan semua balon dengan l ≤ x ≤ r. Berapa panah minimum untuk memecahkan semua balon?</p>
<p>Pasangan disimpan sebagai (r, l) supaya <code>sort</code> langsung mengurutkan berdasarkan ujung kanan.</p>
TXT],
        ['Urutkan berdasarkan ujung kanan', <<<'CPP'

    sort(b.begin(), b.end());
CPP, <<<'TXT'
<p>Balon yang ujung kanannya paling kecil adalah yang paling "mendesak": panah untuknya harus ditembakkan paling lambat di r-nya.</p>
TXT],
        ['Tembak sedalam mungkin', <<<'CPP'

    int panah = 0;
    long long posisi = LLONG_MIN;              // posisi panah terakhir
    for (auto& p : b) {
        long long r = p.first, l = p.second;
        if (l > posisi) {                      // balon ini belum pecah
            panah++;
            posisi = r;                        // tembak di ujung kanannya
        }
    }
    cout << panah << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Untuk balon pertama yang belum pecah, panah boleh di mana saja dalam [l, r]. Menembak di <strong>r</strong> (sejauh mungkin ke kanan) mengenai semua balon yang juga bisa dikenai di posisi lain mana pun dalam [l, r], karena semua balon tersisa punya ujung kanan ≥ r.</p>
<p>Balon berikutnya sudah pecah jika l ≤ posisi panah terakhir. Total O(n log n).</p>
TXT],
    ];

    $giPy = <<<'PY'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
b = sorted((int(data[2 + 2 * i]), int(data[1 + 2 * i])) for i in range(n))   # (r, l)
panah, posisi = 0, None
for r, l in b:
    if posisi is None or l > posisi:
        panah += 1
        posisi = r
print(panah)
PY;

    $giCover = <<<'CPP'
// Menutup ruas [S, T] dengan interval sesedikit mungkin (urut berdasarkan ujung KIRI)
sort(iv.begin(), iv.end());                    // (l, r)
long long tertutup = S;                        // [S, tertutup] sudah tertutup
int dipakai = 0, i = 0;
while (tertutup < T) {
    long long terjauh = tertutup;
    while (i < n && iv[i].first <= tertutup)   // semua interval yang menyambung
        terjauh = max(terjauh, iv[i++].second);
    if (terjauh == tertutup) { dipakai = -1; break; }   // ada celah: mustahil
    tertutup = terjauh;                        // ambil yang menjangkau paling jauh
    dipakai++;
}
CPP;

    $giMerge = <<<'CPP'
// Menggabungkan interval yang bertumpuk
sort(iv.begin(), iv.end());
vector<pair<long long, long long>> hasil;
for (auto& p : iv) {
    if (!hasil.empty() && p.first <= hasil.back().second)
        hasil.back().second = max(hasil.back().second, p.second);   // perpanjang
    else
        hasil.push_back(p);                                         // mulai blok baru
}
CPP;

    $giRooms = <<<'CPP'
// Ruang minimum = tumpukan terbanyak pada satu waktu (sapuan peristiwa)
vector<pair<long long, int>> ev;
for (auto& p : iv) {
    ev.push_back({p.first, +1});      // rapat mulai
    ev.push_back({p.second, -1});     // rapat selesai (interval setengah terbuka [l, r))
}
sort(ev.begin(), ev.end());           // pada waktu sama, -1 diproses lebih dulu
int aktif = 0, ruang = 0;
for (auto& e : ev) {
    aktif += e.second;
    ruang = max(ruang, aktif);
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memilih <strong>kegiatan terbanyak</strong> tanpa bentrok dengan mengurutkan berdasarkan waktu selesai.</li>
        <li>Mencari <strong>titik penusuk</strong> minimum dan <strong>menutup ruas</strong> dengan interval paling sedikit.</li>
        <li>Menghitung <strong>ruang minimum</strong> (tumpukan maksimum) dengan sapuan peristiwa atau heap.</li>
        <li>Menggabungkan interval yang bertumpuk dan memilih kunci urut yang tepat untuk setiap soal.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'interval di garis waktu', 'desc' => 'Satu soal, satu kunci urut yang tepat.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Jadwal Aula">
    <h2>Intuisi: Mengatur Jadwal Aula</h2>
    <div class="prose">
        <p>Pengelola aula menerima banyak permintaan pemakaian, masing-masing dengan jam mulai dan selesai. Jika aula hanya satu dan ia ingin melayani <em>sebanyak mungkin</em> acara, mana yang dipilih dulu? Acara terpendek? Acara yang mulai paling awal? Ternyata keduanya bisa salah. Kuncinya: pilih acara yang <strong>selesai paling awal</strong>, karena itu menyisakan aula untuk waktu yang paling panjang.</p>
        <p>Soal interval hampir selalu diselesaikan dengan <strong>mengurutkan</strong> lalu menyapu dari kiri ke kanan. Tantangannya adalah memilih kunci urut: ujung kiri atau ujung kanan.</p>
    </div>
    <table class="cx-table">
        <tr><th>Soal</th><th>Urutkan berdasarkan</th><th>Aturan greedy</th></tr>
        <tr><td>Kegiatan terbanyak tanpa bentrok</td><td>ujung kanan</td><td>ambil jika mulai ≥ selesai kegiatan terakhir</td></tr>
        <tr><td>Titik penusuk minimum</td><td>ujung kanan</td><td>tusuk di ujung kanan interval yang belum tertusuk</td></tr>
        <tr><td>Menutup ruas dengan interval minimum</td><td>ujung kiri</td><td>dari yang menyambung, ambil yang menjangkau terjauh</td></tr>
        <tr><td>Ruang minimum</td><td>ujung kiri (atau peristiwa)</td><td>tumpukan terbanyak pada satu waktu</td></tr>
        <tr><td>Gabungkan interval</td><td>ujung kiri</td><td>perpanjang blok selama bertumpuk</td></tr>
    </table>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Kegiatan Terbanyak dan Ruang Minimum</h2>
    <div class="prose"><p>Tulis interval sebagai <code>l-r</code> dipisah spasi. Mode pertama memilih kegiatan terbanyak; mode kedua membagikan rapat ke ruang memakai heap waktu selesai.</p></div>
    <div data-viz="interval-sched"></div>
</section>

<section class="lesson-section" id="salah" data-toc="Kriteria yang Salah">
    <h2>Mengapa Kriteria Lain Gagal</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Mulai paling awal</h4><p>[0, 100] mulai paling awal, tetapi menghalangi [1, 2], [3, 4], [5, 6].</p></div>
        <div class="pattern"><h4>Terpendek dulu</h4><p>[4, 6] paling pendek, tetapi bentrok dengan [0, 5] dan [5, 10] yang bisa dipilih berdua.</p></div>
        <div class="pattern"><h4>Paling sedikit bentrok</h4><p>Ada contoh penyangkal yang lebih rumit; selain itu, menghitungnya mahal.</p></div>
        <div class="pattern"><h4>Selesai paling awal ✓</h4><p>Terbukti optimal dengan exchange argument.</p></div>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'greedy interval di C++', 'desc' => 'Titik penusuk ditulis lengkap, lalu menutup ruas, menggabung, dan ruang minimum.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Balon dan Panah</h2>
    @include('lessons.walkthrough', [
        'title' => 'Titik penusuk minimum',
        'steps' => $giSteps,
        'sample' => ['input' => "4\n10 16\n2 8\n1 6\n7 12\n", 'output' => "2\n"],
        'py' => $giPy,
    ])
    <table class="trace-table">
        <tr><th>balon (urut r)</th><th>l &gt; posisi?</th><th>aksi</th><th>panah</th></tr>
        <tr class="hl"><td>[1, 6]</td><td>ya</td><td>tembak di 6</td><td>1</td></tr>
        <tr><td>[2, 8]</td><td>2 ≤ 6</td><td>sudah pecah</td><td>1</td></tr>
        <tr class="hl"><td>[7, 12]</td><td>7 &gt; 6</td><td>tembak di 12</td><td>2</td></tr>
        <tr class="ok"><td>[10, 16]</td><td>10 ≤ 12</td><td>sudah pecah</td><td><b>2</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="varian" data-toc="Menutup, Menggabung, Ruang">
    <h2>Menutup Ruas, Menggabung, dan Ruang Minimum</h2>
    <div class="prose"><p><strong>Menutup ruas.</strong> Urutkan berdasarkan ujung kiri. Dari semua interval yang mulai di dalam bagian yang sudah tertutup, ambil yang menjangkau paling jauh ke kanan. Jika tidak ada yang menjangkau lebih jauh, ada celah.</p></div>
    @include('lessons.code', ['cpp' => $giCover, 'js' => null, 'py' => null])
    <div class="prose"><p><strong>Menggabung.</strong> Setelah diurutkan berdasarkan ujung kiri, interval yang bertumpuk pasti berdampingan.</p></div>
    @include('lessons.code', ['cpp' => $giMerge, 'js' => null, 'py' => null])
    <div class="prose"><p><strong>Ruang minimum.</strong> Ubah setiap interval menjadi dua peristiwa (mulai +1, selesai −1), urutkan, lalu jumlahkan. Nilai terbesar adalah banyak rapat yang berlangsung bersamaan, dan itu juga banyak ruang minimum.</p></div>
    @include('lessons.code', ['cpp' => $giRooms, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Ujung tertutup atau terbuka?</strong> Apakah [1, 3] dan [3, 5] bentrok? Baca soalnya. Untuk peristiwa, urutan pemrosesan pada waktu yang sama (−1 dulu atau +1 dulu) menentukan jawabannya.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Nilai awal posisi.</strong> Koordinat bisa negatif atau 0. Pakai <code>LLONG_MIN</code> atau penanda "belum ada", bukan 0 atau −1.</p>
    </div>
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Semua varian di atas O(n log n) karena didominasi pengurutan. Sapuannya sendiri O(n).</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'bukti dan dualitas', 'desc' => 'Mengapa titik penusuk minimum sama dengan interval saling lepas maksimum.'])

<section class="lesson-section" id="bukti" data-toc="Bukti & Dualitas">
    <h2>Bukti dan Dualitas</h2>
    <div class="proof">
        <p><strong>Kegiatan terbanyak (greedy stays ahead).</strong> Misalkan greedy memilih g<sub>1</sub>, g<sub>2</sub>, … dan solusi optimal o<sub>1</sub>, o<sub>2</sub>, … (keduanya urut). Dengan induksi, selesai(g<sub>k</sub>) ≤ selesai(o<sub>k</sub>): untuk k = 1 karena g<sub>1</sub> selesai paling awal; untuk k berikutnya, o<sub>k</sub> mulai setelah o<sub>k−1</sub> selesai ≥ g<sub>k−1</sub> selesai, jadi o<sub>k</sub> juga kandidat bagi greedy, dan greedy memilih yang selesai paling awal. Maka greedy tidak pernah kehabisan kandidat sebelum solusi optimal.</p>
        <p><strong>Dualitas.</strong> Pada soal balon, setiap panah greedy ditembakkan di ujung kanan sebuah balon b<sub>i</sub>, dan balon-balon b<sub>i</sub> itu saling lepas (balon berikutnya dimulai setelah panah sebelumnya). Balon yang saling lepas butuh panah berbeda, jadi tidak ada solusi dengan panah lebih sedikit. Banyak titik penusuk minimum = banyak interval saling lepas maksimum.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>Kegiatan berbobot</h4><p>Jika setiap kegiatan punya nilai, greedy gagal: pakai DP + binary search.</p></div>
        <div class="pattern"><h4>k ruang, kegiatan terbanyak</h4><p>Greedy dengan multiset waktu selesai: taruh di ruang yang selesai paling akhir tetapi masih ≤ mulai.</p></div>
        <div class="pattern"><h4>Interval di lingkaran</h4><p>Gandakan koordinat atau coba setiap titik potong.</p></div>
        <div class="pattern"><h4>Koordinat besar</h4><p>Peristiwa + sort, tanpa array sepanjang koordinat.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Memilih kegiatan yang selesai paling awal menyisakan waktu paling banyak; diurutkan berdasarkan waktu selesai.">
        <p class="quiz-q">Untuk memilih kegiatan terbanyak tanpa bentrok, urutkan berdasarkan…</p>
        <div class="quiz-options">
            <button class="quiz-option">waktu mulai</button>
            <button class="quiz-option">waktu selesai</button>
            <button class="quiz-option">durasi</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Pada waktu 4 ada tiga rapat yang berlangsung: [1,5], [2,6], dan [4,8]. Jadi butuh 3 ruang.">
        <p class="quiz-q">Rapat [1, 5], [2, 6], [4, 8], [6, 9] (setengah terbuka). Berapa ruang minimum?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Untuk menutup ruas, dari semua interval yang sudah menyambung dengan bagian tertutup, ambil yang menjangkau paling jauh ke kanan.">
        <p class="quiz-q">Menutup ruas [0, 10] dengan interval sesedikit mungkin: dari interval yang menyambung, mana yang diambil?</p>
        <div class="quiz-options">
            <button class="quiz-option">Yang ujung kanannya paling jauh</button>
            <button class="quiz-option">Yang paling pendek</button>
            <button class="quiz-option">Yang ujung kirinya paling kecil</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
