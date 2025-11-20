<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (empty($roles)) {
            return $next($request);
        }

        $roleKey = $user->roleKey();
        if (!in_array($roleKey, array_map('strtolower', $roles))) {
            abort(403);
        }

        return $next($request);
    }
}

