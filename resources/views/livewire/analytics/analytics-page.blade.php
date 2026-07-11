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

    {{-- Donut Charts --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-glass-card title="Win Rate" subtitle="Win vs Loss">
            <div x-data="donutChart('winRateDonut')" class="flex flex-col items-center">
                <div x-ref="chart" class="w-full h-48"></div>
                <div class="text-center mt-1">
                    <div class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100" x-text="displayValue"></div>
                    <div class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400" x-text="displayLabel"></div>
                </div>
            </div>
        </x-glass-card>

        <x-glass-card title="Profit Factor" subtitle="Win vs Loss P&L">
            <div x-data="donutChart('profitFactorDonut')" class="flex flex-col items-center">
                <div x-ref="chart" class="w-full h-48"></div>
                <div class="text-center mt-1">
                    <div class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100" x-text="displayValue"></div>
                    <div class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400" x-text="displayLabel"></div>
                </div>
            </div>
        </x-glass-card>

        <x-glass-card title="Total P&L" subtitle="Trade outcome split">
            <div x-data="donutChart('totalPnlDonut')" class="flex flex-col items-center">
                <div x-ref="chart" class="w-full h-48"></div>
                <div class="text-center mt-1">
                    <div class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100" x-text="displayValue"></div>
                    <div class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400" x-text="displayLabel"></div>
                </div>
            </div>
        </x-glass-card>

        <x-glass-card title="Avg R:R" subtitle="Risk/Reward distribution">
            <div x-data="donutChart('avgRrDonut')" class="flex flex-col items-center">
                <div x-ref="chart" class="w-full h-48"></div>
                <div class="text-center mt-1">
                    <div class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100" x-text="displayValue"></div>
                    <div class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400" x-text="displayLabel"></div>
                </div>
            </div>
        </x-glass-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-glass-card title="Performance by Pair" subtitle="P&L breakdown per asset">
            @if (count($byPair) > 0)
                <div class="space-y-3">
                    @foreach ($byPair as $pair)
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
            @if (count($byStrategy) > 0)
                <div class="space-y-3">
                    @foreach ($byStrategy as $s)
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
                <p class="text-sm text-zinc-400 text-center py-8">No strategy data yet. Add strategies to your trades.
                </p>
            @endif
        </x-glass-card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
        <script>
            function getThemeColors() {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    isDark,
                    text: isDark ? '#a3a3a3' : '#525252',
                    textStrong: isDark ? '#fafafa' : '#0a0a0a',
                    grid: isDark ? 'rgba(250,250,250,0.06)' : 'rgba(10,10,10,0.06)',
                    surface: isDark ? '#1a1a1a' : '#f5f5f5',
                    profit: '#22c55e',
                    loss: '#ef4444',
                    neutral: '#525252',
                    accent: '#6366f1',
                };
            }

            function getDonutConfig(chartId) {
                const c = getThemeColors();
                const winLoss = @json($winLoss);
                const pfDonut = @json($profitFactorDonut);
                const pnlDonut = @json($totalPnlDonut);
                const rrDonut = @json($avgRrDonut);

                const configs = {
                    winRateDonut: {
                        series: [winLoss.wins, winLoss.losses, winLoss.breakeven],
                        labels: ['Win', 'Loss', 'Breakeven'],
                        colors: [c.profit, c.loss, c.neutral],
                        totalLabel: 'Win Rate',
                        totalFormatter: () => @json($stats['win_rate']) + '%',
                        valueFormatter: v => v + '',
                    },
                    profitFactorDonut: {
                        series: pfDonut.series,
                        labels: pfDonut.labels,
                        colors: [c.profit, c.loss],
                        totalLabel: 'P/F Ratio',
                        totalFormatter: () => @json($stats['profit_factor']),
                        valueFormatter: v => '$' + parseFloat(v).toLocaleString(),
                    },
                    totalPnlDonut: {
                        series: pnlDonut.series,
                        labels: pnlDonut.labels,
                        colors: [c.profit, c.loss, c.neutral],
                        totalLabel: 'Total P&L',
                        totalFormatter: () => '@json($stats["total_pnl"] >= 0 ? "+" : "")$' + @json(number_format($stats['total_pnl'], 2)),
                        valueFormatter: v => v + '',
                    },
                    avgRrDonut: {
                        series: rrDonut.series,
                        labels: rrDonut.labels,
                        colors: [c.loss, '#f59e0b', c.accent, c.profit],
                        totalLabel: 'Avg R:R',
                        totalFormatter: () => @json($stats['avg_rr']),
                        valueFormatter: v => v + '',
                    },
                };
                return configs[chartId] || configs.winRateDonut;
            }

            function donutChart(chartId) {
                return {
                    chart: null,
                    observer: null,
                    totalValue: '',
                    totalLabel: '',
                    hoveredValue: null,
                    hoveredLabel: null,

                    get displayValue() {
                        return this.hoveredValue !== null ? this.hoveredValue : this.totalValue;
                    },

                    get displayLabel() {
                        return this.hoveredLabel !== null ? this.hoveredLabel : this.totalLabel;
                    },

                    init() {
                        const c = getThemeColors();
                        const cfg = getDonutConfig(chartId);
                        this.totalValue = cfg.totalFormatter();
                        this.totalLabel = cfg.totalLabel;

                        const options = {
                            chart: {
                                type: 'donut',
                                height: '100%',
                                fontFamily: "'Inter', sans-serif"
                            },
                            series: cfg.series,
                            labels: cfg.labels,
                            colors: cfg.colors,
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '72%',
                                        labels: {
                                            show: false,
                                        },
                                    },
                                },
                            },
                            stroke: { width: 0 },
                            dataLabels: { enabled: false },
                            legend: {
                                position: 'bottom',
                                fontSize: '10px',
                                labels: { colors: c.text }
                            },
                            tooltip: {
                                theme: c.isDark ? 'dark' : 'light',
                            },
                            events: {
                                dataPointMouseOver: (event, chartCtx, config) => {
                                    const idx = config.dataPointIndex;
                                    this.hoveredValue = cfg.valueFormatter(cfg.series[idx]);
                                    this.hoveredLabel = cfg.labels[idx];
                                },
                            },
                        };

                        this.renderChart(options);

                        this.$el.addEventListener('mouseleave', () => {
                            this.hoveredValue = null;
                            this.hoveredLabel = null;
                        });

                        this.observer = new MutationObserver(() => {
                            this.renderChart(options);
                        });
                        this.observer.observe(document.documentElement, {
                            attributes: true,
                            attributeFilter: ['class']
                        });
                    },

                    renderChart(options) {
                        if (this.chart) this.chart.destroy();
                        this.chart = new ApexCharts(this.$refs.chart, options);
                        this.chart.render();
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
