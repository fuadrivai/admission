<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>MHIS Enrolment Form</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/assets/static/css/enrolment-external.css?v=1.0.0">
</head>

<body>
    <div class="enrollment-wrapper">
        <div class="form-container">
            <!-- Header -->
            <div class="form-header">
                <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo" class="header-logo"
                    onerror="this.style.display='none';" />
                <h2 class="text-white">MHIS Enrolment Form</h2>
                <p>This form is quick and easy to complete.</p>
                <!-- Step Progress -->
                <div class="step-progress">
                    <div class="progress-line" id="progressLine"></div>
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-title">Visit</div>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-title">Level</div>
                    </div>
                    <div class="step-item" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-title">Details</div>
                    </div>
                    <div class="step-item" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-title">Insights</div>
                    </div>
                    <div class="step-item" data-step="5">
                        <div class="step-circle">5</div>
                        <div class="step-title">Complete</div>
                    </div>
                </div>
            </div>
            <!-- Form Content -->
            <div class="form-content">
                <form id="enrollmentForm" novalidate autocomplete="off">
                    <!-- Section 1: Visit Confirmation -->
                    <div class="section-step active" data-step="1">
                        <div class="section-title">
                            <i class="fas fa-school"></i> Visit Confirmation
                        </div>
                        <label class="form-label">
                            Have you visited our school?
                            <span class="required-asterisk">*</span>
                        </label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visitedBefore" id="visitYes"
                                    value="true" required />
                                <label class="form-check-label" for="visitYes">
                                    <i class="fas fa-check-circle"></i> Yes, I have visited
                                    before
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visitedBefore" id="visitNo"
                                    value="false" />
                                <label class="form-check-label" for="visitNo">
                                    <i class="fas fa-times-circle"></i> No, this is my first
                                    time
                                </label>
                            </div>
                        </div>
                        <div id="codeInputWrapper" style="display: none">
                            <div class="code-input-wrapper">
                                <label for="visitCode" class="form-label">
                                    Enter your visit code
                                    <span class="required-asterisk">*</span>
                                </label>
                                <input type="hidden" id="prospects_id" name="prospects_id">
                                <input type="text" class="form-control" id="visitCode"
                                    placeholder="e.g., VISIT2024001" />
                                <div class="invalid-feedback" id="visitCodeTextError">Invalid School Visit Code</div>
                            </div>
                        </div>
                        <br>
                        <label class="form-label">
                            Is your child currently studying at MHIS?
                            <span class="required-asterisk">*</span>
                        </label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currentMHIS" id="currentYes"
                                    value="yes" required />
                                <label class="form-check-label" for="currentYes">
                                    <i class="fas fa-check-circle"></i> Yes
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currentMHIS" id="currentNo"
                                    value="no" />
                                <label class="form-check-label" for="currentNo">
                                    <i class="fas fa-times-circle"></i> No
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currentMHIS"
                                    id="yesSiblingRegistration" value="yes_sibling_registration" />
                                <label class="form-check-label" for="yesSiblingRegistration">
                                    <i class="fas fa-times-circle"></i> Yes, but I would like to register a sibling
                                </label>
                            </div>
                        </div>
                        <div id="codeInputPortal" style="display: none">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="code-input-wrapper">
                                        <label for="branch-portal" class="form-label">
                                            Which MHIS branch is your child currently attending?
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <select class="form-select required-select2" name="branch-portal"
                                            id="branch-portal">
                                            <option disabled selected value="">Select branch...</option>
                                            @foreach ($branches as $item)
                                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="code-input-wrapper">
                                        <label for="mhis-portal" class="form-label">
                                            Enter MHIS Portal Username
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="mhis-portal-username"
                                            placeholder="P0xx / PB0xx / PS0xx" />
                                        <div class="invalid-feedback mhPortalTextError">Invalid username or
                                            password</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="code-input-wrapper">
                                        <label for="mhis-portal" class="form-label">
                                            Enter MHIS Portal Password
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <input type="password" class="form-control" id="mhis-portal-password" />
                                        <div class="invalid-feedback">Invalid username or
                                            password</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center pt-3" id="row-button" style="display: none">
                                <div class="col-md-4 text-center">
                                    <button type="button" id="btn-portal"
                                        class="btn btn-lg btn-primary">Enter</button>
                                </div>
                                <div class="col-md-12 pt-2" id="list-student">

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Section 2: Level Enrollment -->
                    <div class="section-step" data-step="2">
                        <div class="section-title">
                            <i class="fas fa-graduation-cap"></i> Enrolment
                        </div>
                        <p class="section-subtitle">
                            Select the enrolment level and details
                        </p>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="branch" class="form-label">
                                    Preferred branch?
                                    <span class="required-asterisk">*</span>
                                </label>
                                <br><small><i>Cabang MHIS yang ingin dipilih</i></small>
                                <select class="form-select required-select2" name="branch" id="branch" required>
                                    <option disabled selected value="">Select branch...</option>
                                    @foreach ($branches as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="level" class="form-label">
                                    Preferred level?
                                    <span class="required-asterisk">*</span>
                                </label>
                                <br><small><i>Tingkat pendidikan yang dituju</i></small>
                                <select disabled class="form-select required-select2" id="level" required>
                                    <option disabled selected value="">Select level...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="grade" class="form-label">
                                    Grade <span class="required-asterisk">*</span>
                                </label>
                                <select disabled class="form-select required-select2" id="grade" required>
                                    <option value="">Select grade...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="academic-year" class="form-label">
                                    Academic Year <span class="required-asterisk">*</span>
                                </label>
                                <select disabled class="form-select required-select2" id="academic-year" required>
                                    <option value="">Select academic year...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-price pt-5 d-none">
                            <h6> Detail </h6>
                            <div class="row">
                                <div class="col-md-6">Enrolment Form</div>
                                <div class="col-md-6 text-end">Rp. <span id="enrolment-form">0</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">Bank Charger</div>
                                <div class="col-md-6 text-end">Rp. <span id="bank-form">0</span></div>
                            </div>
                            <div class="row fw-bold">
                                <div class="col-md-6">Total</div>
                                <div class="col-md-6 text-end">Rp. <span id="total-form">0</span></div>
                            </div>
                        </div>
                    </div>
                    <!-- Section 3: Parent & Child Information -->
                    <div class="section-step" data-step="3">
                        <div class="section-title">
                            <i class="fas fa-users"></i> Parent &amp; Child Details
                        </div>
                        <p class="section-subtitle">
                            Please provide the required information
                        </p>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="parentName" class="form-label">
                                    Parent's Name <span class="required-asterisk">*</span>
                                </label>
                                <br><small><i>Nama orang tua</i></small>
                                <input type="text" class="form-control" id="parentName" required />
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    E-mail <span class="required-asterisk">*</span>
                                </label>
                                <br><small><i>E-mail orang tua</i></small>
                                <input type="email" name="email" class="form-control" id="email" required />
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    Phone Number <span class="required-asterisk">*</span>
                                </label>
                                <br><small><i>Nomer telp orang tua</i></small>
                                <input type="tel" class="form-control" name="phone" id="phone" required />
                                <div class="invalid-feedback">Please enter a valid phone number.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="Relationship" class="form-label">
                                    Relationship <span class="required-asterisk">*</span>
                                </label>
                                <br><small><i>Hubungan dengan Anak</i></small>
                                <select name="relationship" class="form-select required-select2" id="relationship"
                                    required>
                                    <option disabled selected value="">Select relationship...</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="guardian">Guardian</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="zipCode" class="form-label">
                                    Zip Code <span class="required-asterisk">*</span>
                                </label>
                                <input type="number" class="form-control" id="zipCode" required />
                            </div>
                            <div class="col-md-9">
                                <label for="address" class="form-label">
                                    Current Address <span class="required-asterisk">*</span>
                                </label>
                                <textarea class="form-control" id="address" required></textarea>
                            </div>
                            <hr>
                            <div class="col-md-6">
                                <label for="childName" class="form-label">
                                    Child's Full Name <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" class="form-control" id="childName" required />
                            </div>
                            <div class="col-md-6">
                                <label for="birthPlace" class="form-label">
                                    Place of Birth <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" class="form-control" id="birthPlace" required />
                            </div>
                            <div class="col-md-6">
                                <label for="birthDate" class="form-label">
                                    Date of Birth <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" readonly class="form-control date-picker" id="birthDate"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label for="currentSchool" class="form-label">
                                    Current School <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" class="form-control" id="currentSchool" required />
                            </div>
                            <div class="col-md-6 social-media-field" id="socialMediaWrapper" style="display: none">
                                <label for="socialMedia" class="form-label">
                                    Student's Social Media
                                    <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" class="form-control" id="socialMedia"
                                    placeholder="@username" />
                            </div>
                        </div>
                    </div>
                    <!-- Section 4: Additional Information & Recommendations -->
                    <div class="section-step" data-step="4">
                        <div class="section-title">
                            <i class="fas fa-clipboard-list"></i> Needs and Preferences
                        </div>
                        <p class="section-subtitle">
                            Help us understand you and your child better
                        </p>
                        <div class="mb-4">
                            <label class="form-label">
                                Have you visited our Open Day before?
                                <span class="required-asterisk">*</span>
                            </label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="openDay" id="openDayYes"
                                        value="true" required />
                                    <label class="form-check-label" for="openDayYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="openDay" id="openDayNo"
                                        value="false" />
                                    <label class="form-check-label" for="openDayNo">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                Do you already know more or less our school program?
                                <span class="required-asterisk">*</span>
                            </label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="knowProgram" id="knowYes"
                                        value="yes" required />
                                    <label class="form-check-label" for="knowYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="knowProgram" id="knowNo"
                                        value="no" />
                                    <label class="form-check-label" for="knowNo">No</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="knowProgram" id="knowMaybe"
                                        value="maybe" />
                                    <label class="form-check-label" for="knowMaybe">Somewhat</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="hearAbout" class="form-label">
                                How did you hear about us?
                                <span class="required-asterisk">*</span>
                            </label>
                            <select class="form-control required-select2" name="info_from" id="hearAbout" required>
                                <option disabled selected value="">Select an option</option>
                                <option value="website">Website</option>
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="friends">Friends/Family</option>
                                <option value="google">Google Search</option>
                                <option value="parent">I am MHIS Parent</option>
                                <option value="mhis_parent">Recommendation from MHIS Parent</option>
                                <option value="others">Others</option>
                            </select>
                            <div class="form-group mt-2" id="hear-other-group" style="display:none;">
                                <input type="text" class="form-control" id="hear-other-text"
                                    name="info_from_message" placeholder="Please specify" />
                                <div class="invalid-feedback">
                                    Please specify how you heard about us.
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="enrollReason" class="form-label">
                                What specifically makes you want to enrol in MHIS? <span
                                    class="required-asterisk">*</span>
                            </label>
                            <input type="text" class="form-control" id="enrollReason" required />
                        </div>
                        <div class="mb-4">
                            <label for="bestProgram" class="form-label">
                                Which MHIS program is best suited for your child?
                                <span class="required-asterisk">*</span>
                            </label>
                            <br><small>Program MHIS mana yang paling sesuai dengan yang Ayah/Bunda cari untuk
                                Ananda?</small>
                            <input type="text" class="form-control" id="bestProgram" required />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                To what extent do you believe MHIS educational standards will help your child succeed as
                                a champion?
                                <span class="required-asterisk">*</span>
                            </label>
                            <br><small>Sejauh mana Ayah/Bunda percaya standar pendidikan MHIS akan membantu Ananda
                                menjadi champion?</small>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="standards" id="fullyAgree"
                                        value="fully_agree" required />
                                    <label class="form-check-label" for="fullyAgree">Fully Agree</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="standards"
                                        id="somewhatAgree" value="somewhat_agree" />
                                    <label class="form-check-label" for="somewhatAgree">Somewhat Agree</label>
                                </div>
                            </div>
                        </div>
                        <div class="recommender">
                            <div class="section-title mt-5">
                                <i class="fas fa-handshake"></i> Recommendations
                            </div>
                            <p class="section-subtitle">Optional but helpful information</p>
                            <div class="info-box">
                                <p>
                                    <strong>If you received a recommendation from a colleague, please let us know their
                                        name and phone number. If they are MHIS parents, kindly include their child’s
                                        name and class. Thank you, Parents!
                                </p>
                                <p><small><i>Apabila Ayah/Bunda mendapatkan rekomendasi terkait MHIS, silakan
                                            informasikan nama dan nomor telepon pemberi rekomendasi. Jika mereka
                                            merupakan orang tua MHIS, silakan cantumkan nama dan kelas
                                            Ananda.</i></small></p>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="recommenderName" class="form-label">
                                        Recommender Name
                                    </label>
                                    <br><small>Nama Pemberi Rekomendasi</small>
                                    <input type="text" class="form-control" id="recommenderName" />
                                </div>
                                <div class="col-md-6">
                                    <label for="recommenderPhone" class="form-label">
                                        Recommender Phone Number
                                    </label>
                                    <br><small>Nomor Telepon Pemberi Rekomendasi</small>
                                    <input type="tel" class="form-control" id="recommenderPhone" />
                                </div>
                                <div class="col-md-6">
                                    <label for="recommenderChildName" class="form-label">
                                        Recommender Child Name
                                    </label>
                                    <br><small>Nama Anak Pemberi Rekomendasi</small>
                                    <input type="text" class="form-control" id="recommenderChildName" />
                                </div>
                                <div class="col-md-6">
                                    <label for="recommenderChildClass" class="form-label">
                                        Recommender Child Class
                                    </label>
                                    <br><small>Kelas Anak Pemberi Rekomendasi</small>
                                    <input type="text" class="form-control" id="recommenderChildClass" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Section 5: Success Message -->
                    <div class="section-step" data-step="5">
                        <div class="success-message">
                            <div class="success-icon"><i class="fas fa-check"></i></div>
                            <h2>Enrolment Submitted Successfully!</h2>
                            <p>
                                Thank you for enroling your child at MHIS. Our admissions team
                                will contact you shortly with the next steps.
                            </p>
                            <button type="button" class="btn-custom btn-next" id="backToFormBtn"
                                style="margin: 30px auto 0">
                                <i class="fas fa-arrow-left"></i> Back to Form
                            </button>
                        </div>
                    </div>
                    <!-- Navigation Buttons -->
                    <div class="button-group" id="buttonGroup">
                        <button type="button" class="btn-custom btn-prev" id="prevBtn" style="display: none">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn-custom btn-next" id="nextBtn">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.4"></script>
    <script src="/assets/static/js/pages/enrolment-external.js?v=1.0.1"></script>
</body>

</html>
