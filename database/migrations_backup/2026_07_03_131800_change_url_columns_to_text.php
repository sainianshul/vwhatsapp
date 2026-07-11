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
        Schema::table('target_profiles', function (Blueprint $table) {
            $table->text('photo_url')->nullable()->change();
        });

        Schema::table('social_accounts', function (Blueprint $table) {
            $table->text('account_url')->nullable()->change();
            $table->text('profile_pic_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_profiles', function (Blueprint $table) {
            $table->string('photo_url', 255)->nullable()->change();
        });

        Schema::table('social_accounts', function (Blueprint $table) {
            $table->string('account_url', 255)->nullable()->change();
            $table->string('profile_pic_url', 255)->nullable()->change();
        });
    }
};
