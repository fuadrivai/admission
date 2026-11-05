@extends('main-layout.index')

@section('content-style')
    <link href="/assets/extensions/summernote/summernote-bs5.min.css" rel="stylesheet">
@endsection

@section('content-child')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $template->name }}</h5>
            <a href="{{ route('merchandise.email-templates') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('merchandise.email-templates.update', $template->key) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="subject">Email subject</label>
                    <input id="subject" name="subject" class="form-control" maxlength="255" required
                        value="{{ old('subject', $template->subject) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="body">Email body (HTML)</label>
                    <textarea id="body" name="body" class="summernote" required>{{ old('body', $template->body) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Available variables</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ([
                            'parent_name',
                            'order_code',
                            'order_date',
                            'branch_name',
                            'portal_username',
                            'email',
                            'phone',
                            'order_items',
                            'payment_table',
                            'subtotal',
                            'discount',
                            'admin_fee',
                            'total',
                            'payment_url',
                            'expires_at',
                            'payment_date',
                            'payment_status',
                        ] as $variable)
                            @php($placeholder = '{' . '{' . $variable . '}' . '}')
                            <code>{{ $placeholder }}</code>
                        @endforeach
                    </div>
                    <small class="form-text text-muted">
                        @php($orderItemsPlaceholder = '{' . '{' . 'order_items' . '}' . '}')
                        @php($paymentTablePlaceholder = '{' . '{' . 'payment_table' . '}' . '}')
                        <code>{{ $paymentTablePlaceholder }}</code> inserts a mobile-friendly table containing all products, discounts, admin fee, and total.
                        <code>{{ $orderItemsPlaceholder }}</code> inserts only the product rows.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">Save Template</button>
                <a href="{{ route('merchandise.email-templates') }}" class="btn btn-light-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@section('content-script')
    <script src="/assets/extensions/summernote/summernote-bs5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 330,
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
        });
    </script>
@endsection
