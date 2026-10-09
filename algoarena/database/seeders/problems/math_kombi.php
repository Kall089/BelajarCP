<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Kombinatorika: faktorial & invers faktorial modulo prima,
 * permutasi multiset, bintang dan sekat, derangement, dan jalur grid dengan rintangan.
 * Semua jawaban dicetak modulo 10^9 + 7. Semua kode C++ harus lolos C++14.
 * JavaScript memakai perkalian modulo yang dipecah 16 bit agar tetap tepat dengan Number.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nconst long long MOD = 1e9 + 7;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini (cetak jawaban modulo MOD)\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : '')."const MOD = 1000000007;\n// a · b mod MOD tanpa melewati batas presisi Number (2^53)\nconst mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;\n\n// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\nMOD = 10**9 + 7\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini (pow(x, MOD - 2, MOD) memberi invers x)\n\n\nprint()\n",
    ];
};

$MOD = 1000000007;

$pw = function (int $a, int $e): int {
    $r = 1;
    $a %= 1000000007;
    while ($e > 0) {
        if ($e & 1) {
            $r = $r * $a % 1000000007;
        }
        $a = $a * $a % 1000000007;
        $e >>= 1;
    }

    return $r;
};

/** [fact, invFact] untuk 0..n: fact[i] = i!, invFact[i] = 1 / i! (mod p). Untuk solusi referensi PHP. */
$fakt = function (int $n) use ($pw): array {
    $fact = array_fill(0, $n + 1, 1);
    for ($i = 1; $i <= $n; $i++) {
        $fact[$i] = $fact[$i - 1] * $i % 1000000007;
    }
    $inv = array_fill(0, $n + 1, 1);
    $inv[$n] = $pw($fact[$n], 1000000005);
    for ($i = $n; $i > 0; $i--) {
        $inv[$i - 1] = $inv[$i] * $i % 1000000007;
    }

    return [$fact, $inv];
};

/** Kode C++ faktorial & invers faktorial yang dipakai bersama semua solusi. */
$cppFakt = <<<'CODE'
const long long MOD = 1e9 + 7;
vector<long long> fact, invFact;

long long pangkat(long long a, long long e) {   // a^e mod MOD dalam O(log e)
    long long hasil = 1;
    a %= MOD;
    while (e > 0) {
        if (e & 1) hasil = hasil * a % MOD;
        a = a * a % MOD;
        e >>= 1;
    }
    return hasil;
}

// fact[i] = i! dan invFact[i] = 1 / i! (mod MOD) untuk i = 0..n, total O(n)
void siapkan(int n) {
    fact.assign(n + 1, 1);
    invFact.assign(n + 1, 1);
    for (int i = 1; i <= n; i++) fact[i] = fact[i - 1] * i % MOD;
    invFact[n] = pangkat(fact[n], MOD - 2);          // Teorema kecil Fermat: x^(p-2) = 1/x
    for (int i = n; i > 0; i--) invFact[i - 1] = invFact[i] * i % MOD;
}
CODE;

$cppC = <<<'CODE'
// C(n, k) dalam O(1); 0 jika k di luar 0..n
long long C(int n, int k) {
    if (k < 0 || k > n) return 0;
    return fact[n] * invFact[k] % MOD * invFact[n - k] % MOD;
}
CODE;

$jsFakt = <<<'CODE'
const MOD = 1000000007;
// a · b mod MOD tanpa melewati batas presisi Number (2^53)
const mul = (a, b) => (((a * (b >>> 16)) % MOD) * 65536 + a * (b & 65535)) % MOD;
const pangkat = (a, e) => {
  let hasil = 1;
  a %= MOD;
  while (e > 0) {
    if (e % 2 === 1) hasil = mul(hasil, a);
    a = mul(a, a);
    e = Math.floor(e / 2);
  }
  return hasil;
};
// fact[i] = i! dan invFact[i] = 1 / i! (mod MOD) untuk i = 0..n
let fact, invFact;
const siapkan = (n) => {
  fact = new Float64Array(n + 1);
  invFact = new Float64Array(n + 1);
  fact[0] = 1;
  for (let i = 1; i <= n; i++) fact[i] = mul(fact[i - 1], i);
  invFact[n] = pangkat(fact[n], MOD - 2); // Teorema kecil Fermat
  for (let i = n; i > 0; i--) invFact[i - 1] = mul(invFact[i], i);
};
CODE;

$jsC = <<<'CODE'
// C(n, k) dalam O(1); 0 jika k di luar 0..n
const C = (n, k) => (k < 0 || k > n ? 0 : mul(mul(fact[n], invFact[k]), invFact[n - k]));
CODE;

$pyFakt = <<<'CODE'
MOD = 10**9 + 7


def siapkan(n):
    """fact[i] = i! dan inv[i] = 1 / i! (mod MOD) untuk i = 0..n."""
    fact = [1] * (n + 1)
    for i in range(1, n + 1):
        fact[i] = fact[i - 1] * i % MOD
    inv = [1] * (n + 1)
    inv[n] = pow(fact[n], MOD - 2, MOD)     # Teorema kecil Fermat
    for i in range(n, 0, -1):
        inv[i - 1] = inv[i] * i % MOD
    return fact, inv
CODE;

$pyHead = "import sys\ninput = sys.stdin.readline\n\n".$pyFakt."\n\n\n";

return [
    [
        'slug' => 'susun-huruf',
        'lesson' => 'kombinatorika',
        'title' => 'Menyusun Kartu Huruf',
        'difficulty' => 'Mudah',
        'tags' => ['kombinatorika', 'permutasi multiset', 'invers faktorial'],
        'statement' => '<p>Nadia menemukan setumpuk kartu dari permainan tebak kata. Setiap kartu bertuliskan satu huruf kecil, dan jika dijajarkan, kartu-kartu itu membentuk kata <strong>S</strong>. Ia penasaran: ada berapa banyak untaian huruf <em>berbeda</em> yang bisa disusun dengan memakai <strong>semua</strong> kartu tersebut, termasuk S sendiri? Untaiannya tidak harus bermakna.</p>
<p>Kartu yang hurufnya sama tidak bisa dibedakan, jadi dua susunan dianggap sama jika huruf di setiap posisinya sama. Karena jawabannya bisa sangat besar, cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Satu baris berisi kata <code>S</code>.</p>',
        'output_format' => '<p>Banyak untaian berbeda, modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ |S| ≤ 10<sup>6</sup></li><li>S hanya berisi huruf kecil <code>a</code>–<code>z</code></li></ul>',
        'samples' => [
            ['input' => "mama\n", 'explanation' => 'Keenam untaian itu: aamm, amam, amma, maam, mama, mmaa. Dengan rumus: 4! / (2! · 2!) = 24 / 4 = 6.'],
            ['input' => "banana\n", 'explanation' => 'Ada 3 huruf a, 2 huruf n, dan 1 huruf b, sehingga jawabannya 6! / (3! · 2! · 1!) = 720 / 12 = 60.'],
            ['input' => "abcdefghijklmnopqrstuvwxyz\n", 'explanation' => 'Semua huruf berbeda, jadi jawabannya 26! = 403 291 461 126 605 635 584 000 000. Sisa pembagiannya oleh 10<sup>9</sup> + 7 adalah 459 042 011.'],
        ],
        'tests' => function () {
            $sedikit = str_repeat('k', 1000000);
            for ($i = 0; $i < 10; $i++) {
                $sedikit[mt_rand(0, 999999)] = 'abc'[mt_rand(0, 2)];
            }

            return [
                "a\n", "zz\n", "ab\n", "abcdefghij\n", "mississippi\n", T::word(8, 'abc')."\n", T::word(1000, 'abcde')."\n",
                str_repeat('a', 1000000)."\n", T::word(1000000, 'abcdefghijklmnopqrstuvwxyz')."\n", T::word(1000000, 'ab')."\n",
                T::word(999999, 'xyz')."\n", $sedikit."\n",
            ];
        },
        'solve' => function (string $input) use ($MOD, $fakt) {
            $s = trim($input);
            $n = strlen($s);
            [$fact, $inv] = $fakt($n);
            $ans = $fact[$n];
            foreach (count_chars($s, 1) as $c) {
                $ans = $ans * $inv[$c] % $MOD;
            }

            return (string) $ans;
        },
        'starter' => $st("    string s;\n    cin >> s;", 'const s = readLine().trim();', 's = input().strip()'),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppFakt.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string s;
    cin >> s;
    int n = s.size();
    siapkan(n);

    int cnt[26] = {0};
    for (char ch : s) cnt[ch - 'a']++;

    // n! / (c_a! · c_b! · ... · c_z!)  =  n! · invFact[c_a] · invFact[c_b] · ... · invFact[c_z]
    long long ans = fact[n];
    for (int i = 0; i < 26; i++) ans = ans * invFact[cnt[i]] % MOD;
    cout << ans << '\n';
    return 0;
}
CODE,
            'javascript' => "const s = readLine().trim();\n\n".$jsFakt.<<<'CODE'


const n = s.length;
siapkan(n);
const cnt = new Array(26).fill(0);
for (let i = 0; i < n; i++) cnt[s.charCodeAt(i) - 97]++;
// n! · invFact[c_a] · invFact[c_b] · ... · invFact[c_z]
let ans = fact[n];
for (let c = 0; c < 26; c++) ans = mul(ans, invFact[cnt[c]]);
console.log(ans);
CODE,
            'python' => $pyHead.<<<'CODE'
s = input().strip()
n = len(s)
fact, inv = siapkan(n)
ans = fact[n]
for ch in set(s):
    ans = ans * inv[s.count(ch)] % MOD   # membagi dengan c! = mengalikan dengan 1/c!
print(ans)
CODE,
        ],
        'editorial' => '<p>Andaikan dulu setiap kartu diberi nomor kecil di pojoknya sehingga semua kartu bisa dibedakan. Banyak susunannya adalah <code>n!</code> (n = |S|). Sekarang hapus nomor-nomor itu. Satu untaian huruf ternyata muncul berkali-kali di antara n! susunan tadi, tepatnya <code>c<sub>a</sub>! · c<sub>b</sub>! · … · c<sub>z</sub>!</code> kali, karena kartu-kartu berhuruf sama boleh saling bertukar tempat tanpa mengubah untaiannya (c<sub>x</sub> = banyak huruf x di S). Maka</p>
<p style="text-align:center"><code>jawaban = n! / (c<sub>a</sub>! · c<sub>b</sub>! · … · c<sub>z</sub>!)</code></p>
<p>Rumus ini disebut <strong>permutasi multiset</strong>.</p>
<p>Dalam modulo, pembagian diganti perkalian dengan invers. Karena 10<sup>9</sup> + 7 prima, siapkan <code>fact[i] = i!</code> untuk i = 0..n, lalu <code>invFact[n] = fact[n]<sup>p−2</sup></code> (Teorema kecil Fermat) dan mundur <code>invFact[i−1] = invFact[i] · i</code>. Jawabannya <code>fact[n] · invFact[c<sub>a</sub>] · … · invFact[c<sub>z</sub>]</code>. Total O(n + log p).</p>
<p><strong>Jebakan:</strong> 21! saja sudah melampaui <code>long long</code>, jadi n! tidak bisa dihitung utuh lalu dibagi. Membagi sisa modulo dengan pembagian biasa, misalnya <code>(n! mod p) / (c! mod p)</code>, juga salah.</p>',
        'hints' => [
            'Bayangkan semua kartu diberi nomor sehingga bisa dibedakan. Ada berapa susunan? Berapa kali satu untaian huruf terhitung di dalamnya?',
            'Kartu berhuruf sama bisa saling ditukar tanpa mengubah untaian: jawaban = n! / (c_a! · c_b! · … · c_z!).',
            'Pembagian modulo prima = perkalian dengan invers. Siapkan fact[] dan invFact[] sampai n, lalu kalikan fact[n] dengan invFact[c_x] untuk setiap huruf x.',
        ],
    ],

    [
        'slug' => 'bagi-permen',
        'lesson' => 'kombinatorika',
        'title' => 'Bagi-Bagi Permen',
        'difficulty' => 'Sedang',
        'tags' => ['kombinatorika', 'bintang dan sekat', 'invers faktorial'],
        'statement' => '<p>Bu Sari membawa <strong>N</strong> permen yang semuanya sama ke kelas dan ingin membagikan <strong>semuanya</strong> kepada <strong>K</strong> murid. Sebelumnya ia sudah berjanji bahwa murid ke-i akan mendapat paling sedikit <code>L<sub>i</sub></code> permen. Murid boleh mendapat lebih dari janjinya, dan murid dengan <code>L<sub>i</sub> = 0</code> boleh tidak mendapat permen sama sekali.</p>
<p>Dua cara pembagian dianggap berbeda jika ada murid yang menerima banyak permen yang berbeda. Ada berapa cara membagikan permen yang menepati semua janji? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>L<sub>1</sub> L<sub>2</sub> … L<sub>K</sub></code>.</p>',
        'output_format' => '<p>Banyak cara modulo 10<sup>9</sup> + 7 (0 jika janji Bu Sari mustahil ditepati).</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>6</sup></li><li>1 ≤ K ≤ 10<sup>5</sup></li><li>0 ≤ L<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "5 3\n1 0 2\n", 'explanation' => 'Setelah jatah minimum (1, 0, 2) dibagikan, tersisa 2 permen bebas. Keenam pembagiannya: (3, 0, 2), (1, 2, 2), (1, 0, 4), (2, 1, 2), (2, 0, 3), dan (1, 1, 3).'],
            ['input' => "4 2\n3 2\n", 'explanation' => 'Janjinya saja sudah 3 + 2 = 5 permen, lebih dari 4.'],
            ['input' => "7 3\n0 0 0\n", 'explanation' => 'Tanpa batas bawah: 7 bintang dan 2 sekat, C(9, 2) = 36 cara.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $k, int $lo, int $hi) {
                $L = [];
                for ($i = 0; $i < $k; $i++) {
                    $L[] = mt_rand($lo, $hi);
                }

                return "$n $k\n".implode(' ', $L)."\n";
            };
            // L acak yang jumlahnya tepat $total
            $mkSum = function (int $n, int $k, int $total) {
                $L = array_fill(0, $k, 0);
                for ($t = 0; $t < $total; $t++) {
                    $L[mt_rand(0, $k - 1)]++;
                }

                return "$n $k\n".implode(' ', $L)."\n";
            };

            return [
                "1 1\n0\n", "1 1\n2\n", "5 5\n1 1 1 1 1\n", "6 5\n1 1 1 1 1\n", $mk(20, 4, 0, 4), $mk(1000, 50, 0, 15),
                $mk(1000000, 100000, 0, 0), $mk(1000000, 100000, 0, 9), $mkSum(1000000, 100000, 999997), $mkSum(999999, 100000, 1000000),
                "1000000 2\n123456 654321\n", "999999 1\n999999\n", $mkSum(1000000, 3, 10),
            ];
        },
        'solve' => function (string $input) use ($MOD, $pw) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $janji = array_sum(T::ints($lines[1] ?? ''));
            if ($janji > $n) {
                return '0';
            }
            $m = $n - $janji;
            // C(m + k - 1, k - 1) = (m + 1)(m + 2)…(m + k - 1) / (k - 1)!
            $atas = 1;
            $bawah = 1;
            for ($i = 1; $i < $k; $i++) {
                $atas = $atas * ($m + $i) % $MOD;
                $bawah = $bawah * $i % $MOD;
            }

            return (string) ($atas * $pw($bawah, $MOD - 2) % $MOD);
        },
        'starter' => $st("    int n, k;\n    cin >> n >> k;\n    vector<long long> L(k);\n    for (auto& x : L) cin >> x;", "const [n, k] = readInts();\nconst L = readInts();", "n, k = map(int, input().split())\nL = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppFakt."\n\n".$cppC.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, k;
    cin >> n >> k;
    long long janji = 0;                 // jumlah semua L_i, bisa sampai 10^11
    for (int i = 0; i < k; i++) {
        long long l;
        cin >> l;
        janji += l;
    }
    if (janji > n) {                     // permen tidak cukup untuk menepati janji
        cout << 0 << '\n';
        return 0;
    }
    int m = (int) (n - janji);           // permen bebas setelah jatah minimum dibagikan

    // Bintang dan sekat: susun m bintang (permen) dan k - 1 sekat dalam satu baris.
    siapkan(m + k - 1);
    cout << C(m + k - 1, k - 1) << '\n';
    return 0;
}
CODE,
            'javascript' => "const [n, k] = readInts();\nconst L = readInts();\n\n".$jsFakt."\n\n".$jsC.<<<'CODE'


let janji = 0; // ≤ 10^11, masih tepat di Number
for (const x of L) janji += x;
if (janji > n) {
  console.log(0);
} else {
  const m = n - janji; // permen bebas
  siapkan(m + k - 1);
  console.log(C(m + k - 1, k - 1));
}
CODE,
            'python' => $pyHead.<<<'CODE'
n, k = map(int, input().split())
janji = sum(map(int, input().split()))
if janji > n:
    print(0)
else:
    m = n - janji                       # permen bebas setelah jatah minimum
    fact, inv = siapkan(m + k - 1)
    # bintang dan sekat: C(m + k - 1, k - 1)
    print(fact[m + k - 1] * inv[k - 1] % MOD * inv[m] % MOD)
CODE,
        ],
        'editorial' => '<p>Pertama, tepati semua janji: berikan L<sub>i</sub> permen kepada murid ke-i. Jika <code>S = L<sub>1</sub> + … + L<sub>K</sub> &gt; N</code>, permennya tidak cukup dan jawabannya 0. Jika cukup, tersisa <code>M = N − S</code> permen yang boleh dibagi bebas (setiap murid boleh mendapat 0 tambahan). Jatah minimum itu wajib dan hanya ada satu cara memberikannya, jadi jawabannya sama dengan banyak cara membagi M permen kepada K murid tanpa batas bawah.</p>
<p>Gunakan <strong>bintang dan sekat</strong> (<em>stars and bars</em>): jajarkan M permen (bintang) lalu sisipkan K − 1 sekat yang memisahkan bagian murid 1, 2, …, K. Setiap pembagian berpadanan satu-satu dengan susunan M bintang dan K − 1 sekat dalam satu baris sepanjang M + K − 1, sehingga</p>
<p style="text-align:center"><code>jawaban = C(M + K − 1, K − 1)</code></p>
<p>Pada contoh pertama M = 2 dan K = 3; susunan <code>★|★|</code> berarti tambahan (1, 1, 0), dan banyak susunannya C(4, 2) = 6.</p>
<p>Hitung C dengan faktorial dan invers faktorial sampai M + K − 1 ≤ 1,1 · 10<sup>6</sup>, total O(N + K). <strong>Jebakan:</strong> jumlah L bisa mencapai 10<sup>11</sup>, jadi simpan dalam <code>long long</code>, dan jangan lupa kasus S &gt; N.</p>',
        'hints' => [
            'Berikan dulu jatah minimum setiap murid. Berapa permen yang tersisa, dan apa artinya jika tidak cukup?',
            'Sisa M permen dibagi ke K murid, masing-masing boleh 0. Bayangkan M bintang dalam satu baris dan K − 1 sekat pemisah.',
            'Banyak susunan M bintang dan K − 1 sekat adalah C(M + K − 1, K − 1). Hitung dengan faktorial dan invers faktorial.',
        ],
    ],

    [
        'slug' => 'kado-tertukar',
        'lesson' => 'kombinatorika',
        'title' => 'Tukar Kado Akhir Tahun',
        'difficulty' => 'Sedang',
        'tags' => ['kombinatorika', 'derangement', 'prekomputasi'],
        'statement' => '<p>Pada acara tukar kado akhir tahun, setiap anak membawa satu kado yang sudah diberi nama pembawanya. Semua kado dikumpulkan, diacak, lalu dibagikan kembali sehingga setiap anak menerima tepat satu kado. Anak yang menerima kadonya sendiri tentu akan sedikit kecewa.</p>
<p>Panitia punya <strong>Q</strong> pertanyaan. Setiap pertanyaan berisi <code>n</code> dan <code>k</code>: jika ada n anak, ada berapa cara membagikan kado sehingga <strong>tepat k</strong> anak menerima kadonya sendiri? Dua cara berbeda jika ada anak yang menerima kado berbeda. Cetak setiap jawaban modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi <code>n k</code>.</p>',
        'output_format' => '<p>Q baris, jawaban setiap pertanyaan modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ n ≤ 10<sup>6</sup></li><li>0 ≤ k ≤ n</li></ul>',
        'samples' => [
            ['input' => "5\n3 0\n3 1\n3 2\n3 3\n4 2\n", 'explanation' => 'Misalkan anaknya A, B, C, dan tulis pemilik kado yang diterima A, B, C berurutan. Tidak ada yang menerima kadonya sendiri: BCA dan CAB (2 cara). Tepat satu: ACB, CBA, dan BAC (3 cara). Tepat dua mustahil, karena jika dua anak sudah memegang kadonya sendiri, kado yang tersisa pasti milik anak ketiga. Tepat tiga: ABC saja. Untuk 4 anak dan tepat 2: pilih 2 anak yang menerima kadonya sendiri (C(4, 2) = 6 cara), lalu dua anak sisanya harus saling bertukar.'],
            ['input' => "2\n10 0\n6 3\n", 'explanation' => 'Untuk 10 anak ada 1 334 961 cara tanpa seorang pun menerima kadonya sendiri. Untuk 6 anak dan tepat 3: C(6, 3) · 2 = 20 · 2 = 40, karena tiga anak sisanya punya 2 cara saling menukar kado (seperti BCA dan CAB).'],
        ],
        'tests' => function () {
            $mk = function (int $q, int $nLo, int $nHi, string $mode) {
                $out = [(string) $q];
                for ($i = 0; $i < $q; $i++) {
                    $n = mt_rand($nLo, $nHi);
                    if ($mode === 'nol') {
                        $k = 0;
                    } elseif ($mode === 'kecil') {
                        $k = mt_rand(0, min($n, 20));
                    } elseif ($mode === 'ujung') {
                        $k = mt_rand(max(0, $n - 5), $n);
                    } else {
                        $k = mt_rand(0, $n);
                    }
                    $out[] = "$n $k";
                }

                return implode("\n", $out)."\n";
            };
            $semua = [];
            for ($n = 1; $n <= 6; $n++) {
                for ($k = 0; $k <= $n; $k++) {
                    $semua[] = "$n $k";
                }
            }

            return [
                "1\n1 0\n", "1\n1 1\n", count($semua)."\n".implode("\n", $semua)."\n", $mk(1000, 1, 20, 'acak'), $mk(200000, 1, 30, 'acak'),
                $mk(200000, 1, 1000000, 'acak'), $mk(200000, 1, 1000000, 'nol'), $mk(200000, 1000000, 1000000, 'kecil'), "1\n1000000 0\n",
                $mk(200000, 999000, 1000000, 'ujung'), "3\n1000000 1000000\n1000000 999999\n1000000 999998\n",
            ];
        },
        'solve' => function (string $input) use ($MOD, $fakt) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $qs = [];
            $N = 1;
            for ($i = 1; $i <= $q; $i++) {
                $qs[] = T::ints($lines[$i]);
                $N = max($N, $qs[$i - 1][0]);
            }
            [$fact, $inv] = $fakt($N);
            $D = array_fill(0, $N + 1, 0);
            $D[0] = 1;
            for ($m = 2; $m <= $N; $m++) {
                $D[$m] = ($m - 1) * (($D[$m - 1] + $D[$m - 2]) % $MOD) % $MOD;
            }
            $out = [];
            foreach ($qs as [$n, $k]) {
                $out[] = $fact[$n] * $inv[$k] % $MOD * $inv[$n - $k] % $MOD * $D[$n - $k] % $MOD;
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int q;\n    cin >> q;\n    vector<int> n(q), k(q);\n    for (int i = 0; i < q; i++) cin >> n[i] >> k[i];", "const [q] = readInts();\nconst tanya = [];\nfor (let i = 0; i < q; i++) tanya.push(readInts()); // [n, k]", "q = int(input())\ntanya = [tuple(map(int, input().split())) for _ in range(q)]  # (n, k)"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppFakt."\n\n".$cppC.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    vector<int> n(q), k(q);
    int N = 1;
    for (int i = 0; i < q; i++) {
        cin >> n[i] >> k[i];
        N = max(N, n[i]);
    }
    siapkan(N);

    // D[m] = banyak derangement m kado: tidak ada yang kembali ke pemiliknya
    // D[m] = (m - 1) · (D[m-1] + D[m-2]),  D[0] = 1, D[1] = 0
    vector<long long> D(N + 1, 0);
    D[0] = 1;
    for (int m = 2; m <= N; m++) D[m] = (m - 1) * ((D[m - 1] + D[m - 2]) % MOD) % MOD;

    string out;
    for (int i = 0; i < q; i++) {
        // pilih k anak yang menerima kadonya sendiri, sisanya harus derangement
        long long jawab = C(n[i], k[i]) * D[n[i] - k[i]] % MOD;
        out += to_string(jawab);
        out += '\n';
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [q] = readInts();
const qn = new Int32Array(q), qk = new Int32Array(q);
let N = 1;
for (let i = 0; i < q; i++) {
  const [n, k] = readInts();
  qn[i] = n;
  qk[i] = k;
  if (n > N) N = n;
}


CODE.$jsFakt."\n\n".$jsC.<<<'CODE'


siapkan(N);
// D[m] = derangement m kado: D[m] = (m - 1)(D[m-1] + D[m-2]), D[0] = 1, D[1] = 0
const D = new Float64Array(N + 1);
D[0] = 1;
for (let m = 2; m <= N; m++) D[m] = mul(m - 1, (D[m - 1] + D[m - 2]) % MOD);
const out = new Array(q);
for (let i = 0; i < q; i++) out[i] = mul(C(qn[i], qk[i]), D[qn[i] - qk[i]]);
console.log(out.join("\n"));
CODE,
            'python' => $pyHead.<<<'CODE'
q = int(input())
tanya = [tuple(map(int, input().split())) for _ in range(q)]
N = max(n for n, k in tanya)
fact, inv = siapkan(N)

# D[m] = derangement m kado: D[m] = (m - 1)(D[m-1] + D[m-2]), D[0] = 1, D[1] = 0
D = [0] * (N + 1)
D[0] = 1
for m in range(2, N + 1):
    D[m] = (m - 1) * (D[m - 1] + D[m - 2]) % MOD

# jawaban = C(n, k) · D(n - k)
out = [fact[n] * inv[k] % MOD * inv[n - k] % MOD * D[n - k] % MOD for n, k in tanya]
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Pilih dulu <strong>siapa saja</strong> k anak yang menerima kadonya sendiri: <code>C(n, k)</code> cara. Kado milik n − k anak sisanya harus berputar di antara mereka sendiri tanpa seorang pun menerima kadonya sendiri. Permutasi m benda tanpa titik tetap seperti ini disebut <strong>derangement</strong>, banyaknya ditulis D(m). Jadi</p>
<p style="text-align:center"><code>jawaban = C(n, k) · D(n − k)</code></p>
<p><strong>Rekurens derangement.</strong> Perhatikan anak pertama di antara m anak. Ia menerima kado milik anak lain, sebut anak j (m − 1 pilihan). Ada dua kasus:</p>
<ul><li>anak j menerima kado anak pertama: keduanya saling bertukar, dan m − 2 anak sisanya membentuk derangement, yaitu D(m − 2) cara;</li>
<li>anak j <em>tidak</em> menerima kado anak pertama: anggap kado anak pertama sebagai kado "terlarang" bagi anak j. Sekarang m − 1 anak (semua kecuali anak pertama) masing-masing punya tepat satu kado terlarang, jadi banyaknya D(m − 1).</li></ul>
<p style="text-align:center"><code>D(m) = (m − 1) · (D(m − 1) + D(m − 2)),  D(0) = 1,  D(1) = 0</code></p>
<p>Hitung fact, invFact, dan D sekali sampai n terbesar dalam O(N), lalu setiap pertanyaan dijawab O(1). Total O(N + Q).</p>
<p>Dengan inklusi–eksklusi juga didapat <code>D(m) = Σ<sub>i=0..m</sub> (−1)<sup>i</sup> · m! / i!</code>, tetapi untuk banyak pertanyaan, rekurens di atas lebih praktis. <strong>Jebakan:</strong> D(0) = 1 (tidak ada anak tersisa berarti tepat satu cara), sehingga k = n memberi jawaban 1, sedangkan k = n − 1 selalu 0 karena D(1) = 0.</p>',
        'hints' => [
            'Pilih dulu siapa saja k anak yang menerima kadonya sendiri. Apa syarat untuk n − k anak sisanya?',
            'Sisanya harus berupa permutasi tanpa titik tetap (derangement). Jawaban = C(n, k) · D(n − k).',
            'D(m) = (m − 1)(D(m − 1) + D(m − 2)) dengan D(0) = 1, D(1) = 0. Prekomputasi D, faktorial, dan invers faktorial sampai 10^6.',
        ],
    ],

    [
        'slug' => 'jalan-berlubang',
        'lesson' => 'kombinatorika',
        'title' => 'Kurir dan Jalan Berlubang',
        'difficulty' => 'Sulit',
        'tags' => ['kombinatorika', 'jalur grid', 'inklusi-eksklusi', 'dp'],
        'statement' => '<p>Kota Petakan berbentuk grid <strong>H</strong> × <strong>W</strong>: petak (r, c) berada di baris r (1..H, dari atas) dan kolom c (1..W, dari kiri). Seorang kurir berangkat dari petak (1, 1) menuju petak (H, W). Agar cepat sampai, ia hanya melangkah ke <strong>kanan</strong> (kolom bertambah 1) atau ke <strong>bawah</strong> (baris bertambah 1).</p>
<p>Musim hujan membuat <strong>K</strong> petak berlubang besar sehingga tidak boleh diinjak. Ada berapa jalur berbeda yang bisa ditempuh kurir? Cetak modulo 10<sup>9</sup> + 7.</p>',
        'input_format' => '<p>Baris pertama berisi <code>H W K</code>. K baris berikutnya masing-masing berisi <code>r c</code>, posisi sebuah lubang.</p>',
        'output_format' => '<p>Banyak jalur dari (1, 1) ke (H, W) yang tidak menginjak lubang, modulo 10<sup>9</sup> + 7.</p>',
        'constraints' => '<ul><li>1 ≤ H, W ≤ 10<sup>5</sup></li><li>0 ≤ K ≤ 2000</li><li>1 ≤ r ≤ H dan 1 ≤ c ≤ W; semua lubang berbeda</li><li>Petak (1, 1) dan (H, W) tidak berlubang</li></ul>',
        'samples' => [
            ['input' => "3 3 1\n2 2\n", 'explanation' => 'Tanpa lubang ada C(4, 2) = 6 jalur. Jalur yang menginjak (2, 2): 2 cara dari (1, 1) ke (2, 2) dikali 2 cara dari (2, 2) ke (3, 3), yaitu 4. Tersisa 6 − 4 = 2 jalur: menyusuri baris teratas lalu kolom paling kanan, atau kolom paling kiri lalu baris terbawah.'],
            ['input' => "3 4 2\n2 2\n2 3\n", 'explanation' => 'Ada 10 jalur tanpa lubang; 6 jalur menginjak (2, 2), 6 jalur menginjak (2, 3), dan 4 jalur menginjak keduanya. Inklusi–eksklusi: 10 − 6 − 6 + 4 = 2 (lurus ke kanan lalu turun, atau turun dulu lalu lurus ke kanan).'],
            ['input' => "2 2 2\n1 2\n2 1\n", 'explanation' => 'Kedua kemungkinan langkah pertama sama-sama berlubang, jadi tidak ada jalur.'],
        ],
        'tests' => function () {
            $fmt = function (int $h, int $w, array $holes) {
                T::shuffle($holes);

                return "$h $w ".count($holes)."\n".implode("\n", $holes).(count($holes) ? "\n" : '');
            };
            // $k lubang acak di dalam persegi panjang [r0, r1] × [c0, c1]
            $acak = function (int $h, int $w, int $k, int $r0, int $r1, int $c0, int $c1) use ($fmt) {
                $set = [];
                while (count($set) < $k) {
                    $r = mt_rand($r0, $r1);
                    $c = mt_rand($c0, $c1);
                    if (($r === 1 && $c === 1) || ($r === $h && $c === $w)) {
                        continue;
                    }
                    $set["$r $c"] = true;
                }

                return $fmt($h, $w, array_keys($set));
            };
            // rantai: lubang-lubang yang saling berada di kiri-atas (kasus terburuk O(K^2))
            $rantai = function (int $h, int $w, int $k) use ($fmt) {
                $rs = [];
                $cs = [];
                while (count($rs) < $k) {
                    $rs[mt_rand(2, $h - 1)] = true;
                }
                while (count($cs) < $k) {
                    $cs[mt_rand(2, $w - 1)] = true;
                }
                $rs = array_keys($rs);
                $cs = array_keys($cs);
                sort($rs);
                sort($cs);
                $holes = [];
                for ($i = 0; $i < $k; $i++) {
                    $holes[] = $rs[$i].' '.$cs[$i];
                }

                return $fmt($h, $w, $holes);
            };
            // dinding diagonal r + c = s; $celah = baris yang dibiarkan kosong (0 = tanpa celah)
            $dinding = function (int $h, int $w, int $s, int $celah) use ($fmt) {
                $holes = [];
                for ($r = 1; $r < $s; $r++) {
                    if ($r !== $celah) {
                        $holes[] = $r.' '.($s - $r);
                    }
                }

                return $fmt($h, $w, $holes);
            };
            $B = 100000;

            return [
                "1 1 0\n", "1 6 1\n1 4\n", "100000 1 0\n", $acak(6, 7, 6, 1, 6, 1, 7), $acak(30, 40, 100, 1, 30, 1, 40),
                $acak(200, 200, 2000, 1, 200, 1, 200), "$B $B 0\n", $acak($B, $B, 2000, 1, $B, 1, $B), $rantai($B, $B, 2000),
                $dinding($B, $B, 2001, mt_rand(1, 2000)), $dinding(1500, $B, 1501, 0), $acak($B, $B, 2000, 49971, 50030, 49971, 50030),
                $acak($B, 7, 10, 1, $B, 1, 7),
            ];
        },
        'solve' => function (string $input) use ($MOD, $fakt) {
            $lines = T::lines($input);
            [$h, $w, $k] = T::ints($lines[0]);
            $pts = [];
            for ($i = 1; $i <= $k; $i++) {
                $pts[] = T::ints($lines[$i]);
            }
            usort($pts, fn ($a, $b) => $a <=> $b);
            $pts[] = [$h, $w];
            [$fact, $inv] = $fakt($h + $w);
            $R = array_column($pts, 0);
            $Cc = array_column($pts, 1);
            $m = count($pts);
            $dp = [];
            for ($i = 0; $i < $m; $i++) {
                $ri = $R[$i];
                $ci = $Cc[$i];
                $v = $fact[$ri + $ci - 2] * $inv[$ri - 1] % $MOD * $inv[$ci - 1] % $MOD;
                for ($j = 0; $j < $i; $j++) {
                    $cj = $Cc[$j];
                    if ($cj > $ci) {
                        continue;
                    }
                    $dr = $ri - $R[$j];
                    $dc = $ci - $cj;
                    $v -= $dp[$j] * ($fact[$dr + $dc] * $inv[$dr] % $MOD * $inv[$dc] % $MOD) % $MOD;
                }
                $dp[$i] = ($v % $MOD + $MOD) % $MOD;
            }

            return (string) $dp[$m - 1];
        },
        'starter' => $st("    int h, w, k;\n    cin >> h >> w >> k;\n    vector<pair<int, int>> lubang(k);\n    for (auto& p : lubang) cin >> p.first >> p.second;", "const [h, w, k] = readInts();\nconst lubang = [];\nfor (let i = 0; i < k; i++) lubang.push(readInts()); // [r, c]", "h, w, k = map(int, input().split())\nlubang = [tuple(map(int, input().split())) for _ in range(k)]"),
        'solutions' => [
            'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\n".$cppFakt."\n\n".$cppC.<<<'CODE'


int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int h, w, k;
    cin >> h >> w >> k;
    vector<pair<int, int>> titik(k);
    for (auto& p : titik) cin >> p.first >> p.second;
    // Urutkan lubang berdasarkan (baris, kolom); tujuan (h, w) menjadi titik terakhir.
    sort(titik.begin(), titik.end());
    titik.push_back(make_pair(h, w));
    siapkan(h + w);

    // dp[i] = banyak jalur (1,1) -> titik i yang tidak menginjak lubang lain sebelum titik i
    int m = titik.size();
    vector<long long> dp(m);
    for (int i = 0; i < m; i++) {
        int ri = titik[i].first, ci = titik[i].second;
        long long nilai = C(ri + ci - 2, ri - 1);        // semua jalur ke titik i
        for (int j = 0; j < i; j++) {
            int rj = titik[j].first, cj = titik[j].second;
            if (cj > ci) continue;                       // j tidak berada di kiri-atas i
            // buang jalur yang lubang PERTAMA-nya adalah j
            long long lewatJ = dp[j] * C(ri - rj + ci - cj, ri - rj) % MOD;
            nilai = (nilai - lewatJ + MOD) % MOD;
        }
        dp[i] = nilai;
    }
    cout << dp[m - 1] << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [h, w, k] = readInts();
const titik = [];
for (let i = 0; i < k; i++) titik.push(readInts());
// Urutkan lubang berdasarkan (baris, kolom); tujuan (h, w) menjadi titik terakhir.
titik.sort((a, b) => a[0] - b[0] || a[1] - b[1]);
titik.push([h, w]);


CODE.$jsFakt."\n\n".$jsC.<<<'CODE'


siapkan(h + w);
const m = titik.length;
const bar = new Int32Array(m), kol = new Int32Array(m);
for (let i = 0; i < m; i++) {
  bar[i] = titik[i][0];
  kol[i] = titik[i][1];
}
// dp[i] = jalur (1,1) -> titik i yang tidak menginjak lubang lain sebelum titik i
const dp = new Float64Array(m);
for (let i = 0; i < m; i++) {
  const ri = bar[i], ci = kol[i];
  let nilai = C(ri + ci - 2, ri - 1);
  for (let j = 0; j < i; j++) {
    const cj = kol[j];
    if (cj > ci) continue; // j tidak berada di kiri-atas i
    const rj = bar[j];
    nilai -= mul(dp[j], C(ri - rj + ci - cj, ri - rj)); // lubang pertama = j
    if (nilai < 0) nilai += MOD;
  }
  dp[i] = nilai;
}
console.log(dp[m - 1]);
CODE,
            'python' => $pyHead.<<<'CODE'
def main():
    h, w, k = map(int, input().split())
    # Urutkan lubang berdasarkan (baris, kolom); tujuan (h, w) menjadi titik terakhir.
    titik = sorted(tuple(map(int, input().split())) for _ in range(k))
    titik.append((h, w))
    fact, inv = siapkan(h + w)

    # dp[i] = jalur (1,1) -> titik i yang tidak menginjak lubang lain sebelum titik i
    R, Cc, dp = [], [], []
    for ri, ci in titik:
        nilai = fact[ri + ci - 2] * inv[ri - 1] % MOD * inv[ci - 1] % MOD
        for r, c, d in zip(R, Cc, dp):
            if c <= ci:                 # r <= ri sudah pasti karena terurut
                # buang jalur yang lubang pertamanya (r, c)
                nilai -= d * fact[ri - r + ci - c] % MOD * (inv[ri - r] * inv[ci - c] % MOD)
        dp.append(nilai % MOD)
        R.append(ri)
        Cc.append(ci)
    print(dp[-1])


main()
CODE,
        ],
        'editorial' => '<p>Tanpa lubang, banyak jalur dari (r<sub>1</sub>, c<sub>1</sub>) ke (r<sub>2</sub>, c<sub>2</sub>) dengan r<sub>1</sub> ≤ r<sub>2</sub> dan c<sub>1</sub> ≤ c<sub>2</sub> adalah <code>C(Δr + Δc, Δr)</code>: dari Δr + Δc langkah, pilih mana saja yang ke bawah. Grid 10<sup>5</sup> × 10<sup>5</sup> terlalu besar untuk DP per petak, tetapi lubangnya hanya 2000.</p>
<p>Inklusi–eksklusi langsung atas semua himpunan lubang (seperti contoh kedua) punya 2<sup>K</sup> suku. Triknya: kelompokkan jalur yang "buruk" berdasarkan lubang <strong>pertama</strong> yang diinjak. Urutkan lubang berdasarkan (baris, kolom) dan tambahkan tujuan (H, W) sebagai titik terakhir. Definisikan <code>dp[i]</code> = banyak jalur dari (1, 1) ke titik i yang <em>tidak</em> menginjak lubang lain sebelum sampai di titik i. Setiap jalur buruk ke titik i punya tepat satu lubang pertama j yang berada di kiri-atas i, sehingga</p>
<p style="text-align:center"><code>dp[i] = C(r<sub>i</sub> + c<sub>i</sub> − 2, r<sub>i</sub> − 1) − Σ<sub>j</sub> dp[j] · C(r<sub>i</sub> − r<sub>j</sub> + c<sub>i</sub> − c<sub>j</sub>, r<sub>i</sub> − r<sub>j</sub>)</code></p>
<p>dengan j berjalan atas lubang yang memenuhi r<sub>j</sub> ≤ r<sub>i</sub> dan c<sub>j</sub> ≤ c<sub>i</sub>. Potongan jalur dari j ke i tidak perlu dijaga dari lubang lain, karena yang kita tetapkan hanya bahwa j adalah lubang <em>pertama</em>; setelah itu jalurnya bebas. Berkat pengurutan, semua dp[j] yang dibutuhkan sudah dihitung sebelum dp[i]. Jawabannya adalah dp untuk titik tujuan.</p>
<p>Pada contoh kedua: dp(2, 2) = C(2, 1) = 2, dp(2, 3) = C(3, 1) − 2 · C(1, 0) = 1, dan dp tujuan = C(5, 2) − 2 · C(3, 1) − 1 · C(2, 1) = 10 − 6 − 2 = 2.</p>
<p><strong>Kompleksitas:</strong> faktorial sampai H + W dalam O(H + W), lalu O(K<sup>2</sup>) pasangan dengan C dalam O(1). <strong>Jebakan:</strong> syarat c<sub>j</sub> ≤ c<sub>i</sub> wajib dicek, karena lubang yang muncul lebih dulu di urutan belum tentu berada di kiri titik i; dan tambahkan MOD setelah pengurangan agar hasilnya tidak negatif.</p>',
        'hints' => [
            'Tanpa lubang, banyak jalur dari (1, 1) ke (r, c) adalah C(r + c − 2, r − 1). Grid terlalu besar untuk DP per petak, tetapi lubangnya sedikit.',
            'Kelompokkan jalur yang buruk berdasarkan lubang PERTAMA yang diinjak. Urutkan lubang dan perlakukan tujuan sebagai titik terakhir.',
            'dp[i] = C(jalur ke i) − Σ dp[j] · C(jalur dari j ke i) untuk lubang j di kiri-atas i. Jawabannya dp titik tujuan, total O(K²).',
        ],
    ],
];
