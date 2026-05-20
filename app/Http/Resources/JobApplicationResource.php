<?php

namespace App\Http\Resources;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'status_label' => JobApplication::statusLabel($this->status),
            'match_score'  => $this->match_score,
            'applied_at'   => $this->applied_at?->toIso8601String(),
            'job'          => $this->whenLoaded('job', fn () => [
                'id'           => $this->job->id,
                'title'        => $this->job->title,
                'company_name' => $this->job->company_name,
                'location'     => $this->job->location,
            ]),
            'candidate' => $this->whenLoaded('user', fn () => $this->user?->candidate ? [
                'full_name' => $this->user->candidate->full_name,
                'ai_score'  => $this->user->candidate->ai_score,
                'skills'    => $this->user->candidate->skills ?? [],
            ] : null),
        ];
    }
}
