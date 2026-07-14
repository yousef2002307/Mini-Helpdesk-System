<?php

namespace App\Repositories\User;

use App\DTOs\User\StoreTicketDTO;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketRepository
{
    /**
     * Retrieve a paginated list of tickets belonging to the given user,
     * with optional status filtering.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginateForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        return $user->tickets()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(min((int) ($filters['per_page'] ?? 10), 50));
    }

    /**
     * Find a ticket by ID that belongs to the given user.
     */
    public function findForUser(User $user, int $ticketId): Ticket
    {
        return $user->tickets()->with('replies.user')->findOrFail($ticketId);
    }

    /**
     * Create a new ticket for the given user.
     */
    public function create(User $user, StoreTicketDTO $dto): Ticket
    {
        return $user->tickets()->create([
            'title'       => $dto->title,
            'description' => $dto->description,
        ]);
    }
}
