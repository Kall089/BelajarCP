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
    /**
     * Daftar materi, urut sesuai peta belajar (mengikuti urutan folder materi CP).
     * level: 1 = Dasar, 2 = Menengah, 3 = Lanjut.
     * Materi yang file isinya (resources/views/lessons/content/{slug}.blade.php) belum ada akan dilewati.
     */
    private const LESSONS = [
        // ───────────── Fondasi & STL ─────────────
        ['slug' => 'kompleksitas', 'track' => 'fondasi', 'level' => 1, 'title' => 'Kompleksitas & Big-O', 'subtitle' => 'Memperkirakan kecepatan program sebelum menulisnya', 'viz' => 'bigo', 'minutes' => 20,
            'summary' => 'Cara judge bekerja, notasi Big-O, membaca batasan soal, dan memilih algoritma yang cukup cepat.'],
        ['slug' => 'stl-vector', 'track' => 'fondasi', 'level' => 1, 'title' => 'Vector, Pair & Comparator', 'subtitle' => 'Kontainer serbaguna dan cara mengurutkan sesukamu', 'viz' => 'vector-mem', 'minutes' => 25,
            'summary' => 'vector, kapasitas & realokasi, iterator, pair dan tuple, serta menulis comparator yang benar (strict weak ordering).'],
        ['slug' => 'stack-queue', 'track' => 'fondasi', 'level' => 1, 'title' => 'Stack, Queue & Deque', 'subtitle' => 'Masuk terakhir keluar pertama, dan sebaliknya', 'viz' => 'stack-queue', 'minutes' => 25,
            'summary' => 'LIFO dan FIFO, kurung seimbang, simulasi antrian, deque dua ujung, dan kapan memakai masing-masing.'],
        ['slug' => 'set-map', 'track' => 'fondasi', 'level' => 1, 'title' => 'Set, Map & Priority Queue', 'subtitle' => 'Kontainer terurut dan antrian prioritas', 'viz' => 'set-ops', 'minutes' => 30,
            'summary' => 'set, multiset, map, unordered_map, priority_queue, lower_bound pada kontainer, dan memilih kontainer yang tepat.'],
        ['slug' => 'stl-algoritma', 'track' => 'fondasi', 'level' => 1, 'title' => 'Algoritma STL Penting', 'subtitle' => 'sort, unique, next_permutation, dan kawan-kawan', 'viz' => 'stl-algo', 'minutes' => 30,
            'summary' => 'sort dengan comparator, unique + erase, next_permutation, accumulate, reverse, rotate, dan idiom yang sering dipakai.'],
        ['slug' => 'bitset', 'track' => 'fondasi', 'level' => 3, 'title' => 'Optimasi Bitset', 'subtitle' => '64 nilai boolean dalam satu instruksi', 'viz' => 'bitset-shift', 'minutes' => 30,
            'summary' => 'std::bitset, operasi geser dan AND/OR, subset sum satu baris, DP boolean yang 64 kali lebih cepat, dan menghitung segitiga.'],

        // ───────────── Teknik Array ─────────────
        ['slug' => 'prefix-sum', 'track' => 'array', 'level' => 1, 'title' => 'Prefix Sum 1D & 2D', 'subtitle' => 'Jumlah rentang dalam O(1)', 'viz' => 'prefix-2d', 'minutes' => 25,
            'summary' => 'pre[i] = jumlah awalan, jumlah rentang dengan satu pengurangan, prefix sum 2D dengan inklusi-eksklusi, dan subarray berjumlah K.'],
        ['slug' => 'difference-array', 'track' => 'array', 'level' => 1, 'title' => 'Difference Array', 'subtitle' => 'Update rentang dalam O(1)', 'viz' => 'diff-array', 'minutes' => 20,
            'summary' => 'Menambah nilai pada rentang hanya dengan dua titik, membangun kembali dengan prefix sum, versi 2D, dan sweep pada interval.'],
        ['slug' => 'two-pointers', 'track' => 'array', 'level' => 2, 'title' => 'Two Pointers & Sliding Window', 'subtitle' => 'Dua penunjuk yang hanya maju', 'viz' => 'two-pointer-pair', 'minutes' => 30,
            'summary' => 'Pasangan pada array urut, jendela ukuran tetap, jendela ukuran variabel dengan syarat monoton, dan menghitung subarray valid.'],
        ['slug' => 'kadane', 'track' => 'array', 'level' => 1, 'title' => 'Kadane & Maximum Subarray', 'subtitle' => 'DP satu dimensi pertama yang perlu dikuasai', 'viz' => 'kadane', 'minutes' => 25,
            'summary' => 'Subarray berjumlah maksimum dalam O(n), rekonstruksi posisi, versi melingkar, dan hubungan dengan prefix sum minimum.'],
        ['slug' => 'monotonic-stack', 'track' => 'array', 'level' => 2, 'title' => 'Monotonic Stack', 'subtitle' => 'Elemen lebih besar berikutnya dalam O(n)', 'viz' => 'mono-stack', 'minutes' => 30,
            'summary' => 'Next greater / previous smaller element, persegi panjang terbesar di histogram, dan kontribusi setiap elemen sebagai minimum.'],
        ['slug' => 'monotonic-deque', 'track' => 'array', 'level' => 2, 'title' => 'Monotonic Deque', 'subtitle' => 'Maksimum jendela geser dalam O(n)', 'viz' => 'mono-deque', 'minutes' => 25,
            'summary' => 'Deque yang menjaga kandidat maksimum, sliding window maximum, dan optimasi DP dengan jendela.'],
        ['slug' => 'kompresi-koordinat', 'track' => 'array', 'level' => 2, 'title' => 'Kompresi Koordinat', 'subtitle' => 'Nilai raksasa menjadi indeks kecil', 'viz' => 'compress', 'minutes' => 20,
            'summary' => 'sort + unique + lower_bound, memetakan nilai sampai 10^9 ke 0..n−1, dan memakainya untuk array frekuensi dan sweep.'],
        ['slug' => 'string-frekuensi', 'track' => 'array', 'level' => 1, 'title' => 'String & Array Frekuensi', 'subtitle' => 'Palindrom, anagram, dan menghitung kemunculan', 'viz' => 'freq-count', 'minutes' => 25,
            'summary' => 'Operasi string di C++, palindrom, anagram, array frekuensi 26 huruf, dan counting sebagai senjata utama.'],

        // ───────────── Sorting & Searching ─────────────
        ['slug' => 'sorting-dasar', 'track' => 'sorting', 'level' => 1, 'title' => 'Sorting Dasar & Stabilitas', 'subtitle' => 'Bubble, selection, insertion, dan multi-kriteria', 'viz' => 'sort-basic', 'minutes' => 25,
            'summary' => 'Tiga sorting O(n²), invarian setiap algoritma, stabilitas, dan mengurutkan dengan beberapa kriteria sekaligus.'],
        ['slug' => 'merge-sort', 'track' => 'sorting', 'level' => 2, 'title' => 'Merge Sort, Inversi & D&C', 'subtitle' => 'Pecah, taklukkan, gabungkan', 'viz' => 'merge-sort', 'minutes' => 30,
            'summary' => 'Divide and conquer, merge sort [l, r), menghitung inversi dengan mid − i, pertukaran berdampingan minimum, dan varian dua fase.'],
        ['slug' => 'quick-sort', 'track' => 'sorting', 'level' => 2, 'title' => 'Quick Sort & Quickselect', 'subtitle' => 'Partisi di sekitar pivot', 'viz' => 'quick-sort', 'minutes' => 25,
            'summary' => 'Partisi Lomuto, pivot acak, kasus terburuk O(n²), quickselect untuk elemen ke-k dalam O(n), dan nth_element.'],
        ['slug' => 'counting-sort', 'track' => 'sorting', 'level' => 2, 'title' => 'Counting, Radix & Bucket Sort', 'subtitle' => 'Mengurutkan tanpa membandingkan', 'viz' => 'radix-sort', 'minutes' => 25,
            'summary' => 'Counting sort O(n + K), radix sort per digit yang stabil, bucket sort, dan batas bawah Ω(n log n) untuk sorting berbasis perbandingan.'],
        ['slug' => 'binary-search', 'track' => 'sorting', 'level' => 1, 'title' => 'Binary Search & Two Pointers', 'subtitle' => 'Membuang setengah kemungkinan setiap langkah', 'viz' => 'window', 'minutes' => 30,
            'summary' => 'lower_bound/upper_bound, binary search pada jawaban, two pointers, sliding window, dan invarian.'],
        ['slug' => 'ternary-search', 'track' => 'sorting', 'level' => 2, 'title' => 'Ternary Search & Fungsi Unimodal', 'subtitle' => 'Mencari puncak atau lembah', 'viz' => 'ternary', 'minutes' => 25,
            'summary' => 'Fungsi unimodal, ternary search pada bilangan real dan bulat, binary search pada turunan, dan jebakan dataran datar.'],
        ['slug' => 'interaktif', 'track' => 'sorting', 'level' => 2, 'title' => 'Soal Interaktif', 'subtitle' => 'Bertanya pada juri dengan anggaran terbatas', 'viz' => 'interactive', 'minutes' => 20,
            'summary' => 'Protokol tanya-jawab, flush, menghitung anggaran pertanyaan, binary search interaktif, dan cara menguji solusi interaktif sendiri.'],
        ['slug' => 'meet-in-the-middle', 'track' => 'sorting', 'level' => 3, 'title' => 'Meet in the Middle', 'subtitle' => 'Membelah 2^n menjadi dua kali 2^(n/2)', 'viz' => 'mitm', 'minutes' => 30,
            'summary' => 'Enumerasi dua paruh, menggabungkan dengan sort + binary search atau two pointers, subset sum untuk n = 40, dan 4-sum.'],

        // ───────────── Rekursi & Brute Force ─────────────
        ['slug' => 'rekursi', 'track' => 'rekursi', 'level' => 1, 'title' => 'Rekursi & Backtracking', 'subtitle' => 'Mencoba semua kemungkinan dengan rapi', 'viz' => 'nqueen', 'minutes' => 30,
            'summary' => 'Base case, call stack, pohon rekursi, pola pilih–telusuri–batalkan, pemangkasan, dan jembatan menuju DP.'],
        ['slug' => 'enumerasi-subset', 'track' => 'rekursi', 'level' => 1, 'title' => 'Enumerasi Subset & Bitmask', 'subtitle' => 'Menelusuri seluruh 2^n himpunan bagian', 'viz' => 'subset-enum', 'minutes' => 25,
            'summary' => 'Operasi bit, mewakili himpunan dengan bilangan, iterasi 0..2^n − 1, rekursi ambil/tidak, permutasi, dan iterasi submask.'],

        // ───────────── Greedy ─────────────
        ['slug' => 'greedy-dasar', 'track' => 'greedy', 'level' => 1, 'title' => 'Prinsip Greedy & Exchange Argument', 'subtitle' => 'Kapan mengambil yang terbaik saat ini itu benar', 'viz' => 'greedy-coin', 'minutes' => 25,
            'summary' => 'Pilihan serakah, exchange argument, cara membantah greedy dengan contoh kecil, dan tanda-tanda soal yang butuh DP.'],
        ['slug' => 'greedy-interval', 'track' => 'greedy', 'level' => 2, 'title' => 'Greedy Interval', 'subtitle' => 'Activity selection, covering, dan merging', 'viz' => 'interval-sched', 'minutes' => 30,
            'summary' => 'Memilih kegiatan terbanyak (urut berdasarkan selesai), menutup ruas dengan interval paling sedikit, dan menggabungkan interval.'],
        ['slug' => 'greedy-rasio', 'track' => 'greedy', 'level' => 2, 'title' => 'Fractional Knapsack & Huffman', 'subtitle' => 'Greedy berbasis rasio dan heap', 'viz' => 'huffman', 'minutes' => 25,
            'summary' => 'Fractional knapsack dengan rasio nilai/berat, menggabungkan dua terkecil dengan heap (Huffman), dan biaya penggabungan minimum.'],
        ['slug' => 'greedy-pq', 'track' => 'greedy', 'level' => 3, 'title' => 'Greedy dengan Priority Queue', 'subtitle' => 'Teknik regret dan penjadwalan deadline', 'viz' => 'regret', 'minutes' => 30,
            'summary' => 'Multi-kriteria, mengambil lalu menyesal (buang yang terburuk dari heap), penjadwalan dengan deadline dan penalti, dan pembuktiannya.'],
        ['slug' => 'konstruktif', 'track' => 'greedy', 'level' => 2, 'title' => 'Ad-hoc & Konstruktif', 'subtitle' => 'Membangun jawaban yang sah', 'viz' => 'constructive', 'minutes' => 25,
            'summary' => 'Melatih observasi dari kasus kecil, pola membangun jawaban, permutasi dengan syarat, dan cara memeriksa jawaban konstruktif.'],
        ['slug' => 'invariant', 'track' => 'greedy', 'level' => 2, 'title' => 'Invarian, Paritas & Monovarian', 'subtitle' => 'Sesuatu yang tidak pernah berubah', 'viz' => 'invariant', 'minutes' => 25,
            'summary' => 'Menemukan besaran yang tetap atau selalu turun, argumen paritas, pewarnaan papan, dan membuktikan sesuatu mustahil.'],

        // ───────────── Teori Bilangan & Kombinatorika ─────────────
        ['slug' => 'fpb-modular', 'track' => 'matematika', 'level' => 1, 'title' => 'FPB, KPK & Aritmetika Modular', 'subtitle' => 'Euclid, pangkat cepat, dan invers', 'viz' => 'euclid', 'minutes' => 35,
            'summary' => 'Algoritma Euclid, KPK tanpa overflow, aturan modulo, pangkat cepat, invers modular (Fermat dan Euclid diperluas), persamaan Diophantine, dan CRT.'],
        ['slug' => 'bilangan-prima', 'track' => 'matematika', 'level' => 1, 'title' => 'Bilangan Prima & Saringan', 'subtitle' => 'Eratosthenes, faktorisasi, dan phi Euler', 'viz' => 'sieve', 'minutes' => 35,
            'summary' => 'Uji prima O(√n), saringan Eratosthenes, faktor prima terkecil untuk faktorisasi cepat, banyak pembagi, fungsi phi Euler, dan saringan rentang.'],
        ['slug' => 'matriks', 'track' => 'matematika', 'level' => 2, 'title' => 'Eksponensiasi Matriks', 'subtitle' => 'Rekurens linear sampai suku ke-10^18', 'viz' => 'matpow', 'minutes' => 35,
            'summary' => 'Perkalian matriks modulo, pangkat cepat matriks, menyusun matriks transisi dari rekurens, suku konstan, dan menghitung jalan sepanjang k langkah.'],
        ['slug' => 'kombinatorika', 'track' => 'matematika', 'level' => 2, 'title' => 'Kombinatorika', 'subtitle' => 'Menghitung tanpa mendaftar', 'viz' => 'gridpath', 'minutes' => 40,
            'summary' => 'Faktorial dan invers faktorial modulo prima, permutasi multiset, bintang dan sekat, inklusi–eksklusi, derangement, dan jalur grid dengan rintangan.'],
        ['slug' => 'mobius', 'track' => 'matematika', 'level' => 3, 'title' => 'Fungsi Möbius & Inversi', 'subtitle' => 'Menukar syarat gcd menjadi syarat kelipatan', 'viz' => 'mobius', 'minutes' => 35,
            'summary' => 'Fungsi multiplikatif, phi Euler, Möbius dengan sieve linear, menghitung pasangan gcd = 1, dan jumlah gcd semua pasangan.'],
        ['slug' => 'xor-basis', 'track' => 'matematika', 'level' => 3, 'title' => 'Basis Linear XOR', 'subtitle' => '2^n himpunan bagian menjadi 60 bilangan', 'viz' => 'xor-basis', 'minutes' => 30,
            'summary' => 'Eliminasi Gauss pada bit, menyisipkan bilangan ke basis, XOR maksimum dari subset, dan banyak nilai XOR berbeda.'],
        ['slug' => 'pollard-rho', 'track' => 'matematika', 'level' => 3, 'title' => 'Miller-Rabin & Pollard Rho', 'subtitle' => 'Uji prima dan faktorisasi sampai 10^18', 'viz' => 'rho', 'minutes' => 35,
            'summary' => 'Perkalian modulo 128-bit, uji keprimaan Miller-Rabin deterministik, paradoks ulang tahun, dan Pollard Rho dengan Floyd.'],
        ['slug' => 'fft', 'track' => 'matematika', 'level' => 3, 'title' => 'FFT & Perkalian Polinomial', 'subtitle' => 'Konvolusi O(n²) menjadi O(n log n)', 'viz' => 'fft', 'minutes' => 40,
            'summary' => 'Polinomial sebagai nilai di titik, akar kesatuan, FFT rekursif dan iteratif, NTT modulo 998244353, dan aplikasi konvolusi.'],

        // ───────────── Graph ─────────────
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
        ['slug' => 'dsu', 'track' => 'graph', 'level' => 2, 'title' => 'Disjoint Set Union', 'subtitle' => 'Menggabungkan kelompok dan bertanya "satu kelompok?"', 'viz' => 'dsu', 'minutes' => 35,
            'summary' => 'find dengan path compression, union by size, ukuran komponen, pemrosesan offline terbalik untuk penghapusan, dan DSU berbobot/paritas.'],
        ['slug' => 'euler', 'track' => 'graph', 'level' => 3, 'title' => 'Jalur & Sirkuit Euler', 'subtitle' => 'Melewati setiap sisi tepat sekali', 'viz' => 'euler', 'minutes' => 35,
            'summary' => 'Syarat derajat, algoritma Hierholzer tanpa rekursi, graph berarah, urutan leksikografis terkecil, dan soal yang menyamar (rangkaian kata, De Bruijn).'],
        ['slug' => 'floyd-warshall', 'track' => 'graph', 'level' => 3, 'title' => 'Floyd-Warshall & Bellman-Ford', 'subtitle' => 'Jarak semua pasangan & bobot negatif', 'viz' => 'floyd', 'minutes' => 35,
            'summary' => 'DP di atas graph: jarak antar semua pasangan simpul, sisi berbobot negatif, dan deteksi siklus negatif.'],
        ['slug' => 'scc', 'track' => 'graph', 'level' => 3, 'title' => 'Strongly Connected Components', 'subtitle' => 'Kosaraju, Tarjan, dan graph kondensasi', 'viz' => 'scc', 'minutes' => 35,
            'summary' => 'Komponen terhubung kuat pada graph berarah, algoritma Kosaraju dan Tarjan, serta DP di atas graph kondensasi.'],
        ['slug' => 'jembatan', 'track' => 'graph', 'level' => 3, 'title' => 'Jembatan & Titik Artikulasi', 'subtitle' => 'Titik rawan sebuah jaringan', 'viz' => 'bridges', 'minutes' => 35,
            'summary' => 'Pohon DFS, sisi balik, nilai tin/low, mencari jembatan dan titik artikulasi dalam O(N + M), serta pohon jembatan.'],
        ['slug' => 'two-sat', 'track' => 'graph', 'level' => 3, 'title' => '2-SAT', 'subtitle' => 'Soal logika menjadi soal graph', 'viz' => 'twosat', 'minutes' => 35,
            'summary' => 'Klausa (a ∨ b) menjadi dua implikasi, graph implikasi, SCC untuk memeriksa x dan ¬x, dan membangun penugasan.'],
        ['slug' => 'max-flow', 'track' => 'graph', 'level' => 3, 'title' => 'Aliran Maksimum & Pencocokan', 'subtitle' => 'Edmonds-Karp, potongan minimum, dan Kuhn', 'viz' => 'flow', 'minutes' => 40,
            'summary' => 'Jaringan aliran, graph residual dan sisi balik, Edmonds-Karp, teorema max-flow min-cut, pencocokan bipartit, dan teknik pemodelan.'],
        ['slug' => 'mcmf', 'track' => 'graph', 'level' => 3, 'title' => 'Aliran Biaya Minimum & Penugasan', 'subtitle' => 'MCMF dengan SPFA, dan Hungarian', 'viz' => 'mcmf', 'minutes' => 40,
            'summary' => 'Biaya per unit aliran, jalur penambah termurah dengan Bellman-Ford/SPFA, sisi balik berbiaya negatif, dan penugasan optimal.'],

        // ───────────── Dynamic Programming ─────────────
        ['slug' => 'konsep-dp', 'track' => 'dp', 'level' => 1, 'title' => 'Konsep Dynamic Programming', 'subtitle' => 'Dari rekursi lambat ke tabel cepat', 'viz' => 'fib', 'minutes' => 25,
            'summary' => 'Overlapping subproblem, memoization, tabulasi, dan resep 4 langkah merancang DP.'],
        ['slug' => 'dp-state', 'track' => 'dp', 'level' => 1, 'title' => 'Top-Down, Bottom-Up & Desain State', 'subtitle' => 'Dua gaya penulisan, satu cara berpikir', 'viz' => 'memo-tree', 'minutes' => 30,
            'summary' => 'Memoization vs tabulasi berdampingan, urutan pengisian, cara menemukan state dari pertanyaan "apa yang perlu diingat", dan jebakan state.'],
        ['slug' => 'dp-1d', 'track' => 'dp', 'level' => 1, 'title' => 'DP 1 Dimensi', 'subtitle' => 'Naik tangga & tukar koin', 'viz' => 'coin', 'minutes' => 25,
            'summary' => 'Merancang state, transisi, dan base case untuk soal menghitung cara dan mencari minimum.'],
        ['slug' => 'dp-hitung', 'track' => 'dp', 'level' => 2, 'title' => 'DP Menghitung Cara & Modulo', 'subtitle' => 'Aturan menghitung, segitiga Pascal, modulo', 'viz' => 'pascal', 'minutes' => 30,
            'summary' => 'Aturan penjumlahan dan perkalian, aritmetika modulo, C(n, k) dengan segitiga Pascal, invers modular, dan bilangan Catalan.'],
        ['slug' => 'dp-grid', 'track' => 'dp', 'level' => 2, 'title' => 'DP pada Grid', 'subtitle' => 'Menghitung jalur di petak', 'viz' => 'grid', 'minutes' => 25,
            'summary' => 'Tabel dua dimensi, urutan pengisian, rintangan, dan variasi maksimum/minimum.'],
        ['slug' => 'knapsack', 'track' => 'dp', 'level' => 2, 'title' => 'Knapsack 0/1', 'subtitle' => 'Memilih barang dengan kapasitas terbatas', 'viz' => 'knapsack', 'minutes' => 30,
            'summary' => 'Ambil atau tidak, tabel item × kapasitas, optimasi 1 dimensi, telusur balik, dan keluarga knapsack.'],
        ['slug' => 'subset-sum', 'track' => 'dp', 'level' => 2, 'title' => 'Subset Sum & Partition', 'subtitle' => 'Bisa atau tidak, dan membagi dua sama besar', 'viz' => 'subset-sum', 'minutes' => 25,
            'summary' => 'DP boolean reachable[s], membagi dua kelompok dengan selisih minimum, menghitung banyak subset, dan percepatan bitset.'],
        ['slug' => 'bounded-knapsack', 'track' => 'dp', 'level' => 2, 'title' => 'Bounded Knapsack', 'subtitle' => 'Dekomposisi biner untuk stok terbatas', 'viz' => 'binary-split', 'minutes' => 25,
            'summary' => 'Barang dengan stok k, memecah stok menjadi 1, 2, 4, …, sisa, membuktikan semua jumlah terbentuk, dan versi uang kembalian terbatas.'],
        ['slug' => 'lis', 'track' => 'dp', 'level' => 2, 'title' => 'Longest Increasing Subsequence', 'subtitle' => 'Subsequence naik terpanjang', 'viz' => 'lis', 'minutes' => 30,
            'summary' => 'DP O(N²) dengan pointer prev, rekonstruksi, dan versi cepat O(N log N) dengan lower_bound.'],
        ['slug' => 'lcs', 'track' => 'dp', 'level' => 2, 'title' => 'Longest Common Subsequence', 'subtitle' => 'DP dua string', 'viz' => 'lcs', 'minutes' => 30,
            'summary' => 'Membandingkan dua string huruf demi huruf, panah diagonal, rekonstruksi, dan edit distance.'],
        ['slug' => 'dp-rekonstruksi', 'track' => 'dp', 'level' => 2, 'title' => 'Rekonstruksi & Optimasi Memori DP', 'subtitle' => 'Mengeluarkan jawabannya, lalu menghemat tabel', 'viz' => 'dp-trace', 'minutes' => 30,
            'summary' => 'Menyimpan pilihan, menelusuri balik dari keadaan akhir, jawaban leksikografis terkecil, rolling array dua baris, dan satu baris.'],
        ['slug' => 'dp-review', 'track' => 'dp', 'level' => 2, 'title' => 'Review & Latihan Terpadu DP', 'subtitle' => 'Mengenali jenis DP dari pernyataan soal', 'viz' => 'dp-classify', 'minutes' => 30,
            'summary' => 'Peta jenis-jenis DP, cara memilih state dari batasan, daftar periksa sebelum menulis kode, dan soal gabungan tingkat menengah.'],
        ['slug' => 'dp-interval', 'track' => 'dp', 'level' => 3, 'title' => 'DP Interval', 'subtitle' => 'Memecah rentang menjadi dua bagian', 'viz' => 'interval', 'minutes' => 35,
            'summary' => 'State dp[l][r] pada rentang, urutan pengisian berdasarkan panjang, dan pola "titik potong terakhir".'],
        ['slug' => 'desain-state', 'track' => 'dp', 'level' => 3, 'title' => 'Desain State Lanjut', 'subtitle' => 'Memilih keadaan yang tepat', 'viz' => 'state-design', 'minutes' => 35,
            'summary' => 'Menukar peran nilai dan dimensi, state dengan informasi terakhir, membuang dimensi yang bisa dihitung, dan DP dengan prefix sum.'],
        ['slug' => 'dp-bitmask', 'track' => 'dp', 'level' => 3, 'title' => 'DP Bitmask', 'subtitle' => 'Himpunan sebagai bilangan biner', 'viz' => 'bitmask', 'minutes' => 35,
            'summary' => 'Menyimpan himpunan dalam satu bilangan, operasi bit, Travelling Salesman, dan pembagian tugas.'],
        ['slug' => 'sos-dp', 'track' => 'dp', 'level' => 3, 'title' => 'Sum over Subsets (SOS DP)', 'subtitle' => 'Menjumlahkan seluruh himpunan bagian dalam O(n 2^n)', 'viz' => 'sos', 'minutes' => 35,
            'summary' => 'Dari O(3^n) ke O(n·2^n), memproses bit satu per satu, superset sum, dan menghitung pasangan dengan AND = 0.'],
        ['slug' => 'digit-dp', 'track' => 'dp', 'level' => 3, 'title' => 'Digit DP', 'subtitle' => 'Menghitung bilangan sampai 10^18', 'viz' => 'digit', 'minutes' => 35,
            'summary' => 'Menyusun bilangan digit demi digit, flag ketat, memo keadaan bebas, F(B) − F(A − 1), nol di depan, dan sisa bagi.'],
        ['slug' => 'dp-pohon', 'track' => 'dp', 'level' => 3, 'title' => 'DP pada Pohon', 'subtitle' => 'Subtree sebagai subsoal', 'viz' => 'dp-tree', 'minutes' => 35,
            'summary' => 'State dp[u][keadaan], menggabungkan anak, DP pohon tanpa rekursi, menghitung cara, teknik kontribusi, dan knapsack di pohon.'],
        ['slug' => 'rerooting', 'track' => 'dp', 'level' => 3, 'title' => 'Rerooting Technique', 'subtitle' => 'Jawaban untuk setiap simpul sebagai akar', 'viz' => 'reroot', 'minutes' => 35,
            'summary' => 'DP ke bawah lalu ke atas, memindahkan akar dari ayah ke anak dalam O(1), prefix/suffix untuk operasi tanpa invers.'],
        ['slug' => 'dp-peluang', 'track' => 'dp', 'level' => 3, 'title' => 'DP Peluang & Nilai Harapan', 'subtitle' => 'Dadu, harapan, dan pecahan modulo', 'viz' => 'dice', 'minutes' => 35,
            'summary' => 'Distribusi peluang dengan DP, DP nilai harapan dari belakang, self-loop, pecahan P·Q⁻¹ mod p, linearitas, distribusi geometrik, dan jumlah ekor.'],
        ['slug' => 'broken-profile', 'track' => 'dp', 'level' => 3, 'title' => 'Broken Profile DP', 'subtitle' => 'Menutup grid dengan domino', 'viz' => 'profile', 'minutes' => 40,
            'summary' => 'Profil kolom sebagai bitmask, DP sel demi sel, menghitung cara menutup grid n × m dengan domino, dan variasinya.'],
        ['slug' => 'dp-optimasi', 'track' => 'dp', 'level' => 3, 'title' => 'Optimasi Transisi DP', 'subtitle' => 'Prefix sum, deque monoton, binary search', 'viz' => 'monoqueue', 'minutes' => 35,
            'summary' => 'Mempercepat transisi DP: jumlah rentang dengan prefix sum, minimum jendela dengan deque monoton, memisahkan suku, dan penjadwalan berbobot.'],
        ['slug' => 'dnc-knuth', 'track' => 'dp', 'level' => 3, 'title' => 'Optimasi DP: D&C & Knuth', 'subtitle' => 'Memangkas jangkauan titik pecah', 'viz' => 'dnc-opt', 'minutes' => 40,
            'summary' => 'Titik optimal yang monoton, divide and conquer optimization O(kn log n), quadrangle inequality, dan Knuth O(n²).'],
        ['slug' => 'cht', 'track' => 'dp', 'level' => 3, 'title' => 'Convex Hull Trick & Li Chao Tree', 'subtitle' => 'Suku DP berbentuk garis', 'viz' => 'cht', 'minutes' => 40,
            'summary' => 'Menulis transisi sebagai min dari garis, CHT dengan kemiringan monoton memakai deque, dan Li Chao tree untuk kasus umum.'],
        ['slug' => 'aliens-trick', 'track' => 'dp', 'level' => 3, 'title' => 'Aliens Trick (Lagrangian)', 'subtitle' => 'Menghapus batasan "tepat k"', 'viz' => 'aliens', 'minutes' => 35,
            'summary' => 'Fungsi cekung terhadap k, menambah penalti λ per pemakaian, binary search λ, dan mengembalikan jawaban asli.'],
        ['slug' => 'slope-trick', 'track' => 'dp', 'level' => 3, 'title' => 'Slope Trick', 'subtitle' => 'Menyimpan fungsi cembung dengan heap', 'viz' => 'slope', 'minutes' => 40,
            'summary' => 'Fungsi cembung sepotong-sepotong, titik patah di priority queue, operasi tambah |x − a| dan prefix minimum, serta soal membuat barisan naik.'],

        // ───────────── Struktur Data ─────────────
        ['slug' => 'fenwick', 'track' => 'struktur-data', 'level' => 2, 'title' => 'Fenwick Tree (BIT)', 'subtitle' => 'Update titik dan jumlah prefix dalam O(log N)', 'viz' => 'fenwick', 'minutes' => 35,
            'summary' => 'Bit terendah i & -i, update dan query prefix O(log N), menghitung inversi, update rentang, dan mencari elemen ke-k.'],
        ['slug' => 'segment-tree', 'track' => 'struktur-data', 'level' => 2, 'title' => 'Segment Tree', 'subtitle' => 'Query rentang apa pun, update kapan pun', 'viz' => 'segtree', 'minutes' => 40,
            'summary' => 'Membangun, query, dan update segment tree; versi iteratif; informasi kustom di simpul; lazy propagation; dan sparse table.'],
        ['slug' => 'sqrt', 'track' => 'struktur-data', 'level' => 3, 'title' => 'Dekomposisi Akar & Algoritma Mo', 'subtitle' => 'Membagi menjadi √N blok', 'viz' => 'mo', 'minutes' => 40,
            'summary' => 'Blok berukuran √N untuk query dan update rentang, tag per blok, algoritma Mo untuk query rentang offline, dan memilih ukuran blok.'],
        ['slug' => 'persistent-segtree', 'track' => 'struktur-data', 'level' => 3, 'title' => 'Segment Tree Persisten', 'subtitle' => 'Menyimpan seluruh versi', 'viz' => 'persistent', 'minutes' => 40,
            'summary' => 'Menyalin hanya log n simpul per perubahan, akar setiap versi, elemen terkecil ke-k di rentang, dan query pada versi lama.'],
        ['slug' => 'treap', 'track' => 'struktur-data', 'level' => 3, 'title' => 'Treap & Implicit Treap', 'subtitle' => 'Seimbang tanpa rotasi rumit', 'viz' => 'treap', 'minutes' => 40,
            'summary' => 'BST + heap prioritas acak, split dan merge, ukuran subtree, implicit treap sebagai array yang bisa dipotong dan dibalik.'],
        ['slug' => 'dsu-rollback', 'track' => 'struktur-data', 'level' => 3, 'title' => 'DSU Rollback & Konektivitas Dinamis', 'subtitle' => 'Menghapus sisi dengan membatalkan', 'viz' => 'rollback', 'minutes' => 40,
            'summary' => 'DSU tanpa path compression yang bisa dibatalkan, stack riwayat, segment tree atas waktu, dan menjawab konektivitas dengan sisi yang dihapus.'],
        ['slug' => 'parallel-bs', 'track' => 'struktur-data', 'level' => 3, 'title' => 'Parallel Binary Search', 'subtitle' => 'Ribuan binary search berbagi satu sapuan', 'viz' => 'pbs', 'minutes' => 35,
            'summary' => 'Kapan setiap query punya jawaban monoton terhadap waktu, mengelompokkan query per mid, dan menyapu kejadian sekali per putaran.'],

        // ───────────── Pohon ─────────────
        ['slug' => 'pohon', 'track' => 'pohon', 'level' => 2, 'title' => 'Algoritma pada Pohon', 'subtitle' => 'Akar, subtree, dan diameter', 'viz' => 'tree', 'minutes' => 30,
            'summary' => 'Sifat pohon, menjadikan pohon berakar, ukuran subtree, diameter dengan dua BFS, Euler tour, dan rerooting.'],
        ['slug' => 'euler-tour', 'track' => 'pohon', 'level' => 2, 'title' => 'Euler Tour & Query Subtree', 'subtitle' => 'Meratakan pohon menjadi array', 'viz' => 'euler-tour', 'minutes' => 30,
            'summary' => 'tin/tout, subtree menjadi rentang berurutan, update simpul + jumlah subtree dengan Fenwick, dan cek leluhur dalam O(1).'],
        ['slug' => 'lca', 'track' => 'pohon', 'level' => 3, 'title' => 'Lowest Common Ancestor', 'subtitle' => 'Binary lifting pada pohon', 'viz' => 'lca', 'minutes' => 35,
            'summary' => 'Leluhur bersama terdekat dua simpul dalam O(log N) per pertanyaan, plus jarak di pohon.'],
        ['slug' => 'small-to-large', 'track' => 'pohon', 'level' => 3, 'title' => 'Small to Large & DSU on Tree', 'subtitle' => 'Menggabungkan yang kecil ke yang besar', 'viz' => 'small-large', 'minutes' => 35,
            'summary' => 'Argumen setiap elemen pindah paling banyak log n kali, menggabungkan set anak, warna berbeda di setiap subtree, dan teknik sack.'],
        ['slug' => 'hld', 'track' => 'pohon', 'level' => 3, 'title' => 'Heavy-Light Decomposition', 'subtitle' => 'Query jalur menjadi query rentang', 'viz' => 'hld', 'minutes' => 40,
            'summary' => 'Anak berat, rantai berat, posisi berurutan, memecah jalur menjadi O(log n) rentang, dan query jalur dengan segment tree.'],
        ['slug' => 'centroid', 'track' => 'pohon', 'level' => 3, 'title' => 'Centroid Decomposition', 'subtitle' => 'Membelah pohon pada titik seimbangnya', 'viz' => 'centroid', 'minutes' => 40,
            'summary' => 'Mencari centroid, pohon centroid berkedalaman log n, menghitung pasangan berjarak k, dan query simpul merah terdekat.'],

        // ───────────── String ─────────────
        ['slug' => 'string-hashing', 'track' => 'string', 'level' => 2, 'title' => 'String Hashing', 'subtitle' => 'Membandingkan potongan dalam O(1)', 'viz' => 'hash', 'minutes' => 35,
            'summary' => 'Hash polinomial, prefix hash, hash potongan O(1), tabrakan dan double hashing, palindrom dengan hash maju-mundur, dan binary search + hash.'],
        ['slug' => 'kmp', 'track' => 'string', 'level' => 2, 'title' => 'KMP & Fungsi Prefiks', 'subtitle' => 'Mencari pola tanpa pernah mundur', 'viz' => 'kmp', 'minutes' => 35,
            'summary' => 'Border dan fungsi prefiks π, pencarian pola O(n + m), periode terpendek, fungsi Z, dan menghitung kemunculan setiap awalan.'],
        ['slug' => 'palindrom', 'track' => 'string', 'level' => 3, 'title' => 'Palindrom & Algoritma Manacher', 'subtitle' => 'Semua palindrom dalam waktu linear', 'viz' => 'manacher', 'minutes' => 35,
            'summary' => 'Ekspansi dari pusat, algoritma Manacher dengan jari-jari ganjil dan genap, menghitung substring palindrom, cek s[l..r] dalam O(1), dan partisi palindrom.'],
        ['slug' => 'trie', 'track' => 'string', 'level' => 2, 'title' => 'Trie', 'subtitle' => 'Pohon awalan untuk kata dan bit', 'viz' => 'trie', 'minutes' => 30,
            'summary' => 'Menyisipkan dan mencari kata, menghitung kata berawalan sama, saran kata, trie biner untuk XOR maksimum, dan prefix XOR.'],
        ['slug' => 'suffix-array', 'track' => 'string', 'level' => 3, 'title' => 'Suffix Array & LCP', 'subtitle' => 'Semua sufiks diurutkan', 'viz' => 'suffix-array', 'minutes' => 40,
            'summary' => 'Pengurutan dengan penggandaan O(n log n), algoritma Kasai untuk LCP, banyak substring berbeda, dan substring berulang terpanjang.'],
        ['slug' => 'aho-corasick', 'track' => 'string', 'level' => 3, 'title' => 'Aho-Corasick', 'subtitle' => 'Banyak pola sekaligus, satu kali telusur', 'viz' => 'aho', 'minutes' => 40,
            'summary' => 'Trie dari semua pola, suffix link dengan BFS, fungsi go, menghitung kemunculan setiap pola, dan DP di atas automaton.'],
        ['slug' => 'suffix-automaton', 'track' => 'string', 'level' => 3, 'title' => 'Suffix Automaton', 'subtitle' => 'Mesin terkecil yang mengenali seluruh substring', 'viz' => 'sam', 'minutes' => 45,
            'summary' => 'Kelas endpos, link sufiks, menambah huruf dengan clone, banyak substring berbeda, dan kemunculan setiap substring.'],

        // ───────────── Geometri ─────────────
        ['slug' => 'geometri', 'track' => 'geometri', 'level' => 2, 'title' => 'Geometri Dasar', 'subtitle' => 'Hasil kali silang sebagai pisau serbaguna', 'viz' => 'hull', 'minutes' => 40,
            'summary' => 'Vektor dengan koordinat bulat, cross product dan orientasi, perpotongan ruas garis, luas poligon (shoelace), titik dalam poligon, dan convex hull monotone chain.'],
        ['slug' => 'sweep-line', 'track' => 'geometri', 'level' => 3, 'title' => 'Sweep Line & Titik Terdekat', 'subtitle' => 'Garis yang bergerak, bidang menjadi barisan', 'viz' => 'sweep', 'minutes' => 35,
            'summary' => 'Kejadian yang diurutkan, sweep untuk interval dan persegi panjang, pasangan titik terdekat O(n log n) dengan set.'],

        // ───────────── Teori Permainan ─────────────
        ['slug' => 'permainan', 'track' => 'permainan', 'level' => 2, 'title' => 'Teori Permainan: Nim & Sprague–Grundy', 'subtitle' => 'Siapa menang jika keduanya bermain sempurna?', 'viz' => 'nim', 'minutes' => 35,
            'summary' => 'Posisi menang dan kalah, DP permainan, pola periodik, Nim dan XOR, nilai Grundy dengan mex, dan menggabungkan banyak permainan.'],

        // ───────────── Teknik Kontes ─────────────
        ['slug' => 'stress-testing', 'track' => 'kontes', 'level' => 1, 'title' => 'Stress Testing & Mencari Bug', 'subtitle' => 'Uji banding dengan solusi brute force', 'viz' => 'stress', 'minutes' => 25,
            'summary' => 'Generator acak, solusi naif, skrip pembanding, memperkecil kasus gagal, dan daftar periksa bug yang paling sering terjadi.'],
        ['slug' => 'randomisasi', 'track' => 'kontes', 'level' => 2, 'title' => 'Randomisasi dalam Kompetisi', 'subtitle' => 'Keacakan yang bisa dihitung peluang gagalnya', 'viz' => 'random-sample', 'minutes' => 30,
            'summary' => 'mt19937 dan seed waktu, mengacak urutan, sampling acak untuk elemen mayoritas, hash anti-serangan, dan menghitung peluang gagal.'],

        // ───────────── Latihan Terpadu ─────────────
        ['slug' => 'latihan-dp', 'track' => 'latihan', 'level' => 2, 'title' => 'Kumpulan Soal DP', 'subtitle' => 'EDPC, CSES, Codeforces, dan Gemastik', 'viz' => 'practice-map', 'minutes' => 20,
            'summary' => 'Cara mengenali jenis DP dari pernyataan, daftar soal bertingkat yang dipetakan ke materi, soal Gemastik, dan rencana latihan enam minggu.'],
        ['slug' => 'latihan-graf', 'track' => 'latihan', 'level' => 2, 'title' => 'Kumpulan Soal Graf', 'subtitle' => 'CSES Graph Algorithms dan Codeforces', 'viz' => 'practice-map', 'minutes' => 20,
            'summary' => 'Ciri soal graf yang menyamar, daftar soal bertingkat yang dipetakan ke materi, kesalahan yang sering terjadi, dan rencana latihan.'],
        ['slug' => 'latihan-greedy', 'track' => 'latihan', 'level' => 2, 'title' => 'Kumpulan Soal Greedy', 'subtitle' => 'CSES Sorting & Searching dan Codeforces', 'viz' => 'practice-map', 'minutes' => 20,
            'summary' => 'Membedakan greedy dari DP, daftar soal bertingkat yang dipetakan ke materi, soal Gemastik, dan rencana latihan.'],
    ];

    /** Urutan file soal; soal nantinya diurutkan mengikuti urutan materi. File yang belum ada dilewati. */
    private const PROBLEM_FILES = [
        'fondasi', 'graph', 'graph_more', 'dp', 'dp_more',
        'stl', 'array', 'sorting', 'rekursi', 'greedy', 'matematika', 'graph_lanjut', 'dp_lanjut',
        'struktur_data', 'pohon', 'string', 'geometri', 'permainan', 'kontes', 'latihan',
        'fondasi_more', 'fondasi_stl', 'fondasi_bit', 'fondasi_greedy',
        'graph_extra', 'graph_extra2', 'graph_extra3', 'graph_euler', 'graph_flow',
        'dp_extra', 'dp_extra2', 'dp_optimasi', 'dp_peluang',
        'ds_prefix', 'ds_stack', 'ds_dsu', 'ds_fenwick', 'ds_segtree', 'ds_sqrt',
        'math_fpb', 'math_prima', 'math_kombi', 'math_matriks', 'math_game', 'math_geo',
        'str_hash', 'str_kmp', 'str_trie', 'str_palindrom',
    ];

    public function run(): void
    {
        $lessons = [];
        foreach (self::LESSONS as $i => $data) {
            // Materi tanpa isi belum ditampilkan di peta belajar
            if (! view()->exists('lessons.content.'.$data['slug'])) {
                continue;
            }
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

        // Materi dan soal yang sudah tidak ada di kurikulum dihapus, agar peta belajar
        // tidak menyimpan materi lama yang isinya sudah digabung ke materi lain.
        Lesson::whereNotIn('slug', array_keys($lessons))->delete();
        Problem::whereNotIn('slug', array_keys($seen))->delete();
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
