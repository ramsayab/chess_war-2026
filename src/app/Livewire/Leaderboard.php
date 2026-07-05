<?php

namespace App\Livewire;

use Livewire\Component;

class Leaderboard extends Component
{
    public $search = '';

    public function render()
    {
        $leaderboardQuery = \App\Models\User::select('users.id', 'users.name')
            ->selectRaw('count(matches.id) as total_matches')
            ->selectRaw('sum(case when matches.is_win = 1 then 1 else 0 end) as won_matches')
            ->leftJoin('matches', 'users.id', '=', 'matches.user_id')
            ->where(function($query) {
                $query->where('users.is_admin', '!=', 1)
                      ->orWhereNull('users.is_admin');
            })
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['admin', 'super_admin']);
            });

        if (!empty($this->search)) {
            $leaderboardQuery->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.username', 'like', '%' . $this->search . '%');
            });
        }

        $leaderboard = $leaderboardQuery->groupBy('users.id', 'users.name')
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

        return view('livewire.leaderboard', compact('leaderboard'));
    }
}
