@extends('main-layout.index')
@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-users me-2"></i>Maximum Capacity
            </div>
            <div class="card-body">
                <form action="max-capacity" id="form-max" autocomplete="off" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label for="max" class="form-label required-label">Max visitors per time slot</label>
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <input type="hidden" name="id" value="{{ $max->id ?? '' }}">
                                    <input type="number" class="form-control" required placeholder="Enter maximum capacity"
                                        value="{{ $max->max ?? '0' }}" id="max" name="max">
                                    <div class="form-control-icon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            @if (session('success'))
                                <small>
                                    <i class="text-success">{{ session('success') }}</i>
                                </small>
                            @endif
                        </div>
                        <div class="col-md-2" style="padding-top: 34px">
                            <button class="btn btn-block btn-md btn-primary"><i class="fa fa-save"></i> submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if (session('holiday-success'))
            <small>
                <i class="text-success">{{ session('holiday-success') }}</i>
            </small>
        @endif
        <div class="card">
            <div class="card-header">
                <i class="fa fa-calendar me-2"></i>School Holiday
            </div>
            <div class="card-body">
                <div class="row">
                    <table class="table table-striped" id="tbl-holiday">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($holidays as $holiday)
                                <tr>
                                    <td>{{ $holiday->getDateFrontendAttribute() }}</td>
                                    <td>{{ $holiday->description }}</td>
                                    <td>
                                        <form action="/holiday/{{ $holiday->id }}" method="POST"
                                            onsubmit="return confirm('Delete this holiday?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="modal-holiday" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form id="form-holiday" action="/holiday" method="POST" autocomplete="off" class="needs-validation"
                    novalidate>
                    @csrf
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white" id="myModalLabel160">Holiday Form</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date" class="form-label required-label">Start date</label>
                                <div class="form-group has-icon-left">
                                    <div class="position-relative">
                                        <input type="text" class="form-control date-picker" id="date" name="date"
                                            readonly required placeholder="Enter holiday date" value="">
                                        <div class="form-control-icon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="end-date" class="form-label required-label">End date</label>
                                <div class="form-group has-icon-left">
                                    <div class="position-relative">
                                        <input type="text" class="form-control date-picker" id="end-date"
                                            name="end-date" readonly required placeholder="Enter holiday date"
                                            value="">
                                        <div class="form-control-icon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label required-label">Description</label>
                                <input type="text" class="form-control" required name="description" id="description">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" class="btn btn-primary ms-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Submit</span>
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
        $(document).ready(function() {
            tblTime = $('#tbl-holiday').DataTable({
                responsive: true,
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add Holiday <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-holiday'
                    },
                    className: 'btn btn-success btn-md font-weight-bold',
                    action: function() {
                        $('#modal-holiday').modal('show')
                    }
                }],
                language: {
                    info: "Page _PAGE_ of _PAGES_",
                    lengthMenu: "_MENU_ ",
                    search: "",
                    searchPlaceholder: "Search.."
                },
            });
        });
    </script>
@endsection
