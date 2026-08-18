<label for="event-field-{{ Str::slug($field->field_key) }}">
    {{ $field->label }}
    @if ($field->is_required)
        <span class="required-asterisk">*</span>
    @endif
</label>
<textarea id="event-field-{{ Str::slug($field->field_key) }}" name="{{ $field->field_key }}" rows="4"
    class="form-control @error($field->field_key) is-invalid @enderror" {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_key) }}</textarea>
@error($field->field_key)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
