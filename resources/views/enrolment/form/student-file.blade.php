<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        Form Pengumpulan Berkas Pendaftaran - Mutiara Harapan Islamic School
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="/assets/static/css/student-file.css?v=1.0.0">
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
                        <h1 class="h4 mb-1">Form Pengumpulan Berkas Pendaftaran</h1>
                        <p class="mb-0">Mutiara Harapan Islamic School</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container main-container">
        <h2 class="section-title">Pengumpulan Dokumen Pendaftaran</h2>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-parent">
                    <div class="card-header-custom">Data Ayah</div>
                    <div class="card-body-custom">
                        <div class="parent-info">
                            <span class="parent-label">Nama:</span>
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
                    <div class="card-header-custom">Data Ibu</div>
                    <div class="card-body-custom">
                        <div class="parent-info">
                            <span class="parent-label">Nama:</span>
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
                    <div class="card-header-custom">Data Wali</div>
                    <div class="card-body-custom">
                        <div class="parent-info">
                            <span class="parent-label">Nama:</span>
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
                Informasi Calon Siswa
            </h5>
            <div class="student-info-row">
                <div class="student-label">Nama Lengkap:</div>
                <div class="student-value" id="studentFullName">{{ $admission->applicant->fullname ?? '--' }}</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Level:</div>
                <div class="student-value" id="studentLevel">{{ $admission->level->name ?? '--' }} /
                    {{ $admission->grade->name ?? '--' }}</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Tahun Akademik:</div>
                <div class="student-value" id="academicYear">{{ $admission->accademic_year ?? '--' }}</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Cabang:</div>
                <div class="student-value" id="branch">{{ $admission->branch->name ?? '--' }}</div>
            </div>
        </div>

        <div class="upload-section">
            <h3 class="section-title">Upload Dokumen Pendaftaran</h3>
            <p class="mb-4">
                Silakan upload dokumen-dokumen berikut ini. Pastikan dokumen terlihat
                jelas dan tidak terpotong.
            </p>

            <div class="upload-card" id="ktpAyahCard">
                <div class="document-title required">KTP Ayah</div>
                <div class="document-requirement">
                    Format: JPG, PNG, atau PDF. Maksimal ukuran: 5MB. Dokumen harus
                    jelas terbaca.
                </div>
                <input type="file" id="ktpAyahInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn" onclick="document.getElementById('ktpAyahInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="ktpAyahPreview"></div>
            </div>

            <div class="upload-card" id="ktpIbuCard">
                <div class="document-title required">KTP Ibu</div>
                <div class="document-requirement">
                    Format: JPG, PNG, atau PDF. Maksimal ukuran: 5MB. Dokumen harus
                    jelas terbaca.
                </div>
                <input type="file" id="ktpIbuInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn" onclick="document.getElementById('ktpIbuInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="ktpIbuPreview"></div>
            </div>

            <div class="upload-card" id="akteCard">
                <div class="document-title required">Akte Kelahiran Ananda</div>
                <div class="document-requirement">
                    Format: JPG, PNG, atau PDF. Maksimal ukuran: 5MB. Dokumen harus
                    jelas terbaca.
                </div>
                <input type="file" id="akteInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn" onclick="document.getElementById('akteInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="aktePreview"></div>
            </div>

            <div class="upload-card" id="kkCard">
                <div class="document-title required">Kartu Keluarga (KK)</div>
                <div class="document-requirement">
                    Format: JPG, PNG, atau PDF. Maksimal ukuran: 5MB. Halaman yang
                    berisi data keluarga.
                </div>
                <input type="file" id="kkInput" class="file-input" accept="image/*,.pdf"
                    capture="environment" />
                <button type="button" class="upload-btn" onclick="document.getElementById('kkInput').click()">
                    <i class="bi bi-upload"></i> Upload File
                </button>
                <div class="file-preview" id="kkPreview"></div>
            </div>
        </div>

        <div class="submit-section">
            <button type="button" class="btn btn-submit-custom btn-lg" id="submitBtn" disabled>
                <i class="bi bi-send-check"></i> Kirim Semua Berkas
            </button>
            <p class="text-muted mt-2">
                Pastikan semua dokumen yang diperlukan sudah diupload sebelum
                mengirim.
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
    <script src="/assets/compiled/js/script.js?v=1.1.3"></script>
    <script src="/assets/static/js/pages/student-file.js?v=1.0.0"></script>
</body>

</html>
