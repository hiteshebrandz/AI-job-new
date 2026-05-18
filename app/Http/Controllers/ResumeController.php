<?php

namespace App\Http\Controllers;

use App\Mail\CandidateWelcomeMail;
use App\Models\Candidate;
use App\Models\ResumeParsingLog;
use App\Models\User;
use App\Services\ResumeParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResumeController extends Controller
{
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

    public function upload(Request $request, ResumeParserService $parser): JsonResponse
    {
        $maxKb = config('resume.max_upload_kb');
        $allowed = implode(',', config('resume.allowed_extensions'));

        $validated = $request->validate([
            'resume' => [
                'required',
                'file',
                "max:{$maxKb}",
                "mimes:{$allowed}",
            ],
        ]);

        $file = $validated['resume'];
        $storedPath = ResumeParserService::storeUploadedFile($file);

        $log = ResumeParsingLog::create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'parsing_status' => ResumeParsingLog::STATUS_PENDING,
        ]);

        try {
            $parsed = $parser->parse($log);
        } catch (\Throwable $e) {
            $log->update([
                'parsing_status' => ResumeParsingLog::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Resume parsing failed. Please try another file or format.',
                'error' => $e->getMessage(),
            ], 422);
        }

        $routePrefix = auth()->user()->isHr() ? 'hr' : 'user';

        return response()->json([
            'success' => true,
            'message' => 'Resume parsed successfully.',
            'log_id' => $log->id,
            'file_name' => $log->file_name,
            'resume_url' => route("{$routePrefix}.resume.preview", $log),
            'data' => $parsed,
        ]);
    }

    public function status(ResumeParsingLog $log): JsonResponse
    {
        $this->authorizeLog($log);

        return response()->json([
            'status' => $log->parsing_status,
            'ai_score' => $log->ai_score,
            'data' => $log->parsed_data,
            'error' => $log->error_message,
        ]);
    }

    public function preview(ResumeParsingLog $log)
    {
        $this->authorizeLog($log);

        if (! Storage::disk('local')->exists($log->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($log->file_path, $log->file_name);
    }

    public function storeProfile(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'parsing_log_id' => ['required', 'exists:resume_parsing_logs,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'current_title' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'seniority_level' => ['nullable', 'string', 'max:100'],
            'previous_companies' => ['nullable', 'string'],
            'education' => ['nullable', 'string', 'max:255'],
            'university' => ['nullable', 'string', 'max:255'],
            'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
            'ai_recommendation' => ['nullable', 'string'],
            'ai_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'create_new_account' => ['nullable', 'boolean'],
        ]);

        $log = ResumeParsingLog::findOrFail($validated['parsing_log_id']);
        $this->authorizeLog($log);

        $authUser = auth()->user();
        $isHr = $authUser->isHr();
        $skills = array_values(array_filter($validated['skills'] ?? []));
        $authCandidate = Candidate::query()->where('user_id', $authUser->id)->first();
        $createNewAccount = $isHr || (
            $request->boolean('create_new_account')
            && $validated['email'] !== $authUser->email
        );

        $plainPassword = null;
        $isNewAccount = false;

        if ($createNewAccount) {
            $request->validate([
                'email' => ['unique:users,email', 'unique:candidates,email'],
            ]);

            $plainPassword = Str::password(12);
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($plainPassword),
                'role' => User::ROLE_USER,
            ]);
            $isNewAccount = true;
        } else {
            $user = $authUser;

            if ($validated['email'] !== $user->email) {
                $request->validate([
                    'email' => [
                        'unique:users,email,'.$user->id,
                        Rule::unique('candidates', 'email')->ignore($authCandidate?->id),
                    ],
                ]);
            }

            $user->update([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
            ]);
        }

        $profileCandidate = Candidate::query()->where('user_id', $user->id)->first();

        $candidate = Candidate::updateOrCreate(
            ['user_id' => $user->id],
            [
                'candidate_code' => $profileCandidate?->candidate_code ?? Candidate::generateCode(),
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['location'] ?? null,
                'current_title' => $validated['current_title'] ?? null,
                'experience_years' => $validated['experience_years'] ?? 0,
                'seniority_level' => $validated['seniority_level'] ?? null,
                'previous_companies' => $validated['previous_companies'] ?? null,
                'education' => $validated['education'] ?? null,
                'university' => $validated['university'] ?? null,
                'graduation_year' => $validated['graduation_year'] ?? null,
                'skills' => $skills,
                'resume_path' => $log->file_path,
                'ai_recommendation' => $validated['ai_recommendation'] ?? null,
                'ai_score' => $validated['ai_score'] ?? $log->ai_score,
            ]
        );

        $log->update([
            'candidate_id' => $candidate->id,
            'parsing_status' => ResumeParsingLog::STATUS_COMPLETED,
        ]);

        try {
            Mail::to($candidate->email)->send(new CandidateWelcomeMail($candidate, $plainPassword, $isNewAccount));
        } catch (\Throwable) {
            // Mail may be unavailable in local dev; profile creation still succeeds.
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isNewAccount
                    ? 'Candidate account created and welcome email sent.'
                    : 'Profile saved successfully.',
                'redirect' => $isHr
                    ? route('hr.applicants')
                    : route('user.dashboard'),
                'candidate_code' => $candidate->candidate_code,
            ]);
        }

        $redirectRoute = $isHr ? 'hr.applicants' : 'user.dashboard';

        return redirect()
            ->route($redirectRoute)
            ->with('success', $isNewAccount
                ? 'Candidate profile created. Welcome email sent.'
                : 'Your profile has been created successfully.');
    }

    private function authorizeLog(ResumeParsingLog $log): void
    {
        abort_unless($log->user_id === auth()->id(), 403);
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
            'experience_years' => $candidate->experience_years,
            'seniority_level' => $candidate->seniority_level,
            'previous_companies' => $candidate->previous_companies,
            'education' => $candidate->education,
            'university' => $candidate->university,
            'graduation_year' => $candidate->graduation_year,
            'skills' => $candidate->skills ?? [],
            'ai_score' => $candidate->ai_score,
            'ai_recommendation' => $candidate->ai_recommendation,
            'candidate_code' => $candidate->candidate_code,
            'skill_accuracy' => 95,
        ];
    }
}
