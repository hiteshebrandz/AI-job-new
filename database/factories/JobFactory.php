<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'hr_id'               => User::factory()->state(['role' => 'hr']),
            'title'               => fake()->jobTitle(),
            'company_name'        => fake()->company(),
            'location'            => fake()->city(),
            'job_type'            => fake()->randomElement(['Full-time', 'Part-time', 'Contract']),
            'work_mode'           => fake()->randomElement(['On-site', 'Remote', 'Hybrid']),
            'description'         => fake()->paragraphs(2, true),
            'status'              => Job::STATUS_ACTIVE,
            'min_salary'          => fake()->numberBetween(30000, 80000),
            'max_salary'          => fake()->numberBetween(80000, 150000),
            'experience_required' => fake()->randomElement(['0-1 years', '2-4 years', '5+ years']),
            'skills_required'     => json_encode(fake()->randomElements(['PHP', 'Laravel', 'React', 'Python', 'SQL'], 3)),
            'application_deadline'=> now()->addDays(30),
        ];
    }
}
