<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->longText('driver_note')->nullable()->after('note');
            $table->longText('approval_note')->nullable()->after('driver_note');
            $table->longText('denied_note')->nullable()->after('approval_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropColumn(['driver_note','approval_note','denied_note']);
        });
    }
};
