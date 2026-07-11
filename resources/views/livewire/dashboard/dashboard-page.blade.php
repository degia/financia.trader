<div>
    <div class="mb-6">
        <h1 class="page-title">Dashboard</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Overview of your trading performance</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stat-box label="Total P&L" :value="'$' . number_format($stats['total_pnl'], 2)" icon="wallet" :trend="$stats['total_pnl'] >= 0 ? 'up' : 'down'" :trendValue="$stats['total_pnl'] >= 0 ? '+' . number_format($stats['total_pnl'], 2) : number_format($stats['total_pnl'], 2)" />

        <x-stat-box label="Win Rate" :value="$stats['win_rate'] . '%'" icon="target" :trendValue="$stats['wins'] . 'W / ' . $stats['losses'] . 'L'" />

        <x-stat-box label="Total Trades" :value="$stats['total_trades']" icon="arrow-trending-up" :trendValue="$stats['open_trades'] . ' open'" />

        <x-stat-box label="Profit Factor" :value="$stats['profit_factor']" icon="chart-bar" :trendValue="'Avg R:R ' . $stats['avg_rr']" />
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Equity Curve --}}
        <x-glass-card title="Equity Curve" subtitle="Portfolio growth over time">
            <div x-data="equityChart()" x-init="init()" class="h-64">
                <div x-ref="chart" class="w-full h-full"></div>
            </div>
        </x-glass-card>

        {{-- Monthly P&L --}}
        <x-glass-card title="Monthly P&L" subtitle="Profit and loss by month">
            <div x-data="monthlyPnlChart()" x-init="init()" class="h-64">
                <div x-ref="chart" class="w-full h-full"></div>
            </div>
        </x-glass-card>
    </div>

    {{-- Recent Trades --}}
    <x-glass-card title="Recent Trades" subtitle="Last 10 trades">
        @if(count($recentTrades) > 0)
            <div class="overflow-x-auto -mx-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200/50 dark:border-zinc-700/50">
                            <th class="px-6 py-3 text-left label-text">Pair</th>
                            <th class="px-6 py-3 text-left label-text">Direction</th>
                            <th class="px-6 py-3 text-left label-text">Entry</th>
                            <th class="px-6 py-3 text-left label-text">Exit</th>
                            <th class="px-6 py-3 text-right label-text">P&L</th>
                            <th class="px-6 py-3 text-right label-text">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTrades as $trade)
                            <tr class="border-b border-zinc-100/50 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $trade['pair'] }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium {{ $trade['direction'] === 'long' ? 'text-emerald-400' : 'text-red-400' }}">
                                        <x-icon :name="$trade['direction'] === 'long' ? 'arrow-up-right' : 'arrow-down-right'" class="w-3 h-3" />
                                        {{ ucfirst($trade['direction']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">{{ $trade['entry_price'] }}</td>
                                <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">{{ $trade['exit_price'] ?? '—' }}</td>
                                <td class="px-6 py-3 text-right font-medium {{ $trade['pnl_amount'] >= 0 ? 'stat-profit' : 'stat-loss' }}">
                                    {{ $trade['pnl_amount'] >= 0 ? '+' : '' }}${{ number_format($trade['pnl_amount'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider
                                        {{ match($trade['outcome']) {
                                            'win' => 'bg-emerald-400/10 text-emerald-400',
                                            'loss' => 'bg-red-400/10 text-red-400',
                                            'breakeven' => 'bg-zinc-400/10 text-zinc-400',
                                            default => 'bg-blue-400/10 text-blue-400',
                                        } }}">
                                        {{ $trade['outcome'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <x-icon name="document-text" class="w-12 h-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" />
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No trades yet. Start by adding your first trade.</p>
                <a href="{{ route('trades.index') }}" class="btn-primary mt-4 inline-flex">
                    <x-icon name="plus" class="w-4 h-4" />
                    Add Trade
                </a>
            </div>
        @endif
    </x-glass-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
    <script>
        function equityChart() {
            return {
                chart: null,
                init() {
                    const data = @json($equityCurve);
                    const options = {
                        chart: { type: 'area', height: '100%', toolbar: { show: false }, background: 'transparent', foreColor: '#71717a' },
                        series: [{ name: 'Equity', data: data }],
                        colors: ['#a1a1aa'],
                        stroke: { width: 2, curve: 'smooth' },
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } },
                        xaxis: { type: 'datetime', labels: { style: { colors: '#71717a', fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { labels: { style: { colors: '#71717a', fontSize: '11px' }, formatter: v => '$' + v.toLocaleString() } },
                        grid: { borderColor: 'rgba(113,113,122,0.08)', strokeDashArray: 3 },
                        tooltip: { theme: 'dark', style: { fontSize: '12px' }, y: { formatter: v => '$' + v.toLocaleString() } },
                        theme: { mode: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light' }
                    };

                    const applyTheme = () => {
                        const isDark = document.documentElement.classList.contains('dark');
                        options.tooltip.theme = isDark ? 'dark' : 'light';
                        if (this.chart) this.chart.destroy();
                        this.chart = new ApexCharts(this.$refs.chart, options);
                        this.chart.render();
                    };

                    applyTheme();
                    const observer = new MutationObserver(() => applyTheme());
                    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                }
            }
        }

        function monthlyPnlChart() {
            return {
                chart: null,
                init() {
                    const data = @json($monthlyPnl);
                    const options = {
                        chart: { type: 'bar', height: '100%', toolbar: { show: false }, background: 'transparent', foreColor: '#71717a' },
                        series: [{ name: 'P&L', data: data }],
                        colors: ['#a1a1aa'],
                        plotOptions: { bar: { borderRadius: 6, columnWidth: '60%', colors: { ranges: [{ from: -Infinity, to: 0, color: '#ef4444' }, { from: 0, to: Infinity, color: '#22c55e' }] } } },
                        xaxis: { type: 'category', labels: { style: { colors: '#71717a', fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { labels: { style: { colors: '#71717a', fontSize: '11px' }, formatter: v => '$' + v.toLocaleString() } },
                        grid: { borderColor: 'rgba(113,113,122,0.08)', strokeDashArray: 3 },
                        tooltip: { theme: 'dark', style: { fontSize: '12px' }, y: { formatter: v => '$' + v.toLocaleString() } },
                        theme: { mode: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light' }
                    };

                    const applyTheme = () => {
                        const isDark = document.documentElement.classList.contains('dark');
                        options.tooltip.theme = isDark ? 'dark' : 'light';
                        if (this.chart) this.chart.destroy();
                        this.chart = new ApexCharts(this.$refs.chart, options);
                        this.chart.render();
                    };

                    applyTheme();
                    const observer = new MutationObserver(() => applyTheme());
                    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                }
            }
        }
    </script>
    @endpush
</div>
