<?php

// database/migrations/xxxx_xx_xx_create_promo_codes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                        // Сам код, напр. MUNICH10
            $table->enum('type', ['percent', 'fixed']);              // % або фіксована сума
            $table->decimal('value', 8, 2);                         // 10 = 10% або 10 = 10€
            $table->decimal('min_order_price', 8, 2)->nullable();   // Мінімальна сума замовлення
            $table->integer('usage_limit')->nullable();              // null = необмежено
            $table->integer('usage_count')->default(0);              // Лічильник використань
            $table->timestamp('expires_at')->nullable();             // null = не закінчується
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Додаємо promo_code_id до orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('promo_code_id')->nullable()->after('driver_id')->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 8, 2)->nullable()->after('total_price'); // скільки знижено
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\PromoCode::class);
            $table->dropColumn(['promo_code_id', 'discount_amount']);
        });

        Schema::dropIfExists('promo_codes');
    }
};
