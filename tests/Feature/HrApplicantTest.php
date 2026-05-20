<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrApplicantTest extends TestCase
{
    use RefreshDatabase;

    private User $hr;
    private User $candidate;
    private Job  $job;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hr        = User::factory()->create(['role' => 'hr']);
        $this->candidate = User::factory()->create(['role' => 'user']);
        $this->job       = Job::factory()->create([
            'hr_id'  => $this->hr->id,
            'status' => Job::STATUS_ACTIVE,
        ]);
    }

    public function test_hr_can_view_applicants_list(): void
    {
        // Use 3 different candidates to avoid (user_id, job_id) unique constraint
        $candidates = User::factory()->count(3)->create(['role' => 'user']);
        foreach ($candidates as $candidate) {
            JobApplication::create([
                'user_id'    => $candidate->id,
                'job_id'     => $this->job->id,
                'status'     => JobApplication::STATUS_APPLIED,
                'match_score'=> 75,
                'applied_at' => now(),
            ]);
        }

        $response = $this->actingAs($this->hr)->get('/hr/applicants');

        $response->assertOk();
    }

    public function test_hr_can_filter_applicants_by_status(): void
    {
        JobApplication::factory()->create([
            'job_id'     => $this->job->id,
            'user_id'    => $this->candidate->id,
            'status'     => JobApplication::STATUS_SHORTLISTED,
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($this->hr)
            ->get('/hr/applicants?status=' . JobApplication::STATUS_SHORTLISTED);

        $response->assertOk();
    }

    public function test_hr_can_update_application_status(): void
    {
        $application = JobApplication::factory()->create([
            'job_id'     => $this->job->id,
            'user_id'    => $this->candidate->id,
            'status'     => JobApplication::STATUS_APPLIED,
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($this->hr)
            ->postJson("/hr/applications/{$application->id}/status", [
                'status' => JobApplication::STATUS_SHORTLISTED,
            ]);

        $response->assertOk()
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('job_applications', [
            'id'     => $application->id,
            'status' => JobApplication::STATUS_SHORTLISTED,
        ]);
    }

    public function test_hr_cannot_update_applications_for_other_hr_jobs(): void
    {
        $otherHr  = User::factory()->create(['role' => 'hr']);
        $otherJob = Job::factory()->create(['hr_id' => $otherHr->id]);
        $app      = JobApplication::factory()->create([
            'job_id'     => $otherJob->id,
            'applied_at' => now(),
        ]);

        $this->actingAs($this->hr)
             ->postJson("/hr/applications/{$app->id}/status", [
                 'status' => JobApplication::STATUS_SHORTLISTED,
             ])
             ->assertForbidden();
    }

    public function test_candidate_cannot_access_hr_applicants_page(): void
    {
        $this->actingAs($this->candidate)
             ->get('/hr/applicants')
             ->assertRedirect();
    }
}
