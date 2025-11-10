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
        @if (session('whatsapp-success'))
            <small>
                <i class="text-success">{{ session('whatsapp-success') }}</i>
            </small>
        @endif
        <div class="card">
            <div class="card-header">
                <i class="fa fa-whatsapp me-2"></i>Whatsapp Code
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal">
                        <table class="table table-striped" id="tbl-whatsapp">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($waCodes as $wa)
                                    <tr>
                                        <td>{{ $wa->branch->name }}</td>
                                        <td>{{ $wa->code ?? '' }}</td>
                                        <td>{{ $wa->description ?? '' }}</td>
                                        <td><button data-branch={{ $wa->branch->id }} data-id={{ $wa->id }}
                                                class="btn btn-info btn-sm btn-edit-wa"><i
                                                    class="fa fa-pencil text-white"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if (session('email-success'))
            <small>
                <i class="text-success">{{ session('email-success') }}</i>
            </small>
        @endif
        <div class="card">
            <div class="card-header">
                <i class="fa fa-envelope me-2"></i>Email Settings
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive datatable-minimal">
                        <table class="table table-striped" id="tbl-email">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>mailer</th>
                                    <th>Host / Port</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emails as $email)
                                    <tr>
                                        <td>{{ $email->branch->name }}</td>
                                        <td>{{ $email->from_name ?? '' }}</td>
                                        <td>{{ $email->username ?? '' }}</td>
                                        <td>{{ $email->host ?? '' }}</td>
                                        <td>{{ $email->mailer ?? '' }} / {{ $email->port }}</td>
                                        <td><a href="/setting/form/email/{{ $email->id }}"
                                                class="btn btn-info btn-sm btn-edit-email"><i
                                                    class="fa fa-pencil text-white"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="modal-whatsapp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form id="form-whatsapp" action="/setting/form/wa" method="POST" autocomplete="off"
                    class="needs-validation">
                    @csrf
                    <div id="method-form-wa"></div>
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title white" id="myModalLabel160">Whatsapp Form</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="code" class="form-label required-label">Whatsapp Code</label>
                                <div class="form-group has-icon-left">
                                    <div class="position-relative">
                                        <input type="hidden" id="wa_id" name="wa_id">
                                        <input type="text" class="form-control" id="code" name="code" required
                                            placeholder="Enter whatsapp code" value="">
                                        <div class="form-control-icon">
                                            <i class="fa fa-whatsapp"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="branch_id" class="form-label required-label">Branch</label>
                                <div class="form-group">
                                    <select name="branch_id" required id="branch_id" class="form-select">
                                        <option value="" disabled selected> -- select branch --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label required-label">Description*</label>
                                <input type="text" class="form-control" name="description" id="description">
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
            tblWhatsapp = $('#tbl-whatsapp').DataTable({
                responsive: true,
                searching: false,
                paging: false,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add Code <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-whatsapp'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        $('#method-form-wa').append(`
                            @method('post')
                        `)
                        $('#modal-whatsapp').modal('show')
                    }
                }],
                language: {
                    info: "Page _PAGE_ of _PAGES_",
                    lengthMenu: "_MENU_ ",
                    search: "",
                    searchPlaceholder: "Search.."
                },
            });

            tblEmail = $('#tbl-email').DataTable({
                responsive: true,
                searching: false,
                paging: false,
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add Email <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-email'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        window.location.href = "/setting/form/email";
                    }
                }],
                language: {
                    info: "Page _PAGE_ of _PAGES_",
                    lengthMenu: "_MENU_ ",
                    search: "",
                    searchPlaceholder: "Search.."
                },
            });

            $('.btn-edit-wa').on('click', function() {
                let data = tblWhatsapp.row($(this).parents('tr')).data();
                let waId = $(this).data('id');
                let branchId = $(this).data('branch');

                $('#description').val(data[2])
                $('#code').val(data[1])
                $('#branch_id').val(branchId).trigger('change')
                $('#wa_id').val(waId)
                $('#method-form-wa').append(`
                    @method('put')
                `)
                $('#modal-whatsapp').modal('show')
            })

        });
    </script>
@endsection
