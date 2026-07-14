<?php

namespace App\Services\User;

use App\DTOs\User\StoreReplyDTO;
use App\DTOs\User\StoreTicketDTO;
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
     * Create a new ticket for the user from a DTO.
     */
    public function create(User $user, StoreTicketDTO $dto): Ticket
    {
        return $this->ticketRepository->create($user, $dto);
    }

    /**
     * Add a reply to a ticket on behalf of the user from a DTO.
     */
    public function addReply(User $user, Ticket $ticket, StoreReplyDTO $dto): Reply
    {
        return $ticket->replies()->create([
            'user_id' => $user->id,
            'body'    => $dto->body,
        ]);
    }
}
