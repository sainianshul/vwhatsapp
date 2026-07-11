<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fb_bot_accounts', function (Blueprint $table) {
            $table->dropColumn('fb_password');
            $table->longText('fb_cookies')->after('fb_email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fb_bot_accounts', function (Blueprint $table) {
            $table->dropColumn('fb_cookies');
            $table->text('fb_password')->after('fb_email')->nullable();
        });
    }
};
