<div wire:loading.class="opacity-60" wire:loading.class.delay="opacity-100" wire:target="loadData">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Overview of your trading performance</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">

        {{-- Current Balance --}}
        <x-glass-card>
            <div class="flex items-center justify-between mb-3">
                <span class="label-text">Current Balance</span>
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="wallet" class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                ${{ number_format($stats['current_balance'] ?? 0, 2) }}
            </div>
            @if(isset($stats['balance_change']))
                <div class="flex items-center gap-1.5 mt-1.5">
                    <span class="text-xs font-medium {{ ($stats['balance_change'] ?? 0) >= 0 ? 'stat-profit' : 'stat-loss' }}">
                        {{ ($stats['balance_change'] ?? 0) >= 0 ? '+' : '' }}${{ number_format(abs($stats['balance_change']), 2) }}
                    </span>
                    <span class="text-xs text-zinc-400 dark:text-zinc-600">
                        ({{ ($stats['balance_change_pct'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['balance_change_pct'] ?? 0 }}%)
                    </span>
                </div>
            @endif
        </x-glass-card>

        {{-- Total P&L --}}
        <x-glass-card>
            <div class="flex items-center justify-between mb-3">
                <span class="label-text">Total P&L</span>
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight {{ ($stats['total_pnl'] ?? 0) >= 0 ? 'stat-profit' : 'stat-loss' }}">
                {{ ($stats['total_pnl'] ?? 0) >= 0 ? '+' : '' }}${{ number_format($stats['total_pnl'] ?? 0, 2) }}
            </div>
            <div class="flex items-center gap-1.5 mt-1.5">
                <span class="text-xs text-zinc-400 dark:text-zinc-600">PF {{ $stats['profit_factor'] ?? '0.00' }}</span>
            </div>
        </x-glass-card>

        {{-- Win Rate --}}
        <x-glass-card>
            <div class="flex items-center justify-between mb-3">
                <span class="label-text">Win Rate</span>
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="target" class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $stats['win_rate'] ?? '0.0' }}%
            </div>
            <div class="flex items-center gap-1.5 mt-1.5">
                <span class="text-xs stat-profit">{{ $stats['wins'] ?? 0 }}W</span>
                <span class="text-xs text-zinc-300 dark:text-zinc-700">/</span>
                <span class="text-xs stat-loss">{{ $stats['losses'] ?? 0 }}L</span>
            </div>
        </x-glass-card>

        {{-- Total Trades --}}
        <x-glass-card>
            <div class="flex items-center justify-between mb-3">
                <span class="label-text">Total Trades</span>
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="chart-bar" class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $stats['total_trades'] ?? 0 }}
            </div>
            <div class="flex items-center gap-1.5 mt-1.5">
                <span class="text-xs text-zinc-400 dark:text-zinc-600">
                    {{ $stats['open_trades'] ?? 0 }} open · {{ $stats['closed_trades'] ?? 0 }} closed
                </span>
            </div>
        </x-glass-card>

    </div>

    {{-- Equity Curve --}}
    <x-glass-card title="Equity Curve" subtitle="Portfolio balance over time" class="mb-6">
        @if(count($equityCurve) > 1)
            <div x-data="equityChart()" x-init="init()" class="h-64 sm:h-72 -mx-2">
                <div x-ref="chart" class="w-full h-full"></div>
            </div>
        @else
            <div class="flex items-center justify-center h-64">
                <div class="text-center">
                    <x-icon name="chart-bar" class="w-10 h-10 text-zinc-300 dark:text-zinc-600 mx-auto mb-2" />
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Not enough data for equity curve</p>
                </div>
            </div>
        @endif
    </x-glass-card>

    {{-- Recent Trades --}}
    <x-glass-card title="Recent Trades" subtitle="Last 5 transactions">
        @if(count($recentTrades) > 0)
            <div class="overflow-x-auto -mx-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200/50 dark:border-zinc-700/50">
                            <th class="px-6 py-3 text-left label-text">Pair</th>
                            <th class="px-6 py-3 text-left label-text hidden sm:table-cell">Direction</th>
                            <th class="px-6 py-3 text-right label-text hidden sm:table-cell">Entry</th>
                            <th class="px-6 py-3 text-right label-text hidden md:table-cell">Exit</th>
                            <th class="px-6 py-3 text-right label-text">P&L</th>
                            <th class="px-6 py-3 text-right label-text">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTrades as $trade)
                            <tr class="border-b border-zinc-100/50 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $trade['pair'] }}</div>
                                    <div class="text-xs text-zinc-400 dark:text-zinc-600 sm:hidden">
                                        {{ ucfirst($trade['direction']) }}
                                    </div>
                                </td>
                                <td class="px-6 py-3 hidden sm:table-cell">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium {{ $trade['direction'] === 'long' ? 'stat-profit' : 'stat-loss' }}">
                                        <x-icon :name="$trade['direction'] === 'long' ? 'arrow-up-right' : 'arrow-down-right'" class="w-3 h-3" />
                                        {{ ucfirst($trade['direction']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right text-zinc-500 dark:text-zinc-400 font-mono text-xs hidden sm:table-cell">
                                    {{ $trade['entry_price'] }}
                                </td>
                                <td class="px-6 py-3 text-right text-zinc-500 dark:text-zinc-400 font-mono text-xs hidden md:table-cell">
                                    {{ $trade['exit_price'] ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-right font-medium font-mono text-xs {{ $trade['pnl_amount'] >= 0 ? 'stat-profit' : 'stat-loss' }}">
                                    {{ $trade['pnl_amount'] >= 0 ? '+' : '' }}${{ number_format($trade['pnl_amount'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider
                                        {{ match($trade['outcome']) {
                                            'win' => 'bg-emerald-400/10 text-emerald-600 dark:text-emerald-400',
                                            'loss' => 'bg-red-400/10 text-red-600 dark:text-red-400',
                                            'breakeven' => 'bg-zinc-400/10 text-zinc-600 dark:text-zinc-400',
                                            default => 'bg-zinc-400/10 text-zinc-600 dark:text-zinc-400',
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
                observer: null,
                init() {
                    const rawData = @json($equityCurve);
                    const self = this;

                    const getOptions = () => {
                        const isDark = document.documentElement.classList.contains('dark');
                        const lineColor = isDark ? '#fafafa' : '#0a0a0a';
                        const textColor = isDark ? '#71717a' : '#a3a3a3';
                        const gridColor = isDark ? 'rgba(250,250,250,0.06)' : 'rgba(10,10,10,0.06)';
                        const gradientStart = isDark ? 'rgba(250,250,250,0.15)' : 'rgba(10,10,10,0.10)';
                        const gradientEnd = isDark ? 'rgba(250,250,250,0.01)' : 'rgba(10,10,10,0.01)';

                        return {
                            chart: {
                                type: 'area',
                                height: '100%',
                                toolbar: { show: false },
                                background: 'transparent',
                                foreColor: textColor,
                                fontFamily: "'Inter', sans-serif",
                            },
                            series: [{ name: 'Balance', data: rawData }],
                            colors: [lineColor],
                            stroke: { width: 2, curve: 'smooth' },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.2,
                                    opacityTo: 0.02,
                                    colorStops: [
                                        { offset: 0, color: gradientStart, opacity: 0.2 },
                                        { offset: 100, color: gradientEnd, opacity: 0.02 },
                                    ],
                                },
                            },
                            xaxis: {
                                type: 'datetime',
                                labels: { style: { colors: textColor, fontSize: '11px' }, format: 'dd MMM' },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                                crosshairs: { show: false },
                            },
                            yaxis: {
                                labels: {
                                    style: { colors: textColor, fontSize: '11px' },
                                    formatter: v => '$' + v.toLocaleString(),
                                },
                                forceNiceScale: true,
                            },
                            grid: {
                                borderColor: gridColor,
                                strokeDashArray: 3,
                                xaxis: { lines: { show: false } },
                                yaxis: { lines: { show: true } },
                                padding: { left: 8, right: 8 },
                            },
                            tooltip: {
                                theme: isDark ? 'dark' : 'light',
                                style: { fontSize: '12px', fontFamily: "'Inter', sans-serif" },
                                y: { formatter: v => '$' + v.toLocaleString(undefined, { minimumFractionDigits: 2 }) },
                                x: { format: 'dd MMM yyyy' },
                            },
                            markers: {
                                size: 0,
                                hover: { size: 5, sizeOffset: 3 },
                            },
                            dataLabels: { enabled: false },
                        };
                    };

                    const render = () => {
                        if (self.chart) self.chart.destroy();
                        self.chart = new ApexCharts(self.$refs.chart, getOptions());
                        self.chart.render();
                    };

                    render();

                    self.observer = new MutationObserver(() => render());
                    self.observer.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class'],
                    });
                },

                destroy() {
                    if (this.chart) this.chart.destroy();
                    if (this.observer) this.observer.disconnect();
                },
            };
        }
    </script>
    @endpush
</div>
