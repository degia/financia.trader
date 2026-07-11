<div>
    <div class="mb-6">
        <h1 class="page-title">Analytics</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Deep dive into your trading performance</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stat-box label="Win Rate" :value="$stats['win_rate'] . '%'" icon="target" />
        <x-stat-box label="Profit Factor" :value="$stats['profit_factor']" icon="chart-bar" />
        <x-stat-box label="Total P&L" :value="'$' . number_format($stats['total_pnl'], 2)" icon="wallet" :trend="$stats['total_pnl'] >= 0 ? 'up' : 'down'" />
        <x-stat-box label="Avg R:R" :value="$stats['avg_rr']" icon="percentage" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-glass-card title="Performance by Pair" subtitle="P&L breakdown per asset">
            @if(count($byPair) > 0)
                <div class="space-y-3">
                    @foreach($byPair as $pair)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ $pair['pair'] }}</span>
                                <span class="text-zinc-400 dark:text-zinc-500">({{ $pair['total'] }} trades)</span>
                            </div>
                            <span class="font-medium {{ $pair['total_pnl'] >= 0 ? 'stat-profit' : 'stat-loss' }}">
                                {{ $pair['total_pnl'] >= 0 ? '+' : '' }}${{ number_format($pair['total_pnl'], 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-400 text-center py-8">No completed trades yet.</p>
            @endif
        </x-glass-card>

        <x-glass-card title="Performance by Strategy" subtitle="Which strategies work best?">
            @if(count($byStrategy) > 0)
                <div class="space-y-3">
                    @foreach($byStrategy as $s)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ $s['strategy'] }}</span>
                                <span class="text-zinc-400 dark:text-zinc-500">({{ $s['total'] }} trades)</span>
                            </div>
                            <span class="font-medium {{ $s['total_pnl'] >= 0 ? 'stat-profit' : 'stat-loss' }}">
                                {{ $s['total_pnl'] >= 0 ? '+' : '' }}${{ number_format($s['total_pnl'], 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-400 text-center py-8">No strategy data yet. Add strategies to your trades.</p>
            @endif
        </x-glass-card>
    </div>
</div>
