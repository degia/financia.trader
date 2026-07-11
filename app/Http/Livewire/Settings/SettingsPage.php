<?php

namespace App\Http\Livewire\Settings;

use App\Models\Setting;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Trade;
use App\Models\BalanceHistory;
use App\Models\CalendarEvent;
use App\Models\Strategy;
use Livewire\Component;

class SettingsPage extends Component
{
    public string $userName = '';
    public string $currency = 'USD';
    public float $initialBalance = 10000;

    public bool $showResetModal = false;
    public bool $showResetConfirm = false;
    public string $confirmText = '';

    protected array $rules = [
        'userName' => 'required|string|max:255',
        'currency' => 'required|string|max:3',
        'initialBalance' => 'required|numeric|min:0',
    ];

    public function mount(): void
    {
        $user = User::first();
        $this->userName = $user?->name ?? 'Trader';
        $this->currency = Setting::get('currency', 'USD');
        $portfolio = Portfolio::where('is_active', true)->first();
        $this->initialBalance = $portfolio ? (float) $portfolio->initial_balance : 10000;
    }

    public function saveProfile(): void
    {
        $this->validate(['userName' => 'required|string|max:255']);
        $user = User::first();
        if ($user) {
            $user->update(['name' => $this->userName]);
        }
        $this->dispatch('saved', message: 'Profile updated.');
    }

    public function saveCurrency(): void
    {
        $this->validate(['currency' => 'required|string|max:3']);
        Setting::set('currency', strtoupper($this->currency));
        $portfolio = Portfolio::where('is_active', true)->first();
        if ($portfolio) {
            $portfolio->update(['currency' => strtoupper($this->currency)]);
        }
        $this->dispatch('saved', message: 'Currency updated.');
    }

    public function saveBalance(): void
    {
        $this->validate(['initialBalance' => 'required|numeric|min:0']);
        $portfolio = Portfolio::where('is_active', true)->first();
        if ($portfolio) {
            $portfolio->update([
                'initial_balance' => $this->initialBalance,
                'current_balance' => $this->initialBalance,
            ]);
            $portfolio->balanceHistory()->delete();
            $portfolio->balanceHistory()->create([
                'date' => now()->toDateString(),
                'balance' => $this->initialBalance,
            ]);
        }
        $this->dispatch('saved', message: 'Balance reset.');
    }

    public function openResetModal(): void
    {
        $this->showResetModal = true;
        $this->showResetConfirm = false;
        $this->confirmText = '';
    }

    public function closeResetModal(): void
    {
        $this->showResetModal = false;
        $this->showResetConfirm = false;
        $this->confirmText = '';
    }

    public function proceedToConfirm(): void
    {
        $this->showResetConfirm = true;
    }

    public function resetAllData(): void
    {
        if ($this->confirmText !== 'DELETE') {
            $this->dispatch('error', message: 'Type DELETE to confirm.');
            return;
        }

        Trade::query()->delete();
        BalanceHistory::query()->delete();
        CalendarEvent::query()->delete();
        Strategy::query()->delete();
        Portfolio::query()->delete();
        Setting::query()->delete();

        $portfolio = Portfolio::create([
            'user_id' => User::first()->id ?? 1,
            'name' => 'Main Portfolio',
            'initial_balance' => 10000,
            'current_balance' => 10000,
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $portfolio->balanceHistory()->create([
            'date' => now()->toDateString(),
            'balance' => 10000,
        ]);

        $this->currency = 'USD';
        $this->initialBalance = 10000;
        $this->closeResetModal();
        $this->dispatch('saved', message: 'All data has been reset.');
    }

    public function render()
    {
        return view('livewire.settings.settings-page')
            ->layout('layouts.app', ['title' => 'Settings - ' . config('app.name')]);
    }
}
