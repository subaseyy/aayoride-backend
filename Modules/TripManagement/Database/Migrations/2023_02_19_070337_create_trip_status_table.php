<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_status', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('trip_request_id');
            $table->foreignUuid('customer_id');
            $table->foreignUuid('driver_id')->nullable();
            $table->timestamp('pending')->nullable();
            $table->timestamp('accepted')->nullable();
            $table->timestamp('out_for_pickup')->nullable();
            $table->timestamp('picked_up')->nullable();
            $table->timestamp('ongoing')->nullable();
            $table->timestamp('completed')->nullable();
            $table->timestamp('cancelled')->nullable();
            $table->timestamp('failed')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('trip_status');
    }
}
