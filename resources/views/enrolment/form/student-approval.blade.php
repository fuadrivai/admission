<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Persetujuan Orang Tua - Mutiara Harapan Islamic School</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #800000;
            --primary-light: #a33333;
            --primary-dark: #660000;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            padding: 1.5rem;
            background-color: #f1f3f4;
            border-bottom: 1px solid #dee2e6;
            overflow-x: auto;
            white-space: nowrap;
        }

        .step {
            text-align: center;
            flex: 1;
            position: relative;
            min-width: 80px;
            padding: 0 0.5rem;
        }

        .step-number {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #ced4da;
            color: #495057;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.3rem;
            font-weight: bold;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background-color: var(--primary-color);
            color: white;
        }

        .step.completed .step-number {
            background-color: #28a745;
            color: white;
        }

        .step-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            line-height: 1.2;
        }

        .step.active .step-title {
            color: var(--primary-color);
        }

        .step-content {
            padding: 2rem;
            display: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .step-content.active {
            display: block;
        }

        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .subsection-title {
            color: var(--primary-dark);
            font-weight: 600;
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #dee2e6;
        }

        .info-box {
            background-color: #e7f1ff;
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0 5px 5px 0;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-primary-custom {
            color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
        }

        .btn-outline-primary-custom:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .wizard-navigation {
            padding: 1.5rem 2rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
        }

        .validation-error {
            border-color: #dc3545 !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none;
        }

        .checkbox-declaration {
            border: 1px solid #dee2e6;
            padding: 1.5rem;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 1.5rem;
        }

        .statement-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .statement-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .parent-info-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #f8f9fa;
            margin-bottom: 1.5rem;
        }

        .parent-info-row {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .parent-label {
            font-weight: 600;
            color: #495057;
            width: 180px;
            flex-shrink: 0;
        }

        .parent-value {
            color: #333;
        }

        .student-info-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #fff;
            margin-bottom: 1.5rem;
        }

        .student-info-row {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .student-label {
            font-weight: 600;
            color: var(--primary-color);
            width: 150px;
            flex-shrink: 0;
        }

        .student-value {
            color: #333;
        }

        .money-input-group {
            position: relative;
        }

        .money-input-group .form-control {
            padding-left: 2.5rem;
        }

        .money-input-group .input-group-text {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 3;
            background-color: transparent;
            border: none;
            font-weight: 600;
            color: var(--primary-color);
        }

        .terbilang-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 0.75rem;
            margin-top: 0.5rem;
            font-style: italic;
        }

        .current-date-display {
            font-weight: 600;
            color: var(--primary-color);
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .conditional-section {
            display: none;
        }

        @media (max-width: 768px) {
            .step-indicator {
                padding: 1rem;
            }

            .step {
                min-width: 70px;
            }

            .step-number {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .step-title {
                font-size: 0.7rem;
            }

            .step-content {
                padding: 1.5rem;
            }

            .wizard-navigation {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .wizard-navigation div {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .parent-label,
            .student-label {
                width: 120px;
            }
        }

        @media (max-width: 576px) {
            .step {
                min-width: 60px;
            }

            .step-content {
                padding: 1rem;
            }

            .header-title h1 {
                font-size: 1.2rem;
            }

            .header-title p {
                font-size: 0.9rem;
            }

            .parent-label,
            .student-label {
                width: 100px;
            }

            .parent-info-row,
            .student-info-row {
                flex-direction: column;
            }

            .parent-label,
            .student-label {
                width: 100%;
                margin-bottom: 0.25rem;
            }
        }

        .final-submit-section {
            text-align: center;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-top: 2rem;
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
                        <h1 class="h4 mb-1">Form Persetujuan Orang Tua</h1>
                        <p class="mb-0">Mutiara Harapan Islamic School</p>
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
                <div class="step-title">Syarat Pendaftaran</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">Persetujuan Pembayaran</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">Pernyataan Orang Tua</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">Pernyataan Wali Murid</div>
            </div>
            <div class="step" data-step="5">
                <div class="step-number">5</div>
                <div class="step-title">Tes Narkotika</div>
            </div>
            <div class="step" data-step="6">
                <div class="step-number">6</div>
                <div class="step-title">Pernyataan Siswa</div>
            </div>
        </div>

        <!-- Step 1: Syarat Pendaftaran -->
        <div class="step-content active" id="step-1">
            <h2 class="section-title">SYARAT PENDAFTARAN</h2>

            <p>Mengisi dokumen persyaratan pendaftaran sebagai berikut :</p>

            <ol class="mb-4">
                <li>Surat/form persetujuan pembayaran biaya sekolah</li>
                <li>Surat/form pernyataan orang tua/wali murid</li>
            </ol>

            <div class="info-box">
                <p class="mb-2">Untuk rincian berkas di bawah ini dapat diisi melalui link yang berbeda
                    <strong>mhis.link/PengumpulanBerkasMHIS</strong>
                </p>
                <ol class="mb-0">
                    <li>Scan KTP Ayah dan Bunda -> masing – masing 1 lembar</li>
                    <li>Scan Akte Kelahiran siswa -> 1 lembar</li>
                    <li>Scan Kartu keluarga -> 1 lembar</li>
                    <li>Foto 6x4 (kemeja putih berkerah) background merah (dikumpulkan hard file ke sekolah, dapat
                        dikumpulkan sebelum interview dengan Principal)</li>
                </ol>
            </div>

            <div class="info-box">
                <h5 class="mb-2">Dokumen khusus tambahan untuk siswa pindahan:</h5>
                <ol class="mb-0">
                    <li>Surat keterangan pindah dari sekolah asal dan NISN (Nomor Induk Siswa Nasional) yang sudah
                        divalidasi oleh Dinas Pendidikan setempat (harus dikumpulkan sebelum interview dengan Principal)
                    </li>
                    <li>Scan raport cover dan kelas terakhir -> 1 rangkap (harus dikumpulkan sebelum interview dengan
                        Principal)</li>
                </ol>
            </div>

            <h3 class="subsection-title">FORM PERNYATAAN PERSETUJUAN PEMBAYARAN BIAYA SEKOLAH</h3>

            <div class="mb-4">
                <label for="parentSelector" class="form-label required">Atas nama (Yang Menyetujui)</label>
                <select class="form-select" id="parentSelector" required>
                    <option value="" selected disabled>Pilih yang menyetujui</option>
                    <option value="father">Ayah</option>
                    <option value="mother">Bunda</option>
                    <option value="guardian">Wali</option>
                </select>
                <div class="error-message" id="parentSelector-error">Harap pilih yang menyetujui</div>
            </div>

            <!-- Parent Information Display -->
            <div class="parent-info-card" id="parentInfoCard" style="display: none;">
                <h5 class="mb-3">Informasi Orang Tua/Wali</h5>
                <div class="parent-info-row">
                    <div class="parent-label">Nama Lengkap:</div>
                    <div class="parent-value" id="parentFullName">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Email:</div>
                    <div class="parent-value" id="parentEmail">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Nomor HP:</div>
                    <div class="parent-value" id="parentPhone">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Tempat Lahir:</div>
                    <div class="parent-value" id="parentBirthPlace">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Tanggal Lahir:</div>
                    <div class="parent-value" id="parentBirthDate">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">No. KTP:</div>
                    <div class="parent-value" id="parentIdCard">-</div>
                </div>
            </div>

            <div class="statement-item">
                <p>Menyatakan bahwa benar Saya adalah merupakan orang tua/wali yang sepenuhnya berwenang dari calon
                    siswa Mutiara Harapan Islamic School tersebut di bawah.</p>
            </div>

            <!-- Student Information Display -->
            <div class="student-info-card">
                <h5 class="mb-3">Informasi Calon Siswa</h5>
                <div class="student-info-row">
                    <div class="student-label">Nama Lengkap:</div>
                    <div class="student-value" id="studentFullName">Muhammad Al-Fatih Sudirman</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Usia:</div>
                    <div class="student-value" id="studentAge">7 tahun 2 bulan</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Level:</div>
                    <div class="student-value" id="studentLevel">SD</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Kelas:</div>
                    <div class="student-value" id="studentGrade">Kelas 1</div>
                </div>
            </div>
        </div>

        <!-- Step 2: Form Persetujuan Pembayaran -->
        <div class="step-content" id="step-2">
            <h2 class="section-title">FORM PERNYATAAN PERSETUJUAN PEMBAYARAN BIAYA SEKOLAH</h2>

            <div class="statement-item">
                <p>Dengan ini menyatakan MENYETUJUI SEPENUHNYA seluruh ketentuan dan tata cara pembayaran dan bersedia
                    untuk membayar</p>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreePayment1" required>
                    <label class="form-check-label required" for="agreePayment1">Iya, saya menyetujui
                        sepenuhnya</label>
                    <div class="error-message" id="agreePayment1-error">Harap setujui pernyataan ini</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="developmentFee" class="form-label required">Development Fee</label>
                    <div class="money-input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="developmentFee" placeholder="12,500,000"
                            required>
                    </div>
                    <div class="error-message" id="developmentFee-error">Harap masukkan development fee</div>
                    <div class="terbilang-display" id="developmentFeeTerbilang">-</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="annualFee" class="form-label required">Annual Fee</label>
                    <div class="money-input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="annualFee" placeholder="4,000,000" required>
                    </div>
                    <div class="error-message" id="annualFee-error">Harap masukkan annual fee</div>
                    <div class="terbilang-display" id="annualFeeTerbilang">-</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="schoolFee" class="form-label required">School Fee</label>
                    <div class="money-input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="schoolFee" placeholder="1,750,000" required>
                    </div>
                    <div class="error-message" id="schoolFee-error">Harap masukkan school fee</div>
                    <div class="terbilang-display" id="schoolFeeTerbilang">-</div>
                </div>
            </div>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Development fee akan Saya bayar sesuai dengan ketentuan yang berlaku pada saat pendaftaran siswa
                        baru Mutiara Harapan Islamic School.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment2" required>
                        <label class="form-check-label required" for="agreePayment2">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment2-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Annual fee dan school fee akan Saya bayar pada saat putra/putri Saya dinyatakan diterima di
                        Mutiara Harapan Islamic School.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment3" required>
                        <label class="form-check-label required" for="agreePayment3">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment3-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang Ujian International (Cambridge International Examination: Checkpoint/IGCSE/A Level) yang
                        bersifat wajib dan besarnya ditetapkan pada saat ujian akan dilaksanakan.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment4" required>
                        <label class="form-check-label required" for="agreePayment4">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment4-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang sumber pembelajaran, baik dalam bentuk buku cetak atau sumber digital, yang besarnya
                        ditetapkan berdasarkan info dari penerbit.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment5" required>
                        <label class="form-check-label required" for="agreePayment5">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment5-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang ekstrakurikuler sesuai dengan ekstrakurikuler yang dipilih putra/putri Saya.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment6" required>
                        <label class="form-check-label required" for="agreePayment6">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment6-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang kegiatan lainnya yang besarnya ditetapkan pada saat kegiatan akan dilaksanakan.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment7" required>
                        <label class="form-check-label required" for="agreePayment7">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment7-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>School fee Saya bayarkan per bulan, pembayaran paling lambat tanggal 10 bulan berjalan.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment8" required>
                        <label class="form-check-label required" for="agreePayment8">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment8-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang Ittihada yang besarnya ditetapkan kemudian oleh Ittihada dan dibayarkan per tahun.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment9" required>
                        <label class="form-check-label required" for="agreePayment9">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment9-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Saya menyatakan kesanggupan saya untuk melunasi seluruh kewajiban pembayaran tersebut di atas
                        sebelum kegiatan belajar di Mutiara Harapan Islamic School dimulai dan saya bersedia menerima
                        segala konsekuensinya apabila tidak memenuhi kewajiban tersebut, termasuk namun tidak terbatas
                        pada hilangnya hak anak saya untuk mengikuti kegiatan belajar di Mutiara Harapan Islamic School
                        sebelum kewajiban tersebut kami lunasi seluruhnya.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment10" required>
                        <label class="form-check-label required" for="agreePayment10">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment10-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Selanjutnya saya juga menyatakan Setuju/Sepakat bahwa :</p>
                    <ol type="A" class="mb-3">
                        <li>Annual fee di setiap jenjang kenaikannya 2 tahun sekali maksimal 20% dari besaran pada tahun
                            tersebut.</li>
                        <li>School fee besarnya ditetapkan melalui kebijakan sekolah dengan ketentuan kenaikannya 3
                            tahun sekali maksimal 20% dari besaran pada tahun tersebut.</li>
                        <li>Development fee, annual fee dan school fee yang sudah Saya bayar tidak akan Saya tarik
                            kembali atau Saya alihkan dengan alasan apapun, kecuali putra/putri Saya dinyatakan tidak
                            diterima di Mutiara Harapan Islamic School.</li>
                        <li>Saya tidak dapat mengambil hasil evaluasi belajar putra/putri Saya sebelum Saya memenuhi
                            seluruh kewajiban Administrasi yang ditetapkan MHIS.</li>
                        <li>Saya menyatakan kesanggupan saya untuk melunasi seluruh kewajiban pembayaran tersebut di
                            atas dan menyerahkan seluruh kelengkapan dokumen yang diperlukan sebelum kegiatan belajar di
                            Mutiara Harapan Islamic School dimulai dan saya bersedia menerima segala konsekuensinya
                            apabila tidak memenuhi kewajiban tersebut, termasuk namun tidak terbatas pada hilangnya hak
                            anak saya untuk mengikuti kegiatan belajar di Mutiara Harapan Islamic School sebelum
                            kewajiban tersebut kami lunasi seluruhnya.</li>
                    </ol>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment11" required>
                        <label class="form-check-label required" for="agreePayment11">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment11-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Saya juga menyatakan bahwa Saya memahami sepenuhnya seluruh isi formulir ini dan Saya
                        menandatanganinya dalam keadaan sehat jasmani dan rohani, tanpa tekanan atau paksaan pihak
                        manapun untuk dipergunakan sebagaimana mestinya.</p>

                    <div class="current-date-display" id="currentDate1"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment12" required>
                        <label class="form-check-label required" for="agreePayment12">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment12-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Form Pernyataan Orang Tua -->
        <div class="step-content" id="step-3">
            <h2 class="section-title">FORM PERNYATAAN ORANG TUA / WALI MURID</h2>

            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle"></i> Mohon dibaca secara seksama sebelum diisi.
            </div>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Dengan ini Saya menyatakan bahwa saya setuju:</p>
                    <p>Bersedia menaati dan mendukung seluruh Peraturan, Ketentuan dan Tata Tertib yang ditentukan oleh
                        Mutiara Harapan Islamic School, baik yang sudah berjalan, ataupun yang akan ditentukan kemudian.
                    </p>
                </div>

                <div class="statement-item">
                    <p>Bersedia bekerja sama dengan Principal, Teachers, Administration Staff, Manajemen serta seluruh
                        perangkat sekolah dengan baik.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia mendukung seluruh kegiatan anak Saya baik di sekolah maupun di luar sekolah dan akan
                        memberikan pendidikan serta kegiatan yang searah dan sejalan dengan program sekolah terhadap
                        anak Saya di rumah.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia berkomunikasi dan berkoordinasi dengan pihak sekolah, termasuk datang jika
                        diminta/diundang oleh pihak sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia mendahulukan dan mengutamakan kepentingan sekolah di atas keperluan lainnya.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia apabila ditunjuk oleh pihak sekolah untuk menjadi pengurus Persatuan Orang Tua Murid dan
                        Guru (Ittihada) di lingkungan Mutiara Harapan Islamic School.</p>
                </div>

                <div class="statement-item">
                    <p>Saya paham bahwa selain kurikulum nasional sekolah juga menggunakan kurikulum internasional
                        sehingga penilaian dan kriteria promosi siswa didasarkan pada kedua kurikulum tersebut sehingga
                        Saya akan mematuhi sepenuhnya.</p>
                </div>

                <div class="statement-item">
                    <p>Dengan mengajukan form ini, Saya sepenuhnya akan mengikuti serta mendukung sekolah dalam berbagai
                        program dan kegiatan, untuk pencapaian tujuan sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Saya setuju untuk mematuhi dan mendukung seluruh ketentuan ini dengan menjaga dan tidak akan
                        melakukan tindakan dan reputasi dan nama baik Sekolah. Saya juga berkomitmen untuk menjaga
                        kerahasiaan seluruh informasi, data, serta dokumen yang berkaitan dengan sekolah kepada pihak
                        manapun tanpa persetujuan tertulis dari sekolah. Saya dapat menerima segala konsekuensinya,
                        termasuk namun tidak terbatas, pada anak saya dikeluarkan secara sepihak dari sekolah apabila
                        Saya atau anak Saya gagal memenuhi ketentuan ini.</p>
                </div>

                <div class="statement-item">
                    <p>Bahwa apabila berdasarkan observasi dan assessment baik sebelum (trial class) maupun setelah
                        proses belajar mengajar berjalan, ternyata anak saya memerlukan penanganan khusus, Saya
                        menyetujui anak saya dipindahkan ke Development Class.</p>
                </div>

                <div class="statement-item">
                    <p>Sehubungan dengan butir 10 apabila anak saya dipindahkan ke Development Class, Saya bersedia
                        mengikuti semua ketentuan yang berlaku di Development Class.</p>
                </div>

                <div class="statement-item">
                    <p>Saya memberi izin kepada Sekolah untuk mengadakan tes psikologis pada anak saya kapanpun
                        diperlukan oleh sekolah. Pemberitahuan pra-tindakan akan diberikan kepada saya untuk persetujuan
                        lebih lanjut.</p>
                </div>

                <div class="statement-item">
                    <p>Saya mengetahui dan menyetujui sepenuhnya bahwa sekolah melakukan segala hal yang dirasa perlu
                        dengan sebaik-baiknya sesuai tujuan pendidikan dengan mengindahkan prosedur kesehatan dan
                        keamanan dengan mengedepankan kebaikan anak Saya. Oleh karenanya Saya membebaskan sekolah dari
                        segala tuntutan serta membebaskan Mutiara Harapan Islamic School dari segala macam bentuk
                        tanggung jawab termasuk namun tidak terbatas pada gugatan perdata maupun tuntutan pidana.</p>
                </div>

                <div class="statement-item">
                    <p>Apabila Saya memerlukan antar jemput sekolah, Saya sepenuhnya setuju pada keputusan sekolah yang
                        ditetapkan berdasarkan survey rute dan atau kapasitas kendaraan antar jemput sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Saya memberi izin kepada Sekolah untuk memproduksi foto dan video anak Saya dalam media apapun.
                        Saya mengerti bahwa gambar – gambar tersebut akan digunakan untuk kepentingan internal dan
                        eksternal serta kepentingan promosi/publikasi sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Atas nama anak Saya, Saya memberi izin kepada pihak Sekolah untuk menggunakan hasil karya anak
                        Saya dalam media apapun (termasuk hasil karya tulis, audio dan materi visual)</p>
                </div>

                <div class="statement-item">
                    <p>Saya juga menyatakan bahwa Saya memahami sepenuhnya seluruh isi formulir ini dan Saya
                        menandatanganinya dalam keadaan sehat jasmani dan rohani, tanpa tekanan atau paksaan pihak
                        manapun untuk dipergunakan sebagaimana mestinya di Mutiara Harapan Islamic School.</p>

                    <div class="current-date-display" id="currentDate2"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeStatement1" required>
                        <label class="form-check-label required" for="agreeStatement1">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreeStatement1-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Form Pernyataan Wali Murid -->
        <div class="step-content" id="step-4">
            <h2 class="section-title">FORM PERNYATAAN ORANG TUA / WALI MURID</h2>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Dengan ini menyatakan bersedia mengikuti sosialisasi orang tua peserta didik baru sesuai dengan
                        waktu yang akan ditetapkan kemudian.</p>
                </div>

                <div class="statement-item">
                    <p>Demikian form pernyataan ini Saya sepakati secara sadar tanpa ada tekanan dari pihak manapun.</p>

                    <div class="current-date-display" id="currentDate3"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeStatement2" required>
                        <label class="form-check-label required" for="agreeStatement2">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreeStatement2-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5: Surat Pernyataan Tes Narkotika -->
        <div class="step-content conditional-section" id="step-5">
            <h2 class="section-title">SURAT PERNYATAAN ORANG TUA KESEDIAAN MENJALANI TES NARKOTIKA DAN OBAT TERLARANG
            </h2>

            <div class="parent-info-card">
                <h5 class="mb-3">Saya yang bertanda tangan dibawah ini sebagai :</h5>
                <div class="parent-info-row">
                    <div class="parent-label">Nama Lengkap:</div>
                    <div class="parent-value" id="parentFullName2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Email:</div>
                    <div class="parent-value" id="parentEmail2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Nomor HP:</div>
                    <div class="parent-value" id="parentPhone2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Tempat Lahir:</div>
                    <div class="parent-value" id="parentBirthPlace2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Tanggal Lahir:</div>
                    <div class="parent-value" id="parentBirthDate2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">No. KTP:</div>
                    <div class="parent-value" id="parentIdCard2">-</div>
                </div>
            </div>

            <div class="statement-item">
                <p>Menyatakan bahwa benar Saya adalah merupakan orang tua/wali yang sepenuhnya berwenang dari calon
                    siswa Mutiara Harapan Islamic School tersebut di bawah.</p>
            </div>

            <div class="student-info-card">
                <h5 class="mb-3">Informasi Calon Siswa</h5>
                <div class="student-info-row">
                    <div class="student-label">Nama Lengkap:</div>
                    <div class="student-value" id="studentFullName2">Muhammad Al-Fatih Sudirman</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Usia:</div>
                    <div class="student-value" id="studentAge2">7 tahun 2 bulan</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Level:</div>
                    <div class="student-value" id="studentLevel2">SD</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Kelas:</div>
                    <div class="student-value" id="studentGrade2">Kelas 1</div>
                </div>
            </div>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Dengan ini menyatakan memberi izin kepada sekolah atau pihak yang ditunjuk untuk mengadakan
                        tes/pemeriksaan atas penyalahgunaan narkotika, obat – obatan terlarang dan zat adiktif lainnya.
                    </p>
                    <p>Kami mengerti sepenuhnya atas dampak negatif zat – zat tersebut, dan bersedia menerima
                        konsekuensi atas hasil pemeriksaan sesuai dengan tata tertib yang berlaku.</p>

                    <div class="current-date-display" id="currentDate4"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeNarkotika" required>
                        <label class="form-check-label required" for="agreeNarkotika">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreeNarkotika-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6: Surat Pernyataan Siswa -->
        <div class="step-content conditional-section" id="step-6">
            <h2 class="section-title">SURAT PERNYATAAN SISWA</h2>

            <div class="student-info-card">
                <h5 class="mb-3">Informasi Siswa</h5>
                <div class="student-info-row">
                    <div class="student-label">Nama Lengkap:</div>
                    <div class="student-value" id="studentFullName3">Muhammad Al-Fatih Sudirman</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Usia:</div>
                    <div class="student-value" id="studentAge3">7 tahun 2 bulan</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Level:</div>
                    <div class="student-value" id="studentLevel3">SD</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Kelas:</div>
                    <div class="student-value" id="studentGrade3">Kelas 1</div>
                </div>
            </div>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Dengan ini menyatakan taat dan patuh pada peraturan tata tertib murid dan ketentuan lainnya yang
                        terkait dengan siswa Mutiara Harapan Islamic School yang berlaku di Mutiara Harapan Islamic
                        School.</p>
                    <p>Jika dikemudian hari saya melanggar peraturan tersebut, maka saya bersedia menerima
                        konsekuensinya.</p>

                    <div class="current-date-display" id="currentDate5"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeStudent" required>
                        <label class="form-check-label required" for="agreeStudent">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreeStudent-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>
            </div>

            <!-- Final Submit Section -->
            <div class="final-submit-section">
                <div class="alert alert-success">
                    <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Penyelesaian Formulir</h5>
                    <p class="mb-3">Selamat! Anda telah menyelesaikan semua bagian formulir persetujuan orang tua.
                        Silakan tinjau informasi Anda sebelum mengirimkan.</p>
                    <button type="button" class="btn btn-success btn-lg" id="final-submit-btn">
                        <i class="bi bi-send-check"></i> Kirim Formulir Persetujuan
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Current step tracking
            let currentStep = 1;
            const totalSteps = 6;

            // Student level for conditional steps
            const studentLevel = "SD"; // Change to "Upper Secondary" to see conditional steps

            // Sample data
            const sampleData = {
                father: {
                    name: "Ahmad Sudirman",
                    email: "ahmad.sudirman@email.com",
                    phone: "0812 3456 7890",
                    birthPlace: "Jakarta",
                    birthDate: "15 Januari 1980",
                    idCard: "1234567890123456"
                },
                mother: {
                    name: "Siti Fatimah",
                    email: "siti.fatimah@email.com",
                    phone: "0813 4567 8901",
                    birthPlace: "Bandung",
                    birthDate: "20 Maret 1982",
                    idCard: "2345678901234567"
                },
                guardian: {
                    name: "Budi Santoso",
                    email: "budi.santoso@email.com",
                    phone: "0814 5678 9012",
                    birthPlace: "Surabaya",
                    birthDate: "10 Mei 1975",
                    idCard: "3456789012345678"
                },
                student: {
                    fullName: "Muhammad Al-Fatih Sudirman",
                    age: "7 tahun 2 bulan",
                    level: studentLevel,
                    grade: "Kelas 1"
                }
            };

            // Format current dates
            function formatCurrentDate() {
                const now = new Date();
                const day = now.getDate();
                const month = now.toLocaleString('id-ID', {
                    month: 'long'
                });
                const year = now.getFullYear();
                return `${day} ${month} ${year}`;
            }

            // Set current dates
            $('#currentDate1').text(formatCurrentDate());
            $('#currentDate2').text(formatCurrentDate());
            $('#currentDate3').text(formatCurrentDate());
            $('#currentDate4').text(formatCurrentDate());
            $('#currentDate5').text(formatCurrentDate());

            // Set student info in all sections
            $('#studentFullName').text(sampleData.student.fullName);
            $('#studentAge').text(sampleData.student.age);
            $('#studentLevel').text(sampleData.student.level);
            $('#studentGrade').text(sampleData.student.grade);

            $('#studentFullName2').text(sampleData.student.fullName);
            $('#studentAge2').text(sampleData.student.age);
            $('#studentLevel2').text(sampleData.student.level);
            $('#studentGrade2').text(sampleData.student.grade);

            $('#studentFullName3').text(sampleData.student.fullName);
            $('#studentAge3').text(sampleData.student.age);
            $('#studentLevel3').text(sampleData.student.level);
            $('#studentGrade3').text(sampleData.student.grade);

            // Parent selector change handler
            $('#parentSelector').on('change', function() {
                const selectedParent = $(this).val();
                const parentInfoCard = $('#parentInfoCard');

                if (selectedParent) {
                    let parentData;
                    if (selectedParent === 'father') {
                        parentData = sampleData.father;
                    } else if (selectedParent === 'mother') {
                        parentData = sampleData.mother;
                    } else {
                        parentData = sampleData.guardian;
                    }

                    // Update parent info in step 1
                    $('#parentFullName').text(parentData.name);
                    $('#parentEmail').text(parentData.email);
                    $('#parentPhone').text(parentData.phone);
                    $('#parentBirthPlace').text(parentData.birthPlace);
                    $('#parentBirthDate').text(parentData.birthDate);
                    $('#parentIdCard').text(parentData.idCard);

                    // Update parent info in step 5
                    $('#parentFullName2').text(parentData.name);
                    $('#parentEmail2').text(parentData.email);
                    $('#parentPhone2').text(parentData.phone);
                    $('#parentBirthPlace2').text(parentData.birthPlace);
                    $('#parentBirthDate2').text(parentData.birthDate);
                    $('#parentIdCard2').text(parentData.idCard);

                    parentInfoCard.slideDown(300);
                } else {
                    parentInfoCard.slideUp(300);
                }

                validateCurrentStep();
            });

            // Format number to Indonesian currency
            function formatCurrency(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            // Convert number to Indonesian words
            function convertToTerbilang(number) {
                const ones = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
                const teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
                    'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'
                ];
                const tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh',
                    'tujuh puluh', 'delapan puluh', 'sembilan puluh'
                ];
                const thousands = ['', 'ribu', 'juta', 'miliar', 'triliun'];

                if (number === 0) return 'nol';

                let result = '';
                let groupIndex = 0;

                while (number > 0) {
                    const group = number % 1000;

                    if (group > 0) {
                        let groupWords = '';
                        const hundreds = Math.floor(group / 100);
                        const tensAndOnes = group % 100;

                        if (hundreds > 0) {
                            if (hundreds === 1) {
                                groupWords += 'seratus ';
                            } else {
                                groupWords += ones[hundreds] + ' ratus ';
                            }
                        }

                        if (tensAndOnes > 0) {
                            if (tensAndOnes < 10) {
                                groupWords += ones[tensAndOnes] + ' ';
                            } else if (tensAndOnes < 20) {
                                groupWords += teens[tensAndOnes - 10] + ' ';
                            } else {
                                const tensDigit = Math.floor(tensAndOnes / 10);
                                const onesDigit = tensAndOnes % 10;
                                groupWords += tens[tensDigit] + ' ';
                                if (onesDigit > 0) {
                                    groupWords += ones[onesDigit] + ' ';
                                }
                            }
                        }

                        if (groupIndex === 1 && group === 1) {
                            groupWords = 'seribu ';
                        } else {
                            groupWords += thousands[groupIndex] + ' ';
                        }

                        result = groupWords + result;
                    }

                    number = Math.floor(number / 1000);
                    groupIndex++;
                }

                return result.trim() + ' rupiah';
            }

            // Money input formatting
            function formatMoneyInput(input) {
                let value = input.val().replace(/[^0-9]/g, '');
                if (value) {
                    const numberValue = parseInt(value);
                    input.val(formatCurrency(numberValue));

                    // Update terbilang
                    const inputId = input.attr('id');
                    const terbilangId = inputId + 'Terbilang';
                    $('#' + terbilangId).text(convertToTerbilang(numberValue));
                } else {
                    const terbilangId = input.attr('id') + 'Terbilang';
                    $('#' + terbilangId).text('-');
                }
            }

            // Initialize money inputs
            $('#developmentFee, #annualFee, #schoolFee').on('input', function() {
                formatMoneyInput($(this));
                validateCurrentStep();
            });

            // Initialize with example values
            setTimeout(() => {
                $('#developmentFee').val('12,500,000').trigger('input');
                $('#annualFee').val('4,000,000').trigger('input');
                $('#schoolFee').val('1,750,000').trigger('input');
            }, 100);

            // Checkbox validation
            $('input[type="checkbox"][required]').on('change', function() {
                validateCurrentStep();
            });

            // Navigation handlers
            $('#next-btn').on('click', nextStep);
            $('#prev-btn').on('click', prevStep);
            $('#final-submit-btn').on('click', submitForm);

            // Form validation on input
            $('input, select, textarea').on('input change', function() {
                validateCurrentStep();
            });

            // Initialize step validation
            validateCurrentStep();

            // Handle conditional sections based on student level
            if (sampleData.student.level === "Upper Secondary") {
                $('#step-5, #step-6').removeClass('conditional-section').addClass('step-content');
                totalSteps = 6;
            } else {
                $('#step-5, #step-6').addClass('conditional-section');
                totalSteps = 4;

                // Update step indicator
                $('.step[data-step="5"], .step[data-step="6"]').hide();

                // Adjust step widths
                $('.step').css('flex', '0 0 25%');
            }

            // Function to validate current step
            function validateCurrentStep() {
                const currentStepElement = $(`#step-${currentStep}`);
                let isValid = true;

                // Reset error states
                currentStepElement.find('.validation-error').removeClass('validation-error');
                currentStepElement.find('.error-message').hide();

                // Check all required fields in current step
                currentStepElement.find('input[required], select[required], textarea[required]').each(function() {
                    const field = $(this);
                    const errorElement = $(`#${field.attr('id')}-error`);

                    // Validate based on field type
                    if (field.is('input[type="checkbox"]')) {
                        if (!field.is(':checked')) {
                            isValid = false;
                            field.addClass('validation-error');
                            errorElement.show();
                        }
                    } else if (field.is('select')) {
                        if (!field.val()) {
                            isValid = false;
                            field.addClass('validation-error');
                            errorElement.show();
                        }
                    } else if (field.is('input[type="text"]')) {
                        if (!field.val().trim()) {
                            isValid = false;
                            field.addClass('validation-error');
                            errorElement.show();
                        }
                    }
                });

                // Update Next button state
                if (currentStep === totalSteps) {
                    $('#next-btn').hide();
                    $('#prev-btn').show();
                } else {
                    $('#next-btn').show().prop('disabled', !isValid);
                    $('#prev-btn').show();
                }

                return isValid;
            }

            // Function to go to next step
            function nextStep() {
                if (!validateCurrentStep()) {
                    return;
                }

                // Mark current step as completed
                $(`.step[data-step="${currentStep}"]`).removeClass('active').addClass('completed');

                // Hide current step content
                $(`#step-${currentStep}`).removeClass('active');

                // Increment step
                currentStep++;

                // Update step indicator
                $(`.step[data-step="${currentStep}"]`).addClass('active');

                // Show next step content
                $(`#step-${currentStep}`).addClass('active');

                // Update navigation buttons
                $('#prev-btn').prop('disabled', false);

                // Scroll to top of step
                $('.main-container').animate({
                    scrollTop: 0
                }, 300);

                // Validate new step
                validateCurrentStep();
            }

            // Function to go to previous step
            function prevStep() {
                // Mark current step as not completed
                $(`.step[data-step="${currentStep}"]`).removeClass('active').removeClass('completed');

                // Hide current step content
                $(`#step-${currentStep}`).removeClass('active');

                // Decrement step
                currentStep--;

                // Update step indicator
                $(`.step[data-step="${currentStep}"]`).addClass('active').removeClass('completed');

                // Show previous step content
                $(`#step-${currentStep}`).addClass('active');

                // Update navigation buttons
                $('#prev-btn').prop('disabled', currentStep === 1);
                $('#next-btn').show().prop('disabled', false);

                // Scroll to top of step
                $('.main-container').animate({
                    scrollTop: 0
                }, 300);

                // Validate step
                validateCurrentStep();
            }

            // Function to submit form
            function submitForm() {
                if (!validateCurrentStep()) {
                    alert('Harap lengkapi semua bidang yang diperlukan sebelum mengirimkan.');
                    return;
                }

                // Collect form data
                const formData = {
                    parentSelected: $('#parentSelector').val(),
                    parentName: $('#parentFullName').text(),
                    developmentFee: $('#developmentFee').val(),
                    annualFee: $('#annualFee').val(),
                    schoolFee: $('#schoolFee').val(),
                    studentName: sampleData.student.fullName,
                    studentLevel: sampleData.student.level,
                    submissionDate: formatCurrentDate(),
                    agreements: {
                        payment: {
                            agree1: $('#agreePayment1').is(':checked'),
                            agree2: $('#agreePayment2').is(':checked'),
                            agree3: $('#agreePayment3').is(':checked'),
                            agree4: $('#agreePayment4').is(':checked'),
                            agree5: $('#agreePayment5').is(':checked'),
                            agree6: $('#agreePayment6').is(':checked'),
                            agree7: $('#agreePayment7').is(':checked'),
                            agree8: $('#agreePayment8').is(':checked'),
                            agree9: $('#agreePayment9').is(':checked'),
                            agree10: $('#agreePayment10').is(':checked'),
                            agree11: $('#agreePayment11').is(':checked'),
                            agree12: $('#agreePayment12').is(':checked')
                        },
                        statement: {
                            agree1: $('#agreeStatement1').is(':checked'),
                            agree2: $('#agreeStatement2').is(':checked')
                        },
                        narkotika: sampleData.student.level === "Upper Secondary" ? $('#agreeNarkotika').is(
                            ':checked') : null,
                        student: sampleData.student.level === "Upper Secondary" ? $('#agreeStudent').is(
                            ':checked') : null
                    }
                };

                console.log('Form submitted with data:', formData);

                // Simulate submission
                $('#final-submit-btn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...'
                );
                $('#final-submit-btn').prop('disabled', true);

                setTimeout(() => {
                    $('#final-submit-btn').html(
                        '<i class="bi bi-send-check"></i> Kirim Formulir Persetujuan');
                    $('#final-submit-btn').prop('disabled', false);

                    // Show success message
                    alert('Formulir persetujuan berhasil dikirim! Terima kasih.');

                    // In a real app, you would redirect or show a success page
                    // window.location.href = 'success.html';
                }, 2000);
            }
        });
    </script>
</body>

</html>
