<?php

namespace App\Services;

use App\Models\SlaRule;
use Carbon\Carbon;

class TicketSlaService
{
    public function calculateDueDate(Carbon $start, SlaRule $slaRule, string $status): Carbon
    {
        $hours = match ($status) {
            'open' => (int) $slaRule->response_time,
            'assigned', 'in_progress', 'waiting_for_customer',
            'escalated', 'reopened' => (int) $slaRule->resolution_time,

            default => (int) $slaRule->resolution_time,
        };

        $hoursLeft = $hours;
        $current = $start->copy();

        while ($hoursLeft > 0) {
            $current->addHour();

            if ($this->isWorkingHour($current)) {
                $hoursLeft--;
            }
        }

        return $current;
    }

    public function isWorkingHour(Carbon $date): bool
    {
        if ($date->isWeekend()) {
            return false;
        }

        if ($date->hour < 8 || $date->hour >= 17) {
            return false;
        }

        return true;
    }
}
