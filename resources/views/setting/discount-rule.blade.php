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
                    <table class="table" id="tbl-rule">
                        <thead>
                            <tr class="text-center">
                                <th>Name</th>
                                <th>Registration Place</th>
                                <th>Branch</th>
                                <th>Applies To</th>
                                <th>Discount</th>
                                <th>Requires DP</th>
                                <th>Date</th>
                                <th>Quota (used)</th>
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

    <div class="modal fade text-left" id="rule-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="form-rule" autocomplete="off">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white">Discount Rule Form</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="rule-id">
                        <div class="mb-3">
                            <label class="form-label required-label" for="rule-name">Name</label>
                            <input type="text" id="rule-name" class="form-control" required>
                            <div class="text-danger small" data-error="name"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required-label" for="rule-place">Registration Place</label>
                            <select id="rule-place" class="form-select" required>
                                @foreach ($places as $place)
                                    <option value="{{ $place->code }}">{{ $place->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger small" data-error="registration_place_code"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="rule-branch">Branch</label>
                            <select id="rule-branch" class="form-select">
                                <option value="">All branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger small" data-error="branch_id"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-label" for="rule-type">Applies to item type</label>
                                <input type="text" id="rule-type" class="form-control" value="enrolment" required>
                                <div class="form-text">"enrolment" = Registration Fee.</div>
                                <div class="text-danger small" data-error="applies_to_type"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-label" for="rule-percentage">Discount (%)</label>
                                <input type="number" id="rule-percentage" class="form-control" min="0.01" max="100" step="0.01" value="100" required>
                                <div class="text-danger small" data-error="percentage"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="rule-quota">Quota (first N payers)</label>
                                <input type="number" id="rule-quota" class="form-control" min="1" placeholder="Unlimited">
                                <div class="text-danger small" data-error="quota"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="rule-date">Valid only on date</label>
                                <input type="date" id="rule-date" class="form-control">
                                <div class="form-text">Empty = always valid.</div>
                                <div class="text-danger small" data-error="valid_date"></div>
                            </div>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="rule-dp" checked>
                            <label class="form-check-label" for="rule-dp">Only when the payer also pays DP</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rule-active" checked>
                            <label class="form-check-label" for="rule-active">Active</label>
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
            const baseUrl = '/setting/discount-rule';
            const csrf = '{{ csrf_token() }}';
            const modal = new bootstrap.Modal(document.getElementById('rule-modal'));
            const placeNames = @json($places->pluck('name', 'code'));
            const branchNames = @json($branches->pluck('name', 'id'));

            const table = $('#tbl-rule').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add discount rule <i class="fa fa-plus-circle"></i>',
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        resetForm();
                        modal.show();
                    }
                }],
                language: { info: "Page _PAGE_ of _PAGES_", lengthMenu: "_MENU_ ", search: "", searchPlaceholder: "Search.." },
                processing: true,
                serverSide: true,
                ajax: { url: "{{ route('setting.discount-rule.datatables') }}", type: "GET" },
                columns: [
                    { data: 'name' },
                    { data: 'registration_place_code', render: d => placeNames[d] ?? d },
                    { data: 'branch_id', defaultContent: 'All', render: d => d ? (branchNames[d] ?? d) : 'All branches' },
                    { data: 'applies_to_type' },
                    { data: 'percentage', className: 'text-center', render: d => d + '%' },
                    { data: 'requires_dp', className: 'text-center', render: d => d ? 'Yes' : 'No' },
                    { data: 'valid_date', className: 'text-center', defaultContent: '-', render: d => d ? d.substring(0, 10) : 'Always' },
                    { data: 'quota', className: 'text-center', render: (d, t, r) => d ? `${r.used} / ${d}` : `${r.used} / unlimited` },
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
                $('#form-rule')[0].reset();
                $('#rule-id').val('');
                $('#rule-type').val('enrolment');
                $('#rule-percentage').val(100);
                $('#rule-dp').prop('checked', true);
                $('#rule-active').prop('checked', true);
                $('[data-error]').text('');
            }

            $('#tbl-rule').on('click', '.btn-edit', function() {
                const d = table.row($(this).parents('tr')).data();
                resetForm();
                $('#rule-id').val(d.id);
                $('#rule-name').val(d.name);
                $('#rule-place').val(d.registration_place_code);
                $('#rule-branch').val(d.branch_id || '');
                $('#rule-type').val(d.applies_to_type);
                $('#rule-percentage').val(d.percentage);
                $('#rule-quota').val(d.quota);
                $('#rule-date').val(d.valid_date ? d.valid_date.substring(0, 10) : '');
                $('#rule-dp').prop('checked', !!d.requires_dp);
                $('#rule-active').prop('checked', !!d.is_active);
                modal.show();
            });

            $('#tbl-rule').on('click', '.btn-delete', function() {
                const d = table.row($(this).parents('tr')).data();
                if (!confirm(`Delete "${d.name}"?`)) return;
                $.ajax({
                    url: `${baseUrl}/${d.id}`,
                    type: 'POST',
                    data: { _method: 'DELETE', _token: csrf },
                    success: () => table.ajax.reload(null, false),
                    error: () => alert('Failed to delete discount rule.')
                });
            });

            $('#form-rule').on('submit', function(e) {
                e.preventDefault();
                $('[data-error]').text('');
                const id = $('#rule-id').val();
                $.ajax({
                    url: id ? `${baseUrl}/${id}` : baseUrl,
                    type: 'POST',
                    data: {
                        _token: csrf,
                        _method: id ? 'PUT' : 'POST',
                        name: $('#rule-name').val(),
                        registration_place_code: $('#rule-place').val(),
                        branch_id: $('#rule-branch').val(),
                        applies_to_type: $('#rule-type').val(),
                        percentage: $('#rule-percentage').val(),
                        quota: $('#rule-quota').val(),
                        valid_date: $('#rule-date').val(),
                        requires_dp: $('#rule-dp').is(':checked') ? 1 : 0,
                        is_active: $('#rule-active').is(':checked') ? 1 : 0
                    },
                    success: () => { modal.hide(); table.ajax.reload(null, false); },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors ?? {};
                        Object.keys(errors).forEach(k => $(`[data-error="${k}"]`).text(errors[k][0]));
                        if (!xhr.responseJSON?.errors) alert('Failed to save discount rule.');
                    }
                });
            });
        });
    </script>
@endsection