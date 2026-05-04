<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Support\Facades\App; // 1. Обов'язково додаємо цей імпорт!

class VehicleTypeController extends Controller
{
    public function index()
    {
        $locale = App::getLocale(); // Отримуємо поточну мову ('de', 'uk', 'en' або 'ru')
        $descColumn = 'description_' . $locale; // Формуємо назву: description_de

        // 2. Робимо запит
        $types = VehicleType::where('is_active', true)
            ->select(
                'id', 'name', 'slug', 'start_price', 'price_per_km',
                'image_path', 'max_weight_kg', 'volume_m3',
                // Вибираємо наші нові колонки з мовами замість старої 'description'
                'description_uk', 'description_de', 'description_en', 'description_ru'
            )
            ->orderBy('start_price')
            ->get();

        // 3. Перебираємо результати і залишаємо тільки ОДИН description для фронта
        $formattedTypes = $types->map(function ($vehicle) use ($descColumn) {
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'slug' => $vehicle->slug,
                'start_price' => $vehicle->start_price,
                'price_per_km' => $vehicle->price_per_km,
                'image_path' => $vehicle->image_path,
                'max_weight_kg' => $vehicle->max_weight_kg,
                'volume_m3' => $vehicle->volume_m3,

                // Магія: беремо потрібну мову. Якщо для цієї мови текст ще не заповнили,
                // підстраховуємося і віддаємо українську (або німецьку) версію
                'description' => $vehicle->$descColumn ?? $vehicle->description_uk ?? null,
            ];
        });

        // 4. Віддаємо відформатований JSON
        return response()->json($formattedTypes);
    }
}
