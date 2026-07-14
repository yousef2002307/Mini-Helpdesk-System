<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\LoginRequest;
use App\Http\Requests\Shared\RegisterRequest;
use App\Http\Resources\Shared\UserResource;
use App\Services\Shared\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->register(
            $request->validated()
        );

        return $this->successResponse([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Registration successful.', 201);
    }

    /**
     * Log in and return an API token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->login(
            $request->validated()
        );

        return $this->successResponse([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login successful.');
    }

    /**
     * Log out
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponseWithoutData('Logged out successfully.');
    }

    /**
     * Return the currently authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'Profile retrieved successfully.'
        );
    }
}
