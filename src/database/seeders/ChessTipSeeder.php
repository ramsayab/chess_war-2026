<?php

namespace Database\Seeders;

use App\Models\ChessTip;
use Illuminate\Database\Seeder;

class ChessTipSeeder extends Seeder
{
    public function run(): void
    {
        $tips = [
            [
                'tip' => 'Always search for check, capture, and threat on every single move. These are the forcing moves you must calculate first.',
                'author' => 'Tactical Principles',
            ],
            [
                'tip' => 'Control the center of the board with your pawns and pieces. The player who controls the center dominates the battlefield.',
                'author' => 'Classical Strategy',
            ],
            [
                'tip' => 'Develop your knights before your bishops. Knights are shorter-range pieces and benefit from being positioned earlier.',
                'author' => 'Opening Rules',
            ],
            [
                'tip' => 'Do not move the same piece multiple times in the opening unless necessary. Time (tempo) is a crucial asset.',
                'author' => 'Development Speed',
            ],
            [
                'tip' => 'Castle early to keep your king safe and bring your rook into the action. A king in the center is an easy target.',
                'author' => 'King Safety',
            ],
            [
                'tip' => 'A knight on the rim is dim. Keep your knights towards the center where they can attack up to eight squares.',
                'author' => 'Tarrasch Rule',
            ],
            [
                'tip' => 'Rooks belong on open files where they can exert maximum pressure and potentially penetrate to the 7th rank.',
                'author' => 'Rook Activity',
            ],
            [
                'tip' => 'When you are ahead in material, trade pieces, not pawns. When you are behind, trade pawns, not pieces.',
                'author' => 'Endgame Guide',
            ],
            [
                'tip' => 'Every pawn move creates permanent weaknesses. Think carefully before moving your pawns, as they cannot move backward.',
                'author' => 'Pawn Structure',
            ],
            [
                'tip' => 'Chess is 99% tactics. Solve puzzles daily to train your pattern recognition and calculation skills.',
                'author' => 'Richard Teichmann',
            ]
        ];

        foreach ($tips as $tip) {
            ChessTip::firstOrCreate(['tip' => $tip['tip']], $tip);
        }
    }
}
