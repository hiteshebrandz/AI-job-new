<?php

namespace App\Services;

use App\Jobs\AnalyzeJobDescriptionJob;
use App\Models\JobDescription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

class JobDescriptionService
{
    public function __construct(
        private PythonJdAnalyzerService $jdAnalyzer,
    ) {}

    public function create(
        User $hr,
        ?string $title,
        ?string $jdText,
        ?UploadedFile $file = null,
    ): JobDescription {
        $content = $this->normalizeJdText($jdText);
        $sourceType = JobDescription::SOURCE_TEXT;
        $filePath = null;
        $fileName = null;

        if ($file !== null) {
            $filePath = ResumeParserService::storeUploadedFile($file);
            $fileName = $file->getClientOriginalName();
            $sourceType = JobDescription::SOURCE_FILE;

            $fileText = $this->extractTextFromStoredFile($filePath);
            if ($fileText !== '') {
                $content = $content !== '' ? $content."\n\n".$fileText : $fileText;
            }
        }

        if ($content === '') {
            throw ValidationException::withMessages([
                'jd_text' => $file !== null
                    ? 'Could not read text from the uploaded file. Paste the job description in the text box or try a different file (PDF, DOCX, or TXT).'
                    : 'Paste a job description or upload a file.',
            ]);
        }

        $jd = JobDescription::create([
            'hr_id' => $hr->id,
            'title' => $title ?: $this->guessTitle($content),
            'jd_content' => $content,
            'source_type' => $sourceType,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'status' => JobDescription::STATUS_PENDING,
        ]);

        $this->dispatchAnalysis($jd);

        return $jd;
    }

    public function dispatchAnalysis(JobDescription $jd): void
    {
        if (config('queue.default') !== 'sync') {
            AnalyzeJobDescriptionJob::dispatch($jd->id);

            return;
        }

        AnalyzeJobDescriptionJob::dispatchSync($jd->id);
    }

    public function analyze(JobDescription $jd): void
    {
        $jd->update([
            'status' => JobDescription::STATUS_ANALYZING,
            'analysis_error' => null,
        ]);

        try {
            $result = $this->runAnalyzer($jd);
            $jd->update([
                'extracted_requirements' => $result,
                'status' => JobDescription::STATUS_READY,
            ]);
        } catch (\Throwable $e) {
            Log::warning('JD AI analysis failed, using heuristic extraction.', [
                'jd_id' => $jd->id,
                'error' => $e->getMessage(),
            ]);

            $jd->update([
                'extracted_requirements' => $this->heuristicExtract($jd->jd_content),
                'status' => JobDescription::STATUS_READY,
                'analysis_error' => 'AI analysis unavailable: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function runAnalyzer(JobDescription $jd): array
    {
        if ($jd->file_path) {
            $disk = config('resume.disk', 'local');
            $absolutePath = Storage::disk($disk)->path($jd->file_path);

            $result = $this->jdAnalyzer->analyzeFile($absolutePath);
            if (! empty($result['extracted_text'])) {
                $jd->update(['jd_content' => $result['extracted_text']]);
            }

            return $this->normalizeRequirements($result['data']);
        }

        $result = $this->jdAnalyzer->analyzeText($jd->jd_content);

        return $this->normalizeRequirements($result['data']);
    }

    private function normalizeJdText(?string $jdText): string
    {
        $content = trim((string) $jdText);

        if (in_array(strtolower($content), ['null', 'undefined', 'nil', 'none'], true)) {
            return '';
        }

        return $content;
    }

    private function extractTextFromStoredFile(string $filePath): string
    {
        $disk = config('resume.disk', 'local');
        $absolutePath = Storage::disk($disk)->path($filePath);

        if (! is_readable($absolutePath)) {
            Log::warning('JD file not readable on disk.', ['path' => $filePath]);

            return '';
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        if ($ext === 'txt') {
            return trim((string) file_get_contents($absolutePath));
        }

        $pythonBin = env('PYTHON_BIN', 'python3');
        $scriptPath = base_path('scripts/extract_document_text.py');

        if (! is_file($scriptPath)) {
            return $this->extractTextViaParseResume($absolutePath);
        }

        try {
            $process = new Process([$pythonBin, $scriptPath, $absolutePath], null, null, null, 90);
            $process->run();

            $decoded = json_decode(trim($process->getOutput()), true);
            if (is_array($decoded) && ($decoded['success'] ?? false) === true && ! empty($decoded['text'])) {
                return trim((string) $decoded['text']);
            }

            if (is_array($decoded) && ! empty($decoded['error'])) {
                Log::warning('JD text extraction script error', ['error' => $decoded['error']]);
            }
        } catch (\Throwable $e) {
            Log::warning('JD text extraction failed', ['error' => $e->getMessage()]);
        }

        return $this->extractTextViaParseResume($absolutePath);
    }

    private function extractTextViaParseResume(string $absolutePath): string
    {
        $pythonBin = env('PYTHON_BIN', 'python3');
        $scriptPath = base_path('scripts/parse_resume.py');

        if (! is_file($scriptPath)) {
            return '';
        }

        try {
            $process = new Process([$pythonBin, $scriptPath, $absolutePath], null, null, null, 90);
            $process->run();

            if (! $process->isSuccessful()) {
                return '';
            }

            $decoded = json_decode(trim($process->getOutput()), true);
            if (! is_array($decoded)) {
                return '';
            }

            $parts = array_filter([
                $decoded['title'] ?? '',
                $decoded['summary'] ?? '',
                $decoded['ai_recommendation'] ?? '',
                is_array($decoded['skills'] ?? null) ? implode(', ', $decoded['skills']) : '',
            ]);

            return trim(implode("\n\n", $parts));
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeRequirements(array $data): array
    {
        return [
            'skills' => $this->stringList($data['skills'] ?? []),
            'experience' => (string) ($data['experience'] ?? ''),
            'education' => (string) ($data['education'] ?? ''),
            'technologies' => $this->stringList($data['technologies'] ?? []),
            'responsibilities' => $this->stringList($data['responsibilities'] ?? []),
            'preferred_qualifications' => $this->stringList($data['preferred_qualifications'] ?? []),
            'keywords' => $this->stringList($data['keywords'] ?? []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function heuristicExtract(string $content): array
    {
        $lower = mb_strtolower($content);
        $vocab = [
            'php', 'laravel', 'javascript', 'typescript', 'react', 'angular', 'vue', 'node', 'nodejs',
            'python', 'django', 'java', 'spring', 'mysql', 'postgresql', 'mongodb', 'redis', 'docker',
            'kubernetes', 'aws', 'azure', 'git', 'rest', 'api', 'graphql', 'machine learning', 'ai',
            'sql', 'html', 'css', 'tailwind', 'devops', 'agile', 'scrum',
        ];

        $found = [];
        foreach ($vocab as $term) {
            if (str_contains($lower, $term)) {
                $found[] = ucfirst($term);
            }
        }

        $experience = '';
        if (preg_match('/(\d+)\+?\s*(?:years?|yrs?)/i', $content, $m)) {
            $experience = $m[0];
        }

        return [
            'skills' => array_slice(array_unique($found), 0, 20),
            'experience' => $experience,
            'education' => '',
            'technologies' => array_slice(array_unique($found), 0, 15),
            'responsibilities' => [],
            'preferred_qualifications' => [],
            'keywords' => array_slice(array_unique($found), 0, 25),
        ];
    }

    /**
     * @param  mixed  $items
     * @return array<int, string>
     */
    private function stringList(mixed $items): array
    {
        if (! is_array($items)) {
            return $items !== '' && $items !== null ? [(string) $items] : [];
        }

        $out = [];
        foreach ($items as $item) {
            $s = is_string($item) ? trim($item) : trim((string) ($item['name'] ?? $item['title'] ?? ''));
            if ($s !== '') {
                $out[] = $s;
            }
        }

        return array_values(array_unique($out));
    }

    private function guessTitle(string $content): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $content) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '' && mb_strlen($line) <= 120) {
                return $line;
            }
        }

        return 'Job Description '.now()->format('M j, Y');
    }
}
