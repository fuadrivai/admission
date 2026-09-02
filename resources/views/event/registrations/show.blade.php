@extends('main-layout.index')

@section('content-style')
    <style>
        .registration-detail {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            border: 1px solid #e9ecef;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .detail-title h2 {
            margin: 0 0 0.5rem;
            font-size: 1.75rem;
            color: #333;
        }

        .detail-subtitle {
            color: #666;
            font-size: 0.95rem;
            margin: 0;
        }

        .detail-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.1rem;
            color: #333;
            font-weight: 500;
        }

        .info-value code {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: #e83e8c;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-submitted {
            background: #cfe2ff;
            color: #084298;
        }

        .status-pending {
            background: #fff3cd;
            color: #664d03;
        }

        .status-paid {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #842029;
        }

        .status-expired {
            background: #e2e3e5;
            color: #383d41;
        }

        .answers-section {
            margin-top: 2rem;
        }

        .answers-section h4 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .answer-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            border-left: 4px solid #2563eb;
        }

        .answer-label {
            font-weight: 600;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .answer-value {
            color: #333;
            font-size: 1rem;
            word-break: break-word;
        }

        .answer-value code {
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: #e83e8c;
            font-size: 0.9rem;
        }

        .attachment-preview {
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .attachment-preview img {
            max-width: 220px;
            max-height: 180px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            background: #fff;
            object-fit: cover;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .attachment-preview iframe {
            width: 100%;
            max-width: 420px;
            height: 260px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #fff;
        }

        .attachment-download {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1rem;
            border: 1px solid #dfe3e8;
            background: #fff;
            border-radius: 8px;
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
            margin-top: 0.75rem;
        }

        .attachment-download:hover {
            text-decoration: none;
            background: #f8f9fa;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
            }

            .detail-actions {
                width: 100%;
            }

            .detail-actions .btn {
                flex: 1;
                min-width: 0;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <a href="{{ route('event.registrations.index', $event) }}" class="back-link">
                    <i class="fa fa-arrow-left"></i> Back to Registrations
                </a>

                <div class="registration-detail">
                    <div class="detail-header">
                        <div class="detail-title">
                            <h2>Registration Details</h2>
                            <p class="detail-subtitle">{{ $event->title }}</p>
                        </div>
                        <div class="detail-actions">
                            <a href="{{ route('event.registrations.index', $event) }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times"></i> Close
                            </a>
                            <form method="POST" action="{{ route('event.registration.delete', [$event, $registration]) }}"
                                style="display: inline;"
                                onsubmit="return confirm('Are you sure you want to delete this registration?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Registration Code</span>
                            <span class="info-value"><code>{{ $registration->code }}</code></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="status-badge status-{{ strtolower($registration->status) }}">
                                {{ $registration->status }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Amount</span>
                            <span class="info-value">Rp
                                {{ number_format((float) $registration->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Registered At</span>
                            <span
                                class="info-value">{{ $registration->registered_at ? $registration->registered_at->format('d M Y H:i:s') : '--' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Submitted At</span>
                            <span class="info-value">{{ $registration->created_at->format('d M Y H:i:s') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Last Updated</span>
                            <span class="info-value">{{ $registration->updated_at->format('d M Y H:i:s') }}</span>
                        </div>
                    </div>

                    @if ($registration->fieldAnswers->isNotEmpty())
                        <div class="answers-section">
                            <h4>Form Answers</h4>
                            @foreach ($registration->fieldAnswers as $answer)
                                <div class="answer-item">
                                    <div class="answer-label">
                                        {{ $answer->eventField->label ?? 'Unknown Field' }}
                                        <span style="color: #999; font-weight: normal;">
                                            ({{ $answer->eventField->type ?? 'text' }})
                                        </span>
                                    </div>
                                    <div class="answer-value">
                                        @php
                                            $fieldType = $answer->eventField->type ?? null;
                                            $attachmentValue = $fieldType === 'attachment' ? $answer->value : null;
                                            $attachmentExists = $attachmentValue
                                                ? Storage::disk('event')->exists($attachmentValue)
                                                : false;
                                            $attachmentUrl = $attachmentExists
                                                ? route('event.registration.attachment', [$event, $registration]) .
                                                    '?file=' .
                                                    urlencode($attachmentValue)
                                                : null;
                                            $attachmentExtension = $attachmentValue
                                                ? strtolower(pathinfo($attachmentValue, PATHINFO_EXTENSION))
                                                : '';
                                            $attachmentIsImage = in_array(
                                                $attachmentExtension,
                                                ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'],
                                                true,
                                            );
                                            $attachmentIsPdf = $attachmentExtension === 'pdf';

                                            try {
                                                $decoded = json_decode($answer->value, true);
                                                $isJson = is_array($decoded);
                                            } catch (\Exception $e) {
                                                $isJson = false;
                                            }
                                        @endphp

                                        @if ($fieldType === 'attachment')
                                            @if ($attachmentExists && $attachmentUrl)
                                                @if ($attachmentIsImage)
                                                    <div class="attachment-preview">
                                                        <img src="{{ $attachmentUrl }}" alt="Attachment preview" />
                                                    </div>
                                                @elseif ($attachmentIsPdf)
                                                    <div class="attachment-preview">
                                                        <iframe src="{{ $attachmentUrl }}"
                                                            title="Attachment preview"></iframe>
                                                    </div>
                                                    <a href="{{ $attachmentUrl }}" target="_blank"
                                                        rel="noopener noreferrer" class="attachment-download">
                                                        <i class="fa fa-file-pdf"></i> Open PDF
                                                    </a>
                                                @else
                                                    <a href="{{ $attachmentUrl }}" target="_blank"
                                                        rel="noopener noreferrer" class="attachment-download">
                                                        <i class="fa fa-download"></i> Download File
                                                    </a>
                                                @endif
                                            @else
                                                {{ $attachmentValue ?: '(empty)' }}
                                            @endif
                                        @elseif ($isJson)
                                            <code>{{ implode(', ', $decoded) }}</code>
                                        @else
                                            {{ $answer->value ?: '(empty)' }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            style="padding: 2rem; text-align: center; background: #f8f9fa; border-radius: 6px; color: #666;">
                            No form answers submitted for this registration.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
