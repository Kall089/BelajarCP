<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Segment Tree: minimum rentang dengan update titik (iteratif),
 * sparse table untuk RMQ statis, informasi kustom di simpul, dan lazy propagation.
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$arr = function (int $n, int $lo, int $hi): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = mt_rand($lo, $hi);
    }

    return implode(' ', $a);
};

/** Pasangan l ≤ r acak. */
$rentang = function (int $n): array {
    $l = mt_rand(1, $n);
    $r = mt_rand(1, $n);

    return $l <= $r ? [$l, $r] : [$r, $l];
};

/**
 * Input "N Q", array awal, lalu Q operasi "1 i x" (ubah) atau "2 l r" (tanya).
 * $pu = peluang (persen) sebuah operasi berjenis ubah. Operasi terakhir selalu jenis 2.
 * Mode: 'acak', 'titik' (l = r), 'penuh' (l = 1, r = N), 'turun' (nilai baru selalu lebih kecil dari semua sebelumnya).
 */
$mkUbahTanya = function (int $n, int $q, int $lo, int $hi, int $pu, string $mode = 'acak') use ($arr, $rentang): string {
    $ops = [];
    for ($k = 0; $k < $q; $k++) {
        if ($k < $q - 1 && mt_rand(1, 100) <= $pu) {
            $x = $mode === 'turun' ? $lo - 1 - $k : mt_rand($lo, $hi);
            $ops[] = '1 '.mt_rand(1, $n).' '.$x;
        } else {
            if ($mode === 'titik') {
                $l = $r = mt_rand(1, $n);
            } elseif ($mode === 'penuh') {
                [$l, $r] = [1, $n];
            } else {
                [$l, $r] = $rentang($n);
            }
            $ops[] = "2 $l $r";
        }
    }

    return "$n $q\n".$arr($n, $lo, $hi)."\n".implode("\n", $ops)."\n";
};

$tok = fn (string $input): array => array_map('intval', preg_split('/\s+/', trim($input)));

return [
    [
        'slug' => 'suhu-terendah',
        'lesson' => 'segment-tree',
        'title' => 'Suhu Terendah Gudang Beku',
        'difficulty' => 'Sedang',
        'tags' => ['segment tree', 'minimum rentang', 'update titik'],
        'statement' => '<p>Gudang beku milik Bu Sari dipasangi <strong>N</strong> sensor suhu yang berjajar dan bernomor 1 sampai N. Mula-mula sensor ke-i menunjukkan angka <code>a<sub>i</sub></code> (bisa negatif).</p>
<p>Selama satu shift, teknisi mencatat <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 i x</code>: sensor ke-i mengirim bacaan baru, sehingga angkanya sekarang <code>x</code>.</li><li><code>2 l r</code>: pengawas bertanya, berapa suhu terendah yang sedang ditunjukkan oleh sensor bernomor l sampai r?</li></ul>
<p>Jawab setiap pertanyaan pengawas.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak suhu terendah pada baris tersendiri, sesuai urutan kejadian.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub>, x ≤ 10<sup>9</sup></li><li>1 ≤ i ≤ N dan 1 ≤ l ≤ r ≤ N</li><li>Ada paling sedikit satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "5 6\n3 -2 5 -1 4\n2 1 5\n2 3 5\n1 2 6\n2 1 3\n1 5 -7\n2 4 5\n", 'explanation' => 'Mula-mula suhu terendah sensor 1..5 adalah −2 dan sensor 3..5 (5, −1, 4) adalah −1. Sensor 2 lalu berubah menjadi 6, sehingga sensor 1..3 berisi 3, 6, 5 dengan minimum 3. Terakhir sensor 5 menjadi −7, jadi sensor 4..5 berisi −1 dan −7.'],
            ['input' => "1 3\n5\n2 1 1\n1 1 -5\n2 1 1\n", 'explanation' => 'Hanya ada satu sensor. Pertanyaan dengan l = r tetap sah: jawabannya 5, lalu −5 setelah bacaan berubah.'],
        ],
        'tests' => function () use ($mkUbahTanya) {
            return [
                "1 1\n7\n2 1 1\n", "2 3\n5 -5\n2 1 2\n1 2 9\n2 1 2\n", $mkUbahTanya(10, 15, -20, 20, 40), $mkUbahTanya(1000, 1000, -1000000000, 1000000000, 50),
                $mkUbahTanya(200000, 200000, -1000000000, 1000000000, 50), $mkUbahTanya(200000, 200000, -1000000000, 1000000000, 0),
                $mkUbahTanya(200000, 200000, 0, 1000000000, 50, 'turun'), $mkUbahTanya(200000, 200000, -5, 5, 50),
                $mkUbahTanya(200000, 200000, -1000000000, 1000000000, 30, 'titik'), $mkUbahTanya(200000, 200000, -1000000000, 1000000000, 70, 'penuh'),
                $mkUbahTanya(200000, 200000, -1000000000, 1000000000, 95),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $a = $tok($input);
            [$n, $q] = [$a[0], $a[1]];
            $t = array_fill(0, 2 * $n, PHP_INT_MAX);
            for ($i = 0; $i < $n; $i++) {
                $t[$n + $i] = $a[2 + $i];
            }
            for ($i = $n - 1; $i >= 1; $i--) {
                $t[$i] = min($t[2 * $i], $t[2 * $i + 1]);
            }
            $out = [];
            $p = 2 + $n;
            for ($k = 0; $k < $q; $k++, $p += 3) {
                [$jenis, $x, $y] = [$a[$p], $a[$p + 1], $a[$p + 2]];
                if ($jenis === 1) {
                    $i = $n + $x - 1;
                    $t[$i] = $y;
                    for ($i >>= 1; $i >= 1; $i >>= 1) {
                        $t[$i] = min($t[2 * $i], $t[2 * $i + 1]);
                    }
                } else {
                    $res = PHP_INT_MAX;
                    for ($l = $x - 1 + $n, $r = $y + $n; $l < $r; $l >>= 1, $r >>= 1) {
                        if ($l & 1) {
                            $res = min($res, $t[$l++]);
                        }
                        if ($r & 1) {
                            $res = min($res, $t[--$r]);
                        }
                    }
                    $out[] = $res;
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<int> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];", "const [n, q] = readInts();\nconst a = readInts();   // a[0] = sensor 1\n// setiap kejadian: const [jenis, x, y] = readInts();", "data = sys.stdin.buffer.read().split()   # baca seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\na = list(map(int, data[2:2 + n]))\n# kejadian ke-k: data[2 + n + 3k], data[2 + n + 3k + 1], data[2 + n + 3k + 2]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;

    // Segment tree iteratif: daun sensor i (1-based) ada di t[n + i - 1],
    // simpul dalam i menyimpan minimum dari anak t[2i] dan t[2i + 1].
    vector<int> t(2 * n);
    for (int i = 0; i < n; i++) cin >> t[n + i];
    for (int i = n - 1; i >= 1; i--) t[i] = min(t[2 * i], t[2 * i + 1]);

    string out;
    while (q--) {
        int jenis, x, y;
        cin >> jenis >> x >> y;
        if (jenis == 1) {
            int i = n + x - 1;
            t[i] = y;
            // naik ke akar sambil menghitung ulang setiap leluhur
            for (i >>= 1; i >= 1; i >>= 1) t[i] = min(t[2 * i], t[2 * i + 1]);
        } else {
            int hasil = INT_MAX;
            // rentang daun setengah terbuka [l, r)
            for (int l = x - 1 + n, r = y + n; l < r; l >>= 1, r >>= 1) {
                if (l & 1) hasil = min(hasil, t[l++]);   // l anak kanan: ambil, lalu geser
                if (r & 1) hasil = min(hasil, t[--r]);   // r - 1 anak kiri: ambil
            }
            out += to_string(hasil);
            out += '\n';
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
// daun sensor i (1-based) di t[n + i - 1]; t[i] = min(t[2i], t[2i + 1])
const t = new Int32Array(2 * n);
for (let i = 0; i < n; i++) t[n + i] = a[i];
for (let i = n - 1; i >= 1; i--) t[i] = Math.min(t[2 * i], t[2 * i + 1]);

const out = [];
for (let k = 0; k < q; k++) {
  const [jenis, x, y] = readInts();
  if (jenis === 1) {
    let i = n + x - 1;
    t[i] = y;
    for (i >>= 1; i >= 1; i >>= 1) t[i] = Math.min(t[2 * i], t[2 * i + 1]);
  } else {
    let hasil = Infinity;
    for (let l = x - 1 + n, r = y + n; l < r; l >>= 1, r >>= 1) {
      if (l & 1) hasil = Math.min(hasil, t[l++]);
      if (r & 1) hasil = Math.min(hasil, t[--r]);
    }
    out.push(hasil);
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    # daun sensor i (1-based) di t[n + i - 1]; t[i] = min(t[2i], t[2i + 1])
    t = [0] * n + list(map(int, data[2:2 + n]))
    for i in range(n - 1, 0, -1):
        a = t[2 * i]; b = t[2 * i + 1]
        t[i] = a if a < b else b
    out = []
    pos = 2 + n
    for _ in range(q):
        jenis = data[pos]; x = int(data[pos + 1]); y = int(data[pos + 2])
        pos += 3
        if jenis == b"1":
            i = n + x - 1
            t[i] = y
            i >>= 1
            while i:
                a = t[2 * i]; b = t[2 * i + 1]
                m = a if a < b else b
                if t[i] == m:
                    break          # simpul ini tidak berubah, leluhurnya pun tidak
                t[i] = m
                i >>= 1
        else:
            l = x - 1 + n; r = y + n      # rentang daun setengah terbuka [l, r)
            hasil = t[l]
            while l < r:
                if l & 1:
                    if t[l] < hasil: hasil = t[l]
                    l += 1
                if r & 1:
                    r -= 1
                    if t[r] < hasil: hasil = t[r]
                l >>= 1; r >>= 1
            out.append(hasil)
    sys.stdout.write("\n".join(map(str, out)) + "\n")

main()
CODE,
        ],
        'editorial' => '<p>Cara langsung menelusuri sensor l..r untuk setiap pertanyaan memakan O(N) per pertanyaan, sampai 4 · 10<sup>10</sup> langkah. Kita butuh <strong>segment tree</strong> yang menjawab minimum rentang dan menerima perubahan satu titik dalam O(log N).</p>
<p><strong>Versi iteratif (bottom-up).</strong> Simpan daun di <code>t[N..2N−1]</code> (sensor i di <code>t[N + i − 1]</code>) dan bangun simpul dalam dari <code>i = N − 1</code> turun ke 1 dengan <code>t[i] = min(t[2i], t[2i+1])</code>.</p>
<ul><li><strong>Ubah:</strong> tulis daunnya, lalu naik ke orang tua (<code>i = i / 2</code>) dan hitung ulang setiap simpul sampai akar. Hanya simpul di jalur ini yang memuat sensor tersebut, jadi O(log N).</li>
<li><strong>Tanya:</strong> ubah menjadi rentang daun setengah terbuka <code>[l, r) = [N + l − 1, N + r)</code>. Selama <code>l &lt; r</code>: jika l ganjil, l adalah anak kanan sehingga orang tuanya ikut memuat elemen di luar rentang, jadi ambil <code>t[l]</code> lalu <code>l++</code>; jika r ganjil, ambil <code>t[r − 1]</code> dan <code>r−−</code>. Kemudian naik satu tingkat (<code>l /= 2, r /= 2</code>). Setiap tingkat menyumbang paling banyak dua simpul, jadi O(log N).</li></ul>
<p>Total O(N + Q log N). Jebakan: nilai awal hasil harus "tak hingga" (misalnya <code>INT_MAX</code>), bukan 0, karena suhu bisa negatif. Kumpulkan semua jawaban lalu cetak sekaligus. Pada solusi Python, pembaruan berhenti lebih awal begitu nilai sebuah simpul tidak berubah, karena leluhurnya pasti juga tidak berubah.</p>',
        'hints' => [
            'Menelusuri l..r untuk setiap pertanyaan bisa memakan 200 000 × 200 000 langkah. Kamu butuh struktur yang menyimpan minimum dari potongan-potongan array.',
            'Segment tree: setiap simpul menyimpan minimum dari dua anaknya. Mengubah satu sensor hanya memengaruhi simpul-simpul di jalur dari daun ke akar.',
            'Versi iteratif: daun di t[N..2N−1], t[i] = min(t[2i], t[2i+1]). Untuk menjawab, pakai l = N + l − 1 dan r = N + r; selama l < r ambil t[l++] jika l ganjil dan t[−−r] jika r ganjil, lalu bagi dua keduanya.',
        ],
    ],

    [
        'slug' => 'pemandangan-statis',
        'lesson' => 'segment-tree',
        'title' => 'Potongan Foto Pemandangan',
        'difficulty' => 'Sedang',
        'tags' => ['sparse table', 'rmq', 'query statis'],
        'statement' => '<p>Dimas memotret panorama pegunungan lalu memindainya menjadi <strong>N</strong> kolom yang berjajar dari kiri ke kanan. Kolom ke-i memuat titik puncak setinggi <code>h<sub>i</sub></code> meter. Foto ini sudah jadi dan tidak akan diubah lagi.</p>
<p>Dimas ingin mencoba <strong>Q</strong> potongan foto. Potongan <code>l r</code> hanya memuat kolom l sampai r. Menurutnya, sebuah potongan makin dramatis jika <strong>selisih antara titik tertinggi dan titik terendah</strong> di dalamnya makin besar. Hitung selisih itu untuk setiap potongan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>h<sub>1</sub> … h<sub>N</sub></code>. Q baris berikutnya masing-masing berisi <code>l r</code>.</p>',
        'output_format' => '<p>Q baris. Baris ke-j berisi selisih tinggi tertinggi dan terendah pada potongan ke-j.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>0 ≤ h<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ l ≤ r ≤ N</li></ul>',
        'samples' => [
            ['input' => "8 4\n4 7 2 9 3 3 8 1\n1 8\n2 3\n5 6\n3 7\n", 'explanation' => 'Potongan 1..8: tertinggi 9, terendah 1, selisihnya 8. Potongan 2..3: 7 − 2 = 5. Potongan 5..6 berisi dua kolom setinggi 3, selisihnya 0. Potongan 3..7 berisi 2, 9, 3, 3, 8, jadi 9 − 2 = 7.'],
        ],
        'tests' => function () use ($arr, $rentang) {
            // $mode: 'acak', 'pendek' (panjang ≤ 10), 'campur' (separuh l = r), 'panjang' (hampir seluruh foto)
            $mk = function (int $n, int $q, string $h, string $mode = 'acak') use ($rentang) {
                $qs = [];
                for ($k = 0; $k < $q; $k++) {
                    if ($mode === 'pendek') {
                        $l = mt_rand(1, $n);
                        $r = min($n, $l + mt_rand(0, 9));
                    } elseif ($mode === 'campur' && mt_rand(0, 1) === 1) {
                        $l = $r = mt_rand(1, $n);
                    } elseif ($mode === 'panjang') {
                        $l = mt_rand(1, min($n, 10));
                        $r = mt_rand(max($l, $n - 9), $n);
                    } else {
                        [$l, $r] = $rentang($n);
                    }
                    $qs[] = "$l $r";
                }

                return "$n $q\n$h\n".implode("\n", $qs)."\n";
            };

            return [
                "1 1\n5\n1 1\n", "3 3\n1 3 2\n1 3\n2 3\n1 2\n", $mk(10, 10, $arr(10, 0, 20)), $mk(1000, 1000, $arr(1000, 0, 1000000000)),
                $mk(200000, 200000, $arr(200000, 0, 1000000000)), $mk(200000, 200000, $arr(200000, 0, 1000000000), 'pendek'),
                $mk(200000, 200000, $arr(200000, 0, 3)), $mk(200000, 200000, implode(' ', range(0, 999995000, 5000))),
                $mk(200000, 200000, $arr(200000, 0, 1000000000), 'campur'), $mk(131072, 200000, $arr(131072, 0, 1000000000), 'panjang'),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            // Solusi referensi memakai dua segment tree iteratif (min dan maks).
            $a = $tok($input);
            [$n, $q] = [$a[0], $a[1]];
            $mn = array_fill(0, 2 * $n, PHP_INT_MAX);
            $mx = array_fill(0, 2 * $n, PHP_INT_MIN);
            for ($i = 0; $i < $n; $i++) {
                $mn[$n + $i] = $mx[$n + $i] = $a[2 + $i];
            }
            for ($i = $n - 1; $i >= 1; $i--) {
                $mn[$i] = min($mn[2 * $i], $mn[2 * $i + 1]);
                $mx[$i] = max($mx[2 * $i], $mx[2 * $i + 1]);
            }
            $out = [];
            $p = 2 + $n;
            for ($k = 0; $k < $q; $k++, $p += 2) {
                $lo = PHP_INT_MAX;
                $hi = PHP_INT_MIN;
                for ($l = $a[$p] - 1 + $n, $r = $a[$p + 1] + $n; $l < $r; $l >>= 1, $r >>= 1) {
                    if ($l & 1) {
                        $lo = min($lo, $mn[$l]);
                        $hi = max($hi, $mx[$l]);
                        $l++;
                    }
                    if ($r & 1) {
                        $r--;
                        $lo = min($lo, $mn[$r]);
                        $hi = max($hi, $mx[$r]);
                    }
                }
                $out[] = $hi - $lo;
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<int> h(n);\n    for (int i = 0; i < n; i++) cin >> h[i];   // h[0] = kolom 1", "const [n, q] = readInts();\nconst h = readInts();   // h[0] = kolom 1\n// setiap potongan: const [l, r] = readInts();", "data = sys.stdin.buffer.read().split()   # baca seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\nh = list(map(int, data[2:2 + n]))   # h[0] = kolom 1\n# potongan ke-j: l = data[2 + n + 2j], r = data[2 + n + 2j + 1]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;

    // Sparse table: mx[k][i] = tertinggi pada h[i .. i + 2^k - 1], mn[k][i] = terendah.
    int LOG = 1;
    while ((1 << LOG) <= n) LOG++;
    vector<vector<int>> mx(LOG, vector<int>(n)), mn(LOG, vector<int>(n));
    for (int i = 0; i < n; i++) {
        cin >> mx[0][i];
        mn[0][i] = mx[0][i];
    }
    for (int k = 1; k < LOG; k++) {
        int setengah = 1 << (k - 1);
        for (int i = 0; i + (1 << k) <= n; i++) {
            mx[k][i] = max(mx[k - 1][i], mx[k - 1][i + setengah]);
            mn[k][i] = min(mn[k - 1][i], mn[k - 1][i + setengah]);
        }
    }
    // lg[len] = floor(log2(len))
    vector<int> lg(n + 1, 0);
    for (int i = 2; i <= n; i++) lg[i] = lg[i / 2] + 1;

    string out;
    while (q--) {
        int l, r;
        cin >> l >> r;
        l--;
        r--;                                   // indeks 0-based, inklusif
        int k = lg[r - l + 1];
        // dua blok panjang 2^k: [l, l + 2^k - 1] dan [r - 2^k + 1, r] menutup [l, r]
        int tinggi = max(mx[k][l], mx[k][r - (1 << k) + 1]);
        int rendah = min(mn[k][l], mn[k][r - (1 << k) + 1]);
        out += to_string(tinggi - rendah);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const h = readInts();
// mx[k][i] = tertinggi pada h[i .. i + 2^k - 1], mn[k][i] = terendah
const mx = [Int32Array.from(h)];
const mn = [Int32Array.from(h)];
for (let k = 1; (1 << k) <= n; k++) {
  const setengah = 1 << (k - 1), m = n - (1 << k) + 1;
  const pa = mx[k - 1], pb = mn[k - 1];
  const A = new Int32Array(m), B = new Int32Array(m);
  for (let i = 0; i < m; i++) {
    A[i] = Math.max(pa[i], pa[i + setengah]);
    B[i] = Math.min(pb[i], pb[i + setengah]);
  }
  mx.push(A);
  mn.push(B);
}

const out = [];
for (let j = 0; j < q; j++) {
  let [l, r] = readInts();
  l -= 1;                               // rentang 0-based setengah terbuka [l, r)
  const k = 31 - Math.clz32(r - l);     // floor(log2(panjang))
  r -= 1 << k;                          // awal blok kedua
  out.push(Math.max(mx[k][l], mx[k][r]) - Math.min(mn[k][l], mn[k][r]));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    h = list(map(int, data[2:2 + n]))
    # mx[k][i] = tertinggi pada h[i .. i + 2^k - 1], mn[k][i] = terendah
    mx = [h]
    mn = [h]
    k = 1
    while (1 << k) <= n:
        setengah = 1 << (k - 1)
        m = n - (1 << k) + 1                  # banyak posisi awal yang sah
        a, b = mx[-1], mn[-1]
        mx.append(list(map(max, a[:m], a[setengah:setengah + m])))
        mn.append(list(map(min, b[:m], b[setengah:setengah + m])))
        k += 1
    out = []
    pos = 2 + n
    for _ in range(q):
        l = int(data[pos]) - 1; r = int(data[pos + 1])   # 0-based setengah terbuka [l, r)
        pos += 2
        k = (r - l).bit_length() - 1          # 2^k <= panjang < 2^(k+1)
        r -= 1 << k                           # awal blok kedua
        a, b = mx[k], mn[k]
        tinggi = a[l] if a[l] > a[r] else a[r]
        rendah = b[l] if b[l] < b[r] else b[r]
        out.append(tinggi - rendah)
    sys.stdout.write("\n".join(map(str, out)) + "\n")

main()
CODE,
        ],
        'editorial' => '<p>Tinggi kolom tidak pernah berubah, jadi kita boleh menyiapkan tabel di awal agar setiap pertanyaan dijawab dalam O(1). Itulah <strong>sparse table</strong>.</p>
<p>Definisikan <code>mx[k][i]</code> = tinggi tertinggi pada blok sepanjang 2<sup>k</sup> yang dimulai di i (dan <code>mn[k][i]</code> untuk terendah). Blok panjang 2<sup>k</sup> terdiri dari dua blok panjang 2<sup>k−1</sup>, sehingga <code>mx[k][i] = max(mx[k−1][i], mx[k−1][i + 2<sup>k−1</sup>])</code>. Ada sekitar log N tingkat, masing-masing N entri: pembangunan O(N log N).</p>
<p>Untuk potongan [l, r] dengan panjang L = r − l + 1, pilih k = ⌊log<sub>2</sub> L⌋. Dua blok <code>[l, l + 2<sup>k</sup> − 1]</code> dan <code>[r − 2<sup>k</sup> + 1, r]</code> menutupi seluruh potongan dan mungkin saling tumpang tindih. Untuk maksimum dan minimum, tumpang tindih tidak masalah karena mengambil elemen yang sama dua kali tidak mengubah hasil (operasi <em>idempoten</em>). Jadi jawabannya <code>max(dua blok) − min(dua blok)</code> dalam O(1).</p>
<p>Total O(N log N + Q). Segment tree untuk min dan maks (O(log N) per pertanyaan) juga lolos. Ingat batasan sparse table: tidak mendukung perubahan nilai, dan trik dua blok yang tumpang tindih tidak berlaku untuk jumlah karena bagian yang tumpang tindih akan terhitung dua kali.</p>',
        'hints' => [
            'Tinggi kolom tidak pernah berubah. Bisakah kamu menyiapkan sebuah tabel di awal agar setiap potongan dijawab sangat cepat?',
            'Simpan maksimum dan minimum untuk setiap blok yang panjangnya pangkat dua. Blok panjang 2^k dibentuk dari dua blok panjang 2^(k−1).',
            'Potongan sepanjang L bisa ditutup oleh dua blok panjang 2^k (k = ⌊log₂ L⌋) yang boleh tumpang tindih. Untuk maks dan min, tumpang tindih tidak mengubah jawaban.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'untung-beruntun',
        'lesson' => 'segment-tree',
        'title' => 'Rekor Untung Beruntun',
        'difficulty' => 'Sulit',
        'tags' => ['segment tree', 'penggabungan simpul', 'subarray maksimum'],
        'statement' => '<p>Kedai kopi Pak Bayu mencatat keuntungan harian selama <strong>N</strong> hari. Keuntungan hari ke-i adalah <code>a<sub>i</sub></code> ribu rupiah; nilai negatif berarti hari itu rugi.</p>
<p>Sambil menyiapkan laporan untuk calon investor, Pak Bayu menangani <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 i x</code>: catatan hari ke-i ternyata keliru dan dikoreksi menjadi <code>x</code>.</li><li><code>2 l r</code>: investor bertanya, jika hanya melihat hari l sampai r, berapa total keuntungan terbesar dari <strong>beberapa hari berturut-turut</strong> (paling sedikit satu hari)?</li></ul>
<p>Jawab setiap pertanyaan berdasarkan catatan terbaru.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak jawabannya pada baris tersendiri, sesuai urutan kejadian.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 100 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub>, x ≤ 10<sup>9</sup></li><li>1 ≤ i ≤ N dan 1 ≤ l ≤ r ≤ N</li><li>Ada paling sedikit satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "6 5\n3 -4 5 -1 2 -6\n2 1 6\n2 1 2\n1 4 -10\n2 1 6\n2 6 6\n", 'explanation' => 'Mula-mula hari 3..5 memberi 5 − 1 + 2 = 6, yang terbesar. Pada hari 1..2 pilihan terbaik hanya hari 1 (3), karena 3 − 4 dan −4 lebih kecil. Setelah hari 4 dikoreksi menjadi −10, menyambung melewati hari 4 tidak lagi menguntungkan, sehingga terbaik adalah hari 3 saja (5). Pertanyaan terakhir hanya memuat hari 6, dan pilihan tidak boleh kosong, jadi jawabannya −6.'],
            ['input' => "3 3\n-5 -2 -8\n2 1 3\n1 2 7\n2 1 3\n", 'explanation' => 'Semua hari rugi, jadi pilihan terbaik adalah satu hari dengan rugi terkecil: −2. Setelah hari 2 dikoreksi menjadi 7, jawabannya 7 (hari 2 saja); menambah hari 1 atau hari 3 hanya mengurangi total.'],
        ],
        'tests' => function () use ($mkUbahTanya) {
            return [
                "1 1\n-7\n2 1 1\n", "4 4\n2 -1 2 -1\n2 1 4\n1 2 -5\n2 1 4\n2 2 3\n", $mkUbahTanya(10, 20, -10, 10, 40), $mkUbahTanya(1000, 1000, -1000, 1000, 50),
                $mkUbahTanya(100000, 100000, -1000000000, 1000000000, 50), $mkUbahTanya(100000, 100000, -1000000000, 1000000000, 10),
                $mkUbahTanya(100000, 100000, -1000000000, 1000000000, 90), $mkUbahTanya(100000, 100000, -1000000000, -1, 50),
                $mkUbahTanya(100000, 100000, 1, 1000000000, 50), $mkUbahTanya(100000, 100000, -3, 3, 50),
                $mkUbahTanya(100000, 100000, -1000000000, 1000000000, 30, 'titik'), $mkUbahTanya(99999, 100000, -1000000000, 1000000000, 60, 'penuh'),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            // Referensi: segment tree dengan ukuran pangkat dua, akumulator kiri dan kanan.
            $a = $tok($input);
            [$n, $q] = [$a[0], $a[1]];
            $NEG = -(1 << 60);
            $size = 1;
            while ($size < $n) {
                $size *= 2;
            }
            // S = jumlah, P = prefiks, X = sufiks, B = terbaik; daun cadangan berisi identitas.
            $S = array_fill(0, 2 * $size, 0);
            $P = $X = $B = array_fill(0, 2 * $size, $NEG);
            for ($i = 0; $i < $n; $i++) {
                $S[$size + $i] = $P[$size + $i] = $X[$size + $i] = $B[$size + $i] = $a[2 + $i];
            }
            $hitung = function (int $i) use (&$S, &$P, &$X, &$B) {
                $l = 2 * $i;
                $r = $l + 1;
                $S[$i] = $S[$l] + $S[$r];
                $P[$i] = max($P[$l], $S[$l] + $P[$r]);
                $X[$i] = max($X[$r], $S[$r] + $X[$l]);
                $B[$i] = max($B[$l], $B[$r], $X[$l] + $P[$r]);
            };
            for ($i = $size - 1; $i >= 1; $i--) {
                $hitung($i);
            }
            $out = [];
            $p = 2 + $n;
            for ($k = 0; $k < $q; $k++, $p += 3) {
                [$jenis, $x, $y] = [$a[$p], $a[$p + 1], $a[$p + 2]];
                if ($jenis === 1) {
                    $i = $size + $x - 1;
                    $S[$i] = $P[$i] = $X[$i] = $B[$i] = $y;
                    for ($i >>= 1; $i >= 1; $i >>= 1) {
                        $hitung($i);
                    }
                } else {
                    // akumulator kiri (ls, lp, lx, lb) dan kanan (rs, rp, rx, rb), mulai dari identitas
                    $ls = $rs = 0;
                    $lp = $lx = $lb = $rp = $rx = $rb = $NEG;
                    for ($l = $x - 1 + $size, $r = $y + $size; $l < $r; $l >>= 1, $r >>= 1) {
                        if ($l & 1) {
                            $lb = max($lb, $B[$l], $lx + $P[$l]);
                            $lp = max($lp, $ls + $P[$l]);
                            $lx = max($X[$l], $S[$l] + $lx);
                            $ls += $S[$l];
                            $l++;
                        }
                        if ($r & 1) {
                            $r--;
                            $rb = max($B[$r], $rb, $X[$r] + $rp);
                            $rx = max($rx, $rs + $X[$r]);
                            $rp = max($P[$r], $S[$r] + $rp);
                            $rs += $S[$r];
                        }
                    }
                    $out[] = max($lb, $rb, $lx + $rp);
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<long long> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];", "const [n, q] = readInts();\nconst a = readInts();   // a[0] = hari 1\n// setiap kejadian: const [jenis, x, y] = readInts();", "data = sys.stdin.buffer.read().split()   # baca seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\na = list(map(int, data[2:2 + n]))\n# kejadian ke-k: data[2 + n + 3k], data[2 + n + 3k + 1], data[2 + n + 3k + 2]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

struct Simpul {
    long long jumlah;    // total seluruh rentang
    long long prefiks;   // jumlah awalan (tidak kosong) terbesar
    long long sufiks;    // jumlah akhiran (tidak kosong) terbesar
    long long terbaik;   // jumlah bagian berurutan (tidak kosong) terbesar
};

const long long NEG = -(1LL << 60);          // "minus tak hingga" yang aman dijumlahkan
const Simpul KOSONG = {0, NEG, NEG, NEG};    // elemen identitas untuk gabung

// a di kiri, b di kanan. Urutan penting: gabung(a, b) != gabung(b, a).
Simpul gabung(const Simpul& a, const Simpul& b) {
    Simpul c;
    c.jumlah = a.jumlah + b.jumlah;
    c.prefiks = max(a.prefiks, a.jumlah + b.prefiks);
    c.sufiks = max(b.sufiks, b.jumlah + a.sufiks);
    c.terbaik = max(max(a.terbaik, b.terbaik), a.sufiks + b.prefiks);
    return c;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<Simpul> t(2 * n);
    for (int i = 0; i < n; i++) {
        long long v;
        cin >> v;
        t[n + i] = {v, v, v, v};
    }
    for (int i = n - 1; i >= 1; i--) t[i] = gabung(t[2 * i], t[2 * i + 1]);

    string out;
    while (q--) {
        int jenis;
        long long x, y;
        cin >> jenis >> x >> y;
        if (jenis == 1) {
            int i = n + (int)x - 1;
            t[i] = {y, y, y, y};
            for (i >>= 1; i >= 1; i >>= 1) t[i] = gabung(t[2 * i], t[2 * i + 1]);
        } else {
            // kiri dikumpulkan dari kiri ke kanan, kanan dari kanan ke kiri
            Simpul kiri = KOSONG, kanan = KOSONG;
            for (int l = (int)x - 1 + n, r = (int)y + n; l < r; l >>= 1, r >>= 1) {
                if (l & 1) kiri = gabung(kiri, t[l++]);
                if (r & 1) kanan = gabung(t[--r], kanan);
            }
            out += to_string(gabung(kiri, kanan).terbaik);
            out += '\n';
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
// simpul i: J = jumlah, P = prefiks terbaik, S = sufiks terbaik, B = bagian terbaik
// |nilai| ≤ 10^14, masih tepat untuk Number
const J = new Float64Array(2 * n), P = new Float64Array(2 * n);
const S = new Float64Array(2 * n), B = new Float64Array(2 * n);
for (let i = 0; i < n; i++) J[n + i] = P[n + i] = S[n + i] = B[n + i] = a[i];

// hitung simpul i dari anak kiri 2i dan anak kanan 2i + 1
function hitung(i) {
  const l = 2 * i, r = l + 1;
  J[i] = J[l] + J[r];
  P[i] = Math.max(P[l], J[l] + P[r]);
  S[i] = Math.max(S[r], J[r] + S[l]);
  B[i] = Math.max(B[l], B[r], S[l] + P[r]);
}
for (let i = n - 1; i >= 1; i--) hitung(i);

// Saat menjawab, simpul-simpul ditempel dari kiri ke kanan seperti Kadane:
// akhir = jumlah akhiran terbaik dari bagian yang sudah ditempel, terbaik = jawaban sementara.
let akhir, terbaik;
function tempel(v) {
  terbaik = Math.max(terbaik, B[v], akhir + P[v]);
  akhir = Math.max(S[v], akhir + J[v]);
}

const out = [];
const kanan = [];
for (let k = 0; k < q; k++) {
  const [jenis, x, y] = readInts();
  if (jenis === 1) {
    let i = n + x - 1;
    J[i] = P[i] = S[i] = B[i] = y;
    for (i >>= 1; i >= 1; i >>= 1) hitung(i);
  } else {
    akhir = -Infinity;
    terbaik = -Infinity;
    kanan.length = 0;
    for (let l = x - 1 + n, r = y + n; l < r; l >>= 1, r >>= 1) {
      if (l & 1) tempel(l++);          // simpul kiri sudah datang berurutan
      if (r & 1) kanan.push(--r);      // simpul kanan datang terbalik, simpan dulu
    }
    for (let j = kanan.length - 1; j >= 0; j--) tempel(kanan[j]);
    out.push(terbaik);
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from struct import Struct

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    NEG = -(1 << 60)
    # Simpul i = empat bilangan 64-bit (jumlah, prefiks, sufiks, terbaik) di buf[32i .. 32i + 31].
    # Anak 2i dan 2i + 1 bersebelahan, jadi keduanya terbaca sekaligus dengan satu unpack_from.
    # Ini lebih hemat memori (dan lebih cepat) daripada list berisi tuple.
    satu = Struct("4q")
    baca1 = satu.unpack_from          # baca satu simpul
    tulis = satu.pack_into            # tulis satu simpul
    baca2 = Struct("8q").unpack_from  # baca dua simpul bersaudara
    buf = bytearray(64 * n)
    for i, v in enumerate(map(int, data[2:2 + n]), n):   # daun hari i di simpul n + i - 1
        tulis(buf, 32 * i, v, v, v, v)
    for i in range(n - 1, 0, -1):
        ls, lp, lx, lb, rs, rp, rx, rb = baca2(buf, 64 * i)
        p = ls + rp
        if lp > p: p = lp
        x = rs + lx
        if rx > x: x = rx
        b = lx + rp
        if lb > b: b = lb
        if rb > b: b = rb
        tulis(buf, 32 * i, ls + rs, p, x, b)
    out = []
    pos = 2 + n
    for _ in range(q):
        jenis = data[pos]; u = int(data[pos + 1]); w = int(data[pos + 2])
        pos += 3
        if jenis == b"1":
            i = n + u - 1
            tulis(buf, 32 * i, w, w, w, w)
            i >>= 1
            while i:                       # hitung ulang semua leluhur
                ls, lp, lx, lb, rs, rp, rx, rb = baca2(buf, 64 * i)
                p = ls + rp
                if lp > p: p = lp
                x = rs + lx
                if rx > x: x = rx
                b = lx + rp
                if lb > b: b = lb
                if rb > b: b = rb
                tulis(buf, 32 * i, ls + rs, p, x, b)
                i >>= 1
        else:
            # Tempel simpul dari kiri ke kanan seperti Kadane:
            # akhir = akhiran terbaik bagian yang sudah ditempel, terbaik = jawaban sementara.
            l = u - 1 + n; r = w + n
            akhir = terbaik = NEG
            kanan = []
            while l < r:
                if l & 1:
                    s, p, x, b = baca1(buf, 32 * l)
                    if b > terbaik: terbaik = b
                    p += akhir
                    if p > terbaik: terbaik = p
                    akhir += s
                    if x > akhir: akhir = x
                    l += 1
                if r & 1:
                    r -= 1
                    kanan.append(r)        # simpul kanan datang terbalik, simpan dulu
                l >>= 1; r >>= 1
            for v in reversed(kanan):
                s, p, x, b = baca1(buf, 32 * v)
                if b > terbaik: terbaik = b
                p += akhir
                if p > terbaik: terbaik = p
                akhir += s
                if x > akhir: akhir = x
            out.append(terbaik)
    sys.stdout.write("\n".join(map(str, out)) + "\n")

main()
CODE,
        ],
        'editorial' => '<p>Jika kita tahu jawaban untuk separuh kiri dan separuh kanan sebuah rentang, jawaban gabungannya belum tentu salah satu dari keduanya: bagian terbaik bisa <em>melewati</em> titik tengah. Bagian seperti itu terdiri dari akhiran separuh kiri ditambah awalan separuh kanan. Karena itu setiap simpul segment tree menyimpan empat angka:</p>
<ul><li><code>jumlah</code>: total seluruh rentang;</li><li><code>prefiks</code>: jumlah awalan (tidak kosong) terbesar;</li><li><code>sufiks</code>: jumlah akhiran (tidak kosong) terbesar;</li><li><code>terbaik</code>: jumlah bagian berurutan (tidak kosong) terbesar.</li></ul>
<p>Untuk simpul A di kiri dan B di kanan:</p>
<ul><li><code>jumlah = A.jumlah + B.jumlah</code></li><li><code>prefiks = max(A.prefiks, A.jumlah + B.prefiks)</code>: awalan berhenti di dalam A, atau memuat seluruh A lalu berlanjut ke B;</li><li><code>sufiks = max(B.sufiks, B.jumlah + A.sufiks)</code>, simetris;</li><li><code>terbaik = max(A.terbaik, B.terbaik, A.sufiks + B.prefiks)</code>: seluruhnya di A, seluruhnya di B, atau melewati batas.</li></ul>
<p>Daun berisi (v, v, v, v). Ubah titik: tulis daun lalu hitung ulang leluhurnya, O(log N). Pertanyaan: penggabungan ini <strong>tidak komutatif</strong>, jadi pada versi iteratif simpan dua akumulator, <code>kiri = gabung(kiri, t[l])</code> dan <code>kanan = gabung(t[r], kanan)</code>, lalu jawabannya <code>gabung(kiri, kanan).terbaik</code>. Akumulator dimulai dari elemen identitas (jumlah 0, tiga lainnya "minus tak hingga"). Solusi JavaScript dan Python memakai cara setara yang lebih hemat: menempel simpul dari kiri ke kanan sambil hanya mengingat akhiran terbaik dan jawaban sementara, seperti algoritma Kadane. Solusi Python juga menyimpan keempat angka setiap simpul secara berurutan di sebuah <code>bytearray</code> (modul <code>struct</code>) agar hemat memori dan lebih cepat daripada list berisi tuple.</p>
<p>Total O((N + Q) log N). Jebakan:</p>
<ul><li>Jawaban bisa mencapai 10<sup>14</sup>, jadi gunakan <code>long long</code>.</li><li>Bagian tidak boleh kosong: jika semua hari rugi, jawabannya negatif. Jangan memulai jawaban dari 0.</li><li>Gunakan "minus tak hingga" seperti −2<sup>60</sup>, bukan <code>LLONG_MIN</code>, agar penjumlahan dengan nilai lain tidak overflow.</li></ul>',
        'hints' => [
            'Misalkan kamu tahu jawaban untuk separuh kiri dan separuh kanan. Apakah itu cukup? Bagian terbaik bisa saja melewati titik tengah.',
            'Bagian yang melewati tengah = akhiran terbaik separuh kiri + awalan terbaik separuh kanan. Jadi setiap simpul perlu menyimpan lebih dari satu angka.',
            'Simpan (jumlah, prefiks terbaik, sufiks terbaik, terbaik) di setiap simpul. Saat menjawab, gabungkan simpul-simpul sesuai urutan kiri ke kanan, karena penggabungan ini tidak komutatif.',
        ],
    ],

    [
        'slug' => 'cat-pagar',
        'lesson' => 'segment-tree',
        'title' => 'Kerja Bakti Mengecat Pagar',
        'difficulty' => 'Sulit',
        'tags' => ['segment tree', 'lazy propagation', 'update rentang'],
        'statement' => '<p>Menjelang 17 Agustus, warga gang mengecat pagar yang terdiri dari <strong>N</strong> papan berjajar, bernomor 1 sampai N. Papan ke-i masih menyimpan lapisan cat seberat <code>a<sub>i</sub></code> gram dari tahun lalu.</p>
<p>Selama kerja bakti terjadi <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 l r v</code>: sekelompok relawan mengecat papan l sampai r, sehingga setiap papan di rentang itu bertambah <code>v</code> gram cat.</li><li><code>2 l r</code>: Pak RT ingin tahu total berat cat pada papan l sampai r saat ini.</li></ul>
<p>Jawab setiap pertanyaan Pak RT.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi <code>1 l r v</code> atau <code>2 l r</code>.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak totalnya pada baris tersendiri. Total bisa jauh melebihi 2<sup>31</sup>, tetapi tidak lebih dari 4,2 · 10<sup>15</sup>.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ v ≤ 10<sup>5</sup></li><li>1 ≤ l ≤ r ≤ N</li><li>Ada paling sedikit satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "5 5\n1 2 3 4 5\n2 1 5\n1 2 4 10\n2 3 5\n1 1 3 1\n2 1 2\n", 'explanation' => 'Mula-mula total papan 1..5 adalah 1 + 2 + 3 + 4 + 5 = 15. Setelah papan 2..4 ditambah 10, isinya menjadi 1, 12, 13, 14, 5, sehingga papan 3..5 bertotal 13 + 14 + 5 = 32. Setelah papan 1..3 ditambah 1, isinya 2, 13, 14, 14, 5, dan papan 1..2 bertotal 15.'],
        ],
        'tests' => function () use ($arr, $rentang) {
            // $pu = peluang (persen) operasi tambah; mode 'penuh': setiap penambahan mengenai papan 1..N; 'titik': l = r
            $mk = function (int $n, int $q, int $lo, int $hi, int $vmax, int $pu, string $mode = 'acak') use ($arr, $rentang) {
                $ops = [];
                for ($k = 0; $k < $q; $k++) {
                    if ($mode === 'titik') {
                        $l = $r = mt_rand(1, $n);
                    } else {
                        [$l, $r] = $rentang($n);
                    }
                    if ($k < $q - 1 && mt_rand(1, 100) <= $pu) {
                        if ($mode === 'penuh') {
                            [$l, $r] = [1, $n];
                        }
                        $ops[] = "1 $l $r ".mt_rand(1, $vmax);
                    } else {
                        $ops[] = "2 $l $r";
                    }
                }

                return "$n $q\n".$arr($n, $lo, $hi)."\n".implode("\n", $ops)."\n";
            };

            return [
                "1 2\n0\n1 1 1 5\n2 1 1\n", "3 4\n7 0 2\n1 1 2 3\n2 2 3\n1 3 3 100000\n2 1 3\n", $mk(10, 15, 0, 20, 10, 50), $mk(1000, 1000, 0, 1000000000, 100000, 50),
                $mk(200000, 200000, 0, 1000000000, 100000, 50), $mk(200000, 200000, 0, 1000000000, 100000, 95, 'penuh'),
                $mk(200000, 200000, 0, 1000000000, 100000, 10), $mk(200000, 200000, 0, 0, 1, 50),
                $mk(200000, 200000, 1000000000, 1000000000, 100000, 50, 'titik'), $mk(200000, 200000, 0, 1000000000, 100000, 90),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            // Referensi: dua Fenwick tree (range add, range sum).
            $a = $tok($input);
            [$n, $q] = [$a[0], $a[1]];
            $pre = [0];
            for ($i = 0, $s = 0; $i < $n; $i++) {
                $s += $a[2 + $i];
                $pre[] = $s;
            }
            $f1 = array_fill(0, $n + 1, 0);
            $f2 = array_fill(0, $n + 1, 0);
            $tambahan = function (int $i) use (&$f1, &$f2): int {
                $s1 = $s2 = 0;
                for ($j = $i; $j > 0; $j &= $j - 1) {
                    $s1 += $f1[$j];
                    $s2 += $f2[$j];
                }

                return ($i + 1) * $s1 - $s2;
            };
            $out = [];
            $p = 2 + $n;
            for ($k = 0; $k < $q; $k++) {
                if ($a[$p] === 1) {
                    [$l, $r, $v] = [$a[$p + 1], $a[$p + 2] + 1, $a[$p + 3]];
                    $p += 4;
                    for ($i = $l; $i <= $n; $i += $i & -$i) {
                        $f1[$i] += $v;
                        $f2[$i] += $v * $l;
                    }
                    for ($i = $r; $i <= $n; $i += $i & -$i) {
                        $f1[$i] -= $v;
                        $f2[$i] -= $v * $r;
                    }
                } else {
                    [$l, $r] = [$a[$p + 1], $a[$p + 2]];
                    $p += 3;
                    $out[] = $pre[$r] - $pre[$l - 1] + $tambahan($r) - $tambahan($l - 1);
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<long long> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];", "const [n, q] = readInts();\nconst a = readInts();   // a[0] = papan 1\n// setiap kejadian: const op = readInts();   // [1, l, r, v] atau [2, l, r]", "data = sys.stdin.buffer.read().split()   # baca seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\na = list(map(int, data[2:2 + n]))\npos = 2 + n   # kejadian berikutnya dimulai di data[pos]; jenis 1 memakai 4 angka, jenis 2 memakai 3"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> jumlah;   // jumlah[v] = total cat pada rentang simpul v (sudah benar)
vector<long long> tunda;    // tunda[v] = tambahan per papan yang belum diteruskan ke anak v

void bangun(const vector<long long>& a, int v, int lo, int hi) {
    if (lo == hi) {
        jumlah[v] = a[lo];
        return;
    }
    int mid = (lo + hi) / 2;
    bangun(a, 2 * v, lo, mid);
    bangun(a, 2 * v + 1, mid + 1, hi);
    jumlah[v] = jumlah[2 * v] + jumlah[2 * v + 1];
}

// Tambahkan x ke setiap papan di rentang simpul v (panjang len) tanpa turun ke anak.
void terapkan(int v, int len, long long x) {
    jumlah[v] += x * len;
    tunda[v] += x;
}

// Sebelum turun ke anak, teruskan dulu catatan tunda milik v.
void dorong(int v, int lo, int hi) {
    if (tunda[v] != 0) {
        int mid = (lo + hi) / 2;
        terapkan(2 * v, mid - lo + 1, tunda[v]);
        terapkan(2 * v + 1, hi - mid, tunda[v]);
        tunda[v] = 0;
    }
}

void tambah(int v, int lo, int hi, int l, int r, long long x) {
    if (r < lo || hi < l) return;              // tidak beririsan
    if (l <= lo && hi <= r) {                  // tercakup penuh: cukup dicatat di sini
        terapkan(v, hi - lo + 1, x);
        return;
    }
    dorong(v, lo, hi);
    int mid = (lo + hi) / 2;
    tambah(2 * v, lo, mid, l, r, x);
    tambah(2 * v + 1, mid + 1, hi, l, r, x);
    jumlah[v] = jumlah[2 * v] + jumlah[2 * v + 1];
}

long long tanya(int v, int lo, int hi, int l, int r) {
    if (r < lo || hi < l) return 0;
    if (l <= lo && hi <= r) return jumlah[v];
    dorong(v, lo, hi);
    int mid = (lo + hi) / 2;
    return tanya(2 * v, lo, mid, l, r) + tanya(2 * v + 1, mid + 1, hi, l, r);
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> n >> q;
    vector<long long> a(n + 1);
    for (int i = 1; i <= n; i++) cin >> a[i];
    jumlah.assign(4 * n, 0);
    tunda.assign(4 * n, 0);
    bangun(a, 1, 1, n);

    string out;
    while (q--) {
        int jenis, l, r;
        cin >> jenis >> l >> r;
        if (jenis == 1) {
            long long v;
            cin >> v;
            tambah(1, 1, n, l, r, v);
        } else {
            out += to_string(tanya(1, 1, n, l, r));
            out += '\n';
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
// semua nilai ≤ 4,2 · 10^15 < 2^53, jadi Number masih tepat
const jumlah = new Float64Array(4 * n);   // total rentang simpul (sudah benar)
const tunda = new Float64Array(4 * n);    // tambahan per papan yang belum diteruskan ke anak

function bangun(v, lo, hi) {
  if (lo === hi) { jumlah[v] = a[lo - 1]; return; }
  const mid = (lo + hi) >> 1;
  bangun(2 * v, lo, mid);
  bangun(2 * v + 1, mid + 1, hi);
  jumlah[v] = jumlah[2 * v] + jumlah[2 * v + 1];
}
function terapkan(v, len, x) {
  jumlah[v] += x * len;
  tunda[v] += x;
}
function dorong(v, lo, hi) {
  if (tunda[v] !== 0) {
    const mid = (lo + hi) >> 1;
    terapkan(2 * v, mid - lo + 1, tunda[v]);
    terapkan(2 * v + 1, hi - mid, tunda[v]);
    tunda[v] = 0;
  }
}
function tambah(v, lo, hi, l, r, x) {
  if (r < lo || hi < l) return;
  if (l <= lo && hi <= r) { terapkan(v, hi - lo + 1, x); return; }
  dorong(v, lo, hi);
  const mid = (lo + hi) >> 1;
  tambah(2 * v, lo, mid, l, r, x);
  tambah(2 * v + 1, mid + 1, hi, l, r, x);
  jumlah[v] = jumlah[2 * v] + jumlah[2 * v + 1];
}
function tanya(v, lo, hi, l, r) {
  if (r < lo || hi < l) return 0;
  if (l <= lo && hi <= r) return jumlah[v];
  dorong(v, lo, hi);
  const mid = (lo + hi) >> 1;
  return tanya(2 * v, lo, mid, l, r) + tanya(2 * v + 1, mid + 1, hi, l, r);
}

bangun(1, 1, n);
const out = [];
for (let k = 0; k < q; k++) {
  const op = readInts();
  if (op[0] === 1) tambah(1, 1, n, op[1], op[2], op[3]);
  else out.push(tanya(1, 1, n, op[1], op[2]));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from array import array

# Lazy segment tree rekursif terlalu lambat di Python, jadi di sini dipakai trik
# setara yang lebih ringan: dua Fenwick tree (lihat editorial).

def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    # pre[i] = total cat awal papan 1..i (tidak pernah berubah)
    pre = [0] * (n + 1)
    s = 0
    for i in range(n):
        s += int(data[2 + i])
        pre[i + 1] = s
    # Penambahan dicatat sebagai array beda D: tambah v ke l..r -> D[l] += v, D[r + 1] -= v.
    # Total tambahan papan 1..i = (i + 1) * jumlah(D[1..i]) - jumlah(k * D[k], k = 1..i).
    f1 = array("q", [0]) * (n + 1)    # Fenwick untuk D[k]
    f2 = array("q", [0]) * (n + 1)    # Fenwick untuk k * D[k]
    out = []
    pos = 2 + n
    for _ in range(q):
        if data[pos] == b"1":
            l = int(data[pos + 1]); r = int(data[pos + 2]) + 1; v = int(data[pos + 3])
            pos += 4
            i = l; w = v * l
            while i <= n:
                f1[i] += v; f2[i] += w
                i += i & -i
            i = r; w = v * r
            while i <= n:
                f1[i] -= v; f2[i] -= w
                i += i & -i
        else:
            l = int(data[pos + 1]) - 1; r = int(data[pos + 2])
            pos += 3
            hasil = pre[r] - pre[l]
            s1 = s2 = 0; i = r                 # tambahan papan 1..r
            while i:
                s1 += f1[i]; s2 += f2[i]
                i &= i - 1
            hasil += (r + 1) * s1 - s2
            s1 = s2 = 0; i = l                 # dikurangi tambahan papan 1..l
            while i:
                s1 += f1[i]; s2 += f2[i]
                i &= i - 1
            hasil -= (l + 1) * s1 - s2
            out.append(hasil)
    sys.stdout.write("\n".join(map(str, out)) + "\n")

main()
CODE,
        ],
        'editorial' => '<p>Menambah v ke setiap papan satu per satu memakan O(N) per operasi, terlalu lambat. Segment tree biasa juga tidak cukup, karena update rentang akan mengubah O(N) daun. Solusinya adalah <strong>lazy propagation</strong>.</p>
<p>Setiap simpul menyimpan <code>jumlah</code> (total rentangnya, selalu benar) dan <code>tunda</code> (tambahan per papan yang <em>belum</em> diteruskan ke anak-anaknya).</p>
<ul><li><strong>Tambah v ke [l, r]:</strong> jika rentang simpul tercakup penuh, cukup lakukan <code>jumlah += v · panjang</code> dan <code>tunda += v</code>, lalu berhenti tanpa turun. Jika hanya beririsan sebagian, <em>dorong</em> dulu tunda simpul itu ke kedua anak, rekursi ke anak, lalu hitung ulang <code>jumlah = jumlah kiri + jumlah kanan</code>.</li>
<li><strong>Tanya [l, r]:</strong> simpul yang tercakup penuh langsung mengembalikan <code>jumlah</code>; simpul sebagian mendorong tundanya lalu menjumlahkan jawaban kedua anak.</li></ul>
<p>Sama seperti query biasa, setiap operasi hanya mengunjungi O(log N) simpul, sehingga total O(N + Q log N).</p>
<p><strong>Jebakan:</strong> total bisa mencapai sekitar 4 · 10<sup>15</sup>, jadi <code>jumlah</code>, <code>tunda</code>, dan hasil kali <code>v · panjang</code> harus <code>long long</code>. Jangan lupa mendorong tunda sebelum turun, baik saat update maupun saat query.</p>
<p><strong>Alternatif (dipakai solusi Python):</strong> catat penambahan sebagai array beda D (tambah v ke l..r berarti D[l] += v dan D[r+1] −= v). Total tambahan papan 1..i adalah <code>(i + 1) · ΣD[k] − Σk · D[k]</code> untuk k = 1..i, sehingga cukup dua Fenwick tree: satu untuk D[k] dan satu untuk k · D[k]. Hasilnya sama-sama O(log N) per operasi dengan konstanta lebih kecil, tetapi lazy propagation lebih umum (misalnya untuk "ubah rentang menjadi x" atau minimum rentang).</p>',
        'hints' => [
            'Menambah v ke papan satu per satu bisa memakan N langkah per operasi. Coba catat penambahan hanya di simpul segment tree yang rentangnya tercakup penuh.',
            'Simpul yang tercakup penuh cukup diperbarui jumlahnya (v × panjang rentang) dan diberi catatan "tunda" bahwa anak-anaknya belum ikut ditambah.',
            'Sebelum turun ke anak sebuah simpul (saat update maupun query yang hanya beririsan sebagian), teruskan dulu catatan tundanya ke kedua anak. Pakai long long karena total bisa sekitar 4 · 10^15.',
        ],
        'sample_visual' => 'bars',
    ],
];
