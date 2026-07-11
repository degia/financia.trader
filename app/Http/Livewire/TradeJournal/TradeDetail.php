<?php

namespace App\Http\Livewire\TradeJournal;

use App\Models\Trade;
use Livewire\Component;

class TradeDetail extends Component
{
    public ?Trade $trade = null;

    public function mount(Trade $trade): void
    {
        $this->trade = $trade->load(['portfolio', 'strategyRef']);
    }

    public function render()
    {
        return view('livewire.trade-journal.trade-detail')
            ->layout('layouts.app', ['title' => $this->trade->pair . ' Trade — ' . config('app.name')]);
    }
}
