@extends('main-layout.index')
@section('content-style')
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <form action="/setting/password/change" method="POST" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12 mb-6">
                            <label for="old_password" class="form-label required-label">Current password</label>
                            <input type="password" class="form-control form-control-xl" name="old_password" required
                                placeholder="Enter Current Password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-12 mb-6">
                            <label for="new_password" class="form-label required-label">New password</label>
                            <input type="password" class="form-control form-control-xl" name="new_password" required
                                placeholder="Enter New Password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <label for="new_password_confirmation" class="form-label required-label">Confirm
                                password</label>
                            <input type="password" class="form-control form-control-xl" name="new_password_confirmation"
                                required placeholder="Enter again">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-6">
                            <div class="form-check form-check-lg d-flex align-items-end">
                                <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                    show password
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-12">
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <small class="text-danger">{{ $error }}</small>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-12 text-center">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
                        </div>
                    </div>
                </form>
            </div>
    </section>
@endsection


@section('content-script')
    <script>
        $(document).ready(function() {
            $('.form-check-input').on('change', function() {
                let val = $(this).prop('checked')
                $('.form-control-xl').attr('type', val ? "text" : "password");
            })
        })
    </script>
@endsection
