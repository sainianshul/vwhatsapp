<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class FbBotAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'fb_email',
        'fb_cookies',
        'status',
        'last_used_at',
        'total_comments_posted',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'fb_cookies',
    ];

    // Encrypt cookies when setting
    public function setFbCookiesAttribute($value)
    {
        $this->attributes['fb_cookies'] = Crypt::encryptString($value);
    }

    // Decrypt cookies when getting
    public function getFbCookiesDecryptedAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['fb_cookies']);
        } catch (\Exception $e) {
            return $this->attributes['fb_cookies'];
        }
    }

    // Encrypt email when setting
    public function setFbEmailAttribute($value)
    {
        $this->attributes['fb_email'] = Crypt::encryptString($value);
    }

    // Decrypt email when getting
    public function getFbEmailDecryptedAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['fb_email']);
        } catch (\Exception $e) {
            return $this->attributes['fb_email'];
        }
    }

    public function tasks()
    {
        return $this->hasMany(CommentTask::class, 'fb_bot_account_id');
    }
}
