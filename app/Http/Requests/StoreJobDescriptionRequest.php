<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreJobDescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isHr() ?? false;
    }

    public function rules(): array
    {
        $maxKb = config('resume.max_upload_kb');
        $allowed = implode(',', config('resume.allowed_extensions'));

        return [
            'title' => ['nullable', 'string', 'max:255'],
            'jd_text' => ['nullable', 'string', 'max:50000'],
            'jd_file' => ['nullable', 'file', "max:{$maxKb}", "mimes:{$allowed}"],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $text = trim((string) $this->input('jd_text'));
            if (in_array(strtolower($text), ['null', 'undefined', 'nil', 'none'], true)) {
                $text = '';
            }
            $file = $this->file('jd_file');

            if ($text === '' && ! $file) {
                $v->errors()->add('jd_text', 'Paste a job description or upload a file.');
            }
        });
    }
}
