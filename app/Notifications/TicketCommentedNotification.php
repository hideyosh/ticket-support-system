<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class TicketCommentedNotification extends Notification
{
    use Queueable;

    public function __construct(public Comment $comment)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->comment->ticket;

        return (new MailMessage)
            ->subject("Komentar baru di Ticket #{$ticket->ticket_number}")
            ->greeting("Halo {$notifiable->name},")
            ->line("{$this->comment->user->name} menambahkan komentar pada ticket \"{$ticket->title}\":")
            ->line('"' . Str::limit($this->comment->body, 150) . '"')
            ->action('Lihat Ticket', $this->getTicketUrl($notifiable))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        $ticket = $this->comment->ticket;

        return [
            'ticket_id'  => $ticket->id,
            'comment_id' => $this->comment->id,
            'message'    => "{$this->comment->user->name} menambahkan komentar baru pada ticket #{$ticket->ticket_number}.",
            'url'        => $this->getTicketUrl($notifiable),
        ];
    }

    /**
     * Route detail ticket beda per role -> sesuaikan URL berdasarkan role penerima.
     */
    private function get(object $notifiable): string
    {
        $ticket = $this->comment->ticket;
        $roleName = $notifiable->role?->role_name;

        return match ($roleName) {
            'customer' => route('customer.tickets.show', $ticket),
            'agent'    => route('agent.tickets.show', $ticket),
            'supervisor' => route('supervisor.tickets.show', $ticket),
            'admin'    => route('admin.tickets.show', $ticket),
        };
    }
}
