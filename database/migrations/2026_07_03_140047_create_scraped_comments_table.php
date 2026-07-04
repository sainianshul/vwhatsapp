<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scraped_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scraped_post_id')->constrained('scraped_posts')->onDelete('cascade');
            $table->string('commenter_name');
            $table->text('commenter_url')->nullable();
            $table->text('comment_text');
            $table->integer('reactions_count')->default(0);
            $table->timestamp('commented_at')->nullable();
            $table->timestamp('scraped_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraped_comments');
    }
};
