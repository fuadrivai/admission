@php
    $options = is_array($field->options_json) ? $field->options_json : [];
@endphp

<div class="form-group">
    <label class="form-label">
        {{ $field->label }}
        @if ($field->is_required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select class="form-select" data-preview-field-key="{{ $field->field_key }}"
        data-preview-other-select="{{ $field->field_key }}"
        @if ($field->dependsOnField) data-preview-dependent-on="{{ $field->dependsOnField->field_key }}"
            data-preview-dependent-options='@json($field->options_json ?? [])' disabled @endif>
        <option value="">-- Select --</option>
        @unless ($field->dependsOnField)
            @foreach ($options as $option)
                @php
                    $optionLabel = is_array($option) ? $option['label'] ?? ($option['value'] ?? '') : (string) $option;
                @endphp
                <option value="{{ is_array($option) ? $option['value'] ?? $optionLabel : $optionLabel }}">
                    {{ $optionLabel }}</option>
            @endforeach
        @endunless
        @if ($field->allow_other)
            <option value="__OTHER__">Other</option>
        @endif
    </select>
    @if ($field->allow_other)
        <input type="text" class="form-control mt-2" data-preview-other-input="{{ $field->field_key }}"
            placeholder="Please specify" disabled>
    @endif
</div>
