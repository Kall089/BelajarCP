<?php

/*
 * Flag kompilasi bawaan bergantung pada sistem operasi server:
 *  - Windows (MinGW g++): link statis agar tidak butuh DLL, stack 256 MB lewat linker.
 *    -s (strip) membuat file lebih kecil dan jarang dikira virus oleh antivirus.
 *  - macOS: "g++" sebenarnya clang dan tidak mendukung -static / -Wl,--stack.
 *    Stack utama diperbesar lewat -Wl,-stack_size (cadangan tanpa flag itu jika ditolak).
 *  - Linux: flag standar; stack diperbesar saat program dijalankan (ulimit).
 * Jika kompilasi gagal karena flag/linker (bukan karena kode siswa), judge otomatis
 * mencoba flag cadangan lalu flag paling sederhana "-std=c++14 -O2".
 */
$defaults = match (PHP_OS_FAMILY) {
    'Windows' => [
        '-std=c++14 -O2 -s -static -static-libgcc -static-libstdc++ -Wl,--stack,268435456',
        // Cadangan jika hasil kompilasi diblokir antivirus: link dinamis (DLL diambil dari folder compiler).
        '-std=c++14 -O2 -s -Wl,--stack,268435456',
    ],
    'Darwin' => [
        '-std=c++14 -O2 -Wl,-stack_size,0x10000000',
        '-std=c++14 -O2',
    ],
    default => [
        '-std=c++14 -O2',
        '-std=c++14 -O2',
    ],
};

return [
    /*
    | Judge C++ dijalankan di server memakai compiler lokal (g++ / clang++).
    | PERINGATAN: kode siswa dieksekusi di komputer server. Judge menolak fungsi
    | berbahaya (system, akses file, dll.), tetapi ini bukan sandbox penuh.
    | Gunakan hanya di jaringan sekolah / komputer guru yang terpercaya.
    */
    'cpp' => [
        'enabled' => env('CPP_JUDGE', true),
        'compiler' => env('CPP_COMPILER', 'g++'),
        'flags' => env('CPP_FLAGS', $defaults[0]),
        'fallback_flags' => env('CPP_FALLBACK_FLAGS', $defaults[1]),
        'label' => env('CPP_LABEL', 'C++14'),
    ],
];
