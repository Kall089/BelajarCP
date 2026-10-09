<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal track Teknik Array: prefix sum, difference array, two pointers, Kadane,
 * monotonic stack/deque, kompresi koordinat, dan string & frekuensi.
 * Semua kode C++ harus lolos C++14: tanpa structured binding.
 */

return [

    // ───────────────────────── Prefix Sum 1D & 2D ─────────────────────────
    [
        'slug' => 'hutan-2d',
        'lesson' => 'prefix-sum',
        'title' => 'Menghitung Pohon di Hutan',
        'difficulty' => 'Mudah',
        'tags' => ['prefix sum 2D'],
        'statement' => '<p>Peta sebuah hutan berbentuk grid <strong>R × C</strong>. Setiap petak berisi <code>*</code> (pohon) atau <code>.</code> (tanah kosong). Petugas kehutanan punya <strong>Q</strong> pertanyaan: berapa banyak pohon di dalam persegi panjang dengan sudut kiri atas <code>(r1, c1)</code> dan sudut kanan bawah <code>(r2, c2)</code>?</p>
<p>Baris dinomori 1 sampai R dari atas, kolom 1 sampai C dari kiri.</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C Q</code>. R baris berikutnya berisi peta. Q baris berikutnya masing-masing berisi <code>r1 c1 r2 c2</code>.</p>',
        'output_format' => '<p>Q baris, banyak pohon pada setiap persegi panjang.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 1000</li><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ r1 ≤ r2 ≤ R, 1 ≤ c1 ≤ c2 ≤ C</li></ul>',
        'samples' => [
            ['input' => "4 5 3\n.*..*\n**.*.\n...*.\n*.***\n2 2 4 4\n1 1 4 5\n3 1 3 3\n", 'explanation' => 'Persegi baris 2–4, kolom 2–4 berisi pohon di (2,2), (2,4), (3,4), (4,3), (4,4): 5 pohon. Seluruh hutan berisi 10 pohon. Baris 3 kolom 1–3 kosong.'],
        ],
        'tests' => function () {
            $make = function (int $r, int $c, int $q, float $p) {
                $out = ["$r $c $q"];
                foreach (T::grid($r, $c, $p, '*', '.') as $row) {
                    $out[] = $row;
                }
                for ($i = 0; $i < $q; $i++) {
                    $r1 = mt_rand(1, $r);
                    $r2 = mt_rand($r1, $r);
                    $c1 = mt_rand(1, $c);
                    $c2 = mt_rand($c1, $c);
                    $out[] = "$r1 $c1 $r2 $c2";
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1 2\n*\n1 1 1 1\n1 1 1 1\n", $make(5, 7, 20, 0.4), $make(100, 100, 1000, 0.5),
                $make(1000, 1000, 200000, 0.3), $make(1000, 1000, 200000, 1.0), $make(1, 1000, 200000, 0.5), $make(1000, 1, 200000, 0.5),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C, $Q] = T::ints($lines[0]);
            $P = [array_fill(0, $C + 1, 0)];
            for ($i = 1; $i <= $R; $i++) {
                $row = $lines[$i];
                $prev = $P[$i - 1];
                $cur = [0];
                $run = 0;
                for ($j = 1; $j <= $C; $j++) {
                    $run += $row[$j - 1] === '*' ? 1 : 0;
                    $cur[$j] = $prev[$j] + $run;
                }
                $P[$i] = $cur;
            }
            $out = [];
            for ($k = 0; $k < $Q; $k++) {
                [$r1, $c1, $r2, $c2] = T::ints($lines[$R + 1 + $k]);
                $out[] = $P[$r2][$c2] - $P[$r1 - 1][$c2] - $P[$r2][$c1 - 1] + $P[$r1 - 1][$c1 - 1];
            }

            return implode("\n", $out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int P[1001][1001];   // P[i][j] = banyak pohon di persegi (1,1)..(i,j)

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C, Q;
    cin >> R >> C >> Q;
    for (int i = 1; i <= R; i++) {
        string s;
        cin >> s;
        // isi P[i][j]
    }
    while (Q--) {
        int r1, c1, r2, c2;
        cin >> r1 >> c1 >> r2 >> c2;
        // jawab dengan inklusi-eksklusi
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C, Q] = readInts();
const peta = [];
for (let i = 0; i < R; i++) peta.push(readLine());
// Bangun prefix 2D, lalu jawab setiap pertanyaan
const out = [];
for (let k = 0; k < Q; k++) {
  const [r1, c1, r2, c2] = readInts();
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C, Q = map(int, input().split())
peta = [input().strip() for _ in range(R)]
# Bangun prefix 2D, lalu jawab setiap pertanyaan
out = []
for _ in range(Q):
    r1, c1, r2, c2 = map(int, input().split())

print("\n".join(map(str, out)))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int P[1001][1001];   // P[i][j] = banyak pohon di persegi (1,1)..(i,j)

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C, Q;
    cin >> R >> C >> Q;
    for (int i = 1; i <= R; i++) {
        string s;
        cin >> s;
        for (int j = 1; j <= C; j++)
            P[i][j] = (s[j - 1] == '*') + P[i - 1][j] + P[i][j - 1] - P[i - 1][j - 1];
    }
    string out;
    while (Q--) {
        int r1, c1, r2, c2;
        cin >> r1 >> c1 >> r2 >> c2;
        int ans = P[r2][c2] - P[r1 - 1][c2] - P[r2][c1 - 1] + P[r1 - 1][c1 - 1];
        out += to_string(ans) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C, Q] = readInts();
const W = C + 1;
const P = new Int32Array((R + 1) * W);     // P[i*W + j]
for (let i = 1; i <= R; i++) {
  const s = readLine();
  for (let j = 1; j <= C; j++) {
    P[i * W + j] = (s[j - 1] === "*" ? 1 : 0) + P[(i - 1) * W + j] + P[i * W + j - 1] - P[(i - 1) * W + j - 1];
  }
}
const out = [];
for (let k = 0; k < Q; k++) {
  const [r1, c1, r2, c2] = readInts();
  out.push(P[r2 * W + c2] - P[(r1 - 1) * W + c2] - P[r2 * W + c1 - 1] + P[(r1 - 1) * W + c1 - 1]);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
R, C, Q = int(data[0]), int(data[1]), int(data[2])
P = [[0] * (C + 1)]
for i in range(R):
    row = data[3 + i]
    prev = P[-1]
    cur = [0] * (C + 1)
    run = 0
    for j in range(C):
        run += row[j] == 42          # 42 = kode ASCII '*'
        cur[j + 1] = prev[j + 1] + run
    P.append(cur)
out = []
pos = 3 + R
for _ in range(Q):
    r1, c1, r2, c2 = map(int, data[pos:pos + 4])
    pos += 4
    out.append(P[r2][c2] - P[r1 - 1][c2] - P[r2][c1 - 1] + P[r1 - 1][c1 - 1])
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Menghitung pohon petak demi petak untuk setiap pertanyaan bisa butuh 10<sup>6</sup> langkah per pertanyaan, total 2 · 10<sup>11</sup>. Terlalu lambat.</p>
<p>Bangun <strong>prefix sum 2D</strong>: <code>P[i][j]</code> = banyak pohon di persegi dari (1, 1) sampai (i, j).</p>
<p style="text-align:center"><code>P[i][j] = pohon(i, j) + P[i−1][j] + P[i][j−1] − P[i−1][j−1]</code></p>
<p>Lalu setiap pertanyaan dijawab dengan inklusi-eksklusi dalam O(1):</p>
<p style="text-align:center"><code>P[r2][c2] − P[r1−1][c2] − P[r2][c1−1] + P[r1−1][c1−1]</code></p>
<p>Total <code>O(R · C + Q)</code>. Memakai indeks mulai 1 dengan baris dan kolom 0 berisi nol membuat rumusnya tanpa kasus khusus.</p>',
        'hints' => [
            'Siapkan tabel P[i][j] = banyak pohon di persegi dari (1,1) sampai (i,j).',
            'Saat membangun: tambah persegi atas dan kiri, kurangi persegi kiri-atas yang terhitung dua kali.',
            'Jawaban = P[r2][c2] − P[r1−1][c2] − P[r2][c1−1] + P[r1−1][c1−1].',
        ],
    ],

    [
        'slug' => 'subarray-kelipatan',
        'lesson' => 'prefix-sum',
        'title' => 'Subarray Kelipatan M',
        'difficulty' => 'Sedang',
        'tags' => ['prefix sum', 'modulo', 'counting'],
        'statement' => '<p>Diberikan barisan <code>a<sub>1</sub>, …, a<sub>N</sub></code> (boleh negatif) dan bilangan <strong>M</strong>. Ada berapa subarray (bagian berurutan, tidak kosong) yang jumlahnya <strong>habis dibagi M</strong>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak subarray yang jumlahnya kelipatan M.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ M ≤ 10<sup>9</sup></li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5 3\n2 1 -3 4 5\n", 'explanation' => 'Prefix: 0, 2, 3, 0, 4, 9. Sisa bagi 3: 0, 2, 0, 0, 1, 0. Sisa 0 muncul 4 kali → C(4, 2) = 6 pasang; sisa 2 dan 1 masing-masing sekali. Jadi ada 6 subarray, misalnya [2, 1], [−3], [2, 1, −3], dan [−3, 4, 5].'],
        ],
        'tests' => function () {
            $make = function (int $n, int $m, int $lo, int $hi) {
                return "$n $m\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return [
                "1 1\n-5\n", "3 2\n1 1 1\n", $make(10, 3, -5, 5), $make(1000, 7, -1000, 1000),
                $make(200000, 1000000000, -1000000000, 1000000000), $make(200000, 2, -1000000000, 1000000000),
                $make(200000, 1, -10, 10), $make(200000, 13, 0, 0), $make(200000, 99991, -1000000000, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $cnt = [0 => 1];
            $pre = 0;
            $ans = 0;
            foreach (T::ints($lines[1]) as $x) {
                $pre = (($pre + $x) % $m + $m) % $m;
                $ans += $cnt[$pre] ?? 0;
                $cnt[$pre] = ($cnt[$pre] ?? 0) + 1;
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
    long long m;
    cin >> n >> m;
    // Hitung banyak pasangan prefix dengan sisa bagi M yang sama

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const a = readInts();
// Hitung banyak pasangan prefix dengan sisa bagi M yang sama
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
a = list(map(int, input().split()))
# Hitung banyak pasangan prefix dengan sisa bagi M yang sama
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
    long long m;
    cin >> n >> m;
    map<long long, long long> cnt;   // sisa prefix -> berapa kali muncul
    cnt[0] = 1;                      // prefix kosong
    long long pre = 0, ans = 0;
    for (int i = 0; i < n; i++) {
        long long a;
        cin >> a;
        pre = ((pre + a) % m + m) % m;   // sisa selalu di [0, m)
        ans += cnt[pre];                 // pasangkan dengan prefix sebelumnya bersisa sama
        cnt[pre]++;
    }
    cout << ans << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const a = readInts();
const cnt = new Map([[0, 1]]);
let pre = 0, ans = 0;
for (const x of a) {
  pre = (((pre + x) % m) + m) % m;     // |pre + x| < 2^53, aman untuk Number
  const c = cnt.get(pre) || 0;
  ans += c;
  cnt.set(pre, c + 1);
}
console.log(String(ans));
CODE,
            'python' => <<<'CODE'
import sys
from collections import defaultdict
input = sys.stdin.readline

n, m = map(int, input().split())
a = list(map(int, input().split()))
cnt = defaultdict(int)
cnt[0] = 1
pre = ans = 0
for x in a:
    pre = (pre + x) % m          # % di Python selalu tidak negatif
    ans += cnt[pre]
    cnt[pre] += 1
print(ans)
CODE,
        ],
        'editorial' => '<p>Subarray a[l..r] berjumlah <code>pre[r] − pre[l−1]</code>. Jumlah itu kelipatan M tepat ketika <code>pre[r]</code> dan <code>pre[l−1]</code> punya <strong>sisa bagi M yang sama</strong>.</p>
<p>Jadi soalnya menjadi: hitung banyak pasangan indeks prefix (i &lt; j) dengan sisa yang sama. Jalan dari kiri ke kanan, simpan <code>cnt[sisa]</code> untuk prefix yang sudah lewat (termasuk prefix kosong bersisa 0), dan setiap prefix baru menambah jawaban sebesar <code>cnt[sisanya]</code>.</p>
<p><strong>Jebakan:</strong></p>
<ul><li>Di C++ dan JavaScript, <code>%</code> pada bilangan negatif menghasilkan sisa negatif. Normalisasi: <code>((x % m) + m) % m</code>.</li><li>Jawaban bisa sampai N(N+1)/2 ≈ 2 · 10<sup>10</sup>: pakai <code>long long</code>.</li><li>M sampai 10<sup>9</sup>, jadi array berukuran M tidak mungkin; pakai <code>map</code>.</li></ul>',
        'hints' => [
            'Tulis jumlah subarray a[l..r] sebagai selisih dua prefix.',
            'Selisih dua bilangan habis dibagi M jika dan hanya jika keduanya bersisa sama saat dibagi M.',
            'Hitung banyak pasangan prefix bersisa sama dengan map cnt[sisa]. Normalisasi sisa negatif, dan pakai long long.',
        ],
    ],

    // ───────────────────────── Difference Array ─────────────────────────
    [
        'slug' => 'tambah-rentang',
        'lesson' => 'difference-array',
        'title' => 'Bonus Gaji Bertahap',
        'difficulty' => 'Mudah',
        'tags' => ['difference array', 'prefix sum'],
        'statement' => '<p>Sebuah perusahaan punya <strong>N</strong> karyawan bernomor 1 sampai N dengan gaji awal <code>g<sub>1</sub>, …, g<sub>N</sub></code>. Selama setahun ada <strong>Q</strong> keputusan: keputusan ke-j menambah gaji semua karyawan bernomor <code>l</code> sampai <code>r</code> sebesar <code>v</code> (v boleh negatif, artinya potongan).</p>
<p>Cetak gaji akhir setiap karyawan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi <code>g<sub>1</sub> … g<sub>N</sub></code>. Q baris berikutnya masing-masing berisi <code>l r v</code>.</p>',
        'output_format' => '<p>Satu baris berisi N bilangan: gaji akhir karyawan 1 sampai N.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 200 000</li><li>0 ≤ g<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ l ≤ r ≤ N</li><li>−10<sup>9</sup> ≤ v ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5 3\n10 20 30 40 50\n1 3 5\n2 5 -10\n3 3 100\n", 'explanation' => 'Karyawan 1: 10 + 5 = 15. Karyawan 2: 20 + 5 − 10 = 15. Karyawan 3: 30 + 5 − 10 + 100 = 125. Karyawan 4: 40 − 10 = 30. Karyawan 5: 50 − 10 = 40.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $q, int $vmax, bool $wide = false) {
                $out = ["$n $q", T::join(T::arr($n, 0, 1000000000))];
                for ($i = 0; $i < $q; $i++) {
                    $l = $wide ? mt_rand(1, 10) : mt_rand(1, $n);
                    $r = $wide ? mt_rand($n - 10, $n) : mt_rand($l, $n);
                    $r = max($l, $r);
                    $out[] = "$l $r ".mt_rand(-$vmax, $vmax);
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n0\n1 1 -7\n", $make(10, 10, 100), $make(1000, 1000, 1000000000),
                $make(200000, 200000, 1000000000), $make(200000, 200000, 1000000000, true), $make(200000, 200000, 1),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $g = T::ints($lines[1]);
            $d = array_fill(0, $n + 2, 0);
            for ($i = 0; $i < $q; $i++) {
                [$l, $r, $v] = T::ints($lines[2 + $i]);
                $d[$l] += $v;
                $d[$r + 1] -= $v;
            }
            $run = 0;
            for ($i = 1; $i <= $n; $i++) {
                $run += $d[$i];
                $g[$i - 1] += $run;
            }

            return T::join($g);
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
    vector<long long> g(n + 1), d(n + 2, 0);
    for (int i = 1; i <= n; i++) cin >> g[i];
    while (q--) {
        int l, r;
        long long v;
        cin >> l >> r >> v;
        // catat update dalam O(1)
    }
    // hitung gaji akhir dengan prefix sum dari d

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const g = readInts();
// catat setiap update dalam O(1), lalu prefix sum

CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
g = list(map(int, input().split()))
# catat setiap update dalam O(1), lalu prefix sum

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
    vector<long long> g(n + 1), d(n + 2, 0);
    for (int i = 1; i <= n; i++) cin >> g[i];
    while (q--) {
        int l, r;
        long long v;
        cin >> l >> r >> v;
        d[l] += v;          // mulai menambah v di l
        d[r + 1] -= v;      // berhenti setelah r
    }
    long long berjalan = 0;
    for (int i = 1; i <= n; i++) {
        berjalan += d[i];
        cout << g[i] + berjalan << " \n"[i == n];
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const g = readInts();
// Jumlah bisa sampai 2·10^14: masih aman di Number (< 9·10^15)
const d = new Float64Array(n + 2);
for (let i = 0; i < q; i++) {
  const [l, r, v] = readInts();
  d[l] += v;
  d[r + 1] -= v;
}
let berjalan = 0;
const out = new Array(n);
for (let i = 1; i <= n; i++) {
  berjalan += d[i];
  out[i - 1] = g[i - 1] + berjalan;
}
console.log(out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
g = list(map(int, data[2:2 + n]))
d = [0] * (n + 2)
pos = 2 + n
for _ in range(q):
    l, r, v = int(data[pos]), int(data[pos + 1]), int(data[pos + 2])
    pos += 3
    d[l] += v
    d[r + 1] -= v
berjalan = 0
for i in range(1, n + 1):
    berjalan += d[i]
    g[i - 1] += berjalan
print(" ".join(map(str, g)))
CODE,
        ],
        'editorial' => '<p>Menambah v ke setiap karyawan di [l, r] dengan loop butuh O(N) per keputusan, total O(N · Q) = 4 · 10<sup>10</sup>.</p>
<p>Karena gaji baru ditanyakan <strong>setelah semua keputusan</strong>, pakai difference array: setiap keputusan cukup <code>d[l] += v</code> dan <code>d[r+1] −= v</code>. Di akhir, jumlah berjalan <code>d[1] + … + d[i]</code> adalah total tambahan untuk karyawan i.</p>
<p>Total <code>O(N + Q)</code>. Tambahan bisa mencapai 2 · 10<sup>5</sup> · 10<sup>9</sup> = 2 · 10<sup>14</sup>: pakai <code>long long</code>, dan siapkan d berukuran N + 2 agar <code>d[r+1]</code> aman.</p>',
        'hints' => [
            'Gaji hanya ditanyakan di akhir. Apakah setiap keputusan harus langsung diterapkan ke semua karyawan?',
            'Catat "mulai menambah v di l" dan "berhenti setelah r" saja.',
            'd[l] += v, d[r+1] −= v. Di akhir, gaji i = g[i] + (d[1] + … + d[i]). Pakai long long.',
        ],
    ],

    [
        'slug' => 'karpet-2d',
        'lesson' => 'difference-array',
        'title' => 'Tumpukan Karpet',
        'difficulty' => 'Sedang',
        'tags' => ['difference array 2D', 'prefix sum 2D'],
        'statement' => '<p>Lantai aula berbentuk grid <strong>R × C</strong> petak. Panitia menggelar <strong>K</strong> karpet; karpet ke-i menutupi semua petak dari baris <code>r1</code> sampai <code>r2</code> dan kolom <code>c1</code> sampai <code>c2</code>. Karpet boleh saling menumpuk.</p>
<p>Sebuah petak disebut <strong>empuk</strong> jika ditutupi paling sedikit <strong>T</strong> karpet. Ada berapa petak empuk?</p>',
        'input_format' => '<p>Baris pertama berisi <code>R C K T</code>. K baris berikutnya masing-masing berisi <code>r1 c1 r2 c2</code> (baris dan kolom mulai dari 1).</p>',
        'output_format' => '<p>Satu bilangan: banyak petak empuk.</p>',
        'constraints' => '<ul><li>1 ≤ R, C ≤ 1000</li><li>1 ≤ K ≤ 200 000</li><li>1 ≤ T ≤ K</li><li>1 ≤ r1 ≤ r2 ≤ R, 1 ≤ c1 ≤ c2 ≤ C</li></ul>',
        'samples' => [
            ['input' => "4 5 3 2\n1 1 2 3\n2 2 4 4\n1 3 3 5\n", 'explanation' => 'Petak yang tertutup 2 karpet atau lebih: (1,3), (2,2), (2,3), (2,4), (3,3), (3,4). Petak (2,3) bahkan tertutup ketiga karpet. Jawabannya 6.'],
        ],
        'tests' => function () {
            $make = function (int $R, int $C, int $K, int $T, int $maxSide) {
                $out = ["$R $C $K $T"];
                for ($i = 0; $i < $K; $i++) {
                    $r1 = mt_rand(1, $R);
                    $c1 = mt_rand(1, $C);
                    $r2 = min($R, $r1 + mt_rand(0, $maxSide));
                    $c2 = min($C, $c1 + mt_rand(0, $maxSide));
                    $out[] = "$r1 $c1 $r2 $c2";
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1 1 1\n1 1 1 1\n", $make(5, 5, 4, 2, 3), $make(50, 60, 100, 3, 20), $make(1000, 1000, 200000, 50, 200),
                $make(1000, 1000, 200000, 1, 5), $make(1000, 1000, 1000, 10, 1000), $make(1, 1000, 200000, 100000, 1000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$R, $C, $K, $T] = T::ints($lines[0]);
            $W = $C + 2;
            $d = array_fill(0, ($R + 2) * $W, 0);
            for ($i = 1; $i <= $K; $i++) {
                [$r1, $c1, $r2, $c2] = T::ints($lines[$i]);
                $d[$r1 * $W + $c1]++;
                $d[$r1 * $W + $c2 + 1]--;
                $d[($r2 + 1) * $W + $c1]--;
                $d[($r2 + 1) * $W + $c2 + 1]++;
            }
            $cnt = 0;
            for ($i = 1; $i <= $R; $i++) {
                for ($j = 1; $j <= $C; $j++) {
                    $k = $i * $W + $j;
                    $d[$k] += $d[$k - $W] + $d[$k - 1] - $d[$k - $W - 1];
                    if ($d[$k] >= $T) {
                        $cnt++;
                    }
                }
            }

            return (string) $cnt;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int D[1002][1002];   // global

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C, K, T;
    cin >> R >> C >> K >> T;
    for (int i = 0; i < K; i++) {
        int r1, c1, r2, c2;
        cin >> r1 >> c1 >> r2 >> c2;
        // empat catatan di sudut-sudut
    }
    // prefix sum 2D, lalu hitung petak yang >= T

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C, K, T] = readInts();
// difference array 2D, lalu prefix sum 2D

CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

R, C, K, T = map(int, input().split())
# difference array 2D, lalu prefix sum 2D

CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int D[1002][1002];   // global

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C, K, T;
    cin >> R >> C >> K >> T;
    for (int i = 0; i < K; i++) {
        int r1, c1, r2, c2;
        cin >> r1 >> c1 >> r2 >> c2;
        D[r1][c1]++;                 // sudut kiri atas: mulai
        D[r1][c2 + 1]--;             // berhenti di kanan
        D[r2 + 1][c1]--;             // berhenti di bawah
        D[r2 + 1][c2 + 1]++;         // kembalikan sudut yang terkurangi dua kali
    }
    long long empuk = 0;
    for (int i = 1; i <= R; i++)
        for (int j = 1; j <= C; j++) {
            D[i][j] += D[i - 1][j] + D[i][j - 1] - D[i - 1][j - 1];   // kini = banyak karpet di (i, j)
            if (D[i][j] >= T) empuk++;
        }
    cout << empuk << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [R, C, K, T] = readInts();
const W = C + 2;
const D = new Int32Array((R + 2) * W);
for (let i = 0; i < K; i++) {
  const [r1, c1, r2, c2] = readInts();
  D[r1 * W + c1]++;
  D[r1 * W + c2 + 1]--;
  D[(r2 + 1) * W + c1]--;
  D[(r2 + 1) * W + c2 + 1]++;
}
let empuk = 0;
for (let i = 1; i <= R; i++) {
  for (let j = 1; j <= C; j++) {
    const k = i * W + j;
    D[k] += D[k - W] + D[k - 1] - D[k - W - 1];
    if (D[k] >= T) empuk++;
  }
}
console.log(String(empuk));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
R, C, K, T = map(int, data[:4])
W = C + 2
D = [0] * ((R + 2) * W)
pos = 4
for _ in range(K):
    r1, c1, r2, c2 = map(int, data[pos:pos + 4])
    pos += 4
    D[r1 * W + c1] += 1
    D[r1 * W + c2 + 1] -= 1
    D[(r2 + 1) * W + c1] -= 1
    D[(r2 + 1) * W + c2 + 1] += 1
empuk = 0
for i in range(1, R + 1):
    run = 0                          # prefix baris saat ini
    atas = (i - 1) * W
    cur = i * W
    for j in range(1, C + 1):
        run += D[cur + j]
        D[cur + j] = run + D[atas + j]   # = prefix 2D
        if D[cur + j] >= T:
            empuk += 1
print(empuk)
CODE,
        ],
        'editorial' => '<p>Menandai setiap petak dari setiap karpet bisa butuh 2 · 10<sup>5</sup> · 10<sup>6</sup> langkah. Kita perlu <strong>difference array 2D</strong>.</p>
<p>Untuk satu karpet, tulis empat catatan:</p>
<p style="text-align:center"><code>D[r1][c1] += 1, D[r1][c2+1] −= 1, D[r2+1][c1] −= 1, D[r2+1][c2+1] += 1</code></p>
<p>Setelah semua karpet dicatat, prefix sum 2D atas D memberi banyak karpet di setiap petak: catatan +1 di sudut kiri atas "menyebar" ke kanan dan ke bawah, dua catatan −1 menghentikannya di luar karpet, dan catatan +1 terakhir mengembalikan sudut kanan bawah yang terkurangi dua kali.</p>
<p>Total <code>O(K + R · C)</code>. Siapkan array berukuran (R + 2) × (C + 2) agar indeks r2 + 1 dan c2 + 1 aman.</p>',
        'hints' => [
            'Coba dulu versi 1 dimensi: menambah 1 pada rentang [l, r] cukup dua catatan.',
            'Versi 2D butuh empat catatan di sudut-sudut persegi, dengan tanda + − − +.',
            'Setelah semua catatan, hitung prefix sum 2D: nilainya adalah banyak karpet di setiap petak. Hitung yang ≥ T.',
        ],
    ],

    // ───────────────────────── Two Pointers & Sliding Window ─────────────────────────
    [
        'slug' => 'pasangan-jumlah',
        'lesson' => 'two-pointers',
        'title' => 'Pasangan Hemat',
        'difficulty' => 'Mudah',
        'tags' => ['two pointers', 'sorting'],
        'statement' => '<p>Sebuah toko menjual <strong>N</strong> barang dengan harga <code>a<sub>1</sub>, …, a<sub>N</sub></code>. Ada promo: beli tepat <strong>dua barang berbeda</strong> dengan total harga paling banyak <strong>X</strong>, dapat hadiah.</p>
<p>Ada berapa pasangan barang (i, j) dengan i &lt; j yang memenuhi syarat promo?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N X</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak pasangan.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub>, X ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6 10\n7 2 5 8 1 4\n", 'explanation' => 'Urut: 1 2 4 5 7 8. Pasangan dengan total ≤ 10: (1,2), (1,4), (1,5), (1,7), (1,8), (2,4), (2,5), (2,7), (2,8), (4,5). Ada 10.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi, int $x) {
                return "$n $x\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1 5\n3\n", "2 5\n2 3\n", "2 4\n2 3\n", $make(10, 10, 10), $make(1000, 1000, 1000),
                $make(200000, 1000000000, 1000000000), $make(200000, 1000000000, 1), $make(200000, 5, 1000000000), $make(200000, 100, 100),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $x] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            sort($a);
            $l = 0;
            $r = $n - 1;
            $cnt = 0;
            while ($l < $r) {
                if ($a[$l] + $a[$r] <= $x) {
                    $cnt += $r - $l;
                    $l++;
                } else {
                    $r--;
                }
            }

            return (string) $cnt;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long x;
    cin >> n >> x;
    vector<long long> a(n);
    for (auto& v : a) cin >> v;

    // urutkan, lalu dua penunjuk dari kedua ujung

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, x] = readInts();
const a = readInts();
// urutkan (dengan comparator angka!), lalu dua penunjuk dari kedua ujung
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, x = map(int, input().split())
a = list(map(int, input().split()))
# urutkan, lalu dua penunjuk dari kedua ujung
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
    long long x;
    cin >> n >> x;
    vector<long long> a(n);
    for (auto& v : a) cin >> v;
    sort(a.begin(), a.end());

    int l = 0, r = n - 1;
    long long cnt = 0;
    while (l < r) {
        if (a[l] + a[r] <= x) {
            cnt += r - l;      // a[l] cocok dengan a[l+1], ..., a[r]
            l++;
        } else {
            r--;               // a[r] terlalu mahal, bahkan dengan yang termurah
        }
    }
    cout << cnt << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, x] = readInts();
const a = Float64Array.from(readInts()).sort();   // typed array: urut numerik
let l = 0, r = n - 1, cnt = 0;
while (l < r) {
  if (a[l] + a[r] <= x) {
    cnt += r - l;
    l++;
  } else r--;
}
console.log(String(cnt));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, x = map(int, input().split())
a = sorted(map(int, input().split()))
l, r, cnt = 0, n - 1, 0
while l < r:
    if a[l] + a[r] <= x:
        cnt += r - l
        l += 1
    else:
        r -= 1
print(cnt)
CODE,
        ],
        'editorial' => '<p>Urutkan harga. Taruh <code>l</code> di barang termurah dan <code>r</code> di barang termahal.</p>
<ul><li>Jika <code>a[l] + a[r] ≤ X</code>, maka a[l] juga cocok dengan <em>semua</em> barang di antara l dan r (semuanya ≤ a[r]). Tambah <code>r − l</code> pasangan sekaligus, lalu <code>l++</code>: semua pasangan yang memakai a[l] sudah terhitung.</li>
<li>Jika lebih dari X, a[r] terlalu mahal bahkan dipasangkan dengan yang termurah: buang, <code>r−−</code>.</li></ul>
<p>Setiap langkah membuang satu barang dari pertimbangan, jadi totalnya O(N) setelah sort O(N log N). Jawaban bisa sampai N(N−1)/2 ≈ 2 · 10<sup>10</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Urutan barang tidak penting untuk banyak pasangan, jadi boleh diurutkan.',
            'Pada array urut, jika a[l] + a[r] ≤ X, berapa pasangan yang langsung ketahuan valid dengan a[l]?',
            'Two pointers dari kedua ujung: jika valid tambah r − l lalu l++, jika tidak r−−. Jawaban long long.',
        ],
    ],

    [
        'slug' => 'jendela-k-jenis',
        'lesson' => 'two-pointers',
        'title' => 'Playlist Paling Beragam',
        'difficulty' => 'Sedang',
        'tags' => ['sliding window', 'frekuensi'],
        'statement' => '<p>Sebuah radio memutar <strong>N</strong> lagu berurutan; lagu ke-i bergenre <code>g<sub>i</sub></code>. Kamu ingin merekam satu potongan siaran (lagu-lagu <strong>berurutan</strong>) yang berisi paling banyak <strong>K</strong> genre berbeda.</p>
<p>Berapa banyak lagu terbanyak yang bisa ada di potongan itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>g<sub>1</sub> … g<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: panjang potongan terpanjang.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 200 000</li><li>1 ≤ g<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "9 2\n1 2 1 3 3 2 2 3 1\n", 'explanation' => 'Potongan lagu ke-4 sampai ke-8 (genre 3 3 2 2 3) hanya berisi 2 genre dan panjangnya 5. Tidak ada potongan lebih panjang dengan ≤ 2 genre.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $kinds, bool $runs = false) {
                $g = [];
                while (count($g) < $n) {
                    $v = mt_rand(1, $kinds);
                    $len = $runs ? mt_rand(1, 50) : 1;
                    for ($i = 0; $i < $len && count($g) < $n; $i++) {
                        $g[] = $v;
                    }
                }

                return "$n $k\n".T::join($g)."\n";
            };
            $big = function (int $n, int $k) {
                return "$n $k\n".T::join(T::arr($n, 1, 1000000000))."\n";
            };

            return [
                "1 1\n5\n", "5 1\n1 2 3 4 5\n", "5 5\n1 2 3 4 5\n", $make(20, 2, 4), $make(1000, 3, 10, true),
                $make(200000, 5, 30, true), $make(200000, 100, 1000), $big(200000, 50000), $make(200000, 1, 2, true),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $g = T::ints($lines[1]);
            $cnt = [];
            $kinds = 0;
            $l = 0;
            $best = 0;
            for ($r = 0; $r < $n; $r++) {
                $v = $g[$r];
                if (($cnt[$v] ?? 0) === 0) {
                    $kinds++;
                }
                $cnt[$v] = ($cnt[$v] ?? 0) + 1;
                while ($kinds > $k) {
                    $u = $g[$l++];
                    if (--$cnt[$u] === 0) {
                        $kinds--;
                    }
                }
                $best = max($best, $r - $l + 1);
            }

            return (string) $best;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<int> g(n);
    for (auto& x : g) cin >> x;

    // sliding window: jaga banyak genre berbeda di jendela <= k

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const g = readInts();
// sliding window: jaga banyak genre berbeda di jendela <= k
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
g = list(map(int, input().split()))
# sliding window: jaga banyak genre berbeda di jendela <= k
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<int> g(n);
    for (auto& x : g) cin >> x;

    map<int, int> cnt;      // genre -> banyak lagu genre itu di jendela
    int l = 0, best = 0;
    for (int r = 0; r < n; r++) {
        cnt[g[r]]++;
        while ((int)cnt.size() > k) {            // terlalu banyak genre
            if (--cnt[g[l]] == 0) cnt.erase(g[l]);
            l++;
        }
        best = max(best, r - l + 1);
    }
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const g = readInts();
const cnt = new Map();
let l = 0, best = 0;
for (let r = 0; r < n; r++) {
  cnt.set(g[r], (cnt.get(g[r]) || 0) + 1);
  while (cnt.size > k) {
    const c = cnt.get(g[l]) - 1;
    if (c === 0) cnt.delete(g[l]);
    else cnt.set(g[l], c);
    l++;
  }
  if (r - l + 1 > best) best = r - l + 1;
}
console.log(String(best));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
g = list(map(int, input().split()))
cnt = {}
l = best = 0
for r in range(n):
    cnt[g[r]] = cnt.get(g[r], 0) + 1
    while len(cnt) > k:
        cnt[g[l]] -= 1
        if cnt[g[l]] == 0:
            del cnt[g[l]]
        l += 1
    best = max(best, r - l + 1)
print(best)
CODE,
        ],
        'editorial' => '<p>Sliding window ukuran variabel. Syaratnya monoton: jika jendela [l, r] sudah punya lebih dari K genre, jendela yang lebih lebar pasti juga.</p>
<p>Simpan <code>cnt[genre]</code> untuk lagu-lagu di jendela. Banyak genre berbeda adalah banyak entri dengan cnt &gt; 0 (di C++ cukup <code>cnt.size()</code> jika entri bernilai 0 dihapus).</p>
<ol><li>Masukkan lagu r.</li><li>Selama genre berbeda &gt; K, keluarkan lagu l dan majukan l. Jika cnt genre itu menjadi 0, hapus entrinya.</li><li>Jendela [l, r] kini valid: perbarui jawaban dengan r − l + 1.</li></ol>
<p>Setiap lagu masuk dan keluar paling banyak sekali: <code>O(N log N)</code> dengan map, atau O(N) setelah kompresi nilai genre.</p>',
        'hints' => [
            'Jika potongan [l, r] sudah punya lebih dari K genre, apakah memperpanjangnya bisa menolong?',
            'Pakai sliding window: tambah lagu dari kanan, buang dari kiri selama genre berbeda > K.',
            'Simpan banyak lagu per genre di map; hapus entri saat menjadi 0 agar map.size() = banyak genre berbeda.',
        ],
    ],

    [
        'slug' => 'hitung-subarray-hemat',
        'lesson' => 'two-pointers',
        'title' => 'Menghitung Belanja Hemat',
        'difficulty' => 'Sedang',
        'tags' => ['sliding window', 'counting'],
        'statement' => '<p>Selama <strong>N</strong> hari, Dina mencatat pengeluarannya <code>a<sub>1</sub>, …, a<sub>N</sub></code> (tidak negatif). Sebuah rentang hari berurutan disebut <strong>hemat</strong> jika total pengeluarannya paling banyak <strong>S</strong>.</p>
<p>Ada berapa rentang hari (tidak kosong) yang hemat?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N S</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak rentang hemat.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>0 ≤ S ≤ 10<sup>15</sup></li></ul>',
        'samples' => [
            ['input' => "5 6\n2 4 1 3 5\n", 'explanation' => 'Rentang hemat yang berakhir di hari 1: [2]. Hari 2: [4] dan [2, 4]. Hari 3: [1] dan [4, 1]. Hari 4: [3] dan [1, 3]. Hari 5: hanya [5]. Totalnya 8.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi, int $s) {
                return "$n $s\n".T::join(T::arr($n, 0, $hi))."\n";
            };

            return [
                "1 0\n0\n", "1 0\n5\n", "3 100\n1 2 3\n", $make(10, 10, 15), $make(1000, 1000, 5000),
                $make(200000, 1000000000, 1000000000000000), $make(200000, 1000000000, 3000000000), $make(200000, 0, 0), $make(200000, 3, 2),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $s] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $l = 0;
            $sum = 0;
            $cnt = 0;
            for ($r = 0; $r < $n; $r++) {
                $sum += $a[$r];
                while ($sum > $s) {
                    $sum -= $a[$l++];
                }
                $cnt += $r - $l + 1;
            }

            return (string) $cnt;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long s;
    cin >> n >> s;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // untuk setiap r, berapa rentang hemat yang berakhir di r?

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, s] = readLine().split(" ").map(Number);   // s sampai 1e15: masih aman di Number
const a = readInts();
// untuk setiap r, berapa rentang hemat yang berakhir di r?
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, s = map(int, input().split())
a = list(map(int, input().split()))
# untuk setiap r, berapa rentang hemat yang berakhir di r?
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
    long long s;
    cin >> n >> s;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    long long sum = 0, cnt = 0;
    int l = 0;
    for (int r = 0; r < n; r++) {
        sum += a[r];
        while (sum > s) sum -= a[l++];   // persempit sampai hemat lagi (bisa sampai kosong)
        cnt += r - l + 1;                // [l..r], [l+1..r], ..., [r..r] semuanya hemat
    }
    cout << cnt << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, s] = readLine().split(" ").map(Number);
const a = readInts();
// Jumlah jendela <= s + 1e9 < 2^53, aman di Number
let sum = 0, cnt = 0, l = 0;
for (let r = 0; r < n; r++) {
  sum += a[r];
  while (sum > s) sum -= a[l++];
  cnt += r - l + 1;
}
console.log(String(cnt));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, s = map(int, input().split())
a = list(map(int, input().split()))
total = cnt = l = 0
for r in range(n):
    total += a[r]
    while total > s:
        total -= a[l]
        l += 1
    cnt += r - l + 1
print(cnt)
CODE,
        ],
        'editorial' => '<p>Karena pengeluaran tidak negatif, jika rentang [l, r] hemat maka setiap rentang yang lebih pendek dengan ujung kanan sama, [l+1, r], …, [r, r], juga hemat. Syaratnya monoton.</p>
<p>Untuk setiap ujung kanan r, cari ujung kiri <strong>terkecil</strong> l sehingga [l, r] hemat. Seiring r maju, l juga hanya maju (sliding window). Banyak rentang hemat yang berakhir di r adalah <code>r − l + 1</code> (bisa 0 jika a[r] sendiri sudah melebihi S; saat itu l = r + 1).</p>
<p>Total <code>O(N)</code>. Jawabannya bisa sampai N(N+1)/2 ≈ 2 · 10<sup>10</sup>, dan S sampai 10<sup>15</sup>: semua harus <code>long long</code>.</p>',
        'hints' => [
            'Jika sebuah rentang hemat, apakah rentang yang lebih pendek di dalamnya juga hemat?',
            'Untuk setiap ujung kanan r, cukup cari ujung kiri terkecil l. Saat r maju, apakah l pernah perlu mundur?',
            'Sliding window: sum += a[r]; while (sum > S) sum −= a[l++]; jawaban += r − l + 1. Pakai long long.',
        ],
    ],

    // ───────────────────────── Kadane ─────────────────────────
    [
        'slug' => 'jual-beli-saham',
        'lesson' => 'kadane',
        'title' => 'Jual Beli Saham Sekali',
        'difficulty' => 'Mudah',
        'tags' => ['kadane', 'prefix minimum'],
        'statement' => '<p>Kamu tahu harga sebuah saham selama <strong>N</strong> hari ke depan: <code>p<sub>1</sub>, …, p<sub>N</sub></code>. Kamu boleh membeli satu lembar pada suatu hari, lalu menjualnya pada hari yang <strong>sesudahnya</strong> (bukan hari yang sama). Kamu juga boleh tidak bertransaksi sama sekali.</p>
<p>Berapa keuntungan terbesar yang bisa didapat?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>p<sub>1</sub> … p<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: keuntungan terbesar (0 jika tidak ada transaksi yang menguntungkan).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ p<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "7\n7 1 5 3 6 4 2\n", 'explanation' => 'Beli di hari ke-2 (harga 1), jual di hari ke-5 (harga 6): untung 5.'],
            ['input' => "4\n9 7 4 1\n", 'explanation' => 'Harga terus turun, jadi lebih baik tidak bertransaksi: untung 0.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };
            $down = function (int $n) {
                return "$n\n".T::join(range($n, 1))."\n";
            };

            return ["1\n5\n", "2\n3 8\n", $make(10, 20), $make(1000, 1000000000), $make(200000, 1000000000), $down(200000), $make(200000, 2)];
        },
        'solve' => function (string $input) {
            $p = T::ints(T::lines($input)[1]);
            $min = PHP_INT_MAX;
            $best = 0;
            foreach ($p as $x) {
                if ($x - $min > $best) {
                    $best = $x - $min;
                }
                $min = min($min, $x);
            }

            return (string) $best;
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
    vector<long long> p(n);
    for (auto& x : p) cin >> x;

    // keuntungan terbesar

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const p = readInts();
// keuntungan terbesar
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
p = list(map(int, input().split()))
# keuntungan terbesar
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
    vector<long long> p(n);
    for (auto& x : p) cin >> x;

    long long termurah = LLONG_MAX, best = 0;   // tidak bertransaksi = 0
    for (int i = 0; i < n; i++) {
        if (termurah != LLONG_MAX) best = max(best, p[i] - termurah);  // jual hari ini
        termurah = min(termurah, p[i]);                                // calon hari beli
    }
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const p = readInts();
let termurah = Infinity, best = 0;
for (const x of p) {
  if (x - termurah > best) best = x - termurah;
  if (x < termurah) termurah = x;
}
console.log(String(best));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
p = list(map(int, input().split()))
termurah = float('inf')
best = 0
for x in p:
    best = max(best, x - termurah)
    termurah = min(termurah, x)
print(int(best))
CODE,
        ],
        'editorial' => '<p>Jika kita menjual di hari j, keuntungan terbaiknya adalah <code>p<sub>j</sub> − (harga termurah sebelum j)</code>. Jadi cukup satu kali jalan sambil menyimpan harga termurah sejauh ini.</p>
<p>Ini sebenarnya <strong>Kadane</strong> pada selisih harian <code>d<sub>i</sub> = p<sub>i</sub> − p<sub>i−1</sub></code>: untung membeli di hari i dan menjual di hari j adalah jumlah <code>d<sub>i+1</sub> + … + d<sub>j</sub></code>, yaitu sebuah subarray. Bedanya, subarray kosong (tidak bertransaksi) diizinkan, sehingga jawabannya minimal 0.</p>
<p>O(N), dan jawaban muat di <code>long long</code>.</p>',
        'hints' => [
            'Bayangkan kamu pasti menjual di hari j. Hari beli terbaik yang mana?',
            'Simpan harga termurah dari hari-hari sebelumnya sambil berjalan.',
            'best = max(best, p[j] − termurah) lalu termurah = min(termurah, p[j]). Mulai best dari 0.',
        ],
    ],

    [
        'slug' => 'subarray-melingkar',
        'lesson' => 'kadane',
        'title' => 'Meja Bundar',
        'difficulty' => 'Sedang',
        'tags' => ['kadane', 'melingkar'],
        'statement' => '<p><strong>N</strong> orang duduk melingkar di meja bundar; orang ke-i punya nilai keceriaan <code>a<sub>i</sub></code> (boleh negatif). Orang ke-N duduk bersebelahan dengan orang ke-1.</p>
<p>Kamu ingin mengajak <strong>sekelompok orang yang duduk berdampingan</strong> (paling sedikit satu orang, paling banyak semua) sehingga jumlah keceriaan mereka sebesar mungkin. Berapa jumlah terbesar itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: jumlah keceriaan terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n5 -3 -4 2 4\n", 'explanation' => 'Kelompok terbaik melingkar: orang ke-4, ke-5, lalu ke-1 (2 + 4 + 5 = 11).'],
            ['input' => "3\n-2 -5 -1\n", 'explanation' => 'Semua negatif: ajak satu orang dengan nilai terbesar, −1.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return [
                "1\n-7\n", "2\n3 -1\n", $make(10, -10, 10), $make(1000, -1000, 1000), $make(200000, -1000000000, 1000000000),
                $make(200000, -1000000000, -1), $make(200000, 1, 1000000000), $make(200000, -1000000000, 900000000),
            ];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $total = array_sum($a);
            $curMax = $bestMax = $a[0];
            $curMin = $bestMin = $a[0];
            for ($i = 1, $n = count($a); $i < $n; $i++) {
                $x = $a[$i];
                $curMax = max($x, $curMax + $x);
                $bestMax = max($bestMax, $curMax);
                $curMin = min($x, $curMin + $x);
                $bestMin = min($bestMin, $curMin);
            }
            if ($bestMax < 0) {
                return (string) $bestMax;
            }

            return (string) max($bestMax, $total - $bestMin);
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // Kasus 1: kelompok tidak melewati batas N -> 1
    // Kasus 2: kelompok melingkar

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// Kasus 1: kelompok tidak melewati batas; Kasus 2: melingkar
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# Kasus 1: kelompok tidak melewati batas; Kasus 2: melingkar
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    long long total = 0;
    for (long long x : a) total += x;

    // Kadane untuk maksimum dan minimum sekaligus
    long long curMax = a[0], bestMax = a[0];
    long long curMin = a[0], bestMin = a[0];
    for (int i = 1; i < n; i++) {
        curMax = max(a[i], curMax + a[i]);
        bestMax = max(bestMax, curMax);
        curMin = min(a[i], curMin + a[i]);
        bestMin = min(bestMin, curMin);
    }

    // Semua negatif: kelompok melingkar "total - bestMin" akan kosong, tidak sah
    if (bestMax < 0) cout << bestMax << '\n';
    else cout << max(bestMax, total - bestMin) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// Nilai sampai 2e14: aman di Number
let total = 0;
for (const x of a) total += x;
let curMax = a[0], bestMax = a[0], curMin = a[0], bestMin = a[0];
for (let i = 1; i < n; i++) {
  curMax = Math.max(a[i], curMax + a[i]);
  bestMax = Math.max(bestMax, curMax);
  curMin = Math.min(a[i], curMin + a[i]);
  bestMin = Math.min(bestMin, curMin);
}
console.log(String(bestMax < 0 ? bestMax : Math.max(bestMax, total - bestMin)));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
total = sum(a)
cur_max = best_max = cur_min = best_min = a[0]
for x in a[1:]:
    cur_max = max(x, cur_max + x)
    best_max = max(best_max, cur_max)
    cur_min = min(x, cur_min + x)
    best_min = min(best_min, cur_min)
print(best_max if best_max < 0 else max(best_max, total - best_min))
CODE,
        ],
        'editorial' => '<p>Kelompok terbaik entah <strong>tidak melewati</strong> batas antara orang ke-N dan ke-1, entah <strong>melewatinya</strong>.</p>
<ul><li>Kasus pertama: subarray biasa, jawabannya Kadane maksimum.</li>
<li>Kasus kedua: kelompok yang melingkar adalah semua orang <em>kecuali</em> sebuah subarray di tengah. Agar jumlahnya maksimum, subarray yang dibuang harus berjumlah <strong>minimum</strong>: jawabannya <code>total − (Kadane minimum)</code>.</li></ul>
<p>Jawaban = max keduanya. <strong>Pengecualian:</strong> jika semua bilangan negatif, Kadane minimum mengambil seluruh array dan <code>total − bestMin = 0</code> berarti kelompok kosong, yang tidak sah. Dalam kasus itu, jawabannya Kadane maksimum (elemen terbesar).</p>
<p>O(N).</p>',
        'hints' => [
            'Pisahkan dua kasus: kelompok yang tidak melewati batas N→1, dan yang melewatinya.',
            'Kelompok melingkar = semua orang dikurangi satu subarray di tengah. Subarray mana yang sebaiknya dibuang?',
            'Jawaban = max(Kadane maksimum, total − Kadane minimum), kecuali jika semua negatif (jawabannya Kadane maksimum).',
        ],
    ],

    [
        'slug' => 'hapus-satu-elemen',
        'lesson' => 'kadane',
        'title' => 'Potong Satu Hari Buruk',
        'difficulty' => 'Sulit',
        'tags' => ['kadane', 'dp dua state'],
        'statement' => '<p>Laporan keuangan sebuah warung selama <strong>N</strong> hari berisi untung/rugi harian <code>a<sub>1</sub>, …, a<sub>N</sub></code>. Pemilik ingin memamerkan satu periode hari <strong>berurutan</strong> dengan total sebesar mungkin.</p>
<p>Karena ia sedikit curang, ia boleh <strong>menghapus paling banyak satu hari</strong> dari periode itu (hari-hari lain tetap harus berurutan). Periode yang dipamerkan tetap harus berisi paling sedikit satu hari setelah penghapusan.</p>
<p>Berapa total terbesar yang bisa dipamerkan?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: total terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6\n3 -1 4 -9 5 2\n", 'explanation' => 'Ambil hari 1–6 lalu hapus hari ke-4 (−9): 3 − 1 + 4 + 5 + 2 = 13.'],
            ['input' => "3\n-4 -2 -7\n", 'explanation' => 'Semua rugi. Periode minimal satu hari, jadi pamerkan hari ke-2 saja: −2.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $lo, int $hi) {
                return "$n\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return [
                "1\n-5\n", "2\n-5 3\n", "2\n4 -1\n", $make(10, -10, 10), $make(1000, -1000, 1000),
                $make(200000, -1000000000, 1000000000), $make(200000, -1000000000, -1), $make(200000, -10, 3), $make(200000, 1, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $keep = $a[0];
            $del = PHP_INT_MIN;
            $best = $a[0];
            for ($i = 1, $n = count($a); $i < $n; $i++) {
                $x = $a[$i];
                $nd = $del === PHP_INT_MIN ? $keep : max($keep, $del + $x);
                $keep = max($x, $keep + $x);
                $del = $nd;
                $best = max($best, $keep, $del);
            }

            return (string) $best;
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // Dua state untuk periode yang berakhir di hari i:
    //   utuh[i]  = belum menghapus apa pun
    //   hapus[i] = sudah menghapus tepat satu hari

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// Dua state: utuh (belum menghapus) dan hapus (sudah menghapus satu hari)
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# Dua state: utuh (belum menghapus) dan hapus (sudah menghapus satu hari)
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    const long long NEG = LLONG_MIN / 4;   // "tidak mungkin", aman dijumlahkan
    long long utuh = a[0];                 // periode berakhir di hari 0, tanpa hapus
    long long hapus = NEG;                 // belum ada hari sebelumnya untuk dipertahankan
    long long best = a[0];
    for (int i = 1; i < n; i++) {
        // hapus hari i (periode utuh sampai i-1 dipertahankan), atau sudah hapus sebelumnya lalu sambung a[i]
        long long hapusBaru = max(utuh, hapus + a[i]);
        long long utuhBaru = max(a[i], utuh + a[i]);   // Kadane biasa
        utuh = utuhBaru;
        hapus = hapusBaru;
        best = max(best, max(utuh, hapus));
    }
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
let utuh = a[0], hapus = -Infinity, best = a[0];
for (let i = 1; i < n; i++) {
  const hapusBaru = Math.max(utuh, hapus + a[i]);
  utuh = Math.max(a[i], utuh + a[i]);
  hapus = hapusBaru;
  best = Math.max(best, utuh, hapus);
}
console.log(String(best));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
utuh = a[0]
hapus = float('-inf')
best = a[0]
for x in a[1:]:
    hapus_baru = max(utuh, hapus + x)
    utuh = max(x, utuh + x)
    hapus = hapus_baru
    best = max(best, utuh, hapus)
print(int(best))
CODE,
        ],
        'editorial' => '<p>Perluas state Kadane dengan satu informasi: <strong>apakah sudah menghapus?</strong> Untuk periode yang berakhir di hari i:</p>
<ul><li><code>utuh[i]</code> = total terbesar tanpa penghapusan: Kadane biasa, <code>max(a[i], utuh[i−1] + a[i])</code>.</li>
<li><code>hapus[i]</code> = total terbesar dengan tepat satu hari dihapus. Dua kemungkinan: hari i itulah yang dihapus (sisanya periode utuh yang berakhir di i−1: <code>utuh[i−1]</code>), atau penghapusan sudah terjadi sebelumnya dan hari i disambung (<code>hapus[i−1] + a[i]</code>).</li></ul>
<p>Jawaban = maksimum semua <code>utuh[i]</code> dan <code>hapus[i]</code>. Karena <code>hapus[i]</code> selalu menyisakan periode utuh yang tidak kosong (berakhir di i−1), syarat "minimal satu hari" otomatis terpenuhi; di hari pertama <code>hapus</code> belum mungkin.</p>
<p>Pola "tambah satu dimensi kecil ke state" ini akan sering muncul di track DP. O(N).</p>',
        'hints' => [
            'Kadane biasa tidak tahu apakah sudah memakai jatah hapus. Tambahkan informasi itu ke state.',
            'Simpan dua nilai untuk periode yang berakhir di hari i: utuh (belum menghapus) dan hapus (sudah menghapus satu).',
            'hapus[i] = max(utuh[i−1], hapus[i−1] + a[i]); utuh[i] = max(a[i], utuh[i−1] + a[i]). Jawaban = max semua.',
        ],
    ],

    // ───────────────────────── Monotonic Stack ─────────────────────────
    [
        'slug' => 'menara-sinyal',
        'lesson' => 'monotonic-stack',
        'title' => 'Menara Sinyal',
        'difficulty' => 'Mudah',
        'tags' => ['monotonic stack', 'previous greater'],
        'statement' => '<p>Di sepanjang jalan lurus berdiri <strong>N</strong> menara, menara ke-i setinggi <code>h<sub>i</sub></code>. Setiap menara memancarkan sinyal ke arah kiri, dan sinyal itu diterima oleh menara <strong>terdekat di kiri yang lebih tinggi</strong> (tinggi sama tidak dihitung).</p>
<p>Untuk setiap menara, cetak nomor menara yang menerima sinyalnya, atau <code>0</code> jika tidak ada. Menara dinomori 1 sampai N dari kiri.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>h<sub>1</sub> … h<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris berisi N bilangan.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ h<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6\n6 9 5 7 7 4\n", 'explanation' => 'Menara 3 (tinggi 5) → menara 2 (9). Menara 4 (7) melewati menara 3 yang lebih pendek → menara 2. Menara 5 (7) tidak diterima menara 4 yang sama tinggi → menara 2. Menara 6 (4) → menara 5.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1\n5\n", "4\n3 3 3 3\n", $make(10, 10), $make(1000, 1000000000), $make(200000, 1000000000),
                "200000\n".T::join(range(200000, 1))."\n", "200000\n".T::join(range(1, 200000))."\n", $make(200000, 3),
            ];
        },
        'solve' => function (string $input) {
            $h = T::ints(T::lines($input)[1]);
            $st = [];
            $ans = [];
            foreach ($h as $i => $x) {
                while ($st && $h[end($st)] <= $x) {
                    array_pop($st);
                }
                $ans[] = $st ? end($st) + 1 : 0;
                $st[] = $i;
            }

            return T::join($ans);
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
    vector<long long> h(n + 1);
    for (int i = 1; i <= n; i++) cin >> h[i];

    // monotonic stack berisi nomor menara

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const h = readInts();
// monotonic stack berisi indeks menara
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
h = list(map(int, input().split()))
# monotonic stack berisi indeks menara
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
    vector<long long> h(n + 1);
    for (int i = 1; i <= n; i++) cin >> h[i];

    vector<int> st;          // nomor menara, tingginya menurun tegas dari dasar ke puncak
    string out;
    for (int i = 1; i <= n; i++) {
        while (!st.empty() && h[st.back()] <= h[i]) st.pop_back();   // tidak lebih tinggi: tak berguna lagi
        out += to_string(st.empty() ? 0 : st.back());
        out += (i == n ? '\n' : ' ');
        st.push_back(i);
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const h = readInts();
const st = [];
const ans = new Array(n);
for (let i = 0; i < n; i++) {
  while (st.length && h[st[st.length - 1]] <= h[i]) st.pop();
  ans[i] = st.length ? st[st.length - 1] + 1 : 0;
  st.push(i);
}
console.log(ans.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
h = list(map(int, input().split()))
st = []
ans = []
for i, x in enumerate(h):
    while st and h[st[-1]] <= x:
        st.pop()
    ans.append(st[-1] + 1 if st else 0)
    st.append(i)
print(" ".join(map(str, ans)))
CODE,
        ],
        'editorial' => '<p>Ini <strong>previous greater element</strong>. Memeriksa semua menara di kiri untuk setiap menara butuh O(N²).</p>
<p>Pakai stack berisi nomor menara. Saat menara i datang, buang dari puncak semua menara yang tingginya <strong>≤ h<sub>i</sub></strong>: mereka tidak akan pernah menerima sinyal dari menara mana pun di kanan i, karena menara i lebih dekat dan tidak lebih pendek. Setelah pembersihan, puncak stack adalah jawaban untuk i. Lalu push i.</p>
<p>Setiap menara masuk dan keluar paling banyak sekali: O(N). Perhatikan tanda <code>≤</code>: menara yang sama tinggi tidak menerima sinyal, jadi ikut dibuang.</p>',
        'hints' => [
            'Jika menara j di kiri i dan h[j] ≤ h[i], apakah j masih bisa menjadi jawaban untuk menara di kanan i?',
            'Simpan hanya kandidat yang mungkin berguna, dalam stack. Tingginya akan menurun dari dasar ke puncak.',
            'Untuk setiap i: pop selama h[top] ≤ h[i]; jawaban = top (atau 0); lalu push i.',
        ],
    ],

    [
        'slug' => 'air-hujan',
        'lesson' => 'monotonic-stack',
        'title' => 'Air Hujan Tertampung',
        'difficulty' => 'Sedang',
        'tags' => ['monotonic stack', 'two pointers'],
        'statement' => '<p>Sebuah dinding tersusun dari <strong>N</strong> kolom bata berlebar 1; kolom ke-i setinggi <code>h<sub>i</sub></code>. Setelah hujan deras, air tertampung di cekungan antar kolom (air di luar kolom paling kiri dan paling kanan tumpah).</p>
<p>Berapa total satuan air yang tertampung?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>h<sub>1</sub> … h<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: total air.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ h<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "12\n0 1 0 2 1 0 1 3 2 1 2 1\n", 'explanation' => 'Air tertampung 1 di kolom 3, 1 + 2 + 1 di kolom 5–7, dan 1 di kolom 10: total 6.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 0, $hi))."\n";
            };
            $valley = function (int $n) {
                $h = [];
                for ($i = 0; $i < $n; $i++) {
                    $h[] = abs($i - intdiv($n, 2)) * 5000;
                }

                return "$n\n".T::join($h)."\n";
            };

            return ["1\n5\n", "3\n2 0 2\n", "3\n0 5 0\n", $make(12, 5), $make(1000, 1000), $make(200000, 1000000000), $valley(200000), $make(200000, 2)];
        },
        'solve' => function (string $input) {
            $h = T::ints(T::lines($input)[1]);
            $n = count($h);
            $l = 0;
            $r = $n - 1;
            $lm = $rm = 0;
            $w = 0;
            while ($l < $r) {
                if ($h[$l] < $h[$r]) {
                    $lm = max($lm, $h[$l]);
                    $w += $lm - $h[$l];
                    $l++;
                } else {
                    $rm = max($rm, $h[$r]);
                    $w += $rm - $h[$r];
                    $r--;
                }
            }

            return (string) $w;
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
    vector<long long> h(n);
    for (auto& x : h) cin >> x;

    // total air tertampung

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const h = readInts();
// total air tertampung
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
h = list(map(int, input().split()))
# total air tertampung
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
    vector<long long> h(n);
    for (auto& x : h) cin >> x;

    // Monotonic stack: setiap kali menemukan dinding kanan, isi "kolam" di atas dasar yang di-pop
    vector<int> st;              // indeks, tinggi menurun dari dasar ke puncak
    long long air = 0;
    for (int i = 0; i < n; i++) {
        while (!st.empty() && h[st.back()] < h[i]) {
            int dasar = st.back();
            st.pop_back();
            if (st.empty()) break;                      // tidak ada dinding kiri
            int kiri = st.back();
            long long tinggi = min(h[kiri], h[i]) - h[dasar];
            long long lebar = i - kiri - 1;
            air += tinggi * lebar;                      // lapisan air di atas dasar
        }
        st.push_back(i);
    }
    cout << air << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const h = readInts();
// Versi two pointers: air di kolom i = min(maks kiri, maks kanan) - h[i]
let l = 0, r = n - 1, lm = 0, rm = 0, air = 0;
while (l < r) {
  if (h[l] < h[r]) {
    lm = Math.max(lm, h[l]);
    air += lm - h[l];
    l++;
  } else {
    rm = Math.max(rm, h[r]);
    air += rm - h[r];
    r--;
  }
}
console.log(String(air));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
h = list(map(int, input().split()))
st = []
air = 0
for i in range(n):
    while st and h[st[-1]] < h[i]:
        dasar = st.pop()
        if not st:
            break
        kiri = st[-1]
        air += (min(h[kiri], h[i]) - h[dasar]) * (i - kiri - 1)
    st.append(i)
print(air)
CODE,
        ],
        'editorial' => '<p>Air di atas kolom i setinggi <code>min(maks kiri, maks kanan) − h<sub>i</sub></code>. Ada dua cara O(N):</p>
<p><strong>Monotonic stack.</strong> Simpan indeks dengan tinggi menurun. Saat kolom i lebih tinggi dari puncak, puncak itu adalah <em>dasar</em> sebuah kolam: dinding kirinya adalah elemen di bawahnya di stack, dinding kanannya i. Tambahkan lapisan air setinggi <code>min(h[kiri], h[i]) − h[dasar]</code> selebar <code>i − kiri − 1</code>. Air terisi lapis demi lapis dari bawah.</p>
<p><strong>Two pointers.</strong> Majukan sisi yang lebih rendah; untuk sisi itu, batasnya pasti maksimum di sisinya sendiri, karena sisi seberang sudah dijamin lebih tinggi.</p>
<p>Total air bisa sampai 10<sup>9</sup> · 2 · 10<sup>5</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Berapa air di atas satu kolom? Itu bergantung pada kolom tertinggi di kirinya dan di kanannya.',
            'Dengan stack menurun: saat datang kolom yang lebih tinggi dari puncak, puncak itu adalah dasar sebuah kolam.',
            'Pop dasar; jika stack masih ada isinya, kiri = puncak baru; tambah (min(h[kiri], h[i]) − h[dasar]) × (i − kiri − 1).',
        ],
    ],

    [
        'slug' => 'jumlah-minimum',
        'lesson' => 'monotonic-stack',
        'title' => 'Jumlah Minimum Semua Subarray',
        'difficulty' => 'Sulit',
        'tags' => ['monotonic stack', 'kontribusi'],
        'statement' => '<p>Diberikan barisan <code>a<sub>1</sub>, …, a<sub>N</sub></code>. Untuk <strong>setiap</strong> subarray (bagian berurutan yang tidak kosong), ambil nilai minimumnya. Hitung jumlah semua nilai minimum itu.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: jumlah minimum semua subarray.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 1000</li></ul>',
        'samples' => [
            ['input' => "4\n3 1 2 4\n", 'explanation' => 'Minimum subarray: [3]=3, [1]=1, [2]=2, [4]=4, [3,1]=1, [1,2]=1, [2,4]=2, [3,1,2]=1, [1,2,4]=1, [3,1,2,4]=1. Jumlahnya 17.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $hi) {
                return "$n\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1\n7\n", "3\n2 2 2\n", $make(8, 5), $make(1000, 1000), $make(200000, 1000), $make(200000, 2),
                "200000\n".T::join(array_fill(0, 200000, 1000))."\n", "200000\n".T::join(array_map(fn ($i) => 1 + $i % 1000, range(0, 199999)))."\n",
            ];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $n = count($a);
            $left = $right = [];
            $st = [];
            for ($i = 0; $i < $n; $i++) {
                while ($st && $a[end($st)] >= $a[$i]) {
                    array_pop($st);
                }
                $left[$i] = $st ? end($st) : -1;
                $st[] = $i;
            }
            $st = [];
            for ($i = $n - 1; $i >= 0; $i--) {
                while ($st && $a[end($st)] > $a[$i]) {
                    array_pop($st);
                }
                $right[$i] = $st ? end($st) : $n;
                $st[] = $i;
            }
            $tot = 0;
            for ($i = 0; $i < $n; $i++) {
                $tot += $a[$i] * ($i - $left[$i]) * ($right[$i] - $i);
            }

            return (string) $tot;
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // Untuk setiap i: di berapa subarray a[i] menjadi minimum?

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// Untuk setiap i: di berapa subarray a[i] menjadi minimum?
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# Untuk setiap i: di berapa subarray a[i] menjadi minimum?
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    vector<int> kiri(n), kanan(n), st;
    // kiri[i] : indeks terdekat di kiri dengan nilai < a[i]   (tegas)
    for (int i = 0; i < n; i++) {
        while (!st.empty() && a[st.back()] >= a[i]) st.pop_back();
        kiri[i] = st.empty() ? -1 : st.back();
        st.push_back(i);
    }
    st.clear();
    // kanan[i]: indeks terdekat di kanan dengan nilai <= a[i]  (tidak tegas)
    for (int i = n - 1; i >= 0; i--) {
        while (!st.empty() && a[st.back()] > a[i]) st.pop_back();
        kanan[i] = st.empty() ? n : st.back();
        st.push_back(i);
    }

    long long total = 0;
    for (int i = 0; i < n; i++)
        total += a[i] * (long long)(i - kiri[i]) * (kanan[i] - i);   // banyak subarray dengan minimum a[i]
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const a = readInts();
// Total <= 1000 * n(n+1)/2 ≈ 2e13 < 2^53: aman di Number
const kiri = new Int32Array(n), kanan = new Int32Array(n);
let st = [];
for (let i = 0; i < n; i++) {
  while (st.length && a[st[st.length - 1]] >= a[i]) st.pop();
  kiri[i] = st.length ? st[st.length - 1] : -1;
  st.push(i);
}
st = [];
for (let i = n - 1; i >= 0; i--) {
  while (st.length && a[st[st.length - 1]] > a[i]) st.pop();
  kanan[i] = st.length ? st[st.length - 1] : n;
  st.push(i);
}
let total = 0;
for (let i = 0; i < n; i++) total += a[i] * (i - kiri[i]) * (kanan[i] - i);
console.log(String(total));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
kiri, kanan = [-1] * n, [n] * n
st = []
for i in range(n):
    while st and a[st[-1]] >= a[i]:
        st.pop()
    kiri[i] = st[-1] if st else -1
    st.append(i)
st = []
for i in range(n - 1, -1, -1):
    while st and a[st[-1]] > a[i]:
        st.pop()
    kanan[i] = st[-1] if st else n
    st.append(i)
print(sum(a[i] * (i - kiri[i]) * (kanan[i] - i) for i in range(n)))
CODE,
        ],
        'editorial' => '<p>Ada sekitar 2 · 10<sup>10</sup> subarray, jadi kita tidak bisa memeriksanya satu per satu. Gunakan <strong>teknik kontribusi</strong>: hitung berapa kali setiap a[i] menjadi minimum.</p>
<p>a[i] adalah minimum subarray [l, r] jika l dan r tidak melewati elemen yang lebih kecil. Dengan <code>kiri[i]</code> = indeks terdekat di kiri yang nilainya lebih kecil dan <code>kanan[i]</code> = indeks terdekat di kanan yang nilainya lebih kecil, ada <code>(i − kiri[i]) × (kanan[i] − i)</code> pilihan pasangan (l, r). Keduanya dihitung dengan monotonic stack dalam O(N).</p>
<p><strong>Jebakan nilai kembar:</strong> pada <code>[2, 2]</code>, subarray [2, 2] punya dua minimum. Agar terhitung tepat sekali, satu sisi memakai "lebih kecil tegas" (&lt;) dan sisi lain "lebih kecil atau sama" (≤). Jika keduanya &lt;, subarray itu dihitung dua kali.</p>
<p>Total bisa mencapai sekitar 2 · 10<sup>13</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Balik pertanyaannya: untuk setiap a[i], di berapa banyak subarray ia menjadi minimum?',
            'Subarray itu tidak boleh melewati elemen yang lebih kecil di kiri maupun kanan. Cari batas keduanya dengan monotonic stack.',
            'Kontribusi = a[i] × (i − kiri[i]) × (kanan[i] − i). Untuk nilai kembar, pakai < di satu sisi dan ≤ di sisi lain.',
        ],
    ],

    // ───────────────────────── Monotonic Deque ─────────────────────────
    [
        'slug' => 'harga-termurah-pekan',
        'lesson' => 'monotonic-deque',
        'title' => 'Harga Termurah Sepekan',
        'difficulty' => 'Mudah',
        'tags' => ['monotonic deque', 'sliding window'],
        'statement' => '<p>Sebuah aplikasi mencatat harga beras selama <strong>N</strong> hari: <code>p<sub>1</sub>, …, p<sub>N</sub></code>. Setiap hari mulai hari ke-K, aplikasi menampilkan <strong>harga termurah dalam K hari terakhir</strong> (termasuk hari itu).</p>
<p>Cetak semua angka yang ditampilkan, yaitu untuk hari ke-K sampai hari ke-N.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>p<sub>1</sub> … p<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu baris berisi N − K + 1 bilangan.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 200 000</li><li>1 ≤ p<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "8 3\n12 10 15 11 9 14 13 8\n", 'explanation' => 'Jendela hari 1–3: min 10; 2–4: 10; 3–5: 9; 4–6: 9; 5–7: 9; 6–8: 8.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $hi) {
                return "$n $k\n".T::join(T::arr($n, 1, $hi))."\n";
            };

            return [
                "1 1\n5\n", "5 5\n4 2 8 1 9\n", $make(10, 3, 20), $make(1000, 50, 1000000000), $make(200000, 1, 1000000000),
                $make(200000, 200000, 1000000000), $make(200000, 777, 1000000000), "200000 1000\n".T::join(range(1, 200000))."\n", "200000 1000\n".T::join(range(200000, 1))."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $dq = new SplDoublyLinkedList;
            $out = [];
            for ($i = 0; $i < $n; $i++) {
                while (! $dq->isEmpty() && $a[$dq->top()] >= $a[$i]) {
                    $dq->pop();
                }
                $dq->push($i);
                if ($dq->bottom() <= $i - $k) {
                    $dq->shift();
                }
                if ($i >= $k - 1) {
                    $out[] = $a[$dq->bottom()];
                }
            }

            return T::join($out);
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> p(n);
    for (auto& x : p) cin >> x;

    // deque monoton untuk MINIMUM jendela

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const p = readInts();
// deque monoton untuk MINIMUM jendela (pakai array + indeks kepala)
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
p = list(map(int, input().split()))
# deque monoton untuk MINIMUM jendela
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> p(n);
    for (auto& x : p) cin >> x;

    deque<int> dq;                 // indeks, harga MENAIK dari depan ke belakang
    string out;
    for (int i = 0; i < n; i++) {
        while (!dq.empty() && p[dq.back()] >= p[i]) dq.pop_back();   // lebih mahal & lebih tua: buang
        dq.push_back(i);
        if (dq.front() <= i - k) dq.pop_front();                     // keluar dari jendela
        if (i >= k - 1) {
            out += to_string(p[dq.front()]);
            out += (i + 1 == n ? '\n' : ' ');
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const p = readInts();
const dq = new Int32Array(n);   // deque sebagai array: [head, tail)
let head = 0, tail = 0;
const out = [];
for (let i = 0; i < n; i++) {
  while (tail > head && p[dq[tail - 1]] >= p[i]) tail--;
  dq[tail++] = i;
  if (dq[head] <= i - k) head++;
  if (i >= k - 1) out.push(p[dq[head]]);
}
console.log(out.join(" "));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
p = list(map(int, input().split()))
dq = deque()
out = []
for i in range(n):
    while dq and p[dq[-1]] >= p[i]:
        dq.pop()
    dq.append(i)
    if dq[0] <= i - k:
        dq.popleft()
    if i >= k - 1:
        out.append(p[dq[0]])
print(" ".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Ini <strong>sliding window minimum</strong>, kebalikan dari contoh di materi. Simpan indeks di deque dengan harga <strong>menaik</strong> dari depan ke belakang:</p>
<ol><li>Saat hari i datang, buang dari belakang semua hari yang harganya ≥ p<sub>i</sub>: mereka lebih mahal dan akan kedaluwarsa lebih dulu, jadi tidak akan pernah menjadi yang termurah.</li><li>Push i.</li><li>Jika indeks terdepan ≤ i − K, ia sudah keluar dari jendela: buang dari depan.</li><li>Depan deque adalah harga termurah jendela.</li></ol>
<p>Setiap hari masuk dan keluar paling banyak sekali: O(N). Di JavaScript, hindari <code>Array.shift()</code> yang O(n); pakai indeks kepala.</p>',
        'hints' => [
            'Ini kebalikan dari maksimum jendela geser.',
            'Jika hari j lebih tua dan harganya ≥ harga hari i, apakah j masih bisa menjadi yang termurah?',
            'Deque indeks dengan harga menaik: pop_back selama p[back] ≥ p[i], push i, pop_front jika front ≤ i − K, jawab p[front].',
        ],
    ],

    [
        'slug' => 'selisih-jendela',
        'lesson' => 'monotonic-deque',
        'title' => 'Suhu yang Stabil',
        'difficulty' => 'Sedang',
        'tags' => ['monotonic deque', 'two pointers'],
        'statement' => '<p>Sebuah sensor mencatat suhu ruang server setiap menit selama <strong>N</strong> menit: <code>t<sub>1</sub>, …, t<sub>N</sub></code>. Sebuah periode menit berurutan disebut <strong>stabil</strong> jika selisih suhu tertinggi dan terendah di periode itu paling banyak <strong>K</strong>.</p>
<p>Berapa panjang periode stabil terpanjang?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>t<sub>1</sub> … t<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: panjang periode stabil terpanjang.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ K ≤ 10<sup>9</sup></li><li>−10<sup>9</sup> ≤ t<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "8 3\n5 7 6 9 8 4 5 6\n", 'explanation' => 'Periode menit 2–5 (7 6 9 8) punya selisih 9 − 6 = 3 dan panjang 4. Periode menit 1–3 (5 7 6) dan 6–8 (4 5 6) hanya sepanjang 3.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $lo, int $hi) {
                return "$n $k\n".T::join(T::arr($n, $lo, $hi))."\n";
            };
            $walk = function (int $n, int $k, int $step) {
                $t = [0];
                for ($i = 1; $i < $n; $i++) {
                    $t[] = max(-1000000000, min(1000000000, $t[$i - 1] + mt_rand(-$step, $step)));
                }

                return "$n $k\n".T::join($t)."\n";
            };

            return [
                "1 0\n5\n", "4 0\n3 3 3 3\n", $make(10, 3, 0, 10), $make(1000, 100, 0, 1000), $walk(200000, 50, 10),
                $walk(200000, 1000, 100), $make(200000, 1000000000, -1000000000, 1000000000), $make(200000, 0, 1, 3), $make(200000, 2000000000 / 2, -1000000000, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $mx = new SplDoublyLinkedList;
            $mn = new SplDoublyLinkedList;
            $l = 0;
            $best = 0;
            for ($r = 0; $r < $n; $r++) {
                while (! $mx->isEmpty() && $a[$mx->top()] <= $a[$r]) {
                    $mx->pop();
                }
                while (! $mn->isEmpty() && $a[$mn->top()] >= $a[$r]) {
                    $mn->pop();
                }
                $mx->push($r);
                $mn->push($r);
                while ($a[$mx->bottom()] - $a[$mn->bottom()] > $k) {
                    $l++;
                    if ($mx->bottom() < $l) {
                        $mx->shift();
                    }
                    if ($mn->bottom() < $l) {
                        $mn->shift();
                    }
                }
                $best = max($best, $r - $l + 1);
            }

            return (string) $best;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    long long k;
    cin >> n >> k;
    vector<long long> t(n);
    for (auto& x : t) cin >> x;

    // jendela variabel + deque maksimum + deque minimum

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const t = readInts();
// jendela variabel + deque maksimum + deque minimum
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
t = list(map(int, input().split()))
# jendela variabel + deque maksimum + deque minimum
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
    long long k;
    cin >> n >> k;
    vector<long long> t(n);
    for (auto& x : t) cin >> x;

    deque<int> mx, mn;      // mx: suhu menurun (maks di depan), mn: suhu menaik (min di depan)
    int l = 0, best = 0;
    for (int r = 0; r < n; r++) {
        while (!mx.empty() && t[mx.back()] <= t[r]) mx.pop_back();
        while (!mn.empty() && t[mn.back()] >= t[r]) mn.pop_back();
        mx.push_back(r);
        mn.push_back(r);
        while (t[mx.front()] - t[mn.front()] > k) {   // tidak stabil: persempit
            l++;
            if (mx.front() < l) mx.pop_front();
            if (mn.front() < l) mn.pop_front();
        }
        best = max(best, r - l + 1);
    }
    cout << best << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const t = readInts();
const mx = new Int32Array(n), mn = new Int32Array(n);
let mxh = 0, mxt = 0, mnh = 0, mnt = 0, l = 0, best = 0;
for (let r = 0; r < n; r++) {
  while (mxt > mxh && t[mx[mxt - 1]] <= t[r]) mxt--;
  while (mnt > mnh && t[mn[mnt - 1]] >= t[r]) mnt--;
  mx[mxt++] = r;
  mn[mnt++] = r;
  while (t[mx[mxh]] - t[mn[mnh]] > k) {
    l++;
    if (mx[mxh] < l) mxh++;
    if (mn[mnh] < l) mnh++;
  }
  if (r - l + 1 > best) best = r - l + 1;
}
console.log(String(best));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
t = list(map(int, input().split()))
mx, mn = deque(), deque()
l = best = 0
for r in range(n):
    while mx and t[mx[-1]] <= t[r]:
        mx.pop()
    while mn and t[mn[-1]] >= t[r]:
        mn.pop()
    mx.append(r)
    mn.append(r)
    while t[mx[0]] - t[mn[0]] > k:
        l += 1
        if mx[0] < l:
            mx.popleft()
        if mn[0] < l:
            mn.popleft()
    best = max(best, r - l + 1)
print(best)
CODE,
        ],
        'editorial' => '<p>Syaratnya monoton: jika [l, r] tidak stabil, memperlebarnya tidak akan membuatnya stabil. Jadi pakai <strong>jendela variabel</strong>: untuk setiap r, majukan l selama selisih maks − min di jendela &gt; K.</p>
<p>Yang tersisa: mengetahui maksimum dan minimum jendela dengan cepat saat jendela berubah di kedua ujung. Jaga <strong>dua deque monoton</strong>: satu dengan suhu menurun (maksimum di depan), satu dengan suhu menaik (minimum di depan). Saat l maju, buang depan deque yang indeksnya &lt; l.</p>
<p>Setiap indeks masuk dan keluar masing-masing deque paling banyak sekali: O(N). (Alternatif lebih lambat namun sederhana: multiset berisi isi jendela, O(N log N).)</p>',
        'hints' => [
            'Jika periode [l, r] tidak stabil, apakah [l, r+1] bisa stabil? Pakai sliding window.',
            'Kamu butuh maksimum dan minimum jendela yang berubah di kedua ujung.',
            'Jaga dua deque monoton (maks menurun, min menaik). Majukan l selama t[maks] − t[min] > K, buang depan deque yang < l.',
        ],
    ],

    [
        'slug' => 'lompat-skor',
        'lesson' => 'monotonic-deque',
        'title' => 'Lompat Batu Bernilai',
        'difficulty' => 'Sulit',
        'tags' => ['dp', 'monotonic deque'],
        'statement' => '<p>Ada <strong>N</strong> batu berjajar di sungai, batu ke-i bernilai <code>v<sub>i</sub></code> (boleh negatif). Kamu berdiri di batu 1 dan harus sampai di batu N. Dari batu i, kamu boleh melompat ke batu i + 1, i + 2, …, sampai i + K (tidak boleh melewati N).</p>
<p>Skormu adalah jumlah nilai semua batu yang kamu injak, termasuk batu 1 dan batu N. Berapa skor terbesar?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>v<sub>1</sub> … v<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: skor terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 200 000</li><li>−10<sup>9</sup> ≤ v<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6 2\n1 -1 -2 4 -7 3\n", 'explanation' => 'Injak batu 1, 2, 4, 6: 1 − 1 + 4 + 3 = 7. Batu 3 (−2) dan 5 (−7) dilompati.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $lo, int $hi) {
                return "$n $k\n".T::join(T::arr($n, $lo, $hi))."\n";
            };

            return [
                "1 1\n-5\n", "3 1\n-1 -2 -3\n", $make(10, 3, -10, 10), $make(1000, 10, -1000, 1000), $make(200000, 1, -1000000000, 1000000000),
                $make(200000, 200000, -1000000000, 1000000000), $make(200000, 300, -1000000000, 1000000000), $make(200000, 5000, -1000000000, -1),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $v = T::ints($lines[1]);
            $dp = [$v[0]];
            $dq = new SplDoublyLinkedList;
            $dq->push(0);
            for ($i = 1; $i < $n; $i++) {
                if ($dq->bottom() < $i - $k) {
                    $dq->shift();
                }
                $dp[$i] = $v[$i] + $dp[$dq->bottom()];
                while (! $dq->isEmpty() && $dp[$dq->top()] <= $dp[$i]) {
                    $dq->pop();
                }
                $dq->push($i);
            }

            return (string) $dp[$n - 1];
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> v(n), dp(n);
    for (auto& x : v) cin >> x;

    // dp[i] = v[i] + max(dp[i-k], ..., dp[i-1])

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const v = readInts();
// dp[i] = v[i] + max(dp[i-k..i-1]) dengan deque
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
v = list(map(int, input().split()))
# dp[i] = v[i] + max(dp[i-k..i-1]) dengan deque
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> v(n), dp(n);
    for (auto& x : v) cin >> x;

    dp[0] = v[0];
    deque<int> dq;          // indeks, dp menurun dari depan: depan = max jendela
    dq.push_back(0);
    for (int i = 1; i < n; i++) {
        if (dq.front() < i - k) dq.pop_front();          // terlalu jauh untuk melompat ke i
        dp[i] = v[i] + dp[dq.front()];
        while (!dq.empty() && dp[dq.back()] <= dp[i]) dq.pop_back();
        dq.push_back(i);
    }
    cout << dp[n - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const v = readInts();
// Nilai |dp| <= 2e14: aman di Number
const dp = new Float64Array(n);
const dq = new Int32Array(n);
let head = 0, tail = 0;
dp[0] = v[0];
dq[tail++] = 0;
for (let i = 1; i < n; i++) {
  if (dq[head] < i - k) head++;
  dp[i] = v[i] + dp[dq[head]];
  while (tail > head && dp[dq[tail - 1]] <= dp[i]) tail--;
  dq[tail++] = i;
}
console.log(String(dp[n - 1]));
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
v = list(map(int, input().split()))
dp = [0] * n
dp[0] = v[0]
dq = deque([0])
for i in range(1, n):
    if dq[0] < i - k:
        dq.popleft()
    dp[i] = v[i] + dp[dq[0]]
    while dq and dp[dq[-1]] <= dp[i]:
        dq.pop()
    dq.append(i)
print(dp[n - 1])
CODE,
        ],
        'editorial' => '<p>DP: <code>dp[i]</code> = skor terbesar untuk sampai di batu i (dengan menginjaknya). Batu sebelumnya pasti salah satu dari i − K … i − 1, jadi</p>
<p style="text-align:center"><code>dp[i] = v[i] + max(dp[i−K], …, dp[i−1])</code></p>
<p>Menghitung max itu dengan loop membuat total O(N · K) = 4 · 10<sup>10</sup> pada kasus terburuk. Tetapi "max dari K nilai dp terakhir" adalah <strong>maksimum jendela geser</strong> atas array dp. Pakai deque monoton: sebelum menghitung dp[i], buang depan yang indeksnya &lt; i − K; dp[depan] adalah max-nya; setelah itu masukkan i dengan membuang dari belakang yang dp-nya ≤ dp[i].</p>
<p>Total O(N). Nilai bisa sampai 2 · 10<sup>14</sup>: pakai <code>long long</code>. Batu 1 dan N wajib diinjak, jadi jawabannya dp[N].</p>',
        'hints' => [
            'Tulis DP: skor terbaik untuk sampai di batu i bergantung pada batu-batu di jendela [i−K, i−1].',
            'dp[i] = v[i] + max(dp[i−K..i−1]). Bagian max-nya adalah sliding window maximum atas dp.',
            'Jaga deque indeks dengan dp menurun: buang depan yang < i−K, hitung dp[i] dari depan, lalu pop_back selama dp[back] ≤ dp[i] dan push i.',
        ],
    ],

    // ───────────────────────── Kompresi Koordinat ─────────────────────────
    [
        'slug' => 'nomor-baru',
        'lesson' => 'kompresi-koordinat',
        'title' => 'Nomor Urut Baru',
        'difficulty' => 'Mudah',
        'tags' => ['kompresi koordinat', 'sorting'],
        'statement' => '<p>Panitia lomba lari mencatat nomor punggung <strong>N</strong> pelari: <code>a<sub>1</sub>, …, a<sub>N</sub></code> (beberapa pelari bisa terdaftar dengan nomor sama karena salah ketik). Nomor-nomor itu sangat besar, jadi panitia ingin menggantinya dengan <strong>nomor urut baru</strong>:</p>
<ul><li>nomor terkecil yang muncul menjadi 1, nomor berbeda berikutnya menjadi 2, dan seterusnya;</li><li>nomor yang sama mendapat nomor baru yang sama.</li></ul>
<p>Cetak banyak nomor berbeda, lalu nomor baru setiap pelari sesuai urutan input.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Baris pertama berisi banyak nomor berbeda. Baris kedua berisi N nomor baru.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>−10<sup>18</sup> ≤ a<sub>i</sub> ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "6\n900000000000 15 -7 15 3000 -7\n", 'explanation' => 'Nomor berbeda terurut: −7, 15, 3000, 900000000000. Jadi −7 → 1, 15 → 2, 3000 → 3, 900000000000 → 4.'],
        ],
        'tests' => function () {
            $big = function (int $n, int $distinct) {
                $vals = [];
                for ($i = 0; $i < $distinct; $i++) {
                    $vals[] = mt_rand(-1000000000, 1000000000) * 1000000000 + mt_rand(0, 999999999);
                }
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = $vals[mt_rand(0, $distinct - 1)];
                }

                return "$n\n".T::join($a)."\n";
            };

            return [
                "1\n-1000000000000000000\n", "3\n5 5 5\n", $big(10, 4), $big(1000, 1000), $big(200000, 200000), $big(200000, 50),
                "4\n1000000000000000000 -1000000000000000000 0 1000000000000000000\n",
            ];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $b = array_values(array_unique($a));
            sort($b);
            $id = array_flip($b);

            return count($b)."\n".T::join(array_map(fn ($x) => $id[$x] + 1, $a));
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // sort + unique + lower_bound

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
// Nilai sampai 1e18 melebihi presisi Number: baca sebagai BigInt
const a = readLine().trim().split(/\s+/).map(BigInt);
// urutkan nilai berbeda, lalu petakan
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
# urutkan nilai berbeda, lalu petakan
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
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    vector<long long> b = a;
    sort(b.begin(), b.end());
    b.erase(unique(b.begin(), b.end()), b.end());

    string out = to_string(b.size()) + "\n";
    for (int i = 0; i < n; i++) {
        int r = lower_bound(b.begin(), b.end(), a[i]) - b.begin();
        out += to_string(r + 1);
        out += (i + 1 == n ? '\n' : ' ');
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const s = readLine().trim().split(/\s+/);
const a = s.map(BigInt);                         // 1e18 butuh BigInt
const b = [...new Set(s)].map(BigInt).sort((x, y) => (x < y ? -1 : x > y ? 1 : 0));
const id = new Map(b.map((v, i) => [v.toString(), i + 1]));
console.log(b.length + "\n" + a.map((v) => id.get(v.toString())).join(" "));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
b = sorted(set(a))
nomor = {v: i + 1 for i, v in enumerate(b)}
print(len(b))
print(" ".join(str(nomor[x]) for x in a))
CODE,
        ],
        'editorial' => '<p>Ini idiom kompresi koordinat:</p>
<ol><li>Salin a ke b, urutkan b.</li><li><code>b.erase(unique(b.begin(), b.end()), b.end())</code>: b kini berisi nomor berbeda yang terurut, dan <code>b.size()</code> adalah banyak nomor berbeda.</li><li>Nomor baru a[i] adalah posisinya di b ditambah 1: <code>lower_bound(b.begin(), b.end(), a[i]) − b.begin() + 1</code>.</li></ol>
<p>Total O(N log N). Nilai sampai 10<sup>18</sup> muat di <code>long long</code>; di JavaScript, nilai sebesar itu harus dibaca sebagai <code>BigInt</code> (Number hanya presisi sampai 9 · 10<sup>15</sup>).</p>',
        'hints' => [
            'Yang penting hanyalah urutan nomor, bukan nilainya.',
            'Urutkan salinan array, lalu buang yang kembar.',
            'Nomor baru = posisi di array unik + 1, cari dengan lower_bound.',
        ],
    ],

    [
        'slug' => 'cat-pagar',
        'lesson' => 'kompresi-koordinat',
        'title' => 'Mengecat Pagar Panjang',
        'difficulty' => 'Sedang',
        'tags' => ['kompresi koordinat', 'difference array'],
        'statement' => '<p>Sebuah pagar sangat panjang membentang dari titik 0 sampai 10<sup>9</sup>. Ada <strong>N</strong> tukang cat; tukang ke-i mengecat ruas <code>[L<sub>i</sub>, R<sub>i</sub>)</code> sebanyak satu lapis (ruas boleh saling tumpang tindih).</p>
<p>Bagian pagar dianggap <strong>awet</strong> jika tertutup paling sedikit <strong>K</strong> lapis cat. Berapa total panjang bagian pagar yang awet?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. N baris berikutnya masing-masing berisi <code>L R</code>.</p>',
        'output_format' => '<p>Satu bilangan: total panjang yang awet.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 200 000</li><li>0 ≤ L &lt; R ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 2\n0 10\n5 15\n8 20\n30 40\n", 'explanation' => 'Ruas [5, 15) tertutup minimal 2 lapis: [5, 8) dua lapis, [8, 10) tiga lapis, [10, 15) dua lapis. Lalu [15, 20) hanya 1 lapis. Total 10.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $maxX, int $maxLen) {
                $out = ["$n $k"];
                for ($i = 0; $i < $n; $i++) {
                    $l = mt_rand(0, $maxX - 1);
                    $r = min($maxX, $l + mt_rand(1, $maxLen));
                    $out[] = "$l $r";
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n0 1000000000\n", "2 2\n0 5\n5 10\n", $make(10, 2, 50, 20), $make(1000, 5, 1000000000, 100000000),
                $make(200000, 1, 1000000000, 1000), $make(200000, 100, 1000000000, 1000000000), $make(200000, 3, 1000000, 50), $make(200000, 200000, 1000000000, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $ev = [];
            for ($i = 1; $i <= $n; $i++) {
                [$l, $r] = T::ints($lines[$i]);
                $ev[$l] = ($ev[$l] ?? 0) + 1;
                $ev[$r] = ($ev[$r] ?? 0) - 1;
            }
            ksort($ev);
            $xs = array_keys($ev);
            $cur = 0;
            $tot = 0;
            foreach ($xs as $j => $x) {
                $cur += $ev[$x];
                if ($cur >= $k && isset($xs[$j + 1])) {
                    $tot += $xs[$j + 1] - $x;
                }
            }

            return (string) $tot;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> L(n), R(n);
    for (int i = 0; i < n; i++) cin >> L[i] >> R[i];

    // kompres semua ujung, difference array, lalu jumlahkan panjang segmen yang >= k

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const L = [], R = [];
for (let i = 0; i < n; i++) {
  const [l, r] = readInts();
  L.push(l);
  R.push(r);
}
// kompres semua ujung, difference array, lalu jumlahkan panjang segmen yang >= k
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
seg = [tuple(map(int, input().split())) for _ in range(n)]
# kompres semua ujung, difference array, lalu jumlahkan panjang segmen yang >= k
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> L(n), R(n), xs;
    for (int i = 0; i < n; i++) {
        cin >> L[i] >> R[i];
        xs.push_back(L[i]);
        xs.push_back(R[i]);
    }
    sort(xs.begin(), xs.end());
    xs.erase(unique(xs.begin(), xs.end()), xs.end());
    int m = xs.size();
    auto id = [&](long long x) { return int(lower_bound(xs.begin(), xs.end(), x) - xs.begin()); };

    vector<int> d(m + 1, 0);
    for (int i = 0; i < n; i++) {
        d[id(L[i])]++;
        d[id(R[i])]--;
    }
    long long total = 0;
    int lapis = 0;
    for (int j = 0; j + 1 < m; j++) {
        lapis += d[j];                          // lapis pada segmen [xs[j], xs[j+1])
        if (lapis >= k) total += xs[j + 1] - xs[j];   // panjang ASLI, bukan 1
    }
    cout << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const L = new Array(n), R = new Array(n);
const all = [];
for (let i = 0; i < n; i++) {
  const [l, r] = readInts();
  L[i] = l;
  R[i] = r;
  all.push(l, r);
}
const xs = [...new Set(all)].sort((a, b) => a - b);
const id = new Map(xs.map((x, i) => [x, i]));
const d = new Int32Array(xs.length + 1);
for (let i = 0; i < n; i++) {
  d[id.get(L[i])]++;
  d[id.get(R[i])]--;
}
let lapis = 0, total = 0;
for (let j = 0; j + 1 < xs.length; j++) {
  lapis += d[j];
  if (lapis >= k) total += xs[j + 1] - xs[j];
}
console.log(String(total));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
n, k = int(data[0]), int(data[1])
L = list(map(int, data[2::2][:n]))
R = list(map(int, data[3::2][:n]))
xs = sorted(set(L) | set(R))
idx = {x: i for i, x in enumerate(xs)}
d = [0] * (len(xs) + 1)
for l, r in zip(L, R):
    d[idx[l]] += 1
    d[idx[r]] -= 1
lapis = total = 0
for j in range(len(xs) - 1):
    lapis += d[j]
    if lapis >= k:
        total += xs[j + 1] - xs[j]
print(total)
CODE,
        ],
        'editorial' => '<p>Banyak lapis cat hanya berubah di ujung-ujung ruas. Jadi kompres semua 2N ujung menjadi <code>xs[0] &lt; xs[1] &lt; … &lt; xs[m−1]</code>. Di antara dua koordinat berurutan, segmen <code>[xs[j], xs[j+1])</code> punya banyak lapis yang <strong>tetap</strong>.</p>
<p>Pakai difference array atas indeks terkompresi: ruas [L, R) memberi <code>d[id(L)]++</code> dan <code>d[id(R)]−−</code>. Prefix sum d sampai j adalah banyak lapis di segmen ke-j.</p>
<p><strong>Kunci:</strong> panjang segmen ke-j adalah <code>xs[j+1] − xs[j]</code> dalam koordinat <em>asli</em>. Kompresi menghilangkan jarak, jadi jarak harus diambil dari array xs, bukan dari indeks.</p>
<p>Total O(N log N); jawabannya bisa sampai 10<sup>9</sup> (pakai long long untuk aman).</p>',
        'hints' => [
            'Banyak lapis cat hanya berubah di titik L dan R. Berapa banyak titik penting?',
            'Kompres semua ujung, lalu difference array atas indeks terkompresi.',
            'Segmen [xs[j], xs[j+1]) punya lapis tetap = prefix d sampai j. Jika ≥ K, tambahkan panjang aslinya xs[j+1] − xs[j].',
        ],
    ],

    // ───────────────────────── String & Array Frekuensi ─────────────────────────
    [
        'slug' => 'palindrom-susun',
        'lesson' => 'string-frekuensi',
        'title' => 'Susun Jadi Palindrom',
        'difficulty' => 'Mudah',
        'tags' => ['string', 'frekuensi', 'palindrom'],
        'statement' => '<p>Kakak memberi adik beberapa kartu huruf. Adik ingin menyusun <strong>semua</strong> kartunya menjadi sebuah <strong>palindrom</strong> (kata yang sama jika dibaca dari depan maupun belakang, misalnya <code>katak</code> atau <code>abba</code>).</p>
<p>Untuk setiap kumpulan kartu, tentukan apakah hal itu mungkin.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. T baris berikutnya masing-masing berisi satu string huruf kecil: huruf-huruf pada kartu.</p>',
        'output_format' => '<p>T baris, masing-masing <code>YA</code> atau <code>TIDAK</code>.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 100 000</li><li>Total panjang semua string ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "4\nkatak\naabbc\nabc\nzz\n", 'explanation' => '"aabbc" bisa disusun menjadi "abcba". "abc" punya tiga huruf berfrekuensi ganjil, jadi mustahil.'],
        ],
        'tests' => function () {
            $make = function (int $t, int $maxLen, string $alpha) {
                $out = [(string) $t];
                for ($i = 0; $i < $t; $i++) {
                    $s = T::word(mt_rand(1, $maxLen), $alpha);
                    if (mt_rand(0, 1)) {
                        $s = $s.strrev($s).(mt_rand(0, 1) ? T::word(1, $alpha) : '');
                        $s = str_shuffle($s);
                    }
                    $out[] = $s;
                }

                return implode("\n", $out)."\n";
            };

            return ["1\na\n", "2\nab\naa\n", $make(30, 6, 'abc'), $make(1000, 50, 'abcdefghijklmnopqrstuvwxyz'), $make(100000, 4, 'ab'), $make(10, 49000, 'abcdefghijklmnopqrstuvwxyz')];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $t = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $t; $i++) {
                $odd = 0;
                foreach (count_chars(trim($lines[$i]), 1) as $c) {
                    $odd += $c % 2;
                }
                $out[] = $odd <= 1 ? 'YA' : 'TIDAK';
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

    int t;
    cin >> t;
    while (t--) {
        string s;
        cin >> s;
        int cnt[26] = {0};
        // hitung frekuensi, lalu periksa

    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const t = Number(readLine());
const out = [];
for (let i = 0; i < t; i++) {
  const s = readLine().trim();
  // hitung frekuensi, lalu periksa
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

t = int(input())
out = []
for _ in range(t):
    s = input().strip()
    # hitung frekuensi, lalu periksa

print("\n".join(out))
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int t;
    cin >> t;
    string out;
    while (t--) {
        string s;
        cin >> s;
        int cnt[26] = {0};
        for (char c : s) cnt[c - 'a']++;
        int ganjil = 0;
        for (int x = 0; x < 26; x++) ganjil += cnt[x] % 2;
        out += (ganjil <= 1 ? "YA\n" : "TIDAK\n");   // paling banyak satu huruf di tengah
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const t = Number(readLine());
const out = [];
for (let i = 0; i < t; i++) {
  const s = readLine().trim();
  const cnt = new Int32Array(26);
  for (let j = 0; j < s.length; j++) cnt[s.charCodeAt(j) - 97]++;
  let ganjil = 0;
  for (let x = 0; x < 26; x++) ganjil += cnt[x] & 1;
  out.push(ganjil <= 1 ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
from collections import Counter
data = sys.stdin.read().split()
t = int(data[0])
out = []
for s in data[1:1 + t]:
    ganjil = sum(c % 2 for c in Counter(s).values())
    out.append("YA" if ganjil <= 1 else "TIDAK")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Pada palindrom, setiap huruf di posisi i dipasangkan dengan huruf yang sama di posisi cerminnya. Jadi setiap huruf muncul dalam jumlah <strong>genap</strong>, kecuali paling banyak <strong>satu</strong> huruf yang menempati posisi tengah (jika panjangnya ganjil).</p>
<p>Sebaliknya, jika paling banyak satu huruf berfrekuensi ganjil, palindrom selalu bisa disusun: taruh separuh dari setiap huruf di kiri, cerminkan di kanan, dan huruf ganjil di tengah.</p>
<p>Hitung frekuensi dengan <code>int cnt[26]</code>, lalu periksa banyak frekuensi ganjil ≤ 1. Total O(panjang semua string).</p>',
        'hints' => [
            'Pada palindrom, huruf di posisi i sama dengan huruf di posisi cerminnya.',
            'Akibatnya hampir semua huruf harus muncul dalam jumlah genap. Kecuali?',
            'Hitung frekuensi 26 huruf. Jawabannya YA jika banyak huruf berfrekuensi ganjil paling banyak 1.',
        ],
    ],

    [
        'slug' => 'huruf-rentang',
        'lesson' => 'string-frekuensi',
        'title' => 'Huruf Terbanyak di Potongan',
        'difficulty' => 'Sedang',
        'tags' => ['string', 'prefix sum', 'frekuensi'],
        'statement' => '<p>Diberikan string <strong>s</strong> sepanjang N (huruf kecil). Ada <strong>Q</strong> pertanyaan; setiap pertanyaan berisi <code>l r</code>: huruf apa yang paling sering muncul di potongan <code>s[l..r]</code> (1-indexed, termasuk keduanya), dan berapa kali?</p>
<p>Jika ada beberapa huruf dengan banyak kemunculan sama, pilih yang terkecil menurut abjad.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Baris kedua berisi s. Q baris berikutnya masing-masing berisi <code>l r</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing berisi huruf dan banyak kemunculannya.</p>',
        'constraints' => '<ul><li>1 ≤ N, Q ≤ 100 000</li><li>1 ≤ l ≤ r ≤ N</li></ul>',
        'samples' => [
            ['input' => "11 3\nabracadabra\n1 11\n2 4\n6 7\n", 'explanation' => 'Seluruh string: a muncul 5 kali. "bra": b, r, a masing-masing sekali, pilih a. "da": a dan d sekali, pilih a.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $q, string $alpha) {
                $out = ["$n $q", T::word($n, $alpha)];
                for ($i = 0; $i < $q; $i++) {
                    $l = mt_rand(1, $n);
                    $r = mt_rand($l, $n);
                    $out[] = "$l $r";
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\nz\n1 1\n", $make(20, 20, 'abc'), $make(1000, 1000, 'abcdefghijklmnopqrstuvwxyz'),
                $make(100000, 100000, 'abcdefghijklmnopqrstuvwxyz'), $make(100000, 100000, 'xyz'), $make(100000, 100000, 'q'),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $s = trim($lines[1]);
            $pre = array_fill(0, 26, [0]);
            for ($i = 0; $i < $n; $i++) {
                $c = ord($s[$i]) - 97;
                for ($x = 0; $x < 26; $x++) {
                    $pre[$x][$i + 1] = $pre[$x][$i] + ($x === $c ? 1 : 0);
                }
            }
            $out = [];
            for ($k = 0; $k < $q; $k++) {
                [$l, $r] = T::ints($lines[2 + $k]);
                $best = -1;
                $bx = 0;
                for ($x = 0; $x < 26; $x++) {
                    $c = $pre[$x][$r] - $pre[$x][$l - 1];
                    if ($c > $best) {
                        $best = $c;
                        $bx = $x;
                    }
                }
                $out[] = chr(97 + $bx).' '.$best;
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
    string s;
    cin >> n >> q >> s;
    // 26 prefix sum: pre[i][x] = banyak huruf x di s[0..i-1]

    while (q--) {
        int l, r;
        cin >> l >> r;
    }
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const s = readLine().trim();
// 26 prefix sum
const out = [];
for (let k = 0; k < q; k++) {
  const [l, r] = readInts();
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
s = input().strip()
# 26 prefix sum
out = []
for _ in range(q):
    l, r = map(int, input().split())

print("\n".join(out))
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
    string s;
    cin >> n >> q >> s;
    vector<array<int, 26>> pre(n + 1);
    pre[0].fill(0);
    for (int i = 0; i < n; i++) {
        pre[i + 1] = pre[i];
        pre[i + 1][s[i] - 'a']++;
    }
    string out;
    while (q--) {
        int l, r;
        cin >> l >> r;
        int best = -1, bx = 0;
        for (int x = 0; x < 26; x++) {
            int c = pre[r][x] - pre[l - 1][x];
            if (c > best) {          // > agar huruf terkecil menang saat seri
                best = c;
                bx = x;
            }
        }
        out += char('a' + bx);
        out += " " + to_string(best) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const s = readLine().trim();
const pre = new Int32Array((n + 1) * 26);   // pre[i*26 + x]
for (let i = 0; i < n; i++) {
  for (let x = 0; x < 26; x++) pre[(i + 1) * 26 + x] = pre[i * 26 + x];
  pre[(i + 1) * 26 + s.charCodeAt(i) - 97]++;
}
const out = [];
for (let k = 0; k < q; k++) {
  const [l, r] = readInts();
  let best = -1, bx = 0;
  for (let x = 0; x < 26; x++) {
    const c = pre[r * 26 + x] - pre[(l - 1) * 26 + x];
    if (c > best) { best = c; bx = x; }
  }
  out.push(String.fromCharCode(97 + bx) + " " + best);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
data = sys.stdin.buffer.read().split()
n, q = int(data[0]), int(data[1])
s = data[2]
# pre[x][i] = banyak huruf x di s[0..i-1], disimpan per huruf
pre = []
for x in range(26):
    ch = 97 + x
    row = [0] * (n + 1)
    c = 0
    for i in range(n):
        if s[i] == ch:
            c += 1
        row[i + 1] = c
    pre.append(row)
out = []
pos = 3
for _ in range(q):
    l, r = int(data[pos]), int(data[pos + 1])
    pos += 2
    best, bx = -1, 0
    for x in range(26):
        c = pre[x][r] - pre[x][l - 1]
        if c > best:
            best, bx = c, x
    out.append(f"{chr(97 + bx)} {best}")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Menghitung frekuensi potongan dengan loop untuk setiap pertanyaan butuh O(N · Q) = 10<sup>10</sup>.</p>
<p>Siapkan <strong>26 prefix sum</strong>: <code>pre[i][x]</code> = banyak huruf x di s[1..i]. Banyak huruf x di s[l..r] = <code>pre[r][x] − pre[l−1][x]</code>. Untuk setiap pertanyaan, periksa ke-26 huruf dan ambil yang terbanyak; memakai <code>&gt;</code> (bukan ≥) saat memperbarui membuat huruf terkecil menang jika seri.</p>
<p>Waktu O(26 · (N + Q)), memori 26 · (N + 1) bilangan (sekitar 10 MB untuk N = 10<sup>5</sup>).</p>',
        'hints' => [
            'Untuk setiap huruf, berapa kali ia muncul di s[l..r]? Itu soal jumlah rentang.',
            'Buat prefix sum terpisah untuk setiap huruf: 26 array.',
            'Banyak huruf x di [l, r] = pre[r][x] − pre[l−1][x]. Coba ke-26 huruf, ambil yang terbanyak (seri → huruf terkecil).',
        ],
    ],

    [
        'slug' => 'pasangan-selisih',
        'lesson' => 'string-frekuensi',
        'title' => 'Pasangan Berselisih K',
        'difficulty' => 'Sedang',
        'tags' => ['frekuensi', 'counting'],
        'statement' => '<p>Diberikan <strong>N</strong> bilangan <code>a<sub>1</sub>, …, a<sub>N</sub></code> dan bilangan <strong>K</strong>. Ada berapa pasangan indeks (i, j) dengan i &lt; j sehingga <code>|a<sub>i</sub> − a<sub>j</sub>| = K</code>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Satu bilangan: banyak pasangan.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>0 ≤ K ≤ 10<sup>6</sup></li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "6 2\n1 3 5 3 1 4\n", 'explanation' => 'Pasangan bernilai (1, 3): ada 2 × 2 = 4 pasang indeks; (3, 5): 2 pasang. Total 6.'],
            ['input' => "4 0\n7 7 7 2\n", 'explanation' => 'K = 0: pasangan dengan nilai sama. Tiga angka 7 membentuk C(3, 2) = 3 pasang.'],
        ],
        'tests' => function () {
            $make = function (int $n, int $k, int $hi) {
                return "$n $k\n".T::join(T::arr($n, 0, $hi))."\n";
            };

            return [
                "1 0\n5\n", "2 1000000\n0 1000000\n", $make(10, 1, 5), $make(1000, 3, 50), $make(200000, 0, 1000),
                $make(200000, 1, 100), $make(200000, 777, 1000000), $make(200000, 1000000, 1000000), $make(200000, 5, 10),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $cnt = [];
            $ans = 0;
            foreach (T::ints($lines[1]) as $x) {
                if ($k === 0) {
                    $ans += $cnt[$x] ?? 0;
                } else {
                    $ans += ($cnt[$x - $k] ?? 0) + ($cnt[$x + $k] ?? 0);
                }
                $cnt[$x] = ($cnt[$x] ?? 0) + 1;
            }

            return (string) $ans;
        },
        'starter' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAXV = 1000000;
int cnt[MAXV + 1];

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    // untuk setiap a[j], berapa a[i] sebelumnya yang berselisih k?

    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const a = readInts();
// array frekuensi berukuran 1e6 + 1
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
a = list(map(int, input().split()))
# array frekuensi berukuran 10**6 + 1
CODE,
        ],
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAXV = 1000000;
int cnt[MAXV + 1];      // cnt[v] = banyak v di antara elemen yang SUDAH dilewati

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    long long ans = 0;
    for (int j = 0; j < n; j++) {
        int x;
        cin >> x;
        if (k == 0) {
            ans += cnt[x];                         // pasangan nilai sama
        } else {
            if (x - k >= 0) ans += cnt[x - k];     // pasangan a_i = x - k
            if (x + k <= MAXV) ans += cnt[x + k];  // pasangan a_i = x + k
        }
        cnt[x]++;                                  // masukkan SETELAH menghitung
    }
    cout << ans << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const a = readInts();
const MAXV = 1000000;
const cnt = new Int32Array(MAXV + 1);
let ans = 0;
for (const x of a) {
  if (k === 0) ans += cnt[x];
  else {
    if (x - k >= 0) ans += cnt[x - k];
    if (x + k <= MAXV) ans += cnt[x + k];
  }
  cnt[x]++;
}
console.log(String(ans));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
a = list(map(int, input().split()))
MAXV = 10**6
cnt = [0] * (MAXV + 1)
ans = 0
for x in a:
    if k == 0:
        ans += cnt[x]
    else:
        if x - k >= 0:
            ans += cnt[x - k]
        if x + k <= MAXV:
            ans += cnt[x + k]
    cnt[x] += 1
print(ans)
CODE,
        ],
        'editorial' => '<p>Jalan dari kiri ke kanan sambil menyimpan <code>cnt[v]</code> = banyak nilai v di antara elemen yang sudah dilewati. Untuk elemen <code>x = a<sub>j</sub></code>, pasangan (i, j) dengan i &lt; j yang valid adalah elemen sebelumnya bernilai <code>x − K</code> atau <code>x + K</code>. Tambahkan keduanya, lalu masukkan x.</p>
<p>Karena setiap pasangan dihitung tepat sekali (di indeks yang lebih besar), tidak perlu membagi dua.</p>
<p><strong>Jebakan K = 0:</strong> x − K dan x + K adalah nilai yang sama, jadi hanya hitung sekali (<code>cnt[x]</code>). Jika dijumlahkan dua kali, jawabannya ganda.</p>
<p>Nilai ≤ 10<sup>6</sup>, jadi array frekuensi biasa cukup dan jauh lebih cepat dari map. Jawaban bisa sampai N(N−1)/2 ≈ 2 · 10<sup>10</sup>: pakai <code>long long</code>.</p>',
        'hints' => [
            'Untuk setiap elemen x, pasangannya di kiri harus bernilai x − K atau x + K.',
            'Simpan frekuensi nilai yang sudah dilewati dalam array (nilai ≤ 10^6).',
            'ans += cnt[x−K] + cnt[x+K] (hanya sekali jika K = 0), lalu cnt[x]++. Jawaban long long.',
        ],
    ],
];
