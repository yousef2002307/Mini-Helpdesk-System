<?php

namespace App\DTOs\User;

use App\Http\Requests\User\StoreReplyRequest;

readonly class StoreReplyDTO
{
    public function __construct(
        public string $body,
    ) {}

    /**
     * Create a DTO instance from a StoreReplyRequest (User context).
     */
    public static function fromRequest(StoreReplyRequest $request): self
    {
        return new self(
            body: $request->validated('body'),
        );
    }
}
