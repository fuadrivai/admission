let admission = null;
let statement = null;
let currentStep = 1;
let totalSteps = 5;

$(document).ready(function () {
    getAdmissionByCode();

    $("#parentSelector").on("change", async function () {
        const parentInfoCard = $("#parentInfoCard");
        let role = $(this).val();
        let parent = await getParentByRole(role);
        const dobRaw = parent.birth_date;
        const dob = dobRaw
            ? moment(dobRaw, "YYYY-MM-DD").format("DD MMMM YYYY")
            : null;
        $(".parentFullName").text(parent.fullname ?? "--");
        $(".parentEmail").text(parent.email ?? "--");
        $(".parentPhone").text(parent.phone ?? "--");
        $(".parentBirthPlace").text(parent.birth_place ?? "--");
        $(".parentBirthDate").text(dob ?? "--");
        $(".parentIdCard").text(parent.identity_number ?? "--");
        parentInfoCard.slideDown(300);
        validateCurrentStep();
    });

    $("#developmentFee, #annualFee, #schoolFee").on("input", function () {
        formatMoneyInput($(this));
        validateCurrentStep();
    });

    $('input[type="checkbox"][required]').on("change", function () {
        validateCurrentStep();
    });

    $("#next-btn").on("click", nextStep);
    $("#prev-btn").on("click", prevStep);
    $(".final-submit-btn").on("click", submitForm);

    $("input, select, textarea").on("input change", function () {
        validateCurrentStep();
    });
});

function formatCurrency(number) {
    return new Intl.NumberFormat("id-ID").format(number);
}

function formatCurrentDate() {
    return moment().format("DD MMMM YYYY");
}

function formatMoneyInput(input) {
    let value = input.val().replace(/[^0-9]/g, "");
    if (value) {
        const numberValue = parseInt(value);
        input.val(formatCurrency(numberValue));

        // Update terbilang
        const inputId = input.attr("id");
        const terbilangId = inputId + "Terbilang";
        $("#" + terbilangId).text(convertToTerbilang(numberValue));
    } else {
        const terbilangId = input.attr("id") + "Terbilang";
        $("#" + terbilangId).text("-");
    }
}

function validateCurrentStep() {
    const currentStepElement = $(`#step-${currentStep}`);
    let isValid = true;

    // Reset error states
    currentStepElement
        .find(".validation-error")
        .removeClass("validation-error");
    currentStepElement.find(".error-message").hide();

    // Check all required fields in current step
    currentStepElement
        .find("input[required], select[required], textarea[required]")
        .each(function () {
            const field = $(this);
            const errorElement = $(`#${field.attr("id")}-error`);

            // Validate based on field type
            if (field.is('input[type="checkbox"]')) {
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
            } else if (field.is('input[type="text"]')) {
                if (!field.val().trim()) {
                    isValid = false;
                    field.addClass("validation-error");
                    errorElement.show();
                }
            }
        });

    // Update Next button state
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
        await postStatement(false);
        if (admission.statement.financial) {
            await getFinancialStatement();
        }
    }

    if (currentStep == 2) {
        await postFinancial();
        await getAgreement("parent");
    }
    if (currentStep == 3) {
        await postAgreement("parent");
        await getAgreement("guardian");
    }
    // if (currentStep == 4) {
    //     await postAgreement("guardian");
    //     await getAgreement("narcotica");
    // }
    if (currentStep == 4) {
        await postAgreement("narcotica");
        await getAgreement("student");
    }
    if (currentStep == 5) {
        await postAgreement("student");
    }

    // Mark current step as completed
    $(`.step[data-step="${currentStep}"]`)
        .removeClass("active")
        .addClass("completed");

    // Hide current step content
    $(`#step-${currentStep}`).removeClass("active");

    // Increment step
    currentStep++;

    // Update step indicator
    $(`.step[data-step="${currentStep}"]`).addClass("active");

    // Show next step content
    $(`#step-${currentStep}`).addClass("active");

    // Update navigation buttons
    $("#prev-btn").prop("disabled", false);

    // Scroll to top of step
    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300,
    );

    // Validate new step
    validateCurrentStep();
}

function prevStep() {
    // Mark current step as not completed
    $(`.step[data-step="${currentStep}"]`)
        .removeClass("active")
        .removeClass("completed");

    // Hide current step content
    $(`#step-${currentStep}`).removeClass("active");

    // Decrement step
    currentStep--;

    // Update step indicator
    $(`.step[data-step="${currentStep}"]`)
        .addClass("active")
        .removeClass("completed");

    // Show previous step content
    $(`#step-${currentStep}`).addClass("active");

    // Update navigation buttons
    $("#prev-btn").prop("disabled", currentStep === 1);
    $("#next-btn").show().prop("disabled", false);

    // Scroll to top of step
    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300,
    );

    // Validate step
    validateCurrentStep();
}

async function submitForm() {
    blockUI();
    $(".final-submit-btn").html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...',
    );
    $(".final-submit-btn").prop("disabled", true);

    let type = totalSteps == 5 ? "student" : "parent";
    await postAgreement(type);
    let statement = await postStatement(true);

    setTimeout(() => {
        $(".final-submit-btn").html(
            '<i class="bi bi-send-check"></i> Kirim Formulir Persetujuan',
        );
        $(".final-submit-btn").prop("disabled", false);
        toastify("success", "Formulir persetujuan telah berhasil dikirim.");
    }, 500);
}

async function getAdmissionByCode() {
    blockUI();
    const code = $("#admission-code").val();
    admission = await ajaxPromise(null, `/document/code/${code}`, "GET");

    $(".studentFullName").text(admission.applicant.fullname);
    $(".studentAge").text(admission.applicant.age);
    $(".studentLevel").text(admission.level.name);
    $(".studentGrade").text(admission.grade.name);
    $(".academicYear").text(admission.accademic_year);
    if (admission.statement) {
        statement = admission.statement;
        $("#parentSelector")
            .val(admission?.statement?.actor ?? "")
            .trigger("change");
    }

    if (admission.level.name === "Upper Secondary") {
        $("#step-5, #step-4")
            .removeClass("conditional-section")
            .addClass("step-content");
        totalSteps = 5;
        $("#btn-under-upper-secondary").addClass("d-none");
        validateCurrentStep();
    } else {
        $("#step-4, #step-5").addClass("conditional-section");
        totalSteps = 3;
        $('.step[data-step="5"], .step[data-step="4"]').hide();
        $(".step").css("flex", "0 0 25%");
        $("#btn-under-upper-secondary").removeClass("d-none");
        validateCurrentStep();
    }
}

async function getParentByRole(role) {
    blockUI();
    let parent = await ajaxPromise(
        null,
        `/document/parent/${admission.applicant.id}/${role}`,
        "GET",
    );

    return parent;
}

async function postStatement(isComplete) {
    const data = {
        id: admission.statement ? admission.statement.id : null,
        admission_id: admission.id,
        actor: $("#parentSelector").val(),
        identity_number: $("#parentIdCard").text(),
        fullname: $("#parentFullName").text(),
        is_completed: isComplete,
    };
    blockUI();
    let statement = await ajaxPromise(data, `/document/statement`, "POST");
    admission.statement = statement;
}

async function postFinancial() {
    let financial = admission?.statement?.financial;
    const data = {
        id: financial?.id ?? null,
        admission_statement_id: admission.statement.id,
        agree_full_payment_terms: $("#agreePayment1").is(":checked"),
        development_fee: parseFloat(
            $("#developmentFee")
                .val()
                .replace(/[^0-9]/g, ""),
        ),
        annual_fee: parseFloat(
            $("#annualFee")
                .val()
                .replace(/[^0-9]/g, ""),
        ),
        school_fee: parseFloat(
            $("#schoolFee")
                .val()
                .replace(/[^0-9]/g, ""),
        ),
        agree_development_fee_policy: $("#agreePayment2").is(":checked"),
        agree_annual_and_school_fee_policy: $("#agreePayment3").is(":checked"),
        agree_exam_fee: $("#agreePayment4").is(":checked"),
        agree_learning_material_fee: $("#agreePayment5").is(":checked"),
        agree_exschool_fee: $("#agreePayment6").is(":checked"),
        agree_additional_activity_fee: $("#agreePayment7").is(":checked"),
        agree_monthly_school_fee_payment: $("#agreePayment8").is(":checked"),
        agree_ittihada_fee: $("#agreePayment9").is(":checked"),
        agree_full_financial_obligation: $("#agreePayment10").is(":checked"),
        agree_financial_terms_and_consequences:
            $("#agreePayment11").is(":checked"),
        agree_truth_and_consent: $("#agreePayment12").is(":checked"),
    };
    blockUI();
    try {
        financial = await ajaxPromise(
            data,
            `/document/statement/financial`,
            "POST",
        );
        admission.statement.financial = financial;
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ?? "Please try again later",
            "bottom",
        );
    }
}

async function getFinancialStatement() {
    const id = admission?.statement?.financial?.id ?? null;
    let financial = await ajaxPromise(
        null,
        `/document/statement/financial/${id}`,
        "GET",
    );

    $("#agreePayment1").prop("checked", financial.agree_full_payment_terms);
    $("#developmentFee").val(formatCurrency(financial.development_fee));
    formatMoneyInput($("#developmentFee"));
    $("#annualFee").val(formatCurrency(financial.annual_fee));
    formatMoneyInput($("#annualFee"));
    $("#schoolFee").val(formatCurrency(financial.school_fee));
    formatMoneyInput($("#schoolFee"));
    $("#agreePayment2").prop("checked", financial.agree_development_fee_policy);
    $("#agreePayment3").prop(
        "checked",
        financial.agree_annual_and_school_fee_policy,
    );
    $("#agreePayment4").prop("checked", financial.agree_exam_fee);
    $("#agreePayment5").prop("checked", financial.agree_learning_material_fee);
    $("#agreePayment6").prop("checked", financial.agree_exschool_fee);
    $("#agreePayment7").prop(
        "checked",
        financial.agree_additional_activity_fee,
    );
    $("#agreePayment8").prop(
        "checked",
        financial.agree_monthly_school_fee_payment,
    );
    $("#agreePayment9").prop("checked", financial.agree_ittihada_fee);
    $("#agreePayment10").prop(
        "checked",
        financial.agree_full_financial_obligation,
    );
    $("#agreePayment11").prop(
        "checked",
        financial.agree_financial_terms_and_consequences,
    );
    $("#agreePayment12").prop("checked", financial.agree_truth_and_consent);
}

async function postAgreement(type) {
    const data = {
        admission_statement_id: admission.statement
            ? admission.statement.id
            : null,
        id: $(`#${type}AgreeStatementId`).val() || null,
        type: type,
        agreed: $(`#${type}AgreeStatement`).is(":checked"),
    };
    blockUI();
    let agreement = await ajaxPromise(
        data,
        `/document/statement/agreement`,
        "POST",
    );
    $(`#${type}AgreeStatementId`).val(agreement.id);
}
async function getAgreement(type) {
    blockUI();
    let agreement = await ajaxPromise(
        null,
        `/document/statement/${admission.statement.id}/agreement/${type}`,
        "GET",
    );
    $(`#${type}AgreeStatement`).prop("checked", agreement.agreed);
}
