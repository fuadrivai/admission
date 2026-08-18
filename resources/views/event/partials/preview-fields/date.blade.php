<div class="form-group">
    <label class="form-label">
        {{ $field->label }}
        @if ($field->is_required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input type="date" class="form-control" disabled>
</div>
