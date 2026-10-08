<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Problem;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Seeder kurikulum bersifat "upsert": materi & soal diperbarui berdasarkan slug,
 * sehingga akun, progres, dan riwayat submisi siswa tetap aman saat seeding ulang.
 */
class CurriculumSeeder extends Seeder
{
    /** level: 1 = Dasar, 2 = Menengah, 3 = Lanjut */
    private const LESSONS = [
        // Fondasi
        ['slug' => 'kompleksitas', 'track' => 'fondasi', 'level' => 1, 'title' => 'Kompleksitas & Big-O', 'subtitle' => 'Memperkirakan kecepatan program sebelum menulisnya', 'viz' => 'bigo', 'minutes' => 20,
            'summary' => 'Cara judge bekerja, notasi Big-O, membaca batasan soal, dan memilih algoritma yang cukup cepat.'],
        ['slug' => 'rekursi', 'track' => 'fondasi', 'level' => 1, 'title' => 'Rekursi & Backtracking', 'subtitle' => 'Mencoba semua kemungkinan dengan rapi', 'viz' => 'nqueen', 'minutes' => 30,
            'summary' => 'Base case, call stack, pohon rekursi, pola pilih–telusuri–batalkan, pemangkasan, dan jembatan menuju DP.'],
        ['slug' => 'binary-search', 'track' => 'fondasi', 'level' => 1, 'title' => 'Binary Search & Two Pointers', 'subtitle' => 'Membuang setengah kemungkinan setiap langkah', 'viz' => 'window', 'minutes' => 30,
            'summary' => 'lower_bound/upper_bound, binary search pada jawaban, two pointers, sliding window, dan invarian.'],
        // Graph
        ['slug' => 'representasi-graph', 'track' => 'graph', 'level' => 1, 'title' => 'Mengenal Graph', 'subtitle' => 'Simpul, sisi, adjacency list & matrix', 'viz' => 'graph-repr', 'minutes' => 20,
            'summary' => 'Apa itu graph, istilah penting, dan dua cara utama menyimpannya di program C++.'],
        ['slug' => 'bfs', 'track' => 'graph', 'level' => 1, 'title' => 'Breadth-First Search', 'subtitle' => 'Menjelajah lapis demi lapis', 'viz' => 'bfs', 'minutes' => 25,
            'summary' => 'Menjelajah graph memakai antrian, jarak terpendek tak berbobot, BFS di grid, dan multi-source BFS.'],
        ['slug' => 'dfs', 'track' => 'graph', 'level' => 1, 'title' => 'Depth-First Search', 'subtitle' => 'Menyelam sedalam mungkin, lalu kembali', 'viz' => 'dfs', 'minutes' => 25,
            'summary' => 'Penjelajahan rekursif, komponen terhubung, flood fill, pewarnaan dua warna, dan deteksi siklus.'],
        ['slug' => 'dijkstra', 'track' => 'graph', 'level' => 2, 'title' => 'Algoritma Dijkstra', 'subtitle' => 'Jalur terpendek pada graph berbobot', 'viz' => 'dijkstra', 'minutes' => 30,
            'summary' => 'Priority queue, relaksasi sisi, rekonstruksi rute, dan Dijkstra dengan state tambahan.'],
        ['slug' => 'bfs-01', 'track' => 'graph', 'level' => 2, 'title' => 'BFS 0-1 & Graph Keadaan', 'subtitle' => 'Deque, graph tersembunyi, dan state tambahan', 'viz' => 'bfs01', 'minutes' => 30,
            'summary' => 'Memilih algoritma jalur terpendek, BFS 0-1 dengan deque, BFS multi-sumber, dan graph keadaan (posisi + informasi).'],
        ['slug' => 'topological-sort', 'track' => 'graph', 'level' => 2, 'title' => 'Topological Sort', 'subtitle' => 'Mengurutkan tugas yang saling bergantung', 'viz' => 'toposort', 'minutes' => 25,
            'summary' => 'Algoritma Kahn, indegree, deteksi siklus, urutan leksikografis, dan DP di atas DAG.'],
        ['slug' => 'mst', 'track' => 'graph', 'level' => 2, 'title' => 'Minimum Spanning Tree', 'subtitle' => 'Kruskal & Union-Find', 'viz' => 'mst', 'minutes' => 30,
            'summary' => 'Menghubungkan semua simpul dengan biaya minimum memakai Kruskal dan struktur Union-Find (DSU).'],
        ['slug' => 'pohon', 'track' => 'graph', 'level' => 2, 'title' => 'Algoritma pada Pohon', 'subtitle' => 'Akar, subtree, dan diameter', 'viz' => 'tree', 'minutes' => 30,
            'summary' => 'Sifat pohon, menjadikan pohon berakar, ukuran subtree, diameter dengan dua BFS, Euler tour, dan rerooting.'],
        ['slug' => 'floyd-warshall', 'track' => 'graph', 'level' => 3, 'title' => 'Floyd-Warshall & Bellman-Ford', 'subtitle' => 'Jarak semua pasangan & bobot negatif', 'viz' => 'floyd', 'minutes' => 35,
            'summary' => 'DP di atas graph: jarak antar semua pasangan simpul, sisi berbobot negatif, dan deteksi siklus negatif.'],
        ['slug' => 'lca', 'track' => 'graph', 'level' => 3, 'title' => 'Lowest Common Ancestor', 'subtitle' => 'Binary lifting pada pohon', 'viz' => 'lca', 'minutes' => 35,
            'summary' => 'Leluhur bersama terdekat dua simpul dalam O(log N) per pertanyaan, plus jarak di pohon.'],
        ['slug' => 'scc', 'track' => 'graph', 'level' => 3, 'title' => 'Strongly Connected Components', 'subtitle' => 'Kosaraju, Tarjan, dan graph kondensasi', 'viz' => 'scc', 'minutes' => 35,
            'summary' => 'Komponen terhubung kuat pada graph berarah, algoritma Kosaraju dan Tarjan, serta DP di atas graph kondensasi.'],
        ['slug' => 'jembatan', 'track' => 'graph', 'level' => 3, 'title' => 'Jembatan & Titik Artikulasi', 'subtitle' => 'Titik rawan sebuah jaringan', 'viz' => 'bridges', 'minutes' => 35,
            'summary' => 'Pohon DFS, sisi balik, nilai tin/low, mencari jembatan dan titik artikulasi dalam O(N + M), serta pohon jembatan.'],
        ['slug' => 'euler', 'track' => 'graph', 'level' => 3, 'title' => 'Jalur & Sirkuit Euler', 'subtitle' => 'Melewati setiap sisi tepat sekali', 'viz' => 'euler', 'minutes' => 35,
            'summary' => 'Syarat derajat, algoritma Hierholzer tanpa rekursi, graph berarah, urutan leksikografis terkecil, dan soal yang menyamar (rangkaian kata, De Bruijn).'],
        ['slug' => 'max-flow', 'track' => 'graph', 'level' => 3, 'title' => 'Aliran Maksimum & Pencocokan', 'subtitle' => 'Edmonds-Karp, potongan minimum, dan Kuhn', 'viz' => 'flow', 'minutes' => 40,
            'summary' => 'Jaringan aliran, graph residual dan sisi balik, Edmonds-Karp, teorema max-flow min-cut, pencocokan bipartit, dan teknik pemodelan.'],
        // Dynamic Programming
        ['slug' => 'konsep-dp', 'track' => 'dp', 'level' => 1, 'title' => 'Konsep Dynamic Programming', 'subtitle' => 'Dari rekursi lambat ke tabel cepat', 'viz' => 'fib', 'minutes' => 25,
            'summary' => 'Overlapping subproblem, memoization, tabulasi, dan resep 4 langkah merancang DP.'],
        ['slug' => 'dp-1d', 'track' => 'dp', 'level' => 1, 'title' => 'DP 1 Dimensi', 'subtitle' => 'Naik tangga & tukar koin', 'viz' => 'coin', 'minutes' => 25,
            'summary' => 'Merancang state, transisi, dan base case untuk soal menghitung cara dan mencari minimum.'],
        ['slug' => 'dp-hitung', 'track' => 'dp', 'level' => 2, 'title' => 'DP Menghitung Cara & Modulo', 'subtitle' => 'Aturan menghitung, segitiga Pascal, modulo', 'viz' => 'pascal', 'minutes' => 30,
            'summary' => 'Aturan penjumlahan dan perkalian, aritmetika modulo, C(n, k) dengan segitiga Pascal, invers modular, dan bilangan Catalan.'],
        ['slug' => 'dp-grid', 'track' => 'dp', 'level' => 2, 'title' => 'DP pada Grid', 'subtitle' => 'Menghitung jalur di petak', 'viz' => 'grid', 'minutes' => 25,
            'summary' => 'Tabel dua dimensi, urutan pengisian, rintangan, dan variasi maksimum/minimum.'],
        ['slug' => 'knapsack', 'track' => 'dp', 'level' => 2, 'title' => 'Knapsack 0/1', 'subtitle' => 'Memilih barang dengan kapasitas terbatas', 'viz' => 'knapsack', 'minutes' => 30,
            'summary' => 'Ambil atau tidak, tabel item × kapasitas, optimasi 1 dimensi, telusur balik, dan keluarga knapsack.'],
        ['slug' => 'lcs', 'track' => 'dp', 'level' => 2, 'title' => 'Longest Common Subsequence', 'subtitle' => 'DP dua string', 'viz' => 'lcs', 'minutes' => 30,
            'summary' => 'Membandingkan dua string huruf demi huruf, panah diagonal, rekonstruksi, dan edit distance.'],
        ['slug' => 'lis', 'track' => 'dp', 'level' => 2, 'title' => 'Longest Increasing Subsequence', 'subtitle' => 'Subsequence naik terpanjang', 'viz' => 'lis', 'minutes' => 30,
            'summary' => 'DP O(N²) dengan pointer prev, rekonstruksi, dan versi cepat O(N log N) dengan lower_bound.'],
        ['slug' => 'dp-interval', 'track' => 'dp', 'level' => 3, 'title' => 'DP Interval', 'subtitle' => 'Memecah rentang menjadi dua bagian', 'viz' => 'interval', 'minutes' => 35,
            'summary' => 'State dp[l][r] pada rentang, urutan pengisian berdasarkan panjang, dan pola "titik potong terakhir".'],
        ['slug' => 'dp-bitmask', 'track' => 'dp', 'level' => 3, 'title' => 'DP Bitmask', 'subtitle' => 'Himpunan sebagai bilangan biner', 'viz' => 'bitmask', 'minutes' => 35,
            'summary' => 'Menyimpan himpunan dalam satu bilangan, operasi bit, Travelling Salesman, dan pembagian tugas.'],
        ['slug' => 'dp-pohon', 'track' => 'dp', 'level' => 3, 'title' => 'DP pada Pohon', 'subtitle' => 'Subtree sebagai subsoal', 'viz' => 'dp-tree', 'minutes' => 35,
            'summary' => 'State dp[u][keadaan], menggabungkan anak, DP pohon tanpa rekursi, menghitung cara, teknik kontribusi, dan knapsack di pohon.'],
        ['slug' => 'digit-dp', 'track' => 'dp', 'level' => 3, 'title' => 'Digit DP', 'subtitle' => 'Menghitung bilangan sampai 10^18', 'viz' => 'digit', 'minutes' => 35,
            'summary' => 'Menyusun bilangan digit demi digit, flag ketat, memo keadaan bebas, F(B) − F(A − 1), nol di depan, dan sisa bagi.'],
        ['slug' => 'dp-optimasi', 'track' => 'dp', 'level' => 3, 'title' => 'Optimasi Transisi DP', 'subtitle' => 'Prefix sum, deque monoton, binary search', 'viz' => 'monoqueue', 'minutes' => 35,
            'summary' => 'Mempercepat transisi DP: jumlah rentang dengan prefix sum, minimum jendela dengan deque monoton, memisahkan suku, dan penjadwalan berbobot.'],
        ['slug' => 'dp-peluang', 'track' => 'dp', 'level' => 3, 'title' => 'DP Peluang & Nilai Harapan', 'subtitle' => 'Dadu, harapan, dan pecahan modulo', 'viz' => 'dice', 'minutes' => 35,
            'summary' => 'Distribusi peluang dengan DP, DP nilai harapan dari belakang, self-loop, pecahan P·Q⁻¹ mod p, linearitas, distribusi geometrik, dan jumlah ekor.'],
        // Struktur Data
        ['slug' => 'prefix-sum', 'track' => 'struktur-data', 'level' => 1, 'title' => 'Prefix Sum & Array Selisih', 'subtitle' => 'Bertanya dan mengubah rentang dengan cepat', 'viz' => 'diffarray', 'minutes' => 25,
            'summary' => 'Prefix sum 1D dan 2D, array selisih untuk penambahan rentang, inklusi–eksklusi, dan menghitung subarray dengan tabel frekuensi.'],
        ['slug' => 'stack-monoton', 'track' => 'struktur-data', 'level' => 1, 'title' => 'Stack & Stack Monoton', 'subtitle' => 'Elemen lebih besar berikutnya dan histogram', 'viz' => 'monostack', 'minutes' => 30,
            'summary' => 'Stack LIFO, validasi kurung, next greater element, persegi panjang terbesar di histogram, dan teknik kontribusi.'],
        ['slug' => 'fenwick', 'track' => 'struktur-data', 'level' => 2, 'title' => 'Fenwick Tree (BIT)', 'subtitle' => 'Update titik dan jumlah prefix dalam O(log N)', 'viz' => 'fenwick', 'minutes' => 35,
            'summary' => 'Bit terendah i & -i, update dan query prefix O(log N), menghitung inversi, update rentang, dan mencari elemen ke-k.'],
        ['slug' => 'segment-tree', 'track' => 'struktur-data', 'level' => 2, 'title' => 'Segment Tree', 'subtitle' => 'Query rentang apa pun, update kapan pun', 'viz' => 'segtree', 'minutes' => 40,
            'summary' => 'Membangun, query, dan update segment tree; versi iteratif; informasi kustom di simpul; lazy propagation; dan sparse table.'],
        // Matematika
        ['slug' => 'fpb-modular', 'track' => 'matematika', 'level' => 1, 'title' => 'FPB, KPK & Aritmetika Modular', 'subtitle' => 'Euclid, pangkat cepat, dan invers', 'viz' => 'euclid', 'minutes' => 35,
            'summary' => 'Algoritma Euclid, KPK tanpa overflow, aturan modulo, pangkat cepat, invers modular (Fermat dan Euclid diperluas), persamaan Diophantine, dan CRT.'],
        ['slug' => 'bilangan-prima', 'track' => 'matematika', 'level' => 1, 'title' => 'Bilangan Prima & Saringan', 'subtitle' => 'Eratosthenes, faktorisasi, dan phi Euler', 'viz' => 'sieve', 'minutes' => 35,
            'summary' => 'Uji prima O(√n), saringan Eratosthenes, faktor prima terkecil untuk faktorisasi cepat, banyak pembagi, fungsi phi Euler, dan saringan rentang.'],
        ['slug' => 'kombinatorika', 'track' => 'matematika', 'level' => 2, 'title' => 'Kombinatorika', 'subtitle' => 'Menghitung tanpa mendaftar', 'viz' => 'gridpath', 'minutes' => 40,
            'summary' => 'Faktorial dan invers faktorial modulo prima, permutasi multiset, bintang dan sekat, inklusi–eksklusi, derangement, dan jalur grid dengan rintangan.'],
    ];

    /** Urutan file soal; soal nantinya diurutkan mengikuti urutan materi. */
    private const PROBLEM_FILES = ['fondasi', 'fondasi_more', 'graph', 'graph_more', 'graph_extra', 'graph_extra2', 'graph_extra3', 'graph_euler', 'graph_flow', 'dp', 'dp_more', 'dp_extra', 'dp_extra2', 'dp_optimasi', 'dp_peluang', 'ds_prefix', 'ds_stack', 'ds_fenwick', 'ds_segtree', 'math_fpb', 'math_prima', 'math_kombi'];

    public function run(): void
    {
        $lessons = [];
        foreach (self::LESSONS as $i => $data) {
            $lessons[$data['slug']] = Lesson::updateOrCreate(['slug' => $data['slug']], $data + ['position' => $i + 1]);
        }

        $extras = require __DIR__.'/problems/extras.php';
        $cpp = require __DIR__.'/problems/cpp.php';
        $all = [];
        foreach (self::PROBLEM_FILES as $file) {
            $path = __DIR__."/problems/{$file}.php";
            if (! is_file($path)) {
                continue;
            }
            foreach (require $path as $p) {
                $all[] = $p;
            }
        }

        // Urutkan berdasarkan posisi materi (stabil: urutan di file tetap dipertahankan)
        $order = array_flip(array_keys($lessons));
        $indexed = array_map(null, array_keys($all), $all);
        usort($indexed, fn ($a, $b) => [$order[$a[1]['lesson']] ?? 999, $a[0]] <=> [$order[$b[1]['lesson']] ?? 999, $b[0]]);

        $seen = [];
        foreach ($indexed as $pos => [, $p]) {
            if (isset($seen[$p['slug']])) {
                throw new RuntimeException("Slug soal ganda: {$p['slug']}");
            }
            $seen[$p['slug']] = true;
            $p['hints'] ??= $extras['hints'][$p['slug']] ?? null;
            $p['sample_visual'] ??= $extras['visual'][$p['slug']] ?? null;

            // C++ selalu di urutan pertama
            if (isset($cpp[$p['slug']])) {
                $p['starter'] = ['cpp' => $cpp[$p['slug']]['starter']] + $p['starter'];
                $p['solutions'] = ['cpp' => $cpp[$p['slug']]['solution']] + $p['solutions'];
            }
            if (! isset($p['starter']['cpp'], $p['solutions']['cpp'])) {
                throw new RuntimeException("Soal {$p['slug']} belum punya starter/solusi C++.");
            }

            $this->upsertProblem($p, $lessons, $pos + 1);
        }
    }

    private function upsertProblem(array $p, array $lessons, int $position): void
    {
        $lesson = $lessons[$p['lesson']] ?? throw new RuntimeException("Materi {$p['lesson']} tidak ditemukan untuk soal {$p['slug']}.");

        // Seed tetap per soal agar tes selalu sama setiap kali seeding.
        mt_srand(crc32($p['slug']));

        $problem = Problem::updateOrCreate(['slug' => $p['slug']], [
            'lesson_id' => $lesson->id,
            'position' => $position,
            'title' => $p['title'],
            'difficulty' => $p['difficulty'],
            'tags' => $p['tags'],
            'statement' => $p['statement'],
            'input_format' => $p['input_format'],
            'output_format' => $p['output_format'],
            'constraints' => $p['constraints'],
            'hints' => $p['hints'],
            'sample_visual' => $p['sample_visual'],
            'starter' => $p['starter'],
            'editorial' => $p['editorial'],
            'solutions' => $p['solutions'],
            'time_limit' => $p['time_limit'] ?? 2000,
        ]);

        $problem->testCases()->delete();
        $order = 1;
        foreach ($p['samples'] as $sample) {
            $problem->testCases()->create([
                'position' => $order++,
                'is_sample' => true,
                'input' => $sample['input'],
                'output' => $p['solve']($sample['input']),
                'explanation' => $sample['explanation'] ?? null,
            ]);
        }

        foreach (($p['tests'])() as $input) {
            $problem->testCases()->create([
                'position' => $order++,
                'is_sample' => false,
                'input' => $input,
                'output' => $p['solve']($input),
            ]);
        }
    }
}
