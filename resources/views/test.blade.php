<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Test</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800|jetbrains-mono:400,500" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data class="min-h-screen">

    <div class="min-h-screen p-4 sm:p-8 max-w-4xl mx-auto space-y-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title">Design System Test</h1>
                <p class="page-subtitle">Verify layout, dark mode toggle, and component styles</p>
            </div>
            <x-theme-toggle />
        </div>

        {{-- Color Palette --}}
        <x-glass-card title="Color Palette">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="space-y-2">
                    <div class="h-16 rounded-xl bg-zinc-900 dark:bg-[#0a0a0a] border border-zinc-200 dark:border-zinc-800"></div>
                    <span class="label-text">Base Dark</span>
                </div>
                <div class="space-y-2">
                    <div class="h-16 rounded-xl bg-white border border-zinc-200 dark:border-zinc-800"></div>
                    <span class="label-text">Base Light</span>
                </div>
                <div class="space-y-2">
                    <div class="h-16 rounded-xl stat-profit-bg border border-emerald-200 dark:border-emerald-500/20"></div>
                    <span class="label-text">Profit BG</span>
                </div>
                <div class="space-y-2">
                    <div class="h-16 rounded-xl stat-loss-bg border border-red-200 dark:border-red-500/20"></div>
                    <span class="label-text">Loss BG</span>
                </div>
            </div>
        </x-glass-card>

        {{-- Status Colors --}}
        <x-glass-card title="Status Colors">
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center space-y-2">
                    <span class="text-3xl font-bold stat-profit">+$1,245.50</span>
                    <span class="label-text">Profit</span>
                </div>
                <div class="text-center space-y-2">
                    <span class="text-3xl font-bold stat-loss">-$432.00</span>
                    <span class="label-text">Loss</span>
                </div>
                <div class="text-center space-y-2">
                    <span class="text-3xl font-bold stat-neutral">$0.00</span>
                    <span class="label-text">Breakeven</span>
                </div>
            </div>
        </x-glass-card>

        {{-- Buttons --}}
        <x-glass-card title="Buttons">
            <div class="flex flex-wrap gap-3">
                <button class="btn-primary">Primary</button>
                <button class="btn-secondary">Secondary</button>
                <button class="btn-ghost">Ghost</button>
                <button class="btn-danger">Danger</button>
                <button class="btn-profit">Profit Action</button>
            </div>
        </x-glass-card>

        {{-- Form Elements --}}
        <x-glass-card title="Form Elements">
            <div class="space-y-4">
                <div>
                    <label class="label-text mb-1.5 block">Trade Pair</label>
                    <input type="text" class="input-field" placeholder="e.g. EUR/USD" value="EUR/USD" />
                </div>
                <div>
                    <label class="label-text mb-1.5 block">Notes</label>
                    <textarea class="input-field" rows="3" placeholder="Add trade notes..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-text mb-1.5 block">Entry Price</label>
                        <input type="number" class="input-field" placeholder="0.00000" step="0.00001" />
                    </div>
                    <div>
                        <label class="label-text mb-1.5 block">Exit Price</label>
                        <input type="number" class="input-field" placeholder="0.00000" step="0.00001" />
                    </div>
                </div>
            </div>
        </x-glass-card>

        {{-- Stats Grid --}}
        <x-glass-card title="Stats">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <x-stat-box label="Total Trades" value="23" icon="chart-bar" />
                <x-stat-box label="Win Rate" value="73.7%" icon="trophy" trend="up" trendValue="+5.2%" />
                <x-stat-box label="Total PnL" value="+$1,365" icon="arrow-trending-up" trend="up" trendValue="+27.3%" />
                <x-stat-box label="Profit Factor" value="3.24" icon="target" />
            </div>
        </x-glass-card>

        {{-- Glass Variations --}}
        <x-glass-card title="Glass Card Variations" subtitle="Testing backdrop blur and transparency">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="glass rounded-xl p-4 text-center">
                    <span class="label-text">Glass</span>
                </div>
                <div class="glass-strong rounded-xl p-4 text-center">
                    <span class="label-text">Glass Strong</span>
                </div>
                <div class="glass-card !p-4 text-center">
                    <span class="label-text">Glass Card</span>
                </div>
            </div>
        </x-glass-card>

        {{-- Footer --}}
        <div class="text-center py-4">
            <p class="text-xs text-zinc-400 dark:text-zinc-600">
                Toggle dark/light mode using the sun/moon button above
            </p>
        </div>

    </div>
</body>
</html>
