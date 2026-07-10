<?php

namespace App\Services;

use App\Models\Ticket;
use App\Exceptions\InvalidStatusTransitionException;

class TicketStatusService
{
    /**
     * @var array<string, array<string>>
     */
    protected const TRANSITIONS = [
        'open'                 => ['assigned', 'closed'],
        'assigned'             => ['in_progress', 'escalated'],
        'in_progress'          => ['waiting_for_customer', 'resolved', 'escalated'],
        'waiting_for_customer' => ['in_progress', 'resolved'],
        'resolved'             => ['closed', 'reopened'],
        'closed'               => ['reopened'],
        'reopened'             => ['assigned', 'in_progress'],
        'escalated'            => ['in_progress', 'resolved'],
    ];

    /**
     * Dapatkan daftar status tujuan yang valid.
     *
     * @param string $currentStatus
     * @return array<string>
     */
    public function allowedTransitions(string $currentStatus): array
    {
        return self::TRANSITIONS[$currentStatus] ?? [];
    }

    /**
     * Cek apakah transisi dari suatu status ke status lain valid.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function canTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return true; // Transisi ke status yang sama dianggap valid (tidak ada perubahan)
        }
        
        $allowed = $this->allowedTransitions($from);
        
        return in_array($to, $allowed);
    }

    /**
     * Lakukan transisi status tiket.
     *
     * @param Ticket $ticket
     * @param string $newStatus
     * @return void
     * @throws InvalidStatusTransitionException
     */
    public function transition(Ticket $ticket, string $newStatus): void
    {
        if ($ticket->status === $newStatus) {
            return; // Tidak ada perubahan status
        }

        if (!$this->canTransition($ticket->status, $newStatus)) {
            throw new InvalidStatusTransitionException($ticket->status, $newStatus);
        }

        $ticket->status = $newStatus;

        if ($newStatus === 'resolved') {
            $ticket->resolved_at = now();
        } elseif ($newStatus === 'closed') {
            $ticket->closed_at = now();
        } elseif ($newStatus === 'reopened') {
            $ticket->resolved_at = null;
            $ticket->closed_at = null;
        }

        $ticket->save();
    }
}
