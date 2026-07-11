<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <script>
        (function() {
            var t = localStorage.getItem('theme');
            if (t === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800|jetbrains-mono:400,500" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .lw-progress{position:fixed;top:0;left:0;height:2px;background:currentColor;z-index:9999;transition:width .3s ease,opacity .3s;opacity:0}
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="min-h-screen">

    <div id="lw-progress" class="lw-progress bg-zinc-900 dark:bg-zinc-100"></div>

    <div class="flex min-h-screen">

        <x-sidebar variant="desktop" />
        <x-sidebar variant="mobile" />

        {{-- Main Content --}}
        <div class="flex-1 lg:ml-64 min-h-screen flex flex-col">

            <x-topbar />

            {{-- Page Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-bottom-nav />

    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            const bar = document.getElementById('lw-progress');
            Livewire.hook('request.start', () => {
                bar.style.opacity = '1';
                bar.style.width = '40%';
            });
            Livewire.hook('request.commit', () => {
                bar.style.width = '80%';
            });
            Livewire.hook('request.finish', () => {
                bar.style.width = '100%';
                setTimeout(() => { bar.style.opacity = '0'; bar.style.width = '0%'; }, 200);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
