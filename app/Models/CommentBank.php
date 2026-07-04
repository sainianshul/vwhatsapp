<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentBank extends Model
{
    use HasFactory;

    protected $table = 'comment_bank';

    protected $fillable = [
        'comment_text',
        'type',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tasks()
    {
        return $this->hasMany(CommentTask::class, 'comment_bank_id');
    }
}
