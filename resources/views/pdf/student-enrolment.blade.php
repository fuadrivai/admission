<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student and Parent Information Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(128, 0, 0, 0.1);
            overflow: hidden;
            padding: 15px;
            border: 1px solid #e0d6d6;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 2px solid #eae0e0;
        }

        .logo-container {
            flex: 0 0 180px;
        }

        .logo {
            max-width: 150px;
            height: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .title-container {
            text-align: right;
            flex: 1;
        }

        .main-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: #800000;
            font-size: 20px;
            margin-bottom: 8px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            font-style: italic;
            color: #8B4513;
            font-size: 14px;
            font-weight: 400;
        }

        /* Content Sections */
        .section {
            margin-bottom: 35px;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: #800000;
            font-size: 22px;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #A0522D;
            position: relative;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background-color: #D2691E;
        }

        .intro-content {
            background-color: #fff5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #800000;
            box-shadow: 0 3px 10px rgba(128, 0, 0, 0.05);
        }

        .intro-content p {
            margin-bottom: 12px;
        }

        .greeting {
            color: #800000;
            font-weight: 500;
            font-size: 18px;
        }

        .requirements-list {
            list-style-type: none;
            margin-top: 15px;
            padding-left: 5px;
        }

        .requirements-list li {
            margin-bottom: 8px;
            padding-left: 25px;
            position: relative;
        }

        .contact-info {
            background-color: #fff8f0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #D2691E;
            box-shadow: 0 3px 10px rgba(210, 105, 30, 0.1);
        }

        .contact-info i {
            color: #800000;
            margin-right: 10px;
        }

        .signature {
            text-align: right;
            margin-top: 25px;
            font-weight: 500;
            color: #555;
        }

        .school-name {
            color: #800000;
            font-weight: 600;
            font-size: 18px;
            margin-top: 5px;
        }

        .hashtags {
            color: #A0522D;
            font-weight: 500;
            margin-top: 8px;
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
            font-size: 15px;
        }

        .label-english {
            font-weight: 400;
            color: #666;
            font-size: 13px;
            font-style: italic;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 95%;
            padding: 10px 10px;
            border: 1px solid #d0b7b7;
            border-radius: 4px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            transition: all 0.3s;
            background-color: #fff;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 2px rgba(128, 0, 0, 0.2);
        }

        input[readonly] {
            background-color: #f9f0f0;
            color: #800000;
            font-weight: 500;
            border-color: #d0b7b7;
        }

        .radio-group {
            margin-top: 6px;
        }

        .radio-option {
            display: inline-block;
            margin-right: 25px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .radio-option input {
            vertical-align: middle;
            margin-right: 6px;
        }

        .radio-option label {
            display: inline;
            font-weight: 400;
            font-size: 14px;
            color: #333;
        }

        .conditional-field {
            margin-top: 15px;
            padding-left: 20px;
            border-left: 2px solid #D2691E;
            display: none;
        }

        /* Break Page for PDF */
        .page-break {
            page-break-before: always;
            break-before: page;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px dashed #d0b7b7;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eae0e0;
            text-align: center;
            color: #777;
            font-size: 14px;
        }


        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .title-container {
                text-align: center;
                margin-top: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .main-title {
                font-size: 24px;
            }
        }

        /* PDF Specific Adjustments */
        @media print {
            body {
                padding: 0;
                background: none;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
                border: none;
            }

            .page-break {
                page-break-before: always;
                break-before: page;
                margin-top: 0;
                padding-top: 30px;
                border-top: none;
            }

            .no-print {
                display: none;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="border-bottom:2px solid #eae0e0; margin-bottom:30px;">
            <tr>
                <!-- LEFT : LOGO -->
                <td width="40%" valign="top" style="padding-bottom:25px;">
                    <img src="{{ $logo }}" alt="School Logo" style="max-width:180px;">
                </td>

                <!-- RIGHT : TITLE -->
                <td width="60%" valign="top" align="right" style="padding-bottom:25px;">
                    <h1 style="margin:0; font-size:24px; color:#800000;">
                        Student and Parent Information Form
                    </h1>
                    <p style="margin-top:6px; font-style:italic; color:#800000;">
                        Formulir Data Diri Siswa dan Orang Tua
                    </p>
                </td>
            </tr>
        </table>
        <!-- Introduction Section -->
        <div class="section" id="section1">
            <div class="intro-content">
                <p class="greeting"> {{ config('student_enrolment.step1.labels.text0.indonesian') }}</p>
                <p>{{ config('student_enrolment.step1.labels.text1.english') }}</p>
                <p><i><small>{{ config('student_enrolment.step1.labels.text1.indonesian') }}</small></i></p>

                <p><strong>{{ config('student_enrolment.step1.labels.text2.english') }}</strong></p>
                <ul class="requirements-list">
                    <li>{{ config('student_enrolment.step1.labels.text3.english') }}</li>
                    <li>{{ config('student_enrolment.step1.labels.text4.english') }}</li>
                    <li>{{ config('student_enrolment.step1.labels.text5.english') }}</li>
                </ul>

                <div class="contact-info">
                    <p><i class="fas fa-comment-alt"></i> {!! config('student_enrolment.step1.labels.text6.english') !!}</p>
                </div>

                <div class="signature">
                    <p>Warm regards,</p>
                    <p class="school-name">Mutiara Harapan Islamic School</p>
                    <p class="hashtags">#HomeOfChampions #HomeOfIslamicLeaders</p>
                </div>
            </div>
        </div>

        <!-- Page Break for PDF -->
        <div class="page-break"></div>

        <!-- Student Information Section -->
        <div class="section" id="section2">
            <h2 class="section-title">Student Information</h2>

            <div class="form-grid">
                <!-- Row 1 -->
                <div class="form-group">
                    <label for="enrolmentCode">{{ config('student_enrolment.step1.labels.text9.english') }}</label>
                    <input type="text" id="enrolmentCode" name="enrolmentCode" value="{{ $data->code }}" readonly>
                </div>

                <div class="form-group">
                    <label for="fullName">{{ config('student_enrolment.step2.labels.fullname.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.fullname.indonesian') }})</span></label>
                    <input type="text" id="fullName" name="fullName" value="{{ $data->applicant->fullname }}"
                        readonly>
                </div>

                <!-- Row 2 -->
                <div class="form-group">
                    <label for="nickname">{{ config('student_enrolment.step2.labels.nickname.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.nickname.indonesian') }})</span></label>
                    <input type="text" id="nickname" name="nickname" value="{{ $data->applicant->nickname }}"
                        placeholder="Enter student's nickname">
                </div>

                <div class="form-group">
                    <label for="gender">{{ config('student_enrolment.step2.labels.gender.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.gender.indonesian') }})</span></label>

                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $data->applicant->gender == 'male' ? 'checked' : '' }} type="radio"
                                id="notAttendingYes" value="male">
                            <label for="notAttendingYes">Male</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $data->applicant->gender == 'female' ? 'checked' : '' }} type="radio"
                                id="notAttendingNo" value="female">
                            <label for="notAttendingNo">Female</label>
                        </div>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="form-group">
                    <label for="birthPlace">{{ config('student_enrolment.step2.labels.place_birth.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.place_birth.indonesian') }})</span></label>
                    <input type="text" id="birthPlace" name="birthPlace" placeholder="Enter place of birth"
                        value="{{ $data->applicant->place_of_birth }}">
                </div>

                <div class="form-group">
                    <label for="birthDate">{{ config('student_enrolment.step2.labels.date_birth.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.date_birth.indonesian') }})</span></label>
                    <input type="date" id="birthDate" name="birthDate" value="{{ $data->applicant->dateBirth() }}">
                </div>

                <!-- Row 4 -->
                <div class="form-group">
                    <label for="religion">{{ config('student_enrolment.step2.labels.religion.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.religion.indonesian') }})</span></label>
                    <input type="text" id="religion" name="religion" value="{{ $data->applicant->religion }}">
                </div>

                <div class="form-group">
                    <label for="ethnicity">{{ config('student_enrolment.step2.labels.ethnicity.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.ethnicity.indonesian') }})</span></label>
                    <input type="text" id="ethnicity" name="ethnicity"
                        value="{{ $data->applicant->ethnicity }}">
                </div>

                <!-- Row 5 -->
                <div class="form-group">
                    <label for="citizenship">{{ config('student_enrolment.step2.labels.citizenship.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.citizenship.indonesian') }})</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $data->applicant->citizenship == 'WNI' ? 'checked' : '' }} type="radio"
                                id="citizenshipWNI" name="citizenshipWNI" value="WNI">
                            <label for="citizenshipWNI">WNI</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $data->applicant->citizenship == 'WNA' ? 'checked' : '' }} type="radio"
                                id="citizenshipWNA" name="citizenshipWNA" value="WNA">
                            <label for="citizenshipWNA">WNA</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="height">{{ config('student_enrolment.step2.labels.height.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.height.indonesian') }})</span></label>
                    <input type="number" id="height" name="height"
                        value="{{ (float) $data->applicant->height }}">
                </div>

                <!-- Row 6 -->
                <div class="form-group">
                    <label for="weight">{{ config('student_enrolment.step2.labels.weight.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.weight.indonesian') }})</span></label>
                    <input type="number" id="weight" name="weight"
                        value="{{ (float) $data->applicant->weight }}">
                </div>

                <div class="form-group">
                    <label for="siblings">{{ config('student_enrolment.step2.labels.number_sibling.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.number_sibling.indonesian') }})</span></label>
                    <input type="number" id="siblings" name="siblings"
                        value="{{ $data->applicant->siblings_count }}">
                </div>

                <!-- Row 7 -->
                <div class="form-group">
                    <label for="birthOrder">{{ config('student_enrolment.step2.labels.order.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.order.indonesian') }})</span></label>
                    <input type="number" id="birthOrder" name="birthOrder"
                        value="{{ $data->applicant->birth_order }}">
                </div>

                <div class="form-group">
                    <label
                        for="primaryLanguage">{{ config('student_enrolment.step2.labels.primary_language.english') }}<span
                            class="label-english">({{ config('student_enrolment.step2.labels.primary_language.indonesian') }})</span></label>
                    <input type="text" id="primaryLanguage" name="primaryLanguage"
                        value="{{ $data->applicant->home_language }}">
                </div>

                <!-- Row 8 -->
                <div class="form-group">
                    <label for="otherLanguages">{{ config('student_enrolment.step2.labels.other_language.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.other_language.indonesian') }})</span></label>
                    <input type="text" id="otherLanguages" name="otherLanguages"
                        value="{{ $data->applicant->other_languages }}">
                </div>

                <!-- Full Width Fields -->
                <div class="form-group full-width">
                    <label for="fullAddress">{{ config('student_enrolment.step2.labels.address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.address.indonesian') }})</span></label>
                    <textarea id="fullAddress" name="fullAddress" rows="2">{{ $data->applicant->address }}</textarea>
                </div>

                <!-- Row 9 -->
                <div class="form-group">
                    <label for="postalCode">{{ config('student_enrolment.step2.labels.zipcode.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.zipcode.indonesian') }})</span></label>
                    <input type="text" id="postalCode" name="postalCode"
                        value="{{ $data->applicant->zipcode }}">
                </div>

                <div class="form-group">
                    <label for="homePhone">{{ config('student_enrolment.step2.labels.home_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.home_phone.indonesian') }})</span></label>
                    <input type="text" id="homePhone" name="homePhone"
                        value="{{ $data->applicant->home_phone ?? '-' }}">
                </div>

                <!-- Row 10 -->
                <div class="form-group">
                    <label for="parentMobile">{{ config('student_enrolment.step2.labels.parent_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.parent_phone.indonesian') }})</span></label>
                    <input type="text" id="parentMobile" name="parentMobile"
                        value="{{ $data->applicant->parent_phone ?? '-' }}">
                </div>

                <div class="form-group">
                    <label for="livingWith">{{ config('student_enrolment.step2.labels.living_with.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.living_with.indonesian') }})</span></label>
                    <input type="text" id="livingWith" name="livingWith"
                        value="{{ $data->applicant->living_with ?? '-' }}">
                </div>
                <div class="form-group">
                    <label for="livingWithOther"><span class="label-english">If the student does not live with
                            their parents, please let us know the details.</span></label>
                    <input type="text" id="livingWithOther" name="livingWithOther"
                        value="{{ $data->applicant->living_with_other ?? '-' }}">
                </div>

                <!-- Row 11 -->
                <div class="form-group">
                    <label for="distanceToSchool">{{ config('student_enrolment.step2.labels.distance.english') }}
                        <span
                            class="label-english">{{ config('student_enrolment.step2.labels.distance.indonesian') }}</span></label>
                    <input type="number" id="distanceToSchool" name="distanceToSchool"
                        value="{{ (float) $data->applicant->distance_km }}">
                </div>

                <div class="form-group">
                    <label for="previousSchool">{{ config('student_enrolment.step2.labels.prev_school.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.prev_school.indonesian') }})</span></label>
                    <input type="text" id="previousSchool" name="previousSchool"
                        value="{{ $data->applicant->previous_school ?? '-' }}">
                </div>

                <!-- Full Width Fields -->
                <div class="form-group full-width">
                    <label
                        for="previousSchoolAddress">{{ config('student_enrolment.step2.labels.prev_address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.prev_address.indonesian') }})</span></label>
                    <input type="text" id="previousSchoolAddress" name="previousSchoolAddress"
                        value="{{ $data->applicant->previous_school_address ?? '-' }}">
                </div>

                <!-- Row 12 -->
                <div class="form-group">
                    <label for="graduationYear">{{ config('student_enrolment.step2.labels.year_grad.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.year_grad.indonesian') }})</span></label>
                    <input type="number" id="graduationYear" name="graduationYear"
                        value="{{ $data->applicant->graduation_year ?? '-' }}">
                </div>

                <div class="form-group">
                    <label for="enrolmentLevel">{{ config('student_enrolment.step2.labels.level.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.level.indonesian') }})</span></label>
                    <input type="number" id="enrolmentLevel" name="enrolmentLevel"
                        value="{{ $data->level->name ?? '-' }}">
                </div>

                <!-- Row 13 -->
                <div class="form-group">
                    <label for="grade">{{ config('student_enrolment.step2.labels.grade.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.grade.indonesian') }})</span></label>
                    <input type="text" id="grade" name="grade" value="{{ $data->grade->name ?? '-' }}">
                </div>

                <div class="form-group">
                    <label for="academicYear">{{ config('student_enrolment.step2.labels.academic_year.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.academic_year.indonesian') }})</span></label>
                    <input type="text" id="academicYear" name="academicYear"
                        value="{{ $data->accademic_year ?? '-' }}">
                </div>

                <!-- Row 14 -->
                <div class="form-group full-width">
                    <label for="emergencyContact">{{ config('student_enrolment.step2.labels.emergency.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.emergency.indonesian') }})</span></label>
                    <input type="text" id="emergencyContact" name="emergencyContact"
                        value="{{ $data->applicant->emergency_contact_priority ?? '-' }}">
                </div>
                <div class="form-group full-width">
                    <label
                        for="emergencyContact">{{ config('student_enrolment.step2.labels.emergency_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.emergency_phone.indonesian') }})</span></label>
                    <input type="text" id="emergencyContact" name="emergencyContact"
                        value="{{ $data->applicant->emergency_contact_phone ?? '-' }}">
                </div>

                <!-- Conditional Questions -->
                <div class="form-group full-width">
                    <label>{{ config('student_enrolment.step2.labels.not_school.english') }} <span
                            class="label-english">({{ config('student_enrolment.step2.labels.not_school.indonesian') }})</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $data->applicant->ever_not_school == true ? 'checked' : '' }} type="radio"
                                id="notAttendingYes" name="notAttendingSchool" value="true">
                            <label for="notAttendingYes">Yes</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $data->applicant->ever_not_school == false ? 'checked' : '' }} type="radio"
                                id="notAttendingNo" name="notAttendingSchool" value="false">
                            <label for="notAttendingNo">No</label>
                        </div>
                    </div>
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="30%" valign="middle">
                            <div class="form-group">
                                <label>{{ config('student_enrolment.step2.labels.duration.english') }}</label>
                                <input type="text" id="notAttendingDuration" name="notAttendingDuration"
                                    value="{{ $data->applicant->not_school_duration ?? '-' }}">
                            </div>
                        </td>
                        <td width="70%" valign="middle">
                            <div class="form-group">
                                <label
                                    for="notAttendingReason">{{ config('student_enrolment.step2.labels.reason.english') }}</label>
                                <input type="text" id="notAttendingReason" name="notAttendingReason"
                                    value="{{ $data->applicant->not_school_reason ?? '-' }}">
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="form-group full-width">
                    <label>{{ config('student_enrolment.step2.labels.developmental.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.developmental.indonesian') }})</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $data->applicant->dev_check == true ? 'checked' : '' }} type="radio"
                                id="assessmentYes" name="psychologicalAssessment" value="true">
                            <label for="assessmentYes">Yes</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $data->applicant->dev_check == false ? 'checked' : '' }} type="radio"
                                id="assessmentNo" name="psychologicalAssessment" value="false">
                            <label for="assessmentNo">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>{{ config('student_enrolment.step2.labels.description.english') }}</label>
                    <textarea rows="2" readonly>{{ $data->applicant->dev_diagnosis ?? '-' }}</textarea>
                </div>

                <div class="form-group full-width">
                    <label>{{ config('student_enrolment.step2.labels.therapy.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step2.labels.therapy.indonesian') }})</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $data->applicant->therapy_history == true ? 'checked' : '' }} type="radio"
                                id="therapyYes" name="specialTherapy" value="true">
                            <label for="therapyYes">Yes</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $data->applicant->therapy_history == false ? 'checked' : '' }} type="radio"
                                id="therapyNo" name="specialTherapy" value="false">
                            <label for="therapyNo">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>{{ config('student_enrolment.step2.labels.type_therapy.english') }}</label>
                    <textarea rows="2" readonly>{{ $data->applicant->therapy_detail ?? '-' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Page Break for PDF -->
        <div class="page-break"></div>

        <!-- Father's Information Section -->
        <div class="section" id="section3">
            <h2 class="section-title">{{ config('student_enrolment.step3_4_5.title3') }}</h2>

            <div class="form-grid">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="fatherFullName">{{ config('student_enrolment.step3_4_5.labels.fullname.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.fullname.indonesian') }})</span></label>
                    <input type="text" id="fatherFullName" name="fatherFullName"
                        value="{{ $father->fullname ?? '-' }}" readonly>
                </div>

                <!-- Mobile Phone -->
                <div class="form-group">
                    <label for="fatherMobile">{{ config('student_enrolment.step3_4_5.labels.mobile_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.mobile_phone.indonesian') }})</span></label>
                    <input type="text" id="fatherMobile" name="fatherMobile"
                        value="{{ $father->phone ?? '-' }}" readonly>
                </div>

                <!-- Home Phone -->
                <div class="form-group">
                    <label
                        for="fatherHomePhone">{{ config('student_enrolment.step3_4_5.labels.home_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.home_phone.indonesian') }})</span></label>
                    <input type="text" id="fatherHomePhone" name="fatherHomePhone"
                        value="{{ $father->home_phone ?? '-' }}" readonly>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="fatherEmail">{{ config('student_enrolment.step3_4_5.labels.email.english') }} <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.email.indonesian') }})</span></label>
                    <input type="text" id="fatherEmail" name="fatherEmail" value="{{ $father->email ?? '-' }}"
                        readonly>
                </div>

                <!-- Place of Birth -->
                <div class="form-group">
                    <label
                        for="fatherPlaceOfBirth">{{ config('student_enrolment.step3_4_5.labels.place_birth.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.place_birth.indonesian') }})</span></label>
                    <input type="text" id="fatherPlaceOfBirth" name="fatherPlaceOfBirth"
                        value="{{ $father->birth_place ?? '-' }}" readonly>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label
                        for="fatherDateOfBirth">{{ config('student_enrolment.step3_4_5.labels.date_birth.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.date_birth.indonesian') }})</span></label>
                    <input type="date" id="fatherDateOfBirth" name="fatherDateOfBirth"
                        value="{{ date('d F Y', strtotime($father->birth_date)) }}" readonly>
                </div>

                <!-- ID Card / Passport -->
                <div class="form-group">
                    <label for="fatherIdCard">{{ config('student_enrolment.step3_4_5.labels.id_card.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.id_card.indonesian') }})</span></label>
                    <input type="text" id="fatherIdCard" name="fatherIdCard"
                        value="{{ $father->identity_number ?? '-' }}" readonly>
                </div>

                <!-- Religion -->
                <div class="form-group">
                    <label for="fatherReligion">{{ config('student_enrolment.step3_4_5.labels.religion.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.religion.indonesian') }})</span></label>
                    <input type="text" id="fatherReligion" name="fatherReligion"
                        value="{{ $father->religion ?? '-' }}" readonly>
                </div>

                <!-- Ethnicity -->
                <div class="form-group">
                    <label for="fatherEthnicity">{{ config('student_enrolment.step3_4_5.labels.ethnicity.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.ethnicity.indonesian') }})</span></label>
                    <input type="text" id="fatherEthnicity" name="fatherEthnicity"
                        value="{{ $father->ethnicity ?? '-' }}" readonly>
                </div>

                <!-- Full Address -->
                <div class="form-group full-width">
                    <label for="fatherAddress">{{ config('student_enrolment.step3_4_5.labels.address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.address.indonesian') }})</span></label>
                    <textarea id="fatherAddress" name="fatherAddress" rows="2" readonly>{{ $father->address ?? '-' }}</textarea>
                </div>

                <!-- Postal Code -->
                <div class="form-group">
                    <label for="fatherPostalCode">{{ config('student_enrolment.step3_4_5.labels.zipcode.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.zipcode.indonesian') }})</span></label>
                    <input type="text" id="fatherPostalCode" name="fatherPostalCode"
                        value="{{ $father->zipcode ?? '-' }}" readonly>
                </div>

                <!-- Education Level -->
                <div class="form-group">
                    <label for="fatherEducation">{{ config('student_enrolment.step3_4_5.labels.education.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.education.indonesian') }})</span></label>
                    <input type="text" id="fatherEducation" name="fatherEducation"
                        value="{{ $father->education ?? '-' }}" readonly>
                </div>

                <!-- Occupation -->
                <div class="form-group">
                    <label
                        for="fatherOccupation">{{ config('student_enrolment.step3_4_5.labels.occupation.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.occupation.indonesian') }})</span></label>
                    <input type="text" id="fatherOccupation" name="fatherOccupation"
                        value="{{ $father->occupation ?? '-' }}" readonly>
                </div>

                <!-- Institution Name -->
                <div class="form-group">
                    <label
                        for="fatherInstitution">{{ config('student_enrolment.step3_4_5.labels.institution.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.institution.indonesian') }})</span></label>
                    <input type="text" id="fatherInstitution" name="fatherInstitution"
                        value="{{ $father->company_name ?? '-' }}" readonly>
                </div>

                <!-- Institution Address -->
                <div class="form-group full-width">
                    <label
                        for="fatherInstitutionAddress">{{ config('student_enrolment.step3_4_5.labels.institution_address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.institution_address.indonesian') }})</span></label>
                    <textarea id="fatherInstitutionAddress" name="fatherInstitutionAddress" rows="2" readonly>{{ $father->company_address ?? '-' }}</textarea>
                </div>

                <!-- Monthly Income -->
                <div class="form-group">
                    <label for="fatherIncome">{{ config('student_enrolment.step3_4_5.labels.income.english') }} <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.income.indonesian') }})</span></label>
                    <input type="text" id="fatherIncome" name="fatherIncome"
                        value="{{ number_format($father->monthly_income, 0, '.', ',') }}" readonly>
                </div>

                <!-- Marital Status -->
                <div class="form-group">
                    <label
                        for="fatherMaritalStatus">{{ config('student_enrolment.step3_4_5.labels.status.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.status.indonesian') }})</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $father->marital_status == 'Married' ? 'checked' : '' }} type="radio"
                                id="fatherMaritalStatusMarried" name="fatherMaritalStatusMarried" value="Married">
                            <label for="fatherMaritalStatusMarried">Married</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $father->marital_status == 'Divorced' ? 'checked' : '' }} type="radio"
                                id="fatherMaritalStatusDivorced" name="fatherMaritalStatusDivorced" value="Divorced">
                            <label for="fatherMaritalStatusDivorced">Divorced</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mother's Information Section -->
        <div class="section" id="section4">
            <h2 class="section-title">{{ config('student_enrolment.step3_4_5.title4') }}</h2>

            <div class="form-grid">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="motherFullName">{{ config('student_enrolment.step3_4_5.labels.fullname.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.fullname.indonesian') }})</span></label>
                    <input type="text" id="motherFullName" name="motherFullName"
                        value="{{ $mother->fullname ?? '-' }}" readonly>
                </div>

                <!-- Mobile Phone -->
                <div class="form-group">
                    <label for="motherMobile">{{ config('student_enrolment.step3_4_5.labels.mobile_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.mobile_phone.indonesian') }})</span></label>
                    <input type="text" id="motherMobile" name="motherMobile"
                        value="{{ $mother->phone ?? '-' }}" readonly>
                </div>

                <!-- Home Phone -->
                <div class="form-group">
                    <label
                        for="motherHomePhone">{{ config('student_enrolment.step3_4_5.labels.home_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.home_phone.indonesian') }})</span></label>
                    <input type="text" id="motherHomePhone" name="motherHomePhone"
                        value="{{ $mother->home_phone ?? '-' }}" readonly>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="motherEmail">{{ config('student_enrolment.step3_4_5.labels.email.english') }} <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.email.indonesian') }})</span></label>
                    <input type="text" id="motherEmail" name="motherEmail" value="{{ $mother->email ?? '-' }}"
                        readonly>
                </div>

                <!-- Place of Birth -->
                <div class="form-group">
                    <label
                        for="motherPlaceOfBirth">{{ config('student_enrolment.step3_4_5.labels.place_birth.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.place_birth.indonesian') }})</span></label>
                    <input type="text" id="motherPlaceOfBirth" name="motherPlaceOfBirth"
                        value="{{ $mother->birth_place ?? '-' }}" readonly>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label
                        for="motherDateOfBirth">{{ config('student_enrolment.step3_4_5.labels.date_birth.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.date_birth.indonesian') }})</span></label>
                    <input type="date" id="motherDateOfBirth" name="motherDateOfBirth"
                        value="{{ date('d F Y', strtotime($mother->birth_date)) }}" readonly>
                </div>

                <!-- ID Card / Passport -->
                <div class="form-group">
                    <label for="motherIdCard">{{ config('student_enrolment.step3_4_5.labels.id_card.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.id_card.indonesian') }})</span></label>
                    <input type="text" id="motherIdCard" name="motherIdCard"
                        value="{{ $mother->identity_number ?? '-' }}" readonly>
                </div>

                <!-- Religion -->
                <div class="form-group">
                    <label for="motherReligion">{{ config('student_enrolment.step3_4_5.labels.religion.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.religion.indonesian') }})</span></label>
                    <input type="text" id="motherReligion" name="motherReligion"
                        value="{{ $mother->religion ?? '-' }}" readonly>
                </div>

                <!-- Ethnicity -->
                <div class="form-group">
                    <label for="motherEthnicity">{{ config('student_enrolment.step3_4_5.labels.ethnicity.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.ethnicity.indonesian') }})</span></label>
                    <input type="text" id="motherEthnicity" name="motherEthnicity"
                        value="{{ $mother->ethnicity ?? '-' }}" readonly>
                </div>

                <!-- Full Address -->
                <div class="form-group full-width">
                    <label for="motherAddress">{{ config('student_enrolment.step3_4_5.labels.address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.address.indonesian') }})</span></label>
                    <textarea id="motherAddress" name="motherAddress" rows="2" readonly>{{ $mother->address ?? '-' }}</textarea>
                </div>

                <!-- Postal Code -->
                <div class="form-group">
                    <label for="motherPostalCode">{{ config('student_enrolment.step3_4_5.labels.zipcode.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.zipcode.indonesian') }})</span></label>
                    <input type="text" id="motherPostalCode" name="motherPostalCode"
                        value="{{ $mother->zipcode ?? '-' }}" readonly>
                </div>

                <!-- Education Level -->
                <div class="form-group">
                    <label for="motherEducation">{{ config('student_enrolment.step3_4_5.labels.education.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.education.indonesian') }})</span></label>
                    <input type="text" id="motherEducation" name="motherEducation"
                        value="{{ $mother->education ?? '-' }}" readonly>
                </div>

                <!-- Occupation -->
                <div class="form-group">
                    <label
                        for="motherOccupation">{{ config('student_enrolment.step3_4_5.labels.occupation.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.occupation.indonesian') }})</span></label>
                    <input type="text" id="motherOccupation" name="motherOccupation"
                        value="{{ $mother->occupation ?? '-' }}" readonly>
                </div>

                <!-- Institution Name -->
                <div class="form-group">
                    <label
                        for="motherInstitution">{{ config('student_enrolment.step3_4_5.labels.institution.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.institution.indonesian') }})</span></label>
                    <input type="text" id="motherInstitution" name="motherInstitution"
                        value="{{ $mother->company_name ?? '-' }}" readonly>
                </div>

                <!-- Institution Address -->
                <div class="form-group full-width">
                    <label
                        for="motherInstitutionAddress">{{ config('student_enrolment.step3_4_5.labels.institution_address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.institution_address.indonesian') }})</span></label>
                    <textarea id="motherInstitutionAddress" name="motherInstitutionAddress" rows="2" readonly>{{ $mother->company_address ?? '-' }}</textarea>
                </div>

                <!-- Monthly Income -->
                <div class="form-group">
                    <label for="motherIncome">{{ config('student_enrolment.step3_4_5.labels.income.english') }} <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.income.indonesian') }})</span></label>
                    <input type="text" id="motherIncome" name="motherIncome"
                        value="{{ number_format($mother->monthly_income, 0, '.', ',') }}" readonly>
                </div>

                <!-- Marital Status -->
                <div class="form-group">
                    <label
                        for="motherMaritalStatus">{{ config('student_enrolment.step3_4_5.labels.status.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.status.indonesian') }})</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $mother->marital_status == 'Married' ? 'checked' : '' }} type="radio"
                                id="motherMaritalStatusMarried" name="motherMaritalStatusMarried" value="Married">
                            <label for="motherMaritalStatusMarried">Married</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $mother->marital_status == 'Divorced' ? 'checked' : '' }} type="radio"
                                id="motherMaritalStatusDivorced" name="motherMaritalStatusDivorced" value="Divorced">
                            <label for="motherMaritalStatusDivorced">Divorced</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guardian's Information Section (Optional) -->
        <div class="section" id="section5">
            <h2 class="section-title">{{ config('student_enrolment.step3_4_5.title5') }}</h2>
            <p style="color: #666; margin-bottom: 20px; font-style: italic;">Please fill in only if there is a guardian
                other than the biological parents <br><i><small>(Isi hanya jika ada wali selain orang tua
                        kandung)</small></i></p>

            <div class="form-grid">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="guardianFullName">{{ config('student_enrolment.step3_4_5.labels.fullname.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.fullname.indonesian') }})</span></label>
                    <input type="text" id="guardianFullName" name="guardianFullName"
                        value="{{ $guardian->fullname ?? '-' }}" readonly>
                </div>

                <!-- Mobile Phone -->
                <div class="form-group">
                    <label
                        for="guardianMobile">{{ config('student_enrolment.step3_4_5.labels.mobile_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.mobile_phone.indonesian') }})</span></label>
                    <input type="text" id="guardianMobile" name="guardianMobile"
                        value="{{ $guardian->phone ?? '-' }}" readonly>
                </div>

                <!-- Home Phone -->
                <div class="form-group">
                    <label
                        for="guardianHomePhone">{{ config('student_enrolment.step3_4_5.labels.home_phone.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.home_phone.indonesian') }})</span></label>
                    <input type="text" id="guardianHomePhone" name="guardianHomePhone"
                        value="{{ $guardian->home_phone ?? '-' }}" readonly>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="guardianEmail">{{ config('student_enrolment.step3_4_5.labels.email.english') }} <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.email.indonesian') }})</span></label>
                    <input type="text" id="guardianEmail" name="guardianEmail"
                        value="{{ $guardian->email ?? '-' }}" readonly>
                </div>

                <!-- Place of Birth -->
                <div class="form-group">
                    <label
                        for="guardianPlaceOfBirth">{{ config('student_enrolment.step3_4_5.labels.place_birth.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.place_birth.indonesian') }})</span></label>
                    <input type="text" id="guardianPlaceOfBirth" name="guardianPlaceOfBirth"
                        value="{{ $guardian->birth_place ?? '-' }}" readonly>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label
                        for="guardianDateOfBirth">{{ config('student_enrolment.step3_4_5.labels.date_birth.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.date_birth.indonesian') }})</span></label>
                    <input type="date" id="guardianDateOfBirth" name="guardianDateOfBirth"
                        value="{{ date('d F Y', strtotime($guardian->birth_date)) }}" readonly>
                </div>

                <!-- ID Card / Passport -->
                <div class="form-group">
                    <label for="guardianIdCard">{{ config('student_enrolment.step3_4_5.labels.id_card.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.id_card.indonesian') }})</span></label>
                    <input type="text" id="guardianIdCard" name="guardianIdCard"
                        value="{{ $guardian->identity_number ?? '-' }}" readonly>
                </div>

                <!-- Religion -->
                <div class="form-group">
                    <label for="guardianReligion">{{ config('student_enrolment.step3_4_5.labels.religion.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.religion.indonesian') }})</span></label>
                    <input type="text" id="guardianReligion" name="guardianReligion"
                        value="{{ $guardian->religion ?? '-' }}" readonly>
                </div>

                <!-- Ethnicity -->
                <div class="form-group">
                    <label
                        for="guardianEthnicity">{{ config('student_enrolment.step3_4_5.labels.ethnicity.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.ethnicity.indonesian') }})</span></label>
                    <input type="text" id="guardianEthnicity" name="guardianEthnicity"
                        value="{{ $guardian->ethnicity ?? '-' }}" readonly>
                </div>

                <!-- Full Address -->
                <div class="form-group full-width">
                    <label for="guardianAddress">{{ config('student_enrolment.step3_4_5.labels.address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.address.indonesian') }})</span></label>
                    <textarea id="guardianAddress" name="guardianAddress" rows="2" readonly>{{ $guardian->address ?? '-' }}</textarea>
                </div>

                <!-- Postal Code -->
                <div class="form-group">
                    <label
                        for="guardianPostalCode">{{ config('student_enrolment.step3_4_5.labels.zipcode.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.zipcode.indonesian') }})</span></label>
                    <input type="text" id="guardianPostalCode" name="guardianPostalCode"
                        value="{{ $guardian->zipcode ?? '-' }}" readonly>
                </div>

                <!-- Education Level -->
                <div class="form-group">
                    <label
                        for="guardianEducation">{{ config('student_enrolment.step3_4_5.labels.education.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.education.indonesian') }})</span></label>
                    <input type="text" id="guardianEducation" name="guardianEducation"
                        value="{{ $guardian->education ?? '-' }}" readonly>
                </div>

                <!-- Occupation -->
                <div class="form-group">
                    <label
                        for="guardianOccupation">{{ config('student_enrolment.step3_4_5.labels.occupation.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.occupation.indonesian') }})</span></label>
                    <input type="text" id="guardianOccupation" name="guardianOccupation"
                        value="{{ $guardian->occupation ?? '-' }}" readonly>
                </div>

                <!-- Institution Name -->
                <div class="form-group">
                    <label
                        for="guardianInstitution">{{ config('student_enrolment.step3_4_5.labels.institution.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.institution.indonesian') }})</span></label>
                    <input type="text" id="guardianInstitution" name="guardianInstitution"
                        value="{{ $guardian->company_name ?? '-' }}" readonly>
                </div>

                <!-- Institution Address -->
                <div class="form-group full-width">
                    <label
                        for="guardianInstitutionAddress">{{ config('student_enrolment.step3_4_5.labels.institution_address.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.institution_address.indonesian') }})</span></label>
                    <textarea id="guardianInstitutionAddress" name="guardianInstitutionAddress" rows="2" readonly>{{ $guardian->company_address ?? '-' }}</textarea>
                </div>

                <!-- Monthly Income -->
                <div class="form-group">
                    <label for="guardianIncome">{{ config('student_enrolment.step3_4_5.labels.income.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.income.indonesian') }})</span></label>
                    <input type="text" id="guardianIncome" name="guardianIncome"
                        value="{{ number_format($guardian->monthly_income, 0, '.', ',') }}" readonly>
                </div>

                <!-- Marital Status -->
                <div class="form-group">
                    <label
                        for="guardianMaritalStatus">{{ config('student_enrolment.step3_4_5.labels.status.english') }}
                        <span
                            class="label-english">({{ config('student_enrolment.step3_4_5.labels.status.indonesian') }})</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $guardian->marital_status == 'Married' ? 'checked' : '' }} type="radio"
                                id="guardianMaritalStatusMarried" name="guardianMaritalStatusMarried"
                                value="Married">
                            <label for="guardianMaritalStatusMarried">Married</label>
                        </div>
                        <div class="radio-option">
                            <input {{ $guardian->marital_status == 'Divorced' ? 'checked' : '' }} type="radio"
                                id="guardianMaritalStatusDivorced" name="guardianMaritalStatusDivorced"
                                value="Divorced">
                            <label for="guardianMaritalStatusDivorced">Divorced</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Break for PDF -->
        <div class="page-break"></div>

        <!-- Health Information Section -->
        <div class="section" id="section6">
            <h2 class="section-title">{{ config('student_enrolment.step6.title') }}</h2>

            <!-- BASIC IMMUNISATION Subsection -->
            <h3 class="subsection-title">{{ config('student_enrolment.step6.labels.immunisation.english') }}
                <span
                    class="label-english"><i>{{ config('student_enrolment.step6.labels.immunisation.indonesian') }}</i></span>
            </h3>

            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="middle">
                        <!-- BCG -->
                        <div class="form-group">
                            <label
                                for="immunizationBCG">{{ config('student_enrolment.step6.labels.bcg.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->bcg ? 'checked' : '' }} type="radio"
                                        id="bcgYes" disabled>
                                    <label for="bcgYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->bcg ? 'checked' : '' }} type="radio"
                                        id="bcgNo" disabled>
                                    <label for="bcgNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <!-- Hepatitis B -->
                        <div class="form-group">
                            <label
                                for="immunizationHepatitis">{{ config('student_enrolment.step6.labels.hepatitis.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->hepatitis ? 'checked' : '' }}
                                        type="radio" id="hepatitisYes" disabled>
                                    <label for="hepatitisYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->hepatitis ? 'checked' : '' }}
                                        type="radio" id="hepatitisNo" disabled>
                                    <label for="hepatitisNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="middle">
                        <!-- DTP (Diphtheria/Tetanus/Pertussis) -->
                        <div class="form-group">
                            <label
                                for="immunizationDTP">{{ config('student_enrolment.step6.labels.diphtheria.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->dtp ? 'checked' : '' }} type="radio"
                                        id="dtpYes" disabled>
                                    <label for="dtpYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->dtp ? 'checked' : '' }} type="radio"
                                        id="dtpNo" disabled>
                                    <label for="dtpNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <!-- Polio -->
                        <div class="form-group">
                            <label
                                for="immunizationPolio">{{ config('student_enrolment.step6.labels.polio.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->polio ? 'checked' : '' }}
                                        type="radio" id="polioYes" disabled>
                                    <label for="polioYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->polio ? 'checked' : '' }}
                                        type="radio" id="polioNo" disabled>
                                    <label for="polioNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="middle">
                        <!-- Measles -->
                        <div class="form-group">
                            <label
                                for="immunizationMeasles">{{ config('student_enrolment.step6.labels.measles.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->measles ? 'checked' : '' }}
                                        type="radio" id="measlesYes" disabled>
                                    <label for="measlesYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->measles ? 'checked' : '' }}
                                        type="radio" id="measlesNo" disabled>
                                    <label for="measlesNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">

                        <!-- Hepatitis A -->
                        <div class="form-group">
                            <label
                                for="immunizationHepatitisA">{{ config('student_enrolment.step6.labels.hepatitis_a.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->hepatitis_a ? 'checked' : '' }}
                                        type="radio" id="hepatitisAYes" disabled>
                                    <label for="hepatitisAYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->hepatitis_a ? 'checked' : '' }}
                                        type="radio" id="hepatitisANo" disabled>
                                    <label for="hepatitisANo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="middle">
                        <!-- MMR -->
                        <div class="form-group">
                            <label
                                for="immunizationMMR">{{ config('student_enrolment.step6.labels.mmr.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->mmr ? 'checked' : '' }} type="radio"
                                        id="mmrYes" disabled>
                                    <label for="mmrYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->mmr ? 'checked' : '' }} type="radio"
                                        id="mmrNo" disabled>
                                    <label for="mmrNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <!-- Influenza -->
                        <div class="form-group">
                            <label
                                for="immunizationInfluenza">{{ config('student_enrolment.step6.labels.influenza.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->immunization->influenza ? 'checked' : '' }}
                                        type="radio" id="influenzaYes" disabled>
                                    <label for="influenzaYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->immunization->influenza ? 'checked' : '' }}
                                        type="radio" id="influenzaNo" disabled>
                                    <label for="influenzaNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- MEDICAL RECORDS Subsection -->
            <h3 class="subsection-title">{{ config('student_enrolment.step6.labels.medical.english') }}
                <span
                    class="label-english"><i>({{ config('student_enrolment.step6.labels.medical.indonesian') }})</i></span>
            </h3>

            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="middle">
                        <!-- Food Allergy -->
                        <div class="form-group">
                            <label
                                for="foodAllergy">{{ config('student_enrolment.step6.labels.food_allergy.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->health->food_allergy ? 'checked' : '' }}
                                        type="radio" id="foodAllergyYes" disabled>
                                    <label for="foodAllergyYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->health->food_allergy ? 'checked' : '' }}
                                        type="radio" id="foodAllergyNo" disabled>
                                    <label for="foodAllergyNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <!-- Medication Allergy -->
                        <div class="form-group">
                            <label
                                for="drugAllergy">{{ config('student_enrolment.step6.labels.medication_allergy.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->health->drug_allergy ? 'checked' : '' }}
                                        type="radio" id="drugAllergyYes" disabled>
                                    <label for="drugAllergyYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->health->drug_allergy ? 'checked' : '' }}
                                        type="radio" id="drugAllergyNo" disabled>
                                    <label for="drugAllergyNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="middle">
                        <div class="form-group full-width">
                            <label>{{ config('student_enrolment.step6.labels.food_allergy_explanation.english') }} :
                                {{ $data->applicant->health->food_allergy_note ?? '-' }}</label>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <div class="form-group full-width">
                            <label>{{ config('student_enrolment.step6.labels.medication_allergy_explanation.english') }}
                                : {{ $data->applicant->health->drug_allergy_note ?? '-' }}</label>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="form-grid">
                <!-- Blood Type -->
                <div class="form-group">
                    <label>{{ config('student_enrolment.step6.labels.bloody_type.english') }}</label>
                    <input type="text" value="{{ $data->applicant->health->blood_type ?? '-' }}">
                </div>
            </div>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="middle">
                        <!-- Wears Glasses -->
                        <div class="form-group">
                            <label
                                for="usesGlasses">{{ config('student_enrolment.step6.labels.wears_glasses.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->health->uses_glasses ? 'checked' : '' }}
                                        type="radio" id="usesGlassesYes" disabled>
                                    <label for="usesGlassesYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->health->uses_glasses ? 'checked' : '' }}
                                        type="radio" id="usesGlassesNo" disabled>
                                    <label for="usesGlassesNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <!-- Uses Hearing Aid -->
                        <div class="form-group">
                            <label
                                for="usesHearingAid">{{ config('student_enrolment.step6.labels.hearing_aid.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->health->uses_hearing_aid ? 'checked' : '' }}
                                        type="radio" id="usesHearingAidYes" disabled>
                                    <label for="usesHearingAidYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->health->uses_hearing_aid ? 'checked' : '' }}
                                        type="radio" id="usesHearingAidNo" disabled>
                                    <label for="usesHearingAidNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- PREGNANCY HISTORY Subsection -->
            <h3 class="subsection-title">{{ config('student_enrolment.step6.labels.pregnancy_history.english') }}
            </h3>

            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="middle">
                        <div class="form-group">
                            <label
                                for="usesHearingAid">{{ config('student_enrolment.step6.labels.medication.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input
                                        {{ $data->applicant->pregnancyHistory->medication_during_pregnancy ? 'checked' : '' }}
                                        type="radio" id="usesHearingAidYes" disabled>
                                    <label for="usesHearingAidYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input
                                        {{ !$data->applicant->pregnancyHistory->medication_during_pregnancy ? 'checked' : '' }}
                                        type="radio" id="usesHearingAidNo" disabled>
                                    <label for="usesHearingAidNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <div class="form-group">
                            <label
                                for="usesHearingAid">{{ config('student_enrolment.step6.labels.illnes.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input
                                        {{ $data->applicant->pregnancyHistory->illness_during_pregnancy ? 'checked' : '' }}
                                        type="radio" id="usesHearingAidYes" disabled>
                                    <label for="usesHearingAidYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input
                                        {{ !$data->applicant->pregnancyHistory->illness_during_pregnancy ? 'checked' : '' }}
                                        type="radio" id="usesHearingAidNo" disabled>
                                    <label for="usesHearingAidNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="middle">
                        <div class="form-group full-width">
                            <label>{{ config('student_enrolment.step6.labels.medication_explanation.english') }} :
                                {{ $data->applicant->pregnancyHistory->medication_note ?? '-' }}</label>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <div class="form-group full-width">
                            <label>{{ config('student_enrolment.step6.labels.medication_allergy_explanation.english') }}
                                : {{ $data->applicant->pregnancyHistory->illness_note ?? '-' }}</label>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="form-grid">
                <!-- Gestational Age at Birth -->
                <div class="form-group">
                    <label
                        for="gestationalAge">{{ config('student_enrolment.step6.labels.age_birth.english') }}</label>
                    <input type="text" id="gestationalAge"
                        value="{{ $data->applicant->pregnancyHistory->gestational_age ?? '-' }}" readonly>
                </div>

                <!-- Mode of Delivery -->
                <div class="form-group">
                    <label
                        for="deliveryMethod">{{ config('student_enrolment.step6.labels.delivery.english') }}</label>
                    <input type="text" id="deliveryMethod"
                        value="{{ $data->applicant->pregnancyHistory->delivery_method ?? '-' }}" readonly>
                </div>
            </div>

            <!-- BIRTH CONDITIONS Subsection -->
            <h3 class="subsection-title">{{ config('student_enrolment.step6.labels.birth_condition.english') }}</h3>

            <div class="form-grid">
                <!-- Birth Weight -->
                <div class="form-group">
                    <label for="birthWeight">{{ config('student_enrolment.step6.labels.weight.english') }}</label>
                    <input type="text" id="birthWeight"
                        value="{{ (float) $data->applicant->pregnancyHistory->birth_weight ?? '-' }}" readonly>
                </div>

                <!-- Birth Length -->
                <div class="form-group">
                    <label for="birthHeight">{{ config('student_enrolment.step6.labels.length.english') }}</label>
                    <input type="text" id="birthHeight"
                        value="{{ (float) $data->applicant->pregnancyHistory->birth_length ?? '-' }}" readonly>
                </div>

                <!-- Age Started Walking -->
                <div class="form-group">
                    <label
                        for="walkedAtAge">{{ config('student_enrolment.step6.labels.age_walking.english') }}</label>
                    <input type="text" id="walkedAtAge"
                        value="{{ $data->applicant->pregnancyHistory->walk_age_month ?? '-' }}" readonly>
                </div>

                <!-- Age Started Speaking -->
                <div class="form-group">
                    <label
                        for="spokeAtAge">{{ config('student_enrolment.step6.labels.age_speaking.english') }}</label>
                    <input type="text" id="spokeAtAge"
                        value="{{ $data->applicant->pregnancyHistory->talk_age_month ?? '-' }}" readonly>
                </div>

                <!-- Currently Taking Medication -->
                <div class="form-group">
                    <label
                        for="routineMedication">{{ config('student_enrolment.step6.labels.taking_medication.english') }}</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $data->applicant->health->routine_medication ? 'checked' : '' }} type="radio"
                                id="routineMedicationYes" disabled>
                            <label for="routineMedicationYes">Yes</label>
                        </div>
                        <div class="radio-option">
                            <input {{ !$data->applicant->health->routine_medication ? 'checked' : '' }}
                                type="radio" id="routineMedicationNo" disabled>
                            <label for="routineMedicationNo">No</label>
                        </div>
                    </div>
                </div>

                <!-- Routine Medication Details -->
                <div class="form-group full-width">
                    <label
                        for="routineMedicationExplanation">{{ config('student_enrolment.step6.labels.taking_medication_explanation.english') }}</label>
                    <textarea id="routineMedicationExplanation" rows="2" readonly>{{ $data->applicant->health->routine_medication_explanation ?? '-' }}</textarea>
                </div>
            </div>

            <!-- PREVIOUS MEDICAL HISTORY Subsection -->
            <h3 class="subsection-title">{{ config('student_enrolment.step6.labels.previous_medical.english') }}
            </h3>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="middle">
                        <!-- Has Student Undergone Surgery -->
                        <div class="form-group">
                            <label
                                for="surgeryHistory">{{ config('student_enrolment.step6.labels.surgery.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->medicalHistory->surgery_history ? 'checked' : '' }}
                                        type="radio" id="surgeryHistoryYes" disabled>
                                    <label for="surgeryHistoryYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->medicalHistory->surgery_history ? 'checked' : '' }}
                                        type="radio" id="surgeryHistoryNo" disabled>
                                    <label for="surgeryHistoryNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <label>{{ config('student_enrolment.step6.labels.surgery_explanation.english') }} :
                            {{ $data->applicant->medicalHistory->surgery_note ?? '-' }}</label>
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="middle">
                        <!-- Has Student Been Hospitalized -->
                        <div class="form-group">
                            <label>{{ config('student_enrolment.step6.labels.hospitalized.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input
                                        {{ $data->applicant->medicalHistory->hospitalization_history ? 'checked' : '' }}
                                        type="radio" id="hospitalizationHistoryYes" disabled>
                                    <label for="hospitalizationHistoryYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input
                                        {{ !$data->applicant->medicalHistory->hospitalization_history ? 'checked' : '' }}
                                        type="radio" id="hospitalizationHistoryNo" disabled>
                                    <label for="hospitalizationHistoryNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <label>{{ config('student_enrolment.step6.labels.hospitalized_explanation.english') }} :
                            {{ $data->applicant->medicalHistory->hospitalization_note ?? '-' }}</label>
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="middle">
                        <!-- Has Student Had Seizures -->
                        <div class="form-group">
                            <label
                                for="seizureHistory">{{ config('student_enrolment.step6.labels.seizures.english') }}</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input {{ $data->applicant->medicalHistory->seizure_history ? 'checked' : '' }}
                                        type="radio" id="seizureHistoryYes" disabled>
                                    <label for="seizureHistoryYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input {{ !$data->applicant->medicalHistory->seizure_history ? 'checked' : '' }}
                                        type="radio" id="seizureHistoryNo" disabled>
                                    <label for="seizureHistoryNo">No</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td width="50%" valign="middle">
                        <label>{{ config('student_enrolment.step6.labels.seizures_explanation.english') }} :
                            {{ $data->applicant->medicalHistory->seizure_note ?? '-' }}</label>
                    </td>
                </tr>
            </table>

            <div class="form-grid">
                <!-- Has Student Had Accidents or Injuries -->
                <div class="form-group">
                    <label
                        for="accidentHistory">{{ config('student_enrolment.step6.labels.accidents.english') }}</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input {{ $data->applicant->medicalHistory->accident_history ? 'checked' : '' }}
                                type="radio" id="accidentHistoryYes" disabled>
                            <label for="accidentHistoryYes">Yes</label>
                        </div>
                        <div class="radio-option">
                            <input {{ !$data->applicant->medicalHistory->accident_history ? 'checked' : '' }}
                                type="radio" id="accidentHistoryNo" disabled>
                            <label for="accidentHistoryNo">No</label>
                        </div>
                    </div>
                </div>

                <!-- Accident Details -->
                <div class="form-group full-width">
                    <label
                        for="accidentNote">{{ config('student_enrolment.step6.labels.accidents_explanation.english') }}</label>
                    <textarea id="accidentNote" rows="2" readonly>{{ $data->applicant->medicalHistory->accident_note ?? '-' }}</textarea>
                </div>

                <!-- Family Disease History -->
                <div class="form-group full-width">
                    <label
                        for="familyDiseaseHistory">{{ config('student_enrolment.step6.labels.family_disease.english') }}</label>
                    <textarea id="familyDiseaseHistory" rows="3" readonly>{{ $data->applicant->health->family_disease_history ?? '-' }}</textarea>
                </div>

                <!-- Referral Health Facility -->
                <div class="form-group full-width">
                    <label
                        for="referralFacility">{{ config('student_enrolment.step6.labels.healthy_facility.english') }}</label>
                    <textarea id="referralFacility" rows="2" readonly>{{ $data->applicant->health->referral_health_facility ?? '-' }}</textarea>
                </div>

                <!-- Emergency Contact Information -->
                <div class="form-group full-width">
                    <label
                        for="emergencyContactInfo">{{ config('student_enrolment.step6.labels.emergency.english') }}</label>
                    <textarea id="emergencyContactInfo" rows="2" readonly>{{ $data->applicant->health->emergency_contact ?? '-' }}</textarea>
                </div>

                <!-- Parent Declaration Checkbox -->
                <div class="form-group full-width"
                    style="margin-top: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="parentDeclaration"
                            {{ $data->applicant->declaration->agreed == true ? 'checked' : '' }}>
                        <label class="form-check-label" style="text-align: justify; margin-left: 10px;"
                            for="parentDeclaration">
                            {!! config('student_enrolment.step6.labels.agreement.content') !!}
                        </label>
                    </div>

                    <!-- Agreed At Date -->
                    <div style="margin-top: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Agreed at</label>
                        <input type="date"
                            value="{{ date('d F Y H:i', strtotime($data->applicant->declaration->agreed_at)) }}"
                            readonly style="width: 200px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Mutiara Harapan Islamic School - Student and Parent Information Form</p>
            <p>This form is part of the official enrolment process</p>
        </div>
    </div>

    <script>
        // Show/hide conditional fields based on radio button selection
        document.addEventListener('DOMContentLoaded', function() {
            // Function to toggle conditional fields
            function toggleConditionalField(radioName, fieldId) {
                const radios = document.querySelectorAll(`input[name="${radioName}"]`);
                const field = document.getElementById(fieldId);

                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'true') {
                            field.style.display = 'block';
                        } else {
                            field.style.display = 'none';
                            // Clear fields when hidden
                            const inputs = field.querySelectorAll('input, textarea, select');
                            inputs.forEach(input => input.value = '');
                        }
                    });
                });
            }

            // Living with "others" conditional field
            const livingWithSelect = document.getElementById('livingWith');
            const livingWithOthersField = document.getElementById('livingWithOthers');

            livingWithSelect.addEventListener('change', function() {
                if (this.value === 'others') {
                    livingWithOthersField.style.display = 'block';
                } else {
                    livingWithOthersField.style.display = 'none';
                    document.getElementById('livingWithSpecify').value = '';
                }
            });

            // Toggle conditional fields for yes/no questions
            toggleConditionalField('notAttendingSchool', 'notAttendingDetails');
            toggleConditionalField('psychologicalAssessment', 'assessmentDetails');
            toggleConditionalField('specialTherapy', 'therapyDetails');

            // Set current academic year as default
            const currentYear = new Date().getFullYear();
            document.getElementById('academicYear').value = `${currentYear}/${currentYear + 1}`;

            // Auto-format phone numbers
            const phoneInputs = document.querySelectorAll('input[name="parentMobile"], input[name="homePhone"]');
            phoneInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 3 && value.length <= 7) {
                        value = value.replace(/(\d{3})(\d+)/, '$1-$2');
                    } else if (value.length > 7) {
                        value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1-$2-$3');
                    }
                    e.target.value = value;
                });
            });

            // Set today's date as default for date of birth
            const today = new Date();
            const fifteenYearsAgo = new Date(today.getFullYear() - 15, today.getMonth(), today.getDate());
            document.getElementById('birthDate').valueAsDate = fifteenYearsAgo;

            // Form validation before printing
            document.querySelector('.print-btn').addEventListener('click', function() {
                // Check if all required fields are filled (you can customize this)
                const requiredFields = document.querySelectorAll(
                    'input[required], select[required], textarea[required]');
                let allFilled = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        allFilled = false;
                        field.style.borderColor = '#ff0000';
                    } else {
                        field.style.borderColor = '#d0b7b7';
                    }
                });

                if (!allFilled) {
                    alert('Please fill in all required fields before printing.');
                    return;
                }

                // Proceed with printing
                window.print();
            });
        });
    </script>
</body>

</html>
