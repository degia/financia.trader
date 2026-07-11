<?php

namespace App\Enums;

enum TradeOutcome: string
{
    case WIN = 'win';
    case LOSS = 'loss';
    case BREAKEVEN = 'breakeven';
    case OPEN = 'open';

    public function label(): string
    {
        return match ($this) {
            self::WIN => 'Win',
            self::LOSS => 'Loss',
            self::BREAKEVEN => 'Breakeven',
            self::OPEN => 'Open',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::WIN => 'text-emerald-400',
            self::LOSS => 'text-red-400',
            self::BREAKEVEN => 'text-zinc-400',
            self::OPEN => 'text-blue-400',
        };
    }
}
