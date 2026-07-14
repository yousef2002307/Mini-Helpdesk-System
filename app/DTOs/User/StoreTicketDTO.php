<?php

namespace App\DTOs\User;

use App\Http\Requests\User\StoreTicketRequest;

readonly class StoreTicketDTO
{
    public function __construct(
        public string $title,
        public string $description,
    ) {}

    /**
     * Create a DTO instance from a StoreTicketRequest.
     */
    public static function fromRequest(StoreTicketRequest $request): self
    {
        return new self(
            title: $request->validated('title'),
            description: $request->validated('description'),
        );
    }

}
