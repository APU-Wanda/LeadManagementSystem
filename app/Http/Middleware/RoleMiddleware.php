<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param ...$roles
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (! auth()->check() || ! in_array(auth()->user()->role, $roles)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
