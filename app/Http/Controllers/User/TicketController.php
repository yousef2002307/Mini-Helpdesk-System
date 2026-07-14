<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreTicketRequest;
use App\Http\Resources\User\TicketResource;
use App\Services\User\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    /**
     * List the authenticated user's tickets (paginated, filterable by status).
     *
     * Query params:
     *   - status: open | in_progress | closed
     *   - per_page: int (max 50, default 10)
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = $this->ticketService->listForUser(
            $request->user(),
            $request->only('status', 'per_page')
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
     * Create a new ticket for the authenticated user.
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->create($request->user(), $request->validated());

        return $this->successResponse(
            new TicketResource($ticket),
            'Ticket created successfully.',
            201
        );
    }

    /**
     * Show a single ticket belonging to the authenticated user.
     */
    public function show(Request $request, int $ticket): JsonResponse
    {
        $ticket = $this->ticketService->findForUser($request->user(), $ticket);

        return $this->successResponse(
            new TicketResource($ticket),
            'Ticket retrieved successfully.'
        );
    }
}
