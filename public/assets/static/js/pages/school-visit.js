let levels = [];

$(document).ready(function () {
    getAcademicYear();
    setAnimationFadeout();
    getLevel();
    appendRules();
    appendTimes();

    $(".form-control, .form-select").on("input change", function () {
        if (this.checkValidity()) {
            $(this).removeClass("is-invalid").addClass("is-valid");
        } else {
            $(this).removeClass("is-valid").addClass("is-invalid");
        }
    });

    // Checkbox validation
    $('input[type="checkbox"]').change(function () {
        if (this.checked) {
            $(this).removeClass("is-invalid");
        }
    });

    $("#visit-date").on("changeDate", function () {
        const selectedDate = new Date(this.value);
        const dayOfWeek = selectedDate.getDay();
        if (dayOfWeek === 0) {
            this.setCustomValidity(
                "School visits are not available on Sundays. Please select Monday through Saturday."
            );
            $(this).addClass("is-invalid");
            changeVisitTime(true);
        } else {
            this.setCustomValidity("");
            $(this).removeClass("is-invalid");
            changeVisitTime(false);
        }
    });

    $("#visit-time").change(function () {
        const time = this.value;
        if (time < "07:30" || time > "15:00") {
            this.setCustomValidity(
                "Please select a time between 07:30 and 15:00."
            );
            $(this).addClass("is-invalid");
        } else {
            this.setCustomValidity("");
            $(this).removeClass("is-invalid");
        }
    });

    $("#visit-level").on("change", function () {
        $("#grade").attr("disabled", false);

        let levelId = $(this).val();
        const level = levels.find((l) => l.id == levelId);
        $("#grade").empty();
        $("#grade").append(`
                <option value="" selected disabled>Pilih Grade</option>  
            `);
        level.grades.forEach((val) => {
            $("#grade").append(`
                <option value="${val.id}">${val.name}</option>    
            `);
        });
    });

    $("#hear-about").on("change", function () {
        if ($(this).val() === "others") {
            $("#hear-other-group").slideDown(200);
            $("#hear-other-text").attr("required", true);
        } else {
            $("#hear-other-group").slideUp(200);
            $("#hear-other-text").removeAttr("required").val("");
        }
    });
    $(".checkbox-group").on("change", ".rules", function () {
        let id = $(this).attr("id");
        let value = $(this).prop("checked");
        schoolVisitForm.rules.forEach((val) => {
            if (val.rule === id) {
                val.checked = value;
            }
        });
    });

    $("#visit-form").on("submit", function (e) {
        e.preventDefault();
        const form = this;

        let trueRules = schoolVisitForm.rules.filter(
            (val) => val.checked == false
        );
        if (trueRules.length > 0) {
            $(".checkbox-group").addClass("border-danger");
            return false;
        } else {
            $(".checkbox-group").removeClass("border-danger");
        }

        if (!validateRequiredSelect2()) {
            return false;
        }
        if (!validateRadio()) {
            return false;
        }

        if (form.checkValidity()) {
            $("#submit-btn").attr("disabled", true);
            blockUI();
            let data = $(form).serializeArray();
            let dataJSON = {};
            data.forEach((val) => {
                dataJSON[val.name] = val.value;
            });
            dataJSON.date = moment(dataJSON.date, "DD MMMM YYYY").format(
                "YYYY-MM-DD"
            );
            dataJSON.level_name = $("#visit-level option:selected").text();
            dataJSON.grade_name = $("#grade option:selected").text();
            dataJSON.branch_name = $("#branch option:selected").text();
            dataJSON.roles = schoolVisitForm.rules;
            postSchoolVisit(dataJSON);
        }

        $(this).addClass("was-validated");
    });
});

function appendRules() {
    schoolVisitForm.rules.forEach((val) => {
        $(".checkbox-group").append(`
            <div class="checkbox-item">
                <input class="rules" type="checkbox" id="${val.rule}" required />
                <label for="${val.rule}">${val.value}</label>
            </div>
        `);
    });
}
function appendTimes() {
    schoolVisitForm.times.forEach((val) => {
        $("#visit-time").append(`
            <option value="${val}">${val}</option>
        `);
    });
}

function changeVisitTime(isDisabled) {
    $("#visit-time").attr("disabled", isDisabled);
    $("#visit-time").val("").trigger("change");
}

function getAcademicYear() {
    const currentYear = new Date().getFullYear();
    const academicYearSelect = $("#academic-year");

    for (let i = 0; i <= 3; i++) {
        const startYear = currentYear + i;
        const endYear = startYear + 1;
        const yearOption = `${startYear}/${endYear}`;
        academicYearSelect.append(
            `<option value="${yearOption}">${yearOption}</option>`
        );
    }
}

function setAnimationFadeout() {
    $("#next-btn").click(function () {
        $("#intro-section").fadeOut(400, function () {
            $("#form-section").fadeIn(600);
            $("html, body").animate({ scrollTop: 0 }, 300);
        });
    });

    $("#previous-btn").click(function () {
        $("#form-section").fadeOut(400, function () {
            $("#intro-section").fadeIn(600);
            $("html, body").animate({ scrollTop: 0 }, 300);
        });
    });
}

function getLevel() {
    ajax(
        null,
        `/level/get`,
        "GET",
        function (json) {
            levels = json;
            levels.forEach((val) => {
                $("#visit-level").append(`
                <option value="${val.id}">${val.name}</option>
            `);
            });
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "error"
            );
        }
    );
}

function postSchoolVisit(data) {
    ajax(
        data,
        `/school-visit-post`,
        "POST",
        function (json) {
            $("#submit-btn").attr("disabled", false);
            toastify("success", "your data has been submitted", "bottom");
            setTimeout(() => {
                window.location.href = "/schoolvisit-success";
            }, 1500);
        },
        function (err) {
            $("#submit-btn").attr("disabled", false);
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom"
            );
        }
    );
}
