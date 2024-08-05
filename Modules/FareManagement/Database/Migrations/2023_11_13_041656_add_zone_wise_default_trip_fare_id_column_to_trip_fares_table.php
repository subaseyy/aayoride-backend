<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZoneWiseDefaultTripFareIdColumnToTripFaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_fares', function (Blueprint $table) {
            $table->foreignUuid('zone_wise_default_trip_fare_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_fares', function (Blueprint $table) {
            $table->dropColumn('zone_wise_default_trip_fare_id');
        });
    }
}
