<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['api', 'auth:sanctum', \App\Http\Middleware\EnsureIsUser::class])
                ->prefix('api/user')
                ->group(base_path('routes/user.php'));

            Route::middleware(['api', 'auth:sanctum', \App\Http\Middleware\EnsureIsAdmin::class])
                ->prefix('api/admin')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle ValidationException to return simple message without errors array
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Get the first error message
                $errors     = $e->errors();
                $firstError = reset($errors);
                $message    = is_array($firstError) ? reset($firstError) : $firstError;

                return response()->json([
                    'status'  => 422,
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
        });

        // Handle ThrottleRequestsException (rate limiting)
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 429,
                    'success' => false,
                    'message' => "too many requests",
                ], 429);
            }
        });

        // Handle AuthenticationException (401 Unauthorized)
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 401,
                    'success' => false,
                    'message' => "unauthorized",
                ], 401);
            }
        });

        // Handle NotFound (404 Not Found)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'message' => 'Route not found.',
                ], 404);
            }
        });

        // Handle Method Not Allowed (405 Method Not Allowed)
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 405,
                    'success' => false,
                    'message' => 'Method not allowed.',
                ], 405);
            }
        });

        // Handle Access Denied (403 Forbidden)
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 403,
                    'success' => false,
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        // Handle Model/Record Not Found (404 Not Found)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 404,
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        // Database errors for API: never expose raw SQL to clients
        $exceptions->render(function (QueryException $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            $sqlState = $e->errorInfo[0] ?? '';

            if ($sqlState === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                $isUnifiedAuthRoute = $request->is('api/auth/*');
                $message = $isUnifiedAuthRoute
                    ? __('unified_auth.duplicate_identity')
                    : __('unified_auth.duplicate_record');

                Log::error('database.constraint_violation', [
                    'sqlstate' => $sqlState,
                    'error_code' => $e->errorInfo[1] ?? null,
                    'message' => $e->getMessage(),
                    'url' => $request->fullUrl(),
                ]);

                return response()->json([
                    'status'  => 409,
                    'success' => false,
                    'message' => config('app.debug') ? $e->getMessage() : $message,
                ], 409);
            }

            Log::error('database.query_exception', [
                'sqlstate' => $sqlState,
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json([
                'status'  => 500,
                'success' => false,
                'message' => $e->getMessage() !== '' ? $e->getMessage() : '500 database error',
            ], 500);
        });

        // Fallback for general server errors (500 Internal Server Error)
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($e instanceof HttpExceptionInterface) {
                    $status  = $e->getStatusCode();
                    $message = $e->getMessage() !== '' ? $e->getMessage() : 'Error';

                    return response()->json([
                        'status'  => $status,
                        'success' => false,
                        'message' => $message,
                    ], $status);
                }

                Log::error('api.unhandled_exception', ['exception' => $e]);

                $message = $e->getMessage() !== '' ? $e->getMessage() : __('unified_auth.unexpected_error');

                return response()->json([
                    'status'  => 500,
                    'success' => false,
                    'message' => $message,
                ], 500);
            }
        });
    })->create();

