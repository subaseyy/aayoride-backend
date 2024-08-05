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
        Schema::table('zone_wise_default_trip_fares', function (Blueprint $table) {
            $table->float('pickup_bonus_amount')->default(0)->after('fee_add_to_next');
            $table->float('minimum_pickup_distance')->default(0)->after('pickup_bonus_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_bonus_amount', 'minimum_pickup_distance'
            ]);
        });
    }
};
