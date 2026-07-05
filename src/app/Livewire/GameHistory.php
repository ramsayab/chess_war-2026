<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\WithPagination;

class GameHistory extends Component
{
    use WithPagination;

    public function deleteSavedGame()
    {
        $user = auth()->user();
        if ($user && $user->savedGame) {
            $user->savedGame->delete();
            session()->flash('message', 'Active saved game deleted successfully!');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $savedGame = $user ? $user->savedGame : null;
        $matches = $user ? $user->matches()->orderBy('created_at', 'desc')->paginate(5) : collect();

        return view('livewire.game-history', compact('savedGame', 'matches'));
    }
}
