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
        Schema::create('customer_coupon_setups', function (Blueprint $table) {
            $table->foreignUuid('user_id');
            $table->foreignUuid('coupon_setup_id');
            $table->integer('limit_per_user')->default(0);
            $table->primary(['user_id', 'coupon_setup_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_coupon_setups');
    }
};
