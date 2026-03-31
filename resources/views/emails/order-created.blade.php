<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background: #f4f4f4; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 32px; border-radius: 8px;">

    <h1 style="color: #1A1A1A;">Ваше замовлення прийнято!</h1>

    <p>Дякуємо, <strong>{{ $order->customer_name }}</strong>! Ми отримали ваше замовлення.</p>

    <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin: 20px 0;">
        <p><strong>Замовлення #{{ $order->id }}</strong></p>
        <p>📍 Звідки: {{ $order->pickup_address }}</p>
        <p>📍 Куди: {{ $order->delivery_address }}</p>
        <p>🚛 Транспорт: {{ $order->vehicleType->name }}</p>
        <p>💶 Вартість: €{{ number_format($order->total_price, 2) }}</p>
    </div>

    <a href="{{ url('/order/track/' . $order->tracking_token) }}"
       style="display:inline-block; background:#A68966; color:white; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:bold;">
        Відстежити замовлення
    </a>

    <p style="color: #999; font-size: 12px; margin-top: 24px;">
        Збережіть це посилання — воно дає доступ до статусу без паролю.
    </p>

    <p>З повагою,<br><strong>KrLux Logistics München</strong></p>
</div>
</body>
</html>
