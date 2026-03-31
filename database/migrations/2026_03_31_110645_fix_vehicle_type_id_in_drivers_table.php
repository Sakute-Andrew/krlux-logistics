<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['vehicle_type_id']);
            $table->foreign('vehicle_type_id')
                ->references('id')
                ->on('vehicle_type')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        //
    }
};
