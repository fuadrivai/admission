let admission = null;
let statement = null;
let currentStep = 1;
let totalSteps = 5;

$(document).ready(function () {
    checkAdmissionByCode();

    $("#parentSelector").on("change", async function () {
        const parentInfoCard = $("#parentInfoCard");
        let role = $(this).val();
        let parent = await getParentByRole(role);
        const dobRaw = parent.birth_date;
        const dob = dobRaw
            ? moment(dobRaw, "YYYY-MM-DD").format("DD MMMM YYYY")
            : null;
        const datePlace = `${parent.birth_place ?? "--"}, ${dob}`;
        $(".parentFullName").text(parent.fullname ?? "--");
        $(".parentEmail").text(parent.email ?? "--");
        $(".parentPhone").text(parent.phone ?? "--");
        $(".parentBirthPlace").text(datePlace);
        $(".parentBirthDate").text(dob ?? "--");
        $(".parentIdCard").text(parent.identity_number ?? "--");
        parentInfoCard.slideDown(300);
        validateCurrentStep();
    });

    $("#developmentFee, #annualFee, #schoolFee, #ittihada, #mhsu, #uniform").on(
        "input",
        function () {
            formatMoneyInput($(this));
            validateCurrentStep();
        },
    );

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

    currentStepElement
        .find(".validation-error")
        .removeClass("validation-error");
    currentStepElement.find(".error-message").hide();
    $("#parent-statement-required-error").addClass("d-none").hide();

    currentStepElement
        .find("input[required], select[required], textarea[required]")
        .each(function () {
            const field = $(this);
            const errorElement = $(`#${field.attr("id")}-error`);

            if (field.is('input[type="checkbox"]')) {
                if (!field.is(":checked")) {
                    isValid = false;
                    field.addClass("validation-error");
                    if (errorElement.length) {
                        errorElement.show();
                    }
                }
            } else if (field.is("select")) {
                if (!field.val()) {
                    isValid = false;
                    field.addClass("validation-error");
                    if (errorElement.length) {
                        errorElement.show();
                    }
                }
            } else if (field.is('input[type="text"]')) {
                if (!field.val().trim()) {
                    isValid = false;
                    field.addClass("validation-error");
                    if (errorElement.length) {
                        errorElement.show();
                    }
                }
            }
        });

    if (currentStep === 2) {
        const requiredParentItems = currentStepElement.find(
            ".parent-statement-item[required]",
        );
        if (requiredParentItems.length) {
            const missing = requiredParentItems.filter(function () {
                return !$(this).is(":checked");
            });
            if (missing.length) {
                isValid = false;
                missing.addClass("validation-error");
                $("#parent-statement-required-error")
                    .removeClass("d-none")
                    .show();
            }
        }
    }

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

    try {
        if (currentStep == 1) {
            await postStatement(false);
            if (admission.statement.financial) {
                await getFinancialStatement();
            }
        }

        if (currentStep == 2) {
            await saveParentAgreement();
        }
        if (currentStep == 3) {
            await postFinancial();
            await getAgreement("narcotica");
        }
        if (currentStep == 4) {
            await postAgreement("narcotica");
            await getAgreement("student");
        }
        if (currentStep == 5) {
            await postAgreement("student");
        }
    } catch (err) {
        toastify(
            "Error",
            err?.responseJSON?.message ??
                err?.message ??
                "Please try again later",
            "bottom",
        );
        return;
    }

    $(`.step[data-step="${currentStep}"]`)
        .removeClass("active")
        .addClass("completed");

    $(`#step-${currentStep}`).removeClass("active");
    currentStep++;
    $(`.step[data-step="${currentStep}"]`).addClass("active");
    $(`#step-${currentStep}`).addClass("active");

    $("#prev-btn").prop("disabled", false);
    $("html, body").animate({ scrollTop: 0 }, 300);
    validateCurrentStep();
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

    $("html, body").animate({ scrollTop: 0 }, 300);
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

    toastify("success", "Formulir persetujuan telah berhasil dikirim.");
    setTimeout(() => {
        $(".final-submit-btn").html(
            '<i class="bi bi-send-check"></i> Kirim Formulir Persetujuan',
        );
        $(".final-submit-btn").prop("disabled", false);
        window.location.href = `/document/success/${admission.code}`;
    }, 500);
}

async function checkAdmissionByCode() {
    try {
        blockUI();
        const code = $("#admission-code").val().trim();
        const path = await ajaxPromise(null, `/document/check/${code}`, "GET");
        switch (path) {
            case "student":
                window.location.href = `/document/${path}?code=${code}`;
                break;
            default:
                if (path != "statement") {
                    window.location.href = `/document/${path}/${code}`;
                    return false;
                } else {
                    getAdmissionByCode();
                    return true;
                }
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
    blockUI();
    const code = $("#admission-code").val();
    admission = await ajaxPromise(null, `/document/code/${code}`, "GET");

    $(".studentFullName").text(admission.applicant.fullname);
    $(".studentAge").text(admission.applicant.age);
    $(".studentLevel").text(`${admission.level.name}/${admission.grade.name}`);
    $(".studentGrade").text(admission.grade.name);
    $(".academicYear").text(admission.accademic_year);
    if (admission.statement) {
        statement = admission.statement;
        $("#parentSelector")
            .val(admission?.statement?.actor ?? "")
            .trigger("change");
        loadParentAgreementState();
    }

    if (admission.level.division.name.toLowerCase() !== "secondary") {
        $(".secondary").removeClass("col-md-6").addClass("col-md-4");
        $(".div-mhsu").addClass("d-none");
        $(".div-mhsu input").prop("required", false);
    }

    if (admission.level.name === "Upper Secondary") {
        $("#step-5, #step-4")
            .removeClass("conditional-section")
            .addClass("step-content");
        totalSteps = 5;
        $("#btn-under-upper-secondary").addClass("d-none");
    } else {
        $("#step-4, #step-5").addClass("conditional-section");
        totalSteps = 3;
        $('.step[data-step="5"], .step[data-step="4"]').hide();
        $(".step").css("flex", "0 0 25%");
        $("#btn-under-upper-secondary").removeClass("d-none");
    }

    validateCurrentStep();
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

async function loadParentAgreementState() {
    if (!admission?.statement?.id) {
        return;
    }

    const agreementIds = await ajaxPromise(
        null,
        `/document/statement/parent-agreement/${admission.statement.id}`,
        "GET",
    );

    $(".parent-statement-item").each(function () {
        const itemId = Number($(this).val());
        $(this).prop("checked", agreementIds.includes(itemId));
    });

    validateCurrentStep();
}

async function saveParentAgreement() {
    const selected = $(".parent-statement-item:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    const requiredParentItems = $(".parent-statement-item[required]");
    if (
        requiredParentItems.length &&
        selected.length !== requiredParentItems.length
    ) {
        throw new Error(
            "Please check all required parent statement items before continuing.",
        );
    }

    blockUI();
    await ajaxPromise(
        {
            admission_statement_id: admission.statement.id,
            statement_item_id: selected,
        },
        "/document/statement/parent-agreement",
        "POST",
    );
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
    const financialAgreementAccepted = $("#agreeFinancialDocument").is(
        ":checked",
    );
    const data = {
        id: financial?.id ?? null,
        admission_statement_id: admission.statement.id,
        financial_document_id: Number(
            $("#agreeFinancialDocument").data("document-id"),
        ),
        agree_financial_document: financialAgreementAccepted,
        agree_full_payment_terms: financialAgreementAccepted,
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
        ittihada_fee: parseFloat(
            $("#ittihada")
                .val()
                .replace(/[^0-9]/g, ""),
        ),
        uniform_fee: parseFloat(
            $("#uniform")
                .val()
                .replace(/[^0-9]/g, ""),
        ),
        agree_development_fee_policy: financialAgreementAccepted,
        agree_annual_and_school_fee_policy: financialAgreementAccepted,
        agree_exam_fee: financialAgreementAccepted,
        agree_learning_material_fee: financialAgreementAccepted,
        agree_exschool_fee: financialAgreementAccepted,
        agree_additional_activity_fee: financialAgreementAccepted,
        agree_monthly_school_fee_payment: financialAgreementAccepted,
        agree_ittihada_fee: financialAgreementAccepted,
        agree_full_financial_obligation: financialAgreementAccepted,
        agree_financial_terms_and_consequences: financialAgreementAccepted,
        agree_truth_and_consent: financialAgreementAccepted,
    };

    if (admission.level.division.name.toLowerCase() == "secondary") {
        data.mhsu_fee = parseFloat(
            $("#mhsu")
                .val()
                .replace(/[^0-9]/g, ""),
        );
    }

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

    const legacyAgreementFlags = [
        financial.agree_full_payment_terms,
        financial.agree_development_fee_policy,
        financial.agree_annual_and_school_fee_policy,
        financial.agree_exam_fee,
        financial.agree_learning_material_fee,
        financial.agree_exschool_fee,
        financial.agree_additional_activity_fee,
        financial.agree_monthly_school_fee_payment,
        financial.agree_ittihada_fee,
        financial.agree_full_financial_obligation,
        financial.agree_financial_terms_and_consequences,
        financial.agree_truth_and_consent,
    ];
    $("#agreeFinancialDocument").prop(
        "checked",
        Number(financial.financial_document_id) ===
            Number($("#agreeFinancialDocument").data("document-id")) &&
            legacyAgreementFlags.every(
                (value) => value === true || value === 1,
            ),
    );
    $("#developmentFee").val(formatCurrency(financial.development_fee));
    formatMoneyInput($("#developmentFee"));
    $("#annualFee").val(formatCurrency(financial.annual_fee));
    formatMoneyInput($("#annualFee"));
    $("#schoolFee").val(formatCurrency(financial.school_fee));
    formatMoneyInput($("#schoolFee"));

    $("#ittihada").val(formatCurrency(financial.ittihada_fee));
    formatMoneyInput($("#ittihada"));
    $("#mhsu").val(formatCurrency(financial.mhsu_fee));
    formatMoneyInput($("#mhsu"));
    $("#uniform").val(formatCurrency(financial.uniform_fee));
    formatMoneyInput($("#uniform"));
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
