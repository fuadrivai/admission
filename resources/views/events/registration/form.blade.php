<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $event->title }} | Registration</title>
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/assets/static/css/enrolment-external.css?v=1.0.2">
    <style>
        :root {
            --maroon-900: #4b0000;
            --maroon-800: #660000;
            --maroon-700: #7b0d0d;
            --maroon-600: #8d1c1c;
            --maroon-200: #f8dfe2;
            --maroon-100: #fef0f2;
            --maroon-50: #fef7f8;
            --soft-white: #fffafc;
            --text-dark: #2f1d1d;
            --text-muted: #6b4d4d;
            --line: #eed5d8;
            --success: #2d8a5d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: #ffffff;
            color: var(--text-dark);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .event-registration-wrapper {
            min-height: 100%;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .event-registration-container {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
        }

        .registration-card {
            background: #ffffff;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .event-card-header {
            background: linear-gradient(135deg, #800000 0%, #a00000 100%);
            color: white;
            padding: 20px 30px;
            text-align: center;
            position: relative;
        }

        .event-card-header::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 16px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.12) 100%);
        }

        .event-card-header img {
            width: 40%;
            max-width: none;
            height: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.18));
        }

        .event-card-header h1,
        .event-card-header .event-title {
            margin: 0;
            font-size: 2rem;
            line-height: 1.25;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .event-card-body {
            padding: 28px 18px 30px;
        }

        .event-title {
            margin: 0;
        }

        .event-intro {
            padding: 18px 18px 12px;
            background: var(--maroon-100);
            border: 1px solid var(--line);
            border-radius: 16px;
            color: var(--text-dark);
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .event-intro p {
            margin-bottom: 0.9rem;
        }

        .event-intro p:last-child {
            margin-bottom: 0;
        }

        .event-intro h2,
        .event-intro h3,
        .event-intro h4,
        .event-intro h5 {
            color: var(--maroon-800);
            margin-top: 1rem;
            margin-bottom: 0.6rem;
        }

        .event-intro ul,
        .event-intro ol {
            padding-left: 1.2rem;
        }

        .event-intro a {
            color: var(--maroon-700);
            font-weight: 600;
        }

        .cta-box {
            text-align: center;
            margin: 0 0 12px;
        }

        .cta-button,
        .submit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--maroon-800) 0%, var(--maroon-600) 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.01em;
            padding: 12px 18px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            box-shadow: 0 10px 22px rgba(99, 0, 0, 0.2);
        }

        .cta-button:hover,
        .submit-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(99, 0, 0, 0.26);
        }

        .cta-button:focus,
        .submit-button:focus,
        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(139, 30, 30, 0.18);
        }

        .submit-button:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        .field-block {
            margin-bottom: 1.25rem;
        }

        .field-block label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.55rem;
        }

        .field-block .required-asterisk {
            color: #d42121;
        }

        .form-control,
        .form-select {
            width: 100%;
            min-height: 48px;
            border: 2px solid #e8d6d9;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 1rem;
            background: #fff;
            color: var(--text-dark);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--maroon-700);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #d33030;
            background-color: #fff7f7;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .form-check-card {
            border: 2px solid #ecd9dc;
            border-radius: 12px;
            padding: 10px 12px;
            background: #fffafc;
            margin-bottom: 10px;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .form-check-card:hover {
            border-color: var(--maroon-700);
            background: #fff2f4;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: var(--maroon-700);
        }

        .form-check-label {
            margin: 0;
            font-weight: 500;
            color: var(--text-dark);
        }

        .radio-group,
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .price-option-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .price-option-card {
            position: relative;
            display: block;
            width: 100%;
            border: 2px solid #ecd9dc;
            border-radius: 14px;
            background: #fffafc;
            padding: 14px 16px;
            cursor: pointer;
            transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .price-option-card:hover {
            border-color: var(--maroon-700);
            background: #fff4f6;
        }

        .price-option-card.selected {
            border-color: var(--maroon-700);
            background: #fff1f3;
            box-shadow: 0 0 0 3px rgba(123, 13, 13, 0.08);
        }

        .price-option-card input[type="radio"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .price-option-card.sold-out {
            opacity: 0.72;
            background: #f5f5f5;
        }

        .price-option-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            width: 100%;
            font-weight: 700;
            color: var(--text-dark);
        }

        .price-option-meta {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 6px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .price-option-amount {
            white-space: nowrap;
            color: var(--maroon-700);
            font-weight: 800;
        }

        .invalid-feedback {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.82rem;
            color: #d63737;
            font-weight: 600;
        }

        .event-options-group {
            border: 2px solid var(--line);
            border-radius: 14px;
            padding: 12px;
            background: #fff;
            margin-bottom: 1rem;
        }

        .event-option-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 10px;
            border: 2px solid #e8d6d9;
            border-radius: 12px;
            background: #fffafc;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .event-option-card:last-child {
            margin-bottom: 0;
        }

        .event-option-card:hover {
            border-color: var(--maroon-700);
            background: #fff4f6;
        }

        .event-option-card input[type="radio"],
        .event-option-card input[type="checkbox"] {
            flex: 0 0 auto;
            width: 18px;
            height: 18px;
            margin: 3px 0 0 0;
            cursor: pointer;
            accent-color: var(--maroon-700);
        }

        .event-option-label {
            flex: 1;
            cursor: pointer;
            color: var(--text-dark);
            font-weight: 500;
            line-height: 1.5;
        }

        .event-option-card.is-selected {
            border-color: var(--maroon-700);
            background: #fff1f3;
            box-shadow: 0 0 0 3px rgba(123, 13, 13, 0.08);
        }

        .event-option-card.is-selected .event-option-label {
            font-weight: 600;
            color: var(--maroon-800);
        }

        .file-upload-preview-wrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .file-preview-box {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #ecd9dc;
            border-radius: 12px;
            background: #fffafc;
            min-height: 72px;
        }

        .file-preview-image {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e8d6d9;
            background: #fff;
            display: block;
        }

        .file-preview-icon {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f8e8ea;
            color: var(--maroon-700);
            font-size: 1.6rem;
            border: 1px solid #e8d6d9;
        }

        .file-preview-meta {
            flex: 1;
            min-width: 0;
        }

        .file-preview-name {
            color: var(--text-dark);
            font-weight: 600;
            line-height: 1.4;
            word-break: break-word;
        }

        .file-preview-size {
            display: block;
            margin-top: 2px;
            color: var(--text-muted);
            font-size: 0.82rem;
        }

        .file-remove-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background: #f3d5d8;
            color: var(--maroon-700);
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
            flex-shrink: 0;
        }

        .file-remove-btn:hover {
            background: #f1c8ce;
            transform: scale(1.03);
        }

        .field-block.has-error .form-control,
        .field-block.has-error .form-select,
        .field-block.has-error .event-options-group {
            border-color: #d63737 !important;
            background-color: #fff7f7 !important;
        }

        .event-options-group.has-error {
            border-color: #d63737 !important;
            background-color: #fff7f7 !important;
        }

        .field-error {
            display: block !important;
            margin-top: 0.5rem;
            font-size: 0.82rem;
            color: #d63737;
            font-weight: 600;
        }

        .submit-button-group {
            display: flex;
            gap: 12px;
            margin-top: 1.5rem;
        }

        .prev-button {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            border: 2px solid var(--maroon-700);
            border-radius: 14px;
            background: #fff;
            color: var(--maroon-700);
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.01em;
            padding: 12px 18px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            box-shadow: 0 4px 12px rgba(123, 13, 13, 0.15);
            cursor: pointer;
        }

        .prev-button:hover {
            background: var(--maroon-50);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(123, 13, 13, 0.2);
        }

        .prev-button:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(123, 13, 13, 0.2);
        }

        .prev-button:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        #submit-button-wrapper {
            margin-top: 1.5rem;
        }

        #submit-button-wrapper .submit-button {
            flex: 1;
            margin: 0;
        }

        @media (max-width: 576px) {
            .event-registration-wrapper {
                padding: 16px 10px 28px;
            }

            .event-card-body {
                padding: 20px 14px 22px;
            }

            .event-card-header {
                padding: 22px 14px 18px;
            }

            .event-intro {
                padding: 16px 14px;
            }

            .price-option-label,
            .price-option-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .price-option-amount {
                white-space: normal;
            }

            .event-options-group {
                padding: 10px;
            }

            .event-option-card {
                padding: 12px 14px;
            }

            .submit-button-group {
                flex-direction: column;
                gap: 10px;
            }

            .submit-button-group .submit-button,
            .submit-button-group .prev-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="event-registration-wrapper">
        <div class="event-registration-container">
            <div class="registration-card">
                <div class="event-card-header">
                    <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo"
                        onerror="this.style.display='none';">
                    <h2 class="event-title">{{ $event->title }}</h2>
                </div>

                <div class="event-card-body">
                    @if (!empty($event->intro_html))
                        <div id="intro-area">
                            <div class="event-intro">
                                {!! $event->intro_html !!}
                            </div>

                            <div class="cta-box">
                                <button type="button" class="cta-button" id="show-event-form-btn">
                                    Go to Registration Form
                                </button>
                            </div>
                        </div>
                    @endif


                    <div id="event-form-wrapper" class="{{ !empty($event->intro_html) ? 'd-none' : '' }}">
                        <form method="POST" action="{{ route('events.register', $event) }}"
                            enctype="multipart/form-data" novalidate>
                            @csrf

                            @foreach ($fields as $field)
                                <div class="field-block">
                                    @include('events.registration.fields.' . $field->type, [
                                        'field' => $field,
                                    ])
                                </div>
                            @endforeach

                            @if ($priceOptions->isNotEmpty())
                                <div class="field-block mt-4">
                                    <label for="price_option_id">
                                        {{ $event->price_question_label ?? 'Registration Option' }}
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="price-option-list" id="price-option-list">
                                        @foreach ($priceOptions as $priceOption)
                                            @php
                                                $isSoldOut =
                                                    !is_null($priceOption->quota) &&
                                                    (int) $priceOption->sold_count >= (int) $priceOption->quota;
                                            @endphp
                                            <label class="price-option-card {{ $isSoldOut ? 'sold-out' : '' }}">
                                                <input type="radio" name="price_option_id"
                                                    value="{{ $priceOption->id }}"
                                                    {{ old('price_option_id') == $priceOption->id ? 'checked' : '' }}
                                                    {{ $isSoldOut ? 'disabled' : '' }}>
                                                <span class="price-option-label">
                                                    <span>{{ $priceOption->name }}{{ $isSoldOut ? ' (Sold Out)' : '' }}</span>
                                                    <span class="price-option-amount">
                                                        @if (strtoupper($priceOption->currency ?? 'IDR') === 'IDR')
                                                            Rp
                                                            {{ number_format((float) $priceOption->amount, 0, ',', '.') }}
                                                        @else
                                                            {{ strtoupper($priceOption->currency ?? 'IDR') }}
                                                            {{ number_format((float) $priceOption->amount, 2, '.', ',') }}
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="price-option-meta">
                                                    <span>{{ $priceOption->code }}</span>
                                                    @if (!is_null($priceOption->quota))
                                                        <span>{{ max((int) $priceOption->quota - (int) $priceOption->sold_count, 0) }}
                                                            left</span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('price_option_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="submit-button-group" id="submit-button-wrapper">
                                <button type="button" class="prev-button" id="prev-button">
                                    ← Previous
                                </button>
                                <button type="submit" class="submit-button" id="event-submit-button">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#show-event-form-btn').on('click', function() {
                var $introArea = $('#intro-area');
                var $wrapper = $('#event-form-wrapper');

                // Hide intro area
                $introArea.addClass('d-none');

                // Show form
                $wrapper.removeClass('d-none');

                // Scroll to form
                $('html, body').animate({
                    scrollTop: $wrapper.offset().top - 20
                }, 400);
            });

            // Previous button handler
            $('#prev-button').on('click', function() {
                var $introArea = $('#intro-area');
                var $wrapper = $('#event-form-wrapper');

                // Show intro area
                $introArea.removeClass('d-none');

                // Hide form
                $wrapper.addClass('d-none');

                // Scroll to intro area
                $('html, body').animate({
                    scrollTop: $introArea.offset().top - 20
                }, 400);
            });

            // Handle price option selection
            $('.price-option-card input[type="radio"]').on('change', function() {
                $('.price-option-card').removeClass('selected');
                $(this).closest('.price-option-card').addClass('selected');
            });

            $('.price-option-card input[type="radio"]:checked').trigger('change');

            // Handle event option card (radio) selection
            $('.event-options-group input[type="radio"]').on('change', function() {
                var name = $(this).attr('name');
                $('.event-options-group input[name="' + name + '"]')
                    .closest('.event-option-card')
                    .removeClass('is-selected');
                if ($(this).is(':checked')) {
                    $(this).closest('.event-option-card').addClass('is-selected');
                }
            });

            // Handle event option card (checkbox) selection
            $('.event-options-group input[type="checkbox"]').on('change', function() {
                $(this)
                    .closest('.event-option-card')
                    .toggleClass('is-selected', $(this).is(':checked'));
            });

            // Initialize selected state on page load
            $('.event-options-group input[type="radio"]:checked, .event-options-group input[type="checkbox"]:checked')
                .each(function() {
                    $(this).closest('.event-option-card').addClass('is-selected');
                });

            function syncOtherInput(fieldKey) {
                const $toggle = $('[data-other-toggle]').filter(function() {
                    return $(this).data('other-toggle') === fieldKey && $(this).is(':checked');
                });
                const $select = $('[data-other-select]').filter(function() {
                    return $(this).data('other-select') === fieldKey;
                });
                const isOtherSelected = $toggle.length > 0 || $select.val() === '__OTHER__';
                const $input = $('[data-other-input]').filter(function() {
                    return $(this).data('other-input') === fieldKey;
                });

                $input.prop('disabled', !isOtherSelected);
                if (!isOtherSelected) {
                    $input.val('');
                }

                $('[data-other-wrapper]').filter(function() {
                    return $(this).data('other-wrapper') === fieldKey;
                }).toggle(isOtherSelected);
            }

            $('[data-other-toggle], [data-other-select]').on('change', function() {
                const fieldKey = $(this).data('other-toggle') || $(this).data('other-select');
                syncOtherInput(fieldKey);
            });

            $('[data-other-toggle], [data-other-select]').each(function() {
                syncOtherInput($(this).data('other-toggle') || $(this).data('other-select'));
            });

            function formatBytes(bytes) {
                if (!bytes) return '0 Bytes';
                const units = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
                const value = bytes / Math.pow(1024, i);
                return value.toFixed(value >= 10 || i === 0 ? 0 : 1) + ' ' + units[i];
            }

            function renderFilePreview(input) {
                const $input = $(input);
                const fieldKey = $input.data('file-field');
                const $previewBox = $('[data-file-preview-for="' + fieldKey + '"]');
                const $image = $previewBox.find('.file-preview-image');
                const $icon = $previewBox.find('.file-preview-icon');
                const $name = $previewBox.find('.file-preview-name');
                const $size = $previewBox.find('.file-preview-size');
                const file = input.files && input.files[0];

                if (!file) {
                    $previewBox.addClass('d-none');
                    $image.addClass('d-none').removeAttr('src');
                    $icon.addClass('d-none');
                    $name.text('No file selected');
                    $size.text('');
                    return;
                }

                $name.text(file.name);
                $size.text(formatBytes(file.size));
                $previewBox.removeClass('d-none');

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $image.attr('src', e.target.result).removeClass('d-none');
                        $icon.addClass('d-none');
                    };
                    reader.readAsDataURL(file);
                    return;
                }

                $image.addClass('d-none').removeAttr('src');
                $icon.removeClass('d-none');
            }

            $(document).on('change', 'input[type="file"][data-file-field]', function() {
                renderFilePreview(this);
            });

            $(document).on('click', '[data-file-remove]', function() {
                const fieldKey = $(this).data('file-remove');
                const $input = $('input[data-file-field="' + fieldKey + '"]');
                const $previewBox = $('[data-file-preview-for="' + fieldKey + '"]');
                const input = $input.get(0);

                if (input) {
                    input.value = '';
                }

                $previewBox.addClass('d-none');
                $previewBox.find('.file-preview-image').addClass('d-none').removeAttr('src');
                $previewBox.find('.file-preview-icon').addClass('d-none');
                $previewBox.find('.file-preview-name').text('No file selected');
                $previewBox.find('.file-preview-size').text('');
            });

            function selectedFieldValue(fieldKey) {
                const $select = $('[data-field-key="' + fieldKey + '"]').filter('select');
                if ($select.length) {
                    return $select.val() || '';
                }

                return $('input[type="radio"][name="' + fieldKey + '"]:checked').val() || '';
            }

            function refreshDependentFields(parentKey) {
                $('[data-dependent-on="' + parentKey + '"]').each(function() {
                    const $dependent = $(this);
                    const mapping = $dependent.data('dependent-options') || {};
                    const parentValue = selectedFieldValue(parentKey);
                    const options = parentValue && Array.isArray(mapping[parentValue]) ? mapping[
                        parentValue] : [];
                    const currentValue = $dependent.data('current-value');

                    $dependent.empty().append($('<option>', {
                        value: '',
                        text: parentValue ? '-- Select --' :
                            'Please select the parent field first'
                    }));
                    options.forEach(function(option) {
                        $dependent.append($('<option>', {
                            value: option,
                            text: option
                        }));
                    });
                    $dependent.prop('disabled', !parentValue || options.length === 0);
                    $dependent.val(currentValue && options.includes(currentValue) ? currentValue : '');
                    $dependent.removeData('current-value');
                    refreshDependentFields($dependent.data('field-key'));
                });
            }

            $(document).on('change', '[data-field-key]', function() {
                refreshDependentFields($(this).data('field-key'));
            });
            $('[data-dependent-on]').each(function() {
                refreshDependentFields($(this).data('dependent-on'));
            });

            // Form Validation
            function validateForm() {
                let isValid = true;
                const $form = $('form');
                const processedGroups = new Set(); // Track processed radio/checkbox groups

                // Clear previous error states
                $form.find('.field-block').removeClass('has-error');
                $form.find('.event-options-group').removeClass('has-error');
                $form.find('.field-error').remove();

                // Validate all required fields
                $form.find('[required]').each(function() {
                    const $field = $(this);
                    const fieldType = $field.attr('type');
                    const fieldName = $field.attr('name');
                    let fieldValue = $field.val();

                    // Skip if this is a radio/checkbox group we've already validated
                    if ((fieldType === 'radio' || fieldType === 'checkbox') && processedGroups.has(
                            fieldName)) {
                        return;
                    }

                    // Mark this radio/checkbox group as processed
                    if (fieldType === 'radio' || fieldType === 'checkbox') {
                        processedGroups.add(fieldName);
                    }

                    // Handle different field types
                    if (fieldType === 'radio') {
                        // For radio buttons, check if any in the group is selected
                        fieldValue = $form.find('input[name="' + fieldName + '"]:checked').val();
                    } else if (fieldType === 'checkbox') {
                        // For checkboxes, check if at least one is selected
                        fieldValue = $form.find('input[name="' + fieldName + '"]:checked').length;
                    } else if ($field.is('select')) {
                        fieldValue = $field.val();
                    }

                    // Validate field
                    if (fieldType === 'checkbox') {
                        if (fieldValue === 0) {
                            isValid = false;
                            const $fieldBlock = $field.closest('.field-block');
                            if ($fieldBlock.length) {
                                $fieldBlock.addClass('has-error');
                                const $optionsGroup = $fieldBlock.find('.event-options-group');
                                $optionsGroup.addClass('has-error');
                                $optionsGroup.after(
                                    '<div class="invalid-feedback field-error" style="display: block;">Please select at least one option.</div>'
                                );
                            }
                        }
                    } else if (fieldType === 'radio') {
                        if (!fieldValue) {
                            isValid = false;
                            const $fieldBlock = $field.closest('.field-block');
                            if ($fieldBlock.length) {
                                $fieldBlock.addClass('has-error');
                                const $optionsGroup = $fieldBlock.find('.event-options-group');
                                $optionsGroup.addClass('has-error');
                                $optionsGroup.after(
                                    '<div class="invalid-feedback field-error" style="display: block;">Please select an option.</div>'
                                );
                            }
                        }
                    } else if (!fieldValue || fieldValue.trim() === '') {
                        isValid = false;
                        const $fieldBlock = $field.closest('.field-block');
                        if ($fieldBlock.length) {
                            $fieldBlock.addClass('has-error');
                            let errorMsg = 'This field is required.';

                            if ($field.is('input[type="email"]')) {
                                errorMsg = 'Please enter a valid email address.';
                            } else if ($field.is('input[type="tel"]')) {
                                errorMsg = 'Please enter a valid phone number.';
                            } else if ($field.is('select')) {
                                errorMsg = 'Please select an option.';
                            }

                            if ($field.is('select')) {
                                $field.after(
                                    '<div class="invalid-feedback field-error" style="display: block;">' +
                                    errorMsg + '</div>'
                                );
                            } else {
                                $field.after(
                                    '<div class="invalid-feedback field-error" style="display: block;">' +
                                    errorMsg + '</div>'
                                );
                            }
                        }
                    }
                });

                $form.find('[data-other-input]').filter(':enabled').each(function() {
                    const $input = $(this);
                    const fieldKey = $input.data('other-input');
                    const $toggle = $('[data-other-toggle]').filter(function() {
                        return $(this).data('other-toggle') === fieldKey && $(this).is(':checked');
                    }).first();
                    const $select = $('[data-other-select]').filter(function() {
                        return $(this).data('other-select') === fieldKey;
                    });
                    const required = $toggle.data('other-required') ||
                        $select.find('option:selected').data('other-required');

                    if (String(required) === '1' && !$.trim($input.val())) {
                        isValid = false;
                        const $fieldBlock = $input.closest('.field-block');
                        $fieldBlock.addClass('has-error');
                        $input.after(
                            '<div class="invalid-feedback field-error" style="display: block;">Please specify the Other value.</div>'
                        );
                    }
                });

                // Scroll to first error
                const $firstError = $form.find('.field-error').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 300);
                }

                return isValid;
            }

            // Validate on form submit
            $('form').on('submit', function(e) {
                e.preventDefault();

                if (validateForm()) {
                    // Form is valid, allow submission
                    var $btn = $('#event-submit-button');
                    $btn.prop('disabled', true).text('Submitting...');
                    $(this).unbind('submit').submit();
                }
            });

            // Real-time validation on field change
            $('form').on('change', '[required]', function() {
                const $field = $(this);
                const fieldType = $field.attr('type');
                const fieldName = $field.attr('name');
                const $fieldBlock = $field.closest('.field-block');

                if (!$fieldBlock.length) return;

                let isFieldValid = true;
                let fieldValue = $field.val();

                if (fieldType === 'radio') {
                    fieldValue = $('form').find('input[name="' + fieldName + '"]:checked').val();
                    isFieldValid = !!fieldValue;
                } else if (fieldType === 'checkbox') {
                    fieldValue = $('form').find('input[name="' + fieldName + '"]:checked').length;
                    isFieldValid = fieldValue > 0;
                } else {
                    isFieldValid = fieldValue && fieldValue.trim() !== '';
                }

                // Remove error feedback if field is now valid
                if (isFieldValid) {
                    $fieldBlock.removeClass('has-error');
                    $fieldBlock.find('.event-options-group').removeClass('has-error');
                    $fieldBlock.find('.field-error').remove();
                }
            });
        });
    </script>
</body>

</html>
