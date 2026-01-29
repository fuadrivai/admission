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
                <div class="step-title">student</div>
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

            <div class="arabic-text">
                ٱلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ ٱللَّٰهِ وَبَرَكَاتُهُ
            </div>


            <p class="mb-3" style="text-align: justify">Dear Parents, as part of the enrolment process, please
                complete this Parent & Student Information Form. This form will take approximately 5–10 minutes to
                complete. Please read and fill in each section carefully.</p>
            <p><small><i>Ayah/Bunda, sebagai bagian dari proses pendaftaran, harap membaca dan mengisi setiap poin dalam
                        formulir ini dengan teliti.</i></small></p>

            <div class="info-box">
                <h5 class="mb-2">Form Requirements:</h5>
                <ol class="mb-0">
                    <li>Information form</li>
                    <li>Parent consent letter</li>
                    <li>Submission of supporting documents</li>
                </ol>
            </div>

            <p class="mb-4">For further assistance, please contact our Admission team via WhatsApp: <strong><a
                        style="text-decoration:unset" href="https://wa.me/6281291823247" target="_blank">
                        +62 812 9182 3247
                    </a></strong>
            </p>

            <p class="mb-4">Warm regards,<br>
                Mutiara Harapan Islamic School</p>

            <p class="mb-4"><em>#HomeOfChampions #HomeOfIslamicLeaders</em></p>

            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">School Visit Code/Enrolment Code</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">

                        <input type="text" class="form-control" id="enrollmentCode"
                            placeholder="kode pendaftaran Ananda" required>
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
                    <input type="text" class="form-control" id="religion" required>
                    <div class="error-message" id="religion-error">Please enter religion</div>
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
                    <br><small><i>Kode Pos</i></small>
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
                    <label for="graduationYear" class="form-label required">Year of Graduation/Leaving Previous
                        School</label>
                    <br><small><i>Tahun kelulusan/meninggalkan sekolah asal</i></small>
                    <select class="form-select academic-year" id="graduationYear" required>
                        <option value="" selected disabled>Select year</option>
                    </select>
                    <div class="error-message" id="graduationYear-error">Please select graduation year</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="applyingLevel" class="form-label required">Enrolment Level:</label>
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
                    <select class="form-select academic-year" id="academic-year" required>
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
                    <label for="notAttendingSchool" class="form-label required">Has the student ever had a period of
                        not attending school?</label>
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
                        Has the student ever had a developmental or psychological assessment?
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
                        Have students ever participated in special therapy related to physical or behavioral challenges?
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
            <h2 class="section-title">Father’s Information</h2>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fatherFullName" class="form-label required">Full Name</label>
                    <br><i><small>Nama Lengkap</small></i>
                    <input type="hidden" class="form-control" id="fatherId">
                    <input type="text" class="form-control" id="fatherFullName" required>
                    <div class="error-message" id="fatherFullName-error">
                        Please enter the father’s full name
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherMobile" class="form-label required">Mobile Phone</label>
                    <br><i><small>Telepon Seluler</small></i>
                    <input type="tel" class="form-control" id="fatherMobile" required>
                    <div class="error-message" id="fatherMobile-error">
                        Please enter the father’s mobile phone number
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherHomePhone" class="form-label">Home Phone (Optional)</label>
                    <br><i><small>Telepon Rumah (Opsional)</small></i>
                    <input type="tel" class="form-control" id="fatherHomePhone">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherEmail" class="form-label required">Email Address</label>
                    <br><i><small>Email</small></i>
                    <input type="email" class="form-control" id="fatherEmail" required>
                    <div class="error-message" id="fatherEmail-error">
                        Please enter a valid email address
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherPlaceOfBirth" class="form-label required">Place of Birth</label>
                    <br><i><small>Tempat Lahir</small></i>
                    <input type="text" class="form-control" id="fatherPlaceOfBirth" required>
                    <div class="error-message" id="fatherPlaceOfBirth-error">
                        Please enter the father’s place of birth
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherDateOfBirth" class="form-label required">Date of Birth</label>
                    <br><i><small>Tanggal Lahir</small></i>
                    <input type="text" class="form-control date-picker" readonly id="fatherDateOfBirth" required>
                    <div class="error-message" id="fatherDateOfBirth-error">
                        Please enter the father’s date of birth
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherIdCard" class="form-label required">ID Card / Passport Number</label>
                    <br><i><small>No. KTP / Paspor</small></i>
                    <input type="text" class="form-control" id="fatherIdCard" required>
                    <div class="error-message" id="fatherIdCard-error">
                        Please enter the father’s ID card or passport number
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherReligion" class="form-label required">Religion</label>
                    <br><i><small>Agama</small></i>
                    <input type="text" class="form-control" id="fatherReligion" required>
                    <div class="error-message" id="fatherReligion-error">
                        Please select the father’s religion
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherEthnicity" class="form-label required">Ethnicity</label>
                    <br><i><small>Suku Bangsa</small></i>
                    <input type="text" class="form-control" id="fatherEthnicity" required>
                    <div class="error-message" id="fatherEthnicity-error">
                        Please enter the father’s ethnicity
                    </div>
                </div>

                <div class="col-md-8 mb-3">
                    <label for="fatherAddress" class="form-label required">Full Address</label>
                    <br><i><small>Alamat Lengkap</small></i>
                    <textarea class="form-control" id="fatherAddress" rows="3" required></textarea>
                    <div class="error-message" id="fatherAddress-error">
                        Please enter the father’s full address
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherPostalCode" class="form-label required">Postal Code</label>
                    <br><i><small>Kode Pos</small></i>
                    <input type="number" class="form-control" id="fatherPostalCode" required>
                    <div class="error-message" id="fatherPostalCode-error">
                        Please enter the father’s postal code
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherEducation" class="form-label required">Education Level</label>
                    <br><i><small>Pendidikan</small></i>
                    <select class="form-select education" id="fatherEducation" required>
                        <option value="" selected disabled>Select education level</option>
                    </select>
                    <div class="error-message" id="fatherEducation-error">
                        Please select the father’s education level
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherOccupation" class="form-label required">Occupation</label>
                    <br><i><small>Pekerjaan</small></i>
                    <select class="form-select job" id="fatherOccupation" required>
                        <option value="" selected disabled>Select occupation</option>
                    </select>
                    <div class="error-message" id="fatherOccupation-error">
                        Please select the father’s occupation
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fatherInstitution" class="form-label required">Institution Name</label>
                    <br><i><small>Nama Instansi</small></i>
                    <input type="text" class="form-control" id="fatherInstitution" required>
                    <div class="error-message" id="fatherInstitution-error">
                        Please enter the father’s institution name
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="fatherInstitutionAddress" class="form-label required">Institution Address</label>
                    <br><i><small>Alamat Instansi</small></i>
                    <textarea class="form-control" id="fatherInstitutionAddress" rows="2" required></textarea>
                    <div class="error-message" id="fatherInstitutionAddress-error">
                        Please enter the father’s institution address
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="fatherIncome" class="form-label required">Monthly Income (IDR)</label>
                    <br><i><small>Penghasilan Bulanan</small></i>
                    <input type="text" class="form-control number2" id="fatherIncome" required>
                    <div class="error-message" id="fatherIncome-error">
                        Please enter the father’s monthly income
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required">Marital Status</label>
                    <br><i><small>Status Pernikahan</small></i>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="fatherMaritalStatus"
                                id="fatherMarried" value="Married" required>
                            <label class="form-check-label" for="fatherMarried">Married</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="fatherMaritalStatus"
                                id="fatherDivorced" value="Divorced">
                            <label class="form-check-label" for="fatherDivorced">Divorced</label>
                        </div>
                    </div>
                    <div class="error-message" id="fatherMaritalStatus-error">
                        Please select the father’s marital status
                    </div>
                </div>
            </div>
        </div>


        <!-- Step 4: Mother Data -->
        <div class="step-content" id="step-4">
            <h2 class="section-title">Mother’s Information</h2>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="motherFullName" class="form-label required">Full Name</label>
                    <br><i><small>Nama Lengkap</small></i>
                    <input type="hidden" class="form-control" id="motherId">
                    <input type="text" class="form-control" id="motherFullName" required>
                    <div class="error-message" id="motherFullName-error">
                        Please enter the mother’s full name
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherMobile" class="form-label required">Mobile Phone</label>
                    <br><i><small>Telepon Seluler</small></i>
                    <input type="tel" class="form-control" id="motherMobile" required>
                    <div class="error-message" id="motherMobile-error">
                        Please enter the mother’s mobile phone number
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherHomePhone" class="form-label">Home Phone (Optional)</label>
                    <br><i><small>Telepon Rumah (Opsional)</small></i>
                    <input type="tel" class="form-control" id="motherHomePhone">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherEmail" class="form-label required">Email Address</label>
                    <br><i><small>Email</small></i>
                    <input type="email" class="form-control" id="motherEmail" required>
                    <div class="error-message" id="motherEmail-error">
                        Please enter a valid email address
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherPlaceOfBirth" class="form-label required">Place of Birth</label>
                    <br><i><small>Tempat Lahir</small></i>
                    <input type="text" class="form-control" id="motherPlaceOfBirth" required>
                    <div class="error-message" id="motherPlaceOfBirth-error">
                        Please enter the mother’s place of birth
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherDateOfBirth" class="form-label required">Date of Birth</label>
                    <br><i><small>Tanggal Lahir</small></i>
                    <input type="text" class="form-control date-picker" readonly id="motherDateOfBirth" required>
                    <div class="error-message" id="motherDateOfBirth-error">
                        Please enter the mother’s date of birth
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherIdCard" class="form-label required">ID Card / Passport Number</label>
                    <br><i><small>No. KTP / Paspor</small></i>
                    <input type="text" class="form-control" id="motherIdCard" required>
                    <div class="error-message" id="motherIdCard-error">
                        Please enter the mother’s ID card or passport number
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherReligion" class="form-label required">Religion</label>
                    <br><i><small>Agama</small></i>
                    <input type="text" class="form-control" id="motherReligion" required>
                    <div class="error-message" id="motherReligion-error">
                        Please enter the mother’s religion
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherEthnicity" class="form-label required">Ethnicity</label>
                    <br><i><small>Suku Bangsa</small></i>
                    <input type="text" class="form-control" id="motherEthnicity" required>
                    <div class="error-message" id="motherEthnicity-error">
                        Please enter the mother’s ethnicity
                    </div>
                </div>

                <div class="col-md-8 mb-3">
                    <label for="motherAddress" class="form-label required">Full Address</label>
                    <br><i><small>Alamat Lengkap</small></i>
                    <textarea class="form-control" id="motherAddress" rows="3" required></textarea>
                    <div class="error-message" id="motherAddress-error">
                        Please enter the mother’s full address
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherPostalCode" class="form-label required">Postal Code</label>
                    <br><i><small>Kode Pos</small></i>
                    <input type="number" class="form-control" id="motherPostalCode" required>
                    <div class="error-message" id="motherPostalCode-error">
                        Please enter the mother’s postal code
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherEducation" class="form-label required">Education Level</label>
                    <br><i><small>Pendidikan</small></i>
                    <select class="form-select education" id="motherEducation" required>
                        <option value="" selected disabled>Select education level</option>
                    </select>
                    <div class="error-message" id="motherEducation-error">
                        Please select the mother’s education level
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherOccupation" class="form-label required">Occupation</label>
                    <br><i><small>Pekerjaan</small></i>
                    <select class="form-select job" id="motherOccupation" required>
                        <option value="" selected disabled>Select occupation</option>
                    </select>
                    <div class="error-message" id="motherOccupation-error">
                        Please select the mother’s occupation
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="motherInstitution" class="form-label required">Institution Name</label>
                    <br><i><small>Nama Instansi</small></i>
                    <input type="text" class="form-control" id="motherInstitution" required>
                    <div class="error-message" id="motherInstitution-error">
                        Please enter the mother’s institution name
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="motherInstitutionAddress" class="form-label required">Institution Address</label>
                    <br><i><small>Alamat Instansi</small></i>
                    <textarea class="form-control" id="motherInstitutionAddress" rows="2" required></textarea>
                    <div class="error-message" id="motherInstitutionAddress-error">
                        Please enter the mother’s institution address
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="motherIncome" class="form-label required">Monthly Income (IDR)</label>
                    <br><i><small>Penghasilan Bulanan</small></i>
                    <input type="text" class="form-control number2" id="motherIncome" required>
                    <div class="error-message" id="motherIncome-error">
                        Please enter the mother’s monthly income
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required">Marital Status</label>
                    <br><i><small>Status Pernikahan</small></i>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="motherMaritalStatus"
                                id="motherMarried" value="Married" required>
                            <label class="form-check-label" for="motherMarried">Married</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="motherMaritalStatus"
                                id="motherDivorced" value="Divorced">
                            <label class="form-check-label" for="motherDivorced">Divorced</label>
                        </div>
                    </div>
                    <div class="error-message" id="motherMaritalStatus-error">
                        Please select the mother’s marital status
                    </div>
                </div>
            </div>
        </div>


        <!-- Step 5: Guardian Data (Optional) -->
        <div class="step-content" id="step-5">
            <h2 class="section-title">Guardian’s Information (Optional)</h2>
            <p class="text-muted mb-4">
                Please fill in only if there is a guardian other than the biological parents
                <br><i><small>Isi hanya jika ada wali selain orang tua kandung</small></i>
            </p>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="guardianFullName" class="form-label">Full Name</label>
                    <br><i><small>Nama Lengkap</small></i>
                    <input type="hidden" class="form-control" id="guardianId">
                    <input type="text" class="form-control" id="guardianFullName">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianMobile" class="form-label">Mobile Phone</label>
                    <br><i><small>Telepon Seluler</small></i>
                    <input type="tel" class="form-control" id="guardianMobile">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianHomePhone" class="form-label">Home Phone</label>
                    <br><i><small>Telepon Rumah</small></i>
                    <input type="tel" class="form-control" id="guardianHomePhone">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianEmail" class="form-label">Email Address</label>
                    <br><i><small>Email</small></i>
                    <input type="email" class="form-control" id="guardianEmail">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianPlaceOfBirth" class="form-label">Place of Birth</label>
                    <br><i><small>Tempat Lahir</small></i>
                    <input type="text" class="form-control" id="guardianPlaceOfBirth">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianDateOfBirth" class="form-label">Date of Birth</label>
                    <br><i><small>Tanggal Lahir</small></i>
                    <input type="text" class="form-control date-picker" readonly id="guardianDateOfBirth">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianIdCard" class="form-label">ID Card / Passport Number</label>
                    <br><i><small>No. KTP / Paspor</small></i>
                    <input type="text" class="form-control" id="guardianIdCard">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianReligion" class="form-label">Religion</label>
                    <br><i><small>Agama</small></i>
                    <input type="text" class="form-control" id="guardianReligion">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianEthnicity" class="form-label">Ethnicity</label>
                    <br><i><small>Suku Bangsa</small></i>
                    <input type="text" class="form-control" id="guardianEthnicity">
                </div>

                <div class="col-md-8 mb-3">
                    <label for="guardianAddress" class="form-label">Full Address</label>
                    <br><i><small>Alamat Lengkap</small></i>
                    <textarea class="form-control" id="guardianAddress" rows="3"></textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianPostalCode" class="form-label">Postal Code</label>
                    <br><i><small>Kode Pos</small></i>
                    <input type="number" class="form-control" id="guardianPostalCode">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianEducation" class="form-label">Education Level</label>
                    <br><i><small>Pendidikan</small></i>
                    <select class="form-select education" id="guardianEducation">
                        <option value="" selected>Select education level (optional)</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianOccupation" class="form-label">Occupation</label>
                    <br><i><small>Pekerjaan</small></i>
                    <select class="form-select job" id="guardianOccupation">
                        <option value="" selected>Select occupation (optional)</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="guardianInstitution" class="form-label">Institution Name</label>
                    <br><i><small>Nama Instansi</small></i>
                    <input type="text" class="form-control" id="guardianInstitution">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="guardianInstitutionAddress" class="form-label">Institution Address</label>
                    <br><i><small>Alamat Instansi</small></i>
                    <textarea class="form-control" id="guardianInstitutionAddress" rows="2"></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="guardianIncome" class="form-label">Monthly Income (IDR)</label>
                    <br><i><small>Penghasilan Bulanan</small></i>
                    <input type="text" class="number2 form-control" id="guardianIncome">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Marital Status</label>
                    <br><i><small>Status Pernikahan</small></i>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="guardianMaritalStatus"
                                id="guardianMarried" value="Married">
                            <label class="form-check-label" for="guardianMarried">Married</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="guardianMaritalStatus"
                                id="guardianDivorced" value="Divorced">
                            <label class="form-check-label" for="guardianDivorced">Divorced</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Step 6: Student Health Data & Declaration -->
        <div class="step-content" id="step-6">
            <h2 class="section-title">Student Health Information</h2>

            <!-- Health Data Section -->
            <h5 class="subsection-title">BASIC IMMUNISATION <i><small>(Imunisasi Dasar)</small></i></h5>
            <div class="mb-4">
                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">BCG</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationBCG"
                                    id="immunizationBCGYes" value="true" required>
                                <label class="form-check-label" for="immunizationBCGYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationBCG"
                                    id="immunizationBCGNo" value="false">
                                <label class="form-check-label" for="immunizationBCGNo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationBCG-error">Please select an option</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Hepatitis</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitis"
                                    id="immunizationHepatitisYes" value="true" required>
                                <label class="form-check-label" for="immunizationHepatitisYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitis"
                                    id="immunizationHepatitisNo" value="false">
                                <label class="form-check-label" for="immunizationHepatitisNo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationHepatitis-error">Please select an option</div>
                    </div>
                </div>

                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Diphtheria / Tetanus / Pertussis</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationDTP"
                                    id="immunizationDTPYes" value="true" required>
                                <label class="form-check-label" for="immunizationDTPYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationDTP"
                                    id="immunizationDTPNo" value="false">
                                <label class="form-check-label" for="immunizationDTPNo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationDTP-error">Please select an option</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Polio</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationPolio"
                                    id="immunizationPolioYes" value="true" required>
                                <label class="form-check-label" for="immunizationPolioYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationPolio"
                                    id="immunizationPolioNo" value="false">
                                <label class="form-check-label" for="immunizationPolioNo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationPolio-error">Please select an option</div>
                    </div>
                </div>

                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Measles</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMeasles"
                                    id="immunizationMeaslesYes" value="true" required>
                                <label class="form-check-label" for="immunizationMeaslesYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMeasles"
                                    id="immunizationMeaslesNo" value="false">
                                <label class="form-check-label" for="immunizationMeaslesNo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationMeasles-error">Please select an option</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Hepatitis A</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitisA"
                                    id="immunizationHepatitisAYes" value="true" required>
                                <label class="form-check-label" for="immunizationHepatitisAYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationHepatitisA"
                                    id="immunizationHepatitisANo" value="false">
                                <label class="form-check-label" for="immunizationHepatitisANo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationHepatitisA-error">Please select an option</div>
                    </div>
                </div>

                <div class="row immunization-row">
                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">MMR</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMMR"
                                    id="immunizationMMRYes" value="true" required>
                                <label class="form-check-label" for="immunizationMMRYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationMMR"
                                    id="immunizationMMRNo" value="false">
                                <label class="form-check-label" for="immunizationMMRNo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationMMR-error">Please select an option</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required d-block mb-2">Influenza</label>
                        <div class="inline-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationInfluenza"
                                    id="immunizationInfluenzaYes" value="true" required>
                                <label class="form-check-label" for="immunizationInfluenzaYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="immunizationInfluenza"
                                    id="immunizationInfluenzaNo" value="false">
                                <label class="form-check-label" for="immunizationInfluenzaNo">No</label>
                            </div>
                        </div>
                        <div class="error-message" id="immunizationInfluenza-error">Please select an option</div>
                    </div>
                </div>
            </div>


            <h5 class="subsection-title">MEDICAL RECORDS <i><small>Catatan Kesehatan</small></i></h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="foodAllergy" class="form-label required">Food Allergy</label>
                    <select class="form-select" id="foodAllergy" required>
                        <option value="" selected disabled>select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="foodAllergy-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="foodAllergyYesField">
                        <div class="mb-3">
                            <label for="foodAllergyExplanation" class="form-label required">Explanation</label>
                            <textarea class="form-control" id="foodAllergyExplanation" rows="2"></textarea>
                            <div class="error-message" id="foodAllergyExplanation-error">Please explain the food
                                allergy
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="drugAllergy" class="form-label required">Medication Allergy</label>
                    <select class="form-select" id="drugAllergy" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="drugAllergy-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="drugAllergyYesField">
                        <div class="mb-3">
                            <label for="drugAllergyExplanation" class="form-label required">Explanation</label>
                            <textarea class="form-control" id="drugAllergyExplanation" rows="2"></textarea>
                            <div class="error-message" id="drugAllergyExplanation-error">Please explain the
                                medication allergy
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="bloodType" class="form-label required">Blood Type</label>
                    <select class="form-select" id="bloodType" required>
                        <option value="" selected disabled>Select blood type</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                        <option value="Unknown">Unknown</option>
                    </select>
                    <div class="error-message" id="bloodType-error">Please select blood type</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Wears Glasses</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesGlasses"
                                id="usesGlassesYes" value="true" required>
                            <label class="form-check-label" for="usesGlassesYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesGlasses" id="usesGlassesNo"
                                value="false">
                            <label class="form-check-label" for="usesGlassesNo">No</label>
                        </div>
                    </div>
                    <div class="error-message" id="usesGlasses-error">Please select an option</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required">Uses Hearing Aid</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesHearingAid"
                                id="usesHearingAidYes" value="true" required>
                            <label class="form-check-label" for="usesHearingAidYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usesHearingAid"
                                id="usesHearingAidNo" value="false">
                            <label class="form-check-label" for="usesHearingAidNo">No</label>
                        </div>
                    </div>
                    <div class="error-message" id="usesHearingAid-error">Please select an option</div>
                </div>
            </div>

            <h5 class="subsection-title">Pregnancy History (Student’s Mother)</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="pregnancyMedication" class="form-label required">Medication Use During
                        Pregnancy</label>
                    <select class="form-select" id="pregnancyMedication" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="pregnancyMedication-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="pregnancyMedicationYesField">
                        <div class="mb-3">
                            <label for="pregnancyMedicationExplanation"
                                class="form-label required">Explanation</label>
                            <textarea class="form-control" id="pregnancyMedicationExplanation" rows="2"></textarea>
                            <div class="error-message" id="pregnancyMedicationExplanation-error">Please explain
                                medication use during pregnancy</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pregnancyIllness" class="form-label required">Illness During Pregnancy</label>
                    <select class="form-select" id="pregnancyIllness" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="pregnancyIllness-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="pregnancyIllnessYesField">
                        <div class="mb-3">
                            <label for="pregnancyIllnessExplanation" class="form-label required">Explanation</label>
                            <textarea class="form-control" id="pregnancyIllnessExplanation" rows="2"></textarea>
                            <div class="error-message" id="pregnancyIllnessExplanation-error">Please explain
                                illness
                                during pregnancy</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gestationalAge" class="form-label required">Gestational Age at Birth</label>
                    <select class="form-select" id="gestationalAge" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="Full Term">Full Term</option>
                        <option value="Preterm">Preterm</option>
                        <option value="Post-term">Post-term</option>
                    </select>
                    <div class="error-message" id="gestationalAge-error">Please select gestational age</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="deliveryMethod" class="form-label required">Mode of Delivery</label>
                    <select class="form-select" id="deliveryMethod" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="normal">Vaginal Delivery (Persalinan
                            pervaginam)</option>
                        <option value="cesarean">Caesarean Section</option>
                    </select>
                    <div class="error-message" id="deliveryMethod-error">Please select delivery method</div>
                </div>

                <h6 class="subsection-title">Condition at Birth (Student)</h6>

                <div class="col-md-6 mb-3">
                    <label for="birthWeight" class="form-label required">Birth Weight (kg)</label>
                    <input type="number" class="form-control" id="birthWeight" step="0.01" min="0"
                        required>
                    <div class="error-message" id="birthWeight-error">Please enter birth weight</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="birthHeight" class="form-label required">Birth Length (cm) </label>
                    <input type="number" class="form-control" id="birthHeight" step="0.1" min="0"
                        required>
                    <div class="error-message" id="birthHeight-error">Please enter birth length</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="walkedAtAge" class="form-label required">Age Started Walking (months)</label>
                    <input type="number" class="form-control" id="walkedAtAge" min="0" required>
                    <div class="error-message" id="walkedAtAge-error">Please enter age started walking</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="spokeAtAge" class="form-label required">Age Started Speaking (months)</label>
                    <input type="number" class="form-control" id="spokeAtAge" min="0" required>
                    <div class="error-message" id="spokeAtAge-error">Please enter age started speaking</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required">Is the student currently taking any medication on a regular
                        basis?</label>
                    <div class="inline-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="routineMedication"
                                id="routineMedicationYes" value="true" required>
                            <label class="form-check-label" for="routineMedicationYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="routineMedication"
                                id="routineMedicationNo" value="false">
                            <label class="form-check-label" for="routineMedicationNo">No</label>
                        </div>
                    </div>
                    <div class="error-message" id="routineMedication-error">Please select an option</div>
                </div>

                <div class="col-12 conditional-field" id="routineMedicationYesField">
                    <div class="mb-3">
                        <label for="routineMedicationExplanation" class="form-label required">Explanation</label>
                        <textarea class="form-control" id="routineMedicationExplanation" rows="2"></textarea>
                        <div class="error-message" id="routineMedicationExplanation-error">Please explain the
                            routine medication</div>
                    </div>
                </div>
            </div>

            <h5 class="subsection-title">Previous Medical History</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="surgeryHistory" class="form-label required">Has the student ever undergone
                        surgery?</label>
                    <select class="form-select" id="surgeryHistory" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="surgeryHistory-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="surgeryHistoryYesField">
                        <div class="mb-3">
                            <label for="surgeryNote" class="form-label required">Explanation</label>
                            <textarea class="form-control" id="surgeryNote" rows="2"></textarea>
                            <div class="error-message" id="surgeryNote-error">Please explain the surgery</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="hospitalizationHistory" class="form-label required">Has the student ever been
                        hospitalized?</label>
                    <select class="form-select" id="hospitalizationHistory" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="hospitalizationHistory-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="hospitalizationHistoryYesField">
                        <div class="mb-3">
                            <label for="hospitalizationNote" class="form-label required">Explanation</label>
                            <textarea class="form-control" id="hospitalizationNote" rows="2"></textarea>
                            <div class="error-message" id="hospitalizationNote-error">Please explain the reason for
                                hospitalization</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="seizureHistory" class="form-label required">Does the student have a history of
                        seizures?</label>
                    <select class="form-select" id="seizureHistory" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="seizureHistory-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="seizureHistoryYesField">
                        <div class="mb-3">
                            <label for="seizureNote" class="form-label required">Explanation</label>
                            <textarea class="form-control" id="seizureNote" rows="2"></textarea>
                            <div class="error-message" id="seizureNote-error">Please explain the reason for seizures
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="accidentHistory" class="form-label required">Does the student have a history of
                        accidents or injuries? (e.g. head injury)</label>
                    <select class="form-select" id="accidentHistory" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="false">No</option>
                        <option value="true">Yes</option>
                    </select>
                    <div class="error-message" id="accidentHistory-error">Please select an option</div>

                    <div class="col-12 conditional-field" id="accidentHistoryYesField">
                        <div class="mb-3">
                            <label for="accidentNote" class="form-label required">Explanation</label>
                            <textarea class="form-control" id="accidentNote" rows="2"></textarea>
                            <div class="error-message" id="accidentNote-error">Please explain the reason for
                                accidents or injuries</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="familyDiseaseHistory" class="form-label required">Family Disease History</label>
                    <textarea class="form-control" id="familyDiseaseHistory" rows="3" required></textarea>
                    <div class="error-message" id="familyDiseaseHistory-error">Please enter family disease history
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="referralFacility" class="form-label required">Referral Health Facility (Name &
                        Location)</label>
                    <textarea class="form-control" id="referralFacility" rows="2" required></textarea>
                    <div class="error-message" id="referralFacility-error">Please enter referral health facility
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="emergencyContactInfo" class="form-label required">Emergency Contact Information
                        (name,
                        relationship, and phone number that can be contacted in case of emergency):
                    </label>
                    <textarea class="form-control" id="emergencyContactInfo" rows="2" required></textarea>
                    <div class="error-message" id="emergencyContactInfo-error">Please enter emergency contact
                        information</div>
                </div>
            </div>

            <!-- Parent Declaration Section -->
            <div class="checkbox-declaration">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="parentDeclaration" required>
                    <label class="form-check-label required" style="text-align: justify" for="parentDeclaration">
                        I hereby declare my commitment to complete and fulfil all financial and administrative
                        obligations at Mutiara Harapan Islamic School as required. I fully understand and agree to
                        accept any consequences arising from the failure to meet these obligations, including but not
                        limited to the suspension of my child’s right to participate in learning activities at Mutiara
                        Harapan Islamic School until such obligations have been fully settled. <br> <br>
                        The development fee, annual fee, school fee, and any other fees that I have paid are
                        non-refundable and non-transferable under any circumstances, except in the event that my child
                        is declared not accepted at Mutiara Harapan Islamic School Bangka. <br> <br>
                        <i><small>Development
                                fee, annual fee, school fee, dan biaya lainnya yang telah Saya bayarkan tidak dapat
                                ditarik kembali maupun dialihkan dengan alasan apa pun, kecuali apabila Putra/Putri Saya
                                dinyatakan tidak diterima di Mutiara Harapan Islamic School Bangka.</small></i>

                    </label>
                    <div class="error-message" id="parentDeclaration-error">You must agree to the declaration.</div>
                </div>
            </div>

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
    <script src="/assets/compiled/js/script.js?v=1.1.3"></script>
    <script src="/assets/static/js/constant.js?v=1.1.5"></script>
    <script src="/assets/static/js/pages/student-enrolment.js?v=1.0.0"></script>
</body>

</html>
