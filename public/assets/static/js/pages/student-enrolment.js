let currentStep = 1;
let admission = null;
const totalSteps = 6;
let enrolment = null;
let levels = [];

$(document).ready(async function () {
    getAcademicYear();
    getConstantData();
    let query = getQueryString();
    $("#enrollmentCode").val(query.code || "");

    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300
    );

    initializeDynamicDropdowns();

    setupConditionalFields();

    $("#next-btn").on("click", nextStep);
    $("#prev-btn").on("click", prevStep);
    $("#final-submit-btn").on("click", submitForm);

    $("input, select, textarea").on("input change", function () {
        validateCurrentStep();
    });

    validateCurrentStep();
});

function initializeDynamicDropdowns() {
    const currentYear = new Date().getFullYear();
    const graduationYearSelect = $("#graduationYear");

    for (let i = 0; i < 5; i++) {
        const year = currentYear + 2 - i;
        graduationYearSelect.append(`<option value="${year}">${year}</option>`);
    }

    const academicYearSelect = $("#academicYear");

    for (let i = 0; i < 4; i++) {
        const year = currentYear + i;
        const nextYear = year + 1;
        academicYearSelect.append(
            `<option value="${year}/${nextYear}">${year}/${nextYear}</option>`
        );
    }
}

function setupConditionalFields() {
    $("#livingWith").on("change", function () {
        if ($(this).val() === "others") {
            $("#livingWithOthersField").slideDown(300);
            $("#livingWithOthers").prop("required", true);
        } else {
            $("#livingWithOthersField").slideUp(300);
            $("#livingWithOthers").prop("required", false);
        }
    });

    $("#notAttendingSchool").on("change", function () {
        if ($(this).val() === "true") {
            $("#notAttendingSchoolYesField").slideDown(300);
            $("#notAttendingDuration, #notAttendingReason").prop(
                "required",
                true
            );
        } else {
            $("#notAttendingSchoolYesField").slideUp(300);
            $("#notAttendingDuration, #notAttendingReason").prop(
                "required",
                false
            );
        }
    });
    $("#surgeryHistory").on("change", function () {
        if ($(this).val() === "true") {
            $("#surgeryHistoryYesField").slideDown(300);
            $("#surgeryNote").prop("required", true);
        } else {
            $("#surgeryHistoryYesField").slideUp(300);
            $("#surgeryNote").prop("required", false);
        }
    });

    $("#hospitalizationHistory").on("change", function () {
        if ($(this).val() === "true") {
            $("#hospitalizationHistoryYesField").slideDown(300);
            $("#hospitalizationNote").prop("required", true);
        } else {
            $("#hospitalizationHistoryYesField").slideUp(300);
            $("#hospitalizationNote").prop("required", false);
        }
    });

    $("#seizureHistory").on("change", function () {
        if ($(this).val() === "true") {
            $("#seizureHistoryYesField").slideDown(300);
            $("#seizureNote").prop("required", true);
        } else {
            $("#seizureHistoryYesField").slideUp(300);
            $("#seizureNote").prop("required", false);
        }
    });
    $("#accidentHistory").on("change", function () {
        if ($(this).val() === "true") {
            $("#accidentHistoryYesField").slideDown(300);
            $("#accidentNote").prop("required", true);
        } else {
            $("#accidentHistoryYesField").slideUp(300);
            $("#accidentNote").prop("required", false);
        }
    });

    $("#developmentalAssessment").on("change", function () {
        if ($(this).val() === "true") {
            $("#developmentalAssessmentYesField").slideDown(300);
            $("#developmentalDiagnosis").prop("required", true);
        } else {
            $("#developmentalAssessmentYesField").slideUp(300);
            $("#developmentalDiagnosis").prop("required", false);
        }
    });

    $("#therapyHistory").on("change", function () {
        if ($(this).val() === "true") {
            $("#therapyHistoryYesField").slideDown(300);
            $("#therapyType").prop("required", true);
        } else {
            $("#therapyHistoryYesField").slideUp(300);
            $("#therapyType").prop("required", false);
        }
    });

    $("#foodAllergy").on("change", function () {
        if ($(this).val() === "true") {
            $("#foodAllergyYesField").slideDown(300);
            $("#foodAllergyExplanation").prop("required", true);
        } else {
            $("#foodAllergyYesField").slideUp(300);
            $("#foodAllergyExplanation").prop("required", false);
        }
    });

    $("#drugAllergy").on("change", function () {
        if ($(this).val() === "true") {
            $("#drugAllergyYesField").slideDown(300);
            $("#drugAllergyExplanation").prop("required", true);
        } else {
            $("#drugAllergyYesField").slideUp(300);
            $("#drugAllergyExplanation").prop("required", false);
        }
    });

    $("#pregnancyMedication").on("change", function () {
        if ($(this).val() === "true") {
            $("#pregnancyMedicationYesField").slideDown(300);
            $("#pregnancyMedicationExplanation").prop("required", true);
        } else {
            $("#pregnancyMedicationYesField").slideUp(300);
            $("#pregnancyMedicationExplanation").prop("required", false);
        }
    });

    $("#pregnancyIllness").on("change", function () {
        if ($(this).val() === "true") {
            $("#pregnancyIllnessYesField").slideDown(300);
            $("#pregnancyIllnessExplanation").prop("required", true);
        } else {
            $("#pregnancyIllnessYesField").slideUp(300);
            $("#pregnancyIllnessExplanation").prop("required", false);
        }
    });

    $('input[name="routineMedication"]').on("change", function () {
        if ($(this).val() === "true") {
            $("#routineMedicationYesField").slideDown(300);
            $("#routineMedicationExplanation").prop("required", true);
        } else {
            $("#routineMedicationYesField").slideUp(300);
            $("#routineMedicationExplanation").prop("required", false);
        }
    });
}

function validateCurrentStep() {
    const currentStepElement = $(`#step-${currentStep}`);
    let isValid = true;

    currentStepElement
        .find(".validation-error")
        .removeClass("validation-error");
    currentStepElement.find(".error-message").hide();

    currentStepElement
        .find("input[required], select[required], textarea[required]")
        .each(function () {
            const field = $(this);
            const errorElement = $(`#${field.attr("id")}-error`);

            if (
                field.closest(".conditional-field").length &&
                !field.closest(".conditional-field").is(":visible")
            ) {
                return;
            }

            if (field.is('input[type="radio"]')) {
                const radioName = field.attr("name");
                const radioChecked =
                    currentStepElement.find(
                        `input[name="${radioName}"]:checked`
                    ).length > 0;

                if (!radioChecked) {
                    isValid = false;
                    currentStepElement
                        .find(`input[name="${radioName}"]`)
                        .addClass("validation-error");
                    if (errorElement.length) {
                        errorElement.show();
                    } else {
                        $(`#${radioName}-error`).show();
                    }
                }
            } else if (field.is('input[type="checkbox"]')) {
                if (!field.is(":checked")) {
                    isValid = false;
                    field.addClass("validation-error");
                    errorElement.show();
                }
            } else if (field.is("select")) {
                if (!field.val()) {
                    isValid = false;
                    field.addClass("validation-error");
                    errorElement.show();
                }
            } else {
                if (!field.val().trim()) {
                    isValid = false;
                    field.addClass("validation-error");
                    errorElement.show();
                } else if (field.is('input[type="email"]')) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.val().trim())) {
                        isValid = false;
                        field.addClass("validation-error");
                        errorElement.show();
                    }
                }
            }
        });

    if (currentStep === totalSteps) {
        $("#next-btn").hide();
        $("#prev-btn").show();
    } else {
        $("#next-btn").show().prop("disabled", !isValid);
        $("#prev-btn").show();
    }

    return isValid;
}

async function nextStep() {
    if (!validateCurrentStep()) {
        return;
    }
    if (currentStep == 1) {
        getData();
    }
    if (currentStep == 2) {
        await postApplicant();
    }
    $(`.step[data-step="${currentStep}"]`)
        .removeClass("active")
        .addClass("completed");
    $(`#step-${currentStep}`).removeClass("active");
    currentStep++;
    $(`.step[data-step="${currentStep}"]`).addClass("active");
    $(`#step-${currentStep}`).addClass("active");
    $("#prev-btn").prop("disabled", false);
    $(".wizard-container").animate(
        {
            scrollTop: 0,
        },
        300
    );

    validateCurrentStep();
    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300
    );
}

function prevStep() {
    // Mark current step as not completed
    $(`.step[data-step="${currentStep}"]`)
        .removeClass("active")
        .removeClass("completed");
    $(`#step-${currentStep}`).removeClass("active");
    currentStep--;
    $(`.step[data-step="${currentStep}"]`)
        .addClass("active")
        .removeClass("completed");
    $(`#step-${currentStep}`).addClass("active");
    $("#prev-btn").prop("disabled", currentStep === 1);
    $("#next-btn").show().prop("disabled", false);

    $(".wizard-container").animate(
        {
            scrollTop: 0,
        },
        300
    );

    validateCurrentStep();
    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300
    );
}

function submitForm() {
    if (!validateCurrentStep()) {
        alert(
            "Harap lengkapi semua bidang yang diperlukan sebelum mengirimkan."
        );
        return;
    }

    let dob = moment($("#dateOfBirth").val(), "DD MMMM YYYY").format(
        "YYYY-MM-DD"
    );

    let admission = {
        enrolment_id: enrolment.id,
        code: enrolment.code,
        branch_id: enrolment.branch_id,
        level_id: $("#applyingLevel").val(),
        grade_id: $("#applyingClass").val(),
        academic_year: $("#academicYear").val(),
        applicant: {
            fullname: $("#studentFullName").val().trim(),
            nickname: $("#studentNickname").val().trim(),
            gender: $("input[name='studentGender']:checked").val(),
            place_of_birth: $("#placeOfBirth").val().trim(),
            date_of_birth: dob,
            age: calculateAge(dob),
            religion: $("#religion").val(),
            ethnicity: $("#ethnicity").val(),
            citizenship: $("input[name='citizenship']:checked").val(),
            height: parseFloat($("#height").val()),
            weight: parseFloat($("#weight").val()),
            siblings_count: parseInt($("#siblingsCount").val()),
            birth_order: parseInt($("#birthOrder").val()),
            home_language: $("#homeLanguage").val().trim(),
            other_languages: $("#otherLanguages").val().trim(),
            address: $("#fullAddress").val().trim(),
            zipcode: $("#postalCode").val().trim(),
            home_phone: $("#homePhone").val().trim(),
            parent_phone: $("#parentPhone").val().trim(),
            living_with: $("#livingWith").val(),
            living_with_other:
                $("#livingWith").val() === "others"
                    ? $("#livingWithOthers").val().trim()
                    : null,
            distance_km: parseFloat($("#distanceToSchool").val()),
            previous_school: $("#previousSchool").val().trim(),
            previous_school_address: $("#previousSchoolAddress").val().trim(),
            graduation_year: $("#graduationYear").val(),
            ever_not_schooling:
                $("#notAttendingSchool").val() == "true" ? true : false,
            not_school_duration:
                $("#notAttendingSchool").val() == "true"
                    ? $("#notAttendingDuration").val().trim()
                    : null,
            not_school_reason:
                $("#notAttendingSchool").val() == "true"
                    ? $("#notAttendingReason").val().trim()
                    : null,
            dev_checked:
                $("#developmentalAssessment").val() == "true" ? true : false,
            dev_diagnosis:
                $("#developmentalAssessment").val() == "true"
                    ? $("#developmentalDiagnosis").val().trim()
                    : null,
            therapy_history:
                $("#therapyHistory").val() == "true" ? true : false,
            therapy_detail:
                $("#therapyHistory").val() == "true"
                    ? $("#therapyType").val().trim()
                    : null,
            emergency_contact_priority: $("#emergencyContactPriority").val(),
            health: {
                blood_type: $("#bloodType").val(),
                food_allergy: $("#foodAllergy").val() == "true" ? true : false,
                food_allergy_note:
                    $("#foodAllergy").val() == "true"
                        ? $("#foodAllergyExplanation").val().trim()
                        : null,
                drug_allergy: $("#drugAllergy").val() == "true" ? true : false,
                drug_allergy_note:
                    $("#drugAllergy").val() == "true"
                        ? $("#drugAllergyExplanation").val().trim()
                        : null,
                uses_glasses:
                    $('input[name="usesGlasses"]:checked').val() == "true"
                        ? true
                        : false,
                uses_hearing_aid:
                    $('input[name="usesHearingAid"]:checked').val() == "true"
                        ? true
                        : false,
                routine_medication:
                    $('input[name="routineMedication"]:checked').val() == "true"
                        ? true
                        : false,
                routine_medication_note:
                    $('input[name="routineMedication"]:checked').val() == "true"
                        ? $("#routineMedicationExplanation").val().trim()
                        : null,
                family_disease_history: $("#familyDiseaseHistory").val().trim(),
                referral_health_facility: $("#referralFacility").val().trim(),
                emergency_contact: $("#emergencyContactInfo").val().trim(),
            },
            immunizations: {
                bcg:
                    $('input[name="immunizationBCG"]:checked').val() == "true"
                        ? true
                        : false,
                hepatitis:
                    $('input[name="immunizationHepatitis"]:checked').val() ==
                    "true"
                        ? true
                        : false,
                dpt:
                    $('input[name="immunizationDTP"]:checked').val() == "true"
                        ? true
                        : false,
                polio:
                    $('input[name="immunizationPolio"]:checked').val() == "true"
                        ? true
                        : false,
                measles:
                    $('input[name="immunizationMeasles"]:checked').val() ==
                    "true"
                        ? true
                        : false,
                hepatitis_a:
                    $('input[name="immunizationHepatitisA"]:checked').val() ==
                    "true"
                        ? true
                        : false,
                mmr:
                    $('input[name="immunizationMMR"]:checked').val() == "true"
                        ? true
                        : false,
                influenza:
                    $('input[name="immunizationInfluenza"]:checked').val() ==
                    "true"
                        ? true
                        : false,
            },
            medicalHistory: {
                surgery_history: $("#surgeryHistory").val().trim(),
                surgery_note:
                    $("#surgeryHistory").val().trim() === "true"
                        ? $("#surgeryNote").val().trim()
                        : null,
                hospitalization_history: $("#hospitalizationHistory")
                    .val()
                    .trim(),
                hospitalization_note:
                    $("#hospitalizationHistory").val().trim() === "true"
                        ? $("#hospitalizationNote").val().trim()
                        : null,
                seizure_history: $("#seizureHistory").val().trim(),
                seizure_note:
                    $("#seizureHistory").val().trim() === "true"
                        ? $("#seizureNote").val().trim()
                        : null,
                accident_history: $("#accidentHistory").val().trim(),
                accident_note:
                    $("#accidentHistory").val().trim() === "true"
                        ? $("#accidentNote").val().trim()
                        : null,
            },
            pregnancy_histories: {
                medication_during_pregnancy: $("#pregnancyMedication")
                    .val()
                    .trim(),
                medication_note:
                    $("#pregnancyMedication").val().trim() === "true"
                        ? $("#pregnancyMedicationExplanation").val().trim()
                        : null,
                illness_during_pregnancy: $("#pregnancyIllness").val().trim(),
                illness_note:
                    $("#pregnancyIllness").val().trim() === "true"
                        ? $("#pregnancyIllnessExplanation").val().trim()
                        : null,
                gestational_age: $("#gestationalAge").val(),
                delivery_method: $("#deliveryMethod").val(),
                birth_weight: parseFloat($("#birthWeight").val()),
                birth_height: parseFloat($("#birthHeight").val()),
                walk_age_month: parseInt($("#walkedAtAge").val()),
                talk_age_month: parseInt($("#spokeAtAge").val()),
            },
            declarations: {
                agreed: $("#parentDeclaration").is(":checked") ? true : false,
                agreed_at: new Date().toISOString(),
            },
            parents: [
                {
                    role: "father",
                    fullname: $("#fatherFullName").val().trim(),
                    phone: validatePhone($("#fatherMobile").val().trim()),
                    home_phone: $("#fatherHomePhone").val().trim(),
                    email: validateEmail($("#fatherEmail").val().trim()),
                    birth_place: $("#fatherPlaceOfBirth").val().trim(),
                    date_of_birth: moment(
                        $("#fatherDateOfBirth").val().trim(),
                        "DD MMMM YYYY"
                    ).format("YYYY-MM-DD"),
                    identity_number: $("#fatherIdCard").val().trim(),
                    religion: $("#fatherReligion").val(),
                    ethnicity: $("#fatherEthnicity").val().trim(),
                    address: $("#fatherAddress").val().trim(),
                    zipcode: $("#fatherPostalCode").val().trim(),
                    education: $("#fatherEducation").val(),
                    occupation: $("#fatherOccupation").val(),
                    company_name: $("#fatherInstitution").val().trim(),
                    company_address: $("#fatherInstitutionAddress")
                        .val()
                        .trim(),
                    marital_status: $(
                        "input[name='fatherMaritalStatus']:checked"
                    ).val(),
                    monthly_income: parseFloat($("#fatherIncome").val()),
                },
                {
                    role: "mother",
                    fullname: $("#motherFullName").val().trim(),
                    phone: validatePhone($("#motherMobile").val().trim()),
                    home_phone: $("#motherHomePhone").val().trim(),
                    email: validateEmail($("#motherEmail").val().trim()),
                    birth_place: $("#motherPlaceOfBirth").val().trim(),
                    date_of_birth: moment(
                        $("#motherDateOfBirth").val().trim(),
                        "DD MMMM YYYY"
                    ).format("YYYY-MM-DD"),
                    identity_number: $("#motherIdCard").val().trim(),
                    religion: $("#motherReligion").val(),
                    ethnicity: $("#motherEthnicity").val().trim(),
                    address: $("#motherAddress").val().trim(),
                    zipcode: $("#motherPostalCode").val().trim(),
                    education: $("#motherEducation").val(),
                    occupation: $("#motherOccupation").val(),
                    company_name: $("#motherInstitution").val().trim(),
                    company_address: $("#motherInstitutionAddress")
                        .val()
                        .trim(),
                    marital_status: $(
                        "input[name='motherMaritalStatus']:checked"
                    ).val(),
                    monthly_income: parseFloat($("#motherIncome").val()),
                },
                {
                    role: "guardian",
                    fullname: $("#guardianFullName").val().trim(),
                    phone: validatePhone($("#guardianMobile").val().trim()),
                    home_phone: $("#guardianHomePhone").val().trim(),
                    email: validateEmail($("#guardianEmail").val().trim()),
                    birth_place: $("#guardianPlaceOfBirth").val().trim(),
                    date_of_birth: moment(
                        $("#guardianDateOfBirth").val().trim(),
                        "DD MMMM YYYY"
                    ).format("YYYY-MM-DD"),
                    identity_number: $("#guardianIdCard").val().trim(),
                    religion: $("#guardianReligion").val(),
                    ethnicity: $("#guardianEthnicity").val().trim(),
                    address: $("#guardianAddress").val().trim(),
                    zipcode: $("#guardianPostalCode").val().trim(),
                    education: $("#guardianEducation").val(),
                    occupation: $("#guardianOccupation").val(),
                    company_name: $("#guardianInstitution").val().trim(),
                    company_address: $("#guardianInstitutionAddress")
                        .val()
                        .trim(),
                    marital_status: $(
                        "input[name='guardianMaritalStatus']:checked"
                    ).val(),
                    monthly_income: parseFloat($("#guardianIncome").val()),
                },
            ],
        },
    };

    console.log("Formulir dikirim dengan data:", admission);
    alert(
        "Formulir berhasil dikirim! Terima kasih telah melengkapi formulir pendaftaran untuk Mutiara Harapan Islamic School."
    );
}

async function getData() {
    blockUI();
    const code = $("#enrollmentCode").val().trim();
    try {
        await getAdmissionByCode(code);
        await getLevelsAndGrades(admission);
    } catch (err) {
        if (err.status == 404) {
            await getEnrolmentByCode(code);
            await getLevelsAndGrades(enrolment);
        } else {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    }
}

async function getAdmissionByCode(code) {
    admission = await ajaxPromise(null, `/admission/code/${code}`, "GET");
    fillStudent();
}

async function getEnrolmentByCode(code) {
    try {
        blockUI();

        enrolment = await ajaxPromise(
            null,
            `/enrolment/student/${code}`,
            "GET"
        );

        $("#studentFullName").val(enrolment.child_name);
        $("#placeOfBirth").val(enrolment.place_of_birth);
        $("#dateOfBirth").val(
            moment(enrolment.date_of_birth).format("DD MMMM YYYY")
        );
        $("#academic-year").val(enrolment.academic_year).trigger("change");
        $("#previousSchool").val(enrolment.current_school);
        $("#postalCode").val(enrolment.zipcode);
        $("#fullAddress").val(enrolment.address);

        $("#fatherAddress ,#motherAddress").val(enrolment.address);

        if (enrolment.relationship === "father") {
            $("#fatherFullName").val(enrolment.parent_name);
            $("#fatherEmail").val(enrolment.email);
            $("#fatherMobile, #parentPhone").val(enrolment.phone_number);
        } else if (enrolment.relationship === "mother") {
            $("#motherFullName").val(enrolment.parent_name);
            $("#motherEmail").val(enrolment.email);
            $("#motherMobile, #parentPhone").val(enrolment.phone_number);
        } else if (enrolment.relationship === "guardian") {
            $("#guardianFullName").val(enrolment.parent_name);
            $("#guardianEmail").val(enrolment.email);
            $("#guardianMobile, #parentPhone").val(enrolment.phone_number);
        }
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom"
        );
        throw err;
    }
}

async function getLevelsAndGrades(data) {
    try {
        const levels = await ajaxPromise(
            null,
            `/level/branch/${data.branch.id}`,
            "GET"
        );

        $("#applyingLevel")
            .empty()
            .append('<option value="">Select level...</option>');

        levels.forEach((level) => {
            $("#applyingLevel").append(
                `<option value="${level.id}">${level.name}</option>`
            );
        });

        $("#applyingLevel").val(data.level.id).trigger("change");

        const selectedLevel = levels.find((level) => level.id == data.level.id);

        $("#applyingClass").empty();

        selectedLevel?.grades.forEach((grade) => {
            $("#applyingClass").append(
                `<option value="${grade.id}">${grade.name}</option>`
            );
        });

        $("#applyingClass").val(data.grade.id).trigger("change");
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom"
        );
    }
}
async function postApplicant() {
    try {
        let dob = moment($("#dateOfBirth").val(), "DD MMMM YYYY").format(
            "YYYY-MM-DD"
        );
        let _admission = {
            enrolment_id: enrolment.id,
            code: enrolment.code,
            branch_id: enrolment.branch_id,
            level_id: $("#applyingLevel").val(),
            grade_id: $("#applyingClass").val(),
            academic_year: $("#academic-year").val(),
            applicant: {
                fullname: $("#studentFullName").val().trim(),
                nickname: $("#studentNickname").val().trim(),
                gender: $("input[name='studentGender']:checked").val(),
                place_of_birth: $("#placeOfBirth").val().trim(),
                date_of_birth: dob,
                age: calculateAge(dob),
                religion: $("#religion").val(),
                ethnicity: $("#ethnicity").val(),
                citizenship: $("input[name='citizenship']:checked").val(),
                height: parseFloat($("#height").val()),
                weight: parseFloat($("#weight").val()),
                siblings_count: parseInt($("#siblingsCount").val()),
                birth_order: parseInt($("#birthOrder").val()),
                home_language: $("#homeLanguage").val().trim(),
                other_languages: $("#otherLanguages").val().trim(),
                address: $("#fullAddress").val().trim(),
                zipcode: $("#postalCode").val().trim(),
                home_phone: $("#homePhone").val().trim(),
                parent_phone: $("#parentPhone").val().trim(),
                living_with: $("#livingWith").val(),
                living_with_other:
                    $("#livingWith").val() === "others"
                        ? $("#livingWithOthers").val().trim()
                        : null,
                distance_km: parseFloat($("#distanceToSchool").val()),
                previous_school: $("#previousSchool").val().trim(),
                previous_school_address: $("#previousSchoolAddress")
                    .val()
                    .trim(),
                graduation_year: $("#graduationYear").val(),
                ever_not_schooling:
                    $("#notAttendingSchool").val() == "true" ? true : false,
                not_school_duration:
                    $("#notAttendingSchool").val() == "true"
                        ? $("#notAttendingDuration").val().trim()
                        : null,
                not_school_reason:
                    $("#notAttendingSchool").val() == "true"
                        ? $("#notAttendingReason").val().trim()
                        : null,
                dev_checked:
                    $("#developmentalAssessment").val() == "true"
                        ? true
                        : false,
                dev_diagnosis:
                    $("#developmentalAssessment").val() == "true"
                        ? $("#developmentalDiagnosis").val().trim()
                        : null,
                therapy_history:
                    $("#therapyHistory").val() == "true" ? true : false,
                therapy_detail:
                    $("#therapyHistory").val() == "true"
                        ? $("#therapyType").val().trim()
                        : null,
                emergency_contact_priority: $(
                    "#emergencyContactPriority"
                ).val(),
            },
        };
        console.log(_admission);
        const data = await ajaxPromise(
            _admission,
            `/admission/applicant`,
            "POST"
        );
        admission = data;
        console.log(admission);
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom"
        );
    }
}

function getConstantData() {
    religions.forEach((religion) => {
        $(".religion").append(
            `<option value="${religion}">${religion}</option>`
        );
    });
    educations.forEach((education) => {
        $(".education").append(
            `<option value="${education}">${education}</option>`
        );
    });
    jobs.forEach((job) => {
        $(".job").append(`<option value="${job}">${job}</option>`);
    });
}

function fillStudent() {
    let applicant = admission.applicant;
    $("#admissionID").val(admission.id);
    $("#applicantId").val(applicant.id);
    $("#studentFullName").val(applicant.fullname);
    $("#studentNickname").val(applicant.nickname);
    $(`input[name="studentGender"][value="${applicant.gender}"]`)
        .prop("checked", true)
        .trigger("change");
    $("#placeOfBirth").val(applicant.place_of_birth);
    $("#dateOfBirth").val(
        moment(applicant.date_of_birth).format("DD MMMM YYYY")
    );

    $("#religion").val(applicant.religion).trigger("change");
    $("#ethnicity").val(applicant.ethnicity);
    $(`input[name="citizenship"][value="${applicant.citizenship}"]`)
        .prop("checked", true)
        .trigger("change");
    $("#height").val(formatNumber(applicant.height));
    $("#weight").val(formatNumber(applicant.weight));
    $("#siblingsCount").val(formatNumber(applicant.siblings_count));
    $("#birthOrder").val(formatNumber(applicant.birth_order));
    $("#homeLanguage").val(applicant.home_language);
    $("#otherLanguages").val(applicant.other_languages);
    $("#fullAddress").val(applicant.address);
    $("#postalCode").val(applicant.zipcode);
    $("#homePhone").val(applicant.home_phone);
    $("#parentPhone").val(applicant.parent_phone);
    $("#livingWith").val(applicant.living_with).trigger("change");
    $("#livingWithOthers").val(applicant.living_with_other);
    $("#distanceToSchool").val(formatNumber(applicant.distance_km));
    $("#previousSchool").val(applicant.previous_school);
    $("#previousSchoolAddress").val(applicant.previous_school_address);
    $("#graduationYear").val(applicant.graduation_year).trigger("change");
    $("#academic-year").val(admission.accademic_year).trigger("change");
    $("#emergencyContactPriority")
        .val(applicant.emergency_contact_priority)
        .trigger("change");
    $("#notAttendingSchool")
        .val(applicant.ever_not_school.toString())
        .trigger("change");
    $("#notAttendingDuration").val(applicant.not_school_duration);
    $("#notAttendingReason").val(applicant.not_school_reason);
    $("#developmentalAssessment")
        .val(applicant.dev_check.toString())
        .trigger("change");
    $("#developmentalDiagnosis").val(applicant.dev_diagnosis);
    $("#therapyType").val(applicant.therapy_detail);
    $("#therapyHistory")
        .val(applicant.therapy_history.toString())
        .trigger("change");
}
