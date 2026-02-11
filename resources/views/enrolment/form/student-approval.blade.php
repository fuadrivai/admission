<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Persetujuan Orang Tua - Mutiara Harapan Islamic School</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="/assets/static/css/student-approval.css?v=1.0.1">
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
                        <h1 class="h4 mb-1">Parent’s Statement Form</h1>
                        <p class="mb-0">Formulir persetujuan orang tua</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Wizard Container -->
    <div class="container main-container">
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-title">Confirmation</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">Payment</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">Statement</div>
            </div>
            {{-- <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">Guardian</div>
            </div> --}}
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">Drug</div>
            </div>
            <div class="step" data-step="5">
                <div class="step-number">5</div>
                <div class="step-title">Student</div>
            </div>
        </div>

        <!-- Step 1: Syarat Pendaftaran -->
        <div class="step-content active" id="step-1">
            @include('enrolment.form.approval.step1')
        </div>

        <!-- Step 2: Form Persetujuan Pembayaran -->
        <div class="step-content" id="step-2">
            @include('enrolment.form.approval.step2')
        </div>

        <!-- Step 3: Form Pernyataan Orang Tua -->
        <div class="step-content" id="step-3">
            @include('enrolment.form.approval.step3')
        </div>

        <!-- Step 4: Form Pernyataan Wali Murid -->
        {{-- <div class="step-content" id="step-4">
            @include('enrolment.form.approval.step4')
        </div> --}}

        <!-- Step 5: Surat Pernyataan Tes Narkotika -->
        <div class="step-content conditional-section" id="step-4">
            @include('enrolment.form.approval.step5')
        </div>

        <!-- Step 6: Surat Pernyataan Siswa -->
        <div class="step-content conditional-section" id="step-5">
            @include('enrolment.form.approval.step6')
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

    <!-- jQuery -->
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.6"></script>
    <script src="/assets/static/js/pages/student-approval.js?v=1.0.4"></script>
</body>

</html>
