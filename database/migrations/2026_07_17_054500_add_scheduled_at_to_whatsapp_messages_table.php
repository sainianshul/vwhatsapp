<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('status');
        });

        // Expand status enum to include 'scheduled'
        DB::statement("ALTER TABLE whatsapp_messages MODIFY COLUMN status ENUM('pending','scheduled','sent','failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });

        DB::statement("ALTER TABLE whatsapp_messages MODIFY COLUMN status ENUM('pending','sent','failed') DEFAULT 'pending'");
    }
};
