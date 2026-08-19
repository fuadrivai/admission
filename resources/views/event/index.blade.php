@extends('main-layout.index')

@section('content-style')
    <style>
        .event-filter-card,
        .event-summary,
        .event-card {
            border: 0;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .event-card {
            margin-top: 0.3rem;
        }

        .event-card-title {
            color: #1f2937;
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .event-card-url {
            display: block;
            overflow-wrap: anywhere;
            font-size: 0.8rem;
        }

        .event-card-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .event-info-label {
            color: #6b7280;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .event-info-value {
            color: #1f2937;
            font-weight: 600;
            margin-top: 0.2rem;
        }

        @media (max-width: 767.98px) {
            .event-card-actions {
                justify-content: flex-start;
            }

            .event-card-actions .btn {
                flex: 1 1 auto;
            }
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card event-filter-card">
            <div class="card-body">
                <p class="d-inline-flex gap-1 mb-0">
                    <a data-bs-toggle="collapse" href="#collapse-event-filter" aria-expanded="false"
                        aria-controls="collapse-event-filter">
                        Insert Filter <i class="fa fa-caret-down"></i>
                    </a>
                </p>
                <div class="collapse" id="collapse-event-filter">
                    <div class="row gy-3 mt-1">
                        <div class="col-md-12">
                            <label for="event-filter-search">Search</label>
                            <input type="text" class="form-control" id="event-filter-search" placeholder="title or slug">
                        </div>
                        <div class="col-md-4">
                            <label for="event-filter-status">Status</label>
                            <select id="event-filter-status" class="form-select">
                                <option value="all" {{ request('status', 'PUBLISHED') === 'all' ? 'selected' : '' }}>All
                                    statuses</option>
                                <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                <option value="PUBLISHED"
                                    {{ request('status', 'PUBLISHED') === 'PUBLISHED' ? 'selected' : '' }}>Published
                                </option>
                                <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Closed
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="event-filter-availability">Availability</label>
                            <select id="event-filter-availability" class="form-select">
                                <option value="all">All types</option>
                                <option value="ALWAYS">Always</option>
                                <option value="LIMITED">Limited</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="event-filter-branch">Branch</label>
                            <select id="event-filter-branch" class="form-select">
                                <option value="all">All branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ (string) request('branch_id', 'all') === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="event-list" class="mt-3">
            @include('event._list')
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        let eventTypingTimer;

        $(document).ready(function() {
            $('#event-filter-search').on('keyup', function() {
                clearTimeout(eventTypingTimer);
                eventTypingTimer = setTimeout(loadEvents, 350);
            });

            $('#event-filter-status, #event-filter-availability, #event-filter-branch').on('change', loadEvents);

            $(document).on('click', '#event-list .pagination a', function(e) {
                e.preventDefault();
                loadEvents($(this).attr('href'));
            });
        });

        function eventFilterData() {
            return {
                search: $('#event-filter-search').val(),
                status: $('#event-filter-status').val(),
                availability_type: $('#event-filter-availability').val(),
                branch_id: $('#event-filter-branch').val(),
            };
        }

        function loadEvents(url = "{{ route('event.index') }}") {
            $.ajax({
                url: url,
                data: eventFilterData(),
                type: 'GET',
                success: function(html) {
                    $('#event-list').html(html);
                }
            });
        }
    </script>
@endsection
