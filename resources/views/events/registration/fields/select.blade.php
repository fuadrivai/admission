<label for="event-field-{{ Str::slug($field->field_key) }}">
    {{ $field->label }}
    @if ($field->is_required)
        <span class="required-asterisk">*</span>
    @endif
</label>
<select id="event-field-{{ Str::slug($field->field_key) }}" name="{{ $field->field_key }}"
    class="form-select @error($field->field_key) is-invalid @enderror" {{ $field->is_required ? 'required' : '' }}
    @if ($field->allow_other) data-other-select="{{ $field->field_key }}" @endif>
    <option value="">-- Select {{ $field->label }} --</option>
    @foreach ($field->options_json ?? [] as $option)
        <option value="{{ $option['value'] ?? $option }}"
            {{ old($field->field_key) == ($option['value'] ?? $option) ? 'selected' : '' }}>
            {{ $option['label'] ?? ($option['value'] ?? $option) }}
        </option>
    @endforeach
    @if ($field->allow_other)
        <option value="__OTHER__" data-other-required="{{ $field->is_required ? '1' : '0' }}"
            {{ old($field->field_key) === '__OTHER__' ? 'selected' : '' }}>Other</option>
    @endif
</select>
@if ($field->allow_other)
    <div class="event-other-wrapper mt-2" data-other-wrapper="{{ $field->field_key }}" style="display: none;">
        <label for="{{ $field->field_key }}__other">Please specify:</label>
        <input type="text" id="{{ $field->field_key }}__other" name="{{ $field->field_key }}__other"
            class="form-control event-other-input" data-other-input="{{ $field->field_key }}"
            value="{{ old($field->field_key . '__other', '') }}" disabled>
    </div>
@endif
@error($field->field_key)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
