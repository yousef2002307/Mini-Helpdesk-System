<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\UpdateTicketRequest;

readonly class UpdateTicketDTO
{
    public function __construct(
        public string $status,
    ) {}

    /**
     * Create a DTO instance from an UpdateTicketRequest.
     */
    public static function fromRequest(UpdateTicketRequest $request): self
    {
        return new self(
            status: $request->validated('status'),
        );
    }
}
