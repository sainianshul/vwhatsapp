<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    protected $fillable = [
        'media_group_id',
        'asset_code',
        'name',
        'file_path',
        'file_name',
        'mime_type',
        'size',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(MediaGroup::class, 'media_group_id');
    }
}
