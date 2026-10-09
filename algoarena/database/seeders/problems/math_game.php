<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Teori Permainan: posisi menang/kalah, pola periodik,
 * Nim (XOR semua tumpukan), nilai Grundy (mex), dan teorema Sprague–Grundy.
 * Semua kode C++ harus lolos C++14.
 * JavaScript memakai BigInt untuk nilai sampai 10^18.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Token-token input sebagai bilangan bulat (int PHP 64 bit cukup untuk 10^18). */
$tok = fn (string $input): array => array_map('intval', preg_split('/\s+/', trim($input)));

/** Bilangan acak di [lo, hi] untuk rentang sampai 10^18 (mt_rand hanya 31 bit). */
$besar = function (int $lo, int $hi): int {
    $r = mt_rand(0, 999999999) * 1000000000 + mt_rand(0, 999999999);

    return $lo + $r % ($hi - $lo + 1);
};

/** Gabungkan beberapa babak ("N\na_1 ... a_N\n") menjadi satu input bertanda T. */
$babak = fn (array $daftar): string => count($daftar)."\n".implode('', $daftar);

/**
 * Permainan "pecah jadi dua tumpukan tak sama besar" untuk ukuran 0..2000:
 * [g, cnt, perNilai] dengan g[v] = nilai Grundy, cnt[v][t] = banyak cara memecah v
 * menjadi a + b (a < b) dengan g[a] XOR g[b] = t, perNilai[x] = daftar v dengan g[v] = x.
 */
$pecah = function (): array {
    static $tab = null;
    if ($tab !== null) {
        return $tab;
    }
    $maks = 2000;
    $g = array_fill(0, $maks + 1, 0);
    $cnt = array_fill(0, $maks + 1, []);
    for ($v = 3; $v <= $maks; $v++) {
        $c = [];
        for ($a = 1; 2 * $a < $v; $a++) {
            $t = $g[$a] ^ $g[$v - $a];
            $c[$t] = ($c[$t] ?? 0) + 1;
        }
        $m = 0;
        while (isset($c[$m])) {
            $m++;
        }
        $g[$v] = $m;
        $cnt[$v] = $c;
    }
    $perNilai = [];
    for ($v = 1; $v <= $maks; $v++) {
        $perNilai[$g[$v]][] = $v;
    }

    return $tab = [$g, $cnt, $perNilai];
};

return [
    [
        'slug' => 'kelereng-terakhir',
        'lesson' => 'permainan',
        'title' => 'Kelereng Terakhir',
        'difficulty' => 'Mudah',
        'tags' => ['teori permainan', 'posisi menang kalah', 'pola periodik'],
        'statement' => '<p>Dina dan Eko bermain dengan sekantong kelereng berisi <strong>N</strong> butir. Mereka bergiliran, dimulai dari Dina. Pada gilirannya, pemain harus mengambil paling sedikit 1 dan paling banyak <strong>K</strong> butir dari kantong. Pemain yang mendapat giliran ketika kantong sudah kosong kalah; dengan kata lain, pemain yang mengambil kelereng terakhir menang.</p>
<p>Keduanya selalu bermain optimal. Mereka memainkan <strong>Q</strong> permainan dengan nilai N dan K yang berbeda-beda. Tentukan pemenang setiap permainan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi <code>N K</code> untuk satu permainan.</p>',
        'output_format' => '<p>Q baris; baris ke-i berisi <code>Dina</code> atau <code>Eko</code>, yaitu pemenang permainan ke-i.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 10<sup>5</sup></li><li>1 ≤ N, K ≤ 10<sup>18</sup></li></ul>',
        'samples' => [
            ['input' => "4\n5 2\n6 2\n1 10\n12 3\n", 'explanation' => 'Permainan 1 (N = 5, K = 2): Dina mengambil 2 butir sehingga tersisa 3. Apa pun yang diambil Eko (1 atau 2 butir), Dina mengambil sisanya. Permainan 2 (N = 6, K = 2): berapa pun yang diambil Dina, Eko mengambil secukupnya agar tersisa 3 butir, lalu keadaannya sama seperti permainan 1 dengan peran tertukar. Permainan 3: Dina langsung mengambil satu-satunya kelereng. Permainan 4 (N = 12, K = 3): setiap kali Dina mengambil x butir, Eko mengambil 4 − x butir, sehingga Eko yang mengambil kelereng terakhir.'],
            ['input' => "3\n1000000000000000000 999999999999999999\n999999999999999999 1\n1000000000000000000 1000000000000000000\n", 'explanation' => 'Permainan 1: N tepat sama dengan K + 1, jadi berapa pun yang diambil Dina, Eko bisa mengambil semua sisanya. Permainan 2: setiap giliran hanya boleh mengambil 1 butir, jadi pemenangnya ditentukan oleh ganjil-genapnya N; N ganjil sehingga Dina yang mengambil butir terakhir. Permainan 3: Dina langsung mengambil semuanya. Nilai sebesar ini membutuhkan <code>long long</code> (BigInt di JavaScript).'],
        ],
        'tests' => function () use ($besar) {
            $e18 = 1000000000000000000;
            // pKelipatan persen kueri dibuat dengan N kelipatan K + 1 agar pemenangnya bervariasi
            $kueri = function (int $q, int $maxN, int $maxK, int $pKelipatan) use ($besar) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $k = $besar(1, $maxK);
                    $batas = intdiv($maxN, $k + 1);
                    if ($batas >= 1 && mt_rand(1, 100) <= $pKelipatan) {
                        $n = ($k + 1) * $besar(1, $batas);
                    } else {
                        $n = $besar(1, $maxN);
                    }
                    $rows[] = "$n $k";
                }

                return "$q\n".implode("\n", $rows)."\n";
            };
            $semua = [];
            for ($n = 1; $n <= 12; $n++) {
                for ($k = 1; $k <= 5; $k++) {
                    $semua[] = "$n $k";
                }
            }
            $tepi = [
                "$e18 $e18", "$e18 999999999999999999", '999999999999999999 999999999999999998', "$e18 1", '999999999999999999 1',
                "1 $e18", "$e18 499999999999999999", '999999999999999999 2', "$e18 2", '576460752303423488 3', '576460752303423487 3',
            ];

            return [
                "1\n1 1\n",
                "1\n2 1\n",
                count($semua)."\n".implode("\n", $semua)."\n",
                count($tepi)."\n".implode("\n", $tepi)."\n",
                $kueri(1000, 100, 20, 50),
                $kueri(30000, 1000000, 1000, 40),
                $kueri(100000, 1000000000, 10, 50),
                $kueri(45000, $e18, $e18, 50),
                $kueri(50000, $e18, 10, 50),
                $kueri(30000, $e18, 1000000000, 50),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            $q = $t[0];
            $out = [];
            for ($i = 0; $i < $q; $i++) {
                $n = $t[1 + 2 * $i];
                $k = $t[2 + 2 * $i];
                $out[] = $n % ($k + 1) === 0 ? 'Eko' : 'Dina';
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int q;\n    cin >> q;\n    while (q--) {\n        long long n, k;\n        cin >> n >> k;\n    }",
            "const q = Number(readLine());\nfor (let i = 0; i < q; i++) {\n  // N dan K sampai 10^18: pakai BigInt\n  const [n, k] = readLine().trim().split(/\\s+/).map(BigInt);\n}",
            "q = int(input())\nfor _ in range(q):\n    n, k = map(int, input().split())"
        ),
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
        long long n, k;
        cin >> n >> k;
        // Posisi kalah = kelipatan K + 1 (K + 1 ≤ 10^18 + 1 masih muat di long long).
        // Jika N bukan kelipatan, Dina mengambil N mod (K + 1) butir dan menyerahkan
        // kelipatan K + 1 kepada Eko.
        if (n % (k + 1) == 0) out += "Eko\n";
        else out += "Dina\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  // N dan K sampai 10^18 melewati presisi Number (2^53): pakai BigInt
  const [n, k] = readLine().trim().split(/\s+/).map(BigInt);
  // posisi kalah = kelipatan K + 1
  out.push(n % (k + 1n) === 0n ? "Eko" : "Dina");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    n, k = map(int, input().split())
    # posisi kalah = kelipatan K + 1
    out.append("Eko" if n % (k + 1) == 0 else "Dina")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Sebut banyak kelereng n sebagai <strong>posisi kalah</strong> jika pemain yang mendapat giliran pada keadaan itu pasti kalah (selama lawan bermain optimal), dan <strong>posisi menang</strong> jika sebaliknya. Posisi 0 adalah posisi kalah karena tidak ada langkah. Posisi n menang jika <em>ada</em> langkah menuju posisi kalah, dan kalah jika <em>semua</em> langkahnya menuju posisi menang.</p>
<p>Isi tabel kecil untuk K = 2: n = 0 kalah, 1 menang, 2 menang, 3 kalah, 4 menang, 5 menang, 6 kalah, … Polanya berulang dengan periode K + 1: <strong>posisi kalah tepat kelipatan K + 1</strong>. Buktinya:</p>
<ul><li>Dari kelipatan K + 1, mengambil x butir (1 ≤ x ≤ K) selalu menghasilkan bilangan yang bukan kelipatan K + 1. Jadi semua langkah menuju posisi menang.</li><li>Dari n yang bukan kelipatan, sisa r = n mod (K + 1) bernilai antara 1 dan K, sehingga pemain bisa mengambil tepat r butir dan menyerahkan kelipatan K + 1 kepada lawan.</li></ul>
<p>Jadi Dina menang jika dan hanya jika <code>N mod (K + 1) ≠ 0</code>. Setiap permainan dijawab dalam O(1), total O(Q).</p>
<p><strong>Jebakan:</strong> N dan K sampai 10<sup>18</sup>, jadi DP sampai N mustahil dan nilainya butuh <code>long long</code>; K + 1 ≤ 10<sup>18</sup> + 1 masih muat. Di JavaScript, Number hanya tepat sampai sekitar 9 · 10<sup>15</sup>, jadi baca angkanya sebagai BigInt.</p>',
        'hints' => [
            'Mulailah dari keadaan kecil: dengan 0 kelereng, pemain yang mendapat giliran kalah. Tandai 1, 2, 3, … sebagai posisi menang atau kalah untuk K = 2.',
            'Posisi kalah muncul berulang dengan jarak K + 1. Coba buktikan bahwa posisi kalah adalah kelipatan K + 1.',
            'Dina menang tepat jika N mod (K + 1) ≠ 0. Pakai long long di C++ dan BigInt di JavaScript.',
        ],
    ],

    [
        'slug' => 'nim-jam-istirahat',
        'lesson' => 'permainan',
        'title' => 'Nim di Jam Istirahat',
        'difficulty' => 'Sedang',
        'tags' => ['teori permainan', 'nim', 'xor'],
        'statement' => '<p>Saat jam istirahat, Citra dan Dodi bermain dengan <strong>N</strong> tumpukan koin di atas meja; tumpukan ke-i berisi <code>a<sub>i</sub></code> koin. Mereka bergiliran, dimulai dari Citra. Pada gilirannya, pemain memilih <strong>satu</strong> tumpukan yang masih berisi koin lalu mengambil berapa pun koin dari tumpukan itu (minimal satu, boleh semuanya). Pemain yang mendapat giliran ketika semua tumpukan sudah kosong kalah.</p>
<p>Keduanya selalu bermain optimal. Mereka memainkan <strong>T</strong> babak dengan susunan tumpukan yang berbeda-beda. Untuk setiap babak, tentukan pemenangnya. Jika Citra yang menang, Dodi juga penasaran: ada berapa <em>langkah pertama</em> Citra yang menjamin kemenangannya? Dua langkah dianggap berbeda jika tumpukan yang dipilih berbeda atau banyak koin yang diambil berbeda.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap babak terdiri dari dua baris: baris pertama berisi <code>N</code>, baris kedua berisi <code>a<sub>1</sub> a<sub>2</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>T baris. Untuk setiap babak, cetak <code>Citra X</code> jika Citra menang, dengan X banyak langkah pertama yang menjamin kemenangannya, atau <code>Dodi</code> jika Dodi yang menang.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 10<sup>4</sup></li><li>1 ≤ N; jumlah N dari semua babak ≤ 2 · 10<sup>5</sup></li><li>1 ≤ a<sub>i</sub> ≤ 10<sup>9</sup></li></ul>',
        'samples' => [
            ['input' => "4\n2\n3 3\n3\n1 2 4\n3\n5 6 7\n1\n10\n", 'explanation' => 'Babak 1: apa pun yang diambil Citra dari satu tumpukan, Dodi mengambil jumlah yang sama dari tumpukan lainnya, sehingga kedua tumpukan selalu sama dan Dodi yang mengambil koin terakhir. Babak 2: 1 XOR 2 XOR 4 = 7; satu-satunya langkah yang menang adalah mengambil 1 koin dari tumpukan berisi 4 sehingga tersisa 1, 2, 3 (XOR-nya 0). Babak 3: 5 XOR 6 XOR 7 = 4, dan ketiga tumpukan bisa dipakai: 5 → 1, 6 → 2, atau 7 → 3. Babak 4: Citra mengambil semua 10 koin sekaligus; mengambil kurang dari itu membuat Dodi bisa mengambil sisanya.'],
            ['input' => "2\n2\n1000000000 999999999\n3\n1 1 1\n", 'explanation' => 'Babak 1: Citra mengambil 1 koin dari tumpukan pertama sehingga kedua tumpukan sama besar, lalu meniru langkah Dodi. Babak 2: Citra boleh mengambil dari tumpukan mana saja; ketiga langkah itu menyisakan dua tumpukan berisi 1 koin, yang merupakan posisi kalah bagi Dodi.'],
        ],
        'tests' => function () use ($babak) {
            // satu babak acak; $nol = true memaksa XOR semua tumpukan = 0 (Dodi menang)
            $nim = function (int $n, int $maxv, bool $nol) {
                while (true) {
                    $a = [];
                    $x = 0;
                    $isi = $nol && $n >= 2 ? $n - 1 : $n;
                    for ($i = 0; $i < $isi; $i++) {
                        $a[] = mt_rand(1, $maxv);
                        $x ^= $a[$i];
                    }
                    if ($isi < $n) {
                        if ($x < 1 || $x > $maxv) {
                            continue;
                        }
                        $a[] = $x;
                        T::shuffle($a);
                    }

                    return "$n\n".implode(' ', $a)."\n";
                }
            };
            $acak = function (int $t, int $nMin, int $nMaks, int $maxv) use ($nim, $babak) {
                $d = [];
                for ($i = 0; $i < $t; $i++) {
                    $d[] = $nim(mt_rand($nMin, $nMaks), $maxv, mt_rand(0, 1) === 1);
                }

                return $babak($d);
            };
            $pangkat = [];
            for ($i = 0; $i < 2000; $i++) {
                $n = mt_rand(1, 100);
                $a = [];
                for ($j = 0; $j < $n; $j++) {
                    $a[] = 1 << mt_rand(0, 29);
                }
                $pangkat[] = "$n\n".implode(' ', $a)."\n";
            }

            return [
                "1\n1\n1\n",
                "3\n2\n5 5\n2\n1 1000000000\n4\n7 7 7 7\n",
                $acak(300, 1, 4, 7),
                $acak(5000, 2, 30, 15),
                $acak(10000, 1, 20, 1000000000),
                $babak([$nim(200000, 1000000000, false)]),
                $babak([$nim(200000, 1000000000, true)]),
                $acak(100, 2000, 2000, 1000000000),
                $babak(["99999\n".implode(' ', array_fill(0, 99999, 1))."\n", "100000\n".implode(' ', array_fill(0, 100000, 1))."\n"]),
                $babak($pangkat),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            $p = 0;
            $tc = $t[$p++];
            $out = [];
            for ($c = 0; $c < $tc; $c++) {
                $n = $t[$p++];
                $a = array_slice($t, $p, $n);
                $p += $n;
                $x = 0;
                foreach ($a as $v) {
                    $x ^= $v;
                }
                if ($x === 0) {
                    $out[] = 'Dodi';

                    continue;
                }
                $banyak = 0;
                foreach ($a as $v) {
                    if (($v ^ $x) < $v) {
                        $banyak++;
                    }
                }
                $out[] = "Citra $banyak";
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int t;\n    cin >> t;\n    while (t--) {\n        int n;\n        cin >> n;\n        vector<long long> a(n);\n        for (auto& x : a) cin >> x;\n    }",
            "const t = Number(readLine());\nfor (let tc = 0; tc < t; tc++) {\n  const n = Number(readLine());\n  const a = readInts();\n}",
            "t = int(input())\nfor _ in range(t):\n    n = int(input())\n    a = list(map(int, input().split()))"
        ),
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
        int n;
        cin >> n;
        vector<long long> a(n);
        long long x = 0;                    // nim-sum: XOR semua tumpukan
        for (auto& v : a) {
            cin >> v;
            x ^= v;
        }
        if (x == 0) {                       // posisi kalah bagi pemain yang melangkah
            out += "Dodi\n";
            continue;
        }
        // Langkah pada tumpukan i menang tepat jika ukuran barunya a[i] ^ x
        // (agar XOR menjadi 0). Itu sah hanya jika a[i] ^ x < a[i].
        int banyak = 0;
        for (long long v : a)
            if ((v ^ x) < v) banyak++;
        out += "Citra " + to_string(banyak) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const t = Number(readLine());
const out = [];
for (let tc = 0; tc < t; tc++) {
  readLine(); // N (tidak diperlukan)
  const a = readInts();
  // a_i ≤ 10^9 < 2^30, jadi operator ^ (32 bit) aman
  let x = 0;
  for (const v of a) x ^= v;
  if (x === 0) {
    out.push("Dodi");
    continue;
  }
  // tumpukan i bisa dikecilkan menjadi a_i ^ x jika hasilnya lebih kecil
  let banyak = 0;
  for (const v of a) if ((v ^ x) < v) banyak++;
  out.push("Citra " + banyak);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

t = int(input())
out = []
for _ in range(t):
    n = int(input())
    a = list(map(int, input().split()))
    x = 0
    for v in a:
        x ^= v               # nim-sum
    if x == 0:
        out.append("Dodi")
        continue
    # langkah menang di tumpukan v: kecilkan menjadi v ^ x (sah jika v ^ x < v)
    banyak = sum(1 for v in a if v ^ x < v)
    out.append("Citra " + str(banyak))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Ini adalah permainan <strong>Nim</strong>. Hitung <code>X = a<sub>1</sub> XOR a<sub>2</sub> XOR … XOR a<sub>N</sub></code> (nim-sum). Teorema Bouton: posisi kalah bagi pemain yang melangkah tepat ketika X = 0.</p>
<ul><li>Keadaan akhir (semua kosong) memiliki X = 0, dan itu posisi kalah.</li><li>Dari X = 0, setiap langkah mengubah tepat satu tumpukan, sehingga XOR-nya pasti berubah menjadi ≠ 0.</li><li>Dari X ≠ 0, misalkan h bit tertinggi X. Ada tumpukan a<sub>i</sub> yang bit h-nya menyala; untuk tumpukan itu <code>a<sub>i</sub> XOR X &lt; a<sub>i</sub></code> (bit h padam, bit di atasnya tetap). Kecilkan tumpukan itu menjadi a<sub>i</sub> XOR X, maka XOR seluruhnya menjadi 0.</li></ul>
<p><strong>Menghitung langkah menang.</strong> Langkah menang adalah langkah menuju posisi kalah, yaitu yang membuat XOR menjadi 0. Jika tumpukan i diubah menjadi b, XOR barunya X XOR a<sub>i</sub> XOR b, yang bernilai 0 hanya untuk <code>b = a<sub>i</sub> XOR X</code>. Jadi setiap tumpukan memberi paling banyak satu langkah menang, dan langkah itu sah tepat ketika a<sub>i</sub> XOR X &lt; a<sub>i</sub> (setara: a<sub>i</sub> memiliki bit tertinggi X). Jawabannya adalah banyak tumpukan yang memenuhi syarat itu.</p>
<p>Kompleksitas O(N) per babak. Nilai a<sub>i</sub> &lt; 2<sup>30</sup>, jadi XOR muat di <code>int</code> dan operator <code>^</code> JavaScript aman.</p>',
        'hints' => [
            'Coba dua tumpukan dulu. Kapan pemain pertama kalah? Perhatikan strategi meniru lawan.',
            'Hitung X = XOR semua tumpukan. Buktikan bahwa X = 0 adalah posisi kalah dan dari X ≠ 0 selalu ada langkah ke X = 0.',
            'Langkah di tumpukan i menang jika ukuran barunya a_i XOR X, dan itu sah hanya jika a_i XOR X < a_i. Hitung tumpukan yang memenuhi.',
        ],
    ],

    [
        'slug' => 'ambil-sesuai-kartu',
        'lesson' => 'permainan',
        'title' => 'Ambil Sesuai Kartu',
        'difficulty' => 'Sedang',
        'tags' => ['teori permainan', 'dp', 'posisi menang kalah'],
        'statement' => '<p>Ani dan Budi punya <strong>M</strong> kartu angka yang bertuliskan bilangan berbeda <code>s<sub>1</sub>, s<sub>2</sub>, …, s<sub>M</sub></code>. Mereka bermain dengan satu tumpukan batu dan bergiliran, dimulai dari Ani. Pada gilirannya, pemain memilih satu kartu yang angkanya tidak melebihi banyak batu yang tersisa, lalu mengambil <strong>tepat</strong> sebanyak angka itu dari tumpukan. Kartu tidak habis dipakai, jadi setiap kartu boleh dipilih berkali-kali. Pemain yang tidak bisa melangkah kalah, walaupun batunya mungkin belum habis.</p>
<p>Keduanya selalu bermain optimal. Ada <strong>Q</strong> pertanyaan: jika tumpukan awal berisi <code>n<sub>j</sub></code> batu, siapa pemenangnya?</p>',
        'input_format' => '<p>Baris pertama berisi <code>M Q</code>. Baris kedua berisi <code>s<sub>1</sub> … s<sub>M</sub></code>. Baris ketiga berisi <code>n<sub>1</sub> … n<sub>Q</sub></code>.</p>',
        'output_format' => '<p>Q baris; baris ke-j berisi <code>Ani</code> atau <code>Budi</code>, yaitu pemenang jika tumpukan awal berisi n<sub>j</sub> batu.</p>',
        'constraints' => '<ul><li>1 ≤ M ≤ 20</li><li>1 ≤ s<sub>i</sub> ≤ 10<sup>5</sup>, semuanya berbeda</li><li>1 ≤ Q ≤ 10<sup>5</sup></li><li>1 ≤ n<sub>j</sub> ≤ 10<sup>5</sup></li></ul>',
        'samples' => [
            ['input' => "3 6\n4 1 3\n1 2 5 7 9 99\n", 'explanation' => 'Untuk kartu {1, 3, 4}, posisi kalah adalah 0, 2, 7, 9, 14, 16, … Dengan 2 batu, Ani hanya bisa mengambil 1, lalu Budi mengambil batu terakhir. Dengan 5 batu, Ani mengambil 3 sehingga Budi menghadapi 2 batu. Dengan 7 batu, sisa setelah langkah Ani adalah 6, 4, atau 3, dan dari masing-masing Budi bisa menyerahkan posisi kalah (6 → 2, 4 → 0, 3 → 0). Posisi kalah ini berulang setiap 7 batu, dan 99 = 14 · 7 + 1 adalah posisi menang.'],
            ['input' => "1 3\n5\n4 5 12\n", 'explanation' => 'Dengan 4 batu, kartu 5 tidak bisa dipakai sehingga Ani langsung kalah walaupun batu masih ada. Dengan 5 batu Ani mengambil semuanya. Dengan 12 batu permainannya pasti 12 → 7 → 2, lalu Ani tidak bisa melangkah.'],
        ],
        'tests' => function () {
            // M bilangan berbeda acak di [lo, hi], urutan diacak
            $kartu = function (int $m, int $lo, int $hi): array {
                $set = [];
                while (count($set) < $m) {
                    $set[mt_rand($lo, $hi)] = true;
                }
                $s = array_keys($set);
                T::shuffle($s);

                return $s;
            };
            $buat = function (array $s, array $n): string {
                return count($s).' '.count($n)."\n".implode(' ', $s)."\n".implode(' ', $n)."\n";
            };
            $acakN = function (int $q, int $lo, int $hi): array {
                $n = [];
                for ($i = 0; $i < $q; $i++) {
                    $n[] = mt_rand($lo, $hi);
                }

                return $n;
            };
            $genap = [];
            for ($i = 1; $i <= 20; $i++) {
                $genap[] = 2 * $i;
            }
            $tepi = array_merge($acakN(1000, 1, 100000), [49999, 50000, 99999, 100000]);
            T::shuffle($tepi);
            // untuk kartu 1..20 posisi kalah adalah kelipatan 21: separuh pertanyaan dibuat kelipatan 21
            $kelipatan21 = [];
            for ($i = 0; $i < 30000; $i++) {
                $kelipatan21[] = mt_rand(0, 1) ? 21 * mt_rand(1, 4761) : mt_rand(1, 100000);
            }

            return [
                $buat([1], [1]),
                $buat([2], [1, 2, 3, 4, 5]),
                $buat([4, 1, 3], range(1, 100)),
                $buat($kartu(5, 1, 10), $acakN(1000, 1, 1000)),
                $buat($kartu(20, 1, 100), $acakN(100000, 1, 100000)),
                $buat($kartu(20, 1, 100000), $acakN(50000, 1, 100000)),
                $buat(range(1, 20), $kelipatan21),
                $buat($genap, $acakN(20000, 1, 100000)),
                $buat($kartu(20, 1000, 5000), $acakN(50000, 1, 100000)),
                $buat($kartu(3, 1, 30), array_merge($acakN(49999, 1, 100000), [100000])),
                $buat([100000, 99999, 50000], $tepi),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            [$m, $q] = [$t[0], $t[1]];
            $s = array_slice($t, 2, $m);
            $n = array_slice($t, 2 + $m, $q);
            sort($s);
            $maks = max($n);
            $menang = array_fill(0, $maks + 1, false);
            for ($i = 0; $i <= $maks; $i++) {
                if (! $menang[$i]) {
                    // i posisi kalah: setiap i + s menjadi posisi menang
                    foreach ($s as $x) {
                        if ($i + $x > $maks) {
                            break;
                        }
                        $menang[$i + $x] = true;
                    }
                }
            }
            $out = [];
            foreach ($n as $v) {
                $out[] = $menang[$v] ? 'Ani' : 'Budi';
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int m, q;\n    cin >> m >> q;\n    vector<int> s(m), n(q);\n    for (auto& x : s) cin >> x;\n    for (auto& x : n) cin >> x;",
            "const [m, q] = readInts();\nconst s = readInts();\nconst n = readInts();",
            "m, q = map(int, input().split())\ns = list(map(int, input().split()))\nn = list(map(int, input().split()))"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int m, q;
    cin >> m >> q;
    vector<int> s(m), n(q);
    for (auto& x : s) cin >> x;
    for (auto& x : n) cin >> x;
    int maks = *max_element(n.begin(), n.end());

    // menang[i] = 1 jika pemain yang mendapat giliran dengan i batu pasti menang.
    // menang[0] = 0. Posisi i menang jika ADA kartu x ≤ i dengan menang[i - x] = 0;
    // jika tidak ada (termasuk tidak ada langkah sama sekali), posisi i kalah.
    vector<char> menang(maks + 1, 0);
    for (int i = 1; i <= maks; i++) {
        for (int x : s) {
            if (x <= i && !menang[i - x]) {
                menang[i] = 1;
                break;
            }
        }
    }

    string out;
    for (int v : n) out += menang[v] ? "Ani\n" : "Budi\n";
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const [m, q] = readInts();
const s = readInts();
const n = readInts();
let maks = 0;
for (const v of n) if (v > maks) maks = v;

// menang[i] = 1 jika ada kartu x ≤ i sehingga i - x adalah posisi kalah
const menang = new Uint8Array(maks + 1);
for (let i = 1; i <= maks; i++) {
  for (const x of s) {
    if (x <= i && !menang[i - x]) {
      menang[i] = 1;
      break;
    }
  }
}

const out = new Array(q);
for (let j = 0; j < q; j++) out[j] = menang[n[j]] ? "Ani" : "Budi";
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

m, q = map(int, input().split())
s = sorted(map(int, input().split()))
n = list(map(int, input().split()))
maks = max(n)

# Versi "dorong": setiap posisi kalah i membuat semua posisi i + x menjadi menang.
# Posisi yang tidak pernah ditandai menang adalah posisi kalah (semua langkahnya
# menuju posisi menang, atau tidak ada langkah sama sekali).
menang = bytearray(maks + 1)
for i in range(maks + 1):
    if not menang[i]:
        for x in s:
            j = i + x
            if j > maks:
                break
            menang[j] = 1

print("\n".join("Ani" if menang[v] else "Budi" for v in n))
CODE,
        ],
        'editorial' => '<p>Keadaan permainan cukup diwakili oleh banyak batu yang tersisa. Definisikan <code>menang[i]</code> = benar jika pemain yang mendapat giliran dengan i batu pasti menang.</p>
<ul><li>Posisi i <strong>menang</strong> jika ada kartu s ≤ i sehingga i − s adalah posisi kalah (pemain menyerahkan posisi kalah kepada lawan).</li><li>Posisi i <strong>kalah</strong> jika semua langkahnya menuju posisi menang. Ini termasuk keadaan tanpa langkah sama sekali, misalnya 0 batu, atau batu yang lebih sedikit daripada kartu terkecil.</li></ul>
<p>Karena setiap langkah mengurangi batu, menang[i] hanya bergantung pada nilai yang lebih kecil, jadi tabel bisa diisi dari i = 0 ke atas. Isi tabel sekali sampai n terbesar dalam O(max n · M) ≈ 2 · 10<sup>6</sup> operasi, lalu jawab setiap pertanyaan dengan melihat tabel. Mengulang DP untuk setiap pertanyaan akan terlalu lambat.</p>
<p>Cara lain yang setara (dipakai di solusi Python): untuk setiap posisi kalah i, tandai semua i + s sebagai posisi menang. Posisi yang tidak pernah ditandai adalah posisi kalah.</p>
<p>Tidak seperti soal "ambil 1 sampai K", di sini tidak ada rumus umum yang sederhana. Tabelnya memang selalu menjadi periodik pada akhirnya (contoh 1 berperiode 7), tetapi DP langsung sudah cukup cepat untuk batasan ini.</p>',
        'hints' => [
            'Posisi dengan n batu hanya bergantung pada posisi n − s yang lebih kecil. Mulailah dari 0 batu.',
            'Posisi n menang jika ada kartu s ≤ n sehingga n − s adalah posisi kalah. Jika tidak ada langkah sama sekali, posisi itu kalah.',
            'Hitung tabel menang[0..max n] sekali dalam O(max n · M), lalu jawab setiap pertanyaan dalam O(1).',
        ],
    ],

    [
        'slug' => 'pecah-jadi-dua',
        'lesson' => 'permainan',
        'title' => 'Pecah Jadi Dua',
        'difficulty' => 'Sulit',
        'tags' => ['teori permainan', 'sprague-grundy', 'mex', 'xor'],
        'statement' => '<p>Raka dan Sinta bermain dengan <strong>N</strong> tumpukan batu; tumpukan ke-i berisi <code>a<sub>i</sub></code> batu. Mereka bergiliran, dimulai dari Raka. Pada gilirannya, pemain memilih satu tumpukan lalu <strong>memecahnya menjadi dua tumpukan tidak kosong yang banyak batunya berbeda</strong>. Misalnya tumpukan berisi 7 batu boleh dipecah menjadi 1 + 6, 2 + 5, atau 3 + 4, sedangkan tumpukan berisi 4 batu hanya boleh menjadi 1 + 3 (bukan 2 + 2). Tumpukan berisi 1 atau 2 batu tidak bisa dipecah lagi. Pemain yang tidak bisa melangkah kalah.</p>
<p>Keduanya selalu bermain optimal. Mereka memainkan <strong>T</strong> babak. Untuk setiap babak, tentukan pemenangnya. Jika Raka yang menang, hitung juga banyak <em>langkah pertama</em> Raka yang menjamin kemenangannya. Sebuah langkah ditentukan oleh tumpukan yang dipecah dan ukuran kedua bagiannya; memecah 7 menjadi 2 + 5 sama dengan memecahnya menjadi 5 + 2. Tumpukan yang berbeda dihitung terpisah walaupun ukurannya sama.</p>',
        'input_format' => '<p>Baris pertama berisi <code>T</code>. Setiap babak terdiri dari dua baris: baris pertama berisi <code>N</code>, baris kedua berisi <code>a<sub>1</sub> a<sub>2</sub> … a<sub>N</sub></code>.</p>',
        'output_format' => '<p>T baris. Untuk setiap babak, cetak <code>Raka X</code> jika Raka menang, dengan X banyak langkah pertama yang menjamin kemenangannya, atau <code>Sinta</code> jika Sinta yang menang.</p>',
        'constraints' => '<ul><li>1 ≤ T ≤ 10<sup>4</sup></li><li>1 ≤ N; jumlah N dari semua babak ≤ 2 · 10<sup>5</sup></li><li>1 ≤ a<sub>i</sub> ≤ 2000</li></ul>',
        'samples' => [
            ['input' => "4\n1\n4\n1\n5\n2\n3 6\n2\n7 8\n", 'explanation' => 'Babak 1: Raka terpaksa memecah 4 menjadi 1 + 3, Sinta memecah 3 menjadi 1 + 2, dan Raka tidak bisa melangkah lagi. Babak 2: memecah 5 menjadi 1 + 4 menang (Sinta lalu terjebak seperti Raka di babak 1, tumpukan 1 tidak berpengaruh), sedangkan 2 + 3 kalah karena Sinta tinggal memecah 3. Babak 3: nilai Grundy tumpukan 3 dan 6 sama-sama 1, jadi XOR-nya 0 dan Sinta menang. Babak 4: g(7) = 0 dan g(8) = 2; langkah yang menang adalah 7 → 2 + 5 dan 8 → 1 + 7.'],
            ['input' => "1\n3\n9 10 11\n", 'explanation' => 'g(9) = 1, g(10) = 0, g(11) = 2, jadi XOR-nya 3 dan Raka menang. Langkah yang menang adalah 9 → 1 + 8, 9 → 4 + 5, dan 11 → 2 + 9; masing-masing membuat XOR nilai Grundy semua tumpukan menjadi 0. Tumpukan 10 tidak memberi langkah menang.'],
        ],
        'tests' => function () use ($pecah, $babak) {
            [$g, , $perNilai] = $pecah();
            // satu babak acak; $nol = true memaksa XOR nilai Grundy = 0 (Sinta menang)
            $satu = function (int $n, int $maxv, bool $nol) use ($g, $perNilai) {
                $a = [];
                for ($i = 0; $i < $n; $i++) {
                    $a[] = mt_rand(1, $maxv);
                }
                if ($nol) {
                    while (true) {
                        $x = 0;
                        for ($i = 1; $i < $n; $i++) {
                            $x ^= $g[$a[$i]];
                        }
                        $cocok = array_values(array_filter($perNilai[$x] ?? [], fn ($v) => $v <= $maxv));
                        if ($cocok) {
                            $a[0] = $cocok[mt_rand(0, count($cocok) - 1)];
                            break;
                        }
                        $a[mt_rand(0, $n - 1)] = mt_rand(1, $maxv);
                    }
                    T::shuffle($a);
                }

                return "$n\n".implode(' ', $a)."\n";
            };
            $acak = function (int $t, int $nMin, int $nMaks, int $maxv) use ($satu, $babak) {
                $d = [];
                for ($i = 0; $i < $t; $i++) {
                    $d[] = $satu(mt_rand($nMin, $nMaks), $maxv, mt_rand(0, 1) === 1);
                }

                return $babak($d);
            };
            $tunggal = [];
            for ($v = 1; $v <= 2000; $v++) {
                $tunggal[] = "1\n$v\n";
            }
            $kecil = [];
            for ($i = 0; $i < 50000; $i++) {
                $kecil[] = mt_rand(1, 2);
            }
            $nolSemua = [];
            for ($i = 0; $i < 50000; $i++) {
                $nolSemua[] = $perNilai[0][mt_rand(0, count($perNilai[0]) - 1)];
            }

            return [
                "1\n1\n1\n",
                "2\n1\n3\n2\n2000 2000\n",
                $babak($tunggal),
                $acak(300, 1, 4, 12),
                $acak(10000, 1, 20, 2000),
                $babak([$satu(200000, 2000, false)]),
                $babak([$satu(200000, 2000, true)]),
                $acak(100, 2000, 2000, 2000),
                $acak(1000, 1, 100, 50),
                $babak(["50000\n".implode(' ', array_fill(0, 50000, 2000))."\n", "50000\n".implode(' ', $kecil)."\n", "50000\n".implode(' ', $nolSemua)."\n", $satu(49999, 2000, false)]),
            ];
        },
        'solve' => function (string $input) use ($tok, $pecah) {
            [$g, $cnt] = $pecah();
            $t = $tok($input);
            $p = 0;
            $tc = $t[$p++];
            $out = [];
            for ($c = 0; $c < $tc; $c++) {
                $n = $t[$p++];
                $a = array_slice($t, $p, $n);
                $p += $n;
                $x = 0;
                foreach ($a as $v) {
                    $x ^= $g[$v];
                }
                if ($x === 0) {
                    $out[] = 'Sinta';

                    continue;
                }
                $banyak = 0;
                foreach ($a as $v) {
                    $banyak += $cnt[$v][$x ^ $g[$v]] ?? 0;
                }
                $out[] = "Raka $banyak";
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int t;\n    cin >> t;\n    while (t--) {\n        int n;\n        cin >> n;\n        vector<int> a(n);\n        for (auto& x : a) cin >> x;\n    }",
            "const t = Number(readLine());\nfor (let tc = 0; tc < t; tc++) {\n  const n = Number(readLine());\n  const a = readInts();\n}",
            "t = int(input())\nfor _ in range(t):\n    n = int(input())\n    a = list(map(int, input().split()))"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

const int MAKS = 2000;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    // g[v] = nilai Grundy satu tumpukan berisi v batu.
    // Memecah v menjadi a + (v - a) dengan a < v - a menghasilkan dua tumpukan,
    // yaitu gabungan dua permainan, sehingga nilainya g[a] ^ g[v - a].
    // g[v] = mex dari semua nilai itu. g[1] = g[2] = 0 (tidak ada langkah).
    // Banyak pilihan < 1000, jadi semua nilai Grundy < 1024 dan XOR-nya pun < 1024.
    vector<int> g(MAKS + 1, 0), tanda(1024, -1);
    int gMaks = 0;
    for (int v = 3; v <= MAKS; v++) {
        for (int a = 1; a < v - a; a++) tanda[g[a] ^ g[v - a]] = v;
        int m = 0;
        while (tanda[m] == v) m++;          // mex
        g[v] = m;
        gMaks = max(gMaks, m);
    }

    // W = pangkat dua terkecil yang > gMaks; XOR dua nilai < W tetap < W.
    int W = 1;
    while (W <= gMaks) W *= 2;
    // cnt[v][t] = banyak cara memecah v dengan g[a] ^ g[v - a] = t
    vector<vector<int>> cnt(MAKS + 1, vector<int>(W, 0));
    for (int v = 3; v <= MAKS; v++)
        for (int a = 1; a < v - a; a++) cnt[v][g[a] ^ g[v - a]]++;

    int t;
    cin >> t;
    string out;
    while (t--) {
        int n;
        cin >> n;
        vector<int> a(n);
        int x = 0;                           // teorema Sprague–Grundy: XOR nilai Grundy
        for (auto& v : a) {
            cin >> v;
            x ^= g[v];
        }
        if (x == 0) {
            out += "Sinta\n";
            continue;
        }
        // Memecah tumpukan v menjadi a + b membuat XOR total menjadi
        // x ^ g[v] ^ g[a] ^ g[b]; langkah menang jika hasilnya 0,
        // yaitu g[a] ^ g[b] = x ^ g[v].
        long long banyak = 0;
        for (int v : a) banyak += cnt[v][x ^ g[v]];
        out += "Raka " + to_string(banyak) + "\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const MAKS = 2000;

// g[v] = nilai Grundy tumpukan v batu = mex{ g[a] ^ g[v - a] : 1 ≤ a < v - a }.
// Banyak pilihan < 1000, jadi semua nilai Grundy (dan XOR-nya) < 1024.
const g = new Int32Array(MAKS + 1);
const tanda = new Int32Array(1024).fill(-1);
let gMaks = 0;
for (let v = 3; v <= MAKS; v++) {
  for (let a = 1; a < v - a; a++) tanda[g[a] ^ g[v - a]] = v;
  let m = 0;
  while (tanda[m] === v) m++; // mex
  g[v] = m;
  if (m > gMaks) gMaks = m;
}

// cnt[v * W + t] = banyak cara memecah v dengan g[a] ^ g[v - a] = t
let W = 1;
while (W <= gMaks) W *= 2;
const cnt = new Int32Array((MAKS + 1) * W);
for (let v = 3; v <= MAKS; v++)
  for (let a = 1; a < v - a; a++) cnt[v * W + (g[a] ^ g[v - a])]++;

const t = Number(readLine());
const out = [];
for (let tc = 0; tc < t; tc++) {
  readLine(); // N
  const a = readInts();
  let x = 0; // Sprague–Grundy: XOR nilai Grundy semua tumpukan
  for (const v of a) x ^= g[v];
  if (x === 0) {
    out.push("Sinta");
    continue;
  }
  // langkah memecah v menang jika g[a] ^ g[b] = x ^ g[v]
  let banyak = 0;
  for (const v of a) banyak += cnt[v * W + (x ^ g[v])];
  out.push("Raka " + banyak);
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

MAKS = 2000

# g[v] = nilai Grundy tumpukan v batu = mex{ g[a] ^ g[v - a] : 1 <= a < v - a }
g = [0] * (MAKS + 1)
for v in range(3, MAKS + 1):
    bisa = {g[a] ^ g[v - a] for a in range(1, (v + 1) // 2)}
    m = 0
    while m in bisa:
        m += 1
    g[v] = m

# W = pangkat dua terkecil yang > max(g); XOR dua nilai < W tetap < W
W = 1
while W <= max(g):
    W *= 2
# cnt[v][t] = banyak cara memecah v dengan g[a] ^ g[v - a] = t
cnt = [[0] * W for _ in range(MAKS + 1)]
for v in range(3, MAKS + 1):
    baris = cnt[v]
    for a in range(1, (v + 1) // 2):
        baris[g[a] ^ g[v - a]] += 1

t = int(input())
out = []
for _ in range(t):
    n = int(input())
    a = list(map(int, input().split()))
    x = 0
    for v in a:
        x ^= g[v]            # teorema Sprague–Grundy
    if x == 0:
        out.append("Sinta")
        continue
    # langkah memecah v menang jika g[a] ^ g[b] = x ^ g[v]
    banyak = sum(cnt[v][x ^ g[v]] for v in a)
    out.append("Raka " + str(banyak))
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Setiap langkah hanya mengubah satu tumpukan, jadi permainan ini adalah <strong>gabungan</strong> N permainan satu tumpukan. Menurut <strong>teorema Sprague–Grundy</strong>, setiap permainan netral setara dengan satu tumpukan Nim berukuran nilai Grundy-nya, dan nilai Grundy gabungan permainan adalah XOR nilai Grundy komponennya. Posisi kalah tepat ketika nilainya 0.</p>
<p><strong>Nilai Grundy satu tumpukan.</strong> g(1) = g(2) = 0 karena tidak ada langkah. Memecah v menjadi a + (v − a) dengan a &lt; v − a menghasilkan <em>dua</em> tumpukan, yaitu gabungan dua permainan, sehingga nilai Grundy hasilnya <code>g(a) XOR g(v − a)</code>. Maka</p>
<p style="text-align:center"><code>g(v) = mex{ g(a) XOR g(v − a) : 1 ≤ a &lt; v − a }</code></p>
<p>dengan mex = bilangan cacah terkecil yang tidak ada di himpunan. Contohnya g(3) = 1, g(4) = 0, g(5) = 2, g(6) = 1, g(7) = 0, g(8) = 2. Menghitung semua g sampai 2000 butuh sekitar 2000² / 4 = 10<sup>6</sup> operasi, cukup sekali di awal. Nilai Grundy terbesarnya ternyata hanya 50.</p>
<p><strong>Pemenang.</strong> X = g(a<sub>1</sub>) XOR … XOR g(a<sub>N</sub>). Raka menang jika dan hanya jika X ≠ 0.</p>
<p><strong>Banyak langkah menang.</strong> Langkah menang adalah langkah menuju posisi bernilai 0. Memecah tumpukan v menjadi a + b mengubah nilai total menjadi X XOR g(v) XOR g(a) XOR g(b), jadi langkah itu menang tepat ketika <code>g(a) XOR g(b) = X XOR g(v)</code>. Siapkan tabel <code>cnt[v][t]</code> = banyak cara memecah v dengan g(a) XOR g(b) = t, dihitung dengan perulangan yang sama seperti g. Jawabannya Σ cnt[a<sub>i</sub>][X XOR g(a<sub>i</sub>)]. Lebar tabel cukup W = pangkat dua terkecil yang lebih besar dari nilai Grundy terbesar, karena XOR dua bilangan &lt; W tetap &lt; W.</p>
<p>Total O(2000² + ΣN). Perhatikan perbedaannya dengan Nim biasa: satu tumpukan bisa memberi <em>banyak</em> langkah menang, dan tumpukan yang nilai Grundy-nya 0 pun bisa memberi langkah menang (contoh 1, babak 4: tumpukan 7). Jangan pula menganggap nilai Grundy sama dengan ukuran tumpukan; itu hanya berlaku untuk Nim.</p>',
        'hints' => [
            'Satu langkah hanya mengubah satu tumpukan, jadi ini gabungan beberapa permainan. Hitung nilai Grundy setiap tumpukan lalu XOR-kan.',
            'Memecah v menjadi a dan v − a menghasilkan dua permainan sekaligus, nilainya g(a) XOR g(v − a). Jadi g(v) = mex dari semua nilai itu, untuk 1 ≤ a < v − a.',
            'Memecah tumpukan v menjadi a + b adalah langkah menang jika g(a) XOR g(b) = X XOR g(v). Hitung tabel cnt[v][t] sekali di awal.',
        ],
    ],
];
