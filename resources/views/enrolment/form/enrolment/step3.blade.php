<div>
    <h2 class="section-title">{{ config('student_enrolment.step3_4_5.title3') }}</h2>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="fatherFullName"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.fullname.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.fullname.indonesian') }}</small></i>
            <input type="hidden" class="form-control" id="fatherId">
            <input type="text" class="form-control" id="fatherFullName" required>
            <div class="error-message" id="fatherFullName-error">
                Please enter the father’s full name
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherMobile"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.mobile_phone.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.mobile_phone.indonesian') }}</small></i>
            <input type="tel" class="form-control" id="fatherMobile" required>
            <div class="error-message" id="fatherMobile-error">
                Please enter the father’s mobile phone number
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherHomePhone"
                class="form-label">{{ config('student_enrolment.step3_4_5.labels.home_phone.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.home_phone.indonesian') }}</small></i>
            <input type="tel" class="form-control" id="fatherHomePhone">
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherEmail"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.email.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.email.indonesian') }}</small></i>
            <input type="email" class="form-control" id="fatherEmail" required>
            <div class="error-message" id="fatherEmail-error">
                Please enter a valid email address
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherPlaceOfBirth"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.place_birth.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.place_birth.indonesian') }}</small></i>
            <input type="text" class="form-control" id="fatherPlaceOfBirth" required>
            <div class="error-message" id="fatherPlaceOfBirth-error">
                Please enter the father’s place of birth
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherDateOfBirth"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.date_birth.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.date_birth.indonesian') }}</small></i>
            <input type="text" class="form-control date-picker" readonly id="fatherDateOfBirth" required>
            <div class="error-message" id="fatherDateOfBirth-error">
                Please enter the father’s date of birth
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherIdCard"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.id_card.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.id_card.indonesian') }}</small></i>
            <input type="text" class="form-control" id="fatherIdCard" required>
            <div class="error-message" id="fatherIdCard-error">
                Please enter the father’s ID card or passport number
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherReligion"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.religion.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.religion.indonesian') }}</small></i>
            <input type="text" class="form-control" id="fatherReligion" required>
            <div class="error-message" id="fatherReligion-error">
                Please select the father’s religion
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherEthnicity"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.ethnicity.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.ethnicity.indonesian') }}</small></i>
            <input type="text" class="form-control" id="fatherEthnicity" required>
            <div class="error-message" id="fatherEthnicity-error">
                Please enter the father’s ethnicity
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <label for="fatherAddress"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.address.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.address.indonesian') }}</small></i>
            <textarea class="form-control" id="fatherAddress" rows="3" required></textarea>
            <div class="error-message" id="fatherAddress-error">
                Please enter the father’s full address
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherPostalCode"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.zipcode.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.zipcode.indonesian') }}</small></i>
            <input type="number" class="form-control" id="fatherPostalCode" required>
            <div class="error-message" id="fatherPostalCode-error">
                Please enter the father’s postal code
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherEducation"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.education.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.education.indonesian') }}</small></i>
            <select class="form-select education" id="fatherEducation" required>
                <option value="" selected disabled>Select education level</option>
            </select>
            <div class="error-message" id="fatherEducation-error">
                Please select the father’s education level
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherOccupation"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.occupation.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.occupation.indonesian') }}</small></i>
            <select class="form-select job" id="fatherOccupation" required>
                <option value="" selected disabled>Select occupation</option>
            </select>
            <div class="error-message" id="fatherOccupation-error">
                Please select the father’s occupation
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="fatherInstitution"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.institution.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.institution.indonesian') }}</small></i>
            <input type="text" class="form-control" id="fatherInstitution" required>
            <div class="error-message" id="fatherInstitution-error">
                Please enter the father’s institution name
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <label for="fatherInstitutionAddress"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.institution_address.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.institution_address.indonesian') }}</small></i>
            <textarea class="form-control" id="fatherInstitutionAddress" rows="2" required></textarea>
            <div class="error-message" id="fatherInstitutionAddress-error">
                Please enter the father’s institution address
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="fatherIncome"
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.income.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.income.indonesian') }}</small></i>
            <input type="text" class="form-control number2" id="fatherIncome" required>
            <div class="error-message" id="fatherIncome-error">
                Please enter the father’s monthly income
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label
                class="form-label required">{{ config('student_enrolment.step3_4_5.labels.status.english') }}</label>
            <br><i><small>{{ config('student_enrolment.step3_4_5.labels.status.indonesian') }}</small></i>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="fatherMaritalStatus" id="fatherMarried"
                        value="Married" required>
                    <label class="form-check-label" for="fatherMarried">Married</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="fatherMaritalStatus" id="fatherDivorced"
                        value="Divorced">
                    <label class="form-check-label" for="fatherDivorced">Divorced</label>
                </div>
            </div>
            <div class="error-message" id="fatherMaritalStatus-error">
                Please select the father’s marital status
            </div>
        </div>
    </div>
</div>
