<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('sequence');
            $table->string('name', 191);
            $table->string('reward_type', 20);
            $table->decimal('reward_amount')->nullable();
            $table->string('image', 191)->nullable();
            $table->integer('targeted_ride');
            $table->integer('targeted_ride_point');
            $table->double('targeted_amount');
            $table->integer('targeted_amount_point');
            $table->integer('targeted_cancel');
            $table->integer('targeted_cancel_point');
            $table->integer('targeted_review');
            $table->integer('targeted_review_point');
            $table->string('user_type', 20);
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('user_levels');
    }
}
