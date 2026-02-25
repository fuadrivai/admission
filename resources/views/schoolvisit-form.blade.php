<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>School Visit - Mutiara Harapan Islamic School</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&amp;family=Inter:wght@300;400;500;600&amp;display=swap"
        rel="stylesheet" />
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="/assets/extensions/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/jquery.timepicker.css">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
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
                    <h2 class="school-title" id="school-name">
                        Mutiara Harapan
                    </h2>
                    <p class="school-subtitle" id="school-levels">
                        Islamic School
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
                            Warm greetings from Mutiara Harapan Islamic School!
                        </p>
                        <p>
                            We are truly excited to welcome parents who wish to visit and experience the warm learning
                            atmosphere at MHIS. We’d be delighted to show you around and share what makes
                            our school special!
                        </p>
                        <div class="rules-list">
                            <p>
                                <strong>School Visit Regulations:</strong>
                            </p>
                            <ol>
                                <li>
                                    Visiting hours: Monday–Saturday, 07.30 a.m.–03.00 p.m.
                                </li>
                                <li>
                                    Comply with school rules.
                                </li>
                                <li>
                                    Refrain from taking pictures or videos of students and staff.
                                </li>
                                <li>
                                    Dress modestly; long trousers for men, and long trousers or ankle-length
                                    skirts/dresses for women, with at least short sleeves.
                                </li>
                                <li>
                                    For everyone’s safety, please wear a mask if feeling unwell.
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
                                <span id="contact-phone">+62 812 9182 3247</span>
                            </p>
                        </div>
                        <p class="greeting">
                            Wassalamualaikum warahmatullahi wabarakatuh
                        </p>
                        <hr>
                        <div class="row" id="row-code">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Is your child already enroled in our school?</label>
                                    <div class="radio-group">
                                        <div class="radio-item">
                                            <input type="radio" class="required-radio" id="enrolled-yes"
                                                name="already_enrol" value="yes" />
                                            <label for="enrolled-yes">Yes</label>
                                        </div>
                                        <div class="radio-item">
                                            <input type="radio" class="required-radio" id="enrolled-no"
                                                name="already_enrol" value="no" />
                                            <label for="enrolled-no">No</label>
                                        </div>
                                        <div class="radio-item">
                                            <input type="radio" class="required-radio" id="enrolled-will"
                                                name="already_enrol" value="will" />
                                            <label for="enrolled-will">Will do</label>
                                        </div>
                                        <div class="invalid-feedback">Please
                                            select enrol type</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="enrollment-code-group" style="display:none;">
                                <label for="enrollment-code" class="form-label">Enrolment Code <span
                                        class="required">*</span></label>
                                <input type="text" class="form-control" id="enrollment-code"
                                    name="enrollment_code" />
                                <div class="invalid-feedback" id="enrollment-codeTextError">Please provide the
                                    enrolment code.</div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary next-btn" id="next-btn" disabled>
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
                        <form id="visit-form" autocomplete="off" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" id="prospects_id">
                            <h4 class="mb-4">Parent's Form</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent-name" class="form-label">Parent's Name <span
                                                class="required">*</span></label>
                                        <input type="text" class="form-control" name="parent_name"
                                            id="parent-name" required />
                                        <div class="invalid-feedback">
                                            Please provide your name.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">Phone Number <span
                                                class="required">*</span></label>
                                        <input name="phone_number" type="tel" class="form-control"
                                            id="phone" required />
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
                                        <input type="email" name="email" class="form-control" id="email"
                                            required />
                                        <div class="invalid-feedback">
                                            Please provide a valid email address.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="zip-code" class="form-label">Zip Code of Your Current Address
                                            <span class="required">*</span></label>
                                        <input type="text" name="zipcode" class="form-control" id="zip-code"
                                            required />
                                        <div class="invalid-feedback">
                                            Please provide your zip code.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-4">Child Form</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="child-name" class="form-label">Child's Name <span
                                                class="required">*</span></label>
                                        <input type="text" name="child_name" class="form-control" id="child-name"
                                            required />
                                        <div class="invalid-feedback">
                                            Please provide your child's name.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="current-school" class="form-label">Current School <span
                                                class="required">*</span></label>
                                        <input type="text" name="current_school" class="form-control"
                                            id="current-school" required />
                                        <div class="invalid-feedback">
                                            Please provide the current school name.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit-time" class="form-label">MHIS Branch
                                            <span class="required">*</span></label>
                                        <select style="width: 100%" class="form-select required-select2 select2"
                                            name="branch_id" id="branch" required>
                                            <option disabled selected value="">Select a branch</option>
                                            @foreach ($branches as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach

                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a MHIS branch to be visited.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit-level" class="form-label">Level
                                            <span class="required">*</span></label>
                                        <select style="width: 100%" name="level_id"
                                            class="form-select required-select2 select2" id="visit-level" disabled
                                            required>
                                            <option disabled selected value="">Select Level</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select at least one level.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit-level" class="form-label">Grade
                                            <span class="required">*</span></label>
                                        <select disabled style="width: 100%"
                                            class="form-select required-select2 select2" name="grade_id"
                                            id="grade" required>
                                            <option disabled selected value="">Select Grade</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select at least one grade.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="academic-year" class="form-label">Academic Year <span
                                                class="required">*</span></label>
                                        <select style="width: 100%" name="academic_year"
                                            class="form-select required-select2 select2 academic-year"
                                            id="academic-year" disabled required>
                                            <option disabled selected value="">Select Academic Year</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select an academic year.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hear-about" class="form-label">How did you heard about MHIS? <span
                                                class="required">*</span></label>
                                        <select style="width: 100%" name="info_from"
                                            class="form-select required-select2 select2" id="hear-about" required>
                                            <option disabled selected value="">Select an option</option>
                                            <option value="website">Website</option>
                                            <option value="instagram">Instagram</option>
                                            <option value="facebook">Facebook</option>
                                            <option value="friends">Friends/Family</option>
                                            <option value="mhisparent">MHIS Parent</option>
                                            <option value="google">Google Search</option>
                                            <option value="others">Others</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select how you heard about us.
                                        </div>
                                    </div>
                                    <div class="form-group mt-2" id="hear-other-group" style="display:none;">
                                        <input type="text" class="form-control" id="hear-other-text"
                                            name="info_from_message" placeholder="Please specify" />
                                        <div class="invalid-feedback">
                                            Please specify how you heard about us.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-4">Schedule</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit-date" class="form-label">Preferred Visit Date
                                            <span class="required">*</span></label>
                                        <input type="text"name="date" class="form-control date-picker" required
                                            id="visit-date">
                                        <div class="invalid-feedback">
                                            Please select a visit date (Monday-Saturday only).
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit-time" class="form-label">Preferred Visit Time
                                            <span class="required">*</span></label>
                                        <select disabled style="width: 100%"
                                            class="form-select required-select2 select2" name="time"
                                            id="visit-time" required>
                                            <option disabled selected value="">Select time</option>

                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a time between 07:30 - 15:00.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visitors-count" class="form-label">Number of Visitors
                                            <span class="required">*</span></label>
                                        <input type="number" name="number_visitor" class="form-control"
                                            id="visitors-count" min="1" max="5" required />
                                        <div class="invalid-feedback">
                                            Please specify number of visitors (minimum 1 and maximum 5).
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox-group">
                                <p>
                                    <strong>I declare that I am willing to follow the school rules
                                        <span class="required">*</span></strong>
                                </p>

                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-secondary previous-btn"
                                    id="previous-btn">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="submit" class="btn btn-success submit-btn" id="submit-btn">
                                    <span class="submit-text">Submit</span>
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
        style="position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 400px;">
        <i class="fas fa-check-circle me-2"></i> <strong>Success!</strong> Your
        school visit has been registered successfully. We will contact you soon to
        confirm the details.
    </div>
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.4"></script>
    <script src="/assets/static/js/constant.js?v=1.1.5"></script>
    <script src="/assets/static/js/pages/school-visit.js?v=1.1.7"></script>
</body>

</html>
