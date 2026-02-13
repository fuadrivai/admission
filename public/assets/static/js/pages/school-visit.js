let levels = [];

$(document).ready(function () {
    getAcademicYear();
    setAnimationFadeout();
    appendRules();
    appendTimes();

    // Initially disable next-btn
    $("#next-btn").prop("disabled", true);

    // Add event listener to radios
    $('input[name="already_enrol"]').change(function () {
        $("#next-btn").prop("disabled", false);
        if ($(this).val() === "yes") {
            $("#enrollment-code-group").slideDown(200);
            $("#enrollment-code").attr("required", true);
        } else {
            $("#enrollment-code-group").slideUp(200);
            $("#enrollment-code").removeAttr("required").val("");
        }
    });

    // Remove invalid class on input
    $("#enrollment-code").on("input", function () {
        $(this).removeClass("is-invalid");
    });

    $(".form-control, .form-select").on("input change", function () {
        if (this.checkValidity()) {
            $(this).removeClass("is-invalid").addClass("is-valid");
        } else {
            $(this).removeClass("is-valid").addClass("is-invalid");
        }
    });

    $("#branch").on("change", function () {
        const id = $(this).val();
        getLevelsAndGrades(id);
    });

    $("#visit-level").on("change", function () {
        const id = $(this).val();
        const selectedLevel = levels.find((level) => level.id == id);
        $("#grade")
            .attr("disabled", false)
            .empty()
            .append('<option value="">Select grade...</option>');
        if (selectedLevel) {
            selectedLevel.grades.forEach((grade) => {
                $("#grade").append(
                    `<option value="${grade.id}">${grade.name}</option>`,
                );
            });
        }
    });

    // Checkbox validation
    $('input[type="checkbox"]').change(function () {
        if (this.checked) {
            $(this).removeClass("is-invalid");
        }
    });

    $("#visit-date").datepicker("setStartDate", new Date());

    $("#visit-date").on("changeDate", function () {
        let visitDate = moment($(this).val(), "DD MMMM YYYY").format(
            "YYYY-MM-DD",
        );
        const selectedDate = new Date(this.value);
        const dayOfWeek = selectedDate.getDay();
        if (dayOfWeek === 0) {
            this.setCustomValidity(
                "School visits are not available on Sundays. Please select Monday through Saturday.",
            );
            $(this).addClass("is-invalid");
            changeVisitTime(true);
        } else {
            checkHoliday(visitDate, this);
        }
    });

    $("#visit-time").change(function () {
        const time = this.value;
        if (time < "07:30" || time > "15:00") {
            this.setCustomValidity(
                "Please select a time between 07:30 and 15:00.",
            );
            $(this).addClass("is-invalid");
        } else {
            let date = moment($("#visit-date").val(), "DD MMMM YYYY").format(
                "YYYY-MM-DD",
            );
            checkCapacity(date, `${time}:00`, this);
        }
    });

    $("#visit-level").on("change", function () {
        $("#grade").attr("disabled", false);

        let levelId = $(this).val();
        const level = levels.find((l) => l.id == levelId);
        $("#grade").empty();
        $("#grade").append(`
                <option value="" selected disabled>Select a grade</option>  
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
            (val) => val.checked == false,
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
            dataJSON.already_enrol = $(
                'input[name="already_enrol"]:checked',
            ).val();
            if (dataJSON.already_enrol === "yes") {
                dataJSON.code = $("#enrollment-code").val();
                dataJSON.prospects_id = $("#prospects_id").val();
            }
            dataJSON.date = moment(dataJSON.date, "DD MMMM YYYY").format(
                "YYYY-MM-DD",
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

function setAnimationFadeout() {
    $("#next-btn").click(function () {
        const selectedRadio = $('input[name="already_enrol"]:checked');
        if (selectedRadio.length === 0) {
            return;
        }
        if (selectedRadio.val() === "yes") {
            const code = $("#enrollment-code").val().trim();
            if (!code) {
                $("#enrollment-code").addClass("is-invalid");
                return;
            } else {
                $("#enrollment-code").removeClass("is-invalid");
            }
            getProspectByCode(
                code,
                function (json) {
                    $("#intro-section").fadeOut(400, function () {
                        $("#form-section").fadeIn(600);
                        $("html, body").animate({ scrollTop: 0 }, 300);
                    });
                },
                function (err) {
                    toastify(
                        "Error",
                        err?.responseJSON?.message ?? "Please try again later",
                        "error",
                    );
                },
            );
        } else {
            $("#intro-section").fadeOut(400, function () {
                $("#form-section").fadeIn(600);
                $("html, body").animate({ scrollTop: 0 }, 300);
            });
        }
    });

    $("#previous-btn").click(function () {
        $("#form-section").fadeOut(400, function () {
            $("#intro-section").fadeIn(600);
            $("html, body").animate({ scrollTop: 0 }, 300);
        });
    });
}

function checkHoliday(date, dataThis) {
    blockUI();
    ajax(null, `/holiday/check/${date}`, "GET", function (json) {
        if (Object.keys(json).length === 0) {
            changeVisitTime(false);
            dataThis.setCustomValidity("");
            $(dataThis).removeClass("is-invalid");
        } else {
            $(dataThis)
                .siblings(".invalid-feedback")
                .text("The selected date is a holiday and cannot be selected.");
            $(dataThis).addClass("is-invalid");
            changeVisitTime(true);
        }
    });
}
function checkCapacity(date, time, dataThis) {
    ajax(
        null,
        `school-visit/capacity/check?date=${date}&time=${time}`,
        "GET",
        function (json) {
            if (json == true || json == "true") {
                $(dataThis)
                    .siblings(".invalid-feedback")
                    .text(
                        "The selected time slot for the school visit is already full. Kindly choose a different time.",
                    );
                $(dataThis).addClass("is-invalid");
                $("#visit-time").val("").trigger("change");
            } else {
                dataThis.setCustomValidity("");
                $(dataThis).removeClass("is-invalid");
            }
        },
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
                "bottom",
            );
        },
    );
}

function getProspectByCode(code, resolve, reject) {
    blockUI();
    ajax(
        null,
        `/prospect/code/${code}`,
        "GET",
        function (json) {
            $("#prospects_id").val(json.id);
            $("#parent-name").val(json.parent_name);
            $("#email").val(json.email);
            $("#phone").val(json.phone_number);
            $("#zip-code").val(json.zipcode);
            $("#child-name").val(json.child_name);
            $("#current-school").val(json.current_school);
            $("#enrollment-code").val(json.code);

            $("#enrollment-code").removeClass("is-invalid");
            resolve(json);
        },
        function (err) {
            $("#enrollment-code").addClass("is-invalid");
            $("#enrollment-codeTextError").text(
                err?.responseJSON?.message ?? "Please try again later",
            );
            reject(err);
        },
    );
}

function getLevelsAndGrades(branchId) {
    blockUI();
    ajax(
        null,
        `/level/branch/${branchId}`,
        "GET",
        function (json) {
            $("#academic-year, #visit-level").attr("disabled", false);
            $("#visit-level")
                .empty()
                .append('<option value="">Select level...</option>');
            levels = json;
            levels.forEach((level) => {
                $("#visit-level").append(
                    `<option value="${level.id}">${level.name}</option>`,
                );
            });
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom",
            );
        },
    );
}
