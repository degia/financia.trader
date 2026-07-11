<div>
    <div class="mb-6">
        <h1 class="page-title">Settings</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Configure your preferences</p>
    </div>

    {{-- Flash message --}}
    @if (session()->has('saved'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="mb-4 px-4 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ session('saved') }}
        </div>
    @endif

    <div class="max-w-2xl space-y-4">

        {{-- Profile --}}
        <x-glass-card title="Profile">
            <form wire:submit="saveProfile" class="space-y-4">
                <div>
                    <label class="label-text">Display Name</label>
                    <input type="text" wire:model="userName" class="input-field mt-1" placeholder="Your name" />
                    @error('userName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveProfile">Save Profile</span>
                        <span wire:loading wire:target="saveProfile" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" class="opacity-75"/></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </x-glass-card>

        {{-- Appearance --}}
        <x-glass-card title="Appearance">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Dark Mode</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Toggle between dark and light theme</p>
                </div>
                <x-theme-toggle />
            </div>
        </x-glass-card>

        {{-- Trading Preferences --}}
        <x-glass-card title="Trading Preferences">
            <div class="space-y-4">
                <form wire:submit="saveCurrency" class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="label-text">Default Currency</label>
                        <select wire:model="currency" class="input-field mt-1">
                            <option value="USD">USD — US Dollar</option>
                            <option value="EUR">EUR — Euro</option>
                            <option value="GBP">GBP — British Pound</option>
                            <option value="JPY">JPY — Japanese Yen</option>
                            <option value="AUD">AUD — Australian Dollar</option>
                            <option value="CAD">CAD — Canadian Dollar</option>
                            <option value="CHF">CHF — Swiss Franc</option>
                            <option value="IDR">IDR — Indonesian Rupiah</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary shrink-0" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveCurrency">Save</span>
                        <span wire:loading wire:target="saveCurrency">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" class="opacity-75"/></svg>
                        </span>
                    </button>
                </form>

                <form wire:submit="saveBalance" class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="label-text">Initial Balance</label>
                        <div class="relative mt-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400 dark:text-zinc-500">$</span>
                            <input type="number" step="0.01" min="0" wire:model="initialBalance" class="input-field pl-7" />
                        </div>
                        @error('initialBalance') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-primary shrink-0" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveBalance">Save</span>
                        <span wire:loading wire:target="saveBalance">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" class="opacity-75"/></svg>
                        </span>
                    </button>
                </form>
            </div>
        </x-glass-card>

        {{-- Danger Zone --}}
        <x-glass-card title="Danger Zone" subtitle="This action cannot be undone">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Reset All Data</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Delete all trades, portfolios, strategies, and settings</p>
                </div>
                <button wire:click="openResetModal" class="btn-danger">
                    <x-icon name="trash" class="w-4 h-4" />
                    Reset
                </button>
            </div>
        </x-glass-card>
    </div>

    {{-- Reset Modal Step 1: Warning --}}
    @if($showResetModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4"
             x-data
             x-on:keydown.escape.window="$wire.closeResetModal()">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeResetModal"></div>
            <div class="relative glass-card w-full max-w-md z-10 border border-zinc-200 dark:border-zinc-700"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                @if(!$showResetConfirm)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center shrink-0">
                            <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Reset All Data</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">This is irreversible</p>
                        </div>
                    </div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
                        All your trades, portfolios, strategies, calendar events, and settings will be <strong class="text-red-500">permanently deleted</strong>. A fresh portfolio with $10,000 will be created.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button wire:click="closeResetModal" class="btn-ghost">Cancel</button>
                        <button wire:click="proceedToConfirm" class="btn-danger">Continue</button>
                    </div>
                @else
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center shrink-0">
                            <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Final Confirmation</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Type DELETE to confirm</p>
                        </div>
                    </div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                        Type <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-semibold text-red-500">DELETE</code> below to permanently erase all data.
                    </p>
                    <input type="text" wire:model="confirmText" class="input-field mb-4 font-mono" placeholder="Type DELETE" autocomplete="off" />
                    <div class="flex justify-end gap-3">
                        <button wire:click="closeResetModal" class="btn-ghost">Cancel</button>
                        <button wire:click="resetAllData"
                                class="btn-danger {{ $confirmText !== 'DELETE' ? 'opacity-50 pointer-events-none' : '' }}"
                                wire:loading.attr="disabled" wire:loading.class="opacity-50">
                            <span wire:loading.remove wire:target="resetAllData">Delete Everything</span>
                            <span wire:loading wire:target="resetAllData" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" class="opacity-75"/></svg>
                                Deleting...
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:saved', (e) => {
            const msg = e.detail?.message || 'Saved!';
            Livewire.dispatch('notify', { message: msg });
        });
    </script>
    @endpush
</div>
