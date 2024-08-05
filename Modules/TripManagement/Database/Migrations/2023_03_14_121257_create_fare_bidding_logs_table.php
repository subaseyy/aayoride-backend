<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFareBiddingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fare_bidding_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_request_id')->nullable();
            $table->foreignUuid('driver_id')->nullable();
            $table->foreignUuid('customer_id')->nullable();
            $table->decimal('bid_fare')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fare_bidding_logs');
    }
}
