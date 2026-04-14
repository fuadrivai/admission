@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/static/css/enrolment.css?v=1.0.1">
    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <p class="d-inline-flex gap-1">
                    <a data-bs-toggle="collapse" href="#collapse-filter" aria-expanded="false" aria-controls="collapse-filter">
                        <i class="fa fa-filter"></i> Insert Filter <i class="fa fa-caret-down"></i>
                    </a>
                </p>
                <div class="collapse" id="collapse-filter">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter-name">Search</label>
                                <input placeholder="code, child name, parent name, email" type="text"
                                    class="form-control" id="filter-name" name="filter-name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-enrol">Already Enrol</label>
                                <select id="filter-enrol" name="filter-enrol" class="form-select" style="width: 100%">
                                    <option value="all">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-payment">Payment</label>
                                <select disabled id="filter-payment" name="filter-payment" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All</option>
                                    <option value="PENDING">PENDING</option>
                                    <option value="PAID">PAID</option>
                                    <option value="EXPIRED">EXPIRED</option>
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
                                <label for="filter-status">Status</label>
                                <select id="filter-status" name="filter-status" class="form-select" style="width: 100%">
                                    <option value="all">All status</option>
                                    <option value="registered">Registered</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="cancelled">Cancelled</option>
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


        <div id="schoolvisit-list" class="mt-3">
            @include('schoolvisit._list')
        </div>

        <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <form id="form-date" action="" autocomplete="off" class="needs-validation" novalidate>
                        @csrf
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title white" id="myModalLabel160">Edit Schedule Date
                            </h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="d-none" id="id">
                                    <label for="date" class="form-label required-label">Visit Date</label>
                                    <input type="text" name="date" class="form-control date-picker" required
                                        id="date">
                                    <div class="invalid-feedback">
                                        Insert a valid date
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label required-label">Jam Tersedia : </label>
                                    <select disabled name="time" id="selectedTime" class="form-select" required>
                                        <option selected disabled>Select Time</option>
                                        <option value="07:30">07:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="13:00">13:00</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        choose the time
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label for="date" class="form-label required-label">Reason</label>
                                    <textarea class="form-control" required name="note" id="note" rows="3"></textarea>
                                    <div class="invalid-feedback">
                                        Insert a valid reason
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" id="btn-accept" class="btn btn-primary ms-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- modal confirmation present --}}
        <div class="modal fade text-left" id="modal-present" tabindex="-1" role="dialog"
            aria-labelledby="myModalConfirmation" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <form id="form-confirm" action="" autocomplete="off" class="needs-validation" novalidate>
                        @csrf
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title white" id="myModalConfirmation">Visit Confirmation</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="admission_staff" class="form-label required-label">Admission Staff</label>
                                    <input type="text" name="admission_staff" class="form-control" required
                                        id="admission_staff">
                                    <div class="invalid-feedback">
                                        Insert a valid date
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label for="date" class="form-label required-label">Enrolment
                                        Consideration</label>
                                    <textarea class="form-control" required name="enrolment_consideration" id="enrolment_consideration" rows="3"></textarea>
                                    <div class="invalid-feedback">
                                        Insert a valid text
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="d-none" id="id-visit">
                                    <label for="reason_for_enrolment" class="form-label required-label">Enrolment
                                        Reason</label>
                                    <select name="reason_for_enrolment" class="form-select reason"
                                        id="reason_for_enrolment" required>
                                        <option value="">Select Reason</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Insert a valid reason
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="d-none" id="id-visit">
                                    <label for="reason_for_not_enrolment" class="form-label required-label">Not Enrolment
                                        Reason</label>
                                    <select name="reason_for_not_enrolment" class="form-select reason"
                                        id="reason_for_not_enrolment" required>
                                        <option value="">Select Reason</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Insert a valid reason
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" id="btn-confirm" class="btn btn-primary ms-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade text-left" id="modal-cancel" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <form id="form-cancel" action="" autocomplete="off" class="needs-validation" novalidate>
                        @csrf
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title white" id="myModalLabel160">Cancel Visit
                            </h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="date" class="form-label required-label">Reason</label>
                                    <input type="hidden" id="id-cancel">
                                    <textarea class="form-control" required name="note" id="note-cancel" rows="3"></textarea>
                                    <div class="invalid-feedback">
                                        Insert a valid reason
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" id="btn-cancel" class="btn btn-primary ms-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- history/timeline modal -->
        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-secondary">
                        <h5 class="modal-title white" id="historyModalLabel">Visit History</h5>
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

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-center"><b>Are you sure you want to delete the selected visits?
                                <br>This action cannot be undone.</b>
                        </p>
                        <div id="selectedVisitsList" class="mt-3">
                            <!-- Selected visits will be listed here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button onclick="deleteSelected()" type="button" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Delete Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection


@section('content-script')
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/static/js/constant.js"></script>
    <script>
        let branches = [];
        let levels = [];
        let typingTimer;
        let selectedVisitId = [];
        $(document).ready(function() {
            getBranch();
            getEnrolmentReasons();
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
                    loadSchoolvisit()
                }, 400);
            });

            $('#filter-level, #filter-branch, #filter-status , #filter-grade, #filter-start-date, #filter-end-date, #filter-enrol, #filter-payment')
                .on('change keyup', function() {
                    loadSchoolvisit();
                });

            $(document).on('click', '#schoolvisit-list .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadSchoolvisit(url);
            });

            $('#schoolvisit-list').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                $('#id').val(id);
                $('#primary').modal('show')
            })

            // open history/timeline modal
            $('#schoolvisit-list').on('click', '.btn-history', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                $('#historyContent').html('Loading...');
                $('#historyModal').modal('show');

                $.get(`/schoolvisit/${id}/history`, function(html) {
                    $('#historyContent').html(html);
                }).fail(function() {
                    $('#historyContent').html(
                        '<div class="text-danger">Unable to load history.</div>');
                });
            })
            $('#schoolvisit-list').on('click', '.btn-confirm', function(e) {
                const id = $(this).data('id');
                $('#id-visit').val(id);
                $('#modal-present').modal('show')
            })
            $('#schoolvisit-list').on('click', '.btn-cancel', function(e) {
                const id = $(this).data('id');
                $('#id-cancel').val(id);
                $('#modal-cancel').modal('show')
            })

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
            });

            $('#filter-enrol').on('change', function() {
                if (this.value == "yes") {
                    $('#filter-payment').attr('disabled', false);
                } else {
                    $('#filter-payment').attr('disabled', true);
                    $('#filter-payment').val('all').trigger('change');
                }
            })


            $("#date").on("changeDate", function() {
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

            $("#selectedTime").change(function() {
                const time = this.value;
                if (time < "07:30" || time > "15:00") {
                    this.setCustomValidity(
                        "Please select a time between 07:30 and 15:00.",
                    );
                    $(this).addClass("is-invalid");
                } else {
                    let date = moment($("#date").val(), "DD MMMM YYYY").format(
                        "YYYY-MM-DD",
                    );
                    checkCapacity(date, `${time}:00`, this);
                }
            });

            $('#form-date').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                if (form.checkValidity()) {
                    $('#btn-accept').attr('disabled', true)
                    $(form).addClass("was-validated");

                    if ($("#selectedTime").val() == "" || $("#selectedTime").val() == null) {
                        $("#selectedTime").addClass("is-invalid");
                        form.reportValidity();
                        return false;
                    }
                    $('.modal').modal('hide')
                    blockUI();
                    reschedule();
                } else {
                    $(form).addClass("was-validated");
                    form.reportValidity();
                    $('#btn-accept').attr('disabled', false)
                }
            })

            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form')[0]?.reset();
                $(this).find('form').removeClass('was-validated');
                $(this).find('.is-invalid').removeClass('is-invalid');
                $('button').attr('disabled', false);
            })

            $('#form-confirm').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                if (form.checkValidity()) {
                    $('#btn-confirm').attr('disabled', true)
                    $(form).addClass("was-validated");

                    if ($("#enrolment_consideration").val() == "" || $("#enrolment_consideration").val() ==
                        null) {
                        $("#enrolment_consideration").addClass("is-invalid");
                        form.reportValidity();
                        return false;
                    }

                    $('.modal').modal('hide')
                    blockUI();
                    changeStatus('present', {
                        id: $('#id-visit').val(),
                        enrolment_consideration: $('#enrolment_consideration').val(),
                        reason_for_enrol: $('#reason_for_enrolment').val(),
                        reason_not_enrol: $('#reason_for_not_enrolment').val(),
                        admission_staff: $('#admission_staff').val(),
                    });
                } else {
                    $(form).addClass("was-validated");
                    form.reportValidity();
                    $('#btn-confirm').attr('disabled', false)
                }
            })
            $('#form-cancel').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                if (form.checkValidity()) {
                    $('#btn-cancel').attr('disabled', true)
                    $(form).addClass("was-validated");

                    if ($("#note-cancel").val() == "" || $("#note-cancel").val() ==
                        null) {
                        $("#note-cancel").addClass("is-invalid");
                        form.reportValidity();
                        return false;
                    }

                    $('.modal').modal('hide')
                    blockUI();
                    changeStatus('cancelled', {
                        id: $('#id-cancel').val(),
                        note: $('#note-cancel').val(),
                        status: 'cancelled'
                    });
                } else {
                    $(form).addClass("was-validated");
                    form.reportValidity();
                    $('#btn-cancel').attr('disabled', false)
                }
            })

            $(document).on('change', '.visit-checkbox', function() {
                const visitId = $(this).val();
                if ($(this).is(':checked')) {
                    selectedVisitId.push({
                        id: visitId,
                        name: $(this).closest('.card').find('.student-name').text(),
                        code: $(this).closest('.card').find('.code').text()
                    });
                } else {
                    selectedVisitId = selectedVisitId.filter(visit => visit.id != visitId);
                }
                toggleDeleteButton();
            });

            $(document).on('click', '.btn-delete', async function() {
                const visitId = $(this).data('id');
                blockUI();
                let confirmDelete = confirm("Are you sure you want to delete selected data?");
                if (!confirmDelete) return;

                try {
                    let data = await ajaxPromise(null, `/schoolvisit/${visitId}`, "DELETE");
                    toastify("success", "Selected visits deleted successfully", "bottom");
                    toggleDeleteButton();
                    loadSchoolvisit();
                    $('#deleteModal').modal('hide');
                } catch (error) {
                    toastify("error", err?.responseJSON?.message ?? "Failed to delete selected visits",
                        "bottom");
                }

            })

            $('#schoolvisit-list').on('click', '#delete-selected', function() {
                if (selectedVisitId.length === 0) return;

                let listHtml = '<ol class="list-group list-group-numbered">';
                selectedVisitId.forEach(visit => {
                    listHtml +=
                        `<li class="list-group-item">Kode: ${visit.code} - ${visit.name}</li>`;
                });
                listHtml += '</ol>';
                $('#selectedVisitsList').html(listHtml);
                $('#deleteModal').modal('show');
            });
        });

        function toggleDeleteButton() {
            if (selectedVisitId.length > 0) {
                $('#delete-selected').html(`<i class="fa fa-trash"></i> Delete Selected (${selectedVisitId.length})`)
                    .show();
            } else {
                $('#delete-selected').hide();
            }
        }

        function checkCapacity(date, time, dataThis) {
            ajax(
                null,
                `school-visit/capacity/check?date=${date}&time=${time}`,
                "GET",
                function(json) {
                    if (json == true || json == "true") {
                        $(dataThis)
                            .siblings(".invalid-feedback")
                            .text(
                                "The selected time slot for the school visit is already full. Kindly choose a different time.",
                            );
                        $(dataThis).addClass("is-invalid");
                        $("#selectedTime").val("").trigger("change");
                    } else {
                        dataThis.setCustomValidity("");
                        $(dataThis).removeClass("is-invalid");
                    }
                },
            );
        }

        function changeVisitTime(isDisabled) {
            $("#selectedTime").attr("disabled", isDisabled);
            $("#selectedTime").val("").trigger("change");
        }

        function checkHoliday(date, dataThis) {
            blockUI();
            ajax(null, `/holiday/check/${date}`, "GET", function(json) {
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

        function loadSchoolvisit(url = "{{ url('/schoolvisit') }}") {
            blockUI();
            const data = {
                search: $('#filter-name').val(),
                start_date: $('#filter-start-date').val(),
                end_date: $('#filter-end-date').val(),
                branch: $('#filter-branch').val(),
                level: $('#filter-level').val(),
                grade: $('#filter-grade').val(),
                status: $('#filter-status').val(),
                enrol: $('#filter-enrol').val(),
                payment: $('#filter-payment').val(),
            };

            $.ajax({
                url: url,
                data: data,
                type: "GET",
                success: function(html) {
                    $('#schoolvisit-list').html(html);
                    $('.visit-checkbox').each(function() {
                        if (selectedVisitId.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });
                    toggleDeleteButton();
                    unBlockUI();
                },
                error: function(err) {
                    toastify(
                        "Error",
                        err?.responseJSON?.message ?? "Please try again later",
                        "error"
                    );
                    unBlockUI();
                }
            });
        }

        async function changeStatus(status, data) {
            data._token = $('meta[name="csrf-token"]').attr('content');
            data.status = status;

            try {
                blockUI();
                let visit = await ajaxPromise(data, `/schoolvisit/${data.id}`, "PUT");
                toastify("success", "Status berhasil diubah", "bottom");
                setTimeout(function() {
                    loadSchoolvisit();
                }, 1000);
            } catch (err) {
                toastify(
                    "Error",
                    err?.responseJSON?.message ?? "Please try again later",
                    "bottom",
                );
            }
        }

        async function reschedule() {
            const data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: $('#id').val(),
                date: moment($('#date').val(), 'DD MMMM YYYY').format('YYYY-MM-DD'),
                time: $('#selectedTime').val(),
                note: $('#note').val(),
                status: 'registered'
            }
            try {
                blockUI();
                let visit = await ajaxPromise(
                    data,
                    `/schoolvisit/${data.id}`,
                    "PUT",
                );
                toastify("success", "Data berhasil disimpan", "bottom");
                setTimeout(function() {
                    loadSchoolvisit();
                }, 1000);
            } catch (err) {
                toastify(
                    "error",
                    err?.responseJSON?.message ?? "Please try again later",
                    "bottom",
                );
            }
            $('#btn-accept').attr('disabled', false)
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
                enrol: $('#filter-enrol').val(),
                payment: $('#filter-payment').val(),
            };

            let queryString = new URLSearchParams(data).toString();
            window.location.href = `/schoolvisit/export?${queryString}`;
        }

        function getEnrolmentReasons() {
            enrolReasons.forEach(reason => {
                $('.reason').append(`
                    <option value="${reason}">${reason}</option>
                `);
            });
        }

        async function deleteSelected() {
            if (selectedVisitId.length === 0) return;
            blockUI();
            let codes = selectedVisitId.map(visit => visit.code);

            try {
                let data = await ajaxPromise({
                    codes
                }, `/schoolvisit/delete/many`, "POST");
                toastify("success", "Selected visits deleted successfully", "bottom");
                selectedVisitId = [];
                toggleDeleteButton();
                loadSchoolvisit();
                $('#deleteModal').modal('hide');
            } catch (error) {
                toastify("error", err?.responseJSON?.message ?? "Failed to delete selected visits", "bottom");
            }
        }
    </script>
@endsection
