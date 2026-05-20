<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateCandidateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResumeUploadRequest;
use App\Http\Requests\StoreCandidateProfileRequest;
use App\Models\ResumeParsingLog;
use App\Services\ResumeParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResumeUploadController extends Controller
{
    public function upload(ResumeUploadRequest $request, ResumeParserService $parser): JsonResponse
    {
        $file = $request->file('resume');
        $storedPath = ResumeParserService::storeUploadedFile($file);

        $log = ResumeParsingLog::create([
            'user_id' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'parsing_status' => ResumeParsingLog::STATUS_PENDING,
        ]);

        $useQueue = config('resume.queue', false) && config('queue.default') !== 'sync';

        if ($useQueue) {
            $parser->dispatchParse($log);
            $log->refresh();

            return response()->json($this->uploadResponse($log, $request->user()->isHr() ? 'hr' : 'user', pending: true));
        }

        try {
            $parser->parse($log);
        } catch (\Throwable $e) {
            $log->update([
                'parsing_status' => ResumeParsingLog::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Resume parsing failed. Please try another file or format.',
                'error' => $e->getMessage(),
            ], 422);
        }

        $log->refresh();

        return response()->json($this->uploadResponse($log, $request->user()->isHr() ? 'hr' : 'user'));
    }

    public function status(Request $request, ResumeParsingLog $log): JsonResponse
    {
        $this->authorizeLog($request, $log);

        $payload = [
            'success' => $log->parsing_status === ResumeParsingLog::STATUS_COMPLETED,
            'status' => $log->parsing_status,
            'log_id' => $log->id,
            'ai_score' => $log->ai_score,
            'data' => $log->parsing_status === ResumeParsingLog::STATUS_COMPLETED
                ? $log->toRegistrationPayload()
                : null,
            'error' => $log->error_message,
        ];

        if ($log->parsing_status === ResumeParsingLog::STATUS_COMPLETED
            && ! empty($log->parsed_data['parse_warning'])) {
            $payload['parse_warning'] = $log->parsed_data['parse_warning'];
        }

        if ($log->parsing_status === ResumeParsingLog::STATUS_FAILED) {
            return response()->json(array_merge($payload, [
                'success' => false,
                'message' => $log->error_message ?? 'Parsing failed.',
            ]), 422);
        }

        return response()->json($payload);
    }

    public function storeProfile(
        StoreCandidateProfileRequest $request,
        CreateCandidateProfileAction $action
    ): JsonResponse {
        $validated = $request->validated();
        $authUser = $request->user();
        $forceNew = $authUser->isHr();

        $result = $action->execute($validated, $authUser, $forceNew);

        return response()->json([
            'success' => true,
            'message' => $result['is_new_account']
                ? 'Candidate account created and welcome email sent.'
                : 'Profile saved successfully.',
            'redirect' => $authUser->isHr()
                ? route('hr.applicants')
                : route('user.dashboard'),
            'candidate_code' => $result['candidate']->candidate_code,
        ]);
    }

    private function authorizeLog(Request $request, ResumeParsingLog $log): void
    {
        abort_unless($log->user_id === $request->user()->id, 403);
    }

    private function uploadResponse(ResumeParsingLog $log, string $routePrefix, bool $pending = false): array
    {
        $response = [
            'success' => ! $pending,
            'message' => $pending
                ? 'Resume uploaded. Parsing in progress.'
                : 'Resume parsed successfully.',
            'log_id' => $log->id,
            'status' => $log->parsing_status,
            'file_name' => $log->file_name,
            'resume_url' => route("{$routePrefix}.resume.preview", $log),
            'poll_url' => request()->is('api/*')
                ? url("/api/resume/parse/{$log->id}")
                : route("{$routePrefix}.resume.parse.status", $log),
        ];

        if (! $pending && $log->parsed_data) {
            $response['data'] = $log->toRegistrationPayload();

            if (! empty($log->parsed_data['parse_warning'])) {
                $response['parse_warning'] = $log->parsed_data['parse_warning'];
            }
        }

        return $response;
    }
}
