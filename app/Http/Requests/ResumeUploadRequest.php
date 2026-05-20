<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ResumeUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $maxKb   = config('resume.max_upload_kb');
        $allowed = implode(',', config('resume.allowed_extensions'));

        return [
            'resume' => [
                'required',
                'file',
                "max:{$maxKb}",
                "mimes:{$allowed}",
            ],
        ];
    }

    /**
     * Extra validation: verify the real mime type using finfo (not just extension).
     */
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
                'text/plain',
            ];

            $realMime = $file->getMimeType();

            if ($realMime && ! in_array($realMime, $allowedMimes, true)) {
                $v->errors()->add('resume', "File type '{$realMime}' is not allowed. Upload a PDF, DOC, DOCX, or TXT file.");
            }

            // Block files that look like executables regardless of extension
            $bytes = file_get_contents($file->getRealPath(), false, null, 0, 4);
            if ($bytes && (
                substr($bytes, 0, 2) === 'MZ' ||  // Windows PE executable
                substr($bytes, 0, 4) === "\x7FELF"  // Linux ELF executable
            )) {
                $v->errors()->add('resume', 'Executable files are not allowed.');
            }
        });
    }
}
