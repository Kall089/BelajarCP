<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Stack & Stack Monoton: validasi kurung, elemen lebih besar berikutnya,
 * persegi panjang terbesar di histogram, dan teknik kontribusi minimum subarray.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$MOD = 1000000007;

$arr = function (int $n, int $lo, int $hi): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = mt_rand($lo, $hi);
    }

    return implode(' ', $a);
};

/** String kurung seimbang acak sepanjang 2m (tiga jenis kurung). */
$kurung = function (int $m): string {
    $buka = '([{';
    $tutup = ')]}';
    $s = '';
    $tumpukan = [];
    $sisa = $m;
    for ($i = 0; $i < 2 * $m; $i++) {
        if ($sisa > 0 && (! $tumpukan || mt_rand(0, 1) === 1)) {
            $t = mt_rand(0, 2);
            $s .= $buka[$t];
            $tumpukan[] = $t;
            $sisa--;
        } else {
            $s .= $tutup[array_pop($tumpukan)];
        }
    }

    return $s;
};

/** Ganti jenis satu kurung tutup acak di posisi indeks >= $lo. */
$gantiTutup = function (string $s, int $lo): string {
    $tutup = ')]}';
    do {
        $p = mt_rand($lo, strlen($s) - 1);
        $k = strpos($tutup, $s[$p]);
    } while ($k === false);
    $s[$p] = $tutup[($k + mt_rand(1, 2)) % 3];

    return $s;
};

return [
    [
        'slug' => 'kurung-seimbang-tiga',
        'lesson' => 'stack-monoton',
        'title' => 'Pemeriksa Kurung',
        'difficulty' => 'Mudah',
        'tags' => ['stack', 'kurung', 'simulasi'],
        'statement' => '<p>Tika sedang membuat editor kode sederhana. Salah satu fiturnya memeriksa apakah kurung-kurung dalam kode sudah <strong>seimbang</strong>. Kode yang diperiksa sudah disaring sehingga hanya tersisa string <strong>S</strong> berisi karakter <code>(</code>, <code>)</code>, <code>[</code>, <code>]</code>, <code>{</code>, dan <code>}</code>.</p>
<p>S seimbang jika setiap kurung buka punya pasangan kurung tutup sejenis di sebelah kanannya, setiap kurung tutup punya pasangan, dan pasangan-pasangan itu tidak saling silang. Contohnya <code>{[()]}()</code> seimbang, sedangkan <code>([)]</code> dan <code>(()</code> tidak.</p>
<p>Jika S tidak seimbang, editor harus menunjuk <strong>posisi kesalahan pertama</strong>. Editor membaca S dari kiri ke kanan sambil mencatat kurung buka yang masih menunggu pasangan. Kurung tutup yang dibaca dianggap <strong>salah</strong> jika tidak ada kurung buka yang sedang menunggu, atau kurung buka yang paling akhir menunggu berbeda jenis dengannya. Jika tidak salah, kurung tutup itu berpasangan dengan kurung buka tersebut.</p>
<ul><li>Jika ada kurung tutup yang salah, posisi kesalahan adalah posisi kurung tutup salah yang pertama.</li><li>Jika tidak ada, tetapi setelah S selesai dibaca masih ada kurung buka yang belum berpasangan, posisi kesalahan adalah posisi kurung buka tak berpasangan yang paling kiri.</li></ul>
<p>Posisi dihitung mulai dari 1.</p>',
        'input_format' => '<p>Satu baris berisi string <code>S</code>.</p>',
        'output_format' => '<p>Jika S seimbang, cetak <code>YA</code>. Jika tidak, cetak <code>TIDAK</code>, lalu pada baris kedua cetak posisi kesalahan pertama.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 10<sup>6</sup></li><li>S hanya berisi karakter <code>()[]{}</code></li></ul>',
        'samples' => [
            ['input' => "{[()]}()\n", 'explanation' => 'Setiap kurung tutup bertemu kurung buka sejenis yang paling akhir menunggu, dan di akhir tidak ada kurung buka yang tersisa.'],
            ['input' => "([)]\n", 'explanation' => 'Saat <code>)</code> di posisi 3 dibaca, kurung buka yang paling akhir menunggu adalah <code>[</code> di posisi 2. Jenisnya berbeda, jadi kesalahan pertama ada di posisi 3.'],
            ['input' => "[[]](()\n", 'explanation' => 'Semua kurung tutup benar, tetapi <code>(</code> di posisi 5 tidak pernah ditutup. <code>(</code> di posisi 6 sudah berpasangan dengan <code>)</code> di posisi 7.'],
        ],
        'tests' => function () use ($kurung, $gantiTutup) {
            $dalam = T::word(500000, '([{');
            $hapus = $kurung(500000);
            $p = mt_rand(0, strlen($hapus) - 1);
            $hapus = substr($hapus, 0, $p).substr($hapus, $p + 1);

            return [
                "()\n", ")\n", "()(\n", "([{}])[]{()}\n", "{[(])}\n", $gantiTutup($kurung(10), 0)."\n",
                $kurung(500000)."\n",
                $dalam.strtr(strrev($dalam), '([{', ')]}')."\n",
                $gantiTutup($kurung(500000), 900000)."\n",
                $kurung(250000).'('.$kurung(249999)."\n",
                $hapus."\n",
                T::word(1000000, '()[]{}')."\n",
                $kurung(300000).']'.$kurung(199999)."\n",
            ];
        },
        'solve' => function (string $input) {
            $s = trim($input);
            $n = strlen($s);
            $pasangan = [')' => '(', ']' => '[', '}' => '{'];
            $tumpukan = [];
            $atas = -1;
            $salah = 0;
            for ($i = 0; $i < $n; $i++) {
                $c = $s[$i];
                if (isset($pasangan[$c])) {
                    if ($atas < 0 || $s[$tumpukan[$atas]] !== $pasangan[$c]) {
                        $salah = $i + 1;
                        break;
                    }
                    $atas--;
                } else {
                    $tumpukan[++$atas] = $i;
                }
            }
            if ($salah === 0 && $atas >= 0) {
                $salah = $tumpukan[0] + 1;
            }

            return $salah === 0 ? 'YA' : "TIDAK\n$salah";
        },
        'starter' => $st("    string s;\n    cin >> s;", 'const s = readLine().trim();', 's = input().strip()'),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int n = s.size();

    // tumpukan menyimpan POSISI kurung buka yang masih menunggu pasangan
    vector<int> tumpukan;
    tumpukan.reserve(n);
    int salah = 0;   // posisi kesalahan (1-based), 0 = belum ada
    for (int i = 0; i < n && salah == 0; i++) {
        char c = s[i];
        if (c == '(' || c == '[' || c == '{') {
            tumpukan.push_back(i);
        } else {
            char buka = (c == ')') ? '(' : (c == ']') ? '[' : '{';
            // kurung tutup harus cocok dengan kurung buka yang PALING AKHIR menunggu
            if (tumpukan.empty() || s[tumpukan.back()] != buka) salah = i + 1;
            else tumpukan.pop_back();
        }
    }
    // tidak ada kurung tutup yang salah, tetapi ada kurung buka tersisa:
    // yang paling kiri berada di dasar tumpukan
    if (salah == 0 && !tumpukan.empty()) salah = tumpukan[0] + 1;

    if (salah == 0) cout << "YA\n";
    else cout << "TIDAK\n" << salah << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const s = readLine().trim();
const n = s.length;
const pasangan = { ")": "(", "]": "[", "}": "{" };
const tumpukan = new Int32Array(n); // posisi kurung buka yang masih menunggu
let atas = 0;                         // banyak isi tumpukan
let salah = 0;
for (let i = 0; i < n; i++) {
  const c = s[i];
  if (c === "(" || c === "[" || c === "{") {
    tumpukan[atas++] = i;
  } else {
    if (atas === 0 || s[tumpukan[atas - 1]] !== pasangan[c]) {
      salah = i + 1;
      break;
    }
    atas--;
  }
}
if (salah === 0 && atas > 0) salah = tumpukan[0] + 1; // dasar tumpukan = paling kiri
console.log(salah === 0 ? "YA" : "TIDAK\n" + salah);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

s = input().strip()
pasangan = {")": "(", "]": "[", "}": "{"}
tumpukan = []   # posisi kurung buka yang masih menunggu pasangan
salah = 0
for i, c in enumerate(s):
    if c in "([{":
        tumpukan.append(i)
    elif not tumpukan or s[tumpukan[-1]] != pasangan[c]:
        salah = i + 1
        break
    else:
        tumpukan.pop()

if salah == 0 and tumpukan:
    salah = tumpukan[0] + 1   # dasar tumpukan = kurung buka tak berpasangan paling kiri

if salah == 0:
    print("YA")
else:
    print("TIDAK")
    print(salah)
CODE,
        ],
        'editorial' => '<p>Kurung tutup harus berpasangan dengan kurung buka yang <strong>paling akhir</strong> dibuka dan belum ditutup. Jika ia dipasangkan dengan kurung buka yang lebih awal, kurung buka yang lebih akhir terjebak di dalam pasangan itu, dan pasangannya kelak pasti menyilang. Pola "terakhir masuk, pertama keluar" ini persis perilaku <strong>stack</strong>.</p>
<p>Baca S dari kiri ke kanan. Untuk kurung buka, masukkan <em>posisinya</em> ke stack. Untuk kurung tutup, jika stack kosong atau karakter di posisi puncak stack berbeda jenis, itulah kesalahan pertama; jika cocok, keluarkan puncaknya. Jika pembacaan selesai tanpa kesalahan tetapi stack masih berisi, kurung buka tak berpasangan yang paling kiri ada di <strong>dasar</strong> stack, karena elemen itu dimasukkan paling awal. Total O(|S|).</p>
<p><strong>Jebakan:</strong> menghitung saldo (banyak kurung buka dikurangi kurung tutup) untuk setiap jenis tidak cukup. String <code>([)]</code> punya saldo nol untuk setiap jenis tetapi tidak seimbang. Simpan posisi, bukan hanya karakter, agar posisi kesalahan bisa dicetak. Stack bisa sedalam 10<sup>6</sup>, jadi jangan memakai rekursi.</p>',
        'hints' => [
            'Sebuah kurung tutup harus berpasangan dengan kurung buka yang mana: yang paling awal dibuka atau yang paling akhir?',
            'Simpan kurung buka yang masih menunggu di sebuah stack. Setiap kurung tutup dicocokkan dengan puncak stack.',
            'Simpan posisi di stack. Kesalahan terjadi saat stack kosong atau puncaknya berbeda jenis. Jika lolos sampai akhir tetapi stack tidak kosong, jawabannya elemen paling bawah stack.',
        ],
    ],

    [
        'slug' => 'hari-lebih-hangat',
        'lesson' => 'stack-monoton',
        'title' => 'Menunggu Hari Lebih Hangat',
        'difficulty' => 'Mudah',
        'tags' => ['stack monoton', 'elemen lebih besar berikutnya'],
        'statement' => '<p>Stasiun cuaca di lereng gunung mencatat suhu selama <strong>N</strong> hari berturut-turut. Sensornya mencatat suhu hari ke-i sebagai bilangan bulat <code>t<sub>i</sub></code> (boleh negatif). Para petani ingin tahu, untuk setiap hari, berapa hari lagi mereka harus menunggu sampai datang hari yang <strong>lebih hangat</strong>, yaitu suhunya lebih tinggi (suhu yang sama tidak dihitung).</p>
<p>Untuk setiap hari i, cetak <code>j − i</code> dengan j adalah hari pertama setelah i yang memenuhi <code>t<sub>j</sub> &gt; t<sub>i</sub></code>. Jika tidak ada hari seperti itu dalam catatan, cetak 0.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>t<sub>1</sub> … t<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris berisi N bilangan dipisah spasi: jawaban untuk hari 1 sampai N.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>−10<sup>9</sup> ≤ t<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "8\n23 24 22 20 21 25 25 19\n", 'explanation' => 'Hari ke-2 (24) baru dikalahkan oleh hari ke-6 (25), empat hari kemudian. Hari ke-6 dan ke-7 sama-sama 25: hari ke-7 tidak lebih hangat dari hari ke-6, dan sesudahnya tidak ada hari yang lebih panas, jadi keduanya 0.'],
            ['input' => "6\n31 30 30 32 29 33\n", 'explanation' => 'Hari ke-2 dan ke-3 sama-sama 30 dan sama-sama menunggu hari ke-4 (32), masing-masing 2 dan 1 hari. Hari ke-1 (31) juga menunggu hari ke-4, sedangkan hari ke-4 menunggu hari ke-6 (33).'],
        ],
        'tests' => function () use ($arr) {
            $n = 200000;
            $turun = [];
            $v = 1000000000;
            for ($i = 0; $i < $n; $i++) {
                $turun[] = $v;
                $v -= mt_rand(1, 9000);
            }
            $naik = [];
            $v = -1000000000;
            for ($i = 0; $i < $n; $i++) {
                $naik[] = $v;
                $v += mt_rand(1, 9000);
            }
            // bentuk V: turun lalu naik
            $lembah = [];
            $v = 100000000;
            for ($i = 0; $i < $n / 2; $i++) {
                $lembah[] = $v;
                $v -= mt_rand(1, 1000);
            }
            for ($i = $n / 2; $i < $n; $i++) {
                $lembah[] = $v;
                $v += mt_rand(1, 1000);
            }
            // gergaji: blok-blok menurun, setiap blok dimulai lebih tinggi dari blok sebelumnya
            $gergaji = [];
            for ($i = 0; $i < $n; $i++) {
                $gergaji[] = intdiv($i, 1000) * 5000 - $i % 1000;
            }

            return [
                "1\n5\n", "2\n3 7\n", "3\n4 4 4\n", "10\n".$arr(10, -5, 5)."\n", "1000\n".$arr(1000, -100, 100)."\n",
                "$n\n".$arr($n, -1000000000, 1000000000)."\n", "$n\n".$arr($n, -3, 3)."\n",
                "$n\n".implode(' ', $turun)."\n", "$n\n".implode(' ', $naik)."\n", "$n\n".implode(' ', array_fill(0, $n, -7))."\n",
                "$n\n".implode(' ', $lembah)."\n", "$n\n".implode(' ', $gergaji)."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $t = T::ints($lines[1]);
            $jawab = array_fill(0, $n, 0);
            $tumpukan = [];
            $atas = -1;
            for ($i = 0; $i < $n; $i++) {
                $x = $t[$i];
                while ($atas >= 0 && $t[$tumpukan[$atas]] < $x) {
                    $j = $tumpukan[$atas--];
                    $jawab[$j] = $i - $j;
                }
                $tumpukan[++$atas] = $i;
            }

            return implode(' ', $jawab);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> t(n);\n    for (int i = 0; i < n; i++) cin >> t[i];", "const n = Number(readLine());\nconst t = readInts();", "n = int(input())\nt = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> t(n);
    for (int i = 0; i < n; i++) cin >> t[i];

    // tumpukan berisi hari-hari yang BELUM menemukan hari lebih hangat.
    // Suhu mereka tidak pernah naik dari dasar ke puncak (stack monoton).
    vector<int> jawab(n, 0), tumpukan;
    tumpukan.reserve(n);
    for (int i = 0; i < n; i++) {
        // hari i menjawab semua hari di puncak yang suhunya lebih rendah (tegas)
        while (!tumpukan.empty() && t[tumpukan.back()] < t[i]) {
            int j = tumpukan.back();
            tumpukan.pop_back();
            jawab[j] = i - j;
        }
        tumpukan.push_back(i);
    }
    // hari yang tersisa di tumpukan tetap bernilai 0

    string out;
    for (int i = 0; i < n; i++) {
        out += to_string(jawab[i]);
        out += (i + 1 < n) ? ' ' : '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const t = readInts();
const jawab = new Int32Array(n);    // otomatis 0
const tumpukan = new Int32Array(n); // hari yang belum terjawab
let atas = 0;
for (let i = 0; i < n; i++) {
  while (atas > 0 && t[tumpukan[atas - 1]] < t[i]) {
    const j = tumpukan[--atas];
    jawab[j] = i - j;
  }
  tumpukan[atas++] = i;
}
console.log(jawab.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
t = list(map(int, input().split()))
jawab = [0] * n
tumpukan = []   # hari yang belum menemukan hari lebih hangat
for i in range(n):
    x = t[i]
    while tumpukan and t[tumpukan[-1]] < x:
        j = tumpukan.pop()
        jawab[j] = i - j
    tumpukan.append(i)
print(" ".join(map(str, jawab)))
CODE,
        ],
        'editorial' => '<p>Cara langsung, yaitu untuk setiap hari maju satu per satu sampai menemukan suhu yang lebih tinggi, bisa O(N<sup>2</sup>). Contohnya jika suhu terus turun: hari pertama memeriksa N − 1 hari, hari kedua N − 2 hari, dan seterusnya.</p>
<p>Proses hari dari kiri ke kanan sambil menyimpan stack berisi hari-hari yang <strong>belum</strong> menemukan hari lebih hangat. Suhu hari-hari di stack tidak pernah naik dari dasar ke puncak: seandainya hari j berada di atas hari k dengan t<sub>j</sub> &gt; t<sub>k</sub>, hari k pasti sudah dijawab oleh j saat j datang. Saat hari i datang, keluarkan semua hari j di puncak yang memenuhi <code>t<sub>j</sub> &lt; t<sub>i</sub></code> dan isi jawabannya <code>i − j</code>, lalu masukkan i. Hari yang masih tersisa di stack sampai akhir mendapat jawaban 0.</p>
<p>Setiap hari masuk dan keluar stack paling banyak sekali, jadi totalnya O(N). Perhatikan perbandingan <strong>tegas</strong> (&lt;, bukan ≤): suhu yang sama tidak dianggap lebih hangat. Output berisi sampai 200 000 bilangan, jadi gabungkan dulu menjadi satu string sebelum dicetak.</p>',
        'hints' => [
            'Cara langsung: untuk setiap hari, maju sampai menemukan yang lebih hangat. Data seperti apa yang membuat cara ini O(N²)?',
            'Proses hari dari kiri ke kanan dan simpan hari-hari yang belum menemukan jawaban. Apa yang bisa kamu katakan tentang urutan suhu mereka?',
            'Saat hari i datang, keluarkan dari puncak stack semua hari j dengan t_j < t_i dan isi jawabannya i − j, lalu masukkan i. Setiap hari masuk dan keluar paling banyak sekali.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'persegi-histogram',
        'lesson' => 'stack-monoton',
        'title' => 'Spanduk Terbesar',
        'difficulty' => 'Sedang',
        'tags' => ['stack monoton', 'histogram', 'elemen lebih kecil terdekat'],
        'statement' => '<p>Di sepanjang Jalan Merdeka berdiri <strong>N</strong> bangunan yang saling menempel, masing-masing selebar 1 meter. Tinggi bangunan ke-i adalah <code>h<sub>i</sub></code> meter; tinggi 0 berarti lahan kosong.</p>
<p>Panitia festival ingin memasang <strong>satu</strong> spanduk persegi panjang di dinding depan deretan bangunan itu. Spanduk harus menutupi selebar penuh beberapa bangunan yang <strong>berurutan</strong>, sisi bawahnya menempel di tanah, dan seluruh bagiannya harus menempel di dinding. Artinya, tinggi spanduk tidak boleh melebihi bangunan terpendek yang ditutupinya.</p>
<p>Berapa luas spanduk terbesar (dalam meter persegi) yang bisa dipasang?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>h<sub>1</sub> … h<sub>N</sub></code>.</p>',
        'output_format' => '<p>Luas spanduk terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ h<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "8\n3 1 4 4 5 2 4 1\n", 'explanation' => 'Spanduk setinggi 4 menutupi tiga bangunan berurutan bertinggi 4, 4, 5, luasnya 4 × 3 = 12. Spanduk setinggi 2 bisa lebih lebar (lima bangunan 4, 4, 5, 2, 4), tetapi luasnya hanya 2 × 5 = 10.'],
            ['input' => "4\n1000000000 1000000000 1000000000 1000000000\n", 'explanation' => 'Spanduk menutupi keempat bangunan. Luasnya 4 · 10<sup>9</sup>, sudah melewati batas <code>int</code>.'],
        ],
        'tests' => function () use ($arr) {
            $n = 200000;
            $gunung = [];
            $v = 0;
            for ($i = 0; $i < $n; $i++) {
                $v += $i < $n / 2 ? mt_rand(1, 9999) : -mt_rand(1, 9999);
                $gunung[] = max(0, $v);
            }

            return [
                "1\n7\n", "1\n0\n", "2\n3 3\n", "6\n0 0 0 0 0 0\n", "6\n2 0 2 2 0 1\n", "10\n".$arr(10, 0, 10)."\n", "1000\n".$arr(1000, 0, 1000)."\n",
                "$n\n".implode(' ', array_fill(0, $n, 1000000000))."\n", "$n\n".implode(' ', range(1, $n))."\n",
                "$n\n".$arr($n, 1, 1000000000)."\n", "$n\n".$arr($n, 0, 5)."\n", "$n\n".implode(' ', $gunung)."\n",
                "$n\n".$arr($n, 999990000, 1000000000)."\n",
            ];
        },
        'solve' => function (string $input) {
            // Versi satu lintasan: saat sebuah batang dikeluarkan, batas kanannya adalah i
            // dan batas kirinya adalah elemen di bawahnya pada stack.
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $h = T::ints($lines[1]);
            $tumpukan = [];
            $atas = -1;
            $terbaik = 0;
            for ($i = 0; $i <= $n; $i++) {
                $cur = $i < $n ? $h[$i] : -1;
                while ($atas >= 0 && $h[$tumpukan[$atas]] >= $cur) {
                    $tinggi = $h[$tumpukan[$atas--]];
                    $kiri = $atas >= 0 ? $tumpukan[$atas] : -1;
                    $terbaik = max($terbaik, $tinggi * ($i - $kiri - 1));
                }
                $tumpukan[++$atas] = $i;
            }

            return (string) $terbaik;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> h(n);\n    for (int i = 0; i < n; i++) cin >> h[i];", "const n = Number(readLine());\nconst h = readInts();", "n = int(input())\nh = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> h(n);
    for (int i = 0; i < n; i++) cin >> h[i];

    // kiri[i]  = indeks terdekat di kiri dengan tinggi < h[i] (atau -1)
    // kanan[i] = indeks terdekat di kanan dengan tinggi < h[i] (atau n)
    vector<int> kiri(n), kanan(n), tumpukan;
    tumpukan.reserve(n);
    for (int i = 0; i < n; i++) {
        while (!tumpukan.empty() && h[tumpukan.back()] >= h[i]) tumpukan.pop_back();
        kiri[i] = tumpukan.empty() ? -1 : tumpukan.back();
        tumpukan.push_back(i);
    }
    tumpukan.clear();
    for (int i = n - 1; i >= 0; i--) {
        while (!tumpukan.empty() && h[tumpukan.back()] >= h[i]) tumpukan.pop_back();
        kanan[i] = tumpukan.empty() ? n : tumpukan.back();
        tumpukan.push_back(i);
    }

    // Bangunan i sebagai yang terpendek: spanduk melebar dari kiri[i]+1 sampai kanan[i]-1
    long long terbaik = 0;
    for (int i = 0; i < n; i++) {
        terbaik = max(terbaik, h[i] * (kanan[i] - kiri[i] - 1));   // sampai 2 · 10^14
    }
    cout << terbaik << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const h = readInts();
const kiri = new Int32Array(n), kanan = new Int32Array(n);
const tumpukan = new Int32Array(n);
let atas = 0;
for (let i = 0; i < n; i++) {
  while (atas > 0 && h[tumpukan[atas - 1]] >= h[i]) atas--;
  kiri[i] = atas > 0 ? tumpukan[atas - 1] : -1;
  tumpukan[atas++] = i;
}
atas = 0;
for (let i = n - 1; i >= 0; i--) {
  while (atas > 0 && h[tumpukan[atas - 1]] >= h[i]) atas--;
  kanan[i] = atas > 0 ? tumpukan[atas - 1] : n;
  tumpukan[atas++] = i;
}
let terbaik = 0; // ≤ 2 · 10^14, masih aman untuk Number
for (let i = 0; i < n; i++) {
  terbaik = Math.max(terbaik, h[i] * (kanan[i] - kiri[i] - 1));
}
console.log(terbaik);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
h = list(map(int, input().split()))

kiri = [-1] * n     # indeks terdekat di kiri dengan tinggi < h[i]
tumpukan = []
for i in range(n):
    x = h[i]
    while tumpukan and h[tumpukan[-1]] >= x:
        tumpukan.pop()
    if tumpukan:
        kiri[i] = tumpukan[-1]
    tumpukan.append(i)

kanan = [n] * n     # indeks terdekat di kanan dengan tinggi < h[i]
tumpukan = []
for i in range(n - 1, -1, -1):
    x = h[i]
    while tumpukan and h[tumpukan[-1]] >= x:
        tumpukan.pop()
    if tumpukan:
        kanan[i] = tumpukan[-1]
    tumpukan.append(i)

print(max(h[i] * (kanan[i] - kiri[i] - 1) for i in range(n)))
CODE,
        ],
        'editorial' => '<p>Lihat spanduk terbaik yang menutupi bangunan l..r. Tingginya paling besar <code>min(h<sub>l</sub>, …, h<sub>r</sub>)</code>, dan nilai minimum itu dimiliki oleh suatu bangunan i di dalam rentang. Jadi cukup mencoba setiap bangunan i sebagai <strong>bangunan terpendek</strong> yang ditutupi: spanduk setinggi h<sub>i</sub>, lalu dilebarkan ke kiri dan ke kanan selama bangunannya tidak lebih pendek dari h<sub>i</sub>.</p>
<p>Misalkan <code>kiri[i]</code> adalah indeks bangunan terdekat di kiri yang tingginya &lt; h<sub>i</sub> (atau −1 jika tidak ada), dan <code>kanan[i]</code> adalah indeks terdekat di kanan yang tingginya &lt; h<sub>i</sub> (atau N). Spanduk untuk i lebarnya <code>kanan[i] − kiri[i] − 1</code>, dan jawabannya adalah maksimum <code>h<sub>i</sub> · (kanan[i] − kiri[i] − 1)</code> atas semua i.</p>
<p>Kedua larik itu adalah soal "elemen lebih kecil terdekat" yang diselesaikan dengan stack monoton. Dari kiri ke kanan, buang puncak stack selama tingginya ≥ h<sub>i</sub>; puncak yang tersisa adalah kiri[i]. Lakukan hal yang sama dari kanan ke kiri untuk kanan[i]. Setiap indeks masuk dan keluar stack sekali, jadi totalnya O(N).</p>
<p><strong>Awas overflow:</strong> luas bisa mencapai 200 000 · 10<sup>9</sup> = 2 · 10<sup>14</sup>, jadi pakai <code>long long</code>. Bangunan yang tingginya sama tidak menimbulkan masalah. Beberapa bangunan bisa menghasilkan spanduk yang sama, tetapi yang dicari hanya maksimumnya.</p>',
        'hints' => [
            'Pada spanduk terbaik, tingginya pasti sama dengan tinggi salah satu bangunan yang ditutupinya. Mengapa?',
            'Jika bangunan i adalah yang terpendek di bawah spanduk, spanduk bisa melebar ke kiri dan ke kanan sampai bertemu bangunan yang lebih pendek dari h_i.',
            'Cari bangunan lebih pendek terdekat di kiri dan di kanan untuk setiap i dengan stack monoton. Luasnya h_i · (kanan − kiri − 1); simpan dalam long long.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'jumlah-minimum-subarray',
        'lesson' => 'stack-monoton',
        'title' => 'Kekuatan Potongan Rantai',
        'difficulty' => 'Sulit',
        'tags' => ['stack monoton', 'teknik kontribusi', 'modulo'],
        'statement' => '<p>Pak Darmo, seorang pandai besi, menempa rantai panjang yang terdiri dari <strong>N</strong> mata rantai berurutan. Mata rantai ke-i punya kekuatan <code>a<sub>i</sub></code>. Pembeli boleh meminta potongan berupa beberapa mata rantai yang <strong>berurutan</strong>, dan sebuah potongan hanya sekuat mata rantai <strong>terlemahnya</strong>.</p>
<p>Untuk laporan mutu, Pak Darmo ingin menjumlahkan kekuatan semua potongan yang mungkin, yaitu untuk semua pasangan <code>1 ≤ l ≤ r ≤ N</code>:</p>
<p style="text-align:center"><code>Σ<sub>l ≤ r</sub> min(a<sub>l</sub>, a<sub>l+1</sub>, …, a<sub>r</sub>)</code></p>
<p>Karena hasilnya bisa sangat besar, cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Jumlah kekuatan semua potongan, modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n3 1 2\n", 'explanation' => 'Potongan [3], [1], [2], [3, 1], [1, 2], dan [3, 1, 2] berkekuatan 3, 1, 2, 1, 1, 1. Jumlahnya 9.'],
            ['input' => "3\n2 2 2\n", 'explanation' => 'Keenam potongan berkekuatan 2, jadi jumlahnya 12. Potongan [2, 2, 2] punya tiga mata terlemah, tetapi tetap hanya dihitung sekali.'],
            ['input' => "2\n1000000000 1000000000\n", 'explanation' => 'Ada tiga potongan berkekuatan 10<sup>9</sup>, jumlahnya 3 · 10<sup>9</sup>. Modulo 10<sup>9</sup> + 7 hasilnya 3 · 10<sup>9</sup> − 2 · (10<sup>9</sup> + 7) = 999999986.'],
        ],
        'tests' => function () use ($arr) {
            $n = 200000;
            $turun = [];
            $v = 1000000000;
            for ($i = 0; $i < $n; $i++) {
                $turun[] = $v;
                $v -= mt_rand(1, 4000);
            }
            $zigzag = [];
            for ($i = 0; $i < $n; $i++) {
                $zigzag[] = $i % 2 ? 1000000000 : 1;
            }
            // satu mata terlemah di tengah dengan nilai besar: a_i · L · R ≈ 10^19
            $lembah = explode(' ', $arr($n, 999999001, 1000000000));
            $lembah[$n / 2] = 999999000;

            return [
                "1\n5\n", "1\n1000000000\n", "4\n2 2 2 2\n", "10\n".$arr(10, 1, 3)."\n", "1000\n".$arr(1000, 1, 1000)."\n",
                "$n\n".implode(' ', array_fill(0, $n, 1000000000))."\n", "$n\n".implode(' ', range(1, $n))."\n",
                "$n\n".implode(' ', $turun)."\n", "$n\n".$arr($n, 1, 1000000000)."\n", "$n\n".$arr($n, 1, 3)."\n",
                "$n\n".$arr($n, 1, 100)."\n", "$n\n".implode(' ', $zigzag)."\n", "$n\n".implode(' ', $lembah)."\n",
            ];
        },
        'solve' => function (string $input) use ($MOD) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $a = T::ints($lines[1]);
            $kiri = array_fill(0, $n, -1);
            $kanan = array_fill(0, $n, $n);
            $tumpukan = [];
            $atas = -1;
            for ($i = 0; $i < $n; $i++) {
                while ($atas >= 0 && $a[$tumpukan[$atas]] >= $a[$i]) {
                    $atas--;
                }
                if ($atas >= 0) {
                    $kiri[$i] = $tumpukan[$atas];
                }
                $tumpukan[++$atas] = $i;
            }
            $atas = -1;
            for ($i = $n - 1; $i >= 0; $i--) {
                while ($atas >= 0 && $a[$tumpukan[$atas]] > $a[$i]) {
                    $atas--;
                }
                if ($atas >= 0) {
                    $kanan[$i] = $tumpukan[$atas];
                }
                $tumpukan[++$atas] = $i;
            }
            $total = 0;
            for ($i = 0; $i < $n; $i++) {
                $banyak = (($i - $kiri[$i]) * ($kanan[$i] - $i)) % $MOD;
                $total = ($total + ($a[$i] % $MOD) * $banyak) % $MOD;
            }

            return (string) $total;
        },
        'starter' => $st("    const long long MOD = 1e9 + 7;\n    int n;\n    cin >> n;\n    vector<long long> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i];", "const n = Number(readLine());\nconst a = readInts();\nconst MOD = 1000000007;\n// x · y mod MOD tanpa melewati batas presisi Number (2^53)\nconst mul = (x, y) => (((x * (y >>> 16)) % MOD) * 65536 + x * (y & 65535)) % MOD;", "MOD = 10**9 + 7\nn = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> a(n);
    for (int i = 0; i < n; i++) cin >> a[i];

    // Setiap potongan diwakili oleh mata terlemahnya yang PALING KANAN.
    // kiri[i]  = indeks terdekat di kiri dengan a < a[i]  (berhenti hanya di yang lebih kecil tegas)
    // kanan[i] = indeks terdekat di kanan dengan a <= a[i] (berhenti juga di nilai kembar)
    vector<int> kiri(n), kanan(n), tumpukan;
    tumpukan.reserve(n);
    for (int i = 0; i < n; i++) {
        while (!tumpukan.empty() && a[tumpukan.back()] >= a[i]) tumpukan.pop_back();
        kiri[i] = tumpukan.empty() ? -1 : tumpukan.back();
        tumpukan.push_back(i);
    }
    tumpukan.clear();
    for (int i = n - 1; i >= 0; i--) {
        while (!tumpukan.empty() && a[tumpukan.back()] > a[i]) tumpukan.pop_back();
        kanan[i] = tumpukan.empty() ? n : tumpukan.back();
        tumpukan.push_back(i);
    }

    long long total = 0;
    for (int i = 0; i < n; i++) {
        // banyak potongan yang diwakili i = L · R, bisa sampai 10^10;
        // a[i] · L · R bisa 10^19 (melewati long long), jadi modulo dulu
        long long banyak = (long long)(i - kiri[i]) * (kanan[i] - i) % MOD;
        total = (total + a[i] % MOD * banyak) % MOD;
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
const MOD = 1000000007;
// x · y mod MOD tanpa melewati batas presisi Number (2^53)
const mul = (x, y) => (((x * (y >>> 16)) % MOD) * 65536 + x * (y & 65535)) % MOD;

const kiri = new Int32Array(n), kanan = new Int32Array(n);
const tumpukan = new Int32Array(n);
let atas = 0;
for (let i = 0; i < n; i++) {
  while (atas > 0 && a[tumpukan[atas - 1]] >= a[i]) atas--;  // berhenti di a < a[i]
  kiri[i] = atas > 0 ? tumpukan[atas - 1] : -1;
  tumpukan[atas++] = i;
}
atas = 0;
for (let i = n - 1; i >= 0; i--) {
  while (atas > 0 && a[tumpukan[atas - 1]] > a[i]) atas--;   // berhenti di a <= a[i]
  kanan[i] = atas > 0 ? tumpukan[atas - 1] : n;
  tumpukan[atas++] = i;
}
let total = 0;
for (let i = 0; i < n; i++) {
  const banyak = ((i - kiri[i]) * (kanan[i] - i)) % MOD; // L · R ≤ 10^10, masih tepat
  total = (total + mul(a[i], banyak)) % MOD;
}
console.log(total);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7
n = int(input())
a = list(map(int, input().split()))

kiri = [-1] * n     # indeks terdekat di kiri dengan a < a[i]
tumpukan = []
for i in range(n):
    x = a[i]
    while tumpukan and a[tumpukan[-1]] >= x:
        tumpukan.pop()
    if tumpukan:
        kiri[i] = tumpukan[-1]
    tumpukan.append(i)

kanan = [n] * n     # indeks terdekat di kanan dengan a <= a[i]
tumpukan = []
for i in range(n - 1, -1, -1):
    x = a[i]
    while tumpukan and a[tumpukan[-1]] > x:
        tumpukan.pop()
    if tumpukan:
        kanan[i] = tumpukan[-1]
    tumpukan.append(i)

total = 0
for i in range(n):
    total += a[i] * (i - kiri[i]) * (kanan[i] - i)
print(total % MOD)
CODE,
        ],
        'editorial' => '<p>Ada N(N + 1)/2 ≈ 2 · 10<sup>10</sup> potongan, terlalu banyak untuk dihitung satu per satu. Balik sudut pandangnya dengan <strong>teknik kontribusi</strong>: setiap potongan menyumbangkan nilai mata terlemahnya, jadi</p>
<p style="text-align:center"><code>jawaban = Σ<sub>i</sub> a<sub>i</sub> · (banyak potongan yang diwakili oleh mata i sebagai minimumnya)</code></p>
<p>Potongan [l, r] yang diwakili i harus memuat i dan tidak boleh memuat mata yang lebih lemah dari a<sub>i</sub>. Jika <code>kiri[i]</code> dan <code>kanan[i]</code> adalah batas terdekat yang tidak boleh dilewati, ada <code>L = i − kiri[i]</code> pilihan ujung kiri dan <code>R = kanan[i] − i</code> pilihan ujung kanan, sehingga i menyumbang <code>a<sub>i</sub> · L · R</code>.</p>
<p><strong>Nilai kembar.</strong> Potongan [2, 2] punya dua mata terlemah. Jika kedua sisi berhenti di nilai yang lebih kecil tegas (&lt;), potongan itu dihitung dua kali. Jika kedua sisi berhenti di nilai ≤, potongan itu tidak terhitung sama sekali. Solusinya, buat aturannya tidak simetris: <code>kiri[i]</code> adalah indeks terdekat di kiri dengan <code>a &lt; a<sub>i</sub></code>, sedangkan <code>kanan[i]</code> adalah indeks terdekat di kanan dengan <code>a ≤ a<sub>i</sub></code>. Dengan begitu setiap potongan diwakili tepat satu kali, yaitu oleh mata terlemahnya yang <em>paling kanan</em>.</p>
<p>Kedua batas dihitung dengan stack monoton seperti pada soal elemen lebih kecil terdekat, total O(N).</p>
<p><strong>Awas overflow:</strong> L · R bisa sekitar 10<sup>10</sup> (misalnya mata terlemah berada di tengah), sehingga a<sub>i</sub> · L · R bisa mencapai 10<sup>19</sup>, melebihi batas <code>long long</code> (sekitar 9,2 · 10<sup>18</sup>). Modulo-kan L · R terlebih dahulu sebelum dikalikan dengan a<sub>i</sub>. Di JavaScript, L · R masih tepat sebagai Number, tetapi perkalian dengan a<sub>i</sub> harus memakai helper <code>mul</code>.</p>',
        'hints' => [
            'Daripada mencari minimum setiap potongan, balik pertanyaannya: untuk setiap mata i, di berapa potongan ia menjadi yang terlemah?',
            'Potongan dengan i sebagai minimum bisa melebar ke kiri dan ke kanan sampai bertemu mata yang lebih lemah. Jika ada L pilihan ujung kiri dan R pilihan ujung kanan, kontribusinya a_i · L · R.',
            'Untuk nilai kembar, pakai < di satu sisi dan ≤ di sisi lain (kiri berhenti di a_j < a_i, kanan berhenti di a_j ≤ a_i) agar setiap potongan dihitung tepat sekali. Hitung batasnya dengan stack monoton dan kerjakan modulo.',
        ],
        'sample_visual' => 'bars',
    ],
];
