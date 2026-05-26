<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobDescriptionRequest;
use App\Models\CandidateMatch;
use App\Models\JobDescription;
use App\Models\User;
use App\Services\ConversationService;
use App\Services\JobDescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiHiringController extends Controller
{
    public function __construct(
        private JobDescriptionService $jobDescriptionService,
        private ConversationService $conversationService,
    ) {}

    public function index(): View
    {
        $descriptions = JobDescription::query()
            ->where('hr_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('pages.hr.ai-hiring.index', [
            'activeNav' => 'ai-hiring',
            'descriptions' => $descriptions,
        ]);
    }

    public function create(): View
    {
        return view('pages.hr.ai-hiring.create', [
            'activeNav' => 'ai-hiring',
            'maxBytes' => config('resume.max_upload_kb') * 1024,
        ]);
    }

    public function store(StoreJobDescriptionRequest $request): RedirectResponse
    {
        $jd = $this->jobDescriptionService->create(
            $request->user(),
            $request->input('title'),
            $request->input('jd_text'),
            $request->file('jd_file'),
        );

        return redirect()
            ->route('hr.ai-hiring.matches', $jd)
            ->with('status', 'Job description submitted. AI is analyzing and matching candidates…');
    }

    public function status(JobDescription $jobDescription): JsonResponse
    {
        $this->authorizeJd($jobDescription);

        $matchCount = $jobDescription->candidateMatches()->count();

        return response()->json([
            'status' => $jobDescription->status,
            'status_label' => $jobDescription->statusLabel(),
            'analysis_error' => $jobDescription->analysis_error,
            'match_count' => $matchCount,
            'ready' => $jobDescription->status === JobDescription::STATUS_COMPLETED,
            'failed' => $jobDescription->status === JobDescription::STATUS_FAILED,
        ]);
    }

    public function matches(Request $request, JobDescription $jobDescription): View
    {
        $this->authorizeJd($jobDescription);

        $matches = $this->filteredMatchesQuery($request, $jobDescription)
            ->paginate(18)
            ->withQueryString();

        return view('pages.hr.ai-hiring.matches', [
            'activeNav' => 'ai-hiring',
            'jobDescription' => $jobDescription,
            'matches' => $matches,
            'filters' => $request->only(['search', 'min_score', 'skill', 'min_experience', 'location']),
        ]);
    }

    public function matchesJson(Request $request, JobDescription $jobDescription): JsonResponse
    {
        $this->authorizeJd($jobDescription);

        $paginator = $this->filteredMatchesQuery($request, $jobDescription)->paginate(18);

        return response()->json([
            'status' => $jobDescription->status,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function showCandidate(JobDescription $jobDescription, User $user): View
    {
        $this->authorizeJd($jobDescription);
        abort_unless($user->role === User::ROLE_USER, 404);

        $user->load('candidate');
        $match = CandidateMatch::query()
            ->where('job_description_id', $jobDescription->id)
            ->where('user_id', $user->id)
            ->first();

        return view('pages.hr.ai-hiring.candidate', [
            'activeNav' => 'ai-hiring',
            'jobDescription' => $jobDescription,
            'jobSeeker' => $user,
            'candidate' => $user->candidate,
            'match' => $match,
        ]);
    }

    public function connect(Request $request, JobDescription $jobDescription, User $user): RedirectResponse
    {
        $this->authorizeJd($jobDescription);
        abort_unless($user->role === User::ROLE_USER, 404);

        $conversation = $this->conversationService->findOrCreate(
            $request->user(),
            $user,
            $jobDescription,
        );

        $opener = $request->input('message')
            ?: "Hi, we think you're a strong fit for {$jobDescription->title}. Would you like to discuss this opportunity?";

        if ($conversation->messages()->count() === 0) {
            app(\App\Services\MessageService::class)->send($conversation, $request->user(), $opener);
        }

        return redirect()->route('hr.messages.show', $conversation);
    }

    private function authorizeJd(JobDescription $jobDescription): void
    {
        abort_unless($jobDescription->hr_id === auth()->id(), 403);
    }

    private function filteredMatchesQuery(Request $request, JobDescription $jobDescription)
    {
        $query = CandidateMatch::query()
            ->where('job_description_id', $jobDescription->id)
            ->with(['user.candidate', 'candidate'])
            ->orderByDesc('match_score');

        if ($min = $request->integer('min_score')) {
            $query->where('match_score', '>=', min(100, max(0, $min)));
        }

        if ($skill = trim((string) $request->input('skill'))) {
            $like = '%'.$skill.'%';
            $query->whereHas('candidate', function ($q) use ($like) {
                $q->where('skills', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('current_title', 'like', $like);
            });
        }

        if ($minExp = $request->integer('min_experience')) {
            $query->whereHas('candidate', fn ($q) => $q->where('experience_years', '>=', $minExp));
        }

        if ($location = trim((string) $request->input('location'))) {
            $like = '%'.$location.'%';
            $query->whereHas('candidate', fn ($q) => $q->where('location', 'like', $like));
        }

        if ($search = trim((string) $request->input('search'))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->whereHas('user', function ($u) use ($like) {
                    $u->where('name', 'like', $like)->orWhere('email', 'like', $like);
                })->orWhereHas('candidate', function ($c) use ($like) {
                    $c->where('full_name', 'like', $like)
                        ->orWhere('current_title', 'like', $like);
                });
            });
        }

        return $query;
    }
}
