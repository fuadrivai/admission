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
    <link rel="stylesheet" href="/assets/static/css/student-enrolment.css?v=1.0.0">

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
                        <h1 class="h4 mb-1">Formulir Data Diri</h1>
                        <p class="mb-0">Mutiara Harapan Islamic School</p>
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
                <div class="step-title">Data Awal</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">Data Siswa</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">Data Ayah</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">Data Ibu</div>
            </div>
            <div class="step" data-step="5">
                <div class="step-number">5</div>
                <div class="step-title">Data Wali</div>
            </div>
            <div class="step" data-step="6">
                <div class="step-number">6</div>
                <div class="step-title">Kesehatan</div>
            </div>
        </div>

        <!-- Step 1: Introduction & Enrollment Code -->
        <div class="step-content active" id="step-1">
            <h2 class="section-title">Formulir Data Diri - Mutiara Harapan Islamic School</h2>

            <div class="arabic-text">
                ٱلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ ٱللَّٰهِ وَبَرَكَاتُهُ
            </div>

            <p class="mb-3"><strong>Assalamualaikum Warahmatullahi Wabarakatuh</strong></p>

            <p class="mb-3" style="text-align: justify">Ayah/Bunda, untuk mempermudah proses pendaftaran, kami memohon
                kesediaan Anda untuk
                melengkapi formulir data pribadi siswa ini dengan benar dan lengkap. Data yang dikirimkan akan digunakan
                sebagai dokumen pendaftaran resmi.</p>

            <div class="info-box">
                <h5 class="mb-2">Persyaratan Pendaftaran:</h5>
                <ol class="mb-0">
                    <li>Paket formulir pendaftaran siswa baru</li>
                    <li>Surat persetujuan orang tua</li>
                    <li>Pengumpulan dokumen</li>
                </ol>
            </div>

            <p class="mb-4">Untuk bantuan, hubungi Admission via WhatsApp: <strong><a style="text-decoration:unset"
                        href="https://wa.me/6281291823247" target="_blank">
                        0812 9182 3247
                    </a></strong>
            </p>

            <p class="mb-4">Salam hangat,<br>
                Mutiara Harapan Islamic School</p>

            <p class="mb-4"><em>#HomeOfChampions #HomeOfIslamicLeaders</em></p>

            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kode Kunjungan Sekolah / Kode Pendaftaran</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="enrollmentCode" class="form-label required">Kode Kunjungan Sekolah / Kode
                            Pendaftaran</label>
                        <input type="text" class="form-control" id="enrollmentCode"
                            placeholder="Masukkan kode pendaftaran Anda" required>
                        <input type="text" class="form-control" id="admissionID" hidden>
                        <input type="text" class="form-control" id="applicantId" hidden>
                        <div class="error-message" id="enrollmentCode-error">Harap masukkan kode pendaftaran yang valid
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Student Data -->
        <div class="step-content" id="step-2">
            <h2 class="section-title">Data Siswa</h2>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="studentFullName" class="form-label required">Nama Lengkap Ananda</label>
                    <input type="text" class="form-control" id="studentFullName" required>
                    <div class="error-message" id="studentFullName-error">Harap masukkan nama lengkap ananda</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="studentNickname" class="form-label required">Nama Panggilan Ananda</label>
                    <input type="text" class="form-control" id="studentNickname" required>
                    <div class="error-message" id="studentNickname-error">Harap masukkan nama panggilan ananda</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Jenis Kelamin</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="studentGender"
                                id="studentGenderFemale" value="female" required>
                            <label class="form-check-label" for="studentGenderFemale">Perempuan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="studentGender"
                                id="studentGenderMale" value="male">
                            <label class="form-check-label" for="studentGenderMale">Laki-laki</label>
                        </div>
                    </div>
                    <div class="error-message" id="studentGender-error">Harap pilih jenis kelamin</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="placeOfBirth" class="form-label required">Tempat Lahir</label>
                    <input type="text" class="form-control" id="placeOfBirth" required>
                    <div class="error-message" id="placeOfBirth-error">Harap masukkan tempat lahir</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="dateOfBirth" class="form-label required">Tanggal Lahir</label>
                    <input type="text" class="form-control date-picker" readonly id="dateOfBirth" required>
                    <div class="error-message" id="dateOfBirth-error">Harap masukkan tanggal lahir</div>
                </div>

                {{-- <div class="col-12 mb-3">
                    <label class="form-label">Usia Saat Pendaftaran</label>
                    <div class="form-control" id="ageCalculation">Masukkan tanggal lahir untuk menghitung usia</div>
                </div> --}}



                <div class="col-md-4 mb-3">
                    <label for="religion" class="form-label required">Agama</label>
                    <select class="form-select religion" id="religion" required>
                        <option value="" selected disabled>Pilih agama</option>
                    </select>
                    <div class="error-message" id="religion-error">Harap pilih agama</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="ethnicity" class="form-label required">Suku Bangsa</label>
                    <input type="text" class="form-control" id="ethnicity" required>
                    <div class="error-message" id="ethnicity-error">Harap masukkan suku bangsa</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Kewarganegaraan</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="citizenship" id="citizenshipWNI"
                                value="WNI" required>
                            <label class="form-check-label" for="citizenshipWNI">WNI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="citizenship" id="citizenshipWNA"
                                value="WNA">
                            <label class="form-check-label" for="citizenshipWNA">WNA</label>
                        </div>
                    </div>
                    <div class="error-message" id="citizenship-error">Harap pilih kewarganegaraan</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="height" class="form-label required">Tinggi Badan (cm)</label>
                    <input type="number" class="form-control" id="height" step="0.1" min="0"
                        value="0" required>
                    <div class="error-message" id="height-error">Harap masukkan tinggi badan dalam cm</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="weight" class="form-label required">Berat Badan (kg)</label>
                    <input type="number" class="form-control" id="weight" step="0.1" min="0"
                        value="0" required>
                    <div class="error-message" id="weight-error">Harap masukkan berat badan dalam kg</div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="siblingsCount" class="form-label required">Jumlah Saudara</label>
                    <small><i>kandung/tiri/angkat</i></small>
                    <input type="number" class="form-control" id="siblingsCount" min="0" required>
                    <div class="error-message" id="siblingsCount-error">Harap masukkan jumlah saudara</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="birthOrder" class="form-label required">Anak Ke-</label>
                    <input type="number" class="form-control" id="birthOrder" min="1" required>
                    <div class="error-message" id="birthOrder-error">Harap masukkan urutan anak</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="homeLanguage" class="form-label required">Bahasa pengantar di rumah</label>
                    <input type="text" class="form-control" id="homeLanguage" required>
                    <div class="error-message" id="homeLanguage-error">Harap masukkan bahasa pengantar di rumah
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="otherLanguages" class="form-label required">Bahasa lain yang digunakan</label>
                    <input type="text" class="form-control" id="otherLanguages" required>
                    <div class="error-message" id="otherLanguages-error">Harap masukkan bahasa lain yang digunakan
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="fullAddress" class="form-label required">Alamat Lengkap</label>
                    <textarea class="form-control" id="fullAddress" rows="3" required></textarea>
                    <div class="error-message" id="fullAddress-error">Harap masukkan alamat lengkap</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="postalCode" class="form-label required">Kode Pos</label>
                    <input type="number" class="form-control" id="postalCode" required>
                    <div class="error-message" id="postalCode-error">Harap masukkan kode pos</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="homePhone" class="form-label">Telepon Rumah (Opsional)</label>
                    <input type="tel" class="form-control" id="homePhone">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="parentPhone" class="form-label required">No. handphone orangtua</label>
                    <input type="tel" class="form-control" id="parentPhone" required>
                    <div class="error-message" id="parentPhone-error">Harap masukkan nomor handphone orangtua</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="livingWith" class="form-label required">Tinggal Bersama</label>
                    <select class="form-select" id="livingWith" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="parents">Orang Tua</option>
                        <option value="family">Keluarga</option>
                        <option value="others">Lainnya</option>
                    </select>
                    <div class="error-message" id="livingWith-error">Harap pilih dengan siapa siswa tinggal</div>

                    <div class="col-md-12 mb-3 conditional-field" id="livingWithOthersField">
                        <label for="livingWithOthers" class="form-label required">Sebutkan</label>
                        <input type="text" class="form-control" id="livingWithOthers">
                        <div class="error-message" id="livingWithOthers-error">Harap sebutkan dengan siapa siswa
                            tinggal
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="distanceToSchool" class="form-label required">Jarak ke Sekolah (KM)</label>
                    <input type="number" class="form-control" id="distanceToSchool" step="0.1" min="0"
                        required>
                    <div class="error-message" id="distanceToSchool-error">Harap masukkan jarak ke sekolah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="previousSchool" class="form-label required">Asal sekolah</label>
                    <input type="text" class="form-control" id="previousSchool" required>
                    <div class="error-message" id="previousSchool-error">Harap masukkan sekolah sebelumnya</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="previousSchoolAddress" class="form-label required">Alamat asal sekolah</label>
                    <input type="text" class="form-control" id="previousSchoolAddress" required>
                    <div class="error-message" id="previousSchoolAddress-error">Harap masukkan alamat sekolah
                        sebelumnya</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="graduationYear" class="form-label required">Tahun kelulusan/meninggalkan sekolah
                        asal</label>
                    <select class="form-select" id="graduationYear" required>
                        <option value="" selected disabled>Pilih tahun</option>
                    </select>
                    <div class="error-message" id="graduationYear-error">Harap pilih tahun kelulusan</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="applyingLevel" class="form-label required">Mendaftar untuk level:</label>
                    <select class="form-select" id="applyingLevel" required>
                    </select>
                    <div class="error-message" id="applyingLevel-error">Harap pilih tingkat pendaftaran</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="applyingClass" class="form-label required">Kelas</label>
                    <select class="form-select" id="applyingClass" required>
                        <option value="" selected disabled>Pilih kelas</option>
                    </select>
                    <div class="error-message" id="applyingClass-error">Harap pilih kelas</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="academic-year" class="form-label required">Tahun ajaran</label>
                    <select class="form-select" id="academic-year" required>
                        <option value="" selected disabled>Pilih tahun akademik</option>
                    </select>
                    <div class="error-message" id="academic-year-error">Harap pilih tahun akademik</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="emergencyContactPriority" class="form-label required">Prioritas Kontak Darurat</label>
                    <select class="form-select" id="emergencyContactPriority" required>
                        <option value="" selected disabled>Pilih prioritas</option>
                        <option value="Ayah">Ayah</option>
                        <option value="Ibu">Ibu</option>
                        <option value="Wali">Wali</option>
                    </select>
                    <div class="error-message" id="emergencyContactPriority-error">Harap pilih prioritas kontak
                        darurat</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="notAttendingSchool" class="form-label required">Apakah calon siswa pernah tidak
                        bersekolah?</label>
                    <select class="form-select" id="notAttendingSchool" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="notAttendingSchool-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="notAttendingSchoolYesField">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="notAttendingDuration" class="form-label required">Berapa Lama</label>
                                <input type="text" class="form-control" id="notAttendingDuration">
                                <div class="error-message" id="notAttendingDuration-error">Harap masukkan durasi</div>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="notAttendingReason" class="form-label required">Alasan</label>
                                <input type="text" class="form-control" id="notAttendingReason">
                                <div class="error-message" id="notAttendingReason-error">Harap masukkan alasan</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="developmentalAssessment" class="form-label required">Apakah calon siswa pernah
                        melakukan pemeriksaan tumbuh kembang atau psikologis?</label>
                    <select class="form-select" id="developmentalAssessment" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="developmentalAssessment-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="developmentalAssessmentYesField">
                        <div class="mb-3">
                            <label for="developmentalDiagnosis" class="form-label required">Deskripsi
                                Diagnosis</label>
                            <textarea class="form-control" id="developmentalDiagnosis" rows="2"></textarea>
                            <div class="error-message" id="developmentalDiagnosis-error">Harap masukkan deskripsi
                                diagnosis</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="therapyHistory" class="form-label required">Apakah calon siswa pernah menjalani terapi
                        khusus sehubungan dengan hambatan fisik atau tingkah laku?</label>
                    <select class="form-select" id="therapyHistory" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="therapyHistory-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="therapyHistoryYesField">
                        <div class="mb-3">
                            <label for="therapyType" class="form-label required">Jenis & Waktu Terapi</label>
                            <textarea class="form-control" id="therapyType" rows="2"></textarea>
                            <div class="error-message" id="therapyType-error">Harap masukkan jenis dan waktu terapi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Father Data -->
        <div class="step-content" id="step-3">
            <h2 class="section-title">Data Ayah</h2>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fatherFullName" class="form-label required">Nama Lengkap</label>
                    <input type="hidden" class="form-control" id="fatherId">
                    <input type="text" class="form-control" id="fatherFullName" required>
                    <div class="error-message" id="fatherFullName-error">Harap masukkan nama lengkap ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherMobile" class="form-label required">Telepon Seluler</label>
                    <input type="tel" class="form-control" id="fatherMobile" required>
                    <div class="error-message" id="fatherMobile-error">Harap masukkan telepon seluler ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherHomePhone" class="form-label">Telepon Rumah (Opsional)</label>
                    <input type="tel" class="form-control" id="fatherHomePhone">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherEmail" class="form-label required">Email</label>
                    <input type="email" class="form-control" id="fatherEmail" required>
                    <div class="error-message" id="fatherEmail-error">Harap masukkan alamat email yang valid</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherPlaceOfBirth" class="form-label required">Tempat Lahir</label>
                    <input type="text" class="form-control" id="fatherPlaceOfBirth" required>
                    <div class="error-message" id="fatherPlaceOfBirth-error">Harap masukkan tempat lahir ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherDateOfBirth" class="form-label required">Tanggal Lahir</label>
                    <input type="text" class="form-control date-picker" readonly id="fatherDateOfBirth" required>
                    <div class="error-message" id="fatherDateOfBirth-error">Harap masukkan tanggal lahir ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherIdCard" class="form-label required">No. KTP / Paspor</label>
                    <input type="text" class="form-control" id="fatherIdCard" required>
                    <div class="error-message" id="fatherIdCard-error">Harap masukkan nomor KTP atau paspor ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherReligion" class="form-label required">Agama</label>
                    <select class="form-select religion" id="fatherReligion" required>
                        <option value="" selected disabled>Pilih agama</option>
                    </select>
                    <div class="error-message" id="fatherReligion-error">Harap pilih agama ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherEthnicity" class="form-label required">Suku Bangsa</label>
                    <input type="text" class="form-control" id="fatherEthnicity" required>
                    <div class="error-message" id="fatherEthnicity-error">Harap masukkan suku bangsa ayah</div>
                </div>

                <div class="col-md-8 mb-3">
                    <label for="fatherAddress" class="form-label required">Alamat Lengkap</label>
                    <textarea class="form-control" id="fatherAddress" rows="3" required></textarea>
                    <div class="error-message" id="fatherAddress-error">Harap masukkan alamat lengkap ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherPostalCode" class="form-label required">Kode Pos</label>
                    <input type="number" class="form-control" id="fatherPostalCode" required>
                    <div class="error-message" id="fatherPostalCode-error">Harap masukkan kode pos ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherEducation" class="form-label required">Pendidikan</label>
                    <select class="form-select education" id="fatherEducation" required>
                        <option value="" selected disabled>Pilih tingkat pendidikan</option>
                    </select>
                    <div class="error-message" id="fatherEducation-error">Harap pilih tingkat pendidikan ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherOccupation" class="form-label required">Pekerjaan</label>
                    <select class="form-select job" id="fatherOccupation" required>
                        <option value="" selected disabled>Pilih pekerjaan</option>
                    </select>
                    <div class="error-message" id="fatherOccupation-error">Harap pilih pekerjaan ayah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherInstitution" class="form-label required">Nama Instansi</label>
                    <input type="text" class="form-control" id="fatherInstitution" required>
                    <div class="error-message" id="fatherInstitution-error">Harap masukkan nama instansi ayah</div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="fatherInstitutionAddress" class="form-label required">Alamat Instansi</label>
                    <textarea class="form-control" id="fatherInstitutionAddress" rows="2" required></textarea>
                    <div class="error-message" id="fatherInstitutionAddress-error">Harap masukkan alamat instansi ayah
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="fatherIncome" class="form-label required">Kisaran Penghasilan Bulanan (IDR)</label>
                    <input type="text" class="form-control number2" id="fatherIncome" required>
                    <div class="error-message" id="fatherIncome-error">Harap masukkan penghasilan bulanan ayah</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required">Status Pernikahan</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="fatherMaritalStatus"
                                id="fatherMarried" value="Menikah" required>
                            <label class="form-check-label" for="fatherMarried">Menikah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="fatherMaritalStatus"
                                id="fatherDivorced" value="Cerai">
                            <label class="form-check-label" for="fatherDivorced">Cerai</label>
                        </div>
                    </div>
                    <div class="error-message" id="fatherMaritalStatus-error">Harap pilih status pernikahan ayah</div>
                </div>
            </div>
        </div>

        <!-- Step 4: Mother Data -->
        <div class="step-content" id="step-4">
            <h2 class="section-title">Data Ibu</h2>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="motherFullName" class="form-label required">Nama Lengkap</label>
                    <input type="hidden" class="form-control" id="motherId">
                    <input type="text" class="form-control" id="motherFullName" required>
                    <div class="error-message" id="motherFullName-error">Harap masukkan nama lengkap ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherMobile" class="form-label required">Telepon Seluler</label>
                    <input type="tel" class="form-control" id="motherMobile" required>
                    <div class="error-message" id="motherMobile-error">Harap masukkan telepon seluler ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherHomePhone" class="form-label">Telepon Rumah (Opsional)</label>
                    <input type="tel" class="form-control" id="motherHomePhone">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherEmail" class="form-label required">Email</label>
                    <input type="email" class="form-control" id="motherEmail" required>
                    <div class="error-message" id="motherEmail-error">Harap masukkan alamat email yang valid</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherPlaceOfBirth" class="form-label required">Tempat Lahir</label>
                    <input type="text" class="form-control" id="motherPlaceOfBirth" required>
                    <div class="error-message" id="motherPlaceOfBirth-error">Harap masukkan tempat lahir ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherDateOfBirth" class="form-label required">Tanggal Lahir</label>
                    <input type="text" class="form-control date-picker" readonly id="motherDateOfBirth" required>
                    <div class="error-message" id="motherDateOfBirth-error">Harap masukkan tanggal lahir ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherIdCard" class="form-label required">No. KTP / Paspor</label>
                    <input type="text" class="form-control" id="motherIdCard" required>
                    <div class="error-message" id="motherIdCard-error">Harap masukkan nomor KTP atau paspor ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherReligion" class="form-label required">Agama</label>
                    <select class="form-select religion" id="motherReligion" required>
                        <option value="" selected disabled>Pilih agama</option>
                    </select>
                    <div class="error-message" id="motherReligion-error">Harap pilih agama ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherEthnicity" class="form-label required">Suku Bangsa</label>
                    <input type="text" class="form-control" id="motherEthnicity" required>
                    <div class="error-message" id="motherEthnicity-error">Harap masukkan suku bangsa ibu</div>
                </div>

                <div class="col-md-8 mb-3">
                    <label for="motherAddress" class="form-label required">Alamat Lengkap</label>
                    <textarea class="form-control" id="motherAddress" rows="3" required></textarea>
                    <div class="error-message" id="motherAddress-error">Harap masukkan alamat lengkap ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherPostalCode" class="form-label required">Kode Pos</label>
                    <input type="number" class="form-control" id="motherPostalCode" required>
                    <div class="error-message" id="motherPostalCode-error">Harap masukkan kode pos ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherEducation" class="form-label required">Pendidikan</label>
                    <select class="form-select education" id="motherEducation" required>
                        <option value="" selected disabled>Pilih tingkat pendidikan</option>
                    </select>
                    <div class="error-message" id="motherEducation-error">Harap pilih tingkat pendidikan ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherOccupation" class="form-label required">Pekerjaan</label>
                    <select class="form-select job" id="motherOccupation" required>
                        <option value="" selected disabled>Pilih pekerjaan</option>
                    </select>
                    <div class="error-message" id="motherOccupation-error">Harap pilih pekerjaan ibu</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherInstitution" class="form-label required">Nama Instansi</label>
                    <input type="text" class="form-control" id="motherInstitution" required>
                    <div class="error-message" id="motherInstitution-error">Harap masukkan nama instansi ibu</div>
                </div>

                <div class="col-12 mb-3">
                    <label for="motherInstitutionAddress" class="form-label required">Alamat Instansi</label>
                    <textarea class="form-control" id="motherInstitutionAddress" rows="2" required></textarea>
                    <div class="error-message" id="motherInstitutionAddress-error">Harap masukkan alamat instansi ibu
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="motherIncome" class="form-label required">Kisaran Penghasilan Bulanan (IDR)</label>
                    <input type="text" class="form-control number2" id="motherIncome" required>
                    <div class="error-message" id="motherIncome-error">Harap masukkan penghasilan bulanan ibu</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required">Status Pernikahan</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="motherMaritalStatus"
                                id="motherMarried" value="Menikah" required>
                            <label class="form-check-label" for="motherMarried">Menikah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="motherMaritalStatus"
                                id="motherDivorced" value="Cerai">
                            <label class="form-check-label" for="motherDivorced">Cerai</label>
                        </div>
                    </div>
                    <div class="error-message" id="motherMaritalStatus-error">Harap pilih status pernikahan ibu</div>
                </div>
            </div>
        </div>

        <!-- Step 5: Guardian Data (Optional) -->
        <div class="step-content" id="step-5">
            <h2 class="section-title">Data Wali (Opsional)</h2>
            <p class="text-muted mb-4">Isi hanya jika ada wali selain orang tua kandung</p>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="guardianFullName" class="form-label">Nama Lengkap</label>
                    <input type="hidden" class="form-control" id="guardianId">
                    <input type="text" class="form-control" id="guardianFullName">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianMobile" class="form-label">Telepon Seluler</label>
                    <input type="tel" class="form-control" id="guardianMobile">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianHomePhone" class="form-label">Telepon Rumah</label>
                    <input type="tel" class="form-control" id="guardianHomePhone">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="guardianEmail">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianPlaceOfBirth" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control" id="guardianPlaceOfBirth">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianDateOfBirth" class="form-label">Tanggal Lahir</label>
                    <input type="text" class="form-control date-picker" readonly id="guardianDateOfBirth">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianIdCard" class="form-label">No. KTP / Paspor</label>
                    <input type="text" class="form-control" id="guardianIdCard">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianReligion" class="form-label">Agama</label>
                    <select class="form-select religion" id="guardianReligion">
                        <option value="" selected>Pilih agama (opsional)</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianEthnicity" class="form-label">Suku Bangsa</label>
                    <input type="text" class="form-control" id="guardianEthnicity">
                </div>

                <div class="col-md-8 mb-3">
                    <label for="guardianAddress" class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" id="guardianAddress" rows="3"></textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianPostalCode" class="form-label">Kode Pos</label>
                    <input type="number" class="form-control" id="guardianPostalCode">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianEducation" class="form-label">Pendidikan</label>
                    <select class="form-select education" id="guardianEducation">
                        <option value="" selected>Pilih tingkat pendidikan (opsional)</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianOccupation" class="form-label">Pekerjaan</label>
                    <select class="form-select job" id="guardianOccupation">
                        <option value="" selected>Pilih pekerjaan (opsional)</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianInstitution" class="form-label">Nama Instansi</label>
                    <input type="text" class="form-control" id="guardianInstitution">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="guardianInstitutionAddress" class="form-label">Alamat Instansi</label>
                    <textarea class="form-control" id="guardianInstitutionAddress" rows="2"></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="guardianIncome" class="form-label">Kisaran Penghasilan Bulanan (IDR)</label>
                    <input type="number" class="form-control" id="guardianIncome" step="100000" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Pernikahan</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="guardianMaritalStatus"
                                id="guardianMarried" value="Menikah">
                            <label class="form-check-label" for="guardianMarried">Menikah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="guardianMaritalStatus"
                                id="guardianDivorced" value="Cerai">
                            <label class="form-check-label" for="guardianDivorced">Cerai</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6: Student Health Data & Declaration -->
        <div class="step-content" id="step-6">
            <h2 class="section-title">Data Kesehatan Siswa & Deklarasi Orang Tua</h2>

            <!-- Health Data Section -->
            <h5 class="subsection-title">IMUNISASI DASAR</h5>
            <div class="mb-4">
                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">BCG</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationBCG"
                                    id="immunizationBCGYes" value="true" required>
                                <label class="form-check-label" for="immunizationBCGYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationBCG"
                                    id="immunizationBCGNo" value="false">
                                <label class="form-check-label" for="immunizationBCGNo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationBCG-error">Harap pilih opsi</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Hepatitis</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitis"
                                    id="immunizationHepatitisYes" value="true" required>
                                <label class="form-check-label" for="immunizationHepatitisYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitis"
                                    id="immunizationHepatitisNo" value="false">
                                <label class="form-check-label" for="immunizationHepatitisNo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationHepatitis-error">Harap pilih opsi</div>
                    </div>
                </div>

                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Difteri/Tetanus/Pertusis</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationDTP"
                                    id="immunizationDTPYes" value="true" required>
                                <label class="form-check-label" for="immunizationDTPYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationDTP"
                                    id="immunizationDTPNo" value="false">
                                <label class="form-check-label" for="immunizationDTPNo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationDTP-error">Harap pilih opsi</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Polio</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationPolio"
                                    id="immunizationPolioYes" value="true" required>
                                <label class="form-check-label" for="immunizationPolioYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationPolio"
                                    id="immunizationPolioNo" value="false">
                                <label class="form-check-label" for="immunizationPolioNo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationPolio-error">Harap pilih opsi</div>
                    </div>
                </div>

                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Campak</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMeasles"
                                    id="immunizationMeaslesYes" value="true" required>
                                <label class="form-check-label" for="immunizationMeaslesYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMeasles"
                                    id="immunizationMeaslesNo" value="false">
                                <label class="form-check-label" for="immunizationMeaslesNo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationMeasles-error">Harap pilih opsi</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Hepatitis A</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitisA"
                                    id="immunizationHepatitisAYes" value="true" required>
                                <label class="form-check-label" for="immunizationHepatitisAYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitisA"
                                    id="immunizationHepatitisANo" value="false">
                                <label class="form-check-label" for="immunizationHepatitisANo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationHepatitisA-error">Harap pilih opsi</div>
                    </div>
                </div>

                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">MMR</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMMR"
                                    id="immunizationMMRYes" value="true" required>
                                <label class="form-check-label" for="immunizationMMRYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMMR"
                                    id="immunizationMMRNo" value="false">
                                <label class="form-check-label" for="immunizationMMRNo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationMMR-error">Harap pilih opsi</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Influenza</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationInfluenza"
                                    id="immunizationInfluenzaYes" value="true" required>
                                <label class="form-check-label" for="immunizationInfluenzaYes">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationInfluenza"
                                    id="immunizationInfluenzaNo" value="false">
                                <label class="form-check-label" for="immunizationInfluenzaNo">Tidak</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationInfluenza-error">Harap pilih opsi</div>
                    </div>
                </div>
            </div>

            <h5 class="subsection-title">CATATAN KESEHATAN</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="foodAllergy" class="form-label required">Alergi Makanan</label>
                    <select class="form-select" id="foodAllergy" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="foodAllergy-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="foodAllergyYesField">
                        <div class="mb-3">
                            <label for="foodAllergyExplanation" class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="foodAllergyExplanation" rows="2"></textarea>
                            <div class="error-message" id="foodAllergyExplanation-error">Harap jelaskan alergi
                                makanan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="drugAllergy" class="form-label required">Alergi Obat</label>
                    <select class="form-select" id="drugAllergy" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="drugAllergy-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="drugAllergyYesField">
                        <div class="mb-3">
                            <label for="drugAllergyExplanation" class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="drugAllergyExplanation" rows="2"></textarea>
                            <div class="error-message" id="drugAllergyExplanation-error">Harap jelaskan alergi obat
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="bloodType" class="form-label required">Golongan Darah</label>
                    <select class="form-select" id="bloodType" required>
                        <option value="" selected disabled>Pilih golongan darah</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                        <option value="Unknown">Tidak Tahu</option>
                    </select>
                    <div class="error-message" id="bloodType-error">Harap pilih golongan darah</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Menggunakan Kacamata</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesGlasses"
                                id="usesGlassesYes" value="true" required>
                            <label class="form-check-label" for="usesGlassesYes">Ya</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesGlasses" id="usesGlassesNo"
                                value="false">
                            <label class="form-check-label" for="usesGlassesNo">Tidak</label>
                        </div>
                    </div>
                    <div class="error-message" id="usesGlasses-error">Harap pilih opsi</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Menggunakan Alat Bantu Dengar</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesHearingAid"
                                id="usesHearingAidYes" value="true" required>
                            <label class="form-check-label" for="usesHearingAidYes">Ya</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesHearingAid"
                                id="usesHearingAidNo" value="false">
                            <label class="form-check-label" for="usesHearingAidNo">Tidak</label>
                        </div>
                    </div>
                    <div class="error-message" id="usesHearingAid-error">Harap pilih opsi</div>
                </div>
            </div>

            <h5 class="subsection-title">RIWAYAT KEHAMILAN (Ibu dari Siswa)</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="pregnancyMedication" class="form-label required">Konsumsi obat-obatan pada saat
                        kehamilan</label>
                    <select class="form-select" id="pregnancyMedication" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="pregnancyMedication-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="pregnancyMedicationYesField">
                        <div class="mb-3">
                            <label for="pregnancyMedicationExplanation"
                                class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="pregnancyMedicationExplanation" rows="2"></textarea>
                            <div class="error-message" id="pregnancyMedicationExplanation-error">Harap jelaskan
                                pengobatan selama kehamilan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pregnancyIllness" class="form-label required">Penyakit Selama Kehamilan</label>
                    <select class="form-select" id="pregnancyIllness" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="pregnancyIllness-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="pregnancyIllnessYesField">
                        <div class="mb-3">
                            <label for="pregnancyIllnessExplanation" class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="pregnancyIllnessExplanation" rows="2"></textarea>
                            <div class="error-message" id="pregnancyIllnessExplanation-error">Harap jelaskan
                                penyakit
                                selama kehamilan</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gestationalAge" class="form-label required">Usia kehamilan pada saat
                        persalinan</label>
                    <select class="form-select" id="gestationalAge" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="Cukup Bulan">Cukup Bulan</option>
                        <option value="Prematur">Kurang Bulan</option>
                        <option value="Lewat Waktu">Lebih Bulan</option>
                    </select>
                    <div class="error-message" id="gestationalAge-error">Harap pilih usia kehamilan</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="deliveryMethod" class="form-label required">Proses persalinan</label>
                    <select class="form-select" id="deliveryMethod" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="normal">Persalinan pervaginam</option>
                        <option value="operasi">Operasi</option>
                    </select>
                    <div class="error-message" id="deliveryMethod-error">Harap pilih cara persalinan</div>
                </div>

                <h6 class="subsection-title">KONDISI BAYI SAAT LAHIR (ananda)</h6>

                <div class="col-md-6 mb-3">
                    <label for="birthWeight" class="form-label required">Berat Badan Lahir (kg)</label>
                    <input type="number" class="form-control" id="birthWeight" step="0.01" min="0"
                        required>
                    <div class="error-message" id="birthWeight-error">Harap masukkan berat badan lahir</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="birthHeight" class="form-label required">Tinggi Badan Lahir (cm)</label>
                    <input type="number" class="form-control" id="birthHeight" step="0.1" min="0"
                        required>
                    <div class="error-message" id="birthHeight-error">Harap masukkan tinggi badan lahir</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="walkedAtAge" class="form-label required">Mulai Berjalan pada Usia (bulan)</label>
                    <input type="number" class="form-control" id="walkedAtAge" min="0" required>
                    <div class="error-message" id="walkedAtAge-error">Harap masukkan usia saat mulai berjalan</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="spokeAtAge" class="form-label required">Mulai Bicara pada Usia (bulan)</label>
                    <input type="number" class="form-control" id="spokeAtAge" min="0" required>
                    <div class="error-message" id="spokeAtAge-error">Harap masukkan usia saat mulai bicara</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required">Apakah ada obat-obatan yang secara rutin dikonsumsi oleh
                        Ananda</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="routineMedication"
                                id="routineMedicationYes" value="true" required>
                            <label class="form-check-label" for="routineMedicationYes">Ya</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="routineMedication"
                                id="routineMedicationNo" value="false">
                            <label class="form-check-label" for="routineMedicationNo">Tidak</label>
                        </div>
                    </div>
                    <div class="error-message" id="routineMedication-error">Harap pilih opsi</div>
                </div>

                <div class="col-12 conditional-field" id="routineMedicationYesField">
                    <div class="mb-3">
                        <label for="routineMedicationExplanation" class="form-label required">Penjelasan</label>
                        <textarea class="form-control" id="routineMedicationExplanation" rows="2"></textarea>
                        <div class="error-message" id="routineMedicationExplanation-error">Harap jelaskan konsumsi
                            obat rutin</div>
                    </div>
                </div>
            </div>

            <h5 class="subsection-title">RIWAYAT PENYAKIT DAHULU</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="surgeryHistory" class="form-label required">Pernahkah Ananda dioperasi</label>
                    <select class="form-select" id="surgeryHistory" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="surgeryHistory-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="surgeryHistoryYesField">
                        <div class="mb-3">
                            <label for="surgeryNote" class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="surgeryNote" rows="2"></textarea>
                            <div class="error-message" id="surgeryNote-error">Harap jelaskan operasi apa</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="hospitalizationHistory" class="form-label required">Pernahkah Ananda dirawat di
                        Rumah Sakit</label>
                    <select class="form-select" id="hospitalizationHistory" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="hospitalizationHistory-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="hospitalizationHistoryYesField">
                        <div class="mb-3">
                            <label for="hospitalizationNote" class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="hospitalizationNote" rows="2"></textarea>
                            <div class="error-message" id="hospitalizationNote-error">Harap jelaskan karena penyakit
                                apa</div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-6 mb-3">
                    <label for="pastIllness" class="form-label required">Penyakit yang pernah diderita oleh Ananda
                        sebelumnya</label>
                    <select class="form-select" id="pastIllness" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Ya">Ya</option>
                    </select>
                    <div class="error-message" id="pastIllness-error">Harap pilih opsi</div>
                </div> --}}

                <div class="col-md-6 mb-3">
                    <label for="seizureHistory" class="form-label required">Apakah anak memiliki riwayat
                        kejang?</label>
                    <select class="form-select" id="seizureHistory" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="seizureHistory-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="seizureHistoryYesField">
                        <div class="mb-3">
                            <label for="seizureNote" class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="seizureNote" rows="2"></textarea>
                            <div class="error-message" id="seizureNote-error">Harap jelaskan karena penyakit
                                apa</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="accidentHistory" class="form-label required">Apakah anak memiliki riwayat
                        kecelakaan? (contoh: kepala terbentur)</label>
                    <select class="form-select" id="accidentHistory" required>
                        <option value="" selected disabled>Pilih opsi</option>
                        <option value="false">Tidak</option>
                        <option value="true">Ya</option>
                    </select>
                    <div class="error-message" id="accidentHistory-error">Harap pilih opsi</div>

                    <div class="col-12 conditional-field" id="accidentHistoryYesField">
                        <div class="mb-3">
                            <label for="accidentNote" class="form-label required">Penjelasan</label>
                            <textarea class="form-control" id="accidentNote" rows="2"></textarea>
                            <div class="error-message" id="accidentNote-error">Harap jelaskan karena penyakit
                                apa</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="familyDiseaseHistory" class="form-label required">Riwayat Penyakit Keluarga</label>
                    <textarea class="form-control" id="familyDiseaseHistory" rows="3" required></textarea>
                    <div class="error-message" id="familyDiseaseHistory-error">Harap masukkan riwayat penyakit
                        keluarga</div>
                </div>

                <div class="col-12 mb-3">
                    <label for="referralFacility" class="form-label required">Fasilitas Kesehatan Rujukan (Nama &
                        Lokasi)</label>
                    <textarea class="form-control" id="referralFacility" rows="2" required></textarea>
                    <div class="error-message" id="referralFacility-error">Harap masukkan fasilitas kesehatan
                        rujukan</div>
                </div>

                <div class="col-12 mb-3">
                    <label for="emergencyContactInfo" class="form-label required">Informasi kontak darurat (nama,
                        hubungan, dan nomor telepon yang dapat dihubungi):
                    </label>
                    <textarea class="form-control" id="emergencyContactInfo" rows="2" required></textarea>
                    <div class="error-message" id="emergencyContactInfo-error">Harap masukkan informasi kontak
                        darurat</div>
                </div>
            </div>

            <!-- Parent Declaration Section -->
            <div class="checkbox-declaration">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="parentDeclaration" required>
                    <label class="form-check-label required" style="text-align: justify" for="parentDeclaration">
                        Saya menyatakan kesanggupan saya untuk melengkapi dan membayarkan seluruh kewajiban di Mutiara
                        Harapan Islamic School dimulai dan saya bersedia menerima segala konsekuensinya apabila tidak
                        memenuhi kewajiban tersebut, termasuk namun tidak terbatas pada hilangnya hak anak saya untuk
                        mengikuti kegiatan belajar di Mutiara Harapan Islamic School sebelum kewajiban tersebut kami
                        penuhi seluruhnya.
                    </label>
                    <div class="error-message" id="parentDeclaration-error">Anda harus menyetujui deklarasi</div>
                </div>
            </div>

            <!-- Final Submit Section -->
            <div class="final-submit-section">
                <div class="alert alert-success">
                    <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Penyelesaian Formulir</h5>
                    <p class="mb-3">Selamat! Anda telah menyelesaikan semua bagian formulir pendaftaran. Silakan
                        tinjau informasi Anda sebelum mengirimkan.</p>
                    <button type="button" class="btn btn-success btn-lg" id="final-submit-btn">
                        <i class="bi bi-send-check"></i> Kirim Formulir
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
    <script src="/assets/compiled/js/script.js?v=1.1.3"></script>
    <script src="/assets/static/js/constant.js?v=1.1.5"></script>
    <script src="/assets/static/js/pages/student-enrolment.js?v=1.0.0"></script>
</body>

</html>
