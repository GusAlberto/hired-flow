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
