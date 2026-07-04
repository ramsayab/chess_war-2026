<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Puzzle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'difficulty',
        'diff_label',
        'fen',
        'description',
        'side_to_move',
        'solution',
        'moves_limit',
    ];

    protected $casts = [
        'id' => 'string',
        'solution' => 'array',
        'moves_limit' => 'integer',
    ];

    protected $appends = [
        'sideToMove',
        'diffLabel',
        'movesLimit',
    ];

    public function getSideToMoveAttribute(): string
    {
        return $this->attributes['side_to_move'] ?? '';
    }

    public function getDiffLabelAttribute(): string
    {
        return $this->attributes['diff_label'] ?? '';
    }

    public function getMovesLimitAttribute(): int
    {
        return (int) ($this->attributes['moves_limit'] ?? 0);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(PuzzleAttempt::class);
    }
}
