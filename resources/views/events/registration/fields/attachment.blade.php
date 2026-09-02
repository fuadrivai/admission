<label for="event-field-{{ Str::slug($field->field_key) }}">
    {{ $field->label }}
    @if ($field->is_required)
        <span class="required-asterisk">*</span>
    @endif
</label>
<div class="file-upload-preview-wrapper">
    <input id="event-field-{{ Str::slug($field->field_key) }}" type="file" name="{{ $field->field_key }}"
        accept=".pdf,image/png,image/jpeg,image/jpg" class="form-control @error($field->field_key) is-invalid @enderror"
        {{ $field->is_required ? 'required' : '' }} data-file-field="{{ $field->field_key }}">

    <div class="file-preview-box d-none" data-file-preview-for="{{ $field->field_key }}" aria-live="polite">
        <img class="file-preview-image d-none" alt="Selected attachment preview" />
        <div class="file-preview-icon d-none" aria-hidden="true">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="file-preview-meta">
            <div class="file-preview-name">No file selected</div>
            <small class="file-preview-size text-muted"></small>
        </div>
        <button type="button" class="file-remove-btn" data-file-remove="{{ $field->field_key }}"
            aria-label="Remove selected file" title="Remove file">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<small class="text-muted d-block mt-2">Allowed: PDF, JPG, JPEG, PNG • Max 10MB</small>
@error($field->field_key)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
