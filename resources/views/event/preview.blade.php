@extends('main-layout.index')

@section('content-style')
    <style>
        .preview-wrap {
            max-width: 820px;
            margin: 0 auto;
        }

        .preview-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }

        .preview-header {
            margin-bottom: 1.75rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eef2f7;
        }

        .preview-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
        }

        .preview-section {
            margin-top: 1.5rem;
        }

        .preview-section h5 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-label {
            display: inline-block;
            font-weight: 600;
            margin-bottom: 0.45rem;
            color: #374151;
        }

        .preview-field input,
        .preview-field textarea,
        .preview-field select {
            background-color: #f8fafc;
        }

        .preview-options .form-check {
            margin-bottom: 0.55rem;
        }

        .preview-price .form-check {
            margin-bottom: 0.9rem;
            padding: 0.8rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f8fafc;
        }

        .preview-price .form-check-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            width: 100%;
        }

        .price-amount {
            font-weight: 600;
            color: #111827;
        }

        .text-muted-small {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .preview-actions {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eef2f7;
        }
    </style>
@endsection

@section('content-script')
    <script>
        $(function() {
            function syncPreviewOther(fieldKey) {
                const $otherToggle = $('[data-preview-other-toggle]').filter(function() {
                    return $(this).data('preview-other-toggle') === fieldKey && $(this).is(':checked');
                });
                const $select = $('[data-preview-other-select]').filter(function() {
                    return $(this).data('preview-other-select') === fieldKey;
                });
                const isOtherSelected = $otherToggle.length > 0 || $select.val() === '__OTHER__';
                const $input = $('[data-preview-other-input]').filter(function() {
                    return $(this).data('preview-other-input') === fieldKey;
                });

                $input.prop('disabled', !isOtherSelected);
                if (!isOtherSelected) {
                    $input.val('');
                }
            }

            $(document).on('change', '[data-preview-other-toggle], [data-preview-other-select]', function() {
                const fieldKey = $(this).data('preview-other-toggle') || $(this).data(
                    'preview-other-select');
                syncPreviewOther(fieldKey);
            });

            $('[data-preview-other-toggle], [data-preview-other-select]').each(function() {
                const fieldKey = $(this).data('preview-other-toggle') || $(this).data(
                    'preview-other-select');
                syncPreviewOther(fieldKey);
            });
        });
    </script>
@endsection

@section('content-child')
    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="alert alert-info">
                        <strong>Preview Mode</strong>
                        This is a preview of how the registration form will appear to users.
                        No data will be submitted or saved.
                    </div>

                    <div class="preview-wrap">
                        <div class="preview-card">
                            <div class="preview-header">
                                <h1 class="preview-title">{{ $event->title }}</h1>
                            </div>

                            @if (!empty($event->intro_html))
                                <div class="mb-4">
                                    {!! $event->intro_html !!}
                                </div>
                            @endif

                            <div class="preview-section">
                                <h5>Registration Form</h5>

                                @foreach ($fields as $field)
                                    <div class="preview-field">
                                        @includeWhen(View::exists('event.partials.preview-fields.' . $field->type),
                                            'event.partials.preview-fields.' . $field->type,
                                            ['field' => $field]
                                        )
                                        @unless (View::exists('event.partials.preview-fields.' . $field->type))
                                            <div class="form-group">
                                                <label class="form-label">Unsupported field type.</label>
                                            </div>
                                        @endunless
                                    </div>
                                @endforeach
                            </div>

                            @if ($priceOptions->isNotEmpty())
                                <div class="preview-section preview-price">
                                    <h5>{{ $event->price_question_label ?? 'Registration Option' }}</h5>
                                    @foreach ($priceOptions as $priceOption)
                                        @php
                                            $available = null;
                                            if (!is_null($priceOption->quota)) {
                                                $available = $priceOption->quota - ($priceOption->sold_count ?? 0);
                                            }
                                            $isSoldOut = !is_null($available) && $available <= 0;
                                        @endphp

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" disabled
                                                {{ $isSoldOut ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="price-{{ $priceOption->id }}">
                                                <span>{{ $priceOption->name }}</span>
                                                <span class="price-amount">
                                                    @php
                                                        $currency = strtoupper($priceOption->currency ?? 'IDR');
                                                    @endphp
                                                    @if ($currency === 'IDR')
                                                        Rp {{ number_format((float) $priceOption->amount, 0, ',', '.') }}
                                                    @else
                                                        {{ $currency }}
                                                        {{ number_format((float) $priceOption->amount, 2, '.', ',') }}
                                                    @endif
                                                </span>
                                            </label>
                                        </div>

                                        @if ($available !== null)
                                            <div class="mb-2 text-muted-small">
                                                @if ($isSoldOut)
                                                    Sold Out
                                                @else
                                                    Available: {{ $available }}
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <div class="preview-actions">
                                <button type="button" class="btn btn-primary" disabled>Submit Registration</button>
                                <div class="mt-2 text-muted-small">This is a preview. Form submission is disabled.</div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('event.forms.index', $event) }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Builder
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
