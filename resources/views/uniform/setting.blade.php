@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <style>
        /* Card & KPI Styling */
        .kpi-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Nav Pills Styling */
        .custom-nav-pills .nav-link {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            color: #555555;
            transition: all 0.25s ease;
            border: 1px solid transparent;
        }
        .custom-nav-pills .nav-link:hover {
            background-color: #f1f5f9;
            color: #435ebe;
        }
        .custom-nav-pills .nav-link.active {
            background: linear-gradient(135deg, #435ebe 0%, #25396e 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(67, 94, 190, 0.3);
        }

        /* Filter Section Styling */
        .filter-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        /* Table & Badge Styling */
        .table-responsive {
            border-radius: 10px;
        }
        .badge-unit-pcs {
            background-color: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }
        .badge-unit-meter {
            background-color: #ccfbf1;
            color: #0f766e;
            border: 1px solid #99f6e4;
        }
        .badge-size-yes {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-size-no {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .code-badge {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        /* Switch Toggle Styling */
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            cursor: pointer;
        }
        .form-switch .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        .dt-buttons {
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <!-- KPI Summary Cards -->
        <div class="row mb-1">
            <div class="col-6 col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card kpi-card">
                    <div class="card-body px-3 py-3-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-4 col-12 mb-2 mb-xl-0">
                                <div class="kpi-icon bg-light-primary text-primary">
                                    <i class="bi bi-box-seam-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-8 col-12">
                                <h6 class="text-muted font-semibold mb-1">Master Products</h6>
                                <h4 class="font-extrabold mb-0" id="stat-total-products">{{ $stats['total_products'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card kpi-card">
                    <div class="card-body px-3 py-3-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-4 col-12 mb-2 mb-xl-0">
                                <div class="kpi-icon bg-light-success text-success">
                                    <i class="bi bi-tags-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-8 col-12">
                                <h6 class="text-muted font-semibold mb-1">Price Rules</h6>
                                <h4 class="font-extrabold mb-0" id="stat-total-prices">{{ $stats['total_prices'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card kpi-card">
                    <div class="card-body px-3 py-3-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-4 col-12 mb-2 mb-xl-0">
                                <div class="kpi-icon bg-warning text-white">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-8 col-12">
                                <h6 class="text-muted font-semibold mb-1">Active Prices</h6>
                                <h4 class="font-extrabold mb-0" id="stat-active-prices">{{ $stats['active_prices'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card kpi-card">
                    <div class="card-body px-3 py-3-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-4 col-12 mb-2 mb-xl-0">
                                <div class="kpi-icon bg-info text-white">
                                    <i class="bi bi-rulers"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-8 col-12">
                                <h6 class="text-muted font-semibold mb-1">Has Size Items</h6>
                                <h4 class="font-extrabold mb-0" id="stat-has-size">{{ $stats['has_size'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Container with Navigation Pills -->
        <div class="card shadow-sm border-0 rounded-2">
            <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                <ul class="nav nav-pills custom-nav-pills card-header-pills" id="uniformTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#tab-products" type="button" role="tab" aria-controls="tab-products" aria-selected="true">
                            <i class="bi bi-box-seam me-2"></i> Product
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prices-tab" data-bs-toggle="tab" data-bs-target="#tab-prices" type="button" role="tab" aria-controls="tab-prices" aria-selected="false">
                            <i class="bi bi-tags me-2"></i> Pricing Settings
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body pt-4">
                <div class="tab-content" id="uniformTabsContent">
                    <div class="tab-pane fade show active" id="tab-products" role="tabpanel" aria-labelledby="products-tab">
                        <p><strong><i class="bi bi-filter me-1"></i> Filter Products</strong></p>
                        <div class="filter-box">
                            <div class="row align-items-end g-3">
                                <div class="col-md-3">
                                    <label class="form-label font-semibold text-secondary small mb-1">Unit Type</label>
                                    <select class="form-select" id="filterProductUnit">
                                        <option value="">All Units</option>
                                        <option value="pcs">Pcs</option>
                                        <option value="meter">Meter</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-semibold text-secondary small mb-1">Size Requirement</label>
                                    <select class="form-select" id="filterProductHasSize">
                                        <option value="">All</option>
                                        <option value="1">Has Size Options</option>
                                        <option value="0">No Size Required</option>
                                    </select>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-outline-secondary me-2" id="btnResetProductFilter">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                    </button>
                                    <button type="button" class="btn btn-primary font-weight-bold" id="btnOpenAddProduct">
                                        <i class="bi bi-plus-circle-fill me-1"></i> Add Master Product
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Products DataTable -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="tbl-products">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;">Code</th>
                                        <th>Product Name</th>
                                        <th class="text-center" style="width: 110px;">Unit Type</th>
                                        <th class="text-center" style="width: 130px;">Has Size?</th>
                                        <th class="text-center" style="width: 130px;">Price Rules</th>
                                        <th class="text-center" style="width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-prices" role="tabpanel" aria-labelledby="prices-tab">
                        <div class="filter-box">
                            <div class="row align-items-end g-3">
                                <div class="col-md-3">
                                    <label class="form-label font-semibold text-secondary small mb-1"><i class="bi bi-box-seam me-1"></i> Master Product</label>
                                    <select class="form-select" id="filterPriceProduct">
                                        <option value="">All Products</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->code }} - {{ $prod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-semibold text-secondary small mb-1"><i class="bi bi-building me-1"></i> Branch</label>
                                    <select class="form-select" id="filterPriceBranch">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-semibold text-secondary small mb-1"><i class="bi bi-diagram-3 me-1"></i> Level</label>
                                    <select class="form-select" id="filterPriceLevel">
                                        <option value="">All Levels</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-semibold text-secondary small mb-1"><i class="bi bi-toggle-on me-1"></i> Active Status</label>
                                    <select class="form-select" id="filterPriceActive">
                                        <option value="">All Status</option>
                                        <option value="1">Active Only</option>
                                        <option value="0">Inactive Only</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end pt-2">
                                    <button type="button" class="btn btn-outline-secondary me-2" id="btnResetPriceFilter">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                    </button>
                                    <button type="button" class="btn btn-success font-weight-bold" id="btnOpenAddPrice">
                                        <i class="bi bi-cash-stack me-1"></i> Set Product Price
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Prices DataTable -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="tbl-prices">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Branch</th>
                                        <th>Level</th>
                                        <th class="text-center">Size</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Status</th>
                                        <th>Description</th>
                                        <th class="text-center" style="width: 140px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalProduct" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form id="formProduct" autocomplete="off">
                    @csrf
                    <input type="hidden" id="productId" name="id">
                        <div class="modal-header bg-primary text-white py-3">
                            <h5 class="modal-title text-white fw-bold" id="modalProductLabel">
                                <i class="bi bi-box-seam me-2"></i> Master Product Form
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="productCode" class="form-label fw-semibold">Code<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-qr-code"></i></span>
                                    <input type="text" class="form-control text-uppercase" id="productCode" name="code" required>
                                    <button class="btn btn-outline-secondary" type="button" id="btnAutoCode" title="Generate SKU Code">Auto Code</button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="productName" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control" id="productName" name="name" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productUnitType" class="form-label fw-semibold">Unit Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="productUnitType" name="unit_type" required>
                                        <option value="pcs">Pcs (Pieces)</option>
                                        <option value="meter">Meter</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Size Options</label>
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" id="productHasSize" name="has_size" value="1">
                                        <label class="form-check-label fw-semibold text-secondary" for="productHasSize">Has Size Options?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary fw-bold px-4" id="btnSaveProduct">
                                <i class="bi bi-save me-1"></i> Save Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPrice" tabindex="-1" aria-labelledby="modalPriceLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="formPrice" autocomplete="off">
                @csrf
                <input type="hidden" id="priceId" name="id">
                    <div class="modal-header bg-success text-white py-3">
                        <h5 class="modal-title text-white fw-bold" id="modalPriceLabel">
                            <i class="bi bi-cash-stack me-2"></i> Set Product Price
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="priceProductId" class="form-label fw-semibold">Master Product <span class="text-danger">*</span></label>
                                <select class="form-select" id="priceProductId" name="product_id" required>
                                    <option value="">-- Select Master Product --</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}" data-hassize="{{ $prod->has_size }}" data-code="{{ $prod->code }}">{{ $prod->code }} - {{ $prod->name }} ({{ strtoupper($prod->unit_type) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="priceBranchId" class="form-label fw-semibold">Branch(es) <span class="text-danger">*</span></label>
                                <select class="form-select" id="priceBranchId" name="branch_id" data-placeholder="-- Select Branch(es) --" required>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priceLevelId" class="form-label fw-semibold">Level(s) <span class="text-danger">*</span></label>
                                <select class="form-select select2" style="width:100%" id="priceLevelId" name="level_id[]" multiple="multiple" data-placeholder="-- Select Level(s) --" required>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" id="sizeContainer" style="display: none;">
                                <label for="priceSize" class="form-label fw-semibold">Size Option(s)</label>
                                <select class="form-select" id="priceSize" name="size" data-placeholder="-- Select Size(s) --">
                                    <option value="XS">XS (Extra Small)</option>
                                    <option value="S">S (Small)</option>
                                    <option value="M">M (Medium)</option>
                                    <option value="L">L (Large)</option>
                                    <option value="XL">XL (Extra Large)</option>
                                    <option value="XXL">XXL (Double Extra Large)</option>
                                    <option value="XXXL">XXXL (Triple Extra Large)</option>
                                    <option value="OTHER">OTHER (Custom Size)</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priceAmount" class="form-label fw-semibold">Product Price (IDR) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text font-semibold">Rp</span>
                                    <input type="text" class="form-control number2 text-end font-extrabold fs-5" id="priceAmount" name="price" placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" id="priceIsActive" name="is_active" value="1" checked>
                                    <label class="form-check-label fw-semibold text-success" for="priceIsActive">Active Price Rule</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priceDescription" class="form-label fw-semibold">Note / Description</label>
                                <input type="text" class="form-control" id="priceDescription" name="description" placeholder="e.g., Standard price for Academic Year 2026/2027">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success fw-bold px-4" id="btnSavePrice">
                            <i class="bi bi-save me-1"></i> Save Price Rule
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
    <script src="/assets/extensions/sweetalert2/sweetalert2.all.min.js"></script>

    <script>
        let tblProducts, tblPrices;

        $(document).ready(function () {
            tblProducts = $('#tbl-products').DataTable({
                responsive: true,
                pagingType: 'simple_numbers',
                processing: true,
                serverSide: true,
                dom: `<"row mb-3"<"col-sm-6"l><"col-sm-6 text-end"f>>t<"row mt-3"<"col-sm-5"i><"col-sm-7"p>>`,
                language: {
                    search: '',
                    searchPlaceholder: 'Search products by code or name...',
                    lengthMenu: 'Show _MENU_ entries',
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                },
                ajax: {
                    url: "{{ route('uniform.products.datatables') }}",
                    type: 'GET',
                    data: function (d) {
                        d.unit_type = $('#filterProductUnit').val();
                        d.has_size = $('#filterProductHasSize').val();
                    }
                },
                columns: [
                    {
                        data: 'code',
                        name: 'code',
                        render: function (data) {
                            return `<span class="code-badge">${data || '-'}</span>`;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function (data) {
                            return `<span class="fw-bold text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'unit_type',
                        name: 'unit_type',
                        className: 'text-center',
                        render: function (data) {
                            if (data === 'meter') {
                                return `<span class="badge badge-unit-meter px-3 py-2 rounded-pill"><i class="bi bi-ruler me-1"></i> Meter</span>`;
                            }
                            return `<span class="badge badge-unit-pcs px-3 py-2 rounded-pill"><i class="bi bi-box me-1"></i> Pcs</span>`;
                        }
                    },
                    {
                        data: 'has_size',
                        name: 'has_size',
                        className: 'text-center',
                        render: function (data) {
                            if (data == 1) {
                                return `<span class="badge badge-size-yes px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Yes</span>`;
                            }
                            return `<span class="badge badge-size-no px-3 py-2 rounded-pill"><i class="bi bi-dash-circle me-1"></i> No</span>`;
                        }
                    },
                    {
                        data: 'price_count',
                        name: 'price_count',
                        className: 'text-center',
                        render: function (data) {
                            return `<span class="badge bg-light-primary text-primary fw-bold px-3 py-2">${data || 0} Rules</span>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function (data, type, row) {
                            return `
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-success btn-set-price me-1" data-id="${data}" data-name="${row.name}" data-code="${row.code}" data-size="${row.has_size}" title="Set Price For Product">
                                        <i class="bi bi-currency-dollar"></i> Set Price
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-product me-1" data-id="${data}" title="Edit Master Product">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-product" data-id="${data}" title="Delete Master Product">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[1, 'asc']]
            });

            // Master Product Filters
            $('#filterProductUnit, #filterProductHasSize').on('change', function () {
                tblProducts.ajax.reload();
            });

            $('#btnResetProductFilter').on('click', function () {
                $('#filterProductUnit').val('');
                $('#filterProductHasSize').val('');
                tblProducts.ajax.reload();
            });

            // Auto SKU generator helper
            $('#btnAutoCode').on('click', function () {
                const randomNum = Math.floor(1000 + Math.random() * 9000);
                $('#productCode').val(`UNIF-${randomNum}`);
            });

            // Open Modal Add Product
            $('#btnOpenAddProduct').on('click', function () {
                $('#formProduct')[0].reset();
                $('#productId').val('');
                $('#modalProductLabel').html('<i class="bi bi-box-seam me-2"></i> Add New Master Product');
                $('#modalProduct').modal('show');
            });

            // Submit Master Product Form
            $('#formProduct').on('submit', function (e) {
                e.preventDefault();
                const id = $('#productId').val();
                const payload = {
                    code: $('#productCode').val(),
                    name: $('#productName').val(),
                    unit_type: $('#productUnitType').val(),
                    has_size: $('#productHasSize').is(':checked') ? 1 : 0,
                };

                const url = id ? `/uniform/products/${id}` : '/uniform/products';
                const method = id ? 'PUT' : 'POST';

                blockUI();
                ajax(payload, url, method, function (res) {
                    toastify('success', res.message || 'Master Product saved successfully!');
                    $('#modalProduct').modal('hide');
                    tblProducts.ajax.reload(null, false);
                    reloadProductSelectOptions();
                    updateKPIStats();
                }, function (err) {
                    const msg = err?.responseJSON?.message || 'Failed to save product. Please check input.';
                    toastify('error', msg);
                });
            });

            // Edit Master Product
            $('#tbl-products').on('click', '.btn-edit-product', function () {
                const id = $(this).data('id');
                blockUI();
                $.get(`/uniform/products/${id}`, function (res) {
                    $('#productId').val(res.id);
                    $('#productCode').val(res.code);
                    $('#productName').val(res.name);
                    $('#productUnitType').val(res.unit_type);
                    $('#productHasSize').prop('checked', res.has_size == 1);
                    $('#modalProductLabel').html('<i class="bi bi-pencil-square me-2"></i> Edit Master Product');
                    $('#modalProduct').modal('show');
                }).fail(function () {
                    toastify('error', 'Could not load product details.');
                });
            });

            // Delete Master Product
            $('#tbl-products').on('click', '.btn-delete-product', function () {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this master product? All associated price rules will also be deleted.')) {
                    blockUI();
                    ajax({}, `/uniform/products/${id}`, 'DELETE', function (res) {
                        toastify('success', res.message || 'Product deleted successfully.');
                        tblProducts.ajax.reload(null, false);
                        tblPrices.ajax.reload(null, false);
                        reloadProductSelectOptions();
                        updateKPIStats();
                    }, function (err) {
                        toastify('error', err?.responseJSON?.message || 'Failed to delete product.');
                        tblProducts.ajax.reload(null, false);
                        tblPrices.ajax.reload(null, false);
                    });
                }
            });

            // Quick "Set Price" button from product table row
            $('#tbl-products').on('click', '.btn-set-price', function () {
                const prodId = $(this).data('id');
                openPriceModalForProduct(prodId);
            });

            tblPrices = $('#tbl-prices').DataTable({
                responsive: true,
                pagingType: 'simple_numbers',
                processing: true,
                serverSide: true,
                dom: `<"row mb-3"<"col-sm-6"l><"col-sm-6 text-end"f>>t<"row mt-3"<"col-sm-5"i><"col-sm-7"p>>`,
                language: {
                    search: '',
                    searchPlaceholder: 'Search price rules...',
                    lengthMenu: 'Show _MENU_ entries',
                    processing: '<div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>'
                },
                ajax: {
                    url: "{{ route('uniform.prices.datatables') }}",
                    type: 'GET',
                    data: function (d) {
                        d.product_id = $('#filterPriceProduct').val();
                        d.branch_id = $('#filterPriceBranch').val();
                        d.level_id = $('#filterPriceLevel').val();
                        d.is_active = $('#filterPriceActive').val();
                    }
                },
                columns: [
                    {
                        data: 'product_name',
                        name: 'product.name',
                        render: function (data, type, row) {
                            return `<div>
                                <span class="fw-bold text-dark">${data}</span>
                                <br><small class="code-badge">${row.product_code}</small>
                            </div>`;
                        }
                    },
                    {
                        data: 'branch_name',
                        name: 'branch.name',
                        render: function (data) {
                            return `<span class="badge bg-light-secondary text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'level_name',
                        name: 'level.name',
                        render: function (data) {
                            return `<span class="badge bg-light-info text-info fw-bold">${data}</span>`;
                        }
                    },
                    {
                        data: 'size',
                        name: 'size',
                        className: 'text-center',
                        render: function (data) {
                            if (!data) return `<span class="text-muted small">-</span>`;
                            return `<span class="badge bg-primary px-3 py-1">${data}</span>`;
                        }
                    },
                    {
                        data: 'price',
                        name: 'price',
                        className: 'text-end fw-extrabold text-success fs-6',
                        render: function (data,type,full) {
                            return 'Rp ' + formatNumber(data);
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        className: 'text-center',
                        render: function (data, type, row) {
                            const checked = data == 1 ? 'checked' : '';
                            return `
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input toggle-price-status" type="checkbox" ${checked} data-id="${row.id}">
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (data) {
                            return data ? `<span class="text-muted small">${data}</span>` : `<span class="text-muted small italic">-</span>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function (data) {
                            return `
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-price me-1" data-id="${data}" title="Edit Price Rule">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-price" data-id="${data}" title="Delete Price Rule">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[0, 'asc']]
            });

            // Price Filters
            $('#filterPriceProduct, #filterPriceBranch, #filterPriceLevel, #filterPriceActive').on('change', function () {
                tblPrices.ajax.reload();
            });

            // Dependent Branch -> Level Filter Dropdown
            $('#filterPriceBranch').on('change', function () {
                const branchId = $(this).val();
                $('#filterPriceLevel').html('<option value="">All Levels</option>');
                if (branchId) {
                    $.get(`/uniform/get-levels/${branchId}`, function (levels) {
                        levels.forEach(function (lvl) {
                            $('#filterPriceLevel').append(`<option value="${lvl.id}">${lvl.name}</option>`);
                        });
                    });
                }
            });

            $('#btnResetPriceFilter').on('click', function () {
                $('#filterPriceProduct').val('');
                $('#filterPriceBranch').val('');
                $('#filterPriceLevel').html('<option value="">All Levels</option>');
                $('#filterPriceActive').val('');
                tblPrices.ajax.reload();
            });

            // Open Modal Add Price
            $('#btnOpenAddPrice').on('click', function () {
                openPriceModalForProduct(null);
            });

            // Handle Product Selection Change in Price Modal to show/hide Size container
            $('#priceProductId').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const hasSize = selectedOption.data('hassize');
                if (hasSize == 1) {
                    $('#sizeContainer').slideDown(200);
                } else {
                    $('#sizeContainer').slideUp(200);
                    $('#priceSize').val('');
                }
            });

            // Initialize Select2 on modalPrice dropdowns
            $('#priceLevelId, #priceProductId').select2({
                dropdownParent: $('#modalPrice'),
                width: '100%'
            });

            // Dependent Branch -> Level in Price Modal (Supports Multiple Branch Selection)
            $('#priceBranchId').on('change', function () {
                const branchVals = $(this).val();
                const branchIds = Array.isArray(branchVals) ? branchVals : (branchVals ? [branchVals] : []);
                $('#priceLevelId').empty();
                if (branchIds.length > 0) {
                    let fetchPromises = branchIds.map(bId => $.get(`/uniform/get-levels/${bId}`));
                    blockUI()
                    Promise.all(fetchPromises).then(results => {
                        results.forEach(levels => {
                            levels.forEach(lvl => {
                                if ($(`#priceLevelId option[value="${lvl.id}"]`).length === 0) {
                                    $('#priceLevelId').append(`<option value="${lvl.id}">${lvl.name}</option>`);
                                }
                            });
                        });
                        $('#priceLevelId').trigger('change');
                    });
                } else {
                    $('#priceLevelId').trigger('change');
                }
            });

            // Submit Price Form
            $('#formPrice').on('submit', function (e) {
                e.preventDefault();
                const id = $('#priceId').val();
                const payload = {
                    product_id: $('#priceProductId').val(),
                    branch_id: $('#priceBranchId').val(),
                    level_id: $('#priceLevelId').val(),
                    size: $('#priceSize').val() || null,
                    price: $('#priceAmount').val(),
                    is_active: $('#priceIsActive').is(':checked') ? 1 : 0,
                    description: $('#priceDescription').val(),
                };

                const url = id ? `/uniform/prices/${id}` : '/uniform/prices';
                const method = id ? 'PUT' : 'POST';

                blockUI();
                ajax(payload, url, method, function (res) {
                    toastify('success', res.message || 'Price Rule saved successfully!');
                    $('#modalPrice').modal('hide');
                    tblPrices.ajax.reload(null, false);
                    tblProducts.ajax.reload(null, false);
                    updateKPIStats();
                }, function (err) {
                    const msg = err?.responseJSON?.message || 'Failed to save price rule.';
                    toastify('error', msg);
                });
            });

            // Toggle Price Status directly from switch
            $('#tbl-prices').on('change', '.toggle-price-status', function () {
                const id = $(this).data('id');
                blockUI();
                ajax({}, `/uniform/prices/${id}/toggle-active`, 'PUT', function (res) {
                    toastify('success', res.message);
                    updateKPIStats();
                }, function (err) {
                    toastify('error', 'Failed to change price status.');
                    tblPrices.ajax.reload(null, false);
                });
            });

            // Edit Price
            $('#tbl-prices').on('click', '.btn-edit-price', function () {
                const id = $(this).data('id');
                blockUI();
                $.get(`/uniform/prices/${id}`, function (res) {
                    $('#priceId').val(res.id);
                    $('#priceProductId').val(res.uniform_product_id || res.product_id).trigger('change');

                    $('#priceBranchId').val(res.branch_id).trigger('change');

                    // Load levels for branch, then select level
                    $.get(`/uniform/get-levels/${res.branch_id}`, function (levels) {
                        $('#priceLevelId').empty();
                        levels.forEach(function (lvl) {
                            $('#priceLevelId').append(`<option value="${lvl.id}">${lvl.name}</option>`);
                        });
                        $('#priceLevelId').val([res.level_id]).trigger('change');
                    });

                    if (res.size) {
                        $('#priceSize').val(res.size).trigger('change');
                    } else {
                        $('#priceSize').val().trigger('change');
                    }

                    $('#priceAmount').val(formatNumber(res.price));
                    $('#priceIsActive').prop('checked', res.is_active == 1);
                    $('#priceDescription').val(res.description || '');

                    $('#modalPriceLabel').html('<i class="bi bi-pencil-square me-2"></i> Edit Product Price Rule');
                    $('#modalPrice').modal('show');
                }).fail(function () {
                    toastify('error', 'Could not load price rule details.');
                });
            });

            // Delete Price Rule
            $('#tbl-prices').on('click', '.btn-delete-price', function () {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this price rule?')) {
                    blockUI();
                    ajax({}, `/uniform/prices/${id}`, 'DELETE', function (res) {
                        toastify('success', res.message || 'Price rule deleted.');
                        tblPrices.ajax.reload(null, false);
                        tblProducts.ajax.reload(null, false);
                        updateKPIStats();
                    }, function (err) {
                        toastify('error', err?.responseJSON?.message || 'Failed to delete price rule.');
                        tblPrices.ajax.reload(null, false);
                        updateKPIStats();
                    });
                }
            });
        });

        // Helper: Open Price Modal with optional product pre-selected
        function openPriceModalForProduct(productId = null) {
            $('#formPrice')[0].reset();
            $('#priceId').val('');
            $('#priceBranchId').val("").trigger('change');
            $('#priceLevelId').empty().val([]).trigger('change');
            $('#priceSize').val("").trigger('change');
            $('#sizeContainer').hide();
            $('#modalPriceLabel').html('<i class="bi bi-cash-stack me-2"></i> Set Product Price');

            if (productId) {
                $('#priceProductId').val(productId).trigger('change');
                // Switch to price tab if coming from product list
                const priceTab = new bootstrap.Tab(document.querySelector('#prices-tab'));
                priceTab.show();
            }

            $('#modalPrice').modal('show');
        }

        // Helper: Reload product dropdown in price modal and price filter
        function reloadProductSelectOptions() {
            $.get('/uniform/product', function (products) {
                let filterHtml = '<option value="">All Products</option>';
                let modalHtml = '<option value="">-- Select Master Product --</option>';

                products.forEach(function (p) {
                    filterHtml += `<option value="${p.id}">${p.code} - ${p.name}</option>`;
                    modalHtml += `<option value="${p.id}" data-hassize="${p.has_size}" data-code="${p.code}">${p.code} - ${p.name} (${p.unit_type.toUpperCase()})</option>`;
                });

                $('#filterPriceProduct').html(filterHtml);
                $('#priceProductId').html(modalHtml);
            });
        }

        // Helper: Dynamically refresh KPI Stat cards
        function updateKPIStats() {
            $.get('/uniform/product', function (products) {
                $('#stat-total-products').text(products.length);
                let hasSizeCount = 0;
                products.forEach(p => { if (p.has_size == 1) hasSizeCount++; });
                $('#stat-has-size').text(hasSizeCount);
            });
        }

        // Format number with commas
        function formatNumber(num) {
            return num.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endsection
