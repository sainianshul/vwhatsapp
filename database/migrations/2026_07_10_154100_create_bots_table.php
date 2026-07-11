<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('platform');          // facebook, instagram, twitter, youtube, tiktok
            $table->string('type');              // scraper, action, both

            // Dual-status system
            $table->string('status')->default('active');                  // Our internal status
            $table->string('platform_status')->default('unknown');        // Platform-side status
            $table->text('platform_status_note')->nullable();
            $table->timestamp('platform_status_checked_at')->nullable();

            // Platform account info
            $table->string('platform_username')->nullable();
            $table->string('platform_user_id')->nullable();

            // Cookie management
            $table->longText('cookie')->nullable();
            $table->timestamp('cookie_updated_at')->nullable();

            // Connection details
            $table->text('user_agent')->nullable();
            $table->string('proxy')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Activity tracking
            $table->timestamp('last_action_at')->nullable();
            $table->timestamp('last_scrape_at')->nullable();
            $table->unsignedInteger('total_actions_count')->default(0);
            $table->unsignedInteger('total_scrapes_count')->default(0);

            // Ownership
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('platform');
            $table->index('type');
            $table->index('status');
            $table->index('platform_status');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
};
