# Resume parsing setup

## Requirements

- PHP 8.1+ with Laravel 10
- MySQL
- Python 3.10+ with packages from `scripts/requirements.txt`

## Install

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
pip install -r scripts/requirements.txt
```

## Environment

```env
RESUME_PYTHON_PATH=C:\Python314\python.exe
```

On Windows, PHP started by Apache/XAMPP/`php artisan serve` often does **not** inherit your terminal `PATH`. Use the full path to `python.exe` (run `where python` in PowerShell). The app also auto-searches `C:\Python3xx\python.exe` and `%LOCALAPPDATA%\Programs\Python\`.

```env
RESUME_PYTHON_PATH=C:\Python314\python.exe
RESUME_MAX_UPLOAD_KB=10240
RESUME_DISK=local
RESUME_QUEUE=false
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,127.0.0.1:8000
```

For public resume storage:

```env
RESUME_DISK=public
```

Then run `php artisan storage:link`.

## Async parsing (optional)

```env
RESUME_QUEUE=true
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## API endpoints

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/resume/upload` | Sanctum (session cookie or token) |
| GET | `/api/resume/parse/{log}` | Sanctum |
| POST | `/api/resume/profile` | Sanctum |
| POST | `/api/register` | Public |
| POST | `/api/login` | Public |

## Test flow

1. Log in as a candidate user.
2. Open `/user/resume/upload`.
3. Upload a PDF, DOCX, or TXT resume.
4. Confirm the form auto-fills.
5. Edit fields and click **Create Profile**.

## Run tests

```bash
php artisan test --filter=ResumeUploadTest
```
