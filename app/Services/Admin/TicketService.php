<?php

namespace App\Services\Admin;

use App\DTOs\Admin\StoreReplyDTO;
use App\DTOs\Admin\UpdateTicketDTO;
use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use App\Repositories\Admin\TicketRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
    ) {}

    /**
     * Retrieve a paginated list of all tickets.
     *
     * @param  array<string, mixed>  $filters
     */
    public function listAll(array $filters): LengthAwarePaginator
    {
        return $this->ticketRepository->paginateAll($filters);
    }

    /**
     * Find a ticket by ID.
     */
    public function find(int $ticketId): Ticket
    {
        return $this->ticketRepository->find($ticketId);
    }

    /**
     * Update the status of a ticket from a DTO.
     */
    public function updateStatus(Ticket $ticket, UpdateTicketDTO $dto): Ticket
    {
        return $this->ticketRepository->updateStatus($ticket, $dto->status);
    }

    /**
     * Add an admin reply to a ticket from a DTO.
     */
    public function addReply(User $user, Ticket $ticket, StoreReplyDTO $dto): Reply
    {
        return $ticket->replies()->create([
            'user_id' => $user->id,
            'body'    => $dto->body,
        ]);
    }
}
