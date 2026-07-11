<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Trade;
use App\Models\BalanceHistory;
use App\Services\AnalyticsService;
use Livewire\Component;

class DashboardPage extends Component
{
    public array $stats = [];
    public array $recentTrades = [];
    public array $equityCurve = [];
    public array $monthlyPnl = [];
    public string $pnlPeriod = 'all';

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
        $this->recentTrades = Trade::latest('entry_date')
            ->limit(10)
            ->get()
            ->toArray();

        $this->equityCurve = $this->getEquityCurveData();
        $this->monthlyPnl = $this->getMonthlyPnlData();
    }

    public function getEquityCurveData(): array
    {
        $firstBalance = BalanceHistory::latest()->first();
        $initialBalance = $firstBalance?->balance ?? 10000;

        $trades = Trade::closed()
            ->orderBy('exit_date')
            ->get(['pnl_amount', 'exit_date']);

        $equity = $initialBalance;
        $data = [['x' => now()->subDays(30)->timestamp * 1000, 'y' => $initialBalance]];

        foreach ($trades as $trade) {
            $equity += $trade->pnl_amount;
            $data[] = [
                'x' => ($trade->exit_date ?? $trade->created_at)->timestamp * 1000,
                'y' => round($equity, 2),
            ];
        }

        return $data;
    }

    public function getMonthlyPnlData(): array
    {
        $trades = Trade::closed()
            ->selectRaw("DATE_FORMAT(entry_date, '%Y-%m') as month, SUM(pnl_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $trades->map(fn($t) => ['x' => $t->month, 'y' => round($t->total, 2)])->toArray();
    }

    public function getProfitClass(): string
    {
        return $this->stats['total_pnl'] >= 0 ? 'stat-profit' : 'stat-loss';
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-page')
            ->layout('layouts.app', ['title' => 'Dashboard - ' . config('app.name')]);
    }
}
