<?php

namespace App\Http\Livewire\TradeJournal;

use App\Models\Trade;
use Livewire\Component;

class TradeDetail extends Component
{
    public ?Trade $trade = null;

    public function mount(int $trade): void
    {
        $this->trade = Trade::findOrFail($trade);
    }

    public function render()
    {
        return view('livewire.trade-journal.trade-detail')
            ->layout('layouts.app', ['title' => 'Trade Detail - ' . config('app.name')]);
    }
}
