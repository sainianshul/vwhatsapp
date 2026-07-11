<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    // ── Status Constants ─────────────────────────────────────────
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'user_id',
        'created_by_id',
        'name',
        'photo_url',
        'designation',
        'notes',
        'status',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE   => 'Active',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE   => 'success',
            self::STATUS_ARCHIVED => 'warning',
            default               => 'secondary',
        };
    }

    public function getAccountsCountAttribute(): int
    {
        return $this->socialAccounts()->count();
    }

    public function getTotalPostsCountAttribute(): int
    {
        return Post::whereIn('social_account_id', $this->socialAccounts()->pluck('id'))->count();
    }
}
