<div class="event-summary card shadow-sm">
    <div class="card-body py-2">
        <div class="row text-center gy-2">
            <div class="col-6 col-md">
                <div class="small text-muted">Total Events</div>
                <div class="h5 mb-0">{{ $events->total() }}</div>
            </div>
            <div class="col-6 col-md">
                <div class="small text-muted">Showing</div>
                <div class="h5 mb-0">{{ $events->count() }}</div>
            </div>
        </div>
    </div>
</div>

@forelse ($events as $event)
    <div class="card event-card shadow-sm mb-2">
        <div class="card-body">
            <div class="row align-items-start gy-3">
                <div class="col-lg-7 col-md-8">
                    <div class="event-card-title">{{ $event->title }}</div>
                    <a class="event-card-url" href="{{ route('events.show', $event) }}" target="_blank">
                        {{ route('events.show', $event) }}
                    </a>
                </div>
                <div class="col-lg-5 col-md-4">
                    <div class="event-card-actions">
                        @if (!auth()->check() || auth()->user()->role != 'user')
                            <a href="{{ route('event.edit', $event) }}" class="btn btn-sm btn-primary"><i
                                    class="fa fa-pencil"></i> Edit</a>
                        @endif
                        <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-warning"><i
                                class="fa fa-eye"></i> Live Form</a>
                        <a href="{{ route('event.registrations.index', $event) }}"
                            class="btn btn-sm btn-info text-white"><i class="fa fa-users"></i> Registrations</a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row gy-3">
                <div class="col-md-3 col-6">
                    <div class="event-info-label">Branch</div>
                    <div class="event-info-value">{{ $event->branch->name ?? '-' }}</div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="event-info-label">Status</div>
                    <div class="event-info-value"><span
                            class="badge bg-{{ $event->status === 'PUBLISHED' ? 'success' : ($event->status === 'CLOSED' ? 'danger' : 'secondary') }}">{{ $event->status }}</span>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="event-info-label">Availability</div>
                    <div class="event-info-value">{{ ucfirst(strtolower($event->availability_type ?? 'ALWAYS')) }}
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="event-info-label">Active Until</div>
                    <div class="event-info-value">
                        {{ $event->active_until ? $event->active_until->format('d M Y H:i') : '-' }}</div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="event-info-label">Registered</div>
                    <div class="event-info-value">{{ $event->registrations_count }}</div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted">No events found.</div>
    </div>
@endforelse

<div class="mt-3">{{ $events->onEachSide(0)->links() }}</div>
