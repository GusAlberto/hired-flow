<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hired Flow - Demo Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4f4f1;
            --card: #ffffff;
            --line: #d8d5cc;
            --ink: #171716;
            --muted: #5c5c57;
            --primary: #0f6c5d;
            --badge: #f97316;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'IBM Plex Sans', sans-serif;
            background:
                radial-gradient(1000px 520px at -15% -5%, #dbefe9 0%, rgba(219, 239, 233, 0) 65%),
                var(--bg);
            color: var(--ink);
        }

        .container {
            width: min(1120px, 94vw);
            margin: 0 auto;
            padding: 24px 0 40px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .title {
            font-family: 'Space Grotesk', sans-serif;
            margin: 0;
            font-size: clamp(1.2rem, 2vw, 1.8rem);
        }

        .note {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .btn {
            text-decoration: none;
            color: #e9fff9;
            background: var(--primary);
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 0.92rem;
            display: inline-block;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 14px;
        }

        .stat {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
        }

        .stat b {
            display: block;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.6rem;
        }

        .board {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .col {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 12px;
            min-height: 300px;
        }

        .col h3 {
            margin: 0 0 10px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .count {
            font-size: 0.8rem;
            color: var(--muted);
            background: #f3f1eb;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 2px 8px;
        }

        .item {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 8px;
            background: #fff;
        }

        .item strong {
            display: block;
            margin-bottom: 3px;
            font-size: 0.95rem;
        }

        .item span {
            display: block;
            font-size: 0.83rem;
            color: var(--muted);
        }

        .item .tag {
            margin-top: 7px;
            display: inline-block;
            background: #fff2e7;
            color: #8b3f0d;
            border: 1px solid #ffd5b8;
            border-radius: 999px;
            padding: 2px 7px;
            font-size: 0.74rem;
            font-weight: 600;
        }

        @media (max-width: 980px) {
            .stats, .board {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .stats, .board {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div>
                <h1 class="title">Demo Dashboard</h1>
                <p class="note">Example data to preview how Hired Flow tracks your applications.</p>
            </div>
            <a class="btn" href="{{ route('register') }}">Create your account</a>
        </div>

        <section class="stats">
            <article class="stat"><b>24</b>Total applications</article>
            <article class="stat"><b>5</b>Interviews this month</article>
            <article class="stat"><b>2</b>Offers received</article>
            <article class="stat"><b>7</b>Favorited opportunities</article>
        </section>

        <section class="board">
            <article class="col">
                <h3>Applied <span class="count">6</span></h3>
                <div class="item">
                    <strong>Backend Developer</strong>
                    <span>NovaCore · Remote</span>
                    <span class="tag">Score 8/10</span>
                </div>
                <div class="item">
                    <strong>Software Engineer</strong>
                    <span>LoopWorks · Hybrid</span>
                    <span class="tag">Score 7/10</span>
                </div>
            </article>

            <article class="col">
                <h3>Waiting <span class="count">8</span></h3>
                <div class="item">
                    <strong>Platform Engineer</strong>
                    <span>Cloudton · Remote</span>
                    <span class="tag">Applied 4 days ago</span>
                </div>
                <div class="item">
                    <strong>Laravel Developer</strong>
                    <span>Tech Harbor · On-site</span>
                    <span class="tag">Expected salary saved</span>
                </div>
            </article>

            <article class="col">
                <h3>Interview <span class="count">5</span></h3>
                <div class="item">
                    <strong>Full Stack Engineer</strong>
                    <span>BrightCode · Google Meet</span>
                    <span class="tag">Tomorrow 14:30</span>
                </div>
                <div class="item">
                    <strong>PHP Engineer</strong>
                    <span>HexaSoft · In-person</span>
                    <span class="tag">Monday 10:00</span>
                </div>
            </article>

            <article class="col">
                <h3>Offer <span class="count">2</span></h3>
                <div class="item">
                    <strong>Senior Laravel Engineer</strong>
                    <span>Flowly · Remote</span>
                    <span class="tag">Offer R$ 12.500</span>
                </div>
                <div class="item">
                    <strong>Backend Specialist</strong>
                    <span>Codenest · Hybrid</span>
                    <span class="tag">Negotiating terms</span>
                </div>
            </article>
        </section>
    </div>
</body>
</html>
