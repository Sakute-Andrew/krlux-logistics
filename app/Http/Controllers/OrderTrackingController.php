<?php

// app/Http/Controllers/OrderTrackingController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function show(string $token)
    {
        $order = Order::with(['vehicleType', 'driver'])
            ->where('tracking_token', $token)
            ->firstOrFail();

        return view('order.track', compact('order'));
    }
}
