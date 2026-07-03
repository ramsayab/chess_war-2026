<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChessTip extends Model
{
    use HasFactory;

    protected $table = 'chess_tips';

    protected $fillable = [
        'tip',
        'author',
    ];
}
