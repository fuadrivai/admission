@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/static/css/enrolment.css?v=1.0.0">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <p class="d-inline-flex gap-1">
                    <a data-bs-toggle="collapse" href="#collapse-filter" aria-expanded="false" aria-controls="collapse-filter">
                        Insert Filter <i class="fa fa-caret-down"></i>
                    </a>
                </p>
                <div class="collapse" id="collapse-filter">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter-name">Search</label>
                                <input placeholder="code, child name, parent name, email, phone" type="text"
                                    class="form-control" id="filter-name" name="filter-name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-start-date">Start date</label>
                                <input type="text"name="filter-start-date" class="form-control date-picker"
                                    id="filter-start-date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <lable for="filter-end-date">End date</lable>
                                <input disabled type="text" class="form-control date-picker" id="filter-end-date"
                                    name="filter-end-date">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-source-data">Parent</label>
                                <select id="filter-source-data" name="filter-source-data" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All</option>
                                    <option value="internal">Internal</option>
                                    <option value="external">External</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-data-from">Payment Source</label>
                                <select id="filter-data-from" name="filter-data-from" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All</option>
                                    <option value="custom_form">Custom Payment</option>
                                    <option value="web_form">Dashboard</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-regis-place">Place</label>
                                <select id="filter-regis-place" disabled name="filter-regis-place" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All</option>
                                    <option value="Exhibition">Exhibition</option>
                                    <option value="School">School</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-status">Status</label>
                                <select id="filter-status" name="filter-status" class="form-select" style="width: 100%">
                                    <option value="all">All status</option>
                                    <option value="PENDING">Pending</option>
                                    <option value="PAID">Paid</option>
                                    <option value="EXPIRED">Expired</option>
                                    <option value="CANCEL">Cancel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-branch">Branch</label>
                                <select id="filter-branch" name="filter-branch" class="form-select" style="width: 100%">
                                    <option value="all">All Branches</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-level">Level</label>
                                <select id="filter-level" disabled name="filter-level" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All Levels</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-grade">Grade</label>
                                <select id="filter-grade" disabled name="filter-grade" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All Grades</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-ay">Academic Year</label>
                                <select id="filter-ay" name="filter-ay" class="form-select" style="width: 100%">
                                    <option value="all">All Academic Years</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <hr>
                        <div class="col-md-12 text-center">
                            <button onclick="download()" class="btn btn-sm btn-success" type="submit">
                                <i class="fa fa-download"></i> Download excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="enrolment-list">
            @include('enrolment._list')
        </div>

        <!-- history/timeline modal -->
        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-secondary">
                        <h5 class="modal-title white" id="historyModalLabel">Enrolment History</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="historyContent">Loading...</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                            <span>Close</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="sourceDataModal" tabindex="-1" aria-labelledby="sourceDataModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="sourceDataModalLabel">Edit Source Data</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="source-data-enrolment-id">
                        <div class="form-group mb-0">
                            <label for="source-data-value" class="form-label">Source Data</label>
                            <select id="source-data-value" class="form-select">
                                <option value="internal">Internal</option>
                                <option value="external">External</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="save-source-data-btn">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
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
            getBranch()
            academicYear()
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
                $("#filter-level").append(`
                    <option value="all">All Levels</option>  
                `);
                levels = branch.levels;
                branch.levels.forEach((val) => {
                    $("#filter-level").append(`
                        <option value="${val.id}">${val.name}</option>    
                    `);
                });
                $("#filter-level").val("all").trigger('change');
            })

            $("#filter-level").on("change", function() {
                let levelVal = $(this).val()
                if (levelVal == "all") {
                    $("#filter-grade").attr("disabled", true);
                    $("#filter-grade").val("all").trigger('change');
                    return;
                }

                $("#filter-grade").attr("disabled", false);

                let levelId = $(this).val();
                const level = levels.find((l) => l.id == levelId);
                $("#filter-grade").empty();
                $("#filter-grade").append(`
                    <option value="all">All grades</option>  
                `);
                level.grades.forEach((val) => {
                    $("#filter-grade").append(`
                        <option value="${val.id}">${val.name}</option>    
                    `);
                });
                $("#filter-grade").val("all").trigger('change');
            });

            $('#filter-name').on('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    loadEnrolments()
                }, 400);
            });

            $('#filter-level, #filter-branch, #filter-status, #filter-source-data, #filter-ay')
                .on('change keyup', function() {
                    loadEnrolments();
                });

            $("#filter-data-from").on("change", function() {
                let value = $(this).val();

                if (value != "custom_form") {
                    $("#filter-regis-place").prop('disabled', true);
                    $("#filter-regis-place").val('all').trigger('change');
                    return;
                }

                $("#filter-regis-place").prop('disabled', false);
                $("#filter-regis-place").val('all').trigger('change');

                loadEnrolments();
            });

            $('#filter-regis-place').on('change', function() {
                loadEnrolments();
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
                loadEnrolments();
            });
            $("#filter-end-date").on("changeDate", function() {
                loadEnrolments();
            });

            $(document).on('click', '#enrolment-list .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadEnrolments(url);
            });

            $('#enrolment-list').on('click', '.view-history', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                $('#historyContent').html('Loading...');
                $('#historyModal').modal('show');

                $.get(`/enrolment/${id}/history`, function(html) {
                    $('#historyContent').html(html);
                }).fail(function() {
                    $('#historyContent').html(
                        '<div class="text-danger">Unable to load history.</div>');
                });
            });

            $('#enrolment-list').on('click', '.edit-source-data', function() {
                const enrolmentId = $(this).data('id');
                const sourceData = ($(this).data('source') || 'external').toString().toLowerCase();

                $('#source-data-enrolment-id').val(enrolmentId);
                $('#source-data-value').val(sourceData === 'internal' ? 'internal' : 'external');
                $('#sourceDataModal').modal('show');
            });

            $('#save-source-data-btn').on('click', function() {
                const enrolmentId = $('#source-data-enrolment-id').val();
                const sourceData = $('#source-data-value').val();

                if (!enrolmentId) {
                    toastify('Error', 'Enrolment ID not found', 'error');
                    return;
                }

                $.ajax({
                    url: `/enrolment/${enrolmentId}/source-data`,
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        source_data: sourceData
                    },
                    success: function() {
                        $('#sourceDataModal').modal('hide');
                        loadEnrolments();
                        toastify('success', 'Source data updated successfully', 'success');
                    },
                    error: function(err) {
                        toastify(
                            'Error',
                            err?.responseJSON?.message ?? 'Failed to update source data',
                            'error'
                        );
                    }
                });
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

        function academicYear() {
            blockUI();
            ajax(
                null,
                `/setting/year/get`,
                "GET",
                function(json) {
                    json.forEach((val) => {
                        $("#filter-ay").append(`
                            <option value="${val.id}">${val.name}</option>
                        `);
                    });
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
                source_data: $('#filter-source-data').val(),
                data_from: $('#filter-data-from').val(),
                regis_place: $('#filter-regis-place').val(),
                academic_year: $('#filter-ay').val(),
            };

            let queryString = new URLSearchParams(data).toString();
            window.location.href = `/enrolment/export?${queryString}`;
        }

        function loadEnrolments(url = "/enrolment") {
            const data = {
                search: $('#filter-name').val(),
                start_date: $('#filter-start-date').val(),
                end_date: $('#filter-end-date').val(),
                branch: $('#filter-branch').val(),
                academic_year: $('#filter-ay').val(),
                level: $('#filter-level').val(),
                grade: $('#filter-grade').val(),
                status: $('#filter-status').val(),
                source_data: $('#filter-source-data').val(),
                data_from: $('#filter-data-from').val(),
                regis_place: $('#filter-regis-place').val(),
            };

            $.ajax({
                url: url,
                data: data,
                type: "GET",
                success: function(html) {
                    $('#enrolment-list').html(html);
                }
            });
        }
    </script>
@endsection
