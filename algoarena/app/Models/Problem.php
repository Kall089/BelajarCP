<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Problem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'hints' => 'array',
            'starter' => 'array',
            'solutions' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(TestCase::class)->orderBy('position');
    }

    public function samples(): HasMany
    {
        return $this->testCases()->where('is_sample', true);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /** Batas waktu per tes; Python (Pyodide) lebih lambat sehingga diberi kelonggaran. */
    public function timeLimitFor(string $language): int
    {
        return $language === 'python' ? $this->time_limit * 3 : $this->time_limit;
    }

    /** Bandingkan output: abaikan spasi di akhir baris dan baris kosong di akhir. */
    public static function outputsMatch(string $expected, string $actual): bool
    {
        $normalize = fn (string $s) => rtrim(implode("\n", array_map('rtrim', preg_split('/\r?\n/', $s))));

        return $normalize($expected) === $normalize($actual);
    }
}
