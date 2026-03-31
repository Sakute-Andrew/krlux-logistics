<?php

// app/Models/Order.php  — оновлена версія з новими зв'язками

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',       // НОВЕ
        'promo_code_id',   // НОВЕ
        'customer_name',
        'phone',
        'email',
        'vehicle_type_id',
        'pickup_address',
        'delivery_address',
        'distance_km',
        'total_price',
        'discount_amount', // НОВЕ
        'scheduled_at',
        'status',
        'customer_note',
        'admin_note',
        'tracking_token'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // --- ЗВ'ЯЗКИ ---

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }
}
