@props(['variant' => 'desktop'])

@if($variant === 'desktop')
    {{-- Desktop sidebar: fixed, always visible on lg+ --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 glass-sidebar z-40">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <div class="w-8 h-8 rounded-lg bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center shadow-sm">
                <x-icon name="chart-candlestick" class="w-4 h-4 text-white dark:text-zinc-900" />
            </div>
            <div>
                <span class="font-bold text-lg tracking-tight">{{ config('app.name') }}</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto scrollbar-thin py-4 px-3">
            @include('components._sidebar-nav')
        </nav>

        <div class="px-3 py-4 border-t border-zinc-200/50 dark:border-zinc-800/50">
            <div class="flex items-center justify-between px-3">
                <x-theme-toggle />
                <span class="text-[10px] text-zinc-400 dark:text-zinc-600 font-medium">v1.0</span>
            </div>
        </div>
    </aside>

@elseif($variant === 'mobile')
    {{-- Mobile sidebar: slide-in, controlled by Alpine --}}
    {{-- Overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
        @click="sidebarOpen = false"
    ></div>

    {{-- Drawer --}}
    <aside
        x-show="sidebarOpen"
        x-transition:enter="transition-transform duration-200 ease-out"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-150 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-72 glass-sidebar z-50 lg:hidden flex flex-col"
    >
        <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center shadow-sm">
                    <x-icon name="chart-candlestick" class="w-4 h-4 text-white dark:text-zinc-900" />
                </div>
                <span class="font-bold text-lg tracking-tight">{{ config('app.name') }}</span>
            </div>
            <button
                @click="sidebarOpen = false"
                class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
            >
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto scrollbar-thin py-4 px-3">
            @include('components._sidebar-nav')
        </nav>

        <div class="px-3 py-4 border-t border-zinc-200/50 dark:border-zinc-800/50">
            <div class="flex items-center justify-between px-3">
                <x-theme-toggle />
                <span class="text-[10px] text-zinc-400 dark:text-zinc-600 font-medium">v1.0</span>
            </div>
        </div>
    </aside>
@endif
