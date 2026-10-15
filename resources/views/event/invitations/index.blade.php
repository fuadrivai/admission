@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1">Invitations: {{ $event->title }}</h5>
                    <div class="small text-muted">Each Event registration gets one family code of five characters.</div>
                </div>
                <a href="{{ route('event.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Events
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                @if (! $emailField)
                    <div class="alert alert-warning">
                        No primary email field is configured for this Event. Set an active email field as the primary email in Form Settings before sending invitations.
                    </div>
                @endif
                @if (! $event->microsite || ! $event->microsite->publishedRevision)
                    <div class="alert alert-warning">
                        Publish the Microsite before sending event invitations.
                        <a href="{{ route('event.microsite.edit', $event) }}">Open Microsite settings</a>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Registration</th>
                                <th>Parent email</th>
                                <th>Invitation status</th>
                                <th>Last sent</th>
                                <th>Delivery history</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registrations as $registration)
                                @php
                                    $answer = $registration->fieldAnswers->first();
                                    $invitation = $registration->invitation;
                                    $lastLog = $invitation ? $invitation->emailLogs->first() : null;
                                    $email = $answer ? trim((string) $answer->value) : '';
                                    $hasValidEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
                                @endphp
                                <tr>
                                    <td><code>{{ $registration->code }}</code></td>
                                    <td>{{ $email ?: 'No email provided' }}</td>
                                    <td>
                                        @if ($invitation)
                                            <span class="badge bg-{{ $invitation->status === 'SENT' ? 'success' : ($invitation->status === 'FAILED' ? 'danger' : 'secondary') }}">
                                                {{ $invitation->status }}
                                            </span>
                                            <div class="small text-muted">Code issued; hidden for security</div>
                                        @else
                                            <span class="badge bg-secondary">Not sent</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($invitation->last_sent_at ?? null)->format('d M Y H:i') ?: '-' }}</td>
                                    <td>
                                        @if ($lastLog)
                                            {{ $lastLog->type }} / {{ $lastLog->status }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($emailField && $hasValidEmail && $event->microsite && $event->microsite->publishedRevision)
                                            <form method="POST"
                                                action="{{ route('event.invitations.send', [$event, $registration]) }}"
                                                onsubmit="return confirm('Send this email to the registered parent?');">
                                                @csrf
                                                @if ($invitation && $invitation->status === 'SENT')
                                                    <input type="hidden" name="type" value="REMINDER">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa fa-paper-plane"></i> Send reminder
                                                    </button>
                                                @else
                                                    <input type="hidden" name="type" value="INVITATION">
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-envelope"></i> Send invitation
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-secondary" type="button" disabled>Unavailable</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No Event registrations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $registrations->links() }}
            </div>
        </div>
    </section>
@endsection
