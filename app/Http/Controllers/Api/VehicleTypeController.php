<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;

class VehicleTypeController extends Controller
{
    public function index()
    {
        $types = VehicleType::where('is_active', true)
            ->select('id', 'name', 'slug', 'start_price', 'price_per_km', 'description', 'image_path', 'max_weight_kg', 'volume_m3')
            ->orderBy('start_price')
            ->get();

        return response()->json($types);
    }
}
