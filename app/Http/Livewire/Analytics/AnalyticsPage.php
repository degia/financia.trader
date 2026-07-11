<?php

namespace App\Http\Livewire\Analytics;

use App\Services\AnalyticsService;
use Livewire\Component;

class AnalyticsPage extends Component
{
    public array $stats = [];
    public array $byPair = [];
    public array $byStrategy = [];
    public array $winLoss = [];

    public function boot(AnalyticsService $analytics): void
    {
        $this->stats = $analytics->getStats();
        $this->byPair = $analytics->getPerformanceByPair();
        $this->byStrategy = $analytics->getPerformanceByStrategy();
        $this->winLoss = $analytics->getWinLossCounts();
    }

    public function render()
    {
        return view('livewire.analytics.analytics-page')
            ->layout('layouts.app', ['title' => 'Analytics - ' . config('app.name')]);
    }
}
