@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="/assets/extensions/summernote/summernote-bs5.min.css">
    <style>
        .campaign-page {
            --campaign-ink: #17233c;
            --campaign-muted: #718096;
            --campaign-line: #e7ebf2;
            --campaign-accent: #435ebe;
            color: var(--campaign-ink);
        }

        .campaign-page .card {
            border: 1px solid var(--campaign-line);
            border-radius: .75rem;
            box-shadow: 0 4px 18px rgba(35, 50, 80, .045);
        }

        .campaign-page .card-header {
            border-bottom: 1px solid var(--campaign-line);
            background: #fff;
            padding: 1.1rem 1.25rem;
        }

        .campaign-page .card-body {
            padding: 1.25rem;
        }

        .campaign-page .form-label {
            font-size: .85rem;
            font-weight: 600;
        }

        .campaign-page .text-muted {
            color: var(--campaign-muted) !important;
        }

        .campaign-page .upload-zone {
            border: 1.5px dashed #bdc8dc;
            border-radius: .65rem;
            background: #f8faff;
            padding: 2rem 1rem;
            text-align: center;
            transition: .2s ease;
        }

        .campaign-page .upload-zone.is-dragging,
        .campaign-page .upload-zone:hover {
            border-color: var(--campaign-accent);
            background: #f2f5ff;
        }

        .campaign-page .upload-icon {
            align-items: center;
            background: #e8edff;
            border-radius: 50%;
            color: var(--campaign-accent);
            display: inline-flex;
            font-size: 1.6rem;
            height: 3.2rem;
            justify-content: center;
            margin-bottom: .75rem;
            width: 3.2rem;
        }

        .campaign-page .metric-card {
            background: #fafbfe;
            border: 1px solid var(--campaign-line);
            border-radius: .65rem;
            padding: .9rem 1rem;
        }

        .campaign-page .metric-value {
            font-size: 1.35rem;
            font-weight: 700;
        }

        .campaign-page .variable-button {
            background: #f2f5ff;
            border: 1px solid #dce3ff;
            border-radius: .4rem;
            color: #354a9f;
            font-family: monospace;
            font-size: .8rem;
            padding: .35rem .55rem;
        }

        .campaign-page .variable-button:hover {
            background: #e5ebff;
        }

        .campaign-page .attachment-row {
            border-bottom: 1px solid var(--campaign-line);
            padding: .7rem 0;
        }

        .campaign-page .attachment-row:last-child {
            border-bottom: 0;
        }

        .campaign-page .sticky-actions {
            background: rgba(255, 255, 255, .96);
            border-top: 1px solid var(--campaign-line);
            bottom: 0;
            padding: 1rem 0;
            position: sticky;
            z-index: 10;
        }

        .campaign-page .preview-paper {
            background: #f5f7fb;
            border: 1px solid var(--campaign-line);
            border-radius: .65rem;
        }

        .campaign-page .preview-paper .preview-body {
            background: #fff;
            border-radius: .45rem;
            margin: 1rem;
            padding: 1.25rem;
        }

        .campaign-page .status-icon {
            font-size: 1.05rem;
        }

        .campaign-page .table th {
            color: var(--campaign-muted);
            font-size: .75rem;
            text-transform: uppercase;
        }

        .campaign-page .table td {
            vertical-align: middle;
        }

        .campaign-page .note-editor.note-frame {
            border-color: var(--campaign-line);
        }
    </style>
@endsection

@section('content-child')
    <div class="campaign-page container-fluid py-2">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Campaign Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><label for="campaignName" class="form-label">Campaign Name <span
                                        class="text-danger">*</span></label><input id="campaignName" class="form-control"
                                    placeholder="September Billing Notification"></div>
                            <div class="col-md-6"><label for="emailSubject" class="form-label">Email Subject <span
                                        class="text-danger">*</span></label><input id="emailSubject" class="form-control"
                                    placeholder="September Billing Information"></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>Import Recipients
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="recipientDropzone" class="upload-zone">
                            <div class="upload-icon"><i class="bi bi-upload"></i></div>
                            <h6>Import recipients from Excel or CSV</h6>
                            <p class="text-muted small mb-3">Upload .xlsx, .xls or .csv file</p>
                            <button type="button" id="chooseRecipients" class="btn btn-primary"><i
                                    class="bi bi-folder2-open me-1"></i> Choose File</button>
                            <button type="button" id="clearRecipients" class="btn btn-outline-danger d-none ms-1"><i
                                    class="bi bi-arrow-repeat me-1"></i> Clear &amp; Re-import</button>
                            <input id="recipientFile" type="file" accept=".xlsx,.xls,.csv" hidden>
                            <div class="text-muted small mt-3">Maximum file size: 5 MB</div>
                        </div>
                        <div id="importResult" class="d-none mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Import Result</h6><span class="badge bg-success-subtle text-success"><i
                                        class="bi bi-check-circle me-1"></i> File imported successfully.</span>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="metric-card">
                                        <div class="text-muted small">Total Rows</div>
                                        <div id="totalRows" class="metric-value">0</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="metric-card">
                                        <div class="text-muted small">Valid Email</div>
                                        <div id="validEmails" class="metric-value text-success">0</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="metric-card">
                                        <div class="text-muted small">Invalid</div>
                                        <div id="invalidEmails" class="metric-value text-danger">0</div>
                                    </div>
                                </div>
                            </div>
                            <div class="border rounded p-3">
                                <div class="fw-semibold small mb-2">Detected Columns</div>
                                <div id="detectedColumns" class="d-flex flex-wrap gap-2"></div>
                                <div id="emailColumnWarning" class="alert alert-warning d-none mt-3 mb-0 py-2 small"><i
                                        class="bi bi-exclamation-triangle me-1"></i> Email column not found. The imported
                                    file must contain an "email" column.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="recipientSection" class="card mb-4 d-none">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Recipients</h5><span
                            id="validRecipientBadge" class="badge bg-light text-dark">0 valid recipients</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="recipientTable">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="card-body py-3"><small id="recipientSummary" class="text-muted">No recipients
                            imported.</small></div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-envelope me-2 text-primary"></i>Email Content</h5>
                    </div>
                    <div class="card-body">
                        <textarea id="message" class="form-control"></textarea>
                        <div class="mt-4">
                            <h6 class="mb-1">Available Variables</h6>
                            <p class="text-muted small">Click a variable to insert it into the email message.</p>
                            <div class="small fw-semibold text-muted mb-2">Imported Variables</div>
                            <div id="importedVariables" class="d-flex flex-wrap gap-2 mb-3"></div>
                            <div class="small fw-semibold text-muted mb-2">System Variables</div>
                            <div id="systemVariables" class="d-flex flex-wrap gap-2"></div>
                        </div>
                        <div id="variableValidation" class="alert alert-success py-2 small mt-3 mb-0"><i
                                class="bi bi-check-circle me-1"></i> All variables are valid</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-paperclip me-2 text-primary"></i>Attachments</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Maximum 2 attachments · Maximum 5 MB per file</p>
                        <div id="attachmentDropzone" class="upload-zone py-4">
                            <div class="upload-icon"><i class="bi bi-paperclip"></i></div>
                            <h6>Add attachments</h6>
                            <p class="text-muted small mb-3">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</p><button type="button"
                                id="chooseAttachments" class="btn btn-outline-primary btn-sm"><i
                                    class="bi bi-folder2-open me-1"></i> Choose Files</button><input id="attachmentFiles"
                                type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple hidden>
                        </div>
                        <div id="attachmentAlert" class="alert alert-danger d-none py-2 small mt-3 mb-0"></div>
                        <div id="attachmentList" class="mt-3"></div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-eye me-2 text-primary"></i>Email Preview</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Review the rendered message using a recipient from the imported list.
                        </p><button type="button" id="previewButton" class="btn btn-primary w-100"
                            data-bs-toggle="modal" data-bs-target="#previewModal"><i class="bi bi-eye me-1"></i> Preview
                            Email</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky-actions">
            <div class="d-flex justify-content-between align-items-center"><button type="button"
                    class="btn btn-outline-secondary">Cancel</button><button type="button" id="saveDraft"
                    class="btn btn-primary"><i class="bi bi-save me-1"></i> Save as Draft</button></div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel"><i
                            class="bi bi-envelope-open me-2 text-primary"></i>Email Preview</h5><button type="button"
                        class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"><label for="previewRecipient" class="form-label">Preview Recipient</label><select
                        id="previewRecipient" class="form-select mb-3"></select>
                    <div class="preview-paper">
                        <div class="preview-body">
                            <div class="small text-muted mb-1"><strong class="text-dark">From:</strong> MHIS</div>
                            <div class="small text-muted mb-1"><strong class="text-dark">To:</strong> <span
                                    id="previewTo"></span></div>
                            <div class="small text-muted"><strong class="text-dark">Subject:</strong> <span
                                    id="previewSubject"></span></div>
                            <hr>
                            <div id="previewMessage"></div>
                            <div id="previewAttachments" class="border-top mt-4 pt-3"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Close</button><button type="button" id="refreshPreview"
                        class="btn btn-primary"><i class="bi bi-arrow-clockwise me-1"></i> Refresh Preview</button></div>
            </div>
        </div>
    </div>
@endsection

@section('content-script')
    <script src="/assets/extensions/summernote/summernote-bs5.min.js"></script>
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        $(function() {
            const variable = (name) => '{' + '{' + name + '}' + '}';
            const systemVariables = ['current_date', 'current_year'];
            let availableVariables = [];
            let recipientColumns = [];
            let recipients = [];
            let recipientTable = null;
            let attachments = [],
                lastRange = null;

            $('#message').summernote({
                height: 320,
                placeholder: 'Write your email message here...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                callbacks: {
                    onBlur: function() {
                        lastRange = $(this).summernote('createRange');
                    }
                }
            });
            $('#message').summernote('code', '<p>Dear parent,</p><p>Write your message here.</p><p>Thank you.</p>');

            const renderButtons = (selector, names) => {
                $(selector).empty();
                names.forEach((name) => $('<button>', {
                    type: 'button',
                    class: 'variable-button',
                    text: variable(name),
                    title: 'Insert ' + variable(name)
                }).data('variable', variable(name)).appendTo(selector));
            };
            renderButtons('#importedVariables', availableVariables);
            renderButtons('#systemVariables', systemVariables);
            $(document).on('mousedown', '.variable-button', function(event) {
                event.preventDefault();
            }).on('click', '.variable-button', function() {
                const editor = $('#message');
                editor.summernote('focus');
                if (lastRange) editor.summernote('restoreRange', lastRange);
                editor.summernote('insertText', $(this).data('variable'));
                lastRange = editor.summernote('createRange');
                validateVariables();
            });

            $('#message').on('summernote.change', validateVariables);

            function renderTemplate(template, recipient) {
                const values = Object.assign({}, recipient, {
                    current_date: new Date().toLocaleDateString('en-GB'),
                    current_year: new Date().getFullYear()
                });
                return template.replace(/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/g, (match, key) => values[key] ===
                    undefined ? match : values[key]);
            }

            function validateVariables() {
                const html = $('#message').summernote('code');
                const found = [...html.matchAll(/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/g)].map((match) => match[1]);
                const unknown = [...new Set(found.filter((name) => !availableVariables.includes(name) && !
                    systemVariables.includes(name)))];
                $('#variableValidation').toggleClass('alert-success', !unknown.length).toggleClass('alert-warning',
                    unknown.length).html(unknown.length ?
                    '<i class="bi bi-exclamation-triangle me-1"></i> Unknown variables detected: ' + unknown
                    .map(variable).join(', ') :
                    '<i class="bi bi-check-circle me-1"></i> All variables are valid');
            }

            function normalizeHeader(header, index) {
                const normalized = String(header || '').trim().toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(
                    /^_|_$/g, '');
                return normalized || 'column_' + (index + 1);
            }

            function parseCsv(text) {
                const rows = [],
                    row = [];
                let value = '',
                    quoted = false;
                for (let index = 0; index < text.length; index++) {
                    const character = text[index];
                    if (character === '"' && text[index + 1] === '"' && quoted) {
                        value += '"';
                        index++;
                    } else if (character === '"') quoted = !quoted;
                    else if (character === ',' && !quoted) {
                        row.push(value);
                        value = '';
                    } else if ((character === '\n' || character === '\r') && !quoted) {
                        if (character === '\r' && text[index + 1] === '\n') index++;
                        row.push(value);
                        rows.push(row.splice(0));
                        value = '';
                    } else value += character;
                }
                if (value.length || row.length) {
                    row.push(value);
                    rows.push(row);
                }
                return rows;
            }

            function findColumn(names) {
                return recipientColumns.find((column) => names.includes(column));
            }

            function setDefaultMessage() {
                const name = findColumn(['nama', 'name']) || availableVariables[0];
                const amount = findColumn(['tagihan', 'amount', 'billing']) || availableVariables[1];
                const grade = findColumn(['grade', 'kelas']) || availableVariables[2];
                const level = findColumn(['level', 'jenjang']) || availableVariables[3];
                $('#message').summernote('code', '<p>Dear parent of ' + variable(name) +
                    ',</p><p>We would like to inform you that your current billing amount is <strong>' +
                    variable(amount) +
                    '</strong>.</p><p>Grade: ' + variable(grade) + '<br>Level: ' + variable(level) +
                    '</p><p>Thank you.</p>');
            }

            function renderRecipients() {
                if (recipientTable) recipientTable.destroy();
                $('#recipientTable thead').html('<tr>' + recipientColumns.map((column) => '<th>' + column + '</th>')
                    .join('') + '<th>Status</th></tr>');
                recipientTable = $('#recipientTable').DataTable({
                    data: recipients,
                    columns: recipientColumns.map((column) => ({
                        data: column,
                        defaultContent: ''
                    })).concat({
                        data: null,
                        orderable: false,
                        render: () =>
                            '<span class="badge bg-success-subtle text-success">Valid</span>'
                    }),
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search recipients...',
                        info: 'Showing _START_–_END_ of _TOTAL_ recipients'
                    }
                });
                const emailColumn = findColumn(['email', 'e_mail', 'email_address']);
                const nameColumn = findColumn(['nama', 'name']) || recipientColumns[0];
                const recipientOptions = recipients.map((recipient, index) => $('<option>').val(index).text(
                    (recipient[nameColumn] || 'Recipient ' + (index + 1)) + ' - ' + (emailColumn ?
                        recipient[emailColumn] : '')
                ));
                $('#previewRecipient').empty().append(recipientOptions);
                $('#recipientSummary').text('Showing 1–10 of ' + recipients.length + ' recipients');
            }

            function importRows(rows) {
                if (!rows.length) {
                    alert('The imported file does not contain any rows.');
                    return;
                }
                const headers = rows.shift().map((header, index) => normalizeHeader(header, index));
                recipientColumns = [...new Set(headers)];
                recipients = rows.filter((row) => row.some((value) => String(value).trim() !== '')).map((row) =>
                    recipientColumns.reduce((record, column, index) => {
                        record[column] = String(row[index] ?? '').trim();
                        return record;
                    }, {}));
                availableVariables = recipientColumns;
                const emailColumn = findColumn(['email', 'e_mail', 'email_address']);
                const validEmails = emailColumn ? recipients.filter((recipient) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/
                    .test(recipient[emailColumn])).length : 0;
                $('#totalRows').text(recipients.length);
                $('#validEmails').text(validEmails);
                $('#invalidEmails').text(recipients.length - validEmails);
                $('#validRecipientBadge').text(validEmails + ' valid recipients');
                $('#detectedColumns').html(recipientColumns.map((name) => '<span class="badge ' + (name ===
                    emailColumn ? 'bg-success' : 'bg-light text-dark') + '">' + name + (name ===
                    emailColumn ? ' ✓' : '') + '</span>').join(''));
                $('#emailColumnWarning').toggleClass('d-none', Boolean(emailColumn));
                $('#importResult, #recipientSection').removeClass('d-none');
                $('#clearRecipients').removeClass('d-none');
                renderButtons('#importedVariables', availableVariables);
                renderRecipients();
                setDefaultMessage();
                validateVariables();
            }

            function importRecipients(file) {
                if (!file) return;
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds the 5 MB limit.');
                    return;
                }
                const extension = file.name.split('.').pop().toLowerCase();
                const reader = new FileReader();
                reader.onload = function(event) {
                    try {
                        if (extension !== 'csv' && typeof XLSX === 'undefined') throw new Error(
                            'Excel parser unavailable');
                        const workbook = extension === 'csv' ? null : XLSX.read(event.target.result, {
                            type: 'array'
                        });
                        const rows = extension === 'csv' ? parseCsv(event.target.result) : XLSX.utils
                            .sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], {
                                header: 1,
                                defval: ''
                            });
                        importRows(rows);
                    } catch (error) {
                        alert('Unable to read this file. Please check the file format.');
                    }
                };
                extension === 'csv' ? reader.readAsText(file) : reader.readAsArrayBuffer(file);
            }
            $('#chooseRecipients').on('click', () => $('#recipientFile').trigger('click'));
            $('#recipientDropzone').on('dragover', (e) => {
                e.preventDefault();
                $('#recipientDropzone').addClass('is-dragging');
            }).on('dragleave drop', () => $('#recipientDropzone').removeClass('is-dragging')).on('drop', (
                e) => {
                e.preventDefault();
                importRecipients(e.originalEvent.dataTransfer.files[0]);
            });
            $('#recipientFile').on('change', function() {
                importRecipients(this.files[0]);
            });

            function clearImportedRecipients() {
                if (recipientTable) {
                    recipientTable.destroy();
                    recipientTable = null;
                }
                recipients = [];
                recipientColumns = [];
                availableVariables = [];
                $('#recipientFile').val('');
                $('#recipientTable thead, #recipientTable tbody').empty();
                $('#previewRecipient').empty();
                $('#importResult, #recipientSection, #emailColumnWarning').addClass('d-none');
                $('#clearRecipients').addClass('d-none');
                $('#totalRows, #validEmails, #invalidEmails').text('0');
                $('#validRecipientBadge').text('0 valid recipients');
                $('#detectedColumns').empty();
                $('#recipientSummary').text('No recipients imported.');
                renderButtons('#importedVariables', []);
                $('#message').summernote('code',
                    '<p>Dear parent,</p><p>Write your message here.</p><p>Thank you.</p>');
                validateVariables();
                $('#recipientFile').trigger('click');
            }

            $('#clearRecipients').on('click', clearImportedRecipients);

            function formatSize(bytes) {
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function renderAttachments() {
                $('#attachmentList').html(attachments.map((file, index) =>
                    '<div class="attachment-row d-flex justify-content-between align-items-center"><div><i class="bi bi-file-earmark me-2 text-primary"></i><span class="fw-semibold small">' +
                    file.name + '</span><div class="text-muted small ms-4">' + formatSize(file.size) +
                    '</div></div><button type="button" class="btn btn-sm btn-link text-danger remove-attachment" data-index="' +
                    index + '"><i class="bi bi-x"></i> Remove</button></div>').join(''));
                $('.remove-attachment').on('click', function() {
                    attachments.splice($(this).data('index'), 1);
                    renderAttachments();
                });
            }
            $('#chooseAttachments').on('click', () => $('#attachmentFiles').trigger('click'));
            $('#attachmentFiles').on('change', function() {
                $('#attachmentAlert').addClass('d-none');
                [...this.files].forEach((file) => {
                    if (file.size > 5 * 1024 * 1024) {
                        $('#attachmentAlert').removeClass('d-none').text(
                            'File size exceeds the 5 MB limit.');
                        return;
                    }
                    if (attachments.length >= 2) {
                        $('#attachmentAlert').removeClass('d-none').text(
                            'Maximum 2 attachments are allowed.');
                        return;
                    }
                    attachments.push(file);
                });
                renderAttachments();
                this.value = '';
            });

            function updatePreview() {
                const recipient = recipients[$('#previewRecipient').val() || 0];
                if (!recipient) {
                    $('#previewTo, #previewSubject').text('');
                    $('#previewMessage').empty();
                    return;
                }
                const emailColumn = findColumn(['email', 'e_mail', 'email_address']);
                $('#previewTo').text(emailColumn ? recipient[emailColumn] : '');
                $('#previewSubject').text($('#emailSubject').val() || 'September Billing Information');
                $('#previewMessage').html(renderTemplate($('#message').summernote('code'), recipient));
                $('#previewAttachments').html(attachments.length ?
                    '<div class="fw-semibold small mb-2">Attachments</div>' + attachments.map((file) =>
                        '<div class="small text-muted mb-1"><i class="bi bi-paperclip me-1"></i>' + file
                        .name +
                        ' — ' + formatSize(file.size) + '</div>').join('') : '');
            }
            $('#previewButton, #refreshPreview, #previewRecipient').on('click change', updatePreview);
            $('#emailSubject').on('input', updatePreview);
            $('#saveDraft').on('click', () => alert('Campaign UI is ready to be connected to backend.'));
        });
    </script>
@endsection
