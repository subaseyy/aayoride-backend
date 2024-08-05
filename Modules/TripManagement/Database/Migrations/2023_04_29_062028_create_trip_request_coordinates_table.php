<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripRequestCoordinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_request_coordinates', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('trip_request_id');
            $table->point('pickup_coordinates')->nullable();
            $table->string('pickup_address')->nullable();
            $table->point('destination_coordinates')->nullable();
            $table->boolean('is_reached_destination')->default(false);
            $table->string('destination_address')->nullable();
            $table->text('intermediate_coordinates')->nullable();
            $table->point('int_coordinate_1')->nullable();
            $table->boolean('is_reached_1')->default(false);
            $table->point('int_coordinate_2')->nullable();
            $table->boolean('is_reached_2')->default(false);
            $table->text('intermediate_addresses')->nullable();
            $table->point('start_coordinates')->nullable();
            $table->point('drop_coordinates')->nullable();
            $table->point('driver_accept_coordinates')->nullable();
            $table->point('customer_request_coordinates')->nullable();
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
        Schema::dropIfExists('trip_request_coordinates');
    }
}
