<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        Form Pengumpulan Berkas Pendaftaran - Mutiara Harapan Islamic School
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #800000;
            --primary-light: #a33333;
            --primary-dark: #660000;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding-bottom: 2rem;
        }

        .header-section {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            border-bottom: 5px solid #ffc107;
        }

        .school-logo {
            max-height: 60px;
            width: auto;
        }

        @media (min-width: 768px) {
            .school-logo {
                max-height: 80px;
            }
        }

        .header-title {
            border-left: 3px solid rgba(255, 255, 255, 0.3);
            padding-left: 1rem;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-top: 2rem;
            padding: 2rem;
        }

        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .card-parent {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .card-parent:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .card-header-custom {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1rem;
            border-radius: 7px 7px 0 0 !important;
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .parent-info {
            margin-bottom: 0.5rem;
        }

        .parent-label {
            font-weight: 600;
            color: #495057;
            display: inline-block;
            width: 100px;
        }

        .parent-value {
            color: #333;
        }

        .student-info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .student-info-row {
            display: flex;
            margin-bottom: 1rem;
            align-items: center;
        }

        .student-label {
            font-weight: 600;
            color: var(--primary-color);
            width: 200px;
            flex-shrink: 0;
        }

        .student-value {
            color: #333;
            font-weight: 500;
        }

        .upload-section {
            margin-top: 2rem;
        }

        .upload-card {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .upload-card:hover {
            border-color: var(--primary-color);
            background-color: #fff;
        }

        .upload-card.uploaded {
            border-color: #28a745;
            border-style: solid;
            background-color: rgba(40, 167, 69, 0.05);
        }

        .upload-card.error {
            border-color: #dc3545;
            border-style: solid;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .document-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .document-requirement {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .upload-btn {
            background-color: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .upload-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .camera-btn {
            background-color: white;
            border: 2px solid #0d6efd;
            color: #0d6efd;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 0.5rem;
        }

        .camera-btn:hover {
            background-color: #0d6efd;
            color: white;
        }

        .file-preview {
            margin-top: 1rem;
            display: none;
        }

        .file-preview.show {
            display: block;
        }

        .file-info {
            display: flex;
            align-items: center;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .file-icon {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-right: 0.75rem;
        }

        .file-details {
            flex: 1;
        }

        .file-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .file-size {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .file-remove {
            color: #dc3545;
            cursor: pointer;
            font-size: 1.25rem;
        }

        .file-remove:hover {
            color: #bd2130;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
            margin-top: 0.5rem;
            border: 1px solid #dee2e6;
        }

        .submit-section {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        .btn-submit-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 0.75rem 3rem;
            font-size: 1.1rem;
        }

        .btn-submit-custom:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-submit-custom:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .file-input {
            display: none;
        }

        .camera-modal .modal-content {
            border-radius: 10px;
            overflow: hidden;
        }

        .camera-preview {
            width: 100%;
            height: 300px;
            background-color: #000;
            margin-bottom: 1rem;
            border-radius: 5px;
            overflow: hidden;
        }

        .camera-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1.5rem;
                margin-top: 1rem;
            }

            .card-parent {
                margin-bottom: 1.5rem;
            }

            .student-label {
                width: 150px;
            }

            .camera-btn {
                margin-left: 0;
                margin-top: 0.5rem;
                width: 100%;
                justify-content: center;
            }

            .upload-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .header-title h1 {
                font-size: 1.2rem;
            }

            .header-title p {
                font-size: 0.9rem;
            }

            .main-container {
                padding: 1rem;
            }

            .student-info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .student-label {
                width: 100%;
                margin-bottom: 0.25rem;
            }
        }
    </style>
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
                            <span class="parent-value" id="fatherName">-</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">HP:</span>
                            <span class="parent-value" id="fatherPhone">-</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">Email:</span>
                            <span class="parent-value" id="fatherEmail">-</span>
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
                            <span class="parent-value" id="motherName">-</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">HP:</span>
                            <span class="parent-value" id="motherPhone">-</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">Email:</span>
                            <span class="parent-value" id="motherEmail">-</span>
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
                            <span class="parent-value" id="guardianName">-</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">HP:</span>
                            <span class="parent-value" id="guardianPhone">-</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">Email:</span>
                            <span class="parent-value" id="guardianEmail">-</span>
                        </div>
                        <div class="parent-info">
                            <span class="parent-label">Status:</span>
                            <span class="parent-value" id="guardianStatus">Tidak ada data wali</span>
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
                <div class="student-value" id="studentFullName">-</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Level Pendidikan:</div>
                <div class="student-value" id="studentLevel">-</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Kelas:</div>
                <div class="student-value" id="studentClass">-</div>
            </div>
            <div class="student-info-row">
                <div class="student-label">Tahun Akademik:</div>
                <div class="student-value" id="academicYear">-</div>
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

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentDocumentType = "";
        let currentFacingMode = "environment";
        let mediaStream = null;
        const uploadedFiles = {
            ktpAyah: null,
            ktpIbu: null,
            akte: null,
            kk: null,
        };
        const sampleData = {
            father: {
                name: "Ahmad Sudirman",
                phone: "0812 3456 7890",
                email: "ahmad.sudirman@email.com",
            },
            mother: {
                name: "Siti Fatimah",
                phone: "0813 4567 8901",
                email: "siti.fatimah@email.com",
            },
            guardian: {
                name: "",
                phone: "",
                email: "",
                hasGuardian: false,
            },
            student: {
                fullName: "Muhammad Al-Fatih Sudirman",
                level: "SD",
                class: "Kelas 1",
                academicYear: "2024/2025",
            },
        };
        $(document).ready(function() {

            $("#fatherName").text(sampleData.father.name);
            $("#fatherPhone").text(sampleData.father.phone);
            $("#fatherEmail").text(sampleData.father.email);

            $("#motherName").text(sampleData.mother.name);
            $("#motherPhone").text(sampleData.mother.phone);
            $("#motherEmail").text(sampleData.mother.email);

            if (sampleData.guardian.hasGuardian) {
                $("#guardianName").text(sampleData.guardian.name);
                $("#guardianPhone").text(sampleData.guardian.phone);
                $("#guardianEmail").text(sampleData.guardian.email);
                $("#guardianStatus").text("Data wali tersedia");
            } else {
                $("#guardianName").text("-");
                $("#guardianPhone").text("-");
                $("#guardianEmail").text("-");
                $("#guardianStatus").text("Tidak ada data wali");
            }

            $("#studentFullName").text(sampleData.student.fullName);
            $("#studentLevel").text(sampleData.student.level);
            $("#studentClass").text(sampleData.student.class);
            $("#academicYear").text(sampleData.student.academicYear);

            $("#ktpAyahInput").on("change", function(e) {
                handleFileUpload(e, "ktpAyah");
            });

            $("#ktpIbuInput").on("change", function(e) {
                handleFileUpload(e, "ktpIbu");
            });

            $("#akteInput").on("change", function(e) {
                handleFileUpload(e, "akte");
            });

            $("#kkInput").on("change", function(e) {
                handleFileUpload(e, "kk");
            });

            $("#submitBtn").on("click", submitDocuments);


            window.removeFile = function(documentType) {
                uploadedFiles[documentType] = null;
                $(`#${documentType}Preview`).removeClass("show").html("");
                updateCardStatus(documentType, "removed");
                checkSubmitButton();
            };
            checkSubmitButton();
        });

        function handleFileUpload(event, documentType) {
            const file = event.target.files[0];
            if (!file) return;

            // Check file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
                showError(documentType, "Ukuran file terlalu besar. Maksimal 5MB.");
                return;
            }

            // Check file type
            const validTypes = [
                "image/jpeg",
                "image/jpg",
                "image/png",
                "application/pdf",
            ];
            if (!validTypes.includes(file.type)) {
                showError(
                    documentType,
                    "Format file tidak didukung. Gunakan JPG, PNG, atau PDF."
                );
                return;
            }

            uploadedFiles[documentType] = file;
            showFilePreview(file, documentType);
            updateCardStatus(documentType, "uploaded");
            checkSubmitButton();

            // Reset file input to allow uploading the same file again if needed
            event.target.value = "";
        }

        function showFilePreview(file, documentType) {
            const previewContainer = $(`#${documentType}Preview`);
            const fileSize = formatFileSize(file.size);

            let previewContent = "";

            if (file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContent = `
                            <div class="file-info">
                                <div class="file-icon">
                                    <i class="bi bi-file-image"></i>
                                </div>
                                <div class="file-details">
                                    <div class="file-name">${file.name}</div>
                                    <div class="file-size">${fileSize}</div>
                                </div>
                                <div class="file-remove" onclick="removeFile('${documentType}')">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                            </div>
                            <img src="${e.target.result}" class="preview-image" alt="Preview">
                        `;
                    previewContainer.html(previewContent).addClass("show");
                };
                reader.readAsDataURL(file);
            } else if (file.type === "application/pdf") {
                previewContent = `
                        <div class="file-info">
                            <div class="file-icon">
                                <i class="bi bi-file-pdf"></i>
                            </div>
                            <div class="file-details">
                                <div class="file-name">${file.name}</div>
                                <div class="file-size">${fileSize}</div>
                            </div>
                            <div class="file-remove" onclick="removeFile('${documentType}')">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                        <div class="alert alert-info mt-2">
                            <i class="bi bi-info-circle"></i> File PDF telah diupload. Pastikan isi dokumen sudah benar.
                        </div>
                    `;
                previewContainer.html(previewContent).addClass("show");
            }
        }

        function showError(documentType, message) {
            const card = $(`#${documentType}Card`);
            card.addClass("error");

            setTimeout(() => {
                card.removeClass("error");
            }, 3000);

            alert(message);
        }

        function updateCardStatus(documentType, status) {
            const card = $(`#${documentType}Card`);
            card.removeClass("uploaded error");

            if (status === "uploaded") {
                card.addClass("uploaded");
            } else if (status === "removed") {
                // Card returns to normal state
            }
        }

        function checkSubmitButton() {
            const allUploaded = Object.values(uploadedFiles).every(
                (file) => file !== null
            );
            $("#submitBtn").prop("disabled", !allUploaded);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return "0 Bytes";
            const k = 1024;
            const sizes = ["Bytes", "KB", "MB", "GB"];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return (
                parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
            );
        }

        function showSuccessMessage(message) {
            const alertDiv = $("<div>")
                .addClass(
                    "alert alert-success alert-dismissible fade show position-fixed"
                )
                .css({
                    top: "20px",
                    right: "20px",
                    "z-index": "9999",
                    "min-width": "300px",
                }).html(`
                        <i class="bi bi-check-circle"></i> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `);

            $("body").append(alertDiv);

            setTimeout(() => {
                alertDiv.alert("close");
            }, 3000);
        }

        function submitDocuments() {
            const formData = new FormData();
            Object.keys(uploadedFiles).forEach((key) => {
                if (uploadedFiles[key]) {
                    formData.append(key, uploadedFiles[key]);
                }
            });

            formData.append("studentName", sampleData.student.fullName);
            formData.append("timestamp", new Date().toISOString());

            console.log("Submitting documents:", formData);

            $("#submitBtn").html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengupload...'
            );
            $("#submitBtn").prop("disabled", true);

            setTimeout(() => {
                $("#submitBtn").html(
                    '<i class="bi bi-send-check"></i> Kirim Semua Berkas'
                );
                $("#submitBtn").prop("disabled", false);

                showSuccessMessage(
                    "Semua dokumen berhasil diupload! Terima kasih."
                );
            }, 2000);
        }
    </script>
</body>

</html>
