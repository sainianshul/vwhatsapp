<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scraped_post_id')->constrained('scraped_posts')->onDelete('cascade');
            $table->foreignId('comment_bank_id')->constrained('comment_bank')->onDelete('cascade');
            $table->foreignId('fb_bot_account_id')->constrained('fb_bot_accounts')->onDelete('cascade');
            $table->enum('type', ['good', 'bad'])->default('good');
            $table->enum('status', ['pending', 'processing', 'posted', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_tasks');
    }
};
