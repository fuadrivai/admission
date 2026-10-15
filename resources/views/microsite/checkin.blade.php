<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check-in | {{ $revision->title }}</title>
    <style>
        :root { --primary: {{ $revision->primary_color }}; --secondary: {{ $revision->secondary_color }}; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; background: #f5f7fa; font: 16px/1.6 Arial, sans-serif; }
        header { padding: 2rem 1rem; color: #fff; background: var(--primary); text-align: center; }
        main { max-width: 680px; margin: 2rem auto; padding: 0 1rem; }
        section { padding: 1.5rem; border-radius: 12px; background: #fff; }
        h1, h2 { margin-top: 0; }
        h2 { color: var(--primary); }
        a { color: var(--primary); }
        .button { display: inline-block; padding: .75rem 1rem; border: 0; border-radius: 8px; color: #172033; background: var(--secondary); font-weight: 700; cursor: pointer; text-decoration: none; }
        .alert { margin-bottom: 1rem; padding: .75rem 1rem; border-radius: 8px; background: #e7f5eb; }
        .error { color: #a32020; }
    </style>
</head>

<body>
    <header>
        <h1>{{ $revision->title }}</h1>
        <div>Family check-in</div>
    </header>
    <main>
        @if (session('checkin_message'))
            <div class="alert" role="status">{{ session('checkin_message') }}</div>
        @endif

        <section>
            @if ($invitation)
                <h2>Welcome{{ $greeting['parent'] ? ', ' . $greeting['parent'] : '' }}!</h2>
                @if ($greeting['student'])
                    <p>We look forward to welcoming {{ $greeting['student'] }}.</p>
                @endif
                @if ($checkedIn)
                    <div class="alert" role="status">
                        Check-in recorded at {{ $checkedIn->checked_in_at->format('d M Y H:i') }}.
                    </div>
                @else
                    <p>Confirm your arrival to record your family's attendance.</p>
                    <form method="POST" action="{{ route('microsite.self-checkin', $event->slug) }}">
                        @csrf
                        <button class="button" type="submit">Confirm check-in</button>
                    </form>
                @endif
            @else
                <h2>Check in to the event</h2>
                <p>Enter the five-character family code from your invitation.</p>
                @include('microsite._code-form', ['destination' => 'check-in'])
                <p class="mt-4">No invitation code? <a href="{{ route('microsite.walk-in', $event->slug) }}">Continue with a reception-issued walk-in token</a>.</p>
            @endif
            <p class="mt-4"><a href="{{ route('microsite.show', $event->slug) }}">Return to event information</a></p>
        </section>
    </main>
</body>

</html>
