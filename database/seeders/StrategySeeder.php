<?php

namespace Database\Seeders;

use App\Models\Strategy;
use App\Models\User;
use Illuminate\Database\Seeder;

class StrategySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $strategies = [
            [
                'name' => 'Breakout Momentum',
                'description' => 'Trade breakout dari support/resistance dengan konfirmasi volume tinggi. Entry setelah candle close di luar range.',
            ],
            [
                'name' => 'Supply & Demand Zone',
                'description' => 'Entry di zona supply/demand yang sudah teridentifikasi. Menunggu rejection candle untuk konfirmasi.',
            ],
            [
                'name' => 'Trend Following EMA',
                'description' => 'Follow trend menggunakan EMA 20 & 50. Entry saat pullback ke EMA 20 dengan RSI belum overbought.',
            ],
            [
                'name' => 'Liquidity Grab',
                'description' => 'Trading false breakout / stop hunt di level-level likuiditas besar. Entry setelah fakey pattern.',
            ],
            [
                'name' => 'Scalping M15',
                'description' => 'Scalping di timeframe M15 menggunakan Bollinger Band squeeze dan RSI divergence.',
            ],
        ];

        foreach ($strategies as $strategy) {
            Strategy::create(array_merge($strategy, ['user_id' => $user->id]));
        }
    }
}
