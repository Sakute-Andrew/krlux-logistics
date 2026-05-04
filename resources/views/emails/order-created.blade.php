<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background: #f4f4f4; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 32px; border-radius: 8px;">

    {{-- Динамічний заголовок залежно від типу послуги --}}
    <h1 style="color: #1A1A1A;">
        {{ $order->service_type === 'garbage' ? __('emails.created_title_garbage') : __('emails.created_title') }}
    </h1>

    <p>{{ __('emails.greeting') }}, <strong>{{ $order->customer_name }}</strong>!
        {{ $order->service_type === 'garbage' ? __('emails.garbage_intro_text') : __('emails.created_msg') }}
    </p>

    <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin: 20px 0;">
        <p><strong>{{ __('emails.order_title') }} #{{ $order->id }}</strong></p>

        {{-- Адреса звідки (завжди є) --}}
        <p>📍 {{ $order->service_type === 'garbage' ? __('emails.pickup_address') : __('emails.from') }}: {{ $order->pickup_address }}</p>

        {{-- Адреса куди (тільки для транспортування) --}}
        @if($order->service_type !== 'garbage')
            <p>📍 {{ __('emails.to') }}: {{ $order->delivery_address }}</p>
        @endif

        <p>🚛 {{ __('emails.transport') }}: {{ $order->vehicleType->name }}</p>
        <p>💶 {{ __('emails.cost') }}: €{{ number_format($order->total_price, 2) }}</p>
    </div>

    <a href="{{ url('/order/track/' . $order->tracking_token) }}"
       style="display:inline-block; background:#A68966; color:white; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:bold;">
        {{ __('emails.track_order') }}
    </a>

    <p style="color: #999; font-size: 12px; margin-top: 24px;">
        {{ __('emails.save_link_track') }}
    </p>

    <p>{{ __('emails.regards') }},<br><strong>KrLux Logistics München</strong></p>
</div>
</body>
</html>
