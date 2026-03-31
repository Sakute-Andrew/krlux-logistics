<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = VehicleType::where('is_active', true)->get();

        // Загортаємо в 'data', щоб React отримав response.data.data, як ти і писав
        return response()->json([
            'data' => $cars
        ]);
    }

    // Створення замовлення від клієнта
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'client_name' => 'required|string',
            'phone' => 'required|string',
        ]);

        $order = \App\Models\Order::create($validated);

        return response()->json(['message' => 'Order created!', 'order' => $order], 201);
    }
}
