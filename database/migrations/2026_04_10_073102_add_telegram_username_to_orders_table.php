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
        if (!Schema::hasColumn('orders', 'telegram_username')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('telegram_username')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'telegram_username')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('telegram_username');
            });
        }
    }
};
