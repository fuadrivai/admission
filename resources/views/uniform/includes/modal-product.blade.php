<!-- Modal Product -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Product Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm">
                @csrf
                <input type="hidden" id="productId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="productName" class="form-label">Product Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="productName" name="name"
                                placeholder="e.g., School Shirt" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="productCategory" class="form-label">Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="productCategory" name="category_id" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="hasSize" name="has_size"
                                    value="1">
                                <label class="form-check-label" for="hasSize">
                                    Product Has Size
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="sizeOptionsContainer" style="display: none;">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Select Sizes</label>
                            <div id="sizesCheckboxes">
                                <!-- Sizes will be loaded here -->
                            </div>
                        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hasSizeCheckbox = document.getElementById('hasSize');
        const sizeOptionsContainer = document.getElementById('sizeOptionsContainer');
        const sizesCheckboxes = document.getElementById('sizesCheckboxes');

        // Toggle size options visibility
        hasSizeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                sizeOptionsContainer.style.display = 'block';
                // loadSizes();
            } else {
                sizeOptionsContainer.style.display = 'none';
                sizesCheckboxes.innerHTML = '';
            }
        });

        // Load sizes from API/endpoint
        function loadSizes() {
            // Replace with your actual API endpoint
            fetch('/api/sizes')
                .then(response => response.json())
                .then(data => {
                    sizesCheckboxes.innerHTML = '';
                    data.forEach(size => {
                        const div = document.createElement('div');
                        div.className = 'form-check';
                        div.innerHTML = `
                        <input class="form-check-input size-checkbox" type="checkbox" 
                               name="sizes[]" value="${size.id}" id="size_${size.id}">
                        <label class="form-check-label" for="size_${size.id}">
                            ${size.name}
                        </label>
                    `;
                        sizesCheckboxes.appendChild(div);
                    });
                })
                .catch(error => console.error('Error loading sizes:', error));
        }

        // Reset form when modal is shown
        document.getElementById('productModal').addEventListener('show.bs.modal', function() {
            document.getElementById('productForm').reset();
            sizeOptionsContainer.style.display = 'none';
            sizesCheckboxes.innerHTML = '';
        });
    });
</script>
