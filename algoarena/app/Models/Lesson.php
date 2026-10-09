<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $guarded = [];

    /** Urutan track mengikuti urutan folder materi CP (01 Dasar sampai 16 Teknik Kontes). */
    public const TRACKS = [
        'fondasi' => [
            'name' => 'Fondasi & STL',
            'tagline' => 'Kompleksitas, kontainer STL, dan alat dasar C++',
            'icon' => 'fondasi',
        ],
        'array' => [
            'name' => 'Teknik Array',
            'tagline' => 'Prefix sum, two pointers, stack dan deque monoton',
            'icon' => 'array',
        ],
        'sorting' => [
            'name' => 'Sorting & Searching',
            'tagline' => 'Mengurutkan, membagi dua, dan mencari jawaban',
            'icon' => 'sorting',
        ],
        'rekursi' => [
            'name' => 'Rekursi & Brute Force',
            'tagline' => 'Mencoba semua kemungkinan dengan rapi',
            'icon' => 'rekursi',
        ],
        'greedy' => [
            'name' => 'Greedy',
            'tagline' => 'Mengambil yang terbaik saat ini, lalu membuktikannya',
            'icon' => 'greedy',
        ],
        'matematika' => [
            'name' => 'Teori Bilangan & Kombinatorika',
            'tagline' => 'Prima, modulo, matriks, dan seni menghitung',
            'icon' => 'matematika',
        ],
        'graph' => [
            'name' => 'Graph',
            'tagline' => 'Simpul, sisi, dan cara menjelajahinya',
            'icon' => 'graph',
        ],
        'dp' => [
            'name' => 'Dynamic Programming',
            'tagline' => 'Pecah masalah besar, ingat jawaban kecil',
            'icon' => 'dp',
        ],
        'struktur-data' => [
            'name' => 'Struktur Data',
            'tagline' => 'Menjawab query rentang dalam O(log N)',
            'icon' => 'struktur-data',
        ],
        'pohon' => [
            'name' => 'Pohon',
            'tagline' => 'Akar, subtree, leluhur, dan dekomposisi pohon',
            'icon' => 'pohon',
        ],
        'string' => [
            'name' => 'String',
            'tagline' => 'Mencari pola dan membandingkan teks dengan cepat',
            'icon' => 'string',
        ],
        'geometri' => [
            'name' => 'Geometri',
            'tagline' => 'Titik, garis, dan selubung cembung',
            'icon' => 'geometri',
        ],
        'permainan' => [
            'name' => 'Teori Permainan',
            'tagline' => 'Posisi menang, posisi kalah, dan bilangan Grundy',
            'icon' => 'permainan',
        ],
        'kontes' => [
            'name' => 'Teknik Kontes',
            'tagline' => 'Menemukan bug sendiri dan memakai keacakan dengan benar',
            'icon' => 'kontes',
        ],
        'latihan' => [
            'name' => 'Latihan Terpadu',
            'tagline' => 'Kumpulan soal DP, graf, dan greedy bertingkat',
            'icon' => 'latihan',
        ],
    ];

    public const LEVELS = [
        1 => ['name' => 'Dasar', 'icon' => '', 'class' => 'lvl-1'],
        2 => ['name' => 'Menengah', 'icon' => '', 'class' => 'lvl-2'],
        3 => ['name' => 'Lanjut', 'icon' => '', 'class' => 'lvl-3'],
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return array{name: string, icon: string, class: string} */
    public function levelInfo(): array
    {
        return self::LEVELS[$this->level] ?? self::LEVELS[1];
    }

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class)->orderBy('position');
    }

    public function trackName(): string
    {
        return self::TRACKS[$this->track]['name'] ?? $this->track;
    }
}
