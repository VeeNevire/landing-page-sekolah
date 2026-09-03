<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportPublication extends Model
{
    protected $fillable = ['teacher_id', 'telegram_template_id', 'telegram_bot_id', 'report_type', 'class_name', 'report_date', 'published_at'];
    protected $casts = ['report_date' => 'date', 'published_at' => 'datetime'];
}
