<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    protected $table = 'balance_history';

    protected $fillable = [
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
            'date' => 'date',
            'balance' => 'decimal:2',
            'equity' => 'decimal:2',
            'deposit' => 'decimal:2',
            'withdrawal' => 'decimal:2',
        ];
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('date');
    }
}
