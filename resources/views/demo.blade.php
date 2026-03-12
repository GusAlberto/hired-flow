<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hired Flow - Demo Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/demo.css') }}">
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
