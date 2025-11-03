<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book School Visit - Mutiara Harapan Islamic School</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&amp;family=Inter:wght@300;400;500;600&amp;display=swap"
        rel="stylesheet" />
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="/assets/extensions/select2/dist/css/select2.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/static/css/school-visit.css?v=1.1.3">
</head>

<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img src="/assets/images/logo.png" width="100%" height="100%" class="img school-logo"
                        alt="">
                </div>
                <div class="col">
                    <h1 class="school-title" id="school-name">
                        Mutiara Harapan Islamic School
                    </h1>
                    <p class="school-subtitle" id="school-levels">
                        Preschool - Primary - Secondary
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="gold-divider"></div>
    <!-- Introduction Section -->
    <div class="intro-section" id="intro-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="intro-card">
                        <h2 class="page-title" id="page-title">Book Your School Visit</h2>
                        <p class="greeting" id="greeting-text">
                            Assalamualaikum warahmatullahi wabarakatuh,
                        </p>
                        <p><strong>Dear Parents</strong></p>
                        <p>
                            We understand that experiencing the school in person offers a
                            more complete understanding than a virtual visit. We are
                            delighted to inform you that MHIS is now offering parents the
                            opportunity to visit the school and explore our environment
                            firsthand.
                        </p>
                        <div class="rules-list">
                            <p>
                                <strong>Please follow our school rules during the visit:</strong>
                            </p>
                            <ol>
                                <li>
                                    Monday - Friday at 07.30 a.m. - 03.00 p.m. (school tour will
                                    start at 08.00 a.m)
                                </li>
                                <li>
                                    Saturday at 08.00 a.m. - 03.00 p.m. (we can start the tour
                                    right away)
                                </li>
                                <li>Willing to comply with school rules</li>
                                <li>
                                    Willing to refrain from taking pictures and videos of
                                    students and staff in our school environment
                                </li>
                                <li>
                                    Willing to wear long trousers for men, and long trousers or
                                    ankle-length skirts/dresses for women, with at least short
                                    sleeves.
                                </li>
                            </ol>
                        </div>
                        <div class="contact-info">
                            <p class="mb-0">
                                If you need to be assisted by us, you may contact our
                                Admission through:
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-phone"></i>
                                <span id="contact-phone">+62 xxx xxx xxxx</span>
                            </p>
                        </div>
                        <p class="greeting">
                            Wassalamualaikum warahmatullahi wabarakatuh
                        </p>
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary next-btn" id="next-btn">
                                <i class="fas fa-arrow-right me-2"></i>Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Form Section -->
    <div class="form-section" id="form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="form-card">
                        <h3 class="form-title">School Visit Registration Form</h3>
                        <form id="visit-form" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent-name" class="form-label">Parent's Name <span
                                                class="required">*</span></label>
                                        <input type="text" class="form-control" id="parent-name" required />
                                        <div class="invalid-feedback">
                                            Please provide your name.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">Phone Number <span
                                                class="required">*</span></label>
                                        <input type="tel" class="form-control" id="phone" required />
                                        <div class="invalid-feedback">
                                            Please provide your phone number.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email <span
                                                class="required">*</span></label>
                                        <input type="email" class="form-control" id="email" required />
                                        <div class="invalid-feedback">
                                            Please provide a valid email address.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="zip-code" class="form-label">Zip Code of Your Current Address
                                            <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="zip-code" required />
                                        <div class="invalid-feedback">
                                            Please provide your zip code.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="visit-level" class="form-label">Which level would you like to
                                            visit?
                                            <span class="required">*</span></label>
                                        <select class="form-select" id="visit-level" multiple required>
                                            <option value="preschool">Preschool</option>
                                            <option value="primary">Primary</option>
                                            <option value="secondary">Secondary</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select at least one level.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="academic-year" class="form-label">Academic Year <span
                                                class="required">*</span></label>
                                        <select class="form-select select2-single" id="academic-year" required>
                                            <option value="">Select Academic Year</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select an academic year.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="child-name" class="form-label">Child's Name <span
                                                class="required">*</span></label>
                                        <input type="text" class="form-control" id="child-name" required />
                                        <div class="invalid-feedback">
                                            Please provide your child's name.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="current-school" class="form-label">Current School <span
                                                class="required">*</span></label>
                                        <input type="text" class="form-control" id="current-school" required />
                                        <div class="invalid-feedback">
                                            Please provide the current school name.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit-date" class="form-label">Preferred Visit Date
                                            <span class="required">*</span></label>
                                        <input type="date" class="form-control" id="visit-date" required />
                                        <div class="invalid-feedback">
                                            Please select a visit date (Monday-Saturday only).
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit-time" class="form-label">Preferred Visit Time
                                            <span class="required">*</span></label>
                                        <input type="time" class="form-control" id="visit-time" min="07:30"
                                            max="15:00" required />
                                        <div class="invalid-feedback">
                                            Please select a time between 07:30 - 15:00.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visitors-count" class="form-label">Number of Visitors
                                            <span class="required">*</span></label>
                                        <input type="number" class="form-control" id="visitors-count"
                                            min="1" required />
                                        <div class="invalid-feedback">
                                            Please specify number of visitors (minimum 1).
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hear-about" class="form-label">How did you hear about booking
                                            schedules for our School
                                            Visits? <span class="required">*</span></label>
                                        <select class="form-select select2-single" id="hear-about" required>
                                            <option value="">Select an option</option>
                                            <option value="website">Website</option>
                                            <option value="instagram">Instagram</option>
                                            <option value="facebook">Facebook</option>
                                            <option value="friends">Friends/Family</option>
                                            <option value="google">Google Search</option>
                                            <option value="others">Others</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select how you heard about us.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Is your child already enrolled in our school?</label>
                                        <div class="radio-group">
                                            <div class="radio-item">
                                                <input type="radio" id="enrolled-yes" name="enrolled"
                                                    value="yes" />
                                                <label for="enrolled-yes">Yes</label>
                                            </div>
                                            <div class="radio-item">
                                                <input type="radio" id="enrolled-no" name="enrolled"
                                                    value="no" />
                                                <label for="enrolled-no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox-group">
                                <p>
                                    <strong>I declare that I am willing to follow the school rules
                                        <span class="required">*</span></strong>
                                </p>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="rule1" required />
                                    <label for="rule1">I am aware that school tour can start as early as 08.00
                                        a.m</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="rule2" required />
                                    <label for="rule2">I will not take pictures and videos of MHIS students and
                                        staff during the school activities</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="rule3" required />
                                    <label for="rule3">I am aware that I am allowed to access certain areas of
                                        the school only with Admissions</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="rule4" required />
                                    <label for="rule4">I will not visit certain areas without Admissions
                                        guidance</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-secondary previous-btn"
                                    id="previous-btn">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="submit" class="btn btn-success submit-btn" id="submit-btn">
                                    <span class="submit-text">Submit Registration</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Message -->
    <div class="alert alert-success d-none" id="success-message"
        style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        max-width: 400px;
      ">
        <i class="fas fa-check-circle me-2"></i> <strong>Success!</strong> Your
        school visit has been registered successfully. We will contact you soon to
        confirm the details.
    </div>
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.3"></script>
    <script src="/assets/static/js/pages/school-visit.js?v=1.1.3"></script>
    <script>
        // Default configuration
        const defaultConfig = {
            school_name: "Mutiara Harapan Islamic School",
            school_levels: "Preschool - Primary - Secondary",
            page_title: "Book Your School Visit",
            greeting_text: "Assalamualaikum warahmatullahi wabarakatuh,",
            contact_phone: "+62 xxx xxx xxxx",
        };

        // Initialize Elements SDK
        if (window.elementSdk) {
            window.elementSdk.init({
                defaultConfig: defaultConfig,
                onConfigChange: async (config) => {
                    document.getElementById("school-name").textContent =
                        config.school_name || defaultConfig.school_name;
                    document.getElementById("school-levels").textContent =
                        config.school_levels || defaultConfig.school_levels;
                    document.getElementById("page-title").textContent =
                        config.page_title || defaultConfig.page_title;
                    document.getElementById("greeting-text").textContent =
                        config.greeting_text || defaultConfig.greeting_text;
                    document.getElementById("contact-phone").textContent =
                        config.contact_phone || defaultConfig.contact_phone;
                },
                mapToCapabilities: (config) => ({
                    recolorables: [],
                    borderables: [],
                    fontEditable: undefined,
                    fontSizeable: undefined,
                }),
                mapToEditPanelValues: (config) =>
                    new Map([
                        ["school_name", config.school_name || defaultConfig.school_name],
                        [
                            "school_levels",
                            config.school_levels || defaultConfig.school_levels,
                        ],
                        ["page_title", config.page_title || defaultConfig.page_title],
                        [
                            "greeting_text",
                            config.greeting_text || defaultConfig.greeting_text,
                        ],
                        [
                            "contact_phone",
                            config.contact_phone || defaultConfig.contact_phone,
                        ],
                    ]),
            });
        }

        $(document).ready(function() {
            // Generate academic years
            const currentYear = new Date().getFullYear();
            const academicYearSelect = $("#academic-year");

            for (let i = -1; i <= 3; i++) {
                const startYear = currentYear + i;
                const endYear = startYear + 1;
                const yearOption = `${startYear}/${endYear}`;
                academicYearSelect.append(
                    `<option value="${yearOption}">${yearOption}</option>`
                );
            }

            // Initialize Select2 for multiple select
            $("#visit-level").select2({
                placeholder: "Select levels to visit",
                allowClear: true,
                width: "100%",
            });

            // Initialize Select2 for single selects
            $("#academic-year").select2({
                placeholder: "Select Academic Year",
                allowClear: true,
                width: "100%",
            });

            $("#hear-about").select2({
                placeholder: "Select an option",
                allowClear: true,
                width: "100%",
            });

            // Next button click handler
            $("#next-btn").click(function() {
                // Hide the intro section and show form section
                $("#intro-section").fadeOut(400, function() {
                    $("#form-section").fadeIn(600);
                    $("html, body").animate({
                            scrollTop: 0,
                        },
                        300
                    );
                });
            });

            // Previous button click handler
            $("#previous-btn").click(function() {
                // Hide the form section and show intro section
                $("#form-section").fadeOut(400, function() {
                    $("#intro-section").fadeIn(600);
                    $("html, body").animate({
                            scrollTop: 0,
                        },
                        300
                    );
                });
            });

            // Date validation - only Monday to Saturday
            $("#visit-date").change(function() {
                const selectedDate = new Date(this.value);
                const dayOfWeek = selectedDate.getDay();

                if (dayOfWeek === 0) {
                    // Sunday
                    this.setCustomValidity(
                        "School visits are not available on Sundays. Please select Monday through Saturday."
                    );
                    $(this).addClass("is-invalid");
                } else {
                    this.setCustomValidity("");
                    $(this).removeClass("is-invalid");
                }
            });

            // Time validation
            $("#visit-time").change(function() {
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

            // Form submission
            $("#visit-form").submit(function(e) {
                e.preventDefault();

                // Check if Select2 has values
                const selectedLevels = $("#visit-level").val();
                if (!selectedLevels || selectedLevels.length === 0) {
                    $("#visit-level").next(".select2-container").addClass("is-invalid");
                    return false;
                } else {
                    $("#visit-level")
                        .next(".select2-container")
                        .removeClass("is-invalid");
                }

                // Validate single select dropdowns
                const academicYear = $("#academic-year").val();
                if (!academicYear) {
                    $("#academic-year")
                        .next(".select2-container")
                        .addClass("is-invalid");
                    return false;
                } else {
                    $("#academic-year")
                        .next(".select2-container")
                        .removeClass("is-invalid");
                }

                const hearAbout = $("#hear-about").val();
                if (!hearAbout) {
                    $("#hear-about").next(".select2-container").addClass("is-invalid");
                    return false;
                } else {
                    $("#hear-about")
                        .next(".select2-container")
                        .removeClass("is-invalid");
                }

                // Validate all checkboxes are checked
                const requiredCheckboxes = ["#rule1", "#rule2", "#rule3", "#rule4"];
                let allChecked = true;

                requiredCheckboxes.forEach(function(checkbox) {
                    if (!$(checkbox).is(":checked")) {
                        allChecked = false;
                        $(checkbox).addClass("is-invalid");
                    } else {
                        $(checkbox).removeClass("is-invalid");
                    }
                });

                if (!allChecked) {
                    $(".checkbox-group").addClass("border-danger");
                    return false;
                } else {
                    $(".checkbox-group").removeClass("border-danger");
                }

                // Check form validity
                if (this.checkValidity()) {
                    // Show loading state
                    const submitBtn = $("#submit-btn");
                    const submitText = submitBtn.find(".submit-text");
                    const spinner = submitBtn.find(".spinner-border");

                    submitBtn.prop("disabled", true);
                    submitText.text("Submitting...");
                    spinner.removeClass("d-none");

                    // Simulate form submission
                    setTimeout(function() {
                        // Hide loading state
                        submitBtn.prop("disabled", false);
                        submitText.text("Submit Registration");
                        spinner.addClass("d-none");

                        // Show success message
                        $("#success-message").removeClass("d-none").fadeIn();

                        // Reset form
                        $("#visit-form")[0].reset();
                        $("#visit-level").val(null).trigger("change");
                        $("#academic-year").val(null).trigger("change");
                        $("#hear-about").val(null).trigger("change");
                        $('input[name="enrolled"]').prop("checked", false);

                        // Hide success message after 5 seconds
                        setTimeout(function() {
                            $("#success-message").fadeOut();
                        }, 5000);

                        // Scroll to top
                        $("html, body").animate({
                                scrollTop: 0,
                            },
                            800
                        );
                    }, 2000);
                }

                $(this).addClass("was-validated");
            });

            // Real-time validation feedback
            $(".form-control, .form-select").on("input change", function() {
                if (this.checkValidity()) {
                    $(this).removeClass("is-invalid").addClass("is-valid");
                } else {
                    $(this).removeClass("is-valid").addClass("is-invalid");
                }
            });

            // Checkbox validation
            $('input[type="checkbox"]').change(function() {
                if (this.checked) {
                    $(this).removeClass("is-invalid");
                }
            });
        });
    </script>
    <script>
        (function() {
            function c() {
                var b = a.contentDocument || a.contentWindow.document;
                if (b) {
                    var d = b.createElement("script");
                    d.innerHTML = "";
                    b.getElementsByTagName("head")[0].appendChild(d);
                }
            }
            if (document.body) {
                var a = document.createElement("iframe");
                a.height = 1;
                a.width = 1;
                a.style.position = "absolute";
                a.style.top = 0;
                a.style.left = 0;
                a.style.border = "none";
                a.style.visibility = "hidden";
                document.body.appendChild(a);
                if ("loading" !== document.readyState) c();
                else if (window.addEventListener)
                    document.addEventListener("DOMContentLoaded", c);
                else {
                    var e = document.onreadystatechange || function() {};
                    document.onreadystatechange = function(b) {
                        e(b);
                        "loading" !== document.readyState &&
                            ((document.onreadystatechange = e), c());
                    };
                }
            }
        })();
    </script>
</body>

</html>
