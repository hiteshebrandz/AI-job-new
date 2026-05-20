<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ResumeUploadController as ApiResumeUploadController;
use App\Http\Requests\ResumeUploadRequest;
use App\Http\Requests\StoreCandidateProfileRequest;
use App\Models\Candidate;
use App\Models\ResumeParsingLog;
use App\Services\ResumeParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResumeController extends Controller
{
    public function __construct(
        private ApiResumeUploadController $apiResume
    ) {}

    public function show(): View
    {
        $user = auth()->user();
        $routePrefix = $user->isHr() ? 'hr' : 'user';

        $existingCandidate = Candidate::query()
            ->where('user_id', $user->id)
            ->first();

        return view('pages.resume_upload_parsing', [
            'activeNav' => 'resume',
            'resumeRoutePrefix' => $routePrefix,
            'recentLogs' => ResumeParsingLog::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(8)
                ->get(),
            'existingCandidate' => $existingCandidate,
            'prefillData' => self::candidatePrefillPayload($existingCandidate),
        ]);
    }

    public function upload(ResumeUploadRequest $request, ResumeParserService $parser): JsonResponse
    {
        return $this->apiResume->upload($request, $parser);
    }

    public function status(Request $request, ResumeParsingLog $log): JsonResponse
    {
        return $this->apiResume->status($request, $log);
    }

    public function preview(ResumeParsingLog $log)
    {
        abort_unless($log->user_id === auth()->id(), 403);

        $disk = config('resume.disk', 'local');

        if (! Storage::disk($disk)->exists($log->file_path)) {
            abort(404);
        }

        return Storage::disk($disk)->response($log->file_path, $log->file_name);
    }

    public function storeProfile(StoreCandidateProfileRequest $request): JsonResponse|RedirectResponse
    {
        $response = $this->apiResume->storeProfile(
            $request,
            app(\App\Actions\CreateCandidateProfileAction::class)
        );

        if ($request->expectsJson() || $request->wantsJson()) {
            return $response;
        }

        $data = $response->getData(true);

        $redirectRoute = auth()->user()->isHr() ? 'hr.applicants' : 'user.dashboard';

        return redirect()
            ->route($redirectRoute)
            ->with('success', $data['message'] ?? 'Profile saved.');
    }

    private static function candidatePrefillPayload(?Candidate $candidate): ?array
    {
        if (! $candidate) {
            return null;
        }

        return [
            'name' => $candidate->full_name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'location' => $candidate->location,
            'title' => $candidate->current_title,
            'current_title' => $candidate->current_title,
            'experience_years' => $candidate->experience_years,
            'seniority_level' => $candidate->seniority_level,
            'previous_companies' => $candidate->previous_companies,
            'education' => $candidate->education,
            'university' => $candidate->university,
            'graduation_year' => $candidate->graduation_year,
            'skills' => $candidate->skills ?? [],
            'summary' => $candidate->summary ?? $candidate->ai_recommendation,
            'ai_score' => $candidate->ai_score,
            'ai_recommendation' => $candidate->ai_recommendation,
            'candidate_code' => $candidate->candidate_code,
            'skill_accuracy' => 95,
        ];
    }
}
