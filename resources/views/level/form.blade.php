@extends('main-layout.index')
@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-6">
                        <form autocomplete="off">
                            <label for="date" class="form-label required-label">Level</label>
                            <input type="text" class="d-none" id="id" name="id"
                                value="{{ isset($level) ? $level->id : '' }}">
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="name" name="name" required
                                        placeholder="Enter level" value="{{ isset($level) ? $level->name : '' }}">
                                    <div class="form-control-icon">
                                        <i class="fa fa-graduation-cap"></i>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12"><button id="btn-add-grade" class="btn btn-danger"> Add Grade</button></div>
                </div>
                <div class="row">
                    <div class="table-responsive datatable-minimal">
                        <table class="table" id="tbl-grade">
                            <thead>
                                <tr class="text-center">
                                    <th>Grade</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center"><button onclick="saveLevel()" id="btn-save" class="btn btn-primary"><i
                                class="fa fa-save"></i> Save Level</button></div>
                </div>
            </div>
    </section>
@endsection


@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let levelId = $('#id').val();
        let level = {
            name: '',
            grades: []
        }
        $(document).ready(function() {
            tblGrade = $('#tbl-grade').DataTable({
                paging: false,
                searching: false,
                length: false,
                info: false,
                data: level.grades,
                ordering: false,
                columns: [{
                        data: 'name',
                        mRender: function(data, type, row) {
                            '';
                            return ` <input type="text" class="form-control text-center grade-name" value="${data?data:""}" required
                            placeholder="Grade">`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<button class="btn btn-danger btn-sm btn-delete-grade"><i class="fa fa-trash"></i></button>`;
                        }
                    }
                ]
            });

            $('#btn-add-grade').on('click', function() {
                level.grades.push({
                    name: null,
                });
                reloadJsonDataTable(tblGrade, level.grades);
            });

            $('#tbl-grade').on('change', '.grade-name', function() {
                let data = tblGrade.row($(this).parents('tr')).data();
                data.name = $(this).val();
                reloadJsonDataTable(tblGrade, level.grades);
            });

            $('#tbl-grade').on('click', '.btn-delete-grade', function() {
                let data = tblGrade.row($(this).parents('tr')).index();
                level.grades.splice(data, 1);
                reloadJsonDataTable(tblGrade, level.grades);
            });

            if (levelId != '') {
                getLevel(levelId);
            }
        });

        function getLevel(id) {
            ajax(null, `/level/${id}`, 'GET', function(json) {
                level = json;
                reloadJsonDataTable(tblGrade, level.grades);
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
            });
        }

        function saveLevel() {
            level.name = $('#name').val()
            if (level.name == '') {
                toastify("Error", "Name of level is required");
                return false;
            }
            if (level.grades.length == 0) {
                toastify("Error", "insert at least one grade");
                return false;
            }
            level.grades.forEach(e => {
                if (e.name == null || e.name == '') {
                    toastify("Error", "Name of grade is required");
                    return false;
                }
            });
            let method = "POST";
            let url = '/level';
            if (levelId != '') {
                method = "PUT";
                url = `/level/${levelId}`
            }
            ajax(level, url, method, function(json) {
                toastify("success", "Level saved successfully");
                setTimeout(() => {
                    window.location.href = "/level";
                }, 1500);
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later",
                    "error");
            });
        }
    </script>
@endsection
