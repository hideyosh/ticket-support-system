<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketEscalatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[Eskalasi] Ticket #{$this->ticket->ticket_number}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Ticket \"{$this->ticket->title}\" telah di-eskalasi dan memerlukan perhatian segera.")
            ->line("Prioritas: {$this->ticket->priority?->priority_name}")
            ->action('Tinjau Ticket', route('admin.tickets.show', $this->ticket))
            ->line('Mohon segera ditindaklanjuti.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message'   => "Ticket #{$this->ticket->ticket_number} di-eskalasi dan butuh perhatian segera.",
            'url'       => route('admin.tickets.show', $this->ticket),
        ];
    }
}
