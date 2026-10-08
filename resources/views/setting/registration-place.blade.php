@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <style>
        .dt-button { margin-left: 0.5rem; margin-bottom: 0.5rem }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive datatable-minimal table-striped">
                    <table class="table" id="tbl-place">
                        <thead>
                            <tr class="text-center">
                                <th>Name</th>
                                <th>Code</th>
                                <th>Free Text</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="place-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="form-place" autocomplete="off">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white">Registration Place Form</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="place-id">
                        <div class="mb-3">
                            <label for="place-name" class="form-label required-label">Name</label>
                            <input type="text" id="place-name" class="form-control" required>
                            <div class="text-danger small" data-error="name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="place-code" class="form-label required-label">Code</label>
                            <input type="text" id="place-code" class="form-control" required>
                            <div class="text-danger small" data-error="code"></div>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="place-other">
                            <label class="form-check-label" for="place-other">Require free-text detail (like "Other")</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="place-active" checked>
                            <label class="form-check-label" for="place-active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ms-1">Save</button>
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
            const baseUrl = '/setting/registration-place';
            const csrf = '{{ csrf_token() }}';
            const modal = new bootstrap.Modal(document.getElementById('place-modal'));

            const table = $('#tbl-place').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add registration place <i class="fa fa-plus-circle"></i>',
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        resetForm();
                        modal.show();
                    }
                }],
                language: { info: "Page _PAGE_ of _PAGES_", lengthMenu: "_MENU_ ", search: "", searchPlaceholder: "Search.." },
                processing: true,
                serverSide: true,
                ajax: { url: "{{ route('setting.registration-place.datatables') }}", type: "GET" },
                columns: [
                    { data: 'name' },
                    { data: 'code' },
                    { data: 'is_other', className: 'text-center', render: d => d ? 'Yes' : 'No' },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        render: d => d ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: () => `<a class="btn btn-sm btn-primary text-white btn-edit"><i class="fa fa-pencil"></i> Edit</a>
                            <a class="btn btn-sm btn-danger text-white btn-delete"><i class="fa fa-trash"></i> Delete</a>`
                    }
                ],
                order: [[0, 'asc']]
            });

            function resetForm() {
                $('#form-place')[0].reset();
                $('#place-id').val('');
                $('#place-active').prop('checked', true);
                $('[data-error]').text('');
            }

            $('#tbl-place').on('click', '.btn-edit', function() {
                const d = table.row($(this).parents('tr')).data();
                resetForm();
                $('#place-id').val(d.id);
                $('#place-name').val(d.name);
                $('#place-code').val(d.code);
                $('#place-other').prop('checked', !!d.is_other);
                $('#place-active').prop('checked', !!d.is_active);
                modal.show();
            });

            $('#tbl-place').on('click', '.btn-delete', function() {
                const d = table.row($(this).parents('tr')).data();
                if (!confirm(`Delete "${d.name}"?`)) return;
                $.ajax({
                    url: `${baseUrl}/${d.id}`,
                    type: 'POST',
                    data: { _method: 'DELETE', _token: csrf },
                    success: () => table.ajax.reload(null, false),
                    error: () => alert('Failed to delete registration place.')
                });
            });

            $('#form-place').on('submit', function(e) {
                e.preventDefault();
                $('[data-error]').text('');
                const id = $('#place-id').val();
                $.ajax({
                    url: id ? `${baseUrl}/${id}` : baseUrl,
                    type: 'POST',
                    data: {
                        _token: csrf,
                        _method: id ? 'PUT' : 'POST',
                        name: $('#place-name').val(),
                        code: $('#place-code').val(),
                        is_other: $('#place-other').is(':checked') ? 1 : 0,
                        is_active: $('#place-active').is(':checked') ? 1 : 0
                    },
                    success: () => { modal.hide(); table.ajax.reload(null, false); },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors ?? {};
                        Object.keys(errors).forEach(k => $(`[data-error="${k}"]`).text(errors[k][0]));
                        if (!xhr.responseJSON?.errors) alert('Failed to save registration place.');
                    }
                });
            });
        });
    </script>
@endsection
