<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bot extends Model
{
    use HasFactory, SoftDeletes;

    // ── Platforms ─────────────────────────────────────────────────────
    public const PLATFORM_FACEBOOK  = 'facebook';
    public const PLATFORM_INSTAGRAM = 'instagram';
    public const PLATFORM_TWITTER   = 'twitter';
    public const PLATFORM_YOUTUBE   = 'youtube';
    public const PLATFORM_TIKTOK    = 'tiktok';

    // ── Bot Type ─────────────────────────────────────────────────────
    public const TYPE_SCRAPER = 'scraper';
    public const TYPE_ACTION  = 'action';
    public const TYPE_BOTH    = 'both';

    // ── Our Internal Status ──────────────────────────────────────────
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PAUSED   = 'paused';
    public const STATUS_RETIRED  = 'retired';

    // ── Platform-Side Status ─────────────────────────────────────────
    public const PLATFORM_STATUS_ACTIVE      = 'active';
    public const PLATFORM_STATUS_RESTRICTED  = 'restricted';
    public const PLATFORM_STATUS_WARNED      = 'warned';
    public const PLATFORM_STATUS_TEMP_BANNED = 'temporarily_banned';
    public const PLATFORM_STATUS_PERM_BANNED = 'permanently_banned';
    public const PLATFORM_STATUS_CHECKPOINT  = 'checkpoint_required';
    public const PLATFORM_STATUS_UNKNOWN     = 'unknown';
    public const PLATFORM_STATUS_EXPIRED     = 'expired';

    protected $fillable = [
        'name',
        'platform',
        'type',
        'status',
        'platform_status',
        'platform_status_note',
        'platform_status_checked_at',
        'platform_username',
        'platform_user_id',
        'gender',
        'language',
        'slang_level',
        'ai_persona',
        'system_prompt_override',
        'cookie',
        'cookie_updated_at',
        'user_agent',
        'proxy',
        'notes',
        'last_action_at',
        'last_scrape_at',
        'total_actions_count',
        'total_scrapes_count',
        'created_by',
    ];

    protected $casts = [
        'platform_status_checked_at' => 'datetime',
        'cookie_updated_at'          => 'datetime',
        'last_action_at'             => 'datetime',
        'last_scrape_at'             => 'datetime',
        'total_actions_count'        => 'integer',
        'total_scrapes_count'        => 'integer',
    ];

    // ── List Helpers ─────────────────────────────────────────────────

    public static function getPlatformList(): array
    {
        return [
            self::PLATFORM_FACEBOOK  => 'Facebook',
            self::PLATFORM_INSTAGRAM => 'Instagram',
            self::PLATFORM_TWITTER   => 'Twitter / X',
            self::PLATFORM_YOUTUBE   => 'YouTube',
            self::PLATFORM_TIKTOK    => 'TikTok',
        ];
    }

    public static function getTypeList(): array
    {
        return [
            self::TYPE_SCRAPER => 'Scraper',
            self::TYPE_ACTION  => 'Action',
            self::TYPE_BOTH    => 'Both',
        ];
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE   => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_PAUSED   => 'Paused',
            self::STATUS_RETIRED  => 'Retired',
        ];
    }

    public static function getPlatformStatusList(): array
    {
        return [
            self::PLATFORM_STATUS_ACTIVE      => 'Active',
            self::PLATFORM_STATUS_RESTRICTED  => 'Restricted',
            self::PLATFORM_STATUS_WARNED      => 'Warned',
            self::PLATFORM_STATUS_TEMP_BANNED => 'Temporarily Banned',
            self::PLATFORM_STATUS_PERM_BANNED => 'Permanently Banned',
            self::PLATFORM_STATUS_CHECKPOINT  => 'Checkpoint Required',
            self::PLATFORM_STATUS_UNKNOWN     => 'Unknown',
            self::PLATFORM_STATUS_EXPIRED     => 'Expired',
        ];
    }

    // ── Color Accessors ──────────────────────────────────────────────

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE   => 'success',
            self::STATUS_INACTIVE => 'warning',
            self::STATUS_PAUSED   => 'info',
            self::STATUS_RETIRED  => 'secondary',
            default               => 'secondary',
        };
    }

    public function getPlatformStatusColorAttribute(): string
    {
        return match ($this->platform_status) {
            self::PLATFORM_STATUS_ACTIVE      => 'success',
            self::PLATFORM_STATUS_RESTRICTED  => 'warning',
            self::PLATFORM_STATUS_WARNED      => 'warning',
            self::PLATFORM_STATUS_TEMP_BANNED => 'danger',
            self::PLATFORM_STATUS_PERM_BANNED => 'danger',
            self::PLATFORM_STATUS_CHECKPOINT  => 'info',
            self::PLATFORM_STATUS_UNKNOWN     => 'secondary',
            default                           => 'secondary',
        };
    }

    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK  => 'ki-outline ki-facebook',
            self::PLATFORM_INSTAGRAM => 'ki-outline ki-instagram',
            self::PLATFORM_TWITTER   => 'ki-outline ki-twitter',
            self::PLATFORM_YOUTUBE   => 'ki-outline ki-youtube',
            self::PLATFORM_TIKTOK    => 'ki-outline ki-tiktok',
            default                  => 'ki-outline ki-abstract-26',
        };
    }

    public function getPlatformColorAttribute(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK  => 'primary',
            self::PLATFORM_INSTAGRAM => 'danger',
            self::PLATFORM_TWITTER   => 'info',
            self::PLATFORM_YOUTUBE   => 'danger',
            self::PLATFORM_TIKTOK    => 'dark',
            default                  => 'secondary',
        };
    }

    // ── Computed ──────────────────────────────────────────────────────

    public function getHasCookieAttribute(): bool
    {
        return !empty($this->cookie);
    }

    // ── Relationships ────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}
