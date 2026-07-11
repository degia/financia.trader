<?php

namespace App\Models;

use App\Enums\TradeDirection;
use App\Enums\TradeOutcome;
use App\Enums\TradeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    protected $fillable = [
        'portfolio_id',
        'strategy_id',
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

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function strategyRef(): BelongsTo
    {
        return $this->belongsTo(Strategy::class, 'strategy_id');
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

    public function scopeOfPortfolio($query, $portfolioId)
    {
        return $query->where('portfolio_id', $portfolioId);
    }

    public function getRiskRewardRatioAttribute(): ?float
    {
        if (!$this->stop_loss || !$this->take_profit || $this->entry_price == 0) {
            return null;
        }

        $risk = abs($this->entry_price - $this->stop_loss);
        $reward = abs($this->take_profit - $this->entry_price);

        if ($risk == 0) return null;

        return round($reward / $risk, 2);
    }
}
