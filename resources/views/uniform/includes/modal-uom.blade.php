<!-- Modal UOM -->
<div class="modal fade" id="uomModal" tabindex="-1" role="dialog" aria-labelledby="uomModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uomModalLabel">Unit of Measure Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uomForm">
                @csrf
                <input type="hidden" id="uomId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="uomName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="uomName" name="uom-name"
                            placeholder="e.g., Piece" required>
                    </div>
                    <div class="mb-3">
                        <label for="uomDescription" class="form-label">Description <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="uomDescription" name="uom-description"
                            placeholder="e.g., Piece" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveUomBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
