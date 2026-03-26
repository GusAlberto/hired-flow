# HiredFlow

A simple **Kanban-style job application tracker** built with Laravel, Livewire and Docker.

HiredFlow helps developers and job seekers track their job applications, interviews and offers in a simple visual board.

---

##  Features

*  Kanban board for tracking job applications
*  Track company and position
*  Application date tracking
*  Drag-and-drop status updates
*  Application statistics dashboard
*  Authentication system
*  Fully dockerized development environment

---

## Tech Stack

* **Backend:** Laravel
* **Frontend:** Livewire + Blade
* **Styling:** TailwindCSS
* **Database:** MySQL
* **Containerization:** Docker (Laravel Sail)

---
## Project Architecture
```bash
app/
├── Concerns/
│ └── DetectsApplicationColumns.php # Shared trait for detecting application columns
│
├── Repositories/
│ └── ApplicationRepository.php # Handles database access
│
├── Actions/
│ ├── CreateApplication.php # Handles job application creation
│ ├── UpdateApplication.php # Handles job application updates
│ ├── MoveApplication.php # Handles Kanban column movement
│ └── ScheduleInterview.php # Handles interview scheduling
│
├── Services/
│ └── ApplicationService.php # Business logic orchestrator
│
└── Livewire/
└── ApplicationsBoard.php # UI layer (validation + user interaction)
```
## Screenshot

Add a screenshot of the board here:

```
/docs/screenshot.png
```

Example:

![HiredFlow Dashboard](docs/screenshot.png)

---

## Installation

Clone the repository:

```bash
git clone https://github.com/YOUR_USERNAME/hired-flow.git
cd hired-flow
```

Start Docker containers:

```bash
./vendor/bin/sail up -d
```

Run migrations:

```bash
./vendor/bin/sail artisan migrate
```

Install frontend dependencies:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Open the application:

```
http://localhost
```

---

## Security: Secrets and Environment Variables

Use this simple rule:

* Public values can go to frontend.
* Secrets must stay server-side only.

### What should be secret

Examples:

* `APP_KEY`
* `DB_PASSWORD`
* `AWS_SECRET_ACCESS_KEY`
* API tokens and webhook secrets

### Safe defaults

* Keep `.env` out of git (already ignored in this project).
* Commit only `.env.example` with placeholder values.
* Never expose secrets with frontend prefixes such as `VITE_`.
* In production: `APP_ENV=production` and `APP_DEBUG=false`.

---

## Deploy Without Cloudflare

This project can run in any provider that supports PHP 8.2+ and a database.

### 1) Build and prepare

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2) Set environment variables in your provider panel

At minimum:

* `APP_NAME`
* `APP_ENV=production`
* `APP_DEBUG=false`
* `APP_KEY`
* `APP_URL`
* `DB_CONNECTION`
* `DB_HOST`
* `DB_PORT`
* `DB_DATABASE`
* `DB_USERNAME`
* `DB_PASSWORD`

Generate app key locally if needed:

```bash
php artisan key:generate --show
```

### 3) Run migrations on deploy

```bash
php artisan migrate --force
```

### 4) Point your web root correctly

For traditional servers, point the domain to `public/`.

### Providers that work well

* Render (Web Service with PHP runtime)
* Railway (Nixpacks / Docker)
* VPS (Ubuntu + Nginx + PHP-FPM)
* Shared hosting with Laravel support (public path to `public/`)

---

## Optional: Cloudflare Mode

Cloudflare support is optional in `vite.config.js`.

* Default: Cloudflare plugin is off.
* Enable only when needed:

```bash
USE_CLOUDFLARE=true npm run build
```

For Cloudflare secrets:

```bash
npx wrangler secret put APP_KEY
npx wrangler secret put DB_PASSWORD
```

---

## Roadmap

Future improvements:

* Job link storage (LinkedIn / Indeed)
* Notes per application
* File uploads (job description PDF)
* Email reminders
* Application analytics dashboard

---

## Motivation

Job searching often involves sending dozens or even hundreds of applications.

HiredFlow was created to help organize that process visually and make it easier to track which companies have responded.

---

## License

This project is open-source and available under the MIT License.
