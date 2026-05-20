<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_dashboard_shows_real_application_count(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $hr   = User::factory()->create(['role' => 'hr']);
        // Create 3 different jobs so the unique (user_id, job_id) constraint is not violated
        $jobs = Job::factory()->count(3)->create(['hr_id' => $hr->id, 'status' => Job::STATUS_ACTIVE]);

        foreach ($jobs as $job) {
            JobApplication::create([
                'user_id'    => $user->id,
                'job_id'     => $job->id,
                'status'     => JobApplication::STATUS_APPLIED,
                'match_score'=> 75,
                'applied_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->get('/user/dashboard');

        $response->assertOk()
                 ->assertViewHas('appliedCount', 3);
    }

    public function test_hr_dashboard_shows_real_applicant_count(): void
    {
        $hr  = User::factory()->create(['role' => 'hr']);
        $job = Job::factory()->create(['hr_id' => $hr->id, 'status' => Job::STATUS_ACTIVE]);

        JobApplication::factory()->count(5)->create([
            'job_id'     => $job->id,
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($hr)->get('/hr/dashboard');

        $response->assertOk()
                 ->assertViewHas('totalApplicants', 5);
    }

    public function test_admin_dashboard_shows_real_totals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(4)->create(['role' => 'user']);
        User::factory()->count(2)->create(['role' => 'hr']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk()
                 ->assertViewHas('totalUsers', 4)
                 ->assertViewHas('totalHr', 2);
    }
}
