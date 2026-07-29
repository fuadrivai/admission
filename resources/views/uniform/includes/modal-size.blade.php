<!-- Modal Size -->
<div class="modal fade" id="sizeModal" tabindex="-1" role="dialog" aria-labelledby="sizeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sizeModalLabel">Size Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sizeForm">
                @csrf
                <input type="hidden" id="sizeId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sizeName" class="form-label">Size <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sizeName" name="size-name"
                            placeholder="e.g., Small" required>
                    </div>
                    <div class="mb-3">
                        <label for="sizeDescription" class="form-label">Description <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sizeDescription" name="size-description"
                            placeholder="e.g., Small" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveSizeBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
