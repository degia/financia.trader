<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="page-title">{{ $trade->pair }}</h1>
            <p class="page-subtitle">
                {{ $trade->trade_type->label() }} · {{ $trade->direction->label() }} ·
                {{ $trade->entry_date->format('d M Y H:i') }}
            </p>
        </div>
        <a href="{{ route('trades.index') }}" class="btn-ghost">
            <x-icon name="chevron-left" class="w-4 h-4" />
            <span class="hidden sm:inline">Back</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Trade Info --}}
        <x-glass-card title="Trade Info">
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="label-text">Pair</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $trade->pair }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Type</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $trade->trade_type->label() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Direction</span>
                    <span class="font-medium {{ $trade->direction === 'long' ? 'stat-profit' : 'stat-loss' }}">
                        {{ $trade->direction->label() }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Size / Lot</span>
                    <span class="font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ $trade->size }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Entry Price</span>
                    <span class="font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ $trade->entry_price }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Exit Price</span>
                    <span class="font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ $trade->exit_price ?? '—' }}</span>
                </div>
                @if($trade->stop_loss)
                    <div class="flex justify-between text-sm">
                        <span class="label-text">Stop Loss</span>
                        <span class="font-mono font-medium stat-loss">{{ $trade->stop_loss }}</span>
                    </div>
                @endif
                @if($trade->take_profit)
                    <div class="flex justify-between text-sm">
                        <span class="label-text">Take Profit</span>
                        <span class="font-mono font-medium stat-profit">{{ $trade->take_profit }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="label-text">Fees</span>
                    <span class="font-mono text-zinc-500 dark:text-zinc-400">${{ number_format($trade->fees, 2) }}</span>
                </div>
                <div class="pt-2 border-t border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex justify-between text-sm">
                        <span class="label-text">P&L</span>
                        <span class="font-mono font-bold text-base {{ $trade->pnl_amount >= 0 ? 'stat-profit' : 'stat-loss' }}">
                            {{ $trade->pnl_amount != 0 ? ($trade->pnl_amount > 0 ? '+' : '') . '$' . number_format($trade->pnl_amount, 2) : '—' }}
                        </span>
                    </div>
                    @if($trade->pnl_pips !== null)
                        <div class="flex justify-between text-sm mt-1">
                            <span class="label-text">P&L (pips)</span>
                            <span class="font-mono text-sm {{ $trade->pnl_pips >= 0 ? 'stat-profit' : 'stat-loss' }}">
                                {{ $trade->pnl_pips > 0 ? '+' : '' }}{{ $trade->pnl_pips }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="flex justify-between text-sm">
                    <span class="label-text">Status</span>
                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider
                        {{ match($trade->outcome->value) {
                            'win' => 'bg-emerald-400/10 text-emerald-600 dark:text-emerald-400',
                            'loss' => 'bg-red-400/10 text-red-600 dark:text-red-400',
                            'breakeven' => 'bg-zinc-400/10 text-zinc-600 dark:text-zinc-400',
                            default => 'bg-blue-400/10 text-blue-600 dark:text-blue-400',
                        } }}">
                        {{ $trade->outcome->label() }}
                    </span>
                </div>
                @if($trade->risk_reward_ratio)
                    <div class="flex justify-between text-sm">
                        <span class="label-text">Risk:Reward</span>
                        <span class="font-mono font-medium text-zinc-900 dark:text-zinc-100">1:{{ $trade->risk_reward_ratio }}</span>
                    </div>
                @endif
            </div>
        </x-glass-card>

        {{-- Notes, Strategy & Screenshot --}}
        <div class="space-y-4">
            <x-glass-card title="Notes & Strategy">
                <div class="space-y-3">
                    @if($trade->strategyRef)
                        <div>
                            <span class="label-text">Strategy</span>
                            <p class="text-sm font-medium mt-1 text-zinc-900 dark:text-zinc-100">{{ $trade->strategyRef->name }}</p>
                            @if($trade->strategyRef->description)
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $trade->strategyRef->description }}</p>
                            @endif
                        </div>
                    @elseif($trade->strategy)
                        <div>
                            <span class="label-text">Strategy</span>
                            <p class="text-sm font-medium mt-1 text-zinc-900 dark:text-zinc-100">{{ $trade->strategy }}</p>
                        </div>
                    @endif
                    @if($trade->notes)
                        <div>
                            <span class="label-text">Notes</span>
                            <p class="text-sm mt-1 text-zinc-700 dark:text-zinc-300 whitespace-pre-line">{{ $trade->notes }}</p>
                        </div>
                    @endif
                    @if(!$trade->strategy && !$trade->strategyRef && !$trade->notes)
                        <p class="text-sm text-zinc-400">No notes or strategy recorded.</p>
                    @endif
                </div>
            </x-glass-card>

            @if($trade->screenshot_path)
                <x-glass-card title="Screenshot">
                    <div class="rounded-xl overflow-hidden border border-zinc-200/50 dark:border-zinc-700/50">
                        <img src="{{ Storage::disk('public')->url($trade->screenshot_path) }}"
                             alt="Trade screenshot — {{ $trade->pair }}"
                             class="w-full h-auto" />
                    </div>
                </x-glass-card>
            @endif

            @if($trade->portfolio)
                <x-glass-card title="Portfolio">
                    <div class="flex justify-between text-sm">
                        <span class="label-text">Account</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $trade->portfolio->name }}</span>
                    </div>
                </x-glass-card>
            @endif
        </div>
    </div>
</div>
