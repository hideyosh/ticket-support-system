<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markAsRead(DatabaseNotification $notification): RedirectResponse
    {
        abort_if(
            $notification->notifiable_id !== auth()->id()
                || $notification->notifiable_type !== auth()->user()::class,
            403
        );

        $notification->markAsRead();

        return $notification->data['url'] ?? null
            ? redirect($notification->data['url'])
            : redirect()->route(auth()->user()->dashboardRoute());
    }

    /**
     * Tandai SEMUA notifikasi milik user yang login sebagai sudah dibaca.
     * Tetap pakai POST karena ini aksi massal, bukan navigasi.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}
