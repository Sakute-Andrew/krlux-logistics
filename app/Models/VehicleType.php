<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $table = 'vehicle_type';

    // Дозволяємо редагувати ці поля через адмінку
    protected $fillable = [
        'name',
        'slug',
        'image_path',
        'length_m',
        'width_m',
        'height_m',
        'volume_m3',
        'max_weight_kg',
        'price_per_km',
        'start_price',
        'is_active',
        'description_uk',
        'description_ru',
        'description_en',
        'description_de',
    ];

    // Вказуємо типи даних (щоб цифри були цифрами, а булеві - true/false)
    protected $casts = [
        'is_active' => 'boolean',
        'length_m' => 'decimal:2',
        'width_m' => 'decimal:2',
        'height_m' => 'decimal:2',
        'volume_m3' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'start_price' => 'decimal:2',
    ];

}
