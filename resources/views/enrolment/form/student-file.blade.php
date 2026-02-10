<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        Form Pengumpulan Berkas Pendaftaran - Mutiara Harapan Islamic School
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="/assets/static/css/student-file.css?v=1.0.2">
</head>

<body>
    <header class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo" class="school-logo"
                        onerror="this.style.display='none';" />
                </div>
                <div class="col">
                    <div class="header-title">
                        <h1 class="h4 mb-1">Student & Parent Identification Data</h1>
                        <p class="mb-0">Form pengumpulan data orang tua dan siswa</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container main-container">
        {{-- <h2 class="section-title">Pengumpulan Dokumen Pendaftaran</h2> --}}

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-parent">
                    <div class="card-header-custom">Father’s Information</div>
                    <div class="card-body-custom">
                        <div class="parent-info">
                            <span class="parent-label">Name:</span>
                            <span class="parent-value" id="fatherName">{{ $father->fullname ?? '--' }}</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">HP:</span>
                            <span class="parent-value" id="fatherPhone">{{ $father->phone ?? '--' }}</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">Email:</span>
                            <span class="parent-value" id="fatherEmail">{{ $father->email ?? '--' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card card-parent">
                    <div class="card-header-custom">Mother's Information</div>
                    <div class="card-body-custom">
                        <div class="parent-info">
                            <span class="parent-label">Name:</span>
                            <span class="parent-value" id="motherName">{{ $mother->fullname ?? '--' }}</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">HP:</span>
                            <span class="parent-value" id="motherPhone">{{ $mother->phone ?? '--' }}</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">Email:</span>
                            <span class="parent-value" id="motherEmail">{{ $mother->email ?? '--' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card card-parent">
                    <div class="card-header-custom">Guardian's Information</div>
                    <div class="card-body-custom">
                        <div class="parent-info">
                            <span class="parent-label">Name:</span>
                            <span class="parent-value" id="guardianName">{{ $guardian->fullname ?? '--' }}</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">HP:</span>
                            <span class="parent-value" id="guardianPhone">{{ $guardian->phone ?? '--' }}</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">Email:</span>
                            <span class="parent-value" id="guardianEmail">{{ $guardian->email ?? '--' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="student-info-box">
            <h5 class="mb-3" style="color: var(--primary-color)">
                Student’s Information
            </h5>
            <div class="student-info-row">
                <div class="student-label">Full Name:</div>
                <div class="student-value" id="studentFullName">{{ $admission->applicant->fullname ?? '--' }}</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Level:</div>
                <div class="student-value" id="studentLevel">{{ $admission->level->name ?? '--' }} /
                    {{ $admission->grade->name ?? '--' }}</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Academic Year:</div>
                <div class="student-value" id="academicYear">{{ $admission->accademic_year ?? '--' }}</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Branch:</div>
                <div class="student-value" id="branch">{{ $admission->branch->name ?? '--' }}</div>
            </div>
        </div>

        <div class="upload-section">
            <input type="hidden" id="id" value="{{ $admission->id }}">
            <input type="hidden" id="code" value="{{ $admission->code }}">
            <h3 class="section-title">Upload Document</h3>
            <p class="mb-4">
                This section collects personal and identification information, including KTP, Family Card (KK), and
                Birth Certificate. Photograph of the student (4 × 6 cm): White collared shirt with red background, to be
                submitted in hardcopy before the first day of school.
            </p>

            <p>Additional Documents for Transfer Students</p>
            <ul class="mb-3">
                <li>Previous Report Cards or Academic Transcripts (last two academic years)</li>
                <li>Transfer Letter from the previous school and NISN</li>
                <li>Academic observation</li>
            </ul>

            <p>Additional Documents for International Transfer Students</p>
            <ul>
                <li>Passport – parent’s and student’s identification (foreign nationals)</li>
                <li>Visa or KITAS (Limited Stay Permit) — valid residence permit in Indonesia</li>
                <li>Grade Placement / Equivalency Letter – issued by the local Education Office (required for students
                    from overseas schools)</li>
                <li>Official Translation — all documents not in English or Indonesian must be translated by a certified
                    sworn translator</li>
                <li>Academic Equivalency Certificate – issued by the Ministry of Education through the local Education
                    Office</li>
            </ul>

            <p>
                <strong>Note:</strong><br>
                These documents may be submitted separately via email at
                <a href="mailto:admission@mutiaraharapan.sch.id">admission@mutiaraharapan.sch.id</a>.
            </p>

            <p class="fw-semibold">
                Kindly ensure that all documents are clear and complete.<br>
                <i><small>Silakan unggah kebutuhan dokumen dan pastikan terlihat jelas dan lengkap.</small></i>
            </p>
            <br>

            <div class="upload-card" id="ktp_fatherCard">
                <div class="document-title required">Father's Identity Card (ID Card)</div>
                <div class="document-requirement">
                    Format: JPG, PNG, or PDF. Maximum file size is 5MB. Please ensure the documents are clearly
                    readable.
                </div>
                <input type="file" id="ktp_fatherInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn"
                    onclick="document.getElementById('ktp_fatherInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="ktp_fatherPreview"></div>
            </div>

            <div class="upload-card" id="ktp_motherCard">
                <div class="document-title required">Mother's Identity Card (ID Card)</div>
                <div class="document-requirement">
                    Format: JPG, PNG, or PDF. Maximum file size is 5MB. Please ensure the documents are clearly
                    readable.
                </div>
                <input type="file" id="ktp_motherInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn"
                    onclick="document.getElementById('ktp_motherInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="ktp_motherPreview"></div>
            </div>

            <div class="upload-card" id="birth_certificateCard">
                <div class="document-title required">Student’s Birth Certificate</div>
                <div class="document-requirement">
                    Format: JPG, PNG, or PDF. Maximum file size is 5MB. Please ensure the documents are clearly
                    readable.
                </div>
                <input type="file" id="birth_certificateInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn"
                    onclick="document.getElementById('birth_certificateInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="birth_certificatePreview"></div>
            </div>

            <div class="upload-card" id="family_cardCard">
                <div class="document-title required">Family Card (KK)</div>
                <div class="document-requirement">
                    Format: JPG, PNG, or PDF. Maximum file size is 5MB. Please ensure the documents are clearly
                    readable.
                </div>
                <input type="file" id="family_cardInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn"
                    onclick="document.getElementById('family_cardInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="family_cardPreview"></div>
            </div>
        </div>

        <div class="submit-section">
            <button type="button" class="btn btn-submit-custom btn-lg" id="submitBtn" disabled>
                <i class="bi bi-send-check"></i> Submit File
            </button>
            <p class="text-muted mt-2">
                Make sure all required documents have been uploaded before submitting.
            </p>
        </div>
    </div>

    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.6"></script>
    <script src="/assets/static/js/pages/student-file.js?v=1.0.3"></script>
</body>

</html>
