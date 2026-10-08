<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $guarded = [];

    public const TRACKS = [
        'fondasi' => [
            'name' => 'Fondasi',
            'tagline' => 'Kompleksitas, rekursi, dan pencarian cepat',
            'icon' => 'fondasi',
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
            'tagline' => 'Menjawab jutaan pertanyaan rentang dengan cepat',
            'icon' => 'struktur-data',
        ],
        'matematika' => [
            'name' => 'Matematika',
            'tagline' => 'Bilangan prima, modulo, dan seni menghitung',
            'icon' => 'matematika',
        ],
        'string' => [
            'name' => 'String',
            'tagline' => 'Mencari pola di dalam teks',
            'icon' => 'string',
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
