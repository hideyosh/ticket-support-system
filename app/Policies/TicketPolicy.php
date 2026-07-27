<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        $roleName = $user->role?->role_name;

        if (in_array($roleName, ['admin', 'supervisor'])) {
            return true;
        }

        if ($roleName === 'agent') {
            return $ticket->assigned_to === $user->id;
        }

        if ($roleName === 'customer') {
            return $ticket->created_by === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        $roleName = $user->role?->role_name;

        return in_array($roleName, ['admin', 'customer']);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        $roleName = $user->role?->role_name;
    
        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }
}
