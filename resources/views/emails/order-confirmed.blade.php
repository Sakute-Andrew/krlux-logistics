<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background: #f4f4f4; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 32px; border-radius: 8px;">

    <h1 style="color: #1A1A1A;">Замовлення підтверджено! 🎉</h1>

    <p>Вітаємо, <strong>{{ $order->customer_name }}</strong>! Ваше замовлення підтверджено.</p>

    <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin: 20px 0;">
        <p><strong>Замовлення #{{ $order->id }}</strong></p>
        <p>📍 Звідки: {{ $order->pickup_address }}</p>
        <p>📍 Куди: {{ $order->delivery_address }}</p>
        <p>🚛 Транспорт: {{ $order->vehicleType->name }}</p>
        <p>💶 Вартість: €{{ number_format($order->total_price, 2) }}</p>
    </div>

    @if($order->driver)
        <div style="background: #fff3e0; padding: 16px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Ваш водій</strong></p>
            <p>👤 {{ $order->driver->name }}</p>
            <p>📞 <a href="tel:{{ $order->driver->phone }}">{{ $order->driver->phone }}</a></p>
        </div>
    @endif

    <a href="{{ url('/order/track/' . $order->tracking_token) }}"
       style="display:inline-block; background:#A68966; color:white; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:bold;">
        Відстежити замовлення
    </a>

    <p>З повагою,<br><strong>KrLux Logistics München</strong></p>
</div>
</body>
</html>
