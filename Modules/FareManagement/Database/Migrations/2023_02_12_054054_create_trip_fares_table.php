<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripFaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_fares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('zone_id');
            $table->foreignUuid('vehicle_category_id');
            $table->decimal('base_fare');
            $table->decimal('base_fare_per_km');
            $table->decimal('waiting_fee_per_min');
            $table->decimal('cancellation_fee_percent');
            $table->decimal('min_cancellation_fee');
            $table->decimal('idle_fee_per_min');
            $table->decimal('trip_delay_fee_per_min');
            $table->decimal('penalty_fee_for_cancel');
            $table->decimal('fee_add_to_next');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_fares');
    }
}
