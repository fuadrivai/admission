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
                                    <th>Times</th>
                                    <th>Quota</th>
                                    <th>Available</th>
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
                        className: "text-center",
                        mRender: function(data, type, full) {
                            return moment(data).format('DD MMMM YYYY');
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
                                    return `<span class="badge bg-success">${moment(item, 'HH:mm:ss').format('HH:mm')}</span>`;
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
                                    return `<span class="badge bg-success">${item}</span>`;
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
                                    return `<span class="badge bg-success">${item}</span>`;
                                });
                                return times.join('<br>');
                            } else {
                                return `<span class="text-danger"></span>`;
                            }
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

        });
    </script>
@endsection
