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
        Schema::create('bulk_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_account_id')->constrained('whats_app_accounts')->cascadeOnDelete();
            $table->string('campaign_name');
            $table->text('message_template');
            $table->string('csv_file_path');
            $table->integer('total_contacts')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('status', ['pending', 'running', 'paused', 'completed', 'failed'])->default('pending');
            $table->integer('delay_min')->default(12);
            $table->integer('delay_max')->default(30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_campaigns');
    }
};
