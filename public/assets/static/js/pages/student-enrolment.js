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
        300,
    );

    setupConditionalFields();

    $("#next-btn").on("click", nextStep);
    $("#prev-btn").on("click", prevStep);
    $("#final-submit-btn").on("click", submitForm);

    $("input, select, textarea").on("input change", function () {
        validateCurrentStep();
    });

    validateCurrentStep();
});

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
                true,
            );
        } else {
            $("#notAttendingSchoolYesField").slideUp(300);
            $("#notAttendingDuration, #notAttendingReason").prop(
                "required",
                false,
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
                        `input[name="${radioName}"]:checked`,
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
        $("#final-submit-btn").prop("disabled", !isValid);
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
    blockUI();
    if (currentStep == 1) {
        let isContinue = await checkAdmissionByCode();
        if (!isContinue) {
            return;
        }
    }
    if (currentStep == 2) {
        await postApplicant();
        await getParent("father");
    }
    if (currentStep == 3) {
        await postParentByRole("father");
        await getParent("mother");
    }
    if (currentStep == 4) {
        await postParentByRole("mother");
        await getParent("guardian");
    }
    if (currentStep == 5) {
        await postParentByRole("guardian");
        await getApplicant();
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
        300,
    );

    validateCurrentStep();
    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300,
    );
}

function prevStep() {
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
        300,
    );

    validateCurrentStep();
    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300,
    );
}

async function checkAdmissionByCode() {
    try {
        const code = $("#enrollmentCode").val().trim();
        path = await ajaxPromise(null, `/document/check/${code}`, "GET");
        if (path != "student") {
            window.location.href = `/document/${path}/${code}`;
            return false;
        } else {
            getAdmissionByCode();
            return true;
        }
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom",
        );
        return false;
    }
}

async function getAdmissionByCode() {
    try {
        const code = $("#enrollmentCode").val().trim();
        admission = await ajaxPromise(null, `/document/code/${code}`, "GET");
        getLevelsAndGrades(admission);
        enrolment = admission.enrolment;
        fillStudent();
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom",
        );
    }
}

async function getParent(role) {
    try {
        let parent = await ajaxPromise(
            null,
            `/document/parent/${admission.applicant.id}/${role}`,
            "GET",
        );

        fillParentByRole(role, parent);
    } catch (err) {
        if (err.status === 404) {
            console.log(`Parent with role ${role} not found, skip.`);
            return null;
        }
        throw err;
    }
}

async function getLevelsAndGrades(data) {
    try {
        const levels = await ajaxPromise(
            null,
            `/level/branch/${data.branch.id}`,
            "GET",
        );

        $("#applyingLevel")
            .empty()
            .append('<option value="">Select level...</option>');

        levels.forEach((level) => {
            $("#applyingLevel").append(
                `<option value="${level.id}">${level.name}</option>`,
            );
        });

        $("#applyingLevel").val(data.level.id).trigger("change");

        const selectedLevel = levels.find((level) => level.id == data.level.id);

        $("#applyingClass").empty();

        selectedLevel?.grades.forEach((grade) => {
            $("#applyingClass").append(
                `<option value="${grade.id}">${grade.name}</option>`,
            );
        });

        $("#applyingClass").val(data.grade.id).trigger("change");
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom",
        );
    }
}

async function postApplicant() {
    try {
        const dobRaw = $("#dateOfBirth").val();
        const dob = dobRaw
            ? moment(dobRaw, "DD MMMM YYYY").format("YYYY-MM-DD")
            : null;

        const _admission = {
            code: enrolment.code,
            enrolment_id: enrolment.id,
            branch_id: enrolment.branch.id,
            level_id: $("#applyingLevel").val() || null,
            grade_id: $("#applyingClass").val() || null,
            academic_year: $("#academic-year").val() || null,

            applicant: {
                fullname: str($("#studentFullName").val()),
                nickname: str($("#studentNickname").val()),
                gender: $("input[name='studentGender']:checked").val() || null,
                place_of_birth: str($("#placeOfBirth").val()),
                date_of_birth: dob,
                age: dob ? calculateAge(dob) : null,

                religion: $("#religion").val() || null,
                ethnicity: $("#ethnicity").val() || null,
                citizenship:
                    $("input[name='citizenship']:checked").val() || null,

                height: num($("#height").val()),
                weight: num($("#weight").val()),
                siblings_count: num($("#siblingsCount").val()),
                birth_order: num($("#birthOrder").val()),

                home_language: str($("#homeLanguage").val()),
                other_languages: str($("#otherLanguages").val()),
                address: str($("#fullAddress").val()),
                zipcode: str($("#postalCode").val()),
                home_phone: str($("#homePhone").val()),
                parent_phone: str($("#parentPhone").val()),

                living_with: $("#livingWith").val() || null,
                living_with_other:
                    $("#livingWith").val() === "others"
                        ? str($("#livingWithOthers").val())
                        : null,

                distance_km: num($("#distanceToSchool").val()),
                previous_school: str($("#previousSchool").val()),
                previous_school_address: str($("#previousSchoolAddress").val()),
                graduation_year: $("#graduationYear").val() || null,

                ever_not_schooling: bool($("#notAttendingSchool").val()),
                not_school_duration: bool($("#notAttendingSchool").val())
                    ? str($("#notAttendingDuration").val())
                    : null,
                not_school_reason: bool($("#notAttendingSchool").val())
                    ? str($("#notAttendingReason").val())
                    : null,

                dev_checked: bool($("#developmentalAssessment").val()),
                dev_diagnosis: bool($("#developmentalAssessment").val())
                    ? str($("#developmentalDiagnosis").val())
                    : null,

                therapy_history: bool($("#therapyHistory").val()),
                therapy_detail: bool($("#therapyHistory").val())
                    ? str($("#therapyType").val())
                    : null,

                emergency_contact_priority: $(
                    "#emergencyContactPriority",
                ).val(),
                emergency_contact_phone: $(
                    "#emergencyContactPriorityNumber",
                ).val(),
            },
        };

        const data = await ajaxPromise(
            _admission,
            `/document/applicant`,
            "POST",
        );

        admission = data;
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom",
        );
    }
}

function getConstantData() {
    religions.forEach((religion) => {
        $(".religion").append(
            `<option value="${religion}">${religion}</option>`,
        );
    });
    educations.forEach((education) => {
        $(".education").append(
            `<option value="${education}">${education}</option>`,
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
        moment(applicant.date_of_birth).format("DD MMMM YYYY"),
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
    $("#emergencyContactPriority").val(applicant.emergency_contact_priority);
    $("#emergencyContactPriorityNumber").val(applicant.emergency_contact_phone);
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

function fillParentByRole(role, parent) {
    if (!parent) return;

    const prefix = `#${role}`;

    $(`${prefix}Id`).val(parent.id || "");
    $(`${prefix}FullName`).val(parent.fullname || "");
    $(`${prefix}Mobile`).val(parent.phone || "");
    $(`${prefix}HomePhone`).val(parent.home_phone || "");
    $(`${prefix}Email`).val(parent.email || "");
    $(`${prefix}PlaceOfBirth`).val(parent.birth_place || "");
    $(`${prefix}DateOfBirth`).val(
        parent.birth_date
            ? moment(parent.birth_date).format("DD MMMM YYYY")
            : "",
    );
    $(`${prefix}IdCard`).val(parent.identity_number || "");
    $(`${prefix}Religion`)
        .val(parent.religion || "")
        .trigger("change");
    $(`${prefix}Ethnicity`).val(parent.ethnicity || "");
    $(`${prefix}Address`).val(parent.address || "");
    $(`${prefix}PostalCode`).val(parent.zipcode || "");
    $(`${prefix}Education`)
        .val(parent.education || "")
        .trigger("change");
    $(`${prefix}Occupation`).val(parent.occupation || "");
    $(`${prefix}Institution`).val(parent.company_name || "");
    $(`${prefix}InstitutionAddress`).val(parent.company_address || "");

    if (parent.marital_status) {
        $(
            `input[name="${role}MaritalStatus"][value="${parent.marital_status}"]`,
        )
            .prop("checked", true)
            .trigger("change");
    }

    $(`${prefix}Income`).val(formatNumber(parent.monthly_income || ""));
}
async function postParentByRole(role) {
    const prefix = `#${role}`;
    const dobRaw = $(`${prefix}DateOfBirth`).val();
    const strMoney = $(`${prefix}Income`).val();
    let money =
        strMoney == ""
            ? 0
            : parseFloat(normalizeCurrency($(`${prefix}Income`).val()));

    let parent = {
        id: $(`${prefix}Id`).val(),
        applicant_id: admission.applicant.id,
        role: role,
        fullname: $(`${prefix}FullName`).val(),
        phone: $(`${prefix}Mobile`).val(),
        home_phone: $(`${prefix}HomePhone`).val(),
        email: $(`${prefix}Email`).val(),
        birth_place: $(`${prefix}PlaceOfBirth`).val(),
        birth_date: dobRaw
            ? moment(dobRaw, "DD MMMM YYYY").format("YYYY-MM-DD")
            : null,
        identity_number: $(`${prefix}IdCard`).val(),
        religion: $(`${prefix}Religion`).val(),
        ethnicity: $(`${prefix}Ethnicity`).val(),
        address: $(`${prefix}Address`).val(),
        zipcode: $(`${prefix}PostalCode`).val(),
        education: $(`${prefix}Education`).val(),
        occupation: $(`${prefix}Occupation`).val(),
        company_name: $(`${prefix}Institution`).val(),
        company_address: $(`${prefix}InstitutionAddress`).val(),
        marital_status: $(`input[name="${role}MaritalStatus"]:checked`).val(),
        monthly_income: money,
    };

    if (role === "guardian" && isParentEmpty(parent)) {
        console.log("Guardian empty, skipped saving.");
        return;
    }

    if (role === "father" || role === "mother") {
        if (!parent.fullname || !parent.phone) {
            throw new Error(`${role} fullname and phone are required`);
        }
    }

    await ajaxPromise(parent, `/document/parent`, "POST");
}

async function submitForm() {
    let _admission = {
        id: admission.id,
        applicant: {
            id: admission.applicant.id,
            immunization: {
                bcg: $("input[name='immunizationBCG']:checked").val(),
                hepatitis: $(
                    "input[name='immunizationHepatitis']:checked",
                ).val(),
                dtp: $("input[name='immunizationDTP']:checked").val(),
                polio: $("input[name='immunizationPolio']:checked").val(),
                measles: $("input[name='immunizationMeasles']:checked").val(),
                hepatitis_a: $(
                    "input[name='immunizationHepatitisA']:checked",
                ).val(),
                mmr: $("input[name='immunizationMMR']:checked").val(),
                influenza: $(
                    "input[name='immunizationInfluenza']:checked",
                ).val(),
            },
            health: {
                food_allergy: $("#foodAllergy").val(),
                food_allergy_note: $("#foodAllergyExplanation").val(),
                drug_allergy: $("#drugAllergy").val(),
                drug_allergy_note: $("#drugAllergyExplanation").val(),
                blood_type: $("#bloodType").val(),
                uses_glasses: $("input[name='usesGlasses']:checked").val(),
                uses_hearing_aid: $(
                    "input[name='usesHearingAid']:checked",
                ).val(),
                family_disease_history: $("#familyDiseaseHistory").val(),
                referral_health_facility: $("#referralFacility").val(),
                emergency_contact: $("#emergencyContactInfo").val(),
                routine_medication: $(
                    "input[name='routineMedication']:checked",
                ).val(),
                routine_medication_note: $(
                    "#routineMedicationExplanation",
                ).val(),
            },
            pregnancy_history: {
                medication_during_pregnancy: $("#pregnancyMedication").val(),
                medication_note: $("#pregnancyMedicationExplanation").val(),
                illness_during_pregnancy: $("#pregnancyIllness").val(),
                illness_note: $("#pregnancyIllnessExplanation").val(),
                gestational_age: $("#gestationalAge").val(),
                delivery_method: $("#deliveryMethod").val(),
                birth_weight: $("#birthWeight").val(),
                birth_height: $("#birthHeight").val(),
                walk_age_month: $("#walkedAtAge").val(),
                talk_age_month: $("#spokeAtAge").val(),
            },

            medical_histories: {
                surgery_history: $("#surgeryHistory").val(),
                surgery_note: $("#surgeryNote").val(),
                hospitalization_history: $("#hospitalizationHistory").val(),
                hospitalization_note: $("#hospitalizationNote").val(),
                seizure_history: $("#seizureHistory").val(),
                seizure_note: $("#seizureNote").val(),
                accident_history: $("#accidentHistory").val(),
                accident_note: $("#accidentNote").val(),
            },
            parent_declarations: {
                agreed: $("#parentDeclaration").prop("checked"),
            },
        },
    };

    try {
        blockUI();
        let response = await ajaxPromise(
            _admission,
            `/document/health`,
            "POST",
        );
        toastify("success", "Data berhasil disimpan", "bottom");
        setTimeout(function () {
            window.location.href = `/document/file/${admission.code}`;
        }, 1000);
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom",
        );
    }
}

async function getApplicant() {
    let applicant = await ajaxPromise(
        null,
        `/document/applicant/${admission.applicant.id}`,
        "GET",
    );

    mapHealth(applicant.health);
    mapImmunization(applicant.immunization);
    mapMedicalHistory(applicant.medical_history);
    mapPregnancyHistory(applicant.pregnancy_history);
    mapDeclaration(applicant.declaration);
}

function mapDeclaration(declaration) {
    if (!declaration) return;

    $("#parentDeclaration").prop("checked", declaration.agreed);
}

function mapPregnancyHistory(pregnancy) {
    if (!pregnancy) return;

    $("#pregnancyMedication")
        .val(pregnancy.medication_during_pregnancy.toString())
        .trigger("change");
    $("#pregnancyMedicationExplanation").val(pregnancy.medication_note);

    $("#pregnancyIllness")
        .val(pregnancy.illness_during_pregnancy.toString())
        .trigger("change");
    $("#pregnancyIllnessExplanation").val(pregnancy.illness_note);

    $("#gestationalAge").val(pregnancy.gestational_age).trigger("change");
    $("#deliveryMethod").val(pregnancy.delivery_method).trigger("change");
    $("#birthWeight").val(pregnancy.birth_weight);
    $("#birthHeight").val(pregnancy.birth_height);
    $("#walkedAtAge").val(pregnancy.walk_age_month);
    $("#spokeAtAge").val(pregnancy.talk_age_month);
}

function mapMedicalHistory(history) {
    if (!history) return;

    $("#surgeryHistory")
        .val(history.surgery_history.toString())
        .trigger("change");
    $("#surgeryNote").val(history.surgery_note);

    $("#hospitalizationHistory")
        .val(history.hospitalization_history.toString())
        .trigger("change");
    $("#hospitalizationNote").val(history.hospitalization_note);

    $("#seizureHistory")
        .val(history.seizure_history.toString())
        .trigger("change");
    $("#seizureNote").val(history.seizure_note);

    $("#accidentHistory")
        .val(history.accident_history.toString())
        .trigger("change");
    $("#accidentNote").val(history.accident_note);
}

function mapImmunization(immunization) {
    if (!immunization) return;

    $(`input[name="immunizationBCG"][value="${immunization.bcg}"]`)
        .prop("checked", true)
        .trigger("change");
    $(`input[name="immunizationHepatitis"][value="${immunization.hepatitis}"]`)
        .prop("checked", true)
        .trigger("change");
    $(`input[name="immunizationDTP"][value="${immunization.dtp}"]`)
        .prop("checked", true)
        .trigger("change");
    $(`input[name="immunizationPolio"][value="${immunization.polio}"]`)
        .prop("checked", true)
        .trigger("change");
    $(`input[name="immunizationMeasles"][value="${immunization.measles}"]`)
        .prop("checked", true)
        .trigger("change");
    $(
        `input[name="immunizationHepatitisA"][value="${immunization.hepatitis_a}"]`,
    )
        .prop("checked", true)
        .trigger("change");
    $(`input[name="immunizationMMR"][value="${immunization.mmr}"]`)
        .prop("checked", true)
        .trigger("change");
    $(`input[name="immunizationInfluenza"][value="${immunization.influenza}"]`)
        .prop("checked", true)
        .trigger("change");
}

function mapHealth(health) {
    if (!health) return;

    $("#bloodType").val(health.blood_type).trigger("change");
    $("#foodAllergy").val(health.food_allergy.toString()).trigger("change");
    $("#foodAllergyExplanation").val(health.food_allergy_note);
    $("#drugAllergy").val(health.drug_allergy.toString()).trigger("change");
    $("#drugAllergyExplanation").val(health.drug_allergy_note);

    $(`input[name='usesGlasses'][value="${health.uses_glasses}"]`)
        .prop("checked", true)
        .trigger("change");
    $(`input[name="usesHearingAid"][value="${health.uses_hearing_aid}"]`)
        .prop("checked", true)
        .trigger("change");
    $(`input[name="routineMedication"][value="${health.routine_medication}"]`)
        .prop("checked", true)
        .trigger("change");
    $("#routineMedicationExplanation").val(health.routine_medication_note);
    $("#familyDiseaseHistory").val(health.family_disease_history);
    $("#referralFacility").val(health.referral_health_facility);
    $("#emergencyContactInfo").val(health.emergency_contact);
}
