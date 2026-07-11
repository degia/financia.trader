<?php

namespace App\Models;

use App\Enums\EventImpact;
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'event_date',
        'event_type',
        'impact',
        'pair',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => EventType::class,
            'impact' => EventImpact::class,
            'event_date' => 'datetime',
        ];
    }
}
