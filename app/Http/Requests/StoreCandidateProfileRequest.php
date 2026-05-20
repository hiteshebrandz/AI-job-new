<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'parsing_log_id' => ['required', 'exists:resume_parsing_logs,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'current_title' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'seniority_level' => ['nullable', 'string', 'max:100'],
            'previous_companies' => ['nullable', 'string'],
            'education' => ['nullable', 'string', 'max:255'],
            'university' => ['nullable', 'string', 'max:255'],
            'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
            'summary' => ['nullable', 'string'],
            'ai_recommendation' => ['nullable', 'string'],
            'ai_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'create_new_account' => ['nullable', 'boolean'],
        ];
    }
}
