<?php

namespace App\Services;

use App\Models\SlaRule;
use Carbon\Carbon;

class TicketSlaService
{
    public function calculateDueDate(Carbon $start, SlaRule $slaRule, string $status): ?Carbon
    {
        $hours = match ($status) {
            'open', 'assigned' => (int) $slaRule->response_time,
            'in_progress', 'reopened', 'escalated' => (int) $slaRule->resolution_time,
            'waiting_for_customer', 'resolved', 'closed' => null,

            default => (int) $slaRule->resolution_time,
        };

        return $hours ? $start->copy()->addHours($hours) : null;
    }
}
