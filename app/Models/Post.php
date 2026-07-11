<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // ── Post Type Constants ──────────────────────────────────────
    public const TYPE_TEXT  = 'text';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_VIDEO = 'video';
    public const TYPE_LINK  = 'link';
    public const TYPE_SHARE = 'share';

    protected $fillable = [
        'social_account_id',
        'created_by_id',
        'platform_post_id',
        'post_type',
        'content',
        'media_url',
        'media_urls',
        'likes_count',
        'comments_count',
        'shares_count',
        'posted_at',
        'platform_specific_data',
        'status',
    ];

    protected $casts = [
        'media_urls'             => 'array',
        'platform_specific_data' => 'array',
        'posted_at'              => 'datetime',
        'likes_count'            => 'integer',
        'comments_count'         => 'integer',
        'shares_count'           => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public static function getTypeList(): array
    {
        return [
            self::TYPE_TEXT  => 'Text',
            self::TYPE_PHOTO => 'Photo',
            self::TYPE_VIDEO => 'Video',
            self::TYPE_LINK  => 'Link',
            self::TYPE_SHARE => 'Share',
        ];
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->post_type) {
            self::TYPE_PHOTO => 'success',
            self::TYPE_VIDEO => 'info',
            self::TYPE_LINK  => 'warning',
            self::TYPE_SHARE => 'primary',
            default          => 'secondary',
        };
    }

    public function getTotalEngagementAttribute(): int
    {
        return ($this->likes_count ?? 0) + ($this->comments_count ?? 0) + ($this->shares_count ?? 0);
    }
}
