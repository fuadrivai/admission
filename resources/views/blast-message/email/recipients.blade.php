@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="/assets/extensions/summernote/summernote-bs5.min.css">
    <style>
        .recipient-status {
            min-width: 92px;
        }

        .email-preview-frame {
            background: #f5f7fb;
            border: 0;
            min-height: 520px;
            width: 100%;
        }

        .recipient-error {
            white-space: pre-wrap;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Email Campaign Recipients</h5>
                        <div><strong>Campaign:</strong> {{ $campaign->name }}</div>
                        <div><strong>Subject:</strong> {{ $campaign->subject }}</div>
                    </div>
                    <a href="{{ route('email-campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Campaigns
                    </a>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6 col-xl-3">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Total Recipient</div>
                            <div class="fs-4 fw-bold">{{ $summary['total'] }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Sent</div>
                            <div class="fs-4 fw-bold text-success">{{ $summary['sent'] }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Failed</div>
                            <div class="fs-4 fw-bold text-danger">{{ $summary['failed'] }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="border rounded p-3">
                            <div class="text-muted small">Pending / Processing</div>
                            <div class="fs-4 fw-bold text-warning">{{ $summary['pending'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive datatable-minimal table-striped">
                    <table class="table" id="tbl-campaign-recipients">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="recipientMessageModal" tabindex="-1" aria-labelledby="recipientMessageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recipientMessageModalLabel">Email Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6"><strong>To:</strong> <span id="previewRecipient"></span></div>
                        <div class="col-md-3"><strong>Status:</strong> <span id="previewStatus"></span></div>
                        <div class="col-md-3"><strong>Sent At:</strong> <span id="previewSentAt">-</span></div>
                    </div>
                    <div id="previewError" class="alert alert-danger d-none recipient-error"></div>
                    <div id="previewMessage" class="border rounded overflow-hidden"></div>
                    <div class="mt-3">
                        <h6>Attachments</h6>
                        <div id="previewAttachments" class="list-group"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCampaignModal" tabindex="-1" aria-labelledby="editCampaignModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form id="editCampaignForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCampaignModalLabel">Edit Email Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">The selected recipient will be added to this campaign as a new pending
                        delivery. The message update applies to this campaign.</div>
                    <div class="mb-3"><label for="editSubject" class="form-label">Subject</label><input id="editSubject"
                            class="form-control" required></div>
                    <div class="mb-3"><label for="editMessage" class="form-label">Message</label>
                        <textarea id="editMessage"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Attachments</label>
                        <div id="editAttachments" class="list-group"></div>
                    </div>
                    <div><label for="editAttachmentFiles" class="form-label">Add Attachments</label><input
                            id="editAttachmentFiles" class="form-control" type="file" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <div class="form-text">Maximum 2 attachments total and 5 MB per file.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Add To
                        Campaign</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="resendModal" tabindex="-1" aria-labelledby="resendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resendModalLabel">Resend Email</h5><button type="button"
                        class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to resend this email?</p>
                    <div><strong>To:</strong> <span id="resendRecipient"></span></div>
                    <div><strong>Current status:</strong> <span id="resendStatus"></span></div>
                    <div id="resendError" class="alert alert-danger d-none mt-3 recipient-error"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button><button type="button" id="confirmResend"
                        class="btn btn-primary"><i class="bi bi-send me-1"></i> Resend Email</button></div>
            </div>
        </div>
    </div>
@endsection

@section('content-script')
    <script src="/assets/extensions/summernote/summernote-bs5.min.js"></script>
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            const campaignId = @json($campaign->id);
            const campaignSubject = @json($campaign->subject);
            let selectedRecipientId = null;
            let selectedRecipient = null;
            let campaignAttachments = [];

            $('#editMessage').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ]
            });

            const table = $('#tbl-campaign-recipients').DataTable({
                responsive: true,
                pagingType: 'simple',
                processing: true,
                serverSide: true,
                dom: `<"row"<"col-sm-6"l><"col-sm-6"f>>tip`,
                language: {
                    info: 'Page _PAGE_ of _PAGES_',
                    lengthMenu: '_MENU_ ',
                    search: '',
                    searchPlaceholder: 'Search recipients...'
                },
                ajax: {
                    url: '{{ route('email-campaigns.recipients.datatables', $campaign) }}',
                    type: 'GET'
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        defaultContent: '-'
                    },
                    {
                        data: 'email',
                        defaultContent: '-'
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data) {
                            return statusBadge(data);
                        }
                    },
                    {
                        data: 'sent_at',
                        defaultContent: '-',
                        className: 'text-center'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<div class="d-flex justify-content-center gap-1"><button title="View Message" class="btn btn-sm btn-outline-primary view-message" data-id="${data}"><i class="bi bi-eye"></i></button><button title="Edit Message" class="btn btn-sm btn-outline-secondary edit-message" data-id="${data}"><i class="bi bi-pencil"></i></button><button title="Resend Email" class="btn btn-sm btn-outline-success resend-email" data-id="${data}" data-email="${escapeHtml(row.email || '')}" data-name="${escapeHtml(row.name || '')}" data-status="${escapeHtml(row.status || '')}" data-error="${escapeHtml(row.error_message || '')}"><i class="bi bi-send"></i></button></div>`;
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

            function statusBadge(status) {
                const value = String(status || 'PENDING').toUpperCase();
                const classes = {
                    SENT: 'success',
                    FAILED: 'danger',
                    PROCESSING: 'warning text-dark',
                    PENDING: 'secondary'
                };
                return `<span class="badge bg-${classes[value] || 'secondary'} recipient-status">${escapeHtml(value)}</span>`;
            }

            function formatSize(bytes) {
                return bytes ? (Number(bytes) / 1024 / 1024).toFixed(2) + ' MB' : 'Unknown size';
            }

            function attachmentList(attachments, includeRemove) {
                return attachments.length ? attachments.map(function(file) {
                    const remove = includeRemove ?
                        `<button type="button" class="btn btn-sm btn-outline-danger remove-attachment" data-id="${file.id}"><i class="bi bi-x"></i> Remove</button>` :
                        `<a class="btn btn-sm btn-outline-primary" href="${file.url}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View</a>`;
                    return `<div class="list-group-item d-flex justify-content-between align-items-center"><span><i class="bi bi-paperclip me-2"></i>${escapeHtml(file.original_name)} <small class="text-muted">(${formatSize(file.file_size)})</small></span>${remove}</div>`;
                }).join('') : '<div class="text-muted">No attachments</div>';
            }

            function modal(id) {
                return bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
            }

            $('#tbl-campaign-recipients').on('click', '.view-message', function() {
                $.get(`/email-campaigns/${campaignId}/recipients/${$(this).data('id')}/message`, function(
                    result) {
                    $('#previewRecipient').text(
                        `${result.recipient.name || '-'} <${result.recipient.email}>`);
                    $('#previewStatus').html(statusBadge(result.recipient.status));
                    $('#previewSentAt').text(result.recipient.sent_at || '-');
                    $('#previewError').toggleClass('d-none', !result.recipient.error_message).text(
                        result.recipient.error_message || '');
                    $('#previewMessage').html(result.message ||
                        '<div class="p-3 text-muted">No message</div>');
                    $('#previewAttachments').html(attachmentList(result.attachments || [], false));
                    modal('recipientMessageModal').show();
                }).fail(function(xhr) {
                    alert(xhr.responseJSON?.message || 'Unable to load recipient message.');
                });
            });

            $('#tbl-campaign-recipients').on('click', '.edit-message', function() {
                $.get(`/email-campaigns/${campaignId}/recipients/${$(this).data('id')}/message`, function(
                    result) {
                    selectedRecipient = result.recipient;
                    campaignAttachments = result.attachments || [];
                    $('#editSubject').val(campaignSubject);
                    $('#editMessage').summernote('code', result.body || '');
                    $('#editAttachments').html(attachmentList(campaignAttachments, false));
                    $('#editAttachmentFiles').val('');
                    modal('editCampaignModal').show();
                }).fail(function(xhr) {
                    alert(xhr.responseJSON?.message || 'Unable to load recipient message.');
                });
            });

            $('#editAttachments').on('click', '.remove-attachment', function() {
                $(this).closest('.list-group-item').remove();
                $(this).data('removed', true);
                $('<input>').attr({
                    type: 'hidden',
                    name: 'remove_attachments[]',
                    value: $(this).data('id')
                }).appendTo('#editCampaignForm');
            });

            $('#editCampaignForm').on('submit', function(event) {
                event.preventDefault();
                if (!selectedRecipient) return;
                const saveButton = $(this).find('button[type="submit"]');
                saveButton.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Adding...'
                );
                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('_method', 'PUT');
                formData.append('subject', $('#editSubject').val());
                formData.append('message', $('#editMessage').summernote('code'));
                formData.append('recipients[0][email]', selectedRecipient.email);
                if (selectedRecipient.name) {
                    formData.append('recipients[0][name]', selectedRecipient.name);
                }
                Object.entries(selectedRecipient.data || {}).forEach(function(entry) {
                    formData.append(`recipients[0][data][${entry[0]}]`, String(entry[1] ?? ''));
                });
                Array.from($('#editAttachmentFiles')[0].files).forEach(file => formData.append(
                    'attachments[]', file));
                $.ajax({
                    url: `/email-campaigns/${campaignId}/message`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        saveButton.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i> Add To Campaign');
                        modal('editCampaignModal').hide();
                        alert(response.message || 'Recipient added to campaign successfully.');
                        window.location.reload();
                    },
                    error: function(xhr) {
                        saveButton.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i> Add To Campaign');
                        alert(xhr.responseJSON?.message ||
                            'Unable to update campaign message.');
                    }
                });
            });

            $('#tbl-campaign-recipients').on('click', '.resend-email', function() {
                const button = $(this);
                selectedRecipientId = button.data('id');
                $('#resendRecipient').text(`${button.data('name') || '-'} <${button.data('email')}>`);
                $('#resendStatus').html(statusBadge(button.data('status')));
                $('#resendError').toggleClass('d-none', !button.data('error')).text(button.data('error') ||
                    '');
                modal('resendModal').show();
            });

            $('#confirmResend').on('click', function() {
                if (!selectedRecipientId) return;
                const resendButton = $(this);
                resendButton.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending...'
                );
                $.ajax({
                    url: `/email-campaigns/${campaignId}/recipients/${selectedRecipientId}/resend`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        resendButton.prop('disabled', false).html(
                            '<i class="bi bi-send me-1"></i> Resend Email');
                        modal('resendModal').hide();
                        alert(response.message);
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        resendButton.prop('disabled', false).html(
                            '<i class="bi bi-send me-1"></i> Resend Email');
                        alert(xhr.responseJSON?.message || 'Unable to queue resend.');
                    }
                });
            });
        });
    </script>
@endsection
