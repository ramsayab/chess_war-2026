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
            'puzzle_id' => 'required|exists:puzzles,id',
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

    // 8. Puzzle - AI Generate
    Route::post('/puzzle/generate', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->is_admin && !auth()->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can generate puzzles.',
            ], 403);
        }

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini API key is not configured.',
            ], 500);
        }

        $request->validate([
            'difficulty' => 'required|string|in:easy,medium,hard',
            'moves_limit' => 'required|integer|min:1|max:3',
        ]);

        $difficulty = $request->difficulty;
        $movesLimit = (int) $request->moves_limit;

        $prompt = "Create a valid chess puzzle with the following settings:
- Difficulty: {$difficulty}
- Moves Limit: {$movesLimit} (mate in {$movesLimit})

The response must be in valid JSON format matching the following schema:
{
    \"name\": \"Creative name of the puzzle\",
    \"difficulty\": \"{$difficulty}\",
    \"diff_label\": \"Mate in {$movesLimit}\",
    \"fen\": \"A valid FEN string representing the starting position where it is the player's turn to move (e.g. if player is white, FEN side to move is 'w', if player is black, FEN side to move is 'b'). The position MUST be a real, legally reachable chess position.\",
    \"description\": \"Short instruction (e.g. 'White to move. Deliver checkmate in {$movesLimit}.')\",
    \"side_to_move\": \"white\" or \"black\",
    \"solution\": [\"move1\", \"move2\", ..., \"moveM\"],
    \"moves_limit\": {$movesLimit}
}

IMPORTANT rules for the solution and FEN:
1. The solution moves must be in standard UCI notation (e.g. 'e2e4', 'g8f6', 'd2d4', 'e7e5').
2. The solution format is alternating player moves and opponent responses: [PlayerMove1, OpponentResponse1, PlayerMove2, OpponentResponse2, ..., PlayerMoveN]. The length of the array must be exactly 2 * {$movesLimit} - 1.
3. The final move in the solution MUST deliver a forced checkmate (mate in {$movesLimit}).
4. Ensure all moves are legal from the starting FEN and lead to a forced win.
5. Do NOT include any markdown block (like ```json) in the response text itself since structured JSON format is requested.";

        try {
            $response = Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API request failed: ' . $response->body(),
                ], 500);
            }

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($text)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response structure from Gemini API.',
                ], 500);
            }

            $puzzleData = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($puzzleData['fen']) || empty($puzzleData['solution'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI generated invalid puzzle data format.',
                    'raw' => $text,
                ], 500);
            }

            $puzzle = \App\Models\Puzzle::create([
                'name' => $puzzleData['name'] ?? 'AI Generated Puzzle',
                'difficulty' => $puzzleData['difficulty'] ?? $difficulty,
                'diff_label' => $puzzleData['diff_label'] ?? "Mate in {$movesLimit}",
                'fen' => $puzzleData['fen'],
                'description' => $puzzleData['description'] ?? 'Solve the puzzle',
                'side_to_move' => $puzzleData['side_to_move'] ?? 'white',
                'solution' => $puzzleData['solution'],
                'moves_limit' => $movesLimit,
            ]);

            return response()->json([
                'success' => true,
                'puzzle' => $puzzle,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during generation: ' . $e->getMessage(),
            ], 500);
        }
    });
});

