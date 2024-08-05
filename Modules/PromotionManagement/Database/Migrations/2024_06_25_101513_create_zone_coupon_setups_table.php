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
        Schema::create('zone_coupon_setups', function (Blueprint $table) {
            $table->foreignUuid('zone_id');
            $table->foreignUuid('coupon_setup_id');
            $table->primary(['zone_id', 'coupon_setup_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_coupon_setups');
    }
};
