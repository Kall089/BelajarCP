<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Optimasi Transisi DP: prefix sum, deque monoton, binary search.
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

return [
    [
        'slug' => 'tangga-sakti',
        'lesson' => 'dp-optimasi',
        'title' => 'Tangga Sakti',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'prefix sum', 'menghitung cara'],
        'statement' => '<p>Di sebuah kuil ada tangga dengan <strong>N</strong> anak tangga bernomor 1 sampai N. Sekali melangkah, Raka boleh naik 1, 2, …, sampai <strong>K</strong> anak tangga. Sayangnya <strong>B</strong> anak tangga sudah rapuh dan tidak boleh diinjak.</p>
<p>Raka mulai dari lantai dasar (anak tangga 0) dan harus berhenti tepat di anak tangga N. Ada berapa cara? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K B</code>. Baris kedua berisi B nomor anak tangga yang rapuh (berbeda, baris kosong jika B = 0).</p>',
        'output_format' => '<p>Banyak cara modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 10<sup>6</sup></li><li>0 ≤ B ≤ N</li></ul>',
        'samples' => [
            ['input' => "5 2 1\n3\n", 'explanation' => '0 → 1 → 2 → 4 → 5 dan 0 → 2 → 4 → 5. Anak tangga 3 tidak boleh diinjak.'],
            ['input' => "4 4 0\n\n", 'explanation' => 'Tanpa larangan dan K = N: setiap anak tangga 1..3 boleh diinjak atau dilewati, 2³ = 8 cara.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $k, int $b) {
                $set = [];
                while (count($set) < $b) {
                    $set[mt_rand(1, $n - 1 > 0 ? $n - 1 : 1)] = true;
                }
                $br = array_keys($set);
                T::shuffle($br);

                return "$n $k ".count($br)."\n".implode(' ', $br)."\n";
            };

            return [
                "1 1 0\n\n", "1 1 1\n1\n", "10 3 0\n\n", $mk(20, 4, 3), $mk(1000, 10, 100), $mk(1000000, 1, 0),
                $mk(1000000, 3, 1000), $mk(1000000, 1000, 300000), $mk(1000000, 1000000, 0), $mk(1000000, 37, 999), "6 2 2\n2 3\n",
            ];
        },
        'solve' => function (string $input) use ($MOD) {
            $lines = preg_split('/\r?\n/', $input);
            [$n, $k] = T::ints($lines[0]);
            $bad = array_fill(0, $n + 1, false);
            foreach (T::ints($lines[1] ?? '') as $x) {
                $bad[$x] = true;
            }
            $pre = array_fill(0, $n + 1, 0);
            $pre[0] = 1;
            $dp = 1;
            for ($i = 1; $i <= $n; $i++) {
                if ($bad[$i]) {
                    $dp = 0;
                } else {
                    $dp = $pre[$i - 1] - ($i - $k - 1 >= 0 ? $pre[$i - $k - 1] : 0);
                    $dp %= $MOD;
                    if ($dp < 0) {
                        $dp += $MOD;
                    }
                }
                $pre[$i] = ($pre[$i - 1] + $dp) % $MOD;
            }

            return (string) $dp;
        },
        'starter' => $st("    int n, k, b;\n    cin >> n >> k >> b;\n    vector<bool> rapuh(n + 1, false);\n    for (int i = 0; i < b; i++) {\n        int x;\n        cin >> x;\n        rapuh[x] = true;\n    }", "const [n, k, b] = readInts();\nconst rapuh = new Uint8Array(n + 1);\nfor (const x of readInts()) rapuh[x] = 1;", "n, k, b = map(int, input().split())\nrapuh = [False] * (n + 1)\nfor x in map(int, input().split()):\n    rapuh[x] = True"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k, b;
    cin >> n >> k >> b;
    vector<bool> rapuh(n + 1, false);
    for (int i = 0; i < b; i++) {
        int x;
        cin >> x;
        rapuh[x] = true;
    }

    // dp[i] = banyak cara berhenti di anak tangga i
    // dp[i] = dp[i-1] + ... + dp[i-K]   (0 jika i rapuh)
    // pre[i] = dp[0] + ... + dp[i]  ->  jumlah rentang dalam O(1)
    vector<long long> dp(n + 1, 0), pre(n + 1, 0);
    dp[0] = pre[0] = 1;
    for (int i = 1; i <= n; i++) {
        if (!rapuh[i]) {
            long long kiri = (i - k - 1 >= 0) ? pre[i - k - 1] : 0;
            dp[i] = (pre[i - 1] - kiri + MOD) % MOD;
        }
        pre[i] = (pre[i - 1] + dp[i]) % MOD;
    }
    cout << dp[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k, b] = readInts();
const rapuh = new Uint8Array(n + 1);
for (const x of readInts()) rapuh[x] = 1;
const MOD = 1000000007;
const pre = new Float64Array(n + 1); // nilai < MOD, jumlah dua nilai masih tepat
pre[0] = 1;
let dp = 1;
for (let i = 1; i <= n; i++) {
  dp = 0;
  if (!rapuh[i]) {
    const kiri = i - k - 1 >= 0 ? pre[i - k - 1] : 0;
    dp = (pre[i - 1] - kiri + MOD) % MOD;
  }
  pre[i] = (pre[i - 1] + dp) % MOD;
}
console.log(dp);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k, b = map(int, input().split())
rapuh = [False] * (n + 1)
for x in map(int, input().split()):
    rapuh[x] = True

MOD = 10**9 + 7
pre = [0] * (n + 1)
pre[0] = 1
dp = 1
for i in range(1, n + 1):
    if rapuh[i]:
        dp = 0
    else:
        kiri = pre[i - k - 1] if i - k - 1 >= 0 else 0
        dp = (pre[i - 1] - kiri) % MOD    # % di Python selalu non-negatif
    pre[i] = (pre[i - 1] + dp) % MOD
print(dp)
CODE,
        ],
        'editorial' => '<p>State <code>dp[i]</code> = banyak cara berhenti di anak tangga i. Langkah terakhir datang dari i − 1, …, i − K, jadi <code>dp[i] = dp[i−1] + … + dp[i−K]</code>, dan dp[i] = 0 jika i rapuh. Base case dp[0] = 1.</p>
<p>Menjumlahkan K suku untuk setiap i butuh O(N · K) = 10<sup>12</sup> pada kasus terburuk. Simpan prefix sum <code>pre[i] = dp[0] + … + dp[i]</code>, maka jumlahnya adalah <code>pre[i−1] − pre[i−K−1]</code> (anggap pre bernilai 0 untuk indeks negatif). Total O(N).</p>
<p><strong>Awas modulo:</strong> selisih dua prefix yang sudah dimodulo bisa negatif. Tambahkan MOD sebelum <code>% MOD</code> (di Python, operator % sudah selalu non-negatif).</p>',
        'hints' => [
            'Tuliskan dulu DP-nya: dari anak tangga mana saja Raka bisa sampai di anak tangga i?',
            'dp[i] = jumlah dp[i−K..i−1]. Ini lambat jika K besar. Bagaimana menjumlahkan rentang dalam O(1)?',
            'Prefix sum dari dp: jumlah rentang = pre[i−1] − pre[i−K−1]. Hati-hati hasil negatif saat modulo.',
        ],
    ],

    [
        'slug' => 'jalan-setapak',
        'lesson' => 'dp-optimasi',
        'title' => 'Jalan Setapak Berhadiah',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'deque monoton', 'sliding window'],
        'statement' => '<p>Sebuah jalan setapak terdiri dari <strong>N</strong> petak. Petak ke-i bernilai <code>a<sub>i</sub></code> (bisa negatif: petak jebakan). Kamu mulai di petak 1 dan harus berhenti di petak N. Setiap lompatan maju sejauh 1 sampai <strong>K</strong> petak.</p>
<p>Skormu adalah jumlah nilai semua petak tempat kamu mendarat (termasuk petak 1 dan N). Berapa skor terbesar yang mungkin?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Skor terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 200 000</li><li>−10<sup>9</sup> ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6 2\n1 -1 -2 4 -7 3\n", 'explanation' => 'Petak 1 → 2 → 4 → 6: 1 − 1 + 4 + 3 = 7. Petak −2 dan −7 dilompati.'],
            ['input' => "5 4\n-5 -1 -1 -1 -2\n", 'explanation' => 'Langsung lompat dari petak 1 ke petak 5: −5 − 2 = −7.'],
        ],
        'tests' => function () use ($arr) {
            return [
                "1 1\n-4\n", "2 1\n5 -6\n", "6 3\n1 2 3 4 5 6\n", "8 2\n".$arr(8, -10, 10)."\n", "1000 7\n".$arr(1000, -1000, 1000)."\n",
                "200000 1\n".$arr(200000, -1000000000, 1000000000)."\n", "200000 200000\n".$arr(200000, -1000000000, 1000000000)."\n",
                "200000 50\n".$arr(200000, -1000000000, 1000000000)."\n", "200000 1000\n".$arr(200000, -1000000000, -1)."\n", "200000 3\n".$arr(200000, -5, 3)."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $a = array_merge([0], T::ints($lines[1]));
            $dp = array_fill(0, $n + 1, 0);
            $dp[1] = $a[1];
            $dq = new SplDoublyLinkedList;
            $dq->push(1);
            for ($i = 2; $i <= $n; $i++) {
                while ($dq->bottom() < $i - $k) {
                    $dq->shift();
                }
                $dp[$i] = $a[$i] + $dp[$dq->bottom()];
                while (! $dq->isEmpty() && $dp[$dq->top()] <= $dp[$i]) {
                    $dq->pop();
                }
                $dq->push($i);
            }

            return (string) $dp[$n];
        },
        'starter' => $st("    int n, k;\n    cin >> n >> k;\n    vector<long long> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];", "const [n, k] = readInts();\nconst a = [0, ...readInts()];", "n, k = map(int, input().split())\na = [0] + list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> a(n + 1);
    for (int i = 1; i <= n; i++) cin >> a[i];

    // dp[i] = a[i] + max(dp[i-K..i-1]); deque berisi indeks dengan dp TURUN (maksimum di depan)
    vector<long long> dp(n + 1);
    deque<int> dq;
    dp[1] = a[1];
    dq.push_back(1);
    for (int i = 2; i <= n; i++) {
        while (dq.front() < i - k) dq.pop_front();
        dp[i] = a[i] + dp[dq.front()];
        while (!dq.empty() && dp[dq.back()] <= dp[i]) dq.pop_back();
        dq.push_back(i);
    }
    cout << dp[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const a = [0, ...readInts()];
// |skor| ≤ 2 · 10^14: masih aman untuk Number
const dp = new Float64Array(n + 1);
const dq = new Int32Array(n + 1); // deque sebagai array dengan dua penunjuk
let head = 0, tail = 0;
dp[1] = a[1];
dq[tail++] = 1;
for (let i = 2; i <= n; i++) {
  while (dq[head] < i - k) head++;
  dp[i] = a[i] + dp[dq[head]];
  while (tail > head && dp[dq[tail - 1]] <= dp[i]) tail--;
  dq[tail++] = i;
}
console.log(dp[n]);
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
a = [0] + list(map(int, input().split()))
dp = [0] * (n + 1)
dp[1] = a[1]
dq = deque([1])
for i in range(2, n + 1):
    while dq[0] < i - k:
        dq.popleft()
    dp[i] = a[i] + dp[dq[0]]
    while dq and dp[dq[-1]] <= dp[i]:
        dq.pop()
    dq.append(i)
print(dp[n])
CODE,
        ],
        'editorial' => '<p><code>dp[i]</code> = skor terbesar untuk mendarat di petak i. Lompatan terakhir berasal dari salah satu petak i − K … i − 1, jadi <code>dp[i] = a[i] + max(dp[i−K..i−1])</code>, dengan dp[1] = a[1].</p>
<p>Maksimum jendela geser dihitung dengan <strong>deque monoton</strong> berisi indeks yang dp-nya <em>turun</em> dari depan ke belakang: buang depan yang sudah keluar jendela, ambil depan sebagai maksimum, lalu buang belakang yang dp-nya ≤ dp[i] sebelum memasukkan i. Total O(N).</p>
<p>Petak bernilai negatif tidak boleh langsung dilewati secara serakah: contoh pertama melewati −2 tetapi tetap menginjak −1, karena dari petak 1 dengan K = 2 kita harus mendarat di petak 2 atau 3.</p>',
        'hints' => [
            'Definisikan dp[i] = skor terbesar jika berhenti di petak i. Dari mana saja kamu bisa datang?',
            'dp[i] = a[i] + max(dp[i−K..i−1]). Mencari maksimum dengan loop membuat O(N·K).',
            'Pakai deque monoton yang menyimpan indeks dengan dp menurun. Total O(N).',
        ],
    ],

    [
        'slug' => 'proyek-terbaik',
        'lesson' => 'dp-optimasi',
        'title' => 'Proyek Paling Menguntungkan',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'binary search', 'penjadwalan berbobot'],
        'statement' => '<p>Sebuah studio desain menerima tawaran <strong>N</strong> proyek. Proyek ke-i dikerjakan dari hari <code>s<sub>i</sub></code> sampai hari <code>e<sub>i</sub></code> (keduanya termasuk) dan memberi untung <code>w<sub>i</sub></code>. Studio hanya punya satu tim, jadi dua proyek yang diambil tidak boleh berbagi hari yang sama.</p>
<p>Berapa total untung terbesar?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya berisi <code>s e w</code>.</p>',
        'output_format' => '<p>Total untung terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ s ≤ e ≤ 10<sup>9</sup></li><li>1 ≤ w ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4\n1 3 5\n2 5 6\n4 6 5\n6 7 4\n", 'explanation' => 'Ambil proyek [1, 3] dan [4, 6]: 5 + 5 = 10. Proyek [4, 6] dan [6, 7] tidak boleh diambil bersama karena sama-sama memakai hari 6.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $maxDay, int $maxLen, int $maxW) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $s = mt_rand(1, $maxDay);
                    $e = min($maxDay, $s + mt_rand(0, $maxLen));
                    $rows[] = "$s $e ".mt_rand(1, $maxW);
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return [
                "1\n5 5 7\n", "2\n1 2 3\n2 3 4\n", "3\n1 1 1\n2 2 1\n1 2 3\n", $mk(10, 20, 5, 10), $mk(1000, 5000, 50, 1000),
                $mk(200000, 1000000000, 1000000, 1000000000), $mk(200000, 1000000000, 1000000000, 1000000000), $mk(200000, 1000, 10, 1000000000), $mk(200000, 300000, 0, 5),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $p = [];
            for ($i = 1; $i <= $n; $i++) {
                $p[] = T::ints($lines[$i]);
            }
            usort($p, fn ($x, $y) => $x[1] <=> $y[1]);
            $ends = array_column($p, 1);
            $dp = array_fill(0, $n + 1, 0);
            for ($i = 1; $i <= $n; $i++) {
                $s = $p[$i - 1][0];
                $lo = 0;
                $hi = $n;
                while ($lo < $hi) {
                    $mid = ($lo + $hi) >> 1;
                    if ($ends[$mid] < $s) {
                        $lo = $mid + 1;
                    } else {
                        $hi = $mid;
                    }
                }
                $dp[$i] = max($dp[$i - 1], $dp[$lo] + $p[$i - 1][2]);
            }

            return (string) $dp[$n];
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<array<long long, 3>> p(n);   // {s, e, w}\n    for (auto& x : p) cin >> x[0] >> x[1] >> x[2];", "const n = Number(readLine());\nconst p = [];\nfor (let i = 0; i < n; i++) p.push(readInts()); // [s, e, w]", "n = int(input())\np = [tuple(map(int, input().split())) for _ in range(n)]  # (s, e, w)"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<array<long long, 3>> p(n);   // {s, e, w}
    for (auto& x : p) cin >> x[0] >> x[1] >> x[2];

    // Urutkan berdasarkan hari selesai.
    sort(p.begin(), p.end(), [](const array<long long, 3>& x, const array<long long, 3>& y) { return x[1] < y[1]; });
    vector<long long> selesai(n);
    for (int i = 0; i < n; i++) selesai[i] = p[i][1];

    // dp[i] = untung terbaik hanya memakai proyek 1..i (urutan setelah sort)
    vector<long long> dp(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        long long mulai = p[i - 1][0];
        // j = banyak proyek yang selesai SEBELUM hari 'mulai' (e < mulai)
        int j = lower_bound(selesai.begin(), selesai.end(), mulai) - selesai.begin();
        dp[i] = max(dp[i - 1], dp[j] + p[i - 1][2]);
    }
    cout << dp[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const p = [];
for (let i = 0; i < n; i++) p.push(readInts());
p.sort((x, y) => x[1] - y[1]);
const dp = new Float64Array(n + 1); // ≤ 2 · 10^14, aman untuk Number
for (let i = 1; i <= n; i++) {
  const mulai = p[i - 1][0];
  let lo = 0, hi = n; // cari indeks pertama dengan selesai >= mulai
  while (lo < hi) {
    const mid = (lo + hi) >> 1;
    if (p[mid][1] < mulai) lo = mid + 1;
    else hi = mid;
  }
  dp[i] = Math.max(dp[i - 1], dp[lo] + p[i - 1][2]);
}
console.log(dp[n]);
CODE,
            'python' => <<<'CODE'
import sys
from bisect import bisect_left
input = sys.stdin.readline

n = int(input())
p = [tuple(map(int, input().split())) for _ in range(n)]
p.sort(key=lambda x: x[1])
selesai = [x[1] for x in p]
dp = [0] * (n + 1)
for i in range(1, n + 1):
    s, e, w = p[i - 1]
    j = bisect_left(selesai, s)       # banyak proyek dengan e < s
    dp[i] = max(dp[i - 1], dp[j] + w)
print(dp[n])
CODE,
        ],
        'editorial' => '<p>Urutkan proyek berdasarkan hari selesai. <code>dp[i]</code> = untung terbaik jika hanya boleh memakai proyek 1..i. Untuk proyek i ada dua pilihan:</p>
<ul><li>tidak diambil: <code>dp[i − 1]</code>;</li><li>diambil: proyek lain yang boleh menemaninya adalah yang selesai <em>sebelum</em> hari mulainya. Karena sudah terurut, proyek-proyek itu adalah 1..j untuk suatu j, sehingga untungnya <code>dp[j] + w<sub>i</sub></code>.</li></ul>
<p>j dicari dengan binary search (<code>lower_bound</code> pada daftar hari selesai, mencari yang pertama ≥ s<sub>i</sub>). Total O(N log N). Perhatikan hari dihitung inklusif: proyek yang selesai di hari s<sub>i</sub> tetap bertabrakan, maka syaratnya e<sub>j</sub> &lt; s<sub>i</sub>.</p>',
        'hints' => [
            'Urutkan proyek berdasarkan hari selesai. Untuk proyek terakhir, ada dua pilihan: ambil atau tidak.',
            'Jika proyek i diambil, proyek sebelumnya harus selesai sebelum s_i. Setelah diurutkan, proyek-proyek itu membentuk prefiks 1..j.',
            'dp[i] = max(dp[i−1], dp[j] + w_i), dengan j dicari menggunakan lower_bound.',
        ],
    ],

    [
        'slug' => 'panen-istirahat',
        'lesson' => 'dp-optimasi',
        'title' => 'Panen dengan Hari Libur',
        'difficulty' => 'Sulit',
        'tags' => ['dp', 'deque monoton', 'pemodelan'],
        'statement' => '<p>Pak Tani memanen kebunnya selama <strong>N</strong> hari berturut-turut. Jika ia bekerja pada hari ke-i, ia mendapat <code>a<sub>i</sub></code> kilogram buah. Agar tidak kelelahan, ia <strong>tidak boleh bekerja lebih dari K hari berturut-turut</strong>; pada hari libur ia tidak mendapat apa-apa.</p>
<p>Berapa kilogram buah paling banyak yang bisa ia panen?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Total panen terbesar.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 200 000</li><li>0 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5 2\n3 1 4 1 5\n", 'explanation' => 'Libur di hari 2 dan 4: bekerja di hari 1, 3, 5, total 3 + 4 + 5 = 12. Libur hanya di hari 3 juga sah, tetapi hasilnya 10.'],
            ['input' => "3 3\n5 5 5\n", 'explanation' => 'Tiga hari berturut-turut masih diperbolehkan.'],
        ],
        'tests' => function () use ($arr) {
            return [
                "1 1\n9\n", "2 1\n4 7\n", "4 1\n".$arr(4, 0, 9)."\n", "10 3\n".$arr(10, 0, 20)."\n", "1000 5\n".$arr(1000, 0, 1000)."\n",
                "200000 1\n".$arr(200000, 0, 1000000000)."\n", "200000 2\n".$arr(200000, 0, 1000000000)."\n", "200000 100\n".$arr(200000, 0, 1000000000)."\n",
                "200000 199999\n".$arr(200000, 0, 1000000000)."\n", "200000 7\n".$arr(200000, 0, 3)."\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $a = array_merge([0], T::ints($lines[1]), [0]);
            $L = $k + 1;
            $dp = array_fill(0, $n + 2, 0);
            $dq = new SplDoublyLinkedList;
            $dq->push(0);
            for ($i = 1; $i <= $n + 1; $i++) {
                while ($dq->bottom() < $i - $L) {
                    $dq->shift();
                }
                $dp[$i] = $a[$i] + $dp[$dq->bottom()];
                while (! $dq->isEmpty() && $dp[$dq->top()] >= $dp[$i]) {
                    $dq->pop();
                }
                $dq->push($i);
            }

            return (string) (array_sum($a) - $dp[$n + 1]);
        },
        'starter' => $st("    int n, k;\n    cin >> n >> k;\n    vector<long long> a(n + 1);\n    for (int i = 1; i <= n; i++) cin >> a[i];", "const [n, k] = readInts();\nconst a = [0, ...readInts()];", "n, k = map(int, input().split())\na = [0] + list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    vector<long long> a(n + 2, 0);       // a[0] dan a[n+1] = hari libur "khayalan" bernilai 0
    long long total = 0;
    for (int i = 1; i <= n; i++) {
        cin >> a[i];
        total += a[i];
    }

    // Balik sudut pandang: pilih hari LIBUR dengan total kehilangan minimum,
    // sehingga di antara dua libur berurutan ada paling banyak K hari kerja,
    // yaitu jarak dua libur berurutan paling jauh K + 1.
    // dp[i] = kehilangan minimum jika hari i libur = a[i] + min(dp[i-K-1..i-1]).
    int L = k + 1;
    vector<long long> dp(n + 2, 0);
    deque<int> dq;
    dq.push_back(0);                     // hari 0: libur khayalan, dp[0] = 0
    for (int i = 1; i <= n + 1; i++) {
        while (dq.front() < i - L) dq.pop_front();
        dp[i] = a[i] + dp[dq.front()];
        while (!dq.empty() && dp[dq.back()] >= dp[i]) dq.pop_back();
        dq.push_back(i);
    }
    // hari n+1 adalah libur khayalan terakhir (a = 0)
    cout << total - dp[n + 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const a = [0, ...readInts(), 0];
let total = 0;
for (let i = 1; i <= n; i++) total += a[i];
const L = k + 1;
const dp = new Float64Array(n + 2);
const dq = new Int32Array(n + 2);
let head = 0, tail = 0;
dq[tail++] = 0;
for (let i = 1; i <= n + 1; i++) {
  while (dq[head] < i - L) head++;
  dp[i] = a[i] + dp[dq[head]];
  while (tail > head && dp[dq[tail - 1]] >= dp[i]) tail--;
  dq[tail++] = i;
}
console.log(total - dp[n + 1]);
CODE,
            'python' => <<<'CODE'
import sys
from collections import deque
input = sys.stdin.readline

n, k = map(int, input().split())
a = [0] + list(map(int, input().split())) + [0]
total = sum(a)
L = k + 1
dp = [0] * (n + 2)
dq = deque([0])
for i in range(1, n + 2):
    while dq[0] < i - L:
        dq.popleft()
    dp[i] = a[i] + dp[dq[0]]
    while dq and dp[dq[-1]] >= dp[i]:
        dq.pop()
    dq.append(i)
print(total - dp[n + 1])
CODE,
        ],
        'editorial' => '<p>Memaksimalkan panen sama dengan meminimalkan buah yang <strong>hilang</strong> karena libur. Syarat "tidak lebih dari K hari kerja berturut-turut" berarti di antara dua hari libur berurutan ada paling banyak K hari kerja: jarak keduanya ≤ K + 1.</p>
<p>Tambahkan hari libur khayalan di hari 0 dan hari N + 1 (bernilai 0). <code>dp[i]</code> = kehilangan minimum jika hari i libur dan semua syarat sebelum i terpenuhi. Libur sebelumnya berada di jendela i − K − 1 … i − 1, maka</p>
<p style="text-align:center"><code>dp[i] = a[i] + min(dp[i−K−1..i−1])</code></p>
<p>Ini persis bentuk deque monoton: O(N). Jawaban = total semua a − dp[N + 1].</p>
<p>Mencoba DP langsung pada "berapa hari kerja berturut-turut saat ini" juga mungkin (state dp[i][c]), tetapi O(N · K) terlalu lambat untuk K besar.</p>',
        'hints' => [
            'Daripada memilih hari kerja, pilih hari libur. Apa syarat untuk hari-hari libur?',
            'Jarak antara dua hari libur berurutan paling jauh K + 1. Tambahkan libur khayalan di hari 0 dan N + 1.',
            'dp[i] = a[i] + min(dp[i−K−1..i−1]) dengan deque monoton. Jawaban = total − dp[N+1].',
        ],
        'sample_visual' => 'bars',
    ],
];
