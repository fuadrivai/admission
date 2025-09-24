<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Observasi Sekolah</title>
    <!-- Bootstrap 5 CSS -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/jquery.timepicker.css">
    <link rel="stylesheet" href="/assets/extensions/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
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

        .header-section {
            background-color: var(--fandango-bg);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 15px;
        }

        .logo-title-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
        }

        .card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12);
            margin-bottom: 2.5rem;
            overflow: hidden;
        }

        .form-control,
        .form-select {
            padding: 0.875rem 1.25rem;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--fandango-light);
            box-shadow: 0 0 0 0.35rem rgba(181, 51, 137, 0.2);
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

        .time-badge {
            cursor: pointer;
            padding: 12px 24px;
            margin: 6px;
            border-radius: 12px;
            background-color: #f8f9fa;
            color: #495057;
            transition: all 0.3s ease;
            display: inline-block;
            font-weight: 500;
            border: 1.5px solid #e2e8f0;
        }

        .time-badge:hover {
            background-color: var(--fandango-light);
            color: white;
            transform: translateY(-2px);
            border-color: var(--fandango-light);
        }

        .time-badge.selected {
            background-color: var(--fandango);
            color: white;
            border-color: var(--fandango);
            box-shadow: 0 4px 8px rgba(181, 51, 137, 0.25);
        }

        .time-badge.disabled {
            background-color: #e9ecef;
            color: #adb5bd;
            border-color: #dee2e6;
            cursor: not-allowed;
            pointer-events: none;
            box-shadow: none;
            transform: none;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            border: 3px solid var(--fandango);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .required-label::after {
            content: " *";
            color: var(--fandango);
        }

        .page-title {
            color: var(--fandango);
            font-weight: 800;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin: 0;
            letter-spacing: 0.5px;
        }

        .form-section {
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .form-check-input:checked {
            background-color: var(--fandango);
            border-color: var(--fandango);
        }

        .form-check-input:focus {
            border-color: var(--fandango-light);
            box-shadow: 0 0 0 0.25rem rgba(181, 51, 137, 0.25);
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 1.75rem;
            }

            .header-section {
                padding: 1.25rem;
            }

            .logo-placeholder {
                width: 70px;
                height: 70px;
                margin-right: 1rem;
            }

            .logo-title-container {
                flex-direction: column;
                text-align: center;
            }

            .logo-placeholder {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <!-- Header dengan logo dan judul -->
                <div class="header-section">
                    <div class="logo-title-container">
                        <!-- Logo Sekolah -->
                        <div class="logo-placeholder">
                            {{-- <i class="fas fa-school" style="font-size: 2rem; color: var(--fandango);"></i> --}}
                            <img src="/assets/images/logo.png" width="100%" height="100%" class="img"
                                alt="">
                        </div>

                        <!-- Judul Form -->
                        <div>
                            <h1 class="page-title">Form Observasi</h1>
                            <p class="text-muted mb-0">Isi form berikut untuk mengajukan permohonan observasi sekolah
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Observasi -->
                <div class="card">
                    <div class="form-section">
                        <form id="observationForm" autocomplete="off" class="needs-validation" novalidate>
                            @csrf
                            <h4 class="mb-4" style="color: var(--fandango);">Data Anak</h4>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="childName" class="form-label required-label">Nama Anak</label>
                                    <input type="text" class="form-control" id="childName" required
                                        placeholder="Masukkan nama lengkap anak" name="child_name">
                                    <div class="invalid-feedback">
                                        Harap masukkan nama anak.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label required-label">Jenis Kelamin</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="male"
                                                value="Male" required>
                                            <label class="form-check-label" for="male">Laki-laki</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="female"
                                                value="Female" required>
                                            <label class="form-check-label" for="female">Perempuan</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        Harap pilih jenis kelamin.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <label for="level" class="form-label required-label">Level</label>
                                    <select name="level_id" class="form-select" id="level" required>
                                        <option value="" selected disabled>Pilih Level Pendidikan</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Harap pilih level pendidikan.
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="grade" class="form-label required-label">Grade</label>
                                    <select disabled name="grade_id" class="form-select" id="grade" required>
                                        <option value="" selected disabled>Pilih Grade</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Harap pilih grade.
                                    </div>
                                </div>
                            </div>

                            <h4 class="mb-4 mt-5" style="color: var(--fandango);">Data Orang Tua</h4>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="parentName" class="form-label required-label">Nama Orang Tua</label>
                                    <input type="text" class="form-control" name="parent_name" id="parentName"
                                        required placeholder="Masukkan nama lengkap orang tua">
                                    <div class="invalid-feedback">
                                        Harap masukkan nama orang tua.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="relationship" class="form-label required-label">Hubungan Orang
                                        Tua</label>
                                    <select name="relationship" class="form-select" id="relationship" required>
                                        <option value="" selected disabled>Pilih Hubungan</option>
                                        <option value="Ayah">Ayah</option>
                                        <option value="Ibu">Ibu</option>
                                        <option value="Wali">Wali</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Harap pilih hubungan dengan anak.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="phone" class="form-label required-label">No. Telepon Orang
                                        Tua</label>
                                    <input type="tel" name="phone" class="form-control" id="phone"
                                        required placeholder="Contoh: 081234567890">
                                    <div class="invalid-feedback">
                                        Harap masukkan nomor telepon yang valid.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label required-label">Email Orang Tua</label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        required placeholder="Contoh: nama@email.com">
                                    <div class="invalid-feedback">
                                        Harap masukkan alamat email yang valid.
                                    </div>
                                </div>
                            </div>

                            <h4 class="mb-4 mt-5" style="color: var(--fandango);">Jadwal Observasi</h4>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="date" class="form-label required-label">Tanggal Tersedia</label>
                                    <input type="text"name="date" class="form-control date-picker" required
                                        id="date">
                                    <div class="invalid-feedback">
                                        Harap pilih tanggal observasi.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label required-label">Jam Tersedia</label>
                                    <div id="list-time">

                                    </div>
                                    <input type="hidden" name="time" id="selectedTime" required>
                                    <div class="invalid-feedback">
                                        Harap pilih jam observasi.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" id="btn-submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Form Observasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center text-muted">
                    <p>Form ini akan diproses dalam 1x24 jam. Hubungi kami di <a href="tel:+6281291823247"
                            class="text-decoration-none" style="color: var(--fandango);">+62 812 9182 3247</a> untuk
                        pertanyaan lebih lanjut.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js"></script>
    <script src="/assets/static/js/pages/observation.js"></script>

</body>

</html>
