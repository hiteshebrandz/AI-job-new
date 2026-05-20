<?php

namespace Tests\Feature;

use App\Jobs\ParseResumeJob;
use App\Models\ResumeParsingLog;
use App\Models\Skill;
use App\Models\User;
use App\Services\ResumeParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumeUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        config(['resume.disk' => 'local', 'resume.queue' => false]);
    }

    public function test_resume_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $this->postJson('/api/resume/upload', ['resume' => $file])
            ->assertUnauthorized();
    }

    public function test_resume_upload_validates_file_type(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $file = UploadedFile::fake()->create('resume.exe', 100, 'application/octet-stream');

        $this->actingAs($user)
            ->postJson('/api/resume/upload', ['resume' => $file])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['resume']);
    }

    public function test_resume_upload_parses_and_returns_data(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $parsed = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'title' => 'Developer',
            'current_title' => 'Developer',
            'skills' => ['PHP', 'Laravel'],
            'experience_years' => 3,
            'education' => 'BCA',
            'summary' => 'Experienced developer',
            'ai_recommendation' => 'Experienced developer',
            'ai_score' => 88,
        ];

        $this->mock(ResumeParserService::class, function ($mock) use ($parsed) {
            $mock->shouldReceive('parse')->once()->andReturnUsing(function ($log) use ($parsed) {
                $log->update([
                    'parsing_status' => ResumeParsingLog::STATUS_COMPLETED,
                    'parsed_data' => $parsed,
                    'ai_score' => 88,
                ]);

                return $parsed;
            });
        });

        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)
            ->postJson('/api/resume/upload', ['resume' => $file]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.email', 'jane@example.com');
    }

    public function test_resume_upload_dispatches_queue_job_when_enabled(): void
    {
        Queue::fake();
        config(['resume.queue' => true, 'queue.default' => 'database']);

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)
            ->postJson('/api/resume/upload', ['resume' => $file]);

        $response->assertOk()
            ->assertJsonPath('status', ResumeParsingLog::STATUS_PENDING);

        Queue::assertPushed(ParseResumeJob::class);
    }

    public function test_parse_status_forbidden_for_other_user(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);

        $log = ResumeParsingLog::create([
            'user_id' => $owner->id,
            'file_name' => 'test.pdf',
            'file_path' => 'resumes/test.pdf',
            'parsing_status' => ResumeParsingLog::STATUS_COMPLETED,
            'parsed_data' => ['name' => 'Test'],
        ]);

        $this->actingAs($other)
            ->getJson("/api/resume/parse/{$log->id}")
            ->assertForbidden();
    }

    public function test_store_profile_creates_candidate_and_skills(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $log = ResumeParsingLog::create([
            'user_id' => $user->id,
            'file_name' => 'test.pdf',
            'file_path' => 'resumes/2026/05/test.pdf',
            'parsing_status' => ResumeParsingLog::STATUS_COMPLETED,
            'parsed_data' => ['name' => 'Jane Doe'],
        ]);

        Storage::disk('local')->put($log->file_path, 'fake pdf content');

        $this->actingAs($user)
            ->postJson('/api/resume/profile', [
                'parsing_log_id' => $log->id,
                'full_name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '9876543210',
                'current_title' => 'Laravel Developer',
                'skills' => ['PHP', 'Laravel', 'MySQL'],
                'education' => 'BCA',
                'experience_years' => 3,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('candidates', [
            'user_id' => $user->id,
            'email' => 'jane@example.com',
            'full_name' => 'Jane Doe',
        ]);

        $this->assertDatabaseHas('skills', ['name' => 'PHP']);
        $this->assertEquals(3, Skill::count());
        $user->refresh();
        $this->assertCount(3, $user->skills);
    }

    public function test_api_register_and_login(): void
    {
        $this->postJson('/api/register', [
            'name' => 'API User',
            'email' => 'api@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ])
            ->assertCreated()
            ->assertJsonStructure(['token', 'user']);

        $this->postJson('/api/login', [
            'email' => 'api@example.com',
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonStructure(['token']);
    }
}
