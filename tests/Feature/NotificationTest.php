<?php

namespace Tests\Feature;

use App\Models\ApplicationNotification;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_applying_for_job_creates_notification(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $hr   = User::factory()->create(['role' => 'hr']);
        $job  = Job::factory()->create([
            'hr_id'  => $hr->id,
            'status' => Job::STATUS_ACTIVE,
        ]);

        $this->actingAs($user)
             ->postJson("/user/jobs/{$job->id}/apply")
             ->assertOk()
             ->assertJson(['success' => true]);

        $this->assertDatabaseHas('application_notifications', [
            'user_id' => $user->id,
        ]);
    }

    public function test_notification_settings_can_be_saved(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/user/settings/notifications', [
            'job_alerts'          => '1',
            'application_updates' => '1',
            'weekly_digest'       => '0',
        ]);

        $response->assertRedirect();
        $settings = $user->fresh()->notification_settings;
        $this->assertTrue($settings['job_alerts']);
        $this->assertTrue($settings['application_updates']);
        $this->assertFalse($settings['weekly_digest']);
    }

    public function test_unread_notification_count_shown_on_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $hr   = User::factory()->create(['role' => 'hr']);
        $job  = Job::factory()->create(['hr_id' => $hr->id]);

        // Create 3 unique applications (one per candidate would also work, but same user+different jobs)
        $jobs = Job::factory()->count(3)->create(['hr_id' => $hr->id]);
        foreach ($jobs as $j) {
            $application = JobApplication::create([
                'user_id'    => $user->id,
                'job_id'     => $j->id,
                'status'     => JobApplication::STATUS_APPLIED,
                'match_score'=> 75,
                'applied_at' => now(),
            ]);
            ApplicationNotification::create([
                'user_id'            => $user->id,
                'job_application_id' => $application->id,
                'message'            => 'Test notification',
                'is_read'            => false,
            ]);
        }

        $response = $this->actingAs($user)->get('/user/dashboard');

        $response->assertOk()
                 ->assertViewHas('unreadNotifs', 3);
    }
}
