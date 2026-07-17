<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'user_id',
        'whatsapp_account_id',
        'receiver_number',
        'message_text',
        'media_path',
        'media_type',
        'status',
        'scheduled_at',
        'error_message',
        'bulk_campaign_id',
        'is_bulk',
        'variables',
        'source'
    ];

    protected $casts = [
        'variables' => 'array',
        'scheduled_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }

    public function bulkCampaign()
    {
        return $this->belongsTo(BulkCampaign::class);
    }

    public function account()
    {
        return $this->belongsTo(WhatsAppAccount::class, 'whatsapp_account_id');
    }
}
