<?php

namespace App\Http\Livewire\TradeJournal;

use Livewire\Component;

class TradeJournalPage extends Component
{
    public function render()
    {
        return view('livewire.trade-journal.trade-journal-page')
            ->layout('layouts.app', ['title' => 'Trade Journal - ' . config('app.name')]);
    }
}
