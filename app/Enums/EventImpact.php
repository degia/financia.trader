<?php

namespace App\Enums;

enum EventImpact: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';

    public function label(): string
    {
        return match ($this) {
            self::HIGH => 'High',
            self::MEDIUM => 'Medium',
            self::LOW => 'Low',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HIGH => 'text-red-400 bg-red-400/10',
            self::MEDIUM => 'text-amber-400 bg-amber-400/10',
            self::LOW => 'text-zinc-400 bg-zinc-400/10',
        };
    }
}
