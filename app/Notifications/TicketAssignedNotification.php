<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket #{$this->ticket->ticket_number} di-assign ke Anda")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda telah di-assign untuk menangani ticket berikut:")
            ->line("Judul: {$this->ticket->title}")
            ->line("Prioritas: {$this->ticket->priority?->priority_name}")
            ->action('Buka Ticket', route('agent.tickets.show', $this->ticket, false))
            ->line('Mohon segera ditindaklanjuti.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id'     => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message'       => "Anda di-assign ke ticket #{$this->ticket->ticket_number}.",
            'url'           => route('agent.tickets.show', $this->ticket, false),
        ];
    }
}
