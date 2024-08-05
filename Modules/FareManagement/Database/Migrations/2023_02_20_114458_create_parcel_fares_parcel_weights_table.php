<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelFaresParcelWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_fares_parcel_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('parcel_fare_id');
            $table->foreignUuid('parcel_weight_id');
            $table->foreignUuid('parcel_category_id');
            $table->decimal('fare');
            $table->foreignUuid('zone_id');
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
        Schema::dropIfExists('parcel_fares_parcel_weights');
    }
}
