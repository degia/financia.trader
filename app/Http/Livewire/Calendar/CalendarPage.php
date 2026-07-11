<?php

namespace App\Http\Livewire\Calendar;

use Livewire\Component;

class CalendarPage extends Component
{
    public function render()
    {
        return view('livewire.calendar.calendar-page')
            ->layout('layouts.app', ['title' => 'Calendar - ' . config('app.name')]);
    }
}
