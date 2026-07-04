<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetProfile extends Model
{
    protected $fillable = [
        'name', 'designation', 'party', 'city', 'photo_url', 'notes', 'status',
    ];

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
}
