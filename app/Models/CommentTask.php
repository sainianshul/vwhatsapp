<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'scraped_post_id',
        'post_url',
        'comment_bank_id',
        'fb_bot_account_id',
        'type',
        'comment_text_used',
        'target_name',
        'status',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(ScrapedPost::class, 'scraped_post_id');
    }

    public function comment()
    {
        return $this->belongsTo(CommentBank::class, 'comment_bank_id');
    }

    public function botAccount()
    {
        return $this->belongsTo(FbBotAccount::class, 'fb_bot_account_id');
    }
}
