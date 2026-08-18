@extends('main-layout.index')

@section('content-style')
    <style>
        .field-form-shell {
            max-width: 980px;
            margin: 0 auto;
        }

        .field-form-shell .field-section {
            border-top: 1px solid #e5e7eb;
            padding-top: 1.5rem;
            margin-top: 1.75rem;
        }

        .field-form-shell .field-section:first-child {
            border-top: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .field-form-shell .field-label {
            display: block;
            font-size: 1.05rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 0.7rem;
        }

        .field-form-shell .required-mark {
            color: #d93025;
            margin-left: 0.2rem;
        }

        .field-form-shell .modern-input,
        .field-form-shell .modern-select,
        .field-form-shell .modern-textarea {
            width: 100%;
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            background: #f7f7f8;
            color: #1f2937;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            min-height: 52px;
        }

        .field-form-shell .modern-input:focus,
        .field-form-shell .modern-select:focus,
        .field-form-shell .modern-textarea:focus {
            background: #fff;
            border-color: #c7d2fe;
            box-shadow: 0 0 0 0.12rem rgba(99, 102, 241, 0.12);
            outline: none;
        }

        .field-form-shell .modern-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .field-form-shell .mini-note {
            display: block;
            margin-top: 0.5rem;
            color: #6b7280;
            font-size: 0.92rem;
        }

        .field-form-shell .check-card {
            padding: 1rem 1.2rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .field-form-shell .check-card .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .field-form-shell .check-card .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
            margin-top: 0;
        }

        .field-form-shell .check-card .form-check-label {
            font-size: 1.02rem;
            font-weight: 600;
            color: #1f2937;
        }

        .field-form-shell .system-note {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #374151;
            font-size: 0.97rem;
        }

        .field-form-shell .system-note .info-icon {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #374151;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1;
        }

        .field-form-shell .btn-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 2rem;
        }

        .field-form-shell .btn-save {
            border: 0;
            border-radius: 10px;
            background: #2563eb;
            color: #fff;
            padding: 0.75rem 1.4rem;
            font-weight: 600;
        }

        .field-form-shell .btn-cancel {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: #fff;
            color: #374151;
            padding: 0.75rem 1.2rem;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
@endsection

@section('content-child')
    <section class="section field-form-shell">
        <div class="card">
            <div class="card-body" style="padding: 1.25rem 1.5rem 1.5rem;">
                <form method="POST"
                    action="{{ isset($formField) ? route('event.forms.update', [$event, $formField]) : route('event.forms.store', $event) }}">
                    @csrf
                    @if (isset($formField))
                        @method('PUT')
                    @endif

                    <div class="field-section">
                        <h5 style="margin: 0 0 1.2rem; font-weight: 500; color: #374151;">Field Details</h5>

                        <div class="mb-4">
                            <label class="field-label" for="label">Question Label <span
                                    class="required-mark">*</span></label>
                            <input id="label" type="text" name="label" class="modern-input"
                                value="{{ old('label', $formField->label ?? '') }}"
                                placeholder="Contoh: Nama Anak, Asal Sekolah, dll" required>
                        </div>

                        <div class="mb-4">
                            <label class="field-label" for="type">Input Type</label>
                            <select id="type" name="type" class="modern-select" required>
                                @foreach (['text', 'textarea', 'select', 'radio', 'checkbox', 'email', 'phone', 'number', 'date'] as $type)
                                    <option value="{{ $type }}"
                                        {{ old('type', $formField->type ?? 'text') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="options-wrapper">
                            <label class="field-label" for="options_json">Options (for Select/Radio/Checkbox)</label>
                            <textarea id="options_json" name="options_json" class="modern-textarea" rows="5"
                                placeholder="Satu baris satu opsi
Contoh:
TK A
TK B
SD C">{{ old('options_json', isset($formField) && $formField->options_json ? json_encode($formField->options_json) : '') }}</textarea>
                            <small class="mini-note">Kosongkan jika tipe bukan select, radio, atau checkbox.</small>
                        </div>

                        <div class="mb-4">
                            <div class="check-card">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_required" name="is_required"
                                        value="1"
                                        {{ old('is_required', $formField->is_required ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_required">Required Field</label>
                                </div>
                                <div class="mini-note" style="margin-top: 0.6rem;">User wajib mengisi pertanyaan ini.</div>
                            </div>
                        </div>

                        <div class="mb-4" id="primary-email-wrapper" style="display: none;">
                            <div class="check-card">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_primary_email"
                                        name="is_primary_email" value="1"
                                        {{ old('is_primary_email', $formField->is_primary_email ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_primary_email">Send confirmation to this
                                        email?</label>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="field_key" id="field_key"
                            value="{{ old('field_key', $formField->field_key ?? '') }}">

                        <div class="system-note">
                            <span class="info-icon">i</span>
                            <span>Field Key akan dibuat otomatis secara unik oleh sistem.</span>
                        </div>
                    </div>

                    <div class="btn-actions">
                        <button type="submit" class="btn-save">Save</button>
                        <a href="{{ route('event.forms.index', $event) }}" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        function slugify(value) {
            return (value || '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '_')
                .replace(/-+/g, '_')
                .replace(/^_+|_+$/g, '');
        }

        function toggleOptions() {
            const type = $('#type').val();
            const optionsWrapper = $('#options-wrapper');
            const shouldShow = ['select', 'radio', 'checkbox'].includes(type);
            optionsWrapper.toggle(shouldShow);
        }

        function togglePrimaryEmailField() {
            const type = $('#type').val();
            const primaryEmailWrapper = $('#primary-email-wrapper');
            const isEmailType = type === 'email';

            primaryEmailWrapper.toggle(isEmailType);

            if (!isEmailType) {
                $('#is_primary_email').prop('checked', false);
            }
        }

        $(document).ready(function() {
            const labelInput = $('#label');
            const fieldKeyInput = $('#field_key');

            function syncFieldKey() {
                const generated = slugify(labelInput.val()) || 'field';
                fieldKeyInput.val(generated);
            }

            labelInput.on('input', syncFieldKey);
            $('#type').on('change', function() {
                toggleOptions();
                togglePrimaryEmailField();
            });

            syncFieldKey();
            toggleOptions();
            togglePrimaryEmailField();
        });
    </script>
@endsection
