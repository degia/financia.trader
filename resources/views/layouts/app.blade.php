<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800|jetbrains-mono:400,500" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ sidebarOpen: false }" class="min-h-screen">

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
    @stack('scripts')
</body>
</html>
