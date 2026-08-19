@extends('main-layout.index')

@section('content-style')
    <link href="/assets/extensions/summernote/summernote-bs5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/extensions/flatpickr/flatpickr.min.css">
    <style>
        .note-editor.note-frame {
            border: 1px solid #dee2e6;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            text-align: center;
        }

        .loading-spinner.show {
            display: block;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        }

        .overlay.show {
            display: block;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h5>

                @if (isset($event))
                    <div class="d-flex gap-2">
                        <a href="{{ route('event.forms.index', $event) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-sliders"></i> Form Setting
                        </a>
                        <a href="{{ route('event.email-templates.index', $event) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-envelope"></i> Email Setting
                        </a>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <form id="eventForm" method="POST" autocomplete="off"
                    action="{{ isset($event) ? route('event.update', $event) : route('event.store') }}">
                    @csrf
                    @if (isset($event))
                        @method('PUT')
                    @endif

                    <!-- Event Title & Slug -->
                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <label for="title" class="form-label required-label">Event Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $event->title ?? '') }}"
                                    placeholder="Enter event title" required>
                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-4">
                                <label for="slug" class="form-label">Slug / URL <small
                                        class="text-muted">(Auto-generated)</small></label>
                                <input type="text" class="form-control" id="slug" name="slug"
                                    value="{{ old('slug', $event->slug ?? '') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Branch Selection -->
                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                    name="branch_id">
                                    <option value="">-- Select Branch --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id', $event->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    @foreach (['DRAFT', 'PUBLISHED', 'CLOSED'] as $status)
                                        <option value="{{ $status }}"
                                            {{ old('status', $event->status ?? 'DRAFT') == $status ? 'selected' : '' }}>
                                            {{ ucfirst(strtolower($status)) }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="availability_type" class="form-label">Availability Type</label>
                                <select class="form-select @error('availability_type') is-invalid @enderror"
                                    id="availability_type" name="availability_type">
                                    @foreach (['ALWAYS', 'LIMITED'] as $availabilityType)
                                        <option value="{{ $availabilityType }}"
                                            {{ old('availability_type', $event->availability_type ?? 'ALWAYS') == $availabilityType ? 'selected' : '' }}>
                                            {{ ucfirst(strtolower($availabilityType)) }}</option>
                                    @endforeach
                                </select>
                                @error('availability_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6" id="active_until_wrapper"
                                style="display: {{ old('availability_type', $event->availability_type ?? 'ALWAYS') === 'LIMITED' ? 'block' : 'none' }};">
                                <label for="active_until" class="form-label">Active Until</label>
                                <input type="text" class="form-control @error('active_until') is-invalid @enderror"
                                    id="active_until" name="active_until"
                                    value="{{ old('active_until', isset($event) && $event->active_until ? $event->active_until->format('Y-m-d H:i') : '') }}"
                                    placeholder="YYYY-MM-DD HH:MM">
                                @error('active_until')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Intro HTML & Price Question Label -->
                    <div class="form-section">
                        <label for="intro_html" class="form-label">Intro Text (HTML)</label>
                        <textarea class="form-control summernote @error('intro_html') is-invalid @enderror" id="intro_html" name="intro_html"
                            rows="6">{{ old('intro_html', $event->intro_html ?? '') }}</textarea>
                        @error('intro_html')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="form-section d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fa fa-save"></i> {{ isset($event) ? 'Update Event' : 'Create Event' }}
                        </button>
                        <a href="{{ route('event.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div class="overlay" id="loadingOverlay"></div>
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-white mt-2">Saving...</p>
    </div>
@endsection

@section('content-script')
    <script src="/assets/extensions/summernote/summernote-bs5.min.js"></script>
    <script src="/assets/extensions/flatpickr/flatpickr.min.js"></script>

    <script>
        $(document).ready(function() {
            function toggleAvailabilityFields() {
                var availabilityType = $('#availability_type').val();
                var $activeUntilWrapper = $('#active_until_wrapper');
                var $activeUntilInput = $('#active_until');

                if (availabilityType === 'LIMITED') {
                    $activeUntilWrapper.show();
                    if ($activeUntilInput.data('flatpickr')) {
                        $activeUntilInput.data('flatpickr').open();
                    }
                } else {
                    $activeUntilWrapper.hide();
                    $activeUntilInput.val('');
                }
            }

            $('#availability_type').on('change', toggleAvailabilityFields);
            toggleAvailabilityFields();

            if (typeof flatpickr !== 'undefined') {
                flatpickr('#active_until', {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    time_24hr: true,
                    allowInput: true
                });
            }
            // Wait a bit to ensure Summernote is loaded
            setTimeout(function() {
                // Initialize Summernote
                if (typeof $.fn.summernote === 'undefined') {
                    console.error('Summernote not loaded');
                    return;
                }

                $('.summernote').summernote({
                    height: 300,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'help']]
                    ]
                });
            }, 100);

            // Auto-generate slug from title
            $('#title').on('keyup', function() {
                let title = $(this).val();
                let slug = title
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            });

            // Form submit with AJAX
            $('#eventForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                let url = $(this).attr('action');
                let method = $(this).find('input[name="_method"]').val() || 'POST';

                // Show loading
                $('#loadingOverlay').addClass('show');
                $('#loadingSpinner').addClass('show');
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: url,
                    type: method === 'PUT' ? 'POST' : 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': method,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Hide loading
                        $('#loadingOverlay').removeClass('show');
                        $('#loadingSpinner').removeClass('show');

                        // Show success message
                        if (response.message) {
                            showAlert('success', response.message);
                        }

                        // Redirect after 1 second
                        setTimeout(function() {
                            window.location.href = '/event';
                        }, 1000);
                    },
                    error: function(xhr) {
                        // Hide loading
                        $('#loadingOverlay').removeClass('show');
                        $('#loadingSpinner').removeClass('show');
                        $('#submitBtn').prop('disabled', false);

                        // Handle validation errors
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = '';

                            $.each(errors, function(field, messages) {
                                errorMessage += messages.join('\n') + '\n';
                            });

                            showAlert('danger', 'Validation Error:\n' + errorMessage);

                            // Highlight error fields
                            $.each(errors, function(field, messages) {
                                $('#' + field).addClass('is-invalid');
                            });
                        } else {
                            showAlert('danger', 'An error occurred. Please try again.');
                        }
                    }
                });
            });

            // Helper function to show alerts
            function showAlert(type, message) {
                let alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message.replace(/\n/g, '<br>')}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('.card-body').prepend(alertHtml);

                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
@endsection
