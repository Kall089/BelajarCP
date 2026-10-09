@php
    $sqSteps = [
        ['Bagi array menjadi blok', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int n, B;
vector<long long> a, blok;   // blok[k] = jumlah a[k*B .. k*B + B - 1]

CPP, <<<'TXT'
<p>Pilih ukuran blok B ≈ √N. Elemen ke-i berada di blok <code>i / B</code>. Selain array asli, simpan jumlah setiap blok. Ada sekitar N / B ≈ √N blok, dan setiap blok berisi B ≈ √N elemen.</p>
TXT],
        ['Update titik: O(1)', <<<'CPP'
void ubah(int i, long long v) {
    blok[i / B] += v - a[i];
    a[i] = v;
}

CPP, <<<'TXT'
<p>Mengganti a[i] cukup memperbaiki jumlah satu blok dengan selisihnya.</p>
TXT],
        ['Query rentang: O(√N)', <<<'CPP'
long long jumlah(int l, int r) {
    long long s = 0;
    while (l <= r && l % B != 0) s += a[l++];               // sisa kiri, satu per satu
    while (l + B - 1 <= r) { s += blok[l / B]; l += B; }    // blok utuh, sekaligus
    while (l <= r) s += a[l++];                              // sisa kanan
    return s;
}

CPP, <<<'TXT'
<p>Rentang [l, r] terdiri dari tiga bagian: potongan blok di kiri (paling banyak B − 1 elemen), sederet blok utuh (paling banyak N / B blok), dan potongan di kanan (paling banyak B − 1 elemen). Totalnya O(B + N / B), paling kecil saat B ≈ √N, yaitu O(√N).</p>
TXT],
        ['Program utama', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> n >> q;
    a.resize(n);
    for (auto& x : a) cin >> x;
    B = max(1, (int)sqrt(n));
    blok.assign(n / B + 1, 0);
    for (int i = 0; i < n; i++) blok[i / B] += a[i];

    string out;
    while (q--) {
        int t;
        cin >> t;
        if (t == 1) {
            int i;
            long long v;
            cin >> i >> v;
            ubah(i - 1, v);
        } else {
            int l, r;
            cin >> l >> r;
            out += to_string(jumlah(l - 1, r - 1)) + "\n";
        }
    }
    cout << out;
    return 0;
}
CPP, <<<'TXT'
<p>Input memakai indeks 1, program memakai indeks 0. Untuk N, Q = 2 · 10<sup>5</sup>, setiap query sekitar 900 langkah, total ±2 · 10<sup>8</sup> operasi sederhana: masih masuk 1–2 detik di C++. Segment tree lebih cepat (O(log N)), tetapi blok jauh lebih mudah diubah untuk operasi yang aneh.</p>
TXT],
    ];

    $sqPy = <<<'PY'
import sys
from math import isqrt

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    a = [int(x) for x in data[2:2 + n]]
    B = max(1, isqrt(n))
    blok = [0] * (n // B + 1)
    for i, x in enumerate(a):
        blok[i // B] += x
    out = []
    k = 2 + n
    for _ in range(q):
        if data[k] == b"1":
            i, v = int(data[k + 1]) - 1, int(data[k + 2])
            blok[i // B] += v - a[i]
            a[i] = v
        else:
            l, r = int(data[k + 1]) - 1, int(data[k + 2]) - 1
            s = 0
            while l <= r and l % B:
                s += a[l]; l += 1
            while l + B - 1 <= r:
                s += blok[l // B]; l += B
            while l <= r:
                s += a[l]; l += 1
            out.append(s)
        k += 3
    print("\n".join(map(str, out)))

main()
PY;

    $sqTag = <<<'CPP'
// Tambah v ke semua a[l..r], dan query jumlah. tag[k] = tambahan untuk SELURUH blok k.
// Nilai sebenarnya: a[i] + tag[i / B];  jumlah blok k: blok[k] + tag[k] * panjang(k).
void tambahRentang(int l, int r, long long v) {
    while (l <= r && l % B != 0) { a[l] += v; blok[l / B] += v; l++; }
    while (l + B - 1 <= r) { tag[l / B] += v; l += B; }      // blok utuh: cukup catat
    while (l <= r) { a[l] += v; blok[l / B] += v; l++; }
}

long long jumlahRentang(int l, int r) {
    long long s = 0;
    while (l <= r && l % B != 0) { s += a[l] + tag[l / B]; l++; }
    while (l + B - 1 <= r) { s += blok[l / B] + tag[l / B] * B; l += B; }
    while (l <= r) { s += a[l] + tag[l / B]; l++; }
    return s;
}
CPP;

    $sqMo = <<<'CPP'
// Mo: jawab Q kueri "banyak nilai berbeda di a[l..r]" secara offline. Nilai sudah dikompresi ke 0..N-1.
struct Kueri { int l, r, id; };

vector<int> mo(const vector<int>& a, vector<Kueri> q) {
    int n = a.size(), B = max(1, (int)(n / sqrt((double)q.size() + 1)));
    sort(q.begin(), q.end(), [&](const Kueri& x, const Kueri& y) {
        if (x.l / B != y.l / B) return x.l / B < y.l / B;
        return (x.l / B) % 2 ? x.r > y.r : x.r < y.r;    // trik ganjil-genap
    });
    vector<int> cnt(n, 0), jawab(q.size());
    int L = 0, R = -1, beda = 0;
    auto tambah = [&](int i) { if (cnt[a[i]]++ == 0) beda++; };
    auto hapus  = [&](int i) { if (--cnt[a[i]] == 0) beda--; };
    for (const Kueri& k : q) {
        while (R < k.r) tambah(++R);
        while (L > k.l) tambah(--L);
        while (R > k.r) hapus(R--);
        while (L < k.l) hapus(L++);
        jawab[k.id] = beda;
    }
    return jawab;
}
CPP;

    $sqHeavy = <<<'CPP'
// Q kueri (k, r): jumlah a[i] untuk semua i dengan i % k == r.
// k kecil (≤ S): jawaban sudah disiapkan di tabel.  k besar (> S): paling banyak N / S suku, hitung langsung.
int S = max(1, (int)sqrt(n));
vector<vector<long long>> pre(S + 1);
for (int k = 1; k <= S; k++) {
    pre[k].assign(k, 0);
    for (int i = 0; i < n; i++) pre[k][i % k] += a[i];      // O(N · S) persiapan
}
auto tanya = [&](int k, int r) -> long long {
    if (k <= S) return pre[k][r];                           // O(1)
    long long s = 0;
    for (int i = r; i < n; i += k) s += a[i];               // O(N / k) < O(√N)
    return s;
};
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Membagi array menjadi √N blok untuk query dan update rentang dalam O(√N).</li>
        <li>Memakai <strong>tag per blok</strong> untuk update rentang.</li>
        <li>Menjawab kueri rentang secara offline dengan <strong>algoritma Mo</strong> ketika informasinya sulit digabung.</li>
        <li>Memilih ambang √N untuk memisahkan kasus "berat" dan "ringan".</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'blok berukuran √N', 'desc' => 'Separuh kecepatan segment tree, sepersepuluh kerumitannya.'])

<section class="lesson-section" id="ide" data-toc="Ide Blok">
    <h2>Kompromi di Tengah</h2>
    <div class="prose">
        <p>Untuk query jumlah rentang dengan update titik, ada dua cara ekstrem. Array biasa: update O(1), query O(N). Prefix sum: query O(1), tetapi update O(N). Dekomposisi akar mengambil jalan tengah: kelompokkan elemen menjadi blok berisi B elemen dan simpan ringkasan setiap blok.</p>
        <p>Sebuah query menyentuh paling banyak 2B elemen di pinggir dan N / B blok utuh di tengah. Biaya B + N / B paling kecil saat B = √N, sehingga setiap operasi menjadi O(√N). Untuk N = 10<sup>5</sup>, itu sekitar 630 langkah, bukan 10<sup>5</sup>.</p>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Dekomposisi akar bersinar ketika <strong>ringkasan blok mudah dibuat ulang</strong> tetapi sulit digabung secara pohon: misalnya blok yang disimpan terurut untuk menghitung "berapa elemen ≥ x", atau operasi yang aneh. Kalau segment tree jelas bisa, pakai segment tree.</p>
    </div>
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Input: N dan Q, lalu array. Operasi <code>1 i v</code> mengganti a[i] menjadi v, operasi <code>2 l r</code> menanyakan a[l] + … + a[r].</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Dekomposisi akar: update titik, jumlah rentang',
        'steps' => $sqSteps,
        'sample' => ['input' => "8 7\n3 1 4 1 5 9 2 6\n2 1 8\n2 3 6\n1 5 0\n2 3 6\n2 2 2\n1 1 10\n2 1 3\n", 'output' => "31\n19\n14\n1\n15\n"],
        'py' => $sqPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: jumlah(2, 5) dengan B = 2</h2>
    <div class="prose"><p>Array (indeks 0) 3 1 4 1 5 9 2 6, blok: [3 1] [4 1] [5 9] [2 6], jumlah blok 4, 5, 14, 8. Query indeks 0-based [2, 5] (sama dengan <code>2 3 6</code> pada contoh):</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Bagian</th><th>Kondisi</th><th>Diambil</th><th>s</th></tr>
            <tr><td>Sisa kiri</td><td>l = 2, 2 % 2 = 0: langsung berhenti</td><td>–</td><td>0</td></tr>
            <tr class="hl"><td>Blok utuh</td><td>l = 2, l + 1 = 3 ≤ 5</td><td>blok[1] = 5</td><td>5</td></tr>
            <tr class="hl"><td>Blok utuh</td><td>l = 4, l + 1 = 5 ≤ 5</td><td>blok[2] = 14</td><td>19</td></tr>
            <tr class="ok"><td>Sisa kanan</td><td>l = 6 &gt; 5</td><td>–</td><td>19</td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'tag blok dan algoritma Mo', 'desc' => 'Update rentang, lalu kueri offline yang sulit digabung.'])

<section class="lesson-section" id="tag" data-toc="Tag per Blok">
    <h2>Update Rentang dengan Tag per Blok</h2>
    <div class="prose">
        <p>Ide yang sama dengan lazy propagation, tetapi jauh lebih sederhana: untuk blok yang tercakup utuh, jangan ubah elemennya satu per satu. Cukup catat "seluruh blok ini mendapat tambahan v" di <code>tag</code>. Elemen di pinggir diperbarui langsung.</p>
    </div>
    @include('lessons.code', ['cpp' => $sqTag, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="mo" data-toc="Algoritma Mo">
    <h2>Algoritma Mo</h2>
    <div class="prose">
        <p>Kueri "berapa nilai berbeda di [l, r]?" sulit untuk segment tree, karena himpunan nilai dua anak tidak bisa digabung dengan murah. Namun jika kita sudah tahu jawabannya untuk [L, R], mudah mendapatkan jawaban untuk [L, R + 1] atau [L + 1, R]: cukup perbarui <code>cnt</code> satu nilai.</p>
        <p>Jika semua kueri diketahui di awal (offline), urutkan agar penunjuk L dan R bergerak sesedikit mungkin. Mo mengelompokkan kueri menurut blok l, lalu mengurutkan menurut r. Di dalam satu blok, R hanya bergerak maju (total O(N) per blok, O(N√N) seluruhnya), sedangkan L hanya bergoyang di dalam blok (O(√N) per kueri).</p>
    </div>
    <div data-viz="mo"></div>
    @include('lessons.code', ['cpp' => $sqMo, 'js' => null, 'py' => null])
    <div class="callout note">
        <span class="callout-icon">💡</span>
        <p><strong>Trik ganjil-genap.</strong> Pada blok bernomor ganjil, urutkan r menurun. R tidak perlu kembali ke kiri setiap pindah blok, dan program biasanya hampir dua kali lebih cepat. Ukuran blok N / √Q juga lebih baik daripada √N ketika Q jauh lebih kecil atau lebih besar daripada N.</p>
    </div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Teknik</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Blok, update titik/rentang</td><td><code>O(√N)</code> per operasi</td><td>Online, mudah dimodifikasi.</td></tr>
        <tr><td>Mo</td><td><code>O((N + Q) √N)</code></td><td>Offline, tambah/hapus harus O(1).</td></tr>
        <tr><td>Segment tree</td><td><code>O(log N)</code></td><td>Butuh informasi yang bisa digabung.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Urutan geser penunjuk.</strong> Perluas dulu (R maju, L mundur), baru persempit. Jika menyempit dulu, jendela sempat "terbalik" (L &gt; R + 1) dan cnt bisa menjadi negatif.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Konstanta besar.</strong> 10<sup>5</sup> · 316 ≈ 3 · 10<sup>7</sup> geseran, masing-masing harus benar-benar O(1): pakai array, bukan <code>map</code>. Kompres nilai lebih dulu bila nilainya sampai 10<sup>9</sup>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'ambang √N', 'desc' => 'Memisahkan kasus berat dan ringan.'])

<section class="lesson-section" id="heavy" data-toc="Berat dan Ringan">
    <h2>Trik Berat–Ringan</h2>
    <div class="prose">
        <p>Akar kuadrat tidak hanya untuk blok. Banyak soal punya dua cara: satu cepat untuk parameter kecil, satu cepat untuk parameter besar. Pilih ambang S = √N, lalu pakai cara yang sesuai. Contoh: jumlah a[i] untuk semua i ≡ r (mod k). Untuk k kecil, siapkan tabel; untuk k besar, suku yang dijumlah paling banyak N / k &lt; √N.</p>
    </div>
    @include('lessons.code', ['cpp' => $sqHeavy, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Jumlah/min rentang + update</h4><p>Blok dengan ringkasan; atau segment tree.</p></div>
        <div class="pattern"><h4>Tambah rentang</h4><p>Tag per blok, pinggiran langsung.</p></div>
        <div class="pattern"><h4>Hitung elemen ≥ x di rentang</h4><p>Setiap blok disimpan terurut, binary search per blok.</p></div>
        <div class="pattern"><h4>Nilai berbeda / pasangan sama</h4><p>Mo dengan cnt.</p></div>
        <div class="pattern"><h4>Parameter kecil vs besar</h4><p>Tabel untuk ≤ √N, brute force untuk &gt; √N.</p></div>
        <div class="pattern"><h4>Simpul berderajat besar</h4><p>Paling banyak 2M / √M simpul berderajat ≥ √M; tangani khusus.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="Biaya B + N/B minimum saat keduanya sama, yaitu B = √N.">
        <p class="quiz-q">Mengapa ukuran blok dipilih sekitar √N?</p>
        <div class="quiz-options">
            <button class="quiz-option">Agar banyak blok selalu genap</button>
            <button class="quiz-option">Karena C++ menghitung sqrt dengan cepat</button>
            <button class="quiz-option">Karena biaya B (pinggiran) + N/B (blok utuh) paling kecil saat B = √N</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Mo membutuhkan semua kueri di awal untuk diurutkan.">
        <p class="quiz-q">Syarat utama agar algoritma Mo bisa dipakai?</p>
        <div class="quiz-options">
            <button class="quiz-option">Semua kueri diketahui di awal dan menambah/menghapus satu elemen bisa O(1)</button>
            <button class="quiz-option">Array tidak boleh berisi nilai yang sama</button>
            <button class="quiz-option">Kueri harus terurut menurut l</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Untuk k > √N, i = r, r + k, r + 2k, … paling banyak N/k < √N suku.">
        <p class="quiz-q">Pada trik berat–ringan untuk jumlah a[i] dengan i ≡ r (mod k), mengapa k &gt; √N boleh dihitung langsung?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena r selalu 0</button>
            <button class="quiz-option">Karena sukunya paling banyak N / k &lt; √N</button>
            <button class="quiz-option">Karena tabelnya sudah disiapkan</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
