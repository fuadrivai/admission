@php
    $hasError = $errors->has($field->field_key);
    $options = $field->options_json ?? [];
@endphp

<label class="event-field-label">
    {{ $field->label }}
    @if ($field->is_required)
        <span class="required-asterisk">*</span>
    @endif
</label>
<div class="event-options-group{{ $hasError ? ' has-error' : '' }}">
    @foreach ($options as $index => $option)
        @php
            $value = $option['value'] ?? $option;
            $label = $option['label'] ?? $value;
            $isFirst = $index === 0;
        @endphp
        <label class="event-option-card">
            <input type="radio" name="{{ $field->field_key }}"
                id="event-field-{{ Str::slug($field->field_key) }}-{{ Str::slug((string) $value) }}"
                value="{{ $value }}" {{ old($field->field_key) == $value ? 'checked' : '' }}
                {{ $field->is_required && $isFirst ? 'required' : '' }}>
            <span class="event-option-label">{{ $label }}</span>
        </label>
    @endforeach
</div>
@error($field->field_key)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
