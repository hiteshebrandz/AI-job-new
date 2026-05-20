<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'slug'                => $this->slug,
            'company_name'        => $this->company_name,
            'location'            => $this->location,
            'job_type'            => $this->job_type,
            'work_mode'           => $this->work_mode,
            'min_salary'          => $this->min_salary,
            'max_salary'          => $this->max_salary,
            'experience_required' => $this->experience_required,
            'status'              => $this->status,
            'application_deadline'=> $this->application_deadline?->toDateString(),
            'skills_required'     => $this->skillsList(),
            'match_score'         => $this->match_score ?? null, // attached by controller
            'created_at'          => $this->created_at?->toIso8601String(),
        ];
    }
}
