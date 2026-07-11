<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledOperation extends Model
{
    use HasFactory;

    // ── Status Constants ─────────────────────────────────────────
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_FAILED     = 'failed';
    public const STATUS_CANCELLED  = 'cancelled';

    // ── Operation Types ──────────────────────────────────────────
    public const TYPE_COMMENT = 'comment';
    public const TYPE_LIKE    = 'like';
    public const TYPE_REPLY   = 'reply';

    protected $fillable = [
        'social_account_id',
        'post_id',
        'automation_template_id',
        'assigned_bot_id',
        'operation_type',
        'content_to_post',
        'scheduled_at',
        'status',
        'error_log',
        'completed_at',
        'created_by_id',
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'completed_at'  => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function template()
    {
        return $this->belongsTo(AutomationTemplate::class, 'automation_template_id');
    }

    public function assignedBot()
    {
        return $this->belongsTo(Bot::class, 'assigned_bot_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReadyToExecute($query)
    {
        return $query->pending()->where('scheduled_at', '<=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public static function getStatusList(): array
    {
        return [
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED  => 'Completed',
            self::STATUS_FAILED     => 'Failed',
            self::STATUS_CANCELLED  => 'Cancelled',
        ];
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'warning',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_COMPLETED  => 'success',
            self::STATUS_FAILED     => 'danger',
            self::STATUS_CANCELLED  => 'secondary',
            default                 => 'secondary',
        };
    }
}
