<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapedComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'scraped_post_id',
        'commenter_name',
        'commenter_url',
        'comment_text',
        'reactions_count',
        'commented_at',
        'scraped_at',
    ];

    protected $casts = [
        'commented_at' => 'datetime',
        'scraped_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(ScrapedPost::class, 'scraped_post_id');
    }
}
