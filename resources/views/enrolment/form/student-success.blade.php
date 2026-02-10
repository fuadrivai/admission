<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Sukses - Mutiara Harapan Islamic School</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="/assets/static/css/student-approval.css?v=1.0.0">

    <style>
        .success-container {
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
            padding: 40px 0;
            min-height: 60vh;
        }

        .success-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(128, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
        }

        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
            animation: bounce 0.6s ease-in-out;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .success-title {
            color: #800000;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .success-subtitle {
            color: #666666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.7;
            text-align: left;
        }

        .greeting-text {
            color: #800000;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: left;
        }

        .confirmation-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }

        .email-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .email-alert h5 {
            font-weight: bold;
            margin-bottom: 12px;
            color: #800000;
        }

        .email-alert ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .email-alert li {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .info-section {
            background-color: #f8f9fa;
            border-left: 4px solid #800000;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: left;
        }

        .info-section h4 {
            color: #800000;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6b2b2b;
            width: 35%;
        }

        .info-value {
            color: #333333;
            flex: 1;
            text-align: right;
        }

        .thank-you-section {
            background-color: #fff5f5;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
            border-left: 4px solid #d4af37;
        }

        .thank-you-section p {
            color: #555555;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 35px;
            flex-wrap: wrap;
        }

        .btn-custom-maroon {
            background-color: #800000;
            color: white;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-custom-maroon:hover {
            background-color: #600000;
            color: white;
            text-decoration: none;
        }

        .btn-custom-outline {
            border: 2px solid #800000;
            color: #800000;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-custom-outline:hover {
            background-color: #800000;
            color: white;
            text-decoration: none;
        }

        .contact-section {
            background-color: #f8f9fa;
            border-left: 4px solid #d4af37;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            text-align: center;
        }

        .contact-section p {
            margin: 0;
            color: #666666;
            line-height: 1.7;
        }

        .contact-section strong {
            color: #800000;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <header class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo" class="school-logo"
                        onerror="this.style.display='none';" />
                </div>
                <div class="col">
                    <div class="header-title">
                        <h1 class="h4 mb-1">Enrolment Documents Submission</h1>
                        <p class="mb-0">Pengajuan Dokumen Pendaftaran</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Success Container -->
    <div class="success-container">
        <div class="container">
            <div class="success-card">
                <!-- Success Icon -->
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <!-- Success Title -->
                <h2 class="success-title">Submission Successful!</h2>

                <!-- Greeting -->
                <p class="greeting-text">Hello, Parents of Champions!</p>

                <!-- Main Content -->
                <div class="success-subtitle">
                    <p>
                        All your required enrolment documents have been successfully submitted and processed. Please
                        check your email inbox for important messages from us, including:
                    </p>
                </div>

                <!-- Email Alert Box -->
                <div class="email-alert">
                    <h5><i class="bi bi-envelope-check"></i> Check Your Email for:</h5>
                    <ul>
                        <li>Confirmation of your child's enrolment documents</li>
                        <li>A summary of the signed Statement Form</li>
                        <li>Further information regarding the next steps in the process</li>
                    </ul>
                </div>

                <!-- Student Information -->
                @if (isset($data) && $data)
                    <div class="info-section">
                        <h4><i class="bi bi-person-circle"></i> Student Information</h4>
                        <div class="info-row">
                            <div class="info-label">Enrolment Code</div>
                            <div class="info-value"><strong>{{ $data->code ?? '-' }}</strong></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">{{ $data->applicant->fullname ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Level / Grade</div>
                            <div class="info-value">{{ optional($data->level)->name ?? '-' }} /
                                {{ optional($data->grade)->name ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Academic Year</div>
                            <div class="info-value">{{ $data->accademic_year ?? '-' }}</div>
                        </div>
                    </div>
                @endif

                <!-- Thank You Message -->
                <div class="thank-you-section">
                    <p>
                        <strong>Thank you for completing all the steps so promptly.</strong> We're excited to welcome
                        your child to our school community and can't wait to see them thrive!
                    </p>
                </div>

                <!-- Contact Information -->
                <div class="contact-section">
                    <p>
                        <i class="bi bi-question-circle"></i> <strong>Do you have any questions?</strong><br>
                        Feel free to contact our Admissions team. We are here to assist you with any inquiries.
                    </p>
                </div>

                <!-- Action Buttons -->
                {{-- <div class="action-buttons">
                    <a href="{{ route('enrolment.dashboard') ?? '#' }}" class="btn-custom-maroon">
                        <i class="bi bi-house"></i> Back to Dashboard
                    </a>
                    <a href="{{ route('enrolment.download-confirmation') ?? '#' }}" class="btn-custom-outline">
                        <i class="bi bi-download"></i> Download Confirmation
                    </a>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
</body>

</html>
