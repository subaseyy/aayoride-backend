<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('driver_id');
            $table->date('date');
            $table->time('online')->nullable();
            $table->time('offline')->nullable();
            $table->double('online_time', 23, 2)->default(0);
            $table->time('accepted')->nullable();
            $table->time('completed')->nullable();
            $table->time('start_driving')->nullable();
            $table->double('on_driving_time', 23, 2)->default(0);
            $table->double('idle_time', 23, 2)->default(0);
            $table->string('on_time_completed')->nullable()->default(0);
            $table->string('late_completed')->nullable()->default(0);
            $table->string('late_pickup')->nullable()->default(0);
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
        Schema::dropIfExists('driver_time_logs');
    }
}
