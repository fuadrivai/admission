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
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-division">
                            <thead>
                                <tr class="text-center">
                                    <th>Division</th>
                                    <th>Levels</th>
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
                <form id="form-division" action="/division" method="POST" autocomplete="off" class="needs-validation"
                    novalidate>
                    @csrf
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white" id="myModalLabel160">Division Form
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <input type="text" class="d-none" name="id" id="id">
                                <label for="division" class="form-label required-label">Division</label>
                                <input type="text" name="name" class="form-control" required id="division">
                                <div class="invalid-feedback">
                                    Insert a valid division
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
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script>
        $(document).ready(function() {
            tblDivision = $('#tbl-division').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add division <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-level'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        // window.location.href = "/level/create";
                        $('#primary').modal('show')
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
                    url: "{{ route('division.datatables') }}",
                    type: "GET",
                },
                columns: [{
                        data: "name",
                        defaultContent: "--",
                    },
                    {
                        data: 'levels',
                        defaultContent: "-",
                        className: "text-center",
                        mRender: function(data, type, full) {
                            return `<a title="levels"  >${ data?.length ?? 0}</a>`
                        }
                    },
                    {
                        data: 'id',
                        mRender: function(data, type, full) {
                            return `<a title="Edit" class="btn btn-sm btn-primary text-white btn-division"><i class="fa fa-pencil"></i> Edit</a>`
                        }
                    }
                ],
                order: [
                    [0, "asc"]
                ]
            });

            $('#tbl-division').on('click', '.btn-division', function() {
                let data = tblDivision.row($(this).parents('tr')).data();
                $('#id').val(data.id);
                $('#division').val(data.name);
                $('#primary').modal('show');
            })
        });
    </script>
@endsection
