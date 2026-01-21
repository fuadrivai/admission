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
    <link rel="stylesheet" href="/assets/static/css/student-enrolment.css?v=1.0.1">

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
                <div class="step-title">Code</div>
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
            <h2 class="section-title">Student Information</h2>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="studentFullName" class="form-label required">Full Name</label>
                    <br><small><i>Nama Lengkap Siswa</i></small>
                    <input type="text" class="form-control" id="studentFullName" required>
                    <div class="error-message" id="studentFullName-error">Please enter the student's full name</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="studentNickname" class="form-label required">Nickname</label>
                    <br><small><i>Nama Panggilan Ananda</i></small>
                    <input type="text" class="form-control" id="studentNickname" required>
                    <div class="error-message" id="studentNickname-error">Please enter the student's nickname</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Gender</label>
                    <br><small><i>Jenis Kelamin</i></small>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="studentGender"
                                id="studentGenderFemale" value="female" required>
                            <label class="form-check-label" for="studentGenderFemale">Female</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="studentGender"
                                id="studentGenderMale" value="male">
                            <label class="form-check-label" for="studentGenderMale">Male</label>
                        </div>
                    </div>
                    <div class="error-message" id="studentGender-error">Please select gender</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="placeOfBirth" class="form-label required">Place of Birth</label>
                    <br><small><i>Tempat Lahir</i></small>
                    <input type="text" class="form-control" id="placeOfBirth" required>
                    <div class="error-message" id="placeOfBirth-error">Please enter place of birth</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="dateOfBirth" class="form-label required">Date of Birth</label>
                    <br><small><i>Tanggal Lahir</i></small>
                    <input type="text" class="form-control date-picker" readonly id="dateOfBirth" required>
                    <div class="error-message" id="dateOfBirth-error">Please enter date of birth</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="religion" class="form-label required">Religion</label>
                    <br><small><i>Agama</i></small>
                    <select class="form-select religion" id="religion" required>
                        <option value="" selected disabled>Select religion</option>
                    </select>
                    <div class="error-message" id="religion-error">Please select religion</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="ethnicity" class="form-label required">Ethnicity</label>
                    <br><small><i>Suku Bangsa</i></small>
                    <input type="text" class="form-control" id="ethnicity" required>
                    <div class="error-message" id="ethnicity-error">Please enter ethnicity</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Citizenship</label>
                    <br><small><i>Kewarganegaraan</i></small>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="citizenship" id="citizenshipWNI"
                                value="WNI" required>
                            <label class="form-check-label" for="citizenshipWNI">Indonesian Citizen</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="citizenship" id="citizenshipWNA"
                                value="WNA">
                            <label class="form-check-label" for="citizenshipWNA">Foreign Citizen</label>
                        </div>
                    </div>
                    <div class="error-message" id="citizenship-error">Please select citizenship</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="height" class="form-label required">Height (cm)</label>
                    <br><small><i>Tinggi Badan (cm)</i></small>
                    <input type="number" class="form-control" id="height" step="0.1" min="0"
                        value="0" required>
                    <div class="error-message" id="height-error">Please enter height in cm</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="weight" class="form-label required">Weight (kg)</label>
                    <br><small><i>Berat Badan (kg)</i></small>
                    <input type="number" class="form-control" id="weight" step="0.1" min="0"
                        value="0" required>
                    <div class="error-message" id="weight-error">Please enter weight in kg</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="siblingsCount" class="form-label required">Number of Siblings
                    </label>
                    <br><small><i>Jumlah Saudara (kandung/tiri/angkat)</i></small>
                    <input type="number" class="form-control" id="siblingsCount" min="0" required>
                    <div class="error-message" id="siblingsCount-error">Please enter number of siblings</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="birthOrder" class="form-label required">Birth Order</label>
                    <br><small><i>Anak Ke-</i></small>
                    <input type="number" class="form-control" id="birthOrder" min="1" required>
                    <div class="error-message" id="birthOrder-error">Please enter birth order</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="homeLanguage" class="form-label required">Primary Language Spoken at Home</label>
                    <br><small><i>Bahasa pengantar di rumah</i></small>
                    <input type="text" class="form-control" id="homeLanguage" required>
                    <div class="error-message" id="homeLanguage-error">Please enter primary home language</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="otherLanguages" class="form-label required">Other Languages Spoken</label>
                    <br><small><i>Bahasa lain yang digunakan</i></small>
                    <input type="text" class="form-control" id="otherLanguages" required>
                    <div class="error-message" id="otherLanguages-error">Please enter other languages spoken</div>
                </div>

                <div class="col-12 mb-3">
                    <label for="fullAddress" class="form-label required">Full Address</label>
                    <br><small><i>Alamat Lengkap</i></small>
                    <textarea class="form-control" id="fullAddress" rows="3" required></textarea>
                    <div class="error-message" id="fullAddress-error">Please enter full address</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="postalCode" class="form-label required">Postal Code</label>
                    <br><small><i>Kode Post</i></small>
                    <input type="number" class="form-control" id="postalCode" required>
                    <div class="error-message" id="postalCode-error">Please enter postal code</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="homePhone" class="form-label">Home Phone (Optional)</label>
                    <br><small><i>Telepon Rumah</i></small>
                    <input type="tel" class="form-control" id="homePhone">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="parentPhone" class="form-label required">Parent Mobile Phone</label>
                    <br><small><i>No. handphone orangtua</i></small>
                    <input type="tel" class="form-control" id="parentPhone" required>
                    <div class="error-message" id="parentPhone-error">Please enter parent's mobile number</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="livingWith" class="form-label required">Living With</label>
                    <br><small><i>Tinggal Bersama</i></small>
                    <select class="form-select" id="livingWith" required>
                        <option value="" selected disabled>Select option</option>
                        <option value="parents">Parents</option>
                        <option value="family">Family</option>
                        <option value="others">Others</option>
                    </select>
                    <div class="error-message" id="livingWith-error">Please select whom the student lives with</div>

                    <div class="col-md-12 mb-3 conditional-field" id="livingWithOthersField">
                        <label for="livingWithOthers" class="form-label required">Please Specify</label>
                        <input type="text" class="form-control" id="livingWithOthers">
                        <div class="error-message" id="livingWithOthers-error">Please specify living arrangement</div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="distanceToSchool" class="form-label required">Distance to School (KM)</label>
                    <br><small><i>Jarak Ke Sekolah (KM)</i></small>
                    <input type="number" class="form-control" id="distanceToSchool" step="0.1" min="0"
                        required>
                    <div class="error-message" id="distanceToSchool-error">Please enter distance to school</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="previousSchool" class="form-label required">Previous School</label>
                    <br><small><i>Asal Sekolah</i></small>
                    <input type="text" class="form-control" id="previousSchool" required>
                    <div class="error-message" id="previousSchool-error">Please enter previous school</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="previousSchoolAddress" class="form-label required">Previous School Address</label>
                    <br><small><i>Alamat Sekolah Asal</i></small>
                    <input type="text" class="form-control" id="previousSchoolAddress" required>
                    <div class="error-message" id="previousSchoolAddress-error">Please enter previous school address
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="graduationYear" class="form-label required">Year of Graduation / Leaving Previous
                        School</label>
                    <br><small><i>Tahun kelulusan/meninggalkan sekolah asal</i></small>
                    <select class="form-select" id="graduationYear" required>
                        <option value="" selected disabled>Select year</option>
                    </select>
                    <div class="error-message" id="graduationYear-error">Please select graduation year</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="applyingLevel" class="form-label required">Applying for Level:</label>
                    <br><small><i>Mendaftar untuk level</i></small>
                    <select class="form-select" id="applyingLevel" required></select>
                    <div class="error-message" id="applyingLevel-error">Please select applying level</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="applyingClass" class="form-label required">Grade</label>
                    <br><small><i>Kelas</i></small>
                    <select class="form-select" id="applyingClass" required>
                        <option value="" selected disabled>Select class</option>
                    </select>
                    <div class="error-message" id="applyingClass-error">Please select class</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="academic-year" class="form-label required">Academic Year</label>
                    <br><small><i>Tahun Ajaran</i></small>
                    <select class="form-select" id="academic-year" required>
                        <option value="" selected disabled>Select academic year</option>
                    </select>
                    <div class="error-message" id="academic-year-error">Please select academic year</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="emergencyContactPriority" class="form-label required">Emergency Contact
                        Priority</label>
                    <br><small><i>Prioritas Kontak Darurat</i></small>
                    <select class="form-select" id="emergencyContactPriority" required>
                        <option value="" selected disabled>Select priority</option>
                        <option value="Ayah">Father</option>
                        <option value="Ibu">Mother</option>
                        <option value="Wali">Guardian</option>
                    </select>
                    <div class="error-message" id="emergencyContactPriority-error">Please select emergency contact
                        priority</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="notAttendingSchool" class="form-label required">Has the student ever not attended
                        school?</label>
                    <br><small><i>Apakah calon siswa pernah tidak bersekolah?</i></small>
                    <select class="form-select" id="notAttendingSchool" required>
                        <option value="" selected disabled>Select option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="notAttendingSchool-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="notAttendingSchoolYesField">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="notAttendingDuration" class="form-label required">Duration</label>
                                <input type="text" class="form-control" id="notAttendingDuration">
                                <div class="error-message" id="notAttendingDuration-error">Please enter duration</div>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="notAttendingReason" class="form-label required">Reason</label>
                                <input type="text" class="form-control" id="notAttendingReason">
                                <div class="error-message" id="notAttendingReason-error">Please enter reason</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="developmentalAssessment" class="form-label required">
                        Has the student ever undergone developmental or psychological assessment?
                    </label>
                    <br><small><i>Apakah calon siswa pernah melakukan pemeriksaan tumbuh kembang atau
                            psikologis?</i></small>
                    <select class="form-select" id="developmentalAssessment" required>
                        <option value="" selected disabled>Select option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="developmentalAssessment-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="developmentalAssessmentYesField">
                        <div class="mb-3">
                            <label for="developmentalDiagnosis" class="form-label required">Diagnosis
                                Description</label>
                            <textarea class="form-control" id="developmentalDiagnosis" rows="2"></textarea>
                            <div class="error-message" id="developmentalDiagnosis-error">Please enter diagnosis
                                description</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="therapyHistory" class="form-label required">
                        Has the student ever undergone special therapy related to physical or behavioral challenges?
                    </label>
                    <br><small><i>Apakah calon siswa pernah menjalani terapi khusus sehubungan dengan hambatan fisik
                            atau
                            tingkah laku?</i></small>
                    <select class="form-select" id="therapyHistory" required>
                        <option value="" selected disabled>Select option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="therapyHistory-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="therapyHistoryYesField">
                        <div class="mb-3">
                            <label for="therapyType" class="form-label required">Type & Duration of Therapy</label>
                            <textarea class="form-control" id="therapyType" rows="2"></textarea>
                            <div class="error-message" id="therapyType-error">Please enter type and duration of
                                therapy</div>
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
                    <input type="text" class="number2 form-control" id="guardianIncome">
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
