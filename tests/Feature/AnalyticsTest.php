<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_analytics_data_endpoint_returns_expected_structure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->getJson('/admin/analytics/data');

        $response->assertOk()
                 ->assertJsonStructure([
                     'success',
                     'labels',
                     'registrations_by_month',
                     'applications_by_month',
                     'jobs_by_month',
                     'totals' => ['users', 'hr', 'jobs', 'active_jobs', 'applications'],
                     'status_breakdown',
                 ]);
    }

    public function test_admin_analytics_data_counts_match_db(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(3)->create(['role' => 'user']);
        $hrs  = User::factory()->count(2)->create(['role' => 'hr']);
        // Create 4 jobs all owned by the first HR (JobFactory would otherwise create extra HR users)
        Job::factory()->count(4)->create(['hr_id' => $hrs->first()->id, 'status' => Job::STATUS_ACTIVE]);

        $response = $this->actingAs($admin)->getJson('/admin/analytics/data');

        $totals = $response->json('totals');
        $this->assertEquals(3, $totals['users']);
        $this->assertEquals(2, $totals['hr']);
        $this->assertEquals(4, $totals['jobs']);
    }

    public function test_resume_analytics_page_is_accessible_to_candidate(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/user/resume/analytics');

        $response->assertOk()
                 ->assertViewHas('applicationCount', 0)
                 ->assertViewHas('aiScore', 0);
    }

    public function test_non_admin_cannot_access_admin_analytics_data(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
             ->getJson('/admin/analytics/data')
             ->assertForbidden();
    }
}
