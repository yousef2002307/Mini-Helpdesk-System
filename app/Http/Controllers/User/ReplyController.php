<?php

namespace App\Http\Controllers\User;

use App\DTOs\User\StoreReplyDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreReplyRequest;
use App\Http\Resources\User\ReplyResource;
use App\Models\Ticket;
use App\Services\User\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ReplyController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    /**
     * Add a reply to a ticket.
     *
     * Add a response or message thread update to one of the authenticated user's own tickets.
     *
     * @authenticated
     * @group User Tickets
     *
     * @urlParam ticket integer required The ID of the ticket. Example: 1
     * @bodyParam body string required The content of the reply message. Example: I checked, the host config was wrong. Fixed now!
     *
     * @response 201 {
     *   "status": 201,
     *   "success": true,
     *   "message": "Reply added successfully.",
     *   "data": {
     *     "id": 11,
     *     "ticket_id": 1,
     *     "user_id": 2,
     *     "body": "I checked, the host config was wrong. Fixed now!",
     *     "created_at": "2026-07-14T07:35:00.000000Z",
     *     "updated_at": "2026-07-14T07:35:00.000000Z",
     *     "author": {
     *       "id": 2,
     *       "name": "Normal User",
     *       "email": "test@user.com",
     *       "role": "user"
     *     }
     *   }
     * }
     * @response 403 {
     *   "status": 403,
     *   "success": false,
     *   "message": "This action is unauthorized."
     * }
     */
    public function store(StoreReplyRequest $request, Ticket $ticket): JsonResponse
    {
        Gate::authorize('reply', $ticket);

        $reply = $this->ticketService->addReply(
            $request->user(),
            $ticket,
            StoreReplyDTO::fromRequest($request)
        );

        $reply->load('user');

        return $this->successResponse(
            new ReplyResource($reply),
            'Reply added successfully.',
            201
        );
    }
}
