<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    public int $id;
    public int $chat_id;
    public int $user_id;
    public string $message;
    public bool $is_read;
    public bool $is_show;

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
