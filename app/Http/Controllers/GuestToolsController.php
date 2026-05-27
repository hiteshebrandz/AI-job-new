<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeOptimizerUploadRequest;
use App\Http\Requests\ResumeUploadRequest;
use App\Jobs\AnalyzeResumeOptimizerJob;
use App\Models\ResumeOptimizerRun;
use App\Models\ResumeParsingLog;
use App\Services\GuestToolAttemptService;
use App\Services\ResumeParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestToolsController extends Controller
{
    public function __construct(
        private GuestToolAttemptService $guestAttempts
    ) {}

    public function index(Request $request): View
    {
        $activeTool = in_array($request->query('tool'), ['resume', 'ats'], true)
            ? $request->query('tool')
            : 'resume';

        return view('pages.guest_tools', [
            'activeTool'  => $activeTool,
            'attempts'    => $this->guestAttempts->summary($request),
            'maxAttempts' => GuestToolAttemptService::MAX_ATTEMPTS,
        ]);
    }

    public function uploadResumeTest(ResumeUploadRequest $request, ResumeParserService $parser): JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'success'      => false,
                'message'      => 'You are already signed in. Use your dashboard tools instead.',
                'redirect_url' => route('user.resume.upload'),
            ], 422);
        }

        $guestSessionId = $this->guestAttempts->guestSessionId($request);
        $file = $request->file('resume');
        $storedPath = ResumeParserService::storeUploadedFile($file);

        $log = ResumeParsingLog::create([
            'user_id'          => null,
            'guest_session_id' => $guestSessionId,
            'file_name'        => $file->getClientOriginalName(),
            'file_path'        => $storedPath,
            'parsing_status'   => ResumeParsingLog::STATUS_PENDING,
        ]);

        $this->guestAttempts->increment($request, GuestToolAttemptService::TOOL_RESUME_TEST);

        $useQueue = config('resume.queue', false) && config('queue.default') !== 'sync';

        if ($useQueue) {
            $parser->dispatchParse($log);
            $log->refresh();

            return response()->json($this->resumeTestResponse($request, $log, pending: true));
        }

        try {
            $parser->parse($log);
        } catch (\Throwable $e) {
            $log->update([
                'parsing_status' => ResumeParsingLog::STATUS_FAILED,
                'error_message'  => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Resume parsing failed. Please try another file or format.',
                'error'   => $e->getMessage(),
            ], 422);
        }

        $log->refresh();

        return response()->json($this->resumeTestResponse($request, $log));
    }

    public function resumeTestStatus(Request $request, ResumeParsingLog $log): JsonResponse
    {
        $this->authorizeGuestLog($request, $log);

        $payload = [
            'success'  => $log->parsing_status === ResumeParsingLog::STATUS_COMPLETED,
            'status'   => $log->parsing_status,
            'log_id'   => $log->id,
            'ai_score' => $log->ai_score,
            'data'     => $log->parsing_status === ResumeParsingLog::STATUS_COMPLETED
                ? $log->toRegistrationPayload()
                : null,
            'error'    => $log->error_message,
        ];

        if ($log->parsing_status === ResumeParsingLog::STATUS_FAILED) {
            return response()->json(array_merge($payload, [
                'success' => false,
                'message' => $log->error_message ?? 'Parsing failed.',
            ]), 422);
        }

        return response()->json($payload);
    }

    public function uploadAtsCheck(ResumeOptimizerUploadRequest $request): JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'success'      => false,
                'message'      => 'You are already signed in. Use your dashboard ATS optimizer instead.',
                'redirect_url' => route('user.resume.ai-optimizer'),
            ], 422);
        }

        $guestSessionId = $this->guestAttempts->guestSessionId($request);
        $file = $request->file('resume');
        $extension = strtolower($file->getClientOriginalExtension());
        $disk = config('resume.optimizer_disk', 'public');
        $directory = 'resumes/optimizer/guest/' . date('Y/m');
        $fileName = Str::uuid() . '.' . $extension;
        $filePath = $file->storeAs($directory, $fileName, $disk);

        $run = ResumeOptimizerRun::create([
            'user_id'            => null,
            'guest_session_id'   => $guestSessionId,
            'original_file_name' => $file->getClientOriginalName(),
            'original_file_path' => $filePath,
            'file_type'          => $extension,
            'status'             => ResumeOptimizerRun::STATUS_UPLOADED,
        ]);

        $this->guestAttempts->increment($request, GuestToolAttemptService::TOOL_ATS_CHECK);

        AnalyzeResumeOptimizerJob::dispatch($run->id);

        return response()->json([
            'success'           => true,
            'message'           => 'Resume uploaded. ATS analysis has started.',
            'run_id'            => $run->id,
            'status'            => 'analyzing',
            'poll_url'          => route('tools.guest.ats.status', $run),
            'attempts'          => $this->guestAttempts->toolSummary($request, GuestToolAttemptService::TOOL_ATS_CHECK),
        ]);
    }

    public function atsCheckStatus(Request $request, ResumeOptimizerRun $run): JsonResponse
    {
        $this->authorizeGuestRun($request, $run);

        $payload = [
            'success'   => true,
            'run_id'    => $run->id,
            'status'    => $run->status,
            'file_name' => $run->original_file_name,
        ];

        if ($run->isFailed()) {
            return response()->json(array_merge($payload, [
                'success' => false,
                'error'   => $run->error_message ?? 'Processing failed.',
            ]));
        }

        if ($run->isAnalyzed() || $run->isCompleted()) {
            $payload['score'] = $run->score();
            $payload['analysis'] = $run->analysis_result;
        }

        if ($run->isAnalyzing() || $run->isGenerating()) {
            $payload['progress'] = $run->processingProgress();
        }

        return response()->json($payload);
    }

    private function resumeTestResponse(Request $request, ResumeParsingLog $log, bool $pending = false): array
    {
        $response = [
            'success'  => ! $pending,
            'message'  => $pending
                ? 'Resume uploaded. Parsing in progress.'
                : 'Resume parsed successfully.',
            'log_id'   => $log->id,
            'status'   => $log->parsing_status,
            'ai_score' => $log->ai_score,
            'poll_url' => route('tools.guest.resume.status', $log),
            'attempts' => $this->guestAttempts->toolSummary($request, GuestToolAttemptService::TOOL_RESUME_TEST),
        ];

        if (! $pending && $log->parsed_data) {
            $response['data'] = $log->toRegistrationPayload();

            if (! empty($log->parsed_data['parse_warning'])) {
                $response['parse_warning'] = $log->parsed_data['parse_warning'];
            }
        }

        return $response;
    }

    private function authorizeGuestLog(Request $request, ResumeParsingLog $log): void
    {
        if ($request->user() && $log->user_id === $request->user()->id) {
            return;
        }

        abort_unless(
            $log->guest_session_id && $log->guest_session_id === $this->guestAttempts->guestSessionId($request),
            403
        );
    }

    private function authorizeGuestRun(Request $request, ResumeOptimizerRun $run): void
    {
        if ($request->user() && $run->user_id === $request->user()->id) {
            return;
        }

        abort_unless(
            $run->guest_session_id && $run->guest_session_id === $this->guestAttempts->guestSessionId($request),
            403
        );
    }
}
