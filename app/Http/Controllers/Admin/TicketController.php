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
     * List all tickets.
     *
     * Retrieve a paginated, filterable list of all support tickets across the entire system.
     *
     * @authenticated
     * @group Admin Tickets
     *
     * @queryParam status string Filter tickets by status. No-op if omitted. Example: in_progress
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
     *       "updated_at": "2026-07-14T07:33:02.000000Z",
     *       "user": {
     *         "id": 2,
     *         "name": "Normal User",
     *         "email": "test@user.com",
     *         "role": "user"
     *       }
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
     * Show a single ticket.
     *
     * Retrieve details of a specific ticket across the system, including user details and replies.
     *
     * @authenticated
     * @group Admin Tickets
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
     *     "user": {
     *       "id": 2,
     *       "name": "Normal User",
     *       "email": "test@user.com",
     *       "role": "user"
     *     },
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
    public function show(int $ticket): JsonResponse
    {
        $ticketModel = $this->ticketService->find($ticket);

        return $this->successResponse(
            new TicketResource($ticketModel),
            'Ticket retrieved successfully.'
        );
    }

    /**
     * Update ticket status.
     *
     * Update the status of any support ticket in the system.
     *
     * @authenticated
     * @group Admin Tickets
     *
     * @urlParam ticket integer required The ID of the ticket. Example: 1
     * @bodyParam status string required The new status. Must be one of: open, in_progress, closed. Example: in_progress
     *
     * @response 200 {
     *   "status": 200,
     *   "success": true,
     *   "message": "Ticket status updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "title": "Database connection issue",
     *     "description": "I cannot connect to the database from the client application.",
     *     "status": "in_progress",
     *     "created_at": "2026-07-14T07:33:02.000000Z",
     *     "updated_at": "2026-07-14T07:34:30.000000Z"
     *   }
     * }
     * @response 422 {
     *   "status": 422,
     *   "success": false,
     *   "message": "The selected status is invalid."
     * }
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
