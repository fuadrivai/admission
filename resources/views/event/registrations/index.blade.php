@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <style>
        .registration-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .registration-info {
            flex: 1;
        }

        .registration-info h2 {
            margin: 0 0 0.5rem;
            font-size: 1.75rem;
            color: #333;
        }

        .registration-info p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
        }

        .registration-stats {
            display: flex;
            gap: 1.5rem;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2563eb;
        }

        @media (max-width: 768px) {
            .registration-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .registration-stats {
                flex-direction: column;
                gap: 0.75rem;
            }

            .stat-card {
                flex: 1;
                width: 100%;
            }
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #333;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-group-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-group-actions form {
            margin: 0;
        }

        code {
            background-color: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: #e83e8c;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="registration-header">
                    <div class="registration-info">
                        <h2>{{ $event->title }}</h2>
                        <p>Manage and view all event registrations</p>
                    </div>
                    <div class="registration-stats">
                        <div class="stat-card">
                            <div class="stat-label">Total Registrations</div>
                            <div class="stat-value" id="total-registrations">0</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Status: SUBMITTED</div>
                            <div class="stat-value" id="status-submitted" style="color: #0dcaf0;">0</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Registrations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="registrations-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Registered</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
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
        $(document).ready(function() {
            let table = $('#registrations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('event.registrations.datatables', $event) }}",
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'registered_at',
                        name: 'registered_at'
                    },
                    {
                        data: 'submission_date',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [4, 'desc']
                ],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Processing...',
                    emptyTable: 'No registrations found',
                    info: 'Showing _START_ to _END_ of _TOTAL_ registrations',
                    infoFiltered: '(filtered from _MAX_ total registrations)',
                }
            });

            // Update stats
            table.on('draw.dt', function() {
                let data = table.ajax.params();
                $.ajax({
                    url: "{{ route('event.registrations.datatables', $event) }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#total-registrations').text(response.recordsTotal);

                        // Count submitted status
                        let submitted = 0;
                        $.ajax({
                            url: "{{ route('event.registrations.datatables', $event) }}",
                            type: 'GET',
                            data: {
                                draw: 1,
                                start: 0,
                                length: -1
                            },
                            dataType: 'json',
                            success: function(fullResponse) {
                                fullResponse.data.forEach(item => {
                                    if (item.status.includes(
                                        'info')) { // SUBMITTED status has 'info' class
                                        submitted++;
                                    }
                                });
                                $('#status-submitted').text(submitted);
                            }
                        });
                    }
                });
            });

            // Trigger initial draw to update stats
            table.draw();
        });
    </script>
@endsection
