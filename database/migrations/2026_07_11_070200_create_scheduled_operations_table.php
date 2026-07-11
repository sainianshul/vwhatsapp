<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('automation_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_bot_id')->nullable()->constrained('bots')->nullOnDelete();
            $table->string('operation_type', 20)->default('comment'); // comment, like, reply
            $table->text('content_to_post');
            $table->timestamp('scheduled_at');
            $table->string('status', 20)->default('pending'); // pending, processing, completed, failed
            $table->text('error_log')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']); // For the Executor cron query
            $table->index('social_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_operations');
    }
};
