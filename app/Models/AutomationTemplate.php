<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationTemplate extends Model
{
    use HasFactory;

    // ── Engine Types ─────────────────────────────────────────────
    public const ENGINE_AI   = 'ai';
    public const ENGINE_BANK = 'bank';

    // ── Tone Constants ───────────────────────────────────────────
    public const TONE_POSITIVE = 'positive';
    public const TONE_NEGATIVE = 'negative';
    public const TONE_NEUTRAL  = 'neutral';
    public const TONE_CUSTOM   = 'custom';

    protected $fillable = [
        'name',
        'platform',
        'engine_type',
        'ai_tone',
        'ai_prompt',
        'keywords_include',
        'keywords_exclude',
        'min_likes_required',
        'min_delay_mins',
        'max_delay_mins',
        'max_daily_comments',
        'status',
        'created_by_id',
    ];

    protected $casts = [
        'keywords_include'    => 'array',
        'keywords_exclude'    => 'array',
        'min_likes_required'  => 'integer',
        'min_delay_mins'      => 'integer',
        'max_delay_mins'      => 'integer',
        'max_daily_comments'  => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function automationRules()
    {
        return $this->hasMany(AutomationRule::class);
    }

    public function scheduledOperations()
    {
        return $this->hasMany(ScheduledOperation::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public static function getEngineList(): array
    {
        return [
            self::ENGINE_AI   => 'AI Generator',
            self::ENGINE_BANK => 'Comment Bank',
        ];
    }

    public static function getToneList(): array
    {
        return [
            self::TONE_POSITIVE => 'Positive',
            self::TONE_NEGATIVE => 'Negative',
            self::TONE_NEUTRAL  => 'Neutral',
            self::TONE_CUSTOM   => 'Custom',
        ];
    }

    public function getToneColorAttribute(): string
    {
        return match ($this->ai_tone) {
            self::TONE_POSITIVE => 'success',
            self::TONE_NEGATIVE => 'danger',
            self::TONE_NEUTRAL  => 'warning',
            self::TONE_CUSTOM   => 'info',
            default             => 'secondary',
        };
    }
}
