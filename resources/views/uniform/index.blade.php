@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/static/css/enrolment.css?v=1.0.0">
@endsection

@section('content-child')
    <section class="section">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <p class="d-inline-flex gap-1 mb-2">
                    <a data-bs-toggle="collapse" href="#collapse-filter" aria-expanded="false" aria-controls="collapse-filter"
                        class="fw-bold text-primary">
                        Insert Filter <i class="fa fa-caret-down ms-1"></i>
                    </a>
                </p>
                <div class="collapse" id="collapse-filter">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="filter-name" class="form-label fw-semibold small">Search</label>
                                <input placeholder="Order code, student name, parent name, email, phone" type="text"
                                    class="form-control" id="filter-name" name="filter-name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="filter-start-date" class="form-label fw-semibold small">Start date</label>
                                <input type="text" name="filter-start-date" class="form-control date-picker"
                                    id="filter-start-date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="filter-end-date" class="form-label fw-semibold small">End date</label>
                                <input disabled type="text" class="form-control date-picker" id="filter-end-date"
                                    name="filter-end-date">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="filter-status" class="form-label fw-semibold small">Payment Status</label>
                                <select id="filter-status" name="filter-status" class="form-select" style="width: 100%">
                                    <option value="all">All status</option>
                                    <option value="PENDING">Pending / Unpaid</option>
                                    <option value="PAID">Paid</option>
                                    <option value="EXPIRED">Expired</option>
                                    <option value="CANCEL">Cancel</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="filter-branch" class="form-label fw-semibold small">Branch</label>
                                <select id="filter-branch" name="filter-branch" class="form-select" style="width: 100%">
                                    @if (!auth()->check() || auth()->user()->role != 'user')
                                        <option value="all">All Branches</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="filter-level" class="form-label fw-semibold small">Level</label>
                                <select id="filter-level" disabled name="filter-level" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All Levels</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="filter-grade" class="form-label fw-semibold small">Grade</label>
                                <select id="filter-grade" disabled name="filter-grade" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All Grades</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <hr class="mb-3">
                        <div class="col-md-12 text-center">
                            <button onclick="download()" class="btn btn-sm btn-success fw-bold px-3" type="button">
                                <i class="fa fa-download me-1"></i> Download Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="uniform-order-list">
            @include('uniform._list')
        </div>

        <!-- Order Detail Modal -->
        <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white fw-bold" id="orderDetailModalLabel">
                            <i class="fa fa-receipt me-2"></i> Uniform Order Items
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" id="orderDetailContent">
                        <div class="text-center text-muted py-4">
                            <div class="spinner-border text-primary" role="status"><span
                                    class="visually-hidden">Loading...</span></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pickup Confirmation Modal -->
        <div class="modal fade" id="pickupModal" tabindex="-1" aria-labelledby="pickupModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="pickupForm">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white fw-bold" id="pickupModalLabel">
                                <i class="fa fa-box me-2"></i> Confirm Pickup
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <input type="hidden" id="pickup-order-id" name="order_id">
                            <p class="mb-3 text-muted">Please provide the details of the person picking up the uniform for
                                <strong id="pickup-student-name"></strong>.
                            </p>

                            <div class="mb-3">
                                <label for="pic_name" class="form-label fw-semibold">PIC Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pic_name" name="pic_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="parent_name" class="form-label fw-semibold">Parent Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_name" name="parent_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="note" class="form-label fw-semibold">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold" id="btn-submit-pickup">Confirm
                                Pickup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script src="/assets/extensions/moment/moment.js"></script>
    <script>
        let branches = [];
        let levels = [];
        let typingTimer;

        $(document).ready(function() {
            getBranch();

            $('#filter-branch').on('change', function() {
                let branchVal = $(this).val();
                if (branchVal == "all") {
                    $('#filter-level').attr('disabled', true);
                    $('#filter-level').val('all').trigger('change');
                    $('#filter-grade').attr('disabled', true);
                    $('#filter-grade').val('all').trigger('change');
                    return;
                }
                $('#filter-level').attr('disabled', false);
                const branch = branches.find((b) => b.id == branchVal);
                $("#filter-level").empty();
                $("#filter-level").append(`<option value="all">All Levels</option>`);
                if (branch && branch.levels) {
                    levels = branch.levels;
                    branch.levels.forEach((val) => {
                        $("#filter-level").append(`<option value="${val.id}">${val.name}</option>`);
                    });
                }
                $("#filter-level").val("all").trigger('change');
            });

            $("#filter-level").on("change", function() {
                let levelVal = $(this).val();
                if (levelVal == "all") {
                    $("#filter-grade").attr("disabled", true);
                    $("#filter-grade").val("all").trigger('change');
                    return;
                }

                $("#filter-grade").attr("disabled", false);
                const level = levels.find((l) => l.id == levelVal);
                $("#filter-grade").empty();
                $("#filter-grade").append(`<option value="all">All Grades</option>`);
                if (level && level.grades) {
                    level.grades.forEach((val) => {
                        $("#filter-grade").append(`<option value="${val.id}">${val.name}</option>`);
                    });
                }
                $("#filter-grade").val("all").trigger('change');
            });

            $('#filter-name').on('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    loadOrders();
                }, 400);
            });

            $('#filter-level, #filter-branch, #filter-grade, #filter-status')
                .on('change', function() {
                    loadOrders();
                });

            $("#filter-start-date").on("changeDate", function() {
                let value = $(this).val();

                if (value == "") {
                    $("#filter-end-date").prop('disabled', true);
                    $("#filter-end-date").val('');
                    return;
                }

                let startDate = moment(value, "DD MMMM YYYY").format("YYYY-MM-DD");

                $("#filter-end-date").prop('disabled', false);
                $("#filter-end-date").val('');
                $("#filter-end-date").datepicker("setStartDate", new Date(startDate));
                loadOrders();
            });

            $("#filter-end-date").on("changeDate", function() {
                loadOrders();
            });

            $(document).on('click', '#uniform-order-list .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadOrders(url);
            });

            $(document).on('click', '.confirm-uniform-pickup', function() {
                const button = $(this);
                const studentName = button.data('student');
                const orderId = button.data('order');

                $('#pickup-student-name').text(studentName);
                $('#pickup-order-id').val(orderId);

                // Reset form
                $('#pickupForm')[0].reset();
                $('#pickupModal').modal('show');
            });

            $('#pickupForm').on('submit', function(e) {
                e.preventDefault();

                const orderId = $('#pickup-order-id').val();
                const btnSubmit = $('#btn-submit-pickup');

                btnSubmit.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: `/uniform/${orderId}/pickup`,
                    type: 'POST',
                    data: JSON.stringify({
                        pic_name: $('#pic_name').val(),
                        parent_name: $('#parent_name').val(),
                        note: $('#note').val()
                    }),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(json) {
                        $('#pickupModal').modal('hide');
                        toastify('Success', json.message, 'success');
                        loadOrders();
                    },
                    error: function(xhr) {
                        toastify('Error', xhr.responseJSON?.message ??
                            'Unable to confirm pickup.', 'error');
                    },
                    complete: function() {
                        btnSubmit.prop('disabled', false).html('Confirm Pickup');
                    }
                });
            });

            // View Order Detail Modal
            $(document).on('click', '.view-order-details', function(e) {
                e.preventDefault();
                const detailsJson = $(this).attr('data-details');

                let details = [];
                try {
                    details = JSON.parse(detailsJson);
                } catch (e) {
                    details = [];
                }

                let html = `
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th class="text-center" style="width: 80px;">Size</th>
                                    <th class="text-center" style="width: 70px;">Qty</th>
                                    <th class="text-end" style="width: 120px;">Price</th>
                                    <th class="text-end" style="width: 130px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                if (details && details.length > 0) {
                    details.forEach(item => {
                        const priceFormatted = 'Rp ' + (parseFloat(item.price) || 0).toLocaleString(
                            'id-ID');
                        const subtotalFormatted = 'Rp ' + (parseFloat(item.subtotal) || 0)
                            .toLocaleString('id-ID');
                        html += `
                            <tr>
                                <td><span class="fw-bold text-dark">${item.product_name}</span> <small class="text-muted">(${item.product_code})</small></td>
                                <td class="text-center">${item.size || '-'}</td>
                                <td class="text-center fw-bold">${item.qty}</td>
                                <td class="text-end">${priceFormatted}</td>
                                <td class="text-end fw-semibold text-primary">${subtotalFormatted}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `<tr><td colspan="5" class="text-center text-muted">No items found</td></tr>`;
                }

                html += `</tbody></table></div>`;
                $('#orderDetailContent').html(html);
                $('#orderDetailModal').modal('show');
            });
        });

        function getBranch() {
            blockUI();
            ajax(
                null,
                `/branch/get`,
                "GET",
                function(json) {
                    branches = json;
                    branches.forEach((val) => {
                        $("#filter-branch").append(`
                            <option value="${val.id}">${val.name}</option>
                        `);
                    });

                    $("#filter-branch").trigger("change");
                },
                function(err) {
                    toastify(
                        "Error",
                        err?.responseJSON?.message ?? "Please try again later",
                        "error"
                    );
                }
            );
        }

        function download() {
            const data = {
                search: $('#filter-name').val(),
                start_date: $('#filter-start-date').val(),
                end_date: $('#filter-end-date').val(),
                branch: $('#filter-branch').val(),
                level: $('#filter-level').val(),
                grade: $('#filter-grade').val(),
                status: $('#filter-status').val(),
            };

            let queryString = new URLSearchParams(data).toString();
            window.location.href = `/uniform/export?${queryString}`;
        }

        function loadOrders(url = "/uniform") {
            const data = {
                search: $('#filter-name').val(),
                start_date: $('#filter-start-date').val(),
                end_date: $('#filter-end-date').val(),
                branch: $('#filter-branch').val(),
                level: $('#filter-level').val(),
                grade: $('#filter-grade').val(),
                status: $('#filter-status').val(),
            };

            $.ajax({
                url: url,
                data: data,
                type: "GET",
                success: function(html) {
                    $('#uniform-order-list').html(html);
                }
            });
        }
    </script>
@endsection
