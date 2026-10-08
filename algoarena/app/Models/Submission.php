<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $guarded = [];

    public const VERDICTS = [
        'AC' => 'Accepted',
        'PARTIAL' => 'Sebagian Benar',
        'WA' => 'Wrong Answer',
        'TLE' => 'Time Limit Exceeded',
        'RE' => 'Runtime Error',
        'CE' => 'Compile Error',
    ];

    protected function casts(): array
    {
        return ['results' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    public function verdictLabel(): string
    {
        return self::VERDICTS[$this->verdict] ?? $this->verdict;
    }
}
