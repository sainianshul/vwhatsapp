<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapedPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_account_id',
        'fb_post_id',
        'post_url',
        'post_type',
        'content',
        'media_url',
        'posted_at',
        'reactions_count',
        'comments_count',
        'shares_count',
        'scraped_at',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'scraped_at' => 'datetime',
    ];

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function comments()
    {
        return $this->hasMany(ScrapedComment::class);
    }
}
