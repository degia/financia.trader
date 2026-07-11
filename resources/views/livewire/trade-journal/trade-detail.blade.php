<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="page-title">Trade Detail</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $trade->pair }} — {{ $trade->direction->label() }}</p>
        </div>
        <a href="{{ route('trades.index') }}" class="btn-ghost">
            <x-icon name="chevron-left" class="w-4 h-4" />
            Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-glass-card title="Trade Info">
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="label-text">Pair</span>
                    <span class="font-medium">{{ $trade->pair }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Type</span>
                    <span class="font-medium">{{ $trade->trade_type->label() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Direction</span>
                    <span class="font-medium">{{ $trade->direction->label() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Entry Price</span>
                    <span class="font-medium">{{ $trade->entry_price }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Exit Price</span>
                    <span class="font-medium">{{ $trade->exit_price ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Size</span>
                    <span class="font-medium">{{ $trade->size }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">P&L</span>
                    <span class="font-medium {{ $trade->pnl_amount >= 0 ? 'stat-profit' : 'stat-loss' }}">
                        {{ $trade->pnl_amount >= 0 ? '+' : '' }}${{ number_format($trade->pnl_amount, 2) }}
                    </span>
                </div>
            </div>
        </x-glass-card>

        <x-glass-card title="Notes & Strategy">
            <div class="space-y-3">
                @if($trade->strategy)
                    <div>
                        <span class="label-text">Strategy</span>
                        <p class="text-sm font-medium mt-1">{{ $trade->strategy }}</p>
                    </div>
                @endif
                @if($trade->notes)
                    <div>
                        <span class="label-text">Notes</span>
                        <p class="text-sm mt-1">{{ $trade->notes }}</p>
                    </div>
                @endif
                @if(!$trade->strategy && !$trade->notes)
                    <p class="text-sm text-zinc-400">No notes or strategy recorded.</p>
                @endif
            </div>
        </x-glass-card>
    </div>
</div>
