@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1">Check-in: {{ $event->title }}</h5>
                    <div class="small text-muted">Search families, record attendance, or register a walk-in.</div>
                </div>
                <a href="{{ route('event.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Events
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('walk_in_token'))
                    <div class="alert alert-info">
                        Temporary walk-in token for the verified parent:
                        <strong class="fs-5">{{ session('walk_in_token') }}</strong>
                        <div class="small">Expires in 10 minutes. Give the parent this token and the public registration link:
                            <a href="{{ session('walk_in_url') }}">{{ session('walk_in_url') }}</a>
                        </div>
                    </div>
                @endif
                @if (session('family_code'))
                    <div class="alert alert-info">
                        Family invitation code to share with the verified parent:
                        <strong class="fs-5">{{ session('family_code') }}</strong>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="GET" action="{{ route('event.checkin.index', $event) }}" class="row g-2 mb-4">
                    <div class="col-md-8">
                        <label class="visually-hidden" for="checkin-search">Search families</label>
                        <input class="form-control" id="checkin-search" name="q" value="{{ $search }}"
                            placeholder="Search student, parent, email, or registration code">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </div>
                    @if ($search !== '')
                        <div class="col-auto">
                            <a class="btn btn-outline-secondary" href="{{ route('event.checkin.index', $event) }}">Clear</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Parent / Guardian</th>
                                <th>Email</th>
                                <th>Registration</th>
                                <th>Attendance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registrations as $registration)
                                @php
                                    $answers = $registration->fieldAnswers->keyBy('event_field_id');
                                    $student = $studentField ? optional($answers->get($studentField->id))->value : null;
                                    $parent = $parentField ? optional($answers->get($parentField->id))->value : null;
                                    $email = $emailField ? optional($answers->get($emailField->id))->value : null;
                                @endphp
                                <tr>
                                    <td>{{ $student ?: '-' }}</td>
                                    <td>{{ $parent ?: '-' }}</td>
                                    <td>{{ $email ?: '-' }}</td>
                                    <td><code>{{ $registration->code }}</code></td>
                                    <td>
                                        @if ($registration->checkin)
                                            <span class="badge bg-success">Checked in</span>
                                            <div class="small text-muted">
                                                {{ $registration->checkin->checked_in_at->format('d M Y H:i') }}
                                                · {{ $registration->checkin->source }}
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Not checked in</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if ($revision)
                                                <form method="POST" action="{{ route('event.checkin.code', [$event, $registration]) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Issue / show code</button>
                                                </form>
                                            @endif
                                            @if (! $registration->checkin)
                                                <form method="POST" action="{{ route('event.checkin.staff', [$event, $registration]) }}"
                                                    onsubmit="return confirm('Record this family as checked in?');">
                                                    @csrf
                                                    <button class="btn btn-sm btn-primary" type="submit">Check in family</button>
                                                </form>
                                            @else
                                                <span class="text-muted">Complete</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No registrations match this search.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $registrations->links() }}

                <hr class="my-4">
                <h6>Authorize a walk-in family</h6>
                @if (! $revision)
                    <div class="alert alert-warning">Publish the Microsite before authorizing walk-in registrations.</div>
                @elseif (! $studentField || ! $parentField)
                    <div class="alert alert-warning">Select student-name and parent-name fields in Microsite settings first.</div>
                @else
                    <p class="small text-muted">After verifying the family at reception, issue a short-lived token. The parent uses it on the public Microsite to enter their own details. This does not create an Admission record.</p>
                    <form method="POST" action="{{ route('event.checkin.walk-in', $event) }}">
                        @csrf
                        <button class="btn btn-success" type="submit">Issue parent walk-in token</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
