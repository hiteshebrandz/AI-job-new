<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessResumeAnalyticsJob;
use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserResumeController extends Controller
{
    public function uploadResume(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'resume' => ['required', 'file', 'mimes:pdf,docx', 'max:5120'],
        ]);

        $file      = $request->file('resume');
        $extension = strtolower($file->getClientOriginalExtension());
        $directory = 'resumes/' . date('Y/m');
        $fileName  = Str::uuid() . '.' . $extension;

        $filePath = $file->storeAs($directory, $fileName, 'public');

        $resume = Resume::create([
            'user_id'   => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $extension,
            'status'    => Resume::STATUS_UPLOADED,
        ]);

        ProcessResumeAnalyticsJob::dispatch($resume->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Resume uploaded. Analytics processing has started.',
                'resume_id' => $resume->id,
                'status'    => 'processing',
            ]);
        }

        return redirect()->route('user.resume.analytics');
    }

    public function analytics(Request $request): JsonResponse
    {
        $resume = Resume::where('user_id', $request->user()->id)
            ->latest()
            ->first();

        if (! $resume) {
            return response()->json([
                'success' => true,
                'status'  => 'none',
                'message' => 'No resume uploaded yet.',
            ]);
        }

        if ($resume->isProcessing()) {
            return response()->json([
                'success'   => true,
                'status'    => 'processing',
                'resume_id' => $resume->id,
                'message'   => 'Analytics are being processed. Please check back shortly.',
            ]);
        }

        if ($resume->isFailed()) {
            return response()->json([
                'success'   => false,
                'status'    => 'failed',
                'resume_id' => $resume->id,
                'error'     => $resume->error_message ?? 'Analytics processing failed.',
            ]);
        }

        $analytics = $resume->analytics;

        if (! $analytics) {
            return response()->json([
                'success'   => false,
                'status'    => 'failed',
                'resume_id' => $resume->id,
                'error'     => 'Analytics data is not available.',
            ]);
        }

        return response()->json([
            'success'   => true,
            'status'    => 'completed',
            'resume_id' => $resume->id,
            'file_name' => $resume->file_name,
            'data'      => [
                'candidate_name'         => $analytics->candidate_name,
                'email'                  => $analytics->email,
                'phone'                  => $analytics->phone,
                'current_role'           => $analytics->current_role,
                'total_experience_years' => $analytics->total_experience_years,
                'ai_score'               => $analytics->ai_score,
                'top_match_percentage'   => $analytics->top_match_percentage,
                'application_count'      => $analytics->application_count,
                'skill_count'            => $analytics->skill_count,
                'skills'                 => $analytics->skills ?? [],
                'missing_skills'         => $analytics->missing_skills ?? [],
                'skill_gap_analysis'     => $analytics->skill_gap_analysis,
                'career_growth'          => $analytics->career_growth ?? [],
                'education'              => $analytics->education ?? [],
                'nlp_analysis'           => $analytics->nlp_analysis,
                'soft_skills'            => $analytics->soft_skills ?? [],
                'ai_profile_summary'     => $analytics->ai_profile_summary,
                'resume_improvements'    => $analytics->resume_improvements ?? [],
                'job_recommendations'    => $analytics->job_recommendations ?? [],
                'strengths'              => $analytics->strengths ?? [],
                'weaknesses'             => $analytics->weaknesses ?? [],
            ],
        ]);
    }

    public function reAnalyze(Request $request, int $resumeId): JsonResponse|RedirectResponse
    {
        $resume = Resume::where('id', $resumeId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $resume->update([
            'status'        => Resume::STATUS_UPLOADED,
            'error_message' => null,
        ]);

        ProcessResumeAnalyticsJob::dispatch($resume->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'status'    => 'processing',
                'resume_id' => $resume->id,
                'message'   => 'Re-analysis started.',
            ]);
        }

        return redirect()->route('user.resume.analytics');
    }
}
