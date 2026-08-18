@extends('main-layout.index')

@section('content-style')
    <link href="/assets/extensions/summernote/summernote-bs5.min.css" rel="stylesheet">
    <style>
        .email-template-shell {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 0 30px;
        }

        .email-template-shell .field-block {
            margin-bottom: 1.3rem;
        }

        .email-template-shell .field-label {
            display: block;
            font-size: 1.05rem;
            line-height: 1.3;
            font-weight: 700;
            color: #2b2f36;
            margin: 0 0 0.6rem;
        }

        .email-template-shell .modern-input {
            width: 100%;
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            background: #f9fafb;
            color: #1f2937;
            padding: 0.9rem 1rem;
            font-size: 1.02rem;
            min-height: 52px;
        }

        .email-template-shell .modern-input:focus {
            outline: none;
            box-shadow: 0 0 0 0.12rem rgba(99, 102, 241, 0.12);
            border-color: #c7d2fe;
            background: #fff;
        }

        .email-template-shell .note-editor.note-frame {
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .email-template-shell .note-toolbar {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .email-template-shell .note-editing-area {
            background: #fff;
        }

        .email-template-shell .note-placeholder {
            color: #a1a8b3;
        }

        .email-template-shell .variables-panel {
            margin-top: 1rem;
            background: #dfe3e6;
            border-radius: 10px;
            border: 1px solid #d0d5db;
            overflow: hidden;
        }

        .email-template-shell .variables-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #d4d9dd;
            padding: 0.9rem 1rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #374151;
            font-size: 0.8rem;
        }

        .email-template-shell .variables-body {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.15);
        }

        .email-template-shell .variable-tag {
            display: inline-flex;
            align-items: center;
            padding: 0.42rem 0.7rem;
            border-radius: 6px;
            border: 1px solid rgba(128, 136, 145, 0.35);
            background: rgba(255, 255, 255, 0.4);
            color: #212121;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .email-template-shell .variable-tag:hover {
            background: rgba(255, 255, 255, 0.75);
        }

        .email-template-shell .info-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 0.8rem;
            color: #4b5563;
            font-size: 0.92rem;
        }

        .email-template-shell .info-bullet {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #f0f1f2;
            border: 1px solid #d3d8df;
            color: #4b5563;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .email-template-shell .footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.4rem;
            gap: 1rem;
        }

        .email-template-shell .preview-btn,
        .email-template-shell .save-btn {
            border-radius: 8px;
            border: 1px solid #dfe3e8;
            padding: 0.78rem 1.2rem;
            font-weight: 600;
            min-width: 130px;
            background: #fff;
            color: #1f2937;
        }

        .email-template-shell .save-btn {
            background: #1ea672;
            border-color: #1ea672;
            color: #fff;
        }

        .email-template-shell .preview-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .email-template-shell .preview-btn .icon,
        .email-template-shell .save-btn .icon {
            font-size: 0.95rem;
        }

        .var-badge {
            display: inline-block;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
            color: #d63384;
            font-size: 0.85em;
            margin: 2px;
        }

        .email-preview-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.45);
            z-index: 1060;
            padding: 1rem;
        }

        .email-preview-modal.show {
            display: flex;
        }

        .email-preview-dialog {
            width: min(900px, 100%);
            max-height: 90vh;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
            overflow: hidden;
        }

        .email-preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .email-preview-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .email-preview-close {
            border: 0;
            background: transparent;
            font-size: 1.4rem;
            line-height: 1;
            color: #475569;
            cursor: pointer;
        }

        .email-preview-body {
            padding: 1.25rem;
            max-height: 75vh;
            overflow-y: auto;
            background: #fff;
        }
    </style>
@endsection

@section('content-child')
    <section class="section email-template-shell">
        <form method="POST"
            action="{{ isset($template) ? route('event.email-templates.update', [$event, $template]) : route('event.email-templates.store', $event) }}">
            @csrf
            @if (isset($template))
                @method('PUT')
            @endif
            <input type="hidden" name="key" value="{{ old('key', $template->key ?? 'email_template') }}">

            <div class="field-block">
                <label class="field-label" for="subject">Email Subject</label>
                <input id="subject" type="text" name="subject" class="modern-input"
                    value="{{ old('subject', $template->subject ?? '') }}" placeholder="Payment Details - IKLC 2026"
                    required>
            </div>

            <div class="field-block">
                <label class="field-label" for="body">Email Body (HTML)</label>
                <textarea id="body" name="body" class="summernote" required>{{ old('body', $template->body ?? '') }}</textarea>
            </div>

            <div class="variables-panel">
                <div class="variables-header">
                    <span>Core Variables</span>
                    <span>Form Answers</span>
                </div>
                <div class="variables-body">
                    @php($eventFields = $event->forms()->orderBy('order_index')->get())
                    @forelse ($eventFields as $field)
                        @php($fieldKey = $field->field_key ?? 'field_' . $field->id)
                        @php($placeholder = '{' . '{' . $fieldKey . '}' . '}')

                        <span class="variable-tag" data-value="{{ $placeholder }}">
                            {{ $placeholder }}
                        </span>
                    @empty
                        <span class="text-muted">
                            No form fields found for this event yet.
                        </span>
                    @endforelse
                </div>
            </div>

            <div class="info-row">
                <span class="info-bullet">i</span>
                <span>Copy variabel untuk menyalin (opsional) atau ketik manual di editor.</span>
            </div>

            <div class="footer-actions">
                <button type="button" class="preview-btn"><span class="icon">◫</span> Preview</button>
                <button type="submit" class="save-btn"><span class="icon">✓</span> Save Template</button>
            </div>
        </form>
    </section>

    <div id="emailPreviewModal" class="email-preview-modal" aria-hidden="true">
        <div class="email-preview-dialog" role="dialog" aria-modal="true" aria-labelledby="emailPreviewTitle">
            <div class="email-preview-header">
                <h5 id="emailPreviewTitle">Email Preview</h5>
                <button type="button" class="email-preview-close" aria-label="Close preview">&times;</button>
            </div>
            <div id="emailPreviewContent" class="email-preview-body"></div>
        </div>
    </div>
@endsection

@section('content-script')
    <script src="/assets/extensions/summernote/summernote-bs5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 330,
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'help']]
                ]
            });

            $('.preview-btn').on('click', function() {
                const html = $('#body').summernote('code');
                const content = html && html.trim() ? html : '<p>No email content available.</p>';
                $('#emailPreviewContent').html(content);
                $('#emailPreviewModal').addClass('show').attr('aria-hidden', 'false');
            });

            $('#emailPreviewModal .email-preview-close').on('click', function() {
                $('#emailPreviewModal').removeClass('show').attr('aria-hidden', 'true');
            });

            $('#emailPreviewModal').on('click', function(e) {
                if (e.target === this) {
                    $(this).removeClass('show').attr('aria-hidden', 'true');
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#emailPreviewModal').hasClass('show')) {
                    $('#emailPreviewModal').removeClass('show').attr('aria-hidden', 'true');
                }
            });

            $('.variable-tag').on('click', function() {
                const value = $(this).data('value');
                const editor = $('#body');
                const note = editor.summernote('editor');
                if (note && value) {
                    note.insertText(value);
                }
            });
        });
    </script>
@endsection
