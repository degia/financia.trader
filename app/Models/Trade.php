<?php

namespace App\Models;

use App\Enums\TradeDirection;
use App\Enums\TradeOutcome;
use App\Enums\TradeType;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'pair',
        'trade_type',
        'direction',
        'entry_price',
        'exit_price',
        'entry_date',
        'exit_date',
        'size',
        'stop_loss',
        'take_profit',
        'pnl_amount',
        'pnl_pips',
        'outcome',
        'fees',
        'strategy',
        'notes',
        'screenshot_path',
    ];

    protected function casts(): array
    {
        return [
            'trade_type' => TradeType::class,
            'direction' => TradeDirection::class,
            'outcome' => TradeOutcome::class,
            'entry_date' => 'datetime',
            'exit_date' => 'datetime',
            'entry_price' => 'decimal:8',
            'exit_price' => 'decimal:8',
            'size' => 'decimal:8',
            'stop_loss' => 'decimal:8',
            'take_profit' => 'decimal:8',
            'pnl_amount' => 'decimal:2',
            'pnl_pips' => 'decimal:2',
            'fees' => 'decimal:2',
        ];
    }

    public function scopeClosed($query)
    {
        return $query->where('outcome', '!=', 'open');
    }

    public function scopeOpen($query)
    {
        return $query->where('outcome', 'open');
    }

    public function scopeWins($query)
    {
        return $query->where('outcome', 'win');
    }

    public function scopeLosses($query)
    {
        return $query->where('outcome', 'loss');
    }
}
