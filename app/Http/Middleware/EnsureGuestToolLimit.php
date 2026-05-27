<?php

namespace App\Http\Middleware;

use App\Services\GuestToolAttemptService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuestToolLimit
{
    public function __construct(
        private GuestToolAttemptService $guestAttempts
    ) {}

    public function handle(Request $request, Closure $next, string $tool): Response
    {
        if ($request->user()) {
            return $next($request);
        }

        if (! $this->guestAttempts->isLocked($request, $tool)) {
            return $next($request);
        }

        $message = 'You have used all 3 free guest attempts for this tool. Please log in or create an account to continue.';

        if ($request->expectsJson()) {
            return response()->json([
                'success'        => false,
                'message'        => $message,
                'login_required' => true,
                'login_url'      => route('login'),
                'register_url'   => route('register'),
            ], 403);
        }

        return redirect()
            ->route('login')
            ->with('error', $message);
    }
}
