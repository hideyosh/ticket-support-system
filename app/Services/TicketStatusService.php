<?php

namespace App\Services;

use App\Models\Ticket;
use App\Exceptions\InvalidStatusTransitionException;

class TicketStatusService
{
    /**
     *
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
     * Peta warna Bootstrap badge untuk setiap status.
     *
     * @var array<string, string>
     */
    protected const STATUS_COLORS = [
        'open'                 => 'secondary',
        'assigned'             => 'info',
        'in_progress'          => 'primary',
        'waiting_for_customer' => 'warning',
        'resolved'             => 'success',
        'closed'               => 'dark',
        'reopened'             => 'danger',
        'escalated'            => 'danger',
    ];

    /**
     * Peta label tampilan untuk setiap status.
     *
     * @var array<string, string>
     */
    protected const STATUS_LABELS = [
        'open'                 => 'Open',
        'assigned'             => 'Assigned',
        'in_progress'          => 'In Progress',
        'waiting_for_customer' => 'Waiting for Customer',
        'resolved'             => 'Resolved',
        'closed'               => 'Closed',
        'reopened'             => 'Reopened',
        'escalated'            => 'Escalated',
    ];

    // -------------------------------------------------------------------------
    // Transition Logic
    // -------------------------------------------------------------------------

    /**
     * Dapatkan daftar status tujuan yang valid dari status saat ini.
     *
     * @param  string         $currentStatus
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
            return true;
        }

        return in_array($to, $this->allowedTransitions($from));
    }

    /**
     * Lakukan transisi status tiket dengan validasi ketat.
     *
     * @param  Ticket $ticket
     * @param  string $newStatus
     * @return void
     * @throws InvalidStatusTransitionException
     */
    public function transition(Ticket $ticket, string $newStatus): void
    {
        if ($ticket->status === $newStatus) {
            return;
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
            $ticket->closed_at   = null;
        }

        $ticket->save();
    }

    // -------------------------------------------------------------------------
    // UI Helpers — tersentralisasi agar tidak duplikat di setiap view
    // -------------------------------------------------------------------------

    /**
     * Dapatkan warna Bootstrap badge untuk status tertentu.
     *
     * @param  string $status
     * @return string
     */
    public function statusColor(string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'secondary';
    }

    /**
     * Dapatkan label tampilan untuk status tertentu.
     *
     * @param  string $status
     * @return string
     */
    public function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Dapatkan peta lengkap warna semua status.
     *
     * @return array<string, string>
     */
    public function statusColorMap(): array
    {
        return self::STATUS_COLORS;
    }

    /**
     * Dapatkan semua status yang terdefinisi.
     *
     * @return array<string>
     */
    public function allStatuses(): array
    {
        return array_keys(self::TRANSITIONS);
    }
}
