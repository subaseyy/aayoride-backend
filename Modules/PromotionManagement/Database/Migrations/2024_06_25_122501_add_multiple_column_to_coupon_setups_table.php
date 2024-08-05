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
        Schema::table('coupon_setups', function (Blueprint $table) {
            $table->string('zone_coupon_type')->after('description')->default(CUSTOM);
            $table->string('customer_level_coupon_type')->after('zone_coupon_type')->default(CUSTOM);
            $table->string('customer_coupon_type')->after('customer_level_coupon_type')->default(CUSTOM);
            $table->text('category_coupon_type')->after('customer_coupon_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_setups', function (Blueprint $table) {
            $table->dropColumn(['zone_coupon_type','customer_level_coupon_type','customer_coupon_type','category_coupon_type']);
        });
    }
};
