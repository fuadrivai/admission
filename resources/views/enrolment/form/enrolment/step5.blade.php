<div>
    <h2 class="section-title">{{ config('student_enrolment.step3_4_5.title5') }}</h2>
    <p class="text-muted mb-4">
        Please fill in only if there is a guardian other than the biological parents
        <br><i><small>Isi hanya jika ada wali selain orang tua kandung</small></i>
    </p>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="guardianFullName"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.fullname.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.fullname.indonesian') }}</small></i>
            <input type="hidden" class="form-control" id="guardianId">
            <input type="text" class="form-control" id="guardianFullName">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianMobile"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.mobile_phone.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.mobile_phone.indonesian') }}</small></i>
            <input type="tel" class="form-control" id="guardianMobile">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianHomePhone"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.home_phone.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.home_phone.indonesian') }}</small></i>
            <input type="tel" class="form-control" id="guardianHomePhone">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianEmail"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.email.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.email.indonesian') }}</small></i>
            <input type="email" class="form-control" id="guardianEmail">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianPlaceOfBirth"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.place_birth.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.place_birth.indonesian') }}</small></i>
            <input type="text" class="form-control" id="guardianPlaceOfBirth">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianDateOfBirth"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.date_birth.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.date_birth.indonesian') }}</small></i>
            <input type="text" class="form-control date-picker" readonly id="guardianDateOfBirth">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianIdCard"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.id_card.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.id_card.indonesian') }}</small></i>
            <input type="text" class="form-control" id="guardianIdCard">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianReligion"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.religion.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.religion.indonesian') }}</small></i>
            <input type="text" class="form-control" id="guardianReligion">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianEthnicity"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.ethnicity.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.ethnicity.indonesian') }}</small></i>
            <input type="text" class="form-control" id="guardianEthnicity">
        </div>

        <div class="col-md-8 mb-3">
            <label for="guardianAddress"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.address.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.address.indonesian') }}</small></i>
            <textarea class="form-control" id="guardianAddress" rows="3"></textarea>
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianPostalCode"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.zipcode.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.zipcode.indonesian') }}</small></i>
            <input type="number" class="form-control" id="guardianPostalCode">
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianEducation"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.education.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.education.indonesian') }}</small></i>
            <select class="form-select education" id="guardianEducation">
                <option value="" selected>Select education level (optional)</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianOccupation"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.occupation.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.occupation.indonesian') }}</small></i>
            <select class="form-select job" id="guardianOccupation">
                <option value="" selected>Select occupation (optional)</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label for="guardianInstitution"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.institution.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.institution.indonesian') }}</small></i>
            <input type="text" class="form-control" id="guardianInstitution">
        </div>

        <div class="col-md-12 mb-3">
            <label for="guardianInstitutionAddress"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.institution_address.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.institution_address.indonesian') }}</small></i>
            <textarea class="form-control" id="guardianInstitutionAddress" rows="2"></textarea>
        </div>

        <div class="col-md-6 mb-3">
            <label for="guardianIncome"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.income.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.income.indonesian') }}</small></i>
            <input type="text" class="number2 form-control" id="guardianIncome">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">{{ config('student_enrolment.step3_4_5.labels.status.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.status.indonesian') }}</small></i>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="guardianMaritalStatus" id="guardianMarried"
                        value="Married">
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
