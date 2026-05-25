<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance in Progress</title>
    <style>
        :root {
            --ink: #152757;
            --ink-soft: #4b5f95;
            --line: #cfe1ff;
            --card: rgba(255, 255, 255, 0.95);
            --soft: #eff5ff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(900px 520px at 8% 0%, #d9ebff 0%, transparent 62%),
                radial-gradient(820px 460px at 100% 100%, #b8dcff 0%, transparent 58%),
                linear-gradient(140deg, #cbe6ff 0%, #a8d4ff 100%);
            display: grid;
            place-items: center;
            padding: 26px 14px;
        }

        .card {
            width: min(470px, 100%);
            padding: 22px 20px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 18px 44px rgba(22, 63, 112, 0.2);
            text-align: center;
        }

        .brand {
            margin: 0;
            font-size: 19px;
            font-weight: 800;
            color: #1c3570;
        }

        .brand span {
            font-weight: 500;
            color: #5c75ad;
        }

        h1 {
            margin: 14px 0 8px;
            font-size: clamp(26px, 5vw, 36px);
            line-height: 1.06;
        }

        .lead {
            margin: 0;
            color: var(--ink-soft);
            line-height: 1.55;
            font-size: 15px;
        }

        .timer-wrap {
            margin-top: 16px;
            border: 1px solid var(--line);
            background: #f8fbff;
            border-radius: 12px;
            padding: 12px 10px;
        }

        .timer-label {
            margin: 0;
            font-size: 13px;
            color: #54689e;
        }

        .clock {
            margin-top: 8px;
            display: flex;
            justify-content: center;
            gap: 8px;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
            color: #162862;
            letter-spacing: 0.05em;
        }

        .time {
            background: var(--soft);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 4px 8px;
            min-width: 44px;
            text-align: center;
        }

        .state {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            background: #edf9f5;
            border: 1px solid #cfeee5;
            color: #1a7768;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--good);
            box-shadow: 0 0 0 4px rgba(29, 156, 136, 0.2);
        }

        .action {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #f4f6ff;
            color: #1f2963;
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(23, 27, 76, 0.15);
        }

        @media (max-width: 580px) {
            .card {
                width: min(420px, 100%);
                padding: 18px 14px;
            }

            .brand {
                font-size: 18px;
            }

            h1 {
                font-size: 24px;
            }

            .clock {
                gap: 6px;
            }
        }
    </style>
</head>
<body>
    <main class="card" aria-live="polite">
        <p class="brand">CMSMb<span> Portal</span></p>
        <h1>Quick maintenance break</h1>
        <p class="lead">We are applying updates to improve speed and reliability. Services will be back shortly.</p>

        <div class="timer-wrap" id="countdown" data-end="2026-04-16T23:59:59">
            <p class="timer-label">Estimated return time</p>
            <div class="clock">
                <span class="time" data-part="hours">00</span>
                <span>:</span>
                <span class="time" data-part="minutes">00</span>
                <span>:</span>
                <span class="time" data-part="seconds">00</span>
            </div>
            <div class="state" id="status-pill">
                <span class="dot"></span>
                <span data-part="status">maintenance mode</span>
            </div>
        </div>

        <a class="action" href="{{ route('home') }}">Back to Home</a>
    </main>

    <script>
        (function () {
            var root = document.getElementById('countdown');
            if (!root) return;

            var end = new Date(root.getAttribute('data-end')).getTime();
            var statusEl = document.querySelector('[data-part="status"]');
            var hoursEl = root.querySelector('[data-part="hours"]');
            var minutesEl = root.querySelector('[data-part="minutes"]');
            var secondsEl = root.querySelector('[data-part="seconds"]');

            function pad(value) {
                return String(value).padStart(2, '0');
            }

            function tick() {
                var now = Date.now();
                var diff = end - now;

                if (diff <= 0) {
                    hoursEl.textContent = '00';
                    minutesEl.textContent = '00';
                    secondsEl.textContent = '00';
                    if (statusEl) statusEl.textContent = 'services are live';
                    return;
                }

                var totalSeconds = Math.floor(diff / 1000);
                var hours = Math.floor(totalSeconds / 3600);
                var minutes = Math.floor((totalSeconds % 3600) / 60);
                var seconds = totalSeconds % 60;

                hoursEl.textContent = pad(hours);
                minutesEl.textContent = pad(minutes);
                secondsEl.textContent = pad(seconds);
                if (statusEl) statusEl.textContent = 'maintenance mode';
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>
</body>
</html>
