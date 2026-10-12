<?php

namespace App\Console\Commands;

use App\Models\AttendanceNotificationDelivery;
use App\Services\RfidNotificationService;
use Illuminate\Console\Command;

class DispatchAttendanceNotifications extends Command
{
    protected $signature = 'rfid:dispatch-notifications';

    protected $description = 'Queue pending RFID Telegram notifications';

    public function handle(RfidNotificationService $notifications): int
    {
        AttendanceNotificationDelivery::where('status', 'pending')->select('id')->chunkById(100, function ($rows) use ($notifications) {
            foreach ($rows as $row) {
                $notifications->enqueue($row->id);
            }
        });

        return self::SUCCESS;
    }
}
