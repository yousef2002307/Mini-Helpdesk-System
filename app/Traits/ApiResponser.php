<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * success response method.
     */
    protected function successResponse($data, string $message = 'Success', int $statusCode = 200, ?array $pagination = null): JsonResponse
    {
        $response = [
            'status' => $statusCode,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if ($pagination !== null) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $statusCode);
    }

    protected function successResponseWithoutData(string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => $statusCode,
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * error response method.
     */
    protected function errorResponse(string $message, int $statusCode, array $errors = []): JsonResponse
    {
        $response = [
            'status' => $statusCode,
            'success' => false,
            'message' => $message,
        ];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * unauthorized response method.
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * forbidden response method.
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * not found response method.
     */
    protected function notFoundResponse(string $message = 'Not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * method not allowed response method.
     */
    protected function methodNotAllowedResponse(string $message = 'Method not allowed'): JsonResponse
    {
        return $this->errorResponse($message, 405);
    }

    /**
     * conflict response method.
     */
    protected function conflictResponse(string $message = 'Conflict'): JsonResponse
    {
        return $this->errorResponse($message, 409);
    }

    /**
     * bad request response method.
     */
    protected function badRequestResponse(string $message = 'Bad request'): JsonResponse
    {
        return $this->errorResponse($message, 400);
    }

    /**
     * request entity too large response method.
     */
    protected function requestEntityTooLargeResponse(string $message = 'Request entity too large'): JsonResponse
    {
        return $this->errorResponse($message, 413);
    }

    /**
     * unsupported media type response method.
     */
    protected function unsupportedMediaTypeResponse(string $message = 'Unsupported media type'): JsonResponse
    {
        return $this->errorResponse($message, 415);
    }

    /**
     * server error response method.
     */
    protected function serverErrorResponse(string $message = 'Server error'): JsonResponse
    {
        return $this->errorResponse($message, 500);
    }

    /**
     * service unavailable response method.
     */
    protected function serviceUnavailableResponse(string $message = 'Service unavailable'): JsonResponse
    {
        return $this->errorResponse($message, 503);
    }
}
