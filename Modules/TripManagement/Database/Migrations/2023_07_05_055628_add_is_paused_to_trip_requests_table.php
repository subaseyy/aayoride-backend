<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPausedToTripRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_requests', function (Blueprint $table) {
            $table->boolean('is_paused')->default(false)->comment('trip_pause_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_requests', function (Blueprint $table) {
            $table->dropColumn('is_paused');
        });
    }
}
