<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Http\Controllers\AuthController;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return auth()->check() ? redirect('/dashboard') : app(AuthController::class)->showLogin();
})->name('login');

Route::get('/register', function () {
    return auth()->check() ? redirect('/dashboard') : app(AuthController::class)->showRegister();
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/google/mock', function () {
    return view('auth.google_mock');
})->name('auth.google.mock');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/dashboard', function (Illuminate\Http\Request $request) {
    $user = auth()->user();
    $tab = $request->query('tab', 'overview');

    // Fetch stats
    $totalMatches = $user->matches()->count();
    $wonMatches = $user->matches()->where('is_win', true)->count();
    $winrate = $totalMatches > 0 ? round(($wonMatches / $totalMatches) * 100) : 0;

    $powerCounts = $user->matches()
        ->select('power_type', \DB::raw('count(*) as count'))
        ->groupBy('power_type')
        ->pluck('count', 'power_type')
        ->toArray();

    $avgSeconds = $user->matches()->avg('total_time') ?? 0;
    $avgMinutes = round($avgSeconds / 60, 1);

    // Fetch history
    $matches = $user->matches()->orderBy('created_at', 'desc')->get();

    // Fetch saved game
    $savedGame = $user->savedGame;

    // Fetch leaderboard data
    $leaderboard = \App\Models\User::select('users.id', 'users.name')
        ->selectRaw('count(matches.id) as total_matches')
        ->selectRaw('sum(case when matches.is_win = 1 then 1 else 0 end) as won_matches')
        ->leftJoin('matches', 'users.id', '=', 'matches.user_id')
        ->where(function($query) {
            $query->where('users.is_admin', '!=', 1)
                  ->orWhereNull('users.is_admin');
        })
        ->whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })
        ->groupBy('users.id', 'users.name')
        ->orderByRaw('sum(case when matches.is_win = 1 then 1 else 0 end) desc')
        ->orderByRaw('count(matches.id) desc')
        ->take(20)
        ->get()
        ->map(function ($player, $index) {
            $total = (int)$player->total_matches;
            $won = (int)$player->won_matches;
            return (object)[
                'rank' => $index + 1,
                'id' => $player->id,
                'name' => $player->name,
                'total_matches' => $total,
                'won_matches' => $won,
                'winrate' => $total > 0 ? round(($won / $total) * 100) : 0,
            ];
        });

    // Fetch puzzle progress
    $puzzlesSolved = $user->puzzleAttempts()->where('solved', true)->count();
    $puzzlesTotal = 10; // Total hardcoded puzzles

    // 1. Calculate Player Rank
    $rankings = \App\Models\User::select('users.id')
        ->selectRaw('count(matches.id) as total_matches')
        ->selectRaw('sum(case when matches.is_win = 1 then 1 else 0 end) as won_matches')
        ->leftJoin('matches', 'users.id', '=', 'matches.user_id')
        ->where(function($query) {
            $query->where('users.is_admin', '!=', 1)
                  ->orWhereNull('users.is_admin');
        })
        ->whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })
        ->groupBy('users.id')
        ->orderByRaw('sum(case when matches.is_win = 1 then 1 else 0 end) desc')
        ->orderByRaw('count(matches.id) desc')
        ->get();

    $isAdmin = ($user->is_admin == 1) || $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
    if ($isAdmin) {
        $myRank = 'Admin';
    } else {
        $myRankIndex = $rankings->search(fn($player) => $player->id == $user->id);
        $myRank = $myRankIndex !== false ? $myRankIndex + 1 : '-';
    }

    // 2. Calculate XP and Level based on difficulty
    $difficultyXp = [
        100 => ['win' => 100, 'loss' => 25],
        500 => ['win' => 150, 'loss' => 35],
        1000 => ['win' => 200, 'loss' => 50],
        2500 => ['win' => 300, 'loss' => 75],
        5000 => ['win' => 500, 'loss' => 125],
    ];

    $totalXp = 0;
    $difficultyCounts = [
        100 => 0,
        500 => 0,
        1000 => 0,
        2500 => 0,
        5000 => 0,
    ];

    foreach ($matches as $m) {
        $diff = (int)$m->difficulty;
        if ($diff === 0) {
            $diff = 1000;
        }
        
        $cfg = $difficultyXp[$diff] ?? ['win' => 200, 'loss' => 50];
        if ($m->is_win) {
            $totalXp += $cfg['win'];
        } else {
            $totalXp += $cfg['loss'];
        }

        if (isset($difficultyCounts[$diff])) {
            $difficultyCounts[$diff]++;
        }
    }
    $totalXp += ($puzzlesSolved * 50);

    $level = floor($totalXp / 1000) + 1;
    $xpInCurrentLevel = $totalXp % 1000;
    $nextLevelXp = 1000;
    $xpProgressPercent = ($xpInCurrentLevel / $nextLevelXp) * 100;

    // 3. Fetch Daily chess tip
    $dailyTip = \App\Models\ChessTip::inRandomOrder()->first() ?? (object)[
        'tip' => 'Always look for check, captures, and threats before making your move.',
        'author' => 'Chess War Tip'
    ];

    // 4. Trends for KPI Cards
    $recentMatches5 = $user->matches()->orderBy('created_at', 'desc')->take(5)->get();
    $recentCount5 = $recentMatches5->count();
    $recentWins5 = $recentMatches5->where('is_win', true)->count();
    $recentWinrate = $recentCount5 > 0 ? round(($recentWins5 / $recentCount5) * 100) : 0;
    $winrateDiff = $totalMatches > 0 ? ($recentWinrate - $winrate) : 0;

    $recent7Matches = $user->matches()->orderBy('created_at', 'asc')->take(7)->get();
    $runningWins = 0;
    $runningTotal = 0;
    $winratePoints = [];
    foreach ($recent7Matches as $m) {
        $runningTotal++;
        if ($m->is_win) {
            $runningWins++;
        }
        $winratePoints[] = round(($runningWins / $runningTotal) * 100);
    }
    if (count($winratePoints) < 2) {
        $winratePoints = [50, 50];
    }

    $recentAvgDuration = $recentCount5 > 0 ? $recentMatches5->avg('total_time') : 0;
    $recentAvgMinutes = round($recentAvgDuration / 60, 1);
    $durationDiff = $totalMatches > 0 ? round($recentAvgMinutes - $avgMinutes, 1) : 0;

    $durationPoints = [];
    foreach ($recent7Matches as $m) {
        $durationPoints[] = round($m->total_time / 60, 1);
    }
    if (count($durationPoints) < 2) {
        $durationPoints = [5, 5];
    }

    $solvedToday = $user->puzzleAttempts()
        ->where('solved', true)
        ->whereDate('solved_at', \Carbon\Carbon::today())
        ->count();

    $puzzleAttempts7 = $user->puzzleAttempts()->orderBy('created_at', 'asc')->take(7)->get();
    $runningSolves = 0;
    $puzzlePoints = [];
    foreach ($puzzleAttempts7 as $pa) {
        if ($pa->solved) {
            $runningSolves++;
        }
        $puzzlePoints[] = $runningSolves;
    }
    if (count($puzzlePoints) < 2) {
        $puzzlePoints = [0, 0];
    }

    // 5. Recent matches for activity preview
    $recentMatchesForPreview = $user->matches()->orderBy('created_at', 'desc')->take(3)->get();

    return view('dashboard', compact(
        'tab', 'winrate', 'powerCounts', 'avgMinutes',
        'totalMatches', 'wonMatches', 'matches', 'savedGame',
        'leaderboard', 'puzzlesSolved', 'puzzlesTotal',
        'myRank', 'level', 'xpInCurrentLevel', 'nextLevelXp', 'xpProgressPercent',
        'dailyTip', 'winrateDiff', 'winratePoints', 'durationDiff', 'durationPoints',
        'solvedToday', 'puzzlePoints', 'recentMatchesForPreview', 'difficultyCounts'
    ));
})->middleware('auth')->name('dashboard');

Route::get('/game', function () {
    return view('game');
})->middleware('auth')->name('game');

Route::get('/puzzle', function () {
    return view('puzzle');
})->middleware('auth')->name('puzzle');

Route::post('/matches', function (Illuminate\Http\Request $request) {
    $request->validate([
        'is_win' => 'required|boolean',
        'total_time' => 'required|integer',
        'power_type' => 'nullable|string',
        'difficulty' => 'nullable|integer',
    ]);

    $match = \App\Models\ChessMatch::create([
        'user_id' => auth()->id(),
        'is_win' => $request->is_win,
        'total_time' => $request->total_time,
        'power_type' => $request->power_type,
        'difficulty' => $request->difficulty,
    ]);

    return response()->json([
        'success' => true,
        'match' => $match,
    ]);
})->middleware('auth');
