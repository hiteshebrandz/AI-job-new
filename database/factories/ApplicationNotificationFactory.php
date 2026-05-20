<?php

namespace Database\Factories;

use App\Models\ApplicationNotification;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationNotification>
 */
class ApplicationNotificationFactory extends Factory
{
    protected $model = ApplicationNotification::class;

    public function definition(): array
    {
        $user = User::factory()->create(['role' => 'user']);
        $hr   = User::factory()->create(['role' => 'hr']);
        $job  = Job::factory()->create(['hr_id' => $hr->id]);

        $application = JobApplication::create([
            'user_id'     => $user->id,
            'job_id'      => $job->id,
            'status'      => JobApplication::STATUS_APPLIED,
            'match_score' => 75,
            'applied_at'  => now(),
        ]);

        return [
            'user_id'            => $user->id,
            'job_application_id' => $application->id,
            'message'            => fake()->sentence(),
            'is_read'            => false,
        ];
    }
}
