<?php

namespace App\Enums;

enum TradeType: string
{
    case FOREX = 'forex';
    case CRYPTO = 'crypto';
    case STOCK = 'stock';
    case FUTURES = 'futures';
    case COMMODITY = 'commodity';

    public function label(): string
    {
        return match ($this) {
            self::FOREX => 'Forex',
            self::CRYPTO => 'Crypto',
            self::STOCK => 'Stock',
            self::FUTURES => 'Futures',
            self::COMMODITY => 'Commodity',
        };
    }
}
