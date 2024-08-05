<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripRequestTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_request_times', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('trip_request_id');
            $table->float('estimated_time', 10, 4);
            $table->float('actual_time')->nullable();
            $table->float('waiting_time')->nullable();
            $table->float('delay_time')->nullable(); //fee for reaching destination more than estimated time
            $table->timestamp('idle_timestamp')->nullable();
            $table->float('idle_time')->nullable();
            $table->float('driver_arrival_time')->nullable();
            $table->timestamp('driver_arrival_timestamp')->nullable();
            $table->timestamp('driver_arrives_at')->nullable();
            $table->timestamp('customer_arrives_at')->nullable();
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
        Schema::dropIfExists('trip_request_times');
    }
}
