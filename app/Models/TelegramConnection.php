<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramConnection extends Model
{
    protected $fillable = [
        'user_id', 'telegram_bot_id', 'chat_id', 'telegram_username',
        'link_token_hash', 'link_token_expires_at', 'connected_at',
    ];

    protected $casts = [
        'link_token_expires_at' => 'datetime',
        'connected_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function bot() { return $this->belongsTo(TelegramBot::class, 'telegram_bot_id'); }
}
