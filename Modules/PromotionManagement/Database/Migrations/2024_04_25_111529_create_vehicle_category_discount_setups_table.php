<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_category_discount_setups', function (Blueprint $table) {
            $table->foreignUuid('vehicle_category_id');
            $table->foreignUuid('discount_setup_id');
            $table->primary(['vehicle_category_id', 'discount_setup_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_category_discount_setups');
    }
};
