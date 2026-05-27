<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ResumeOptimizerUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null || $this->routeIs('tools.guest.*');
    }

    public function rules(): array
    {
        $maxKb = config('resume.optimizer_max_upload_kb', config('resume.max_upload_kb'));

        return [
            'resume' => [
                'required',
                'file',
                "max:{$maxKb}",
                'mimes:pdf,doc,docx',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $file = $this->file('resume');
            if (! $file || ! $file->isValid()) {
                return;
            }

            $allowedMimes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];

            $realMime = $file->getMimeType();

            if ($realMime && ! in_array($realMime, $allowedMimes, true)) {
                $v->errors()->add('resume', "File type '{$realMime}' is not allowed. Upload a PDF, DOC, or DOCX file.");
            }

            $bytes = file_get_contents($file->getRealPath(), false, null, 0, 4);
            if ($bytes && (
                substr($bytes, 0, 2) === 'MZ' ||
                substr($bytes, 0, 4) === "\x7FELF"
            )) {
                $v->errors()->add('resume', 'Executable files are not allowed.');
            }
        });
    }
}
