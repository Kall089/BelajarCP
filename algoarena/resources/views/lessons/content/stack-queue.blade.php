@php
    $sqSteps = [
        ['Mencocokkan pasangan', <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

bool cocok(char buka, char tutup) {
    return (buka == '(' && tutup == ')') ||
           (buka == '[' && tutup == ']') ||
           (buka == '{' && tutup == '}');
}
CPP, <<<'TXT'
<p><strong>Soal contoh "Kurung Seimbang":</strong> diberikan beberapa string berisi <code>()[]{}</code>. Untuk setiap string, cetak <code>YA</code> jika semua kurung berpasangan dengan benar, atau <code>TIDAK</code> jika tidak.</p>
<p>Fungsi kecil ini hanya menjawab: apakah kurung tutup <code>tutup</code> menutup kurung buka <code>buka</code>?</p>
TXT],
        ['Stack menyimpan kurung yang belum ditutup', <<<'CPP'

bool seimbang(const string& s) {
    stack<char> st;
    for (char c : s) {
        if (c == '(' || c == '[' || c == '{') {
            st.push(c);
CPP, <<<'TXT'
<p>Setiap kurung buka <em>menunggu</em> pasangannya. Yang harus ditutup lebih dulu adalah kurung buka yang <strong>paling baru</strong>: <code>{ [ (</code> harus ditutup <code>) ] }</code>. "Paling baru keluar lebih dulu" adalah definisi stack (LIFO).</p>
TXT],
        ['Kurung tutup memeriksa top()', <<<'CPP'
        } else {
            if (st.empty() || !cocok(st.top(), c)) return false;
            st.pop();
        }
    }
CPP, <<<'TXT'
<p>Dua cara gagal ketika bertemu kurung tutup:</p>
<ul>
    <li>Stack <strong>kosong</strong>: tidak ada yang bisa ditutup, misalnya <code>())</code>.</li>
    <li><code>top()</code> <strong>tidak cocok</strong>, misalnya <code>(]</code>.</li>
</ul>
<p>Urutan pemeriksaan penting: <code>st.empty()</code> dicek lebih dulu. Memanggil <code>top()</code> pada stack kosong adalah perilaku tak terdefinisi (biasanya crash).</p>
TXT],
        ['Sisa di stack', <<<'CPP'
    return st.empty();
}
CPP, <<<'TXT'
<p>String habis, tetapi masih ada kurung buka di stack? Berarti ada yang tidak pernah ditutup, misalnya <code>((()</code>. String seimbang hanya jika stack <strong>kosong</strong> di akhir.</p>
TXT],
        ['Program utama', <<<'CPP'

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int t;
    cin >> t;
    while (t--) {
        string s;
        cin >> s;
        cout << (seimbang(s) ? "YA" : "TIDAK") << '\n';
    }
    return 0;
}
CPP, <<<'TXT'
<p>Setiap karakter di-push dan di-pop paling banyak sekali, jadi satu string diperiksa dalam <code>O(|s|)</code>.</p>
TXT],
    ];

    $sqPy = <<<'PY'
import sys
pasangan = {')': '(', ']': '[', '}': '{'}

def seimbang(s):
    st = []                      # list Python sebagai stack
    for c in s:
        if c in '([{':
            st.append(c)
        else:
            if not st or st[-1] != pasangan[c]:
                return False
            st.pop()
    return not st

data = sys.stdin.read().split()
t = int(data[0])
print("\n".join("YA" if seimbang(s) else "TIDAK" for s in data[1:1 + t]))
PY;

    $sqOps = <<<'CPP'
stack<int> st;                // LIFO: masuk terakhir, keluar pertama
st.push(5); st.top(); st.pop(); st.empty(); st.size();

queue<int> q;                 // FIFO: masuk pertama, keluar pertama
q.push(5); q.front(); q.back(); q.pop();

deque<int> d;                 // dua ujung, plus akses indeks d[i]
d.push_back(1); d.push_front(2);
d.pop_back();   d.pop_front();
d.front(); d.back(); d[0];

// Semua operasi di atas O(1).
// Catatan: pop() TIDAK mengembalikan nilai. Ambil dulu top()/front(), baru pop().
CPP;

    $sqPostfix = <<<'CPP'
// Evaluasi ekspresi postfix: "3 4 + 2 *" = (3 + 4) * 2 = 14
long long hitung(const vector<string>& token) {
    stack<long long> st;
    for (const string& t : token) {
        if (t == "+" || t == "-" || t == "*") {
            long long b = st.top(); st.pop();   // operan KANAN keluar duluan
            long long a = st.top(); st.pop();
            if (t == "+") st.push(a + b);
            else if (t == "-") st.push(a - b);
            else st.push(a * b);
        } else {
            st.push(stoll(t));
        }
    }
    return st.top();
}
CPP;

    $sqTwoStack = <<<'CPP'
// Queue dari dua stack: setiap elemen dipindah paling banyak sekali → O(1) amortisasi
struct Antrian {
    stack<int> masuk, keluar;
    void push(int x) { masuk.push(x); }
    int pop() {
        if (keluar.empty())
            while (!masuk.empty()) { keluar.push(masuk.top()); masuk.pop(); }
        int x = keluar.top();
        keluar.pop();
        return x;
    }
};
CPP;

    $sqVector = <<<'CPP'
// vector juga bisa menjadi stack, dan sering lebih praktis
vector<int> st;
st.push_back(3);          // push
st.back();                // top
st.pop_back();            // pop
// Bonus: isi stack bisa dilihat semua (st[0..size-1]), stack<int> tidak bisa
CPP;
@endphp

<div class="lead-box">
    <h3>Setelah materi ini kamu bisa:</h3>
    <ul>
        <li>Membedakan <strong>stack</strong> (LIFO), <strong>queue</strong> (FIFO), dan <strong>deque</strong> (dua ujung), serta memakai ketiganya di C++.</li>
        <li>Mengenali soal yang "berbau stack": kurung, undo, ekspresi, dan "yang paling baru diproses lebih dulu".</li>
        <li>Mensimulasikan antrian, termasuk antrian yang bisa diselak dari depan.</li>
        <li>Menghindari jebakan <code>top()</code>/<code>front()</code> pada kontainer kosong.</li>
    </ul>
</div>

@include('lessons.level', ['n' => 1, 'title' => 'tumpukan dan antrian', 'desc' => 'Dua cara paling dasar mengatur urutan data.'])

<section class="lesson-section" id="intuisi" data-toc="Intuisi: Piring & Kasir">
    <h2>Intuisi: Tumpukan Piring dan Antrian Kasir</h2>
    <div class="prose">
        <p><strong>Tumpukan piring:</strong> piring yang terakhir ditaruh selalu diambil lebih dulu. Kamu tidak bisa mengambil piring paling bawah tanpa mengangkat semua yang di atasnya. Inilah <strong>stack</strong>: <em>Last In, First Out</em>.</p>
        <p><strong>Antrian kasir:</strong> yang datang lebih dulu dilayani lebih dulu. Orang baru berdiri di belakang. Inilah <strong>queue</strong>: <em>First In, First Out</em>.</p>
        <p>Bagaimana jika ada tamu VIP yang boleh menyelak ke depan, atau orang di belakang bosan lalu pergi? Kita butuh antrian yang bisa diubah di <strong>kedua ujung</strong>: <strong>deque</strong>.</p>
    </div>
    <div class="term-grid">
        <div class="term"><b>stack</b><span><code>push</code>, <code>top</code>, <code>pop</code> di satu ujung (atas). Untuk kurung, undo, rekursi manual, monotonic stack.</span></div>
        <div class="term"><b>queue</b><span><code>push</code> di belakang, <code>front</code>/<code>pop</code> di depan. Untuk simulasi antrian dan BFS.</span></div>
        <div class="term"><b>deque</b><span>Tambah/buang di depan dan belakang, plus akses indeks. Untuk BFS 0-1 dan sliding window.</span></div>
    </div>
    @include('lessons.code', ['cpp' => $sqOps, 'js' => null, 'py' => null])
</section>

<section class="lesson-section" id="visualisasi" data-toc="Visualisasi Stack">
    <h2>Visualisasi: Kurung Seimbang dengan Stack</h2>
    <div class="prose">
        <p>Ubah stringnya (misalnya <code>([)]</code> atau <code>(()</code>) lalu putar. Di <strong>Mode Tebak</strong>, setiap kali kurung tutup datang kamu diminta menebak isi <code>top()</code> sebelum diperiksa.</p>
    </div>
    <div data-viz="stack-bracket"></div>
</section>

<section class="lesson-section" id="tangan" data-toc="Coba dengan Tangan">
    <h2>Coba dengan Tangan</h2>
    <div class="prose"><p>Telusuri <code>{[()]}</code> dan <code>([)]</code>. Kolom terakhir adalah isi stack (kanan = atas).</p></div>
    <div class="trace-wrap">
        <table class="trace-table">
            <tr><th>String</th><th>Baca</th><th>Aksi</th><th>Stack</th></tr>
            <tr><td rowspan="6"><code>{[()]}</code></td><td>{</td><td>push</td><td>{</td></tr>
            <tr><td>[</td><td>push</td><td>{ [</td></tr>
            <tr><td>(</td><td>push</td><td>{ [ (</td></tr>
            <tr class="hl"><td>)</td><td>top ( cocok → pop</td><td>{ [</td></tr>
            <tr class="hl"><td>]</td><td>top [ cocok → pop</td><td>{</td></tr>
            <tr class="ok"><td>}</td><td>top { cocok → pop</td><td><b>kosong → YA</b></td></tr>
            <tr><td rowspan="3"><code>([)]</code></td><td>(</td><td>push</td><td>(</td></tr>
            <tr><td>[</td><td>push</td><td>( [</td></tr>
            <tr class="hl"><td>)</td><td>top adalah [, bukan (</td><td><b>TIDAK</b></td></tr>
        </table>
    </div>
</section>

@include('lessons.level', ['n' => 2, 'title' => 'stack dan queue di C++', 'desc' => 'Program lengkap, simulasi antrian, dan jebakan yang sering terjadi.'])

<section class="lesson-section" id="kode" data-toc="Kode C++ Lengkap">
    <h2>Kode C++ Lengkap: Kurung Seimbang</h2>
    @include('lessons.walkthrough', [
        'title' => 'Memeriksa kurung dengan stack',
        'steps' => $sqSteps,
        'sample' => ['input' => "4\n{[()()]}\n([)]\n((\n()[]{}\n", 'output' => "YA\nTIDAK\nTIDAK\nYA\n"],
        'py' => $sqPy,
    ])
</section>

<section class="lesson-section" id="antrian" data-toc="Queue & Deque">
    <h2>Queue dan Deque</h2>
    <div class="prose">
        <p>Pilih <strong>queue</strong> lalu <strong>deque</strong> dan bandingkan urutan keluarnya. Pada queue, urutan keluar selalu sama dengan urutan masuk. Pada deque, elemen bisa masuk dan keluar di kedua ujung.</p>
    </div>
    <div data-viz="queue-deque"></div>
    <div class="steps">
        <div class="step-card"><b>Kenali urutannya</b><span>"Yang terakhir datang diproses dulu" → stack. "Yang pertama datang dilayani dulu" → queue. Ada operasi di kedua ujung → deque.</span></div>
        <div class="step-card"><b>Simulasikan apa adanya</b><span>Banyak soal hanya meminta mensimulasikan perintah. Pastikan setiap perintah O(1).</span></div>
        <div class="step-card"><b>Periksa kosong</b><span>Sebelum <code>top()</code>, <code>front()</code>, atau <code>pop()</code>, cek <code>empty()</code>.</span></div>
        <div class="step-card"><b>Cetak sekaligus</b><span>Kumpulkan output, hindari <code>endl</code> di dalam loop besar.</span></div>
    </div>
</section>

<section class="lesson-section" id="jebakan" data-toc="Kompleksitas & Jebakan">
    <h2>Kompleksitas & Jebakan</h2>
    <table class="cx-table">
        <tr><th>Kontainer</th><th>Operasi utama</th><th>Waktu</th></tr>
        <tr><td><code>stack</code></td><td>push, top, pop</td><td><code>O(1)</code></td></tr>
        <tr><td><code>queue</code></td><td>push, front, pop</td><td><code>O(1)</code></td></tr>
        <tr><td><code>deque</code></td><td>push/pop di kedua ujung, <code>d[i]</code></td><td><code>O(1)</code></td></tr>
        <tr><td><code>vector</code> sebagai antrian</td><td><code>erase(begin())</code></td><td><code>O(n)</code> ⚠️</td></tr>
    </table>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>top() pada stack kosong</strong> tidak memberi pesan error yang jelas: program bisa langsung crash atau membaca sampah. Selalu cek <code>empty()</code> lebih dulu.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>pop() tidak mengembalikan nilai.</strong> <code>int x = st.pop();</code> tidak bisa dikompilasi. Tulis <code>int x = st.top(); st.pop();</code>.</p>
    </div>
    <div class="callout warn">
        <span class="callout-icon">⚠️</span>
        <p><strong>Antrian dengan vector</strong> dan <code>erase(v.begin())</code> menggeser semua elemen setiap kali. Untuk 10<sup>5</sup> operasi, itu 10<sup>10</sup> langkah. Pakai <code>queue</code> atau <code>deque</code>.</p>
    </div>
</section>

@include('lessons.level', ['n' => 3, 'title' => 'stack di balik layar', 'desc' => 'Ekspresi postfix, queue dari dua stack, dan vector sebagai stack.'])

<section class="lesson-section" id="postfix" data-toc="Ekspresi Postfix">
    <h2>Ekspresi Postfix</h2>
    <div class="prose">
        <p>Komputer lebih suka ekspresi <strong>postfix</strong> (notasi Polandia terbalik): operator ditulis <em>setelah</em> operannya, tanpa kurung. <code>(3 + 4) × 2</code> menjadi <code>3 4 + 2 *</code>. Evaluasinya hanya butuh satu stack.</p>
    </div>
    @include('lessons.code', ['cpp' => $sqPostfix, 'js' => null, 'py' => null])
    <div class="callout tip">
        <span class="callout-icon">🧩</span>
        <p>Urutan pop penting untuk operator yang tidak komutatif: yang keluar pertama adalah operan <strong>kanan</strong>. <code>8 3 -</code> = 8 − 3 = 5, bukan 3 − 8.</p>
    </div>
</section>

<section class="lesson-section" id="dua-stack" data-toc="Queue dari Dua Stack">
    <h2>Queue dari Dua Stack</h2>
    <div class="prose"><p>Teka-teki klasik wawancara dan dasar analisis amortisasi: bangun queue hanya dengan dua stack.</p></div>
    @include('lessons.code', ['cpp' => $sqTwoStack, 'js' => null, 'py' => null])
    <div class="proof">
        <p>Satu <code>pop</code> bisa memindahkan banyak elemen sekaligus, tetapi setiap elemen paling banyak dipindah <strong>sekali</strong> dari <code>masuk</code> ke <code>keluar</code>. Jadi n operasi total memindahkan paling banyak n elemen: rata-rata O(1) per operasi.</p>
    </div>
    @include('lessons.code', ['cpp' => $sqVector, 'js' => null, 'py' => null])
    <div class="pattern-grid">
        <div class="pattern"><h4>Kurung seimbang</h4><p>Push buka, pop saat tutup yang cocok.</p></div>
        <div class="pattern"><h4>Undo / redo</h4><p>Dua stack: riwayat dan yang dibatalkan.</p></div>
        <div class="pattern"><h4>Simulasi antrian</h4><p>Queue untuk FIFO murni, deque jika ada yang menyelak atau pergi dari belakang.</p></div>
        <div class="pattern"><h4>BFS</h4><p>Queue berisi simpul yang menunggu dijelajahi.</p></div>
        <div class="pattern"><h4>Elemen lebih besar berikutnya</h4><p>Monotonic stack (lihat track Teknik Array).</p></div>
        <div class="pattern"><h4>Maksimum jendela</h4><p>Monotonic deque.</p></div>
    </div>
</section>

<section class="lesson-section" id="kuis" data-toc="Cek Pemahaman">
    <h2>Cek Pemahaman</h2>
    <div class="quiz" data-quiz data-answer="1" data-explain="Stack mengeluarkan yang terakhir masuk: 3, lalu 2. Setelah dua pop, top() adalah 1.">
        <p class="quiz-q">Stack kosong, lalu <code>push(1)</code>, <code>push(2)</code>, <code>push(3)</code>, <code>pop()</code>, <code>pop()</code>. Berapa <code>top()</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">3</button>
            <button class="quiz-option">1</button>
            <button class="quiz-option">2</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="2" data-explain="Queue mengeluarkan yang paling awal masuk. A keluar lebih dulu, lalu B, sehingga front() adalah C.">
        <p class="quiz-q">Queue: <code>push(A)</code>, <code>push(B)</code>, <code>push(C)</code>, <code>pop()</code>, <code>pop()</code>. Berapa <code>front()</code>?</p>
        <div class="quiz-options">
            <button class="quiz-option">A</button>
            <button class="quiz-option">B</button>
            <button class="quiz-option">C</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
    <div class="quiz" data-quiz data-answer="0" data-explain="Stack berisi ( [ dengan [ di atas. Kurung ) harus menutup ( tetapi top() adalah [, jadi tidak seimbang.">
        <p class="quiz-q">Mengapa <code>([)]</code> tidak seimbang?</p>
        <div class="quiz-options">
            <button class="quiz-option">Saat ) datang, top() adalah [</button>
            <button class="quiz-option">Jumlah kurung buka dan tutup berbeda</button>
            <button class="quiz-option">Stack kosong di akhir</button>
        </div>
        <p class="quiz-feedback" hidden></p>
    </div>
</section>
