<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\StoreReplyRequest;

readonly class StoreReplyDTO
{
    public function __construct(
        public string $body,
    ) {}

    /**
     * Create a DTO instance from a StoreReplyRequest (Admin context).
     */
    public static function fromRequest(StoreReplyRequest $request): self
    {
        return new self(
            body: $request->validated('body'),
        );
    }
}
