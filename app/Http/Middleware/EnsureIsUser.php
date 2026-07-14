<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsUser
{
    use ApiResponser;

    /**
     * Handle an incoming request.
     * Rejects the request with 403 if the authenticated user is an admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->isAdmin()) {
            return $this->forbiddenResponse('Forbidden. This area is for standard users only.');
        }

        return $next($request);
    }
}

