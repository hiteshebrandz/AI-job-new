<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CheckPythonSetup extends Command
{
    protected $signature   = 'resume:check-python';
    protected $description = 'Test Python binary + required packages for resume parsing';

    public function handle(): int
    {
        $this->info('Checking Python setup for resume parsing...');
        $this->newLine();

        // 1. Determine Python binary
        $binary = config('resume.python_path', '');

        $candidates = array_filter(array_unique([
            $binary,
            env('RESUME_PYTHON_PATH', ''),
            ...array_filter(explode(',', env('RESUME_PYTHON_EXTRA_PATHS', ''))),
            'python3',
            'python',
            '/usr/bin/python3',
            '/usr/local/bin/python3',
        ]));

        $foundBinary = null;
        foreach ($candidates as $bin) {
            $bin = trim($bin);
            if ($bin === '') {
                continue;
            }
            $proc = Process::fromShellCommandline("{$bin} --version");
            $proc->run();
            if ($proc->isSuccessful()) {
                $foundBinary = $bin;
                $this->line("  ✅ Python binary: <info>{$bin}</info> → " . trim($proc->getOutput() ?: $proc->getErrorOutput()));
                break;
            }
        }

        if (! $foundBinary) {
            $this->error('  ❌ No working Python binary found. Set RESUME_PYTHON_PATH in .env');
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Checking required Python packages...');

        $packages = ['pdfplumber', 'docx', 'pypdf'];
        $allOk    = true;

        foreach ($packages as $pkg) {
            $importName = $pkg === 'docx' ? 'docx' : $pkg;
            $proc = Process::fromShellCommandline("{$foundBinary} -c \"import {$importName}; print('ok')\"");
            $proc->run();

            if ($proc->isSuccessful() && trim($proc->getOutput()) === 'ok') {
                $this->line("  ✅ {$pkg}");
            } else {
                $this->line("  ❌ {$pkg} — not installed. Run: <comment>pip install {$pkg}</comment>");
                $allOk = false;
            }
        }

        $this->newLine();

        if ($allOk) {
            $this->info('✅ Python setup is complete. Resume parsing should work correctly.');
            return self::SUCCESS;
        }

        $this->warn('⚠️  Some packages are missing. Install them with:');
        $this->line("  {$foundBinary} -m pip install pdfplumber python-docx pypdf");
        return self::FAILURE;
    }
}
