<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi DP Peluang & Nilai Harapan.
 * Semua jawaban pecahan P/Q dicetak sebagai P · Q^(-1) mod 10^9 + 7.
 * JavaScript memakai perkalian modulo yang dipecah 16 bit agar tetap tepat dengan Number.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nconst long long MOD = 1e9 + 7;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini (cetak P · Q^(-1) mod MOD)\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : '')."const MOD = 1000000007;\n// a · b mod MOD tanpa melewati batas presisi Number (2^53)\nconst mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;\n\n// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\nMOD = 10**9 + 7\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini (pow(q, MOD - 2, MOD) memberi invers q)\n\n\nprint()\n",
    ];
};

$MOD = 1000000007;
$mul = fn (int $a, int $b): int => ($a * $b) % 1000000007;
$pw = function (int $a, int $e) use ($mul): int {
    $r = 1;
    $a %= 1000000007;
    while ($e > 0) {
        if ($e & 1) {
            $r = $mul($r, $a);
        }
        $a = $mul($a, $a);
        $e >>= 1;
    }

    return $r;
};

$jsMod = <<<'CODE'
const MOD = 1000000007;
// a · b mod MOD tanpa melewati batas presisi Number (2^53)
const mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {
  let h = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) h = mul(h, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return h;
};
CODE;

$cppPow = <<<'CODE'
const long long MOD = 1e9 + 7;

long long pangkat(long long a, long long e) {
    long long h = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) h = h * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return h;
}
CODE;

return [
    [
        'slug' => 'peluang-dadu',
        'lesson' => 'dp-peluang',
        'title' => 'Peluang Jumlah Dadu',
        'difficulty' => 'Sedang',
        'tags' => ['dp peluang', 'prefix sum', 'invers modular'],
        'statement' => '<p>Sebuah permainan papan memakai <strong>N</strong> dadu khusus. Setiap dadu punya <strong>M</strong> sisi bertuliskan 1, 2, …, M, dan setiap sisi sama mungkin muncul. Semua dadu dilempar bersamaan.</p>
<p>Berapa peluang jumlah angka yang muncul tepat <strong>S</strong>? Peluang itu dapat ditulis sebagai pecahan P/Q; cetak <code>P · Q<sup>−1</sup> mod 10<sup>9</sup> + 7</code>.</p>',
        'input_format' => '<p>Satu baris berisi <code>N M S</code>.</p>',
        'output_format' => '<p>Peluangnya dalam bentuk modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100</li><li>1 ≤ M ≤ 100</li><li>1 ≤ S ≤ 10 000</li></ul>',
        'samples' => [
            ['input' => "2 6 7\n", 'explanation' => 'Ada 6 dari 36 hasil yang berjumlah 7, peluangnya 1/6. Bilangan 166666668 memenuhi 6 · 166666668 ≡ 1 (mod 10<sup>9</sup> + 7).'],
            ['input' => "3 2 4\n", 'explanation' => 'Dadu bersisi 2. Hasil berjumlah 4: (1, 1, 2), (1, 2, 1), (2, 1, 1), yaitu 3 dari 8. Peluang 3/8.'],
        ],
        'tests' => function () {
            return ["1 6 3\n", "1 6 7\n", "10 6 35\n", "100 100 5050\n", "100 100 10000\n", "100 100 100\n", "50 7 200\n", "100 1 100\n", "100 1 99\n", "5 10 51\n", "37 53 999\n", "100 100 7777\n"];
        },
        'solve' => function (string $input) use ($MOD, $pw) {
            [$n, $m, $S] = T::ints(trim($input));
            if ($S > $n * $m) {
                return '0';
            }
            $cur = array_fill(0, $S + 1, 0);
            $cur[0] = 1;
            for ($k = 1; $k <= $n; $k++) {
                $pre = array_fill(0, $S + 2, 0);
                for ($s = 0; $s <= $S; $s++) {
                    $pre[$s + 1] = ($pre[$s] + $cur[$s]) % $MOD;
                }
                $nx = array_fill(0, $S + 1, 0);
                for ($s = 1; $s <= $S; $s++) {
                    $hi = $pre[$s];
                    $lo = $s - $m >= 0 ? $pre[$s - $m] : 0;
                    $nx[$s] = ($hi - $lo + $MOD) % $MOD;
                }
                $cur = $nx;
            }

            return (string) ($cur[$S] * $pw($pw($m, $n), $MOD - 2) % $MOD);
        },
        'starter' => $st("    int n, m, s;\n    cin >> n >> m >> s;", 'const [n, m, s] = readInts();', 'n, m, s = map(int, input().split())'),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppPow.<<<'CODE'


int main() {
    int n, m, S;
    cin >> n >> m >> S;
    if (S > n * m) {
        cout << 0 << '\n';
        return 0;
    }
    // cara[s] = banyak hasil k dadu yang jumlahnya s (modulo), digulir untuk k = 1..n
    vector<long long> cara(S + 1, 0), pre(S + 2);
    cara[0] = 1;
    for (int k = 1; k <= n; k++) {
        // cara_baru[s] = cara[s-1] + cara[s-2] + ... + cara[s-m]  -> pakai prefix sum
        pre[0] = 0;
        for (int s = 0; s <= S; s++) pre[s + 1] = (pre[s] + cara[s]) % MOD;
        vector<long long> baru(S + 1, 0);
        for (int s = 1; s <= S; s++) {
            long long kiri = (s - m >= 0) ? pre[s - m] : 0;
            baru[s] = (pre[s] - kiri + MOD) % MOD;
        }
        cara = baru;
    }
    // peluang = cara[S] / m^n
    long long total = pangkat(m, n);
    cout << cara[S] * pangkat(total, MOD - 2) % MOD << '\n';
    return 0;
}
CODE,
            'javascript' => "const [n, m, S] = readInts();\n".$jsMod.<<<'CODE'

let hasil = 0;
if (S <= n * m) {
  let cara = new Array(S + 1).fill(0);
  cara[0] = 1;
  const pre = new Array(S + 2).fill(0);
  for (let k = 1; k <= n; k++) {
    for (let s = 0; s <= S; s++) pre[s + 1] = (pre[s] + cara[s]) % MOD;
    const baru = new Array(S + 1).fill(0);
    for (let s = 1; s <= S; s++) {
      const kiri = s - m >= 0 ? pre[s - m] : 0;
      baru[s] = (pre[s] - kiri + MOD) % MOD;
    }
    cara = baru;
  }
  hasil = mul(cara[S], pangkat(pangkat(m, n), MOD - 2));
}
console.log(hasil);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7
n, m, S = map(int, input().split())
if S > n * m:
    print(0)
else:
    cara = [0] * (S + 1)
    cara[0] = 1
    for _ in range(n):
        pre = [0] * (S + 2)
        for s in range(S + 1):
            pre[s + 1] = (pre[s] + cara[s]) % MOD
        cara = [0] + [(pre[s] - (pre[s - m] if s >= m else 0)) % MOD for s in range(1, S + 1)]
    print(cara[S] * pow(pow(m, n, MOD), MOD - 2, MOD) % MOD)
CODE,
        ],
        'editorial' => '<p>Hitung banyak hasil yang berjumlah S, lalu bagi dengan total hasil M<sup>N</sup>.</p>
<p><code>cara[k][s]</code> = banyak hasil k dadu berjumlah s. Dadu ke-k bernilai d = 1..M, sehingga <code>cara[k][s] = Σ<sub>d</sub> cara[k−1][s−d]</code>. Cara langsung O(N · S · M) ≈ 10<sup>8</sup>, masih cukup di C++ tetapi berat untuk bahasa lain. Penjumlahan itu adalah jumlah rentang cara[k−1][s−M..s−1], jadi dengan prefix sum setiap state O(1) dan totalnya O(N · S).</p>
<p>Semua dihitung modulo 10<sup>9</sup> + 7. Pembagian dengan M<sup>N</sup> diganti perkalian dengan inversnya: <code>(M<sup>N</sup>)<sup>MOD − 2</sup></code>. Jika S &gt; N · M, peluangnya 0.</p>',
        'hints' => [
            'Hitung banyak hasil yang berjumlah S, lalu bagi dengan total hasil M^N.',
            'cara[k][s] = jumlah cara[k−1][s−d] untuk d = 1..M. Jumlah rentang ini bisa O(1) dengan prefix sum.',
            'Bagi dengan M^N menggunakan invers modular: pangkat(M^N, MOD − 2).',
        ],
    ],

    [
        'slug' => 'papan-tangga',
        'lesson' => 'dp-peluang',
        'title' => 'Papan Tangga Ajaib',
        'difficulty' => 'Sedang',
        'tags' => ['nilai harapan', 'dp mundur', 'self-loop'],
        'statement' => '<p>Sebuah papan permainan berisi petak 0 sampai <strong>N</strong>. Bidak mulai di petak 0. Setiap giliran, lempar dadu biasa (1–6, peluang sama) lalu maju sebanyak angka dadu. Jika langkah itu akan <strong>melewati</strong> petak N, bidak tidak bergerak pada giliran itu.</p>
<p>Ada <strong>L</strong> tangga ajaib: jika bidak berhenti tepat di petak <code>a<sub>i</sub></code>, ia langsung naik ke petak <code>b<sub>i</sub></code> (tanpa tambahan giliran). Berapa harapan banyak giliran sampai bidak tepat di petak N? Cetak sebagai <code>P · Q<sup>−1</sup> mod 10<sup>9</sup> + 7</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N L</code>. L baris berikutnya berisi <code>a b</code>.</p>',
        'output_format' => '<p>Harapan banyak giliran, modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 100 000</li><li>0 ≤ L ≤ N / 2</li><li>1 ≤ a &lt; b ≤ N; semua a berbeda; tidak ada b yang sama dengan sebuah a</li></ul>',
        'samples' => [
            ['input' => "10 1\n3 9\n", 'explanation' => 'Harapannya 265/36 ≈ 7,36 giliran (tanpa tangga: 1639/216 ≈ 7,59).'],
            ['input' => "1 0\n", 'explanation' => 'Harus mendapat angka 1: rata-rata 6 giliran.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $l) {
                $usedA = [];
                $usedB = [];
                $rows = [];
                $tries = 0;
                while (count($rows) < $l && $tries++ < 50 * $l + 100) {
                    $a = mt_rand(1, $n - 1);
                    $b = mt_rand($a + 1, min($n, $a + mt_rand(1, 40)));
                    if (isset($usedA[$a]) || isset($usedB[$a]) || isset($usedA[$b])) {
                        continue;
                    }
                    $usedA[$a] = true;
                    $usedB[$b] = true;
                    $rows[] = "$a $b";
                }

                return "$n ".count($rows)."\n".implode("\n", $rows).(count($rows) ? "\n" : '');
            };

            return ["6 0\n", "7 0\n", "2 1\n1 2\n", $mk(20, 4), $mk(100, 20), $mk(1000, 200), $mk(100000, 0), $mk(100000, 30000), $mk(100000, 50000), $mk(99999, 5)];
        },
        'solve' => function (string $input) use ($MOD, $pw) {
            $lines = T::lines($input);
            [$n, $l] = T::ints($lines[0]);
            $dest = range(0, $n);
            $isA = array_fill(0, $n + 1, false);
            for ($i = 1; $i <= $l; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $dest[$a] = $b;
                $isA[$a] = true;
            }
            $inv = [0];
            for ($k = 1; $k <= 6; $k++) {
                $inv[$k] = $pw($k, $MOD - 2);
            }
            $E = array_fill(0, $n + 1, 0);
            for ($i = $n - 1; $i >= 0; $i--) {
                if ($isA[$i]) {
                    continue;
                }
                $sum = 6;
                $ok = 0;
                for ($d = 1; $d <= 6 && $i + $d <= $n; $d++) {
                    $sum += $E[$dest[$i + $d]];
                    $ok++;
                }
                $E[$i] = ($sum % $MOD) * $inv[$ok] % $MOD;
            }

            return (string) $E[0];
        },
        'starter' => $st("    int n, l;\n    cin >> n >> l;\n    vector<int> tujuan(n + 1);\n    iota(tujuan.begin(), tujuan.end(), 0);   // tujuan[x] = petak akhir jika berhenti di x\n    for (int i = 0; i < l; i++) {\n        int a, b;\n        cin >> a >> b;\n        tujuan[a] = b;\n    }", "const [n, l] = readInts();\nconst tujuan = Array.from({ length: n + 1 }, (_, i) => i);\nfor (let i = 0; i < l; i++) {\n  const [a, b] = readInts();\n  tujuan[a] = b;\n}", "n, l = map(int, input().split())\ntujuan = list(range(n + 1))\nfor _ in range(l):\n    a, b = map(int, input().split())\n    tujuan[a] = b"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppPow.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, l;
    cin >> n >> l;
    vector<int> tujuan(n + 1);
    iota(tujuan.begin(), tujuan.end(), 0);   // tujuan[x] = petak akhir jika berhenti di x
    for (int i = 0; i < l; i++) {
        int a, b;
        cin >> a >> b;
        tujuan[a] = b;
    }
    long long inv[7];
    for (int k = 1; k <= 6; k++) inv[k] = pangkat(k, MOD - 2);

    // E[i] = harapan sisa giliran jika bidak berdiam di petak i. E[n] = 0.
    // E[i] = 1 + (1/6)(Σ_{d sah} E[tujuan[i+d]] + (6 - sah)·E[i])
    //  =>  E[i] = (6 + Σ_{d sah} E[tujuan[i+d]]) / sah
    vector<long long> E(n + 1, 0);
    for (int i = n - 1; i >= 0; i--) {
        if (tujuan[i] != i) continue;          // kaki tangga: bidak tidak pernah berdiam di sini
        long long jumlah = 6;
        int sah = 0;
        for (int d = 1; d <= 6 && i + d <= n; d++) {
            jumlah += E[tujuan[i + d]];         // tujuan[i+d] > i, sudah dihitung
            sah++;
        }
        E[i] = jumlah % MOD * inv[sah] % MOD;
    }
    cout << E[0] << '\n';
    return 0;
}
CODE,
            'javascript' => "const [n, l] = readInts();\n".$jsMod.<<<'CODE'

const tujuan = Array.from({ length: n + 1 }, (_, i) => i);
for (let i = 0; i < l; i++) {
  const [a, b] = readInts();
  tujuan[a] = b;
}
const inv = [0];
for (let k = 1; k <= 6; k++) inv.push(pangkat(k, MOD - 2));
const E = new Array(n + 1).fill(0);
for (let i = n - 1; i >= 0; i--) {
  if (tujuan[i] !== i) continue;
  let jumlah = 6, sah = 0;
  for (let d = 1; d <= 6 && i + d <= n; d++) {
    jumlah += E[tujuan[i + d]];
    sah++;
  }
  E[i] = mul(jumlah % MOD, inv[sah]);
}
console.log(E[0]);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7
n, l = map(int, input().split())
tujuan = list(range(n + 1))
for _ in range(l):
    a, b = map(int, input().split())
    tujuan[a] = b

inv = [0] + [pow(k, MOD - 2, MOD) for k in range(1, 7)]
E = [0] * (n + 1)
for i in range(n - 1, -1, -1):
    if tujuan[i] != i:
        continue
    jumlah, sah = 6, 0
    for d in range(1, 7):
        if i + d > n:
            break
        jumlah += E[tujuan[i + d]]
        sah += 1
    E[i] = jumlah % MOD * inv[sah] % MOD
print(E[0])
CODE,
        ],
        'editorial' => '<p>Definisikan <code>E[i]</code> = harapan sisa giliran jika bidak sedang berdiam di petak i, dengan E[N] = 0. Petak yang merupakan kaki tangga tidak pernah menjadi tempat berdiam, jadi saat bidak mendarat di x kita langsung memakai <code>tujuan[x]</code> (puncak tangga atau x sendiri).</p>
<p>Dari petak i, satu giliran dipakai, lalu untuk setiap d = 1..6 dengan peluang 1/6: jika i + d ≤ N, pindah ke <code>tujuan[i + d]</code>; jika tidak, tetap di i. Maka</p>
<p style="text-align:center"><code>E[i] = 1 + (1/6) · (Σ<sub>d sah</sub> E[tujuan[i+d]] + (6 − sah) · E[i])</code></p>
<p>Selesaikan self-loop secara aljabar: <code>E[i] = (6 + Σ E[tujuan[i+d]]) / sah</code>. Karena tujuan[i + d] &gt; i (tangga hanya naik), isi E dari N − 1 turun ke 0. Pembagian dengan sah memakai invers modular; cukup hitung invers 1..6 sekali. Total O(N).</p>',
        'hints' => [
            'Definisikan E[i] = harapan sisa giliran dari petak i. Base case-nya di mana?',
            'Jika lemparan melewati N, bidak tetap di i: E[i] muncul di kedua ruas. Pindahkan ke ruas kiri.',
            'E[i] = (6 + Σ E[tujuan[i+d]]) / sah, diisi dari belakang. Bagi dengan invers modular.',
        ],
    ],

    [
        'slug' => 'kolektor-stiker',
        'lesson' => 'dp-peluang',
        'title' => 'Kolektor Stiker',
        'difficulty' => 'Sedang',
        'tags' => ['nilai harapan', 'distribusi geometrik', 'linearitas'],
        'statement' => '<p>Sebuah merek camilan menyelipkan satu stiker di setiap bungkus. Ada <strong>N</strong> jenis stiker, dan setiap bungkus berisi salah satu jenis secara acak dengan peluang sama, tidak bergantung pada bungkus lain.</p>
<p>Lala sudah punya <strong>K</strong> jenis yang berbeda. Berapa harapan banyak bungkus yang masih harus ia beli sampai koleksinya lengkap? Cetak sebagai <code>P · Q<sup>−1</sup> mod 10<sup>9</sup> + 7</code>.</p>',
        'input_format' => '<p>Satu baris berisi <code>N K</code>.</p>',
        'output_format' => '<p>Harapan banyak bungkus, modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>6</sup></li><li>0 ≤ K ≤ N</li></ul>',
        'samples' => [
            ['input' => "2 0\n", 'explanation' => 'Bungkus pertama pasti jenis baru. Setelah itu, setiap bungkus berpeluang 1/2 berisi jenis kedua, rata-rata 2 bungkus. Total 1 + 2 = 3.'],
            ['input' => "3 1\n", 'explanation' => 'Jenis baru pertama: peluang 2/3 per bungkus, rata-rata 3/2. Jenis terakhir: peluang 1/3, rata-rata 3. Total 9/2.'],
        ],
        'tests' => function () {
            return ["1 0\n", "1 1\n", "5 5\n", "6 0\n", "10 3\n", "1000 0\n", "1000000 0\n", "1000000 999999\n", "1000000 500000\n", "999983 12345\n", "777777 777000\n"];
        },
        'solve' => function (string $input) use ($MOD) {
            [$n, $k] = T::ints(trim($input));
            $inv = array_fill(0, $n + 2, 0);
            $inv[1] = 1;
            for ($i = 2; $i <= $n; $i++) {
                $inv[$i] = ($MOD - intdiv($MOD, $i) * $inv[$MOD % $i] % $MOD) % $MOD;
            }
            $s = 0;
            for ($i = $k; $i < $n; $i++) {
                $s += $inv[$n - $i];
            }

            return (string) ($s % $MOD * $n % $MOD);
        },
        'starter' => $st("    long long n, k;\n    cin >> n >> k;", 'const [n, k] = readInts();', 'n, k = map(int, input().split())'),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1e9 + 7;

int main() {
    int n, k;
    cin >> n >> k;
    // Invers 1..n dalam O(n): inv[i] = -(MOD / i) · inv[MOD % i]  (mod MOD)
    vector<long long> inv(n + 1, 0);
    if (n >= 1) inv[1] = 1;
    for (int i = 2; i <= n; i++) inv[i] = (MOD - (MOD / i) * inv[MOD % i] % MOD) % MOD;

    // Saat punya i jenis, peluang bungkus berisi jenis baru = (n - i) / n.
    // Harapan bungkus sampai jenis baru = n / (n - i)  (distribusi geometrik).
    // Linearitas: jawaban = Σ_{i=k}^{n-1} n / (n - i).
    long long jumlah = 0;
    for (int i = k; i < n; i++) jumlah = (jumlah + inv[n - i]) % MOD;
    cout << jumlah * n % MOD << '\n';
    return 0;
}
CODE,
            'javascript' => "const [n, k] = readInts();\nconst MOD = 1000000007;\nconst mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;\n".<<<'CODE'
const inv = new Float64Array(n + 1);
if (n >= 1) inv[1] = 1;
for (let i = 2; i <= n; i++) inv[i] = (MOD - mul(Math.floor(MOD / i), inv[MOD % i])) % MOD;
let jumlah = 0;
for (let i = k; i < n; i++) jumlah = (jumlah + inv[n - i]) % MOD;
console.log(mul(jumlah, n % MOD));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7
n, k = map(int, input().split())
inv = [0] * (n + 1)
if n >= 1:
    inv[1] = 1
for i in range(2, n + 1):
    inv[i] = (MOD - (MOD // i) * inv[MOD % i] % MOD) % MOD
jumlah = sum(inv[n - i] for i in range(k, n)) % MOD
print(jumlah * n % MOD)
CODE,
        ],
        'editorial' => '<p>Pecah proses menjadi tahap: tahap i adalah saat Lala sudah punya tepat i jenis dan menunggu jenis baru. Setiap bungkus pada tahap itu berisi jenis baru dengan peluang <code>p = (N − i) / N</code>, bebas dari bungkus lain. Banyak bungkus sampai berhasil berdistribusi geometrik dengan harapan <code>1/p = N / (N − i)</code>.</p>
<p>Total bungkus = jumlah bungkus semua tahap i = K, …, N − 1. Dengan <strong>linearitas nilai harapan</strong>:</p>
<p style="text-align:center"><code>E = Σ<sub>i=K..N−1</sub> N / (N − i) = N · (1/1 + 1/2 + … + 1/(N − K))</code></p>
<p>Butuh invers 1..N modulo p. Memanggil pangkat cepat N kali (O(N log p)) sudah cukup, tetapi ada rumus O(N): <code>inv[i] = −⌊p / i⌋ · inv[p mod i]</code>. Rumus ini berasal dari p = ⌊p/i⌋ · i + (p mod i) dan dibaca modulo p.</p>',
        'hints' => [
            'Bagi menjadi tahap: dari punya i jenis menjadi i + 1 jenis. Berapa peluang satu bungkus berisi jenis baru?',
            'Menunggu keberhasilan berpeluang p butuh rata-rata 1/p percobaan. Jumlahkan semua tahap (linearitas).',
            'Jawaban = N · Σ_{j=1}^{N−K} 1/j. Hitung invers 1..N, lalu jumlahkan modulo.',
        ],
    ],

    [
        'slug' => 'harapan-maksimum',
        'lesson' => 'dp-peluang',
        'title' => 'Lemparan Tertinggi',
        'difficulty' => 'Sulit',
        'tags' => ['nilai harapan', 'jumlah ekor', 'pangkat cepat'],
        'statement' => '<p>Dalam sebuah permainan, kamu melempar <strong>N</strong> dadu bersisi <strong>M</strong> (bertuliskan 1..M, peluang sama) dan skormu adalah angka <strong>terbesar</strong> yang muncul.</p>
<p>Berapa harapan skormu? Cetak sebagai <code>P · Q<sup>−1</sup> mod 10<sup>9</sup> + 7</code>.</p>',
        'input_format' => '<p>Satu baris berisi <code>N M</code>.</p>',
        'output_format' => '<p>Harapan nilai maksimum, modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>9</sup></li><li>1 ≤ M ≤ 100 000</li></ul>',
        'samples' => [
            ['input' => "2 6\n", 'explanation' => 'Harapan maksimum dua dadu biasa adalah 161/36 ≈ 4,47.'],
            ['input' => "1 6\n", 'explanation' => 'Satu dadu: harapan 3,5 = 7/2.'],
        ],
        'tests' => function () {
            return ["1 1\n", "5 1\n", "3 2\n", "2 100\n", "1000000000 6\n", "1000000000 100000\n", "1 100000\n", "7 99991\n", "123456789 54321\n", "2 100000\n"];
        },
        'solve' => function (string $input) use ($MOD, $pw) {
            [$n, $m] = T::ints(trim($input));
            $s = 0;
            for ($y = 1; $y < $m; $y++) {
                $s += $pw($y, $n);
            }
            $s %= $MOD;

            return (string) ((($m - $s * $pw($pw($m, $n), $MOD - 2)) % $MOD + $MOD) % $MOD);
        },
        'starter' => $st("    long long n, m;\n    cin >> n >> m;", 'const [n, m] = readInts();', 'n, m = map(int, input().split())'),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppPow.<<<'CODE'


int main() {
    long long n, m;
    cin >> n >> m;
    // E[maks] = Σ_{x=1}^{m} P(maks ≥ x) = Σ_{x=1}^{m} (1 - ((x-1)/m)^n)
    //         = m - (0^n + 1^n + ... + (m-1)^n) / m^n
    long long jumlah = 0;
    for (long long y = 1; y < m; y++) jumlah = (jumlah + pangkat(y, n)) % MOD;
    long long bagi = jumlah * pangkat(pangkat(m, n), MOD - 2) % MOD;
    cout << ((m - bagi) % MOD + MOD) % MOD << '\n';
    return 0;
}
CODE,
            'javascript' => "const [n, m] = readInts();\n".$jsMod.<<<'CODE'

let jumlah = 0;
for (let y = 1; y < m; y++) jumlah = (jumlah + pangkat(y, n)) % MOD;
const bagi = mul(jumlah, pangkat(pangkat(m, n), MOD - 2));
console.log((((m - bagi) % MOD) + MOD) % MOD);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MOD = 10**9 + 7
n, m = map(int, input().split())
jumlah = sum(pow(y, n, MOD) for y in range(1, m)) % MOD
bagi = jumlah * pow(pow(m, n, MOD), MOD - 2, MOD) % MOD
print((m - bagi) % MOD)
CODE,
        ],
        'editorial' => '<p>Menghitung P(maksimum = x) secara langsung bisa, tetapi ada jalan yang lebih bersih: <strong>rumus jumlah ekor</strong>. Untuk variabel acak X bernilai bilangan bulat 1..M,</p>
<p style="text-align:center"><code>E[X] = Σ<sub>x=1..M</sub> P(X ≥ x)</code></p>
<p>(Setiap nilai X = v terhitung tepat v kali di ruas kanan, yaitu untuk x = 1..v.) Kejadian "maksimum ≥ x" adalah kebalikan dari "semua dadu ≤ x − 1", dan karena dadu bebas, <code>P(semua ≤ x − 1) = ((x − 1)/M)<sup>N</sup></code>. Maka</p>
<p style="text-align:center"><code>E = M − (1<sup>N</sup> + 2<sup>N</sup> + … + (M−1)<sup>N</sup>) / M<sup>N</sup></code></p>
<p>Setiap pangkat dihitung dengan pangkat cepat, total O(M log N). Pembagian dengan M<sup>N</sup> memakai invers modular.</p>',
        'hints' => [
            'Mencari P(maks = x) itu repot. Bagaimana dengan P(maks ≥ x)? Atau kebalikannya, P(maks ≤ x − 1)?',
            'Untuk X bilangan bulat positif, E[X] = Σ_{x≥1} P(X ≥ x). Dan P(semua dadu ≤ t) = (t/M)^N.',
            'E = M − Σ_{y=1}^{M−1} y^N / M^N. Pakai pangkat cepat dan invers modular.',
        ],
    ],
];
