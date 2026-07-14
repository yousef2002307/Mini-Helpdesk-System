<?php

namespace App\Http\Controllers\Admin;

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
     * Add a reply to any ticket by admin.
     */
    public function store(StoreReplyRequest $request, Ticket $ticket): JsonResponse
    {
        $reply = $this->ticketService->addReply(
            $request->user(),
            $ticket,
            $request->validated('body')
        );

        $reply->load('user');

        return $this->successResponse(
            new ReplyResource($reply),
            'Admin reply added successfully.',
            201
        );
    }
}
