@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <style>
        .dt-button {
            margin-left: 0.5rem;
            margin-bottom: 0.5rem
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive datatable-minimal table-striped">
                    <table class="table" id="tbl-division">
                        <thead>
                            <tr class="text-center">
                                <th>Division</th>
                                <th>Levels</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($divisions as $division)
                                <tr>
                                    <td>{{ $division->name }}</td>
                                    <td>{{ count($division->levels) }}</td>
                                    <td class="text-center">
                                        <button wire:click="openModal({{ $division->id }})"
                                            class="btn btn-sm btn-primary btn-division">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade text-left" id="primary" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel160" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="save" autocomplete="off" class="needs-validation" novalidate>
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white">{{ $modalTitle ?? 'Division Form' }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <label for="division" class="form-label required-label">Division</label>
                            <input type="text" wire:model.defer="name" name="name" class="form-control" id="division"
                                required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" id="btn-accept" class="btn btn-primary ms-1">
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            initDivisionTable();
        });

        document.addEventListener('DOMContentLoaded', function() {
            initDivisionTable();
        });

        function initDivisionTable() {
            if ($.fn.DataTable.isDataTable('#tbl-division')) {
                $('#tbl-division').DataTable().destroy();
            }

            tblDivision = $('#tbl-division').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add division <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-level'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        window.callLivewire('openModal');
                    }
                }],
                language: {
                    info: "Page _PAGE_ of _PAGES_",
                    lengthMenu: "_MENU_ ",
                    search: "",
                    searchPlaceholder: "Search.."
                },
            });
        }

        // browser events dari Livewire
        window.addEventListener('show-modal', () => {
            console.log('Modal show event received');
            const modal = new bootstrap.Modal(document.getElementById('primary'));
            modal.show();
        });

        window.addEventListener('hide-modal', () => {
            console.log('Modal hide event received');
            const modal = bootstrap.Modal.getInstance(document.getElementById('primary'));
            if (modal) modal.hide();
        });
    </script>
@endsection
