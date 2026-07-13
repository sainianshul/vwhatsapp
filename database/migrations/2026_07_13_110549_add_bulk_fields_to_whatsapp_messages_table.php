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
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->foreignId('bulk_campaign_id')->nullable()->constrained('bulk_campaigns')->nullOnDelete();
            $table->boolean('is_bulk')->default(false);
            $table->json('variables')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropForeign(['bulk_campaign_id']);
            $table->dropColumn(['bulk_campaign_id', 'is_bulk', 'variables']);
        });
    }
};
