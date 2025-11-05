<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MHIS School Visit Confirmation</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --fandango: #9B1134;
            --fandango-light: #b3294c;
            --fandango-dark: #8e082a;
            --fandango-bg: rgba(155, 17, 52, 0.15);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            padding-bottom: 2rem;
        }

        .success-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12);
            padding: 3rem 2rem;
            text-align: center;
            background-color: white;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--fandango-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--fandango);
            font-size: 3rem;
            box-shadow: 0 6px 16px rgba(155, 17, 52, 0.2);
        }

        .success-title {
            font-weight: 800;
            color: var(--fandango);
            margin-bottom: 1rem;
        }

        .success-text {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: var(--fandango);
            border-color: var(--fandango);
            padding: 0.875rem 2.5rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--fandango-dark);
            border-color: var(--fandango-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(181, 51, 137, 0.25);
        }

        .support-text {
            margin-top: 2rem;
            font-size: 0.95rem;
            color: #666;
        }

        .support-text a {
            color: var(--fandango);
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="col-md-8 col-lg-6">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="success-title">Success!</h2>
                <p class="success-text">
                    Thank you for completing the School Visit form. <br>
                    The full details of your scheduled visit have been sent to your email. <br>
                    Please check your inbox for more information.
                </p>

                <a href="{{ url('/schoolvisit-form') }}" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Back to Form
                </a>

                <div class="support-text">
                    If you have not received the email, please check your <strong>Spam</strong> folder or contact us at
                    <a href="mailto:admission@mutiaraharapan.sch.id">admission@mutiaraharapan.sch.id</a>.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
