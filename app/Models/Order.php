<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Дозволяємо масове заповнення цих полів
    protected $fillable = [
        'user_id',
        'customer_name',
        'phone',
        'email',
        'vehicle_type_id', // Важливо: тут ID, а не назва
        'pickup_address',
        'delivery_address',
        'distance_km',
        'total_price',
        'scheduled_at',
        'status',
        'customer_note',
        'admin_note',
    ];

    // Вказуємо, що ці поля - це дати, щоб Laravel зручно з ними працював
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // --- ЗВ'ЯЗКИ (RELATIONSHIPS) ---

    // Замовлення належить конкретному типу авто
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    // Замовлення може належати юзеру
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}