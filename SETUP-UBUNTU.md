# Setup guide — Ubuntu / Debian

Step-by-step setup for this project on **Ubuntu 22.04+** or **Debian 12+** after cloning from GitHub.

**Stack:** Laravel 10, PHP 8.1+, MySQL, Python 3.10+

---

## 1. Install system packages

Open a terminal and run:

```bash
sudo apt update
sudo apt install -y \
  git curl unzip \
  php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-intl \
  mysql-server \
  python3 python3-pip python3-venv \
  nodejs npm
```

If `php8.2` is not available on your release, try `php8.1` or `php8.3` and use that version in package names (e.g. `php8.3-cli`).

**Install Composer:**

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

**Start MySQL:**

```bash
sudo systemctl enable mysql
sudo systemctl start mysql
sudo systemctl status mysql
```

---

## 2. Clone the project

```bash
cd ~
git clone <YOUR_GITHUB_REPO_URL> Ai
cd Ai
```

Replace `<YOUR_GITHUB_REPO_URL>` with your real GitHub URL.

---

## 3. PHP dependencies

```bash
composer install
```

If memory runs out:

```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

**Fix permissions (if needed):**

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

---

## 4. Environment file

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```bash
nano .env
```

Minimum settings:

```env
APP_NAME="TalentSync AI"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_job
DB_USERNAME=ai_job_user
DB_PASSWORD=your_secure_password
```

Save: `Ctrl+O`, Enter, `Ctrl+X`.

---

## 5. Create MySQL database

**Secure MySQL (first time only):**

```bash
sudo mysql_secure_installation
```

**Create database and user:**

```bash
sudo mysql
```

```sql
CREATE DATABASE ai_job CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ai_job_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON ai_job.* TO 'ai_job_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Use the same password in `.env` as `DB_PASSWORD`.

---

## 6. Laravel setup

```bash
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

Reset all candidate passwords to `password`:

```bash
php artisan users:set-candidate-password password
```

---

## 7. Python virtual environment

From the project folder (`~/Ai` or your path):

```bash
python3 -m venv scripts/resume_analyzer/venv
source scripts/resume_analyzer/venv/bin/activate
pip install --upgrade pip
pip install -r scripts/requirements.txt
pip install -r scripts/resume_analyzer/requirements.txt
pip install -r scripts/resume_optimizer/requirements.txt
deactivate
```

Add to `.env`:

```env
PYTHON_BIN=scripts/resume_analyzer/venv/bin/python3
RESUME_PYTHON_PATH=scripts/resume_analyzer/venv/bin/python3
RESUME_AI_PROVIDER=groq
GROQ_API_KEY=your_groq_api_key
```

Get a free Groq key: [console.groq.com/keys](https://console.groq.com/keys)

**Verify Python:**

```bash
php artisan resume:check-python
```

All packages should show ✅.

---

## 8. Optional — Node.js / Vite

```bash
npm install
npm run build
```

For development with hot reload (second terminal):

```bash
npm run dev
```

---

## 9. Run the app

```bash
php artisan serve
```

Open: **http://127.0.0.1:8000**

Login: **http://127.0.0.1:8000/login**

### Optional — queue worker

For background resume/optimizer jobs, in `.env`:

```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
```

In a **second terminal**:

```bash
cd ~/Ai
php artisan queue:work
```

---

## 10. After `git pull` (updates)

```bash
cd ~/Ai
git pull
composer install
php artisan migrate
php artisan config:clear
source scripts/resume_analyzer/venv/bin/activate
pip install -r scripts/requirements.txt
pip install -r scripts/resume_analyzer/requirements.txt
pip install -r scripts/resume_optimizer/requirements.txt
deactivate
php artisan resume:check-python
```

---

## 11. Ubuntu troubleshooting

| Problem | Fix |
|---------|-----|
| `Connection refused` (MySQL) | `sudo systemctl start mysql` |
| `php: command not found` | `sudo apt install php8.2-cli` |
| `Permission denied` on storage | `chmod -R ug+rwx storage bootstrap/cache` |
| Python not found from PHP | Use full path in `.env`: `PYTHON_BIN=/home/YOU/Ai/scripts/resume_analyzer/venv/bin/python3` |
| Port 8000 in use | `php artisan serve --port=8001` |
| Missing PHP extension | `sudo apt install php8.2-<extension>` then restart terminal |

**Run tests:**

```bash
php artisan test
```

---

## 12. Useful commands (Ubuntu)

```bash
php artisan serve              # Start server
php artisan migrate            # Database migrations
php artisan db:seed            # Demo users
php artisan storage:link       # Public uploads
php artisan resume:check-python
php artisan route:list
php artisan about              # Environment info
```

---

**Windows setup:** see [SETUP-WINDOWS.md](SETUP-WINDOWS.md)  
**General index:** see [SETUP.md](SETUP.md)
