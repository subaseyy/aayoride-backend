<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBaseFareColumnToParcelFaresParcelWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcel_fares_parcel_weights', function (Blueprint $table) {
            $table->double('base_fare')->default(0)->after('parcel_category_id');
            $table->renameColumn('fare','fare_per_km');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcel_fares_parcel_weights', function (Blueprint $table) {
            $table->dropColumn('base_fare');
            $table->renameColumn('fare_per_km','fare');
        });
    }
}
