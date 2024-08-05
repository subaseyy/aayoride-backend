<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFareBiddingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fare_biddings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_request_id');
            $table->foreignUuid('driver_id');
            $table->foreignUuid('customer_id');
            $table->decimal('bid_fare');
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
        Schema::dropIfExists('fare_biddings');
    }
}
