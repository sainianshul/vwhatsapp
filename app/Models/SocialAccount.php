<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;

    // ── Platform Constants ────────────────────────────────────────
    public const PLATFORM_FACEBOOK  = 'facebook';
    public const PLATFORM_INSTAGRAM = 'instagram';
    public const PLATFORM_TWITTER   = 'twitter';
    public const PLATFORM_YOUTUBE   = 'youtube';

    // ── Status Constants ─────────────────────────────────────────
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'subject_id',
        'platform',
        'platform_account_id',
        'account_name',
        'account_url',
        'account_type',
        'profile_pic_url',
        'followers_count',
        'verified',
        'scrape_status',
        'last_scraped_at',
        'status',
        'created_by_id',
    ];

    protected $casts = [
        'last_scraped_at' => 'datetime',
        'verified'        => 'boolean',
        'followers_count' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function automationRule()
    {
        return $this->hasOne(AutomationRule::class);
    }

    public function scheduledOperations()
    {
        return $this->hasMany(ScheduledOperation::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public static function getPlatformList(): array
    {
        return [
            self::PLATFORM_FACEBOOK  => 'Facebook',
            self::PLATFORM_INSTAGRAM => 'Instagram',
            self::PLATFORM_TWITTER   => 'Twitter',
            self::PLATFORM_YOUTUBE   => 'YouTube',
        ];
    }

    public function getPlatformColorAttribute(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK  => 'primary',
            self::PLATFORM_INSTAGRAM => 'danger',
            self::PLATFORM_TWITTER   => 'info',
            self::PLATFORM_YOUTUBE   => 'danger',
            default                  => 'secondary',
        };
    }

    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK  => 'ki-outline ki-facebook',
            self::PLATFORM_INSTAGRAM => 'ki-outline ki-instagram',
            self::PLATFORM_TWITTER   => 'ki-outline ki-twitter',
            self::PLATFORM_YOUTUBE   => 'ki-outline ki-youtube',
            default                  => 'ki-outline ki-globe',
        };
    }

    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
    }
}
