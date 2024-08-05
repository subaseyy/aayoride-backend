<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelUserInfomationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_user_infomations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('trip_request_id');
            $table->string('contact_number', 20);
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('user_type'); //between sender and receiver
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
        Schema::dropIfExists('parcel_user_infomations');
    }
}
