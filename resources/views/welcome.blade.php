<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hired Flow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f7f5ef;
            --surface: #fffdf8;
            --ink: #171716;
            --muted: #5b5b57;
            --line: #d9d4c8;
            --brand: #0f6c5d;
            --brand-ink: #e8fff9;
            --accent: #f97316;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 700px at 100% -15%, #ffe9cf 0%, rgba(255, 233, 207, 0) 60%),
                radial-gradient(1000px 560px at -15% 10%, #d5efe8 0%, rgba(213, 239, 232, 0) 60%),
                var(--bg);
        }

        .container {
            width: min(1100px, 92vw);
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 0;
        }

        .brand {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            border: 1px solid var(--line);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            font-size: 0.92rem;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            border: 1px solid var(--line);
            background: transparent;
            color: var(--ink);
            text-decoration: none;
            border-radius: 999px;
            padding: 10px 18px;
            font-size: 0.95rem;
            font-weight: 600;
            transition: 0.2s ease;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-1px);
            border-color: var(--ink);
        }

        .btn.primary {
            border-color: var(--brand);
            background: var(--brand);
            color: var(--brand-ink);
        }

        .hero {
            padding: 38px 0 24px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 32px;
            box-shadow: 0 12px 30px rgba(40, 40, 30, 0.05);
        }

        h1 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
        }

        .lead {
            margin: 22px 0 0;
            font-size: 1.1rem;
            line-height: 1.6;
            color: var(--muted);
            max-width: 60ch;
        }

        .chips {
            margin-top: 24px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .chip {
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #faf8f2;
            padding: 8px 14px;
            font-size: 0.87rem;
            font-weight: 600;
            color: #3f3f3b;
        }

        .stat {
            display: grid;
            gap: 14px;
            align-content: start;
        }

        .stat-card {
            border-radius: 18px;
            border: 1px solid var(--line);
            padding: 18px;
            background: #fff;
        }

        .stat-card strong {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.8rem;
            display: block;
        }

        .grid {
            margin: 20px 0 44px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
        }

        .card h3 {
            margin: 0 0 8px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.06rem;
        }

        .card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .cta {
            margin: 8px 0 56px;
            background: linear-gradient(115deg, #0f6c5d 0%, #14554a 100%);
            border: 1px solid #0c5146;
            border-radius: 22px;
            color: #eafff8;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .cta h2 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(1.4rem, 2.8vw, 2.1rem);
        }

        .cta p {
            margin: 8px 0 0;
            color: #b7ece1;
            max-width: 58ch;
        }

        .footer {
            color: #6b6b67;
            font-size: 0.86rem;
            padding-bottom: 28px;
        }

        @media (max-width: 950px) {
            .hero {
                grid-template-columns: 1fr;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .cta {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="topbar">
            <div class="brand">
                Hired Flow
                <span class="brand-icon" aria-hidden="true">&#128269;</span>
            </div>
            <div class="actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn primary">Open dashboard</a>
                    @else
                        <a href="{{ route('demo') }}" class="btn primary">Open dashboard</a>
                        <a href="{{ route('login') }}" class="btn">Sign in</a>
                    @endauth
                @endif
            </div>
        </header>

        <section class="hero">
            <article class="panel">
                <h1>Ditch spreadsheets. Track your job applications faster and smarter.</h1>
                <p class="lead">
                    Stop wasting hours on tedious manual data entry. Application Tracker automatically extracts job details from job post URLs and organizes them in one dashboard.
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
            Built for professionals who want a faster, structured and less stressful job application workflow.
        </footer>
    </div>
</body>
</html>
