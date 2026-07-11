<?php

namespace App\Services;

use App\Models\Trade;
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getWinRate(): float
    {
        $total = Trade::closed()->count();
        if ($total === 0) return 0;

        return round((Trade::wins()->count() / $total) * 100, 1);
    }

    public function getAverageRR(): float
    {
        $trades = Trade::closed()
            ->whereNotNull('stop_loss')
            ->whereNotNull('take_profit')
            ->get();

        if ($trades->isEmpty()) return 0;

        $rrSum = $trades->map(function ($trade) {
            $risk = abs($trade->entry_price - $trade->stop_loss);
            $reward = abs($trade->take_profit - $trade->entry_price);
            return $risk > 0 ? $reward / $risk : 0;
        })->avg();

        return round($rrSum, 2);
    }

    public function getTotalPnl(): float
    {
        return round(Trade::closed()->sum('pnl_amount'), 2);
    }

    public function getProfitFactor(): float
    {
        $wins = Trade::wins()->sum('pnl_amount');
        $losses = abs(Trade::losses()->sum('pnl_amount'));

        if ($losses == 0) return $wins > 0 ? 999.99 : 0;

        return round($wins / $losses, 2);
    }

    public function getPerformanceByPair(): array
    {
        return Trade::closed()
            ->select('pair', DB::raw('count(*) as total'), DB::raw('sum(pnl_amount) as total_pnl'), DB::raw('avg(pnl_amount) as avg_pnl'))
            ->groupBy('pair')
            ->orderByDesc('total_pnl')
            ->get()
            ->toArray();
    }

    public function getPerformanceByStrategy(): array
    {
        return Trade::closed()
            ->whereNotNull('strategy')
            ->select('strategy', DB::raw('count(*) as total'), DB::raw('sum(pnl_amount) as total_pnl'), DB::raw('avg(pnl_amount) as avg_pnl'))
            ->groupBy('strategy')
            ->orderByDesc('total_pnl')
            ->get()
            ->toArray();
    }

    public function getMonthlyPnl(): array
    {
        return Trade::closed()
            ->select(
                DB::raw("DATE_FORMAT(entry_date, '%Y-%m') as month"),
                DB::raw('sum(pnl_amount) as total_pnl'),
                DB::raw('count(*) as total_trades')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    public function getEquityCurveData(): array
    {
        $firstBalance = BalanceHistory::latest()->first();
        $initialBalance = $firstBalance?->balance ?? 0;

        $trades = Trade::closed()
            ->orderBy('exit_date')
            ->get(['pnl_amount', 'exit_date']);

        $equity = $initialBalance;
        $data = [['date' => now()->subDays(30)->format('Y-m-d'), 'equity' => $initialBalance]];

        foreach ($trades as $trade) {
            $equity += $trade->pnl_amount;
            $data[] = [
                'date' => $trade->exit_date?->format('Y-m-d') ?? $trade->created_at->format('Y-m-d'),
                'equity' => round($equity, 2),
            ];
        }

        return $data;
    }

    public function getStats(): array
    {
        return [
            'total_trades' => Trade::count(),
            'open_trades' => Trade::open()->count(),
            'closed_trades' => Trade::closed()->count(),
            'win_rate' => $this->getWinRate(),
            'total_pnl' => $this->getTotalPnl(),
            'profit_factor' => $this->getProfitFactor(),
            'avg_rr' => $this->getAverageRR(),
            'wins' => Trade::wins()->count(),
            'losses' => Trade::losses()->count(),
        ];
    }
}
