<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function user(): View
    {
        return view('dashboards.user', [
            'user' => auth()->user(),
        ]);
    }

    public function hr(): View
    {
        return view('dashboards.hr', [
            'user' => auth()->user(),
            'jobs' => Job::query()
                ->where('hr_id', auth()->id())
                ->latest()
                ->get(),
        ]);
    }

    public function admin(): View
    {
        return view('dashboards.admin', [
            'totalUsers' => User::where('role', User::ROLE_USER)->count(),
            'totalHr' => User::where('role', User::ROLE_HR)->count(),
            'users' => User::where('role', User::ROLE_USER)->orderBy('name')->get(),
            'hrs' => User::where('role', User::ROLE_HR)->orderBy('name')->get(),
        ]);
    }
}
