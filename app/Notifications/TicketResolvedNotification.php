<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketResolvedNotification extends Notification
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
            ->subject("Ticket #{$this->ticket->ticket_number} telah diselesaikan")
            ->greeting("Halo {$notifiable->name},")
            ->line("Ticket Anda \"{$this->ticket->title}\" telah ditandai selesai oleh tim kami.")
            ->action('Lihat Detail', route('customer.tickets.show', $this->ticket))
            ->line('Jika masalah belum sepenuhnya teratasi, Anda bisa membuka kembali ticket ini.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message'   => "Ticket #{$this->ticket->ticket_number} Anda telah diselesaikan.",
            'url'       => route('customer.tickets.show', $this->ticket),
        ];
    }
}
