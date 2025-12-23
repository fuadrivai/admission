$(document).ajaxStop($.unblockUI);
$(document).ready(function () {
    $(".select2").select2({});
    $(".select2").on("select2:open", function () {
        $("input.select2-search__field")[0].focus();
    });
    $("select.required-select2").on("change", function () {
        $(this).next(".select2-container").removeClass("is-invalid");
    });

    $(".number2").on("keyup", function (event) {
        if (event.which >= 37 && event.which <= 40) return;
        $(this).val(function (index, value) {
            return value
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
    });

    $(".date-picker").datepicker({
        format: "d MM yyyy",
        orientation: "top auto",
        autoclose: true,
        todayHighlight: true,
        language: "id",
        clearBtn: true,
    });

    $(".month-picker").datepicker({
        format: "MM yyyy",
        orientation: "top auto",
        autoclose: true,
        startView: "months",
        minViewMode: "months",
        language: "id",
        clearBtn: true,
    });

    $(".year-picker").datepicker({
        format: "yyyy",
        orientation: "top auto",
        autoclose: true,
        viewMode: "years",
        minViewMode: "years",
        language: "id",
    });

    $(".time-picker").timepicker({
        timeFormat: "HH:mm",
        interval: 30,
        minTime: "00:00",
        maxTime: "23:59",
        dynamic: true,
        dropdown: true,
        scrollbar: false,
    });

    $(".modal").on("hidden.bs.modal", function (event) {
        resetForm($(".modal form"));
    });
});

function toastify(type = "success", message, position = "top") {
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: position,
        position: "right",
        style: {
            background: type === "success" ? "#4fbe87" : "#f44336",
        },
    }).showToast();
}

function ajax(data, url, method, callback, callbackError) {
    $.ajax({
        url: url,
        data: data,
        type: method,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (json, text) {
            json = json;
            callback(json);
        },
        error: function (err) {
            callbackError == null
                ? toastify(
                      "Error",
                      err?.responseJSON?.message ?? "Please try again later",
                      "error"
                  )
                : callbackError(err);
        },
    });
}

function getQueryString() {
    location.queryString = {};
    location.search
        .substring(1)
        .split("&")
        .forEach(function (pair) {
            if (pair === "") return;
            var parts = pair.split("=");
            location.queryString[parts[0]] =
                parts[1] && decodeURIComponent(parts[1].replace(/\+/g, " "));
        });
    return location.queryString;
}

function reloadJsonDataTable(dtable, json) {
    dtable.clear().draw();
    dtable.rows.add(json).draw();
}

function diffTime(start, end) {
    let startTime = moment(start, "HH:mm");
    let endTime = moment(end, "HH:mm");

    let duration = moment.duration(endTime.diff(startTime));

    let hours = duration.hours();
    let minutes = duration.minutes();

    return `${hours}h ${minutes}m`;
}

function resetForm(form) {
    form.find("input").val("");
    form.find("input[type='checkbox']").prop("checked", false);
    form.find("textarea").val("");
    form.find("select").prop("selectedIndex", 0).trigger("change");
    form.find(".error").removeClass("error");
    form.find("#handling-error").remove();
}

function blockUI(message = null) {
    $.blockUI({
        message:
            message ??
            '<label><i class="fa fa-spinner fa-spin"></i> Just a moment...</label>',
    });
}

function unBlockUI() {
    $.unblockUI();
}

function validateRequiredSelect2() {
    let valid = true;

    $("select.required-select2").each(function () {
        let container = $(this).next(".select2-container");
        if (!$(this).val() || $(this).val().length === 0) {
            container.addClass("is-invalid");
            valid = false;
        } else {
            container.removeClass("is-invalid");
        }
    });

    return valid;
}

function validateRadio() {
    let isValid = true;

    $(".radio-group").each(function () {
        const radios = $(this).find(".required-radio");
        const errorText = $(this).find(".invalid-feedback");

        if (radios.is(":checked") === false) {
            errorText.show();
            isValid = false;
        } else {
            errorText.hide();
        }
    });

    return isValid;
}

function formatNumber(total) {
    return parseFloat(total)
        .toFixed(2)
        .replace(/(\d)(?=(\d{3})+\.)/g, "$1,")
        .toString()
        .replace(".00", "");
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

function normalizePhone(phone) {
    phone = phone.replace(/\D/g, "");
    phone = phone.replace(/^\+/, "");
    if (phone.startsWith("0")) {
        phone = "62" + phone.substring(1);
    } else if (phone.startsWith("62")) {
        // nothing
    } else if (phone.startsWith("8")) {
        phone = "62" + phone;
    }

    return phone;
}

function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validatePhone(phone) {
    const regex = /^(?:\+62|62|0)[0-9]{9,13}$/;
    return regex.test(phone);
}

function toTitleCase(text) {
    return text.toLowerCase().replace(/\b\w/g, function (letter) {
        return letter.toUpperCase();
    });
}

function ajaxPromise(data, url, method = "GET") {
    return new Promise((resolve, reject) => {
        ajax(
            data,
            url,
            method,
            function (json) {
                resolve(json);
            },
            function (err) {
                reject(err);
            }
        );
    });
}

function calculateAge(date) {
    const dob = new Date(date);
    if (isNaN(dob.getTime())) {
        return "Masukkan tanggal lahir untuk menghitung usia";
    }

    const today = new Date();
    let years = today.getFullYear() - dob.getFullYear();
    let months = today.getMonth() - dob.getMonth();
    let days = today.getDate() - dob.getDate();

    if (days < 0) {
        months--;
        const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        days += prevMonth.getDate();
    }

    if (months < 0) {
        years--;
        months += 12;
    }

    return `${years} tahun, ${months} bulan, ${days} hari`;
}
