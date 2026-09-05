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
        Schema::table('bulk_campaigns', function (Blueprint $table) {
            $table->unsignedInteger('batch_size')->default(50)->after('delay_max');
            $table->unsignedInteger('cooldown_minutes')->default(5)->after('batch_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_campaigns', function (Blueprint $table) {
            $table->dropColumn(['batch_size', 'cooldown_minutes']);
        });
    }
};
