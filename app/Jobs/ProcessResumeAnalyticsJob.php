<?php

namespace App\Jobs;

use App\Models\Resume;
use App\Models\ResumeAnalytics;
use App\Services\PythonResumeAnalyzerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessResumeAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 130;

    public function __construct(public readonly int $resumeId)
    {
        $this->onQueue('resumes');
    }

    public function handle(PythonResumeAnalyzerService $analyzer): void
    {
        $resume = Resume::find($this->resumeId);

        if (! $resume) {
            return;
        }

        $resume->update(['status' => Resume::STATUS_PROCESSING]);

        try {
            $result = $analyzer->analyze($resume);
        } catch (Throwable $e) {
            $this->markFailed($resume, $e->getMessage());

            // Do not rethrow: with QUEUE_CONNECTION=sync the exception would break the upload request.
            return;
        }

        $extractedText = $result['extracted_text'] ?? '';
        $data          = $result['data'] ?? [];

        // Persist extracted text and mark completed
        $resume->update([
            'extracted_text' => $extractedText,
            'status'         => Resume::STATUS_COMPLETED,
            'error_message'  => null,
        ]);

        // Build analytics payload
        $analyticsPayload = [
            'user_id'                 => $resume->user_id,
            'resume_id'               => $resume->id,
            'candidate_name'          => $data['candidate_name'] ?? null,
            'email'                   => $data['email'] ?? null,
            'phone'                   => $data['phone'] ?? null,
            'current_role'            => $data['current_role'] ?? null,
            'total_experience_years'  => isset($data['total_experience_years'])
                ? (float) $data['total_experience_years']
                : null,
            'ai_score'                => (int) ($data['ai_score'] ?? 0),
            'top_match_percentage'    => (int) ($data['top_match_percentage'] ?? 0),
            'application_count'       => (int) ($data['application_count'] ?? 0),
            'skill_count'             => (int) ($data['skill_count'] ?? count($data['skills'] ?? [])),
            'skills'                  => $data['skills'] ?? [],
            'missing_skills'          => $data['missing_skills'] ?? [],
            'skill_gap_analysis'      => $data['skill_gap_analysis'] ?? null,
            'career_growth'           => $data['career_growth'] ?? [],
            'education'               => $data['education'] ?? [],
            'nlp_analysis'            => $data['nlp_analysis'] ?? null,
            'soft_skills'             => $data['soft_skills'] ?? [],
            'ai_profile_summary'      => $data['ai_profile_summary'] ?? null,
            'resume_improvements'     => $data['resume_improvements'] ?? [],
            'job_recommendations'     => $data['job_recommendations'] ?? [],
            'strengths'               => $data['strengths'] ?? [],
            'weaknesses'              => $data['weaknesses'] ?? [],
            'raw_ai_response'         => $data,
        ];

        ResumeAnalytics::updateOrCreate(
            ['resume_id' => $resume->id],
            $analyticsPayload
        );

        $resume->loadMissing('user.candidate');
        $this->syncCandidateFromAnalytics($resume, $data);
    }

    /**
     * Mirror resume analytics into candidate profile for job matching.
     *
     * @param  array<string, mixed>  $data
     */
    private function syncCandidateFromAnalytics(Resume $resume, array $data): void
    {
        $user = $resume->user;
        if (! $user) {
            return;
        }

        $candidate = $user->candidate;
        if (! $candidate) {
            return;
        }

        $updates = [];

        if (! empty($data['skills']) && is_array($data['skills'])) {
            $updates['skills'] = $data['skills'];
        }

        if (! empty($data['career_growth']) && is_array($data['career_growth'])) {
            $projects = [];
            foreach ($data['career_growth'] as $role) {
                if (! is_array($role)) {
                    continue;
                }
                $projects[] = [
                    'title' => $role['title'] ?? '',
                    'company' => $role['company'] ?? '',
                    'description' => $role['description'] ?? '',
                    'tag' => $role['tag'] ?? '',
                    'technologies' => [],
                ];
            }
            if ($projects !== []) {
                $updates['projects'] = $projects;
            }
        }

        if (isset($data['total_experience_years'])) {
            $updates['experience_years'] = (int) round((float) $data['total_experience_years']);
        }

        if (! empty($data['current_role'])) {
            $updates['current_title'] = $data['current_role'];
        }

        if (! empty($data['ai_profile_summary'])) {
            $updates['summary'] = $data['ai_profile_summary'];
        }

        if ($updates !== []) {
            $candidate->update($updates);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $resume = Resume::find($this->resumeId);

        if (! $resume) {
            return;
        }

        $this->markFailed($resume, $exception?->getMessage() ?? 'Analytics processing failed.');
    }

    private function markFailed(Resume $resume, string $message): void
    {
        $resume->update([
            'status'        => Resume::STATUS_FAILED,
            'error_message' => $message,
        ]);

        Log::error('ProcessResumeAnalyticsJob failed', [
            'resume_id' => $resume->id,
            'user_id'   => $resume->user_id,
            'error'     => $message,
        ]);
    }
}
