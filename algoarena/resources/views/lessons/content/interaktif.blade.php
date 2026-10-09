@php
    $itSteps = [
        ['Baca batas dari juri', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    long long n;
    cin >> n;                       // juri memberi tahu: rahasia ada di [1, n]
CPP, <<<'TXT'
<p><strong>Soal contoh "Tebak Angka":</strong> juri menyimpan bilangan rahasia X di [1, n]. Program boleh mencetak <code>? m</code>; juri menjawab <code>&lt;</code> jika X &lt; m, atau <code>&gt;=</code> jika X ≥ m. Setelah yakin, cetak <code>! X</code>. Paling banyak ⌈log<sub>2</sub> n⌉ pertanyaan.</p>
<p>Perhatikan: <strong>jangan</strong> pakai <code>ios::sync_with_stdio(false)</code> + <code>cin.tie(nullptr)</code> tanpa berpikir. <code>cin.tie</code> justru yang otomatis mem-flush <code>cout</code> sebelum membaca. Di sini kita mem-flush sendiri dengan <code>endl</code>.</p>
TXT],
        ['Tanya titik tengah', <<<'CPP'

    long long lo = 1, hi = n;       // invarian: X ada di [lo, hi]
    while (lo < hi) {
        long long mid = (lo + hi + 1) / 2;   // dibulatkan ke ATAS
        cout << "? " << mid << endl;         // endl = baris baru + flush
CPP, <<<'TXT'
<p>Pertanyaan harus benar-benar terkirim sebelum kita menunggu jawaban. Tanpa flush, teks "? mid" bisa tertahan di buffer program, juri tidak pernah menerimanya, dan keduanya saling menunggu sampai waktu habis (verdict <strong>Idleness Limit Exceeded</strong> atau TLE).</p>
TXT],
        ['Baca jawaban dan persempit', <<<'CPP'
        string jawab;
        cin >> jawab;
        if (jawab == "<") hi = mid - 1;      // X < mid
        else lo = mid;                       // X >= mid
    }
CPP, <<<'TXT'
<p>Karena cabang "&gt;=" memberi <code>lo = mid</code>, mid harus dibulatkan ke atas; jika tidak, saat hi = lo + 1 kita akan menanyakan lo terus dan loopnya tidak pernah selesai.</p>
<p>Setiap jawaban membuang setengah kemungkinan, jadi n = 10<sup>9</sup> hanya butuh 30 pertanyaan.</p>
TXT],
        ['Laporkan jawaban', <<<'CPP'

    cout << "! " << lo << endl;
    return 0;
}
CPP, <<<'TXT'
<p>Jawaban akhir juga harus di-flush. Setelah itu program langsung selesai.</p>
<div class="wt-warn">Contoh di bawah adalah <em>rekaman</em> percakapan untuk X = 73: semua jawaban juri sudah ditulis di input, dan output berisi pertanyaan-pertanyaan program. Di kontes sungguhan, jawaban juri baru muncul setelah pertanyaannya terkirim.</div>
TXT],
    ];

    $itPy = <<<'PY'
n = int(input())
lo, hi = 1, n
while lo < hi:
    mid = (lo + hi + 1) // 2
    print("?", mid, flush=True)      # flush=True wajib
    jawab = input().strip()
    if jawab == "<":
        hi = mid - 1
    else:
        lo = mid
print("!", lo, flush=True)
PY;

    $itJs = <<<'JS'
// Di AlgoArena, readLine() membaca baris berikutnya dari input (rekaman jawaban juri)
const n = Number(readLine());
let lo = 1, hi = n;
while (lo < hi) {
    const mid = Math.floor((lo + hi + 1) / 2);
    print("? " + mid);
    const jawab = readLine().trim();
    if (jawab === "<") hi = mid - 1; else lo = mid;
}
print("! " + lo);
JS;

    $itFlush = <<<'CPP'
// Cara mem-flush output di C++
cout << "? " << mid << endl;              // endl: '\n' + flush
cout << "? " << mid << '\n' << flush;     // sama saja
printf("? %lld\n", mid); fflush(stdout);  // gaya C
// Python : print("?", mid, flush=True)   atau  sys.stdout.flush()
CPP;

    $itLocal = <<<'CPP'
// Menguji sendiri tanpa juri: bungkus pertanyaan dalam satu fungsi
#ifdef LOKAL
long long RAHASIA = 73;  int dipakai = 0;
string tanya(long long m) {                 // juri palsu di dalam program
    dipakai++;
    return RAHASIA < m ? "<" : ">=";
}
#else
string tanya(long long m) {                 // versi kontes: benar-benar bertanya
    cout << "? " << m << endl;
    string r;  cin >> r;
    if (r == "-1") exit(0);                 // juri bilang ada yang salah: berhenti
    return r;
}
#endif
// Kompilasi lokal dengan: g++ -DLOKAL sol.cpp, lalu coba banyak RAHASIA
// dan pastikan 'dipakai' tidak melebihi anggaran.
CPP;

    $itSort = <<<'CPP'
// Pola: urutkan n benda tersembunyi dengan pertanyaan "? a b" (apakah a lebih ringan dari b?)
vector<int> id(n);
iota(id.begin(), id.end(), 1);
stable_sort(id.begin(), id.end(), [](int a, int b) {
    cout << "? " << a << " " << b << endl;
    string r;  cin >> r;
    return r == "YA";            // juri harus konsisten, jika tidak hasil sort tak terdefinisi
});
// stable_sort (merge sort) memakai paling banyak sekitar n·log2(n) perbandingan:
// n = 1000 → ± 10 000 pertanyaan
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Memahami <strong>protokol</strong> soal interaktif: program bertanya, juri menjawab, bergantian.</li>
        <li>Selalu <strong>mem-flush</strong> output dan tahu akibatnya jika lupa.</li>
        <li>Menghitung <strong>anggaran pertanyaan</strong> dengan batas informasi ⌈log<sub>k</sub> M⌉.</li>
        <li>Menulis binary search interaktif dan <strong>menguji</strong> solusi interaktif dengan juri palsu.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'bercakap dengan juri', 'desc' => 'Input tidak lagi datang sekaligus; ia menjawab pertanyaanmu.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Permainan Tebak Angka">
    <h2>Intuisi: Permainan Tebak Angka</h2>
    <div class="prose">
        <p>Di soal biasa, seluruh input sudah tersedia sejak awal. Di <strong>soal interaktif</strong>, sebagian informasi disembunyikan oleh juri. Programmu harus <em>bertanya</em>, dan setiap jawaban baru muncul setelah pertanyaannya sampai.</p>
        <p>Ini persis permainan tebak angka bersama teman: "Apakah angkanya ≥ 50?" "Ya." "≥ 75?" "Tidak." Dengan strategi membelah dua, angka 1–100 selalu tertebak dalam 7 pertanyaan.</p>
        <p>Batas jumlah pertanyaan adalah inti soalnya. Juri akan menolak jawaban jika kamu bertanya terlalu banyak, meskipun jawabanmu benar.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>Juri (interactor)</b><span>Program milik penyelenggara yang menyimpan rahasia dan menjawab pertanyaan.</span></div>
        <div class="term"><b>Flush</b><span>Memaksa output di buffer benar-benar dikirim. Wajib setelah setiap pertanyaan.</span></div>
        <div class="term"><b>Anggaran</b><span>Batas banyaknya pertanyaan. Melewatinya = Wrong Answer.</span></div>
        <div class="term"><b>Juri adaptif</b><span>Juri yang memilih rahasianya belakangan agar strategimu bertemu kasus terburuk.</span></div>
    </div>
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi">
    <h2>Visualisasi: Transkrip Percakapan</h2>
    <div class="prose"><p>Ubah N dan angka rahasia, lalu lihat bagaimana rentang kemungkinan menyusut setiap kali juri menjawab. Di Mode Tebak, kamulah yang berperan sebagai juri.</p></div>
    <div data-viz="interactive"></div>
</section>

<section class="lesson-section" id="protokol" data-toc="Aturan Protokol">
    <h2>Aturan Protokol yang Harus Dipatuhi</h2>
    <div class="steps">
        <div class="step-card"><b>Baca format persis</b><span>Apa yang dicetak untuk bertanya (<code>? m</code>) dan untuk menjawab (<code>! x</code>), serta apa saja kemungkinan balasan juri.</span></div>
        <div class="step-card"><b>Flush setiap kali</b><span>Setelah setiap baris yang harus dibaca juri, termasuk jawaban akhir.</span></div>
        <div class="step-card"><b>Baca semua balasan</b><span>Satu pertanyaan, satu kali baca. Jangan membaca melebihi yang dikirim juri; program akan macet menunggu.</span></div>
        <div class="step-card"><b>Hitung anggaran</b><span>Pastikan strategi terburukmu tidak melewati batas, sebelum mulai menulis kode.</span></div>
        <div class="step-card"><b>Berhenti jika -1</b><span>Banyak juri membalas <code>-1</code> jika ada yang salah. Langsung keluar agar verdict-nya WA, bukan hal aneh lain.</span></div>
    </div>
    @include('lessons.code', ['cpp' => $itFlush, 'js' => null, 'py' => null])
</section>

@include('lessons.level', ['n' => 2, 'title' => 'binary search interaktif', 'desc' => 'Program lengkap, cara mengujinya, dan menghitung anggaran.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Tebak Angka</h2>
    @include('lessons.walkthrough', [
        'title' => 'Binary search lewat pertanyaan',
        'steps' => $itSteps,
        'sample' => ['input' => "100\n>=\n<\n>=\n>=\n>=\n<\n>=\n", 'output' => "? 51\n? 76\n? 63\n? 69\n? 72\n? 74\n? 73\n! 73\n"],
        'py' => $itPy,
        'js' => $itJs,
    ])
    <table class="trace-table">
        <tr><th>[lo, hi]</th><th>tanya</th><th>juri (X = 73)</th><th>sisa</th></tr>
        <tr><td>[1, 100]</td><td>? 51</td><td>&gt;=</td><td>[51, 100]</td></tr>
        <tr><td>[51, 100]</td><td>? 76</td><td>&lt;</td><td>[51, 75]</td></tr>
        <tr><td>[51, 75]</td><td>? 63</td><td>&gt;=</td><td>[63, 75]</td></tr>
        <tr><td>[63, 75]</td><td>? 69</td><td>&gt;=</td><td>[69, 75]</td></tr>
        <tr><td>[69, 75]</td><td>? 72</td><td>&gt;=</td><td>[72, 75]</td></tr>
        <tr><td>[72, 75]</td><td>? 74</td><td>&lt;</td><td>[72, 73]</td></tr>
        <tr class="ok"><td>[72, 73]</td><td>? 73</td><td>&gt;=</td><td>[73, 73] → <b>! 73</b></td></tr>
    </table>
</section>

<section class="lesson-section" id="anggaran" data-toc="Menghitung Anggaran">
    <h2>Menghitung Anggaran: Batas Informasi</h2>
    <div class="prose">
        <p>Misalkan ada M kemungkinan jawaban dan setiap balasan juri punya k kemungkinan (ya/tidak: k = 2; timbangan kiri/kanan/seimbang: k = 3). Setelah q pertanyaan, paling banyak k<sup>q</sup> percakapan berbeda bisa terjadi, dan setiap percakapan hanya bisa berakhir dengan satu jawaban. Jadi kita butuh k<sup>q</sup> ≥ M.</p>
    </div>
    <div class="recurrence"><small>Batas bawah banyak pertanyaan</small>q ≥ ⌈log_k M⌉
tebak angka 1..10^9, jawaban ya/tidak     : q ≥ 30
koin palsu (lebih berat) di antara 27 koin : q ≥ log_3 27 = 3   (timbangan: kiri / kanan / seimbang)
urutkan n benda dengan "? a b"           : q ≥ log_2(n!) ≈ n log_2 n − 1.44 n</div>
    <div class="callout key">
        <span class="callout-icon">🔑</span>
        <p>Jika anggaran di soal sama persis dengan ⌈log<sub>k</sub> M⌉, setiap pertanyaanmu harus membagi kemungkinan yang tersisa <strong>serata mungkin</strong> menjadi k bagian. Itu petunjuk kuat tentang strategi yang diharapkan.</p>
    </div>
</section>

<section class="lesson-section" id="uji" data-toc="Menguji Sendiri">
    <h2>Menguji Solusi Interaktif Sendiri</h2>
    <div class="prose"><p>Kamu tidak bisa sekadar menempelkan contoh input, karena jawaban juri bergantung pada pertanyaanmu. Trik paling praktis: kumpulkan semua komunikasi dalam satu fungsi <code>tanya()</code>, lalu buat versi lokal yang menjawab dari rahasia yang kamu tentukan sendiri.</p></div>
    @include('lessons.code', ['cpp' => $itLocal, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">💡</span>
        <p>Dengan juri palsu, kamu bisa mencoba <em>semua</em> rahasia untuk n kecil dan memeriksa dua hal: jawabannya benar, dan banyak pertanyaan tidak pernah melewati anggaran.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Jangan mencetak debug ke stdout.</strong> Juri akan membacanya sebagai pertanyaan. Pakai <code>cerr</code> untuk pesan debug.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'pola soal interaktif', 'desc' => 'Mengurutkan lewat pertanyaan, juri adaptif, dan pola yang sering muncul.'])

<section class="lesson-section" id="pola" data-toc="Pola Soal Interaktif">
    <h2>Pola Soal Interaktif</h2>
    <div class="pattern-grid">
        <div class="pattern"><h4>Binary search jawaban</h4><p>Tebak angka, cari posisi pertama yang memenuhi syarat. ⌈log<sub>2</sub> n⌉ pertanyaan.</p></div>
        <div class="pattern"><h4>Ternary / bandingkan tetangga</h4><p>Cari puncak array unimodal: bandingkan a[mid] dan a[mid+1].</p></div>
        <div class="pattern"><h4>Urutkan lewat pertanyaan</h4><p>Pakai sort biasa dengan comparator yang bertanya ke juri.</p></div>
        <div class="pattern"><h4>Tiga kemungkinan balasan</h4><p>Timbangan: bagi menjadi tiga kelompok, ⌈log<sub>3</sub> n⌉.</p></div>
        <div class="pattern"><h4>Tanya jumlah / XOR</h4><p>Tanya prefix atau subset untuk merekonstruksi array tersembunyi.</p></div>
        <div class="pattern"><h4>Juri adaptif</h4><p>Strategi harus benar untuk semua rahasia; strategi acak tidak bisa "beruntung".</p></div>
    </div>
    <div class="prose"><p>Mengurutkan benda tersembunyi adalah contoh yang bagus: algoritma sorting tidak peduli dari mana hasil perbandingannya berasal. Merge sort memakai paling banyak sekitar n log<sub>2</sub> n perbandingan, dekat dengan batas informasi log<sub>2</sub>(n!).</p></div>
    @include('lessons.code', ['cpp' => $itSort, 'js' => null, 'py' => null])
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Juri adaptif</strong> tidak menyimpan rahasia sejak awal. Ia hanya memastikan jawabannya tetap konsisten dengan <em>suatu</em> rahasia, lalu memilih jawaban yang paling merugikanmu. Jadi hitung anggaran untuk kasus terburuk, bukan rata-rata.</p>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Tanpa flush, pertanyaan bisa tertahan di buffer. Juri menunggu pertanyaan, program menunggu jawaban: keduanya macet sampai batas waktu.">
        <p class="quiz-q">Program lupa mem-flush setelah mencetak "? 50". Apa yang paling mungkin terjadi?</p>
        <div class="quiz-options">
            <button class="quiz-option">Compile Error</button>
            <button class="quiz-option">Program dan juri saling menunggu sampai waktu habis</button>
            <button class="quiz-option">Juri menjawab dua kali</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="2^9 = 512 < 1000 ≤ 1024 = 2^10, jadi butuh 10 pertanyaan ya/tidak.">
        <p class="quiz-q">Rahasia ada di [1, 1000], juri hanya menjawab ya/tidak. Anggaran minimum yang selalu cukup?</p>
        <div class="quiz-options">
            <button class="quiz-option">9</button>
            <button class="quiz-option">7</button>
            <button class="quiz-option">10</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Timbangan punya tiga hasil (kiri berat, kanan berat, seimbang). 3^3 = 27 ≥ 27, dan 3^2 = 9 < 27, jadi 3 kali menimbang.">
        <p class="quiz-q">Ada 27 koin, satu lebih berat. Dengan timbangan dua lengan, berapa kali menimbang yang selalu cukup?</p>
        <div class="quiz-options">
            <button class="quiz-option">3</button>
            <button class="quiz-option">5</button>
            <button class="quiz-option">4</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
