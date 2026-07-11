@props(['lines' => 3, 'avatar' => false])

<div class="glass-card animate-pulse">
    @if($avatar)
        <div class="flex items-center gap-3 mb-4">
            <div class="skeleton w-10 h-10 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="skeleton h-3.5 w-1/3"></div>
                <div class="skeleton h-2.5 w-1/2"></div>
            </div>
        </div>
    @endif
    <div class="space-y-3">
        @for($i = 0; $i < $lines; $i++)
            <div class="skeleton h-3 {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
        @endfor
    </div>
    {{ $slot }}
</div>
