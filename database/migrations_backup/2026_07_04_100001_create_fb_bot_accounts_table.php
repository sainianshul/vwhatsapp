<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fb_bot_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->text('fb_email');
            $table->text('fb_password');
            $table->enum('status', ['active', 'banned', 'cooldown'])->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->integer('total_comments_posted')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fb_bot_accounts');
    }
};
