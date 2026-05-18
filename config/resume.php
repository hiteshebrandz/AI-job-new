<?php

return [
    'python_path' => env('RESUME_PYTHON_PATH', 'python'),
    'parser_script' => base_path('scripts/parse_resume.py'),
    'max_upload_kb' => (int) env('RESUME_MAX_UPLOAD_KB', 10240),
    'allowed_extensions' => ['pdf', 'docx', 'txt'],
];
