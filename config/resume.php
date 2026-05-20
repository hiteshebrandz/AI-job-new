<?php

return [
    // Use full path on Windows when PHP's PATH omits Python (e.g. C:\Python314\python.exe).
    'python_path' => env('RESUME_PYTHON_PATH', 'python'),
    'python_extra_paths' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('RESUME_PYTHON_EXTRA_PATHS', ''))
    ))),
    'parser_script' => base_path('scripts/parse_resume.py'),
    'max_upload_kb' => (int) env('RESUME_MAX_UPLOAD_KB', 10240),
    'allowed_extensions' => ['pdf', 'docx', 'txt'],
    'disk' => env('RESUME_DISK', 'local'),
    'queue' => env('RESUME_QUEUE', false),
    'timeout' => (int) env('RESUME_PARSE_TIMEOUT', 120),
];
