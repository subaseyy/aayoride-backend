<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payer_id',64)->nullable();
            $table->string('receiver_id',64)->nullable();
            $table->decimal('payment_amount', 24, 2)->default(0);
            $table->string('gateway_callback_url',191)->nullable();
            $table->string('hook',100)->nullable();
            $table->string('transaction_id',100)->nullable();
            $table->string('currency_code',20)->default('USD');
            $table->string('payment_method',50)->nullable();
            $table->json('additional_data')->nullable();
            $table->boolean('is_paid')->default(0);
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
        Schema::dropIfExists('payment_requests');
    }
};
