<?php

namespace App\Repositories\Shared;

use App\Models\User;

use App\DTOs\Shared\RegisterDTO;

class UserRepository
{
    /**
     * Create a new standard user.
     */
    public function create(RegisterDTO $dto): User
    {
        return User::create([
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => $dto->password,
            'role'     => 'user',
        ]);
    }

    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Revoke all personal access tokens for the user.
     */
    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Revoke the current personal access token for the user.
     */
    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
