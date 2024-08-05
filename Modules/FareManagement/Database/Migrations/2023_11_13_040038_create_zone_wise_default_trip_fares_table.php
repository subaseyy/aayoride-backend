<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoneWiseDefaultTripFaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_wise_default_trip_fares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('zone_id');
            $table->double('base_fare');
            $table->double('base_fare_per_km');
            $table->double('waiting_fee_per_min');
            $table->double('cancellation_fee_percent');
            $table->double('min_cancellation_fee');
            $table->double('idle_fee_per_min');
            $table->double('trip_delay_fee_per_min');
            $table->double('penalty_fee_for_cancel');
            $table->double('fee_add_to_next');
            $table->integer('category_wise_different_fare');
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
        Schema::dropIfExists('zone_wise_default_trip_fares');
    }
}
