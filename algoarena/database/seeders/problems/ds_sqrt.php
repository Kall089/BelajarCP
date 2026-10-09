<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Dekomposisi Akar & Algoritma Mo: blok dengan tag untuk tambah rentang +
 * maksimum rentang, trik berat-ringan dengan update titik, dan dua soal algoritma Mo.
 * Semua kode C++ harus lolos C++14. Batas N, Q dipilih agar solusi Python juga cukup cepat.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/**
 * Rentang acak [l, r] di 1..n.
 * $mode = 0: l dan r acak bebas; $mode > 0: panjang paling banyak $mode;
 * $mode < 0: rentang hampir penuh (l di awal, r di akhir array).
 */
$rentang = function (int $n, int $mode): array {
    if ($mode === 0) {
        $l = mt_rand(1, $n);
        $r = mt_rand(1, $n);

        return $l <= $r ? [$l, $r] : [$r, $l];
    }
    if ($mode > 0) {
        $len = mt_rand(1, min($n, $mode));
        $l = mt_rand(1, $n - $len + 1);

        return [$l, $l + $len - 1];
    }
    $l = mt_rand(1, max(1, intdiv($n, 50)));
    $r = mt_rand(max($l, $n - intdiv($n, 50)), $n);

    return [$l, $r];
};

/**
 * Urutan Mo untuk solusi referensi PHP: kelompokkan menurut blok l, lalu r naik pada blok genap
 * dan turun pada blok ganjil. Mengembalikan indeks kueri dalam urutan diproses.
 */
$urutMo = function (array $L, array $R, int $n): array {
    $q = count($L);
    $B = max(1, intdiv($n, max(1, (int) sqrt($q))));
    $kunci = [];
    foreach ($L as $j => $l) {
        $b = intdiv($l, $B);
        $kunci[$j] = $b * 1048576 + ($b % 2 ? 1048575 - $R[$j] : $R[$j]);
    }
    asort($kunci);

    return array_keys($kunci);
};

/** Baca "N Q", array, lalu Q baris "l r" (indeks 0, tertutup). */
$bacaKueri = function (string $input): array {
    $lines = T::lines($input);
    [$n, $q] = T::ints($lines[0]);
    $a = T::ints($lines[1]);
    $L = [];
    $R = [];
    for ($j = 0; $j < $q; $j++) {
        [$l, $r] = T::ints($lines[$j + 2]);
        $L[] = $l - 1;
        $R[] = $r - 1;
    }

    return [$n, $q, $a, $L, $R];
};

return [
    [
        'slug' => 'muka-air-sungai',
        'lesson' => 'sqrt',
        'title' => 'Muka Air Sungai',
        'difficulty' => 'Mudah',
        'tags' => ['dekomposisi akar', 'tag blok', 'maksimum rentang'],
        'statement' => '<p>Sepanjang sebuah sungai dipasang <strong>N</strong> pos pengukur bernomor 1 sampai N dari hulu ke hilir. Mula-mula pos i mencatat ketinggian muka air h<sub>i</sub> sentimeter. Ketinggian diukur terhadap sebuah patokan, jadi bisa saja negatif.</p>
<p>Selama musim hujan, petugas siaga banjir mencatat <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 l r v</code>: hujan atau pintu air mengubah muka air di <em>semua</em> pos l sampai r sebesar v sentimeter (v positif berarti naik, v negatif berarti surut).</li>
<li><code>2 l r</code>: berapa ketinggian muka air tertinggi di antara pos l sampai r saat ini?</li></ul>
<p>Jawab setiap kejadian jenis 2.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>h<sub>1</sub> h<sub>2</sub> … h<sub>N</sub></code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak ketinggian tertinggi pada satu baris, sesuai urutan kejadian.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 40 000</li><li>1 ≤ Q ≤ 40 000</li><li>−10<sup>9</sup> ≤ h<sub>i</sub> ≤ 10<sup>9</sup></li><li>−10<sup>9</sup> ≤ v ≤ 10<sup>9</sup></li><li>1 ≤ l ≤ r ≤ N</li><li>Ada setidaknya satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "8 7\n3 -1 4 1 5 -9 2 6\n2 1 8\n1 2 5 3\n2 1 4\n1 4 8 -5\n2 4 8\n1 1 3 -10\n2 1 3\n", 'explanation' => 'Awalnya pos tertinggi di antara pos 1–8 adalah pos 8 (6 cm). Pos 2–5 naik 3 cm sehingga ketinggian menjadi 3 2 7 4 8 −9 2 6, dan yang tertinggi di pos 1–4 adalah 7. Pos 4–8 lalu surut 5 cm menjadi 3 2 7 −1 3 −14 −3 1; yang tertinggi di pos 4–8 adalah 3 (pos 5). Terakhir pos 1–3 surut 10 cm menjadi −7 −8 −3, jadi jawabannya −3: yang tertinggi pun bisa negatif.'],
        ],
        'tests' => function () use ($rentang) {
            // $pTambah: peluang (persen) sebuah kejadian berjenis 1; $mode: lihat $rentang
            $mk = function (int $n, int $q, int $hMaks, int $vMin, int $vMaks, int $pTambah, int $mode) use ($rentang): string {
                $h = [];
                for ($i = 0; $i < $n; $i++) {
                    $h[] = mt_rand(-$hMaks, $hMaks);
                }
                $out = ["$n $q", implode(' ', $h)];
                for ($j = 0; $j < $q; $j++) {
                    [$l, $r] = $rentang($n, $mode);
                    // kejadian terakhir selalu pertanyaan
                    $out[] = $j < $q - 1 && mt_rand(1, 100) <= $pTambah ? "1 $l $r ".mt_rand($vMin, $vMaks) : "2 $l $r";
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n5\n2 1 1\n",
                "3 5\n-1 -2 -3\n2 1 3\n1 1 1 -5\n2 1 3\n1 2 3 4\n2 1 3\n",
                $mk(10, 30, 10, -5, 5, 50, 0),
                $mk(7, 2000, 100, -50, 50, 50, 0),
                $mk(300, 1000, 1000000000, -1000000000, 1000000000, 50, 0),
                $mk(40000, 40000, 1000000000, -1000000000, 1000000000, 50, 0),
                $mk(40000, 40000, 1000000000, -1000000000, 1000000000, 50, -1),
                $mk(40000, 40000, 1000, -1000000000, -1, 50, -1),
                $mk(40000, 40000, 5, -3, 3, 70, 0),
                $mk(40000, 40000, 1000000000, 0, 1000000000, 30, 200),
                $mk(39999, 40000, 1000000000, -1000000000, 1000000000, 90, -1),
                $mk(2, 1000, 10, -10, 10, 50, 0),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $h = T::ints($lines[1]);
            $B = max(1, (int) sqrt($n));
            $nb = intdiv($n + $B - 1, $B);
            $tag = array_fill(0, $nb, 0);
            $mx = array_fill(0, $nb, 0);
            $bangun = function (int $k) use (&$h, &$mx, $B, $n) {
                $m = PHP_INT_MIN;
                $akhir = min($n, ($k + 1) * $B);
                for ($i = $k * $B; $i < $akhir; $i++) {
                    if ($h[$i] > $m) {
                        $m = $h[$i];
                    }
                }
                $mx[$k] = $m;
            };
            for ($k = 0; $k < $nb; $k++) {
                $bangun($k);
            }
            $out = [];
            for ($j = 2; $j < $q + 2; $j++) {
                $e = T::ints($lines[$j]);
                $l = $e[1] - 1;
                $r = $e[2] - 1;
                $bl = intdiv($l, $B);
                $br = intdiv($r, $B);
                if ($e[0] === 1) {
                    $v = $e[3];
                    if ($bl === $br) {
                        for ($i = $l; $i <= $r; $i++) {
                            $h[$i] += $v;
                        }
                        $bangun($bl);
                        continue;
                    }
                    for ($i = $l, $akhir = ($bl + 1) * $B; $i < $akhir; $i++) {
                        $h[$i] += $v;
                    }
                    $bangun($bl);
                    for ($k = $bl + 1; $k < $br; $k++) {
                        $tag[$k] += $v;
                    }
                    for ($i = $br * $B; $i <= $r; $i++) {
                        $h[$i] += $v;
                    }
                    $bangun($br);
                } else {
                    $m = PHP_INT_MIN;
                    if ($bl === $br) {
                        for ($i = $l; $i <= $r; $i++) {
                            $m = max($m, $h[$i] + $tag[$bl]);
                        }
                    } else {
                        for ($i = $l, $akhir = ($bl + 1) * $B; $i < $akhir; $i++) {
                            $m = max($m, $h[$i] + $tag[$bl]);
                        }
                        for ($k = $bl + 1; $k < $br; $k++) {
                            $m = max($m, $mx[$k] + $tag[$k]);
                        }
                        for ($i = $br * $B; $i <= $r; $i++) {
                            $m = max($m, $h[$i] + $tag[$br]);
                        }
                    }
                    $out[] = $m;
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<long long> h(n + 1);\n    for (int i = 1; i <= n; i++) cin >> h[i];\n    vector<array<long long, 4>> kejadian(q);   // {jenis, l, r, v}; v = 0 untuk jenis 2\n    for (auto& k : kejadian) {\n        cin >> k[0] >> k[1] >> k[2];\n        k[3] = 0;\n        if (k[0] == 1) cin >> k[3];\n    }", "const [n, q] = readInts();\nconst h = readInts();\nconst kejadian = [];\nfor (let i = 0; i < q; i++) kejadian.push(readInts()); // [1, l, r, v] atau [2, l, r]", "n, q = map(int, input().split())\nh = list(map(int, input().split()))\nkejadian = [list(map(int, input().split())) for _ in range(q)]  # [1, l, r, v] atau [2, l, r]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n, B;
vector<long long> h;     // ketinggian TANPA tag blok
vector<long long> mx;    // mx[k]  = maksimum h di blok k, juga tanpa tag
vector<long long> tag;   // tag[k] = tambahan untuk SELURUH blok k
// Ketinggian sebenarnya pos i = h[i] + tag[i / B]; maksimum sebenarnya blok k = mx[k] + tag[k].

// Hitung ulang maksimum blok k dari semua elemennya: O(B)
void bangun(int k) {
    int awal = k * B, akhir = min(n, awal + B);
    mx[k] = LLONG_MIN;
    for (int i = awal; i < akhir; i++) mx[k] = max(mx[k], h[i]);
}

void tambah(int l, int r, long long v) {
    int bl = l / B, br = r / B;
    if (bl == br) {                                     // l dan r di blok yang sama
        for (int i = l; i <= r; i++) h[i] += v;
        bangun(bl);
        return;
    }
    for (int i = l; i < (bl + 1) * B; i++) h[i] += v;   // pinggir kiri, satu per satu
    bangun(bl);
    for (int k = bl + 1; k < br; k++) tag[k] += v;      // blok utuh: cukup catat di tag
    for (int i = br * B; i <= r; i++) h[i] += v;        // pinggir kanan
    bangun(br);
}

long long maks(int l, int r) {
    long long m = LLONG_MIN;
    int bl = l / B, br = r / B;
    if (bl == br) {
        for (int i = l; i <= r; i++) m = max(m, h[i] + tag[bl]);
        return m;
    }
    for (int i = l; i < (bl + 1) * B; i++) m = max(m, h[i] + tag[bl]);
    for (int k = bl + 1; k < br; k++) m = max(m, mx[k] + tag[k]);
    for (int i = br * B; i <= r; i++) m = max(m, h[i] + tag[br]);
    return m;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> n >> q;
    h.resize(n);
    for (auto& x : h) cin >> x;

    B = max(1, (int)sqrt(n));
    int nb = (n + B - 1) / B;
    mx.assign(nb, 0);
    tag.assign(nb, 0);
    for (int k = 0; k < nb; k++) bangun(k);

    string out;
    while (q--) {
        int t, l, r;
        cin >> t >> l >> r;
        l--, r--;   // indeks 0
        if (t == 1) {
            long long v;
            cin >> v;
            tambah(l, r, v);
        } else {
            out += to_string(maks(l, r)) + "\n";
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
// |ketinggian| ≤ 10^9 + 4 · 10^4 · 10^9 ≈ 4 · 10^13: masih tepat untuk Number
const h = Float64Array.from(readInts());   // ketinggian TANPA tag blok
const B = Math.max(1, Math.floor(Math.sqrt(n)));
const nb = Math.ceil(n / B);
const mx = new Float64Array(nb);   // maksimum h di blok k (tanpa tag)
const tag = new Float64Array(nb);  // tambahan untuk seluruh blok k

// Hitung ulang maksimum blok k: O(B)
function bangun(k) {
  const akhir = Math.min(n, (k + 1) * B);
  let m = -Infinity;
  for (let i = k * B; i < akhir; i++) if (h[i] > m) m = h[i];
  mx[k] = m;
}
for (let k = 0; k < nb; k++) bangun(k);

const out = [];
for (let j = 0; j < q; j++) {
  const e = readInts();
  const l = e[1] - 1, r = e[2] - 1;   // indeks 0
  const bl = Math.floor(l / B), br = Math.floor(r / B);
  if (e[0] === 1) {
    const v = e[3];
    if (bl === br) {
      for (let i = l; i <= r; i++) h[i] += v;
      bangun(bl);
      continue;
    }
    for (let i = l; i < (bl + 1) * B; i++) h[i] += v;   // pinggir kiri
    bangun(bl);
    for (let k = bl + 1; k < br; k++) tag[k] += v;      // blok utuh: cukup tag
    for (let i = br * B; i <= r; i++) h[i] += v;        // pinggir kanan
    bangun(br);
  } else {
    let m = -Infinity;
    if (bl === br) {
      for (let i = l; i <= r; i++) m = Math.max(m, h[i] + tag[bl]);
    } else {
      for (let i = l; i < (bl + 1) * B; i++) m = Math.max(m, h[i] + tag[bl]);
      for (let k = bl + 1; k < br; k++) m = Math.max(m, mx[k] + tag[k]);
      for (let i = br * B; i <= r; i++) m = Math.max(m, h[i] + tag[br]);
    }
    out.push(m);
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from math import isqrt
from operator import add


def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    h = list(map(int, data[2:2 + n]))       # ketinggian TANPA tag blok
    B = max(1, isqrt(n))
    nb = (n + B - 1) // B
    mx = [max(h[k * B:(k + 1) * B]) for k in range(nb)]   # maksimum blok, tanpa tag
    tag = [0] * nb                                        # tambahan untuk seluruh blok
    out = []
    p = 2 + n
    for _ in range(q):
        # rentang setengah terbuka [l, r) dengan indeks 0
        l = int(data[p + 1]) - 1
        r = int(data[p + 2])
        bl = l // B
        br = (r - 1) // B
        if data[p] == b"1":
            v = int(data[p + 3])
            p += 4
            # Pinggiran diubah lewat slice + list comprehension (jauh lebih cepat
            # daripada loop biasa di Python), lalu maksimum bloknya dihitung ulang.
            if bl == br:
                h[l:r] = [x + v for x in h[l:r]]
                s = bl * B
                mx[bl] = max(h[s:s + B])
            else:
                e = (bl + 1) * B
                h[l:e] = [x + v for x in h[l:e]]
                mx[bl] = max(h[e - B:e])
                s = br * B
                h[s:r] = [x + v for x in h[s:r]]
                mx[br] = max(h[s:s + B])
                if bl + 1 < br:                   # blok utuh: cukup tambah tag
                    tag[bl + 1:br] = [t + v for t in tag[bl + 1:br]]
        else:
            p += 3
            if bl == br:
                res = max(h[l:r]) + tag[bl]
            else:
                res = max(max(h[l:(bl + 1) * B]) + tag[bl], max(h[br * B:r]) + tag[br])
                if bl + 1 < br:                   # maksimum sebenarnya blok = mx + tag
                    t = max(map(add, mx[bl + 1:br], tag[bl + 1:br]))
                    if t > res:
                        res = t
            out.append(res)
    sys.stdout.write("\n".join(map(str, out)) + "\n")


main()
CODE,
        ],
        'editorial' => '<p>Segment tree dengan lazy propagation bisa menyelesaikan soal ini, tetapi dekomposisi akar jauh lebih pendek. Bagi pos menjadi blok berisi B ≈ √N pos, lalu untuk setiap blok k simpan:</p>
<ul><li><code>tag[k]</code>: tambahan yang berlaku untuk <em>seluruh</em> blok tetapi belum dimasukkan ke elemennya;</li>
<li><code>mx[k]</code>: maksimum <code>h[i]</code> di blok itu, <strong>tanpa</strong> tag.</li></ul>
<p>Ketinggian sebenarnya pos i adalah <code>h[i] + tag[i / B]</code>, dan ketinggian tertinggi sebenarnya di blok k adalah <code>mx[k] + tag[k]</code>, karena menambah v ke semua elemen blok juga menambah maksimumnya sebesar v.</p>
<ul><li><strong>Tambah v ke [l, r].</strong> Untuk blok yang tercakup utuh cukup <code>tag[k] += v</code>. Pada paling banyak dua blok pinggir yang hanya tercakup sebagian, tambahkan v ke elemen yang kena satu per satu, lalu <strong>hitung ulang</strong> <code>mx</code> blok itu dari semua elemennya dalam O(B).</li>
<li><strong>Maksimum [l, r].</strong> Elemen pinggir dibaca sebagai <code>h[i] + tag[i / B]</code>, blok utuh sebagai <code>mx[k] + tag[k]</code>.</li></ul>
<p>Setiap kejadian menyentuh O(B) elemen dan O(N / B) blok, jadi O(√N) per kejadian dan O(N + Q√N) seluruhnya, paling banyak sekitar 4 · 10<sup>4</sup> · 600 ≈ 2,4 · 10<sup>7</sup> langkah. Mengubah dan memeriksa setiap pos satu per satu butuh O(N · Q) = 1,6 · 10<sup>9</sup> langkah pada kasus terburuk.</p>
<p><strong>Jebakan:</strong></p>
<ul><li>Setelah pinggiran diubah, jangan hanya menulis <code>mx = max(mx, nilai baru)</code>. Jika v negatif, maksimum lama bisa ikut turun, jadi maksimum blok harus dihitung ulang dari seluruh elemennya.</li>
<li>Ketinggian bisa mencapai 10<sup>9</sup> + 4 · 10<sup>4</sup> · 10<sup>9</sup> ≈ 4 · 10<sup>13</sup>: pakai <code>long long</code>.</li>
<li>Nilai awal pencarian maksimum harus sangat kecil (bukan 0), karena jawabannya bisa negatif seperti pada contoh.</li></ul>',
        'hints' => [
            'Bagi pos menjadi kelompok berisi sekitar √N pos. Apa yang perlu disimpan setiap kelompok agar maksimumnya bisa dijawab tanpa memeriksa semua isinya?',
            'Untuk blok yang seluruhnya kena perubahan, cukup catat tag[k] += v; maksimum sebenarnya blok itu adalah mx[k] + tag[k]. Elemen di pinggir diubah satu per satu.',
            'Setelah mengubah elemen pinggir sebuah blok, hitung ulang mx blok itu dari semua elemennya, karena v bisa negatif. Gunakan long long dan nilai awal maksimum yang sangat kecil.',
        ],
    ],

    [
        'slug' => 'truk-bank-sampah',
        'lesson' => 'sqrt',
        'title' => 'Rute Truk Bank Sampah',
        'difficulty' => 'Sedang',
        'tags' => ['dekomposisi akar', 'berat-ringan', 'ambang akar'],
        'statement' => '<p>Sebuah kompleks perumahan punya <strong>N</strong> rumah berjajar bernomor 1 sampai N. Rumah i saat ini menyimpan a<sub>i</sub> gram sampah plastik yang siap disetor ke bank sampah.</p>
<p>Truk bank sampah punya kebiasaan unik. Dalam satu putaran, truk memilih dua bilangan k dan r, lalu hanya berhenti di rumah yang nomornya bersisa r jika dibagi k, yaitu semua rumah i (1 ≤ i ≤ N) dengan <code>i mod k = r</code>. Misalnya k = 3 dan r = 1 berarti rumah 1, 4, 7, …, sedangkan k = 3 dan r = 0 berarti rumah 3, 6, 9, ….</p>
<p>Pengurus bank sampah mencatat <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 i x</code>: sampah di rumah i ditimbang ulang dan ternyata sekarang x gram.</li>
<li><code>2 k r</code>: jika truk berangkat sekarang dengan pilihan k dan r, berapa gram sampah yang akan terkumpul? Ini hanya perkiraan; sampah di rumah-rumah tidak berubah.</li></ul>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> a<sub>2</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak total sampah yang akan terkumpul pada satu baris, sesuai urutan kejadian.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>1 ≤ Q ≤ 100 000</li><li>0 ≤ a<sub>i</sub>, x ≤ 10<sup>9</sup></li><li>1 ≤ i ≤ N</li><li>1 ≤ k ≤ N dan 0 ≤ r &lt; k</li><li>Ada setidaknya satu kejadian jenis 2.</li></ul>',
        'samples' => [
            ['input' => "7 7\n4 1 3 5 2 6 8\n2 2 0\n2 3 1\n1 4 10\n2 3 1\n2 5 2\n2 1 0\n2 7 0\n", 'explanation' => 'k = 2, r = 0: rumah 2, 4, 6, total 1 + 5 + 6 = 12. k = 3, r = 1: rumah 1, 4, 7, total 4 + 5 + 8 = 17. Rumah 4 lalu ditimbang ulang menjadi 10, sehingga putaran yang sama sekarang mengumpulkan 4 + 10 + 8 = 22. k = 5, r = 2: rumah 2 dan 7, total 9. k = 1, r = 0: semua rumah, total 34. k = 7, r = 0: hanya rumah 7, yaitu 8.'],
        ],
        'tests' => function () {
            // $pUbah: peluang (persen) sebuah kejadian berjenis 1; k diambil acak dari [$kMin, $kMaks]
            $mk = function (int $n, int $q, int $aMin, int $aMaks, int $pUbah, int $kMin, int $kMaks): string {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand($aMin, $aMaks);
                }
                $out = ["$n $q", implode(' ', $a)];
                for ($j = 0; $j < $q; $j++) {
                    if ($j < $q - 1 && mt_rand(1, 100) <= $pUbah) {
                        $out[] = '1 '.mt_rand(1, $n).' '.mt_rand($aMin, $aMaks);
                    } else {
                        $k = mt_rand(max(1, $kMin), min($n, $kMaks));
                        $out[] = "2 $k ".mt_rand(0, $k - 1);
                    }
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n7\n2 1 0\n",
                "4 6\n1 2 3 4\n2 2 0\n2 2 1\n1 2 10\n2 2 0\n2 4 0\n2 3 2\n",
                $mk(10, 40, 0, 20, 40, 1, 10),
                $mk(1000, 3000, 0, 1000000000, 50, 1, 1000),
                $mk(100000, 100000, 0, 100000, 50, 1, 100000),
                $mk(100000, 100000, 0, 1000, 50, 1, 20),
                $mk(100000, 100000, 0, 1000, 50, 150, 800),
                $mk(100000, 100000, 0, 100000, 90, 1, 400),
                $mk(100000, 40000, 900000000, 1000000000, 60, 1, 3),
                $mk(99991, 100000, 0, 10000, 50, 300, 340),
                $mk(2, 500, 0, 10, 50, 1, 2),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $a = array_merge([0], T::ints($lines[1]));
            $S = max(1, (int) sqrt($n));
            $pre = [[]];
            for ($k = 1; $k <= $S; $k++) {
                $baris = array_fill(0, $k, 0);
                for ($i = 1; $i <= $n; $i++) {
                    $baris[$i % $k] += $a[$i];
                }
                $pre[] = $baris;
            }
            $out = [];
            for ($j = 2; $j < $q + 2; $j++) {
                [$t, $x, $y] = T::ints($lines[$j]);
                if ($t === 1) {
                    $d = $y - $a[$x];
                    $a[$x] = $y;
                    for ($k = 1; $k <= $S; $k++) {
                        $pre[$k][$x % $k] += $d;
                    }
                } elseif ($x <= $S) {
                    $out[] = $pre[$x][$y];
                } else {
                    $s = 0;
                    for ($i = $y; $i <= $n; $i += $x) {
                        $s += $a[$i];
                    }
                    $out[] = $s;
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<long long> a(n + 1, 0);\n    for (int i = 1; i <= n; i++) cin >> a[i];\n    vector<array<long long, 3>> kejadian(q);   // {1, i, x} atau {2, k, r}\n    for (auto& k : kejadian) cin >> k[0] >> k[1] >> k[2];", "const [n, q] = readInts();\nconst a = [0, ...readInts()];\nconst kejadian = [];\nfor (let i = 0; i < q; i++) kejadian.push(readInts()); // [1, i, x] atau [2, k, r]", "n, q = map(int, input().split())\na = [0] + list(map(int, input().split()))\nkejadian = [list(map(int, input().split())) for _ in range(q)]  # [1, i, x] atau [2, k, r]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    // a[0] = 0 sebagai penjaga: untuk r = 0 kita bisa mulai dari indeks 0 tanpa mengubah jumlah
    vector<long long> a(n + 1, 0);
    for (int i = 1; i <= n; i++) cin >> a[i];

    // k kecil (≤ S): pre[k][r] = total a[i] untuk 1 ≤ i ≤ N dengan i mod k = r
    int S = max(1, (int)sqrt(n));
    vector<vector<long long>> pre(S + 1);
    for (int k = 1; k <= S; k++) {
        pre[k].assign(k, 0);
        for (int i = 1; i <= n; i++) pre[k][i % k] += a[i];   // persiapan O(N · S)
    }

    string out;
    for (int j = 0; j < q; j++) {
        int t, x;
        long long y;
        cin >> t >> x >> y;
        if (t == 1) {
            // rumah x hanya masuk ke satu sel per k, yaitu pre[k][x mod k]: O(S)
            long long d = y - a[x];
            a[x] = y;
            for (int k = 1; k <= S; k++) pre[k][x % k] += d;
        } else {
            int k = x, r = (int)y;
            long long total = 0;
            if (k <= S) {
                total = pre[k][r];                              // O(1)
            } else {
                for (int i = r; i <= n; i += k) total += a[i];  // paling banyak N / k < √N suku
            }
            out += to_string(total) + "\n";
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
// a[0] = 0 sebagai penjaga untuk r = 0. Total ≤ 10^14: masih tepat untuk Number.
const a = Float64Array.from([0, ...readInts()]);

// k kecil (≤ S): pre[k][r] = total a[i] dengan i mod k = r
const S = Math.max(1, Math.floor(Math.sqrt(n)));
const pre = [null];
for (let k = 1; k <= S; k++) {
  const baris = new Float64Array(k);
  for (let i = 1; i <= n; i++) baris[i % k] += a[i];
  pre.push(baris);
}

const out = [];
for (let j = 0; j < q; j++) {
  const [t, x, y] = readInts();
  if (t === 1) {
    const d = y - a[x];
    a[x] = y;
    for (let k = 1; k <= S; k++) pre[k][x % k] += d;   // O(S) per perubahan
  } else if (x <= S) {
    out.push(pre[x][y]);                               // O(1)
  } else {
    let total = 0;
    for (let i = y; i <= n; i += x) total += a[i];     // < √N suku
    out.push(total);
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from math import isqrt


def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    a = [0] + list(map(int, data[2:2 + n]))   # a[0] = 0 sebagai penjaga untuk r = 0

    # Di Python, memperbarui tabel memakai loop Python (lambat), sedangkan sum(a[r::k])
    # berjalan di C (cepat). Karena itu ambang S dibuat lebih kecil dari √N agar
    # kedua cara tetap seimbang.
    S = max(1, isqrt(n) // 4)
    # pre[k][r] = total a[i] dengan i mod k = r, untuk 1 ≤ k ≤ S
    pre = [[sum(a[r::k]) for r in range(k)] for k in range(S + 1)]
    baris = pre[1:]
    ks = range(1, S + 1)

    out = []
    p = 2 + n
    for _ in range(q):
        x = int(data[p + 1])
        y = int(data[p + 2])
        if data[p] == b"1":
            d = y - a[x]
            a[x] = y
            for row, k in zip(baris, ks):   # rumah x masuk ke sel x mod k untuk setiap k ≤ S
                row[x % k] += d
        elif x <= S:
            out.append(pre[x][y])
        else:
            out.append(sum(a[y::x]))         # rumah y, y + k, y + 2k, ...
        p += 3
    sys.stdout.write("\n".join(map(str, out)) + "\n")


main()
CODE,
        ],
        'editorial' => '<p>Ada dua cara menjawab kueri (k, r):</p>
<ul><li><strong>Langsung</strong> menjumlahkan rumah r, r + k, r + 2k, …. Banyak sukunya sekitar N / k, jadi cepat jika k <em>besar</em>, tetapi O(N) jika k = 1 atau 2.</li>
<li><strong>Tabel</strong> <code>pre[k][r]</code> = total semua rumah dengan nomor ≡ r (mod k). Menjawab cukup O(1), tetapi tabel untuk semua k terlalu besar dan setiap penimbangan ulang harus memperbarui satu sel untuk <em>setiap</em> k.</li></ul>
<p>Gabungkan keduanya dengan ambang S ≈ √N (trik berat–ringan):</p>
<ul><li><strong>k ≤ S:</strong> simpan tabel. Ukurannya 1 + 2 + … + S ≈ N / 2 angka dan bisa disiapkan dalam O(N · S). Jika rumah i berubah dari a<sub>i</sub> menjadi x, rumah itu masuk ke tepat satu sel untuk setiap k, yaitu <code>pre[k][i mod k]</code>. Tambahkan selisih x − a<sub>i</sub> ke S sel tersebut: O(√N) per perubahan.</li>
<li><strong>k &gt; S:</strong> sukunya paling banyak N / k &lt; N / S ≈ √N, jadi hitung langsung.</li></ul>
<p>Setiap kejadian O(√N), total O((N + Q)√N). Tanpa ambang, kueri k = 1 berulang kali (O(N) per kueri) atau tabel yang dibangun ulang setiap perubahan sama-sama terlalu lambat.</p>
<p><strong>Detail:</strong> rumah bernomor 1 sampai N, jadi untuk r = 0 rumah pertama yang dikunjungi adalah rumah k. Trik praktis: simpan <code>a[0] = 0</code>, lalu jumlahkan mulai dari indeks r; untuk r = 0, a[0] tidak menambah apa pun. Total bisa mencapai 10<sup>5</sup> · 10<sup>9</sup> = 10<sup>14</sup>, jadi pakai <code>long long</code>.</p>
<p>Ambang tidak harus tepat √N; yang penting kedua biaya seimbang. Di Python, memperbarui S sel memakai loop Python yang lambat, sedangkan <code>sum(a[r::k])</code> berjalan di C, sehingga solusi Python memakai ambang sekitar √N / 4.</p>',
        'hints' => [
            'Jika k besar, rumah yang dikunjungi hanya sedikit: sekitar N / k. Kapan menjumlahkan langsung menjadi lambat?',
            'Untuk k kecil (misalnya k ≤ √N), siapkan tabel total per sisa bagi. Tabel untuk semua k ≤ √N hanya berisi sekitar N / 2 angka.',
            'Saat rumah i berubah, untuk setiap k ≤ √N hanya sel pre[k][i mod k] yang terpengaruh: tambahkan selisihnya, O(√N). Kueri dengan k > √N dihitung langsung, juga O(√N).',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'laci-kaus-kaki',
        'lesson' => 'sqrt',
        'title' => 'Laci Kaus Kaki',
        'difficulty' => 'Sedang',
        'tags' => ['algoritma mo', 'offline', 'kompresi nilai'],
        'statement' => '<p>Bu Rina menyimpan <strong>N</strong> kaus kaki dalam satu laci panjang, berjajar dari nomor 1 sampai N. Kaus kaki nomor i bermotif c<sub>i</sub>; motif dinyatakan dengan sebuah kode bilangan bulat.</p>
<p>Setiap pagi anak-anaknya hanya boleh mengambil kaus kaki dari bagian laci tertentu. Bu Rina punya <strong>Q</strong> pertanyaan: jika hanya kaus kaki nomor l sampai r yang boleh diambil, ada berapa cara memilih <em>dua</em> kaus kaki bermotif sama? Dengan kata lain, berapa banyak pasangan (i, j) dengan l ≤ i &lt; j ≤ r dan c<sub>i</sub> = c<sub>j</sub>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>c<sub>1</sub> c<sub>2</sub> … c<sub>N</sub></code>. Q baris berikutnya masing-masing berisi <code>l r</code>.</p>',
        'output_format' => '<p>Q baris. Baris ke-j berisi jawaban pertanyaan ke-j.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 50 000</li><li>1 ≤ Q ≤ 50 000</li><li>1 ≤ c<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ l ≤ r ≤ N</li></ul>',
        'samples' => [
            ['input' => "8 4\n3 1 3 3 2 1 2 3\n1 8\n2 6\n5 7\n3 3\n", 'explanation' => 'Seluruh laci: motif 3 ada di kaus kaki 1, 3, 4, 8 sehingga ada 6 pasangan, motif 1 di kaus kaki 2 dan 6 (1 pasangan), motif 2 di kaus kaki 5 dan 7 (1 pasangan); total 8. Kaus kaki 2–6 bermotif 1 3 3 2 1: satu pasangan motif 1 dan satu pasangan motif 3, total 2. Kaus kaki 5–7 bermotif 2 1 2: satu pasangan. Satu kaus kaki saja tidak membentuk pasangan.'],
            ['input' => "5 2\n1000000000 7 1000000000 7 1000000000\n1 5\n2 4\n", 'explanation' => 'Kode motif bisa besar. Pada seluruh laci, motif 1000000000 muncul 3 kali (3 pasangan) dan motif 7 muncul 2 kali (1 pasangan), total 4. Kaus kaki 2–4 hanya punya satu pasangan, yaitu kaus kaki 2 dan 4.'],
        ],
        'tests' => function () use ($rentang) {
            // $d motif berbeda yang kodenya diambil acak dari 1..10^9
            $mk = function (int $n, int $q, int $d, int $mode) use ($rentang): string {
                $motif = [];
                while (count($motif) < $d) {
                    $motif[mt_rand(1, 1000000000)] = true;
                }
                $motif = array_keys($motif);
                $c = [];
                for ($i = 0; $i < $n; $i++) {
                    $c[] = $motif[mt_rand(0, $d - 1)];
                }
                $out = ["$n $q", implode(' ', $c)];
                for ($j = 0; $j < $q; $j++) {
                    $out[] = implode(' ', $rentang($n, $mode));
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n5\n1 1\n",
                "4 4\n9 9 9 9\n1 4\n2 3\n1 1\n2 4\n",
                $mk(10, 30, 3, 0),
                $mk(200, 500, 10, 0),
                $mk(3000, 3000, 100, 0),
                $mk(50000, 50000, 1000, 0),
                $mk(50000, 20000, 1, 0),
                $mk(50000, 50000, 5000, -1),
                $mk(50000, 50000, 30000, 0),
                $mk(50000, 50000, 50, 100),
                $mk(1000, 50000, 5, 0),
                $mk(50000, 1, 7, -1),
            ];
        },
        'solve' => function (string $input) use ($bacaKueri, $urutMo) {
            [$n, $q, $c, $L, $R] = $bacaKueri($input);
            $nomor = [];
            $a = [];
            foreach ($c as $x) {
                $a[] = $nomor[$x] ??= count($nomor);
            }
            $cnt = array_fill(0, count($nomor), 0);
            $jawab = array_fill(0, $q, 0);
            $kiri = 0;
            $kanan = -1;
            $s = 0;
            foreach ($urutMo($L, $R, $n) as $j) {
                $l = $L[$j];
                $r = $R[$j];
                while ($kanan < $r) {
                    $s += $cnt[$a[++$kanan]]++;
                }
                while ($kiri > $l) {
                    $s += $cnt[$a[--$kiri]]++;
                }
                while ($kanan > $r) {
                    $s -= --$cnt[$a[$kanan--]];
                }
                while ($kiri < $l) {
                    $s -= --$cnt[$a[$kiri++]];
                }
                $jawab[$j] = $s;
            }

            return implode("\n", $jawab);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<int> c(n);\n    for (auto& x : c) cin >> x;\n    vector<int> L(q), R(q);\n    for (int j = 0; j < q; j++) cin >> L[j] >> R[j];", "const [n, q] = readInts();\nconst c = readInts();\nconst L = [], R = [];\nfor (let j = 0; j < q; j++) {\n  const [l, r] = readInts();\n  L.push(l);\n  R.push(r);\n}", "n, q = map(int, input().split())\nc = list(map(int, input().split()))\nkueri = [tuple(map(int, input().split())) for _ in range(q)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<int> c(n);
    for (auto& x : c) cin >> x;

    // Kompres kode motif (sampai 10^9) menjadi 0..D-1 agar cnt cukup berupa array
    vector<int> kode(c);
    sort(kode.begin(), kode.end());
    kode.erase(unique(kode.begin(), kode.end()), kode.end());
    for (auto& x : c) x = lower_bound(kode.begin(), kode.end(), x) - kode.begin();

    vector<int> L(q), R(q), urut(q);
    for (int j = 0; j < q; j++) {
        cin >> L[j] >> R[j];
        L[j]--, R[j]--;   // indeks 0
        urut[j] = j;
    }

    // Urutan Mo: menurut blok l, lalu r naik (blok genap) atau turun (blok ganjil)
    int B = max(1, (int)(n / sqrt((double)q)));
    sort(urut.begin(), urut.end(), [&](int x, int y) {
        int bx = L[x] / B, by = L[y] / B;
        if (bx != by) return bx < by;
        return (bx & 1) ? R[x] > R[y] : R[x] < R[y];
    });

    vector<int> cnt(kode.size(), 0);   // cnt[v] = banyak kaus kaki bermotif v di jendela
    vector<long long> jawab(q);
    long long pasangan = 0;            // bisa sampai ~1,25 · 10^9
    int kiri = 0, kanan = -1;          // jendela [kiri, kanan], awalnya kosong

    for (int j : urut) {
        // Perluas dulu, baru persempit.
        // Kaus kaki bermotif v yang masuk berpasangan dengan cnt[v] kaus kaki yang sudah ada.
        while (kanan < R[j]) { int v = c[++kanan]; pasangan += cnt[v]; cnt[v]++; }
        while (kiri > L[j])  { int v = c[--kiri];  pasangan += cnt[v]; cnt[v]++; }
        while (kanan > R[j]) { int v = c[kanan--]; cnt[v]--; pasangan -= cnt[v]; }
        while (kiri < L[j])  { int v = c[kiri++];  cnt[v]--; pasangan -= cnt[v]; }
        jawab[j] = pasangan;
    }

    string out;
    for (int j = 0; j < q; j++) out += to_string(jawab[j]) + "\n";
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const c = readInts();

// Kompres kode motif menjadi 0..D-1
const nomor = new Map();
const a = new Int32Array(n);
for (let i = 0; i < n; i++) {
  let v = nomor.get(c[i]);
  if (v === undefined) {
    v = nomor.size;
    nomor.set(c[i], v);
  }
  a[i] = v;
}

const L = new Int32Array(q), R = new Int32Array(q);
for (let j = 0; j < q; j++) {
  const [l, r] = readInts();
  L[j] = l - 1;
  R[j] = r - 1;
}

// Urutan Mo dengan trik ganjil-genap
const B = Math.max(1, Math.floor(n / Math.sqrt(q)));
const urut = Array.from({ length: q }, (_, j) => j);
urut.sort((x, y) => {
  const bx = Math.floor(L[x] / B), by = Math.floor(L[y] / B);
  if (bx !== by) return bx - by;
  return bx & 1 ? R[y] - R[x] : R[x] - R[y];
});

const cnt = new Int32Array(nomor.size);
const jawab = new Array(q);
let pasangan = 0, kiri = 0, kanan = -1;
for (const j of urut) {
  // perluas dulu, baru persempit
  while (kanan < R[j]) pasangan += cnt[a[++kanan]]++;   // berpasangan dengan cnt[v] yang sudah ada
  while (kiri > L[j]) pasangan += cnt[a[--kiri]]++;
  while (kanan > R[j]) pasangan -= --cnt[a[kanan--]];
  while (kiri < L[j]) pasangan -= --cnt[a[kiri++]];
  jawab[j] = pasangan;
}
console.log(jawab.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])

    # Kompres kode motif menjadi 0..D-1 (list jauh lebih cepat daripada dict di loop dalam)
    nomor = {}
    a = []
    for x in data[2:2 + n]:
        v = nomor.get(x)
        if v is None:
            v = nomor[x] = len(nomor)
        a.append(v)
    L = [int(x) - 1 for x in data[2 + n::2]]   # jendela setengah terbuka [l, r)
    R = [int(x) for x in data[3 + n::2]]

    # Urutan Mo dengan trik ganjil-genap, memakai satu kunci bilangan agar sort cepat
    B = max(1, int(n / q ** 0.5))
    kunci = [0] * q
    for j in range(q):
        b = L[j] // B
        kunci[j] = (b << 20) | (R[j] if b % 2 == 0 else (1 << 20) - R[j])
    urut = sorted(range(q), key=kunci.__getitem__)

    cnt = [0] * len(nomor)
    jawab = [0] * q
    kiri = kanan = 0
    s = 0
    for j in urut:
        l = L[j]
        r = R[j]
        # perluas dulu, baru persempit; "for v in a[x:y]" lebih cepat daripada indeks satu per satu
        if kanan < r:
            for v in a[kanan:r]:
                s += cnt[v]
                cnt[v] += 1
            kanan = r
        if kiri > l:
            for v in a[l:kiri]:
                s += cnt[v]
                cnt[v] += 1
            kiri = l
        if kanan > r:
            for v in a[r:kanan]:
                cnt[v] -= 1
                s -= cnt[v]
            kanan = r
        if kiri < l:
            for v in a[kiri:l]:
                cnt[v] -= 1
                s -= cnt[v]
            kiri = l
        jawab[j] = s
    sys.stdout.write("\n".join(map(str, jawab)) + "\n")


main()
CODE,
        ],
        'editorial' => '<p>Struktur seperti segment tree sulit dipakai: banyak pasangan di dua bagian tidak bisa digabung tanpa mengetahui seluruh isi motif kedua bagian. Namun jika jawaban untuk jendela [L, R] sudah diketahui beserta <code>cnt[v]</code> (banyak kaus kaki bermotif v di jendela), menggeser jendela satu langkah sangat murah:</p>
<ul><li><strong>Menambah</strong> kaus kaki bermotif v: ia berpasangan dengan <code>cnt[v]</code> kaus kaki bermotif sama yang sudah ada, jadi jawaban bertambah <code>cnt[v]</code>, lalu <code>cnt[v]++</code>.</li>
<li><strong>Menghapus</strong> kaus kaki bermotif v: <code>cnt[v]--</code>, lalu jawaban berkurang <code>cnt[v]</code>.</li></ul>
<p>Keduanya O(1), dan semua pertanyaan diketahui di awal, jadi pakai <strong>algoritma Mo</strong>:</p>
<ol><li>Kompres kode motif menjadi 0..D−1 agar <code>cnt</code> bisa berupa array, bukan map.</li>
<li>Urutkan pertanyaan menurut blok l (ukuran blok sekitar N / √Q), lalu menurut r: naik pada blok bernomor genap dan turun pada blok bernomor ganjil.</li>
<li>Geser penunjuk kiri dan kanan ke setiap pertanyaan; perluas jendela dulu, baru persempit. Simpan jawaban sesuai nomor asli pertanyaan.</li></ol>
<p>Total geseran O(N√Q + Q · N/√Q) = O(N√Q), sekitar 2 · 10<sup>7</sup> langkah untuk batas soal ini. Menghitung setiap pertanyaan dari nol butuh O(N · Q) = 2,5 · 10<sup>9</sup> langkah.</p>
<p><strong>Jebakan:</strong> jawaban bisa mencapai C(50 000, 2) ≈ 1,25 · 10<sup>9</sup>, tidak jauh dari batas <code>int</code> (sekitar 2,1 · 10<sup>9</sup>); pakai <code>long long</code> agar aman. Jangan lupa mengembalikan jawaban sesuai urutan input, bukan urutan pemrosesan.</p>',
        'hints' => [
            'Jika kamu sudah tahu jawaban untuk kaus kaki L..R beserta banyak kaus kaki setiap motif, berapa pasangan baru yang muncul ketika kaus kaki R + 1 ikut masuk?',
            'Menambah kaus kaki bermotif v menambah cnt[v] pasangan; menghapusnya mengurangi cnt[v] − 1 pasangan. Karena semua pertanyaan diketahui di awal, urutkan agar jendela bergeser sesedikit mungkin.',
            'Algoritma Mo: kompres motif, urutkan pertanyaan menurut blok l lalu r (trik ganjil-genap), geser kedua penunjuk, dan simpan jawaban dalam long long sesuai nomor asli pertanyaan.',
        ],
    ],

    [
        'slug' => 'kartu-angka-jujur',
        'lesson' => 'sqrt',
        'title' => 'Kartu Angka Jujur',
        'difficulty' => 'Sulit',
        'tags' => ['algoritma mo', 'offline', 'frekuensi'],
        'statement' => '<p>Dimas menjajarkan <strong>N</strong> kartu dari kiri ke kanan. Kartu ke-i bertuliskan bilangan bulat positif a<sub>i</sub>.</p>
<p>Dalam sekumpulan kartu, sebuah bilangan x disebut <strong>jujur</strong> jika x tertulis pada <em>tepat</em> x kartu. Misalnya pada kartu bertuliskan 2 3 2 1 3, bilangan 1 dan 2 jujur (1 muncul sekali, 2 muncul dua kali), sedangkan 3 tidak (3 hanya muncul dua kali).</p>
<p>Dimas punya <strong>Q</strong> pertanyaan. Pertanyaan ke-j berbunyi: jika hanya kartu ke-l sampai ke-r yang diperhatikan, ada berapa bilangan jujur yang berbeda?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>a<sub>1</sub> a<sub>2</sub> … a<sub>N</sub></code>. Q baris berikutnya masing-masing berisi <code>l r</code>.</p>',
        'output_format' => '<p>Q baris. Baris ke-j berisi banyak bilangan jujur yang berbeda di antara kartu ke-l sampai ke-r pada pertanyaan ke-j.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 50 000</li><li>1 ≤ Q ≤ 50 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ l ≤ r ≤ N</li></ul>',
        'samples' => [
            ['input' => "9 5\n1 2 2 3 3 3 2 1 1000000000\n1 9\n1 3\n2 7\n4 8\n9 9\n", 'explanation' => 'Semua kartu: 1 muncul 2 kali, 2 muncul 3 kali, 3 muncul 3 kali, 1000000000 muncul sekali; hanya 3 yang jujur. Kartu 1–3 (1 2 2): 1 dan 2 jujur. Kartu 2–7 (2 2 3 3 3 2): 2 muncul 3 kali sehingga tidak jujur, hanya 3 yang jujur. Kartu 4–8 (3 3 3 2 1): 3 dan 1 jujur, 2 tidak. Kartu 9 saja: 1000000000 hanya muncul sekali, jadi tidak ada bilangan jujur.'],
        ],
        'tests' => function () use ($rentang) {
            // Kartu dibangun dari kelompok: bilangan x ditulis (sering tepat) x kali berdekatan,
            // lalu diacak dalam jendela kecil berukuran $acak (−1 = acak seluruhnya).
            $mk = function (int $n, int $q, int $maksX, int $acak, int $mode) use ($rentang): string {
                $a = [];
                while (count($a) < $n) {
                    $p = mt_rand(1, 100);
                    if ($p <= 5) {
                        $a[] = mt_rand(max(1, $n + 1), 1000000000);   // terlalu besar untuk jujur
                        continue;
                    }
                    $x = mt_rand(1, $maksX);
                    $kali = $p <= 60 ? $x : mt_rand(1, 2 * $x);
                    for ($i = 0; $i < $kali; $i++) {
                        $a[] = $x;
                    }
                }
                $a = array_slice($a, 0, $n);
                if ($acak < 0) {
                    T::shuffle($a);
                } elseif ($acak > 0) {
                    for ($i = 0; $i < $n; $i++) {
                        $j = min($n - 1, $i + mt_rand(0, $acak));
                        [$a[$i], $a[$j]] = [$a[$j], $a[$i]];
                    }
                }
                $out = ["$n $q", implode(' ', $a)];
                for ($j = 0; $j < $q; $j++) {
                    $out[] = implode(' ', $rentang($n, $mode));
                }

                return implode("\n", $out)."\n";
            };
            // Tangga: 1, 2 2, 3 3 3, ... sehingga rentang panjang bisa punya ratusan bilangan jujur
            $tangga = function (int $n, int $q) use ($rentang): string {
                $a = [];
                for ($x = 1; count($a) < $n; $x++) {
                    for ($i = 0; $i < $x && count($a) < $n; $i++) {
                        $a[] = $x;
                    }
                }
                $out = ["$n $q", implode(' ', $a)];
                for ($j = 0; $j < $q; $j++) {
                    $out[] = implode(' ', $rentang($n, 0));
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n1\n1 1\n",
                "1 1\n2\n1 1\n",
                "5 3\n2 2 2 1 1000000000\n1 2\n1 3\n3 5\n",
                $mk(12, 40, 4, 3, 0),
                $mk(200, 500, 8, 5, 0),
                $mk(3000, 3000, 20, 10, 0),
                $mk(50000, 50000, 150, 20, 0),
                $mk(50000, 50000, 10, 5, 300),
                $mk(50000, 50000, 300, -1, -1),
                $mk(50000, 50000, 3, 2, 30),
                $mk(50000, 50000, 500, 50, 0),
                $tangga(50000, 50000),
            ];
        },
        'solve' => function (string $input) use ($bacaKueri, $urutMo) {
            [$n, $q, $a, $L, $R] = $bacaKueri($input);
            foreach ($a as $i => $x) {
                if ($x > $n) {
                    $a[$i] = $n + 1;
                }
            }
            $cnt = array_fill(0, $n + 2, 0);
            $jawab = array_fill(0, $q, 0);
            $kiri = 0;
            $kanan = -1;
            $s = 0;
            foreach ($urutMo($L, $R, $n) as $j) {
                $l = $L[$j];
                $r = $R[$j];
                while ($kanan < $r) {
                    $v = $a[++$kanan];
                    $c = $cnt[$v]++;
                    if ($c === $v) {
                        $s--;
                    } elseif ($c + 1 === $v) {
                        $s++;
                    }
                }
                while ($kiri > $l) {
                    $v = $a[--$kiri];
                    $c = $cnt[$v]++;
                    if ($c === $v) {
                        $s--;
                    } elseif ($c + 1 === $v) {
                        $s++;
                    }
                }
                while ($kanan > $r) {
                    $v = $a[$kanan--];
                    $c = $cnt[$v]--;
                    if ($c === $v) {
                        $s--;
                    } elseif ($c - 1 === $v) {
                        $s++;
                    }
                }
                while ($kiri < $l) {
                    $v = $a[$kiri++];
                    $c = $cnt[$v]--;
                    if ($c === $v) {
                        $s--;
                    } elseif ($c - 1 === $v) {
                        $s++;
                    }
                }
                $jawab[$j] = $s;
            }

            return implode("\n", $jawab);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<int> a(n);\n    for (auto& x : a) cin >> x;\n    vector<int> L(q), R(q);\n    for (int j = 0; j < q; j++) cin >> L[j] >> R[j];", "const [n, q] = readInts();\nconst a = readInts();\nconst L = [], R = [];\nfor (let j = 0; j < q; j++) {\n  const [l, r] = readInts();\n  L.push(l);\n  R.push(r);\n}", "n, q = map(int, input().split())\na = list(map(int, input().split()))\nkueri = [tuple(map(int, input().split())) for _ in range(q)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    vector<int> a(n);
    for (auto& x : a) {
        cin >> x;
        // Jendela berisi paling banyak N kartu, jadi bilangan > N tidak mungkin jujur.
        // Kumpulkan semuanya di N + 1: cnt[N + 1] tidak pernah mencapai N + 1.
        if (x > n) x = n + 1;
    }
    vector<int> L(q), R(q), urut(q);
    for (int j = 0; j < q; j++) {
        cin >> L[j] >> R[j];
        L[j]--, R[j]--;   // indeks 0
        urut[j] = j;
    }

    // Urutan Mo: menurut blok l, lalu r naik (blok genap) atau turun (blok ganjil)
    int B = max(1, (int)(n / sqrt((double)q)));
    sort(urut.begin(), urut.end(), [&](int x, int y) {
        int bx = L[x] / B, by = L[y] / B;
        if (bx != by) return bx < by;
        return (bx & 1) ? R[x] > R[y] : R[x] < R[y];
    });

    vector<int> cnt(n + 2, 0);
    int jujur = 0;   // banyak v dengan cnt[v] == v

    // Status "jujur" hanya bisa berubah untuk bilangan yang kartunya masuk/keluar:
    // cek sebelum dan sesudah cnt berubah.
    auto tambah = [&](int v) {
        if (cnt[v] == v) jujur--;
        cnt[v]++;
        if (cnt[v] == v) jujur++;
    };
    auto hapus = [&](int v) {
        if (cnt[v] == v) jujur--;
        cnt[v]--;
        if (cnt[v] == v) jujur++;
    };

    vector<int> jawab(q);
    int kiri = 0, kanan = -1;   // jendela [kiri, kanan], awalnya kosong
    for (int j : urut) {
        while (kanan < R[j]) tambah(a[++kanan]);   // perluas dulu
        while (kiri > L[j]) tambah(a[--kiri]);
        while (kanan > R[j]) hapus(a[kanan--]);    // baru persempit
        while (kiri < L[j]) hapus(a[kiri++]);
        jawab[j] = jujur;
    }

    string out;
    for (int j = 0; j < q; j++) out += to_string(jawab[j]) + "\n";
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
// Bilangan > N tidak mungkin jujur: kumpulkan di N + 1 (cnt-nya tidak pernah mencapai N + 1)
const a = Int32Array.from(readInts(), (x) => (x > n ? n + 1 : x));
const L = new Int32Array(q), R = new Int32Array(q);
for (let j = 0; j < q; j++) {
  const [l, r] = readInts();
  L[j] = l - 1;
  R[j] = r - 1;
}

// Urutan Mo dengan trik ganjil-genap
const B = Math.max(1, Math.floor(n / Math.sqrt(q)));
const urut = Array.from({ length: q }, (_, j) => j);
urut.sort((x, y) => {
  const bx = Math.floor(L[x] / B), by = Math.floor(L[y] / B);
  if (bx !== by) return bx - by;
  return bx & 1 ? R[y] - R[x] : R[x] - R[y];
});

const cnt = new Int32Array(n + 2);
let jujur = 0;   // banyak v dengan cnt[v] === v
function tambah(v) {
  if (cnt[v] === v) jujur--;   // tadinya jujur, sebentar lagi kelebihan
  cnt[v]++;
  if (cnt[v] === v) jujur++;
}
function hapus(v) {
  if (cnt[v] === v) jujur--;
  cnt[v]--;
  if (cnt[v] === v) jujur++;
}

const jawab = new Int32Array(q);
let kiri = 0, kanan = -1;
for (const j of urut) {
  while (kanan < R[j]) tambah(a[++kanan]);   // perluas dulu, baru persempit
  while (kiri > L[j]) tambah(a[--kiri]);
  while (kanan > R[j]) hapus(a[kanan--]);
  while (kiri < L[j]) hapus(a[kiri++]);
  jawab[j] = jujur;
}
console.log(jawab.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys


def main():
    data = sys.stdin.buffer.read().split()
    n, q = int(data[0]), int(data[1])
    # Bilangan > N tidak mungkin jujur: kumpulkan di N + 1 (cnt-nya tidak pernah mencapai N + 1)
    a = [x if x <= n else n + 1 for x in map(int, data[2:2 + n])]
    L = [int(x) - 1 for x in data[2 + n::2]]   # jendela setengah terbuka [l, r)
    R = [int(x) for x in data[3 + n::2]]

    # Urutan Mo dengan trik ganjil-genap, memakai satu kunci bilangan agar sort cepat
    B = max(1, int(n / q ** 0.5))
    kunci = [0] * q
    for j in range(q):
        b = L[j] // B
        kunci[j] = (b << 20) | (R[j] if b % 2 == 0 else (1 << 20) - R[j])
    urut = sorted(range(q), key=kunci.__getitem__)

    cnt = [0] * (n + 2)
    jawab = [0] * q
    kiri = kanan = 0
    s = 0   # banyak v dengan cnt[v] == v
    for j in urut:
        l = L[j]
        r = R[j]
        # Menambah satu v: jika cnt[v] == v (tadinya jujur) jawaban berkurang,
        # jika cnt[v] == v - 1 (sebentar lagi jujur) jawaban bertambah.
        if kanan < r:
            for v in a[kanan:r]:
                c = cnt[v]
                if c == v:
                    s -= 1
                elif c == v - 1:
                    s += 1
                cnt[v] = c + 1
            kanan = r
        if kiri > l:
            for v in a[l:kiri]:
                c = cnt[v]
                if c == v:
                    s -= 1
                elif c == v - 1:
                    s += 1
                cnt[v] = c + 1
            kiri = l
        # Menghapus satu v: kebalikannya (cnt[v] == v + 1 berarti sebentar lagi jujur).
        if kanan > r:
            for v in a[r:kanan]:
                c = cnt[v]
                if c == v:
                    s -= 1
                elif c == v + 1:
                    s += 1
                cnt[v] = c - 1
            kanan = r
        if kiri < l:
            for v in a[kiri:l]:
                c = cnt[v]
                if c == v:
                    s -= 1
                elif c == v + 1:
                    s += 1
                cnt[v] = c - 1
            kiri = l
        jawab[j] = s
    sys.stdout.write("\n".join(map(str, jawab)) + "\n")


main()
CODE,
        ],
        'editorial' => '<p>Informasi "bilangan mana yang jujur" tidak bisa digabung dari dua bagian (bilangan yang jujur di kiri dan di kanan belum tentu jujur setelah digabung), jadi segment tree tidak membantu. Semua pertanyaan diketahui di awal, sehingga <strong>algoritma Mo</strong> cocok asalkan menambah dan menghapus satu kartu bisa O(1).</p>
<p>Simpan <code>cnt[v]</code> (banyak kartu bertuliskan v di jendela) dan <code>jujur</code> (banyak v dengan <code>cnt[v] = v</code>). Ketika satu kartu bertuliskan v masuk atau keluar, hanya <code>cnt[v]</code> yang berubah, jadi status jujur hanya bisa berubah untuk v. Periksa sebelum <em>dan</em> sesudah perubahan:</p>
<ul><li>tambah(v): jika <code>cnt[v] = v</code> maka <code>jujur--</code>; lalu <code>cnt[v]++</code>; jika sekarang <code>cnt[v] = v</code> maka <code>jujur++</code>.</li>
<li>hapus(v): sama, tetapi <code>cnt[v]--</code>.</li></ul>
<p><strong>Bilangan besar.</strong> a<sub>i</sub> bisa sampai 10<sup>9</sup>, tetapi jendela berisi paling banyak N kartu, sehingga bilangan x &gt; N tidak mungkin tertulis di tepat x kartu. Ganti semua bilangan &gt; N dengan N + 1, sebuah "tempat sampah" yang cnt-nya tidak pernah mencapai N + 1. Dengan begitu <code>cnt</code> cukup berupa array berukuran N + 2 tanpa kompresi. Hati-hati: jangan mengganti dengan 0, karena cnt[0] = 0 akan dianggap jujur.</p>
<p>Urutkan pertanyaan menurut blok l (ukuran blok sekitar N / √Q), lalu menurut r dengan trik ganjil-genap. Geser penunjuk dengan urutan perluas dulu, baru persempit. Total O(N√Q + Q log Q).</p>
<p><strong>Jebakan:</strong></p>
<ul><li>Jika hanya memeriksa "setelah ditambah, apakah <code>cnt[v] = v</code>?", bilangan yang tadinya jujur lalu kelebihan satu kartu tidak pernah dikurangi dari jawaban.</li>
<li>Bilangan jujur yang berbeda x<sub>1</sub> &lt; x<sub>2</sub> &lt; … memakai x<sub>1</sub> + x<sub>2</sub> + … ≤ N kartu, sehingga jawabannya paling banyak sekitar √(2N) ≈ 316. Ini tidak dibutuhkan oleh Mo, tetapi berguna untuk memeriksa hasil.</li></ul>',
        'hints' => [
            'Jika kamu tahu banyak kemunculan setiap bilangan di kartu l..r, bagaimana status jujur berubah ketika satu kartu ditambahkan atau dibuang? Bilangan mana saja yang mungkin terpengaruh?',
            'Hanya bilangan v pada kartu yang masuk/keluar yang bisa berubah status. Periksa cnt[v] == v sebelum dan sesudah cnt[v] diubah. Lalu pakai algoritma Mo karena semua pertanyaan diketahui di awal.',
            'Bilangan lebih besar dari N tidak mungkin jujur: arahkan semuanya ke N + 1 sehingga cnt cukup array berukuran N + 2. Urutkan pertanyaan menurut blok l lalu r (ganjil-genap), dan simpan jawaban sesuai nomor asli.',
        ],
    ],
];
