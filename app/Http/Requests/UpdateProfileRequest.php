<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'name'  => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ];

        if ($user->isUser()) {
            $rules += [
                'full_name'           => ['nullable', 'string', 'max:120'],
                'location'            => ['nullable', 'string', 'max:120'],
                'current_title'       => ['nullable', 'string', 'max:120'],
                'experience_years'    => ['nullable', 'integer', 'min:0', 'max:50'],
                'summary'             => ['nullable', 'string', 'max:2000'],
            ];
        }

        return $rules;
    }
}
