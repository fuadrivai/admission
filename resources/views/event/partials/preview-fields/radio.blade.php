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
                <input class="form-check-input" type="radio" disabled>
                <label class="form-check-label">{{ $optionLabel }}</label>
            </div>
        @endforeach
    </div>
</div>
