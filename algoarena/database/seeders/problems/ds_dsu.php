<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Disjoint Set Union: find dengan path compression, union by size,
 * menghitung komponen & ukurannya, pemrosesan offline terbalik, dan DSU berbobot.
 * Semua kode C++ harus lolos C++14. Fungsi cari di JavaScript dan Python ditulis iteratif.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Cari akar x secara iteratif sambil memadatkan jalur (untuk solusi referensi PHP). */
$cari = function (array &$induk, int $x): int {
    $akar = $x;
    while ($induk[$akar] !== $akar) {
        $akar = $induk[$akar];
    }
    while ($induk[$x] !== $akar) {
        $berikut = $induk[$x];
        $induk[$x] = $akar;
        $x = $berikut;
    }

    return $akar;
};

/**
 * Dua simpul berbeda secara acak. Jika $g > 0, biasanya keduanya punya sisa bagi $g yang sama
 * (satu "kelompok" tersembunyi) agar banyak pertanyaan punya jawaban yang menarik.
 */
$pasangan = function (int $n, int $g): array {
    $a = mt_rand(1, $n);
    if ($g > 0 && mt_rand(1, 10) <= 9) {
        $kMin = -intdiv($a - 1, $g);
        $kMax = intdiv($n - $a, $g);
        if ($kMin < $kMax || $kMin < 0) {
            do {
                $k = mt_rand($kMin, $kMax);
            } while ($k === 0);

            return [$a, $a + $k * $g];
        }
    }
    do {
        $b = mt_rand(1, $n);
    } while ($b === $a);

    return [$a, $b];
};

/** Kode C++ fungsi cari (iteratif, path compression) yang dipakai beberapa solusi. */
$cariCpp = <<<'CODE'
vector<int> induk, ukuran;

// Cari akar kelompok x, lalu arahkan semua simpul di jalur langsung ke akar (path compression).
int cari(int x) {
    int akar = x;
    while (induk[akar] != akar) akar = induk[akar];
    while (induk[x] != akar) {
        int berikut = induk[x];
        induk[x] = akar;
        x = berikut;
    }
    return akar;
}
CODE;

$cariJs = <<<'CODE'
const induk = new Int32Array(n + 1);
const ukuran = new Int32Array(n + 1).fill(1);
for (let i = 0; i <= n; i++) induk[i] = i;

// Cari akar secara iteratif + path compression (tanpa rekursi)
function cari(x) {
  let akar = x;
  while (induk[akar] !== akar) akar = induk[akar];
  while (induk[x] !== akar) {
    const berikut = induk[x];
    induk[x] = akar;
    x = berikut;
  }
  return akar;
}
CODE;

$cariPy = <<<'CODE'
induk = list(range(n + 1))
ukuran = [1] * (n + 1)


def cari(x):
    # naik sampai akar, lalu arahkan semua simpul di jalur langsung ke akar
    akar = x
    while induk[akar] != akar:
        akar = induk[akar]
    while induk[x] != akar:
        berikut = induk[x]
        induk[x] = akar
        x = berikut
    return akar
CODE;

return [
    [
        'slug' => 'jaringan-lab-komputer',
        'lesson' => 'dsu',
        'title' => 'Jaringan Lab Komputer',
        'difficulty' => 'Mudah',
        'tags' => ['dsu', 'union by size', 'komponen'],
        'statement' => '<p>Lab komputer sekolah punya <strong>N</strong> komputer bernomor 1 sampai N. Awalnya belum ada kabel sama sekali. Selama seminggu, teknisi mencatat <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 a b</code>: sebuah kabel dipasang antara komputer a dan komputer b. Kabel bekerja dua arah.</li>
<li><code>2 a b</code>: seorang siswa bertanya, apakah komputer a bisa mengirim pesan ke komputer b? Pesan boleh lewat kabel langsung atau diteruskan oleh komputer lain.</li>
<li><code>3 a</code>: berapa banyak komputer yang bisa menerima pesan dari komputer a, termasuk a sendiri?</li></ul>
<p>Jawab setiap kejadian jenis 2 dan 3.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Untuk setiap kejadian jenis 2, cetak <code>YA</code> atau <code>TIDAK</code>. Untuk setiap kejadian jenis 3, cetak sebuah bilangan. Satu jawaban per baris, sesuai urutan kejadian.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 200 000</li><li>1 ≤ Q ≤ 200 000</li><li>1 ≤ a, b ≤ N dan a ≠ b</li><li>Kabel antara dua komputer yang sama boleh dipasang lebih dari sekali.</li><li>Ada setidaknya satu kejadian jenis 2 atau 3.</li></ul>',
        'samples' => [
            ['input' => "6 9\n2 1 2\n1 1 2\n1 3 4\n3 1\n1 2 3\n2 1 4\n3 4\n2 5 6\n3 6\n", 'explanation' => 'Awalnya belum ada kabel, jadi 1 dan 2 tidak terhubung (TIDAK). Setelah kabel 1–2 dan 3–4, komputer 1 hanya menjangkau {1, 2} (2 komputer). Setelah kabel 2–3, komputer 1, 2, 3, 4 saling terhubung: pesan dari 1 sampai ke 4 lewat 1 – 2 – 3 – 4 (YA), dan dari 4 terjangkau 4 komputer. Komputer 5 dan 6 belum pernah dipasangi kabel (TIDAK, dan komputer 6 hanya menjangkau dirinya sendiri).'],
        ],
        'tests' => function () use ($pasangan) {
            $mk = function (int $n, int $q, int $p1, int $p2, int $g) use ($pasangan): string {
                $out = ["$n $q"];
                for ($i = 0; $i < $q; $i++) {
                    $r = $n === 1 ? 100 : mt_rand(1, 100);
                    if ($i === $q - 1) {
                        $r = max($r, $p1 + 1); // kejadian terakhir selalu pertanyaan
                    }
                    if ($r <= $p1 + $p2) {
                        [$a, $b] = $pasangan($n, $g);
                        $out[] = ($r <= $p1 ? 1 : 2)." $a $b";
                    } else {
                        $out[] = '3 '.mt_rand(1, $n);
                    }
                }

                return implode("\n", $out)."\n";
            };
            // Rantai panjang 1 - 2 - 3 - ... lalu banyak pertanyaan dari ujungnya:
            // tanpa path compression dan union by size, cari() bisa berjalan sangat jauh.
            $rantai = function (int $n, int $sambung, int $tanya): string {
                $out = [$n.' '.($sambung + $tanya)];
                for ($i = 1; $i <= $sambung; $i++) {
                    $out[] = "1 $i ".($i + 1);
                }
                for ($i = 0; $i < $tanya; $i++) {
                    $out[] = $i % 2 ? '3 '.mt_rand(1, 3) : '2 1 '.mt_rand(2, $n);
                }

                return implode("\n", $out)."\n";
            };

            return [
                "1 1\n3 1\n",
                "2 3\n2 1 2\n1 2 1\n2 1 2\n",
                "3 5\n3 2\n1 1 2\n1 2 1\n3 2\n2 3 1\n",
                $mk(10, 30, 40, 30, 3),
                $mk(100, 500, 30, 35, 5),
                $mk(2000, 5000, 20, 40, 40),
                $mk(200000, 200000, 50, 25, 0),
                $mk(200000, 200000, 60, 20, 4000),
                $mk(200000, 200000, 85, 10, 50),
                $rantai(200000, 150000, 50000),
                $mk(50, 60000, 5, 50, 0),
            ];
        },
        'solve' => function (string $input) use ($cari) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $induk = range(0, $n);
            $ukuran = array_fill(0, $n + 1, 1);
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $k = T::ints($lines[$i]);
                if ($k[0] === 3) {
                    $out[] = $ukuran[$cari($induk, $k[1])];
                    continue;
                }
                $a = $cari($induk, $k[1]);
                $b = $cari($induk, $k[2]);
                if ($k[0] === 2) {
                    $out[] = $a === $b ? 'YA' : 'TIDAK';
                } elseif ($a !== $b) {
                    if ($ukuran[$a] < $ukuran[$b]) {
                        [$a, $b] = [$b, $a];
                    }
                    $induk[$b] = $a;
                    $ukuran[$a] += $ukuran[$b];
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<array<int, 3>> kejadian(q);   // {jenis, a, b}; b = 0 untuk jenis 3\n    for (auto& k : kejadian) {\n        cin >> k[0] >> k[1];\n        k[2] = 0;\n        if (k[0] != 3) cin >> k[2];\n    }", "const [n, q] = readInts();\nconst kejadian = [];\nfor (let i = 0; i < q; i++) kejadian.push(readInts()); // [jenis, a, b] atau [3, a]", "n, q = map(int, input().split())\nkejadian = [list(map(int, input().split())) for _ in range(q)]  # [jenis, a, b] atau [3, a]"),
        'solutions' => [
            'cpp' => <<<CODE
#include <bits/stdc++.h>
using namespace std;

{$cariCpp}

// Gabungkan dua kelompok: akar kelompok kecil menunjuk ke akar kelompok besar (union by size).
void gabung(int a, int b) {
    a = cari(a);
    b = cari(b);
    if (a == b) return;
    if (ukuran[a] < ukuran[b]) swap(a, b);
    induk[b] = a;
    ukuran[a] += ukuran[b];
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    induk.resize(n + 1);
    ukuran.assign(n + 1, 1);
    for (int i = 0; i <= n; i++) induk[i] = i;

    string out;
    for (int i = 0; i < q; i++) {
        int t, a;
        cin >> t >> a;
        if (t == 3) {
            // ukuran[] hanya benar di akar, jadi cari akarnya dulu
            out += to_string(ukuran[cari(a)]) + "\\n";
        } else {
            int b;
            cin >> b;
            if (t == 1) gabung(a, b);
            else out += cari(a) == cari(b) ? "YA\\n" : "TIDAK\\n";
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<CODE
const [n, q] = readInts();
{$cariJs}

const out = [];
for (let i = 0; i < q; i++) {
  const [t, a, b] = readInts();
  if (t === 1) {
    let x = cari(a), y = cari(b);
    if (x !== y) {
      if (ukuran[x] < ukuran[y]) { const tmp = x; x = y; y = tmp; }
      induk[y] = x; // union by size: kelompok kecil masuk ke kelompok besar
      ukuran[x] += ukuran[y];
    }
  } else if (t === 2) {
    out.push(cari(a) === cari(b) ? "YA" : "TIDAK");
  } else {
    out.push(ukuran[cari(a)]); // ukuran hanya benar di akar
  }
}
console.log(out.join("\\n"));
CODE,
            'python' => <<<CODE
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
{$cariPy}


out = []
for _ in range(q):
    k = input().split()
    if k[0] == "1":
        a = cari(int(k[1]))
        b = cari(int(k[2]))
        if a != b:
            if ukuran[a] < ukuran[b]:
                a, b = b, a
            induk[b] = a          # akar kelompok kecil menunjuk ke akar kelompok besar
            ukuran[a] += ukuran[b]
    elif k[0] == "2":
        out.append("YA" if cari(int(k[1])) == cari(int(k[2])) else "TIDAK")
    else:
        out.append(str(ukuran[cari(int(k[1]))]))   # ukuran hanya benar di akar
print("\\n".join(out))
CODE,
        ],
        'editorial' => '<p>Komputer-komputer yang saling terhubung membentuk <strong>kelompok</strong>, dan kelompok hanya pernah <em>bergabung</em>, tidak pernah pecah. Ini pekerjaan yang pas untuk <strong>Disjoint Set Union</strong>.</p>
<ul><li>Setiap kelompok diwakili satu komputer <strong>akar</strong>. Simpan <code>induk[x]</code>; akar adalah komputer dengan <code>induk[x] = x</code>.</li>
<li><code>cari(x)</code> naik mengikuti induk sampai akar. Dengan <strong>path compression</strong>, semua komputer yang dilewati langsung diarahkan ke akar sehingga pencarian berikutnya cepat.</li>
<li>Kejadian jenis 1: cari akar a dan b. Jika berbeda, tempelkan akar kelompok yang <strong>lebih kecil</strong> ke akar kelompok yang lebih besar (<strong>union by size</strong>) dan jumlahkan ukurannya di akar baru.</li>
<li>Jenis 2: jawabannya YA tepat ketika <code>cari(a) = cari(b)</code>.</li>
<li>Jenis 3: jawabannya <code>ukuran[cari(a)]</code>.</li></ul>
<p>Dengan kedua optimasi, setiap operasi berjalan hampir O(1) (tepatnya O(α(N))), jadi total O(N + Q · α(N)).</p>
<p><strong>Jebakan:</strong> <code>ukuran[a]</code> hanya benar jika a adalah akar. Setelah a ditempel ke kelompok lain, nilai <code>ukuran[a]</code> tidak diperbarui lagi, jadi selalu baca ukuran di akarnya. Menjawab setiap pertanyaan dengan BFS/DFS butuh O(Q · (N + Q)) dan terlalu lambat.</p>',
        'hints' => [
            'Kelompok komputer hanya pernah bergabung dan tidak pernah pecah. Struktur data apa yang cocok untuk itu?',
            'Wakili setiap kelompok dengan satu akar. Dua komputer terhubung jika akarnya sama. Simpan juga ukuran kelompok di akarnya.',
            'Saat menggabungkan, tempelkan akar kelompok kecil ke akar kelompok besar dan tambahkan ukurannya. Jenis 3 dijawab dengan ukuran[cari(a)].',
        ],
    ],

    [
        'slug' => 'laporan-pembangunan-jalan',
        'lesson' => 'dsu',
        'title' => 'Laporan Pembangunan Jalan',
        'difficulty' => 'Sedang',
        'tags' => ['dsu', 'komponen', 'simulasi'],
        'statement' => '<p>Sebuah provinsi baru memiliki <strong>N</strong> kota bernomor 1 sampai N dan belum punya jalan sama sekali. Pemerintah membangun <strong>M</strong> jalan dua arah satu per satu; jalan ke-i menghubungkan kota u<sub>i</sub> dan v<sub>i</sub>.</p>
<p>Sekumpulan kota yang saling bisa dicapai (langsung atau lewat kota lain) disebut satu <strong>wilayah</strong>. Kota yang belum tersambung ke mana pun membentuk wilayahnya sendiri.</p>
<p>Setiap kali sebuah jalan selesai dibangun, gubernur meminta laporan: ada berapa wilayah sekarang, dan berapa banyak kota di wilayah terbesar?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M</code>. M baris berikutnya berisi <code>u<sub>i</sub> v<sub>i</sub></code>, sesuai urutan pembangunan.</p>',
        'output_format' => '<p>M baris. Baris ke-i berisi dua bilangan: banyak wilayah dan banyak kota di wilayah terbesar setelah jalan ke-i selesai dibangun.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>1 ≤ M ≤ 50 000</li><li>1 ≤ u<sub>i</sub>, v<sub>i</sub> ≤ N dan u<sub>i</sub> ≠ v<sub>i</sub></li><li>Dua kota boleh dihubungkan oleh lebih dari satu jalan.</li></ul>',
        'samples' => [
            ['input' => "6 5\n1 2\n3 4\n2 3\n1 4\n5 6\n", 'explanation' => 'Setelah jalan 1–2: wilayah {1, 2}, {3}, {4}, {5}, {6}. Setelah 3–4: {1, 2}, {3, 4}, {5}, {6}. Setelah 2–3: {1, 2, 3, 4}, {5}, {6}. Jalan 1–4 menghubungkan dua kota yang sudah satu wilayah, jadi laporannya tidak berubah. Terakhir 5–6 membuat 2 wilayah dengan wilayah terbesar tetap 4 kota.'],
        ],
        'tests' => function () {
            $acak = function (int $n, int $m, int $batas): string {
                // $batas: jalan hanya dibangun di antara kota 1..$batas
                $e = [];
                for ($i = 0; $i < $m; $i++) {
                    $u = mt_rand(1, $batas);
                    do {
                        $v = mt_rand(1, $batas);
                    } while ($v === $u);
                    $e[] = [$u, $v];
                }

                return T::graphInput("$n $m", $e);
            };
            $jalur = function (int $n): string {
                // jalan i - (i+1) dibangun dalam urutan acak, nomor kota diacak
                $p = range(1, $n);
                T::shuffle($p);
                $e = [];
                for ($i = 1; $i < $n; $i++) {
                    $e[] = mt_rand(0, 1) ? [$p[$i - 1], $p[$i]] : [$p[$i], $p[$i - 1]];
                }
                T::shuffle($e);

                return T::graphInput("$n ".($n - 1), $e);
            };
            $bintang = function (int $n): string {
                $c = mt_rand(1, $n);
                $e = [];
                for ($i = 1; $i <= $n; $i++) {
                    if ($i !== $c) {
                        $e[] = mt_rand(0, 1) ? [$c, $i] : [$i, $c];
                    }
                }
                T::shuffle($e);

                return T::graphInput("$n ".($n - 1), $e);
            };

            return [
                "2 1\n1 2\n",
                "3 3\n1 2\n2 1\n1 2\n",
                "5 4\n1 2\n3 4\n2 4\n5 1\n",
                $acak(10, 12, 10),
                $acak(200, 300, 200),
                $acak(5000, 5000, 5000),
                $acak(100000, 50000, 100000),
                $jalur(50001),
                $acak(50000, 50000, 50000),
                $acak(100000, 20000, 300),
                $bintang(20001),
                $acak(2, 3000, 2),
            ];
        },
        'solve' => function (string $input) use ($cari) {
            $lines = T::lines($input);
            [$n, $m] = T::ints($lines[0]);
            $induk = range(0, $n);
            $ukuran = array_fill(0, $n + 1, 1);
            $wilayah = $n;
            $terbesar = 1;
            $out = [];
            for ($i = 1; $i <= $m; $i++) {
                [$u, $v] = T::ints($lines[$i]);
                $a = $cari($induk, $u);
                $b = $cari($induk, $v);
                if ($a !== $b) {
                    if ($ukuran[$a] < $ukuran[$b]) {
                        [$a, $b] = [$b, $a];
                    }
                    $induk[$b] = $a;
                    $ukuran[$a] += $ukuran[$b];
                    $wilayah--;
                    $terbesar = max($terbesar, $ukuran[$a]);
                }
                $out[] = "$wilayah $terbesar";
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, m;\n    cin >> n >> m;\n    vector<int> u(m), v(m);\n    for (int i = 0; i < m; i++) cin >> u[i] >> v[i];", "const [n, m] = readInts();\nconst jalan = [];\nfor (let i = 0; i < m; i++) jalan.push(readInts());", "n, m = map(int, input().split())\njalan = [tuple(map(int, input().split())) for _ in range(m)]"),
        'solutions' => [
            'cpp' => <<<CODE
#include <bits/stdc++.h>
using namespace std;

{$cariCpp}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    induk.resize(n + 1);
    ukuran.assign(n + 1, 1);
    for (int i = 0; i <= n; i++) induk[i] = i;

    int wilayah = n;     // awalnya setiap kota adalah wilayah sendiri
    int terbesar = 1;    // wilayah tidak pernah pecah, jadi nilai ini hanya bisa naik
    string out;
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        int a = cari(u), b = cari(v);
        if (a != b) {
            if (ukuran[a] < ukuran[b]) swap(a, b);
            induk[b] = a;
            ukuran[a] += ukuran[b];
            wilayah--;                          // dua wilayah menjadi satu
            terbesar = max(terbesar, ukuran[a]);
        }
        out += to_string(wilayah) + " " + to_string(terbesar) + "\\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<CODE
const [n, m] = readInts();
{$cariJs}

let wilayah = n;   // awalnya setiap kota adalah wilayah sendiri
let terbesar = 1;  // hanya bisa naik karena wilayah tidak pernah pecah
const out = [];
for (let i = 0; i < m; i++) {
  const [u, v] = readInts();
  let a = cari(u), b = cari(v);
  if (a !== b) {
    if (ukuran[a] < ukuran[b]) { const tmp = a; a = b; b = tmp; }
    induk[b] = a;
    ukuran[a] += ukuran[b];
    wilayah--;
    if (ukuran[a] > terbesar) terbesar = ukuran[a];
  }
  out.push(wilayah + " " + terbesar);
}
console.log(out.join("\\n"));
CODE,
            'python' => <<<CODE
import sys
input = sys.stdin.readline

n, m = map(int, input().split())
{$cariPy}


wilayah = n      # awalnya setiap kota adalah wilayah sendiri
terbesar = 1     # hanya bisa naik karena wilayah tidak pernah pecah
out = []
for _ in range(m):
    u, v = map(int, input().split())
    a = cari(u)
    b = cari(v)
    if a != b:
        if ukuran[a] < ukuran[b]:
            a, b = b, a
        induk[b] = a
        ukuran[a] += ukuran[b]
        wilayah -= 1
        if ukuran[a] > terbesar:
            terbesar = ukuran[a]
    out.append(f"{wilayah} {terbesar}")
print("\\n".join(out))
CODE,
        ],
        'editorial' => '<p>Wilayah = komponen terhubung. Karena jalan hanya ditambah, wilayah hanya bisa <strong>bergabung</strong>; DSU dengan union by size menangani ini.</p>
<ul><li><strong>Banyak wilayah.</strong> Mulai dari N. Setiap jalan yang menghubungkan dua akar <em>berbeda</em> menggabungkan dua wilayah menjadi satu, jadi banyak wilayah berkurang 1. Jalan yang kedua ujungnya sudah satu wilayah (termasuk jalan ganda) tidak mengubah apa pun.</li>
<li><strong>Wilayah terbesar.</strong> Ukuran setiap wilayah disimpan di akarnya. Wilayah tidak pernah pecah, sehingga ukuran terbesar <em>tidak pernah turun</em>: cukup simpan satu variabel <code>terbesar</code> dan perbarui dengan ukuran wilayah hasil gabungan setiap kali terjadi penggabungan.</li></ul>
<p>Total O(N + M · α(N)). Menghitung ulang komponen dengan BFS setelah setiap jalan butuh O(M · (N + M)) ≈ 7,5 · 10<sup>9</sup> langkah, terlalu lambat.</p>
<p><strong>Jebakan:</strong> jangan mengurangi banyak wilayah ketika <code>cari(u) = cari(v)</code>. Pada contoh, jalan 1–4 adalah jalan seperti itu.</p>',
        'hints' => [
            'Jalan hanya ditambah, tidak pernah dihapus. Bagaimana banyak wilayah berubah ketika sebuah jalan menghubungkan dua wilayah yang berbeda? Bagaimana jika kedua kotanya sudah satu wilayah?',
            'Banyak wilayah = N − (banyak penggabungan yang berhasil). Simpan ukuran setiap wilayah di akar DSU.',
            'Wilayah tidak pernah pecah, jadi ukuran wilayah terbesar hanya bisa naik: setelah setiap penggabungan, bandingkan dengan ukuran wilayah yang baru terbentuk.',
        ],
        'sample_visual' => 'graph',
    ],

    [
        'slug' => 'badai-kepulauan',
        'lesson' => 'dsu',
        'title' => 'Badai di Kepulauan',
        'difficulty' => 'Sedang',
        'tags' => ['dsu', 'offline', 'proses terbalik'],
        'statement' => '<p>Sebuah kepulauan terdiri dari <strong>N</strong> pulau yang dihubungkan oleh <strong>M</strong> jembatan dua arah; jembatan ke-i menghubungkan pulau u<sub>i</sub> dan v<sub>i</sub>. Bisa saja ada lebih dari satu jembatan di antara dua pulau yang sama.</p>
<p>Badai besar datang dan meruntuhkan <strong>Q</strong> jembatan satu per satu: pertama jembatan nomor e<sub>1</sub>, lalu e<sub>2</sub>, dan seterusnya. Setiap kali sebuah jembatan runtuh, tim SAR ingin tahu: ada berapa <strong>pasangan pulau</strong> {x, y} dengan x &lt; y yang masih bisa saling dicapai lewat jembatan yang tersisa?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N M Q</code>. M baris berikutnya berisi <code>u<sub>i</sub> v<sub>i</sub></code>; jembatan dinomori 1 sampai M sesuai urutan ini. Baris terakhir berisi Q bilangan <code>e<sub>1</sub> e<sub>2</sub> … e<sub>Q</sub></code>, nomor jembatan yang runtuh sesuai urutan.</p>',
        'output_format' => '<p>Q baris. Baris ke-k berisi banyak pasangan pulau yang masih saling terhubung setelah jembatan e<sub>1</sub>, …, e<sub>k</sub> runtuh.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>1 ≤ M ≤ 100 000</li><li>1 ≤ Q ≤ min(M, 50 000)</li><li>1 ≤ u<sub>i</sub>, v<sub>i</sub> ≤ N dan u<sub>i</sub> ≠ v<sub>i</sub></li><li>1 ≤ e<sub>k</sub> ≤ M dan semua e<sub>k</sub> berbeda</li></ul>',
        'samples' => [
            ['input' => "5 6 4\n1 2\n2 3\n3 1\n3 4\n4 5\n1 2\n1 4 3 5\n", 'explanation' => 'Jembatan 1 dan 6 sama-sama menghubungkan pulau 1 dan 2. Runtuhnya jembatan 1 tidak memutus apa pun: kelima pulau tetap terhubung, 10 pasangan. Runtuhnya jembatan 4 (3–4) memisahkan {1, 2, 3} dan {4, 5}: 3 + 1 = 4 pasangan. Jembatan 3 (3–1) runtuh, tetapi 1 – 2 – 3 masih tersambung lewat jembatan 6 dan 2: tetap 4. Terakhir jembatan 5 (4–5) runtuh: tinggal {1, 2, 3}, yaitu 3 pasangan.'],
            ['input' => "4 2 2\n1 2\n3 4\n2 1\n", 'explanation' => 'Setelah jembatan 3–4 runtuh, hanya pasangan {1, 2} yang terhubung. Setelah jembatan 1–2 juga runtuh, tidak ada pasangan yang terhubung.'],
        ],
        'tests' => function () {
            $mk = function (int $n, int $m, int $q, bool $pohon, int $ganda): string {
                $e = [];
                if ($pohon) {
                    // pohon merentang acak dulu agar ada wilayah raksasa (jawaban melewati batas int)
                    $p = range(1, $n);
                    T::shuffle($p);
                    for ($i = 1; $i < $n && count($e) < $m; $i++) {
                        $e[] = [$p[mt_rand(0, $i - 1)], $p[$i]];
                    }
                }
                while (count($e) < $m) {
                    if ($ganda > 0 && count($e) > 0 && mt_rand(1, 100) <= $ganda) {
                        $x = $e[mt_rand(0, count($e) - 1)];
                        $e[] = [$x[1], $x[0]]; // jembatan ganda
                    } else {
                        $u = mt_rand(1, $n);
                        do {
                            $v = mt_rand(1, $n);
                        } while ($v === $u);
                        $e[] = [$u, $v];
                    }
                }
                T::shuffle($e);
                $idx = range(1, $m);
                T::shuffle($idx);
                $idx = array_slice($idx, 0, $q);

                return T::graphInput("$n $m $q", $e).implode(' ', $idx)."\n";
            };

            return [
                "2 1 1\n1 2\n1\n",
                "3 3 3\n1 2\n1 2\n2 3\n1 3 2\n",
                $mk(8, 10, 10, false, 20),
                $mk(50, 70, 30, true, 10),
                $mk(1000, 1500, 1500, true, 5),
                $mk(100000, 100000, 50000, true, 0),
                $mk(100000, 100000, 40000, false, 0),
                $mk(50000, 100000, 25000, true, 50),
                $mk(100000, 30000, 30000, false, 0),
                $mk(2000, 100000, 8000, false, 30),
            ];
        },
        'solve' => function (string $input) use ($cari) {
            $lines = T::lines($input);
            [$n, $m, $q] = T::ints($lines[0]);
            $u = [0];
            $v = [0];
            for ($i = 1; $i <= $m; $i++) {
                [$a, $b] = T::ints($lines[$i]);
                $u[] = $a;
                $v[] = $b;
            }
            $e = T::ints($lines[$m + 1]);
            $runtuh = array_fill(0, $m + 1, false);
            foreach ($e as $x) {
                $runtuh[$x] = true;
            }
            $induk = range(0, $n);
            $ukuran = array_fill(0, $n + 1, 1);
            $pasangan = 0;
            $tambah = function (int $x, int $y) use (&$induk, &$ukuran, &$pasangan, $cari) {
                $a = $cari($induk, $x);
                $b = $cari($induk, $y);
                if ($a === $b) {
                    return;
                }
                if ($ukuran[$a] < $ukuran[$b]) {
                    [$a, $b] = [$b, $a];
                }
                $pasangan += $ukuran[$a] * $ukuran[$b];
                $induk[$b] = $a;
                $ukuran[$a] += $ukuran[$b];
            };
            for ($i = 1; $i <= $m; $i++) {
                if (! $runtuh[$i]) {
                    $tambah($u[$i], $v[$i]);
                }
            }
            $jawab = array_fill(0, $q, 0);
            for ($k = $q - 1; $k >= 0; $k--) {
                $jawab[$k] = $pasangan;
                $tambah($u[$e[$k]], $v[$e[$k]]);
            }

            return implode("\n", $jawab);
        },
        'starter' => $st("    int n, m, q;\n    cin >> n >> m >> q;\n    vector<int> u(m + 1), v(m + 1);\n    for (int i = 1; i <= m; i++) cin >> u[i] >> v[i];\n    vector<int> e(q);\n    for (auto& x : e) cin >> x;", "const [n, m, q] = readInts();\nconst u = [0], v = [0];\nfor (let i = 0; i < m; i++) {\n  const [a, b] = readInts();\n  u.push(a);\n  v.push(b);\n}\nconst e = readInts();", "n, m, q = map(int, input().split())\nu = [0] * (m + 1)\nv = [0] * (m + 1)\nfor i in range(1, m + 1):\n    u[i], v[i] = map(int, input().split())\ne = list(map(int, input().split()))"),
        'solutions' => [
            'cpp' => <<<CODE
#include <bits/stdc++.h>
using namespace std;

{$cariCpp}

long long pasangan = 0;   // banyak pasangan pulau yang saling terhubung (bisa ~5 · 10^9)

void tambahJembatan(int x, int y) {
    int a = cari(x), b = cari(y);
    if (a == b) return;
    if (ukuran[a] < ukuran[b]) swap(a, b);
    // setiap pulau di wilayah a kini terhubung dengan setiap pulau di wilayah b
    pasangan += (long long)ukuran[a] * ukuran[b];
    induk[b] = a;
    ukuran[a] += ukuran[b];
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, q;
    cin >> n >> m >> q;
    vector<int> u(m + 1), v(m + 1);
    for (int i = 1; i <= m; i++) cin >> u[i] >> v[i];
    vector<int> e(q);
    vector<bool> runtuh(m + 1, false);
    for (int k = 0; k < q; k++) {
        cin >> e[k];
        runtuh[e[k]] = true;
    }

    induk.resize(n + 1);
    ukuran.assign(n + 1, 1);
    for (int i = 0; i <= n; i++) induk[i] = i;

    // Mulai dari keadaan paling akhir: hanya jembatan yang tidak pernah runtuh.
    for (int i = 1; i <= m; i++)
        if (!runtuh[i]) tambahJembatan(u[i], v[i]);

    // Putar waktu mundur: catat jawaban ke-k, lalu "bangun kembali" jembatan e[k].
    vector<long long> jawab(q);
    for (int k = q - 1; k >= 0; k--) {
        jawab[k] = pasangan;
        tambahJembatan(u[e[k]], v[e[k]]);
    }

    string out;
    for (int k = 0; k < q; k++) out += to_string(jawab[k]) + "\\n";
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<CODE
const [n, m, q] = readInts();
const u = new Int32Array(m + 1), v = new Int32Array(m + 1);
for (let i = 1; i <= m; i++) {
  const [a, b] = readInts();
  u[i] = a;
  v[i] = b;
}
const e = readInts();
const runtuh = new Uint8Array(m + 1);
for (const x of e) runtuh[x] = 1;

{$cariJs}

let pasangan = 0; // paling besar ~5 · 10^9, masih aman untuk Number
function tambahJembatan(x, y) {
  let a = cari(x), b = cari(y);
  if (a === b) return;
  if (ukuran[a] < ukuran[b]) { const tmp = a; a = b; b = tmp; }
  pasangan += ukuran[a] * ukuran[b];
  induk[b] = a;
  ukuran[a] += ukuran[b];
}

// Keadaan akhir: hanya jembatan yang tidak pernah runtuh
for (let i = 1; i <= m; i++) if (!runtuh[i]) tambahJembatan(u[i], v[i]);

// Mundur: catat jawaban, lalu bangun kembali jembatan yang runtuh pada langkah itu
const jawab = new Array(q);
for (let k = q - 1; k >= 0; k--) {
  jawab[k] = pasangan;
  tambahJembatan(u[e[k]], v[e[k]]);
}
console.log(jawab.join("\\n"));
CODE,
            'python' => <<<CODE
import sys
input = sys.stdin.readline

n, m, q = map(int, input().split())
u = [0] * (m + 1)
v = [0] * (m + 1)
for i in range(1, m + 1):
    u[i], v[i] = map(int, input().split())
e = list(map(int, input().split()))
runtuh = [False] * (m + 1)
for x in e:
    runtuh[x] = True

{$cariPy}


pasangan = 0


def tambah_jembatan(x, y):
    global pasangan
    a = cari(x)
    b = cari(y)
    if a == b:
        return
    if ukuran[a] < ukuran[b]:
        a, b = b, a
    pasangan += ukuran[a] * ukuran[b]   # semua pasangan lintas dua wilayah kini terhubung
    induk[b] = a
    ukuran[a] += ukuran[b]


# Keadaan akhir: hanya jembatan yang tidak pernah runtuh
for i in range(1, m + 1):
    if not runtuh[i]:
        tambah_jembatan(u[i], v[i])

# Mundur: catat jawaban, lalu bangun kembali jembatan yang runtuh pada langkah itu
jawab = [0] * q
for k in range(q - 1, -1, -1):
    jawab[k] = pasangan
    tambah_jembatan(u[e[k]], v[e[k]])
print("\\n".join(map(str, jawab)))
CODE,
        ],
        'editorial' => '<p>DSU pandai <strong>menggabungkan</strong> kelompok, tetapi tidak bisa <strong>memisahkannya</strong>. Kunci soal ini: semua runtuhan sudah diketahui sejak awal, jadi kita boleh memprosesnya <strong>offline</strong> dan <strong>terbalik</strong>. Jika film runtuhnya jembatan diputar mundur, yang terlihat adalah jembatan yang <em>dibangun kembali</em> satu per satu, dan itu pekerjaan DSU.</p>
<ol><li>Tandai jembatan e<sub>1</sub>, …, e<sub>Q</sub>. Gabungkan semua jembatan yang <em>tidak</em> pernah runtuh. Inilah keadaan setelah runtuhan ke-Q, jadi jawaban ke-Q adalah nilai saat ini.</li>
<li>Untuk k = Q, Q − 1, …, 1: catat jawaban ke-k, lalu tambahkan kembali jembatan e<sub>k</sub>. Setelah e<sub>k</sub> ditambahkan, keadaannya sama dengan setelah runtuhan ke-(k − 1).</li></ol>
<p>Banyak pasangan terhubung adalah jumlah <code>s(s − 1)/2</code> untuk setiap wilayah berukuran s. Tidak perlu menghitung ulang: saat dua wilayah berukuran s<sub>a</sub> dan s<sub>b</sub> bergabung, tepat <code>s<sub>a</sub> · s<sub>b</sub></code> pasangan baru menjadi terhubung. Total O(N + (M + Q) · α(N)).</p>
<p><strong>Jebakan:</strong> jawaban bisa mencapai C(10<sup>5</sup>, 2) ≈ 5 · 10<sup>9</sup>, melebihi batas <code>int</code>. Bahkan <code>s<sub>a</sub> · s<sub>b</sub></code> bisa 2,5 · 10<sup>9</sup>, jadi lakukan perkalian dalam <code>long long</code>. Ingat juga jembatan ganda: runtuhnya satu jembatan belum tentu memutus apa pun, dan pendekatan terbalik menangani ini dengan sendirinya.</p>',
        'hints' => [
            'Menghapus sisi itu sulit, tetapi menambah sisi itu mudah dengan DSU. Semua runtuhan sudah diketahui sejak awal. Bagaimana jika kejadiannya diproses dari belakang?',
            'Mulailah dari keadaan setelah semua Q jembatan runtuh, lalu tambahkan kembali jembatan e_Q, e_(Q−1), … satu per satu. Catat jawaban sebelum setiap penambahan.',
            'Saat wilayah berukuran a dan b bergabung, banyak pasangan terhubung bertambah a · b. Simpan totalnya dalam long long.',
        ],
        'sample_visual' => 'graph',
    ],

    [
        'slug' => 'catatan-harga-antik',
        'lesson' => 'dsu',
        'title' => 'Catatan Harga Barang Antik',
        'difficulty' => 'Sulit',
        'tags' => ['dsu', 'dsu berbobot', 'konsistensi'],
        'statement' => '<p>Pak Darto, pedagang barang antik, punya <strong>N</strong> barang bernomor 1 sampai N yang harganya belum ia ketahui. Para pegawainya menaksir harga dengan membandingkan dua barang sekaligus. Pak Darto mencatat <strong>Q</strong> kejadian secara berurutan:</p>
<ul><li><code>1 a b d</code>: seorang pegawai melapor bahwa barang a lebih mahal tepat <strong>d</strong> rupiah daripada barang b, yaitu harga<sub>a</sub> − harga<sub>b</sub> = d. Jika laporan ini masih mungkin benar bersama semua laporan yang sudah <em>diterima</em> sebelumnya, laporan itu diterima dan kamu mencetak <code>OK</code>. Jika tidak mungkin, laporan itu ditolak (tidak dipakai lagi) dan kamu mencetak <code>SALAH</code>.</li>
<li><code>2 a b</code>: Pak Darto bertanya berapa harga<sub>a</sub> − harga<sub>b</sub>. Jika nilainya sudah pasti berdasarkan laporan yang diterima, cetak nilai tersebut (bisa nol atau negatif). Jika belum bisa dipastikan, cetak <code>TIDAK TAHU</code>.</li></ul>',
        'input_format' => '<p>Baris pertama berisi <code>N Q</code>. Q baris berikutnya masing-masing berisi satu kejadian dengan format di atas.</p>',
        'output_format' => '<p>Q baris: satu jawaban untuk setiap kejadian, sesuai urutan.</p>',
        'constraints' => '<ul><li>2 ≤ N ≤ 100 000</li><li>1 ≤ Q ≤ 100 000</li><li>1 ≤ a, b ≤ N dan a ≠ b</li><li>0 ≤ d ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4 8\n2 1 2\n1 1 2 5\n1 3 2 2\n2 1 3\n2 3 1\n1 1 3 4\n1 1 3 3\n2 4 1\n", 'explanation' => 'Awalnya belum ada laporan (TIDAK TAHU). Dari harga<sub>1</sub> − harga<sub>2</sub> = 5 dan harga<sub>3</sub> − harga<sub>2</sub> = 2 diperoleh harga<sub>1</sub> − harga<sub>3</sub> = 5 − 2 = 3, dan sebaliknya harga<sub>3</sub> − harga<sub>1</sub> = −3. Laporan "1 lebih mahal 4 dari 3" bertentangan (SALAH), sedangkan "1 lebih mahal 3 dari 3" cocok (OK). Barang 4 belum pernah dibandingkan (TIDAK TAHU).'],
            ['input' => "5 6\n1 1 2 10\n1 4 3 7\n1 2 4 0\n2 1 3\n1 3 1 0\n2 3 5\n", 'explanation' => 'Dua kelompok {1, 2} dan {3, 4} digabung oleh laporan harga<sub>2</sub> = harga<sub>4</sub>. Maka harga<sub>1</sub> − harga<sub>3</sub> = 10 + 0 + 7 = 17, sehingga laporan bahwa harga<sub>3</sub> − harga<sub>1</sub> = 0 ditolak (SALAH). Barang 5 tidak pernah disebut (TIDAK TAHU).'],
        ],
        'tests' => function () use ($pasangan) {
            $mk = function (int $n, int $q, int $g, int $pLapor, int $pSalah, int $maxHarga) use ($pasangan): string {
                // harga tersembunyi; sebagian laporan sengaja dibuat meleset sedikit
                $h = [0];
                for ($i = 1; $i <= $n; $i++) {
                    $h[] = mt_rand(0, $maxHarga);
                }
                $out = ["$n $q"];
                for ($i = 0; $i < $q; $i++) {
                    [$a, $b] = $pasangan($n, $g);
                    if (mt_rand(1, 100) <= $pLapor) {
                        if ($h[$a] < $h[$b]) {
                            [$a, $b] = [$b, $a];
                        }
                        $d = $h[$a] - $h[$b];
                        if (mt_rand(1, 100) <= $pSalah) {
                            $t = mt_rand(1, 3);
                            $d = ($d + $t <= 1000000000 && ($d < $t || mt_rand(0, 1))) ? $d + $t : $d - $t;
                        }
                        $out[] = "1 $a $b $d";
                    } else {
                        $out[] = "2 $a $b";
                    }
                }

                return implode("\n", $out)."\n";
            };
            // Rantai harga naik hampir 10^9 per barang: selisih mencapai ~5 · 10^13 (butuh long long).
            $rantai = function (int $n, int $q): string {
                $h = [0, 0];
                for ($i = 2; $i <= $n; $i++) {
                    $h[] = $h[$i - 1] + mt_rand(900000000, 1000000000);
                }
                $ev = [];
                for ($i = 1; $i < $n; $i++) {
                    $ev[] = '1 '.($i + 1)." $i ".($h[$i + 1] - $h[$i]);
                }
                while (count($ev) < $q) {
                    if (mt_rand(0, 1)) {
                        $a = mt_rand(1, $n);
                        do {
                            $b = mt_rand(1, $n);
                        } while ($b === $a);
                        $ev[] = "2 $a $b";
                    } else {
                        $i = mt_rand(1, $n - 1);
                        $d = $h[$i + 1] - $h[$i];
                        if (mt_rand(0, 1)) {
                            $d += $d < 1000000000 ? 1 : -1;
                        }
                        $ev[] = '1 '.($i + 1)." $i $d";
                    }
                }
                T::shuffle($ev);

                return "$n $q\n".implode("\n", $ev)."\n";
            };

            return [
                "2 1\n2 1 2\n",
                "2 3\n1 1 2 0\n2 2 1\n1 2 1 1\n",
                "3 4\n1 1 2 5\n1 2 3 5\n1 3 1 10\n2 3 1\n",
                $mk(10, 30, 2, 50, 20, 20),
                $mk(100, 1000, 5, 50, 15, 1000),
                $mk(1000, 5000, 20, 60, 10, 1000000000),
                $mk(100000, 100000, 1000, 60, 5, 1000000000),
                $mk(100000, 100000, 10, 75, 2, 1000000000),
                $rantai(50000, 90000),
                $mk(6, 30000, 0, 40, 30, 50),
                $mk(100000, 100000, 0, 85, 1, 1000000),
            ];
        },
        'solve' => function (string $input) {
            $lines = T::lines($input);
            [$n, $q] = T::ints($lines[0]);
            $induk = range(0, $n);
            $ukuran = array_fill(0, $n + 1, 1);
            $selisih = array_fill(0, $n + 1, 0); // harga[x] - harga[induk[x]]
            $cariB = function (int $x) use (&$induk, &$selisih): int {
                $jalur = [];
                while ($induk[$x] !== $x) {
                    $jalur[] = $x;
                    $x = $induk[$x];
                }
                for ($i = count($jalur) - 1; $i >= 0; $i--) {
                    $v = $jalur[$i];
                    $selisih[$v] += $selisih[$induk[$v]];
                    $induk[$v] = $x;
                }

                return $x;
            };
            $out = [];
            for ($i = 1; $i <= $q; $i++) {
                $k = T::ints($lines[$i]);
                $a = $k[1];
                $b = $k[2];
                $ra = $cariB($a);
                $rb = $cariB($b);
                $sa = $selisih[$a];
                $sb = $selisih[$b];
                if ($k[0] === 2) {
                    $out[] = $ra === $rb ? (string) ($sa - $sb) : 'TIDAK TAHU';
                } elseif ($ra === $rb) {
                    $out[] = $sa - $sb === $k[3] ? 'OK' : 'SALAH';
                } else {
                    $beda = $k[3] - $sa + $sb; // harga[ra] - harga[rb]
                    if ($ukuran[$ra] < $ukuran[$rb]) {
                        $induk[$ra] = $rb;
                        $selisih[$ra] = $beda;
                        $ukuran[$rb] += $ukuran[$ra];
                    } else {
                        $induk[$rb] = $ra;
                        $selisih[$rb] = -$beda;
                        $ukuran[$ra] += $ukuran[$rb];
                    }
                    $out[] = 'OK';
                }
            }

            return implode("\n", $out);
        },
        'starter' => $st("    int n, q;\n    cin >> n >> q;\n    vector<array<long long, 4>> kejadian(q);   // {jenis, a, b, d}; d = 0 untuk jenis 2\n    for (auto& k : kejadian) {\n        cin >> k[0] >> k[1] >> k[2];\n        k[3] = 0;\n        if (k[0] == 1) cin >> k[3];\n    }", "const [n, q] = readInts();\nconst kejadian = [];\nfor (let i = 0; i < q; i++) kejadian.push(readInts()); // [1, a, b, d] atau [2, a, b]", "n, q = map(int, input().split())\nkejadian = [list(map(int, input().split())) for _ in range(q)]  # [1, a, b, d] atau [2, a, b]"),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

vector<int> induk, ukuran, jalur;
vector<long long> selisih;   // selisih[x] = harga[x] - harga[induk[x]]; selisih[akar] = 0

// Mengembalikan akar x. Sesudahnya induk[x] = akar dan selisih[x] = harga[x] - harga[akar].
int cari(int x) {
    jalur.clear();
    while (induk[x] != x) {
        jalur.push_back(x);
        x = induk[x];
    }
    int akar = x;
    // Proses mulai dari simpul yang paling dekat ke akar: selisih induknya sudah relatif ke akar.
    for (int i = (int)jalur.size() - 1; i >= 0; i--) {
        int v = jalur[i];
        selisih[v] += selisih[induk[v]];
        induk[v] = akar;
    }
    return akar;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, q;
    cin >> n >> q;
    induk.resize(n + 1);
    ukuran.assign(n + 1, 1);
    selisih.assign(n + 1, 0);
    for (int i = 0; i <= n; i++) induk[i] = i;

    string out;
    for (int i = 0; i < q; i++) {
        int t, a, b;
        cin >> t >> a >> b;
        int ra = cari(a), rb = cari(b);
        long long sa = selisih[a], sb = selisih[b];   // harga[a] - harga[ra], harga[b] - harga[rb]
        if (t == 1) {
            long long d;
            cin >> d;
            if (ra == rb) {
                // harga[a] - harga[b] sudah pasti; laporan diterima hanya jika cocok
                out += (sa - sb == d) ? "OK\n" : "SALAH\n";
            } else {
                // Dua kelompok belum punya hubungan, jadi laporan pasti bisa diterima.
                // harga[ra] - harga[rb] = (harga[a] - sa) - (harga[b] - sb) = d - sa + sb
                long long beda = d - sa + sb;
                if (ukuran[ra] < ukuran[rb]) {
                    induk[ra] = rb;
                    selisih[ra] = beda;
                    ukuran[rb] += ukuran[ra];
                } else {
                    induk[rb] = ra;
                    selisih[rb] = -beda;
                    ukuran[ra] += ukuran[rb];
                }
                out += "OK\n";
            }
        } else {
            if (ra == rb) out += to_string(sa - sb) + "\n";
            else out += "TIDAK TAHU\n";
        }
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [n, q] = readInts();
const induk = new Int32Array(n + 1);
const ukuran = new Int32Array(n + 1).fill(1);
const selisih = new Float64Array(n + 1); // harga[x] - harga[induk[x]]; |nilai| ≤ 10^14, tepat sebagai Number
const jalur = new Int32Array(n + 1);
for (let i = 0; i <= n; i++) induk[i] = i;

// Iteratif: kumpulkan jalur ke akar, lalu jumlahkan selisih mulai dari yang dekat akar.
function cari(x) {
  let k = 0;
  while (induk[x] !== x) {
    jalur[k++] = x;
    x = induk[x];
  }
  for (let i = k - 1; i >= 0; i--) {
    const v = jalur[i];
    selisih[v] += selisih[induk[v]];
    induk[v] = x;
  }
  return x;
}

const out = [];
for (let i = 0; i < q; i++) {
  const [t, a, b, d] = readInts();
  const ra = cari(a), rb = cari(b);
  const sa = selisih[a], sb = selisih[b]; // harga[a] - harga[ra], harga[b] - harga[rb]
  if (t === 1) {
    if (ra === rb) {
      out.push(sa - sb === d ? "OK" : "SALAH");
    } else {
      const beda = d - sa + sb; // harga[ra] - harga[rb]
      if (ukuran[ra] < ukuran[rb]) {
        induk[ra] = rb;
        selisih[ra] = beda;
        ukuran[rb] += ukuran[ra];
      } else {
        induk[rb] = ra;
        selisih[rb] = -beda;
        ukuran[ra] += ukuran[rb];
      }
      out.push("OK");
    }
  } else {
    out.push(ra === rb ? String(sa - sb + 0) : "TIDAK TAHU"); // + 0 mengubah -0 menjadi 0
  }
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n, q = map(int, input().split())
induk = list(range(n + 1))
ukuran = [1] * (n + 1)
selisih = [0] * (n + 1)   # harga[x] - harga[induk[x]]; selisih[akar] = 0


def cari(x):
    # iteratif: kumpulkan jalur ke akar dulu
    jalur = []
    while induk[x] != x:
        jalur.append(x)
        x = induk[x]
    # mulai dari simpul yang paling dekat ke akar
    for v in reversed(jalur):
        selisih[v] += selisih[induk[v]]
        induk[v] = x
    return x


out = []
for _ in range(q):
    k = list(map(int, input().split()))
    a, b = k[1], k[2]
    ra = cari(a)
    rb = cari(b)
    sa = selisih[a]   # harga[a] - harga[ra]
    sb = selisih[b]   # harga[b] - harga[rb]
    if k[0] == 1:
        d = k[3]
        if ra == rb:
            out.append("OK" if sa - sb == d else "SALAH")
        else:
            beda = d - sa + sb   # harga[ra] - harga[rb]
            if ukuran[ra] < ukuran[rb]:
                induk[ra] = rb
                selisih[ra] = beda
                ukuran[rb] += ukuran[ra]
            else:
                induk[rb] = ra
                selisih[rb] = -beda
                ukuran[ra] += ukuran[rb]
            out.append("OK")
    else:
        out.append(str(sa - sb) if ra == rb else "TIDAK TAHU")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Barang-barang yang harganya saling terkait (lewat rantai laporan yang diterima) membentuk kelompok, dan kelompok hanya pernah bergabung. Yang berbeda dari DSU biasa: kita juga perlu tahu <em>selisih harga</em> di dalam kelompok. Ini disebut <strong>DSU berbobot</strong>.</p>
<p>Simpan <code>selisih[x] = harga[x] − harga[induk[x]]</code>, dengan <code>selisih[akar] = 0</code>. Saat <code>cari(x)</code> memadatkan jalur, selisih di sepanjang jalur dijumlahkan sehingga sesudahnya <code>selisih[x] = harga[x] − harga[akar]</code>. Urutannya penting: proses simpul yang paling dekat ke akar lebih dulu, karena nilai simpul di bawahnya bergantung pada nilai tersebut. Versi iteratif: kumpulkan jalur, lalu telusuri dari belakang.</p>
<p>Misalkan r<sub>a</sub> = cari(a), r<sub>b</sub> = cari(b), s<sub>a</sub> = selisih[a], s<sub>b</sub> = selisih[b].</p>
<ul><li><strong>Satu kelompok</strong> (r<sub>a</sub> = r<sub>b</sub>): harga<sub>a</sub> − harga<sub>b</sub> = s<sub>a</sub> − s<sub>b</sub> sudah pasti. Laporan diterima hanya jika nilainya sama dengan d; pertanyaan dijawab dengan nilai ini.</li>
<li><strong>Beda kelompok</strong>: belum ada hubungan sama sekali. Seluruh harga di satu kelompok bisa digeser naik atau turun bersama-sama tanpa melanggar laporan mana pun, jadi pertanyaan dijawab <code>TIDAK TAHU</code>, dan laporan baru <em>selalu</em> bisa diterima. Untuk menggabungkan, hitung harga<sub>r<sub>a</sub></sub> − harga<sub>r<sub>b</sub></sub> = (harga<sub>a</sub> − s<sub>a</sub>) − (harga<sub>b</sub> − s<sub>b</sub>) = d − s<sub>a</sub> + s<sub>b</sub>. Tempelkan akar yang kelompoknya lebih kecil ke akar lainnya dengan selisih ini (atau negatifnya, tergantung arah).</li></ul>
<p>Total O(N + Q · α(N)).</p>
<p><strong>Jebakan:</strong> selisih harga di dalam satu kelompok bisa mencapai (N − 1) · 10<sup>9</sup> ≈ 10<sup>14</sup>, jadi gunakan <code>long long</code> (Number di JavaScript masih tepat sampai 9 · 10<sup>15</sup>). Perhatikan juga arah: laporan <code>1 a b d</code> berarti harga<sub>a</sub> − harga<sub>b</sub> = d, bukan sebaliknya; laporan yang ditolak tidak boleh ikut digabungkan.</p>',
        'hints' => [
            'Kelompokkan barang yang harganya saling terkait. Di dalam satu kelompok, cukup ketahui selisih harga setiap barang terhadap satu barang wakil (akar).',
            'Simpan selisih[x] = harga[x] − harga[induk[x]]. Saat path compression, jumlahkan selisih di sepanjang jalur agar menjadi selisih terhadap akar.',
            'Jika a dan b sudah satu kelompok, harga_a − harga_b = selisih[a] − selisih[b]: bandingkan dengan d. Jika belum, tempelkan akar rb ke ra dengan selisih[rb] = selisih[a] − selisih[b] − d.',
        ],
    ],
];
