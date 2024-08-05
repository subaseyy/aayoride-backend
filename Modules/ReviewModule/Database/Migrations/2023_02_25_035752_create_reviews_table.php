<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('trip_request_id')->nullable();
            $table->foreignUuid('given_by')->nullable();
            $table->foreignUuid('received_by')->nullable();
            $table->string('trip_type')->nullable();
            $table->integer('rating')->default(1);
            $table->text('feedback')->nullable();
            $table->string('images')->nullable();
            $table->boolean('is_saved')->default(false);
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
        Schema::dropIfExists('reviews');
    }
}
