<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_account_id',
        'automation_template_id',
        'sync_interval_hours',
        'is_active',
        'last_sync_at',
        'next_sync_at',
        'created_by_id',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'sync_interval_hours'  => 'integer',
        'last_sync_at'         => 'datetime',
        'next_sync_at'         => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function template()
    {
        return $this->belongsTo(AutomationTemplate::class, 'automation_template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForSync($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('next_sync_at')
                  ->orWhere('next_sync_at', '<=', now());
            });
    }
}
