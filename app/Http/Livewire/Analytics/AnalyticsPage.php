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
    public array $dailyPnl = [];
    public array $streaks = [];
    public int $heatmapMonth;
    public int $heatmapYear;

    public function boot(AnalyticsService $analytics): void
    {
        $this->stats = $analytics->getStats();
        $this->byPair = $analytics->getPerformanceByPair();
        $this->byStrategy = $analytics->getPerformanceByStrategy();
        $this->winLoss = $analytics->getWinLossCounts();
        $this->streaks = $analytics->getStreakStats();
    }

    public function mount(): void
    {
        $this->heatmapMonth = (int) now()->format('m');
        $this->heatmapYear = (int) now()->format('Y');
        $this->loadHeatmap();
    }

    public function loadHeatmap(): void
    {
        $analytics = app(AnalyticsService::class);
        $this->dailyPnl = $analytics->getDailyPnl($this->heatmapMonth, $this->heatmapYear);
    }

    public function prevMonth(): void
    {
        $this->heatmapMonth--;
        if ($this->heatmapMonth < 1) {
            $this->heatmapMonth = 12;
            $this->heatmapYear--;
        }
        $this->loadHeatmap();
    }

    public function nextMonth(): void
    {
        $this->heatmapMonth++;
        if ($this->heatmapMonth > 12) {
            $this->heatmapMonth = 1;
            $this->heatmapYear++;
        }
        $this->loadHeatmap();
    }

    public function render()
    {
        return view('livewire.analytics.analytics-page')
            ->layout('layouts.app', ['title' => 'Analytics — ' . config('app.name')]);
    }
}
