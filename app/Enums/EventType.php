<?php

namespace App\Enums;

enum EventType: string
{
    case NEWS = 'news';
    case FOMC = 'fomc';
    case NFP = 'nfp';
    case EARNINGS = 'earnings';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::NEWS => 'News',
            self::FOMC => 'FOMC',
            self::NFP => 'NFP',
            self::EARNINGS => 'Earnings',
            self::CUSTOM => 'Custom',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NEWS => 'newspaper',
            self::FOMC => 'building-2',
            self::NFP => 'users',
            self::EARNINGS => 'chart-bar',
            self::CUSTOM => 'bookmark',
        };
    }
}
