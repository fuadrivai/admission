let admission = null;
let currentStep = 1;
let totalSteps = 6;

// const studentLevel = "SD";
const studentLevel = "Upper Secondary";

const sampleData = {
    father: {
        name: "Ahmad Sudirman",
        email: "ahmad.sudirman@email.com",
        phone: "0812 3456 7890",
        birthPlace: "Jakarta",
        birthDate: "15 Januari 1980",
        idCard: "1234567890123456",
    },
    mother: {
        name: "Siti Fatimah",
        email: "siti.fatimah@email.com",
        phone: "0813 4567 8901",
        birthPlace: "Bandung",
        birthDate: "20 Maret 1982",
        idCard: "2345678901234567",
    },
    guardian: {
        name: "Budi Santoso",
        email: "budi.santoso@email.com",
        phone: "0814 5678 9012",
        birthPlace: "Surabaya",
        birthDate: "10 Mei 1975",
        idCard: "3456789012345678",
    },
    student: {
        fullName: "Muhammad Al-Fatih Sudirman",
        age: "7 tahun 2 bulan",
        level: studentLevel,
        grade: "Kelas 1",
    },
};
$(document).ready(function () {
    getAdmissionByCode();

    $("#parentSelector").on("change", async function () {
        const parentInfoCard = $("#parentInfoCard");
        let role = $(this).val();
        let parent = await getParentByRole(role);
        const dobRaw = parent.birth_date;
        const dob = dobRaw
            ? moment(dobRaw, "DD MMMM YYYY").format("YYYY-MM-DD")
            : null;
        $("#parentFullName").text(parent.fullname ?? "--");
        $("#parentEmail").text(parent.email ?? "--");
        $("#parentPhone").text(parent.phone ?? "--");
        $("#parentBirthPlace").text(parent.birth_place ?? "--");
        $("#parentBirthDate").text(dob ?? "--");
        $("#parentIdCard").text(parent.identity_number ?? "--");
        parentInfoCard.slideDown(300);
        validateCurrentStep();
    });

    $("#currentDate1").text(formatCurrentDate());
    $("#currentDate2").text(formatCurrentDate());
    $("#currentDate3").text(formatCurrentDate());
    $("#currentDate4").text(formatCurrentDate());
    $("#currentDate5").text(formatCurrentDate());

    $("#studentFullName2").text(sampleData.student.fullName);
    $("#studentAge2").text(sampleData.student.age);
    $("#studentLevel2").text(sampleData.student.level);
    $("#studentGrade2").text(sampleData.student.grade);

    $("#studentFullName3").text(sampleData.student.fullName);
    $("#studentAge3").text(sampleData.student.age);
    $("#studentLevel3").text(sampleData.student.level);
    $("#studentGrade3").text(sampleData.student.grade);

    // Initialize money inputs
    $("#developmentFee, #annualFee, #schoolFee").on("input", function () {
        formatMoneyInput($(this));
        validateCurrentStep();
    });

    // Initialize with example values
    setTimeout(() => {
        $("#developmentFee").val("12,500,000").trigger("input");
        $("#annualFee").val("4,000,000").trigger("input");
        $("#schoolFee").val("1,750,000").trigger("input");
    }, 100);

    // Checkbox validation
    $('input[type="checkbox"][required]').on("change", function () {
        validateCurrentStep();
    });

    // Navigation handlers
    $("#next-btn").on("click", nextStep);
    $("#prev-btn").on("click", prevStep);
    $("#final-submit-btn").on("click", submitForm);

    // Form validation on input
    $("input, select, textarea").on("input change", function () {
        validateCurrentStep();
    });

    // Initialize step validation
    validateCurrentStep();

    // Handle conditional sections based on student level
    if (sampleData.student.level === "Upper Secondary") {
        $("#step-5, #step-6")
            .removeClass("conditional-section")
            .addClass("step-content");
        totalSteps = 6;
    } else {
        $("#step-5, #step-6").addClass("conditional-section");
        totalSteps = 4;

        // Update step indicator
        $('.step[data-step="5"], .step[data-step="6"]').hide();

        // Adjust step widths
        $(".step").css("flex", "0 0 25%");
    }
});

function formatCurrency(number) {
    return new Intl.NumberFormat("id-ID").format(number);
}

function formatCurrentDate() {
    const now = new Date();
    const day = now.getDate();
    const month = now.toLocaleString("id-ID", {
        month: "long",
    });
    const year = now.getFullYear();
    return `${day} ${month} ${year}`;
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

function nextStep() {
    if (!validateCurrentStep()) {
        return;
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
    $(".main-container").animate(
        {
            scrollTop: 0,
        },
        300
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
    $(".main-container").animate(
        {
            scrollTop: 0,
        },
        300
    );

    // Validate step
    validateCurrentStep();
}

function submitForm() {
    if (!validateCurrentStep()) {
        alert(
            "Harap lengkapi semua bidang yang diperlukan sebelum mengirimkan."
        );
        return;
    }

    // Collect form data
    const formData = {
        parentSelected: $("#parentSelector").val(),
        parentName: $("#parentFullName").text(),
        developmentFee: $("#developmentFee").val(),
        annualFee: $("#annualFee").val(),
        schoolFee: $("#schoolFee").val(),
        studentName: sampleData.student.fullName,
        studentLevel: sampleData.student.level,
        submissionDate: formatCurrentDate(),
        agreements: {
            payment: {
                agree1: $("#agreePayment1").is(":checked"),
                agree2: $("#agreePayment2").is(":checked"),
                agree3: $("#agreePayment3").is(":checked"),
                agree4: $("#agreePayment4").is(":checked"),
                agree5: $("#agreePayment5").is(":checked"),
                agree6: $("#agreePayment6").is(":checked"),
                agree7: $("#agreePayment7").is(":checked"),
                agree8: $("#agreePayment8").is(":checked"),
                agree9: $("#agreePayment9").is(":checked"),
                agree10: $("#agreePayment10").is(":checked"),
                agree11: $("#agreePayment11").is(":checked"),
                agree12: $("#agreePayment12").is(":checked"),
            },
            statement: {
                agree1: $("#agreeStatement1").is(":checked"),
                agree2: $("#agreeStatement2").is(":checked"),
            },
            narkotika:
                sampleData.student.level === "Upper Secondary"
                    ? $("#agreeNarkotika").is(":checked")
                    : null,
            student:
                sampleData.student.level === "Upper Secondary"
                    ? $("#agreeStudent").is(":checked")
                    : null,
        },
    };

    console.log("Form submitted with data:", formData);

    // Simulate submission
    $("#final-submit-btn").html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...'
    );
    $("#final-submit-btn").prop("disabled", true);

    setTimeout(() => {
        $("#final-submit-btn").html(
            '<i class="bi bi-send-check"></i> Kirim Formulir Persetujuan'
        );
        $("#final-submit-btn").prop("disabled", false);

        // Show success message
        alert("Formulir persetujuan berhasil dikirim! Terima kasih.");

        // In a real app, you would redirect or show a success page
        // window.location.href = 'success.html';
    }, 2000);
}

async function getAdmissionByCode() {
    blockUI();
    const code = $("#admission-code").val();
    admission = await ajaxPromise(null, `/admission/code/${code}`, "GET");

    $("#studentFullName").text(admission.applicant.fullname);
    $("#studentAge").text(admission.applicant.age);
    $("#studentLevel").text(admission.level.name);
    $("#studentGrade").text(admission.grade.name);
    $("#academicYear").text(admission.accademic_year);
}

async function getParentByRole(role) {
    blockUI();
    let parent = await ajaxPromise(
        null,
        `/admission/parent/${admission.applicant.id}/${role}`,
        "GET"
    );

    return parent;
}
