<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Teori Bilangan & Kombinatorika.
 * Catatan PHP: solusi referensi hanya memakai integer 64-bit biasa (tanpa GMP/bcmath),
 * jadi setiap perkalian dijaga agar < 9,2·10^18 (atau memakai mulmod bertahap).
 * Catatan JS: perkalian modulo 10^9+7 memakai pemecahan 16 bit agar tetap tepat di double.
 */

return [
    // ───────────────────────── Sieve, Faktorisasi & GCD ─────────────────────────
    [
        'slug' => 'prima-di-rentang',
        'lesson' => 'sieve-gcd',
        'title' => 'Banyak Prima di Rentang',
        'difficulty' => 'Mudah',
        'tags' => ['bilangan prima', 'saringan', 'prefix sum'],
        'statement' => '<p>Jawab <strong>Q</strong> pertanyaan. Setiap pertanyaan berisi dua bilangan L dan R: ada berapa bilangan prima p dengan L ≤ p ≤ R?</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi <code>L R</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, banyak bilangan prima di [L, R].</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>1 ≤ L ≤ R ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n1 10\n10 20\n1 1\n", 'explanation' => 'Prima di [1, 10]: 2, 3, 5, 7. Di [10, 20]: 11, 13, 17, 19. Angka 1 bukan prima.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $hi) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $a = mt_rand(1, $hi);
                    $b = mt_rand(1, $hi);
                    $rows[] = min($a, $b).' '.max($a, $b);
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return [
                "4\n1 1000000\n2 2\n999983 999983\n1000000 1000000\n", $gen(100, 100), $gen(1000, 10000), $gen(100000, 1000000), $gen(100000, 50),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $qs = [];
            $M = 2;
            for ($i = 1; $i <= $q; $i++) {
                $qs[] = T::ints($lines[$i]);
                $M = max($M, $qs[$i - 1][1]);
            }
            $is = array_fill(0, $M + 1, 1);
            $is[0] = $is[1] = 0;
            for ($i = 2; $i * $i <= $M; $i++) {
                if ($is[$i]) {
                    for ($j = $i * $i; $j <= $M; $j += $i) {
                        $is[$j] = 0;
                    }
                }
            }
            $pre = [0];
            for ($i = 1; $i <= $M; $i++) {
                $pre[$i] = $pre[$i - 1] + $is[$i];
            }
            $out = [];
            foreach ($qs as [$l, $r]) {
                $out[] = $pre[$r] - $pre[$l - 1];
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000000;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    // 1) saring semua prima ≤ MAKS   2) prefix: banyak prima ≤ x
    int q;
    cin >> q;
    while (q--) {
        int l, r;
        cin >> l >> r;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MAKS = 1000000;
// 1) saring semua prima ≤ MAKS   2) prefix: banyak prima ≤ x
const q = readInts()[0];
const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

MAKS = 10**6
# 1) saring semua prima ≤ MAKS   2) prefix: banyak prima ≤ x
data = sys.stdin.buffer.read().split()
q = int(data[0])
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000000;
bool komposit[MAKS + 1];
int pre[MAKS + 1];                 // pre[x] = banyak prima ≤ x

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    komposit[0] = komposit[1] = true;
    for (int i = 2; (long long)i * i <= MAKS; i++)
        if (!komposit[i])
            for (int j = i * i; j <= MAKS; j += i) komposit[j] = true;
    for (int x = 1; x <= MAKS; x++) pre[x] = pre[x - 1] + (komposit[x] ? 0 : 1);

    int q;
    cin >> q;
    while (q--) {
        int l, r;
        cin >> l >> r;
        cout << pre[r] - pre[l - 1] << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MAKS = 1000000;
const komposit = new Uint8Array(MAKS + 1);
komposit[0] = komposit[1] = 1;
for (let i = 2; i * i <= MAKS; i++)
  if (!komposit[i]) for (let j = i * i; j <= MAKS; j += i) komposit[j] = 1;
const pre = new Int32Array(MAKS + 1);
for (let x = 1; x <= MAKS; x++) pre[x] = pre[x - 1] + (komposit[x] ? 0 : 1);

const q = readInts()[0];
const out = [];
for (let i = 0; i < q; i++) {
  const [l, r] = readInts();
  out.push(pre[r] - pre[l - 1]);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from itertools import accumulate

MAKS = 10**6
prima = bytearray([1]) * (MAKS + 1)
prima[0] = prima[1] = 0
for i in range(2, int(MAKS ** 0.5) + 1):
    if prima[i]:
        prima[i * i::i] = bytes(len(range(i * i, MAKS + 1, i)))
pre = list(accumulate(prima))          # pre[x] = banyak prima ≤ x

data = sys.stdin.buffer.read().split()
q = int(data[0])
out = []
for i in range(q):
    l, r = int(data[1 + 2 * i]), int(data[2 + 2 * i])
    out.append(pre[r] - pre[l - 1])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Menguji setiap bilangan di setiap pertanyaan terlalu lambat (10<sup>5</sup> × 10<sup>6</sup>). Kerjakan dua langkah sekali di awal:</p>
<ol><li><strong>Saringan Eratosthenes</strong> sampai 10<sup>6</sup>: tandai komposit dengan mencoret kelipatan setiap prima mulai dari p². O(N log log N).</li>
<li><strong>Prefix sum</strong>: pre[x] = banyak prima ≤ x.</li></ol>
<p>Setiap pertanyaan dijawab dalam O(1): pre[R] − pre[L − 1]. Hati-hati: 1 bukan prima.</p>',
        'hints' => [
            'Jangan menguji keprimaan per pertanyaan. Apa yang bisa dihitung sekali untuk semua bilangan ≤ 10<sup>6</sup>?',
            'Saringan Eratosthenes memberi tahu prima atau tidak untuk semua bilangan sekaligus.',
            'Buat pre[x] = banyak prima ≤ x. Jawabannya pre[R] − pre[L − 1].',
        ],
    ],

    [
        'slug' => 'fpb-terbesar',
        'lesson' => 'sieve-gcd',
        'title' => 'FPB Terbesar dari Sepasang',
        'difficulty' => 'Sedang',
        'tags' => ['gcd', 'kelipatan', 'harmonic sum'],
        'statement' => '<p>Diberikan <strong>n</strong> bilangan bulat positif. Pilih dua di antaranya (dua posisi berbeda) sehingga <strong>FPB</strong> keduanya sebesar mungkin. Cetak FPB terbesar itu.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: FPB terbesar.</p>',
        'constraints' => '<ul><li>2 ≤ n ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "5\n3 14 15 7 9\n", 'explanation' => 'FPB(14, 7) = 7, FPB(15, 9) = 3, FPB(3, 9) = 3. Yang terbesar 7.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };
            $prima = [];
            for ($x = 999000; count($prima) < 60; $x++) {
                $ok = true;
                for ($d = 2; $d * $d <= $x; $d++) {
                    if ($x % $d == 0) {
                        $ok = false;
                        break;
                    }
                }
                if ($ok) {
                    $prima[] = $x;
                }
            }

            return [
                "2\n1 1\n", "2\n1000000 500000\n", "60\n".T::join($prima)."\n", $gen(10, 50), $gen(1000, 1000000), $gen(200000, 1000000),
                $gen(200000, 300), "3\n999983 999979 1\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $M = max($a);
            $cnt = array_fill(0, $M + 1, 0);
            foreach ($a as $x) {
                $cnt[$x]++;
            }
            for ($d = $M; $d >= 1; $d--) {
                $c = 0;
                for ($k = $d; $k <= $M; $k += $d) {
                    $c += $cnt[$k];
                    if ($c >= 2) {
                        return (string) $d;
                    }
                }
            }

            return '1';
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n;
    cin >> n;
    vector<int> a(n);
    for (auto& v : a) cin >> v;
    // FPB dua bilangan = d berarti keduanya kelipatan d. Coba d dari besar ke kecil.
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = readInts();
// FPB dua bilangan = d berarti keduanya kelipatan d. Coba d dari besar ke kecil.
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + n]))
# FPB dua bilangan = d berarti keduanya kelipatan d. Coba d dari besar ke kecil.
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n;
    cin >> n;
    const int M = 1000000;
    vector<int> cnt(M + 1, 0);
    for (int i = 0; i < n; i++) {
        int x;
        cin >> x;
        cnt[x]++;
    }
    // FPB pasangan ≥ d ⇔ ada dua elemen kelipatan d. Cari d terbesar seperti itu.
    for (int d = M; d >= 1; d--) {
        int banyak = 0;
        for (int k = d; k <= M && banyak < 2; k += d) banyak += cnt[k];
        if (banyak >= 2) {
            cout << d << '\n';
            return 0;
        }
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = readInts();
const M = 1000000;
const cnt = new Int32Array(M + 1);
for (const x of a) cnt[x]++;
let jawab = 1;
for (let d = M; d >= 1; d--) {
  let banyak = 0;
  for (let k = d; k <= M && banyak < 2; k += d) banyak += cnt[k];
  if (banyak >= 2) { jawab = d; break; }
}
console.log(String(jawab));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
M = 10**6
cnt = [0] * (M + 1)
for v in data[1:1 + n]:
    cnt[int(v)] += 1
for d in range(M, 0, -1):
    if sum(cnt[d::d]) >= 2:           # banyak elemen kelipatan d
        print(d)
        break
CODE,
        ],
        'editorial' => '<p>Membandingkan semua pasangan butuh O(n²). Balik pertanyaannya: untuk sebuah d, apakah ada dua elemen yang <strong>keduanya kelipatan d</strong>? Jika ya, FPB mereka ≥ d. Jadi jawabannya adalah d terbesar yang punya paling sedikit dua kelipatan di array (elemen bernilai sama dihitung dua kali).</p>
<p>Simpan cnt[x] = banyak elemen bernilai x. Untuk setiap d dari 10<sup>6</sup> turun ke 1, jumlahkan cnt[d], cnt[2d], cnt[3d], …. Total kerjanya Σ M/d = M · H(M) ≈ 1,4·10<sup>7</sup> (deret harmonik). Begitu ketemu, berhenti.</p>',
        'hints' => [
            'Daripada menghitung FPB setiap pasangan, tanyakan: apakah ada dua elemen yang sama-sama kelipatan d?',
            'Simpan banyak kemunculan setiap nilai. Banyak kelipatan d = cnt[d] + cnt[2d] + cnt[3d] + …',
            'Coba d dari besar ke kecil; total kerja Σ M/d = O(M log M).',
        ],
    ],

    [
        'slug' => 'prima-rentang-besar',
        'lesson' => 'sieve-gcd',
        'title' => 'Prima di Sekitar Satu Triliun',
        'difficulty' => 'Sulit',
        'tags' => ['bilangan prima', 'saringan bersegmen'],
        'statement' => '<p>Untuk setiap pertanyaan (L, R), hitung banyak bilangan prima di [L, R]. Bilangannya bisa sangat besar, tetapi rentangnya sempit.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap dari T baris berikutnya berisi <code>L R</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, banyak bilangan prima di [L, R].</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 3</li><li>1 ≤ L ≤ R ≤ 10<sup>12</sup></li><li>R − L ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "2\n1 30\n1000000000000 1000000000100\n", 'explanation' => 'Ada 10 prima ≤ 30. Di antara 10<sup>12</sup> dan 10<sup>12</sup> + 100 ada 4 prima.'],
        ],
        'tests' => function () {
            $gen = function (int $t, int $lo, int $hi, int $w) {
                $rows = [];
                for ($i = 0; $i < $t; $i++) {
                    $l = mt_rand($lo, $hi);
                    $rows[] = $l.' '.($l + mt_rand(0, $w));
                }

                return "$t\n".implode("\n", $rows)."\n";
            };

            return [
                "3\n1 1\n2 2\n1 1000000\n", "1\n999999000000 1000000000000\n", $gen(3, 1, 1000, 1000), $gen(3, 1000000, 100000000, 1000000),
                $gen(3, 999000000000, 999000000000, 1000000), $gen(2, 1, 1000000, 1000000), $gen(3, 1000000000000 - 3000000, 1000000000000 - 1000000, 1000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $qs = [];
            $maxR = 1;
            for ($i = 1; $i <= $t; $i++) {
                $qs[] = T::ints($lines[$i]);
                $maxR = max($maxR, $qs[$i - 1][1]);
            }
            $lim = (int) floor(sqrt($maxR)) + 1;
            $is = array_fill(0, $lim + 1, true);
            $pr = [];
            for ($i = 2; $i <= $lim; $i++) {
                if ($is[$i]) {
                    $pr[] = $i;
                    for ($j = $i * $i; $j <= $lim; $j += $i) {
                        $is[$j] = false;
                    }
                }
            }
            $out = [];
            foreach ($qs as [$L, $R]) {
                $seg = array_fill(0, $R - $L + 1, true);
                foreach ($pr as $p) {
                    if ($p * $p > $R) {
                        break;
                    }
                    $st = max($p * $p, intdiv($L + $p - 1, $p) * $p);
                    for ($x = $st; $x <= $R; $x += $p) {
                        $seg[$x - $L] = false;
                    }
                }
                $c = 0;
                foreach ($seg as $i => $v) {
                    if ($v && $L + $i >= 2) {
                        $c++;
                    }
                }
                $out[] = $c;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    // 1) saring prima kecil sampai 10^6 (= √10^12)
    // 2) untuk setiap pertanyaan, coret kelipatan prima kecil di dalam jendela [L, R]
    int t;
    cin >> t;
    while (t--) {
        long long L, R;
        cin >> L >> R;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// 1) saring prima kecil sampai 10^6 (= √10^12)
// 2) untuk setiap pertanyaan, coret kelipatan prima kecil di dalam jendela [L, R]
const t = readInts()[0];
for (let i = 0; i < t; i++) {
  const [L, R] = readInts();
}
CODE,
            'python' => <<<'CODE'
import sys

# 1) saring prima kecil sampai 10^6 (= √10^12)
# 2) untuk setiap pertanyaan, coret kelipatan prima kecil di dalam jendela [L, R]
data = sys.stdin.read().split()
t = int(data[0])
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    const int LIM = 1000001;                     // √(10^12) = 10^6
    vector<bool> komposit(LIM + 1, false);
    vector<long long> prima;
    for (int i = 2; i <= LIM; i++) {
        if (komposit[i]) continue;
        prima.push_back(i);
        for (long long j = 1LL * i * i; j <= LIM; j += i) komposit[j] = true;
    }

    int t;
    cin >> t;
    while (t--) {
        long long L, R;
        cin >> L >> R;
        vector<char> buang(R - L + 1, 0);
        for (long long p : prima) {
            if (p * p > R) break;
            long long mulai = max(p * p, (L + p - 1) / p * p);   // kelipatan p pertama ≥ L
            for (long long x = mulai; x <= R; x += p) buang[x - L] = 1;
        }
        long long banyak = 0;
        for (long long x = L; x <= R; x++)
            if (!buang[x - L] && x >= 2) banyak++;
        cout << banyak << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const LIM = 1000001;
const komposit = new Uint8Array(LIM + 1);
const prima = [];
for (let i = 2; i <= LIM; i++) {
  if (komposit[i]) continue;
  prima.push(i);
  for (let j = i * i; j <= LIM; j += i) komposit[j] = 1;
}
const t = readInts()[0];
const out = [];
for (let q = 0; q < t; q++) {
  const [L, R] = readInts();              // ≤ 10^12: masih tepat sebagai Number
  const buang = new Uint8Array(R - L + 1);
  for (const p of prima) {
    if (p * p > R) break;
    let mulai = Math.max(p * p, Math.ceil(L / p) * p);
    for (let x = mulai; x <= R; x += p) buang[x - L] = 1;
  }
  let banyak = 0;
  for (let i = 0; i <= R - L; i++) if (!buang[i] && L + i >= 2) banyak++;
  out.push(banyak);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

LIM = 10**6 + 1
ok = bytearray([1]) * (LIM + 1)
ok[0] = ok[1] = 0
for i in range(2, int(LIM ** 0.5) + 1):
    if ok[i]:
        ok[i * i::i] = bytes(len(range(i * i, LIM + 1, i)))
prima = [i for i in range(2, LIM + 1) if ok[i]]

data = sys.stdin.read().split()
t = int(data[0])
out = []
for q in range(t):
    L, R = int(data[1 + 2 * q]), int(data[2 + 2 * q])
    seg = bytearray([1]) * (R - L + 1)
    for p in prima:
        if p * p > R:
            break
        mulai = max(p * p, (L + p - 1) // p * p)
        if mulai <= R:
            seg[mulai - L::p] = bytes((R - mulai) // p + 1)
    for x in range(L, min(R, 1) + 1):        # 0 dan 1 bukan prima
        seg[x - L] = 0
    out.append(seg.count(1))
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Saringan biasa sampai 10<sup>12</sup> mustahil. Tetapi setiap bilangan komposit x ≤ R punya faktor prima ≤ √x ≤ √R ≤ 10<sup>6</sup>. Jadi:</p>
<ol><li>Saring prima "kecil" sampai 10<sup>6</sup> sekali.</li>
<li>Untuk setiap pertanyaan, siapkan array sepanjang R − L + 1 dan coret kelipatan setiap prima kecil p, mulai dari max(p², kelipatan p pertama ≥ L).</li>
<li>Yang tidak tercoret (dan ≥ 2) adalah prima.</li></ol>
<p>Kerja per pertanyaan O((R − L) log log R + π(√R)). Ini <strong>saringan bersegmen</strong>. Mulai dari p² penting agar p sendiri (jika berada di jendela) tidak ikut dicoret.</p>',
        'hints' => [
            'Komposit ≤ 10<sup>12</sup> pasti punya faktor prima ≤ 10<sup>6</sup>. Prima sebesar itu mudah disaring.',
            'Untuk setiap pertanyaan, buat array kecil sepanjang R − L + 1 dan coret kelipatan prima kecil di dalamnya.',
            'Kelipatan p pertama yang ≥ L adalah ⌈L/p⌉·p; mulai dari max(p², itu). Jangan lupa 1 bukan prima.',
        ],
    ],

    // ───────────────────────── Aritmetika Modular ─────────────────────────
    [
        'slug' => 'binomial-modulo',
        'lesson' => 'modular',
        'title' => 'Koefisien Binomial Modulo',
        'difficulty' => 'Mudah',
        'tags' => ['modular', 'kombinatorika', 'invers modulo'],
        'statement' => '<p>Jawab <strong>Q</strong> pertanyaan: untuk setiap (n, k), hitung <code>C(n, k)</code> (banyak cara memilih k benda dari n) modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi <code>n k</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, C(n, k) mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>0 ≤ k ≤ n ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n5 3\n8 1\n1000000 500000\n", 'explanation' => 'C(5, 3) = 10, C(8, 1) = 8. C(10<sup>6</sup>, 5·10<sup>5</sup>) sangat besar; sisa baginya 996692777.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $nMax) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $n = mt_rand(0, $nMax);
                    $rows[] = $n.' '.mt_rand(0, $n);
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return ["3\n0 0\n1000000 0\n1000000 1000000\n", $gen(100, 20), $gen(1000, 1000), $gen(100000, 1000000)];
        },
        'solve' => function (string $input) {
            $M = 1000000007;
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $qs = [];
            $N = 1;
            for ($i = 1; $i <= $q; $i++) {
                $qs[] = T::ints($lines[$i]);
                $N = max($N, $qs[$i - 1][0]);
            }
            $f = [1];
            for ($i = 1; $i <= $N; $i++) {
                $f[$i] = $f[$i - 1] * $i % $M;
            }
            $pw = function ($a, $e) use ($M) {
                $r = 1;
                $a %= $M;
                while ($e > 0) {
                    if ($e & 1) {
                        $r = $r * $a % $M;
                    }
                    $a = $a * $a % $M;
                    $e >>= 1;
                }

                return $r;
            };
            $inv = array_fill(0, $N + 1, 0);
            $inv[$N] = $pw($f[$N], $M - 2);
            for ($i = $N; $i > 0; $i--) {
                $inv[$i - 1] = $inv[$i] * $i % $M;
            }
            $out = [];
            foreach ($qs as [$n, $k]) {
                $out[] = $f[$n] * $inv[$k] % $M * $inv[$n - $k] % $M;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
const int MAKS = 1000000;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    // pra-hitung fact[i] dan invFact[i], lalu C(n, k) = fact[n] · invFact[k] · invFact[n-k]
    int q;
    cin >> q;
    while (q--) {
        int n, k;
        cin >> n >> k;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
// a·b bisa ~10^18 (tidak tepat di double): pecah b menjadi 16 bit atas dan bawah
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
// pra-hitung fact[i] dan invFact[i], lalu C(n, k) = fact[n] · invFact[k] · invFact[n-k]
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
# pra-hitung fact[i] dan inv_fact[i], lalu C(n, k) = fact[n] · inv_fact[k] · inv_fact[n-k]
data = sys.stdin.buffer.read().split()
q = int(data[0])
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
const int MAKS = 1000000;
long long fact[MAKS + 1], invFact[MAKS + 1];

long long pangkat(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    fact[0] = 1;
    for (int i = 1; i <= MAKS; i++) fact[i] = fact[i - 1] * i % MOD;
    invFact[MAKS] = pangkat(fact[MAKS], MOD - 2);            // Fermat, sekali saja
    for (int i = MAKS; i > 0; i--) invFact[i - 1] = invFact[i] * i % MOD;

    int q;
    cin >> q;
    while (q--) {
        int n, k;
        cin >> n >> k;
        cout << fact[n] * invFact[k] % MOD * invFact[n - k] % MOD << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {
  let r = 1;
  a %= MOD;
  while (e > 0) {
    if (e & 1) r = mul(r, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return r;
};
const MAKS = 1000000;
const fact = new Array(MAKS + 1), invFact = new Array(MAKS + 1);
fact[0] = 1;
for (let i = 1; i <= MAKS; i++) fact[i] = mul(fact[i - 1], i);
invFact[MAKS] = pangkat(fact[MAKS], MOD - 2);
for (let i = MAKS; i > 0; i--) invFact[i - 1] = mul(invFact[i], i);

const q = readInts()[0];
const out = [];
for (let i = 0; i < q; i++) {
  const [n, k] = readInts();
  out.push(mul(mul(fact[n], invFact[k]), invFact[n - k]));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
q = int(data[0])
qs = [(int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(q)]
N = max(n for n, _ in qs)
fact = [1] * (N + 1)
for i in range(1, N + 1):
    fact[i] = fact[i - 1] * i % MOD
inv = [1] * (N + 1)
inv[N] = pow(fact[N], MOD - 2, MOD)
for i in range(N, 0, -1):
    inv[i - 1] = inv[i] * i % MOD
print("\n".join(str(fact[n] * inv[k] % MOD * inv[n - k] % MOD) for n, k in qs))
CODE,
        ],
        'editorial' => '<p>C(n, k) = n! / (k!·(n − k)!). Modulo prima p, pembagian diganti perkalian dengan invers. Pra-hitung:</p>
<ul><li>fact[i] = i! mod p untuk i ≤ 10<sup>6</sup>;</li>
<li>invFact[10<sup>6</sup>] = fact[10<sup>6</sup>]<sup>p−2</sup> (Fermat kecil), lalu mundur: invFact[i − 1] = invFact[i]·i.</li></ul>
<p>Setiap pertanyaan O(1). Di JavaScript, hasil kali dua bilangan &lt; 10<sup>9</sup> + 7 bisa mencapai 10<sup>18</sup> dan tidak tepat sebagai double, jadi perkalian modulo dipecah menjadi dua bagian 16 bit.</p>',
        'hints' => [
            'C(n, k) = n! / (k!(n − k)!). Bagaimana membagi di dunia modulo?',
            'Modulo prima, x / y = x · y<sup>p−2</sup> (Fermat kecil).',
            'Pra-hitung faktorial dan invers faktorial sekali; setiap pertanyaan jadi tiga perkalian.',
        ],
    ],

    [
        'slug' => 'deret-geometri',
        'lesson' => 'modular',
        'title' => 'Deret Geometri Raksasa',
        'difficulty' => 'Sedang',
        'tags' => ['modular', 'perpangkatan cepat', 'invers modulo'],
        'statement' => '<p>Untuk setiap pertanyaan (r, n), hitung</p>
<p><code>S = 1 + r + r² + … + r<sup>n</sup></code></p>
<p>modulo 10<sup>9</sup> + 7. (Anggap 0<sup>0</sup> = 1.)</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap dari T baris berikutnya berisi <code>r n</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, S mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 100 000</li><li>0 ≤ r ≤ 10<sup>9</sup></li><li>0 ≤ n ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "4\n2 3\n1 1000000000000000000\n0 5\n3 0\n", 'explanation' => '1 + 2 + 4 + 8 = 15. Untuk r = 1 jumlahnya n + 1 = 10<sup>18</sup> + 1, sisa baginya 50. Untuk r = 0 hanya suku pertama yang bernilai 1. n = 0: hanya suku 1.'],
        ],
        'tests' => function () {
            $gen = function (int $t, int $rMax, int $nMax) {
                $rows = [];
                for ($i = 0; $i < $t; $i++) {
                    $r = mt_rand(0, $rMax);
                    if ($i % 7 == 0) {
                        $r = 1;
                    } elseif ($i % 11 == 0) {
                        $r = 0;
                    }
                    $rows[] = $r.' '.mt_rand(0, $nMax);
                }

                return "$t\n".implode("\n", $rows)."\n";
            };

            return [
                "3\n1000000000 1000000000000000000\n1 0\n0 0\n", $gen(100, 10, 20), $gen(1000, 1000000000, 1000000000000000000),
                $gen(100000, 1000000000, 1000000000000000000), $gen(100000, 3, 1000000),
            ];
        },
        'solve' => function (string $input) {
            // Referensi tanpa invers: gandakan panjang deret bit demi bit
            $M = 1000000007;
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $t; $i++) {
                [$r, $n] = T::ints($lines[$i]);
                $r %= $M;
                $N = $n + 1;                 // banyak suku
                $sum = 0;                    // jumlah L suku pertama
                $pw = 1;                     // r^L
                for ($b = 62; $b >= 0; $b--) {
                    // gandakan: L -> 2L
                    $sum = $sum * (1 + $pw) % $M;
                    $pw = $pw * $pw % $M;
                    if (($N >> $b) & 1) {   // tambah satu suku: L -> L + 1
                        $sum = ($sum + $pw) % $M;
                        $pw = $pw * $r % $M;
                    }
                }
                $out[] = $sum;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int t;
    cin >> t;
    while (t--) {
        long long r, n;
        cin >> r >> n;
        // rumus (r^(n+1) - 1) / (r - 1) ... kecuali satu kasus khusus
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const t = Number(readLine());
for (let i = 0; i < t; i++) {
  const [rs, ns] = readLine().trim().split(/\s+/);
  const r = Number(rs), n = BigInt(ns);   // n sampai 10^18: pakai BigInt untuk pangkat
}
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
t = int(data[0])
# rumus (r^(n+1) - 1) / (r - 1) ... kecuali satu kasus khusus
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

long long pangkat(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int t;
    cin >> t;
    while (t--) {
        long long r, n;
        cin >> r >> n;
        long long jawab;
        if (r % MOD == 1) {
            jawab = (n + 1) % MOD;                    // semua suku bernilai 1
        } else {
            long long atas = (pangkat(r, n + 1) - 1 + MOD) % MOD;
            long long bawah = (r - 1 + MOD) % MOD;     // r = 0 → -1 ≡ MOD - 1, tetap punya invers
            jawab = atas * pangkat(bawah, MOD - 2) % MOD;
        }
        cout << jawab << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {              // e: BigInt
  let r = 1;
  a %= MOD;
  while (e > 0n) {
    if (e & 1n) r = mul(r, a);
    a = mul(a, a);
    e >>= 1n;
  }
  return r;
};
const t = Number(readLine());
const out = [];
for (let i = 0; i < t; i++) {
  const [rs, ns] = readLine().trim().split(/\s+/);
  const r = Number(rs), n = BigInt(ns);
  if (r === 1) {
    out.push(String((n + 1n) % BigInt(MOD)));
  } else {
    const atas = (pangkat(r, n + 1n) - 1 + MOD) % MOD;
    const bawah = (r - 1 + MOD) % MOD;
    out.push(mul(atas, pangkat(bawah, BigInt(MOD - 2))));
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
t = int(data[0])
out = []
for i in range(t):
    r, n = int(data[1 + 2 * i]), int(data[2 + 2 * i])
    if r % MOD == 1:
        out.append((n + 1) % MOD)
    else:
        atas = (pow(r, n + 1, MOD) - 1) % MOD
        out.append(atas * pow((r - 1) % MOD, MOD - 2, MOD) % MOD)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Rumus deret geometri: S = (r<sup>n+1</sup> − 1)/(r − 1). Modulo prima p:</p>
<ul><li>r<sup>n+1</sup> dihitung dengan perpangkatan cepat (n + 1 sampai 10<sup>18</sup> + 1: ±60 putaran);</li>
<li>pembagian dengan (r − 1) diganti perkalian dengan invers (r − 1)<sup>p−2</sup>.</li></ul>
<p><strong>Kasus khusus:</strong> jika r ≡ 1 (mod p), penyebutnya 0 dan tidak punya invers. Tetapi saat itu semua suku bernilai 1, jadi S = (n + 1) mod p. Karena r ≤ 10<sup>9</sup> &lt; p, ini hanya terjadi untuk r = 1. Untuk r = 0, rumus tetap benar: (0 − 1)/(0 − 1) = 1.</p>
<p>Cara lain tanpa invers: hitung jumlah 2L suku dari jumlah L suku, S<sub>2L</sub> = S<sub>L</sub>·(1 + r<sup>L</sup>), lalu bangun n + 1 bit demi bit.</p>',
        'hints' => [
            'Ingat rumus jumlah deret geometri. Bagaimana menghitung r<sup>n+1</sup> untuk n sampai 10<sup>18</sup>?',
            'Pembagian modulo prima = perkalian dengan invers (Fermat). Kapan invers tidak ada?',
            'Jika r = 1, semua suku bernilai 1: jawabannya (n + 1) mod p.',
        ],
    ],

    [
        'slug' => 'pangkat-bertingkat',
        'lesson' => 'modular',
        'title' => 'Menara Pangkat',
        'difficulty' => 'Sedang',
        'tags' => ['modular', 'teorema Fermat', 'perpangkatan cepat'],
        'statement' => '<p>Untuk setiap pertanyaan (a, b, c), hitung <code>a<sup>(b<sup>c</sup>)</sup></code> modulo 10<sup>9</sup> + 7. (Anggap 0<sup>0</sup> = 1.)</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap dari T baris berikutnya berisi <code>a b c</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, a<sup>(b<sup>c</sup>)</sup> mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 100 000</li><li>1 ≤ a ≤ 10<sup>9</sup></li><li>0 ≤ b, c ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n3 7 1\n15 2 2\n3 4 5\n", 'explanation' => '3<sup>7</sup> = 2187. 15<sup>4</sup> = 50625. 3<sup>1024</sup> mod (10<sup>9</sup> + 7) = 763327764.'],
        ],
        'tests' => function () {
            $gen = function (int $t, int $hi) {
                $rows = [];
                for ($i = 0; $i < $t; $i++) {
                    $rows[] = mt_rand(1, $hi).' '.mt_rand(0, $hi).' '.mt_rand(0, $hi);
                }

                return "$t\n".implode("\n", $rows)."\n";
            };

            return [
                "4\n2 0 0\n2 0 5\n5 1000000006 1\n1000000000 1000000000 1000000000\n", $gen(100, 10), $gen(1000, 1000000000), $gen(100000, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $M = 1000000007;
            $pw = function ($a, $e, $m) {
                $r = 1 % $m;
                $a %= $m;
                while ($e > 0) {
                    if ($e & 1) {
                        $r = $r * $a % $m;
                    }
                    $a = $a * $a % $m;
                    $e >>= 1;
                }

                return $r;
            };
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $t; $i++) {
                [$a, $b, $c] = T::ints($lines[$i]);
                $e = $pw($b, $c, $M - 1);
                $out[] = $pw($a, $e, $M);
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int t;
    cin >> t;
    while (t--) {
        long long a, b, c;
        cin >> a >> b >> c;
        // b^c terlalu besar untuk dihitung langsung. Modulo berapa eksponen boleh direduksi?
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
// perkalian modulo m (m ≤ 10^9+7) tanpa kehilangan presisi
const mulm = (a, b, m) => ((a * (b >>> 16)) % m * 65536 + a * (b & 65535)) % m;
const t = readInts()[0];
for (let i = 0; i < t; i++) {
  const [a, b, c] = readInts();
}
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
t = int(data[0])
# b^c terlalu besar untuk dihitung langsung. Modulo berapa eksponen boleh direduksi?
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

long long pangkat(long long a, long long e, long long m) {
    long long r = 1 % m;
    a %= m;
    while (e > 0) {
        if (e & 1) r = r * a % m;
        a = a * a % m;
        e >>= 1;
    }
    return r;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int t;
    cin >> t;
    while (t--) {
        long long a, b, c;
        cin >> a >> b >> c;
        // Fermat: a^(p-1) ≡ 1 untuk a tidak habis dibagi p, jadi eksponen cukup modulo p-1
        long long e = pangkat(b, c, MOD - 1);
        cout << pangkat(a, e, MOD) << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mulm = (a, b, m) => ((a * (b >>> 16)) % m * 65536 + a * (b & 65535)) % m;
const pangkat = (a, e, m) => {
  let r = 1 % m;
  a %= m;
  while (e > 0) {
    if (e % 2 === 1) r = mulm(r, a, m);
    a = mulm(a, a, m);
    e = Math.floor(e / 2);
  }
  return r;
};
const t = readInts()[0];
const out = [];
for (let i = 0; i < t; i++) {
  const [a, b, c] = readInts();
  out.push(pangkat(a, pangkat(b, c, MOD - 1), MOD));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
t = int(data[0])
out = []
for i in range(t):
    a, b, c = int(data[1 + 3 * i]), int(data[2 + 3 * i]), int(data[3 + 3 * i])
    out.append(pow(a, pow(b, c, MOD - 1), MOD))
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>b<sup>c</sup> bisa punya miliaran digit, jadi tidak bisa dihitung. Tetapi kita hanya butuh a<sup>e</sup> mod p dengan p = 10<sup>9</sup> + 7 prima.</p>
<p><strong>Fermat kecil:</strong> jika p tidak membagi a, maka a<sup>p−1</sup> ≡ 1, sehingga a<sup>e</sup> ≡ a<sup>e mod (p−1)</sup>. Karena 1 ≤ a ≤ 10<sup>9</sup> &lt; p, syarat itu selalu terpenuhi. Jadi hitung e = b<sup>c</sup> mod (p − 1) dengan perpangkatan cepat, lalu a<sup>e</sup> mod p.</p>
<p>Hati-hati: eksponen direduksi modulo <strong>p − 1</strong>, bukan p. Kasus b = c = 0 memberi e = 0<sup>0</sup> = 1 (perpangkatan cepat dengan hasil awal 1 menanganinya).</p>',
        'hints' => [
            'b<sup>c</sup> terlalu besar. Apakah a<sup>e</sup> mod p bergantung pada seluruh e, atau cukup e modulo sesuatu?',
            'Teorema Fermat kecil: a<sup>p−1</sup> ≡ 1 (mod p) jika p tidak membagi a.',
            'Hitung e = b<sup>c</sup> mod (p − 1), lalu jawabannya a<sup>e</sup> mod p.',
        ],
    ],

    // ───────────────────────── Extended Euclid & CRT ─────────────────────────
    [
        'slug' => 'beli-ayam-bebek',
        'lesson' => 'ext-euclid',
        'title' => 'Belanja Pas di Pasar Ternak',
        'difficulty' => 'Sedang',
        'tags' => ['extended euclid', 'diophantine'],
        'statement' => '<p>Seekor ayam berharga <strong>a</strong> rupiah dan seekor bebek <strong>b</strong> rupiah. Pak Tani ingin menghabiskan uangnya tepat <strong>c</strong> rupiah (boleh membeli nol ekor dari salah satu jenis).</p>
<p>Ada berapa pasangan (x, y) bilangan bulat tak negatif dengan <code>a·x + b·y = c</code>? Jawab untuk T pertanyaan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap dari T baris berikutnya berisi <code>a b c</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, banyak pasangan (x, y).</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 10 000</li><li>1 ≤ a, b ≤ 10<sup>9</sup></li><li>0 ≤ c ≤ 10<sup>12</sup></li></ul>',
        'samples' => [
            ['input' => "3\n3 5 22\n4 6 15\n2 3 12\n", 'explanation' => '3x + 5y = 22 hanya punya (x, y) = (4, 2). 4x + 6y = 15 mustahil (ruas kiri genap). 2x + 3y = 12: (0, 4), (3, 2), (6, 0) → 3 cara.'],
        ],
        'tests' => function () {
            $gen = function (int $t, int $abMax, int $cMax, bool $kelipatan) {
                $rows = [];
                for ($i = 0; $i < $t; $i++) {
                    $g = $kelipatan ? mt_rand(1, min(50, $abMax)) : 1;
                    $a = mt_rand(1, intdiv($abMax, $g)) * $g;
                    $b = mt_rand(1, intdiv($abMax, $g)) * $g;
                    $c = mt_rand(0, $cMax);
                    if ($i % 3 == 0) {
                        $c -= $c % $g;
                    }
                    $rows[] = "$a $b $c";
                }

                return "$t\n".implode("\n", $rows)."\n";
            };

            return [
                "4\n1 1 0\n1 1 1000000000000\n1000000000 999999999 1000000000000\n7 7 49\n", $gen(300, 20, 200, true), $gen(1000, 1000, 1000000, true),
                $gen(10000, 1000000000, 1000000000000, false), $gen(10000, 100000, 1000000000000, true), $gen(10000, 1000, 1000000000000, false),
            ];
        },
        'solve' => function (string $input) {
            $eg = function ($a, $b) {
                // iteratif: mengembalikan [g, x] dengan a*x ≡ g (mod b)
                $x0 = 1;
                $x1 = 0;
                while ($b) {
                    $q = intdiv($a, $b);
                    [$a, $b] = [$b, $a - $q * $b];
                    [$x0, $x1] = [$x1, $x0 - $q * $x1];
                }

                return [$a, $x0];
            };
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $t; $i++) {
                [$a, $b, $c] = T::ints($lines[$i]);
                [$g] = $eg($a, $b);
                if ($c % $g) {
                    $out[] = 0;

                    continue;
                }
                $A = intdiv($a, $g);
                $B = intdiv($b, $g);
                $C = intdiv($c, $g);
                if ($B == 1) {
                    $x0 = 0;
                } else {
                    [, $inv] = $eg($A % $B, $B);
                    $inv = (($inv % $B) + $B) % $B;
                    $x0 = ($C % $B) * $inv % $B;
                }
                $out[] = $A * $x0 > $C ? 0 : intdiv($C - $A * $x0, $A * $B) + 1;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

long long extgcd(long long a, long long b, long long& x, long long& y) {
    if (b == 0) { x = 1; y = 0; return a; }
    long long x1, y1, g = extgcd(b, a % b, x1, y1);
    x = y1;
    y = x1 - (a / b) * y1;
    return g;
}

int main() {
    int t;
    cin >> t;
    while (t--) {
        long long a, b, c;
        cin >> a >> b >> c;
        // cari x terkecil yang tak negatif, lalu hitung berapa kali x bisa digeser
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// nilai sampai 10^18 → pakai BigInt
const t = Number(readLine());
for (let i = 0; i < t; i++) {
  const [a, b, c] = readLine().trim().split(/\s+/).map(BigInt);
  // cari x terkecil yang tak negatif, lalu hitung berapa kali x bisa digeser
}
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
t = int(data[0])
# cari x terkecil yang tak negatif, lalu hitung berapa kali x bisa digeser
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

long long extgcd(long long a, long long b, long long& x, long long& y) {
    if (b == 0) { x = 1; y = 0; return a; }
    long long x1, y1, g = extgcd(b, a % b, x1, y1);
    x = y1;
    y = x1 - (a / b) * y1;
    return g;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int t;
    cin >> t;
    while (t--) {
        long long a, b, c, x, y;
        cin >> a >> b >> c;
        long long g = extgcd(a, b, x, y);            // a*x + b*y = g
        if (c % g != 0) {
            cout << 0 << '\n';
            continue;
        }
        long long A = a / g, B = b / g, C = c / g;   // A*x ≡ C (mod B), gcd(A, B) = 1
        long long inv = ((x % B) + B) % B;            // x adalah invers A modulo B
        long long x0 = (C % B) * inv % B;             // x tak negatif terkecil (< 10^18)
        if (A * x0 > C) {                             // y pasti negatif
            cout << 0 << '\n';
            continue;
        }
        // x = x0 + k*B, y turun A untuk setiap k; y ≥ 0  ⇔  k ≤ (C - A*x0) / (A*B)
        cout << (C - A * x0) / (A * B) + 1 << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const extgcd = (a, b) => {            // [g, x] dengan a*x ≡ g (mod b)
  let x0 = 1n, x1 = 0n;
  while (b) {
    const q = a / b;
    [a, b] = [b, a - q * b];
    [x0, x1] = [x1, x0 - q * x1];
  }
  return [a, x0];
};
const t = Number(readLine());
const out = [];
for (let i = 0; i < t; i++) {
  const [a, b, c] = readLine().trim().split(/\s+/).map(BigInt);
  const [g, x] = extgcd(a, b);
  if (c % g !== 0n) { out.push("0"); continue; }
  const A = a / g, B = b / g, C = c / g;
  const inv = ((x % B) + B) % B;
  const x0 = (C % B) * inv % B;
  out.push(A * x0 > C ? "0" : String((C - A * x0) / (A * B) + 1n));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

def extgcd(a, b):            # (g, x) dengan a*x ≡ g (mod b)
    x0, x1 = 1, 0
    while b:
        q = a // b
        a, b = b, a - q * b
        x0, x1 = x1, x0 - q * x1
    return a, x0

data = sys.stdin.buffer.read().split()
t = int(data[0])
out = []
for i in range(t):
    a, b, c = int(data[1 + 3 * i]), int(data[2 + 3 * i]), int(data[3 + 3 * i])
    g, x = extgcd(a, b)
    if c % g:
        out.append(0)
        continue
    A, B, C = a // g, b // g, c // g
    x0 = C % B * (x % B) % B
    out.append(0 if A * x0 > C else (C - A * x0) // (A * B) + 1)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Persamaan a·x + b·y = c punya solusi bulat ⇔ g = gcd(a, b) membagi c. Jika ya, bagi semuanya dengan g: A·x + B·y = C dengan gcd(A, B) = 1.</p>
<p>Modulo B, persamaan menjadi A·x ≡ C, jadi x ≡ C·A<sup>−1</sup> (mod B). Invers A modulo B didapat dari extended Euclid. Ambil x<sub>0</sub> ∈ [0, B) sebagai solusi tak negatif terkecil.</p>
<p>Semua solusi: x = x<sub>0</sub> + k·B dan y = (C − A·x<sub>0</sub>)/B − k·A. Syarat y ≥ 0 memberi 0 ≤ k ≤ (C − A·x<sub>0</sub>)/(A·B). Jadi banyaknya ⌊(C − A·x<sub>0</sub>)/(A·B)⌋ + 1, atau 0 jika A·x<sub>0</sub> &gt; C.</p>
<p>Semua hasil kali di sini ≤ 10<sup>18</sup>, aman di <code>long long</code>; di JavaScript pakai <code>BigInt</code>.</p>',
        'hints' => [
            'Kapan a·x + b·y = c punya solusi bulat? Hubungkan dengan gcd(a, b).',
            'Setelah dibagi gcd, x ≡ C·A<sup>−1</sup> (mod B). Ambil x tak negatif terkecil.',
            'Dari x terkecil, setiap kenaikan x sebesar B menurunkan y sebesar A. Hitung berapa kali bisa naik sampai y masih ≥ 0.',
        ],
    ],

    [
        'slug' => 'crt-gabungan',
        'lesson' => 'ext-euclid',
        'title' => 'Jadwal Lampu Berkedip',
        'difficulty' => 'Sulit',
        'tags' => ['chinese remainder theorem', 'extended euclid'],
        'statement' => '<p>Ada <strong>k</strong> lampu. Lampu ke-i berkedip pada detik-detik t yang memenuhi <code>t ≡ r<sub>i</sub> (mod m<sub>i</sub>)</code>. Kapan pertama kali (detik t ≥ 0) <strong>semua</strong> lampu berkedip bersamaan? Cetak <code>-1</code> jika tidak pernah.</p>
<p>Modulus-modulus m<sub>i</sub> tidak harus saling prima. Dijamin KPK semua m<sub>i</sub> tidak melebihi 10<sup>18</sup>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>k</code>. Setiap dari k baris berikutnya berisi <code>r<sub>i</sub> m<sub>i</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: t terkecil, atau -1.</p>',
        'constraints' => '<ul><li>1 ≤ k ≤ 1000</li><li>1 ≤ m<sub>i</sub> ≤ 10<sup>9</sup>, 0 ≤ r<sub>i</sub> &lt; m<sub>i</sub></li><li>KPK semua m<sub>i</sub> ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "3\n2 3\n3 5\n2 7\n", 'explanation' => 't = 23: 23 mod 3 = 2, 23 mod 5 = 3, 23 mod 7 = 2.'],
            ['input' => "2\n1 4\n2 6\n", 'explanation' => 't ≡ 1 (mod 4) berarti t ganjil, t ≡ 2 (mod 6) berarti t genap: mustahil.'],
        ],
        'tests' => function () {
            $buatL = function (int $batas) {
                $pr = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61];
                $L = 1;
                $f = [];
                for ($tries = 0; $tries < 200; $tries++) {
                    $p = $pr[mt_rand(0, count($pr) - 1)];
                    if ($L <= intdiv($batas, $p)) {
                        $L *= $p;
                        $f[] = $p;
                    }
                }

                return [$L, $f];
            };
            $gen = function (int $k, bool $rusak) use ($buatL) {
                [$L, $f] = $buatL(1000000000000000000);
                $x = mt_rand(0, $L - 1);
                $rows = [];
                for ($i = 0; $i < $k; $i++) {
                    // pembagi acak L yang ≤ 10^9
                    $m = 1;
                    $fs = $f;
                    T::shuffle($fs);
                    foreach ($fs as $p) {
                        if ($m * $p <= 1000000000 && mt_rand(0, 2)) {
                            $m *= $p;
                        }
                    }
                    $rows[] = [$x % $m, $m];
                }
                if ($rusak) {
                    for ($i = 0; $i < $k; $i++) {
                        if ($rows[$i][1] > 1) {
                            $rows[$i][0] = ($rows[$i][0] + 1) % $rows[$i][1];
                            break;
                        }
                    }
                }

                return "$k\n".implode("\n", array_map(fn ($r) => $r[0].' '.$r[1], $rows))."\n";
            };

            return [
                "1\n0 1\n", "1\n999999999 1000000000\n", "2\n0 1000000000\n0 999999999\n", "2\n999999998 999999999\n999999999 1000000000\n",
                $gen(5, false), $gen(5, true), $gen(100, false), $gen(1000, false), $gen(1000, true), $gen(30, false), $gen(2, false),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $k = (int) $lines[0];
            $eg = function ($a, $b) {
                $x0 = 1;
                $x1 = 0;
                while ($b) {
                    $q = intdiv($a, $b);
                    [$a, $b] = [$b, $a - $q * $b];
                    [$x0, $x1] = [$x1, $x0 - $q * $x1];
                }

                return [$a, $x0];
            };
            $r = 0;
            $m = 1;
            for ($i = 1; $i <= $k; $i++) {
                [$r2, $m2] = T::ints($lines[$i]);
                [$g, $p] = $eg($m, $m2);           // m*p ≡ g (mod m2)
                if (($r2 - $r) % $g != 0) {
                    return '-1';
                }
                $M2 = intdiv($m2, $g);
                $d = intdiv($r2 - $r, $g) % $M2;
                if ($d < 0) {
                    $d += $M2;
                }
                $pp = $p % $M2;
                if ($pp < 0) {
                    $pp += $M2;
                }
                $tt = $d * $pp % $M2;              // < 10^18
                $r = $r + $m * $tt;                // < KPK baru ≤ 10^18
                $m = $m * $M2;
                $r %= $m;
            }

            return (string) $r;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

long long extgcd(long long a, long long b, long long& x, long long& y) {
    if (b == 0) { x = 1; y = 0; return a; }
    long long x1, y1, g = extgcd(b, a % b, x1, y1);
    x = y1;
    y = x1 - (a / b) * y1;
    return g;
}

int main() {
    int k;
    cin >> k;
    long long r = 0, m = 1;          // sejauh ini: t ≡ r (mod m)
    for (int i = 0; i < k; i++) {
        long long r2, m2;
        cin >> r2 >> m2;
        // gabungkan (r, m) dengan (r2, m2)
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// KPK sampai 10^18 → pakai BigInt
const k = Number(readLine());
let r = 0n, m = 1n;              // sejauh ini: t ≡ r (mod m)
for (let i = 0; i < k; i++) {
  const [r2, m2] = readLine().trim().split(/\s+/).map(BigInt);
  // gabungkan (r, m) dengan (r2, m2)
}
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
k = int(data[0])
r, m = 0, 1                      # sejauh ini: t ≡ r (mod m)
# gabungkan satu per satu
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

long long extgcd(long long a, long long b, long long& x, long long& y) {
    if (b == 0) { x = 1; y = 0; return a; }
    long long x1, y1, g = extgcd(b, a % b, x1, y1);
    x = y1;
    y = x1 - (a / b) * y1;
    return g;
}

int main() {
    int k;
    cin >> k;
    long long r = 0, m = 1;                          // t ≡ r (mod m)
    bool mustahil = false;
    for (int i = 0; i < k; i++) {
        long long r2, m2;
        cin >> r2 >> m2;
        if (mustahil) continue;
        long long p, q;
        long long g = extgcd(m, m2, p, q);           // m*p + m2*q = g
        if ((r2 - r) % g != 0) {                     // tidak konsisten
            mustahil = true;
            continue;
        }
        // t = r + m*s, dengan m*s ≡ r2 - r (mod m2)  →  s ≡ (r2-r)/g * p (mod m2/g)
        long long M2 = m2 / g;
        long long d = ((r2 - r) / g % M2 + M2) % M2;
        long long pp = (p % M2 + M2) % M2;
        long long s = d * pp % M2;                   // d, pp < 10^9: hasil kali < 10^18
        r = r + m * s;                               // m*s < KPK baru ≤ 10^18
        m = m * M2;                                  // KPK(m, m2)
        r %= m;
    }
    cout << (mustahil ? -1 : r) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const extgcd = (a, b) => {
  let x0 = 1n, x1 = 0n;
  while (b) {
    const q = a / b;
    [a, b] = [b, a - q * b];
    [x0, x1] = [x1, x0 - q * x1];
  }
  return [a, x0];
};
const k = Number(readLine());
let r = 0n, m = 1n, mustahil = false;
for (let i = 0; i < k; i++) {
  const [r2, m2] = readLine().trim().split(/\s+/).map(BigInt);
  if (mustahil) continue;
  const [g, p] = extgcd(m, m2);
  if ((r2 - r) % g !== 0n) { mustahil = true; continue; }
  const M2 = m2 / g;
  const s = ((((r2 - r) / g) % M2 + M2) % M2) * ((p % M2 + M2) % M2) % M2;
  r = r + m * s;
  m = m * M2;
  r %= m;
}
console.log(mustahil ? "-1" : String(r));
CODE,
            'python' => <<<'CODE'
import sys

def extgcd(a, b):
    x0, x1 = 1, 0
    while b:
        q = a // b
        a, b = b, a - q * b
        x0, x1 = x1, x0 - q * x1
    return a, x0

data = sys.stdin.buffer.read().split()
k = int(data[0])
r, m = 0, 1
jawab = None
for i in range(k):
    r2, m2 = int(data[1 + 2 * i]), int(data[2 + 2 * i])
    g, p = extgcd(m, m2)
    if (r2 - r) % g:
        jawab = -1
        break
    M2 = m2 // g
    s = (r2 - r) // g % M2 * (p % M2) % M2
    r = (r + m * s) % (m * M2)
    m *= M2
print(r if jawab is None else -1)
CODE,
        ],
        'editorial' => '<p>Gabungkan kongruensi satu per satu. Misalkan sejauh ini t ≡ r (mod m) dan datang t ≡ r<sub>2</sub> (mod m<sub>2</sub>). Tulis t = r + m·s; syaratnya m·s ≡ r<sub>2</sub> − r (mod m<sub>2</sub>).</p>
<ul><li>Dengan g = gcd(m, m<sub>2</sub>): jika g tidak membagi r<sub>2</sub> − r, tidak ada solusi.</li>
<li>Jika membagi, extended Euclid memberi p dengan m·p ≡ g (mod m<sub>2</sub>), sehingga s ≡ (r<sub>2</sub> − r)/g · p (mod m<sub>2</sub>/g).</li>
<li>Gabungannya t ≡ r + m·s (mod KPK(m, m<sub>2</sub>)), dengan KPK = m · (m<sub>2</sub>/g).</li></ul>
<p><strong>Overflow:</strong> dengan s dan p direduksi modulo m<sub>2</sub>/g ≤ 10<sup>9</sup>, hasil kali (r<sub>2</sub> − r)/g · p &lt; 10<sup>18</sup>, dan m·s &lt; KPK baru ≤ 10<sup>18</sup>. Jadi cukup <code>long long</code>. Jika batas KPK lebih besar, pakai <code>__int128</code>.</p>',
        'hints' => [
            'Gabungkan dua kongruensi dulu: t ≡ r (mod m) dan t ≡ r₂ (mod m₂). Tulis t = r + m·s.',
            'm·s ≡ r₂ − r (mod m₂) adalah persamaan Diophantine: punya solusi ⇔ gcd(m, m₂) membagi r₂ − r.',
            'Solusi gabungannya unik modulo KPK(m, m₂). Ulangi untuk semua lampu; jaga semua nilai di bawah modulus agar tidak overflow.',
        ],
    ],

    // ───────────────────────── Matrix Exponentiation ─────────────────────────
    [
        'slug' => 'rekurens-linear',
        'lesson' => 'matrix-expo',
        'title' => 'Rekurens Linear ke-10^18',
        'difficulty' => 'Sedang',
        'tags' => ['matrix exponentiation', 'rekurens'],
        'statement' => '<p>Barisan f didefinisikan oleh k nilai awal f(0), f(1), …, f(k − 1) dan rekurens</p>
<p><code>f(i) = c<sub>1</sub>·f(i − 1) + c<sub>2</sub>·f(i − 2) + … + c<sub>k</sub>·f(i − k)</code> untuk i ≥ k.</p>
<p>Hitung f(n) modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>k n</code>. Baris kedua berisi <code>c<sub>1</sub> … c<sub>k</sub></code>. Baris ketiga berisi <code>f(0) … f(k − 1)</code>.</p>',
        'output_format' => '<p>Satu bilangan: f(n) mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ k ≤ 10</li><li>0 ≤ n ≤ 10<sup>18</sup></li><li>0 ≤ c<sub>i</sub>, f(i) &lt; 10<sup>9</sup> + 7</li></ul>',
        'samples' => [
            ['input' => "2 10\n1 1\n0 1\n", 'explanation' => 'Fibonacci: f(10) = 55.'],
            ['input' => "3 5\n1 1 1\n1 1 1\n", 'explanation' => 'Tribonacci: 1, 1, 1, 3, 5, 9 → f(5) = 9.'],
        ],
        'tests' => function () {
            $gen = function (int $k, int $nMax, int $vMax) {
                $n = mt_rand(0, $nMax);

                return "$k $n\n".T::join(T::arr($k, 0, $vMax))."\n".T::join(T::arr($k, 0, $vMax))."\n";
            };

            return [
                "1 0\n5\n7\n", "1 1000000000000000000\n2\n1\n", "2 1000000000000000000\n1 1\n0 1\n", $gen(3, 30, 10), $gen(5, 50, 1000),
                $gen(10, 1000000000000000000, 1000000006), $gen(10, 9, 1000000006), $gen(7, 1000000000000000000, 1000000006), $gen(4, 1000000, 5),
            ];
        },
        'solve' => function (string $input) {
            $M = 1000000007;
            $lines = T::lines($input);
            [$k, $n] = T::ints($lines[0]);
            $c = T::ints($lines[1]);
            $f = T::ints($lines[2]);
            if ($n < $k) {
                return (string) ($f[$n] % $M);
            }
            $mul = function ($A, $B) use ($k, $M) {
                $C = array_fill(0, $k, array_fill(0, $k, 0));
                for ($i = 0; $i < $k; $i++) {
                    for ($t = 0; $t < $k; $t++) {
                        if ($A[$i][$t] == 0) {
                            continue;
                        }
                        for ($j = 0; $j < $k; $j++) {
                            $C[$i][$j] = ($C[$i][$j] + $A[$i][$t] * $B[$t][$j]) % $M;
                        }
                    }
                }

                return $C;
            };
            $T = array_fill(0, $k, array_fill(0, $k, 0));
            for ($j = 0; $j < $k; $j++) {
                $T[0][$j] = $c[$j] % $M;
            }
            for ($i = 1; $i < $k; $i++) {
                $T[$i][$i - 1] = 1;
            }
            $R = array_fill(0, $k, array_fill(0, $k, 0));
            for ($i = 0; $i < $k; $i++) {
                $R[$i][$i] = 1;
            }
            $e = $n - $k + 1;
            while ($e > 0) {
                if ($e & 1) {
                    $R = $mul($R, $T);
                }
                $T = $mul($T, $T);
                $e >>= 1;
            }
            $ans = 0;
            for ($j = 0; $j < $k; $j++) {
                $ans = ($ans + $R[0][$j] * ($f[$k - 1 - $j] % $M)) % $M;
            }

            return (string) $ans;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
typedef vector<vector<long long>> Mat;

int main() {
    int k;
    long long n;
    cin >> k >> n;
    vector<long long> c(k), f(k);
    for (auto& v : c) cin >> v;
    for (auto& v : f) cin >> v;
    // matriks pendamping k × k, lalu pangkatkan
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const [ks, ns] = readLine().trim().split(/\s+/);
const k = Number(ks), n = BigInt(ns);
const c = readInts(), f = readInts();
// matriks pendamping k × k, lalu pangkatkan
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7
k, n = map(int, input().split())
c = list(map(int, input().split()))
f = list(map(int, input().split()))
# matriks pendamping k × k, lalu pangkatkan
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
typedef vector<vector<long long>> Mat;

Mat kali(const Mat& A, const Mat& B) {
    int k = A.size();
    Mat C(k, vector<long long>(k, 0));
    for (int i = 0; i < k; i++)
        for (int t = 0; t < k; t++) {
            if (!A[i][t]) continue;
            for (int j = 0; j < k; j++) C[i][j] = (C[i][j] + A[i][t] * B[t][j]) % MOD;
        }
    return C;
}

int main() {
    int k;
    long long n;
    cin >> k >> n;
    vector<long long> c(k), f(k);
    for (auto& v : c) cin >> v;
    for (auto& v : f) cin >> v;
    if (n < k) {
        cout << f[n] % MOD << '\n';
        return 0;
    }
    // keadaan [f(i), f(i-1), ..., f(i-k+1)]; T menggeser satu langkah
    Mat T(k, vector<long long>(k, 0)), R(k, vector<long long>(k, 0));
    for (int j = 0; j < k; j++) T[0][j] = c[j] % MOD;
    for (int i = 1; i < k; i++) T[i][i - 1] = 1;
    for (int i = 0; i < k; i++) R[i][i] = 1;
    for (long long e = n - k + 1; e > 0; e >>= 1) {     // dari keadaan i = k-1 ke i = n
        if (e & 1) R = kali(R, T);
        T = kali(T, T);
    }
    long long jawab = 0;
    for (int j = 0; j < k; j++) jawab = (jawab + R[0][j] * (f[k - 1 - j] % MOD)) % MOD;
    cout << jawab << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const [ks, ns] = readLine().trim().split(/\s+/);
const k = Number(ks), n = BigInt(ns);
const c = readInts(), f = readInts();
const kali = (A, B) => {
  const C = Array.from({ length: k }, () => new Array(k).fill(0));
  for (let i = 0; i < k; i++)
    for (let t = 0; t < k; t++) {
      if (!A[i][t]) continue;
      for (let j = 0; j < k; j++) C[i][j] = (C[i][j] + mul(A[i][t], B[t][j])) % MOD;
    }
  return C;
};
if (n < BigInt(k)) {
  console.log(String(f[Number(n)] % MOD));
} else {
  let T = Array.from({ length: k }, () => new Array(k).fill(0));
  let R = Array.from({ length: k }, (_, i) => Array.from({ length: k }, (_, j) => (i === j ? 1 : 0)));
  for (let j = 0; j < k; j++) T[0][j] = c[j] % MOD;
  for (let i = 1; i < k; i++) T[i][i - 1] = 1;
  for (let e = n - BigInt(k) + 1n; e > 0n; e >>= 1n) {
    if (e & 1n) R = kali(R, T);
    T = kali(T, T);
  }
  let jawab = 0;
  for (let j = 0; j < k; j++) jawab = (jawab + mul(R[0][j], f[k - 1 - j] % MOD)) % MOD;
  console.log(String(jawab));
}
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7

def kali(A, B):
    return [[sum(a * b for a, b in zip(baris, kolom)) % MOD for kolom in zip(*B)] for baris in A]

k, n = map(int, input().split())
c = list(map(int, input().split()))
f = list(map(int, input().split()))
if n < k:
    print(f[n] % MOD)
else:
    T = [[0] * k for _ in range(k)]
    T[0] = [x % MOD for x in c]
    for i in range(1, k):
        T[i][i - 1] = 1
    R = [[int(i == j) for j in range(k)] for i in range(k)]
    e = n - k + 1
    while e:
        if e & 1:
            R = kali(R, T)
        T = kali(T, T)
        e >>= 1
    print(sum(R[0][j] * f[k - 1 - j] for j in range(k)) % MOD)
CODE,
        ],
        'editorial' => '<p>Simpan keadaan berupa k suku terakhir: v<sub>i</sub> = [f(i), f(i − 1), …, f(i − k + 1)]. Satu langkah rekurens adalah perkalian dengan <strong>matriks pendamping</strong> T: baris pertama berisi c<sub>1</sub> … c<sub>k</sub> (menghitung suku baru), baris lain menggeser keadaan (T[i][i − 1] = 1).</p>
<p>Mulai dari v<sub>k−1</sub> = [f(k − 1), …, f(0)], maka v<sub>n</sub> = T<sup>n − k + 1</sup>·v<sub>k−1</sub>, dan f(n) adalah komponen pertamanya. Pangkat matriks dengan kuadrat berulang: O(k³ log n) ≈ 1000 · 60 operasi.</p>
<p>Jangan lupa kasus n &lt; k (langsung f(n)) dan ambil modulo setiap kali menjumlahkan hasil kali.</p>',
        'hints' => [
            'Keadaan apa yang cukup untuk menghitung suku berikutnya? (k suku terakhir.)',
            'Tulis satu langkah rekurens sebagai perkalian matriks k × k dengan vektor keadaan.',
            'Pangkatkan matriks itu n − k + 1 kali dengan kuadrat berulang, lalu kalikan dengan vektor awal [f(k−1), …, f(0)].',
        ],
    ],

    [
        'slug' => 'jalan-tepat-k',
        'lesson' => 'matrix-expo',
        'title' => 'Jalan Sepanjang Tepat K',
        'difficulty' => 'Sedang',
        'tags' => ['matrix exponentiation', 'graph'],
        'statement' => '<p>Diberikan graph berarah dengan <strong>n</strong> simpul dan <strong>m</strong> sisi (boleh ada sisi ganda dan sisi ke diri sendiri). Ada berapa <strong>jalan</strong> (walk) dari simpul 1 ke simpul n yang memakai tepat <strong>K</strong> sisi? Simpul dan sisi boleh dilewati berulang kali; dua jalan berbeda jika urutan sisinya berbeda.</p>
<p>Cetak jawabannya modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n m K</code>. Setiap dari m baris berikutnya berisi <code>u v</code>: sisi dari u ke v.</p>',
        'output_format' => '<p>Satu bilangan: banyak jalan mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 30</li><li>0 ≤ m ≤ 2000</li><li>1 ≤ K ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "3 4 2\n1 2\n2 3\n1 3\n3 3\n", 'explanation' => 'Jalan dengan 2 sisi dari 1 ke 3: 1→2→3 dan 1→3→3.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $m, int $kMax) {
                $rows = [];
                for ($i = 0; $i < $m; $i++) {
                    $rows[] = mt_rand(1, $n).' '.mt_rand(1, $n);
                }

                return "$n $m ".mt_rand(1, $kMax)."\n".implode("\n", $rows).($m ? "\n" : '');
            };

            return [
                "1 1 1000000000000000000\n1 1\n", "2 0 5\n", "2 1 1\n1 2\n", $gen(4, 6, 10), $gen(10, 40, 1000), $gen(30, 2000, 1000000000000000000),
                $gen(30, 100, 1000000000000000000), $gen(25, 600, 1000000000), $gen(30, 31, 1000000000000000000),
            ];
        },
        'solve' => function (string $input) {
            $M = 1000000007;
            $lines = T::lines($input);
            [$n, $m, $K] = T::ints($lines[0]);
            $A = array_fill(0, $n, array_fill(0, $n, 0));
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $A[$u - 1][$v - 1]++;
            }
            // vektor baris: hanya baris simpul 1 yang dibutuhkan → v · A^K (vektor × matriks lebih murah)
            $mul = function ($X, $Y) use ($n, $M) {
                $Z = array_fill(0, $n, array_fill(0, $n, 0));
                for ($i = 0; $i < $n; $i++) {
                    for ($t = 0; $t < $n; $t++) {
                        $x = $X[$i][$t];
                        if ($x == 0) {
                            continue;
                        }
                        $row = $Y[$t];
                        for ($j = 0; $j < $n; $j++) {
                            $Z[$i][$j] = ($Z[$i][$j] + $x * $row[$j]) % $M;
                        }
                    }
                }

                return $Z;
            };
            $vec = array_fill(0, $n, 0);
            $vec[0] = 1;
            while ($K > 0) {
                if ($K & 1) {
                    $nv = array_fill(0, $n, 0);
                    for ($t = 0; $t < $n; $t++) {
                        if ($vec[$t] == 0) {
                            continue;
                        }
                        for ($j = 0; $j < $n; $j++) {
                            $nv[$j] = ($nv[$j] + $vec[$t] * $A[$t][$j]) % $M;
                        }
                    }
                    $vec = $nv;
                }
                $K >>= 1;
                if ($K > 0) {
                    $A = $mul($A, $A);
                }
            }

            return (string) $vec[$n - 1];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
typedef vector<vector<long long>> Mat;

int main() {
    int n, m;
    long long K;
    cin >> n >> m >> K;
    Mat A(n, vector<long long>(n, 0));     // A[u][v] = banyak sisi u → v
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        A[u - 1][v - 1]++;
    }
    // (A^K)[0][n-1]
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const [ns, ms, Ks] = readLine().trim().split(/\s+/);
const n = Number(ns), m = Number(ms), K = BigInt(Ks);
const A = Array.from({ length: n }, () => new Array(n).fill(0));
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  A[u - 1][v - 1]++;
}
// (A^K)[0][n-1]
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
n, m, K = int(data[0]), int(data[1]), int(data[2])
A = [[0] * n for _ in range(n)]
for i in range(m):
    A[int(data[3 + 2 * i]) - 1][int(data[4 + 2 * i]) - 1] += 1
# (A^K)[0][n-1]
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
typedef vector<vector<long long>> Mat;

Mat kali(const Mat& X, const Mat& Y) {
    int n = X.size();
    Mat Z(n, vector<long long>(n, 0));
    for (int i = 0; i < n; i++)
        for (int t = 0; t < n; t++) {
            if (!X[i][t]) continue;
            for (int j = 0; j < n; j++) Z[i][j] = (Z[i][j] + X[i][t] * Y[t][j]) % MOD;
        }
    return Z;
}

int main() {
    int n, m;
    long long K;
    cin >> n >> m >> K;
    Mat A(n, vector<long long>(n, 0));
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        A[u - 1][v - 1]++;
    }
    Mat R(n, vector<long long>(n, 0));
    for (int i = 0; i < n; i++) R[i][i] = 1;
    for (long long e = K; e > 0; e >>= 1) {          // R = A^K
        if (e & 1) R = kali(R, A);
        A = kali(A, A);
    }
    cout << R[0][n - 1] << '\n';                      // (A^K)[1][n] = banyak jalan
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const [ns, ms, Ks] = readLine().trim().split(/\s+/);
const n = Number(ns), m = Number(ms);
let K = BigInt(Ks);
let A = Array.from({ length: n }, () => new Array(n).fill(0));
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  A[u - 1][v - 1]++;
}
const kali = (X, Y) => {
  const Z = Array.from({ length: n }, () => new Array(n).fill(0));
  for (let i = 0; i < n; i++)
    for (let t = 0; t < n; t++) {
      const x = X[i][t];
      if (!x) continue;
      const row = Y[t], zi = Z[i];
      for (let j = 0; j < n; j++) zi[j] = (zi[j] + mul(x, row[j])) % MOD;
    }
  return Z;
};
let R = Array.from({ length: n }, (_, i) => Array.from({ length: n }, (_, j) => (i === j ? 1 : 0)));
while (K > 0n) {
  if (K & 1n) R = kali(R, A);
  K >>= 1n;
  if (K > 0n) A = kali(A, A);
}
console.log(String(R[0][n - 1]));
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7

def kali(X, Y):
    kolom = list(zip(*Y))
    return [[sum(a * b for a, b in zip(baris, k)) % MOD for k in kolom] for baris in X]

data = sys.stdin.buffer.read().split()
n, m, K = int(data[0]), int(data[1]), int(data[2])
A = [[0] * n for _ in range(n)]
for i in range(m):
    A[int(data[3 + 2 * i]) - 1][int(data[4 + 2 * i]) - 1] += 1
# cukup baris simpul 1: vektor × matriks lebih murah daripada matriks × matriks
v = [0] * n
v[0] = 1
while K:
    if K & 1:
        v = [sum(v[t] * A[t][j] for t in range(n)) % MOD for j in range(n)]
    K >>= 1
    if K:
        A = kali(A, A)
print(v[n - 1])
CODE,
        ],
        'editorial' => '<p>Misalkan A matriks ketetanggaan: A[u][v] = banyak sisi u → v. Banyak jalan sepanjang t dari u ke v adalah (A<sup>t</sup>)[u][v]: jalan sepanjang t + 1 = jalan sepanjang t ke suatu w, lalu satu sisi w → v, yang persis rumus perkalian matriks.</p>
<p>K sampai 10<sup>18</sup>, jadi hitung A<sup>K</sup> dengan kuadrat berulang: O(n³ log K) ≈ 27 000 · 120 operasi. Jawabannya (A<sup>K</sup>)[1][n].</p>
<p>Optimasi kecil: karena hanya baris simpul 1 yang dibutuhkan, "hasil" cukup disimpan sebagai vektor baris (perkalian vektor × matriks O(n²)); yang tetap dikuadratkan hanya A.</p>',
        'hints' => [
            'Berapa banyak jalan sepanjang 2 dari u ke v? Tuliskan sebagai jumlah atas simpul tengah w.',
            'Itu rumus perkalian matriks: banyak jalan sepanjang t = (A<sup>t</sup>)[u][v].',
            'Pangkatkan matriks ketetanggaan K kali dengan kuadrat berulang (modulo 10<sup>9</sup> + 7).',
        ],
    ],

    // ───────────────────────── Inklusi-Eksklusi ─────────────────────────
    [
        'slug' => 'tukar-kado',
        'lesson' => 'inklusi-eksklusi',
        'title' => 'Tukar Kado Tanpa Dapat Sendiri',
        'difficulty' => 'Mudah',
        'tags' => ['inklusi-eksklusi', 'derangement', 'rekurens'],
        'statement' => '<p>Ada <strong>n</strong> anak yang masing-masing membawa satu kado. Kado-kado dikumpulkan lalu dibagikan lagi sehingga setiap anak menerima tepat satu kado, dan <strong>tidak ada</strong> anak yang menerima kadonya sendiri.</p>
<p>Ada berapa cara membagikannya? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Satu bilangan <code>n</code>.</p>',
        'output_format' => '<p>Banyak cara mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n", 'explanation' => 'Untuk anak A, B, C: (B, C, A) dan (C, A, B). Dua cara.'],
            ['input' => "4\n", 'explanation' => 'Ada 9 cara.'],
        ],
        'tests' => function () {
            return ["1\n", "2\n", "5\n", "10\n", "20\n", "1000\n", "123456\n", "999999\n", "1000000\n"];
        },
        'solve' => function (string $input) {
            // Referensi: inklusi-eksklusi D(n) = Σ (-1)^i n!/i!  (dihitung mundur tanpa invers)
            $M = 1000000007;
            $n = (int) trim($input);
            // n!/i! untuk i = n, n-1, ..., 0
            $suku = 1;
            $hasil = ($n % 2 == 0) ? 1 : $M - 1;       // i = n: (-1)^n · 1
            for ($i = $n - 1; $i >= 0; $i--) {
                $suku = $suku * ($i + 1) % $M;          // n!/i! = n!/(i+1)! · (i+1)
                $hasil = ($i % 2 == 0) ? ($hasil + $suku) % $M : ($hasil - $suku + $M) % $M;
            }

            return (string) $hasil;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    int n;
    cin >> n;
    // D(1) = 0, D(2) = 1, D(n) = (n - 1)(D(n-1) + D(n-2))
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const n = readInts()[0];
// D(1) = 0, D(2) = 1, D(n) = (n - 1)(D(n-1) + D(n-2))
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7
n = int(input())
# D(1) = 0, D(2) = 1, D(n) = (n - 1)(D(n-1) + D(n-2))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    int n;
    cin >> n;
    long long a = 1, b = 0;                 // a = D(0), b = D(1)
    for (int i = 2; i <= n; i++) {
        long long c = (i - 1) * ((a + b) % MOD) % MOD;
        a = b;
        b = c;
    }
    cout << (n == 0 ? a : b) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const n = readInts()[0];
let a = 1, b = 0;                           // D(0), D(1)
for (let i = 2; i <= n; i++) {
  const c = ((i - 1) * ((a + b) % MOD)) % MOD;   // ≤ 10^6 · 10^9 = 10^15: tepat di double
  a = b;
  b = c;
}
console.log(String(b));
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7
n = int(input())
a, b = 1, 0
for i in range(2, n + 1):
    a, b = b, (i - 1) * (a + b) % MOD
print(b)
CODE,
        ],
        'editorial' => '<p>Ini <strong>derangement</strong>: permutasi tanpa titik tetap.</p>
<p><strong>Inklusi-eksklusi.</strong> Dari n! permutasi, buang yang punya titik tetap. Banyak permutasi yang <em>setidaknya</em> memuat i titik tetap tertentu adalah (n − i)!, dan ada C(n, i) cara memilih i itu. Jadi D(n) = Σ<sub>i</sub> (−1)<sup>i</sup> C(n, i)(n − i)! = n! Σ<sub>i</sub> (−1)<sup>i</sup>/i!.</p>
<p><strong>Rekurens.</strong> Lihat ke mana kado anak 1 pergi, misalnya ke anak k (n − 1 pilihan). Jika anak k menerima kado anak 1, sisa n − 2 anak membentuk derangement: D(n − 2). Jika tidak, "anak k tidak boleh menerima kado anak 1" berperan seperti "tidak boleh menerima kadonya sendiri" untuk n − 1 anak: D(n − 1). Maka D(n) = (n − 1)(D(n − 1) + D(n − 2)), O(n).</p>',
        'hints' => [
            'Hitung semua n! cara, lalu buang yang ada anak menerima kadonya sendiri. Bagaimana menghindari menghitung ganda?',
            'Inklusi-eksklusi atas himpunan anak yang menerima kadonya sendiri, atau cari rekurens dengan melihat ke mana kado anak 1 pergi.',
            'D(n) = (n − 1)(D(n − 1) + D(n − 2)) dengan D(1) = 0, D(2) = 1.',
        ],
    ],

    [
        'slug' => 'koprima-rentang',
        'lesson' => 'inklusi-eksklusi',
        'title' => 'Koprima di Rentang Raksasa',
        'difficulty' => 'Sedang',
        'tags' => ['inklusi-eksklusi', 'faktorisasi', 'gcd'],
        'statement' => '<p>Untuk setiap pertanyaan (N, M), hitung banyak bilangan bulat x dengan 1 ≤ x ≤ N dan <code>gcd(x, M) = 1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi <code>N M</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, banyak x yang koprima dengan M.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100</li><li>1 ≤ N ≤ 10<sup>18</sup></li><li>1 ≤ M ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n10 6\n12 12\n1000000000000000000 1\n", 'explanation' => 'Koprima dengan 6 di 1..10: 1, 5, 7 → 3. Koprima dengan 12 di 1..12: 1, 5, 7, 11 → 4. Semua bilangan koprima dengan 1.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $nMax, int $mMax) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $m = mt_rand(1, $mMax);
                    if ($i % 4 == 0) {
                        $m = [223092870, 510510, 9699690, 1000000000, 999999937, 2 * 3 * 5 * 7 * 11 * 13 * 17 * 19 * 23][mt_rand(0, 5)];
                    }
                    $rows[] = mt_rand(1, $nMax).' '.$m;
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return [
                "3\n1 1\n1 1000000000\n1000000000000000000 223092870\n", $gen(50, 100, 100), $gen(100, 1000000000000000000, 1000000000),
                $gen(100, 1000000, 1000000000), $gen(100, 1000000000000000000, 1000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                [$n, $m] = T::ints($lines[$i]);
                $pr = [];
                for ($d = 2; $d * $d <= $m; $d++) {
                    if ($m % $d == 0) {
                        $pr[] = $d;
                        while ($m % $d == 0) {
                            $m = intdiv($m, $d);
                        }
                    }
                }
                if ($m > 1) {
                    $pr[] = $m;
                }
                // rekursi inklusi-eksklusi (tanpa bitmask)
                $hasil = 0;
                $go = function (int $idx, int $kali, int $tanda) use (&$go, &$hasil, $pr, $n) {
                    if ($idx == count($pr)) {
                        $hasil += $tanda * intdiv($n, $kali);

                        return;
                    }
                    $go($idx + 1, $kali, $tanda);
                    $go($idx + 1, $kali * $pr[$idx], -$tanda);
                };
                $go(0, 1, 1);
                $out[] = $hasil;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int q;
    cin >> q;
    while (q--) {
        long long N, M;
        cin >> N >> M;
        // 1) faktor prima berbeda dari M   2) inklusi-eksklusi atas faktor-faktor itu
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// N sampai 10^18 → BigInt untuk pembagian
const q = Number(readLine());
for (let i = 0; i < q; i++) {
  const [Ns, Ms] = readLine().trim().split(/\s+/);
  const N = BigInt(Ns), M = Number(Ms);
  // 1) faktor prima berbeda dari M   2) inklusi-eksklusi atas faktor-faktor itu
}
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.read().split()
q = int(data[0])
# 1) faktor prima berbeda dari M   2) inklusi-eksklusi atas faktor-faktor itu
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int q;
    cin >> q;
    while (q--) {
        long long N, M;
        cin >> N >> M;
        vector<long long> p;                       // faktor prima berbeda (paling banyak 9)
        for (long long d = 2; d * d <= M; d++)
            if (M % d == 0) {
                p.push_back(d);
                while (M % d == 0) M /= d;
            }
        if (M > 1) p.push_back(M);

        int k = p.size();
        long long tidakKoprima = 0;               // habis dibagi salah satu faktor
        for (int mask = 1; mask < (1 << k); mask++) {
            long long kali = 1;
            for (int i = 0; i < k; i++)
                if (mask >> i & 1) kali *= p[i];   // ≤ M ≤ 10^9
            if (__builtin_popcount(mask) % 2) tidakKoprima += N / kali;
            else tidakKoprima -= N / kali;
        }
        cout << N - tidakKoprima << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const out = [];
for (let t = 0; t < q; t++) {
  const [Ns, Ms] = readLine().trim().split(/\s+/);
  const N = BigInt(Ns);
  let M = Number(Ms);
  const p = [];
  for (let d = 2; d * d <= M; d++)
    if (M % d === 0) {
      p.push(d);
      while (M % d === 0) M /= d;
    }
  if (M > 1) p.push(M);
  let tidak = 0n;
  for (let mask = 1; mask < 1 << p.length; mask++) {
    let kali = 1, bit = 0;
    for (let i = 0; i < p.length; i++) if ((mask >> i) & 1) { kali *= p[i]; bit++; }
    const c = N / BigInt(kali);
    tidak += bit % 2 ? c : -c;
  }
  out.push(String(N - tidak));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.read().split()
q = int(data[0])
out = []
for t in range(q):
    N, M = int(data[1 + 2 * t]), int(data[2 + 2 * t])
    p = []
    d = 2
    while d * d <= M:
        if M % d == 0:
            p.append(d)
            while M % d == 0:
                M //= d
        d += 1
    if M > 1:
        p.append(M)
    hasil = 0
    for mask in range(1 << len(p)):
        kali, bit = 1, 0
        for i in range(len(p)):
            if mask >> i & 1:
                kali *= p[i]
                bit += 1
        hasil += -(N // kali) if bit % 2 else N // kali
    out.append(hasil)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>x tidak koprima dengan M ⇔ x habis dibagi salah satu faktor prima M. Jadi faktorkan M (pembagian percobaan sampai √M ≈ 31 623), lalu hitung dengan <strong>inklusi-eksklusi</strong> banyak x ≤ N yang habis dibagi salah satu faktor, dan kurangkan dari N.</p>
<p>Faktor prima berbeda dari M ≤ 10<sup>9</sup> paling banyak 9 (2·3·5·…·23 ≈ 2,2·10<sup>8</sup>), jadi paling banyak 2<sup>9</sup> = 512 suku per pertanyaan. Hasil kali faktor ≤ M, tidak overflow; N sampai 10<sup>18</sup> tetap muat di <code>long long</code>.</p>
<p>Rumus setara: Σ<sub>d | M</sub> μ(d)·⌊N/d⌋ (fungsi Möbius, materi berikutnya).</p>',
        'hints' => [
            'x tidak koprima dengan M jika x habis dibagi suatu faktor prima M. Faktor prima berbeda dari M ≤ 10<sup>9</sup> paling banyak berapa?',
            'Faktorkan M dengan pembagian percobaan sampai √M, lalu inklusi-eksklusi atas faktor-faktornya.',
            'Jawaban = Σ over himpunan bagian S (−1)<sup>|S|</sup> · ⌊N / Π S⌋ (himpunan kosong memberi N).',
        ],
    ],

    [
        'slug' => 'bagi-hadiah',
        'lesson' => 'inklusi-eksklusi',
        'title' => 'Semua Anak Kebagian Hadiah',
        'difficulty' => 'Sedang',
        'tags' => ['inklusi-eksklusi', 'kombinatorika', 'modular'],
        'statement' => '<p>Ada <strong>n</strong> hadiah yang semuanya berbeda dan <strong>k</strong> anak. Setiap hadiah diberikan kepada tepat satu anak, dan <strong>setiap anak harus mendapat paling sedikit satu hadiah</strong>.</p>
<p>Ada berapa cara? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Satu baris berisi <code>n k</code>.</p>',
        'output_format' => '<p>Banyak cara mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 10<sup>9</sup></li><li>1 ≤ k ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "3 2\n", 'explanation' => '2³ = 8 cara tanpa syarat, dikurangi 2 cara di mana semua hadiah jatuh ke satu anak: 6.'],
            ['input' => "2 3\n", 'explanation' => 'Hanya 2 hadiah untuk 3 anak: mustahil semua kebagian.'],
        ],
        'tests' => function () {
            return ["1 1\n", "5 5\n", "10 3\n", "7 4\n", "1000000000 1\n", "1000000000 200000\n", "200000 200000\n", "199999 200000\n", "123456789 98765\n", "1000 999\n"];
        },
        'solve' => function (string $input) {
            $M = 1000000007;
            [$n, $k] = T::ints($input);
            if ($k > $n) {
                return '0';
            }
            $pw = function ($a, $e) use ($M) {
                $r = 1;
                $a %= $M;
                while ($e > 0) {
                    if ($e & 1) {
                        $r = $r * $a % $M;
                    }
                    $a = $a * $a % $M;
                    $e >>= 1;
                }

                return $r;
            };
            // C(k, i) dihitung berurutan: C(k, i) = C(k, i-1) · (k - i + 1) / i
            $inv = [0, 1];
            for ($i = 2; $i <= $k; $i++) {
                $inv[$i] = ($M - intdiv($M, $i)) * $inv[$M % $i] % $M;
            }
            $c = 1;
            $hasil = 0;
            for ($i = 0; $i <= $k; $i++) {
                if ($i > 0) {
                    $c = $c * ($k - $i + 1) % $M * $inv[$i] % $M;
                }
                $suku = $c * $pw($k - $i, $n) % $M;
                $hasil = $i % 2 ? ($hasil - $suku + $M) % $M : ($hasil + $suku) % $M;
            }

            return (string) $hasil;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    long long n;
    int k;
    cin >> n >> k;
    // semua k^n cara, kurangi yang ada anak tidak kebagian (inklusi-eksklusi)
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const [n, k] = readInts();
// semua k^n cara, kurangi yang ada anak tidak kebagian (inklusi-eksklusi)
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7
n, k = map(int, input().split())
# semua k^n cara, kurangi yang ada anak tidak kebagian (inklusi-eksklusi)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

long long pangkat(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}

int main() {
    long long n;
    int k;
    cin >> n >> k;
    vector<long long> fact(k + 1), inv(k + 1);
    fact[0] = 1;
    for (int i = 1; i <= k; i++) fact[i] = fact[i - 1] * i % MOD;
    inv[k] = pangkat(fact[k], MOD - 2);
    for (int i = k; i > 0; i--) inv[i - 1] = inv[i] * i % MOD;

    // Σ (-1)^i C(k, i) (k - i)^n : i anak tertentu dipaksa tidak kebagian
    long long hasil = 0;
    for (int i = 0; i <= k; i++) {
        long long c = fact[k] * inv[i] % MOD * inv[k - i] % MOD;
        long long suku = c * pangkat(k - i, n) % MOD;
        hasil = (i % 2 == 0) ? (hasil + suku) % MOD : (hasil - suku + MOD) % MOD;
    }
    cout << hasil << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {
  let r = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) r = mul(r, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return r;
};
const [n, k] = readInts();
const fact = [1];
for (let i = 1; i <= k; i++) fact.push(mul(fact[i - 1], i));
const inv = new Array(k + 1);
inv[k] = pangkat(fact[k], MOD - 2);
for (let i = k; i > 0; i--) inv[i - 1] = mul(inv[i], i);
let hasil = 0;
for (let i = 0; i <= k; i++) {
  const suku = mul(mul(mul(fact[k], inv[i]), inv[k - i]), pangkat(k - i, n));
  hasil = i % 2 === 0 ? (hasil + suku) % MOD : (hasil - suku + MOD) % MOD;
}
console.log(String(hasil));
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7
n, k = map(int, input().split())
fact = [1] * (k + 1)
for i in range(1, k + 1):
    fact[i] = fact[i - 1] * i % MOD
inv = [1] * (k + 1)
inv[k] = pow(fact[k], MOD - 2, MOD)
for i in range(k, 0, -1):
    inv[i - 1] = inv[i] * i % MOD
hasil = 0
for i in range(k + 1):
    suku = fact[k] * inv[i] % MOD * inv[k - i] % MOD * pow(k - i, n, MOD) % MOD
    hasil = (hasil - suku) % MOD if i % 2 else (hasil + suku) % MOD
print(hasil)
CODE,
        ],
        'editorial' => '<p>Tanpa syarat, setiap hadiah memilih salah satu dari k anak: k<sup>n</sup> cara. Buang yang ada anak tidak kebagian dengan <strong>inklusi-eksklusi</strong>: untuk himpunan i anak tertentu yang dipaksa tidak kebagian, semua hadiah jatuh ke k − i anak lain, (k − i)<sup>n</sup> cara, dan ada C(k, i) himpunan seperti itu:</p>
<p><code>jawaban = Σ<sub>i=0..k</sub> (−1)<sup>i</sup> · C(k, i) · (k − i)<sup>n</sup></code>.</p>
<p>Ini juga sama dengan k!·S(n, k) (Stirling jenis kedua). Butuh k + 1 perpangkatan cepat: O(k log n). Jika k &gt; n, jawabannya 0, dan rumusnya memang menghasilkan 0.</p>',
        'hints' => [
            'Tanpa syarat ada k<sup>n</sup> cara. Bagaimana membuang cara yang membuat suatu anak tidak kebagian?',
            'Jika i anak tertentu dipaksa tidak kebagian, ada (k − i)<sup>n</sup> cara. Pakai inklusi-eksklusi.',
            'Jawaban = Σ (−1)<sup>i</sup> C(k, i) (k − i)<sup>n</sup> modulo p.',
        ],
    ],

    // ───────────────────────── Catalan, Stirling & Burnside ─────────────────────────
    [
        'slug' => 'bagi-permen',
        'lesson' => 'kombinatorika',
        'title' => 'Membagi Permen dengan Jatah Minimum',
        'difficulty' => 'Mudah',
        'tags' => ['kombinatorika', 'bintang dan sekat'],
        'statement' => '<p>Ibu punya <strong>n</strong> permen identik untuk dibagikan habis kepada <strong>k</strong> anak. Anak ke-i harus mendapat <strong>paling sedikit a<sub>i</sub></strong> permen. Ada berapa cara membagi? Dua cara berbeda jika ada anak yang mendapat banyak permen berbeda. Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n k</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>k</sub></code>.</p>',
        'output_format' => '<p>Banyak cara mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>0 ≤ n ≤ 10<sup>6</sup></li><li>1 ≤ k ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "7 3\n0 0 0\n", 'explanation' => 'Bintang dan sekat: C(7 + 2, 2) = 36.'],
            ['input' => "10 3\n2 3 1\n", 'explanation' => 'Berikan jatah minimum dulu (6 permen), sisa 4 permen dibagi bebas: C(4 + 2, 2) = 15.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $k, int $aMax) {
                return "$n $k\n".T::join(T::arr($k, 0, $aMax))."\n";
            };

            return [
                "0 1\n0\n", "5 1\n6\n", "1000000 200000\n".T::join(array_fill(0, 200000, 0))."\n", $gen(20, 4, 3), $gen(1000000, 200000, 4),
                $gen(1000000, 1000, 1000), $gen(500, 100, 10), $gen(1000000, 3, 1000000),
            ];
        },
        'solve' => function (string $input) {
            $M = 1000000007;
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $sisa = $n - array_sum(T::ints($lines[1]));
            if ($sisa < 0) {
                return '0';
            }
            // C(sisa + k - 1, k - 1) dengan rumus perkalian C(N, r) = Π (N - r + i) / i
            $N = $sisa + $k - 1;
            $r = min($k - 1, $sisa);
            $pw = function ($a, $e) use ($M) {
                $x = 1;
                $a %= $M;
                while ($e > 0) {
                    if ($e & 1) {
                        $x = $x * $a % $M;
                    }
                    $a = $a * $a % $M;
                    $e >>= 1;
                }

                return $x;
            };
            $atas = 1;
            $bawah = 1;
            for ($i = 1; $i <= $r; $i++) {
                $atas = $atas * (($N - $r + $i) % $M) % $M;
                $bawah = $bawah * $i % $M;
            }

            return (string) ($atas * $pw($bawah, $M - 2) % $M);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    long long n;
    int k;
    cin >> n >> k;
    long long wajib = 0;
    for (int i = 0; i < k; i++) {
        long long a;
        cin >> a;
        wajib += a;
    }
    // berikan jatah minimum dulu, lalu bintang dan sekat untuk sisanya
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const [n, k] = readInts();
const a = readInts();
// berikan jatah minimum dulu, lalu bintang dan sekat untuk sisanya
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7
n, k = map(int, input().split())
a = list(map(int, input().split()))
# berikan jatah minimum dulu, lalu bintang dan sekat untuk sisanya
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

long long pangkat(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}

int main() {
    long long n;
    int k;
    cin >> n >> k;
    long long wajib = 0;
    for (int i = 0; i < k; i++) {
        long long a;
        cin >> a;
        wajib += a;
    }
    long long sisa = n - wajib;
    if (sisa < 0) {
        cout << 0 << '\n';
        return 0;
    }
    // C(sisa + k - 1, k - 1): sisa bintang, k - 1 sekat
    long long N = sisa + k - 1;
    vector<long long> fact(N + 1);
    fact[0] = 1;
    for (long long i = 1; i <= N; i++) fact[i] = fact[i - 1] * (i % MOD) % MOD;
    long long jawab = fact[N] * pangkat(fact[k - 1] * fact[sisa] % MOD, MOD - 2) % MOD;
    cout << jawab << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {
  let r = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) r = mul(r, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return r;
};
const [n, k] = readInts();
const a = readInts();
let wajib = 0;
for (const x of a) wajib += x;
const sisa = n - wajib;
if (sisa < 0) {
  console.log("0");
} else {
  const N = sisa + k - 1;
  const fact = [1];
  for (let i = 1; i <= N; i++) fact.push(mul(fact[i - 1], i));
  console.log(String(mul(fact[N], pangkat(mul(fact[k - 1], fact[sisa]), MOD - 2))));
}
CODE,
            'python' => <<<'CODE'
MOD = 10**9 + 7
n, k = map(int, input().split())
a = list(map(int, input().split()))
sisa = n - sum(a)
if sisa < 0:
    print(0)
else:
    N = sisa + k - 1
    fact = [1] * (N + 1)
    for i in range(1, N + 1):
        fact[i] = fact[i - 1] * i % MOD
    print(fact[N] * pow(fact[k - 1] * fact[sisa] % MOD, MOD - 2, MOD) % MOD)
CODE,
        ],
        'editorial' => '<p>Berikan dulu setiap anak jatah minimumnya: tersisa s = n − Σ a<sub>i</sub> permen (jika negatif, jawabannya 0). Sekarang soalnya: bagi s permen identik ke k anak tanpa batas bawah, yaitu banyak solusi x<sub>1</sub> + … + x<sub>k</sub> = s dengan x<sub>i</sub> ≥ 0.</p>
<p><strong>Bintang dan sekat:</strong> susun s bintang dan k − 1 sekat dalam satu baris; sekat membagi bintang menjadi k kelompok. Banyak susunan C(s + k − 1, k − 1). Faktorial sampai s + k − 1 ≤ 1,2·10<sup>6</sup> lalu satu invers Fermat.</p>
<p>Hati-hati: Σ a<sub>i</sub> bisa mencapai 2·10<sup>11</sup>, jadi simpan di <code>long long</code>.</p>',
        'hints' => [
            'Berikan jatah minimum lebih dulu. Berapa permen yang tersisa untuk dibagi bebas?',
            'Membagi s permen identik ke k anak tanpa syarat = menyusun s bintang dan k − 1 sekat.',
            'Jawabannya C(s + k − 1, k − 1) mod p, atau 0 jika s < 0.',
        ],
    ],

    [
        'slug' => 'jalan-diagonal',
        'lesson' => 'kombinatorika',
        'title' => 'Jalan di Bawah Diagonal',
        'difficulty' => 'Sedang',
        'tags' => ['kombinatorika', 'prinsip pantulan', 'catalan'],
        'statement' => '<p>Seekor semut mulai di titik (0, 0) dan setiap langkah bergerak satu satuan ke <strong>kanan</strong> (x + 1) atau ke <strong>atas</strong> (y + 1). Ia ingin sampai di (a, b) dengan b ≤ a, tetapi tidak pernah boleh berada di titik dengan <strong>y &gt; x</strong> (di atas garis diagonal).</p>
<p>Ada berapa jalur? Jawab Q pertanyaan, modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, banyak jalur mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>0 ≤ b ≤ a ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n2 2\n3 1\n5 0\n", 'explanation' => 'Ke (2, 2): KKAA dan KAKA (K = kanan, A = atas) → 2 = Catalan C₂. Ke (3, 1): AKKK dilarang, sisanya KAKK, KKAK, KKKA → 3. Ke (5, 0): hanya satu jalur.'],
        ],
        'tests' => function () {
            $gen = function (int $q, int $hi) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $a = mt_rand(0, $hi);
                    $rows[] = $a.' '.($i % 3 == 0 ? $a : mt_rand(0, $a));
                }

                return "$q\n".implode("\n", $rows)."\n";
            };

            return ["3\n0 0\n1000000 1000000\n1000000 0\n", $gen(100, 8), $gen(1000, 1000), $gen(100000, 1000000)];
        },
        'solve' => function (string $input) {
            // Referensi: rumus Catalan umum (a - b + 1)/(a + 1) · C(a + b, b)
            $M = 1000000007;
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $qs = [];
            $N = 1;
            for ($i = 1; $i <= $q; $i++) {
                $qs[] = T::ints($lines[$i]);
                $N = max($N, $qs[$i - 1][0] + $qs[$i - 1][1] + 1);
            }
            $f = [1];
            for ($i = 1; $i <= $N; $i++) {
                $f[$i] = $f[$i - 1] * $i % $M;
            }
            $pw = function ($a, $e) use ($M) {
                $r = 1;
                $a %= $M;
                while ($e > 0) {
                    if ($e & 1) {
                        $r = $r * $a % $M;
                    }
                    $a = $a * $a % $M;
                    $e >>= 1;
                }

                return $r;
            };
            $inv = array_fill(0, $N + 1, 0);
            $inv[$N] = $pw($f[$N], $M - 2);
            for ($i = $N; $i > 0; $i--) {
                $inv[$i - 1] = $inv[$i] * $i % $M;
            }
            $out = [];
            foreach ($qs as [$a, $b]) {
                $c = $f[$a + $b] * $inv[$a] % $M * $inv[$b] % $M;
                // (a - b + 1) / (a + 1) = (a - b + 1) · a! / (a + 1)!
                $out[] = $c * ($a - $b + 1) % $M * $f[$a] % $M * $inv[$a + 1] % $M;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
const int MAKS = 2000001;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    // semua jalur C(a + b, b), kurangi jalur "buruk" (prinsip pantulan)
    int q;
    cin >> q;
    while (q--) {
        int a, b;
        cin >> a >> b;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
// semua jalur C(a + b, b), kurangi jalur "buruk" (prinsip pantulan)
const q = readInts()[0];
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
# semua jalur C(a + b, b), kurangi jalur "buruk" (prinsip pantulan)
data = sys.stdin.buffer.read().split()
q = int(data[0])
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;
const int MAKS = 2000001;
long long fact[MAKS + 1], inv[MAKS + 1];

long long pangkat(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}
long long C(int n, int k) {
    if (k < 0 || k > n) return 0;
    return fact[n] * inv[k] % MOD * inv[n - k] % MOD;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    fact[0] = 1;
    for (int i = 1; i <= MAKS; i++) fact[i] = fact[i - 1] * i % MOD;
    inv[MAKS] = pangkat(fact[MAKS], MOD - 2);
    for (int i = MAKS; i > 0; i--) inv[i - 1] = inv[i] * i % MOD;

    int q;
    cin >> q;
    while (q--) {
        int a, b;
        cin >> a >> b;
        // jalur buruk menyentuh y = x + 1; cerminkan bagian awalnya → jalur ke (b - 1, a + 1)
        cout << (C(a + b, b) - C(a + b, b - 1) + MOD) % MOD << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {
  let r = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) r = mul(r, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return r;
};
const q = readInts()[0];
const qs = [];
let N = 1;
for (let i = 0; i < q; i++) {
  const p = readInts();
  qs.push(p);
  N = Math.max(N, p[0] + p[1]);
}
const fact = new Array(N + 1), inv = new Array(N + 1);
fact[0] = 1;
for (let i = 1; i <= N; i++) fact[i] = mul(fact[i - 1], i);
inv[N] = pangkat(fact[N], MOD - 2);
for (let i = N; i > 0; i--) inv[i - 1] = mul(inv[i], i);
const C = (n, k) => (k < 0 || k > n ? 0 : mul(mul(fact[n], inv[k]), inv[n - k]));
console.log(qs.map(([a, b]) => (C(a + b, b) - C(a + b, b - 1) + MOD) % MOD).join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.buffer.read().split()
q = int(data[0])
qs = [(int(data[1 + 2 * i]), int(data[2 + 2 * i])) for i in range(q)]
N = max(a + b for a, b in qs) + 1
fact = [1] * (N + 1)
for i in range(1, N + 1):
    fact[i] = fact[i - 1] * i % MOD
inv = [1] * (N + 1)
inv[N] = pow(fact[N], MOD - 2, MOD)
for i in range(N, 0, -1):
    inv[i - 1] = inv[i] * i % MOD

def C(n, k):
    return 0 if k < 0 or k > n else fact[n] * inv[k] % MOD * inv[n - k] % MOD

print("\n".join(str((C(a + b, b) - C(a + b, b - 1)) % MOD) for a, b in qs))
CODE,
        ],
        'editorial' => '<p>Tanpa larangan, jalur ke (a, b) memilih posisi b langkah "atas" di antara a + b langkah: C(a + b, b).</p>
<p><strong>Prinsip pantulan.</strong> Jalur buruk pasti pernah menyentuh garis y = x + 1. Ambil titik sentuh pertama, lalu cerminkan bagian jalur <em>sebelum</em> titik itu terhadap garis y = x + 1 (tukar langkah kanan dan atas). Titik awalnya berubah dari (0, 0) menjadi (−1, 1). Ini memasangkan jalur buruk satu-satu dengan semua jalur dari (−1, 1) ke (a, b), yaitu a + 1 langkah kanan dan b − 1 langkah atas: C(a + b, b − 1).</p>
<p>Jawaban: C(a + b, b) − C(a + b, b − 1). Untuk a = b, ini bilangan Catalan C<sub>a</sub>. Faktorial sampai 2·10<sup>6</sup> dipra-hitung sekali.</p>',
        'hints' => [
            'Tanpa larangan ada C(a + b, b) jalur. Hitung jalur yang "buruk" lalu kurangkan.',
            'Jalur buruk pasti menyentuh garis y = x + 1. Cerminkan bagian jalur sebelum sentuhan pertama terhadap garis itu.',
            'Jalur buruk berpasangan dengan jalur dari (−1, 1) ke (a, b): C(a + b, b − 1).',
        ],
    ],

    [
        'slug' => 'kalung-manik',
        'lesson' => 'kombinatorika',
        'title' => 'Kalung Manik Berputar',
        'difficulty' => 'Sulit',
        'tags' => ['burnside', 'phi euler', 'kombinatorika'],
        'statement' => '<p>Sebuah kalung terdiri dari <strong>n</strong> manik yang tersusun melingkar. Setiap manik diberi salah satu dari <strong>k</strong> warna. Dua kalung dianggap <strong>sama</strong> jika salah satunya bisa diputar sehingga menjadi kalung yang lain (membalik kalung tidak diperbolehkan).</p>
<p>Ada berapa kalung berbeda? Jawab T pertanyaan, modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap dari T baris berikutnya berisi <code>n k</code>.</p>',
        'output_format' => '<p>Untuk setiap pertanyaan, banyak kalung mod 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 100</li><li>1 ≤ n, k ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "3\n4 2\n6 2\n3 3\n", 'explanation' => '4 manik 2 warna: (16 + 2 + 4 + 2)/4 = 6. 6 manik 2 warna: 14. 3 manik 3 warna: (27 + 3 + 3)/3 = 11.'],
        ],
        'tests' => function () {
            $gen = function (int $t, int $nMax, int $kMax) {
                $rows = [];
                for ($i = 0; $i < $t; $i++) {
                    $n = mt_rand(1, $nMax);
                    if ($i % 5 == 0) {
                        $n = [735134400, 997920000, 999999937, 536870912, 1000000000][mt_rand(0, 4)];
                        $n = min($n, $nMax);
                    }
                    $rows[] = $n.' '.mt_rand(1, $kMax);
                }

                return "$t\n".implode("\n", $rows)."\n";
            };

            return ["3\n1 1\n1 1000000000\n1000000000 1\n", $gen(50, 12, 4), $gen(100, 1000, 1000000000), $gen(100, 1000000000, 1000000000), $gen(100, 1000000000, 2)];
        },
        'solve' => function (string $input) {
            // Referensi: Burnside dengan pengelompokan gcd(i, n) = d lewat pembagi d dan φ(n/d)
            $M = 1000000007;
            $pw = function ($a, $e) use ($M) {
                $r = 1;
                $a %= $M;
                while ($e > 0) {
                    if ($e & 1) {
                        $r = $r * $a % $M;
                    }
                    $a = $a * $a % $M;
                    $e >>= 1;
                }

                return $r;
            };
            $phi = function (int $x) {
                $r = $x;
                for ($p = 2; $p * $p <= $x; $p++) {
                    if ($x % $p == 0) {
                        while ($x % $p == 0) {
                            $x = intdiv($x, $p);
                        }
                        $r -= intdiv($r, $p);
                    }
                }
                if ($x > 1) {
                    $r -= intdiv($r, $x);
                }

                return $r;
            };
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $t; $i++) {
                [$n, $k] = T::ints($lines[$i]);
                $sum = 0;
                for ($d = 1; $d * $d <= $n; $d++) {
                    if ($n % $d) {
                        continue;
                    }
                    $e = intdiv($n, $d);
                    $sum = ($sum + $phi($e) % $M * $pw($k, $d)) % $M;
                    if ($e != $d) {
                        $sum = ($sum + $phi($d) % $M * $pw($k, $e)) % $M;
                    }
                }
                $out[] = $sum * $pw($n, $M - 2) % $M;
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    int t;
    cin >> t;
    while (t--) {
        long long n, k;
        cin >> n >> k;
        // Burnside: (1/n) Σ_{i=0}^{n-1} k^gcd(i, n). n terlalu besar untuk loop langsung.
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const t = readInts()[0];
for (let i = 0; i < t; i++) {
  const [n, k] = readInts();
  // Burnside: (1/n) Σ_{i=0}^{n-1} k^gcd(i, n). n terlalu besar untuk loop langsung.
}
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.read().split()
t = int(data[0])
# Burnside: (1/n) Σ_{i=0}^{n-1} k^gcd(i, n). n terlalu besar untuk loop langsung.
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

long long pangkat(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}

int main() {
    int t;
    cin >> t;
    while (t--) {
        long long n, k;
        cin >> n >> k;
        // faktor prima n
        vector<long long> p;
        long long x = n;
        for (long long d = 2; d * d <= x; d++)
            if (x % d == 0) {
                p.push_back(d);
                while (x % d == 0) x /= d;
            }
        if (x > 1) p.push_back(x);
        auto phi = [&](long long e) {            // e membagi n, jadi faktor primanya ada di p
            long long r = e;
            for (long long q : p)
                if (e % q == 0) r -= r / q;
            return r;
        };
        // banyak i dengan gcd(i, n) = d adalah φ(n/d)
        long long jumlah = 0;
        for (long long d = 1; d * d <= n; d++) {
            if (n % d) continue;
            jumlah = (jumlah + phi(n / d) % MOD * pangkat(k, d)) % MOD;
            if (d != n / d) jumlah = (jumlah + phi(d) % MOD * pangkat(k, n / d)) % MOD;
        }
        cout << jumlah * pangkat(n, MOD - 2) % MOD << '\n';
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 1000000007;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {
  let r = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) r = mul(r, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return r;
};
const t = readInts()[0];
const out = [];
for (let i = 0; i < t; i++) {
  const [n, k] = readInts();
  const p = [];
  let x = n;
  for (let d = 2; d * d <= x; d++)
    if (x % d === 0) {
      p.push(d);
      while (x % d === 0) x /= d;
    }
  if (x > 1) p.push(x);
  const phi = (e) => {
    let r = e;
    for (const q of p) if (e % q === 0) r -= r / q;
    return r;
  };
  let jumlah = 0;
  for (let d = 1; d * d <= n; d++) {
    if (n % d) continue;
    jumlah = (jumlah + mul(phi(n / d) % MOD, pangkat(k, d))) % MOD;
    if (d !== n / d) jumlah = (jumlah + mul(phi(d) % MOD, pangkat(k, n / d))) % MOD;
  }
  out.push(mul(jumlah, pangkat(n, MOD - 2)));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

MOD = 10**9 + 7
data = sys.stdin.read().split()
t = int(data[0])
out = []
for i in range(t):
    n, k = int(data[1 + 2 * i]), int(data[2 + 2 * i])
    p, x, d = [], n, 2
    while d * d <= x:
        if x % d == 0:
            p.append(d)
            while x % d == 0:
                x //= d
        d += 1
    if x > 1:
        p.append(x)

    def phi(e):
        r = e
        for q in p:
            if e % q == 0:
                r -= r // q
        return r

    jumlah, d = 0, 1
    while d * d <= n:
        if n % d == 0:
            jumlah += phi(n // d) * pow(k, d, MOD)
            if d != n // d:
                jumlah += phi(d) * pow(k, n // d, MOD)
        d += 1
    out.append(jumlah % MOD * pow(n, MOD - 2, MOD) % MOD)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p><strong>Lemma Burnside:</strong> banyak kalung berbeda = rata-rata, atas semua n putaran, dari banyak pewarnaan yang tidak berubah oleh putaran itu. Putaran sejauh i posisi memecah n posisi menjadi gcd(i, n) siklus, dan pewarnaan tetap harus seragam di setiap siklus: k<sup>gcd(i, n)</sup> pewarnaan. Jadi jawaban = (1/n)·Σ<sub>i=0..n−1</sub> k<sup>gcd(i, n)</sup>.</p>
<p>n sampai 10<sup>9</sup> terlalu besar untuk dijumlah langsung. Kelompokkan i menurut d = gcd(i, n): banyak i dengan gcd tepat d adalah φ(n/d). Maka jawaban = (1/n)·Σ<sub>d | n</sub> φ(n/d)·k<sup>d</sup>.</p>
<p>Pembagi n ≤ 10<sup>9</sup> paling banyak 1 344; enumerasi lewat d ≤ √n. φ dihitung dari faktor prima n. Pembagian dengan n memakai invers Fermat (n &lt; p, jadi selalu ada).</p>',
        'hints' => [
            'Pakai Lemma Burnside: rata-rata banyak pewarnaan yang tidak berubah oleh setiap putaran.',
            'Putaran sejauh i membuat gcd(i, n) siklus, jadi ada k<sup>gcd(i, n)</sup> pewarnaan yang tidak berubah.',
            'Kelompokkan menurut d = gcd(i, n): ada φ(n/d) nilai i. Jawaban = (1/n) Σ<sub>d|n</sub> φ(n/d) k<sup>d</sup>.',
        ],
    ],

    // ───────────────────────── Möbius & Phi ─────────────────────────
    [
        'slug' => 'phi-banyak',
        'lesson' => 'mobius',
        'title' => 'Fungsi Phi untuk Banyak Bilangan',
        'difficulty' => 'Mudah',
        'tags' => ['phi euler', 'saringan'],
        'statement' => '<p>Untuk setiap bilangan x yang diberikan, cetak <code>φ(x)</code>: banyak bilangan bulat 1 ≤ y ≤ x dengan gcd(x, y) = 1.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Baris kedua berisi <code>x<sub>1</sub> … x<sub>Q</sub></code>.</p>',
        'output_format' => '<p>Q bilangan dalam satu baris, dipisah spasi: φ(x<sub>1</sub>) … φ(x<sub>Q</sub>).</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ x<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1 7 12 36 1000000\n", 'explanation' => 'φ(1) = 1, φ(7) = 6, φ(12) = 4 (1, 5, 7, 11), φ(36) = 36 · ½ · ⅔ = 12, φ(10<sup>6</sup>) = 10<sup>6</sup> · ½ · ⅘ = 400 000.'],
        ],
        'tests' => function () {
            return [
                "3\n1 2 999983\n", "100\n".T::join(range(1, 100))."\n", "200000\n".T::join(T::arr(200000, 1, 1000000))."\n",
                "200000\n".T::join(T::arr(200000, 999000, 1000000))."\n",
            ];
        },
        'solve' => function (string $input) {
            // Referensi: φ per bilangan lewat faktorisasi dengan spf
            $lines = T::lines($input);
            $xs = T::ints($lines[1]);
            $M = max($xs);
            $spf = array_fill(0, $M + 1, 0);
            for ($i = 2; $i <= $M; $i++) {
                if ($spf[$i] == 0) {
                    for ($j = $i; $j <= $M; $j += $i) {
                        if ($spf[$j] == 0) {
                            $spf[$j] = $i;
                        }
                    }
                }
            }
            $out = [];
            foreach ($xs as $x) {
                $r = $x;
                while ($x > 1) {
                    $p = $spf[$x];
                    $r -= intdiv($r, $p);
                    while ($x % $p == 0) {
                        $x = intdiv($x, $p);
                    }
                }
                $out[] = $r;
            }

            return implode(' ', $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000000;
int phi[MAKS + 1];

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    // saring phi: mulai phi[x] = x, untuk setiap prima p kalikan kelipatannya dengan (1 - 1/p)
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MAKS = 1000000;
// saring phi: mulai phi[x] = x, untuk setiap prima p kalikan kelipatannya dengan (1 - 1/p)
const q = readInts()[0];
const xs = readInts();
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
q = int(data[0])
xs = list(map(int, data[1:1 + q]))
# saring phi: mulai phi[x] = x, untuk setiap prima p kalikan kelipatannya dengan (1 - 1/p)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 1000000;
int phi[MAKS + 1];

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    for (int i = 0; i <= MAKS; i++) phi[i] = i;
    for (int p = 2; p <= MAKS; p++)
        if (phi[p] == p)                          // belum disentuh: p prima
            for (int x = p; x <= MAKS; x += p) phi[x] -= phi[x] / p;   // × (1 - 1/p)

    int q;
    cin >> q;
    string out;
    for (int i = 0; i < q; i++) {
        int x;
        cin >> x;
        if (i) out += ' ';
        out += to_string(phi[x]);
    }
    cout << out << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MAKS = 1000000;
const phi = new Int32Array(MAKS + 1);
for (let i = 0; i <= MAKS; i++) phi[i] = i;
for (let p = 2; p <= MAKS; p++)
  if (phi[p] === p) for (let x = p; x <= MAKS; x += p) phi[x] -= phi[x] / p;
const q = readInts()[0];
const xs = readInts();
console.log(xs.map((x) => phi[x]).join(" "));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
q = int(data[0])
xs = list(map(int, data[1:1 + q]))
M = max(xs)
phi = list(range(M + 1))
for p in range(2, M + 1):
    if phi[p] == p:                          # p prima
        phi[p::p] = [v - v // p for v in phi[p::p]]
print(" ".join(str(phi[x]) for x in xs))
CODE,
        ],
        'editorial' => '<p>φ(x) = x · Π<sub>p | x</sub> (1 − 1/p), atas semua prima berbeda p yang membagi x.</p>
<p>Untuk banyak x sekaligus, saring seperti Eratosthenes: mulai dari phi[x] = x. Untuk setiap prima p (dikenali karena phi[p] masih bernilai p, belum pernah disentuh), kalikan phi setiap kelipatan p dengan (1 − 1/p), yaitu <code>phi[x] -= phi[x] / p</code>. Pembagian ini selalu tepat karena phi[x] pada saat itu masih habis dibagi p.</p>
<p>Total O(M log log M), lalu setiap pertanyaan O(1).</p>',
        'hints' => [
            'φ(x) = x · Π (1 − 1/p) atas faktor prima p dari x.',
            'Daripada memfaktorkan setiap x, sapu semua prima p dan perbarui semua kelipatannya sekaligus.',
            'phi[x] = x di awal; untuk setiap prima p: phi[kelipatan p] −= phi[kelipatan p] / p.',
        ],
    ],

    [
        'slug' => 'pasangan-koprima-array',
        'lesson' => 'mobius',
        'title' => 'Pasangan Koprima di Array',
        'difficulty' => 'Sedang',
        'tags' => ['mobius', 'inklusi-eksklusi', 'gcd'],
        'statement' => '<p>Diberikan n bilangan bulat positif. Ada berapa pasangan indeks (i, j) dengan i &lt; j sehingga <code>gcd(a<sub>i</sub>, a<sub>j</sub>) = 1</code>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>n</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak pasangan koprima.</p>',
        'constraints' => '<ul><li>1 ≤ n ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "5\n2 3 4 5 6\n", 'explanation' => 'Pasangan koprima: (2,3), (2,5), (3,4), (3,5), (4,5), (5,6). Ada 6.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1\n1\n", "3\n1 1 1\n", "4\n6 10 15 30\n", $gen(30, 30), $gen(2000, 1000), $gen(200000, 1000000), $gen(200000, 100),
                "200000\n".T::join(array_fill(0, 200000, 735134400 % 1000000 ?: 2))."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $M = max($a);
            $freq = array_fill(0, $M + 1, 0);
            foreach ($a as $x) {
                $freq[$x]++;
            }
            // mu dengan saringan sederhana
            $mu = array_fill(0, $M + 1, 1);
            $komp = array_fill(0, $M + 1, false);
            for ($p = 2; $p <= $M; $p++) {
                if ($komp[$p]) {
                    continue;
                }
                for ($x = $p; $x <= $M; $x += $p) {
                    if ($x > $p) {
                        $komp[$x] = true;
                    }
                    $mu[$x] = -$mu[$x];
                }
                if ($p <= intdiv($M, $p)) {
                    for ($x = $p * $p; $x <= $M; $x += $p * $p) {
                        $mu[$x] = 0;
                    }
                }
            }
            $ans = 0;
            for ($d = 1; $d <= $M; $d++) {
                if ($mu[$d] == 0) {
                    continue;
                }
                $c = 0;
                for ($x = $d; $x <= $M; $x += $d) {
                    $c += $freq[$x];
                }
                $ans += $mu[$d] * intdiv($c * ($c - 1), 2);
            }

            return (string) $ans;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n;
    cin >> n;
    // c[d] = banyak elemen kelipatan d; jawaban = Σ μ(d) · C(c[d], 2)
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = readInts();
// c[d] = banyak elemen kelipatan d; jawaban = Σ μ(d) · C(c[d], 2)
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + n]))
# c[d] = banyak elemen kelipatan d; jawaban = Σ μ(d) · C(c[d], 2)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int M = 1000000;
int freq[M + 1], mu[M + 1];
bool komposit[M + 1];

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n;
    cin >> n;
    for (int i = 0; i < n; i++) {
        int x;
        cin >> x;
        freq[x]++;
    }
    // μ dengan saringan linear
    vector<int> prima;
    mu[1] = 1;
    for (int i = 2; i <= M; i++) {
        if (!komposit[i]) { prima.push_back(i); mu[i] = -1; }
        for (int p : prima) {
            if ((long long)i * p > M) break;
            komposit[i * p] = true;
            if (i % p == 0) { mu[i * p] = 0; break; }
            mu[i * p] = -mu[i];
        }
    }
    long long jawab = 0;
    for (int d = 1; d <= M; d++) {
        if (!mu[d]) continue;
        long long c = 0;                       // banyak elemen kelipatan d
        for (int x = d; x <= M; x += d) c += freq[x];
        jawab += mu[d] * (c * (c - 1) / 2);    // pasangan yang keduanya kelipatan d
    }
    cout << jawab << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = readInts()[0];
const a = readInts();
let M = 1;
for (const x of a) if (x > M) M = x;
const freq = new Int32Array(M + 1);
for (const x of a) freq[x]++;
const mu = new Int8Array(M + 1).fill(1);
const komposit = new Uint8Array(M + 1);
for (let p = 2; p <= M; p++) {
  if (komposit[p]) continue;
  for (let x = p; x <= M; x += p) {
    if (x > p) komposit[x] = 1;
    mu[x] = -mu[x];
  }
  for (let x = p * p; x <= M; x += p * p) mu[x] = 0;
}
let jawab = 0;
for (let d = 1; d <= M; d++) {
  if (!mu[d]) continue;
  let c = 0;
  for (let x = d; x <= M; x += d) c += freq[x];
  jawab += mu[d] * ((c * (c - 1)) / 2);
}
console.log(String(jawab));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + n]))
M = max(a)
freq = [0] * (M + 1)
for x in a:
    freq[x] += 1
mu = [1] * (M + 1)
prima = bytearray([1]) * (M + 1)
for p in range(2, M + 1):
    if prima[p]:
        prima[p * p::p] = bytes(len(range(p * p, M + 1, p)))
        mu[p::p] = [-v for v in mu[p::p]]
        mu[p * p::p * p] = [0] * len(range(p * p, M + 1, p * p))
jawab = 0
for d in range(1, M + 1):
    if mu[d]:
        c = sum(freq[d::d])
        jawab += mu[d] * (c * (c - 1) // 2)
print(jawab)
CODE,
        ],
        'editorial' => '<p>Mengecek semua pasangan O(n²) terlalu lambat. Pakai identitas [gcd = 1] = Σ<sub>d | gcd</sub> μ(d):</p>
<p><code>banyak pasangan koprima = Σ<sub>d</sub> μ(d) · (banyak pasangan yang keduanya kelipatan d) = Σ<sub>d</sub> μ(d) · C(c[d], 2)</code>,</p>
<p>dengan c[d] = banyak elemen kelipatan d. Ini inklusi-eksklusi atas semua prima sekaligus: kurangi pasangan yang sama-sama genap, sama-sama kelipatan 3, dst., tambah kembali kelipatan 6, dan seterusnya.</p>
<p>c[d] untuk semua d: jumlahkan frekuensi d, 2d, 3d, … → O(M log M). μ dari saringan linear. Jawaban bisa mencapai 2·10<sup>10</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Menghitung pasangan dengan gcd = 1 langsung sulit, tetapi menghitung pasangan yang keduanya kelipatan d mudah.',
            'c[d] = banyak elemen kelipatan d; banyak pasangan kelipatan d = c[d](c[d] − 1)/2.',
            'Gabungkan dengan fungsi Möbius: jawaban = Σ μ(d) · C(c[d], 2).',
        ],
    ],

    [
        'slug' => 'jumlah-fpb',
        'lesson' => 'mobius',
        'title' => 'Jumlah FPB Semua Pasangan',
        'difficulty' => 'Sulit',
        'tags' => ['phi euler', 'teori bilangan', 'penjumlahan pembagi'],
        'statement' => '<p>Hitung</p>
<p><code>Σ<sub>i=1</sub><sup>N</sup> Σ<sub>j=1</sub><sup>N</sup> gcd(i, j)</code>.</p>',
        'input_format' => '<p>Satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Satu bilangan: jumlah tersebut (tanpa modulo).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "3\n", 'explanation' => 'gcd untuk (i, j) di [1..3]²: 1 1 1 / 1 2 1 / 1 1 3, jumlahnya 12.'],
        ],
        'tests' => function () {
            return ["1\n", "2\n", "10\n", "100\n", "12345\n", "999999\n", "1000000\n", "720720\n"];
        },
        'solve' => function (string $input) {
            // Referensi: f(g) = banyak pasangan dengan gcd tepat g, dihitung mundur
            $N = (int) trim($input);
            $f = array_fill(0, $N + 2, 0);
            $total = 0;
            for ($g = $N; $g >= 1; $g--) {
                $q = intdiv($N, $g);
                $v = $q * $q;
                for ($m = 2 * $g; $m <= $N; $m += $g) {
                    $v -= $f[$m];
                }
                $f[$g] = $v;
                $total += $g * $v;
            }

            return (string) $total;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int N;
    cin >> N;
    // gcd(i, j) = Σ_{d | gcd(i, j)} φ(d)
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const N = readInts()[0];
// gcd(i, j) = Σ_{d | gcd(i, j)} φ(d)
CODE,
            'python' => <<<'CODE'
N = int(input())
# gcd(i, j) = Σ_{d | gcd(i, j)} φ(d)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int N;
    cin >> N;
    vector<int> phi(N + 1);
    for (int i = 0; i <= N; i++) phi[i] = i;
    for (int p = 2; p <= N; p++)
        if (phi[p] == p)
            for (int x = p; x <= N; x += p) phi[x] -= phi[x] / p;

    // Σ_i Σ_j gcd(i, j) = Σ_i Σ_j Σ_{d | i, d | j} φ(d) = Σ_d φ(d) · ⌊N/d⌋²
    long long total = 0;
    for (int d = 1; d <= N; d++) {
        long long q = N / d;
        total += (long long)phi[d] * q * q;
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const N = readInts()[0];
const phi = new Int32Array(N + 1);
for (let i = 0; i <= N; i++) phi[i] = i;
for (let p = 2; p <= N; p++)
  if (phi[p] === p) for (let x = p; x <= N; x += p) phi[x] -= phi[x] / p;
let total = 0;                          // < 10^13: masih tepat di double
for (let d = 1; d <= N; d++) {
  const q = Math.floor(N / d);
  total += phi[d] * q * q;
}
console.log(String(total));
CODE,
            'python' => <<<'CODE'
N = int(input())
phi = list(range(N + 1))
for p in range(2, N + 1):
    if phi[p] == p:
        phi[p::p] = [v - v // p for v in phi[p::p]]
print(sum(phi[d] * (N // d) ** 2 for d in range(1, N + 1)))
CODE,
        ],
        'editorial' => '<p><strong>Identitas Gauss:</strong> n = Σ<sub>d | n</sub> φ(d). Terapkan ke n = gcd(i, j): pembagi gcd(i, j) adalah tepat bilangan d yang membagi i dan j. Maka</p>
<p><code>Σ<sub>i,j</sub> gcd(i, j) = Σ<sub>i,j</sub> Σ<sub>d|i, d|j</sub> φ(d) = Σ<sub>d</sub> φ(d) · ⌊N/d⌋²</code></p>
<p>karena untuk d tetap, ada ⌊N/d⌋ pilihan i kelipatan d dan sebanyak itu pula untuk j. Saring φ sampai N (O(N log log N)) lalu jumlahkan (O(N)).</p>
<p>Cara lain: f(g) = banyak pasangan dengan gcd <em>tepat</em> g = ⌊N/g⌋² − Σ<sub>m ≥ 2</sub> f(mg), dihitung dari g besar ke kecil (O(N log N)). Jawaban sampai sekitar 10<sup>13</sup>: <code>long long</code>.</p>',
        'hints' => [
            'Coba tulis gcd(i, j) sebagai jumlah sesuatu atas pembagi-pembaginya.',
            'n = Σ<sub>d|n</sub> φ(d). Tukar urutan penjumlahan: untuk setiap d, berapa pasangan (i, j) yang keduanya kelipatan d?',
            'Jawaban = Σ<sub>d=1..N</sub> φ(d) · ⌊N/d⌋². Saring φ sampai N.',
        ],
    ],

    // ───────────────────────── Basis XOR ─────────────────────────
    [
        'slug' => 'xor-terbentuk',
        'lesson' => 'xor-basis',
        'title' => 'Bisakah XOR-nya Terbentuk?',
        'difficulty' => 'Sedang',
        'tags' => ['basis xor', 'bitmask', 'aljabar linear'],
        'statement' => '<p>Diberikan n bilangan. Sebuah nilai x dikatakan <strong>terbentuk</strong> jika ada himpunan bagian (boleh kosong) dari bilangan-bilangan itu yang XOR semua anggotanya sama dengan x.</p>
<p>Cetak dulu banyak nilai berbeda yang bisa terbentuk, lalu jawab q pertanyaan: apakah x terbentuk?</p>',
        'input_format' => '<p>Baris pertama berisi <code>n q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>n</sub></code>. Setiap dari q baris berikutnya berisi satu bilangan x.</p>',
        'output_format' => '<p>Baris pertama: banyak nilai berbeda yang terbentuk. Lalu q baris berisi <code>YA</code> atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ n, q ≤ 100 000</li><li>0 ≤ a<sub>i</sub>, x &lt; 2<sup>30</sup></li></ul>',
        'samples' => [
            ['input' => "3 3\n1 2 3\n3\n4\n0\n", 'explanation' => '3 = 1 ⊕ 2 tidak menambah apa-apa: basisnya {2, 1}, rank 2, jadi 4 nilai (0, 1, 2, 3). 3 terbentuk, 4 tidak, 0 dari himpunan kosong.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $q, int $bits, bool $kecil) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = $kecil ? (1 << mt_rand(0, $bits - 1)) | (1 << mt_rand(0, $bits - 1)) : mt_rand(0, (1 << $bits) - 1);
                }
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $rows[] = mt_rand(0, (1 << $bits) - 1);
                }

                return "$n $q\n".T::join($a)."\n".implode("\n", $rows)."\n";
            };

            return [
                "1 2\n0\n0\n1\n", $gen(5, 20, 4, true), $gen(100, 1000, 12, true), $gen(100000, 100000, 30, false), $gen(100000, 100000, 30, true),
                $gen(20, 100000, 30, false), $gen(1000, 1000, 10, false),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $b = array_fill(0, 30, 0);
            foreach (T::ints($lines[1]) as $x) {
                for ($k = 29; $k >= 0 && $x; $k--) {
                    if (($x >> $k) & 1) {
                        if (! $b[$k]) {
                            $b[$k] = $x;
                            break;
                        }
                        $x ^= $b[$k];
                    }
                }
            }
            $rank = count(array_filter($b));
            $out = [1 << $rank];
            for ($i = 0; $i < $q; $i++) {
                $x = (int) $lines[2 + $i];
                for ($k = 29; $k >= 0; $k--) {
                    if ((($x >> $k) & 1) && $b[$k]) {
                        $x ^= $b[$k];
                    }
                }
                $out[] = $x == 0 ? 'YA' : 'TIDAK';
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n, q;
    cin >> n >> q;
    int basis[30] = {};
    // sisipkan setiap a_i ke basis
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
const basis = new Array(30).fill(0);
// sisipkan setiap a_i ke basis
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
a = list(map(int, data[2:2 + n]))
basis = [0] * 30
# sisipkan setiap a_i ke basis
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int basis[30];

void sisip(int x) {
    for (int b = 29; b >= 0; b--) {
        if (!(x >> b & 1)) continue;
        if (!basis[b]) { basis[b] = x; return; }
        x ^= basis[b];
    }
}
bool terbentuk(int x) {
    for (int b = 29; b >= 0; b--)
        if (x >> b & 1) {
            if (!basis[b]) return false;   // bit b tidak bisa dihapus
            x ^= basis[b];
        }
    return true;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n, q;
    cin >> n >> q;
    for (int i = 0; i < n; i++) {
        int x;
        cin >> x;
        sisip(x);
    }
    int rank = 0;
    for (int b = 0; b < 30; b++) if (basis[b]) rank++;
    string out = to_string(1LL << rank) + "\n";
    while (q--) {
        int x;
        cin >> x;
        out += terbentuk(x) ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
const basis = new Array(30).fill(0);
for (let x of a) {
  for (let b = 29; b >= 0; b--) {
    if (!((x >> b) & 1)) continue;
    if (!basis[b]) { basis[b] = x; break; }
    x ^= basis[b];
  }
}
const rank = basis.filter((v) => v).length;
const out = [String(2 ** rank)];
for (let i = 0; i < q; i++) {
  let x = readInts()[0];
  for (let b = 29; b >= 0; b--) if (((x >> b) & 1) && basis[b]) x ^= basis[b];
  out.push(x === 0 ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
basis = [0] * 30
for v in data[2:2 + n]:
    x = int(v)
    for b in range(29, -1, -1):
        if x >> b & 1:
            if not basis[b]:
                basis[b] = x
                break
            x ^= basis[b]
rank = sum(1 for v in basis if v)
urut = [(b, basis[b]) for b in range(29, -1, -1) if basis[b]]
out = [str(1 << rank)]
for v in data[2 + n:2 + n + q]:
    x = int(v)
    for b, w in urut:
        if x >> b & 1:
            x ^= w
    out.append("YA" if x == 0 else "TIDAK")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Bangun basis XOR dari semua a<sub>i</sub> (eliminasi Gauss pada bit, O(30) per bilangan). Ruang yang dibentuk basis sama dengan himpunan semua XOR subset.</p>
<ul><li><strong>Banyak nilai:</strong> setiap kombinasi wakil basis memberi nilai yang berbeda, jadi ada tepat 2<sup>rank</sup> nilai (termasuk 0 dari himpunan kosong).</li>
<li><strong>Apakah x terbentuk:</strong> dari bit tertinggi, jika bit b dari x menyala, hapus dengan wakil bit b. Jika wakilnya tidak ada, bit itu tidak bisa dihapus → TIDAK. Jika x habis menjadi 0 → YA.</li></ul>
<p>Total O(30·(n + q)).</p>',
        'hints' => [
            'Himpunan semua XOR subset tertutup terhadap XOR. Cukup simpan "basis" kecilnya.',
            'Sisipkan setiap bilangan ke basis per bit tertinggi. Banyak nilai berbeda = 2<sup>banyak basis</sup>.',
            'Untuk mengecek x, coba hapus bit-bitnya dari tertinggi dengan wakil basis. Terbentuk ⇔ x menjadi 0.',
        ],
    ],

    [
        'slug' => 'xor-ke-k',
        'lesson' => 'xor-basis',
        'title' => 'Nilai XOR Terkecil ke-k',
        'difficulty' => 'Sulit',
        'tags' => ['basis xor', 'basis tereduksi'],
        'statement' => '<p>Diberikan n bilangan. Kumpulkan semua nilai XOR dari himpunan bagian (termasuk himpunan kosong yang bernilai 0), buang yang sama, lalu urutkan naik. Jawab q pertanyaan: berapa nilai ke-k dalam urutan itu (k mulai dari 1)? Jika nilainya kurang dari k, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n q</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>n</sub></code>. Baris ketiga berisi q bilangan k.</p>',
        'output_format' => '<p>q bilangan dalam satu baris, dipisah spasi.</p>',
        'constraints' => '<ul><li>1 ≤ n, q ≤ 100 000</li><li>0 ≤ a<sub>i</sub> &lt; 2<sup>30</sup></li><li>1 ≤ k ≤ 2<sup>31</sup></li></ul>',
        'samples' => [
            ['input' => "3 5\n5 6 3\n1 2 3 4 5\n", 'explanation' => 'Nilai XOR berbeda: 0, 3, 5, 6 (karena 5 ⊕ 6 = 3). Nilai ke-5 tidak ada.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $q, int $bits, int $kMax) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(0, (1 << $bits) - 1);
                }

                return "$n $q\n".T::join($a)."\n".T::join(T::arr($q, 1, $kMax))."\n";
            };

            return [
                "1 3\n0\n1 2 2147483648\n", $gen(4, 20, 4, 20), $gen(10, 500, 8, 300), $gen(100000, 100000, 30, 2147483647),
                $gen(100000, 100000, 30, 1100000000), $gen(25, 100000, 30, 40000000), $gen(1000, 1000, 12, 5000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $b = array_fill(0, 30, 0);
            foreach (T::ints($lines[1]) as $x) {
                for ($k = 29; $k >= 0 && $x; $k--) {
                    if (($x >> $k) & 1) {
                        if (! $b[$k]) {
                            $b[$k] = $x;
                            break;
                        }
                        $x ^= $b[$k];
                    }
                }
            }
            // reduksi
            for ($k = 29; $k >= 0; $k--) {
                if (! $b[$k]) {
                    continue;
                }
                for ($j = $k - 1; $j >= 0; $j--) {
                    if ($b[$j] && (($b[$k] >> $j) & 1)) {
                        $b[$k] ^= $b[$j];
                    }
                }
            }
            $v = [];
            for ($k = 0; $k < 30; $k++) {
                if ($b[$k]) {
                    $v[] = $b[$k];
                }
            }
            $out = [];
            foreach (T::ints($lines[2]) as $kk) {
                $idx = $kk - 1;
                if ($idx >= (1 << count($v))) {
                    $out[] = -1;

                    continue;
                }
                $x = 0;
                foreach ($v as $i => $w) {
                    if (($idx >> $i) & 1) {
                        $x ^= $w;
                    }
                }
                $out[] = $x;
            }

            return implode(' ', $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n, q;
    cin >> n >> q;
    long long basis[30] = {};
    // 1) bangun basis  2) reduksi (bit tertinggi tiap wakil hanya ada di wakil itu)
    // 3) nilai ke-k: baca k-1 dalam biner
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
const ks = readInts();
// 1) bangun basis  2) reduksi (bit tertinggi tiap wakil hanya ada di wakil itu)
// 3) nilai ke-k: baca k-1 dalam biner
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
a = list(map(int, data[2:2 + n]))
ks = list(map(int, data[2 + n:2 + n + q]))
# 1) bangun basis  2) reduksi  3) nilai ke-k: baca k-1 dalam biner
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n, q;
    cin >> n >> q;
    long long basis[30] = {};
    for (int i = 0; i < n; i++) {
        long long x;
        cin >> x;
        for (int b = 29; b >= 0; b--) {
            if (!(x >> b & 1)) continue;
            if (!basis[b]) { basis[b] = x; break; }
            x ^= basis[b];
        }
    }
    // basis tereduksi: hapus bit b dari semua wakil yang lebih tinggi
    for (int b = 0; b < 30; b++)
        if (basis[b])
            for (int c = b + 1; c < 30; c++)
                if (basis[c] >> b & 1) basis[c] ^= basis[b];
    vector<long long> v;                     // wakil dari bit rendah ke tinggi
    for (int b = 0; b < 30; b++) if (basis[b]) v.push_back(basis[b]);

    string out;
    for (int i = 0; i < q; i++) {
        long long k;
        cin >> k;
        long long idx = k - 1, x = 0;
        if (idx >= (1LL << v.size())) x = -1;
        else
            for (size_t t = 0; t < v.size(); t++)
                if (idx >> t & 1) x ^= v[t];
        if (i) out += ' ';
        out += to_string(x);
    }
    cout << out << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const a = readInts();
const ks = readInts();
const basis = new Array(30).fill(0);
for (let x of a) {
  for (let b = 29; b >= 0; b--) {
    if (!((x >> b) & 1)) continue;
    if (!basis[b]) { basis[b] = x; break; }
    x ^= basis[b];
  }
}
for (let b = 0; b < 30; b++)
  if (basis[b]) for (let c = b + 1; c < 30; c++) if ((basis[c] >> b) & 1) basis[c] ^= basis[b];
const v = basis.filter((x) => x);
const out = ks.map((k) => {
  const idx = k - 1;
  if (idx >= 2 ** v.length) return -1;
  let x = 0;
  for (let t = 0; t < v.length; t++) if (Math.floor(idx / 2 ** t) % 2 === 1) x ^= v[t];
  return x;
});
console.log(out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
basis = [0] * 30
for s in data[2:2 + n]:
    x = int(s)
    for b in range(29, -1, -1):
        if x >> b & 1:
            if not basis[b]:
                basis[b] = x
                break
            x ^= basis[b]
for b in range(30):
    if basis[b]:
        for c in range(b + 1, 30):
            if basis[c] >> b & 1:
                basis[c] ^= basis[b]
v = [w for w in basis if w]
out = []
for s in data[2 + n:2 + n + q]:
    idx = int(s) - 1
    if idx >= 1 << len(v):
        out.append(-1)
        continue
    x = 0
    for t, w in enumerate(v):
        if idx >> t & 1:
            x ^= w
    out.append(x)
print(" ".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Bangun basis XOR, lalu ubah menjadi <strong>basis tereduksi</strong>: untuk setiap wakil dengan bit tertinggi b, bit b tidak muncul di wakil lain mana pun (hapus dengan XOR).</p>
<p>Misalkan wakil-wakilnya v<sub>0</sub> &lt; v<sub>1</sub> &lt; … &lt; v<sub>r−1</sub> (urut bit tertinggi). Setiap nilai XOR adalah XOR dari subset wakil, dan karena bit tertinggi setiap wakil "milik sendiri", memilih v<sub>t</sub> sama dengan menyalakan bit tertinggi ke-t. Akibatnya urutan nilai mengikuti urutan biner pilihan: nilai ke-k (mulai dari 1) adalah XOR dari v<sub>t</sub> untuk setiap bit t yang menyala di k − 1.</p>
<p>Ada 2<sup>r</sup> nilai berbeda; jika k &gt; 2<sup>r</sup> cetak −1. O(30·(n + q)).</p>',
        'hints' => [
            'Semua nilai XOR subset sama dengan semua nilai XOR dari basisnya. Ada 2<sup>rank</sup> nilai berbeda.',
            'Reduksi basis sehingga bit tertinggi setiap wakil tidak muncul di wakil lain. Apa akibatnya pada urutan nilai?',
            'Nilai ke-k = XOR wakil-wakil yang dipilih oleh bit-bit dari k − 1 (wakil terkecil untuk bit 0).',
        ],
    ],

    // ───────────────────────── Miller-Rabin & Pollard Rho ─────────────────────────
    [
        'slug' => 'uji-prima-besar',
        'lesson' => 'pollard-rho',
        'title' => 'Uji Prima Bilangan Besar',
        'difficulty' => 'Sedang',
        'tags' => ['miller-rabin', 'bilangan prima', 'modular'],
        'statement' => '<p>Untuk setiap bilangan n yang diberikan, tentukan apakah n prima.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi satu bilangan n.</p>',
        'output_format' => '<p>Untuk setiap n, <code>PRIMA</code> atau <code>BUKAN</code>.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 1000</li><li>1 ≤ n ≤ 10<sup>15</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1\n561\n1000000007\n999999999999989\n341550071728321\n", 'explanation' => '1 bukan prima. 561 = 3·11·17 (bilangan Carmichael, menipu uji Fermat). 999999999999989 prima. 341550071728321 komposit walaupun lolos uji kuat untuk basis 2 sampai 13.'],
        ],
        'tests' => function () {
            $mulmod = function (int $a, int $b, int $m) {
                $r = 0;
                for ($sh = 48; $sh >= 0; $sh -= 12) {
                    $r = ($r * 4096 + (($a >> $sh) & 4095) * $b) % $m;
                }

                return $r;
            };
            $prima = function (int $n) use ($mulmod) {
                if ($n < 2) {
                    return false;
                }
                foreach ([2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37] as $p) {
                    if ($n % $p == 0) {
                        return $n == $p;
                    }
                }
                $d = $n - 1;
                $s = 0;
                while ($d % 2 == 0) {
                    $d = intdiv($d, 2);
                    $s++;
                }
                foreach ([2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37] as $a) {
                    $x = 1;
                    $b = $a;
                    for ($e = $d; $e > 0; $e >>= 1) {
                        if ($e & 1) {
                            $x = $mulmod($x, $b, $n);
                        }
                        $b = $mulmod($b, $b, $n);
                    }
                    if ($x == 1 || $x == $n - 1) {
                        continue;
                    }
                    $ok = false;
                    for ($r = 1; $r < $s; $r++) {
                        $x = $mulmod($x, $x, $n);
                        if ($x == $n - 1) {
                            $ok = true;
                            break;
                        }
                    }
                    if (! $ok) {
                        return false;
                    }
                }

                return true;
            };
            $acakPrima = function (int $lo, int $hi) use ($prima) {
                do {
                    $x = mt_rand($lo, $hi) | 1;
                } while (! $prima($x));

                return $x;
            };
            $istimewa = [2047, 1373653, 25326001, 3215031751, 2152302898747, 3474749660383, 341550071728321, 561, 1105, 1729, 41041, 825265, 321197185, 5394826801, 232250619601, 9746347772161, 2, 3, 4, 9, 1, 999999999999989, 999999999999999, 1000000000000000];
            $campur = [];
            for ($i = 0; $i < 1000; $i++) {
                $j = $i % 4;
                if ($j == 0) {
                    $campur[] = $acakPrima(100000000000000, 1000000000000000);
                } elseif ($j == 1) {
                    $p = $acakPrima(10000000, 31000000);
                    $campur[] = $p * $acakPrima(10000000, intdiv(1000000000000000, $p));
                } elseif ($j == 2) {
                    $p = $acakPrima(1000000, 31622776);
                    $campur[] = $p * $p;
                } else {
                    $campur[] = mt_rand(1, 1000000000000000);
                }
            }
            $kecil = range(1, 1000);

            return [
                count($istimewa)."\n".implode("\n", $istimewa)."\n", "1000\n".implode("\n", $kecil)."\n", "1000\n".implode("\n", $campur)."\n",
                "1000\n".implode("\n", array_map(fn () => $acakPrima(999000000000000, 1000000000000000), range(1, 1000)))."\n",
            ];
        },
        'solve' => function (string $input) {
            $mulmod = function (int $a, int $b, int $m) {
                // a, b < m ≤ 10^15: proses a per 12 bit agar tidak pernah melewati 9,2·10^18
                $r = 0;
                for ($sh = 48; $sh >= 0; $sh -= 12) {
                    $r = ($r * 4096 + (($a >> $sh) & 4095) * $b) % $m;
                }

                return $r;
            };
            $prima = function (int $n) use ($mulmod) {
                if ($n < 2) {
                    return false;
                }
                $basis = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37];
                foreach ($basis as $p) {
                    if ($n % $p == 0) {
                        return $n == $p;
                    }
                }
                $d = $n - 1;
                $s = 0;
                while ($d % 2 == 0) {
                    $d = intdiv($d, 2);
                    $s++;
                }
                foreach ($basis as $a) {
                    $x = 1;
                    $b = $a;
                    for ($e = $d; $e > 0; $e >>= 1) {
                        if ($e & 1) {
                            $x = $mulmod($x, $b, $n);
                        }
                        $b = $mulmod($b, $b, $n);
                    }
                    if ($x == 1 || $x == $n - 1) {
                        continue;
                    }
                    $ok = false;
                    for ($r = 1; $r < $s; $r++) {
                        $x = $mulmod($x, $x, $n);
                        if ($x == $n - 1) {
                            $ok = true;
                            break;
                        }
                    }
                    if (! $ok) {
                        return false;
                    }
                }

                return true;
            };
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $out[] = $prima((int) $lines[$i]) ? 'PRIMA' : 'BUKAN';
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

__extension__ typedef unsigned __int128 u128;
typedef unsigned long long ull;

ull mulmod(ull a, ull b, ull m) { return (ull)((u128)a * b % m); }

int main() {
    int q;
    cin >> q;
    while (q--) {
        ull n;
        cin >> n;
        // Miller-Rabin dengan basis 2, 3, 5, ..., 37
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// n sampai 10^15: hasil kali dua sisa sampai 10^30 → pakai BigInt
const q = Number(readLine());
for (let i = 0; i < q; i++) {
  const n = BigInt(readLine().trim());
  // Miller-Rabin dengan basis 2, 3, 5, ..., 37
}
CODE,
            'python' => <<<'CODE'
import sys

data = sys.stdin.read().split()
q = int(data[0])
# Miller-Rabin dengan basis 2, 3, 5, ..., 37 (pow(a, d, n) bawaan sudah cepat)
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

__extension__ typedef unsigned __int128 u128;
typedef unsigned long long ull;

ull mulmod(ull a, ull b, ull m) { return (ull)((u128)a * b % m); }
ull powmod(ull a, ull e, ull m) {
    ull r = 1;
    a %= m;
    while (e) {
        if (e & 1) r = mulmod(r, a, m);
        a = mulmod(a, a, m);
        e >>= 1;
    }
    return r;
}

bool prima(ull n) {
    if (n < 2) return false;
    const ull basis[] = {2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37};
    for (ull p : basis)
        if (n % p == 0) return n == p;
    ull d = n - 1;
    int s = 0;
    while (d % 2 == 0) { d /= 2; s++; }
    for (ull a : basis) {
        ull x = powmod(a, d, n);
        if (x == 1 || x == n - 1) continue;
        bool saksi = true;
        for (int r = 1; r < s && saksi; r++) {
            x = mulmod(x, x, n);
            if (x == n - 1) saksi = false;
        }
        if (saksi) return false;            // a membuktikan n komposit
    }
    return true;
}

int main() {
    int q;
    cin >> q;
    string out;
    while (q--) {
        ull n;
        cin >> n;
        out += prima(n) ? "PRIMA\n" : "BUKAN\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const BASIS = [2n, 3n, 5n, 7n, 11n, 13n, 17n, 19n, 23n, 29n, 31n, 37n];
const powmod = (a, e, m) => {
  let r = 1n;
  a %= m;
  while (e > 0n) {
    if (e & 1n) r = (r * a) % m;
    a = (a * a) % m;
    e >>= 1n;
  }
  return r;
};
const prima = (n) => {
  if (n < 2n) return false;
  for (const p of BASIS) if (n % p === 0n) return n === p;
  let d = n - 1n, s = 0;
  while (d % 2n === 0n) { d /= 2n; s++; }
  for (const a of BASIS) {
    let x = powmod(a, d, n);
    if (x === 1n || x === n - 1n) continue;
    let saksi = true;
    for (let r = 1; r < s && saksi; r++) {
      x = (x * x) % n;
      if (x === n - 1n) saksi = false;
    }
    if (saksi) return false;
  }
  return true;
};
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) out.push(prima(BigInt(readLine().trim())) ? "PRIMA" : "BUKAN");
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys

BASIS = (2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37)

def prima(n):
    if n < 2:
        return False
    for p in BASIS:
        if n % p == 0:
            return n == p
    d, s = n - 1, 0
    while d % 2 == 0:
        d //= 2
        s += 1
    for a in BASIS:
        x = pow(a, d, n)
        if x == 1 or x == n - 1:
            continue
        for _ in range(s - 1):
            x = x * x % n
            if x == n - 1:
                break
        else:
            return False
    return True

data = sys.stdin.read().split()
q = int(data[0])
print("\n".join("PRIMA" if prima(int(v)) else "BUKAN" for v in data[1:1 + q]))
CODE,
        ],
        'editorial' => '<p>Pembagian percobaan sampai √n ≈ 3·10<sup>7</sup> untuk 1000 bilangan terlalu lambat. Pakai <strong>Miller-Rabin</strong>: tulis n − 1 = d·2<sup>s</sup>; untuk basis a, barisan a<sup>d</sup>, a<sup>2d</sup>, …, a<sup>2<sup>s</sup>d</sup> modulo n prima harus diawali 1 atau memuat n − 1. Jika tidak, n pasti komposit.</p>
<p>Dengan 12 basis prima pertama (2 sampai 37), uji ini <strong>deterministik</strong> untuk n &lt; 3,3·10<sup>24</sup>. Contoh pada soal memuat bilangan Carmichael (menipu uji Fermat) dan <em>strong pseudoprime</em> yang lolos sebagian basis: hanya kombinasi semua basis yang aman.</p>
<p>Hasil kali dua sisa sampai 10<sup>30</sup>: di C++ pakai <code>unsigned __int128</code>, di JavaScript <code>BigInt</code>. Setiap bilangan butuh ±12·50 perkalian.</p>',
        'hints' => [
            'Pembagian percobaan sampai √n terlalu lambat untuk 1000 bilangan sebesar 10<sup>15</sup>.',
            'Uji Fermat saja tertipu bilangan Carmichael (561, 1105, …). Miller-Rabin memeriksa juga akar kuadrat dari 1.',
            'Basis {2, 3, 5, …, 37} sudah deterministik untuk rentang ini. Perkalian modulo butuh 128 bit.',
        ],
    ],

    [
        'slug' => 'faktor-raksasa',
        'lesson' => 'pollard-rho',
        'title' => 'Faktorisasi Bilangan Raksasa',
        'difficulty' => 'Sulit',
        'tags' => ['pollard rho', 'miller-rabin', 'faktorisasi'],
        'statement' => '<p>Faktorkan setiap bilangan n yang diberikan menjadi bilangan prima.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Setiap dari Q baris berikutnya berisi satu bilangan n.</p>',
        'output_format' => '<p>Untuk setiap n, satu baris berisi faktor-faktor primanya terurut naik, diulang sesuai pangkatnya, dipisah spasi.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100</li><li>2 ≤ n ≤ 10<sup>15</sup></li></ul>',
        'samples' => [
            ['input' => "3\n360\n999999999999989\n999962000357\n", 'explanation' => '360 = 2·2·2·3·3·5. 999999999999989 prima. 999962000357 = 999979 · 999983, dua prima besar: pembagian percobaan butuh hampir 10<sup>6</sup> langkah, Pollard Rho jauh lebih sedikit.'],
        ],
        'tests' => function () {
            $mulmod = function (int $a, int $b, int $m) {
                $r = 0;
                for ($sh = 48; $sh >= 0; $sh -= 12) {
                    $r = ($r * 4096 + (($a >> $sh) & 4095) * $b) % $m;
                }

                return $r;
            };
            $prima = function (int $n) use ($mulmod) {
                if ($n < 2) {
                    return false;
                }
                foreach ([2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37] as $p) {
                    if ($n % $p == 0) {
                        return $n == $p;
                    }
                }
                $d = $n - 1;
                $s = 0;
                while ($d % 2 == 0) {
                    $d = intdiv($d, 2);
                    $s++;
                }
                foreach ([2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37] as $a) {
                    $x = 1;
                    $b = $a;
                    for ($e = $d; $e > 0; $e >>= 1) {
                        if ($e & 1) {
                            $x = $mulmod($x, $b, $n);
                        }
                        $b = $mulmod($b, $b, $n);
                    }
                    if ($x == 1 || $x == $n - 1) {
                        continue;
                    }
                    $ok = false;
                    for ($r = 1; $r < $s; $r++) {
                        $x = $mulmod($x, $x, $n);
                        if ($x == $n - 1) {
                            $ok = true;
                            break;
                        }
                    }
                    if (! $ok) {
                        return false;
                    }
                }

                return true;
            };
            $acakPrima = function (int $lo, int $hi) use ($prima) {
                do {
                    $x = mt_rand($lo, $hi) | 1;
                } while (! $prima($x));

                return $x;
            };
            $semi = function () use ($acakPrima) {
                $p = $acakPrima(10000000, 31622776);

                return $p * $acakPrima(10000000, intdiv(1000000000000000, $p));
            };
            $campur = [];
            for ($i = 0; $i < 100; $i++) {
                $j = $i % 5;
                if ($j == 0) {
                    $campur[] = $semi();
                } elseif ($j == 1) {
                    $campur[] = $acakPrima(100000000000000, 1000000000000000);
                } elseif ($j == 2) {
                    $p = $acakPrima(1000, 100000);
                    $campur[] = $p * $p * $acakPrima(1000, intdiv(1000000000000000, $p * $p));
                } elseif ($j == 3) {
                    $campur[] = mt_rand(2, 1000000000000000);
                } else {
                    $p = $acakPrima(100000, 1000000);
                    $campur[] = $p * $p;
                }
            }
            $semua = [];
            for ($i = 0; $i < 100; $i++) {
                $semua[] = $semi();
            }

            return [
                "5\n2\n4\n1000000000000000\n562949953421312\n999999999999989\n", "100\n".implode("\n", range(2, 101))."\n",
                "100\n".implode("\n", $campur)."\n", "100\n".implode("\n", $semua)."\n",
            ];
        },
        'solve' => function (string $input) {
            $mulmod = function (int $a, int $b, int $m) {
                $r = 0;
                for ($sh = 48; $sh >= 0; $sh -= 12) {
                    $r = ($r * 4096 + (($a >> $sh) & 4095) * $b) % $m;
                }

                return $r;
            };
            $gcd = function (int $a, int $b) {
                while ($b) {
                    [$a, $b] = [$b, $a % $b];
                }

                return $a;
            };
            $prima = function (int $n) use ($mulmod) {
                if ($n < 2) {
                    return false;
                }
                $basis = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37];
                foreach ($basis as $p) {
                    if ($n % $p == 0) {
                        return $n == $p;
                    }
                }
                $d = $n - 1;
                $s = 0;
                while ($d % 2 == 0) {
                    $d = intdiv($d, 2);
                    $s++;
                }
                foreach ($basis as $a) {
                    $x = 1;
                    $b = $a;
                    for ($e = $d; $e > 0; $e >>= 1) {
                        if ($e & 1) {
                            $x = $mulmod($x, $b, $n);
                        }
                        $b = $mulmod($b, $b, $n);
                    }
                    if ($x == 1 || $x == $n - 1) {
                        continue;
                    }
                    $ok = false;
                    for ($r = 1; $r < $s; $r++) {
                        $x = $mulmod($x, $x, $n);
                        if ($x == $n - 1) {
                            $ok = true;
                            break;
                        }
                    }
                    if (! $ok) {
                        return false;
                    }
                }

                return true;
            };
            // Brent: satu evaluasi f per langkah, gcd dikumpulkan per 64 langkah
            $rho = function (int $n) use ($mulmod, $gcd) {
                if ($n % 2 == 0) {
                    return 2;
                }
                for ($c = 1; ; $c++) {
                    $y = 2;
                    $r = 1;
                    $q = 1;
                    $g = 1;
                    $x = 2;
                    $ys = 2;
                    while ($g == 1) {
                        $x = $y;
                        for ($i = 0; $i < $r; $i++) {
                            $y = ($mulmod($y, $y, $n) + $c) % $n;
                        }
                        $k = 0;
                        while ($k < $r && $g == 1) {
                            $ys = $y;
                            $lim = min(64, $r - $k);
                            for ($i = 0; $i < $lim; $i++) {
                                $y = ($mulmod($y, $y, $n) + $c) % $n;
                                $q = $mulmod($q, abs($x - $y), $n);
                            }
                            $g = $gcd($q, $n);
                            $k += 64;
                        }
                        $r *= 2;
                    }
                    if ($g == $n) {
                        do {
                            $ys = ($mulmod($ys, $ys, $n) + $c) % $n;
                            $g = $gcd(abs($x - $ys), $n);
                        } while ($g == 1);
                    }
                    if ($g != $n) {
                        return $g;
                    }
                }
            };
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $n = (int) $lines[$i];
                $f = [];
                foreach ([2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37] as $p) {
                    while ($n % $p == 0) {
                        $f[] = $p;
                        $n = intdiv($n, $p);
                    }
                }
                $stack = $n > 1 ? [$n] : [];
                while ($stack) {
                    $m = array_pop($stack);
                    if ($prima($m)) {
                        $f[] = $m;

                        continue;
                    }
                    $d = $rho($m);
                    $stack[] = $d;
                    $stack[] = intdiv($m, $d);
                }
                sort($f);
                $out[] = implode(' ', $f);
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

__extension__ typedef unsigned __int128 u128;
typedef unsigned long long ull;

ull mulmod(ull a, ull b, ull m) { return (ull)((u128)a * b % m); }

// prima(n): Miller-Rabin    rho(n): satu faktor nontrivial n komposit

int main() {
    int q;
    cin >> q;
    while (q--) {
        ull n;
        cin >> n;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// pakai BigInt: hasil kali sampai 10^30
const q = Number(readLine());
for (let i = 0; i < q; i++) {
  const n = BigInt(readLine().trim());
  // prima(n): Miller-Rabin    rho(n): satu faktor nontrivial n komposit
}
CODE,
            'python' => <<<'CODE'
import sys
from math import gcd

data = sys.stdin.read().split()
q = int(data[0])
# prima(n): Miller-Rabin    rho(n): satu faktor nontrivial n komposit
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

__extension__ typedef unsigned __int128 u128;
typedef unsigned long long ull;

ull mulmod(ull a, ull b, ull m) { return (ull)((u128)a * b % m); }
ull powmod(ull a, ull e, ull m) {
    ull r = 1;
    a %= m;
    while (e) {
        if (e & 1) r = mulmod(r, a, m);
        a = mulmod(a, a, m);
        e >>= 1;
    }
    return r;
}
bool prima(ull n) {
    if (n < 2) return false;
    const ull basis[] = {2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37};
    for (ull p : basis) if (n % p == 0) return n == p;
    ull d = n - 1;
    int s = 0;
    while (d % 2 == 0) { d /= 2; s++; }
    for (ull a : basis) {
        ull x = powmod(a, d, n);
        if (x == 1 || x == n - 1) continue;
        bool saksi = true;
        for (int r = 1; r < s && saksi; r++) {
            x = mulmod(x, x, n);
            if (x == n - 1) saksi = false;
        }
        if (saksi) return false;
    }
    return true;
}
ull gcdu(ull a, ull b) { while (b) { ull t = a % b; a = b; b = t; } return a; }

// Pollard Rho varian Brent: gcd dikumpulkan per 64 langkah
ull rho(ull n) {
    if (n % 2 == 0) return 2;
    for (ull c = 1;; c++) {
        auto f = [&](ull v) { return (mulmod(v, v, n) + c) % n; };
        ull y = 2, x = 2, ys = 2, q = 1, g = 1;
        for (ull r = 1; g == 1; r <<= 1) {
            x = y;
            for (ull i = 0; i < r; i++) y = f(y);
            for (ull k = 0; k < r && g == 1; k += 64) {
                ys = y;
                for (ull i = 0; i < min((ull)64, r - k); i++) {
                    y = f(y);
                    q = mulmod(q, x > y ? x - y : y - x, n);
                }
                g = gcdu(q, n);
            }
        }
        if (g == n) {                          // terlewat: ulangi satu per satu dari ys
            do {
                ys = f(ys);
                g = gcdu(x > ys ? x - ys : ys - x, n);
            } while (g == 1);
        }
        if (g != n) return g;                  // jika masih n, coba c berikutnya
    }
}

void faktorkan(ull n, vector<ull>& f) {
    if (n == 1) return;
    if (prima(n)) { f.push_back(n); return; }
    ull d = rho(n);
    faktorkan(d, f);
    faktorkan(n / d, f);
}

int main() {
    int q;
    cin >> q;
    while (q--) {
        ull n;
        cin >> n;
        vector<ull> f;
        for (ull p : {2ULL, 3ULL, 5ULL, 7ULL, 11ULL, 13ULL})   // buang faktor kecil dulu
            while (n % p == 0) { f.push_back(p); n /= p; }
        faktorkan(n, f);
        sort(f.begin(), f.end());
        for (size_t i = 0; i < f.size(); i++) cout << f[i] << (i + 1 == f.size() ? '\n' : ' ');
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const BASIS = [2n, 3n, 5n, 7n, 11n, 13n, 17n, 19n, 23n, 29n, 31n, 37n];
const powmod = (a, e, m) => {
  let r = 1n;
  a %= m;
  while (e > 0n) {
    if (e & 1n) r = (r * a) % m;
    a = (a * a) % m;
    e >>= 1n;
  }
  return r;
};
const prima = (n) => {
  if (n < 2n) return false;
  for (const p of BASIS) if (n % p === 0n) return n === p;
  let d = n - 1n, s = 0;
  while (d % 2n === 0n) { d /= 2n; s++; }
  for (const a of BASIS) {
    let x = powmod(a, d, n);
    if (x === 1n || x === n - 1n) continue;
    let saksi = true;
    for (let r = 1; r < s && saksi; r++) {
      x = (x * x) % n;
      if (x === n - 1n) saksi = false;
    }
    if (saksi) return false;
  }
  return true;
};
const gcd = (a, b) => { while (b) [a, b] = [b, a % b]; return a; };
const abs = (v) => (v < 0n ? -v : v);
const rho = (n) => {
  if (n % 2n === 0n) return 2n;
  for (let c = 1n; ; c++) {
    const f = (v) => (v * v + c) % n;
    let y = 2n, x = 2n, ys = 2n, q = 1n, g = 1n;
    for (let r = 1; g === 1n; r *= 2) {
      x = y;
      for (let i = 0; i < r; i++) y = f(y);
      for (let k = 0; k < r && g === 1n; k += 64) {
        ys = y;
        const lim = Math.min(64, r - k);
        for (let i = 0; i < lim; i++) { y = f(y); q = (q * abs(x - y)) % n; }
        g = gcd(q, n);
      }
    }
    if (g === n) {
      do { ys = f(ys); g = gcd(abs(x - ys), n); } while (g === 1n);
    }
    if (g !== n) return g;
  }
};
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  let n = BigInt(readLine().trim());
  const f = [];
  for (const p of [2n, 3n, 5n, 7n, 11n, 13n]) while (n % p === 0n) { f.push(p); n /= p; }
  const st = n > 1n ? [n] : [];
  while (st.length) {
    const m = st.pop();
    if (prima(m)) { f.push(m); continue; }
    const d = rho(m);
    st.push(d, m / d);
  }
  f.sort((a, b) => (a < b ? -1 : a > b ? 1 : 0));
  out.push(f.join(" "));
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from math import gcd

BASIS = (2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37)

def prima(n):
    if n < 2:
        return False
    for p in BASIS:
        if n % p == 0:
            return n == p
    d, s = n - 1, 0
    while d % 2 == 0:
        d //= 2
        s += 1
    for a in BASIS:
        x = pow(a, d, n)
        if x == 1 or x == n - 1:
            continue
        for _ in range(s - 1):
            x = x * x % n
            if x == n - 1:
                break
        else:
            return False
    return True

def rho(n):
    if n % 2 == 0:
        return 2
    c = 1
    while True:
        y = x = ys = 2
        q = g = r = 1
        while g == 1:
            x = y
            for _ in range(r):
                y = (y * y + c) % n
            k = 0
            while k < r and g == 1:
                ys = y
                for _ in range(min(64, r - k)):
                    y = (y * y + c) % n
                    q = q * abs(x - y) % n
                g = gcd(q, n)
                k += 64
            r *= 2
        if g == n:
            while True:
                ys = (ys * ys + c) % n
                g = gcd(abs(x - ys), n)
                if g > 1:
                    break
        if g != n:
            return g
        c += 1

data = sys.stdin.read().split()
q = int(data[0])
out = []
for v in data[1:1 + q]:
    n = int(v)
    f = []
    for p in (2, 3, 5, 7, 11, 13):
        while n % p == 0:
            f.append(p)
            n //= p
    st = [n] if n > 1 else []
    while st:
        m = st.pop()
        if prima(m):
            f.append(m)
            continue
        d = rho(m)
        st += [d, m // d]
    out.append(" ".join(map(str, sorted(f))))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Faktorisasi rekursif: jika n prima (Miller-Rabin), selesai; jika tidak, cari satu faktor d dengan <strong>Pollard Rho</strong> lalu faktorkan d dan n/d.</p>
<p>Pollard Rho mengikuti barisan x<sub>k+1</sub> = x<sub>k</sub>² + c mod n. Modulo faktor prima p yang belum diketahui, barisan ini berulang setelah ±√p langkah (paradoks ulang tahun), dan saat itu gcd(|x − y|, n) memunculkan p. Untuk n ≤ 10<sup>15</sup>, faktor terkecil ≤ 3,2·10<sup>7</sup>, jadi cukup ±6 000 langkah, jauh lebih cepat dari pembagian percobaan sampai 3,2·10<sup>7</sup> untuk 100 bilangan.</p>
<p>Varian Brent mengalikan banyak |x − y| dulu lalu memanggil gcd sekali per 64 langkah, karena gcd jauh lebih mahal. Jika gcd kumpulan bernilai n (melompati faktor), ulangi langkah satu per satu; jika tetap n, ganti c.</p>',
        'hints' => [
            'Pisahkan dua tugas: menguji apakah suatu bilangan prima (Miller-Rabin), dan menemukan satu faktor bilangan komposit.',
            'Pollard Rho: barisan x ← x² + c mod n dengan kura-kura dan kelinci; gcd(|x − y|, n) memunculkan faktor setelah ±n<sup>1/4</sup> langkah.',
            'Faktorkan rekursif: n prima → simpan; jika tidak, d = rho(n), lalu faktorkan d dan n/d. Urutkan hasil akhirnya.',
        ],
    ],

    // ───────────────────────── FFT / NTT ─────────────────────────
    [
        'slug' => 'kali-polinom',
        'lesson' => 'fft',
        'title' => 'Perkalian Polinom Cepat',
        'difficulty' => 'Sedang',
        'tags' => ['fft', 'ntt', 'konvolusi'],
        'time_limit' => 4000,
        'statement' => '<p>Diberikan dua polinom A(x) berderajat n dan B(x) berderajat m. Cetak semua koefisien C(x) = A(x)·B(x) modulo 998244353.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n m</code>. Baris kedua berisi koefisien A dari x<sup>0</sup> sampai x<sup>n</sup>. Baris ketiga berisi koefisien B dari x<sup>0</sup> sampai x<sup>m</sup>.</p>',
        'output_format' => '<p>n + m + 1 bilangan: koefisien C dari x<sup>0</sup> sampai x<sup>n+m</sup>, modulo 998244353.</p>',
        'constraints' => '<ul><li>0 ≤ n, m ≤ 50 000</li><li>0 ≤ koefisien &lt; 998244353</li></ul>',
        'samples' => [
            ['input' => "2 1\n1 2 3\n4 5\n", 'explanation' => '(1 + 2x + 3x²)(4 + 5x) = 4 + 13x + 22x² + 15x³.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $m, int $hi) {
                return "$n $m\n".T::join(T::arr($n + 1, 0, $hi))."\n".T::join(T::arr($m + 1, 0, $hi))."\n";
            };

            return ["0 0\n998244352\n998244352\n", "3 0\n1 2 3 4\n5\n", $gen(10, 7, 10), $gen(1000, 1000, 998244352), $gen(50000, 50000, 998244352), $gen(50000, 1, 998244352), $gen(32767, 32768, 1)];
        },
        'solve' => function (string $input) {
            $M = 998244353;
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $b = T::ints($lines[2]);
            $pw = function ($x, $e) use ($M) {
                $r = 1;
                $x %= $M;
                while ($e > 0) {
                    if ($e & 1) {
                        $r = $r * $x % $M;
                    }
                    $x = $x * $x % $M;
                    $e >>= 1;
                }

                return $r;
            };
            $sz = 1;
            while ($sz < $n + $m + 1) {
                $sz <<= 1;
            }
            $ntt = function (array $a, bool $balik) use ($sz, $M, $pw) {
                for ($i = 1, $j = 0; $i < $sz; $i++) {
                    $bit = $sz >> 1;
                    for (; $j & $bit; $bit >>= 1) {
                        $j ^= $bit;
                    }
                    $j ^= $bit;
                    if ($i < $j) {
                        [$a[$i], $a[$j]] = [$a[$j], $a[$i]];
                    }
                }
                for ($len = 2; $len <= $sz; $len <<= 1) {
                    $w = $pw(3, intdiv($M - 1, $len));
                    if ($balik) {
                        $w = $pw($w, $M - 2);
                    }
                    $h = $len >> 1;
                    $ws = [1];
                    for ($k = 1; $k < $h; $k++) {
                        $ws[$k] = $ws[$k - 1] * $w % $M;
                    }
                    for ($i = 0; $i < $sz; $i += $len) {
                        for ($k = 0; $k < $h; $k++) {
                            $u = $a[$i + $k];
                            $v = $a[$i + $k + $h] * $ws[$k] % $M;
                            $a[$i + $k] = ($u + $v) % $M;
                            $a[$i + $k + $h] = ($u - $v + $M) % $M;
                        }
                    }
                }
                if ($balik) {
                    $inv = $pw($sz, $M - 2);
                    foreach ($a as $i => $x) {
                        $a[$i] = $x * $inv % $M;
                    }
                }

                return $a;
            };
            $A = array_pad($a, $sz, 0);
            $B = array_pad($b, $sz, 0);
            $A = $ntt($A, false);
            $B = $ntt($B, false);
            for ($i = 0; $i < $sz; $i++) {
                $A[$i] = $A[$i] * $B[$i] % $M;
            }
            $C = $ntt($A, true);

            return implode(' ', array_slice($C, 0, $n + $m + 1));
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 998244353, G = 3;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n, m;
    cin >> n >> m;
    vector<long long> a(n + 1), b(m + 1);
    for (auto& x : a) cin >> x;
    for (auto& x : b) cin >> x;
    // NTT kedua polinom, kalikan titik demi titik, NTT balik
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 998244353;
// perkalian modulo tanpa kehilangan presisi (MOD < 2^30)
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const [n, m] = readInts();
const a = readInts(), b = readInts();
// NTT kedua polinom, kalikan titik demi titik, NTT balik
CODE,
            'python' => <<<'CODE'
MOD, G = 998244353, 3
n, m = map(int, input().split())
a = list(map(int, input().split()))
b = list(map(int, input().split()))
# NTT kedua polinom, kalikan titik demi titik, NTT balik
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 998244353, G = 3;

long long pw(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}

void ntt(vector<long long>& a, bool balik) {
    int n = a.size();
    for (int i = 1, j = 0; i < n; i++) {
        int bit = n >> 1;
        for (; j & bit; bit >>= 1) j ^= bit;
        j ^= bit;
        if (i < j) swap(a[i], a[j]);
    }
    for (int len = 2; len <= n; len <<= 1) {
        long long w = pw(G, (MOD - 1) / len);
        if (balik) w = pw(w, MOD - 2);
        for (int i = 0; i < n; i += len) {
            long long t = 1;
            for (int k = 0; k < len / 2; k++) {
                long long u = a[i + k], v = a[i + k + len / 2] * t % MOD;
                a[i + k] = (u + v) % MOD;
                a[i + k + len / 2] = (u - v + MOD) % MOD;
                t = t * w % MOD;
            }
        }
    }
    if (balik) {
        long long inv = pw(n, MOD - 2);
        for (auto& x : a) x = x * inv % MOD;
    }
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n, m;
    cin >> n >> m;
    vector<long long> a(n + 1), b(m + 1);
    for (auto& x : a) cin >> x;
    for (auto& x : b) cin >> x;
    int sz = 1;
    while (sz < n + m + 1) sz <<= 1;
    a.resize(sz);
    b.resize(sz);
    ntt(a, false);
    ntt(b, false);
    for (int i = 0; i < sz; i++) a[i] = a[i] * b[i] % MOD;
    ntt(a, true);
    string out;
    for (int i = 0; i <= n + m; i++) {
        if (i) out += ' ';
        out += to_string(a[i]);
    }
    cout << out << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 998244353;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pw = (a, e) => {
  let r = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) r = mul(r, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return r;
};
const ntt = (a, balik) => {
  const n = a.length;
  for (let i = 1, j = 0; i < n; i++) {
    let bit = n >> 1;
    for (; j & bit; bit >>= 1) j ^= bit;
    j ^= bit;
    if (i < j) { const t = a[i]; a[i] = a[j]; a[j] = t; }
  }
  for (let len = 2; len <= n; len <<= 1) {
    let w = pw(3, (MOD - 1) / len);
    if (balik) w = pw(w, MOD - 2);
    const h = len >> 1;
    const ws = new Float64Array(h);
    ws[0] = 1;
    for (let k = 1; k < h; k++) ws[k] = mul(ws[k - 1], w);
    for (let i = 0; i < n; i += len)
      for (let k = 0; k < h; k++) {
        const u = a[i + k], v = mul(a[i + k + h], ws[k]);
        a[i + k] = u + v >= MOD ? u + v - MOD : u + v;
        a[i + k + h] = u - v < 0 ? u - v + MOD : u - v;
      }
  }
  if (balik) {
    const inv = pw(n, MOD - 2);
    for (let i = 0; i < n; i++) a[i] = mul(a[i], inv);
  }
};
const [n, m] = readInts();
const a0 = readInts(), b0 = readInts();
let sz = 1;
while (sz < n + m + 1) sz <<= 1;
const a = new Float64Array(sz), b = new Float64Array(sz);
a.set(a0);
b.set(b0);
ntt(a, false);
ntt(b, false);
for (let i = 0; i < sz; i++) a[i] = mul(a[i], b[i]);
ntt(a, true);
console.log(Array.from(a.subarray(0, n + m + 1)).join(" "));
CODE,
            'python' => <<<'CODE'
MOD, G = 998244353, 3

def ntt(a, balik):
    n = len(a)
    j = 0
    for i in range(1, n):
        bit = n >> 1
        while j & bit:
            j ^= bit
            bit >>= 1
        j ^= bit
        if i < j:
            a[i], a[j] = a[j], a[i]
    length = 2
    while length <= n:
        half = length >> 1
        w = pow(G, (MOD - 1) // length, MOD)
        if balik:
            w = pow(w, MOD - 2, MOD)
        ws = [1] * half
        for k in range(1, half):
            ws[k] = ws[k - 1] * w % MOD
        if half < 16:
            for i in range(0, n, length):
                for k in range(half):
                    u = a[i + k]
                    v = a[i + k + half] * ws[k] % MOD
                    a[i + k] = (u + v) % MOD
                    a[i + k + half] = (u - v) % MOD
        else:                                   # blok besar: list comprehension lebih cepat
            for i in range(0, n, length):
                kiri = a[i:i + half]
                kanan = [x * y % MOD for x, y in zip(a[i + half:i + length], ws)]
                a[i:i + half] = [(u + v) % MOD for u, v in zip(kiri, kanan)]
                a[i + half:i + length] = [(u - v) % MOD for u, v in zip(kiri, kanan)]
        length <<= 1
    if balik:
        inv = pow(n, MOD - 2, MOD)
        a[:] = [x * inv % MOD for x in a]

n, m = map(int, input().split())
a = list(map(int, input().split()))
b = list(map(int, input().split()))
sz = 1
while sz < n + m + 1:
    sz <<= 1
a += [0] * (sz - len(a))
b += [0] * (sz - len(b))
ntt(a, False)
ntt(b, False)
c = [x * y % MOD for x, y in zip(a, b)]
ntt(c, True)
print(" ".join(map(str, c[:n + m + 1])))
CODE,
        ],
        'editorial' => '<p>Cara sekolah butuh (n + 1)(m + 1) ≈ 2,5·10<sup>9</sup> perkalian: terlalu lambat. Pakai <strong>NTT</strong> (FFT modulo 998244353 = 119·2<sup>23</sup> + 1, akar primitif 3):</p>
<ol><li>Ambil sz = pangkat dua terkecil ≥ n + m + 1 (agar hasil tidak "membungkus").</li>
<li>Evaluasi A dan B di sz akar kesatuan dengan NTT: O(sz log sz).</li>
<li>Kalikan nilainya titik demi titik.</li>
<li>NTT balik (akar invers, lalu bagi dengan sz) mengembalikan koefisien C.</li></ol>
<p>Untuk sz = 131 072, itu sekitar 3·17·65 536 operasi butterfly. Di JavaScript, perkalian dua bilangan &lt; 2<sup>30</sup> dipecah per 16 bit agar tetap tepat sebagai double.</p>',
        'hints' => [
            'Perkalian koefisien langsung O(nm) terlalu lambat. Di bentuk apa perkalian polinom menjadi O(n)?',
            'Nilai polinom di titik-titik: kalikan titik demi titik. Pilih titik = akar kesatuan agar evaluasi bisa O(n log n).',
            'NTT modulo 998244353 dengan ukuran pangkat dua ≥ n + m + 1, lalu NTT balik.',
        ],
    ],

    [
        'slug' => 'frekuensi-jumlah-pasangan',
        'lesson' => 'fft',
        'title' => 'Semua Jumlah Pasangan',
        'difficulty' => 'Sulit',
        'tags' => ['fft', 'ntt', 'konvolusi', 'kombinatorika'],
        'time_limit' => 4000,
        'statement' => '<p>Diberikan n bilangan bulat tak negatif a<sub>1</sub>, …, a<sub>n</sub>. Misalkan M nilai terbesarnya. Untuk <strong>setiap</strong> s dari 0 sampai 2M, hitung banyak pasangan indeks (i, j) dengan i &lt; j dan a<sub>i</sub> + a<sub>j</sub> = s. Cetak setiap banyaknya modulo 998244353.</p>',
        'input_format' => '<p>Baris pertama berisi <code>n</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>n</sub></code>.</p>',
        'output_format' => '<p>2M + 1 bilangan dalam satu baris: banyak pasangan untuk s = 0, 1, …, 2M (mod 998244353).</p>',
        'constraints' => '<ul><li>2 ≤ n ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 50 000</li></ul>',
        'samples' => [
            ['input' => "4\n1 2 2 3\n", 'explanation' => 'M = 3. Pasangan: 1+2 (dua kali) = 3, 1+3 = 4, 2+2 = 4, 2+3 (dua kali) = 5. Jadi s = 0..6: 0 0 0 2 2 2 0.'],
        ],
        'tests' => function () {
            $gen = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 0, $hi))."\n";
            };

            return ["2\n0 0\n", "3\n50000 50000 50000\n", $gen(10, 10), $gen(1000, 100), $gen(200000, 50000), $gen(200000, 3), $gen(200000, 49999), "200000\n".T::join(array_fill(0, 200000, 7))."\n"];
        },
        'solve' => function (string $input) {
            // Referensi: konvolusi frekuensi dengan NTT, lalu (f*f - diagonal) / 2
            $MOD = 998244353;
            $lines = T::lines($input);
            $a = T::ints($lines[1]);
            $M = max($a);
            $f = array_fill(0, $M + 1, 0);
            foreach ($a as $x) {
                $f[$x]++;
            }
            $pw = function ($x, $e) use ($MOD) {
                $r = 1;
                $x %= $MOD;
                while ($e > 0) {
                    if ($e & 1) {
                        $r = $r * $x % $MOD;
                    }
                    $x = $x * $x % $MOD;
                    $e >>= 1;
                }

                return $r;
            };
            $sz = 1;
            while ($sz < 2 * $M + 1) {
                $sz <<= 1;
            }
            $ntt = function (array $a, bool $balik) use ($sz, $MOD, $pw) {
                for ($i = 1, $j = 0; $i < $sz; $i++) {
                    $bit = $sz >> 1;
                    for (; $j & $bit; $bit >>= 1) {
                        $j ^= $bit;
                    }
                    $j ^= $bit;
                    if ($i < $j) {
                        [$a[$i], $a[$j]] = [$a[$j], $a[$i]];
                    }
                }
                for ($len = 2; $len <= $sz; $len <<= 1) {
                    $w = $pw(3, intdiv($MOD - 1, $len));
                    if ($balik) {
                        $w = $pw($w, $MOD - 2);
                    }
                    $h = $len >> 1;
                    $ws = [1];
                    for ($k = 1; $k < $h; $k++) {
                        $ws[$k] = $ws[$k - 1] * $w % $MOD;
                    }
                    for ($i = 0; $i < $sz; $i += $len) {
                        for ($k = 0; $k < $h; $k++) {
                            $u = $a[$i + $k];
                            $v = $a[$i + $k + $h] * $ws[$k] % $MOD;
                            $a[$i + $k] = ($u + $v) % $MOD;
                            $a[$i + $k + $h] = ($u - $v + $MOD) % $MOD;
                        }
                    }
                }
                if ($balik) {
                    $inv = $pw($sz, $MOD - 2);
                    foreach ($a as $i => $x) {
                        $a[$i] = $x * $inv % $MOD;
                    }
                }

                return $a;
            };
            $F = $ntt(array_pad($f, $sz, 0), false);
            for ($i = 0; $i < $sz; $i++) {
                $F[$i] = $F[$i] * $F[$i] % $MOD;
            }
            $C = $ntt($F, true);
            $inv2 = intdiv($MOD + 1, 2);
            $out = [];
            for ($s = 0; $s <= 2 * $M; $s++) {
                $v = $C[$s];
                if ($s % 2 == 0) {
                    $v = ($v - $f[intdiv($s, 2)] % $MOD + $MOD) % $MOD;
                }
                $out[] = $v * $inv2 % $MOD;
            }

            return implode(' ', $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 998244353, G = 3;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n;
    cin >> n;
    vector<int> a(n);
    for (auto& x : a) cin >> x;
    // f[v] = banyak elemen bernilai v. Kuadrat polinom f menghitung pasangan berurutan.
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 998244353;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const n = readInts()[0];
const a = readInts();
// f[v] = banyak elemen bernilai v. Kuadrat polinom f menghitung pasangan berurutan.
CODE,
            'python' => <<<'CODE'
import sys

MOD, G = 998244353, 3
data = sys.stdin.buffer.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + n]))
# f[v] = banyak elemen bernilai v. Kuadrat polinom f menghitung pasangan berurutan.
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 998244353, G = 3;

long long pw(long long a, long long e) {
    long long r = 1;
    a %= MOD;
    while (e) {
        if (e & 1) r = r * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return r;
}

void ntt(vector<long long>& a, bool balik) {
    int n = a.size();
    for (int i = 1, j = 0; i < n; i++) {
        int bit = n >> 1;
        for (; j & bit; bit >>= 1) j ^= bit;
        j ^= bit;
        if (i < j) swap(a[i], a[j]);
    }
    for (int len = 2; len <= n; len <<= 1) {
        long long w = pw(G, (MOD - 1) / len);
        if (balik) w = pw(w, MOD - 2);
        for (int i = 0; i < n; i += len) {
            long long t = 1;
            for (int k = 0; k < len / 2; k++) {
                long long u = a[i + k], v = a[i + k + len / 2] * t % MOD;
                a[i + k] = (u + v) % MOD;
                a[i + k + len / 2] = (u - v + MOD) % MOD;
                t = t * w % MOD;
            }
        }
    }
    if (balik) {
        long long inv = pw(n, MOD - 2);
        for (auto& x : a) x = x * inv % MOD;
    }
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);
    int n;
    cin >> n;
    vector<int> a(n);
    int M = 0;
    for (auto& x : a) {
        cin >> x;
        M = max(M, x);
    }
    int sz = 1;
    while (sz < 2 * M + 1) sz <<= 1;
    vector<long long> f(sz, 0);
    for (int x : a) f[x]++;
    vector<long long> cnt(f.begin(), f.begin() + M + 1);    // salinan frekuensi asli

    ntt(f, false);
    for (auto& x : f) x = x * x % MOD;                       // f * f = pasangan berurutan (i, j), termasuk i = j
    ntt(f, true);

    long long inv2 = (MOD + 1) / 2;
    string out;
    for (int s = 0; s <= 2 * M; s++) {
        long long v = f[s];
        if (s % 2 == 0) v = (v - cnt[s / 2] % MOD + MOD) % MOD;  // buang i = j (a_i + a_i = s)
        v = v * inv2 % MOD;                                        // (i, j) dan (j, i) sama
        if (s) out += ' ';
        out += to_string(v);
    }
    cout << out << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MOD = 998244353;
const mul = (a, b) => ((a * (b >>> 16)) % MOD * 65536 + a * (b & 65535)) % MOD;
const pw = (a, e) => {
  let r = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) r = mul(r, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return r;
};
const ntt = (a, balik) => {
  const n = a.length;
  for (let i = 1, j = 0; i < n; i++) {
    let bit = n >> 1;
    for (; j & bit; bit >>= 1) j ^= bit;
    j ^= bit;
    if (i < j) { const t = a[i]; a[i] = a[j]; a[j] = t; }
  }
  for (let len = 2; len <= n; len <<= 1) {
    let w = pw(3, (MOD - 1) / len);
    if (balik) w = pw(w, MOD - 2);
    const h = len >> 1;
    const ws = new Float64Array(h);
    ws[0] = 1;
    for (let k = 1; k < h; k++) ws[k] = mul(ws[k - 1], w);
    for (let i = 0; i < n; i += len)
      for (let k = 0; k < h; k++) {
        const u = a[i + k], v = mul(a[i + k + h], ws[k]);
        a[i + k] = u + v >= MOD ? u + v - MOD : u + v;
        a[i + k + h] = u - v < 0 ? u - v + MOD : u - v;
      }
  }
  if (balik) {
    const inv = pw(n, MOD - 2);
    for (let i = 0; i < n; i++) a[i] = mul(a[i], inv);
  }
};
const n = readInts()[0];
const a = readInts();
let M = 0;
for (const x of a) if (x > M) M = x;
let sz = 1;
while (sz < 2 * M + 1) sz <<= 1;
const f = new Float64Array(sz);
for (const x of a) f[x]++;
const cnt = f.slice(0, M + 1);
ntt(f, false);
for (let i = 0; i < sz; i++) f[i] = mul(f[i], f[i]);
ntt(f, true);
const inv2 = (MOD + 1) / 2;
const out = [];
for (let s = 0; s <= 2 * M; s++) {
  let v = f[s];
  if (s % 2 === 0) v = (v - (cnt[s / 2] % MOD) + MOD) % MOD;
  out.push(mul(v, inv2));
}
console.log(out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys

MOD, G = 998244353, 3

def ntt(a, balik):
    n = len(a)
    j = 0
    for i in range(1, n):
        bit = n >> 1
        while j & bit:
            j ^= bit
            bit >>= 1
        j ^= bit
        if i < j:
            a[i], a[j] = a[j], a[i]
    length = 2
    while length <= n:
        half = length >> 1
        w = pow(G, (MOD - 1) // length, MOD)
        if balik:
            w = pow(w, MOD - 2, MOD)
        ws = [1] * half
        for k in range(1, half):
            ws[k] = ws[k - 1] * w % MOD
        if half < 16:
            for i in range(0, n, length):
                for k in range(half):
                    u = a[i + k]
                    v = a[i + k + half] * ws[k] % MOD
                    a[i + k] = (u + v) % MOD
                    a[i + k + half] = (u - v) % MOD
        else:
            for i in range(0, n, length):
                kiri = a[i:i + half]
                kanan = [x * y % MOD for x, y in zip(a[i + half:i + length], ws)]
                a[i:i + half] = [(u + v) % MOD for u, v in zip(kiri, kanan)]
                a[i + half:i + length] = [(u - v) % MOD for u, v in zip(kiri, kanan)]
        length <<= 1
    if balik:
        inv = pow(n, MOD - 2, MOD)
        a[:] = [x * inv % MOD for x in a]

data = sys.stdin.buffer.read().split()
n = int(data[0])
a = list(map(int, data[1:1 + n]))
M = max(a)
sz = 1
while sz < 2 * M + 1:
    sz <<= 1
f = [0] * sz
for x in a:
    f[x] += 1
cnt = f[:M + 1]
ntt(f, False)
f = [x * x % MOD for x in f]
ntt(f, True)
inv2 = (MOD + 1) // 2
out = []
for s in range(2 * M + 1):
    v = f[s]
    if s % 2 == 0:
        v -= cnt[s // 2]
    out.append(v % MOD * inv2 % MOD)
print(" ".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Buat polinom frekuensi F(x) = Σ f[v]·x<sup>v</sup>, dengan f[v] = banyak elemen bernilai v. Koefisien x<sup>s</sup> di F(x)² adalah Σ<sub>v</sub> f[v]·f[s − v]: banyak pasangan <em>berurutan</em> (i, j) dengan a<sub>i</sub> + a<sub>j</sub> = s, <strong>termasuk i = j</strong>.</p>
<ul><li>Buang pasangan i = j: untuk s genap, ada f[s/2] pasangan seperti itu.</li>
<li>Setiap pasangan i &lt; j terhitung dua kali, (i, j) dan (j, i): bagi 2 (kalikan invers 2 modulo p).</li></ul>
<p>Mengkuadratkan F dengan NTT: ukuran pangkat dua ≥ 2M + 1 (131 072), O(M log M). Cara langsung atas pasangan nilai O(M²) = 2,5·10<sup>9</sup> terlalu lambat, apalagi atas pasangan indeks O(n²).</p>',
        'hints' => [
            'Banyak pasangan hanya bergantung pada frekuensi setiap nilai. Tuliskan frekuensi sebagai koefisien polinom.',
            'Kuadrat polinom frekuensi memberi banyak pasangan berurutan untuk setiap jumlah s, tetapi termasuk i = j dan menghitung setiap pasangan dua kali.',
            'Hitung F² dengan NTT, kurangi f[s/2] untuk s genap, lalu kalikan invers 2.',
        ],
    ],
];
