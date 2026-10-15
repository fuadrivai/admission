<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Walk-in registration | {{ $revision->title }}</title>
    <style>
        :root { --primary: {{ $revision->primary_color }}; --secondary: {{ $revision->secondary_color }}; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; background: #f5f7fa; font: 16px/1.6 Arial, sans-serif; }
        header { padding: 2rem 1rem; color: #fff; background: var(--primary); text-align: center; }
        main { max-width: 680px; margin: 2rem auto; padding: 0 1rem; }
        section { padding: 1.5rem; border-radius: 12px; background: #fff; }
        h1, h2 { margin-top: 0; }
        h2 { color: var(--primary); }
        label { display: block; margin: 1rem 0 .3rem; font-weight: 700; }
        input { width: 100%; padding: .7rem; border: 1px solid #bbc3cf; border-radius: 6px; font: inherit; }
        button { margin-top: 1.25rem; padding: .75rem 1rem; border: 0; border-radius: 8px; color: #172033; background: var(--secondary); font-weight: 700; cursor: pointer; }
        .error { color: #a32020; }
        .help { color: #596579; font-size: .9rem; }
    </style>
</head>

<body>
    <header>
        <h1>{{ $revision->title }}</h1>
        <div>Walk-in registration</div>
    </header>
    <main>
        <section>
            @if (! $studentField || ! $parentField || $studentField->id === $parentField->id)
                <h2>Registration is unavailable</h2>
                <p>Please ask reception staff for assistance.</p>
            @else
                <h2>Complete your family details</h2>
                <p>Enter the short-lived token provided by reception, then fill in the student and parent details.</p>
                <form method="POST" action="{{ route('microsite.walk-in.complete', $event->slug) }}">
                    @csrf
                    <label for="walk-in-token">Reception token</label>
                    <input id="walk-in-token" name="token" maxlength="8" minlength="8"
                        pattern="[A-HJ-NP-Z2-9a-hj-np-z]{8}" autocomplete="one-time-code"
                        value="{{ old('token') }}" required>
                    @error('token')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <label for="walk-in-student">{{ $studentField->label }}</label>
                    <input id="walk-in-student" name="student_name" maxlength="255"
                        value="{{ old('student_name') }}" required>
                    @error('student_name')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <label for="walk-in-parent">{{ $parentField->label }}</label>
                    <input id="walk-in-parent" name="parent_name" maxlength="255"
                        value="{{ old('parent_name') }}" required>
                    @error('parent_name')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    @if ($emailField)
                        <label for="walk-in-email">{{ $emailField->label }}</label>
                        <input type="email" id="walk-in-email" name="parent_email" maxlength="255"
                            value="{{ old('parent_email') }}" @if ($emailField->is_required) required @endif>
                        @error('parent_email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    @endif

                    <p class="help">This registration is for this event only and does not create an Admission record.</p>
                    <button type="submit">Register and check in</button>
                </form>
            @endif
            <p class="help"><a href="{{ route('microsite.checkin', $event->slug) }}">Return to check-in</a></p>
        </section>
    </main>
</body>

</html>
