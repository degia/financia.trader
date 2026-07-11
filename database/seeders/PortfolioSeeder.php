<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Forex Live Account',
            'initial_balance' => 5000.00,
            'current_balance' => 5432.67,
            'currency' => 'USD',
            'is_active' => true,
        ]);

        Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Crypto Swing Account',
            'initial_balance' => 2000.00,
            'current_balance' => 1875.50,
            'currency' => 'USD',
            'is_active' => false,
        ]);
    }
}
