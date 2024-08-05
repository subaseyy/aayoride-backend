<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary()->index();
            $table->foreignUuid('user_id')->nullable();
            $table->decimal('payable_balance',24)->default(0);
            $table->decimal('receivable_balance',24)->default(0);
            $table->decimal('received_balance',24)->default(0);
            $table->decimal('pending_balance',24)->default(0);
            $table->decimal('wallet_balance',24)->default(0);
            $table->decimal('total_withdrawn',24)->default(0);
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
        Schema::dropIfExists('user_accounts');
    }
}
