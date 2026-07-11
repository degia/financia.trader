<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Portfolio;
use App\Models\Trade;
use App\Models\BalanceHistory;
use App\Services\AnalyticsService;
use Livewire\Component;

class DashboardPage extends Component
{
    public array $stats = [];
    public array $recentTrades = [];
    public array $equityCurve = [];
    public string $activePortfolio = '';

    public function boot(AnalyticsService $analytics): void
    {
        $this->stats = $analytics->getStats();
    }

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $portfolio = Portfolio::where('is_active', true)->first();

        if ($portfolio) {
            $this->activePortfolio = $portfolio->name;
            $this->stats['current_balance'] = (float) $portfolio->current_balance;
            $this->stats['initial_balance'] = (float) $portfolio->initial_balance;
            $this->stats['balance_change'] = (float) $portfolio->current_balance - (float) $portfolio->initial_balance;
            $this->stats['balance_change_pct'] = $portfolio->initial_balance > 0
                ? round((($portfolio->current_balance - $portfolio->initial_balance) / $portfolio->initial_balance) * 100, 1)
                : 0;

            $this->equityCurve = $this->getEquityCurveFromHistory($portfolio->id);
        } else {
            $this->stats['current_balance'] = 0;
            $this->stats['initial_balance'] = 0;
            $this->stats['balance_change'] = 0;
            $this->stats['balance_change_pct'] = 0;
            $this->equityCurve = [];
        }

        $this->recentTrades = Trade::with('portfolio')
            ->latest('entry_date')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function getEquityCurveFromHistory(int $portfolioId): array
    {
        $records = BalanceHistory::where('portfolio_id', $portfolioId)
            ->orderBy('date')
            ->get(['date', 'balance']);

        if ($records->isEmpty()) {
            return [];
        }

        return $records->map(fn ($record) => [
            'x' => $record->date->timestamp * 1000,
            'y' => (float) $record->balance,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-page')
            ->layout('layouts.app', ['title' => 'Dashboard — ' . config('app.name')]);
    }
}
