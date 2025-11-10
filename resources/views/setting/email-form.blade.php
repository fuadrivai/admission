@extends('main-layout.index')
@section('content-style')
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <form id="form-email" action="/setting/form/email" method="POST" autocomplete="off" class="needs-validation">
                    @csrf
                    @isset($email)
                        @method('PUT')
                    @endisset

                    <div class="row">
                        <div class="col-md-6">
                            <label for="from_name" class="form-label required-label">Email Name</label>
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <input type="hidden" value="{{ isset($email) ? $email->id : '' }}" id="email_id"
                                        name="email_id">
                                    <input type="text" value="{{ isset($email) ? $email->from_name : '' }}"
                                        class="form-control" id="from_name" name="from_name" required
                                        placeholder="Enter Email Name" value="">
                                    <div class="form-control-icon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="branch" class="form-label required-label">Branch</label>
                            <div class="form-group">
                                <select name="branch" required id="branch" class="form-select">
                                    <option value="" disabled selected> -- select branch --</option>
                                    @foreach ($branches as $branch)
                                        <option
                                            {{ isset($email) ? ($email->branch->id == $branch->id ? 'selected' : '') : '' }}
                                            value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label required-label">Username</label>
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <input type="text" value="{{ isset($email) ? $email->username : '' }}"
                                        class="form-control" id="username" name="username" required
                                        placeholder="Enter username" value="">
                                    <div class="form-control-icon">
                                        <i class="fa fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="from_address" class="form-label required-label">From Address</label>
                            <div class="form-group has-icon-left">
                                <div class="position-relative">
                                    <input type="text" value="{{ isset($email) ? $email->from_address : '' }}"
                                        class="form-control" id="from_address" name="from_address" required
                                        placeholder="Enter from address" value="">
                                    <div class="form-control-icon">
                                        <i class="fa fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label required-label">Password*</label>
                            <input type="text" value="{{ isset($email) ? $email->app_password : '' }}" required
                                class="form-control" name="password" id="password">
                        </div>
                        <div class="col-md-6">
                            <label for="host" class="form-label required-label">Host*</label>
                            <input type="text" value="{{ isset($email) ? $email->host : '' }}" class="form-control"
                                name="host" id="host">
                        </div>
                        <div class="col-md-4">
                            <label for="mailer" class="form-label required-label">Mailer*</label>
                            <input type="text" value="{{ isset($email) ? $email->mailer : '' }}" class="form-control"
                                name="mailer" id="mailer">
                        </div>
                        <div class="col-md-4">
                            <label for="port" class="form-label required-label">Port*</label>
                            <input type="text" value="{{ isset($email) ? $email->port : '' }}" class="form-control"
                                name="port" id="port">
                        </div>
                        <div class="col-md-4">
                            <label for="encryption" class="form-label required-label">Encryption*</label>
                            <input type="text" value="{{ isset($email) ? $email->encryption : '' }}"
                                class="form-control" name="encryption" id="encryption">
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-4 text-center">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i>
                                Submit</button>
                        </div>
                    </div>
                </form>
            </div>
    </section>
@endsection


@section('content-script')
@endsection
