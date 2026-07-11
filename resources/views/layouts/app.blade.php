<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ sidebarOpen: false, mobileMenu: false }" class="min-h-screen">

    <div class="flex min-h-screen">

        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 glass-sidebar z-40">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-zinc-200/50 dark:border-zinc-800/50">
                <div class="w-8 h-8 rounded-lg bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center">
                    <x-icon name="chart-candlestick" class="w-4 h-4 text-white dark:text-zinc-900" />
                </div>
                <span class="font-bold text-lg tracking-tight">{{ config('app.name') }}</span>
            </div>

            <nav class="flex-1 overflow-y-auto scrollbar-thin py-4 px-3">
                @include('components._sidebar-nav')
            </nav>

            <div class="px-3 py-4 border-t border-zinc-200/50 dark:border-zinc-800/50">
                @include('components._sidebar-footer')
            </div>
        </aside>

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden" @click="sidebarOpen = false"></div>

        {{-- Mobile Sidebar --}}
        <aside x-show="sidebarOpen" x-transition:enter="transition-transform duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-72 glass-sidebar z-50 lg:hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-200/50 dark:border-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center">
                        <x-icon name="chart-candlestick" class="w-4 h-4 text-white dark:text-zinc-900" />
                    </div>
                    <span class="font-bold text-lg tracking-tight">{{ config('app.name') }}</span>
                </div>
                <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto scrollbar-thin py-4 px-3">
                @include('components._sidebar-nav')
            </nav>

            <div class="px-3 py-4 border-t border-zinc-200/50 dark:border-zinc-800/50">
                @include('components._sidebar-footer')
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 lg:ml-64 min-h-screen flex flex-col">

            {{-- Top Bar (mobile) --}}
            <header class="sticky top-0 z-30 glass-strong lg:hidden">
                <div class="flex items-center justify-between px-4 py-3">
                    <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <x-icon name="bars-3" class="w-5 h-5" />
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center">
                            <x-icon name="chart-candlestick" class="w-3 h-3 text-white dark:text-zinc-900" />
                        </div>
                        <span class="font-bold text-sm">{{ config('app.name') }}</span>
                    </div>
                    <x-theme-toggle />
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <nav class="fixed bottom-0 inset-x-0 z-50 lg:hidden glass-strong border-t border-zinc-200/50 dark:border-zinc-800/50 safe-bottom">
        <div class="flex items-center justify-around py-2 px-2">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => 'squares-2x2', 'label' => 'Dashboard'],
                    ['route' => 'trades.index', 'icon' => 'arrow-trending-up', 'label' => 'Trades'],
                    ['route' => 'portfolio', 'icon' => 'wallet', 'label' => 'Portfolio'],
                    ['route' => 'analytics', 'icon' => 'chart-bar', 'label' => 'Analytics'],
                    ['route' => 'calendar', 'icon' => 'calendar-days', 'label' => 'Calendar'],
                ];
            @endphp
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs($item['route']) ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500' }}">
                    <x-icon :name="$item['icon']" class="w-5 h-5" />
                    <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @livewireScripts
</body>
</html>
