<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Замовлення #{{ $order->id }} — KrLux Logistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F2F0EB] min-h-screen font-sans">

<div class="max-w-2xl mx-auto px-4 py-12">

    {{-- Логотип / назва --}}
    <div class="text-center mb-10">
        <h1 class="text-2xl font-bold text-[#1A1A1A]">KrLux Logistics</h1>
        <p class="text-[#1A1A1A]/50 text-sm mt-1">München</p>
    </div>

    {{-- Картка замовлення --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- Хедер з статусом --}}
        <div class="p-6 border-b border-[#F2F0EB]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#1A1A1A]/50">Замовлення</p>
                    <p class="text-2xl font-bold text-[#1A1A1A]">#{{ $order->id }}</p>
                </div>

                @php
                    $statusConfig = match($order->status) {
                        'pending'     => ['label' => 'Очікує підтвердження', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                        'in_progress' => ['label' => 'В дорозі',             'bg' => 'bg-blue-100',   'text' => 'text-blue-800'],
                        'completed'   => ['label' => 'Завершено',            'bg' => 'bg-green-100',  'text' => 'text-green-800'],
                        'cancelled'   => ['label' => 'Скасовано',            'bg' => 'bg-red-100',    'text' => 'text-red-800'],
                        default       => ['label' => $order->status,         'bg' => 'bg-gray-100',   'text' => 'text-gray-800'],
                    };
                @endphp

                <span class="px-4 py-2 rounded-full text-sm font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    {{ $statusConfig['label'] }}
                </span>
            </div>

            {{-- Прогрес бар --}}
            <div class="mt-6">
                <div class="flex items-center justify-between text-xs text-[#1A1A1A]/40 mb-2">
                    <span>Прийнято</span>
                    <span>Підтверджено</span>
                    <span>В дорозі</span>
                    <span>Завершено</span>
                </div>
                <div class="h-2 bg-[#F2F0EB] rounded-full overflow-hidden">
                    @php
                        $progress = match($order->status) {
                            'pending'     => '25%',
                            'in_progress' => '75%',
                            'completed'   => '100%',
                            'cancelled'   => '0%',
                            default       => '0%',
                        };
                    @endphp
                    <div class="h-full bg-[#A68966] rounded-full transition-all" style="width: {{ $progress }}"></div>
                </div>
            </div>
        </div>

        {{-- Деталі маршруту --}}
        <div class="p-6 border-b border-[#F2F0EB] space-y-4">
            <h2 class="text-sm font-semibold text-[#1A1A1A]/50 uppercase tracking-wide">Маршрут</h2>

            <div class="flex gap-4">
                <div class="flex flex-col items-center pt-1">
                    <div class="w-3 h-3 rounded-full bg-[#A8C5DA] border-2 border-white shadow"></div>
                    <div class="w-0.5 h-8 bg-[#E5E5E5] my-1"></div>
                    <div class="w-3 h-3 rounded-full bg-[#C9B8D8] border-2 border-white shadow"></div>
                </div>
                <div class="space-y-4 flex-1">
                    <div>
                        <p class="text-xs text-[#1A1A1A]/40">Звідки</p>
                        <p class="text-sm text-[#1A1A1A] font-medium">{{ $order->pickup_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#1A1A1A]/40">Куди</p>
                        <p class="text-sm text-[#1A1A1A] font-medium">{{ $order->delivery_address }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Деталі замовлення --}}
        <div class="p-6 border-b border-[#F2F0EB]">
            <h2 class="text-sm font-semibold text-[#1A1A1A]/50 uppercase tracking-wide mb-4">Деталі</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-[#1A1A1A]/40">Транспорт</p>
                    <p class="font-medium text-[#1A1A1A]">{{ $order->vehicleType->name }}</p>
                </div>
                @if($order->distance_km)
                    <div>
                        <p class="text-[#1A1A1A]/40">Відстань</p>
                        <p class="font-medium text-[#1A1A1A]">{{ $order->distance_km }} км</p>
                    </div>
                @endif
                <div>
                    <p class="text-[#1A1A1A]/40">Вартість</p>
                    <p class="font-medium text-[#1A1A1A]">€{{ number_format($order->total_price, 2) }}</p>
                </div>
                @if($order->scheduled_at)
                    <div>
                        <p class="text-[#1A1A1A]/40">Дата</p>
                        <p class="font-medium text-[#1A1A1A]">{{ $order->scheduled_at->format('d.m.Y H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Водій (якщо призначений) --}}
        @if($order->driver)
            <div class="p-6 border-b border-[#F2F0EB]">
                <h2 class="text-sm font-semibold text-[#1A1A1A]/50 uppercase tracking-wide mb-4">Ваш водій</h2>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-[#A68966]/20 flex items-center justify-center">
                    <span class="text-[#A68966] font-bold text-lg">
                        {{ mb_substr($order->driver->name, 0, 1) }}
                    </span>
                    </div>
                    <div>
                        <p class="font-medium text-[#1A1A1A]">{{ $order->driver->name }}</p>
                        <a href="tel:{{ $order->driver->phone }}"
                           class="text-[#A68966] text-sm hover:underline">
                            {{ $order->driver->phone }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Футер --}}
        <div class="p-6 text-center">
            <p class="text-xs text-[#1A1A1A]/40">
                Збережіть це посилання щоб відстежувати статус замовлення
            </p>
            <p class="text-xs text-[#1A1A1A]/40 mt-1">
                Питання? Телефонуйте нам або відповідайте на email з підтвердженням
            </p>
        </div>
    </div>
</div>

</body>
</html>
