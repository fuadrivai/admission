@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <style>
        .time-badge {
            cursor: pointer;
            padding: 12px 24px;
            margin: 6px;
            border-radius: 12px;
            background-color: #f8f9fa;
            color: #495057;
            transition: all 0.3s ease;
            display: inline-block;
            font-weight: 500;
            border: 1.5px solid #e2e8f0;
        }

        .time-badge:hover {
            background-color: #b3294c;
            color: white;
            transform: translateY(-2px);
            border-color: #b3294c;
        }

        .time-badge.selected {
            background-color: #9B1134;
            color: white;
            border-color: #9B1134;
            box-shadow: 0 4px 8px rgba(181, 51, 137, 0.25);
        }

        .time-badge.disabled {
            background-color: #e9ecef;
            color: #adb5bd;
            border-color: #dee2e6;
            cursor: not-allowed;
            pointer-events: none;
            box-shadow: none;
            transform: none;
        }

        .ob:hover {
            color: blue !important;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-setting-date">
                            <thead>
                                <tr class="text-center">
                                    <th>Child's Name</th>
                                    <th>Level</th>
                                    <th>Grade</th>
                                    <th>Gender</th>
                                    <th>Parent's name</th>
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
        let observationDateId;
        $(document).ready(function() {
            tblUserObservation = $('#tbl-setting-date').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('observation.user-datatables') }}",
                    type: "GET",
                },
                columns: [{
                        data: "child_name",
                        defaultContent: "--",
                    },
                    {
                        data: 'level',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let bg = ""
                            switch (full.level) {
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
                            return `<small class="badge ${bg}">${full.level}</small>`
                        }
                    },
                    {
                        data: 'grade',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let bg = ""
                            switch (full.level) {
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
                            return `<small class="badge ${bg}">${full.grade}</small>`
                        }
                    },
                    {
                        data: 'gender',
                        defaultContent: "-",
                        className: "text-center",
                    },
                    {
                        data: "parent_name",
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
                                case "Registered":
                                    bg = "bg-info"
                                    break;
                                case "Present":
                                    bg = "bg-success"
                                    break;
                                case "Absent":
                                    bg = "bg-danger"
                                    break;
                                default:
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
                    [4, "asc"]
                ]
            });

            $('#tbl-setting-date').on('click', '.btn-edit', function() {
                let data = tblUserObservation.row($(this).parents('tr')).data();
                $('#id').val(data.id);
                $('#primary').modal('show')
            })

            $('#date').on('changeDate', function() {
                $('#list-time').empty()
                let date = moment($(this).val(), "DD MMMM YYYY").format("YYYY-MM-DD")
                getObservationDate(date);
            })

            $('#list-time').on('click', '.time-badge', function() {
                $('.time-badge').removeClass('selected');
                $(this).addClass('selected');
                let time = $(this).attr("data-time");
                observationDateId = $(this).attr("data-id");
                $('#selectedTime').val(time)
                $("#selectedTime").removeClass("is-invalid").addClass("is-valid");
            })

            $('.modal').on('hidden.bs.modal', function(event) {
                $('#list-time').empty()
            })

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

                    let data = $(form).serializeArray();
                    let dataJSON = {};
                    $.each(data, function() {
                        dataJSON[this.name] = this.value;
                    });
                    dataJSON.observation_time_id = observationDateId;
                    dataJSON.id = $('#id').val();
                    dataJSON.date = moment(dataJSON.date, "DD MMMM YYYY").format("YYYY-MM-DD")
                    $('.modal').modal('hide')
                    blockUI();
                    postObservation(dataJSON);
                } else {
                    $(form).addClass("was-validated");
                    form.reportValidity();
                    $('#btn-accept').attr('disabled', false)
                }
            })
        });

        function getObservationDate(date) {
            ajax(null, `/observation/get/date/${date}`, 'GET', function(json) {
                $('#list-time').empty()

                if ((json?.times ?? []).length < 1) {
                    $('#list-time').append(`
                <span class="time-badge disabled">No Time Available</span>
            `)
                    return false
                }

                let today = moment().format('YYYY-MM-DD');
                let now = moment();
                let selectedDate = moment(date, 'YYYY-MM-DD');

                json.times.forEach(e => {
                    let time = moment(e.time, "HH:mm:ss");
                    let formattedTime = time.format('HH:mm');
                    let rest = parseInt(e.rest);

                    let isDisabled = false;

                    // 1. Jika tanggal yang dipilih sudah lewat
                    if (selectedDate.isBefore(today)) {
                        isDisabled = true;
                    }
                    // 2. Jika tanggal yang dipilih adalah hari ini dan jam sudah lewat
                    else if (date === today && time.isBefore(now)) {
                        isDisabled = true;
                    }
                    // 3. Jika quota habis
                    else if (rest < 1) {
                        isDisabled = true;
                    }

                    $('#list-time').append(`
                <span class="time-badge ${isDisabled ? 'disabled' : ''}" 
                      data-id="${e.id}" 
                      data-time="${formattedTime}">
                      ${formattedTime}
                </span>
            `)
                });

                $('#selectedTime').val("")
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
            });
        }

        function postObservation(data) {
            ajax(data, `/observation/${data.id}`, 'PUT', function(json) {
                $('#btn-accept').attr('disabled', false)
                toastify("success", "Observasi Berhasil di rubah");
                setTimeout(() => {
                    window.location.href = "/observation";
                }, 1500);
            }, function(err) {
                $('#btn-accept').attr('disabled', false)
                toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
            });
        }
    </script>
@endsection
