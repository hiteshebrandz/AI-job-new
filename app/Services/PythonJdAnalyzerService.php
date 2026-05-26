<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Process;

class PythonJdAnalyzerService
{
    private const TIMEOUT_SECONDS = 120;

    /**
     * @return array{extracted_text: string, data: array<string, mixed>}
     */
    public function analyzeFile(string $absolutePath): array
    {
        if (! is_readable($absolutePath)) {
            throw new RuntimeException('Job description file is not readable.');
        }

        $pythonBin = env('PYTHON_BIN', 'python3');
        $scriptPath = base_path('scripts/jd_analyzer/analyze_jd.py');

        if (! is_file($scriptPath)) {
            throw new RuntimeException('JD analyzer script not found.');
        }

        $process = new Process(
            [$pythonBin, $scriptPath, $absolutePath],
            null,
            $this->buildProcessEnv(),
            null,
            self::TIMEOUT_SECONDS
        );

        $process->run();

        return $this->parseProcessOutput($process);
    }

    /**
     * @return array{extracted_text: string, data: array<string, mixed>}
     */
    public function analyzeText(string $text): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'jd_');
        if ($tmp === false) {
            throw new RuntimeException('Could not create temporary file for JD analysis.');
        }

        $path = $tmp.'.txt';
        rename($tmp, $path);
        file_put_contents($path, $text);

        try {
            return $this->analyzeFile($path);
        } finally {
            @unlink($path);
        }
    }

    public function explainMatch(string $jdSummary, string $candidateSummary): string
    {
        $pythonBin = env('PYTHON_BIN', 'python3');
        $scriptPath = base_path('scripts/jd_analyzer/explain_match.py');

        if (! is_file($scriptPath)) {
            throw new RuntimeException('Explain match script not found.');
        }

        $jdFile = tempnam(sys_get_temp_dir(), 'jd_sum_');
        $candFile = tempnam(sys_get_temp_dir(), 'cand_sum_');
        if ($jdFile === false || $candFile === false) {
            throw new RuntimeException('Could not create temporary files.');
        }

        file_put_contents($jdFile, $jdSummary);
        file_put_contents($candFile, $candidateSummary);

        try {
            $process = new Process(
                [$pythonBin, $scriptPath, $jdFile, $candFile],
                null,
                $this->buildProcessEnv(),
                null,
                60
            );
            $process->run();
            $decoded = $this->parseProcessOutput($process);

            return (string) ($decoded['reason'] ?? '');
        } finally {
            @unlink($jdFile);
            @unlink($candFile);
        }
    }

    /**
     * @return array{extracted_text: string, data: array<string, mixed>, reason?: string}
     */
    private function parseProcessOutput(Process $process): array
    {
        $output = trim($process->getOutput());
        $decoded = $this->tryDecode($output);

        if (! $process->isSuccessful()) {
            if (is_array($decoded) && ($decoded['success'] ?? null) === false) {
                throw new RuntimeException((string) ($decoded['error'] ?? 'JD analysis failed.'));
            }
            $stderr = trim($process->getErrorOutput());
            throw new RuntimeException($stderr !== '' ? $stderr : 'JD analysis process failed.');
        }

        if (! is_array($decoded) || ($decoded['success'] ?? false) !== true) {
            throw new RuntimeException((string) ($decoded['error'] ?? 'Unexpected JD analyzer response.'));
        }

        if (isset($decoded['reason'])) {
            return ['extracted_text' => '', 'data' => [], 'reason' => (string) $decoded['reason']];
        }

        if (! isset($decoded['data']) || ! is_array($decoded['data'])) {
            throw new RuntimeException('JD analyzer returned invalid data structure.');
        }

        return [
            'extracted_text' => (string) ($decoded['extracted_text'] ?? ''),
            'data' => $decoded['data'],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function tryDecode(string $output): ?array
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

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function buildProcessEnv(): array
    {
        $base = getenv();
        if (! is_array($base)) {
            $base = [];
        }

        $provider = strtolower((string) env('RESUME_AI_PROVIDER', 'groq'));
        if (in_array($provider, ['grock', 'grok'], true)) {
            $provider = 'groq';
        }
        $base['RESUME_AI_PROVIDER'] = $provider;

        foreach (['GROQ_API_KEY', 'GROQ_MODEL', 'GROQ_BASE_URL', 'OPENAI_API_KEY', 'OPENAI_MODEL', 'GEMINI_API_KEY', 'GEMINI_MODEL'] as $key) {
            $val = env($key);
            if ($val !== null && $val !== '') {
                $base[$key] = (string) $val;
            }
        }

        return $base;
    }
}
