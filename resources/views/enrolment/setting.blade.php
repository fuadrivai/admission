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
                    Enrolment Price
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-setting-price">
                            <thead>
                                <tr class="text-center">
                                    <th>Branch</th>
                                    <th>Level</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Type</th>
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
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script>
        $(document).ready(function() {
            getPrices()
            tblPrice = $('#tbl-setting-price').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add enrolment price <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-assign'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        window.location.href = "/price/create";
                    }
                }],
                language: {
                    info: "Page _PAGE_ of _PAGES_",
                    lengthMenu: "_MENU_ ",
                    search: "",
                    searchPlaceholder: "Search.."
                },
                columns: [{
                        data: "branch",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            return data.name;
                        }
                    },
                    {
                        data: 'level',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            return data.name;
                        }
                    },
                    {
                        data: 'name',
                        defaultContent: "-",
                    },
                    {
                        data: "price",
                        defaultContent: "--",
                        className: "text-end",
                        mRender: function(data, type, full) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: "type",
                        defaultContent: "--",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            return data == "form" ? `<span class="badge bg-info">Form</span>` :
                                `<span class="badge bg-primary">Enrolment</span>`;
                        }
                    },
                    {
                        data: "is_active",
                        className: "text-center",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            return data == 1 ? `<span class="badge bg-success">Active</span>` :
                                `<span class="badge bg-danger">Inactive</span>`;
                        }
                    },
                    {
                        data: 'id',
                        mRender: function(data, type, full) {
                            return `<a title="Edit" href="/price/${data}/edit" class="btn btn-sm btn-primary text-white"><i class="fa fa-pencil"></i> Edit</a>`
                        }
                    }
                ],
                order: [
                    [0, "asc"]
                ]
            });
        });

        function getPrices() {
            blockUI();
            ajax(null, "/price", "GET", function(json) {
                reloadJsonDataTable(tblPrice, json)
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later",
                    "error");
            });
        }
    </script>
@endsection
