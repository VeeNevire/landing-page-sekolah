<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = ['name', 'mode', 'username', 'token', 'api_id', 'api_hash', 'is_active'];

    protected $casts = ['token' => 'encrypted', 'api_hash' => 'encrypted', 'is_active' => 'boolean', 'last_verified_at' => 'datetime'];

    public function templates()
    {
        return $this->hasMany(TelegramTemplate::class);
    }

    public function connections()
    {
        return $this->hasMany(TelegramConnection::class);
    }
}
