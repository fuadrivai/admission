@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <style>
        .dt-button {
            margin-left: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-academic-year">
                            <thead>
                                <tr class="text-center">
                                    <th>Academic Year</th>
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

    <!-- Modal for Academic Year Form -->
    <div class="modal fade" id="academicYearModal" tabindex="-1" aria-labelledby="academicYearModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="POST" id="academicYearForm" autocomplete="off">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="academicYearModalLabel">Academic Year Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="academicYearId" name="id">
                        <div class="mb-3">
                            <label for="academicYearName" class="form-label required-label">Academic Year</label>
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="academicYearName" name="name" required
                                        placeholder="yyyy/yyyy  e.g. 2026/2027">
                                    <div class="form-control-icon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="isActive" class="form-label">Active</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked id="isActive" name="is_active">
                                <label class="form-check-label" for="isActive">Active status</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i>
                            Close</button>
                        <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/assets/extensions/jquery-mask/dist/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#academicYearName').mask('0000/0000');
            tblAY = $('#tbl-academic-year').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add Academic Year <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-academic-year'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        $('#academicYearModal').modal('show');
                    }
                }],
                language: {
                    info: 'Page _PAGE_ of _PAGES_',
                    lengthMenu: '_MENU_ ',
                    search: '',
                    searchPlaceholder: 'Search..'
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/setting/year/datatables",
                    type: 'GET',
                },
                columns: [{
                        data: 'name',
                        defaultContent: '--',
                    },
                    {
                        data: 'is_active',
                        defaultContent: '--',
                        className: 'text-center',
                        mRender: function(data, type, full) {
                            return `<div class="d-flex justify-content-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input check-input" type="checkbox" ${data==1 ? 'checked' : ''} id="isActive-${full.id}">
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return `<button class="btn btn-sm btn-warning edit-btn" data-id="${data}"><i class="fa fa-eye"></i></button>`;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            $('#tbl-academic-year').on('click', '.edit-btn', function() {
                let data = tblAY.row($(this).parents('tr')).data();
                $('#academicYearId').val(data.id);
                $('#academicYearName').val(data.name);
                $('#isActive').prop('checked', data.is_active);
                $('#academicYearModal').modal('show');
            });

            $('#academicYearForm').on('submit', function(e) {
                e.preventDefault();
                saveAcademicYear();
            });

            $('#tbl-academic-year').on('change', '.check-input', function() {
                let data = tblAY.row($(this).parents('tr')).data();
                const payload = {
                    id: data.id,
                    name: data.name,
                    is_active: $(this).is(':checked') ? 1 : 0,
                };
                blockUI();
                ajaxAY(payload, `/setting/year/${data.id}`, 'PUT');
            })
        });

        function saveAcademicYear() {
            const id = $('#academicYearId').val();
            const payload = {
                id: id,
                name: $('#academicYearName').val(),
                is_active: $('#isActive').is(':checked') ? 1 : 0,
            };

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/setting/year/${id}` : '/setting/year';
            blockUI();
            ajaxAY(payload, url, method);
        }

        function ajaxAY(payload, url, method) {
            ajax(payload, url, method, function() {
                toastify('success', 'Academic year saved successfully', 'Success');
                $('#academicYearModal').modal('hide');
                tblAY.ajax.reload();
            }, function(err) {
                toastify('error', err?.responseJSON?.message || 'Please try again later', 'Error');
            });
        }
    </script>
@endsection
