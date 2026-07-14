<?php

namespace App\DTOs\Shared;

use App\Http\Requests\Shared\RegisterRequest;

readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    /**
     * Create a DTO instance from a RegisterRequest.
     */
    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
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
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
