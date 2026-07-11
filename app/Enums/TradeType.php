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

    public function pairs(): array
    {
        return match ($this) {
            self::FOREX => [
                'EUR/USD', 'GBP/USD', 'USD/JPY', 'USD/CHF', 'AUD/USD',
                'NZD/USD', 'USD/CAD', 'EUR/GBP', 'EUR/JPY', 'GBP/JPY',
                'AUD/JPY', 'EUR/AUD', 'GBP/AUD', 'EUR/CAD', 'GBP/CAD',
                'AUD/CAD', 'AUD/NZD', 'NZD/JPY', 'CHF/JPY', 'EUR/CHF',
            ],
            self::CRYPTO => [
                'BTC/USDT', 'ETH/USDT', 'SOL/USDT', 'BNB/USDT', 'XRP/USDT',
                'ADA/USDT', 'DOGE/USDT', 'AVAX/USDT', 'DOT/USDT', 'LINK/USDT',
                'MATIC/USDT', 'UNI/USDT', 'LTC/USDT', 'ATOM/USDT', 'FIL/USDT',
            ],
            self::STOCK => [
                'AAPL', 'MSFT', 'GOOGL', 'AMZN', 'META',
                'TSLA', 'NVDA', 'AMD', 'NFLX', 'BA',
                'JPM', 'V', 'DIS', 'PYPL', 'SQ',
                'COIN', 'PLTR', 'SNAP', 'UBER', 'SHOP',
            ],
            self::FUTURES => [
                'ES', 'NQ', 'YM', 'RTY', 'NKD',
                'CL', 'NG', 'GC', 'SI', 'HG',
                'ZB', 'ZN', 'ZC', 'ZS', 'ZW',
            ],
            self::COMMODITY => [
                'XAU/USD', 'XAG/USD', 'USOIL', 'UKOIL', 'NGAS',
                'COPPER', 'PLATINUM', 'PALLADIUM', 'WHEAT', 'CORN',
                'SOYBEAN', 'SUGAR', 'COFFEE', 'COTTON', 'COCOA',
            ],
        };
    }
}
