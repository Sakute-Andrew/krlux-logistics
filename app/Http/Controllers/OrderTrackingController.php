<?php

// app/Http/Controllers/OrderTrackingController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Додаємо цей фасад

class OrderTrackingController extends Controller
{
    public function show(string $token)
    {
        $order = Order::with(['vehicleType', 'driver'])
            ->where('tracking_token', $token)
            ->firstOrFail();

        // Встановлюємо мову додатка з бази даних замовлення
        // Тепер Blade автоматично використає правильні файли з lang/
        App::setLocale($order->locale);

        return view('order.track', compact('order'));
    }
}
