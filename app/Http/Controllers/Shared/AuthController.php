<?php

namespace App\Http\Controllers\Shared;

use App\DTOs\Shared\LoginDTO;
use App\DTOs\Shared\RegisterDTO;
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
     *
     * Create a standard user account. On success, an API token is returned.
     *
     * @unauthenticated
     * @group Authentication
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The unique email address. Example: user@example.com
     * @bodyParam password string required The password. Must be at least 8 characters. Example: password
     * @bodyParam password_confirmation string required Password confirmation matching password. Example: password
     *
     * @response 201 {
     *   "status": 201,
     *   "success": true,
     *   "message": "Registration successful.",
     *   "data": {
     *     "user": {
     *       "id": 5,
     *       "name": "John Doe",
     *       "email": "user@example.com",
     *       "role": "user",
     *       "created_at": "2026-07-14T07:33:02.000000Z",
     *       "updated_at": "2026-07-14T07:33:02.000000Z"
     *     },
     *     "token": "1|abcdef123456"
     *   }
     * }
     * @response 422 {
     *   "status": 422,
     *   "success": false,
     *   "message": "The email has already been taken."
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->register(
            RegisterDTO::fromRequest($request)
        );

        return $this->successResponse([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Registration successful.', 201);
    }

    /**
     * Log in and return an API token.
     *
     * Authenticate standard users or administrators.
     *
     * @unauthenticated
     * @group Authentication
     *
     * @bodyParam email string required The user's email address. Example: test@user.com
     * @bodyParam password string required The user's password. Example: 12345678
     *
     * @response 200 {
     *   "status": 200,
     *   "success": true,
     *   "message": "Login successful.",
     *   "data": {
     *     "user": {
     *       "id": 2,
     *       "name": "Normal User",
     *       "email": "test@user.com",
     *       "role": "user",
     *       "created_at": "2026-07-14T07:33:02.000000Z",
     *       "updated_at": "2026-07-14T07:33:02.000000Z"
     *     },
     *     "token": "2|ghijk789012"
     *   }
     * }
     * @response 422 {
     *   "status": 422,
     *   "success": false,
     *   "message": "The provided credentials are incorrect."
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->login(
            LoginDTO::fromRequest($request)
        );

        return $this->successResponse([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login successful.');
    }

    /**
     * Log out the currently authenticated user.
     *
     * Revoke the current access token.
     *
     * @authenticated
     * @group Authentication
     *
     * @response 200 {
     *   "status": 200,
     *   "success": true,
     *   "message": "Logged out successfully."
     * }
     * @response 401 {
     *   "status": 401,
     *   "success": false,
     *   "message": "unauthorized"
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponseWithoutData('Logged out successfully.');
    }

    /**
     * Return the currently authenticated user.
     *
     * Retrieve the user's own profile info.
     *
     * @authenticated
     * @group Authentication
     *
     * @response 200 {
     *   "status": 200,
     *   "success": true,
     *   "message": "Profile retrieved successfully.",
     *   "data": {
     *     "id": 2,
     *     "name": "Normal User",
     *     "email": "test@user.com",
     *     "role": "user",
     *     "created_at": "2026-07-14T07:33:02.000000Z",
     *     "updated_at": "2026-07-14T07:33:02.000000Z"
     *   }
     * }
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'Profile retrieved successfully.'
        );
    }
}
