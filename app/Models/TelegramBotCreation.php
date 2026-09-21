<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBotCreation extends Model
{
    protected $fillable = ['name', 'username', 'manager_username', 'status', 'telegram_bot_id', 'error'];

    protected $casts = ['telegram_bot_id' => 'integer'];

    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }
}
