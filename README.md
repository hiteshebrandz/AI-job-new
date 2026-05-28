# TalentSync AI

Laravel application for job seekers, HR recruiters, and admins — including resume upload/parsing, resume analytics, AI resume optimizer, job applications, and HR job seeker management.

## Quick start

**Full setup (clone → run):**

| OS | Guide |
|----|--------|
| **Ubuntu / Debian** | [SETUP-UBUNTU.md](SETUP-UBUNTU.md) |
| **Windows** | [SETUP-WINDOWS.md](SETUP-WINDOWS.md) |
| Index | [SETUP.md](SETUP.md) |

```bash
git clone <YOUR_GITHUB_REPO_URL> Ai && cd Ai
composer install
cp .env.example .env && php artisan key:generate
# Configure DB_* in .env, create MySQL database, then:
php artisan migrate && php artisan storage:link && php artisan db:seed
# Python venv + pip install — see SETUP.md §7
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) and log in with seeded accounts (see SETUP.md).

## Stack

- **Backend:** Laravel 10, PHP 8.1+
- **Database:** MySQL
- **Frontend:** Blade templates, Tailwind (static assets in `public/`)
- **Python:** Resume parsing, analytics, AI optimizer (`scripts/`)

## Documentation

| File | Description |
|------|-------------|
| [SETUP-UBUNTU.md](SETUP-UBUNTU.md) | Ubuntu / Debian installation |
| [SETUP-WINDOWS.md](SETUP-WINDOWS.md) | Windows installation |
| [SETUP.md](SETUP.md) | Setup index (pick your OS) |
| [docs/RESUME_PARSING_SETUP.md](docs/RESUME_PARSING_SETUP.md) | Resume parsing API & queue notes |
| [docs/PROJECT_ANALYSIS.md](docs/PROJECT_ANALYSIS.md) | Complete project analysis (SRS, viva, ER/DFD source) |
| [docs/COMPLETE_PROJECT_REPORT.md](docs/COMPLETE_PROJECT_REPORT.md) | Full 14-section academic report (testing, viva Q&A, API docs, user manual, security, diagrams) |

## License

MIT (Laravel framework components follow Laravel's license where applicable).
