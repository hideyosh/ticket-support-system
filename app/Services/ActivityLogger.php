<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Ticket;
use App\Models\User;

class ActivityLogger
{
    public static function log(
        Ticket $ticket,
        string $action,
        ?string $field = null,
        mixed $old = null,
        mixed $new = null,
        ?User $actor = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'   => ($actor ?? auth()->user())->id,
            'ticket_id' => $ticket->id,
            'action'    => $action,
            'field'     => $field,
            'old_value' => $old === null ? null : (string) $old,
            'new_value' => $new === null ? null : (string) $new,
        ]);
    }
}
