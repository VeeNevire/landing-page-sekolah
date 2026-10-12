<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceNotificationDelivery extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }
}
