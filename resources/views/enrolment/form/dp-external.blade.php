<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>MHIS Development Fee</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/assets/static/css/enrolment-external.css?v=1.0.2">
    <style>
        .dp-page { --dp-accent: #800000; --dp-soft: #fbf3f3; --dp-border: #e8e4e0; }
        .dp-section { border: 1px solid var(--dp-border); border-radius: .85rem; }
        .dp-choice { display: block; height: 100%; padding: 1rem; border: 1px solid var(--dp-border);
            border-radius: .75rem; cursor: pointer; transition: .15s ease; }
        .dp-choice:has(input:checked) { border-color: var(--dp-accent); background: var(--dp-soft);
            box-shadow: 0 0 0 2px rgba(128, 0, 0, .1); }
        .dp-choice input { accent-color: var(--dp-accent); }
        .dp-summary { border: 1px solid var(--dp-border); border-radius: .85rem; }
        @media (min-width: 992px) { .dp-summary { position: sticky; top: 1rem; } }
        .dp-total { color: var(--dp-accent); font-size: 1.35rem; font-weight: 700; }
        .dp-page .text-success { color: var(--dp-accent) !important; }
        .dp-page .btn-success, .dp-page .btn-success:disabled { background: var(--dp-accent); border-color: var(--dp-accent); }
    </style>
</head>

<body>
    <div class="enrollment-wrapper">
        <div class="form-container">
            <div class="form-header">
                <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo" class="header-logo"
                    onerror="this.style.display='none';" />
                <h2 class="text-white">MHIS Development Fee</h2>
                <p>Pay the development fee and registration fee online.</p>
            </div>
            <div class="form-content dp-page">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-1"></i>Payment could not be created.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($transaction)
                    <div class="success-message mb-4">
                        <div class="success-icon"><i class="fas fa-check"></i></div>
                        <h2>Payment Created Successfully!</h2>
                        <p>
                            Thank you. The payment details have been sent to
                            <strong>{{ optional($transaction->enrolment)->email }}</strong>.
                            Please complete the payment before
                            <strong>{{ optional($transaction->expiry_va_date)->format('d M Y H:i') ?? '-' }}</strong>.
                        </p>
                        <p class="mb-1">Enrolment Code: <strong>{{ optional($transaction->enrolment)->code }}</strong></p>
                        <p>Total: <strong>Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}</strong></p>
                        @if ($transaction->payment_url)
                            <a href="{{ $transaction->payment_url }}" class="btn-custom btn-next" style="margin: 20px auto 0; text-decoration: none; display: inline-flex;">
                                <i class="fas fa-credit-card"></i>&nbsp;Proceed to Payment
                            </a>
                        @endif
                        <a href="{{ route('enrolment.dp-public.index') }}" class="btn-custom btn-prev" style="margin: 10px auto 0; text-decoration: none; display: inline-flex;">
                            <i class="fas fa-plus"></i>&nbsp;Create another payment
                        </a>
                    </div>
                @else
                    @php
                        $selectedMode = old('already_enrolment', filled($prefillCode) ? 'yes' : null);
                    @endphp
                    @include('enrolment._dp_form', ['formAction' => route('enrolment.dp-public.store'), 'public' => true])
                @endif
            </div>
        </div>
    </div>
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    @unless ($transaction)
        @include('enrolment._dp_script', [
            'gradesUrl' => url('/enrolment/dp-form/grades') . '/',
            'discountUrl' => route('enrolment.dp-public.discount'),
            'searchUrl' => route('enrolment.dp-public.search'),
        ])
    @endunless
</body>

</html>
