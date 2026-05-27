<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeResumeOptimizerJob;
use App\Models\ResumeParsingLog;
use App\Models\User;
use App\Services\ResumeParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuestToolsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
        config(['resume.disk' => 'local', 'resume.queue' => false]);
    }

    public function test_guest_tools_page_is_accessible(): void
    {
        $this->get(route('tools.guest'))
            ->assertOk()
            ->assertSee('Test Your Resume')
            ->assertSee('ATS Compatibility');
    }

    public function test_landing_page_links_to_guest_tools(): void
    {
        $this->get(route('landing'))
            ->assertOk()
            ->assertSee(route('tools.guest', ['tool' => 'resume']), false)
            ->assertSee(route('tools.guest', ['tool' => 'ats']), false);
    }

    public function test_guest_resume_test_allows_three_attempts_then_blocks(): void
    {
        $this->mock(ResumeParserService::class, function ($mock) {
            $mock->shouldReceive('parse')->times(3)->andReturnUsing(function ($log) {
                $log->update([
                    'parsing_status' => ResumeParsingLog::STATUS_COMPLETED,
                    'parsed_data'    => ['name' => 'Guest User', 'skills' => ['PHP']],
                    'ai_score'       => 80,
                ]);

                return $log->parsed_data;
            });
        });

        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        for ($i = 1; $i <= 3; $i++) {
            $response = $this->postJson(route('tools.guest.resume.upload'), ['resume' => $file]);
            $response->assertOk()->assertJsonPath('success', true);
        }

        $this->postJson(route('tools.guest.resume.upload'), ['resume' => $file])
            ->assertForbidden()
            ->assertJsonPath('login_required', true);
    }

    public function test_guest_ats_check_allows_three_attempts_then_blocks(): void
    {
        Queue::fake();
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        for ($i = 1; $i <= 3; $i++) {
            $response = $this->postJson(route('tools.guest.ats.upload'), ['resume' => $file]);
            $response->assertOk()->assertJson(['success' => true, 'status' => 'analyzing']);
        }

        $this->postJson(route('tools.guest.ats.upload'), ['resume' => $file])
            ->assertForbidden()
            ->assertJsonPath('login_required', true);

        Queue::assertPushed(AnalyzeResumeOptimizerJob::class, 3);
    }

    public function test_authenticated_user_bypasses_guest_limit_middleware(): void
    {
        Queue::fake();
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->withSession(['guest_attempts' => ['ats_check' => 3]])
            ->postJson(route('user.resume.ai-optimizer.upload'), ['resume' => $file])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'analyzing']);

        Queue::assertPushed(AnalyzeResumeOptimizerJob::class);
    }

    public function test_guest_cannot_access_another_guest_resume_status(): void
    {
        $log = ResumeParsingLog::create([
            'user_id'          => null,
            'guest_session_id' => 'other-session-id',
            'file_name'        => 'cv.pdf',
            'file_path'        => 'resumes/test.pdf',
            'parsing_status'   => ResumeParsingLog::STATUS_COMPLETED,
        ]);

        $this->getJson(route('tools.guest.resume.status', $log))
            ->assertForbidden();
    }

    public function test_guest_resume_status_accessible_for_own_session(): void
    {
        $sessionId = 'guest-session-abc';

        $log = ResumeParsingLog::create([
            'user_id'          => null,
            'guest_session_id' => $sessionId,
            'file_name'        => 'cv.pdf',
            'file_path'        => 'resumes/test.pdf',
            'parsing_status'   => ResumeParsingLog::STATUS_COMPLETED,
            'ai_score'         => 85,
            'parsed_data'      => ['name' => 'Jane', 'skills' => ['Laravel']],
        ]);

        $this->withSession(['guest_tool_session_id' => $sessionId])
            ->getJson(route('tools.guest.resume.status', $log))
            ->assertOk()
            ->assertJsonPath('ai_score', 85);
    }
}
