@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-email-campaign">
                            <thead>
                                <tr class="text-center">
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Recipients</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="emailListModal" tabindex="-1" aria-labelledby="emailListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailListModalLabel">Registered Emails</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 id="emailListCampaignName" class="mb-3"></h6>
                    <div id="emailListContainer" class="list-group"></div>
                </div>
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
            $('#tbl-email-campaign').DataTable({
                responsive: true,
                pagingType: 'simple',
                processing: true,
                serverSide: true,
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'New Campaign <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-email-campaign'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        window.location.href = '{{ route('email-campaigns.create') }}';
                    }
                }],
                language: {
                    info: 'Page _PAGE_ of _PAGES_',
                    lengthMenu: '_MENU_ ',
                    search: '',
                    searchPlaceholder: 'Search..'
                },
                ajax: {
                    url: '{{ route('email-campaigns.datatables') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'name',
                        defaultContent: '--'
                    },
                    {
                        data: 'subject',
                        defaultContent: '--'
                    },
                    {
                        data: 'total_recipient',
                        defaultContent: '0',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        defaultContent: '--',
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        defaultContent: '--'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<div class="d-flex justify-content-center gap-1 flex-wrap">
                                <a title="Edit" href="/email-campaigns/${data}/edit" class="btn btn-sm btn-primary text-white"><i class="fa fa-pencil"></i></a>
                                <a title="Recipients" href="/email-campaigns/${data}/recipients" class="btn btn-sm btn-outline-primary"><i class="bi bi-people"></i></a>
                                <button title="View registered emails" class="btn btn-sm btn-info text-white view-emails" data-id="${data}" data-name="${escapeHtml(row.name || '')}" data-emails='${JSON.stringify(row.emails || [])}'><i class="fa fa-envelope"></i></button>
                                <button title="Delete" class="btn btn-sm btn-danger delete-campaign" data-id="${data}" data-name="${escapeHtml(row.name || '')}"><i class="fa fa-trash"></i></button>
                            </div>`;
                        }
                    }
                ],
                order: [
                    [4, 'desc']
                ]
            });

            function escapeHtml(value) {
                return String(value).replace(/[&<>'"]/g, function(character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        "'": '&#39;',
                        '"': '&quot;'
                    } [character];
                });
            }

            $('#tbl-email-campaign').on('click', '.view-emails', function() {
                const emails = JSON.parse($(this).attr('data-emails') || '[]');
                $('#emailListCampaignName').text($(this).attr('data-name') || 'Campaign');
                $('#emailListContainer').html(emails.length ?
                    emails.map(email => `<div class="list-group-item">${escapeHtml(email)}</div>`).join(
                        '') :
                    '<div class="text-muted">No registered email found.</div>');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('emailListModal')).show();
            });

            $('#tbl-email-campaign').on('click', '.delete-campaign', function() {
                const button = $(this);
                if (!confirm(
                        `Delete ${button.attr('data-name') || 'this campaign'}? This action cannot be undone.`
                    )) {
                    return;
                }

                $.ajax({
                    url: `/email-campaigns/${button.attr('data-id')}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#tbl-email-campaign').DataTable().ajax.reload(null, false);
                        } else {
                            alert(response.message || 'Unable to delete campaign.');
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Unable to delete campaign.');
                    }
                });
            });
        });
    </script>
@endsection
