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
        Schema::create('vehicle_category_coupon_setups', function (Blueprint $table) {
            $table->foreignUuid('vehicle_category_id');
            $table->foreignUuid('coupon_setup_id');
            $table->primary(['vehicle_category_id', 'coupon_setup_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_category_coupon_setups');
    }
};
