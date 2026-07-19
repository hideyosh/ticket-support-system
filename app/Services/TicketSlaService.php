<?php

namespace App\Services;

use App\Models\SlaRule;
use Carbon\Carbon;

class TicketSlaService
{
    public function calculateDueDate(Carbon $start, SlaRule $slaRule, string $status): Carbon
    {
        $hours = match ($status) {
            'open' => (int) $slaRule->resolution_time,
            'assigned' => (int) $slaRule->response_time,
        };

        return $start->copy()->addHours($hours);
    }
}
