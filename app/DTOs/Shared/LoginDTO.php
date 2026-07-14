<?php

namespace App\DTOs\Shared;

use App\Http\Requests\Shared\LoginRequest;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    /**
     * Create a DTO instance from a LoginRequest.
     */
    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }

    /**
     * Convert DTO attributes to an array.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
