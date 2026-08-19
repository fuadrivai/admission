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

    <div class="preview-options">
        @foreach ($options as $option)
            @php
                $optionLabel = is_array($option) ? $option['label'] ?? ($option['value'] ?? '') : (string) $option;
            @endphp
            <div class="form-check">
                <input class="form-check-input" type="checkbox" data-preview-other-field="{{ $field->field_key }}">
                <label class="form-check-label">{{ $optionLabel }}</label>
            </div>
        @endforeach
        @if ($field->allow_other)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" data-preview-other-toggle="{{ $field->field_key }}">
                <label class="form-check-label">Other:</label>
                <input type="text" class="form-control mt-2" data-preview-other-input="{{ $field->field_key }}"
                    disabled>
            </div>
        @endif
    </div>
</div>
