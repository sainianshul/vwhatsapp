<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'whatsapp_account_id',
        'campaign_name',
        'message_template',
        'csv_file_path',
        'total_contacts',
        'sent_count',
        'failed_count',
        'status',
        'media_path',
        'media_filename',
        'media_group_id',
        'scheduled_at',
        'delay_min',
        'delay_max',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }

    public function mediaGroup()
    {
        return $this->belongsTo(MediaGroup::class, 'media_group_id');
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
