@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
@endsection

@section('content-child')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">{{ $event->title }}</h3>
                    <a href="{{ route('event.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Registrations</h5>
                        <a href="{{ route('event.registrations.export', $event) }}" class="btn btn-success">
                            <i class="fa fa-download"></i> Export Excel
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($levelField)
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="level-filter" class="form-label">Level</label>
                                    <select id="level-filter" class="form-select">
                                        <option value="">All Levels</option>
                                        @foreach ($levelOptions as $level)
                                            <option value="{{ $level }}">{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover" id="registrations-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        @foreach ($fields as $field)
                                            <th>{{ $field->label ?: $field->field_key }}</th>
                                        @endforeach
                                        <th>Amount</th>
                                        <th>Registered</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function() {
            const columns = [{
                    data: 'code',
                    name: 'code'
                },
                @foreach ($fields as $field)
                    {
                        data: 'field_{{ $field->id }}',
                        name: 'field_{{ $field->id }}',
                        orderable: false,
                        searchable: false
                    },
                @endforeach {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'registered_at',
                    name: 'registered_at'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ];

            $('#registrations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('event.registrations.datatables', $event) }}",
                    data: function(request) {
                        @if ($levelField)
                            request.filters = request.filters || {};
                            request.filters['{{ $levelField->field_key }}'] = $('#level-filter').val();
                        @endif
                    }
                },
                columns: columns,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Processing...',
                    emptyTable: 'No registrations found'
                }
            });

            @if ($levelField)
                $('#level-filter').on('change', function() {
                    $('#registrations-table').DataTable().ajax.reload();
                });
            @endif
        });
    </script>
@endsection
