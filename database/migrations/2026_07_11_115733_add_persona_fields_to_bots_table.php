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
        Schema::table('bots', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('platform_user_id');
            $table->string('language')->nullable()->after('gender');
            $table->string('slang_level')->nullable()->after('language');
            $table->text('ai_persona')->nullable()->after('slang_level');
            $table->text('system_prompt_override')->nullable()->after('ai_persona');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bots', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'language',
                'slang_level',
                'ai_persona',
                'system_prompt_override'
            ]);
        });
    }
};
