<label for="event-field-{{ Str::slug($field->field_key) }}">
    {{ $field->label }}
    @if ($field->is_required)
        <span class="required-asterisk">*</span>
    @endif
</label>
<input id="event-field-{{ Str::slug($field->field_key) }}" type="tel" name="{{ $field->field_key }}"
    value="{{ old($field->field_key) }}" class="form-control @error($field->field_key) is-invalid @enderror"
    {{ $field->is_required ? 'required' : '' }} inputmode="tel" autocomplete="tel">
@error($field->field_key)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
