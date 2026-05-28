# TalentSync AI — Complete Project Analysis Report

> **Note for documentation work:** This repo has **no Angular frontend**. The UI is **Laravel Blade** + vanilla JS in [`public/js/`](public/js/). Your user rules mention Angular 19, but the actual codebase is server-rendered Laravel.

---

## 1. Project Name

**TalentSync AI** (repository folder: `AI-job`, `APP_NAME` configurable in `.env`)

---

## 2. Project Purpose

An **intelligent HR and job-seeking platform** that connects three types of users:

- **Candidates (user role)** — upload resumes, get AI analytics, optimize resumes for ATS, browse jobs, apply, save jobs, and chat with HR.
- **HR / employers (hr role)** — post jobs, review applicants, use **AI hiring** (upload job descriptions and match candidates), and message candidates.
- **Administrators (admin role)** — oversee platform analytics and manage all job applications globally.

The system combines **traditional job board features** with **Python + LLM-powered resume/JD intelligence**.

---

## 3. Problem Statement

Traditional hiring is slow and manual:

- HR spends hours reading resumes and job descriptions.
- Candidates do not know how ATS systems score their resumes.
- Job–candidate fit is often guessed instead of measured.
- Communication between HR and candidates is scattered across email.

Organizations need a **single platform** where resumes are parsed automatically, match scores are computed, AI explains gaps, and hiring workflows (apply → review → interview → hire) are tracked.

---

## 4. Main Objective

Build an end-to-end **AI-assisted recruitment system** where:

1. Resumes are parsed and stored as structured candidate profiles.
2. Jobs and JDs are analyzed and matched to candidates with scores and reasons.
3. Candidates get recommendations, analytics, and resume optimization.
4. HR manages jobs, applicants, and AI hiring pipelines.
5. Admins monitor applications and analytics.

---

## 5. Target Users

| Role | Who they are | Primary goals |
|------|----------------|---------------|
| **Candidate (`user`)** | Job seekers, students, professionals | Find jobs, improve resume, apply, track applications |
| **HR (`hr`)** | Recruiters, hiring managers, company HR | Post jobs, screen applicants, AI-match candidates to JDs, message candidates |
| **Admin (`admin`)** | Platform owner / super user | View analytics, manage all applications, oversight |
| **Guest (unauthenticated)** | Visitors trying tools before signup | Free resume test + ATS check (limited to 3 attempts per tool) |

**Demo accounts** (after `php artisan db:seed`): see [SETUP.md](SETUP.md) — `user@gmail.com`, `hr@gmail.com`, `admin@gmail.com` (password = email).

---

## 6. Main Features

### Public / marketing
- Landing page ([`resources/views/pages/landing_page.blade.php`](resources/views/pages/landing_page.blade.php))
- Guest tools: resume parsing test + ATS optimizer preview ([`GuestToolsController`](app/Http/Controllers/GuestToolsController.php))
- Executive suite marketing pages (`/suite/1`, `/suite/2`)
- Sitemap

### Candidate features
- Dashboard with stats
- Job recommendations with **match percentage** ([`JobRecommendationService`](app/Services/JobRecommendationService.php))
- Job details, apply, save jobs
- Resume upload → parse → preview → create profile ([`ResumeController`](app/Http/Controllers/ResumeController.php), [`ResumeParserService`](app/Services/ResumeParserService.php))
- **Resume analytics** (AI scores, skills gap, improvements) ([`UserResumeController`](app/Http/Controllers/UserResumeController.php), [`PythonResumeAnalyzerService`](app/Services/PythonResumeAnalyzerService.php))
- **AI resume optimizer** (ATS analysis + downloadable optimized resume) ([`ResumeOptimizerController`](app/Http/Controllers/ResumeOptimizerController.php))
- Saved jobs, applied jobs
- In-app notifications for application status
- Messaging with HR ([`UserMessageController`](app/Http/Controllers/UserMessageController.php))
- Profile + notification settings

### HR features
- Dashboard
- Job CRUD (create, edit, delete, toggle active/inactive) ([`JobController`](app/Http/Controllers/JobController.php))
- Applicant management (list job seekers, view applications, update status, download resume)
- Resume upload (HR can also parse resumes for candidates)
- **AI Hiring**: upload JD (text or file) → analyze → match all candidates → connect/message ([`AiHiringController`](app/Http/Controllers/Hr/AiHiringController.php))
- HR messaging with candidates
- Profile + notification settings

### Admin features
- Dashboard
- Analytics dashboard (charts via JSON API)
- Global job applications list/detail, status updates, resume download
- Profile management

### API (Sanctum token clients)
- Register, login, logout
- Resume upload/parse/profile
- Resume analytics for candidates

---

## 7. Complete Workflow of the Project

**High-level lifecycle:**

1. **Guest** may try resume test or ATS check (session-limited).
2. **User registers** as `user` or `hr` (not admin via public register).
3. **Candidate** uploads resume → Python parses → user confirms profile → `candidates` row created/updated.
4. **Candidate** optionally runs AI analytics and optimizer on resume files.
5. **HR** creates **active** jobs linked to their account (and optional `companies` record).
6. **Candidate** sees recommendations (match score from skills, experience, education, keywords).
7. **Candidate applies** → `job_applications` row + optional notification to candidate when HR/admin changes status.
8. **HR** reviews applicants OR uses **AI Hiring** with a `job_descriptions` record → `candidate_matches` → can **connect** (starts `conversations` + messages).
9. **Admin** monitors all applications and platform analytics.

**Background processing:** Long tasks dispatch Laravel **Jobs** (parse resume, analyze resume, optimizer analyze/generate, analyze JD, match candidates). Default queue is `sync` in `.env.example` (runs inline); production can use `database` + `queue:work`.

---

## 8. Step-by-step User Flow (Candidate)

1. Visit `/` → click Register or Login.
2. Register with role **Candidate** → redirected to `/user/dashboard`.
3. Go to **Resume Upload** (`/user/resume/upload`) → upload PDF/DOCX/TXT.
4. Poll parse status → preview parsed fields → submit **Create Profile** → `candidates` + skills synced.
5. Optional: **Resume Analytics** → upload → AI analysis stored in `resume_files` + `resume_analytics`.
6. Optional: **AI Optimizer** → upload → analysis → generate optimized PDF → download.
7. **Job Recommendations** → open job → see match % → Apply or Save.
8. **Applied Jobs** / **Saved Jobs** to track.
9. Receive **notifications** when application status changes.
10. If HR connects via AI hiring → **Messages** → chat in conversation thread.

---

## 9. Admin Flow

1. Login as `admin@gmail.com` → `/admin/dashboard`.
2. **Analytics** (`/admin/analytics`) — fetches `/admin/analytics/data` for charts (applications, users, jobs stats).
3. **Job Applications** (`/admin/job-applications`) — list all applications across HR/jobs.
4. Open application detail → change status (applied → under_review → shortlisted → interview → hired/rejected).
5. Download candidate resume file if available.
6. Update admin profile/password/photo.

Admin does **not** post jobs or run AI hiring in the current routes (HR-only).

---

## 10. System Architecture

**Pattern:** Monolithic Laravel application with **service layer** + **queued jobs** + **external Python subprocesses** for AI/document work.

**Layers:**
- **Presentation:** Blade views + `public/js/*.js` (fetch/XHR to Laravel routes)
- **HTTP:** Controllers + Form Requests + Middleware
- **Business logic:** `app/Services/*`, `app/Actions/*`, `app/Support/*` DTOs
- **Data:** Eloquent models + MySQL
- **Async:** `app/Jobs/*`
- **AI/document:** `scripts/` Python (parse_resume.py, resume_analyzer, resume_optimizer, jd_analyzer)

**Auth split:**
- **Web UI:** Session guard (`auth` middleware), CSRF on POST
- **API:** Laravel Sanctum bearer tokens (`auth:sanctum`)

---

## 11. Frontend Technologies Used

| Technology | Usage |
|------------|--------|
| **Laravel Blade** | All HTML pages in [`resources/views/`](resources/views/) |
| **Tailwind CSS** | CDN in layouts + per-page config in [`public/js/tailwind-config-*.js`](public/js/) |
| **Vanilla JavaScript** | [`public/js/`](public/js/) — polling, fetch, chat UI, uploads |
| **Vite** (minimal) | [`package.json`](package.json) — `resources/js/app.js`, axios in bootstrap; **most pages use `asset()` not `@vite`** |
| **No Angular/React SPA** | Not present in this repository |

---

## 12. Backend Technologies Used

| Technology | Version / role |
|------------|----------------|
| **PHP** | ^8.1 ([`composer.json`](composer.json)) |
| **Laravel** | 10.x |
| **Laravel Sanctum** | API tokens |
| **Guzzle** | HTTP client (framework dependency) |
| **MySQL** | Primary database |
| **Python 3.10+** | Resume parse, analytics, optimizer, JD analysis |
| **PHPUnit** | Tests in [`tests/`](tests/) |

---

## 13. Database Used

**MySQL** (configured via `DB_*` in `.env`). Default database name in example: `ai_job`.

**Laravel system tables:** `password_reset_tokens`, `failed_jobs`, `personal_access_tokens`, `notifications`.

---

## 14. APIs Used

### Internal REST API (`/api` prefix)

Documented in [`routes/api.php`](routes/api.php) — see Section 24.

### External APIs (via Python services)

Configured by `RESUME_AI_PROVIDER` (default `groq`):

- **Groq API** (`GROQ_API_KEY`, optional `GROQ_MODEL`, `GROQ_BASE_URL`)
- **OpenAI** (`OPENAI_API_KEY`) — supported in Python analyzer code
- **Google Gemini** (`GEMINI_API_KEY`) — supported in Python analyzer code

Resume **parsing** (`scripts/parse_resume.py`) uses local PDF/DOCX extraction (pdfplumber, python-docx) — not necessarily cloud LLM.

---

## 15. Third-party Services Used

- **Groq** (primary LLM for resume analytics, optimizer, JD analysis)
- **OpenAI / Gemini** (alternatives via env)
- **Python libraries:** pdfplumber, pypdf, python-docx, PyMuPDF, openai SDK, google-generativeai
- **Mail** (Laravel mail — Mailpit in dev per `.env.example`)
- **Optional:** AWS S3 (`AWS_*`), Redis, Pusher (configured but not central to core flows)
- **No payment gateway** in codebase

---

## 16. Authentication Flow

### Web (session)

1. User submits login form → [`AuthController@login`](app/Http/Controllers/AuthController.php).
2. `Auth::attempt()` validates email/password.
3. Session regenerated → [`AuthRedirect::dashboardFor()`](app/Support/AuthRedirect.php) sends user to role dashboard.
4. Logout invalidates session and CSRF token.

### API (Sanctum)

1. `POST /api/login` or `/api/register` → [`Api\AuthController`](app/Http/Controllers/Api/AuthController.php).
2. Returns JSON + **plain-text bearer token** (`createToken('api')`).
3. Client sends `Authorization: Bearer {token}` on protected routes.
4. `POST /api/logout` deletes current token.

### Password reset

- Web routes: forgot password → email token → reset form (Laravel `Password` facade).

**No JWT, no OAuth social login** in current code.

---

## 17. Authorization Logic

**Role stored on `users.role`:** `user` | `hr` | `admin`.

**Middleware [`EnsureUserHasRole`](app/Http/Middleware/EnsureUserHasRole.php)** (`role:user`, `role:hr`, `role:admin`):
- Unauthenticated → redirect login (web) or 401 JSON (API).
- Wrong role → redirect to own dashboard (web) or 403 JSON (API).

**Route groups** in [`routes/web.php`](routes/web.php):
- `/user/*` → `auth` + `role:user`
- `/hr/*` → `auth` + `role:hr`
- `/admin/*` → `auth` + `role:admin`

**Resource-level checks:**
- [`ConversationService::authorizeParticipant`](app/Services/ConversationService.php) — only HR or candidate in that conversation can read/send messages.
- HR applicants: scoped to jobs owned by logged-in HR.
- Job apply: only **active** jobs (`Job::STATUS_ACTIVE`).

**Registration:** Public register allows only `user` or `hr` — admin must be seeded manually.

---

## 18. Folder Structure Explanation

```
AI-job/
├── app/                    # Laravel application code
│   ├── Actions/            # Single-purpose actions (candidate profile, skills sync)
│   ├── Console/            # Artisan commands (Python setup check)
│   ├── Http/
│   │   ├── Controllers/    # Web + API + Hr + Admin controllers
│   │   ├── Middleware/     # Auth, role, guest tool limit, CSRF
│   │   ├── Requests/       # Form validation classes
│   │   └── Resources/      # API JSON transformers
│   ├── Jobs/               # Queue jobs (parse, analyze, match)
│   ├── Mail/               # Mailable classes
│   ├── Models/             # Eloquent models
│   ├── Notifications/      # Laravel notifications
│   ├── Providers/          # Service providers, route loading
│   ├── Services/           # Core business logic
│   └── Support/            # DTOs (AuthRedirect, CandidateJobProfile)
├── bootstrap/              # Laravel bootstrap
├── config/                 # app, auth, database, resume, sanctum, cors...
├── database/
│   ├── migrations/         # Schema definitions
│   └── seeders/            # Demo users + sample job
├── docs/                   # RESUME_PARSING_SETUP.md
├── public/                 # Web root (css, js, index.php)
├── resources/
│   ├── views/              # Blade templates (main UI)
│   └── js/                 # Minimal Vite entry
├── routes/                 # web.php, api.php, channels.php
├── scripts/                # Python AI tools + venv
│   ├── parse_resume.py
│   ├── resume_analyzer/
│   ├── resume_optimizer/
│   └── jd_analyzer/
├── storage/                # uploads, logs, framework cache
├── tests/                  # PHPUnit tests
├── vendor/                 # Composer packages
├── .env.example
├── composer.json
├── package.json
└── SETUP*.md               # Installation guides
```

---

## 19. Important Files and Their Purpose

| File | Purpose |
|------|---------|
| [`routes/web.php`](routes/web.php) | All browser routes by role |
| [`routes/api.php`](routes/api.php) | Token-based API |
| [`app/Http/Kernel.php`](app/Http/Kernel.php) | Middleware registration |
| [`config/resume.php`](config/resume.php) | Python paths, upload limits, optimizer settings |
| [`app/Services/ResumeParserService.php`](app/Services/ResumeParserService.php) | Orchestrates resume parsing |
| [`app/Services/JobMatchService.php`](app/Services/JobMatchService.php) | Weighted job–candidate match score |
| [`app/Services/JdCandidateMatchService.php`](app/Services/JdCandidateMatchService.php) | JD → candidate matching for AI hiring |
| [`scripts/parse_resume.py`](scripts/parse_resume.py) | Extract structured data from resume files |
| [`database/seeders/UserSeeder.php`](database/seeders/UserSeeder.php) | Demo accounts |
| [`resources/views/layouts/candidate.blade.php`](resources/views/layouts/candidate.blade.php) | Candidate shell UI |
| [`public/js/resume-upload.js`](public/js/resume-upload.js) | Client-side upload + polling |

---

## 20. All Modules Explanation

### Auth module
Login, register, logout, password reset; API token auth; role-based redirect.

### Candidate profile module
`Candidate` model linked 1:1 to `User`; skills via `candidate_skills` pivot; built from parsing or manual profile update.

### Resume parsing module
Upload → `resume_parsing_logs` → `ParseResumeJob` → Python → JSON `parsed_data` → user confirms → `CreateCandidateProfileAction`.

### Resume analytics module
Separate from parsing: `resume_files` + `resume_analytics`; `ProcessResumeAnalyticsJob` + Groq/OpenAI via Python.

### Resume optimizer module
`resume_optimizer_runs`; analyze then generate PDF; guest can try ATS check (limited).

### Job module
HR creates `jobs` (active/inactive); optional `companies`; slug for URLs.

### Application module
`job_applications` with status workflow and `match_score`; notifications on status change.

### Recommendation module
`JobRecommendationService` ranks active jobs for candidate profile.

### AI Hiring module (HR)
`job_descriptions` → analyze JD → `candidate_matches` → view/connect → `conversations`.

### Messaging module
`conversations` + `messages`; throttled POST; unread counts for badges.

### Guest tools module
Session UUID + max 3 attempts per tool (`GuestToolAttemptService`).

### Admin module
Cross-cutting application management and analytics JSON.

---

## 21. Database Tables

| Table | Purpose |
|-------|---------|
| `users` | Accounts: name, email, password, role, phone, photo, notification_settings JSON |
| `candidates` | Extended profile for job seekers (1:1 with user) |
| `companies` | Company metadata for jobs |
| `jobs` | Job postings by HR |
| `job_applications` | Candidate applications to jobs |
| `saved_jobs` | Bookmarked jobs |
| `skills` | Master skill list |
| `candidate_skills` | User ↔ skill pivot |
| `resume_parsing_logs` | Parse job status + parsed JSON |
| `resume_files` | Analytics upload files (model `Resume`) |
| `resume_analytics` | AI analysis results per resume file |
| `resume_optimizer_runs` | Optimizer pipeline state |
| `job_descriptions` | HR-uploaded JDs for AI hiring |
| `candidate_matches` | JD ↔ candidate match scores |
| `conversations` | HR–candidate threads (optional JD link) |
| `messages` | Chat messages |
| `application_notifications` | In-app alerts for candidates |
| `notifications` | Laravel database notifications |
| `personal_access_tokens` | Sanctum API tokens |
| `password_reset_tokens` | Password reset |
| `failed_jobs` | Failed queue jobs |

---

## 22. Database Relationships

**Core ER relationships (textual):**

- `users` **1—1** `candidates` (nullable `user_id` on candidate for flexibility)
- `users` (hr) **1—N** `jobs` via `jobs.hr_id`
- `companies` **1—N** `jobs` via `jobs.company_id` (optional)
- `users` **N—M** `jobs` through `saved_jobs`
- `users` + `jobs` **N—M** through `job_applications` (unique pair)
- `job_applications` **1—N** `application_notifications`
- `users` **N—M** `skills` through `candidate_skills`
- `users` **1—N** `resume_parsing_logs`, `resume_files`, `resume_optimizer_runs`
- `resume_files` **1—1 or 1—N** `resume_analytics` (per upload analysis)
- `users` (hr) **1—N** `job_descriptions`
- `job_descriptions` **1—N** `candidate_matches` → links `candidates` and `users`
- `conversations`: `hr_id` → users, `candidate_id` → users, optional `job_description_id`
- `conversations` **1—N** `messages`; `messages.sender_id` → users

**Guest support:** `resume_parsing_logs` and `resume_optimizer_runs` allow `user_id` NULL + `guest_session_id` for anonymous tool usage.

---

## 23. CRUD Operations

| Entity | Create | Read | Update | Delete |
|--------|--------|------|--------|--------|
| Users | Register | Profile, dashboards | Profile, password, photo | — |
| Jobs | HR store | Lists, job details, recommendations | HR update, toggle status | HR destroy |
| Applications | Candidate apply | HR/Admin lists, detail | HR/Admin status | — |
| Saved jobs | Candidate save | Index | — | Candidate destroy |
| Candidate profile | Parse store profile | HR view applicant | Profile update | — |
| Resume files/analytics | Upload | Analytics dashboard | Reanalyze | — |
| Optimizer runs | Upload | Status poll | Generate step | — |
| Job descriptions | HR AI hiring store | Matches list | — | — |
| Messages | POST in conversation | List since id | mark read | — |
| Conversations | connect/create | index, show | last_message_at | — |

---

## 24. API Endpoints with Purpose

**Base URL:** `{APP_URL}/api`

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| POST | `/login` | Public | Get Sanctum token |
| POST | `/register` | Public | Create user + token |
| POST | `/logout` | Sanctum | Revoke token |
| GET | `/user` | Sanctum | Current user JSON (`UserResource`) |
| POST | `/resume/upload` | Sanctum | Upload resume for parsing |
| GET | `/resume/parse/{log}` | Sanctum | Parsing status |
| POST | `/resume/profile` | Sanctum | Save parsed data to candidate |
| GET | `/analytics/resume` | Sanctum + role:user | Resume analytics JSON |
| POST | `/user/resume/upload` | Sanctum + role:user | AI analytics upload |
| GET | `/user/resume/analytics` | Sanctum + role:user | Get analytics |
| POST | `/user/resume/{resumeId}/reanalyze` | Sanctum + role:user | Re-run AI analysis |

**Web routes** (100+ endpoints) are the primary UI contract — see [`routes/web.php`](routes/web.php) sections: public, user, hr, admin.

---

## 25. Form Validations

Implemented in [`app/Http/Requests/`](app/Http/Requests/) and inline in [`AuthController`](app/Http/Controllers/AuthController.php):

| Request | Key rules |
|---------|-----------|
| `LoginRequest` | email required, password required |
| `RegisterRequest` | name, unique email, password min 8 confirmed, role in user/hr |
| `ResumeUploadRequest` | resume file required, max KB from config, mimes pdf/docx/txt |
| `ResumeOptimizerUploadRequest` | resume pdf/doc/docx, max size |
| `StoreCandidateProfileRequest` | parsing_log_id exists, full_name, email, experience bounds, skills array |
| `StoreJobDescriptionRequest` | jd_text OR jd_file required (custom after validator) |
| `SendMessageRequest` | body 1–5000 chars |
| `UpdateProfileRequest` | name, unique email, phone; extra candidate fields if user role |
| `UpdateProfilePasswordRequest` | current_password, new password min 8 confirmed |
| `UpdateProfilePhotoRequest` | image jpg/png/webp max 2MB |

**Controller-level:** Job store/update validation in `JobController`; application status must be valid enum from `JobApplication::statuses()`.

---

## 26. Security Features

- **Password hashing** (bcrypt via Laravel)
- **CSRF protection** on web forms (`VerifyCsrfToken`, meta tag + `X-CSRF-TOKEN` in JS)
- **Session regeneration** on login
- **Role middleware** prevents cross-role URL access
- **Sanctum** token authentication for API
- **Rate limiting:** API 60/min; messages 30/min; guest tool limits
- **File upload validation** (type, size from config)
- **Authorization** on conversations and owned resources
- **Password fields excluded** from exception flash (`Handler::$dontFlash`)
- **abort_unless** for inactive jobs on apply/view

**Gaps / cautions for viva:**
- Demo migration sets all user passwords to `password` ([`2026_05_22_150000_set_user_role_password_to_password.php`](database/migrations/2026_05_22_150000_set_user_role_password_to_password.php)) — dev only
- `.env.example` contains sample DB password — should not be committed with real secrets in production
- Admin cannot self-register (good) but must be seeded

---

## 27. Environment Variables and Their Usage

| Variable | Usage |
|----------|--------|
| `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` | Core Laravel app |
| `DB_*` | MySQL connection |
| `SESSION_DRIVER`, `SESSION_LIFETIME` | Web login sessions |
| `QUEUE_CONNECTION` | sync (dev) or database/redis for async jobs |
| `FILESYSTEM_DISK` | File storage default |
| `MAIL_*` | Email notifications |
| `RESUME_PYTHON_PATH` | Python for `parse_resume.py` |
| `PYTHON_BIN` | Venv Python for analyzer/optimizer/JD scripts |
| `RESUME_AI_PROVIDER` | groq / openai / gemini |
| `GROQ_API_KEY` | LLM API key |
| `RESUME_OPTIMIZER_DISK`, `RESUME_OPTIMIZER_MAX_UPLOAD_KB` | Optimizer storage and size limit |
| `RESUME_DISK`, `RESUME_MAX_UPLOAD_KB`, `RESUME_QUEUE`, `RESUME_PARSE_TIMEOUT` | Parser config (see `config/resume.php`) |
| `SANCTUM_STATEFUL_DOMAINS` | SPA cookie auth domains (if used) |
| `VITE_*`, `PUSHER_*` | Optional frontend realtime (mostly unused) |
| `AWS_*` | Optional S3 storage |

---

## 28. State Management

**No global frontend state library** (no NgRx, Redux, Vuex).

- **Server state:** Eloquent + session (auth, guest tool counts, flash messages)
- **Client state:** Per-page IIFE modules; poll timers for async jobs; `window.*Config` objects injected from Blade
- **API state:** Stateless; Sanctum token per client

---

## 29. Routing System

- **Laravel route files** — not client-side router
- **Named routes** (`user.dashboard`, `hr.jobs.store`, etc.) used in Blade `route()` helpers
- **Route model binding** for `{job}`, `{application}`, `{conversation}`, `{log}`, `{run}`
- **Legacy redirects** map old URLs (`/employer/*` → `/hr/*`, `/candidate/*` → `/user/*`)
- **API prefix** `/api` with `api` middleware group in [`RouteServiceProvider`](app/Providers/RouteServiceProvider.php)

---

## 30. Error Handling Logic

- **Default Laravel exception handler** [`app/Exceptions/Handler.php`](app/Exceptions/Handler.php) — no custom renderers registered
- **Validation:** Form requests and `$request->validate()` return 422 with errors (web: redirect back with errors; API: JSON)
- **JSON API errors:** Controllers return `{ success: false, message: '...' }` patterns (e.g. duplicate job apply)
- **Python failures:** Stored in `error_message` on logs/runs; status fields (`failed`, `completed`)
- **Logging:** Laravel `LOG_CHANNEL=stack`
- **Failed queue jobs:** `failed_jobs` table when using database queue

---

## 31. Deployment/Hosting Method

**Documented local deployment** (not cloud-specific IaC in repo):

1. PHP 8.1+, Composer, MySQL, Python venv
2. `composer install`, `.env`, `php artisan key:generate`, `migrate`, `storage:link`, `db:seed`
3. `pip install -r scripts/requirements.txt` (+ analyzer/optimizer requirements)
4. `php artisan serve` → http://127.0.0.1:8000

**Production-style setup** (inferred from docs):
- Web server (Apache/Nginx) pointing to `public/`
- `QUEUE_CONNECTION=database` + `php artisan queue:work` for resume/JD jobs
- `RESUME_DISK=public` + storage link for downloadable files
- Set `APP_DEBUG=false`, secure `APP_KEY`, real `GROQ_API_KEY`
- OS guides: [SETUP-UBUNTU.md](SETUP-UBUNTU.md), [SETUP-WINDOWS.md](SETUP-WINDOWS.md)

**No Docker/Kubernetes/terraform** in repository root.

---

## 32. Dependencies and Their Usage

### PHP ([`composer.json`](composer.json))
- **laravel/framework** — MVC, routing, Eloquent, queues, mail
- **laravel/sanctum** — API tokens
- **guzzlehttp/guzzle** — HTTP client
- **laravel/tinker** — REPL
- Dev: **phpunit**, **pint**, **sail**, **faker**, **ignition**

### Node ([`package.json`](package.json))
- **vite** + **laravel-vite-plugin** — asset bundling (lightly used)
- **axios** — available on window via bootstrap (pages mostly use fetch)

### Python
- [`scripts/requirements.txt`](scripts/requirements.txt) — pdfplumber, pypdf, python-docx (parsing)
- [`scripts/resume_analyzer/requirements.txt`](scripts/resume_analyzer/requirements.txt) — openai, google-generativeai, PyMuPDF, python-docx
- [`scripts/resume_optimizer/requirements.txt`](scripts/resume_optimizer/requirements.txt) — optimizer-specific deps

---

## 33. Project Strengths

- Clear **three-role separation** with middleware and dedicated dashboards
- **Modular services** for matching, parsing, messaging, notifications
- **Dual interface:** full Blade UI + Sanctum API for extensions
- **AI features** cover full hiring funnel: parse → analyze → optimize → JD match
- **Guest funnel** for marketing (limited free tools)
- **Documented setup** for Windows and Ubuntu
- **Database indexes** migration for performance on hot queries
- **Weighted match algorithm** with explainable `matchReason` text

---

## 34. Project Limitations

- **No separate SPA** — mobile app would rely on limited API surface
- **Default sync queue** — long Python tasks block request if not configured
- **Admin cannot be self-registered**
- **Match scores** use heuristics + optional AI — not guaranteed ground truth
- **Vite/CSS split** — many assets bypass Vite (`resources/css/app.css` missing; CSS in `public/css`)
- **Email verification** listener exists but flows may be incomplete for production
- **No multi-tenancy** — single platform instance
- **Python venv in repo path** — environment-dependent on Windows vs Linux
- **User rule mentions Angular 19** but project does not include it

---

## 35. Future Improvements

- Expand REST API to cover HR/admin features for a true mobile app
- Enable `RESUME_QUEUE=true` by default with supervisor/systemd for workers
- Add email/SMS notification channels beyond in-app
- Real-time messaging (Laravel Echo + Pusher/WebSockets)
- OAuth login (Google/LinkedIn)
- Payment/subscription for premium AI tools
- Audit logs for admin actions
- Remove or guard demo password migration for production
- Consolidate frontend build (full Vite pipeline, drop duplicate Tailwind configs)
- Unit/integration tests for AI hiring and matching edge cases
- i18n and accessibility improvements

---

## 36. Real-world Use Cases

- **Campus placement cell** — students upload resumes; TPO posts jobs; match scores shortlist candidates
- **Startup hiring** — single HR uses AI hiring to rank applicants against a JD
- **Resume coaching service** — guests try ATS check; converts to registered optimizer users
- **Recruitment agency** — manage multiple job posts per HR account, message shortlisted candidates
- **Internal HR portal** — company posts jobs; employees apply; admin tracks all applications

---

## Complete Database Understanding

**Central entity:** `users` — every authenticated actor.

**Candidate path:** `users` (role=user) → optional `candidates` → skills, applications, resumes.

**Employer path:** `users` (role=hr) → `jobs`, `job_descriptions`, `conversations` as HR side.

**Matching data:**
- Job board matching: computed at runtime via `JobMatchService` (stored on apply as `match_score`)
- AI hiring matching: persisted in `candidate_matches` after JD analysis

**File storage:** Laravel disks (`local` or `public`) under `storage/app/` — resume paths stored as strings on models.

---

## Relationship Explanation Between Tables (Summary)

Treat **`users`** as the hub. **`candidates`** extends only job seekers. **`jobs`** belong to HR users and optionally **`companies`**. **`job_applications`** is the fact table connecting seekers to jobs. **`job_descriptions`** is a parallel track for AI hiring (not the same as `jobs` table postings). **`candidate_matches`** links JD analysis results to candidates. **`conversations`** bridge HR and candidate users, optionally tied to a JD. **`messages`** are the detail rows under conversations.

---

## Data Flow Explanation

**Resume parse flow:**
Browser upload → Controller validates → store file → create `resume_parsing_logs` → dispatch `ParseResumeJob` → `ResumeParserService` runs Python → updates log `parsed_data` + status → frontend polls status endpoint → user POST profile → `Candidate` + skills updated.

**Apply flow:**
Candidate POST apply → check duplicate → `JobMatchService::percentage` → insert `job_applications` with status `applied` and `match_score` → JSON success to `job-details.js`.

**AI hiring flow:**
HR POST JD → `job_descriptions` row → `AnalyzeJobDescriptionJob` → `MatchCandidatesForJdJob` → populate `candidate_matches` → HR views matches → connect creates/finds `conversations`.

---

## Request/Response Flow

**Typical authenticated web POST:**
1. Browser sends form/multipart with session cookie + CSRF token
2. `web` middleware: session, CSRF verify
3. `auth` + `role:*` middleware
4. Form Request validation
5. Controller calls Service / dispatches Job
6. Response: Blade redirect with flash OR JSON for AJAX endpoints

**Typical API call:**
1. `Authorization: Bearer {token}`
2. `auth:sanctum` resolves user
3. Optional `role:user`
4. JSON via API Resources or custom arrays

---

## Backend Processing Flow

1. **HTTP entry** → routing → middleware chain
2. **Controller** thin — delegates to Services/Actions
3. **Service** encapsulates rules (match %, parse dispatch, message send)
4. **Job** for slow work — calls Python via `Process` or shell exec
5. **Model** persistence + relationships
6. **Notification/Mail** on status changes (where enabled)

---

## Frontend Rendering Flow

1. User hits URL → Laravel route → Controller returns `view(...)` with data
2. Blade layout (`candidate.blade.php` / `employer.blade.php`) wraps page
3. Partials: sidebar, topbar, cards
4. Page pushes scripts via `@push('scripts')` loading `public/js/*.js`
5. JS reads `window.*Config` (routes, CSRF, IDs)
6. Async actions: `fetch`/`XHR` to same-origin Laravel routes → JSON or file download
7. Polling loops for `status` endpoints until `completed` or `failed`

---

## HR Flow (Step-by-step, for completeness)

1. Login as HR → `/hr/dashboard`
2. Create job → fill form → `jobs` record (default may be inactive until toggled)
3. Toggle job **active** to appear in candidate recommendations
4. View **Applicants** for applications to own jobs
5. Update application status → triggers `ApplicationNotificationService`
6. **AI Hiring** → upload JD → wait for analysis → review `candidate_matches` → open candidate → **Connect** → message
7. Manage profile and notification preferences

---

*This report is derived from full codebase exploration including routes, models, migrations, services, views, scripts, and configuration. Use it as the source document for ER diagrams, DFDs, SRS, synopsis, viva preparation, and presentations.*
