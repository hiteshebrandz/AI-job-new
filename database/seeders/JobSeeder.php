<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $hr = User::query()->where('email', 'hr@gmail.com')->first();

        if (! $hr) {
            $this->command?->warn('JobSeeder skipped: HR user (hr@gmail.com) not found. Run UserSeeder first.');

            return;
        }

        $title = 'Senior Full Stack Developer';

        $job = Job::updateOrCreate(
            [
                'hr_id' => $hr->id,
                'slug' => 'senior-full-stack-developer',
            ],
            [
                'title' => $title,
                'company_name' => 'TalentSync AI',
                'location' => 'Remote / Hybrid',
                'job_type' => 'Full-time',
                'work_mode' => 'Hybrid',
                'experience_required' => '5+ years',
                'description' => <<<'TEXT'
We are looking for a Senior Full Stack Developer to build and maintain our hiring platform and internal tools. You will work across Laravel APIs, MySQL, and modern front-end stacks while collaborating with product and HR teams.
TEXT,
                'responsibilities' => <<<'TEXT'
- Design and implement RESTful APIs and background jobs in Laravel
- Build responsive UI features with JavaScript frameworks
- Review code, mentor junior developers, and improve CI/CD pipelines
- Partner with HR stakeholders on candidate matching and analytics features
TEXT,
                'requirements' => <<<'TEXT'
- 5+ years of professional software development experience
- Strong PHP and Laravel skills
- Experience with relational databases (MySQL)
- Familiarity with React or similar SPA frameworks
- Clear communication and ownership mindset
TEXT,
                'skills_required' => 'PHP, Laravel, MySQL, JavaScript, React, REST API, Git',
                'minimum_qualification' => "Bachelor's degree in Computer Science or equivalent experience",
                'preferred_qualification' => 'Experience with AI integrations, queue workers, and HR/recruitment products',
                'min_salary' => 90000,
                'max_salary' => 130000,
                'currency' => 'USD',
                'salary' => '$90k - $130k',
                'application_deadline' => now()->addMonths(2)->toDateString(),
                'number_of_openings' => 1,
                'status' => Job::STATUS_ACTIVE,
            ]
        );

        if (! $job->slug) {
            $job->updateQuietly(['slug' => Str::slug($title)]);
        }
    }
}
