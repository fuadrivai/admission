<!-- Modal Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Category Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="category-name"
                            placeholder="e.g., Shirt" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryUnit" class="form-label">Unit of Measure <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="categoryUnit" name="unit_id" required>
                            <option value="">Select UOM</option>
                        </select>
                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" name="category-description"
                                placeholder="Enter category description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
            </form>
        </div>
    </div>
</div>
