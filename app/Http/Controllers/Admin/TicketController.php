<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\UpdateTicketDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTicketRequest;
use App\Http\Resources\Admin\TicketResource;
use App\Models\Ticket;
use App\Services\Admin\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    /**
     * List all tickets (paginated, filterable by status).
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = $this->ticketService->listAll(
            $request->only(['status', 'per_page'])
        );

        $pagination = [
            'current_page' => $tickets->currentPage(),
            'last_page'    => $tickets->lastPage(),
            'per_page'     => $tickets->perPage(),
            'total'        => $tickets->total(),
        ];

        return $this->successResponse(
            TicketResource::collection($tickets->items()),
            'Tickets retrieved successfully.',
            200,
            $pagination
        );
    }

    /**
     * Show a single ticket with details.
     */
    public function show(int $ticket): JsonResponse
    {
        $ticketModel = $this->ticketService->find($ticket);

        return $this->successResponse(
            new TicketResource($ticketModel),
            'Ticket retrieved successfully.'
        );
    }

    /**
     * Update the status of a ticket.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $updatedTicket = $this->ticketService->updateStatus(
            $ticket,
            UpdateTicketDTO::fromRequest($request)
        );

        return $this->successResponse(
            new TicketResource($updatedTicket),
            'Ticket status updated successfully.'
        );
    }
}
