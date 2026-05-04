<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background: #f4f4f4; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 32px; border-radius: 8px;">

    <h1 style="color: #1A1A1A;">{{ __('emails.confirmed_title') }} 🎉</h1>

    <p>{{ __('emails.hello') }}, <strong>{{ $order->customer_name }}</strong>! {{ __('emails.confirmed_msg') }}</p>

    <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin: 20px 0;">
        <p><strong>{{ __('emails.order') }} #{{ $order->id }}</strong></p>
        <p>📍 {{ __('emails.from') }}: {{ $order->pickup_address }}</p>
        <p>📍 {{ __('emails.to') }}: {{ $order->delivery_address }}</p>
        <p>🚛 {{ __('emails.transport') }}: {{ $order->vehicleType->name }}</p>
        <p>💶 {{ __('emails.cost') }}: €{{ number_format($order->total_price, 2) }}</p>
    </div>

    @if($order->driver)
        <div style="background: #fff3e0; padding: 16px; border-radius: 8px; margin: 20px 0;">
            <p><strong>{{ __('emails.driver') }}</strong></p>
            <p>👤 {{ $order->driver->name }}</p>
            <p>📞 <a href="tel:{{ $order->driver->phone }}">{{ $order->driver->phone }}</a></p>
        </div>
    @endif

    <a href="{{ url('/order/track/' . $order->tracking_token) }}"
       style="display:inline-block; background:#A68966; color:white; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:bold;">
        {{ __('emails.track_order') }}
    </a>

    <p>{{ __('emails.regards') }},<br><strong>KrLux Logistics München</strong></p>
</div>
</body>
</html>
