<div>
    <div class="mb-6">
        <h1 class="page-title">Settings</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Configure your preferences</p>
    </div>

    <div class="max-w-2xl space-y-4">
        <x-glass-card title="Appearance">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Dark Mode</p>
                        <p class="text-xs text-zinc-400">Toggle between dark and light theme</p>
                    </div>
                    <x-theme-toggle />
                </div>
            </div>
        </x-glass-card>

        <x-glass-card title="Trading Preferences">
            <div class="space-y-4">
                <div>
                    <label class="label-text">Default Currency</label>
                    <input type="text" value="USD" class="input-field mt-1" readonly />
                </div>
            </div>
        </x-glass-card>

        <x-glass-card title="Data">
            <div class="space-y-4">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage your trading data</p>
                <button class="btn-danger">
                    <x-icon name="trash" class="w-4 h-4" />
                    Reset All Data
                </button>
            </div>
        </x-glass-card>
    </div>
</div>
