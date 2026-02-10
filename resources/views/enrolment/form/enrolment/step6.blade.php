<div>
    <h2 class="section-title">{{ config('student_enrolment.step6.title') }}</h2>

    <!-- Health Data Section -->
    <h5 class="subsection-title">{{ config('student_enrolment.step6.labels.immunisation.english') }}
        <i><small>{{ config('student_enrolment.step6.labels.immunisation.indonesian') }}</small></i>
    </h5>
    <div class="mb-4">
        <div class="row immunization-row">
            <div class="col-md-6">
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.bcg.english') }}</label>
                <div class="inline-radio-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationBCG" id="immunizationBCGYes"
                            value="true" required>
                        <label class="form-check-label" for="immunizationBCGYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationBCG" id="immunizationBCGNo"
                            value="false">
                        <label class="form-check-label" for="immunizationBCGNo">No</label>
                    </div>
                </div>
                <div class="error-message" id="immunizationBCG-error">Please select an option</div>
            </div>

            <div class="col-md-6">
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.hepatitis.english') }}</label>
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
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.diphtheria.english') }}</label>
                <div class="inline-radio-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationDTP" id="immunizationDTPYes"
                            value="true" required>
                        <label class="form-check-label" for="immunizationDTPYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationDTP" id="immunizationDTPNo"
                            value="false">
                        <label class="form-check-label" for="immunizationDTPNo">No</label>
                    </div>
                </div>
                <div class="error-message" id="immunizationDTP-error">Please select an option</div>
            </div>

            <div class="col-md-6">
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.polio.english') }}</label>
                <div class="inline-radio-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationPolio"
                            id="immunizationPolioYes" value="true" required>
                        <label class="form-check-label" for="immunizationPolioYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationPolio" id="immunizationPolioNo"
                            value="false">
                        <label class="form-check-label" for="immunizationPolioNo">No</label>
                    </div>
                </div>
                <div class="error-message" id="immunizationPolio-error">Please select an option</div>
            </div>
        </div>

        <div class="row immunization-row">
            <div class="col-md-6">
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.measles.english') }}</label>
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
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.hepatitis_a.english') }}</label>
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
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.mmr.english') }}</label>
                <div class="inline-radio-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationMMR"
                            id="immunizationMMRYes" value="true" required>
                        <label class="form-check-label" for="immunizationMMRYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="immunizationMMR" id="immunizationMMRNo"
                            value="false">
                        <label class="form-check-label" for="immunizationMMRNo">No</label>
                    </div>
                </div>
                <div class="error-message" id="immunizationMMR-error">Please select an option</div>
            </div>

            <div class="col-md-6">
                <label
                    class="form-label required d-block mb-2">{{ config('student_enrolment.step6.labels.influenza.english') }}</label>
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


    <h5 class="subsection-title">{{ config('student_enrolment.step6.labels.medical.english') }}
        <i><small>({{ config('student_enrolment.step6.labels.medical.indonesian') }})</small></i>
    </h5>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label for="foodAllergy"
                class="form-label required">{{ config('student_enrolment.step6.labels.food_allergy.english') }}</label>
            <select class="form-select" id="foodAllergy" required>
                <option value="" selected disabled>select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="foodAllergy-error">Please select an option</div>

            <div class="col-12 conditional-field" id="foodAllergyYesField">
                <div class="mb-3">
                    <label for="foodAllergyExplanation"
                        class="form-label required">{{ config('student_enrolment.step6.labels.food_allergy_explanation.english') }}</label>
                    <textarea class="form-control" id="foodAllergyExplanation" rows="2"></textarea>
                    <div class="error-message" id="foodAllergyExplanation-error">Please explain the food
                        allergy
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="drugAllergy"
                class="form-label required">{{ config('student_enrolment.step6.labels.medication_allergy.english') }}</label>
            <select class="form-select" id="drugAllergy" required>
                <option value="" selected disabled>Select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="drugAllergy-error">Please select an option</div>

            <div class="col-12 conditional-field" id="drugAllergyYesField">
                <div class="mb-3">
                    <label for="drugAllergyExplanation"
                        class="form-label required">{{ config('student_enrolment.step6.labels.medication_allergy_explanation.english') }}</label>
                    <textarea class="form-control" id="drugAllergyExplanation" rows="2"></textarea>
                    <div class="error-message" id="drugAllergyExplanation-error">Please explain the
                        medication allergy
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label for="bloodType"
                class="form-label required">{{ config('student_enrolment.step6.labels.bloody_type.english') }}</label>
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
            <label
                class="form-label required">{{ config('student_enrolment.step6.labels.wears_glasses.english') }}</label>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="usesGlasses" id="usesGlassesYes"
                        value="true" required>
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
            <label
                class="form-label required">{{ config('student_enrolment.step6.labels.hearing_aid.english') }}</label>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="usesHearingAid" id="usesHearingAidYes"
                        value="true" required>
                    <label class="form-check-label" for="usesHearingAidYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="usesHearingAid" id="usesHearingAidNo"
                        value="false">
                    <label class="form-check-label" for="usesHearingAidNo">No</label>
                </div>
            </div>
            <div class="error-message" id="usesHearingAid-error">Please select an option</div>
        </div>
    </div>

    <h5 class="subsection-title">{{ config('student_enrolment.step6.labels.pregnancy_history.english') }}</h5>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label for="pregnancyMedication"
                class="form-label required">{{ config('student_enrolment.step6.labels.medication.english') }}</label>
            <select class="form-select" id="pregnancyMedication" required>
                <option value="" selected disabled>Select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="pregnancyMedication-error">Please select an option</div>

            <div class="col-12 conditional-field" id="pregnancyMedicationYesField">
                <div class="mb-3">
                    <label for="pregnancyMedicationExplanation"
                        class="form-label required">{{ config('student_enrolment.step6.labels.medication_explanation.english') }}</label>
                    <textarea class="form-control" id="pregnancyMedicationExplanation" rows="2"></textarea>
                    <div class="error-message" id="pregnancyMedicationExplanation-error">Please explain
                        medication use during pregnancy</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="pregnancyIllness"
                class="form-label required">{{ config('student_enrolment.step6.labels.illnes.english') }}</label>
            <select class="form-select" id="pregnancyIllness" required>
                <option value="" selected disabled>Select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="pregnancyIllness-error">Please select an option</div>

            <div class="col-12 conditional-field" id="pregnancyIllnessYesField">
                <div class="mb-3">
                    <label for="pregnancyIllnessExplanation"
                        class="form-label required">{{ config('student_enrolment.step6.labels.illnes_explanatio.english') }}</label>
                    <textarea class="form-control" id="pregnancyIllnessExplanation" rows="2"></textarea>
                    <div class="error-message" id="pregnancyIllnessExplanation-error">Please explain
                        illness
                        during pregnancy</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="gestationalAge"
                class="form-label required">{{ config('student_enrolment.step6.labels.age_birth.english') }}</label>
            <select class="form-select" id="gestationalAge" required>
                <option value="" selected disabled>Select an option</option>
                <option value="Full Term">Full Term</option>
                <option value="Preterm">Preterm</option>
                <option value="Post-term">Post-term</option>
            </select>
            <div class="error-message" id="gestationalAge-error">Please select gestational age</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="deliveryMethod"
                class="form-label required">{{ config('student_enrolment.step6.labels.delivery.english') }}</label>
            <select class="form-select" id="deliveryMethod" required>
                <option value="" selected disabled>Select an option</option>
                <option value="normal">Vaginal Delivery (Persalinan
                    pervaginam)</option>
                <option value="cesarean">Caesarean Section</option>
            </select>
            <div class="error-message" id="deliveryMethod-error">Please select delivery method</div>
        </div>

        <h6 class="subsection-title">{{ config('student_enrolment.step6.labels.birth_condition.english') }}</h6>

        <div class="col-md-6 mb-3">
            <label for="birthWeight"
                class="form-label required">{{ config('student_enrolment.step6.labels.weight.english') }}</label>
            <input type="number" class="form-control" id="birthWeight" step="0.01" min="0" required>
            <div class="error-message" id="birthWeight-error">Please enter birth weight</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="birthHeight"
                class="form-label required">{{ config('student_enrolment.step6.labels.length.english') }}</label>
            <input type="number" class="form-control" id="birthHeight" step="0.1" min="0" required>
            <div class="error-message" id="birthHeight-error">Please enter birth length</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="walkedAtAge"
                class="form-label required">{{ config('student_enrolment.step6.labels.age_walking.english') }}</label>
            <input type="number" class="form-control" id="walkedAtAge" min="0" required>
            <div class="error-message" id="walkedAtAge-error">Please enter age started walking</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="spokeAtAge"
                class="form-label required">{{ config('student_enrolment.step6.labels.age_speaking.english') }}</label>
            <input type="number" class="form-control" id="spokeAtAge" min="0" required>
            <div class="error-message" id="spokeAtAge-error">Please enter age started speaking</div>
        </div>

        <div class="col-md-6 mb-3">
            <label
                class="form-label required">{{ config('student_enrolment.step6.labels.taking_medication.english') }}</label>
            <div class="inline-radio-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="routineMedication"
                        id="routineMedicationYes" value="true" required>
                    <label class="form-check-label" for="routineMedicationYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="routineMedication" id="routineMedicationNo"
                        value="false">
                    <label class="form-check-label" for="routineMedicationNo">No</label>
                </div>
            </div>
            <div class="error-message" id="routineMedication-error">Please select an option</div>
        </div>

        <div class="col-12 conditional-field" id="routineMedicationYesField">
            <div class="mb-3">
                <label for="routineMedicationExplanation"
                    class="form-label required">{{ config('student_enrolment.step6.labels.taking_medication_explanation.english') }}</label>
                <textarea class="form-control" id="routineMedicationExplanation" rows="2"></textarea>
                <div class="error-message" id="routineMedicationExplanation-error">Please explain the
                    routine medication</div>
            </div>
        </div>
    </div>

    <h5 class="subsection-title">{{ config('student_enrolment.step6.labels.previous_medical.english') }}</h5>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label for="surgeryHistory"
                class="form-label required">{{ config('student_enrolment.step6.labels.surgery.english') }}</label>
            <select class="form-select" id="surgeryHistory" required>
                <option value="" selected disabled>Select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="surgeryHistory-error">Please select an option</div>

            <div class="col-12 conditional-field" id="surgeryHistoryYesField">
                <div class="mb-3">
                    <label for="surgeryNote"
                        class="form-label required">{{ config('student_enrolment.step6.labels.surgery_explanation.english') }}</label>
                    <textarea class="form-control" id="surgeryNote" rows="2"></textarea>
                    <div class="error-message" id="surgeryNote-error">Please explain the surgery</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="hospitalizationHistory"
                class="form-label required">{{ config('student_enrolment.step6.labels.hospitalized.english') }}</label>
            <select class="form-select" id="hospitalizationHistory" required>
                <option value="" selected disabled>Select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="hospitalizationHistory-error">Please select an option</div>

            <div class="col-12 conditional-field" id="hospitalizationHistoryYesField">
                <div class="mb-3">
                    <label for="hospitalizationNote"
                        class="form-label required">{{ config('student_enrolment.step6.labels.hospitalized_explanation.english') }}</label>
                    <textarea class="form-control" id="hospitalizationNote" rows="2"></textarea>
                    <div class="error-message" id="hospitalizationNote-error">Please explain the reason for
                        hospitalization</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="seizureHistory"
                class="form-label required">{{ config('student_enrolment.step6.labels.seizures.english') }}</label>
            <select class="form-select" id="seizureHistory" required>
                <option value="" selected disabled>Select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="seizureHistory-error">Please select an option</div>

            <div class="col-12 conditional-field" id="seizureHistoryYesField">
                <div class="mb-3">
                    <label for="seizureNote"
                        class="form-label required">{{ config('student_enrolment.step6.labels.seizures_explanation.english') }}</label>
                    <textarea class="form-control" id="seizureNote" rows="2"></textarea>
                    <div class="error-message" id="seizureNote-error">Please explain the reason for seizures
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="accidentHistory"
                class="form-label required">{{ config('student_enrolment.step6.labels.accidents.english') }}</label>
            <select class="form-select" id="accidentHistory" required>
                <option value="" selected disabled>Select an option</option>
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
            <div class="error-message" id="accidentHistory-error">Please select an option</div>

            <div class="col-12 conditional-field" id="accidentHistoryYesField">
                <div class="mb-3">
                    <label for="accidentNote"
                        class="form-label required">{{ config('student_enrolment.step6.labels.accidents_explanation.english') }}</label>
                    <textarea class="form-control" id="accidentNote" rows="2"></textarea>
                    <div class="error-message" id="accidentNote-error">Please explain the reason for
                        accidents or injuries</div>
                </div>
            </div>
        </div>

        <div class="col-12 mb-3">
            <label for="familyDiseaseHistory"
                class="form-label required">{{ config('student_enrolment.step6.labels.family_disease.english') }}</label>
            <textarea class="form-control" id="familyDiseaseHistory" rows="3" required></textarea>
            <div class="error-message" id="familyDiseaseHistory-error">Please enter family disease history
            </div>
        </div>

        <div class="col-12 mb-3">
            <label for="referralFacility"
                class="form-label required">{{ config('student_enrolment.step6.labels.healthy_facility.english') }}</label>
            <textarea class="form-control" id="referralFacility" rows="2" required></textarea>
            <div class="error-message" id="referralFacility-error">Please enter referral health facility
            </div>
        </div>

        <div class="col-12 mb-3">
            <label for="emergencyContactInfo"
                class="form-label required">{{ config('student_enrolment.step6.labels.emergency.english') }}
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
                {!! config('student_enrolment.step6.labels.agreement.content') !!}
            </label>
            <div class="error-message" id="parentDeclaration-error">You must agree to the declaration.</div>
        </div>
    </div>

</div>
