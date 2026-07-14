<?php

namespace App\Services\User;

use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use App\Repositories\User\TicketRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
    ) {}

    /**
     * Return a paginated, filtered list of tickets owned by the user.
     *
     * @param  array<string, mixed>  $filters
     */
    public function listForUser(User $user, array $filters): LengthAwarePaginator
    {
        return $this->ticketRepository->paginateForUser($user, $filters);
    }

    /**
     * Return a single ticket owned by the user (with replies).
     */
    public function findForUser(User $user, int $ticketId): Ticket
    {
        return $this->ticketRepository->findForUser($user, $ticketId);
    }

    /**
     * Create a new ticket for the user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, array $data): Ticket
    {
        return $this->ticketRepository->create($user, $data);
    }

    /**
     * Add a reply to a ticket on behalf of the user.
     */
    public function addReply(User $user, Ticket $ticket, string $body): Reply
    {
        return $ticket->replies()->create([
            'user_id' => $user->id,
            'body'    => $body,
        ]);
    }
}
