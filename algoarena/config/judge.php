<?php

return [
    /*
    | Judge C++ dijalankan di server memakai compiler lokal (g++).
    | PERINGATAN: kode siswa dieksekusi di komputer server. Judge menolak fungsi
    | berbahaya (system, akses file, dll.), tetapi ini bukan sandbox penuh.
    | Gunakan hanya di jaringan sekolah / komputer guru yang terpercaya.
    */
    'cpp' => [
        'enabled' => env('CPP_JUDGE', true),
        'compiler' => env('CPP_COMPILER', 'g++'),
        // Static agar tidak butuh DLL MinGW; stack 256 MB agar rekursi dalam (DFS) tidak crash.
        // -s (strip) membuat file lebih kecil dan jarang dikira virus oleh antivirus.
        'flags' => env('CPP_FLAGS', '-std=c++14 -O2 -s -static -static-libgcc -static-libstdc++ -Wl,--stack,268435456'),
        // Cadangan jika hasil kompilasi diblokir antivirus: link dinamis (DLL diambil dari folder compiler).
        'fallback_flags' => env('CPP_FALLBACK_FLAGS', '-std=c++14 -O2 -s -Wl,--stack,268435456'),
        'label' => env('CPP_LABEL', 'C++14 (GCC)'),
    ],
];
