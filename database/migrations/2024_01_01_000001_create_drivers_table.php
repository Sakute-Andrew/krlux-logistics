<?php

// database/migrations/xxxx_xx_xx_create_drivers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_type')->nullOnDelete();
            $table->enum('status', ['available', 'busy', 'unavailable'])->default('available');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Додаємо driver_id до orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Driver::class);
            $table->dropColumn('driver_id');
        });

        Schema::dropIfExists('drivers');
    }
};
