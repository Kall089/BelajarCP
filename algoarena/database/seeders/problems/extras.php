<?php

/*
 * Data tambahan untuk soal yang ditulis sebelum fitur petunjuk & visualisasi contoh ada.
 * Soal baru menulis 'hints' dan 'sample_visual' langsung di definisinya.
 *
 * Jenis sample_visual yang didukung:
 *   graph       N M, lalu M baris "u v" (tak berarah)
 *   wgraph      N M, lalu M baris "u v w" (tak berarah, berbobot)
 *   digraph     N M, lalu M baris "a b" (berarah)
 *   wdigraph-s  N M S, lalu M baris "u v w" (berarah, berbobot, sumber S)
 *   digraph-t   N M, baris durasi, lalu M baris "a b"
 *   wdigraph    N M, lalu M baris "u v w" (berarah, berbobot)
 *   tree        N, lalu N-1 baris "u v" (pohon berakar di 1)
 *   grid        R C, lalu R baris karakter (# = tembok, S = awal, E = tujuan)
 *   island      R C, lalu R baris karakter (# = daratan, . = air)
 *   numgrid     R C, lalu R baris angka
 *   chess       "N r1 c1 r2 c2" (papan catur N × N)
 *   bars        baris pertama N, baris kedua N angka
 *   bars-first  satu baris: N lalu N angka
 */
return [
    'visual' => [
        'derajat-simpul' => 'graph',
        'daftar-tetangga' => 'graph',
        'jarak-pertemanan' => 'graph',
        'labirin' => 'grid',
        'hitung-pulau' => 'island',
        'komponen-terhubung' => 'graph',
        'ongkos-kirim' => 'wgraph',
        'rute-tercepat' => 'wdigraph-s',
        'urutan-mapel' => 'digraph',
        'waktu-proyek' => 'digraph-t',
        'lompatan-katak' => 'bars',
        'jalur-grid' => 'grid',
        'koleksi-koin' => 'numgrid',
        'bagi-dua-adil' => 'bars',
    ],

    'hints' => [
        'derajat-simpul' => [
            'Kamu bahkan tidak perlu menyimpan graph-nya.',
            'Setiap baris "u v" menambah derajat dua simpul sekaligus.',
            'Siapkan array deg berukuran N+1 berisi 0, lalu untuk setiap sisi lakukan deg[u]++ dan deg[v]++.',
        ],
        'daftar-tetangga' => [
            'Bangun adjacency list: N+1 list kosong.',
            'Sisi "u v" masuk ke list u DAN list v.',
            'Urutkan setiap list secara numerik sebelum dicetak. Di JavaScript pakai sort((a, b) => a - b).',
        ],
        'jarak-pertemanan' => [
            'Jarak di sini dihitung dalam jumlah sisi, jadi semua sisi "berbobot" sama.',
            'Gunakan BFS dari simpul 1. Isi dist awal dengan -1 (belum ditemukan).',
            'Saat menemukan tetangga v dengan dist -1: dist[v] = dist[u] + 1, lalu masukkan v ke antrian.',
        ],
        'labirin' => [
            'Anggap setiap petak sebagai simpul dengan 4 tetangga: atas, bawah, kiri, kanan.',
            'Cari posisi S dan E lebih dulu, lalu jalankan BFS dari S.',
            'Jangan masuk ke petak "#", keluar grid, atau petak yang sudah punya jarak. Jawabannya dist di posisi E.',
        ],
        'hitung-pulau' => [
            'Setiap kali menemukan daratan yang belum dikunjungi, itu adalah pulau baru.',
            'Dari petak itu, tandai seluruh daratan yang terhubung (DFS/BFS) supaya tidak dihitung lagi.',
            'Pakai array seen berukuran R × C dan periksa 4 arah dengan dr = [-1, 1, 0, 0], dc = [0, 0, -1, 1].',
        ],
        'komponen-terhubung' => [
            'Setiap kelompok belajar adalah sebuah komponen terhubung.',
            'Loop s = 1..N. Jika s belum dikunjungi, jalankan DFS/BFS dari s sambil menghitung simpul yang dikunjungi.',
            'Catat jumlah komponen dan ukuran komponen terbesar.',
        ],
        'ongkos-kirim' => [
            'Setiap rute punya ongkos berbeda, jadi BFS tidak lagi menjamin jawaban termurah.',
            'Gunakan Dijkstra dari kota 1.',
            'Jika dist[N] masih ∞ di akhir, cetak -1.',
        ],
        'rute-tercepat' => [
            'Jalan satu arah: masukkan sisi hanya ke adj[u], jangan ke adj[v].',
            'Jalankan Dijkstra dari S, lalu cetak dist untuk semua titik (∞ menjadi -1).',
            'JavaScript tidak punya priority queue bawaan. Tulis min-heap kecil, atau pakai Dijkstra O(N²) karena N ≤ 1000.',
        ],
        'urutan-mapel' => [
            'Hitung indegree (banyak prasyarat) setiap materi.',
            'Materi ber-indegree 0 boleh dipelajari sekarang. Selalu pilih nomor terkecil (pakai min-heap atau cari linear).',
            'Jika proses berhenti sebelum N materi terambil, berarti ada siklus → cetak MUSTAHIL.',
        ],
        'waktu-proyek' => [
            'Tugas v baru boleh mulai setelah SEMUA prasyaratnya selesai.',
            'mulai[v] = max(selesai[u]) untuk setiap u → v, dan selesai[v] = mulai[v] + t[v].',
            'Proses tugas dalam urutan topologis (algoritma Kahn), lalu jawabannya adalah max(selesai).',
        ],
        'fibonacci-modulo' => [
            'Rekursi biasa terlalu lambat (eksponensial) untuk n = 100 000.',
            'Hitung dari kecil ke besar: F(0), F(1), F(2), ... sampai F(n).',
            'Cukup simpan dua nilai terakhir, dan ambil modulo 1 000 000 007 di setiap langkah.',
        ],
        'lompatan-katak' => [
            'Definisikan dp[i] = energi minimum untuk sampai di batu i.',
            'Batu i hanya bisa dicapai dari batu i−1 atau i−2.',
            'dp[i] = min(dp[i−1] + |h[i]−h[i−1]|, dp[i−2] + |h[i]−h[i−2]|), dengan dp[0] = 0.',
        ],
        'naik-tangga' => [
            'Definisikan dp[i] = banyak cara berdiri di anak tangga i.',
            'dp[i] = dp[i−1] + dp[i−2], tetapi anak tangga yang rusak selalu bernilai 0.',
            'Mulai dari dp[0] = 1, dan ambil modulo 1 000 000 007 di setiap penjumlahan.',
        ],
        'koin-minimum' => [
            'Strategi serakah (selalu ambil koin terbesar) bisa salah. Contoh: koin {1, 3, 4} untuk nominal 6.',
            'Definisikan dp[x] = banyak koin minimum untuk membentuk x, dengan dp[0] = 0 dan sisanya ∞.',
            'dp[x] = min(dp[x − c] + 1) untuk setiap koin c ≤ x. Jika dp[X] tetap ∞, cetak -1.',
        ],
        'jalur-grid' => [
            'Petak (i, j) hanya bisa dicapai dari atas (i−1, j) atau dari kiri (i, j−1).',
            'dp[i][j] = dp[i−1][j] + dp[i][j−1], dan petak "#" bernilai 0.',
            'Isi tabel baris demi baris dari kiri ke kanan, dan ambil modulo setiap penjumlahan.',
        ],
        'koleksi-koin' => [
            'Strukturnya sama dengan menghitung jalur, tapi kali ini kita mencari maksimum.',
            'dp[i][j] = a[i][j] + max(dp[i−1][j], dp[i][j−1]).',
            'Hati-hati di baris 0 dan kolom 0, karena hanya ada satu arah datang.',
        ],
        'ransel-pendaki' => [
            'Untuk setiap barang hanya ada dua pilihan: ambil atau tidak.',
            'Definisikan dp[c] = nilai terbaik yang bisa didapat dengan kapasitas c.',
            'Untuk setiap barang (w, v), loop c dari W turun ke w: dp[c] = max(dp[c], dp[c − w] + v).',
        ],
        'bagi-dua-adil' => [
            'Jika satu bagian berjumlah s, bagian lainnya total − s, dan selisihnya |total − 2s|.',
            'Cari semua jumlah yang bisa dibentuk dari sebagian kantong (subset sum, mirip knapsack 0/1).',
            'Pilih s ≤ total/2 terbesar yang bisa dibentuk, lalu jawabannya total − 2s.',
        ],
        'lcs-dna' => [
            'Definisikan dp[i][j] = panjang LCS dari i huruf pertama A dan j huruf pertama B.',
            'Jika A[i−1] = B[j−1]: dp[i][j] = dp[i−1][j−1] + 1.',
            'Jika berbeda: dp[i][j] = max(dp[i−1][j], dp[i][j−1]). Baris & kolom 0 bernilai 0.',
        ],
        'jarak-edit' => [
            'Tabelnya sama dengan LCS: dp[i][j] untuk prefiks kedua kata.',
            'Base case: dp[i][0] = i (hapus semua) dan dp[0][j] = j (sisip semua).',
            'Jika hurufnya beda: dp[i][j] = 1 + min(dp[i−1][j−1], dp[i−1][j], dp[i][j−1]).',
        ],
    ],
];
