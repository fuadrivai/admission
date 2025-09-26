@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <style>
        .dt-button {
            margin-left: 0.5rem;
            margin-bottom: 0.5rem
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    List Available Dates for Observation
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-setting-date">
                            <thead>
                                <tr class="text-center">
                                    <th>Date</th>
                                    <th>Division</th>
                                    <th>Times</th>
                                    <th>Quota</th>
                                    <th>Available</th>
                                    <th>Active</th>
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
@endsection


@section('content-script')
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script>
        $(document).ready(function() {
            tbldate = $('#tbl-setting-date').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add Schedule <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-assign'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        window.location.href = "/observation/date/create";
                    }
                }],
                language: {
                    info: "Page _PAGE_ of _PAGES_",
                    lengthMenu: "_MENU_ ",
                    search: "",
                    searchPlaceholder: "Search.."
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('observation.datatables') }}",
                    type: "GET",
                },
                columns: [{
                        data: "date",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            return moment(data).format('DD MMMM YYYY');
                        }
                    },
                    {
                        data: 'division_name',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let bg = ""
                            switch (data) {
                                case "Preschool":
                                    bg = "bg-warning"
                                    break;
                                case "Primary":
                                    bg = "bg-success"
                                    break;
                                case "Secondary":
                                    bg = "bg-info"
                                    break;
                                case "Development Class":
                                    bg = "bg-primary"
                                    break;
                                default:
                                    bg = "bg-danger"
                                    break;
                            }
                            return `<small class="badge ${bg}">${data}</small>`;
                        }
                    },
                    {
                        data: 'times',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let arr = data.split(',');
                            if (arr.length > 0) {
                                let times = arr.map(item => {
                                    return `<span class="badge bg-secondary">${moment(item, 'HH:mm:ss').format('HH:mm')}</span>`;
                                });
                                return times.join('<br>');
                            } else {
                                return `<span class="text-danger">No Time Assigned</span>`;
                            }
                        }
                    },
                    {
                        data: "quota",
                        defaultContent: "--",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let arr = data.split(',');
                            if (arr.length > 0) {
                                let times = arr.map(item => {
                                    return `<span class="badge bg-secondary">${item}</span>`;
                                });
                                return times.join('<br>');
                            } else {
                                return `<span class="text-danger">No Quota Available</span>`;
                            }
                        }
                    },
                    {
                        data: "available",
                        defaultContent: "--",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let arr = data.split(',');
                            if (arr.length > 0) {
                                let times = arr.map(item => {
                                    let int = parseInt(item);
                                    if (int <= 0) {
                                        return `<span class="badge bg-danger">${item}</span>`;
                                    }
                                    return `<span class="badge bg-secondary">${item}</span>`;
                                });
                                return times.join('<br>');
                            } else {
                                return `<span class="text-danger"></span>`;
                            }
                        }
                    },
                    {
                        data: "is_active",
                        className: "text-center",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            return `
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" data-id="${full.id}" ${data==1?"checked":""}>
                                </div>
                            `
                        }
                    },
                    {
                        data: "status",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            let arr = data.split(',');
                            if (arr.length > 0) {
                                let times = arr.map(item => {
                                    return `<span class="badge ${item == "Full"?"bg-danger": "bg-success"}">${item}</span>`;
                                });
                                return times.join('<br>');
                            } else {
                                return `<span class="text-danger">No Time Assigned</span>`;
                            }
                        }
                    },
                    {
                        data: 'id',
                        mRender: function(data, type, full) {
                            return `<a title="Edit" href="/observation/date/${data}/edit" class="btn btn-sm btn-primary text-white"><i class="fa fa-pencil"></i> Edit</a>`
                        }
                    }
                ],
                order: [
                    [1, "asc"]
                ]
            });

            $('#tbl-setting-date').on('click', '.form-check-input', function() {
                let data = tbldate.row($(this).parents('tr')).data();
                let _this = $(this);
                console.log(data);
                blockUI();
                ajax(null, `/observation/setting/active/${data.id}`, "POST", function(json) {
                    toastify("success", "Status is changed successfully");
                }, function(err) {
                    _this.prop('checked', data.is_active == 1 ? "checked" : "");
                    toastify("Error", err?.responseJSON?.message ?? "Please try again later",
                        "error");
                });
            })
        });
    </script>
@endsection
