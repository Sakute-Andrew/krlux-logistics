<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicle_type', function (Blueprint $table) {
            // Додаємо колонки для всіх мов.
            // Робимо їх nullable(), щоб не вилізла помилка для старих записів
            $table->text('description_uk')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();
            $table->text('description_ru')->nullable();
        });
    }

    public function down()
    {
        Schema::table('vehicle_type', function (Blueprint $table) {
            // Відкат міграції (якщо щось піде не так)
            $table->dropColumn([
                'description_uk',
                'description_en',
                'description_de',
                'description_ru'
            ]);
        });
    }
};
