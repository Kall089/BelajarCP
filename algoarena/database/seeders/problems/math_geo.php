<?php

use Database\Seeders\Support\TestGen as T;

/*
 * Soal latihan materi Geometri Dasar: hasil kali silang & orientasi, luas poligon (shoelace),
 * perpotongan dua ruas garis, dan convex hull (monotone chain Andrew).
 * Semua koordinat dan semua jawaban berupa bilangan bulat (luas dicetak 2 × luas atau dengan akhiran .5).
 * Semua kode C++ harus lolos C++14.
 */

$st = function (string $cppRead, string $jsRead = '', string $pyRead = ''): array {
    return [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    ios::sync_with_stdio(false);\n    cin.tie(nullptr);\n\n".$cppRead."\n\n    // Tulis solusimu di sini\n\n    return 0;\n}\n",
        'javascript' => ($jsRead !== '' ? $jsRead."\n\n" : "// Baca input dengan readLine() atau readInts()\n\n")."// Tulis solusimu di sini\n\n\nconsole.log();\n",
        'python' => "import sys\ninput = sys.stdin.readline\n\n".($pyRead !== '' ? $pyRead."\n\n" : '')."# Tulis solusimu di sini\n\n\nprint()\n",
    ];
};

/** Batas mutlak koordinat di semua soal. */
$M = 1000000;

/** Token-token input sebagai bilangan bulat (cepat untuk input besar). */
$tok = fn (string $input): array => array_map('intval', preg_split('/\s+/', trim($input)));

/** Susun input "banyak baris" + baris-barisnya. */
$kumpul = fn (array $rows): string => count($rows)."\n".implode("\n", $rows)."\n";

/** Daftar titik [[x, y], ...] menjadi input "N" + N baris. */
$titikInput = fn (array $pts): string => count($pts)."\n".implode("\n", array_map(fn ($p) => $p[0].' '.$p[1], $pts))."\n";

$gcd = function (int $a, int $b): int {
    while ($b !== 0) {
        $r = $a % $b;
        $a = $b;
        $b = $r;
    }

    return $a;
};

/** Semua vektor primitif (a, b) dengan |a| + |b| ≤ R, terurut menurut sudut (untuk poligon cembung bersudut banyak). */
$primitif = function (int $R) use ($gcd): array {
    $v = [];
    for ($a = -$R; $a <= $R; $a++) {
        for ($b = -$R; $b <= $R; $b++) {
            if (($a === 0 && $b === 0) || abs($a) + abs($b) > $R || $gcd(abs($a), abs($b)) !== 1) {
                continue;
            }
            $v[] = [$a, $b, atan2($b, $a)];
        }
    }
    usort($v, fn ($p, $q) => $p[2] <=> $q[2]);

    return $v;
};

/** Geser sekumpulan titik agar kotak pembatasnya berada di sekitar titik asal. */
$tengahkan = function (array $pts): array {
    $xs = array_column($pts, 0);
    $ys = array_column($pts, 1);
    $dx = intdiv(min($xs) + max($xs), 2);
    $dy = intdiv(min($ys) + max($ys), 2);

    return array_map(fn ($p) => [$p[0] - $dx, $p[1] - $dy], $pts);
};

return [
    [
        'slug' => 'arah-belokan-robot',
        'lesson' => 'geometri',
        'title' => 'Arah Belokan Robot',
        'difficulty' => 'Mudah',
        'tags' => ['geometri', 'hasil kali silang', 'orientasi'],
        'statement' => '<p>Dalam lomba robot pengantar paket, setiap robot berjalan lurus dari titik <strong>A</strong> ke titik <strong>B</strong>, lalu dari B berjalan lurus ke titik <strong>C</strong>. Juri ingin mencatat ke mana robot berbelok ketika tiba di B. Semua titik berada pada bidang Kartesius biasa: sumbu x mengarah ke kanan dan sumbu y mengarah ke atas.</p>
<p>Ada <strong>Q</strong> catatan perjalanan. Untuk setiap catatan, cetak <code>KIRI</code> jika robot berbelok ke kiri (berlawanan arah jarum jam), <code>KANAN</code> jika berbelok ke kanan (searah jarum jam), atau <code>LURUS</code> jika A, B, dan C terletak pada satu garis lurus. Kasus C berada di belakang robot (sehingga robot berbalik arah di B) juga termasuk <code>LURUS</code>.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi enam bilangan bulat <code>x<sub>A</sub> y<sub>A</sub> x<sub>B</sub> y<sub>B</sub> x<sub>C</sub> y<sub>C</sub></code>.</p>',
        'output_format' => '<p>Q baris; baris ke-i berisi <code>KIRI</code>, <code>KANAN</code>, atau <code>LURUS</code> untuk catatan ke-i.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 30 000</li><li>−10<sup>6</sup> ≤ semua koordinat ≤ 10<sup>6</sup></li><li>A ≠ B dan B ≠ C (A boleh sama dengan C)</li></ul>',
        'samples' => [
            ['input' => "4\n0 0 2 0 3 1\n0 0 2 0 3 -2\n0 0 2 2 5 5\n1 1 4 1 -2 1\n", 'explanation' => 'Pada catatan pertama robot bergerak ke kanan sepanjang sumbu x, lalu naik ke (3, 1): belok kiri. Catatan kedua turun ke (3, −2): belok kanan. Catatan ketiga tetap pada garis y = x. Pada catatan keempat C berada di belakang robot sehingga robot berbalik arah di (4, 1); ketiga titik tetap segaris, jadi jawabannya LURUS.'],
            ['input' => "1\n0 0 500000 0 0 300000\n", 'explanation' => 'Hasil kali silangnya 500 000 · 300 000 − 0 · 0 = 150 000 000 000 &gt; 0, jadi KIRI. Nilai ini jauh melewati batas <code>int</code> (sekitar 2,1 · 10<sup>9</sup>); jika dihitung dengan <code>int</code>, hasilnya meluap menjadi bilangan negatif dan jawabannya keliru menjadi KANAN.'],
        ],
        'tests' => function () use ($M, $kumpul) {
            // tiga titik acak dengan A ≠ B dan B ≠ C
            $acak = function (int $lo, int $hi) {
                do {
                    $p = [];
                    for ($k = 0; $k < 6; $k++) {
                        $p[] = mt_rand($lo, $hi);
                    }
                } while (($p[0] === $p[2] && $p[1] === $p[3]) || ($p[2] === $p[4] && $p[3] === $p[5]));

                return implode(' ', $p);
            };
            // tiga titik segaris: B = A + k1·d, C = A + k2·d
            $segaris = function (int $r, int $s, int $kmax) use ($M) {
                while (true) {
                    $dx = mt_rand(-$s, $s);
                    $dy = mt_rand(-$s, $s);
                    $k1 = mt_rand(-$kmax, $kmax);
                    $k2 = mt_rand(-$kmax, $kmax);
                    if (($dx === 0 && $dy === 0) || $k1 === 0 || $k1 === $k2) {
                        continue;
                    }
                    $ax = mt_rand(-$r, $r);
                    $ay = mt_rand(-$r, $r);
                    $p = [$ax, $ay, $ax + $k1 * $dx, $ay + $k1 * $dy, $ax + $k2 * $dx, $ay + $k2 * $dy];
                    if (max(array_map('abs', $p)) <= $M) {
                        return $p;
                    }
                }
            };
            // hampir segaris: C digeser satu satuan
            $hampir = function (int $r, int $s, int $kmax) use ($segaris, $M) {
                while (true) {
                    $p = $segaris($r, $s, $kmax);
                    $p[4 + mt_rand(0, 1)] += mt_rand(0, 1) ? 1 : -1;
                    if (abs($p[4]) <= $M && abs($p[5]) <= $M && ! ($p[2] === $p[4] && $p[3] === $p[5])) {
                        return implode(' ', $p);
                    }
                }
            };
            // koordinat di dekat batas ±10^6: hasil kali silang selalu raksasa
            $ujung = function () use ($M) {
                do {
                    $p = [];
                    for ($k = 0; $k < 6; $k++) {
                        $v = mt_rand($M - 3000, $M);
                        $p[] = mt_rand(0, 1) ? $v : -$v;
                    }
                } while (($p[0] === $p[2] && $p[1] === $p[3]) || ($p[2] === $p[4] && $p[3] === $p[5]));

                return implode(' ', $p);
            };
            $campur = function (int $q, array $gens) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $rows[] = $gens[mt_rand(0, count($gens) - 1)]();
                }

                return $rows;
            };
            $sg = fn (int $r, int $s, int $k) => fn () => implode(' ', $segaris($r, $s, $k));
            $hp = fn (int $r, int $s, int $k) => fn () => $hampir($r, $s, $k);
            $ac = fn (int $lo, int $hi) => fn () => $acak($lo, $hi);

            return [
                "1\n0 0 1 0 1 1\n",
                "6\n0 0 0 5 0 9\n0 0 0 5 0 -3\n0 0 0 5 -1 6\n0 0 0 5 1 6\n3 3 -2 -2 3 3\n5 -1 2 -1 2 -4\n",
                $kumpul($campur(300, [$ac(-3, 3)])),
                $kumpul($campur(2000, [$ac(-1000, 1000), $sg(1000, 10, 50), $hp(1000, 10, 50)])),
                $kumpul($campur(30000, [$ac(-$M, $M)])),
                $kumpul($campur(30000, [$ac(-$M, $M), $sg($M, 1000, 1000), $hp($M, 1000, 1000)])),
                $kumpul($campur(30000, [$ujung])),
                $kumpul($campur(30000, [$sg($M, 3, 300000), $hp($M, 3, 300000), $sg($M, 1, $M), $hp($M, 1, $M)])),
                $kumpul($campur(5000, [$ac(-20, 20), $sg($M, 100, 5000), $hp(100, 1, 10)])),
                "4\n-1000000 -1000000 1000000 1000000 1000000 -1000000\n1000000 -1000000 -1000000 1000000 1000000 -1000000\n-1000000 1000000 1000000 1000000 1000000 -1000000\n1000000 1000000 -1000000 -1000000 999999 1000000\n",
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            $q = $t[0];
            $out = [];
            for ($i = 0, $p = 1; $i < $q; $i++, $p += 6) {
                $s = ($t[$p + 2] - $t[$p]) * ($t[$p + 5] - $t[$p + 1]) - ($t[$p + 3] - $t[$p + 1]) * ($t[$p + 4] - $t[$p]);
                $out[] = $s > 0 ? 'KIRI' : ($s < 0 ? 'KANAN' : 'LURUS');
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int q;\n    cin >> q;\n    while (q--) {\n        long long ax, ay, bx, by, cx, cy;\n        cin >> ax >> ay >> bx >> by >> cx >> cy;\n    }",
            "const q = Number(readLine());\nfor (let i = 0; i < q; i++) {\n  const [ax, ay, bx, by, cx, cy] = readInts();\n}",
            "q = int(input())\nfor _ in range(q):\n    ax, ay, bx, by, cx, cy = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

struct Titik {
    long long x, y;
};

Titik operator-(Titik a, Titik b) { return {a.x - b.x, a.y - b.y}; }

// hasil kali silang u x v = u.x * v.y - u.y * v.x
long long silang(Titik u, Titik v) { return u.x * v.y - u.y * v.x; }

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    string out;
    while (q--) {
        Titik a, b, c;
        cin >> a.x >> a.y >> b.x >> b.y >> c.x >> c.y;
        // positif: C di sebelah kiri arah A -> B; negatif: di kanan; nol: segaris.
        // Nilainya bisa sampai 8 * 10^12, jadi wajib long long.
        long long s = silang(b - a, c - a);
        if (s > 0) out += "KIRI\n";
        else if (s < 0) out += "KANAN\n";
        else out += "LURUS\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [ax, ay, bx, by, cx, cy] = readInts();
  // (B - A) x (C - A); nilainya paling besar sekitar 8 * 10^12, masih tepat untuk Number
  const s = (bx - ax) * (cy - ay) - (by - ay) * (cx - ax);
  out.push(s > 0 ? "KIRI" : s < 0 ? "KANAN" : "LURUS");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

q = int(input())
out = []
for _ in range(q):
    ax, ay, bx, by, cx, cy = map(int, input().split())
    # (B - A) x (C - A): positif = kiri, negatif = kanan, nol = segaris
    s = (bx - ax) * (cy - ay) - (by - ay) * (cx - ax)
    out.append("KIRI" if s > 0 else "KANAN" if s < 0 else "LURUS")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Misalkan <code>u = B − A</code> (arah robot sebelum berbelok) dan <code>v = C − A</code>. Hasil kali silang</p>
<p style="text-align:center"><code>u × v = u<sub>x</sub> · v<sub>y</sub> − u<sub>y</sub> · v<sub>x</sub></code></p>
<p>bernilai positif jika v berada di sebelah kiri u (untuk memutar u ke arah v kita berputar berlawanan arah jarum jam), negatif jika v di sebelah kanan, dan nol jika u dan v segaris. Robot berbelok ke kiri di B tepat ketika C berada di sebelah kiri garis berarah A → B, jadi tanda <code>u × v</code> langsung memberi jawabannya.</p>
<p>Memakai <code>C − B</code> sebagai pengganti <code>C − A</code> memberi tanda yang sama, karena <code>(B − A) × (C − B) = (B − A) × (C − A) − (B − A) × (B − A)</code> dan suku terakhir selalu 0. Kasus berbalik arah (C di belakang) memberi hasil kali silang 0, sehingga tercetak LURUS sesuai soal.</p>
<p>Setiap catatan diproses dalam O(1), total O(Q). Semuanya dihitung dengan bilangan bulat, tanpa sudut dan tanpa <code>atan2</code>, sehingga kasus segaris terdeteksi secara pasti (bilangan desimal bisa memberi 10<sup>−12</sup> alih-alih 0).</p>
<p><strong>Jebakan overflow:</strong> selisih koordinat bisa mencapai 2 · 10<sup>6</sup>, sehingga tiap perkalian sampai 4 · 10<sup>12</sup> dan hasil kali silang sampai 8 · 10<sup>12</sup>, jauh di atas batas <code>int</code>. Pakai <code>long long</code> di C++ (lihat contoh kedua). Di JavaScript nilai ini masih di bawah 9 · 10<sup>15</sup>, jadi Number sudah tepat.</p>',
        'hints' => [
            'Bentuk vektor u = B − A dan v = C − A. Besaran apa yang tandanya memberi tahu apakah v berada di kiri atau di kanan u?',
            'Hasil kali silang u × v = u.x · v.y − u.y · v.x: positif berarti belok kiri, negatif belok kanan, nol berarti segaris.',
            'Nilainya bisa sampai 8 · 10^12. Simpan dalam long long, jangan int.',
        ],
    ],

    [
        'slug' => 'luas-tanah-berpatok',
        'lesson' => 'geometri',
        'title' => 'Luas Tanah Berpatok',
        'difficulty' => 'Sedang',
        'tags' => ['geometri', 'rumus shoelace', 'luas poligon'],
        'statement' => '<p>Pak Darmo membeli sebidang tanah yang batasnya ditandai <strong>N</strong> patok. Juru ukur mencatat koordinat patok-patok itu secara berurutan menyusuri batas tanah, entah searah atau berlawanan arah jarum jam. Patok terakhir tersambung kembali ke patok pertama.</p>
<p>Batas tanah membentuk poligon sederhana: sisi-sisinya tidak saling memotong dan hanya bertemu di patok yang berdampingan. Tanahnya tidak harus cembung; bentuknya bisa seperti huruf L, sisir, atau bahkan spiral.</p>
<p>Berapa luas tanah Pak Darmo?</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya berisi koordinat patok <code>x<sub>i</sub> y<sub>i</sub></code> sesuai urutan di sepanjang batas.</p>',
        'output_format' => '<p>Luas tanah. Luasnya selalu bilangan bulat atau bilangan bulat ditambah setengah. Jika bulat, cetak tanpa desimal (misalnya <code>6</code>); jika tidak, cetak bagian bulatnya diikuti <code>.5</code> (misalnya <code>4.5</code>).</p>',
        'constraints' => '<ul><li>3 ≤ N ≤ 10<sup>5</sup></li><li>−10<sup>6</sup> ≤ x<sub>i</sub>, y<sub>i</sub> ≤ 10<sup>6</sup></li><li>Semua patok berbeda dan poligonnya sederhana dengan luas positif (tiga patok berurutan boleh segaris).</li></ul>',
        'samples' => [
            ['input' => "6\n0 0\n4 0\n4 1\n1 1\n1 3\n0 3\n", 'explanation' => 'Tanah berbentuk huruf L: persegi panjang 4 × 1 di bawah ditambah persegi panjang 1 × 2 di atasnya, luasnya 4 + 2 = 6.'],
            ['input' => "3\n0 0\n1 3\n3 0\n", 'explanation' => 'Segitiga dengan alas 3 dan tinggi 3. Patoknya ditulis searah jarum jam, sehingga jumlah shoelace-nya (0·3 − 1·0) + (1·0 − 3·3) + (3·0 − 0·0) = −9. Luasnya |−9| / 2 = 4,5 dan dicetak <code>4.5</code>.'],
        ],
        'tests' => function () use ($M, $titikInput, $gcd, $primitif, $tengahkan) {
            // poligon bintang: titik acak diurutkan menurut sudut terhadap pusat
            $bintang = function (int $n, int $r) use ($gcd) {
                while (true) {
                    $cx = mt_rand(-intdiv($r, 4), intdiv($r, 4));
                    $cy = mt_rand(-intdiv($r, 4), intdiv($r, 4));
                    $arah = [];
                    $guard = 0;
                    while (count($arah) < $n && $guard++ < 50 * $n) {
                        $x = mt_rand(-$r, $r);
                        $y = mt_rand(-$r, $r);
                        $dx = $x - $cx;
                        $dy = $y - $cy;
                        if ($dx === 0 && $dy === 0) {
                            continue;
                        }
                        $g = $gcd(abs($dx), abs($dy));
                        $key = intdiv($dx, $g).','.intdiv($dy, $g);
                        if (! isset($arah[$key])) {
                            $arah[$key] = [$x, $y, atan2($dy, $dx)];
                        }
                    }
                    $pts = array_values($arah);
                    usort($pts, fn ($a, $b) => $a[2] <=> $b[2]);
                    $m = count($pts);
                    $ok = $m >= 3;
                    for ($i = 0; $ok && $i < $m; $i++) {
                        $celah = ($i + 1 < $m ? $pts[$i + 1][2] : $pts[0][2] + 2 * M_PI) - $pts[$i][2];
                        if ($celah >= M_PI - 1e-9) {
                            $ok = false;
                        }
                    }
                    if ($ok) {
                        return array_map(fn ($p) => [$p[0], $p[1]], $pts);
                    }
                }
            };
            // histogram (sisir): alas di y = -L, m kolom dengan tinggi acak
            $sisir = function (int $m, int $L) {
                $cut = [];
                while (count($cut) < $m - 1) {
                    $cut[mt_rand(-$L + 1, $L - 1)] = true;
                }
                $xs = array_keys($cut);
                sort($xs);
                $xs = array_merge([-$L], $xs, [$L]);
                $h = [];
                for ($j = 0; $j < $m; $j++) {
                    do {
                        $v = mt_rand(-$L + 1, $L);
                    } while ($j > 0 && $v === $h[$j - 1]);
                    $h[] = $v;
                }
                $pts = [[-$L, -$L], [$L, -$L]];
                for ($j = $m - 1; $j >= 0; $j--) {
                    $pts[] = [$xs[$j + 1], $h[$j]];
                    $pts[] = [$xs[$j], $h[$j]];
                }

                return $pts;
            };
            // lorong spiral selebar 2: garis tengah spiral persegi ke dalam, digeser 1 ke kiri dan ke kanan
            $spiral = function (int $L, int $d, int $maxN) {
                $s = [[-$L, -$L]];
                $kiri = -$L;
                $kanan = $L;
                $bawah = -$L;
                $atas = $L;
                $arah = 0;
                while (2 * (count($s) + 1) <= $maxN && $kanan - $kiri > 4 * $d && $atas - $bawah > 4 * $d) {
                    [$x, $y] = $s[count($s) - 1];
                    if ($arah === 0) {
                        $s[] = [$kanan, $y];
                        $bawah += $d;
                    } elseif ($arah === 1) {
                        $s[] = [$x, $atas];
                        $kanan -= $d;
                    } elseif ($arah === 2) {
                        $s[] = [$kiri, $y];
                        $atas -= $d;
                    } else {
                        $s[] = [$x, $bawah];
                        $kiri += $d;
                    }
                    $arah = ($arah + 1) % 4;
                }
                $m = count($s);
                $sisiKiri = [];
                $sisiKanan = [];
                for ($i = 0; $i < $m; $i++) {
                    $nx = 0;
                    $ny = 0;
                    if ($i > 0) {
                        $nx -= $s[$i][1] <=> $s[$i - 1][1];
                        $ny += $s[$i][0] <=> $s[$i - 1][0];
                    }
                    if ($i + 1 < $m) {
                        $nx -= $s[$i + 1][1] <=> $s[$i][1];
                        $ny += $s[$i + 1][0] <=> $s[$i][0];
                    }
                    $sisiKiri[] = [$s[$i][0] + $nx, $s[$i][1] + $ny];
                    $sisiKanan[] = [$s[$i][0] - $nx, $s[$i][1] - $ny];
                }

                return array_merge($sisiKiri, array_reverse($sisiKanan));
            };
            // poligon cembung dari vektor primitif, tiap sisi diperbesar c kali
            $cembung = function (int $R, int $c) use ($primitif, $tengahkan) {
                $x = 0;
                $y = 0;
                $pts = [];
                foreach ($primitif($R) as $v) {
                    $pts[] = [$x, $y];
                    $x += $c * $v[0];
                    $y += $c * $v[1];
                }

                return $tengahkan($pts);
            };
            // acak arah penulisan dan patok awal
            $acakUrut = function (array $pts) {
                if (mt_rand(0, 1)) {
                    $pts = array_reverse($pts);
                }
                $k = mt_rand(0, count($pts) - 1);

                return array_merge(array_slice($pts, $k), array_slice($pts, 0, $k));
            };

            return [
                "3\n0 0\n1 0\n0 1\n",
                "4\n-1000000 -1000000\n-1000000 1000000\n1000000 1000000\n1000000 -1000000\n",
                "3\n-1000000 -1000000\n1000000 -999999\n-999999 1000000\n",
                "5\n0 0\n2 0\n4 0\n4 3\n0 3\n",
                $titikInput($acakUrut($bintang(8, 10))),
                $titikInput($acakUrut($bintang(1000, 1000))),
                $titikInput($acakUrut($bintang(100000, $M))),
                $titikInput($acakUrut($sisir(30, 50))),
                $titikInput($acakUrut($sisir(49999, $M))),
                $titikInput($spiral($M - 2, 80, 100000)),
                $titikInput(array_reverse($spiral($M - 2, 97, 100000))),
                $titikInput($acakUrut($cembung(100, 4))),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            $n = $t[0];
            $s = 0;
            for ($i = 0; $i < $n; $i++) {
                $j = ($i + 1) % $n;
                $s += $t[1 + 2 * $i] * $t[2 + 2 * $j] - $t[1 + 2 * $j] * $t[2 + 2 * $i];
            }
            $s = abs($s);

            return intdiv($s, 2).($s % 2 === 1 ? '.5' : '');
        },
        'starter' => $st(
            "    int n;\n    cin >> n;\n    vector<long long> x(n), y(n);\n    for (int i = 0; i < n; i++) cin >> x[i] >> y[i];",
            "const n = Number(readLine());\nconst x = [], y = [];\nfor (let i = 0; i < n; i++) {\n  const [a, b] = readInts();\n  x.push(a);\n  y.push(b);\n}",
            "n = int(input())\nx = [0] * n\ny = [0] * n\nfor i in range(n):\n    x[i], y[i] = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<long long> x(n), y(n);
    for (int i = 0; i < n; i++) cin >> x[i] >> y[i];

    // Rumus shoelace: s = jumlah (x_i * y_{i+1} - x_{i+1} * y_i) = 2 * luas bertanda.
    // Tiap suku sampai 2 * 10^12 dan jumlah sementaranya bisa jauh lebih besar,
    // jadi semuanya long long.
    long long s = 0;
    for (int i = 0; i < n; i++) {
        int j = (i + 1) % n;            // patok berikutnya (melingkar)
        s += x[i] * y[j] - x[j] * y[i];
    }
    if (s < 0) s = -s;                  // patok searah jarum jam memberi nilai negatif

    // luas = s / 2: bulat jika s genap, berakhiran .5 jika s ganjil
    cout << s / 2;
    if (s % 2 == 1) cout << ".5";
    cout << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const x = [], y = [];
for (let i = 0; i < n; i++) {
  const [a, b] = readInts();
  x.push(a);
  y.push(b);
}
// Setiap suku |x_i * y_j - x_j * y_i| <= 2 * 10^12 masih tepat sebagai Number,
// tetapi jumlah sementaranya bisa melewati 2^53 (misalnya tanah berbentuk spiral),
// jadi akumulasinya memakai BigInt.
let s = 0n;
for (let i = 0; i < n; i++) {
  const j = (i + 1) % n;
  s += BigInt(x[i] * y[j] - x[j] * y[i]);
}
if (s < 0n) s = -s;
const bulat = s / 2n; // pembagian BigInt membulatkan ke bawah untuk s >= 0
console.log(s % 2n === 0n ? bulat.toString() : bulat.toString() + ".5");
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline

n = int(input())
x = [0] * n
y = [0] * n
for i in range(n):
    x[i], y[i] = map(int, input().split())

# rumus shoelace: s = 2 * luas bertanda
s = 0
for i in range(n):
    j = i + 1 if i + 1 < n else 0
    s += x[i] * y[j] - x[j] * y[i]
s = abs(s)
print(s // 2 if s % 2 == 0 else f"{s // 2}.5")
CODE,
        ],
        'editorial' => '<p>Pilih titik asal O. Untuk setiap sisi P<sub>i</sub>P<sub>i+1</sub> (indeks dibaca melingkar, jadi setelah P<sub>N</sub> kembali ke P<sub>1</sub>), luas bertanda segitiga O, P<sub>i</sub>, P<sub>i+1</sub> adalah <code>(x<sub>i</sub> · y<sub>i+1</sub> − x<sub>i+1</sub> · y<sub>i</sub>) / 2</code>, yaitu setengah hasil kali silang P<sub>i</sub> × P<sub>i+1</sub>. Saat kita berkeliling batas tanah, segitiga-segitiga yang menutupi daerah di luar tanah muncul sekali bertanda positif dan sekali bertanda negatif sehingga saling menghapus, dan yang tersisa tepat luas poligon. Inilah <strong>rumus shoelace</strong>:</p>
<p style="text-align:center"><code>2 · luas = | Σ (x<sub>i</sub> · y<sub>i+1</sub> − x<sub>i+1</sub> · y<sub>i</sub>) |</code></p>
<p>Jumlahnya positif jika patok ditulis berlawanan arah jarum jam dan negatif jika searah, karena itu diambil nilai mutlaknya. Rumus ini berlaku untuk semua poligon sederhana, cembung maupun tidak. Waktunya O(N).</p>
<p>Karena semua koordinat bulat, <code>S = 2 · luas</code> adalah bilangan bulat. Luas = S / 2: jika S genap cetak S / 2, jika S ganjil cetak ⌊S / 2⌋ diikuti <code>.5</code>. Jangan memakai <code>double</code> untuk luas: selain berisiko kehilangan presisi, format keluarannya juga harus persis.</p>
<p><strong>Batas nilai:</strong> tanah berada di dalam persegi berukuran 2 · 10<sup>6</sup> × 2 · 10<sup>6</sup>, sehingga S akhir paling besar 8 · 10<sup>12</sup>. Namun setiap suku bisa sampai 2 · 10<sup>12</sup> (sudah di luar <code>int</code>), dan <em>jumlah sementara</em> tidak dibatasi oleh luas akhir: pada tanah berbentuk spiral, segitiga-segitiga dari titik asal melilit berkali-kali, sehingga jumlah sementara bisa melewati 3 · 10<sup>16</sup> sebelum turun lagi (salah satu tes memang seperti ini). Batas kasarnya N · 2 · 10<sup>12</sup> = 2 · 10<sup>17</sup>, masih aman untuk <code>long long</code> (sekitar 9,2 · 10<sup>18</sup>), tetapi sudah di atas 2<sup>53</sup> ≈ 9 · 10<sup>15</sup>, batas bilangan bulat yang tepat untuk Number di JavaScript. Karena itu solusi JavaScript menghitung setiap suku dengan Number (masih tepat) lalu menjumlahkannya dengan BigInt. Python tidak punya masalah ini.</p>',
        'hints' => [
            'Pecah poligon menjadi segitiga yang semuanya memakai satu titik tetap, misalnya titik asal O. Luas bertanda segitiga O, P_i, P_{i+1} mudah dihitung dengan hasil kali silang.',
            'Rumus shoelace: 2 × luas = |Σ (x_i · y_{i+1} − x_{i+1} · y_i)| dengan indeks melingkar. Tanda jumlahnya hanya bergantung pada arah penulisan patok.',
            'Jumlah S selalu bulat. Cetak S / 2, ditambah ".5" jika S ganjil. Pakai long long (di JavaScript, akumulasikan dengan BigInt).',
        ],
    ],

    [
        'slug' => 'kabel-bersentuhan',
        'lesson' => 'geometri',
        'title' => 'Kabel yang Bersentuhan',
        'difficulty' => 'Sedang',
        'tags' => ['geometri', 'perpotongan ruas garis', 'orientasi'],
        'statement' => '<p>Seorang teknisi merakit papan rangkaian. Setiap kabel dipasang lurus di antara dua titik berkoordinat bulat, jadi bentuknya berupa ruas garis. Dua kabel yang bersentuhan, walau hanya di satu titik, akan menyebabkan korsleting. Misalnya keduanya bersilangan, ujung satu kabel menempel pada kabel lain, ujung keduanya bertemu, atau keduanya segaris dan saling bertumpuk.</p>
<p>Ada <strong>Q</strong> pasang kabel yang ingin diperiksa. Untuk setiap pasang, tentukan apakah kedua kabel mempunyai paling sedikit satu titik persekutuan.</p>',
        'input_format' => '<p>Baris pertama berisi <code>Q</code>. Q baris berikutnya masing-masing berisi delapan bilangan bulat <code>x<sub>1</sub> y<sub>1</sub> x<sub>2</sub> y<sub>2</sub> x<sub>3</sub> y<sub>3</sub> x<sub>4</sub> y<sub>4</sub></code>: kabel pertama menghubungkan (x<sub>1</sub>, y<sub>1</sub>) dan (x<sub>2</sub>, y<sub>2</sub>), kabel kedua menghubungkan (x<sub>3</sub>, y<sub>3</sub>) dan (x<sub>4</sub>, y<sub>4</sub>).</p>',
        'output_format' => '<p>Q baris; baris ke-i berisi <code>YA</code> jika kedua kabel pada pasangan ke-i bersentuhan, atau <code>TIDAK</code> jika tidak.</p>',
        'constraints' => '<ul><li>1 ≤ Q ≤ 20 000</li><li>−10<sup>6</sup> ≤ semua koordinat ≤ 10<sup>6</sup></li><li>(x<sub>1</sub>, y<sub>1</sub>) ≠ (x<sub>2</sub>, y<sub>2</sub>) dan (x<sub>3</sub>, y<sub>3</sub>) ≠ (x<sub>4</sub>, y<sub>4</sub>)</li></ul>',
        'samples' => [
            ['input' => "6\n0 0 4 4 0 4 4 0\n0 0 4 0 2 0 2 3\n0 0 2 2 2 2 5 0\n0 0 4 0 2 0 7 0\n0 0 2 0 3 0 5 0\n0 0 4 0 2 1 2 3\n", 'explanation' => 'Pasangan 1 bersilangan di (2, 2). Pada pasangan 2, ujung (2, 0) kabel kedua menempel di tengah kabel pertama. Pasangan 3 bertemu di ujung (2, 2). Pasangan 4 sama-sama terletak pada sumbu x dan bertumpuk dari (2, 0) sampai (4, 0). Pasangan 5 juga segaris, tetapi ada celah antara x = 2 dan x = 3. Pada pasangan 6, kabel kedua berhenti di (2, 1), satu satuan di atas kabel pertama.'],
            ['input' => "2\n-1000000 -1000000 1000000 999999 -1000000 -999999 1000000 1000000\n-1000000 -1000000 1000000 1000000 -1000000 1000000 1000000 -1000000\n", 'explanation' => 'Pada pasangan pertama, kabel kedua berada 1 satuan di atas kabel pertama di kedua ujungnya, jadi di seluruh panjangnya kabel kedua tetap di atas: hampir sejajar tetapi tidak bersentuhan. Pasangan kedua adalah dua diagonal persegi besar yang bersilangan di (0, 0).'],
        ],
        'tests' => function () use ($M, $kumpul, $gcd) {
            $dalam = fn (array $p) => max(array_map('abs', $p)) <= $M;
            $sah = fn (array $p) => ! ($p[0] === $p[2] && $p[1] === $p[3]) && ! ($p[4] === $p[6] && $p[5] === $p[7]);
            // arah acak bukan nol, |dx|, |dy| ≤ s
            $arahAcak = function (int $s) {
                do {
                    $dx = mt_rand(-$s, $s);
                    $dy = mt_rand(-$s, $s);
                } while ($dx === 0 && $dy === 0);

                return [$dx, $dy];
            };
            $acak = function (int $r) use ($sah) {
                do {
                    $p = [];
                    for ($k = 0; $k < 8; $k++) {
                        $p[] = mt_rand(-$r, $r);
                    }
                } while (! $sah($p));

                return implode(' ', $p);
            };
            // dua ruas pada satu garis A + t·d, kadang tepat menempel atau berjarak satu langkah
            $segaris = function (int $r, int $s, int $kmax) use ($arahAcak, $dalam, $sah) {
                while (true) {
                    [$dx, $dy] = $arahAcak($s);
                    $ax = mt_rand(-$r, $r);
                    $ay = mt_rand(-$r, $r);
                    $t = [mt_rand(-$kmax, $kmax), mt_rand(-$kmax, $kmax), 0, mt_rand(-$kmax, $kmax)];
                    $jenis = mt_rand(0, 2);
                    $t[2] = $jenis === 0 ? $t[1] : ($jenis === 1 ? $t[1] + (mt_rand(0, 1) ? 1 : -1) : mt_rand(-$kmax, $kmax));
                    if (mt_rand(0, 1)) {
                        [$t[2], $t[3]] = [$t[3], $t[2]];
                    }
                    $p = [];
                    foreach ($t as $k) {
                        $p[] = $ax + $k * $dx;
                        $p[] = $ay + $k * $dy;
                    }
                    if ($dalam($p) && $sah($p)) {
                        return implode(' ', $p);
                    }
                }
            };
            // dua ruas sejajar tetapi tidak segaris
            $sejajar = function (int $r, int $s, int $kmax) use ($arahAcak, $dalam, $sah) {
                while (true) {
                    [$dx, $dy] = $arahAcak($s);
                    [$ex, $ey] = $arahAcak(3);
                    if ($dx * $ey - $dy * $ex === 0) {
                        continue;
                    }
                    $ax = mt_rand(-$r, $r);
                    $ay = mt_rand(-$r, $r);
                    $p = [];
                    for ($k = 0; $k < 4; $k++) {
                        $tk = mt_rand(-$kmax, $kmax);
                        $p[] = $ax + $tk * $dx + ($k >= 2 ? $ex : 0);
                        $p[] = $ay + $tk * $dy + ($k >= 2 ? $ey : 0);
                    }
                    if ($dalam($p) && $sah($p)) {
                        return implode(' ', $p);
                    }
                }
            };
            // ujung kabel kedua tepat di titik kisi pada kabel pertama, atau digeser satu satuan (nyaris)
            $menempel = function (int $r, int $s, int $kmax, bool $nyaris) use ($arahAcak, $dalam, $sah) {
                while (true) {
                    [$dx, $dy] = $arahAcak($s);
                    $ax = mt_rand(-$r, $r);
                    $ay = mt_rand(-$r, $r);
                    $t1 = mt_rand(-$kmax, $kmax);
                    $t2 = mt_rand(-$kmax, $kmax);
                    $tp = mt_rand(min($t1, $t2), max($t1, $t2));
                    $px = $ax + $tp * $dx;
                    $py = $ay + $tp * $dy;
                    if ($nyaris) {
                        $px += mt_rand(-1, 1);
                        $py += mt_rand(-1, 1);
                    }
                    [$ex, $ey] = $arahAcak(mt_rand(0, 1) ? $s : 1000);
                    $c = mt_rand(1, 50);
                    $q = [$px, $py, $px + $c * $ex, $py + $c * $ey];
                    if (mt_rand(0, 1)) {
                        $q = [$q[2], $q[3], $q[0], $q[1]];
                    }
                    $p = array_merge([$ax + $t1 * $dx, $ay + $t1 * $dy, $ax + $t2 * $dx, $ay + $t2 * $dy], $q);
                    if (mt_rand(0, 1)) {
                        $p = array_merge(array_slice($p, 4), array_slice($p, 0, 4));
                    }
                    if ($dalam($p) && $sah($p)) {
                        return implode(' ', $p);
                    }
                }
            };
            // ruas kedua melewati titik kisi P di kabel pertama (bersilangan di P)
            $silang = function (int $r, int $s, int $kmax) use ($arahAcak, $dalam, $sah) {
                while (true) {
                    [$dx, $dy] = $arahAcak($s);
                    [$ex, $ey] = $arahAcak($s);
                    $ax = mt_rand(-$r, $r);
                    $ay = mt_rand(-$r, $r);
                    $t1 = mt_rand(-$kmax, $kmax);
                    $t2 = mt_rand(-$kmax, $kmax);
                    $tp = mt_rand(min($t1, $t2), max($t1, $t2));
                    $px = $ax + $tp * $dx;
                    $py = $ay + $tp * $dy;
                    $u1 = mt_rand(1, $kmax);
                    $u2 = mt_rand(1, $kmax);
                    $p = [$ax + $t1 * $dx, $ay + $t1 * $dy, $ax + $t2 * $dx, $ay + $t2 * $dy, $px - $u1 * $ex, $py - $u1 * $ey, $px + $u2 * $ex, $py + $u2 * $ey];
                    if ($dalam($p) && $sah($p)) {
                        return implode(' ', $p);
                    }
                }
            };
            // dua kabel panjang hampir sejajar di dekat batas koordinat
            $hampirSejajar = function () use ($M, $sah) {
                while (true) {
                    $y1 = mt_rand(-$M, $M - 2);
                    $y2 = mt_rand(-$M, $M - 2);
                    $a = mt_rand(-2, 2);
                    $b = mt_rand(-2, 2);
                    $p = [-$M, $y1, $M, $y2, -$M, $y1 + $a, $M, $y2 + $b];
                    if (max(array_map('abs', $p)) <= $M && $sah($p)) {
                        if (mt_rand(0, 1)) {
                            $p = [$p[1], $p[0], $p[3], $p[2], $p[5], $p[4], $p[7], $p[6]];
                        }

                        return implode(' ', $p);
                    }
                }
            };
            $campur = function (int $q, array $gens) {
                $rows = [];
                for ($i = 0; $i < $q; $i++) {
                    $rows[] = $gens[mt_rand(0, count($gens) - 1)]();
                }

                return $rows;
            };
            $g = [
                'acak' => fn (int $r) => fn () => $acak($r),
                'segaris' => fn (int $r, int $s, int $k) => fn () => $segaris($r, $s, $k),
                'sejajar' => fn (int $r, int $s, int $k) => fn () => $sejajar($r, $s, $k),
                'tempel' => fn (int $r, int $s, int $k) => fn () => $menempel($r, $s, $k, false),
                'nyaris' => fn (int $r, int $s, int $k) => fn () => $menempel($r, $s, $k, true),
                'silang' => fn (int $r, int $s, int $k) => fn () => $silang($r, $s, $k),
            ];

            return [
                "1\n0 0 1 1 1 0 0 1\n",
                "10\n0 0 10 0 10 0 20 0\n0 0 10 0 11 0 20 0\n0 0 10 10 3 3 5 5\n0 0 0 10 0 -1 0 -5\n0 0 4 2 2 1 6 -3\n0 0 4 2 6 3 2 1\n0 0 4 2 2 2 6 -3\n-1000000 -1000000 1000000 1000000 -1000000 1000000 1000000 -1000000\n0 0 2 2 1 0 3 2\n5 5 5 -5 5 5 5 -5\n",
                $kumpul($campur(500, [$g['acak'](3)])),
                $kumpul($campur(2000, [$g['acak'](20), $g['segaris'](20, 3, 6), $g['sejajar'](20, 3, 6), $g['tempel'](20, 3, 6), $g['nyaris'](20, 3, 6)])),
                $kumpul($campur(20000, [$g['acak']($M)])),
                $kumpul($campur(20000, [$g['segaris']($M, 1000, 1000), $g['segaris']($M, 5, 100000), $g['sejajar']($M, 1000, 1000)])),
                $kumpul($campur(20000, [$g['tempel']($M, 1000, 1000), $g['nyaris']($M, 1000, 1000), $g['tempel']($M, 3, 300000), $g['nyaris']($M, 3, 300000)])),
                $kumpul($campur(20000, [$g['silang']($M, 1000, 1000), $hampirSejajar, $g['acak']($M), $g['nyaris']($M, 50, 20000)])),
                $kumpul($campur(20000, [$g['acak'](1000), $g['segaris'](1000, 10, 100), $g['sejajar'](1000, 10, 100), $g['tempel'](1000, 10, 100), $g['nyaris'](1000, 10, 100), $g['silang'](1000, 10, 100)])),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            $q = $t[0];
            $ori = fn ($ax, $ay, $bx, $by, $cx, $cy) => (($bx - $ax) * ($cy - $ay) - ($by - $ay) * ($cx - $ax)) <=> 0;
            $diRuas = fn ($ax, $ay, $bx, $by, $cx, $cy) => min($ax, $bx) <= $cx && $cx <= max($ax, $bx) && min($ay, $by) <= $cy && $cy <= max($ay, $by);
            $out = [];
            for ($i = 0, $p = 1; $i < $q; $i++, $p += 8) {
                [$x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4] = array_slice($t, $p, 8);
                $d1 = $ori($x3, $y3, $x4, $y4, $x1, $y1);
                $d2 = $ori($x3, $y3, $x4, $y4, $x2, $y2);
                $d3 = $ori($x1, $y1, $x2, $y2, $x3, $y3);
                $d4 = $ori($x1, $y1, $x2, $y2, $x4, $y4);
                $ya = ($d1 * $d2 < 0 && $d3 * $d4 < 0)
                    || ($d1 === 0 && $diRuas($x3, $y3, $x4, $y4, $x1, $y1))
                    || ($d2 === 0 && $diRuas($x3, $y3, $x4, $y4, $x2, $y2))
                    || ($d3 === 0 && $diRuas($x1, $y1, $x2, $y2, $x3, $y3))
                    || ($d4 === 0 && $diRuas($x1, $y1, $x2, $y2, $x4, $y4));
                $out[] = $ya ? 'YA' : 'TIDAK';
            }

            return implode("\n", $out);
        },
        'starter' => $st(
            "    int q;\n    cin >> q;\n    while (q--) {\n        long long x1, y1, x2, y2, x3, y3, x4, y4;\n        cin >> x1 >> y1 >> x2 >> y2 >> x3 >> y3 >> x4 >> y4;\n    }",
            "const q = Number(readLine());\nfor (let i = 0; i < q; i++) {\n  const [x1, y1, x2, y2, x3, y3, x4, y4] = readInts();\n}",
            "q = int(input())\nfor _ in range(q):\n    x1, y1, x2, y2, x3, y3, x4, y4 = map(int, input().split())"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

struct Titik {
    long long x, y;
};

// Tanda dari (B - A) x (C - A): 1 = C di kiri garis A->B, -1 = di kanan, 0 = segaris.
// Yang dikembalikan hanya tandanya, supaya tidak perlu mengalikan dua nilai raksasa.
int orientasi(Titik a, Titik b, Titik c) {
    long long s = (b.x - a.x) * (c.y - a.y) - (b.y - a.y) * (c.x - a.x);
    return (s > 0) - (s < 0);
}

// Dipakai jika c sudah pasti segaris dengan a dan b: apakah c ada di dalam ruas ab?
bool diRuas(Titik a, Titik b, Titik c) {
    return min(a.x, b.x) <= c.x && c.x <= max(a.x, b.x) &&
           min(a.y, b.y) <= c.y && c.y <= max(a.y, b.y);
}

bool bersentuhan(Titik a, Titik b, Titik c, Titik d) {
    int d1 = orientasi(c, d, a), d2 = orientasi(c, d, b);
    int d3 = orientasi(a, b, c), d4 = orientasi(a, b, d);
    // kasus umum: a dan b di sisi berbeda garis cd, c dan d di sisi berbeda garis ab
    if (d1 * d2 < 0 && d3 * d4 < 0) return true;
    // kasus menempel / segaris: salah satu ujung terletak di ruas yang lain
    if (d1 == 0 && diRuas(c, d, a)) return true;
    if (d2 == 0 && diRuas(c, d, b)) return true;
    if (d3 == 0 && diRuas(a, b, c)) return true;
    if (d4 == 0 && diRuas(a, b, d)) return true;
    return false;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int q;
    cin >> q;
    string out;
    while (q--) {
        Titik a, b, c, d;
        cin >> a.x >> a.y >> b.x >> b.y >> c.x >> c.y >> d.x >> d.y;
        out += bersentuhan(a, b, c, d) ? "YA\n" : "TIDAK\n";
    }
    cout << out;
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
// tanda (B - A) x (C - A); nilainya paling besar 8 * 10^12, masih tepat untuk Number
function orientasi(ax, ay, bx, by, cx, cy) {
  return Math.sign((bx - ax) * (cy - ay) - (by - ay) * (cx - ax));
}
// c sudah pasti segaris dengan a dan b: apakah c di dalam ruas ab?
function diRuas(ax, ay, bx, by, cx, cy) {
  return Math.min(ax, bx) <= cx && cx <= Math.max(ax, bx) &&
         Math.min(ay, by) <= cy && cy <= Math.max(ay, by);
}

const q = Number(readLine());
const out = [];
for (let i = 0; i < q; i++) {
  const [x1, y1, x2, y2, x3, y3, x4, y4] = readInts();
  const d1 = orientasi(x3, y3, x4, y4, x1, y1);
  const d2 = orientasi(x3, y3, x4, y4, x2, y2);
  const d3 = orientasi(x1, y1, x2, y2, x3, y3);
  const d4 = orientasi(x1, y1, x2, y2, x4, y4);
  const ya =
    (d1 * d2 < 0 && d3 * d4 < 0) ||
    (d1 === 0 && diRuas(x3, y3, x4, y4, x1, y1)) ||
    (d2 === 0 && diRuas(x3, y3, x4, y4, x2, y2)) ||
    (d3 === 0 && diRuas(x1, y1, x2, y2, x3, y3)) ||
    (d4 === 0 && diRuas(x1, y1, x2, y2, x4, y4));
  out.push(ya ? "YA" : "TIDAK");
}
console.log(out.join("\n"));
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline


def orientasi(ax, ay, bx, by, cx, cy):
    # tanda (B - A) x (C - A): 1 kiri, -1 kanan, 0 segaris
    s = (bx - ax) * (cy - ay) - (by - ay) * (cx - ax)
    return (s > 0) - (s < 0)


def di_ruas(ax, ay, bx, by, cx, cy):
    # c sudah pasti segaris dengan a dan b: apakah c di dalam ruas ab?
    return min(ax, bx) <= cx <= max(ax, bx) and min(ay, by) <= cy <= max(ay, by)


q = int(input())
out = []
for _ in range(q):
    x1, y1, x2, y2, x3, y3, x4, y4 = map(int, input().split())
    d1 = orientasi(x3, y3, x4, y4, x1, y1)
    d2 = orientasi(x3, y3, x4, y4, x2, y2)
    d3 = orientasi(x1, y1, x2, y2, x3, y3)
    d4 = orientasi(x1, y1, x2, y2, x4, y4)
    ya = ((d1 * d2 < 0 and d3 * d4 < 0)
          or (d1 == 0 and di_ruas(x3, y3, x4, y4, x1, y1))
          or (d2 == 0 and di_ruas(x3, y3, x4, y4, x2, y2))
          or (d3 == 0 and di_ruas(x1, y1, x2, y2, x3, y3))
          or (d4 == 0 and di_ruas(x1, y1, x2, y2, x4, y4)))
    out.append("YA" if ya else "TIDAK")
print("\n".join(out))
CODE,
        ],
        'editorial' => '<p>Sebut kabel pertama ruas AB dan kabel kedua ruas CD. Hitung empat orientasi (tanda hasil kali silang):</p>
<ul><li><code>d1</code> = posisi A terhadap garis berarah C → D, <code>d2</code> = posisi B terhadap garis C → D;</li><li><code>d3</code> = posisi C terhadap garis A → B, <code>d4</code> = posisi D terhadap garis A → B.</li></ul>
<p>Masing-masing bernilai +1 (kiri), −1 (kanan), atau 0 (segaris). Ada dua cara kedua ruas bersentuhan:</p>
<ul><li><strong>Bersilangan biasa:</strong> A dan B berada di sisi yang <em>berbeda</em> dari garis CD (d1 · d2 &lt; 0) <em>dan</em> C dan D berada di sisi yang berbeda dari garis AB (d3 · d4 &lt; 0). Syarat pertama saja belum cukup: garis CD memotong ruas AB, tetapi titik potongnya bisa berada di luar ruas CD.</li><li><strong>Menempel atau segaris:</strong> salah satu ujung terletak pada ruas yang lain. Misalnya d1 = 0 berarti A segaris dengan C dan D; A berada pada ruas CD tepat jika A berada di dalam kotak pembatas C dan D (min ≤ koordinat ≤ maks untuk x dan y). Periksa keempat ujung dengan cara yang sama.</li></ul>
<p>Mengapa ini lengkap? Misalkan kedua ruas bersentuhan, tetapi tidak ada ujung yang terletak pada ruas lainnya. Kedua ruas tidak mungkin segaris (dua ruas segaris yang bertumpuk pasti memuat ujung ruas lainnya), jadi garisnya berpotongan di tepat satu titik, dan titik itu berada di bagian dalam kedua ruas. Itu berarti A dan B dipisahkan tegas oleh garis CD, begitu pula C dan D oleh garis AB, yaitu kasus pertama.</p>
<p><strong>Jebakan:</strong></p>
<ul><li>Dua ruas segaris yang terpisah (contoh pertama, pasangan 5) memberi keempat orientasi 0. Rumus "d1 · d2 ≤ 0 dan d3 · d4 ≤ 0" akan salah menjawab YA; pemeriksaan kotak pembatas yang menyelamatkan.</li><li>Satu hasil kali silang bisa sampai 8 · 10<sup>12</sup> (perlu <code>long long</code>), dan perkalian dua hasil kali silang bisa sampai 6,4 · 10<sup>25</sup>, bahkan meluap dari <code>long long</code>. Karena itu ubah dulu ke tanda (−1, 0, 1) baru dikalikan.</li><li>Jangan mencari titik potong dengan pembagian desimal. Titik potong bisa bukan bilangan bulat dan kesalahan pembulatan merusak kasus menempel. Dengan orientasi, semua perhitungan tetap bilangan bulat.</li></ul>
<p>Setiap pasangan diproses dalam O(1), total O(Q).</p>',
        'hints' => [
            'Hitung orientasi setiap ujung kabel terhadap garis kabel lainnya. Kapan dua ruas pasti saling menyilang?',
            'Jika A dan B berada di sisi yang berbeda dari garis CD, dan C dan D di sisi yang berbeda dari garis AB, keduanya bersilangan. Kasus sisanya selalu melibatkan orientasi nol.',
            'Orientasi nol berarti ujung itu segaris dengan ruas lainnya; ia terletak pada ruas itu jika berada di dalam kotak pembatasnya. Kalikan tandanya saja, bukan hasil kali silangnya.',
        ],
    ],

    [
        'slug' => 'pagar-kebun-mangga',
        'lesson' => 'geometri',
        'title' => 'Pagar Kebun Mangga',
        'difficulty' => 'Sulit',
        'tags' => ['geometri', 'convex hull', 'monotone chain', 'rumus shoelace'],
        'statement' => '<p>Pak Wiryo menanam <strong>N</strong> pohon mangga; pohon ke-i berada di titik <code>(x<sub>i</sub>, y<sub>i</sub>)</code>. Pencatatnya kurang teliti, jadi beberapa pohon bisa tercatat di titik yang sama, dan banyak pohon yang segaris.</p>
<p>Pak Wiryo ingin memasang pagar dengan keliling sesingkat mungkin yang mengelilingi semua pohon; pohon boleh tepat berada di garis pagar. Bayangkan karet gelang besar yang dilepaskan mengelilingi paku-paku: bentuk karet itulah pagarnya. Tiang pagar hanya dipasang di titik tempat pagar <em>berbelok</em>. Di sepanjang sisi yang lurus tidak perlu tiang, walaupun ada pohon di sana.</p>
<p>Hitung banyak tiang yang diperlukan dan luas daerah di dalam pagar. Agar tetap bilangan bulat, cetak <strong>dua kali</strong> luasnya.</p>
<p>Jika semua pohon berada di satu titik, cukup 1 tiang. Jika semua pohon segaris tetapi tidak semuanya di satu titik, pagar hanyalah ruas garis antara dua pohon terjauh dengan 2 tiang. Pada kedua kasus ini luasnya 0.</p>',
        'input_format' => '<p>Baris pertama berisi <code>N</code>. N baris berikutnya berisi <code>x<sub>i</sub> y<sub>i</sub></code>.</p>',
        'output_format' => '<p>Dua bilangan bulat dipisah spasi: banyak tiang, lalu dua kali luas daerah di dalam pagar.</p>',
        'constraints' => '<ul><li>1 ≤ N ≤ 10<sup>5</sup></li><li>−10<sup>6</sup> ≤ x<sub>i</sub>, y<sub>i</sub> ≤ 10<sup>6</sup></li></ul>',
        'samples' => [
            ['input' => "8\n0 0\n4 0\n4 4\n0 4\n2 2\n2 0\n4 4\n1 3\n", 'explanation' => 'Pagar berbentuk persegi 4 × 4 dengan tiang di keempat sudutnya. Pohon (2, 0) berada di tengah sisi bawah sehingga tidak perlu tiang, pohon (4, 4) tercatat dua kali, dan pohon (2, 2) serta (1, 3) ada di dalam. Luasnya 16, dicetak 2 · 16 = 32.'],
            ['input' => "4\n1 1\n3 3\n2 2\n5 5\n", 'explanation' => 'Semua pohon segaris, jadi pagar hanyalah ruas garis dari (1, 1) ke (5, 5) dengan 2 tiang dan luas 0.'],
            ['input' => "3\n0 0\n3 1\n1 2\n", 'explanation' => 'Pagar berbentuk segitiga. Dua kali luasnya adalah |(3, 1) × (1, 2)| = |3 · 2 − 1 · 1| = 5 (luasnya 2,5).'],
        ],
        'tests' => function () use ($M, $titikInput, $primitif, $tengahkan) {
            $acakPts = function (int $n, int $lo, int $hi) {
                $pts = [];
                for ($i = 0; $i < $n; $i++) {
                    $pts[] = [mt_rand($lo, $hi), mt_rand($lo, $hi)];
                }

                return $pts;
            };
            $acakUrut = function (array $pts) {
                T::shuffle($pts);

                return $pts;
            };
            // grid k × k berjarak s: hanya 4 tiang, banyak titik segaris di sisi
            $grid = function (int $k, int $s) {
                $o = intdiv(($k - 1) * $s, 2);
                $pts = [];
                for ($i = 0; $i < $k; $i++) {
                    for ($j = 0; $j < $k; $j++) {
                        $pts[] = [$i * $s - $o, $j * $s - $o];
                    }
                }

                return $pts;
            };
            // titik acak di dalam lingkaran berjari-jari r
            $cakram = function (int $n, int $r) {
                $pts = [];
                while (count($pts) < $n) {
                    $x = mt_rand(-$r, $r);
                    $y = mt_rand(-$r, $r);
                    if ($x * $x + $y * $y <= $r * $r) {
                        $pts[] = [$x, $y];
                    }
                }

                return $pts;
            };
            // poligon cembung bersudut banyak (sisi = 2 × vektor primitif) + titik tengah sisi + titik dalam + kembar
            $banyakSudut = function (int $R, int $n) use ($primitif, $tengahkan) {
                $x = 0;
                $y = 0;
                $pts = [];
                foreach ($primitif($R) as $v) {
                    $pts[] = [$x, $y];
                    $pts[] = [$x + $v[0], $y + $v[1]];
                    $x += 2 * $v[0];
                    $y += 2 * $v[1];
                }
                $pts = $tengahkan($pts);
                $sudut = count($pts);
                $cx = 0;
                $cy = 0;
                foreach ($pts as $p) {
                    $cx += $p[0];
                    $cy += $p[1];
                }
                $cx /= $sudut;
                $cy /= $sudut;
                while (count($pts) < $n) {
                    $p = $pts[2 * mt_rand(0, intdiv($sudut, 2) - 1)];
                    if (mt_rand(0, 9) === 0) {
                        $pts[] = $p;
                    } else {
                        $t = mt_rand(0, 950) / 1000;
                        $pts[] = [(int) round($cx + $t * ($p[0] - $cx)), (int) round($cy + $t * ($p[1] - $cy))];
                    }
                }

                return $pts;
            };
            // parabola y = x^2: semua titiknya menjadi tiang, ditambah titik di dalam
            $parabola = function (int $n) {
                $pts = [];
                for ($i = -1000; $i <= 1000; $i++) {
                    $pts[] = [$i, $i * $i];
                }
                while (count($pts) < $n) {
                    $x = mt_rand(-999, 999);
                    $pts[] = [$x, mt_rand($x * $x + 1, 1000000)];
                }

                return array_map(fn ($p) => [$p[1] - 500000, $p[0]], $pts);
            };
            // semua titik segaris (dengan kembar)
            $segaris = function (int $n) {
                $pts = [];
                for ($i = 0; $i < $n; $i++) {
                    $k = mt_rand(-140000, 140000);
                    $pts[] = [3 + 7 * $k, -5 - 3 * $k];
                }

                return $pts;
            };
            // segitiga besar dengan banyak titik di sisi-sisinya
            $segitiga = function (int $n) use ($M) {
                $pts = [[-$M, -$M], [$M, -$M], [0, $M]];
                while (count($pts) < $n) {
                    $jenis = mt_rand(0, 3);
                    $t = mt_rand(0, $M);
                    if ($jenis === 0) {
                        $pts[] = [mt_rand(-$M, $M), -$M];
                    } elseif ($jenis === 1) {
                        $pts[] = [-$M + $t, -$M + 2 * $t];
                    } elseif ($jenis === 2) {
                        $pts[] = [$M - $t, -$M + 2 * $t];
                    } else {
                        $y = mt_rand(-$M + 1, $M - 1);
                        $w = intdiv($M - $y, 2);
                        $pts[] = [mt_rand(-$w + 1, $w - 1), $y];
                    }
                }

                return $pts;
            };
            // hanya beberapa titik berbeda, masing-masing berulang banyak kali
            $sedikit = function (int $n, int $k) use ($M) {
                $beda = [];
                for ($i = 0; $i < $k; $i++) {
                    $beda[] = [mt_rand(-$M, $M), mt_rand(-$M, $M)];
                }
                $pts = [];
                for ($i = 0; $i < $n; $i++) {
                    $pts[] = $beda[mt_rand(0, $k - 1)];
                }

                return $pts;
            };

            return [
                "1\n5 -7\n",
                "5\n3 3\n3 3\n3 3\n3 3\n3 3\n",
                "3\n-1000000 -1000000\n1000000 1000000\n-1000000 -1000000\n",
                $titikInput($acakPts(12, 0, 4)),
                $titikInput($acakPts(1000, -10, 10)),
                $titikInput($acakUrut($grid(316, 6000))),
                $titikInput($acakPts(100000, -$M, $M)),
                $titikInput($cakram(60000, $M)),
                $titikInput($acakUrut($banyakSudut(150, 100000))),
                $titikInput($acakUrut($parabola(60000))),
                $titikInput($segaris(100000)),
                $titikInput($acakUrut($segitiga(50000))),
                $titikInput($sedikit(100000, 6)),
            ];
        },
        'solve' => function (string $input) use ($tok) {
            $t = $tok($input);
            $n = $t[0];
            $K = 2000001;
            $kunci = [];
            for ($i = 0; $i < $n; $i++) {
                $kunci[] = ($t[1 + 2 * $i] + 1000000) * $K + ($t[2 + 2 * $i] + 1000000);
            }
            sort($kunci);
            $p = [];
            $prev = -1;
            foreach ($kunci as $k) {
                if ($k !== $prev) {
                    $p[] = [intdiv($k, $K) - 1000000, $k % $K - 1000000];
                    $prev = $k;
                }
            }
            $m = count($p);
            if ($m === 1) {
                return '1 0';
            }
            $cr = fn ($o, $a, $b) => ($a[0] - $o[0]) * ($b[1] - $o[1]) - ($a[1] - $o[1]) * ($b[0] - $o[0]);
            $rantai = function (array $urut) use ($cr) {
                $h = [];
                $k = 0;
                foreach ($urut as $q) {
                    while ($k >= 2 && $cr($h[$k - 2], $h[$k - 1], $q) <= 0) {
                        array_pop($h);
                        $k--;
                    }
                    $h[] = $q;
                    $k++;
                }
                array_pop($h);

                return $h;
            };
            $hull = array_merge($rantai($p), $rantai(array_reverse($p)));
            $h = count($hull);
            $dua = 0;
            for ($i = 1; $i + 1 < $h; $i++) {
                $dua += $cr($hull[0], $hull[$i], $hull[$i + 1]);
            }

            return $h.' '.$dua;
        },
        'starter' => $st(
            "    int n;\n    cin >> n;\n    vector<pair<long long, long long>> p(n);\n    for (auto& t : p) cin >> t.first >> t.second;",
            "const n = Number(readLine());\nconst p = [];\nfor (let i = 0; i < n; i++) p.push(readInts()); // [x, y]",
            "n = int(input())\np = [tuple(map(int, input().split())) for _ in range(n)]"
        ),
        'solutions' => [
            'cpp' => <<<'CODE'
#include <bits/stdc++.h>
using namespace std;

typedef long long ll;
typedef pair<ll, ll> Titik;   // (x, y); pair otomatis terurut menurut x lalu y

// (A - O) x (B - O): positif jika O -> A -> B berbelok ke kiri
ll silang(const Titik& o, const Titik& a, const Titik& b) {
    return (a.first - o.first) * (b.second - o.second) - (a.second - o.second) * (b.first - o.first);
}

// Satu rantai monotone chain. Titik dibuang selama tiga titik terakhir TIDAK berbelok
// ke kiri (silang <= 0), sehingga titik segaris di tengah sisi ikut terbuang.
vector<Titik> rantai(const vector<Titik>& p) {
    vector<Titik> h;
    for (const Titik& q : p) {
        while (h.size() >= 2 && silang(h[h.size() - 2], h.back(), q) <= 0) h.pop_back();
        h.push_back(q);
    }
    h.pop_back();   // titik terakhir menjadi awal rantai berikutnya
    return h;
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<Titik> p(n);
    for (auto& t : p) cin >> t.first >> t.second;

    sort(p.begin(), p.end());
    p.erase(unique(p.begin(), p.end()), p.end());   // buang pohon kembar
    if (p.size() == 1) {
        cout << "1 0\n";
        return 0;
    }

    vector<Titik> hull = rantai(p);                   // rantai bawah: kiri -> kanan
    vector<Titik> r(p.rbegin(), p.rend());
    vector<Titik> atas = rantai(r);                   // rantai atas: kanan -> kiri
    hull.insert(hull.end(), atas.begin(), atas.end());

    // 2 * luas dengan shoelace berpusat di hull[0]; hull berlawanan jarum jam,
    // jadi setiap suku positif dan totalnya paling besar 8 * 10^12 (long long)
    ll dua = 0;
    for (size_t i = 1; i + 1 < hull.size(); i++) dua += silang(hull[0], hull[i], hull[i + 1]);
    cout << hull.size() << ' ' << dua << '\n';
    return 0;
}
CODE,
            'javascript' => <<<'CODE'
const n = Number(readLine());
const p = [];
for (let i = 0; i < n; i++) p.push(readInts()); // [x, y]

// (A - O) x (B - O); nilainya paling besar 8 * 10^12, masih tepat untuk Number
const silang = (o, a, b) => (a[0] - o[0]) * (b[1] - o[1]) - (a[1] - o[1]) * (b[0] - o[0]);

p.sort((a, b) => a[0] - b[0] || a[1] - b[1]);
const t = []; // tanpa titik kembar
for (const q of p) {
  const akhir = t[t.length - 1];
  if (!akhir || akhir[0] !== q[0] || akhir[1] !== q[1]) t.push(q);
}

// satu rantai monotone chain; silang <= 0 berarti tidak belok kiri -> buang
function rantai(urut) {
  const h = [];
  for (const q of urut) {
    while (h.length >= 2 && silang(h[h.length - 2], h[h.length - 1], q) <= 0) h.pop();
    h.push(q);
  }
  h.pop(); // titik terakhir menjadi awal rantai berikutnya
  return h;
}

if (t.length === 1) {
  console.log("1 0");
} else {
  const hull = rantai(t).concat(rantai(t.slice().reverse()));
  // hull berlawanan jarum jam: setiap suku positif, total <= 8 * 10^12
  let dua = 0;
  for (let i = 1; i + 1 < hull.length; i++) dua += silang(hull[0], hull[i], hull[i + 1]);
  console.log(hull.length + " " + dua);
}
CODE,
            'python' => <<<'CODE'
import sys
input = sys.stdin.readline


def silang(o, a, b):
    # (A - O) x (B - O): positif jika O -> A -> B berbelok ke kiri
    return (a[0] - o[0]) * (b[1] - o[1]) - (a[1] - o[1]) * (b[0] - o[0])


def rantai(urut):
    # satu rantai monotone chain; silang <= 0 berarti tidak belok kiri -> buang
    h = []
    for q in urut:
        while len(h) >= 2 and silang(h[-2], h[-1], q) <= 0:
            h.pop()
        h.append(q)
    h.pop()  # titik terakhir menjadi awal rantai berikutnya
    return h


n = int(input())
p = sorted(set(tuple(map(int, input().split())) for _ in range(n)))  # urut + tanpa kembar

if len(p) == 1:
    print("1 0")
else:
    hull = rantai(p) + rantai(p[::-1])
    dua = 0
    for i in range(1, len(hull) - 1):
        dua += silang(hull[0], hull[i], hull[i + 1])
    print(len(hull), dua)
CODE,
        ],
        'editorial' => '<p>Pagar terpendek yang mengelilingi semua pohon adalah <strong>convex hull</strong> (selubung cembung) dari titik-titik pohon, dan tiangnya adalah titik-titik sudut hull. Kita membangunnya dengan <strong>monotone chain Andrew</strong>:</p>
<ol><li>Urutkan titik menurut x, lalu y, dan buang titik kembar. Titik pertama (paling kiri-bawah) dan titik terakhir (paling kanan-atas) pasti menjadi sudut hull.</li><li><strong>Rantai bawah:</strong> telusuri titik dari kiri ke kanan. Sebelum memasukkan titik baru q, selama dua titik terakhir di rantai bersama q <em>tidak</em> berbelok ke kiri (hasil kali silang ≤ 0), buang titik terakhir. Lalu masukkan q.</li><li><strong>Rantai atas:</strong> lakukan hal yang sama dari kanan ke kiri.</li><li>Gabungkan kedua rantai tanpa mengulang titik ujungnya. Hasilnya adalah hull berlawanan arah jarum jam.</li></ol>
<p>Mengapa benar? Rantai bawah selalu menjaga sifat "setiap tiga titik berurutan berbelok ke kiri". Titik yang dibuang berada di atas atau tepat pada ruas yang menghubungkan tetangganya, sehingga tidak mungkin menjadi sudut bagian bawah hull. Setiap titik dimasukkan dan dibuang paling banyak sekali, jadi pembangunan rantai O(N); total O(N log N) karena pengurutan.</p>
<p>Detail yang menentukan jawaban:</p>
<ul><li><strong>≤ 0, bukan &lt; 0.</strong> Dengan ≤ 0, titik yang segaris di tengah sisi ikut dibuang, sehingga yang tersisa hanya titik tempat pagar benar-benar berbelok. Dengan &lt; 0, pohon di tengah sisi lurus ikut terhitung sebagai tiang.</li><li><strong>Titik kembar harus dibuang.</strong> Jika semua pohon berada di satu titik dan kembarannya tidak dibuang, rantai bisa berisi dua salinan titik yang sama dan jawabannya menjadi 2, bukan 1. Kasus satu titik ditangani terpisah.</li><li><strong>Semua segaris</strong> tidak perlu kasus khusus: rantai bawah menjadi [paling kiri, paling kanan], rantai atas sebaliknya, dan gabungannya memberi tepat 2 tiang dengan luas 0.</li><li>Urutan sekunder menurut y penting untuk titik-titik dengan x sama (misalnya garis vertikal).</li></ul>
<p>Dua kali luas dihitung dengan rumus shoelace pada titik-titik hull. Kami memakai titik hull pertama sebagai pusat: <code>2 · luas = Σ (H<sub>i</sub> − H<sub>0</sub>) × (H<sub>i+1</sub> − H<sub>0</sub>)</code>. Karena hull cembung dan berlawanan arah jarum jam, setiap suku bernilai positif, sehingga jumlah sementaranya tidak pernah melebihi hasil akhir.</p>
<p><strong>Batas nilai:</strong> setiap hasil kali silang paling besar 8 · 10<sup>12</sup> dan dua kali luas hull paling besar 2 · (2 · 10<sup>6</sup>)<sup>2</sup> = 8 · 10<sup>12</sup> (hull berada di dalam persegi bersisi 2 · 10<sup>6</sup>). Nilai ini meluap dari <code>int</code>, jadi pakai <code>long long</code>. Di JavaScript nilainya masih di bawah 9 · 10<sup>15</sup> sehingga Number tetap tepat, dan karena suku-sukunya positif, jumlah sementaranya pun aman.</p>',
        'hints' => [
            'Urutkan titik menurut x lalu y dan buang titik kembar. Titik paling kiri dan paling kanan pasti menjadi tiang.',
            'Bangun rantai bawah dari kiri ke kanan: sebelum menambahkan titik baru, buang titik terakhir selama tiga titik terakhir tidak berbelok ke kiri. Ulangi dari kanan ke kiri untuk rantai atas.',
            'Pakai syarat hasil kali silang ≤ 0 (bukan < 0) saat membuang agar pohon di tengah sisi lurus tidak dihitung sebagai tiang. Dua kali luas dihitung dengan rumus shoelace pada titik-titik hull.',
        ],
    ],
];
