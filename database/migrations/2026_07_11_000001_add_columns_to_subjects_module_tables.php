<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Subjects ─────────────────────────────────────────────────
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->string('name')->after('created_by_id');
            $table->string('photo_url')->nullable()->after('name');
            $table->string('designation')->nullable()->after('photo_url');
            $table->text('notes')->nullable()->after('designation');
            $table->string('status')->default('active')->after('notes');
            $table->softDeletes();
        });

        // ── Social Accounts ──────────────────────────────────────────
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->foreignId('subject_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_id')->nullable()->after('subject_id')->constrained('users')->nullOnDelete();
            $table->string('platform')->default('facebook')->after('created_by_id');
            $table->string('platform_account_id')->nullable()->after('platform');
            $table->string('account_name')->after('platform_account_id');
            $table->string('account_url')->after('account_name');
            $table->string('account_type')->nullable()->after('account_url');
            $table->string('profile_pic_url')->nullable()->after('account_type');
            $table->unsignedBigInteger('followers_count')->nullable()->after('profile_pic_url');
            $table->boolean('verified')->default(false)->after('followers_count');
            $table->timestamp('last_scraped_at')->nullable()->after('verified');
            $table->string('status')->default('active')->after('last_scraped_at');

            // Prevent duplicate account links per subject
            $table->unique(['subject_id', 'account_url'], 'social_accounts_subject_url_unique');
        });

        // ── Posts ─────────────────────────────────────────────────────
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('social_account_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_id')->nullable()->after('social_account_id')->constrained('users')->nullOnDelete();
            $table->string('platform_post_id')->nullable()->after('created_by_id');
            $table->string('post_type')->default('text')->after('platform_post_id');
            $table->text('content')->nullable()->after('post_type');
            $table->string('media_url')->nullable()->after('content');
            $table->json('media_urls')->nullable()->after('media_url');
            $table->unsignedInteger('likes_count')->default(0)->after('media_urls');
            $table->unsignedInteger('comments_count')->default(0)->after('likes_count');
            $table->unsignedInteger('shares_count')->default(0)->after('comments_count');
            $table->timestamp('posted_at')->nullable()->after('shares_count');
            $table->json('platform_specific_data')->nullable()->after('posted_at');
            $table->string('status')->default('active')->after('platform_specific_data');

            // Prevent duplicate posts
            $table->unique(['social_account_id', 'platform_post_id'], 'posts_account_post_unique');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropUnique('posts_account_post_unique');
            $table->dropForeign(['social_account_id']);
            $table->dropForeign(['created_by_id']);
            $table->dropColumn([
                'social_account_id', 'created_by_id', 'platform_post_id', 'post_type',
                'content', 'media_url', 'media_urls', 'likes_count', 'comments_count',
                'shares_count', 'posted_at', 'platform_specific_data', 'status',
            ]);
        });

        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropUnique('social_accounts_subject_url_unique');
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['created_by_id']);
            $table->dropColumn([
                'subject_id', 'created_by_id', 'platform', 'platform_account_id',
                'account_name', 'account_url', 'account_type', 'profile_pic_url',
                'followers_count', 'verified', 'last_scraped_at', 'status',
            ]);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['user_id']);
            $table->dropForeign(['created_by_id']);
            $table->dropColumn([
                'user_id', 'created_by_id', 'name', 'photo_url',
                'designation', 'notes', 'status',
            ]);
        });
    }
};
