<?php

namespace App\Livewire;

use Livewire\Component;

class DailyTip extends Component
{
    public $tipText = '';
    public $author = '';

    public function mount()
    {
        $this->refreshTip();
    }

    public function refreshTip()
    {
        $dailyTip = \App\Models\ChessTip::inRandomOrder()->first();
        if ($dailyTip) {
            $this->tipText = $dailyTip->tip;
            $this->author = $dailyTip->author ?? '';
        } else {
            $this->tipText = 'Always look for check, captures, and threats before making your move.';
            $this->author = 'Chess War Tip';
        }
    }

    public function render()
    {
        return view('livewire.daily-tip');
    }
}
