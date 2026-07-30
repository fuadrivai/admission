<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>MHIS Uniform Order Form</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="/assets/extensions/select2/dist/css/select2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/assets/static/css/enrolment-external.css?v=1.0.1">
    <link rel="stylesheet" href="/assets/static/css/unform.css?v=1.0.2">
</head>

<body>
    <div class="enrollment-wrapper">
        <div class="form-container">
            <!-- Header -->
            <div class="form-header">
                <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo" class="header-logo"
                    onerror="this.style.display='none';" />
                <h2 class="text-white mt-2">MHIS Uniform Order Form</h2>
                <p>Fill in student details, choose branch & grade, and specify your uniform order.</p>

                <!-- Step Progress Bar -->
                <div class="step-progress">
                    <div class="progress-line" id="progressLine"></div>
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-title">Student & Parent</div>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-title">Branch & Grade</div>
                    </div>
                    <div class="step-item" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-title">Uniform Items</div>
                    </div>
                    <div class="step-item" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-title">Complete</div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content">
                <form id="uniformOrderForm" novalidate autocomplete="off">
                    @csrf

                    <!-- Section 1: Student & Parent Information -->
                    <div class="section-step active" data-step="1">
                        <div class="section-title">
                            <i class="fas fa-user-graduate me-2"></i>Student & Parent Details
                        </div>
                        <p class="section-subtitle">Please enter student and parent information</p>

                        <div class="row g-3 g-md-4">
                            <div class="col-md-6">
                                <label for="student_name" class="form-label">
                                    Student's Full Name <span class="required-asterisk">*</span>
                                    <br><small><i>Nama lengkap siswa</i></small>
                                </label>
                                <input type="text" class="form-control" name="student_name" id="student_name" placeholder="Enter student's full name" required />
                                <div class="invalid-feedback">Student's name is required.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="parent_name" class="form-label">
                                    Parent's Name <span class="required-asterisk">*</span>
                                    <br><small><i>Nama orang tua</i></small>
                                </label>
                                <input type="text" class="form-control" name="parent_name" id="parent_name" placeholder="Enter parent's name" required />
                                <div class="invalid-feedback">Parent's name is required.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="parent_phone" class="form-label">
                                    Parent's Phone Number <span class="required-asterisk">*</span>
                                    <br><small><i>Nomor telepon/WhatsApp orang tua</i></small>
                                </label>
                                <input type="tel" class="form-control" name="parent_phone" id="parent_phone" placeholder="e.g. 08123456789" required />
                                <div class="invalid-feedback">Please enter a valid phone number.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="parent_email" class="form-label">
                                    Parent's E-mail <span class="required-asterisk">*</span>
                                    <br><small><i>Alamat email orang tua</i></small>
                                </label>
                                <input type="email" class="form-control" name="parent_email" id="parent_email" placeholder="parent@example.com" required />
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Branch, Level, & Grade Selection -->
                    <div class="section-step" data-step="2">
                        <div class="section-title">
                            <i class="fas fa-school me-2"></i>Branch & Grade Selection
                        </div>
                        <p class="section-subtitle">Select branch, level, and grade for the student</p>

                        <div class="row g-3 g-md-4">
                            <div class="col-md-4">
                                <label for="branch" class="form-label">
                                    Branch <span class="required-asterisk">*</span>
                                    <br><small><i>Cabang MHIS</i></small>
                                </label>
                                <select class="form-select required-select2" name="branch" id="branch" required>
                                    <option disabled selected value="">Select branch...</option>
                                    @foreach ($branches as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a branch.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="level" class="form-label">
                                    Level <span class="required-asterisk">*</span>
                                    <br><small><i>Tingkat pendidikan</i></small>
                                </label>
                                <select class="form-select required-select2" name="level" id="level" required disabled>
                                    <option disabled selected value="">Select level...</option>
                                </select>
                                <div class="invalid-feedback">Please select a level.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="grade_id" class="form-label">
                                    Grade <span class="required-asterisk">*</span>
                                    <br><small><i>Kelas</i></small>
                                </label>
                                <select class="form-select required-select2" name="grade_id" id="grade_id" required disabled>
                                    <option disabled selected value="">Select grade...</option>
                                </select>
                                <div class="invalid-feedback">Please select a grade.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Product & Detail Order -->
                    <div class="section-step" data-step="3">
                        <div class="section-title">
                            <i class="fas fa-tshirt me-2"></i>Uniform Order Items
                        </div>
                        <p class="section-subtitle">Choose the items you need, then customize size and quantity</p>

                        <!-- Multiple Choice Item Selector -->
                        <div class="mb-4">
                            <label for="item_selector" class="form-label fw-bold">
                                <i class="fas fa-list-check me-1 text-primary"></i> Select Uniform Items to Order <span class="required-asterisk">*</span>
                                <br><small class="text-muted fw-normal"><i>Pilih item seragam yang ingin dipesan (bisa pilih lebih dari satu item)</i></small>
                            </label>
                            <select class="form-select required-select2" id="item_selector" multiple="multiple" data-placeholder="Choose/search uniform items (e.g. Badge, Seragam SD, Kaus)...">
                                
                            </select>
                        </div>

                        <!-- Notice when no item is selected -->
                        <div id="no_items_notice" class="text-center py-5 border border-2 border-dashed rounded-3 bg-light my-3">
                            <i class="fas fa-shopping-bag fa-2x text-muted mb-2"></i>
                            <h6 class="fw-bold text-secondary">No Uniform Items Selected Yet</h6>
                            <p class="text-muted small mb-0">Please select one or more items from the dropdown list above to add them to your order details.</p>
                        </div>

                        <!-- Order Items Detail Table -->
                        <div class="table-responsive mt-3">
                            <table class="table table-hover align-middle product-table" style="display: none;">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Product Details</th>
                                        <th style="width: 90px;">Unit Type</th>
                                        <th style="width: 130px;">Size</th>
                                        <th style="width: 130px;" class="text-end">Price</th>
                                        <th style="width: 130px;" class="text-center">Qty</th>
                                        <th style="width: 140px;" class="text-end">Subtotal</th>
                                        <th style="width: 50px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $index => $product)
                                        <tr class="product-row" data-product-id="{{ $product->id }}" style="display: none;">
                                            <td data-label="#">{{ $index + 1 }}</td>
                                            <td data-label="Product Details">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="fw-bold text-dark fs-6">{{ $product->name }}</div>
                                                    <span class="badge d-inline-block d-md-none {{ $product->unit_type == 'pcs' ? 'badge-unit-pcs' : 'badge-unit-meter' }}">
                                                        {{ strtoupper($product->unit_type) }}
                                                    </span>
                                                </div>
                                                <span class="product-code-badge mt-1 d-inline-block">{{ $product->code }}</span>
                                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $product->id }}">
                                            </td>
                                            <td data-label="Unit Type" class="d-none d-md-table-cell">
                                                <span class="badge {{ $product->unit_type == 'pcs' ? 'badge-unit-pcs' : 'badge-unit-meter' }}">
                                                    {{ strtoupper($product->unit_type) }}
                                                </span>
                                            </td>
                                            <td data-label="Size">
                                                @if($product->has_size)
                                                    <select class="form-select form-select-sm item-size" name="items[{{ $index }}][size]" id="size_{{ $product->id }}" data-product-id="{{ $product->id }}" style="max-width: 140px;">
                                                        <option value="">Select size...</option>
                                                        <option value="XS">XS</option>
                                                        <option value="S">S</option>
                                                        <option value="M">M</option>
                                                        <option value="L">L</option>
                                                        <option value="XL">XL</option>
                                                        <option value="XXL">XXL</option>
                                                        <option value="XXXL">XXXL</option>
                                                    </select>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                    <input type="hidden" name="items[{{ $index }}][size]" value="">
                                                @endif
                                            </td>
                                            <td data-label="Price" class="text-md-end">
                                                <span id="price_display_{{ $product->id }}" class="fw-semibold text-dark">Rp 0</span>
                                            </td>
                                            <td data-label="Qty" class="text-md-center">
                                                <div class="input-group input-group-sm qty-stepper">
                                                    <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-product-id="{{ $product->id }}">-</button>
                                                    <input type="number" class="form-control text-center item-qty" 
                                                        name="items[{{ $index }}][qty]" id="qty_{{ $product->id }}" data-product-id="{{ $product->id }}"
                                                        min="0" value="0" step="{{ $product->unit_type == 'pcs' ? '1' : '0.5' }}">
                                                    <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-product-id="{{ $product->id }}">+</button>
                                                </div>
                                            </td>
                                            <td data-label="Subtotal" class="text-md-end">
                                                <span id="subtotal_display_{{ $product->id }}" class="fw-bold text-primary">Rp 0</span>
                                            </td>
                                            <td data-label="Action" class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-product-id="{{ $product->id }}" title="Remove item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No uniform products available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div class="order-summary-box">
                            <div class="order-summary-item">
                                <span>Total Selected Items:</span>
                                <span id="summary_total_items" class="fw-bold">0</span>
                            </div>
                            <div class="order-summary-total">
                                <span>Bank Charge:</span>
                                <span id="bank-form" class="text-primary">Rp 0</span>
                            </div>
                            <div class="order-summary-total">
                                <span>Grand Total Amount:</span>
                                <span id="summary_grand_total" class="text-primary">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Success Message & Summary -->
                    <div class="section-step" data-step="4">
                        <div class="success-message text-center py-3">
                            <div class="success-icon mb-3"><i class="fas fa-check"></i></div>
                            <h3 class="fw-bold text-dark mb-2">Uniform Order Placed Successfully!</h3>
                            <p class="text-muted mb-4">
                                Thank you for your order. Order Code: <span id="successOrderCode" class="badge bg-primary fs-6 px-3 py-2"></span>
                            </p>

                            <!-- Order Summary Card -->
                            <div id="orderSuccessSummary" class="text-start bg-light border rounded-3 p-4 mb-4 mx-auto" style="max-width: 680px;">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-receipt me-2"></i>Order Summary Details
                                </h6>

                                <div class="row g-2 mb-3 small">
                                    <div class="col-6 col-md-3 text-muted">Student Name:</div>
                                    <div class="col-6 col-md-3 fw-bold text-dark" id="summary_student_name">-</div>
                                    <div class="col-6 col-md-3 text-muted">Parent Name:</div>
                                    <div class="col-6 col-md-3 fw-bold text-dark" id="summary_parent_name">-</div>

                                    <div class="col-6 col-md-3 text-muted">Branch / Level:</div>
                                    <div class="col-6 col-md-3 fw-bold text-dark" id="summary_branch_level">-</div>
                                    <div class="col-6 col-md-3 text-muted">Grade:</div>
                                    <div class="col-6 col-md-3 fw-bold text-dark" id="summary_grade">-</div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle bg-white mb-0" id="summary_items_table">
                                        <thead class="table-light">
                                            <tr class="small text-secondary">
                                                <th>Item</th>
                                                <th class="text-center" style="width: 70px;">Size</th>
                                                <th class="text-center" style="width: 60px;">Qty</th>
                                                <th class="text-end" style="width: 100px;">Price</th>
                                                <th class="text-end" style="width: 110px;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="summary_items_tbody" class="small">
                                        </tbody>
                                        <tfoot class="fw-bold">
                                            <tr>
                                                <td colspan="4" class="text-end">Grand Total:</td>
                                                <td class="text-end text-primary" id="summary_final_total">Rp 0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <button type="button" class="btn-custom btn-submit mt-3" id="backToFormBtn" onclick="location.reload();">
                                <i class="fas fa-redo me-2"></i> Place Another Order
                            </button>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="button-group" id="buttonGroup">
                        <button type="button" class="btn-custom btn-prev" id="prevBtn" style="display: none">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn-custom btn-next" id="nextBtn">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.6"></script>
    <script src="/assets/static/js/pages/uniform.js?v=1.0.2"></script>
</body>

</html>
