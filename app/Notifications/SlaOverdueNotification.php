<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SlaOverdueNotification extends Notification
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
            ->subject("[SLA Overdue] Ticket #{$this->ticket->ticket_number}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Ticket \"{$this->ticket->title}\" telah melewati batas waktu SLA yang ditentukan.")
            ->line("Batas waktu: {$this->ticket->due_date?->format('d M Y, H:i')}")
            ->action('Tinjau Ticket', route('admin.tickets.show', $this->ticket))
            ->line('Mohon segera ditindaklanjuti untuk menghindari keterlambatan lebih lanjut.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message'   => "Ticket #{$this->ticket->ticket_number} melewati batas SLA.",
            'url'       => route('admin.tickets.show', $this->ticket),
        ];
    }
}
