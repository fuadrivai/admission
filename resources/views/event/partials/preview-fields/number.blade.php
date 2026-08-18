<div class="form-group">
    <label class="form-label">
        {{ $field->label }}
        @if ($field->is_required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input type="number" class="form-control" disabled>
</div>
