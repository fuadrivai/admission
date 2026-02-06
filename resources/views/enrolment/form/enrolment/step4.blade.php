<div>
    <h2 class="section-title">{{ config('student_enrolment.step3_4_5.title4') }}</h2>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="motherFullName"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.fullname.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.fullname.indonesian') }}</small></i>
            <input type="hidden" class="form-control" id="motherId">
            <input type="text" class="form-control" id="motherFullName" required>
            <div class="error-message" id="motherFullName-error">
                Please enter the mother’s full name
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherMobile"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.mobile_phone.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.mobile_phone.indonesian') }}</small></i>
            <input type="tel" class="form-control" id="motherMobile" required>
            <div class="error-message" id="motherMobile-error">
                Please enter the mother’s mobile phone number
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherHomePhone"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.home_phone.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.home_phone.indonesian') }}</small></i>
            <input type="tel" class="form-control" id="motherHomePhone">
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherEmail"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.email.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.email.indonesian') }}</small></i>
            <input type="email" class="form-control" id="motherEmail" required>
            <div class="error-message" id="motherEmail-error">
                Please enter a valid email address
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherPlaceOfBirth"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.place_birth.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.place_birth.indonesian') }}</small></i>
            <input type="text" class="form-control" id="motherPlaceOfBirth" required>
            <div class="error-message" id="motherPlaceOfBirth-error">
                Please enter the mother’s place of birth
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherDateOfBirth"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.date_birth.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.date_birth.indonesian') }}</small></i>
            <input type="text" class="form-control date-picker" readonly id="motherDateOfBirth" required>
            <div class="error-message" id="motherDateOfBirth-error">
                Please enter the mother’s date of birth
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherIdCard"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.id_card.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.id_card.indonesian') }}</small></i>
            <input type="text" class="form-control" id="motherIdCard" required>
            <div class="error-message" id="motherIdCard-error">
                Please enter the mother’s ID card or passport number
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherReligion"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.religion.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.religion.indonesian') }}</small></i>
            <input type="text" class="form-control" id="motherReligion" required>
            <div class="error-message" id="motherReligion-error">
                Please enter the mother’s religion
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherEthnicity"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.ethnicity.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.ethnicity.indonesian') }}</small></i>
            <input type="text" class="form-control" id="motherEthnicity" required>
            <div class="error-message" id="motherEthnicity-error">
                Please enter the mother’s ethnicity
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <label for="motherAddress"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.address.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.address.indonesian') }}</small></i>
            <textarea class="form-control" id="motherAddress" rows="3" required></textarea>
            <div class="error-message" id="motherAddress-error">
                Please enter the mother’s full address
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherPostalCode"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.zipcode.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.zipcode.indonesian') }}</small></i>
            <input type="number" class="form-control" id="motherPostalCode" required>
            <div class="error-message" id="motherPostalCode-error">
                Please enter the mother’s postal code
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherEducation"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.education.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.education.indonesian') }}</small></i>
            <select class="form-select education" id="motherEducation" required>
                <option value="" selected disabled>Select education level</option>
            </select>
            <div class="error-message" id="motherEducation-error">
                Please select the mother’s education level
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherOccupation"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.occupation.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.occupation.indonesian') }}</small></i>
            <select class="form-select job" id="motherOccupation" required>
                <option value="" selected disabled>Select occupation</option>
            </select>
            <div class="error-message" id="motherOccupation-error">
                Please select the mother’s occupation
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="motherInstitution"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.institution.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.institution.indonesian') }}</small></i>
            <input type="text" class="form-control" id="motherInstitution" required>
            <div class="error-message" id="motherInstitution-error">
                Please enter the mother’s institution name
            </div>
        </div>

        <div class="col-12 mb-3">
            <label for="motherInstitutionAddress"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.institution_address.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.institution_address.indonesian') }}</small></i>
            <textarea class="form-control" id="motherInstitutionAddress" rows="2" required></textarea>
            <div class="error-message" id="motherInstitutionAddress-error">
                Please enter the mother’s institution address
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="motherIncome"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.income.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.income.indonesian') }}</small></i>
            <input type="text" class="form-control number2" id="motherIncome" required>
            <div class="error-message" id="motherIncome-error">
                Please enter the mother’s monthly income
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.status.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.status.indonesian') }}</small></i>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="motherMaritalStatus" id="motherMarried"
                        value="Married" required>
                    <label class="form-check-label" for="motherMarried">Married</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="motherMaritalStatus" id="motherDivorced"
                        value="Divorced">
                    <label class="form-check-label" for="motherDivorced">Divorced</label>
                </div>
            </div>
            <div class="error-message" id="motherMaritalStatus-error">
                Please select the mother’s marital status
            </div>
        </div>
    </div>
</div>
