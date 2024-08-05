<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponSetupVehicleCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_setup_vehicle_category', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('coupon_setup_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('vehicle_category_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('coupon_setup_vehicle_category');
    }
}
