<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan tambahan track Fondasi: kompleksitas (deret harmonik),
 * rekursi & backtracking, binary search (lower_bound, binary search pada jawaban,
 * two pointers / sliding window). Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** "N X" + baris N bilangan acak. */
$arrayInput = function (string $header, int $n, int $lo, int $hi): string {
    $a = [];
    for ($i = 0; $i < $n; $i++) {
        $a[] = mt_rand($lo, $hi);
    }

    return $header."\n".implode(' ', $a)."\n";
};

return [
    // ═════════════════════════════ Kompleksitas ═════════════════════════════
    [
        'slug' => 'pasangan-kelipatan',
        'lesson' => 'kompleksitas',
        'title' => 'Pasangan Kelipatan',
        'difficulty' => 'Sedang',
        'tags' => ['deret harmonik', 'counting', 'kompleksitas'],
        'statement' => '<p>Ada <strong>N</strong> kartu, kartu ke-i bertuliskan bilangan bulat positif <code>a<sub>i</sub></code>. Sepasang kartu (i, j) dengan i &lt; j disebut <strong>pasangan kelipatan</strong> jika salah satu bilangannya habis membagi bilangan yang lain.</p>
<p>Ada berapa pasangan kelipatan?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Banyak pasangan kelipatan.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "6\n2 3 4 6 12 5\n", 'explanation' => '(2, 4), (2, 6), (2, 12), (3, 6), (3, 12), (4, 12), (6, 12). Angka 5 tidak berpasangan dengan siapa pun.'],
            ['input' => "4\n1 1 2 3\n", 'explanation' => 'Kedua kartu 1 berpasangan satu sama lain (1 habis membagi 1), dan masing-masing berpasangan dengan 2 dan 3: 1 + 2 + 2 = 5.'],
        ],
        'tests' => function () use ($arrayInput) {
            $distinct = function (int $n, int $start) {
                $a = range($start, $start + $n - 1);
                T::shuffle($a);

                return "$n\n".implode(' ', $a)."\n";
            };

            return [
                "1\n7\n", "2\n5 5\n", $arrayInput('10', 10, 1, 20), $arrayInput('1000', 1000, 1, 100),
                $arrayInput('200000', 200000, 1, 1000000), $arrayInput('200000', 200000, 1, 1000),
                $distinct(200000, 1), "200000\n".implode(' ', array_fill(0, 200000, 1))."\n",
                $arrayInput('200000', 200000, 999000, 1000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $cnt = [];
            $max = 0;
            foreach (T::ints($lines[1]) as $x) {
                $cnt[$x] = ($cnt[$x] ?? 0) + 1;
                $max = max($max, $x);
            }
            $ans = 0;
            foreach ($cnt as $v => $c) {
                $ans += intdiv($c * ($c - 1), 2);
                $s = 0;
                for ($m = 2 * $v; $m <= $max; $m += $v) {
                    $s += $cnt[$m] ?? 0;
                }
                $ans += $c * $s;
            }

            return (string) $ans;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i];", "const [n] = readInts();\nconst a = readInts();", "n = int(input())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    const int MAXV = 1000000;
    vector<long long> cnt(MAXV + 1, 0);   // cnt[v] = banyak kartu bernilai v
    for (int i = 0; i < n; i++) {
        int x;
        cin >> x;
        cnt[x]++;
    }

    long long ans = 0;
    for (int v = 1; v <= MAXV; v++) {
        if (cnt[v] == 0) continue;
        ans += cnt[v] * (cnt[v] - 1) / 2;         // pasangan dua kartu bernilai sama
        for (int m = 2 * v; m <= MAXV; m += v)    // kelipatan v: MAXV / v langkah
            ans += cnt[v] * cnt[m];
    }
    // Total langkah ≤ MAXV · (1 + 1/2 + 1/3 + ...) ≈ MAXV · ln MAXV ≈ 1,4 · 10^7
    cout << ans << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const a = readInts();
let maxV = 0;
for (const x of a) if (x > maxV) maxV = x;
const cnt = new Int32Array(maxV + 1);
for (const x of a) cnt[x]++;

let ans = 0; // paling banyak ~2 · 10^10: masih aman untuk Number
for (let v = 1; v <= maxV; v++) {
  const c = cnt[v];
  if (c === 0) continue;
  ans += (c * (c - 1)) / 2;
  let s = 0;
  for (let m = 2 * v; m <= maxV; m += v) s += cnt[m];
  ans += c * s;
}
console.log(ans);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
a = list(map(int, input().split()))
max_v = max(a)
cnt = [0] * (max_v + 1)
for x in a:
    cnt[x] += 1

ans = 0
for v in set(a):
    c = cnt[v]
    ans += c * (c - 1) // 2
    # cnt[2v], cnt[3v], ... dijumlahkan lewat slicing (berjalan di C, jauh lebih cepat dari loop)
    ans += c * sum(cnt[2 * v::v])
print(ans)
CODE,
        ],
        'editorial' => '<p>Memeriksa semua pasangan butuh N(N − 1)/2 ≈ 2 · 10<sup>10</sup> langkah: terlalu lambat. Perhatikan bahwa nilainya kecil (≤ 10<sup>6</sup>), jadi kita bisa bekerja per <em>nilai</em>, bukan per kartu.</p>
<p>Hitung <code>cnt[v]</code> = banyak kartu bernilai v. Untuk setiap v yang muncul, pasangan yang bilangan kecilnya v adalah:</p>
<ul><li><code>cnt[v] · (cnt[v] − 1) / 2</code> pasangan dengan dua kartu bernilai v, dan</li><li><code>cnt[v] · cnt[m]</code> untuk setiap kelipatan m = 2v, 3v, … ≤ 10<sup>6</sup>.</li></ul>
<p>Setiap pasangan terhitung tepat sekali (dari bilangan yang lebih kecil). Loop kelipatan v berjalan 10<sup>6</sup>/v kali, sehingga totalnya 10<sup>6</sup> · (1 + ½ + ⅓ + …) ≈ 10<sup>6</sup> · ln 10<sup>6</sup> ≈ 1,4 · 10<sup>7</sup>: <strong>deret harmonik</strong>, O(V log V).</p>
<p><strong>Awas:</strong> jawabannya bisa mencapai ≈ 2 · 10<sup>10</sup> (semua kartu bernilai 1), jadi gunakan <code>long long</code>.</p>',
        'hints' => [
            'Membandingkan semua pasangan kartu butuh sekitar 2 · 10^10 langkah. Batasan apa yang kecil di soal ini?',
            'Kelompokkan kartu berdasarkan nilainya: cnt[v]. Untuk v tertentu, kartu mana saja yang merupakan kelipatannya?',
            'Untuk setiap v, jelajahi m = 2v, 3v, ... dan tambahkan cnt[v] · cnt[m]. Totalnya O(V log V) karena deret harmonik. Jangan lupa pasangan dengan nilai sama.',
        ],
    ],

    // ═════════════════════════════ Rekursi & backtracking ═════════════════════════════
    [
        'slug' => 'menara-hanoi',
        'lesson' => 'rekursi',
        'title' => 'Menara Hanoi',
        'difficulty' => 'Mudah',
        'tags' => ['rekursi'],
        'statement' => '<p>Ada tiga tiang bernomor 1, 2, 3. Di tiang 1 tersusun <strong>N</strong> cakram dengan ukuran berbeda: yang terbesar di bawah, yang terkecil di atas.</p>
<p>Pindahkan seluruh tumpukan ke tiang <strong>3</strong>. Dalam satu langkah kamu mengambil cakram paling atas dari satu tiang dan meletakkannya di atas tiang lain, dan <strong>cakram besar tidak boleh berada di atas cakram yang lebih kecil</strong>.</p>
<p>Cetak cara dengan langkah sesedikit mungkin.</p>',
        'input_format' => '<p>Satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Baris pertama berisi banyak langkah K. K baris berikutnya berisi <code>x y</code>: pindahkan cakram teratas dari tiang x ke tiang y. (Cara dengan langkah paling sedikit hanya ada satu.)</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 16</li></ul>',
        'samples' => [
            ['input' => "2\n", 'explanation' => 'Cakram kecil ke tiang 2, cakram besar ke tiang 3, lalu cakram kecil menyusul ke tiang 3.'],
            ['input' => "3\n", 'explanation' => 'Tiga langkah pertama memindahkan 2 cakram atas ke tiang 2 (memakai tiang 3 sebagai bantuan), lalu cakram terbesar ke tiang 3, lalu 2 cakram tadi dari tiang 2 ke tiang 3.'],
        ],
        'tests' => function () {
            return ["1\n", "4\n", "5\n", "8\n", "10\n", "13\n", "16\n"];
        },
        'solve' => function (string $input) {
            $n = (int) trim($input);
            $moves = [];
            $go = function (int $k, int $from, int $to, int $via) use (&$go, &$moves) {
                if ($k === 0) {
                    return;
                }
                $go($k - 1, $from, $via, $to);
                $moves[] = "$from $to";
                $go($k - 1, $via, $to, $from);
            };
            $go($n, 1, 3, 2);

            return count($moves)."\n".implode("\n", $moves);
        },
        'starter' => $st("    int n;\n    cin >> n;", 'const [n] = readInts();', 'n = int(input())'),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<pair<int, int>> langkah;

// Pindahkan k cakram teratas dari tiang asal ke tiang tujuan, memakai tiang bantu.
void hanoi(int k, int asal, int tujuan, int bantu) {
    if (k == 0) return;                    // base case: tidak ada yang dipindah
    hanoi(k - 1, asal, bantu, tujuan);     // 1. singkirkan k-1 cakram atas ke tiang bantu
    langkah.push_back({asal, tujuan});     // 2. cakram terbesar langsung ke tujuan
    hanoi(k - 1, bantu, tujuan, asal);     // 3. tumpuk lagi k-1 cakram di atasnya
}

int main() {
    int n;
    cin >> n;
    hanoi(n, 1, 3, 2);
    string out = to_string(langkah.size()) + "\n";
    for (auto& p : langkah) out += to_string(p.first) + " " + to_string(p.second) + "\n";
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n] = readInts();
const out = [];

function hanoi(k, asal, tujuan, bantu) {
  if (k === 0) return;
  hanoi(k - 1, asal, bantu, tujuan);
  out.push(asal + " " + tujuan);
  hanoi(k - 1, bantu, tujuan, asal);
}

hanoi(n, 1, 3, 2);
console.log(out.length + "\n" + out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
out = []

def hanoi(k, asal, tujuan, bantu):
    if k == 0:
        return
    hanoi(k - 1, asal, bantu, tujuan)
    out.append(f"{asal} {tujuan}")
    hanoi(k - 1, bantu, tujuan, asal)

hanoi(n, 1, 3, 2)
print(len(out))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Lihat cakram <strong>terbesar</strong>. Ia hanya bisa dipindah ketika semua N − 1 cakram lain sudah tidak ada di atasnya <em>dan</em> tidak ada di tiang tujuan. Artinya ke-N − 1 cakram itu harus berkumpul di tiang ketiga. Dari sini muncul rekursi:</p>
<ol><li>Pindahkan N − 1 cakram atas dari asal ke tiang <em>bantu</em> (soal yang sama, lebih kecil).</li><li>Pindahkan cakram terbesar dari asal ke tujuan.</li><li>Pindahkan N − 1 cakram dari bantu ke tujuan.</li></ol>
<p>Base case: memindahkan 0 cakram tidak butuh langkah. Banyak langkahnya memenuhi <code>T(N) = 2 · T(N − 1) + 1</code>, sehingga <code>T(N) = 2<sup>N</sup> − 1</code>. Karena cakram terbesar memang wajib melewati keadaan di atas, cara ini juga yang paling sedikit.</p>
<p>Kedalaman rekursi hanya N, tetapi banyak langkah 2<sup>N</sup> − 1 = 65 535 untuk N = 16: contoh nyata rekursi eksponensial.</p>',
        'hints' => [
            'Fokus pada cakram terbesar. Di mana semua cakram lain harus berada saat cakram terbesar dipindah?',
            'Buat fungsi hanoi(k, asal, tujuan, bantu) yang memindahkan k cakram teratas.',
            'hanoi(k−1, asal, bantu, tujuan); pindah asal→tujuan; hanoi(k−1, bantu, tujuan, asal). Base case k = 0.',
        ],
    ],

    [
        'slug' => 'paket-menu',
        'lesson' => 'rekursi',
        'title' => 'Paket Menu',
        'difficulty' => 'Mudah',
        'tags' => ['rekursi', 'backtracking', 'kombinasi'],
        'statement' => '<p>Sebuah kafe punya <strong>N</strong> menu bernomor 1 sampai N. Pemiliknya ingin membuat paket berisi tepat <strong>K</strong> menu berbeda dan ingin melihat <strong>semua</strong> kemungkinan paket.</p>
<p>Cetak setiap paket sebagai nomor-nomor menu yang terurut naik. Urutkan paket secara leksikografis (bandingkan nomor pertama, jika sama bandingkan nomor kedua, dan seterusnya).</p>',
        'input_format' => '<p>Satu baris berisi <code>N K</code>.</p>',
        'output_format' => '<p>Setiap paket dalam satu baris: K bilangan dipisah spasi.</p>',
        'constraints' => '<ul><li>1 ≤ K ≤ N ≤ 16</li></ul>',
        'samples' => [
            ['input' => "4 2\n", 'explanation' => 'Ada C(4, 2) = 6 paket.'],
            ['input' => "3 3\n", 'explanation' => 'Hanya satu paket: semua menu.'],
        ],
        'tests' => function () {
            return ["1 1\n", "5 1\n", "5 3\n", "6 6\n", "10 4\n", "16 8\n", "16 3\n", "15 11\n"];
        },
        'solve' => function (string $input) {
            [$n, $k] = T::ints(trim($input));
            $out = [];
            $cur = [];
            $go = function (int $start) use (&$go, &$out, &$cur, $n, $k) {
                if (count($cur) === $k) {
                    $out[] = implode(' ', $cur);

                    return;
                }
                for ($x = $start; $x <= $n - ($k - count($cur)) + 1; $x++) {
                    $cur[] = $x;
                    $go($x + 1);
                    array_pop($cur);
                }
            };
            $go(1);

            return implode("\n", $out);
        },
        'starter' => $st("    int n, k;\n    cin >> n >> k;", 'const [n, k] = readInts();', 'n, k = map(int, input().split())'),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n, k;
vector<int> pilih;   // menu yang sedang dipilih (selalu terurut naik)
string out;

// Pilih menu berikutnya dari nomor 'mulai' ke atas.
void paket(int mulai) {
    if ((int)pilih.size() == k) {                  // base case: paket sudah lengkap
        for (int i = 0; i < k; i++) out += to_string(pilih[i]) + (i + 1 < k ? " " : "\n");
        return;
    }
    int sisa = k - pilih.size();                   // masih perlu sebanyak ini
    for (int x = mulai; x <= n - sisa + 1; x++) {  // pangkas: sisakan cukup menu di kanan
        pilih.push_back(x);                        // pilih
        paket(x + 1);                              // telusuri
        pilih.pop_back();                          // batalkan
    }
}

int main() {
    cin >> n >> k;
    paket(1);
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const pilih = [];
const out = [];

function paket(mulai) {
  if (pilih.length === k) {
    out.push(pilih.join(" "));
    return;
  }
  const sisa = k - pilih.length;
  for (let x = mulai; x <= n - sisa + 1; x++) {
    pilih.push(x);
    paket(x + 1);
    pilih.pop();
  }
}

paket(1);
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
pilih = []
out = []

def paket(mulai):
    if len(pilih) == k:
        out.append(" ".join(map(str, pilih)))
        return
    sisa = k - len(pilih)
    for x in range(mulai, n - sisa + 2):
        pilih.append(x)
        paket(x + 1)
        pilih.pop()

paket(1)
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Gunakan pola backtracking <strong>pilih – telusuri – batalkan</strong>. Keputusan di setiap level: menu berikutnya bernomor berapa. Agar paket selalu terurut naik (dan tidak ada paket ganda), menu berikutnya harus lebih besar dari menu terakhir yang dipilih, jadi kita kirim parameter <code>mulai</code>.</p>
<p>Karena kita mencoba x dari kecil ke besar, paket otomatis keluar dalam urutan leksikografis.</p>
<p><strong>Pemangkasan:</strong> jika masih perlu <code>sisa</code> menu, menu berikutnya paling besar <code>N − sisa + 1</code>. Lebih dari itu, tidak cukup menu tersisa di kanan, jadi cabang tersebut tidak usah dicoba.</p>
<p>Banyak paket adalah C(N, K) ≤ C(16, 8) = 12 870, masing-masing dicetak dalam O(K).</p>',
        'hints' => [
            'Bangun paket satu menu demi satu menu. Keputusan apa yang diambil di setiap level rekursi?',
            'Simpan menu terakhir yang dipilih. Menu berikutnya harus bernomor lebih besar agar tidak ada paket ganda.',
            'Pola: pilih x, panggil rekursi dengan mulai = x + 1, lalu batalkan pilihan x. Cetak saat sudah ada K menu.',
        ],
    ],

    [
        'slug' => 'kartu-angka',
        'lesson' => 'rekursi',
        'title' => 'Kartu Angka',
        'difficulty' => 'Mudah',
        'tags' => ['rekursi', 'himpunan bagian'],
        'statement' => '<p>Dimas memegang <strong>N</strong> kartu. Kartu ke-i bertuliskan bilangan bulat <code>a<sub>i</sub></code> (bisa negatif). Ia ingin memilih <strong>paling sedikit satu</strong> kartu sehingga jumlah angka pada kartu yang dipilih tepat <strong>S</strong>.</p>
<p>Ada berapa cara memilih? Dua cara berbeda jika ada kartu yang dipilih di satu cara tetapi tidak di cara lain (walaupun angkanya sama).</p>',
        'input_format' => '<p>Baris pertama berisi <code>N S</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Banyak cara memilih.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 20</li><li>−10<sup>6</sup> ≤ a<sub>i</sub> ≤ 10<sup>6</sup></li><li>|S| ≤ 2 · 10<sup>7</sup></li></ul>',
        'samples' => [
            ['input' => "4 5\n1 2 3 4\n", 'explanation' => '{1, 4} dan {2, 3}.'],
            ['input' => "3 0\n1 -1 2\n", 'explanation' => 'Hanya {1, −1}. Tidak memilih kartu sama sekali juga berjumlah 0, tetapi tidak dihitung.'],
        ],
        'tests' => function () use ($arrayInput) {
            $withTarget = function (int $n, int $lo, int $hi) {
                $a = [];
                $s = 0;
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand($lo, $hi);
                    if (mt_rand(0, 1)) {
                        $s += $a[$i];
                    }
                }

                return "$n $s\n".implode(' ', $a)."\n";
            };

            return [
                "1 5\n5\n", "1 0\n0\n", "2 0\n0 0\n", $withTarget(8, -5, 5), $withTarget(15, 1, 20),
                $withTarget(20, -3, 3), $withTarget(20, -1000000, 1000000), "20 0\n".implode(' ', array_fill(0, 20, 0))."\n",
                $arrayInput('20 10', 20, 1, 3),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $s] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $sums = [0];
            foreach ($a as $x) {
                $add = [];
                foreach ($sums as $v) {
                    $add[] = $v + $x;
                }
                $sums = array_merge($sums, $add);
            }
            $cnt = 0;
            foreach ($sums as $v) {
                if ($v === $s) {
                    $cnt++;
                }
            }
            if ($s === 0) {
                $cnt--;
            }

            return (string) $cnt;
        },
        'starter' => $st("    int n;\n    long long s;\n    cin >> n >> s;\n    vector<long long> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i];", "const [n, s] = readInts();\nconst a = readInts();", "n, s = map(int, input().split())\na = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
long long S;
vector<long long> a;
long long cara = 0;

// Putuskan kartu ke-i: diambil atau tidak. 'jumlah' = total kartu yang sudah diambil.
void coba(int i, long long jumlah) {
    if (i == n) {                    // semua kartu sudah diputuskan
        if (jumlah == S) cara++;
        return;
    }
    coba(i + 1, jumlah);             // kartu i tidak diambil
    coba(i + 1, jumlah + a[i]);      // kartu i diambil
}

int main() {
    cin >> n >> S;
    a.resize(n);
    for (int i = 0; i < n; i++) cin >> a[i];
    coba(0, 0);
    if (S == 0) cara--;              // pilihan kosong ikut terhitung: buang
    cout << cara << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, S] = readInts();
const a = readInts();
let cara = 0;

function coba(i, jumlah) {
  if (i === n) {
    if (jumlah === S) cara++;
    return;
  }
  coba(i + 1, jumlah);
  coba(i + 1, jumlah + a[i]);
}

coba(0, 0);
if (S === 0) cara--;
console.log(cara);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, S = map(int, input().split())
a = list(map(int, input().split()))

# Pohon rekursi "ambil / tidak ambil" ditelusuri per level:
# setelah memproses kartu ke-i, 'jumlah' berisi total dari SEMUA 2^i pilihan.
# Ini menghindari 2 juta pemanggilan fungsi yang lambat di Python.
jumlah = [0]
for x in a:
    jumlah += [v + x for v in jumlah]

cara = jumlah.count(S)
if S == 0:
    cara -= 1
print(cara)
CODE,
        ],
        'editorial' => '<p>Setiap kartu punya dua pilihan: <strong>diambil</strong> atau <strong>tidak</strong>. Fungsi rekursif <code>coba(i, jumlah)</code> memutuskan kartu ke-i lalu memanggil dirinya untuk kartu berikutnya. Saat i = N, satu pilihan lengkap sudah terbentuk: hitung jika jumlahnya S.</p>
<p>Pohon rekursinya punya 2<sup>N</sup> daun, jadi totalnya O(2<sup>N</sup>) ≈ 10<sup>6</sup> untuk N = 20: masih sangat cepat.</p>
<p><strong>Jebakan:</strong> daun "tidak mengambil apa pun" juga berjumlah 0. Karena soal meminta paling sedikit satu kartu, kurangi 1 ketika S = 0.</p>
<p>Kartu bisa negatif, jadi kita <em>tidak</em> boleh memangkas cabang ketika jumlah sudah melebihi S: menambah kartu negatif bisa menurunkannya lagi.</p>',
        'hints' => [
            'N paling banyak 20. Berapa banyak cara memilih himpunan bagian dari 20 kartu?',
            'Rekursi coba(i, jumlah): kartu i diambil atau tidak, lalu lanjut ke i + 1.',
            'Hitung daun yang jumlahnya S. Jika S = 0, jangan ikut hitung pilihan kosong.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'ratu-berintangan',
        'lesson' => 'rekursi',
        'title' => 'Ratu di Papan Rusak',
        'difficulty' => 'Sedang',
        'tags' => ['backtracking', 'n-queens'],
        'statement' => '<p>Sebuah papan catur berukuran <strong>N × N</strong> sudah tua dan beberapa petaknya rusak. Kamu ingin meletakkan <strong>N</strong> ratu di petak yang <strong>tidak rusak</strong> sehingga tidak ada dua ratu yang saling menyerang (tidak sebaris, tidak sekolom, dan tidak sediagonal).</p>
<p>Petak rusak hanya tidak boleh ditempati; serangan ratu tetap melewatinya. Ada berapa susunan yang mungkin?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya masing-masing berisi N karakter: <code>.</code> untuk petak baik dan <code>#</code> untuk petak rusak.</p>',
        'output_format' => '<p>Banyak susunan.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10</li></ul>',
        'samples' => [
            ['input' => "4\n.#..\n....\n....\n....\n", 'explanation' => 'Papan 4 × 4 punya dua susunan: ratu di kolom (2, 4, 1, 3) dan (3, 1, 4, 2) untuk baris 1 sampai 4. Susunan pertama memakai petak rusak (1, 2), jadi tinggal 1.'],
            ['input' => "3\n...\n...\n...\n", 'explanation' => 'Pada papan 3 × 3 tidak mungkin meletakkan 3 ratu yang aman.'],
        ],
        'tests' => function () {
            $board = function (int $n, float $p) {
                $rows = [];
                for ($i = 0; $i < $n; $i++) {
                    $r = '';
                    for ($j = 0; $j < $n; $j++) {
                        $r .= mt_rand() / mt_getrandmax() < $p ? '#' : '.';
                    }
                    $rows[] = $r;
                }

                return "$n\n".implode("\n", $rows)."\n";
            };

            return ["1\n.\n", "1\n#\n", $board(5, 0), $board(6, 0.1), $board(8, 0), $board(8, 0.1), $board(9, 0.05), $board(10, 0), $board(10, 0.08), $board(10, 0.2)];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $n = (int) $lines[0];
            $g = array_slice($lines, 1, $n);
            $col = array_fill(0, $n, false);
            $d1 = array_fill(0, 2 * $n, false);
            $d2 = array_fill(0, 2 * $n, false);
            $cnt = 0;
            $go = function (int $r) use (&$go, &$col, &$d1, &$d2, &$cnt, $g, $n) {
                if ($r === $n) {
                    $cnt++;

                    return;
                }
                for ($c = 0; $c < $n; $c++) {
                    if ($g[$r][$c] === '#' || $col[$c] || $d1[$r + $c] || $d2[$r - $c + $n]) {
                        continue;
                    }
                    $col[$c] = $d1[$r + $c] = $d2[$r - $c + $n] = true;
                    $go($r + 1);
                    $col[$c] = $d1[$r + $c] = $d2[$r - $c + $n] = false;
                }
            };
            $go(0);

            return (string) $cnt;
        },
        'starter' => $st("    int n;\n    cin >> n;\n    vector<string> g(n);\n    for (int i = 0; i < n; i++) cin >> g[i];", "const n = Number(readLine());\nconst g = [];\nfor (let i = 0; i < n; i++) g.push(readLine().trim());", "n = int(input())\ng = [input().strip() for _ in range(n)]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n;
vector<string> g;
bool kolom[10], diag1[20], diag2[20];   // diag1: r + c sama, diag2: r - c sama
long long susunan = 0;

void taruh(int r) {                      // letakkan ratu untuk baris r
    if (r == n) {                        // semua baris terisi
        susunan++;
        return;
    }
    for (int c = 0; c < n; c++) {
        if (g[r][c] == '#') continue;                              // petak rusak
        if (kolom[c] || diag1[r + c] || diag2[r - c + n]) continue; // diserang
        kolom[c] = diag1[r + c] = diag2[r - c + n] = true;         // pilih
        taruh(r + 1);                                              // telusuri
        kolom[c] = diag1[r + c] = diag2[r - c + n] = false;        // batalkan
    }
}

int main() {
    cin >> n;
    g.resize(n);
    for (int i = 0; i < n; i++) cin >> g[i];
    taruh(0);
    cout << susunan << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const g = [];
for (let i = 0; i < n; i++) g.push(readLine().trim());
const kolom = new Array(n).fill(false);
const diag1 = new Array(2 * n).fill(false);
const diag2 = new Array(2 * n).fill(false);
let susunan = 0;

function taruh(r) {
  if (r === n) {
    susunan++;
    return;
  }
  for (let c = 0; c < n; c++) {
    if (g[r][c] === "#") continue;
    if (kolom[c] || diag1[r + c] || diag2[r - c + n]) continue;
    kolom[c] = diag1[r + c] = diag2[r - c + n] = true;
    taruh(r + 1);
    kolom[c] = diag1[r + c] = diag2[r - c + n] = false;
  }
}

taruh(0);
console.log(susunan);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
g = [input().strip() for _ in range(n)]
kolom = [False] * n
diag1 = [False] * (2 * n)
diag2 = [False] * (2 * n)
susunan = 0

def taruh(r):
    global susunan
    if r == n:
        susunan += 1
        return
    for c in range(n):
        if g[r][c] == "#" or kolom[c] or diag1[r + c] or diag2[r - c + n]:
            continue
        kolom[c] = diag1[r + c] = diag2[r - c + n] = True
        taruh(r + 1)
        kolom[c] = diag1[r + c] = diag2[r - c + n] = False

taruh(0)
print(susunan)
CODE,
        ],
        'editorial' => '<p>Ini N-Queens dengan satu syarat tambahan. Karena setiap baris pasti berisi tepat satu ratu, keputusan di level r adalah: <strong>ratu baris r di kolom mana?</strong></p>
<p>Simpan tiga penanda agar pemeriksaan serangan O(1): <code>kolom[c]</code>, <code>diag1[r + c]</code> (diagonal "/" punya r + c yang sama), dan <code>diag2[r − c + N]</code> (diagonal "\" punya r − c yang sama). Lewati kolom yang petaknya rusak atau sedang diserang, pilih, telusuri baris berikutnya, lalu batalkan.</p>
<p>Petak rusak tidak memblokir serangan, jadi penandanya tetap sama seperti N-Queens biasa. Untuk N = 10 pohon pencariannya hanya berisi puluhan ribu simpul karena pemangkasan bekerja sangat awal.</p>',
        'hints' => [
            'Setiap baris berisi tepat satu ratu. Jadikan baris sebagai level rekursi.',
            'Simpan kolom dan dua arah diagonal yang sudah terpakai. Diagonal dikenali dari r + c dan r − c.',
            'Di level r, coba setiap kolom c yang petaknya baik dan tidak diserang: pilih, rekursi ke r + 1, batalkan.',
        ],
        'sample_visual' => 'board',
    ],

    // ═════════════════════════════ Binary search ═════════════════════════════
    [
        'slug' => 'akar-bulat',
        'lesson' => 'binary-search',
        'title' => 'Akar Bulat',
        'difficulty' => 'Mudah',
        'tags' => ['binary search', 'overflow'],
        'statement' => '<p>Untuk setiap bilangan <code>N</code>, tentukan bilangan bulat <code>x</code> terbesar sehingga <code>x · x ≤ N</code>. Dengan kata lain, akar kuadrat N yang dibulatkan ke bawah.</p>
<p>Hati-hati: fungsi akar bawaan bekerja dengan bilangan pecahan (floating point) dan bisa meleset satu untuk N yang sangat besar.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi satu bilangan <code>N</code>.</p>',
        'output_format' => '<p>Q baris, masing-masing akar bulat dari N.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 100 000</li><li>0 ≤ N ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "4\n10\n16\n0\n999999999999999999\n", 'explanation' => '3 · 3 = 9 ≤ 10 tetapi 4 · 4 = 16 > 10. Akar 16 tepat 4. Untuk 10<sup>18</sup> − 1 jawabannya 999 999 999, karena 10<sup>9</sup> · 10<sup>9</sup> sudah melebihi.'],
        ],
        'tests' => function () {
            $mk = function (int $q, callable $gen) {
                $out = [(string) $q];
                for ($i = 0; $i < $q; $i++) {
                    $out[] = (string) $gen();
                }

                return implode("\n", $out)."\n";
            };
            $big = function () {
                $s = (string) mt_rand(1, 9);
                $len = mt_rand(1, 18);
                for ($i = 1; $i < $len; $i++) {
                    $s .= mt_rand(0, 9);
                }

                return $s;
            };
            $nearSquare = function () {
                $x = mt_rand(1, 1000000000);

                return $x * $x + mt_rand(-1, 0);
            };

            return [
                "3\n0\n1\n2\n", $mk(50, fn () => mt_rand(0, 1000)), $mk(100000, $big), $mk(100000, $nearSquare),
                $mk(100000, fn () => 1000000000000000000 - mt_rand(0, 3)), "2\n1000000000000000000\n999999998000000001\n",
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            $q = (int) $lines[0];
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $n = (int) $lines[$i];
                $lo = 0;
                $hi = 1000000001;
                while ($hi - $lo > 1) {
                    $mid = intdiv($lo + $hi, 2);
                    if ($mid * $mid <= $n) {
                        $lo = $mid;
                    } else {
                        $hi = $mid;
                    }
                }
                $out[] = $lo;
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int q;\n    cin >> q;\n    while (q--) {\n        long long n;\n        cin >> n;\n        // cetak akar bulat n\n    }", "const q = Number(readLine());\nconst out = [];\nfor (let i = 0; i < q; i++) {\n  const n = BigInt(readLine().trim()); // sampai 10^18: pakai BigInt\n  // out.push(...)\n}", "q = int(input())\nout = []\nfor _ in range(q):\n    n = int(input())\n    # out.append(...)"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    string out;
    while (q--) {
        long long n;
        cin >> n;
        // Invarian: lo * lo <= n, hi * hi > n.
        // (10^9 + 1)^2 ≈ 10^18 + 2·10^9 masih muat di long long (≤ 9,2 · 10^18).
        long long lo = 0, hi = 1000000001;
        while (hi - lo > 1) {
            long long mid = (lo + hi) / 2;
            if (mid * mid <= n) lo = mid;
            else hi = mid;
        }
        out += to_string(lo) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const n = BigInt(readLine().trim()); // 10^18 tidak bisa disimpan tepat oleh Number
  // lo dan hi cukup Number (≤ 10^9 + 1); hanya perkaliannya yang memakai BigInt
  let lo = 0, hi = 1000000001;
  while (hi - lo > 1) {
    const mid = Math.floor((lo + hi) / 2);
    const m = BigInt(mid);
    if (m * m <= n) lo = mid;
    else hi = mid;
  }
  out.push(lo);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n = int(input())
    lo, hi = 0, 10**9 + 1          # lo*lo <= n < hi*hi
    while hi - lo > 1:
        mid = (lo + hi) // 2
        if mid * mid <= n:
            lo = mid
        else:
            hi = mid
    out.append(lo)
# (Python juga punya math.isqrt(n) yang sudah tepat.)
print("\n".join(map(str, out)))
CODE,
        ],
        'editorial' => '<p>Fungsi <code>sqrt</code> memakai <code>double</code> yang hanya punya sekitar 15–16 digit presisi. Untuk N mendekati 10<sup>18</sup>, hasilnya bisa meleset satu. Cara aman: <strong>binary search pada jawaban</strong>.</p>
<p>Syarat <code>x · x ≤ N</code> bersifat monoton: jika benar untuk x, benar juga untuk semua yang lebih kecil. Pertahankan invarian <code>lo · lo ≤ N &lt; hi · hi</code> dengan awal lo = 0 dan hi = 10<sup>9</sup> + 1. Setiap langkah ambil mid; jika mid · mid ≤ N geser lo, jika tidak geser hi. Saat hi − lo = 1, jawabannya lo.</p>
<p>Batas atas dipilih hati-hati agar <code>mid · mid</code> tidak overflow: (10<sup>9</sup> + 1)<sup>2</sup> masih jauh di bawah batas <code>long long</code> (≈ 9,2 · 10<sup>18</sup>). Setiap pertanyaan butuh ≈ 30 langkah, total O(Q log N).</p>',
        'hints' => [
            'Jika x memenuhi x · x ≤ N, apakah x − 1 juga memenuhi? Sifat apa ini?',
            'Binary search x di antara 0 dan 10^9 + 1 dengan invarian lo·lo ≤ N < hi·hi.',
            'Pakai long long (C++) atau BigInt (JavaScript) untuk N. Batas atas 10^9 + 1 membuat mid·mid tidak overflow.',
        ],
    ],

    [
        'slug' => 'kapasitas-truk',
        'lesson' => 'binary-search',
        'title' => 'Kapasitas Truk',
        'difficulty' => 'Sedang',
        'tags' => ['binary search pada jawaban', 'greedy'],
        'statement' => '<p>Sebuah gudang harus mengirim <strong>N</strong> paket yang sudah berbaris. Paket ke-i beratnya <code>w<sub>i</sub></code> kg. Setiap hari satu truk berangkat sekali, dan paket harus dimuat <strong>sesuai urutan barisan</strong>: truk mengangkut beberapa paket terdepan yang tersisa selama total beratnya tidak melebihi kapasitas truk.</p>
<p>Semua paket harus terkirim dalam paling lama <strong>D</strong> hari. Berapa kapasitas truk <strong>terkecil</strong> yang cukup?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N D</code>. Baris kedua berisi <code>w<sub>1</sub> … w<sub>N</sub></code>.</p>',
        'output_format' => '<p>Kapasitas truk minimum.</p>',
        'constraints' => '<ul><li>1 ≤ D ≤ N ≤ 100 000</li><li>1 ≤ w<sub>i</sub> ≤ 100 000</li></ul>',
        'samples' => [
            ['input' => "5 3\n3 2 2 4 1\n", 'explanation' => 'Dengan kapasitas 5: hari 1 [3, 2], hari 2 [2], hari 3 [4, 1]. Dengan kapasitas 4 butuh 4 hari: [3], [2, 2], [4], [1].'],
            ['input' => "10 5\n1 2 3 4 5 6 7 8 9 10\n", 'explanation' => '[1, 2, 3, 4, 5], [6, 7], [8], [9], [10]: muatan terberat 15.'],
        ],
        'tests' => function () use ($arrayInput) {
            return [
                "1 1\n7\n", "3 3\n5 1 5\n", "3 1\n5 1 5\n", $arrayInput('10 4', 10, 1, 10), $arrayInput('1000 37', 1000, 1, 1000),
                $arrayInput('100000 1', 100000, 1, 100000), $arrayInput('100000 100000', 100000, 1, 100000),
                $arrayInput('100000 777', 100000, 1, 100000), $arrayInput('100000 50000', 100000, 99990, 100000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $d] = T::ints($lines[0]);
            $w = T::ints($lines[1]);
            $hari = function (int $cap) use ($w) {
                $h = 1;
                $muat = 0;
                foreach ($w as $x) {
                    if ($muat + $x > $cap) {
                        $h++;
                        $muat = 0;
                    }
                    $muat += $x;
                }

                return $h;
            };
            $lo = max($w);
            $hi = array_sum($w);
            while ($lo < $hi) {
                $mid = intdiv($lo + $hi, 2);
                if ($hari($mid) <= $d) {
                    $hi = $mid;
                } else {
                    $lo = $mid + 1;
                }
            }

            return (string) $lo;
        },
        'starter' => $st("    int n, d;\n    cin >> n >> d;\n    vector<long long> w(n);\n    for (int i = 0; i < n; i++) cin >> w[i];", "const [n, d] = readInts();\nconst w = readInts();", "n, d = map(int, input().split())\nw = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n, d;
vector<long long> w;

// Berapa hari yang dibutuhkan jika kapasitas truk = cap? (cap >= paket terberat)
int butuhHari(long long cap) {
    int hari = 1;
    long long muat = 0;
    for (int i = 0; i < n; i++) {
        if (muat + w[i] > cap) {   // paket ini tidak muat: berangkatkan truk, mulai hari baru
            hari++;
            muat = 0;
        }
        muat += w[i];
    }
    return hari;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> d;
    w.resize(n);
    long long lo = 0, hi = 0;
    for (int i = 0; i < n; i++) {
        cin >> w[i];
        lo = max(lo, w[i]);   // kapasitas minimal: paket terberat harus muat
        hi += w[i];           // kapasitas = total berat pasti cukup (1 hari); bisa 10^10
    }
    // Cari cap terkecil dengan butuhHari(cap) <= d. Sifatnya monoton:
    // kapasitas lebih besar tidak pernah butuh hari lebih banyak.
    while (lo < hi) {
        long long mid = (lo + hi) / 2;
        if (butuhHari(mid) <= d) hi = mid;   // cukup: jawaban <= mid
        else lo = mid + 1;                   // kurang: jawaban > mid
    }
    cout << lo << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, d] = readInts();
const w = readInts();

function butuhHari(cap) {
  let hari = 1, muat = 0;
  for (let i = 0; i < n; i++) {
    if (muat + w[i] > cap) {
      hari++;
      muat = 0;
    }
    muat += w[i];
  }
  return hari;
}

let lo = 0, hi = 0;
for (const x of w) {
  if (x > lo) lo = x;
  hi += x;
}
while (lo < hi) {
  const mid = Math.floor((lo + hi) / 2);
  if (butuhHari(mid) <= d) hi = mid;
  else lo = mid + 1;
}
console.log(lo);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, d = map(int, input().split())
w = list(map(int, input().split()))

def butuh_hari(cap):
    hari, muat = 1, 0
    for x in w:
        if muat + x > cap:
            hari += 1
            muat = 0
        muat += x
    return hari

lo, hi = max(w), sum(w)
while lo < hi:
    mid = (lo + hi) // 2
    if butuh_hari(mid) <= d:
        hi = mid
    else:
        lo = mid + 1
print(lo)
CODE,
        ],
        'editorial' => '<p>Jika kapasitas truk <code>C</code> diketahui, menghitung banyak hari mudah dengan <strong>greedy</strong>: muat paket berurutan selama masih muat, dan berangkatkan truk begitu paket berikutnya tidak muat. Ini O(N).</p>
<p>Kuncinya sifat <strong>monoton</strong>: kapasitas yang lebih besar tidak pernah membutuhkan hari lebih banyak. Jadi ada batas: semua C di bawahnya butuh lebih dari D hari, semua C di atasnya cukup. Binary search batas itu di rentang:</p>
<ul><li><code>lo = max(w)</code>: paket terberat harus muat sendirian.</li><li><code>hi = Σ w</code>: semua paket terangkut dalam satu hari.</li></ul>
<p>Total O(N log Σw) ≈ 10<sup>5</sup> · 34. <strong>Awas:</strong> Σw bisa mencapai 10<sup>10</sup>, jadi pakai <code>long long</code>.</p>',
        'hints' => [
            'Seandainya kapasitas truk sudah ditentukan, bagaimana cara menghitung banyak hari yang dibutuhkan?',
            'Greedy: muat paket berurutan sampai tidak muat, lalu mulai hari baru. Apakah kapasitas lebih besar bisa butuh hari lebih banyak?',
            'Binary search kapasitas di [max(w), jumlah w]: jika butuhHari(mid) ≤ D maka hi = mid, jika tidak lo = mid + 1.',
        ],
        'sample_visual' => 'bars',
    ],

    [
        'slug' => 'jarak-kandang',
        'lesson' => 'binary-search',
        'title' => 'Kandang Ayam',
        'difficulty' => 'Sedang',
        'tags' => ['binary search pada jawaban', 'greedy', 'sorting'],
        'statement' => '<p>Pak Tani punya <strong>N</strong> kandang yang berjajar di satu garis lurus; kandang ke-i berada di posisi <code>x<sub>i</sub></code>. Ia ingin memasukkan <strong>K</strong> ayam jago, masing-masing di kandang yang berbeda.</p>
<p>Ayam jago suka berkelahi jika terlalu dekat, jadi Pak Tani ingin <strong>jarak terdekat</strong> antara dua ayam sebesar mungkin. Berapa nilai terbesar jarak terdekat itu?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N K</code>. Baris kedua berisi <code>x<sub>1</sub> … x<sub>N</sub></code> (tidak harus terurut, semuanya berbeda).</p>',
        'output_format' => '<p>Jarak terdekat terbesar yang mungkin.</p>',
        'constraints' => '<ul><li>2 ≤ K ≤ N ≤ 100 000</li><li>0 ≤ x<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "5 3\n1 2 8 4 9\n", 'explanation' => 'Taruh ayam di posisi 1, 4, dan 8 (atau 9): jarak terdekatnya 3. Jarak 4 tidak mungkin.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $k, int $max) {
                $set = [];
                while (count($set) < $n) {
                    $set[mt_rand(0, $max)] = true;
                }
                $x = array_keys($set);
                T::shuffle($x);

                return "$n $k\n".implode(' ', $x)."\n";
            };

            return [
                "2 2\n0 1000000000\n", "3 2\n5 1 3\n", $mk(10, 3, 30), $mk(1000, 10, 100000), $mk(100000, 2, 1000000000),
                $mk(100000, 100000, 1000000000), $mk(100000, 500, 1000000000), $mk(100000, 30000, 200000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $k] = T::ints($lines[0]);
            $x = T::ints($lines[1]);
            sort($x);
            $muat = function (int $d) use ($x, $k) {
                $cnt = 1;
                $last = $x[0];
                foreach ($x as $v) {
                    if ($v - $last >= $d) {
                        $cnt++;
                        $last = $v;
                    }
                }

                return $cnt >= $k;
            };
            $lo = 1;
            $hi = $x[count($x) - 1] - $x[0] + 1;
            while ($hi - $lo > 1) {
                $mid = intdiv($lo + $hi, 2);
                if ($muat($mid)) {
                    $lo = $mid;
                } else {
                    $hi = $mid;
                }
            }

            return (string) $lo;
        },
        'starter' => $st("    int n, k;\n    cin >> n >> k;\n    vector<long long> x(n);\n    for (int i = 0; i < n; i++) cin >> x[i];", "const [n, k] = readInts();\nconst x = readInts();", "n, k = map(int, input().split())\nx = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int n, k;
vector<long long> x;

// Bisakah K ayam ditaruh dengan jarak antar ayam minimal d? Greedy dari kiri.
bool bisa(long long d) {
    int ayam = 1;
    long long terakhir = x[0];          // ayam pertama selalu di kandang paling kiri
    for (int i = 1; i < n; i++) {
        if (x[i] - terakhir >= d) {     // cukup jauh: taruh ayam di sini
            ayam++;
            terakhir = x[i];
        }
    }
    return ayam >= k;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> n >> k;
    x.resize(n);
    for (int i = 0; i < n; i++) cin >> x[i];
    sort(x.begin(), x.end());

    // Invarian: bisa(lo) benar, bisa(hi) salah.
    long long lo = 1, hi = x[n - 1] - x[0] + 1;
    while (hi - lo > 1) {
        long long mid = (lo + hi) / 2;
        if (bisa(mid)) lo = mid;
        else hi = mid;
    }
    cout << lo << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, k] = readInts();
const x = readInts();
x.sort((p, q) => p - q);

function bisa(d) {
  let ayam = 1, terakhir = x[0];
  for (let i = 1; i < n; i++) {
    if (x[i] - terakhir >= d) {
      ayam++;
      terakhir = x[i];
    }
  }
  return ayam >= k;
}

let lo = 1, hi = x[n - 1] - x[0] + 1;
while (hi - lo > 1) {
  const mid = Math.floor((lo + hi) / 2);
  if (bisa(mid)) lo = mid;
  else hi = mid;
}
console.log(lo);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, k = map(int, input().split())
x = sorted(map(int, input().split()))

def bisa(d):
    ayam, terakhir = 1, x[0]
    for v in x:
        if v - terakhir >= d:
            ayam += 1
            terakhir = v
    return ayam >= k

lo, hi = 1, x[-1] - x[0] + 1
while hi - lo > 1:
    mid = (lo + hi) // 2
    if bisa(mid):
        lo = mid
    else:
        hi = mid
print(lo)
CODE,
        ],
        'editorial' => '<p>Ubah pertanyaannya: <em>"Bisakah jarak terdekat paling sedikit d?"</em> Untuk d tertentu, jawabannya bisa dicek dengan <strong>greedy</strong> setelah posisi diurutkan: taruh ayam pertama di kandang paling kiri, lalu taruh ayam berikutnya di kandang pertama yang berjarak ≥ d dari ayam terakhir. Menaruh ayam sekiri mungkin tidak pernah merugikan, karena menyisakan ruang paling banyak di kanan.</p>
<p>Sifatnya <strong>monoton</strong>: jika jarak d bisa dicapai, jarak yang lebih kecil juga bisa. Jadi binary search d terbesar yang masih bisa, dengan invarian <code>bisa(lo)</code> benar (d = 1 selalu bisa karena posisi berbeda) dan <code>bisa(hi)</code> salah (lebih dari rentang posisi tidak mungkin untuk K ≥ 2).</p>
<p>Total O(N log N) untuk sorting + O(N log X) untuk binary search.</p>',
        'hints' => [
            'Mencari nilai maksimum secara langsung sulit. Bagaimana jika pertanyaannya "apakah jarak terdekat ≥ d bisa dicapai?"',
            'Urutkan posisi. Untuk d tertentu, taruh ayam serakah dari kiri: setiap kali ada kandang yang berjarak ≥ d dari ayam terakhir, isi.',
            'Jika d bisa dicapai, semua jarak yang lebih kecil juga bisa. Binary search d di [1, x_max − x_min].',
        ],
    ],

    [
        'slug' => 'jendela-terpendek',
        'lesson' => 'binary-search',
        'title' => 'Target Donasi',
        'difficulty' => 'Sedang',
        'tags' => ['two pointers', 'sliding window'],
        'statement' => '<p>Sebuah kampanye donasi berlangsung <strong>N</strong> hari, dan pada hari ke-i terkumpul <code>a<sub>i</sub></code> rupiah (selalu positif). Panitia ingin mencari <strong>rentang hari berurutan terpendek</strong> yang total donasinya paling sedikit <strong>S</strong> rupiah, untuk dijadikan contoh di laporan.</p>
<p>Berapa panjang rentang terpendek itu? Jika tidak ada rentang yang mencapai S, cetak <code>-1</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N S</code>. Baris kedua berisi <code>a<sub>1</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>Panjang rentang terpendek, atau <code>-1</code>.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li><li>1 ≤ S ≤ 10<sup>15</sup></li></ul>',
        'samples' => [
            ['input' => "6 7\n2 3 1 2 4 3\n", 'explanation' => 'Hari ke-5 dan ke-6: 4 + 3 = 7. Tidak ada satu hari pun yang mencapai 7.'],
            ['input' => "3 100\n1 2 3\n", 'explanation' => 'Total semua hari hanya 6.'],
        ],
        'tests' => function () use ($arrayInput) {
            return [
                "1 5\n5\n", "1 6\n5\n", $arrayInput('10 15', 10, 1, 10), $arrayInput('1000 5000', 1000, 1, 100),
                $arrayInput('200000 1000000000000', 200000, 1, 1000000000), $arrayInput('200000 1000000000000000', 200000, 1, 1000000000),
                $arrayInput('200000 1000000000', 200000, 1, 1000000000), $arrayInput('200000 150000', 200000, 1, 3),
                $arrayInput('200000 199999000000000', 200000, 999999999, 1000000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $s] = T::ints($lines[0]);
            $a = T::ints($lines[1]);
            $best = PHP_INT_MAX;
            $sum = 0;
            $l = 0;
            for ($r = 0; $r < $n; $r++) {
                $sum += $a[$r];
                while ($sum - $a[$l] >= $s) {
                    $sum -= $a[$l];
                    $l++;
                }
                if ($sum >= $s) {
                    $best = min($best, $r - $l + 1);
                }
            }

            return (string) ($best === PHP_INT_MAX ? -1 : $best);
        },
        'starter' => $st("    int n;\n    long long s;\n    cin >> n >> s;\n    vector<long long> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i];", "const [n, s] = readInts();\nconst a = readInts();", "n, s = map(int, input().split())\na = list(map(int, input().split()))"),
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
    for (int i = 0; i < n; i++) cin >> a[i];

    int terbaik = INT_MAX;
    long long jumlah = 0;   // jumlah jendela [l, r]; bisa sampai 2 · 10^14
    int l = 0;
    for (int r = 0; r < n; r++) {
        jumlah += a[r];                         // perlebar ke kanan
        while (jumlah - a[l] >= s) {            // buang kiri selama masih mencapai s
            jumlah -= a[l];
            l++;
        }
        if (jumlah >= s) terbaik = min(terbaik, r - l + 1);
    }
    // Setiap indeks masuk dan keluar jendela paling banyak sekali: O(N).
    cout << (terbaik == INT_MAX ? -1 : terbaik) << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, s] = readInts(); // s ≤ 10^15 dan jumlah ≤ 2 · 10^14: aman untuk Number
const a = readInts();
let terbaik = Infinity;
let jumlah = 0;
let l = 0;
for (let r = 0; r < n; r++) {
  jumlah += a[r];
  while (jumlah - a[l] >= s) {
    jumlah -= a[l];
    l++;
  }
  if (jumlah >= s) terbaik = Math.min(terbaik, r - l + 1);
}
console.log(terbaik === Infinity ? -1 : terbaik);
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, s = map(int, input().split())
a = list(map(int, input().split()))
terbaik = n + 1
jumlah = 0
l = 0
for r in range(n):
    jumlah += a[r]
    while jumlah - a[l] >= s:
        jumlah -= a[l]
        l += 1
    if jumlah >= s and r - l + 1 < terbaik:
        terbaik = r - l + 1
print(-1 if terbaik == n + 1 else terbaik)
CODE,
        ],
        'editorial' => '<p>Mencoba semua rentang [l, r] butuh O(N<sup>2</sup>) = 4 · 10<sup>10</sup>. Karena semua donasi <strong>positif</strong>, kita bisa memakai <strong>sliding window</strong>:</p>
<ul><li>Perlebar jendela ke kanan satu hari setiap langkah (r bertambah).</li><li>Selama membuang hari paling kiri tetap membuat jumlah ≥ S, buang (l bertambah). Jendela sekarang adalah rentang terpendek yang berakhir di r.</li><li>Jika jumlahnya ≥ S, perbarui jawaban dengan r − l + 1.</li></ul>
<p>Mengapa l tidak perlu mundur? Untuk r yang lebih besar, rentang terpendek berakhir di r pasti dimulai di l yang sama atau lebih ke kanan, karena semua angka positif. Setiap indeks masuk dan keluar jendela paling banyak sekali, jadi totalnya <strong>O(N)</strong>, walaupun ada <code>while</code> di dalam <code>for</code>.</p>
<p>Alternatif: prefix sum + binary search (lower_bound) untuk setiap r, O(N log N). Sliding window tidak bekerja jika ada angka negatif.</p>',
        'hints' => [
            'Semua angka positif. Jika rentang [l, r] sudah mencapai S, apa yang terjadi bila kita memperpanjangnya?',
            'Gunakan dua penunjuk l dan r. Tambahkan a[r], lalu geser l ke kanan selama jumlah tanpa a[l] masih ≥ S.',
            'Setiap langkah, jika jumlah ≥ S, kandidat jawaban adalah r − l + 1. Pakai long long untuk jumlah.',
        ],
        'sample_visual' => 'bars',
    ],
];
