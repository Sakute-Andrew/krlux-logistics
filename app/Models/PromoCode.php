<?php

// app/Models/PromoCode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_price',
        'usage_limit',
        'usage_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Чи можна використати промокод
    public function isUsable(): bool
    {
        if (! $this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;

        return true;
    }

    // Розрахунок знижки для суми
    public function calculateDiscount(float $price): float
    {
        return $this->type === 'percent'
            ? round($price * $this->value / 100, 2)
            : min($this->value, $price); // фіксована не може бути більше суми
    }
}
