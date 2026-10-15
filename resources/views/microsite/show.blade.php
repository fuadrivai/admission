<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $revision->description }}">
    <title>{{ $revision->title }}</title>
    <style>
        :root {
            --microsite-primary: {{ $revision->primary_color }};
            --microsite-secondary: {{ $revision->secondary_color }};
        }

        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; background: #f5f7fa; font: 16px/1.6 Arial, sans-serif; }
        header { padding: 3rem 1.25rem; color: #fff; background: var(--microsite-primary); text-align: center; }
        header h1 { margin: 0; font-size: clamp(2rem, 5vw, 3.5rem); }
        header p { margin: .75rem 0 0; color: #fff; }
        nav { position: sticky; top: 0; display: flex; justify-content: center; gap: 1.25rem; flex-wrap: wrap; padding: .9rem; background: #fff; box-shadow: 0 2px 8px #17203318; }
        nav a { color: var(--microsite-primary); font-weight: 700; text-decoration: none; }
        main { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        section { margin-bottom: 1.25rem; padding: 1.5rem; border-radius: 12px; background: #fff; }
        h2 { margin-top: 0; color: var(--microsite-primary); }
        .detail { margin: .5rem 0; }
        .cta { display: inline-block; padding: .8rem 1.2rem; border-radius: 8px; color: #172033; background: var(--microsite-secondary); font-weight: 700; text-decoration: none; }
        .preformatted { white-space: pre-line; }
        .schedule { display: grid; gap: .75rem; }
        .schedule-item { display: grid; grid-template-columns: minmax(120px, 170px) 1fr; gap: 1rem; padding: 1rem; border: 1px solid #e7eaf0; border-radius: 10px; }
        .schedule-time { color: var(--microsite-primary); font-weight: 700; }
        .speakers { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; }
        .speaker { display: flex; align-items: center; gap: .9rem; padding: 1rem; border: 1px solid #e7eaf0; border-radius: 10px; }
        .speaker-photo { width: 64px; height: 64px; flex: 0 0 64px; border-radius: 50%; object-fit: cover; }
        .speaker-placeholder { display: grid; place-items: center; color: #fff; background: var(--microsite-primary); font-weight: 700; }
        .speaker-name { margin: 0; font-weight: 700; }
        .speaker-position { margin: .15rem 0 0; color: #667085; font-size: .9rem; }
        .faq-item { margin: .75rem 0; padding: 1rem; border: 1px solid #e7eaf0; border-radius: 10px; }
        .faq-item summary { color: var(--microsite-primary); font-weight: 700; cursor: pointer; }
        .faq-answer { margin-top: .75rem; white-space: pre-line; }
        @media (max-width: 600px) { .schedule-item { grid-template-columns: 1fr; gap: .25rem; } }
        @media (max-width: 600px) { nav { gap: .8rem; } section { padding: 1.1rem; } }
    </style>
</head>

<body>
    <header>
        <h1>{{ $revision->title }}</h1>
        @if ($revision->tagline)
            <p>{{ $revision->tagline }}</p>
        @endif
    </header>

    <nav aria-label="Event information">
        <a href="#rundown">Rundown</a>
        @if ($revision->map_url)
            <a href="#map">Map</a>
        @endif
        @if ($speakers)
            <a href="#speakers">Speakers</a>
        @endif
        <a href="#faq">FAQ</a>
        <a href="{{ route('microsite.checkin', $event->slug) }}">Check-in</a>
        @if ($event->status === 'PUBLISHED')
            <a href="{{ route('events.show', $event) }}">Registration</a>
        @endif
    </nav>

    <main>
        @if ($invitation)
            <section aria-live="polite">
                <h2>Welcome{{ $greeting['parent'] ? ', ' . $greeting['parent'] : '' }}!</h2>
                @if ($greeting['student'])
                    <p>We look forward to welcoming {{ $greeting['student'] }}.</p>
                @endif
                <a class="cta" href="{{ route('microsite.checkin', $event->slug) }}">Open check-in</a>
            </section>
        @else
            <section>
                <h2>View your family welcome</h2>
                @include('microsite._code-form', ['destination' => 'microsite'])
            </section>
        @endif

        @if ($revision->description)
            <section>
                <h2>About this event</h2>
                <p class="preformatted">{{ $revision->description }}</p>
            </section>
        @endif

        <section>
            <h2>Event information</h2>
            @if ($revision->starts_at)
                <p class="detail"><strong>Starts:</strong> {{ $revision->starts_at->format('d M Y, H:i') }}</p>
            @endif
            @if ($revision->ends_at)
                <p class="detail"><strong>Ends:</strong> {{ $revision->ends_at->format('d M Y, H:i') }}</p>
            @endif
            @if ($revision->location)
                <p class="detail"><strong>Location:</strong> {{ $revision->location }}</p>
            @endif
        </section>

        <section id="rundown">
            <h2>Rundown</h2>
            @if ($rundownItems)
                <div class="schedule">
                    @foreach ($rundownItems as $item)
                        <article class="schedule-item">
                            <div class="schedule-time">
                                @if (!empty($item['starts_at']) || !empty($item['ends_at']))
                                    {{ $item['starts_at'] ?? '--:--' }} – {{ $item['ends_at'] ?? '--:--' }}
                                @else
                                    <span class="text-muted">Time to be announced</span>
                                @endif
                            </div>
                            <div>{{ $item['title'] }}</div>
                        </article>
                    @endforeach
                </div>
            @else
                <p>Event schedule will be announced soon.</p>
            @endif
        </section>

        @if ($speakers)
            <section id="speakers">
                <h2>Speakers</h2>
                <div class="speakers">
                    @foreach ($speakers as $speaker)
                        <article class="speaker">
                            @if (!empty($speaker['photo_url']))
                                <img class="speaker-photo" src="{{ $speaker['photo_url'] }}" alt="{{ $speaker['name'] }}">
                            @else
                                <div class="speaker-photo speaker-placeholder" aria-hidden="true">
                                    {{ strtoupper(substr($speaker['name'], 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="speaker-name">{{ $speaker['name'] }}</p>
                                @if (!empty($speaker['position']))
                                    <p class="speaker-position">{{ $speaker['position'] }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($revision->map_url)
            <section id="map">
                <h2>Map</h2>
                <a class="cta" href="{{ $revision->map_url }}" target="_blank" rel="noopener noreferrer">Open event location map</a>
            </section>
        @endif

        <section id="faq">
            <h2>Frequently asked questions</h2>
            @if ($faqItems)
                @foreach ($faqItems as $index => $item)
                    <details class="faq-item" @if ($index === 0) open @endif>
                        <summary>{{ $item['question'] }}</summary>
                        <div class="faq-answer">{{ $item['answer'] }}</div>
                    </details>
                @endforeach
            @else
                <p>More information will be announced soon.</p>
            @endif
        </section>

        @if ($event->status === 'PUBLISHED')
            <section>
                <h2>Registration</h2>
                <a class="cta" href="{{ route('events.show', $event) }}">Register for this event</a>
            </section>
        @endif
    </main>
</body>

</html>
