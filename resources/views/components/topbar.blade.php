@props(['title' => null])

<header {{ $attributes->class(['sticky top-0 z-30 glass-strong border-b border-zinc-200/50 dark:border-zinc-800/50']) }}>
    <div class="flex items-center justify-between px-4 sm:px-6 py-3">
        {{-- Left: hamburger (mobile) / spacer (desktop) --}}
        <button
            @click="sidebarOpen = true"
            class="p-2 -ml-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer lg:hidden"
        >
            <x-icon name="bars-3" class="w-5 h-5" />
        </button>

        {{-- Center: page title or logo --}}
        <div class="flex items-center gap-2">
            @if($title)
                <h1 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h1>
            @else
                <div class="w-6 h-6 rounded-md bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center">
                    <x-icon name="chart-candlestick" class="w-3 h-3 text-white dark:text-zinc-900" />
                </div>
                <span class="font-bold text-sm">{{ config('app.name') }}</span>
            @endif
        </div>

        {{-- Right: actions --}}
        <div class="flex items-center gap-1">
            <x-theme-toggle />
        </div>
    </div>

    {{ $slot }}
</header>
