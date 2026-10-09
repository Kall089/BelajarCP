@php
    $grSteps = [
        ['Baca rapat sebagai {selesai, mulai}', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<pair<int, int>> rapat(n);   // {selesai, mulai}
    for (int i = 0; i < n; i++) cin >> rapat[i].second >> rapat[i].first;

CPP, <<<'TXT'
<p>Input memberi <code>s e</code>, tetapi kita menyimpannya terbalik sebagai <code>{e, s}</code>. Alasannya: <code>sort</code> pada pasangan membandingkan elemen pertama lebih dulu, jadi rapat otomatis terurut menurut waktu selesai tanpa perlu menulis pembanding sendiri.</p>
TXT],
        ['Urutkan menurut waktu selesai', <<<'CPP'
    sort(rapat.begin(), rapat.end());

CPP, <<<'TXT'
<p>Inilah <strong>pilihan serakah</strong>-nya: dari semua rapat yang masih mungkin, selalu ambil yang <em>selesai paling awal</em>. Rapat itu meninggalkan ruangan kosong sedini mungkin, sehingga sisa waktu untuk rapat lain paling panjang.</p>
<p>Strategi yang terdengar masuk akal lainnya salah: "mulai paling awal" bisa memilih rapat yang sangat panjang, dan "paling pendek" bisa memilih rapat kecil yang memotong dua rapat lain. Coba keduanya di visualisasi.</p>
TXT],
        ['Ambil jika tidak bentrok', <<<'CPP'
    int jumlah = 0;
    int bebas = 0;   // ruangan kosong mulai menit ini
    for (auto [e, s] : rapat) {
        if (s >= bebas) {
            jumlah++;
            bebas = e;
        }
    }
    cout << jumlah << '\n';
    return 0;
}
CPP, <<<'TXT'
<p>Karena rapat diproses menurut waktu selesai, rapat yang sudah diambil semuanya selesai paling lambat pada <code>bebas</code>. Jadi cukup satu perbandingan: rapat baru tidak bentrok jika mulainya ≥ <code>bebas</code>. Tanda ≥ (bukan &gt;) karena selang setengah terbuka [s, e) boleh menempel.</p>
<p>Total O(N log N) untuk sort ditambah O(N) untuk satu kali lintasan.</p>
TXT],
    ];

    $grPy = <<<'PY'
import sys
data = sys.stdin.buffer.read().split()
n = int(data[0])
rapat = sorted((int(data[2 + 2 * i]), int(data[1 + 2 * i])) for i in range(n))  # (selesai, mulai)
jumlah, bebas = 0, 0
for e, s in rapat:
    if s >= bebas:
        jumlah += 1
        bebas = e
print(jumlah)
PY;

    $grKoin = <<<'CPP'
// Greedy koin: ambil koin terbesar yang masih muat, berulang-ulang.
int koinGreedy(vector<int> koin, int x) {
    sort(koin.rbegin(), koin.rend());
    int banyak = 0;
    for (int c : koin)
        while (x >= c) { x -= c; banyak++; }
    return banyak;
}
// koin {1, 5, 10, 25}, x = 63 -> 25+25+10+1+1+1 = 6 koin   (optimal)
// koin {1, 3, 4},      x = 6  -> 4+1+1        = 3 koin   (salah! 3+3 = 2 koin)
// Untuk sistem koin sembarang, pakai DP: dp[v] = min(dp[v - c] + 1).
CPP;

    $grSpt = <<<'CPP'
// Urutan pengerjaan dengan total waktu tunggu minimum: yang tercepat dulu.
sort(t.begin(), t.end());
long long sekarang = 0, total = 0;     // total bisa ~10^15: wajib long long
for (long long x : t) {
    sekarang += x;                     // pekerjaan ini selesai pada menit 'sekarang'
    total += sekarang;
}
CPP;

    $grHuff = <<<'CPP'
// Menggabungkan N tumpukan, biaya tiap penggabungan = jumlah keduanya.
// Selalu gabungkan dua yang terkecil (ide yang sama dengan kode Huffman).
priority_queue<long long, vector<long long>, greater<long long>> pq(a.begin(), a.end());
long long biaya = 0;
while (pq.size() > 1) {
    long long x = pq.top(); pq.pop();
    long long y = pq.top(); pq.pop();
    biaya += x + y;
    pq.push(x + y);                    // hasil gabungan kembali ikut bersaing
}
// a = {5, 2, 4, 2, 3}: 2+2=4, 3+4=7, 4+5=9, 7+9=16  ->  biaya 36
CPP;

    $grTenggat = <<<'CPP'
// Tugas {lama, tenggat}: maksimalkan banyak tugas yang selesai tepat waktu.
sort(tugas.begin(), tugas.end(), [](auto& a, auto& b) { return a.second < b.second; });
priority_queue<long long> dikerjakan;  // max-heap lama tugas yang sedang dipilih
long long waktu = 0;
for (auto [lama, tenggat] : tugas) {
    dikerjakan.push(lama);
    waktu += lama;
    if (waktu > tenggat) {             // terlambat: buang tugas terlama yang sudah dipilih
        waktu -= dikerjakan.top();
        dikerjakan.pop();
    }
}
// jawaban = dikerjakan.size()
CPP;

    $grCmp = <<<'CPP'
// Susun bilangan menjadi angka gabungan terbesar: {3, 30, 34, 5, 9} -> "9534330".
// Mengurutkan secara biasa salah ("30" sebelum "3"?). Bandingkan dua kemungkinan gabungan.
vector<string> a = {"3", "30", "34", "5", "9"};
sort(a.begin(), a.end(), [](const string& x, const string& y) {
    return x + y > y + x;              // x di depan jika "xy" lebih besar daripada "yx"
});
string hasil;
for (auto& s : a) hasil += s;
CPP;

    $grGen = <<<'CPP'
// gen.cpp: buat satu input acak kecil. Seed dari argumen agar tes bisa diulang.
#include <bits/stdc++.h>
using namespace std;

int main(int argc, char* argv[]) {
    mt19937 rng(atoi(argv[1]));
    int n = rng() % 6 + 1;
    cout << n << '\n';
    for (int i = 0; i < n; i++) {
        int s = rng() % 10;
        cout << s << ' ' << s + rng() % 5 + 1 << '\n';
    }
}

// brute.cpp: coba semua 2^n himpunan rapat, ambil yang valid dan terbesar.
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<int> s(n), e(n);
    for (int i = 0; i < n; i++) cin >> s[i] >> e[i];
    int best = 0;
    for (int m = 0; m < (1 << n); m++) {
        bool ok = true;
        for (int i = 0; i < n; i++)
            for (int j = i + 1; j < n; j++)
                if ((m >> i & 1) && (m >> j & 1) && s[i] < e[j] && s[j] < e[i]) ok = false;
        if (ok) best = max(best, __builtin_popcount(m));
    }
    cout << best << '\n';
}
CPP;

    $grSh = <<<'SH'
# uji.sh: bandingkan solusi greedy dengan brute force pada 1000 input kecil
g++ -O2 -o greedy greedy.cpp && g++ -O2 -o brute brute.cpp && g++ -O2 -o gen gen.cpp
for i in $(seq 1 1000); do
    ./gen $i > in.txt
    ./greedy < in.txt > out1.txt
    ./brute < in.txt > out2.txt
    if ! cmp -s out1.txt out2.txt; then
        echo "Beda pada tes $i:"; cat in.txt
        echo "greedy: $(cat out1.txt)   brute: $(cat out2.txt)"
        exit
    fi
done
echo "1000 tes cocok"
SH;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Mengenali soal yang bisa diselesaikan dengan <strong>pilihan terbaik sesaat</strong>, dan soal yang tidak bisa.</li>
        <li>Membuktikan greedy dengan <strong>argumen pertukaran</strong> (exchange argument).</li>
        <li>Menerapkan pola klasik: penjadwalan interval, urutan pengerjaan, gabung dua terkecil, tenggat waktu, dan pembanding khusus.</li>
        <li>Menguji greedy dengan <strong>stress test</strong> melawan brute force sebelum mengirim.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'pilihan terbaik sesaat', 'desc' => 'Ambil yang paling menguntungkan sekarang, jangan pernah menyesal.'])

<section class="lesson-section" id="ide" data-toc="Apa Itu Greedy">
    <h2>Apa Itu Greedy?</h2>
    <div class="prose">
        <p>Algoritma <strong>greedy</strong> (serakah) membangun jawaban langkah demi langkah. Di setiap langkah ia mengambil pilihan yang terlihat paling baik saat itu, lalu <em>tidak pernah</em> meninjaunya lagi. Tidak ada mundur seperti backtracking, tidak ada tabel seperti DP.</p>
        <p>Karena itu greedy biasanya sangat cepat (sering cukup sort + satu lintasan), tetapi juga paling mudah salah. Contoh klasik: memberi kembalian dengan koin sesedikit mungkin.</p>
    </div>
    @include('lessons.code', ['cpp' => $grKoin, 'js' => null, 'py' => null])
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Greedy hanya benar jika pilihan sesaat itu <strong>aman</strong>: selalu ada solusi optimal yang memuat pilihan tersebut. Keyakinan "kelihatannya masuk akal" tidak cukup; buktikan, atau setidaknya uji dengan brute force.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Pilihan serakah</b><span>Aturan sederhana untuk memilih langkah berikutnya, misalnya "selesai paling awal".</span></div>
        <div class="term"><b>Pilihan aman</b><span>Ada solusi optimal yang memuat pilihan itu, jadi mengambilnya tidak merugikan.</span></div>
        <div class="term"><b>Substruktur optimal</b><span>Setelah pilihan diambil, sisanya adalah soal yang sama tetapi lebih kecil.</span></div>
        <div class="term"><b>Argumen pertukaran</b><span>Bukti: tukar bagian solusi optimal dengan pilihan greedy tanpa membuatnya lebih buruk.</span></div>
    </div>
</section>

<section class="lesson-section" id="masalah" data-toc="Jadwal Rapat">
    <h2>Soal Klasik: Jadwal Rapat Terbanyak</h2>
    <div class="prose">
        <p>Satu ruangan, N permintaan rapat [s<sub>i</sub>, e<sub>i</sub>). Setujui sebanyak mungkin rapat tanpa ada dua yang bertabrakan. Mencoba semua himpunan butuh 2<sup>N</sup> langkah. Kita butuh aturan memilih yang selalu benar. Ada tiga calon:</p>
    </div>
    <table class="cx-table">
        <tr><th>Aturan</th><th>Benar?</th><th>Contoh yang menggagalkan</th></tr>
        <tr><td>Mulai paling awal</td><td>Salah</td><td>[0, 11) menutup [1, 5), [6, 10), dan lainnya.</td></tr>
        <tr><td>Paling pendek</td><td>Salah</td><td>[4, 7) memotong [1, 5) dan [6, 10) sekaligus.</td></tr>
        <tr><td>Paling sedikit bentrok</td><td>Salah</td><td>Ada contoh 11 rapat yang menggagalkannya (lebih sulit ditemukan).</td></tr>
        <tr class="ok"><td>Selesai paling awal</td><td>Benar</td><td>Dibuktikan di bawah dengan argumen pertukaran.</td></tr>
    </table>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi</h2>
    <div class="prose"><p>Lajur paling atas adalah ruangan; rapat yang diambil masuk ke sana. Jalankan dengan strategi <strong>Selesai awal</strong>, lalu ganti ke <strong>Mulai awal</strong> dan <strong>Terpendek</strong> dengan data yang sama dan bandingkan hasil akhirnya. Nyalakan Mode Tebak untuk menebak setiap keputusan ambil atau lewati.</p></div>
    <div data-viz="jadwal"></div>
</section>

<section class="lesson-section" id="bukti" data-toc="Argumen Pertukaran">
    <h2>Mengapa "Selesai Paling Awal" Benar?</h2>
    <div class="prose">
        <p>Misalkan g adalah rapat yang selesai paling awal, dan O adalah sembarang jadwal optimal dengan rapat-rapatnya diurutkan menurut waktu: o<sub>1</sub>, o<sub>2</sub>, …, o<sub>k</sub>.</p>
        <ol>
            <li>Ganti o<sub>1</sub> dengan g. Karena e(g) ≤ e(o<sub>1</sub>), dan o<sub>2</sub> mulai setelah o<sub>1</sub> selesai, maka o<sub>2</sub> juga mulai setelah g selesai. Jadwal baru tetap tidak bentrok dan isinya tetap k rapat, jadi <strong>tetap optimal</strong>.</li>
            <li>Artinya selalu ada jadwal optimal yang memuat g: pilihan serakah aman.</li>
            <li>Setelah g diambil, rapat yang tersisa hanyalah yang mulai ≥ e(g). Itu soal yang sama tetapi lebih kecil, sehingga argumen yang sama berlaku lagi (induksi).</li>
        </ol>
        <p>Pola bukti ini, "ambil solusi optimal, tukar satu bagian dengan pilihan greedy, tunjukkan tidak lebih buruk", disebut <strong>argumen pertukaran</strong>. Hampir semua bukti greedy di kompetisi memakai pola ini.</p>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'program lengkap', 'desc' => 'Sort menurut waktu selesai, lalu satu lintasan.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap</h2>
    @include('lessons.walkthrough', [
        'title' => 'Greedy: jadwal rapat terbanyak',
        'steps' => $grSteps,
        'sample' => ['input' => "8\n0 11\n1 5\n4 7\n6 10\n10 13\n11 14\n12 16\n14 17\n", 'output' => "4\n"],
        'py' => $grPy,
    ])
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Data yang sama dengan visualisasi, sudah diurutkan menurut waktu selesai.</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>Rapat [s, e)</th><th>bebas sebelum</th><th>s ≥ bebas?</th><th>Keputusan</th><th>bebas sesudah</th></tr>
            <tr class="ok"><td>[1, 5)</td><td>0</td><td>1 ≥ 0 ya</td><td>ambil (1)</td><td>5</td></tr>
            <tr><td>[4, 7)</td><td>5</td><td>4 ≥ 5 tidak</td><td>lewati</td><td>5</td></tr>
            <tr class="ok"><td>[6, 10)</td><td>5</td><td>6 ≥ 5 ya</td><td>ambil (2)</td><td>10</td></tr>
            <tr><td>[0, 11)</td><td>10</td><td>tidak</td><td>lewati</td><td>10</td></tr>
            <tr class="ok"><td>[10, 13)</td><td>10</td><td>10 ≥ 10 ya</td><td>ambil (3)</td><td>13</td></tr>
            <tr><td>[11, 14)</td><td>13</td><td>tidak</td><td>lewati</td><td>13</td></tr>
            <tr><td>[12, 16)</td><td>13</td><td>tidak</td><td>lewati</td><td>13</td></tr>
            <tr class="ok"><td>[14, 17)</td><td>13</td><td>14 ≥ 13 ya</td><td>ambil (4)</td><td>17</td></tr>
        </table>
    </div>
    <div class="prose"><p>Jawaban 4. Strategi "mulai paling awal" pada data ini hanya mendapat 3, karena langsung mengambil [0, 11).</p></div>
</section>

<section class="lesson-section" id="kompleksitas" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Bagian</th><th>Waktu</th><th>Catatan</th></tr>
        <tr><td>Sort</td><td><code>O(N log N)</code></td><td>Biasanya bagian terberat.</td></tr>
        <tr><td>Lintasan greedy</td><td><code>O(N)</code></td><td>Atau O(N log N) jika memakai priority_queue.</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Pembanding harus "kurang dari" yang ketat.</strong> Menulis <code>return a.e &lt;= b.e;</code> di pembanding <code>sort</code> melanggar aturan C++ dan bisa membuat program crash pada data dengan nilai kembar. Pakai <code>&lt;</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Perhatikan batas selang.</strong> [s, e) setengah terbuka boleh menempel (pakai ≥); selang tertutup [s, e] tidak boleh berbagi titik (pakai &gt;). Baca soal dengan teliti.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jumlah bisa meluap.</strong> Total waktu tunggu atau total biaya penggabungan mudah melebihi 2 · 10<sup>9</sup>. Gunakan <code>long long</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'pola-pola greedy klasik', 'desc' => 'Lima pola yang paling sering muncul, lengkap dengan alasannya.'])

<section class="lesson-section" id="spt" data-toc="Urutan Pengerjaan">
    <h2>1. Urutan Pengerjaan: Tercepat Dulu</h2>
    <div class="prose">
        <p>N pekerjaan dikerjakan bergantian pada satu mesin; minimalkan total waktu tunggu (waktu selesai setiap pekerjaan). Argumen pertukaran pada dua pekerjaan <em>bersebelahan</em> a lalu b: menukarnya hanya mengubah waktu selesai keduanya, dan total berubah sebesar t<sub>b</sub> − t<sub>a</sub>. Jika t<sub>a</sub> &gt; t<sub>b</sub>, menukar membuat total lebih kecil. Jadi pada urutan optimal tidak ada pasangan bersebelahan yang "terbalik": urut naik.</p>
    </div>
    @include('lessons.code', ['cpp' => $grSpt, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="huffman" data-toc="Gabung Dua Terkecil">
    <h2>2. Gabung Dua yang Terkecil</h2>
    <div class="prose">
        <p>Setiap kali menggabungkan, biayanya jumlah keduanya, dan hasilnya ikut digabung lagi nanti. Nilai yang digabung lebih awal akan "terbayar" berkali-kali, jadi yang kecil sebaiknya digabung duluan. Karena hasil gabungan kembali bersaing, gunakan <code>priority_queue</code> (min-heap) agar dua terkecil selalu bisa diambil dalam O(log N).</p>
    </div>
    @include('lessons.code', ['cpp' => $grHuff, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="tenggat" data-toc="Tenggat Waktu">
    <h2>3. Tenggat Waktu: Greedy dengan Penyesalan</h2>
    <div class="prose">
        <p>Setiap tugas punya lama dan tenggat; maksimalkan banyaknya tugas yang selesai tepat waktu. Proses tugas menurut tenggat (yang paling mendesak dulu) dan ambil semuanya. Saat ternyata terlambat, "menyesal": buang tugas <strong>terlama</strong> yang sudah diambil. Banyak tugas berkurang satu (tidak terhindarkan), tetapi waktu yang terpakai berkurang sebanyak mungkin, sehingga tugas berikutnya punya peluang terbesar.</p>
        <p>Pola "ambil dulu, buang yang terburuk bila melanggar" dengan heap ini sering muncul, misalnya juga pada soal "maksimalkan keuntungan dengan tenggat" (heap berisi keuntungan, buang yang terkecil).</p>
    </div>
    @include('lessons.code', ['cpp' => $grTenggat, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="pembanding" data-toc="Pembanding Khusus">
    <h2>4. Pembanding Khusus</h2>
    <div class="prose">
        <p>Kadang kunci pengurutan tidak bisa ditulis sebagai satu angka. Gunakan argumen pertukaran pada dua elemen bersebelahan x dan y: kapan x lebih baik ditaruh di depan y? Jawabannya langsung menjadi pembanding.</p>
    </div>
    @include('lessons.code', ['cpp' => $grCmp, 'js' => null, 'py' => null])
    <div class="callout note">
        <span class="callout-icon">💡</span>
        <p>Pola yang sama: menyusun balok agar tumpukan setinggi mungkin, mengurutkan pekerjaan dengan bobot (urut menurut t/w, dibandingkan sebagai <code>t<sub>a</sub> · w<sub>b</sub> &lt; t<sub>b</sub> · w<sub>a</sub></code> agar tanpa pecahan), atau urutan menyelesaikan misi agar tidak kehabisan tenaga.</p>
    </div>
</section>

<section class="lesson-section" id="stress" data-toc="Stress Test">
    <h2>5. Menguji Greedy: Stress Test</h2>
    <div class="prose">
        <p>Di kompetisi, membuktikan greedy secara formal sering memakan waktu. Kebiasaan peserta kuat: tulis brute force yang <em>pasti benar</em> (walaupun lambat), buat generator input kecil acak, lalu bandingkan ribuan kali. Jika greedy salah, biasanya contoh yang menggagalkannya ditemukan dalam hitungan detik, dan contoh itu kecil sehingga mudah dianalisis.</p>
    </div>
    @include('lessons.code', ['cpp' => $grGen, 'js' => null, 'py' => null])
    <pre class="snippet"><code class="language-bash">{{ $grSh }}</code></pre>
    <div class="pattern-grid">
        <div class="pattern"><h4>Interval terbanyak</h4><p>Urut selesai, ambil jika mulai ≥ bebas.</p></div>
        <div class="pattern"><h4>Titik penusuk interval</h4><p>Urut selesai, tusuk di ujung kanan interval pertama yang belum tertusuk.</p></div>
        <div class="pattern"><h4>Ruangan minimum</h4><p>Urut mulai, min-heap waktu selesai; jawabannya ukuran heap terbesar.</p></div>
        <div class="pattern"><h4>Total tunggu minimum</h4><p>Tercepat dulu (urut naik).</p></div>
        <div class="pattern"><h4>Gabung dengan biaya jumlah</h4><p>Min-heap, gabung dua terkecil.</p></div>
        <div class="pattern"><h4>Tenggat</h4><p>Urut tenggat, heap; buang yang terburuk saat melanggar.</p></div>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Kalau greedy gagal, pikirkan DP.</strong> Knapsack 0/1, koin dengan nilai sembarang, dan interval berbobot (setiap rapat punya nilai) tidak bisa diselesaikan greedy. Lihat materi DP untuk versi berbobotnya.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Greedy mengambil 4 lalu 1 + 1 (3 koin), padahal 3 + 3 hanya 2 koin.">
        <p class="quiz-q">Dengan koin {1, 3, 4}, berapa koin yang dipakai greedy "terbesar dulu" untuk membayar 6?</p>
        <div class="quiz-options">
            <button class="quiz-option">2</button>
            <button class="quiz-option">3</button>
            <button class="quiz-option">4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Menukar rapat pertama jadwal optimal dengan rapat yang selesai paling awal tidak menimbulkan bentrok baru, sehingga jumlahnya tetap.">
        <p class="quiz-q">Inti bukti bahwa "selesai paling awal" benar adalah…</p>
        <div class="quiz-options">
            <button class="quiz-option">Rapat pertama jadwal optimal bisa diganti rapat yang selesai paling awal tanpa merusak jadwal</button>
            <button class="quiz-option">Rapat yang selesai paling awal selalu yang paling pendek</button>
            <button class="quiz-option">Semua rapat pasti bisa diambil</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Hasil gabungan ikut bersaing lagi, jadi daftar berubah setiap langkah. Min-heap memberi dua terkecil dalam O(log N).">
        <p class="quiz-q">Mengapa soal "gabung dua terkecil" memakai priority_queue, bukan cukup sort sekali?</p>
        <div class="quiz-options">
            <button class="quiz-option">Karena sort tidak bisa mengurutkan long long</button>
            <button class="quiz-option">Karena priority_queue selalu lebih cepat daripada sort</button>
            <button class="quiz-option">Karena hasil setiap penggabungan masuk lagi dan harus ikut bersaing</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
