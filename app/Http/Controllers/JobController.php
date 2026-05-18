<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    public function create(): View
    {
        return view('pages.post_a_job', [
            'activeNav' => 'jobs',
            'initialStep' => (int) old('_step', 1),
        ]);
    }

    public function edit(Job $job): View
    {
        $this->authorizeJob($job);

        return view('pages.post_a_job', [
            'activeNav' => 'jobs',
            'job' => $job,
            'initialStep' => (int) old('_step', 1),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'skills_required' => $this->normalizeSkillsRequired($request->input('skills_required')),
        ]);

        $validated = $request->validate($this->jobRules());

        Job::create([
            ...$validated,
            'hr_id' => $request->user()->id,
            'number_of_openings' => $validated['number_of_openings'] ?? 1,
            'currency' => $validated['currency'] ?? 'USD',
        ]);

        $message = $validated['status'] === Job::STATUS_ACTIVE
            ? 'Job posted successfully.'
            : 'Job saved as draft.';

        return redirect()
            ->route('hr.dashboard')
            ->with('success', $message);
    }

    public function update(Request $request, Job $job): RedirectResponse
    {
        $this->authorizeJob($job);

        $request->merge([
            'skills_required' => $this->normalizeSkillsRequired($request->input('skills_required')),
        ]);

        $validated = $request->validate($this->jobRules(isUpdate: true));

        $job->update([
            ...$validated,
            'number_of_openings' => $validated['number_of_openings'] ?? 1,
            'currency' => $validated['currency'] ?? 'USD',
        ]);

        $message = $validated['status'] === Job::STATUS_ACTIVE
            ? 'Job updated and published.'
            : 'Job updated and saved as draft.';

        return redirect()
            ->route('hr.dashboard')
            ->with('success', $message);
    }

    public function destroy(Job $job): RedirectResponse
    {
        $this->authorizeJob($job);

        $job->delete();

        return redirect()
            ->route('hr.dashboard')
            ->with('success', 'Job deleted successfully.');
    }

    public function toggleStatus(Job $job): RedirectResponse
    {
        $this->authorizeJob($job);

        $job->update([
            'status' => $job->status === Job::STATUS_ACTIVE
                ? Job::STATUS_INACTIVE
                : Job::STATUS_ACTIVE,
        ]);

        $message = $job->status === Job::STATUS_ACTIVE
            ? 'Job activated successfully.'
            : 'Job deactivated successfully.';

        return redirect()
            ->route('hr.dashboard')
            ->with('success', $message);
    }

    private function authorizeJob(Job $job): void
    {
        abort_unless($job->hr_id === auth()->id(), 403);
    }

    private function jobRules(bool $isUpdate = false): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'job_type' => ['required', 'string', 'max:100'],
            'experience_required' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'skills_required' => ['nullable', 'string'],
            'screening_question_1' => ['nullable', 'string'],
            'screening_question_2' => ['nullable', 'string'],
            'screening_question_3' => ['nullable', 'string'],
            'minimum_qualification' => ['nullable', 'string'],
            'preferred_qualification' => ['nullable', 'string'],
            'work_mode' => ['nullable', 'string', 'max:100'],
            'notice_period' => ['nullable', 'string', 'max:100'],
            'salary' => ['nullable', 'string', 'max:255'],
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            'max_salary' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'application_deadline' => $isUpdate
                ? ['nullable', 'date']
                : ['nullable', 'date', 'after_or_equal:today'],
            'number_of_openings' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    private function normalizeSkillsRequired(mixed $value): string
    {
        if ($value === null || $value === 'null') {
            return '';
        }

        return trim((string) $value);
    }
}
