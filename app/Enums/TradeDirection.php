<?php

namespace App\Enums;

enum TradeDirection: string
{
    case LONG = 'long';
    case SHORT = 'short';

    public function label(): string
    {
        return match ($this) {
            self::LONG => 'Long / Buy',
            self::SHORT => 'Short / Sell',
        };
    }
}
