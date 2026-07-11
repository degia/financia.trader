<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;

class SettingsPage extends Component
{
    public function render()
    {
        return view('livewire.settings.settings-page')
            ->layout('layouts.app', ['title' => 'Settings - ' . config('app.name')]);
    }
}
