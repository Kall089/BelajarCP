@php
    $vcSteps = [
        ['Satu siswa = satu struct', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

struct Siswa {
    string nama;
    int nilai, umur;
};
CPP, <<<'TXT'
<p><strong>Soal contoh "Peringkat Kelas":</strong> ada n siswa, masing-masing punya nama, nilai, dan umur. Urutkan berdasarkan <strong>nilai menurun</strong>; jika nilainya sama, yang <strong>lebih muda</strong> didahulukan; jika masih sama, <strong>nama</strong> menurut abjad. Cetak namanya.</p>
<p>Tiga data yang selalu berjalan bersama lebih mudah dibaca sebagai <code>struct</code> daripada <code>tuple&lt;string,int,int&gt;</code>: <code>x.nilai</code> jauh lebih jelas daripada <code>get&lt;1&gt;(x)</code>.</p>
TXT],
        ['Membaca ke dalam vector', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<Siswa> s(n);
    for (auto& x : s) cin >> x.nama >> x.nilai >> x.umur;
CPP, <<<'TXT'
<p><code>vector&lt;Siswa&gt; s(n)</code> langsung membuat n elemen kosong, jadi kita bisa mengisinya lewat referensi <code>auto&amp; x</code>. Tanpa <code>&amp;</code>, yang terisi hanyalah <em>salinan</em> dan vector tetap kosong.</p>
<div class="wt-tip">Jika ukurannya baru diketahui sambil membaca, pakai <code>s.reserve(n)</code> lalu <code>push_back</code>, agar tidak ada realokasi.</div>
TXT],
        ['Comparator tiga kriteria', <<<'CPP'

    sort(s.begin(), s.end(), [](const Siswa& x, const Siswa& y) {
        if (x.nilai != y.nilai) return x.nilai > y.nilai;   // menurun
        if (x.umur != y.umur) return x.umur < y.umur;       // menaik
        return x.nama < y.nama;                             // menaik
    });
CPP, <<<'TXT'
<p>Comparator menjawab satu pertanyaan: <strong>"apakah x harus berada di depan y?"</strong></p>
<ul>
    <li>Periksa kriteria pertama. Jika berbeda, hasil perbandingannya langsung menjadi jawaban.</li>
    <li>Jika sama, turun ke kriteria berikutnya. Kriteria terakhir tidak perlu dicek kesamaannya.</li>
    <li>Arah menurun cukup dengan membalik tanda: <code>&gt;</code> untuk menurun, <code>&lt;</code> untuk menaik.</li>
</ul>
<p>Parameter ditulis <code>const Siswa&amp;</code> agar string nama tidak disalin pada setiap perbandingan (sort melakukan sekitar n log n perbandingan).</p>
<div class="wt-warn">Jangan pernah memakai <code>&lt;=</code> atau <code>&gt;=</code>. Untuk dua siswa yang identik, comparator wajib menjawab <code>false</code>.</div>
TXT],
        ['Mencetak hasil', <<<'CPP'

    for (const auto& x : s) cout << x.nama << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Kompleksitas <code>O(n log n)</code> perbandingan. Dengan n = 2 · 10<sup>5</sup> dan nama sampai 20 huruf, ini selesai jauh di bawah satu detik.</p>
<p>Pakai <code>'\n'</code>, bukan <code>endl</code>: <code>endl</code> memaksa flush setiap baris dan bisa membuat program yang benar menjadi TLE.</p>
TXT],
    ];

    $vcPy = <<<'PY'
import sys
data = sys.stdin.read().split()
n = int(data[0])
siswa = []
for i in range(n):
    nama, nilai, umur = data[1 + 3*i], int(data[2 + 3*i]), int(data[3 + 3*i])
    siswa.append((nama, nilai, umur))

# Kunci tuple: nilai menurun (dinegasi), umur menaik, nama menaik
siswa.sort(key=lambda s: (-s[1], s[2], s[0]))
print("\n".join(s[0] for s in siswa))
PY;

    $vcOps = <<<'CPP'
vector<int> v;               // kosong
vector<int> a(5, 0);         // 5 elemen bernilai 0
vector<vector<int>> g(n);    // n list kosong (adjacency list)

v.push_back(7);              // O(1) rata-rata: tambah di belakang
v.pop_back();                // O(1): buang elemen terakhir
v[i]; v.back(); v.front();   // O(1): akses
v.size(); v.empty();         // O(1)
v.insert(v.begin(), 3);      // O(n)! semua elemen bergeser
v.erase(v.begin() + i);      // O(n)! elemen setelah i bergeser
v.clear();                   // O(n), kapasitas tidak dikembalikan
v.resize(10);                // ubah ukuran, elemen baru bernilai 0
v.reserve(100000);           // siapkan kapasitas, size tetap
sort(v.begin(), v.end());    // O(n log n)
CPP;

    $vcPair = <<<'CPP'
pair<int, string> p = {90, "budi"};
p.first;  p.second;

// pair dibandingkan leksikografis: first dulu, jika seri baru second
vector<pair<int, int>> e = {{3, 1}, {1, 9}, {3, 0}};
sort(e.begin(), e.end());              // (1,9) (3,0) (3,1)

// tuple untuk tiga nilai atau lebih
tuple<int, int, string> t = make_tuple(80, 18, "cici");
int nilai, umur; string nama;
tie(nilai, umur, nama) = t;            // membongkar (C++14)

// array<int, 3> juga bisa langsung dibandingkan dan di-sort
array<int, 3> q = {5, 2, 7};

// Trik negasi: urut nilai MENURUN tanpa comparator
vector<pair<int, int>> w;              // (-nilai, umur)
w.push_back({-80, 20});
sort(w.begin(), w.end());              // nilai besar di depan
CPP;

    $vcStruct = <<<'CPP'
struct Tim { string nama; int soal, penalti; };

// Cara 1: operator< di dalam struct (dipakai otomatis oleh sort, set, map)
bool operator<(const Tim& a, const Tim& b) {
    if (a.soal != b.soal) return a.soal > b.soal;
    return a.penalti < b.penalti;
}

// Cara 2: lambda tepat di tempat pemakaian (paling sering di kontes)
sort(t.begin(), t.end(), [](const Tim& a, const Tim& b) {
    return a.penalti < b.penalti;
});

// Cara 3: struct pembanding, untuk set / priority_queue
struct PenaltiKecil {
    bool operator()(const Tim& a, const Tim& b) const {
        return a.penalti > b.penalti;    // priority_queue: "lebih kecil" = di bawah
    }
};
priority_queue<Tim, vector<Tim>, PenaltiKecil> pq;   // penalti terkecil di atas
CPP;

    $vcTraps = <<<'CPP'
// Jebakan 1: menyalin kontainer di setiap pemanggilan
long long jumlah(vector<long long> v);          // menyalin n elemen!
long long jumlah(const vector<long long>& v);   // benar: referensi

// Jebakan 2: insert/erase di depan di dalam loop → O(n²)
for (int x : data) v.insert(v.begin(), x);      // pakai deque atau push_back + reverse

// Jebakan 3: size() bertipe unsigned
vector<int> kosong;
for (int i = 0; i < kosong.size() - 1; i++)     // 0 − 1 = 18446744073709551615 !
for (int i = 0; i + 1 < (int)kosong.size(); i++) // benar

// Jebakan 4: referensi/iterator basi setelah realokasi
int& pertama = v[0];
v.push_back(99);                                // mungkin pindah blok
cout << pertama;                                // perilaku tak terdefinisi
CPP;

    $vcArgsort = <<<'CPP'
// Urutkan INDEKS berdasarkan nilai, tanpa mengubah array aslinya
vector<int> idx(n);
iota(idx.begin(), idx.end(), 0);                 // 0, 1, 2, ..., n-1
sort(idx.begin(), idx.end(), [&](int i, int j) {
    if (a[i] != a[j]) return a[i] > a[j];
    return i < j;                                // seri: indeks kecil dulu
});
// idx[0] = indeks nilai terbesar, dst.
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memilih wadah yang tepat (<code>vector</code>, <code>pair</code>, <code>tuple</code>, <code>struct</code>) dan tahu operasi mana yang murah dan mana yang mahal.</li>
        <li>Menjelaskan mengapa <code>push_back</code> rata-rata O(1) walaupun kadang menyalin seluruh isi vector.</li>
        <li>Menulis comparator multi-kriteria yang benar dan tidak membuat program crash.</li>
        <li>Menghindari empat jebakan vector yang sering membuat solusi benar menjadi salah atau TLE.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'wadah data serba guna', 'desc' => 'Apa itu vector, berapa biaya setiap operasinya, dan mengapa ia bisa tumbuh.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Rak Buku">
    <h2>Intuisi: Rak Buku yang Bisa Memanjang</h2>
    <div class="prose">
        <p>Bayangkan rak buku yang bisa memanjang sendiri. Menaruh buku di <strong>ujung kanan</strong> itu gampang. Tetapi menyisipkan buku di <strong>paling kiri</strong> berarti semua buku harus digeser satu per satu. Itulah <code>vector</code>: <code>push_back</code> murah, <code>insert</code> di depan mahal.</p>
        <p>Saat rak penuh, kamu membeli rak baru yang <strong>dua kali lebih besar</strong> dan memindahkan semua buku sekaligus. Pindahan itu melelahkan, tetapi setelahnya lama sekali tidak perlu pindah lagi. Karena itulah rata-rata biaya menaruh satu buku tetap kecil.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>vector&lt;T&gt;</b><span>Array yang bisa tumbuh. Akses indeks dan tambah di belakang O(1). Pilihan bawaan untuk hampir semua data.</span></div>
        <div class="term"><b>pair&lt;A, B&gt;</b><span>Dua nilai berpasangan. Sudah bisa dibandingkan: <code>first</code> dulu, jika seri baru <code>second</code>.</span></div>
        <div class="term"><b>tuple / array</b><span>Tiga nilai atau lebih yang juga bisa langsung di-<code>sort</code>.</span></div>
        <div class="term"><b>struct + comparator</b><span>Data bernama (nama, nilai, umur) dengan aturan urut buatanmu sendiri.</span></div>
    </div>
</section>

<section class="lesson-section" id="biaya" data-toc="Operasi & Biayanya">
    <h2>Operasi dan Biayanya</h2>
    <div class="prose"><p>Yang perlu dihafal <strong>bukan sintaksnya</strong>, melainkan kolom biayanya. Satu operasi O(n) di dalam loop mengubah solusi O(n) menjadi O(n²).</p></div>
    @include('lessons.code', ['cpp' => $vcOps, 'js' => null, 'py' => null])
    <table class="cx-table">
        <tr><th>Operasi</th><th>Biaya</th><th>Catatan</th></tr>
        <tr><td><code>v[i]</code>, <code>back()</code>, <code>size()</code></td><td><code>O(1)</code></td><td>Elemen tersimpan berurutan di memori</td></tr>
        <tr><td><code>push_back</code>, <code>pop_back</code></td><td><code>O(1)</code> rata-rata</td><td>Sesekali realokasi O(n), tetapi jarang</td></tr>
        <tr><td><code>insert</code>/<code>erase</code> di tengah atau depan</td><td><code>O(n)</code></td><td>Semua elemen di belakangnya bergeser</td></tr>
        <tr><td><code>find</code> (mencari nilai)</td><td><code>O(n)</code></td><td>Butuh cepat? Pakai <code>set</code>/<code>map</code> atau sort + binary search</td></tr>
        <tr><td><code>sort</code></td><td><code>O(n log n)</code></td><td>Bisa dengan comparator sendiri</td></tr>
    </table>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Kapasitas">
    <h2>Visualisasi: Kapasitas yang Berlipat Dua</h2>
    <div class="prose">
        <p><code>size()</code> adalah banyak elemen yang terisi, <code>capacity()</code> adalah banyak slot yang sudah dipesan. Putar animasinya dan perhatikan kapan penyalinan terjadi. Coba juga <strong>Mode Tebak</strong>: tebak kapasitas baru setiap kali blok penuh.</p>
    </div>
    <div data-viz="vector-mem"></div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Untuk n = 100 000 kali <code>push_back</code>, realokasi hanya terjadi sekitar 18 kali dan total penyalinannya kurang dari 2n. Inilah yang disebut <strong>O(1) teramortisasi</strong>: satu operasi bisa mahal, tetapi rata-ratanya tetap konstan.</p>
    </div>
</section>

<section class="lesson-section" id="pair" data-toc="pair, tuple & array">
    <h2>Menggabung Beberapa Nilai: pair, tuple & array</h2>
    <div class="prose"><p>Nilai yang harus diurutkan bersama sebaiknya disimpan bersama. <code>pair</code>, <code>tuple</code>, dan <code>array</code> sudah punya perbandingan leksikografis bawaan, jadi bisa langsung di-<code>sort</code> tanpa comparator.</p></div>
    @include('lessons.code', ['cpp' => $vcPair, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p><strong>Trik negasi</strong> sering dipakai di kontes: simpan <code>-nilai</code> agar urutan menaik bawaan menjadi urutan menurun untuk kriteria itu, tanpa menulis comparator.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'comparator di C++', 'desc' => 'Mengurutkan data dengan banyak kriteria, lengkap dengan programnya.'])

<section class="lesson-section" id="comparator" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: Dua Kunci Pengurutan</h2>
    <div class="prose"><p>Empat siswa dengan (nilai, umur). Aturan: nilai <strong>menurun</strong>, jika sama umur <strong>menaik</strong>.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Langkah</th><th>Urutan</th><th>Keterangan</th></tr>
            <tr><td>Awal</td><td>A (80/20), B (90/19), C (80/18), D (70/22)</td><td>A dan C sama-sama 80: inilah pasangan yang menguji comparator</td></tr>
            <tr class="hl"><td>Kunci 1</td><td>B (90) · A, C (80) · D (70)</td><td>B pasti pertama, D pasti terakhir</td></tr>
            <tr class="hl"><td>Kunci 2</td><td>C (umur 18) sebelum A (umur 20)</td><td>Nilai seri, umur menentukan</td></tr>
            <tr class="ok"><td>Hasil</td><td><b>B, C, A, D</b></td><td>Sepenuhnya tertentu</td></tr>
        </table>
    </div>
    <div class="prose"><p>Jika kunci kedua lupa ditulis, urutan A dan C <em>tidak tentu</em>: <code>std::sort</code> tidak stabil, sehingga hasil di komputer juri bisa berbeda dengan di komputermu.</p></div>
    <div data-viz="cmp-sort"></div>
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Peringkat Kelas</h2>
    @include('lessons.walkthrough', [
        'title' => 'Mengurutkan struct dengan tiga kriteria',
        'steps' => $vcSteps,
        'sample' => ['input' => "4\ndina 80 20\nbudi 90 19\ncici 80 18\neko 70 22\n", 'output' => "budi\ncici\ndina\neko\n"],
        'py' => $vcPy,
    ])
</section>

<section class="lesson-section" id="struct" data-toc="Tiga Cara Menulis Comparator">
    <h2>Tiga Cara Menulis Comparator</h2>
    @include('lessons.code', ['cpp' => $vcStruct, 'js' => null, 'py' => null])
    <div class="steps">
        <div class="step-card"><b>operator&lt;</b><span>Ditulis sekali, dipakai otomatis oleh <code>sort</code>, <code>set</code>, dan <code>map</code>. Cocok jika urutan itu "urutan alami" datanya.</span></div>
        <div class="step-card"><b>Lambda</b><span>Ditulis tepat di tempat <code>sort</code> dipanggil. Paling ringkas; bisa menangkap variabel luar dengan <code>[&amp;]</code>.</span></div>
        <div class="step-card"><b>Struct pembanding</b><span>Wajib untuk <code>priority_queue</code> dan <code>set</code> dengan urutan khusus. Ingat: di priority_queue, elemen yang "paling besar" menurut comparator berada di atas.</span></div>
    </div>
</section>

<section class="lesson-section" id="jebakan" data-toc="Empat Jebakan vector">
    <h2>Empat Jebakan vector</h2>
    <div class="prose"><p>Keempatnya pernah menjatuhkan submisi yang algoritmanya sudah benar.</p></div>
    @include('lessons.code', ['cpp' => $vcTraps, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jebakan 3 paling berbahaya</strong> karena tidak selalu langsung error: pada vector kosong, <code>v.size() - 1</code> menjadi bilangan raksasa dan loop berjalan sangat lama sebelum crash. Ubah ke <code>(int)v.size()</code> atau tulis <code>i + 1 &lt; v.size()</code>.</p>
    </div>
    <table class="cx-table">
        <tr><th>Kebutuhan</th><th>Pakai</th><th>Alasan</th></tr>
        <tr><td>Daftar biasa, tambah di belakang</td><td><code>vector</code></td><td>Pilihan bawaan, ramah cache, tercepat</td></tr>
        <tr><td>Tambah/hapus di kedua ujung</td><td><code>deque</code></td><td>O(1) di depan dan belakang</td></tr>
        <tr><td>Dua nilai berpasangan</td><td><code>pair</code></td><td>Sudah bisa dibandingkan</td></tr>
        <tr><td>Tiga nilai atau lebih</td><td><code>array&lt;T, k&gt;</code> / <code>struct</code></td><td>array bisa langsung di-sort; struct lebih jelas dibaca</td></tr>
        <tr><td>Ukuran tetap & kecil</td><td>array biasa</td><td>Tanpa alokasi dinamis</td></tr>
    </table>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'aturan di balik layar', 'desc' => 'Strict weak ordering, bukti amortisasi, dan mengurutkan indeks.'])

<section class="lesson-section" id="swo" data-toc="Strict Weak Ordering">
    <h2>Mengapa <code>&lt;=</code> Bisa Membuat Crash?</h2>
    <div class="proof">
        <p><code>std::sort</code> mengasumsikan comparator adalah <strong>strict weak ordering</strong>: (1) <code>cmp(x, x)</code> selalu false, (2) jika <code>cmp(x, y)</code> benar maka <code>cmp(y, x)</code> salah, (3) transitif, dan (4) "sama dengan" juga transitif.</p>
        <p>Demi kecepatan, sort memakai elemen pembatas (sentinel) dan loop seperti <code>while (cmp(a[i], pivot)) i++;</code> <em>tanpa memeriksa batas array</em>. Dengan comparator yang benar, loop pasti berhenti paling lambat di pivot itu sendiri karena <code>cmp(pivot, pivot)</code> false.</p>
        <p>Dengan <code>&lt;=</code>, <code>cmp(pivot, pivot)</code> menjadi <strong>true</strong>. Jika banyak elemen bernilai sama, loop melewati pivot dan terus membaca memori di luar array. Hasilnya: jawaban acak atau segmentation fault, hanya pada data yang punya elemen kembar.</p>
    </div>
</section>

<section class="lesson-section" id="amortisasi" data-toc="Bukti Amortisasi">
    <h2>Bukti: Total Salinan Kurang dari 2n</h2>
    <div class="proof">
        <p>Realokasi terjadi saat ukuran mencapai 1, 2, 4, …, 2<sup>k</sup> dengan 2<sup>k</sup> &lt; n. Setiap realokasi menyalin semua elemen yang ada, jadi totalnya</p>
        <p style="text-align:center"><code>1 + 2 + 4 + … + 2<sup>k</sup> = 2<sup>k+1</sup> − 1 &lt; 2n</code></p>
        <p>Dibagi n kali push_back, rata-ratanya kurang dari 2 salinan per operasi: konstan. Perhatikan bahwa ini hanya berlaku karena kapasitasnya <strong>dikali</strong> (×2). Jika kapasitas hanya <em>ditambah</em> konstan (misalnya +10), totalnya menjadi n²/20: kuadratik.</p>
    </div>
</section>

<section class="lesson-section" id="argsort" data-toc="Mengurutkan Indeks">
    <h2>Teknik: Mengurutkan Indeks (Argsort)</h2>
    <div class="prose"><p>Sering kita perlu urutan berdasarkan nilai, tetapi juga harus menjawab dalam <strong>urutan input asli</strong> (misalnya peringkat setiap peserta). Urutkan indeksnya saja; array aslinya tetap utuh.</p></div>
    @include('lessons.code', ['cpp' => $vcArgsort, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Papan skor</h4><p>Banyak soal menurun, penalti menaik, nama menaik.</p></div>
        <div class="pattern"><h4>Peringkat dengan seri</h4><p>Urutkan (nilai, indeks), lalu beri peringkat dan kembalikan ke posisi asal.</p></div>
        <div class="pattern"><h4>Adjacency list</h4><p><code>vector&lt;vector&lt;int&gt;&gt; g(n)</code>, lalu <code>g[u].push_back(v)</code>.</p></div>
        <div class="pattern"><h4>Interval</h4><p><code>vector&lt;pair&lt;int,int&gt;&gt;</code> diurutkan berdasarkan ujung kiri atau kanan.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Menyisipkan di depan menggeser semua elemen: O(n) per operasi, sehingga n kali menjadi O(n²). push_back lalu reverse di akhir hanya O(n).">
        <p class="quiz-q">Program memanggil <code>v.insert(v.begin(), x)</code> untuk setiap x dari 2 · 10<sup>5</sup> bilangan. Kompleksitasnya?</p>
        <div class="quiz-options">
            <button class="quiz-option">O(n)</button>
            <button class="quiz-option">O(n log n)</button>
            <button class="quiz-option">O(n²)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="pair dibandingkan leksikografis: first dulu. (2, 5) < (3, 1) karena 2 < 3, walaupun 5 > 1.">
        <p class="quiz-q">Setelah sort, mana yang berada paling depan: <code>(3, 1)</code>, <code>(2, 5)</code>, atau <code>(3, 0)</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">(3, 0)</button>
            <button class="quiz-option">(2, 5)</button>
            <button class="quiz-option">(3, 1)</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Dengan <=, cmp(x, x) bernilai true. Ini melanggar strict weak ordering, dan sort bisa membaca di luar array pada data dengan elemen kembar.">
        <p class="quiz-q">Apa yang salah dengan comparator <code>return a.nilai &gt;= b.nilai;</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">Untuk dua elemen sama ia mengembalikan true, bisa membuat crash</button>
            <button class="quiz-option">Urutannya menjadi menaik</button>
            <button class="quiz-option">Tidak ada, hanya lebih lambat</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
