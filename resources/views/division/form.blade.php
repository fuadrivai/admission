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
                            <label for="name" class="form-label required-label">Division</label>
                            <input type="text" class="d-none" id="id" name="id"
                                value="{{ isset($division) ? $division->id : '' }}">
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="name" name="name" required
                                        placeholder="Enter division" value="{{ isset($division) ? $division->name : '' }}">
                                    <div class="form-control-icon">
                                        <i class="fa fa-sitemap"></i>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12"><button id="btn-add-level" class="btn btn-danger"> Add Level</button></div>
                </div>
                <div class="row">
                    <div class="table-responsive datatable-minimal">
                        <table class="table" id="tbl-level">
                            <thead>
                                <tr class="text-center">
                                    <th>Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center"><button onclick="saveDivision()" id="btn-save"
                            class="btn btn-primary"><i class="fa fa-save"></i> Save division</button></div>
                </div>
            </div>
    </section>
@endsection


@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let divisionId = $('#id').val();
        let levels = [];
        let division = {
            name: '',
            levels: []
        }
        $(document).ready(function() {
            tblLevel = $('#tbl-level').DataTable({
                paging: false,
                searching: false,
                length: false,
                info: false,
                data: division.levels,
                ordering: false,
                columns: [{
                        data: 'name',
                        mRender: function(data, type, row) {
                            '';
                            return `<select class="form-control select-level">
                                </select>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<button class="btn btn-danger btn-sm btn-delete-level"><i class="fa fa-trash"></i></button>`;
                        }
                    }
                ],
                drawCallback: function(settings, json) {
                    let api = this.api();
                    api.rows().every(function() {
                        let row = this.data();
                        let select = $(this.node()).find('.select-level');
                        select.empty();
                        select.append(
                            `<option selected disabled value="">--select level--</option>`)
                        levels.forEach(function(lvl) {
                            let _selected = (lvl.id === row.id) ? 'selected' : '';
                            select.append(
                                `<option value="${lvl.id}" ${_selected}>${lvl.name}</option>`
                            );
                        });
                    });
                }
            });

            $('#btn-add-level').on('click', function() {
                division.levels.push({
                    name: null,
                });
                reloadJsonDataTable(tblLevel, division.levels);
            });

            $('#tbl-level').on('change', '.select-level', function() {
                let data = tblLevel.row($(this).parents('tr')).data();
                let id = $(this).val()
                let level = levels.find(val => val.id === parseInt(id))
                division.levels.push(level);
            });

            $('#tbl-level').on('click', '.btn-delete-level', function() {
                let data = tblLevel.row($(this).parents('tr')).index();
                division.levels.splice(data, 1);
                reloadJsonDataTable(tblLevel, division.levels);
            });

            if (divisionId != '') {
                getDivision(divisionId);
            }
            getLevel();
        });


        function getLevel() {
            ajax(null, `/level/get`, 'GET', function(json) {
                levels = json;
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
            });
        }

        function getDivision(id) {
            ajax(null, `/division/${id}`, 'GET', function(json) {
                division = json;
                reloadJsonDataTable(tblLevel, division.levels);
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
            });
        }

        function saveDivision() {
            division.name = $('#name').val()
            if (division.name == '') {
                toastify("Error", "Name of division is required");
                return false;
            }
            if (division.levels.length == 0) {
                toastify("Error", "insert at least one level");
                return false;
            }
            division.levels.forEach(e => {
                if (e.name == null || e.name == '') {
                    toastify("Error", "Name of level is required");
                    return false;
                }
            });
            let method = "POST";
            let url = '/division';
            if (divisionId != '') {
                method = "PUT";
                url = `/division/${divisionId}`
            }
            ajax(division, url, method, function(json) {
                toastify("success", "division saved successfully");
                setTimeout(() => {
                    window.location.href = "/division";
                }, 1500);
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later",
                    "error");
            });
        }
    </script>
@endsection
