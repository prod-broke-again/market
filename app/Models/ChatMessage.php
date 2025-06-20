<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $casts = [
        'is_read' => 'boolean',
        'is_show' => 'boolean',
    ];

    protected $fillable = [
        'chat_id', 'user_id', 'message', 'is_read', 'is_show'
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
