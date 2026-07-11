@php
    $navItems = [
        ['route' => 'dashboard', 'icon' => 'squares-2x2', 'label' => 'Home'],
        ['route' => 'trades.index', 'icon' => 'arrow-trending-up', 'label' => 'Trades'],
        ['route' => 'portfolio', 'icon' => 'wallet', 'label' => 'Portfolio'],
        ['route' => 'analytics', 'icon' => 'chart-bar', 'label' => 'Analytics'],
        ['route' => 'calendar', 'icon' => 'calendar-days', 'label' => 'Calendar'],
    ];
@endphp

<nav class="fixed bottom-0 inset-x-0 z-50 lg:hidden glass-strong border-t border-zinc-200/50 dark:border-zinc-800/50 safe-bottom">
    <div class="flex items-center justify-around py-2 px-2">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all duration-150 active:scale-95
                      {{ request()->routeIs($item['route'])
                          ? 'text-zinc-900 dark:text-zinc-100'
                          : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300' }}">
                <x-icon :name="$item['icon']" class="w-5 h-5" />
                <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
