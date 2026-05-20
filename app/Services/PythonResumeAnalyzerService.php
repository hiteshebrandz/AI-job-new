<?php

namespace App\Services;

use App\Models\Resume;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Process;

class PythonResumeAnalyzerService
{
    private const TIMEOUT_SECONDS = 120;

    public function analyze(Resume $resume): array
    {
        $absolutePath = storage_path('app/public/' . $resume->file_path);

        if (! is_readable($absolutePath)) {
            throw new RuntimeException('Resume file is not readable: ' . $resume->file_name);
        }

        $pythonBin  = env('PYTHON_BIN', 'python3');
        $scriptPath = base_path('scripts/resume_analyzer/analyze_resume.py');

        if (! is_file($scriptPath)) {
            throw new RuntimeException('Analytics script not found at: scripts/resume_analyzer/analyze_resume.py');
        }

        $process = new Process(
            [$pythonBin, $scriptPath, $absolutePath],
            null,
            $this->buildProcessEnv(),
            null,
            self::TIMEOUT_SECONDS
        );

        $process->run();

        $output = trim($process->getOutput());
        $decoded = $this->tryDecodeStructuredOutput($output);

        // Python _error() prints JSON to stdout and exits 1 — Symfony treats that as failure,
        // but the real message is in stdout.
        if (! $process->isSuccessful()) {
            if (is_array($decoded) && isset($decoded['success']) && $decoded['success'] === false) {
                $errorMsg = $this->userFacingMessage($decoded['error'] ?? 'Unknown error from Python script.');
                Log::error('PythonResumeAnalyzerService: script reported error', [
                    'resume_id' => $resume->id,
                    'error'     => $errorMsg,
                ]);
                throw new RuntimeException($errorMsg);
            }

            $stderr = trim($process->getErrorOutput());
            $snippet = $this->safeOutputSnippet($output);
            Log::error('PythonResumeAnalyzerService: process failed', [
                'resume_id' => $resume->id,
                'exit_code' => $process->getExitCode(),
                'stderr'    => $stderr,
                'stdout_snippet' => $snippet,
            ]);

            $detail = $stderr !== '' ? $stderr : ($snippet !== '' ? $snippet : 'exit code ' . $process->getExitCode());
            throw new RuntimeException('Python process failed: ' . $detail);
        }

        if (! is_array($decoded)) {
            $decoded = $this->decodeOutput($output);
        }

        if (isset($decoded['success']) && $decoded['success'] === false) {
            $errorMsg = $this->userFacingMessage($decoded['error'] ?? 'Unknown error from Python script.');
            Log::error('PythonResumeAnalyzerService: script returned error', [
                'resume_id' => $resume->id,
                'error'     => $errorMsg,
            ]);
            throw new RuntimeException($errorMsg);
        }

        if (! isset($decoded['data']) || ! is_array($decoded['data'])) {
            throw new RuntimeException('Python script returned an unexpected response structure.');
        }

        return [
            'extracted_text' => $decoded['extracted_text'] ?? '',
            'data'           => $decoded['data'],
        ];
    }

    /**
     * Symfony Process replaces the entire environment when $env is passed.
     * Merge the current PHP environment so PATH, HOME, LANG, etc. are preserved.
     */
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
            $groqKey = env('GROQ_API_KEY', '');
            $groqModel = env('GROQ_MODEL', 'llama-3.3-70b-versatile');
            $groqBase = env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1');
            if ($groqKey !== '') {
                $base['GROQ_API_KEY'] = $groqKey;
            }
            if ($groqModel !== '') {
                $base['GROQ_MODEL'] = $groqModel;
            }
            if ($groqBase !== '') {
                $base['GROQ_BASE_URL'] = $groqBase;
            }
        } elseif ($provider === 'gemini') {
            $geminiKey = env('GEMINI_API_KEY', '');
            $geminiModel = env('GEMINI_MODEL', 'gemini-2.0-flash');
            if ($geminiKey !== '') {
                $base['GEMINI_API_KEY'] = $geminiKey;
            }
            if ($geminiModel !== '') {
                $base['GEMINI_MODEL'] = $geminiModel;
            }
        } elseif ($provider === 'openai') {
            $apiKey = env('OPENAI_API_KEY', '');
            $model  = env('OPENAI_MODEL', 'gpt-4.1-mini');
            if ($apiKey !== '') {
                $base['OPENAI_API_KEY'] = $apiKey;
            }
            if ($model !== '') {
                $base['OPENAI_MODEL'] = $model;
            }
        }

        return $base;
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

    /**
     * Shorten stdout for logs / exceptions (never include secrets).
     */
    /**
     * Map technical API errors to messages safe to show on the analytics page.
     */
    private function userFacingMessage(string $raw): string
    {
        $lower = strtolower($raw);

        if (str_contains($lower, 'insufficient_quota')
            || str_contains($lower, 'exceeded your current quota')
            || (str_contains($lower, '429') && str_contains($lower, 'quota'))) {
            return 'OpenAI API quota exceeded. Switch to Groq: set RESUME_AI_PROVIDER=groq and GROQ_API_KEY in .env (console.groq.com).';
        }

        if (str_contains($lower, 'groq_api_key') || (str_contains($lower, 'groq') && str_contains($lower, 'not set'))) {
            return 'Groq API key missing. Set GROQ_API_KEY in .env (get one at console.groq.com/keys).';
        }

        if (str_contains($lower, 'gemini_api_key') || str_contains($lower, 'gemini api')) {
            return 'Gemini API key missing or invalid. Set GEMINI_API_KEY in .env (get one at aistudio.google.com/apikey).';
        }

        if (str_contains($lower, 'rate limit') || str_contains($lower, '429')) {
            return 'OpenAI rate limit reached. Wait a minute and try again, or check your API usage limits.';
        }

        if (str_contains($lower, 'invalid_api_key') || str_contains($lower, 'incorrect api key')) {
            return 'Invalid OpenAI API key. Check OPENAI_API_KEY in your .env file.';
        }

        if (str_contains($lower, 'openai api call failed')) {
            return 'AI analysis could not be completed. Check your OpenAI account and API key, then try again.';
        }

        if (strlen($raw) > 500) {
            return 'AI analysis failed. Please try again or contact support.';
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

    private function decodeOutput(string $output): array
    {
        $decoded = json_decode($output, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        // Try to extract the first JSON object in case there is stray output
        if (preg_match('/\{[\s\S]*\}/', $output, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw new RuntimeException('Could not parse JSON output from Python script.');
    }
}
