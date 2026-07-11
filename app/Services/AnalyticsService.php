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

    public function getWinLossCounts(): array
    {
        return [
            'wins' => Trade::wins()->count(),
            'losses' => Trade::losses()->count(),
            'breakeven' => Trade::where('outcome', 'breakeven')->count(),
        ];
    }

    public function getDailyPnl(?int $month = null, ?int $year = null): array
    {
        $month = $month ?? (int) now()->format('m');
        $year = $year ?? (int) now()->format('Y');

        $dailyData = Trade::closed()
            ->whereMonth('exit_date', $month)
            ->whereYear('exit_date', $year)
            ->select(
                DB::raw('DATE(exit_date) as day'),
                DB::raw('SUM(pnl_amount) as total_pnl'),
                DB::raw('COUNT(*) as trade_count')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $result = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayOfWeek = (int) date('w', strtotime($date));

            // Skip weekends (0=Sun, 6=Sat)
            if ($dayOfWeek === 0 || $dayOfWeek === 6) {
                $result[] = ['date' => $date, 'pnl' => null, 'trades' => 0, 'dow' => $dayOfWeek];
                continue;
            }

            $record = $dailyData->get($date);
            $result[] = [
                'date' => $date,
                'pnl' => $record ? (float) $record->total_pnl : 0,
                'trades' => $record ? (int) $record->trade_count : 0,
                'dow' => $dayOfWeek,
            ];
        }

        return $result;
    }

    public function getStreakStats(): array
    {
        $trades = Trade::closed()
            ->orderBy('exit_date')
            ->get(['outcome']);

        $maxWinStreak = 0;
        $maxLossStreak = 0;
        $currentWin = 0;
        $currentLoss = 0;

        foreach ($trades as $trade) {
            if ($trade->outcome->value === 'win') {
                $currentWin++;
                $currentLoss = 0;
                $maxWinStreak = max($maxWinStreak, $currentWin);
            } elseif ($trade->outcome->value === 'loss') {
                $currentLoss++;
                $currentWin = 0;
                $maxLossStreak = max($maxLossStreak, $currentLoss);
            } else {
                $currentWin = 0;
                $currentLoss = 0;
            }
        }

        return [
            'max_win_streak' => $maxWinStreak,
            'max_loss_streak' => $maxLossStreak,
        ];
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
