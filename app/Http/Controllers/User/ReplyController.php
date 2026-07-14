<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreReplyRequest;
use App\Http\Resources\User\ReplyResource;
use App\Models\Ticket;
use App\Services\User\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReplyController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    /**
     * Add a reply to one of the authenticated user's tickets.
     */
    public function store(StoreReplyRequest $request, Ticket $ticket): JsonResponse
    {
        Gate::authorize('reply', $ticket);

        $reply = $this->ticketService->addReply(
            $request->user(),
            $ticket,
            $request->validated('body')
        );

        $reply->load('user');

        return $this->successResponse(
            new ReplyResource($reply),
            'Reply added successfully.',
            201
        );
    }
}
