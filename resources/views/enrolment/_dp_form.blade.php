        <form method="POST" action="{{ $formAction }}" id="dp-form" novalidate>
            @csrf
            <input type="hidden" name="request_key" value="{{ $requestKey }}">

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card mb-4 dp-section">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="bi bi-person-check me-2 text-success"></i>Parent Status</h5>
                            <p class="text-muted mb-3">Has the parent already filled in the enrolment form?</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="dp-choice">
                                        <input type="radio" name="already_enrolment" value="yes"
                                            {{ ($selectedMode === 'yes') ? 'checked' : '' }}>
                                        <strong class="ms-2">Yes, already registered</strong>
                                        <span class="d-block small text-muted mt-1">Find the existing enrolment record.</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="dp-choice">
                                        <input type="radio" name="already_enrolment" value="no"
                                            {{ ($selectedMode === 'no') ? 'checked' : '' }}>
                                        <strong class="ms-2">No, new parent</strong>
                                        <span class="d-block small text-muted mt-1">Add the parent and student to enrolments.</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 dp-section d-none" id="existing-flow">
                        <div class="card-body">
                            <h5><i class="bi bi-search me-2 text-success"></i>Find Enrolment</h5>
                            <label class="form-label" for="enrolment-code">Enrolment Code</label>
                            <div class="input-group">
                                <input class="form-control" name="enrolment_code" id="enrolment-code"
                                    value="{{ old('enrolment_code', $prefillCode) }}" placeholder="Example: ENR-2027-000123">
                                <button class="btn btn-outline-primary" type="button" id="search-enrolment">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                            <div id="lookup-alert" class="alert mt-3 d-none" role="status" aria-live="polite"></div>
                            <div id="enrolment-summary" class="dp-section p-3 mt-3 d-none"></div>
                        </div>
                    </div>

                    <div class="card mb-4 dp-section d-none" id="new-flow" aria-hidden="true">
                        <div class="card-body">
                            <h5><i class="bi bi-person-plus me-2 text-success"></i>New Parent & Student</h5>
                            <p class="text-muted">Enter the basic details to create the enrolment master record.</p>
                            <div id="expired-enrolment-notice" class="alert alert-danger d-none" role="alert"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="parent-name">Parent's Name *</label>
                                    <input class="form-control" id="parent-name" name="parent_name"
                                        value="{{ old('parent_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="parent-email">Email *</label>
                                    <input class="form-control" id="parent-email" name="email" type="email"
                                        value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="parent-phone">Phone Number *</label>
                                    <input class="form-control" id="parent-phone" name="phone_number"
                                        value="{{ old('phone_number') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="student-name">Student Name *</label>
                                    <input class="form-control" id="student-name" name="student_name"
                                        value="{{ old('student_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="academic-year">Academic Year *</label>
                                    <select class="form-select" id="academic-year" name="academic_year_id">
                                        <option value="">Select academic year</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ (string) old('academic_year_id') === (string) $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="branch">Branch *</label>
                                    <select class="form-select" id="branch" name="branch_id">
                                        <option value="">Select branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ (string) old('branch_id') === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="level">Level *</label>
                                    <select class="form-select" id="level" name="level_id" disabled>
                                        <option value="">Select level</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="grade">Grade *</label>
                                    <select class="form-select" id="grade" name="grade_id" disabled>
                                        <option value="">Select grade</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 dp-section d-none" id="payment-flow">
                        <div class="card-body">
                            <h5><i class="bi bi-credit-card me-2 text-success"></i>Payment</h5>
                            <div class="row g-3 mb-3">
                                @if ($public)
                                    <input type="hidden" name="registration_type" value="external">
                                @else
                                <div class="col-md-6">
                                    <label class="form-label d-block">Registration Type *</label>
                                    <div class="d-flex gap-3">
                                        <label class="form-check">
                                            <input class="form-check-input" type="radio" name="registration_type"
                                                value="internal" {{ (old('registration_type', 'external') === 'internal') ? 'checked' : '' }}>
                                            <span class="form-check-label">Internal</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="radio" name="registration_type"
                                                value="external" {{ (old('registration_type', 'external') === 'external') ? 'checked' : '' }}>
                                            <span class="form-check-label">External</span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label" for="registration-place">Registration Place *</label>
                                    <select class="form-select" id="registration-place" name="registration_place">
                                        <option value="">Select place</option>
                                        @foreach ($registrationPlaces as $place)
                                            <option value="{{ $place->code }}" data-other="{{ $place->is_other ? 1 : 0 }}" {{ old('registration_place') === $place->code ? 'selected' : '' }}>{{ $place->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 d-none" id="registration-place-other-wrap">
                                    <label class="form-label" for="registration-place-other">Please specify registration place *</label>
                                    <input class="form-control" id="registration-place-other"
                                        name="registration_place_other" value="{{ old('registration_place_other') }}">
                                </div>
                            </div>

                            <label class="form-label d-block">Payment Type *</label>
                            <p class="small text-muted">Select one or more payment items to include in this transaction.</p>
                            <div id="required-price-items" class="alert alert-light border d-none mb-3"></div>
                            <div class="row g-3">
                                <div class="col-md-4" id="registration-fee-option">
                                    <label class="dp-choice">
                                        <input type="checkbox" name="payment_types[]" value="enrolment"
                                            {{ (in_array('enrolment', (array) old('payment_types', []), true)) ? 'checked' : '' }}>
                                        <strong class="ms-2">Registration Fee</strong>
                                        <span class="d-block small text-muted mt-1" id="registration-fee-label">Price from current enrolment pricing</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="dp-choice">
                                        <input type="checkbox" name="payment_types[]" value="dp"
                                            {{ (in_array('dp', (array) old('payment_types', []), true)) ? 'checked' : '' }}>
                                        <strong class="ms-2">Development Fee DP</strong>
                                        <span class="d-block small text-muted mt-1">Default Rp 5.000.000; amount can be adjusted.</span>
                                    </label>
                                </div>
                                @unless ($public)
                                <div class="col-md-4">
                                    <label class="dp-choice">
                                        <input type="checkbox" name="payment_types[]" value="other"
                                            {{ (in_array('other', (array) old('payment_types', []), true)) ? 'checked' : '' }}>
                                        <strong class="ms-2">Other</strong>
                                        <span class="d-block small text-muted mt-1">Enter a custom payment amount.</span>
                                    </label>
                                </div>
                                                                        @endunless
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6 d-none" id="dp-amount-wrap">
                                    <label class="form-label" for="dp-amount">Development Fee DP Amount (Rp) *</label>
                                    <input class="form-control payment-amount money-input" id="dp-amount" name="amounts[dp]"
                                        type="text" inputmode="decimal" autocomplete="off"
                                        value="{{ old('amounts.dp', '5000000') }}">
                                </div>
                                <div class="col-md-6 d-none" id="other-payment-wrap">
                                    <label class="form-label" for="other-amount">Other Payment Amount (Rp) *</label>
                                    <input class="form-control payment-amount money-input" id="other-amount" name="amounts[other]"
                                        type="text" inputmode="decimal" autocomplete="off" value="{{ old('amounts.other') }}">
                                    <label class="form-label mt-2" for="other-description">Description</label>
                                    <input class="form-control" id="other-description" name="descriptions[other]"
                                        value="{{ old('descriptions.other') }}" placeholder="Describe this payment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card dp-summary">
                        <div class="card-body">
                            <h5><i class="bi bi-receipt me-2 text-success"></i>Payment Summary</h5>
                            <div class="small text-muted mb-2">Student</div>
                            <div class="fw-semibold mb-3" id="summary-student">Select or find an enrolment</div>
                            <div class="small text-muted mb-2">Payment Item</div>
                            <div id="summary-item">No payment selected</div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span id="summary-subtotal">Rp 0</span></div>
                            <div class="d-flex justify-content-between mb-2"><span>Discount <small class="text-success" id="summary-discount-note"></small></span><span id="summary-discount">Rp 0</span></div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Bank Charge</span>
                                <span id="summary-bank" data-bank-charge="{{ number_format($bankCharge, 2, '.', '') }}">
                                    Rp {{ number_format($bankCharge, 0, ',', '.') }}
                                </span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between dp-total"><span>Total</span><span id="summary-total">Rp 0</span></div>
                            <button class="btn btn-success btn-lg w-100 mt-4" id="submit-payment" type="submit" disabled>
                                <i class="bi bi-check2-circle me-1"></i>                                {{ $public ? 'Proceed to Payment' : 'Create Payment' }}                            </button>
                            <p class="small text-muted mt-2 mb-0">Payment link is generated by Xendit and is valid for 7 days.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
