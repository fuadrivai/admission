<div class="form-group">
    <label class="form-label">
        {{ $field->label }}
        @if ($field->is_required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="border rounded p-3 bg-light text-muted">
        <i class="fas fa-paperclip me-2"></i>
        File attachment (PDF, JPG, JPEG, PNG, max 10MB)
    </div>
</div>
