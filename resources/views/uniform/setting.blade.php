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
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    
                </ul>
                <div class="tab-content mt-4" id="myTabContent">
                    <div class="tab-pane fade show active" id="product" role="tabpanel" aria-labelledby="product-tab">
                        <table class="table table-sm table-striped" id="productTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Has Size</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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

    @include('uniform.includes.modal-product')
@endsection


@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/extensions/datatables.net-buttons/js/dataTables.buttons.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#productTable').DataTable({
                pagingType: 'simple',
                dom: `<"row"<"col-sm-6 d-flex align-items-center"lB><"col-sm-6"f>>tip`,
                buttons: [{
                    text: 'Add Product <i class="fa fa-plus-circle"></i>',
                    attr: {
                        id: 'btn-product'
                    },
                    className: 'btn btn-success btn-sm font-weight-bold',
                    action: function() {
                        $('#productModal').modal('show');
                    }
                }],
                language: {
                    info: "Page _PAGE_ of _PAGES_",
                    lengthMenu: "_MENU_ ",
                    search: "",
                    searchPlaceholder: "Search.."
                },
                data: [],
            });

            getBranch();
        });

        function getBranch() {
            blockUI();
            ajax(null, `/branch/get`, 'GET', function(json) {
                branches = json;
                branches.forEach((branch,i) => {
                    $("#myTab").append(
                        `
                        <li class="nav-item" role="presentation">
                            <a class="nav-link ${i == 0 ? 'active' : ''}" id="${branch.name}-tab" data-bs-toggle="tab" href="#${branch.name}" role="tab"
                                aria-controls="${branch.name}" aria-selected="false">${branch.name}</a>
                        </li>
                        `
                    );
                });
            }, function(err) {
                console.log(err)
            })
        }
    </script>
@endsection
