<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="page-title">Analytics</h1>
        <p class="page-subtitle">Deep dive into your trading performance</p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-glass-card>
            <div class="flex items-center justify-between mb-2">
                <span class="label-text">Win Rate</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="target" class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $stats['win_rate'] }}%</div>
            <div class="text-xs text-zinc-400 dark:text-zinc-600 mt-1">{{ $stats['wins'] }}W / {{ $stats['losses'] }}L</div>
        </x-glass-card>

        <x-glass-card>
            <div class="flex items-center justify-between mb-2">
                <span class="label-text">Profit Factor</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="chart-bar" class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight {{ ($stats['profit_factor'] ?? 0) >= 1 ? 'stat-profit' : 'stat-loss' }}">{{ $stats['profit_factor'] }}</div>
            <div class="text-xs text-zinc-400 dark:text-zinc-600 mt-1">wins ÷ losses</div>
        </x-glass-card>

        <x-glass-card>
            <div class="flex items-center justify-between mb-2">
                <span class="label-text">Total P&L</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="wallet" class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight {{ ($stats['total_pnl'] ?? 0) >= 0 ? 'stat-profit' : 'stat-loss' }}">
                {{ ($stats['total_pnl'] ?? 0) >= 0 ? '+' : '' }}${{ number_format($stats['total_pnl'] ?? 0, 2) }}
            </div>
            <div class="text-xs text-zinc-400 dark:text-zinc-600 mt-1">{{ $stats['closed_trades'] }} closed trades</div>
        </x-glass-card>

        <x-glass-card>
            <div class="flex items-center justify-between mb-2">
                <span class="label-text">Avg R:R</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <x-icon name="percentage" class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" />
                </div>
            </div>
            <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">1:{{ $stats['avg_rr'] }}</div>
            <div class="text-xs text-zinc-400 dark:text-zinc-600 mt-1">
                Best: {{ $streaks['max_win_streak'] }}W streak
            </div>
        </x-glass-card>
    </div>

    {{-- Row 1: Win Rate Donut + R:R Gauge --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Win Rate Donut --}}
        <x-glass-card title="Win Rate" subtitle="Win vs Loss distribution">
            <div x-data="winRateChart()" x-init="init()" class="h-64">
                <div x-ref="chart" class="w-full h-full"></div>
            </div>
        </x-glass-card>

        {{-- Risk:Reward Gauge --}}
        <x-glass-card title="Risk:Reward Ratio" subtitle="Average across all trades" class="lg:col-span-2">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <div x-data="rrGaugeChart()" x-init="init()" class="w-48 h-48 shrink-0">
                    <div x-ref="chart" class="w-full h-full"></div>
                </div>
                <div class="flex-1 space-y-4">
                    <div>
                        <div class="label-text mb-1">Average R:R</div>
                        <div class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                            1:{{ $stats['avg_rr'] }}
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Max win streak</span>
                            <span class="font-medium stat-profit">{{ $streaks['max_win_streak'] }} trades</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Max loss streak</span>
                            <span class="font-medium stat-loss">{{ $streaks['max_loss_streak'] }} trades</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Profit factor</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['profit_factor'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-glass-card>
    </div>

    {{-- Row 2: Performance by Pair + Performance by Strategy --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Performance by Pair --}}
        <x-glass-card title="Performance by Pair" subtitle="P&L breakdown per instrument">
            @if(count($byPair) > 0)
                <div x-data="pairChart()" x-init="init()" class="h-64 sm:h-72">
                    <div x-ref="chart" class="w-full h-full"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-48">
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">No completed trades yet.</p>
                </div>
            @endif
        </x-glass-card>

        {{-- Performance by Strategy --}}
        <x-glass-card title="Performance by Strategy" subtitle="Which strategies work best?">
            @if(count($byStrategy) > 0)
                <div x-data="strategyChart()" x-init="init()" class="h-64 sm:h-72">
                    <div x-ref="chart" class="w-full h-full"></div>
                </div>
            @else
                <div class="flex items-center justify-center h-48">
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">No strategy data yet.</p>
                </div>
            @endif
        </x-glass-card>
    </div>

    {{-- Row 3: Daily Heatmap --}}
    <x-glass-card title="Daily P&L Heatmap" subtitle="Profit/loss per trading day">
        <div class="flex items-center justify-between mb-4">
            <button wire:click="prevMonth" class="btn-ghost px-3 py-1.5 text-xs">
                <x-icon name="chevron-left" class="w-4 h-4" />
            </button>
            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                {{ date('F Y', mktime(0, 0, 0, $heatmapMonth, 1, $heatmapYear)) }}
            </span>
            <button wire:click="nextMonth" class="btn-ghost px-3 py-1.5 text-xs">
                <x-icon name="chevron-right" class="w-4 h-4" />
            </button>
        </div>

        @if(count($dailyPnl) > 0)
            <div x-data="heatmapChart()" x-init="init()">
                <div x-ref="chart" class="w-full overflow-x-auto scrollbar-thin">
                    <div x-ref="container" style="min-width: 700px; height: 140px;"></div>
                </div>
                {{-- Legend --}}
                <div class="flex items-center justify-center gap-4 mt-3">
                    <div class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                        <div class="w-3 h-3 rounded-sm bg-red-500/60"></div> Loss
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                        <div class="w-3 h-3 rounded-sm bg-zinc-200 dark:bg-zinc-800"></div> Breakeven
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                        <div class="w-3 h-3 rounded-sm bg-emerald-500/60"></div> Profit
                    </div>
                </div>
            </div>
        @else
            <div class="flex items-center justify-center h-32">
                <p class="text-sm text-zinc-400 dark:text-zinc-500">No trades this month.</p>
            </div>
        @endif
    </x-glass-card>

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
                bar: isDark ? '#d4d4d4' : '#262626',
            };
        }

        function makeChart(el, options, onDestroy) {
            let chart = new ApexCharts(el, options);
            chart.render();
            const obs = new MutationObserver(() => {
                chart.destroy();
                if (onDestroy) onDestroy();
                chart = new ApexCharts(el, options);
                chart.render();
            });
            obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            return { chart, obs };
        }

        // ── 1. Win Rate Donut ──
        function winRateChart() {
            return {
                init() {
                    const c = getThemeColors();
                    const data = @json($winLoss);
                    const options = {
                        chart: { type: 'donut', height: '100%', fontFamily: "'Inter', sans-serif" },
                        series: [data.wins, data.losses, data.breakeven],
                        labels: ['Win', 'Loss', 'Breakeven'],
                        colors: [c.profit, c.loss, c.neutral],
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '72%',
                                    labels: {
                                        show: true,
                                        name: { show: true, fontSize: '12px', color: c.text },
                                        value: {
                                            show: true,
                                            fontSize: '22px',
                                            fontWeight: 700,
                                            color: c.textStrong,
                                            formatter: v => v + '%',
                                        },
                                        total: {
                                            show: true,
                                            label: 'Win Rate',
                                            fontSize: '11px',
                                            color: c.text,
                                            formatter: () => @json($stats['win_rate']) + '%',
                                        },
                                    },
                                },
                            },
                        },
                        stroke: { width: 0 },
                        dataLabels: { enabled: false },
                        legend: { position: 'bottom', fontSize: '11px', labels: { colors: c.text } },
                        tooltip: { theme: c.isDark ? 'dark' : 'light', y: { formatter: v => v + ' trades' } },
                    };
                    makeChart(this.$refs.chart, options, () => this.init());
                },
            };
        }

        // ── 2. R:R Gauge ──
        function rrGaugeChart() {
            return {
                init() {
                    const c = getThemeColors();
                    const val = @json($stats['avg_rr']);
                    const options = {
                        chart: { type: 'radialBar', height: '100%', fontFamily: "'Inter', sans-serif" },
                        series: [Math.min(val / 5 * 100, 100)],
                        colors: [c.bar],
                        plotOptions: {
                            radialBar: {
                                hollow: { size: '65%', background: 'transparent' },
                                track: { background: c.surface, strokeWidth: '100%' },
                                dataLabels: {
                                    name: { show: false },
                                    value: {
                                        show: true,
                                        fontSize: '24px',
                                        fontWeight: 700,
                                        color: c.textStrong,
                                        offsetY: 6,
                                        formatter: () => '1:' + val,
                                    },
                                },
                            },
                        },
                        stroke: { lineCap: 'round' },
                        labels: ['R:R'],
                    };
                    makeChart(this.$refs.chart, options, () => this.init());
                },
            };
        }

        // ── 3. Performance by Pair (horizontal bar) ──
        function pairChart() {
            return {
                init() {
                    const c = getThemeColors();
                    const raw = @json($byPair);
                    const options = {
                        chart: { type: 'bar', height: '100%', horizontal: true, toolbar: { show: false }, fontFamily: "'Inter', sans-serif" },
                        series: [{ name: 'P&L', data: raw.map(r => ({ x: r.pair, y: parseFloat(r.total_pnl.toFixed(2)) })) }],
                        colors: [c.bar],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: true,
                                colors: {
                                    ranges: [
                                        { from: 0, to: Infinity, color: c.profit },
                                        { from: -Infinity, to: 0, color: c.loss },
                                    ],
                                },
                            },
                        },
                        xaxis: {
                            labels: { style: { colors: c.text, fontSize: '11px' }, formatter: v => '$' + v },
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                        },
                        yaxis: { labels: { style: { colors: c.text, fontSize: '11px', fontWeight: 500 } } },
                        grid: { borderColor: c.grid, xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                        tooltip: { theme: c.isDark ? 'dark' : 'light', y: { formatter: v => '$' + v.toFixed(2) } },
                        dataLabels: { enabled: false },
                    };
                    makeChart(this.$refs.chart, options, () => this.init());
                },
            };
        }

        // ── 4. Performance by Strategy (horizontal bar) ──
        function strategyChart() {
            return {
                init() {
                    const c = getThemeColors();
                    const raw = @json($byStrategy);
                    const options = {
                        chart: { type: 'bar', height: '100%', horizontal: true, toolbar: { show: false }, fontFamily: "'Inter', sans-serif" },
                        series: [{ name: 'P&L', data: raw.map(r => ({ x: r.strategy, y: parseFloat(r.total_pnl.toFixed(2)) })) }],
                        colors: [c.bar],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: true,
                                colors: {
                                    ranges: [
                                        { from: 0, to: Infinity, color: c.profit },
                                        { from: -Infinity, to: 0, color: c.loss },
                                    ],
                                },
                            },
                        },
                        xaxis: {
                            labels: { style: { colors: c.text, fontSize: '11px' }, formatter: v => '$' + v },
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                        },
                        yaxis: { labels: { style: { colors: c.text, fontSize: '11px', fontWeight: 500 } } },
                        grid: { borderColor: c.grid, xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                        tooltip: { theme: c.isDark ? 'dark' : 'light', y: { formatter: v => '$' + v.toFixed(2) } },
                        dataLabels: { enabled: false },
                    };
                    makeChart(this.$refs.chart, options, () => this.init());
                },
            };
        }

        // ── 5. Daily Heatmap (GitHub-style) ──
        function heatmapChart() {
            return {
                init() {
                    const c = getThemeColors();
                    const raw = @json($dailyPnl);
                    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

                    // Build week-based grid: each week is a column
                    const weeks = [];
                    let currentWeek = [];

                    raw.forEach((d, i) => {
                        if (d.dow === 0 || d.dow === 6) return; // skip weekends

                        const dayIdx = d.dow - 1; // 0=Mon..4=Fri
                        if (dayIdx === 0 && currentWeek.length > 0) {
                            weeks.push(currentWeek);
                            currentWeek = [];
                        }
                        currentWeek.push({ ...d, dayIdx });
                    });
                    if (currentWeek.length > 0) weeks.push(currentWeek);

                    // Flatten for ApexCharts heatmap
                    const series = days.map((day, dayIdx) => ({
                        name: day,
                        data: weeks.map((week, weekIdx) => {
                            const cell = week.find(c => c.dayIdx === dayIdx);
                            if (!cell) return { x: 'W' + (weekIdx + 1), y: null };
                            // Map: profit=1, loss=-1, breakeven=0
                            const val = cell.pnl === null ? null : (cell.pnl > 0 ? 1 : cell.pnl < 0 ? -1 : 0);
                            return { x: 'W' + (weekIdx + 1), y: val, pnl: cell.pnl, date: cell.date, trades: cell.trades };
                        }),
                    }));

                    const options = {
                        chart: { type: 'heatmap', height: 130, toolbar: { show: false }, fontFamily: "'Inter', sans-serif", background: 'transparent' },
                        series,
                        colors: ['#22c55e'],
                        plotOptions: {
                            heatmap: {
                                shadeIntensity: 0,
                                radius: 4,
                                useFillColorAsStroke: false,
                                colorScale: {
                                    ranges: [
                                        { from: -1, to: -1, color: 'rgba(239,68,68,0.55)' },
                                        { from: 0, to: 0, color: c.surface },
                                        { from: 1, to: 1, color: 'rgba(34,197,94,0.55)' },
                                    ],
                                },
                            },
                        },
                        xaxis: {
                            labels: { show: false },
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            categories: weeks.map((_, i) => 'W' + (i + 1)),
                        },
                        yaxis: {
                            min: 0,
                            max: 4,
                            reversed: false,
                            labels: { show: true, formatter: (v) => days[v] || '', style: { colors: c.text, fontSize: '10px' } },
                        },
                        tooltip: {
                            theme: c.isDark ? 'dark' : 'light',
                            custom: function({ seriesIndex, dataPointIndex, w }) {
                                const cell = w.config.series[seriesIndex].data[dataPointIndex];
                                if (!cell || cell.pnl === null || cell.pnl === undefined) return '<div class="p-2 text-xs">No data</div>';
                                const pnlStr = cell.pnl === 0 ? '$0.00' : (cell.pnl > 0 ? '+$' : '-$') + Math.abs(cell.pnl).toFixed(2);
                                const color = cell.pnl > 0 ? 'color:#22c55e' : cell.pnl < 0 ? 'color:#ef4444' : '';
                                return '<div class="p-2 text-xs">' +
                                    '<div class="font-medium mb-1">' + cell.date + '</div>' +
                                    '<div style="' + color + '">' + pnlStr + '</div>' +
                                    '<div class="text-zinc-400 mt-0.5">' + cell.trades + ' trade(s)</div></div>';
                            },
                        },
                        dataLabels: { enabled: false },
                        stroke: { width: 2, colors: ['transparent'] },
                    };

                    makeChart(this.$refs.container, options, () => this.init());
                },
            };
        }
    </script>
    @endpush
</div>
