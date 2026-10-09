@php
    $ieSteps = [
        ['Baca data', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    long long n;
    int k;
    cin >> n >> k;
    vector<long long> a(k);              // k bilangan prima berbeda
    for (auto& x : a) cin >> x;
CPP, <<<'TXT'
<p><strong>Soal contoh "Kelipatan Prima":</strong> diberikan n ≤ 10<sup>18</sup> dan k ≤ 20 bilangan prima berbeda. Ada berapa bilangan di 1..n yang habis dibagi <em>paling sedikit satu</em> dari prima-prima itu?</p>
<p>Menjumlahkan ⌊n/a<sub>i</sub>⌋ salah: bilangan seperti 10 (habis dibagi 2 dan 5) terhitung dua kali.</p>
TXT],
        ['Telusuri semua himpunan bagian', <<<'CPP'

    long long hasil = 0;
    for (int mask = 1; mask < (1 << k); mask++) {
        long long kali = 1;
        bool kebesaran = false;
        for (int i = 0; i < k; i++)
            if (mask >> i & 1) {
                if (kali > n / a[i]) {   // kali * a[i] > n: tidak ada kelipatannya di 1..n
                    kebesaran = true;
                    break;
                }
                kali *= a[i];
            }
        if (kebesaran) continue;
CPP, <<<'TXT'
<p>Setiap himpunan bagian S dari prima-prima itu menyumbang satu suku. Karena prima berbeda, KPK mereka adalah hasil kalinya. Hasil kali bisa meledak melewati 10<sup>18</sup>; cek <code>kali &gt; n / a[i]</code> <em>sebelum</em> mengalikan. Jika hasil kalinya &gt; n, sukunya 0 dan bisa dilewati.</p>
TXT],
        ['Tanda bergantian', <<<'CPP'
        long long banyak = n / kali;
        if (__builtin_popcount(mask) % 2 == 1) hasil += banyak;   // ukuran ganjil: tambah
        else hasil -= banyak;                                     // ukuran genap: kurang
    }
    cout << hasil << "\n";
    return 0;
}
CPP, <<<'TXT'
<p>Himpunan berukuran ganjil ditambah, berukuran genap dikurang. Total O(2<sup>k</sup> · k) ≈ 2·10<sup>7</sup> untuk k = 20.</p>
TXT],
    ];

    $iePy = <<<'PY'
n, k = map(int, input().split())
a = list(map(int, input().split()))
hasil = 0
for mask in range(1, 1 << k):
    kali = 1
    for i in range(k):
        if mask >> i & 1:
            kali *= a[i]
            if kali > n:
                break
    if kali <= n:
        hasil += (n // kali) if bin(mask).count("1") % 2 else -(n // kali)
print(hasil)
PY;

    $ieDer = <<<'CPP'
// Derangement: permutasi tanpa titik tetap (p[i] != i untuk semua i)
// Inklusi-eksklusi atas himpunan posisi yang "tetap": D(n) = Σ (-1)^i C(n, i) (n - i)!
// Bentuk rekurens yang lebih praktis:
D[0] = 1; D[1] = 0;
for (int i = 2; i <= n; i++) D[i] = (i - 1) * (D[i - 1] + D[i - 2]) % MOD;
CPP;

    $ieSurj = <<<'CPP'
// Banyak fungsi SURJEKTIF dari n benda ke k kotak (setiap kotak terisi):
// semua fungsi k^n, kurangi yang kotak i kosong, tambah yang dua kotak kosong, ...
long long surj = 0;
for (int i = 0; i <= k; i++) {
    long long suku = C(k, i) * pangkat(k - i, n) % MOD;
    surj = (i % 2 == 0) ? (surj + suku) % MOD : (surj - suku + MOD) % MOD;
}
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Menerapkan prinsip <strong>inklusi-eksklusi</strong> untuk 2, 3, dan k himpunan.</li>
        <li>Menghitung bilangan yang habis dibagi salah satu dari beberapa bilangan, atau yang <strong>koprima</strong> dengan m.</li>
        <li>Memakai <strong>komplemen</strong>: hitung yang "melanggar" lalu kurangkan.</li>
        <li>Menurunkan rumus <strong>derangement</strong> dan banyak fungsi surjektif.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'tambah, kurangi, tambah', 'desc' => 'Memperbaiki hitungan ganda dengan tanda bergantian.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Klub Sekolah">
    <h2>Intuisi: Anggota Klub Sekolah</h2>
    <div class="prose">
        <p>Di sebuah kelas, 20 siswa ikut klub matematika dan 15 ikut klub fisika. Berapa siswa yang ikut paling sedikit satu klub? Bukan 35, jika ada 6 siswa yang ikut keduanya: mereka terhitung dua kali. Jawabannya 20 + 15 − 6 = 29.</p>
        <p>Dengan tiga klub, mengurangi irisan berpasangan ternyata terlalu banyak: siswa yang ikut ketiga klub ditambah 3 kali, dikurang 3 kali, sehingga hilang. Tambahkan lagi irisan ketiganya. Pola umumnya: <strong>tambah</strong> himpunan tunggal, <strong>kurangi</strong> irisan berpasangan, <strong>tambah</strong> irisan bertiga, dan seterusnya.</p>
    </div>
    <div class="recurrence"><small>Inklusi-eksklusi</small>|A ∪ B|     = |A| + |B| − |A ∩ B|
|A ∪ B ∪ C| = |A| + |B| + |C| − |A∩B| − |A∩C| − |B∩C| + |A∩B∩C|
|A₁ ∪ … ∪ Aₖ| = Σ over S ≠ ∅  (−1)^(|S|+1) · |∩ Aᵢ untuk i ∈ S|</div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Habis Dibagi Salah Satu</h2>
    <div class="prose"><p>Setiap baris tabel adalah satu himpunan bagian pembagi. Kelipatan KPK-nya disorot di grid, lalu ditambah atau dikurang sesuai ukurannya.</p></div>
    <div data-viz="venn"></div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'inklusi-eksklusi di C++', 'desc' => 'Iterasi himpunan bagian dengan bitmask.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kelipatan Prima</h2>
    @include('lessons.walkthrough', [
        'title' => 'Bilangan yang habis dibagi salah satu prima',
        'steps' => $ieSteps,
        'sample' => ['input' => "20 2\n2 5\n", 'output' => "12\n"],
        'py' => $iePy,
    ])
    <div class="prose"><p>Contoh: ⌊20/2⌋ + ⌊20/5⌋ − ⌊20/10⌋ = 10 + 4 − 2 = 12.</p></div>
</section>

<section class="lesson-section" id="komplemen" data-toc="Komplemen & Derangement">
    <h2>Komplemen, Koprima, dan Derangement</h2>
    <div class="prose">
        <p><strong>Komplemen.</strong> "Bilangan di 1..n yang koprima dengan m" = n − (bilangan yang habis dibagi salah satu faktor prima m). Faktor prima berbeda dari m ≤ 10<sup>18</sup> paling banyak 15, jadi 2<sup>15</sup> suku saja.</p>
        <p><strong>Derangement.</strong> Berapa cara membagikan kado tukar-menukar sehingga tidak ada yang mendapat kadonya sendiri? Hitung semua permutasi, lalu inklusi-eksklusi atas himpunan orang yang "mendapat kadonya sendiri".</p>
    </div>
    @include('lessons.code', ['cpp' => $ieDer, 'js' => null, 'py' => null])
    <div class="prose"><p><strong>Surjeksi.</strong> Banyak cara memasukkan n bola berbeda ke k kotak berbeda sehingga tidak ada kotak kosong.</p></div>
    @include('lessons.code', ['cpp' => $ieSurj, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Ledakan suku.</strong> 2<sup>k</sup> himpunan bagian hanya aman untuk k ≤ 20-an. Jika k besar tetapi banyak suku bernilai nol (hasil kali &gt; n), pakai rekursi yang memangkas cabang begitu hasil kali melewati n.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pembagi tidak prima.</strong> Irisan "habis dibagi a dan b" adalah kelipatan KPK(a, b), bukan a·b. Untuk prima berbeda keduanya sama.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'mengapa tandanya bergantian', 'desc' => 'Setiap elemen dihitung tepat sekali.'])

<section class="lesson-section" id="bukti" data-toc="Bukti & Pola">
    <h2>Bukti dan Pola Lanjutan</h2>
    <div class="proof">
        <p>Ambil satu elemen x yang berada di tepat j ≥ 1 himpunan. Di rumus inklusi-eksklusi, x muncul di setiap irisan yang dipilih dari j himpunan itu: C(j, 1) kali dengan tanda +, C(j, 2) kali dengan tanda −, dan seterusnya. Totalnya</p>
        <p>C(j,1) − C(j,2) + C(j,3) − … = 1 − (1 − 1)<sup>j</sup> = 1,</p>
        <p>dengan teorema binomial. Jadi setiap elemen gabungan dihitung tepat sekali, dan elemen di luar semua himpunan (j = 0) tidak dihitung.</p>
    </div>
    <div class="pattern-grid">
        <div class="pattern"><h4>"Paling sedikit satu"</h4><p>Inklusi-eksklusi langsung atas himpunan bagian.</p></div>
        <div class="pattern"><h4>"Tidak ada yang …"</h4><p>Total dikurangi "paling sedikit satu melanggar".</p></div>
        <div class="pattern"><h4>Tepat k syarat</h4><p>Hitung "paling sedikit" dulu, lalu inklusi-eksklusi dengan koefisien binomial.</p></div>
        <div class="pattern"><h4>Syarat gcd</h4><p>Inklusi-eksklusi atas semua prima sekaligus = fungsi Möbius.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="2" data-explain="⌊100/3⌋ + ⌊100/5⌋ − ⌊100/15⌋ = 33 + 20 − 6 = 47.">
        <p class="quiz-q">Berapa bilangan di 1..100 yang habis dibagi 3 atau 5?</p>
        <div class="quiz-options">
            <button class="quiz-option">53</button>
            <button class="quiz-option">41</button>
            <button class="quiz-option">47</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="D(4) = 3 · (D(3) + D(2)) = 3 · (2 + 1) = 9.">
        <p class="quiz-q">Empat orang bertukar kado; tidak ada yang mendapat kadonya sendiri. Berapa cara?</p>
        <div class="quiz-options">
            <button class="quiz-option">9</button>
            <button class="quiz-option">12</button>
            <button class="quiz-option">24</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Faktor prima 12 adalah 2 dan 3. Koprima dengan 12 di 1..12: 12 − (6 + 4 − 2) = 4 (yaitu 1, 5, 7, 11).">
        <p class="quiz-q">Berapa bilangan di 1..12 yang koprima dengan 12?</p>
        <div class="quiz-options">
            <button class="quiz-option">6</button>
            <button class="quiz-option">4</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
