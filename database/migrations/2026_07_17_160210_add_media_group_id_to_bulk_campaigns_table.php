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
            $table->unsignedBigInteger('media_group_id')->nullable()->after('whatsapp_account_id');
            $table->foreign('media_group_id')->references('id')->on('media_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_campaigns', function (Blueprint $table) {
            $table->dropForeign(['media_group_id']);
            $table->dropColumn('media_group_id');
        });
    }
};
