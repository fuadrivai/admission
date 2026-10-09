@extends('main-layout.index')

@section('content-style')
    <style>
        .dp-page {
            --dp-accent: #176b5b;
            --dp-soft: #eef8f4;
            --dp-border: #dce8e3;
        }

        .dp-hero {
            background: linear-gradient(125deg, #f0faf5, #f1f7ff);
            border: 1px solid var(--dp-border);
        }

        .dp-section {
            border: 1px solid var(--dp-border);
            border-radius: .85rem;
        }

        .dp-choice {
            display: block;
            height: 100%;
            padding: 1rem;
            border: 1px solid var(--dp-border);
            border-radius: .75rem;
            cursor: pointer;
            transition: .15s ease;
        }

        .dp-choice:has(input:checked) {
            border-color: var(--dp-accent);
            background: var(--dp-soft);
            box-shadow: 0 0 0 2px rgba(23, 107, 91, .1);
        }

        .dp-choice input {
            accent-color: var(--dp-accent);
        }

        .dp-summary {
            position: sticky;
            top: 1rem;
            border: 1px solid var(--dp-border);
            border-radius: .85rem;
        }

        .dp-total {
            color: var(--dp-accent);
            font-size: 1.35rem;
            font-weight: 700;
        }
    </style>
@endsection

@section('content-child')
    @php
        $selectedMode = old('already_enrolment', filled($prefillCode) ? 'yes' : null);
    @endphp
    <section class="section dp-page">
        <div class="card dp-hero mb-4">
            <div class="card-body d-flex gap-3 align-items-center">
                <span class="avatar avatar-lg bg-success text-white"><i class="bi bi-cash-coin fs-4"></i></span>
                <div>
                    <h3 class="mb-1">Development Fee</h3>
                    <p class="text-muted mb-0">Create a payment transaction for an enrolment.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong><i class="bi bi-exclamation-triangle me-1"></i>Payment could not be created.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($transaction)
            <div class="card border-success mb-4">
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        <div class="flex-grow-1">
                            <h4>Payment Created Successfully</h4>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6"><span class="text-muted">Enrolment Code</span><br>
                                    <strong>{{ optional($transaction->enrolment)->code ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6"><span class="text-muted">Transaction</span><br>
                                    <strong>{{ $transaction->code }}</strong>
                                </div>
                                <div class="col-md-6"><span class="text-muted">Invoice</span><br>
                                    <strong>{{ $transaction->invoice_id ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6"><span class="text-muted">Student</span><br>
                                    <strong>{{ optional($transaction->enrolment)->child_name ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6"><span class="text-muted">Total</span><br>
                                    <strong>Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}</strong>
                                </div>
                                <div class="col-md-6"><span class="text-muted">Payment Status</span><br>
                                    <span class="badge {{ strtoupper($transaction->payment_status) === 'PAID' ? 'bg-success' : (strtoupper($transaction->payment_status) === 'EXPIRED' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ strtoupper($transaction->payment_status) }}
                                    </span>
                                </div>
                                <div class="col-md-6"><span class="text-muted">Payment Link</span><br>
                                    @if ($transaction->payment_url)
                                        <a href="{{ $transaction->payment_url }}" target="_blank" rel="noopener noreferrer">Open Xendit payment</a>
                                    @else
                                        <strong>-</strong>
                                    @endif
                                </div>
                                <div class="col-md-6"><span class="text-muted">Payment Link Created</span><br>
                                    <strong>{{ optional($transaction->create_va_date)->format('d M Y H:i') ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6"><span class="text-muted">Payment Link Expires</span><br>
                                    <strong>{{ optional($transaction->expiry_va_date)->format('d M Y H:i') ?? '-' }}</strong>
                                </div>
                            </div>
                            <a href="{{ route('enrolment.dp.index') }}" class="btn btn-outline-primary mt-3">
                                <i class="bi bi-plus-circle me-1"></i>Create another payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @include('enrolment._dp_form', ['formAction' => route('enrolment.dp.store'), 'public' => false])
    </section>
@endsection

@section('content-script')
    @include('enrolment._dp_script', ['gradesUrl' => '/uniform/get-grades/', 'discountUrl' => route('enrolment.dp.discount'), 'searchUrl' => route('enrolment.dp.search')])
@endsection
