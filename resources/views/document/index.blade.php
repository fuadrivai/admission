@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="/assets/static/css/enrolment.css?v=1.0.0">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <p class="d-inline-flex gap-1">
                    <a data-bs-toggle="collapse" href="#collapse-filter" aria-expanded="false"
                        aria-controls="collapse-filter">
                        Insert Filter <i class="fa fa-caret-down"></i>
                    </a>
                </p>
                <div class="collapse" id="collapse-filter">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter-name">Search</label>
                                <input placeholder="code, applicant name, document type" type="text" class="form-control"
                                    id="filter-name" name="filter-name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter-branch">Branch</label>
                                <select id="filter-branch" name="filter-branch" class="form-select" style="width: 100%">
                                    <option value="all">All Branches</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter-level">Level</label>
                                <select id="filter-level" disabled name="filter-level" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All Levels</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter-grade">Grade</label>
                                <select id="filter-grade" disabled name="filter-grade" class="form-select"
                                    style="width: 100%">
                                    <option value="all">All Grades</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="document-list">
            @include('document._list')
        </div>
    </section>
@endsection


@section('content-script')
    <script src="/assets/extensions/moment/moment.js"></script>
    <script>
        let branches = [];
        let levels = [];
        let typingTimer;
        $(document).ready(function() {
            getBranch()
            $('#filter-branch').on('change', function() {
                let branchVal = $(this).val();
                if (branchVal == "all") {
                    $('#filter-level').attr('disabled', true);
                    $('#filter-level').val('all').trigger('change');
                    $('#filter-grade').attr('disabled', true);
                    $('#filter-grade').val('all').trigger('change');
                    return;
                }
                $('#filter-level').attr('disabled', false);
                const branch = branches.find((b) => b.id == branchVal);
                $("#filter-level").empty();
                $("#filter-level").append(`
                    <option value="all">All Levels</option>  
                `);
                levels = branch.levels;
                branch.levels.forEach((val) => {
                    $("#filter-level").append(`
                        <option value="${val.id}">${val.name}</option>    
                    `);
                });
                $("#filter-level").val("all").trigger('change');
            })

            $("#filter-level").on("change", function() {
                let levelVal = $(this).val()
                if (levelVal == "all") {
                    $("#filter-grade").attr("disabled", true);
                    $("#filter-grade").val("all").trigger('change');
                    return;
                }

                $("#filter-grade").attr("disabled", false);

                let levelId = $(this).val();
                const level = levels.find((l) => l.id == levelId);
                $("#filter-grade").empty();
                $("#filter-grade").append(`
                    <option value="all">All grades</option>  
                `);
                level.grades.forEach((val) => {
                    $("#filter-grade").append(`
                        <option value="${val.id}">${val.name}</option>    
                    `);
                });
                $("#filter-grade").val("all").trigger('change');
            });

            $('#filter-name').on('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    loadDocuments()
                }, 400);
            });

            $('#filter-level, #filter-branch, #filter-grade').on('change', function() {
                loadDocuments();
            });

            $(document).on('click', '#document-list .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadDocuments(url);
            });
        });

        function getBranch() {
            blockUI();
            ajax(
                null,
                `/branch/get`,
                "GET",
                function(json) {
                    branches = json;
                    branches.forEach((val) => {
                        $("#filter-branch").append(`
                            <option value="${val.id}">${val.name}</option>
                        `);
                    });
                },
                function(err) {
                    toastify(
                        "Error",
                        err?.responseJSON?.message ?? "Please try again later",
                        "error"
                    );
                }
            );
        }

        function loadDocuments(url = "{{ url('/applicant') }}") {
            const data = {
                search: $('#filter-name').val(),
                branch: $('#filter-branch').val(),
                level: $('#filter-level').val(),
                grade: $('#filter-grade').val(),
            };

            $.ajax({
                url: url,
                data: data,
                type: "GET",
                success: function(html) {
                    $('#document-list').html(html);
                }
            });
        }
    </script>
@endsection
