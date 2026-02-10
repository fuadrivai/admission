<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Data Diri - Mutiara Harapan Islamic School</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/assets/static/css/student-enrolment.css?v=1.0.2">

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
                        <h1 class="h4 mb-1">Student and Parent Information Form</h1>
                        <p class="mb-0">Formulir Data Diri Siswa dan Orang Tua</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Wizard Container -->
    <div class="container wizard-container">
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-title">Code</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">Student</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">Father</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">Mother</div>
            </div>
            <div class="step" data-step="5">
                <div class="step-number">5</div>
                <div class="step-title">Guardian</div>
            </div>
            <div class="step" data-step="6">
                <div class="step-number">6</div>
                <div class="step-title">Medical</div>
            </div>
        </div>

        <!-- Step 1: Introduction & Enrollment Code -->
        <div class="step-content active" id="step-1">
            @include('enrolment.form.enrolment.step1')
        </div>

        <!-- Step 2: Student Data -->
        <div class="step-content" id="step-2">
            @include('enrolment.form.enrolment.step2')
        </div>


        <!-- Step 3: Father Data -->
        <div class="step-content" id="step-3">
            @include('enrolment.form.enrolment.step3')
        </div>


        <!-- Step 4: Mother Data -->
        <div class="step-content" id="step-4">
            @include('enrolment.form.enrolment.step4')
        </div>


        <!-- Step 5: Guardian Data (Optional) -->
        <div class="step-content" id="step-5">
            @include('enrolment.form.enrolment.step5')
        </div>


        <!-- Step 6: Student Health Data & Declaration -->
        <div class="step-content" id="step-6">
            @include('enrolment.form.enrolment.step6')
            <!-- Final Submit Section -->
            <div class="final-submit-section">
                <div class="alert alert-success">
                    <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Form Completion</h5>
                    <p class="mb-3">Thank you for your time. You have completed all sections of the enrolment form.
                        Please review the information provided before submitting.</p>
                    <button type="button" class="btn btn-success btn-lg" disabled id="final-submit-btn">
                        <i class="bi bi-send-check"></i> Submit
                    </button>
                </div>
            </div>
        </div>

        <!-- Wizard Navigation -->
        <div class="wizard-navigation">
            <div>
                <button type="button" class="btn btn-outline-primary-custom" id="prev-btn" disabled>
                    <i class="bi bi-chevron-left"></i> Sebelumnya
                </button>
                <button type="button" class="btn btn-primary-custom" id="next-btn">
                    Selanjutnya <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.6"></script>
    <script src="/assets/static/js/constant.js?v=1.1.5"></script>
    <script src="/assets/static/js/pages/student-enrolment.js?v=1.0.3"></script>
</body>

</html>
