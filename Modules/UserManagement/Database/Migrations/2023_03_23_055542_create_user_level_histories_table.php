<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLevelHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_level_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_level_id');
            $table->foreignUuid('user_id');
            $table->string('user_type');
            $table->integer('completed_ride')->default(0);
            $table->boolean('ride_reward_status')->default(false);
            $table->decimal('total_amount')->default(0);
            $table->boolean('amount_reward_status')->default(false);
            $table->decimal('cancellation_rate')->default(0);
            $table->boolean('cancellation_reward_status')->default(false);
            $table->integer('reviews')->default(0);
            $table->boolean('reviews_reward_status')->default(false);
            $table->boolean('is_level_reward_granted')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('user_level_histories');
    }
}
