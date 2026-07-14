<?php

namespace App\Http\Controllers\User;

use App\DTOs\User\StoreTicketDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreTicketRequest;
use App\Http\Resources\User\TicketResource;
use App\Services\User\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    /**
     * List the authenticated user's tickets.
     *
     * Retrieve a paginated, filterable list of tickets owned by the current user.
     *
     * @authenticated
     * @group User Tickets
     *
     * @queryParam status string Filter tickets by status. No-op if omitted. Example: open
     * @queryParam per_page integer Number of items per page (max 50, default 10). Example: 10
     *
     * @response 200 {
     *   "status": 200,
     *   "success": true,
     *   "message": "Tickets retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Database connection issue",
     *       "description": "I cannot connect to the database from the client application.",
     *       "status": "open",
     *       "created_at": "2026-07-14T07:33:02.000000Z",
     *       "updated_at": "2026-07-14T07:33:02.000000Z"
     *     }
     *   ],
     *   "pagination": {
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 10,
     *     "total": 1
     *   }
     * }
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
     * Create a new ticket.
     *
     * Create a new support ticket for the authenticated user.
     *
     * @authenticated
     * @group User Tickets
     *
     * @bodyParam title string required Short summary of the issue. Max 255 chars. Example: Login is slow
     * @bodyParam description string required Detailed explanation of the problem. Example: Every time I click log in, it takes 15 seconds to load.
     *
     * @response 201 {
     *   "status": 201,
     *   "success": true,
     *   "message": "Ticket created successfully.",
     *   "data": {
     *     "id": 2,
     *     "title": "Login is slow",
     *     "description": "Every time I click log in, it takes 15 seconds to load.",
     *     "status": "open",
     *     "created_at": "2026-07-14T07:33:02.000000Z",
     *     "updated_at": "2026-07-14T07:33:02.000000Z"
     *   }
     * }
     * @response 422 {
     *   "status": 422,
     *   "success": false,
     *   "message": "The title field is required."
     * }
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->create(
            $request->user(),
            StoreTicketDTO::fromRequest($request)
        );

        return $this->successResponse(
            new TicketResource($ticket),
            'Ticket created successfully.',
            201
        );
    }

    /**
     * Show a single ticket.
     *
     * Retrieve a specific ticket belonging to the user, including all its reply history.
     *
     * @authenticated
     * @group User Tickets
     *
     * @urlParam ticket integer required The ID of the ticket. Example: 1
     *
     * @response 200 {
     *   "status": 200,
     *   "success": true,
     *   "message": "Ticket retrieved successfully.",
     *   "data": {
     *     "id": 1,
     *     "title": "Database connection issue",
     *     "description": "I cannot connect to the database from the client application.",
     *     "status": "open",
     *     "created_at": "2026-07-14T07:33:02.000000Z",
     *     "updated_at": "2026-07-14T07:33:02.000000Z",
     *     "replies": [
     *       {
     *         "id": 10,
     *         "ticket_id": 1,
     *         "user_id": 2,
     *         "body": "Can you check your credentials?",
     *         "created_at": "2026-07-14T07:34:00.000000Z",
     *         "updated_at": "2026-07-14T07:34:00.000000Z",
     *         "author": {
     *           "id": 2,
     *           "name": "Normal User",
     *           "email": "test@user.com",
     *           "role": "user"
     *         }
     *       }
     *     ]
     *   }
     * }
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
