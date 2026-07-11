<div>
    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)"
             x-show="show" x-transition
             class="fixed top-4 right-4 z-[60] glass-strong rounded-xl px-4 py-3 shadow-lg border border-emerald-200 dark:border-emerald-500/20">
            <div class="flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-emerald-500" />
                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)"
             x-show="show" x-transition
             class="fixed top-4 right-4 z-[60] glass-strong rounded-xl px-4 py-3 shadow-lg border border-red-200 dark:border-red-500/20">
            <div class="flex items-center gap-2">
                <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-500" />
                <span class="text-sm font-medium text-red-700 dark:text-red-400">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="page-title">Trade Journal</h1>
            <p class="page-subtitle">Track and manage your trades</p>
        </div>
        <button wire:click="openCreateModal" class="btn-primary">
            <x-icon name="plus" class="w-4 h-4" />
            <span class="hidden sm:inline">New Trade</span>
        </button>
    </div>

    {{-- Filters --}}
    <x-glass-card class="mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Pair --}}
            <div>
                <label class="label-text mb-1.5 block">Pair</label>
                <select wire:model.live="filterPair" wire:change="applyFilters" class="input-field">
                    <option value="">All Pairs</option>
                    @foreach($availablePairs as $pair)
                        <option value="{{ $pair }}">{{ $pair }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label class="label-text mb-1.5 block">Status</label>
                <select wire:model.live="filterStatus" wire:change="applyFilters" class="input-field">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            {{-- Strategy --}}
            <div>
                <label class="label-text mb-1.5 block">Strategy</label>
                <select wire:model.live="filterStrategy" wire:change="applyFilters" class="input-field">
                    <option value="">All Strategies</option>
                    @foreach($availableStrategies as $strategy)
                        <option value="{{ $strategy['id'] }}">{{ $strategy['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <label class="label-text mb-1.5 block">From</label>
                <input type="date" wire:model.live="filterDateFrom" wire:change="applyFilters" class="input-field" />
            </div>

            {{-- Date To --}}
            <div>
                <label class="label-text mb-1.5 block">To</label>
                <div class="flex gap-2">
                    <input type="date" wire:model.live="filterDateTo" wire:change="applyFilters" class="input-field" />
                    <button wire:click="resetFilters" class="btn-ghost shrink-0 px-3" title="Reset filters">
                        <x-icon name="arrow-path" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </x-glass-card>

    {{-- ═══════════════════════════════════
         Desktop Table
         ═══════════════════════════════════ --}}
    <x-glass-card class="mb-6 hidden md:block !p-0 overflow-hidden">
        @if($this->trades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200/50 dark:border-zinc-700/50">
                            <th class="px-5 py-3 text-left label-text">Pair</th>
                            <th class="px-5 py-3 text-left label-text">Type</th>
                            <th class="px-5 py-3 text-left label-text">Direction</th>
                            <th class="px-5 py-3 text-right label-text">Entry</th>
                            <th class="px-5 py-3 text-right label-text">Exit</th>
                            <th class="px-5 py-3 text-right label-text">Size</th>
                            <th class="px-5 py-3 text-right label-text">P&L</th>
                            <th class="px-5 py-3 text-right label-text">Status</th>
                            <th class="px-5 py-3 text-right label-text">Date</th>
                            <th class="px-5 py-3 text-right label-text">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->trades as $trade)
                            <tr class="border-b border-zinc-100/50 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-3">
                                    <a href="{{ route('trades.show', $trade->id) }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:underline">
                                        {{ $trade->pair }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-zinc-500 dark:text-zinc-400 text-xs">{{ $trade->trade_type->label() }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium {{ $trade->direction === 'long' ? 'stat-profit' : 'stat-loss' }}">
                                        <x-icon :name="$trade->direction === 'long' ? 'arrow-up-right' : 'arrow-down-right'" class="w-3 h-3" />
                                        {{ ucfirst($trade->direction->value) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-mono text-xs text-zinc-600 dark:text-zinc-300">{{ $trade->entry_price }}</td>
                                <td class="px-5 py-3 text-right font-mono text-xs text-zinc-600 dark:text-zinc-300">{{ $trade->exit_price ?? '—' }}</td>
                                <td class="px-5 py-3 text-right font-mono text-xs text-zinc-600 dark:text-zinc-300">{{ $trade->size }}</td>
                                <td class="px-5 py-3 text-right font-mono text-xs font-medium {{ $trade->pnl_amount >= 0 ? 'stat-profit' : 'stat-loss' }}">
                                    {{ $trade->pnl_amount != 0 ? ($trade->pnl_amount > 0 ? '+' : '') . '$' . number_format($trade->pnl_amount, 2) : '—' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider
                                        {{ match($trade->outcome->value) {
                                            'win' => 'bg-emerald-400/10 text-emerald-600 dark:text-emerald-400',
                                            'loss' => 'bg-red-400/10 text-red-600 dark:text-red-400',
                                            'breakeven' => 'bg-zinc-400/10 text-zinc-600 dark:text-zinc-400',
                                            default => 'bg-zinc-400/10 text-zinc-600 dark:text-zinc-400',
                                        } }}">
                                        {{ $trade->outcome->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right text-xs text-zinc-400 dark:text-zinc-500">
                                    {{ $trade->entry_date->format('d M y') }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if($trade->outcome->value === 'open')
                                            <button wire:click="openCloseModal({{ $trade->id }})"
                                                    class="p-1.5 rounded-lg text-emerald-500 hover:bg-emerald-500/10 transition-colors"
                                                    title="Close trade">
                                                <x-icon name="check-circle" class="w-4 h-4" />
                                            </button>
                                        @endif
                                        <a href="{{ route('trades.show', $trade->id) }}"
                                           class="p-1.5 rounded-lg text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                                           title="View details">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <button wire:click="openEditModal({{ $trade->id }})"
                                                class="p-1.5 rounded-lg text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                                                title="Edit">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button wire:click="confirmDelete({{ $trade->id }})"
                                                class="p-1.5 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors"
                                                title="Delete">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16">
                <x-icon name="arrow-trending-up" class="w-14 h-14 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">No trades found</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">
                    @if($filterPair || $filterStatus || $filterStrategy || $filterDateFrom || $filterDateTo)
                        No trades match your filters. Try adjusting or resetting them.
                    @else
                        Start by adding your first trade.
                    @endif
                </p>
                @if(!$filterPair && !$filterStatus && !$filterStrategy && !$filterDateFrom && !$filterDateTo)
                    <button wire:click="openCreateModal" class="btn-primary inline-flex">
                        <x-icon name="plus" class="w-4 h-4" />
                        Add Trade
                    </button>
                @endif
            </div>
        @endif

        @if($this->trades->hasPages())
            <div class="px-5 py-3 border-t border-zinc-200/50 dark:border-zinc-700/50">
                {{ $this->trades->links() }}
            </div>
        @endif
    </x-glass-card>

    {{-- ═══════════════════════════════════
         Mobile Cards
         ═══════════════════════════════════ --}}
    <div class="space-y-3 md:hidden mb-6">
        @if($this->trades->count() > 0)
            @foreach($this->trades as $trade)
                <div class="glass-card !p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <a href="{{ route('trades.show', $trade->id) }}" class="font-semibold text-zinc-900 dark:text-zinc-100 hover:underline">
                                {{ $trade->pair }}
                            </a>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-zinc-400 dark:text-zinc-600">{{ $trade->trade_type->label() }}</span>
                                <span class="text-zinc-300 dark:text-zinc-700">·</span>
                                <span class="text-xs {{ $trade->direction === 'long' ? 'stat-profit' : 'stat-loss' }}">{{ ucfirst($trade->direction->value) }}</span>
                            </div>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider
                            {{ match($trade->outcome->value) {
                                'win' => 'bg-emerald-400/10 text-emerald-600 dark:text-emerald-400',
                                'loss' => 'bg-red-400/10 text-red-600 dark:text-red-400',
                                'breakeven' => 'bg-zinc-400/10 text-zinc-600 dark:text-zinc-400',
                                default => 'bg-zinc-400/10 text-zinc-600 dark:text-zinc-400',
                            } }}">
                            {{ $trade->outcome->label() }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-xs mb-3">
                        <div>
                            <span class="label-text">Entry</span>
                            <p class="font-mono text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $trade->entry_price }}</p>
                        </div>
                        <div>
                            <span class="label-text">Exit</span>
                            <p class="font-mono text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $trade->exit_price ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="label-text">Size</span>
                            <p class="font-mono text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $trade->size }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-zinc-200/50 dark:border-zinc-700/50">
                        <span class="font-mono text-sm font-medium {{ $trade->pnl_amount >= 0 ? 'stat-profit' : 'stat-loss' }}">
                            {{ $trade->pnl_amount != 0 ? ($trade->pnl_amount > 0 ? '+' : '') . '$' . number_format($trade->pnl_amount, 2) : '—' }}
                        </span>
                        <div class="flex items-center gap-1">
                            @if($trade->outcome->value === 'open')
                                <button wire:click="openCloseModal({{ $trade->id }})" class="p-2 rounded-lg text-emerald-500 hover:bg-emerald-500/10 transition-colors">
                                    <x-icon name="check-circle" class="w-4 h-4" />
                                </button>
                            @endif
                            <button wire:click="openEditModal({{ $trade->id }})" class="p-2 rounded-lg text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                <x-icon name="pencil-square" class="w-4 h-4" />
                            </button>
                            <button wire:click="confirmDelete({{ $trade->id }})" class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($this->trades->hasPages())
                <div class="pt-2">
                    {{ $this->trades->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <x-icon name="arrow-trending-up" class="w-14 h-14 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">No trades found</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">
                    @if($filterPair || $filterStatus || $filterStrategy || $filterDateFrom || $filterDateTo)
                        No trades match your filters.
                    @else
                        Start by adding your first trade.
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════
         Create / Edit Modal
         ═══════════════════════════════════ --}}
    @if($showModal)
        <div class="fixed inset-0 z-[55] flex items-center justify-center p-4"
             x-data
             @keydown.escape.window="{{ $isEditing ? 'Livewire.dispatch(\'closeModal\')' : 'Livewire.dispatch(\'closeModal\')' }}">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Modal --}}
            <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto glass-strong rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-700/50"
                 x-data @click.outside="Livewire.dispatch('closeModal')">
                <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 border-b border-zinc-200/50 dark:border-zinc-700/50 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-t-2xl">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $isEditing ? 'Edit Trade' : 'New Trade' }}
                    </h2>
                    <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="{{ $isEditing ? 'update' : 'store' }}" class="p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Trade Type --}}
                        <div>
                            <label class="label-text mb-1.5 block">Type <span class="text-red-400">*</span></label>
                            <select wire:model.live="tradeType" class="input-field">
                                @foreach(\App\Enums\TradeType::cases() as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pair --}}
                        <div>
                            <label class="label-text mb-1.5 block">Pair / Asset <span class="text-red-400">*</span></label>
                            <select wire:model="pair" class="input-field">
                                <option value="">Select pair...</option>
                                @foreach($this->pairsByType as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('pair') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Direction --}}
                        <div>
                            <label class="label-text mb-1.5 block">Direction <span class="text-red-400">*</span></label>
                            <select wire:model.live="direction" class="input-field">
                                @foreach(\App\Enums\TradeDirection::cases() as $dir)
                                    <option value="{{ $dir->value }}">{{ $dir->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Size / Lot --}}
                        <div>
                            <label class="label-text mb-1.5 block">Size / Lot <span class="text-red-400">*</span></label>
                            <input type="number" wire:model.live="size" class="input-field" placeholder="0.10" step="0.01" min="0" />
                            @error('size') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Entry Price --}}
                        <div>
                            <label class="label-text mb-1.5 block">Entry Price <span class="text-red-400">*</span></label>
                            <input type="number" wire:model.live="entryPrice" class="input-field" placeholder="1.08450" step="any" min="0" />
                            @error('entryPrice') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Exit Price --}}
                        <div>
                            <label class="label-text mb-1.5 block">Exit Price</label>
                            <input type="number" wire:model.live="exitPrice" class="input-field" placeholder="1.08920" step="any" min="0" />
                            @error('exitPrice') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Entry Date --}}
                        <div>
                            <label class="label-text mb-1.5 block">Entry Date <span class="text-red-400">*</span></label>
                            <input type="datetime-local" wire:model="entryDate" class="input-field" />
                            @error('entryDate') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Exit Date --}}
                        <div>
                            <label class="label-text mb-1.5 block">Exit Date</label>
                            <input type="datetime-local" wire:model="exitDate" class="input-field" />
                            @error('exitDate') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Stop Loss --}}
                        <div>
                            <label class="label-text mb-1.5 block">Stop Loss</label>
                            <input type="number" wire:model="stopLoss" class="input-field" placeholder="1.08200" step="any" min="0" />
                        </div>

                        {{-- Take Profit --}}
                        <div>
                            <label class="label-text mb-1.5 block">Take Profit</label>
                            <input type="number" wire:model="takeProfit" class="input-field" placeholder="1.09000" step="any" min="0" />
                        </div>

                        {{-- Outcome --}}
                        <div>
                            <label class="label-text mb-1.5 block">Outcome <span class="text-red-400">*</span></label>
                            <select wire:model="outcome" class="input-field" disabled>
                                @foreach(\App\Enums\TradeOutcome::cases() as $out)
                                    <option value="{{ $out->value }}">{{ $out->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fees --}}
                        <div>
                            <label class="label-text mb-1.5 block">Fees</label>
                            <input type="number" wire:model.live="fees" class="input-field" placeholder="0.00" step="any" min="0" />
                        </div>

                        {{-- P&L Amount --}}
                        <div>
                            <label class="label-text mb-1.5 block">P&L ($)</label>
                            <input type="number" wire:model="pnlAmount" class="input-field bg-zinc-50 dark:bg-zinc-800/50" placeholder="0.00" step="any" readonly />
                        </div>

                        {{-- P&L Pips --}}
                        <div>
                            <label class="label-text mb-1.5 block">P&L (pips)</label>
                            <input type="number" wire:model="pnlPips" class="input-field bg-zinc-50 dark:bg-zinc-800/50" placeholder="0" step="any" readonly />
                        </div>

                        {{-- Strategy --}}
                        <div class="sm:col-span-2">
                            <label class="label-text mb-1.5 block">Strategy</label>
                            <select wire:model="strategyId" class="input-field">
                                <option value="">— None —</option>
                                @foreach($availableStrategies as $strategy)
                                    <option value="{{ $strategy['id'] }}">{{ $strategy['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Notes --}}
                        <div class="sm:col-span-2">
                            <label class="label-text mb-1.5 block">Notes</label>
                            <textarea wire:model="notes" class="input-field" rows="3" placeholder="Trade notes, setup details, etc."></textarea>
                        </div>

                        {{-- Screenshot --}}
                        <div class="sm:col-span-2">
                            <label class="label-text mb-1.5 block">Screenshot</label>
                            <div class="flex items-center gap-3">
                                <label class="btn-ghost cursor-pointer">
                                    <x-icon name="photo" class="w-4 h-4" />
                                    <span>Choose file</span>
                                    <input type="file" wire:model="screenshot" accept="image/*" class="hidden" />
                                </label>
                                @if($screenshot)
                                    <span class="text-xs text-zinc-500">{{ $screenshot->getClientOriginalName() }}</span>
                                @endif
                            </div>
                            <div wire:loading wire:target="screenshot" class="text-xs text-zinc-400 mt-1">Uploading...</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200/50 dark:border-zinc-700/50">
                        <button type="button" wire:click="closeModal" class="btn-ghost">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <x-icon name="{{ $isEditing ? 'pencil-square' : 'plus' }}" class="w-4 h-4" />
                            {{ $isEditing ? 'Update Trade' : 'Save Trade' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════
         Close Trade Modal
         ═══════════════════════════════════ --}}
    @if($showCloseModal)
        <div class="fixed inset-0 z-[55] flex items-center justify-center p-4" x-data @keydown.escape.window="Livewire.dispatch('cancelClose')">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelClose"></div>
            <div class="relative w-full max-w-md glass-strong rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-700/50">
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200/50 dark:border-zinc-700/50">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Close Trade</h2>
                    <button wire:click="cancelClose" class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>
                <form wire:submit="closeTrade" class="p-6 space-y-4">
                    <div>
                        <label class="label-text mb-1.5 block">Exit Price <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="closeExitPrice" class="input-field" placeholder="1.08920" step="any" min="0" />
                        @error('closeExitPrice') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label-text mb-1.5 block">P&L ($)</label>
                            <input type="number" wire:model="closePnlAmount" class="input-field" placeholder="Auto-calc if empty" step="any" />
                        </div>
                        <div>
                            <label class="label-text mb-1.5 block">P&L (pips)</label>
                            <input type="number" wire:model="closePnlPips" class="input-field" placeholder="Optional" step="any" />
                        </div>
                    </div>

                    <div>
                        <label class="label-text mb-1.5 block">Outcome <span class="text-red-400">*</span></label>
                        <select wire:model="closeOutcome" class="input-field">
                            <option value="">Select...</option>
                            <option value="win">Win</option>
                            <option value="loss">Loss</option>
                            <option value="breakeven">Breakeven</option>
                        </select>
                        @error('closeOutcome') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label-text mb-1.5 block">Notes</label>
                        <textarea wire:model="closeNotes" class="input-field" rows="2" placeholder="Optional close notes"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" wire:click="cancelClose" class="btn-ghost">Cancel</button>
                        <button type="submit" class="btn-profit">
                            <x-icon name="check-circle" class="w-4 h-4" />
                            Close Trade
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════
         Delete Confirmation Modal
         ═══════════════════════════════════ --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[55] flex items-center justify-center p-4" x-data @keydown.escape.window="Livewire.dispatch('cancelDelete')">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelDelete"></div>
            <div class="relative w-full max-w-sm glass-strong rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-700/50 p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-500" />
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Delete Trade?</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">
                    This action cannot be undone. The trade record and any attached screenshot will be permanently removed.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="cancelDelete" class="btn-ghost">Cancel</button>
                    <button wire:click="deleteTrade" class="btn-danger">
                        <x-icon name="trash" class="w-4 h-4" />
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
