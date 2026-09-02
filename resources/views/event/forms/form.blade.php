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

                    @php
                        $oldLabel = old('label', $formField->label ?? '');
                        $oldType = old('type', $formField->type ?? 'text');
                        $oldDependsOnFieldId = old('depends_on_field_id', $formField->depends_on_field_id ?? '');
                        $oldFieldKey = old('field_key', $formField->field_key ?? '');
                        $oldOptions = old('options_json', $formField->options_json ?? '');
                        $oldOptionsText = is_array($oldOptions)
                            ? json_encode($oldOptions)
                            : (is_scalar($oldOptions)
                                ? (string) $oldOptions
                                : '');
                    @endphp

                    <div class="field-section">
                        <h5 style="margin: 0 0 1.2rem; font-weight: 500; color: #374151;">Field Details</h5>

                        <div class="mb-4">
                            <label class="field-label" for="label">Question Label <span
                                    class="required-mark">*</span></label>
                            <input id="label" type="text" name="label" class="modern-input"
                                value="{{ is_scalar($oldLabel) ? $oldLabel : '' }}"
                                placeholder="Contoh: Nama Anak, Asal Sekolah, dll" required>
                        </div>

                        <div class="mb-4">
                            <label class="field-label" for="type">Input Type</label>
                            <select id="type" name="type" class="modern-select" required>
                                @foreach (['text', 'textarea', 'select', 'radio', 'checkbox', 'email', 'phone', 'number', 'date', 'attachment'] as $type)
                                    <option value="{{ $type }}" {{ $oldType == $type ? 'selected' : '' }}>
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
SD C">{{ $oldOptionsText }}</textarea>
                            <small class="mini-note">Kosongkan jika tipe bukan select, radio, atau checkbox.</small>
                        </div>

                        <div class="mb-4" id="depends-on-wrapper" style="display: none;">
                            <label class="field-label" for="depends_on_field_id">Depends On Field</label>
                            <select id="depends_on_field_id" name="depends_on_field_id" class="modern-select">
                                <option value="">None</option>
                                @foreach ($parentFields as $parentField)
                                    <option value="{{ $parentField->id }}"
                                        {{ $oldDependsOnFieldId == $parentField->id ? 'selected' : '' }}>
                                        {{ $parentField->label }} ({{ ucfirst($parentField->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="dependent-options-wrapper" style="display: none;">
                            <label class="field-label">Dependent Options</label>
                            <div id="dependent-options-list">
                                @php
                                    $selectedParentId = old(
                                        'depends_on_field_id',
                                        $formField->depends_on_field_id ?? '',
                                    );
                                    $mapping = old('dependent_options', $formField->options_json ?? []);
                                    $mapping = is_array($mapping) ? $mapping : [];
                                    $optionText = function ($option) {
                                        if (is_array($option)) {
                                            $option = $option['label'] ?? ($option['value'] ?? '');

                                            if (is_array($option)) {
                                                return implode(
                                                    ', ',
                                                    array_filter(
                                                        array_map(function ($value) {
                                                            return is_scalar($value) ? (string) $value : '';
                                                        }, $option),
                                                    ),
                                                );
                                            }
                                        }

                                        return is_scalar($option) ? (string) $option : '';
                                    };
                                @endphp
                                @foreach ($parentFields as $parentField)
                                    @php
                                        $configuredOptions = is_array($parentField->options_json)
                                            ? $parentField->options_json
                                            : [];
                                        $parentOptions = [];
                                        if ($parentField->depends_on_field_id) {
                                            foreach ($configuredOptions as $dependentOptions) {
                                                foreach ((array) $dependentOptions as $dependentOption) {
                                                    if (
                                                        is_scalar($dependentOption) &&
                                                        trim((string) $dependentOption) !== ''
                                                    ) {
                                                        $parentOptions[] = (string) $dependentOption;
                                                    }
                                                }
                                            }
                                            $parentOptions = array_values(array_unique($parentOptions));
                                        } else {
                                            foreach ($configuredOptions as $configuredOption) {
                                                $parentOptions[] = $optionText($configuredOption);
                                            }
                                        }
                                    @endphp
                                    <div class="dependent-parent-group" data-parent-id="{{ $parentField->id }}"
                                        style="display: none;">
                                        @foreach ($parentOptions as $parentOption)
                                            @php
                                                $parentValue = $optionText($parentOption);
                                                $childOptions = is_array($mapping[$parentValue] ?? null)
                                                    ? $mapping[$parentValue]
                                                    : [''];
                                            @endphp
                                            <div class="dependent-option-row mb-2" data-parent-value="{{ $parentValue }}">
                                                <div class="input-group">
                                                    <span class="input-group-text"
                                                        style="min-width: 150px;">{{ $parentValue }}</span>
                                                    <input type="text" class="form-control dependent-option-input"
                                                        name="dependent_options[{{ $parentValue }}][]"
                                                        value="{{ $optionText($childOptions[0] ?? '') }}"
                                                        placeholder="Available option">
                                                    <button type="button"
                                                        class="btn btn-outline-danger remove-dependent-option">-</button>
                                                </div>
                                                @foreach (array_slice($childOptions, 1) as $childOption)
                                                    <div class="input-group mt-2 dependent-option-extra">
                                                        <span class="input-group-text" style="min-width: 150px;"></span>
                                                        <input type="text" class="form-control dependent-option-input"
                                                            name="dependent_options[{{ $parentValue }}][]"
                                                            value="{{ $optionText($childOption) }}"
                                                            placeholder="Available option">
                                                        <button type="button"
                                                            class="btn btn-outline-danger remove-dependent-option">-</button>
                                                    </div>
                                                @endforeach
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary mt-2 add-dependent-option">+ Add
                                                    option</button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <small class="mini-note">Configure available options for each value of the parent field.</small>
                        </div>

                        <div class="mb-4" id="allow-other-wrapper" style="display: none;">
                            <div class="check-card">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="allow_other" name="allow_other"
                                        value="1"
                                        {{ old('allow_other', $formField->allow_other ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_other">Allow "Other" option</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="check-card">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_required" name="is_required"
                                        value="1"
                                        {{ old('is_required', $formField->is_required ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_required">Required Field</label>
                                </div>
                                <div class="mini-note" style="margin-top: 0.6rem;">User wajib mengisi pertanyaan ini.
                                </div>
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
                            value="{{ is_scalar($oldFieldKey) ? $oldFieldKey : '' }}">

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
            const allowOtherWrapper = $('#allow-other-wrapper');
            const dependsOnWrapper = $('#depends-on-wrapper');
            const shouldShow = ['select', 'radio', 'checkbox'].includes(type);
            const shouldShowDependency = type === 'select';
            optionsWrapper.toggle(shouldShow);
            allowOtherWrapper.toggle(shouldShow);
            dependsOnWrapper.toggle(shouldShowDependency);

            if (!shouldShow) {
                $('#allow_other').prop('checked', false);
            }
            if (!shouldShowDependency) {
                $('#depends_on_field_id').val('');
            }
        }

        function toggleDependentOptions() {
            const parentId = $('#depends_on_field_id').val();
            const isDependent = $('#type').val() === 'select' && parentId !== '';
            $('#dependent-options-wrapper').toggle(isDependent);
            $('#options-wrapper').toggle(!isDependent && ['select', 'radio', 'checkbox'].includes($('#type').val()));

            $('.dependent-parent-group').each(function() {
                const $group = $(this);
                const isSelected = isDependent && String($group.data('parent-id')) === String(parentId);

                $group.toggle(isSelected);
                $group.find('.dependent-option-input').prop('disabled', !isSelected);
            });
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
                toggleDependentOptions();
            });

            $('#depends_on_field_id').on('change', toggleDependentOptions);

            $(document).on('click', '.add-dependent-option', function() {
                const $group = $(this).closest('.dependent-option-row');
                const parentValue = $group.data('parent-value');
                const inputName = 'dependent_options[' + parentValue + '][]';
                $(this).before(
                    '<div class="input-group mt-2 dependent-option-extra"><span class="input-group-text" style="min-width: 150px;"></span><input type="text" class="form-control dependent-option-input" name="' +
                    inputName +
                    '" placeholder="Available option"><button type="button" class="btn btn-outline-danger remove-dependent-option">-</button></div>'
                );
            });

            $(document).on('click', '.remove-dependent-option', function() {
                const $row = $(this).closest('.input-group');
                const $parentGroup = $row.closest('.dependent-option-row');
                if ($parentGroup.find('.dependent-option-input').length > 1) {
                    $row.remove();
                } else {
                    $row.find('input').val('');
                }
            });

            syncFieldKey();
            toggleOptions();
            togglePrimaryEmailField();
            toggleDependentOptions();
        });
    </script>
@endsection
