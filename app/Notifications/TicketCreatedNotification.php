<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketCreatedNotification extends Notification
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
            ->subject("Ticket Baru: {$this->ticket->ticket_number}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Ticket baru telah dibuat oleh {$this->ticket->creator?->name}.")
            ->line("Judul: {$this->ticket->title}")
            ->action('Lihat Ticket', $this->getTicketUrl($notifiable))
            ->line('Mohon ditinjau dan diproses sesuai prosedur.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id'     => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title'         => $this->ticket->title,
            'message'       => "Ticket baru #{$this->ticket->ticket_number} dibuat.",
            'url'           =>  $this->getTicketUrl($notifiable),
        ];
    }

    private function getTicketUrl(object $notifiable): string
    {
        $roleName = $notifiable->role?->role_name;

        return match ($roleName) {
            'supervisor' => route('supervisor.tickets.show', $this->ticket),
            'admin'   => route('admin.tickets.show', $this->ticket),
        };
    }
}
