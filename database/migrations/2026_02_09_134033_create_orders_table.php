<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 1. Клієнт
            // Якщо замовлення робить зареєстрований юзер - запишемо ID. Якщо гість - буде null.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name'); // Ім'я (дублюємо, якщо це гість)
            $table->string('phone');         // Телефон (формат +49...)
            $table->string('email')->nullable();

            // 2. Що замовили (Зв'язок з нашою таблицею машин)
            // Це створить колонку vehicle_type_id
            $table->foreignId('vehicle_type_id')->constrained('vehicle_type');

            // 3. Маршрут (Google Maps дані)
            $table->string('pickup_address');   // Звідки
            $table->string('delivery_address'); // Куди
            $table->decimal('distance_km', 8, 2)->nullable(); // Дистанція (наприклад 450.50 км)
            
            // 4. Гроші та Час
            $table->decimal('total_price', 10, 2)->nullable(); // Підсумкова ціна
            $table->dateTime('scheduled_at')->nullable();      // Дата і час переїзду
            
            // 5. Статус CRM
            // new = нове, processing = в роботі, done = виконано, canceled = скасовано
            $table->string('status')->default('new'); 
            
            // 6. Коментарі
            $table->text('customer_note')->nullable(); // Що написав клієнт
            $table->text('admin_note')->nullable();    // Примітки менеджера (не видно клієнту)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};