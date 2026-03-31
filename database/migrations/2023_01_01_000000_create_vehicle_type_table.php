<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_type', function (Blueprint $table) {
            $table->id();

            // Основна інформація
            $table->string('name'); // Наприклад: "Макси", "Спринтер"
            $table->string('slug')->unique(); // Для URL (наприклад: 'maxi-van')
            $table->text('description')->nullable(); // Текст: "Подходит для небольших переездов..."
            $table->string('image_path')->nullable(); // Шлях до картинки авто

            // Габарити (з картинки)
            // Використовуємо decimal для точності (4,2 означає всього 4 цифри, 2 після коми)
            $table->decimal('length_m', 4, 2)->nullable()->comment('Довжина завантаження, м'); // 4.2
            $table->decimal('width_m', 4, 2)->nullable()->comment('Ширина завантаження, м');  // 2.1
            $table->decimal('height_m', 4, 2)->nullable()->comment('Висота завантаження, м'); // 2.1
            $table->decimal('volume_m3', 5, 2)->nullable()->comment('Об\'єм, куб.м');        // 18
            
            // Вантажопідйомність (краще в кг і цілим числом)
            $table->unsignedInteger('max_weight_kg')->nullable()->comment('Макс вага, кг'); // 1000

            // Ціна (важливо для розрахунку!)
            $table->decimal('price_per_km', 8, 2)->default(0); // Ціна за кілометр
            $table->decimal('start_price', 8, 2)->default(0);  // Ціна подачі авто

            $table->boolean('is_active')->default(true); // Щоб можна було тимчасово вимкнути авто
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_type');
    }
};