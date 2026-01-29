@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <p class="d-inline-flex gap-1">
                    <a data-bs-toggle="collapse" href="#collapse-filter" aria-expanded="false"
                        aria-controls="collapse-filter">
                        Insert Filter <i class="fa fa-caret-down"></i>
                    </a>
                </p>
                <div class="collapse" id="collapse-filter">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <lable for="filter-code">Code</lable>
                                <input type="text" class="form-control" id="filter-code" name="filter-code">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <lable for="filter-name">Parent / Child name</lable>
                                <input type="text" class="form-control" id="filter-name" name="filter-name">
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
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-level">Level</label>
                                <select id="filter-level" name="filter-level" class="form-select" style="width: 100%">
                                    <option value="all">All Levels</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter-grade">Grade</label>
                                <select id="filter-grade" disabled name="filter-grade" class="form-select"
                                    style="width: 100%">
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
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-visit">
                            <thead>
                                <tr class="text-center">
                                    <th>Code</th>
                                    <th>Parent's name</th>
                                    <th>Child's Name</th>
                                    <th>Contact</th>
                                    <th>Level</th>
                                    <th>Grade</th>
                                    <th>Branch</th>
                                    <th>Date Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                                <span id="list-time"></span>
                                <input type="hidden" name="time" id="selectedTime" required>
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
                            <span class="d-none d-sm-block">Accept</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('content-script')
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script>
        let levels = [];
        $(document).ready(function() {
            getLevel();
            tblVisit = $('#tbl-visit').DataTable({
                searching: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "schoolvisit/datatables",
                    type: "GET",
                    data: function(d) {
                        d.level_id = $('#filter-level').val();
                        d.grade_id = $('#filter-grade').val();
                        d.startDate = $('#filter-start-date').val();
                        d.endDate = $('#filter-end-date').val();
                        d.name = $('#filter-name').val();
                        d.code = $('#filter-code').val();
                        d.branch_id = $('#filter-branch').val();
                        d.status = $('#filter-status').val();
                    }
                },
                columns: [{
                        data: "code",
                        defaultContent: "--",
                    },
                    {
                        data: "parent_name",
                        defaultContent: "--",
                    },
                    {
                        data: "child_name",
                        defaultContent: "--",
                    },
                    {
                        data: "email",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            return `${data} <br> ${full.phone_number}`
                        }
                    },
                    {
                        data: 'level_name',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let bg = ""
                            switch (full.level_name) {
                                case "Playgroup":
                                case "Kindergarten":
                                    bg = "bg-warning"
                                    break;
                                case "Primary":
                                    bg = "bg-success"
                                    break;
                                case "Lower Secondary":
                                    bg = "bg-info"
                                    break;
                                case "Upper Secondary":
                                    bg = "bg-danger"
                                    break;
                                case "Development Class":
                                    bg = "bg-primary"
                                    break;
                                default:
                                    bg = "bg-primary"
                                    break;
                            }
                            return `<small class="badge ${bg}">${full.level_name}</small>`
                        }
                    },
                    {
                        data: 'grade_name',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let bg = ""
                            switch (full.level_name) {
                                case "Playgroup":
                                case "Kindergarten":
                                    bg = "bg-warning"
                                    break;
                                case "Primary":
                                    bg = "bg-success"
                                    break;
                                case "Lower Secondary":
                                    bg = "bg-info"
                                    break;
                                case "Upper Secondary":
                                    bg = "bg-danger"
                                    break;
                                case "Development Class":
                                    bg = "bg-primary"
                                    break;
                                default:
                                    bg = "bg-primary"
                                    break;
                            }
                            return `<small class="badge ${bg}">${full.grade_name}</small>`
                        }
                    },
                    {
                        data: "branch_name",
                        defaultContent: "--",
                    },

                    {
                        data: "date",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let date = moment(data).format("DD MMMM YYYY")
                            let time = moment(full.time, "HH:mm:ss").format("HH:mm")
                            return `<label>${date}</label><br><label>${time}</label>`
                        }
                    },
                    {
                        data: "status",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let bg = "";
                            switch (data) {
                                case "registered":
                                    bg = "bg-info"
                                    break;
                                case "present":
                                    bg = "bg-success"
                                    break;
                                case "absent":
                                    bg = "bg-danger"
                                    break;
                                default:
                                    bg = "bg-default"
                                    break;
                            }
                            return `<span class="badge ${bg}">${data}</span>`
                        }
                    },
                    {
                        data: 'id',
                        mRender: function(data, type, full) {
                            return `
                            <div class="dropdown dropdown-color-icon">
                                <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenuButtonEmoji" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">Actions
                                </button>
                                <div class="dropdown-menu bg-danger ob" aria-labelledby="dropdownMenuButtonEmoji">
                                    <a class="dropdown-item text-white btn-edit" href="#"><i class="fa fa-pencil"></i> Reschedule</a>
                                    <a class="dropdown-item text-white btn-confirm" href="#"><i class="bi bi-star"></i> Confirm</a>
                                    <a class="dropdown-item text-white btn-detail" href="#"><i class="fa fa-eye"></i> Details</a>
                                </div>
                            </div>`
                        }
                    }
                ],
                order: [
                    [7, "desc"]
                ]
            });

            $('#filter-level, #filter-grade, #filter-start-date, #filter-end-date, #filter-name, #filter-code, #filter-branch, #filter-status')
                .on('change keyup', function() {
                    tblVisit.ajax.reload();
                });

            // $('#collapse-filter').on('hidden.bs.collapse', function() {
            //     $(this).find('input, select').val('');
            //     $('#filter-end-date, #filter-grade').attr('disabled', true)
            // });

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
        });

        function getLevel() {
            ajax(
                null,
                `/level/get`,
                "GET",
                function(json) {
                    levels = json;
                    levels.forEach((val) => {
                        $("#filter-level").append(`
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
    </script>
@endsection
