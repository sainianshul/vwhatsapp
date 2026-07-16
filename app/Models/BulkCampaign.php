<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkCampaign extends Model
{
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
        'delay_min',
        'delay_max',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
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
