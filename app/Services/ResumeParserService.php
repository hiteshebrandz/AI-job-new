<?php

namespace App\Services;

use App\Jobs\ParseResumeJob;
use App\Models\ResumeParsingLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResumeParserService
{
    public function dispatchParse(ResumeParsingLog $log): void
    {
        if (config('resume.queue', false) && config('queue.default') !== 'sync') {
            ParseResumeJob::dispatch($log->id);

            return;
        }

        $this->parse($log);
    }

    public function parse(ResumeParsingLog $log): array
    {
        $log->update(['parsing_status' => ResumeParsingLog::STATUS_PROCESSING]);

        $disk = config('resume.disk', 'local');
        $absolutePath = Storage::disk($disk)->path($log->file_path);

        if (! is_readable($absolutePath)) {
            throw new \RuntimeException('Resume file is not readable on disk.');
        }

        $parserSource = 'python';
        $parseWarning = null;

        try {
            $parsed = $this->runPythonParser($absolutePath);
        } catch (\Throwable $e) {
            Log::warning('Resume Python parser failed, using PHP fallback.', [
                'log_id' => $log->id,
                'file' => $log->file_name,
                'error' => $e->getMessage(),
            ]);

            $parserSource = 'fallback';
            $parseWarning = 'Automated parser could not run ('.$e->getMessage().'). Limited fields were extracted — please review and complete the form.';
            $parsed = $this->fallbackParse($absolutePath, $log->file_name);
        }

        $normalized = $this->normalizePayload($parsed);
        $normalized['parser_source'] = $parserSource;

        if ($parseWarning !== null) {
            $normalized['parse_warning'] = $parseWarning;
        }

        if ($this->isLowQualityExtraction($normalized)) {
            $normalized['parse_warning'] = trim(
                ($normalized['parse_warning'] ?? '').' Little text could be read from this file (e.g. scanned PDF). Please enter details manually.'
            );
        }

        $log->update([
            'parsing_status' => ResumeParsingLog::STATUS_COMPLETED,
            'parsed_data' => $normalized,
            'ai_score' => $normalized['ai_score'] ?? null,
            'error_message' => $parserSource === 'fallback' ? $parseWarning : null,
        ]);

        return $normalized;
    }

    private function isLowQualityExtraction(array $payload): bool
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
        $skills = $payload['skills'] ?? [];

        return ($name === '' || strcasecmp($name, 'Unknown Candidate') === 0)
            && $email === ''
            && empty($skills);
    }

    /**
     * @return list<list<string>>
     */
    private function pythonCommandVariants(): array
    {
        $variants = [];

        foreach ($this->discoverPythonBinaryPaths() as $path) {
            $variants[] = [$path];
        }

        $configured = trim((string) config('resume.python_path', 'python'));
        if ($configured !== '' && ! $this->looksLikeFilesystemPath($configured)) {
            $variants[] = $this->splitCommand($configured);
        }

        $variants[] = ['python'];
        $variants[] = ['python3'];

        if (PHP_OS_FAMILY === 'Windows') {
            if (is_file('C:\\Windows\\py.exe')) {
                $variants[] = ['C:\\Windows\\py.exe', '-3'];
                $variants[] = ['C:\\Windows\\py.exe'];
            } else {
                $variants[] = ['py', '-3'];
                $variants[] = ['py'];
            }
        }

        $unique = [];
        foreach ($variants as $cmd) {
            $key = implode(' ', $cmd);
            if (! isset($unique[$key])) {
                $unique[$key] = $cmd;
            }
        }

        return array_values($unique);
    }

    /**
     * Resolve python.exe locations (web SAPI often has a minimal PATH on Windows).
     *
     * @return list<string>
     */
    private function discoverPythonBinaryPaths(): array
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        $paths = [];

        foreach (config('resume.python_extra_paths', []) as $extra) {
            $extra = trim((string) $extra);
            if ($extra !== '' && is_file($extra)) {
                $paths[] = $this->normalizeExecutablePath($extra);
            }
        }

        $configured = trim((string) config('resume.python_path', ''));
        if ($configured !== '' && $this->looksLikeFilesystemPath($configured) && is_file($configured)) {
            $paths[] = $this->normalizeExecutablePath($configured);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $paths = array_merge($paths, $this->discoverWindowsPythonPaths());
        }

        $cached = array_values(array_unique(array_filter($paths, 'is_file')));

        return $cached;
    }

    /**
     * @return list<string>
     */
    private function discoverWindowsPythonPaths(): array
    {
        $paths = [];

        foreach (['314', '313', '312', '311', '310', '39', '38'] as $version) {
            $exe = "C:\\Python{$version}\\python.exe";
            if (is_file($exe)) {
                $paths[] = $exe;
            }
        }

        $localAppData = getenv('LOCALAPPDATA') ?: ($_SERVER['LOCALAPPDATA'] ?? '');
        if ($localAppData !== '') {
            $matches = glob($localAppData.'\\Programs\\Python\\Python*\\python.exe') ?: [];
            $paths = array_merge($paths, $matches);
        }

        $whereExe = 'C:\\Windows\\System32\\where.exe';
        if (is_file($whereExe)) {
            $result = Process::timeout(10)->run([$whereExe, 'python']);
            if ($result->successful()) {
                foreach (preg_split('/\R/', trim($result->output())) as $line) {
                    $line = trim($line);
                    if ($line === '' || ! is_file($line)) {
                        continue;
                    }
                    if (str_contains(strtolower($line), 'windowsapps')) {
                        continue;
                    }
                    $paths[] = $this->normalizeExecutablePath($line);
                }
            }
        }

        return $paths;
    }

    private function looksLikeFilesystemPath(string $value): bool
    {
        return str_contains($value, '\\')
            || str_contains($value, '/')
            || str_ends_with(strtolower($value), '.exe');
    }

    private function normalizeExecutablePath(string $path): string
    {
        return str_replace('/', '\\', $path);
    }

    /**
     * @return list<string>
     */
    private function splitCommand(string $command): array
    {
        if (str_contains($command, ' ') && ! str_starts_with($command, '"')) {
            return preg_split('/\s+/', $command) ?: [$command];
        }

        return [$command];
    }

    private function runPythonParser(string $absolutePath): array
    {
        $script = config('resume.parser_script');

        if (! is_file($script)) {
            throw new \RuntimeException('Parser script missing at '.$script);
        }

        $errors = [];

        foreach ($this->pythonCommandVariants() as $command) {
            try {
                return $this->invokePythonParser($command, $script, $absolutePath);
            } catch (\Throwable $e) {
                $errors[] = implode(' ', $command).': '.$e->getMessage();
            }
        }

        throw new \RuntimeException(implode(' | ', $errors));
    }

    /**
     * @param  list<string>  $command
     */
    private function invokePythonParser(array $command, string $script, string $absolutePath): array
    {
        $result = Process::timeout((int) config('resume.timeout', 120))
            ->run([...$command, $script, $absolutePath]);

        if (! $result->successful()) {
            $detail = trim($result->errorOutput() ?: $result->output());
            throw new \RuntimeException($detail ?: 'Process exited with code '.$result->exitCode());
        }

        $decoded = $this->decodeParserJson($result->output());

        if (isset($decoded['error'])) {
            throw new \RuntimeException((string) $decoded['error']);
        }

        return $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeParserJson(string $output): array
    {
        $output = trim($output);

        $decoded = json_decode($output, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $output, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw new \RuntimeException('Invalid parser JSON response.');
    }

    private function fallbackParse(string $absolutePath, string $fileName): array
    {
        try {
            return $this->runPythonParser($absolutePath);
        } catch (\Throwable) {
            // Continue with minimal PHP extraction below.
        }

        $text = $this->extractTextInPhp($absolutePath, $fileName);

        $email = '';
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            $email = $matches[0];
        }

        $phone = '';
        if (preg_match('/(\+?\d[\d\s\-().]{8,}\d)/', $text, $phoneMatch)) {
            $phone = trim($phoneMatch[1]);
        }

        $name = 'Unknown Candidate';
        foreach (preg_split('/\R/', $text) as $line) {
            $line = trim($line);
            if ($line && ! str_contains($line, '@') && strlen($line) < 60 && ! preg_match('/\d{3,}/', $line)) {
                $name = $line;
                break;
            }
        }

        return [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'location' => '',
            'title' => '',
            'skills' => [],
            'experience' => '',
            'experience_years' => 0,
            'education' => '',
            'university' => '',
            'graduation_year' => null,
            'seniority_level' => 'Junior',
            'previous_companies' => '',
            'summary' => $text !== ''
                ? mb_substr(trim($text), 0, 500)
                : 'Profile parsed with basic extraction. Please review and complete missing fields.',
            'ai_score' => 78,
            'ai_recommendation' => 'Profile parsed with basic extraction. Please review and complete missing fields.',
            'skill_accuracy' => 80,
        ];
    }

    private function extractTextInPhp(string $absolutePath, string $fileName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($extension === 'txt' && is_readable($absolutePath)) {
            return file_get_contents($absolutePath) ?: '';
        }

        return '';
    }

    public function normalizePayload(array $parsed): array
    {
        $skills = $parsed['skills'] ?? [];
        if (is_string($skills)) {
            $skills = array_filter(array_map('trim', explode(',', $skills)));
        }

        $years = (int) ($parsed['experience_years'] ?? 0);
        if ($years === 0 && ! empty($parsed['experience'])) {
            if (preg_match('/(\d+)/', (string) $parsed['experience'], $m)) {
                $years = (int) $m[1];
            }
        }

        $title = $this->sanitize((string) ($parsed['title'] ?? $parsed['current_title'] ?? ''), 255);
        $summary = $this->sanitize((string) ($parsed['summary'] ?? $parsed['ai_recommendation'] ?? ''));
        $aiScore = (int) ($parsed['ai_score'] ?? 85);

        $payload = [
            'name' => $this->sanitize((string) ($parsed['name'] ?? ''), 255),
            'email' => $this->sanitize((string) ($parsed['email'] ?? ''), 255),
            'phone' => $this->sanitize((string) ($parsed['phone'] ?? ''), 50),
            'location' => $this->sanitize((string) ($parsed['location'] ?? ''), 255),
            'title' => $title,
            'current_title' => $title,
            'skills' => array_values(array_unique(array_filter(array_map(
                fn ($s) => $this->sanitize((string) $s, 100),
                $skills
            )))),
            'experience' => $this->sanitize((string) ($parsed['experience'] ?? ($years ? "{$years} Years" : '')), 255),
            'experience_years' => $years,
            'education' => $this->sanitize((string) ($parsed['education'] ?? ''), 255),
            'university' => $this->sanitize((string) ($parsed['university'] ?? ''), 255),
            'graduation_year' => $parsed['graduation_year'] ?? null,
            'seniority_level' => $this->sanitize((string) ($parsed['seniority_level'] ?? 'Mid-Level'), 100),
            'previous_companies' => $this->sanitize((string) ($parsed['previous_companies'] ?? '')),
            'summary' => $summary,
            'ai_score' => min(99, max(50, $aiScore)),
            'ai_recommendation' => $summary ?: $this->sanitize((string) ($parsed['ai_recommendation'] ?? 'Review recommended for hiring pipeline placement.')),
            'skill_accuracy' => (int) ($parsed['skill_accuracy'] ?? 90),
        ];

        if (! empty($parsed['parser_source'])) {
            $payload['parser_source'] = $parsed['parser_source'];
        }

        if (! empty($parsed['parse_warning'])) {
            $payload['parse_warning'] = $this->sanitize((string) $parsed['parse_warning'], 500);
        }

        return $payload;
    }

    public function sanitize(string $value, ?int $maxLength = null): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
        $value = trim($value);

        if ($maxLength !== null) {
            $value = mb_substr($value, 0, $maxLength);
        }

        return $value;
    }

    public static function allowedMimeTypes(): array
    {
        return [
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
        ];
    }

    public static function storeUploadedFile(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $disk = config('resume.disk', 'local');
        $directory = $disk === 'public' ? 'resumes/'.date('Y/m') : 'resumes/'.date('Y/m');

        return $file->storeAs(
            $directory,
            Str::uuid().'.'.$extension,
            $disk
        );
    }
}
