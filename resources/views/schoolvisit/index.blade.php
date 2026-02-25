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
                </div>
            </div>
        </div>

        <div id="schoolvisit-list" class="mt-3">
            @include('schoolvisit._list')
        </div>

        <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
            aria-hidden="true">
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
                                    <label for="date" class="form-label required-label">Observation Date</label>
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

            $('#filter-level, #filter-branch, #filter-status').on('change', function() {
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
        });

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
                    $('#schoolvisit-list').html(html);
                }
            });
        }

        async function changeStatus(status, id) {
            const data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id,
                status: status
            }
            try {
                blockUI();
                let visit = await ajaxPromise(
                    data,
                    `/schoolvisit/${data.id}`,
                    "PUT",
                );
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
                    "Error",
                    err?.responseJSON?.message ?? "Please try again later",
                    "bottom",
                );
            }
            $('#btn-accept').attr('disabled', false)
        }
    </script>
@endsection
