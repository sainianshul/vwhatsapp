<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'target_profile_id', 'platform', 'account_url', 'account_name',
        'account_username', 'followers_count', 'account_type',
        'profile_pic_url', 'description', 'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function targetProfile()
    {
        return $this->belongsTo(TargetProfile::class);
    }

    public function posts()
    {
        return $this->hasMany(ScrapedPost::class)->latest('posted_at');
    }
}
