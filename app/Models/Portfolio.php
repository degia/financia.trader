<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portfolio extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'initial_balance',
        'current_balance',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'initial_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function balanceHistory(): HasMany
    {
        return $this->hasMany(BalanceHistory::class);
    }

    public function openTrades()
    {
        return $this->trades()->where('outcome', 'open');
    }

    public function closedTrades()
    {
        return $this->trades()->where('outcome', '!=', 'open');
    }

    public function getWinRateAttribute(): float
    {
        $closed = $this->closedTrades()->count();
        if ($closed === 0) return 0;

        $wins = $this->closedTrades()->where('outcome', 'win')->count();
        return round(($wins / $closed) * 100, 1);
    }

    public function getTotalPnlAttribute(): float
    {
        return (float) $this->closedTrades()->sum('pnl_amount');
    }
}
