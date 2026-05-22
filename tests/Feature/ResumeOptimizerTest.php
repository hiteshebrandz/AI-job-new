<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeResumeOptimizerJob;
use App\Models\ResumeOptimizerRun;
use App\Models\User;
use App\Services\PythonResumeOptimizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumeOptimizerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guest_cannot_access_optimizer_page(): void
    {
        $this->get(route('user.resume.ai-optimizer'))
            ->assertRedirect(route('login'));
    }

    public function test_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $this->postJson(route('user.resume.ai-optimizer.upload'), ['resume' => $file])
            ->assertUnauthorized();
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $file = UploadedFile::fake()->create('virus.exe', 100, 'application/octet-stream');

        $this->actingAs($user)
            ->postJson(route('user.resume.ai-optimizer.upload'), ['resume' => $file])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['resume']);
    }

    public function test_upload_accepts_pdf_and_dispatches_job(): void
    {
        Queue::fake();
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)
            ->postJson(route('user.resume.ai-optimizer.upload'), ['resume' => $file]);

        $response->assertOk()
            ->assertJson(['success' => true, 'status' => 'analyzing']);

        $this->assertDatabaseCount('resume_optimizer_runs', 1);

        Queue::assertPushed(AnalyzeResumeOptimizerJob::class);
    }

    public function test_status_endpoint_requires_owner(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);

        $run = ResumeOptimizerRun::create([
            'user_id'            => $owner->id,
            'original_file_name' => 'cv.pdf',
            'original_file_path' => 'resumes/optimizer/test.pdf',
            'file_type'          => 'pdf',
            'status'             => ResumeOptimizerRun::STATUS_ANALYZED,
            'analysis_result'    => ['score' => 70],
        ]);

        $this->actingAs($other)
            ->getJson(route('user.resume.ai-optimizer.status', $run))
            ->assertForbidden();
    }

    public function test_download_forbidden_for_other_user(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);

        $path = 'resumes/optimized/test.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4 fake');

        $run = ResumeOptimizerRun::create([
            'user_id'             => $owner->id,
            'original_file_name'  => 'cv.pdf',
            'original_file_path'  => 'resumes/optimizer/orig.pdf',
            'file_type'           => 'pdf',
            'status'              => ResumeOptimizerRun::STATUS_COMPLETED,
            'generated_file_path' => $path,
        ]);

        $this->actingAs($other)
            ->get(route('user.resume.ai-optimizer.download', $run))
            ->assertForbidden();
    }

    public function test_analyze_job_persists_results_when_service_succeeds(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $run = ResumeOptimizerRun::create([
            'user_id'            => $user->id,
            'original_file_name' => 'cv.pdf',
            'original_file_path' => 'resumes/optimizer/cv.pdf',
            'file_type'          => 'pdf',
            'status'             => ResumeOptimizerRun::STATUS_UPLOADED,
        ]);

        Storage::disk('public')->put($run->original_file_path, 'fake pdf content');

        $this->mock(PythonResumeOptimizerService::class, function ($mock) {
            $mock->shouldReceive('analyze')->once()->andReturn([
                'extracted_text' => 'Jane Doe developer',
                'data'           => [
                    'score'       => 82,
                    'summary'     => 'Good resume',
                    'ats_status'  => 'ats_friendly',
                    'ats_issues'  => [],
                ],
            ]);
        });

        $job = new AnalyzeResumeOptimizerJob($run->id);
        $job->handle(app(PythonResumeOptimizerService::class));

        $run->refresh();
        $this->assertSame(ResumeOptimizerRun::STATUS_ANALYZED, $run->status);
        $this->assertSame(82, $run->score());
        $this->assertSame('Jane Doe developer', $run->extracted_text);
    }
}
