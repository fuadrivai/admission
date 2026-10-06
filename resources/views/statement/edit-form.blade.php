<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Type</label>
        <input type="text" name="type" class="form-control" value="{{ old('type', $document->type ?? 'parent') }}"
            required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control"
            value="{{ old('name', $document->name ?? 'Parent Statement') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Version</label>
        <input type="text" name="version" class="form-control"
            value="{{ old('version', $document->version ?? '1.0') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach (['DRAFT', 'PUBLISHED', 'ARCHIVED'] as $statusOption)
                <option value="{{ $statusOption }}" {{ old('status', $document->status ?? 'DRAFT') == $statusOption ? 'selected' : '' }}>
                    {{ $statusOption }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Effective Date</label>
        <input type="date" name="effective_at" class="form-control"
            value="{{ old('effective_at', optional($document->effective_at)->format('Y-m-d')) }}">
    </div>
</div>

<div class="mb-3">
    <h5 class="mb-0">Sections</h5>
</div>

<div id="statement-sections"></div>

<div class="row g-3 mt-4">
    <div class="col-md-6">
        <label for="description_en" class="form-label">Closing text - English</label>
        <textarea id="description_en" name="description_en" class="form-control" rows="3">{{ old('description_en', $document->description_en ?? $document->description ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label for="description_id" class="form-label">Closing text - Indonesian</label>
        <textarea id="description_id" name="description_id" class="form-control" rows="3">{{ old('description_id', $document->description_id ?? '') }}</textarea>
    </div>
</div>

<div class="mt-3 d-flex justify-content-end">
    <button type="button" id="add-section" class="btn btn-outline-primary btn-sm">Add Section</button>
</div>

<div class="mt-4 d-flex justify-content-between">
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Document</button>
        <a href="{{ route('setting.statement.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
    @if ($document->exists)
        <a href="{{ route('setting.statement.preview', $document->id) }}" class="btn btn-info">Preview</a>
    @endif
</div>
