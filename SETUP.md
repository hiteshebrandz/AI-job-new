# Project setup — choose your OS

Pick the guide for your operating system. Both cover the full flow: clone → PHP/MySQL → Python → run the app.

| Platform | Guide |
|----------|--------|
| **Ubuntu / Debian** | **[SETUP-UBUNTU.md](SETUP-UBUNTU.md)** |
| **Windows 10/11** | **[SETUP-WINDOWS.md](SETUP-WINDOWS.md)** |

---

## What you will install

- **Laravel 10** + PHP 8.1+
- **MySQL** database
- **Python 3.10+** (resume parsing, analytics, AI optimizer)
- **Composer** (PHP packages)
- **Node.js** (optional — Vite only)

---

## Short checklist (any OS)

1. Clone repo from GitHub  
2. `composer install`  
3. Copy `.env.example` → `.env`, run `php artisan key:generate`  
4. Create MySQL database, set `DB_*` in `.env`  
5. `php artisan migrate` → `php artisan storage:link` → `php artisan db:seed`  
6. Python venv + `pip install` (see OS guide)  
7. Set `GROQ_API_KEY` in `.env` for AI features  
8. `php artisan serve` → open http://127.0.0.1:8000  

---

## Demo accounts (after `db:seed`)

| Role | Email | Password |
|------|--------|----------|
| HR | hr@gmail.com | hr@gmail.com |
| Candidate | user@gmail.com | user@gmail.com |
| Admin | admin@gmail.com | admin@gmail.com |

---

## More documentation

- [README.md](README.md) — project overview  
- [docs/RESUME_PARSING_SETUP.md](docs/RESUME_PARSING_SETUP.md) — resume parsing API notes  

Replace `<YOUR_GITHUB_REPO_URL>` in the OS guides with your repository URL.
