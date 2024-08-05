<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 20);
            $table->foreignUuid('brand_id');
            $table->foreignUuid('model_id');
            $table->foreignUuid('category_id');
            $table->string('licence_plate_number');
            $table->date('licence_expire_date');
            $table->string('vin_number');
            $table->string('transmission');
            $table->string('fuel_type');
            $table->string('ownership');
            $table->foreignUuid('driver_id');
            $table->json('documents')->nullable();
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('vehicles');
    }
}
