<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id');
            $table->date('date');
            $table->integer('total_online')->default(0);
            $table->integer('total_offline')->default(0);
            $table->integer('total_idle')->default(0);
            $table->integer('total_driving')->default(0);
            $table->time('last_ride_started_at')->nullable();
            $table->time('last_ride_completed_at')->nullable();
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
        Schema::dropIfExists('time_tracks');
    }
}
