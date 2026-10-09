@php
    $smSteps = [
        ['Semua tiket masuk multiset', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    multiset<int> tiket;
    for (int i = 0; i < n; i++) {
        int h;
        cin >> h;
        tiket.insert(h);
    }
CPP, <<<'TXT'
<p><strong>Soal contoh "Tiket Konser":</strong> ada n tiket dengan harga masing-masing, dan m pembeli datang satu per satu. Pembeli ke-j mau membayar paling banyak <code>t<sub>j</sub></code>; ia membeli tiket <strong>termahal yang harganya ≤ t<sub>j</sub></strong>. Tiket yang sudah terjual hilang. Cetak harga tiket yang dibeli setiap pembeli, atau −1 jika tidak ada.</p>
<p>Harga tiket boleh kembar, jadi wadahnya <code>multiset</code>: terurut, boleh duplikat, dan bisa menghapus elemen di tengah dalam O(log n).</p>
TXT],
        ['Mencari termahal yang ≤ t', <<<'CPP'

    for (int j = 0; j < m; j++) {
        int t;
        cin >> t;
        auto it = tiket.upper_bound(t);   // tiket pertama yang > t
CPP, <<<'TXT'
<p>multiset tidak punya fungsi "terbesar yang ≤ t", tetapi punya <code>upper_bound(t)</code>: elemen <strong>pertama yang &gt; t</strong>. Tepat satu langkah di kirinya adalah jawaban kita.</p>
<pre>tiket : 3  5  5  7  8      t = 6
                 ↑ upper_bound(6) menunjuk 7
              ↑ mundur satu: 5 (termahal yang ≤ 6)</pre>
TXT],
        ['Mundur satu langkah, lalu hapus satu', <<<'CPP'
        if (it == tiket.begin()) {
            cout << -1 << '\n';            // semua tiket lebih mahal dari t
        } else {
            --it;                          // terbesar yang <= t
            cout << *it << '\n';
            tiket.erase(it);               // hapus SATU tiket (pakai iterator)
        }
    }
    return 0;
}
CPP, <<<'TXT'
<p>Jika <code>upper_bound</code> menunjuk ke <code>begin()</code>, berarti <em>semua</em> tiket lebih mahal dari t: tidak ada yang bisa dibeli.</p>
<p>Perhatikan <code>erase(it)</code>. Jika kita menulis <code>tiket.erase(*it)</code> (dengan nilai), <strong>semua</strong> tiket berharga sama ikut terhapus. Itu bug yang tidak memberi error apa pun, hanya jawaban salah.</p>
<div class="wt-tip">Setiap pembeli: satu <code>upper_bound</code> dan satu <code>erase</code>, masing-masing O(log n). Total O((n + m) log n).</div>
TXT],
    ];

    $smPy = <<<'PY'
import sys
from bisect import bisect_right
data = sys.stdin.read().split()
n, m = int(data[0]), int(data[1])
tiket = sorted(map(int, data[2:2 + n]))      # Python tidak punya multiset bawaan
out = []
for t in map(int, data[2 + n:2 + n + m]):
    i = bisect_right(tiket, t)               # = upper_bound
    if i == 0:
        out.append(-1)
    else:
        out.append(tiket[i - 1])
        del tiket[i - 1]                     # O(n): cukup untuk contoh, lambat untuk n besar
print("\n".join(map(str, out)))
PY;

    $smSet = <<<'CPP'
set<int> s;                         // terurut, tanpa duplikat
s.insert(5); s.insert(2); s.insert(5);   // isi: {2, 5}
s.count(5);                         // 1 (ada) atau 0
s.erase(2);
*s.begin();                         // terkecil
*s.rbegin();                        // terbesar
auto it = s.lower_bound(4);         // pertama >= 4
if (it != s.end()) cout << *it;

// JANGAN: lower_bound(s.begin(), s.end(), 4)  → O(n) pada set!
// PAKAI : s.lower_bound(4)                    → O(log n)

multiset<int> ms = {5, 5, 5};
ms.erase(ms.find(5));               // hapus SATU → {5, 5}
ms.erase(5);                        // hapus SEMUA → {}
CPP;

    $smMap = <<<'CPP'
map<string, int> cnt;
cnt["apel"]++;                      // kunci belum ada → dibuat dengan nilai 0, lalu ++
cnt["jeruk"] += 3;
for (auto& kv : cnt)                // terurut menurut kunci
    cout << kv.first << " " << kv.second << "\n";

// Jebakan operator[]: sekadar MEMBACA kunci yang tidak ada akan MEMBUATNYA
if (cnt["mangga"] > 0) { }          // sekarang "mangga" ada di map dengan nilai 0!
if (cnt.count("mangga")) { }        // benar: hanya memeriksa
auto it = cnt.find("mangga");       // benar: it == cnt.end() jika tidak ada
CPP;

    $smPq = <<<'CPP'
priority_queue<int> maxh;                              // top() = TERBESAR (bawaan)
priority_queue<int, vector<int>, greater<int>> minh;   // top() = terkecil

// Pasangan (jarak, simpul) untuk Dijkstra: yang jaraknya kecil di atas
priority_queue<pair<long long, int>,
               vector<pair<long long, int>>,
               greater<pair<long long, int>>> pq;
pq.push({0, 1});
pq.top().first;  pq.pop();

// Trik: min-heap dari max-heap dengan menyimpan nilai negatif
maxh.push(-x);   // -maxh.top() adalah nilai terkecil
CPP;

    $smHash = <<<'CPP'
// unordered_map: rata-rata O(1), tetapi bisa dijebak tes anti-hash di Codeforces.
// Pasang fungsi hash acak (splitmix64 + seed waktu) agar aman.
struct HashAman {
    static unsigned long long splitmix64(unsigned long long x) {
        x += 0x9e3779b97f4a7c15ULL;
        x = (x ^ (x >> 30)) * 0xbf58476d1ce4e5b9ULL;
        x = (x ^ (x >> 27)) * 0x94d049bb133111ebULL;
        return x ^ (x >> 31);
    }
    size_t operator()(unsigned long long x) const {
        static const unsigned long long ACAK =
            chrono::steady_clock::now().time_since_epoch().count();
        return splitmix64(x + ACAK);
    }
};
unordered_map<long long, int, HashAman> hitung;
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memilih antara <code>set</code>, <code>multiset</code>, <code>map</code>, <code>unordered_map</code>, dan <code>priority_queue</code> berdasarkan pertanyaan yang akan diajukan berulang kali.</li>
        <li>Memakai <code>lower_bound</code>/<code>upper_bound</code> pada data yang terus berubah.</li>
        <li>Menghindari jebakan <code>erase</code> pada multiset dan <code>operator[]</code> pada map.</li>
        <li>Memahami cara kerja heap di balik <code>priority_queue</code>, dan mengapa push/pop O(log n).</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'wadah yang selalu rapi', 'desc' => 'Kontainer terurut dan antrian prioritas, dan kapan memakai masing-masing.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi">
    <h2>Intuisi: Tiga Kebutuhan, Tiga Wadah</h2>
    <div class="prose">
        <p><strong>Daftar hadir yang selalu urut abjad:</strong> setiap tamu baru langsung disisipkan di tempat yang benar, jadi kamu selalu tahu siapa yang pertama dan bisa mencari "nama pertama setelah 'Budi'" dengan cepat. Itulah <strong>set</strong>.</p>
        <p><strong>Buku telepon:</strong> dari nama langsung dapat nomornya tanpa membaca seluruh halaman. Itulah <strong>map</strong>: kunci → nilai.</p>
        <p><strong>Antrian IGD:</strong> pasien paling parah dilayani lebih dulu, bukan yang datang lebih dulu. Itulah <strong>priority_queue</strong>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>set / multiset</b><span>Isi selalu terurut. insert, erase, find, lower_bound: O(log n). multiset boleh kembar.</span></div>
        <div class="term"><b>map</b><span>Kunci terurut → nilai. Kunci boleh string, pair, apa pun yang bisa dibandingkan.</span></div>
        <div class="term"><b>unordered_map</b><span>Seperti map tanpa urutan. Rata-rata O(1), tetapi bisa dijebak tes anti-hash.</span></div>
        <div class="term"><b>priority_queue</b><span>Hanya tahu yang paling ekstrem. push, pop O(log n), top O(1).</span></div>
    </div>
    <table class="cx-table">
        <tr><th>Pertanyaan yang diulang-ulang</th><th>Pakai</th></tr>
        <tr><td>"Apakah x sudah pernah muncul?"</td><td><code>set</code> (atau array boolean jika x kecil)</td></tr>
        <tr><td>"Berapa kali x muncul?" / "nilai milik kunci x?"</td><td><code>map</code> / <code>unordered_map</code></td></tr>
        <tr><td>"Nilai terdekat dengan x pada data yang berubah?"</td><td><code>set</code>/<code>multiset</code> + <code>lower_bound</code></td></tr>
        <tr><td>"Ambil yang terbesar/terkecil, lalu buang"</td><td><code>priority_queue</code></td></tr>
        <tr><td>"Ambil terkecil DAN terbesar" atau "hapus elemen tertentu"</td><td><code>multiset</code> (priority_queue hanya satu ujung)</td></tr>
    </table>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi set">
    <h2>Visualisasi: set dan multiset</h2>
    <div class="prose"><p>Perhatikan bedanya <code>erase(find(5))</code> dan <code>erase(5)</code> pada multiset, lalu ganti ke <code>set</code> dan lihat apa yang terjadi saat 5 di-insert dua kali. Mode Tebak akan menanyakan hasil <code>lower_bound</code> dan <code>upper_bound</code>.</p></div>
    <div data-viz="set-ops"></div>
    @include('lessons.code', ['cpp' => $smSet, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>std::lower_bound pada set berjalan O(n)</strong> karena iterator set tidak bisa melompat. Selalu pakai versi milik kontainernya: <code>s.lower_bound(x)</code>.</p>
    </div>
</section>

<section class="lesson-section" id="map" data-toc="map & Jebakan operator[]">
    <h2>map dan Jebakan <code>operator[]</code></h2>
    @include('lessons.code', ['cpp' => $smMap, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><code>cnt[x]</code> untuk kunci yang belum ada akan <strong>membuat</strong> entri baru bernilai 0. Di dalam loop pemeriksaan, map bisa membengkak diam-diam dan iterasi atas map ikut berubah. Pakai <code>count</code> atau <code>find</code> untuk sekadar memeriksa.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'set, map, dan heap di C++', 'desc' => 'Program lengkap memakai multiset, lalu cara kerja priority_queue.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Tiket Konser</h2>
    @include('lessons.walkthrough', [
        'title' => 'multiset + upper_bound + erase(iterator)',
        'steps' => $smSteps,
        'sample' => ['input' => "5 3\n5 3 7 8 5\n4 8 3\n", 'output' => "3\n8\n-1\n"],
        'py' => $smPy,
    ])
</section>

<section class="lesson-section" id="heap" data-toc="Visualisasi Heap">
    <h2>priority_queue: Heap di Dalam Array</h2>
    <div class="prose">
        <p><code>priority_queue</code> sebenarnya adalah <strong>binary heap</strong> yang disimpan dalam array: anak dari indeks i ada di <code>2i + 1</code> dan <code>2i + 2</code>. Aturannya hanya satu: setiap ayah "lebih baik" dari anak-anaknya. Akibatnya puncak (indeks 0) selalu elemen terbaik.</p>
        <p>Saat push, elemen baru naik selama ia lebih baik dari ayahnya. Saat pop, elemen terakhir dipindah ke akar lalu turun. Keduanya hanya menelusuri satu jalur, panjangnya log n.</p>
    </div>
    <div data-viz="heap-pq"></div>
    @include('lessons.code', ['cpp' => $smPq, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>priority_queue bawaan C++ adalah MAX-heap.</strong> Untuk Dijkstra dan Prim kamu butuh yang terkecil: tambahkan <code>greater&lt;…&gt;</code>, atau simpan nilai negatif.</p>
    </div>
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Kontainer</th><th>insert / erase</th><th>cari / batas</th><th>ekstrem</th></tr>
        <tr><td><code>set</code>, <code>multiset</code>, <code>map</code></td><td><code>O(log n)</code></td><td><code>O(log n)</code></td><td><code>O(1)</code> (begin / rbegin)</td></tr>
        <tr><td><code>unordered_map</code></td><td><code>O(1)</code> rata-rata</td><td>tanpa lower_bound</td><td>–</td></tr>
        <tr><td><code>priority_queue</code></td><td>push/pop <code>O(log n)</code></td><td>tidak bisa mencari</td><td>top <code>O(1)</code></td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>multiset::erase(nilai)</strong> menghapus semua salinan. Untuk menghapus satu, pakai <code>ms.erase(ms.find(x))</code>, dan pastikan <code>find</code> tidak mengembalikan <code>end()</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Konstanta besar.</strong> set dan map memakai pohon merah-hitam: setiap operasi melibatkan alokasi dan lompatan pointer. Untuk 10<sup>6</sup> operasi masih aman, tetapi jika kuncinya bilangan kecil (≤ 10<sup>6</sup>), array biasa jauh lebih cepat.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'di balik layar', 'desc' => 'Mengapa heap O(log n), hash yang aman, dan comparator untuk set.'])

<section class="lesson-section" id="bukti" data-toc="Mengapa Heap O(log n)?">
    <h2>Mengapa Heap O(log n)?</h2>
    <div class="proof">
        <p>Heap dengan n elemen adalah pohon biner <em>lengkap</em>: setiap tingkat terisi penuh kecuali mungkin tingkat terakhir. Tingkat ke-d berisi 2<sup>d</sup> simpul, jadi tingginya ⌊log<sub>2</sub> n⌋.</p>
        <p>Saat push, elemen baru naik paling banyak satu tingkat per pertukaran sampai akar: paling banyak log n pertukaran. Saat pop, elemen turun paling banyak log n tingkat. Setelah setiap pertukaran, aturan "ayah lebih baik dari anak" kembali berlaku di bagian yang sudah dilewati, sehingga heap tetap sah.</p>
    </div>
</section>

<section class="lesson-section" id="hash" data-toc="unordered_map yang Aman">
    <h2>unordered_map yang Aman</h2>
    <div class="prose"><p>Fungsi hash bawaan untuk bilangan bulat di GCC adalah identitas. Penyusun soal bisa memilih kunci yang semuanya jatuh ke bucket yang sama, sehingga setiap operasi menjadi O(n). Hash acak membuat serangan itu mustahil disiapkan.</p></div>
    @include('lessons.code', ['cpp' => $smHash, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Menghitung frekuensi</h4><p><code>map&lt;string,int&gt;</code>, lalu urutkan pasangannya.</p></div>
        <div class="pattern"><h4>Nilai terdekat</h4><p>multiset + lower_bound, periksa elemen itu dan sebelumnya.</p></div>
        <div class="pattern"><h4>K terbesar sejauh ini</h4><p>Min-heap berukuran K: buang top() jika ukurannya melebihi K.</p></div>
        <div class="pattern"><h4>Median berjalan</h4><p>Dua heap: max-heap separuh bawah, min-heap separuh atas.</p></div>
        <div class="pattern"><h4>Menggabung K list urut</h4><p>Min-heap berisi (nilai, list, indeks).</p></div>
        <div class="pattern"><h4>Interval aktif</h4><p>set berisi ujung kanan, ambil yang terkecil.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="erase dengan nilai menghapus semua salinan 4. Yang tersisa {1, 7}.">
        <p class="quiz-q"><code>multiset&lt;int&gt; s = {1, 4, 4, 4, 7}; s.erase(4);</code> Isinya sekarang?</p>
        <div class="quiz-options">
            <button class="quiz-option">{1, 4, 4, 7}</button>
            <button class="quiz-option">{1, 4, 7}</button>
            <button class="quiz-option">{1, 7}</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="upper_bound(6) menunjuk 8 (pertama yang > 6). Mundur satu langkah memberi 5, nilai terbesar yang ≤ 6.">
        <p class="quiz-q">set = {2, 5, 8}. Bagaimana mendapat nilai terbesar yang ≤ 6?</p>
        <div class="quiz-options">
            <button class="quiz-option"><code>prev(s.upper_bound(6))</code></button>
            <button class="quiz-option"><code>s.lower_bound(6)</code></button>
            <button class="quiz-option"><code>s.upper_bound(6)</code></button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="priority_queue bawaan adalah max-heap, sehingga top() memberi nilai terbesar: 9.">
        <p class="quiz-q"><code>priority_queue&lt;int&gt; pq;</code> lalu push 4, 9, 1. Berapa <code>pq.top()</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">9</button>
            <button class="quiz-option">4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
