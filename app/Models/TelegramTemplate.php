<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramTemplate extends Model
{
    protected $fillable = ['telegram_bot_id', 'name', 'report_type', 'body', 'parameter_schema', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'parameter_schema' => 'array'];

    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }
}
