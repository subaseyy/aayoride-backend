<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 20);
            $table->foreignUuid('customer_id')->nullable();
            $table->foreignUuid('driver_id')->nullable();
            $table->foreignUuid('vehicle_category_id')->nullable();
            $table->foreignUuid('vehicle_id')->nullable();
            $table->foreignUuid('zone_id')->nullable();
            $table->foreignUuid('area_id')->nullable();
            $table->decimal('estimated_fare', 23, 3);
            $table->decimal('actual_fare', 23, 3)->default(0);
            $table->float('estimated_distance');
            $table->decimal('paid_fare', 23, 3)->default(0);
            $table->float('actual_distance')->nullable();
            $table->text('encoded_polyline')->nullable();
            $table->string('accepted_by')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable()->default('unpaid');
            $table->foreignUuid('coupon_id')->nullable();
            $table->decimal('coupon_amount', 23, 3)->nullable();
            $table->text('note')->nullable();
            $table->string('entrance')->nullable();
            $table->string('otp')->nullable();
            $table->integer('rise_request_count')->default(0);
            $table->string('type')->nullable();
            $table->string('current_status', 20)->default('pending');
            $table->boolean('checked')->default('0');
            $table->double('tips')->default(0);
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
        Schema::dropIfExists('trip_requests');
    }
}
