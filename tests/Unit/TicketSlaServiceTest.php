<?php

use App\Models\SlaRule;
use App\Services\TicketSlaService;
use Carbon\Carbon;

it('skips weekends when calculating due date from sla resolution time', function () {
    $slaRule = new SlaRule(['resolution_time' => 8]);
    $service = new TicketSlaService();

    $start = Carbon::parse('2026-07-10 16:00:00');
    $dueDate = $service->calculateDueDate($start, $slaRule);

    expect($dueDate)->toEqual(Carbon::parse('2026-07-13 15:00:00'));
});
