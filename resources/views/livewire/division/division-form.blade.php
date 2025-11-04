<div wire:ignore.self class="modal fade text-left" id="primary" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form id="form-division" wire:submit.prevent="save" autocomplete="off" class="needs-validation" novalidate>
                @csrf
                <div class="modal-header bg-primary">
                    <h5 class="modal-title white" id="myModalLabel160">Division Form
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <input type="text" class="d-none" name="id" id="id">
                            <label for="division" class="form-label required-label">Division</label>
                            <input wire:model.defer="name" type="text" name="name" class="form-control" required
                                id="division">
                            <div class="invalid-feedback">
                                Insert a valid division
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>
                    <button type="submit" id="btn-accept" class="btn btn-primary ms-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Accept</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        const modalEl = document.getElementById('primary');
        const modal = new bootstrap.Modal(modalEl);

        Livewire.on('show-modal', () => modal.show());
        Livewire.on('close-modal', () => modal.hide());
    });
</script>
