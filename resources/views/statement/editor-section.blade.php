<div class="statement-section border rounded p-3 mb-3" data-section-index="__SECTION_INDEX__"
    id="statement-section-__SECTION_INDEX__">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">Section</h6>
        <button type="button" class="btn btn-sm btn-outline-danger remove-section">Remove Section</button>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">English Title</label>
            <input type="hidden" name="sections[__SECTION_INDEX__][id]" value="">
            <input type="text" name="sections[__SECTION_INDEX__][title_en]" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Indonesian Title</label>
            <input type="text" name="sections[__SECTION_INDEX__][title_id]" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sections[__SECTION_INDEX__][sort_order]" class="form-control" value="1"
                required>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h6 class="mb-0">Items</h6>
        <button type="button" class="btn btn-sm btn-outline-secondary toggle-section-items" aria-expanded="true">
            Hide Items
        </button>
    </div>

    <div class="section-items-content">
        <div class="items-list"></div>

        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-sm btn-outline-primary add-item">Add Item</button>
        </div>
    </div>

    <div class="mt-3">
        <div class="form-check">
            <input type="checkbox" name="sections[__SECTION_INDEX__][is_required]" value="1"
                class="form-check-input">
            <label class="form-check-label">Required section</label>
        </div>
    </div>
</div>
