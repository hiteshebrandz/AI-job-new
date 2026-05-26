<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobDescription;
use App\Models\User;
use App\Services\PythonJdAnalyzerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AiHiringTest extends TestCase
{
    use RefreshDatabase;

    private User $hr;

    private User $candidateUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hr = User::factory()->create(['role' => User::ROLE_HR]);
        $this->candidateUser = User::factory()->create(['role' => User::ROLE_USER]);

        Candidate::create([
            'user_id' => $this->candidateUser->id,
            'candidate_code' => Candidate::generateCode(),
            'full_name' => 'Jane Candidate',
            'email' => $this->candidateUser->email,
            'skills' => ['PHP', 'Laravel', 'React'],
            'experience_years' => 5,
            'current_title' => 'Full Stack Developer',
            'education' => 'Bachelor Computer Science',
        ]);

        $mock = Mockery::mock(PythonJdAnalyzerService::class);
        $mock->shouldReceive('analyzeText')->andReturn([
            'extracted_text' => 'Senior PHP Developer with Laravel',
            'data' => [
                'skills' => ['PHP', 'Laravel'],
                'experience' => '5+ years',
                'education' => 'Bachelor',
                'technologies' => ['PHP', 'Laravel', 'MySQL'],
                'responsibilities' => ['Build APIs'],
                'preferred_qualifications' => [],
                'keywords' => ['backend'],
            ],
        ]);
        $mock->shouldReceive('explainMatch')->andReturn('Strong PHP and Laravel alignment.');
        $this->app->instance(PythonJdAnalyzerService::class, $mock);
    }

    public function test_hr_can_create_jd_and_match_candidates(): void
    {
        $response = $this->actingAs($this->hr)->post('/hr/ai-hiring', [
            'title' => 'Senior PHP Developer',
            'jd_text' => 'We need a Senior PHP Developer with 5+ years Laravel experience.',
        ]);

        $jd = JobDescription::query()->where('hr_id', $this->hr->id)->first();
        $this->assertNotNull($jd);
        $response->assertRedirect(route('hr.ai-hiring.matches', $jd));

        $this->assertEquals(JobDescription::STATUS_COMPLETED, $jd->fresh()->status);

        $match = CandidateMatch::query()
            ->where('job_description_id', $jd->id)
            ->where('user_id', $this->candidateUser->id)
            ->first();

        $this->assertNotNull($match);
        $this->assertGreaterThanOrEqual(52, $match->match_score);
    }

    public function test_hr_can_view_matches_page(): void
    {
        $jd = JobDescription::create([
            'hr_id' => $this->hr->id,
            'title' => 'Test Role',
            'jd_content' => 'PHP Laravel developer needed',
            'source_type' => JobDescription::SOURCE_TEXT,
            'status' => JobDescription::STATUS_COMPLETED,
            'extracted_requirements' => [
                'skills' => ['PHP'],
                'technologies' => ['Laravel'],
                'experience' => '3 years',
                'education' => '',
                'responsibilities' => [],
                'preferred_qualifications' => [],
                'keywords' => [],
            ],
        ]);

        CandidateMatch::create([
            'job_description_id' => $jd->id,
            'candidate_id' => $this->candidateUser->candidate->id,
            'user_id' => $this->candidateUser->id,
            'match_score' => 88,
            'ai_reason' => 'Great PHP fit.',
        ]);

        $this->actingAs($this->hr)
            ->get(route('hr.ai-hiring.matches', $jd))
            ->assertOk()
            ->assertSee('Jane Candidate')
            ->assertSee('88%');
    }

    public function test_hr_can_upload_jd_txt_file_without_pasted_text(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'jd');
        file_put_contents($tmp, 'Senior Laravel developer with 5 years PHP experience.');

        $mock = Mockery::mock(PythonJdAnalyzerService::class);
        $mock->shouldReceive('analyzeText')->andReturn([
            'extracted_text' => 'Senior Laravel developer',
            'data' => [
                'skills' => ['PHP', 'Laravel'],
                'experience' => '5 years',
                'education' => '',
                'technologies' => ['PHP'],
                'responsibilities' => [],
                'preferred_qualifications' => [],
                'keywords' => [],
            ],
        ]);
        $mock->shouldReceive('explainMatch')->andReturn('Good fit.');
        $this->app->instance(PythonJdAnalyzerService::class, $mock);

        $response = $this->actingAs($this->hr)->post('/hr/ai-hiring', [
            'title' => 'Laravel Dev',
            'jd_file' => new \Illuminate\Http\UploadedFile(
                $tmp,
                'job.txt',
                'text/plain',
                null,
                true
            ),
        ]);

        @unlink($tmp);

        $jd = JobDescription::query()->where('hr_id', $this->hr->id)->first();
        $this->assertNotNull($jd);
        $response->assertRedirect(route('hr.ai-hiring.matches', $jd));
        $this->assertStringContainsString('Laravel', $jd->jd_content);
    }

    public function test_hr_cannot_access_another_hrs_jd(): void
    {
        $otherHr = User::factory()->create(['role' => User::ROLE_HR]);
        $jd = JobDescription::create([
            'hr_id' => $otherHr->id,
            'title' => 'Private',
            'jd_content' => 'Secret JD',
            'source_type' => JobDescription::SOURCE_TEXT,
            'status' => JobDescription::STATUS_COMPLETED,
        ]);

        $this->actingAs($this->hr)
            ->get(route('hr.ai-hiring.matches', $jd))
            ->assertForbidden();
    }
}
