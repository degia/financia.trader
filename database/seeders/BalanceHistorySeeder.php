<?php

namespace Database\Seeders;

use App\Models\BalanceHistory;
use App\Models\Portfolio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BalanceHistorySeeder extends Seeder
{
    public function run(): void
    {
        $portfolio = Portfolio::where('name', 'Forex Live Account')->first();
        $cryptoPortfolio = Portfolio::where('name', 'Crypto Swing Account')->first();

        $forexHistory = [
            [30, 5000.00, 5000.00, 5000.00, 0, 'Initial deposit'],
            [28, 5231.50, 5235.00, 0, 0, null],
            [26, 5363.00, 5368.00, 0, 0, null],
            [24, 5305.50, 5302.00, 0, 0, null],
            [22, 5445.50, 5450.00, 0, 0, null],
            [20, 5508.00, 5512.00, 0, 0, null],
            [18, 5417.00, 5412.00, 0, 0, null],
            [16, 5562.00, 5568.00, 0, 0, null],
            [14, 5558.50, 5560.00, 0, 0, null],
            [12, 5621.00, 5625.00, 0, 0, null],
            [10, 5757.50, 5762.00, 0, 0, null],
            [9, 5829.00, 5835.00, 0, 0, null],
            [8, 5738.50, 5732.00, 0, 0, null],
            [7, 5927.50, 5935.00, 0, 0, null],
            [5, 5984.00, 5990.00, 0, 0, null],
            [4, 6100.50, 6108.00, 0, 0, null],
            [3, 5977.00, 5972.00, 0, 0, null],
            [2, 6073.50, 6080.00, 0, 0, null],
            [1, 6228.50, 6235.00, 0, 0, null],
            [0, 5432.67, 5432.67, 0, 0, 'Current balance after all trades'],
        ];

        foreach ($forexHistory as [$daysAgo, $balance, $equity, $deposit, $withdrawal, $notes]) {
            BalanceHistory::create([
                'portfolio_id' => $portfolio->id,
                'date' => Carbon::now()->subDays($daysAgo),
                'balance' => $balance,
                'equity' => $equity,
                'deposit' => $deposit,
                'withdrawal' => $withdrawal,
                'notes' => $notes,
            ]);
        }

        $cryptoHistory = [
            [20, 2000.00, 2000.00, 2000.00, 0, 'Initial deposit'],
            [15, 2085.00, 2090.00, 0, 0, null],
            [11, 2116.00, 2120.00, 0, 0, null],
            [7, 2068.00, 2062.00, 0, 0, null],
            [6, 2020.00, 2015.00, 0, 0, null],
            [0, 1875.50, 1875.50, 0, 0, 'Current balance'],
        ];

        foreach ($cryptoHistory as [$daysAgo, $balance, $equity, $deposit, $withdrawal, $notes]) {
            BalanceHistory::create([
                'portfolio_id' => $cryptoPortfolio->id,
                'date' => Carbon::now()->subDays($daysAgo),
                'balance' => $balance,
                'equity' => $equity,
                'deposit' => $deposit,
                'withdrawal' => $withdrawal,
                'notes' => $notes,
            ]);
        }
    }
}
