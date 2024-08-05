<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attribute_id')->nullable();
            $table->string('attribute')->nullable();
            $table->decimal('debit',24)->default(0);
            $table->decimal('credit',24)->default(0);
            $table->decimal('balance',24)->default(0);
            $table->foreignUuid('user_id')->nullable();
            $table->string('account')->nullable();
            $table->uuid('trx_ref_id')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
