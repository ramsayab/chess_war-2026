<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // 1. Leaderboard API
    Route::get('/leaderboard', function () {
        $leaderboard = \App\Models\User::select('users.id', 'users.name')
            ->selectRaw('count(matches.id) as total_matches')
            ->selectRaw('sum(case when matches.is_win = 1 then 1 else 0 end) as won_matches')
            ->join('matches', 'users.id', '=', 'matches.user_id')
            ->where(function($query) {
                $query->where('users.is_admin', '!=', 1)
                      ->orWhereNull('users.is_admin');
            })
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['admin', 'super_admin']);
            })
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('sum(case when matches.is_win = 1 then 1 else 0 end) desc')
            ->take(10)
            ->get()
            ->map(function ($player, $index) {
                $total = (int)$player->total_matches;
                $won = (int)$player->won_matches;
                return [
                    'rank' => $index + 1,
                    'name' => $player->name,
                    'total_matches' => $total,
                    'won_matches' => $won,
                    'winrate' => $total > 0 ? round(($won / $total) * 100) . '%' : '0%',
                ];
            });

        return response()->json([
            'success' => true,
            'leaderboard' => $leaderboard,
        ]);
    });

    // 2. Personal Stats API
    Route::get('/user/stats', function () {
        $user = auth()->user();
        $total = $user->matches()->count();
        $won = $user->matches()->where('is_win', true)->count();
        $avgSeconds = $user->matches()->avg('total_time') ?? 0;

        return response()->json([
            'success' => true,
            'stats' => [
                'name' => $user->name,
                'total_matches' => $total,
                'won_matches' => $won,
                'winrate' => $total > 0 ? round(($won / $total) * 100) : 0,
                'avg_duration_minutes' => round($avgSeconds / 60, 1),
            ]
        ]);
    });

    // 3. Powers API
    Route::get('/powers', function () {
        return response()->json([
            'success' => true,
            'powers' => [
                [
                    'value' => 'blink_knight',
                    'name' => 'Blink Knight',
                    'description' => 'Knight jumps with a longer reach, doubling the usual movement patterns.'
                ],
                [
                    'value' => 'super_rook',
                    'name' => 'Super Rook',
                    'description' => 'Rook keeps straight lines and gains one-step forward diagonals.'
                ],
                [
                    'value' => 'confused_pawn',
                    'name' => 'Confused Pawn',
                    'description' => 'Pawn can move backward too, making file control much more chaotic.'
                ],
                [
                    'value' => 'undying_king',
                    'name' => 'Undying King',
                    'description' => 'King has 2 lives. The enemy piece that captures the King dies, and the King is restored.'
                ],
                [
                    'value' => 'omni_queen',
                    'name' => 'Omni Queen',
                    'description' => 'Queen can move like a Queen and jump like a Knight.'
                ],
                [
                    'value' => 'grey_bishop',
                    'name' => 'Grey Bishop',
                    'description' => 'Bishop can shift 1 step left/right (changing square color) and then slide diagonally.'
                ]
            ]
        ]);
    });

    // 4. Save Game API
    Route::post('/game/save', function (Illuminate\Http\Request $request) {
        $request->validate([
            'fen' => 'required|string',
            'power_type' => 'nullable|string',
            'difficulty' => 'nullable|integer',
        ]);

        $savedGame = \App\Models\SavedGame::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'fen' => $request->fen,
                'power_type' => $request->power_type,
                'difficulty' => $request->difficulty,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Game state saved successfully.',
            'saved_game' => $savedGame,
        ]);
    });

    // 5. Resume Game API
    Route::get('/game/resume', function () {
        $savedGame = auth()->user()->savedGame;

        if (!$savedGame) {
            return response()->json([
                'success' => false,
                'message' => 'No saved game found for this user.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'saved_game' => $savedGame,
        ]);
    });

    // 6. Puzzle - Record Completion
    Route::post('/puzzle/complete', function (Illuminate\Http\Request $request) {
        $request->validate([
            'puzzle_id' => 'required|string',
            'attempts' => 'required|integer|min:1',
        ]);

        $attempt = \App\Models\PuzzleAttempt::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'puzzle_id' => $request->puzzle_id,
            ],
            [
                'solved' => true,
                'attempts' => $request->attempts,
                'solved_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'attempt' => $attempt,
        ]);
    });

    // 7. Puzzle - Get User Progress
    Route::get('/puzzle/progress', function () {
        $attempts = auth()->user()->puzzleAttempts()
            ->where('solved', true)
            ->pluck('puzzle_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'solved_puzzles' => $attempts,
        ]);
    });
});

