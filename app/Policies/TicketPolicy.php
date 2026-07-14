<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Admins can view any ticket listing. Regular users see their own via scoping.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * A user can view a ticket if they are the owner or an admin.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $user->id === $ticket->user_id;
    }

    /**
     * Any authenticated user can create a ticket.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * A user can reply to a ticket if they are the owner or an admin.
     */
    public function reply(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $user->id === $ticket->user_id;
    }

    /**
     * Only admins can update (change status of) a ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}
