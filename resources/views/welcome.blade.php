<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hired Flow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>
    <div class="container">
        <header class="topbar">
            <div class="brand">
                Hired Flow
                {{-- <span class="brand-icon" aria-hidden="true">&#128269;</span> --}}
            </div>
            <div class="actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn primary">Open dashboard</a>
                    @else
                        <a href="{{ route('demo') }}" class="btn primary">Open dashboard</a>
                        <a href="{{ route('login') }}" class="btn signin">Sign in</a>
                    @endauth
                @endif
            </div>
        </header>

        <section class="hero">
            <article class="panel">
                <h1>Ditch spreadsheets. Track your job applications faster and smarter.</h1>
                <p class="lead">
                    Stop wasting hours on tedious manual data entry. Application Tracker automatically extracts job
                    details from job post URLs and organizes them in one dashboard.
                </p>

                <div class="chips">
                    <span class="chip">Kanban by stage</span>
                    <span class="chip">Interview scheduling</span>
                    <span class="chip">Favorites and notes</span>
                    <span class="chip">Automatic archiving</span>
                </div>
            </article>

            <aside class="stat">
                <div class="stat-card">
                    <strong>1 dashboard</strong>
                    All applications centralized with status, score, salary and notes.
                </div>
                <div class="stat-card">
                    <strong>Less busywork</strong>
                    Keep focus on interviews and follow-ups, not on copying data.
                </div>
                <div class="stat-card">
                    <strong>Cleaner pipeline</strong>
                    Know exactly what is Applied, Waiting, Interview, Offer or Rejected.
                </div>
            </aside>
        </section>

        <section class="grid">
            <article class="card">
                <h3>Capture from URL</h3>
                <p>Paste a job post link and let the tracker fill the basics quickly, reducing repetitive typing.</p>
            </article>
            <article class="card">
                <h3>Move with clarity</h3>
                <p>Drag and drop each opportunity across stages and see your pipeline health in real time.</p>
            </article>
            <article class="card">
                <h3>Never miss interviews</h3>
                <p>Schedule interview date, time, location and format so your week stays predictable.</p>
            </article>
            <article class="card">
                <h3>Prioritize better</h3>
                <p>Use favorites and personal score to focus your effort on the most valuable opportunities.</p>
            </article>
            <article class="card">
                <h3>Track compensation</h3>
                <p>Record offered and expected salary to compare opportunities with confidence.</p>
            </article>
            <article class="card">
                <h3>Keep board clean</h3>
                <p>Archive old applications automatically based on your preferred number of days.</p>
            </article>
        </section>

        <section class="cta">
            <div>
                <h2>Ready to manage your job search like a pro?</h2>
                <p>Start now and replace scattered spreadsheets with one organized flow.</p>
            </div>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn primary">Go to dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn primary">Start free</a>
                @endauth
            @endif
        </section>

        <footer class="footer">
            <p class="footer-copy">
                Built for professionals who want a faster, structured and less stressful job application workflow.
            </p>
            <p class="footer-rights">© 2026 Hired Flow. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>
