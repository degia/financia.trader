@props([
    'label',
    'value',
    'icon' => null,
    'trend' => null,
    'trendValue' => null,
])

@php
    $trendClass = match($trend) {
        'up' => 'stat-profit',
        'down' => 'stat-loss',
        default => 'stat-neutral',
    };
@endphp

<div class="glass-card flex flex-col gap-3">
    <div class="flex items-center justify-between">
        <span class="label-text">{{ $label }}</span>
        @if($icon)
            <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <x-icon :name="$icon" class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
            </div>
        @endif
    </div>
    <div class="flex items-end gap-2">
        <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $value }}</span>
        @if($trendValue)
            <span class="text-xs font-medium {{ $trendClass }} mb-1">{{ $trendValue }}</span>
        @endif
    </div>
    {{ $slot }}
</div>
