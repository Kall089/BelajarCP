@php
    $geSteps = [
        ['Titik dan hasil kali silang', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

typedef long long ll;
struct P {
    ll x, y;
};

ll cross(const P& o, const P& a, const P& b) {
    return (a.x - o.x) * (b.y - o.y) - (a.y - o.y) * (b.x - o.x);
}

CPP, <<<'TXT'
<p><code>cross(O, A, B)</code> adalah hasil kali silang vektor OA dan OB. Tandanya menjawab "dari O menuju A, apakah B ada di kiri?":</p>
<ul>
    <li>&gt; 0: O → A → B belok <strong>kiri</strong> (berlawanan jarum jam).</li>
    <li>&lt; 0: belok <strong>kanan</strong>.</li>
    <li>= 0: ketiganya <strong>segaris</strong>.</li>
</ul>
<p>Semua dihitung dengan bilangan bulat, jadi tidak ada galat pembulatan. Koordinat sampai 10<sup>9</sup> membuat hasil kali sampai ±4 · 10<sup>18</sup>: masih muat di <code>long long</code>, tetapi <code>int</code> pasti meluap.</p>
TXT],
        ['Urutkan dan bangun rantai bawah', <<<'CPP'
vector<P> hull(vector<P> p) {
    sort(p.begin(), p.end(), [](const P& a, const P& b) {
        return a.x < b.x || (a.x == b.x && a.y < b.y);
    });
    p.erase(unique(p.begin(), p.end(), [](const P& a, const P& b) {
        return a.x == b.x && a.y == b.y;
    }), p.end());
    int n = p.size();
    if (n <= 2) return p;
    vector<P> h(2 * n);
    int k = 0;
    for (int i = 0; i < n; i++) {
        while (k >= 2 && cross(h[k - 2], h[k - 1], p[i]) <= 0) k--;
        h[k++] = p[i];
    }

CPP, <<<'TXT'
<p><strong>Monotone chain</strong> (Andrew): urutkan titik dari kiri ke kanan, buang titik kembar. Rantai bawah hull, jika ditelusuri dari kiri ke kanan, selalu belok kiri. Jadi setiap kali titik baru membuat belokan kanan atau lurus (<code>cross ≤ 0</code>), titik terakhir di stack pasti bukan sudut hull dan dibuang.</p>
<p>Memakai <code>≤ 0</code> berarti titik segaris di tepi hull ikut dibuang. Jika soal meminta titik segaris tetap disimpan, ganti menjadi <code>&lt; 0</code>, dan hati-hati dengan kasus semua titik segaris.</p>
TXT],
        ['Rantai atas dan hasil akhir', <<<'CPP'
    for (int i = n - 2, batas = k + 1; i >= 0; i--) {
        while (k >= batas && cross(h[k - 2], h[k - 1], p[i]) <= 0) k--;
        h[k++] = p[i];
    }
    h.resize(k - 1);
    return h;
}

CPP, <<<'TXT'
<p>Rantai atas dibangun dengan aturan yang sama dari kanan ke kiri. <code>batas</code> menjaga agar rantai bawah tidak ikut terhapus. Titik pertama muncul lagi di akhir (lingkaran tertutup), jadi dibuang dengan <code>k − 1</code>.</p>
<p>Hasilnya titik sudut hull berlawanan arah jarum jam, dimulai dari titik paling kiri-bawah. Setelah sort O(N log N), setiap titik masuk dan keluar stack paling banyak sekali per rantai: O(N).</p>
TXT],
        ['Program utama', <<<'CPP'
int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<P> p(n);
    for (auto& q : p) cin >> q.x >> q.y;
    vector<P> h = hull(p);
    cout << h.size() << '\n';
    for (auto& q : h) cout << q.x << ' ' << q.y << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Untuk semua titik di satu tempat, hasilnya 1 titik; jika semuanya segaris, hasilnya 2 titik ujung. Kasus-kasus kecil seperti ini sering menjadi tes jebakan.</p>
TXT],
    ];

    $gePy = <<<'PY'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n = int(data[0])
    p = sorted(set((int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(n)))
    if len(p) <= 2:
        h = p
    else:
        def cross(o, a, b):
            return (a[0] - o[0]) * (b[1] - o[1]) - (a[1] - o[1]) * (b[0] - o[0])
        def rantai(titik):
            st = []
            for q in titik:
                while len(st) >= 2 and cross(st[-2], st[-1], q) <= 0:
                    st.pop()
                st.append(q)
            return st
        bawah = rantai(p)
        atas = rantai(p[::-1])
        h = bawah[:-1] + atas[:-1]
    out = [str(len(h))] + [f"{x} {y}" for x, y in h]
    print("\n".join(out))

main()
PY;

    $geIntersect = <<<'CPP'
// Apakah ruas AB dan CD punya titik persekutuan (termasuk menyentuh di ujung)?
int arah(const P& a, const P& b, const P& c) {
    ll v = cross(a, b, c);
    return (v > 0) - (v < 0);                       // 1 kiri, -1 kanan, 0 segaris
}
bool diRuas(const P& p, const P& a, const P& b) {  // p segaris dengan ab: apakah di antara a dan b?
    return min(a.x, b.x) <= p.x && p.x <= max(a.x, b.x) &&
           min(a.y, b.y) <= p.y && p.y <= max(a.y, b.y);
}
bool berpotongan(P a, P b, P c, P d) {
    int d1 = arah(a, b, c), d2 = arah(a, b, d);
    int d3 = arah(c, d, a), d4 = arah(c, d, b);
    if (d1 * d2 < 0 && d3 * d4 < 0) return true;   // saling menyeberang sungguhan
    if (d1 == 0 && diRuas(c, a, b)) return true;   // ujung menempel / segaris bertumpuk
    if (d2 == 0 && diRuas(d, a, b)) return true;
    if (d3 == 0 && diRuas(a, c, d)) return true;
    if (d4 == 0 && diRuas(b, c, d)) return true;
    return false;
}
CPP;

    $geArea = <<<'CPP'
// Luas poligon sederhana (titik berurutan, searah atau berlawanan jarum jam): rumus shoelace.
ll luas2(const vector<P>& p) {                      // mengembalikan 2 × luas, selalu bulat
    ll s = 0;
    int n = p.size();
    for (int i = 0; i < n; i++) {
        const P& a = p[i];
        const P& b = p[(i + 1) % n];
        s += a.x * b.y - b.x * a.y;
    }
    return llabs(s);                                // tanda: + berlawanan jarum jam, - searah
}

// Titik di dalam poligon: 1 di dalam, 0 di tepi, -1 di luar. Sinar ke kanan dihitung perpotongannya.
int posisi(const vector<P>& p, P q) {
    int n = p.size();
    bool dalam = false;
    for (int i = 0; i < n; i++) {
        P a = p[i], b = p[(i + 1) % n];
        if (cross(a, b, q) == 0 && min(a.x, b.x) <= q.x && q.x <= max(a.x, b.x) &&
            min(a.y, b.y) <= q.y && q.y <= max(a.y, b.y)) return 0;
        if (a.y > b.y) swap(a, b);
        if (a.y <= q.y && q.y < b.y && cross(a, b, q) > 0) dalam = !dalam;   // sisi melintasi garis y = q.y di kanan q
    }
    return dalam ? 1 : -1;
}
CPP;

    $gePick = <<<'CPP'
// Teorema Pick untuk poligon bertitik sudut bulat: Luas = I + B/2 - 1,
// I = titik bulat di dalam, B = titik bulat di tepi. Jadi I = (2·Luas - B + 2) / 2.
ll titikTepi(const vector<P>& p) {
    ll B = 0;
    int n = p.size();
    for (int i = 0; i < n; i++) {
        P a = p[i], b = p[(i + 1) % n];
        B += __gcd(llabs(a.x - b.x), llabs(a.y - b.y));   // titik bulat pada sisi, tanpa ujung kedua
    }
    return B;
}
ll titikDalam = (luas2(p) - titikTepi(p) + 2) / 2;
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memakai <strong>hasil kali silang</strong> untuk orientasi tiga titik dengan bilangan bulat murni.</li>
        <li>Membangun <strong>convex hull</strong> dengan monotone chain dalam O(N log N).</li>
        <li>Memeriksa perpotongan dua ruas garis, termasuk kasus segaris dan ujung menempel.</li>
        <li>Menghitung luas poligon (shoelace), memeriksa titik di dalam poligon, dan memakai teorema Pick.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'hasil kali silang', 'desc' => 'Satu rumus yang menjawab hampir semua pertanyaan geometri dasar.'])

<section class="lesson-section" id="ide" data-toc="Cross Product">
    <h2>Kiri, Kanan, atau Lurus?</h2>
    <div class="prose">
        <p>Soal geometri terkenal menakutkan karena galat pembulatan dan banyak kasus khusus. Kabar baiknya: untuk koordinat bulat, hampir semua pertanyaan dasar bisa dijawab <strong>tanpa pembagian dan tanpa bilangan desimal</strong>, cukup dengan hasil kali silang.</p>
        <p>Untuk vektor u = (u<sub>x</sub>, u<sub>y</sub>) dan v = (v<sub>x</sub>, v<sub>y</sub>), <code>u × v = u<sub>x</sub>·v<sub>y</sub> − u<sub>y</sub>·v<sub>x</sub></code>. Nilainya sama dengan luas bertanda jajar genjang yang dibentuk u dan v: positif jika v berada di kiri u, negatif jika di kanan, nol jika sejajar.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>cross(O, A, B)</b><span>(A − O) × (B − O). Tanda = arah belokan O → A → B.</span></div>
        <div class="term"><b>dot(u, v)</b><span>u<sub>x</sub>v<sub>x</sub> + u<sub>y</sub>v<sub>y</sub>. Positif jika sudutnya lancip, nol jika tegak lurus.</span></div>
        <div class="term"><b>Luas segitiga</b><span>|cross(A, B, C)| / 2.</span></div>
        <div class="term"><b>Convex hull</b><span>Poligon cembung terkecil yang memuat semua titik, seperti karet gelang yang dilepas.</span></div>
    </div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Selama input bulat, tetaplah di bilangan bulat: bandingkan tanda cross product, simpan 2 × luas, dan hindari <code>atan2</code>, <code>sqrt</code>, atau pembagian kecuali benar-benar perlu.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'convex hull', 'desc' => 'Monotone chain: sort, lalu dua kali stack.'])

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Rantai hijau adalah isi stack. Saat titik baru (oranye) membuat belokan kanan, titik merah dibuang. Tekan <strong>Acak titik</strong> beberapa kali, termasuk sampai muncul titik segaris di tepi.</p></div>
    <div data-viz="hull"></div>
</section>

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    <div class="prose"><p>Input: N titik bulat. Cetak banyak titik sudut hull, lalu koordinatnya berlawanan arah jarum jam mulai dari titik paling kiri-bawah. Titik segaris di tepi tidak dihitung sebagai sudut.</p></div>
    @include('lessons.walkthrough', [
        'title' => 'Convex hull: monotone chain',
        'steps' => $geSteps,
        'sample' => ['input' => "12\n1 1\n3 0\n6 1\n8 3\n7 6\n4 7\n1 5\n3 3\n5 4\n5 2\n2 3\n6 5\n", 'output' => "7\n1 1\n3 0\n6 1\n8 3\n7 6\n4 7\n1 5\n"],
        'py' => $gePy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan: Rantai Bawah</h2>
    <div class="prose"><p>Lima titik pertama setelah diurutkan: (1, 1), (1, 5), (2, 3), (3, 0), (3, 3).</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Titik baru</th><th>Pemeriksaan</th><th>Aksi</th><th>Stack</th></tr>
            <tr><td>(1, 1)</td><td>stack &lt; 2</td><td>masuk</td><td>(1,1)</td></tr>
            <tr><td>(1, 5)</td><td>stack &lt; 2</td><td>masuk</td><td>(1,1) (1,5)</td></tr>
            <tr class="hl"><td>(2, 3)</td><td>cross((1,1), (1,5), (2,3)) = 0·2 − 4·1 = −4 ≤ 0</td><td>buang (1,5), masuk</td><td>(1,1) (2,3)</td></tr>
            <tr class="hl"><td>(3, 0)</td><td>cross((1,1), (2,3), (3,0)) = 1·(−1) − 2·2 = −5 ≤ 0</td><td>buang (2,3), masuk</td><td>(1,1) (3,0)</td></tr>
            <tr class="ok"><td>(3, 3)</td><td>cross((1,1), (3,0), (3,3)) = 2·2 − (−1)·2 = 6 &gt; 0</td><td>masuk</td><td>(1,1) (3,0) (3,3)</td></tr>
        </table>
    </div>
    <div class="prose"><p>(3, 3) belum tentu sudut hull: ia langsung dibuang saat (4, 7) datang, karena (3, 0) → (3, 3) → (4, 7) belok kanan. Stack hanya menjamin "sejauh ini selalu belok kiri".</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Operasi</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Orientasi, perpotongan ruas</td><td><code>O(1)</code></td><td>Bilangan bulat murni.</td></tr>
        <tr><td>Luas poligon, titik dalam poligon</td><td><code>O(N)</code></td><td>Satu putaran sisi.</td></tr>
        <tr><td>Convex hull</td><td><code>O(N log N)</code></td><td>Didominasi sort.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Overflow.</strong> Koordinat 10<sup>9</sup> membuat cross product ±4 · 10<sup>18</sup>; menjumlahkan beberapa hasil seperti itu (shoelace) bisa melewati batas <code>long long</code>. Perhatikan batas soal; gunakan <code>__int128</code> bila perlu.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Kasus kecil.</strong> Titik kembar, semua titik segaris, hanya satu atau dua titik: uji semuanya. Banyak solusi hull gagal di sini.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pakai double hanya jika terpaksa</strong>, dan bandingkan dengan toleransi: <code>fabs(a − b) &lt; 1e-9</code>, bukan <code>a == b</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'ruas garis dan poligon', 'desc' => 'Perpotongan, luas, titik di dalam, dan teorema Pick.'])

<section class="lesson-section" id="potong" data-toc="Perpotongan Ruas">
    <h2>Perpotongan Dua Ruas Garis</h2>
    <div class="prose"><p>Dua ruas saling menyeberang jika C dan D berada di sisi berbeda dari garis AB, <em>dan</em> A dan B berada di sisi berbeda dari garis CD. Sisanya kasus menempel: salah satu ujung segaris dengan ruas lain dan berada di antara kedua ujungnya.</p></div>
    @include('lessons.code', ['cpp' => $geIntersect, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="poligon" data-toc="Luas & Titik Dalam">
    <h2>Luas Poligon dan Titik di Dalam Poligon</h2>
    <div class="prose">
        <p><strong>Shoelace</strong>: jumlahkan cross product setiap sisi terhadap titik asal. Segitiga-segitiga "di luar" poligon saling meniadakan karena tandanya berlawanan, sehingga rumus ini berlaku untuk poligon tak cembung juga.</p>
        <p><strong>Titik di dalam</strong>: tarik sinar dari titik ke kanan dan hitung berapa sisi yang dipotongnya. Ganjil berarti di dalam. Aturan setengah terbuka (<code>a.y ≤ q.y &lt; b.y</code>) mencegah titik sudut dihitung dua kali.</p>
    </div>
    @include('lessons.code', ['cpp' => $geArea, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="pick" data-toc="Teorema Pick">
    <h2>Teorema Pick</h2>
    <div class="prose"><p>Untuk poligon dengan titik sudut bulat, banyak titik bulat di dalamnya bisa dihitung tanpa mengecek satu per satu. Banyak titik bulat pada sebuah sisi dari (x<sub>1</sub>, y<sub>1</sub>) ke (x<sub>2</sub>, y<sub>2</sub>), tanpa satu ujungnya, adalah FPB(|Δx|, |Δy|).</p></div>
    @include('lessons.code', ['cpp' => $gePick, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Belok kiri/kanan</h4><p>Tanda cross(A, B, C).</p></div>
        <div class="pattern"><h4>Ruas berpotongan</h4><p>Empat orientasi + kasus segaris.</p></div>
        <div class="pattern"><h4>Luas poligon</h4><p>Shoelace, simpan 2 × luas.</p></div>
        <div class="pattern"><h4>Titik dalam poligon</h4><p>Sinar ke kanan, hitung perpotongan.</p></div>
        <div class="pattern"><h4>Keliling/luas terkecil yang memuat semua titik</h4><p>Convex hull.</p></div>
        <div class="pattern"><h4>Titik bulat di dalam</h4><p>Teorema Pick + FPB untuk titik tepi.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="0" data-explain="cross((0,0), (2,0), (1,3)) = 2·3 − 0·1 = 6 > 0: belok kiri.">
        <p class="quiz-q">Robot berjalan dari (0, 0) ke (2, 0), lalu menuju (1, 3). Ke mana ia berbelok?</p>
        <div class="quiz-options">
            <button class="quiz-option">Kiri</button>
            <button class="quiz-option">Kanan</button>
            <button class="quiz-option">Lurus</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Rantai bawah yang benar selalu belok kiri; belokan kanan atau lurus berarti titik tengahnya berada di dalam atau di tepi hull.">
        <p class="quiz-q">Pada monotone chain, mengapa titik terakhir di stack dibuang saat cross ≤ 0?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena titiknya terlalu jauh</button>
            <button class="quiz-option">Karena stack sudah penuh</button>
            <button class="quiz-option">Karena belokan kanan/lurus berarti titik itu tidak mungkin menjadi sudut hull</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="1" data-explain="Shoelace: 0·0 − 4·0 + 4·3 − 0·0 + 0·0 − 0·3 = 12 = 2 × luas, jadi luas 6.">
        <p class="quiz-q">Berapa luas segitiga (0, 0), (4, 0), (0, 3)?</p>
        <div class="quiz-options">
            <button class="quiz-option">12</button>
            <button class="quiz-option">6</button>
            <button class="quiz-option">7</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
