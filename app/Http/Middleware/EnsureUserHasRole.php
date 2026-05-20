<?php

namespace App\Http\Middleware;

use App\Support\AuthRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. You do not have the required role.',
                ], 403);
            }

            return AuthRedirect::dashboardFor($request->user());
        }

        return $next($request);
    }
}
