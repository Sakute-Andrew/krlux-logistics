<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\VehicleType;
use Illuminate\Http\Request;

use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'nullable|email|max:255',
            'vehicle_type_id'  => 'required|exists:vehicle_type,id',
            'pickup_address'   => 'required|string',
            'delivery_address' => 'required|string',
            'distance_km'      => 'nullable|numeric|min:0',
            //'scheduled_at'     => 'nullable|date',
            'customer_note'    => 'nullable|string',
        ]);

        // Рахуємо ціну на бекенді (не довіряємо фронту)
        $vehicleType = VehicleType::findOrFail($validated['vehicle_type_id']);
        $distance    = $validated['distance_km'] ?? 0;
        $totalPrice  = $vehicleType->start_price + ($distance * $vehicleType->price_per_km);

        $order = Order::create([
            ...$validated,
            'tracking_token' => Str::uuid(),
            'total_price'    => round($totalPrice, 2),
            'status'         => 'pending',
        ]);

        Log::info('Order created: ' . $order->id . ', email: ' . $order->email);

        if ($order->email) {
            Log::info('Sending email...');
            Mail::to($order->email)->send(new OrderCreated($order));
            Log::info('Email sent!');
        }

        return response()->json([
            'message'     => 'Замовлення створено',
            'order_id'    => $order->id,
            'total_price' => $order->total_price,
        ], 201);
    }
}
