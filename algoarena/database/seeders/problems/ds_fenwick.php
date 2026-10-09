<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Fenwick Tree (Binary Indexed Tree).
 * Semua kode C++ harus lolos C++14.
 * Python membaca seluruh input sekaligus (sys.stdin.buffer) karena input bisa 200 000 baris.
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

/** Rentang acak [l, r]: $maxLen < 0 = seluruh array, 0 = sembarang, > 0 = panjang paling banyak $maxLen. */
$rentang = function (int $n, int $maxLen): array {
    if ($maxLen < 0) {
        return [1, $n];
    }
    $l = mt_rand(1, $n);
    if ($maxLen > 0) {
        return [$l, min($n, $l + mt_rand(0, $maxLen - 1))];
    }
    $r = mt_rand(1, $n);

    return $l <= $r ? [$l, $r] : [$r, $l];
};

/**
 * Untuk setiap elemen (urut dari kiri), hitung banyak elemen SEBELUMNYA yang nilainya
 * lebih kecil ($ketat = true) atau tidak lebih besar ($ketat = false). Dipakai solve soal 2 dan 3.
 *
 * @return int[]
 */
$hitungKiri = function (array $a, bool $ketat): array {
    $u = $a;
    sort($u);
    $nilai = [];
    foreach ($u as $x) {
        if (! $nilai || end($nilai) !== $x) {
            $nilai[] = $x;
        }
    }
    $rank = array_flip($nilai);
    $m = count($nilai);
    $bit = array_fill(0, $m + 1, 0);
    $hasil = [];
    foreach ($a as $x) {
        $r = $rank[$x] + 1;
        $s = 0;
        for ($i = $ketat ? $r - 1 : $r; $i > 0; $i -= $i & -$i) {
            $s += $bit[$i];
        }
        $hasil[] = $s;
        for ($i = $r; $i <= $m; $i += $i & -$i) {
            $bit[$i]++;
        }
    }

    return $hasil;
};

return [
    [
        'slug' => 'stok-gudang',
        'lesson' => 'fenwick',
        'title' => 'Stok Gudang Grosir',
        'difficulty' => 'Mudah',
        'tags' => ['fenwick tree', 'update titik', 'jumlah rentang'],
        'statement' => '<p>Gudang grosir milik Bu Ratna punya <strong>N</strong> rak bernomor 1 sampai N. Mula-mula rak ke-i berisi <code>a<sub>i</sub></code> karton barang. Sepanjang hari terjadi <strong>Q</strong> kejadian berurutan, masing-masing salah satu dari dua jenis berikut:</p>
<ul><li><code>1 i x</code>: petugas menghitung ulang rak i dan mencatat bahwa isinya sekarang <strong>tepat x</strong> karton (bukan bertambah x).</li><li><code>2 l r</code>: Bu Ratna bertanya berapa total karton di rak l sampai r.</li></ul>
<p>Jawab setiap pertanyaan Bu Ratna sesuai keadaan gudang pada saat pertanyaan itu diajukan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak total kartonnya dalam satu baris, sesuai urutan kejadian.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>0 ≤ a<sub>i</sub>, x ≤ 10<sup>9</sup></li><li>1 ≤ i ≤ N, 1 ≤ l ≤ r ≤ N</li><li>Ada paling sedikit satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "5 5\n3 1 4 1 5\n2 1 5\n1 3 10\n2 2 4\n1 1 0\n2 1 3\n", 'explanation' => 'Mula-mula total semua rak 3 + 1 + 4 + 1 + 5 = 14. Rak 3 lalu dihitung ulang menjadi 10 karton, sehingga rak 2..4 berisi 1 + 10 + 1 = 12. Setelah rak 1 menjadi 0, rak 1..3 berisi 0 + 1 + 10 = 11.'],
        ],
        'tests' => function () use ($arr, $rentang) {
            $mk = function (int $n, int $q, int $loA, int $hiA, int $pUbah, int $maxLen = 0) use ($arr, $rentang) {
                $ops = [];
                for ($k = 0; $k < $q; $k++) {
                    if ($k < $q - 1 && mt_rand(1, 100) <= $pUbah) {
                        $ops[] = '1 '.mt_rand(1, $n).' '.mt_rand($loA, $hiA);
                    } else {
                        [$l, $r] = $rentang($n, $maxLen);
                        $ops[] = "2 $l $r";
                    }
                }

                return "$n $q\n".$arr($n, $loA, $hiA)."\n".implode("\n", $ops)."\n";
            };

            return [
                "1 1\n7\n2 1 1\n",
                "1 5\n5\n2 1 1\n1 1 1000000000\n2 1 1\n1 1 0\n2 1 1\n",
                "3 4\n2 2 2\n1 2 2\n2 1 3\n1 2 5\n2 2 2\n",
                $mk(8, 10, 0, 9, 40),
                $mk(1000, 1000, 0, 1000, 50),
                $mk(200000, 200000, 0, 1000000000, 50),
                $mk(200000, 200000, 0, 1000000000, 10),
                $mk(200000, 200000, 0, 10, 90, 5),
                $mk(200000, 200000, 1000000000, 1000000000, 30, -1),
                $mk(200000, 200000, 0, 1000000000, 50, 1),
                $mk(200000, 1, 0, 1000000000, 0),
            ];
        },
        'solve' => function (string $input) {
            $tok = preg_split('/\s+/', trim($input));
            $n = (int) $tok[0];
            $q = (int) $tok[1];
            $stok = [0];
            $bit = [0];
            for ($i = 1; $i <= $n; $i++) {
                $stok[$i] = (int) $tok[1 + $i];
                $bit[$i] = $stok[$i];
            }
            for ($i = 1; $i <= $n; $i++) {
                $j = $i + ($i & -$i);
                if ($j <= $n) {
                    $bit[$j] += $bit[$i];
                }
            }
            $p = 2 + $n;
            $out = [];
            for ($k = 0; $k < $q; $k++) {
                $t = $tok[$p];
                $x = (int) $tok[$p + 1];
                $y = (int) $tok[$p + 2];
                $p += 3;
                if ($t === '1') {
                    $d = $y - $stok[$x];
                    $stok[$x] = $y;
                    for ($i = $x; $i <= $n; $i += $i & -$i) {
                        $bit[$i] += $d;
                    }
                } else {
                    $s = 0;
                    for ($i = $y; $i > 0; $i -= $i & -$i) {
                        $s += $bit[$i];
                    }
                    for ($i = $x - 1; $i > 0; $i -= $i & -$i) {
                        $s -= $bit[$i];
                    }
                    $out[] = $s;
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<long long> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];\n    for (int k = 0; k < q; k++) {\n        int t;\n        long long x, y;\n        cin >> t >> x >> y;   // t = 1: rak x menjadi y karton; t = 2: total rak x..y\n    }", "const [n, q] = readInts();\nconst a = [0, ...readInts()];\nfor (let k = 0; k < q; k++) {\n  const [t, x, y] = readInts(); // t = 1: rak x menjadi y karton; t = 2: total rak x..y\n}", "data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\na = [0] + [int(v) for v in data[2:2 + n]]\npos = 2 + n\nfor _ in range(q):\n    t, x, y = int(data[pos]), int(data[pos + 1]), int(data[pos + 2])\n    pos += 3   # t = 1: rak x menjadi y karton; t = 2: total rak x..y"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> bit;   // bit[i] menyimpan jumlah a[i - lowbit(i) + 1 .. i]

// a[i] += v
void tambah(int i, long long v) {
    for (; i <= n; i += i & -i) bit[i] += v;
}

// a[1] + ... + a[i]
long long prefiks(int i) {
    long long s = 0;
    for (; i > 0; i -= i & -i) s += bit[i];
    return s;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> n >> q;
    vector<long long> stok(n + 1);
    bit.assign(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        cin >> stok[i];
        tambah(i, stok[i]);
    }

    string out;
    while (q--) {
        int t;
        long long x, y;
        cin >> t >> x >> y;
        if (t == 1) {
            // BIT hanya mengenal "tambah", jadi tambahkan selisih nilai baru dan lama
            tambah(x, y - stok[x]);
            stok[x] = y;
        } else {
            out += to_string(prefiks(y) - prefiks(x - 1));
            out += '\n';
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const awal = readInts();
const stok = new Float64Array(n + 1);
const bit = new Float64Array(n + 1); // total ≤ 2 · 10^14, aman untuk Number

// Bangun BIT dalam O(N): setiap simpul menyumbang ke "induknya" i + lowbit(i)
for (let i = 1; i <= n; i++) {
  stok[i] = awal[i - 1];
  bit[i] += stok[i];
  const j = i + (i & -i);
  if (j <= n) bit[j] += bit[i];
}

function tambah(i, v) {
  for (; i <= n; i += i & -i) bit[i] += v;
}
function prefiks(i) {
  let s = 0;
  for (; i > 0; i -= i & -i) s += bit[i];
  return s;
}

const out = [];
for (let k = 0; k < q; k++) {
  const [t, x, y] = readInts();
  if (t === 1) {
    tambah(x, y - stok[x]); // ganti nilai = tambah selisihnya
    stok[x] = y;
  } else {
    out.push(prefiks(y) - prefiks(x - 1));
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()   # seluruh input sekaligus
    n, q = int(data[0]), int(data[1])
    stok = [0] + [int(v) for v in data[2:2 + n]]

    # Bangun BIT dalam O(N): setiap simpul menyumbang ke "induknya" i + lowbit(i)
    bit = stok[:]
    for i in range(1, n + 1):
        j = i + (i & -i)
        if j <= n:
            bit[j] += bit[i]

    out = []
    pos = 2 + n
    for _ in range(q):
        t = data[pos]
        x = int(data[pos + 1])
        y = int(data[pos + 2])
        pos += 3
        if t == b"1":
            d = y - stok[x]          # ganti nilai = tambah selisihnya
            stok[x] = y
            i = x
            while i <= n:
                bit[i] += d
                i += i & -i
        else:
            s = 0
            i = y
            while i > 0:
                s += bit[i]
                i -= i & -i
            i = x - 1
            while i > 0:
                s -= bit[i]
                i -= i & -i
            out.append(s)
    print("\n".join(map(str, out)))


main()   # variabel lokal di dalam fungsi lebih cepat di Python
CODE,
        ],
        'editorial' => '<p>Cara langsung mengubah stok dalam O(1) tetapi menjumlahkan l..r dengan loop O(N). Dengan N = Q = 200 000, total langkahnya bisa 4 · 10<sup>10</sup>. Prefix sum biasa membalik masalahnya: query O(1), tetapi setiap perubahan stok memaksa O(N) prefix dihitung ulang.</p>
<p>Fenwick tree menyeimbangkan keduanya. <code>tambah(i, v)</code> naik dengan <code>i += i &amp; -i</code> dan <code>prefiks(i)</code> turun dengan <code>i -= i &amp; -i</code>; keduanya menyentuh paling banyak sekitar log<sub>2</sub> N ≈ 18 simpul. Total rak l..r = <code>prefiks(r) − prefiks(l − 1)</code>.</p>
<p><strong>Jebakan utama:</strong> kejadian jenis 1 <em>mengganti</em> nilai, sedangkan BIT hanya bisa <em>menambah</em>. Simpan stok saat ini di array terpisah; untuk mengganti stok rak i menjadi x, panggil <code>tambah(i, x − stok[i])</code> lalu set <code>stok[i] = x</code>. Jika langsung memanggil <code>tambah(i, x)</code>, contoh di atas akan mencetak 16 alih-alih 12.</p>
<p>Total bisa mencapai 2 · 10<sup>5</sup> · 10<sup>9</sup> = 2 · 10<sup>14</sup>, jadi pakai <code>long long</code>. Kompleksitas O((N + Q) log N). BIT awal boleh dibangun dengan N kali <code>tambah</code> (O(N log N)) atau dalam O(N) dengan menyumbangkan setiap simpul ke simpul <code>i + (i &amp; -i)</code>.</p>',
        'hints' => [
            'Menjumlahkan l..r dengan loop terlalu lambat, dan prefix sum biasa harus dibangun ulang setiap kali stok berubah. Kamu butuh struktur yang mendukung keduanya dengan cepat.',
            'Fenwick tree: tambah(i, v) dan prefiks(i) sama-sama O(log N) dengan melompat sejauh i &amp; −i. Total rak l..r = prefiks(r) − prefiks(l − 1).',
            'Kejadian 1 mengganti nilai, bukan menambah. Simpan stok saat ini di array lain, panggil tambah(i, x − stok[i]), lalu perbarui stok[i] = x. Jangan lupa long long.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'hitung-inversi',
        'lesson' => 'fenwick',
        'title' => 'Rak Buku Berantakan',
        'difficulty' => 'Sedang',
        'tags' => ['fenwick tree', 'inversi', 'kompresi koordinat'],
        'statement' => '<p>Pustakawan sekolah menata <strong>N</strong> buku dalam satu rak panjang. Setiap buku punya nomor katalog; buku ke-i dari kiri bernomor <code>a<sub>i</sub></code>. Nomor boleh kembar karena beberapa buku punya lebih dari satu eksemplar. Rak dianggap rapi jika nomor katalog tidak pernah turun dari kiri ke kanan.</p>
<p>Untuk mengukur seberapa berantakan rak itu, pustakawan menghitung banyak <strong>pasangan terbalik</strong>: pasangan posisi <code>i &lt; j</code> dengan <code>a<sub>i</sub> &gt; a<sub>j</sub></code>. Dua buku bernomor sama tidak membentuk pasangan terbalik. Ada berapa pasangan terbalik di rak itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Banyak pasangan terbalik.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n5 3 8 3 1\n", 'explanation' => 'Pasangan terbaliknya (ditulis sebagai nomor): (5, 3), (5, 3), (5, 1), (3, 1), (8, 3), (8, 1), dan (3, 1), totalnya 7. Kedua buku bernomor 3 tidak dihitung karena nomornya sama.'],
            ['input' => "4\n1000000000 999999999 7 7\n", 'explanation' => 'Buku pertama lebih besar dari tiga buku di kanannya, buku kedua lebih besar dari dua buku di kanannya: 3 + 2 = 5. Nomor sampai 10<sup>9</sup> tidak bisa langsung dijadikan indeks BIT.'],
        ],
        'tests' => function () use ($arr) {
            $hampirUrut = function (int $n, int $tukar) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, 1000000000);
                }
                sort($a);
                for ($k = 0; $k < $tukar; $k++) {
                    $i = mt_rand(0, $n - 1);
                    $j = mt_rand(0, $n - 1);
                    [$a[$i], $a[$j]] = [$a[$j], $a[$i]];
                }

                return "$n\n".implode(' ', $a)."\n";
            };
            $turunKembar = function (int $n, int $maxV) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, $maxV);
                }
                rsort($a);

                return "$n\n".implode(' ', $a)."\n";
            };
            $turun = [];
            for ($i = 0; $i < 200000; $i++) {
                $turun[] = 1000000000 - 3 * $i;
            }

            return [
                "1\n5\n",
                "2\n2 1\n",
                "5\n1 2 3 4 5\n",
                "6\n7 7 7 7 7 7\n",
                "10\n".$arr(10, 1, 5)."\n",
                "1000\n".$arr(1000, 1, 1000000000)."\n",
                "200000\n".implode(' ', $turun)."\n",
                "200000\n".$arr(200000, 1, 1000000000)."\n",
                "200000\n".$arr(200000, 1, 10)."\n",
                $hampirUrut(200000, 100),
                $turunKembar(200000, 100),
            ];
        },
        'solve' => function (string $input) use ($hitungKiri) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $total = 0;
            foreach ($hitungKiri($a, false) as $j => $tidakLebihBesar) {
                $total += $j - $tidakLebihBesar;
            }

            return (string) $total;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;", "const n = Number(readLine());\nconst a = readInts();", "data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)\nn = int(data[0])\na = [int(v) for v in data[1:1 + n]]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> a(n);
    for (auto& x : a) cin >> x;

    // Kompresi koordinat: nomor -> peringkat 1..m di antara nomor unik
    vector<int> nilai = a;
    sort(nilai.begin(), nilai.end());
    nilai.erase(unique(nilai.begin(), nilai.end()), nilai.end());
    int m = nilai.size();

    vector<int> bit(m + 1, 0);   // bit menghitung banyak buku per peringkat yang sudah dilewati
    long long inversi = 0;       // bisa mencapai ~2 · 10^10
    for (int j = 0; j < n; j++) {
        int r = lower_bound(nilai.begin(), nilai.end(), a[j]) - nilai.begin() + 1;
        // banyak buku di kiri yang nomornya <= a[j]
        int tidakLebihBesar = 0;
        for (int i = r; i > 0; i -= i & -i) tidakLebihBesar += bit[i];
        inversi += j - tidakLebihBesar;   // sisanya (dari j buku di kiri) pasti lebih besar
        for (int i = r; i <= m; i += i & -i) bit[i]++;
    }
    cout << inversi << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();

// Kompresi koordinat: nomor -> peringkat 1..m di antara nomor unik
const urut = Float64Array.from(a).sort();
const nilai = [];
for (let i = 0; i < n; i++) if (i === 0 || urut[i] !== urut[i - 1]) nilai.push(urut[i]);
const m = nilai.length;
const peringkat = new Map();
for (let i = 0; i < m; i++) peringkat.set(nilai[i], i + 1);

const bit = new Int32Array(m + 1);
let inversi = 0; // ≤ 2 · 10^10, masih tepat sebagai Number
for (let j = 0; j < n; j++) {
  const r = peringkat.get(a[j]);
  let tidakLebihBesar = 0; // banyak buku di kiri yang nomornya <= a[j]
  for (let i = r; i > 0; i -= i & -i) tidakLebihBesar += bit[i];
  inversi += j - tidakLebihBesar;
  for (let i = r; i <= m; i += i & -i) bit[i]++;
}
console.log(inversi);
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()   # seluruh input sekaligus
    n = int(data[0])
    a = [int(v) for v in data[1:1 + n]]

    # Kompresi koordinat: nomor -> peringkat 1..m di antara nomor unik
    nilai = sorted(set(a))
    peringkat = {v: i + 1 for i, v in enumerate(nilai)}
    m = len(nilai)

    bit = [0] * (m + 1)
    inversi = 0
    for j in range(n):
        r = peringkat[a[j]]
        tidak_lebih_besar = 0          # banyak buku di kiri yang nomornya <= a[j]
        i = r
        while i > 0:
            tidak_lebih_besar += bit[i]
            i -= i & -i
        inversi += j - tidak_lebih_besar
        i = r
        while i <= m:
            bit[i] += 1
            i += i & -i
    print(inversi)


main()
CODE,
        ],
        'editorial' => '<p>Memeriksa semua pasangan butuh O(N<sup>2</sup>) ≈ 2 · 10<sup>10</sup> langkah. Sebagai gantinya, proses buku dari kiri ke kanan dan hitung untuk setiap buku j berapa buku <em>di kirinya</em> yang bernomor lebih besar. Jumlah semua hitungan itu adalah jawabannya, karena setiap pasangan terbalik (i, j) dihitung tepat sekali, yaitu saat memproses j.</p>
<p>Simpan BIT di atas <em>nilai</em>: <code>cnt[v]</code> = banyak buku bernomor v yang sudah dilewati. Saat memproses buku ke-j (berindeks 0), sudah ada j buku di kiri, dan <code>prefiks(a<sub>j</sub>)</code> dari mereka bernomor ≤ a<sub>j</sub>. Jadi yang lebih besar ada <code>j − prefiks(a<sub>j</sub>)</code>. Setelah itu panggil <code>tambah(a<sub>j</sub>, 1)</code>. Karena yang dikurangkan adalah "≤", buku bernomor sama otomatis tidak terhitung.</p>
<p>Nomor bisa sampai 10<sup>9</sup>, terlalu besar untuk ukuran array BIT. Yang penting hanya <em>urutan</em> nomor, jadi lakukan <strong>kompresi koordinat</strong>: urutkan nomor unik, lalu ganti setiap nomor dengan peringkatnya 1..M (lewat <code>lower_bound</code> atau map). Nomor yang sama mendapat peringkat yang sama.</p>
<p>Jawaban paling besar N(N − 1)/2 ≈ 2 · 10<sup>10</sup>, melebihi batas <code>int</code>: pakai <code>long long</code>. Kompleksitas O(N log N). Alternatif lain yang juga O(N log N) adalah merge sort yang menghitung inversi saat menggabungkan.</p>',
        'hints' => [
            'Memeriksa semua pasangan O(N²) terlalu lambat. Proses buku dari kiri ke kanan: untuk buku ke-j, berapa buku di kirinya yang bernomor lebih besar?',
            'Jika BIT menyimpan banyak buku per nomor, maka banyak buku di kiri yang lebih besar = j − prefiks(a_j). Tetapi nomor bisa sampai 10<sup>9</sup>.',
            'Kompresi koordinat: urutkan nomor unik dan ganti setiap nomor dengan peringkatnya 1..M. Nomor kembar mendapat peringkat sama. Jawaban bisa melebihi 2<sup>31</sup>, pakai long long.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'peringkat-pelari',
        'lesson' => 'fenwick',
        'title' => 'Papan Skor Lari Waktu',
        'difficulty' => 'Sedang',
        'tags' => ['fenwick tree', 'kompresi koordinat', 'peringkat'],
        'statement' => '<p>Lomba lari lintas alam memakai sistem <em>time trial</em>: <strong>N</strong> pelari berlari satu per satu, dan pelari ke-i menyelesaikan rute dalam <code>t<sub>i</sub></code> milidetik. Begitu seorang pelari selesai, papan skor langsung menampilkan peringkat sementaranya di antara semua pelari yang <strong>sudah</strong> berlari, termasuk dirinya sendiri.</p>
<p>Peringkat sementara pelari ke-i adalah 1 ditambah banyak pelari sebelumnya yang waktunya <strong>lebih kecil</strong> dari t<sub>i</sub>. Pelari dengan waktu sama berbagi peringkat yang sama; waktu yang sama tidak dihitung lebih cepat. Tentukan angka yang muncul di papan skor setelah setiap pelari selesai.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>t<sub>1</sub> … t<sub>N</sub></code> sesuai urutan berlari.</p>',
        'output_format' => '<p>N baris; baris ke-i berisi peringkat sementara pelari ke-i tepat setelah ia selesai.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ t<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6\n420 380 450 380 500 400\n", 'explanation' => 'Pelari 3 (450) kalah dari 420 dan 380, jadi peringkat 3. Pelari 4 (380) menyamai pelari 2 dan tidak ada yang lebih cepat, jadi keduanya berbagi peringkat 1. Pelari 5 (500) kalah dari keempat pelari sebelumnya, peringkat 5. Pelari 6 (400) hanya kalah dari dua pelari bercatatan 380, sehingga peringkatnya 3.'],
            ['input' => "3\n7 7 7\n", 'explanation' => 'Semua waktu sama, jadi setiap pelari berbagi peringkat 1.'],
        ],
        'tests' => function () use ($arr) {
            $naik = [];
            $makinCepat = [];
            for ($i = 0; $i < 200000; $i++) {
                $naik[] = 1000 + 5 * $i;
                $makinCepat[] = 900000000 - 4000 * $i + mt_rand(0, 50000000);
            }

            return [
                "1\n100\n",
                "3\n5 5 5\n",
                "4\n10 20 30 40\n",
                "4\n40 30 20 10\n",
                "10\n".$arr(10, 1, 10)."\n",
                "1000\n".$arr(1000, 1, 1000000000)."\n",
                "200000\n".$arr(200000, 1, 1000000000)."\n",
                "200000\n".$arr(200000, 1, 100)."\n",
                "200000\n".implode(' ', $naik)."\n",
                "200000\n".implode(' ', $makinCepat)."\n",
                "200000\n".implode(' ', array_fill(0, 200000, 1000000000))."\n",
            ];
        },
        'solve' => function (string $input) use ($hitungKiri) {
            $lines = T::lines($input);
            $t = T::ints($lines[1]);
            $out = [];
            foreach ($hitungKiri($t, true) as $lebihCepat) {
                $out[] = $lebihCepat + 1;
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> t(n);\n    for (auto& x : t) cin >> x;", "const n = Number(readLine());\nconst t = readInts();", "data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)\nn = int(data[0])\nt = [int(v) for v in data[1:1 + n]]"),
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
    for (auto& x : t) cin >> x;

    // Semua waktu sudah diketahui di awal, jadi bisa dikompresi: waktu -> peringkat 1..m
    vector<int> nilai = t;
    sort(nilai.begin(), nilai.end());
    nilai.erase(unique(nilai.begin(), nilai.end()), nilai.end());
    int m = nilai.size();

    vector<int> bit(m + 1, 0);   // banyak pelari yang sudah selesai, per peringkat waktu
    string out;
    for (int k = 0; k < n; k++) {
        int r = lower_bound(nilai.begin(), nilai.end(), t[k]) - nilai.begin() + 1;
        // pelari yang LEBIH cepat punya peringkat waktu 1..r-1 (waktu sama tidak dihitung)
        int lebihCepat = 0;
        for (int i = r - 1; i > 0; i -= i & -i) lebihCepat += bit[i];
        out += to_string(lebihCepat + 1);
        out += '\n';
        for (int i = r; i <= m; i += i & -i) bit[i]++;
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const t = readInts();

// Kompresi koordinat: waktu -> peringkat 1..m di antara waktu unik
const urut = Float64Array.from(t).sort();
const nilai = [];
for (let i = 0; i < n; i++) if (i === 0 || urut[i] !== urut[i - 1]) nilai.push(urut[i]);
const m = nilai.length;
const peringkat = new Map();
for (let i = 0; i < m; i++) peringkat.set(nilai[i], i + 1);

const bit = new Int32Array(m + 1);
const out = new Array(n);
for (let k = 0; k < n; k++) {
  const r = peringkat.get(t[k]);
  let lebihCepat = 0; // prefiks(r - 1): waktu sama tidak dihitung
  for (let i = r - 1; i > 0; i -= i & -i) lebihCepat += bit[i];
  out[k] = lebihCepat + 1;
  for (let i = r; i <= m; i += i & -i) bit[i]++;
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()   # seluruh input sekaligus
    n = int(data[0])
    t = [int(v) for v in data[1:1 + n]]

    # Kompresi koordinat: waktu -> peringkat 1..m di antara waktu unik
    nilai = sorted(set(t))
    peringkat = {v: i + 1 for i, v in enumerate(nilai)}
    m = len(nilai)

    bit = [0] * (m + 1)
    out = []
    for x in t:
        r = peringkat[x]
        lebih_cepat = 0                # prefiks(r - 1): waktu sama tidak dihitung
        i = r - 1
        while i > 0:
            lebih_cepat += bit[i]
            i -= i & -i
        out.append(lebih_cepat + 1)
        i = r
        while i <= m:
            bit[i] += 1
            i += i & -i
    print("\n".join(map(str, out)))


main()
CODE,
        ],
        'editorial' => '<p>Peringkat pelari ke-i hanya bergantung pada banyak waktu yang lebih kecil dari t<sub>i</sub> di antara t<sub>1</sub>, …, t<sub>i−1</sub>. Mencari ulang setiap kali butuh O(N<sup>2</sup>). Yang kita perlukan adalah struktur yang bisa <em>menerima nilai baru</em> dan <em>menghitung banyak nilai &lt; x</em> dengan cepat: BIT di atas nilai waktu.</p>
<p>Misalkan <code>cnt[v]</code> = banyak pelari yang sudah selesai dengan waktu v. Untuk pelari baru dengan waktu v, banyak yang lebih cepat adalah <code>prefiks(v − 1)</code>; cetak nilai itu ditambah 1, lalu panggil <code>tambah(v, 1)</code>. Memakai <code>v − 1</code> (bukan v) membuat waktu yang sama tidak terhitung, sesuai aturan seri.</p>
<p>Waktu bisa sampai 10<sup>9</sup>. Karena seluruh input dibaca di awal, kita boleh mengerjakannya <em>offline</em>: kompresi koordinat semua waktu menjadi peringkat 1..M, lalu jalankan simulasi di atas peringkat. Waktu yang sama mendapat peringkat yang sama, sehingga <code>prefiks(r − 1)</code> tetap menghitung yang benar-benar lebih cepat.</p>
<p>Kompleksitas O(N log N). Perhatikan bedanya dengan soal inversi: di sini kita menghitung yang <em>lebih kecil</em> dan mencetak hasil setiap langkah, bukan menjumlahkannya.</p>',
        'hints' => [
            'Peringkat pelari ke-i = 1 + banyak waktu sebelumnya yang lebih kecil. Struktur apa yang bisa menambah satu nilai dan menghitung banyak nilai yang lebih kecil dari x, keduanya dengan cepat?',
            'BIT di atas nilai waktu: cnt[v] = banyak pelari bercatatan v. Banyak yang lebih cepat = prefiks(v − 1). Masalahnya, v bisa sampai 10<sup>9</sup>.',
            'Semua waktu diketahui di awal, jadi kompresi dulu: ganti t dengan peringkatnya r di antara waktu unik. Cetak 1 + prefiks(r − 1), lalu tambah(r, 1).',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'tambah-dan-jumlah',
        'lesson' => 'fenwick',
        'title' => 'Proyek Perataan Jalan',
        'difficulty' => 'Sulit',
        'tags' => ['fenwick tree', 'update rentang', 'jumlah rentang', 'dua bit'],
        'statement' => '<p>Dinas pekerjaan umum sedang meratakan jalan desa yang dibagi menjadi <strong>N</strong> segmen berurutan. Ketinggian segmen ke-i diukur dari sebuah patokan dan bernilai <code>a<sub>i</sub></code> milimeter (negatif jika lebih rendah dari patokan). Selama proyek berlangsung terjadi <strong>Q</strong> kejadian berurutan:</p>
<ul><li><code>1 l r v</code>: truk menimbun (v &gt; 0) atau mengeruk (v &lt; 0) segmen l sampai r, sehingga ketinggian <em>setiap</em> segmen di rentang itu berubah sebesar v.</li><li><code>2 l r</code>: pengawas meminta total ketinggian segmen l sampai r untuk menghitung rata-ratanya.</li></ul>
<p>Jawab setiap permintaan pengawas sesuai keadaan jalan saat itu.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak total ketinggiannya dalam satu baris.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>−10<sup>4</sup> ≤ v ≤ 10<sup>4</sup></li><li>1 ≤ l ≤ r ≤ N</li><li>Ada paling sedikit satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "5 5\n2 -1 3 0 4\n2 1 5\n1 2 4 3\n2 1 3\n1 1 5 -2\n2 3 5\n", 'explanation' => 'Mula-mula total semua segmen 2 − 1 + 3 + 0 + 4 = 8. Segmen 2..4 ditimbun 3 sehingga ketinggiannya menjadi 2, 2, 6, 3, 4 dan segmen 1..3 berjumlah 10. Lalu semua segmen dikeruk 2 menjadi 0, 0, 4, 1, 2, sehingga segmen 3..5 berjumlah 7.'],
        ],
        'tests' => function () use ($arr, $rentang) {
            $mk = function (int $n, int $q, int $loA, int $hiA, int $maxV, int $pUbah, int $maxLen = 0) use ($arr, $rentang) {
                $ops = [];
                for ($k = 0; $k < $q; $k++) {
                    [$l, $r] = $rentang($n, $maxLen);
                    if ($k < $q - 1 && mt_rand(1, 100) <= $pUbah) {
                        $ops[] = "1 $l $r ".mt_rand(-$maxV, $maxV);
                    } else {
                        $ops[] = "2 $l $r";
                    }
                }

                return "$n $q\n".$arr($n, $loA, $hiA)."\n".implode("\n", $ops)."\n";
            };
            // Semua elemen = $a, hampir semua kejadian menambah $v ke seluruh jalan: nilai mendekati batas.
            $ekstrem = function (int $n, int $q, int $a, int $v, bool $acak) {
                $ops = [];
                for ($k = 0; $k < $q; $k++) {
                    if ($k % 1000 === 999 || $k === $q - 1) {
                        if ($acak) {
                            $l = mt_rand(1, $n);
                            $r = mt_rand($l, $n);
                            $ops[] = "2 $l $r";
                        } else {
                            $ops[] = "2 1 $n";
                        }
                    } else {
                        $ops[] = "1 1 $n $v";
                    }
                }

                return "$n $q\n".implode(' ', array_fill(0, $n, $a))."\n".implode("\n", $ops)."\n";
            };

            return [
                "1 1\n5\n2 1 1\n",
                "1 4\n-1000000000\n1 1 1 -10000\n2 1 1\n1 1 1 10000\n2 1 1\n",
                "4 5\n0 0 0 0\n1 1 4 1\n1 2 3 -1\n2 1 4\n2 2 3\n2 4 4\n",
                $mk(8, 10, -10, 10, 5, 50),
                $mk(1000, 1000, -1000, 1000, 100, 50),
                $mk(200000, 200000, -1000000000, 1000000000, 10000, 50),
                $mk(200000, 200000, -1000000000, 1000000000, 10000, 20, 10),
                $mk(200000, 200000, -1000000000, 1000000000, 10000, 80),
                $mk(200000, 200000, -1000000000, 1000000000, 10000, 50, 1),
                $ekstrem(200000, 200000, 1000000000, 10000, false),
                $ekstrem(200000, 200000, -1000000000, -10000, true),
            ];
        },
        'solve' => function (string $input) {
            $tok = preg_split('/\s+/', trim($input));
            $n = (int) $tok[0];
            $q = (int) $tok[1];
            $awal = [0];
            for ($i = 1; $i <= $n; $i++) {
                $awal[$i] = $awal[$i - 1] + (int) $tok[1 + $i];
            }
            $b1 = array_fill(0, $n + 2, 0);
            $b2 = array_fill(0, $n + 2, 0);
            $p = 2 + $n;
            $out = [];
            for ($k = 0; $k < $q; $k++) {
                $t = $tok[$p];
                $l = (int) $tok[$p + 1];
                $r = (int) $tok[$p + 2];
                if ($t === '1') {
                    $v = (int) $tok[$p + 3];
                    $p += 4;
                    $w = $v * ($l - 1);
                    for ($i = $l; $i <= $n; $i += $i & -$i) {
                        $b1[$i] += $v;
                        $b2[$i] += $w;
                    }
                    $w = $v * $r;
                    for ($i = $r + 1; $i <= $n; $i += $i & -$i) {
                        $b1[$i] -= $v;
                        $b2[$i] -= $w;
                    }
                } else {
                    $p += 3;
                    $hasil = 0;
                    foreach ([[$r, 1], [$l - 1, -1]] as [$x, $tanda]) {
                        $s1 = 0;
                        $s2 = 0;
                        for ($i = $x; $i > 0; $i -= $i & -$i) {
                            $s1 += $b1[$i];
                            $s2 += $b2[$i];
                        }
                        $hasil += $tanda * ($awal[$x] + $s1 * $x - $s2);
                    }
                    $out[] = $hasil;
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<long long> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];\n    for (int k = 0; k < q; k++) {\n        int t, l, r;\n        cin >> t >> l >> r;\n        if (t == 1) {\n            long long v;\n            cin >> v;\n        }\n    }", "const [n, q] = readInts();\nconst a = [0, ...readInts()];\nfor (let k = 0; k < q; k++) {\n  const op = readInts(); // [1, l, r, v] atau [2, l, r]\n}", "data = sys.stdin.buffer.read().split()   # seluruh input sekaligus (cepat)\nn, q = int(data[0]), int(data[1])\na = [0] + [int(v) for v in data[2:2 + n]]\npos = 2 + n\nfor _ in range(q):\n    t, l, r = int(data[pos]), int(data[pos + 1]), int(data[pos + 2])\n    pos += 3\n    if t == 1:\n        v = int(data[pos])\n        pos += 1"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<long long> awal;     // awal[x] = jumlah nilai awal a[1..x] (tidak pernah berubah)
vector<long long> b1, b2;   // dua BIT yang mencatat semua penambahan

void tambah(vector<long long>& b, int i, long long v) {
    for (; i <= n; i += i & -i) b[i] += v;
}

long long jumlah(const vector<long long>& b, int i) {
    long long s = 0;
    for (; i > 0; i -= i & -i) s += b[i];
    return s;
}

// a[l..r] += v
void tambahRentang(int l, int r, long long v) {
    // Pengaruh pada S(x) = a[1] + ... + a[x]:
    //   l <= x <= r : v*x - v*(l-1)
    //   x > r       : v*(r-l+1) = 0*x - (v*(l-1) - v*r)
    // Koefisien x disimpan di b1, konstanta yang dikurangkan di b2.
    tambah(b1, l, v);
    tambah(b1, r + 1, -v);
    tambah(b2, l, v * (l - 1));
    tambah(b2, r + 1, -v * r);
}

// S(x) = a[1] + ... + a[x] saat ini
long long prefiks(int x) {
    return awal[x] + jumlah(b1, x) * x - jumlah(b2, x);
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> n >> q;
    awal.assign(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        long long x;
        cin >> x;
        awal[i] = awal[i - 1] + x;
    }
    b1.assign(n + 2, 0);
    b2.assign(n + 2, 0);

    string out;
    while (q--) {
        int t, l, r;
        cin >> t >> l >> r;
        if (t == 1) {
            long long v;
            cin >> v;
            tambahRentang(l, r, v);
        } else {
            out += to_string(prefiks(r) - prefiks(l - 1));
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

// awal[x] = jumlah nilai awal a[1..x]; semua nilai ≤ ~10^15, masih tepat sebagai Number
const awal = new Float64Array(n + 1);
for (let i = 1; i <= n; i++) awal[i] = awal[i - 1] + a[i - 1];
const b1 = new Float64Array(n + 2); // koefisien x
const b2 = new Float64Array(n + 2); // konstanta yang dikurangkan

function tambah(b, i, v) {
  for (; i <= n; i += i & -i) b[i] += v;
}
function jumlah(b, i) {
  let s = 0;
  for (; i > 0; i -= i & -i) s += b[i];
  return s;
}
// S(x) = a[1] + ... + a[x] saat ini
function prefiks(x) {
  return awal[x] + jumlah(b1, x) * x - jumlah(b2, x);
}

const out = [];
for (let k = 0; k < q; k++) {
  const op = readInts();
  const l = op[1], r = op[2];
  if (op[0] === 1) {
    const v = op[3];
    // l <= x <= r: S(x) bertambah v*x - v*(l-1); x > r: bertambah v*(r-l+1)
    tambah(b1, l, v);
    tambah(b1, r + 1, -v);
    tambah(b2, l, v * (l - 1));
    tambah(b2, r + 1, -v * r);
  } else {
    out.push(prefiks(r) - prefiks(l - 1));
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from array import array


def main():
    data = list(map(int, sys.stdin.buffer.read().split()))   # seluruh input sekaligus
    n, q = data[0], data[1]

    # awal[x] = jumlah nilai awal a[1..x] (tidak pernah berubah)
    awal = [0] * (n + 1)
    s = 0
    for i in range(1, n + 1):
        s += data[1 + i]
        awal[i] = s

    # array bertipe "q" (int64) menyimpan angka berdampingan di memori,
    # sehingga akses acak lebih cepat daripada list berisi objek int
    b1 = array("q", bytes(8 * (n + 2)))   # koefisien x
    b2 = array("q", bytes(8 * (n + 2)))   # konstanta yang dikurangkan
    out = []
    pos = 2 + n
    for _ in range(q):
        l = data[pos + 1]
        r = data[pos + 2]
        if data[pos] == 1:
            v = data[pos + 3]
            pos += 4
            # l <= x <= r: S(x) bertambah v*x - v*(l-1); x > r: bertambah v*(r-l+1)
            w = v * (l - 1)
            i = l
            while i <= n:            # b1 += v dan b2 += v*(l-1) di posisi l
                b1[i] += v
                b2[i] += w
                i += i & -i
            w = v * r
            i = r + 1
            while i <= n:            # b1 -= v dan b2 -= v*r di posisi r+1
                b1[i] -= v
                b2[i] -= w
                i += i & -i
        else:
            pos += 3
            # S(x) = awal[x] + jumlah(b1, x) * x - jumlah(b2, x); jawab S(r) - S(l-1)
            s1 = s2 = 0
            i = r
            while i > 0:
                s1 += b1[i]
                s2 += b2[i]
                i -= i & -i
            hasil = awal[r] + s1 * r - s2
            x = l - 1
            s1 = s2 = 0
            i = x
            while i > 0:
                s1 += b1[i]
                s2 += b2[i]
                i -= i & -i
            out.append(hasil - (awal[x] + s1 * x - s2))
    print("\n".join(map(str, out)))


main()
CODE,
        ],
        'editorial' => '<p>Kedua operasi bekerja pada rentang. Segment tree dengan <em>lazy propagation</em> bisa menyelesaikannya, tetapi dua Fenwick tree jauh lebih ringkas.</p>
<p>Nilai awal hanya berubah lewat penambahan, jadi simpan prefix sum biasa <code>awal[x]</code> dan biarkan BIT mencatat <em>tambahan</em> saja. Tinjau satu penambahan v pada [l, r] dan pengaruhnya pada prefix sum S(x) = a<sub>1</sub> + … + a<sub>x</sub>:</p>
<ul><li>x &lt; l: tidak berubah;</li><li>l ≤ x ≤ r: bertambah v · (x − l + 1) = <strong>v · x − v · (l − 1)</strong>;</li><li>x &gt; r: bertambah v · (r − l + 1) = <strong>0 · x − (v · (l − 1) − v · r)</strong>.</li></ul>
<p>Setiap kasus berbentuk <code>c · x − d</code>, dan c, d berubah hanya di posisi l dan r + 1. Jadi simpan c di BIT pertama dan d di BIT kedua sebagai update titik:</p>
<ul><li><code>B1.tambah(l, v)</code>, <code>B1.tambah(r + 1, −v)</code>;</li><li><code>B2.tambah(l, v · (l − 1))</code>, <code>B2.tambah(r + 1, −v · r)</code>.</li></ul>
<p>Maka <code>S(x) = awal[x] + B1.prefiks(x) · x − B2.prefiks(x)</code> dan jumlah segmen l..r = S(r) − S(l − 1). Setiap kejadian O(log N), total O(N + Q log N). Perhatikan B1 sendirian adalah BIT di atas array selisih: <code>B1.prefiks(x)</code> adalah total tambahan pada satu segmen x (teknik update rentang + query titik). B2 adalah koreksinya agar bisa menjumlahkan rentang.</p>
<p><strong>Awas overflow:</strong> satu segmen bisa mencapai 10<sup>9</sup> + 2 · 10<sup>5</sup> · 10<sup>4</sup> = 3 · 10<sup>9</sup>, dan totalnya sekitar 6 · 10<sup>14</sup>. Gunakan <code>long long</code>, termasuk saat menghitung <code>v · r</code> dan <code>B1.prefiks(x) · x</code>. Semua nilai antara masih di bawah 9 · 10<sup>15</sup>, jadi Number di JavaScript tetap tepat.</p>',
        'hints' => [
            'Mulai dari versi lebih mudah: update rentang dan query satu titik. BIT di atas array selisih menyelesaikannya: tambah v di l dan −v di r + 1.',
            'Penambahan v pada [l, r] membuat prefix sum S(x) bertambah v·x − v·(l − 1) untuk l ≤ x ≤ r dan v·(r − l + 1) untuk x &gt; r. Keduanya berbentuk c·x − d.',
            'Pakai dua BIT: B1 menyimpan c (v di l, −v di r + 1) dan B2 menyimpan d (v·(l − 1) di l, −v·r di r + 1). S(x) = B1.prefiks(x)·x − B2.prefiks(x), ditambah prefix sum nilai awal.',
        ],
    ],
];
