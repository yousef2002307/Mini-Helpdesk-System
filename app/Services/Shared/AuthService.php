<?php

namespace App\Services\Shared;

use App\Models\User;
use App\Repositories\Shared\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * Register a new user and return the user with an API token.
     *
     * @param  array<string, mixed>  $data
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = $this->userRepository->create($data);

        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    /**
     * Authenticate a user and return an API token.
     *
     * @param  array<string, mixed>  $credentials
     * @return array{user: User, token: string}
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens for security (single-session)
        $this->userRepository->revokeAllTokens($user);

        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    /**
     * Revoke the current user's token (logout).
     */
    public function logout(User $user): void
    {
        $this->userRepository->revokeCurrentToken($user);
    }
}

