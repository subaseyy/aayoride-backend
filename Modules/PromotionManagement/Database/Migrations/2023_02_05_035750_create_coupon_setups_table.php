<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_setups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->foreignUuid('user_id')->nullable();
            $table->foreignUuid('user_level_id')->nullable();
            $table->decimal('min_trip_amount')->default(0);
            $table->decimal('max_coupon_amount')->default(0);
            $table->decimal('coupon')->default(0);
            $table->string('amount_type',15)->default('percentage');
            $table->string('coupon_type',15)->default('default');
            $table->string('coupon_code')->unique()->nullable();
            $table->integer('limit')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('rules')->nullable();
            $table->decimal('total_used')->default(0);
            $table->decimal('total_amount')->default(0);
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
        Schema::dropIfExists('coupon_setups');
    }
}
