<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    public static function log(
        string $action,
        ?string $entityType = null,
        $entityId = null,
        ?int $userId = null
    ): AuditLog {
        return AuditLog::create([
            'user_id'     => $userId ?? auth()->id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId ? (string) $entityId : null,
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
