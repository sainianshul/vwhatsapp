<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scraped_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_account_id')->constrained('social_accounts')->onDelete('cascade');
            $table->string('fb_post_id')->nullable();
            $table->text('post_url');
            $table->enum('post_type', ['text', 'photo', 'video', 'link', 'share', 'reel', 'live', 'unknown'])->default('unknown');
            $table->text('content')->nullable();
            $table->text('media_url')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->integer('reactions_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->timestamp('scraped_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraped_posts');
    }
};
