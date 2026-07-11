<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Strategy extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function getWinRateAttribute(): float
    {
        $closed = $this->trades()->where('outcome', '!=', 'open')->count();
        if ($closed === 0) return 0;

        $wins = $this->trades()->where('outcome', 'win')->count();
        return round(($wins / $closed) * 100, 1);
    }

    public function getTotalPnlAttribute(): float
    {
        return (float) $this->trades()->where('outcome', '!=', 'open')->sum('pnl_amount');
    }
}
