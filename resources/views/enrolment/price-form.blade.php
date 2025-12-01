@extends('main-layout.index')
@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <form autocomplete="off" action="{{ isset($price) ? '/price/' . $price->id : '/price' }}" method="POST">
                    @csrf
                    @if (isset($price))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <div class="form-group">
                                <label for="name" class="form-label required-label">Enrolment Price Name</label>
                                <input type="text" class="d-none" id="id" name="id"
                                    value="{{ isset($price) ? $price->id : '' }}">
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="Enter enrolment price" value="{{ isset($price) ? $price->name : '' }}">
                            </div>

                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="form-group">
                                <label for="price" class="form-label required-label">Price</label>
                                <input type="text" class="form-control number2" id="price" name="price" required
                                    placeholder="Enter price"
                                    value="{{ isset($price) ? number_format($price->price, 0, ',', ',') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-4 mb-6">
                            <label for="branch" class="form-label required-label">Branch</label>
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <select name="branch" id="branch" class="choices form-select">
                                        <option selected disabled value="">Select branch</option>
                                        @foreach ($branches as $div)
                                            <option
                                                {{ isset($price) ? ($div->id == optional($price->branch)->id ? 'selected' : '') : '' }}
                                                value="{{ $div->id }}"> {{ $div->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-6">
                            <div class="form-group">
                                <label for="level" class="form-label required-label">Level</label>
                                <select disabled name="level" id="level" class="form-select">
                                    <option selected disabled value="">Select level</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mb-6">
                            <label for="type" class="form-label required-label">Type</label>
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <select name="type" id="type" class="choices form-select">
                                        <option selected disabled value="">Select type</option>
                                        <option {{ isset($price) ? ($price->type == 'form' ? 'selected' : '') : '' }}
                                            value="form">Form</option>
                                        <option {{ isset($price) ? ($price->type == 'fee' ? 'selected' : '') : '' }}
                                            value="fee">Enrolment Fee</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center"><button type ="submit" class="btn btn-primary"><i
                                    class="fa fa-save"></i> Save Price</button></div>
                    </div>
                </form>

            </div>
        </div>
    </section>
@endsection
@section('content-script')
    <script>
        $(document).ready(function() {
            $('#branch').on('change', function() {
                let id = $(this).val();
                getLevelByBranch(id);
            })
        })

        function getLevelByBranch(id) {
            blockUI();
            ajax(null, `/level/branch/${id}`, 'GET', function(json) {
                $("#level").attr("disabled", false);
                $("#level")
                    .empty()
                    .append('<option value="">Select level...</option>');
                levels = json;
                levels.forEach((level) => {
                    $("#level").append(
                        `<option value="${level.id}">${level.name}</option>`
                    );
                });
            }, function(err) {
                console.log(err)
            })
        }
    </script>
@endsection
