<div>
    <h2 class="section-title">{{ config('student_enrolment.step2.title') }}</h2>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="studentFullName"
                class="form-label required">{{ config('student_enrolment.step2.labels.fullname.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.fullname.indonesian') }}</i></small>
            <input type="text" class="form-control" id="studentFullName" required>
            <div class="error-message" id="studentFullName-error">Please enter the student's full name</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="studentNickname"
                class="form-label required">{{ config('student_enrolment.step2.labels.nickname.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.nickname.indonesian') }}</i></small>
            <input type="text" class="form-control" id="studentNickname" required>
            <div class="error-message" id="studentNickname-error">Please enter the student's nickname</div>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label required">{{ config('student_enrolment.step2.labels.gender.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.gender.indonesian') }}</i></small>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="studentGender" id="studentGenderFemale"
                        value="female" required>
                    <label class="form-check-label" for="studentGenderFemale">Female</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="studentGender"
                        id="studentGenderMale"value="male">
                    <label class="form-check-label" for="studentGenderMale">Male</label>
                </div>
            </div>
            <div class="error-message" id="studentGender-error">Please select gender</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="placeOfBirth"
                class="form-label required">{{ config('student_enrolment.step2.labels.place_birth.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.place_birth.indonesian') }}</i></small>
            <input type="text" class="form-control" id="placeOfBirth" required>
            <div class="error-message" id="placeOfBirth-error">Please enter place of birth</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="dateOfBirth"
                class="form-label required">{{ config('student_enrolment.step2.labels.date_birth.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.date_birth.indonesian') }}</i></small>
            <input type="text" class="form-control date-picker" readonly id="dateOfBirth" required>
            <div class="error-message" id="dateOfBirth-error">Please enter date of birth</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="religion"
                class="form-label required">{{ config('student_enrolment.step2.labels.religion.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.religion.indonesian') }}</i></small>
            <input type="text" class="form-control" id="religion" required>
            <div class="error-message" id="religion-error">Please enter religion</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="ethnicity"
                class="form-label required">{{ config('student_enrolment.step2.labels.ethnicity.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.ethnicity.indonesian') }}</i></small>
            <input type="text" class="form-control" id="ethnicity" required>
            <div class="error-message" id="ethnicity-error">Please enter ethnicity</div>
        </div>

        <div class="col-md-4 mb-3">
            <label
                class="form-label required">{{ config('student_enrolment.step2.labels.citizenship.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.citizenship.indonesian') }}</i></small>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="citizenship" id="citizenshipWNI" value="WNI"
                        required>
                    <label class="form-check-label" for="citizenshipWNI">WNI</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="citizenship" id="citizenshipWNA"
                        value="WNA">
                    <label class="form-check-label" for="citizenshipWNA">WNA</label>
                </div>
            </div>
            <div class="error-message" id="citizenship-error">Please select citizenship</div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="height"
                class="form-label required">{{ config('student_enrolment.step2.labels.height.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.height.indonesian') }}</i></small>
            <input type="number" class="form-control" id="height" step="0.1" min="0" required>
            <div class="error-message" id="height-error">Please enter height in cm</div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="weight"
                class="form-label required">{{ config('student_enrolment.step2.labels.weight.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.weight.indonesian') }}</i></small>
            <input type="number" class="form-control" id="weight" step="0.1" min="0" required>
            <div class="error-message" id="weight-error">Please enter weight in kg</div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="siblingsCount"
                class="form-label required">{{ config('student_enrolment.step2.labels.number_sibling.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.number_sibling.indonesian') }}</i></small>
            <input type="number" class="form-control" id="siblingsCount" min="0" required>
            <div class="error-message" id="siblingsCount-error">Please enter number of siblings</div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="birthOrder"
                class="form-label required">{{ config('student_enrolment.step2.labels.order.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.order.indonesian') }}</i></small>
            <input type="number" class="form-control" id="birthOrder" min="1" required>
            <div class="error-message" id="birthOrder-error">Please enter birth order</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="homeLanguage"
                class="form-label required">{{ config('student_enrolment.step2.labels.primary_language.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.primary_language.indonesian') }}</i></small>
            <input type="text" class="form-control" id="homeLanguage" required>
            <div class="error-message" id="homeLanguage-error">Please enter primary home language</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="otherLanguages"
                class="form-label required">{{ config('student_enrolment.step2.labels.other_language.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.other_language.indonesian') }}</i></small>
            <input type="text" class="form-control" id="otherLanguages" required>
            <div class="error-message" id="otherLanguages-error">Please enter other languages spoken</div>
        </div>

        <div class="col-md-9 mb-3">
            <label for="fullAddress"
                class="form-label required">{{ config('student_enrolment.step2.labels.address.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.address.indonesian') }}</i></small>
            <textarea class="form-control" id="fullAddress" rows="3" required></textarea>
            <div class="error-message" id="fullAddress-error">Please enter full address</div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="postalCode"
                class="form-label required">{{ config('student_enrolment.step2.labels.zipcode.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.zipcode.indonesian') }}</i></small>
            <input type="number" class="form-control" id="postalCode" required>
            <div class="error-message" id="postalCode-error">Please enter postal code</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="homePhone"
                class="form-label">{{ config('student_enrolment.step2.labels.home_phone.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.home_phone.indonesian') }}</i></small>
            <input type="tel" class="form-control" id="homePhone">
        </div>

        <div class="col-md-4 mb-3">
            <label for="parentPhone"
                class="form-label required">{{ config('student_enrolment.step2.labels.parent_phone.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.parent_phone.indonesian') }}</i></small>
            <input type="tel" class="form-control" id="parentPhone" required>
            <div class="error-message" id="parentPhone-error">Please enter parent's mobile number</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="livingWith"
                class="form-label required">{{ config('student_enrolment.step2.labels.living_with.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.living_with.indonesian') }}</i></small>
            <select class="form-select" id="livingWith" required>
                <option value="" selected disabled>Select option</option>
                <option value="parents">Parents</option>
                <option value="family">Family</option>
                <option value="others">Others</option>
            </select>
            <div class="error-message" id="livingWith-error">Please select whom the student lives with</div>

            <div class="col-md-12 mb-3 conditional-field" id="livingWithOthersField">
                <label for="livingWithOthers"
                    class="form-label required">{{ config('student_enrolment.step2.labels.living_with_other.english') }}</label>
                <input type="text" class="form-control" id="livingWithOthers">
                <div class="error-message" id="livingWithOthers-error">Please specify living arrangement</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <label for="previousSchool"
                class="form-label required">{{ config('student_enrolment.step2.labels.prev_school.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.prev_school.indonesian') }}</i></small>
            <input type="text" class="form-control" id="previousSchool" required>
            <div class="error-message" id="previousSchool-error">Please enter previous school</div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="previousSchoolAddress"
                class="form-label required">{{ config('student_enrolment.step2.labels.prev_address.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.prev_address.indonesian') }}</i></small>
            <input type="text" class="form-control" id="previousSchoolAddress" required>
            <div class="error-message" id="previousSchoolAddress-error">Please enter previous school address
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <label for="distanceToSchool"
                class="form-label required">{{ config('student_enrolment.step2.labels.distance.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.distance.indonesian') }}</i></small>
            <input type="number" class="form-control" id="distanceToSchool" step="0.1" min="0"
                required>
            <div class="error-message" id="distanceToSchool-error">Please enter distance to school</div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="graduationYear" class="form-label required">Leaving Previous School</label>
            <br><small><i>Tahun kelulusan</i></small>
            <select class="form-select academic-year" id="graduationYear" required>
                <option value="" selected disabled>Select year</option>
                <option value="Not Yet Enrolled">Not yet Enroled</option>
            </select>
            <div class="error-message" id="graduationYear-error">Please select graduation year</div>
        </div>
        <div class="col-md-4 mb-3">
            <label for="applyingLevel"
                class="form-label required">{{ config('student_enrolment.step2.labels.level.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.level.indonesian') }}</i></small>
            <select class="form-select" id="applyingLevel" required></select>
            <div class="error-message" id="applyingLevel-error">Please select applying level</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="applyingClass"
                class="form-label required">{{ config('student_enrolment.step2.labels.grade.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.grade.indonesian') }}</i></small>
            <select class="form-select" id="applyingClass" required>
                <option value="" selected disabled>Select class</option>
            </select>
            <div class="error-message" id="applyingClass-error">Please select class</div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="academic-year"
                class="form-label required">{{ config('student_enrolment.step2.labels.academic_year.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.academic_year.indonesian') }}</i></small>
            <select class="form-select academic-year" id="academic-year" required>
                <option value="" selected disabled>Select academic year</option>
            </select>
            <div class="error-message" id="academic-year-error">Please select academic year</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="emergencyContactPriority"
                class="form-label required">{{ config('student_enrolment.step2.labels.emergency.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.emergency.indonesian') }}</i></small>
            <input type="text" class="form-control" id="emergencyContactPriority">
            <div class="error-message" id="emergencyContactPriority-error">Please select emergency contact
                priority</div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="emergencyContactPriorityNumber"
                class="form-label required">{{ config('student_enrolment.step2.labels.emergency_phone.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.emergency_phone.indonesian') }}</i></small>
            <input type="text" class="form-control" id="emergencyContactPriorityNumber" required>
            <div class="error-message" id="emergencyContactPriorityNumber-error">Please select emergency contact
                priority</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="notAttendingSchool"
                class="form-label required">{{ config('student_enrolment.step2.labels.not_school.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.not_school.indonesian') }}</i></small>
            <select class="form-select" id="notAttendingSchool" required>
                <option value="" selected disabled>Select option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="notAttendingSchool-error">Please select an option</div>

            <div class="col-12 conditional-field" id="notAttendingSchoolYesField">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="notAttendingDuration"
                            class="form-label required">{{ config('student_enrolment.step2.labels.duration.english') }}</label>
                        <input type="text" class="form-control" id="notAttendingDuration">
                        <div class="error-message" id="notAttendingDuration-error">Please enter duration</div>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="notAttendingReason"
                            class="form-label required">{{ config('student_enrolment.step2.labels.reason.english') }}</label>
                        <input type="text" class="form-control" id="notAttendingReason">
                        <div class="error-message" id="notAttendingReason-error">Please enter reason</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="developmentalAssessment"
                class="form-label required">{{ config('student_enrolment.step2.labels.developmental.english') }}</label>
            <br><small><i>{{ config('student_enrolment.step2.labels.developmental.indonesian') }}</i></small>
            <select class="form-select" id="developmentalAssessment" required>
                <option value="" selected disabled>Select option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="developmentalAssessment-error">Please select an option</div>

            <div class="col-12 conditional-field" id="developmentalAssessmentYesField">
                <div class="mb-3">
                    <label for="developmentalDiagnosis"
                        class="form-label required">{{ config('student_enrolment.step2.labels.description.english') }}</label>
                    <textarea class="form-control" id="developmentalDiagnosis" rows="2"></textarea>
                    <div class="error-message" id="developmentalDiagnosis-error">Please enter diagnosis
                        description</div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <label for="therapyHistory" class="form-label required">
                {{ config('student_enrolment.step2.labels.therapy.english') }}
            </label>
            <br><small><i>{{ config('student_enrolment.step2.labels.therapy.indonesian') }}</i></small>
            <select class="form-select" id="therapyHistory" required>
                <option value="" selected disabled>Select option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="therapyHistory-error">Please select an option</div>

            <div class="col-12 conditional-field" id="therapyHistoryYesField">
                <div class="mb-3">
                    <label for="therapyType"
                        class="form-label required">{{ config('student_enrolment.step2.labels.type_therapy.english') }}</label>
                    <textarea class="form-control" id="therapyType" rows="2"></textarea>
                    <div class="error-message" id="therapyType-error">Please enter type and duration of
                        therapy</div>
                </div>
            </div>
        </div>
    </div>
</div>
