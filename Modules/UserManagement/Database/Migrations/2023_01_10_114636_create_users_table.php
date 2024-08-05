<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_level_id')->nullable();
                $table->string('first_name',191)->nullable();
                $table->string('last_name',191)->nullable();
                $table->string('email',191)->unique()->nullable();
                $table->string('phone',20)->unique()->nullable();
                $table->string('identification_number',191)->nullable();
                $table->string('identification_type',25)->nullable();
                $table->json('identification_image')->nullable();
                $table->json('other_documents')->nullable();
                $table->string('profile_image',191)->nullable();
                $table->string('fcm_token',191)->nullable();
                $table->timestamp('phone_verified_at')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->double('loyalty_points')->default(0);
                $table->string('password',191)->nullable();
                $table->string('user_type',25)->default('customer'); // super-admin, admin-employee, customer, rider
                $table->foreignUuid('role_id')->nullable();
                $table->rememberToken();
                $table->boolean('is_active')->default(0);
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
