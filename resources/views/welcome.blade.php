<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRLux Logistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Додамо твій фірмовий фон */
        body { background-color: #F2F0EB; }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased min-h-screen flex flex-col">

    {{-- <x-header /> --}}
    <div class="bg-white p-4 text-center border-b">
        Тут буде Хедер (Header)
    </div>

    {{-- <x-hero-section /> --}}
    <main class="flex-grow">
        <div class="py-20 text-center">
            <h1 class="text-4xl font-bold">Тут буде Hero Section</h1>
        </div>
        
        <section class="py-12 px-4">
            <div class="container mx-auto">
                <h2 class="text-3xl font-bold mb-8 text-center">Наш Автопарк</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach(\App\Models\VehicleType::where('is_active', true)->get() as $vehicle)
                        <div class="bg-white p-4 rounded-lg shadow">
                            <img src="{{ asset('storage/' . $vehicle->image_path) }}" class="w-full h-48 object-cover rounded mb-4">
                            <h3 class="font-bold text-xl">{{ $vehicle->name }}</h3>
                            <p class="text-blue-600 font-bold">{{ $vehicle->price_per_km }} €/км</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    {{-- <x-footer /> --}}
    <div class="bg-gray-800 text-white p-4 text-center mt-auto">
        Тут буде Футер (Footer)
    </div>

</body>
</html>