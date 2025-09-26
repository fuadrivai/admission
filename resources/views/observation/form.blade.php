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
                    <div class="col-md-6 mb-4">
                        <label for="date" class="form-label required-label">Observation date</label>
                        <input type="text" class="d-none" id="observation-date"
                            value="{{ isset($observationDate) ? $observationDate->id : '' }}">
                        <div class="form-group has-icon-left">
                            <div class="position-relative">
                                <input type="text" class="form-control date-picker"id="date" readonly required
                                    placeholder="Enter observation date"
                                    value="{{ isset($observationDate) ? date('d F Y', strtotime($observationDate->date)) : '' }}">
                                <div class="form-control-icon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="date" class="form-label required-label">Division</label>
                        <select name="division" id="division" class="choices form-select">
                            <option selected disabled value="">-- Choose division --</option>
                            @foreach ($divisions as $div)
                                <option
                                    {{ isset($observationDate) ? ($div->id == optional($observationDate->division)->id ? 'selected' : '') : '' }}
                                    value="{{ $div->id }}"> {{ $div->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12"><button id="btn-add-time" class="btn btn-danger">Add
                            Time</button></div>
                </div>
                <div class="row">
                    <div class="table-responsive datatable-minimal">
                        <table class="table" id="tbl-time">
                            <thead>
                                <tr class="text-center">
                                    <th>Start time</th>
                                    <th>End time</th>
                                    <th>Max quota</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center"><button id="btn-save" onclick="saveObservationDate()"
                            class="btn btn-primary"><i class="fa fa-save"></i> Save
                            observation</button></div>
                </div>
            </div>
    </section>
@endsection


@section('content-script')
    <script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let ObservationDateId = $('#observation-date').val();
        let observationDate = {
            date: '',
            times: []
        }
        $(document).ready(function() {
            tblTime = $('#tbl-time').DataTable({
                paging: false,
                searching: false,
                length: false,
                info: false,
                data: observationDate.times,
                ordering: false,
                columns: [{
                        data: 'time',
                        className: 'text-center',
                        mRender: function(data, type, row) {
                            let formattedTime = data ? moment(data, 'HH:mm:ss').format('HH:mm') :
                                '';
                            return ` <input type="text" class="form-control time-picker start-time text-center" value="${data?formattedTime:""}" required
                            placeholder="HH:mm">`;
                        }
                    },
                    {
                        data: 'end_time',
                        className: 'text-center',
                        mRender: function(data, type, row) {
                            let formattedTime = data ? moment(data, 'HH:mm:ss').format('HH:mm') :
                                '';
                            return ` <input type="text" class="form-control time-picker end-time text-center" value="${data?formattedTime:""}" required
                            placeholder="HH:mm">`;
                        }
                    },
                    {
                        data: 'max_quota',
                        className: 'text-center',
                        mRender: function(data, type, row) {
                            return ` <input type="number" style="text-align: right;" class="form-control max_quota text-right" value="${data}" required
                            placeholder="Enter quota">`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<button class="btn btn-danger btn-sm btn-delete-time" data-time="${data.time}"><i class="fa fa-trash"></i></button>`;
                        }
                    }
                ],
            });

            $('#btn-add-time').on('click', function() {
                observationDate.times.push({
                    time: null,
                    max_quota: 0
                });
                reloadJsonDataTable(tblTime, observationDate.times);
            });

            $('#tbl-time').on('change', '.start-time', function() {
                let time = tblTime.row($(this).parents('tr')).data();
                time.time = $(this).val();
                reloadJsonDataTable(tblTime, observationDate.times);
            });
            $('#tbl-time').on('change', '.end-time', function() {
                let time = tblTime.row($(this).parents('tr')).data();
                time.end_time = $(this).val();
                reloadJsonDataTable(tblTime, observationDate.times);
            });

            $('#tbl-time').on('change', '.max_quota', function() {
                let time = tblTime.row($(this).parents('tr')).data();
                time.max_quota = $(this).val();
                reloadJsonDataTable(tblTime, observationDate.times);
            });
            $('#tbl-time').on('click', '.btn-delete-time', function() {
                let data = tblTime.row($(this).parents('tr')).index();
                observationDate.times.splice(data, 1);
                reloadJsonDataTable(tblTime, observationDate.times);
            });

            if (ObservationDateId != '') {
                getObservationDate(ObservationDateId);
            }
        });

        function getObservationDate(id) {
            ajax(null, `/observation/date/${id}`, 'GET', function(json) {
                observationDate = json;
                reloadJsonDataTable(tblTime, observationDate.times);
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later", "error");
            });
        }

        function saveObservationDate() {
            let date = $('#date').val()
            if (date == '') {
                toastify("Error", "Date is required");
                return false;
            }
            if (observationDate.times.length == 0) {
                toastify("Error", "insert at least one time");
                return false;
            }
            let momentDate = moment(date, "DD MMMM YYYY");
            let today = moment();

            if (momentDate.isBefore(today, 'day')) {
                toastify("Error", "Set observation date for Previous date is not allow");
                return false;
            }
            observationDate.times.forEach(e => {
                if (e.time == null || e.time == '') {
                    toastify("Error", "Time is required");
                    return false;
                }
                if (e.max_quota == null || e.max_quota == '') {
                    toastify("Error", "Max quota is required");
                    return false;
                }
            });
            observationDate.date = moment($('#date').val(), 'DD MMMM YYYY').format('YYYY-MM-DD');
            observationDate.type = 1;
            observationDate.division = $('#division').val();
            let method = "POST";
            let url = '/observation/date';
            if (ObservationDateId != '') {
                method = "PUT";
                url = `/observation/date/${ObservationDateId}`
            }
            ajax(observationDate, url, method, function(json) {
                toastify("success", "Observation date saved successfully");
                setTimeout(() => {
                    window.location.href = "/observation/setting";
                }, 1500);
            }, function(err) {
                toastify("Error", err?.responseJSON?.message ?? "Please try again later",
                    "error");
            });
        }
    </script>
@endsection
