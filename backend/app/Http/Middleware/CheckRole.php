<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Check if the authenticated user has the required role.
     *
     * Usage in routes: ->middleware('role:admin')
     *                  ->middleware('role:admin,manager')
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // Make sure user is authenticated
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Check if user role is in the allowed roles list
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to perform this action.',
            ], 403);
        }

        return $next($request);
    }
}
