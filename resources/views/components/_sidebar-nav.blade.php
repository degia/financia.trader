@php
    $navItems = [
        ['route' => 'dashboard', 'icon' => 'squares-2x2', 'label' => 'Dashboard'],
        ['route' => 'trades.index', 'icon' => 'arrow-trending-up', 'label' => 'Trade Journal'],
        ['route' => 'portfolio', 'icon' => 'wallet', 'label' => 'Portfolio'],
        ['route' => 'analytics', 'icon' => 'chart-bar', 'label' => 'Analytics'],
        ['route' => 'calendar', 'icon' => 'calendar-days', 'label' => 'Calendar'],
        ['route' => 'settings', 'icon' => 'cog-6-tooth', 'label' => 'Settings'],
    ];
@endphp

@foreach($navItems as $item)
    <a href="{{ route($item['route']) }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 mb-0.5
              {{ request()->routeIs($item['route'])
                  ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900'
                  : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
        <x-icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
        <span>{{ $item['label'] }}</span>
    </a>
@endforeach
