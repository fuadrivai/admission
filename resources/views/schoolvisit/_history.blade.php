@php
    // ensure we have the proper variable from controller
    $activities = $prospect->activities ?? collect();
@endphp

@if ($activities->count())
    <style>
        /* simple vertical timeline styles */
        .timeline {
            position: relative;
            padding-left: 30px;
            margin: 0;
        }

        .timeline:before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-left: 15px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -3px;
            top: 5px;
            width: 10px;
            height: 10px;
            background: #fff;
            border: 2px solid #6f42c1;
            border-radius: 50%;
        }

        .timeline-date {
            font-size: 0.85em;
            color: #6f42c1;
            font-weight: 500;
        }

        /* alternating background colors for entries */
        .timeline-item:nth-child(odd) {
            background: rgba(111, 66, 193, 0.05);
            padding: 8px 12px;
            border-radius: 4px;
        }

        .timeline-item:nth-child(even) {
            background: rgba(102, 16, 242, 0.05);
            padding: 8px 12px;
            border-radius: 4px;
        }
    </style>

    <div class="timeline">
        @foreach ($activities as $act)
            <div class="timeline-item">
                <div class="timeline-date">{{ $act->created_at->format('d F Y H:i') }}</div>
                <div>{{ $act->note ?? '-' }}</div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-muted">No history available.</div>
@endif
