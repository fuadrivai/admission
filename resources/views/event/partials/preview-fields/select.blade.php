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
    <select class="form-select" disabled>
        <option value="">-- Select --</option>
        @foreach ($options as $option)
            @php
                $optionLabel = is_array($option) ? $option['label'] ?? ($option['value'] ?? '') : (string) $option;
            @endphp
            <option value="{{ is_array($option) ? $option['value'] ?? $optionLabel : $optionLabel }}">
                {{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
