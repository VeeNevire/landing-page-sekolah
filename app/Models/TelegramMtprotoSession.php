<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramMtprotoSession extends Model
{
    protected $fillable = [
        'api_id', 'api_hash', 'session_path', 'telegram_user_id', 'username',
        'first_name', 'status', 'connected_at', 'last_error',
    ];

    protected $casts = [
        'api_hash' => 'encrypted',
        'connected_at' => 'datetime',
        'telegram_user_id' => 'integer',
    ];
}
