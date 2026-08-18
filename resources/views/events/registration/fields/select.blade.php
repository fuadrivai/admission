<label for="event-field-{{ Str::slug($field->field_key) }}">
    {{ $field->label }}
    @if ($field->is_required)
        <span class="required-asterisk">*</span>
    @endif
</label>
<select id="event-field-{{ Str::slug($field->field_key) }}" name="{{ $field->field_key }}"
    class="form-select @error($field->field_key) is-invalid @enderror" {{ $field->is_required ? 'required' : '' }}>
    <option value="">-- Select {{ $field->label }} --</option>
    @foreach ($field->options_json ?? [] as $option)
        <option value="{{ $option['value'] ?? $option }}"
            {{ old($field->field_key) == ($option['value'] ?? $option) ? 'selected' : '' }}>
            {{ $option['label'] ?? ($option['value'] ?? $option) }}
        </option>
    @endforeach
</select>
@error($field->field_key)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
