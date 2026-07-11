@props(['title' => null, 'subtitle' => null])

<div class="glass-card" {{ $attributes->merge(['class' => '']) }}>
    @if($title || $subtitle)
        <div class="mb-4">
            @if($title)
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
