<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeOptimizerUploadRequest;
use App\Jobs\AnalyzeResumeOptimizerJob;
use App\Jobs\GenerateOptimizedResumeJob;
use App\Models\ResumeOptimizerRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeOptimizerController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->boolean('new')) {
            return view('pages.resume_ai_optimizer', [
                'run'              => null,
                'pageStatus'     => 'empty',
                'analysis'       => null,
                'pollRunId'      => null,
                'processingMode' => null,
            ]);
        }

        $query = ResumeOptimizerRun::where('user_id', $request->user()->id);

        if ($request->filled('run')) {
            $run = $query->where('id', (int) $request->input('run'))->first();
        }

        $run = $run ?? $query->latest()->first();

        $pageStatus = 'empty';
        $analysis   = null;

        if ($run) {
            if ($run->isFailed()) {
                $pageStatus = 'failed';
            } elseif ($run->isAnalyzing()) {
                $pageStatus = 'analyzing';
            } elseif ($run->isGenerating()) {
                $pageStatus = 'generating';
            } elseif ($run->isCompleted()) {
                $pageStatus = 'completed';
                $analysis   = $run->analysis_result;
            } elseif ($run->isAnalyzed()) {
                $pageStatus = 'analyzed';
                $analysis   = $run->analysis_result;
            }
        }

        if ($run && $request->boolean('analyzing') && ! $run->isAnalyzing()) {
            return redirect()->route('user.resume.ai-optimizer', ['run' => $run->id]);
        }

        if ($run && $request->boolean('generating') && ! $run->isGenerating()) {
            return redirect()->route('user.resume.ai-optimizer', ['run' => $run->id]);
        }

        $pollRunId = ($run && in_array($pageStatus, ['analyzing', 'generating'], true))
            ? $run->id
            : null;

        $processingMode = in_array($pageStatus, ['analyzing', 'generating'], true) ? $pageStatus : null;

        return view('pages.resume_ai_optimizer', [
            'run'        => $run,
            'pageStatus' => $pageStatus,
            'analysis'   => $analysis,
            'pollRunId'        => $pollRunId,
            'processingMode'   => $processingMode,
        ]);
    }

    public function upload(ResumeOptimizerUploadRequest $request): JsonResponse
    {
        $file      = $request->file('resume');
        $extension = strtolower($file->getClientOriginalExtension());
        $disk      = config('resume.optimizer_disk', 'public');
        $directory = 'resumes/optimizer/' . date('Y/m');
        $fileName  = Str::uuid() . '.' . $extension;

        $filePath = $file->storeAs($directory, $fileName, $disk);

        $run = ResumeOptimizerRun::create([
            'user_id'            => $request->user()->id,
            'original_file_name' => $file->getClientOriginalName(),
            'original_file_path' => $filePath,
            'file_type'          => $extension,
            'status'             => ResumeOptimizerRun::STATUS_UPLOADED,
        ]);

        AnalyzeResumeOptimizerJob::dispatch($run->id);

        return response()->json([
            'success' => true,
            'message' => 'Resume uploaded. Analysis has started.',
            'run_id'  => $run->id,
            'status'  => 'analyzing',
        ]);
    }

    public function status(Request $request, ResumeOptimizerRun $run): JsonResponse
    {
        $this->authorizeRun($request, $run);

        $this->failStaleProcessing($run);

        $payload = [
            'success'  => true,
            'run_id'   => $run->id,
            'status'   => $run->status,
            'file_name'=> $run->original_file_name,
        ];

        if ($run->isFailed()) {
            return response()->json(array_merge($payload, [
                'success' => false,
                'error'   => $run->error_message ?? 'Processing failed.',
            ]));
        }

        if ($run->isAnalyzed() || $run->isCompleted()) {
            $payload['score']    = $run->score();
            $payload['analysis'] = $run->analysis_result;
        }

        if ($run->isCompleted()) {
            $payload['generated_ready'] = true;
            $payload['download_url']    = route('user.resume.ai-optimizer.download', $run);
        }

        if ($run->isAnalyzing() || $run->isGenerating()) {
            $payload['progress'] = $run->processingProgress();
        }

        return response()->json($payload);
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'run_id' => ['required', 'integer', 'exists:resume_optimizer_runs,id'],
        ]);

        $run = ResumeOptimizerRun::where('id', $request->input('run_id'))
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (! $run->isAnalyzed()) {
            return response()->json([
                'success' => false,
                'message' => 'Resume must be analyzed before generating an optimized version.',
            ], 422);
        }

        $run->update([
            'status'                => ResumeOptimizerRun::STATUS_GENERATING,
            'processing_started_at' => now(),
            'error_message'         => null,
        ]);

        GenerateOptimizedResumeJob::dispatch($run->id);

        return response()->json([
            'success'     => true,
            'message'     => 'Generating your optimized resume…',
            'run_id'      => $run->id,
            'status'      => 'generating',
            'redirect_url'=> route('user.resume.ai-optimizer', ['run' => $run->id, 'generating' => 1]),
        ]);
    }

    public function download(Request $request, ResumeOptimizerRun $run): StreamedResponse
    {
        $this->authorizeRun($request, $run);

        if (! $run->isCompleted() || ! $run->generated_file_path) {
            abort(404, 'Optimized resume is not available yet.');
        }

        $disk = config('resume.optimizer_disk', 'public');

        if (! Storage::disk($disk)->exists($run->generated_file_path)) {
            abort(404, 'File not found.');
        }

        $downloadName = 'optimized_resume_' . $run->user_id . '_' . $run->updated_at->format('Ymd_His') . '.pdf';

        return Storage::disk($disk)->download($run->generated_file_path, $downloadName);
    }

    private function authorizeRun(Request $request, ResumeOptimizerRun $run): void
    {
        if ($run->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function failStaleProcessing(ResumeOptimizerRun $run): void
    {
        if (! $run->isGenerating() && ! $run->isAnalyzing()) {
            return;
        }

        $started = $run->processing_started_at ?? $run->updated_at;
        if (! $started || $started->diffInMinutes(now()) < 5) {
            return;
        }

        $run->update([
            'status'        => ResumeOptimizerRun::STATUS_FAILED,
            'error_message' => 'Processing timed out. Please click Generate again or re-upload your resume.',
        ]);
    }
}
