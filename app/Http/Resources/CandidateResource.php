<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'candidate_code'     => $this->candidate_code,
            'full_name'          => $this->full_name,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'location'           => $this->location,
            'current_title'      => $this->current_title,
            'experience_years'   => $this->experience_years,
            'seniority_level'    => $this->seniority_level,
            'skills'             => $this->skills ?? [],
            'education'          => $this->education,
            'university'         => $this->university,
            'graduation_year'    => $this->graduation_year,
            'previous_companies' => $this->previous_companies,
            'ai_score'           => $this->ai_score,
            'summary'            => $this->summary,
        ];
    }
}
