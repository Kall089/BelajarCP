<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan tambahan track Dynamic Programming (bagian 2):
 * LCS, LIS, DP interval, DP bitmask, DP pohon, digit DP.
 * Semua kode C++ harus lolos C++14. Bilangan sampai 10^18 dibaca sebagai long long
 * (C++), BigInt (JavaScript), atau int biasa (Python).
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

$MOD = 1000000007;

$word = fn (int $len, string $abc) => T::word($len, $abc);

/** Bilangan acak 0..10^18 (PHP int 64-bit). */
$bigRand = function (int $digits): int {
    $s = (string) mt_rand(1, 9);
    for ($i = 1; $i < $digits; $i++) {
        $s .= mt_rand(0, 9);
    }

    return (int) $s;
};

/** Pohon acak "N" + N−1 sisi, opsional dengan baris nilai. */
$treeWithValues = function (int $n, ?array $valRange, string $shape = 'acak'): string {
    $perm = range(1, $n);
    T::shuffle($perm);
    $edges = [];
    for ($i = 1; $i < $n; $i++) {
        $p = $shape === 'garis' ? $i - 1 : ($shape === 'bintang' ? 0 : mt_rand(max(0, $i - 30), $i - 1));
        $edges[] = [$perm[$p], $perm[$i]];
    }
    T::shuffle($edges);
    $out = [(string) $n];
    if ($valRange) {
        $vals = [];
        for ($i = 0; $i < $n; $i++) {
            $vals[] = mt_rand($valRange[0], $valRange[1]);
        }
        $out[] = implode(' ', $vals);
    }
    foreach ($edges as $e) {
        $out[] = "{$e[0]} {$e[1]}";
    }

    return implode("\n", $out)."\n";
};

/** Urutan BFS dari akar 1 beserta orang tua (PHP). */
$bfsOrder = function (int $n, array $adj): array {
    $par = array_fill(0, $n + 1, 0);
    $par[1] = -1;
    $order = [1];
    for ($h = 0; $h < count($order); $h++) {
        $u = $order[$h];
        foreach ($adj[$u] as $v) {
            if ($v !== $par[$u]) {
                $par[$v] = $u;
                $order[] = $v;
            }
        }
    }

    return [$order, $par];
};

$cppTreeVal = "    int n;\n    cin >> n;\n    vector<long long> nilai(n + 1);\n    for (int i = 1; i <= n; i++) cin >> nilai[i];\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < n - 1; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }";
$cppTree = "    int n;\n    cin >> n;\n    vector<vector<int>> adj(n + 1);\n    for (int i = 0; i < n - 1; i++) {\n        int u, v;\n        cin >> u >> v;\n        adj[u].push_back(v);\n        adj[v].push_back(u);\n    }";

return [
    // ═════════════════════════════ LCS & DP string ═════════════════════════════
    [
        'slug' => 'palindrom-terpanjang',
        'lesson' => 'lcs',
        'title' => 'Palindrom Tersembunyi',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'lcs', 'palindrom'],
        'statement' => '<p>Sebuah pesan rahasia berupa string <strong>S</strong>. Kata kuncinya adalah <strong>subsequence palindrom terpanjang</strong> dari S: huruf-huruf yang diambil berurutan (tidak harus bersebelahan) dan dibaca sama dari depan maupun belakang.</p>
<p>Berapa panjang kata kunci itu?</p>',
        'input_format' => '<p>Satu baris berisi string S (huruf kecil).</p>',
        'output_format' => '<p>Panjang subsequence palindrom terpanjang.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 1500</li></ul>',
        'samples' => [
            ['input' => "bbbab\n", 'explanation' => '"bbbb" adalah palindrom sepanjang 4.'],
            ['input' => "algoritma\n", 'explanation' => 'Misalnya "aga" atau "ala" → 3.'],
        ],
        'tests' => function () use ($word) {
            return ["a\n", "ab\n", $word(10, 'ab')."\n", $word(100, 'abc')."\n", $word(1500, 'abcdefghijklmnopqrstuvwxyz')."\n", $word(1500, 'ab')."\n", str_repeat('z', 1500)."\n"];
        },
        'solve' => function (string $input) {
            $s = trim($input);
            $r = strrev($s);
            $n = strlen($s);
            $prev = array_fill(0, $n + 1, 0);
            for ($i = 1; $i <= $n; $i++) {
                $cur = [0];
                $ch = $s[$i - 1];
                for ($j = 1; $j <= $n; $j++) {
                    $cur[$j] = $ch === $r[$j - 1] ? $prev[$j - 1] + 1 : max($prev[$j], $cur[$j - 1]);
                }
                $prev = $cur;
            }

            return (string) $prev[$n];
        },
        'starter' => $st("    string s;\n    cin >> s;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    string s;
    cin >> s;
    // Subsequence palindrom terpanjang = LCS antara s dan kebalikannya
    string r(s.rbegin(), s.rend());
    int n = s.size();
    vector<int> prev(n + 1, 0), cur(n + 1, 0);
    for (int i = 1; i <= n; i++) {
        for (int j = 1; j <= n; j++) {
            if (s[i - 1] == r[j - 1]) cur[j] = prev[j - 1] + 1;
            else cur[j] = max(prev[j], cur[j - 1]);
        }
        swap(prev, cur);
    }
    cout << prev[n] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const s = readLine().trim();
const r = s.split("").reverse().join("");
const n = s.length;
let prev = new Int32Array(n + 1), cur = new Int32Array(n + 1);
for (let i = 1; i <= n; i++) {
  for (let j = 1; j <= n; j++) {
    cur[j] = s[i - 1] === r[j - 1] ? prev[j - 1] + 1 : Math.max(prev[j], cur[j - 1]);
  }
  [prev, cur] = [cur, prev];
}
console.log(prev[n]);
CODE,
            'python' => <<<'CODE'
s = input().strip()
r = s[::-1]
n = len(s)
prev = [0] * (n + 1)
for i in range(1, n + 1):
    ch = s[i - 1]
    cur = [0] * (n + 1)
    for j in range(1, n + 1):
        if ch == r[j - 1]:
            cur[j] = prev[j - 1] + 1
        else:
            a, b = prev[j], cur[j - 1]
            cur[j] = a if a > b else b
    prev = cur
print(prev[n])
CODE,
        ],
        'editorial' => '<p>Palindrom dibaca sama dari depan dan belakang, jadi subsequence palindrom S juga merupakan subsequence dari <strong>kebalikan S</strong>. Sebaliknya, LCS antara S dan kebalikannya selalu bisa disusun menjadi palindrom. Maka jawabannya <code>LCS(S, reverse(S))</code>.</p>
<p>Cara lain (DP interval): <code>dp[l][r] = dp[l+1][r−1] + 2</code> jika S[l] = S[r], selain itu <code>max(dp[l+1][r], dp[l][r−1])</code>.</p>
<p>Dua baris tabel sudah cukup. <strong>Kompleksitas:</strong> O(|S|²).</p>',
        'hints' => [
            'Palindrom terlihat sama jika dibalik. Hubungkan S dengan kebalikannya.',
            'Jawabannya LCS antara S dan reverse(S).',
            'Atau DP interval: jika huruf ujung sama, dp[l][r] = dp[l+1][r−1] + 2.',
        ],
    ],

    [
        'slug' => 'substring-bersama',
        'lesson' => 'lcs',
        'title' => 'Potongan Lagu yang Sama',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'string', 'substring'],
        'statement' => '<p>Dua lagu ditulis sebagai string not <strong>A</strong> dan <strong>B</strong>. Seorang juri ingin memeriksa plagiarisme: berapa panjang <strong>potongan berurutan</strong> (substring) terpanjang yang muncul di kedua lagu?</p>
<p>Berbeda dengan subsequence, huruf-huruf substring harus <strong>bersebelahan</strong>.</p>',
        'input_format' => '<p>Dua baris berisi A dan B (huruf kecil).</p>',
        'output_format' => '<p>Panjang substring bersama terpanjang (0 jika tidak ada).</p>',
        'constraints' => '<ul><li>1 ≤ |A|, |B| ≤ 1500</li></ul>',
        'samples' => [
            ['input' => "dolasilado\nmisilasol\n", 'explanation' => '"sila" muncul di keduanya (panjang 4).'],
            ['input' => "abc\nxyz\n", 'explanation' => 'Tidak ada huruf yang sama.'],
        ],
        'tests' => function () use ($word) {
            $plant = function (int $n, int $m, int $k, string $abc) use ($word) {
                $c = $word($k, $abc);
                $a = $word($n, $abc);
                $b = $word($m, $abc);
                $pa = mt_rand(0, $n - $k);
                $pb = mt_rand(0, $m - $k);

                return substr($a, 0, $pa).$c.substr($a, $pa + $k)."\n".substr($b, 0, $pb).$c.substr($b, $pb + $k)."\n";
            };

            return ["a\na\n", "a\nb\n", $word(10, 'ab')."\n".$word(8, 'ab')."\n", $plant(100, 120, 20, 'abcd'), $plant(1500, 1500, 300, 'abcdefghij'), $word(1500, 'ab')."\n".$word(1500, 'ab')."\n", $plant(1500, 1400, 2, 'abcdefghijklmnopqrstuvwxyz')];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $a = trim($lines[0]);
            $b = trim($lines[1]);
            $n = strlen($a);
            $m = strlen($b);
            $prev = array_fill(0, $m + 1, 0);
            $best = 0;
            for ($i = 1; $i <= $n; $i++) {
                $cur = array_fill(0, $m + 1, 0);
                $ch = $a[$i - 1];
                for ($j = 1; $j <= $m; $j++) {
                    if ($ch === $b[$j - 1]) {
                        $cur[$j] = $prev[$j - 1] + 1;
                        if ($cur[$j] > $best) {
                            $best = $cur[$j];
                        }
                    }
                }
                $prev = $cur;
            }

            return (string) $best;
        },
        'starter' => $st("    string a, b;\n    cin >> a >> b;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    string a, b;
    cin >> a >> b;
    int n = a.size(), m = b.size();
    // dp[i][j] = panjang substring bersama yang BERAKHIR tepat di a[i-1] dan b[j-1]
    vector<int> prev(m + 1, 0), cur(m + 1, 0);
    int terbaik = 0;
    for (int i = 1; i <= n; i++) {
        for (int j = 1; j <= m; j++) {
            if (a[i - 1] == b[j - 1]) cur[j] = prev[j - 1] + 1;   // sambung diagonal
            else cur[j] = 0;                                      // putus: harus bersebelahan
            terbaik = max(terbaik, cur[j]);
        }
        swap(prev, cur);
    }
    cout << terbaik << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const a = readLine().trim(), b = readLine().trim();
const n = a.length, m = b.length;
let prev = new Int32Array(m + 1), cur = new Int32Array(m + 1), best = 0;
for (let i = 1; i <= n; i++) {
  for (let j = 1; j <= m; j++) {
    cur[j] = a[i - 1] === b[j - 1] ? prev[j - 1] + 1 : 0;
    if (cur[j] > best) best = cur[j];
  }
  [prev, cur] = [cur, prev];
}
console.log(best);
CODE,
            'python' => <<<'CODE'
a = input().strip()
b = input().strip()
n, m = len(a), len(b)
prev = [0] * (m + 1)
best = 0
for i in range(1, n + 1):
    ch = a[i - 1]
    cur = [0] * (m + 1)
    for j in range(1, m + 1):
        if ch == b[j - 1]:
            cur[j] = prev[j - 1] + 1
    best = max(best, max(cur))
    prev = cur
print(best)
CODE,
        ],
        'editorial' => '<p>Gunakan tabel seperti LCS, tetapi dengan state <code>dp[i][j]</code> = panjang substring bersama yang <strong>berakhir tepat</strong> di A[i−1] dan B[j−1]. Jika kedua huruf sama, substring bisa disambung dari diagonal: <code>dp[i−1][j−1] + 1</code>. Jika berbeda, substring yang berakhir di sini tidak ada: <code>0</code>.</p>
<p>Jawabannya maksimum seluruh tabel, bukan <code>dp[n][m]</code>. <strong>Kompleksitas:</strong> O(|A| · |B|) waktu, O(|B|) memori.</p>',
        'hints' => [
            'Mirip LCS, tetapi hurufnya harus bersebelahan. Apa yang terjadi saat huruf berbeda?',
            'State: panjang substring bersama yang berakhir tepat di A[i] dan B[j].',
            'Sama → diagonal + 1. Beda → 0. Jawaban = maksimum seluruh tabel.',
        ],
    ],

    // ═════════════════════════════ LIS ═════════════════════════════
    [
        'slug' => 'jumlah-naik-terbesar',
        'lesson' => 'lis',
        'title' => 'Tangga Nilai Terbesar',
        'difficulty' => 'Sedang',
        'tags' => ['dp', 'lis'],
        'statement' => '<p>Diberikan <strong>N</strong> bilangan. Pilih subsequence (urutan tetap, tidak harus bersebelahan) yang <strong>naik tegas</strong> sehingga <strong>jumlahnya</strong> sebesar mungkin. Berapa jumlah terbesar itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N bilangan positif.</p>',
        'output_format' => '<p>Jumlah maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 2000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "6\n1 101 2 3 100 4\n", 'explanation' => '1, 2, 3, 100 → 106. Subsequence terpanjang belum tentu berjumlah terbesar.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, $max);
                }

                return "$n\n".implode(' ', $a)."\n";
            };

            return ["1\n5\n", "3\n5 5 5\n", $mk(10, 20), $mk(300, 1000), $mk(2000, 1000000000), $mk(2000, 50), "5\n5 4 3 2 1\n"];
        },
        'solve' => function (string $input) {
            $a = T::ints(T::lines($input)[1]);
            $n = count($a);
            $dp = [];
            $best = 0;
            for ($i = 0; $i < $n; $i++) {
                $b = 0;
                for ($j = 0; $j < $i; $j++) {
                    if ($a[$j] < $a[$i] && $dp[$j] > $b) {
                        $b = $dp[$j];
                    }
                }
                $dp[$i] = $b + $a[$i];
                $best = max($best, $dp[$i]);
            }

            return (string) $best;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> a(n);\n    for (auto& x : a) cin >> x;", "const [n] = readInts();\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // dp[i] = jumlah terbesar subsequence naik yang BERAKHIR di a[i]
    vector<long long> dp(n);
    long long jawaban = 0;
    for (int i = 0; i < n; i++) {
        dp[i] = a[i];                                 // a[i] sendirian
        for (int j = 0; j < i; j++)
            if (a[j] < a[i]) dp[i] = max(dp[i], dp[j] + a[i]);
        jawaban = max(jawaban, dp[i]);
    }
    cout << jawaban << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
const dp = new Array(n).fill(0);
let best = 0;
for (let i = 0; i < n; i++) {
  let b = 0;
  for (let j = 0; j < i; j++) if (a[j] < a[i] && dp[j] > b) b = dp[j];
  dp[i] = b + a[i];
  if (dp[i] > best) best = dp[i];
}
console.log(best);
CODE,
            'python' => <<<'CODE'
n = int(input())
a = list(map(int, input().split()))
dp = [0] * n
for i in range(n):
    ai = a[i]
    b = 0
    for j in range(i):
        if a[j] < ai and dp[j] > b:
            b = dp[j]
    dp[i] = b + ai
print(max(dp))
CODE,
        ],
        'editorial' => '<p>Sama persis dengan LIS O(N²), hanya yang dioptimalkan <strong>jumlah</strong>, bukan panjang:</p>
<p style="text-align:center"><code>dp[i] = a[i] + max(dp[j])</code> untuk j &lt; i dengan a[j] &lt; a[i] (atau 0 jika tidak ada).</p>
<p>Jawabannya maksimum seluruh dp. Contoh menunjukkan bahwa subsequence terpanjang (1, 2, 3, 4) bukan yang berjumlah terbesar (1, 2, 3, 100). Total bisa mencapai 2 · 10<sup>12</sup>: <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(N²).</p>',
        'hints' => [
            'Pola state-nya sama dengan LIS: "berakhir di i".',
            'Ganti "panjang + 1" dengan "jumlah + a[i]".',
            'dp[i] = a[i] + max(dp[j]) untuk a[j] < a[i]; jawaban = maksimum dp.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'banyak-lis',
        'lesson' => 'lis',
        'title' => 'Berapa Banyak LIS?',
        'difficulty' => 'Sulit',
        'tags' => ['dp', 'lis', 'menghitung cara'],
        'statement' => '<p>Diberikan <strong>N</strong> bilangan. Tentukan panjang subsequence naik tegas terpanjang (LIS), dan <strong>berapa banyak</strong> subsequence berbeda yang mencapai panjang itu. Dua subsequence berbeda jika himpunan <em>posisi</em> yang dipilih berbeda. Cetak banyaknya modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N bilangan.</p>',
        'output_format' => '<p>Dua bilangan: panjang LIS dan banyaknya (mod 10<sup>9</sup> + 7).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 2000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n1 3 5 4 7\n", 'explanation' => 'Panjang 4: 1-3-5-7 dan 1-3-4-7.'],
            ['input' => "5\n2 2 2 2 2\n", 'explanation' => 'Panjang 1, ada 5 pilihan posisi.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, $max);
                }

                return "$n\n".implode(' ', $a)."\n";
            };
            // Banyak LIS: blok-blok berisi beberapa angka yang tersusun turun
            $blocks = function (int $k, int $w) {
                $a = [];
                for ($b = 0; $b < $k; $b++) {
                    for ($j = $w; $j >= 1; $j--) {
                        $a[] = $b * 100 + $j;
                    }
                }

                return count($a)."\n".implode(' ', $a)."\n";
            };

            return ["1\n5\n", $mk(10, 5), $mk(200, 100), $mk(2000, 1000000000), $mk(2000, 30), $blocks(400, 5), $blocks(1000, 2)];
        },
        'solve' => function (string $input) use ($MOD) {
            $a = T::ints(T::lines($input)[1]);
            $n = count($a);
            $len = [];
            $cnt = [];
            for ($i = 0; $i < $n; $i++) {
                $len[$i] = 1;
                $cnt[$i] = 1;
                for ($j = 0; $j < $i; $j++) {
                    if ($a[$j] < $a[$i]) {
                        if ($len[$j] + 1 > $len[$i]) {
                            $len[$i] = $len[$j] + 1;
                            $cnt[$i] = $cnt[$j];
                        } elseif ($len[$j] + 1 === $len[$i]) {
                            $cnt[$i] = ($cnt[$i] + $cnt[$j]) % $MOD;
                        }
                    }
                }
            }
            $L = max($len);
            $tot = 0;
            for ($i = 0; $i < $n; $i++) {
                if ($len[$i] === $L) {
                    $tot = ($tot + $cnt[$i]) % $MOD;
                }
            }

            return "$L $tot";
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> a(n);\n    for (auto& x : a) cin >> x;", "const [n] = readInts();\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n;
    cin >> n;
    vector<long long> a(n);
    for (auto& x : a) cin >> x;

    // len[i] = panjang LIS yang berakhir di i, cnt[i] = banyaknya
    vector<int> len(n, 1);
    vector<long long> cnt(n, 1);
    for (int i = 0; i < n; i++)
        for (int j = 0; j < i; j++) {
            if (a[j] >= a[i]) continue;
            if (len[j] + 1 > len[i]) {          // panjang baru yang lebih baik: hitungan dimulai ulang
                len[i] = len[j] + 1;
                cnt[i] = cnt[j];
            } else if (len[j] + 1 == len[i]) {  // cara lain mencapai panjang yang sama
                cnt[i] = (cnt[i] + cnt[j]) % MOD;
            }
        }
    int L = *max_element(len.begin(), len.end());
    long long total = 0;
    for (int i = 0; i < n; i++)
        if (len[i] == L) total = (total + cnt[i]) % MOD;
    cout << L << ' ' << total << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
const MOD = 1000000007;
const len = new Array(n).fill(1), cnt = new Array(n).fill(1);
for (let i = 0; i < n; i++)
  for (let j = 0; j < i; j++) {
    if (a[j] >= a[i]) continue;
    if (len[j] + 1 > len[i]) { len[i] = len[j] + 1; cnt[i] = cnt[j]; }
    else if (len[j] + 1 === len[i]) cnt[i] = (cnt[i] + cnt[j]) % MOD;
  }
const L = Math.max(...len);
let total = 0;
for (let i = 0; i < n; i++) if (len[i] === L) total = (total + cnt[i]) % MOD;
console.log(L + " " + total);
CODE,
            'python' => <<<'CODE'
n = int(input())
a = list(map(int, input().split()))
MOD = 10**9 + 7
ln = [1] * n
cnt = [1] * n
for i in range(n):
    ai = a[i]
    for j in range(i):
        if a[j] < ai:
            if ln[j] + 1 > ln[i]:
                ln[i] = ln[j] + 1
                cnt[i] = cnt[j]
            elif ln[j] + 1 == ln[i]:
                cnt[i] = (cnt[i] + cnt[j]) % MOD
L = max(ln)
print(L, sum(c for l, c in zip(ln, cnt) if l == L) % MOD)
CODE,
        ],
        'editorial' => '<p>Simpan dua nilai untuk setiap i: <code>len[i]</code> = panjang LIS yang berakhir di i, dan <code>cnt[i]</code> = banyak LIS seperti itu. Untuk setiap j &lt; i dengan a[j] &lt; a[i]:</p>
<ul><li>Jika <code>len[j] + 1 &gt; len[i]</code>: panjang baru lebih baik, semua cara lama tidak berlaku. <code>cnt[i] = cnt[j]</code>.</li>
<li>Jika <code>len[j] + 1 == len[i]</code>: cara lain dengan panjang sama. <code>cnt[i] += cnt[j]</code>.</li></ul>
<p>Jawabannya jumlah <code>cnt[i]</code> untuk semua i dengan <code>len[i]</code> = panjang maksimum. Polanya sama dengan menghitung rute tercepat pada Dijkstra.</p>
<p><strong>Kompleksitas:</strong> O(N²).</p>',
        'hints' => [
            'Selain panjang LIS yang berakhir di i, simpan juga berapa banyak cara mencapainya.',
            'Panjang lebih baik → hitungan diganti; panjang sama → hitungan ditambah.',
            'Jawaban = jumlah cnt[i] untuk semua i yang len[i] = panjang maksimum.',
        ],
    ],

    // ═════════════════════════════ DP interval ═════════════════════════════
    [
        'slug' => 'rantai-matriks',
        'lesson' => 'dp-interval',
        'title' => 'Perkalian Rantai Matriks',
        'difficulty' => 'Sedang',
        'tags' => ['dp interval', 'matriks'],
        'statement' => '<p>Ada <strong>N</strong> matriks yang harus dikalikan berurutan: M<sub>1</sub> · M<sub>2</sub> · … · M<sub>N</sub>. Matriks ke-i berukuran <code>p<sub>i−1</sub> × p<sub>i</sub></code>. Mengalikan matriks a × b dengan matriks b × c membutuhkan <strong>a · b · c</strong> perkalian bilangan dan menghasilkan matriks a × c.</p>
<p>Perkalian matriks bersifat asosiatif, jadi kita bebas memilih urutan pengurungan. Berapa banyak perkalian bilangan <strong>minimum</strong>?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi N + 1 bilangan p<sub>0</sub> … p<sub>N</sub>.</p>',
        'output_format' => '<p>Banyak perkalian minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200</li><li>1 ≤ p<sub>i</sub> ≤ 100</li></ul>',
        'samples' => [
            ['input' => "3\n10 30 5 60\n", 'explanation' => '(M1 · M2) · M3 = 10·30·5 + 10·5·60 = 1500 + 3000 = 4500, lebih baik dari M1 · (M2 · M3) = 30·5·60 + 10·30·60 = 27000.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $max) {
                $p = [];
                for ($i = 0; $i <= $n; $i++) {
                    $p[] = mt_rand(1, $max);
                }

                return "$n\n".implode(' ', $p)."\n";
            };

            return ["1\n5 7\n", "2\n2 3 4\n", $mk(6, 20), $mk(50, 100), $mk(200, 100), $mk(200, 5)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $p = T::ints($lines[1]);
            $dp = array_fill(0, $n, array_fill(0, $n, 0));
            for ($len = 2; $len <= $n; $len++) {
                for ($l = 0; $l + $len - 1 < $n; $l++) {
                    $r = $l + $len - 1;
                    $best = PHP_INT_MAX;
                    for ($k = $l; $k < $r; $k++) {
                        $c = $dp[$l][$k] + $dp[$k + 1][$r] + $p[$l] * $p[$k + 1] * $p[$r + 1];
                        if ($c < $best) {
                            $best = $c;
                        }
                    }
                    $dp[$l][$r] = $best;
                }
            }

            return (string) $dp[0][$n - 1];
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<long long> p(n + 1);\n    for (auto& x : p) cin >> x;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int n;
    cin >> n;
    vector<long long> p(n + 1);
    for (auto& x : p) cin >> x;

    // dp[l][r] = biaya minimum mengalikan M_l ... M_r (indeks 0-based)
    // Matriks i berukuran p[i] x p[i+1]
    vector<vector<long long>> dp(n, vector<long long>(n, 0));
    for (int len = 2; len <= n; len++)
        for (int l = 0; l + len - 1 < n; l++) {
            int r = l + len - 1;
            dp[l][r] = LLONG_MAX;
            for (int k = l; k < r; k++) {
                // perkalian terakhir: (M_l..M_k) x (M_k+1..M_r) = p[l] x p[k+1] dikali p[k+1] x p[r+1]
                long long biaya = dp[l][k] + dp[k + 1][r] + p[l] * p[k + 1] * p[r + 1];
                dp[l][r] = min(dp[l][r], biaya);
            }
        }
    cout << dp[0][n - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const p = readInts();
const dp = Array.from({ length: n }, () => new Array(n).fill(0));
for (let len = 2; len <= n; len++)
  for (let l = 0; l + len - 1 < n; l++) {
    const r = l + len - 1;
    let best = Infinity;
    for (let k = l; k < r; k++) {
      const c = dp[l][k] + dp[k + 1][r] + p[l] * p[k + 1] * p[r + 1];
      if (c < best) best = c;
    }
    dp[l][r] = best;
  }
console.log(dp[0][n - 1]);
CODE,
            'python' => <<<'CODE'
n = int(input())
p = list(map(int, input().split()))
dp = [[0] * n for _ in range(n)]
for length in range(2, n + 1):
    for l in range(n - length + 1):
        r = l + length - 1
        pl, pr = p[l], p[r + 1]
        dl = dp[l]
        dp[l][r] = min(dl[k] + dp[k + 1][r] + pl * p[k + 1] * pr for k in range(l, r))
print(dp[0][n - 1])
CODE,
        ],
        'editorial' => '<p>Lihat <strong>perkalian terakhir</strong>: hasil M<sub>l</sub>…M<sub>k</sub> (ukuran p<sub>l</sub> × p<sub>k+1</sub>) dikali hasil M<sub>k+1</sub>…M<sub>r</sub> (ukuran p<sub>k+1</sub> × p<sub>r+1</sub>), berbiaya p<sub>l</sub> · p<sub>k+1</sub> · p<sub>r+1</sub>. Maka</p>
<p style="text-align:center"><code>dp[l][r] = min<sub>k</sub> (dp[l][k] + dp[k+1][r] + p[l] · p[k+1] · p[r+1])</code></p>
<p>dengan <code>dp[i][i] = 0</code>. Isi berdasarkan panjang interval. Ini DP interval klasik, sama strukturnya dengan menggabungkan tumpukan batu.</p>
<p><strong>Kompleksitas:</strong> O(N³).</p>',
        'hints' => [
            'Pengurungan mana pun pasti punya satu perkalian TERAKHIR yang membelah rantai menjadi dua bagian.',
            'Jika belahannya di k, biayanya dp[l][k] + dp[k+1][r] + p[l]·p[k+1]·p[r+1].',
            'Isi dp berdasarkan panjang interval; jawaban dp[0][N−1].',
        ],
    ],

    [
        'slug' => 'sisip-palindrom',
        'lesson' => 'dp-interval',
        'title' => 'Jadikan Palindrom',
        'difficulty' => 'Sedang',
        'tags' => ['dp interval', 'palindrom', 'string'],
        'statement' => '<p>Diberikan string <strong>S</strong>. Kamu boleh menyisipkan huruf apa pun di posisi mana pun. Berapa huruf paling sedikit yang harus disisipkan agar S menjadi <strong>palindrom</strong>?</p>',
        'input_format' => '<p>Satu baris berisi S (huruf kecil).</p>',
        'output_format' => '<p>Banyak sisipan minimum.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 2000</li></ul>',
        'samples' => [
            ['input' => "abcb\n", 'explanation' => 'Sisipkan "a" di akhir: abcba.'],
            ['input' => "kodok\n", 'explanation' => 'Sudah palindrom.'],
        ],
        'tests' => function () use ($word) {
            return ["a\n", "ab\n", $word(10, 'abc')."\n", $word(200, 'ab')."\n", $word(2000, 'abcdefghijklmnopqrstuvwxyz')."\n", $word(2000, 'ab')."\n", str_repeat('ab', 1000)."\n"];
        },
        'solve' => function (string $input) {
            $s = trim($input);
            $n = strlen($s);
            // dp untuk panjang len-2 dan len-1 (bergulir)
            $d2 = array_fill(0, $n + 1, 0);
            $d1 = array_fill(0, $n, 0);
            for ($len = 2; $len <= $n; $len++) {
                $cur = [];
                for ($l = 0; $l + $len - 1 < $n; $l++) {
                    $r = $l + $len - 1;
                    $cur[$l] = $s[$l] === $s[$r] ? $d2[$l + 1] : 1 + min($d1[$l + 1], $d1[$l]);
                }
                $d2 = $d1;
                $d1 = $cur;
            }

            return (string) ($n === 1 ? 0 : $d1[0]);
        },
        'starter' => $st("    string s;\n    cin >> s;\n    int n = s.size();"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    string s;
    cin >> s;
    int n = s.size();
    // dp[l][r] = sisipan minimum agar s[l..r] menjadi palindrom
    vector<vector<int>> dp(n, vector<int>(n, 0));
    for (int len = 2; len <= n; len++)
        for (int l = 0; l + len - 1 < n; l++) {
            int r = l + len - 1;
            if (s[l] == s[r])
                dp[l][r] = (len == 2) ? 0 : dp[l + 1][r - 1];   // ujung cocok, abaikan keduanya
            else
                dp[l][r] = 1 + min(dp[l + 1][r], dp[l][r - 1]);  // sisipkan pasangan salah satu ujung
        }
    cout << dp[0][n - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const s = readLine().trim();
const n = s.length;
// Hanya butuh baris panjang len-1 dan len-2
let d2 = new Int32Array(n + 1), d1 = new Int32Array(n + 1);
for (let len = 2; len <= n; len++) {
  const cur = new Int32Array(n + 1);
  for (let l = 0; l + len - 1 < n; l++) {
    const r = l + len - 1;
    cur[l] = s[l] === s[r] ? d2[l + 1] : 1 + Math.min(d1[l + 1], d1[l]);
  }
  d2 = d1;
  d1 = cur;
}
console.log(n === 1 ? 0 : d1[0]);
CODE,
            'python' => <<<'CODE'
s = input().strip()
n = len(s)
d2 = [0] * (n + 1)   # interval sepanjang len-2
d1 = [0] * (n + 1)   # interval sepanjang len-1
for length in range(2, n + 1):
    cur = [0] * (n + 1)
    for l in range(n - length + 1):
        r = l + length - 1
        if s[l] == s[r]:
            cur[l] = d2[l + 1]
        else:
            a, b = d1[l + 1], d1[l]
            cur[l] = 1 + (a if a < b else b)
    d2, d1 = d1, cur
print(0 if n == 1 else d1[0])
CODE,
        ],
        'editorial' => '<p>Lihat kedua <strong>ujung</strong> potongan s[l..r]:</p>
<ul><li>Jika s[l] = s[r], keduanya sudah berpasangan: <code>dp[l][r] = dp[l+1][r−1]</code>.</li>
<li>Jika berbeda, salah satu ujung harus diberi pasangan sisipan: sisipkan tiruan s[l] di kanan (sisa s[l+1..r]) atau tiruan s[r] di kiri (sisa s[l..r−1]). <code>dp[l][r] = 1 + min(dp[l+1][r], dp[l][r−1])</code>.</li></ul>
<p>Isi berdasarkan panjang. Karena panjang len hanya membutuhkan panjang len−1 dan len−2, memori bisa O(N).</p>
<p><strong>Fakta menarik:</strong> jawabannya sama dengan |S| − (subsequence palindrom terpanjang).</p>
<p><strong>Kompleksitas:</strong> O(|S|²).</p>',
        'hints' => [
            'Perhatikan huruf pertama dan terakhir. Kapan keduanya bisa "diabaikan"?',
            'Jika sama, jawaban = dp[l+1][r−1]. Jika beda, satu ujung harus diberi pasangan sisipan.',
            'dp[l][r] = 1 + min(dp[l+1][r], dp[l][r−1]) untuk ujung berbeda. Isi berdasarkan panjang.',
        ],
    ],

    // ═════════════════════════════ DP bitmask ═════════════════════════════
    [
        'slug' => 'jalur-hamilton',
        'lesson' => 'dp-bitmask',
        'title' => 'Tur Semua Kota',
        'difficulty' => 'Sulit',
        'tags' => ['dp bitmask', 'jalur hamilton', 'menghitung cara'],
        'statement' => '<p>Ada <strong>N</strong> kota dan <strong>M</strong> penerbangan <strong>satu arah</strong> (tidak ada penerbangan ganda). Sebuah agen tur ingin membuat rute yang dimulai di kota <strong>1</strong>, berakhir di kota <strong>N</strong>, dan mengunjungi <strong>setiap kota tepat sekali</strong>.</p>
<p>Ada berapa rute seperti itu? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>Banyak rute modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 15</li><li>0 ≤ M ≤ N(N − 1)</li></ul>',
        'samples' => [
            ['input' => "4 6\n1 2\n1 3\n2 3\n3 2\n2 4\n3 4\n", 'explanation' => '1 → 2 → 3 → 4 dan 1 → 3 → 2 → 4.'],
        ],
        'tests' => function () {
            $mk = function (int $n, float $p) {
                $edges = [];
                for ($a = 1; $a <= $n; $a++) {
                    for ($b = 1; $b <= $n; $b++) {
                        if ($a !== $b && mt_rand() / mt_getrandmax() < $p) {
                            $edges[] = [$a, $b];
                        }
                    }
                }
                T::shuffle($edges);

                return T::graphInput("$n ".count($edges), $edges);
            };

            return ["2 1\n1 2\n", "2 1\n2 1\n", $mk(5, 0.6), $mk(8, 0.5), $mk(12, 0.6), $mk(15, 0.9), $mk(15, 1.0), $mk(15, 0.4)];
        },
        'solve' => function (string $input) use ($MOD) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $in = array_fill(0, $n, []);
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $in[$b - 1][] = $a - 1;
            }
            $FULL = 1 << $n;
            $dp = array_fill(0, $FULL * $n, 0);
            $dp[1 * $n + 0] = 1;
            for ($mask = 1; $mask < $FULL; $mask += 2) {
                for ($v = 1; $v < $n; $v++) {
                    if (! ($mask >> $v & 1)) {
                        continue;
                    }
                    if ($v === $n - 1 && $mask !== $FULL - 1) {
                        continue;
                    }
                    $pm = $mask ^ (1 << $v);
                    $s = 0;
                    foreach ($in[$v] as $u) {
                        if ($pm >> $u & 1) {
                            $s += $dp[$pm * $n + $u];
                        }
                    }
                    $dp[$mask * $n + $v] = $s % $MOD;
                }
            }

            return (string) $dp[($FULL - 1) * $n + $n - 1];
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<vector<int>> masuk(n);   // masuk[v] = kota asal penerbangan ke v (0-based)\n    for (int i = 0; i < m; i++) {\n        int a, b;\n        cin >> a >> b;\n        masuk[b - 1].push_back(a - 1);\n    }"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n, m;
    cin >> n >> m;
    vector<vector<int>> masuk(n);          // masuk[v] = kota u dengan penerbangan u -> v (0-based)
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        masuk[b - 1].push_back(a - 1);
    }

    // dp[mask][v] = banyak rute dari kota 0 yang mengunjungi tepat kota di mask dan berakhir di v
    int FULL = 1 << n;
    vector<vector<long long>> dp(FULL, vector<long long>(n, 0));
    dp[1][0] = 1;
    for (int mask = 1; mask < FULL; mask += 2) {          // kota 0 selalu ada di mask
        for (int v = 1; v < n; v++) {
            if (!(mask >> v & 1)) continue;
            if (v == n - 1 && mask != FULL - 1) continue; // kota terakhir hanya boleh di akhir
            int sebelum = mask ^ (1 << v);
            long long total = 0;
            for (int u : masuk[v])                        // langkah terakhir: u -> v
                if (sebelum >> u & 1) total += dp[sebelum][u];
            dp[mask][v] = total % MOD;
        }
    }
    cout << dp[FULL - 1][n - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, m] = readInts();
const masuk = Array.from({ length: n }, () => []);
for (let i = 0; i < m; i++) {
  const [a, b] = readInts();
  masuk[b - 1].push(a - 1);
}
const MOD = 1000000007;
const FULL = 1 << n;
const dp = new Float64Array(FULL * n);
dp[1 * n + 0] = 1;
for (let mask = 1; mask < FULL; mask += 2) {
  for (let v = 1; v < n; v++) {
    if (!((mask >> v) & 1)) continue;
    if (v === n - 1 && mask !== FULL - 1) continue;
    const pm = mask ^ (1 << v);
    let s = 0;
    for (const u of masuk[v]) if ((pm >> u) & 1) s += dp[pm * n + u];
    dp[mask * n + v] = s % MOD;
  }
}
console.log(dp[(FULL - 1) * n + n - 1]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
masuk = [[] for _ in range(n)]
for _ in range(m):
    a, b = map(int, input().split())
    masuk[b - 1].append(a - 1)

MOD = 10**9 + 7
FULL = 1 << n
dp = [[0] * n for _ in range(FULL)]
dp[1][0] = 1
for mask in range(1, FULL, 2):
    row = dp[mask]
    for v in range(1, n):
        if not (mask >> v) & 1:
            continue
        if v == n - 1 and mask != FULL - 1:
            continue
        pm = mask ^ (1 << v)
        prev = dp[pm]
        total = 0
        for u in masuk[v]:
            if (pm >> u) & 1:
                total += prev[u]
        row[v] = total % MOD
print(dp[FULL - 1][n - 1])
CODE,
        ],
        'editorial' => '<p>Rute yang mengunjungi semua simpul tepat sekali disebut <strong>jalur Hamilton</strong>. Mencoba semua urutan butuh (N − 2)! ≈ 6 · 10<sup>9</sup> untuk N = 15. Yang perlu diingat di tengah rute hanya <em>himpunan</em> kota yang sudah dikunjungi dan kota terakhir.</p>
<p><code>dp[mask][v]</code> = banyak rute dari kota 1 yang mengunjungi tepat kota-kota di mask dan berakhir di v. Lihat langkah terakhir u → v: <code>dp[mask][v] = Σ dp[mask tanpa v][u]</code> untuk setiap penerbangan u → v.</p>
<p>Pemangkasan penting: kota N hanya boleh dikunjungi paling akhir, jadi abaikan state yang berakhir di N sebelum mask penuh. Jawaban: <code>dp[semua][N]</code>.</p>
<p><strong>Kompleksitas:</strong> O(2<sup>N</sup> · (N + M)).</p>',
        'hints' => [
            'Mencoba semua urutan kota terlalu lambat. Apa yang benar-benar perlu diingat di tengah rute?',
            'Himpunan kota yang sudah dikunjungi (bitmask) dan kota terakhir.',
            'dp[mask][v] = jumlah dp[mask tanpa v][u] untuk setiap u → v. Kota N hanya boleh di akhir.',
        ],
        'sample_visual' => 'digraph',
    ],

    [
        'slug' => 'pasangan-belajar',
        'lesson' => 'dp-bitmask',
        'title' => 'Pasangan Belajar',
        'difficulty' => 'Sedang',
        'tags' => ['dp bitmask', 'pencocokan'],
        'statement' => '<p>Guru ingin membagi <strong>2K</strong> siswa menjadi K pasangan belajar. Untuk setiap dua siswa i dan j diketahui <code>c[i][j]</code>, tingkat kecocokan mereka (simetris: c[i][j] = c[j][i]).</p>
<p>Bagi semua siswa menjadi pasangan agar <strong>total kecocokan</strong> sebesar mungkin.</p>',
        'input_format' => '<p>Baris pertama berisi <code>K</code>. 2K baris berikutnya berisi matriks c (2K bilangan per baris; c[i][i] = 0).</p>',
        'output_format' => '<p>Total kecocokan maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ 8 (paling banyak 16 siswa)</li><li>0 ≤ c[i][j] ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "2\n0 5 2 1\n5 0 3 4\n2 3 0 6\n1 4 6 0\n", 'explanation' => 'Pasangan (1, 2) dan (3, 4): 5 + 6 = 11.'],
        ],
        'tests' => function () {
            $mk = function (int $k, int $max) {
                $n = 2 * $k;
                $c = array_fill(0, $n, array_fill(0, $n, 0));
                for ($i = 0; $i < $n; $i++) {
                    for ($j = $i + 1; $j < $n; $j++) {
                        $c[$i][$j] = $c[$j][$i] = mt_rand(0, $max);
                    }
                }

                return "$k\n".implode("\n", array_map(fn ($r) => implode(' ', $r), $c))."\n";
            };

            return ["1\n0 7\n7 0\n", $mk(2, 10), $mk(4, 100), $mk(6, 1000000), $mk(8, 1000000), $mk(8, 3)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $k = (int) $lines[0];
            $n = 2 * $k;
            $c = [];
            for ($i = 0; $i < $n; $i++) {
                $c[] = T::ints($lines[1 + $i]);
            }
            $FULL = 1 << $n;
            $dp = array_fill(0, $FULL, -1);
            $dp[0] = 0;
            for ($mask = 0; $mask < $FULL; $mask++) {
                if ($dp[$mask] < 0) {
                    continue;
                }
                $i = 0;
                while ($i < $n && ($mask >> $i & 1)) {
                    $i++;
                }
                if ($i === $n) {
                    continue;
                }
                for ($j = $i + 1; $j < $n; $j++) {
                    if (! ($mask >> $j & 1)) {
                        $nm = $mask | (1 << $i) | (1 << $j);
                        $dp[$nm] = max($dp[$nm], $dp[$mask] + $c[$i][$j]);
                    }
                }
            }

            return (string) $dp[$FULL - 1];
        },
        'starter' => $st("    int k;\n    cin >> k;\n    int n = 2 * k;\n    vector<vector<long long>> c(n, vector<long long>(n));\n    for (auto& baris : c)\n        for (auto& x : baris) cin >> x;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    int k;
    cin >> k;
    int n = 2 * k;
    vector<vector<long long>> c(n, vector<long long>(n));
    for (auto& baris : c)
        for (auto& x : baris) cin >> x;

    // dp[mask] = total terbaik jika siswa di mask sudah berpasangan
    int FULL = 1 << n;
    vector<long long> dp(FULL, -1);
    dp[0] = 0;
    for (int mask = 0; mask < FULL; mask++) {
        if (dp[mask] < 0) continue;
        // Siswa bernomor TERKECIL yang belum berpasangan pasti dipasangkan sekarang.
        // Ini mencegah pasangan yang sama terhitung dalam urutan berbeda.
        int i = 0;
        while (i < n && (mask >> i & 1)) i++;
        if (i == n) continue;
        for (int j = i + 1; j < n; j++) {
            if (mask >> j & 1) continue;
            int baru = mask | (1 << i) | (1 << j);
            dp[baru] = max(dp[baru], dp[mask] + c[i][j]);
        }
    }
    cout << dp[FULL - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [k] = readInts();
const n = 2 * k;
const c = [];
for (let i = 0; i < n; i++) c.push(readInts());
const FULL = 1 << n;
const dp = new Float64Array(FULL).fill(-1);
dp[0] = 0;
for (let mask = 0; mask < FULL; mask++) {
  if (dp[mask] < 0) continue;
  let i = 0;
  while (i < n && ((mask >> i) & 1)) i++;
  if (i === n) continue;
  for (let j = i + 1; j < n; j++) {
    if ((mask >> j) & 1) continue;
    const nm = mask | (1 << i) | (1 << j);
    if (dp[mask] + c[i][j] > dp[nm]) dp[nm] = dp[mask] + c[i][j];
  }
}
console.log(dp[FULL - 1]);
CODE,
            'python' => <<<'CODE'
k = int(input())
n = 2 * k
c = [list(map(int, input().split())) for _ in range(n)]
FULL = 1 << n
dp = [-1] * FULL
dp[0] = 0
for mask in range(FULL):
    cur = dp[mask]
    if cur < 0:
        continue
    i = 0
    while i < n and (mask >> i) & 1:
        i += 1
    if i == n:
        continue
    ci = c[i]
    base = mask | (1 << i)
    for j in range(i + 1, n):
        if not (mask >> j) & 1:
            nm = base | (1 << j)
            t = cur + ci[j]
            if t > dp[nm]:
                dp[nm] = t
print(dp[FULL - 1])
CODE,
        ],
        'editorial' => '<p><code>dp[mask]</code> = total kecocokan terbaik jika siswa-siswa di mask sudah dipasangkan. Untuk melanjutkan, ambil <strong>siswa bernomor terkecil</strong> i yang belum berpasangan, lalu coba pasangkan dengan setiap j lain yang belum berpasangan.</p>
<p>Mengapa selalu siswa terkecil? Siswa itu toh harus mendapat pasangan, dan memilih urutan baku seperti ini membuat setiap pembagian dihasilkan tepat sekali, sehingga transisinya hanya O(N) per mask, bukan O(N²).</p>
<p><strong>Kompleksitas:</strong> O(2<sup>2K</sup> · 2K) ≈ 10<sup>6</sup>.</p>',
        'hints' => [
            'Ada paling banyak 16 siswa. Simpan himpunan siswa yang sudah berpasangan sebagai bitmask.',
            'Dari sebuah mask, siswa bernomor terkecil yang belum berpasangan harus dipasangkan sekarang.',
            'Coba semua pasangan j untuknya: dp[mask | i | j] = max(…, dp[mask] + c[i][j]).',
        ],
    ],

    // ═════════════════════════════ DP pada pohon ═════════════════════════════
    [
        'slug' => 'penjaga-taman',
        'lesson' => 'dp-pohon',
        'title' => 'Penjaga Taman',
        'difficulty' => 'Sedang',
        'tags' => ['dp pohon', 'vertex cover'],
        'statement' => '<p>Taman kota punya <strong>N</strong> pos yang dihubungkan N − 1 jalan setapak berbentuk pohon. Pengelola ingin menempatkan penjaga di beberapa pos sehingga <strong>setiap jalan setapak</strong> punya penjaga di paling sedikit salah satu ujungnya. Menempatkan penjaga di pos i berbiaya <code>b<sub>i</sub></code>.</p>
<p>Berapa total biaya minimum?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi b<sub>1</sub> … b<sub>N</sub>. N − 1 baris berikutnya berisi <code>u v</code>.</p>',
        'output_format' => '<p>Biaya minimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ b<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5\n3 1 4 1 5\n1 2\n1 3\n3 4\n3 5\n", 'explanation' => 'Pilih pos 2 dan 3 (biaya 1 + 4 = 5): jalan 1–2 dijaga pos 2, sedangkan 1–3, 3–4, dan 3–5 dijaga pos 3. Kalau pos 3 tidak dipilih, pos 1, 4, dan 5 semuanya wajib dipilih (biaya 3 + 1 + 5 = 9), jadi 5 adalah yang termurah.'],
        ],
        'tests' => function () use ($treeWithValues) {
            return ["1\n7\n", "2\n5 3\n1 2\n", $treeWithValues(8, [1, 10]), $treeWithValues(1000, [1, 1000]), $treeWithValues(200000, [1, 1000000000]), $treeWithValues(200000, [1, 5], 'garis'), $treeWithValues(200000, [1, 1000000000], 'bintang')];
        },
        'solve' => function (string $input) use ($bfsOrder) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $b = array_merge([0], T::ints($lines[1]));
            $adj = array_fill(0, $n + 1, []);
            for ($i = 2; $i <= $n; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            [$order, $par] = $bfsOrder($n, $adj);
            $d0 = array_fill(0, $n + 1, 0);
            $d1 = $b;
            for ($i = $n - 1; $i >= 1; $i--) {
                $v = $order[$i];
                $p = $par[$v];
                $d0[$p] += $d1[$v];
                $d1[$p] += min($d0[$v], $d1[$v]);
            }

            return (string) min($d0[1], $d1[1]);
        },
        'starter' => $st($cppTreeVal),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> biaya(n + 1);
    for (int i = 1; i <= n; i++) cin >> biaya[i];
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }

    // Urutan BFS dari akar 1 (orang tua sebelum anak)
    vector<int> par(n + 1, 0), urutan = {1};
    par[1] = -1;
    for (int h = 0; h < (int)urutan.size(); h++) {
        int u = urutan[h];
        for (int v : adj[u])
            if (v != par[u]) {
                par[v] = u;
                urutan.push_back(v);
            }
    }

    // d0[u] = biaya minimum subtree u jika u TIDAK dijaga
    // d1[u] = biaya minimum subtree u jika u DIJAGA
    vector<long long> d0(n + 1, 0), d1(biaya);
    for (int i = n - 1; i >= 1; i--) {      // dari bawah ke atas
        int v = urutan[i], p = par[v];
        d0[p] += d1[v];                     // p tidak dijaga → jalan p–v wajib dijaga v
        d1[p] += min(d0[v], d1[v]);         // p dijaga → v bebas
    }
    cout << min(d0[1], d1[1]) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const b = [0, ...readInts()];
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const par = new Int32Array(n + 1), urutan = [1];
par[1] = -1;
for (let h = 0; h < urutan.length; h++) {
  const u = urutan[h];
  for (const v of adj[u]) if (v !== par[u]) { par[v] = u; urutan.push(v); }
}
// Total bisa mencapai 2·10^14: aman untuk Float64Array
const d0 = new Float64Array(n + 1), d1 = Float64Array.from(b);
for (let i = n - 1; i >= 1; i--) {
  const v = urutan[i], p = par[v];
  d0[p] += d1[v];
  d1[p] += Math.min(d0[v], d1[v]);
}
console.log(Math.min(d0[1], d1[1]));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
b = [0] + list(map(int, input().split()))
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

par = [0] * (n + 1)
par[1] = -1
urutan = [1]
for u in urutan:
    for v in adj[u]:
        if v != par[u]:
            par[v] = u
            urutan.append(v)

d0 = [0] * (n + 1)
d1 = b[:]
for v in reversed(urutan[1:]):
    p = par[v]
    d0[p] += d1[v]
    d1[p] += min(d0[v], d1[v])
print(min(d0[1], d1[1]))
CODE,
        ],
        'editorial' => '<p>Ini <strong>minimum vertex cover berbobot</strong> pada pohon. Untuk setiap pos simpan dua jawaban:</p>
<ul><li><code>d0[u]</code>: u tidak dijaga. Semua jalan u–anak harus dijaga oleh anaknya, jadi <code>d0[u] = Σ d1[v]</code>.</li>
<li><code>d1[u]</code>: u dijaga. Anak bebas: <code>d1[u] = b[u] + Σ min(d0[v], d1[v])</code>.</li></ul>
<p>Hitung dari daun ke akar (urutan BFS terbalik), jawabannya <code>min(d0[1], d1[1])</code>. Total bisa mencapai 2 · 10<sup>14</sup>: <code>long long</code>.</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
        'hints' => [
            'Untuk setiap pos ada dua keadaan: dijaga atau tidak. Apa akibatnya bagi anak-anaknya?',
            'Jika u tidak dijaga, setiap anak WAJIB dijaga. Jika u dijaga, anak bebas memilih yang termurah.',
            'd0[u] = Σ d1[v]; d1[u] = b[u] + Σ min(d0[v], d1[v]). Hitung dari daun ke akar.',
        ],
    ],

    [
        'slug' => 'pasangan-pohon',
        'lesson' => 'dp-pohon',
        'title' => 'Pasangan Bertetangga',
        'difficulty' => 'Sulit',
        'tags' => ['dp pohon', 'matching'],
        'statement' => '<p>Sebuah komunitas punya <strong>N</strong> anggota dengan hubungan pertemanan berbentuk pohon (N − 1 pasangan teman). Untuk kegiatan berpasangan, setiap pasangan harus terdiri dari <strong>dua teman langsung</strong>, dan setiap anggota paling banyak masuk satu pasangan.</p>
<p>Berapa banyak pasangan terbanyak yang bisa dibentuk?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N − 1 baris berikutnya berisi <code>u v</code>.</p>',
        'output_format' => '<p>Banyak pasangan maksimum.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "5\n1 2\n1 3\n3 4\n3 5\n", 'explanation' => 'Pasangan (1, 2) dan (3, 4). Anggota 5 tidak kebagian.'],
        ],
        'tests' => function () use ($treeWithValues) {
            return ["1\n", "2\n1 2\n", $treeWithValues(8, null), $treeWithValues(1000, null), $treeWithValues(200000, null), $treeWithValues(200000, null, 'garis'), $treeWithValues(200000, null, 'bintang')];
        },
        'solve' => function (string $input) use ($bfsOrder) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i < $n; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            [$order, $par] = $bfsOrder($n, $adj);
            $free = array_fill(0, $n + 1, true);
            $cnt = 0;
            for ($i = $n - 1; $i >= 1; $i--) {
                $v = $order[$i];
                $p = $par[$v];
                if ($free[$v] && $free[$p]) {
                    $free[$v] = $free[$p] = false;
                    $cnt++;
                }
            }

            return (string) $cnt;
        },
        'starter' => $st($cppTree),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
    vector<int> par(n + 1, 0), urutan = {1};
    par[1] = -1;
    for (int h = 0; h < (int)urutan.size(); h++) {
        int u = urutan[h];
        for (int v : adj[u])
            if (v != par[u]) {
                par[v] = u;
                urutan.push_back(v);
            }
    }

    // DP pohon: dp0[u] = pasangan terbanyak di subtree u jika u TIDAK dipasangkan dengan anaknya,
    //           dp1[u] = pasangan terbanyak di subtree u (u boleh dipasangkan dengan salah satu anak)
    vector<long long> dp0(n + 1, 0), dp1(n + 1, 0);
    for (int i = n - 1; i >= 0; i--) {
        int u = urutan[i];
        for (int v : adj[u])
            if (v != par[u]) dp0[u] += dp1[v];
        dp1[u] = dp0[u];
        // pasangkan u dengan anak v: v tidak boleh dipasangkan dengan anaknya sendiri
        for (int v : adj[u])
            if (v != par[u]) dp1[u] = max(dp1[u], dp0[u] - dp1[v] + dp0[v] + 1);
    }
    cout << dp1[1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const par = new Int32Array(n + 1), urutan = [1];
par[1] = -1;
for (let h = 0; h < urutan.length; h++) {
  const u = urutan[h];
  for (const v of adj[u]) if (v !== par[u]) { par[v] = u; urutan.push(v); }
}
// Serakah dari daun ke atas: pasangkan simpul dengan orang tuanya jika keduanya masih bebas
const bebas = new Uint8Array(n + 1).fill(1);
let pasangan = 0;
for (let i = n - 1; i >= 1; i--) {
  const v = urutan[i], p = par[v];
  if (bebas[v] && bebas[p]) { bebas[v] = bebas[p] = 0; pasangan++; }
}
console.log(pasangan);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

par = [0] * (n + 1)
par[1] = -1
urutan = [1]
for u in urutan:
    for v in adj[u]:
        if v != par[u]:
            par[v] = u
            urutan.append(v)

dp0 = [0] * (n + 1)
dp1 = [0] * (n + 1)
for u in reversed(urutan):
    anak = [v for v in adj[u] if v != par[u]]
    s = sum(dp1[v] for v in anak)
    dp0[u] = s
    dp1[u] = max([s] + [s - dp1[v] + dp0[v] + 1 for v in anak])
print(dp1[1])
CODE,
        ],
        'editorial' => '<p>Ini <strong>maximum matching</strong> pada pohon. Dengan DP dua keadaan:</p>
<ul><li><code>dp0[u]</code>: u tidak dipasangkan dengan anaknya. Setiap anak bebas: <code>Σ dp1[v]</code>.</li>
<li><code>dp1[u]</code>: terbaik di subtree u. Selain <code>dp0[u]</code>, coba pasangkan u dengan satu anak v: anak itu tidak boleh dipasangkan ke bawah, jadi <code>dp0[u] − dp1[v] + dp0[v] + 1</code>.</li></ul>
<p><strong>Solusi serakah</strong> juga benar: proses dari daun ke atas, pasangkan simpul dengan orang tuanya jika keduanya masih bebas. Daun sebaiknya selalu dipasangkan dengan orang tuanya, karena orang tua itu hanya bisa "dikorbankan" untuk satu pasangan. (Kode C++ dan Python memakai DP, JavaScript memakai serakah.)</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
        'hints' => [
            'Untuk setiap simpul, penting diketahui apakah ia sudah dipakai berpasangan dengan salah satu anaknya.',
            'dp0[u] = Σ dp1[anak]; untuk dp1, coba pasangkan u dengan satu anak v yang tidak berpasangan ke bawah.',
            'Alternatif serakah: dari daun ke atas, pasangkan simpul dengan orang tuanya jika keduanya masih bebas.',
        ],
        'sample_visual' => 'tree',
    ],

    [
        'slug' => 'kelompok-tenang',
        'lesson' => 'dp-pohon',
        'title' => 'Kelompok yang Tenang',
        'difficulty' => 'Sedang',
        'tags' => ['dp pohon', 'menghitung cara', 'modulo'],
        'statement' => '<p>Struktur organisasi berbentuk pohon dengan <strong>N</strong> anggota. Untuk rapat kecil, dipilih sebuah kelompok (boleh kosong) sehingga <strong>tidak ada dua anggota kelompok yang terhubung langsung</strong> (atasan–bawahan langsung).</p>
<p>Ada berapa kelompok berbeda yang bisa dipilih? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N − 1 baris berikutnya berisi <code>u v</code>.</p>',
        'output_format' => '<p>Banyak kelompok modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li></ul>',
        'samples' => [
            ['input' => "3\n1 2\n1 3\n", 'explanation' => '{}, {1}, {2}, {3}, {2, 3}: ada 5 kelompok.'],
        ],
        'tests' => function () use ($treeWithValues) {
            return ["1\n", "2\n1 2\n", $treeWithValues(8, null), $treeWithValues(1000, null), $treeWithValues(200000, null), $treeWithValues(200000, null, 'garis'), $treeWithValues(200000, null, 'bintang')];
        },
        'solve' => function (string $input) use ($bfsOrder, $MOD) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $adj = array_fill(0, $n + 1, []);
            for ($i = 1; $i < $n; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $adj[$u][] = $v;
                $adj[$v][] = $u;
            }
            [$order, $par] = $bfsOrder($n, $adj);
            $c0 = array_fill(0, $n + 1, 1);
            $c1 = array_fill(0, $n + 1, 1);
            for ($i = $n - 1; $i >= 1; $i--) {
                $v = $order[$i];
                $p = $par[$v];
                $c0[$p] = $c0[$p] * (($c0[$v] + $c1[$v]) % $MOD) % $MOD;
                $c1[$p] = $c1[$p] * $c0[$v] % $MOD;
            }

            return (string) (($c0[1] + $c1[1]) % $MOD);
        },
        'starter' => $st($cppTree),
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
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < n - 1; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }
    vector<int> par(n + 1, 0), urutan = {1};
    par[1] = -1;
    for (int h = 0; h < (int)urutan.size(); h++) {
        int u = urutan[h];
        for (int v : adj[u])
            if (v != par[u]) {
                par[v] = u;
                urutan.push_back(v);
            }
    }

    // c0[u] = banyak kelompok di subtree u yang TIDAK memuat u
    // c1[u] = banyak kelompok di subtree u yang MEMUAT u
    vector<long long> c0(n + 1, 1), c1(n + 1, 1);
    for (int i = n - 1; i >= 1; i--) {
        int v = urutan[i], p = par[v];
        // Anak-anak saling bebas → banyak cara DIKALIKAN (aturan perkalian)
        c0[p] = c0[p] * ((c0[v] + c1[v]) % MOD) % MOD;  // p tidak dipilih: anak bebas
        c1[p] = c1[p] * c0[v] % MOD;                    // p dipilih: anak tidak boleh
    }
    cout << (c0[1] + c1[1]) % MOD << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const adj = Array.from({ length: n + 1 }, () => []);
for (let i = 0; i < n - 1; i++) {
  const [u, v] = readInts();
  adj[u].push(v);
  adj[v].push(u);
}
const par = new Int32Array(n + 1), urutan = [1];
par[1] = -1;
for (let h = 0; h < urutan.length; h++) {
  const u = urutan[h];
  for (const v of adj[u]) if (v !== par[u]) { par[v] = u; urutan.push(v); }
}
// Perkalian modulo dengan BigInt agar tidak kehilangan presisi
const MOD = 1000000007n;
const c0 = new Array(n + 1).fill(1n), c1 = new Array(n + 1).fill(1n);
for (let i = n - 1; i >= 1; i--) {
  const v = urutan[i], p = par[v];
  c0[p] = (c0[p] * ((c0[v] + c1[v]) % MOD)) % MOD;
  c1[p] = (c1[p] * c0[v]) % MOD;
}
console.log(((c0[1] + c1[1]) % MOD).toString());
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
adj = [[] for _ in range(n + 1)]
for _ in range(n - 1):
    u, v = map(int, input().split())
    adj[u].append(v)
    adj[v].append(u)

par = [0] * (n + 1)
par[1] = -1
urutan = [1]
for u in urutan:
    for v in adj[u]:
        if v != par[u]:
            par[v] = u
            urutan.append(v)

MOD = 10**9 + 7
c0 = [1] * (n + 1)
c1 = [1] * (n + 1)
for v in reversed(urutan[1:]):
    p = par[v]
    c0[p] = c0[p] * (c0[v] + c1[v]) % MOD
    c1[p] = c1[p] * c0[v] % MOD
print((c0[1] + c1[1]) % MOD)
CODE,
        ],
        'editorial' => '<p>Ini menghitung <strong>himpunan independen</strong> pada pohon. Untuk setiap simpul u simpan:</p>
<ul><li><code>c0[u]</code>: banyak kelompok di subtree u yang tidak memuat u. Setiap anak boleh dipilih atau tidak: kalikan <code>(c0[v] + c1[v])</code>.</li>
<li><code>c1[u]</code>: banyak kelompok yang memuat u. Setiap anak tidak boleh dipilih: kalikan <code>c0[v]</code>.</li></ul>
<p>Subtree anak-anak tidak saling memengaruhi, jadi pilihan di masing-masing anak <strong>dikalikan</strong> (aturan perkalian), berbeda dengan "Undangan Pesta" yang menjumlahkan nilai. Jawaban <code>c0[akar] + c1[akar]</code>, termasuk kelompok kosong.</p>
<p><strong>Kompleksitas:</strong> O(N).</p>',
        'hints' => [
            'Sama seperti soal undangan pesta, tetapi yang dihitung banyak cara.',
            'Dua keadaan per simpul: dipilih atau tidak. Bagaimana menggabungkan anak-anak yang saling bebas?',
            'c0[u] = Π (c0[v] + c1[v]), c1[u] = Π c0[v]. Jawaban c0[1] + c1[1].',
        ],
    ],

    // ═════════════════════════════ Digit DP ═════════════════════════════
    [
        'slug' => 'nomor-tanpa-empat',
        'lesson' => 'digit-dp',
        'title' => 'Nomor Rumah Tanpa Angka 4',
        'difficulty' => 'Mudah',
        'tags' => ['digit dp'],
        'statement' => '<p>Di beberapa negara, angka 4 dianggap kurang beruntung. Sebuah kompleks perumahan ingin memakai nomor rumah 1, 2, 3, … tetapi <strong>melewati</strong> semua nomor yang mengandung digit 4 (misalnya 4, 14, 40, 143).</p>
<p>Berapa banyak nomor dari 1 sampai <strong>N</strong> yang <strong>tidak</strong> mengandung digit 4?</p>',
        'input_format' => '<p>Satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Banyak bilangan di [1, N] tanpa digit 4.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "20\n", 'explanation' => 'Yang dilewati: 4 dan 14. Sisanya 18 nomor.'],
            ['input' => "100\n", 'explanation' => null],
        ],
        'tests' => function () use ($bigRand) {
            return ["1\n", "4\n", "44\n", "399\n", "1000\n", (string) $bigRand(9)."\n", (string) $bigRand(15)."\n", (string) $bigRand(18)."\n", "1000000000000000000\n", "444444444444444444\n"];
        },
        'solve' => function (string $input) {
            $N = trim($input);
            $L = strlen($N);
            $pow9 = [1];
            for ($i = 1; $i <= $L; $i++) {
                $pow9[$i] = $pow9[$i - 1] * 9;
            }
            $ans = 0;
            $ok = true;
            for ($i = 0; $i < $L; $i++) {
                $d = (int) $N[$i];
                for ($x = 0; $x < $d; $x++) {
                    if ($x !== 4) {
                        $ans += $pow9[$L - 1 - $i];
                    }
                }
                if ($d === 4) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                $ans++;
            }

            return (string) ($ans - 1);
        },
        'starter' => $st("    long long N;\n    cin >> N;\n    string D = to_string(N);   // digit-digit N"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

string D;
long long memo[20][2];
bool ada[20][2];

// Banyak cara mengisi digit ke-pos..akhir tanpa digit 4, dengan flag ketat
long long go(int pos, bool tight) {
    if (pos == (int)D.size()) return 1;
    if (ada[pos][tight]) return memo[pos][tight];
    int batas = tight ? D[pos] - '0' : 9;
    long long res = 0;
    for (int d = 0; d <= batas; d++) {
        if (d == 4) continue;                      // digit terlarang
        res += go(pos + 1, tight && d == batas);
    }
    ada[pos][tight] = true;
    return memo[pos][tight] = res;
}

int main() {
    long long N;
    cin >> N;
    D = to_string(N);
    // go menghitung bilangan 0..N (dengan nol di depan); kurangi 1 untuk bilangan 0
    cout << go(0, true) - 1 << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// N sampai 10^18 melebihi presisi Number: baca sebagai string, hitung dengan BigInt
const N = readLine().trim();
const L = N.length;
const pow9 = [1n];
for (let i = 1; i <= L; i++) pow9.push(pow9[i - 1] * 9n);
let ans = 0n, ok = true;
for (let i = 0; i < L; i++) {
  const d = Number(N[i]);
  for (let x = 0; x < d; x++) if (x !== 4) ans += pow9[L - 1 - i];
  if (d === 4) { ok = false; break; }
}
if (ok) ans += 1n;
console.log((ans - 1n).toString());
CODE,
            'python' => <<<'CODE'
from functools import lru_cache

D = input().strip()

@lru_cache(maxsize=None)
def go(pos, tight):
    if pos == len(D):
        return 1
    batas = int(D[pos]) if tight else 9
    return sum(go(pos + 1, tight and d == batas) for d in range(batas + 1) if d != 4)

print(go(0, True) - 1)
CODE,
        ],
        'editorial' => '<p>N sampai 10<sup>18</sup>, jadi tidak bisa diperiksa satu per satu. Susun bilangan <strong>digit demi digit</strong> dengan flag <em>ketat</em>: selama awalan sama dengan N, digit berikutnya paling besar digit N; begitu memilih digit yang lebih kecil, sisa digit bebas.</p>
<p>Untuk setiap posisi, coba semua digit kecuali 4. Hitungan yang didapat mencakup 0..N (bilangan pendek ditulis dengan nol di depan, dan 0 tidak mengandung 4), jadi kurangi 1 untuk mengeluarkan bilangan 0.</p>
<p>Versi tanpa rekursi: jika sudah bebas dengan r digit tersisa, banyak caranya 9<sup>r</sup>.</p>
<p><strong>Kompleksitas:</strong> O(19 · 10).</p>',
        'hints' => [
            'N terlalu besar untuk dicek satu per satu. Susun bilangan digit demi digit.',
            'Gunakan flag ketat: apakah awalan masih sama dengan awalan N?',
            'Lewati digit 4 di setiap posisi. Hasil menghitung 0..N, jadi kurangi 1.',
        ],
    ],

    [
        'slug' => 'digit-habis-dibagi',
        'lesson' => 'digit-dp',
        'title' => 'Jumlah Digit Habis Dibagi K',
        'difficulty' => 'Sedang',
        'tags' => ['digit dp', 'modulo'],
        'statement' => '<p>Berapa banyak bilangan bulat x di rentang <strong>[A, B]</strong> yang <strong>jumlah digitnya habis dibagi K</strong>? Contohnya untuk K = 5, bilangan 23 (2 + 3 = 5) dan 37 (3 + 7 = 10) dihitung. Bilangan 0 punya jumlah digit 0, yang habis dibagi K.</p>',
        'input_format' => '<p>Satu baris berisi <code>A B K</code>.</p>',
        'output_format' => '<p>Banyak bilangan yang memenuhi.</p>',
        'constraints' => '<ul><li>0 ≤ A ≤ B ≤ 10<sup>18</sup></li><li>1 ≤ K ≤ 100</li></ul>',
        'samples' => [
            ['input' => "1 30 5\n", 'explanation' => '5, 14, 19, 23, 28: ada 5 bilangan.'],
            ['input' => "0 0 7\n", 'explanation' => 'Bilangan 0 berjumlah digit 0.'],
        ],
        'tests' => function () use ($bigRand) {
            $r = function () use ($bigRand) {
                $a = $bigRand(mt_rand(1, 18));
                $b = $bigRand(mt_rand(1, 18));

                return min($a, $b).' '.max($a, $b).' '.mt_rand(1, 100)."\n";
            };

            return ["0 100 1\n", "10 99 9\n", "1 1000 7\n", "0 1000000000000000000 100\n", "999999999999999999 1000000000000000000 1\n", "123456789 987654321 13\n", $r(), $r(), $r(), $r()];
        },
        'solve' => function (string $input) {
            [$A, $B, $K] = array_map('intval', preg_split('/\s+/', trim($input)));
            // g[r][m] = banyak string r digit (0-9) yang jumlah digitnya ≡ m (mod K)
            $g = [array_fill(0, $K, 0)];
            $g[0][0] = 1;
            for ($r = 1; $r <= 19; $r++) {
                $row = array_fill(0, $K, 0);
                for ($m = 0; $m < $K; $m++) {
                    if (! $g[$r - 1][$m]) {
                        continue;
                    }
                    for ($d = 0; $d <= 9; $d++) {
                        $row[($m + $d) % $K] += $g[$r - 1][$m];
                    }
                }
                $g[] = $row;
            }
            $F = function (int $X) use ($g, $K) {
                if ($X < 0) {
                    return 0;
                }
                $D = (string) $X;
                $L = strlen($D);
                $ans = 0;
                $p = 0;
                for ($i = 0; $i < $L; $i++) {
                    $dig = (int) $D[$i];
                    for ($d = 0; $d < $dig; $d++) {
                        $need = ((-($p + $d)) % $K + $K) % $K;
                        $ans += $g[$L - 1 - $i][$need];
                    }
                    $p += $dig;
                }
                if ($p % $K === 0) {
                    $ans++;
                }

                return $ans;
            };

            return (string) ($F($B) - $F($A - 1));
        },
        'starter' => $st("    long long A, B;\n    int K;\n    cin >> A >> B >> K;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int K;
string D;
long long memo[20][100];
bool ada[20][100];

// pos = posisi digit, sisa = (jumlah digit sejauh ini) mod K
long long go(int pos, int sisa, bool tight) {
    if (pos == (int)D.size()) return sisa == 0;
    if (!tight && ada[pos][sisa]) return memo[pos][sisa];
    int batas = tight ? D[pos] - '0' : 9;
    long long res = 0;
    for (int d = 0; d <= batas; d++)
        res += go(pos + 1, (sisa + d) % K, tight && d == batas);
    if (!tight) {
        ada[pos][sisa] = true;
        memo[pos][sisa] = res;
    }
    return res;
}

// banyak x di [0, X] yang jumlah digitnya habis dibagi K
long long F(long long X) {
    if (X < 0) return 0;
    D = to_string(X);
    memset(ada, 0, sizeof ada);
    return go(0, 0, true);
}

int main() {
    long long A, B;
    cin >> A >> B >> K;
    cout << F(B) - F(A - 1) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// A, B sampai 10^18: baca sebagai string, hitung dengan BigInt
const [As, Bs, Ks] = readLine().trim().split(/\s+/);
const K = Number(Ks);
// g[r][m] = banyak string r digit yang jumlah digitnya ≡ m (mod K)
const g = [new Array(K).fill(0n)];
g[0][0] = 1n;
for (let r = 1; r <= 19; r++) {
  const row = new Array(K).fill(0n);
  for (let m = 0; m < K; m++) {
    if (g[r - 1][m] === 0n) continue;
    for (let d = 0; d <= 9; d++) row[(m + d) % K] += g[r - 1][m];
  }
  g.push(row);
}
function F(X) {           // X berupa BigInt
  if (X < 0n) return 0n;
  const D = X.toString(), L = D.length;
  let ans = 0n, p = 0;
  for (let i = 0; i < L; i++) {
    const dig = Number(D[i]);
    for (let d = 0; d < dig; d++) ans += g[L - 1 - i][(((-(p + d)) % K) + K) % K];
    p += dig;
  }
  if (p % K === 0) ans += 1n;
  return ans;
}
console.log((F(BigInt(Bs)) - F(BigInt(As) - 1n)).toString());
CODE,
            'python' => <<<'CODE'
from functools import lru_cache

A, B, K = map(int, input().split())

def F(X):
    if X < 0:
        return 0
    D = str(X)

    @lru_cache(maxsize=None)
    def go(pos, sisa, tight):
        if pos == len(D):
            return 1 if sisa == 0 else 0
        batas = int(D[pos]) if tight else 9
        return sum(go(pos + 1, (sisa + d) % K, tight and d == batas) for d in range(batas + 1))

    return go(0, 0, True)

print(F(B) - F(A - 1))
CODE,
        ],
        'editorial' => '<p>Gunakan trik rentang: jawaban = F(B) − F(A − 1), dengan F(X) = banyak bilangan di [0, X] yang memenuhi.</p>
<p>Untuk F(X), susun digit demi digit. Informasi yang perlu diingat bukan jumlah digit seluruhnya, cukup <strong>sisa baginya terhadap K</strong> (paling banyak 100 nilai). State: <code>(posisi, sisa, ketat)</code>. Di akhir, bilangan dihitung jika sisanya 0.</p>
<p>Memo hanya untuk keadaan tidak ketat, dan direset untuk setiap X. Hasil bisa mendekati 10<sup>18</sup>: <code>long long</code> (BigInt di JavaScript).</p>
<p><strong>Kompleksitas:</strong> O(19 · K · 10) per F.</p>',
        'hints' => [
            'Hitung F(X) = banyak bilangan di [0, X], lalu jawaban F(B) − F(A − 1).',
            'Tidak perlu menyimpan jumlah digit lengkap; sisa baginya terhadap K sudah cukup.',
            'State (posisi, sisa, ketat). Memo hanya keadaan tidak ketat.',
        ],
    ],

    [
        'slug' => 'tanpa-kembar',
        'lesson' => 'digit-dp',
        'title' => 'Bilangan Tanpa Digit Kembar Bersebelahan',
        'difficulty' => 'Sulit',
        'tags' => ['digit dp', 'nol di depan'],
        'statement' => '<p>Sebuah bilangan disebut <strong>rapi</strong> jika tidak ada dua digit <strong>bersebelahan</strong> yang sama. Contoh: 1213 dan 909 rapi, tetapi 1223 dan 100 tidak. Semua bilangan satu digit (termasuk 0) rapi.</p>
<p>Berapa banyak bilangan rapi di rentang <strong>[A, B]</strong>?</p>',
        'input_format' => '<p>Satu baris berisi <code>A B</code>.</p>',
        'output_format' => '<p>Banyak bilangan rapi di [A, B].</p>',
        'constraints' => '<ul><li>0 ≤ A ≤ B ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "123 321\n", 'explanation' => null],
            ['input' => "0 20\n", 'explanation' => 'Semua kecuali 11: ada 20 bilangan.'],
        ],
        'tests' => function () use ($bigRand) {
            $r = function () use ($bigRand) {
                $a = $bigRand(mt_rand(1, 18));
                $b = $bigRand(mt_rand(1, 18));

                return min($a, $b).' '.max($a, $b)."\n";
            };

            return ["0 0\n", "0 9\n", "10 100\n", "100 100\n", "0 1000000000000000000\n", "1 999999999999999999\n", "1122334455 9988776655\n", $r(), $r(), $r(), $r()];
        },
        'solve' => function (string $input) {
            [$A, $B] = array_map('intval', preg_split('/\s+/', trim($input)));
            $F = function (int $X) {
                if ($X < 0) {
                    return 0;
                }
                $D = (string) $X;
                $L = strlen($D);
                $p9 = [1];
                for ($i = 1; $i <= $L; $i++) {
                    $p9[$i] = $p9[$i - 1] * 9;
                }
                $ans = 1; // bilangan 0
                for ($len = 1; $len < $L; $len++) {
                    $ans += 9 * $p9[$len - 1];
                }
                if ($L === 1) {
                    return (int) $D + 1;
                }
                $ok = true;
                for ($i = 0; $i < $L; $i++) {
                    $dig = (int) $D[$i];
                    $from = $i === 0 ? 1 : 0;
                    for ($d = $from; $d < $dig; $d++) {
                        if ($i > 0 && $d === (int) $D[$i - 1]) {
                            continue;
                        }
                        $ans += $p9[$L - 1 - $i];
                    }
                    if ($i > 0 && $dig === (int) $D[$i - 1]) {
                        $ok = false;
                        break;
                    }
                }
                if ($ok) {
                    $ans++;
                }

                return $ans;
            };

            return (string) ($F($B) - $F($A - 1));
        },
        'starter' => $st("    long long A, B;\n    cin >> A >> B;"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

string D;
long long memo[20][11][2];
bool ada[20][11][2];

// prev = digit sebelumnya (10 = belum ada), mulai = sudah ada digit bukan nol?
long long go(int pos, int prev, bool tight, bool mulai) {
    if (pos == (int)D.size()) return 1;
    if (!tight && ada[pos][prev][mulai]) return memo[pos][prev][mulai];
    int batas = tight ? D[pos] - '0' : 9;
    long long res = 0;
    for (int d = 0; d <= batas; d++) {
        if (mulai && d == prev) continue;               // dua digit kembar bersebelahan
        bool mulaiBaru = mulai || d != 0;               // nol di depan tidak dihitung digit
        res += go(pos + 1, mulaiBaru ? d : 10, tight && d == batas, mulaiBaru);
    }
    if (!tight) {
        ada[pos][prev][mulai] = true;
        memo[pos][prev][mulai] = res;
    }
    return res;
}

long long F(long long X) {          // banyak bilangan rapi di [0, X]
    if (X < 0) return 0;
    D = to_string(X);
    memset(ada, 0, sizeof ada);
    return go(0, 10, true, false);
}

int main() {
    long long A, B;
    cin >> A >> B;
    cout << F(B) - F(A - 1) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// Bilangan sampai 10^18: pakai BigInt
const [As, Bs] = readLine().trim().split(/\s+/);
function F(X) {
  if (X < 0n) return 0n;
  const D = X.toString(), L = D.length;
  if (L === 1) return X + 1n;
  const p9 = [1n];
  for (let i = 1; i <= L; i++) p9.push(p9[i - 1] * 9n);
  let ans = 1n;                                   // bilangan 0
  for (let len = 1; len < L; len++) ans += 9n * p9[len - 1];   // semua bilangan rapi yang lebih pendek
  let ok = true;
  for (let i = 0; i < L; i++) {
    const dig = Number(D[i]);
    for (let d = i === 0 ? 1 : 0; d < dig; d++) {
      if (i > 0 && d === Number(D[i - 1])) continue;
      ans += p9[L - 1 - i];
    }
    if (i > 0 && dig === Number(D[i - 1])) { ok = false; break; }
  }
  if (ok) ans += 1n;
  return ans;
}
console.log((F(BigInt(Bs)) - F(BigInt(As) - 1n)).toString());
CODE,
            'python' => <<<'CODE'
from functools import lru_cache

A, B = map(int, input().split())

def F(X):
    if X < 0:
        return 0
    D = str(X)

    @lru_cache(maxsize=None)
    def go(pos, prev, tight, mulai):
        if pos == len(D):
            return 1
        batas = int(D[pos]) if tight else 9
        res = 0
        for d in range(batas + 1):
            if mulai and d == prev:
                continue
            mb = mulai or d != 0
            res += go(pos + 1, d if mb else 10, tight and d == batas, mb)
        return res

    return go(0, 10, True, False)

print(F(B) - F(A - 1))
CODE,
        ],
        'editorial' => '<p>Jawaban = F(B) − F(A − 1). Untuk F(X), susun digit demi digit dengan state <code>(posisi, digit sebelumnya, ketat, sudah mulai)</code>.</p>
<p>Flag <strong>sudah mulai</strong> penting karena nol di depan bukan digit sungguhan: bilangan 7 ditulis 007, dan "00" di depannya tidak boleh dianggap dua digit kembar. Selama belum mulai, digit sebelumnya ditandai "tidak ada" (misalnya 10).</p>
<p>Alternatif tanpa rekursi: bilangan rapi dengan panjang ℓ ada 9 · 9<sup>ℓ−1</sup> (digit pertama 1–9, setiap digit berikutnya 9 pilihan), lalu telusuri digit X seperti biasa.</p>
<p><strong>Kompleksitas:</strong> O(19 · 11 · 2 · 10) per F.</p>',
        'hints' => [
            'Jawaban = F(B) − F(A − 1), dengan F(X) menghitung [0, X].',
            'Yang perlu diingat: digit sebelumnya. Tetapi hati-hati dengan nol di depan (007).',
            'Tambahkan flag "sudah mulai": sebelum ada digit bukan nol, digit sebelumnya dianggap tidak ada.',
        ],
    ],
];
