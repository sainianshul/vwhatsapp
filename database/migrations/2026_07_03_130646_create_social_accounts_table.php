<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_profile_id')->constrained('target_profiles')->onDelete('cascade');
            $table->enum('platform', ['facebook', 'twitter', 'instagram'])->default('facebook');
            $table->string('account_url');
            $table->string('account_name');
            $table->string('account_username')->nullable();
            $table->string('followers_count')->nullable();
            $table->string('account_type')->nullable();
            $table->string('profile_pic_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
