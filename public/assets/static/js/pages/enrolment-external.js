let currentStep = 1;
const totalSteps = 5;
let levels = [];
let bankCharger = 0;

$(document).ready(function () {
    getAcademicYear();
    getBankCharger();

    $("#branch").on("change", function () {
        const id = $(this).val();
        getLevelsAndGrades(id);
        $(".row-price").addClass("d-none");
    });

    $("#level").on("change", function () {
        const id = $(this).val();
        const selectedLevel = levels.find((level) => level.id == id);
        $("#grade")
            .attr("disabled", false)
            .empty()
            .append('<option value="">Select grade...</option>');
        if (selectedLevel) {
            selectedLevel.grades.forEach((grade) => {
                $("#grade").append(
                    `<option value="${grade.id}">${grade.name}</option>`
                );
            });
        }

        const needsSocialMedia =
            selectedLevel.name.toLowerCase() === "lower secondary" ||
            selectedLevel.name.toLowerCase() === "upper secondary";

        if (needsSocialMedia) {
            $("#socialMediaWrapper").slideDown(300);
            $("#socialMedia").prop("required", true);
        } else {
            $("#socialMediaWrapper").slideUp(300);
            $("#socialMedia").prop("required", false).val("");
        }

        const branchId = $("#branch").val();
        if (id !== null || id !== "") {
            getPriceByBranchLevel(branchId, id);
        }
    });

    $("#hearAbout").on("change", function () {
        if ($(this).val() === "others") {
            $("#hear-other-group").slideDown(200);
            $("#hear-other-text").attr("required", true);
        } else {
            $("#hear-other-group").slideUp(200);
            $("#hear-other-text").removeAttr("required").val("");
        }
    });

    // Handle visit confirmation
    $('input[name="visitedBefore"]').change(function () {
        if ($(this).val() === "true") {
            $("#codeInputWrapper").slideDown(300);
            $("#visitCode").prop("required", true);
        } else {
            $("#codeInputWrapper").slideUp(300);
            $("#visitCode").prop("required", false).val("");
        }
    });

    $('input[name="currentMHIS"]').change(function () {
        if (
            $(this).val() === "yes" ||
            $(this).val() === "yes_sibling_registration"
        ) {
            $("#codeInputPortal").slideDown(300);
            $("#mhis-portal-username").prop("required", true);
            $("#mhis-portal-password").prop("required", true);
        } else {
            $("#codeInputPortal").slideUp(300);
            $("#mhis-portal-username").prop("required", false).val("");
            $("#mhis-portal-password").prop("required", false).val("");
        }
    });

    // Navigation
    $("#nextBtn").click(function () {
        if (validateStep(currentStep)) {
            if (currentStep === 1) {
                if ($("#visitCode").val() != "") {
                    getProspectByCode($("#visitCode").val());
                }
                if (
                    $("#mhis-portal-username").val() != "" &&
                    $("#mhis-portal-password").val() != ""
                ) {
                    const username = $("#mhis-portal-username").val();
                    const password = $("#mhis-portal-password").val();
                    getParentMHPortal(username, password);
                }
                currentStep++;
                showStep(currentStep);
            } else {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        }
    });

    $("#prevBtn").click(function () {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Back to Form button
    $("#backToFormBtn").click(function () {
        window.location.reload();
    });
});

function showStep(step) {
    $(".section-step").removeClass("active");
    $(`.section-step[data-step="${step}"]`).addClass("active");

    $(".step-item").removeClass("active");
    $(`.step-item[data-step="${step}"]`).addClass("active");

    for (let i = 1; i < step; i++) {
        $(`.step-item[data-step="${i}"]`).addClass("completed");
    }

    for (let i = step; i <= totalSteps; i++) {
        $(`.step-item[data-step="${i}"]`).removeClass("completed");
    }

    const progressPercent = ((step - 1) / (totalSteps - 1)) * 100;
    const progressLineWidth = `calc(${progressPercent}%)`;
    $("#progressLine").css("width", progressLineWidth);

    if (step === 1) {
        $("#prevBtn").hide();
    } else {
        $("#prevBtn").show();
    }

    if (step === 4) {
        $("#nextBtn")
            .html('<i class="fas fa-paper-plane"></i> Submit')
            .removeClass("btn-next")
            .addClass("btn-submit");
    } else if (step === 5) {
        $("#buttonGroup").hide();
    } else {
        $("#nextBtn")
            .html('Next <i class="fas fa-arrow-right"></i>')
            .removeClass("btn-submit")
            .addClass("btn-next");
        $("#buttonGroup").show();
    }
    // Smooth scroll to top
    $("html, body").animate({ scrollTop: 0 }, 300);
}

function validateStep(step) {
    let isValid = true;
    const currentSection = $(`.section-step[data-step="${step}"]`);

    // Remove previous validation messages
    currentSection.find(".alert-validation").remove();
    currentSection.find(".is-invalid").removeClass("is-invalid");

    // Validate required fields
    currentSection
        .find("input[required], select[required], textarea[required]")
        .each(function () {
            const field = $(this);

            if (field.attr("type") === "radio") {
                const name = field.attr("name");
                if (!$(`input[name="${name}"]:checked`).length) {
                    isValid = false;
                    $(`input[name="${name}"]`).first().addClass("is-invalid");
                }
            } else if (field.attr("type") === "email") {
                isValid = false;
                if (!validateEmail(field.val())) {
                    field.addClass("is-invalid");
                } else {
                    isValid = true;
                    field.removeClass("is-invalid");
                }
            } else if (field.attr("type") === "tel") {
                isValid = false;
                if (!validatePhone(field.val())) {
                    field.addClass("is-invalid");
                } else {
                    isValid = true;
                    field.removeClass("is-invalid");
                }
            } else if (!field.val()) {
                isValid = false;
                if (field.hasClass("select2-hidden-accessible")) {
                    field
                        .next(".select2")
                        .find(".select2-selection")
                        .addClass("is-invalid");
                } else {
                    field.addClass("is-invalid");
                }
            }
        });

    if (!isValid) {
        const alert = $(
            '<div class="alert alert-danger alert-validation" role="alert">' +
                '<i class="fas fa-exclamation-triangle"></i> Please fill in all required fields before proceeding.' +
                "</div>"
        );
        currentSection.prepend(alert);

        // Scroll to alert
        $("html, body").animate({ scrollTop: alert.offset().top - 100 }, 300);
    } else if (step === 4) {
        // Submit form
        submitForm();
    }

    return isValid;
}

function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validatePhone(phone) {
    const regex = /^(?:\+62|62|0)[0-9]{9,13}$/;
    return regex.test(phone);
}

function submitForm() {
    const formData = {
        alreadyVisit: $('input[name="visitedBefore"]:checked').val(),
        code: $("#visitCode").val(),
        prospectsId: $("#prospects_id").val(),
        branch: $("#branch").val(),
        level: $("#level").val(),
        grade: $("#grade").val(),
        academicYear: $("#academic-year").val(),
        parentName: $("#parentName").val(),
        email: $("#email").val(),
        phone: $("#phone").val(),
        relationship: $("#relationship").val(),
        zipCode: $("#zipCode").val(),
        address: $("#address").val(),
        childName: $("#childName").val(),
        placeOfBirth: $("#birthPlace").val(),
        dateOfBirth: moment($("#birthDate").val(), "DD MMMM YYYY").format(
            "YYYY-MM-DD"
        ),
        currentSchool: $("#currentSchool").val(),
        childSosmed: $("#socialMedia").val(),
        opendayVisited: $('input[name="openDay"]:checked').val(),
        knowledgeAboutProgram: $('input[name="knowProgram"]:checked').val(),
        infoFrom: $("#hearAbout").val(),
        infoFromMessage: $("#hear-other-text").val(),
        reasonForEnrolment: $("#enrollReason").val(),
        prefferedProgram: $("#bestProgram").val(),
        expectationMhisImpact: $('input[name="standards"]:checked').val(),
        recommenderName: $("#recommenderName").val(),
        recommenderPhone: $("#recommenderPhone").val(),
        recommenderChildName: $("#recommenderChildName").val(),
        recommenderChildClass: $("#recommenderChildClass").val(),
    };
    blockUI();
    ajax(
        formData,
        "/enrolment/external",
        "POST",
        function (json) {
            console.log(json);
            // currentStep = 5;
            // showStep(currentStep);
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    );
}

function getLevelsAndGrades(branchId) {
    blockUI();
    ajax(
        null,
        `/level/branch/${branchId}`,
        "GET",
        function (json) {
            $("#academic-year, #level").attr("disabled", false);
            $("#level")
                .empty()
                .append('<option value="">Select level...</option>');
            levels = json;
            levels.forEach((level) => {
                $("#level").append(
                    `<option value="${level.id}">${level.name}</option>`
                );
            });
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    );
}

function getProspectByCode(code) {
    blockUI();
    ajax(
        null,
        `/prospect/code/${code}`,
        "GET",
        function (json) {
            $("#prospects_id").val(json.id);
            $("#parentName").val(json.name);
            $("#email").val(json.email);
            $("#phone").val(json.phone);
            $("#relationship").val(json.relationship);
            $("#zipCode").val(json.zip_code);
            $("#address").val(json.address);
            $("#childName").val(json.child_name);
            $("#birthPlace").val(json.place_of_birth);
            $("#birthDate").val(
                moment(json.date_of_birth).format("DD MMMM YYYY")
            );
            $("#currentSchool").val(json.current_school);
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    );
}
function getParentMHPortal(username, password) {
    blockUI();
    ajax(
        null,
        `/mhis/portal/parent?username=${username}&password=${password}`,
        "GET",
        function (json) {
            console.log(json);
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    );
}

function getPriceByBranchLevel(branchId, levelId) {
    blockUI();
    ajax(
        null,
        `/price/branch/level/${branchId}/${levelId}`,
        "GET",
        function (json) {
            let price = parseFloat(json.price);
            $(".row-price").removeClass("d-none");
            $("#enrolment-form").text(formatNumber(price));
            let total = price + bankCharger;
            $("#total-form").text(formatNumber(total));
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    );
}

function getBankCharger() {
    ajax(
        null,
        "/bank/single",
        "GET",
        function (json) {
            bankCharger = parseFloat(json.price ?? 0);
            $("#bank-form").text(formatNumber(bankCharger));
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    );
}
