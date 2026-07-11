<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_account_id',
        'status',
        'message',
        'posts_found',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }
}
