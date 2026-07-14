<?php

namespace App\Repositories\Admin;

use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketRepository
{
    /**
     * Retrieve a paginated list of all tickets with optional status filtering.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginateAll(array $filters = []): LengthAwarePaginator
    {
        return Ticket::with('user')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(min((int) ($filters['per_page'] ?? 10), 50));
    }

    /**
     * Find a ticket by ID.
     */
    public function find(int $ticketId): Ticket
    {
        return Ticket::with(['user', 'replies.user'])->findOrFail($ticketId);
    }

    /**
     * Update the status of a ticket.
     */
    public function updateStatus(Ticket $ticket, string $status): Ticket
    {
        $ticket->update(['status' => $status]);
        return $ticket;
    }
}
