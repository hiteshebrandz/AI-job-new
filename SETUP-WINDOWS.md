# Setup guide — Windows

Step-by-step setup for this project on **Windows 10/11** after cloning from GitHub.

**Stack:** Laravel 10, PHP 8.1+, MySQL, Python 3.10+

Use **PowerShell** (recommended) or **Command Prompt** unless noted otherwise.

---

## 1. Install required software

Install each tool and enable **“Add to PATH”** during setup where offered.

| Software | Download | Notes |
|----------|----------|--------|
| **Git** | [git-scm.com](https://git-scm.com/download/win) | Includes Git Bash |
| **PHP 8.2+** | [windows.php.net](https://windows.php.net/download/) | Thread Safe ZIP; enable extensions in `php.ini` (see below) |
| **Composer** | [getcomposer.org](https://getcomposer.org/download/) | Windows installer |
| **MySQL** | [MySQL Installer](https://dev.mysql.com/downloads/installer/) | MySQL Server + Workbench (optional) |
| **Python 3.10+** | [python.org](https://www.python.org/downloads/) | Check **“Add python.exe to PATH”** |
| **Node.js** (optional) | [nodejs.org](https://nodejs.org/) | For Vite only |

### PHP extensions (`php.ini`)

Open your PHP folder (e.g. `C:\php`) and edit `php.ini`. Uncomment (remove `;`):

```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
```

Restart any open terminals after changing PATH or `php.ini`.

### Verify installs (PowerShell)

```powershell
git --version
php -v
composer -V
mysql --version
python --version
```

---

## 2. Clone the project

```powershell
cd $HOME\Desktop
git clone <YOUR_GITHUB_REPO_URL> Ai
cd Ai
```

Replace `<YOUR_GITHUB_REPO_URL>` with your real GitHub URL. Use any folder you prefer instead of `Desktop`.

---

## 3. PHP dependencies

```powershell
composer install
```

If memory error:

```powershell
$env:COMPOSER_MEMORY_LIMIT=-1; composer install
```

---

## 4. Environment file

```powershell
copy .env.example .env
php artisan key:generate
```

Open `.env` in Notepad or VS Code (`code .env`) and set:

```env
APP_NAME="AI Job Platform"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_job
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

Use the MySQL root password you set during MySQL installation.

---

## 5. Create MySQL database

**Option A — MySQL Workbench**

1. Connect to local MySQL.
2. Run:

```sql
CREATE DATABASE ai_job CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Optionally create a dedicated user and grant privileges on `ai_job`.

**Option B — Command line**

```powershell
mysql -u root -p
```

```sql
CREATE DATABASE ai_job CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ai_job_user'@'localhost' IDENTIFIED BY 'YourPassword123!';
GRANT ALL PRIVILEGES ON ai_job.* TO 'ai_job_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Update `.env` with `DB_USERNAME` and `DB_PASSWORD`.

**Start MySQL service** if it is stopped:

- Press `Win + R` → `services.msc` → find **MySQL80** (or similar) → **Start**

---

## 6. Laravel setup

In the project folder:

```powershell
php artisan migrate
php artisan storage:link
php artisan db:seed
```

**Demo logins after seed:**

| Role | Email | Password |
|------|--------|----------|
| HR | hr@gmail.com | hr@gmail.com |
| Candidate | user@gmail.com | user@gmail.com |
| Admin | admin@gmail.com | admin@gmail.com |

Reset all candidate passwords:

```powershell
php artisan users:set-candidate-password password
```

---

## 7. Python virtual environment (important on Windows)

`php artisan serve` often **does not** see `python` on PATH. Use a **full path** to the venv Python.

From the project folder (example: `C:\Users\YourName\Desktop\Ai`):

```powershell
python -m venv scripts\resume_analyzer\venv
```

**Activate and install packages:**

```powershell
.\scripts\resume_analyzer\venv\Scripts\Activate.ps1
```

If you get an execution policy error:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\scripts\resume_analyzer\venv\Scripts\Activate.ps1
```

Then:

```powershell
pip install --upgrade pip
pip install -r scripts\requirements.txt
pip install -r scripts\resume_analyzer\requirements.txt
pip install -r scripts\resume_optimizer\requirements.txt
deactivate
```

**Find your Python path:**

```powershell
(Get-Item .\scripts\resume_analyzer\venv\Scripts\python.exe).FullName
```

Copy the output into `.env` (use forward slashes or escaped backslashes). Example:

```env
PYTHON_BIN=C:/Users/YourName/Desktop/Ai/scripts/resume_analyzer/venv/Scripts/python.exe
RESUME_PYTHON_PATH=C:/Users/YourName/Desktop/Ai/scripts/resume_analyzer/venv/Scripts/python.exe
RESUME_AI_PROVIDER=groq
GROQ_API_KEY=your_groq_api_key
```

Get a Groq key: [console.groq.com/keys](https://console.groq.com/keys)

**Verify Python:**

```powershell
php artisan resume:check-python
```

---

## 8. Optional — Node.js / Vite

```powershell
npm install
npm run build
```

Development (second PowerShell window):

```powershell
npm run dev
```

---

## 9. Run the app

```powershell
php artisan serve
```

Open: **http://127.0.0.1:8000**

Login: **http://127.0.0.1:8000/login**

Keep this PowerShell window open while developing.

### Optional — queue worker

In `.env`:

```env
QUEUE_CONNECTION=database
```

```powershell
php artisan queue:table
php artisan migrate
```

Open a **second** PowerShell in the project folder:

```powershell
php artisan queue:work
```

---

## 10. After `git pull` (updates)

```powershell
cd C:\Users\YourName\Desktop\Ai
git pull
composer install
php artisan migrate
php artisan config:clear
.\scripts\resume_analyzer\venv\Scripts\Activate.ps1
pip install -r scripts\requirements.txt
pip install -r scripts\resume_analyzer\requirements.txt
pip install -r scripts\resume_optimizer\requirements.txt
deactivate
php artisan resume:check-python
```

---

## 11. Windows troubleshooting

| Problem | Fix |
|---------|-----|
| `php` is not recognized | Add PHP folder to **System Environment Variables → Path** |
| `composer` is not recognized | Reinstall Composer; restart terminal |
| MySQL connection refused | Start **MySQL80** in `services.msc`; check `DB_PASSWORD` in `.env` |
| Resume upload — Python not found | Set **full paths** for `PYTHON_BIN` and `RESUME_PYTHON_PATH` (see §7) |
| `where python` shows nothing | Reinstall Python with **Add to PATH** |
| `storage:link` fails | Run PowerShell **as Administrator** once, or enable Developer Mode |
| Port 8000 busy | `php artisan serve --port=8001` |
| SSL / curl errors in Composer | Update `cacert.pem` in `php.ini` or use `composer install --prefer-dist` |
| Slow antivirus on `vendor/` | Exclude project folder temporarily during `composer install` |

**Find system Python (if not using venv):**

```powershell
where.exe python
```

Prefer the **venv** path in `.env`, not the Microsoft Store stub.

**Run tests:**

```powershell
php artisan test
```

---

## 12. Useful commands (Windows)

```powershell
php artisan serve
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan resume:check-python
php artisan route:list
php artisan about
```

---

## 13. Quick path reference

| Item | Typical path |
|------|----------------|
| Project | `C:\Users\<You>\Desktop\Ai` |
| Venv Python | `...\Ai\scripts\resume_analyzer\venv\Scripts\python.exe` |
| `.env` file | `...\Ai\.env` |
| PHP config | `C:\php\php.ini` |

---

**Ubuntu setup:** see [SETUP-UBUNTU.md](SETUP-UBUNTU.md)  
**General index:** see [SETUP.md](SETUP.md)
