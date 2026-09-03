<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Successful</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/assets/static/css/enrolment-external.css?v=1.0.2">
    <style>
        :root {
            --maroon-900: #4b0000;
            --maroon-800: #660000;
            --maroon-700: #7b0d0d;
            --maroon-600: #8d1c1c;
            --maroon-200: #f8dfe2;
            --maroon-100: #fef0f2;
            --maroon-50: #fef7f8;
            --soft-white: #fffafc;
            --text-dark: #2f1d1d;
            --text-muted: #6b4d4d;
            --line: #eed5d8;
            --success: #2d8a5d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(180deg, #fffaf9 0%, #fff 100%);
            color: var(--text-dark);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .event-registration-wrapper {
            min-height: 100vh;
            padding: 24px 12px 40px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .event-registration-container {
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
        }

        .registration-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: 0 20px 45px rgba(64, 0, 0, 0.12);
            overflow: hidden;
        }

        .event-card-header {
            background: linear-gradient(135deg, var(--maroon-900) 0%, var(--maroon-700) 100%);
            color: white;
            padding: 28px 18px 24px;
            text-align: center;
            position: relative;
        }

        .event-card-header::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 16px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.12) 100%);
        }

        .event-card-header img {
            width: min(58%, 220px);
            max-width: 220px;
            height: auto;
            margin-bottom: 12px;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.18));
        }

        .event-card-header h1 {
            margin: 0;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            line-height: 1.25;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
        }

        .event-card-body {
            padding: 28px 18px 30px;
        }

        .success-content {
            text-align: center;
        }

        .success-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 1.5rem;
        }

        .event-title {
            margin: 0 0 1.5rem;
            color: var(--maroon-800);
            font-size: clamp(1.6rem, 2.4vw, 2.3rem);
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .success-message {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .success-code-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .success-code {
            background: #f0fdf4;
            border: 2px solid #dcfce7;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 1.1rem;
            font-weight: 600;
            color: #15803d;
            word-break: break-all;
            box-shadow: 0 4px 12px rgba(45, 138, 93, 0.1);
        }

        .success-note {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--maroon-800) 0%, var(--maroon-600) 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.01em;
            padding: 12px 18px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 10px 22px rgba(99, 0, 0, 0.2);
            text-decoration: none;
        }

        .cta-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(99, 0, 0, 0.26);
            color: #fff;
            text-decoration: none;
        }

        .cta-button:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(139, 30, 30, 0.18);
        }

        @media (max-width: 576px) {
            .event-registration-wrapper {
                padding: 16px 10px 28px;
            }

            .event-card-body {
                padding: 20px 14px 22px;
            }

            .event-card-header {
                padding: 22px 14px 18px;
            }

            .success-icon {
                font-size: 3.5rem;
                margin-bottom: 1rem;
            }

            .event-title {
                margin-bottom: 1rem;
            }

            .success-message {
                margin-bottom: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="event-registration-wrapper">
        <div class="event-registration-container">
            <div class="registration-card">
                <div class="event-card-header">
                    <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo"
                        onerror="this.style.display='none';">
                    <h1>Registration Successful</h1>
                </div>

                <div class="event-card-body">
                    <div class="success-content">
                        <div class="success-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>

                        <h2 class="event-title">{{ $event->title }}</h2>

                        <p class="success-message">
                            Thank you for registering for this event. Your registration has been successfully submitted.
                        </p>

                        <a href="/events/{{ $event->slug }}" class="cta-button">
                            <i class="fa fa-home" style="margin-right: 8px;"></i> Back to Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
