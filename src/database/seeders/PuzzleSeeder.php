<?php

namespace Database\Seeders;

use App\Models\Puzzle;
use Illuminate\Database\Seeder;

class PuzzleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puzzles = [
            [
                'name' => "Scholar's Mate",
                'difficulty' => 'easy',
                'diff_label' => 'Mate in 1',
                'fen' => 'r1bqkb1r/pppp1ppp/2n2n2/4p2Q/2B1P3/8/PPPP1PPP/RNB1K1NR w KQkq - 4 4',
                'description' => 'White to move. Deliver checkmate in 1.',
                'side_to_move' => 'white',
                'solution' => ['h5f7'],
                'moves_limit' => 1,
            ],
            [
                'name' => 'Back Rank Mate',
                'difficulty' => 'easy',
                'diff_label' => 'Mate in 1',
                'fen' => '6k1/5ppp/8/8/8/8/8/4R1K1 w - - 0 1',
                'description' => 'White to move. Back rank mate in 1.',
                'side_to_move' => 'white',
                'solution' => ['e1e8'],
                'moves_limit' => 1,
            ],
            [
                'name' => 'Queen Infiltration',
                'difficulty' => 'easy',
                'diff_label' => 'Mate in 2',
                'fen' => 'r1b2r1k/pp3p1p/2n2P1p/4p2Q/8/8/PPP2PPP/R3K2R w KQ - 0 1',
                'description' => 'White to move. Find checkmate in 2.',
                'side_to_move' => 'white',
                'solution' => ['h5h6', 'h8g8', 'h6g7'],
                'moves_limit' => 2,
            ],
            [
                'name' => 'Queen Mate',
                'difficulty' => 'easy',
                'diff_label' => 'Mate in 1',
                'fen' => 'k7/8/2K5/8/8/8/1Q6/8 w - - 0 1',
                'description' => 'White to move. Deliver checkmate.',
                'side_to_move' => 'white',
                'solution' => ['b2b7'],
                'moves_limit' => 1,
            ],
            [
                'name' => 'Bishop Mate',
                'difficulty' => 'easy',
                'diff_label' => 'Mate in 1',
                'fen' => 'k7/P7/1K6/8/8/8/8/1B6 w - - 0 1',
                'description' => 'White to move. Checkmate in 1.',
                'side_to_move' => 'white',
                'solution' => ['b1e4'],
                'moves_limit' => 1,
            ],
            [
                'name' => 'Rook Ladder',
                'difficulty' => 'medium',
                'diff_label' => 'Mate in 2',
                'fen' => '4k3/8/8/8/8/8/1R6/R3K3 w - - 0 1',
                'description' => 'White to move. Checkmate in 2 moves.',
                'side_to_move' => 'white',
                'solution' => ['a1a7', 'e8d8', 'b2b8'],
                'moves_limit' => 2,
            ],
            [
                'name' => 'Queen & King Dance',
                'difficulty' => 'medium',
                'diff_label' => 'Mate in 2',
                'fen' => 'k7/8/1K6/8/8/8/8/1Q6 w - - 0 1',
                'description' => 'White to move. Deliver mate in 2.',
                'side_to_move' => 'white',
                'solution' => ['b1h7', 'a8b8', 'h7b7'],
                'moves_limit' => 2,
            ],
            [
                'name' => 'Rook Roller',
                'difficulty' => 'medium',
                'diff_label' => 'Mate in 2',
                'fen' => 'k7/p7/2K5/8/8/8/1R6/8 w - - 0 1',
                'description' => 'White to move. Find mate in 2.',
                'side_to_move' => 'white',
                'solution' => ['b2h2', 'a8b8', 'h2h8'],
                'moves_limit' => 2,
            ],
            [
                'name' => 'Rook Rollercoaster',
                'difficulty' => 'hard',
                'diff_label' => 'Mate in 3',
                'fen' => 'k7/8/2K5/8/8/8/8/B3R3 w - - 0 1',
                'description' => 'White to move. Deliver mate in 3.',
                'side_to_move' => 'white',
                'solution' => ['e1e8', 'a8a7', 'a1d4', 'a7a6', 'e8a8'],
                'moves_limit' => 3,
            ],
            [
                'name' => 'Royal Zugzwang',
                'difficulty' => 'hard',
                'diff_label' => 'Mate in 3',
                'fen' => 'k7/8/2K5/8/8/8/8/R7 w - - 0 1',
                'description' => 'White to move. Move the rook to mate in 3.',
                'side_to_move' => 'white',
                'solution' => ['a1h1', 'a8b8', 'h1h7', 'b8c8', 'h7h8'],
                'moves_limit' => 3,
            ],
        ];

        foreach ($puzzles as $puzzle) {
            Puzzle::create($puzzle);
        }
    }
}
