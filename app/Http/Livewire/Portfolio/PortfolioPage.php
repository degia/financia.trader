<?php

namespace App\Http\Livewire\Portfolio;

use Livewire\Component;

class PortfolioPage extends Component
{
    public function render()
    {
        return view('livewire.portfolio.portfolio-page')
            ->layout('layouts.app', ['title' => 'Portfolio - ' . config('app.name')]);
    }
}
