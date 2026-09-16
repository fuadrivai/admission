@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <style>
        .dt-button {
            margin-left: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal table-striped">
                        <table class="table" id="tbl-academic-year">
                            <thead>
                                <tr class="text-center">
                                    <th>Academic Year</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script>
        $(document).ready(function() {
            tblAY = $('#tbl-academic-year').DataTable({
                responsive: true,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'New Email Form <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-add-form',
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        window.location.href = '/blast/email/form';
                    }
                }],
                language: {
                    info: 'Page _PAGE_ of _PAGES_',
                    lengthMenu: '_MENU_ ',
                    search: '',
                    searchPlaceholder: 'Search..'
                },
            });

        });
    </script>
@endsection
