<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceHistory extends Model
{
    protected $table = 'balance_history';

    protected $fillable = [
        'portfolio_id',
        'date',
        'balance',
        'equity',
        'deposit',
        'withdrawal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'portfolio_id' => 'integer',
            'date' => 'date',
            'balance' => 'decimal:2',
            'equity' => 'decimal:2',
            'deposit' => 'decimal:2',
            'withdrawal' => 'decimal:2',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('date');
    }

    public function scopeForPortfolio($query, $portfolioId)
    {
        return $query->where('portfolio_id', $portfolioId);
    }
}
