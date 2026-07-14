<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\StoreReplyDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReplyRequest;
use App\Http\Resources\Admin\ReplyResource;
use App\Models\Ticket;
use App\Services\Admin\TicketService;
use Illuminate\Http\JsonResponse;

class ReplyController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    /**
     * Add a reply to any ticket.
     *
     * Add an administrative response or answer to any support ticket in the system.
     *
     * @authenticated
     * @group Admin Tickets
     *
     * @urlParam ticket integer required The ID of the ticket. Example: 1
     * @bodyParam body string required The content of the admin reply message. Example: We have verified the database is now up and reachable. Please check on your end.
     *
     * @response 201 {
     *   "status": 201,
     *   "success": true,
     *   "message": "Admin reply added successfully.",
     *   "data": {
     *     "id": 12,
     *     "ticket_id": 1,
     *     "user_id": 1,
     *     "body": "We have verified the database is now up and reachable. Please check on your end.",
     *     "created_at": "2026-07-14T07:36:00.000000Z",
     *     "updated_at": "2026-07-14T07:36:00.000000Z",
     *     "author": {
     *       "id": 1,
     *       "name": "Admin User",
     *       "email": "admin@example.com",
     *       "role": "admin"
     *     }
     *   }
     * }
     */
    public function store(StoreReplyRequest $request, Ticket $ticket): JsonResponse
    {
        $reply = $this->ticketService->addReply(
            $request->user(),
            $ticket,
            StoreReplyDTO::fromRequest($request)
        );

        $reply->load('user');

        return $this->successResponse(
            new ReplyResource($reply),
            'Admin reply added successfully.',
            201
        );
    }
}
