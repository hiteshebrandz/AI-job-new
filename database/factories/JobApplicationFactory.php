<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory()->state(['role' => 'user']),
            'job_id'      => Job::factory(),
            'status'      => JobApplication::STATUS_APPLIED,
            'match_score' => fake()->numberBetween(52, 99),
            'applied_at'  => now(),
        ];
    }
}
