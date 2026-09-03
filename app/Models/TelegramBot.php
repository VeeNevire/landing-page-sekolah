<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = ['name', 'username', 'token', 'is_active'];

    protected $casts = ['token' => 'encrypted', 'is_active' => 'boolean'];

    public function templates()
    {
        return $this->hasMany(TelegramTemplate::class);
    }

    public function connections()
    {
        return $this->hasMany(TelegramConnection::class);
    }
}
