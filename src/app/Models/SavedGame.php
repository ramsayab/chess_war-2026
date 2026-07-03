<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fen',
        'power_type',
        'difficulty',
    ];

    protected $casts = [
        'difficulty' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
