<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppliedJobsController extends Controller
{
    public function index(Request $request): View
    {
        $applications = JobApplication::query()
            ->where('user_id', $request->user()->id)
            ->with(['job.hr'])
            ->latest('applied_at')
            ->paginate(10);

        return view('pages.user_applied_jobs', [
            'activeNav' => 'jobs',
            'applications' => $applications,
        ]);
    }
}
