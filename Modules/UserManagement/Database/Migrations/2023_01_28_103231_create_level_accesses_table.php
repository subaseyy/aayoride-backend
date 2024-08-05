<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('level_id');
            $table->string('user_type', 50);
            $table->boolean('bid')->default(0);
            $table->boolean('see_destination')->default(0);
            $table->boolean('see_subtotal')->default(0);
            $table->boolean('see_level')->default(0);
            $table->boolean('create_hire_request')->default(0);
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
        Schema::dropIfExists('level_accesses');
    }
}
