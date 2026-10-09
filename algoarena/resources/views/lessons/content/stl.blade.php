@php
    $stSteps = [
        ['Baca dan urutkan', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;
    sort(a.begin(), a.end());

CPP, <<<'TXT'
<p><code>vector</code> adalah array yang ukurannya bisa diatur saat program berjalan. <code>sort</code> mengurutkan dalam O(N log N); setelah terurut, banyak pertanyaan bisa dijawab dengan binary search.</p>
<p><code>ios::sync_with_stdio(false); cin.tie(nullptr);</code> membuat <code>cin</code>/<code>cout</code> secepat <code>scanf</code>/<code>printf</code>. Tanpa baris ini, membaca 10<sup>6</sup> bilangan bisa memakan waktu lebih dari satu detik.</p>
TXT],
        ['Hitung isi rentang dengan dua binary search', <<<'CPP'
    string out;
    while (q--) {
        long long l, r;
        cin >> l >> r;
        auto kiri = lower_bound(a.begin(), a.end(), l);
        auto kanan = upper_bound(a.begin(), a.end(), r);
        out += to_string(kanan - kiri) + "\n";
    }

CPP, <<<'TXT'
<ul>
    <li><code>lower_bound(..., l)</code>: posisi pertama dengan nilai <strong>≥ l</strong>.</li>
    <li><code>upper_bound(..., r)</code>: posisi pertama dengan nilai <strong>&gt; r</strong>.</li>
</ul>
<p>Semua nilai di [l, r] berada tepat di antara kedua posisi itu, jadi banyaknya adalah selisih iteratornya. Setiap kueri O(log N).</p>
<div class="wt-tip">Jika l &gt; r atau tidak ada nilai di rentang, kanan − kiri bernilai 0 dengan sendirinya: tidak perlu kasus khusus.</div>
TXT],
        ['Cetak sekaligus', <<<'CPP'
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Mengumpulkan output di satu <code>string</code> lalu mencetaknya sekali lebih cepat daripada <code>cout</code> berkali-kali, terutama bila memakai <code>endl</code> (yang memaksa <em>flush</em> setiap baris). Gunakan <code>'\n'</code>, bukan <code>endl</code>.</p>
TXT],
    ];

    $stPy = <<<'PY'
import sys
from bisect import bisect_left, bisect_right

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    a = sorted(int(x) for x in data[2:2 + n])
    out = []
    k = 2 + n
    for _ in range(q):
        l, r = int(data[k]), int(data[k + 1]); k += 2
        out.append(bisect_right(a, r) - bisect_left(a, l))
    print("\n".join(map(str, out)))

main()
PY;

    $stSort = <<<'CPP'
vector<int> a = {5, 1, 4};
sort(a.begin(), a.end());                       // 1 4 5
sort(a.rbegin(), a.rend());                     // 5 4 1 (menurun)
sort(a.begin(), a.end(), greater<int>());      // juga menurun

// pair dan tuple dibandingkan elemen demi elemen: cara cepat mengurutkan dengan beberapa kunci
vector<pair<int, string>> nilai = {{90, "Budi"}, {85, "Ani"}, {90, "Ari"}};
sort(nilai.begin(), nilai.end());               // (85,Ani) (90,Ari) (90,Budi)

// Pembanding sendiri: skor menurun, lalu nama naik. WAJIB "kurang dari" ketat (tidak boleh <=).
sort(nilai.begin(), nilai.end(), [](const pair<int, string>& x, const pair<int, string>& y) {
    if (x.first != y.first) return x.first > y.first;
    return x.second < y.second;
});                                             // (90,Ari) (90,Budi) (85,Ani)
CPP;

    $stSet = <<<'CPP'
set<int> s = {5, 1, 9};          // terurut, tanpa kembar
s.insert(4);                      // O(log N)
s.count(9);                       // 1 jika ada, 0 jika tidak
s.erase(1);
int terkecil = *s.begin();        // 4
int terbesar = *s.rbegin();       // 9
auto it = s.lower_bound(6);       // elemen pertama >= 6 -> 9  (pakai fungsi MILIK set!)
if (it != s.begin()) {
    int sebelum = *prev(it);      // elemen terbesar < 6 -> 5
}

multiset<int> ms = {3, 3, 3, 7};  // boleh kembar
ms.erase(ms.find(3));             // hapus SATU salinan -> {3, 3, 7}
ms.erase(3);                      // hati-hati: hapus SEMUA salinan -> {7}

map<string, int> frek;            // kunci terurut
frek["apel"]++;                   // kunci baru otomatis bernilai 0 lalu ditambah
for (auto& [kata, f] : frek) {}   // iterasi menurut urutan kunci
CPP;

    $stPq = <<<'CPP'
priority_queue<int> pq;                               // max-heap: top() = terbesar
priority_queue<int, vector<int>, greater<int>> minpq; // min-heap: top() = terkecil
pq.push(5); pq.push(9); pq.push(2);
pq.top();        // 9, O(1)
pq.pop();        // buang 9, O(log N)

// Pasangan {jarak, simpul} pada Dijkstra: min-heap berdasarkan jarak dulu.
priority_queue<pair<long long, int>, vector<pair<long long, int>>, greater<>> antre;

deque<int> dq;                    // tambah/buang di depan dan belakang O(1)
dq.push_front(1); dq.push_back(2); dq.pop_front();
CPP;

    $stKompres = <<<'CPP'
// Ubah nilai sampai 10^9 menjadi peringkat 0..k-1 tanpa mengubah urutan.
vector<int> v = a;
sort(v.begin(), v.end());
v.erase(unique(v.begin(), v.end()), v.end());
for (int i = 0; i < n; i++)
    a[i] = lower_bound(v.begin(), v.end(), a[i]) - v.begin();
// nilai asli: v[a[i]]
CPP;

    $stHash = <<<'CPP'
// unordered_map rata-rata O(1), tetapi bisa diserang tes khusus menjadi O(N) per operasi.
// Lindungi dengan hash acak (splitmix64):
struct HashAman {
    static unsigned long long mix(unsigned long long x) {
        x += 0x9e3779b97f4a7c15ULL;
        x = (x ^ (x >> 30)) * 0xbf58476d1ce4e5b9ULL;
        x = (x ^ (x >> 27)) * 0x94d049bb133111ebULL;
        return x ^ (x >> 31);
    }
    size_t operator()(unsigned long long x) const {
        static const unsigned long long acak = chrono::steady_clock::now().time_since_epoch().count();
        return mix(x + acak);
    }
};

int main() {
    unordered_map<long long, int, HashAman> hitung;
    hitung.reserve(1 << 20);     // siapkan tempat agar tidak sering rehash
    hitung[123]++;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengurutkan dengan berbagai kunci memakai <code>sort</code>, <code>pair</code>, dan pembanding lambda.</li>
        <li>Memakai <code>lower_bound</code>/<code>upper_bound</code> untuk menjawab pertanyaan rentang pada data terurut.</li>
        <li>Memilih wadah yang tepat: <code>set</code>, <code>multiset</code>, <code>map</code>, <code>priority_queue</code>, <code>deque</code>.</li>
        <li>Melakukan <strong>kompresi koordinat</strong> dan menghindari jebakan STL yang sering membuat TLE atau WA.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'sort dan binary search bawaan', 'desc' => 'Dua alat yang dipakai di hampir setiap soal.'])

<section class="lesson-section" id="ide" data-toc="Mengapa STL">
    <h2>Jangan Menulis Ulang yang Sudah Ada</h2>
    <div class="prose">
        <p><em>Standard Template Library</em> (STL) berisi struktur data dan algoritma yang sudah teruji dan sangat cepat. Peserta kompetisi yang kuat tidak menulis ulang sort atau pohon seimbang; mereka hafal kapan memakai yang mana dan berapa biayanya.</p>
    </div>
    @include('lessons.code', ['cpp' => $stSort, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pembanding harus ketat.</strong> <code>return x.first &gt;= y.first;</code> melanggar aturan <code>sort</code> (strict weak ordering) dan bisa membuat program crash atau hasilnya kacau saat ada nilai kembar.</p>
    </div>
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Hitung Nilai di Rentang</h2>
    <div class="prose"><p>Input: N bilangan dan Q kueri [l, r]. Untuk setiap kueri, cetak banyak bilangan yang nilainya di antara l dan r (inklusif).</p></div>
    @include('lessons.walkthrough', [
        'title' => 'sort + lower_bound + upper_bound',
        'steps' => $stSteps,
        'sample' => ['input' => "7 5\n5 1 9 3 7 3 12\n3 7\n1 1\n10 11\n0 100\n8 8\n", 'output' => "4\n1\n0\n7\n0\n"],
        'py' => $stPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Setelah diurutkan: indeks 0..6 berisi 1 3 3 5 7 9 12.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Kueri</th><th>lower_bound(l)</th><th>upper_bound(r)</th><th>Selisih</th></tr>
            <tr class="ok"><td>[3, 7]</td><td>indeks 1 (nilai 3)</td><td>indeks 5 (nilai 9)</td><td>4</td></tr>
            <tr><td>[1, 1]</td><td>indeks 0</td><td>indeks 1</td><td>1</td></tr>
            <tr><td>[10, 11]</td><td>indeks 6 (nilai 12)</td><td>indeks 6</td><td>0</td></tr>
            <tr><td>[0, 100]</td><td>indeks 0</td><td>indeks 7 (akhir)</td><td>7</td></tr>
            <tr><td>[8, 8]</td><td>indeks 5</td><td>indeks 5</td><td>0</td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'wadah STL dan kompresi koordinat', 'desc' => 'Memilih struktur data yang tepat untuk setiap pekerjaan.'])

<section class="lesson-section" id="wadah" data-toc="Memilih Wadah">
    <h2>Memilih Wadah</h2>
    <table class="cx-table">
        <tr><th>Wadah</th><th>Operasi utama</th><th>Biaya</th><th>Kapan dipakai</th></tr>
        <tr><td><code>vector</code></td><td>akses [i], push_back</td><td>O(1)</td><td>Hampir selalu, sebagai array.</td></tr>
        <tr><td><code>deque</code></td><td>push/pop depan dan belakang</td><td>O(1)</td><td>BFS 0-1, jendela geser.</td></tr>
        <tr><td><code>set</code> / <code>multiset</code></td><td>insert, erase, lower_bound</td><td>O(log N)</td><td>Data dinamis yang harus tetap terurut.</td></tr>
        <tr><td><code>map</code></td><td>m[kunci]</td><td>O(log N)</td><td>Frekuensi, kunci besar/string, iterasi terurut.</td></tr>
        <tr><td><code>unordered_map</code></td><td>m[kunci]</td><td>O(1) rata-rata</td><td>Kunci banyak, urutan tidak penting.</td></tr>
        <tr><td><code>priority_queue</code></td><td>push, top, pop</td><td>O(log N)</td><td>Ambil terbesar/terkecil berulang (Dijkstra, greedy).</td></tr>
    </table>
    @include('lessons.code', ['cpp' => $stSet, 'js' => null, 'py' => null])
    @include('lessons.code', ['cpp' => $stPq, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompresi" data-toc="Kompresi Koordinat">
    <h2>Kompresi Koordinat</h2>
    <div class="prose">
        <p>Banyak teknik (array frekuensi, prefix sum, Fenwick tree) butuh nilai kecil sebagai indeks. Jika nilainya sampai 10<sup>9</sup> tetapi banyaknya hanya 2 · 10<sup>5</sup>, ganti setiap nilai dengan peringkatnya. Urutan relatif tetap sama, jadi jawaban yang hanya bergantung pada perbandingan tidak berubah.</p>
    </div>
    <div data-viz="kompresi"></div>
    @include('lessons.code', ['cpp' => $stKompres, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Jebakan STL">
    <h2>Jebakan STL yang Paling Sering</h2>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong><code>lower_bound(s.begin(), s.end(), x)</code> pada set adalah O(N)</strong>, karena iterator set tidak bisa melompat. Pakai fungsi milik set: <code>s.lower_bound(x)</code>, yang O(log N).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong><code>ms.erase(x)</code> pada multiset menghapus semua salinan x.</strong> Untuk menghapus satu: <code>ms.erase(ms.find(x))</code> (pastikan x ada).</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong><code>v.size() − 1</code> saat v kosong</strong> bernilai sangat besar karena <code>size()</code> tidak bertanda. Tulis <code>(int)v.size() − 1</code> atau periksa kosong lebih dulu.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong><code>m[k]</code> pada map membuat kunci baru</strong> jika k belum ada. Untuk sekadar memeriksa, pakai <code>m.count(k)</code> atau <code>m.find(k)</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'hash yang aman', 'desc' => 'unordered_map tanpa takut diserang.'])

<section class="lesson-section" id="hash" data-toc="unordered_map Aman">
    <h2>unordered_map yang Aman</h2>
    <div class="prose"><p>Fungsi hash bawaan untuk bilangan bulat sangat sederhana, sehingga penyusun soal (atau peserta lain di Codeforces) bisa membuat input yang membuat semua kunci jatuh ke wadah yang sama. Setiap operasi menjadi O(N) dan program TLE. Solusinya: campur kunci dengan bilangan acak saat program berjalan.</p></div>
    @include('lessons.code', ['cpp' => $stHash, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Banyak nilai di [l, r]</h4><p>sort, lalu upper_bound(r) − lower_bound(l).</p></div>
        <div class="pattern"><h4>Elemen terdekat dengan x</h4><p>s.lower_bound(x) dan prev-nya.</p></div>
        <div class="pattern"><h4>Ambil terbesar berulang</h4><p>priority_queue.</p></div>
        <div class="pattern"><h4>Hapus satu dari kumpulan kembar</h4><p>multiset + erase(find(x)).</p></div>
        <div class="pattern"><h4>Nilai besar sebagai indeks</h4><p>Kompresi koordinat.</p></div>
        <div class="pattern"><h4>Frekuensi kunci besar</h4><p>map, atau unordered_map dengan hash aman.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="upper_bound mencari elemen pertama > 3, yaitu 5 di indeks 3 (array 1 3 3 5).">
        <p class="quiz-q">Pada array terurut 1 3 3 5, <code>upper_bound(..., 3)</code> menunjuk indeks berapa?</p>
        <div class="quiz-options">
            <button class="quiz-option">1</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="erase dengan nilai menghapus semua salinan; erase dengan iterator menghapus satu.">
        <p class="quiz-q">multiset berisi {4, 4, 4}. Bagaimana menghapus tepat satu angka 4?</p>
        <div class="quiz-options">
            <button class="quiz-option">ms.erase(4)</button>
            <button class="quiz-option">ms.pop(4)</button>
            <button class="quiz-option">ms.erase(ms.find(4))</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Nilai 5, 100, 5, 7 memiliki tiga nilai berbeda 5 < 7 < 100, sehingga peringkatnya 0, 2, 0, 1.">
        <p class="quiz-q">Hasil kompresi koordinat dari 5 100 5 7 adalah…</p>
        <div class="quiz-options">
            <button class="quiz-option">0 2 0 1</button>
            <button class="quiz-option">0 3 1 2</button>
            <button class="quiz-option">1 3 1 2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
