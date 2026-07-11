<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('platform', 30)->default('facebook');
            $table->string('engine_type', 20)->default('ai'); // ai, bank
            $table->string('ai_tone', 30)->default('positive'); // positive, negative, neutral, custom
            $table->text('ai_prompt')->nullable();
            $table->json('keywords_include')->nullable();
            $table->json('keywords_exclude')->nullable();
            $table->unsignedInteger('min_likes_required')->default(0);
            $table->unsignedInteger('min_delay_mins')->default(5);
            $table->unsignedInteger('max_delay_mins')->default(15);
            $table->unsignedInteger('max_daily_comments')->default(20);
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_templates');
    }
};
