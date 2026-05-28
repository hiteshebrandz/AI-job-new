<?php

namespace App\Services;

use App\Models\ResumeOptimizerRun;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Process;

class PythonResumeOptimizerService
{
    private const TIMEOUT_ANALYZE = 120;

    private const TIMEOUT_GENERATE = 60;

    public function analyze(ResumeOptimizerRun $run): array
    {
        $absolutePath = $this->originalFilePath($run);

        if (! is_readable($absolutePath)) {
            throw new RuntimeException('Resume file is not readable.');
        }

        $process = $this->runProcess(
            ['analyze', $absolutePath],
            self::TIMEOUT_ANALYZE,
            $run->id
        );

        $decoded = $this->decodeOutput($process, $run->id);

        if (! isset($decoded['data']) || ! is_array($decoded['data'])) {
            throw new RuntimeException('Python script returned an unexpected analysis structure.');
        }

        return [
            'extracted_text' => $decoded['extracted_text'] ?? '',
            'data'           => $decoded['data'],
        ];
    }

    public function generate(ResumeOptimizerRun $run, string $outputAbsolutePath): array
    {
        $absolutePath = $this->originalFilePath($run);

        if (! is_readable($absolutePath)) {
            throw new RuntimeException('Resume file is not readable.');
        }

        $analysisPath = $this->writeTempAnalysisJson($run);
        $textPath     = $this->writeTempExtractedText($run);

        try {
            $command = ($run->extracted_text && is_file($textPath))
                ? ['generate-fast', $textPath, $analysisPath, $outputAbsolutePath]
                : ['generate', $absolutePath, $analysisPath, $outputAbsolutePath];

            $process = $this->runProcess($command, self::TIMEOUT_GENERATE, $run->id);

            return $this->decodeOutput($process, $run->id);
        } finally {
            if (is_file($analysisPath)) {
                @unlink($analysisPath);
            }
            if (is_file($textPath)) {
                @unlink($textPath);
            }
        }
    }

    private function writeTempExtractedText(ResumeOptimizerRun $run): string
    {
        $path = storage_path('app/temp/optimizer_text_' . $run->id . '.txt');
        $dir  = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, $run->extracted_text ?? '');

        return $path;
    }

    private function originalFilePath(ResumeOptimizerRun $run): string
    {
        $disk = config('resume.optimizer_disk', 'public');

        return storage_path('app/' . ($disk === 'public' ? 'public/' : '') . $run->original_file_path);
    }

    private function writeTempAnalysisJson(ResumeOptimizerRun $run): string
    {
        $path = storage_path('app/temp/optimizer_analysis_' . $run->id . '.json');
        $dir  = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, json_encode($run->analysis_result ?? [], JSON_UNESCAPED_UNICODE));

        return $path;
    }

    /**
     * @param  array<int, string>  $args
     */
    private function runProcess(array $args, int $timeout, int $runId): Process
    {
        $pythonBin  = $this->resolvePythonBinary();
        $scriptPath = config('resume.optimizer_script', base_path('scripts/resume_optimizer/optimizer.py'));

        if (! is_file($scriptPath)) {
            throw new RuntimeException('Resume optimizer script not found.');
        }

        $command = array_merge([$pythonBin, $scriptPath], $args);

        $process = new Process(
            $command,
            base_path('scripts/resume_optimizer'),
            $this->buildProcessEnv(),
            null,
            $timeout
        );

        $process->run();

        return $process;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeOutput(Process $process, int $runId): array
    {
        $output  = trim($process->getOutput());
        $decoded = $this->tryDecodeStructuredOutput($output);

        if (! $process->isSuccessful()) {
            if (is_array($decoded) && isset($decoded['success']) && $decoded['success'] === false) {
                $errorMsg = $this->userFacingMessage($decoded['error'] ?? 'Unknown error from Python script.');
                Log::error('PythonResumeOptimizerService: script reported error', [
                    'run_id' => $runId,
                    'error'  => $errorMsg,
                ]);
                throw new RuntimeException($errorMsg);
            }

            $stderr  = trim($process->getErrorOutput());
            $snippet = $this->safeOutputSnippet($output);
            Log::error('PythonResumeOptimizerService: process failed', [
                'run_id'    => $runId,
                'exit_code' => $process->getExitCode(),
                'stderr'    => $stderr,
                'stdout'    => $snippet,
            ]);

            $detail = $stderr !== '' ? $stderr : ($snippet !== '' ? $snippet : 'exit code ' . $process->getExitCode());
            throw new RuntimeException('Resume optimizer failed: ' . $detail);
        }

        if (! is_array($decoded)) {
            $decoded = $this->tryDecodeStructuredOutput($output) ?? throw new RuntimeException('Could not parse JSON from Python script.');
        }

        if (isset($decoded['success']) && $decoded['success'] === false) {
            $errorMsg = $this->userFacingMessage($decoded['error'] ?? 'Unknown error from Python script.');
            Log::error('PythonResumeOptimizerService: script returned error', [
                'run_id' => $runId,
                'error'  => $errorMsg,
            ]);
            throw new RuntimeException($errorMsg);
        }

        return $decoded;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function tryDecodeStructuredOutput(string $output): ?array
    {
        $output = trim($output);
        if ($output === '') {
            return null;
        }

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

        return null;
    }

    private function resolvePythonBinary(): string
    {
        $candidates = [];

        foreach (array_filter([
            env('PYTHON_BIN'),
            config('resume.python_path'),
        ]) as $configured) {
            if (! is_string($configured) || trim($configured) === '') {
                continue;
            }
            $path = trim($configured);
            if (! str_starts_with($path, '/') && ! preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) {
                $path = base_path($path);
            }
            $candidates[] = $path;
        }

        $candidates[] = base_path('scripts/resume_analyzer/venv/Scripts/python.exe');
        $candidates[] = base_path('scripts/resume_analyzer/venv/bin/python3');

        foreach ($candidates as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        return PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
    }

    private function buildProcessEnv(): array
    {
        $base = getenv();
        if (! is_array($base)) {
            $base = [];
        }
        if ($base === []) {
            foreach ($_SERVER as $key => $value) {
                if (! is_string($key) || $key === '' || $key[0] === "\0") {
                    continue;
                }
                if (! is_string($value) && ! is_numeric($value)) {
                    continue;
                }
                $base[$key] = (string) $value;
            }
        }

        $provider = strtolower((string) env('RESUME_AI_PROVIDER', 'groq'));
        if (in_array($provider, ['grock', 'grok'], true)) {
            $provider = 'groq';
        }
        $base['RESUME_AI_PROVIDER'] = $provider;

        if ($provider === 'groq') {
            if ($key = env('GROQ_API_KEY', '')) {
                $base['GROQ_API_KEY'] = $key;
            }
            if ($model = env('GROQ_MODEL', '')) {
                $base['GROQ_MODEL'] = $model;
            }
            if ($url = env('GROQ_BASE_URL', '')) {
                $base['GROQ_BASE_URL'] = $url;
            }
        } elseif ($provider === 'gemini') {
            if ($key = env('GEMINI_API_KEY', '')) {
                $base['GEMINI_API_KEY'] = $key;
            }
            if ($model = env('GEMINI_MODEL', '')) {
                $base['GEMINI_MODEL'] = $model;
            }
        } elseif ($provider === 'openai') {
            if ($key = env('OPENAI_API_KEY', '')) {
                $base['OPENAI_API_KEY'] = $key;
            }
            if ($model = env('OPENAI_MODEL', '')) {
                $base['OPENAI_MODEL'] = $model;
            }
        }

        return $base;
    }

    private function userFacingMessage(string $raw): string
    {
        $lower = strtolower($raw);

        if (str_contains($lower, 'insufficient_quota') || str_contains($lower, 'exceeded your current quota')) {
            return 'AI API quota exceeded. Check your API provider settings in .env.';
        }

        if (str_contains($lower, 'groq_api_key') || (str_contains($lower, 'groq') && str_contains($lower, 'not set'))) {
            return 'Groq API key missing. Set GROQ_API_KEY in .env.';
        }

        if (str_contains($lower, 'gemini_api_key')) {
            return 'Gemini API key missing or invalid. Set GEMINI_API_KEY in .env.';
        }

        if (str_contains($lower, 'rate limit') || str_contains($lower, '429')) {
            return 'AI rate limit reached. Wait a moment and try again.';
        }

        if (strlen($raw) > 500) {
            return 'Resume optimizer failed. Please try again.';
        }

        return $raw;
    }

    private function safeOutputSnippet(string $output, int $maxLen = 800): string
    {
        $output = trim($output);
        if ($output === '') {
            return '';
        }

        $output = preg_replace('/sk-[a-zA-Z0-9_-]{10,}/', '[redacted]', $output) ?? $output;

        return mb_strlen($output) > $maxLen
            ? mb_substr($output, 0, $maxLen) . '…'
            : $output;
    }
}
