<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comment_tasks', function (Blueprint $table) {
            $table->text('post_url')->nullable()->after('scraped_post_id');
            $table->text('comment_text_used')->nullable()->after('type');
            $table->string('target_name')->nullable()->after('comment_text_used');
            // Make scraped_post_id and comment_bank_id nullable for manual comments
            $table->unsignedBigInteger('scraped_post_id')->nullable()->change();
            $table->unsignedBigInteger('comment_bank_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('comment_tasks', function (Blueprint $table) {
            $table->dropColumn(['post_url', 'comment_text_used', 'target_name']);
        });
    }
};
