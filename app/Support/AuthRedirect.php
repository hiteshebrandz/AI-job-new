<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AuthRedirect
{
    public static function dashboardFor(User $user): RedirectResponse
    {
        return match ($user->role) {
            User::ROLE_USER => redirect()->route('user.dashboard'),
            User::ROLE_HR => redirect()->route('hr.dashboard'),
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            default => redirect()->route('login'),
        };
    }

    public static function profileRouteFor(User $user): string
    {
        return match ($user->role) {
            User::ROLE_HR    => route('hr.profile'),
            User::ROLE_ADMIN => route('admin.profile'),
            default          => route('user.profile'),
        };
    }
}
